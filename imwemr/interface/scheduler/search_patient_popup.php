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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
require_once($GLOBALS['fileroot'].'/library/classes/class.app_base.php');
$app_base			= new app_base();

$elem_status="Active";

$fax = (isset($_REQUEST['from']) && $_REQUEST['from']==='inbound_fax')?true:false;
$faxfieldKey = trim($_REQUEST['fieldKey']);
$faxfieldKey = (int)$faxfieldKey;

$call_from = ($_REQUEST['call_from']) ? $_REQUEST['call_from'] : 'scheduler';

if(isset($_REQUEST["btn_enter"]) || ($_REQUEST["btn_sub"] == "Search")){   
	$txt_for =addslashes(trim($_REQUEST["txt_for"]));
	$sel_by = $_REQUEST["sel_by"];	
	
	if(empty($txt_for)){
	  $sel_by = "Nothing";      
	}
	else{
		if($sel_by != "Resp.LN" && $sel_by != "Ins.Policy" && $sel_by != "Address" && $sel_by != "External MRN") {
			$elem_status=$sel_by;
			$sel_by=$app_base->getFindBy($txt_for);
		}
		
	}     
	if($txt_for<>""){			
		list ($result1s,$nurow,$prevDataPtIdArr,$Total_Records) = $app_base->core_search($sel_by,$elem_status,$txt_for,$previousSearch = false);
	}
	$elem_status = $sel_by;
}

//START
$arr_balance 		= array();
$arr_pt_appt 		= array();
$arr_askForReason 	= array();
$arr_pt_rp 			= array();
$str_all_pt 		= "";

if(count($result1s) > 0){
	foreach($result1s as $key=>$subArray)
	{
		$arr_pt[] = $subArray["id"];
	}
	//for($k = 0; $k < count($result1s); $k++){
//		echo "<br>id=".$result1s[$k]["id"];
//		$arr_pt[] = $result1s[$k]["id"];
//	}
	$str_patient = implode(",", $arr_pt);
}

$total_ids=count($arr_pt);
$str_all_pt = $str_patient;
if($str_all_pt != ""){
	$where=$join="";
	if($total_ids>10)
	{
		$tmp_table="PatientIdList_".time().'_'.$_SESSION["authId"];
		//add temporary table here
		imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
		imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (id INT)");
		$insertValues=str_replace(',','),(',$str_all_pt);
		
		imw_query("INSERT INTO $tmp_table (id) VALUES($insertValues)");
		$join=" INNER JOIN ".$tmp_table." pl ON pcl.patient_id  = pl.id ";
	}
	else{
		//add IN clause in query
		$where="pcl.patient_id IN (".$str_all_pt.") AND ";
	}
	
	$sql = "select sum(pcl.totalBalance) as totalBalance, pcl.patient_id from patient_charge_list pcl $join where $where pcl.del_status='0' GROUP BY pcl.patient_id";
	$res = imw_query($sql);
	if(imw_num_rows($res) > 0){
		while($arr=imw_fetch_assoc($res))
		{
			$temp = $arr["patient_id"];
			$arr_balance[$temp] = $arr["totalBalance"];
		}
	}
	$where=$join="";
	if($total_ids>10)
	{
		$join=" INNER JOIN ".$tmp_table." pl ON sa.sa_patient_id  = pl.id ";
	}
	else{
		//add IN clause in query
		$where="sa.sa_patient_id IN (".$str_all_pt.") AND ";
	}
	$sql_appt = "SELECT sa.sa_patient_id, date_format(sa.sa_app_start_date,'%m-%d-%Y') as ap_start_date, sa.sa_app_starttime, sa.procedureid, sp.proc, sp.acronym FROM schedule_appointments sa
			INNER JOIN slot_procedures sp ON (sp.id=sa.procedureid) 
			$join
			WHERE $where sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
			AND CONCAT(sa.sa_app_start_date, ' ', sa.sa_app_starttime) > NOW()";
	$res_appt = imw_query($sql_appt);
	if(imw_num_rows($res_appt) > 0){
		while($arr_appt=imw_fetch_assoc($res_appt))
		{
			$str_apt_details = "";
			$str_apt_details .= $arr_appt['ap_start_date'].' '.core_time_format($arr_appt['sa_app_starttime']).' <br/>';
			$str_apt_details .= $arr_appt['acronym']=='' ? $arr_appt['proc'] : $arr_appt['acronym'];
			$arr_pt_appt[$arr_appt["sa_patient_id"]] = $str_apt_details;
		}
	}
	//echo"<pre>";print_r($arr_pt_appt);echo"</pre>";
	$where=$join="";
	if($total_ids>10){
		$join=" INNER JOIN ".$tmp_table." pl ON rp.patient_id  = pl.id ";
	}
	else{
		$where=" rp.patient_id IN (".$str_all_pt.") and ";
	}	
	
	
	$sql_rp = "SELECT  rp.restrict_providers,  rp.patient_id FROM restricted_providers rp $join where $where rp.restrict_providers != ''";
	$res_rp = imw_query($sql_rp);
	if(imw_num_rows($res_rp) > 0){
		while($arr_rp=imw_fetch_assoc($res_rp)){
			if(isset($_SESSION["glassBreaked_ptId"]) && $_SESSION["glassBreaked_ptId"] == $arr_rp["patient_id"]){
				continue;
			}
			$explodeArray = explode(",", $arr_rp["restrict_providers"]);
			if(in_array($_SESSION["authId"], $explodeArray)){
				$arr_pt_rp[$arr_rp["patient_id"]] = $arr_rp["patient_id"];
			}
		}
	}
	
	//remove temporary table
	if($tmp_table){
	imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);}
	
}
//END  end

//break glass privilege chekc
$isBGPriv = $app_base->core_check_privilege("priv_break_glass");
$bgPriv = ($isBGPriv == true) ? "y" : "n";

?>
<!DOCTYPE html>
<head>
<title>iMedic: Select Patient</title>
<!-- Bootstrap -->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/html5shiv.min.js"></script>
  <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script><!--
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery-ui.min.js"></script>-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap-dropdownhover.min.js"></script>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
<!--jquery to suport discontinued functions-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery-migrate-1.2.1.js"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <!--    
<script language="javascript" src="../../js/jquery.js" type="text/javascript"></script>
<script language="javascript" src="common/scheduler.js" type="text/javascript"></script>
<script language="javascript" src="../main/javascript/common4all.js" type="text/javascript"></script>
<script type="text/javascript" src="../main/javascript/prompt.js" language="javascript"></script>
--><script language="javascript" type="text/javascript">
window.focus();
var call_from = '<?php echo $call_from;?>';
function chk(frm)
{
	if(frm.sel_by.value == '')
	{
		fAlert("Please select 'Select By'. ");
		return false;
	}
	
	if(frm.txt_for.value == '')
	{
		fAlert("Please fill patient name criteria or patient id.");
		return false;
	}
	if(document.getElementById('div_pop_loading_image')) {
		document.getElementById('div_pop_loading_image').style.display = "block";
	}
	return true;
}

	function selpid(pid,fname,mname,lname,suffix,ph,pm,pb,ps,pd,pstreet,pcity,pstate,pzip,ttl,p1,p2,p3,p4,sel_follow,sel_follow_val,c1,c2,c3) 
	{
				
			if(opener.closed || ! opener.setpatient)
			{
				fAlert('The destination form was closed; I cannot act on your selection.');
			}
			else
			{ 		  	
				opener.setpatient(pid,fname,mname,lname,suffix,ph,pm,pb,ps,pd,pstreet,pcity,pstate,pzip,ttl,1,p1,p2,p3,p4,sel_follow,sel_follow_val,c1,c2,c3);
				window.close();
			}
			return false;
	}

    /****Function to perform action to load a patient***/
    function getpid(pid, bgpriv, rp_alert){
        if(pid != ""){
            //$('#patient_searched').val(pid);
            if(rp_alert == "y"){
                opener.top.core_restricted_prov_alert(pid, bgpriv);
                window.close();
                return false;
            }else{
                if(opener.pre_load_front_desk){opener.pre_load_front_desk(pid, '');}
                window.close();
            }
        }
    }
    
    /* 
    this function is not working properly as behaviour of break glass is changed in R8. this function act like as in R7 and was not coded according to R8 functionality.
	function getpid(patid,askReason){		
		var askReason = (askReason)?askReason:false;
		if(askReason){
			var title="Restricted User";
			var msg="Your account is restricted to access this patient. ";
			var btn1="Give Reason";
			var btn2="Close";
			var func="RestrictedUserAlertsShow";
			var showCancel=0;
			var showImage=0;
			var misc="";
			var objDivMsg=displayConfirmYesNo_v3(title,msg,btn1,btn2,func,showCancel,showImage,misc);
			if(objDivMsg){
			objDivMsg.style.top="100px"; 
			objDivMsg.style.left="150px"; 
			}
			if(document.getElementById("divRestrictedUserAlerts")){
				document.getElementById("divRestrictedUserAlerts").style.top="100px"; 
				document.getElementById("divRestrictedUserAlerts").style.left="150px"; 
			}
			if(document.frmRestrictedUserAlerts){
			document.frmRestrictedUserAlerts.patient_searched.value=patid;
			document.frmRestrictedUserAlerts.searched_path.value="FrontDesk";
			}

		}
		
		if(askReason==false && patid!=""){
			//opener.refresh_patient_infopage(patid,'','');
			if(opener.pre_load_front_desk){opener.pre_load_front_desk(patid, '');}
			//opener.PriPhyFlagSet();
			window.close();
		}	
	} */
	
	function getpidFax(pid, patientName){
		var parentKey = <?php echo $faxfieldKey; ?>;
		opener.$('#pateintId_'+parentKey).val(pid);
		opener.$('#patientSearch_'+parentKey).val(patientName+' - '+pid);
		opener.$('#selicon_'+parentKey).css('visibility', 'visible');
		opener.$('#imgSel_'+parentKey).focus();
		window.close();
	}
		
	function searchPatient2(obj){
			var patientdetails = obj.value.split(':');
			if(isNaN(patientdetails[0]) == false){
				document.getElementById("txt_for").value = patientdetails[1];
				document.getElementById("sel_by").value = patientdetails[2];
				document.frm_sel.submit();
			}
		}
</script>
</head>

<body class="body_c" scroll="no">
<div id="div_pop_loading_image" class="loading" style=" position:absolute;left:150px; top:10px;width:300px;">
    <div id="div_pop_loading_text" style="width:300px;position:absolute;padding-top:50px;text-align:center;">Please wait</div>
</div>
<div class="whtbox">
    <div class="pd10"><div class="row boxheadertop">
        <div class="col-sm-8 text-right">
        	<form name="frm_sel" action="search_patient_popup.php" method="post" onSubmit="return chk(this)">
            <div class="row">
                <div class="col-sm-5"><input type="text" id="txt_for" name="txt_for" value="<?php echo stripslashes($txt_for)?>" class="form-control"></div>
                <div class="col-sm-5">
                    <select id="sel_by" name="sel_by" onchange="searchPatient2(this);" onkeypress="return entsub(this.form)" class="form-control minimal selecicon">
                        <option value="Active" <?php echo ($elem_status=='Active')?'selected':'';?>>Active</option>
                        <option value="Inactive" <?php echo ($elem_status=='Inactive')?'selected':'';?>>Inactive</option>
                        <?php 
						if(constant('EXTERNAL_MRN_SEARCH')=="YES") 
						{
							echo '<option value="External MRN"';
							echo ($elem_status=='External MRN')?'selected':'';
							echo '>External MRN</option>';
						}
						?>
                        <option value="Deceased" <?php echo ($elem_status=='Deceased')?'selected':'';?>>Deceased</option>
                        <option value="Moved out of Area" <?php echo ($elem_status=='Moved out of Area')?'selected':'';?>>Moved out of Area</option>
                        <option value="No response Recall" <?php echo ($elem_status=='No response Recall')?'selected':'';?>>No response Recall</option>
                        <option value="Seen as Consult Only" <?php echo ($elem_status=='Seen as Consult Only')?'selected':'';?>>Seen as Consult Only</option>
                        <option value="EID" <?php echo ($elem_status=='EID')?'selected':'';?>>EID</option>
                        <option value="Resp.LN" <?php echo ($elem_status=='Resp.LN')?'selected':'';?>>Resp.LN</option>
                        <option value="Ins.Policy" <?php echo ($elem_status=='Ins.Policy')?'selected':'';?>>Ins.Policy</option>
                        <option value="Address" <?php echo ($elem_status=='Address')?'selected':'';?>>Address</option>
                   </select>
                </div>
                <div class="col-sm-2">
                    <input type='hidden' id="btn_enter" name="btn_enter" value='a' >
                    <input type='hidden' id="btn_sub" name="btn_sub" value='Search'>
                    <input type='hidden' id="call_from" name="call_from" value='<?php echo $call_from;?>'>
                    <input type="hidden" name="from" value="<?php echo ($fax)?$_REQUEST['from']:''; ?>">
                    <input type="hidden" name="fieldKey" value="<?php echo $faxfieldKey; ?>">
                    <button type="submit" value="save" class="btn btn-info" name="save_butt" id="save_butt" >Search</button>
                </div>
            </div>
            </form>
    	</div>
        <div class="col-sm-4 text-right" id="currentResultSpanId"><strong>Total match found: <?php echo $nurow;?></strong></div>         
    </div></div>
    
    
    <div class="row">
        <div class="col-sm-12">
            <div id="body_area" style="height:370px; overflow:auto">
            <?php if($nurow>0){ ?>
            <table class="table table-striped table-bordered table-hover adminnw">
                <thead>
                    <tr class="section_header">
                        <th>Patient Name</th>
                        <th>Gender</th>
                        <th>Social Security</th>
                        <th>DOB</th>
                        <th>Patient ID</th>
                        <th title="Outstanding Balance">Balance</th>
                        <th title="First Future Appointment">Future Appt.</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                  foreach ($result1s as $iter){
                    $iterpid  	= $iter['pid'];
                    $patient_id	= $iter['pid'];
                    $iterlname 	= $iter['lname'] ;//str_replace("'"," ",$iter['lname']);
                    $iterfname 	= $iter['fname'];//str_replace("'"," ",$iter['fname']);
                    $itermname 	= $iter['mname'] ;//str_replace("'"," ",$iter['mname']);
                    $itersuffix = $iter['suffix'] ;//str_replace("'"," ",$iter['suffix']);
                    $phone_home = $iter['phone_home'];
                    $phone_cell = $iter['phone_cell'];
                    $sex 		= $iter['sex'];
                    $street 	= $iter['street'];
                    $city 		= $iter['city'];
                    $title 		= $iter['title'];
                    $state 		= $iter['state'];						  
                    $phone_biz 	= $iter['phone_biz'];
                    $zip 		= $iter['postal_code'];						   
                                               
                    $dob		= explode("-",$iter['DOB']);
                    $yr			= $dob[0];
                    $mo			= $dob[1];
                    $dy			= $dob[2];						
					$patientname="";
                    if($iterlname!="")	{
                        $patientname.=' '.$iterlname;
                    }
                    if($iterfname!="")	{
                        $patientname.=', '.$iterfname;
                    }
                    if($itermname!="")	{
                        $patientname.=' '.$itermname;
                    }
                    if($itersuffix!="")	{
                        $patientname.=' '.$itersuffix;
                    } 
                    $dob_format	= $mo."-".$dy."-".$yr;
                    $dob_format = get_date_format($dob_format,'mm-dd-yyyy');
                    $pid		= $iter['id'];
            
                    //RESTRICTED ALERT
                    $askForReason = "";
                    if(isset($arr_pt_rp[$iter["id"]])){
                        $askForReason = true;
                    }
                    $rp_alert = (($askForReason == true) ? "y" : "n");
                    
                    $balance = "";
                    if(isset($arr_balance[$iter["id"]])){
                        $bal 		= $arr_balance[$iter["id"]];
                        $balance 	= numberFormat($bal, 2);
                    }
                    $balance 		= ($balance == '') ? 'N/A' : $balance;
                    
                    
                    
                    /*APPOINTMENT DETAILS*/
                    //$apptDetails = get_appointment_details($iter['id']);
                    //$apptDetails = $apptDetails == '' ? 'N/A' : $apptDetails;		
                    $apptDetails 		= "";
                    if(isset($arr_pt_appt[$iter["id"]])){
                        $apptDetails 	= $arr_pt_appt[$iter["id"]];
                    }
                    $apptDetails 		= ($apptDetails == '') ? 'N/A' : $apptDetails;
                    
                    if($call_from == 'scheduler')
										{
                    	if($fax)
                        $clinkFunc = "javascript:getpidFax($pid ,'".stripslashes($patientname)."');";
                    	else
                        //$clinkFunc = "javascript:getpid($pid ,'".$askForReason."');";
                        $clinkFunc = "javascript:getpid('".$pid."', '".$bgPriv."', '".$rp_alert."');";
					}
					elseif($call_from == 'physician_console')
					{
						$clinkFunc = "javascript:opener.physician_console($pid ,'".$patientname."'); window.close();";	
					}
					elseif($call_from == 'physician_console2')
					{
						$clinkFunc = "javascript:opener.physician_console2($pid ,'".$patientname."'); window.close();";	
					}
					elseif($call_from == 'demographics')
					{
						$clinkFunc = "javascript:opener.setHeardAboutUsVal($pid ,'".$patientname."'); window.close();";	
					}
					else
					{
						$clinkFunc = "javascript:selpid($pid,'".$iterfname."','".$itermname."','".$iterlname."','".$itersuffix."','".$phone_home."','".$phone_cell."','".$phone_biz."','','','".$street."','".$city."','".$state."','".$zip."','','','','','','".$_REQUEST['btn_enter']."','','','','');";
					}
										$patAccStatusHTML = '';
										if( $_REQUEST['sel_by'] == 'Active' && ($iter['patientStatus'] == 'Deceased' || $iter['patientStatus'] == 'Inactive') )
											$patAccStatusHTML = '<span class="pull-right btn-sm btn-'.($iter['patientStatus']=='Deceased'?'danger':'info').'" style="padding:1px 5px; border-radius:0px;">'.$iter['patientStatus'].'</span>';
                    
                    echo "  <tr onClick=\"".$clinkFunc."\" style=\"cursor:pointer\">";
                    echo "  <td>".stripslashes($patientname).$patAccStatusHTML."</td>\n";
                    echo "  <td align=\"center\">" . $iter['sex'] . "</td>\n";
                    echo "  <td align=\"center\">" . $iter['ss'] . "</td>\n";
                    echo "  <td align=\"center\">" . $dob_format . "</td>\n";
                    echo "  <td align=\"right\">" . $iter['id'] . "</td>\n";
                    echo "  <td align=\"right\">".$balance."</td>\n";
                    echo "  <td align=\"center\">".$apptDetails."</td>\n";		
                    echo "  </tr>";
                  }
                ?>
                </tbody>
            </table>
            <?php } else if($sel_by<>"" || $nurow<=0){ ?>
            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <th class="warning" >No record found !</th>
                </tr>
            </table>
            <?php } ?>
            </div>
        </div>        
    </div>
</div>

<div class="container-fluid text-right">
	<button class="btn btn-danger" name="btn_find" id="btn_find" onclick="javascript:window.close();">Close</button>
</div>

<?php include("../common/restricted_users_alerts.php");?>		
</body>
</html>
<script language="javascript" type="text/javascript">
if(document.getElementById('div_pop_loading_image')) {
	document.getElementById('div_pop_loading_image').style.display = "none";
}
</script>