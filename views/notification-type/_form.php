<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Type */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'disabled' => !$model->isNewRecord]) ?>

    <?= $form->field($model, 'code', ['template' => '{label}
    <pre>
<span class="blue">class</span> <span class="green">'.($model->class_name ? $model->class_name : '...NotificationType').'</span> <span class="blue">extends</span> <span class="green">NotificationType</span>
{
    <span class="blue">function</span> <span class="green">doExecute()</span>
    {
        /* 
        * доступные переменные:
        * <span class="green">$this->from;</span> // User объект
        * <span class="green">$this->to;</span> // User объект
        * <span class="green">$this->title;</span> // string
        * <span class="green">$this->text;</span> // string
        *
        * доступные функции:
        * <span class="blue">$this->sendMail</span>(<span class="green">$this->from->email, $this->to->email, $this->title, $this->text</span>) // отправка email
        * <span class="blue">$this->saveToDb</span>(<span class="green">$this->from, $this->to, $this->title, $this->text</span>) // сохранение в базу
        */
        {input}
    }
}
    </pre>
    '])->textarea(['rows' => 8]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
