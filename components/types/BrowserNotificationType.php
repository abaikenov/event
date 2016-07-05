<?php
/**
 * Created by PhpStorm.
 * User: a.baikenov
 * Date: 04.07.2016
 * Time: 16:01
 */

namespace app\components;


use app\models\Notice;

class BrowserNotificationType extends NotificationType
{
    function doExecute()
    {
        $notification = new Notice();
        $notification->setAttributes([
            'type_id' => 2,
            'from' => $this->from->id,
            'to' => $this->to->id,
            'title' => $this->title,
            'text' => $this->text,
        ]);
        if($notification->validate())
            $notification->save();
    }
}