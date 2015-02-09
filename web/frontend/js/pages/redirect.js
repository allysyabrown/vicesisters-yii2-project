Redirect = {

    page: '',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
                'page'
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Redirect[element] = data[element];
            });
        }

        Redirect.setHandlers();
    },

    setHandlers: function(){
        Redirect.redirect();
    },

    // Handlers

    redirect: function(){
        window.location.replace(Redirect.page);
    }

    // END Handlers


    // Functions

    // END Functions

}