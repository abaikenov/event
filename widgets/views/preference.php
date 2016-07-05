<?php

use app\models\Preferences;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\User */

?>
<div class="user-preferences">
    <?= Html::beginForm(Url::toRoute('/user/preference'), 'post', ['id' => 'form-preference']) ?>
    <?php foreach ($preferences as $item): ?>
        <div class="form-group">
            <?php switch ($item->type) {
                case Preferences::TYPE_INTEGER :
                    echo Html::label($item->name, $item->option, ['class' => 'form-group']);
                    echo Html::input('text', $item->option, $item->userValue, ['id' => $item->option, 'class' => 'form-control']);
                    break;
                case Preferences::TYPE_CHECKBOX :
                    echo '<div class="checkbox">' . Html::label('<input type = "checkbox" id="'.$item->option.'" name = "'.$item->option.'"' . ($item->userValue ? 'checked>' : '>') . Html::input('hidden', $item->option, $item->userValue) .  $item->name, $item->option, ['class' => 'form-group']) . '</div>';
                    break;
                default:
                    continue;
                    break;
            } ?>
        </div>
    <?php endforeach; ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
        <?= Html::tag('span', '', ['id' => 'alert']) ?>
    </div>
    <?= Html::endForm() ?>
</div>

<?php $js =
    <<<JS
        $(document).ready(function() {
            $('form#form-preference input[type = "checkbox"]').change(function() {
                $(this).next().val($(this).is(':checked') ? 1 : 0);
            })
        
        
            $('form#form-preference').submit(function(e) {
                e.preventDefault();
                $('span#alert').removeClass('alert alert-info alert-danger').text('');
                $.ajax({
                    type: "POST",
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function (result) {
                        $('span#alert').addClass('alert alert-info').text(result.msg);
                        location.reload();
                    },
                    error: function (request, status, error) {
                        $('span#alert').addClass('alert alert-danger').text('Не удалось сохранить');
                    }
                });
            });
    });
JS;
?>
<?php Yii::$app->view->registerJs($js) ?>
