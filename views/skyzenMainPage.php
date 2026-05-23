<?php
    session_start();
    require_once "../bl/userManagement.php";
    $userManagement = new userManagement();
    
    $airports = [];
    if (method_exists($userManagement, 'getAirports')) {
        $airports = $userManagement->getAirports();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Flights | SkyZen Airlines</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- PUBLIC NAVIGATION -->
    <nav class="skyzen-public-nav">
        <div class="skyzen-nav-container">
            <div class="skyzen-brand">
                <i class="fa-solid fa-plane"></i> SkyZen Airlines
            </div>
            
            <ul class="skyzen-nav-links">
                <li><a href="skyzenHome.php" class="active">Book</a></li>
                <li class="has-mega-menu">
                    <a href="#" >Manage</a>
                    <!-- MEGA MENU DROPDOWN -->
                    <div class="mega-menu">
                        <div class="mega-menu-top">
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-location-dot"></i></div>
                                Check in
                            </a>
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-file-signature"></i></div>
                                Manage Booking
                            </a>
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-plane-circle-check"></i></div>
                                Flight Status
                            </a>
                        </div>
                    </div>
                </li>
                <li><a href="#">Travel Info</a></li>
                <li><a href="#">Explore</a></li>
                <li><a href="#">About</a></li>
            </ul>

            <div class="skyzen-nav-actions">
                <a href="skyzenLoginPage.php" class="skyzen-login-btn"><i class="fa fa-user-circle"></i> Log in</a>
                <a href="#" class="skyzen-search-icon"><i class="fa-solid fa-magnifying-glass"></i></a>
            </div>
        </div>
    </nav>

    <main class="skyzen-home-main">
        <!-- HERO SECTION & SEARCH WIDGET -->
        <div class="skyzen-hero" id="heroSlider">
            <div class="skyzen-hero-overlay"></div>
            
            <div class="skyzen-search-container">
                <h1 class="skyzen-hero-title">More flights, more adventures.</h1>
                
                <div class="skyzen-search-widget">
                    <div class="skyzen-widget-tabs">
                        <button type="button" class="skyzen-tab active" onclick="setTripType('round', this)">Round-trip</button>
                        <button type="button" class="skyzen-tab" onclick="setTripType('one', this)">One-way</button>
                        <button type="button" class="skyzen-tab" onclick="setTripType('multi', this)">Multi-city</button>
                    </div>

                    <form action="skyzenSelectFlight.php" method="GET" class="skyzen-widget-form">
                        <div class="skyzen-form-grid">
                            <!-- Routing -->
                            <div class="skyzen-input-group">
                                <label>Origin</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-solid fa-plane-departure"></i>
                                    <select name="origin" id="searchOrigin" required>
                                        <option value="" disabled selected>Where from?</option>
                                        <?php foreach($airports as $apt): ?>
                                            <option value="<?= htmlspecialchars($apt['airportsCode']) ?>"><?= htmlspecialchars($apt['airportsName']) ?> (<?= htmlspecialchars($apt['airportsCode']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <button type="button" class="skyzen-swap-btn" onclick="swapAirports()"><i class="fa-solid fa-right-left"></i></button>

                            <div class="skyzen-input-group">
                                <label>Destination</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-solid fa-plane-arrival"></i>
                                    <select name="dest" id="searchDest" required>
                                        <option value="" disabled selected>Where to?</option>
                                        <?php foreach($airports as $apt): ?>
                                            <option value="<?= htmlspecialchars($apt['airportsCode']) ?>"><?= htmlspecialchars($apt['airportsName']) ?> (<?= htmlspecialchars($apt['airportsCode']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="skyzen-input-group">
                                <label>Depart</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-regular fa-calendar"></i>
                                    <input type="date" name="dep_date" min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>

                            <div class="skyzen-input-group" id="returnDateGroup">
                                <label>Return</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-regular fa-calendar-check"></i>
                                    <input type="date" name="ret_date" id="returnDate" min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>

                            <!-- Passengers -->
                            <div class="skyzen-input-group">
                                <label>Passengers</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-solid fa-user-group"></i>
                                    <select name="pax" required>
                                        <option value="1">1 Pax(s)</option>
                                        <option value="2">2 Pax(s)</option>
                                        <option value="3">3 Pax(s)</option>
                                        <option value="4">4 Pax(s)</option>
                                        <option value="5">5+ Pax(s)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="skyzen-widget-footer">
                            <div class="skyzen-promo-wrapper">
                                <i class="fa-solid fa-tag"></i>
                                <input type="text" name="promo" placeholder="Promo Code">
                            </div>
                            <button type="submit" class="skyzen-submit-btn">Search flights</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- FEATURES SECTION (c-everyoneflies) -->
        <section class="skyzen-features-section">
            <div class="skyzen-container">
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-check-to-slot"></i></div>
                        <h4>Check In</h4>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-ticket"></i></div>
                        <h4>Super Pass</h4>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-plane-circle-check"></i></div>
                        <h4>Flight Status</h4>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                        <h4>Manage Booking</h4>
                    </div>
                </div>
            </div>
        </section>

        <!-- CHEAP FLIGHTS PROMO SECTION (c-cheap-flights) -->
        <section class="skyzen-promo-section">
            <div class="skyzen-container">
                <h2>Book cheap flights from</h2>
                <div class="promo-tabs">
                    <button class="active">Manila</button>
                    <button>Cebu</button>
                    <button>Davao</button>
                    <button>Clark</button>
                </div>
                
                <div class="promo-cards-grid">
                    <!-- Promo Card 1 -->
                    <div class="promo-card">
                        <img src="https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?q=80&w=600&auto=format&fit=crop" alt="Cebu">
                        <div class="promo-details">
                            <span class="promo-label">For as low as</span>
                            <h3 class="promo-price">₱388*</h3>
                            <span class="promo-dest">Cebu</span>
                            <button class="btn-book-now">Book now</button>
                        </div>
                    </div>
                    <!-- Promo Card 2 -->
                    <div class="promo-card">
                        <img src="https://images.unsplash.com/photo-1542296332-2e4473faf563?q=80&w=600&auto=format&fit=crop" alt="Boracay">
                        <div class="promo-details">
                            <span class="promo-label">For as low as</span>
                            <h3 class="promo-price">₱298*</h3>
                            <span class="promo-dest">Iloilo</span>
                            <button class="btn-book-now">Book now</button>
                        </div>
                    </div>
                    <!-- Promo Card 3 -->
                    <div class="promo-card">
                        <img src="https://images.unsplash.com/photo-1531804055935-76f44d22d204?q=80&w=600&auto=format&fit=crop" alt="Siargao">
                        <div class="promo-details">
                            <span class="promo-label">For as low as</span>
                            <h3 class="promo-price">₱799*</h3>
                            <span class="promo-dest">Laoag</span>
                            <button class="btn-book-now">Book now</button>
                        </div>
                    </div>
                </div>
                <div class="promo-disclaimer">*One-way base fares</div>
            </div>
        </section>

        <!-- FOOTER (c-footer) -->
        <footer class="skyzen-footer">
            <div class="skyzen-container">
                <div class="footer-grid">
                    <div class="footer-col">
                        <h4>BOOK</h4>
                        <ul>
                            <li><a href="#">Flights</a></li>
                            <li><a href="#">Seat Sale</a></li>
                            <li><a href="#">Partner Agents</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>MANAGE</h4>
                        <ul>
                            <li><a href="#">Check in</a></li>
                            <li><a href="#">Manage Booking</a></li>
                            <li><a href="#">Flight Status</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>EXPLORE</h4>
                        <ul>
                            <li><a href="#">Philippine Destinations</a></li>
                            <li><a href="#">International Destinations</a></li>
                            <li><a href="#">Where We Fly</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>ABOUT</h4>
                        <ul>
                            <li><a href="#">Our Story</a></li>
                            <li><a href="#">Careers</a></li>
                            <li><a href="#">Talk to Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="footer-legal">
                        <a href="#">Privacy and Cookie Policy</a>
                        <a href="#">Website Terms of Use</a>
                    </div>
                    <div class="footer-copyright">
                        <p>© Copyright <?= date('Y') ?> SkyZen Airlines</p>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script src="../scripts/service.js"></script>
</body>
</html>