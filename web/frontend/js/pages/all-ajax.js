AllAjax = {

    lastHotMessageUrl: '',
    lastFeedbackUrl: '',
    hotMessagesUrl: '',
    feedbackUrl: '',
    lastVerifiedUrl: '',
    lastHotMessageTime: 10,
    lastFeedBackTime: 10,
    lastVerifiedTime: 10,

    getMessagesAjax: null,


    FeedLiObj: null,

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
                'lastHotMessageUrl',
                'lastFeedbackUrl',
                'hotMessagesUrl',
                'feedbackUrl',
                'lastVerifiedUrl',
                'lastHotMessageTime',
                'lastFeedBackTime',
                'lastVerifiedTime'
            ];

            $.each(attributes, function (index, element) {
                if (typeof data[element] != 'undefined')
                    AllAjax[element] = data[element];
            });
        }
    },

    // Functions

    getTopVips: function(){
        Main.ajx({
            url: MainPage.vipsUrl,
            type: 'GET',
            success: function (json) {
                MainPage.bubbleManager.prepareGilrlsData(json.vips);
                MainPage.bubbleManager.showBubbles();
                MainPage.bubbleManager.initRotation();
            }
        });
    },

    getHotMessages: function(){

        //version in which 4 messages
        //
        //AllAjax.getMessagesAjax = Main.ajx({
        //    url: AllAjax.hotMessagesUrl,
        //    type: 'GET',
        //    success: function(json){
        //        if(json.html){
        //            $('#escort-messages').html(json.html);
        //        }
        //    }
        //});

        var menu = $('.main-header-bg');
        var header = $('.header-js');
        var headWrap = $('.head-wrap');
        var escLink = $('#escort-messages li');
        var escMsg = $('#escort-messages');
        var escBtn = $('.add-top-members-post-btn');
        var membersArea = $('.top-members-area');

        var distance = $('.top-members-area').offset().top;
        var $window = $(window);
        $window.scroll(function() {
            if ( ($window.scrollTop()) + 88 >= distance ) {
                menu.addClass('top-zero');
                header.addClass('top-zero');
                headWrap.addClass('head-wrap-height');
                escMsg.addClass('fixed-ul-message');
                escBtn.addClass('fixed-div-message');
                escLink.addClass('fixed-li-message');
                membersArea.addClass('transparent-fixed');
            }
            else{
                menu.removeClass('top-zero');
                header.removeClass('top-zero');
                headWrap.removeClass('head-wrap-height');
                escMsg.removeClass('fixed-ul-message');
                escBtn.removeClass('fixed-div-message');
                escLink.removeClass('fixed-li-message');
                membersArea.removeClass('transparent-fixed');
            }
        });

//        $( window ).scroll(function() {
//            var docViewTop = $(window).scrollTop();
//
//            if (docViewTop > 471) {
//                menu.addClass('top-zero');
//                header.addClass('top-zero');
//                escMsg.addClass('fixed-ul-message');
//                escBtn.addClass('fixed-div-message');
//                escLink.addClass('fixed-li-message');
//                membersArea.addClass('transparent');
//            }
//            else {
//                menu.removeClass('top-zero');
//                header.removeClass('top-zero');
//                escMsg.removeClass('fixed-ul-message');
//                escBtn.removeClass('fixed-div-message');
//                escLink.removeClass('fixed-li-message');
//                membersArea.removeClass('transparent');
//            }
//        });
    },

    getLastHotMessage: function(lastMessId){
        Main.ajx({
            url: AllAjax.lastHotMessageUrl,
            type: 'GET',
            data: {
                lastId: lastMessId
            },
            success: function(json){
                if(json.html){
                    AllAjax.addNewMessageToList(json.html);

                    //verstion with 4 messages
                    //AllAjax.updateNewMassege(json.html);
                }
            }
        });
    },

    getVipPersons: function(){
        Main.ajx({
            url: MainPage.premiumsUrl,
            type: 'GET',
            success: function(json){
                if(json.html){
                    $(MainPage.vipPersonsID).html(json.html);
                }
            }
        });
    },

    getTopprofiles: function(){
        Main.ajx({
            url: MainPage.topprofilesUrl,
            type: 'GET',
            success: function(json){
                if(json.html){
                    $(MainPage.topprofilesID).html(json.html);
                }
            }
        });
    },

    getLastVerified: function(){
        Main.ajx({
            url: AllAjax.lastVerifiedUrl,
            type: 'GET',
            success: function(json){
                if(json.html){
                    $('#last-verified').html(json.html);
                }
            }
        });
    },

    getFeedbacks: function(){
        Main.ajx({
            url: AllAjax.feedbackUrl,
            type: 'GET',
            success: function(json){
                if(json.html){
                    $('#escort-feedback').html(json.html);
                }
            }
        });
    },

    getNewFeedback: function(){
        Main.ajx({
            url: AllAjax.lastFeedbackUrl,
            type: 'GET',
            data: AllAjax.getLastFeedId(),
            success: function(json){
                AllAjax.FeedLiObj = json.html;
                if(json.html){
                    AllAjax.showNewFeedBack(json.html);
                }
            }
        });
    },

    addNewMessageToList: function(li){
        $(li).insertAfter($(MainPage.hotMessagesListId).children().eq(3));
    },

    updateNewMassege: function(){
        var vipMailHide = 2500;
        var content = $('.top-members-area .content');
        var nextNumber = 4;

        //version with 4 messages
        //var liObj = $(li);
        //
        //liObj.appendTo(content);

        $(MainPage.hotMessagesListId).children().first().addClass('hide-me');
        $(MainPage.hotMessagesListId).children().eq(nextNumber).addClass('revert-me');

        setTimeout(function () {
            $(MainPage.hotMessagesListId).children().eq(nextNumber).removeClass('revert-me');
        }, 300);

        setTimeout(function () {
            var first = $(MainPage.hotMessagesListId).children().first();
            first.removeClass('hide-me');
            first.appendTo(first.parent());

            //version with 4 messages
            //content.find('li').first().remove();
        }, vipMailHide);
    },

    getLastFeedId: function(){
        var lastId = $('.feedback-area ul li:last').attr('data-id');
        return 'lastId='+lastId;
    },

    showNewFeedBack: function(html){
        var content = $('.feedback-area ul');
        content.find('li').first().addClass('hide-me');
        content.find('li').eq(2).addClass('revert-me-2');
        content.find('li').eq(3).addClass('revert-me');

        setTimeout(function(){
            content.find('li').eq(2).removeClass('revert-me-2');
            content.find('li').eq(3).removeClass('revert-me');
        }, 500);

        setTimeout(function(){
            content.find('li').first().remove();
            $(html).appendTo(content);
            setTimeout(function(){
                content.find('li').last().removeClass('hide-me');
            }, 500);
        }, 2000);
    },

    // END Functions


    // Timeout functions

    lastHotMessageTimeOut: function(){
        setInterval(function(){
            var elemsId = [];
            var lastId = 0;

            $(MainPage.hotMessagesListId).children().each(function(i,el){
                elemsId.push($(el).data('id'));
            });

            lastId = Math.max.apply(null,elemsId);
            AllAjax.getLastHotMessage(lastId);

            //version with 4 messages
            //var content = $('.top-members-area .content');
            //var lastId = content.find('li').last().attr('data-id');
            //AllAjax.getLastHotMessage(lastId);
        }, AllAjax.lastHotMessageTime);
    },

    lastVerifiedTimeOut: function(){
        setInterval(function(){
            var lastVerifiedContainer = $('.last-certified ul');

            lastVerifiedContainer.fadeOut(4000);
            AllAjax.getLastVerified();

            lastVerifiedContainer.fadeIn(4000);
        }, AllAjax.lastVerifiedTime);
    },

    lastFeedBackTimeOut: function () {
        setInterval(function () {
            AllAjax.getNewFeedback();
        }, AllAjax.lastFeedBackTime);
    }

    // END Timeout functions

};