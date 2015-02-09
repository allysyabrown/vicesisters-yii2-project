Signup = {
    mainWrap: '.main-wrap',
    logWrap: '.log-wrapper',
    registrationButton: '.reg-btn',
    loginButton: '.login-btn',
    joinButton: '.join-btn',
    registrationForm: '#reg-form',
    loginForm: '#login-form',
    loginButtons: '.log-buttons',
    openLoginForm: 0,
    openRegForm: 0,
    init: function (data) {
        if (typeof data != 'undefined') {
            var attributes = [
                'mainWrap',
                'logWrap',
                'registrationButton',
                'loginButton',
                'joinButton',
                'registrationForm',
                'loginForm',
                'loginButtons',
                'openLoginForm',
                'openRegForm'
            ];
            $.each(attributes, function (index, element) {
                if (typeof data[element] != 'undefined')
                    Signup[element] = data[element];
            });
        }

        this.setHandlers();
    },
    setHandlers: function () {
        this.regMain();
        this.setForms();
        this.setShowLoginForm();
        this.setShowRegForm();
        //this.setWindowScrol();
    },
    // Handlers

    regMain: function () {
        // убрать Help Block;
//        $('.help-block').hide();
        $(Signup.logWrap).hide();
        $(Signup.mainWrap).css({
            //'-webkit-filter': 'blur(9px)',
            //'filter': 'blur(8px)'
        });
        $(Signup.logWrap).fadeIn('slow');
    },
    setForms: function () {
        if (this.openLoginForm) {
            Signup.showLoginForm();
        } else if (this.openRegForm) {
            Signup.showRegForm();
        }
    },
    setShowLoginForm: function () {
        $(Signup.loginButton).click(function () {
            Signup.showLoginForm();
        });
    },
    setShowRegForm: function () {
        $(Signup.registrationButton).on('click', function () {
            Signup.showRegForm();
        });
    },
    setWindowScrol: function () {
        $(window).scroll(function () {
            $('.age-wrapper').stop().animate({
                'top': window.scrollY + 'px',
                'margine-top': -243 + 'px'
            });
        });
        $(window).scroll(function () {
            $('.log-content').stop().animate({
                'top': 50 % +window.scrollY + 'px'
            }, 1000);
        });
    },
//    regInputsPlaceholders: function () {
//        $('.form-group.field-registrationform-user_name input').attr('placeholder', 'Name ... ');
//        $('.form-group.field-registrationform-email input').attr('placeholder', 'Email ... ');
//        $('.form-group.field-registrationform-password input').attr('placeholder', 'Password ...');
//        $('.form-group.field-registrationform-retypepassword input').attr('placeholder', 'Repeat Password ...');
//    },
//    logInputsPlaceholders: function () {
//        $('.form-group.field-loginform-username input').attr('placeholder', 'Name ... ');
//        $('.form-group.field-loginform-password input').attr('placeholder', 'Password ...');
//    },
// END Handlers


// Private methods



    showLoginForm: function () {
        $(Signup.loginButtons).fadeOut('slow');
        $(Signup.registrationForm).fadeOut('slow');
        $(Signup.loginForm).fadeIn('slow');
        $('.log-btn-lines').fadeIn('slow');
    },
    showRegForm: function () {
        $(Signup.loginButtons).fadeOut('slow');
        $(Signup.registrationForm).fadeIn('slow');
        $('.log-btn-lines').fadeIn('slow');
    },
    setShowLogBtn: function () {
        $(Signup.joinButton).click(function () {
            $(Signup.loginButtons).fadeIn('slow');
            $(Signup.registrationForm).fadeOut('slow');
            $(Signup.loginForm).fadeOut('slow');
            $('.log-btn-lines').fadeOut('slow');
        });
    }

// END Private methods
};




//        regInputsPlaceholders: function () {
//        $('.form-group.field-registrationform-user_name input').attr('placeholder', 'Name ... ');
//                $('.form-group.field-registrationform-email input').attr('placeholder', 'Email ... ');
//                $('.form-group.field-registrationform-password input').attr('placeholder', 'Password ...');
//                $('.form-group.field-registrationform-retypepassword input').attr('placeholder', 'Repeat Password ...');
//        },
//        logInputsPlaceholders: function () {
//        $('.form-group.field-loginform-username input').attr('placeholder', 'Name ... ');
//                $('.form-group.field-loginform-password input').attr('placeholder', 'Password ...');
//        }
//Register.logInputsPlaceholders();
//Register.regInputsPlaceholders();




//
//        Register = {
//        mainWrap: '.main-wrap',
//                logWrap: '.log-wrapper',
//                registrationButton: '.reg-btn',
//                loginButton: '.login-btn',
//                joinButton: '.join-btn',
//                registrationForm: '#reg-form',
//                loginForm: '#login-form',
//                loginButtons: '.log-buttons',
//                init: function (data) {
//                if (typeof data != 'undefined') {
//
//                var attributes = [
//                ];
//                        $.each(attributes, function (index, element) {
//                        if (typeof data[element] != 'undefined')
//                                this[element] = data[element];
//                        });
//                }
//
//                this.setHandlers();
//                },
//                setHandlers: function () {
//                this.regMain();
//                },
//                regMain: function () {
////        убрать label;
//                $('#login-form label').hide();
//                        $('#reg-form label').hide();
////        убрать Help Block;
//                        $('.help-block').hide();
//                        $(Register.logWrap).hide();
//                        $(Register.mainWrap).css({
//                '-webkit-filter': 'blur(9px)',
//                        'filter': 'blur(8px)'
//                });
//                        $(Register.logWrap).fadeIn('slow');
//                        $(Register.registrationButton).click(function () {
//                $(Register.loginButtons).fadeOut('slow');
//                        $(Register.registrationForm).fadeIn('slow');
//                        $('.log-btn-lines').fadeIn('slow');
//                        Register.regInputsPlaceholders();
//                });
//                        $(Register.loginButton).click(function () {
//                $(Register.loginButtons).fadeOut('slow');
//                        $(Register.registrationForm).fadeOut('slow');
//                        $(Register.loginForm).fadeIn('slow');
//                        $('.log-btn-lines').fadeIn('slow');
//                        Register.logInputsPlaceholders();
//                });
//                        $(Register.joinButton).click(function () {
//                $(Register.loginButtons).fadeIn('slow');
//                        $(Register.registrationForm).fadeOut('slow');
//                        $(Register.loginForm).fadeOut('slow');
//                        $('.log-btn-lines').fadeOut('slow');
//                });
//                        $(window).scroll(function () {
//
//                $('.age-wrapper').stop().animate({
//                'top': window.scrollY + 'px',
//                        'margine-top': - 243 + 'px'
//                });
//                });
//                        $(window).scroll(function () {
//
//                $('.log-content').stop().animate({
//                'top': 50 % + window.scrollY + 'px'
//                }, 1000);
//                });
//                },
//                regInputsPlaceholders: function () {
//                $('.form-group.field-registrationform-user_name input').attr('placeholder', 'Name ... ');
//                        $('.form-group.field-registrationform-email input').attr('placeholder', 'Email ... ');
//                        $('.form-group.field-registrationform-password input').attr('placeholder', 'Password ...');
//                        $('.form-group.field-registrationform-retypepassword input').attr('placeholder', 'Repeat Password ...');
//                },
//                logInputsPlaceholders: function () {
//                $('.form-group.field-loginform-username input').attr('placeholder', 'Name ... ');
//                        $('.form-group.field-loginform-password input').attr('placeholder', 'Password ...');
//                }
//
//        };