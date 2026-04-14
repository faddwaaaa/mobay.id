// Dark Mode Handler
class DarkModeManager {
    constructor() {
        this.darkModeKey = 'payou_theme_preference';
        this.init();
    }

    init() {
        // Load saved theme or use system preference
        const savedTheme = localStorage.getItem(this.darkModeKey);
        
        if (savedTheme) {
            this.setTheme(savedTheme);
        } else {
            // Check system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.setTheme(prefersDark ? 'dark' : 'light');
        }

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem(this.darkModeKey)) {
                this.setTheme(e.matches ? 'dark' : 'light');
            }
        });

        this.setupEventListeners();
    }

    setTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark-mode');
        } else {
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark-mode');
        }
        
        localStorage.setItem(this.darkModeKey, theme);
        this.updateThemeIcons(theme);
    }

    toggleTheme() {
        const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }

    updateThemeIcons(theme) {
        // Update active state on theme buttons
        const lightBtns = document.querySelectorAll('[data-theme="light"]');
        const darkBtns = document.querySelectorAll('[data-theme="dark"]');
        
        lightBtns.forEach(btn => {
            if (theme === 'light') {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        darkBtns.forEach(btn => {
            if (theme === 'dark') {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    setupEventListeners() {
        // Theme toggle buttons
        document.addEventListener('click', (e) => {
            const themeBtn = e.target.closest('[data-theme]');
            if (themeBtn) {
                e.preventDefault();
                const theme = themeBtn.getAttribute('data-theme');
                this.setTheme(theme);
                
                // Close dropdown
                const dropdown = themeBtn.closest('.theme-dropdown-menu');
                if (dropdown) {
                    dropdown.classList.remove('show');
                }
            }
        });

        // Theme dropdown toggle
        const themeToggles = document.querySelectorAll('.theme-dropdown-toggle');
        themeToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const dropdown = toggle.nextElementSibling;
                
                // Close other dropdowns
                document.querySelectorAll('.theme-dropdown-menu').forEach(d => {
                    if (d !== dropdown) {
                        d.classList.remove('show');
                    }
                });
                
                dropdown.classList.toggle('show');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.theme-dropdown')) {
                document.querySelectorAll('.theme-dropdown-menu').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });
    }

    getCurrentTheme() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    }
}

// Initialize dark mode when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.darkModeManager = new DarkModeManager();
});
