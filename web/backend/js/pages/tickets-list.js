TicketsList = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    TicketsList[element] = data[element];
            });
        }

        TicketsList.setHandlers();
    },

    setHandlers: function(){

    }

    // Handlers

    // END Handlers


    // Functions

    // END Functions

}