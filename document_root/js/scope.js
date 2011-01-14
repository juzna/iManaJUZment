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
 * Working with mutiple scopes in one applications
 */

var _scope = {}, _el = document.body; // Default scope and parent element
var Scope = {
	/**
	* Get scope witch name for element
	*/
	get: function(el, name) {
		while(el) {
			if(el._scope) {
				// Je vybrano konkrtni scope
				if(name) {
					if(typeof el._scope[name] != 'undefined') return el._scope[name];
				}
				
				// Prvni scope na rade
				else {
					for(var i in el._scope) return el._scope[i];
				}
			}
			
			// Jdeme na nadrazeny element
			el = el.parentNode;
		}
		
		// Nic nenalezeno, zkusime document scope
		if(name) {
			if(document._scope && typeof document._scope[name] != 'undefined') return document._scope[name];
			else return undefined;
		}
		
		// Prvni scope na rade
		else if(typeof document._scope == 'Object') {
			for(var i in document._scope) return document._scope[i];
		}
		
		// Neni vubec zadne -> udelame v documentu global
		return Scope.create(document, 'global');	
	},
	
	/**
	* Create scope for element
	*/
	create: function(el, name, scope) {
		el = $(el) || document;
		name = name || 'global';
		scope = Object.extend(scope || {}, { _el: el } );
		
		// Zakladni object pro scope
		if(typeof el._scope != 'Object') el._scope = {};
		
		// Ulozime scope
		return el._scope[name] = Object.extend(el._scope[name] || {}, scope);
	},
	
	/**
	* Get variable from all ancestors scopes
	*/
	getVar: function(el, scopeName, valueName) {
		while(el) {
			// Ma scope
			if(el._scope) {
				// Je vybrano konkrtni scope
				if(scopeName) {
					if(typeof el._scope[scopeName] != 'undefined' && typeof el._scope[scopeName][valueName] != 'undefined') return el._scope[scopeName][valueName];
				}
				
				// Vsechna mozna scope
				else {
					for(var i in el._scope) {
						if(typeof el._scope[i][valueName] != 'undefined') return el._scope[i][valueName];
					}
				}
			}
			
			// Jdeme na nadrazeny element
			el = el.parentNode;
		}
		
		// Nic nenalezeno, zkusime document scope
		if(scopeName) {
			if(document._scope && typeof document._scope[scopeName] != 'undefined' && typeof document._scope[scopeName][valueName] != 'undefined') return document._scope[scopeName][valueName];
			else return undefined;
		}
		
		// Prvni scope na rade
		else if(typeof document._scope == 'Object') {
			for(var i in document._scope) {
				if(typeof document._scope[i][valueName] != 'undefined') return document._scope[i][valueName];
			}
		}
		
		// Neni vubec zadne
		return undefined;
	},
	
	/**
	* Calls function within scope
	*/
	call: function(el, functionName) {
		var args = $A(arguments);
		var el = args.shift();
		var functionName = args.shift();
		
		var fce = Scope.getVar(el, undefined, functionName);
		if(typeof fce == 'function') return fce.apply(el, args);
		else {
			var e = new Error('Funkce ' + functionName + ' v danem scope neexistuje');
			e.element = el;
			e.scopeValue = fce;
			
			handleException(e);
		}
	},
	
	/**
	* Get element from scope
	*/
	getElementById: function(el, id, scopeName) {
		while(el) {
			// Ma scope
			if(el._scope) {
				// Je vybrano konkrtni scope
				if(scopeName) {
					if(typeof el._scope[scopeName] != 'undefined') {
						var ret = _(el._scope[scopeName]._el, id);
						if(ret) return ret;
					}
				}
				
				// Vsechna mozna scope
				else {
					for(var i in el._scope) {
						var ret = _(el._scope[i]._el, id);
						if(ret) return ret;
					}
				}
			}
			
			// Jdeme na nadrazeny element
			el = el.parentNode;
		}
		
		return undefined;
	},
	
	/**
	* Najde callback
	* @param element el Element, jehoz callback volame
	* @params Nazvy callbacku
	*/
	getCallback: function(el) {
		var args = $A(arguments);
		var fce;
		
		for(var i = 1; i < args.length; i++) {
			// This arg is function
			if(typeof args[i] == 'function') return args[i];
			
			var attr = el[args[i]] || el.getAttribute(args[i]) || args[i];
			
			if(typeof attr == 'function') return attr;
			else if(typeof attr == 'string' && attr.isIdent() && typeof (fce = Scope.getVar(el, null, attr)) == 'function') return fce;
			else if(typeof (fce = window[attr]) == 'function') return fce;
		}
	}
};
