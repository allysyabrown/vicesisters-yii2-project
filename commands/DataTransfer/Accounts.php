<?php

namespace app\commands\DataTransfer;


/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 11:32
 *
 * @property string $id
 * @property string $type
 * @property string $name
 * @property string $city_t
 * @property string $country_t
 * @property string $city
 * @property string $state
 * @property string $age
 * @property string $height
 * @property string $weight
 * @property string $eyes
 * @property string $hair
 * @property string $sex
 * @property string $ethnicity
 * @property string $language
 * @property string $stats
 * @property string $status
 * @property string $description
 * @property string $email
 * @property string $phone
 * @property string $web
 * @property string $password
 * @property string $orientation
 * @property string $booking
 * @property string $active
 * @property string $created
 * @property string $views
 * @property string $has_pic
 * @property string $rviews
 * @property string $popularity
 * @property string $lastAccess
 * @property string $credits
 *
 * @property \app\commands\DataTransfer\AccountPassports $passport
 * @property \app\commands\DataTransfer\AccountProperties $properties
 */
class Accounts extends Model
{
    public static function tableName()
    {
        return 'accounts';
    }

    public function getPassport()
    {
        return $this->hasOne(AccountPassports::className(), ['user_id' => 'id']);
    }

    public function getProperties()
    {
        return $this->hasOne(AccountProperties::className(), ['account_id' => 'id']);
    }

    public function getTravels()
    {
        return $this->hasMany(TravelList::className(), ['id' => 'id']);
    }
} 