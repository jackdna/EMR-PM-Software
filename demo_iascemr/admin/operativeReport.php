<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?>
<?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php"); 
?>
<!DOCTYPE html>
<html>
<head>
<title>Operative Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("adminLinkfile.php");?><script src="../js/jscript.js"></script>

<style>
	div.div_display{
		display:none;
		position:absolute;
		top:68px;
		left:60px;
		background:#CCCCCC;
	}
	.header_side_local
	{
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;	
	}
</style>
<script>
function changeRefresh(val){
	location.href='operativeReport.php?surgeonId='+val;
}
top.frames[0].document.frameSrc.source.value = 'operativeReport.php';	

top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
top.frames[0].document.getElementById('deleteSelected').style.display = 'inline-block';
top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';

	top.frames[0].document.getElementById('CopyToCommunity').style.display = 'none';
	top.frames[0].document.getElementById('CopyFromCommunity').style.display = 'none';
<?php 

	if($_REQUEST['surgeonId']==0 && $_REQUEST['template']){?>
	top.frames[0].document.getElementById('CopyFromCommunity').style.display = 'inline-block';	
	top.frames[0].document.getElementById('CopyToCommunity').style.display = 'none';
	<?php }elseif($_REQUEST['surgeonId']){?>
	top.frames[0].document.getElementById('CopyToCommunity').style.display = 'inline-block';
	top.frames[0].document.getElementById('CopyFromCommunity').style.display = 'none';
	<?php }?>
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

function open_div_surgeon(obj){
	if(obj){
		$('#open_div_surgeon').modal({
		show: true,
		backdrop: true,
		keyboard: true
		});
	}
	else {
		$('#open_div_surgeon').modal({
		show: false,
		backdrop: true,
		keyboard: true
		});
	}
}

function copyCommunityToSurgeon()
{

	var selSurId=$('#copyToSurgeon').val();
	if(selSurId)
	{
		$("#surgeonIdCopied").val(selSurId);
		$('#act').val('CopyFromCommunity');
		frmOperativeReport.submit();
		/*var ask = confirm('Are you sure to copy template');
		if(ask==true){
			
			$('#act').val('CopyFromCommunity');
			frmOperativeReport.submit();
		}*/
	}	
	else
	{
		alert('Please select surgeon to copy template')	
	}
}
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
		var EH		=		10; 			// Extra Height;
		var RH		=		WH - ( SH + TH + FH + EH )  ;	
		//console.log('Textbox Height'+RH)
		$(".op_right_main").css({ 'overflow' : 'hidden' , 'min-height' : RH+'px' , 'height' : RH+'px' , 'max-height' : (RH+EH)+'px' } );
		
		$("#fieldSample").css({'margin-top':EH+'px' } );
};
$(window).load(function() 	{  ORLoad(); });
$(window).resize(function() 	{  ORLoad(); });
</script>
</head>
<body>
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
$templateId = $_REQUEST['template'];
if($_REQUEST['sbtTemplate']){
	$tmplateData = $_REQUEST['editor1'];
	$templateid = $_REQUEST['templateid'];	
	$arrayRecord['surgeonId'] = $_REQUEST['surgeonId'];
	$arrayRecord['template_name'] = addslashes($_REQUEST['template_name']);
	$arrayRecord['template_data'] = addslashes($tmplateData);
	if($templateid){
		$c=$objManageData->UpdateRecord($arrayRecord, 'operative_template', 'template_id', $templateid);
	}else{
		$d=$objManageData->addRecords($arrayRecord, 'operative_template');
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
if($_POST['act']=='delete')
{
	if($_POST['chkBox']){
		$delChkBoxes = $_POST['chkBox'];
		if(is_array($delChkBoxes)){
					$counter=0;
			foreach($delChkBoxes as $OptemplateId){
				$rec_del=$objManageData->delRecord('operative_template', 'template_id', $OptemplateId);
				if($rec_del)$counter++;
			}
			if($rec_del)
			{
				echo "<script>top.frames[0].alert_msg('success','".$counter."')</script>";
			}
		}
	}
}
if($_POST['act']=='CopyFromCommunity'){
	
	//get data for that template
	$templateDetails = $objManageData->getRowRecord('operative_template', 'template_id', $_POST['templateid']);
	$data = $templateDetails->template_data;
	$name = $templateDetails->template_name;

	$arrayRecord['surgeonId'] = $_POST['surgeonIdCopied'];
	
	$_REQUEST['surgeonId']= $_POST['surgeonIdCopied'];
	$_REQUEST['template']=$_POST['templateid'];
	
	$arrayRecord['template_name'] = $name;
	$arrayRecord['template_data'] = $data;
	$arrayRecord['copy_community_opr_temp_id'] = $_POST['templateid'];
	
	$d=$objManageData->addRecords($arrayRecord, 'operative_template');
	
	$counter++;	
	if($counter)
	{
		echo "<script>top.frames[0].alert_msg('success','".$counter."')</script>";
	}
}
//COPY SELECTED TEMPLATE
if($_POST['act']=='CopyToCommunity')
{
	if($_POST['chkBox']){
		$delChkBoxes = $_POST['chkBox'];
		if(is_array($delChkBoxes)){
			$counter=0;
			foreach($delChkBoxes as $OptemplateId){
				//get data for that template
				$templateDetails = $objManageData->getRowRecord('operative_template', 'template_id', $OptemplateId);
				$data = $templateDetails->template_data;
				$name = $templateDetails->template_name;

				$arrayRecord['surgeonId'] = 0;
				$arrayRecord['template_name'] = $name;
				$arrayRecord['template_data'] = $data;
				$arrayRecord['copy_surgeon_opr_temp_id'] = $OptemplateId;
				
				$d=$objManageData->addRecords($arrayRecord, 'operative_template');
				
				$counter++;
			}
			if($counter)
			{
				echo "<script>top.frames[0].alert_msg('success','".$counter."')</script>";
			}
		}
	}
}

$templateId = $_REQUEST['template'];
$templateDetails = $objManageData->getRowRecord('operative_template', 'template_id', $templateId);
$data = $templateDetails->template_data;
$surgeonId = $templateDetails->surgeonId;

?>
<div class="modal fade" id="open_div_surgeon">
     <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob" style="color:#FFF"> <!--title here-->Select Surgeon To Copy </h4>  
            </div>
            <div class="modal-body" style="padding:10px; height:50px; min-height:50px">
                 <select name="copyToSurgeon" id="copyToSurgeon" class="form-control selectpicker" data-header="Select Surgeon" title="Select Surgeon">
                    <option value="" >Select Surgeon</option>
                        <?php
                        
                        $userSurgeonsDetails = $objManageData->getArrayRecords('users', 'user_type', 'Surgeon','lname','ASC');
                        if($userSurgeonsDetails) {
                            foreach($userSurgeonsDetails as $surgeon){
                                $deleteStatus = $surgeon->deleteStatus;
                                if($deleteStatus=="Yes") { //IF THIS USER HAS BEEN COMMITTED AS DELETED(BY SETTING ITS deleteStatus TO Yes)
                                    ////DO NOT SHOW DELETED USER IN DROP DOWN 
                                }else {
                                
                                ?>
                                    <option value="<?php echo $surgeon->usersId; ?>" <?php if($surgeonId == $surgeon->usersId) echo "SELECTED"; ?>><?php echo $surgeon->lname.', '.$surgeon->fname; ?></option>
                                <?php
                                }
                            }
                        }	
                        ?>
                </select>
            </div>
            <div class="modal-footer">
            <button name="saveSurgeonCopy" id="saveSurgeonCopy" class="btn btn-success" onClick="copyCommunityToSurgeon();"><i class="fa fa-save"></i> Save</button>
            <button name="cancelCopy" id="cancelCopy" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
            </div>
         </div>
     </div>
 </div>
 
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
            <tr >
                <td>Pre-op diagnosis : </td>
                <td>{PRE-OP DIAGNOSIS}</td>
                <td>Post-op diagnosis : </td>
                <td>{POST-OP DIAGNOSIS}</td>
            </tr>
            <tr>
                <td>DATE : </td>
                <td>{DATE}</td>
                <td>TIME :</td>
                <td>{TIME}</td>
            </tr>
            <tr>
                <td>ASC Name : </td>
                <td>{ASC NAME}</td>
                <td>ASC Address :</td>
                <td>{ASC ADDRESS}</td>
            </tr>
            <tr>
                <td>ASC Phone</td>
                <td>{ASC PHONE}</td>
              	<td>Patient Id</td>
                <td>{PATIENT ID}</td>
            </tr>
            <tr>
                <td>Arrival Time</td>
                <td>{ARRIVAL TIME}</td>
              	<td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
                </table>
            </div>
         </div>
     </div>
 </div>

    <form name="frmOperativeReport" action="operativeReport.php" method="post">
    <input type="hidden" name="templateid" value="<?php echo $templateId; ?>">
    <input type="hidden" name="sbtTemplate" value="">	
    <input type="hidden" name="act" id="act" value="">
    <div class="inner_surg_middle ">
					
                  
<div style="" id="" class="all_content1_slider ">	         
 <div class="head_scheduler new_head_slider padding_head_adjust_admin">
 
        <span>
             Op Report
            </span>
        </div>
      <div class="wrap_inside_admin">
        <div class="col-lg-3 col-sm-4 col-xs-12 col-md-3" onMouseOver="open_div(false);">
            <div class="sidebar-wrap-op"> <?php
  //  if(!$surgeonId) {
        $surgeonId = $_REQUEST['surgeonId'];
    //}
    ?><span class="header_side">
            <a class="header_side" href="javascript:void(0)" style="padding-left:0" >Operative Report Templates </a>
       <select name="surgeonId" onChange="javascript:changeRefresh(this.value);" class="selectpicker form-control header_side_local" data-width="100%" data-header="Select Surgeon" title="Select Surgeon">
                        <option value="0" selected>Community</option>
                            <?php
                            
                            $userSurgeonsDetails = $objManageData->getArrayRecords('users', 'user_type', 'Surgeon','lname','ASC');
                            if($userSurgeonsDetails) {
                                foreach($userSurgeonsDetails as $surgeon){
                                    $deleteStatus = $surgeon->deleteStatus;
                                    if($deleteStatus=="Yes") { //IF THIS USER HAS BEEN COMMITTED AS DELETED(BY SETTING ITS deleteStatus TO Yes)
                                        ////DO NOT SHOW DELETED USER IN DROP DOWN 
                                    }else {
                                    
                                    ?>
                                        <option value="<?php echo $surgeon->usersId; ?>" <?php if($surgeonId == $surgeon->usersId) echo "SELECTED"; ?>><?php echo $surgeon->lname.', '.$surgeon->fname; ?></option>
                                    <?php
                                    }
                                }
                            }	
                            ?>
                    </select>
                    </span>
               <ul class="list-group">
               <?php
                        $get_surgeonId = $_REQUEST['surgeonId'];
                        //if($get_surgeonId) {
                            //$templateLists = $objManageData->getArrayRecords('operative_template','surgeonId',$get_surgeonId, 'template_name', 'ASC');
							$condition_arr['1']='1';
							$templateLists = $objManageData->getMultiChkArrayRecords('operative_template', $condition_arr, 'template_name', 'ASC', " AND surgeonId='$get_surgeonId'");
                      //  }else { 
                       //     $templateLists = $objManageData->getArrayRecords('operative_template','','', 'template_name', 'ASC');
                       // }
                        
                        if($templateLists){
                            foreach($templateLists as $key => $list){
                                unset($conditionArr);
                                $conditionArr['usersId'] = $list->surgeonId;
                                $conditionArr['deleteStatus'] = "Yes";
                                $userDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
                                $surgeonIdTemp = $list->surgeonId;
                                $surgeonDetails = $objManageData->getRowRecord('users', 'usersId', $surgeonIdTemp);
                                $surgeonName = $surgeonDetails->lname.', '.$surgeonDetails->fname;
                                
                                if($userDetails) {
                                    //DO NOT SHOW TEMPLATE RECORD
                                }else {
                                    ++$seq;
                                ?>
                                 <a href="operativeReport.php?template=<?php echo $list->template_id; ?>&amp;surgeonId=<?php echo $list->surgeonId;?>" class="list-group-item border-bb"><label>  <input type="checkbox" name="chkBox[]" value="<?php echo $list->template_id; ?>"> </label> 
                                  <?php echo stripslashes($list->template_name);//.' ('.$surgeonName.')' ?>
                                </a>
                                <?php
                                }
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
				<input type="hidden" name="surgeonIdCopied" id="surgeonIdCopied" value="<?php echo $surgeonId; ?>">
                <input type="text" name="template_name" value="<?php echo stripslashes($templateDetails->template_name); ?>" class="form-control" id="t_name">
                </div>
                <div class="clearfix margin_adjustment_only"></div>
                <div class="clearfix margin_adjustment_only border-dashed"></div>
                <div class="clearfix margin_adjustment_only"></div>
            </div>
            <div class="op_right_main" >
                <textarea name="editor1" id="editor1" rows="" cols="" ><?php echo stripslashes($data);?> </textarea>
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
           <div id="fieldSample" class="template_wrap"> *Fields marked in parenthesis {} Will change. <a href="javascript:void(0);" onClick="open_div(true);" class="black" style="color:#800080;"><b>Click here for example</b></a></div>
         </div>	         
      </div>		
 </div>
</div>
<div id="myModal" class="modal fade" style="top: 20%;"> <!--Common Alert Container-->
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header" style="padding:6px 12px;">
				<button style="color:#FFFFFF;opacity:0.9" ype="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 style="color:#FFFFFF;" class="modal-title">Selset Surgeon</h4>
			</div>
			<div class="modal-body" style="min-height:auto;">
				<p style="padding: 10px;" class="">
                Surgeon list
                </p>
			</div>
			<div class="modal-footer" style="text-align:center;margin-top:0;padding:4px;">
				<button id="cancel_yes" class="btn btn-primary hidden" onclick="document.getElementById('frm_sign_all_pre_op_order').submit();">Yes</button>
				<button id="cancel_no" class="btn btn-danger hidden" data-dismiss="modal">No</button>
				<button style="margin-left:0;" id="missing_feilds" class="btn btn-primary" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
   </form>
</body>
</html>