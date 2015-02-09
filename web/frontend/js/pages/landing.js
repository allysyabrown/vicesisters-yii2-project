Landing = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Landing[element] = data[element];
            });
        }

        Landing.setHandlers();
    },

    setHandlers: function(){

    }

}