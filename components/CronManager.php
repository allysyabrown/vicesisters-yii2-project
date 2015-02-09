<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 28.11.14
 * Time: 16:32
 * To change this template use File | Settings | File Templates.
 */

namespace app\components;


use yii\base\Component;

class CronManager extends Component
{
    const FILE_NAME = "crontab";
    const DELIMITER = "# m h dom mon dow user	command\n";

    /**
     * @var string
     */
    public $root;

    /**
     * @var string
     */
    private $_file;

    /**
     * @var string
     */
    private $_body;

    /**
     * @var array
     */
    private $_tasks;

    public function init()
    {
        $this->_file = file_get_contents($this->root.self::FILE_NAME);
        $this->_body = explode(self::DELIMITER,$this->_file)[1];
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @return $this
     */
    public function tasks()
    {
        $removeEmpty = function($element){
            return $element != '';
        };

        $this->_tasks = array_filter(explode("\n",$this->_body),$removeEmpty);

        return $this;
    }

    /**
     * @return array
     */
    public function enabled()
    {
        $removeNonActive = function($element){
            return substr($element, 0, 1) !== '#';
        };

        return array_filter($this->_tasks,$removeNonActive);
    }

    /**
     * @return array
     */
    public function disabled()
    {
        $removeActive = function($element){
            return substr($element, 0, 1) === '#';
        };

        return array_filter($this->_tasks,$removeActive);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->_tasks;
    }

    public function add($task)
    {
        file_put_contents($this->root.self::FILE_NAME, $task.PHP_EOL, FILE_APPEND);
    }

    /**
     * @param null | integer $index
     * @return bool
     */
    public function remove($index = null)
    {
        if($index === null)
            return false;

        $removed = $this->_tasks[$index];
        $newContent = str_replace($removed,'',$this->_file);

        if(file_put_contents($this->root.self::FILE_NAME, $newContent))
            return true;

        return false;
    }
}