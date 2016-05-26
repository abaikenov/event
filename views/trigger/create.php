<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Trigger */

$this->title = Yii::t('app', 'Create Trigger');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Triggers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trigger-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
