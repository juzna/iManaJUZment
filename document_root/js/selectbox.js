/**
* SelectBox - more powerful <select> element
*/

// Create basic class
var SelectBox = Class.create();

// Define parameters and static variables
SelectBox.allowCache = true; // Whether to allow cache
SelectBox.cache = {}; // Cache for ajax loaded selectboxes
SelectBox.flushCache = function() {
	for(var i in SelectBox.cache) delete SelectBox.cache[i];
};
SelectBox.config = {}; // Config per SelectBoxName

SelectBox.config['ucto,ciselnaRada'] = { showDialogButton: true, dialogUrl: 'index.php?id=ucto&fce=ciselnaRada&agenda=#{subname}', readOnly: true, title: 'Ciselna rada', arrowTitle: 'Vyber ciselne rady', buttonTitle: 'Nastaveni ciselnych rad', dialogNewTab: true, allowCache: false };
SelectBox.config['ucto,cislo']	      = { showDialogButton: false, readOnly: true, allowCache: false };
SelectBox.config['ucto,predkontace'] = { showDialogButton: true, dialogUrl: 'index.php?id=ucto&fce=predkontace&agenda=#{subname}', readOnly: true, title: 'Predkontace' };
SelectBox.config['ucto,stredisko']   = { showDialogButton: true, dialogUrl: 'index.php?id=ciselnik&tbl=stredisko', title: 'Stredisko' };
SelectBox.config['ucto,osnova']      = { showDialogButton: true, dialogUrl: 'index.php?id=ciselnik&tbl=osnova', title: 'Uctova osnova', width: '60px' };
SelectBox.config['ucto,platbaMisto'] = { showDialogButton: true, dialogUrl: 'index.php?id=ucto&fce=platbaMisto', readOnly: true, title: 'Pokladna' };
SelectBox.config['ucto,bankUcet'] =    { showDialogButton: true, dialogUrl: 'index.php?id=ucto&fce=bankUcet', readOnly: true, title: 'Bankovni ucty' };

/**
* Factory for creating SelectBox elements
*/
SelectBox.factory = function(name, type, value, params) {
	var el = new Element('input', {
		type: 'text',
		name: name,
		selectBoxName: type,
		value: value || '',
	});
	
	(function() { new SelectBox(el, params); }).defer();
	
	return el;
};


Object.extend(SelectBox.prototype, {
	// Observer for events
	observe: Observer.observe.methodize(),
	stopObserving: Observer.stop.methodize(),
	
	/**
	* Convert input (type=text) to SelectBox
	* @param Element inputElement Element to be converted to SelectBox
	* @param object options
	*/
	initialize: function(inputElement, options) {
		// Check wherher it was already created?
		if(inputElement.selectBox) return inputElement.selectBox;
		inputElement.selectBox = this;
		
		var name = inputElement.getAttribute('selectBoxName');
		var subname = inputElement.getAttribute('selectBoxSubName');
		
		// Get configured value
		var getConfig = function(varName, defaultValue) {
			if(!name) return defaultValue;
			
			var cfg = SelectBox.config[name];
			if(typeof cfg == 'undefined') return defaultValue;
			else if(typeof cfg[varName] == 'undefined') return defaultValue;
			else return cfg[varName];
		};
		
		// Default options
		var title = getConfig('title', name);
		var defaultOptions = {
			name: 		name,			// Name for ajax
			subname: 	subname,		// Subname for ajax
			title:		title,			// Title for some messages
			allowCache: 	SelectBox.allowCache,	// Whether to allow cache for this selectbox
			allowAjax: 	true,			// Allow loading data via ajax request
			ajaxParams: 	{},			// Parameters to be sent to server via ajax request
			showHeader: 	true,			// Show header in option list
			showDialogButton: false,		// Show dialog button?
			dialogNewTab:	false,			// Instead of dialog open in new tab
			dialogUrl: 	'',			// Dialog URL
			monospace: 	false,			// Use fixed-width font
			readOnly:	false,			// User can't rewrite the value
			arrowTitle: 	'Vyber z ' + title,	// Tooltip for arrow
			buttonTitle:	'Nastaveni ' + title,	// Tooltip dialog button
			width:		null,
			optionListOverride: false,		// Vlastni option list
			clearOnDependChange: true,		// Clear actual value when dependancy is changed
		};
		
		// Get options
		this.options = options || {};
		if(typeof options == 'undefined') options = {};
		for(var i in defaultOptions) {
			if(typeof options[i] != 'undefined') this.options[i] = options[i];
			else this.options[i] = getConfig(i, defaultOptions[i]);
		}
		
		this.initialized = false; // Wherher it's successfully initialized
		this.generated = false;
		this.inputElement = inputElement; // Input element
		this.optionsVisible = false; // Option list is visible
		this.hoveredItem = null;
		this.selectedItem = null;
		this.fieldList = options.fieldList; // Array of fields
		this.optionList = null; // Array of option items
		this.hashedList = null; // Object (associative) of items where key is indexFile
		this.fieldIndex = 0; // Fild which indicates unique index
		this.depend = {}; // Dependant fields
		
		// Store events (so we can remove them later)
		this.eventHandlers = [];
		this.replacedProperties = [];
		
		if(this.options.readOnly) this.inputElement.readOnly = true;
		if(this.options.width) this.inputElement.style.width = this.options.width;
		
		// Create container
		inputElement.addClassName('selectBoxInput');
		this.inputElement.setAttribute('autocomplete', 'off');
		this.container = new Element('span', { className: 'selectBoxContainer' } );
		inputElement.parentNode.insertBefore(this.container, inputElement);
		this.container.appendChild(inputElement);
		if(this.options.monospace) this.container.style.fontFamily = 'monospace';
		
		// Create arrow icon
		this.container.appendChild(this.arrowImg = new Element('span', { className: this.options.showDialogButton ? 'selectBoxArrow2' : 'selectBoxArrow', title: this.options.arrowTitle } ));
		this.arrowImg.observe('click', function(ev) {
			if(this.disabled) return; // Ignore it
			if(this.optionsVisible) this.hideOptions();
			else this.showOptions();
			
			// Clear filter
			this.clearFilter();
		}.bindAsEventListener(this));
		
		// Create dialog button
		if(this.options.showDialogButton) {
			this.container.appendChild(this.dialogImg = new Element('span', { className: 'selectBoxDialogButton', title: this.options.buttonTitle } ));
			this.dialogImg.observe('click', function(ev) {
				if(!this.disabled) this.showDialog(ev);
			}.bindAsEventListener(this));
		}
		
		// Create option list container
		this.divOptions = new Element('div', { className: 'selectBoxOptions' } );
		this.container.appendChild(this.divOptions);
		if(!this.options.optionListOverride) {
			this.tbl = new Element('table');
			this.tbl.appendChild(this.tbh = new Element('thead'));
			this.tbl.appendChild(this.tbd = new Element('tbody'));
			this.divOptions.appendChild(this.tbl);
		}
		
		// Ajax parameters
		if(this.inputElement.hasAttribute('ajaxParams')) {
			this.inputElement.getAttribute('ajaxParams').split(',').each(function(param) {
				var x = param.split('=');
				if(x.length >= 2) this.options.ajaxParams[x[0]] = x[1];
			}, this);
		}
		
		// Add dependacies from attribute
		if(this.inputElement.hasAttribute('dependant')) {
			this.inputElement.getAttribute('dependant').split(',').each(function(depItem) {
				var x = depItem.split('=');
				if(x.length >= 2) this.addDepend(x[0], this.inputElement.form.elements[x[1]]);
				else this.addDepend(this.inputElement.form.elements[depItem]);
			}, this);
		}
		
		// Add given dependancies
		if(this.options.dependant) for(var name in this.options.dependant) {
			this.addDepend(name, this.options.dependant[name]);
		}
		
		// Localize value
		if(!inputElement.value.blank()) {
			this.value = inputElement.getValue();
			// console.log(this.inputElement, 'Set serialized value to', this.value);
			this.localize();
		}
		else this.value = '';
		
		// Add event handlers
		if(!this.options.optionListOverride) {
			this.observe(this.tbd, 'click', this.onTBodyClick, true);
			this.observe(this.tbh, 'click', this.onTHeadClick, true);
		}
		this.observe(this.inputElement, 'blur', this.onBlur, true);
		//this.observe(this.inputElement, 'focus', function() { this.showOptions(); }, true);
		this.observe(this.inputElement, 'blur', function(ev) { var trg = ev.explicitOriginalTarget; if(!trg.up) trg = trg.parentNode; if(trg.up && (trg.up('span.selectBoxContainer') != this.container)) this.hideOptions(); }, true, undefined, true);
		this.observe(this.inputElement, 'change', this.onchange, true);
		this.replaceProperty(this.inputElement, 'getValue', function() { return this.getValue(); }.bind(this));
		this.replaceProperty(this.inputElement, 'setValue', function(val) { return this.setValue(val); }.bind(this));
		this.replaceProperty(this.inputElement, 'disable', function() { this.disabled = true; this.hideOptions(); this.container.addClassName('disabled'); this.inputElement.disabled = true; return this.inputElement; }.bind(this));
		this.replaceProperty(this.inputElement, 'enable', function() { this.disabled = false; this.container.removeClassName('disabled'); this.inputElement.disabled = false; return this.inputElement; }.bind(this));
		this.replaceProperty(this.inputElement, 'clear', function() { this.value = null; this.inputElement.value = ''; }.bind(this));
		
		// Mark as initialized
		this.initialized = true;
	},
	
	/**
	* Replace property
	*/
	replaceProperty: function(element, propName, newVal) {
		// Remember old value
		this.replacedProperties.push( { element: element, propName: propName, oldVal: element[propName] } );
		
		// Store new value
		element[propName] = newVal;
	},
	
	/**
	* Load data for this selectbox and then call callback
	*/
	getData: function(callback) {
		var dataTable   = (this.options.dataTableSource || Prototype.emptyFunction)(this) || this.inputElement.selectBoxTable || this.inputElement.getAttribute('selectBoxTable');
		var dataOptions = (this.options.dataOptionsSource || Prototype.emptyFunction)(this) || this.inputElement.selectBoxOptions|| this.inputElement.getAttribute('selectBoxOptions');
		var dataHeader  = this.inputElement.selectBoxHeader || this.inputElement.getAttribute('selectBoxHeader') || this.inputElement.getAttribute('header');
		var data;
		
		// Table is set
		if(dataTable) {
			if(typeof dataTable == 'string') data = dataTable.split('|');
			else if(Object.isArray(dataTable)) data = dataTable;
			else throw new Error("Wrong data type in dataTable parameter");
			
			for(var i in data) {
				if(typeof data[i] == 'string') data[i] = data[i].split(';');
				else if(!Object.isArray(data[i]) && data.constructor.name !== 'Object') {
					delete data[i];
					// echo error
				}
			}
			
			// TODO: this.fieldList
			this.optionList = data;
		}
		
		// Option list is set
		else if(dataOptions) {
			if(typeof dataOptions == 'string') data = dataOptions.split(';');
			else if(Object.isArray(dataOptions)) data = dataOptions;
			else throw new Error("Wrong data type in dataOptions parameter");
			
			// Upravime na pole
			this.optionList = data.map(function(item) { if(!Object.isArray(item)) return [ item ]; });
		}
		
		// Get from cache
		else if(this.getCachedData()) {
		}
		
		// Load using AJAX
		else if(this.options.name && this.options.allowAjax) {
			this.loadAjax(callback);
			callback = null; // Do not use callback right now, but async
		}
		
		else throw new Error("No option list source found");
		
		// Call callback
		if(typeof callback == 'function') callback.call(this);
	},
	
	/**
	* Load data using ajax and then call callback
	*/
	loadAjax: function(callback) {
		// TODO: asserts
		
		var x = this.options.name.split(',');
		var modName = x[0];
		var selectBoxName = x[1];
		
		// Prepare URL
		var url = 'index.php?id=selectbox&fce=load&mod=' + modName + '&name=' + selectBoxName + '&subname=' + this.options.subname;
		
		if(this.ajaxRequest) {
			// TODO: cancel old request
		}
		
		// Send request
		this.loading = true;
		this.ajaxRequest = new Ajax.Request(url, {
			method: 'POST',
			parameters: this.options.ajaxParams,
			
			// When request is complete
			onComplete: function(transport) {
				this.loading = false;
				this.ajaxRequest = null;
				this.ajaxLoaded = true;
				
				var json = transport.responseJSON;
				if(!json) throw 'Chyba p�i na��t�n� selectboxu ze serveru, vr�ceno ' + transport.reponseText;
				if(!json.stav) return false;
				
				// Store data to cache
				if(this.options.allowCache) this.saveCache(json);
				
				// Get data
				this.fieldList = Object.values(json.fields); // Field list
				this.optionList = Object.values(json.data);
				this.fieldIndex = json.fieldIndex;
				this.options.showHeader = json.showHeader ? true : false;
				if(json.width) {
					this.options.width = json.width;
					this.inputElement.style.width = json.width + 'px'; // Update width
				}
				
				// Call callback
				(callback || Prototype.emptyFunction).call(this);
			}.bind(this),
		});
	},
	
	/**
	* Get options count
	* @return int Number of options; or undefined when options are not yet loaded
	*/
	getNumOptions: function() {
		if(Object.isArray(this.optionList)) return this.optionList.length;
		else return undefined;
	},
	
	/**
	* Show option list
	*/
	showOptions: function() {
		//console.log('Showing options', this);
		// Generate option list if not yet generated
		if(!this.generated) this.generate();
		
		this.inputElement.activate(); // Catch focus
		this.container.addClassName('selectBox_optionsVisible');
		this.optionsVisible = true;
		
		// Fire showed event
		this.inputElement.fire('selectbox:optionsShow', { selectBox: this } );
	},
	
	/**
	* Hide option list
	*/
	hideOptions: function() {
		//console.log('Hiding options', this);
		this.container.removeClassName('selectBox_optionsVisible');
		this.optionsVisible = false;
		
		// Fire hidden event
		this.inputElement.fire('selectbox:optionsHide', { selectBox: this } );
	},
	
	/**
	* Generate item list
	*/
	generate: function() {
		if(this.loading) return false; // Already loading
		if(this.generated) return true;
		
		// Remove old option list
		this.clearOptionList();
		
		if(this.options.optionListOverride) (this.options.generateOverride || Prototype.emptyFunction)(); // Own generator
		else if(this.optionList) this.generate2(); // Data already loaded
		else this.getData(this.generate2); // Data not loaded, do it async
	},
	
	/**
	* Async generate, when data are available
	*/
	generate2: function() {
		this.tbl.hide(); // Hide table to create DOM faster
		
		// Create table header
		if(this.options.showHeader) {
			var tr = new Element('tr');
			
			// Add header fields
			this.fieldList.each(function(field) {
				if(field.show) tr.appendChild((new Element('th')).update(field.title));
			});
			
			this.tbh.appendChild(tr);
		}
		
		// Prepare field variables
		var index = 0;
		var fieldVariables = this.fieldVariables = {};
		this.fieldList.each(function(field) {
			if(field.show) fieldVariables[field.variable] = index++;
		});
		
		// Create option list
		var cnt = this.optionList.length;
		for(var i = 0; i < cnt; ++i) {
			if(typeof this.optionList[i] == 'undefined') continue; // Item may be deleted
			this.addTableRow(i);
		}
		
		// Mark as generated
		this.generated = true;
		
		// Show table again
		this.tbl.show();
		
		// Fire generated event
		this.inputElement.fire('selectbox:generated', { selectBox: this } );
	},
	
	/**
	* Add option item row to table body
	*/
	addTableRow: function(i) {
		var tr = document.createElement('tr');
		tr.selectBoxOptionIndex = i;
		tr.selectBoxOption = this.optionList[i];
		tr.selectBoxOption.tr = tr;
		tr.setAttribute('selectBoxValue', this.optionList[i][this.fieldIndex]);
		
		// Add cols
		for(var j in this.fieldVariables) tr.appendChild((new Element('td')).update(this.optionList[i][j]));
		
		this.tbd.appendChild(tr);
	},
	
	/**
	* Clear all data
	*/
	clear: function() {
		this.clearOptionList();
		this.clearFilter();
		this.optionList = this.hashedList = null;
		// this.fieldList = this.fieldIndex = null;
		this.generated = false;
	},
	
	/**
	* Force to regenerate when next shown
	*/
	dirty: function() {
		this.optionList = undefined;
		
		if(this.optionsVisible) this.refresh(); // Visible, refresh immediately
		else {
			// Defer it
			this.clear();
			delete SelectBox.cache[this.getCacheName()];
			this.generated = false;
		}
	},
	
	/**
	* Refresh items (clear and show again)
	*/
	refresh: function() {
		this.clear();
		delete SelectBox.cache[this.getCacheName()];
		this.generate();
	},
	
	/**
	* Change SelectBox's name or subname
	*/
	rename: function(name, subname) {
		if(this.options.name == name && this.options.subname == subname) return; // No change, no job
		
		this.clear(); // Clear it
		
		// Change name
		this.options.name = name;
		this.options.subname = name;
		
		// Regenerate
		this.generate();
	},
	
	
	/**
	* Destroys selectbox and make input simple
	*/
	destroy: function() {
		this.container.parentNode.insertBefore(this.inputElement, this.container); // Move out of container
		this.container.remove(); // Remove container
		
		// Remove all event handlers
		Observer.clear(this);
		
		// Restore properties
		this.replacedProperties.each(function(item) {
			item.element[item.propName] = item.oldVal;
		});
		
		this.inputElement.removeClassName('selectBoxInput');
		
		delete this.inputElement.selectBox;
		for(var i in this) delete this[i];
	},
	
	/**
	* Clear option list
	*/
	clearOptionList: function() {
		if(this.options.optionListOverride) return; // Options list if overridden
		
		this.tbl.hide(); // Hide to remove it faster
		
		// Remove thead
		if(this.tbh.rows[0]) this.tbh.rows[0].remove();
		
		// Remove tbody rows
		var cnt = this.tbd.rows.length;
		while(cnt-- > 0) this.tbd.rows[0].remove();
		
		this.tbl.show(); // Show again
	},
	
	/**
	* Get key for cache hash
	*/
	getCacheName: function() {
		var name = this.options.name;
		if(this.options.subname) name += '|' + this.options.subname;
		if(Object.keys(this.options.ajaxParams).length) name += '|' + Object.toJSON(this.options.ajaxParams);
		return name;
	},
	
	/**
	* Save data from ajax to internal cache
	*/
	saveCache: function(json) {
		SelectBox.cache[this.getCacheName()] = json;
	},
	
	/**
	* Try to load data from internal cache
	*/
	getCachedData: function() {
		if(!this.options.allowCache) return false;
		
		var name = this.getCacheName();
		if(typeof SelectBox.cache[name] != 'undefined' && SelectBox.cache[name]) {
			var json = SelectBox.cache[name];
			this.fieldList = Object.values(json.fields); // Field list
			this.optionList = Object.values(json.data);
			
			this.fieldIndex = json.fieldIndex;
			this.options.showHeader = json.showHeader ? true : false;
			if(json.width) {
				this.options.width = json.width;
				this.inputElement.style.width = json.width + 'px'; // Update width
			}
			
			return true;
		}
		
		else return false;
	},
	
	/**
	* Click on option list table body handler
	*/
	onTBodyClick: function(ev) {
		var el = ev.target;
		var tagName = el.tagName.toLowerCase();
		var tr;
		
		if(tagName == 'tr') tr = el;
		else if(tagName == 'td') tr = el.parentNode;
		else return; // Click out of rows, ignore it
		
		// Select row and hide options
		this.selectRow(tr, true);
		this.hideOptions();
		
		// TODO: focus to next element
	},
	
	/**
	* Click on option list header
	*/
	onTHeadClick: function(ev) {
	
	},
	
	onBlur: function(ev) {
	
	},
	
	onchange: function(ev) {
		if(ev.memo && ev.memo.selectBoxOption) return; // It's our change, no need to handle it
		
		this.value = this.inputElement.value;
		this.localize(); // Try to localize it
	},
	
	/**
	* Mark given row as selected
	*/
	selectRow: function(tr, fireEvent) {
		// Unmark old selected item
		if(this.selectedItem && this.selectedItem != tr.selectBoxOption) {
			if(this.selectedItem.tr) this.selectedItem.tr.removeClassName('selected');
		}
		
		if(!tr) {
			this.value = '';
			this.inputElement.value = '';
			this.selectedItem = null;
		}
		
		else {
			this.value = tr.getAttribute('selectBoxValue');
			this.inputElement.value = tr.cells[0].textContent;
			this.selectedItem = tr.selectBoxOption;
			tr.addClassName('selected');
		}
		
		// Fire event
		if(fireEvent) Event.fire(this.inputElement, 'change', { selectBox: this, selectBoxOption: this.selectedItem } );
	},
	
	/**
	* Get serialized value
	*/
	getValue: function() {
		return this.value;
	},
	
	/**
	* Set value
	*/
	setValue: function(val) {
		this.inputElement.value = this.value = val;
		this.localize();
	},
	
	/**
	* Set serialized and localized value at once
	*/
	setValue2: function(serializedValue, localizedValue) {
		this.inputElement.value = localizedValue;
		this.value = serializedValue;
	},
	
	clearFilter: function() {},
	
	/**
	* Localize initial value
	*/
	localize: function() {
		// Overriden
		if(this.options.optionListOverride) return (this.options.localizeOverride || Prototype.emptyFunction)();
		
		if(this.inputElement.value.blank()) return;
		
		// Async
		if(this.optionList) this.localize2(); // Data loaded, localize directly
		else this.getData(this.localize2); // Do it async
	},
	
	/**
	* Localize value async callback
	*/
	localize2: function() {
		var val = this.inputElement.value;
		var item;
		
		// Unselect actual selected
		if(this.selectedItem && this.selectedItem.tr) {
			this.selectedItem.tr.removeClassName('selected');
			this.selectedItem = null;
		}
		
		// Find localized value
		if(item = this.getItemByValue(val)) {
			this.value = val;
			// console.log(this.inputElement, 'Set serialized value to', this.value);
			this.inputElement.value = item[this.getFirstColumn()];
			this.selectedItem = item;
			if(this.generated) this.selectedItem.tr.addClassName('selected');
		}
		else {
			// Store given value
			this.value = val;
			// console.log(this.inputElement, 'Set serialized value to', this.value);
		}
	},
	
	/**
	* Find option item by index value
	*/
	getItemByValue: function(val) {
		var key;
		
		// Create hashed list if not yet created
		if(!this.hashedList) {
			this.hashedList = {};
			
			// Create option list
			var cnt = this.optionList.length;
			for(var i = 0; i < cnt; ++i) {
				if(typeof this.optionList[i] == 'undefined') continue; // Item may be deleted
				
				key = this.optionList[i][this.fieldIndex];
				this.hashedList[key] = this.optionList[i];
			}
		}
		
		// Get it very easily
		return this.hashedList[val];
	},
	
	/**
	* Find first visible column name
	*/
	getFirstColumn: function() {
		var cnt = this.fieldList.length;
		for(var i = 0; i < cnt; ++i) {
			if(this.fieldList[i].show) return this.fieldList[i].variable;
		}
		
		return this.fieldIndex; // Default is index field
	},
	
	/**
	* Add item to option list
	*/
	addItem: function(item) {
		if(!this.optionList) throw new Error("Option list is not yet initialized");
		
		// Add item to option list
		var index = this.optionList.push(item) - 1;
		
		// Add to hash list
		if(this.hashedList) {
			var key = item[this.fieldIndex];
			this.hashedList[key] = item;
		}
		
		// Add to option list table
		if(this.generated) this.addTableRow(index);
		
		return index;
	},
	
	/**
	* Remove item from option list
	* @param int|object Item index or reference to item
	*/
	removeItem: function(item) {
		var itemIndex;
		if(typeof item == 'number') itemIndex = item;
		else itemIndex = this.getItemIndex(item);
		
		// Remove that item
		if(typeof itemIndex != undefined) {
			if(this.optionList[itemIndex]) this.optionList[itemIndex].tr.remove();
			delete this.optionList[itemIndex];
			
			return true;
		}
		else return false;
	},
	
	/**
	* Find item's index
	* @param object item Reference to item
	* @return int Index of item or undefined if not found
	*/
	getItemIndex: function(item) {
		var cnt = this.optionList.length;
		for(var i = 0; i < cnt; ++i) {
			if(this.optionList[i] == item) return i;
		}
	},
	
	/**
	* Update item's values
	* @param int index Index of item to be updated
	* @param object newValues Hash of new values
	*/
	updateItem: function(oldItem, newValues) {
		var itemIndex;
		if(typeof oldItem == 'number') itemIndex = oldItem;
		else itemIndex = this.getItemIndex(oldItem);
		
		if(typeof this.optionList[itemIndex] == 'undefined') return; // Item is not defined
		
		// Get this item
		item = this.optionList[itemIndex];
		var firstCol = this.getFirstColumn();
		
		// Check new values
		for(var key in newValues) {
			var val = newValues[key];
			if(item[key] == val) continue; // Value is not changed
			
			// Update value
			item[key] = val;
			
			// Update table cell
			if(this.generated) {
				var cellIndex = this.fieldVariables[key];
				if(typeof cellIndex != 'undefined') item.tr.cells[cellIndex].innerHTML = val;
				
				// Update row value
				if(key == this.fieldIndex) item.tr.setAttribute('selectBoxValue', val);
			}
			
			// Change input value it it's selected
			if(firstCol == key && item == this.selectedItem) {
				this.inputElement.value = val;
			}
		}
		
		return true;
	},
	
	/**
	* Show dialog
	*/
	showDialog: function(ev) {
		if(!this.dialogUrlTemplate) this.dialogUrlTemplate = new Template(this.options.dialogUrl);
		var url = this.dialogUrlTemplate.evaluate(Object.extend(this.options.ajaxParams, { subname: this.options.subname }));
		
		// Show in new tab
		if(this.dialogNewTab) {
			this.inputElement.fire('navigate', { url: url, newTab: true } );
			return;
		}
		
		// Create window
		if(!this.win) {
			this.win = new Window( {
				onShowed: this.onDialogShowed ? this.onDialogShowed.bind(this) : null,
				minimizable: false,
				maximizable: false,
				width: 400,
				height: 450,
				destroyOnClose: false,
				parent: Try.these(
					function() { return window.Tabs.getByElement(this.inputElement).content; }.bind(this),
					function() { return document.body; }
				),
				selectBox: this,
				inputElement: this.inputElement,
				onClose: function(win) {
					var selectBox = win.options.selectBox;
					selectBox.dialogImg.removeClassName('down');
					
					// Set value
					if(typeof win.returnValue != 'undefined') {
						selectBox.inputElement.setValue(win.returnValue);
						selectBox.inputElement.fire('change');
					}
					
					// Fire hide event
					selectBox.inputElement.fire('selectbox:dialogHide', { selectBox: selectBox } );
					
					// Activate next element
					if(win.options.inputElement.form) Form.activateNext(win.options.inputElement.form, win.options.inputElement);
				}
			} );
		}
		
		this.win.setAjaxContent(url);
		this.win.show();
		
		this.dialogImg.addClassName('down'); // Button down state
		
		// Fire event
		this.inputElement.fire('selectbox:dialogShow', { selectBox: this } );
	},
	
	/**
	* Add dependancy
	*/
	addDepend: function(varName, input) {
		if(arguments.length == 1 && Object.isElement(varName)) {
			input = varName;
			varName = input.name;
		}
		
		// Remove old dependancy
		if(typeof this.depend[varName] != 'undefined') this.removeDepend(varName);
		
		// Add new depend
		var x = {};
		x.input = input;
		x.eventHandlerIndex = this.observe(input, 'change', this.onDependChange, [ varName ] );
		x.eventHandlerIndex2 = this.observe(input, 'set', this.onDependChange, [ varName ] );
		this.depend[varName] = x;
		
		// Store actual value for ajax
		this.options.ajaxParams[varName] = input.getValue();
	},
	
	/**
	* Remove dependancy
	*/
	removeDepend: function(varName) {
		// Remove event handler
		if(this.depend[varName]) {
			this.stopObserving(this.depend[varName].input, 'change');
			this.stopObserving(this.depend[varName].input, 'set');
		}
		delete this.depend[varName];
	},
	
	/**
	* Handler for change on depend variable
	*/
	onDependChange: function(ev, varName) {
		var input = this.depend[varName].input;
		var val = input.getValue();
		
		// console.log('SelectBox', this.inputElement.name, 'depend changed', input, varName, val);
		
		// Clear value if needed
		var skipClearing = ev.memo && ev.memo.loading;
		if(this.options.clearOnDependChange && this.value && !skipClearing) {
			this.inputElement.value = this.value = '';
			Event.fire(this.inputElement, 'change', { selectBox: this } );
		}
		
		// New params list
		var x = {};
		x[varName] = val;
		
		this.updateAjaxParams(x);
	},
	
	/**
	* Change ajax params and refresh option list
	*/
	updateAjaxParams: function(newParams) {
		var numChanges = 0;
		
		// Check for changes
		for(var varName in newParams) {
			if(this.options.ajaxParams[varName] != newParams[varName]) {
				this.options.ajaxParams[varName] = newParams[varName];
				numChanges++;
			}
		}
		
		// Refresh if there were changes
		if(numChanges > 0) this.dirty();
	}
});


var SelectBoxTree = Class.create(SelectBox, {
	initialize: function($super, inputElement, treeOptions, selectBoxOptions) {
		var oThis = this;
		treeOptions = treeOptions || {};
		treeOptions.selectBox = this;
		
		this.onGenerated = [];
		
		// Extend selectBox options
		selectBoxOptions = Object.extend(selectBoxOptions || {}, {
			optionListOverride: true,
			generateOverride: function() { // Own generator
				if(!oThis.treeViewGenerated) oThis.treeViewGenerate();
			},
			
			localizeOverride: function() { // Overriden localize
				if(!oThis.treeViewGenerated) {
					oThis.onGenerated.push(arguments.callee);
					return;
				}
				var index = treeOptions.options.index;
				var val = inputElement.value;
				
				for(var i = 0; i < treeOptions.nodes.length; i++) {
					if(treeOptions.nodes[i][index] == val) {
						oThis.setValue2(val, treeOptions.nodes[i][treeOptions.cols[0].name]);
						break;
					}
				}
			},
			treeViewOptions: treeOptions,
		});
		
		$super(inputElement, selectBoxOptions);
	},
	
	/**
	* Generate tree view
	*/
	treeViewGenerate: function() {
		if(this.treeViewGenerated) return true;
		this.treeViewGenerated = true; // Mark as generated
		
		
		if(typeof this.options.treeViewOptions.options == 'undefined') this.ajaxLoad(this.generate2); // Load and then generate
		else this.generate2();
	},
	
	generate2: function() {
		// Initialize tree view
		this.treeView = new TreeView(this.divOptions, this.options.treeViewOptions);
		this.treeView.show();
		
		// Call all functions waiting for generation
		this.onGenerated.each(function(f) { f(); });
		this.onGenerated = [];
	},
	
	/*
	* Load data using ajax and then do callback
	*/
	ajaxLoad: function(callback) {
		var _name = this.inputElement.getAttribute('selectBoxTreeName');
		if(!_name) return;
		
		var oThis = this;
		var x = _name.split(',');
		var modName = x[0], tvName = x[1];
		
		new Ajax.Request('index.php?id=selectboxtree&fce=load&mod=' + modName + '&name=' + tvName + '&' + this.inputElement.getAttribute('ajaxParams'), {
			onSuccess: function(transport) {
				if(transport.getHeader('content-type') != 'application/javascript') {
					alert("Nedostal jsem spravny vysledek");
					return;
				}
				var x2 = eval('(' + transport.responseText + ')');
				
				Object.extend(oThis.options.treeViewOptions, x2);
				callback.call(oThis);
			}
		});
	}
});