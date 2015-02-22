var $windowWidth = $(window).width();
var $settingsSidebarControl = $('.settings-sidebar > button');
var $navigationControl = $('header > nav > ul > li.menu-control > button');
var $leftNavigation = $('#left-navigation');
var $minWrapper = $('#min-wrapper');
var $navigation = $('ul.main-nav');

jQuery(document).ready(function ($) {
    'use strict';

    callNavigation(); // Left Navigation Accordion

    // Browser size detect on loading Start
    if ($windowWidth > 1025) {
        onDesktop();
    }
    else if ($windowWidth < 500) {
        // On phone
        onPhoneDefault()
    } else if ($windowWidth < 1025) {
        // On Pad
        onTabletDefault();
    }

    minimizeLeftMenuHoverDisplay();
    settingsSidebarDisplay(); //Right Box Display
    phoneNavControl();
    layoutChangeColorStart();
    pluginLoadForLayout();
    paneChangeStart();
    dropdownTopNavBar();
    dropDownMenuControl();
    leftBarMinimize();
});

//-------------------------------
// Left Menu accordion Start
//-------------------------------

function callNavigation() {
    'use strict';

    $navigation.multiAccordion({
        multiAccordion: true,
        speed: 500,
        closedSign: '<i class="fa fa-caret-down"></i>',
        openedSign: '<i class="fa fa-caret-up"></i>'
    });
}

//-------------------------------------------
// Minimize left menu hover Display Start
//-------------------------------------------

function minimizeLeftMenuHoverDisplay() {
    $('ul.main-nav li').hover(
        function () {
            if ($($leftNavigation).hasClass('active')) {
                $(this).children('ul').addClass('open');
            }
        }, function () {
            if ($($leftNavigation).hasClass('active')) {
                $(this).children('ul').removeClass('open');
                $(this).children('ul').removeAttr("style");
            }
        }
    );
}

//-----------------------------------------------
// Call resize when minimize left menu Start
//-----------------------------------------------

function dropDownMenuControl() {
    'use strict';

    $('ul.main-nav li').children('ul').removeAttr("style");
}

//-----------------------------------------------
// Resize Call after resize left menu Start
//-----------------------------------------------

function changeMenuSizeTriger() {
    'use strict';

    $(window).trigger('resize');
}

//----------------------
// Desktop view Start
//----------------------

//-----------------------------------------------
// Left Navigation Transition  callback Start
//-----------------------------------------------
// $leftNavigation.on('webkitTransitionEnd moztransitionend transitionend oTransitionEnd', function (){
// });

function leftBarMinimize() {
    'use strict';

    $($navigationControl).click(function () {
        if ($navigation.hasClass('active')) {
            $leftNavigation.removeClass('active');
            $navigation.removeClass('active');
            $minWrapper.removeClass('active');
            changeMenuSizeTriger();
        } else {
            $navigation.addClass('active');
            $minWrapper.addClass('active');
            $leftNavigation.addClass('active');
            $navigation.find('ul').removeAttr('style');
            changeMenuSizeTriger();
        }
    });
}

function onDesktop() {
    'use strict';
}

//------------------------
// Tablet view Start
//------------------------

function onTabletDefault() {
    'use strict';

    $navigation.addClass('active');
    $minWrapper.addClass('active');
    $leftNavigation.addClass('active');
    $navigation.slideDown();
}

function onTablet() {
    'use strict';

    $navigation.addClass('active');
    $minWrapper.addClass('active');
    $leftNavigation.addClass('active');
}

//----------------------
// Phone view Start
//----------------------

function onPhoneDefault() {
    'use strict';

    $navigation.addClass('mobile');
    $navigation.css('display', 'none');
    $leftNavigation.css('width', '100%');
    $navigationControl.removeClass('spinIn').addClass('spinOut');
    $navigationControl.removeClass('active');
    $leftNavigation.children('ul').removeClass('active');
    $leftNavigation.removeClass('active');

    $($minWrapper).css('paddingLeft', '0');
}
function onPhone() {
    'use strict';

    $($navigation).addClass('mobile');
    $($navigation).css('display', 'none');
    $($leftNavigation).animate({
        width: '100%'
    }, 100, function () {
        $navigationControl.removeClass('spinIn').addClass('spinOut');
        $navigationControl.removeClass('active');
        $leftNavigation.children('ul').removeClass('active');
        $leftNavigation.removeClass('active');
    });
    $($minWrapper).animate({
        paddingLeft: '0'
    }, 100, function () {
    });
}

//-----------------------------
// Layout Size Change Start
//-----------------------------

var resizeId;
$(window).resize(function () {
    clearTimeout(resizeId);
    resizeId = setTimeout(doneResizingLayout, 500);
});

function doneResizingLayout() {
    var $currentWindowWidth = $(window).width();
    if ($windowWidth != $currentWindowWidth) {
        if ($currentWindowWidth < 500) {
            onPhone();
        } else if ($currentWindowWidth < 1025) {
            $leftNavigation.removeAttr('style');
            $minWrapper.removeAttr('style');
            $leftNavigation.removeAttr('style');
            $navigation.removeAttr('style');
            $navigation.removeClass('mobile');

            onTablet();
        } else if ($currentWindowWidth > 1025) {
            desktopResize();
        }
        $windowWidth = $currentWindowWidth;
    } else {
        $windowWidth = $currentWindowWidth;
    }
}

function desktopResize() {
    $leftNavigation.removeAttr('style');
    $minWrapper.removeAttr('style');
    $leftNavigation.removeAttr('style');
    $navigation.removeAttr('style');
    $navigation.removeClass('mobile');
    onDesktop();

}

//-----------------------------
// Right Box Display Start
//-----------------------------

function settingsSidebarDisplay() {
    'use strict';

    $settingsSidebarControl.click(function (e) {
        e.defaultPrevented = true;
        $(".settings-sidebar > form").show().animate({right: '0'}, 500);
    });
    $('.settings-sidebar button.close-button').click(function (e) {
        e.defaultPrevented = true;
        console.log($(".settings-sidebar > form").animate({right: '-280px'}));
    });
}

//----------------------------
// phone nav control Start
//----------------------------

function phoneNavControl() {
    'use strict';

    $('.phone-nav-control').click(function () {
        if ($navigation.is(":hidden")) {
            $navigation.slideDown();
        } else {
            $navigation.slideUp();
        }
    });
}

//------------------------------
// Layout Change color Start
//------------------------------

function layoutChangeColorStart() {
    'use strict';

    var $fullContent = $("body");
    $('.change-color-box ul li ').click(function () {
        $fullContent.removeClass('black-color');
        $fullContent.removeClass('blue-color');
        $fullContent.removeClass('deep-blue-color');
        $fullContent.removeClass('red-color');
        $fullContent.removeClass('light-green-color');
        $fullContent.removeClass('default');
        $('.change-color-box ul li ').removeClass('active');
        if (!$(this).hasClass('active')) {
            var className = $(this).attr('class');
            $fullContent.addClass(className);
            $(this).addClass('active');
        }
    });
    var $changeColor = $('#change-color');
    $('#change-color-control a').click(function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $($changeColor).animate({right: '-200px'});
        } else {
            $(this).addClass('active');
            $($changeColor).animate({right: '0'}, 500);
        }
    });
}

//-------------------------
// Panel Change Start
//-------------------------

function paneChangeStart() {
    'use strict';

    $('.panel-control li a.close-panel').click(function () {
        var $elements = $(this).parents(".panel");
        $elements.animate({opacity: 0.1}, 1000, function () {
            $(this).remove();
        });
    });
    $('.panel-control li a.minus').click(function () {
        var $elements = $(this).parents(".panel").children(".panel-body");
        if ($(this).hasClass('active')) {
            $elements.slideDown(200);
            $(this).children('i').removeClass('fa-square-o');
            $(this).children('i').addClass('fa-minus');
            $(this).removeClass('active');
        } else {
            $elements.slideUp(200);
            $(this).children('i').removeClass('fa-minus');
            $(this).children('i').addClass('fa-square-o');
            $(this).addClass('active');
        }
    });
}

function dropdownTopNavBar() {
    'use strict';

    $('.dropdown').on('show.bs.dropdown', function (e) {
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown(500, function () {
        });
    });

    // ADD SLIDEUP ANIMATION TO DROPDOWN //
    $('.dropdown').on('hide.bs.dropdown', function (e) {
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp(500, function () {
        });
    });
}

//----------------------------------
// Plugin load for layout Start
//----------------------------------

function pluginLoadForLayout() {
    'use strict';

    try {
        $('.nano').nanoScroller({
            preventPageScrolling: true,
            alwaysVisible: true,
            scroll: 'top'
        });
        $('.nano-chat').nanoScroller({
            preventPageScrolling: true,
            alwaysVisible: true,
            scroll: 'bottom'
        });
        $('.progress-bar').progressbar({
            display_text: 'fill'
        });
        $('.easyPieChart').easyPieChart({
            barColor: $redActive,
            scaleColor: $redActive,
            easing: 'easeOutBounce',
            onStep: function (from, to, percent) {
                $(this.el).find('.easyPiePercent').text(Math.round(percent));
            }
        });
        var chart1 = window.chart = $('.easyPieChart').data('easyPieChart');
        $('.js_update').on('click', function () {
            chart1.update(Math.random() * 200 - 100);
        });

        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            var switchery = new Switchery(html);
        });

        var elementRed = Array.prototype.slice.call(document.querySelectorAll('.js-switch-red'));
        elementRed.forEach(function (htmlRed) {
            var switcheryRed = new Switchery(htmlRed, {color: $redActive});
        });

        var elementLightGreen = Array.prototype.slice.call(document.querySelectorAll('.js-switch-light-green'));
        elementLightGreen.forEach(function (htmlgreen) {
            var elementLightGreen = new Switchery(htmlgreen, {color: $lightGreen});
        });
        var elementLightBlue = Array.prototype.slice.call(document.querySelectorAll('.js-switch-light-blue'));
        elementLightBlue.forEach(function (htmlLightBlue) {
            var switcheryLightBlue = new Switchery(htmlLightBlue, {color: $lightBlueActive});
        });
    } catch (e) {

    }
}

//---------------------
// Detect IE Start
//---------------------

function detectIE() {
    'use strict';

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    var trident = ua.indexOf('Trident/');

    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    if (trident > 0) {
        // IE 11 (or newer) => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }
    // other browser
    return false;
}
