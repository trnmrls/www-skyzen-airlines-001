<?php
session_start();
$isLoggedIn = isset($_SESSION['user']);
$firstName = $isLoggedIn ? htmlspecialchars($_SESSION['user']['users_firstName']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baggage Information | SkyZen Airlines</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--bg-cream);">

    <div class="manage-booking-hero" style="padding-top: 60px;">
        <h1 style="color: #005A9C;">Baggage Information</h1>
        <p>Know your allowances, restrictions, and special baggage rules before packing.</p>
    </div>

    <main class="skyzen-container" style="padding: 60px 20px; max-width: 800px; min-height: 50vh;">
        
        <div class="dest-accordion">
            <div class="accordion-header">
                <h3><i class="fa-solid fa-briefcase" style="color: #005A9C; margin-right: 10px;"></i> Carry-on Baggage</h3>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="accordion-content">
                <p>Every passenger is entitled to bring <strong>one (1) carry-on bag</strong> and <strong>one (1) personal item</strong> (like a laptop bag or purse) into the cabin.</p>
                <ul>
                    <li><strong>Maximum weight:</strong> 7kg (15 lbs)</li>
                    <li><strong>Maximum dimensions:</strong> 56cm x 36cm x 23cm</li>
                </ul>
                <p>Liquids, aerosols, and gels must be in containers of 100ml or less, placed inside a single clear, resealable plastic bag.</p>
            </div>
        </div>

        <div class="dest-accordion">
            <div class="accordion-header">
                <h3><i class="fa-solid fa-suitcase-rolling" style="color: #005A9C; margin-right: 10px;"></i> Checked Baggage</h3>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="accordion-content">
                <p>Checked baggage allowances depend on your purchased seat class:</p>
                <ul>
                    <li><strong>Economy:</strong> Standard pre-paid allowance options of 20kg or 32kg.</li>
                    <li><strong>Business Class:</strong> Complimentary 40kg allowance.</li>
                    <li><strong>First Class:</strong> Complimentary 50kg allowance.</li>
                </ul>
                <p><em>Tip: Purchase prepaid baggage during the online booking process to save up to 40% compared to airport counter rates!</em></p>
            </div>
        </div>

        <div class="dest-accordion">
            <div class="accordion-header">
                <h3><i class="fa-solid fa-golf-club" style="color: #005A9C; margin-right: 10px;"></i> Special Baggage</h3>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="accordion-content">
                <p>Traveling with bulky items? We accommodate:</p>
                <ul>
                    <li><strong>Sports Equipment:</strong> Bicycles, surfboards, and golf clubs are accepted for a standard sports equipment fee.</li>
                    <li><strong>Musical Instruments:</strong> Small instruments can be carried on if they fit in the overhead bin. Large instruments must be checked or have a dedicated cabin seat purchased for them.</li>
                </ul>
            </div>
        </div>

        <div class="dest-accordion">
            <div class="accordion-header">
                <h3><i class="fa-solid fa-ban" style="color: #DC2626; margin-right: 10px;"></i> Restricted Items</h3>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="accordion-content">
                <p style="color: #DC2626; font-weight: bold;">For the safety of all passengers, the following items are strictly prohibited:</p>
                <ul>
                    <li>Explosives, fireworks, and flares.</li>
                    <li>Flammable liquids and solids (e.g., lighter fluid, matches).</li>
                    <li>Corrosives (e.g., bleach, acid).</li>
                </ul>
                <p><strong>Note on Lithium Batteries:</strong> Power banks and spare lithium batteries are strictly prohibited in checked baggage. They must be carried in your cabin baggage.</p>
            </div>
        </div>

    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>