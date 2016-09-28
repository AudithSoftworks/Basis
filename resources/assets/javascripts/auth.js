require('../sass/auth.scss');

require('jquery');
require('bootstrap-sass/assets/javascripts/bootstrap.js');
require('jquery-validation');
require('js-cookie');
require('bootstrap-switch');

var Login = function () {
    var $loginForm = jQuery('.login-form');
    var $passwordReminderForm = jQuery('.forget-form');
    var $registrationForm = jQuery('.register-form');

    var handleLogin = function () {
        $loginForm.validate({
            errorElement: 'span', // default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                email: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
                email: {
                    required: "Email is required."
                },
                password: {
                    required: "Password is required."
                }
            },

            /**
             * Displays error alert on form submit
             */
            invalidHandler: function (/* event, validator */) {
                $('.alert-danger', $loginForm).show();
            },

            /**
             * Hightlight error inputs
             * @param element
             */
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error'); // Set error class to the control group
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function (error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function (form) {
                form.submit();
            }
        });

        $('.login-form input').keypress(function (e) {
            if (e.which == 13) {
                if ($loginForm.validate().form()) {
                    $loginForm.submit();
                }
                return false;
            }
        });
    };

    var handleForgetPassword = function () {
        $passwordReminderForm.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },

            messages: {
                email: {
                    required: "Email is required."
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit

            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function (error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function (form) {
                form.submit();
            }
        });

        $('.forget-form input').keypress(function (e) {
            if (e.which == 13) {
                if ($passwordReminderForm.validate().form()) {
                    $passwordReminderForm.submit();
                }
                return false;
            }
        });
    };

    var handleRegister = function () {
        $registrationForm.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true
                },
                password_confirmation: {
                    equalTo: "#register_password"
                },
                tnc: {
                    required: true
                }
            },

            messages: { // custom messages for radio buttons and checkboxes
                tnc: {
                    required: "Please accept TNC first."
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit

            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function (error, element) {
                if (element.attr("name") == "tnc") { // insert checkbox errors after the container
                    error.insertAfter($('#register_tnc_error'));
                } else if (element.closest('.input-icon').size() === 1) {
                    error.insertAfter(element.closest('.input-icon'));
                } else {
                    error.insertAfter(element);
                }
            },

            submitHandler: function (form) {
                form[0].submit();
            }
        });

        $('.register-form input').keypress(function (e) {
            if (e.which == 13) {
                if ($registrationForm.validate().form()) {
                    $registrationForm.submit();
                }
                return false;
            }
        });
    };

    return {

        /*
         |----------------------------------------------
         | Main function to initiate the module
         |----------------------------------------------
         */

        init: function () {
            handleLogin();
            handleForgetPassword();
            handleRegister();
        }
    };

}();

jQuery(document).ready(function () {
    Login.init();
});
