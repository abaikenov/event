<?php
/**
 * Created by PhpStorm.
 * User: a.baikenov
 * Date: 30.06.2016
 * Time: 17:45
 */

namespace app\widgets;


use app\models\Preferences;
use yii\bootstrap\Widget;

class UserPreferencesWidget extends Widget
{
    public $preferences;
    
    public function init()
    {
        parent::init();
        $this->preferences = Preferences::getUserPreferencesList();
    }

    public function run()
    {
        return $this->render('preference', ['preferences' => $this->preferences]);
    }
}