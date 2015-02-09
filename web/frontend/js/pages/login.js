Login = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Login[element] = data[element];
            });
        }

        Login.setHandlers();
    },

    setHandlers: function(){

    }

}