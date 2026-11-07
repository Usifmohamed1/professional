(() => {
    const initializeFloatingContact = () => {
        const widget = document.querySelector('[data-floating-contact]');

        if (!widget) {
            return;
        }

        const toggle = widget.querySelector('.floating-contact__toggle');
        const actions = widget.querySelectorAll('.floating-contact__action');

        if (!toggle || !actions.length) {
            return;
        }

        const setExpanded = (expanded) => {
            widget.classList.toggle('is-open', expanded);
            document.body.classList.toggle('has-floating-contact', true);
            toggle.setAttribute('aria-expanded', String(expanded));
            toggle.setAttribute(
                'aria-label',
                expanded ? toggle.dataset.closeLabel : toggle.dataset.openLabel
            );

            actions.forEach((action) => {
                action.setAttribute('aria-hidden', String(!expanded));
                action.tabIndex = expanded ? 0 : -1;
            });
        };

        toggle.addEventListener('click', () => {
            setExpanded(toggle.getAttribute('aria-expanded') !== 'true');
        });

        document.addEventListener('click', (event) => {
            if (!widget.contains(event.target)) {
                setExpanded(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
                setExpanded(false);
                toggle.focus();
            }
        });

        document.body.classList.add('has-floating-contact');
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeFloatingContact);
    } else {
        initializeFloatingContact();
    }
})();
