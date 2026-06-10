document.addEventListener('DOMContentLoaded', () => {
    const dropdown = document.querySelector('[data-user-dropdown]');
    const dropdownTrigger = document.querySelector('[data-user-dropdown-trigger]');

    function setExpandedState(control, expanded) {
        if (!control) {
            return;
        }

        control.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    }

    if (dropdownTrigger && dropdown) {
        setExpandedState(dropdownTrigger, false);
        dropdown.setAttribute('aria-hidden', dropdown.classList.contains('hidden') ? 'true' : 'false');

        dropdownTrigger.addEventListener('click', () => {
            const willHide = !dropdown.classList.contains('hidden');

            dropdown.classList.toggle('hidden');
            dropdown.setAttribute('aria-hidden', willHide ? 'true' : 'false');
            setExpandedState(dropdownTrigger, !willHide);
        });
    }

    document.addEventListener('click', (event) => {
        if (!dropdown || dropdown.classList.contains('hidden')) {
            return;
        }

        if (event.target.closest('[data-user-dropdown-trigger]')) {
            return;
        }

        if (!dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
            dropdown.setAttribute('aria-hidden', 'true');
            setExpandedState(dropdownTrigger, false);
        }
    });

    document.querySelectorAll('[data-panel-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const panelId = button.dataset.panelToggle;
            const panel = panelId ? document.getElementById(panelId) : null;

            if (panel) {
                const willHide = !panel.classList.contains('hidden');

                panel.classList.toggle('hidden');
                panel.setAttribute('aria-hidden', willHide ? 'true' : 'false');
                setExpandedState(button, !willHide);
            }
        });
    });

    document.querySelectorAll('[data-panel-hide]').forEach((button) => {
        button.addEventListener('click', () => {
            const panelId = button.dataset.panelHide;
            const panel = panelId ? document.getElementById(panelId) : null;

            if (panel) {
                panel.classList.add('hidden');
                panel.setAttribute('aria-hidden', 'true');
                const relatedToggle = document.querySelector('[data-panel-toggle="' + panelId + '"]');

                setExpandedState(relatedToggle, false);
            }
        });
    });

    document.querySelectorAll('[data-logout-button]').forEach((button) => {
        button.addEventListener('click', async () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch('/logout', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                window.alert('No se pudo cerrar sesión');
                return;
            }

            window.location.assign('/login');
        });
    });
});