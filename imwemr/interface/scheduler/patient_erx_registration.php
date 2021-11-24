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
//require_once(getcwd()."/../common/functions.inc.php");

require_once(dirname(__FILE__).'/../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
require_once(dirname(__FILE__).'/../../library/classes/SaveFile.php');
$save_file = new SaveFile();

//--- GET ERX STATUS ----
$q= imw_query("select Allow_erx_medicare, EmdeonUrl from copay_policies");
$copay_policies_res = imw_fetch_assoc($q);
$EmdeonUrl = $copay_policies_res['EmdeonUrl'];

if(strtolower($copay_policies_res['Allow_erx_medicare']) == 'yes'){
		
	$q1= imw_query("select schedule_appointments.sa_patient_id,patient_data.lname,
			patient_data.fname,patient_data.mname,
			date_format(patient_data.DOB,'%m/%d/%Y') as DOB,patient_data.street,patient_data.postal_code,
			patient_data.city,patient_data.state,patient_data.ss,patient_data.phone_home,patient_data.sex, 
			patient_data.street2
			from schedule_appointments join patient_data on patient_data.id = schedule_appointments.sa_patient_id
			where schedule_appointments.sa_patient_app_status_id NOT IN(203,201,18,19,20)
			and schedule_appointments.sa_app_start_date = '$sel_date'
			and schedule_appointments.sa_facility_id IN ($facility_id)
			and patient_data.erx_patient_id = ''");
	$patQryResRow = imw_num_rows($q1);
	if($patQryResRow > 0){
		//---- GET ERX USER NAME AND PASSWORD ---
		$userId = $_SESSION['authId'];
		$q3 = imw_query("select eRx_user_name, erx_password,eRx_facility_id from users where id = '$userId'");
		$userRes = imw_fetch_assoc($q3);
		
		$eRx_user_name = $userRes['eRx_user_name'];
		$erx_password = $userRes['erx_password'];
		$eRx_facility_id = trim($_SESSION['login_facility_erx_id']);//$userRes['eRx_facility_id'];
		if($eRx_user_name!='' && $erx_password!=''){
			if($eRx_facility_id!='' && $eRx_facility_id!='0'){
				
				$cookie_file = $save_file->upDir.'/'.'cookie_'.$_SESSION['authId'].'.txt';
				
				//--- ERX LOGIN CALL ----
				$cur = curl_init();
				$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&target=html/LoginSuccess.html&testLogin=true";
				curl_setopt($cur,CURLOPT_URL,$url);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
				curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
				
				$data = curl_exec($cur);
				curl_close($cur);
				
				preg_match('/Login Error/',$data,$login_error);		
			
				if(count($login_error) == 0){
					while($patQryRes=imw_fetch_assoc($q1)){
						$i++;
						$patient_id = $patQryRes['sa_patient_id'];
						$lname = $patQryRes['lname'];
						$fname = $patQryRes['fname'];
						$mname = $patQryRes['mname'];
						
						$patNameArr = array();
						$patNameArr['LAST_NAME'] = $lname;
						$patNameArr['FIRST_NAME'] = $fname;
						$patNameArr['MIDDLE_NAME'] = $mname;
						$patient_name = changeNameFormat($patNameArr);
						
						$pat_dob = $patQryRes['DOB'];				
						$street = $patQryRes['street'];
						$street2 = $patQryRes['street2'];
						$postal_code = $patQryRes['postal_code'];
						$city = $patQryRes['city'];				
						$state = $patQryRes['state'];
						$ss = $patQryRes['ss'];
						$phone_home = $patQryRes['phone_home'];
						$pat_gender = substr($patQryRes['sex'],0,1);
						
						$phone_home = preg_replace('/-/','',$phone_home);
						$ss = preg_replace('/-/','',$ss);	
						
						$url = "$EmdeonUrl/servlet/servlets.apiPersonServlet?apiuserid=$eRx_user_name&actionCommand=upload&P_ACT=$patient_id&P_LNM=$lname&P_FNM=$fname&P_MID=$mname&P_ADR=$street&P_CIT=$city&P_STA=$state&P_ZIP=$postal_code&P_SEX=$pat_gender&P_DOB=$pat_dob&P_SSN=$ss&P_PHN=$phone_home&P_AD2=$street2&P_REL=1";
						$url = preg_replace('/ /','%20',$url);
						$cur = curl_init();
						curl_setopt($cur,CURLOPT_URL,$url);
						curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
						curl_setopt($cur,CURLOPT_COOKIEFILE,$cookie_file);
						curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
						$erx_data = curl_exec($cur);
		
						$erx_status_arr = preg_split('/ /',$erx_data);
						$erx_status = $erx_status_arr[count($erx_status_arr)-2];
						$erx_status_msg = strtolower($erx_status) == 'null' ? '' : 'true';
						
						if($erx_status_msg){
							$msg[] = "$patient_name - $patient_id Registered successfully With eRx.";
							imw_query("update patient_data set erx_patient_id = '$erx_status' where id = '$patient_id'");
						}
						else{
							$msg[] = "$patient_name - $patient_id not Registered With eRx.";
						}
						curl_close($cur);
					}
					
					//--- ERX LOG OUT CALL --------
					$cur = curl_init();
					$url = "$EmdeonUrl/servlet/lab.security.DxLogout?userid=$eRx_user_name&BaseUrl=$EmdeonUrl&LogoutPath=/html/AutoPrintFinished.html";
					curl_setopt($cur,CURLOPT_URL,$url);
					curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
					curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
					curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
					$data = curl_exec($cur);
					curl_close($cur);
				}
				else{
					?>
					<script type="text/javascript">
						top.fAlert('eRx login error.', '', 'parent.window.close()', '250px');
					</script>
					<?php
				}
			}else{
				die('eRx Facility mapping not done. Contact Administrator.');
			}
		}else{
			die('eRx Crdentials not found for logged in user.');
		}
	}
	else{ 
		?>
        <script type="text/javascript">
			top.fAlert('No patient to register with eRx.', '', 'parent.window.close()', '250px;');
		</script>
        <?php 
	}
}else{
	die('eRx not allowed. Contact Administrator.');
}
?>
<html>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">

<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<body>
<div class="container-fluid">  
	<?php
    for($i=0;$i<count($msg);$i++){
    ?>
    <div class="row">
    	<div class="col-sm-12"><?php print $msg[$i]; ?></div>
    </div>
    <?php
    }
    ?>
    
    <div class="row">
        <div class="col-sm-12">
        <button name="close_btn" value="Close" class="btn btn-danger" id="close_btn" onClick="javascript:parent.window.close();">Close</button>
        </div>
	</div>
</div>
</body>
</html>
<script type="text/javascript">
	parent.document.getElementById("pat_reg").innerHTML = '';
	parent.document.getElementById("divAjaxLoader").style.display = 'none';
</script>