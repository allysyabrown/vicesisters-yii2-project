FeedMessages = {

    addCommentId: '.add-feed-comment',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    FeedMessages[element] = data[element];
            });
        }

        FeedMessages.setHandlers();
    },

    setHandlers: function(){
        FeedMessages.setAddComment();
    },


    /* Handlers */

    setAddComment: function(){
        $(FeedMessages.addCommentId).on('click', function(){
            $($(this).data('id')).css({display: 'block'});
        });
    }

    /* END Handlers */

}