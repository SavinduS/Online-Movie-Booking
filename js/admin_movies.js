// Admin Movies JavaScript

// Global variables
let currentDeleteId = null;

// DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide messages after 5 seconds
    const messageAlert = document.getElementById('messageAlert');
    if (messageAlert) {
        setTimeout(() => {
            messageAlert.style.animation = 'slideOut 0.3s ease-in forwards';
            setTimeout(() => {
                messageAlert.remove();
            }, 300);
        }, 5000);
    }
});

// Modal Functions
function openAddModal() {
    const modal = document.getElementById('addModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Reset form
    document.getElementById('addMovieForm').reset();
}

function closeAddModal() {
    const modal = document.getElementById('addModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openEditModal() {
    const modal = document.getElementById('editModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    currentDeleteId = null;
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const modals = ['addModal', 'editModal', 'deleteModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal[style*="block"]');
        openModals.forEach(modal => {
            modal.style.display = 'none';
        });
        document.body.style.overflow = 'auto';
        currentDeleteId = null;
    }
});

// Search/Filter functionality
function filterMovies() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const movieCards = document.querySelectorAll('.movie-card');
    let visibleCount = 0;

    movieCards.forEach(card => {
        const title = card.getAttribute('data-title');
        const movieType = card.querySelector('.movie-type').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || movieType.includes(searchTerm)) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Update section title with count
    const sectionTitle = document.querySelector('.section-title');
    const totalMovies = movieCards.length;
    if (searchTerm) {
        sectionTitle.textContent = `Movies (${visibleCount} of ${totalMovies} shown)`;
    } else {
        sectionTitle.textContent = `Current Movies (${totalMovies})`;
    }
}

// Edit movie function
function editMovie(movieId) {
    // Make AJAX request to get movie data
    fetch(`get_movie.php?id=${movieId}`)
        .then(response => response.json())
        .then(movie => {
            if (movie.error) {
                alert('Error loading movie data: ' + movie.error);
                return;
            }

            // Populate edit form
            document.getElementById('editId').value = movie.id;
            document.getElementById('editTitle').value = movie.title;
            document.getElementById('editType').value = movie.type;
            document.getElementById('editPrice').value = movie.price;
            document.getElementById('editShowTime').value = movie.show_time;

            // Show current thumbnail if exists
            const currentThumbnail = document.getElementById('currentThumbnail');
            if (movie.thumbnail) {
                currentThumbnail.innerHTML = `
                    <div style="margin-top: 0.5rem;">
                        <small>Current image:</small><br>
                        <img src="${movie.thumbnail}" alt="Current thumbnail" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid #e6e6fa;">
                    </div>
                `;
            } else {
                currentThumbnail.innerHTML = '';
            }

            openEditModal();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading movie data. Please try again.');
        });
}

// Delete confirmation
function confirmDelete(movieId, movieTitle) {
    currentDeleteId = movieId;
    document.getElementById('deleteMovieTitle').textContent = movieTitle;
    openDeleteModal();
}

// Execute delete
function executeDelete() {
    if (!currentDeleteId) return;

    // Show loading state
    const deleteBtn = document.querySelector('.btn-danger');
    const originalText = deleteBtn.textContent;
    deleteBtn.textContent = 'Deleting...';
    deleteBtn.disabled = true;

    // Make delete request
    fetch('delete_movie.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${currentDeleteId}`
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Remove the card from DOM
            const movieCard = document.querySelector(`[data-movie-id="${currentDeleteId}"]`);
            if (movieCard) {
                movieCard.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => {
                    movieCard.remove();
                    updateMovieCount();
                }, 300);
            }
            
            closeDeleteModal();
            showMessage('Movie deleted successfully!', 'success');
        } else {
            alert('Error deleting movie: ' + (result.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting movie. Please try again.');
    })
    .finally(() => {
        deleteBtn.textContent = originalText;
        deleteBtn.disabled = false;
    });
}

// Update movie count in section title
function updateMovieCount() {
    const movieCards = document.querySelectorAll('.movie-card');
    const sectionTitle = document.querySelector('.section-title');
    const count = movieCards.length;
    sectionTitle.textContent = `Current Movies (${count})`;

    // Show empty state if no movies
    if (count === 0) {
        const moviesGrid = document.getElementById('moviesGrid');
        const moviesSection = document.querySelector('.movies-section');
        moviesGrid.style.display = 'none';
        
        const emptyState = document.createElement('div');
        emptyState.className = 'empty-state';
        emptyState.innerHTML = `
            <div class="empty-icon">🎭</div>
            <h3>No Movies Found</h3>
            <p>Start by adding your first movie to the collection</p>
            <button class="btn-primary" onclick="openAddModal()">Add Your First Movie</button>
        `;
        moviesSection.appendChild(emptyState);
    }
}

// Show message function
function showMessage(text, type = 'success') {
    // Remove existing message
    const existingMessage = document.getElementById('messageAlert');
    if (existingMessage) {
        existingMessage.remove();
    }

    // Create new message
    const message = document.createElement('div');
    message.id = 'messageAlert';
    message.className = `message ${type}`;
    message.innerHTML = `
        <span class="message-text">${text}</span>
        <button class="message-close" onclick="closeMessage()">✕</button>
    `;

    // Insert after header
    const header = document.querySelector('.admin-header');
    header.insertAdjacentElement('afterend', message);

    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (message.parentNode) {
            message.style.animation = 'slideOut 0.3s ease-in forwards';
            setTimeout(() => {
                if (message.parentNode) {
                    message.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Close message
function closeMessage() {
    const messageAlert = document.getElementById('messageAlert');
    if (messageAlert) {
        messageAlert.style.animation = 'slideOut 0.3s ease-in forwards';
        setTimeout(() => {
            if (messageAlert.parentNode) {
                messageAlert.remove();
            }
        }, 300);
    }
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const title = form.querySelector('[name="title"]').value.trim();
    const type = form.querySelector('[name="type"]').value;
    const price = form.querySelector('[name="price"]').value;
    const showTime = form.querySelector('[name="show_time"]').value.trim();

    if (!title) {
        alert('Please enter a movie title');
        return false;
    }

    if (!type) {
        alert('Please select a movie type');
        return false;
    }

    if (!price || parseFloat(price) < 0) {
        alert('Please enter a valid price');
        return false;
    }

    if (!showTime) {
        alert('Please enter show time');
        return false;
    }

    return true;
}

// Form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    // Add movie form
    const addForm = document.getElementById('addMovieForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            if (!validateForm('addMovieForm')) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Edit movie form
    const editForm = document.getElementById('editMovieForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (!validateForm('editMovieForm')) {
                e.preventDefault();
                return false;
            }
        });
    }
});

// File upload preview
function setupFilePreview() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add preview functionality here if needed
                    console.log('File selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    });
}

// Initialize file preview on page load
document.addEventListener('DOMContentLoaded', setupFilePreview);

// Add slideOut animation to CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(-20px);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }
`;
document.head.appendChild(style);