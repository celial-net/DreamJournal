<?php
/** @var \yii\web\View $this */
/** @var \app\models\dj\Dream $dream */
/** @var \app\models\dj\DreamType[] $dreamTypes */
/** @var bool $dreamTypesDisabled */
/** @var int $resultsPerPage */
?>

<div class="container">
    <br>
	<!-- Used by JS to find the dream's id. -->
	<?= \yii\helpers\Html::hiddenInput('_Dream[id]', $dream->getId(), [
		'id' => 'Dream_id'
	]) ?>
    <div class="row col-lg-12">
        <div class="col-lg-9">
            <h4><?=$dream->getTitle()?></h4>
        </div>
        <div class="col-lg-3 text-center">
			<?=$dream->getFormattedDate()?>
        </div>
    </div>
    <hr>
    <div class="row col-lg-12">
        <p><?=nl2br($dream->getDescription())?></p>
    </div>
    <hr>
    <div class="row col-lg-12">
		<?php
        //$dreamTypesElement->render()
        ?>
    </div>
	<div class="row col-lg-12">
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
						<input type="checkbox" class="custom-control-input" id="DreamType_<?=$dreamType->getId()?>}" name="Dream[types][<?=$dreamType->getId()?>]" <?=$checked?> <?=$disabled?>>
						<label class="custom-control-label" for="DreamType_<?=$dreamType->getId()?>"><?=$dreamType->getName()?></label>
					</div>
<?php
				}
?>
			</div>
		</div>
	</div>
	<hr>
    <div class="row col-lg-12">
        <div class="form-group">
            <label>Dream Categories</label>
            <div>
				<?php
				foreach($dream->categories as $category)
				{
					?>
                    <span class="badge badge-primary badge-pill rounded-pill"><?=$category->getName()?></span>
					<?php
				}

				if(!$dream->categories)
				{
					?>
                    <span class="badge badge-primary badge-pill rounded-pill">None</span>
					<?php
				}
				?>
            </div>
        </div>
    </div>
	<hr>
	<div class="row col-lg-12">
		<div class="form-group">
			<label>Concepts</label>
			<div>
				<?php
				echo '<ul>';
				foreach($dream->getConcepts() as $concept)
				{
					echo '<li>' . $concept->name . '</li>';
				}
				echo '</ul>';
				?>
			</div>
		</div>
	</div>
	<hr>
	<div class="row col-lg-12">
		<div class="form-group">
			<div>
				<?=$this->renderFile('@app/views/dream/related.php', ['canFilter' => true, 'formAction' => '/search/related/' . $dream->getId(), 'searchOnLoad' => true, 'resultsPerPage' => $resultsPerPage])?>
			</div>
		</div>
	</div>
	<hr>
	<div class="row col-lg-12">
		<label>Comments</label>
		<?=$this->render('comment', ['editable' => false])?>
	</div>
</div>