<?php

/** @var \yii\web\View $this */
/** @var \app\models\UserUpdateForm $userUpdateForm */

?>

<div class="container">
	<br>
	<h3><?=$this->title?></h3>

	<div id="profile-form" class="form">
		<?php $form = \yii\widgets\ActiveForm::begin(); ?>
		<div class="form-row">
			<?= $form->field($userUpdateForm, 'name')->textInput() ?>
		</div>

		<div class="form-row">
			<?= $form->field($userUpdateForm, 'email')->textInput() ?>
		</div>

		<div class="form-row">
			<?= $form->field($userUpdateForm, 'new_password')->passwordInput() ?>
		</div>

		<div class="form-row">
			<?= $form->field($userUpdateForm, 'new_password_verify')->passwordInput() ?>
		</div>
		<hr>
		<div class="form-row">
			<?= $form->field($userUpdateForm, 'current_password')->passwordInput() ?>

			<div class="form-group col-12">
				<?= \yii\helpers\Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
			</div>
		</div>
		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
</div>
