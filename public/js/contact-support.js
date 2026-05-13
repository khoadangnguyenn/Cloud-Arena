/**
 * Trang Liên hệ — Support console (GSAP topology, tilt, Typed / fallback).
 */
(function () {
    'use strict';

    var cfgEl = document.getElementById('contact-support-config');
    var cfg = {};
    try {
        cfg = cfgEl ? JSON.parse(cfgEl.textContent || '{}') : {};
    } catch (e) {
        cfg = {};
    }

    var gate = document.getElementById('support-step-gate');
    var main = document.getElementById('support-step-main');
    var btnOpen = document.getElementById('support-open-console');
    var form = document.getElementById('support-ticket-form');
    var catInput = document.getElementById('ticket_category');
    var catCards = document.querySelectorAll('[data-support-category]');
    var panels = document.querySelectorAll('[data-support-panel]');
    var consoleWrap = document.querySelector('.support-console-wrap');
    var terminalTa = document.querySelector('.support-terminal-field');
    var metricsEls = {
        healthy: document.querySelector('[data-metric="nodes-healthy"]'),
        latency: document.querySelector('[data-metric="vn-latency"]'),
        engineers: document.querySelector('[data-metric="engineers"]')
    };
    var topoLatencyEl = document.querySelector('[data-topology-latency]');

    function setStep(step) {
        if (!gate || !main) return;
        if (step === 'main') {
            gate.classList.add('support-hidden-step');
            main.classList.remove('support-hidden-step');
        } else {
            main.classList.add('support-hidden-step');
            gate.classList.remove('support-hidden-step');
        }
    }

    if (cfg.initialStep === 'main') {
        setStep('main');
    }

    if (btnOpen) {
        btnOpen.addEventListener('click', function () {
            setStep('main');
            try {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (e) {
                window.scrollTo(0, 0);
            }
        });
    }

    var labelEl = document.getElementById('support-category-label');

    function resetForgotPasswordVisibility() {
        var pwInput = document.getElementById('support_previous_password');
        var pwToggle = document.querySelector('[data-support-pw-toggle="forgot"]');
        if (pwInput) {
            pwInput.type = 'password';
        }
        if (pwToggle) {
            pwToggle.setAttribute('aria-pressed', 'false');
            pwToggle.setAttribute('aria-label', 'Hiện mật khẩu');
            pwToggle.setAttribute('title', 'Hiện mật khẩu');
        }
    }

    function setCategory(key) {
        if (!catInput) return;
        if (key !== 'forgot_password') {
            resetForgotPasswordVisibility();
        }
        catInput.value = key;
        if (labelEl && cfg.categoryLabels && cfg.categoryLabels[key]) {
            labelEl.textContent = cfg.categoryLabels[key];
        }
        panels.forEach(function (p) {
            p.classList.toggle('hidden', p.getAttribute('data-support-panel') !== key);
        });
        catCards.forEach(function (c) {
            var active = c.getAttribute('data-support-category') === key;
            c.classList.toggle('ring-2', active);
            c.classList.toggle('ring-cyan-400/60', active);
            c.classList.toggle('border-cyan-500/50', active);
        });
    }

    catCards.forEach(function (card) {
        function activateCard() {
            var key = card.getAttribute('data-support-category');
            if (!key) return;
            if (key === 'purchase_issue' && !cfg.isLoggedIn) {
                return;
            }
            setCategory(key);
            card.classList.add('is-glow');
            window.setTimeout(function () {
                card.classList.remove('is-glow');
            }, 450);
        }
        card.addEventListener('click', function (e) {
            if (e.target.closest('a[href]')) return;
            activateCard();
        });
        card.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            if (e.target.closest('a[href]')) return;
            e.preventDefault();
            activateCard();
        });
    });

    if (catInput && catInput.value) {
        setCategory(catInput.value);
    }

    (function initForgotPasswordToggle() {
        var pwInput = document.getElementById('support_previous_password');
        var pwToggle = document.querySelector('[data-support-pw-toggle="forgot"]');
        if (!pwInput || !pwToggle) {
            return;
        }
        pwToggle.addEventListener('click', function () {
            var show = pwInput.type === 'password';
            pwInput.type = show ? 'text' : 'password';
            pwToggle.setAttribute('aria-pressed', show ? 'true' : 'false');
            pwToggle.setAttribute('aria-label', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
            pwToggle.setAttribute('title', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
        });
    })();

    if (typeof gsap !== 'undefined') {
        catCards.forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                var x = e.clientX - r.left;
                var y = e.clientY - r.top;
                var rx = ((y / r.height) - 0.5) * -10;
                var ry = ((x / r.width) - 0.5) * 10;
                gsap.to(card, { duration: 0.25, rotateX: rx, rotateY: ry, y: -6, ease: 'power2.out' });
            });
            card.addEventListener('mouseleave', function () {
                gsap.to(card, { duration: 0.45, rotateX: 0, rotateY: 0, y: 0, ease: 'power2.out' });
            });
        });
    } else {
        catCards.forEach(function (card) {
            card.addEventListener('mouseenter', function () {
                card.style.transform = 'translateY(-6px)';
            });
            card.addEventListener('mouseleave', function () {
                card.style.transform = '';
            });
        });
    }

    function removeTypedCursors(el) {
        if (!el) return;
        el.querySelectorAll('.typed-cursor').forEach(function (n) {
            if (n.parentNode) {
                n.parentNode.removeChild(n);
            }
        });
    }

    function appendDiscordLinkLine(el) {
        if (!el) return;
        var href = typeof cfg.discordInviteUrl === 'string' ? cfg.discordInviteUrl : '';
        var label = typeof cfg.discordAnchorText === 'string' ? cfg.discordAnchorText : '';
        if (!href || !label) return;
        el.appendChild(document.createTextNode('\n'));
        var a = document.createElement('a');
        a.href = href;
        a.textContent = label;
        a.className = 'support-discord-terminal-link';
        a.target = '_blank';
        a.rel = 'noopener noreferrer';
        el.appendChild(a);
    }

    function initDiscordTyped() {
        var el = document.getElementById('support-discord-typed');
        if (!el) return;
        var body = typeof cfg.discordTypeText === 'string' ? cfg.discordTypeText : '';
        var href = typeof cfg.discordInviteUrl === 'string' ? cfg.discordInviteUrl : '';
        var anchorText = typeof cfg.discordAnchorText === 'string' ? cfg.discordAnchorText : '';

        if (body === '' && href && anchorText) {
            appendDiscordLinkLine(el);
            return;
        }

        if (typeof Typed !== 'undefined' && body !== '') {
            el.textContent = '';
            try {
                // eslint-disable-next-line no-new
                new Typed('#support-discord-typed', {
                    strings: [body],
                    typeSpeed: 14,
                    backSpeed: 0,
                    loop: false,
                    showCursor: true,
                    cursorChar: '▌',
                    onComplete: function () {
                        removeTypedCursors(el);
                        appendDiscordLinkLine(el);
                    }
                });
                return;
            } catch (err) {
                /* fall through */
            }
        }

        if (body === '') {
            return;
        }

        var ci = 0;
        var timer = window.setInterval(function () {
            if (ci < body.length) {
                el.textContent = body.slice(0, ci + 1) + '▌';
                ci++;
            } else {
                window.clearInterval(timer);
                el.textContent = body;
                appendDiscordLinkLine(el);
            }
        }, 42);
    }

    initDiscordTyped();

    function loopPacketOnPath(circle, pathEl, durationSec) {
        if (!circle || !pathEl || typeof pathEl.getTotalLength !== 'function') return;
        var len = pathEl.getTotalLength();
        if (!len || typeof gsap === 'undefined') return;
        var state = { t: 0 };
        gsap.to(state, {
            t: len,
            duration: durationSec,
            repeat: -1,
            ease: 'none',
            onUpdate: function () {
                var pt = pathEl.getPointAtLength(state.t % len);
                circle.setAttribute('cx', pt.x);
                circle.setAttribute('cy', pt.y);
            }
        });
    }

    function initSupportTopology() {
        var svg = document.getElementById('support-topology-svg');
        if (!svg || typeof gsap === 'undefined') return;

        var p1 = document.getElementById('support-path-sg-edge');
        var p2 = document.getElementById('support-path-tk-edge');
        var p3 = document.getElementById('support-path-vn-edge');
        var packs = svg.querySelectorAll('.support-topo-packet');
        if (packs[0] && p1) loopPacketOnPath(packs[0], p1, 3.2);
        if (packs[1] && p2) loopPacketOnPath(packs[1], p2, 2.8);
        if (packs[2] && p3) loopPacketOnPath(packs[2], p3, 2.2);

        var nodes = svg.querySelectorAll('.support-topo-node');
        if (nodes.length) {
            gsap.to(nodes, {
                opacity: 0.55,
                duration: 1.3,
                yoyo: true,
                repeat: -1,
                ease: 'sine.inOut',
                stagger: 0.18
            });
        }
    }

    initSupportTopology();

    function jitter(el, base, amp, decimals, suffix) {
        if (!el) return;
        var v = base + (Math.random() * 2 - 1) * amp;
        var t = decimals > 0 ? v.toFixed(decimals) : String(Math.round(v));
        el.textContent = t + (suffix || '');
    }

    window.setInterval(function () {
        jitter(metricsEls.healthy, 98, 0.35, 1, '%');
        var ms = 12 + Math.round((Math.random() * 2 - 1) * 2);
        if (metricsEls.latency) metricsEls.latency.textContent = ms + 'ms';
        if (topoLatencyEl) topoLatencyEl.textContent = ms + 'ms';
        var eng = metricsEls.engineers;
        if (eng && Math.random() > 0.92) {
            eng.textContent = String(2 + Math.round(Math.random()));
        }
    }, 1800);

    if (consoleWrap && form) {
        form.querySelectorAll('input, select, textarea').forEach(function (el) {
            el.addEventListener('focus', function () {
                consoleWrap.classList.add('is-focused');
            });
            el.addEventListener('blur', function () {
                consoleWrap.classList.remove('is-focused');
            });
        });
    }

    if (terminalTa) {
        function syncCaret() {
            terminalTa.classList.toggle('is-idle', document.activeElement !== terminalTa);
        }
        terminalTa.addEventListener('focus', syncCaret);
        terminalTa.addEventListener('blur', syncCaret);
        syncCaret();
    }

    if (form) {
        form.addEventListener('submit', function () {
            var submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('support-btn-loading');
                submitBtn.setAttribute('disabled', 'disabled');
            }
        });

        var resetBtn = document.getElementById('support-form-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                form.reset();
                resetForgotPasswordVisibility();
                var def = cfg.defaultCategory || 'bugs_technical';
                if (catInput) catInput.value = def;
                setCategory(def);
            });
        }
    }
})();
