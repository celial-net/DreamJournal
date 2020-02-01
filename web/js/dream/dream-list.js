$(document).ready(function(){
	let dreamListApp = new Vue({
		el: '#dream-list-app',
		data: {
			action: '',
			dreams: [],
			loadingMessage: 'No dreams found.',

			resultsPerPage: 10,
			currentPage: 0,
			lastPage: null,
			totalPages: 1,

			searchOnLoad: false,

			filter: '',
			lastFilter: null
		},
		methods: {
			loadResults: function(data) {
				this.dreams = data.results;
				this.totalPages = Number.parseInt(Math.ceil(data.total / this.resultsPerPage));

				if(this.dreams.length > 0) {
					this.loadingMessage = '';
				} else {
					this.loadingMessage = 'No dreams found.';
				}
			},

			first: function() {
				if(this.currentPage > 0) {
					this.currentPage = 0;
					this.search();
				}
			},

			prev: function() {
				if(this.currentPage > 0) {
					this.lastPage = this.currentPage;
					this.currentPage--;
					this.search();
				}
			},

			next: function() {
				if(this.currentPage + 1 < this.totalPages) {
					this.lastPage = this.currentPage;
					this.currentPage++;
					this.search();
				}
			},

			last: function() {
				if(this.currentPage < this.totalPages - 1) {
					this.currentPage = this.totalPages - 1;
					this.search();
				}
			},

			search: function() {
				//Don't repeat a search
				if(this.filter === this.lastFilter && this.currentPage === this.lastPage) {
					return;
				}

				//Filter has changed, reset paging!
				if(this.filter !== this.lastFilter) {
					this.currentPage = 0;
					this.totalPages = 1;
					this.lastPage = null;
				}

				let self = this;
				$.ajax({
					url: this.action,
					method: 'GET',
					data: {
						'DreamForm[limit]': this.resultsPerPage,
						'DreamForm[page]': this.currentPage,
						'DreamForm[search]': this.filter
					},
					success: function (data) {
						self.lastFilter = self.filter;
						self.loadResults(data);
					},
					error: function (error) {
						console.log(error);
					}
				});
			}
		},
		mounted () {
			//Load settings from HTML (populated by PHP)
			let $vueData = $('#dream-list-app > .vue-data').first();

			//Get the form action
			this.action = $vueData.data('action') || '';
			this.searchOnLoad = $vueData.data('search-on-load') || 0;
			this.resultsPerPage = $vueData.data('results-per-page') || 10;

			if(this.searchOnLoad) {
				this.search();
			}
		},
	});
});