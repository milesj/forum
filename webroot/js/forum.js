
var Forum = {

	/**
	 * Open or close child forums.
	 *
	 * @param {object} self
	 * @param {int} id
	 */
	toggleForums: function(self, id) {
		var node = $(self),
			target = $('#forums-'+ id);

		if (target.is(':hidden')) {
			node.html('-');
			node.parent().removeClass('closed');
			target.slideDown();
		} else {
			node.html('+');
			node.parent().addClass('closed');
			target.slideUp();
		}

		return false;
	},

	/**
	 * Toggle all checkboxes.
	 *
	 * @param {object} self
	 */
	toggleCheckboxes: function(self) {
		var node = $(self),
			form = node.parents('form');

		form.find(':checkbox').attr('checked', self.checked);
	},

	/**
	 * AJAX call to subscribe to a topic. Use the button's href attribute as the AJAX URL.
	 *
	 * @param {object} node
	 */
	subscribe: function(node) {
		node = $(node);

		if (node.hasClass('disabled')) {
			return false;
		}

		$.ajax({
			url: node.attr('href'),
			type: 'post',
			success: function(response) {
				$('.subscription').text(response.data).addClass('disabled');
			}
		});

		return false;
	},

	/**
	 * Unsubscribe from a topic.
	 *
	 * @param {object} node
	 */
	unsubscribe: function(node) {
		return Forum.subscribe(node);
	}

}
