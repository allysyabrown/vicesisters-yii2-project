Payment = {

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Payment[element] = data[element];
            });
        }

        Payment.setHandlers();
    },

    setHandlers: function(){
        Payment.affixSettings();
    },

    affixSettings: function(){
        var distance = $('.side-settings-block').offset().top;
        var $window = $(window);
        $window.scroll(function() {
            if ( ($window.scrollTop()) - 1000 >= distance ) {
                $('.side-settings-block').css("margin-top","0");
            }
            else{
                $('.side-settings-block').css("margin-top","65");
            }
        });
    }
    // Handlers



    // END Handlers


    // Functions


    // END Functions

}