<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 12.11.14
 * Time: 16:06
 * To change this template use File | Settings | File Templates.
 */

namespace app\components;

use Yii;
use app\abstracts\BaseModel;
use app\abstracts\BaseForm;
use app\abstracts\BaseComponent;
use yii\base\Exception;
use yii\image\ImageDriver;
use yii\web\UploadedFile;

class Image extends BaseComponent
{
    /**
     * @var string
     */
    public $driver = 'GD';

    /**
     * @var ImageDriver
     */
    private $_imageDriver;

    /**
     * @var string
     */
    private $_image;

    public function init()
    {
        $this->getImageDriver();
    }

    /**
     * @return ImageDriver
     */
    private function getImageDriver()
    {
        if($this->_imageDriver === null){
            $imageDriver = new ImageDriver();
            $imageDriver->driver = $this->driver;

            $this->_imageDriver = $imageDriver;
        }

        return $this->_imageDriver;
    }

    /**
     * @param BaseForm $model
     * @param string $attrName
     * @param array $params
     * @param int $hostId
     * @throws \yii\base\ErrorException
     * @return bool|string
     */
    public function upload(BaseForm $model, $attrName = 'file', $params = [], $hostId = 1){
        if(!$_FILES)
            return false;;

        $model->$attrName = UploadedFile::getInstance($model, $attrName);
        if($model->$attrName)
            $this->_image = $model->$attrName->tempName;

        $image = $this->load($this->_image);

        if(!$image)
            return false;

        if($params === null)
            $params = [];

        $width = isset($params[0]) ? $params[0] : null;
        $height = isset($params[1]) ? $params[1] : 200;

        if(!$image->resize($width, $height)->save()){
            $model->addError($attrName, Yii::t('error', 'Не удалось загрузить файл'));
            return false;
        }

        $fileName = BaseModel::randomWord().StaticData::EXTENSION_IMAGE;

        if(!Yii::$app->static->uploadImg($fileName, $image->file, $hostId)){
            $model->addError($attrName, Yii::t('error', 'Не удалось загрузить файл'));
            return false;
        }

        return Yii::$app->static->getImg($fileName, $hostId);
    }

    public function uploadImageFromSource($data, $hostId = 1)
    {
        $name = $this->uploadImageFromSourceNoHost($data, $hostId);
        return Yii::$app->static->getImg($name, $hostId);
    }

    public function uploadImageFromSourceNoHost($data, $hostId = 1)
    {
        $data = str_replace('data:image/jpeg;base64,', '', $data);
        $data = base64_decode($data);

        try{
            $im = imagecreatefromstring($data);

            if($im === false){
                $this->addError(Yii::t('error', 'Не удалось создать изображение'));
                return false;
            }

            $name = BaseModel::randomWord().StaticData::EXTENSION_IMAGE;
            $fileName = sys_get_temp_dir().DIRECTORY_SEPARATOR.$name;

            $im = imagejpeg($im, $fileName);

            if($im === false){
                $this->addError(Yii::t('error', 'Не удалось создать изображение'));
                return false;
            }

            $path = Yii::$app->static->uploadImg($name, $fileName, $hostId);

            if($path === false){
                if($im === false){
                    $this->addError(Yii::t('error', 'Не удалось сохранить изображение'));
                    return false;
                }
            }

            return $path.'/'.$name;
        }catch(Exception $e){
            $this->addError(Yii::t('error', $e->getMessage()));
            return false;
        }
    }

    /**
     * @param $file
     * @return \yii\image\drivers\ImageGD
     */
    private function load($file)
    {
        return $this->getImageDriver()->load($file);
    }
}