@section('document-body-header')
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
                                        <span class="label label-red"><i class="fa fa-check white"></i></span>
                                        You have 4 pending tasks.
                                        <span class="date">Just now</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-light-green"><i class="fa fa-bar-chart-o"></i></span>
                                        Finance Report for year 2013
                                        <span class="date">30 min</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-lightBlue"><i class="fa fa-shopping-cart"></i></span>
                                        New order received with
                                        <span class="date">45 min</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-lightBlue"><i class="fa fa-user"></i></span>
                                        5 pending membership
                                        <span class="date">50 min</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-red"><i class="fa fa-bell"></i></span>
                                        Server hardware upgraded
                                        <span class="date">1 hr</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <span class="label label-blue"><i class="fa fa-briefcase"></i></span>
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
                                        <span class="easyPieChart" data-percent="90"><span class="easyPiePercent"></span></span>
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
                                    <figure class="progress">
                                        <div class="progress-bar ls-light-blue-progress six-sec-ease-in-out" data-transitiongoal="10"></div>
                                    </figure>

                                    <h4>Plugin setup</h4>
                                    <figure class="progress progress-striped active">
                                        <div class="progress-bar six-sec-ease-in-out ls-light-green-progress" data-transitiongoal="20"></div>
                                    </figure>

                                    <h4>Post New Article</h4>
                                    <figure class="progress progress-striped active">
                                        <div class="progress-bar ls-yellow-progress six-sec-ease-in-out" data-transitiongoal="80"></div>
                                    </figure>

                                    <h4>Create New User</h4>
                                    <figure class="progress progress-striped active">
                                        <div class="progress-bar ls-red-progress six-sec-ease-in-out" data-transitiongoal="100"></div>
                                    </figure>
                                </fieldset>
                            </fieldset>
                        </form>
                    </li>

                    <li role="menuitem"><a href=""><i class="fa fa-lock"></i></a></li>

                    <li role="menuitem"><a href=""><i class="fa fa-power-off"></i></a></li>
                </ul>
            </nav>
        </header>
@stop
