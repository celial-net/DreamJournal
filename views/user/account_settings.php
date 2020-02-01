<?php

/** @var \yii\web\View $this */
/** @var \app\models\dj\AccountSettings $accountSettings */

?>

<div class="container">
	<br>
	<h3><?=$this->title?></h3>

	<div id="account-settings-form" class="form">
		<p>
			You can select how many pages to see on standard paged interfaces and which date to use as the default when creating dreams. Some users prefer to record their dreams for the current day and others the previous night.
		</p>
		<?php $form = \yii\widgets\ActiveForm::begin(); ?>
		<div class="form-row">
			<?= $form->field($accountSettings, 'results_per_page')->textInput([
				'pattern' => '[0-9]+'
			]) ?>

			<?= $form->field($accountSettings, 'default_dream_date')->dropDownList($accountSettings->getDreamDateOptions()) ?>

			<?= $form->field($accountSettings, 'default_dream_period')->dropDownList($accountSettings->getDreamPeriodOptions()) ?>

			<div class="form-group col-12">
				<?= \yii\helpers\Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
			</div>
		</div>

		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
</div>
