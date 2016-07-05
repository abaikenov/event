<?php
use yii\bootstrap\Html;

$attrName = (new ReflectionClass($model))->getShortName() . '[' . $attribute . ']';
?>


<?= Html::hiddenInput($attrName, '') ?>
<div class="row">

    <?php foreach ($items as $item) : ?>
        <div class="col-sm-4" id="<?= $item['name'] ?>">
            <p><?= $item['title'] ?> ( <a href="#" class="check_all"
                                          target="<?= $item['name'] ?>"><?= Yii::t('app', 'check all') ?></a> / <a
                    href="#" class="check_out" target="<?= $item['name'] ?>"><?= Yii::t('app', 'check out') ?></a> )</p>
            <?= Html::checkboxList($attrName . '[' . $item['name'] . '][]', $item['selection'], $item['items'], ['separator' => '<br/>']) ?>
        </div>
    <?php endforeach; ?>
</div>


<?php
$js = <<<JS
    $(document).ready(function() {
        $('a.check_all').click(function(e) {
            e.preventDefault();
            $('div#' + $(this).attr('target') + ' input[type="checkbox"]').prop('checked', true);
        })    
        $('a.check_out').click(function(e) {
            e.preventDefault();
            $('div#' + $(this).attr('target') + ' input[type="checkbox"]').prop('checked', false);
        })    
    })
JS;

$this->registerJs($js);

?>
