<?php
/**
 * Created by PhpStorm.
 * User: a.baikenov
 * Date: 04.07.2016
 * Time: 16:01
 */

namespace app\components;


class EmailNotificationType extends NotificationType
{
    function doExecute()
    {
        \Yii::$app->mailer->compose()
            ->setFrom($this->from->email)
            ->setTo($this->to->email)
            ->setSubject($this->title)
            ->setHtmlBody($this->text)
            ->send();
    }
}