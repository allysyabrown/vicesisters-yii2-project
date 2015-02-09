<?php
/**
 * Created by PhpStorm.
 * User: rem
 * Date: 27.11.2014
 * Time: 15:58
 */

namespace app\forms;


use Yii;
use app\abstracts\BaseForm;

class FeedMessageForm extends BaseForm
{
    public $id;
    public $title;
    public $text;
    public $escortId;
    public $ownerId;
    public $date;

    public function rules()
    {
        return [
            [['text'], 'required'],
            [['escortId', 'ownerId'], 'integer'],
            [['title'], 'string', 'max' => 256],
            [['text'], 'string'],
            [['date'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'escort_id' => Yii::t('front', 'ID владельца'),
            'owner_id' => Yii::t('front', 'ID автора'),
            'title' => Yii::t('base', 'Заголовок'),
            'text' => Yii::t('base', 'Что у вас нового?'),
            'date' => Yii::t('base', 'Дата'),
        ];
    }

    /**
     * @return bool|\app\models\FeedMessage
     */
    public function save()
    {
        if(!$this->validate())
            return false;

        if(!$this->ownerId)
            $this->ownerId = Yii::$app->user->id;

        return  Yii::$app->data->getRepository('FeedMessage')->addFeedMessage($this);
    }
}