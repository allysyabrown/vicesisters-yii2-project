Search = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Search[element] = data[element];
            });
        }

        Search.setHandlers();
    },

    setHandlers: function(){

    }

    // Handlers

    // END Handlers


    // Functions

    // END Functions

}