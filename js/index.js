// ✅ Auto Scroll Function
function autoScrollCarousel(containerId, speed = 1.5) {
  const container = document.getElementById(containerId);

  if (!container) return;

  let scroll = 0;

  function loop() {
    scroll += speed;
    container.scrollLeft = scroll;

    // when reaching scroll end, reset seamlessly
    if (scroll >= container.scrollWidth / 2) {
      scroll = 0;
      container.scrollLeft = 0;
    }

    requestAnimationFrame(loop);
  }

  requestAnimationFrame(loop);
}

window.addEventListener("DOMContentLoaded", () => {
  autoScrollCarousel('trending-carousel');
  autoScrollCarousel('upcoming-carousel');
  autoScrollCarousel('otherfilms-carousel');
});
// ✅ Live Search Filter
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("searchInput");
  const allMovies = document.querySelectorAll(".movie-card-savi");

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      const keyword = searchInput.value.toLowerCase();

      allMovies.forEach(card => {
        const title = card.dataset.title.toLowerCase();
        const category = card.dataset.category.toLowerCase();

        if (title.includes(keyword) || category.includes(keyword)) {
          card.style.display = "flex";
        } else {
          card.style.display = "none";
        }
      });
    });
  }
});

// ✅ Category Button Filter
document.addEventListener("DOMContentLoaded", () => {
  const filterButtons = document.querySelectorAll(".filter-btn-savi");
  const movieCards = document.querySelectorAll(".movie-card-savi");

  filterButtons.forEach(button => {
    button.addEventListener("click", () => {
      const selectedCategory = button.getAttribute("data-category");

      // Update active class
      filterButtons.forEach(btn => btn.classList.remove("active"));
      button.classList.add("active");

      // Filter cards
      movieCards.forEach(card => {
        const cardCategory = card.dataset.category;

        if (selectedCategory === "all" || cardCategory === selectedCategory) {
          card.style.display = "flex";
        } else {
          card.style.display = "none";
        }
      });
    });
  });
});
