<?php

return [
            // <-- Ajax -->
    'ajax-verification-<id:\d+>'                => 'ajax/verification',
    'clear-cache'                               => 'ajax/clearcache',
    'get-region-countries'                      => 'ajax/counties',
    'get-country-cities'                        => 'ajax/countrycities',
    'get-state-cities'                          => 'ajax/statecities',

            // <-- Index -->

            // <-- Acccount -->
    'accounts-list'                             => 'account/all',
    'escorts-list'                              => 'account/escorts',
    'users-list'                                => 'account/users',
    'online-list'                               => 'account/online',
    'accounts-ajax'                             => 'account/accountsajax',
    'escorts-ajax'                              => 'account/escortsajax',
    'users-ajax'                                => 'account/usersajax',
    'online-ajax'                               => 'account/onlineajax',
    'login-by-user-<id:\d+>'                    => 'account/loginbyuserid',
    'delete-user-<id:\d+>'                      => 'account/delete',
    'last-registered/<role:\w+>-<date:\w+>'     => 'account/lastregistered',
    'last-registered-<role:\w+>'                => 'account/lastregistered',
    'last-registered/<date:\w+>'                => 'account/lastregistered',
    'last-registered'                           => 'account/lastregistered',
    'ajax-last-registered/<role:\w+>-<date:\w+>'=> 'account/lastregisteredajax',
    'ajax-last-registered-<role:\w+>'           => 'account/lastregisteredajax',
    'ajax-last-registered/<date:\w+>'           => 'account/lastregisteredajax',
    'ajax-last-registered'                      => 'account/lastregisteredajax',
    'last-active/<role:\w+>-<date:\w+>'         => 'account/lastactive',
    'last-active-<role:\w+>'                    => 'account/lastactive',
    'last-active/<date:\w+>'                    => 'account/lastactive',
    'last-active'                               => 'account/lastactive',
    'ajax-last-active/<role:\w+>-<date:\w+>'    => 'account/lastactiveajax',
    'ajax-last-active-<role:\w+>'               => 'account/lastactiveajax',
    'ajax-last-active/<date:\w+>'               => 'account/lastactiveajax',
    'ajax-last-active'                          => 'account/lastactiveajax',

            // <-- Transaction -->
    'all-transactions'                          => 'payment/alltransactions',
    'all-tranactions-ajax'                      => 'payment/alltranactionsajax',
    'all-payments-<status:\d+>'                 => 'payment/allpayments',
    'all-payments'                              => 'payment/allpayments',
    'all-payments-ajax-<status:\d+>'            => 'payment/allpaymentsajax',
    'all-payments-ajax'                         => 'payment/allpaymentsajax',
    'close-payment-<id:\d+>'                    => 'payment/closepayment',
    'reject-payment-<id:\d+>'                   => 'payment/rejectpayment',

            // <-- Proplan -->
    'proplans-list'                             => 'proplan/list',
    'proplans-users-list-<id:\d+>'              => 'proplan/userslist',
    'proplans-users-list'                       => 'proplan/userslist',
    'proplans-users-list-ajax-<id:\d+>'         => 'proplan/userslistajax',
    'proplans-users-list-ajax'                  => 'proplan/userslistajax',
    'edit-proplan-<id:\d+>'                     => 'proplan/edit',

            // <-- Ticket -->
    'tickets-all'                               => 'ticket/all',
    'tickets-opened'                            => 'ticket/opened',
    'tickets-closed'                            => 'ticket/closed',
    'tickets-all-jax'                           => 'ticket/allajax',
    'tickets-opened-ajax'                       => 'ticket/openedajax',
    'tickets-closed-ajax'                       => 'ticket/closedajax',
    'close-ticket-<id:\d+>'                     => 'ticket/close',
];