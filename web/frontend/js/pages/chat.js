Chat = {

    iframeId: '#vice-chat',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Chat[element] = data[element];
            });
        }

        Chat.setHandlers();
    },

    setHandlers: function(){
        Chat.userClick();
    },

    // Handlers

    userClick: function(){
        //$('#iframeID').contents().find('#someID')
    }

    // END Handlers

}