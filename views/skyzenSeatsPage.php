<?php
session_cache_limiter('private_no_expire');
session_start();
require_once "../model/database_airlines.php"; // Required to check for taken seats!

if (!isset($_SESSION['user'])) { header('Location: skyzenLoginPage.php'); exit; }

$flightID = $_POST['flightID'] ?? null;
$flightPrice = $_POST['flightPrice'] ?? 0;
$paxCount = $_POST['paxCount'] ?? 1;

if (!$flightID) { header('Location: skyzenMainPage.php'); exit; }

// 1. Collect Passenger Names from previous step
$paxNames = [];
for ($i = 1; $i <= $paxCount; $i++) {
    $paxNames[] = $_POST['pax_name_'.$i] ?? 'Passenger '.$i;
}

// 2. Fetch Taken Seats from the Database!
$takenSeats = [];
try {
    $db = (new Database())->connect();
    // Get all tickets for this flight where a seat has already been assigned
    $stmt = $db->prepare("SELECT tickets_seatNumber FROM tbl_tickets WHERE tickets_flightID = :fid AND tickets_seatNumber != 'TBA' AND tickets_seatNumber IS NOT NULL");
    $stmt->execute([':fid' => $flightID]);
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $takenSeats[] = $row['tickets_seatNumber'];
    }
} catch(Exception $e) {
    // Failsafe if db errors out
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Choose Seats | SkyZen</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background-color: var(--bg-cream);">
    
    <div class="booking-progress-container" style="margin-bottom: 30px;">
        <div class="skyzen-container">
            <h2 class="progress-title" onclick="window.location.href='skyzenMainPage.php'">Booking Wizard</h2>
            <div class="progress-tracker">
                <div class="progress-step clickable" style="color:#16A34A; border-color:#16A34A;" onclick="window.history.go(-1)"><span>1/4</span> Flight & Passengers</div>
                <div class="progress-step active" style="color:#005A9C; border-color:#005A9C;"><span>2/4</span> Choose Seats</div>
                <div class="progress-step"><span>3/4</span> Add Extras</div>
                <div class="progress-step"><span>4/4</span> Review & Pay</div>
            </div>
        </div>
    </div>

    <main class="skyzen-container" style="max-width: 600px; padding-bottom: 60px;">
        <div style="background: white; padding: 30px; border-radius: 8px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h2 style="color: #005A9C; margin-top: 0;"><i class="fa-solid fa-chair"></i> Choose Your Seats</h2>
            <p style="color: #64748B;">Please select exactly <strong style="color: #111827; font-size: 1.2rem;"><?= $paxCount ?></strong> seat(s) for your trip.</p>

            <form action="skyzenExtrasPage.php" method="POST" id="seatForm">
                <input type="hidden" name="flightID" value="<?= htmlspecialchars($flightID) ?>">
                <input type="hidden" name="flightPrice" value="<?= htmlspecialchars($flightPrice) ?>">
                <input type="hidden" name="paxCount" id="maxPax" value="<?= htmlspecialchars($paxCount) ?>">
                <?php foreach($paxNames as $index => $name): ?>
                    <input type="hidden" name="pax_name_<?= $index+1 ?>" value="<?= htmlspecialchars($name) ?>">
                <?php endforeach; ?>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin: 30px auto; max-width: 300px;">
                    <?php 
                    $rows = ['A', 'B', 'C', 'D'];
                    // Let's generate 8 rows of seats for a more realistic plane
                    for($r=1; $r<=8; $r++): 
                        foreach($rows as $col): 
                            $seatNum = $r.$col;
                            // Check if this seat is inside our database array of taken seats!
                            $isTaken = in_array($seatNum, $takenSeats);
                    ?>
                        <?php if($isTaken): ?>
                            <label class="seat-label taken" title="Seat Unavailable">
                                <input type="checkbox" disabled style="display:none;">
                                <div class="seat-box seat-taken"><?= $seatNum ?></div>
                            </label>
                        <?php else: ?>
                            <label class="seat-label">
                                <input type="checkbox" name="seats[]" value="<?= $seatNum ?>" class="seat-checkbox" style="display:none;">
                                <div class="seat-box"><?= $seatNum ?></div>
                            </label>
                        <?php endif; ?>
                    <?php 
                        endforeach; 
                    endfor; 
                    ?>
                </div>

                <button type="submit" class="skyzen-submit-btn">Confirm Seats & Continue</button>
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>