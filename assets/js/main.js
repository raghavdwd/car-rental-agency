document.querySelectorAll(".needs-confirm").forEach((button) => {
  button.addEventListener("click", (event) => {
    const text = button.getAttribute("data-confirm-text") || "Are you sure?";
    if (!window.confirm(text)) {
      event.preventDefault();
    }
  });
});
