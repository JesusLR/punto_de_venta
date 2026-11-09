(function () {
    'use strict';

    function initializeSidebar() {
        var sidebar = document.getElementById('appSidebar');
        if (!sidebar) return;
        var body = document.body;
        var desktop = window.matchMedia('(min-width: 992px)');
        var toggle = document.getElementById('sidebarDesktopToggle');
        var mobileToggle = document.getElementById('botonMenuMobile');
        var close = document.getElementById('sidebarCloseMobile');
        var overlay = document.getElementById('sidebarOverlay');
        var storageKey = 'colibri.sidebar.collapsed';

        // Some older submenu items use plain text instead of a label element.
        sidebar.querySelectorAll('.sidebar-link').forEach(function (link) {
            Array.from(link.childNodes).forEach(function (node) {
                if (node.nodeType === 3 && node.textContent.trim()) {
                    var label = document.createElement('span');
                    label.className = 'sidebar-link-label';
                    label.textContent = node.textContent.trim();
                    link.replaceChild(label, node);
                } else if (node.nodeType === 1 && node.tagName === 'SPAN' && !node.classList.contains('badge')) {
                    node.classList.add('sidebar-link-label');
                }
            });
            var label = Array.from(link.querySelectorAll('.sidebar-link-label')).map(function (node) {
                return node.textContent.trim();
            }).join(' ');
            if (label) {
                if (!link.hasAttribute('title')) link.title = label;
                if (!link.hasAttribute('aria-label')) link.setAttribute('aria-label', label);
            }
            if (link.classList.contains('active') && link.getAttribute('data-toggle') !== 'collapse') {
                link.setAttribute('aria-current', 'page');
            }
            if (!link.hasAttribute('href')) {
                link.setAttribute('role', 'button');
                link.setAttribute('tabindex', '0');
                link.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        link.click();
                    }
                });
            }
        });

        function updateDesktopControl() {
            var collapsed = body.classList.contains('sidebar-collapsed');
            var label = collapsed ? 'Expandir menú' : 'Contraer menú';
            toggle.setAttribute('aria-expanded', String(!collapsed));
            toggle.setAttribute('aria-label', label);
            toggle.title = label;
            toggle.querySelector('i').className = 'fas fa-chevron-' + (collapsed ? 'right' : 'left');
        }

        function setCollapsed(collapsed) {
            body.classList.toggle('sidebar-collapsed', collapsed);
            updateDesktopControl();
            try { localStorage.setItem(storageKey, collapsed ? '1' : '0'); } catch (error) { /* Optional preference. */ }
        }

        function setMobileOpen(open, restoreFocus) {
            open = open && !desktop.matches;
            sidebar.classList.toggle('active', open);
            overlay.classList.toggle('active', open);
            body.classList.toggle('sidebar-mobile-open', open);
            mobileToggle.setAttribute('aria-expanded', String(open));
            mobileToggle.setAttribute('aria-label', open ? 'Cerrar menú de navegación' : 'Abrir menú de navegación');
            if (!desktop.matches && !open) {
                sidebar.setAttribute('inert', '');
                sidebar.setAttribute('aria-hidden', 'true');
            } else {
                sidebar.removeAttribute('inert');
                sidebar.removeAttribute('aria-hidden');
            }
            if (open) close.focus();
            else if (restoreFocus && !desktop.matches) mobileToggle.focus();
        }

        try { body.classList.toggle('sidebar-collapsed', localStorage.getItem(storageKey) === '1'); } catch (error) { /* Keep default expanded. */ }
        updateDesktopControl();
        setMobileOpen(false, false);

        toggle.addEventListener('click', function () {
            if (desktop.matches) setCollapsed(!body.classList.contains('sidebar-collapsed'));
        });
        mobileToggle.addEventListener('click', function () { setMobileOpen(!sidebar.classList.contains('active'), true); });
        close.addEventListener('click', function () { setMobileOpen(false, true); });
        overlay.addEventListener('click', function () { setMobileOpen(false, true); });

        sidebar.addEventListener('click', function (event) {
            var link = event.target.closest('.sidebar-link');
            if (!link) return;
            if (desktop.matches && body.classList.contains('sidebar-collapsed') && link.getAttribute('data-toggle') === 'collapse') {
                event.preventDefault();
                event.stopPropagation();
                setCollapsed(false);
                var submenu = document.getElementById(link.getAttribute('aria-controls'));
                if (submenu && window.jQuery && window.jQuery.fn.collapse) {
                    window.jQuery(submenu).collapse('show');
                } else if (submenu) {
                    submenu.classList.add('show');
                    link.setAttribute('aria-expanded', 'true');
                    link.classList.remove('collapsed');
                }
            } else if (!desktop.matches && link.getAttribute('data-toggle') !== 'collapse') {
                setMobileOpen(false, false);
            }
        }, true);

        document.addEventListener('keydown', function (event) {
            if (desktop.matches || !sidebar.classList.contains('active')) return;
            if (event.key === 'Escape') {
                event.preventDefault();
                setMobileOpen(false, true);
            } else if (event.key === 'Tab') {
                var focusable = Array.from(sidebar.querySelectorAll('a[href], button, [tabindex="0"]')).filter(function (node) {
                    return !node.disabled && node.offsetParent !== null;
                });
                if (!focusable.length) return;
                var first = focusable[0];
                var last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault(); last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault(); first.focus();
                }
            }
        });
        desktop.addEventListener('change', function () { setMobileOpen(false, false); });
        sidebar.addEventListener('transitionend', function (event) {
            if (event.propertyName === 'width') window.dispatchEvent(new Event('resize'));
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeSidebar, { once: true });
    else initializeSidebar();
}());
