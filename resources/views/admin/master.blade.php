<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Audith.Basis">
        <meta name="author" content="Shahriyar Imanov <shehi@imanov.me>">

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <!-- Viewport metatags -->
        <meta name="HandheldFriendly" content="true">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <!-- iOS webapp metatags -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">

        <!-- iOS webapp icons -->
        <!-- <link rel="apple-touch-icon-precomposed" href="/media-/media_assets/images/fickle/ios/logo-72.png"> -->
        <!-- <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/media-/media_assets/images/fickle/ios/logo-72.png"> -->
        <!-- <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/media-/media_assets/images/fickle/ios/logo-114.png"> -->

        <!-- @todo Add a favicon -->
        <!-- <link rel="shortcut icon" href="/media-/media_assets/images/fickle/ico/fab.ico"> -->

        <title>Audith.Basis</title>

        <!--Page loading plugin Start -->
        <link rel="stylesheet" href="/bower_components/pace/themes/orange/pace-theme-minimal.css">

        <link href="/bower_components/bootstrap-progressbar/css/bootstrap-progressbar-3.1.1.css" rel="stylesheet" media="screen, projection" type="text/css">
        <link href="/bower_components/bower-jvectormap-2/jquery-jvectormap-2.0.0.css" rel="stylesheet" media="screen, projection" type="text/css">

        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" media="screen, projection">
        <link href="//fonts.googleapis.com/css?family=Armata|PT+Sans|Montserrat:700,400" rel="stylesheet" media="screen, projection">

        <link href="/bower_components/amaranjs/dist/css/amaran.min.css" rel="stylesheet" media="screen, projection" type="text/css">
        <link href="/bower_components/nanoscroller/bin/css/nanoscroller.css" rel="stylesheet" media="screen, projection">
        <link href="/bower_components/switchery/switchery.css" rel="stylesheet" media="screen, projection">
        <link href="/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet" media="screen, projection">

        <link href="/stylesheets/fickle.css" rel="stylesheet" media="screen, projection" type="text/css">
        <link href="/stylesheets/fickle_responsive.css" rel="stylesheet" media="screen, projection" type="text/css">

        <link href="/stylesheets/admin/screen.css" rel="stylesheet" media="screen, projection" type="text/css">

        <script src="/bower_components/pace/pace.min.js"></script>

        <!--Layout Script start -->
        <script type="text/javascript" src="/javascripts/color.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/jquery/dist/jquery.min.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/bootstrap/dist/js/bootstrap.min.js" defer="defer"></script>
        <script type="text/javascript" src="/javascripts/multipleAccordion.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/jquery-easing/jquery.easing.min.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/nanoscroller/bin/javascripts/jquery.nanoscroller.min.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/switchery/dist/switchery.min.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/bootstrap-progressbar/bootstrap-progressbar.min.js" defer="defer"></script>

        <script type="text/javascript" src="/bower_components/jquery-flot/jquery.flot.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/jquery-flot/jquery.flot.pie.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/jquery-flot/jquery.flot.resize.js" defer="defer"></script>

        <script type="text/javascript" src="/javascripts/admin-demo/layout.js" defer="defer"></script>

        <script type="text/javascript" src="/bower_components/countUp.js/countUp.min.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/skycons-html5/skycons.js" defer="defer"></script>

        <script type="text/javascript" src="/bower_components/bower-jvectormap-2/jquery-jvectormap-2.0.0.min.js" defer="defer"></script>
        <script type="text/javascript" src="/bower_components/bower-jvectormap-2/jquery-jvectormap-world-mill-en.js" defer="defer"></script>

        <script type="text/javascript" src="/bower_components/amaranjs/dist/js/jquery.amaran.min.js" defer="defer"></script>

        <script src="/javascripts/admin-demo/dashboard.js" defer="defer"></script>
    </head>
    <body>
        <header class="navigation container-fluid">
            <h1><a href="" title="">Audith.Basis</a></h1>

            <nav class="top-navigation">
                <!-- Top Navigation -->
                <ul role="menu">
                    <!-- Collapse navigation menu icon -->
                    <li class="menu-control hidden-xs pull-left" role="menuitem">
                        <button class="fa fa-bars"></button>
                    </li>

                    <!-- Search box -->
                    <li class="dropdown search-box pull-left" role="menuitem">
                        <button class="dropdown-toggle" data-toggle="dropdown">
                            <span class="fa fa-search"></span>
                        </button>

                        <form class="dropdown-menu">
                            <label for="search">Search</label>
                            <input type="search" id="search" class="pull-left" placeholder="enter keyword and hit enter to search.">
                        </form>
                    </li>

                    <li class="phone-nav-box pull-left" role="menuitem"><button class="phone-nav-control visible-xs"><span class="fa fa-bars"></span></button></li>
                </ul>

                <ul role="menu">
                    <!-- Tasks drop down -->
                    <li class="dropdown">
                        <button class="dropdown-toggle" data-toggle="dropdown">
                            <span class="fa fa-tasks"></span>
                            <span class="badge badge-lightBlue">3</span>
                        </button>

                        <aside class="dropdown-menu right top-tasks">
                            <strong>All Task</strong>
                            <ul class="goal-items">
                                <li>
                                    <a href="javascript:void(0)">
                                        <img class="goal-user-image rounded" src="/media_assets/images/fickle/demo/avatar-80.png" alt="user image"/>
                                        <dl class="goal-content pull-left">
                                            <dt>Wordpress Theme</dt>
                                            <dd class="progress progress-striped active">
                                                <div class="progress-bar ls-light-blue-progress six-sec-ease-in-out" data-transitiongoal="100"></div>
                                            </dd>
                                        </dl>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <img class="goal-user-image rounded" src="/media_assets/images/fickle/demo/avatar-80.png" alt="user image"/>
                                        <dl class="goal-content pull-left">
                                            <dt>Wordpress Theme</dt>
                                            <dd class="progress progress-striped active">
                                                <div class="progress-bar ls-light-blue-progress six-sec-ease-in-out" data-transitiongoal="25"></div>
                                            </dd>
                                        </dl>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <img class="goal-user-image rounded" src="/media_assets/images/fickle/demo/avatar-80.png" alt="user image"/>
                                        <dl class="goal-content pull-left">
                                            <dt>Wordpress Theme</dt>
                                            <dd class="progress progress-striped active">
                                                <div class="progress-bar ls-light-blue-progress six-sec-ease-in-out" data-transitiongoal="75"></div>
                                            </dd>
                                        </dl>
                                    </a>
                                </li>
                                <li class="only-link">
                                    <a href="javascript:void(0)">View All</a>
                                </li>
                            </ul>
                        </aside>
                    </li>

                    <!-- Notification drop down -->
                    <li class="dropdown" role="menuitem">
                        <button class="dropdown-toggle" data-toggle="dropdown">
                            <span class="fa fa-bell-o"></span>
                            <span class="badge badge-red">6</span>
                        </button>

                        <aside class="dropdown-menu right top-notification">
                            <strong>Notification</strong>
                            <ul class="ls-feed">
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-red">
                                            <i class="fa fa-check white"></i>
                                        </span>
                                        You have 4 pending tasks.
                                        <span class="date">Just now</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-light-green">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </span>
                                        Finance Report for year 2013
                                        <span class="date">30 min</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-lightBlue">
                                            <i class="fa fa-shopping-cart"></i>
                                        </span>
                                        New order received with
                                        <span class="date">45 min</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-lightBlue">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        5 pending membership
                                        <span class="date">50 min</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-red">
                                            <i class="fa fa-bell"></i>
                                        </span>
                                        Server hardware upgraded
                                        <span class="date">1 hr</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-blue">
                                            <i class="fa fa-briefcase"></i>
                                        </span>
                                        IPO Report for
                                        <span class="lightGreen">2014</span>
                                        <span class="date">5 hrs</span>
                                    </a>
                                </li>
                                <li class="only-link">
                                    <a href="javascript:void(0)">View All</a>
                                </li>
                            </ul>
                        </aside>
                    </li>

                    <!-- Email drop down -->
                    <li class="dropdown" role="menuitem">
                        <button class="dropdown-toggle" data-toggle="dropdown">
                            <span class="fa fa-envelope-o"></span>
                            <span class="badge badge-red">3</span>
                        </button>

                        <aside class="dropdown-menu right email-notification">
                            <strong>Email</strong>
                            <ul class="email-top">
                                <li>
                                    <a href="javascript:void(0)">
                                        <div class="email-top-image">
                                            <img class="rounded" src="/media_assets/images/fickle/demo/avatar-80.png" alt="user image"/>
                                        </div>
                                        <div class="email-top-content">
                                            John Doe
                                            <div>Sample Mail 1</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <div class="email-top-image">
                                            <img class="rounded" src="/media_assets/images/fickle/demo/avatar-80.png" alt="user image"/>
                                        </div>
                                        <div class="email-top-content">
                                            John Doe
                                            <div>Sample Mail 2</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <div class="email-top-image">
                                            <img class="rounded" src="/media_assets/images/fickle/demo/avatar-80.png" alt="user image"/>
                                        </div>
                                        <div class="email-top-content">
                                            John Doe
                                            <div> Sample Mail 4</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="only-link">
                                    <a href="#">View All</a>
                                </li>
                            </ul>
                        </aside>
                        <!--Email drop down end-->
                    </li>

                    <li class="settings-sidebar hidden-xs" role="menuitem">
                        <button type="button">
                            <i class="fa fa-cogs"></i>
                        </button>

                        <!--Right hidden section-->
                        <form id="right-wrapper" class="pull-left" name="right-sidebar-for-settings">
                            <button class="close-button" type="button">
                                <i class="fa fa-times"></i>
                            </button>

                            <h2 class="pull-left"><span class="fa fa-cogs"></span> Settings</h2>

                            <fieldset class="clear">
                                <h3>Account Setting</h3>

                                <fieldset class="setting-box-content">
                                    <ul>
                                        <li><span class="pull-left">Online status: </span><input type="checkbox" class="js-switch-red" checked/></li>
                                        <li><span class="pull-left">Show offline contact: </span><input type="checkbox" class="js-switch-light-blue" checked/></li>
                                        <li><span class="pull-left">Invisible mode: </span><input class="js-switch" type="checkbox" checked></li>
                                        <li><span class="pull-left">Log all message:</span><input class="js-switch-light-green" type="checkbox" checked></li>
                                    </ul>
                                </fieldset>
                            </fieldset>
                            <fieldset class="clear">
                                <h3>Maintenance</h3>

                                <fieldset class="setting-box-content">
                                    <div class="easy-pai-box">
                                        <span class="easyPieChart" data-percent="90">
                                            <span class="easyPiePercent"></span>
                                        </span>
                                    </div>
                                    <div class="easy-pai-box">
                                        <button class="btn btn-xs ls-red-btn js_update">Update Data</button>
                                    </div>
                                </fieldset>
                            </fieldset>
                            <fieldset class="clear">
                                <h3>Progress</h3>

                                <fieldset class="setting-box-content">

                                    <h4>File uploading</h4>

                                    <div class="progress">
                                        <div class="progress-bar ls-light-blue-progress six-sec-ease-in-out" data-transitiongoal="10"></div>
                                    </div>

                                    <h4>Plugin setup</h4>

                                    <div class="progress progress-striped active">
                                        <div class="progress-bar six-sec-ease-in-out ls-light-green-progress" data-transitiongoal="20"></div>
                                    </div>
                                    <h4>Post New Article</h4>

                                    <div class="progress progress-striped active">
                                        <div class="progress-bar ls-yellow-progress six-sec-ease-in-out" data-transitiongoal="80"></div>
                                    </div>
                                    <h4>Create New User</h4>

                                    <div class="progress progress-striped active">
                                        <div class="progress-bar ls-red-progress six-sec-ease-in-out" data-transitiongoal="100"></div>
                                    </div>
                                </fieldset>
                            </fieldset>
                        </form>
                    </li>

                    <li role="menuitem"><a href=""><i class="fa fa-lock"></i></a></li>

                    <li role="menuitem"><a href=""><i class="fa fa-power-off"></i></a></li>
                </ul>
            </nav>
        </header>

        <main id="main-container">
            <!-- Left navigation section -->
            <aside id="left-navigation">
                <!-- Left navigation user details -->
                <figure class="user-image">
                    <img src="/media_assets/images/fickle/demo/avatar-80.png" alt="User is Online.">
                    <figcaption class="user-online-status"><span class="user-status is-online">User is Online.</span></figcaption>
                </figure>
                <ul class="social-icon">
                    <li><a href="javascript:void(0)"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="javascript:void(0)"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="javascript:void(0)"><i class="fa fa-github"></i></a></li>
                    <li><a href="javascript:void(0)"><i class="fa fa-bitbucket"></i></a></li>
                </ul>

                <!--Left navigation start-->
                <ul class="main-nav">
                    <li class="active">
                        <a class="active" href="index.html">
                            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-envelope-o"></i> <span>Email</span> <span class="badge badge-red">3</span>
                        </a>
                        <ul>
                            <li><a href="mail.html">Inbox</a></li>
                            <li><a href="compose-mail.html">Compose Mail</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-bar-chart-o"></i> <span>Charts</span>
                        </a>
                        <ul>
                            <li>
                                <a href="c3js.html">C3 Chart </a>
                            </li>
                            <li>
                                <a href="chartjs.html">Chart js</a>
                            </li>
                            <li><a href="flotchart.html">Flot</a>
                            </li>
                            <li>
                                <a href="morrisjs.html">Morris</a>
                            </li>
                            <li>
                                <a href="sparkline.html">Spark Line</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-glass"></i>
                            <span>Form Staffs</span>
                        </a>
                        <ul>
                            <li><a href="sample-form.html">Sample Form</a></li>
                            <li><a href="form-wizard.html">Form Wizards</a></li>
                            <li><a href="form-validation.html">Form Validation</a></li>
                            <li><a href="editor.html">Editor</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-flag"></i>
                            <span>Ui Elements</span>
                            <span class="badge badge-red">New</span>
                        </a>
                        <ul>
                            <li><a href="button-switch.html">Button & Switch</a></li>
                            <li><a href="checkbox-radio.html">Checkbox & Radio</a></li>
                            <li><a href="select-tag.html">Select & Tag</a></li>
                            <li><a href="knob-slider.html">Knob & Slider</a></li>
                            <li><a href="picker-tool.html">Picker</a></li>
                            <li><a href="drag-drop.html">Drag & Drop</a></li>
                            <li><a href="ui-elements.html">Elements</a></li>
                            <li><a href="tree-view.html">Tree View <span class="badge badge-red">New</span></a></li>

                        </ul>
                    </li>
                    <li>
                        <a href="timeline.html">
                            <i class="fa fa-clock-o"></i> <span>TimeLine</span>
                        </a>
                    </li>
                    <li>
                        <a href="table.html">
                            <i class="fa fa-table"></i> <span>Table</span>
                        </a>
                    </li>
                    <li>
                        <a href="notification.html">
                            <i class="fa fa-bullhorn"></i> <span>Notification</span>
                        </a>
                    </li>
                    <li>
                        <a href="note-task.html">
                            <i class="fa fa-pencil"></i> <span>Task & Note</span> <span class="badge badge-red">5</span>
                        </a>
                    </li>
                    <li>
                        <a href="calender.html">
                            <i class="fa fa-calendar-o"></i> <span>Calender</span> <span class="badge badge-red">15</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-map-marker"></i>
                            <span>Maps</span>
                        </a>
                        <ul>
                            <li><a href="googlemap.html">Google Map</a></li>
                            <li><a href="vector-maps.html">Vector Map</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-image"></i>
                            <span>Gallery</span>
                        </a>
                        <ul>
                            <li><a href="four-column-gallery.html">Four Column</a></li>
                            <li><a href="three-column-gallery.html">Three Column</a></li>
                            <li><a href="two-column-gallery.html">Two Column</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-gift"></i>
                            <span>Media</span>
                            <span class="badge badge-red">New</span>
                        </a>
                        <ul>
                            <li><a href="image-crop.html">Image Cropper</a></li>
                            <li><a href="magnify.html">Image Magnify <span class="badge badge-red">New</span></a></li>
                            <li><a href="media.html">Media Player <span class="badge badge-red">New</span></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-list-alt"></i>
                            <span>Pages</span>
                        </a>
                        <ul>
                            <li><a href="typography.html">Typography</a></li>
                            <li><a href="pricing-table.html">Pricing Table</a></li>
                            <li><a href="profile.html">Profile</a></li>
                            <li><a href="login.html">Login</a></li>
                            <li><a href="lock-screen.html">Lock Screen</a></li>
                            <li><a href="registration.html">Registration</a></li>
                            <li><a href="coming-soon.html">ComingSoon</a></li>
                            <li><a href="widget.html">Widgets</a></li>
                            <li><a href="grid.html">Grids</a></li>
                            <li><a href="panel.html">Panels</a></li>
                            <li><a href="404.html">404</a></li>
                            <li><a href="500.html">500</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-flag-o"></i>
                            <span>Icons</span>
                        </a>
                        <ul>
                            <li><a href="font-awesome.html">Font Awesome</a></li>
                            <li><a href="glyphicons.html">Glyphicons</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-flash"></i>
                            <span>Layout</span>
                        </a>
                        <ul>
                            <li><a href="blank.html">Blank Page</a></li>
                            <li><a href="minimize-left.html">Minimize Left</a></li>
                            <li><a href="maximize-right.html">Maximize Right</a></li>
                            <li><a href="with-footer.html">With Footer</a></li>
                            <li>
                                <a href="#">Color</a>
                                <ul>
                                    <li><a href="red-color.html">Red</a></li>
                                    <li><a href="blue-color.html">Blue</a></li>
                                    <li><a href="light-green-color.html">Light Green</a></li>
                                    <li><a href="black-color.html">Black</a></li>
                                    <li><a href="deep-blue-color.html">Deep Blue</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-magnet"></i>
                            <span>Multi Level Menu</span>
                        </a>
                        <ul>
                            <li><a href="javascript:void(0)">Page 1</a></li>
                            <li>
                                <a href="javascript:void(0)">Page 2</a>
                                <ul>
                                    <li><a href="javascript:void(0)">Page 2.1</a></li>
                                    <li>
                                        <a href="javascript:void(0)">Page 2.2</a>
                                        <ul>
                                            <li><a href="javascript:void(0)">Page 2.2.1</a></li>
                                            <li><a href="javascript:void(0)">Page 2.2.2</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)">Page 3</a>
                                <ul>
                                    <li><a href="javascript:void(0)">Page 3.1</a></li>
                                    <li>
                                        <a href="javascript:void(0)">Page 3.2</a>
                                        <ul>
                                            <li><a href="javascript:void(0)">Page 3.2.1</a></li>
                                            <li>
                                                <a href="javascript:void(0)">Page 3.2.2</a>
                                                <ul>
                                                    <li><a href="javascript:void(0)">Page 3.2.2.1</a></li>
                                                    <li><a href="javascript:void(0)">Page 3.2.2.2</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </aside>

            <!-- Page main section -->
            <section id="min-wrapper">
                <div id="main-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <!--Top header start-->
                                <h3 class="ls-top-header">Dashboard</h3>
                                <!--Top header end -->

                                <!--Top breadcrumb start -->
                                <ol class="breadcrumb">
                                    <li><a href="#"><i class="fa fa-home"></i></a></li>
                                    <li class="active">Dashboard</li>
                                </ol>
                                <!--Top breadcrumb start -->
                            </div>
                        </div>
                        <!-- Main Content Element  Start-->
                        <div class="row">
                            <div class="col-md-9">
                                <div class="memberBox">
                                    <div class="memberBox-header">
                                        <h5>Logged In Member</h5>
                                    </div>
                                    <div id="realTimeChart" class="flotChartRealTime widgetRealTime">

                                    </div>
                                    <div class="memberBox-details">
                                        <ul>
                                            <li>
                                                <div class="memberBox-title">
                                                    <i class="fa fa-users"></i>
                                                    <h4>Member</h4>
                                                </div>
                                                <div class="memberBox-value up"><i class="fa fa-user"></i> <span>4250</span></div>
                                            </li>
                                            <li>
                                                <div class="memberBox-title">
                                                    <i class="fa fa-eye"></i>
                                                    <h4>Visitor</h4>
                                                </div>
                                                <div class="memberBox-value down"><i class="fa fa-flag"></i> <span>9050</span></div>
                                            </li>
                                            <li>
                                                <div class="memberBox-title">
                                                    <i class="fa fa-shopping-cart"></i>
                                                    <h4>Sales</h4>
                                                </div>
                                                <div class="memberBox-value up"><i class="fa  fa-money"></i> <span>50250</span></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="current-status-widget">
                                    <ul>
                                        <li>
                                            <div class="status-box">
                                                <div class="status-box-icon label-light-green white">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </div>
                                            </div>
                                            <div class="status-box-content">
                                                <h5 id="sale-view">2129</h5>

                                                <p class="lightGreen"><i class="fa fa-arrow-up lightGreen"></i> Total sold</p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </li>
                                        <li>
                                            <div class="status-box">
                                                <div class="status-box-icon label-red white">
                                                    <i class="fa fa-download"></i>
                                                </div>
                                            </div>
                                            <div class="status-box-content">
                                                <h5 id="download-show">5340</h5>

                                                <p class="light-blue"><i class="fa fa-arrow-down light-blue"></i> Total download</p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </li>
                                        <li>
                                            <div class="status-box">
                                                <div class="status-box-icon label-lightBlue white">
                                                    <i class="fa fa-truck"></i>
                                                </div>
                                            </div>
                                            <div class="status-box-content">
                                                <h5 id="deliver-show">10490</h5>

                                                <p class="light-blue"><i class="fa fa-arrow-up light-blue"></i> Product delivered</p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </li>
                                        <li>
                                            <div class="status-box">
                                                <div class="status-box-icon label-light-green white">
                                                    <i class="fa fa-users"></i>
                                                </div>
                                            </div>
                                            <div class="status-box-content">
                                                <h5 id="user-show">132129</h5>

                                                <p class="lightGreen"><i class="fa fa-arrow-up lightGreen"></i> Total users</p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </li>
                                        <li>
                                            <div class="status-box">
                                                <div class="status-box-icon label-success white">
                                                    <i class="fa fa-github"></i>
                                                </div>
                                            </div>
                                            <div class="status-box-content">
                                                <h5 id="product-up">29</h5>

                                                <p class="text-success"><i class="fa fa-arrow-up text-success"></i> Uploaded project</p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </li>
                                        <li>
                                            <div class="status-box">
                                                <div class="status-box-icon label-light-green white">
                                                    <i class="fa fa-dollar"></i>
                                                </div>
                                            </div>
                                            <div class="status-box-content">
                                                <h5 id="income-show">10299 </h5>

                                                <p class="lightGreen"><i class="fa fa-arrow-up lightGreen"></i> Total income</p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>

                        <div class="row home-row-top">
                            <div class="col-md-3 col-sm-3 col-xs-6 col-lg-3">
                                <div class="pie-widget">
                                    <div id="pie-widget-1" class="chart-pie-widget" data-percent="73">
                                        <span class="pie-widget-count-1 pie-widget-count"></span>
                                    </div>
                                    <p>
                                        New Projects
                                    </p>
                                    <h5><i class="fa fa-bomb"></i> 240 </h5>

                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-6 col-lg-3">
                                <div class="pie-widget">
                                    <div id="pie-widget-2" class="chart-pie-widget" data-percent="93">
                                        <span class="pie-widget-count-2 pie-widget-count"></span>
                                    </div>
                                    <p>
                                        New Users
                                    </p>
                                    <h5><i class="fa fa-child"></i> 1240 </h5>

                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-6 col-lg-3">
                                <div class="pie-widget">
                                    <div id="pie-widget-3" class="chart-pie-widget" data-percent="23">
                                        <span class="pie-widget-count-3 pie-widget-count"></span>
                                    </div>
                                    <p>
                                        Total income
                                    </p>
                                    <h5><i class="fa fa-dollar"></i> 120,040.35 </h5>

                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-6 col-lg-3">
                                <div class="pie-widget">
                                    <div id="pie-widget-4" class="chart-pie-widget" data-percent="33">
                                        <span class="pie-widget-count-4 pie-widget-count"></span>
                                    </div>
                                    <p>
                                        Sale reports
                                    </p>
                                    <h5><i class="fa fa-file-excel-o"></i> 40</h5>

                                </div>
                            </div>
                        </div>

                        <div class="row home-row-top">
                            <div class="col-md-6">
                                <div class="sale-widget">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs icon-tab icon-tab-home nav-justified">

                                        <li><a href="#monthly" data-toggle="tab"><i class="fa fa-calendar-o"></i> <span>Monthly</span></a></li>
                                        <li class="active"><a href="#yearly" data-toggle="tab"><i class="fa fa-dollar"></i> <span>Yearly</span></a></li>
                                        <li><a href="#product" data-toggle="tab" data-identifier="heroDonut"><i class="fa fa-shopping-cart"></i> <span>Product</span></a></li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div class="tab-pane fade" id="monthly">
                                            <h4>Monthly Sales Report</h4>

                                            <p>In 6 month</p>

                                            <div class="monthlySale">
                                                <ul>
                                                    <li>
                                                        <div class="progress vertical bottom">
                                                            <div class="progress-bar ls-light-blue-progress six-sec-ease-in-out" role="progressbar" data-transitiongoal="25"></div>
                                                        </div>
                                                        <div class="monthName">
                                                            Jan
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="progress vertical bottom">
                                                            <div class="progress-bar progress-bar-info six-sec-ease-in-out" role="progressbar" data-transitiongoal="40"></div>
                                                        </div>
                                                        <div class="monthName">
                                                            Feb
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="progress vertical bottom">
                                                            <div class="progress-bar progress-bar-success six-sec-ease-in-out" role="progressbar" data-transitiongoal="60"></div>
                                                        </div>
                                                        <div class="monthName">
                                                            Mar
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="progress vertical bottom">
                                                            <div class="progress-bar progress-bar-warning six-sec-ease-in-out" role="progressbar" data-transitiongoal="80"></div>
                                                        </div>
                                                        <div class="monthName">
                                                            Apr
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="progress vertical bottom">
                                                            <div class="progress-bar progress-bar-danger six-sec-ease-in-out" role="progressbar" data-transitiongoal="95"></div>
                                                        </div>
                                                        <div class="monthName">
                                                            May
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="progress vertical bottom">
                                                            <div class="progress-bar  six-sec-ease-in-out" role="progressbar" data-transitiongoal="15"></div>
                                                        </div>
                                                        <div class="monthName">
                                                            Jun
                                                        </div>
                                                    </li>
                                                </ul>

                                            </div>
                                        </div>

                                        <div class="tab-pane fade in active" id="yearly">
                                            <div id="seriesToggleWidget" class="seriesToggleWidget"></div>
                                            <ul id="choicesWidget"></ul>
                                        </div>
                                        <div class="tab-pane fade" id="product">
                                            <div id="flotPieChart" class="flotPieChartWidget"></div>
                                        </div>

                                    </div>
                                    <!-- Tab End -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="v-map-widget">
                                    <div class="v-map-overlay">
                                        <ul>
                                            <li><span class="user-status is-online"></span> Online</li>
                                            <li><span class="user-status is-idle"></span> Idle</li>
                                            <li><span class="user-status is-busy"></span> Busy</li>
                                            <li><span class="user-status is-offline"></span> Offline</li>
                                        </ul>
                                    </div>
                                    <h3 class="ls-header">User Status</h3>

                                    <div id="world_map" class="world_map_home_widget">

                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row home-row-top">
                            <div class="col-md-12">
                                <!--Table Wrapper Start-->
                                <div class="table-responsive ls-table">
                                    <table class="table table-bordered table-striped table-responsive">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Info</th>
                                                <th>Progress</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>PSD Design</td>
                                                <td>Lorem ipsum dolor sit amet</td>
                                                <td class="ls-table-progress">
                                                    <div class="progress progress-striped active">
                                                        <div class="progress-bar progress-bar-success" role="progressbar" data-transitiongoal="50"></div>
                                                    </div>
                                                </td>
                                                <td><span class="label label-warning">Pending</span></td>
                                                <td>
                                                    <button class="btn btn-xs btn-info"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-xs ls-light-green-btn"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-xs ls-red-btn"><i class="fa fa-trash-o"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>PSD</td>
                                                <td>Lorem ipsum dolor sit amet</td>
                                                <td class="ls-table-progress">
                                                    <div class="progress progress-striped active">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-transitiongoal="90"></div>
                                                    </div>
                                                </td>
                                                <td><span class="label label-light-green">On Way</span></td>
                                                <td>
                                                    <button class="btn btn-xs btn-info"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-xs ls-light-green-btn"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-xs ls-red-btn"><i class="fa fa-trash-o"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>PSD Theme</td>
                                                <td>Lorem ipsum dolor sit amet</td>
                                                <td class="ls-table-progress">
                                                    <div class="progress progress-striped active">
                                                        <div class="progress-bar progress-bar-info" role="progressbar" data-transitiongoal="80"></div>
                                                    </div>
                                                </td>
                                                <td><span class="label label-warning">Pending</span></td>
                                                <td>
                                                    <button class="btn btn-xs btn-info"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-xs ls-light-green-btn"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-xs ls-red-btn"><i class="fa fa-trash-o"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Wordpress Theme</td>
                                                <td>Lorem ipsum dolor sit amet</td>

                                                <td class="ls-table-progress">
                                                    <div class="progress progress-striped active">
                                                        <div class="progress-bar progress-bar-danger" role="progressbar" data-transitiongoal="80"></div>
                                                    </div>
                                                </td>
                                                <td><span class="label label-red">Error</span></td>
                                                <td>
                                                    <button class="btn btn-xs btn-info"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-xs ls-light-green-btn"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-xs ls-red-btn"><i class="fa fa-trash-o"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>HTML Template</td>
                                                <td>Lorem ipsum dolor sit amet</td>

                                                <td class="ls-table-progress">
                                                    <div class="progress progress-striped active">
                                                        <div class="progress-bar progress-bar-warning" role="progressbar" data-transitiongoal="70"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="label label-light-green">On Way</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-xs btn-info"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-xs ls-light-green-btn"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-xs ls-red-btn"><i class="fa fa-trash-o"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>Plugin</td>
                                                <td>Lorem ipsum dolor sit amet</td>

                                                <td class="ls-table-progress">
                                                    <div class="progress progress-striped active">
                                                        <div class="progress-bar progress-bar-success" role="progressbar" data-transitiongoal="90"></div>
                                                    </div>
                                                </td>
                                                <td><span class="label label-success">Successfull</span></td>
                                                <td>
                                                    <button class="btn btn-xs btn-info"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-xs ls-light-green-btn"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-xs ls-red-btn"><i class="fa fa-trash-o"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div>
                                <!--Table Wrapper Finish-->
                            </div>
                        </div>

                        <div class="row home-row-top">
                            <div class="col-md-4 col-sm-6">
                                <div class="setting-widget">
                                    <div class="setting-widget-header">
                                        <h5 class="ls-header">Setting Widget <i class="fa fa-cog"></i></h5>
                                    </div>
                                    <div class="setting-widget-box">
                                        <ul>
                                            <li>
                                                Make invisible
                                                <div class="setting-switch">
                                                    <input class="switchCheckBox" type="checkbox" checked data-size="mini"
                                                           data-label-text="<span class='user-status is-online'></span>"
                                                           data-on-text="<span class='fa fa-check'></span>"
                                                           data-off-text="<span class='fa fa-times'></span>"
                                                           data-on-color="success" data-off-color="danger">
                                                </div>
                                            </li>
                                            <li>Self Destruct
                                                <div class="setting-switch">
                                                    <input class="switchCheckBox" type="checkbox" checked data-size="mini"
                                                           data-label-text="<i class='fa fa-gear'></i>"
                                                           data-on-text="<i class='fa fa-check'></i>"
                                                           data-off-text="<span class='fa fa-times'></span>">
                                                </div>
                                            </li>

                                            <li>Currency
                                                <div class="setting-switch">
                                                    <input class="switchCheckBox" type="checkbox" data-size="mini" checked
                                                           data-label-text="<span class='fa fa-money fa-lg'></span>"
                                                           data-on-text="<i class='fa fa-dollar'><i>"
                                                           data-off-text="<i class='fa fa-eur'><i>">
                                                </div>
                                            </li>
                                            <li>FireWall
                                                <div class="setting-switch">
                                                    <input class="switchCheckBox" type="checkbox" data-size="mini"
                                                           data-label-text="<span class='fa fa-shield'></span>"
                                                           data-on-text="<i class='fa fa-check'><i>"
                                                           data-off-text="<i class='fa fa-times'><i>"
                                                           data-on-color="success" data-off-color="danger">
                                                </div>
                                            </li>
                                            <li>Change Color
                                                <div class="setting-switch">
                                                    <div class="change-color-switch">
                                                        <ul>
                                                            <li class="default active"></li>
                                                            <li class="red-color"></li>
                                                            <li class="blue-color"></li>
                                                            <li class="light-green-color"></li>
                                                            <li class="black-color"></li>
                                                            <li class="deep-blue-color"></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="skyWeather label-light-green white">
                                    <div class="current-weather">
                                        <div class="current-weather-icon">
                                            <canvas id="rain" width="128" height="128">
                                            </canvas>
                                        </div>
                                        <div class="current-weather-details">
                                            <h2>20°c</h2>
                                            <span>Heavy Rani</span>

                                            <p>24°c / 12°c</p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="feature-weather">
                                        <ul>
                                            <li>
                                                <a href="javascript:void(0)">
                                                    <canvas id="clear-day" width="32" height="32">
                                                    </canvas>
                                                    <span>Sat</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)">
                                                    <canvas id="clear-night" width="32" height="32">
                                                    </canvas>
                                                    <span>Sun</span>
                                                </a>

                                            </li>
                                            <li>
                                                <a href="javascript:void(0)">
                                                    <canvas id="partly-cloudy-day" width="32" height="32">
                                                    </canvas>
                                                    <span>Mon</span>
                                                </a>

                                            </li>
                                            <li>
                                                <a href="javascript:void(0)">
                                                    <canvas id="cloudy" width="32" height="32">
                                                    </canvas>
                                                    <span>Tue</span>
                                                </a>

                                            </li>
                                            <li>
                                                <a href="javascript:void(0)">
                                                    <canvas id="fog" width="32" height="32">
                                                    </canvas>
                                                    <span>Wed</span>
                                                </a>

                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="social-share-box">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="ls-fb-share social-share col-md-6 col-sm-3 col-xs-6">
                                                <i class="fa fa-facebook"></i>

                                                <div class="share-overlay">
                                                    <a href="#" class="expand">4.2K</a>
                                                    <a class="close-overlay hidden">x</a>
                                                </div>
                                            </div>
                                            <div class="ls-tw-share social-share col-md-6 col-sm-3 col-xs-6">
                                                <i class="fa fa-twitter"></i>

                                                <div class="share-overlay">
                                                    <a href="#" class="expand">5.4K</a>
                                                    <a class="close-overlay hidden">x</a>
                                                </div>
                                            </div>
                                            <div class="ls-google-plus social-share col-md-6 col-sm-3 col-xs-6">
                                                <i class="fa fa-google-plus"></i>

                                                <div class="share-overlay">
                                                    <a href="#" class="expand">7.8K</a>
                                                    <a class="close-overlay hidden">x</a>
                                                </div>
                                            </div>
                                            <div class="ls-dribble-plus social-share col-md-6 col-sm-3 col-xs-6">
                                                <i class="fa fa-dribbble"></i>

                                                <div class="share-overlay">
                                                    <a href="#" class="expand">1.2K</a>
                                                    <a class="close-overlay hidden">x</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Content Element  End-->

                    </div>
                </div>

            </section>
            <!--Page main section end -->
            <!--Right hidden  section end -->
            <div id="change-color">
                <div id="change-color-control">
                    <a href="javascript:void(0)"><i class="fa fa-magic"></i></a>
                </div>
                <div class="change-color-box">
                    <ul>
                        <li class="default active"></li>
                        <li class="red-color"></li>
                        <li class="blue-color"></li>
                        <li class="light-green-color"></li>
                        <li class="black-color"></li>
                        <li class="deep-blue-color"></li>
                    </ul>
                </div>
            </div>
        </main>
    </body>
</html>