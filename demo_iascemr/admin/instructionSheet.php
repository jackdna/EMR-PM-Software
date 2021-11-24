<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Instruction Sheet</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
include("adminLinkfile.php");
?>
<script>
function delTemplateFn(id){
	var ask = confirm("Are you sure to delete the template.");
	if(ask==true){
		top.frames[0].frames[0].location.href = 'instructionSheet.php?delRecordId='+id;
	}
}
function delSelectedRecords(){
}	

function open_div(obj){
	if(obj){
		$('#open_div_sample').modal({
		show: true,
		backdrop: true,
		keyboard: true
		});
	}
	else {
		$('#open_div_sample').modal({
		show: false,
		backdrop: true,
		keyboard: true
		});
	}
}

/*$(window).load(function()
{
	var LDL	=	function()
	{
		var H	=	parent.top.$("#div_middle").height()- top.frames[0].$("#div_innr_btn").outerHeight();
		H=H-($(".subtracting-head").height()+100);
		var height_custom_scroll_new=	top.frames[0].frames[0].$('.sidebar-wrap-op ul');
		height_custom_scroll_new.css({ 'min-height' : H , 'max-height': H});
	}
	LDL();
	$(window).resize(function(e) {
	   LDL();
	});
});*/

var ORLoad	=	function()
{
		
		var WH	=	$(window).height();
		var SH		=	$(".head_scheduler").outerHeight(true);
		var HH		=	$(".header_side").outerHeight(true);
		
		
		var AH		=	WH	-	(SH + HH);
		//alert(AH);
		$("ul.list-group").css({'overflow':'hidden', 'overflow-y':'auto', 'min-height':AH+'px', 'max-height':AH+'px'});
		
		var TH		=		$(".template_wrap").outerHeight(true);
		var FH		=		$("#fieldSample").outerHeight(true);
		var EH		=		30; 			// Extra Height;
		var RH		=		WH - ( SH + TH + FH + EH )  ;	
		console.log('Textbox Height'+RH)
		$(".op_right_main").css({ 'overflow' : 'hidden' , 'min-height' : RH+'px' , 'height' : RH+'px' , 'max-height' : (RH+EH)+'px' } );
		
		$("#fieldSample").css({'margin-top':EH+'px' } );
};
$(window).load(function() 	{  ORLoad(); });
$(window).resize(function() 	{  ORLoad(); });

</script>

</head>
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
$templateId = $_REQUEST['template'];

//CODE TO DISPLAY CANCEL BUTTON
if($templateId<>"") {
	echo "<script>top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';</script>";
}
//END CODE TO DISPLAY CANCEL BUTTON

$instructionId = $_REQUEST['instruction'];
if($_REQUEST['sbtInstruction']){
	$tmplateData = $_REQUEST['editor1'];
	$templateid = $_REQUEST['templateid'];
	$arrayRecord['instruction_name'] = addslashes($_REQUEST['instruction_name']);
	$arrayRecord['instruction_desc'] = addslashes($tmplateData);
	if($templateid){
		$c=$objManageData->UpdateRecord($arrayRecord, 'instruction_template', 'instruction_id', $templateid);
	}else{
		$d=$objManageData->addRecords($arrayRecord, 'instruction_template');
	}
	if($c && !$rec_del)
	{
		echo "<script>top.frames[0].alert_msg('update')</script>";
	}
	if($d)
	{
		echo "<script>top.frames[0].alert_msg('success')</script>";
	}
}
//DELETE SELECTED TEMPLATE
	if($_POST['chkBox']){
		$delChkBoxes = $_POST['chkBox'];
		if(is_array($delChkBoxes)){
			$counter=0;
			foreach($delChkBoxes as $OptemplateId){
				$rec_del=$objManageData->delRecord('instruction_template', 'instruction_id', $OptemplateId);
				if($rec_del)$counter++;
			}
			if($rec_del)
			{
				echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
			}
		}
	}
//DELETE SELECTED TEMPLATE


$templateDetails = $objManageData->getRowRecord('instruction_template', 'instruction_id', $templateId);
$data = stripslashes($templateDetails->instruction_desc);
$insName = stripslashes($templateDetails->instruction_name);
?>



<body>
<div class="modal fade" id="open_div_sample">
     <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob"> <!--title here--> </h4>  
            </div>
            <div class="modal-body">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                <tr>
				<td>Patient First Name : </td>
				<td>{PATIENT FIRST NAME}</td>
				<td>Middle Name : </td>
				<td>{MIDDLE INITIAL}</td>
			</tr>
			<tr>
				<td>Last Name : </td>
				<td>{LAST NAME}</td>
				<td>Date of Birth : </td>
				<td>{DOB}</td>
				
			</tr>
			<tr>
				<td>Date of Surgery : </td>
				<td>{DOS}</td>
				<td>Surgeon Name : </td>
				<td>{SURGEON NAME}</td>
			</tr>
			<tr>
				<td>Patient Site : </td>
				<td>{SITE}</td>
				<td>Primary Procedure : </td>
				<td>{PROCEDURE}</td>
			</tr>
		<tr>
			<td>Secondary Procedure : </td>
			<td>{SECONDARY PROCEDURE}</td>
			<td>Tertiary Procedure : </td>
			<td>{TERTIARY PROCEDURE}</td>
		</tr>
		<tr>
        	<td>Pre-op diagnosis : </td>
			<td>{PRE-OP DIAGNOSIS}</td>	
			<td>Post-op diagnosis : </td>
			<td>{POST-OP DIAGNOSIS}</td>
		</tr>
		<tr>
			<td>Very Small Text Box : </td>
			<td>{TEXTBOX_XSMALL}</td>
			 
			<td>Small Text Box : </td>
			<td>{TEXTBOX_SMALL}</td>
			
		</tr>
		<tr >
			<td>Large Text Box : </td>
			<td>{TEXTBOX_MEDIUM}</td>
			 
			<td>Very Large Text Box : </td>
			<td>{TEXTBOX_LARGE}</td>
			
		</tr>
		<tr>
			<td>Surgeon's Signature : </td>
			<td>{Surgeon's Signature}</td>
			 
			<td>Nurse's Signature : </td>
			<td>{Nurse's Signature}</td>
			
		</tr>
		<tr >
			<td>Witness Signature : </td>
			<td>{Witness Signature}</td>
			<td>Signature : </td>
			<td>{SIGNATURE}</td>
		</tr>	
        <tr>
    		<td>DATE : </td>
			<td>{DATE}</td>
            <td>ASC Name</td>
            <td>{ASC NAME}</td>
        </tr>
        <tr>
            <td>ASC Address : </td>
            <td>{ASC ADDRESS}</td>
            <td>ASC Phone</td>
            <td>{ASC PHONE}</td>
        </tr>
        <tr>
			<td>Patient Id</td>
			<td>{PATIENT ID}</td>
            <td>Arrival Time</td>
            <td>{ARRIVAL TIME}</td>
        </tr>
        
                </table>
            </div>
         </div>
     </div>
 </div>	
		


	<form name="frmInstruction" action="instructionSheet.php" method="post">
	<input type="hidden" name="templateid" value="<?php echo $templateId; ?>">
	<input type="hidden" name="sbtInstruction" value="">
    
    <div class="inner_surg_middle ">
					
                  
<div style="" id="" class="all_content1_slider ">	         
 <div class="head_scheduler new_head_slider padding_head_adjust_admin">
        <span>
            Instruct Sheet
            </span>
        </div>
      <div class="wrap_inside_admin">
        <div class="col-lg-3 col-sm-4 col-xs-12 col-md-3" onMouseOver="open_div(false);">
            <div class="sidebar-wrap-op">
            <a href="instructionSheet.php?template=<?php echo $list->instruction_id; ?>" class="header_side"><?php echo $list->instruction_name; ?>Instruction Sheet Templates</a>
               <ul class="list-group">
               <?php
					$templateLists = $objManageData->getArrayRecords('instruction_template','','', 'instruction_name', 'ASC');
					if($templateLists){
						foreach($templateLists as $key => $list){
							++$seq;
							?>
                            <a href="instructionSheet.php?template=<?php echo $list->instruction_id; ?>" class="list-group-item border-bb">
                            <label> <input type="checkbox" name="chkBox[]" value="<?php echo $list->instruction_id; ?>"> </label> 
							  <?php echo stripslashes($list->instruction_name); ?>
                            </a>
                           <?php
						}
					}
					?>
                          
                           
                </ul>	
            </div>
           </div>
           <div class="clearfix visible-xs margin_adjustment_only"></div>
            <div class="col-lg-9 col-sm-8 col-xs-12 col-md-9"> 
            <div class="template_wrap">
                <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                    <label for="t_name"> Template Name</label>
                </div>
                <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
                <?php
				if(!$surgeonId) {
				$surgeonId = $_REQUEST['surgeonId'];
				}
				?>
				<input type="hidden" name="surgeonId" value="<?php echo $surgeonId; ?>">
                     <input type="text" name="instruction_name" value="<?php echo stripslashes($templateDetails->instruction_name); ?>" class="form-control" id="instruction_name">
                </div>
                <div class="clearfix margin_adjustment_only"></div>
                <div class="clearfix margin_adjustment_only border-dashed"></div>
                <div class="clearfix margin_adjustment_only"></div>
            </div>
            <div class="op_right_main">
                <textarea name="editor1" id="editor1" rows="" cols=""><?php echo stripslashes($data);?> </textarea>
				<script>
                    // Replace the <textarea id="editor1"> with a CKEditor
                    // instance, using default configuration.
                    CKEDITOR.replace( 'editor1' ,
					{ on :
								{
										// Maximize the editor on start-up.
										'instanceReady' : function( evt )
										{
												//evt.editor.execCommand( 'maximize' );
												//evt.editor.resize("200", editorElem.clientHeight);
												evt.editor.resize($(".op_right_main").width(),$(".op_right_main").height());
										}
								} 
   					});
                </script>
            </div>
           <div>*Fields marked in parenthesis {} Will change. <a href="javascript:void(0);" onClick="open_div(true);" class="black" style="color:#800080;"><b>Click here for example</b></a></div>
         </div>	         
      </div>		
 </div>
</div>
	
	</form>
</body>
</html>