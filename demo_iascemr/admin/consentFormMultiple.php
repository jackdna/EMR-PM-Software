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
<title>Consent Forms</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("adminLinkfile.php");?>
<style>
	div.div_display{
		display:none;
		position:absolute;
		top:68px;
		left:60px;
		background:#CCCCCC;
	}
</style>
<script>
function delTemplateFn(id){
	var ask = confirm("Are you sure to delete the template.");
	if(ask==true){
		top.frames[0].frames[0].location.href = 'consentFormMultiple.php?delRecordId='+id;
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
function chk_category(obj_cat)
{
	//	alert(obj_cat);
		var cat_consentid=obj_cat;
		//document.getElementById("consent_detail").style.display = 'inline-block';
		location.href='consentFormMultiple.php?select_cat='+cat_consentid;
}
</script>
</head>
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
$templateId = $_REQUEST['template'];
$category_consent=$_REQUEST['select_cat'];
//CODE TO DISPLAY CANCEL BUTTON
if($templateId<>"") {
	echo "<script>top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';</script>";
}
//END CODE TO DISPLAY CANCEL BUTTON

	$text=$_REQUEST['text'];
if($_REQUEST['sbtConsent']){
	$tmplateData = $_REQUEST['editor1'];
	$consentId = $_REQUEST['consentId'];
	$category_consent = $_REQUEST['category_consent'];
	$tmplateData = str_ireplace("\\","/",$tmplateData);
	unset($arrayRecord);
	$arrayRecord['consent_name'] = addslashes($_REQUEST['consent_name']);
	$arrayRecord['consent_alias'] = addslashes($_REQUEST['consent_alias']);
	$arrayRecord['consent_category_id'] = addslashes($_REQUEST['category_consent']);
	$arrayRecord['consent_data'] = addslashes($tmplateData);
	if($consentId){
		$c=$objManageData->UpdateRecord($arrayRecord, 'consent_forms_template', 'consent_id', $consentId);
	}else{
		$d=$objManageData->addRecords($arrayRecord, 'consent_forms_template');
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
//SET DELETE STATUS TO TRUE FOR SELECTED TEMPLATE
	if($_POST['chkBox']){
		$delChkBoxes = $_POST['chkBox'];
		if(is_array($delChkBoxes)){
			$counter=0;
			foreach($delChkBoxes as $OptemplateId){
				unset($arrayRecord);
				$arrayRecord['consent_delete_status'] = 'true';
				$rec_del=$objManageData->UpdateRecord($arrayRecord, 'consent_forms_template', 'consent_id', $OptemplateId);
				if($rec_del)$counter++;
				//$objManageData->delRecord('consent_forms_template', 'consent_id', $OptemplateId);
			}
			if($rec_del)
			{
				echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
			}
		}
	}
//SET DELETE STATUS TO TRUE FOR SELECTED TEMPLATE


$templateDetails = $objManageData->getRowRecord('consent_forms_template', 'consent_id', $templateId);
$data = stripslashes($templateDetails->consent_data);
$consentName = $templateDetails->consent_name;
$consentAlias = $templateDetails->consent_alias;


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
			<td>Patient Id : </td>
			<td>{PATIENT ID}</td>
			<td>Anes Name : </td>
			<td>{ANES NAME}</td>
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
			<td>Very Small Text Box : </td>
			<td>{TEXTBOX_XSMALL}</td>
			 
			<td>Small Text Box : </td>
			<td>{TEXTBOX_SMALL}</td>
			
		</tr>
		<tr>
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
		<tr>
			<td>Anesthesiologist's Signature : </td>
			<td>{Anesthesiologist's Signature}</td>
			 
			<td>Witness Signature : </td>
			<td>{Witness Signature}</td>
			
		</tr>
		<tr>
        	<td>Signature : </td>
			<td>{SIGNATURE}</td>
            
			<td>DATE : </td>
			<td>{DATE}</td>
		</tr>
		<tr>
        	<td>ASC Name : </td>
			<td>{ASC NAME}</td>

            <td>ASC Address : </td>
            <td>{ASC ADDRESS}</td>
		</tr>
        <tr>
            <td>ASC Phone</td>
            <td>{ASC PHONE}</td>

			<td>Patient Gender</td>
			<td>{PATIENT GENDER}</td>
        </tr>
        <tr>
            <td>Assistant Surgeon's Signature</td>
            <td>{ASSISTANT_SURGEON_SIGNATURE}</td>

			<td>Arrival Time</td>
			<td>{ARRIVAL TIME}</td>
        </tr>
        
                </table>
            </div>
         </div>
     </div>
 </div>

	<form name="frmConsent" action="consentFormMultiple.php" method="post">
	<input type="hidden" name="consentId" value="<?php echo $templateId; ?>">
	<input type="hidden" name="sbtConsent" value="">	
    
    <div class="inner_surg_middle ">
					
                  
<div style="" id="" class="all_content1_slider ">	         
 <div class="head_scheduler new_head_slider padding_head_adjust_admin">
        <span>Consent Form</span>
        </div>
      <div class="wrap_inside_admin">
        <div class="col-lg-3 col-sm-4 col-xs-12 col-md-3" onMouseOver="open_div(false);">
            <div class="sidebar-wrap-op">
            
            <a href="consentFormMultiple.php?template=<?php echo $list->consent_id; ?>" class="header_side"><?php echo $list->consent_name; ?>Consent Form Templates</a>
               <ul class="list-group">
              <?php	if($category_consent) {
					unset($conditionArr);
					$conditionArr['consent_delete_status'] = ' ';  
					$conditionArr['consent_category_id'] = $category_consent;  	
					$templateLists = $objManageData->getMultiChkArrayRecords('consent_forms_template',$conditionArr,'consent_name','ASC');
					if($templateLists){
						foreach($templateLists as $key => $list){
							++$seq;
							$consentNme = ucfirst(stripslashes($list->consent_name));
							$consentNmeNew = str_ireplace("H&P","H&amp;P",$consentNme);
							?>
							<a href="consentFormMultiple.php?select_cat=<?php echo $category_consent;?>&amp;template=<?php echo $list->consent_id; ?>" class="list-group-item border-bb"><label>  <input type="checkbox" name="chkBox[]" value="<?php echo $list->consent_id; ?>"> </label>&nbsp; <?php echo $consentNmeNew;?></a>
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
                                    <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                    	<div class="row">
                                            <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                                                <label for="Category">Category </label>
                                            </div>
                                            <div class="col-md-8 col-sm-12 col-xs-12 col-lg-8">
                                            <select name="category_consent" class="selectpicker form-control" onChange="return chk_category(this.value);">
                                                <option  value="">Select Category</option>
                                                <?php
                                                        $consent_category_sel = $objManageData->getArrayRecords('consent_category', 'category_status', ' ','category_name','ASC');
                                                        foreach($consent_category_sel as $cat_consent)
                                                        {
                                                ?>			<option value="<?php echo $cat_consent->category_id; ?>" <?php if(($cat_consent->category_id==$category_consent)||$cat_consent->category_id==$templateDetails->consent_category_id) {  echo "selected";} ?>><?php echo $cat_consent->category_name?> </option>
                                                <?php
                                                        }
                                            ?>
                                            </select>
                                           </div>
                                       </div> 
                                    </div>
                                    <div class="clearfix visible-lg margin_adjustment_only"></div>
                                    <div class="clearfix visible-md margin_adjustment_only"></div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                    	<div class="row">
                                            <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                                                <label for="t_name">  Template Name  </label>
                                            </div>
                                            <div class="col-md-8 col-sm-12 col-xs-12 col-lg-8">
                                                <input type="text" name="consent_name" value="<?php echo stripslashes($templateDetails->consent_name); ?>" class="form-control">
                                            </div>
                                       </div> 
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                    	<div class="row">
                                            <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                                                <label for="t_alias">  Template Alias  </label>
                                            </div>
                                            <div class="col-md-8 col-sm-12 col-xs-12 col-lg-8">
                                                <input type="text" name="consent_alias" value="<?php echo stripslashes($templateDetails->consent_alias); ?>" class="form-control">
                                            </div>
                                       </div> 
                                    </div>
                                    <div class="clearfix margin_adjustment_only"></div>
                                    <div class="clearfix margin_adjustment_only border-dashed"></div>
                                    <div class="clearfix margin_adjustment_only"></div>
                                </div>
                                <div class="op_right_main">
                                	    <textarea name="editor1" id="editor1" rows="" cols=""><?php echo $data;?></textarea>
                                        <script>
                                            // Replace the <textarea id="editor1"> with a CKEditor
                                            // instance, using default configuration.
                                            CKEDITOR.replace( 'editor1' );
                                        </script>
                                </div> *Fields marked in parenthesis {} Will change. <a href="javascript:void(0);" onClick="open_div(true);" class="black" style="color:#800080;"><b>Click here for example</b></a>
                             </div>	   
                                  
      </div>		
 </div>
</div>
	
	</form>
</body>
</html>