<?php
/** @var bool $canFilter */
/** @var string $formAction */
/** @var bool $searchOnLoad */
/** @var int $resultsPerPage */
?>
<div id="dream-list-app" class="col-lg-12" v-cloak>
	<div
		class="vue-data"
		data-action="<?=$formAction?>"
		data-search-on-load="<?=intval($searchOnLoad)?>"
		data-results-per-page="<?=intval($resultsPerPage)?>"
		data-can-filter="<?=intval($canFilter)?>"
	></div>
	<?php
	if($canFilter)
	{
		?>
		<input v-model="filter" type="text" name="filter" placeholder="Search for dreams..." class="form-control" v-on:keyup.enter="search()" v-on:blur="search()" />
		<?php
	}
	?>

	<div v-show="dreams.length > 0">
		<button v-on:click="first()" type="button" class="btn btn-primary badge">&lt;&lt;</button>
		<button v-on:click="prev()" type="button" class="btn btn-primary badge">&lt;</button>
		<span  class="badge badge-primary">{{ currentPage + 1 + ' / ' + totalPages }}</span>
		<button v-on:click="next()" type="button" class="btn btn-primary badge">&gt;</button>
		<button v-on:click="last()" type="button" class="btn btn-primary badge">&gt;&gt;</button>
	</div>
	<div id="dream-list">
		<div id="dream-loading">
			{{ loadingMessage }}
		</div>
		<li v-for="dream in dreams" class="list-group-item">
			<div class="media align-items-center">
				<ul class="avatars">
					<li>
						<div class="avatar bg-primary">
							<i class="material-icons">single_bed</i>
						</div>
					</li>
				</ul>
				<div class="media-body">
					<div>
						<a :href="'/dream/view/' + dream.id" class="A-filter-by-text">{{ dream.title }}</a>
					</div>
					<span class="text-small">{{ dream.date }}</span>
				</div>
			</div>
		</li>
	</div>
	<div v-show="dreams.length > 0">
		<button v-on:click="first()" type="button" class="btn btn-primary badge">&lt;&lt;</button>
		<button v-on:click="prev()" type="button" class="btn btn-primary badge">&lt;</button>
		<span  class="badge badge-primary">{{ currentPage + 1 + ' / ' + totalPages }}</span>
		<button v-on:click="next()" type="button" class="btn btn-primary badge">&gt;</button>
		<button v-on:click="last()" type="button" class="btn btn-primary badge">&gt;&gt;</button>
	</div>
</div>