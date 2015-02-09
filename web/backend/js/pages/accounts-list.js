AccountsList = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    AccountsList[element] = data[element];
            });
        }

        AccountsList.setHandlers();
    },

    setHandlers: function(){

    }

    // Handlers

    // END Handlers


    // Functions

    // END Functions

}