PaymentsList = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    PaymentsList[element] = data[element];
            });
        }

        PaymentsList.setHandlers();
    },

    setHandlers: function(){

    }

    // Handlers

    // END Handlers


    // Functions

    // END Functions

}