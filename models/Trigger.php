<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "trigger".
 *
 * @property integer $id
 * @property string $title
 * @property integer $type
 * @property string $model
 * @property string $attribute
 */
class Trigger extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trigger';
    }


    public function behaviors()
    {
        return [
            [
                'class' => \voskobovich\behaviors\ManyToManyBehavior::className(),
                'relations' => [
                    'event_ids' => [
                        'events',
                        'fields' => [
                            'link' => [
                                'get' => function($values) {
                                    foreach($values as $key => $value){
                                        $values[$key] = Html::a(Event::findOne(['id' => $value])->name, Url::to(['event/view', 'id' => Event::findOne(['id' => $value])->id]));
                                    }
                                    return implode('<br/> ', $values);
                                },
                            ],
                        ]
                    ],
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
            [['title', 'type', 'model'], 'required'],
            [['title', 'type', 'model', 'attribute'], 'string', 'max' => 255],
            [['event_ids'], 'each', 'rule' => ['integer']]
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
            'type' => Yii::t('app', 'Type'),
            'model' => Yii::t('app', 'Model'),
            'attribute' => Yii::t('app', 'Attribute'),
        ];
    }

    public static function types()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['id' => 'event_id'])
            ->viaTable('{{%lnk_event_to_trigger}}', ['trigger_id' => 'id']);
    }

    public function doActivate($model)
    {
        foreach($this->getEvents()->all() as $event) {
            $event->doExecute($model);
        }
    }

}
