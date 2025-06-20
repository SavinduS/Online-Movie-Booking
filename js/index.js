// ========== Enhanced Auto Scroll Function ==========
function autoScrollCarousel(containerId, speed = 0.5, direction = 1) {
  const container = document.getElementById(containerId);
  if (!container) return;

  let isHovered = false;
  let animationId;
  
  // Pause scrolling on hover
  container.addEventListener('mouseenter', () => {
    isHovered = true;
  });
  
  container.addEventListener('mouseleave', () => {
    isHovered = false;
  });

  function scroll() {
    if (!isHovered) {
      container.scrollLeft += speed * direction;
      
      // Reset to beginning when reaching the end
      if (container.scrollLeft >= container.scrollWidth - container.clientWidth) {
        container.scrollLeft = 0;
      }
      
      // Reset to end when reaching the beginning (for reverse scroll)
      if (container.scrollLeft <= 0 && direction === -1) {
        container.scrollLeft = container.scrollWidth - container.clientWidth;
      }
    }
    
    animationId = requestAnimationFrame(scroll);
  }
  
  // Start the animation
  animationId = requestAnimationFrame(scroll);
  
  // Return cleanup function
  return () => {
    if (animationId) {
      cancelAnimationFrame(animationId);
    }
  };
}

// ========== Initialize Auto Scroll ==========
document.addEventListener("DOMContentLoaded", () => {
  // Initialize carousels with different speeds and directions
  autoScrollCarousel('trending-carousel', 0.8, 1);
  autoScrollCarousel('upcoming-carousel', 0.6, -1); // Reverse direction
  autoScrollCarousel('ourfilms-carousel', 0.7, 1);
});

// ========== Enhanced Search Functionality ==========
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("searchInput");
  const allMovies = document.querySelectorAll(".movie-card-savi");
  const noResultsMessage = createNoResultsMessage();

  if (searchInput) {
    // Add search input animation
    searchInput.addEventListener('focus', () => {
      searchInput.parentElement.style.transform = 'scale(1.02)';
    });
    
    searchInput.addEventListener('blur', () => {
      searchInput.parentElement.style.transform = 'scale(1)';
    });

    // Enhanced search with debouncing
    let searchTimeout;
    searchInput.addEventListener("input", (e) => {
      clearTimeout(searchTimeout);
      
      searchTimeout = setTimeout(() => {
        const keyword = e.target.value.toLowerCase().trim();
        let visibleCount = 0;

        allMovies.forEach(card => {
          const title = card.dataset.title.toLowerCase();
          const category = card.dataset.category.toLowerCase();
          
          if (keyword === '' || title.includes(keyword) || category.includes(keyword)) {
            card.style.display = "block";
            card.style.animation = "fadeInUp-savi 0.5s ease-out";
            visibleCount++;
          } else {
            card.style.display = "none";
          }
        });

        // Show/hide no results message
        toggleNoResultsMessage(visibleCount === 0 && keyword !== '');
      }, 300); // 300ms debounce
    });
  }

  function createNoResultsMessage() {
    const message = document.createElement('div');
    message.className = 'no-results-savi';
    message.innerHTML = `
      <div class="no-results-content-savi">
        <i class="fas fa-search"></i>
        <h3>No movies found</h3>
        <p>Try searching with different keywords</p>
      </div>
    `;
    message.style.display = 'none';
    
    // Add styles
    const style = document.createElement('style');
    style.textContent = `
      .no-results-savi {
        text-align: center;
        padding: 60px 20px;
        color: rgba(255, 255, 255, 0.7);
      }
      
      .no-results-content-savi i {
        font-size: 4rem;
        color: #6a5acd;
        margin-bottom: 20px;
        opacity: 0.5;
      }
      
      .no-results-content-savi h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: white;
      }
      
      .no-results-content-savi p {
        font-size: 1rem;
        opacity: 0.8;
      }
    `;
    document.head.appendChild(style);
    
    return message;
  }

  function toggleNoResultsMessage(show) {
    const moviesWrapper = document.getElementById('allMovies');
    if (show) {
      if (!document.querySelector('.no-results-savi')) {
        moviesWrapper.appendChild(noResultsMessage);
      }
      noResultsMessage.style.display = 'block';
    } else {
      noResultsMessage.style.display = 'none';
    }
  }
});

// ========== Enhanced Category Filter ==========
document.addEventListener("DOMContentLoaded", () => {
  const filterButtons = document.querySelectorAll(".filter-btn-savi");
  const movieCards = document.querySelectorAll(".movie-card-savi");
  const sections = document.querySelectorAll(".section-savi");

  filterButtons.forEach(button => {
    button.addEventListener("click", () => {
      const selectedCategory = button.getAttribute("data-category");

      // Update active class with animation
      filterButtons.forEach(btn => {
        btn.classList.remove("active");
        btn.style.transform = "scale(1)";
      });
      
      button.classList.add("active");
      button.style.transform = "scale(1.05)";
      
      // Reset transform after animation
      setTimeout(() => {
        button.style.transform = "scale(1)";
      }, 200);

      // Filter cards with stagger animation
      let delay = 0;
      movieCards.forEach(card => {
        const cardCategory = card.dataset.category;
        
        if (selectedCategory === "all" || cardCategory === selectedCategory) {
          setTimeout(() => {
            card.style.display = "block";
            card.style.animation = "fadeInUp-savi 0.5s ease-out";
          }, delay);
          delay += 50; // Stagger effect
        } else {
          card.style.animation = "fadeOut-savi 0.3s ease-out";
          setTimeout(() => {
            card.style.display = "none";
          }, 300);
        }
      });

      // Update section visibility
      updateSectionVisibility(selectedCategory);
    });
  });

  function updateSectionVisibility(category) {
    sections.forEach(section => {
      const sectionCards = section.querySelectorAll('.movie-card-savi');
      const visibleCards = Array.from(sectionCards).filter(card => {
        return category === 'all' || card.dataset.category === category;
      });

      if (visibleCards.length === 0) {
        section.style.display = 'none';
      } else {
        section.style.display = 'block';
      }
    });
  }
});

// ========== Movie Card Interactions ==========
document.addEventListener("DOMContentLoaded", () => {
  const movieCards = document.querySelectorAll(".movie-card-savi");

  movieCards.forEach(card => {
    // Play button functionality
    const playBtn = card.querySelector('.play-btn-savi');
    const infoBtn = card.querySelector('.info-btn-savi');

    if (playBtn) {
      playBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const movieTitle = card.dataset.title;
        
        // Add ripple effect
        addRippleEffect(playBtn, e);
        
        // Handle play action (you can customize this)
        handlePlayMovie(movieTitle);
      });
    }

    if (infoBtn) {
      infoBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const movieTitle = card.dataset.title;
        
        // Add ripple effect
        addRippleEffect(infoBtn, e);
        
        // Handle info action (you can customize this)
        handleMovieInfo(movieTitle);
      });
    }

    // Card click to view details
    card.addEventListener('click', () => {
      const movieTitle = card.dataset.title;
      // Redirect to movie details page
      // window.location.href = `movie-details.php?title=${encodeURIComponent(movieTitle)}`;
      console.log(`Viewing details for: ${movieTitle}`);
    });
  });

  function addRippleEffect(button, event) {
    const rect = button.getBoundingClientRect();
    const ripple = document.createElement('span');
    const size = Math.max(button.offsetWidth, button.offsetHeight);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.className = 'ripple-savi';
    
    // Add ripple styles
    if (!document.querySelector('#ripple-styles-savi')) {
      const style = document.createElement('style');
      style.id = 'ripple-styles-savi';
      style.textContent = `
        .ripple-savi {
          position: absolute;
          border-radius: 50%;
          background: rgba(255, 255, 255, 0.6);
          transform: scale(0);
          animation: ripple-animation-savi 0.6s linear;
          pointer-events: none;
        }
        
        @keyframes ripple-animation-savi {
          to {
            transform: scale(2);
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);
    }
    
    button.style.position = 'relative';
    button.style.overflow = 'hidden';
    button.appendChild(ripple);
    
    setTimeout(() => {
      ripple.remove();
    }, 600);
  }

  function handlePlayMovie(movieTitle) {
    // Customize this function based on your needs
    console.log(`Playing movie: ${movieTitle}`);
    // You can redirect to booking page or show trailer
    // window.location.href = `booking.php?movie=${encodeURIComponent(movieTitle)}`;
  }

  function handleMovieInfo(movieTitle) {
    // Customize this function based on your needs
    console.log(`Showing info for: ${movieTitle}`);
    // You can show a modal or redirect to details page
    // showMovieModal(movieTitle);
  }
});

// ========== Smooth Scroll to Sections ==========
function scrollToSection(sectionId) {
  const section = document.getElementById(sectionId);
  if (section) {
    section.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
  }
}

// ========== Add Fade Animations ==========
const style = document.createElement('style');
style.textContent = `
  @keyframes fadeOut-savi {
    from {
      opacity: 1;
      transform: translateY(0);
    }
    to {
      opacity: 0;
      transform: translateY(-20px);
    }
  }
  
  @keyframes fadeInUp-savi {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
`;
document.head.appendChild(style);

// ========== Performance Optimization ==========
// Intersection Observer for lazy loading
const observerOptions = {
  root: null,
  rootMargin: '50px',
  threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('animate-in-savi');
    }
  });
}, observerOptions);

// Observe all movie cards for animation
document.addEventListener('DOMContentLoaded', () => {
  const movieCards = document.querySelectorAll('.movie-card-savi');
  movieCards.forEach(card => {
    observer.observe(card);
  });
});

// Add animation class styles
const animationStyles = document.createElement('style');
animationStyles.textContent = `
  .animate-in-savi {
    animation: slideInScale-savi 0.6s ease-out forwards;
  }
  
  @keyframes slideInScale-savi {
    from {
      opacity: 0;
      transform: translateY(30px) scale(0.9);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
`;
document.head.appendChild(animationStyles);