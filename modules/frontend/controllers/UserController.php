<?php

namespace app\modules\frontend\controllers;

use app\forms\UserAccountForm;
use Yii;
use app\abstracts\FrontController;
use yii\web\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 10.12.2014
 * Time: 12:54
 */
class UserController extends FrontController
{
    public function actionIndex()
    {
        $id = Yii::$app->user->id;

        $user = $this->model('User')->findById($id);
        if(!$user)
            throw new NotFoundHttpException(Yii::t('error', 'Не удалось найти пользователя ID {id}', ['id' => $id]));

        $accountForm = new UserAccountForm();

        $accountForm->setUser($user);

        if($accountForm->load($this->getPost())){
            if($accountForm->save())
                $this->goUserHome();
        }

        return $this->render([
            'user' => $user,
            'accountForm' => $accountForm,
        ]);
    }

    public function actionProfile($id)
    {
        $user = $this->model('User')->findCashedById($id);

        $lastMembers = $this->model('Escort')->getNewMembers(4);
        $lastPhotos = $this->model('EscortPhoto')->getNewPhotos(4);

        if(!$user)
            throw new NotFoundHttpException(Yii::t('error', 'Не удалось найти пользователя ID {id}', ['id' => $id]));

        return $this->render([
            'user' => $user,
            'lastVerified' => $lastMembers,
            'lastPhotos' => $lastPhotos,
        ]);
    }

    public function actionChangeavatar()
    {
        $this->validateAjax();
        $image = $this->getPost('image');
        if(!$image)
            $this->ajaxError('Неверные данные');

        if(!$this->model('Account')->setAvatar($this->getPost('image')))
            $this->ajaxError('Неверные данные');
        else
            $this->ajax('OK');
    }
} 