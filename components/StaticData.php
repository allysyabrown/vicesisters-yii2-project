<?php
namespace app\components;

use app\models\Host;
use Yii;
use yii\base\Component;
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 05.11.14
 * Time: 11:57
 * To change this template use File | Settings | File Templates.
 */
class StaticData extends Component
{
    const PROTOCOL = 'http://';
    const EXTENSION_STYLE = '.css';
    const EXTENSION_SCRIPT = '.js';
    const EXTENSION_IMAGE = '.jpg';

    const TYPE_IMAGE = 1;
    const TYPE_CSS = 2;
    const TYPE_SCRIPT = 3;

    /**
     * @var Host
     */
    public $host;

    public $basePath = '/';

    public $cssRoot = 'css/';

    public $jsRoot = 'js/';

    public $imgRoot = 'img/';

    public $defaultHostId;

    /**
     * @var \app\components\FtpManager
     */
    private $_ftp;

    public function init()
    {
        $this->getHost();
        $this->_ftp = Yii::$app->ftp;
    }

    /**
     * @param null $id
     * @return Host
     */
    public function getHost($id = null)
    {
        if($this->host === null){
            if($id === null){
                $id = $this->defaultHostId;
            }
            $this->host = Yii::$app->data->getRepository('Host')->findHost($id);
        }

        return $this->host;
    }

    public function getBaseUrl($hostId = null){
        return self::PROTOCOL.$this->getHost($hostId)->name.$this->basePath;
    }

    public function getCssPath(){
        return $this->getBaseUrl().$this->cssRoot;
    }

    public function getScriptsPath(){
        return $this->getBaseUrl().$this->jsRoot;
    }

    public function getImgPath($hostId = null){
        if($hostId === null)
            $hostId = $this->defaultHostId;

        return $this->getBaseUrl($hostId).$this->imgRoot;
    }

    public function getCss($fileName){
        return $this->getCssPath().$fileName.self::EXTENSION_STYLE;
    }

    public function getScript($fileName){
        return $this->getScriptsPath().$fileName.self::EXTENSION_SCRIPT;
    }

    public function getImg($file, $hostId = null){
        return $this->getImgPath($hostId).$file;
    }

    public function uploadImg($remote, $temp, $hostId = null){
        $ftp = $this->_ftp->connect($this->getHost($hostId));

        $ftp->cd('img');

        $date = new \DateTime();
        $y = $date->format('Y');
        $m = $date->format('m');
        $d = $date->format('d');

        if(!$ftp->isDir($y)){
            $ftp->mkdir($y);
            $ftp->mkdir($y.'/'.$m);
            $ftp->mkdir($y.'/'.$m.'/'.$d);
            $ftp->cd($y.'/'.$m.'/'.$d);
        }elseif(!$ftp->isDir($m)){
            $ftp->mkdir($m);
            $ftp->mkdir($m.'/'.$d);
            $ftp->cd($m.'/'.$d);
        }elseif(!$ftp->isDir($d)){
            $ftp->mkdir($d);
            $ftp->cd($d);
        }

        $ftp->put($remote, $temp);

        $ftp->close();

        return $y.'/'.$m.'/'.$d;
    }

    public function subStr($string, $len)
    {
        return mb_substr($string, 0, $len, 'UTF-8');
    }
}