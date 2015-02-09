<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 19.11.14
 * Time: 18:07
 * To change this template use File | Settings | File Templates.
 */

namespace app\modules\frontend\controllers;

use app\forms\HotMessageForm;
use app\forms\SearchForm;
use app\models\Membership;
use Yii;
use app\forms\MessageForm;
use app\forms\ProplanForm;
use app\abstracts\FrontController;
use app\models\Feedback;
use yii\helpers\Url;

class AjaxController extends FrontController
{
    public function actionFeedback($id)
    {
        $this->validateAjax();

        $feedback = new Feedback();

        $feedback->setAttributes([
            'user_id' => \Yii::$app->user->id,
            'escort_id' => $id,
            'text' => \Yii::$app->request->post('FeedbackForm')['text'],
            'time' => 'now'
        ]);

        $feedback->save(true);
    }
    
    public function actionFeedbacklist()
    {
        $this->validateAjax();

        $feedbacks = $this->model('Feedback')->getFeedbackList();

        if(!$feedbacks)
            $this->ajaxError('Отзывы не найдены');

        $this->ajax('_feedbacks', [
            'feedbacks' => $this->mergeFeedbacks($feedbacks),
        ]);
    }

    public function actionLastfeedback()
    {
        $this->validateAjax();

        $feedback = $this->model('Feedback')->getLastFeedback($this->get('lastId'));

        if(!$feedback)
            $this->ajaxError('Отзыв не найден');

        $feedback = $this->mergeFeedbacks([$feedback]);
        $feedback = $feedback[0];

        $this->ajax('_feedback', [
            'feedback' => $feedback,
        ]);
    }

    public function actionCounties()
    {
        $this->validateAjax();

        $id = (int)$this->get('region');

        if($id !== 0)
            $region = $this->model('Region')->findRegion($this->get('region'));
        else
            $region = ['countries' => []];

        if(!$region)
            $this->ajaxNotFound('Не найден регион');

        $searchForm = new SearchForm();
        $searchForm->setCountries($region['countries']);

        $this->ajax('_countriesSelector', [
            'searchForm' => $searchForm,
        ]);
    }

    public function actionCountrycities()
    {
        $this->validateAjax();

        $id = (int)$this->get('country');

        if($id !== 0)
            $country = $this->model('Region')->findCountryWithStates($this->get('country'));
        else
            $country = ['states' => []];

        if(!$country)
            $this->ajaxNotFound('Страна не найдена');

        $searchForm = new SearchForm();

        if($country['states']){
            $searchForm->setStates($country['states']);
            $this->ajax('_statesSelector', ['searchForm' => $searchForm]);
        }else{
            if($id !== 0)
                $country = $this->model('Region')->findCountryWithCities($id);
            else
                $country = ['cities' => []];

            if(!$country)
                $this->ajaxNotFound('Страна не найдена');

            $searchForm->setCities($country['cities']);
            $this->ajax('_citiesSelector', ['searchForm' => $searchForm]);
        }
    }

    public function actionStatecities()
    {
        $this->validateAjax();

        $id = (int)$this->get('state');

        if($id !== 0)
            $state = $this->model('Region')->findState($id);
        else
            $state = ['cities' => []];

        if(!$state)
            $this->ajaxNotFound('Штат не найден');

        $searchForm = new SearchForm();
        $searchForm->setCities($state['cities']);

        $this->ajax('_citiesSelector', ['searchForm' => $searchForm]);
    }

    public function actionProfiles()
    {
        $this->validateAjax();

        Yii::$app->session->setSearchSettings($this->getPost());

        /*$profiles = $this->model('Escort')->getProfiles(['topLimit' => Yii::$app->params['topProfilesLimit'], 'sortByDate' => true]);

        if(!$profiles)
            $this->ajaxNotFound('Не удалось найти профили');*/

        $this->ajax([
            //'html' => $this->renderPartial('_topProfiles', ['vips' => $profiles]),
            'redirect' => Url::toRoute(['escort/list']),
        ]);
    }
    
    public function actionVips()
    {
        $this->validateAjax();

        $vips = $this->model('Escort')->getVips();

        if(!$vips)
            $this->ajaxNotFound('Не удалось найти профили');

        $this->ajax([
            'vips' => $vips,
        ]);
    }

    public function actionPremiums()
    {
        $this->validateAjax();

        $vips = $this->model('Escort')->getPremiums();

        if(!$vips)
            $this->ajaxNotFound('Не удалось найти профили');

        $this->ajax('_vipPersons', [
            'vips' => $vips,
        ]);
    }

    public function actionTopprofiles()
    {
        $this->validateAjax();
        $vips = $this->model('Escort')->getProfiles(['topLimit' => Yii::$app->params['topProfilesLimit'], 'sortByDate' => true]);

        if(!$vips)
            $this->ajaxNotFound('Не удалось найти профили');

        $this->ajax('_topProfiles', [
            'vips' => $vips,
        ]);
    }

    public function actionHotmessages()
    {
        $this->validateAjax();

        $messages = $this->model('Message')->getHotMessages();

        if(!$messages)
            $this->ajaxNotFound('Не удалось найти сообщения');

        $this->ajax('_hotMessages', [
            'messages' => $messages,
        ]);
    }

    public function actionLasthotmessage()
    {
        $this->validateAjax();

        $message = $this->model('Message')->getLastHotMessage($this->get('lastId'));

        if(!$message)
            $this->ajaxError('Не удалось найти сообщение');

        $this->ajax('_hotMessage', [
            'message' => $message,
        ]);
    }

    public function actionLastverified()
    {
        $this->validateAjax();

        $escorts = $this->model('Escort')->getLastVerified();

        if(!$escorts)
            $this->ajaxNotFound('Не удалось найти профили');

        $this->ajax('_lastVerified', [
            'vips' => $this->mergeProfiles($escorts),
        ]);
    }

    public function actionGetmessagepopup($id)
    {
        $this->validateAjax();

        $account = $this->model('Account')->findEntityById($id);

        $messageForm = new MessageForm();

        if(!$account)
            $this->ajaxNotFound('Не удалось найти профиль');

        $this->ajax('_sendMessagePopup',[
            'account' => $account,
            'messageForm' => $messageForm
        ]);
    }

    public function actionProplancost($id)
    {
        $this->validateAjax();

        $proplan = $this->model('Membership')->findById($id);
        if(!$proplan)
            $this->ajaxNotFound('Не удалось найти account ID {id}', ['id' => $id]);

        $proplanForm = new ProplanForm();
        $proplanForm->setProplan($proplan);

        $this->ajax('partials/_proplanCost', [
            'proplan' => $proplan,
            'proplanForm' => $proplanForm,
        ]);
    }

    public function actionProplansubmit($id)
    {
        $this->validateAjax();

        $proplan = $this->model('Membership')->findById($id);
        if(!$proplan)
            $this->ajaxNotFound('Не удалось найти account ID {id}', ['id' => $id]);

        $proplanForm = new ProplanForm();

        $proplanForm->id = $id;
        $proplanForm->userId = Yii::$app->user->id;
        $proplanForm->setProplan($proplan);

        if(!$proplanForm->load($this->getPost()))
            $this->ajaxError('Неверные данные');

        $price = $proplanForm->price*($proplanForm->duration/$proplan->duration);

        $money = Yii::$app->user->getBalance() - $price;

        if($money < 0){
            $this->ajax([
                'money' => $this->renderPartial('partials/_proplanFailed', [
                    'amount' => abs($money),
                ]),
                'url' => Url::toRoute(['account/index', 'page' => 'credits']),
            ]);
        }

        if($endDate = $proplanForm->save()){
            $this->ajax('partials/_proplanExtended', [
                'date' => Yii::$app->local->formatDate($endDate),
            ]);
        }else
            $this->ajaxError('Не удалось подключить аккаунт');
    }


    /**
     * @param \app\models\Escort[] $escorts
     * @return \stdClass[]
     */
    private function mergeProfiles(array $escorts)
    {
        foreach($escorts as $i => $escort){
            $escortData = $escort->getAttributes();

            $escortData['fullName'] = $escort->getFullName();
            $escortData['ava'] = $escort->getRandomImg();
            $escortData['city'] = $escort->getCityName();
            $escortData['isOnline'] = $escort->getIsOnline();

            $escorts[$i] = (object)$escortData;
        }

        return (array)$escorts;
    }

    /**
     * @param \app\models\Feedback[] $feedbacks
     * @return array
     */
    private function mergeFeedbacks(array $feedbacks)
    {
        foreach($feedbacks as $i => $feedback){
            $feedbackInfo = $feedback->getAttributes();
            $escortInfo = $feedback->escort->getAttributes();

            unset($escortInfo['id']);
            $escortInfo['ava'] = $feedback->escort->getAva();
            $escortInfo['userId'] = $feedbackInfo['user_id'];
            $escortInfo['userName'] = $feedback->escort->getFullName();
            $escortInfo['escortId'] = $feedbackInfo['escort_id'];

            $feedbacks[$i] = (object)array_merge($feedbackInfo, $escortInfo);
        }

        return (array)$feedbacks;
    }
}