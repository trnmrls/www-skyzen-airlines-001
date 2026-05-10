document.addEventListener('DOMContentLoaded', function() {
    setupAuthSwitcher();
    togglePass();
});

    function setupAuthSwitcher() {
        document.querySelectorAll('[data-auth-switch]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-auth-switch');
                
                document.querySelectorAll('.nk-auth-card').forEach(card => {
                    card.classList.remove('toggled');
                });
                
                document.querySelector(targetId).classList.add('toggled');
                
                const urlMap = {
                    '#l-register': 'skyzenRegisPage.php',
                    '#l-login': 'skyzenLoginPage.php'
                };
                
                if (urlMap[targetId]) {
                    history.pushState(null, '', urlMap[targetId]);
                }
            });
        });
    }

    function togglePass(inputId, iconId) {
        var x = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        
        if (x.type === "password") {
            x.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            x.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    function loginAdminFunc() {
        let username = $('#users_username').val();
        let password = $('#users_password').val();

        if (!username || !password) {
            Swal.fire( {
                title: 'Error',
                text: 'Please enter both your username and password.',
                icon: 'warning'
            });
            return;
        }

        $.ajax({
            url: '../controllers/userController.php',
            type: 'POST',
            data: {
                action: 'login',
                users_username: username, 
                users_password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.success === true) {
                    if (response.rolesID == 1) {
                        window.location.href = 'skyzenAdminDash.php';
                    } else {
                        window.location.href = 'skyzenUserDash.php'; 
                    }
                } else {
                    Swal.fire('Login Failed', response.message || 'Invalid credentials.', 'error');
                }
            },
            error: function(xhr) {
                let msg = 'Invalid credentials. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    msg = xhr.responseText;
                }
                Swal.fire('Login Failed', msg, 'error');
            }
        });
    }

    function registerUserFunc() {
        var payload = {
            action: 'register',
            users_firstName: $('#regFirstName').val(),
            users_middleName: $('#regmiddleName').val(),
            users_lastName: $('#regLastName').val(),
            users_email: $('#regEmail').val(),
            users_phoneNum: $('#regPhone').val(),
            users_birthday: $('#regBirthday').val(),
            users_username: $('#regUsername').val(),
            users_password: $('#regPassword').val(),
            rolesID: 2 
        };

        if (!payload.users_firstName || !payload.users_lastName || !payload.users_email || !payload.users_phoneNum || !payload.users_username || !payload.users_password || !payload.users_birthday) {
        Swal.fire({ icon: 'warning', title: 'Missing fields', text: 'Please fill all required fields.' });
        return; 
    }
        // VALIDATION TASKING 2  
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(payload.users_email)) {
        Swal.fire({ icon: 'warning', title: 'Invalid Email', text: 'Please enter a valid email address.' });
        return;
    }

    var phonePattern = /^[0-9]{10,15}$/;
    if (!phonePattern.test(payload.users_phoneNum)) {
        Swal.fire({ icon: 'warning', title: 'Invalid Phone', text: 'Phone number must contain only numbers (10 to 15 digits).' });
        return;
    }

    var inputDate = new Date(payload.users_birthday);
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    if (inputDate > today) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Invalid Birthday', 
            text: 'Your birthday cannot be in the future!' 
        });
        return;
    }

    var confirmPassword = $('#regConfirmPassword').val();
    var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*_)[A-Za-z\d_]{8,}$/;
    if (!passwordPattern.test(payload.users_password)) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Weak Password', 
            text: 'Password must be at least 8 characters long and MUST contain a mix of letters, numbers, and an underscore (_).' 
        });
        return;
    }
    if (payload.users_password !== confirmPassword) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Passwords Mismatch', 
            text: 'Your passwords do not match. Please re-type them carefully!' 
        });
        return; 
    }

    $.ajax({
            url: '../controllers/userController.php',
            type: 'POST',
            data: payload,
            success: function(returnData) {
                if (returnData) {
                Swal.fire({
                    title: 'Registration Successful',
                    text: 'Your account has been created and your email has been sent. You can now log in.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    willClose: () => {
                        window.location.href = 'skyzenLoginPage.php';
                    }
                });
                } 
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: xhr.responseText || 'Could not register your account. Please check your input and try again.'
                });
            }
        });
    }

// charts    
    $(document).ready(function() {
        const themeGreen = '#008C4A';
        const themeRed = '#D9232D';
        const themeDark = '#1A3626';
        const gray = '#E5E7EB';

        const chartOptions = { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } };
        const barOptions = { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: {display: false} }, x: {grid: {display: false}} } };

        // 1. Line Chart (Revenue)
        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels: window.lineChartData.labels,
                datasets: [{
                    label: 'Revenue (₱)', data: window.lineChartData.data,
                    borderColor: themeGreen, backgroundColor: 'rgba(0, 140, 74, 0.15)',
                    borderWidth: 3, tension: 0.4, fill: true, pointBackgroundColor: themeDark
                }]
            }, options: barOptions
        });

        // 4. Pie Chart (flight Status)
        new Chart(document.getElementById('flightChart'), {
            type: 'pie',
            data: {
                labels: window.flightChartData.labels,
                datasets: [{ data: window.flightChartData.data, backgroundColor: [themeDark, gray, themeRed], borderWidth: 0 }]
            }, options: chartOptions
        });

        // 5. Bar Chart (Booking Statuses)
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: window.barChartData.labels,
                datasets: [{ label: 'Bookings', data: window.barChartData.data, backgroundColor: [themeDark, themeGreen, themeRed], borderRadius: 6 }]
            }, options: barOptions
        });

        // 6. Doughnut Chart (Roles)
        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: window.pieChartData.labels,
                datasets: [{ data: window.pieChartData.data, backgroundColor: [themeRed, themeGreen], borderWidth: 0 }]
            }, options: chartOptions
        });
    });

    if (typeof $.fn.DataTable !== 'undefined' && $('#data-table-basic').length > 0) {
        $('#data-table-basic').DataTable({
            destroy: true,
            language: { search: "Search Users:" },
            pageLength: 10,
            responsive: true
        });

        function openEditForm(id, fname, lname, phone, email, birthday, username) {
            $('#edit_id').val(id);
            $('#edit_fname').val(fname);
            $('#edit_lname').val(lname);
            $('#edit_phone').val(phone);
            $('#edit_email').val(email);
            $('#edit_birthday').val(birthday);
            $('#edit_username').val(username);
            $('#editDisplayLabel').text(fname + ' ' + lname);
            $('#editFormWrapper').slideDown(400);
            $('html, body').animate({ scrollTop: $("#editFormWrapper").offset().top - 30 }, 500);
        }

        function closeEditForm() {
            $('#editFormWrapper').slideUp(300);
        }

        function submitUpdate() {
            var payload = {
                action: 'update',
                usersID: $('#edit_id').val(),
                users_firstName: $('#edit_fname').val(),
                users_lastName: $('#edit_lname').val(),
                users_phoneNum: $('#edit_phone').val(),
                users_email: $('#edit_email').val(),
                users_birthday: $('#edit_birthday').val(),
                users_username: $('#edit_username').val(),
                users_password: $('#edit_password').val()
            };

            if (!payload.users_firstName || !payload.users_lastName || !payload.users_phoneNum || !payload.users_email || !payload.users_birthday || !payload.users_username) {
                Swal.fire({ icon: 'warning', title: 'Missing fields', text: 'Please complete all required fields.' });
                return;
            }

            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(payload.users_email)) {
                Swal.fire({ icon: 'warning', title: 'Invalid Email', text: 'Please enter a valid email address.' });
                return;
            }

            const phonePattern = document.getElementById("txtPhoneNumber");
            if (phonePattern) {
                phonePattern.addEventListener("input", function() {
                    allowOnlyNumber(this);
                });
            }

            function allowOnlyNumber(element) {
                element.value = element.value.replace(/[^0-9]/g, "");

            }

            var inputDate = new Date(payload.users_birthday);
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            if (inputDate > today) {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'Invalid Birthday', 
                    text: 'Your birthday cannot be in the future!' 
                });
                return;
            }

            var confirmPassword = $('#regConfirmPassword').val();
            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*_)[A-Za-z\d_]{8,}$/;
            if (!passwordPattern.test(payload.users_password)) {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'Weak Password', 
                    text: 'Password must be at least 8 characters long and MUST contain a mix of letters, numbers, and an underscore (_).' 
                });
                return;
            }

            $.ajax({
                url: '../controllers/userController.php',
                type: 'POST',
                data: payload,
                success: function(response) {
                    Swal.fire({
                        title: 'Success!', text: 'User details saved.', icon: 'success', confirmButtonColor: '#00c292'
                    }).then(() => { location.reload(); });
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: 'Update Failed', text: xhr.responseText });
                }
            });
        }

        function confirmDelete(userID) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F44336',
                cancelButtonColor: '#999',
                confirmButtonText: 'Yes, delete user'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../controllers/userController.php',
                        type: 'POST',
                        data: {
                            action: 'delete',
                            usersID: userID
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'User has been successfully deleted.',
                                'success'
                            ).then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Failed to delete user: ' + xhr.responseText, 'error');
                        }
                    });
                }
            });
        }
    };