// Get the theme toggler element
const themeToggler = document.querySelector('.theme-toggler');

// Get the icons
const lightIcon = document.getElementById('light-icon');
const darkIcon = document.getElementById('dark-icon');

// Function to apply the correct theme based on localStorage
function applyTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.add('dark-theme');
        lightIcon.style.display = 'none';
        darkIcon.style.display = 'block';
    } else {
        document.body.classList.remove('dark-theme');
        lightIcon.style.display = 'block';
        darkIcon.style.display = 'none';
    }
}

// Check for saved theme in localStorage and apply it
let currentTheme = localStorage.getItem('theme') || 'light';
applyTheme(currentTheme);

// Add event listener to the theme toggler
themeToggler.addEventListener('click', () => {
    // Toggle between 'light' and 'dark' themes
    currentTheme = document.body.classList.contains('dark-theme') ? 'light' : 'dark';

    // Apply the toggled theme
    applyTheme(currentTheme);

    // Save theme preference to localStorage
    localStorage.setItem('theme', currentTheme);
});

