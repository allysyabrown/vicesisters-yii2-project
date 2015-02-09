UserAccount = {

    changeAvaId: '.user-ava',
    backBtnId: '.back-btn',
    cropperId: '.crop-ava',

    changeAvaUrl: '',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
                'changeAvaUrl'
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    UserAccount[element] = data[element];
            });
        }

        UserAccount.setHandlers();
    },

    setHandlers: function(){
        //UserAccount.changeAva();
        UserAccount.initCropper();
    },


    // Handlers

    changeAva: function(){
        $(UserAccount.changeAvaId).on('click', function(){

        });
    },

    initCropper: function(){
        Crop.init({
            popupId: UserAccount.changeAvaId,
            cropDoneFunction: function(imageSource){
                if(imageSource){
                    $('.shadow.crop-ava, .shadow').fadeOut();
                    $('.popup-step-1').fadeOut();
                    $('.popup-step-2').fadeOut();
                    $(Frontend.regionPopup).removeClass('region');
                    $(Frontend.headWrap).removeClass('blur-add');
                    $(Frontend.contentWrap).removeClass('blur-add');
                    $("html").css("overflow-y","scroll");

                    Main.ajx({
                        url: UserAccount.changeAvaUrl,
                        data: {image: imageSource},
                        success: function(json){
                            if(!json.error){
                                location.reload();
                            }else{
                                Main.addNotify(json.error);
                            }
                        }
                    });
                }
            }
        });

        $(UserAccount.backBtnId).on('click', function(){
            $(UserAccount.cropperId).hide();
        });
    }

    // END Handlers

    // Functions

    // END Functions

}