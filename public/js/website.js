document.addEventListener('DOMContentLoaded', () => {
    initMobileNav();
    initDestinationCoverflow();
    initSectionDots('categoryFan', 'categoryDots', 5);
    initSectionDots('guidesGrid', 'guideDots', 4);
    initTestimonialShowcase();
    initGalleryLightbox();
    initStatCounters();
    initScrollTopButton();
    initDemoLoginButtons();
});

function initMobileNav() {
    const toggle = document.getElementById('navToggle');
    const menu = document.getElementById('navMenu');
    if (!toggle || !menu) {
        return;
    }

    toggle.addEventListener('click', () => {
        menu.classList.toggle('is-open');
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
