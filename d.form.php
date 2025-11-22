<?php
$routeFrom   = "CGK";
$routeTo     = "DPS";
$flightDate  = "Thu, 20 Nov 2025 · One-way";
$airlineCode = "GA";
$airlineName = "Garuda Indonesia";
$flightNo    = "GA 412";

$baseFare   = 1500000;
$taxes      = 200000;
$serviceFee = 20000;
$passengers = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Booking</title>

    <link rel="stylesheet" href="style/d.form.css">
    <link rel="stylesheet" href="component/navbar.css">
</head>

<body>
<?php include 'component/navbar.php'; ?>

<div class="navbar-space"></div>

<div class="steps-wrapper">
    <ul class="steps">
        <li class="step-item">
            <div class="step-badge">1</div>
            <span>Seat & Extras</span>
        </li>
        <span class="step-separator"></span>

        <li class="step-item active">
            <div class="step-badge">2</div>
            <span>Booking Details</span>
        </li>
        <span class="step-separator"></span>

        <li class="step-item">
            <div class="step-badge">3</div>
            <span>Payment</span>
        </li>
        <span class="step-separator"></span>

        <li class="step-item">
            <div class="step-badge">4</div>
            <span>E-ticket</span>
        </li>
    </ul>
</div>

<main class="main">
    <section>
        <!-- CARD FLIGHT -->
        <article class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Your Flight</div>
                    <div class="card-subtitle">Please make sure your flight details are correct.</div>
                </div>
                <div class="badge-outline">Economy · <?php echo $passengers; ?> Adult</div>
            </div>

            <div class="flight-summary">
                <div class="route-top">
                    <div class="route-main">
                        <span><?php echo $routeFrom; ?></span> → <span><?php echo $routeTo; ?></span>
                    </div>
                    <div class="route-date"><?php echo $flightDate; ?></div>
                </div>

                <div class="airline-info">
                    <div class="airline-logo"><?php echo $airlineCode; ?></div>
                    <div class="airline-meta">
                        <span class="airline-name"><?php echo $airlineName; ?></span>
                        <span class="airline-class">Economy · <?php echo $flightNo; ?></span>
                    </div>
                </div>

                <div class="segment">
                    <div class="segment-col">
                        <div class="segment-time">07:00</div>
                        <div class="segment-airport">Soekarno-Hatta (CGK)</div>
                    </div>
                    <div class="segment-center">
                        <span class="duration">1h 55m</span>
                        <div class="line"></div>
                        <span>Direct flight</span>
                    </div>
                    <div class="segment-col" style="text-align: right;">
                        <div class="segment-time">09:55</div>
                        <div class="segment-airport">Ngurah Rai (DPS)</div>
                    </div>
                </div>

                <div class="segment-badges">
                    <span class="segment-badge">Cabin baggage 7 kg</span>
                    <span class="segment-badge">Checked baggage 20 kg</span>
                    <span class="segment-badge">Reschedule available</span>
                    <span class="segment-badge">Non-refundable</span>
                </div>
            </div>
        </article>

        <article class="card" style="margin-top: 14px;">
            <div class="card-header">
                <div>
                    <div class="card-title">Passenger Details</div>
                    <div class="card-subtitle">Make sure the name and ID match the passenger's official document.</div>
                </div>
            </div>

            <form id="bookingForm" class="passenger-form" method="post" action="#">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Title <span>*</span></label>
                        <select class="form-select" name="title">
                            <option value="">Select title</option>
                            <option value="MR">Mr</option>
                            <option value="MRS">Mrs</option>
                            <option value="MS">Ms</option>
                        </select>
                        <div class="form-error">Title is required.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">First Name <span>*</span></label>
                        <input type="text" class="form-input" name="firstName" placeholder="As on ID" />
                        <div class="form-error">First name is required.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-input" name="lastName" placeholder="Optional" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date of Birth <span>*</span></label>
                        <input type="date" class="form-input" name="dob" />
                        <div class="form-error">Date of birth is required.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nationality <span>*</span></label>
                        <select class="form-select" name="nationality">
                            <option value="">Select nationality</option>
                            <option value="ID">Indonesia</option>
                            <option value="MY">Malaysia</option>
                            <option value="SG">Singapore</option>
                            <option value="TH">Thailand</option>
                            <option value="OTHER">Other</option>
                        </select>
                        <div class="form-error">Nationality is required.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ID / Passport Number <span>*</span></label>
                        <input type="text" class="form-input" name="idNumber" />
                        <div class="form-error">ID / Passport number is required.</div>
                    </div>
                </div>

                <hr class="card-divider" />

                <div class="card-subtitle" style="margin-bottom: 4px;">Contact Details</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email <span>*</span></label>
                        <input type="email" class="form-input" name="email" placeholder="you@example.com" />
                        <div class="form-error">Valid email is required.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number <span>*</span></label>
                        <input type="tel" class="form-input" name="phone" placeholder="+62..." />
                        <div class="form-error">Phone number is required.</div>
                        <div class="form-hint">We'll send your e-ticket to this number.</div>
                    </div>
                </div>

                <div class="form-hint">
                    By continuing, you confirm that all passenger details are correct and match the official ID/passport.
                </div>
            </form>
        </article>
    </section>

    <aside class="card">
        <div class="summary-title">Price Summary</div>

        <div class="price-row">
            <span>Base Fare (<?php echo $passengers; ?> Adult)</span>
            <span id="baseFareText"><?php echo "IDR " . number_format($baseFare, 0, ',', '.'); ?></span>
        </div>

        <div class="price-row muted">
            <span>Taxes & Fees</span>
            <span id="taxesText"><?php echo "IDR " . number_format($taxes, 0, ',', '.'); ?></span>
        </div>

        <div class="price-row muted">
            <span>Service Fee</span>
            <span id="serviceText"><?php echo "IDR " . number_format($serviceFee, 0, ',', '.'); ?></span>
        </div>

        <div class="price-row muted">
            <span>Passenger(s)</span>
            <span id="passengerCountText"><?php echo $passengers; ?></span>
        </div>

        <hr class="card-divider" />

        <div class="price-row total">
            <span>Total Payment</span>
            <span id="totalText">
                <?php 
                $total = $baseFare + $taxes + $serviceFee;
                echo "IDR " . number_format($total, 0, ',', '.'); 
                ?>
            </span>
        </div>

        <div class="pill-info">
            <strong>Free reschedule fee</strong> may apply on specific dates. Airlines may charge additional fare difference.
        </div>

        <a href="payment.php" class="submit-btn" 
           style="display:block; text-align:center; margin-top:14px;">
            Continue to Payment
        </a>

        <div class="summary-footer-text">
            By clicking “Continue to Payment”, you agree to our terms & conditions and privacy policy.
        </div>
    </aside>
</main>

</body>
</html>

