
/**
* Panel with tabs
*/
var TabPanel = Class.create({
	// Which class to use
	tabPanelClassName: '',
	
	// Path used to store cookies
	cookiePath: '/',


	/**
	 * Creating new TabPanel: given container element
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
		this.defaultCache = Element.hasAttribute(el, 'cache') ? (parseInt(el.getAttribute('cache')) ? true : false) : true; // Zda muzeme cachovat
		
		// Store index attribute
		this.index2 = this.element.getAttribute('index2');
		this.id2 = this.id + (this.index2 ? ('-' + this.index2) : '');

		// Set-up classes
		this.element.addClassName(this.tabPanelClassName);
		this.element.addClassName('tabPanel');

		// Create list of tab-page headers
		this.tabRow = new Element('ol', { className: 'tabPanelTabs' } );
		this.element.insertBefore(this.tabRow, this.element.firstChild);
		
		// Use all sub-elements in container to create TabPages
		childs.each(this.addPage, this);
		
		// Select default page
		var selected = this.getDefaultPage();
		if(!selected) throw new Error("TabPanel " + this.id + " has no default page");
		this.select(selected, false, true);
		
		// Register history handler
		var page = this.pages[selected];
		//this.history = new HistoryStack( { page: selected, ajax: page.ajax, link: page.link } );
		//HistoryHandler.register(this.element, this.onHistory.bind(this));
	},

  /**
   * Get page element by it's name
   * @param string name
   * @reutrn TabPage
   */
	getPage: function(name) {
		return this.pages[name];
	},
	
	/**
	 * History move (not used now)
	 */
	onHistory____: function(ev, step, visible) {
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
	 * Find which TabPage is default one
   * @return string name or index
	 */
	getDefaultPage: function() {
		var ret;
		
		// Explicitly defined at TabPanel element
		if(this.element.hasAttribute('active')) {
			ret = this.element.getAttribute('active');
			if(typeof this.pages[ret] != 'undefined') return ret;
		}
		
		/*// Zadana pres URL
		if(Tabs) {
			var url = Tabs.getUrlParser(this.element);
			var tabIndex = url.get('tabPanel-' + this.id2);
			
			if(tabIndex) ret = tabIndex;
		} else {
			if(HistoryParser && (tabIndex = HistoryParser.get('tabPanel-' + this.id2))) ret = tabIndex;
		}
		*/
		
		// Cookies
		if(!ret && this.useCookie && (tabIndex = this.getCookie('tabPanel' + this.id2))) ret = tabIndex;
		
		// Find 'selected' attribute in TabPage's element
		for(var p in this.pages) {
			var x = this.pages[p].element.getAttribute('selected');
			if(x && parseInt(x)) return p;
		}
		
		// Or use default one: the first one
		if(ret && typeof this.pages[ret] != 'undefined') return ret;
		for(var i in this.pages) return i;
	},
	
	/**
	 * Adds new element to TabPanel and makes it a TabPage
	 */
	addPage: function(el, params) {
		if(el.tabPage) return false; // It's already a page
		
		var name;
		
		// Options not given -> try to look-up defaults
		if(typeof params != 'object') {
			if(el && (name = el.getAttribute('name')) && this.params.pages && this.params.pages[name]) params = this.params.pages[name];
			else params = {};
		}
		
		// Get page name
		this.pagesCnt++;
		if(!name) name = params.name || ('page-' + this.pagesCnt);
		
		// Error
		if(typeof this.pages[name] != 'undefined') {
			alert('Tab with given name already exists:' + name);
			return false;
		}
		
		params.name = name;
		
		// Instantiate new page
		var page = new TabPanel.Page(this, el, params);
		this.pages[name] = page;

		// Add tab into tab row
		this.tabRow.appendChild(page.tab);
		
		// Active?
		if(el.getAttribute('active') == '1') this.element.setAttribute('active', name);
	},
	
	
	/**
	 * Make a tab active
	 */
	select: function(name, disableAjax, disableHistory) {
		var page = this.pages[name];
		if(typeof page == 'undefined') return false; // Stranka neexistuje
		
		this.hideSelected();
		
		// Nastavime jako zvolenou
		this.active = name;
		
		// Zobrazime div
		page.show(disableAjax);
		
		/*if(Tabs) {
			var url = Tabs.getUrlParser(this.element);
			url.setMake('tabPanel-' + this.id2, name);
		}
		*/

		// Ulozime stav
		if(this.useCookie) {
			// Ukladame do cookies
			this.setCookie('tabPanel' + this.id2, name);
		}/* else if(HistoryParser) {
			// Ukladame do url
			HistoryParser.set('tabPanel' + this.id2, name);
		}*/
		
		// Save history
		//if(!disableHistory) this.history.navigate( { page: name, ajax: page.ajax, link: page.link } );
		
		// Zavolame callback
		(this.onSelect || Prototype.emptyFunction)(name);
		
		// Fire event
		Element.fire(page.element, 'tabPanel:select');
		
		return page;
	},
	
	/**
	 * Hide actual page
	 */
	hideSelected: function() {
		if(this.active) {
			this.pages[this.active].hide();
		}
	},

	/**
	 * Destroy's whole TabPanel
	 */
	dispose: function() {
		this.element.tabPanel = null;
		this.element = null;
		$(this.tabRow).remove();
		
		// Destroy pages
		for(var name in this.pages) {
			this.pages[name].dispose();
			this.pages[name] = null;
		}
		this.pages = null;
	},

	/**
	 * Set a cookies
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
	 * Get a cookie
	 */
	getCookie: function(name) {
		var re = new RegExp('(\;|^)[^;]*(' + name + ')\=([^;]*)(;|$)');
		var res = re.exec(document.cookie);
		return res != null ? res[3] : null;
	},
	
	/**
	 * Remove a cookie
	 */
	removeCookie: function(name) {
		this.setCookie(name, '', -1);
	}
});


/**
* One page of TabPanel
* TODO: dodelat volani eventu
*/
TabPanel.Page = Class.create({
	/**
	 * Create new TabPage
   * @param TabPanel tabPanel in which is this page created
   * @param Element el element containing this tab
   * @param object options Options
	 */
	initialize: function(tabPanel, el, options) {
		options = options || {};
	
		if(el.tagName == 'A') {
			options.title = el.innerHTML;
			options.ajax = 1;
			options.link = el.href;
			
			this.a = el;
			
			el = new Element('div');
			this.a.insertAdjacentElement('afterEnd', el);
		}
		
		else {
			var d = el.down(0);
			if(d && (d.tagName == 'LEGEND' || d.tagName.match(/^h\d$/i))) {
				options.title = d.innerHTML;
				d.remove();
			}
		}
		
	
		this.name = options.name;
		this.tabPanel = tabPanel;
		this.params = options;
		this.element = el;
		this.element.tabPage = this;
		this.index = tabPanel.pagesCnt;
		
		// Povoleni cache
		this.allowCache = options.cache || (Element.hasAttribute(el, 'cache') ? (parseInt(el.getAttribute('cache')) ? true : false) : tabPanel.defaultCache);
		
		// Styly
		this.element.addClassName('tabPanelContent');
		this.element.hide();
		
		// Nadpis
		this.title = options.title || this.element.getAttribute('title');
		this.tab = new Element('li', { className: 'tab' } );
		if(this.a) {
			this.tab.innerHTML = this.a.innerHTML;
			this.a.remove();
		}
		else this.tab.update(this.title);
		
		// Ajax
		this.ajax = options.ajax || parseInt(el.getAttribute('ajax'));
		if(this.ajax) {
			this.link = options.link || el.getAttribute('link');
			
			// Callbacky
			this.fceLoad = options.fceLoad || Scope.getCallback(el, 'fceLoad');
			this.fceOnload = options.onload || Scope.getCallback(el, 'onload');
			
			this.element.ajaxLoaded = false;
		} else if(Element.hasAttribute(el, 'link')) {
//			this.a.href = params.link || el.getAttribute('link');
//			this.a.target = params.target || el.getAttribute('target');
		}
		
		// Callback
		this.onShow = options.onshow || Scope.getCallback(el, 'onshow');
		this.onHide = options.onhide || Scope.getCallback(el, 'onhide');
		
		
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
