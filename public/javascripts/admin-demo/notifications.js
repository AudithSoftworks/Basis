jQuery(document).ready(function($) {
    'use strict';

    notify_js_trigger_call();
    amaran_trigger_call();
    bootbox_trigger_call();
    humane_trigger_call();
});
function notificationCenter(icon, type, message, position) {
    'use strict';

    $.notify.addStyle('custom', {
        html: "<div>" +
        "<div class='clearfix'>" +
        "<div class='customNotification alert " + type + "'>" +
        "<span class='" + icon + "'></span>  " + message +
        "</div>" +
        "</div>" +
        "</div>"
    });
    $.notify('Message', {
        style: 'custom',
        className: 'superblue',
        autoHide: true,
        globalPosition: position
    });
}


function bootbox_trigger_call(){
    'use strict';

    $('.alertBox').click(function () {
        bootbox.alert("Hello world!", function () {
            notificationCenter(
                'glyphicon glyphicon-ok',
                'alert-success',
                '<strong>Close</strong> alert box',
                'top right'
            );
        });
    });
    $('.confirmBox').click(function () {
        bootbox.confirm("Are you sure?", function (result) {
            if (result) {
                var html = 'Ok';
            } else {
                var html = 'Cancel';
            }
            notificationCenter(
                'glyphicon glyphicon-ok',
                'alert-success',
                '<strong>Close</strong> You press ' + html,
                'top right'
            );
        });
    });

    $('.promptBox').click(function () {
        bootbox.prompt("What is your name?", function (result) {
            if (result === null) {
                notificationCenter(
                    'glyphicon glyphicon-trash',
                    'alert-danger',
                    '<strong>Wrong</strong> Answer',
                    'top right'
                );
            } else {
                notificationCenter(
                    'glyphicon glyphicon-user',
                    'alert-success',
                    '<strong>Hi</strong> ' + result,
                    'top right'
                );
            }
        });
    });
    $('.dialogBox').click(function () {
        bootbox.dialog({
            message: "I am a custom dialog",
            title: "Custom title",
            buttons: {
                success: {
                    label: "Success!",
                    className: "btn-success",
                    callback: function () {
                        $.notify("Press On Success", "success");
                    }
                },
                danger: {
                    label: "Danger!",
                    className: "btn-danger",
                    callback: function () {
                        $.notify("Press on Danger!", "error");
                    }
                },
                main: {
                    label: "Click ME!",
                    className: "btn-primary",
                    callback: function () {
                        $.notify("Press On Click ME!", "info");
                    }
                }
            }
        });
    });
}

function notify_js_trigger_call(){
    'use strict';

    $('.successNotification').click(function () {
        $.notify("Successfully notified", "success", {
            autoHide: false
        });
    });
    $('.errorNotification').click(function () {
        $.notify("Get some error in here", "error");
    });
    $('.warningNotification').click(function () {
        $.notify("Get some warning in here", "warn");
    });
    $('.infoNotification').click(function () {
        $.notify("Get some info in here", "info");
    });

    $('.topRight').click(function () {
        notificationCenter(
            'glyphicon glyphicon-ok',
            'alert-success',
            'Top Right Notification',
            'top right'
        );
    });

    $('.bottomRight').click(function () {
        notificationCenter(
            'glyphicon glyphicon-ok',
            'alert-success',
            'Bottom Right Notification',
            'bottom right'
        );
    });
    $('.middleRight').click(function () {
        notificationCenter(
            'glyphicon glyphicon-ok',
            'alert-info',
            'Middle Right Notification',
            'right middle'
        );
    });

    $('.centerBottom').click(function () {
        notificationCenter(
            'glyphicon glyphicon-ok',
            'alert-info',
            'Center Bottom Notification',
            'bottom center'
        );
    });
    $('.bottomLeft').click(function () {
        notificationCenter(
            'glyphicon glyphicon-ok',
            'alert-success',
            'Bottom Left Notification',
            'bottom left'
        );
    });

    $('.middleLeft').click(function () {
        notificationCenter(
            'glyphicon glyphicon-ok',
            'alert-success',
            'Middle Left Notification',
            'left middle'
        );
    });

    $('.topLeft').click(function () {
        notificationCenter(
            'glyphicon glyphicon-ok',
            'alert-success',
            'Top Left Notification',
            'Top left'
        );
    });

    $('.topCenter').click(function () {
        notificationCenter(
            'glyphicon glyphicon-ok',
            'alert-success',
            'Top Center Notification',
            'top center'
        );
    });

    $('.successCustomNotification').click(function () {
        notificationCenter(
            'glyphicon glyphicon-ok',
            'alert-success',
            '<strong>Well done!</strong> You successfully read this important alert message.',
            'top right'
        );
    });
    $('.errorCustomNotification').click(function () {
        notificationCenter(
            'glyphicon glyphicon-fire',
            'alert-danger',
            '<strong>Oh snap!<strong> Change a few things up and try submitting again.',
            'top right'
        );
    });
    $('.warningCustomNotification').click(function () {
        notificationCenter(
            'glyphicon glyphicon-warning-sign',
            'alert-warning',
            '<strong>Warning!<strong> Better check yourself, you\'re not looking too good.',
            'top right'
        );
    });
    $('.infoCustomNotification').click(function () {
        notificationCenter(
            'glyphicon glyphicon-eye-open',
            'alert-info',
            '<strong>Heads up!<strong> This alert needs your attention, but it\'s not super important. ',
            'top right'
        );
    });

    $('.emailCustomNotification').click(function () {
        notificationCenter(
            'fa fa-envelope',
            'alert-warning',
            '3 new mail in you inbox',
            'bottom right'
        );
    });
}

function amaran_trigger_call(){
    'use strict';

    $.amaran({
        content:{
            img:'assets/images/demo/avatar-80.png',
            title:'Want to see Notification?',
            content: 'Click the button'
        },
        theme:'readmore red',
        position:'bottom right',
        inEffect:'slideRight',
        outEffect:'slideBottom',
        delay:5000

    });
    $('.successMessage').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.errorMessage').click(function(){
        $.amaran({
            content:{
                message:'Download Failed !',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-times'
            },
            theme:'default error',
            position:'bottom right',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.warningMessage').click(function(){
        $.amaran({
            content:{
                message:'Can\'t deliver the product',
                size:'32 Kg',
                file:'H: 32 Road: 21, Chicago, NY 3210',
                icon:'fa fa fa-truck'
            },
            theme:'default warning',
            position:'bottom right',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.infoMessage').click(function(){
        $.amaran({
            content:{
                message:'4 products sold out',
                size:'Product A x4',
                file: 'Purchased By: Jon Doe',
                icon:'fa fa-shopping-cart'
            },
            theme:'default blue',
            position:'bottom right',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.greenMessage').click(function(){
        $.amaran({
            content:{
                message:'2 Downloads Completed',
                size:'2.8 GB',
                file: 'Now you can use it',
                icon:'fa fa fa-cloud-download'
            },
            theme:'default green',
            position:'bottom right',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.bottomRightMessage').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.topRightMessage').click(function(){
        $.amaran({
            content:{
                message:'Can\'t deliver the product',
                size:'32 Kg',
                file:'H: 32 Road: 21, Chicago, NY 3210',
                icon:'fa fa fa-truck'
            },
            theme:'default warning',
            position:'top right',
            inEffect:'slideRight',
            outEffect:'slideTop'
        });
    });
    $('.bottomLeftMessage').click(function(){
        $.amaran({
            content:{
                message:'4 products sold out',
                size:'Product A x4',
                file: 'Purchased By: Jon Doe',
                icon:'fa fa-shopping-cart'
            },
            theme:'default blue',
            position:'bottom left',
            inEffect:'slideLeft',
            outEffect:'slideBottom'
        });
    });
    $('.topLeftMessage').click(function(){
        $.amaran({
            content:{
                message:'2 Downloads Completed',
                size:'2.8 GB',
                file: 'Now you can use it',
                icon:'fa fa fa-cloud-download'
            },
            theme:'default green',
            position:'top left',
            inEffect:'slideLeft',
            outEffect:'slideTop'
        });
    });

    $('.userMessage').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                user:'John Doe 1',
                message:'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate, ducimus?'
            },
            theme:'user',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.userGreenMessage').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                user:'John Doe 4',
                message:'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate, ducimus?'
            },
            theme:'user green',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.userYellowMessage').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                user:'John Doe 2',
                message:'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate, ducimus?'
            },
            theme:'user yellow',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.userBlueMessage').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                user:'John Doe 4',
                message:'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate, ducimus?'
            },
            theme:'user blue',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.readMore').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                title:'Do you like this post ?',
                content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, quisquam!'
            },
            theme:'readmore',
            inEffect:'slideRight',
            outEffect:'slideBottom',
            closeButton:true

        });
    });
    $('.successReadMore').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                title:'Do you like this post ?',
                content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, quisquam!'
            },
            theme:'readmore success',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.errorReadMore').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                title:'Do you like this post ?',
                content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, quisquam!'
            },
            theme:'readmore red',
            inEffect:'slideRight',
            outEffect:'slideBottom'

        });
    });
    $('.warningReadMore').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                title:'Do you like this post ?',
                content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, quisquam!'
            },
            theme:'readmore warning',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.infoReadMore').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                title:'Do you like this post ?',
                content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, quisquam!'
            },
            theme:'readmore info',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.greenReadMore').click(function(){
        $.amaran({
            content:{
                img:'assets/images/demo/avatar-80.png',
                title:'Do you like this post ?',
                content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, quisquam!'
            },
            theme:'readmore green',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.inFadeIn').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'fadeIn',
            outEffect:'slideBottom'
        });
    });
    $('.inSlideLeft').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideLeft',
            outEffect:'slideBottom'
        });
    });
    $('.inSlideRight').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideRight',
            outEffect:'slideBottom'
        });
    });
    $('.inSlideTop').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideTop',
            outEffect:'slideBottom'
        });
    });
    $('.inSlideBottom').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideBottom',
            outEffect:'slideBottom'
        });
    });
    $('.outFadeOut').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'fadeIn',
            outEffect:'fadeOut'
        });
    });
    $('.outSlideLeft').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'fadeIn',
            outEffect:'slideLeft'
        });
    });
    $('.outSlideRight').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'fadeIn',
            outEffect:'slideRight'
        });
    });
    $('.outSlideTop').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'fadeIn',
            outEffect:'slideTop'
        });
    });
    $('.outSlideBottom').click(function(){
        $.amaran({
            content:{
                message:'Your Download is Ready!',
                size:'1.4 GB',
                file:'my_birthday.mp4',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'fadeIn',
            outEffect:'slideBottom'
        });
    });
    $('.delayStyle').click(function(){
        $.amaran({
            content:{
                message:'Delay 10s to close',
                size:'You can change',
                file:'as you wish',
                icon:'fa fa-smile-o'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideBottom',
            outEffect:'slideBottom',
            closeButton:true,
            delay:10000
        });
    });
    $('.stickyStyle').click(function(){
        $.amaran({
            content:{
                message:'Sticky Notification',
                size:'click to close it',
                file:'',
                icon:'fa fa-fire'
            },
            theme:'default ok',
            position:'top right',
            inEffect:'slideBottom',
            outEffect:'slideBottom',
            sticky:true,
            closeButton:true
        });
    });
    $('.closeOnClickStyle').click(function(){
        $.amaran({
            content:{
                message:'Cant\'t close by click it',
                size:'wait 5s',
                file:'',
                icon:'fa fa-stop'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideBottom',
            outEffect:'slideTop',
            closeOnClick:false,
            delay:5000
        });
    });
    $('.closeButtonStyle').click(function(){
        $.amaran({
            content:{
                message:'With Close button Notification',
                size:'Press that close',
                file:'',
                icon:'fa fa-times-circle-o'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideBottom',
            outEffect:'slideTop',
            closeButton:true,
            sticky:true
        });
    });
    $('.createNotificationsStyle').click(function(){

        $.amaran({
            content:{
                message:'Sample Notification 1',
                size:'Fro clearing all Notification press Clear All button',
                file:'',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideBottom',
            outEffect:'slideTop',
            closeButton:true,
            sticky:true
        });
        $.amaran({
            content:{
                message:'Sample Notification 2',
                size:'Fro clearing all Notification press Clear All button',
                file:'',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideBottom',
            outEffect:'slideTop',
            closeOnClick:false,
            sticky:true
        });
        $.amaran({
            content:{
                message:'Sample Notification 3',
                size:'Fro clearing all Notification press Clear All button',
                file:'',
                icon:'fa fa-download'
            },
            theme:'default ok',
            position:'bottom right',
            inEffect:'slideBottom',
            outEffect:'slideTop',
            closeButton:true,
            sticky:true
        });
    });
    $('.clearAllStyle').click(function(){
        $.amaran({
            content:{
                message:'Clear All',
                size:'Clear all previous notification',
                file:'',
                icon:'fa fa-check'
            },
            theme:'default ok',
            clearAll:true
        });
    });
}

function humane_trigger_call(){
    'use strict';

    $(".bigNotification").click(function () {
        var bigbox = humane.create({ timeout: 4000, baseCls: 'humane-bigbox' });
        bigbox.log('<span class="fa fa-inbox"></span> Notifier')
    });

    $(".bigTopRight").click(function () {
        var libnotify = humane.create({ timeout: 4000, baseCls: 'humane-libnotify' });
        libnotify.log("<span class='fa fa-user'> Welcome Back");
    });


    $(".bigSequenceBox").click(function () {
        var bigbox = humane.create({baseCls: 'humane-bigbox', timeout: 1000});
        bigbox.error = bigbox.spawn({addnCls: 'humane-bigbox-error'});
        bigbox.log('Oh!').error('No!');
    });

    $(".bigTopCenterSuccess").click(function () {
        var jacked = humane.create({baseCls: 'humane-jackedup', addnCls: 'humane-jackedup-success'});
        jacked.log("<span class='glyphicon glyphicon-ok'></span> Successfully updated the server");
    });
    $(".bigTopCenterError").click(function () {
        var jacked = humane.create({baseCls: 'humane-jackedup', addnCls: 'humane-jackedup-error'});
        jacked.log("<span class='fa fa-lock'></span> Locked !");
    });

    $(".bigError").click(function () {
        var bigError = humane.create({baseCls: 'humane-bigbox', addnCls: 'humane-bigbox-error'});
        bigError.log("<span class='fa fa-lock'></span> Locked !");
    });

    $(".bigSuccess").click(function () {
        var bigError = humane.create({baseCls: 'humane-bigbox', addnCls: 'humane-bigbox-success'});
        bigError.log("<span class='glyphicon glyphicon-ok'></span> Updated Successfully");
    });
}
