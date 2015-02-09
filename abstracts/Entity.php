<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jangolle
 * Date: 05.12.14
 * Time: 15:03
 * To change this template use File | Settings | File Templates.
 */

namespace app\abstracts;

use Yii;
use yii\base\Object;

class Entity extends Object
{
    public function setAttributes($params){
        if(is_string($params))
            $params = json_decode($params, true);
        if($params){
            foreach($params as $name => $param){
                $setter = 'set'.ucfirst($name);
                if(method_exists($this, $setter))
                    $this->$setter($param);
                elseif($this->hasProperty($name))
                    $this->$name = $param;
            }
        }
    }

    public static function load(array $params){
        $object = new static;
        $object->setAttributes($params);

        return $object;
    }
}