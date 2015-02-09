<?php

namespace app\modules\backend\controllers;

use Yii;
use app\abstracts\BackController;
use app\models\Account;
use app\models\Escort;
use app\models\User;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 23.12.2014
 * Time: 13:11
 */
class AccountController extends BackController
{
    public function actionAll()
    {
        return $this->render('list', [
            'ajaxUrl' => Url::toRoute(['account/accountsajax']),
            'attributes' => Account::dataAttributes(),
        ]);
    }

    public function actionEscorts()
    {
        return $this->render('list', [
            'ajaxUrl' => Url::toRoute(['account/escortsajax']),
            'attributes' => Escort::dataAttributes(),
        ]);
    }

    public function actionUsers()
    {
        return $this->render('list', [
            'ajaxUrl' => Url::toRoute(['account/usersajax']),
            'attributes' => User::dataAttributes(),
        ]);
    }

    public function actionOnline()
    {
        return $this->render('list',[
            'ajaxUrl' => Url::toRoute(['account/onlineajax']),
            'attributes' => Account::dataAttributes(),
        ]);
    }

    public function actionAccountsajax()
    {
        $this->validateAjax();

        $accounts = $this->model('Account')->findByAjax($this->get());

        $this->ajax($accounts);
    }

    public function actionEscortsajax()
    {
        $this->validateAjax();

        $accounts = $this->model('Escort')->findByAjax($this->get(), 'escortInfo');

        $this->ajax($accounts);
    }

    public function actionUsersajax()
    {
        $this->validateAjax();

        $accounts = $this->model('User')->findByAjax($this->get());

        $this->ajax($accounts);
    }

    public function actionOnlineajax()
    {
        $this->validateAjax();

        $accounts = $this->model('Account')->findByAjax($this->get());

        $this->ajax($accounts);
    }

    public function actionLastregistered($role = null, $date = null)
    {
        $model = '\\app\\models\\'.$this->getModelByRoleWord($role);

        return $this->render('list', [
            'ajaxUrl' => Url::toRoute(['account/lastregisteredajax', 'role' => $role, 'date' => $date]),
            'attributes' => $model::dataAttributes(),
            'tableMultiFilteringSettings' => $this->getMultiFilteringSettings($role, $date)
        ]);
    }

    public function actionLastregisteredajax($role = null, $date = null)
    {
        $this->validateAjax();

        $model = $this->getModelByRoleWord($role);
        $params  = [];

        if($date != null && $date !== 'all-time')
            $params['where'] = ['>', 'registration_date', $this->getDateByDateWord($date)];

        $accounts = $this->model($model)->findByAjax($this->get(), $params);

        $this->ajax($accounts);
    }

    public function actionLastactive($role = null, $date = null)
    {
        $model = '\\app\\models\\'.$this->getModelByRoleWord($role);

        return $this->render('list', [
            'ajaxUrl' => Url::toRoute(['account/lastregisteredajax', 'role' => $role, 'date' => $date]),
            'attributes' => $model::dataAttributes(),
            'tableMultiFilteringSettings' => $this->getMultiFilteringSettings($role, $date)
        ]);
    }

    public function actionLastactiveajax($role = null, $date = null)
    {
        $this->validateAjax();

        $model = $this->getModelByRoleWord($role);
        $params  = [];

        if($date != null && $date !== 'all-time')
            $params['where'] = ['>', 'last_login', $this->getDateByDateWord($date)];

        $accounts = $this->model($model)->findByAjax($this->get(), $params);

        $this->ajax($accounts);
    }

    public function actionLoginbyuserid($id)
    {
        $account = $this->model('Account')->findById($id);
        if(!$account)
            throw new NotFoundHttpException(Yii::t('error', 'Пользователь ID {id} не найден', ['id' => $id]));

        if(!Yii::$app->user->login($account, 2600*24))
            return $this->redirect(Yii::$app->request->getReferrer());

        $route = Yii::$app->user->isEscort ? Url::to(['/frontend/escort/profile', 'id' => $id], true) : Url::to(['/frontend/user/profile', 'id' => $id], true);
        return $this->redirect($route);
    }

    public function actionDelete($id)
    {
        $this->validateAjax();

        $account = $this->model('Account')->findById($id);
        if(!$account)
            $this->ajaxError('Пользователь ID {id} не найден', ['id' => $id]);

        if($account->remove())
            $this->ajax(Yii::t('back', 'Пользователь ID {id} удалён'));
        else
            $this->ajaxError($account->errors);
    }

    private function getModelByRoleWord($role)
    {
        switch($role){
            case 'user':
                $model = 'User';
                break;
            case 'escort':
                $model = 'Escort';
                break;
            default:
                $model = 'Escort';
                break;
        }

        return $model;
    }

    private function getDateByDateWord($dateWord)
    {
        switch($dateWord){
            case 'day':
                $date = Yii::$app->local->getLastDayDateTime();
                break;
            case 'week':
                $date = Yii::$app->local->getLastWeekDateTime();
                break;
            case 'month':
                $date = Yii::$app->local->getLastMonthDateTime();
                break;
            case 'year':
                $date = Yii::$app->local->getLastYearDateTime();
                break;
            default:
                $date = 'all-time';
                break;
        }

        return $date;
    }

    private function getMultiFilteringSettings($role, $date)
    {
        $tableMultiFilteringSettings = [
            [
                'name' => Yii::t('back', 'Показывать только:'),
                'settings' => [
                    [
                        'name' => Yii::t('back', 'Эксорты'),
                        'url' => Url::toRoute(['account/lastregistered', 'role' => 'escort', 'date' => $date]),
                        'selected' => $role === 'escort' || $role === null
                    ],
                    [
                        'name' => Yii::t('back', 'Пользователи'),
                        'url' => Url::toRoute(['account/lastregistered', 'role' => 'user', 'date' => $date]),
                        'selected' => $role === 'user'
                    ]
                ],
            ],
            [
                'name' => Yii::t('back', 'Период:'),
                'settings' => [
                    [
                        'name' => Yii::t('back', 'За сутки'),
                        'url' => Url::toRoute(['account/lastregistered', 'role' => $role, 'date' => 'day']),
                        'selected' => $date === 'day'
                    ],
                    [
                        'name' => Yii::t('back', 'За неделю'),
                        'url' => Url::toRoute(['account/lastregistered', 'role' => $role, 'date' => 'week']),
                        'selected' => $date === 'week'
                    ],
                    [
                        'name' => Yii::t('back', 'За месяц'),
                        'url' => Url::toRoute(['account/lastregistered', 'role' => $role, 'date' => 'month']),
                        'selected' => $date === 'month'
                    ],
                    [
                        'name' => Yii::t('back', 'За год'),
                        'url' => Url::toRoute(['account/lastregistered', 'role' => $role, 'date' => 'year']),
                        'selected' => $date === 'year'
                    ],
                    [
                        'name' => Yii::t('back', 'За всё время'),
                        'url' => Url::toRoute(['account/lastregistered', 'role' => $role, 'date' => 'all-time']),
                        'selected' => $date === 'all-time' || $date === null
                    ]
                ],
            ]
        ];

        return $tableMultiFilteringSettings;
    }
}