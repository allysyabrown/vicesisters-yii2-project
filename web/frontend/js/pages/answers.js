Answers = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Answers[element] = data[element];
            });
        }

        Answers.setHandlers();
    },

    setHandlers: function(){

    }

}