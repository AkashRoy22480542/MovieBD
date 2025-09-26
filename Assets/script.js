// Search functionality with debouncing
let searchTimeout;
let currentSearchRequest;

function liveSearch() {
    const query = document.getElementById('search-box').value.trim();
    const suggestionsDiv = document.getElementById('live-suggestions');
    
    // Cancel any existing timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Cancel any ongoing request
    if (currentSearchRequest) {
        currentSearchRequest.abort();
        currentSearchRequest = null;
    }
    
    if (query.length === 0) {
        suggestionsDiv.innerHTML = '';
        return;
    }
    
    if (query.length < 2) {
        // Don't search for single characters
        suggestionsDiv.innerHTML = '<div class="no-match">Type at least 2 characters</div>';
        return;
    }
    
    // Show loading indicator
    suggestionsDiv.innerHTML = '<div class="loading">Searching...</div>';
    
    // Debounce the search - wait 300ms after user stops typing
    searchTimeout = setTimeout(function() {
        performSearch(query, suggestionsDiv);
    }, 300);
}

function performSearch(query, suggestionsDiv) {
    // Make AJAX call to movie controller
    const xhr = new XMLHttpRequest();
    currentSearchRequest = xhr;
    
    xhr.open('POST', 'Controller/movieController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.timeout = 10000; // 10 second timeout
    
    xhr.onreadystatechange = function() {
        if (this.readyState === 4) {
            currentSearchRequest = null;
            
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);
                    
                    if (response.success && response.results && response.results.length > 0) {
                        suggestionsDiv.innerHTML = response.results
                            .map(movie => `<div class="suggestion-item" onclick="selectMovie('${escapeHtml(movie.title)}', ${movie.id})">${escapeHtml(movie.title)}</div>`)
                            .join('');
                    } else {
                        suggestionsDiv.innerHTML = '<div class="no-match">No matches found</div>';
                    }
                } catch (error) {
                    console.error('Error parsing search results:', error);
                    suggestionsDiv.innerHTML = '<div class="no-match">Search error occurred</div>';
                }
            } else if (this.status === 0) {
                // Request was aborted
                return;
            } else {
                console.error('Search request failed with status:', this.status);
                suggestionsDiv.innerHTML = '<div class="no-match">Search temporarily unavailable</div>';
            }
        }
    };
    
    xhr.onerror = function() {
        currentSearchRequest = null;
        console.error('Network error during search');
        suggestionsDiv.innerHTML = '<div class="no-match">Network error</div>';
    };
    
    xhr.ontimeout = function() {
        currentSearchRequest = null;
        console.error('Search request timed out');
        suggestionsDiv.innerHTML = '<div class="no-match">Search timed out</div>';
    };
    
    // Send as form data instead of JSON for better compatibility
    xhr.send('action=search&query=' + encodeURIComponent(query));
}

// Helper function to escape HTML
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

function selectMovie(movieName, movieId) {
    document.getElementById('search-box').value = movieName;
    document.getElementById('live-suggestions').innerHTML = '';
    
    // Navigate to movie details if ID is provided
    if (movieId) {
        viewMovie(movieId);
    } else {
        console.log('Selected movie:', movieName);
    }
}

// Movie display functions
function displayTrendingMovies(movies) {
    const movieSlider = document.getElementById('movie-slider');
    
    if (!movies || movies.length === 0) {
        movieSlider.innerHTML = '<p>No trending movies available</p>';
        return;
    }
    
    const movieCards = movies.map(movie => `
        <div class="movie-card" onclick="viewMovie(${movie.id})">
            <div class="movie-image-wrapper">
                <img src="${movie.poster || 'https://via.placeholder.com/200x300?text=No+Image'}" 
                     alt="${movie.title}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
            </div>
            <p>${movie.title}</p>
        </div>
    `).join('');
    
    movieSlider.innerHTML = movieCards;
}


function viewMovie(movieId) {
    // Navigate to movie details page
    window.location.href = `view/movie_details.php?id=${movieId}`;
}


// Hide suggestions when clicking outside
document.addEventListener('click', function(event) {
    const searchBox = document.getElementById('search-box');
    const suggestions = document.getElementById('live-suggestions');
    
    if (!searchBox.contains(event.target) && !suggestions.contains(event.target)) {
        suggestions.innerHTML = '';
    }
});

// Profile page functions - from View/profile.php
function goBack() {
    window.history.back();
}

function toggleEdit() {
    const nameInput = document.getElementById('name');
    const toggleBtn = document.getElementById('toggleBtn');
    const saveBtn = document.getElementById('saveBtn');
    
    if (nameInput.hasAttribute('readonly')) {
        // Enable editing
        nameInput.removeAttribute('readonly');
        nameInput.style.backgroundColor = 'rgba(255, 255, 255, 1)';
        toggleBtn.textContent = 'Cancel';
        saveBtn.style.display = 'inline-block';
    } else {
        // Disable editing
        nameInput.setAttribute('readonly', true);
        nameInput.style.backgroundColor = '#f9fafb';
        toggleBtn.textContent = 'Edit';
        saveBtn.style.display = 'none';
        
        // Reset values to original - get from data attribute if available
        if (nameInput.dataset.originalValue) {
            nameInput.value = nameInput.dataset.originalValue;
        }
    }
}

function previewProfilePic() {
    const file = document.getElementById('uploadPic').files[0];
    const preview = document.getElementById('profilePic');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

// Signup page functions - from View/signup.php
function initPasswordStrengthChecker() {
    const passwordField = document.getElementById('password');
    if (passwordField) {
        passwordField.addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthDiv = document.getElementById('password-strength');
            
            if (!strengthDiv) return;
            
            let strength = 0;
            let message = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^A-Za-z0-9]/)) strength++;
            
            if (password.length === 0) {
                strengthDiv.textContent = '';
                strengthDiv.className = 'password-strength';
            } else if (strength < 3) {
                message = 'Weak password';
                strengthDiv.className = 'password-strength weak';
            } else if (strength < 5) {
                message = 'Medium password';
                strengthDiv.className = 'password-strength medium';
            } else {
                message = 'Strong password';
                strengthDiv.className = 'password-strength strong';
            }
            
            strengthDiv.textContent = message;
        });
    }
}

// Login form validation - from View/login.php (function-based for onsubmit)
function validateLogin() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    if (!email) {
        alert('Please enter your email address');
        return false;
    }
    
    if (!isValidEmail(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    if (!password) {
        alert('Please enter your password');
        return false;
    }
    
    return true;
}

// Signup form validation - from View/signup.php (function-based for onsubmit)
function validateSignup() {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const terms = document.getElementById('terms').checked;
    
    if (!firstName) {
        alert('Please enter your first name');
        return false;
    }
    
    if (!lastName) {
        alert('Please enter your last name');
        return false;
    }
    
    if (!username) {
        alert('Please enter a username');
        return false;
    }
    
    if (username.length < 3) {
        alert('Username must be at least 3 characters long');
        return false;
    }
    
    if (!email) {
        alert('Please enter your email address');
        return false;
    }
    
    if (!isValidEmail(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    if (!password) {
        alert('Please enter a password');
        return false;
    }
    
    if (password.length < 8) {
        alert('Password must be at least 8 characters long');
        return false;
    }
    
    if (!confirmPassword) {
        alert('Please confirm your password');
        return false;
    }
    
    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return false;
    }
    
    if (!terms) {
        alert('Please agree to the terms and conditions');
        return false;
    }
    
    return true;
}

// Helper function for email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Initialize functions when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initPasswordStrengthChecker();
});
