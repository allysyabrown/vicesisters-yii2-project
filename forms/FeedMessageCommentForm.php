<?php
/**
 * Created by PhpStorm.
 * User: rem
 * Date: 27.11.2014
 * Time: 15:58
 */

namespace app\forms;

use app\models\FeedMessageComment;
use Yii;
use app\abstracts\BaseForm;

class FeedMessageCommentForm extends BaseForm
{
    public $id;
    public $title;
    public $text;
    public $feedMessageId;
    public $ownerId;
    public $escortId;
    public $date;

    public function rules()
    {
        return [
            [['text'], 'required'],
            [['feedMessageId', 'ownerId', 'escortId'], 'integer'],
            [['title'], 'string', 'max' => 256],
            [['text'], 'string'],
            [['date'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'feedMessageId' => Yii::t('front', 'ID новости'),
            'ownerId' => Yii::t('front', 'ID автора'),
            'title' => Yii::t('base', 'Заголовок'),
            'text' => Yii::t('base', 'Текст вашего комментария'),
            'date' => Yii::t('base', 'Дата'),
        ];
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        return Yii::$app->data->getRepository('FeedMessage')->addFeedComment($this);
    }
}