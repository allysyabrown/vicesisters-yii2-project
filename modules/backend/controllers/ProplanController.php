<?php

namespace app\modules\backend\controllers;

use Yii;
use app\forms\ProplanDurationOptionsForm;
use app\forms\SearchForm;
use app\forms\EditProlanForm;
use app\models\Membership;
use app\abstracts\BackController;
use app\models\MembershipDuration;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;


/**
 * Created by PhpStorm.
 * User: rem
 * Date: 24.12.2014
 * Time: 11:05
 */
class ProplanController extends BackController
{
    public function actionList()
    {
        return $this->render([
            'proplans' => $this->model('Membership')->getProplans(),
            'pageName' => Yii::t('back', 'Участники'),
        ]);
    }

    public function actionUserslist($id = null)
    {
        return $this->render([
            'attributes' => MembershipDuration::dataAttributes(),
            'ajaxUrl' => Url::toRoute(['proplan/userslistajax', 'id' => $id]),
            'pageName' => Yii::t('back', 'Виды аккаунтов'),
            'tableFilteringSettings' => Membership::getProplansArray($id, 'proplan/userslist'),
        ]);
    }

    public function actionUserslistajax($id = null)
    {
        $this->validateAjax();

        $params = [
            'with' => ['membership', 'escort'],
            'where' => $id === null ? null : ['membership_id' => $id]
        ];

        $transaction = $this->model('MembershipDuration')->findByAjax($this->get(), $params);

        $this->ajax($transaction);
    }

    public function actionEdit($id)
    {
        $proplan = $this->model('Membership')->findById($id);
        if(!$proplan)
            throw new NotFoundHttpException(Yii::t('error', 'Не удалось найти проплан ID {id}', ['id' => $id]));

        $proplanForm = new EditProlanForm();
        $proplanForm->setEntity($proplan);

        if($proplanForm->load($this->getPost())){
            if($proplanForm->save())
                return $this->redirect(Url::to(['proplan/list']));
        }

        $regionsForm = new SearchForm();
        $duraionsForm = new ProplanDurationOptionsForm();

        return $this->render([
            'pageName' => Yii::t('back', 'Редатирование проплана "{proplan}"', ['proplan' => $proplan->getProplanName()]),
            'proplanForm' => $proplanForm,
            'regionsForm' => $regionsForm,
            'duraionsForm' => $duraionsForm,
        ]);
    }
}