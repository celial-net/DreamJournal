<div class="modal fade" id="dream-list-modal" tabindex="-1" role="dialog" aria-labelledby="dream-list-model-label" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="dream-list-model-label">Related Dreams</h5>
			</div>
			<div class="modal-body">
				<?php

				/** @var \yii\web\View $this */
				/** @var bool $canFilter */
				/** @var string $formAction */
				/** @var bool $searchOnLoad */
				/** @var int $resultsPerPage */
				
				echo $this->renderFile('@app/views/dream/dream-list.php', [
					'canFilter' => true,
					'formAction' => $formAction,
					'searchOnLoad' => $searchOnLoad,
					'resultsPerPage' => $resultsPerPage
				]);

				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- End of Modal -->

<button type="button" class="btn btn-info" data-toggle="modal" data-target="#dream-list-modal">Related Dreams</button>
