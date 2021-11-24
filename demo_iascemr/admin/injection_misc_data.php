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
<title>Injection Misc Template</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("adminLinkfile.php");?>
<script>
//START FUNCTION TO FIND POSITION FROM LEFT
function findPos_X_custom(id){
	var obj = document.getElementById(id);
	var leftPanel =	parseFloat($('.sidebar-wrap-op').outerWidth(true));
	var posX = obj.offsetLeft;
	while(obj.offsetParent){
		posX=posX+obj.offsetParent.offsetLeft;
		if(obj==document.getElementsByTagName('body')[0]){break}
		else{obj=obj.offsetParent;}
	}
	var posXNew = parseFloat(posX - leftPanel);
	return(posXNew);
}
//END FUNCTION TO FIND POSITION FROM LEFT

//START FUNCTION TO FIND POSITION FROM TOP
function findPos_Y_custom(id){
	var obj = document.getElementById(id);
	var posY = obj.offsetTop;
	while(obj.offsetParent){
		posY=posY+obj.offsetParent.offsetTop;
		if(obj==document.getElementsByTagName('body')[0]){break}
		else{obj=obj.offsetParent;}
	}
	return(posY);
}
//END FUNCTION TO FIND POSITION FROM TOP

function move_templete(val){
		location.href='?injProcedureId='+val;
}
top.frames[0].document.frameSrc.source.value = 'injection_misc.php';	
top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
top.frames[0].document.getElementById('deleteSelected').style.display = 'inline-block';
top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';	
</script>

<script >
var preDefineCloseOut;
function preDefineOpenCloseFun() {
	document.getElementById("hiddPreDefineId").value = "preDefineOpenYes";
}
function preCloseFun(Id) {
	if(document.getElementById("hiddPreDefineId")) {
		if(document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
			if(document.getElementById(Id)) {
				if(document.getElementById(Id).style.display == "block"){
					document.getElementById(Id).style.display = "none"; 
					//document.getElementById("hiddPreDefineId").value = "";
				}
			}
			if(top.frames[0].frames[0].document.getElementById(Id)) {
				if(top.frames[0].frames[0].document.getElementById(Id).style.display == "block"){
					top.frames[0].frames[0].document.getElementById(Id).style.display = "none"; 
					//top.frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
				}
			}
		}
		
	}
}

function showMediDiv($this,c)
{
	var CLeft	=	$this.parent('label').parent('div').parent('div').offset().left  -105;
	var CTop	=	$this.parent('label').parent('div').parent('div').offset().top;	
	var obj		=	$("#medicationPopupAdmin");
	
	CTop		=	CTop - obj.outerHeight(true) -12  ;
	
	obj.css({'left' : CLeft +'px' , 'top' : CTop + 'px' , 'display':'block'});
	
	document.getElementById("counter").value = c;
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100) ;
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
	
}

$(document).ready(function()
{
		$("#cptCode, #dxCode").click(function(e) {
     	
			var Id	=	$(this).attr('id');				
			var Pn	=	'common_cpt_dx_profile.php';
			var PId	=	$("#templateID").val() ;
			var Dct	=	$("#DxCodeType").val();
			
			var t	=	'<?=base64_encode('inj_misc_procedure_template')?>';
			var k	=	'<?=base64_encode('templateID')?>';
			var url	=	Pn;
			url 	+=	'?t='+ t ;
			url 	+=	'&k='+ k ;
			url 	+=	'&pro_id='+ PId;
			url		+=	'&'+Id+'=yes';
			url		+=	(Id === 'dxCode' ) ? '&diagnosis_code_type='+Dct : '' ;
			
			var SW	=	window.screen.width ;
			var SH	=	window.screen.height;
			
			var	W	=	( SW > 1200 ) ?  1200	: SW ;
			var	H	=	W * 0.65
	
			window.open(url,'Injection/Misc. CPT & Dx Code','width='+W+',height='+H+',resizable=1');
			
		});
		
		
		$("#procedureConsents").click(function(){
			
			var Pn	=	'common_procedure_consents.php';
			var PId	=	$("#templateID").val() ;
			
			var t	=	'<?=base64_encode('inj_misc_procedure_template')?>';
			var k=	'<?=base64_encode('templateID')?>';
			var url	=	Pn;
			url 	+=	'?t='+ t ;
			url 	+=	'&k='+ k ;
			url 	+=	'&pro_id='+ PId;
			
			var SW	=	window.screen.width ;
			var SH	=	window.screen.height;
			
			var	W	=	( SW > 1200 ) ?  parseInt(SW/3)	: parseInt(SW/2) ;
			var	H	=	SH * 0.75;
	
			window.open(url,'Injection/Misc. - ProcedureConsents','width='+W+',height='+H+',resizable=1');
		});
		
	
});
</script>
</head>
<body >
<?php
include_once("injectionMiscSpreadSheet.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$injProcedureId = $_REQUEST['injProcedureId'];
$templeteID_show=	$_REQUEST['templeteID_show'];

include("../common/medication_pop_admin.php");


//Insert and edit
if($_REQUEST['submitForm']){
	
	/* Sorting Pre Op Medication */
	$PreOpMed	=	$_REQUEST['PreOpMed'];
	$PreOpLot	=	$_REQUEST['PreOpLot'];
	
	$preOpMedsArray	=	array();
	$preOpMedsDB		=	'';
	
	if(is_array($PreOpMed) && count($PreOpMed) > 0)
	{
		foreach($PreOpMed as $key=>$medicationName)
		{
			if(trim($medicationName))
			{
				$preOpMedsArray[]	=	$medicationName.'@#@'.$PreOpLot[$key];
			}
		}
		$preOpMedsDB	=	implode('~@~',$preOpMedsArray);
	}
	
	/* Sorting Intravitreal Medication */
	$IntravitrealMed	=	$_REQUEST['IntravitrealMed'];
	$IntravitrealLot	=	$_REQUEST['IntravitrealLot'];
	
	$intravitrealMedsArray	=	array();
	$intravitrealMedsDB			=	'';
	
	if(is_array($IntravitrealMed) && count($IntravitrealMed) > 0)
	{
		foreach($IntravitrealMed as $key=>$medicationName)
		{
			if(trim($medicationName))
			{
				$intravitrealMedsArray[]	=	$medicationName.'@#@'.$IntravitrealLot[$key];
			}
		}
		$intravitrealMedsDB	=	implode('~@~',$intravitrealMedsArray);
	}
	
	
	/* Sorting Post Op Medication */
	$PostOpMed=	$_REQUEST['PostOpMed'];
	$PostOpLot=	$_REQUEST['PostOpLot'];
	
	$postOpMedsArray	=	array();
	$postOpMedsDB			=	'';
	
	if(is_array($PostOpMed) && count($PostOpMed) > 0)
	{
		foreach($PostOpMed as $key=>$medicationName)
		{
			if(trim($medicationName))
			{
				$postOpMedsArray[]	=	$medicationName.'@#@'.$PostOpLot[$key];
			}
		}
		$postOpMedsDB	=	implode('~@~',$postOpMedsArray);
	}
	
	
	
	unset($arrayRecord);
	if($_REQUEST['surgeonID']){
		$surgeonID=implode(",",$_REQUEST['surgeonID']);
	}else {
		$surgeonID='all';
	}
	$arrayRecord['procedureID'] 	= addslashes($_REQUEST['injProcedureId']);
	$arrayRecord['templateName']	= addslashes($_REQUEST['templateName']);
	$arrayRecord['surgeonID'] 		= $surgeonID;
	$arrayRecord['instructionSheetID']=	$_REQUEST['instructionSheetID'];
	$arrayRecord['operativeReportID']=	$_REQUEST['operativeReportID'];
	$arrayRecord['timeoutReq']		=	isset($_REQUEST['timeoutReq'])	?	$_REQUEST['timeoutReq']	:	0;
	$arrayRecord['preOpMeds']			=	addslashes($preOpMedsDB);
	$arrayRecord['intravitrealMeds']=	addslashes($intravitrealMedsDB);
	$arrayRecord['postOpMeds']		=	addslashes($postOpMedsDB);
	
	if($_REQUEST['templateID']){
		$templete_change=$_REQUEST['templateID'];
		$c	=	$objManageData->UpdateRecord($arrayRecord, 'inj_misc_procedure_template', 'templateID', $templete_change);
	}
	else
	{
		$arrayRecord['cpt_id'] 							=	$_REQUEST['cpt_id'];
		$arrayRecord['cpt_id_default']			=	$_REQUEST['cpt_id_default'];
		$arrayRecord['dx_id']								=	$_REQUEST['dx_id'];
		$arrayRecord['dx_id_default']				=	$_REQUEST['dx_id_default'];
		$arrayRecord['dx_id_icd10']					=	$_REQUEST['dx_id_icd10'];
		$arrayRecord['dx_id_default_icd10']	=	$_REQUEST['dx_id_default_icd10'];
		$arrayRecord['consentTemplateId']		=	$_REQUEST['consentTemplateId'];
		
		$d	=	$objManageData->addRecords($arrayRecord, 'inj_misc_procedure_template');
	}
	if($c)
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
		$counter=0;
		$delChkBoxes = $_POST['chkBox'];
		if(is_array($delChkBoxes)){
			foreach($delChkBoxes as $templateID){
				$rec_del=$objManageData->delRecord('inj_misc_procedure_template', 'templateID', $templateID);
				if($rec_del)$counter++;
			}
			if($rec_del)
			{
				echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
			}
		}
}

if($templeteID_show)
{
	$templateDetails = $objManageData->getExtractRecord('inj_misc_procedure_template', 'templateID', $templeteID_show);
	extract($templateDetails);
}

//START GETTING DX CODE TYPE
$sqlStr 		= "SELECT * FROM surgerycenter WHERE surgeryCenterId = '1'";
$sqlQry 		= imw_query($sqlStr);
$rowsCount 	= imw_num_rows($sqlQry);
$DxCodeType = 'icd9';
if($rowsCount > 0){
	$sqlRows = imw_fetch_array($sqlQry);
	$DxCodeType	=	$sqlRows['diagnosis_code_type'];
}
//END GETTING DX CODE TYPE
?>

<form name="frmInjectionMiscTemplate" action="<?=basename($_SERVER['PHP_SELF'])?>" method="post">
		<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
    <input type="hidden" name="templateID" id="templateID" value="<?php echo $templeteID_show; ?>">
    <input type="hidden" name="DxCodeType" id="DxCodeType" value="<?=$DxCodeType?>">	
    <input type="hidden" name="cpt_id" id="cpt_id" value="">	
    <input type="hidden" name="cpt_id_default" id="cpt_id_default" value="">	
    <input type="hidden" name="dx_id" id="dx_id" value="">	
    <input type="hidden" name="dx_id_default" id="dx_id_default" value="">
    <input type="hidden" name="dx_id_icd10" id="dx_id_icd10" value="">	
    <input type="hidden" name="dx_id_default_icd10" id="dx_id_default_icd10" value="">	
    <input type="hidden" name="consentTemplateId" id="consentTemplateId" value="">	
    <input type="hidden" name="sbtTemplate" id="sbtTemplate" value="">	
    <input type="hidden" name="divId" id="divId">
    <input type="hidden" name="counter" id="counter">
    
    <input type="hidden" name="secondaryValues" id="secondaryValues">
    <input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
    <input type="hidden" name="submitForm" id="submitForm" value="">
    
    <span id="selectedInjProcedureNameId"  class="text_10b blue_txt" style="padding-left:8;white-space:nowrap; ">
			<?php
      $GET_PROCEDURE="";
      if($_REQUEST['injProcedureId'])
      {
        $procedureNameDetails = $objManageData->getArrayRecords('procedures', 'procedureId', $_REQUEST['injProcedureId']);
        foreach($procedureNameDetails as $procedureName){
          $GET_PROCEDURE	=	ucfirst($procedureName->name); 
        }
      }
      ?>							
    </span>
    
    <div class="margin_bottom_mid_adjustment scheduler_margins_head ">
    	<div class="container-fluid padding_0">
      	<div class="inner_surg_middle ">
        	<div style="" id="" class="all_content1_slider ">
          	<div class="all_admin_content_agree wrap_inside_admin">
            	
              <div class=" subtracting-head">
              	<div class="head_scheduler new_head_slider padding_head_adjust_admin"><span>Injection/Misc.</span></div>
            	</div>
              
              <div class="wrap_inside_admin">
              	<div class="col-lg-3 col-sm-4 col-xs-12 col-md-3">
                	<div class="sidebar-wrap-op">
                  	<a class="header_side" href="javascript:void(0)"> Injection/Misc. Templates </a>
                  	<ul class="list-group scrollable_yes_left">
                    <?php
											$procedureId	=	addslashes($_REQUEST['injProcedureId']);
											$procedureTempQry	=	"Select * From inj_misc_procedure_template Where procedureID='".$procedureId."' Order By templateName Asc";
											$procedureTempSql	=	imw_query($procedureTempQry) or die("not selected".imw_error());
											while($procedureTempRow	=	imw_fetch_array($procedureTempSql))
											{
										?>
                    			<a href="?templeteID_show=<?=$procedureTempRow['templateID'];?>&amp;injProcedureId=<?=$injProcedureId?>" class="list-group-item border-bb">
                          	<label>
                            	<input type="checkbox" name="chkBox[]" value="<?php echo $procedureTempRow['templateID']; ?>">
                            </label>
                            &nbsp;&nbsp;&nbsp;
														<?php echo stripslashes(ucfirst($procedureTempRow['templateName'])); ?>
                        	</a>
                  	<?php 
											}
										?>
                    </ul>	
                 	</div>
               	</div>
                
                <div class="clearfix visible-xs margin_adjustment_only"></div>
                
                <div class="col-lg-9 col-sm-8 col-xs-12 col-md-9">
                	<h5 class="ans_pro_h"> <span><?php echo $GET_PROCEDURE; ?></span>  </h5>
                  
                  <div class="clearfix  margin_adjustment_only"></div>
                  
                  <div class="template_wrap scrollable_yes_right">
                  	<div class="form_reg">
                    	<div class="">
                      	
                        <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
                        	
                          <div class="col-md-12 col-sm-12 col-xs-12 col-lg-5">
                          	<label for="s_name">Select Injection/Misc. Procedure</label>
                         	</div>
                          
                          <div class="col-md-12 col-sm-12 col-xs-12 col-lg-7">
                          	<select class="form-control selectpicker" name="injProcedureId" id="injProcedureId" onChange="javascript:move_templete(this.value);" data-container="body">
                            	<option value="">Select Injection/Misc. Procedure</option>
                              <?php
																$injMiscData	=	$objManageData->getInjectionMiscProcedures();
																if($injMiscData)
																{
																	foreach($injMiscData as $injMiscDetail)
																	{
															?>
                              			<option value="<?php echo $injMiscDetail->procedureId; ?>" <?php if($injMiscDetail->procedureId==$injProcedureId) { echo "Selected";} else {}?>><?php echo ucfirst($injMiscDetail->name);?></option>
                             	<?php
																	}
																}
															?>
                          	</select>
                        	</div>	
                          
                       	</div>
                        
                        <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
                        	
                          <div class="col-md-12 col-sm-12 col-xs-12 col-lg-4 text-right">
                          	<label for="template_name">Template Name</label>
                         	</div>
                          
                          <div class="col-md-12 col-sm-12 col-xs-12 col-lg-8 text-left">
                          	<input type="text" class="form-control" id="templateName" name="templateName" value="<?php echo stripslashes(ucfirst($templateName));?>" />
                         	</div>
                      	</div>	
                        
                    	</div>	
                   	</div>
                    
                    <div class="clearfix margin_adjustment_only"></div>
                    <div class="clearfix margin_adjustment_only border-dashed"></div>
                    <div class="clearfix margin_adjustment_only"></div>
                    
                    <div class="full_inner_wrap_laser">
                    	<div class="scanner_win new_s">
                      	<h4 ><span>Surgeon</span></h4>
                        <div id="laserCodeDiv">
                          <span id="cptCode">CPT</span>
                          <span id="dxCode">Dx</span>
                          <span id="procedureConsents">Procedure Consents</span>
                     		</div>
                   		</div>
                    
                      <div class="">
                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                          <div class=" caption2">
                            <label for="laserprocedure_surgeonId" data-placement="top" >
                              Select Surgeon
                            </label>
                          </div>
                        </div>
                        
                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                        <?php
                          $select_surID	=	explode(",",$surgeonID);
                          $userSurgeonsDetails = $objManageData->getArrayRecords('users', 'user_type', 'Surgeon','lname','ASC');
                          if($userSurgeonsDetails)
                          {
                            foreach($userSurgeonsDetails as $surgeon)
                            {
                              $deleteStatus = $surgeon->deleteStatus;
                              if($deleteStatus=="Yes")
                              {  
                                //IF THIS USER HAS BEEN COMMITTED AS DELETED(BY SETTING ITS deleteStatus TO Yes)
                                //DO NOT SHOW DELETED USER IN DROP DOWN
                              }
                              else
                              {
                                $selSurg	=	"";
                                if(in_array($surgeon->usersId,$select_surID)){ $selSurg =  "SELECTED";}
                                $surgeonName	=	$surgeon->lname.', '.$surgeon->fname;
                                $surgeonOption .='<option value="'.$surgeon->usersId.'" '.$selSurg.' data-attending="1">'.$surgeonName.'</option>';
                              }
                            }
                          }
                        ?>
                          <select name="surgeonID[]" id="surgeonID" class="selectpicker form-control" multiple="multiple" data-container="body" data-title="All Surgeon" >
                              <option value="all" <?php if($surgeonID == "all" || ! $surgeonID) { echo "selected";}?> data-attending="0">All Surgeon</option>
                              <?php echo $surgeonOption; ?>
                          </select> 
                        </div>
                      </div>
                    
                      <div class="clearfix margin_adjustment_only"></div>
                      <div class="clearfix margin_adjustment_only"></div>
                    
                      <div class="">
                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                          <div class=" caption2">
                            <label for="instructionSheetID" data-placement="top" >
                              Select Instruction Sheet
                            </label>
                          </div>
                        </div>
                        
                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                        <?php
                          $insRow	=	$objManageData->getArrayRecords('instruction_template','','','instruction_name','Asc');
                        ?>
                        <select name="instructionSheetID" id="instructionSheetID" class="selectpicker form-control" data-container="body" data-header="Select Instruction Sheet" title="Select Instruction Sheet" data-size="10" >
                        	<option value="">-- Select --</option>
												<?php
                            if(is_array($insRow) && count($insRow) > 0 )
                            {
                              foreach($insRow as $insData)
                              {
                                echo '<option value="'.$insData->instruction_id.'" '.($instructionSheetID == $insData->instruction_id ? "Selected" : '').'>'.$insData->instruction_name.'</option>';	
                              }
                            }
                        ?>
                        </select>
                        </div>
                      </div>
                    	
                      
                      <div class="clearfix margin_adjustment_only"></div>
                      <div class="clearfix margin_adjustment_only"></div>
                      
                      <div class="">
                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                          <div class=" caption2">
                            <label for="operativeReportID" data-placement="top" >
                              Select Operative Report
                            </label>
                          </div>
                        </div>
                        
                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                        <?php
												$preOpTemplates	=	$objManageData->getArrayRecords('operative_template','1','1','template_name','Asc'," And surgeonId = '0'");
                        ?>
                        <select name="operativeReportID" id="operativeReportID" class="selectpicker form-control" data-container="body" data-header="operativeReportID" title="Select Operative Report" data-size="10" >
                        	<option value="">-- Select --</option>
                          <?php										
                          	if(is_array($preOpTemplates) && count($preOpTemplates) > 0 )
                            {
															foreach($preOpTemplates as $templates)
															{
                         	?>
                          			<option value="<?=$templates->template_id?>" <?=($operativeReportID == $templates->template_id ? "Selected" : '')?>><?=$templates->template_name; ?></option>
                         	<?php
															}
														}
                          ?>
                     		</select>
                  			</div>
                      </div>
                      
                      <div class="clearfix margin_adjustment_only"></div>
                      <div class="clearfix margin_adjustment_only"></div>
                      
                      <div class=""><br>
                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                          <div class=" caption2">
                            <label data-placement="top">Timeout Required</label>
                          </div>
                        </div>
                        
                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6 margin_top_5">
                        	<input name="timeoutReq" id="timeoutReq" type="checkbox" value="1" <?=($timeoutReq ? 'checked' : '')?>> Yes
                        </div>
                     	</div>
                      <!-- Pre Op Meds -->
                      <div class="scanner_win new_s">
                      	<h4><span >Pre Op Medication</span></h4>
                      </div>
                    
                      <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                        <div class="caption caption2">
                          <label href="" >
                            <a data-placement="top" class="show-pop-list_pr" style="cursor:pointer;" onClick="return showMediDiv($(this), 20),document.getElementById('selected_frame_name_id').value='PreOp';"> Pre Op Medication </a>
                          </label>
                        </div>
                      </div>
                    
                      <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf width_table table-striped" >
                        <thead>
                        	<tr>
                          	<th style="width:45%;"> Medication </th>
                            <th > #Lot </th>
                         	</tr>
                       	</thead>
                        <tbody>
                        	<tr>
                          	<td colspan="2" style="padding:1px 0">
                            	<div class="over_wrap" id="spreadSheetPreOpMed" >
                              <?php printSpreadSheet('PreOp',$preOpMeds);?>
                              </div>
                          	</td>
                         	</tr>
                      	</tbody>
                        </table>
                      </div>
                      
                      <!-- Intravitreal  Op Meds -->
                      <div class="scanner_win new_s">
                      	<h4><span >Intravitreal  Medication</span></h4>
                      </div>
                    
                      <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                        <div class="caption caption2">
                          <label href="" >
                            <a data-placement="top" class="show-pop-list_pr" style="cursor:pointer;" onClick="return showMediDiv($(this), 20),document.getElementById('selected_frame_name_id').value='Intravitreal';"> Intravitreal  Medication </a>
                          </label>
                        </div>
                      </div>
                    
                      <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf width_table table-striped" >
                        <thead>
                        	<tr>
                          	<th style="width:45%;"> Medication </th>
                            <th > #Lot </th>
                         	</tr>
                       	</thead>
                        <tbody>
                        	<tr>
                          	<td colspan="2" style="padding:1px 0">
                            	<div class="over_wrap" id="spreadSheetIntravitrealMed" >
                              <?php printSpreadSheet('Intravitreal',$intravitrealMeds);?>
                              </div>
                          	</td>
                         	</tr>
                      	</tbody>
                        </table>
                      </div>
                      
                      
                      <!-- Post Op Meds -->
                      <div class="scanner_win new_s">
                      	<h4><span >Post Op Medication</span></h4>
                      </div>
                    
                      <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                        <div class="caption caption2">
                          <label href="" >
                            <a data-placement="top" class="show-pop-list_pr" style="cursor:pointer;" onClick="return showMediDiv($(this), 20),document.getElementById('selected_frame_name_id').value='PostOp';"> Post Op Medication </a>
                          </label>
                        </div>
                      </div>
                    
                      <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf width_table table-striped" >
                        <thead>
                        	<tr>
                          	<th style="width:45%;"> Medication </th>
                            <th style="width:45%;"> #Lot </th>
                         	</tr>
                       	</thead>
                        <tbody>
                        	<tr>
                          	<td colspan="2" style="padding:1px 0">
                            	<div class="over_wrap" id="spreadSheetPostOpMed" >
                              <?php printSpreadSheet('PostOp',$postOpMeds);?>
                              </div>
                          	</td>
                         	</tr>
                      	</tbody>
                        </table>
                      </div>
                 
                 		</div>
                  
                  	<div class="clearfix margin_adjustment_only"></div>
             			</div>
               	
                </div>
             	
              </div>
              
          	</div>
        	</div>
      	</div> 
     	</div>
      
      <!-- NEcessary PUSH     -->	 
      <div class="push"></div>
      <!-- NEcessary PUSH     -->
  	</div>
    
</form>

</body>
</html>