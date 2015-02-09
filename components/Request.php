<?php
namespace app\components;

use Yii;

class Request extends \yii\web\Request
{
    private static $_robots = [
        'rambler','googlebot','aport','yahoo','msnbot','turtle','mail.ru','omsktele',
        'yetibot','picsearch','sape.bot','sape_context','gigabot','snapbot','alexa.com',
        'megadownload.net','askpeter.info','igde.ru','ask.com','qwartabot','yanga.co.uk',
        'scoutjet','similarpages','oozbot','shrinktheweb.com','aboutusbot','followsite.com',
        'dataparksearch','google-sitemaps','appEngine-google','feedfetcher-google',
        'liveinternet.ru','xml-sitemaps.com','agama','metadatalabs.com','h1.hrn.ru',
        'googlealert.com','seo-rus.com','yaDirectBot','yandeG','yandex',
        'yandexSomething','Copyscape.com','AdsBot-Google','domaintools.com',
        'Nigma.ru','bing.com','dotnetdotcom',
    ];

    public $noCsrfRoutes = [
        'ajax/profiles',
        'ajax/getmessagepopup',
        'payment/bitcoin',
        'like/escortphoto',
        'photo/next',
        'escort/getmore',
        'ticket/close',
        'ticket/usertoadminform',
        'ajax/proplancost',
        'ajax/proplansubmit',
        'account/dialog',
        'user/changeavatar',
        'ajax/clearcache',
        'escort/topmessagecost',
        'payment/closepayment',
        'escort/find',
        'account/delete',
        'payment/rejectpayment',
        'escort/search',
        'escort/searchmore',
        'escort/morefeeds',
    ];

    public function validateCsrfToken()
    {
        if($this->enableCsrfValidation){
            $route = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            if(in_array($route, $this->noCsrfRoutes))
                return true;
        }

        return parent::validateCsrfToken();
    }

    public function getIsRobot()
    {
        foreach(self::$_robots as $bot){
            if(stripos($this->getUserAgent(), $bot) !== false)
                return true;
        }

        return false;
    }
}