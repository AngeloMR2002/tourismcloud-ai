//

// Only collapse the visual filter panel on initial mobile display.
// Native details/summary keeps filtering usable without JavaScript.
if (window.matchMedia('(max-width: 760px)').matches) {
    document.querySelectorAll('[data-responsive-filters]').forEach((panel) => {
        panel.open = false;
    });
}

const navigationDrawer = document.getElementById('tc-navigation-drawer');
const navigationButton = document.querySelector('[data-open-navigation]');

if (navigationDrawer && navigationButton) {
    let previousOverflow = '';

    navigationButton.addEventListener('click', () => {
        previousOverflow = document.body.style.overflow;
        navigationDrawer.showModal();
        document.body.style.overflow = 'hidden';
        navigationButton.setAttribute('aria-expanded', 'true');
    });

    navigationDrawer.addEventListener('close', () => {
        document.body.style.overflow = previousOverflow;
        navigationButton.setAttribute('aria-expanded', 'false');
    });

    navigationDrawer.addEventListener('click', (event) => {
        if (event.target === navigationDrawer) {
            const bounds = navigationDrawer.getBoundingClientRect();
            if (event.clientX > bounds.right || event.clientX < bounds.left) {
                navigationDrawer.close();
            }
        }
    });
}
