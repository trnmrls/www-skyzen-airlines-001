
/* =======================================================
   1. INITIALIZATION & GLOBAL LISTENERS
======================================================= */
document.addEventListener('DOMContentLoaded', function() {
    setupAuthSwitcher();
    const toggleBtn = document.getElementById('yourToggleButtonId'); // Update this ID to whatever you used
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            togglePass();
        });
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
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    // DEFENSIVE NULL CHECK: If they don't exist on this page, stop right here!
    if (!input || !icon) return; 

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
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
        // DEFENSIVE CHECK: Only run this if Chart.js is loaded and the canvas exists!
        if (typeof Chart !== 'undefined' && document.getElementById('lineChart')) {
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
        }
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

/* =======================================================
   9. DESTINATION GUIDE ACCORDION LOGIC
======================================================= */
document.addEventListener('DOMContentLoaded', function() {
    const accordions = document.querySelectorAll('.accordion-header');

    accordions.forEach(acc => {
        acc.addEventListener('click', function() {
            const parent = this.parentElement;
            
            document.querySelectorAll('.dest-accordion').forEach(otherAcc => {
                if (otherAcc !== parent) {
                    otherAcc.classList.remove('active');
                }
            });

            parent.classList.toggle('active');
        });
    });
});



/* =======================================================
   10. AIRPORT DIRECTORY LOGIC (Where We Fly)
======================================================= */
function filterAirports() {
    let filterDropdown = document.getElementById('airportFilter');
    if (!filterDropdown) return; // Exit if we aren't on the Airports page

    let filterValue = filterDropdown.value;
    let cards = document.querySelectorAll('.airport-card');
    
    cards.forEach(card => {
        if (filterValue === 'all' || card.getAttribute('data-code') === filterValue) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function processCheckout() {
            // Trigger the awesome Earth Loader!
            showLoader();

            $.ajax({
                url: '../controllers/userController.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'checkout_flight',
                    flightID: $('#flightID').val(),
                    paxCount: $('#paxCount').val()
                },
                success: function(response) {
                    hideLoader();
                    if (response.success) {
                        Swal.fire({
                            title: 'Booking Confirmed!',
                            html: `Your PNR Code is: <strong style="color:#008C4A; font-size:24px;">${response.pnr}</strong><br><br>You can view this in your dashboard.`,
                            icon: 'success',
                            confirmButtonColor: '#005A9C'
                        }).then(() => {
                            window.location.href = 'skyzenUserDash.php'; // Send them to the dashboard to see it!
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoader();
                    Swal.fire('Error', 'Could not process booking.', 'error');
                }
            });
        }

/* =======================================================
   11. GLOBAL LOADER INJECTION (Bulletproof version)
======================================================= */
document.addEventListener("DOMContentLoaded", function() {
    // 1. Only inject if it doesn't already exist
    if(!document.getElementById('skyzen-global-loader')) {
        const loaderHTML = `
        <div id="skyzen-global-loader">
            <div class="loader">
                <div class="wait"> L O A D I N G ... </div>
                <div class="iata_code departure_city">SKY</div>
                <div class="plane"><img src="https://zupimages.net/up/19/34/4820.gif" class="plane-img"></div>
                <div class="earth-wrapper"><div class="earth"></div></div>  
                <div class="iata_code arrival_city">ZEN</div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('afterbegin', loaderHTML);
    }

    // 2. Attach loader to all standard forms (excluding AJAX ones)
    const forms = document.querySelectorAll('form:not([id="userUpdateForm"]):not([id="regForm"]):not([id="loginForm"]):not([id="checkoutForm"]):not([id="paymentForm"])');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            showLoader();
        });
    });
});

// 3. Force hide the loader as soon as the page is fully loaded
window.addEventListener('load', function() {
    hideLoader();
});

// 4. Force hide the loader if the user clicks the browser's "Back" button
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        hideLoader();
    }
});

function showLoader() { 
    const loader = document.getElementById('skyzen-global-loader');
    if(loader) loader.style.display = 'flex'; 
}
function hideLoader() { 
    const loader = document.getElementById('skyzen-global-loader');
    if(loader) loader.style.display = 'none'; 
}

/* =======================================================
   12. MAIN PAGE: FLIGHT SEARCH LOGIC
======================================================= */
function swapAirports() {
    const originSelect = document.getElementById('searchOrigin');
    const destSelect = document.getElementById('searchDest');
    
    if (originSelect && destSelect) {
        // Temporarily hold the origin value
        let tempOrigin = originSelect.value;
        
        // Swap values
        originSelect.value = destSelect.value;
        destSelect.value = tempOrigin;
    }
}



/* =======================================================
   13. TRIP TYPE TOGGLE (Main Page)
======================================================= */
function setTripType(type, buttonElement) {
    // 1. Remove 'active' class from all buttons
    const buttons = document.querySelectorAll('.skyzen-tab, .type-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // 2. Add 'active' class to the clicked button
    if(buttonElement) {
        buttonElement.classList.add('active');
    }

    // 3. Hide or show the Return Date field
    const returnDateGroup = document.getElementById('returnDateGroup');
    const returnDateInput = document.getElementById('returnDate');
    
    if (returnDateGroup) {
        if (type === 'one') {
            returnDateGroup.style.display = 'none';
            if(returnDateInput) returnDateInput.removeAttribute('required');
        } else {
            returnDateGroup.style.display = 'block'; // Or 'flex' depending on your CSS
            if(returnDateInput) returnDateInput.setAttribute('required', 'required');
        }
        }
    }

// =====================================================
// 15. PASSENGER INFO FORM DISPLAY (PassInfo Page)
// =====================================================
function showPassengerForm(flightID, price) {
            document.getElementById('passengerFormBox').style.display = 'block';
            document.getElementById('selectedFlightID').value = flightID;
            document.getElementById('selectedFlightPrice').value = price;
            // Scroll down to the form
            document.getElementById('passengerFormBox').scrollIntoView({ behavior: 'smooth' });
        }


// =====================================================
// 16. Review Payment (PaymentPage)
// =====================================================
function processFinalPayment() {
            // Trigger Earth Loader!
            showLoader();

            $.ajax({
                url: '../controllers/userController.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'checkout_flight',
                    flightID: $('#flightID').val(),
                    paxCount: $('#paxCount').val()
                },
                success: function(response) {
                    hideLoader();
                    if (response.success) {
                        Swal.fire({
                            title: 'Payment Successful!',
                            html: `Your flight is booked! Generating your boarding pass...`,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            // Redirect to the Boarding Pass generation page!
                            window.location.href = 'skyzenBoardingPage.php?pnr=' + response.pnr;
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoader();
                    Swal.fire('Error', 'Payment Gateway Failed.', 'error');
                }
            });
        }
        
        document.getElementById('ccNum').addEventListener('input', function (e) {
            let target = e.target;
            // Strip all non-digits, then add a space every 4 digits
            let val = target.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
            target.value = val;
            target.classList.remove('input-error');
        });

        // Auto-format Expiry Date with slash
        document.getElementById('ccExpiry').addEventListener('input', function (e) {
            let target = e.target;
            // Strip all non-digits
            let val = target.value.replace(/\D/g, '');
            // Add slash after the 2nd digit
            if (val.length > 2) {
                val = val.substring(0, 2) + '/' + val.substring(2, 4);
            }
            target.value = val;
            target.classList.remove('input-error');
        });

        // Restrict CVV to numbers only
        document.getElementById('ccCvv').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '');
            e.target.classList.remove('input-error');
        });

        // Remove error styling on name input
        document.getElementById('ccName').addEventListener('input', function (e) {
            e.target.classList.remove('input-error');
        });


function switchTab(method) {
            document.querySelectorAll('.pay-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.pay-method-content').forEach(c => c.classList.remove('active'));
            
            document.getElementById('selectedMethod').value = method;
            event.currentTarget.classList.add('active');
            document.getElementById('method-' + method).classList.add('active');
        }

        const ccNum = document.getElementById('ccNum');
        if (ccNum) {
            ccNum.addEventListener('input', function (e) {
                this.value = this.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
                this.classList.remove('input-error');
            });
        }
        const ccExpiry = document.getElementById('ccExpiry');
        if (ccExpiry) {
            ccExpiry.addEventListener('input', function (e) {
                let val = this.value.replace(/\D/g, '');
                if (val.length > 2) val = val.substring(0, 2) + '/' + val.substring(2, 4);
                this.value = val;
                this.classList.remove('input-error');
            });
        }
        const ccCvv = document.getElementById('ccCvv');
        if (ccCvv) {
            ccCvv.addEventListener('input', function (e) {
                this.value = this.value.replace(/\D/g, '');
                this.classList.remove('input-error');
            });
        }
        
        // GCash Formatting
        const gcashNum = document.getElementById('gcashNum');
        if (gcashNum) {
            gcashNum.addEventListener('input', function (e) {
                this.value = this.value.replace(/\D/g, '');
                this.classList.remove('input-error');
            });
        }

        function validateAndProcessPayment() {
    // 1. Safely grab the payment method (Defaults to 'cc' if the element is missing)
    let methodElement = document.getElementById('selectedMethod');
    let method = methodElement ? methodElement.value : 'cc'; 
    
    let isValid = true;
    let errorMsg = '';

    if (method === 'cc') {
        let name = document.getElementById('ccName');
        let num = document.getElementById('ccNum');
        let expiry = document.getElementById('ccExpiry');
        let cvv = document.getElementById('ccCvv');

        // Safely check if elements exist before reading .value
        if (!name || name.value.trim() === '') { if(name) name.classList.add('input-error'); isValid = false; errorMsg = 'Please enter Cardholder Name.'; }
        if (!num || num.value.replace(/\s/g, '').length !== 16) { if(num) num.classList.add('input-error'); isValid = false; if(!errorMsg) errorMsg = 'Invalid Card Number.'; }
        if (!expiry || !expiry.value.match(/^(0[1-9]|1[0-2])\/?([0-9]{2})$/)) { if(expiry) expiry.classList.add('input-error'); isValid = false; if(!errorMsg) errorMsg = 'Invalid Expiry Date.'; }
        if (!cvv || cvv.value.length < 3) { if(cvv) cvv.classList.add('input-error'); isValid = false; if(!errorMsg) errorMsg = 'Invalid CVV.'; }
    } else {
        let gcashNum = document.getElementById('gcashNum');
        if (!gcashNum || gcashNum.value.length !== 11 || !gcashNum.value.startsWith('09')) {
            if(gcashNum) gcashNum.classList.add('input-error'); 
            isValid = false; 
            errorMsg = 'Enter a valid 11-digit GCash number starting with 09.';
        }
    }

    if (!isValid) {
        Swal.fire({ icon: 'warning', title: 'Invalid Details', text: errorMsg, confirmButtonColor: '#005A9C' });
        return;
    }

    showLoader();
    
    let paxNamesArray = [];
            document.querySelectorAll('.forward-pax-name').forEach(input => paxNamesArray.push(input.value));

            showLoader();
            $.ajax({
                url: '../controllers/userController.php',
                type: 'POST',
                dataType: 'json',
                data: { 
                    action: 'checkout_flight', 
                    flightID: $('#flightID').val(), 
                    paxCount: $('#paxCount').val(),
                    grandTotal: '<?= $grandTotal ?>',
                    seats: '<?= $seats ?>',
                    paxNames: paxNamesArray 
                },
                success: function(response) {
                    hideLoader();
                    if (response.success) {
                        Swal.fire({
                            title: 'Payment Successful!',
                            html: `Transaction approved. Generating boarding pass...`,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = 'skyzenBoardingPage.php?pnr=' + response.pnr;
                        });
                    } else {
                        // This will now successfully show the REAL backend error!
                        Swal.fire('Transaction Failed', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    hideLoader();
                    console.error("AJAX Error: ", xhr.responseText);
                    Swal.fire('Error', 'Payment Gateway timeout. Please check the console.', 'error');
                }
            });
        }

// =====================================================
// seat selection logic (SeatSelect Page)
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
            const maxSeats = parseInt(document.getElementById('maxPax').value);
            const checkboxes = document.querySelectorAll('.seat-checkbox');
            const seatForm = document.getElementById('seatForm');

            // 1. Enforce Maximum Seat Selection
            checkboxes.forEach(box => {
                box.addEventListener('change', function() {
                    const checkedCount = document.querySelectorAll('.seat-checkbox:checked').length;
                    
                    if (this.checked && checkedCount > maxSeats) {
                        this.checked = false; // Immediately uncheck the extra seat
                        Swal.fire({
                            icon: 'warning',
                            title: 'Seat Limit Reached',
                            text: `You have only booked for ${maxSeats} passenger(s).`,
                            confirmButtonColor: '#005A9C'
                        });
                    }
                });
            });

            // 2. Enforce EXACT Seat Selection Before Submitting
            seatForm.addEventListener('submit', function(event) {
                const checkedCount = document.querySelectorAll('.seat-checkbox:checked').length;
                
                if (checkedCount !== maxSeats) {
                    event.preventDefault(); // HALT the form submission
                    Swal.fire({
                        icon: 'error',
                        title: 'Incomplete Selection',
                        text: `Please select exactly ${maxSeats} seat(s) before continuing.`,
                        confirmButtonColor: '#005A9C'
                    });
                } else {
                    showLoader(); // Fire the global loader if everything is perfect
                }
            });
        });

        
        function processCheckIn() {
            let pnr = document.getElementById('ci_pnr').value.toUpperCase();
            let lastName = document.getElementById('ci_lastName').value;

            showLoader();
            $.ajax({
                url: '../controllers/userController.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'web_checkin', pnr: pnr, lastName: lastName },
                success: function(response) {
                    hideLoader();
                    if (response.success) {
                        Swal.fire({
                            title: 'Checked In!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#005A9C'
                        }).then(() => {
                            window.location.href = 'skyzenBoardingPage.php?pnr=' + pnr;
                        });
                    } else {
                        Swal.fire('Check-In Failed', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoader();
                    Swal.fire('Error', 'Could not connect to server.', 'error');
                }
            });
        }

/* =======================================================
   13. BOOKING CHECKOUT LOGIC
======================================================= */
function processCheckout() {
    // 1. Grab the hidden data from the form
    let flightID = document.getElementById('flightID').value;
    let paxCount = document.getElementById('paxCount').value;

    // 2. Show your awesome Earth Loader!
    showLoader();

    // 3. Send the data to the controller via AJAX
    $.ajax({
        url: '../controllers/userController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'checkout_flight',
            flightID: flightID,
            paxCount: paxCount
        },
        success: function(response) {
            hideLoader();
            if (response.success) {
                Swal.fire({
                    title: 'Booking Confirmed!',
                    html: `Your PNR Code is: <strong style="color:#005A9C; font-size:24px;">${response.pnr}</strong><br><br>You can view this in your dashboard.`,
                    icon: 'success',
                    confirmButtonColor: '#005A9C',
                    allowOutsideClick: false // Force them to click the button
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect them to the User Dashboard so they can see their new flight!
                        window.location.href = 'skyzenUserDash.php'; 
                    }
                });
            } else {
                Swal.fire('Checkout Failed', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoader();
            console.error(xhr.responseText); // Good for backend debugging
            Swal.fire('System Error', 'Could not process the booking. Check the console.', 'error');
        }
    });
}

/* =======================================================
   17. MANAGE BOOKING - EDIT PASSENGER
======================================================= */
function editPassengerName(ticketID, currentName) {
    Swal.fire({
        title: 'Edit Passenger Name',
        input: 'text',
        inputValue: currentName,
        showCancelButton: true,
        confirmButtonColor: '#005A9C',
        confirmButtonText: 'Save Changes',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to write something!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            showLoader();
            $.ajax({
                url: '../controllers/userController.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_passenger',
                    ticketID: ticketID,
                    newName: result.value
                },
                success: function(response) {
                    hideLoader();
                    if(response.success) {
                        Swal.fire('Updated!', 'Passenger name has been corrected.', 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoader();
                    Swal.fire('Error', 'Could not update passenger.', 'error');
                }
            });
        }
    });
}

        