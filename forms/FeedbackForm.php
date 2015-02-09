<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 19.11.14
 * Time: 16:53
 * To change this template use File | Settings | File Templates.
 */

namespace app\forms;

use Yii;
use app\abstracts\BaseForm;

class FeedbackForm extends BaseForm
{
    public $escortId;
    public $userId;
    public $text;
    public $time;

    public function rules()
    {
        return [
            [['text'], 'string', 'message' => \Yii::t('error','В поле отзыва должен быть только текст')],
            [['text'], 'required', 'message' => \Yii::t('error','Это поле не может быть пустым')],
            [['escortId', 'userId'], 'integer'],
            [['time'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'escort_id' => Yii::t('front', 'ID владельца'),
            'user_id' => Yii::t('front', 'ID автора'),
            'text' => Yii::t('front', 'Текст отзыва').' ...',
            'time' => Yii::t('base', 'Дата'),
        ];
    }

    /**
     * @return bool|\app\models\Feedback
     */
    public function save()
    {
        if($this->userId === null)
            $this->userId = Yii::$app->user->id;

        if(!$this->validate())
            return false;

        $result =  Yii::$app->data->getRepository('Feedback')->addFeedback($this);

        return $result;
    }
}