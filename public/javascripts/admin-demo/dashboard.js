jQuery(document).ready(function ($) {
    'use strict';

    var $windowWidth = $(window).width();
    if ($windowWidth > 640) {
        if (!detectIE()) {
            autoUpdateNumber();
        }
    }
    easyPeiChartWidget();
    weatherIcon();
    socialShare();
    vectorMapWidget();
    flotChartStartPie();

    plotAccordingToChoicesDataSet(); // Series Toggle Widget chart Load
    plotAccordingToChoicesToggle(); // Series Toggle Widget chart toggle button Trigger


    flot_real_time_chart_start();
    flot_real_time_chart_start_update(); // Real time update call

    notificationCall();
    colorChanger();
});

//-------------------------------
// Easy Pie Chart Widget Start
//-------------------------------

function easyPeiChartWidget() {
    'use strict';

    $('#pie-widget-1').easyPieChart({
        animate: 2000,
        barColor: $redActive,
        scaleColor: $redActive,
        lineWidth: 5,
        easing: 'easeOutBounce',
        onStep: function (from, to, percent) {
            $(this.el).find('.pie-widget-count-1').text(Math.round(percent));
        }
    });

    $('#pie-widget-2').easyPieChart({
        animate: 2000,
        barColor: $lightGreen,
        scaleColor: $lightGreen,
        lineWidth: 5,
        easing: 'easeOutBounce',
        onStep: function (from, to, percent) {
            $(this.el).find('.pie-widget-count-2').text(Math.round(percent));
        }
    });

    $('#pie-widget-3').easyPieChart({
        animate: 2000,
        barColor: $lightBlueActive,
        scaleColor: $lightBlueActive,
        easing: 'easeOutBounce',
        lineWidth: 5,
        onStep: function (from, to, percent) {
            $(this.el).find('.pie-widget-count-3').text(Math.round(percent));
        }
    });

    $('#pie-widget-4').easyPieChart({
        animate: 2000,
        barColor: $success,
        scaleColor: $success,
        easing: 'easeOutBounce',
        lineWidth: 5,
        onStep: function (from, to, percent) {
            $(this.el).find('.pie-widget-count-4').text(Math.round(percent));
        }
    });

    // Update instance after 10 sec
    var $windowWidth = $(window).width();
    if ($windowWidth > 640) {
        setInterval(function () {
            var randomNumber = getRandomNumber();
            $('#pie-widget-1').data('easyPieChart').update(randomNumber);
            randomNumber = getRandomNumber();
            $('#pie-widget-2').data('easyPieChart').update(randomNumber);
            randomNumber = getRandomNumber();
            $('#pie-widget-3').data('easyPieChart').update(randomNumber);
            randomNumber = getRandomNumber();
            $('#pie-widget-4').data('easyPieChart').update(randomNumber);
        }, 10000);
    }
}

function getRandomNumber() {
    'use strict';

    return 1 + Math.floor(Math.random() * 100);
}

//-------------------------------
// Weather  Widget Icon Start
//-------------------------------

function weatherIcon() {
    'use strict';

    var icons = new Skycons({"color": "#fff"}),
        list = [
            "clear-day", "clear-night", "partly-cloudy-day",
            "partly-cloudy-night", "cloudy", "rain", "sleet", "snow", "wind",
            "fog"
        ],
        i;

    for (i = list.length; i--;)
        icons.set(list[i], list[i]);

    icons.play();
}

//-------------------------------
// Social Share overlay Start
//-------------------------------

function socialShare() {
    'use strict';

    $(".social-share").mouseenter(function () {
        // handle the mouseenter functionality
        $(this).addClass("overlay-hover");
    }).mouseleave(function () {
        // handle the mouseleave functionality
        $(this).removeClass("overlay-hover");
    });
}

//-------------------------------
// Vector Map Widget Start
//-------------------------------

function vectorMapWidget() {
    'use strict';

    var $mapBackgroundColor = '#fff';
    var $mapRegionStyle = $lightGreen;
    var $mapRegionStyleHover = '#58595B';
    var $mapMarkerColor = $isOnline;
    var $mapStrokeColor = $isOnline;

    $('#world_map').vectorMap({
        map: 'world_mill_en',
        normalizeFunction: 'polynomial',
        backgroundColor: $mapBackgroundColor,
        regionStyle: {
            initial: {
                fill: $mapRegionStyle
            },
            hover: {
                fill: $mapRegionStyleHover
            }
        },
        markerStyle: {
            initial: {
                fill: $mapMarkerColor,
                stroke: $mapStrokeColor,
                r: 4
            },
            hover: {
                stroke: '#f2f2f2',
                "stroke-width": 1
            }
        },
        markers: [
            {latLng: [23.780, 90.419], name: 'Dhaka, Bangladesh'},
            {latLng: [40.71, -74.00], name: 'New York'},
            {latLng: [34.05, -118.24], name: 'Los Angeles', style: {fill: $isBusy, stroke: 'none'}},
            {latLng: [41.87, -87.62], name: 'Chicago', style: {fill: $isBusy, stroke: 'none'}},
            {latLng: [29.76, -95.36], name: 'Houston'},
            {latLng: [39.95, -75.16], name: 'Philadelphia'},
            {latLng: [38.90, -77.03], name: 'Washington'},
            {latLng: [37.36, -122.03], name: 'Silicon Valley'},
            {latLng: [41.90, 12.45], name: 'Vatican City'},
            {latLng: [43.73, 7.41], name: 'Monaco'},
            {latLng: [-0.52, 166.93], name: 'Nauru'},
            {latLng: [-8.51, 179.21], name: 'Tuvalu'},
            {latLng: [43.93, 12.46], name: 'San Marino'},
            {latLng: [47.14, 9.52], name: 'Liechtenstein', style: {fill: $isBusy, stroke: 'none'}},
            {latLng: [7.11, 171.06], name: 'Marshall Islands'},
            {latLng: [17.3, -62.73], name: 'Saint Kitts and Nevis'},
            {latLng: [3.2, 73.22], name: 'Maldives'},
            {latLng: [35.88, 14.5], name: 'Malta'},
            {latLng: [12.05, -61.75], name: 'Grenada'},
            {latLng: [13.16, -61.23], name: 'Saint Vincent and the Grenadines'},
            {latLng: [13.16, -59.55], name: 'Barbados'},
            {latLng: [17.11, -61.85], name: 'Antigua and Barbuda'},
            {latLng: [-4.61, 55.45], name: 'Seychelles'},
            {latLng: [7.35, 134.46], name: 'Palau'},
            {latLng: [42.5, 1.51], name: 'Andorra'},
            {latLng: [14.01, -60.98], name: 'Saint Lucia'},
            {latLng: [6.91, 158.18], name: 'Federated States of Micronesia'},
            {latLng: [1.3, 103.8], name: 'Singapore'},
            {latLng: [1.46, 173.03], name: 'Kiribati'},
            {latLng: [-21.13, -175.2], name: 'Tonga', style: {fill: $isIdle, stroke: 'none'}},
            {latLng: [15.3, -61.38], name: 'Dominica'},
            {latLng: [-20.2, 57.5], name: 'Mauritius'},
            {latLng: [26.02, 50.55], name: 'Bahrain', style: {fill: $isIdle, stroke: 'none'}},
            {latLng: [19.082, 72.881], name: 'Mumbai,  India', style: {fill: $isOffline, stroke: 'none'}},
            {latLng: [55.749, 37.632], name: 'Russia, Moscow', style: {fill: $isBusy, stroke: 'none'}},
            {latLng: [51.629, -69.259], name: 'Rio Gallegos, Argentina'}
        ]
    });
}

//-------------------------------
// Flot Chart Pie Widget
//-------------------------------

function flotChartStartPie() {
    'use strict';

    var pieData = [],
        series = Math.floor(Math.random() * 6) + 1;

    for (var i = 0; i < series; i++) {
        pieData[i] = {
            label: "Product - " + (i + 1),
            data: Math.floor(Math.random() * 100) + 1
        }
    }
    var $flotPieChart = $('#flotPieChart');
    pieData = [
        {
            label: "Product - 1",
            data: 43
        }, {
            label: "Product - 2",
            data: 19
        }, {
            label: "Product - 3", data: 89
        }, {
            label: "Product - 4", data: 83
        }
    ];
    $.plot($flotPieChart, pieData, {
        series: {
            pie: {
                show: true,
                radius: 1,
                label: {
                    show: true,
                    radius: 3 / 4,
                    formatter: labelFormatter,
                    background: {
                        opacity: 0.5,
                        color: '#000'
                    }
                }
            }
        },
        legend: {
            show: false
        },
        colors: [$fillColor2, $lightBlueActive, $redActive, $blueActive, $brownActive, $greenActive]
    });
}

function labelFormatter(label, series) {
    return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
}

//-------------------------------
// Change color Widget
//-------------------------------

function colorChanger() {
    'use strict';

    var $fullContent = $("body");
    $('.change-color-switch ul li ').click(function () {

        $fullContent.removeClass('black-color');
        $fullContent.removeClass('blue-color');
        $fullContent.removeClass('deep-blue-color');
        $fullContent.removeClass('red-color');
        $fullContent.removeClass('light-green-color');
        $fullContent.removeClass('default');
        $('.change-color-switch ul li ').removeClass('active');

        if (!($(this).hasClass('active'))) {
            var className = $(this).attr('class');
            $fullContent.addClass(className);
            $(this).addClass('active');
        }
    });
}

//------------------------------------
// Notification On dashboard Start
//------------------------------------

function notificationCall() {
    'use strict';

    $.amaran({
        content: {
            message: 'New Mail Arrived',
            size: '4 new mail in inbox',
            file: '',
            icon: 'fa fa-envelope-o'
        },
        theme: 'default ok',
        position: 'bottom right',
        inEffect: 'slideRight',
        outEffect: 'slideBottom',
        closeButton: true,
        delay: 5000
    });
    setTimeout(function () {
        $.amaran({
            content: {
                img: '/media_assets/images/fickle/demo/avatar-80.png',
                user: 'New Chat Message',
                message: 'Hi, How are you ? please knock me when you arrived <i class="fa fa-smile-o"></i>'
            },
            theme: 'user',
            position: 'bottom left',
            inEffect: 'slideRight',
            outEffect: 'slideBottom',
            closeButton: true,
            delay: 5000
        });
        setTimeout(function () {
            $.amaran({
                content: {
                    message: 'Can\'t deliver the product',
                    size: '32 Kg',
                    file: 'H: 32 Road: 21, Chicago, NY 3210',
                    icon: 'fa fa fa-truck'
                },
                theme: 'default error',
                position: 'top right',
                inEffect: 'slideRight',
                outEffect: 'slideTop',
                closeButton: true,
                delay: 5000
            });
        }, 5000)
    }, 5000);
}

//---------------------------------
// Auto Update Number Dashboard
//---------------------------------

function autoUpdateNumber() {
    'use strict';

    var countUpOptions = {
        useEasing: true, // toggle easing
        useGrouping: true, // 1,000,000 vs 1000000
        separator: ',', // character to use as a separator
        decimal: '.' // character to use as a decimal
    };

    var productUp = new countUp('product-up', 0, 39, 0, 9.5, countUpOptions);
    productUp.start();

    var incomeShow = new countUp('income-show', 0, 10299.30, 2, 9.5, countUpOptions);
    incomeShow.start();

    var userShow = new countUp('user-show', 0, 132129, 0, 5.5, countUpOptions);
    userShow.start();

    var deliverShow = new countUp('deliver-show', 0, 10490, 1, 6.5, countUpOptions);
    deliverShow.start();

    var downloadShow = new countUp('download-show', 0, 5340, 0, 9.5, countUpOptions);
    downloadShow.start();
    var saleView = new countUp('sale-view', 0, 2129, 0, 9.5, countUpOptions);
    saleView.start();
}

//-------------------------------
// Flot Real time chart
//-------------------------------

var data = [],
    totalPoints = 500;

function getRandomData() {
    'use strict';

    if (data.length > 0)
        data = data.slice(1);

    // Do a random walk
    while (data.length < totalPoints) {
        var prev = data.length > 0 ? data[data.length - 1] : 50,
            y = prev + Math.random() * 10 - 5;

        if (y < 0) {
            y = 0;
        } else if (y > 100) {
            y = 99;
        }

        data.push(y);
    }

    // Zip the generated y values with the x values

    var res = [];
    for (var i = 0; i < data.length; ++i) {
        res.push([i, data[i]])
    }

    return res;
}

var plot;
function flot_real_time_chart_start() {
    'use strict';

    plot = $.plot("#realTimeChart", [getRandomData()], {
        series: {
            lines: {
                show: true,
                lineWidth: 1,
                fill: true,
                fillColor: {

                    colors: [
                        {
                            opacity: 0.2
                        },
                        {
                            opacity: 0.1
                        }
                    ]
                }
            },
            shadowSize: 0
        },
        colors: ['#1FB5AD'],
        yaxis: {
            min: 0,
            max: 150
        },
        xaxis: {
            show: false
        },
        grid: {
            tickColor: $fillColor1,
            borderWidth: 0
        }
    });
}

function flot_real_time_chart_start_update() {
    'use strict';

    var updateInterval = 300;

    plot.setData([getRandomData()]);

    // Since the axes don't change, we don't need to call plot.setupGrid()

    plot.draw();
    setTimeout(flot_real_time_chart_start_update, updateInterval);
}

//------------------------------------
// Flot According To Choices chart
//------------------------------------

var choiceContainer;
var datasets;

function plotAccordingToChoicesDataSet() {
    'use strict';

    datasets = {
        "a": {
            label: "Product A",
            data: [
                [2010, 0],
                [2011, 40],
                [2012, 60],
                [2013, 80],
                [2014, 70]
            ]
        },
        "b": {
            label: "Product B",
            data: [
                [2010, 30],
                [2011, 45],
                [2012, 80],
                [2013, 75],
                [2014, 90]
            ]
        },
        "c": {
            label: "Product C",
            data: [
                [2010, 10],
                [2011, 20],
                [2012, 30],
                [2013, 40],
                [2014, 80]
            ]
        }
    };

    var i = 0;
    $.each(datasets, function (key, val) {
        val.color = i;
        ++i;
    });

// insert checkboxes
    choiceContainer = $("#choicesWidget");
    $.each(datasets, function (key, val) {
        //<input class="switchCheckBox" type="checkbox" checked data-size="mini">
        choiceContainer.append("<li><input class='switchCheckBox' data-size='mini' type='checkbox' name='" + key +
        "' checked='checked' id='id" + key + "'></input>" +
        "<br/><label class='switch-label' for='id" + key + "'>"
        + val.label + "</label></li>");
    });

    plotAccordingToChoices();
}

function plotAccordingToChoices() {
    'use strict';

    var data = [];
    choiceContainer.find("input:checked").each(function () {
        var key = $(this).attr("name");
        if (key && datasets[key]) {
            data.push(datasets[key]);
        }
    });

    if (data.length > 0) {
        $.plot("#seriesToggleWidget", data, {
            highlightColor: $lightGreen,
            yaxis: {
                min: 0,
                show: true,
                color: '#E3DFD8'
            },
            xaxis: {
                tickDecimals: 0,
                show: true,
                color: '#E3DFD8'

            },
            colors: [$lightGreen, $redActive, $lightBlueActive, $greenActive],
            grid: {
                borderColor: '#E3DFD8'
            }
        });
    }
    $(".switchCheckBox").bootstrapSwitch();
}
function plotAccordingToChoicesToggle() {
    'use strict';

    $(".switchCheckBox").on('switchChange.bootstrapSwitch', plotAccordingToChoices());
}
