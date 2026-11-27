document.addEventListener('DOMContentLoaded', () => {
    initStickyHeader();
    initMobileNav();
    initHeroSlider();
    initDestinationCoverflow();
    initCategorySlider();
    initSectionDots('guidesGrid', 'guideDots', 4);
    initTestimonialShowcase();
    initGalleryLightbox();
    initStatCounters();
    initScrollTopButton();
    initDemoLoginButtons();
    initToursFilterToggle();
    initGalleryPageFilters();
    initTourBookingGuestCalc();
});

function initStickyHeader() {
    const header = document.getElementById('siteHeader');
    if (!header) {
        return;
    }

    let ticking = false;
    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                if (window.scrollY > 24) {
                    header.classList.add('is-scrolled');
                } else {
                    header.classList.remove('is-scrolled');
                }
                ticking = false;
            });
            ticking = true;
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

function initHeroSlider() {
    const slider = document.getElementById('heroSlider');
    if (!slider) {
        return;
    }

    const slides = Array.from(slider.querySelectorAll('.hero-slide'));
    if (slides.length === 0) {
        return;
    }

    const prevBtn = document.getElementById('heroPrev');
    const nextBtn = document.getElementById('heroNext');
    const dotsContainer = document.getElementById('heroDots');
    const progressFill = document.getElementById('heroProgressFill');

    let currentIndex = 0;
    const totalSlides = slides.length;
    let isTransitioning = false;

    let dots = [];
    if (dotsContainer) {
        dots = Array.from(dotsContainer.querySelectorAll('.hero-side-dot'));
        if (dots.length !== totalSlides) {
            dotsContainer.innerHTML = '';
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'hero-side-dot' + (i === 0 ? ' is-active' : '');
                dot.setAttribute('data-slide-to', String(i));
                dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                dotsContainer.appendChild(dot);
            }
            dots = Array.from(dotsContainer.querySelectorAll('.hero-side-dot'));
        }
    }

    const resetProgress = () => {
        if (progressFill) {
            progressFill.style.width = '0%';
        }
    };

    const updateSlide = (nextIndex, isManual = false) => {
        if (isTransitioning) {
            return;
        }

        const prevIndex = currentIndex;
        const targetIndex = (nextIndex + totalSlides) % totalSlides;

        if (prevIndex === targetIndex && slides[targetIndex].classList.contains('is-active')) {
            return;
        }

        isTransitioning = true;
        currentIndex = targetIndex;

        // Transition slides: mark previous as outgoing to prevent blank frames or background flashes
        slides.forEach((slide, idx) => {
            if (idx === prevIndex) {
                slide.classList.remove('is-active');
                slide.classList.add('is-outgoing');
                slide.setAttribute('aria-hidden', 'true');
            } else if (idx === currentIndex) {
                slide.classList.remove('is-outgoing');
                slide.classList.add('is-active');
                slide.setAttribute('aria-hidden', 'false');
            } else {
                slide.classList.remove('is-active', 'is-outgoing');
                slide.setAttribute('aria-hidden', 'true');
            }
        });

        // Update pagination dots
        dots.forEach((dot, idx) => {
            const isActive = idx === currentIndex;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        resetProgress();

        // Unlock transitions and remove outgoing status once crossfade is complete
        window.setTimeout(() => {
            if (slides[prevIndex]) {
                slides[prevIndex].classList.remove('is-outgoing');
            }
            isTransitioning = false;
        }, 750);
    };

    const nextSlide = (isManual = false) => {
        updateSlide(currentIndex + 1, isManual);
    };

    const prevSlide = (isManual = false) => {
        updateSlide(currentIndex - 1, isManual);
    };

    // Manual-only navigation event listeners (hero slider autoplay intentionally disabled)
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            prevSlide(true);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            nextSlide(true);
        });
    }

    dots.forEach((dot, idx) => {
        dot.addEventListener('click', (e) => {
            e.preventDefault();
            updateSlide(idx, true);
        });
    });

    // Keyboard navigation
    slider.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide(true);
        } else if (e.key === 'ArrowRight') {
            nextSlide(true);
        }
    });

    // Touch swipe navigation with directional ratio check (prevents vertical scroll conflict)
    let touchStartX = 0;
    let touchStartY = 0;
    let touchEndX = 0;
    let touchEndY = 0;

    slider.addEventListener('touchstart', (e) => {
        if (e.touches && e.touches[0]) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }
    }, { passive: true });

    slider.addEventListener('touchend', (e) => {
        if (e.changedTouches && e.changedTouches[0]) {
            touchEndX = e.changedTouches[0].clientX;
            touchEndY = e.changedTouches[0].clientY;
            const diffX = touchEndX - touchStartX;
            const diffY = touchEndY - touchStartY;

            // Only trigger if horizontal movement is dominant
            if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX < 0) {
                    nextSlide(true);
                } else {
                    prevSlide(true);
                }
            }
        }
    }, { passive: true });

    // Initialize first slide and keep stationary until manual interaction
    updateSlide(0);
    resetProgress();
}

function initMobileNav() {
    const toggle = document.getElementById('navToggle');
    const menu = document.getElementById('navMenu');
    if (!toggle || !menu) {
        return;
    }

    const toggleMenu = (open) => {
        const isOpen = typeof open === 'boolean' ? open : !menu.classList.contains('is-open');
        menu.classList.toggle('is-open', isOpen);
        toggle.classList.toggle('is-active', isOpen);
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMenu();
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (menu.classList.contains('is-open') && !menu.contains(e.target) && !toggle.contains(e.target)) {
            toggleMenu(false);
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menu.classList.contains('is-open')) {
            toggleMenu(false);
        }
    });

    // Close when clicking a nav link that navigates on the page
    const menuLinks = menu.querySelectorAll('.nav-links a');
    menuLinks.forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992) {
                toggleMenu(false);
            }
        });
    });
}

function initDestinationCoverflow() {
    const stage = document.getElementById('destStage');
    if (!stage) {
        return;
    }

    const cards = Array.from(stage.querySelectorAll('.dest-card'));
    if (cards.length === 0) {
        return;
    }

    let activeIndex = Math.min(2, Math.floor(cards.length / 2));

    const applyPositions = () => {
        const total = cards.length;
        cards.forEach((card, index) => {
            let offset = index - activeIndex;
            if (total > 2) {
                if (offset > Math.floor(total / 2)) {
                    offset -= total;
                }
                if (offset < -Math.floor(total / 2)) {
                    offset += total;
                }
            }

            let pos = 'hidden';
            if (offset === 0) {
                pos = 'center';
            } else if (offset === -1) {
                pos = 'prev1';
            } else if (offset === 1) {
                pos = 'next1';
            } else if (offset === -2) {
                pos = 'prev2';
            } else if (offset === 2) {
                pos = 'next2';
            }

            card.setAttribute('data-pos', pos);
        });
    };

    cards.forEach((card, index) => {
        card.addEventListener('click', (event) => {
            if (card.getAttribute('data-pos') !== 'center') {
                event.preventDefault();
                activeIndex = index;
                applyPositions();
            }
        });
    });

    const prevBtn = document.getElementById('destPrev');
    const nextBtn = document.getElementById('destNext');

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            activeIndex = (activeIndex - 1 + cards.length) % cards.length;
            applyPositions();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            activeIndex = (activeIndex + 1) % cards.length;
            applyPositions();
        });
    }

    applyPositions();
}

function initCategorySlider() {
    const slider = document.getElementById('categorySlider');
    if (!slider) {
        return;
    }

    const viewport = slider.querySelector('.category-slider-viewport');
    const track = slider.querySelector('.category-slider-track');
    const dotsWrap = document.getElementById('categorySliderDots');
    if (!viewport || !track) {
        return;
    }

    const originalSlides = Array.from(track.querySelectorAll('.category-slide'));
    const totalOriginal = originalSlides.length;
    if (totalOriginal === 0) {
        return;
    }

    // Clone slides for seamless infinite loop
    const CLONE_COUNT = Math.min(6, totalOriginal);
    for (let i = totalOriginal - CLONE_COUNT; i < totalOriginal; i++) {
        const clone = originalSlides[i].cloneNode(true);
        clone.classList.add('is-clone');
        track.insertBefore(clone, track.firstChild);
    }
    for (let i = 0; i < CLONE_COUNT; i++) {
        const clone = originalSlides[i].cloneNode(true);
        clone.classList.add('is-clone');
        track.appendChild(clone);
    }

    const allSlides = Array.from(track.querySelectorAll('.category-slide'));
    let currentIndex = CLONE_COUNT;
    let autoplayTimer = null;
    let isDragging = false;
    let dragged = false;
    let startX = 0;
    let currentDragX = 0;
    const interval = parseInt(slider.getAttribute('data-interval'), 10) || 4000;

    const getSlotWidth = () => {
        if (allSlides.length === 0) {
            return 264;
        }
        const rect = allSlides[0].getBoundingClientRect();
        const style = window.getComputedStyle(allSlides[0]);
        const mr = parseFloat(style.marginRight) || 44;
        return (rect.width || 220) + mr;
    };

    const updateDots = () => {
        if (!dotsWrap) {
            return;
        }
        const realIndex = (currentIndex - CLONE_COUNT + totalOriginal) % totalOriginal;
        Array.from(dotsWrap.children).forEach((dot, idx) => {
            const active = idx === realIndex;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    };

    const updateTrackPosition = (animate = true) => {
        const slotWidth = getSlotWidth();
        const viewportWidth = viewport.getBoundingClientRect().width || window.innerWidth;
        const targetX = (viewportWidth / 2) - ((currentIndex + 0.5) * slotWidth);

        if (animate) {
            track.style.transition = 'transform 0.65s cubic-bezier(0.2, 0.9, 0.3, 1)';
        } else {
            track.style.transition = 'none';
        }
        track.style.transform = `translate3d(${targetX}px, 0, 0)`;
        updateDots();
    };

    const goTo = (index, animate = true) => {
        currentIndex = index;
        updateTrackPosition(animate);
    };

    // Seamless clone wrap
    track.addEventListener('transitionend', (e) => {
        if (e.target !== track) {
            return;
        }
        if (currentIndex >= CLONE_COUNT + totalOriginal) {
            currentIndex = currentIndex - totalOriginal;
            updateTrackPosition(false);
        } else if (currentIndex < CLONE_COUNT) {
            currentIndex = currentIndex + totalOriginal;
            updateTrackPosition(false);
        }
    });

    // Pagination Dots
    if (dotsWrap) {
        dotsWrap.innerHTML = '';
        for (let i = 0; i < totalOriginal; i++) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'slider-dot' + (i === 0 ? ' is-active' : '');
            dot.setAttribute('role', 'tab');
            dot.setAttribute('aria-label', `Show category ${i + 1}`);
            dot.addEventListener('click', () => {
                goTo(CLONE_COUNT + i);
                startAutoplay();
            });
            dotsWrap.appendChild(dot);
        }
    }

    // Click on slide to center it
    allSlides.forEach((slide, idx) => {
        const links = slide.querySelectorAll('a');
        links.forEach((link) => {
            link.addEventListener('click', (e) => {
                if (dragged) {
                    e.preventDefault();
                    return;
                }
                if (idx !== currentIndex) {
                    e.preventDefault();
                    goTo(idx);
                    startAutoplay();
                }
            });
        });
    });

    // Touch & Mouse Drag
    const onPointerDown = (clientX) => {
        isDragging = true;
        dragged = false;
        startX = clientX;
        currentDragX = clientX;
        track.classList.add('is-dragging');
        stopAutoplay();
    };

    const onPointerMove = (clientX) => {
        if (!isDragging) {
            return;
        }
        const deltaX = clientX - startX;
        if (Math.abs(deltaX) > 6) {
            dragged = true;
        }

        const slotWidth = getSlotWidth();
        const viewportWidth = viewport.getBoundingClientRect().width || window.innerWidth;
        const baseX = (viewportWidth / 2) - ((currentIndex + 0.5) * slotWidth);
        track.style.transform = `translate3d(${baseX + deltaX}px, 0, 0)`;
        currentDragX = clientX;
    };

    const onPointerUp = () => {
        if (!isDragging) {
            return;
        }
        isDragging = false;
        track.classList.remove('is-dragging');

        const deltaX = currentDragX - startX;
        if (deltaX < -45) {
            goTo(currentIndex + 1);
        } else if (deltaX > 45) {
            goTo(currentIndex - 1);
        } else {
            goTo(currentIndex);
        }
        startAutoplay();
    };

    viewport.addEventListener('touchstart', (e) => {
        if (e.touches && e.touches[0]) {
            onPointerDown(e.touches[0].clientX);
        }
    }, { passive: true });

    window.addEventListener('touchmove', (e) => {
        if (isDragging && e.touches && e.touches[0]) {
            onPointerMove(e.touches[0].clientX);
        }
    }, { passive: true });

    window.addEventListener('touchend', onPointerUp, { passive: true });

    viewport.addEventListener('mousedown', (e) => {
        if (e.button === 0) {
            onPointerDown(e.clientX);
        }
    });

    window.addEventListener('mousemove', (e) => {
        if (isDragging) {
            onPointerMove(e.clientX);
        }
    });

    window.addEventListener('mouseup', onPointerUp);

    // Autoplay
    const startAutoplay = () => {
        stopAutoplay();
        if (totalOriginal > 1) {
            autoplayTimer = window.setInterval(() => {
                if (!document.hidden && !isDragging) {
                    goTo(currentIndex + 1);
                }
            }, interval);
        }
    };

    const stopAutoplay = () => {
        if (autoplayTimer) {
            window.clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    };

    slider.addEventListener('mouseenter', stopAutoplay);
    slider.addEventListener('mouseleave', startAutoplay);
    slider.addEventListener('focusin', stopAutoplay);
    slider.addEventListener('focusout', startAutoplay);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });

    slider.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            goTo(currentIndex - 1);
            startAutoplay();
        } else if (e.key === 'ArrowRight') {
            goTo(currentIndex + 1);
            startAutoplay();
        }
    });

    let resizeTimer = null;
    window.addEventListener('resize', () => {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(() => {
            updateTrackPosition(false);
        }, 100);
    });

    updateTrackPosition(false);
    startAutoplay();

    // ==============================================================
    // EXACT TOURM THEME CURVED WHEEL ALGORITHM
    // ==============================================================
    const multiplier = { translate: 0.1, rotate: 0.01 };
    function calculateWheel() {
        const cards = track.querySelectorAll('.category-card.single');
        const centerX = window.innerWidth * 0.5;
        cards.forEach((card) => {
            const rect = card.getBoundingClientRect();
            const r = centerX - (rect.x + rect.width * 0.5);
            let ty = Math.abs(r) * multiplier.translate - rect.width * multiplier.translate;
            if (ty < 0) {
                ty = 0;
            }
            const transformOrigin = r < 0 ? 'left top' : 'right top';
            card.style.transform = `translate(0, ${ty}px) rotate(${-r * multiplier.rotate}deg)`;
            card.style.transformOrigin = transformOrigin;
        });
    }

    function raf() {
        requestAnimationFrame(raf);
        calculateWheel();
    }
    raf();
}

function initSectionDots(containerId, dotsId, pageSize) {
    const container = document.getElementById(containerId);
    const dotsWrap = document.getElementById(dotsId);
    if (!container || !dotsWrap) {
        return;
    }

    const items = Array.from(container.children);
    if (items.length <= pageSize) {
        return;
    }

    const totalPages = Math.ceil(items.length / pageSize);
    let currentPage = 0;

    const renderPage = () => {
        const start = currentPage * pageSize;
        const end = start + pageSize;
        items.forEach((item, index) => {
            const visible = index >= start && index < end;
            item.classList.toggle('is-hidden-slide', !visible);
            if (containerId === 'categoryFan' && visible) {
                item.setAttribute('data-fan-pos', String(index - start));
            }
        });

        Array.from(dotsWrap.querySelectorAll('.carousel-dot')).forEach((dot, idx) => {
            dot.classList.toggle('is-active', idx === currentPage);
        });
    };

    dotsWrap.innerHTML = '';
    for (let i = 0; i < totalPages; i++) {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'carousel-dot' + (i === 0 ? ' is-active' : '');
        dot.setAttribute('aria-label', `Show page ${i + 1}`);
        dot.addEventListener('click', () => {
            currentPage = i;
            renderPage();
        });
        dotsWrap.appendChild(dot);
    }

    renderPage();
}

function initTestimonialShowcase() {
    const grid = document.getElementById('testimonialsGrid');
    const dotsWrap = document.getElementById('testimonialDots');
    if (!grid || !dotsWrap) {
        return;
    }

    const cards = Array.from(grid.querySelectorAll('.testimonial-card'));
    if (cards.length === 0) {
        return;
    }

    let centerIdx = Math.min(1, cards.length - 1);

    const renderWindow = () => {
        const total = cards.length;
        const visibleIndices = new Set();

        if (total <= 3) {
            cards.forEach((_, idx) => visibleIndices.add(idx));
        } else {
            visibleIndices.add((centerIdx - 1 + total) % total);
            visibleIndices.add(centerIdx);
            visibleIndices.add((centerIdx + 1) % total);
        }

        cards.forEach((card, idx) => {
            card.classList.toggle('is-hidden-slide', !visibleIndices.has(idx));
            card.classList.toggle('is-center-card', idx === centerIdx);
        });

        Array.from(dotsWrap.querySelectorAll('.carousel-dot')).forEach((dot, idx) => {
            dot.classList.toggle('is-active', idx === centerIdx);
        });
    };

    dotsWrap.innerHTML = '';
    cards.forEach((_, idx) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'carousel-dot' + (idx === centerIdx ? ' is-active' : '');
        dot.setAttribute('aria-label', `Focus testimonial ${idx + 1}`);
        dot.addEventListener('click', () => {
            centerIdx = idx;
            renderWindow();
        });
        dotsWrap.appendChild(dot);
    });

    const prevBtn = document.getElementById('testimonialPrevBtn');
    const nextBtn = document.getElementById('testimonialNextBtn');

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            centerIdx = (centerIdx - 1 + cards.length) % cards.length;
            renderWindow();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            centerIdx = (centerIdx + 1) % cards.length;
            renderWindow();
        });
    }

    renderWindow();
}

function initGalleryLightbox() {
    const modal = document.getElementById('galleryLightbox');
    const imgEl = document.getElementById('lightboxImage');
    const titleEl = document.getElementById('lightboxTitle');
    const locationEl = document.getElementById('lightboxLocation');
    const closeBtn = document.getElementById('lightboxClose');
    const triggers = Array.from(document.querySelectorAll('[data-lightbox-src]'));

    if (!modal || !imgEl || triggers.length === 0) {
        return;
    }

    const closeModal = () => {
        modal.classList.remove('is-open');
    };

    triggers.forEach((btn) => {
        btn.addEventListener('click', () => {
            imgEl.src = btn.getAttribute('data-lightbox-src') || '';
            if (titleEl) {
                titleEl.textContent = btn.getAttribute('data-lightbox-title') || '';
            }
            if (locationEl) {
                locationEl.textContent = btn.getAttribute('data-lightbox-location') || '';
            }
            modal.classList.add('is-open');
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
}

function initStatCounters() {
    const counters = Array.from(document.querySelectorAll('[data-stat-value]'));
    if (counters.length === 0 || !('IntersectionObserver' in window)) {
        return;
    }

    const animateCounter = (el) => {
        const raw = el.getAttribute('data-stat-value') || '';
        const match = raw.match(/^(\d+)(.*)$/);
        if (!match) {
            return;
        }

        const target = parseInt(match[1], 10);
        const suffix = match[2] || '';
        const duration = 900;
        const start = performance.now();

        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const current = Math.max(1, Math.round(target * progress));
            el.textContent = `${current}${suffix}`;
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = raw;
            }
        };

        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.35 });

    counters.forEach((el) => observer.observe(el));
}

function initScrollTopButton() {
    const btn = document.getElementById('scrollTopBtn');
    if (!btn) {
        return;
    }

    const toggleVisibility = () => {
        btn.classList.toggle('is-visible', window.scrollY > 360);
    };

    window.addEventListener('scroll', toggleVisibility, { passive: true });
    toggleVisibility();

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function initDemoLoginButtons() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('loginSubmitBtn');
    const demoButtons = document.querySelectorAll('[data-demo-email][data-demo-password]');

    if (!form || !emailInput || !passwordInput || demoButtons.length === 0) {
        return;
    }

    demoButtons.forEach((button) => {
        button.addEventListener('click', () => {
            emailInput.value = button.getAttribute('data-demo-email') || '';
            passwordInput.value = button.getAttribute('data-demo-password') || '';

            emailInput.classList.add('is-prefilled');
            passwordInput.classList.add('is-prefilled');

            window.setTimeout(() => {
                emailInput.classList.remove('is-prefilled');
                passwordInput.classList.remove('is-prefilled');
            }, 700);

            if (button.getAttribute('data-demo-submit') === 'true') {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
                return;
            }

            if (submitBtn) {
                submitBtn.focus();
            }
        });
    });
}

function initToursFilterToggle() {
    const toggleBtn = document.getElementById('toursFilterToggle');
    const sidebar = document.getElementById('toursFilterSidebar');
    if (!toggleBtn || !sidebar) {
        return;
    }

    toggleBtn.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('is-open');
        toggleBtn.setAttribute('aria-expanded', String(isOpen));
        const span = toggleBtn.querySelector('span');
        if (span) {
            span.textContent = isOpen ? 'Hide Filters' : 'Filter Journeys';
        }
    });
}

function initGalleryPageFilters() {
    const filterTabs = Array.from(document.querySelectorAll('[data-gallery-filter]'));
    const cards = Array.from(document.querySelectorAll('.photo-masonry-card'));
    if (filterTabs.length === 0 || cards.length === 0) {
        return;
    }

    filterTabs.forEach((tab) => {
        tab.addEventListener('click', (e) => {
            if (e.metaKey || e.ctrlKey || e.shiftKey) {
                return;
            }
            e.preventDefault();
            const target = (tab.getAttribute('data-gallery-filter') || 'all').toLowerCase();

            filterTabs.forEach((t) => {
                const isActive = t === tab;
                t.classList.toggle('is-active', isActive);
                t.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            let visibleCount = 0;
            cards.forEach((card) => {
                const itemCat = (card.getAttribute('data-photo-category') || '').toLowerCase();
                const itemLoc = (card.getAttribute('data-photo-location') || '').toLowerCase();
                const itemTitle = (card.getAttribute('data-photo-title') || '').toLowerCase();

                const matches = target === 'all'
                    || itemCat.includes(target)
                    || itemLoc.includes(target)
                    || itemTitle.includes(target);

                if (matches) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const emptyNotice = document.getElementById('galleryEmptyNotice');
            if (emptyNotice) {
                emptyNotice.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            const newUrl = tab.getAttribute('href');
            if (newUrl && window.history && window.history.replaceState) {
                window.history.replaceState(null, '', newUrl);
            }
        });
    });

    cards.forEach((card) => {
        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                card.click();
            }
        });
    });
}

function initTourBookingGuestCalc() {
    const guestsInput = document.getElementById('guests');
    const unitPriceEl = document.getElementById('tourUnitPrice');
    const liveTotalEl = document.getElementById('liveBookingTotal');

    if (!guestsInput || !unitPriceEl || !liveTotalEl) {
        return;
    }

    const price = parseFloat(unitPriceEl.getAttribute('data-price')) || 0;

    const updateTotal = () => {
        const guests = parseInt(guestsInput.value, 10) || 1;
        const total = price * Math.max(1, guests);
        liveTotalEl.textContent = `₹${total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    guestsInput.addEventListener('input', updateTotal);
    guestsInput.addEventListener('change', updateTotal);
}


