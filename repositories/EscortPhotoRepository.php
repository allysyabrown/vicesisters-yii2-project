<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 17.11.14
 * Time: 10:53
 * To change this template use File | Settings | File Templates.
 */

namespace app\repositories;

use app\components\FtpManager;
use app\models\Escort;
use Yii;
use app\components\FastData;
use app\abstracts\Repository;
use app\components\StaticData;
use app\models\EscortPhoto;
use yii\image\drivers\ImageGD;

class EscortPhotoRepository extends Repository
{
    /**
     * @param ImageGD $image
     * @param $escortId
     * @param $hostId
     */
    public function add(ImageGD $image, $escortId, $hostId)
    {
        $image->crop(500,400,500,320)->rotate(-25)->resize(250,200)->save();

        $this->entity->setAttributes([
            'path' => $this->entity->randomName().StaticData::EXTENSION_IMAGE,
            'escort_id' => $escortId,
            'host_id' => $hostId,
        ]);

        if(Yii::$app->static->uploadImg($this->entity->path, $image->file, $this->entity->host_id)){
            $this->save(true);
        }
    }

    public function getById($id)
    {
        $id = (int)$id;
        if(!$id)
            return null;

        return $this->entity->findCachedOne($id);
    }

    public function getRandomEscortPhoto($id)
    {
        $key = FastData::RANDOM_PHOTO_KEY.':'.$id;
        $image = Yii::$app->cache->get($key);

        if(!$image){
            $img = EscortPhoto::find()
                        ->select(['path', 'host_id'])
                        ->where(['escort_id' => $id, 'verified' => EscortPhoto::VERIFIED])
                        ->orderBy('RANDOM()')
                        ->one();

            if($img)
                $image = $this->getUserPhoto($img->path, $img->host_id);
            else
                $image = (new Escort())->getDefaultAvatar();

            Yii::$app->cache->set($key, $image, Yii::$app->params['randomImageCacheTime']);
        }

        return $image;
    }

    public function getEscortPhotos($escortId)
    {
        $escortId = (int)$escortId;
        if(!$escortId)
            return null;

        return $this->findByEscortId($escortId);
    }

    public function updatePhotosCache($escortId)
    {
        $query = EscortPhoto::find()->where(['escort_id' => $escortId]);
        Yii::$app->dbCache->update($query, Yii::$app->params['escortPhotoGalleryCacheTime']);
    }

    public function getPrevPhoto($id, $escortId)
    {
        $id = (int)$id;
        if(!$id > 1)
            return null;

        $photos = $this->getEscortPhotos($escortId);
        $result = null;

        if($photos){
            foreach($photos as $num => $photo){
                if($photo->id == $id){
                    if($num > 0)
                        $result = $photos[$num - 1];
                    else
                        $result = end($photos);

                    break;
                }
            }
        }

        return $result;
    }

    public function getNextPhoto($id, $escortId)
    {
        $id = (int)$id;
        if(!$id > 1)
            return null;

        $photos = $this->getEscortPhotos($escortId);
        $result = null;

        if($photos){
            foreach($photos as $num => $photo){
                if($photo->id == $id){
                    if(isset($photos[$num + 1]))
                        $result = $photos[$num + 1];
                    else
                        $result = array_shift($photos);

                    break;
                }
            }
        }

        return $result;
    }

    private static function likesCompare(EscortPhoto $a, EscortPhoto $b)
    {
        $aLikes = $a->getLikes();
        $bLikes = $b->getLikes();

        return $aLikes < $bLikes;
    }

    public function getTopPhotos($escortId)
    {
        $topPhotos = $this->sortByLikesDesc($this->findByEscortId($escortId));
        $topPhotos = array_slice($topPhotos, 0, Yii::$app->params['topPhotosCount'] - 1);

        return $topPhotos;
    }

    private function sortByLikesDesc($photos)
    {
        @usort($photos, [self::className(),'likesCompare']);

        return $photos;
    }

    public function getNewPhotos($limit)
    {
        $query = EscortPhoto::find()
            ->limit($limit)
            ->orderBy('id DESC');

        $photos = Yii::$app->dbCache->getAll($query, Yii::$app->params['escortPhotoGalleryCacheTime']);

        return $photos;
    }

    public function getUserPhoto($ava, $hostId = 1)
    {
        $key = FastData::ACCOUNT_AVATAR_KEY.':'.$ava;
        $image = Yii::$app->cache->get($key);
        if(!$image){
            $image = Yii::$app->static->getImg($ava, $hostId);
            Yii::$app->cache->set($key, $image, Yii::$app->params['userAvatarCacheTime']);
        }

        return $image;
    }

    public function addNewPhoto($data, $hostId = 1)
    {
        $image = Yii::$app->image->uploadImageFromSource($data, $hostId);
        $image = trim($image);

        if(!$image)
            return false;

        $path = explode('/img/',$image)[1];

        $escortId = Yii::$app->user->id;

        $escortPhoto = new EscortPhoto();
        $escortPhoto->setAttributes([
            'host_id' => $hostId,
            'escort_id' => $escortId,
            'path' => $path,
        ]);
        $escortPhoto->save();

        $this->updatePhotosCache($escortId);

        return $escortPhoto;
    }

    public function findByEscortId($id)
    {
        $query = EscortPhoto::find()->where(['escort_id' => $id]);
        return Yii::$app->dbCache->getAll($query, Yii::$app->params['escortPhotoGalleryCacheTime']);
    }

    public function remove($id)
    {
        $photo = EscortPhoto::findOne(['id' => $id]);

        Yii::$app->ftp->connect($photo->host);

        if(EscortPhoto::deleteAll(['id' => $id]) && Yii::$app->ftp->del('img/'.$photo->path)){

            $escortId = Yii::$app->user->id;
            $this->updatePhotosCache($escortId);

            return true;
        }
        return false;
    }
}