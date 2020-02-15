<?php
/** @var bool $editable */
use kartik\select2\Select2;
use yii\web\JsExpression;
?>
<div id="concept-word-list-app" class="col-lg-12">
	<!-- Word Modal -->
	<?php
	if($editable)
	{
		?>
		<div class="modal fade" id="wordModal" tabindex="-1" role="dialog" aria-labelledby="wordModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="wordModalLabel">Add Word</h5>
					</div>
					<div class="modal-body">
						<div class="form">
							<div class="form-group">
								<label for="concept-word">Word</label>
								<?php
								echo Select2::widget([
									'id' => 'concept_words',
									'name' => 'Concept[words]',
									'maintainOrder' => true,
									'data' => '',
									'value' => '',
									'pluginOptions' => [
										'ajax' => [
											'url' => '/dreamconcept/words',
											'dataType' => 'json',
											'data' => new JsExpression('function(params) { return {search:params.term}; }')
										],
										'allowClear' => true,
										'minimumInputLength' => 2,
										'language' => [
											'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
										],
										'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
										'templateResult' => new JsExpression('function(word) { return word.text; }'),
										'templateSelection' => new JsExpression('function (word) {  return word.text; }'),
									],
									'options' => [
										'placeholder' => 'Select word...',
										'multiple' => false
									],
								]);
								?>
							</div>
							<div class="form-group">
								<label for="Concept_certainty">Weight</label>
								<input v-model="certainty" type="number" min="0" max="1" step="0.01" id="Concept_certainty" class="form-control" />
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" v-on:click="saveWord" data-dismiss="modal">Add</button>
					</div>
				</div>
			</div>
		</div>
		<!-- End of Modal -->
		<?php
	}
	?>

	<div class="concept concept-words">
		<div class="deleted">
			<input v-for="deleted in deletedWords" type="hidden" :name="'Concept[word][deleted][' + deleted.id + ']'" value="1" />
		</div>

		<div v-for="word in words" class="dream comment row">
			<div class="col-lg-8">
				<b>{{ word.word }}</b>
			</div>
			<div class="col-lg-4">
				<i>{{ word.certainty }}</i>
			</div>
			<?php
			if($editable)
			{
				?>
				<div class="col-lg-12">
					<input type="hidden" :name="'Concept[word][' + word.id + ']'" :value="word.word" :id="'ConceptWord_' + word.id" />
					<input type="hidden" :name="'Concept[word][' + word.id + ']'" :value="word.certainty" :id="'ConceptWord_' + word.id + '_certainty'" />
					<button type="button" class="btn btn-sm btn-danger" v-on:click="deleteWord(word.id)">Delete</button>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<!-- Button trigger modal -->
	<?php
	if($editable)
	{
		?>
		<button type="button" class="btn btn-success" v-on:click="newWord">Add word</button>
		<?php
	}
	?>
</div>