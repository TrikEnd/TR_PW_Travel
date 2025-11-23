document.addEventListener("DOMContentLoaded", () => {
    const metodeRadios = document.querySelectorAll('input[name="metode"]');
    const sectionVA   = document.getElementById("section-va");
    const sectionQRIS = document.getElementById("section-qris");
    const sectionDebit= document.getElementById("section-debit");

    const bankSelect  = document.getElementById("bank_va");
    const vaBox       = document.getElementById("va_box");
    const vaNumberDisp= document.getElementById("va_number_display");
    const vaNumberInput= document.getElementById("va_number_input");

    function showSection(metode) {
        sectionVA.style.display    = (metode === "VA") ? "block" : "none";
        sectionQRIS.style.display  = (metode === "QRIS") ? "block" : "none";
        sectionDebit.style.display = (metode === "Debit") ? "block" : "none";
    }

    // awal: VA aktif
    showSection("VA");

    metodeRadios.forEach(r => {
        r.addEventListener("change", () => {
            showSection(r.value);
        });
    });

    // generate VA ketika bank dipilih
    bankSelect.addEventListener("change", () => {
        const bank = bankSelect.value;
        if (!bank) {
            vaBox.style.display = "none";
            vaNumberDisp.textContent = "-";
            vaNumberInput.value = "";
            return;
        }
        // dummy generator: <bank code> + "88" + 6 digit terakhir timestamp
        const bankCodeMap = {
            "BCA": "014",
            "BNI": "009",
            "BRI": "002",
            "Mandiri": "008"
        };
        const bankCode = bankCodeMap[bank] || "000";
        const tail = ("" + Date.now()).slice(-6);
        const vaNumber = bankCode + "88" + tail;

        vaNumberDisp.textContent = vaNumber;
        vaNumberInput.value = vaNumber;
        vaBox.style.display = "block";
    });
});
