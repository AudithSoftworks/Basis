var gulp = require('gulp'),
    elixir = require('laravel-elixir');

elixir.config.sourcemaps = false;

elixir(function (mix) {

    /*------------------------------
     | Merge assets in view-basis
     *-----------------------------*/

    var resolveAssetMapToActualFilePaths = function (assetNameSpace, typeOfAsset) {
        var outputList = [];
        var assetMap = typeOfAsset == 'js' ? jsMaps : cssMaps;
        var assetAliases = typeOfAsset == 'js' ? jsAliases : cssAliases;
        var explodedRequestPath = assetNameSpace.split('/');

        if (explodedRequestPath.length > 1 && assetMap.hasOwnProperty(explodedRequestPath[0] + '/*')) {
            outputList.push.apply(outputList, assetMap[explodedRequestPath[0] + '/*']);
        } else if (explodedRequestPath.length == 1 || !assetMap.hasOwnProperty(assetNameSpace)) {
            outputList = assetMap[explodedRequestPath[0] + '/*']
        }
        if (assetMap.hasOwnProperty(assetNameSpace)) {
            outputList.push.apply(outputList, assetMap[assetNameSpace]);
        }

        var resolvedList = [];
        for (var item in outputList) {
            if (outputList.hasOwnProperty(item) && assetAliases.hasOwnProperty(outputList[item])) {
                resolvedList.push.apply(resolvedList, assetAliases[outputList[item]]);
            } else {
                resolvedList.push.apply(resolvedList, outputList[item]);
            }
        }

        return resolvedList;
    };

    var jsAliases = {
        'pace': ['public/bower_components/pace/pace.js'],
        'jquery': ['public/bower_components/jquery/dist/jquery.js'],
        'bootstrap': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap.min.js'],
        'bootstrap_affix': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/affix.js'],
        'bootstrap_alert': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/alert.js'],
        'bootstrap_button': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/button.js'],
        'bootstrap_carousel': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/carousel.js'],
        'bootstrap_collapse': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/collapse.js'],
        'bootstrap_dropdown': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/dropdown.js'],
        'bootstrap_modal': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/modal.js'],
        'bootstrap_popover': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/popover.js'],
        'bootstrap_scrollspy': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/scrollspy.js'],
        'bootstrap_tab': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/tab.js'],
        'bootstrap_tooltip': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/tooltip.js'],
        'bootstrap_transition': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/transition.js'],
        'amaranjs': ['public/bower_components/amaranjs/dist/js/jquery.amaran.js'],
        'bootbox': ['public/bower_components/bootbox/bootbox.js'],
        'bootstrap_breakpoints': ['public/bower_components/bootstrap-breakpoints/src/bootstrap-breakpoints.js'],
        'bootstrap_progressbar': ['public/bower_components/bootstrap-progressbar/bootstrap-progressbar.js'],
        'bootstrap_switch': ['public/bower_components/bootstrap-switch/dist/js/bootstrap-switch.js'],
        'bower_jvectormap_2': [
            'public/bower_components/bower-jvectormap-2/jquery-jvectormap-2.0.0.min.js',
            'public/bower_components/bower-jvectormap-2/jquery-jvectormap-world-mill-en.js'
        ],
        'count_up': ['public/bower_components/countUp.js/countUp.js'],
        'fastclick': ['public/bower_components/fastclick/lib/fastclick.js'],
        'humane_js': ['public/bower_components/humane-js/humane.js'],
        'jquery_easy_pie_chart': ['public/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.js'],
        'easy_pie_chart': ['public/bower_components/jquery.easy-pie-chart/dist/easypiechart.js'],
        'jquery_easing': ['public/bower_components/jquery-easing/jquery.easing.js'],
        'jquery_flot': [
            'public/bower_components/jquery-flot/jquery.flot.js',
            'public/bower_components/jquery-flot/jquery.flot.pie.js',
            'public/bower_components/jquery-flot/jquery.flot.resize.js'
        ],
        'jquery_ui': ['public/bower_components/jquery-ui/jquery-ui.js'],
        'nanoscroller': ['public/bower_components/nanoscroller/bin/javascripts/jquery.nanoscroller.js'],
        'notifyjs': ['public/bower_components/notifyjs/dist/notify.js'],
        'requirejs': ['public/bower_components/requirejs/require.js'],
        'skycons_html5': ['public/bower_components/skycons-html5/skycons.js'],
        'transitionize': ['public/bower_components/transitionize/dist/transitionize.js'],
        'switchery': ['public/bower_components/switchery/dist/switchery.min.js']
    };

    var jsMaps = {
        '/*': ['pace', 'humane_js', 'jquery', ['resources/assets/javascripts/app.js']],
        '/auth': ['switchery'],
        'admin-demo/*': ['pace', 'jquery', 'bootstrap', 'bootstrap_breakpoints', 'amaranjs', ['resources/assets/javascripts/admin-demo.js']],
        'admin-demo/dashboard': [
            'jquery_easing', 'jquery_easy_pie_chart', 'bower_jvectormap_2', 'skycons_html5', 'count_up', 'nanoscroller', 'bootstrap_switch', 'switchery',
            'bootstrap_progressbar', 'jquery_flot', ['resources/assets/javascripts/admin-demo/dashboard.js']
        ],
        'admin-demo/notifications': []
    };

    var cssAliases = {
        'bootstrap_switch': ['public/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css'],
        'bower_jvectormap_2': ['public/bower_components/bower-jvectormap-2/jquery-jvectormap-2.0.0.css'],
        'nanoscroller': ['public/bower_components/nanoscroller/bin/css/nanoscroller.css'],
        'pace': ['public/bower_components/pace/themes/orange/pace-theme-minimal.css'],
        'switchery': ['public/bower_components/switchery/switchery.css'],
        'humane_js': [
            'public/bower_components/humane-js/themes/bigbox.css',
            'public/bower_components/humane-js/themes/boldlight.css',
            'public/bower_components/humane-js/themes/jackedup.css',
            'public/bower_components/humane-js/themes/libnotify.css',
            'public/bower_components/humane-js/themes/original.css'
        ]
    };

    var cssMaps = {
        '/*': ['pace', 'humane_js'],
        '/auth': ['switchery', ['resources/assets/stylesheets/auth.css']],
        'admin-demo/*': [
            ['resources/assets/stylesheets/admin-demo.css'], 'pace', 'bootstrap_switch', 'nanoscroller', 'switchery',
            ['public/stylesheets/fickle.css', 'public/stylesheets/fickle_responsive.css']
        ],
        'admin-demo/dashboard': ['bower_jvectormap_2', ['resources/assets/stylesheets/admin-demo/dashboard.css']],
        'admin-demo/notifications': ['humane_js']
    };

    mix.scripts(resolveAssetMapToActualFilePaths('/auth', 'js'), 'public/javascripts/auth.js', './');
    mix.scripts(resolveAssetMapToActualFilePaths('admin-demo/dashboard', 'js'), 'public/javascripts/admin-demo/dashboard.js', './');

    mix.styles(resolveAssetMapToActualFilePaths('/auth', 'css'), 'public/stylesheets/auth.css', './');
    mix.styles(resolveAssetMapToActualFilePaths('admin-demo/dashboard', 'css'), 'public/stylesheets/admin-demo/dashboard.css', './');

    /*---------------
     | Versioning
     *--------------*/

    mix.version(['public/javascripts/**/*.js', 'public/stylesheets/**/*.css']);
});
