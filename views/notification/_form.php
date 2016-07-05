<?php

use app\models\Notification;
use app\models\Type;
use app\models\User;
use app\widgets\RecipientsWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'event')->dropDownList($model->getAllEvents()) ?>
    <?= $form->field($model, 'model')->dropDownList($model->getAllModels()) ?>
    <?= $form->field($model, 'attribute')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'from')->dropDownList(User::listAll()) ?>
    <?= $form->field($model, 'recipients')->widget(RecipientsWidget::className()) ?>
    <?= $form->field($model, 'type_ids')->checkboxList(Type::listAll(), ['separator' => '<br/>']) ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'text')->widget(\yii\imperavi\Widget::className(), ['id' => 'text', 'plugins' => ['fullscreen']]) ?>

    <div class="insert-words">
        <?= Yii::t('app', 'Words for insert') . ' - ' ?>
        <span id="replace"></span>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php $js = <<<JS
    'use strict';
    $(document).ready(function() {
        setInsertWords();
        $('#notification-model').change(function() {
            setInsertWords();
        })
        function setInsertWords() {
            $('#replace').empty();
            $.ajax({
                url : '/notification/get-insert-words',
                method : 'GET',
                data : { model : $('#notification-model option:selected').val()},
                'success' : function(result) {
                    result.forEach(function(value) {
                        $('#replace').append('<span>' + value + '</span>');
                    })
                }
             });
        }
    })
JS;
    $this->registerJs($js)
    ?>
</div>
