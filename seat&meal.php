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
$total = ($baseFare + $taxes + $serviceFee) * $passengers;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat & Extras - Flight Booking</title>
    <link rel="stylesheet" href="style/seat.css">
    <link rel="stylesheet" href="component/navbar.css">
</head>
<body>

<?php include 'component/navbar.php'; ?>
<div class="navbar-space"></div>

<div class="steps-wrapper">
    <ul class="steps">
        <li class="step-item active">
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

        <li class="step-item">
            <div class="step-badge">4</div>
            <span>E-ticket</span>
        </li>
    </ul>
</div>

<main class="main">
    <section>
        <article class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Seat Selection & Extras</div>
                    <div class="card-subtitle">
                        Pilih kursi, layanan disabilitas, bagasi, dan makanan di pesawat.
                    </div>
                </div>
            </div>

            <form class="passenger-form" method="post" action="d.form.php">
                <div class="card-subtitle" style="margin-bottom:6px;">Seat Selection</div>

                <div class="seat-legend">
                    <span class="dot available"></span> Available
                    <span class="dot selected"></span> Selected
                    <span class="dot taken"></span> Taken
                </div>

                <div class="seat-grid">
                    <?php
                    $rows = [10, 11, 12, 13];
                    $takenSeats = ['10C', '11D', '12B']; 
                    foreach ($rows as $row) {
                        echo '<div class="seat-row">';
                        echo '<div class="seat-row-label">'.$row.'</div>';

                        foreach (['A','B','C','D','E','F'] as $col) {
                            $code = $row.$col;
                            $isTaken = in_array($code, $takenSeats);
                            ?>
                            <label class="seat-option <?php echo $isTaken ? 'taken' : ''; ?>">
                                <input 
                                    type="radio" 
                                    name="seat" 
                                    value="<?php echo $code; ?>" 
                                    <?php echo $isTaken ? 'disabled' : ''; ?>
                                >
                                <span><?php echo $col; ?></span>
                            </label>
                            <?php
                        }

                        echo '</div>';
                    }
                    ?>
                </div>

                <div class="form-hint">
                    Kursi dekat lorong cocok untuk mobilitas terbatas, kursi dekat jendela cocok untuk kenyamanan ekstra.
                </div>

                <hr class="card-divider">

                <div class="card-subtitle" style="margin-bottom:6px;">Disability Assistance</div>

                <div class="form-row">
                    <div class="form-group" style="flex:1 1 100%;">
                        <label class="form-label">Pilih layanan tambahan jika diperlukan</label>
                        <div class="extra-options">
                            <label>
                                <input type="checkbox" name="disability[]" value="wheelchair">
                                Wheelchair assistance (kursi roda dari check-in ke gate)
                            </label>
                            <label>
                                <input type="checkbox" name="disability[]" value="priority_boarding">
                                Priority boarding (naik pesawat lebih awal)
                            </label>
                            <label>
                                <input type="checkbox" name="disability[]" value="visual_hearing">
                                Assistance for visual / hearing impairment
                            </label>
                        </div>
                        <div class="form-hint">
                            Maskapai mungkin akan menghubungi Anda untuk konfirmasi detail kebutuhan khusus.
                        </div>
                    </div>
                </div>

                <hr class="card-divider">

                <div class="card-subtitle" style="margin-bottom:6px;">Baggage Options</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Cabin Baggage</label>
                        <input type="text" class="form-input" value="Max 7 kg (included)" readonly>
                        <div class="form-hint">Sesuai kebijakan maskapai.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Checked Baggage</label>
                        <select class="form-select" name="checked_baggage">
                            <option value="20">20 kg (included)</option>
                            <option value="25">+5 kg (25 kg total) - IDR 150.000</option>
                            <option value="30">+10 kg (30 kg total) - IDR 250.000</option>
                            <option value="40">+20 kg (40 kg total) - IDR 400.000</option>
                        </select>
                        <div class="form-hint">
                            Penambahan bagasi lebih murah jika dibeli sekarang dibandingkan di bandara.
                        </div>
                    </div>
                </div>

                <hr class="card-divider">

                <div class="card-subtitle" style="margin-bottom:6px;">In-flight Meal</div>

                <div class="form-row">
                    <div class="form-group" style="flex:1 1 100%;">
                        <label class="form-label">Pilih makanan yang diinginkan</label>
                        <div class="extra-options">
                            <label>
                                <input type="radio" name="meal" value="standard" checked>
                                Standard meal (Nasi + lauk) - included
                            </label>
                            <label>
                                <input type="radio" name="meal" value="vegetarian">
                                Vegetarian meal
                            </label>
                            <label>
                                <input type="radio" name="meal" value="snack">
                                Snack only (roti / kue + minuman)
                            </label>
                            <label>
                                <input type="radio" name="meal" value="no_meal">
                                No meal (saya tidak ingin makan)
                            </label>
                        </div>
                        <div class="form-hint">
                            Opsi makanan khusus (vegan, alergi tertentu) sebaiknya diinformasikan ke maskapai minimal 24 jam sebelum keberangkatan.
                        </div>
                    </div>
                </div>

                <hr class="card-divider">

                <div class="card-subtitle" style="margin-bottom:6px;">Catatan Tambahan</div>

                <div class="form-row">
                    <div class="form-group" style="flex:1 1 100%;">
                        <label class="form-label">Kebutuhan khusus lain (opsional)</label>
                        <textarea 
                            class="form-input" 
                            name="notes" 
                            rows="3" 
                            style="resize:vertical; min-height:70px;"
                            placeholder="Contoh: butuh kursi dekat toilet, alergi kacang, dll."></textarea>
                    </div>
                </div>

                <button type="submit" class="submit-btn" style="margin-top:18px;">
                    Continue to Passenger Details
                </button>
            </form>
        </article>
    </section>

    <aside class="card">
        <div class="summary-title">Flight Summary</div>

        <div class="price-row">
            <span>Route</span>
            <span><?php echo $routeFrom . " → " . $routeTo; ?></span>
        </div>
        <div class="price-row muted">
            <span>Date</span>
            <span><?php echo $flightDate; ?></span>
        </div>
        <div class="price-row muted">
            <span>Flight</span>
            <span><?php echo $airlineName . " · " . $flightNo; ?></span>
        </div>

        <hr class="card-divider" />

        <div class="summary-title" style="font-size:0.9rem; margin-bottom:8px;">Price (before extras)</div>

        <div class="price-row">
            <span>Base Fare (<?php echo $passengers; ?> Adult)</span>
            <span><?php echo "IDR " . number_format($baseFare * $passengers, 0, ',', '.'); ?></span>
        </div>
        <div class="price-row muted">
            <span>Taxes & Fees</span>
            <span><?php echo "IDR " . number_format($taxes * $passengers, 0, ',', '.'); ?></span>
        </div>
        <div class="price-row muted">
            <span>Service Fee</span>
            <span><?php echo "IDR " . number_format($serviceFee * $passengers, 0, ',', '.'); ?></span>
        </div>

        <hr class="card-divider" />

        <div class="price-row total">
            <span>Subtotal</span>
            <span><?php echo "IDR " . number_format($total, 0, ',', '.'); ?></span>
        </div>

        <div class="pill-info">
            Penambahan bagasi & makanan khusus mungkin menambah total harga pada langkah berikutnya.
        </div>
    </aside>
</main>

</body>
</html>
