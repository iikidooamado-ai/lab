// assets/toast.js
function showToast(message, type = "info", timeout = 3000) {
    const container = document.getElementById("toast-container");
    if (!container) {
        // fallback
        alert(message);
        return;
    }

    const colorClass = {
        success: "bg-success",
        error: "bg-danger",
        warning: "bg-warning text-dark",
        info: "bg-secondary"
    }[type] || "bg-secondary";

    const toast = document.createElement("div");
    toast.className = `toast align-items-center text-white ${colorClass} border-0 show`;
    toast.style.minWidth = "220px";
    toast.style.marginTop = "8px";
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" aria-label="Close"></button>
        </div>
    `;

    // close button handler
    toast.querySelector(".btn-close").addEventListener("click", function () {
        toast.remove();
    });

    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, timeout);
}