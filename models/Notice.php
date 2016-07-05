<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property integer $from
 * @property integer $to
 * @property string $title
 * @property string $text
 * @property string $date
 */
class NotificationBrowser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_browser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to', 'title'], 'required'],
            [['from', 'to'], 'integer'],
            [['text'], 'string'],
            [['date'], 'safe'],
            [['date'], 'default', 'value' => date("Y-m-d H:i:s")],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'from' => Yii::t('app', 'From'),
            'title' => Yii::t('app', 'Title'),
            'text' => Yii::t('app', 'Text'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

    public static function findMyNotifications()
    {
        return self::find()->where(['to' => Yii::$app->user->id])->orderBy("id desc");
    }

    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'from']);
    }

    public function getRecipient()
    {
        return $this->hasOne(User::className(), ['id' => 'to']);
    }

    public function viewed()
    {
        $this->view = 1;
        $this->save();
    }
}
