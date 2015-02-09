<?php

namespace app\components;

use Yii;
use yii\widgets\ActiveForm as YiiForm;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 27.10.2014
 * Time: 20:23
 */
class ActiveForm extends YiiForm
{
    public static function begin($config = [], $returnable = false)
    {
        if($returnable)
            return parent::begin($config);
        else
            parent::begin($config);
        return null;
    }

    public static function end()
    {
        parent::end();
    }

    public function beginFirm($config = [], $returnable = false)
    {
        return self::begin($config, $returnable);
    }

    public function endForm()
    {
        self::end();
    }

    public function field($model, $attribute = '', $options = [], $returnable = false)
    {
        if(isset($options['placeholder'])){
            $placeholder = $options['placeholder'];
            $type = isset($options['type']) ? $options['type'] : 'text';
            unset($options['placeholder']);
            unset($options['type']);
        }else{
            $placeholder = '';
            $type = 'text';
        }

        if(isset($options['label'])){
            $showLabel = $options['label'];
            unset($options['label']);
        }else{
            $showLabel = true;
        }

        $field = parent::field($model, $attribute, $options);
        $specialOptions = [];

        if(isset($options['inputOptions'])){
            $specialOptions = $options['inputOptions'];
            unset($options['inputOptions']);
        }

        if($placeholder){
            $specialOptions['placeholder'] = $placeholder;

            $field->parts['{input}'] = Html::activeInput($type, $model, $attribute, $specialOptions);
        }elseif($specialOptions){
            $field->parts['{input}'] = Html::activeInput($type, $model, $attribute, $specialOptions);
        }

        if(!$showLabel)
            $field->parts['{label}'] = '';

        if($returnable)
            return $field;
        else
            echo $field;

        return null;
    }

    public function text($model, $attribute = '', $options = [])
    {
        $this->field($model, $attribute, $options);
    }

    public function password($model, $attribute = '', $options = [])
    {
        if(isset($options['passwordOptions'])){
            $passwordOptions = $options['passwordOptions'];
            unset($options['passwordOptions']);
        }else{
            $passwordOptions = [];
        }

        echo $this->field($model, $attribute, $options, true)->passwordInput($passwordOptions);
    }
    
    public function email($model, $attribute = '', $options = [])
    {
        $input = $this->field($model, $attribute, $options, true);
        $input->parts['{input}'] = Html::activeInput('email', $model, $attribute, $options);

        echo $input;
    }

    public function textarea($model, $attribute = '', $options = [])
    {
        if(isset($options['textareaOptions'])){
            $textareaOptions = $options['textareaOptions'];
            unset($options['textareaOptions']);
        }else{
            $textareaOptions = [];
        }

        echo $this->field($model, $attribute, $options, true)->textarea($textareaOptions);
    }

    public function radioList($model, $attribute = '', $options = [])
    {
        if(isset($options['items'])){
            $items = $options['items'];
            unset($options['items']);
        }else{
            $items = [];
        }

        if(isset($options['radioOptions'])){
            $radioOptions = $options['radioOptions'];
            unset($options['radioOptions']);
        }else{
            $radioOptions = [];
        }

        $radioOptions['unselect'] = null;

        echo $this->field($model, $attribute, $options, true)->radioList($items, $radioOptions);
    }

    public function checkboxList($model, $attribute = '', $options = [])
    {
        if(isset($options['items'])){
            $items = $options['items'];
            unset($options['items']);
        }else{
            $items = [];
        }

        if(isset($options['checkboxOptions'])){
            $checkboxOptions = $options['checkboxOptions'];
            unset($options['checkboxOptions']);
        }else{
            $checkboxOptions = [];
        }

        $checkboxOptions['unselect'] = null;
        $value = $model->$attribute;
        $options['inputOptions']['value'] = is_array($value) ? (isset($value[0]) ? $value[0] : '') : $value;

        echo $this->field($model, $attribute, $options, true)->checkboxList($items, $checkboxOptions);
    }

    public function hidden($model, $attribute = '', $options = [])
    {
        if(isset($options['hiddenOptions'])){
            $hiddenOptions = $options['hiddenOptions'];
            unset($options['hiddenOptions']);
        }else{
            $hiddenOptions = [];
        }

        $options['placeholder'] = false;
        $options['label'] = false;

        echo $this->field($model, $attribute, $options, true)->hiddenInput($hiddenOptions);
    }

    public function checkBox($model, $attribute = '', $options = [])
    {
        if(isset($options['checkBoxOptions'])){
            $checkBox = $options['checkBoxOptions'];
            unset($options['checkBoxOptions']);
        }else{
            $checkBox = [];
        }

        echo $this->field($model, $attribute, $options, true)->checkbox($checkBox);
    }

    public function select($model, $attribute = '', $options = [])
    {
        if(isset($options['items'])){
            $items = $options['items'];
            unset($options['items']);
        }else{
            $items = [];
        }

        if(isset($options['selectOptions'])){
            $selectOptions = $options['selectOptions'];
            unset($options['selectOptions']);
        }else{
            $selectOptions = [];
        }

        echo $this->field($model, $attribute, $options, true)->dropDownList($items, $selectOptions);
    }

    public static function submitButton($content = 'Submit', $options = [])
    {
        $options['type'] = 'submit';
        $options['class'] = isset($options['class']) ? $options['class'].' submit-button' : 'submit-button';

        if($content == 'Submit')
            $content = Yii::t('base', 'Принять');

        echo Html::button($content, $options);
    }

    public static function cancelButton($content = 'Cancel', $options = [])
    {
        $options['type'] = 'submit';
        $options['class'] = isset($options['class']) && $options['class'] ? $options['class'].' cancel-button' : 'cancel-button';

        if($content == 'Cancel')
            $content = Yii::t('base', 'Отмена');

        echo Html::button($content, $options);
    }

    public function file($model, $attribute = '', $options = [])
    {
        if(isset($options['fileOptions'])){
            $fileOptions = $options['fileOptions'];
            unset($options['fileOptions']);
        }else{
            $fileOptions = [];
        }

        echo $this->field($model, $attribute, $options, true)->fileInput($fileOptions);
    }
}
