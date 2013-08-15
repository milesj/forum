/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

'use strict';

var Forum = {

	/**
	 * Update an input with a characters remaining info box.
	 *
	 * @param {Element} input
	 * @param {int} max
	 */
	charsRemaining: function(input, max) {
		var target = $(input.get('id') + 'CharsRemaining'),
			current = max - input.value.length;

		if (current < 0) {
			current = 0;
			input.value = input.value.substr(0, max);
		}

		target.set('html', current);
	},

	/**
	 * Toggle a buried post.
	 *
	 * @param {int} post_id
	 * @returns {boolean}
	 */
	toggleBuried: function(post_id) {
		$('post-buried-' + post_id).toggle();

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

		if (node.hasClass('is-disabled')) {
			return false;
		}

		new Request.JSON({
			method: 'POST',
			url: node.get('href'),
			onSuccess: function(response) {
				$$('.subscription').set('text', response.data).addClass('is-disabled');
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
	},

	/**
	 * Rate a post.
	 *
	 * @param {int} post_id
	 * @param {String} type
	 * @returns {boolean}
	 */
	ratePost: function(post_id, type) {
		new Request.JSON({
			method: 'POST',
			url: '/forum/posts/rate/' + post_id + '/' + (type == 'up' ? 1 : 0),
			onSuccess: function(response) {
				var parent = $('post-ratings-' + post_id),
					rating = parent.getElement('.rating');

				parent.getElements('a').dispose();

				if (response.success) {
					if (rating) {
						parent.addClass('has-rated');
						rating.set('text', parseInt(rating.get('text')) + (type == 'up' ? 1 : -1));
					} else {
						parent.dispose();
					}
				}
			}
		}).send();

		return false;
	}

};

window.addEvent('domready', function() {
	Titon.Tooltip.factory('.js-tooltip', {
		position: 'topCenter'
	});
});