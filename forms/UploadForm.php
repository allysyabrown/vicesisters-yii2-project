<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 12.11.14
 * Time: 18:14
 * To change this template use File | Settings | File Templates.
 */

namespace app\forms;

use app\abstracts\BaseForm;
use yii\web\UploadedFile;

class UploadForm extends BaseForm {

    /**
     * @var UploadedFile|Null file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'image', 'skipOnEmpty' => false, 'message' => \Yii::t('error','Загруженный файл не является изображением')],
        ];
    }
}