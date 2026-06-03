/* =======================================================
   1. INITIALIZATION & GLOBAL LISTENERS
======================================================= */
document.addEventListener('DOMContentLoaded', function() {
    setupAuthSwitcher();
    const toggleBtn = document.getElementById('yourToggleButtonId'); 
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
    setTimeout(() => { checkTPScroll(document.getElementById('tpContentArea')); }, 100);
}

// Calculate scroll tracking for Terms and Conditions modal agreement
function checkTPScroll(el) {
    let scrollPosition = el.scrollTop;
    let maxScroll = el.scrollHeight - el.clientHeight;
    let scrollPercentage = (scrollPosition / maxScroll) * 100;
    
    if (maxScroll <= 0) scrollPercentage = 100;

    document.getElementById('tpProgressBar').style.width = scrollPercentage + '%';

    if (scrollPercentage >= 99) {
        let btn = document.getElementById('tpAcceptBtn');
        btn.disabled = false;
        btn.classList.add('accept-enabled');
        document.getElementById('tpEndMessage').innerHTML = "You may now click Accept.";
    }
}

function acceptTP() {
    let checkbox = document.getElementById('terms');
    checkbox.disabled = false;
    checkbox.checked = true;
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
    toggleRequirement('req-length', val.length >= 8);
    toggleRequirement('req-upper', /[A-Z]/.test(val));
    toggleRequirement('req-lower', /[a-z]/.test(val));
    toggleRequirement('req-number', /[0-9]/.test(val));
    toggleRequirement('req-special', /_/.test(val));
}

function toggleRequirement(elementId, isValid) {
    const el = document.getElementById(elementId);
    if (el) {
        const icon = el.querySelector('i');
        if (isValid) {
            el.classList.add('valid'); 
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-check'); 
        } else {
            el.classList.remove('valid'); 
            icon.classList.remove('fa-check');
            icon.classList.add('fa-xmark'); 
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
        Swal.fire({
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
    if (typeof Chart !== 'undefined' && document.getElementById('lineChart')) {
        const themeGreen = '#008C4A';
        const themeRed = '#D9232D';
        const themeDark = '#1A3626';
        const gray = '#E5E7EB';

        const chartOptions = { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } };
        const barOptions = { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: {display: false} }, x: {grid: {display: false}} } };

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

        new Chart(document.getElementById('flightChart'), {
            type: 'pie',
            data: {
                labels: window.flightChartData.labels,
                datasets: [{ data: window.flightChartData.data, backgroundColor: [themeDark, gray, themeRed], borderWidth: 0 }]
            }, options: chartOptions
        });

        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: window.barChartData.labels,
                datasets: [{ label: 'Bookings', data: window.barChartData.data, backgroundColor: [themeDark, themeGreen, themeRed], borderRadius: 6 }]
            }, options: barOptions
        });

        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: window.pieChartData.labels,
                datasets: [{ data: window.pieChartData.data, backgroundColor: [themeRed, themeGreen], borderWidth: 0 }]
            }, options: chartOptions
        });
    }

    // --- CRITICAL FIX: Properly closed this DataTable initialization block ---
    if (typeof $.fn.DataTable !== 'undefined' && $('#data-table-basic').length > 0) {
        $('#data-table-basic').DataTable({
            destroy: true,
            language: { search: "Search Users:" },
            pageLength: 10,
            responsive: true
        });
    }
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
    if (payload.users_password && !passwordPattern.test(payload.users_password)) {
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

/* =======================================================
   7. EVENT BINDERS (Connects the HTML to your functions cleanly)
======================================================= */
$(document).ready(function() {
    if ($('#data-table-basic').length > 0) {
        $('#data-table-basic').DataTable();
    }

    $(document).on('click', '.trigger-edit-btn', function() {
        let btn = $(this);
        openEditForm(
            btn.data('id'), btn.data('fname'), btn.data('lname'), 
            btn.data('phone'), btn.data('email'), btn.data('bday'), btn.data('uname')
        );
    });

    $(document).on('click', '.cancel-edit-btn', function() { closeEditForm(); });
    $(document).on('click', '.save-edit-btn', function() { submitUpdate(); });
    $(document).on('click', '.trigger-del-btn', function() { confirmDelete($(this).data('id')); });
});

/* =======================================================
   8. USER DASHBOARD
======================================================= */
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

function updateMyProfileFunc() {
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

function checkUpdatePasswordReqs(val) {
    const reqList = document.getElementById('upd-password-reqs');
    
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
    if (!filterDropdown) return; 

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

/* =======================================================
   11. GLOBAL LOADER INJECTION (Bulletproof version)
======================================================= */
document.addEventListener("DOMContentLoaded", function() {
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

    const forms = document.querySelectorAll('form:not([id="userUpdateForm"]):not([id="regForm"]):not([id="loginForm"]):not([id="checkoutForm"]):not([id="paymentForm"])');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            showLoader();
        });
    });
});

window.addEventListener('load', function() {
    hideLoader();
});

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
        let tempOrigin = originSelect.value;
        originSelect.value = destSelect.value;
        destSelect.value = tempOrigin;
    }
}

/* =======================================================
   13. TRIP TYPE TOGGLE (Main Page)
======================================================= */
function setTripType(type, buttonElement) {
    const buttons = document.querySelectorAll('.skyzen-tab, .type-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    if(buttonElement) {
        buttonElement.classList.add('active');
    }

    const returnDateGroup = document.getElementById('returnDateGroup');
    const returnDateInput = document.getElementById('returnDate');
    
    if (returnDateGroup) {
        if (type === 'one') {
            returnDateGroup.style.display = 'none';
            if(returnDateInput) returnDateInput.removeAttribute('required');
        } else {
            returnDateGroup.style.display = 'block'; 
            if(returnDateInput) returnDateInput.setAttribute('required', 'required');
        }
    }
}

/* =======================================================
   15. PASSENGER INFO FORM DISPLAY (PassInfo Page)
======================================================= */
function showPassengerForm(flightID, price) {
    document.getElementById('passengerFormBox').style.display = 'block';
    document.getElementById('selectedFlightID').value = flightID;
    document.getElementById('selectedFlightPrice').value = price;
    document.getElementById('passengerFormBox').scrollIntoView({ behavior: 'smooth' });
}

/* =======================================================
   16. REVIEW PAYMENT & CHECKOUT (Dynamic Fallbacks)
======================================================= */
function processFinalPayment() {
    showLoader();
    $.ajax({
        url: '../controllers/userController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'checkout_flight',
            flightID: $('#flightID').val(),
            paxCount: $('#paxCount').val(),
            // CRITICAL FIX: Safe element reads rather than static leaked PHP syntax string literal tags
            grandTotal: $('#grandTotalValue').val() || '0', 
            seats: $('#selectedSeatsList').val() || 'TBA'
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
        
// Attaches standard credit card input masking configurations 
if (document.getElementById('ccNum')) {
    document.getElementById('ccNum').addEventListener('input', function (e) {
        this.value = this.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
        this.classList.remove('input-error');
    });
    document.getElementById('ccExpiry').addEventListener('input', function (e) {
        let val = this.value.replace(/\D/g, '');
        if (val.length > 2) val = val.substring(0, 2) + '/' + val.substring(2, 4);
        this.value = val;
        this.classList.remove('input-error');
    });
    document.getElementById('ccCvv').addEventListener('input', function (e) {
        this.value = this.value.replace(/\D/g, '');
        this.classList.remove('input-error');
    });
    document.getElementById('ccName').addEventListener('input', function (e) {
        this.classList.remove('input-error');
    });
}

if (document.getElementById('gcashNum')) {
    document.getElementById('gcashNum').addEventListener('input', function (e) {
        this.value = this.value.replace(/\D/g, '');
        this.classList.remove('input-error');
    });
}

function switchTab(method) {
    document.querySelectorAll('.pay-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.pay-method-content').forEach(c => c.classList.remove('active'));
    
    document.getElementById('selectedMethod').value = method;
    if(event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }
    document.getElementById('method-' + method).classList.add('active');
}

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

/* =======================================================
   19. ADMIN FLEET, FLIGHTS, & BOOKINGS LOGIC
======================================================= */
$(document).ready(function() {
    // --- FLIGHTS MANAGEMENT ---
    $('#btnShowAddFlight').click(function() { 
        $('#addFlightWrapper').slideDown(300); 
    });
    $('#btnCancelFlight').click(function() { 
        $('#addFlightWrapper').slideUp(200); 
    });
    
    $('#btnSaveFlight').click(function() {
        let payload = {
            action: 'add_flight', 
            num: $('#f_num').val(), 
            aircraft: $('#f_aircraft').val(),
            origin: $('#f_origin').val(), 
            dest: $('#f_dest').val(), 
            dep: $('#f_dep').val(),
            arr: $('#f_arr').val(), 
            price: $('#f_price').val(), 
            seats: $('#f_seats').val()
        };
        if(!payload.num || !payload.origin || !payload.dest || !payload.dep || !payload.price || !payload.seats) {
            Swal.fire('Warning', 'All flight fields are required.', 'warning'); 
            return;
        }
        $.post('../controllers/userController.php', payload, function(res) {
            if(res.success) { 
                Swal.fire('Success', 'Flight added.', 'success').then(() => location.reload()); 
            } else { 
                Swal.fire('Error', res.message, 'error'); 
            }
        }, 'json');
    });

    $(document).on('click', '.trigger-del-flight', function() {
        let id = $(this).data('id');
        Swal.fire({ 
            title: 'Delete Flight?', 
            text: "This will remove the flight from schedule.", 
            icon: 'warning', 
            showCancelButton: true, 
            confirmButtonColor: '#d33', 
            confirmButtonText: 'Yes, delete!' 
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controllers/userController.php', {action: 'delete_flight', id: id}, function(res) {
                    if(res.success) location.reload();
                    else Swal.fire('Cannot Delete', res.message, 'error');
                }, 'json');
            }
        });
    });

    // --- BOOKINGS MANAGEMENT ---
    $(document).on('click', '.trigger-status-btn', function() {
        let id = $(this).data('id');
        let newStatus = $(this).data('status');
        let actionWord = newStatus === 'Confirmed' ? 'Confirm' : 'Cancel';
        
        Swal.fire({ 
            title: actionWord + ' Booking?', 
            text: "Mark this booking as " + newStatus + "?", 
            icon: 'question', 
            showCancelButton: true, 
            confirmButtonColor: '#008C4A', 
            confirmButtonText: 'Yes, proceed' 
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controllers/userController.php', {action: 'update_booking_status', id: id, status: newStatus}, function(res) {
                    if(res.success) location.reload();
                }, 'json');
            }
        });
    });

    // --- FLEET MANAGEMENT ---
    $('#btnShowAddFleet').click(function() { 
        $('#addFleetWrapper').slideDown(300); 
    });
    $('#btnCancelFleet').click(function() { 
        $('#addFleetWrapper').slideUp(200); 
    });
    
    $('#btnSaveFleet').click(function() {
        let payload = { 
            action: 'add_fleet', 
            model: $('#a_model').val(), 
            cap: $('#a_cap').val() 
        };
        
        if(!payload.model || !payload.cap) { 
            Swal.fire('Warning', 'Model and Capacity are required.', 'warning'); 
            return; 
        }
        
        $.post('../controllers/userController.php', payload, function(res) {
            if(res.success) { 
                Swal.fire('Success', 'Aircraft added.', 'success').then(() => location.reload()); 
            } else {
                Swal.fire('Error', res.message || 'Could not save aircraft.', 'error');
            }
        }, 'json');
    });

    $(document).on('click', '.trigger-del-fleet', function() {
        let id = $(this).data('id');
        Swal.fire({ 
            title: 'Retire Aircraft?', 
            text: "Remove this aircraft from the fleet?", 
            icon: 'warning', 
            showCancelButton: true, 
            confirmButtonColor: '#d33', 
            confirmButtonText: 'Yes, remove' 
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controllers/userController.php', {action: 'delete_fleet', id: id}, function(res) {
                    if(res.success) location.reload();
                    else Swal.fire('Cannot Delete', res.message, 'error');
                }, 'json');
            }
        });
    });
});