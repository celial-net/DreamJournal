<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

/** @var $this yii\web\View */
/** @var $model app\models\freud\Concept */
/** @var $form yii\widgets\ActiveForm */
?>

<div class="container concept-form">
	<br>
	<h3><?=$this->title?></h3>
    <?php $form = ActiveForm::begin(); ?>

	<?= \yii\helpers\Html::hiddenInput('_Concept[id]', $model->getId(), [
		'id' => 'Concept_id'
	]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<label class="control-label">Words</label>
		<?=$this->renderFile('@app/views/dreamconcept/word-list.php', [
			'editable' => true
		])?>
	</div>

    <div class="form-group">
        <?= Html::submitButton($model->getIsNewRecord() ? 'Add' : 'Edit', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
