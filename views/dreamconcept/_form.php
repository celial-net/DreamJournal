<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model app\models\freud\Concept */
/** @var $form yii\widgets\ActiveForm */
?>

<div class="container concept-form">
	<br>
	<h3><?=$this->title?></h3>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->getIsNewRecord() ? 'Add' : 'Edit', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
