<?php
/** @var bool $editable */
use kartik\select2\Select2;
use yii\web\JsExpression;
?>
<div id="category-word-list-app" class="col-lg-12">
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
								<label for="category-word">Word</label>
								<?php
								echo Select2::widget([
									'id' => 'category_words',
									'name' => '_Category[words]',
									'maintainOrder' => true,
									'data' => '',
									'value' => '',
									'pluginOptions' => [
										'ajax' => [
											'url' => '/dreamcategory/words',
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
								<label for="Category_certainty">Weight</label>
								<input v-model="certainty" type="number" min="0" max="1" step="0.01" id="Category_certainty" class="form-control" />
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

	<div class="category category-words">
		<table class="table table-striped table-bordered detail-view">
			<tbody>
				<tr>
					<th>Word</th>
					<th>Weight</th>
					<?=$editable ? '<th>Action</th>' : ''?>
				</tr>
				<tr v-for="word in words">
					<td>{{ word.word }}</td>
					<td>{{ word.certainty }}</td>
					<?php
					if($editable)
					{
						?>
						<td>
							<div class="col-lg-12">
								<input type="hidden" :name="'Category[word][' + word.id + '][word]'" :value="word.word" :id="'CategoryWord_' + word.id" />
								<input type="hidden" :name="'Category[word][' + word.id + '][certainty]'" :value="word.certainty" :id="'CategoryWord_' + word.id + '_certainty'" />
								<button type="button" class="btn btn-sm btn-danger" v-on:click="deleteWord(word.id)">Delete</button>
							</div>
						</td>
						<?php
					}
					?>
				</tr>
			</tbody>
		</table>
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