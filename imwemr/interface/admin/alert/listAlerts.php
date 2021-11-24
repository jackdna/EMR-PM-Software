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
require_once('../admin_header.php');

$unit_array = array("","mg","%"); //array("","cc","mg","ml");
$arrQuantity = array("","Tabs","ml","cc");
$eye_array = array("PO","OU","OS","OD","RLL","RUL","LLL","LUL","O/O","IV","IM","Topical","L/R Ear","Both Ears");
$use_array= array("qd","qhs","qAM","qid","bid","tid","qod","__hrs","__Xdaily");		
$substitute_array = array("Permissible","Not Permissible","Brand");	
if($_REQUEST['delId']){
	$_REQUEST['delId']=preg_replace('/[^0-9]+/','',$_REQUEST['delId']);
	$qryDelSiteCare = "DELETE FROM alert_tbl WHERE alertId = '".$_REQUEST['delId']."' ";
	$rsDelSiteCare	= imw_query($qryDelSiteCare);	
}

$qryScpAccess="Select id,scp_status,user_access from scp_access LIMIT 1";
$resScpAccess=imw_query($qryScpAccess);
$scpId=0;

$_REQUEST['siteCareStatus'] = (isset($_REQUEST['siteCareStatus']))?xss_rem($_REQUEST['siteCareStatus'], 1):false;

 if($_REQUEST['siteCareStatus'] && (!$_REQUEST['delId'])){
	$_REQUEST['siteCareStatus']=preg_replace('/[^A-Za-z0-9 \_]+/','',$_REQUEST['siteCareStatus']);
	$qry=" INSERT INTO ";	
	if(imw_num_rows($resScpAccess)>0){
		$resultScpAccess=imw_fetch_array($resScpAccess);
		$qry=" UPDATE ";
		//Start Audit
			$patientCcHistoryFields = make_field_type_array('scp_access');
			if($patientCcHistoryFields == 1146){
				$patientCcHistoryError = "Error : Table 'scp_access' doesn't exist";
			}
			$table = array("scp_access");
			$error = array($patientCcHistoryError);
			$mergedArray = merging_array($table,$error);
			
			$opreaterId = $_SESSION['authId'];			
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];													 
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);													 
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);	
			$arrAuditTrail= array();
			$action="update";
			
			$oldVal='';
			if($resultScpAccess['scp_status']=='active'){
				$oldVal='Enabled';
			}else if($resultScpAccess['scp_status']=='de_active'){
				$oldVal='Disabled';
			}
			
			$scpSts='';
			if($_REQUEST['siteCareStatus']=='active'){
				$scpSts='Enabled';
			}else if($_REQUEST['siteCareStatus']=='de_active'){
				$scpSts='Disabled';
			}

			$arrAuditTrail [] = 
			array(
					"Pk_Id"=> $resultScpAccess['id'],
					"Table_Name"=>"scp_access",
					"Data_Base_Field_Name"=> "scp_status" ,
					"Data_Base_Field_Type"=> fun_get_field_type($patientCcHistoryFields,"scp_status") ,
					"Filed_Label"=> "siteCareStatus",
					"Filed_Text"=> "Site Care Plan Status",
					"Action"=> $action,
					"Operater_Id"=> $opreaterId,
					"Operater_Type"=> get_operator_type($opreaterId),
					"IP"=> $ip,
					"MAC_Address"=> $_REQUEST['macaddrs'],
					"URL"=> $URL,
					"Browser_Type"=> $browserName,
					"OS"=> $os,
					"Machine_Name"=> $machineName,
					"pid"=> $_SESSION['patient'],
					"Category"=> "admin",
					"Category_Desc"=> "providers",	
					"Old_Value"=> addcslashes(addslashes($oldVal),"\0..\37!@\177..\377"),
					"New_Value"=> trim($scpSts)
				);
			$policyStatus = 0;
			$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Provider_Login___Logout'];
			if($policyStatus == 1){
				auditTrail($arrAuditTrail,$mergedArray,0,0,0);
			}			
	}
	
	$qryScp=$qry." scp_access SET scp_status='".$_REQUEST['siteCareStatus']."',user_access='".$_SESSION['authUserID']."'";
	$resScp=imw_query($qryScp)or die(imw_error().$qryScp);
	$msg="Site Care Plan has been Disabled";	
	if($_REQUEST['siteCareStatus']=='active'){
		$msg="Site Care Plan has been Enabled";	
	}
	echo "<script>top.fAlert('".$msg."');</script>";
}

$qryScpAccess="Select id,scp_status,user_access from scp_access LIMIT 1";
$resScpAccess=imw_query($qryScpAccess);
if(imw_num_rows($resScpAccess)>0){
	$rowSCP=imw_fetch_assoc($resScpAccess);
	$scpId=$rowSCP['id'];
	$scpAccess=$rowSCP['scp_status'];
	$scpUsrAccess=$rowSCP['user_access'];
	$scpUsrArr=explode(",",$scpUsrAccess);
}
//$userAccessPriv=false;
$userAccessPriv=true;//REMOVE CHECK OF USER ACCESS PRIVILEGE
$qryUsers="SELECT access_pri FROM users WHERE id='".$_SESSION['authUserID']."' LIMIT 1";
$UsersRes=imw_query($qryUsers) or die(imw_error());
$users_arr = array();
$usersRow=imw_fetch_assoc($UsersRes);
if($usersRow['access_pri']){
	$userAccess=unserialize(html_entity_decode(trim($usersRow['access_pri'])));
	//print_r($userAccess);
	if($userAccess['priv_cdc']==1){
		//$userAccessPriv=true; //REMOVE CHECK OF USER ACCESS PRIVILEGE
	}
	
}
?>
<script type="text/javascript">
	var iFrameConsole = top.fmain;
	var objConsoleAlert = iFrameConsole.document.frmConsoleAlert;
	function edit(id)
	{	
		parent.parent.show_loading_image('block');
		iFrameConsole.location.replace("index.php?edId="+id);
	}
	
	
	
	function del_list_alert(id,msg){		
		if(typeof(msg)!='boolean'){msg = true;}
		
		var selectId = new Array;
		$('.chk_sel').each(function(id,elem){
			if($(elem).is(':checked')){
				var value = $(elem).val();
				selectId.push(value);
			}
		});
		
		if(selectId.length > 0){
			if(msg){
				top.fancyConfirm("Do you want to delete Alert?","","window.top.fmain.del_list_alert('',false)");
			}else{
				var sel_ids = selectId.join(',');
				document.getElementById("delId").value=sel_ids;
				document.listSiteCare.submit();
			}
		}else{
			top.fAlert('Please select atleast one record !');
			return false;
		}
	}
	
	
	
	function new_site_care(planId){
	<?php  if($scpAccess!= 'de_active' && $userAccessPriv!=false){ ?>		
		var wi = document.body.clientWidth;
		var hit = '<?php echo $_SESSION['wn_height']; ?>';	
		var url = "";
		planId = parseInt(planId);		
		if(isNaN(planId)){
			url = '../admin/alert/index.php';
		}
		else{
			url = 'index.php';
		}		
		top.popup_win(url+'?edId='+planId,'resizable=1,scrollbars=1,width='+wi+'');
		parent.show_loading_image('none');
		<?php } ?>
	}	
	
	function winReload(){
		location.href=location.href;
	}
	function disAbleStatus(obj1,obj2){
		var checked_id = $('input[name=siteCareStatus]:checked').attr('id');
		if(checked_id == 'cbActive'){
			$("#scp_div_disable").css("display", "none");
		}else if(checked_id == 'cbDeActive'){
			$("#scp_div_disable").css("display", "block");	
		}
	}
	function submit_form(){
		listSiteCare.submit();
	}
	
</script>
</head>
<body>
<div class="whtbox">
	<form name="listSiteCare" id="listSiteCare" method="post"  style="position:relative">
			<input type="hidden" id="delId" name="delId"/>
			<?php if($userAccessPriv==true){ ?>
			<div class="head">
				<div class="row">
					<div class="col-sm-2">
						<span>&nbsp; Site Care Plan</span>
					</div>
					<div class="col-sm-9 content_box">
						<div class="radio radio-inline">
							<input type="radio" name="siteCareStatus" class="css-checkbox" id="cbActive" value="active" <?php if($scpAccess == 'active'){echo 'checked';} ?> onClick="disAbleStatus('cbActive','cbDeActive');"/>
							<label for="cbActive" onClick="disAbleStatus('cbActive','cbDeActive');">Enable</label>
						</div>
						<div class="radio radio-inline" style="vertical-align:bottom">
							<input type="radio" id="cbDeActive" style="cursor:pointer;" name="siteCareStatus" value="de_active" <?php if($scpAccess== 'de_active'){echo 'checked';} ?> onClick="disAbleStatus('cbActive','cbDeActive');" class="css-checkbox"/>
							<label for="cbDeActive" onClick="disAbleStatus('cbActive','cbDeActive');">Disabled</label>
						</div>
					</div>
				</div>
			</div>
			<?php } $displaySCP="none";
			if($scpAccess== 'de_active' || $userAccessPriv==false){ $displaySCP="block";}
			?>
			   <div id="scp_div_disable" class="content_disable" style="display:<?php echo $displaySCP; ?>"></div>
					<div id="scp_div"  class="table-responsive ">
					<table class="table table-bordered adminnw">
						<thead>
							<tr>
								<th style="width:20px;padding-left:10px">
									<div class="checkbox text-center">
										<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="" autocomplete="off">
										<label for="chk_sel_all">
										</label>
									</div>
								</th>
								<th>Site Care Name</th>
								<th>Alert</th>
								<th>Gender</th>
								<th>Age (From - To)</th>
								<th>Status</th>
							</tr>
						</thead>
						<?php
							$sql 	= "SELECT * FROM alert_tbl where alert_created_console = '0' ORDER BY alertId ";
							$rez 	= imw_query($sql);	
							$alrtTr = "";
							if(imw_num_rows($rez) > 0){
								$i=0;
								for($i=0;$row=imw_fetch_array($rez);$i++) {
									$dxCodeArr	=	array();
									$cptCodeArr	=	array();
									$vitalSignIdArr	=	array();
									$bgcolor 		= (($i%2)  == 0) ? 'alt3 ' : "";
									$alertContent 				= stripslashes($row["alertContent"]);
									$AlertDesc 				= stripslashes($row["AlertDesc"]);
									$add_tests =stripslashes($row["add_tests"]);
									//START CODE TO GET DX-CODE
									$dxCodeId					= stripslashes($row["dxCodeId"]);
									$dxCode='';
									if($dxCodeId) {
										$dxCodeId = str_replace(",","','",$dxCodeId);
										$listDiagQry 			= "SELECT dx_code,diagnosis_id,diag_description FROM diagnosis_code_tbl WHERE diagnosis_id in('".$dxCodeId."') ORDER BY dx_code ASC";
										$listDiagRes 			= imw_query($listDiagQry);
										$listDiagNumRow 		= imw_num_rows($listDiagRes);
										if($listDiagNumRow>0) {
											while($listDiagRow 	= imw_fetch_array($listDiagRes)) {
												$dxCodeArr[] 	= $listDiagRow['dx_code'];
											}
											if($dxCodeArr) {
												$dxCode 		= implode(', ',$dxCodeArr);
											}
										}
										
									}
									//END CODE TO GET DX-CODE
									
									//START CODE TO GET CPT-CODE
									$cptCodeId 					= stripslashes($row["cptCodeId"]);
									$cptCode='';
									if($cptCodeId) {
										$cptCodeId = str_replace(",","','",$cptCodeId);
										$listCptQry 			= "SELECT cpt_prac_code,cpt_fee_id FROM cpt_fee_tbl WHERE cpt_fee_id in('".$cptCodeId."') AND delete_status = '0'  ORDER BY cpt_prac_code ASC";
										$listCptRes 			= imw_query($listCptQry);
										$listCptNumRow 			= imw_num_rows($listCptRes);
										if($listCptNumRow>0) {
											while($listCptRow 	= imw_fetch_array($listCptRes)) {
												$cptCodeArr[] 	= $listCptRow['cpt_prac_code'];
											}
											if($cptCodeArr) {
												$cptCode 		= implode(', ',$cptCodeArr);
											}
										}	
									}
									//END CODE TO GET CPT-CODE
									
									
									//START CODE TO GET VITAL-SIGN
									$vitalSignId 				= stripslashes($row["vitalSignId"]);
									$vitalSign='';
									if($vitalSignId) {
										$listVitalSignQry 		= "SELECT id,vital_sign FROM vital_sign_limits WHERE id='".$vitalSignId."' ORDER BY vital_sign";
										$listVitalSignRes 		= imw_query($listVitalSignQry);
										$listVitalSignNumRow 	= imw_num_rows($listVitalSignRes);
										if($listVitalSignNumRow>0) {
											$listVitalSignRow 	= imw_fetch_array($listVitalSignRes);
											$vitalSign 			= $listVitalSignRow['vital_sign'];
										}
									}	
									//END CODE TO GET VITAL-SIGN
									
									$vitalSignIdFrom 			= stripslashes($row["vitalSignIdFrom"]);
									$vitalSignIdTo 				= stripslashes($row["vitalSignIdTo"]);
									
									//START CODE TO SET HIFFEN AND BRACKETS OF VITAL SIGN
									$vitalSignIdHifen			= '';
									$vitalRoundBracketStart 	= '';
									$vitalRoundBracketEnd 		= '';
									if($vitalSignIdFrom || $vitalSignIdTo) {
										$vitalRoundBracketStart = '(';
										$vitalRoundBracketEnd 	= ')';
									}
									if($vitalSignIdFrom && $vitalSignIdTo) {
										$vitalSignIdHifen 		= ' - ';
									}
									//END CODE TO SET HIFFEN AND BRACKETS OF VITAL SIGN
									
									$medication 				= stripslashes($row["medication"]);
									$ageFrom 					= stripslashes($row["ageFrom"]);
									$ageTo 						= stripslashes($row["ageTo"]);
									$ageToIdHifen				= '';
									if($ageFrom && $ageTo) {
										$ageToIdHifen 			= ' - ';
									}
									$gender 					= stripslashes($row["gender"]);
									$cdRatio 					= stripslashes($row["cdRatio"]);
									$iopPressure 				= stripslashes($row["iopPressure"]);
									$editId 					= $row["alertId"];
									$patient_id 				= $row["patient_id"];
									if($row["status"]=='0'){
										$status='off';
									}else{
										$status='on';
									}
									
									$siteCarePlanName = $row['site_care_plan_name'];
									
									$frequencyType = $row['frequency_type'];
									if($frequencyType == '1'){
										$frequencyType = "Month(s), ";
									}
									elseif($frequencyType == '2'){
										$frequencyType = "Year(s), ";
									} 
									$frequencyValue = $row['frequency_value'];
									if($row['frequency_type'] == '2' && $row['frequency_value'] == "200~~"){
										$frequencyValue = "";
									}
									else{
										if(($frequencyType == "Month(s), " || $frequencyType == "Year(s), ") && empty($frequencyValue) == false && $frequencyValue != "~~"){
											$frequencyValue = str_replace("~~",$frequencyType,$frequencyValue);	
											$frequencyValue = substr(trim($frequencyValue), 0, -1); 							
										}
									}
									
									$qry1 = imw_query("select concat(lname,', ',fname) as name , mname from patient_data
											where id = $patient_id");
									$patientDetails1 = imw_fetch_array($qry1);
									$patient = ucwords($patientDetails1['name']);
									
									$alrtTr .= "<tr class=".$bgcolor.">";
									$alrtTr .= "<td class='text-center'>
												<div class='checkbox'><input type='checkbox' name='id' class='chk_sel' id='chk_sel_".$editId."' value='".$editId."'><label for='chk_sel_".$editId."'></label></div>
											</td>";
									$alrtTr .= "<td><a href=\"javascript:void(0);\" onclick=\"new_site_care('".$editId."')\">".$siteCarePlanName."</a></td>";
									$alrtTr .= "<td><a href=\"javascript:void(0);\" onclick=\"new_site_care('".$editId."')\">".$alertContent."</a></td>";
									$alrtTr .= "<td><a href=\"javascript:void(0);\" onclick=\"new_site_care('".$editId."')\">".$gender."</a></td>";
									$alrtTr .= "<td class=\"text-center\"><a href=\"javascript:void(0);\" onclick=\"new_site_care('".$editId."')\">".$ageFrom.$ageToIdHifen.$ageTo."</a></td>";
									$alrtTr .= "<td class=\"text-center\"><a href=\"javascript:void(0);\" id=\"scp_".$editId."\">".$status."</a></td>";									
									$alrtTr .= "</tr>";		
								}
							}else {
								$alrtTr .= "<tr>".
												"<td colspan=\"7\" style='background:#FFFFFF' class=\"failureMsg\">No Record.</td>".
											"</tr>";
						}
						echo $alrtTr;
						?>
					</table>
				</div>
			</form>
</div>
<?php
	if(isset($_GET["op"]) && !empty($_GET["op"]))
	{
		if($_GET["op"] == "1")
		{
			$msg = "Alert infomation is saved.";
		}
		else if($_GET["op"] == "2")
		{
			$msg = "Alert information is edited.";
		}
		else if($_GET["op"] == "3")
		{
			$msg = "Alert information is deleted.";
		}
		
		echo "<script>
			  	fAlert('".$msg."');				
				objConsoleAlert.editId.value=\"\";
				objConsoleAlert.editMode.value=\"insert\";
				iFrameConsole.document.getElementById(\"elemDxCodeId\").value=\"\";
				iFrameConsole.document.getElementById(\"elemCptCodeId\").value=\"\";
				objConsoleAlert.alertContent.value=\"\";
				objConsoleAlert.vitalSignId.value=\"\";
				objConsoleAlert.vitalSignIdFrom.value=\"\";
				objConsoleAlert.vitalSignIdTo.value=\"\";
				objConsoleAlert.medication.value=\"\";
				objConsoleAlert.cdRatio.value=\"\";
				objConsoleAlert.iopPressure.value=\"\";
				objConsoleAlert.gender.value=\"\";
				objConsoleAlert.ageFrom.value=\"\";
				objConsoleAlert.ageTo.value=\"\";	
				objConsoleAlert.add_tests_elem.value=\"\";	
				objConsoleAlert.elemDxCodeId.value=\"\";	
				objConsoleAlert.elemCptCodeId.value=\"\";	
				objConsoleAlert.cdRatio_od_os.value=\"\";	
				objConsoleAlert.iopPressure_od_os.value=\"\";
				objConsoleAlert.Reference.value=\"\";							
			  </script>";
	}
?>
<script type="text/javascript">
var btnArr = new Array();
	<?php if($scpAccess!= 'de_active'  && $userAccessPriv!=false){  ?>btnArr[0]=["alerts_new","New","top.fmain.new_site_care();"]; <?php } ?>
	<?php if($userAccessPriv==true && $scpAccess!= 'de_active'){ ?>btnArr[1]=["alerts_new","Save","top.fmain.submit_form();"]; <?php }else if($userAccessPriv==true && $scpAccess== 'de_active'){ ?>btnArr[0]=["alerts_new","Save","top.fmain.submit_form();"]; <?php } ?>
btnArr.push(["Delete","Delete","top.fmain.del_list_alert();"]);
top.btn_show("ADMN",btnArr);
set_header_title('Site Care Plan');	
check_checkboxes();
parent.show_loading_image('none');
</script>
<?php 
	require_once('../admin_footer.php');
?>