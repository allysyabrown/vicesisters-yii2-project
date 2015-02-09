AdultCheck = {
    init: function () {
        var cookie = this.getCookie();

        if (cookie['adult']) {
            if (cookie['adult'] == 1) {
//                window.location = 'http://vicesisters.local/registration';
            } else {
//                window.location = 'http://vicesisters.local/age';
            }
        } else {
//                window.location = 'http://vicesisters.local/age';
        }
    },
    IamAdult: function () {
        this.setCookie('adult', 1, {expires: 1000 * 60 * 60 * 4});
    },
    IamNotAdult: function () {
        this.setCookie('adult', 0, {expires: 1000 * 60 * 60 * 4});
    },
    getCookie: function () {
        if (!document.cookie || document.cookie.length < 2)
            return {};

        var res = {}, coo,
                cArr = document.cookie.split(/;\s?/);
        for (var i = 0; i < cArr.length; i++) {
            coo = cArr [i].split('=');
            res[coo[0]] = decodeURIComponent(coo[1]);
        }
        return res;
    },
    setCookie: function (name, value, options) {
        options = options || {};

        var expires = options.expires;

        if (typeof expires == "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }

        value = encodeURIComponent(value);

        var updatedCookie = name + "=" + value;

        for (var propName in options) {
            updatedCookie += "; " + propName;
            var propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }

        document.cookie = updatedCookie;
    }
};





