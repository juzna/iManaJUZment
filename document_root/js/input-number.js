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
* Integer and decimal numbers
*/
(function() {
  function numericFormat(inp, options, format) {
    inp.format = format;
    inp.onkeypress = function(ev) {
      ev = ev || window.event;
      if(ev.ctrlKey) return;
      if(!ev.charCode) return; // Special key

      var code = ev.charCode || ev.keyCode;

      if(code == 8) return; // Backspace
      if(code >= 48 & code <= 57) return; // Number
      if(code == 45) return;

      // Decimal point
      if((this.format == 'float' || this.format == 'mena' || this.format == 'currency') && (code == 46 || code == 44) && this.value.indexOf('.') == -1 && this.selectionStart > 0) {
        var st = this.selectionStart;
        if(!st) return false;
        this.value = this.value.substr(0, st) + '.' + this.value.substr(st, this.value.length - st);
        this.selectionStart = this.selectionEnd = st + 1;
        return false;
      }

      return false;
    };
  }

  // Add formats
  inputFormats.add('int', numericFormat);
  inputFormats.add('float', numericFormat);
  inputFormats.add('currency', numericFormat);
  inputFormats.add('mena', numericFormat); // Czech alias for currency
})();
