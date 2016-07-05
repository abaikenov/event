<?php

namespace app\models;

use app\components\EventBehavior;
use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $title
 * @property string $announce
 * @property string $text
 * @property integer $visit
 * @property integer $created_at
 * @property integer $updated_at
 */
class News extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            EventBehavior::className(),
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
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'announce', 'text'], 'required'],
            [['announce', 'text'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'announce' => Yii::t('app', 'Announce'),
            'text' => Yii::t('app', 'Text'),
            'visit' => Yii::t('app', 'Visits'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getDate()
    {
        return date('d.m.Y H:i', $this->created_at);
    }

    public function incVisit()
    {
        $this->visit += 1;
        $this->save(false);
    }

    public static function listInsertWords()
    {
        return [
            '{title}',
            '{announce}',
            '{text}',
            '{create_at}',
        ];
    }

    public function getForTemplate($attr)
    {
        switch ($attr) {
            case 'create_at':
                return $this->getDate();
            default:
                return $this->$attr ?  $this->$attr : $attr;
        }
    }
}
