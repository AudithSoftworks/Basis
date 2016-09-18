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
        'bootstrap': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap.min.js'],
        'bootstrap_affix': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/affix.js'],
        'bootstrap_alert': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/alert.js'],
        'bootstrap_button': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/button.js'],
        'bootstrap_carousel': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/carousel.js'],
        'bootstrap_collapse': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/collapse.js'],
        'bootstrap_dropdown': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/dropdown.js'],
        'bootstrap_hover_dropdown': ['public/bower_components/bootstrap-hover-dropdown/bootstrap-hover-dropdown.js'],
        'bootstrap_modal': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/modal.js'],
        'bootstrap_popover': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/popover.js'],
        'bootstrap_scrollspy': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/scrollspy.js'],
        'bootstrap_tab': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/tab.js'],
        'bootstrap_tooltip': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/tooltip.js'],
        'bootstrap_transition': ['public/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap/transition.js'],
        'bootbox': ['public/bower_components/bootbox/bootbox.js'],
        'bootstrap_progressbar': ['public/bower_components/bootstrap-progressbar/bootstrap-progressbar.js'],
        'bootstrap_switch': ['public/bower_components/bootstrap-switch/dist/js/bootstrap-switch.js'],
        'bower_jvectormap_2': [
            'public/bower_components/bower-jvectormap-2/jquery-jvectormap-2.0.0.min.js',
            'public/bower_components/bower-jvectormap-2/jquery-jvectormap-world-mill-en.js'
        ],
        'easy_pie_chart': ['public/bower_components/jquery.easy-pie-chart/dist/easypiechart.js'],
        'excanvas': ['public/bower_components/excanvas/excanvas.js'],
        'fastclick': ['public/bower_components/fastclick/lib/fastclick.js'],
        'fine_uploader': ['public/bower_components/fine-uploader/_build/fine-uploader.js'],
        'humane_js': ['public/bower_components/humane-js/humane.js'],
        'jquery': ['public/bower_components/jquery/dist/jquery.js'],
        'jquery_backstretch': ['public/bower_components/jquery-backstretch-2/jquery.backstretch.js'],
        'jquery_blockui': ['public/bower_components/jquery.blockUI/jquery.blockUI.js'],
        'jquery_easy_pie_chart': ['public/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.js'],
        'jquery_easing': ['public/bower_components/jquery-easing/jquery.easing.js'],
        'jquery_flot': [
            'public/bower_components/jquery-flot/jquery.flot.js',
            'public/bower_components/jquery-flot/jquery.flot.pie.js',
            'public/bower_components/jquery-flot/jquery.flot.resize.js'
        ],
        'jquery_ui': ['public/bower_components/jquery-ui/jquery-ui.js'],
        'jquery_slimscroll': ['public/bower_components/jquery.slimscroll/jquery.slimscroll.js'],
        'jquery_validation': [
            'public/bower_components/jquery.validation/dist/jquery.validate.js',
            'public/bower_components/jquery.validation/dist/additional-methods.js'
        ],
        'js_cookie': ['public/bower_components/js-cookie/src/js.cookie.js'],
        'pace': ['public/bower_components/pace/pace.js'],
        'react': [
            'public/bower_components/react/react.js',
            'public/bower_components/react/react-dom.js'
        ],
        'requirejs': ['public/bower_components/requirejs/require.js'],
        'respond': [
            'public/bower_components/respond/dest/matchmedia.polyfill.js',
            'public/bower_components/respond/dest/respond.js'
        ],
        'select2': ['public/bower_components/select2/dist/js/select2.full.js'],
        'skycons_html5': ['public/bower_components/skycons-html5/skycons.js'],
        'transitionize': ['public/bower_components/transitionize/dist/transitionize.js'],
        'switchery': ['public/bower_components/switchery/dist/switchery.min.js']
    };

    var jsMaps = {
        '/*': ['jquery', 'bootstrap', 'js_cookie', 'bootstrap_hover_dropdown', 'jquery_slimscroll', 'jquery_blockui', 'bootstrap_switch'],
        '/auth': ['jquery_validation', 'select2', 'jquery_backstretch', ['resources/assets/javascripts/app.js'], ['resources/assets/javascripts/auth.js']],
        '/file': ['bootstrap', 'bootstrap_progressbar', 'fine_uploader'],

        'admin/*': [
            'jquery', 'bootstrap', 'js_cookie', 'jquery_slimscroll', 'jquery_blockui', 'bootstrap_switch',
            ['resources/assets/javascripts/app.js'], ['resources/assets/javascripts/admin.js'],
            ['resources/assets/javascripts/admin/layout.js'], ['resources/assets/javascripts/admin/quick-sidebar.js']],
        'admin/user': []
    };

    var cssAliases = {
        'bootstrap_switch': ['public/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css'],
        'bower_jvectormap_2': ['public/bower_components/bower-jvectormap-2/jquery-jvectormap-2.0.0.css'],
        'fine_uploader': ['public/bower_components/fine-uploader/_build/fine-uploader-gallery.css'],
        'humane_js': [
            'public/bower_components/humane-js/themes/bigbox.css',
            'public/bower_components/humane-js/themes/boldlight.css',
            'public/bower_components/humane-js/themes/jackedup.css',
            'public/bower_components/humane-js/themes/libnotify.css',
            'public/bower_components/humane-js/themes/original.css'
        ],
        'pace': ['public/bower_components/pace/themes/orange/pace-theme-minimal.css'],
        'switchery': ['public/bower_components/switchery/switchery.css']
    };

    var cssMaps = {
        '/*': ['pace', 'humane_js'],
        '/auth': ['switchery', ['resources/assets/stylesheets/auth.css'], 'bootstrap_switch'],
        '/file': ['switchery', ['resources/assets/stylesheets/auth.css'], 'fine_uploader'],
        'admin/*': ['pace', ['resources/assets/stylesheets/admin.css']],
        'admin/user': []
    };

    mix.scripts(resolveAssetMapToActualFilePaths('/auth', 'js'), 'public/javascripts/auth.js', './');
    mix.scripts(resolveAssetMapToActualFilePaths('/file', 'js'), 'public/javascripts/file.js', './');
    mix.scripts(resolveAssetMapToActualFilePaths('admin/user', 'js'), 'public/javascripts/admin.js', './');

    mix.styles(resolveAssetMapToActualFilePaths('/auth', 'css'), 'public/stylesheets/auth.css', './');
    mix.styles(resolveAssetMapToActualFilePaths('/file', 'css'), 'public/stylesheets/file.css', './');
    mix.styles(resolveAssetMapToActualFilePaths('admin/user', 'css'), 'public/stylesheets/admin.css', './');

    /*---------------
     | Versioning
     *--------------*/

    mix.version(['public/javascripts/**/*.js', 'public/stylesheets/**/*.css']);
});
