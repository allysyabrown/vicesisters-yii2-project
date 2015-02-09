Contact = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Contact[element] = data[element];
            });
        }

        Contact.setHandlers();
    },

    setHandlers: function(){

    }

    // Handlers



    // END Handlers

}