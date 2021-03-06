<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Promotion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="promotion-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'section_id')->dropDownList($this->context->getItemsForSectionField())->label('Section name') ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
