Feed = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Feed[element] = data[element];
            });
        }

        Feed.setHandlers();
    },

    setHandlers: function(){

    }

}