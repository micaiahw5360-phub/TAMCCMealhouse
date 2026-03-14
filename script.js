/**
 * TAMCC Deli – Main JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle (same as before)
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (menuToggle) {
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.addEventListener('click', () => {
            const expanded = navLinks.classList.contains('show') ? 'false' : 'true';
            navLinks.classList.toggle('show');
            menuToggle.setAttribute('aria-expanded', expanded);
            if (expanded === 'true') {
                const firstLink = navLinks.querySelector('a');
                if (firstLink) firstLink.focus();
            } else {
                menuToggle.focus();
            }
        });
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            if (navLinks && navLinks.classList.contains('show')) {
                navLinks.classList.remove('show');
                if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Smooth scroll for category links
    const categoryLinks = document.querySelectorAll('.dropdown-content a');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (window.location.pathname.includes('menu.php')) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });

    if (window.location.pathname.includes('menu.php') && window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const target = document.getElementById(targetId);
        if (target) {
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
    }

    // Live menu search
    const searchInput = document.getElementById('menu-search');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const term = this.value.toLowerCase().trim();
                document.querySelectorAll('.menu-item').forEach(item => {
                    const name = item.querySelector('.menu-item-name')?.textContent.toLowerCase() || '';
                    item.style.display = name.includes(term) ? '' : 'none';
                });
            }, 300);
        });
    }

    // Category filter buttons
    const filterButtons = document.querySelectorAll('.filter-btn');
    const categories = document.querySelectorAll('.category');
    if (filterButtons.length && categories.length) {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const selectedCat = this.dataset.category;
                if (selectedCat === 'all') {
                    categories.forEach(cat => cat.style.display = 'block');
                } else {
                    categories.forEach(cat => {
                        cat.style.display = cat.id === selectedCat ? 'block' : 'none';
                    });
                }
            });
        });
    }

    // Auto‑dismiss alerts
    document.querySelectorAll('.error, .success').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // AJAX Add to Cart
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('/tamccdeli/cart.php?action=add', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const result = await response.json();
                if (result.success) {
                    showToast('Item added to cart!', 'success');
                    updateCartCount();
                } else {
                    showToast('Error adding item', 'error');
                }
            } catch (error) {
                console.error('Add to cart error:', error);
                showToast('Failed to add item. Please try again.', 'error');
                // Fallback: submit the form normally
                this.submit();
            }
        });
    });

    // Update cart count
    async function updateCartCount() {
        try {
            const response = await fetch('/tamccdeli/get-cart-count.php');
            const data = await response.json();
            const countSpan = document.getElementById('cart-count');
            if (countSpan) {
                countSpan.textContent = data.count;
                countSpan.style.transform = 'scale(1.2)';
                setTimeout(() => countSpan.style.transform = 'scale(1)', 200);
            }
        } catch (error) {
            console.error('Failed to update cart count', error);
        }
    }
    updateCartCount();

    // Scroll to top button
    const scrollButton = document.getElementById('scroll-to-top');
    if (scrollButton) {
        scrollButton.setAttribute('aria-label', 'Scroll to top');
        scrollButton.setAttribute('title', 'Scroll to top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollButton.classList.add('show');
            } else {
                scrollButton.classList.remove('show');
            }
        });
        scrollButton.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Dropdown keyboard navigation
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('a');
        const content = dropdown.querySelector('.dropdown-content');
        if (!link || !content) return;
        link.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                content.style.display = 'block';
                const firstItem = content.querySelector('a');
                if (firstItem) firstItem.focus();
            }
        });
        content.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                content.style.display = '';
                link.focus();
            }
        });
        const focusableItems = content.querySelectorAll('a');
        if (focusableItems.length > 0) {
            const first = focusableItems[0];
            const last = focusableItems[focusableItems.length - 1];
            content.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === first) {
                            e.preventDefault();
                            last.focus();
                        }
                    } else {
                        if (document.activeElement === last) {
                            e.preventDefault();
                            first.focus();
                        }
                    }
                }
            });
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dropdown-content[style*="display: block"]').forEach(content => {
                content.style.display = '';
            });
        }
    });

    // Service worker (optional)
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/tamccdeli/sw.js')
            .then(reg => console.log('SW registered', reg))
            .catch(err => console.log('SW failed', err));
    }
});

// Toast function
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position:fixed; bottom:20px; right:20px; z-index:9999;';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        background: ${type === 'success' ? '#4caf50' : type === 'error' ? '#f44336' : '#2196f3'};
        color: white; padding: 12px 20px; margin-top: 10px; border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2); opacity: 0; transition: opacity 0.3s;
    `;
    container.appendChild(toast);
    setTimeout(() => toast.style.opacity = 1, 10);
    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}