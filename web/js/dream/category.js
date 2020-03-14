$(document).ready(function(){
	let categoryWordListApp = new Vue({
		el: '#category-word-list-app',
		data: {
			words: [],
			certainty: 1
		},
		mounted: function () {
			let self = this;
			$.ajax({
				url: '/dreamcategory/' + $('#Category_id').val() + '/words',
				method: 'GET',
				success: function (data) {
					self.words = data;
				},
				error: function (error) {
					console.log(error);
				}
			});
		},
		methods: {
			newWord: function () {
				this.resetModalOptions();
				this.setModalOptions();
				$('#wordModal').modal('show');
			},

			resetModalOptions: function () {
				this.certainty = 1;
				$("#category_words").select2('val', '');
				$("#category_words").empty('');
			},

			setModalOptions: function () {

			},

			getWord: function() {
				return $("#category_words").select2("data")[0];
			},

			saveWord: function () {
				let word = this.getWord();

				if(!word || !word.id || word.id == 'Select word...') {
					return;
				}

				this.words.push({
					'id': word.id,
					'word': word.text,
					'certainty': this.certainty
				});
				this.resetModalOptions();
			},

			deleteWord: function(wordId) {
				$('#CategoryWord_' + wordId).closest('tr').remove();
			}
		}
	});
});