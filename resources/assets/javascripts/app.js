/*
 |------------------------------------------------------------------
 | Core script to handle the entire theme and core functions
 |------------------------------------------------------------------
 */

var App = function () {
    var isRTL = false;
    var isIE8 = false;
    var isIE9 = false;
    var isIE10 = false;

    var resizeHandlers = [];

    var assetsPath = '../assets/';
    var globalImgPath = 'global/img/';
    var globalPluginsPath = 'global/plugins/';
    var globalCssPath = 'global/css/';

    /**
     * Theme layout color set
     * @type {{blue: string, red: string, green: string, purple: string, grey: string, yellow: string}}
     */
    var brandColors = {
        'blue': '#89C4F4',
        'red': '#F3565D',
        'green': '#1bbc9b',
        'purple': '#9b59b6',
        'grey': '#95a5a6',
        'yellow': '#F8CB00'
    };

    /**
     * Initializes main settings
     */
    var handleInit = function () {

        if ($('body').css('direction') === 'rtl') {
            isRTL = true;
        }

        isIE8 = !!navigator.userAgent.match(/MSIE 8.0/);
        isIE9 = !!navigator.userAgent.match(/MSIE 9.0/);
        isIE10 = !!navigator.userAgent.match(/MSIE 10.0/);

        if (isIE10) {
            $('html').addClass('ie10'); // detect IE10 version
        }

        if (isIE10 || isIE9 || isIE8) {
            $('html').addClass('ie'); // detect IE10 version
        }
    };

    /**
     * Runs callback functions set by App.addResponsiveHandler().
     * @private
     */
    var _runResizeHandlers = function () {
        // Reinitialize other subscribed elements
        for (var i = 0; i < resizeHandlers.length; i++) {
            var each = resizeHandlers[i];
            each.call();
        }
    };

    /**
     * Handles the layout reinitialization on window resize
     */
    var handleOnResize = function () {
        var resize;
        if (isIE8) {
            var currheight;
            $(window).resize(function () {
                if (currheight == document.documentElement.clientHeight) {
                    return; // Quiet event since only body resized not window.
                }
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function () {
                    _runResizeHandlers();
                }, 50); // Wait 50ms until window resize finishes.
                currheight = document.documentElement.clientHeight; // Store last body client height
            });
        } else {
            $(window).resize(function () {
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function () {
                    _runResizeHandlers();
                }, 50); // wait 50ms until window resize finishes.
            });
        }
    };

    /**
     * Handles portlet tools & actions
     */
    var handlePortletTools = function () {
        // handle portlet remove
        $('body').on('click', '.portlet > .portlet-title > .tools > a.remove', function (e) {
            e.preventDefault();
            var portlet = $(this).closest(".portlet");

            if ($('body').hasClass('page-portlet-fullscreen')) {
                $('body').removeClass('page-portlet-fullscreen');
            }

            portlet.find('.portlet-title .fullscreen').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .reload').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .remove').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .config').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip('destroy');

            portlet.remove();
        });

        // handle portlet fullscreen
        $('body').on('click', '.portlet > .portlet-title .fullscreen', function (e) {
            e.preventDefault();
            var portlet = $(this).closest(".portlet");
            if (portlet.hasClass('portlet-fullscreen')) {
                $(this).removeClass('on');
                portlet.removeClass('portlet-fullscreen');
                $('body').removeClass('page-portlet-fullscreen');
                portlet.children('.portlet-body').css('height', 'auto');
            } else {
                var height = App.getViewPort().height -
                    portlet.children('.portlet-title').outerHeight() -
                    parseInt(portlet.children('.portlet-body').css('padding-top')) -
                    parseInt(portlet.children('.portlet-body').css('padding-bottom'));

                $(this).addClass('on');
                portlet.addClass('portlet-fullscreen');
                $('body').addClass('page-portlet-fullscreen');
                portlet.children('.portlet-body').css('height', height);
            }
        });

        $('body').on('click', '.portlet > .portlet-title > .tools > a.reload', function (e) {
            e.preventDefault();
            var el = $(this).closest(".portlet").children(".portlet-body");
            var url = $(this).attr("data-url");
            var error = $(this).attr("data-error-display");
            if (url) {
                App.blockUI({
                    target: el,
                    animate: true,
                    overlayColor: 'none'
                });
                $.ajax({
                    type: "GET",
                    cache: false,
                    url: url,
                    dataType: "html",
                    success: function (res) {
                        App.unblockUI(el);
                        el.html(res);
                        App.initAjax(); // Reinitialize elements & plugins for newly loaded content
                    },
                    error: function (/* xhr, ajaxOptions, thrownError */) {
                        App.unblockUI(el);
                        var msg = 'Error on reloading the content. Please check your connection and try again.';
                        if (error == "toastr" && toastr) {
                            toastr.error(msg);
                        } else if (error == "notific8" && $.notific8) {
                            $.notific8('zindex', 11500);
                            $.notific8(msg, {
                                theme: 'ruby',
                                life: 3000
                            });
                        } else {
                            alert(msg);
                        }
                    }
                });
            } else {
                // For demo purpose
                App.blockUI({
                    target: el,
                    animate: true,
                    overlayColor: 'none'
                });
                window.setTimeout(function () {
                    App.unblockUI(el);
                }, 1000);
            }
        });

        // Load AJAX data on page init
        $('.portlet .portlet-title a.reload[data-load="true"]').click();

        $('body').on('click', '.portlet > .portlet-title > .tools > .collapse, .portlet .portlet-title > .tools > .expand', function (e) {
            e.preventDefault();
            var el = $(this).closest(".portlet").children(".portlet-body");
            if ($(this).hasClass("collapse")) {
                $(this).removeClass("collapse").addClass("expand");
                el.slideUp(200);
            } else {
                $(this).removeClass("expand").addClass("collapse");
                el.slideDown(200);
            }
        });
    };

    /**
     * Handles custom checkboxes & radios using jQuery Uniform plugin
     */
    var handleUniform = function () {
        if (!$().uniform) {
            return;
        }
        var test = $("input[type=checkbox]:not(.toggle, .md-check, .md-radiobtn, .make-switch, .icheck), input[type=radio]:not(.toggle, .md-check, .md-radiobtn, .star, .make-switch, .icheck)");
        if (test.size() > 0) {
            test.each(function () {
                if ($(this).parents(".checker").size() === 0) {
                    $(this).show();
                    $(this).uniform();
                }
            });
        }
    };

    /**
     * Handles material design checkboxes
     */
    var handleMaterialDesign = function () {
        // Material design ckeckbox and radio effects
        $('body').on('click', '.md-checkbox > label, .md-radio > label', function () {
            var the = $(this);
            // Find the first span which is our circle/bubble
            var el = $(this).children('span:first-child');

            // Add the bubble class (we do this so it doesnt show on page load)
            el.addClass('inc');

            // Clone it
            var newone = el.clone(true);

            // Add the cloned version before our original
            el.before(newone);

            // Remove the original so that it is ready to run on next click
            $("." + el.attr("class") + ":last", the).remove();
        });

        if ($('body').hasClass('page-md')) {
            // Material design click effect
            // @see http://thecodeplayer.com/walkthrough/ripple-click-effect-google-material-design
            var element, circle, d, x, y;
            $('body').on('click', 'a.btn, button.btn, input.btn, label.btn', function (e) {
                element = $(this);

                if (element.find(".md-click-circle").length == 0) {
                    element.prepend("<span class='md-click-circle'></span>");
                }

                circle = element.find(".md-click-circle");
                circle.removeClass("md-click-animate");

                if (!circle.height() && !circle.width()) {
                    d = Math.max(element.outerWidth(), element.outerHeight());
                    circle.css({height: d, width: d});
                }

                x = e.pageX - element.offset().left - circle.width() / 2;
                y = e.pageY - element.offset().top - circle.height() / 2;

                circle.css({top: y + 'px', left: x + 'px'}).addClass("md-click-animate");

                setTimeout(function () {
                    circle.remove();
                }, 1000);
            });
        }

        // Floating labels
        var handleInput = function (el) {
            if (el.val() != "") {
                el.addClass('edited');
            } else {
                el.removeClass('edited');
            }
        };

        $('body').on('keydown', '.form-md-floating-label .form-control', function (/* e */) {
            handleInput($(this));
        });
        $('body').on('blur', '.form-md-floating-label .form-control', function (/* e */) {
            handleInput($(this));
        });

        $('.form-md-floating-label .form-control').each(function () {
            if ($(this).val().length > 0) {
                $(this).addClass('edited');
            }
        });
    };

    /**
     * Handles custom checkboxes & radios using jQuery iCheck plugin
     */
    var handleiCheck = function () {
        if (!$().iCheck) {
            return;
        }

        $('.icheck').each(function () {
            var checkboxClass = $(this).attr('data-checkbox') ? $(this).attr('data-checkbox') : 'icheckbox_minimal-grey';
            var radioClass = $(this).attr('data-radio') ? $(this).attr('data-radio') : 'iradio_minimal-grey';

            if (checkboxClass.indexOf('_line') > -1 || radioClass.indexOf('_line') > -1) {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass,
                    insert: '<div class="icheck_line-icon"></div>' + $(this).attr("data-label")
                });
            } else {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass
                });
            }
        });
    };

    /**
     * Handles Bootstrap switches
     */
    var handleBootstrapSwitch = function () {
        if (!$().bootstrapSwitch) {
            return;
        }
        $('.make-switch').bootstrapSwitch();
    };

    /**
     * Handles Bootstrap confirmations
     */
    var handleBootstrapConfirmation = function () {
        if (!$().confirmation) {
            return;
        }
        $('[data-toggle=confirmation]').confirmation({container: 'body', btnOkClass: 'btn btn-sm btn-success', btnCancelClass: 'btn btn-sm btn-danger'});
    };

    /**
     * Handles Bootstrap Accordions.
     */
    var handleAccordions = function () {
        $('body').on('shown.bs.collapse', '.accordion.scrollable', function (e) {
            App.scrollTo($(e.target));
        });
    };

    /**
     * Handles Bootstrap Tabs.
     */
    var handleTabs = function () {
        // Activate tab if tab id provided in the URL
        if (location.hash) {
            var tabId = encodeURI(location.hash.substr(1));
            $('a[href="#' + tabId + '"]').parents('.tab-pane:hidden').each(function () {
                var tabid = $(this).attr("id");
                $('a[href="#' + tabid + '"]').click();
            });
            $('a[href="#' + tabId + '"]').click();
        }

        if ($().tabdrop) {
            $('.tabbable-tabdrop .nav-pills, .tabbable-tabdrop .nav-tabs').tabdrop({
                text: '<i class="fa fa-ellipsis-v"></i>&nbsp;<i class="fa fa-angle-down"></i>'
            });
        }
    };

    /**
     * Handles Bootstrap Modals.
     */
    var handleModals = function () {
        // Fix stackable modal issue: when 2 or more modals opened, closing one of modal will remove .modal-open class.
        $('body').on('hide.bs.modal', function () {
            if ($('.modal:visible').size() > 1 && $('html').hasClass('modal-open') === false) {
                $('html').addClass('modal-open');
            } else if ($('.modal:visible').size() <= 1) {
                $('html').removeClass('modal-open');
            }
        });

        // Fix page scrollbars issue
        $('body').on('show.bs.modal', '.modal', function () {
            if ($(this).hasClass("modal-scroll")) {
                $('body').addClass("modal-open-noscroll");
            }
        });

        // Fix page scrollbars issue
        $('body').on('hide.bs.modal', '.modal', function () {
            $('body').removeClass("modal-open-noscroll");
        });

        // Remove ajax content and remove cache on modal closed
        $('body').on('hidden.bs.modal', '.modal:not(.modal-cached)', function () {
            $(this).removeData('bs.modal');
        });
    };

    /**
     * Handles Bootstrap Tooltips.
     */
    var handleTooltips = function () {
        // Global tooltips
        $('.tooltips').tooltip();

        // Portlet tooltips
        $('.portlet > .portlet-title .fullscreen').tooltip({
            container: 'body',
            title: 'Fullscreen'
        });
        $('.portlet > .portlet-title > .tools > .reload').tooltip({
            container: 'body',
            title: 'Reload'
        });
        $('.portlet > .portlet-title > .tools > .remove').tooltip({
            container: 'body',
            title: 'Remove'
        });
        $('.portlet > .portlet-title > .tools > .config').tooltip({
            container: 'body',
            title: 'Settings'
        });
        $('.portlet > .portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip({
            container: 'body',
            title: 'Collapse/Expand'
        });
    };

    /**
     * Handles Bootstrap Dropdowns.
     */
    var handleDropdowns = function () {
        // Hold dropdown on click
        $('body').on('click', '.dropdown-menu.hold-on-click', function (e) {
            e.stopPropagation();
        });
    };

    /**
     * Handles Alerts.
     */
    var handleAlerts = function () {
        $('body').on('click', '[data-close="alert"]', function (e) {
            $(this).parent('.alert').hide();
            $(this).closest('.note').hide();
            e.preventDefault();
        });

        $('body').on('click', '[data-close="note"]', function (e) {
            $(this).closest('.note').hide();
            e.preventDefault();
        });

        $('body').on('click', '[data-remove="note"]', function (e) {
            $(this).closest('.note').remove();
            e.preventDefault();
        });
    };

    /**
     * Handle hover dropdowns.
     */
    var handleDropdownHover = function () {
        $('[data-hover="dropdown"]').not('.hover-initialized').each(function () {
            $(this).dropdownHover();
            $(this).addClass('hover-initialized');
        });
    };

    /**
     * Handle textarea autosize.
     */
    var handleTextareaAutosize = function () {
        if (typeof autosize == "function") {
            autosize(document.querySelector('textarea.autosizeme'));
        }
    };

    /**
     * Last pop'ed popover
     */
    var lastPopedPopover;

    /**
     * Handles Bootstrap Popovers.
     */
    var handlePopovers = function () {
        $('.popovers').popover();

        // Close last displayed popover
        $(document).on('click.bs.popover.data-api', function (/* e */) {
            if (lastPopedPopover) {
                lastPopedPopover.popover('hide');
            }
        });
    };

    /**
     * Handles scrollable contents using jQuery SlimScroll plugin.
     */
    var handleScrollers = function () {
        App.initSlimScroll('.scroller');
    };

    /**
     * Handles Image Preview using jQuery Fancybox plugin.
     */
    var handleFancybox = function () {
        if (!jQuery.fancybox) {
            return;
        }

        if ($(".fancybox-button").size() > 0) {
            $(".fancybox-button").fancybox({
                groupAttr: 'data-rel',
                prevEffect: 'none',
                nextEffect: 'none',
                closeBtn: true,
                helpers: {
                    title: {
                        type: 'inside'
                    }
                }
            });
        }
    };

    /**
     * Handles counterup plugin wrapper.
     */
    var handleCounterup = function () {
        if (!$().counterUp) {
            return;
        }

        $("[data-counter='counterup']").counterUp({
            delay: 10,
            time: 1000
        });
    };

    /**
     * Fixes input placeholder issue for IE8 and IE9.
     */
    var handleFixInputPlaceholderForIE = function () {
        // Fix HTML-5 placeholder attribute for IE-7 & -8
        if (isIE8 || isIE9) {
            // This is HTML-5 placeholder fix for inputs, inputs with placeholder-no-fix class will be skipped (e.g.: we need this for password fields)
            $('input[placeholder]:not(.placeholder-no-fix), textarea[placeholder]:not(.placeholder-no-fix)').each(function () {
                var input = $(this);

                if (input.val() === '' && input.attr("placeholder") !== '') {
                    input.addClass("placeholder").val(input.attr('placeholder'));
                }

                input.focus(function () {
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                });

                input.blur(function () {
                    if (input.val() === '' || input.val() == input.attr('placeholder')) {
                        input.val(input.attr('placeholder'));
                    }
                });
            });
        }
    };

    /**
     * Handle Select2 Dropdowns.
     */
    var handleSelect2 = function () {
        if ($().select2) {
            $.fn.select2.defaults.set("theme", "bootstrap");
            $('.select2me').select2({
                placeholder: "Select",
                width: 'auto',
                allowClear: true
            });
        }
    };

    /**
     * Handle group element heights.
     */
    var handleHeight = function () {
        $('[data-auto-height]').each(function () {
            var parent = $(this);
            var items = $('[data-height]', parent);
            var height = 0;
            var mode = parent.attr('data-mode');
            var offset = parseInt(parent.attr('data-offset') ? parent.attr('data-offset') : 0);

            items.each(function () {
                if ($(this).attr('data-height') == "height") {
                    $(this).css('height', '');
                } else {
                    $(this).css('min-height', '');
                }

                var height_ = (mode == 'base-height' ? $(this).outerHeight() : $(this).outerHeight(true));
                if (height_ > height) {
                    height = height_;
                }
            });

            height = height + offset;

            items.each(function () {
                if ($(this).attr('data-height') == "height") {
                    $(this).css('height', height);
                } else {
                    $(this).css('min-height', height);
                }
            });

            if (parent.attr('data-related')) {
                $(parent.attr('data-related')).css('height', parent.height());
            }
        });
    };

    return {
        /**
         * Main function to initiate the theme
         */
        init: function () {

            /*
             |----------------------------------------------
             | Do not modify the core handlers call order!
             |----------------------------------------------
             */

            // Core handlers
            handleInit(); // initialize core variables
            handleOnResize(); // set and handle responsive

            // UI Component handlers
            handleMaterialDesign(); // handles material design
            handleUniform(); // handles custom radio & checkboxes
            handleiCheck(); // handles custom icheck radio and checkboxes
            handleBootstrapSwitch(); // handles bootstrap switch plugin
            handleScrollers(); // handles slim scrolling contents
            handleFancybox(); // handles fancy box
            handleSelect2(); // handles custom Select2 dropdowns
            handlePortletTools(); // handles portlet action bar functionality(refresh, configure, toggle, remove)
            handleAlerts(); //handles closabled alerts
            handleDropdowns(); // handles dropdowns
            handleTabs(); // handles tabs
            handleTooltips(); // handles bootstrap tooltips
            handlePopovers(); // handles bootstrap popovers
            handleAccordions(); //handles accordions
            handleModals(); // handles modals
            handleBootstrapConfirmation(); // handles bootstrap confirmations
            handleTextareaAutosize(); // handles autosize textareas
            handleCounterup(); // handles counterup instances
            this.addResizeHandler(handleHeight); // handles auto calculating height on window resize

            // Hacks
            handleFixInputPlaceholderForIE(); // IE-8 & -9 input placeholder issue fix
        },

        /**
         * Main function to initiate core javascript after AJAX complete
         */
        initAjax: function () {
            handleUniform(); // handles custom radio & checkboxes
            handleiCheck(); // handles custom icheck radio and checkboxes
            handleBootstrapSwitch(); // handles bootstrap switch plugin
            handleDropdownHover(); // handles dropdown hover
            handleScrollers(); // handles slim scrolling contents
            handleSelect2(); // handles custom Select2 dropdowns
            handleFancybox(); // handles fancy box
            handleDropdowns(); // handles dropdowns
            handleTooltips(); // handles bootstrap tooltips
            handlePopovers(); // handles bootstrap popovers
            handleAccordions(); //handles accordions
            handleBootstrapConfirmation(); // handles bootstrap confirmations
        },

        /**
         * Init main components
         */
        initComponents: function () {
            this.initAjax();
        },

        /**
         * Public function to remember last opened popover that needs to be closed on click
         * @param el
         */
        setLastPopedPopover: function (el) {
            lastPopedPopover = el;
        },

        /**
         * Public function to add callback a function which will be called on window resize
         * @param func
         */
        addResizeHandler: function (func) {
            resizeHandlers.push(func);
        },

        /**
         * Public functon to call _runresizeHandlers()
         */
        runResizeHandlers: function () {
            _runResizeHandlers();
        },

        /**
         * Wrapper function to scroll (focus) to an element
         * @param el
         * @param offeset
         */
        scrollTo: function (el, offeset) {
            var pos = (el && el.size() > 0) ? el.offset().top : 0;

            if (el) {
                if ($('body').hasClass('page-header-fixed')) {
                    pos = pos - $('.page-header').height();
                } else if ($('body').hasClass('page-header-top-fixed')) {
                    pos = pos - $('.page-header-top').height();
                } else if ($('body').hasClass('page-header-menu-fixed')) {
                    pos = pos - $('.page-header-menu').height();
                }
                pos = pos + (offeset ? offeset : -1 * el.height());
            }

            $('html,body').animate({
                scrollTop: pos
            }, 'slow');
        },

        initSlimScroll: function (el) {
            $(el).each(function () {
                if ($(this).attr("data-initialized")) {
                    return; // exit
                }

                var height;

                if ($(this).attr("data-height")) {
                    height = $(this).attr("data-height");
                } else {
                    height = $(this).css('height');
                }

                $(this).slimScroll({
                    allowPageScroll: true, // Allow page scroll when the element scroll is ended
                    size: '7px',
                    color: ($(this).attr("data-handle-color") ? $(this).attr("data-handle-color") : '#bbb'),
                    wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                    railColor: ($(this).attr("data-rail-color") ? $(this).attr("data-rail-color") : '#eaeaea'),
                    position: isRTL ? 'left' : 'right',
                    height: height,
                    alwaysVisible: $(this).attr("data-always-visible") == "1",
                    railVisible: $(this).attr("data-rail-visible") == "1",
                    disableFadeOut: true
                });

                $(this).attr("data-initialized", "1");
            });
        },

        destroySlimScroll: function (el) {
            $(el).each(function () {
                if ($(this).attr("data-initialized") === "1") { // Destroy existing instance before updating the height
                    $(this).removeAttr("data-initialized");
                    $(this).removeAttr("style");

                    var attrList = {};

                    // Store the custom attribures so later we will reassign.
                    if ($(this).attr("data-handle-color")) {
                        attrList["data-handle-color"] = $(this).attr("data-handle-color");
                    }
                    if ($(this).attr("data-wrapper-class")) {
                        attrList["data-wrapper-class"] = $(this).attr("data-wrapper-class");
                    }
                    if ($(this).attr("data-rail-color")) {
                        attrList["data-rail-color"] = $(this).attr("data-rail-color");
                    }
                    if ($(this).attr("data-always-visible")) {
                        attrList["data-always-visible"] = $(this).attr("data-always-visible");
                    }
                    if ($(this).attr("data-rail-visible")) {
                        attrList["data-rail-visible"] = $(this).attr("data-rail-visible");
                    }

                    $(this).slimScroll({
                        wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                        destroy: true
                    });

                    var the = $(this);

                    // Re-assign custom attributes
                    $.each(attrList, function (key, value) {
                        the.attr(key, value);
                    });

                }
            });
        },

        /**
         * Function to scroll to the top.
         */
        scrollTop: function () {
            App.scrollTo();
        },

        /**
         * Wrapper function to block element (indicate loading).
         * @param options
         */
        blockUI: function (options) {
            options = $.extend(true, {}, options);
            var html = '';
            if (options.animate) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '">' + '<div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>' + '</div>';
            } else if (options.iconOnly) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif" align=""></div>';
            } else if (options.textOnly) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><span>&nbsp;&nbsp;' + (options.message ? options.message : 'LOADING...') + '</span></div>';
            } else {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif" align=""><span>&nbsp;&nbsp;' + (options.message ? options.message : 'LOADING...') + '</span></div>';
            }

            if (options.target) { // Element blocking
                var el = $(options.target);
                if (el.height() <= ($(window).height())) {
                    options.cenrerY = true;
                }
                el.block({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    centerY: options.cenrerY !== undefined ? options.cenrerY : false,
                    css: {
                        top: '10%',
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            } else { // Page blocking
                $.blockUI({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    css: {
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            }
        },

        /**
         * Wrapper function to  un-block element (finish loading).
         * @param target
         */
        unblockUI: function (target) {
            if (target) {
                $(target).unblock({
                    onUnblock: function () {
                        $(target).css('position', '');
                        $(target).css('zoom', '');
                    }
                });
            } else {
                $.unblockUI();
            }
        },

        startPageLoading: function (options) {
            if (options && options.animate) {
                $('.page-spinner-bar').remove();
                $('body').append('<div class="page-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
            } else {
                $('.page-loading').remove();
                $('body').append('<div class="page-loading"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>' + (options && options.message ? options.message : 'Loading...') + '</span></div>');
            }
        },

        stopPageLoading: function () {
            $('.page-loading, .page-spinner-bar').remove();
        },

        alert: function (options) {
            options = $.extend(true, {
                container: "", // alerts parent container(by default placed after the page breadcrumbs)
                place: "append", // "append" or "prepend" in container
                type: 'success', // alert's type
                message: "", // alert's message
                close: true, // make alert closable
                reset: true, // close all previouse alerts first
                focus: true, // auto scroll to the alert after shown
                closeInSeconds: 0, // auto close after defined seconds
                icon: "" // put icon before the message
            }, options);

            var id = App.getUniqueID("App_alert");

            var html = '<div id="' + id + '" class="custom-alerts alert alert-' + options.type + ' fade in">' + (options.close ? '<button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>' : '') + (options.icon !== "" ? '<i class="fa-lg fa fa-' + options.icon + '"></i>  ' : '') + options.message + '</div>';

            if (options.reset) {
                $('.custom-alerts').remove();
            }

            if (!options.container) {
                if ($('body').hasClass("page-container-bg-solid") || $('body').hasClass("page-content-white")) {
                    $('.page-title').after(html);
                } else {
                    if ($('.page-bar').size() > 0) {
                        $('.page-bar').after(html);
                    } else {
                        $('.page-breadcrumb').after(html);
                    }
                }
            } else {
                if (options.place == "append") {
                    $(options.container).append(html);
                } else {
                    $(options.container).prepend(html);
                }
            }

            if (options.focus) {
                App.scrollTo($('#' + id));
            }

            if (options.closeInSeconds > 0) {
                setTimeout(function () {
                    $('#' + id).remove();
                }, options.closeInSeconds * 1000);
            }

            return id;
        },

        /**
         * Initializes uniform elements.
         * @param els
         */
        initUniform: function (els) {
            if (els) {
                $(els).each(function () {
                    if ($(this).parents(".checker").size() === 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            } else {
                handleUniform();
            }
        },

        /**
         * Wrapper function to update/sync jquery uniform checkbox & radios.
         * @param els
         */
        updateUniform: function (els) {
            $.uniform.update(els); // Update the uniform checkbox & radios UI after the actual input control state changed
        },

        /**
         * Public function to initialize the fancybox plugin.
         */
        initFancybox: function () {
            handleFancybox();
        },

        /**
         * Public helper function to get actual input value (used in IE-9 and IE-8 due to placeholder attribute not supported).
         * @param el
         * @returns {*}
         */
        getActualVal: function (el) {
            el = $(el);
            if (el.val() === el.attr("placeholder")) {
                return "";
            }
            return el.val();
        },

        /**
         * Public function to get a paremeter by name from URL.
         * @param paramName
         * @returns {*}
         */
        getURLParameter: function (paramName) {
            var searchString = window.location.search.substring(1),
                i, val, params = searchString.split("&");

            for (i = 0; i < params.length; i++) {
                val = params[i].split("=");
                if (val[0] == paramName) {
                    return unescape(val[1]);
                }
            }
            return null;
        },

        /**
         * Check for device touch support.
         * @returns {boolean}
         */
        isTouchDevice: function () {
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        },

        /**
         * Gets the correct viewport width
         * @returns {{width: *, height: *}}
         * @see http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
         */
        getViewPort: function () {
            var e = window,
                a = 'inner';
            if (!('innerWidth' in window)) {
                a = 'client';
                e = document.documentElement || document.body;
            }

            return {
                width: e[a + 'Width'],
                height: e[a + 'Height']
            };
        },

        getUniqueID: function (prefix) {
            return prefix + '_' + Math.floor(Math.random() * (new Date()).getTime());
        },

        /**
         * Checks IE8 mode.
         * @returns {boolean}
         */
        isIE8: function () {
            return isIE8;
        },

        /**
         * Checks IE9 mode.
         * @returns {boolean}
         */
        isIE9: function () {
            return isIE9;
        },

        /**
         * * Checks RTL mode.
         * @returns {boolean}
         */
        isRTL: function () {
            return isRTL;
        },

        isAngularJsApp: function () {
            return (typeof angular != 'undefined');
        },

        getAssetsPath: function () {
            return assetsPath;
        },

        setAssetsPath: function (path) {
            assetsPath = path;
        },

        setGlobalImgPath: function (path) {
            globalImgPath = path;
        },

        getGlobalImgPath: function () {
            return assetsPath + globalImgPath;
        },

        setGlobalPluginsPath: function (path) {
            globalPluginsPath = path;
        },

        getGlobalPluginsPath: function () {
            return assetsPath + globalPluginsPath;
        },

        getGlobalCssPath: function () {
            return assetsPath + globalCssPath;
        },

        /**
         * Gets layout color code by color name.
         * @param name
         * @returns {*}
         */
        getBrandColor: function (name) {
            return brandColors[name] ? brandColors[name] : '';
        },

        getResponsiveBreakpoint: function (size) {
            var sizes = {
                'xs': 480,     // extra small
                'sm': 768,     // small
                'md': 992,     // medium
                'lg': 1200     // large
            };

            return sizes[size] ? sizes[size] : 0;
        }
    };

}();

jQuery(document).ready(function () {
    App.init(); // Init metronic core componets
});
