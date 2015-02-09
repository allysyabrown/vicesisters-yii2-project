Gallery = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Gallery[element] = data[element];
            });
        }
        

        Gallery.setHandlers();
    },

    setHandlers: function(){

    }

}