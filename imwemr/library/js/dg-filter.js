/************************************************************************************************************
DG Filter
Copyright (C) 2009  DTHMLGoodies.com, Alf Magne Kalleland

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

Dhtmlgoodies.com., hereby disclaims all copyright interest in this script
written by Alf Magne Kalleland.

Alf Magne Kalleland, 2009
Owner of DHTMLgoodies.com

************************************************************************************************************/

if(!window.DG){
	window.DG = {};
}

DG.Filter = new Class({
	Extends : Events,
	xPathFilterElements : false,	/** Css class for text elements to be indexed */
	html :  {
		filterField : null,	/** Reference to text field */
		filterEl : null		/** Reference to element to be filtered */
	},
	filterElType : null,
	searchIndex : null,
	onMatchShowChildren : false,
	idCounter : 0,
	matchedNodes : null,			/** Temporary array of matched nodes */
	txtNoMatchFound : '',			/** No matches found text */
    colIndexes : null,
	frameName: null,
	/**
	 * Constructor
	 * @param {Object} config
	 */
	initialize : function(config) {
		this.html.filterField = config.filterField;
		this.html.filterEl = config.filterEl;
		
		this.xPathFilterElements = config.xPathFilterElements;

        if(config.colIndexes){
            this.colIndexes = config.colIndexes;
        }

		this.filterElType = this.html.filterEl.tagName.toLowerCase() == 'table' ? 'table' : 'list';
		this.onMatchShowChildren = config.onMatchShowChildren;
		this.txtNoMatchFound = config.txtNoMatchFound || 'No matches found';
		this.listeners = config.listeners || null;
		
		this.frameName = config.frameName;

		this.addFilterEvents();
		this.createSearchIndex();

		this._createNoMatchFoundElement();

	},

	_setNewNodeId : function(node) {
		node.setProperty('id', 'dgfilter' + this.idCounter);
		this.idCounter++;

	},
	/**
	 * Create search index - makes the search work faster
	 */
	createSearchIndex : function() {

		if(this.filterElType == 'list') {
			var elements = this.html.filterEl.getElements('li');
		}else{
			var elements = this.html.filterEl.getElements('tbody')[0].getElements('tr');
		}

		//var textProperty = document.body.innerText ? 'innerText' : 'textContent';
		var textProperty = 'textContent';
		this.searchIndex = [];

		for(var i=0; i< elements.length; i++) {
			if(!elements[i].get('id')) {
				this._setNewNodeId(elements[i]);
			}
			var textContent = '';
			var firstParent = null;
            var branchText = elements[i][textProperty].toLowerCase();

			if(this.xPathFilterElements) {
				var textElements = this.filterElType == 'list' ? [elements[i].getElements(this.xPathFilterElements)[0]] : elements[i].getElements(this.xPathFilterElements);
				for(var j=0;j<textElements.length;j++) {
					textContent = textContent + textElements[j][textProperty];
				}
			}
            if(this.filterElType != 'list' && this.colIndexes){
                branchText='';
                for(var j=0;j<this.colIndexes.length;j++){
                    branchText = branchText + elements[i].cells[this.colIndexes[j]][textProperty];
                }
                branchText = branchText.toLowerCase();
            }
			this.searchIndex.push({
				itemText : textContent.toLowerCase(),
				branchText : branchText,
				el : elements[i],
				parentEl : (this.filterElType == 'list' && i>0) ? elements[i].getParent('li') : null
			});
		}


	},

	addFilterEvents : function() {
		this.html.filterField.addEvent('keyup', this.filter.bind(this));
		this.html.filterField.addEvent('blur', this.filter.bind(this));
		this.html.filterField.addEvent('paste', this.filter.bind(this));
		this.addEvents(this.listeners);
	},

	_showNode : function(node) {
		node.el.setStyle('display', '');

	},
	_hideNode : function(node) {
		node.el.setStyle('display', 'none');
	},

	filter : function() {
		this.fireEvent('beforefilter', this);
		this.matchedNodes = {};
		if(!this.searchIndex) {
			this.createSearchIndex();
		}
		var searchPhrase = this.html.filterField.get('value').toLowerCase();
		if (this.filterElType == 'list') {
			var matchesFound = this.filterList(searchPhrase);
		}
		else {
			var matchesFound = this.filterTable(searchPhrase);
		}

		if(!matchesFound) {
			this._showNoMatchFoundElement();
		}else{
			this._hideNoMatchFoundText();
		}

		this.fireEvent('afterfilter', this);
	},

	_matchAbove : function(node) {
		return this.matchedNodes[node.el.id];
	},

	_nodeMatch : function(indexNode, searchPhrase) {
		return indexNode.branchText.indexOf(searchPhrase) >=0;
	},

	storeNodeMatch : function(indexNode, searchPhrase) {
		if(!this.onMatchShowChildren) {
			return;
		}
		var match = indexNode.itemText.indexOf(searchPhrase) >=0;
		if(!match && indexNode.parentEl) {
			match = this.matchedNodes[indexNode.parentEl.id];
		}
		this.matchedNodes[indexNode.el.id] = match;
	},
	_getIdOfNoMatchFoundElement : function() {
		return this.html.filterEl.id + 'noMatchFound';
	},
	_hideNoMatchFoundText : function() {
		var el = $(this._getIdOfNoMatchFoundElement());
		if(el && el.length > 0 ) {
			el.setStyle('display', 'none');
		}
	},

	_showNoMatchFoundElement : function() {
		var el = $(this._getIdOfNoMatchFoundElement());
		if(el && el.length > 0 ) {
			el.setStyle('display', '');
		}
	},
	_createNoMatchFoundElement : function() {
		var el = new fmain.Element('div');
		el.id = this._getIdOfNoMatchFoundElement();
		el.inject(this.html.filterEl, 'after');
		el.set('html', this.txtNoMatchFound);
		el.setStyle('display','none')
	},

	filterList : function(searchPhrase) {
		var matchesFound = false;
		for(var i=0;i<this.searchIndex.length;i++) {
			this.storeNodeMatch(this.searchIndex[i], searchPhrase)

			if(this._nodeMatch(this.searchIndex[i], searchPhrase) || this._matchAbove(this.searchIndex[i])) {
				matchesFound = true;
				this._showNode(this.searchIndex[i]);
			}else{
				this._hideNode(this.searchIndex[i]);
			}
		}

		return matchesFound;
	},

	filterTable : function(searchPhrase) {
		var matchesFound = false;
		for (var i = 0; i < this.searchIndex.length; i++) {
			if(this._nodeMatch(this.searchIndex[i], searchPhrase)) {
				matchesFound = true;
				this._showNode(this.searchIndex[i]);
			}else{
				this._hideNode(this.searchIndex[i]);
			}
		}
		return matchesFound;
	}


});