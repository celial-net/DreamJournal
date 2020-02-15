$(document).ready(function(){
	let conceptWordListApp = new Vue({
		el: '#concept-word-list-app',
		data: {
			words: [],
			certainty: 1,
			deletedWords: []
		},
		mounted: function () {
			let self = this;
			$.ajax({
				url: '/dreamconcept/' + $('#Concept_id').val() + '/words',
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
				$("#concept_words").select2('val', '');
				$("#concept_words").empty('');
			},

			setModalOptions: function () {

			},

			getWord: function() {
				return $("#concept_words").select2("data")[0];
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
				this.deletedWords.push({
					id: wordId
				});
				$('#ConceptWord_' + wordId).parent().parent().remove();
			}
		}
	});
});