<?php
session_start();
require_once "../model/database_airlines.php";
require_once "../bl/userManagement.php";

$isLoggedIn = isset($_SESSION['user']);
$firstName = $isLoggedIn ? htmlspecialchars($_SESSION['user']['users_firstName']) : '';

$userManagement = new UserManagement();
// Fetch deals under 12,500 PHP
$promoFlights = method_exists($userManagement, 'getPromoFlights') ? $userManagement->getPromoFlights(12500) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Seat Sale & Promos | SkyZen Airlines</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--bg-cream);">

    <div class="manage-booking-hero" style="padding-top: 60px; background: linear-gradient(135deg, #D9232D, #F59E0B);">
        <h1 style="color: white; font-size: 3rem; text-transform: uppercase; font-weight: 900;"><i class="fa-solid fa-fire"></i> Mega Seat Sale</h1>
        <p style="color: white; font-size: 1.2rem;">Book now and fly for less! Limited seats available.</p>
    </div>

    <main class="skyzen-container" style="padding: 60px 20px; max-width: 1000px;">
        
        <?php if(empty($promoFlights)): ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 8px;">
                <h3 style="color: #64748B;">No active promos right now. Check back later!</h3>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <?php foreach($promoFlights as $flight): 
                    $price = $flight['flights_basePrice'] ?? $flight['flights_price'] ?? 0;
                ?>
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); position: relative;">
                        <div style="position: absolute; top: 15px; right: 15px; background: #DC2626; color: white; padding: 5px 15px; border-radius: 20px; font-weight: 800; font-size: 0.8rem; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);">
                            HOT DEAL
                        </div>
                        <div style="padding: 30px;">
                            <div style="color: #64748B; font-size: 0.9rem; font-weight: 700; margin-bottom: 10px;">
                                <?= date('M d, Y', strtotime($flight['flights_departureTime'])) ?>
                            </div>
                            <h3 style="margin: 0 0 5px 0; color: #005A9C; display: flex; align-items: center; gap: 10px;">
                                <?= htmlspecialchars($flight['flights_originCode']) ?> <i class="fa-solid fa-arrow-right" style="color: #CBD5E1; font-size: 1rem;"></i> <?= htmlspecialchars($flight['flights_destinationCode']) ?>
                            </h3>
                            <p style="margin: 0 0 20px 0; font-size: 0.85rem; color: #94A3B8;">
                                <?= htmlspecialchars($flight['originName']) ?> to <?= htmlspecialchars($flight['destName']) ?>
                            </p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                                <div>
                                    <span style="font-size: 0.8rem; color: #64748B; text-decoration: line-through;">₱<?= number_format($price + 5000, 2) ?></span>
                                    <div style="color: #D97706; font-size: 2rem; font-weight: 900; line-height: 1;">₱<?= number_format($price, 2) ?></div>
                                </div>
                                <a href="skyzenMainPage.php" class="skyzen-submit-btn" style="width: auto; padding: 10px 20px; text-decoration: none;">Book</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>