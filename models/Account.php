<?php

namespace app\models;


use Yii;
use app\abstracts\UserEntity;
use yii\web\IdentityInterface;
use yii\helpers\Url;
use app\components\AjaxData;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string $user_name
 * @property string $password
 * @property string $role
 * @property float $balance
 * @property string $userName
 * @property string $favorites
 * @property string $avatar
 * @property array $rolesNames
 *
 * @property string $fullName
 * @property bool $isOnline
 * @property string $roleName
 */
class Account extends UserEntity implements IdentityInterface
{
    const SEX_MALE = 2;
    const SEX_FEMALE = 1;
    const SEX_SHEMALE = 0;

    const ORIENTATION_STRAIGHT = 2;
    const ORIENTATION_BI = 1;
    const ORIENTATION_GEY = 0;

    const ETHNICITY_ASIAN = 0;
    const ETHNICITY_CAUCASIAN = 1;
    const ETHNICITY_BLACK = 2;
    const ETHNICITY_INDIAN = 3;
    const ETHNICITY_LATIN = 4;
    const ETHNICITY_MIDDLE_EAST = 5;
    const ETHNICITY_NATIVE_AMERICAN = 6;
    const ETHNICITY_WHITE = 7;
    const ETHNICITY_OTHER = 8;

    const EYES_BROWN = 0;
    const EYES_BLUE = 1;
    const EYES_GREEN = 2;
    const EYES_GREY = 3;
    const EYES_HAZEL = 4;
    const EYES_OTHER = 5;

    const HAIR_AUBURN = 0;
    const HAIR_BLONDE = 1;
    const HAIR_BLACK = 2;
    const HAIR_BROWN = 3;
    const HAIR_GREY = 4;
    const HAIR_RED = 5;
    const HAIR_OTHER = 6;

    const ROLE_GUEST = 'ROLE_GUEST';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ESCORT = 'ROLE_ESCORT';
    const ROLE_VERIFIED_ESCORT = 'ROLE_VERIFIED_ESCORT';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const ESCORT_TABLE = 'escort';
    const USER_TABLE = 'user';

    private static $_rolesIerarchy = [
        self::ROLE_ADMIN => [
            self::ROLE_VERIFIED_ESCORT,
        ],
        self::ROLE_VERIFIED_ESCORT => [
            self::ROLE_ESCORT,
        ],
        self::ROLE_ESCORT => [
            self::ROLE_USER,
            self::ROLE_GUEST,
        ],
        self::ROLE_USER => [
            self::ROLE_GUEST,
        ]
    ];

    private static $_sexItems = [
        self::SEX_FEMALE => 'Женщина',
        self::SEX_MALE => 'Мужчина',
        self::SEX_SHEMALE => 'Транссексуал',
    ];

    private static $_orientationItems = [
        self::ORIENTATION_STRAIGHT => 'Стандартная',
        self::ORIENTATION_BI => 'Бисексуал',
        self::ORIENTATION_GEY => 'Гей/лесбиянка',
    ];

    private static $_rolesNames = [
        self::ROLE_GUEST => 'Гость',
        self::ROLE_USER => 'Пользователь',
        self::ROLE_ESCORT => 'Эскорт',
        self::ROLE_VERIFIED_ESCORT => 'Верифицированный эскорт',
        self::ROLE_ADMIN => 'Админ',
    ];

    /**
     * @var Escort|User
     */
    private static $_user;

    private static $_rolesNamesArrey;

    protected $_userModelName;

    public $phone;

    public $fullName;

    public static function tableAttributes()
    {
        return [
            'id' => [
                'value' => Yii::t('base', 'ID'),
                'type' => 'int',
                'sort' => true,
                'search' => true,
            ],
            'loginUrl' => [
                'value' => Yii::t('base', 'Email'),
                'sort' => 'user_name',
                'search' => [
                    'name' => 'user_name',
                    'cond' => 'LIKE',
                ],
            ],
            'roleName' => Yii::t('base', 'Роль'),
            'balance' => [
                'value' => Yii::t('base', 'Баланс'),
                'sort' => true,
            ],
            'deleteButton' => [
                'type' => AjaxData::TABLE_BUTTON,
                'value' => Yii::t('back', 'Удалить аккаунт'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'password', 'role'], 'required', 'message' => Yii::t('error', 'Это поле не может быть пустым')],
            [['user_name'], 'unique', 'message' => Yii::t('error', 'Такой email уже зарегистрирован в системе')],
            [['user_name'], 'string', 'max' => 126, 'message' => Yii::t('error', 'Это поле не может превышать {count} символов', ['count' => 126])],
            [['password', 'password2'], 'string', 'max' => 512, 'message' => Yii::t('error', 'Это поле не может превышать {count} символов', ['count' => 512])],
            [['role'], 'string', 'max' => 16, 'message' => Yii::t('error', 'Это поле не может превышать {count} символов', ['count' => 16])],
            [['balance', 'favorites'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Yii::t('base', 'E-mail'),
            'password' => Yii::t('base', 'Пароль'),
            'password2' => Yii::t('front', 'Повторите пароль'),
            'role' => Yii::t('base', 'Роль'),
            'favorites' => Yii::t('base', 'Избранные'),
        ];
    }

    /**
     * @param int $id
     * @return UserEntity
     */
    public static function findIdentity($id)
    {
        $query = self::find()
                    ->where(['id' => $id]);

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['findOneCacheTime']);
    }

    /**
     * @param string $username
     * @return UserEntity
     */
    public static function findByUsername($username)
    {
        $query = self::find()
            ->where(['user_name' => $username]);

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['findOneCacheTime']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {

    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {

    }

    public function setUserName($name)
    {
        if($this->getIsRegistered())
            $this->user_name = $name;
    }

    public function getUserName()
    {
        $name = trim($this->user_name);
        $name = explode('@', $name);
        $name = $name[0];
        return $name;
    }

    public function beforeSave($insert)
    {
        if(!$this->role){
            $this->role = $this->getDefaultRole();
        }

        if($this->getIsNewRecord()){
            $this->password = parent::passwordHash($this->password);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {

    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return Escort|User
     */
    public function getModel()
    {
        $className = $this->getUserClass();
        return new $className();
    }

    /**
     * @return Escort|User
     */
    public function getEntity()
    {
        self::$_user = Yii::$app->data->getRepository('Account')->findEntity($this);

        return static::$_user;
    }

    /**
     * @return bool
     */
    public function getIsOnline()
    {
        return $this->getEntity()->getIsOnline();
    }

    public static function getRolesIerarchy()
    {
        return static::$_rolesIerarchy;
    }

    /**
     * @return \app\models\Escort::className() | \app\models\User::className()
     */
    public function getUserClass()
    {
        if($this->getRole() === Account::ROLE_ESCORT || $this->getRole() === Account::ROLE_VERIFIED_ESCORT)
            return Escort::className();

        if($this->getRole() === Account::ROLE_USER)
            return User::className();

        return Account::className();
    }

    public function getUserModelName()
    {
        if($this->_userModelName === null){
            $this->_userModelName = str_replace('\\', '_', $this->getUserClass());
        }

        return $this->_userModelName;
    }

    public function getDefaultRole()
    {
        return static::ROLE_GUEST;
    }

    /**
     * @return array
     */
    public static function getSexItems()
    {
        $items = [];

        foreach(static::$_sexItems as $val => $name){
            $items[$val] = Yii::t('front', $name);
        }

        return $items;
    }

    /**
     * @return array
     */
    public static function getOrientationItems()
    {
        $items = [];

        foreach(static::$_orientationItems as $val => $name){
            $items[$val] = Yii::t('front', $name);
        }

        return $items;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->getIsRegistered() ? $this->getEntity()->getFirstName() : $this->getUserName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->getIsRegistered() ? $this->getEntity()->getLastName() : $this->getUserName();
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->getIsRegistered() ? $this->getEntity()->getAva() : $this->getDefaultAvatar();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if($this->fullName === null){
            $this->fullName = $this->getFirstName().' '.$this->getLastName();
        }
        return $this->fullName;
    }

    /**
     * @return array
     */
    public static function getRolesNames()
    {
        if(self::$_rolesNamesArrey === null){
            self::$_rolesNamesArrey = [];

            foreach(self::$_rolesNames as $id => $name){
                self::$_rolesNamesArrey[$id] = Yii::t('base', $name);
            }
        }

        return self::$_rolesNamesArrey;
    }

    public function getRoleName()
    {
        return self::roleName(trim($this->role));
    }

    public function getIsEscort($role = null)
    {
        if($role === null)
            $role = $this->role;

        $role = trim($role);

        return $role === self::ROLE_ESCORT || $role === self::ROLE_VERIFIED_ESCORT;
    }

    public function getIsRegistered()
    {
        return $this->getRole() === self::ROLE_ESCORT || $this->getRole() === self::ROLE_USER || $this->getRole() === self::ROLE_VERIFIED_ESCORT;
    }

    public function getDeleteButton()
    {
        return $this->button(Yii::t('back', 'Уадалить'), [
            'class' => 'auto-ajax ask-before',
            'data' => [
                'closestdel' => 'tr',
                'ask' => Yii::t('back', 'Вы действительно хотите удалить пользователя {userName}?', ['userName' => $this->getEntity()->getFullName()]),
                'url' => Url::toRoute(['account/delete', 'id' => $this->id]),
            ],
        ]);
    }

    public static function roleName($role)
    {
        $role = trim($role);
        return isset(self::$_rolesNames[$role]) ? Yii::t('base', self::$_rolesNames[$role]) : Yii::t('base', self::ROLE_GUEST);
    }

    public function remove()
    {
        return Yii::$app->data->getRepository('Account')->remove($this);
    }
}
