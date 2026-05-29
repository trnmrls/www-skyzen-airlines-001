
/* =======================================================
   1. INITIALIZATION & GLOBAL LISTENERS
======================================================= */
document.addEventListener('DOMContentLoaded', function() {
    setupAuthSwitcher();
    if (typeof togglePass === 'function') {
        togglePass();
    }
    if (document.getElementById('tpModal')) {
        openTP();
    }
});

/* =======================================================
   2. AUTHENTICATION UI & MODAL LOGIC
======================================================= */
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

    function openTP() {
        document.getElementById('tpModal').style.display = 'flex';
        // Reset progress in case they reopen it
        setTimeout(() => { checkTPScroll(document.getElementById('tpContentArea')); }, 100);
    }

    function checkTPScroll(el) {
        // Calculate how far they have scrolled
        let scrollPosition = el.scrollTop;
        let maxScroll = el.scrollHeight - el.clientHeight;
        let scrollPercentage = (scrollPosition / maxScroll) * 100;
        
        // Safety fallback for very short screens
        if (maxScroll <= 0) scrollPercentage = 100;

        // Update the visual progress bar width
        document.getElementById('tpProgressBar').style.width = scrollPercentage + '%';

        // Once they hit the bottom, ONLY enable the button (Do NOT check the box yet)
        if (scrollPercentage >= 99) {
            let btn = document.getElementById('tpAcceptBtn');
            
            // Unlock the Accept button
            btn.disabled = false;
            btn.classList.add('accept-enabled');

            document.getElementById('tpEndMessage').innerHTML = "You may now click Accept.";
        }
    }

    function acceptTP() {
        let checkbox = document.getElementById('terms');
        
        // 1. Remove the 'disabled' lock so HTML5 validation works
        checkbox.disabled = false;
        
        // 2. Automatically check the box!
        checkbox.checked = true;
        
        // 3. Close the modal window
        document.getElementById('tpModal').style.display = 'none';

        checkbox.onclick = function(e) {
                e.preventDefault();
                return false;
            };
    }

    function closeTP() {
    document.getElementById('tpModal').style.display = 'none';
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
    
/* =======================================================
   3. DYNAMIC PASSWORD VALIDATOR (REAL-TIME)
======================================================= */
    function checkPasswordReqs(val) {
        // 1. Minimum 8 characters
        toggleRequirement('req-length', val.length >= 8);
        
        // 2. Contains at least 1 uppercase letter
        toggleRequirement('req-upper', /[A-Z]/.test(val));
        
        // 3. Contains at least 1 lowercase letter
        toggleRequirement('req-lower', /[a-z]/.test(val));
        
        // 4. Contains at least 1 number
        toggleRequirement('req-number', /[0-9]/.test(val));
        
        // 5. Contains an underscore (_)
        toggleRequirement('req-special', /_/.test(val));
    }

    // Helper function that swaps the Orange X to a Green Checkmark
    function toggleRequirement(elementId, isValid) {
        const el = document.getElementById(elementId);
        if (el) {
            const icon = el.querySelector('i');
            if (isValid) {
                el.classList.add('valid'); // Triggers Green CSS
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-check'); // Changes icon to Check
            } else {
                el.classList.remove('valid'); // Reverts to Orange CSS
                icon.classList.remove('fa-check');
                icon.classList.add('fa-xmark'); // Changes icon back to X
            }
        }
    }

/* =======================================================
   4. BACKEND API CALLS (Login & Register AJAX)
======================================================= */

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
            text: 'Password must be at least 8 characters long and MUST contain a mix of small and capital letters, numbers, and an underscore (_).' 
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

/* =======================================================
   5. ADMIN DASHBOARD CHARTS (Chart.js)
======================================================= */
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

/* =======================================================
   6. ADMIN CRUD OPERATIONS (Edit, Update, Delete)
======================================================= */

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

// =======================================================
// 7. CLIENT BOOKING ENGINE LOGIC (Homepage Interactions)
// =======================================================
/* =======================================================
   7. CLIENT BOOKING ENGINE LOGIC (Homepage)
======================================================= */
function setTripType(type, element) {
    document.querySelectorAll('.skyzen-tab').forEach(tab => tab.classList.remove('active'));
    element.classList.add('active');

    const returnGroup = document.getElementById('returnDateGroup');
    const returnInput = document.getElementById('returnDate');

    if (type === 'one') {
        returnGroup.style.display = 'none';
        returnInput.removeAttribute('required');
        returnInput.value = '';
    } else {
        returnGroup.style.display = 'block';
        returnInput.setAttribute('required', 'true');
    }
}

function swapAirports() {
    const originSelect = document.getElementById('searchOrigin');
    const destSelect = document.getElementById('searchDest');
    const tempOrigin = originSelect.value;
    const tempDest = destSelect.value;

    if(tempOrigin || tempDest) {
        originSelect.value = tempDest;
        destSelect.value = tempOrigin;
    }
}

// Emulate the Hero Slider Array from the Reference Model
document.addEventListener('DOMContentLoaded', function() {
    const heroSection = document.getElementById('heroSlider');
    if(heroSection) {
        const images = [
            'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=2074&auto=format&fit=crop', // Default
            'https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?q=80&w=2000&auto=format&fit=crop', // Cebu
            'https://images.unsplash.com/photo-1542296332-2e4473faf563?q=80&w=2070&auto=format&fit=crop'  // Iloilo
        ];
        let index = 0;
        
        setInterval(() => {
            index = (index + 1) % images.length;
            heroSection.style.backgroundImage = `url('${images[index]}')`;
        }, 6000); // Rotates the hero image every 6 seconds
    }
});

// ======================================================
// USER DASHBOARD
// ======================================================

// Toggle between Dashboard and Profile Edit view
function toggleProfileView(showProfile) {
    const dashView = document.getElementById('dashboardView');
    const profView = document.getElementById('profileView');
    
    if (dashView && profView) {
        if (showProfile) {
            dashView.style.display = 'none';
            profView.style.display = 'block';
        } else {
            profView.style.display = 'none';
            dashView.style.display = 'block';
        }
    }
}

// Submits the user's profile changes to the backend
function updateMyProfileFunc() {
// --- NEW: STRICT PASSWORD & CONFIRM PASSWORD CHECK ---
    var newPassword = $('#upd_password').val();
    var confPassword = $('#upd_confirm_password').val();

    if (newPassword) {
        var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*_)[A-Za-z\d_]{8,}$/;
        if (!passwordPattern.test(newPassword)) {
            Swal.fire({ icon: 'warning', title: 'Weak Password', text: 'Your new password must meet all the security criteria.' });
            return;
        }
        if (newPassword !== confPassword) {
            Swal.fire({ icon: 'warning', title: 'Passwords Mismatch', text: 'Your new passwords do not match. Please re-type them carefully!' });
            return; 
        }
    }

    $.ajax({
        url: '../controllers/userController.php',
        type: 'POST',
        data: payload,
        success: function(response) {
            Swal.fire({
                title: 'Profile Updated!', 
                text: 'Your information has been successfully saved.', 
                icon: 'success', 
                confirmButtonColor: '#005A9C'
            }).then(() => { 
                location.reload(); 
            });
        },
        error: function(xhr) {
            Swal.fire({ icon: 'error', title: 'Update Failed', text: xhr.responseText });
        }
    });
}

// Dynamic Password Tracker for Update Profile
function checkUpdatePasswordReqs(val) {
    const reqList = document.getElementById('upd-password-reqs');
    
    // Only show the checklist if they start typing a new password
    if (val.length > 0) {
        reqList.style.display = 'block';
        toggleRequirement('upd-req-length', val.length >= 8);
        toggleRequirement('upd-req-upper', /[A-Z]/.test(val));
        toggleRequirement('upd-req-lower', /[a-z]/.test(val));
        toggleRequirement('upd-req-number', /[0-9]/.test(val));
        toggleRequirement('upd-req-special', /_/.test(val));
    } else {
        reqList.style.display = 'none';
    }
}