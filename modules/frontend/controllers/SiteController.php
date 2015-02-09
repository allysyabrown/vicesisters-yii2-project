<?php

namespace app\modules\frontend\controllers;

use app\models\Membership;
use Yii;
use app\abstracts\FrontController;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 21.12.2014
 * Time: 18:05
 */
class SiteController extends FrontController
{
    private $returnUrl;

    public function beforeAction($action)
    {
        $this->returnUrl = Yii::$app->request->getReferrer();
        return parent::beforeAction($action);
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        $code = $exception ? $exception->getCode() : 0;
        if($code == 0){
            $code = $exception ? $exception->statusCode : 'unknown code';
        }

        if($code === 403)
            return $this->redirect(Url::toRoute(['index/signup']));

        if(YII_DEBUG === true){
            $message = $exception ? $exception->getMessage() : 'unknown error message';
        }else{
            $message = Yii::t('front', 'Мы сожалеем, но страница, которую вы ищете, не найдена. Мы можем предложить вам воспользоваться поиском в нашем каталоге');
        }

        return $this->render([
            'code' => $code,
            'message' => $message,
            'notShowShit' => true,
            'returnUrl' => $this->returnUrl,
        ]);
    }

    public function actionInwork()
    {
        return $this->render('pageInWork', [
            'returnUrl' => $this->returnUrl,
            'notShowShit' => true,
        ]);
    }

    public function actionDisclaimer()
    {
        return $this->renderInWork();
    }

    public function actionPrivacy()
    {
        return $this->renderInWork();
        /*return $this->render([
            'notShowShit' => true,
        ]);*/
    }

    public function actionTerms()
    {
        return $this->renderInWork();
    }

    public function actionHelp()
    {
        return $this->renderInWork();
        /*return $this->render([
            'notShowShit' => true,
        ]);*/
    }

    public function actionContact()
    {
        return $this->render([
            'notShowShit' => true,
        ]);
    }

    public function actionAbuse()
    {
        return $this->renderInWork();
    }

    public function actionAgencies()
    {
        return $this->renderInWork();
    }

    public function actionAppartaments()
    {
        return $this->renderInWork();
    }

    public function actionVacancies()
    {
        return $this->renderInWork();
    }

    public function actionEscortagencias()
    {
        return $this->renderInWork();
    }

    public function actionStripclub()
    {
        return $this->renderInWork();
    }

    public function actionMassage()
    {
        return $this->renderInWork();
    }

    public function actionAdvertising()
    {
        return $this->renderInWork();
    }

    public function actionProplaninfo($name)
    {
        return $this->renderInWork();
    }

    public function actionResources()
    {
        return $this->renderInWork();
    }

    public function actionAbout()
    {
        //return $this->renderInWork();
        return $this->render([
            'notShowShit' => true,
        ]);
    }

    public function actionAboutgeo()
    {
        return $this->renderInWork();
    }

    public function actionAgency($id)
    {
        return $this->renderInWork();
    }

    public function actionFeedbacks()
    {
        return $this->renderInWork();
    }

    public function actionEscortfeedbacks($id)
    {
        return $this->renderInWork();
    }

    public function actionSertinfo()
    {
        return $this->renderInWork();
    }

    public function actionArticles()
    {
        return $this->renderInWork();
    }

    public function actionAddresource()
    {
        return $this->renderInWork();
    }

    public function actionLadyboy()
    {
        return $this->renderInWork();
    }

    public function actionSearch()
    {
        return $this->renderInWork();
    }

    public function actionAnnounce()
    {
        return $this->renderInWork();
    }

    public function actionProplans()
    {
        $memberships = $this->model('Membership')->getProplans();
        $vipPrice = 400;
        $premiumPrice = 150;
        $purplePrice = 5;
        $hotMessagePrice = 3;

        $vipPurplePlace = 0;
        $premiumPurplePrice = 0;
        $vipHotMessagePrice = 0;
        $premiumHotMessagePrice = 0;


        foreach($memberships as $membership){
            switch($membership->id){
                case Membership::VIP_ACCOUNT:
                    $vipPrice = $membership->price;
                    break;
                case Membership::PREMIUM_ACCOUNT;
                    $premiumPrice = $membership->price;
                    break;
                case Membership::PURPLE_PLACE:
                    $purplePrice = $membership->price;
                    $vipPurplePlace = $membership->getVipPrice();
                    $premiumPurplePrice = $membership->getPremiumPrice();
                    break;
                case Membership::HOT_MESSAGE:
                    $hotMessagePrice = $membership->price;
                    $vipHotMessagePrice = $membership->getVipPrice();
                    $premiumHotMessagePrice = $membership->getPremiumPrice();
                    break;
            }
        }

        return $this->render([
            'proplans' => $memberships,
            'vipPrice' => $vipPrice,
            'premiumPrice' => $premiumPrice,
            'purplePrice' => $purplePrice,
            'hotMessagePrice' => $hotMessagePrice,
            'vipPurplePlace' => $vipPurplePlace,
            'premiumPurplePrice' => $premiumPurplePrice,
            'vipHotMessagePrice' => $vipHotMessagePrice,
            'premiumHotMessagePrice' => $premiumHotMessagePrice,
        ]);
    }

    public function actionSitemap()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml; charset=utf-8');

       return $this->renderFile('@www/sitemap.xml');
    }

    private function renderInWork()
    {
        return $this->render('pageInWork', [
            'returnUrl' => $this->returnUrl,
            'notShowShit' => true,
            'canonical' => Url::toRoute(['site/inwork'], true),
        ]);
    }
}