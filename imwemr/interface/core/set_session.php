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
?>
<?php
/*
File: set_session.php
Purpose: This file set session of patient in work view and other areas of applications.
Access Type : Direct
*/
?>
<?php
include_once(getcwd()."/../../config/globals.php");
require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
$app_base			= new app_base();

if(isset($_REQUEST["set_pid"]) && !empty($_REQUEST["set_pid"]) && is_numeric($_REQUEST["set_pid"])){
	// --- role change ---
	if(isset($_SESSION["sess_user_role"])){
		require_once(dirname(__FILE__).'/../../library/classes/work_view/User.php');
		require_once(dirname(__FILE__).'/../../library/classes/work_view/RoleAs.php');
		$oRoleAs = new RoleAs();
		$oRoleAs->reset_user_role_ptonly();
	}	
	// --- role change ---
	//------- Update pt chart lock if any ----------------
	if(!empty($_SESSION['patient'])){
	//	$oPtLock = new ChartPtLock($_SESSION['authId'],$_SESSION["patient"]);			
	//	$oPtLock->releaseUsersPastPt();	
	}
	
    // If break glass is already set for requested patient
    $breakGLassId='';
    if(isset($_SESSION["glassBreaked_ptId"]) && $_SESSION["glassBreaked_ptId"] == $_REQUEST["set_pid"]){
        $breakGLassId=$_SESSION["glassBreaked_ptId"];
    }


	//Clear --
	if(!$_REQUEST['fromDocTab']){
		$app_base->clean_patient_session();
		$_SESSION['new_casetype'] = NULL;
	}
	$_SESSION['alertShowForMedication']='';
	$_SESSION['patient'] = "";
	$_SESSION['patient'] = NULL;
	$_SESSION['first_avail_alert']='';//from scheduler
	unset($_SESSION['patient']);
	
	//FormId
	$_SESSION['form_id'] = "";
	$_SESSION['form_id'] = NULL;
	unset($_SESSION['form_id']);
	
	$_SESSION["PT_DOC_ALERT_STATUS"] = "";
	$_SESSION["PT_DOC_ALERT_STATUS"] = NULL;
	unset($_SESSION["PT_DOC_ALERT_STATUS"]);
	
	//FinalizedID	
	$_SESSION['finalize_id'] = "";
	$_SESSION['finalize_id'] = NULL;
	unset($_SESSION['finalize_id']);
	
	//Patient_Viewed	
	$_SESSION['Patient_Viewed'] = "";
	$_SESSION['Patient_Viewed'] = NULL;
	unset($_SESSION['Patient_Viewed']);
	
	//clear alertShowForThisSession session variable so that in new session alert could come
	$_SESSION['alertShowForThisSession'] = "";
	$_SESSION["alertShowForThisSession"] = NULL;
	unset($_SESSION["alertShowForThisSession"]);
	
	//clear alertImmShowForThisSession session variable so that in new session imm alert could come
	$_SESSION['alertImmShowForThisSession'] = "";
	$_SESSION["alertImmShowForThisSession"] = NULL;
	unset($_SESSION["alertImmShowForThisSession"]);
	
	$_SESSION['patient'] = $_REQUEST["set_pid"];
    if($breakGLassId!='' && $breakGLassId==$_SESSION['patient']){$_SESSION["glassBreaked_ptId"]=$_SESSION['patient'];}
	if(isset($_SESSION['POSTPONEPGHD']) && $_SESSION['POSTPONEPGHD']!='' && $_SESSION['POSTPONEPGHD']!=$_SESSION['patient']){$_SESSION['POSTPONEPGHD']="";$_SESSION['POSTPONEPGHD']=NULL;unset($_SESSION['POSTPONEPGHD']);}
	patient_monitor_daily("PATIENT_LOAD");
	$_SESSION['selectedPatientId'] = $_REQUEST["set_pid"];
	$patientViewed = array(	"Demographics" => 0,
							"Insurance" => 0,
							"Medical History" => array(
													"Ocular"=>0,
													"General_Health"=>0,
													"Medications"=>0,
													"Sx_Procedures"=>0,
													"Allergies"=>0,
													"Immunizations"=>0,
													"Social"=>0,
													"CC_and_History"=>0,
													"VS"=>0,
													"Order_Sets"=>0,
													"Problem_List"=>0
												)
						);
    if(isset($_GET["rd2"]) && !empty($_GET["rd2"])){
        $rd2 = $_GET["rd2"];
		//if(trim($rd2) == "patient_tabs.php"){
			$_SESSION['Patient_Viewed'] = $patientViewed;
		//}
		//START CODE FOR ADDITIONAL LINKS
		foreach($_GET as $gKey=>$gVal) {
			if($gKey !="set_pid" && $gKey !="rd2") {
				$gLnk .= "&".$gKey."=".$gVal;
			}
		}
		$rd2.=$gLnk;
		//END CODE FOR ADDITIONAL LINKS
    }
    
    $str="";
    foreach($_GET as $key => $val){
       if(($key != "set_pid") && ($key != "rd2")){
         $str .= $key."=".$val."&";
       } 
    }
    if(!empty($str)){
        $str = "&".substr($str,0,-1);    
    }
	
	//--- Code to save five recent patients ----
	if($_SESSION['patient']!= ''){
		$max_recent_pts = $GLOBALS["max_recent_search_cache"];
		$auth_id 		= $_SESSION['authId'];
		$patient_id 	= $_SESSION['patient'];
		$patientFindBy 	= $_SESSION['patientFindBy'];
		$qry = "select patient_id,recent_user_id from recent_users 
				where provider_id = $auth_id order by enter_date desc";
		$res = imw_query($qry);
		$patientRecent = false;
		if(imw_num_rows($res) > 0){
			$next_recent_user_id = '';
			$i = 0;
			while($qryRes = imw_fetch_assoc($res)){
                $recent_user_id = $qryRes['recent_user_id']; 
				if($_SESSION['patient'] == $qryRes['patient_id']){
					$patientRecent = true;
					$recent_user_id = $qryRes['recent_user_id'];
					$curDate = date('Y-m-d H:i:s');
					$qry = "update recent_users set patient_id = $patient_id,
								 enter_date = '$curDate' where recent_user_id = $recent_user_id";
					imw_query($qry);
				}
				$i++;
				if($i==$max_recent_pts && !$patientRecent) $next_recent_user_id = $recent_user_id;
			}
		}	
		if($patientRecent == false){	
			$curDate = date('Y-m-d H:i:s');
			if(imw_num_rows($res) == $max_recent_pts){
				$qry = "update recent_users set patient_id = $patient_id,
						provider_id = $auth_id,patientFindBy = '$patientFindBy' ,enter_date = '$curDate' 
						where recent_user_id = $next_recent_user_id";
			}else{			
				$qry = "insert into recent_users set patient_id = $patient_id,
						provider_id = $auth_id,patientFindBy = '$patientFindBy',enter_date = '$curDate'";			
			}
			imw_query($qry);
		}
	}
	
	/*Get All Scripts Details*/
	if( is_allscripts() )
	{
		$sqlAS = 'SELECT `External_MRN_4`, `as_id` 	FROM `patient_data` WHERE `id`='.$_SESSION['patient'];
		$sqlAS = imw_query($sqlAS);
		if( $sqlAS )
		{
			$sqlAS = imw_fetch_assoc($sqlAS);
			$_SESSION['as_mrn'] = $sqlAS['External_MRN_4'];
			$_SESSION['as_id'] = $sqlAS['as_id'];
		}
	}
	
	if($toDo == 'true'){
		?>
		<script type="text/javascript">
			if(top.document.getElementById("curr_main_tab")){
				var tabDefault = top.document.getElementById("curr_main_tab").value;
			}
			else{
				var tabDefault = 'Work View';
			}
			if(top.fmain){
				var mydoc=top.ftop;
			}
			else{
				var mydoc=top;
			}
			mydoc.location.href= "../chart_notes/index.php?schedular=1&defaultTab="+tabDefault;
		</script>
		<?php
	}else{		
		?>		
		<script>
			// to suppress old patient opened message alerts
			/*if(typeof(top.document.getElementById("divCommonAlertMsg")) != "undefined") {
				top.document.getElementById("divCommonAlertMsg").style.display = "none";
			}
			if(typeof(top.document.getElementById("divCommonAlertMsgNew")) != "undefined") {
				top.document.getElementById("divCommonAlertMsgNew").style.display = "none";
			}*/
			
			function open_ordered_page(){
				<?php if(isset($_GET['activateTab']) && !empty($_GET['activateTab'])){?>
					//top.refresh_control_panel('<?php echo $_GET['activateTab'];?>');
					top.change_main_Selection(top.document.getElementById('<?php echo $_GET['activateTab'];?>'));
				<?php }else {?>
					//top.refresh_control_panel(top.$("#curr_main_tab").val());
				<?php }?>			
				top.$("#li_rc").hide();//role change
				var u = top.JS_WEB_ROOT_PATH+'/interface/core/<?php echo $rd2;?>';//?1=1$str&recent_search=reload";?>';
				top.fmain.location.href = u;
			}
			open_ordered_page();
		</script>
		<?php
	}
    exit();          
}

?>