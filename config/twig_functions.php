<?php

return [
    'alias' => '\Yii::getAlias',
    'form_begin' => '\app\components\ActiveForm::begin',
    'form_end' => '\app\components\ActiveForm::end',
    'textField' => '\app\components\ActiveForm::text',
    'passwordField' => '\app\components\ActiveForm::password',
    'submit_button' => '\app\components\ActiveForm::submitButton',
    'cancel_button' => '\app\components\ActiveForm::cancelButton',
    't' => '\Yii::t',
    'img' => '\Yii::$app->static->getImg',
    'css' => '\Yii::$app->static->getCss',
    'script' => '\Yii::$app->static->getScript',
    'cropper' => '\yii\jcrop\jCrop::widget',
    'time' => 'time',
    'date' => 'date',
    'urldecode' => 'urldecode',
    'onlineCount' => '\Yii::$app->fastData->getOnlineCount',
    'substr' => '\Yii::$app->static->subStr',

    // Chat
    'chatNewMessageUrl' => 'Yii::$app->chat->getNewMessageUrl',
];