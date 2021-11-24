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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.erx_functions.php');
$objErx	= new ERXClass;
$erx_url = "";
$errors = array();
if($_GET["patientFromSheduler"]<>""){
	$patientId = $_GET["patientFromSheduler"];
}else{
	$patientId = $_SESSION['patient'];
}
$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;
$status_heading = 'Loading...';

$isErxON = $objErx->get_erx_status_and_url();
if($isErxON && is_array($isErxON)){
	if(strtolower($isErxON['Allow_erx_medicare'])=='yes'){
		$userRes = $objErx->get_provider_erx_auth($_SESSION['authId']);
		if($userRes && is_array($userRes)){
			$eRx_user_name 		= $userRes['eRx_user_name'];
			$erx_password 		= $userRes['erx_password'];	
			$eRx_facility_id 	= trim($_SESSION['login_facility_erx_id']);	
			$erx_prescriber_id	= $userRes['eRx_prescriber_id'];
			$EmdeonUrl			= $isErxON['EmdeonUrl'];
			if(!empty($eRx_facility_id)){
				$patient = $objErx->get_patient_details();
				if($patient && is_array($patient)){
					$lname		= urlencode($patient['lname']);
					$fname		= urlencode($patient['fname']);
					$mname		= urlencode($patient['mname']);
					$street		= urlencode($patient['street']);
					$street2	= urlencode($patient['street2']);
					$city		= urlencode($patient['city']);
					$state		= $patient['state'];
					$postal_code= $patient['postal_code'];
					$sex_erx	= strtoupper(substr($patient['sex'],0,1));
					$dob		= $patient['DOB'];
					$phone_home	= $patient['phone_home'];
					$phone_biz	= $patient['phone_biz'];
					$phone_cell	= $patient['phone_cell'];
					//$ss			= $patient['ss'];
					$pid		= $patient['id'];
					//--- ERX patient data upload  ----------
					$phone_home = str_replace('-','',$phone_home);
					$phone_biz = str_replace('-','',$phone_biz);
					$ss = preg_replace('/-/','',$ss);
					list($y,$m,$d) = preg_split('/-/',$dob);
					$date_convert2 = $m.'/'.$d.'/'.$y;
					
					if(isset($_GET['loadmodule']) && trim($_GET['loadmodule'])=='ptdemo'){
						$subModuleURL = 'lab/person/PersonDemographics.jsp';
						
					}else{
						$subModuleURL = 'lab/person/PersonRxHistory.jsp';
					}
					/*******UPDATE PATIENT ALLERGY OR PROBLEM LIST HERE***/
					switch($step){
						case 0:
							$status_heading = "Updating patient allergies to eRx...";
						//	$allergy = $objErx->create_allergy_on_erx($EmdeonUrl,$eRx_user_name,$erx_password,$eRx_facility_id);
						//	if($allergy && is_array($allergy) && count($allergy)>0) break;
							break;
						case 1:
							$status_heading = "Loading eRx Panel...";
							$allergy = $objErx->create_allergy_on_erx($EmdeonUrl,$eRx_user_name,$erx_password,$eRx_facility_id);
							break;
						default:
							$status_heading = "Loading eRx Panel...";
							if(isset($_GET['loadmodule']) && trim($_GET['loadmodule'])=='prescription'){
								$erx_url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&apiLogin=true";
								$erx_url.= "&target=jsp/lab/person/PatientLookup.jsp&FromOrder=false&actionCommand=Search&FromRx=true&loadPatient=false";
								$erx_url.= "&link=false&searchaccountId=$pid&prescriberId=$erx_prescriber_id&drugFdbId=0&sig=&daySupply=&quantity=&refill=";
							}else{
								$erx_url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&apiLogin=true&target=jsp/$subModuleURL&";
								$erx_url.= "actionCommand=apiRxHistory&P_LNM=$lname&P_FNM=$fname&P_MID=$mname&P_AD2=$street2&P_ADR=$street";
								$erx_url.= "&P_CIT=$city&P_STA=$state&P_ZIP=$postal_code&P_SEX=$sex_erx&";
								$erx_url.= "P_DOB=$date_convert2&P_PHW=$phone_biz&P_PHN=$phone_home&P_CELLPHONE=$phone_cell&P_REL=1&P_ACT=$pid";
							}
							$erx_url = preg_replace('/ /','%20',$erx_url);
							
					}
					/*******UPDATE PATIENT DATA END HERE******************/					
				}else{
					$errors[] = 'Unable to fetch patient details.';
				}				
			}else{
				$errors[] = 'eRx Facility mapping error!';
			}	
		}else{
			$errors[] = 'eRx login credentials not found.';
		}
	}else{
		$errors[] = 'eRx is not enabled. Check settings with HQ Facility.';	
	}
}else{
	$errors[] = 'Unable to fetch eRx settings status. Contact Support team.';	
}?><html>
<head>
<link rel="stylesheet" href="../../library/css/common.css">
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script type="text/javascript">
if(typeof(window.opener.top.innerDim)=='function'){
	var innerDim = window.opener.top.innerDim();
	if(innerDim['w'] > 799) innerDim['w'] = innerDim['w']-100;
	if(innerDim['h'] > 599) innerDim['h'] = innerDim['h']-100;
	window.resizeTo(innerDim['w'],innerDim['h']);
}
<?php if(count($errors)==0){?>
//	window.location.href = '<?php echo $erx_url;?>';
<?php }?>
</script>
</head>
<body>
<?php if(count($errors)>0){?>
	<h3>Error!</h3>
    <ul>
    <?php echo '<li>'.implode('<li>',$errors).'</li>';?>
   	</ul>

<?php }else{?>

<h3><?php echo $status_heading;?></h3>
<script>
	<?php if(empty($erx_url)){?>
	setTimeout(function(){ window.location.href = 'erx_patient_selection.php?loadmodule=<?php echo @$_GET['loadmodule'];?>&step=<?php echo $step+1;?>'; }, 100);
	<?php }else{ ?>
	window.location.href = '<?php echo $erx_url;?>';
	<?php }?>
</script>
<?php } ?>
</body>
</html>
