Dialogs = {

    scrollbarId: '.nano',
    messageContent: '.msg-scroll-area',
    messageList: '#messages-list',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Dialogs[element] = data[element];
            });
        }

        Dialogs.setHandlers();
    },

    setHandlers: function(){
        Dialogs.setScrollbar();
    },

    // Handlers

    setScrollbar: function(){
        NanoScrollerExt.init();

        $(Dialogs.scrollbarId).nanoScroller({
            preventPageScrolling: true,
            alwaysVisible: true
        });

        Dialogs.scrollToBottom();
    },

    // END Handlers


    // Functions

    scrollToBottom: function(){
        var offsetTop = $(Dialogs.messageList).height() + 500;
        $(Dialogs.messageContent).scrollTop(offsetTop);
    }


    // END Functions

}