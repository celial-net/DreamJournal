<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\dj\DreamCategory */
?>
<div class="container">
	<br>
	<h3><?= Html::encode($this->title) ?></h3>
	<div class="dream-category-view">
		<?= DetailView::widget([
			'model' => $model,
			'attributes' => [
				'id',
				'name',
			],
		]) ?>
	</div>

	<?= \yii\helpers\Html::hiddenInput('_Category[id]', $model->getId(), [
		'id' => 'Category_id'
	]) ?>

	<?php
	echo '<label class="control-label">Words</label>';
	echo $this->renderFile('@app/views/dreamcategory/word-list.php', [
		'editable' => false
	]);
	?>
</div>