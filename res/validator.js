
var formId = 0;
var config = [];
var inputids = [];
var contentids = [];

window.onload = function() {
	for (formId in contentids) {
		if (typeof(contentids[formId]) != 'number') continue;

		var form = document.getElementById('datamints_feuser_' + formId + '_form');
		addEvent(form, 'submit', formCheck);

		var input;
		for (var i = 0; i < inputids[formId].length; i++) {
			input = document.getElementById(inputids[formId][i]);
			// Wenn Input Typ eine Checkbox ist ein Klickevent setzten, da der IE bei onchange das Event erst nach verlieren des Focus ausloest.
			if (input.type == 'checkbox') {
				addEvent(input, 'click', inputItemCheck);
			} else {
				addEvent(input, 'change', inputItemCheck);
			}
		}
	}
}

function formCheck(evt) {
	var error;
	var ret = false;

	// ID des aktuell verwendeten Formulars ueber das aktuell verwendete Input Element ermitteln.
	formId = getEventTarget(evt).id.split('_')[2];

	for (fieldId in inputids[formId]) {
		if (typeof(inputids[formId][fieldId]) != 'string') continue;

		error = inputItemCheck(null, document.getElementById(inputids[formId][fieldId]));
		if (error == true && ret == false) {
			ret = true;
			window.event ? event.returnValue = false : evt.preventDefault();
		}
	}
}

function inputItemCheck(evt, input) {
	var i = null;
	var j = null;
	var arrLength = null;

	if (evt != null) {
		input = getEventTarget(evt);
	}

	var value = input.value;

	if (input.type == 'select-multiple') {
		j = 0;
		value = new Array();
		for (i = 0; i < input.options.length; i++) {
			if (input.options[i] != null && input.options[i].selected) {
				value[j] = input.options[i].value;
				j++;
			}
		}
	}

	if (input.type == 'checkbox') {
		value = input.checked;
	}

	if (input.type == 'radio') {
		value = false;
		var name = input.id.slice(0, input.id.lastIndexOf('_'));
		name = name.slice(0, name.lastIndexOf('_'));
		var elements = document.getElementById(name + '_wrapper').getElementsByTagName('input');
		for (i = 0; i < elements.length; i++) {
			if (elements[i] != null && elements[i].checked) {
				value = true;
			}
		}
	}

	var fieldName = input.name.split('[')[2].split(']')[0];
	if (fieldName.split('_').reverse()[0] == 'rep') {
		fieldName = fieldName.slice(0, fieldName.length - 4);
	}

	// Den Error Dialog loeschen, damit er wenn die Validierung korrekt ist nicht mehr da ist.
	removeInfo(fieldName);

	// ID des aktuell verwendeten Formulars ueber das aktuell verwendete Input Element ermitteln.
	formId = input.id.split('_')[2];

	if (config[formId][fieldName] != null) {
		var validate = config[formId][fieldName]['validation'];
		if (config[formId][fieldName]['required'] && (!value || (typeof(value) == 'object' && !value.length))) {
			showInfo(fieldName, 'required');
			return true;
		} else if (validate) {

			switch (validate['type']) {

				case 'password':
					var input_rep;
					if (input.id.split('_').reverse()[0] != 'rep') {
						input_rep = document.getElementById(input.id + '_rep');
					} else {
						input_rep = document.getElementById(input.id.slice(0, input.id.length - 4));
					}
					var value_rep = input_rep.value;
					if (value != '' || value_rep != '') {
						arrLength = new Array('6');
						if (value == value_rep) {
							if (validate['size']) {
								arrLength = validate['size'].replace(' ', '').split(',');
								if (arrLength[1]) {
									// Wenn eine Maximallaenge festgelegt wurde.
									if (value.length < arrLength[0] || value.length > arrLength[1]) {
										showInfo(fieldName, 'size');
										return true;
									}
								} else {
									// Wenn nur eine Minimallaenge festgelegt wurde.
									if (value.length < arrLength[0]) {
										showInfo(fieldName, 'size');
										return true;
									}
								}
							} else {
								// Wenn nur eine Minimallaenge festgelegt wurde.
								if (value.length < arrLength[0]) {
									showInfo(fieldName, 'size');
									return true;
								}
							}
						} else {
							showInfo(fieldName, 'equal');
							return true;
						}
					}
					break;

				case 'email':
					if (!value.match(/^[a-zA-Z0-9\._%+-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,6}$/)) {
						showInfo(fieldName, 'valid');
						return true;
					}
					break;

				case 'username':
					if (!value.match(/^[^ ]*$/)) {
						showInfo(fieldName, 'valid');
						return true;
					}
					break;

				case 'zero':
					if (value == '0') {
						showInfo(fieldName, 'valid');
						return true;
					}
					break

				case 'emptystring':
					if (value == '') {
						showInfo(fieldName, 'valid');
						return true;
					}
					break

				case 'custom':
					if (validate['regexp']) {
						if (typeof(value) == 'object') {
							var k = 0;
							for (k in value) {
								if (!value[k].match(validate['regexp'])) {
									showInfo(fieldName, 'valid');
									return true;
								}
							}
						} else {
							if (!value.match(validate['regexp'])) {
								showInfo(fieldName, 'valid');
								return true;
							}
						}
					}
					if (validate['size']) {
						arrLength = validate['size'].replace(' ', '').split(',');
						if (arrLength[1]) {
							// Wenn eine Maximallaenge festgelegt wurde.
							if (value.length < arrLength[0] || value.length > arrLength[1]) {
								showInfo(fieldName, 'size');
								return true;
							}
						} else {
							// Wenn nur eine Minimallaenge festgelegt wurde.
							if (value.length < arrLength[0]) {
								showInfo(fieldName, 'size');
								return true;
							}
						}
					}
					break;

			}

		}
	}

	return false;
}

function addEvent(obj, type, fn) {
   if (obj.addEventListener) {
      obj.addEventListener(type, fn, false);
   } else if (obj.attachEvent) {
      obj["e" + type + fn] = fn;
      obj[type + fn] = function() {obj["e" + type + fn](window.event);}
      obj.attachEvent("on" + type, obj[type + fn]);
   }
}

function getEventTarget(evt) {
	if (evt.target) {
		return evt.target;
	} else {
		return evt.srcElement;
	}
}

function showInfo(fieldName, error) {
	var error_item_father = getErrorItemFather(fieldName);
	if (error_item_father != undefined) {
		if (error_item_father.lastChild.className == 'form_error ' + fieldName + '_error') {
			error_item_father.removeChild(error_item_father.lastChild);
		}

		var div = document.createElement('div');
		div.className = 'form_error ' + fieldName + '_error';
		div.innerHTML = config[formId][fieldName][error];
		error_item_father.appendChild(div);
	}
}

function removeInfo(fieldName) {
	var error_item_father = getErrorItemFather(fieldName);
	if (error_item_father != undefined && error_item_father.lastChild.className == 'form_error ' + fieldName + '_error') {
		error_item_father.removeChild(error_item_father.lastChild);
	}
}

function getErrorItemFather(fieldName) {
	var fieldNameWrapper = fieldName;
	if (config[formId][fieldName]['validation'] && config[formId][fieldName]['validation']['type'] == 'password') {
		fieldNameWrapper = fieldName + '_rep';
	}

	return document.getElementById('datamints_feuser_' + formId + '_' + fieldNameWrapper + '_wrapper');
}
