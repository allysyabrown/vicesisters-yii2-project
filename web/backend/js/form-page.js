FormPage = {

    tagsInputId: '.tags-input',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    FormPage[element] = data[element];
            });
        }

        FormPage.setHandlers();
    },

    setHandlers: function(){
        FormPage.tagsInput();
    },

    // Handlers

    tagsInput: function(){
        var input = $(FormPage.tagsInputId);
        if(input.length){
            input.tagsInput();
        }
    }

    // END Handlers


    // Functions

    // END Functions

}