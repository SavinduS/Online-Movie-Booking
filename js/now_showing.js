// Swans Cinema - Now Showing Page JavaScript
// Enhanced UX with smooth interactions and booking functionality

document.addEventListener('DOMContentLoaded', function () {
    initializeNowShowingPage();
});

function initializeNowShowingPage() {
    setupBookingButtons();
    setupMovieCardInteractions();
    setupSmoothScrolling();
    setupImageLazyLoading();
    setupKeyboardNavigation();
    checkBookingStatus();
}

// Booking buttons
function setupBookingButtons() {
    const bookTicketButtons = document.querySelectorAll('.book-ticket-btn-savi, .book-now-btn-savi');
    const viewDetailsButtons = document.querySelectorAll('.view-details-btn-savi');

    bookTicketButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const movieCard = this.closest('.movie-card-savi');
            const movieId = movieCard.getAttribute('data-movie-id');
            const movieTitle = movieCard.querySelector('.movie-title-savi').textContent;
            handleBookingClick(movieId, movieTitle);
        });
    });

    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const movieCard = this.closest('.movie-card-savi');
            const movieId = movieCard.getAttribute('data-movie-id');
            showMovieDetails(movieId);
        });
    });
}

function handleBookingClick(movieId, movieTitle) {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;

    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    button.disabled = true;

    setTimeout(() => {
        showBookingConfirmation(movieTitle);
        button.innerHTML = originalText;
        button.disabled = false;
    }, 1500);
}

function showBookingConfirmation(movieTitle) {
    const modal = createModal(`
        <div class="booking-modal-savi">
            <div class="modal-icon-savi"><i class="fas fa-check-circle"></i></div>
            <h3>Booking Initiated</h3>
            <p>Redirecting you to book tickets for <strong>${movieTitle}</strong></p>
            <button class="btn-primary-savi modal-close-btn-savi">
                <i class="fas fa-ticket-alt"></i> Continue to Booking
            </button>
        </div>
    `);
    document.body.appendChild(modal);

    modal.querySelector('.modal-close-btn-savi').addEventListener('click', () => {
        closeModal(modal);
        // window.location.href = 'booking.php'; // Uncomment in production
    });
}

function showMovieDetails(movieId) {
    const movieCard = document.querySelector(`[data-movie-id="${movieId}"]`);
    const title = movieCard.querySelector('.movie-title-savi').textContent;
    const category = movieCard.querySelector('.movie-category-savi span').textContent;
    const rating = movieCard.querySelector('.rating-badge-savi span').textContent;
    const description = movieCard.querySelector('.movie-description-savi')?.textContent || 'No description available.';
    const poster = movieCard.querySelector('.movie-poster-savi').src;

    const modal = createModal(`
        <div class="movie-details-modal-savi">
            <button class="modal-close-btn-savi modal-x-btn-savi"><i class="fas fa-times"></i></button>
            <div class="modal-content-savi">
                <div class="modal-poster-savi"><img src="${poster}" alt="${title}"></div>
                <div class="modal-info-savi">
                    <h2>${title}</h2>
                    <div class="modal-meta-savi">
                        <span class="modal-category-savi"><i class="fas fa-tag"></i> ${category}</span>
                        <span class="modal-rating-savi"><i class="fas fa-star"></i> ${rating}</span>
                    </div>
                    <p class="modal-description-savi">${description}</p>
                    <div class="modal-actions-savi">
                        <button class="btn-primary-savi modal-book-btn-savi" data-movie-id="${movieId}">
                            <i class="fas fa-ticket-alt"></i> Book Tickets
                        </button>
                        <button class="btn-secondary-savi modal-trailer-btn-savi">
                            <i class="fas fa-play"></i> Watch Trailer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `);
    document.body.appendChild(modal);

    modal.querySelector('.modal-x-btn-savi').addEventListener('click', () => closeModal(modal));
    modal.querySelector('.modal-book-btn-savi').addEventListener('click', () => {
        closeModal(modal);
        handleBookingClick(movieId, title);
    });
    modal.querySelector('.modal-trailer-btn-savi').addEventListener('click', () => {
        showNotification('Trailer feature coming soon!', 'info');
    });
}

function createModal(content) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay-savi';
    modal.innerHTML = content;

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal(modal);
    });

    document.addEventListener('keydown', function escListener(e) {
        if (e.key === 'Escape') {
            closeModal(modal);
            document.removeEventListener('keydown', escListener);
        }
    });

    return modal;
}

function closeModal(modal) {
    modal.style.opacity = '0';
    setTimeout(() => {
        if (modal.parentNode) modal.parentNode.removeChild(modal);
    }, 300);
}

function setupMovieCardInteractions() {
    const movieCards = document.querySelectorAll('.movie-card-savi');

    movieCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
        });
        card.addEventListener('click', function (e) {
            if (!e.target.closest('button')) {
                const movieId = this.getAttribute('data-movie-id');
                showMovieDetails(movieId);
            }
        });
    });
}

function setupSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

function setupImageLazyLoading() {
    const images = document.querySelectorAll('.movie-poster-savi');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }
}

function setupKeyboardNavigation() {
    // Optional: Add keyboard support for modals or movie grid nav
}

function checkBookingStatus() {
    // Optional: Display alert or banner if redirected from booking
}

function showNotification(message, type = 'info') {
    alert(message); // You can customize this to use toast messages instead of alert
}
