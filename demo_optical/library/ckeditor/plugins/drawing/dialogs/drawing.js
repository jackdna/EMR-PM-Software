CKEDITOR.dialog.add( 'drawing', function( editor )
{ 	 template_id = CKEDITOR.template_id;
	return {
		title : 'Drawing',
		minWidth : 900,
		minHeight : dw_height,
		contents :
		[
			{
				id : 'drawing',
				label : 'Drawing',
				elements :
				[
				 	{
						type : 'html',
						html : '<iframe src="../drawing.php?flag=1" style="width:900px;height:'+dw_height+'px;" id="frameDrawing"></iframe>'
					}
				]
			}
		],
		buttons:[{
		   type:'button',
		   id : 'submit',
			label : 'Submit',
			title : 'Submit',
		   onClick: function(){
			  addCode(); //function for adding time to the source
		   }
		  }, CKEDITOR.dialog.cancelButton],
		onShow: function(){
			this.move(200,0);
			template_id = CKEDITOR.template_id;
			html = CKEDITOR.instances['FCKeditor1'].getData();
			document.getElementById('frameDrawing').src = '../drawing.php?flag=1';
			//frameDrawing.set_frame_height();
		},
		onCancel: function(){
			frameDrawing.reset_frame_height();
			CKEDITOR.dialog.getCurrent().hide()
		},	
	};
	function addCode(){
		frameDrawing.saveCanvas('frmDrawing');
		var myEditor = CKEDITOR.instances.FCKeditor1;
		frameDrawing.reset_frame_height();
		CKEDITOR.dialog.getCurrent().hide();
	 };
});