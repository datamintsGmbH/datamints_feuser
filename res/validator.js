
window.onload = function() {
	var form = document.getElementById('datamints_feuser_' + contentid + '_form');
	addEvent(form, 'submit', formCheck);
	form.getElementsByTagName('input');
	var input;
	for (var i = 0; i < inputids.length; i++) {
		input = document.getElementById(inputids[i]);
		// Wenn Input Typ eine Checkbox ist ein Klickevent setzten, da der IE bei onchange das Event erst  nach verlieren des Focus auslöst.
		if (input.type == 'checkbox') {
			addEvent(input, 'click', inputItemCheck);
		} else {
			addEvent(input, 'change', inputItemCheck);
		}
	}
}

function formCheck(evt) {
	var error;
	var ret = false;

	for (fieldId in inputids) {
		if (typeof(inputids[fieldId]) == 'function') continue;
		error = inputItemCheck(null, document.getElementById(inputids[fieldId]));
		if (error == true && ret == false) {
			ret = true;
			window.event ? event.returnValue = false : evt.preventDefault();
		}
	}
}

function inputItemCheck(evt, input) {
	var ret = false;
	var arrLength = null;
	if (evt != null) {
		if (evt.target) {
			input = evt.target;
		} else {
			input = evt.srcElement;
		}
	}
	var value = input.value;
	if (input.type == 'select-one' || input.type == 'select-multiple') {
		value = new Array();
		var i = 0;
		var j = 0;
		for (i in input.options) {
			if (input.options[i] != null && input.options[i].selected) {
				value[j] = input.options[i].value;
				j++;
			}
		}
	}
	if (input.type == 'checkbox') {
		value = input.checked;
	}
	var fieldName = input.name.split('[')[1].split(']')[0];
	if (fieldName.split('_')[1] == 'rep') {
		fieldName = fieldName.split('_')[0];
	}

	// Den Error Dialog löschen, damit er wenn die Validierung korrekt ist nicht mehr da ist.
	removeInfo(fieldName);

	if (config[fieldName] != null) {
		var error_item;
		var validate = config[fieldName]['validation'];
		if (config[fieldName]['required'] && (!value || (typeof(value) == 'object' && !value.length))) {
			error_item = input;
			if (validate && validate['type'] == 'password') {
				if (input.id.split('_').reverse()[0] != 'rep') {
					input_rep = document.getElementById(input.id + '_rep');
					error_item = input_rep;
				}
			}
			ret = true;
			removeInfo(fieldName);
			showInfo(error_item, fieldName, 'required');
		} else if (validate) {

			switch (validate['type']) {

				case 'password':
					var input_rep;
					if (input.id.split('_').reverse()[0] != 'rep') {
						input_rep = document.getElementById(input.id + '_rep');
						error_item = input_rep;
					} else {
						input_rep = document.getElementById(input.id.slice(0, input.id.length - 4));
						error_item = input;
					}
					var value_rep = input_rep.value;
					if (value != '' && value_rep != '') {
						arrLength = new Array('6');
						if (value == value_rep) {
							if (validate['size']) {
								arrLength = validate['size'].replace(' ', '').split(',');
								if (arrLength[1]) {
									// Wenn eine Maximallänge festgelegt wurde.
									if (value.length < arrLength[0] || value.length > arrLength[1]) {
										ret = true;
										removeInfo(fieldName);
										showInfo(error_item, fieldName, 'size');
									}
								} else {
									// Wenn nur eine Minimallänge festgelegt wurde.
									if (value.length < arrLength[0]) {
										ret = true;
										removeInfo(fieldName);
										showInfo(error_item, fieldName, 'size');
									}
								}
							} else {
								// Wenn nur eine Minimallänge festgelegt wurde.
								if (value.length < arrLength[0]) {
									ret = true;
									removeInfo(fieldName);
									showInfo(error_item, fieldName, 'size');
								}
							}
						} else {
							ret = true;
							removeInfo(fieldName);
							showInfo(error_item, fieldName, 'equal');
						}
					}
					break;

				case 'email':
					error_item = input;
					if (!value.match(/^[a-zA-Z0-9\._%+-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,6}$/)) {
						ret = true;
						removeInfo(fieldName);
						showInfo(error_item, fieldName, 'valid');
					}
					break;

				case 'username':
					error_item = input;
					if (!value.match(/^[^ ]*$/)) {
						ret = true;
						removeInfo(fieldName);
						showInfo(error_item, fieldName, 'valid');
					}
					break;

				case 'custom':
					error_item = input;
					if (validate['regexp']) {
						if (typeof(value) == 'object') {
							var k = 0;
							for (k in value) {
								if (!value[k].match(validate['regexp'])) {
									ret = true;
									removeInfo(fieldName);
									showInfo(error_item, fieldName, 'valid');
								}
							}
						} else {
							if (!value.match(validate['regexp'])) {
								ret = true;
								removeInfo(fieldName);
								showInfo(error_item, fieldName, 'valid');
							}
						}
					}
					if (validate['size']) {
						arrLength = validate['size'].replace(' ', '').split(',');
						if (arrLength[1]) {
							// Wenn eine Maximallänge festgelegt wurde.
							if (value.length < arrLength[0] || value.length > arrLength[1]) {
								ret = true;
								removeInfo(fieldName);
								showInfo(error_item, fieldName, 'size');
							}
						} else {
							// Wenn nur eine Minimallänge festgelegt wurde.
							if (value.length < arrLength[0]) {
								ret = true;
								removeInfo(fieldName);
								showInfo(error_item, fieldName, 'size');
							}
						}
					}
					break;

			}

		}
	}

	return ret;
}

function showInfo(input, fieldName, error) {
	var div = document.createElement('div');
	div.className = 'form_error ' + fieldName + '_error';
	div.innerHTML = config[fieldName][error];
	input.parentNode.insertBefore(div, input.nextSibling);
}

function removeInfo(fieldName) {
	var error_item_father = document.getElementById('datamints_feuser_' + contentid + '_' + fieldName + '_wrapper');
	if (error_item_father != undefined && error_item_father.lastChild.className == 'form_error ' + fieldName + '_error') {
		error_item_father.removeChild(error_item_father.lastChild);
	}
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
