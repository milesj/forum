
var Forum = {

	/**
	 * Open or close child forums.
	 *
	 * @param {object} self
	 * @param {int} id
	 */
	toggleForums: function(self, id) {
		var node = new Element(self),
			target = $('forums-'+ id);

		if (target.is(':hidden')) {
			node.set('html', '-');
			node.getParent().removeClass('closed');
			target.show();
		} else {
			node.set('html', '+');
			node.getParent().addClass('closed');
			target.hide();
		}

		return false;
	},

	/**
	 * Toggle all checkboxes.
	 *
	 * @param {object} self
	 */
	toggleCheckboxes: function(self) {
		var node = new Element(self),
			form = node.getParent('form');

		form.getElements('input[type="checkbox"]').set('checked', self.checked);
	},

	/**
	 * AJAX call to subscribe to a topic. Use the button's href attribute as the AJAX URL.
	 *
	 * @param {object} self
	 */
	subscribe: function(self) {
		var node = new Element(self);

		if (node.hasClass('disabled')) {
			return false;
		}

		new Request.JSON({
			method: 'POST',
			url: node.attr('href'),
			onSuccess: function(response) {
				$$('.subscription').text(response.data).addClass('disabled');
			}
		}).send();

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

};
