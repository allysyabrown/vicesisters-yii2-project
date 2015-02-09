<?php

return [
            // <-- Index -->
    'signup'                                => 'index/signup',
    'registration'                          => 'index/registration',
    'login'                                 => 'index/login',
    'logout'                                => 'index/logout',
    'age'                                   => 'index/age',
    'set-age'                               => 'index/setage',
    'set-language-<lang:\w+>'               => 'index/language',
    'testing-<escortId:\d+>'                => 'index/test',
    'reset-search-params'                   => 'index/resetsearch',

            // <-- Main -->
    'main'                                  => 'main/index',

            // <-- Tickets -->
    'ticket'                                => 'ticket/index',
    'ticket/add'                            => 'ticket/add',
    'ticket/send'                           => 'ticket/send',
    'ticket-form'                           => 'ticket/usertoadminform',
    'ticket-<id:\d+>'                       => 'ticket/open',
    'send-ticket'                           => 'ticket/sendticket',

            // <-- Account -->
    'dialog-<code:\d+\-\d+>'                => 'account/dialog',
    'dialogs'                               => 'account/dialogs',
    'favorites'                             => 'account/favorites',
    'cities-by-country'                     => 'account/citiesbycountry',
    'cities-by-state'                       => 'account/citiesbystate',
    'feed'                                  => 'account/feed',
    'account/changepassword'                => 'account/changepassword',
    'account/uploadavatar'                  => 'account/uploadavatar',
    'set-travels-list'                      => 'account/settravelslist',
    'account/changetravellist'              => 'account/changetravellist',
    'account/add-private-message-<id:\d+>'  => 'account/addprivatemessage',
    'account/addfavorite-<id:\d+>'          => 'account/addfavorite',
    'account/removefavorite-<id:\d+>'       => 'account/removefavorite',
    'account/<page:\w+>'                    => 'account/index',
    'account'                               => 'account/index',

            // <-- User -->
    'my-page'                               => 'user/index',
    'user-profile-<id:\d+>'                 => 'user/profile',
    'change-user-ava'                       => 'user/changeavatar',

            // <-- Answers -->
    'answers'                               => 'answers/index',

            // <-- Escort -->
    'profile-<id:\d+>/<page:\w+>'           => 'escort/profile',
    'profile-<id:\d+>'                      => 'escort/profile',
    'get-top-message-cost'                  => 'escort/topmessagecost',
    'add-hot-message'                       => 'escort/addhotmessage',
    'add-feed-message'                      => 'escort/addfeedmessage',
    'add-feed-comment-<id:\d+>'             => 'escort/addfeedcomment',
    'escort/add-feedback'                   => 'escort/addfeedback',
    'females'                               => 'escort/females',
    'males'                                 => 'escort/males',
    'shemales'                              => 'escort/shemales',
    'vips-list'                             => 'escort/vips',
    'escort-list'                           => 'escort/list',
    'show-more-<sex:\w+>'                   => 'escort/getmore',
    'escort/uploadphoto'                    => 'escort/uploadphoto',
    'remove-photo-<id:\d+>'                 => 'escort/removephoto',
    'search-escorts'                        => 'escort/search',
    'find-more-by-params'                   => 'escort/searchmore',
    'escorts-city-<id:\d+>'                 => 'escort/listincity',
    'escort-more-feeds-<id:\d+>'            => 'escort/morefeeds',
    
            // <-- Payment -->
    'payment'                               => 'payment/index',
    'payment/bitcoin'                       => 'payment/bitcoin',
    'payment/bitcoin/confirm/<user:\d+>/<secret:\w+>'               => 'payment/bitcoinconfirm',
    'payment/bitcoin/info'                  => 'payment/bitcoininfo',
    'payment/bitcoin/money'                 => 'payment/bitcoinmoney',
    'payment/wu'                            => 'payment/wu',
    'payment/wu/add-payment'                => 'payment/wuaddpayment',

            // <-- Photo -->
    'photo-gallery-<escortId:\d+>'          => 'photo/gallery',
    'escort-photo-<id:\d+>-<escortId:\d+>'  => 'photo/index',
    'prev-photo-<id:\d+>-<escortId:\d+>'    => 'photo/prev',
    'next-photo-<id:\d+>-<escortId:\d+>'    => 'photo/next',

            // <-- Chat -->
    'chat'                                  => 'chat/index',
    'new-chat-message'                      => 'chat/newmessage',

            // <-- Ajax -->
    'ajax-feedback-<id:\d+>'                => 'ajax/feedback',
    'ajax-feedback-list'                    => 'ajax/feedbacklist',
    'ajax-last-feedback'                    => 'ajax/lastfeedback',
    'ajax-countries'                        => 'ajax/counties',
    'ajax-states'                           => 'ajax/states',
    'ajax-country-cities'                   => 'ajax/countrycities',
    'ajax-state-cities'                     => 'ajax/statecities',
    'profiles'                              => 'ajax/profiles',
    'vips-list-ajax'                        => 'ajax/vips',
    'premiums-list-ajax'                    => 'ajax/premiums',
    'top-profiles'                          => 'ajax/topprofiles',
    'hot-messages'                          => 'ajax/hotmessages',
    'last-verified'                         => 'ajax/lastverified',
    'last-hot-message'                      => 'ajax/lasthotmessage',
    'get-message-popup-<id:\d+>'            => 'ajax/getmessagepopup',
    'get-proplan-cost-<id:\d+>'             => 'ajax/proplancost',
    'submit-proplan-<id:\d+>'               => 'ajax/proplansubmit',

            // <-- Like -->
    'like-feed-message-<id:\d+>'            => 'like/feedmessage',
    'like/escortphoto'                      => 'like/escortphoto',

            // <-- Site -->
    'page-in-work'                          => 'site/inwork',
    'error-page'                            => 'site/error',
    'disclaimer'                            => 'site/disclaimer',
    'privacy-policy'                        => 'site/privacy',
    'terms-of-use'                          => 'site/terms',
    'help'                                  => 'site/help',
    'contact'                               => 'site/contact',
    'report-abuse'                          => 'site/abuse',
    'agencies'                              => 'site/agencies',
    'appartaments'                          => 'site/appartaments',
    'vacancies'                             => 'site/vacancies',
    'escort-agencias'                       => 'site/escortagencias',
    'strip-club'                            => 'site/stripclub',
    'massage'                               => 'site/massage',
    'advertising'                           => 'site/advertising',
    'proplans'                              => 'site/proplans',
    'proplan-info/<name:\w+>'               => 'site/proplaninfo',
    'resources'                             => 'site/resources',
    'aboutgeo'                              => 'site/aboutgeo',
    'agency-<id:\d+>'                       => 'site/agency',
    'feedbacks-<id:\d+>'                    => 'site/escortfeedbacks',
    'feedbacks'                             => 'site/feedbacks',
    'sert-info'                             => 'site/sertinfo',
    'articles'                              => 'site/articles',
    'add-resource'                          => 'site/addresource',
    'ladyboy'                               => 'site/ladyboy',
    'search'                                => 'site/search',
    'sitemap'                               => 'site/sitemap',
    'announce'                              => 'site/announce',
    'about'                                 => 'site/about',

            // <-- Redirect -->
    'go-to-page-<page>'                     => 'redirect/index',
];
