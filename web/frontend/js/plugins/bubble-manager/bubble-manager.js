function BubbleManager() {
    var self = this;
    var bubblesSelector = '.bubbles-area ul';
    var bubblesPopupSelector = '.bubbles-popup';
    var bubblesView;
    var bubblePopupView;
    var girlsData;
    var openedConf = {};
    var pagingOffset = 0;
    var bubblesCount = 24;

    this.init = function () {
        bubblesView = $(bubblesSelector);
        bubblePopupView = $(bubblesPopupSelector);
        bubblesView.find('li').click(function () {

            var bblId = $(this).attr('data-bblid');
            $(this).toggleClass('toCenter');
            self.openPopup(bblId);


        });
        self.getData();
        //Выводит выбранный бабл;
        bubblePopupView.click(function () {
            if ($(this).hasClass('shadow')) {
                self.closePopup();
            }
        });
    };

    this.getData = function () {

    };

    this.initRotation = function () {
        setInterval(function () {
            $('.bubbles-area ul li').addClass('toCenter');
            setTimeout(function () {
                self.showBubbles();
            }, 500);
            setTimeout(function () {
                $('.bubbles-area ul li').removeClass('toCenter');
            }, 2000);
        }, 30000);
    };

    this.prepareGilrlsData = function (data) {
        girlsData = data;
    };

    this.showBubbles = function () {
        for (var i = 0; i < bubblesCount; i++) {
            var girl = girlsData[i + pagingOffset];
            var li = bubblesView.find('.pos_' + (i + 1));
            li.attr('data-bblid', girl.id);
            li.find('img').attr('src', girl.ava);
            if (i + pagingOffset == girlsData.length - 1) {
                pagingOffset = -i - 1;
            }
        }

        pagingOffset = pagingOffset + bubblesCount;
    };

    this.openPopup = function (girlId) {
        for (var i = 0; i < girlsData.length; i++) {
            if (girlsData[i].id == girlId) {
                var currentGirl = girlsData[i];
                var onlineText;
                var isOnline;

                if(currentGirl.isOnline){
                    onlineText = 'Online';
                    isOnline = 'yes';
                } else {
                    onlineText = 'Offline';
                    isOnline = 'no';
                }

                bubblePopupView.find('.current-girl-ava').attr('src', currentGirl.ava);
                bubblePopupView.find('.status_text').text(onlineText);
                bubblePopupView.find('.status-indicator').addClass(isOnline);
                bubblePopupView.find('.user-name').text(currentGirl.fullName);
                bubblePopupView.find('.city-name').text(currentGirl.cityName);
                bubblePopupView.find('.message-btn').attr('data-url', '/get-message-popup-' + currentGirl.id);
                bubblePopupView.find('.profile-btn').attr('href', 'profile-' + currentGirl.id);
                bubblePopupView.find('.user-status').attr('href', 'profile-' + currentGirl.id);
                bubblePopupView.find('.photo-btn').attr('href', 'profile-' + currentGirl.id + '/gallery');
                $('.main-wrapper').addClass('blur');
//                if (currentGirl.onlineStatus) {
//                    bubblePopupView.find('.status-indicator').toggleClass('yes', true);
//                    bubblePopupView.find('.status-indicator').toggleClass('no', false);
//                } else {
//                    bubblePopupView.find('.status-indicator').toggleClass('yes', false);
//                    bubblePopupView.find('.status-indicator').toggleClass('no', true);
//                }


                $('.blur-wrap').fadeIn('slow');
                $('.shadow.bubbles-popup').fadeIn(500);
                $('.active-b-wrap').fadeIn(500);
                $('.active-bubble').fadeIn(500);
                $('.circles-bg').fadeIn(1500);
                $('.circles-bg-2').fadeIn(1500);
                $('.circles-bg-3').fadeIn(1000);
                $('.circles-bg-4').fadeIn('slow');
                $('.circles-bg-5').fadeIn(1000);
                $('.current-girl-ava').fadeIn(1000);
                $('.active-b-wrap ul li .sex-btn').removeClass('start');
                $('.active-b-wrap ul li .vip-btn').removeClass('start');
                $('.active-b-wrap ul li .message-btn').removeClass('start');
                $('.active-b-wrap ul li .profile-btn').removeClass('start');
                $('.active-b-wrap ul li .photo-btn').removeClass('start');
//                console.log('_____________________________________________________- start blur');

                break;
            }
        }

    };
    this.closePopup = function () {
        $('.active-b-wrap ul li .sex-btn').addClass('start');
        $('.active-b-wrap ul li .vip-btn').addClass('start');
        $('.active-b-wrap ul li .message-btn').addClass('start');
        $('.active-b-wrap ul li .profile-btn').addClass('start');
        $('.active-b-wrap ul li .photo-btn').addClass('start');
        $('.main-wrapper').removeClass('blur');
        bubblePopupView.fadeOut();
        bubblesView.find('li').toggleClass('toCenter', false);
    };
}