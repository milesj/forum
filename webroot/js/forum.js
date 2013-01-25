/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

var Forum = {

	/**
	 * Open or close child forums.
	 *
	 * @param {Element} self
	 * @param {int} id
	 */
	toggleForums: function(self, id) {
		var node = new Element(self),
			target = $('forums-'+ id);

		if (target.style.display === 'none') {
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
	 * @param {Element} self
	 */
	toggleCheckboxes: function(self) {
		var node = new Element(self),
			form = node.getParent('form');

		form.getElements('input[type="checkbox"]').set('checked', self.checked);
	},

	/**
	 * AJAX call to subscribe to a topic. Use the button's href attribute as the AJAX URL.
	 *
	 * @param {Element} self
	 */
	subscribe: function(self) {
		var node = new Element(self);

		if (node.hasClass('disabled')) {
			return false;
		}

		new Request.JSON({
			method: 'POST',
			url: node.get('href'),
			onSuccess: function(response) {
				$$('.subscription').set('text', response.data).addClass('disabled');
			}
		}).send();

		return false;
	},

	/**
	 * Unsubscribe from a topic.
	 *
	 * @param {Element} self
	 */
	unsubscribe: function(self) {
		return Forum.subscribe(self);
	}

};
