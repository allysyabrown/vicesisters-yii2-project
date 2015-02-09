Dashboard = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Dashboard[element] = data[element];
            });
        }

        Dashboard.setHandlers();
    },
    
    setHandlers: function(){

    }

    // Handlers

    // END Handlers


    // Functions

    // END Functions

}