<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%notification_recipient}}".
 *
 * @property integer $notification_id
 * @property string $group
 * @property string $group_id
 */
class NotificationRecipient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification_recipient}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_id', 'group', 'group_id'], 'required'],
            [['notification_id'], 'integer'],
            [['group'], 'string'],
            [['group_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => Yii::t('app', 'Notification ID'),
            'group' => Yii::t('app', 'Group'),
            'group_id' => Yii::t('app', 'Group ID'),
        ];
    }
}
