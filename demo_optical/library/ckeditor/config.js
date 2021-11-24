/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

/*CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};
*/
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = 'Custom'; //makes all editors use this toolbar
	/*config.toolbar_Custom =
	 [
	  { name: 'drawing', items : [ 'drawing' ] }
	 ];*/
	
};

CKEDITOR.editorConfig = function( config ) {
    config.allowedContent = true;  

    // The toolbar groups arrangement, optimized for a single toolbar row.
   /* config.toolbarGroups = [
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'links' },
        { name: 'insert' },
        { name: 'styles' },
        { name: 'colors' },
    ];*/
	
    // The default plugins included in the basic setup define some buttons that
    // we don't want too have in a basic editor. We remove them here.
    config.removeButtons = 'Cut,Copy,Paste,Undo,Redo,Anchor,Underline,Strike,About,Others,Forms';

    // Let's have it basic on dialogs as well.
    config.removeDialogTabs = 'link:advanced';
	config.toolbarStartupExpanded = false;
	config.toolbarCanCollapse   = true;
	
	//config.extraPlugins = 'imagebrowser';
};
