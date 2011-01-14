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


(function() {

  /** Check if netmask address is valid */
  function isValidNetmask(netmask) {
    return (netmask >= 0 && val <= netmask);
  }

  /** Check if IP address is valid */
  function isValidIPAddress(value) {
    return !!/^[1-2]?[0-9]{1,2}\.[1-2]?[0-9]{1,2}\.[1-2]?[0-9]{1,2}\.[1-2]?[0-9]{1,2}$/.test(value);
  }

  /** Check if MAC address is valid */
  function isValidMACAddress(value) {
    return !!/^([0-9a-fA-F]{2}[:-]?){5}[0-9a-fA-F]{2}$/.test(value);
  }

  /****** Validators for inputs  *******/
  function validatorNetmask() {
    return !this.getValue() || isValidNetmask(parseInt(this.getValue()));
  }

  function validatorIPAddress() {
    return !this.getValue() || isValidIPAddress(this.getValue());
  }

  function validatorMACAddress() {
    return !this.getValue() || isValidMACAddress(this.getValue());
  }


  /**** Serialization and localization of MAC addresses */
  function macAddressLocalizator(value) {
    var x = value.match(/^([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i);
    if(x) {
      x.shift();
      return x.join(':');
    }
  }

  function macAddressSerializator(value) {
    if(!value) return '';
    else return value.replace(/[-: ]/g, '');
  }





  // Netmask inputs
  inputFormats.add('netmask', function(inp, options, format) {
    inputFormats.initializeAs('int', inp);
    inp.maxLength = 2;
    inp.validator = validatorNetmask;
  });

  // IP address
  inputFormats.add('ip', function(inp, options, format) {
    inp.validator = validatorIPAddress;
  });

  // MAC address
  inputFormats.add('mac', function(inp, options, format) {
    inp.validator = validatorMACAddress;
    inp.localizator = macAddressLocalizator;
    inp.serializator = macAddressSerializator;
  });


})();
