<?php
/** @var \yii\web\View $this */
/** @var \app\models\data\ExportForm $exportForm*/

use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>

<div class="container">
	<br>
	<h3><?=$this->title?></h3>

	<div class="export-form">
		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($exportForm, 'format')->dropDownList([
			'json' => 'JSON',
			'html' => 'HTML',
			'json-dc' => 'JSON (DreamCloud Format)'
		]) ?>

		<?= $form->field($exportForm, 'start_date')->textInput([
			'class' => 'form-control flatpickr-input',
			'data-flatpickr' => '',
			'data-default-date' => '',
			'data-alt-input' => 'true'
		]) ?>

		<?= $form->field($exportForm, 'end_date')->textInput([
			'class' => 'form-control flatpickr-input',
			'data-flatpickr' => '',
			'data-default-date' => '',
			'data-alt-input' => 'true'
		]) ?>

		<?= Html::submitButton('Export', ['class' => 'btn btn-primary']) ?>

		<?php ActiveForm::end(); ?>
	</div>
</div>