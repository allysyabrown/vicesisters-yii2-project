AccountDialog = {

    init: function (data) {

        if (typeof data != 'undefined') {
            var attributes = [
            ];

            $.each(attributes, function (index, element) {
                if (typeof data[element] != 'undefined')
                    AccountDialog[element] = data[element];
            });
        }

        AccountDialog.setHandlers();
    },

    setHandlers: function () {

    }

}