CKEDITOR.plugins.add('drawing',   //name of our plugin
{    
      requires: ['dialog'], //requires a dialog window
   	  init:function(a) {
		  var b="drawing";
		  var c=a.addCommand(b,new CKEDITOR.dialogCommand(b));
		  c.modes={wysiwyg:1,source:1}; //Enable our plugin in both modes
		  c.canUndo=true;
		
		  //add new button to the editor
		  a.ui.addButton("drawing",
		  {
		   label:'Drawing',
		   command:b,
		   icon:this.path+"images/anchor.png"
		  });
		  CKEDITOR.dialog.add(b,this.path+"dialogs/drawing.js") //path of our dialog file
	 }
});