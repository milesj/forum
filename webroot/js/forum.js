
var Forum = {
	
	/**
	 * Open or close child forums.
	 * 
	 * @param object self
	 * @param int id
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

