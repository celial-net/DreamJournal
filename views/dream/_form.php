<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dream app\models\dj\Dream */
/* @var $form yii\widgets\ActiveForm */
/** @var \app\models\dj\DreamType[] $dreamTypes */
/** @var bool $dreamTypesDisabled */
/** @var string $defaultDreamDate */
?>

<div class="container">
	<br>
	<h3><?= Html::encode($this->title) ?></h3>
	<div class="dream-form">
		<?php $form = ActiveForm::begin(); ?>

		<!-- Used by JS to find the dream's id. -->
		<?= Html::hiddenInput('_Dream[id]', $dream->getId(), [
			'id' => 'Dream_id'
		]) ?>

		<?= $form->field($dream, 'title')->textInput(['maxlength' => true]) ?>

		<?php
		$dreamDate = NULL;
		if(isset($defaultDreamDate))
		{
			switch($defaultDreamDate)
			{
				case 'current_day':
					$dreamDate = date('Y-m-d', time());
					break;
				case 'previous_day':
					$dreamDate = date('Y-m-d', strtotime('-1 day'));
					break;
			}
		}
		echo $form->field($dream, 'dreamt_at')->textInput([
			'class' => 'form-control flatpickr-input',
			'data-flatpickr' => '',
			'data-default-date' => '',
			'data-alt-input' => 'true',
			'value' => $dreamDate
		]);
		?>

		<?= $form->field($dream, 'description')->textarea(['rows' => 12]) ?>

		<div class="form-group">
			<label>Dream Type</label>
			<div>
				<?php
				foreach($dreamTypes as $dreamType)
				{
					$checked = $dream->hasType($dreamType) ? 'checked' : '';
					$disabled = $dreamTypesDisabled ? 'disabled' : '';
					?>
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" id="DreamType_<?=$dreamType->getId()?>" name="Dream[types][<?=$dreamType->getId()?>]" <?=$checked?> <?=$disabled?>>
						<label class="custom-control-label" for="DreamType_<?=$dreamType->getId()?>"><?=$dreamType->getName()?></label>
					</div>
					<?php
				}
				?>
			</div>
		</div>

		<div class="form-group">
			<label for="Dream_categories">Dream Categories</label>
			<?= Html::textInput('Dream[categories]', $categoryIdString, [
				'id' => 'Dream_categories',
				'class' => 'form-control'
			])?>
		</div>

		<div class="row col-lg-12">
			<label>Comments</label>
			<?=$this->render('comment', ['editable' => true])?>
		</div>

		<div class="form-group">
			<?= Html::submitButton($dream->getIsNewRecord() ? 'Add' : 'Update', ['class' => 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>
