<?php
    session_start();
    require_once "../bl/userManagement.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SkyZen Airlines</title>
    
    <link rel="stylesheet" href="../views/style.css">
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div id="tpModal" class="tp-modal-overlay">
        <div class="tp-modal">
            <button type="button" class="tp-close-btn" onclick="closeTP()">
                <i class="fa fa-times"></i>
            </button>
            <h3 style="margin-top:0; margin-bottom:5px; color: var(--nav-dark);">Legal Framework & Privacy Policy</h3>
            
            <div class="tp-progress-container">
                <div class="tp-progress-bar" id="tpProgressBar"></div>
            </div>
            <p style="font-size:0.8rem; color:var(--text-muted); margin-top:5px;">Please read Terms & Conditions and Privacy Policy before you accept.</p>
            
            <div class="tp-content" id="tpContentArea" onscroll="checkTPScroll(this)"> 
                <p>
                    <strong>Welcome to SkyZen Airlines.</strong>
                        These legal frameworks outline your passenger rights, booking provisions, code of conduct, and privacy protection protocols when utilizing our online portal, booking engines, and onboard services. By accessing our platform, registering an account, or purchasing a ticket, you explicitly enter into a binding contractual agreement with SkyZen Airlines.
                </p>
                
                <!-- separate the style -->
                <h4 style="color: var(--theme-red); margin-top:20px;">
                    PART I: GENERAL TERMS AND CONDITIONS OF CARRIAGE
                </h4>
                
                <h4>
                    1. Scope of Agreement and Eligibility
                </h4>
                <p>
                    This Passenger Agreement governs all interactions with SkyZen Airlines' flight inventory, web dashboards, booking mechanisms, and flight fulfillment services. To maintain an active digital passenger profile or issue a Passenger Name Record (PNR), individuals must be at least eighteen (18) years of age or possess formal legal parental or guardian authorization to undertake financial commitments. By initiating a registration on our platform, you confirm that all personal metrics, name structures matphing your government-issued passport, and payment channels are true, accurate, and under your direct legitimate ownership.
                </p>
                
                <h4>
                    2. Account Creation, Security, and Password Protocols
                </h4>
                <p>
                    Passengers are granted access to a personal SkyZen Booking Dashboard upon successful authentication and verification of credentials. You are entirely responsible for preserving the structural confidentiality of your login passphrase and associated profile data. Any flight modifications, ancillary upgrades, or point-of-sale seat class selection executed through your authenticated passenger profile will be legally deemed an act by you. SkyZen Airlines disclaims any liability for financial shortfalls or seat cancellations stemming from unauthorized access due to passenger negligence in safeguarding login tokens.
                </p>
                <p>
                    <em>Important Passenger Advisory:</em> 
                    Your passenger profile name must exactly replicate the typographical layout of your legal identity document or passport. Discrepancies may prevent boarding or result in an involuntary forfeiture of the booking code under regulatory international security mandates.
                </p>

                <h4>
                    3. Flight Bookings, PNR Generation, and Fare Allocation
                </h4>
                <p>
                    A contract of carriage is finalized only when a unique alphanumeric Passenger Name Record (PNR Code) is successfully generated and displayed on your dashboard, supplemented by an email confirmation containing your ticket serial identifiers. All available flights, fare categories, routing pathways, and operational parameters shown during search are dynamically evaluated in real-time. Until the final authorization of payment forms is cleared, seat inventory remains unallocated and subject to fluctuating airline yields or unexpected structural scheduling adjustments.
                </p>

                <h4>
                    4. Seat Class Selections, Aircraft Specifications, and Upgrades</h4>
                <p>
                    SkyZen Airlines provides a tiered matrix of seat classes configured to cater to varying levels of comfort and pricing preferences. The baseline tier balances efficiency with comfort, providing standardized amenities. Mid-tier configurations offer additional physical clearance and elevated catering provisions, while our premium tiers deliver maximum serenity with fully adjustable premium spacing, private entertainment arrays, and priority routing privileges. Specific aircraft models are assigned dynamically according to operational requirements; therefore, while the selected class tiers are legally guaranteed, the exact layout, pitph orientation, or electronic component availability remains flexible based on fleet scheduling demands.</p>

                <h4>
                    5. Payment Terms, Cancellations, Adjustments, and Refund Disclaimers</h4>
                <p>
                    All pricing metrics are stated inclusive of standard fuel premiums, security fees, and governmental transport taxes. Payment collection occurs instantly upon confirmation through secure gateway processing channels. Once a ticket is issued with an approved operational state:</p>
                <ul>
                    <li><strong>Voluntary Cancellation:</strong> Depending on the fare family selected, voluntary cancellations may incur substantial administrative deductions or restrict the output value entirely to a non-transferable SkyZen Travel Voucher.</li>
                    <li><strong>Flight Rescheduling:</strong> Passengers may modify departure windows up to twenty-four (24) hours before standard scheduling, provided they settle the baseline adjustment differences between the historical ticket cost and active inventory valuation.</li>
                    <li><strong>Involuntary Irregularities:</strong> In the event of climate blockages, air traffic constraints, or internal engineering disruptions causing route adjustments, SkyZen Airlines will instantly initiate re-accommodation onto the next operational flight or provide an unconditional account refund.</li>
                </ul>

                <h4>
                    6. Check-In Window Requirements and Boarding Rules</h4>
                <p>
                    The online check-in protocol opens forty-eight (48) hours prior to scheduled departures and locks ninety (90) minutes before takeoff. Terminal gates close exactly twenty (20) minutes before flight departure. Failure to execute physical clearance checks, report to the security podiums, or provide genuine verification documentation within these standard timelines will cause the passenger to be marked as a No-Show, invalidating the remaining segments of that specific PNR journey without financial recourse.</p>

                <h4>
                    7. Code of Conduct and Onboard Flight Decorum</h4>
                <p>
                    Passenger safety and serenity form the foundation of our operations. SkyZen Airlines maintains an absolute zero-tolerance baseline regarding disruptive, verbal, or physical hostility toward terminal operations teams, flight attendants, or fellow passengers. The captain commands total administrative jurisdiction over the aircraft during flight operations. Any passenger displaying symptoms of chemical intoxication, aggressive behavior, or refusal to comply with cabin crew instructions will be safely contained, offloaded at the nearest waypoint, permanently banned from the SkyZen ecosystem, and handed over to international aviation enforcement authorities.</p>
                
                <!-- separate the style -->
                <h4 style="color: var(--theme-green); margin-top:30px;">
                    PART II: PRIVACY POLICY & USER DATA PROTECTION
                </h4>
                <p>
                    <strong>Global Privacy Policy and Data Stewardship Charter</strong>
                </p>

                <h4>
                    1. Core Architecture of Personal Data Harvesting
                </h4>
                <p>
                    SkyZen Airlines is committed to protecting user privacy through responsible data handling. To fulfill your ticketing contracts and provide online service utilities, we handle specific segments of personal indicators. This dataset includes your first and last name, geographical country markers, contact parameters (mobile number and electronic mail address), age data, and specialized flight historical tracks. Additionally, operational system metrics, system logs, and browsing attributes are collected automatically to improve the platform's stability and layout performance.
                </p>

                <h4>
                    2. How Your Travel and Profile Information is Processed
                </h4>
                <p>
                    The metrics you share during your interaction with our service are processed for the following operational tasks:
                    </p>
                <ul>
                    <li><strong>Ticketing and Authentication:</strong> Processing and executing your flight inquiries, constructing secure Passenger Name Records, and generating flight validation documentation.</li>
                    <li><strong>System Alerts and Notices:</strong> Dispatphing real-time notifications regarding changes to flight timetables, terminal updates, and electronic boarding receipts.</li>
                    <li><strong>Interface Tuning:</strong> Monitoring web application latency and resolving navigation friction to optimize the passenger interface across various devices.</li>
                    <li><strong>Compliance Mandates:</strong> Satisfying mandatory border control, immigration review, and global aviation tracking legal frameworks.</li>
                </ul>

                <h4>
                    3. Data Retention and Safety Protocols
                </h4>
                <p>
                    Your user profile metrics, structural transaction values, and booking logs are securely retained for as long as your passenger profile remains active or as required by financial, tax, and aviation auditing frameworks. We enforce industry-standard structural security controls, secure socket network protocols, and cryptographic measures across our platform to safeguard your data against external tampering, accidental loss, or unauthorized profiling.
                </p>
                <p>
                    <em>Passenger Data Privacy Assurance:</em> 
                    SkyZen Airlines will never sell, lease, or distribute your private profile indicators, email addresses, or transaction histories to third-party marketing companies for promotional purposes.
                </p>

                <h4>
                    4. Third-Party Sharing and Flight Integration Networks
                </h4>
                <p>
                    To successfully deliver your travel experience, certain information must be shared with authorized operational entities, including ground handling networks, catering providers, secure financial clearing houses, and state immigration desks. Every single data-sharing event is conducted in strict compliance with safety frameworks to guarantee that your profile indicators remain fully protected throughout your journey.
                </p>

                <h4>
                    5. Individual Privacy Rights and Data Control Choices
                </h4>
                <p>
                    As a valued passenger of SkyZen Airlines, you maintain comprehensive management controls over your personal information. You can access your data, update biographical text directly through your profile view, request corrections to typographical errors in your travel records, or request complete account deletion, provided there are no active, unfulfilled flight contracts or legal retention mandates associated with your profile.
                </p>

                <h4>
                    6. Revisions and Amendments to These Policies
                </h4>
                <p>
                    SkyZen Airlines reserves the right to modify these regulatory Terms and Privacy Policies at any time to reflect changing international aviation safety standards or updated technical frameworks. When significant adjustments are made, a prominent update alert will be deployed across the primary user dashboard, and a notice will be sent to your registered email address. Continued engagement with our booking system or boarding services after such notifications constitutes complete acceptance of the updated terms.
                </p>
            <button id="tpAcceptBtn" class="btn-premium" disabled onclick="acceptTP()"><i class='fa fa-check'></i> I hereby accept the Terms and Conditions.</button>

            </div>

            <!-- separate the style -->            
        </div>
    </div>

    <div class="nk-auth-wrapper">    
        <div class="nk-auth-brand">
            <div class="brand-logo">
                <div class="brand-logo-icon"><i class="fa-solid fa-plane"></i></div>
                SkyZen Airlines
    </div>
        
        <h1>Welcome to SkyZen Airlines!</h1>
        <p class="brand-sub">Airline Reservation System for SkyZen Airlines Admins</p>
        
        <div class="feature-list">
            <div class="feature-item"><i class="fa-solid fa-shield-halved"></i> Secure Authentication</div>
            <div class="feature-item"><i class="fa-solid fa-chart-line"></i> Real-time Flight Data</div>
            <div class="feature-item"><i class="fa-solid fa-bolt"></i> Lightning Fast Bookings</div>
        </div>
    </div>

    <div class="nk-auth-form-panel">
        <div class="auth-card" style="max-width: 550px;"> 
            <h2>Create Account</h2>
            <p class="subtitle">Fill in the details to get started</p>

    <form onsubmit="event.preventDefault(); registerUserFunc();">
                
                <div class="form-row">
                    <div class="form-group form-col">
                        <label class="form-label">First Name <span class="req-mark">*</span></label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-user"></i></div>
                            <input type="text" id="regFirstName" class="form-control" maxlength="30" placeholder="First Name" required>
                        </div>
                    </div>
                    
                    <div class="form-group form-col">
                        <label class="form-label">Middle Name</label>
                        <div class="input-group">
                            <input type="text" id="regmiddleName" class="form-control" placeholder="Middle Name" maxlength="30">
                        </div>
                    </div>

                    <div class="form-group form-col">
                        <label class="form-label">Last Name <span class="req-mark">*</span></label>
                        <div class="input-group">
                            <input type="text" id="regLastName" class="form-control" maxlength="30" placeholder="Last Name" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address <span class="req-mark">*</span></label>
                    <div class="input-group">
                        <div class="input-icon"><i class="fa-solid fa-envelope"></i></div>
                        <input type="email" id="regEmail" class="form-control" maxlength="30" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col">
                        <label class="form-label">Phone Number <span class="req-mark">*</span></label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-phone"></i></div>
                            <input type="text" id="regPhone" class="form-control" maxlength="15" placeholder="Phone Number" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                    </div>
                    
                    <div class="form-group form-col">
                        <label class="form-label">Birthday <span class="req-mark">*</span></label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-cake-candles"></i></div>
                            <input type="date" id="regBirthday" class="form-control" required style="padding-right: 14px;" max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col">
                        <label class="form-label">Username <span class="req-mark">*</span></label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-at"></i></div>
                            <input type="text" id="regUsername" class="form-control" maxlength="10" placeholder="Choose a username" required>
                        </div>
                    </div>
                    
                    <div class="form-group form-col">
                        <label class="form-label">Password <span class="req-mark">*</span></label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-lock"></i></div>
                            <input type="password" id="regPassword" class="form-control" maxlength="15" placeholder="Create a password" required>
                            <button type="button" class="toggle-password" onclick="togglePass('regPassword', 'eyeIcon1')">
                                <i class="fa-solid fa-eye" id="eyeIcon1"></i>
                            </button>

                        </div>                            
                            <ul class="password-reqs" id="password-reqs">
                                <li id="req-length"><i class="fa-solid fa-xmark"></i> At least 8 characters</li>
                                <li id="req-upper"><i class="fa-solid fa-xmark"></i> At least 1 uppercase letter</li>
                                <li id="req-lower"><i class="fa-solid fa-xmark"></i> At least 1 lowercase letter</li>
                                <li id="req-number"><i class="fa-solid fa-xmark"></i> At least 1 number</li>
                                <li id="req-special"><i class="fa-solid fa-xmark"></i> At least 1 underscore (_)</li>
                            </ul>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col">
                        <label class="form-label">Confirm Password <span class="req-mark">*</span></label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-lock"></i></div>
                            <input type="password" id="regConfirmPassword" class="form-control" maxlength="15" placeholder="Repeat your password" required>
                            <button type="button" class="toggle-password" onclick="togglePass('regConfirmPassword', 'eyeIcon2')">
                                <i class="fa-solid fa-eye" id="eyeIcon2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                    <div class="form-check">
                        <input type="checkbox" id="terms" required disabled>
                        <label for="terms">I agree to the 
                            <a href="javascript:void(0)" onclick="openTP()"><u>Terms of Service & Privacy Policy.</u>*</a>
                        </label>
                    </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-user-plus"></i> Create Account
                </button>

                <div class="auth-divider"><span>or continue with</span></div>

                <div class="social-btns">
                    <button type="button" class="social-btn"><i class="fa-brands fa-google"></i></button>
                    <button type="button" class="social-btn"><i class="fa-brands fa-github"></i></button>
                    <button type="button" class="social-btn"><i class="fa-brands fa-facebook-f"></i></button>
                </div>

                <div class="auth-bottom">
                    Already have an account? <a href="skyzenLoginPage.php">Log In</a>
                </div>

            </form>
        </div>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../scripts/service.js"></script>
</body>
</html>