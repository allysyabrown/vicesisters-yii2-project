<?php

namespace app\modules\frontend\controllers;

use app\models\Escort;
use Yii;
use app\abstracts\FrontController;
use app\forms\RegistrationForm;
use app\forms\LoginForm;
use app\forms\ChatForm;
use app\components\Cookie;
use app\forms\HotMessageForm;
use yii\helpers\Url;

class IndexController extends FrontController
{
    public function actionIndex()
    {
        $hotMessageForm = new HotMessageForm();
        $chatForm = new ChatForm();
        $hotMessages = $this->model('Message')->getHotMessages();

        return $this->render([
            'hotMessages' => $hotMessages,
            'hotMessageForm' => $hotMessageForm,
            'chatIframeUrl' => Yii::$app->chat->getIframeUrl(),
            'chatForm' => $chatForm,
            'iframeUrl' => Yii::$app->chat->getIframeUrl(),
        ]);
    }

    public function actionAge()
    {
        return $this->render();
    }

    public function actionLanguage($lang)
    {
        Yii::$app->local->setLanguage($lang);
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionSetage()
    {
        $referrer = Yii::$app->session->get('referrer');
        if(!$referrer)
            $referrer = Yii::$app->request->getReferrer();
        if(!$referrer || $referrer == Yii::$app->request->getUrl())
            $referrer = $this->getHomeUrl();

        Yii::$app->cookie->set(Cookie::AGE_COOKIE_NAME, 18);
        Yii::$app->session->remove('referrer');

        if(strpos($referrer, 'age') !== false)
            $referrer = $this->getHomeUrl();

        return $this->redirect($referrer);
    }

    public function actionSignup()
    {
        $regForm = new RegistrationForm();
        $loginForm = new LoginForm();

        return $this->render('signup', [
            'loginForm' => $loginForm,
            'regForm' => $regForm,
            'openLoginForm' => false,
            'openRegForm' => false,
        ]);
    }

    public function actionRegistration()
    {
        $regForm = new RegistrationForm();
        $loginForm = new LoginForm();

        if($regForm->load($this->getPost()) && $regForm->save()){
            $loginForm->login($this->getPost('RegistrationForm'));
            return $this->goUserHome();
        }

        return $this->render('signup', [
            'loginForm' => $loginForm,
            'regForm' => $regForm,
            'openLoginForm' => false,
            'openRegForm' => true,
        ]);
    }

    public function actionLogin()
    {
        $loginForm = new LoginForm();

        if($loginForm->load($this->getPost()) && $loginForm->login())
            return $this->goUserHome();

        $regForm = new RegistrationForm();

        return $this->render('signup', [
            'loginForm' => $loginForm,
            'regForm' => $regForm,
            'openLoginForm' => true,
            'openRegForm' => false,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionResetsearch()
    {
        $referrer = Yii::$app->request->getReferrer();
        if(strpos($referrer, $this->id) !== false)
            $referrer = Url::toRoute('index/index');

        Yii::$app->session->resetSearchSettings();

        return $this->redirect($referrer);
    }

   /* public function actionUpload()
    {
        $model = new UploadForm();

        if($image = Yii::$app->image->upload($model)){
            Yii::$app->data->getRepository('EscortPhoto')->add($image, Yii::$app->user->getEntity()->id,1);
        }

        return $this->render('upload', ['uploadForm' => $model]);
    }

    public function actionCrop()
    {
        return $this->render([
            'url' => 'http://static.devpride.com.ua/img/mail_header.jpg',
            'onRelease' => new JsExpression("function() {ejcrop_cancelCrop(this);}")
        ]);
    }

    public function actionCropping()
    {
        if(Yii::$app->request->isAjax){
            $image = Yii::$app->image->load(Yii::getAlias('@temp-images').'/3x8mm1gsb8hg5j5qyh98fefujthj3k.jpg');
            header("Content-Type: image/png");
            echo $image->resize(120,210)->rotate(30)->render();
        }
    }
   */
}
