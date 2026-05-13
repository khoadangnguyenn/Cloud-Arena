// Custom JavaScript for the client side
function initCloudArenaUi() {
    if (typeof window.AOS !== 'undefined') {
        var aosReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!aosReduced) {
            window.AOS.init({
                duration: 700,
                easing: 'ease-out-cubic',
                once: true,
                offset: 56,
                anchorPlacement: 'top-bottom'
            });
            window.addEventListener('load', function() {
                if (typeof window.AOS !== 'undefined') {
                    window.AOS.refresh();
                }
            });
        }
    }

    var siteHeader = document.getElementById('site-header');
    if (siteHeader) {
        var updateHeaderState = function() {
            if ((window.scrollY || window.pageYOffset) > 24) {
                siteHeader.classList.add('is-scrolled');
            } else {
                siteHeader.classList.remove('is-scrolled');
            }
        };
        updateHeaderState();
        window.addEventListener('scroll', updateHeaderState);
    }

    // Homepage: sync nav underline with hero vs #about-us while scrolling.
    (function initHomeNavScrollSpy() {
        var headerEl = document.getElementById('site-header');
        var aboutEl = document.getElementById('about-us');
        if (!headerEl || !aboutEl || !headerEl.classList.contains('header-home')) {
            return;
        }
        var homeLinks = document.querySelectorAll('a[data-nav-spy="home-top"]');
        var aboutLinks = document.querySelectorAll('a[data-nav-spy="about-us"]');
        if (!homeLinks.length || !aboutLinks.length) {
            return;
        }

        var rafId = null;
        var applySpy = function() {
            rafId = null;
            var headerH = headerEl.offsetHeight || 80;
            var threshold = headerH + 40;
            var inAbout = aboutEl.getBoundingClientRect().top <= threshold;
            homeLinks.forEach(function(link) {
                link.classList.toggle('is-active', !inAbout);
            });
            aboutLinks.forEach(function(link) {
                link.classList.toggle('is-active', inAbout);
            });
        };

        var scheduleSpy = function() {
            if (rafId !== null) {
                return;
            }
            rafId = window.requestAnimationFrame(applySpy);
        };

        applySpy();
        window.addEventListener('scroll', scheduleSpy, { passive: true });
        window.addEventListener('resize', scheduleSpy);
        window.addEventListener('load', scheduleSpy);
        window.addEventListener('hashchange', scheduleSpy);
    })();

    (function initClientMobileMenu() {
        var toggle = document.getElementById('site-mobile-menu-toggle');
        var panel = document.getElementById('site-mobile-menu');
        var backdrop = document.getElementById('site-mobile-menu-backdrop');
        if (!toggle || !panel || !backdrop) {
            return;
        }

        var setMenuOpen = function(isOpen) {
            toggle.classList.toggle('is-open', isOpen);
            panel.classList.toggle('is-open', isOpen);
            backdrop.classList.toggle('is-open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            toggle.setAttribute('aria-label', isOpen ? 'Đóng menu điều hướng' : 'Mở menu điều hướng');
            panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            backdrop.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            document.body.classList.toggle('site-mobile-menu-open', isOpen);
        };

        toggle.addEventListener('click', function() {
            setMenuOpen(!panel.classList.contains('is-open'));
        });

        backdrop.addEventListener('click', function() {
            setMenuOpen(false);
        });

        panel.addEventListener('click', function(event) {
            var anchor = event.target.closest('a');
            if (anchor && anchor.getAttribute('href')) {
                setMenuOpen(false);
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && panel.classList.contains('is-open')) {
                setMenuOpen(false);
            }
        });

        var closeIfDesktop = function() {
            if (window.matchMedia && window.matchMedia('(min-width: 768px)').matches) {
                setMenuOpen(false);
            }
        };
        window.addEventListener('resize', closeIfDesktop);
    })();

    var hero = document.getElementById('hero-parallax');
    if (hero) {
        var layers = hero.querySelectorAll('.parallax-layer');
        window.addEventListener('scroll', function() {
            var scrollTop = window.scrollY || window.pageYOffset;
            layers.forEach(function(layer) {
                var speed = parseFloat(layer.getAttribute('data-speed') || '0.2');
                layer.style.transform = 'translate3d(0, ' + Math.round(scrollTop * speed) + 'px, 0)';
            });
        });
    }

    var heroTypewriter = document.getElementById('hero-typewriter');
    if (heroTypewriter) {
        var fullHeroTitle = heroTypewriter.getAttribute('data-typewriter') || '';
        var cursorEl = heroTypewriter.parentNode
            ? heroTypewriter.parentNode.querySelector('.hero-type-cursor')
            : null;
        var heroReducedMotion = window.matchMedia
            && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (fullHeroTitle === '') {
            if (cursorEl) {
                cursorEl.style.visibility = 'hidden';
            }
        } else if (heroReducedMotion) {
            heroTypewriter.textContent = fullHeroTitle;
            if (cursorEl) {
                cursorEl.style.display = 'none';
            }
        } else {
            var visibleLen = 0;
            var phase = 'forward';
            var typeForwardMs = 52;
            var typeBackwardMs = 38;
            var pauseFullMs = 1800;
            var pauseEmptyMs = 1500;

            var applyHeroText = function() {
                heroTypewriter.textContent = fullHeroTitle.slice(0, visibleLen);
            };

            var typeStep = function() {
                if (phase === 'forward') {
                    if (visibleLen < fullHeroTitle.length) {
                        visibleLen += 1;
                        applyHeroText();
                        window.setTimeout(typeStep, typeForwardMs);
                    } else {
                        window.setTimeout(function() {
                            phase = 'backward';
                            typeStep();
                        }, pauseFullMs);
                    }
                } else if (phase === 'backward') {
                    if (visibleLen > 0) {
                        visibleLen -= 1;
                        applyHeroText();
                        window.setTimeout(typeStep, typeBackwardMs);
                    } else {
                        window.setTimeout(function() {
                            phase = 'forward';
                            typeStep();
                        }, pauseEmptyMs);
                    }
                }
            };

            window.setTimeout(typeStep, 280);
        }
    }

    var quickSearchForm = document.getElementById('quick-resource-search');
    if (quickSearchForm) {
        quickSearchForm.addEventListener('submit', function(event) {
            event.preventDefault();
            var typeInput = document.getElementById('resource_type');
            var keywordInput = document.getElementById('resource_keyword');

            var type = typeInput ? typeInput.value : 'products';
            var keyword = keywordInput ? keywordInput.value.trim() : '';
            var targetUrl = type === 'news' ? '/news' : '/products';

            if (keyword !== '') {
                targetUrl += '?keyword=' + encodeURIComponent(keyword);
            }

            window.location.href = window.URLROOT ? (window.URLROOT + targetUrl) : targetUrl;
        });
    }

    var initAdminCustomSelects = function(rootNode) {
        var scope = rootNode && rootNode.querySelectorAll ? rootNode : document;
        var selects = scope.querySelectorAll('select[data-admin-custom-select="true"]');
        if (!selects.length) {
            return;
        }

        var positionCustomSelectMenu = function(toggle, menu) {
            var viewportPadding = 12;
            var spacing = 6;
            var toggleRect = toggle.getBoundingClientRect();
            var targetWidth = Math.max(130, Math.round(toggleRect.width));

            menu.style.position = 'fixed';
            menu.style.margin = '0';
            menu.style.left = '0px';
            menu.style.top = '0px';
            menu.style.right = 'auto';
            menu.style.width = targetWidth + 'px';
            menu.style.minWidth = targetWidth + 'px';
            menu.style.maxWidth = targetWidth + 'px';
            menu.style.zIndex = '9999';
            menu.classList.remove('menu-dropup');

            var menuRect = menu.getBoundingClientRect();
            var left = toggleRect.left;
            if (left + targetWidth > window.innerWidth - viewportPadding) {
                left = window.innerWidth - targetWidth - viewportPadding;
            }
            if (left < viewportPadding) {
                left = viewportPadding;
            }

            var top = toggleRect.bottom + spacing;
            if (top + menuRect.height > window.innerHeight - viewportPadding) {
                top = toggleRect.top - menuRect.height - spacing;
                menu.classList.add('menu-dropup');
            }
            if (top < viewportPadding) {
                top = Math.max(viewportPadding, window.innerHeight - menuRect.height - viewportPadding);
            }

            menu.style.left = Math.round(left) + 'px';
            menu.style.top = Math.round(top) + 'px';
        };

        var closeCustomSelectMenus = function(exceptMenu) {
            var openMenus = document.querySelectorAll('.admin-custom-select-menu.show');
            openMenus.forEach(function(openMenu) {
                if (exceptMenu && openMenu === exceptMenu) {
                    return;
                }
                var ownerWrap = openMenu._adminSelectOwner;
                if (ownerWrap) {
                    var ownerToggle = ownerWrap.querySelector('.admin-custom-select-toggle');
                    if (ownerToggle) {
                        ownerToggle.setAttribute('aria-expanded', 'false');
                    }
                }
                openMenu.classList.remove('show');
                openMenu.classList.remove('menu-floating');
                openMenu.classList.remove('menu-dropup');
                openMenu.style.position = '';
                openMenu.style.left = '';
                openMenu.style.top = '';
                openMenu.style.right = '';
                openMenu.style.width = '';
                openMenu.style.minWidth = '';
                openMenu.style.maxWidth = '';
                openMenu.style.margin = '';
                openMenu.style.zIndex = '';
                if (ownerWrap && openMenu.parentNode === document.body) {
                    ownerWrap.appendChild(openMenu);
                }
            });
        };

        selects.forEach(function(select) {
            if (select.getAttribute('data-admin-custom-select-bound') === '1') {
                return;
            }
            select.setAttribute('data-admin-custom-select-bound', '1');

            var customWrap = document.createElement('div');
            customWrap.className = 'admin-custom-select';
            var toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'admin-custom-select-toggle';
            toggle.setAttribute('aria-haspopup', 'listbox');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Chọn giá trị');
            var label = document.createElement('span');
            label.className = 'admin-custom-select-label';
            var chevron = document.createElement('i');
            chevron.className = 'fa-solid fa-chevron-down';
            chevron.setAttribute('aria-hidden', 'true');
            toggle.appendChild(label);
            toggle.appendChild(chevron);

            var menu = document.createElement('div');
            menu.className = 'admin-custom-select-menu';
            menu.setAttribute('role', 'listbox');
            menu._adminSelectOwner = customWrap;

            var syncUi = function() {
                var activeOption = select.options[select.selectedIndex] || select.options[0];
                label.textContent = activeOption ? activeOption.textContent : 'Chọn';
                var optionButtons = menu.querySelectorAll('.admin-custom-select-option');
                optionButtons.forEach(function(btn) {
                    var isActive = btn.getAttribute('data-value') === select.value;
                    btn.classList.toggle('active', isActive);
                    btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
                toggle.classList.toggle('is-disabled', !!select.disabled);
                toggle.setAttribute('aria-disabled', select.disabled ? 'true' : 'false');
            };

            Array.prototype.forEach.call(select.options, function(option) {
                var optionBtn = document.createElement('button');
                optionBtn.type = 'button';
                optionBtn.className = 'admin-custom-select-option';
                optionBtn.setAttribute('role', 'option');
                optionBtn.setAttribute('data-value', option.value);
                optionBtn.textContent = option.textContent;
                if (option.disabled) {
                    optionBtn.disabled = true;
                }
                optionBtn.addEventListener('click', function() {
                    syncUi();
                    if (optionBtn.disabled || select.disabled) {
                        return;
                    }
                    select.value = option.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    syncUi();
                    closeCustomSelectMenus();
                });
                menu.appendChild(optionBtn);
            });

            toggle.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                syncUi();
                if (select.disabled) {
                    return;
                }
                var isOpen = menu.classList.contains('show');
                closeCustomSelectMenus();
                if (!isOpen) {
                    document.body.appendChild(menu);
                    menu.classList.add('show');
                    menu.classList.add('menu-floating');
                    positionCustomSelectMenu(toggle, menu);
                    toggle.setAttribute('aria-expanded', 'true');
                } else {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });

            select.addEventListener('change', syncUi);
            select.classList.add('admin-custom-select-native');
            select.setAttribute('tabindex', '-1');

            customWrap.appendChild(toggle);
            customWrap.appendChild(menu);
            select.insertAdjacentElement('afterend', customWrap);
            syncUi();
        });

        if (document.body.getAttribute('data-admin-custom-select-global-bound') !== '1') {
            document.body.setAttribute('data-admin-custom-select-global-bound', '1');

            document.addEventListener('click', function(event) {
                if (!event.target.closest('.admin-custom-select') && !event.target.closest('.admin-custom-select-menu')) {
                    closeCustomSelectMenus();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key !== 'Escape') {
                    return;
                }
                closeCustomSelectMenus();
            });

            window.addEventListener('resize', function() {
                closeCustomSelectMenus();
            });
            document.addEventListener('scroll', function() {
                var openMenu = document.querySelector('.admin-custom-select-menu.show');
                if (openMenu) {
                    closeCustomSelectMenus();
                }
            }, true);
        }
    };

    if (quickSearchForm) {
        initAdminCustomSelects(quickSearchForm);
    }

    initAuthForms();

    // Admin shell interactions.
    if (document.body.classList.contains('admin-modern')) {
        var adminUiAlreadyBound = document.body.getAttribute('data-admin-ui-bound') === '1';
        if (!adminUiAlreadyBound) {
            document.body.setAttribute('data-admin-ui-bound', '1');
        }

        var STORAGE_THEME_KEY = 'admin_theme_mode';
        var STORAGE_SIDEBAR_KEY = 'admin_sidebar_hidden';
        var sidebarToggle = document.getElementById('adminSidebarToggle');
        var sidebarOverlay = document.getElementById('adminOverlay');
        var themeToggle = document.getElementById('adminThemeToggle');
        var desktopSidebarHidden = false;
        var isDesktop = function() {
            return window.innerWidth >= 992;
        };
        var syncToggleState = function() {
            if (!sidebarToggle) {
                return;
            }
            if (isDesktop()) {
                sidebarToggle.setAttribute('aria-expanded', document.body.classList.contains('admin-sidebar-hidden') ? 'false' : 'true');
            } else {
                sidebarToggle.setAttribute('aria-expanded', document.body.classList.contains('admin-sidebar-open') ? 'true' : 'false');
            }
        };
        var closeSidebar = function() {
            document.body.classList.remove('admin-sidebar-open');
            syncToggleState();
        };
        var openSidebar = function() {
            document.body.classList.add('admin-sidebar-open');
            syncToggleState();
        };
        var setDesktopSidebarState = function(isHidden) {
            desktopSidebarHidden = !!isHidden;
            if (desktopSidebarHidden) {
                document.body.classList.add('admin-sidebar-hidden');
            } else {
                document.body.classList.remove('admin-sidebar-hidden');
            }
            syncToggleState();
        };
        var setThemeState = function(isDark) {
            if (isDark) {
                document.body.classList.add('admin-theme-dark');
            } else {
                document.body.classList.remove('admin-theme-dark');
            }
            document.body.setAttribute('data-admin-theme', isDark ? 'dark' : 'light');

            if (themeToggle) {
                themeToggle.innerHTML = isDark ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun"></i>';
                themeToggle.setAttribute('title', isDark ? 'Giao diện tối' : 'Giao diện sáng');
            }
        };

        if (!adminUiAlreadyBound) {
            try {
                var savedTheme = window.localStorage.getItem(STORAGE_THEME_KEY);
                if (savedTheme === 'dark' || savedTheme === 'light') {
                    setThemeState(savedTheme === 'dark');
                } else {
                    setThemeState(false);
                }
            } catch (e) {
                setThemeState(false);
            }

            try {
                setDesktopSidebarState(window.localStorage.getItem(STORAGE_SIDEBAR_KEY) === '1');
            } catch (e) {
                setDesktopSidebarState(false);
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (isDesktop()) {
                        var nextState = !document.body.classList.contains('admin-sidebar-hidden');
                        setDesktopSidebarState(nextState);
                        try {
                            window.localStorage.setItem(STORAGE_SIDEBAR_KEY, nextState ? '1' : '0');
                        } catch (e) {}
                    } else {
                        if (document.body.classList.contains('admin-sidebar-open')) {
                            closeSidebar();
                        } else {
                            openSidebar();
                        }
                    }
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    var toDark = !document.body.classList.contains('admin-theme-dark');
                    setThemeState(toDark);
                    try {
                        window.localStorage.setItem(STORAGE_THEME_KEY, toDark ? 'dark' : 'light');
                    } catch (e) {}
                });
            }

            window.addEventListener('resize', function() {
                if (isDesktop()) {
                    closeSidebar();
                    setDesktopSidebarState(desktopSidebarHidden);
                } else {
                    document.body.classList.remove('admin-sidebar-hidden');
                    syncToggleState();
                }
            });
            syncToggleState();
        }

        var chartSvg = document.getElementById('revenueAreaChart');
        var areaPath = document.getElementById('revenueAreaPath');
        var linePath = document.getElementById('revenueLinePath');
        var pointsGroup = document.getElementById('revenuePoints');
        var animatedLayer = document.getElementById('revenueAnimatedLayer');
        var axisLabels = document.getElementById('revenueAxisLabels');
        var filterSelect = document.getElementById('dashboardRevenueFilter');
        var totalLabel = document.getElementById('revenueTotalLabel');
        var revenueSeries = Array.isArray(window.adminRevenueSeries) ? window.adminRevenueSeries : [];
        var escapeHtmlAttr = function(value) {
            return String(value || '').replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        };
        var chartWrap = chartSvg ? chartSvg.closest('.revenue-area-wrap') : null;
        var revenueTooltip = null;
        if (chartWrap) {
            revenueTooltip = chartWrap.querySelector('.revenue-point-tooltip');
            if (!revenueTooltip) {
                revenueTooltip = document.createElement('div');
                revenueTooltip.className = 'revenue-point-tooltip';
                chartWrap.appendChild(revenueTooltip);
            }
        }
        var hideRevenueTooltip = function() {
            if (!revenueTooltip) return;
            revenueTooltip.classList.remove('is-visible');
        };
        var showRevenueTooltip = function(clientX, clientY, label, revenue) {
            if (!revenueTooltip || !chartWrap) return;
            revenueTooltip.innerHTML =
                '<strong>' + label + '</strong>' +
                '<span>Tổng: ' + formatCurrency(revenue) + '</span>';
            var wrapRect = chartWrap.getBoundingClientRect();
            var x = clientX - wrapRect.left;
            var y = clientY - wrapRect.top;
            revenueTooltip.classList.add('is-visible');
            var tipRect = revenueTooltip.getBoundingClientRect();
            var left = x - (tipRect.width / 2);
            left = Math.max(8, Math.min(left, wrapRect.width - tipRect.width - 8));
            var top = y - tipRect.height - 12;
            if (top < 8) {
                top = y + 12;
            }
            revenueTooltip.style.left = left + 'px';
            revenueTooltip.style.top = top + 'px';
        };

        var formatCurrency = function(value) {
            return '$' + Number(value || 0).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };
        var parseRevenueValue = function(value) {
            if (typeof value === 'number') {
                return isNaN(value) ? 0 : value;
            }
            if (typeof value === 'string') {
                var normalized = value.replace(/,/g, '').trim();
                var parsed = Number(normalized);
                return isNaN(parsed) ? 0 : parsed;
            }
            var fallback = Number(value || 0);
            return isNaN(fallback) ? 0 : fallback;
        };

        var renderRevenueChart = function(limit) {
            if (!chartSvg || !areaPath || !linePath || !pointsGroup || !axisLabels || revenueSeries.length === 0) {
                return;
            }
            var count = parseInt(limit, 10);
            if (!count || count < 1) {
                count = 5;
            }

            var points = revenueSeries.slice(-count);
            if (points.length === 0) {
                return;
            }

            var xStart = 50;
            var xEnd = 610;
            var yTop = 30;
            var yBottom = 240;
            var maxRevenue = 1;
            var i;
            for (i = 0; i < points.length; i++) {
                maxRevenue = Math.max(maxRevenue, parseRevenueValue(points[i].revenue));
            }

            var gap = points.length > 1 ? (xEnd - xStart) / (points.length - 1) : 0;
            var lineParts = [];
            var areaParts = [];
            var circleMarkup = [];
            var total = 0;

            for (i = 0; i < points.length; i++) {
                var revenue = parseRevenueValue(points[i].revenue);
                var x = xStart + (gap * i);
                var y = yBottom - ((revenue / maxRevenue) * (yBottom - yTop));
                total += revenue;

                lineParts.push((i === 0 ? 'M ' : 'L ') + x.toFixed(2) + ' ' + y.toFixed(2));
                areaParts.push((i === 0 ? 'M ' : 'L ') + x.toFixed(2) + ' ' + y.toFixed(2));
                var pointLabel = String(points[i].label || '');
                circleMarkup.push(
                    '<circle cx="' + x.toFixed(2) + '" cy="' + y.toFixed(2) + '" r="5"' +
                    ' data-label="' + escapeHtmlAttr(pointLabel) + '"' +
                    ' data-revenue="' + revenue + '"' +
                    ' tabindex="0"></circle>'
                );
            }

            areaParts.push('L ' + xEnd.toFixed(2) + ' ' + yBottom.toFixed(2));
            areaParts.push('L ' + xStart.toFixed(2) + ' ' + yBottom.toFixed(2));
            areaParts.push('Z');

            linePath.setAttribute('d', lineParts.join(' '));
            areaPath.setAttribute('d', areaParts.join(' '));
            pointsGroup.innerHTML = circleMarkup.join('');
            var pointNodes = pointsGroup.querySelectorAll('circle');
            pointNodes.forEach(function(node) {
                var onMove = function(evt) {
                    var label = node.getAttribute('data-label') || '';
                    var revenue = parseRevenueValue(node.getAttribute('data-revenue'));
                    showRevenueTooltip(evt.clientX, evt.clientY, label, revenue);
                };
                node.addEventListener('mouseenter', onMove);
                node.addEventListener('mousemove', onMove);
                node.addEventListener('mouseleave', hideRevenueTooltip);
                node.addEventListener('focus', function() {
                    var rect = node.getBoundingClientRect();
                    var label = node.getAttribute('data-label') || '';
                    var revenue = parseRevenueValue(node.getAttribute('data-revenue'));
                    showRevenueTooltip(rect.left + (rect.width / 2), rect.top, label, revenue);
                });
                node.addEventListener('blur', hideRevenueTooltip);
            });
            
            axisLabels.style.gridTemplateColumns = 'repeat(' + points.length + ', minmax(0, 1fr))';
            axisLabels.innerHTML = points.map(function(item) {
                return '<span>' + item.label + '</span>';
            }).join('');

            if (totalLabel) {
                totalLabel.textContent = 'Tổng: ' + formatCurrency(total);
            }

            if (animatedLayer) {
                animatedLayer.classList.remove('is-wiping');
                animatedLayer.getBBox();
                animatedLayer.classList.add('is-wiping');
            }
        };

        var showAdminToast = function(message, variant) {
            var container = document.getElementById('adminFloatingToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'adminFloatingToastContainer';
                container.className = 'admin-floating-toast-container';
                document.body.appendChild(container);
            }

            var toast = document.createElement('div');
            toast.className = 'admin-floating-toast ' + (variant === 'error' ? 'is-error' : 'is-success');
            toast.textContent = message;
            container.appendChild(toast);

            window.requestAnimationFrame(function() {
                toast.classList.add('is-visible');
            });

            window.setTimeout(function() {
                toast.classList.remove('is-visible');
                window.setTimeout(function() {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 220);
            }, 2600);
        };

        var initAdminLoginNotificationToast = function() {
            var stateNode = document.getElementById('adminLoginNotificationState');
            if (!stateNode || stateNode.getAttribute('data-bound') === '1') {
                return;
            }
            stateNode.setAttribute('data-bound', '1');

            var shouldShow = (stateNode.getAttribute('data-show') || '') === '1';
            var count = Math.max(0, parseInt(stateNode.getAttribute('data-count'), 10) || 0);
            if (!shouldShow || count <= 0) {
                return;
            }

            var label = count > 99 ? '99+' : String(count);
            window.setTimeout(function() {
                showAdminToast('Có ' + label + ' thông báo mới', 'success');
            }, 350);
        };

        var initAdminAutoSaveForms = function(rootNode) {
            var scope = rootNode && rootNode.querySelectorAll ? rootNode : document;
            var forms = scope.querySelectorAll('form[data-admin-autosave="true"]');
            if (!forms.length) {
                return;
            }

            forms.forEach(function(form) {
                if (form.getAttribute('data-admin-autosave-bound') === '1') {
                    return;
                }
                form.setAttribute('data-admin-autosave-bound', '1');

                var input = form.querySelector('[data-admin-autosave-input="true"]');
                if (!input) {
                    return;
                }

                input.addEventListener('change', function() {
                    var formData = new FormData(form);
                    var oldValue = input.getAttribute('data-prev-value') || '';
                    input.disabled = true;

                    window.fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    }).then(function(response) {
                        return response.json().then(function(payload) {
                            return {
                                ok: response.ok,
                                payload: payload
                            };
                        });
                    }).then(function(result) {
                        if (!result.ok || !result.payload || !result.payload.success) {
                            throw new Error((result.payload && result.payload.message) ? result.payload.message : 'Auto-save failed');
                        }
                        input.setAttribute('data-prev-value', input.value);
                        showAdminToast(form.getAttribute('data-toast-success') || result.payload.message || 'Đã cập nhật tự động.', 'success');
                    }).catch(function(error) {
                        input.value = oldValue || input.value;
                        showAdminToast(error.message || 'Không thể tự động cập nhật.', 'error');
                    }).finally(function() {
                        input.disabled = false;
                    });
                });

                input.setAttribute('data-prev-value', input.value);
            });
        };

        var initAdminGlobalSearch = function() {
            var searchForm = document.getElementById('adminGlobalSearchForm');
            var searchInput = document.getElementById('adminGlobalSearchInput');
            if (!searchForm || !searchInput || searchForm.getAttribute('data-admin-search-bound') === '1') {
                return;
            }
            searchForm.setAttribute('data-admin-search-bound', '1');

            var activeSection = (searchForm.getAttribute('data-active-section') || '').toLowerCase();
            var moduleRoutes = [
                { keywords: ['dashboard', 'tong quan', 'bảng điều khiển', 'bang dieu khien'], path: '/admin' },
                { keywords: ['dịch vụ', 'dich vu', 'service', 'services'], path: '/adminproducts' },
                { keywords: ['ticket', 'support', 'liên hệ', 'lien he'], path: '/admincontacts' },
                { keywords: ['tin tức', 'tin tuc', 'news'], path: '/adminnews' },
                { keywords: ['người dùng', 'nguoi dung', 'thành viên', 'thanh vien', 'user'], path: '/admin/users' },
                { keywords: ['cài đặt', 'cai dat', 'setting', 'settings'], path: '/admin/settings/homepage' }
            ];

            searchForm.addEventListener('submit', function(event) {
                event.preventDefault();
                var keyword = (searchInput.value || '').trim();
                if (keyword === '') {
                    searchInput.focus();
                    return;
                }

                var normalized = keyword.toLowerCase();
                var moduleTarget = null;
                moduleRoutes.forEach(function(routeItem) {
                    if (moduleTarget !== null) {
                        return;
                    }
                    routeItem.keywords.forEach(function(itemKeyword) {
                        if (moduleTarget === null && normalized.indexOf(itemKeyword) !== -1) {
                            moduleTarget = routeItem.path;
                        }
                    });
                });

                if (moduleTarget !== null) {
                    window.location.href = (window.URLROOT || '') + moduleTarget;
                    return;
                }

                var targetUrl = '/admin/users?keyword=' + encodeURIComponent(keyword);
                if (activeSection === 'tickets') {
                    targetUrl = '/admincontacts?keyword=' + encodeURIComponent(keyword);
                }
                window.location.href = (window.URLROOT || '') + targetUrl;
            });
        };

        var initBrandingUploadZone = function() {
            var zone = document.getElementById('brandingUploadZone');
            var input = document.getElementById('branding_asset');
            var browseButton = document.getElementById('brandingUploadBrowse');
            var fileLabel = document.getElementById('brandingUploadFilename');

            if (!zone || !input) {
                return;
            }

            var updateLabel = function() {
                if (fileLabel) {
                    fileLabel.textContent = input.files && input.files.length ? input.files[0].name : '';
                }
            };

            if (browseButton) {
                browseButton.addEventListener('click', function() {
                    input.click();
                });
            }

            input.addEventListener('change', updateLabel);

            zone.addEventListener('dragover', function(event) {
                event.preventDefault();
                zone.classList.add('is-dragging');
            });
            zone.addEventListener('dragleave', function() {
                zone.classList.remove('is-dragging');
            });
            zone.addEventListener('drop', function(event) {
                event.preventDefault();
                zone.classList.remove('is-dragging');
                if (event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length) {
                    input.files = event.dataTransfer.files;
                    updateLabel();
                }
            });
        };

        var initHeroBgUploadZone = function() {
            var zone = document.getElementById('heroBgUploadZone');
            var input = document.getElementById('hero_bg_asset');
            var browseButton = document.getElementById('heroBgUploadBrowse');
            var fileLabel = document.getElementById('heroBgUploadFilename');
            if (!zone || !input) {
                return;
            }
            var updateLabel = function() {
                if (fileLabel) {
                    fileLabel.textContent = input.files && input.files.length ? input.files[0].name : '';
                }
            };
            if (browseButton) {
                browseButton.addEventListener('click', function() {
                    input.click();
                });
            }
            input.addEventListener('change', updateLabel);
            zone.addEventListener('dragover', function(event) {
                event.preventDefault();
                zone.classList.add('is-dragging');
            });
            zone.addEventListener('dragleave', function() {
                zone.classList.remove('is-dragging');
            });
            zone.addEventListener('drop', function(event) {
                event.preventDefault();
                zone.classList.remove('is-dragging');
                if (event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length) {
                    input.files = event.dataTransfer.files;
                    updateLabel();
                }
            });
        };

        var initMapPreview = function() {
            var mapInput = document.getElementById('site_map_embed_url');
            var mapFrame = document.getElementById('mapPreviewFrame');
            var mapPlaceholder = document.getElementById('mapPreviewPlaceholder');
            if (!mapInput || !mapFrame || !mapPlaceholder) {
                return;
            }

            var updatePreview = function() {
                var value = (mapInput.value || '').trim();
                var isValid = /^https?:\/\/.+/i.test(value);

                if (isValid) {
                    mapFrame.src = value;
                    mapFrame.classList.remove('d-none');
                    mapPlaceholder.classList.add('d-none');
                } else {
                    mapFrame.src = '';
                    mapFrame.classList.add('d-none');
                    mapPlaceholder.classList.remove('d-none');
                }
            };

            mapInput.addEventListener('input', updatePreview);
            updatePreview();
        };

        var initUserActionDropdowns = function() {
            var toggles = document.querySelectorAll('.user-actions-toggle');
            if (!toggles.length) {
                return;
            }
            var globalListenersBound = document.body.getAttribute('data-user-dropdown-global-bound') === '1';

            var positionMenu = function(toggle, menu) {
                var viewportPadding = 12;
                var spacing = 6;
                var toggleRect = toggle.getBoundingClientRect();
                var menuRect = menu.getBoundingClientRect();
                var menuWidth = menuRect.width;
                var menuHeight = menuRect.height;

                var top = toggleRect.bottom + spacing;
                if (top + menuHeight > window.innerHeight - viewportPadding) {
                    top = toggleRect.top - menuHeight - spacing;
                }
                if (top < viewportPadding) {
                    top = Math.max(viewportPadding, window.innerHeight - menuHeight - viewportPadding);
                }

                var left = toggleRect.right - menuWidth;
                if (left + menuWidth > window.innerWidth - viewportPadding) {
                    left = window.innerWidth - menuWidth - viewportPadding;
                }
                if (left < viewportPadding) {
                    left = viewportPadding;
                }

                menu.style.left = left + 'px';
                menu.style.top = top + 'px';
            };

            var closeAllMenus = function() {
                var openMenus = document.querySelectorAll('.user-actions-menu.show');
                openMenus.forEach(function(menu) {
                    menu.classList.remove('show');
                    menu.classList.remove('menu-dropup');
                    menu.classList.remove('menu-floating');
                    menu.style.left = '';
                    menu.style.top = '';
                    var toggle = menu.parentElement ? menu.parentElement.querySelector('.user-actions-toggle') : null;
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            };

            toggles.forEach(function(toggle) {
                if (toggle.getAttribute('data-dropdown-bound') === '1') {
                    return;
                }
                toggle.setAttribute('data-dropdown-bound', '1');

                toggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    var wrapper = toggle.closest('.dropdown');
                    var menu = wrapper ? wrapper.querySelector('.user-actions-menu') : null;
                    if (!menu) {
                        return;
                    }

                    var isOpen = menu.classList.contains('show');
                    closeAllMenus();
                    if (!isOpen) {
                        menu.classList.add('show');
                        menu.classList.add('menu-floating');
                        toggle.setAttribute('aria-expanded', 'true');
                        positionMenu(toggle, menu);
                    } else {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            });

            if (!globalListenersBound) {
                document.body.setAttribute('data-user-dropdown-global-bound', '1');

                document.addEventListener('click', function(event) {
                    if (!event.target.closest('.dropdown')) {
                        closeAllMenus();
                    }
                });

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeAllMenus();
                    }
                });

                window.addEventListener('resize', closeAllMenus);
                document.addEventListener('scroll', function() {
                    var openMenu = document.querySelector('.user-actions-menu.show');
                    if (openMenu) {
                        closeAllMenus();
                    }
                }, true);
            }
        };

        var initAdminProfileDropdown = function() {
            var profileToggle = document.querySelector('.admin-profile-btn');
            if (!profileToggle || profileToggle.getAttribute('data-admin-profile-bound') === '1') {
                return;
            }
            var wrapper = profileToggle.closest('.dropdown');
            var profileMenu = wrapper ? wrapper.querySelector('.admin-dropdown-menu') : null;
            if (!profileMenu) {
                return;
            }

            profileToggle.setAttribute('data-admin-profile-bound', '1');
            profileMenu.setAttribute('data-admin-profile-menu', 'true');

            var closeProfileMenu = function() {
                profileMenu.classList.remove('show');
                profileToggle.setAttribute('aria-expanded', 'false');
            };
            var openProfileMenu = function() {
                profileMenu.classList.add('show');
                profileToggle.setAttribute('aria-expanded', 'true');
            };

            profileToggle.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                if (profileMenu.classList.contains('show')) {
                    closeProfileMenu();
                } else {
                    openProfileMenu();
                }
            });

            document.addEventListener('click', function(event) {
                if (event.target.closest('.admin-profile-btn') || event.target.closest('[data-admin-profile-menu="true"]')) {
                    return;
                }
                closeProfileMenu();
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeProfileMenu();
                }
            });
        };

        var initAdminNotificationBox = function() {
            var notifyToggle = document.getElementById('adminNotificationToggle');
            var notifyMenu = document.getElementById('adminNotificationMenu');
            var notifyList = document.getElementById('adminNotificationList');
            var notifyCount = document.getElementById('adminNotifyCount');
            if (!notifyToggle || !notifyMenu || !notifyList || notifyToggle.getAttribute('data-admin-notification-bound') === '1') {
                return;
            }
            if (typeof window.fetch !== 'function') {
                return;
            }
            notifyToggle.setAttribute('data-admin-notification-bound', '1');

            var setBadge = function(countValue) {
                var total = Math.max(0, parseInt(countValue, 10) || 0);
                if (!notifyCount) {
                    return;
                }
                if (total > 0) {
                    notifyCount.textContent = total > 99 ? '99+' : String(total);
                    notifyCount.classList.remove('d-none');
                } else {
                    notifyCount.textContent = '0';
                    notifyCount.classList.add('d-none');
                }
            };

            var renderEmpty = function(message) {
                notifyList.innerHTML = '';
                var emptyNode = document.createElement('div');
                emptyNode.className = 'admin-notification-empty';
                emptyNode.textContent = message;
                notifyList.appendChild(emptyNode);
            };

            var renderItems = function(items) {
                notifyList.innerHTML = '';
                if (!Array.isArray(items) || items.length === 0) {
                    renderEmpty('Hiện chưa có thông báo mới.');
                    return;
                }

                items.forEach(function(item) {
                    var isLink = item && typeof item.href === 'string' && item.href !== '';
                    var row = document.createElement(isLink ? 'a' : 'div');
                    var isNew = !!(item && item.is_new);
                    row.className = 'admin-notification-item ' + (isNew ? 'admin-notification-item-new' : 'admin-notification-item-read');
                    if (isLink) {
                        row.href = item.href;
                    }

                    var icon = document.createElement('span');
                    icon.className = 'admin-notification-icon';
                    var iconInner = document.createElement('i');
                    iconInner.className = item && item.icon ? item.icon : 'ti-bell';
                    icon.appendChild(iconInner);

                    var content = document.createElement('div');
                    content.className = 'admin-notification-content';

                    var title = document.createElement('strong');
                    title.textContent = item && item.title ? item.title : 'Thông báo';
                    content.appendChild(title);

                    var description = document.createElement('p');
                    description.textContent = item && item.message ? item.message : '';
                    content.appendChild(description);

                    var timestamp = document.createElement('small');
                    timestamp.className = 'admin-notification-time';
                    timestamp.textContent = item && item.created_at_label ? item.created_at_label : '';
                    content.appendChild(timestamp);

                    row.appendChild(icon);
                    row.appendChild(content);
                    notifyList.appendChild(row);
                });
            };

            var fetchNotifications = function(showLoading) {
                if (showLoading) {
                    renderEmpty('Đang tải thông báo...');
                }

                var endpoint = (window.URLROOT || '') + '/admin/notifications?ajax=1&_ts=' + Date.now();
                return window.fetch(endpoint, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                }).then(function(response) {
                    return response.json().then(function(payload) {
                        return {
                            ok: response.ok,
                            payload: payload
                        };
                    });
                }).then(function(result) {
                    if (!result.ok || !result.payload || !result.payload.success) {
                        throw new Error('Không thể tải thông báo.');
                    }

                    var unseen = parseInt(result.payload.unseen_count, 10) || 0;
                    setBadge(unseen);
                    renderItems(result.payload.items || []);
                    return unseen;
                }).catch(function() {
                    renderEmpty('Không thể tải thông báo. Vui lòng thử lại.');
                    return 0;
                });
            };

            var markSeen = function() {
                return window.fetch((window.URLROOT || '') + '/admin/markNotificationsSeen?ajax=1', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-Token': window.adminCsrfToken || ''
                    }
                }).then(function() {
                    setBadge(0);
                }).catch(function() {
                    return null;
                });
            };

            var closeMenu = function() {
                notifyMenu.classList.remove('show');
                notifyMenu.setAttribute('aria-hidden', 'true');
                notifyToggle.setAttribute('aria-expanded', 'false');
            };

            var openMenu = function() {
                notifyMenu.classList.add('show');
                notifyMenu.setAttribute('aria-hidden', 'false');
                notifyToggle.setAttribute('aria-expanded', 'true');
                fetchNotifications(true).then(function(unseen) {
                    if (unseen > 0) {
                        markSeen();
                    }
                });
            };

            notifyToggle.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                if (notifyMenu.classList.contains('show')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            document.addEventListener('click', function(event) {
                if (event.target.closest('#adminNotificationToggle') || event.target.closest('#adminNotificationMenu')) {
                    return;
                }
                closeMenu();
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });

            fetchNotifications(false);
            window.setInterval(function() {
                fetchNotifications(false);
            }, 45000);
        };

        var initTicketDetailSelection = function() {
            var detailContainer = document.getElementById('ticketDetailContainer');
            if (!detailContainer || document.body.getAttribute('data-ticket-delegate-bound') === '1') {
                return;
            }
            document.body.setAttribute('data-ticket-delegate-bound', '1');
            var activeTicketController = null;

            var setActiveTicketRow = function(activeLink) {
                var ticketRows = document.querySelectorAll('.ticket-row');
                ticketRows.forEach(function(row) {
                    row.classList.remove('ticket-row-active');
                });
                var selectedRow = activeLink ? activeLink.closest('.ticket-row') : null;
                if (selectedRow) {
                    selectedRow.classList.add('ticket-row-active');
                }
            };

            var cleanQueryString = function() {
                var params = new URLSearchParams(window.location.search || '');
                params.delete('url');
                params.delete('user_id');
                params.delete('contact_id');
                params.delete('ajax');
                params.delete('_ts');
                return params.toString();
            };

            document.addEventListener('click', function(event) {
                var link = event.target.closest('[data-ticket-select="true"]');
                if (!link) {
                    return;
                }
                if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                    return;
                }
                event.preventDefault();

                var userId = link.getAttribute('data-ticket-user-id');
                var contactId = link.getAttribute('data-ticket-contact-id');
                if (!userId || !contactId || typeof window.fetch !== 'function') {
                    window.location.href = link.href;
                    return;
                }

                if (activeTicketController) {
                    activeTicketController.abort();
                }
                if (typeof AbortController !== 'undefined') {
                    activeTicketController = new AbortController();
                } else {
                    activeTicketController = null;
                }

                detailContainer.classList.add('ticket-loading');
                detailContainer.setAttribute('aria-busy', 'true');

                var query = cleanQueryString();
                var detailUrl = (window.URLROOT || '') + '/admincontacts/detail/' + encodeURIComponent(userId) + '/' + encodeURIComponent(contactId);
                var requestQuery = 'ajax=1&_ts=' + Date.now();
                if (query !== '') {
                    requestQuery += '&' + query;
                }
                detailUrl += '?' + requestQuery;

                window.fetch(detailUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    cache: 'no-store',
                    signal: activeTicketController ? activeTicketController.signal : undefined
                }).then(function(response) {
                    return response.text().then(function(text) {
                        return {
                            ok: response.ok,
                            text: text,
                            redirected: response.redirected
                        };
                    });
                }).then(function(result) {
                    var payload = null;
                    var rawText = String(result.text || '').trim();
                    if (rawText !== '') {
                        try {
                            payload = JSON.parse(rawText);
                        } catch (parseError) {
                            var firstBrace = rawText.indexOf('{');
                            var lastBrace = rawText.lastIndexOf('}');
                            if (firstBrace !== -1 && lastBrace > firstBrace) {
                                try {
                                    payload = JSON.parse(rawText.substring(firstBrace, lastBrace + 1));
                                } catch (secondParseError) {
                                    payload = null;
                                }
                            }
                        }
                    }

                    if (!payload && result.redirected) {
                        window.location.href = link.href;
                        return;
                    }

                    if (!result.ok || !payload || !payload.success || !payload.html) {
                        throw new Error((payload && payload.message) ? payload.message : 'Không thể tải chi tiết ticket.');
                    }

                    detailContainer.innerHTML = payload.html;
                    setActiveTicketRow(link);
                    initAdminAutoSaveForms(detailContainer);
                    initAdminCustomSelects(detailContainer);
                    if (window.history && window.history.replaceState) {
                        var nextUrl = link.href;
                        if (typeof payload.query_string === 'string') {
                            var baseAdminContactsUrl = (window.URLROOT || '') + '/admincontacts';
                            nextUrl = payload.query_string ? (baseAdminContactsUrl + '?' + payload.query_string) : baseAdminContactsUrl;
                        }
                        window.history.replaceState(null, '', nextUrl);
                    }
                }).catch(function(error) {
                    if (error && error.name === 'AbortError') {
                        return;
                    }
                    showAdminToast('Không thể tải nhanh ticket. Vui lòng thử lại.', 'error');
                }).finally(function() {
                    detailContainer.classList.remove('ticket-loading');
                    detailContainer.removeAttribute('aria-busy');
                });
            });
        };

        if (filterSelect) {
            renderRevenueChart(filterSelect.value);
            filterSelect.addEventListener('change', function() {
                renderRevenueChart(filterSelect.value);
            });
        } else {
            renderRevenueChart(5);
        }

        initAdminAutoSaveForms();
        initAdminCustomSelects();
        initAdminGlobalSearch();
        initBrandingUploadZone();
        initHeroBgUploadZone();
        initMapPreview();
        initUserActionDropdowns();
        initAdminNotificationBox();
        initAdminLoginNotificationToast();
        initAdminProfileDropdown();
        initTicketDetailSelection();
        initResetPasswordModal();
    }

    function initAuthForms() {
        // Shared helpers
        function setError(input, span, msg) {
            if (span) { span.textContent = msg; }
            if (input) {
                input.classList.add('border-red-500', 'bg-red-500/5');
                input.classList.remove('border-gray-700');
            }
        }
        function clearError(input, span) {
            if (span) { span.textContent = ''; }
            if (input) {
                input.classList.remove('border-red-500', 'bg-red-500/5');
            }
        }

        // Shared rule functions
        var RULES = {
            username: function(val, input, span) {
                if (!val) {
                    setError(input, span, 'Vui lòng nhập tên đăng nhập.'); return false;
                }
                if (val.length < 3 || val.length > 50) {
                    setError(input, span, 'Tên đăng nhập phải từ 3 đến 50 ký tự.'); return false;
                }
                if (!/^[a-zA-Z0-9$@_!]+$/.test(val)) {
                    setError(input, span, 'Tên đăng nhập chỉ dùng chữ cái, số và ký tự $ @ _ !'); return false;
                }
                return true;
            },
            fullName: function(val, input, span) {
                if (!val) { return true; } // optional
                if (val.length > 100) {
                    setError(input, span, 'Tên hiển thị tối đa 100 ký tự.'); return false;
                }
                if (!/^[a-zA-ZÀ-ỹ\s]+$/.test(val)) {
                    setError(input, span, 'Tên hiển thị chỉ được chứa chữ cái và khoảng cách.'); return false;
                }
                return true;
            },
            email: function(val, input, span) {
                if (!val) {
                    setError(input, span, 'Vui lòng nhập email.'); return false;
                }
                if (!/^[a-zA-Z0-9@.]+$/.test(val)) {
                    setError(input, span, 'Email chỉ được chứa chữ cái, số, @ và dấu chấm.'); return false;
                }
                if (!/^[^@]+@[^@]+\.[^@]+$/.test(val)) {
                    setError(input, span, 'Email không hợp lệ.'); return false;
                }
                return true;
            },
            password: function(val, input, span, label) {
                label = label || 'Mật khẩu';
                if (!val) {
                    setError(input, span, 'Vui lòng nhập ' + label.toLowerCase() + '.'); return false;
                }
                if (val.length < 6) {
                    setError(input, span, label + ' phải có ít nhất 6 ký tự.'); return false;
                }
                return true;
            },
            confirmPassword: function(pw, cpw, input, span) {
                if (!cpw) {
                    setError(input, span, 'Vui lòng xác nhận mật khẩu.'); return false;
                }
                if (pw !== cpw) {
                    setError(input, span, 'Mật khẩu xác nhận không khớp.'); return false;
                }
                return true;
            }
        };

        // ── Login ────────────────────────────────────────
        var loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                var uInput = document.getElementById('username');
                var pInput = document.getElementById('password');
                var uErr   = document.getElementById('login-username-err');
                var pErr   = document.getElementById('login-password-err');
                clearError(uInput, uErr); clearError(pInput, pErr);
                var valid = true;
                if (!uInput || !uInput.value.trim()) { setError(uInput, uErr, 'Vui lòng nhập tên đăng nhập.'); valid = false; }
                if (!pInput || !pInput.value)         { setError(pInput, pErr, 'Vui lòng nhập mật khẩu.');      valid = false; }
                if (!valid) { e.preventDefault(); }
            });
        }

        // ── Register ─────────────────────────────────────
        var registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                var f = {
                    username:        document.getElementById('reg-username'),
                    fullName:        document.getElementById('reg-full-name'),
                    email:           document.getElementById('reg-email'),
                    password:        document.getElementById('reg-password'),
                    confirmPassword: document.getElementById('reg-confirm-password')
                };
                var s = {
                    username:        document.getElementById('reg-username-err'),
                    fullName:        document.getElementById('reg-full-name-err'),
                    email:           document.getElementById('reg-email-err'),
                    password:        document.getElementById('reg-password-err'),
                    confirmPassword: document.getElementById('reg-confirm-password-err')
                };
                Object.keys(f).forEach(function(k) { clearError(f[k], s[k]); });
                var valid = true;
                var pw = f.password ? f.password.value : '';
                if (!RULES.username(f.username ? f.username.value.trim() : '', f.username, s.username))               { valid = false; }
                if (!RULES.fullName(f.fullName ? f.fullName.value.trim() : '', f.fullName, s.fullName))               { valid = false; }
                if (!RULES.email(f.email ? f.email.value.trim() : '', f.email, s.email))                             { valid = false; }
                if (!RULES.password(pw, f.password, s.password, 'Mật khẩu'))                                         { valid = false; }
                if (!RULES.confirmPassword(pw, f.confirmPassword ? f.confirmPassword.value : '', f.confirmPassword, s.confirmPassword)) { valid = false; }
                if (!valid) { e.preventDefault(); }
            });
        }

        // ── Profile: Personal Information ─────────────────
        var profileInfoForm = document.getElementById('profileInfoForm');
        if (profileInfoForm) {
            profileInfoForm.addEventListener('submit', function(e) {
                var fnInput = document.getElementById('prof-full-name');
                var emInput = document.getElementById('prof-email');
                var fnErr   = document.getElementById('prof-full-name-err');
                var emErr   = document.getElementById('prof-email-err');
                clearError(fnInput, fnErr); clearError(emInput, emErr);
                var valid = true;
                if (!RULES.fullName(fnInput ? fnInput.value.trim() : '', fnInput, fnErr)) { valid = false; }
                if (!RULES.email(emInput ? emInput.value.trim() : '', emInput, emErr))    { valid = false; }
                if (!valid) { e.preventDefault(); }
            });
        }

        // ── Profile: Change Password ──────────────────────
        var changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
                var curInput = document.getElementById('prof-current-password');
                var newInput = document.getElementById('prof-new-password');
                var cfmInput = document.getElementById('prof-confirm-password');
                var curErr   = document.getElementById('prof-current-password-err');
                var newErr   = document.getElementById('prof-new-password-err');
                var cfmErr   = document.getElementById('prof-confirm-password-err');
                clearError(curInput, curErr); clearError(newInput, newErr); clearError(cfmInput, cfmErr);
                var valid = true;
                var npw = newInput ? newInput.value : '';
                if (!curInput || !curInput.value) { setError(curInput, curErr, 'Vui lòng nhập mật khẩu hiện tại.'); valid = false; }
                if (!RULES.password(npw, newInput, newErr, 'Mật khẩu mới'))                                      { valid = false; }
                if (!RULES.confirmPassword(npw, cfmInput ? cfmInput.value : '', cfmInput, cfmErr))                { valid = false; }
                if (!valid) { e.preventDefault(); }
            });
        }
    }

    function initResetPasswordModal() {
        var overlay = document.getElementById('resetPasswordModal');
        var confirmBtn = document.getElementById('resetPasswordConfirm');
        var cancelBtn = document.getElementById('resetPasswordCancel');
        var userNameEl = document.getElementById('resetPasswordUserName');
        if (!overlay || !confirmBtn || !cancelBtn) {
            return;
        }

        var targetUserId = null;

        var closeModal = function() {
            overlay.classList.remove('is-active');
            overlay.setAttribute('aria-hidden', 'true');
            targetUserId = null;
        };

        var openModal = function(userId, userName) {
            targetUserId = userId;
            if (userNameEl) {
                userNameEl.textContent = userName ? 'Người dùng: ' + userName : '';
            }
            overlay.classList.add('is-active');
            overlay.setAttribute('aria-hidden', 'false');
        };

        document.querySelectorAll('[data-reset-user-id]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                openModal(
                    btn.getAttribute('data-reset-user-id'),
                    btn.getAttribute('data-reset-user-name')
                );
            });
        });

        cancelBtn.addEventListener('click', closeModal);

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        confirmBtn.addEventListener('click', function() {
            if (!targetUserId) {
                return;
            }
            confirmBtn.disabled = true;
            fetch(window.URLROOT + '/admin/resetPassword/' + targetUserId, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': window.adminCsrfToken || ''
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(result) {
                closeModal();
                showAdminToast(result.message || 'Reset mật khẩu thành công!', result.success ? 'success' : 'error');
            })
            .catch(function() {
                closeModal();
                showAdminToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            })
            .finally(function() {
                confirmBtn.disabled = false;
            });
        });
    }

    var homeProductDeck = document.getElementById('home-product-deck');
    if (homeProductDeck) {
        var homeCards = homeProductDeck.querySelectorAll('[data-home-product-card]');
        var reducedMotionHp = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var pingEls = homeProductDeck.querySelectorAll('[data-home-product-ping]');

        var tickHomePing = function() {
            pingEls.forEach(function(el) {
                var base = parseInt(el.getAttribute('data-ping-base') || '12', 10);
                var jitter = Math.round((Math.random() - 0.5) * 5);
                el.textContent = Math.max(8, base + jitter) + ' ms';
            });
        };
        if (pingEls.length) {
            tickHomePing();
            if (!reducedMotionHp) {
                window.setInterval(tickHomePing, 700);
            }
        }

        var bwFineHover = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        var bwPop = null;
        var hideHomeBandwidthPop = function() {
            if (bwPop) {
                bwPop.classList.remove('is-visible');
            }
        };
        var escapeHomeBwHtml = function(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        };
        var showHomeBandwidthPop = function(clientX, clientY, bwText, accent) {
            if (!bwPop) {
                return;
            }
            bwPop.innerHTML =
                '<strong>Băng thông gói</strong>' +
                '<span>' + escapeHomeBwHtml(bwText) + '</span>';
            if (accent) {
                bwPop.style.setProperty('--bw-pop-accent', accent);
            }
            bwPop.classList.add('is-visible');
            var tipRect = bwPop.getBoundingClientRect();
            var left = clientX - tipRect.width / 2;
            var top = clientY - tipRect.height - 14;
            left = Math.max(10, Math.min(left, window.innerWidth - tipRect.width - 10));
            if (top < 10) {
                top = clientY + 14;
            }
            if (top + tipRect.height > window.innerHeight - 10) {
                top = window.innerHeight - tipRect.height - 10;
            }
            bwPop.style.left = Math.round(left) + 'px';
            bwPop.style.top = Math.round(top) + 'px';
        };

        if (bwFineHover && homeCards.length) {
            bwPop = document.getElementById('home-product-bandwidth-pop');
            if (!bwPop) {
                bwPop = document.createElement('div');
                bwPop.id = 'home-product-bandwidth-pop';
                bwPop.className = 'home-product-bandwidth-pop';
                bwPop.setAttribute('role', 'tooltip');
                document.body.appendChild(bwPop);
            }
            var readCardAccent = function(card) {
                try {
                    return window.getComputedStyle(card).getPropertyValue('--rarity-accent').trim() || '#22d3ee';
                } catch (err) {
                    return '#22d3ee';
                }
            };
            homeCards.forEach(function(card) {
                var bwText = card.getAttribute('data-home-bandwidth') || '';
                var onBwPointer = function(evt) {
                    if (!evt.isPrimary) {
                        return;
                    }
                    showHomeBandwidthPop(evt.clientX, evt.clientY, bwText, readCardAccent(card));
                };
                card.addEventListener('pointerenter', onBwPointer);
                card.addEventListener('pointermove', onBwPointer);
                card.addEventListener('pointerleave', hideHomeBandwidthPop);
                card.addEventListener('pointercancel', hideHomeBandwidthPop);
            });
            document.addEventListener(
                'scroll',
                function() {
                    if (bwPop && bwPop.classList.contains('is-visible')) {
                        hideHomeBandwidthPop();
                    }
                },
                true
            );
        }

        if (!reducedMotionHp && homeCards.length) {
            homeCards.forEach(function(card) {
                var surface = card.querySelector('.home-product-card__surface');
                if (!surface) {
                    return;
                }
                var onMove = function(e) {
                    if (!e.isPrimary) {
                        return;
                    }
                    var rect = card.getBoundingClientRect();
                    var x = e.clientX - rect.left;
                    var y = e.clientY - rect.top;
                    var px = Math.max(0, Math.min(1, x / rect.width));
                    var py = Math.max(0, Math.min(1, y / rect.height));
                    card.style.setProperty('--pointer-x', (px * 100).toFixed(2) + '%');
                    card.style.setProperty('--pointer-y', (py * 100).toFixed(2) + '%');
                    var tiltX = (py - 0.5) * -9;
                    var tiltY = (px - 0.5) * 9;
                    surface.style.transform =
                        'perspective(820px) rotateX(' + tiltX + 'deg) rotateY(' + tiltY + 'deg) translateZ(0)';
                };
                var onLeave = function() {
                    surface.style.transform = '';
                    card.style.setProperty('--pointer-x', '50%');
                    card.style.setProperty('--pointer-y', '42%');
                };
                card.addEventListener('pointermove', onMove);
                card.addEventListener('pointerleave', onLeave);
                card.addEventListener('pointercancel', onLeave);
            });
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCloudArenaUi);
} else {
    initCloudArenaUi();
}
