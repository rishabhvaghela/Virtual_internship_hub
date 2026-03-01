(function () {

  let container = document.querySelector(".toast-container");

  if (!container) {

    container = document.createElement("div");
    container.className = "toast-container";
    document.body.appendChild(container);

  }

  window.showToast = function (message, type = "info", duration = 2500) {

    const toast = document.createElement("div");

    toast.className = `toast ${type}`;
    toast.textContent = message;

    container.appendChild(toast);

    requestAnimationFrame(() => {
      toast.classList.add("show");
    });

    setTimeout(() => {

      toast.classList.remove("show");

      setTimeout(() => {
        toast.remove();
      }, 350);

    }, duration);

  };

})();