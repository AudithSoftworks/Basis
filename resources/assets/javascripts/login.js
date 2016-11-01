require('../sass/auth.scss');

require('jquery');
require('bootstrap-sass/assets/javascripts/bootstrap.js');
require('jquery-validation');
require('js-cookie');
require('bootstrap-switch');

var Login = function () {
    var handleLogin = function () {
        var $loginForm = jQuery('.login-form');
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

    return {

        /*
         |----------------------------------------------
         | Main function to initiate the module
         |----------------------------------------------
         */

        init: function () {
            handleLogin();
        }
    };

}();

jQuery(document).ready(function () {
    Login.init();
});
