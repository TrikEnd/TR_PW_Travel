
document.addEventListener('DOMContentLoaded', function () {
    const btnChooseSeat   = document.getElementById('btn_choose_seat');
    const seatModal       = document.getElementById('seatModalBackdrop');
    const seatModalClose  = document.getElementById('seatModalClose');
    const seatModalCancel = document.getElementById('seatModalCancel');
    const seatModalApply  = document.getElementById('seatModalApply');
    const seatGrid        = document.getElementById('seatGrid');
    const seatInput       = document.getElementById('seat_input_1');
    const hiddenSeatInput = document.getElementById('no_kursi_1');
    const isDisabilityChk = document.getElementById('is_disability');

    if (!btnChooseSeat || !seatModal || !seatGrid) {
        // kalau elemen tidak ada (halaman lain), stop
        return;
    }

    // Konfigurasi kursi
    const rows = 6;                      // 6 baris: 1 - 6
    const cols = 6;                      // 6 kolom: A - F
    const colLetters = ['A','B','C','D','E','F'];

    let selectedSeatCode = null;
    let isDisabilityMode = false;

    // Buka modal
    btnChooseSeat.addEventListener('click', function () {
        seatModal.style.display = 'flex';
        renderSeats();
    });

    // Tutup modal
    function closeModal() {
        seatModal.style.display = 'none';
    }

    if (seatModalClose) {
        seatModalClose.addEventListener('click', closeModal);
    }
    if (seatModalCancel) {
        seatModalCancel.addEventListener('click', closeModal);
    }

    // Checkbox disabilitas (di form utama)
    if (isDisabilityChk) {
        isDisabilityChk.addEventListener('change', () => {
            isDisabilityMode = isDisabilityChk.checked;
            // Kalau modal lagi kebuka, update kursi
            if (seatModal.style.display === 'flex') {
                renderSeats();
            }
        });
    }

    // Generate kursi
    function renderSeats() {
        seatGrid.innerHTML = '';

        for (let r = 1; r <= rows; r++) {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'seat-row-line';

            for (let c = 0; c < cols; c++) {
                const seatCode = r + colLetters[c];  // contoh: 1A, 1B, dst
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = seatCode;
                btn.className = 'seat-btn';

                // Kursi dekat jendela: kolom A & F → kursi disabilitas
                const isDisabilitySeat = (colLetters[c] === 'A' || colLetters[c] === 'F');
                btn.dataset.seatCode = seatCode;
                btn.dataset.disability = isDisabilitySeat ? '1' : '0';

                if (isDisabilitySeat) {
                    btn.classList.add('seat-window'); // beda warna
                } else {
                    btn.classList.add('seat-available');
                }

                // Aturan klik berdasarkan mode disabilitas
                if (!isDisabilityMode && isDisabilitySeat) {
                    // mode normal: kursi disabilitas tidak bisa dipilih
                    btn.classList.add('seat-disabled');
                }

                if (selectedSeatCode === seatCode) {
                    btn.classList.add('seat-selected');
                }

                btn.addEventListener('click', function () {
                    handleSeatClick(btn);
                });

                rowDiv.appendChild(btn);
            }

            seatGrid.appendChild(rowDiv);
        }
    }

    function handleSeatClick(btn) {
        const seatCode = btn.dataset.seatCode;
        const isDisSeat = btn.dataset.disability === '1';

        // Jika kursi disabilitas tapi mode biasa
        if (!isDisabilityMode && isDisSeat) {
            alert('Kursi dekat jendela ini diprioritaskan untuk penumpang dengan disabilitas.\nSilakan centang "Saya penumpang dengan disabilitas" jika sesuai.');
            return;
        }

        // Kalau sudah terpilih, unselect
        if (selectedSeatCode === seatCode) {
            selectedSeatCode = null;
        } else {
            selectedSeatCode = seatCode;
        }

        // Refresh tampilan kursi
        refreshSeatSelection();
    }

    function refreshSeatSelection() {
        const allSeats = seatGrid.querySelectorAll('.seat-btn');
        allSeats.forEach(btn => {
            btn.classList.remove('seat-selected');

            const isDisSeat = btn.dataset.disability === '1';

            // Lock kursi disabilitas jika bukan mode disabilitas
            if (!isDisabilityMode && isDisSeat) {
                btn.classList.add('seat-disabled');
            } else {
                btn.classList.remove('seat-disabled');
            }

            if (btn.dataset.seatCode === selectedSeatCode) {
                btn.classList.add('seat-selected');
            }
        });
    }

    // Simpan kursi terpilih ke input
    if (seatModalApply) {
        seatModalApply.addEventListener('click', function () {
            if (!selectedSeatCode) {
                alert('Silakan pilih kursi terlebih dahulu.');
                return;
            }
            if (seatInput) seatInput.value       = selectedSeatCode;
            if (hiddenSeatInput) hiddenSeatInput.value = selectedSeatCode;
            closeModal();
        });
    }
});
