UserProfile = {
    privateMessageFormId: '#MessageForm-form',
    sendMsgButton: '.send-msg',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    UserProfile[element] = data[element];
            });
        }

        UserProfile.setHandlers();
    },

    setHandlers: function(){
        UserProfile.setMessage();
        UserProfile.setPopups();
    },
    setMessage: function(){
        $(UserProfile.sendMsgButton).on('click', function(){
            Main.openPopup();
            Main.unsetPopupButtons();
        });
    },
    setPopups: function(){
        $(document).on('mouseup', function(e){
            var container = $(Main.popupContainer);

            if (!container.is(e.target)
                && container.has(e.target).length === 0)
            {
                Main.closePopup();
            }
        });

        $(document).on('submit', UserProfile.privateMessageFormId, function(){
            Main.closePopup();
        });
    }

    // Handlers

    // END Handlers


    // Functions

    // END Functions

}