<?php

namespace app\modules\frontend\controllers;

use app\forms\SearchForm;
use app\models\Escort;
use app\models\FeedMessage;
use Yii;
use app\models\Membership;
use app\models\Account;
use app\forms\FeedMessageCommentForm;
use app\forms\FeedMessageForm;
use app\forms\HotMessageForm;
use app\forms\FeedbackForm;
use app\abstracts\FrontController;
use yii\debug\models\search\Debug;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use app\forms\MessageForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 19.11.2014
 * Time: 12:02
 */
class EscortController extends FrontController
{
    public function actionProfile($id, $page = null)
    {
        if(Yii::$app->user->id != $id)
            Yii::$app->stats->escortViews($id)->add();

        $profile = $this->model('Escort')->getProfileFoolInfo($id);

        if(!$profile)
            throw new NotFoundHttpException(Yii::t('error', 'Анкета не найдена'));

        $messageFrom = new FeedMessageForm();
        $commentForm = new FeedMessageCommentForm();
        $feedbackForm = new FeedbackForm();
        $privateMessageForm = new MessageForm();

        $messageFrom->escortId = $id;
        $commentForm->escortId = $id;
        $feedbackForm->escortId = $id;
        $privateMessageForm->to = $id;

        $feeds = $this->model('FeedMessage')->findByEscortId($id);
        $showShowMoreFeedsButton = count($feeds) >= Yii::$app->params['escortFeedsLimit'];

        $topPhotos = $this->model('EscortPhoto')->getTopPhotos($id);
        $feedbacks = $this->model('Feedback')->findByEscortId($id);

        if($page === null){
            $page = 'index';
            $canonical = false;
        }else{
            $canonical = true;
        }

        switch($page){
            case 'index':
                $show = '.about-area';
                break;
            case 'feed':
                $show = '.feedmessages-area';
                break;
            case 'feedbacks':
                $show = '.feedbacks-area';
                break;
            case 'gallery':
                $show = '.gallery-area';
                break;
            default:
                $show = '.about-area';
                break;

        }

        $params = [
            'profile' => $profile,
            'topPhotos' => $topPhotos,
            'feeds' => $feeds,
            'feedbacks' => $feedbacks,
            'messageForm' => $messageFrom,
            'commentForm' => $commentForm,
            'feedbackForm' => $feedbackForm,
            'privateMessageForm' => $privateMessageForm,
            'hasMineFeedback' => false,
            'show' => $show,
            'title' => Yii::t('front', 'Эскорт {gender}, {name}, {region}', ['gender' => $profile->getGender(), 'name' => $profile->getFullName(), 'region' => $profile->getRegionName()]),
            'isMe' => Yii::$app->user->id == $id,
            'showMoreUrl' => Url::toRoute(['escort/morefeeds', 'id' => $id]),
            'showShowMoreFeedsButton' => $showShowMoreFeedsButton,
        ];

        if($canonical === true)
            $params['canonical'] = Url::toRoute(['escort/profile', 'id' => $id], true);

        return $this->render($params);
    }

    public function actionList()
    {
        $vips = $this->model('Escort')->getProfiles(['sex' => 'escorts', 'topLimit' => Yii::$app->params['topProfilesListLimit']]);
        $hotMessageForm = new HotMessageForm();

        return $this->render([
            'vips' => $vips,
            'showMoreUrl' => Url::toRoute(['escort/getmore', 'sex' => 'escorts']),
            'hotMessageForm' => $hotMessageForm,
            'hotMessages' => $this->model('Message')->getHotMessages(),
        ]);
    }

    public function actionListincity($id)
    {
        $params = [
            'SearchForm' => [
                'city' => $id,
            ],
        ];

        Yii::$app->session->setSearchSettings($params);
        $escorts = $this->model('Escort')->getProfiles(['sex' => 'escorts', 'topLimit' => Yii::$app->params['topProfilesListLimit']]);

        $hotMessageForm = new HotMessageForm();

        return $this->render('list', [
            'vips' => $escorts,
            'showMoreUrl' => Url::toRoute(['escort/getmore', 'sex' => 'escorts']),
            'findProfilesUrl' => Url::toRoute(['escort/find', 'sex' => 'escorts']),
            'hotMessageForm' => $hotMessageForm,
            'hotMessages' => $this->model('Message')->getHotMessages(),
        ]);
    }

    public function actionFemales()
    {
        $escorts = $this->model('Escort')->getProfiles(['sex' => Account::SEX_FEMALE, 'topLimit' => Yii::$app->params['topProfilesListLimit']]);

        $hotMessageForm = new HotMessageForm();

        return $this->render('list', [
            'vips' => $escorts,
            'showMoreUrl' => Url::toRoute(['escort/getmore', 'sex' => Account::SEX_FEMALE]),
            'findProfilesUrl' => Url::toRoute(['escort/find', 'sex' => Account::SEX_FEMALE]),
            'hotMessageForm' => $hotMessageForm,
            'hotMessages' => $this->model('Message')->getHotMessages(),
        ]);
    }

    public function actionMales()
    {
        $escorts = $this->model('Escort')->getProfiles(['sex' => Account::SEX_MALE, 'topLimit' => Yii::$app->params['topProfilesListLimit']]);
        $hotMessageForm = new HotMessageForm();

        return $this->render('list', [
            'vips' => $escorts,
            'showMoreUrl' => Url::toRoute(['escort/getmore', 'sex' => Account::SEX_MALE]),
            'findProfilesUrl' => Url::toRoute(['escort/find', 'sex' => Account::SEX_MALE]),
            'hotMessageForm' => $hotMessageForm,
            'hotMessages' => $this->model('Message')->getHotMessages(),
        ]);
    }

    public function actionShemales()
    {
        $escorts = $this->model('Escort')->getProfiles(['sex' => Account::SEX_SHEMALE, 'topLimit' => Yii::$app->params['topProfilesListLimit']]);
        $hotMessageForm = new HotMessageForm();

        return $this->render('list', [
            'vips' => $escorts,
            'showMoreUrl' => Url::toRoute(['escort/getmore', 'sex' => Account::SEX_SHEMALE]),
            'findProfilesUrl' => Url::toRoute(['escort/find', 'sex' => Account::SEX_SHEMALE]),
            'hotMessageForm' => $hotMessageForm,
            'hotMessages' => $this->model('Message')->getHotMessages(),
        ]);
    }

    public function actionUploadphoto()
    {
        $this->validateAjax();
        $data = $this->getPost('image');
        if(!$data)
            $this->ajaxError('Неверные данные');

        if(!Yii::$app->user->canUploadPhotos()){
            $this->ajaxError('Лимит фотографий исчерпан');
        }

        if(!$this->model('EscortPhoto')->addNewPhoto($data))
            $this->ajaxError('Не удалось загрузить изображение');

        $this->ajax('Фотография успешно загружена');
    }

    /**
     * @todo Вывести список випов, а не просто список эскортов
     * @return string
     */
    public function actionVips()
    {
        $vips = $this->model('Escort')->getProfiles(['sex' => 'escorts', 'topLimit' => Yii::$app->params['topProfilesListLimit']]);
        $hotMessageForm = new HotMessageForm();

        return $this->render('list', array_merge($this->model('Region')->getRegionsArray(), [
            'vips' => $vips,
            'showMoreUrl' => Url::toRoute(['escort/getmore', 'sex' => 'escorts']),
            'hotMessageForm' => $hotMessageForm,
        ]));
    }

    public function actionSearch()
    {
        $searchForm = Yii::$app->session->getSearchSettings();

        if($searchForm->load($this->getPost())){
            //\Debug::show($this->getPost());
            Yii::$app->session->setSearchSettings($this->getPost());
            $escorts = $this->model('Escort')->findProfiles($searchForm);
            $showMoreUrl = Url::toRoute(['escort/searchmore']);
        }else{
            $escorts = $this->model('Escort')->getProfiles(['sex' => 'escorts', 'topLimit' => Yii::$app->params['topProfilesListLimit']]);
            $showMoreUrl = Url::toRoute(['escort/getmore', 'sex' => $searchForm->sex === null ? 'escorts' : $searchForm->sex]);
        }

        return $this->render('list', [
            'vips' => $escorts,
            'showMoreUrl' => $showMoreUrl,
            'hotMessageForm' => new HotMessageForm(),
            'hotMessages' => $this->model('Message')->getHotMessages(),
            'findProfilesUrl' => Url::to(['escort/search']),
        ]);
    }

    public function actionGetmore($sex)
    {
        $this->validateAjax();

        $escorts = $this->model('Escort')->getProfiles(['sex' => $sex, 'offset' => $this->getPost('offset'), 'topLimit' => Yii::$app->params['topProfilesListLimit']]);

        if(!$escorts)
            $this->ajaxError('Эскорты не найдены');

        $this->ajax('ajax/_topProfiles', [
            'vips' => $escorts,
        ]);
    }

    public function actionSearchmore()
    {
        $this->validateAjax();

        $searchForm = Yii::$app->session->getSearchSettings();
        $escorts = $this->model('Escort')->findProfiles($searchForm, $this->getPost('offset'));
        if(!$escorts)
            $this->ajaxError('Эскорты не найдены');

        $this->ajax('ajax/_topProfiles', [
            'vips' => $escorts,
        ]);
    }

    // Ajax messages

    public function actionMorefeeds($id)
    {
        $this->validateAjax();

        $count = (int)$this->getPost('feedsCont');
        if(!$count)
            $this->ajaxError(Yii::t('ajax', 'Неверные данные'));

        $feeds = $this->model('FeedMessage')->findByEscortId($id, $count);
        $commentForm = new FeedMessageCommentForm();

        $this->ajax('_feedMessageList', [
            'feeds' => $feeds,
            'commentForm' => $commentForm,
        ]);
    }

    public function actionTopmessagecost()
    {
        $this->validateAjax();
        $membership = $this->model('Membership')->findById(Membership::HOT_MESSAGE);

        $this->ajax('_hotMessageCost', [
            'price' => $membership->getDiscountPrice(),
        ]);
    }

    public function actionAddhotmessage()
    {
        $this->validateAjax();

        $form = new HotMessageForm();
        $membership =$this->model('Membership')->findById(Membership::HOT_MESSAGE);
        $price = $membership->getDiscountPrice();

        if($price > Yii::$app->user->getBalance()){
            $this->ajax([
                'redirect' => Url::toRoute(['account/index', 'page' => 'credits']),
            ]);
        }

        if($form->load($this->getPost()) && $form->addHotMessage()){
            $this->ajax([
                'count' => $price,
                'balance' => Yii::$app->user->getBalance(),
            ]);
        }

        $this->ajaxError($this->parseError($form->getErrors()));
    }

    public function actionAddfeedmessage()
    {
        $this->validateAjax();

        $form = new FeedMessageForm();

        if(!$form->load($this->getPost()))
            $this->ajaxError($form->getErrors());

        $message = $form->save();
        if(!$message)
            $this->ajaxError($form->getErrors());

        $commentForm = new FeedMessageCommentForm();

        $this->ajax('_feedMessage', [
            'message' => $message,
            'commentForm' => $commentForm
        ]);
    }

    public function actionAddfeedcomment($id)
    {
        $this->validateAjax();

        $form = new FeedMessageCommentForm();

        if(!$form->load($this->getPost()))
            $this->ajaxError($form->getErrors());

        $form->ownerId = Yii::$app->user->id;
        $form->feedMessageId = $id;

        $comment = $form->save();

        if(!$comment)
            $this->ajaxError('Не удалось добавить комментарий');

        $this->ajax('_feedComment', [
            'comment' => $comment,
        ]);
    }

    public function actionAddfeedback()
    {
        $this->validateAjax();

        $form = new FeedbackForm();

        if(!$form->load($this->getPost()))
            $this->ajaxError($form->getErrors());

        $message = $form->save();

        if(!$message)
            $this->ajaxError($form->getErrors());

        $this->ajax('_feedbackMessage', [
            'message' => $message,
        ]);
    }

    public function actionRemovephoto($id)
    {
        $this->validateAjax();

        if(!$this->model('EscortPhoto')->remove($id))
            $this->ajaxError(Yii::t('base','Не удалось удалить фото'));

        $this->ajax([]);
    }

    // END Ajax messages
}