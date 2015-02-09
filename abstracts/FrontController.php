<?php
/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 06.10.14
 * Time: 19:40
 */

namespace app\abstracts;

use Yii;
use app\models\Account;
use app\components\Cookie;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;

abstract class FrontController extends BaseController
{
    protected static $access = [
        Account::ROLE_GUEST => [
            'site' => '*',
            'index' => [
                'index',
                'signup',
                'login',
                'logout',
                'registration',
                'crop',
                'age',
                'setage',
                'signup',
                'transfer',
                'language',
                'resetsearch',
            ],
            'main' => '*',
            'escort' => [
                'profile',
                'list',
                'females',
                'males',
                'shemales',
                'getmore',
                'find',
                'search',
                'searchmore',
                'listincity',
                'morefeeds',
            ],
            'ajax' => '*',
            'photo' => [
                'gallery',
                'index',
                'next',
                'prev',
            ],
            'chat' => '*',
            'payment' => [
                'bitcoinconfirm',
            ],
            'redirect' => '*',
        ],
        Account::ROLE_USER => [
            //'payment' => '*',
            'index' => '*',
            'ticket' => '*',
            'ajax' => '*',
            'like' => '*',
            'user' => '*',
            'escort' => [
                'addfeedback',
                'addfeedmessage',
                'addfeedcomment',
            ],
            'account' => [
                'addprivatemessage',
                'dialogs',
                'dialog',
            ],
        ],
        Account::ROLE_ESCORT => [
            'payment' => '*',
            'escort' => '*',
            'answers' => '*',
            'account' => '*',
        ],
    ];

    protected static $noAdultPages = [
        'index/age',
        'index/setage',
        'payment/bitcoinconfirm',
        'site/error',
    ];

    public function beforeAction($action)
    {
        if(Yii::$app->request->getIsRobot())
            return parent::beforeAction($action);

        if(!Yii::$app->cookie->has(Cookie::AGE_COOKIE_NAME) && !$this->isAjax()){
            $route = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;

            if(!in_array($route, static::$noAdultPages)){
                Yii::$app->session->set('referrer', Yii::$app->request->getUrl());
                return $this->redirect(Url::toRoute('index/age'));
            }
        }

        if(!$this->checkAccess(static::$access))
            throw new ForbiddenHttpException(Yii::t('error', 'В доступе отказано'));

        if(!Yii::$app->user->isGuest)
            Notification::run();

        Yii::$app->user->setActivity();
        Yii::$app->user->checkOnlineTime();

        return parent::beforeAction($action);
    }

    public function goHome()
    {
        return $this->redirect($this->getHomeUrl());
    }

    public function goUserHome()
    {
        return $this->redirect($this->getUserHomeUrl());
    }

    public function getHomeUrl()
    {
        return Url::toRoute('index/index');
    }

    public function getUserHomeUrl()
    {
        if(Yii::$app->user->getIsEscort())
            return Url::toRoute(['escort/profile', 'id' => Yii::$app->user->id]);
        elseif(Yii::$app->user->getIsUser())
            return Url::toRoute(['user/profile', 'id' => Yii::$app->user->id]);
        elseif(Yii::$app->user->isAdmin)
            return Url::to('admin');
        else
            return $this->getHomeUrl();
    }

    public function render($view = null, $params = [])
    {
        if(!isset($params['searchForm'])){
            $form = Yii::$app->session->getSearchSettings();

            $search = [
                'searchForm' => $form,
                'isAdvancedFrom' => (int)$form->advanced,
            ];
        }else{
            $search = [];
        }

        if(is_array($view))
            $view = array_merge($search, $view);
        else
            $params = array_merge($search, $params);

        return parent::render($view, $params);
    }
}