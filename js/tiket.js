document.addEventListener("DOMContentLoaded", () => {
    const btnPrint = document.getElementById("btnPrint");
    if (btnPrint) {
        btnPrint.addEventListener("click", () => {
            window.print();
        });
    }
});
