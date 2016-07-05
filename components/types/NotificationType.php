<?php
/**
 * Created by PhpStorm.
 * User: a.baikenov
 * Date: 04.07.2016
 * Time: 15:57
 */

namespace app\components;


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
}