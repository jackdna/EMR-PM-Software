<script>
	<?php if($content == 'Modifiers'){?>
	$(function(){
			
			jQuery.extend({
				getValues: function(url) {
						var result = null;
						$.ajax({
								url: url,
								type: 'get',
								dataType: 'json',
								async: false,
								success: function(data) {
									result = data;
								}
						});
						return result;
				}
			});
			
			var Modifiers = $.getValues("../iDocModifiers.php?val="+Math.random());
			
			$("body").on('click focus keyup', '.typeHead',function(e){
				
				if(e.type == 'click' || e.type == 'focusin' || e.type == 'focus') { return ; }
				var $_this	=	$(this);
				var RowId	=	$_this.attr('data-row-index');
				var ContObj	=	$("#preDefineModifiers");
				var Typo	=	$_this.val().toLowerCase();
				Typo		=	Typo.replace(/[^0-9a-z]/gi, '')
				
				if(Typo.length == 0 ) { 
					if(ContObj.hasClass('active')) {
						ContObj.removeClass('active').hide(50);
					}
					return; 
				} 
				
				var List	=	Modifiers;
				var FirstLi	=	$("#preDefineModifiers li:first");
				var SelLi	=	$("#preDefineModifiers li.scrollSelected");
				var SelIndex=	$("#preDefineModifiers li").index(SelLi);
				
				if(!ContObj.hasClass('active'))
				{
					ContObj.addClass('active')
					var Top		=	setVerticalPosition($_this,$(document),ContObj);// $_this.offset().top;
					var Left	=	setHorizontalPosition($_this,$(document),ContObj) ;
				}
				
				if(e.which === 38)	
				{	
					if(SelIndex > 0 )	
					{
						SelLi.removeClass()
						SelLi.prev().addClass('scrollSelected');	
					}
				} 
				else if(e.which === 40)	
				{
					if(SelIndex < ($("#preDefineModifiers li").length -1))
					{	  
						SelLi.removeClass();
						SelLi.next().addClass('scrollSelected');
					}
				}
				else if(e.which === 13)	
				{
					$("#preDefineModifiers li.scrollSelected").trigger('click');
					$("#preDefineModifiers").removeClass('active').hide(50);
					$("#preDefineModifiers #HtmlContainer").html('');
					$_this.focus(true);
					
				}
				else if(e.which === 27)	
				{
					ContObj.removeClass('active').hide(50);
					$("#preDefineModifiers #HtmlContainer").html('');
					$(this).focus(true);
					
				}
				else
				{
					$("#preDefineModifiers #HtmlContainer").html('');
					var Html = '';
					var counter = 0;
					$.each(List, function(i,v){
						counter++;
						
						var elemM = v['modifier_code']; 
						var elemP = v['mod_prac_code'];
						var elemD = v['mod_description'];
						if (	(elemM.toLowerCase().indexOf(Typo.toLowerCase()) != -1)
							||	(elemP.toLowerCase().indexOf(Typo.toLowerCase()) != -1)
							||	(elemD.toLowerCase().indexOf(Typo.toLowerCase()) != -1)
						   ) 
						
						{
							Html += '<li tabindex="'+RowId+'" data-row-index="'+RowId+'" data-mod-val="'+elemM+'" data-prac-val="'+elemP+'" data-description="'+elemD+'" class="hoverdiv" style="padding:5px; border-bottom:1px solid #ccc; font-weight:bold; " >';
							Html += '<span style="width:100%">';
							Html += '<span style="width:25%">'+elemM+'</span>';
							Html += '<span style="width:25%; margin-left:2%">'+elemP+'</span>';
							Html += '<span style="width:45%; margin-left:2%">'+elemD+'</span>';
							Html += '</span>';
							Html += '</li>';
							
							
						}
						
					});
					if(!Html)
					{
						Html = 	'<li tabindex="'+RowId+'" data-row-index="'+RowId+'" data-mod-val="" data-prac-val="" data-description="" class="hoverdiv" style="padding:5px; border-bottom:1px solid #ccc; font-weight:bold; " >No Result Match</li>';	
					}
					$("#preDefineModifiers #HtmlContainer").append(Html);
					ContObj.css({'left' : Left + 'px', 'top' : Top + 'px'}).addClass('active').fadeIn(50);	
					if(Html)
					{
						$("#preDefineModifiers li:first").addClass('scrollSelected');
					}
					
				}
				
			});
			
			$("body").on("mouseenter", "#preDefineModifiers", function(){ 
				$("#hiddPopUpField").val("PopUpEnable");	
			});
			
			$("body").on("mouseleave","#preDefineModifiers", function(){
				if($("#hiddPopUpField").val() == 'PopUpEnable' )
				{
					$(this).fadeOut(100);
					$("#hiddPopUpField").val('');
				}
			});
			
			$('body').on('click focus','body, select,input',function(){
				$("#preDefineModifiers").removeClass('active').fadeOut(100);	
				$("#preDefineModifiers #HtmlContainer").html('');	
			});
			
			$("body").on("click", "#preDefineModifiers li", function(){ 
				var li 	=	$("#preDefineModifiers li.scrollSelected");
				var RowId=	$(this).attr('data-row-index');
				var Mod =	$(this).attr('data-mod-val');
				var Prac=	$(this).attr('data-prac-val');
				var Desc=	$(this).attr('data-description');
				
				li.removeClass()
				$(this).addClass('scrollSelected');
				
				$("#practice"+RowId+"").val(Prac)
				$("#description"+RowId+"").val(Desc)
				$("#modifier"+RowId+"").val(Mod).focus().trigger('blur');
				
				$("#preDefineModifiers").removeClass('active').hide(50);	
				$("#preDefineModifiers #HtmlContainer").html('');	
				
			});
			
			
	});
<?php }?>
</script>
<form name="perDefineModifierFrm" action="predefineFrmForm.php" method="post">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
		
         <div class="scheduler_table_Complete">
         	<div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
            	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                	<thead>
                    	<tr>
                    		<th style="width:2%">&nbsp;</th>
                            <th style="width:2%">&nbsp;</th>
                            <th style="width:32%" align="left" class="">Modifier Code</th>
                            <th style="width:32%" align="left" class="">Practice Code</th>
                            <th style="width:32%" align="left" class="">Description</th>
                    	</tr>
       </thead>
       <tbody>
       <?php
	   	$getDetails = $objManageData->getArrayRecords('modifiers','' ,'' ,'modifierCode','ASC');
		$counter = 0;
		if(count($getDetails)>0)
		{
			foreach($getDetails as $key => $detailsPreDefine)
			{ $counter++;

				$modifierId		=	$detailsPreDefine->modifierId;
				$modifierCode	=	$detailsPreDefine->modifierCode;
				$practiceCode	=	$detailsPreDefine->practiceCode;
				$description	=	$detailsPreDefine->description;
				$deleted		=	$detailsPreDefine->deleted;
				
		?>
        		<input type="hidden" name="modifierId[]" value="<?php echo $modifierId; ?>" />
                <tr>
                	<td class="<?=($deleted == 1 ? 'inactive-record' : 'active-record')?>" data-record-id = "<?=$modifierId?>" data-table-name="<?=$table?>" data-unique-field="modifierId" >&nbsp;</td>
                    <td align="left">
                    	<input type="checkbox" name="chkBox[]" value="<?php echo $modifierId; ?>">
                   	</td>
                    <td align="left">
                    	<input type="text" class="form-control typeHead" name="modifierCode[]" id="modifier<?=$counter?>" data-row-index="<?=$counter?>" value="<?php echo stripslashes($modifierCode); ?>" />
                    </td>
                    <td align="left">
                    	<input type="text" class="form-control" name="practiceCode[]" id="practice<?=$counter?>" data-row-index="<?=$counter?>" value="<?php echo stripslashes($practiceCode); ?>" />
                    </td>
                    <td align="left">
                    	<input type="text" class="form-control" name="description[]" id="description<?=$counter?>" data-row-index="<?=$counter?>" value="<?php echo stripslashes($description); ?>" />
                  	</td>
			</tr>
			<?php
			}
		}
		?>
		</tbody>
        </table>
     </div>
     </div>           
            
        <?php
        for($j=0;$j<1;$j++){
			$counter++;
            ?>
              <div class="modal fade" id="modifierTr">
             <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title rob">Add Modifiers</h4>  
                    </div>
                    <div class="modal-body">
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf  table-striped">
                            <tr>
                             	<td align="left">
                                	<input type="text" class="form-control typeHead" name="modifierCode[]" id="modifier<?=$counter?>" data-row-index="<?=$counter?>"  />
                              	</td>
                                <td align="left">
                                	<input type="text" class="form-control" name="practiceCode[]" id="practice<?=$counter?>" data-row-index="<?=$counter?>" />
                               	</td>
                                <td align="left">
                                	<input type="text" class="form-control" name="description[]" id="description<?=$counter?>" data-row-index="<?=$counter?>"  />
                               	</td>
                         	</tr>
                            <tr>
                                <td >Modifier Code</td>
                                <td >Practice Code</td>
                                <td >Description</td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                    <div id="div_innr_btn">
                        
                        <a href="javascript:void(0)" class="btn btn-info " id="saveButtonSub" style="display: inline-block;" onclick="return top.frames[0].getPageSrc('Save');"><b class="fa fa-save"></b> Save</a>
                        <a href="javascript:void(0)" class="btn btn-danger " id="cancelButton" style="display: inline-block;" data-dismiss="modal"><b class="fa fa-times"></b> Close</a>
                         
                    </div>
                    </div>
                 
                </div>
             </div>
            </div>
           		<script>
                  function closeModal()
                  {
                  top.frames[0].frames[0].frames[0].$('#modifierTr').modal({
                            show: false,
                            backdrop: true,
                            keyboard: true
                            });
                  }	</script>	
            <?php
        }
        ?>
    
		<div id="preDefineModifiers" style="z-index:9999" class="col-md-4 col-lg-3 col-xs-4 col-sm-4 preDefinePopUp padding_0 active">
        	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Modifiers<span onClick="document.getElementById('preDefineModifiers').style.display='none';" style="float:right; color:#FFF; cursor:pointer; list-style:none; ">X</span></div>
            <ul class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="HtmlContainer" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"></ul>
      	</div>
						
		</form>