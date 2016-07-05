<?php
/**
 * Created by PhpStorm.
 * User: a.baikenov
 * Date: 30.06.2016
 * Time: 17:06
 */

namespace app\models;


class UserPreference extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_preference}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['preference_id', 'user_id', 'value'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'preference_id' => 'Preference ID',
            'user_id' => 'User ID',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreference()
    {
        return $this->hasOne(Preferences::className(), ['id' => 'preference_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id', 'user_id']);
    }
}