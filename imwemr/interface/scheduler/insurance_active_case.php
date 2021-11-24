<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
/*
File: insurance_active_case.php
Purpose: Get active insurance cases
Access Type: Direct
*/

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');

if($_POST["counterArray"]>0){
	for($cins=0;$cins<$_POST["counterArray"];$cins++){
	$updateQuery="update insurance_data set provider='".$_POST["tmp_provider_id".$cins]."', policy_number='".addslashes($_POST["tmp_policy_number".$cins])."',group_number='".addslashes($_POST["tmp_group_number".$cins])."',copay='".addslashes($_POST["tmp_copay".$cins])."' where id='".$_POST["primaryMainId".$cins]."' and ins_caseid='".$_POST["ins_caseid".$cins]."'";
	$res=imw_query($updateQuery);
	//Update Auth amount
	if(trim($_POST["tmp_authAmmount".$cins])!=""){
		$updateAuthTable=imw_query("update patient_auth set AuthAmount='".floatval($_POST["tmp_authAmmount".$cins])."' where ins_data_id='".$_POST["primaryMainId".$cins]."' and ins_case_id='".$_POST["ins_caseid".$cins]."'");
	}
	//End Update Auth amount
	
	}
}
//print_r($_POST); policy_number  group_number  type  id
//$insurenceCompniesData = $objManageData2->getInsurenceCompaniesData();

//---- Re arrange the ins provider ---------
if($re_arrange_btn){
	$pid = $_REQUEST['curr_pt_id'];
	$ins_arr = array('Primary','Secondary','Tertiary');
	for($i=0;$i<count($compId);$i++){
		$id = $compId[$i];
		if(empty($id) == false){
			$id = $compId[$i];
			$name_data_id_arr = preg_split('/__/',$_REQUEST['name_'.$ins_arr[$i]]);
			$name = $name_data_id_arr[0];
			$data_id = $name_data_id_arr[1];
			$qry = "select type,provider,referal_required,auth_required,ins_caseid 
					from insurance_data where id = '$data_id'";
			$qryId = imw_query($qry);
			$qryRes = imw_fetch_assoc($qryId);
			$qry = "update insurance_data set type = '$name' where id = '$data_id'";
			imw_query($qry);
			switch($name){
				case 'primary':
					$new_reff_type = 1;
				break;
				case 'secondary':
					$new_reff_type = 2;
				break;
				case 'tertiary':
					$new_reff_type = 3;
				break;
				
			}	
			if(imw_num_rows($qryId) > 0){
				$type = $qryRes['type'];
				$provider = $qryRes['provider'];
				$ins_caseid = $qryRes['ins_caseid'];
				switch($type){
					case 'primary':
						$old_reff_type = 1;
					break;
					case 'secondary':
						$old_reff_type = 2;
					break;
					case 'tertiary':
						$old_reff_type = 3;
					break;
					
				}
				//--- REFERRAL PROVIDER SWITCH ------
				if(strtolower($qryRes['referal_required']) == 'yes'){					
					$qry = "update patient_reff set reff_type = '$new_reff_type' where ins_data_id = '$data_id'
							and ins_provider = '$provider' and patient_id = '$pid'
							and reff_type = '$old_reff_type'";
					imw_query($qry);
				}
				//--- AUTH REQUIRED SWITCH ------
				if(strtolower($qryRes['auth_required']) == 'yes'){
					 $qry = "update patient_auth set ins_type = '$new_reff_type' where 
							patient_id = '$pid' and ins_case_id = '$ins_caseid'";
					imw_query($qry);
				}
			}

		}
	}
}
$insCompName = array();
$getInsCompTypeAhead = "select id,name,in_house_code,contact_address,City,State,Zip from insurance_companies order by in_house_code asc";
$rsInsCompTypeAhead  = imw_query($getInsCompTypeAhead); 
if($rsInsCompTypeAhead){
	while ($row = imw_fetch_array($rsInsCompTypeAhead)){
		if($row['in_house_code']!=""){
				$insCompName[] = "'".addslashes(trim($row['in_house_code']).'-'.trim($row['id'])."-".trim($row['contact_address']).'-'.trim($row['City']).','.trim($row['State']).' '.trim($row['Zip']))."'";
			}
			else{				
				$insCompName[] ="'".addslashes(trim($row['name']).'-'.trim($row['id'])."-".trim($row['contact_address']).'-'.trim($row['City']).','.trim($row['State']).' '.trim($row['Zip']))."'";
			}	
	}	
	imw_free_result($rsInsCompTypeAhead);
	if(count($insCompName)>0){
		$strAllInsComp=implode(',',$insCompName);
	}
}
	
?><script language="javascript" type="text/javascript">	var t;	function closeInsDiv(){	t = setTimeout('close_div()',200);}
function close_div(){document.getElementById("insActiveCase").style.display = 'none';}
function cleartimeout(){clearTimeout(t);}
function closeWindow(){	document.getElementById("insActiveCase").style.display = "none";}
function getToolTip(id,ins_name){if(id){xmlHttp=GetXmlHttpObject();	if (xmlHttp==null){alert ("Browser does not support HTTP Request");return;} 
var url="insuranceResult.php?id="+id;xmlHttp.onreadystatechange=stateChanged; xmlHttp.open("GET",url,true);xmlHttp.send(null);var curPos = getPosition();
document.getElementById("insActiveCase").style.top =  curPos.y;document.getElementById("insActiveCase").style.left = curPos.x;}}
function stateChanged(){ if(xmlHttp.readyState==4){ document.getElementById("insActiveCase").innerHTML=xmlHttp.responseText;			document.getElementById("insActiveCase").style.display = "block";}else{ document.getElementById("insActiveCase").innerHTML="Wait"; document.getElementById("insActiveCase").style.display = "block";}}
var strAllInsComp = "";<?php
	if($strAllInsComp!=""){
	?>var strAllInsComp = new Array(<?php echo fnLineBrk($strAllInsComp); ?>);<?php
	}	
?>	
function setInsuranceAutoFill(objNumber,obj){var strString=obj.value;if(strString!=""){	var strArray=strString.split("-");if(document.getElementById("tmp_provider_id"+objNumber) && strArray[1]){document.getElementById("tmp_provider_id"+objNumber).value=strArray[1];} 			if(document.getElementById("tmp_provider_name"+objNumber) && strArray[0]){document.getElementById("tmp_provider_name"+objNumber).value=strArray[0];}} EnableSaveButton();}<?php if(count($_POST["counterArray"])>0){?>top.fmain.get_copay('del_notes_val');<?php } ?>
function setInsuranceReArrange(){top.fmain.re_arrange_id.innerHTML=document.getElementById("re_arrange_id").innerHTML;top.fmain.re_arrange_ins('block');}
</script><div id="insActiveCase" class="text_10b" onMouseOver="cleartimeout(t);" onMouseOut="closeInsDiv();" style="display:none; background-color:#FFCC66; position:absolute; border:1 solid blue; top:40px;"></div><form name="frmFrontdeskInsurance" id="frmFrontdeskInsurance" action="" method="post" onsubmit="">	
<table class="table table-striped table-bordered table-hover"><input type="hidden" id="current_caseids" name="current_caseids" value="<?php echo($_REQUEST["current_caseids"]);?>"><input type="hidden" id="current_patient_id" name="current_patient_id" value="<?php echo $_SESSION['patient'];?>" /><?php
	$qry_case = "SELECT ins_caseid FROM insurance_case WHERE ins_caseid = '".$_REQUEST["current_caseids"]."'";
	
	$res_case = imw_query($qry_case);	
	
	if(imw_num_rows($res_case) > 0){
		
	//Code To Get Insurance Details //
	$qryINSAll ="SELECT ic.*, ic.id as idc, insurance_data.* FROM insurance_data, insurance_companies AS ic
	WHERE insurance_data.pid ='".$_SESSION['patient']."'
	AND insurance_data.provider = ic.id AND insurance_data.ins_caseid ='".$_REQUEST["current_caseids"]."' and insurance_data.actInsComp=1 ORDER BY type";		

	$qryINSALL =imw_query($qryINSAll);			
	$insCounter=0;
	$validInsComArr = array();
	if(imw_num_rows($qryINSALL)>0){
	while($qryINSResALL = imw_fetch_array($qryINSALL)){	
	$insCaseName=get_insurance_case_name_schedule($qryINSResALL['ins_caseid'],"Yes");
	if($insCaseName!=""){
		$subtstrNameInsArray=explode("-",$insCaseName);//substr($insCaseName,0,6);
		$subVisionYesNo=$subtstrNameInsArray[2];
	}
	$authRisationNumber="";
	$authRefLable="Ref#";
	$priFlag="";
	$secFlag=""; 
	$terFlag="";
	$id = $qryINSResALL["id"];
	$idc = $qryINSResALL["idc"];
	$rco_code_id = $qryINSResALL["rco_code"];
	$referal_required=$qryINSResALL["referal_required"];
	if(ucfirst($qryINSResALL["type"])=="Primary"){
			$priFlag = getReferralFlagFrontdesk(1,$id,'primary');
			$qryType=1;
			$primaryInsCompanyName=$qryINSResALL["in_house_code"];
			$primaryInsCompanyId=$qryINSResALL["provider"];
			if($qryINSResALL['actInsComp'] == 1){
				$validInsComArr['primary'] = $qryINSResALL['id'];
			}
		}
		if(ucfirst($qryINSResALL["type"])=="Secondary"){
			$secFlag = getReferralFlagFrontdesk(2,$id,'secondary');
			$qryType=2;
			$secInsCompanyName=$qryINSResALL["in_house_code"];
			$secInsCompanyId=$qryINSResALL["provider"];
			if($qryINSResALL['actInsComp'] == 1){
				$validInsComArr['secondary'] = $qryINSResALL['id'];
			}
		}
		if(ucfirst($qryINSResALL["type"])=="Tertiary"){
			$terFlag = getReferralFlagFrontdesk(3,$id,'tertiary');
			$qryType=3;
			$terInsCompanyName=$qryINSResALL["in_house_code"];
			$terInsCompanyId=$qryINSResALL["provider"];
			if($qryINSResALL['actInsComp'] == 1){
				$validInsComArr['tertiary'] = $qryINSResALL['id'];
			}
		}
	if($subVisionYesNo==1){
		$AUTHamountReadonly="";
		$authRefLable="Auth#";
		$expired=""	;

		$fet_auth=imw_query("select a_id,auth_name,auth_date,auth_comment,auth_operator,AuthAmount from patient_auth where patient_id='".$_SESSION['patient']."' and ins_type='".$qryType."' order by a_id desc");
		$row_auth=imw_fetch_array($fet_auth);
		$authRisationNumber=$row_auth["auth_name"];
		$AuthAmount=$row_auth["AuthAmount"];
	  }else{
	   $qryREF = "SELECT * FROM `patient_reff` WHERE reff_type ='".$qryType."' and 
			ins_data_id = '".$qryINSResALL["id"]."'
			order by effective_date desc,reff_id desc limit 0,1";
		$qryIdRES = imw_query($qryREF);							 
		$reffRes =imw_fetch_array($qryIdRES);
		 $authRisationNumber=$reffRes["reffral_no"];
		if(($reffRes["end_date"] < date('Y-m-d') && $reffRes["end_date"] != '0000-00-00') || ($reffRes["no_of_reffs"] == 0 && $reffRes["reff_used"] > 0))
		{
			$expired=' style="color: red" title="Expired"';
		}
		 $AUTHamountReadonly=" readonly=\"readonly\"  style=\"pointer-events: none\"";

	  }
	if($insCounter==0){?><thead><tr class="tophead"><th><strong>Ins. Carrier</strong></th><th><strong>Policy#</strong></th><th><strong>Group #</strong></th><th><strong>CoPay</strong></th><th><strong>Type</strong></th><th><strong><?php print ($authRefLable);?></strong></th><th><strong>Auth.Amt.</strong></th></tr></thead><tbody><?php }
	?><input type="hidden" name="tmp_provider_id<?php echo($insCounter);?>" id="tmp_provider_id<?php echo($insCounter);?>" value="<?php print $qryINSResALL["provider"];?>" data-referral="<?php print $qryINSResALL["ref_management"];?>"><input type="hidden" name="ins_caseid<?php echo($insCounter);?>" id="ins_caseid<?php echo($insCounter);?>" value="<?php print $qryINSResALL["ins_caseid"];?>" ><input type="hidden" name="primaryMainId<?php echo($insCounter);?>" id="primaryMainId<?php echo($insCounter);?>" value="<?php print $qryINSResALL["id"];?>" ><tr><td data-label=""><input type="text" id="tmp_provider_name<?php echo($insCounter);?>"  name="tmp_provider_name<?php echo($insCounter);?>" value="<?php print $ins_comp=($qryINSResALL["in_house_code"]!=''?$qryINSResALL["in_house_code"]:$qryINSResALL["name"]);?>" onChange="javascript: setInsuranceAutoFill('<?php echo($insCounter);?>',this);" onmouseover="getToolTip('<?php print $idc; ?>', '<?php echo $rco_code_id; ?>')" onmouseout="hideToolTip();" onClick="javascript:EnableSaveButton();" class="form-control" readonly="readonly"/> <!----></td><td data-label=""><input type="text" id="tmp_policy_number<?php echo($insCounter);?>" name="tmp_policy_number<?php echo($insCounter);?>" value="<?php print $qryINSResALL["policy_number"];?>"  onClick="javascript:EnableSaveButton();" class="form-control"/></td><td data-label=""><input type="text" id="tmp_group_number<?php echo($insCounter);?>" name="tmp_group_number<?php echo($insCounter);?>" value="<?php print $qryINSResALL["group_number"];?>" onClick="javascript:EnableSaveButton();" class="form-control"/></td><td data-label=""><input type="text" id="tmp_copay<?php echo($insCounter);?>" name="tmp_copay<?php echo($insCounter);?>" value="<?php print $qryINSResALL["copay"];?>" class="form-control"/></td><td data-label=""><input type="text" id="tmp_type<?php echo($insCounter);?>"  name="tmp_type<?php echo($insCounter);?>"  value="<?php print ucfirst(substr($qryINSResALL["type"],0,3));?>"  onClick="javascript:EnableSaveButton();" readonly="readonly" class="form-control" style="pointer-events: none" /></td><td data-label="<?php print $authRisationNumber;?>"><!--<input type="text" id="tmp_Referral<?php echo($insCounter);?>" name="tmp_Referral<?php echo($insCounter);?>"  value="<?php print $authRisationNumber;?>" onClick="javascript:EnableSaveButton();"  readonly="readonly" class="form-control" style="width: 90%; pointer-events: none" />--><input type="hidden" id="tmp_Referral<?php echo($insCounter);?>" name="tmp_Referral<?php echo($insCounter);?>"  value="<?php print $authRisationNumber;?>"/><span <?php echo $expired;?>><?php print $authRisationNumber;?></span><?php if($referal_required=="Yes"){
	if($priFlag != ''){
	?><img  src="<?php echo $GLOBALS['webroot'];?>/library/images/<?php print $priFlag; ?>.gif"  alt="Primary Referrals" title="Primary Referrals" border="0"   align="absmiddle" tbl="pri">&nbsp;
	<?php }if($secFlag != ''){
	?><img src="<?php echo $GLOBALS['webroot'];?>/library/images/<?php print $secFlag; ?>.gif"  border="0" alt="Secondary Referrals" title="Secondary Referrals"  align="absmiddle"tbl="sec">&nbsp;<?php
	}
	if($terFlag != ''){
	?><img src="<?php echo $GLOBALS['webroot'];?>/library/images/<?php print $terFlag; ?>.gif"  alt="Tertiary Referrals" title="Tertiary Referrals" border="0"  align="absmiddle" tbl="ter">&nbsp;<?php }
	} ?></td><td data-label=""><input type="text" id="tmp_authAmmount<?php echo($insCounter);?>" name="tmp_authAmmount<?php echo($insCounter);?>"  value="<?php print $AuthAmount;?>"  <?php echo($AUTHamountReadonly);?> onClick="javascript:EnableSaveButton();" class="form-control"/></td></tr><script>	new actb(document.getElementById('tmp_provider_name<?php echo($insCounter);?>'),strAllInsComp);	</script><?php $insCounter++; }
	}else{?><tr> <td colspan="8"><b>No active case present.</b></td></tr><?php }
	}else{?><tr><td colspan="8"><b>No active case present.</b></td></tr><?php } ?><input type="hidden" name="counterArray" id="counterArray" value="<?php echo($insCounter);?>"><div style="display:none"><input type="submit" value="Sbmt" name="insSbmtBtn" id="insSbmtBtn" /></div></tbody></table></form><div class="div_popup" style="text-align:left; display: none" id="ins_show_div"></div><div id="re_arrange_id" style="left:50px; display:none; height:150px; width:570px; top:0px; z-index:344;position:absolute" class="bgcolor"><form name="reArrangeFrm" action="insuranceActiveCase.php" method="post" target="frontdeskInsuranceifrm" onSubmit="re_arrange_ins('none');"><input type="hidden" id="new_case_id" name="new_case_id" value="<?php print $_REQUEST["current_caseids"]; ?>" ><input type="hidden" id="curr_pt_id" name="curr_pt_id" value="<?php print $_SESSION['patient']; ?>" ><input type="hidden" id="current_caseids_2" name="current_caseids" value="<?php print $_REQUEST["current_caseids"]; ?>" ><input type="hidden" name="compId[]" value="<?php print $primaryInsCompanyId; ?>" ><input type="hidden" name="compId[]" value="<?php print $secInsCompanyId; ?>" ><input type="hidden" name="compId[]" value="<?php print $terInsCompanyId; ?>" ><table class="table table-striped table-bordered table-hover"><tr><th class="tophead" colspan="3">Re-arrange insurance provider</th></tr><?php	
			$tableData = '';
			$ins_arr = array('Primary'=>$primaryInsCompanyName,'Secondary'=>$secInsCompanyName,'Tertiary'=>$terInsCompanyName);
			$ins_arr_keys = array_keys($ins_arr);
			for($i=0;$i<count($ins_arr_keys);$i++){
				$val = $ins_arr_keys[$i];
				$com_name = $ins_arr[$val];
					
				//if(empty($com_name) == false){
					$pri_sel = '';
					if($i == 0 and $com_name != ''){
						$pri_sel = 'checked="checked"';
					}
					$sec_sel = '';
					if($i == 1 and $com_name != ''){
						$sec_sel = 'checked="checked"';
					}
					$ter_sel = '';
					if($i == 2 and $com_name != ''){
						$ter_sel = 'checked="checked"';
					}
					$insurance_data_id = $validInsComArr[strtolower($val)];
					$tableData .= <<<DATA
						<tr><td>$val Ins.</td><td>$com_name</td><td><input type="radio" id="name_p_$i" name="name_$val" $pri_sel value="primary__$insurance_data_id" onClick="switch_ins('$val','Primary');" style="cursor:pointer;">Primary &nbsp;<input type="radio" id="name_s_$i" name="name_$val" $sec_sel value="secondary__$insurance_data_id" onClick="switch_ins('$val','Secondary');" style="cursor:pointer;">Secondary &nbsp;<input type="radio" id="name_t_$i" name="name_$val" $ter_sel value="tertiary__$insurance_data_id" onClick="switch_ins('$val','Tertiary');" style="cursor:pointer;">Tertiary &nbsp;<td></tr>
DATA;
				}
			//}
			print $tableData;
		?><tr><td colspan="3"><input type="submit" name="re_arrange_btn" value="Submit" class="dff_button" id="re_arrange_btn" onMouseOver="button_over('re_arrange_btn')" onMouseOut="button_over('re_arrange_btn','')"><input type="button" name="btn_close" value="Close" class="dff_button" id="btn_close" onClick="re_arrange_ins('none')" onMouseOver="button_over('btn_close')" onMouseOut="button_over('btn_close','')"></td></tr></table></form></div>