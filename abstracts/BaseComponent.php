<?php

namespace app\abstracts;

use Yii;
use yii\base\Component;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 13.11.14
 * Time: 13:37
 */
class BaseComponent extends Component
{
    protected $_errors = [];

    public function addError($error)
    {
        $this->_errors[] = Yii::t('payment', $error);
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getError()
    {
        return implode('; ', $this->getErrors());
    }

    public function hasError()
    {
        return count($this->getErrors()) !== 0;
    }
} 