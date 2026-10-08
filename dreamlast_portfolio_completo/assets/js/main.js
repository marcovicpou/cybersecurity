(() => {
    const header = document.querySelector('.site-header');
    const button = document.querySelector('.menu-toggle');
    const navigation = document.getElementById('navigation');
    const mobile = window.matchMedia('(max-width: 960px)');
    const setMenu = (open, restoreFocus = false) => {
        button?.setAttribute('aria-expanded', String(open));
        button?.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
        header?.classList.toggle('menu-open', open);
        if (restoreFocus) button?.focus();
    };
    button?.addEventListener('click', () => setMenu(button.getAttribute('aria-expanded') !== 'true'));
    navigation?.addEventListener('click', (event) => {
        if (event.target.closest('a')) setMenu(false);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && button?.getAttribute('aria-expanded') === 'true') setMenu(false, true);
    });
    document.addEventListener('click', (event) => {
        if (header && !header.contains(event.target)) setMenu(false);
    });
    header?.addEventListener('focusout', () => {
        setTimeout(() => {
            if (!header.contains(document.activeElement)) setMenu(false);
        }, 0);
    });
    mobile.addEventListener('change', () => setMenu(false));
    const updateHeader = () => header?.classList.toggle('is-scrolled', window.scrollY > 16);
    window.addEventListener('scroll', updateHeader, { passive: true });
    updateHeader();
    if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });
        document.querySelectorAll('.reveal').forEach((element) => {
            element.classList.add('reveal-ready');
            observer.observe(element);
        });
    }
})();
