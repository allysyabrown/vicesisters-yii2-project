<?php
/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 19:18
 */

namespace app\components;

use Yii;
use app\forms\SearchForm;
use yii\redis\Session as YiiSession;

class Session extends YiiSession
{
    const REGION_SETTINGS_KEY = 'user_region_settings';

    /**
     * @var \app\entities\Geo
     */
    private $_searchSettings;

    public function setSearchSettings($params)
    {
        $this->resetSearchSettings();
        $this->getSearchSettings()->load($params);

        $this->set(self::REGION_SETTINGS_KEY, $this->getSearchSettings()->getAttributes());
    }

    /**
     * @return \app\forms\SearchForm
     */
    public function getSearchSettings()
    {
        if($this->_searchSettings === null){
            $this->_searchSettings = new SearchForm();
            $settings = $this->get(self::REGION_SETTINGS_KEY);
            if($settings)
                $this->_searchSettings->setAttributes($settings);
        }

        return $this->_searchSettings;
    }

    public function resetSearchSettings()
    {
        $this->_searchSettings = new SearchForm();
        $id = Yii::$app->user->isGuest ? $this->getId() : Yii::$app->user->id;
        Yii::$app->fastData->del(FastData::USER_REGION_SETTINGS_KEY.':'.$id);
        $this->remove(self::REGION_SETTINGS_KEY);
    }
}