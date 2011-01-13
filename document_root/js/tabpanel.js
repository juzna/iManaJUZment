
/**
* Panel se zalozkama
*/

var TabPanel = Class.create({
	// Styl pro TabPanel
	tabPanelClassName: '',
	
	// Cesta pro kolacky
	cookiePath: '/',


	/**
	* Inicializace, zadame element (div) ze ktereho bude panel tvoren
	*/
	initialize: function(el, params) {
		params = params || {};
		
		el.addClassName('tabPanel');
		
		var childs = el.childElements();
		
		this.counter = ++TabPanel.counter;
		this.params = params;
		this.element = el;
		this.element.tabPanel = this;
		this.id = el.id;
		this.pages = {};
		this.pagesCnt = 0;
		this.active = null;
		this.useCookie = el.hasAttribute('useCookie') ? (parseInt(el.getAttribute('useCookie')) ? true : false) : true; //!!params.useCookie;
		this.defaultCache = Element.hasAttribute(el, 'cache') ? (parseFloat2(el.getAttribute('cache')) ? true : false) : true; // Zda muzeme cachovat
		
		// Druhy index (napriklad cislo zakaznika)
		this.index2 = this.element.getAttribute('index2');
		this.id2 = this.id + (this.index2 ? ('-' + this.index2) : '');
		
		
		// Pridame styl
		this.element.addClassName(this.tabPanelClassName);
		this.element.addClassName('tabPanel');
		
		
		// Vytvorime radek se zalozkami
		this.tabRow = new Element('ol', { className: 'tabPanelTabs' } );
		this.element.insertBefore(this.tabRow, this.element.firstChild);
		
		// Projdeme vsecky elementy v panelu a pridame stranky
		childs.each(this.addPage.bind(this));
		
		// Oznacime defaultni
		var oznacena = this.getDefaultPage();
		if(!oznacena) throw new Error("TabPanel " + this.id + " nema zvolenou zadnou defaultni stranku");
		this.select(oznacena, false, true);
		
		// Register history handler
		var page = this.pages[oznacena];
		this.history = new HistoryStack( { page: oznacena, ajax: page.ajax, link: page.link } );
		HistoryHandler.register(this.element, this.onHistory.bind(this));
	},
	
	getPage: function(name) {
		return this.pages[name];
	},
	
	/**
	* History move
	*/
	onHistory: function(ev, step, visible) {
		if(!visible) return;
		
		var url = this.history.move(step);
		if(!url) return;
		
		// Stop event
		Event.stop(ev);
		
		// Move history
		var page = this.pages[url.page];
		this.select(url.page, true, true);
		
		if(url.ajax && (url.link != page.actualUrl)) this.pages[url.page]._load(url.link);
	},
	
	/**
	* Najde vychozi stranku
	*/
	getDefaultPage: function() {
		var ret;
		
		// Primo zadana u zalozky
		if(Element.hasAttribute(this.element, 'active')) {
			ret = this.element.getAttribute('active');
			if(typeof this.pages[ret] != 'undefined') return ret;
		}
		
		// Zadana pres URL
		if(Tabs) {
			var url = Tabs.getUrlParser(this.element);
			var tabIndex = url.get('tabPanel-' + this.id2);
			
			if(tabIndex) ret = tabIndex;
		} else {
			if(HistoryParser && (tabIndex = HistoryParser.get('tabPanel-' + this.id2))) ret = tabIndex;
		}
		
		// Cookies
		if(!ret && this.useCookie && (tabIndex = this.getCookie('tabPanel' + this.id2))) ret = tabIndex;
		
		// Co ma zvolen atribut selected
		for(var p in this.pages) {
			var x = this.pages[p].element.getAttribute('selected');
			if(x && parseInt(x)) return p;
		}
		
		// Defaultne: prvni
		if(ret && typeof this.pages[ret] != 'undefined') return ret;
		for(var i in this.pages) return i;
	},
	
	
	/**
	* Prida novou stranku do panelu
	*/
	addPage: function(el, params) {
		if(el.tabPage) return false; // Uz je pridan
		
		var name;
		
		// Parametry nejsou zadane -> hledame vychozi
		if(typeof params != 'object') {
			if(el && (name = el.getAttribute('name')) && this.params.pages && this.params.pages[name]) params = this.params.pages[name];
			else params = {};
		}
		
		// Nazev stranky
		this.pagesCnt++;
		if(!name) name = params.name || ('page-' + this.pagesCnt);
		
		// Error
		if(typeof this.pages[name] != 'undefined') {
			alert('Z�lo�ka se jm�nem ' + name + ' ji� existuje');
			return false;
		}
		
		params.name = name;
		
		// Vytvorime novou stranku
		var page = new TabPanel.Page(this, el, params);
		this.pages[name] = page;
		
		
		// Pridame mezi zalozky
		this.tabRow.appendChild(page.tab);
		
		// Active?
		if(el.getAttribute('active') == '1') this.element.setAttribute('active', name);
	},
	
	
	/**
	* Oznaci danou stranku
	*/
	select: function(name, disableAjax, disableHistory) {
		var page = this.pages[name];
		if(typeof page == 'undefined') return false; // Stranka neexistuje
		
		this.hideSelected();
		
		// Nastavime jako zvolenou
		this.active = name;
		
		// Zobrazime div
		page.show(disableAjax);
		
		if(Tabs) {
			var url = Tabs.getUrlParser(this.element);
			url.setMake('tabPanel-' + this.id2, name);
		}
		
		// Ulozime stav
		if(this.useCookie) {
			// Ukladame do cookies
			this.setCookie('tabPanel' + this.id2, name);
		} else if(HistoryParser) {
			// Ukladame do url
			HistoryParser.set('tabPanel' + this.id2, name);
		}
		
		// Save history
		if(!disableHistory) this.history.navigate( { page: name, ajax: page.ajax, link: page.link } );
		
		// Zavolame callback
		(this.onSelect || Prototype.emptyFunction)(name);
		
		// Fire event
		Element.fire(page.element, 'select');
		
		return page;
	},
	
	/**
	* Skryje aktualni
	*/
	hideSelected: function() {
		// Skryjeme
		if(this.active) {
			this.pages[this.active].hide();
		}
	},
	
	
	/**
	* Zrusi cely panel
	*/
	dispose: function() {
		this.element.tabPanel = null;
		this.element = null;
		$(this.tabRow).remove();
		
		// Zrusime stranky
		for(var name in this.pages) {
			this.pages[name].dispose();
			this.pages[name] = null;
		}
		this.pages = null;
	},

	
	/**
	* Nastavi cookies
	*/
	setCookie: function(name, value, days) {
		var expires = '';
		if(days) {
			var d = new Date();
			d.setTime( d.getTime() + nDays * 24 * 60 * 60 * 1000 );
			expires = '; expires=' + d.toGMTString();
		}
		
		// nastavime
		document.cookie = name + '=' + value + expires + '; path=' + this.cookiePath;
	},
	
	/**
	* Nacte cookies
	*/
	getCookie: function(name) {
		var re = new RegExp('(\;|^)[^;]*(' + name + ')\=([^;]*)(;|$)');
		var res = re.exec(document.cookie);
		return res != null ? res[3] : null;
	},
	
	/**
	* Smaze kolacek
	*/
	removeCookie: function(name) {
		this.setCookie(name, '', -1);
	}
});

/**
* Stranka panelu zalozek
* TODO: dodelat volani eventu
*/
TabPanel.Page = Class.create({
	/**
	* Pridani nove stranky
	*/
	initialize: function(tabPanel, el, params) {
		params = params || {};
	
		if(el.tagName == 'A') {
			params.nadpis = el.innerHTML;
			params.ajax = 1;
			params.link = el.href;
			
			this.a = el;
			
			el = new Element('div');
			this.a.insertAdjacentElement('afterEnd', el);
		}
		
		else {
			var d = el.down(0);
			if(d && (d.tagName == 'LEGEND' || d.tagName == 'H2')) {
				params.nadpis = d.innerHTML;
				d.remove();
			}
		}
		
	
		this.name = params.name;
		this.tabPanel = tabPanel;
		this.params = params;
		this.element = el;
		this.element.tabPage = this;
		this.index = tabPanel.pagesCnt;
		
		// Povoleni cache
		this.allowCache = params.cache || (Element.hasAttribute(el, 'cache') ? (parseFloat2(el.getAttribute('cache')) ? true : false) : tabPanel.defaultCache);
		
		// Styly
		this.element.addClassName('tabPanelContent');
		this.element.hide();
		
		// Nadpis
		this.nadpis = params.nadpis || this.element.getAttribute('nadpis');
		this.tab = new Element('li', { className: 'tab' } );
		if(this.a) {
			this.tab.innerHTML = this.a.innerHTML;
			this.a.remove();
		}
		else this.tab.update(this.nadpis);
		
		// Ajax
		this.ajax = params.ajax || parseFloat2(el.getAttribute('ajax'));
		if(this.ajax) {
			this.link = params.link || el.getAttribute('link');
			
			// Callbacky
			this.fceLoad = params.fceLoad || getCallback(el, 'fceLoad');
			this.fceOnload = params.onload || getCallback(el, 'onload');
			
			this.element.ajaxLoaded = false;
		} else if(Element.hasAttribute(el, 'link')) {
//			this.a.href = params.link || el.getAttribute('link');
//			this.a.target = params.target || el.getAttribute('target');
		}
		
		// Callback
		this.onShow = params.onshow || getCallback(el, 'onshow');
		this.onHide = params.onhide || getCallback(el, 'onhide');
		
		
		// Eventy
		var oThis = this;
		// this.tab.onmouseover = function(ev) { oThis.tabMouse.call(oThis, ev, 1); }; // STRAJK
		// this.tab.onmouseout = function(ev) { oThis.tabMouse.call(oThis, ev, 0); }; // STRAJK
		this.tab.onclick = function(ev) { oThis.select.call(oThis, ev); };
		this.tab.ondblclick = function(ev) { oThis.select.call(oThis, ev, true); };
	},
	
	getContent: function() {
		return this.element;
	},
	
	/**
	* Udalost pri prejeti mysi
	*/
	/* Strajk
	tabMouse: function(ev, show) {
		this.tab[show ? 'addClassName' : 'removeClassName']('hover');
	},
	*/
	
	/**
	* Kliknuti na zalozku
	*/
	select: function(ev, forceReload) {
		// Already having an request
		if(this.ajaxRequest) {
			if(!forceReload) return;
			else {
				// Cancel old request
				try { this.ajaxRequest.transport.abort(); } catch(e) {}
			}
		}
		
		// Nacteme pres ajax
		if(this.ajax && (!this.element.ajaxLoaded || !this.allowCache || forceReload)) this.element.ajaxLoaded = false;
		
		this.tabPanel.select(this.name);
	},
	
	/**
	* Nacteni zalozky pres ajax
	*/
	load: function() {
		if(!this.ajax || this.element.ajaxLoaded) return false;
		
		this.actualUrl = null;
		
		// Vlastni funkce
		if(typeof this.fceLoad == 'function') return this.fceLoad(this);
		else this._load(this.link);
	},
	
	_load: function(url) {
		//if(typeof this.element.ajaxLoaded != 'undefined' && !this.element.ajaxLoaded) return;
		// TODO: store request
		var el = this.element;
		new Ajax.Updater(el, url, {
			onComplete: function() {
				el.ajaxLoaded = true;
			}
		});
	},
	
	
	/**
	* Reloads tab page content
	*/
	refresh: function() {
		if(!this.ajax) return;
		
		this._load(this.actualUrl || this.link);
	},

	
	/**
	* Zobrazi danou stranku
	*/
	show: function(disableAjax) {
		if(!disableAjax && this.ajax && !this.element.ajaxLoaded) this.load();
		
		// Nastavime jako oznacene
		this.tab.addClassName('active');
		
		// Zobrazime div
		this.element.show();
		
		// Callback
		if(typeof this.onShow == 'function') this.onShow(this);
	},
	
	/**
	* Skova zalozku
	*/
	hide: function() {
		// Nastavime jako oznacene
		this.tab.removeClassName('active');
		
		// Zobrazime div
		this.element.hide();
		
		// Callback
		if(typeof this.onHide == 'function') this.onHide(this);
	},
	
	
	/**
	* Zrusi stranku a uvolni pamet
	*/
	dispose: function() {
		this.element.tabPage = null;
		this.element.remove();
		this.element = null;
		
		this.tab.remove();
		this.tab = null;
		this.a = null;
		
		this.tabPanel = null;
	}
});

/**
 * Finds TabPanel by any element in it
 */
function getTabPanel(el, skip) {
	var div = $(el).up('.tabPanel', skip);
	if(!div) return false;
	
	return div.tabPanel;
}

/**
 * Register live initialization of TabPanels
 */
document.onLive('tab-panel', function(container) {
	container.select('div.tabPanel').each(function(el) {
		if(!el.tabPanel) new TabPanel(el);
	} );
});

function disposeAllTabs() {
	var all = document.getElementsByTagName('*');
	
	for(var i = 0; i < all.length; i++) {
		var el = $(all[i]);
		
		if(el.tabPanel) {
			el.tabPanel.dispose();
			el.tabPanel = null;
		}
	}
}