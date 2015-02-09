<?php

namespace app\forms;

use Yii;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 24.11.2014
 * Time: 15:34
 */
class ChatForm extends BaseForm
{
    public $id;
    public $userId;
    public $userName;
    public $message;
    public $date;

    public function rules()
    {
        return [
            [['userId', 'userName', 'message'], 'required', 'message' => Yii::t('error', 'Это поле не может быть пустым')],
            [['userName', 'message'], 'string', 'message' => Yii::t('error', 'Это текстовое поле')],
            [['date'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('front', 'ID'),
            'userId' => Yii::t('front', 'ID пользователя'),
            'userName' => Yii::t('front', 'Пользователь'),
            'message' => Yii::t('front', 'Сообщение'),
            'date' => Yii::t('front', 'Время'),
        ];
    }


}