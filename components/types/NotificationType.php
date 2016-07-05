<?php
/**
 * Created by PhpStorm.
 * User: a.baikenov
 * Date: 04.07.2016
 * Time: 15:57
 */

namespace app\components\types;


use app\models\Notice;
use app\models\Type;
use ReflectionClass;

abstract class NotificationType
{
    protected $from;
    protected $to;
    protected $title;
    protected $text;

    public function __construct($from, $to, $title, $text)
    {
        $this->from = $from;
        $this->to = $to;
        $this->title = $title;
        $this->text = $text;
    }

    abstract function doExecute();

    public function sendMail($from, $to, $title, $text)
    {
        \Yii::$app->mailer->compose()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($title)
            ->setHtmlBody($text)
            ->send();
    }

    public function saveToDb($from, $to, $title, $text)
    {
        $notification = new Notice();
        $notification->setAttributes([
            'type_id' => $this->getTypeId(),
            'from' => $from->id,
            'to' => $to->id,
            'title' => $title,
            'text' => $text,
        ]);
        if($notification->validate())
            $notification->save();
    }

    public function getTypeId()
    {
        return Type::find()->where(['class_name' => (new ReflectionClass(get_called_class()))->getShortName()])->one()->id;
    }

}