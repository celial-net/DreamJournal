$(document).ready(function(){
	let dreamCommentApp = new Vue({
		el: '#dream-comment-app',
		data: {
			newCounter: 0,
			text: '',
			title: '',
			buttonTitle: '',
			commentId: '',
			mode: '', // new|edit
			comments: [],
			deletedComments: []
		},
		mounted: function () {
			let self = this;
			$.ajax({
				url: '/dreamcomment?dreamId=' + $('#Dream_id').val(),
				method: 'GET',
				success: function (data) {
					self.comments = data;
				},
				error: function (error) {
					console.log(error);
				}
			});
		},
		methods: {
			newComment: function () {
				this.resetModalOptions();
				this.mode = 'new';
				this.setModalOptions();
				$('#commentModal').modal('show');
			},

			editComment: function(commentId) {
				this.resetModalOptions();
				this.mode = 'edit';
				this.text = $('#DreamComment_' + commentId).val();
				this.commentId = commentId;
				this.setModalOptions();
				$('#commentModal').modal('show');
			},

			resetModalOptions: function () {
				this.mode = '';
				this.text = '';
				this.title = '';
				this.buttonTitle = '';
				this.commentId = '';
			},

			setModalOptions: function () {
				if(this.mode === 'new') {
					this.title = 'New Comment';
					this.buttonTitle = 'Add';
				}
				else {
					this.title = 'Edit Comment';
					this.buttonTitle = 'Edit';
				}
			},

			saveComment: function () {
				if(this.mode === 'new') {
					let now = new Date();
					this.comments.push({
						'id': 'new-' + this.newCounter++,
						'author': 'Sheldon Juncker',
						'date': now.getFullYear()+'/'+(now.getMonth()+1)+'/'+now.getDate(),
						'text':  this.text
					});
				}
				else {
					for(let i=0;i<this.comments.length;i++) {
						if(this.comments[i].id == this.commentId) {
							this.comments[i].text = this.text;
							break;
						}
					}
				}
				this.resetModalOptions();
			},

			deleteComment: function(commentId) {
				this.deletedComments.push({
					id: commentId
				});
				$('#DreamComment_' + commentId).parent().parent().remove();
			}
		}
	});
});