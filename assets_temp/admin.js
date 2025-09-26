// Admin Panel JavaScript Functions

// Toggle add movie form
function toggleAddForm() {
    const form = document.getElementById('addMovieForm');
    const button = document.querySelector('.toggle-form');
    
    if (form.classList.contains('form-hidden')) {
        form.classList.remove('form-hidden');
        button.textContent = '× Cancel';
        button.style.background = '#dc2626';
        
        // Hide edit form if open
        const editForm = document.getElementById('editMovieForm');
        if (editForm && !editForm.classList.contains('form-hidden')) {
            editForm.classList.add('form-hidden');
        }
    } else {
        form.classList.add('form-hidden');
        button.textContent = '+ Add New Movie';
        button.style.background = '#059669';
    }
}

// Edit movie function (requires moviesData to be passed from PHP)
function editMovie(movieId) {
    // Check if moviesData exists (should be set in PHP)
    if (typeof moviesData === 'undefined') {
        alert('Movie data not available. Please refresh the page.');
        return;
    }
    
    // Find the movie data
    const movie = moviesData.find(m => m.id == movieId);
    if (!movie) {
        alert('Movie not found.');
        return;
    }
    
    // Populate edit form
    document.getElementById('edit_movie_id').value = movie.id;
    document.getElementById('edit_title').value = movie.title || '';
    document.getElementById('edit_overview').value = movie.overview || '';
    document.getElementById('edit_release_date').value = movie.release_date || '';
    document.getElementById('edit_runtime').value = movie.runtime || '';
    document.getElementById('edit_vote_average').value = movie.vote_average || '';
    document.getElementById('edit_popularity').value = movie.popularity || '';
    document.getElementById('edit_tagline').value = movie.tagline || '';
    document.getElementById('edit_poster_path').value = movie.poster_path || '';
    document.getElementById('edit_backdrop_path').value = movie.backdrop_path || '';
    document.getElementById('edit_trailer_url').value = movie.trailer_url || '';
    
    // Show edit form and hide add form
    const addForm = document.getElementById('addMovieForm');
    const editForm = document.getElementById('editMovieForm');
    const button = document.querySelector('.toggle-form');
    
    addForm.classList.add('form-hidden');
    editForm.classList.remove('form-hidden');
    button.textContent = '+ Add New Movie';
    button.style.background = '#059669';
    
    // Scroll to edit form
    editForm.scrollIntoView({ behavior: 'smooth' });
}

// Cancel edit function
function cancelEdit() {
    const editForm = document.getElementById('editMovieForm');
    if (editForm) {
        editForm.classList.add('form-hidden');
    }
}

// Utility function to confirm deletion
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item? This action cannot be undone.');
}

// Initialize admin panel functions when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success/error messages after 5 seconds
    const messages = document.querySelectorAll('.success-message, .error-message');
    messages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 300);
        }, 5000);
    });
    
    // Add smooth transitions to buttons
    const buttons = document.querySelectorAll('.btn-small, .action-btn, .create-btn, .add-btn');
    buttons.forEach(function(button) {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Handle form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc2626';
                    isValid = false;
                } else {
                    field.style.borderColor = '#e1e5e9';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
    
    // Handle responsive navigation on mobile
    const navbar = document.querySelector('.admin-navbar');
    if (navbar && window.innerWidth <= 768) {
        const navLinks = navbar.querySelector('.nav-links');
        if (navLinks) {
            navLinks.style.flexDirection = 'column';
            navLinks.style.gap = '10px';
        }
    }
});

// Handle window resize for responsive design
window.addEventListener('resize', function() {
    const navbar = document.querySelector('.admin-navbar');
    if (navbar) {
        const navLinks = navbar.querySelector('.nav-links');
        if (navLinks) {
            if (window.innerWidth <= 768) {
                navLinks.style.flexDirection = 'column';
                navLinks.style.gap = '10px';
            } else {
                navLinks.style.flexDirection = 'row';
                navLinks.style.gap = '20px';
            }
        }
    }
});

// Show loading state for forms
function showLoading(formElement) {
    const submitBtn = formElement.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';
    }
}

// Hide loading state for forms
function hideLoading(formElement, originalText) {
    const submitBtn = formElement.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText || 'Submit';
    }
}

// Handle form submissions with loading states
document.addEventListener('submit', function(e) {
    if (e.target.tagName === 'FORM') {
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
            const originalText = submitBtn.textContent;
            showLoading(e.target);
            
            // Reset after 10 seconds if no response (failsafe)
            setTimeout(function() {
                hideLoading(e.target, originalText);
            }, 10000);
        }
    }
});
