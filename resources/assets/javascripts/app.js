var jQueryInit = false;

/* jQuery Setup */
if (jQuery != undefined && typeof jQuery == 'function') {
    jQuery.noConflict();
    jQueryInit = true;
    jQuery.ajaxSetup({
        async: true,
        cache: true,
        data: "",
        dataType: "json",
        global: true,
        ifModified: true,
        timeout: 15000,
        type: "POST",
        url: window.location.href,

        error: function () {
            appClient.Form.notifications.log("Unspecified error occured! Possible causes are <i>connection problems</i> or <i>invalid response from server</i>.");

            return 0;
        }
    });
}

/**
 * Basic extend function.
 */
function extend(B, A) {
    // Intermediate function
    function I() {
    }

    // The rest...
    I.prototype = A.prototype;
    B.prototype = new I;
    B.prototype.constructor = B;
    B.prototype.parent = A;
}

/**
 * Javascript Singleton pattern.
 */
Function.prototype.singleton = function () {
    if (this._singleton === undefined) {
        // Create an Intermediate constructor to avoid problems during initialization of the generic construct itself
        function I() {
        }

        // Assign the same prototype to extend itself
        I.prototype = this.prototype;

        // Create a singleton instance
        this._singleton = new I;

        // No need to re-declare the instance constructor, as we are extending the contructor itself. Let's call it with apply();
        this.apply(this._singleton, arguments);
    }

    return this._singleton;
};

/**
 * Main Javascript object.
 *
 * @returns {Object} Object handler.
 */
var AppClient = function () {
    this.Helpers = {
        /**
         * Checks whether given arrays are the same.
         *
         * @param {Array} a1 First array.
         * @param {Array} a2 Second array.
         *
         * @returns {Boolean} TRUE if both arrays are the same, FALSE otherwise.
         *
         * @see http://stackoverflow.com/questions/784012/javascript-equivalent-of-phps-in-array
         */
        arrayCompare: function (a1, a2) {
            if (a1.length != a2.length) {
                return false;
            }
            var length = a2.length;
            for (var i = 0; i < length; i++) {
                if (a1[i] !== a2[i]) {
                    return false;
                }
            }
            return true;
        },

        /**
         * Checks if a value exists in an array.
         * {function(needle=, haystack=, ?strict=)}
         *
         * @param {*} needle The searched value.
         * @param {Array} haystack The haystack-array.
         * @param {Boolean} strict Optional: If TRUE then the types of the needle will also be checked. Defaults to FALSE.
         *
         * @returns {Boolean} TRUE if needle is found in the array, FALSE otherwise.
         *
         * @see http://stackoverflow.com/questions/784012/javascript-equivalent-of-phps-in-array
         */
        inArray: function (needle, haystack, strict) {
            var length = haystack.length;
            if (strict === undefined) strict = false;

            for (var i = 0; i < length; i++) {
                if (typeof haystack[i] == 'object') {
                    if (appClient.arrayCompare(haystack[i], needle)) {
                        return true;
                    }
                }
                else {
                    if (strict) {
                        if (haystack[i] === needle) {
                            return true;
                        }
                    }
                    else {
                        if (haystack[i] == needle) {
                            return true;
                        }
                    }
                }
            }
            return false;
        },

        /**
         * @param {Object} objectToClone
         *
         * @returns {Object}
         */
        cloneObject: function (objectToClone) {
            var newObj = (objectToClone instanceof Array) ? [] : {};
            for (var i in objectToClone) {
                if (objectToClone.hasOwnProperty(i) && typeof objectToClone[i] == "object") {
                    newObj[i] = appClient.cloneObject(objectToClone[i]);
                } else {
                    newObj[i] = objectToClone[i];
                }
            }

            return newObj;
        },

        /**
         * @param {Object|Array} arrayOrObject
         *
         * @returns {Boolean|undefined} TRUE if an entity is empty, FALSE if not; undefined if not applicable.
         */
        isEmpty: function (arrayOrObject) {
            // Return undefined if we are dealing with non-object
            if (typeof arrayOrObject != 'object') {
                return undefined;
            }

            // The null
            if (arrayOrObject === null) return true;

            // Arrays + jQuery objects
            if (arrayOrObject instanceof Array || arrayOrObject instanceof jQuery) return arrayOrObject.length === 0;

            // Generic objects
            return Object.getOwnPropertyNames(arrayOrObject).length <= 0;
        },

        /**
         * @param {String|String[]} string
         *
         * @returns {String|String[]}
         */
        decodeHtmlEntities: function (string) {
            if (!(string instanceof Array)) {
                string = string.replace('&#38;', '&');
                string = string.replace('&#60;', '<');
                string = string.replace('&#62;', '>');
                string = string.replace('&#34;', '"');
                string = string.replace('&#39;', "'");
                string = string.replace('&#33;', '!');
                string = string.replace("&#36;", "$");
                string = string.replace('&#46;&#46;/', '../');
                string = string.replace('&lt;', '<');
                string = string.replace('&gt;', '>');
                string = string.replace('&quot;', '"');
            } else {
                for (var i in string) {
                    if (string.hasOwnProperty(i)) string[i] = this.decodeHtmlEntities(string[i]);
                }
            }

            return string;
        }
    };

    this.Form = {
        /**
         * @var {*} Working form object.
         */
        self: null,

        notifications: humane.create({baseCls: 'humane-jackedup', addnCls: 'humane-jackedup-error'})
    };

    this.Session = {
        flashMessages: function (messages, type) {
            if (jQueryInit === true) {
                jQuery(document).ready(function (/* event */) {
                    var parsedMessages,
                        humaneJsHandle = humane.create({
                            baseCls: 'humane-jackedup',
                            addnCls: 'humane-jackedup-' + type,
                            timeout: 5000,
                            waitForMove: true,
                            timeoutAfterMove: 1500
                        });

                    try {
                        parsedMessages = jQuery.parseJSON(messages);
                    } catch (e) {
                        parsedMessages = messages;
                    }

                    if (typeof parsedMessages === 'string') {
                        humaneJsHandle.log(parsedMessages);
                    } else if (typeof parsedMessages === 'object') {
                        var messageForNotification = "";
                        for (var idx in parsedMessages) {
                            if (parsedMessages.hasOwnProperty(idx)) {
                                if (isNaN(idx)) {
                                    jQuery("input[name='" + idx + "']")
                                        .attr('aria-describedby', 'helpBlock_' + idx)
                                        .parent()
                                        .addClass('has-error has-feedback')
                                        .append('<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>')
                                        .append('<span class="help-block" id="helpBlock_' + idx + '">' + parsedMessages[idx] + '</span>');
                                }
                                messageForNotification += "<li>" + parsedMessages[idx] + "</li>";
                            }
                        }
                        humaneJsHandle.log("<ul>" + messageForNotification + "</ul>");
                    }
                });
            }
        }
    };

    return {
        constructor: AppClient
    };
};

var appClient = AppClient.singleton();

if (jQueryInit === true) {
    jQuery(document).ready(function (/* event */) {
        // jQuery-UI Accordion
        if ('function' == typeof jQuery().accordion) {
            jQuery(".ui-accordion").accordion({
                active: false,
                animated: 'easeslide',
                autoHeight: false,
                clearStyle: false,
                collapsible: true,
                icons: {
                    'header': "",
                    'headerSelected': ""
                },
                navigation: true
            });
        }
    });
}
