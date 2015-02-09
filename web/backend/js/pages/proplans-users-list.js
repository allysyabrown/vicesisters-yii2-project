ProplansUsersList = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    ProplansUsersList[element] = data[element];
            });
        }

        ProplansUsersList.setHandlers();
    },

    setHandlers: function(){

    }

    // Handlers

    // END Handlers


    // Functions

    // END Functions

}