<?php

use app\models\Section;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Promotion */
/* @var $form yii\widgets\ActiveForm */

$form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'POST',
]);

//echo $form->field($model, 'section_id')->dropDownList(Section::getSectionsForSelect())->label('Section name');
?>
    <div class="form-group">
        <p>
            <?= Html::label('Section name', 'select-section'); ?>
            <?= Html::dropDownList('section', null, Section::getSectionsForSelect(), ['id' => 'select-section', 'class' => 'form-control']); ?>
        </p>
        <p>
            <?= Html::submitButton('Parse', ['class' => 'btn btn-primary', 'name' => 'parse', 'value' => 1]); ?>
        </p>
    </div>
<?php
ActiveForm::end();
