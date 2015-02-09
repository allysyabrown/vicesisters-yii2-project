<?php

namespace app\abstracts;

use app\components\AjaxData;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 28.10.2014
 * Time: 21:55
 *
 * @property integer $id
 */
abstract class BaseModel extends ActiveRecord
{
    const FAST_WITH_KEY = 'fastData';

    /**
     * @var \app\abstracts\Repository
     */
    protected $repository;

    protected static $_className;

    public static function tableAttributes()
    {
        return [];
    }

    public static function dataAttributes($additional = null)
    {
        return self::parseAttributes(static::tableAttributes(), $additional);
    }

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
            }elseif($this->hasAttribute($name)){
                $this->setAttribute($name, $value);
                unset($attributes[$name]);
            }elseif(property_exists($this, $name)){
                $this->$name = $value;
                unset($attributes[$name]);
            }
        }

        if(!empty($attributes))
            parent::setAttributes($attributes, $safeOnly);
    }

    public function setRepository(\app\abstracts\Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param $name
     * @return Repository|null
     */
    public function model($name)
    {
        return Yii::$app->data->getRepository($name);
    }

    /**
     * @return \app\abstracts\Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public static function getModelName()
    {
        if(static::$_className === null){
            static::$_className = str_replace('\\', '_', parent::className());
        }

        return static::$_className;
    }

    /**
     * @param string $condition
     * @return BaseModel|null
     */
    public static function findCachedOne($condition)
    {
        $condition = trim($condition);

        if(is_string($condition) && strpos($condition, ' ') === false && strpos($condition, '=') === false)
            $condition = ['id' => (int)$condition];

        $query = static::find()
                    ->where($condition);

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['findOneCacheTime']);
    }

    public static function findCachedAll($condition)
    {
        $condition = trim($condition);

        if(is_string($condition) && strpos($condition, ' ') === false && strpos($condition, '=') === false && (int)$condition > 0)
            $condition = ['id' => (int)$condition];

        $query = static::find()
            ->where($condition);

        return Yii::$app->dbCache->getAll($query, Yii::$app->params['findAllCacheTime']);
    }

    public function addToCache($class = null)
    {
        if($class === null)
            $class = static::getModelName();


        Yii::$app->cache->set($class.':'.$this->id, $this);
    }

    /**
     * @return string
     */
    public function randomName()
    {
        return static::randomWord();
    }

    public function setErrors(array $errors)
    {
        foreach($errors as $name => $error){
            $this->addError($name, $errors);
        }
    }

    public static function randomWord()
    {
        $length = 30;
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    public static function getCacheKey($condition, $class = null)
    {
        if($class === null)
            $class = static::className();

        if(!is_array($condition))
            return $class.':'.$condition;
        else
            return $class.':'.json_encode($condition);
    }

    public function getFastData()
    {
        return [];
    }

    protected function setObjectAttributes(BaseModel $object, $attributes)
    {
        if($attributes){
            if(is_array($attributes)){
                $object->setAttributes($attributes);
                if(isset($attributes['id']))
                    $object->id = $attributes['id'];

                return $object;
            }

            if($attributes instanceof BaseModel){
                return $attributes;
            }
        }

        return null;
    }

    protected function setObjectsAttributes($object, $attributes)
    {
        $objects = [];

        if($attributes){
            $attributes = (array)$attributes;

            foreach($attributes as $attr){
                $objects[] = $this->setObjectAttributes(new $object(), $attr);
            }
        }

        return count($objects) > 0 ? $objects : null;
    }

    /**
     * @param array $attributes
     * @param array|null $additional
     * @return array
     */
    protected static function parseAttributes($attributes, $additional = null)
    {
        $res = [];
        if(!empty($attributes)){
            foreach($attributes as $name => $attribute){
                if($additional !== null && isset($attribute['type'])){
                    if($attribute['type'] === AjaxData::TABLE_BUTTON){
                        if(!in_array($name, $additional))
                            continue;
                    }
                }

                $attrName = null;
                $value = null;
                $sort = false;
                $search = false;
                $type = 'string';

                if(is_string($name) && strlen($name) > 1)
                    $attrName = $name;

                if(is_string($attribute))
                    $value = $attribute;

                if(is_array($attribute)){
                    if(isset($attribute['name']))
                        $attrName = $attribute['name'];
                    if(isset($attribute['value']))
                        $value = $attribute['value'];
                    if(isset($attribute['sort']))
                        $sort = $attribute['sort'];
                    if(isset($attribute['search']))
                        $search = $attribute['search'];
                    if(isset($attribute['type']))
                        $type = $attribute['type'];
                }elseif($attrName === null){
                    $attrName = $attribute;
                }

                $res[] = [
                    'name' => $attrName,
                    'sort' => $sort,
                    'value' => $value,
                    'search' => $search,
                    'type' => $type,
                ];
            }
        }

        return $res;
    }

    protected function button($name, $params = [])
    {
        $class = isset($params['class']) ? $params['class'].' ' : '';
        $params['class'] = $class.'button table-button';

        $id = isset($params['id']) ? $params['id'] : null;
        if($id === null && $this->id)
            $id = str_replace('\\', '_', $this->className()).'_button_'.$this->id;
        if($id !== null)
            $params['id'] = $id;

        $dataId = isset($params['data-id']) ? $params['data-id'] : $this->id;
        $params['data-id'] = $dataId;

        $button = Html::button($name, $params);
        return $button;
    }
} 