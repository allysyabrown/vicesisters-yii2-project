<?php
/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 06.10.14
 * Time: 19:42
 */

namespace app\abstracts;

use Yii;
use app\models\Account;
use yii\web\ForbiddenHttpException;

abstract class BackController extends BaseController
{
    private $access = [
        Account::ROLE_USER => [
            'ajax' => '*'
        ],
    ];

    public function beforeAction($action)
    {
        $role = Yii::$app->user->getRole();
        if(!$this->checkAccess($role, $this->access))
            throw new ForbiddenHttpException(Yii::t('error', 'В доступе отказано'));

        return parent::beforeAction($action);
    }
}