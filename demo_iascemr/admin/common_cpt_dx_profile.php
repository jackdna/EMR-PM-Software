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
 

$tblName	=	base64_decode($_REQUEST['t']);
$keyField	=	base64_decode($_REQUEST['k']);
$pro_id		=	$_REQUEST['pro_id'];

// Save Dx/Cpt Code 
if($_REQUEST['codeType'])
{
	if($_REQUEST['codeType']=="DX_CODE"){
		$dxCodeDefaultArr	= $_REQUEST['dxCodeDefaultChkBox'];
		$dxCodeArr 		  	= $_REQUEST['dxCodeChkBox'];
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

	}
	else if($_REQUEST['codeType']=="CPT_CODE"){
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
		$updateCptDxCodeQry = "Update ".$tblName." Set ".$fieldName." Where ".$keyField." = '".$pro_id."'";
		$updatCptDxCode_qry = imw_query($updateCptDxCodeQry) or die(imw_error());		
	}
	
	else if($_REQUEST['pro_id']=="")
	{
		if($_REQUEST['codeType']=="DX_CODE")
		{
			$dxIdField	=	($diagCodTyp=="icd10") ?	'dx_id_icd10' :  'dx_id'; 
			$dxIdDField	=	($diagCodTyp=="icd10") ?	'dx_id_default_icd10' : 'dx_id_default'; 
				echo "<script>
								window.opener.document.getElementById('".$dxIdField."').value = "."'$dxCodeArrImplode'".";
								window.opener.document.getElementById('".$dxIdDField."').value = "."'$dxCodeDefaultArrImplode'".";
						</script>";
		}
		else if($_REQUEST['codeType']=="CPT_CODE")
		{
				echo		"<script>
								window.opener.document.getElementById('cpt_id').value = "."'$cptCodeArrImplode'".";
								window.opener.document.getElementById('cpt_id_default').value = "."'$cptCodeDefaultArrImplode'".";
						 	</script>";
		}
		else if($_REQUEST['codeType']=="CPT_CODE_A")
		{
				echo		"<script>
								window.opener.document.getElementById('cpt_id_anes').value = "."'$cptCodeArrImplode'".";
								window.opener.document.getElementById('cpt_id_anes_default').value = "."'$cptCodeDefaultArrImplode'".";
						 	</script>";
		}	
	}
	
		
}
// End Save Dx/Cpt Code 
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
			
			function switch_code_fun(diagnosis_code_type,pro_id,dxCode,t,k) {
				$(".loader").fadeIn(1000).show(1000);
				top.location.href = "?t="+t+"&k="+k+"&pro_id="+pro_id+"&dxCode="+dxCode+"&diagnosis_code_type="+diagnosis_code_type;
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
                    
					<?php	if($_REQUEST['dxCode']=='yes'): ?>
                    <div class="change_temp_div switch_div" style="width:180px;">
                    	<label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-right" for="n_select">
                        	<b>  Switch </b> 
                      	</label>
                        <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                        	<select name="diagnosis_code_type" id="diagnosis_code_type" class="selectpicker form-control bs-select-hidden" onChange="switch_code_fun(this.value,'<?php echo $_REQUEST['pro_id'];?>','<?php echo $_REQUEST['dxCode'];?>','<?=$_REQUEST['t']?>','<?=$_REQUEST['k']?>');" >
                            	<option value="icd9" <?php if($diagCodTyp=='icd9') { echo "SELECTED";} ?>>ICD9</option>
                                <option value="icd10" <?php if($diagCodTyp=='icd10') { echo "SELECTED";} ?>>ICD10</option>
                           	</select>
                       	</div>
                     </div>
                     <?php endif; ?>
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
                	<form action="common_cpt_dx_profile.php?t=<?php echo $_REQUEST['t'];?>&k=<?php echo $_REQUEST['k'];?>&pro_id=<?php echo $_REQUEST['pro_id'];?>&<?php echo $requestType; ?>" method="post" name="CptDxFrm" class="alignCenter">
                    	<input type="hidden" value="yes" name="sub" id="sub">
						<input type="hidden" value="<?php echo $cType;?>" name="codeType">
                        <?php
							// Get Existing Values From Template  and on Form Submit
								
								if( $_REQUEST['pro_id'] <> '' )
								{
									$fields = "*";
									if($_REQUEST['dxCode']=='yes')
									{
										$fields	=	(($diagCodTyp=='icd10' ) ? 
														'dx_id_icd10 as cptDxId, dx_id_default_icd10 as cptDxIdDefault' : 
														'dx_id as cptDxId, dx_id_default as cptDxIdDefault');	
									}
									elseif($_REQUEST['cptCode']=='yes')
									{
										$fields	=	'cpt_id as cptDxId, cpt_id_default as cptDxIdDefault  ';
									}
									elseif($_REQUEST['cptCodeA']=='yes')
									{
										$fields	=	'cpt_id_anes as cptDxId, cpt_id_anes_default as cptDxIdDefault  ';	
									}
									
									$selectTemplateQry = "Select ".$fields." From ".$tblName." Where ".$keyField." = '".$_REQUEST['pro_id']."' ";
									$selectTemplateSql = imw_query($selectTemplateQry) or die(imw_error());
									$selectTemplateRow = imw_fetch_array($selectTemplateSql);
									
									$cptDxId 		= $selectTemplateRow['cptDxId'];
									$cptDxIdDefault = $selectTemplateRow['cptDxIdDefault'];
								}
								else
								{
									$cptDxId 		= ($_REQUEST['dxCode']=='yes') ? $dxCodeArrImplode	:	$cptCodeArrImplode;
									$cptDxIdDefault	= ($_REQUEST['dxCode']=='yes') ? $dxCodeDefaultArrImplode : $cptCodeDefaultArrImplode;
									
								}
								
								
								$cptDxIdExplode = $cptDxDefaultExplode = array() ;
								if($cptDxId) 
								{	
									$cptDxIdExplode 	 	= explode(',',$cptDxId);
									$cptDxDefaultExplode	= explode(',',$cptDxIdDefault);  	
								}
								
							// End Get Existing Values From Template and on Form Submit
						
						?>
						<?php if($_REQUEST['dxCode']=='yes'){ ?>
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped padding_0">
                        <?php
								$i	=	0;
								$ctrColor	=	0;
								unset($condArr);
								$condArr['1']	= '1';
								$dxCodeDetail = $objManageData->getMultiChkArrayRecords("diagnosis_tbl", $condArr,"diag_code","ASC"," AND del_status !='yes' ");
								//START GET ICD10 DETAIL
								if($diagCodTyp=='icd10') {
									$dxCodeDetail = $objManageData->getMultiChkArrayRecords("icd10_data", $condArr,"icd10","ASC","AND icd10!='' AND deleted ='0' ");	
								}
								//END GET ICD10 DETAIL
								$dxId = $dxCodeDescription = $dxCodeDescriptionSplit = "";
								
								foreach($dxCodeDetail as $dxCodeData)
								{
									$ctrColor++;
									$i++;
									$dxId = ($diagCodTyp=='icd10') ? $dxCodeData->id :	$dxCodeData->diag_id	;
									$dxCodeDescription = ($diagCodTyp=='icd10') ? $dxCodeData->icd10 	:	$dxCodeData->diag_code	;
								
									list($getDxCode,$getDxDescription)	=	explode(",",$dxCodeDescription);
							?>
								<tr style="background-color:<?php if(($ctrColor%2)!=0) echo '#FFFFFF';?>;">
									<td style="width:10%; text-align:center"><input type="checkbox"  name="dxCodeDefaultChkBox[]" <?php if(in_array($dxId,$cptDxDefaultExplode)){ ?> checked="checked" <?php } ?> id="dxCodeDefaultChkBox<?php echo $i; ?>"  value="<?php echo $dxId; ?>"  onClick="javascript:if(this.checked==true){document.getElementById('dxCodeChkBox<?php echo $i; ?>').checked=true;}"></td>
									<td style="width:10%; text-align:center"><input type="checkbox" name="dxCodeChkBox[]" <?php if(in_array($dxId,$cptDxIdExplode)){ ?> checked="checked"  <?php } ?> id="dxCodeChkBox<?php echo $i; ?>" value="<?php echo $dxId; ?>" onClick="javascript:if(this.checked==false){document.getElementById('dxCodeDefaultChkBox<?php echo $i; ?>').checked=false;}"></td>
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
		<?php }
						else if($_REQUEST['cptCode']=='yes' || $_REQUEST['cptCodeA']=='yes' ){?>
				<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped padding_0">
					<?php
						$ctrColor=0;
						$j=0;
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
							
							$cptId	 = $cptCodeData->procedureId; 
							$cptCode = $cptCodeData->code;
							$cptName = $cptCodeData->name;
							if($cptCode){
						?>
							<tr style="background-color:<?php if(($ctrColor%2)!=0) echo '#FFFFFF';?>;">
								<td style="width:10%; text-align:center;">
                                	<input type="checkbox" name="cptCodeDefaultChkBox[]" id='cptCodeDefaultChkBox<?php echo $j; ?>' <?php if(in_array($cptId,$cptDxDefaultExplode)){ ?>  checked="checked"  <?php } ?> value="<?php echo $cptId; ?>" onClick="javascript:if(this.checked==true){document.getElementById('cptCodeChkBox<?php echo $j; ?>').checked=true;}">
                              	</td>
								<td style="width:10%; text-align:center;"><input type="checkbox" name="cptCodeChkBox[]" id='cptCodeChkBox<?php echo $j; ?>'  <?php if(in_array($cptId,$cptDxIdExplode)){ ?>  checked="checked"  <?php } ?> value="<?php echo $cptId; ?>" onClick="javascript:if(this.checked==false){document.getElementById('cptCodeDefaultChkBox<?php echo $j; ?>').checked=false;}"></td>
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
                	<a class="btn btn-success" href="javascript:void(0)" id="saveButton" onClick="document.CptDxFrm.submit();">  <b class="fa fa-floppy-o" ></b>&nbsp;Save</a>
                    <a class="btn btn-danger" href="javascript:void(0)"  onclick="window.close();" id="closeButtonIOL" >  <b class="fa fa-close" ></b>&nbsp;Close</a>
              	</div>
                
            </div>
        </div><div>
				
		</div>
    </div>
   
		
	</body>
</html>