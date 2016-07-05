<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "type".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property string $class
 * @property string $class_name
 */
class Type extends \yii\db\ActiveRecord
{
    public $code;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title'], 'required'],
            [['name', 'title', 'class', 'class_name'], 'string', 'max' => 255],
            [['code'], 'string'],
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
            'code' => Yii::t('app', 'Code'),
            'title' => Yii::t('app', 'Title'),
            'class' => Yii::t('app', 'Class'),
            'class_name' => Yii::t('app', 'Class Name'),
        ];
    }

    public function beforeSave($insert)
    {
        $this->class_name = ucfirst($this->name).'NotificationType';
        $this->class = '\app\components\types\\'.$this->class_name;

        if($insert) {
            Yii::$app->fs->write($this->class_name.'.txt', $this->code);
            Yii::$app->fs->write($this->class_name.'.php',
                '<?php namespace app\components\types; 
                class '.$this->class_name.' extends NotificationType
                {
                    function doExecute()
                    {
                        
                        '.$this->code.'
                    }
            }'
            );
        } else {
            Yii::$app->fs->update($this->class_name.'.txt', $this->code);
            Yii::$app->fs->update($this->class_name.'.php',
                '<?php namespace app\components\types; 
                class '.$this->class_name.' extends NotificationType
                {
                    function doExecute()
                    {
                        
                        '.$this->code.'
                    }
            }'
            );
        }

        return parent::beforeSave($insert);

    }

    public function initCode()
    {
        $this->code = Yii::$app->fs->read($this->class_name.'.txt');
    }


    public static function listAll($keyField = 'id', $valueField = 'title', $asArray = true)
    {
        $query = static::find();
        if ($asArray) {
            $query->select([$keyField, $valueField])->asArray();
        }

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }
}
