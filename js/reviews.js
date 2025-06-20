/**
 * Swans Cinema Reviews Page - Professional JavaScript
 * File: js/reviews-savi.js
 * Author: Senior Full-Stack Developer
 * Description: Enhanced functionality for the reviews page with animations, interactions, and professional features
 */

const ReviewsPage = {
    // Configuration
    config: {
        animationDelay: 200,
        counterSpeed: 2000,
        parallaxSpeed: 0.5,
        reviewsPerPage: 6,
        autoScrollSpeed: 3000
    },

    // Initialize all functionality
    init() {
        this.setupEventListeners();
        this.initializeAnimations();
        this.setupFormValidation();
        this.initializeCounters();
        this.setupScrollEffects();
        this.setupReviewInteractions();
        this.setupParallaxEffects();
        this.initializeLoadingEffects();
    },

    // Setup event listeners
    setupEventListeners() {
        // Smooth scroll for hero scroll indicator
        const scrollIndicator = document.querySelector('.hero-scroll-indicator-savi');
        if (scrollIndicator) {
            scrollIndicator.addEventListener('click', () => {
                const reviewsSection = document.querySelector('.review-container-savi');
                if (reviewsSection) {
                    reviewsSection.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        }

        // Window resize handler
        window.addEventListener('resize', () => {
            this.handleResize();
        });

        // Window scroll handler
        window.addEventListener('scroll', () => {
            this.handleScroll();
        });
    },

    // Initialize entrance animations
    initializeAnimations() {
        // Animate review cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('animate-in-savi');
                    }, index * this.config.animationDelay);
                }
            });
        }, observerOptions);

        // Observe review cards
        const reviewCards = document.querySelectorAll('.review-card-savi');
        reviewCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease-out';
            observer.observe(card);
        });

        // Add animation class styles
        const style = document.createElement('style');
        style.textContent = `
            .animate-in-savi {
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
        `;
        document.head.appendChild(style);
    },

    // Setup form validation and enhancement
    setupFormValidation() {
        const form = document.getElementById('reviewForm-savi');
        const successMessage = document.getElementById('successMessage-savi');
        
        if (!form) return;

        // Enhanced form submission
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (this.validateForm()) {
                this.submitForm(form, successMessage);
            }
        });

        // Real-time validation
        const inputs = form.querySelectorAll('.input-savi, .textarea-savi, .select-savi');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('error-savi')) {
                    this.validateField(input);
                }
            });

            // Add focus animations
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused-savi');
            });

            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('focused-savi');
            });
        });

        // Add focus styles
        const focusStyles = document.createElement('style');
        focusStyles.textContent = `
            .focused-savi .label-savi {
                color: #9370db !important;
                transform: translateY(-2px);
                transition: all 0.3s ease;
            }
            .error-savi {
                border-color: #e74c3c !important;
                box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1) !important;
            }
        `;
        document.head.appendChild(focusStyles);
    },

    // Form validation logic
    validateForm() {
        let isValid = true;
        const form = document.getElementById('reviewForm-savi');
        
        // Clear previous errors
        this.clearErrors();
        
        // Validate name
        const name = document.getElementById('name-savi');
        if (!name.value.trim()) {
            this.showError('nameError-savi', 'Please enter your full name');
            isValid = false;
        } else if (name.value.trim().length < 2) {
            this.showError('nameError-savi', 'Name must be at least 2 characters long');
            isValid = false;
        }
        
        // Validate email
        const email = document.getElementById('email-savi');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            this.showError('emailError-savi', 'Please enter your email address');
            isValid = false;
        } else if (!emailRegex.test(email.value)) {
            this.showError('emailError-savi', 'Please enter a valid email address');
            isValid = false;
        }
        
        // Validate rating
        const rating = document.getElementById('rating-savi');
        if (!rating.value) {
            this.showError('ratingError-savi', 'Please select a rating');
            isValid = false;
        }
        
        // Validate comment
        const comment = document.getElementById('comment-savi');
        if (!comment.value.trim()) {
            this.showError('commentError-savi', 'Please share your review');
            isValid = false;
        } else if (comment.value.trim().length < 10) {
            this.showError('commentError-savi', 'Review must be at least 10 characters long');
            isValid = false;
        }
        
        return isValid;
    },

    // Validate individual field
    validateField(field) {
        field.classList.remove('error-savi');
        
        if (field.hasAttribute('required') && !field.value.trim()) {
            field.classList.add('error-savi');
            return false;
        }
        
        if (field.type === 'email' && field.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                field.classList.add('error-savi');
                return false;
            }
        }
        
        return true;
    },

    // Submit form with animation
    submitForm(form, successMessage) {
        // Add loading state
        const submitBtn = form.querySelector('.btn-savi');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            form.style.transform = 'scale(0.95)';
            form.style.opacity = '0.5';
            
            setTimeout(() => {
                form.style.display = 'none';
                successMessage.style.display = 'block';
                successMessage.style.animation = 'fadeInScale-savi 0.5s ease-out';
                
                // Reset form after delay
                setTimeout(() => {
                    this.resetForm(form, successMessage, submitBtn, originalText);
                }, 3000);
            }, 500);
        }, 1000);
    },

    // Reset form to original state
    resetForm(form, successMessage, submitBtn, originalText) {
        form.reset();
        form.style.display = 'grid';
        form.style.transform = 'scale(1)';
        form.style.opacity = '1';
        form.style.transition = 'all 0.5s ease';
        
        successMessage.style.display = 'none';
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        this.clearErrors();
    },

    // Initialize counter animations
    initializeCounters() {
        const counters = document.querySelectorAll('.stat-number-savi');
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    },

    // Animate counter numbers
    animateCounter(element) {
        const target = parseFloat(element.getAttribute('data-target'));
        const duration = this.config.counterSpeed;
        const step = target / (duration / 16); // 60fps
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            // Format number based on target
            if (target >= 1000) {
                element.textContent = Math.floor(current).toLocaleString() + '+';
            } else {
                element.textContent = current.toFixed(1);
            }
        }, 16);
    },

    // Setup scroll effects
    setupScrollEffects() {
        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.updateScrollEffects();
                    ticking = false;
                });
                ticking = true;
            }
        });
    },

    // Update scroll-based effects
    updateScrollEffects() {
        const scrolled = window.pageYOffset;
        const heroSection = document.querySelector('.hero-section-savi');
        
        if (heroSection) {
            // Parallax effect for hero background
            const parallaxElements = heroSection.querySelectorAll('.hero-particles-savi');
            parallaxElements.forEach(element => {
                const speed = this.config.parallaxSpeed;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        }

        // Show/hide scroll indicator
        const scrollIndicator = document.querySelector('.hero-scroll-indicator-savi');
        if (scrollIndicator) {
            scrollIndicator.style.opacity = scrolled > 100 ? '0' : '1';
        }
    },

    // Setup review card interactions
    setupReviewInteractions() {
        const reviewCards = document.querySelectorAll('.review-card-savi');
        
        reviewCards.forEach(card => {
            // Add hover sound effect (optional)
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-8px) scale(1.02)';
                card.style.boxShadow = '0 12px 30px rgba(147, 112, 219, 0.25)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
                card.style.boxShadow = '0 4px 15px rgba(147, 112, 219, 0.1)';
            });

            // Add click ripple effect
            card.addEventListener('click', (e) => {
                this.createRipple(e, card);
            });
        });
    },

    // Create ripple effect
    createRipple(event, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(147, 112, 219, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-savi 0.6s linear;
            pointer-events: none;
            z-index: 1;
        `;
        
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    },

    // Setup parallax effects
    setupParallaxEffects() {
        const parallaxElements = document.querySelectorAll('[data-parallax-savi]');
        
        if (parallaxElements.length > 0) {
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                
                parallaxElements.forEach(element => {
                    const speed = element.getAttribute('data-parallax-savi') || 0.5;
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translateY(${yPos}px)`;
                });
            });
        }
    },

    // Initialize loading effects
    initializeLoadingEffects() {
        // Add loading skeleton effect
        this.addLoadingSkeletons();
        
        // Simulate content loading
        setTimeout(() => {
            this.removeLoadingSkeletons();
            this.revealContent();
        }, 1500);

        // Add ripple animation styles
        const rippleStyles = document.createElement('style');
        rippleStyles.textContent = `
            @keyframes ripple-savi {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            @keyframes fadeInScale-savi {
                from {
                    opacity: 0;
                    transform: scale(0.8);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            
            @keyframes shimmer-savi {
                0% {
                    background-position: -468px 0;
                }
                100% {
                    background-position: 468px 0;
                }
            }
            
            .loading-skeleton-savi {
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 400% 100%;
                animation: shimmer-savi 1.2s ease-in-out infinite;
            }
            
            .fade-in-up-savi {
                opacity: 0;
                transform: translateY(30px);
                animation: fadeInUp-savi 0.8s ease-out forwards;
            }
        `;
        document.head.appendChild(rippleStyles);
    },

 addLoadingSkeletons() {
    const reviewCards = document.querySelectorAll('.review-card-savi');

    reviewCards.forEach(card => {
        if (card.getAttribute('data-loaded') === 'true') return;

        // Don't overwrite content — just add visual loading effect
        card.classList.add('loading-skeleton-savi');
    });
},

removeLoadingSkeletons() {
    const reviewCards = document.querySelectorAll('.review-card-savi');

    reviewCards.forEach(card => {
        card.classList.remove('loading-skeleton-savi');
        card.setAttribute('data-loaded', 'true'); // mark as safe from future loading
    });
},


    // Reveal content with animation
  revealContent() {
    const reviewCards = document.querySelectorAll('.review-card-savi');

    reviewCards.forEach((card, index) => {
        if (!card.classList.contains('fade-in-up-savi')) {
            setTimeout(() => {
                card.classList.add('fade-in-up-savi');
            }, index * 100);
        }
    });
},

    // Handle window resize
    handleResize() {
        // Recalculate animations and layouts
        const heroSection = document.querySelector('.hero-section-savi');
        if (heroSection && window.innerWidth < 768) {
            heroSection.style.minHeight = '600px';
        } else if (heroSection) {
            heroSection.style.minHeight = '700px';
        }
    },

    // Handle scroll events
    handleScroll() {
        // Throttled scroll handler
        if (!this.scrollTicking) {
            requestAnimationFrame(() => {
                this.updateScrollEffects();
                this.updateProgressBar();
                this.scrollTicking = false;
            });
            this.scrollTicking = true;
        }
    },

    // Update reading progress bar
    updateProgressBar() {
        const scrolled = window.pageYOffset;
        const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
        const progress = (scrolled / maxScroll) * 100;
        
        let progressBar = document.querySelector('.progress-bar-savi');
        if (!progressBar) {
            progressBar = document.createElement('div');
            progressBar.className = 'progress-bar-savi';
            progressBar.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 0%;
                height: 3px;
                background: linear-gradient(90deg, #9370db, #dda0dd);
                z-index: 9999;
                transition: width 0.1s ease;
            `;
            document.body.appendChild(progressBar);
        }
        
        progressBar.style.width = `${progress}%`;
    },

    // Utility functions
    showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            errorElement.style.animation = 'fadeInScale-savi 0.3s ease-out';
        }
    },

    clearErrors() {
        const errorMessages = document.querySelectorAll('.error-message-savi');
        errorMessages.forEach(msg => {
            msg.style.display = 'none';
            msg.textContent = '';
        });
        
        const inputs = document.querySelectorAll('.input-savi, .textarea-savi, .select-savi');
        inputs.forEach(input => {
            input.classList.remove('error-savi');
        });
    },

    // Advanced features
    initializeAdvancedFeatures() {
        this.setupKeyboardNavigation();
        this.setupAccessibility();
        this.setupPerformanceOptimizations();
    },

    // Setup keyboard navigation
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // ESC key to close any open modals/messages
            if (e.key === 'Escape') {
                const successMessage = document.getElementById('successMessage-savi');
                if (successMessage && successMessage.style.display === 'block') {
                    successMessage.style.display = 'none';
                    const form = document.getElementById('reviewForm-savi');
                    if (form) form.style.display = 'grid';
                }
            }
        });
    },

    // Setup accessibility improvements
    setupAccessibility() {
        // Add ARIA labels and roles
        const reviewCards = document.querySelectorAll('.review-card-savi');
        reviewCards.forEach((card, index) => {
            card.setAttribute('role', 'article');
            card.setAttribute('aria-label', `Customer review ${index + 1}`);
            card.setAttribute('tabindex', '0');
        });

        // Add focus indicators
        const focusableElements = document.querySelectorAll('button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
        focusableElements.forEach(element => {
            element.addEventListener('focus', () => {
                element.style.outline = '2px solid #9370db';
                element.style.outlineOffset = '2px';
            });
            
            element.addEventListener('blur', () => {
                element.style.outline = 'none';
            });
        });
    },

    // Setup performance optimizations
    setupPerformanceOptimizations() {
        // Lazy load images if any
        const images = document.querySelectorAll('img[data-src]');
        if (images.length > 0) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.getAttribute('data-src');
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }

        // Debounce resize events
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.handleResize();
            }, 250);
        });
    },

    // Easter egg features
    setupEasterEggs() {
        let clickCount = 0;
        const logo = document.querySelector('.hero-badge-savi');
        
        if (logo) {
            logo.addEventListener('click', () => {
                clickCount++;
                if (clickCount === 5) {
                    this.showEasterEgg();
                    clickCount = 0;
                }
            });
        }
    },

    showEasterEgg() {
        const confetti = document.createElement('div');
        confetti.innerHTML = '🎉🍿🎬✨';
        confetti.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            z-index: 10000;
            animation: confetti-savi 2s ease-out forwards;
            pointer-events: none;
        `;
        
        const confettiStyles = document.createElement('style');
        confettiStyles.textContent = `
            @keyframes confetti-savi {
                0% {
                    opacity: 0;
                    transform: translate(-50%, -50%) scale(0) rotate(0deg);
                }
                50% {
                    opacity: 1;
                    transform: translate(-50%, -50%) scale(1.2) rotate(180deg);
                }
                100% {
                    opacity: 0;
                    transform: translate(-50%, -50%) scale(0) rotate(360deg);
                }
            }
        `;
        
        document.head.appendChild(confettiStyles);
        document.body.appendChild(confetti);
        
        setTimeout(() => {
            confetti.remove();
            confettiStyles.remove();
        }, 2000);
    }
};

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        ReviewsPage.init();
        ReviewsPage.initializeAdvancedFeatures();
        ReviewsPage.setupEasterEggs();
    });
} else {
    ReviewsPage.init();
    ReviewsPage.initializeAdvancedFeatures();
    ReviewsPage.setupEasterEggs();
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ReviewsPage;
}