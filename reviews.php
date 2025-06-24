<?php include("partial/header.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swans Cinema | Reviews</title>
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9ff 0%, #e8e8ff 100%);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Hero Section Styles */
        .hero-section-savi {
            position: relative;
            height: 100vh;
            min-height: 700px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: white;
        }

        .hero-background-savi {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #6a4c93 0%, #9370db 50%, #dda0dd 100%);
            z-index: -2;
        }

        .hero-overlay-savi {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }

        .hero-particles-savi {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,0.3), transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.2), transparent),
                radial-gradient(1px 1px at 90px 40px, rgba(255,255,255,0.4), transparent),
                radial-gradient(1px 1px at 130px 80px, rgba(255,255,255,0.3), transparent),
                radial-gradient(2px 2px at 160px 30px, rgba(255,255,255,0.2), transparent);
            background-repeat: repeat;
            background-size: 200px 100px;
            animation: sparkle-savi 20s linear infinite;
        }

        @keyframes sparkle-savi {
            from { transform: translateY(0px); }
            to { transform: translateY(-100px); }
        }

        .hero-content-savi {
            text-align: center;
            max-width: 800px;
            padding: 0 20px;
            animation: fadeInUp-savi 1s ease-out;
        }

        @keyframes fadeInUp-savi {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-badge-savi {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 30px;
            animation: pulse-savi 2s infinite;
        }

        @keyframes pulse-savi {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .hero-title-savi {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .hero-accent-savi {
            background: linear-gradient(45deg, #fff, #dda0dd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description-savi {
            font-size: 1.3rem;
            font-weight: 300;
            margin-bottom: 50px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .hero-stats-savi {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 40px;
        }

        .stat-item-savi {
            text-align: center;
            animation: fadeInUp-savi 1s ease-out;
        }

        .stat-number-savi {
            font-size: 3rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .stat-label-savi {
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.8;
            margin-top: 5px;
        }

        .hero-scroll-indicator-savi {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 1.5rem;
            animation: bounce-savi 2s infinite;
            cursor: pointer;
        }

        @keyframes bounce-savi {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }

        .container-savi {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .section-title-savi {
            text-align: center;
            margin: 60px 0 20px 0;
            color: #4a4a4a;
            font-size: 2.5rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section-subtitle-savi {
            text-align: center;
            margin-bottom: 50px;
            color: #666;
            font-size: 1.2rem;
            font-weight: 400;
        }

        /* Review Cards Section */
        .review-container-savi {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .review-card-savi {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(147, 112, 219, 0.1);
            border: 1px solid rgba(147, 112, 219, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .edit-btn-savi,
        .delete-btn-savi {
            padding: 6px 14px;
            margin: 5px 4px 0 0;
            font-size: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        /* Edit button - lavender themed */
        .edit-btn-savi {
            background-color: #d9b3ff;
            color: #4b0082;
        }

        .edit-btn-savi:hover {
            background-color: #c799f0;
            box-shadow: 0 0 8px rgba(147, 112, 219, 0.4);
        }

        /* Delete button - soft red theme */
        .delete-btn-savi {
            background-color: #fddede;
            color: #b30000;
        }

        .delete-btn-savi:hover {
            background-color: #fbbbbb;
            box-shadow: 0 0 8px rgba(255, 0, 0, 0.2);
        }

        .review-card-savi::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #9370db, #dda0dd);
        }

        .review-card-savi:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 25px rgba(147, 112, 219, 0.2);
            border-color: #9370db;
        }

        .reviewer-info-savi {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .reviewer-avatar-savi {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9370db, #dda0dd);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .reviewer-name-savi {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .rating-savi {
            color: #ffc107;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .review-comment-savi {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.6;
            font-style: italic;
        }

        .review-date-savi {
            color: #999;
            font-size: 0.85rem;
            margin-top: 10px;
            text-align: right;
        }

        /* Form Section */
        .form-section-savi {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 6px 20px rgba(147, 112, 219, 0.1);
            border: 1px solid rgba(147, 112, 219, 0.1);
            margin-top: 50px;
        }

        .form-title-savi {
            text-align: center;
            color: #4a4a4a;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 30px;
            position: relative;
        }

        .form-title-savi::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #9370db, #dda0dd);
            border-radius: 2px;
        }

        .form-savi {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 30px;
        }

        .form-group-savi {
            display: flex;
            flex-direction: column;
        }

        .form-group-savi.full-width-savi {
            grid-column: span 2;
        }

        .label-savi {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .input-savi, .textarea-savi, .select-savi {
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .input-savi:focus, .textarea-savi:focus, .select-savi:focus {
            outline: none;
            border-color: #9370db;
            background: white;
            box-shadow: 0 0 0 3px rgba(147, 112, 219, 0.1);
        }

        .textarea-savi {
            resize: vertical;
            min-height: 120px;
        }

        .btn-savi {
            background: linear-gradient(135deg, #9370db, #dda0dd);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            justify-self: center;
        }

        .btn-savi:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(147, 112, 219, 0.3);
        }

        .btn-savi:active {
            transform: translateY(0);
        }

        .error-message-savi {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }

        .success-message-savi {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            border: 1px solid #c3e6cb;
            display: none;
        }

        /* Modal Styles for Edit Form */
        .modal-savi {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal-content-savi {
            background-color: white;
            margin: 5% auto;
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close-savi {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            margin-top: -20px;
            margin-right: -20px;
        }

        .close-savi:hover {
            color: #9370db;
        }

        .loading-savi {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner-savi {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #9370db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Alert Messages */
        .alert-savi {
            padding: 15px;
            margin: 20px 0;
            border-radius: 10px;
            font-weight: 500;
        }

        .alert-success-savi {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error-savi {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title-savi {
                font-size: 2.5rem;
            }

            .hero-description-savi {
                font-size: 1.1rem;
            }

            .hero-stats-savi {
                gap: 30px;
            }

            .stat-number-savi {
                font-size: 2rem;
            }

            .container-savi {
                padding: 15px;
            }

            .section-title-savi {
                font-size: 2rem;
            }

            .review-container-savi {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .form-savi {
                grid-template-columns: 1fr;
            }

            .form-group-savi.full-width-savi {
                grid-column: span 1;
            }

            .form-section-savi {
                padding: 25px;
            }

            .modal-content-savi {
                padding: 25px;
                margin: 10% auto;
            }
        }

        @media (max-width: 480px) {
            .hero-section-savi {
                min-height: 600px;
            }

            .hero-title-savi {
                font-size: 2rem;
            }

            .hero-stats-savi {
                flex-direction: column;
                gap: 20px;
            }

            .review-card-savi {
                padding: 20px;
            }

            .form-title-savi {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <!-- Alert Container -->
    <div id="alertContainer-savi"></div>

    <!-- Hero Section -->
    <section class="hero-section-savi">
        <div class="hero-background-savi">
            <div class="hero-particles-savi"></div>
            <div class="hero-overlay-savi"></div>
        </div>
        <div class="hero-content-savi">
            <div class="hero-badge-savi">
                <i class="fas fa-award"></i>
                <span>Rated #1 Cinema Experience</span>
            </div>
            <h1 class="hero-title-savi">
                What Our Customers Say
                <span class="hero-accent-savi">About Us</span>
            </h1>
            <p class="hero-description-savi">
                Discover why thousands of movie lovers choose Swans Cinema for their ultimate entertainment experience. 
                From blockbuster premieres to indie gems, we deliver unforgettable moments.
            </p>
            <div class="hero-stats-savi">
                <div class="stat-item-savi">
                    <div class="stat-number-savi" data-target="15000">0</div>
                    <div class="stat-label-savi">Happy Customers</div>
                </div>
                <div class="stat-item-savi">
                    <div class="stat-number-savi" data-target="9.8">0</div>
                    <div class="stat-label-savi">Average Rating</div>
                </div>
                <div class="stat-item-savi">
                    <div class="stat-number-savi" data-target="492">0</div>
                    <div class="stat-label-savi">Movies Screened</div>
                </div>
            </div>
        </div>
        <div class="hero-scroll-indicator-savi">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <div class="container-savi">
        <h2 class="section-title-savi">
            <i class="fas fa-comments"></i> Customer Reviews
        </h2>
        <p class="section-subtitle-savi">Real experiences from our valued moviegoers</p>

        <!-- Customer Reviews Section -->
        <div class="review-container-savi" id="reviewContainer-savi">
            <!-- Reviews will be loaded here -->
        </div>

        <!-- Submit Review Form -->
        <div class="form-section-savi">
            <h2 class="form-title-savi">
                <i class="fas fa-pen-alt"></i> Share Your Experience
            </h2>
            
            <form class="form-savi" id="reviewForm-savi" novalidate>
                <div class="form-group-savi">
                    <label for="name-savi" class="label-savi">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" id="name-savi" name="name" class="input-savi" required>
                    <div class="error-message-savi" id="nameError-savi"></div>
                </div>

                <div class="form-group-savi">
                    <label for="email-savi" class="label-savi">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email-savi" name="email" class="input-savi" required>
                    <div class="error-message-savi" id="emailError-savi"></div>
                </div>

                <div class="form-group-savi">
                    <label for="rating-savi" class="label-savi">
                        <i class="fas fa-star"></i> Rating
                    </label>
                    <select id="rating-savi" name="rating" class="select-savi" required>
                        <option value="">Select Rating</option>
                        <option value="5">5 Stars - Excellent</option>
                        <option value="4">4 Stars - Very Good</option>
                        <option value="3">3 Stars - Good</option>
                        <option value="2">2 Stars - Fair</option>
                        <option value="1">1 Star - Poor</option>
                    </select>
                    <div class="error-message-savi" id="ratingError-savi"></div>
                </div>

                <div class="form-group-savi full-width-savi">
                    <label for="comment-savi" class="label-savi">
                        <i class="fas fa-comment"></i> Your Review
                    </label>
                    <textarea id="comment-savi" name="comment" class="textarea-savi" placeholder="Share your experience with us..." required></textarea>
                    <div class="error-message-savi" id="commentError-savi"></div>
                </div>

                <div class="form-group-savi full-width-savi">
                    <button type="submit" class="btn-savi" id="submitBtn-savi">
                        <i class="fas fa-paper-plane"></i> Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Review Modal -->
    <div id="editModal-savi" class="modal-savi">
        <div class="modal-content-savi">
            <span class="close-savi" onclick="closeEditModal()">&times;</span>
            <h2 class="form-title-savi">
                <i class="fas fa-edit"></i> Edit Review
            </h2>
            
            <form class="form-savi" id="editForm-savi" novalidate>
                <input type="hidden" id="editId-savi" name="id">
                
                <div class="form-group-savi">
                    <label for="editName-savi" class="label-savi">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" id="editName-savi" name="name" class="input-savi" required>
                </div>

                <div class="form-group-savi">
                    <label for="editEmail-savi" class="label-savi">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="editEmail-savi" name="email" class="input-savi" required>
                </div>

                <div class="form-group-savi">
                    <label for="editRating-savi" class="label-savi">
                        <i class="fas fa-star"></i> Rating
                    </label>
                    <select id="editRating-savi" name="rating" class="select-savi" required>
                        <option value="">Select Rating</option>
                        <option value="5">5 Stars - Excellent</option>
                        <option value="4">4 Stars - Very Good</option>
                        <option value="3">3 Stars - Good</option>
                        <option value="2">2 Stars - Fair</option>
                        <option value="1">1 Star - Poor</option>
                    </select>
                </div>

                <div class="form-group-savi full-width-savi">
                    <label for="editComment-savi" class="label-savi">
                        <i class="fas fa-comment"></i> Your Review
                    </label>
                    <textarea id="editComment-savi" name="comment" class="textarea-savi" required></textarea>
                </div>

                <div class="form-group-savi full-width-savi">
                    <button type="submit" class="btn-savi">
                        <i class="fas fa-save"></i> Update Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include("partial/footer.php"); ?>

    <script>
        // Global variables
        let currentEditId = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadReviews();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Add review form submission
            document.getElementById('reviewForm-savi').addEventListener('submit', handleAddReview);
            
            // Edit review form submission
            document.getElementById('editForm-savi').addEventListener('submit', handleEditReview);
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('editModal-savi');
                if (event.target === modal) {
                    closeEditModal();
                }
            });
        }

        // Load all reviews
        function loadReviews() {
            fetch('review_api.php?action=get_all')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayReviews(data.reviews);
                    } else {
                        showAlert('Error loading reviews', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading reviews', 'error');
                });
        }

        // Display reviews in the container
        function displayReviews(reviews) {
            const container = document.getElementById('reviewContainer-savi');
            
            if (reviews.length === 0) {
                container.innerHTML = `
                    <div class="review-card-savi" style="text-align: center; grid-column: 1/-1;">
                        <p style="color: #666; font-size: 1.1rem;">
                            <i class="fas fa-comments" style="margin-right: 10px;"></i>
                            No reviews yet. Be the first to share your experience!
                        </p>
                    </div>
                `;
                return;
            }

            container.innerHTML = reviews.map(review => {
                const nameParts = review.name.split(' ');
                const initials = nameParts[0].charAt(0).toUpperCase() + 
                    (nameParts[1] ? nameParts[1].charAt(0).toUpperCase() : '');
                
                const stars = Array.from({length: 5}, (_, i) => 
                    i < review.rating ? 
                    '<i class="fas fa-star"></i>' : 
                    '<i class="far fa-star"></i>'
                ).join('');

                const reviewDate = new Date(review.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                return `
                    <div class="review-card-savi">
                        <div class="reviewer-info-savi">
                            <div class="reviewer-avatar-savi">${initials}</div>
                            <div>
                                <div class="reviewer-name-savi">${escapeHtml(review.name)}</div>
                                <div class="rating-savi">${stars}</div>
                            </div>
                        </div>
                        <div class="review-comment-savi">
                            "${escapeHtml(review.comment)}"
                        </div>
                        <div class="review-date-savi">${reviewDate}</div>
                        <div class="review-actions-savi" style="margin-top: 15px;">
                            <button onclick="editReview(${review.id})" class="edit-btn-savi">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="deleteReview(${review.id})" class="delete-btn-savi">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Handle add review form submission
        function handleAddReview(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const submitBtn = document.getElementById('submitBtn-savi');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            
            fetch('review_api.php?action=add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Review submitted successfully!', 'success');
                    document.getElementById('reviewForm-savi').reset();
                    loadReviews(); // Reload reviews
                } else {
                    showAlert(data.message || 'Error submitting review', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error submitting review', 'error');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
            });
        }

        // Edit review function
        function editReview(id) {
            fetch(`review_api.php?action=get_single&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const review = data.review;
                        
                        // Populate edit form
                        document.getElementById('editId-savi').value = review.id;
                        document.getElementById('editName-savi').value = review.name;
                        document.getElementById('editEmail-savi').value = review.email;
                        document.getElementById('editRating-savi').value = review.rating;
                        document.getElementById('editComment-savi').value = review.comment;
                        
                        // Show modal
                        document.getElementById('editModal-savi').style.display = 'block';
                        currentEditId = id;
                    } else {
                        showAlert('Error loading review data', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading review data', 'error');
                });
        }

        // Handle edit review form submission
        function handleEditReview(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            fetch('review_api.php?action=update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Review updated successfully!', 'success');
                    closeEditModal();
                    loadReviews(); // Reload reviews
                } else {
                    showAlert(data.message || 'Error updating review', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error updating review', 'error');
            });
        }

        // Delete review function
        function deleteReview(id) {
            if (!confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('review_api.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Review deleted successfully!', 'success');
                    loadReviews(); // Reload reviews
                } else {
                    showAlert(data.message || 'Error deleting review', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error deleting review', 'error');
            });
        }

        // Close edit modal
        function closeEditModal() {
            document.getElementById('editModal-savi').style.display = 'none';
            document.getElementById('editForm-savi').reset();
            currentEditId = null;
        }

        // Show alert message
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer-savi');
            const alertClass = type === 'success' ? 'alert-success-savi' : 'alert-error-savi';
            const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            
            const alertHtml = `
                <div class="alert-savi ${alertClass}" style="position: fixed; top: 20px; right: 20px; z-index: 1001; min-width: 300px;">
                    <i class="${icon}" style="margin-right: 10px;"></i>
                    ${message}
                </div>
            `;
            
            alertContainer.innerHTML = alertHtml;
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Hero stats animation
        function animateStats() {
            const stats = document.querySelectorAll('.stat-number-savi');
            stats.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target'));
                const increment = target / 100;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current);
                    }
                }, 20);
            });
        }

        // Start stats animation when page loads
        setTimeout(animateStats, 1000);

        // Smooth scroll for hero indicator
        document.querySelector('.hero-scroll-indicator-savi').addEventListener('click', function() {
            document.querySelector('.container-savi').scrollIntoView({
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>