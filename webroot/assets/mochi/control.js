function setFocus(id) {
    var field = document.getElementById(id);
    if (field && field.focus && field.type != "hidden" && field.disabled != true) {
    	try {
			field.focus();
		} catch (err) {
		}
    }
}

function formatNumber(number) {
	var text = new String(number);
	while(text != (text = text.replace(/^(-?\d+)(\d{3})/, "$1,$2")));
	return text;
}

function isNumber(value) {
	return value != null && value != "" && !isNaN(value);
}
