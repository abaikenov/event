<?php

namespace app\models;

use app\components\EventBehavior;
use developeruz\db_rbac\interfaces\UserRbacInterface;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface, UserRbacInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            EventBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['username', 'email'], 'string', 'max' => 255],
            ['email', 'email'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'signup' => ['username', 'email', 'status'],
            'create' => ['username', 'email', 'status'],
            'update' => ['status'],
            'create_password' => ['password', 'confirm_password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'User ID',
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by authKey
     *
     * @param string $authKey
     * @return static|null
     */
    public static function findByAuthKey($authKey)
    {
        return static::findOne(['auth_key' => $authKey, 'status' => self::STATUS_INACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByRoles($roles)
    {
        if(!empty($roles)) {
            $ids = [];
            foreach ($roles as $role) {
                $ids = ArrayHelper::merge($ids, Yii::$app->authManager->getUserIdsByRole($role));
            }
            return self::find()->where(['id' => $ids])->all();
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function getUserName()
    {
        return $this->username;
    }

    /**
     * @return array List of all possible statuses for User instance
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public function getStatusName()
    {
        if ($this->status == self::STATUS_ACTIVE)
            return Yii::t('app', 'Active');
        else if ($this->status == self::STATUS_INACTIVE)
            return Yii::t('app', 'Inactive');
        else
            return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $userRole = Yii::$app->authManager->getRole('authorized');
            Yii::$app->authManager->assign($userRole, $this->getId());
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function doActivate()
    {
        $this->auth_key = null;
        $this->status = User::STATUS_ACTIVE;
        $this->save(false);
    }

    public function getRoles()
    {
        return Yii::$app->authManager->getRolesByUser($this->id);
    }

    public function getNewNoticeCount()
    {
        return Notice::findMyNotice()->count();
    }

    public function isUser()
    {
        return in_array('user', array_keys(Yii::$app->authManager->getRolesByUser($this->id))) ? true : false;
    }

    public function isAdmin()
    {
        return in_array('admin', array_keys(Yii::$app->authManager->getRolesByUser($this->id))) ? true : false;
    }

    public static function listAll($keyField = 'id', $valueField = 'username', $asArray = true)
    {
        $query = static::find();
        if ($asArray) {
            $query->select([$keyField, $valueField])->asArray();
        }

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }

    public function getPreference($option)
    {
        $preferenceModel = Preferences::findOne(['option' => $option]);
        if(null != $preferenceModel) {
            return $preferenceModel->getUserValue($this->getId());
        } else {
            if(isset(Yii::$app->params['defaultOptions'][$option]))
                return Yii::$app->params['defaultOptions'][$option];
            else
                return null;
        }
    }

    public function getUserPreferences()
    {
        return $this->hasMany(UserPreference::className(), ['user_id' => 'id']);
    }

    public function getPreferences()
    {
        return $this->hasOne(Preferences::className(), ['id' => 'user_id'])->via('userPreferences');
    }

    /**
     * @param $permission
     * @param bool $all - true = проверка всех прав, false = хотя бы один
     * @return bool
     */
    public function can($permission, $all = true)
    {
        if(is_array($permission)) {
            if($all) {
                $bool = true;
                foreach ($permission as $permit) {
                    $bool *= Yii::$app->user->can($permit);
                }
                return $bool;
            } else {
                foreach ($permission as $permit) {
                    if(Yii::$app->user->can($permit))
                        return true;
                }
                return false;
            }
        } else {
            return Yii::$app->user->can($permission);
        }
    }

    public static function listInsertWords()
    {
        return [
            '{username}',
            '{email}',
            '{status}',
            '{auth_key}',
        ];
    }

    public function getForTemplate($attr)
    {
        switch ($attr) {
            case 'status':
                return $this->getStatusName();
            default:
                return $this->$attr ?  $this->$attr : $attr;
        }
    }
}
