
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

/**
 * Toggle to show/hide an element
 * @param string target
 * @param string toggler
 */
function toggleElement(target, toggler) {
	var element = document.getElementById(target);
	var text = toggler.innerHTML;

	if (element.style.display == 'none')
		element.style.display = 'block';
	else
		element.style.display = 'none';
	
	if (text == '+')
		toggler.innerHTML = '-';
	else
		toggler.innerHTML = '+';
		
	return false;
}
