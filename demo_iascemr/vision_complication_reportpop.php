<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
 	session_start();
	include("common/conDb.php");
	if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
		echo '<script>top.location.href="index.php"</script>';
	}
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
  include_once('common/user_agent.php');
  
	// query part to exclude surgeons in vcna reports
	$usersExclude =	'';
	$surgeonExclude = '';
	if( defined('VCNA_SURGEON_EXCLUDE') && constant('VCNA_SURGEON_EXCLUDE') )
	{
		$usersExclude = 'And usersId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').') ';	
		$surgeonExclude = 'And pc.surgeonId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').')';	
	}
	 
	$fac_qry	=	" and st.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
	$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' ); 
	
	//GET LOGGED IN USER TYPE
	unset($conditionArr);
	$conditionArr['usersId'] = $_SESSION["loginUserId"];
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails){
		foreach($surgeonsDetails as $usersDetail)
		{
			$loggedInUserType = $usersDetail->user_type;
			$loggedInCoordinatorType = $usersDetail->coordinator_type;
		}
	}
	$allowUpdate	=	false;
	if( $loggedInUserType == 'Surgeon' )
	{
		$allowUpdate	=	true;	
	}
	//END GET LOGGED IN USER TYPE
	
	
$superBillProcIdArr = $objManageData->superBillProcIdArrFun();		
if($_REQUEST['proc_save']=='yes') {
	$date1 = trim($_REQUEST["date1"]);
	$date2 = trim($_REQUEST["date2"]);
	
	$currDt = date("m-d-Y");
	
	if(!$date1) { $date1 = $currDt; }
	if(!$date2) { $date2 = $currDt; }
	
	$from_date 	= $objManageData->changeDateYMD($date1);
	$to_date 	= $objManageData->changeDateYMD($date2);
	
	$procedureArr = $_REQUEST["procedure"];
	if(is_array($procedureArr))
	{$procedure_id = implode(",",$procedureArr);}
	else $procedure_id = $procedureArr;
	$tmpReqProcArr 	= array_values(array_unique(array_filter(explode(",",$procedure_id))));
	$procedure_id 	= implode(",", $tmpReqProcArr);
	
	
	$physicianArr = $_REQUEST["physician"];
	if(is_array($physicianArr))
	{$physicianImp = implode(",",$physicianArr);}
	else $physicianImp = $physicianArr;
	
	if(!$procedure_id) {  $procedure_id = '0';}
	if(!$physicianImp) {  $physicianImp = '0';} 
	else
	{
		if($physicianImp!='all'){
		$physicianQry=" AND pc.surgeonId IN ($physicianImp)";}
		
		if($physicianImp =='all'){
		$physicianQry = $surgeonExclude;}
	}
	
	if( defined('STRING_SEARCH') && constant('STRING_SEARCH')=='YES')
	{	$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.cost_procedure_name)"; 
	}else{
		$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.cost_procedure_id)"; 
	}

	$procIdQry = "";
	if($procedure_id!='all') {
		if( defined('STRING_SEARCH') && constant('STRING_SEARCH')=='YES')
		{
			$procedure_tbl=imw_query("select procedureId, name from procedures where procedureId IN(".$procedure_id.") ");
			$procedure_name = array();
			while($proc=imw_fetch_array($procedure_tbl)){
				array_push($procedure_name,"'".$proc['name']."'");
			}
			$procNameImplode = implode(",",$procedure_name);
			$procIdQry = " AND pc.cost_procedure_name IN(".$procNameImplode.") ";	
			$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.cost_procedure_name)"; 
		}else{
			$procIdQry = " AND pc.cost_procedure_id IN(".$procedure_id.") ";	 
			$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.cost_procedure_id)"; 
		}
			
	}
	
	$qry = "SELECT pc.patientId, pc.patientConfirmationId, pc.dos, pc.ascId, pc.site,
			CONCAT(pt.patient_lname,', ',pt.patient_fname,' ',pt.patient_mname) AS pt_name ,
			pc.surgeonId, pc.cost_procedure_id,
			CONCAT(users.lname,', ',users.fname,' ',users.mname) AS surgeon_name ,
			users.usersId as surgeonID,
			orr.manufacture, orr.lensBrand, orr.model, orr.Diopter
			FROM patientconfirmation pc
			INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.patient_status!='Canceled')
			LEFT JOIN patient_data_tbl pdt ON(pc.patientId = pdt.patient_id)
			LEFT JOIN users ON(users.usersId = pc.surgeonId)
			LEFT JOIN patient_data_tbl pt ON pc.patientId=pt.patient_id
			LEFT JOIN operatingroomrecords orr ON pc.patientConfirmationId=orr.confirmation_id
			$proc_JOIN
			WHERE (pc.dos BETWEEN '".$from_date."' AND '".$to_date."') ".$fac_con."
			AND pc.finalize_status='true' ".$physicianQry."
			ORDER BY users.lname, users.fname ASC, pc.dos DESC , pc.surgery_time ASC";	//die($qry);
	//file_put_contents('qytext.txt',"\n\n ".$qry, FILE_APPEND);
	$res = imw_query($qry)or die(imw_error().' ----- ');
	$recExist=false;//die($qry);
	if(imw_num_rows($res)>0) {
		while($row = imw_fetch_array($res)) {
			$pConfId = $row["patientConfirmationId"];
			if(count(array_intersect($superBillProcIdArr[$pConfId], $tmpReqProcArr)) == count($tmpReqProcArr) && count($superBillProcIdArr[$pConfId]) == count($tmpReqProcArr)){
				$recExist = true;
				break;
			}
		}
	}
	if(imw_num_rows($res)==0 || $recExist == false) {
	?>
  	<form id="resetFormPrefil" name="resetFormPrefil" action="vision_complication_report.php" method="post">
      <input type="hidden" name="date1" value="<?=$_REQUEST["date1"]?>" />
      <input type="hidden" name="date2" value="<?=$_REQUEST["date2"]?>" />
      <input type="hidden" name="procedureImp" value="<?= base64_encode(serialize($_REQUEST["procedure"]))?>" />
      <input type="hidden" name="physicianImp" value="<?=$physicianImp?>" />
      <input type="hidden" name="no_rec" value="true" />
  	</form>	
    
    <script>
    	document.forms.resetFormPrefil.submit();
    </script>
	
	<?PHP	
			exit;
		}
							
	}
	
//create existing record array for given dos range to omit repeated queries while update and showing records
$checkQuery=imw_query("select confirmation_id from vision_success where (dos BETWEEN '".$from_date."' AND '".$to_date."') ")or die(imw_error());
while($checkRes = imw_fetch_object($checkQuery))
{
	$existing[$checkRes->confirmation_id]['confirmation_id']=$checkRes->confirmation_id;
}

if($_POST['config_id'])
{
	
	$config_id_arr=$_POST['config_id'];
	$pt_id_arr=$_POST['pt_id'];
	$dos_arr=$_POST['dos'];
	$asc_id_arr=$_POST['asc_id'];
	$site_arr=$_POST['site'];
	//$vision_arr=$_POST['vision'];
	//$complication_arr=$_POST['complication'];
	$proc_arr=$_POST['proc'];
	$surgeon_arr=$_POST['surgeon'];
	//save records
	foreach($config_id_arr as $key=>$val)
	{
		$visionStatus				=	isset($_POST['vision_'.$val])				?	$_POST['vision_'.$val]				:	''	;
		$complicationStatus	=	isset($_POST['complication_'.$val])	?	$_POST['complication_'.$val]	:	'' ;
		
		if($existing[$val]['confirmation_id'])
		{
			imw_query("update vision_success set vision_20_40='".$visionStatus."', 
					complication='".$complicationStatus."'
					where confirmation_id='$val';")or die(imw_error());	
		}	
		else
		{
			imw_query("insert into vision_success set confirmation_id='$val',
					vision_20_40='".$visionStatus."', 
					complication='".$complicationStatus."', 
					patientId='".$pt_id_arr[$key]."', 
					dos='".$dos_arr[$key]."', 
					ascId='".$asc_id_arr[$key]."', 
					site='".$site_arr[$key]."',
					`procedure`='".$proc_arr[$key]."',
					surgeonId='".$surgeon_arr[$key]."'")or die(imw_error());
		}
	}
	$update="success";
}

//create existing record array for given dos range to omit repeated queries while update and showing records
$checkQuery=imw_query("select confirmation_id, vision_20_40, complication from vision_success where (dos BETWEEN '".$from_date."' AND '".$to_date."') ")or die(imw_error());
while($checkRes = imw_fetch_object($checkQuery))
{
	$existing[$checkRes->confirmation_id]['confirmation_id']=$checkRes->confirmation_id;
	$existing[$checkRes->confirmation_id]['vision_20_40']=$checkRes->vision_20_40;
	$existing[$checkRes->confirmation_id]['complication']=$checkRes->complication;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Vision Success & Complication</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
$spec = "
</head>
<body onLoad=\"MM_preloadImages('images/generate_report_hover.jpg','images/reset_hover.jpg')\">
";
include("common/link_new_file.php");
include_once("no_record.php");
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
	$get_http_path = 'https';
	 }
elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
{
	$get_http_path= 'http';
}									

?>	

<div class="main_wrapper">
	<form name="vision_report_result" id="vision_report_result" action="" method="post">	
    
    <input type="hidden" name="proc_save" id="proc_save" value="<?php echo $_POST['proc_save']?>">
    <input type="hidden" name="procedure" id="procedure" value="<?php echo (is_array($_POST['procedure']))?implode(',',$_POST['procedure']):$_POST['procedure'];?>">
    <input type="hidden" name="physician" id="physician" value="<?php echo (is_array($_POST['physician']))?implode(',',$_POST['physician']):$_POST['physician'];?>">
    <input type="hidden" name="date1" id="date1" value="<?php echo $_POST['date1']?>">
    <input type="hidden" name="date2" id="date2" value="<?php echo $_POST['date2']?>">
    
    	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle">
            
            		<div style="" id="" class="all_content1_slider ">	         
                    
                          <div class="wrap_inside_admin">
                          	<div class="subtracting-head">
                            	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                	<span>Vision Success & Complication</span>
                               	</div>
                          	</div>
                            
                            <div class="wrap_inside_admin scrollable_yes">
       							<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                <thead class="cf">
                                    <tr>
                                        <th class="text-left" width="12%">DOS</th>
                                        <th class="text-left" width="20%">Patient Name - ID</th>
                                        <th class="text-left" width="10%">IOL MAN(Brand)</th>
                                        <th class="text-left" width="10%">IOL Model</th>
                                        <th class="text-left" width="10%">Diopter</th>
                                        <th class="text-left" width="10%">Site</th>
                                        <th class="text-left" width="14%">
                                        	20/40 or Better<br>

                                        	<span style=" font-size:12px;">
                                          	<span class="clearfix visible-sm visible-md"></span>
                                          	<label><input type="checkbox" id="all_20_40" data-default-value="Yes" data-target-field="vision_" /></label>
                                            All (20/40 or Better)
                                         	</span>
                                        </th>
                                        <th class="text-left" width="14%">
                                        	Complication<br>

                                          <span style=" font-size:12px;">
                                          	<span class="clearfix visible-sm visible-md"></span>
                                          	<label><input type="checkbox" id="all_no_complication" data-default-value="No" data-target-field="complication_" /></label>
                                            All (No Complication)
                                         	</span>
                                       	</th>
                                    </tr>
                                </thead>
                                <tbody>
																<?php
																$inSurgeon	=	array();
																$recExist = false;
																$res = imw_query($qry)or die(imw_error().' ----- ');
                                while($data=imw_fetch_object($res))
                                {
																		$recExist = false;
																		$pConfId = $data->patientConfirmationId;
																		if(count(array_intersect($superBillProcIdArr[$pConfId], $tmpReqProcArr)) == count($tmpReqProcArr) && count($superBillProcIdArr[$pConfId]) == count($tmpReqProcArr)){
																			$recExist = true;
																		}
																		if( !$recExist ) continue;
			
																		// APPLYING NUMBERS TO PATIENT SITE
																		if($data->site == 1) {
																			$site = "Left Eye";  //OS
																		}else if($data->site== 2) {
																			$site = "Right Eye";  //OD
																		}else if($data->site== 3) {
																			$site = "Both Eye";  //OU
																		}
																		
																		$visionName		=	'vision_'.$data->patientConfirmationId;
																		$complicationName	=	'complication_'.$data->patientConfirmationId;
																		
																		$displaySurgeonNameRow	=	false;
																		if(!in_array($data->surgeonID,$inSurgeon))
																		{
																				array_push($inSurgeon,$data->surgeonID);
																				$displaySurgeonNameRow	=	true;
																		}
																			
                                ?>
                                			<?php
																				if($displaySurgeonNameRow)
																				{
																					echo '<tr ><td class="text-left" style="background:#333 !important; color:white;" colspan="8"><label>'.$data->surgeon_name.'</label></td></tr>';
																				}
																			?>
                                     <tr>
                                        <td class="text-left low_width_t"> 
                                        <input type="hidden" name="config_id[]" value="<?php echo $data->patientConfirmationId;?>">
                                        <input type="hidden" name="pt_id[]" value="<?php echo $data->patientId;?>">
                                        <input type="hidden" name="dos[]" value="<?php echo $data->dos;?>">
                                        <input type="hidden" name="asc_id[]" value="<?php echo $data->ascId;?>">
                                        <input type="hidden" name="site[]" value="<?php echo $data->site;?>">
                                        <input type="hidden" name="proc[]" value="<?php echo $data->cost_procedure_id;?>">
                                        <input type="hidden" name="surgeon[]" value="<?php echo $data->surgeonId;?>">
                                        
										<?php echo $objManageData->changeDateMDY($data->dos);?></td>
                                        <td class="text-left high_width_t"><?php echo $data->pt_name.' - '.$data->patientId;?></td>
                                        <td class="text-left low_width_t"><?php 
										echo $data->manufacture;
										if($data->lensBrand)echo' ('.$data->lensBrand.')';
										?></td>
                                        <td class="text-left low_width_t"><?php echo $data->model;?></td>
                                        <td class="text-left low_width_t"><?php echo $data->Diopter;?></td>
                                        <td class="text-left low_width_t"><?php echo $site;?></td>
                                        <td class="text-left low_width_t">
                                        <?php
										$vision=$yes_sel=$no_sel='';
                                        if($vision=$existing[$data->patientConfirmationId]['vision_20_40'])
										{
											if($vision=='Yes')$yes_sel='checked';else $no_sel='checked';	
										}
										?>
                                        	<label>
                                        		<input type="checkbox" value="Yes" name="<?=$visionName?>" id="<?=$visionName?>_Y" <?=$yes_sel?> onClick="checkSingle('<?=$visionName?>_Y','<?=$visionName?>');"  />
                                         	</label>&nbsp;Yes
                                          <label style="margin-left:14px;">
                                          	<input type="checkbox" value="No" name="<?=$visionName?>" id="<?=$visionName?>_N" <?=$no_sel?> onClick="checkSingle('<?=$visionName?>_N','<?=$visionName?>');" />
                                          </label>&nbsp;No
                                          <!--<select name="vision[]" class="selectpicker" title="<?php echo $vision;?>">
                                          <option value="">Please Select</option>
                                          <option <?php echo $yes_sel;?>>Yes</option>
                                          <option <?php echo $no_sel;?>>No</option>
                                          </select>-->
                                        </td>
                                        <td class="text-left low_width_t">
                                        <?php
																				$complication=$yes_sel=$no_sel='';
                                        if($complication=$existing[$data->patientConfirmationId]['complication'])
																				{
																					if($complication=='Yes')$yes_sel='checked';else $no_sel='checked';	
																				}
																				?>
                                        	<label>
                                        		<input type="checkbox" value="Yes" name="<?=$complicationName?>" id="<?=$complicationName?>_Y" <?=$yes_sel?> onClick="checkSingle('<?=$complicationName?>_Y','<?=$complicationName?>');"  />
                                         	</label>&nbsp;Yes
                                          <label style="margin-left:14px;">
                                          	<input type="checkbox" value="No" name="<?=$complicationName?>" id="<?=$complicationName?>_N" <?=$no_sel?> onClick="checkSingle('<?=$complicationName?>_N','<?=$complicationName	?>');" />
                                          </label>&nbsp;No
                                          
                                        <!--<select name="complication[]" class="selectpicker" >
                                        <option value="">Please Select</option>
                                        <option <?php echo $yes_sel;?>>Yes</option>
                                        <option <?php echo $no_sel;?>>No</option>
                                        </select>-->
                                        </td>
                                    </tr>
                       		<?php }?>
                                </tbody>
                        </table>
                                </div>
                                <div id="div_innr_btn" class="btn-footer-slider shadow_adjust_above" style="position:static; bottom:0;">
                                <?php if($allowUpdate) { ?>
                                <a href="javascript:void(0)" class="btn btn-info" id="generate_report" onclick="document.vision_report_result.submit();"><b class="fa fa-edit"></b> Update</a>
                                <?php } ?>
                        				<a class="btn btn-default" onclick="resetSearch();"  id="reset">
                                	<b class="fa fa-refresh"></b> Reset Search
                                </a>
                                </div>
                    </div> 
                  </div>  
                  <!-- NEcessary PUSH     -->	 
                  <Div class="push"></Div>
                  <!-- NEcessary PUSH     -->
            </div>
        </div>
        
  	</form>
</div>

<form id="resetForm" name="resetForm" action="vision_complication_report.php" method="post">
	<input type="hidden" name="date1" value="<?=$_REQUEST["date1"]?>" />
  <input type="hidden" name="date2" value="<?=$_REQUEST["date2"]?>" />
  <input type="hidden" name="procedureImp" value="<?= base64_encode(serialize($_REQUEST["procedure"]))?>" />
  <input type="hidden" name="physicianImp" value="<?=$physicianImp?>" />
</form>
<?php 
	if($update == "success")
	{
		?>
			<script>
				modalAlert("Records update successfully!");
			</script>
		<?php
	}
?>
<script>
	function resetSearch()
	{
			document.forms.resetForm.submit();
	}
	$(function(){
			
			$('body').on('click','#all_no_complication, #all_20_40',function(){
				
				var _this	=	$(this);
				var DSV		=	_this.attr('data-default-value');
				var DTF		=	_this.attr('data-target-field');
				var action=	(_this.is(':checked'))	?	true : false;
				
				$('input[name^="'+DTF+'"]').each(function(){
					
						var nam	=	$(this).attr('name'); 
						var obj = $('input[name^="'+nam+'"]:not(:checked)');
						var mat	=	(obj.length == $('input[name^="'+nam+'"]').length) ? false : true;
						if(action)
						{
							obj	= $('input[name^="'+nam+'"]:checked');
							mat	=	(obj.length == 0) ? true : false;;
						}
						
						if(mat)
						{
							var targetID = nam + '_Y';
							if(DSV == 'No')	targetID = nam + '_N';
							$('#'+targetID+'').prop('checked',action);	
						}
						
				});
					
			});
		
	});

</script>