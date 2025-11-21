<?php
// Data dummy, nanti bisa diambil dari DB / session
$bookingCode = "TRV12345";
$passengerName = "Mr memek wangi 123 Passenger";
$routeFrom   = "CGK";
$routeTo     = "DPS";
$flightDate  = "Thu, 20 Nov 2025";
$flightTime  = "07:00 - 09:55";
$airlineName = "Garuda Indonesia";
$flightNo    = "GA 412";
$seat        = "12A";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-ticket - Flight Booking</title>
    <link rel="stylesheet" href="style/d.form.css">
    <link rel="stylesheet" href="component/navbar.css">
</head>
<body>

<?php include 'component/navbar.php'; ?>

<div class="navbar-space"></div>

<!-- STEPS (Step 4 aktif) -->
<div class="steps-wrapper">
    <ul class="steps">
        <li class="step-item">
            <div class="step-badge">1</div>
            <span>Seat & Extras</span>
        </li>
        <span class="step-separator"></span>

        <li class="step-item">
            <div class="step-badge">2</div>
            <span>Booking Details</span>
        </li>
        <span class="step-separator"></span>

        <li class="step-item">
            <div class="step-badge">3</div>
            <span>Payment</span>
        </li>
        <span class="step-separator"></span>

        <li class="step-item active">
            <div class="step-badge">4</div>
            <span>E-ticket</span>
        </li>
    </ul>
</div>

<main class="main" style="grid-template-columns:minmax(0,1fr); max-width:800px;">
    <section>
        <article class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Your E-ticket is Ready</div>
                    <div class="card-subtitle">Show this ticket at the airport during check-in.</div>
                </div>
                <div class="badge-outline">
                    Booking Code: <strong><?php echo $bookingCode; ?></strong>
                </div>
            </div>

            <div class="flight-summary">
                <div class="route-top">
                    <div class="route-main">
                        <span><?php echo $routeFrom; ?></span> → <span><?php echo $routeTo; ?></span>
                    </div>
                    <div class="route-date"><?php echo $flightDate . " · " . $flightTime; ?></div>
                </div>

                <div class="airline-info">
                    <div class="airline-logo"><?php echo substr($airlineName,0,2); ?></div>
                    <div class="airline-meta">
                        <span class="airline-name"><?php echo $airlineName; ?></span>
                        <span class="airline-class">Flight <?php echo $flightNo; ?></span>
                    </div>
                </div>

                <hr class="card-divider">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Passenger</label>
                        <div style="font-size:0.9rem; font-weight:600;">
                            <?php echo $passengerName; ?>
                        </div>
                        <div class="form-hint">Adult</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Seat</label>
                        <div style="font-size:0.9rem; font-weight:600;">
                            <?php echo $seat; ?>
                        </div>
                        <div class="form-hint">Assigned seat</div>
                    </div>
                </div>

                <div class="form-row" style="margin-top:8px;">
                    <div class="form-group">
                        <label class="form-label">Check-in Info</label>
                        <div class="form-hint">
                            Please arrive at the airport at least 2 hours before departure. 
                            Have your ID/passport and this e-ticket ready.
                        </div>
                    </div>
                </div>

                <hr class="card-divider">

                <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
                    <div style="width:120px; height:120px; border-radius:12px; border:1px dashed #cbd0e3;
                                display:flex; align-items:center; justify-content:center; font-size:0.75rem; color:#6c757d;">
                        QR CODE
                    </div>
                    <div style="font-size:0.78rem; color:#6c757d; max-width:320px;">
                        This QR code is a placeholder. In a real system, it would contain your booking information 
                        and be scannable at the airport.
                    </div>
                </div>

                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:18px;">
                    <button class="submit-btn" type="button" style="flex:0 0 auto; max-width:220px;">
                        Download E-ticket (PDF)
                    </button>
                    <button class="submit-btn" type="button" style="flex:0 0 auto; max-width:160px; background:#fff; color:#0d6efd; box-shadow:none; border:1px solid #cbd0e3;">
                        Print Ticket
                    </button>
                </div>

                <div class="summary-footer-text" style="margin-top:14px; text-align:left;">
                    A copy of this e-ticket has also been sent to your email.
                </div>
            </div>
        </article>
    </section>
</main>

<script src="script.js"></script>
</body>
</html>
