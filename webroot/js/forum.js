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
        input = $(input);

		var target = $('#' + input.attr('id') + 'CharsRemaining'),
			current = max - input.val().length;

		if (current < 0) {
			current = 0;
			input.val(input.val().substr(0, max));
		}

		target.html(current);
	},

	/**
	 * Toggle a buried post.
	 *
	 * @param {int} post_id
	 * @returns {boolean}
	 */
	toggleBuried: function(post_id) {
		$('#post-buried-' + post_id).toggle();

		return false;
	},

	/**
	 * Toggle all checkboxes.
	 *
	 * @param {Element} self
	 */
	toggleCheckboxes: function(self) {
		$(self).parents('form')
            .find('input[type="checkbox"]').prop('checked', self.checked);
	},

	/**
	 * AJAX call to subscribe to a topic. Use the button's href attribute as the AJAX URL.
	 *
	 * @param {Element} self
	 */
	subscribe: function(self) {
		var node = $(self);

		if (node.hasClass('is-disabled')) {
			return false;
		}

		$.ajax({
			type: 'POST',
			url: node.attr('href')
        }).done(function(response) {
            $('.subscription').text(response.data).addClass('is-disabled');
        });

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
		$.ajax({
			type: 'POST',
			url: '/forum/posts/rate/' + post_id + '/' + (type == 'up' ? 1 : 0)
        }).done(function(response) {
            var parent = $('#post-ratings-' + post_id),
                rating = parent.find('.rating');

            parent.find('a').remove();

            if (response.success) {
                if (rating.length) {
                    parent.addClass('has-rated');
                    rating.text(parseInt(rating.text()) + (type == 'up' ? 1 : -1));
                } else {
                    parent.remove();
                }
            }
        });

		return false;
	}

};

$(function() {
	$('.js-tooltip').tooltip({
		position: 'topCenter'
	});
});