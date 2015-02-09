Age = {
    name: '',
    ageWrapperId: '.age-wrapper',
    mainWrap: '.main-wrap',
    init: function (data) {
        if (typeof data != 'undefined') {
            var attributes = [
                'name'
            ];

            $.each(attributes, function (index, element) {
                if (typeof data[element] != 'undefined')
                    Age[element] = data[element];
            });
        }

        Age.setHandlers();
    },
    setHandlers: function () {
        //Age.ageMain();
    },
    ageMain: function () {
        $(Age.ageWrapperId).hide();

        $(Age.mainWrap).css({
            //'-webkit-filter': 'blur(9px)',
            //'filter': 'blur(8px)'
        });

        $(Age.ageWrapperId).fadeIn('slow');

        $(window).scroll(function () {

            $('.age-wrapper').stop().animate({
                'top': window.scrollY + 'px',
                'margine-top': -243 + 'px'
            });
        });

        $(window).scroll(function () {

            $('.age-content').stop().animate({
                'top': 50 % +window.scrollY + 'px'
            }, 1000);

        });

        $('.go-away-btn').click(function () {
            AdultCheck.IamNotAdult();

//            var cookie = AdultCheck.getCookie();
        });

        $('.enter-the-site-btn').click(function () {
            AdultCheck.IamAdult();

//            var cookie = AdultCheck.getCookie();
        });
    }


};

