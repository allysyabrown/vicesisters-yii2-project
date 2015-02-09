About = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    About[element] = data[element];
            });
        }

        About.setHandlers();
    },

    setHandlers: function(){

    }

    // Handlers



    // END Handlers

}