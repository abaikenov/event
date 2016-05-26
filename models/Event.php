<?php

namespace app\models;

use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "event".
 *
 * @property integer $id
 * @property string $name
 * @property integer $from
 * @property integer $to
 * @property string $title
 * @property string $text
 */
class Event extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \voskobovich\behaviors\ManyToManyBehavior::className(),
                'relations' => [
                    'type_ids' => [
                        'types',
                        'fields' => [
                            'name' => [
                                'get' => function ($values) {
                                    foreach ($values as $key => $value) {
                                        $values[$key] = Type::findOne(['id' => $value])->name;
                                    }
                                    return implode(', ', $values);
                                },
                            ],
                            'code' => [
                                'get' => function ($values) {
                                    foreach ($values as $key => $value) {
                                        $values[$key] = Type::findOne(['id' => $value])->name;
                                    }
                                    return $values;
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
            [['name', 'from', 'to', 'title', 'text', 'type_ids'], 'required'],
            [['from', 'to'], 'integer'],
            [['text'], 'string'],
            ['name', 'string', 'max' => 50],
            [['title'], 'string', 'max' => 255],
            [['type_ids'], 'each', 'rule' => ['integer']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'from' => Yii::t('app', 'From'),
            'to' => Yii::t('app', 'To'),
            'title' => Yii::t('app', 'Title'),
            'text' => Yii::t('app', 'Text'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    public static function listAll($keyField = 'id', $valueField = 'name', $asArray = true)
    {
        $query = static::find();
        if ($asArray) {
            $query->select([$keyField, $valueField])->asArray();
        }

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }

    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'from']);
    }

    public function getRecipient()
    {
        if ($this->to)
            return $this->hasOne(User::className(), ['id' => 'to']);
        else
            return User::find();
    }

    public function getTypes()
    {
        return $this->hasMany(Type::className(), ['id' => 'type_id'])
            ->viaTable('{{%lnk_event_to_type}}', ['event_id' => 'id']);
    }

    public function doExecute($model)
    {
        foreach ($this->getRecipient()->all() as $recipient) {
            //Если это уведомление по email
            if (in_array('email', $this->type_ids_code)) {
                $from = $this->getSender()->one()->email;
                if ($from) {
                    Yii::$app->mailer->compose()
                        ->setFrom($from)
                        ->setTo($recipient->email)
                        ->setSubject($this->getForTemplate($model, $recipient, $this->title))
                        ->setTextBody($this->getForTemplate($model, $recipient, $this->text))
                        ->send();
                }

            }

            //Если это уведомление по browser
            if (in_array('browser', $this->type_ids_code)) {
                $notification = new Notification();
                $notification->setAttributes([
                    'from' => $this->from,
                    'to' => $recipient->id,
                    'title' => $this->getForTemplate($model, $recipient, $this->title),
                    'text' => $this->getForTemplate($model, $recipient, $this->text),
                ]);
                $notification->save();
            }
        }
    }

    public function getForTemplate($model, $recipient, $text)
    {
        if (preg_match_all('/\{(.+?)\}/e', $text, $aMatches)) {
            foreach ($aMatches[1] as $sPlaceholderAttCode) {
                switch ($sPlaceholderAttCode) {
                    case 'sitename':
                        $text = str_replace("{sitename}", "Event System", $text);
                        break;
                    case 'recipient':
                        $text = str_replace("{recipient}", $recipient->username, $text);
                        break;
                    case 'hyperlink()':
                        $text = str_replace("{hyperlink()}", Html::a("Читать далее...", Url::to(['article/view', 'id' => $model->id])), $text);
                        break;
                    default:
                        if(isset($model->getAttributes()[$sPlaceholderAttCode]))
                            $text = str_replace("{".$sPlaceholderAttCode."}", $model->getAttributes()[$sPlaceholderAttCode], $text);
                        break;
                }
            }
        }

        return $text;
    }

}
