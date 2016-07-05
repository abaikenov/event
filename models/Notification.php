<?php

namespace app\models;

use app\components\types\NotificationType;
use ReflectionClass;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "event".
 *
 * @property integer $id
 * @property string $name
 * @property string $event
 * @property string $model
 * @property string $attribute
 * @property integer $from
 * @property integer $to
 * @property string $title
 * @property string $text
 */
class Notification extends \yii\db\ActiveRecord
{

    public $recipients;
    public $eventSender;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification}}';
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
            [['name', 'event', 'model', 'from', 'recipients', 'title', 'text', 'type_ids'], 'required'],
            [['from', 'created_at', 'updated_at'], 'integer'],
            [['text'], 'string'],
            [['name', 'event', 'model', 'title', 'attribute'], 'string', 'max' => 255],
            [['type_ids'], 'each', 'rule' => ['integer']],
            [['recipients'], 'safe'],
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
            'event' => Yii::t('app', 'Event'),
            'model' => Yii::t('app', 'Model'),
            'attribute' => Yii::t('app', 'Attribute'),
            'from' => Yii::t('app', 'From'),
            'recipients' => Yii::t('app', 'Recipients'),
            'title' => Yii::t('app', 'Title'),
            'text' => Yii::t('app', 'Text'),
            'type_ids' => Yii::t('app', 'Types'),
            'type_ids_name' => Yii::t('app', 'Types'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // сохраняем связи для получателей
        $this->unlinkAll('recipients', true);

        foreach ($this->recipients as $group => $recipients) {
            foreach ($recipients as $recipient) {
                $model = new NotificationRecipient();
                $model->notification_id = $this->id;
                $model->group = $group;
                $model->group_id = $recipient;
                $this->link('recipients', $model);
            }
        }
    }

    public static function listAll($keyField = 'id', $valueField = 'name', $asArray = true)
    {
        $query = static::find();
        if ($asArray) {
            $query->select([$keyField, $valueField])->asArray();
        }

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }

    public function getAllEvents()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => Yii::t('app', 'beforeInsert'),
            ActiveRecord::EVENT_BEFORE_UPDATE => Yii::t('app', 'beforeUpdate'),
            ActiveRecord::EVENT_AFTER_INSERT => Yii::t('app', 'afterInsert'),
            ActiveRecord::EVENT_AFTER_UPDATE => Yii::t('app', 'afterUpdate'),
        ];
    }

    public function getAllModels()
    {
        return [
            'app\models\News' => Yii::t('app', 'News'),
            'app\models\User' => Yii::t('app', 'User'),
        ];
    }

    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'from']);
    }

    public function getSenderDisplayName()
    {
        return $this->sender->username;
    }

    public function getRecipients()
    {
        return $this->hasMany(NotificationRecipient::className(), ['notification_id' => 'id']);
    }

    public function getRecipientsForDisplay()
    {
        $str = '';
        $recipients = [
            'role' => [],
            'user' => [],
            'other' => [],
        ];
        foreach ($this->getRecipients()->all() as $item) {
            if ($item->group == 'user') {
                $recipients[$item->group][] = User::findOne($item->group_id)->username;
            } else
                $recipients[$item->group][] = $item->group_id;
        }
        foreach ($recipients as $key => $value) {
            if (!empty($value)) {
                $str .= '<strong>' . Yii::t('app', $key) . '</strong>' . ' : ' . implode(', ', $value) . '<br/>';
            }
        }
        return $str;
    }

    public function getTypes()
    {
        return $this->hasMany(Type::className(), ['id' => 'type_id'])
            ->viaTable('{{%lnk_notification_to_type}}', ['notification_id' => 'id']);
    }

    public static function getInsertWords($model)
    {
        $defaultWords = [
            '{sitename}',
            '{recipient}',
            '{hyperlink()}',
            '{base_url}',
        ];

        if (class_exists($model))
            return ArrayHelper::merge($defaultWords, $model::listInsertWords());
        else
            return $defaultWords;
    }


    /*
     * Здесь формируется массив принимателей уведомлений из user_preference и notification_recipients
     * return ['email' => [Users], 'browser' => [Users], ...]  
     */
    public function getRecipientsByNotificationType()
    {
        $ids = [];
        //находим всех получателей по указанным ролям
        foreach (ArrayHelper::getColumn($this->getRecipients()->where(['group' => 'role'])->asArray()->all(), 'group_id') as $role) {
            $ids = ArrayHelper::merge($ids, Yii::$app->authManager->getUserIdsByRole($role));
        }

        // далее к ним добавляем указанных получателей
        $ids = ArrayHelper::merge($ids, ArrayHelper::getColumn($this->getRecipients()->where(['group' => 'user'])->asArray()->all(), 'group_id'));

        // если стоит галочка самому себе, то добавляем текущего пользователя в получателей
        if ($this->getRecipients()->where(['group' => 'other', 'group_id' => 'yourself'])->one()) {
            if (null != $this->eventSender && (new ReflectionClass($this->eventSender))->getShortName() === 'User')
                $ids = ArrayHelper::merge($ids, [$this->eventSender->id]);
        }

        $users = User::find()->with('userPreferences')->where(['id' => $ids])->all();
        $usersByTypes = [];

        foreach ($this->types as $type) {
            foreach ($users as $user) {
                if (null === $user->getPreference($type->name)) {
                    $usersByTypes[$type->name][] = $user;
                } else if ($user->getPreference($type->name)) {
                    $usersByTypes[$type->name][] = $user;
                }
            }
        }

        return $usersByTypes;
    }

    /*
     * Отправка уведомлений
     */
    public function doExecute($model)
    {
        $this->eventSender = $model;
        $recipients = $this->getRecipientsByNotificationType();

        foreach ($this->types as $type) {
            if ($recipients[$type->name])
                foreach ($recipients[$type->name] as $recipient) {
                    try {
                        if ((new ReflectionClass($type->class))->isSubclassOf(NotificationType::class)) {
                            $class = new $type->class($this->sender, $recipient, $this->getForTemplate($recipient, $this->title), $this->getForTemplate($recipient, $this->text));
                            $class->doExecute();
                        }
                    } catch (ErrorException $e) {
                        $e->getTrace();
                    }
                }
        }
    }

    // заменяем вставочные слова реальными значениями
    public function getForTemplate($recipient, $text)
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
                        if (null != $this->eventSender)
                            $text = str_replace("{hyperlink()}", Html::a(Yii::t('app', 'read more...'), Url::to(['/site/view', 'id' => $this->eventSender->id], true)), $text);
                        break;
                    case 'base_url':
                        $text = str_replace("{base_url}", Url::base(true), $text);
                        break;
                    default:
                        if (null != $this->eventSender)
                            $text = str_replace("{" . $sPlaceholderAttCode . "}", $this->eventSender->getForTemplate($sPlaceholderAttCode), $text);
                        break;
                }
            }
        }
        return $text;
    }

}
