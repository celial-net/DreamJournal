<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\dj\DreamCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container">
	<br>
	<h3><?= Html::encode($this->title) ?></h3>
	<div class="dream-category-form">
		<?php $form = ActiveForm::begin(); ?>

		<?= \yii\helpers\Html::hiddenInput('_Concept[id]', $model->getId(), [
			'id' => 'Concept_id'
		]) ?>

		<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

		<div class="form-group">
			<label class="control-label">Words</label>
			<?=$this->renderFile('@app/views/dreamcategory/word-list.php', [
				'editable' => true
			])?>
		</div>

		<div class="form-group">
			<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>
