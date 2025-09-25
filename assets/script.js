// Show alert when logo clicked
document.querySelector('.logo').addEventListener('click', () => {
  window.location.href = "index.html";
});

// Smooth scroll to top when clicking HOME
document.querySelector('.home-btn').addEventListener('click', (e) => {
  e.preventDefault();
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

// "JOIN NOW" button redirect
document.querySelector('.shop-btn').addEventListener('click', (e) => {
  e.preventDefault();
  window.location.href = "https://www.netflix.com"; // replace with your join page later
});

// Logo hover effect (cinematic glow)
const logo = document.querySelector('.logo');
logo.addEventListener('mouseenter', () => {
  logo.style.textShadow = "0px 0px 20px red";
});
logo.addEventListener('mouseleave', () => {
  logo.style.textShadow = "none";
});

// Movie Slider
const sliderContainer = document.querySelector('.slider-container');
const slides = document.querySelectorAll('.slide');
const prevButton = document.querySelector('.slider-button.prev');
const nextButton = document.querySelector('.slider-button.next');
let slideIndex = 0;
const slideWidth = 300; // Fixed slide width

function updateSlider() {
  sliderContainer.style.transform = `translateX(-${slideIndex * slideWidth}px)`;
}

prevButton.addEventListener('click', () => {
  slideIndex = (slideIndex - 1 + slides.length) % slides.length;
  updateSlider();
});

nextButton.addEventListener('click', () => {
  slideIndex = (slideIndex + 1) % slides.length;
  updateSlider();
});

// Automatic slider
setInterval(() => {
  slideIndex = (slideIndex + 1) % slides.length;
  updateSlider();
}, 3000);
