document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-booking");

    // ====== SEAT POPUP LOGIC ======
    const seatBackdrop = document.getElementById("seatModalBackdrop");
    const btnChooseSeat = document.getElementById("btn_choose_seat");
    const btnClose = document.getElementById("seatModalClose");
    const btnCancel = document.getElementById("seatModalCancel");
    const btnApply = document.getElementById("seatModalApply");
    const seatGrid = document.getElementById("seatGrid");
    const seatInput = document.getElementById("seat_input_1");
    const seatHidden = document.getElementById("no_kursi_1");

    let tempSelectedSeat = null; // seat terpilih di popup (belum di-apply)

    // Generate kursi: 10 baris, kolom A–D
    const rows = 10;
    const cols = ["A", "B", "C", "D"]; // A & D = window (disability)
    for (let r = 1; r <= rows; r++) {
        for (let c = 0; c < cols.length; c++) {
            const code = `${r}${cols[c]}`;

            const item = document.createElement("div");
            item.classList.add("seat-item");
            item.dataset.code = code;

            // window seat = disability only (A dan D)
            if (cols[c] === "A" || cols[c] === "D") {
                item.classList.add("disabled", "seat-window");
                item.title = "Window seat – reserved for disabled passengers";
            } else {
                item.classList.add("seat-available");
            }

            item.textContent = code;
            seatGrid.appendChild(item);
        }
    }

    // buka modal
    function openSeatModal() {
        tempSelectedSeat = seatHidden.value || null;
        // tandai seat yang sudah disimpan sebelumnya
        document.querySelectorAll(".seat-item").forEach(el => {
            el.classList.remove("selected");
            if (tempSelectedSeat && el.dataset.code === tempSelectedSeat) {
                el.classList.add("selected");
            }
        });
        seatBackdrop.classList.add("show");
    }

    // tutup modal
    function closeSeatModal() {
        seatBackdrop.classList.remove("show");
    }

    // klik seat
    seatGrid.addEventListener("click", (e) => {
        const target = e.target;
        if (!target.classList.contains("seat-item")) return;
        if (target.classList.contains("disabled")) {
            alert("Window seats are reserved for passengers with disabilities.");
            return;
        }

        document.querySelectorAll(".seat-item").forEach(el => el.classList.remove("selected"));
        target.classList.add("selected");
        tempSelectedSeat = target.dataset.code;
    });

    // apply seat ke input
    btnApply.addEventListener("click", () => {
        if (tempSelectedSeat) {
            seatInput.value = tempSelectedSeat;
            seatHidden.value = tempSelectedSeat;
        }
        closeSeatModal();
    });

    // event open / close
    btnChooseSeat.addEventListener("click", openSeatModal);
    btnClose.addEventListener("click", closeSeatModal);
    btnCancel.addEventListener("click", closeSeatModal);

    // klik backdrop untuk tutup
    seatBackdrop.addEventListener("click", (e) => {
        if (e.target === seatBackdrop) {
            closeSeatModal();
        }
    });

    // ====== SUBMIT: susun nama & tanggal lahir ke hidden input ======
    form.addEventListener("submit", function () {
        const first = document.getElementById("first_name_1").value.trim();
        const last  = document.getElementById("last_name_1").value.trim();
        const fullName = (first + " " + last).trim();
        document.getElementById("nama_penumpang_1").value = fullName;

        const seatVal = seatInput.value.trim();
        seatHidden.value = seatVal;

        const dd = document.getElementById("dob_day_1").value.trim().padStart(2, "0");
        const mm = document.getElementById("dob_month_1").value.trim().padStart(2, "0");
        const yy = document.getElementById("dob_year_1").value.trim();

        if (dd && mm && yy && yy.length === 4) {
            document.getElementById("tanggal_lahir_1").value = `${yy}-${mm}-${dd}`;
        }
        // validasi umur bisa ditambah di sini kalau mau
    });
});
