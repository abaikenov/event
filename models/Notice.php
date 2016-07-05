<?php

namespace app\models;

use Faker\Provider\DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FormatConverter;

/**
 * This is the model class for table "notice".
 *
 * @property integer $id
 * @property integer $type_id
 * @property integer $from
 * @property integer $to
 * @property string $title
 * @property string $text
 * @property integer $created_at
 * @property integer $updated_at
 */
class Notice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notice';
    }

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
            [['type_id', 'from', 'to', 'title'], 'required'],
            [['type_id', 'from', 'to'], 'integer'],
            [['text'], 'string'],
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
            'type_id' => Yii::t('app', 'Type ID'),
            'from' => Yii::t('app', 'From'),
            'title' => Yii::t('app', 'Title'),
            'text' => Yii::t('app', 'Text'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public static function findMyNotice()
    {
        return self::find()->where(['to' => Yii::$app->user->id])->orderBy('created_at desc');
    }

    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'from']);
    }

    public function getRecipient()
    {
        return $this->hasOne(User::className(), ['id' => 'to']);
    }

    public function getDate()
    {
        return date('d.m.Y H:i', $this->created_at);
    }
    
    public function viewed()
    {
        $this->view = 1;
        $this->save();
    }
}
