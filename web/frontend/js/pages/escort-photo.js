EscortPhoto = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    EscortPhoto[element] = data[element];
            });
        }

        EscortPhoto.setHandlers();
    },

    setHandlers: function(){

    }

}