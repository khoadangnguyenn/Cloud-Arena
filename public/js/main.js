// Custom JavaScript for the client side
function initCloudArenaUi() {
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
                circleMarkup.push('<circle cx="' + x.toFixed(2) + '" cy="' + y.toFixed(2) + '" r="4"></circle>');
            }

            areaParts.push('L ' + xEnd.toFixed(2) + ' ' + yBottom.toFixed(2));
            areaParts.push('L ' + xStart.toFixed(2) + ' ' + yBottom.toFixed(2));
            areaParts.push('Z');

            linePath.setAttribute('d', lineParts.join(' '));
            areaPath.setAttribute('d', areaParts.join(' '));
            pointsGroup.innerHTML = circleMarkup.join('');

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
                { keywords: ['cài đặt', 'cai dat', 'setting', 'settings'], path: '/admin/settings' }
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
        initAdminGlobalSearch();
        initBrandingUploadZone();
        initMapPreview();
        initUserActionDropdowns();
        initAdminProfileDropdown();
        initTicketDetailSelection();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCloudArenaUi);
} else {
    initCloudArenaUi();
}
