Help = {

    messageFormId: '#HotMessageForm-form',
    escortTagsId: '#escortaccountform-extended input:checkbox',
    formSubmitId: '.save-settings-btn',
    formId: '#EscortAccountForm-form',
    settMenuSelectorId: '.side-settings-block .side-menu',
    settingsId: '.escort-account-settings',
    showPage: '.escort-account',
    addTravelButtonId: '.add-to-travel-list-btn',
    travelsId: '.user-travel-list ul',
    travelsSelectorId: '#escorttravelsform-travels option:selected',
    travelsHiddenId: '#escorttravelsform-travelslist',
    removeFormTravelListId: '.remove-city',
    travelsDelimiter: ':',
    nonSwitch: 'non-switch',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [

            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Help[element] = data[element];
            });
        }

        Help.setHandlers();
    },

    setHandlers: function(){
        Help.menuSelectors();
    },

    // Handlers

    menuSelectors: function(){
        $('.content .side-menu').removeClass(Main.activeClass);
        $('.escort-account-settings').fadeOut();
        $('.side-menu[data-id=".privacyPolicy-new"]').addClass(Main.activeClass);
        $('.privacyPolicy-new').fadeIn();

        $(Help.settMenuSelectorId).on('click', function(){
            if($(this).hasClass(Help.nonSwitch))
                return true;

            $(Help.settMenuSelectorId).removeClass(Main.activeClass);
            $(this).addClass(Main.activeClass);

            $(Help.settingsId).hide();
            $($(this).data('id')).show();
        });
    },


    // END Handlers

}