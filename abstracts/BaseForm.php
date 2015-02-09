<?php

namespace app\abstracts;

use Yii;
use app\components\ActiveForm;
use yii\base\Model;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm as YiiForm;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 28.10.2014
 * Time: 23:19
 *
 * @property \yii\web\UploadedFile $file
 */
abstract class BaseForm extends Model
{
    /**
     * @var \app\components\ActiveForm
     */
    public $form;

    public $showPlaceholders = true;
    public $showLabels = false;

    /**
     * @param array $attributes
     * @param bool $safeOnly
     */
    public function setAttributes($attributes, $safeOnly = true)
    {
        foreach($attributes as $name => $value){
            $setter = 'set'.ucfirst($name);
            if(method_exists($this, $setter)){
                $this->$setter($value);
                unset($attributes[$name]);
            }elseif(property_exists($this, $name)){
                $this->$name = $value;
                unset($attributes[$name]);
            }
        }

        if(count($attributes) > 0)
            parent::setAttributes($attributes, $safeOnly);
    }

    public function label($name = '')
    {
        $labels = $this->attributeLabels();

        if(isset($labels[$name]))
            return $labels[$name];

        return $name;
    }

    /**
     * @return string
     */
    public function randomName()
    {
        $length = 30;
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    public function hostId()
    {
        return 1;
    }

    public function begin($config = [])
    {
        if(!isset($config['id'])){
            $classParts = explode('\\', get_class($this));
            $config['id'] = end($classParts).'-form';
        }

        if(isset($config['placeholders'])){
            $this->showPlaceholders = $config['placeholders'] === true;
            unset($config['placeholders']);
        }

        if(isset($config['labels'])){
            $this->showLabels = $config['labels'] === true;
            unset($config['labels']);
        }

        $this->form = ActiveForm::begin($config, true);
    }

    public function end()
    {
        ActiveForm::end();
    }

    public function text($attr, $config = [])
    {
        $this->field('text', $attr, $config);
    }

    public function password($attr, $config = [])
    {
        $this->field('password', $attr, $config);
    }

    public function mail($attr, $config = [])
    {
        $this->field('email', $attr, $config);
    }

    public function textarea($attr, $config = [])
    {
        $this->field('textarea', $attr, $config);
    }

    public function upload($attr, $config = [])
    {
        $this->field('file', $attr, $config);
    }

    public function hidden($attr, $config = [])
    {
        $this->field('hidden', $attr, $config);
    }

    public function checkBox($attr, $config = [])
    {
        $this->field('checkBox', $attr, $config);
    }

    public function select($attr, $config = [])
    {
        $itemsGetter = 'get'.ucfirst($attr).'Items';

        if(method_exists($this, $itemsGetter))
            $items = $this->$itemsGetter();
        else
            $items = [];

        if(isset($config['items'])){
            $config['items'] = array_merge($items, $config['items']);
        }else{
            $config['items'] = $items;
        }

        $this->field('select', $attr, $config);
    }

    public function radioList($attr, $config = [])
    {
        $itemsGetter = 'get'.ucfirst($attr).'Items';

        if(method_exists($this, $itemsGetter))
            $items = $this->$itemsGetter();
        else
            $items = [];

        if(isset($config['items'])){
            $config['items'] = array_merge($items, $config['items']);
        }else{
            $config['items'] = $items;
        }

        //$this->showLabels = true;

        $this->field('radioList', $attr, $config);
    }

    public function checkboxList($attr, $config = [])
    {
        $itemsGetter = 'get'.ucfirst($attr).'Items';

        if(method_exists($this, $itemsGetter))
            $items = $this->$itemsGetter();
        else
            $items = [];

        if(isset($config['items'])){
            $config['items'] = array_merge($items, $config['items']);
        }else{
            $config['items'] = $items;
        }

        //$this->showLabels = true;

        $this->field('checkboxList', $attr, $config);
    }

    public function submit($content = 'Submit', $options = [])
    {
        ActiveForm::submitButton($content, $options);
    }

    public function cancel($content = 'Cancel', $options = [])
    {
        ActiveForm::cancelButton($content, $options);
    }

    public function field($type, $attr, $config = [])
    {
        if($this->showPlaceholders || (isset($config['placeholder']) && $config['placeholder'] === true)){
            $config['placeholder'] = $this->label($attr);
            $config['type'] = $type;
        }

        if($this->showLabels || (isset($config['labels']) && $config['labels'] === true)){
            $config['label'] = true;
        }else{
            $config['label'] = false;
        }

        switch($type){
            case 'password':
                $method = 'password';
                $config = $this->parseConfig($method, $config);
                break;
            case 'email':
                $method = 'email';
                break;
            case 'textarea':
                $method = 'textarea';
                $config = $this->parseConfig($method, $config);
                break;
            case 'file':
                $method = 'file';
                $config = $this->parseConfig($method, $config);
                break;
            case 'hidden':
                $method = 'hidden';
                break;
            case 'checkBox':
                $method = 'checkBox';
                break;
            case 'select':
                $method = 'select';
                break;
            case 'radioList':
                $method = 'radioList';
                break;
            case 'checkboxList':
                $method = 'checkboxList';
                break;
            default:
                $method = 'text';
                break;
        }

        $this->form->$method($this, $attr, $config);
    }

    public function setErrors(array $errors)
    {
        foreach($errors as $name => $error){
            $this->addError($name, $errors);
        }
    }

    public function uploadFile($attrName = 'file', $params = [], $hostId = null)
    {
        if($hostId === null)
            $hostId = $this->hostId();

        $image = Yii::$app->image->upload($this, $attrName, $params, $hostId);

        return $this->hasErrors() ? null : $image;
    }

    public function dropDownList($attribute, $items, $params = [], $return = false)
    {
        $field = new ActiveField();

        $field->form = new YiiForm();
        $field->model = $this;

        $field->attribute = $attribute;
        if(!$this->showLabels)
            $field->parts['{label}'] = '';

        if($return){
            $field->begin();
            $field->dropDownList($items, $params);
            $field->end();
            return $field;
        }

        echo $field->begin();
        echo $field->dropDownList($items, $params);
        echo $field->end();

        return null;
    }

    protected function parseArrayToString(array $array)
    {
        return '{'.implode(',', $array).'}';
    }

    protected function parseStringToArray($string)
    {
        $array = explode(',', str_replace('{', '', str_replace('}', '', $string)));
        foreach($array as $num => $val){
            $array[$num] = trim(str_replace('"', '', $val));
        }
        return $array;
    }

    /**
     * @param $name
     * @return Repository|null
     */
    protected function model($name)
    {
        return Yii::$app->data->getRepository($name);
    }

    private function parseConfig($type, $config)
    {
        if(isset($config['placeholder'])){
            $specificName = $type.'Options';

            if(isset($config[$specificName]))
                $specificOptions = $config[$specificName];
            else
                $specificOptions = [];

            $specificOptions['placeholder'] = $config['placeholder'];
            unset($config['placeholder'], $config['type']);

            $config[$specificName] = $specificOptions;
        }

        return $config;
    }
}