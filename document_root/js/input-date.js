/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */

/**
* Funkce pro upravu datumu
*/
var DateInput = {
	/**
	* Hodnota bez data
	*/
	blank: '__.__.__',


	/**
	* Zacatek vkladani datumu
	*/
	zacni: function(el) {
		el.date = new Date;
		DateInput.parse(el);

		// Zadani niceho
		DateInput.zadej(el, -1, -1);
	},

	parse: function(el) {
		// Naparsujeme aktualni datum
		var x = el.value.match(/^([0-9_]{1,2}).\s*([0-9_]{1,2}).\s*([12][0-9])?([0-9_]{1,2})$/);
		if(x) {
			el.datumDen = x[1];
			el.datumMesic = x[2];
			el.datumRok = x[4];
		} else {
			el.datumDen = el.datumMesic = el.datumRok = '__';
		}
	},

	/**
	* Zadani hodnoty
	*/
	zadej: function(el, pos, code) {
		if(pos >= 8) return false;
		el.changed = true;

		var s = '';
		s += el.datumDen.padStart(2, '0') + '.';
		s += el.datumMesic.padStart(2, '0') + '.';
		s += el.datumRok.padStart(2, '0');

		// Je to cislo
		if((code >= 48 && code <= 57) || code == 0) {
			var znak = code ? String.fromCharCode(code) : '_';

			var zac = s.substr(0, pos);
			var kon = s.substr(pos + 1, 7 - pos);

			s = zac + znak + kon;
		}
		el.value = s;
		DateInput.parse(el);

		DateInput.selection(el, pos + 1);
	},

	/**
	* Provede zvyrazneni
	*/
	selection: function(el, pos) {
		if(pos == 2 || pos == 5) pos++;

		el.selectionStart = pos;
		el.selectionEnd = pos + 1;
	},

	/**
	* Stisk klavesy
	*/
	onkeypress: function(ev) {
		if(ev.ctrlKey) {
			if(ev.charCode == 99) { // Copy
				this.selectionStart = 0;
				this.selectionEnd = 8;
			}
			else if(ev.charCode == 118) { // Paste
				this.value = '';
			}

			return true;
		}

		// Zacatek oznaceni
		var s = this.selectionStart, e = this.selectionEnd;

		if(ev.keyCode == Event.KEY_TAB) {
			(this.onchange || Prototype.emptyFunction)(ev);
			//if(this.changed) Element.fire(this, 'change', { prev: ev } );
			return true;
		}

		else if(ev.keyCode == Event.KEY_RETURN) {
			(this.onchange || Prototype.emptyFunction)(ev);
			//if(this.changed) Element.fire(this, 'change', { prev: ev } );
			this.blur();
			return;
		}

		// Home
		else if(ev.keyCode == Event.KEY_HOME) DateInput.selection(this, 0);

		// End
		else if(ev.keyCode == Event.KEY_END) DateInput.selection(this, this.value.length - 1);

		// Komplet smazani
		else if(s == 0 && e == this.value.length && (ev.keyCode == Event.KEY_BACKSPACE || ev.keyCode == Event.KEY_DELETE)) {
			this.value = DateInput.blank;
			this.changed = true;
		}

		// Backspace
		else if(ev.keyCode == Event.KEY_BACKSPACE) {
			if(s == 3 || s == 6) s--;

			if(s > 0) DateInput.zadej(this, s - 1, 0);
			DateInput.selection(this, s - 1);
		}

		// Delete
		else if(ev.keyCode == Event.KEY_DELETE) {
			DateInput.zadej(this, s, 0);
			DateInput.selection(this, s + 1);
		}

		// Left
		else if(ev.keyCode == Event.KEY_LEFT || ev.keyCode == Event.KEY_UP) {
			if(s == 3 || s == 6) s--;
			DateInput.selection(this, s - 1);
		}

		// Right
		else if(ev.keyCode == Event.KEY_RIGHT || ev.keyCode == Event.KEY_DOWN) {
			DateInput.selection(this, s + 1);
		}

		// Ulozime znak
		else if(ev.charCode) DateInput.zadej(this, s, ev.charCode, ev);

		// Zvyraznime
		else DateInput.selection(this, s);

		return false;
	},

	/**
	* Inicializace inputu
	*/
	init: function(inp) {
		// Stisk klavesy
		inp.onkeypress = DateInput.onkeypress;

		// Kliknuti na formular
		inp.onfocus = function(ev) {
			DateInput.zacni(this);
		}.prepend(inp.onfocus);

		inp.onclick = function(ev) {
			DateInput.selection(this, this.selectionStart);
		}.prepend(inp.onclick);

		// Kontrola pri opusteni inpuptu
		Element.observe(inp, 'blur', function(ev) {
			if(inp.changed) Element.fire(inp, 'change', { prev: ev } );
			var opts;

			// Neni zadane datum
			if(this.value == DateInput.blank) {
				this.value = '';
				this.removeClassName('invalidValue');
			}

			if(this.validate(this, opts = { errors: {} })) {
				this.removeClassName('invalidValue');
				this.myLocalize();
			} else {
				this.addClassName('invalidValue');
				//alert(opts.errors[this.name]);

				// Dame focus
				// try { this.focus(); } catch(e) {}
			}
		});

		Element.observe(inp, 'contextmenu', function(ev) {
			if(ev.target.value == DateInput.blank) ev.target.value = '';
		} );


		// Validace
		inp.validate = function(el, options) {
			// Neni zadane a je pozadovane
			if(el.value.blank() && (el.required || el.getAttribute('required'))) {
				var req = el.required || el.getAttribute('required');
				if(req == 1) req = 'Hodnota neni zadana, ale je pozadovana';

				options.errors[el.name] = req;
				return false;
			}

			// Je zadane a neni platne
			if(el.value && Date.parseLocal(el.value) == null) {
				options.errors[el.name] = 'Neplatny format data!';
				return false;
			}

			// Je to OK
			return true;
		};

		// Fce pro zmenu hodnot
		inp.myLocalize = function(ret) {
			console.log('Localizing date', this.value);
			if(this.value.blank() || this.value == DateInput.blank) {
				this.value = '';
				return;
			}
			else {
				var d = Date.parse2(this.value);
				if(d) this.value = d.getLocal();
				else this.value = '';
			}
		};
		inp.getValue = function() {
			if(this.value.blank()) return '';
			else return Date.parseLocal(this.value).serialize();
		};
		inp.setValue = function(val) { this.value = val; this.myLocalize(); };
		inp.getDate = function() {
			return this.value.blank() ? undefined : Date.parseLocal(this.value);
		};
		inp.setDate = function(datum) {
			if(!datum) this.value = '';
			else this.value = datum.getLocal();
		};
	}
};




// Register input with date formatting
inputFormats.add('date', function(inp, options, format) {
	// Add calendar icon
	var icon = document.createElement('img');
	icon.wrap('span');
	icon.className = 'icon calendar';

	inp.insertAdjacentElement('afterEnd', icon);
	inp.setAttribute('size', 8);

	Calendar.setup(
		{
			parentElement: inp.parentElement,
			inputField: inp,
			ifFormat: '%d.%m.%Y',
			button: icon
		}
	);

	// Inicializace
	DateInput.init(inp);
});


inputFormats.add('time', function(inp, options, format) {
	if(!inp.onblur) inp.onblur = function() {
		if(this.value && !this.value.match(/^[0-2]?[0-9]:[0-5]?[0-9](:[0-5]?[0-9])?$/)) {
			this.addClassName('invalidValue');
			alert('Invalid time format!');
			try { inp.focus(); } catch(e) {}
		} else {
			this.removeClassName('invalidValue');
		}
	};
});
