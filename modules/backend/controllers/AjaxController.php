<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 19.11.14
 * Time: 18:07
 * To change this template use File | Settings | File Templates.
 */

namespace app\modules\backend\controllers;

use app\forms\SearchForm;
use Yii;
use app\abstracts\BackController;
use app\models\Account;

class AjaxController extends BackController
{
    public function actionVerification($id)
    {
        $this->validateAjax();

        $account = Account::findOne($id);
        if(!$account)
            $this->ajaxError('Не удалось найти аккаунт');

        $account->setAttribute('role', Account::ROLE_VERIFIED_ESCORT);
        $account->update(false, ['role']);
        $account->getEntity()->setAttribute('verification_date', 'now');
        $account->getEntity()->update(false,['verification_date']);
    }

    public function actionClearcache()
    {
        $this->validateAjax();

        Yii::$app->cache->clear();

        $this->ajax(Yii::t('back', 'Кэш очищен'));
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

        $regionsForm = new SearchForm();
        $regionsForm->setCountries($region['countries']);

        $this->ajax('_countriesSelector', [
            'regionsForm' => $regionsForm,
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

        $regionsForm = new SearchForm();

        if($country['states']){
            $regionsForm->setStates($country['states']);
            $this->ajax('_statesSelector', ['regionsForm' => $regionsForm]);
        }else{
            if($id !== 0)
                $country = $this->model('Region')->findCountryWithCities($id);
            else
                $country = ['cities' => []];

            if(!$country)
                $this->ajaxNotFound('Страна не найдена');

            $regionsForm->setCities($country['cities']);
            $this->ajax('_citiesSelector', ['regionsForm' => $regionsForm]);
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

        $regionsForm = new SearchForm();
        $regionsForm->setCities($state['cities']);

        $this->ajax('_citiesSelector', ['regionsForm' => $regionsForm]);
    }
}