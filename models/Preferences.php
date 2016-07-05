<?php
/**
 * Created by PhpStorm.
 * User: a.baikenov
 * Date: 30.06.2016
 * Time: 17:05
 */

namespace app\models;


use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Preferences extends \yii\db\ActiveRecord
{
    const TYPE_INTEGER = 1;
    const TYPE_CHECKBOX = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%preferences}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['default_value', 'created_at', 'updated_at'], 'integer'],
            [['name', 'default_value', 'type'], 'required'],
            [['name', 'option'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('preferences', 'ID'),
            'name' => Yii::t('preferences', 'Name'),
            'option' => Yii::t('preferences', 'Option'),
            'default_value' => Yii::t('preferences', 'Default value'),
            'type' => Yii::t('preferences', 'Type'),
            'usage' => Yii::t('preferences', 'Usage'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    //настройка от пользователя
    public function getUserValue($user_id = null)
    {
        if($user_id === null)
            $user_id = Yii::$app->user->identity->getId();
        return $this->getUserPreference($user_id) === null ? $this->default_value : $this->getUserPreference($user_id)->value;
    }

    public function getPreferenceUsers()
    {
        return $this->hasMany(UserPreference::className(), ['preference_id' => 'id']);
    }

    public function getUserPreference($user_id)
    {
        return UserPreference::findOne(['preference_id' => $this->id, 'user_id' => $user_id]);
    }

    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->via('preferenceUsers');
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->viaTable('{{%user_preference}}', ['preference_id' => 'id']);
    }

    public static function getUserPreferencesList()
    {
        return self::find()->all();
    }
}