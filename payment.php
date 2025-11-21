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

$total = $baseFare + $taxes + $serviceFee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>

    <!-- CSS form & layout (sama seperti booking) -->
    <link rel="stylesheet" href="style/d.form.css">
    <!-- CSS navbar -->
    <link rel="stylesheet" href="component/navbar.css">
</head>

<body>
<?php include 'component/navbar.php'; ?>

<div class="navbar-space"></div>

<!-- STEPS: Payment aktif -->
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

        <li class="step-item active">
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
    <!-- KIRI: METODE PEMBAYARAN -->
    <section>
        <article class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Choose Payment Method</div>
                    <div class="card-subtitle">Complete your payment to issue the ticket.</div>
                </div>
            </div>

            <!-- Form hanya tampilan, belum kirim ke mana-mana -->
            <form class="passenger-form" method="post" action="#">
                <!-- Virtual Account -->
                <div class="card-subtitle" style="margin-bottom:6px;">Virtual Account / Bank Transfer</div>
                <div class="form-row">
                    <div class="form-group" style="flex:1 1 100%;">
                        <div class="extra-options">
                            <label>
                                <input type="radio" name="method" value="bca_va" checked>
                                BCA Virtual Account
                            </label>
                            <label>
                                <input type="radio" name="method" value="bri_va">
                                BRI Virtual Account
                            </label>
                            <label>
                                <input type="radio" name="method" value="bni_va">
                                BNI Virtual Account
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="card-divider">

                <!-- E-Wallet -->
                <div class="card-subtitle" style="margin-bottom:6px;">E-Wallet</div>
                <div class="form-row">
                    <div class="form-group" style="flex:1 1 100%;">
                        <div class="extra-options">
                            <label>
                                <input type="radio" name="method" value="gopay">
                                GoPay
                            </label>
                            <label>
                                <input type="radio" name="method" value="ovo">
                                OVO
                            </label>
                            <label>
                                <input type="radio" name="method" value="dana">
                                DANA
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="card-divider">

                <!-- Kartu -->
                <div class="card-subtitle" style="margin-bottom:6px;">Credit / Debit Card</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Card Number</label>
                        <input type="text" class="form-input" placeholder="•••• •••• •••• ••••">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expiry</label>
                        <input type="text" class="form-input" placeholder="MM/YY">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CVV</label>
                        <input type="password" class="form-input" placeholder="•••">
                    </div>
                </div>
                <div class="form-hint">contoh tok</div>
            </form>
        </article>
    </section>

    <!-- KANAN: RINGKASAN + TOMBOL KE E-TICKET -->
    <aside class="card">
        <div class="summary-title">Price Summary</div>

        <div class="price-row">
            <span>Base Fare (<?php echo $passengers; ?> Adult)</span>
            <span><?php echo "IDR " . number_format($baseFare, 0, ',', '.'); ?></span>
        </div>

        <div class="price-row muted">
            <span>Taxes & Fees</span>
            <span><?php echo "IDR " . number_format($taxes, 0, ',', '.'); ?></span>
        </div>

        <div class="price-row muted">
            <span>Service Fee</span>
            <span><?php echo "IDR " . number_format($serviceFee, 0, ',', '.'); ?></span>
        </div>

        <hr class="card-divider" />

        <div class="price-row total">
            <span>Total Payment</span>
            <span><?php echo "IDR " . number_format($total, 0, ',', '.'); ?></span>
        </div>

        <div class="pill-info">
            <strong>Secure Payment</strong> – your transaction is protected with encryption.
        </div>

        <a href="eticket.php" class="submit-btn" 
           style="display:block; text-align:center; margin-top:14px;">
            Pay Now & Get E-ticket
        </a>

        <div class="summary-footer-text">
            By clicking “Pay Now & Get E-ticket”, you agree to our terms & conditions.
        </div>
    </aside>
</main>

</body>
</html>

