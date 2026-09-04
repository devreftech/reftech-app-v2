/**
 * Main
 */

'use strict';

let isRtl = window.Helpers.isRtl(),
  isDarkStyle = window.Helpers.isDarkStyle(),
  menu,
  animate,
  isHorizontalLayout = false;

if (document.getElementById('layout-menu')) {
  isHorizontalLayout = document.getElementById('layout-menu').classList.contains('menu-horizontal');
}

(function () {
  // Button & Pagination Waves effect
  if (typeof Waves !== 'undefined') {
    Waves.init();
    Waves.attach(
      ".btn[class*='btn-']:not(.position-relative):not([class*='btn-outline-']):not([class*='btn-label-'])",
      ['waves-light']
    );
    Waves.attach("[class*='btn-outline-']:not(.position-relative)");
    Waves.attach("[class*='btn-label-']:not(.position-relative)");
    Waves.attach('.pagination .page-item .page-link');
    Waves.attach('.dropdown-menu .dropdown-item');
    Waves.attach('.light-style .list-group .list-group-item-action');
    Waves.attach('.dark-style .list-group .list-group-item-action', ['waves-light']);
    Waves.attach('.nav-tabs:not(.nav-tabs-widget) .nav-item .nav-link');
    Waves.attach('.nav-pills .nav-item .nav-link', ['waves-light']);
    Waves.attach('.menu-vertical .menu-item .menu-link.menu-toggle');
  }

  // Window scroll function for navbar
  function onScroll() {
    var layoutPage = document.querySelector('.layout-page');
    if (layoutPage) {
      if (window.pageYOffset > 0) {
        layoutPage.classList.add('window-scrolled');
      } else {
        layoutPage.classList.remove('window-scrolled');
      }
    }
  }
  // On load time out
  setTimeout(() => {
    onScroll();
  }, 200);

  // On window scroll
  window.onscroll = function () {
    onScroll();
  };

  // Initialize menu
  //-----------------

  let layoutMenuEl = document.querySelectorAll('#layout-menu');
  layoutMenuEl.forEach(function (element) {
    menu = new Menu(element, {
      orientation: isHorizontalLayout ? 'horizontal' : 'vertical',
      closeChildren: isHorizontalLayout ? true : false,
      // ? This option only works with Horizontal menu
      showDropdownOnHover: localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover') // If value(showDropdownOnHover) is set in local storage
        ? localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover') === 'true' // Use the local storage value
        : window.templateCustomizer !== undefined // If value is set in config.js
        ? window.templateCustomizer.settings.defaultShowDropdownOnHover // Use the config.js value
        : true // Use this if you are not using the config.js and want to set value directly from here
    });
    // Change parameter to true if you want scroll animation
    window.Helpers.scrollToActive((animate = false));
    window.Helpers.mainMenu = menu;
  });

  // Initialize menu togglers and bind click on each
  let menuToggler = document.querySelectorAll('.layout-menu-toggle');
  menuToggler.forEach(item => {
    item.addEventListener('click', event => {
      event.preventDefault();
      window.Helpers.toggleCollapsed();
      // Enable menu state with local storage support if enableMenuLocalStorage = true from config.js
      if (config.enableMenuLocalStorage && !window.Helpers.isSmallScreen()) {
        try {
          localStorage.setItem(
            'templateCustomizer-' + templateName + '--LayoutCollapsed',
            String(window.Helpers.isCollapsed())
          );
        } catch (e) {}
      }
    });
  });

  // Menu swipe gesture

  // Detect swipe gesture on the target element and call swipe In
  window.Helpers.swipeIn('.drag-target', function (e) {
    window.Helpers.setCollapsed(false);
  });

  // Detect swipe gesture on the target element and call swipe Out
  window.Helpers.swipeOut('#layout-menu', function (e) {
    if (window.Helpers.isSmallScreen()) window.Helpers.setCollapsed(true);
  });

  // Display in main menu when menu scrolls
  let menuInnerContainer = document.getElementsByClassName('menu-inner'),
    menuInnerShadow = document.getElementsByClassName('menu-inner-shadow')[0];
  if (menuInnerContainer.length > 0 && menuInnerShadow) {
    menuInnerContainer[0].addEventListener('ps-scroll-y', function () {
      if (this.querySelector('.ps__thumb-y').offsetTop) {
        menuInnerShadow.style.display = 'block';
      } else {
        menuInnerShadow.style.display = 'none';
      }
    });
  }

  // Style Switcher (Light/Dark Mode)
  //---------------------------------

  let styleSwitcherToggleEl = document.querySelector('.style-switcher-toggle');
  if (window.templateCustomizer) {
    // setStyle light/dark on click of styleSwitcherToggleEl
    if (styleSwitcherToggleEl) {
      styleSwitcherToggleEl.addEventListener('click', function () {
        if (window.Helpers.isLightStyle()) {
          window.templateCustomizer.setStyle('dark');
        } else {
          window.templateCustomizer.setStyle('light');
        }
      });
    }
    // Update style switcher icon and tooltip based on current style
    if (window.Helpers.isLightStyle()) {
      if (styleSwitcherToggleEl) {
        styleSwitcherToggleEl.querySelector('i').classList.add('mdi-weather-night');
        new bootstrap.Tooltip(styleSwitcherToggleEl, {
          title: 'Dark mode',
          fallbackPlacements: ['bottom']
        });
      }
      switchImage('light');
    } else {
      if (styleSwitcherToggleEl) {
        styleSwitcherToggleEl.querySelector('i').classList.add('mdi-weather-sunny');
        new bootstrap.Tooltip(styleSwitcherToggleEl, {
          title: 'Light mode',
          fallbackPlacements: ['bottom']
        });
      }
      switchImage('dark');
    }
  } else {
    // Removed style switcher element if not using template customizer
    styleSwitcherToggleEl.parentElement.remove();
  }

  // Update light/dark image based on current style
  function switchImage(style) {
    const switchImagesList = [].slice.call(document.querySelectorAll('[data-app-' + style + '-img]'));
    switchImagesList.map(function (imageEl) {
      const setImage = imageEl.getAttribute('data-app-' + style + '-img');
      imageEl.src = assetsPath + 'img/' + setImage; // Using window.assetsPath to get the exact relative path
    });
  }

  // Internationalization (Language Dropdown)
  // ---------------------------------------

  if (typeof i18next !== 'undefined' && typeof i18NextHttpBackend !== 'undefined') {
    i18next
      .use(i18NextHttpBackend)
      .init({
        lng: 'en',
        debug: false,
        fallbackLng: 'en',
        backend: {
          loadPath: assetsPath + 'json/locales/{{lng}}.json'
        },
        returnObjects: true
      })
      .then(function (t) {
        localize();
      });
  }

  let languageDropdown = document.getElementsByClassName('dropdown-language');

  if (languageDropdown.length) {
    let dropdownItems = languageDropdown[0].querySelectorAll('.dropdown-item');

    for (let i = 0; i < dropdownItems.length; i++) {
      dropdownItems[i].addEventListener('click', function () {
        let currentLanguage = this.getAttribute('data-language');

        for (let sibling of this.parentNode.children) {
          var siblingEle = sibling.parentElement.parentNode.firstChild;

          // Loop through each sibling and push to the array
          while (siblingEle) {
            if (siblingEle.nodeType === 1 && siblingEle !== siblingEle.parentElement) {
              siblingEle.querySelector('.dropdown-item').classList.remove('active');
            }
            siblingEle = siblingEle.nextSibling;
          }
        }
        this.classList.add('active');

        i18next.changeLanguage(currentLanguage, (err, t) => {
          if (err) return console.log('something went wrong loading', err);
          localize();
        });
      });
    }
  }

  function localize() {
    let i18nList = document.querySelectorAll('[data-i18n]');
    // Set the current language in dd
    let currentLanguageEle = document.querySelector('.dropdown-item[data-language="' + i18next.language + '"]');

    if (currentLanguageEle) {
      currentLanguageEle.click();
    }

    i18nList.forEach(function (item) {
      item.innerHTML = i18next.t(item.dataset.i18n);
    });
  }

  // Notification
  // ------------
  const notificationMarkAsReadAll = document.querySelector('.dropdown-notifications-all');
  const notificationMarkAsReadList = document.querySelectorAll('.dropdown-notifications-read');

  // Notification: Mark as all as read
  if (notificationMarkAsReadAll) {
    notificationMarkAsReadAll.addEventListener('click', event => {
      notificationMarkAsReadList.forEach(item => {
        item.closest('.dropdown-notifications-item').classList.add('marked-as-read');
      });
    });
  }
  // Notification: Mark as read/unread onclick of dot
  if (notificationMarkAsReadList) {
    notificationMarkAsReadList.forEach(item => {
      item.addEventListener('click', event => {
        item.closest('.dropdown-notifications-item').classList.toggle('marked-as-read');
      });
    });
  }

  // Notification: Mark as read/unread onclick of dot
  const notificationArchiveMessageList = document.querySelectorAll('.dropdown-notifications-archive');
  notificationArchiveMessageList.forEach(item => {
    item.addEventListener('click', event => {
      item.closest('.dropdown-notifications-item').remove();
    });
  });

  // Init helpers & misc
  // --------------------

  // Init BS Tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Accordion active class
  const accordionActiveFunction = function (e) {
    if (e.type == 'show.bs.collapse' || e.type == 'show.bs.collapse') {
      e.target.closest('.accordion-item').classList.add('active');
    } else {
      e.target.closest('.accordion-item').classList.remove('active');
    }
  };

  const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
  const accordionList = accordionTriggerList.map(function (accordionTriggerEl) {
    accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
    accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
  });

  // If layout is RTL add .dropdown-menu-end class to .dropdown-menu
  if (isRtl) {
    Helpers._addClass('dropdown-menu-end', document.querySelectorAll('#layout-navbar .dropdown-menu'));
  }

  // Auto update layout based on screen size
  window.Helpers.setAutoUpdate(true);

  // Toggle Password Visibility
  window.Helpers.initPasswordToggle();

  // Speech To Text
  window.Helpers.initSpeechToText();

  // Nav tabs animation
  window.Helpers.navTabsAnimation();

  // Init PerfectScrollbar in Navbar Dropdown (i.e notification)
  window.Helpers.initNavbarDropdownScrollbar();

  // On window resize listener
  // -------------------------
  window.addEventListener(
    'resize',
    function (event) {
      // Hide open search input and set value blank
      if (window.innerWidth >= window.Helpers.LAYOUT_BREAKPOINT) {
        if (document.querySelector('.search-input-wrapper')) {
          document.querySelector('.search-input-wrapper').classList.add('d-none');
          document.querySelector('.search-input').value = '';
        }
      }
      // Horizontal Layout : Update menu based on window size
      let horizontalMenuTemplate = document.querySelector("[data-template^='horizontal-menu']");
      if (horizontalMenuTemplate) {
        setTimeout(function () {
          if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
            if (document.getElementById('layout-menu')) {
              if (document.getElementById('layout-menu').classList.contains('menu-horizontal')) {
                menu.switchMenu('vertical');
              }
            }
          } else {
            if (document.getElementById('layout-menu')) {
              if (document.getElementById('layout-menu').classList.contains('menu-vertical')) {
                menu.switchMenu('horizontal');
              }
            }
          }
        }, 100);
      }

      window.Helpers.navTabsAnimation();
    },
    true
  );

  // Manage menu expanded/collapsed with templateCustomizer & local storage
  //------------------------------------------------------------------

  // If current layout is horizontal OR current window screen is small (overlay menu) than return from here
  if (isHorizontalLayout || window.Helpers.isSmallScreen()) {
    return;
  }

  // If current layout is vertical and current window screen is > small

  // Auto update menu collapsed/expanded based on the themeConfig
  if (typeof TemplateCustomizer !== 'undefined') {
    if (window.templateCustomizer.settings.defaultMenuCollapsed) {
      window.Helpers.setCollapsed(true, false);
    }
  }

  // Manage menu expanded/collapsed state with local storage support If enableMenuLocalStorage = true in config.js
  if (typeof config !== 'undefined') {
    if (config.enableMenuLocalStorage) {
      try {
        if (
          localStorage.getItem('templateCustomizer-' + templateName + '--LayoutCollapsed') !== null &&
          localStorage.getItem('templateCustomizer-' + templateName + '--LayoutCollapsed') !== 'false'
        )
          window.Helpers.setCollapsed(
            localStorage.getItem('templateCustomizer-' + templateName + '--LayoutCollapsed') === 'true',
            false
          );
      } catch (e) {}
    }
  }
})();

// ! Removed following code if you do't wish to use jQuery. Remember that navbar search functionality will stop working on removal.
if (typeof $ !== 'undefined') {
  $(function () {
    // ! TODO: Required to load after DOM is ready, did this now with jQuery ready.
    window.Helpers.initSidebarToggle();
    // Toggle Universal Sidebar

    // Navbar Search with autosuggest (typeahead)
    // ? You can remove the following JS if you don't want to use search functionality.
    //----------------------------------------------------------------------------------

    var searchToggler = $('.search-toggler'),
      searchInputWrapper = $('.search-input-wrapper'),
      searchInput = $('.search-input'),
      contentBackdrop = $('.content-backdrop');

    // Open search input on click of search icon
    if (searchToggler.length) {
      searchToggler.on('click', function () {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      });
    }
    // Open search on 'CTRL+/'
    $(document).on('keydown', function (event) {
      let ctrlKey = event.ctrlKey,
        slashKey = event.which === 191;

      if (ctrlKey && slashKey) {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      }
    });
    // Todo: Add container-xxl to twitter-typeahead
    searchInput.on('focus', function () {
      if (searchInputWrapper.hasClass('container-xxl')) {
        searchInputWrapper.find('.twitter-typeahead').addClass('container-xxl');
      }
    });

    if (searchInput.length) {
      var cachedResults = {};
      var pendingCallbacks = {};
      var searchTimer = null;
      var activeAjax = null;

      function requestSearch(category, query, asyncResults) {
        var q = (query || '').trim();
        if (!q) {
          asyncResults([]);
          return;
        }

        // If result already cached in memory for exact query, return immediately
        if (cachedResults[q]) {
          asyncResults(cachedResults[q][category] || []);
          return;
        }

        // Register callback for this query & category
        if (!pendingCallbacks[q]) {
          pendingCallbacks[q] = [];
        }
        pendingCallbacks[q].push({ category: category, cb: asyncResults });

        // Debounce single AJAX call across all datasets
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
          if (activeAjax && activeAjax.readyState !== 4) {
            activeAjax.abort();
          }

          activeAjax = $.ajax({
            url: '/db/global-search',
            type: 'GET',
            data: { q: q },
            dataType: 'json',
            success: function (res) {
              cachedResults[q] = res || {};
              var callbacks = pendingCallbacks[q] || [];
              delete pendingCallbacks[q];
              callbacks.forEach(function (item) {
                if (typeof item.cb === 'function') {
                  item.cb((res && res[item.category]) || []);
                }
              });
            },
            error: function (xhr, status) {
              if (status !== 'abort') {
                var callbacks = pendingCallbacks[q] || [];
                delete pendingCallbacks[q];
                callbacks.forEach(function (item) {
                  if (typeof item.cb === 'function') {
                    item.cb([]);
                  }
                });
              }
            }
          });
        }, 150);
      }

      var makeSearchSource = function (category) {
        return function (q, syncResults, asyncResults) {
          var trimmed = (q || '').trim();
          if (trimmed.length === 0) {
            syncResults([]);
            return;
          }
          if (cachedResults[trimmed]) {
            syncResults(cachedResults[trimmed][category] || []);
            return;
          }
          requestSearch(category, trimmed, asyncResults);
        };
      };

      // Init typeahead on searchInput
      searchInput.each(function () {
        var $this = $(this);
        searchInput
          .typeahead(
            {
              hint: false,
              minLength: 1,
              classNames: {
                menu: 'tt-menu navbar-search-suggestion',
                cursor: 'active',
                suggestion: 'suggestion d-flex justify-content-between px-3 py-2 w-100'
              }
            },
            // Pages
            {
              name: 'pages',
              display: 'name',
              limit: 5,
              source: makeSearchSource('pages'),
              templates: {
                header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2"><i class="mdi mdi-view-grid-outline me-1"></i>Menu / Halaman</h6>',
                suggestion: function (data) {
                  return (
                    '<a href="' + (data.url || 'javascript:void(0);') + '" class="d-flex align-items-center justify-content-between w-100 text-decoration-none">' +
                    '<div>' +
                    '<i class="mdi ' + (data.icon || 'mdi-file-outline') + ' me-2 text-primary"></i>' +
                    '<span class="align-middle fw-semibold text-body">' + (data.name || '') + '</span>' +
                    '</div>' +
                    (data.category ? '<span class="badge bg-label-secondary small">' + data.category + '</span>' : '') +
                    '</a>'
                  );
                }
              }
            },
            // Clients / Customers
            {
              name: 'clients',
              display: 'name',
              limit: 5,
              source: makeSearchSource('clients'),
              templates: {
                header: '<h6 class="suggestions-header text-success mb-0 mx-3 mt-3 pb-2"><i class="mdi mdi-account-group-outline me-1"></i>Client & Customer</h6>',
                suggestion: function (data) {
                  return (
                    '<a href="' + (data.url || 'javascript:void(0);') + '" class="d-flex align-items-center justify-content-between w-100 text-decoration-none">' +
                    '<div class="d-flex align-items-center">' +
                    '<i class="mdi ' + (data.icon || 'mdi-domain') + ' me-2 text-success fs-5"></i>' +
                    '<div>' +
                    '<div class="fw-semibold text-body">' + (data.name || '') + '</div>' +
                    '<small class="text-muted">' + (data.subtitle || '') + '</small>' +
                    '</div>' +
                    '</div>' +
                    (data.badge ? '<span class="badge ' + (data.badge_class || 'bg-label-primary') + ' small">' + data.badge + '</span>' : '') +
                    '</a>'
                  );
                }
              }
            },
            // Quotations
            {
              name: 'quotations',
              display: 'name',
              limit: 5,
              source: makeSearchSource('quotations'),
              templates: {
                header: '<h6 class="suggestions-header text-info mb-0 mx-3 mt-3 pb-2"><i class="mdi mdi-file-document-outline me-1"></i>Quotation</h6>',
                suggestion: function (data) {
                  return (
                    '<a href="' + (data.url || 'javascript:void(0);') + '" class="d-flex align-items-center justify-content-between w-100 text-decoration-none">' +
                    '<div class="d-flex align-items-center">' +
                    '<i class="mdi ' + (data.icon || 'mdi-file-document-outline') + ' me-2 text-info fs-5"></i>' +
                    '<div>' +
                    '<div class="fw-semibold text-body">' + (data.name || '') + '</div>' +
                    '<small class="text-muted text-truncate d-inline-block" style="max-width:320px;">' + (data.subtitle || '') + '</small>' +
                    '</div>' +
                    '</div>' +
                    (data.badge ? '<span class="badge ' + (data.badge_class || 'bg-label-info') + ' small">' + data.badge + '</span>' : '') +
                    '</a>'
                  );
                }
              }
            },
            // Invoices
            {
              name: 'invoices',
              display: 'name',
              limit: 4,
              source: makeSearchSource('invoices'),
              templates: {
                header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2"><i class="mdi mdi-receipt-text-outline me-1"></i>Invoice</h6>',
                suggestion: function (data) {
                  return (
                    '<a href="' + (data.url || 'javascript:void(0);') + '" class="d-flex align-items-center justify-content-between w-100 text-decoration-none">' +
                    '<div class="d-flex align-items-center">' +
                    '<i class="mdi ' + (data.icon || 'mdi-receipt-text-outline') + ' me-2 text-primary fs-5"></i>' +
                    '<div>' +
                    '<div class="fw-semibold text-body">' + (data.name || '') + '</div>' +
                    '<small class="text-muted">' + (data.subtitle || '') + '</small>' +
                    '</div>' +
                    '</div>' +
                    (data.badge ? '<span class="badge ' + (data.badge_class || 'bg-label-primary') + ' small">' + data.badge + '</span>' : '') +
                    '</a>'
                  );
                }
              }
            },
            // Units (Master Unit Global)
            {
              name: 'units',
              display: 'name',
              limit: 4,
              source: makeSearchSource('units'),
              templates: {
                header: '<h6 class="suggestions-header text-warning mb-0 mx-3 mt-3 pb-2"><i class="mdi mdi-cog-outline me-1"></i>Unit Master</h6>',
                suggestion: function (data) {
                  return (
                    '<a href="' + (data.url || 'javascript:void(0);') + '" class="d-flex align-items-center justify-content-between w-100 text-decoration-none">' +
                    '<div class="d-flex align-items-center">' +
                    '<i class="mdi ' + (data.icon || 'mdi-cog-outline') + ' me-2 text-warning fs-5"></i>' +
                    '<div>' +
                    '<div class="fw-semibold text-body">' + (data.name || '') + '</div>' +
                    '<small class="text-muted">' + (data.subtitle || '') + '</small>' +
                    '</div>' +
                    '</div>' +
                    (data.badge ? '<span class="badge ' + (data.badge_class || 'bg-label-secondary') + ' small">' + data.badge + '</span>' : '') +
                    '</a>'
                  );
                }
              }
            },
            // Service Reports
            {
              name: 'service_reports',
              display: 'name',
              limit: 4,
              source: makeSearchSource('service_reports'),
              templates: {
                header: '<h6 class="suggestions-header text-danger mb-0 mx-3 mt-3 pb-2"><i class="mdi mdi-wrench-outline me-1"></i>Service Reports</h6>',
                suggestion: function (data) {
                  return (
                    '<a href="' + (data.url || 'javascript:void(0);') + '" class="d-flex align-items-center justify-content-between w-100 text-decoration-none">' +
                    '<div class="d-flex align-items-center">' +
                    '<i class="mdi ' + (data.icon || 'mdi-wrench-outline') + ' me-2 text-danger fs-5"></i>' +
                    '<div>' +
                    '<div class="fw-semibold text-body">' + (data.name || '') + '</div>' +
                    '<small class="text-muted">' + (data.subtitle || '') + '</small>' +
                    '</div>' +
                    '</div>' +
                    (data.badge ? '<span class="badge ' + (data.badge_class || 'bg-label-danger') + ' small">' + data.badge + '</span>' : '') +
                    '</a>'
                  );
                }
              }
            },
            // Purchase Orders
            {
              name: 'purchase_orders',
              display: 'name',
              limit: 4,
              source: makeSearchSource('purchase_orders'),
              templates: {
                header: '<h6 class="suggestions-header text-dark mb-0 mx-3 mt-3 pb-2"><i class="mdi mdi-cart-outline me-1"></i>Purchase Orders</h6>',
                suggestion: function (data) {
                  return (
                    '<a href="' + (data.url || 'javascript:void(0);') + '" class="d-flex align-items-center justify-content-between w-100 text-decoration-none">' +
                    '<div class="d-flex align-items-center">' +
                    '<i class="mdi ' + (data.icon || 'mdi-cart-outline') + ' me-2 text-dark fs-5"></i>' +
                    '<div>' +
                    '<div class="fw-semibold text-body">' + (data.name || '') + '</div>' +
                    '<small class="text-muted">' + (data.subtitle || '') + '</small>' +
                    '</div>' +
                    '</div>' +
                    (data.badge ? '<span class="badge ' + (data.badge_class || 'bg-label-warning') + ' small">' + data.badge + '</span>' : '') +
                    '</a>'
                  );
                }
              }
            }
          )
          // On typeahead result render
          .bind('typeahead:render', function () {
            contentBackdrop.addClass('show').removeClass('fade');
          })
          // On typeahead select
          .bind('typeahead:select', function (ev, suggestion) {
            if (suggestion && suggestion.url) {
              window.location.href = suggestion.url;
            }
          })
          // On typeahead close
          .bind('typeahead:close', function () {
            searchInput.val('');
            $this.typeahead('val', '');
            searchInputWrapper.addClass('d-none');
            contentBackdrop.addClass('fade').removeClass('show');
          });

        // Quick enter support
        searchInput.on('keydown', function (e) {
          if (e.which === 13) {
            var $active = searchInputWrapper.find('.tt-suggestion.active a');
            if ($active.length && $active.attr('href')) {
              window.location.href = $active.attr('href');
            } else {
              var $first = searchInputWrapper.find('.tt-suggestion a').first();
              if ($first.length && $first.attr('href')) {
                window.location.href = $first.attr('href');
              }
            }
          }
        });

        // Fallback for direct clicks on suggestion link
        $(document).on('click', '.navbar-search-suggestion .suggestion a', function (e) {
          var href = $(this).attr('href');
          if (href && href !== 'javascript:void(0);') {
            window.location.href = href;
          }
        });

        // On searchInput keyup, Fade content backdrop if search input is blank
        searchInput.on('keyup', function () {
          if (searchInput.val() == '') {
            contentBackdrop.addClass('fade').removeClass('show');
          }
        });
      });

      // Init PerfectScrollbar in search result
      var psSearch;
      $('.navbar-search-suggestion').each(function () {
        psSearch = new PerfectScrollbar($(this)[0], {
          wheelPropagation: false,
          suppressScrollX: true
        });
      });

      searchInput.on('keyup', function () {
        if (psSearch) psSearch.update();
      });
    }
  });
}
