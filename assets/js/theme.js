(function () {
	'use strict';

	const toggle = document.querySelector('.mobile-menu-toggle');
	const menu = document.querySelector('#site-menu-panel');

    const drawer = document.querySelector('#site-menu-drawer');
    const backdrop = document.querySelector('.menu-backdrop');
    const closeButton = document.querySelector('.mobile-menu-close');
    if (toggle && menu && drawer && backdrop && closeButton) {
        const mobile = window.matchMedia('(max-width: 1023px)');
        const label = toggle.querySelector('.screen-reader-text');
        const inertBefore = new Map();
        let open = false;
        let scrollBefore = 0;
        let bodyStyles;
        const lockBackground = () => {
            let branch = drawer;
            while (branch.parentElement && branch.parentElement !== document.documentElement) {
                for (const sibling of branch.parentElement.children) {
                    if (sibling === branch || sibling === backdrop || !(sibling instanceof HTMLElement) || ['SCRIPT','STYLE','LINK'].includes(sibling.tagName)) continue;
                    inertBefore.set(sibling, sibling.inert);
                    sibling.inert = true;
                }
                branch = branch.parentElement;
            }
            scrollBefore = window.scrollY;
            bodyStyles = {};
            for (const prop of ['position', 'top', 'left', 'width']) bodyStyles[prop] = document.body.style[prop];
            Object.assign(document.body.style, { position: 'fixed', top: `-${scrollBefore}px`, left: '0', width: `${document.documentElement.clientWidth}px` });
        };
        const unlockBackground = () => {
            inertBefore.forEach((value, element) => { element.inert = value; });
            inertBefore.clear();
            if (bodyStyles) {
                Object.assign(document.body.style, bodyStyles);
                bodyStyles = null;
                window.scrollTo({ top: scrollBefore, behavior: 'instant' });
            }
        };
        const setMenu = (next, restoreFocus = true) => {
            const expanded = mobile.matches && next;
            const wasOpen = open;
            open = expanded;
            toggle.setAttribute('aria-expanded', String(open));
            if (label) label.textContent = open ? toggle.dataset.closeLabel : toggle.dataset.openLabel;
            drawer.classList.toggle('is-open', open);
            drawer.inert = mobile.matches && !open;
            backdrop.hidden = !open;
            if (mobile.matches) {
                drawer.setAttribute('role', 'dialog');
                drawer.setAttribute('aria-modal', 'true');
                drawer.setAttribute('aria-labelledby', 'mobile-menu-title');
            } else {
                ['role', 'aria-modal', 'aria-labelledby'].forEach(attr => drawer.removeAttribute(attr));
            }
            if (open && !wasOpen) {
                lockBackground();
                drawer.querySelector('.menu-drawer-surface').scrollTop = 0;
                requestAnimationFrame(() => { if (open) closeButton.focus({ preventScroll: true }); });
            } else if (!open && wasOpen) {
                unlockBackground();
                if (restoreFocus && mobile.matches) toggle.focus({ preventScroll: true });
            }
        };
        const focusable = () => [...drawer.querySelectorAll('a[href], button, input, select, textarea, [tabindex]')]
            .filter(el => !el.disabled && el.tabIndex >= 0 && !el.closest('[hidden]') && el.getClientRects().length && getComputedStyle(el).visibility !== 'hidden');
        toggle.addEventListener('click', () => setMenu(!open));
        closeButton.addEventListener('click', () => setMenu(false));
        backdrop.addEventListener('click', () => setMenu(false));
        document.addEventListener('keydown', event => {
            if (!open) return;
            if (event.key === 'Escape') { event.preventDefault(); setMenu(false); }
            if (event.key === 'Tab') {
                const items = focusable();
                const first = items[0]; const last = items[items.length - 1];
                if (event.shiftKey && (document.activeElement === first || !drawer.contains(document.activeElement))) {
                    event.preventDefault(); last?.focus();
                } else if (!event.shiftKey && (document.activeElement === last || !drawer.contains(document.activeElement))) {
                    event.preventDefault(); first?.focus();
                }
            }
        });
        document.addEventListener('focusin', event => {
            if (open && !drawer.contains(event.target)) closeButton.focus({ preventScroll: true });
        });
        menu.addEventListener('click', event => {
            if (event.target.closest('a')) setMenu(false);
        });
        const submenus = [...menu.querySelectorAll('.sub-menu')].map((submenu, index) => {
            const parent = submenu.parentElement;
            const link = [...parent.children].find(el => el.matches('a'));
            if (!link) return null;
            submenu.id ||= `mobile-submenu-${index}`;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'submenu-toggle';
            button.setAttribute('aria-controls', submenu.id);
            button.setAttribute('aria-expanded', 'false');
            const text = link.textContent.trim();
            const update = expanded => {
                button.setAttribute('aria-expanded', String(expanded));
                button.setAttribute('aria-label', `${expanded ? drawer.dataset.collapseLabel : drawer.dataset.expandLabel}: ${text}`);
                button.textContent = expanded ? '\u2212' : '+';
                submenu.hidden = mobile.matches && !expanded;
            };
            parent.classList.add('has-submenu-toggle');
            link.after(button);
            button.addEventListener('click', () => update(button.getAttribute('aria-expanded') !== 'true'));
            return { update, current: parent.classList.contains('current-menu-ancestor') };
        }).filter(Boolean);
        const sync = () => {
            setMenu(false, false);
            submenus.forEach(item => item.update(mobile.matches && item.current));
        };
        mobile.addEventListener('change', sync);
        sync();
    }

    const fullHeader = document.querySelector('.site-header');
    const compactHeader = document.querySelector('.desktop-compact-header');
    if (fullHeader && compactHeader && menu) {
        const desktop = window.matchMedia('(min-width: 1024px)');
        const fullMenu = compactHeader.querySelector('.desktop-compact-menu');
        const cleanClone = element => {
            const copy = element.cloneNode(true);
            [copy, ...copy.querySelectorAll('[id]')].forEach(node => node.removeAttribute('id'));
            copy.querySelectorAll('.submenu-toggle').forEach(button => button.remove());
            copy.querySelectorAll('[hidden]').forEach(node => node.removeAttribute('hidden'));
            return copy;
        };
        const brand = fullHeader.querySelector('.header-branding a');
        if (brand) compactHeader.querySelector('.desktop-compact-brand').append(cleanClone(brand));
        const originalList = menu.querySelector('.site-menu');
        if (originalList) {
            const allLinks = cleanClone(originalList);
            allLinks.className = 'desktop-compact-all-links';
            fullMenu.querySelector('nav').append(allLinks);
            const editorias = document.createElement('ul');
            editorias.className = 'desktop-compact-short-links';
            // Prefer editorial links over the home link in the compact selection.
            const items = [...originalList.children];
            items.slice(items.length > 3 ? 1 : 0, items.length > 3 ? 4 : 3).forEach(item => {
                const link = [...item.children].find(node => node.matches('a'));
                if (!link) return;
                const li = document.createElement('li');
                li.append(cleanClone(link));
                editorias.append(li);
            });
            compactHeader.querySelector('.desktop-compact-editorias').append(editorias);
        }
        const search = menu.querySelector('.site-search');
        if (search) {
            const copy = cleanClone(search);
            copy.className = 'desktop-compact-search-form';
            const input = copy.querySelector('input');
            const label = copy.querySelector('label');
            input.id = 'desktop-compact-search-input';
            if (label) label.htmlFor = input.id;
            compactHeader.querySelector('.desktop-compact-search').append(copy);
        }
        const syncCompactHeader = () => {
            const show = desktop.matches && fullHeader.getBoundingClientRect().bottom <= 0;
            if (!show) {
                fullMenu.open = false;
                if (compactHeader.contains(document.activeElement)) {
                    const destination = desktop.matches ? brand : toggle;
                    destination?.focus({ preventScroll: true });
                }
            }
            compactHeader.hidden = !show;
            compactHeader.inert = !show;
        };
        const observer = new IntersectionObserver(syncCompactHeader);
        observer.observe(fullHeader);
        new ResizeObserver(syncCompactHeader).observe(fullHeader);
        desktop.addEventListener('change', syncCompactHeader);
        window.addEventListener('resize', syncCompactHeader);
        fullMenu.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                fullMenu.open = false;
                fullMenu.querySelector('summary').focus();
            }
        });
        document.addEventListener('click', event => {
            if (!fullMenu.contains(event.target)) fullMenu.open = false;
        });
        syncCompactHeader();
    }

	const carousel = document.querySelector('[data-featured-carousel]');
	if (carousel) {
		const slides = Array.from(carousel.querySelectorAll('.featured-slide'));
		const dots = Array.from(carousel.querySelectorAll('[data-carousel-dot]'));
		const prevButton = carousel.querySelector('[data-carousel-prev]');
		const nextButton = carousel.querySelector('[data-carousel-next]');
		let activeIndex = 0;
		const showSlide = (index) => {
			activeIndex = (index + slides.length) % slides.length;
			slides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === activeIndex));
			dots.forEach((dot, dotIndex) => dot.classList.toggle('is-active', dotIndex === activeIndex));
		};
		prevButton?.addEventListener('click', () => showSlide(activeIndex - 1));
		nextButton?.addEventListener('click', () => showSlide(activeIndex + 1));
		dots.forEach((dot) => dot.addEventListener('click', () => showSlide(Number(dot.dataset.carouselDot))));
		window.setInterval(() => showSlide(activeIndex + 1), 5000);
	}

	const copyLink = document.querySelector('[data-copy-link]');
	if (copyLink) {
		copyLink.addEventListener('click', async () => {
			await navigator.clipboard.writeText(window.location.href);
			copyLink.textContent = '✓';
			window.setTimeout(() => { copyLink.textContent = '↗'; }, 1600);
		});
	}

	const article = document.querySelector('.single-content');
	const readButton = document.querySelector('[data-read-article]');
	const stopButton = document.querySelector('[data-stop-reading]');
	const status = document.querySelector('[data-reading-status]');
	let fontScale = 0;

	if (article && readButton && stopButton && 'speechSynthesis' in window) {
		readButton.addEventListener('click', () => {
			window.speechSynthesis.cancel();
			const utterance = new SpeechSynthesisUtterance(article.innerText);
			utterance.lang = 'pt-BR';
			utterance.rate = 0.95;
			utterance.onstart = () => { stopButton.disabled = false; status.textContent = 'Leitura em andamento'; };
			utterance.onend = () => { stopButton.disabled = true; status.textContent = 'Leitura concluída'; };
			window.speechSynthesis.speak(utterance);
		});
		stopButton.addEventListener('click', () => {
			window.speechSynthesis.cancel();
			stopButton.disabled = true;
			status.textContent = 'Leitura pausada';
		});
	} else if (readButton) {
		readButton.disabled = true;
		readButton.title = 'A leitura em voz alta não está disponível neste navegador';
	}

	document.querySelector('[data-font-increase]')?.addEventListener('click', () => {
		fontScale = Math.min(fontScale + 1, 2);
		document.documentElement.dataset.fontScale = String(fontScale);
	});
	document.querySelector('[data-font-decrease]')?.addEventListener('click', () => {
		fontScale = Math.max(fontScale - 1, -1);
		document.documentElement.dataset.fontScale = String(fontScale);
	});
	document.querySelector('[data-contrast-toggle]')?.addEventListener('click', (event) => {
		const button = event.currentTarget;
		const enabled = document.documentElement.classList.toggle('accessibility-contrast');
		button.setAttribute('aria-pressed', String(enabled));
	});
})();
