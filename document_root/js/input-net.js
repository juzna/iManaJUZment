
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
    inp.validate = validatorNetmask;
  });

  // IP address
  inputFormats.add('ip', function(inp, options, format) {
    inp.validate = validatorIPAddress;
  });

  // MAC address
  inputFormats.add('mac', function(inp, options, format) {
    inp.validate = validatorMACAddress;
    inp.localizator = macAddressLocalizator;
    inp.serializator = macAddressSerializator;
  });


})();
