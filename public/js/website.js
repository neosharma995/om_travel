document.addEventListener('DOMContentLoaded', () => {
    initStickyHeader();
    initMobileNav();
    initHeroSlider();
    initDestinationCoverflow();
    initSectionDots('categoryFan', 'categoryDots', 5);
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
    const intervalDuration = parseInt(slider.getAttribute('data-interval'), 10) || 6000;
    let autoplayTimer = null;
    let isPaused = false;
    let isTransitioning = false;
    let progressStartTime = 0;
    let progressAnimFrame = null;

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
        progressStartTime = performance.now();
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

    const runProgressBar = (timestamp) => {
        if (!isPaused && progressFill) {
            if (!progressStartTime) {
                progressStartTime = timestamp;
            }
            const elapsed = timestamp - progressStartTime;
            const percentage = Math.min((elapsed / intervalDuration) * 100, 100);
            progressFill.style.width = `${percentage}%`;
        }
        if (autoplayTimer) {
            progressAnimFrame = window.requestAnimationFrame(runProgressBar);
        }
    };

    const stopAutoplay = () => {
        if (autoplayTimer) {
            window.clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
        if (progressAnimFrame) {
            window.cancelAnimationFrame(progressAnimFrame);
            progressAnimFrame = null;
        }
    };

    const startAutoplay = () => {
        stopAutoplay();
        isPaused = false;
        resetProgress();
        progressAnimFrame = window.requestAnimationFrame(runProgressBar);

        autoplayTimer = window.setInterval(() => {
            if (!isPaused && !document.hidden) {
                nextSlide(false);
            }
        }, intervalDuration);
    };

    const pauseAutoplay = () => {
        isPaused = true;
    };

    const resumeAutoplay = () => {
        if (!document.hidden) {
            isPaused = false;
            resetProgress();
        }
    };

    // Manual navigation event listeners with immediate autoplay reset
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            prevSlide(true);
            startAutoplay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            nextSlide(true);
            startAutoplay();
        });
    }

    dots.forEach((dot, idx) => {
        dot.addEventListener('click', (e) => {
            e.preventDefault();
            updateSlide(idx, true);
            startAutoplay();
        });
    });

    // Pause on hover / focus, resume on mouse leave / blur
    slider.addEventListener('mouseenter', pauseAutoplay);
    slider.addEventListener('mouseleave', resumeAutoplay);
    slider.addEventListener('focusin', pauseAutoplay);
    slider.addEventListener('focusout', resumeAutoplay);

    // Browser tab visibility handling: pauses when inactive, resumes without stuttering
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });

    // Keyboard navigation
    slider.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide(true);
            startAutoplay();
        } else if (e.key === 'ArrowRight') {
            nextSlide(true);
            startAutoplay();
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
        pauseAutoplay();
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
                startAutoplay();
            } else {
                resumeAutoplay();
            }
        }
    }, { passive: true });

    // Initialize first slide and start autoplay loop
    updateSlide(0);
    startAutoplay();
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
        liveTotalEl.textContent = `$${total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    guestsInput.addEventListener('input', updateTotal);
    guestsInput.addEventListener('change', updateTotal);
}


