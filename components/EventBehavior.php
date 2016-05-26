<?php

namespace app\components;

use app\models\Trigger;
use yii\db\ActiveRecord;
use yii\base\Behavior;
/**
 * Created by PhpStorm.
 * User: a.baikenov
 * Date: 25.05.2016
 * Time: 15:04
 */
class EventBehavior extends Behavior
{
    // ...

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    public function beforeInsert($event)
    {
        foreach(Trigger::findAll(['type' => $event->name, 'model' => get_class($event->sender)]) as $trigger) {
            $trigger->doActivate($event->sender);
        }
    }

    public function beforeUpdate($event)
    {
        foreach(Trigger::findAll(['type' => $event->name, 'model' => get_class($event->sender)]) as $trigger) {
            if($trigger->attribute && $event->sender->isAttributeChanged($trigger->attribute)) {
                $trigger->doActivate($event->sender);
            }
        }
    }

    public function afterInsert($event)
    {
        foreach(Trigger::findAll(['type' => $event->name, 'model' => get_class($event->sender)]) as $trigger) {
            $trigger->doActivate($event->sender);
        }
    }

    public function afterUpdate($event)
    {
        foreach(Trigger::findAll(['type' => $event->name, 'model' => get_class($event->sender)]) as $trigger) {
            if($trigger->attribute && $event->sender->isAttributeChanged($trigger->attribute)) {
                $trigger->doActivate($event->sender);
            }
        }
    }
}