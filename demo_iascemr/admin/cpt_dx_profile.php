<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$diagCodTyp = trim($_REQUEST['diagnosis_code_type']);
if($_REQUEST['dxCode']=='yes')
{ 
	$cType	=	'DX_CODE';
	$title	=	"DX Codes";
	$fieldT	=	"ICD9 Codes";
	if($diagCodTyp=="icd10") {
		$fieldT="ICD10 Codes";
	}
	$requestType="dxCode=yes&diagnosis_code_type=".$diagCodTyp;

}
else if($_REQUEST['cptCode']=='yes')
{
	$cType	=	'CPT_CODE';
	$title	=	"CPT Codes (Surgeon &amp; Facility)";
	$fieldT	=	"CPT Codes";
	$requestType="cptCode=yes";
}
else if($_REQUEST['cptCodeA']=='yes')
{
	$cType	=	'CPT_CODE_A';
	$title	=	"CPT Codes (Anesthesia)";
	$fieldT	=	"CPT Codes ";
	$requestType="cptCodeA=yes";
 }
 
 $pref_card_dx_id_default=$pref_card_dx_id=$pref_card_cpt_id=$pref_card_cpt_id_default = array();
 //get data for prefrence card if any
if($_REQUEST['pref_card'])
{
	$query=imw_query("select * from procedureprofile where procedureId='$_REQUEST[pref_card]'")or die(imw_error());
	if(imw_num_rows($query)>=1)
	{
		$pref_card=imw_fetch_object($query);
		if($diagCodTyp=="icd10")
		{
			$pref_card_dx_id=explode(',',$pref_card->dx_id_icd10);
			$pref_card_dx_id_default=explode(',',$pref_card->dx_id_default_icd10);
		}
		else
		{
			$pref_card_dx_id=explode(',',$pref_card->dx_id);
			$pref_card_dx_id_default=explode(',',$pref_card->dx_id_default);
		}
		
		if($cType == 'CPT_CODE_A')
		{
			$pref_card_cpt_id=explode(',',$pref_card->cpt_id_anes);
			$pref_card_cpt_id_default=explode(',',$pref_card->cpt_id_anes_default);
		}
		else
		{
			$pref_card_cpt_id=explode(',',$pref_card->cpt_id);
			$pref_card_cpt_id_default=explode(',',$pref_card->cpt_id_default);	
		}
	}
}

$pro_id = $_REQUEST['pro_id'];
$cont=$_REQUEST['cnt'];
if($_REQUEST['codeType']){
	
	if($_REQUEST['codeType']=="DX_CODE"){
		$dxCodeDefaultArr = $_REQUEST['dxCodeDefaultChkBox'];
		$dxCodeArr 		  = $_REQUEST['dxCodeChkBox'];
		if($dxCodeDefaultArr) {
			$dxCodeDefaultArrImplode = implode(',',$dxCodeDefaultArr);
		}
		if($dxCodeArr){
			$dxCodeArrImplode = implode(',',$dxCodeArr);
		}
		$fieldName=" dx_id='".$dxCodeArrImplode."', dx_id_default='".$dxCodeDefaultArrImplode."' ";

		//START SET ICD10 DETAIL
		if($diagCodTyp=="icd10") {
			$fieldName=" dx_id_icd10='".$dxCodeArrImplode."', dx_id_default_icd10='".$dxCodeDefaultArrImplode."' ";	
		}
		//END SET ICD10 DETAIL

	}else if($_REQUEST['codeType']=="CPT_CODE"){
		$cptCodeDefaultArr = $_REQUEST['cptCodeDefaultChkBox'];
		$cptCodeArr 	   = $_REQUEST['cptCodeChkBox'];
		if($cptCodeDefaultArr) {
			$cptCodeDefaultArrImplode = implode(',',$cptCodeDefaultArr);
		}
		if($cptCodeArr){
			$cptCodeArrImplode = implode(',',$cptCodeArr);
		}
		$fieldName=" cpt_id='".$cptCodeArrImplode."', cpt_id_default='".$cptCodeDefaultArrImplode."' ";
	}
	
	else if($_REQUEST['codeType']=="CPT_CODE_A"){
		$cptCodeDefaultArr = $_REQUEST['cptCodeDefaultChkBox'];
		$cptCodeArr 	   = $_REQUEST['cptCodeChkBox'];
		if($cptCodeDefaultArr) {
			$cptCodeDefaultArrImplode = implode(',',$cptCodeDefaultArr);
		}
		if($cptCodeArr){
			$cptCodeArrImplode = implode(',',$cptCodeArr);
		}
		$fieldName=" cpt_id_anes='".$cptCodeArrImplode."', cpt_id_anes_default='".$cptCodeDefaultArrImplode."' ";
	}
	
	if($_REQUEST['pro_id']!=""){
		$updateCptDxCodeQry = "update surgeonprofileprocedure set ".$fieldName." where id = '$pro_id'";
		$updatCptDxCode_qry = imw_query($updateCptDxCodeQry) or die(imw_error());		
	}else if($_REQUEST['pro_id']==""){
		if($_REQUEST['cnt']){
			$cont = $_REQUEST['cnt'];
			if($_REQUEST['codeType']=="DX_CODE"){
				echo "<script>
						window.opener.document.getElementById('dxCodeVal".$cont."').value = "."'$dxCodeArrImplode'".";
						window.opener.document.getElementById('dxCodeTyp".$cont."').value = "."'$diagCodTyp'".";
						window.opener.document.getElementById('dxCodeDefaultVal".$cont."').value = "."'$dxCodeDefaultArrImplode'".";
						//window.close();
					</script>";
			}else if($_REQUEST['codeType']=="CPT_CODE"){
				echo "<script>
					window.opener.document.getElementById('cptCodeVal".$cont."').value = "."'$cptCodeArrImplode'".";
					window.opener.document.getElementById('cptCodeDefaultVal".$cont."').value = "."'$cptCodeDefaultArrImplode'".";
					//window.close();
				</script>";
			}else if($_REQUEST['codeType']=="CPT_CODE_A"){
				echo "<script>
					window.opener.document.getElementById('cptCodeAnesVal".$cont."').value = "."'$cptCodeArrImplode'".";
					window.opener.document.getElementById('cptCodeAnesDefaultVal".$cont."').value = "."'$cptCodeDefaultArrImplode'".";
					//window.close();
				</script>";
			}	
		}
	}	
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<meta name="viewport" content="width=device-width, maximum-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<?php include("adminLinkfile.php");?>
		<script>window.focus();</script>
       <script type="text/javascript" src="../js/jquery-1.11.3.js"></script>
		
		<script>
			$(window).load(function() 
			{
				$(".loader").fadeOut(1000).hide(1000); 
				bodySize();
				
				<?php
				if($_REQUEST['codeType']) {
				?>
				top.alert_msg('update');
				<?php
				}
				?>
			});
			$(window).resize(function()
			{
				bodySize();
			});
			
			var bodySize = function()
			{
				var HH	=	$(".header").height();
				var FH	=	$(".footer").height();
				var DH	=	$(window).height();
				var BH	=	DH - ( HH + FH )  - 105;
				//alert('HEader'  + HH + '\n Footer -  ' + FH + '\n Document - ' + DH + '\nBody' + BH);
				
				$(".body").css({'min-height':BH+'px', 'max-height':BH+'px' })
			
			}
			
			//$(window).resize(function(){ size = [1034,630]; window.resizeTo(size[0],size[1]); });
			
			function switch_code_fun(diagnosis_code_type,pro_id,dxCode,cnt) {
				$(".loader").fadeIn(1000).show(1000);
				top.location.href = "cpt_dx_profile.php?pro_id="+pro_id+"&dxCode="+dxCode+"&diagnosis_code_type="+diagnosis_code_type+"&cnt="+cnt+'&pref_card=<?php echo $_REQUEST['pref_card']?>';
			}
		</script>
	</head>
	<body onLoad="defaultCheckedBox1('sds','sd')">
    <!-- Loader -->
		<div class="loader">
			<span><b class='fa fa-spinner fa-pulse' ></b>&nbsp;Loading...</span>
		</div>
		<!-- Loader-->
        <div class="alert alert-success alert-msg " id="alert_success" > <strong>Record(s) Saved Successfully</strong> </div>
        
    <div class="box box-sizing">
        <div class="dialog box-sizing">
            <div class="content box-sizing">
                <div class="header box-sizing text-left ">
                    <b><?php echo $title; ?></b>
                    <?php
					if($_REQUEST['dxCode']=='yes'){ 
					?>
                    <div class="change_temp_div switch_div" style="width:180px;"> 
                            <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-right" for="n_select">
                                     <b>  Switch </b> 
                            </label>
                            <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                <select name="diagnosis_code_type" id="diagnosis_code_type" class="selectpicker form-control bs-select-hidden" onChange="switch_code_fun(this.value,'<?php echo $_REQUEST['pro_id'];?>','<?php echo $_REQUEST['dxCode'];?>','<?php echo $_REQUEST['cnt'];?>');" >
                                    <option value="icd9" <?php if($diagCodTyp=='icd9') { echo "SELECTED";} ?>>ICD9</option>
                                    <option value="icd10" <?php if($diagCodTyp=='icd10') { echo "SELECTED";} ?>>ICD10</option>
                                 </select>
                            </div>
                     </div>
                     <?php
					}
					 ?>
                </div>
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf  table-striped" style="background-color:#F1F4F0;">
                    <tr>
                        <th style="width:10%; text-align:center;">Default</th>
                        <th style="width:10%; text-align:center;">Status</th>
                        <?php if($_REQUEST['dxCode']=='yes'): ?>
							<?php if($diagCodTyp=='icd10'): ?>
                                <th style="width:10%; text-align:center;">ICD9 Codes</th>
                                <th style="width:20%; text-align:center;"><?php echo $fieldT; ?></th>
                                <th style="width:auto; text-align:left;">ICD10 Description</th>
                        	<?php	else: ?>
								<th style="width:20%; text-align:center;""><?php echo $fieldT; ?></th>
                            	<th style="width:auto; text-align:left;">ICD9 Description</th>
                        	<?php	endif; ?>    
                       	<?php	else: ?>	
                        	<th style="width:20%; text-align:left;""><?php echo $fieldT; ?></th>
                            <th style="width:auto; text-align:left;"">Description</th>
						<?php	endif; ?>
                    </tr>
                </table>
				 
				<div class="body">
                <form action="cpt_dx_profile.php?pro_id=<?php echo $_REQUEST['pro_id'];?>&<?php echo $requestType; ?>&cnt=<?php echo $cont;?>" method="post" name="CptDxFrm" class="alignCenter">
			<input type="hidden" value="yes" name="sub" id="sub">
			<input type="hidden" value="<?php echo $cont;?>" name="cont">
			<input type="hidden" value="<?php echo $cType;?>" name="codeType">
			
			<?php  if($_REQUEST['dxCode']=='yes'){?>
					<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped padding_0">
						<?php
							$i=0;
							$ctrColor=0;
							unset($condArr);
							$condArr['1'] = '1';
							$dxCodeDetail = $objManageData->getMultiChkArrayRecords("diagnosis_tbl", $condArr,"diag_code","ASC"," AND del_status !='yes' ");
							//START GET ICD10 DETAIL
							if($diagCodTyp=='icd10') {
								$dxCodeDetail = $objManageData->getMultiChkArrayRecords("icd10_data", $condArr,"icd10","ASC","AND icd10!='' AND deleted ='0' ");	
							}
							//END GET ICD10 DETAIL
							$dxId = $dxCodeDescription = $dxCodeDescriptionSplit = "";
							foreach($dxCodeDetail as $dxCodeData){
								$ctrColor++;
								$i++;
								if($_REQUEST['pro_id']<>'') {
									$selectSurgeonProfileProcedureQry = "select * from surgeonprofileprocedure where id = '".$_REQUEST['pro_id']."' AND procedureName != '' order by procedureName";
									$selectSurgeonProfileProcedureRes = imw_query($selectSurgeonProfileProcedureQry) or die(imw_error());
									$selectSurgeonProfileProcedureRow = imw_fetch_array($selectSurgeonProfileProcedureRes);
									$dxIdGet = $selectSurgeonProfileProcedureRow['dx_id'];
									$dxIdDefaultGet = $selectSurgeonProfileProcedureRow['dx_id_default'];
									//START GET ICD10 FIELDS
									if($diagCodTyp=='icd10') {
										$dxIdGet = $selectSurgeonProfileProcedureRow['dx_id_icd10'];
										$dxIdDefaultGet = $selectSurgeonProfileProcedureRow['dx_id_default_icd10'];
										
									}
									//END GET ICD10 FIELDS
									
									$dxIdExplode=array();
									$dxIdDefaultExplode=array();
									if($dxIdGet){ 	     $dxIdExplode 		 = explode(',',$dxIdGet);  }
									if($dxIdDefaultGet){ $dxIdDefaultExplode = explode(',',$dxIdDefaultGet);  }
								}else{
									if($dxCodeArrImplode){		 $dxIdExplode 	 	 = explode(',',$dxCodeArrImplode); }
									if($dxCodeDefaultArrImplode){$dxIdDefaultExplode = explode(',',$dxCodeDefaultArrImplode);  }
								}
								$dxId									 = $dxCodeData->diag_id; 
								$dxCodeDescription						 = $dxCodeData->diag_code;
								
								//START GET ICD10 FIELDS
								if($diagCodTyp=='icd10') {
									$dxId								 = $dxCodeData->id; 
									$dxCodeDescription					 = $dxCodeData->icd10;
								}
								//END GET ICD10 FIELDS
								list($getDxCode,$getDxDescription)		 = explode(",",$dxCodeDescription);
							?>
								<tr style="background-color:<?php if(($ctrColor%2)!=0) echo '#FFFFFF';?>;">
									<td style="width:10%; text-align:center"><input type="checkbox"  name="dxCodeDefaultChkBox[]" <?php if(in_array($dxId,$dxIdDefaultExplode) || in_array($dxId,$pref_card_dx_id_default)){ ?> checked="checked" <?php } ?> id="dxCodeDefaultChkBox<?php echo $i; ?>"  value="<?php echo $dxId; ?>"  onClick="javascript:if(this.checked==true){document.getElementById('dxCodeChkBox<?php echo $i; ?>').checked=true;}"></td>
									<td style="width:10%; text-align:center"><input type="checkbox" name="dxCodeChkBox[]" <?php if(in_array($dxId,$dxIdExplode) || in_array($dxId,$pref_card_dx_id)){ ?> checked="checked"  <?php } ?> id="dxCodeChkBox<?php echo $i; ?>" value="<?php echo $dxId; ?>" onClick="javascript:if(this.checked==false){document.getElementById('dxCodeDefaultChkBox<?php echo $i; ?>').checked=false;}"></td>
									<?php
                                    if($diagCodTyp=='icd10') {
									?>
                                    	<td style="width:10%; text-align:left"><?php echo $dxCodeData->icd9; ?></td>
                                        <td style="width:20%; text-align:left"><?php echo $getDxCode; ?></td>
                                        <td style="width:auto; text-align:left"><?php echo $dxCodeData->icd10_desc; ?></td>
                                    <?php	
									}else {
									?>
                                    	<td style="width:20%; text-align:left"><?php echo $getDxCode; ?></td>
                                        <td style="width:auto; text-align:left"><?php echo $getDxDescription; ?></td>
                                    <?php	
									}
                                    ?>
									
                                    
								</tr>	
						<?php } ?>
				</table>
		<?php }else if($_REQUEST['cptCode']=='yes' || $_REQUEST['cptCodeA']=='yes'){?>
				<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped padding_0">
					<?php
						$ctrColor=0;
						$j=0;
						$fieldCptId	=	'cpt_id'; $fieldCptIdDefault = 'cpt_id_default';
						$extraCondition = " AND del_status !='yes' ";
						
						$procCatRow	=	$objManageData->getRowRecord('procedurescategory','name','Anesthesia','name','Asc','proceduresCategoryId');
						$AnesthesiaCatId	=	$procCatRow->proceduresCategoryId;
							
						if($_REQUEST['cptCodeA']=='yes'){
							$fieldCptId	=	'cpt_id_anes'; $fieldCptIdDefault = 'cpt_id_anes_default';
							$extraCondition .= " And catID = '".$AnesthesiaCatId."' ";	
						} else{
							$extraCondition .= " And catID <> '".$AnesthesiaCatId."' ";
						}
						
						
						unset($condArr);
						$condArr['1'] = '1';
						$cptCodeDetail = $objManageData->getMultiChkArrayRecords("procedures", $condArr,"code, name","ASC",$extraCondition);
						$cptId = $cptCode = "";
						if(is_array($cptCodeDetail) && count($cptCodeDetail) > 0 )
						{
							foreach($cptCodeDetail as $cptCodeData){
							$ctrColor++;
							$j++;
							if($_REQUEST['pro_id']<>'') {
								$selectSurgeonProfileProcedureQry = "select * from surgeonprofileprocedure where id = '".$_REQUEST['pro_id']."' AND procedureName != '' order by procedureName";
								$selectSurgeonProfileProcedureRes = imw_query($selectSurgeonProfileProcedureQry) or die(imw_error());
								$selectSurgeonProfileProcedureRow = imw_fetch_array($selectSurgeonProfileProcedureRes);
								
								$cptIdGet = $selectSurgeonProfileProcedureRow[$fieldCptId];
								$cptIdDefaultGet = $selectSurgeonProfileProcedureRow[$fieldCptIdDefault];
								$cptIdExplode=array();
								$cptIdDefaultExplode=array();
								if($cptIdGet){		 $cptIdExplode 	      = explode(',',$cptIdGet); }
								if($cptIdDefaultGet){$cptIdDefaultExplode = explode(',',$cptIdDefaultGet); }
							}else{
									if($cptCodeArrImplode){		 $cptIdExplode 	 	 = explode(',',$cptCodeArrImplode); }
									if($cptCodeDefaultArrImplode){$cptIdDefaultExplode = explode(',',$cptCodeDefaultArrImplode);  }
							}
							$cptId	 = $cptCodeData->procedureId; 
							$cptCode = $cptCodeData->code;
							$cptName = $cptCodeData->name;
							if($cptCode){
						?>
							<tr style="background-color:<?php if(($ctrColor%2)!=0) echo '#FFFFFF';?>;">
								<td style="width:10%; text-align:center;"><input type="checkbox" name="cptCodeDefaultChkBox[]" id='cptCodeDefaultChkBox<?php echo $j; ?>' <?php if(in_array($cptId,$cptIdDefaultExplode) || in_array($cptId,$pref_card_cpt_id_default)){ ?>  checked="checked"  <?php } ?> value="<?php echo $cptId; ?>" onClick="javascript:if(this.checked==true){document.getElementById('cptCodeChkBox<?php echo $j; ?>').checked=true;}"></td>
								<td style="width:10%; text-align:center;"><input type="checkbox" name="cptCodeChkBox[]" id='cptCodeChkBox<?php echo $j; ?>'  <?php if(in_array($cptId,$cptIdExplode) || in_array($cptId,$pref_card_cpt_id)){ ?>  checked="checked"  <?php } ?> value="<?php echo $cptId; ?>" onClick="javascript:if(this.checked==false){document.getElementById('cptCodeDefaultChkBox<?php echo $j; ?>').checked=false;}"></td>
								<td style="width:20%; text-align:left"><?php echo $cptCode; ?></td>
                                <td style="width:auto; text-align:left"><?php echo $cptName; ?></td>
							</tr>	
					<?php	}
						} 
						}
					?>
				</table>
		<?php } ?>			
			
		</form>
                </div>
                <div class="footer text-center">
                <a class="btn btn-primary" href="javascript:void(0)" id="saveButton" onClick="document.CptDxFrm.submit();">  <b class="fa fa-floppy-o" ></b>&nbsp;Save</a>
                <a class="btn btn-primary" href="javascript:void(0)"  onclick="window.close();" id="closeButtonIOL" >  <b class="fa fa-close" ></b>&nbsp;Close</a>
                </div>
            </div>
        </div><div>
				
		</div>
    </div>
   
		
	</body>
</html>