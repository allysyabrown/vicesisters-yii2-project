<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jangolle
 * Date: 05.12.14
 * Time: 9:29
 * To change this template use File | Settings | File Templates.
 */

namespace app\modules\frontend\controllers;

use app\models\Membership;
use Yii;
use app\abstracts\Notification;
use app\forms\ChangePasswordForm;
use app\forms\FeedMessageForm;
use app\abstracts\FrontController;
use app\forms\EscortAccountForm;
use app\forms\MessageForm;
use app\forms\HotMessageForm;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use app\forms\EscortTravelsForm;

class AccountController extends FrontController
{
    public function actionIndex($page = null)
    {
        $escort = Yii::$app->user->getEntity();
        if(!$escort)
            throw new NotFoundHttpException(Yii::t('error', 'Анкета не найдена'));

        $hotMessageForm = new HotMessageForm();
        $accountForm = new EscortAccountForm();
        $passForm = new ChangePasswordForm();
        $travelsForm = new EscortTravelsForm();

        $accountForm->setAccount($escort);
        $travelsForm->setAccount($escort);

        if($accountForm->load($this->getPost())){
            $accountForm->save();
        }

        if($page === null){
            $page = 'index';
            $canonical = false;
        }else{
            $canonical = true;
        }

        switch($page){
            case 'index':
                $show = '.escort-account';
                break;
            case 'preference':
                $show = '.escort-preference';
                break;
            case 'credits':
                $show = '.escort-credits';
                break;
            case 'password':
                $show = '.escort-password';
                break;
            case 'travels':
                $show = '.escort-travels';
                break;
            default:
                $show = '.escort-account';
                break;
        }

        $params = [
            'escort' => $escort,
            'hotMessageForm' => $hotMessageForm,
            'accountForm' => $accountForm,
            'changePasswordForm' => $passForm,
            'travelsForm' => $travelsForm,
            'services' => Yii::$app->payment->getServices(),
            'show' => $show,
            'title' => Yii::t('front', 'Мой профиль'),
        ];

        $params = array_merge($params, Yii::$app->payment->getForms());

        if($canonical === true)
            $params['canonical'] = Url::toRoute(['account/index'], true);

        return $this->render($params);
    }

    public function actionDialogs()
    {
        $dialogs = $this->model('Message')->getDialogs();

        $code = $dialogs ? $dialogs[0]->dialogCode : 0;

        $messages = $this->model('Message')->getPrivateMessagesByCode($code);
        $to = $this->model('Message')->getOpponentByCode($code);

        $messageForm = new MessageForm();

        Notification::readAll(Notification::FIELD_MESSAGES, Yii::$app->user->id);

        return $this->render([
            'dialogs' => $dialogs,
            'messages' => $messages,
            'messageForm' => $messageForm,
            'to' => $to,
        ]);
    }

    public function actionFeed()
    {
        $id = Yii::$app->user->id;
        $escort = $this->model('Escort')->getProfile($id);

        if(!$escort)
            throw new NotFoundHttpException(Yii::t('error', 'Анкета не найдена'));

        $feeds = $this->model('FeedMessage')->findByEscortId($id);

        $feedForm = new FeedMessageForm();
        $feedForm->escortId = $id;

        return $this->render([
            'feeds' => $feeds,
            'messageForm' => $feedForm,
        ]);
    }

    public function actionDialog($code)
    {
        $this->validateAjax();

        $messages = $this->model('Message')->getPrivateMessagesByCode($code);
        $this->model('Message')->readNewMessages($messages);

        $to = $this->model('Message')->getOpponentByCode($code);
        $messageForm = new MessageForm();

        $this->ajax('_messagesArea',[
            'messages' => $messages,
            'messageForm' => $messageForm,
            'to' => $to,
        ]);
    }

    public function actionFavorites()
    {
        $myFavorites = $this->model('Account')->getFavorites();
        $otherLikesMe = $this->model('Account')->getOtherLikesMe();

        return $this->render([
            'myFavorites' => $myFavorites,
            'otherLikesMe' => $otherLikesMe
        ]);
    }

    public function actionAddfavorite($id)
    {
        $this->validateAjax();

        if(!$this->model('Account')->addFavorite($id))
            $this->ajaxError(Yii::t('base', 'Не удалось добавить пользователя в избранное'));
    }

    public function actionRemovefavorite($id)
    {
        $this->validateAjax();

        if(!$this->model('Account')->removeFavorite($id))
            $this->ajaxError(Yii::t('base', 'Не удалось убрать пользователя из списка избранных'));
    }

    public function actionChangepassword()
    {
        $this->validateAjax();
        $passForm = new ChangePasswordForm();

        if(!$passForm->load($this->getPost()))
            $this->ajaxError($passForm->getErrors());

        if(!$passForm->save())
            $this->ajaxError($passForm->getErrors());

        $this->ajax(Yii::t('account', 'Пароль успешно изменён'));
    }

    public function actionUploadavatar()
    {
        $this->validateAjax();
        $data = $this->getPost('image');
        if(!$data)
            $this->ajaxError('Неверные данные');

        if(!$this->model('Account')->setAvatar($data))
            $this->ajaxError('Не удалось загрузить изображение');

        $this->ajax('Аватар успешно изменён');
    }

    public function actionSettravelslist()
    {
        $this->validateAjax();

        $escort = Yii::$app->user->getEntity();
        if(!$escort)
            $this->ajaxError('Анкета не найдена');

        $travelsForm = new EscortTravelsForm();
        $travelsForm->setAccount($escort);

        if($travelsForm->load($this->getPost()) && $travelsForm->save())
            $this->ajax('Список путешествий обновлён');
        else
            $this->ajaxError($travelsForm->getErrors());
    }

    public function actionAddprivatemessage($id)
    {
        $this->validateAjax();

        $form = new MessageForm();
        $form->to = $id;

        if(!$form->load($this->getPost()))
            $this->ajaxError($form->getErrors());

        $message = $form->savePrivateMessage();
        if(!$message)
            $this->ajaxError($form->getErrors());

        $this->ajax('_myMessage', [
            'message' => $message,
        ]);
    }

    public function actionCitiesbycountry()
    {
        $this->validateAjax();

        $countryId = $this->get('country');
        if(!$countryId)
            $this->ajaxError('Неверные параметры запроса');

        $form = new EscortAccountForm();
        $form->setCountry_id($countryId);

        $view = $form->getStateItems() ? '_statesSelector' : '_citiesSelector';

        $this->ajax($view, [
            'accountForm' => $form,
            'citiesContainer' => true,
        ]);
    }

    public function actionCitiesbystate()
    {
        $this->validateAjax();

        $stateId = $this->get('state');
        if(!$stateId)
            $this->ajaxError('Неверные параметры запроса');

        $form = new EscortAccountForm();
        $form->setState_id($stateId);

        $this->ajax('_citiesSelector', [
            'accountForm' => $form,
        ]);
    }
}