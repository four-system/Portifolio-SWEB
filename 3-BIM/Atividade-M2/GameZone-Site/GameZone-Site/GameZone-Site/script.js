const menuToggle = document.querySelector(".menu-toggle");
const navLinks = document.querySelector(".nav-links");
const navItems = document.querySelectorAll(".nav-links a");
const filterButtons = document.querySelectorAll(".filter-btn");
const gameCards = document.querySelectorAll(".game-card");
const contactForm = document.querySelector("#contactForm");
const formFeedback = document.querySelector("#formFeedback");

function closeMenu() {
  if (!menuToggle || !navLinks) {
    return;
  }

  menuToggle.classList.remove("active");
  navLinks.classList.remove("open");
  document.body.classList.remove("menu-open");
  menuToggle.setAttribute("aria-expanded", "false");
}

if (menuToggle && navLinks) {
  menuToggle.addEventListener("click", () => {
    const isOpen = navLinks.classList.toggle("open");
    menuToggle.classList.toggle("active", isOpen);
    document.body.classList.toggle("menu-open", isOpen);
    menuToggle.setAttribute("aria-expanded", String(isOpen));
  });
}

navItems.forEach((item) => {
  item.addEventListener("click", closeMenu);
});

const currentPage = window.location.pathname.split("/").pop() || "index.html";

navItems.forEach((item) => {
  if (item.dataset.page === currentPage) {
    item.classList.add("current-page");
  }
});

filterButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const selectedCategory = button.dataset.filter;

    filterButtons.forEach((currentButton) => currentButton.classList.remove("active"));
    button.classList.add("active");

    gameCards.forEach((card) => {
      const shouldShow = selectedCategory === "todos" || card.dataset.category === selectedCategory;
      card.classList.toggle("hidden", !shouldShow);
    });
  });
});

if (contactForm && formFeedback) {
  contactForm.addEventListener("submit", (event) => {
    event.preventDefault();

    formFeedback.textContent = "Mensagem enviada! Obrigado por entrar em contato com o GameZone.";
    contactForm.reset();

    window.setTimeout(() => {
      formFeedback.textContent = "";
    }, 5000);
  });
}
