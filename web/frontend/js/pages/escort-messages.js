EscortMessages = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    EscortMessages[element] = data[element];
            });
        }

        EscortMessages.setHandlers();
    },

    setHandlers: function(){

    }

}