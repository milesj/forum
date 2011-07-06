
var forum = {
	
	/**
	 * Open or close child forums.
	 * 
	 * @param string node
	 * @param string id
	 */
	toggle: function(self, id) {
		var node = $(self),
			target = $('#forums-'+ id);

		if (target.is(':hidden'))
			target.slideDown();
		else
			target.slideUp();

		if (node.text() == '+')
			node.html('-');
		else
			node.html('+');

		return false;
	},
	
	toggleCheckboxes: function(self) {
		var node = $(self),
			form = node.parents('form');
			
		form.find(':checkbox').attr('checked', self.checked);
	}
}

/**
 * Relocates the page to a new URL
 * @param string url
 * @param string text
 * @return boolean
 */
function goTo(url, text) {
	if (text != null) {
		if (confirm(text))
			document.location = url;
	} else {
		document.location = url;
	}
	
	return false;
}

/**
 * Toggle all checkboxes in the list
 * @param mixed current
 * @param string form
 * @param string field
 */
function toggleCheckboxes(current, form, field) {
	var cbs = document.getElementById(form +'AddForm').getElementsByTagName('input');
	var length = cbs.length;
	
	for (var i=0; i < length; i++) {
		if (cbs[i].name == 'data['+ form +']['+ field +'][]' && cbs[i].type == 'checkbox')
			cbs[i].checked = current.checked;
	}
}

