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
include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once(dirname(__FILE__)."/../../../library/classes/class.mur_reports.php");
include_once(dirname(__FILE__)."/../../../library/classes/class.erx_functions.php");
$objMUR			= new MUR_Reports;
$objERX			= new ERXClass;
$cookie_path 	= $objERX->cookie_path;
//--- GET ERX STATUS AND EMDEON ACCESS URL -------
$get_erx_status_and_url = $objERX->get_erx_status_and_url();
$Allow_erx_medicare	= $get_erx_status_and_url['Allow_erx_medicare'];
$EmdeonUrl			= $get_erx_status_and_url['EmdeonUrl'];
/*GET POSTED VALUES*/
$st					= isset($_POST['st']) ? intval($_POST['st']) : '';
$provider 			= intval($_POST['provider']);

$temp_createdBy 	= $objMUR->get_provider_ar($provider);
$createdFor 		= $temp_createdBy[$provider];

$eRx_prescriber_id 	= isset($_POST['eRx_prescriber_id']) ? $_POST['eRx_prescriber_id'] : '';
$eRx_facility_id 	= isset($_POST['eRx_facility_id']) ? $_POST['eRx_facility_id'] : trim($_SESSION['login_facility_erx_id']);
$dtfrom 			= isset($_POST['dtfrom']) ? trim(strip_tags($_POST['dtfrom'])) : date('m-01-Y');
$dtupto 			= isset($_POST['dtupto']) ? trim(strip_tags($_POST['dtupto'])) : date('m-d-Y');
$dtfrom1 			= $objMUR->dbdtfrom;
$dtupto1 			= $objMUR->dbdtupto;
//die($dtfrom1.' -- '.$dtupto1);
$date_range_array	= isset($_POST['date_range_array']) ? unserialize(html_entity_decode($_POST['date_range_array'])) : array();
$appt_erx_facilities= isset($_POST['appt_erx_facilities']) ? unserialize(html_entity_decode($_POST['appt_erx_facilities'])) : array();
//--- GET ERX USERNAME, PASSWORD & FACILITY_ID --------
if(empty($eRx_facility_id)==true || empty($eRx_prescriber_id)==true){
	$phyQryRes 			= $objERX->get_provider_erx_auth($provider);
	$eRx_user_name		= $phyQryRes['eRx_user_name'];
	$erx_password		= $phyQryRes['erx_password'];
	$eRx_prescriber_id	= $phyQryRes['eRx_prescriber_id'];
	$eRx_facility_id	= trim($_SESSION['login_facility_erx_id']);
	$date_range_array 	= createDateRangeArray($dtfrom1,$dtupto1);
	$appt_erx_facilities= getApptFacilities($provider,$dtfrom1,$dtupto1);
	
}



//FUNCTION TO GET DATE RANGE ARRAY.
function createDateRangeArray($strDateFrom,$strDateTo) {
  // takes two dates formatted as YYYY-MM-DD and creates an inclusive array of the dates between the from and to dates.
  $aryRange=array();
  $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
  $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
  if ($iDateTo>=$iDateFrom) {
    array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
    while ($iDateFrom<$iDateTo) {
      $iDateFrom+=86400; // add 24 hours
      array_push($aryRange,date('Y-m-d',$iDateFrom));
    }
  }
  return $aryRange;
}

function getApptFacilities($p,$d1,$d2){
	$q = "SELECT distinct(fe.fac_obj_id) FROM schedule_appointments sa 
					JOIN facility f ON (f.id = sa.sa_facility_id) 
					JOIN facilities_emdeon fe ON (fe.id = f.erx_facility_id) 
					WHERE sa.sa_doctor_id = '".$p."' 
					AND sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3)  
					AND date_format(sa.sa_app_start_date,'%Y-%m-%d')>='".$d1."' 
					AND date_format(sa.sa_app_start_date,'%Y-%m-%d')<='".$d2."'";
	$res = imw_query($q);
	$erx_Facs = false;
	if($res && imw_num_rows($res)>0){
		$erx_Facs = array();
		while($rs = imw_fetch_assoc($res)){
			$erx_Facs[] = $rs['fac_obj_id'];
		}
	}
	return $erx_Facs;
}

//---FUNCTION TO GET EMDEON PRESCRIBER IDs---
function updateEmdeonPrescriberIDs($provider,$eRx_user_name,$erx_password,$eRx_facility_id){
	global $EmdeonUrl; global $cookie_path;
	$cookie_file = $cookie_path.'/cookie_'.$provider.'.txt';
	$cur = curl_init();
	$url = $EmdeonUrl."/servlet/DxLogin?userid=".$eRx_user_name."&PW=".$erx_password."&target=html/LoginSuccess.html&testLogin=true";
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
	$data = curl_exec($cur);
	curl_close($cur);
	preg_match('/Login Success/',$data,$loginSuccessArr);
	if(count($loginSuccessArr)){
		$url = $EmdeonUrl."/servlet/servlets.apiPersonServlet?actionCommand=listCaregivers&apiuserid=".$eRx_user_name."&facilityobjid=".$eRx_facility_id;
		$cur = curl_init();
		curl_setopt($cur,CURLOPT_URL,$url);
		curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($cur,CURLOPT_COOKIEFILE,$cookie_file);
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
		$cg_data = curl_exec($cur);
		preg_match('<--BEGIN CAREGIVER LIST>',$cg_data,$correct_cg_data);
		if($correct_cg_data){
			$cg_data = preg_replace('/<--BEGIN CAREGIVER LIST>/','',$cg_data);
			$cg_data = preg_replace('/<--END CAREGIVER LIST>/','',$cg_data);
			file_put_contents($cookie_path."/caregivers_".date('Ymd').".txt",$cg_data);
			$cg_data_arr = preg_split('/'.chr(10).'/', $cg_data, -1, PREG_SPLIT_NO_EMPTY);	//pre($cg_data_arr);
			if(count($cg_data_arr)>=3){
				$erx_provider_id = $erx_provider_name = $erx_provider_npi = '';
				for($i=0;$i<count($cg_data_arr);$i++){
					$curr_line = $cg_data_arr[$i];
					$curr_line_arr = explode('=',$curr_line);
					$field = $curr_line_arr[0];
					$value = $curr_line_arr[1];
					switch($field){
						case 'CAREGIVEROBJID'	:	$erx_provider_id	= $value; break;
						case 'CAREGIVERNAME'	:	$erx_provider_name	= $value; break;
						case 'CAREGIVERNPI'		:	$erx_provider_npi	= $value; break;
					}
					
					if($erx_provider_id != '' && $erx_provider_name != '' && $erx_provider_npi != ''){
						$pro_name_arr	= explode(',',$erx_provider_name);
						//pre($pro_name_arr);
						$pro_lname 		= trim($pro_name_arr[0]);//var_dump(substr(trim($pro_name_arr[1]),-2,1));
						if(trim($pro_name_arr[1]) != '' && strpos(trim($pro_name_arr[1]),' ')> 0){
							$pro_fname_arr	= explode(' ',trim($pro_name_arr[1]));
							$pro_fname 		= $pro_fname_arr[0];
							$pro_mname		= $pro_fname_arr[1];
						}else{
							$pro_fname 		= trim($pro_name_arr[1]);
						}
						//echo $erx_provider_id.' = '.$pro_lname.'::'.$pro_fname.'::'.$pro_mname.' = '.$erx_provider_npi.'<br>';
						$sql1 = "UPDATE users SET eRx_prescriber_id='".$erx_provider_id."' 
								WHERE user_npi = '$erx_provider_npi' 
								AND user_npi <> '' LIMIT 1";//echo $sql1.'<br>';
						$res1 = imw_query($sql1);
						$erx_provider_id = $erx_provider_name = $erx_provider_npi = '';
					}
					
				}
			}						
		}else{
			$error = 'Unable to get CHC Caregiver ID. Contact administrator.';
		}
	}else{
		$error = 'Please check your eRx Crednetials on Clinician Website. ';
	}
	// AGAIN GETTING ERX PROVIDER ID.
	$get_pres_id_q 		= "select eRx_prescriber_id FROM users WHERE id = '$provider' AND eRx_prescriber_id > 0 AND eRx_prescriber_id != ''";
	$get_pres_id_res 	= imw_query($get_pres_id_q);
	if($get_pres_id_res && imw_num_rows($get_pres_id_res)==1){
		$get_pres_id_rs		= imw_fetch_assoc($get_pres_id_res);
		$eRx_prescriber_id 	= $get_pres_id_rs['eRx_prescriber_id'];
	}
	if(isset($eRx_prescriber_id) && $eRx_prescriber_id != '' && $eRx_prescriber_id > 0){
		return $eRx_prescriber_id;
	}else{
		return $error;
	}
}

//$dtfrom 			= preg_replace('/-/','/',$dtfrom);
//$dtupto 			= preg_replace('/-/','/',$dtupto);
//echo $dtfrom.' :: '.$dtupto;die;
$taskStatus = 'working';
$msgArr = array();

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>MUR eRx DOWNLOAD</title>
<link href="../../../library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
<link href="../../../library/css/bootstrap.min.css" type="text/css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="../../../library/css/common.css" />

<script type="text/javascript">
window.onload = function(){
		window.resizeTo(500, 600);                             // Resizes the new window
		window.focus();
}
</script>
</head>
<body scroll="yes">
<div class="container-fluid">
<div class="panel panel-primary">
	<div class="panel-heading"><span class="pull-right">Report for: <?php echo $createdFor;?></span>&nbsp;Download eRx</div>
    <div class="panel-body">
    <?php
    if($Allow_erx_medicare!='Yes'){
        $msgArr['Error'][] = 'eRx is disabled from Settings > HQ Facility.';
    }
    if($EmdeonUrl==''){
        $msgArr['Error'][] = 'eRx URL not found. Check eRx settings with HQ Facility.';
    }
    if(empty($eRx_facility_id) == true){
        $msgArr['Error'][] = 'CHC Facility ID not found. Cannot proceed.';	
    }else{
        if(empty($eRx_prescriber_id)==true || $eRx_prescriber_id==0){
            $eRx_prescriber_id = updateEmdeonPrescriberIDs($provider,$eRx_user_name,$erx_password,$eRx_facility_id);
            if(empty($eRx_prescriber_id)==true){
                $msgArr['Error'][] = 'Unable to SET/GET CHC Prescriber ID.<br>(<i>i.e. Provider\'s NPI must be same as on Clinician website.</i>)';
            }
            preg_match('/unable/',strtolower($eRx_prescriber_id),$prescribeIDerror);
            preg_match('/crednetials/',strtolower($eRx_prescriber_id),$prescribeIDerror1);
            if(count($prescribeIDerror)>0 || count($prescribeIDerror1)>0){
                $msgArr['Error'][] = $eRx_prescriber_id;
                $eRx_prescriber_id = '';
            }
        }
    }
    if(empty($eRx_prescriber_id)==true || empty($eRx_user_name)==true || empty($erx_password)==true){
        $msgArr['Error'][] = 'Some required information (<i>i.e. eRx Prescriber ID / eRx UserName / eRx Password</i>) missing. Cannot proceed. Contact administrator.';
    }

    if(count($msgArr['Error'])>0){
        echo '<ul class="m10"><li>'.implode('</li><li>',$msgArr['Error']).'</li></ul>';
        $noJS = true;
    }else{
       // pre($_POST);
        if(is_array($appt_erx_facilities) && count($appt_erx_facilities)>0){
            if(is_int($st) && $st>=0 && $st<count($date_range_array)){
                $prescrip_date = $date_range_array[$st];
                //SETTING THIS DATE COUNT TO ZERO IF FOUND.
                $temp_prescrip_date_arr = explode('-',$prescrip_date);
				
                $erx_count_record_id = '';
                
                $prescrip_date2 = $temp_prescrip_date_arr[1].'/'.$temp_prescrip_date_arr[2].'/'.$temp_prescrip_date_arr[0];
                //$prescrip_date2 = preg_replace('/-/','/',$prescrip_date2);
        
                $q1 = "select id from emdeon_erx_count where provider_id='".$provider."' AND date='".$prescrip_date."'";
                $res1 = imw_query($q1);//echo imw_error();
                if($res1 && imw_num_rows($res1)==1){
                    $rs1 = imw_fetch_assoc($res1);
                    $erx_count_record_id = $rs1['id'];
                }
                if($erx_count_record_id==''){
                    $r = imw_query("INSERT INTO emdeon_erx_count (provider_id,date,prescriptions) VALUES('$provider','$prescrip_date',0)");
                    $erx_count_record_id = imw_insert_id();
                }else{
                    imw_query("UPDATE emdeon_erx_count SET prescriptions = '0' WHERE id= '".$erx_count_record_id."'");	
                }
                
                $found_prescription_count = 0;
                $patient_list_id="";
                echo '<div class="alignCenter text12b m10" style="color:#333"><br><br>***Checking for DATE: '.str_replace('/','-',$prescrip_date2).'***</div>';
                    
                    foreach($appt_erx_facilities as $appt_erx_facility){
                    //die('date='.$prescrip_date);			
                    $xml_data = "<?xml version='1.0'?>
                        <REQUEST userid='".$eRx_user_name."' password='".$erx_password."' facility='".$eRx_facility_id."'>
                        <OBJECT name='rx' op='search_prescriber' rx_type='New'>
                            <rx_issue_type>Electronic</rx_issue_type>
                            <rx_status>Authorized</rx_status>
                            <prescriber>".$eRx_prescriber_id."</prescriber>
                            <organization>".$appt_erx_facility."</organization>
                            <creation_date_from>".$prescrip_date2."</creation_date_from>
                            <creation_date_to>".$prescrip_date2."</creation_date_to>
                        </OBJECT>
                        </REQUEST>
                    ";//echo htmlentities($xml_data);die;
                    $ch = curl_init($EmdeonUrl."/servlet/XMLServlet");
    
                    curl_setopt($ch, CURLOPT_MUTE, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, "request=$xml_data");
                    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $output = curl_exec($ch);
					$erxprescript_xml_file = $cookie_path.'/eRx_prescriptions_'.$provider.'_'.date('Ymd').'.xml';
                    file_put_contents($erxprescript_xml_file,$output);
                    $lastError = curl_error($ch); 
                    curl_close($ch);
                   // echo 'Output= '.htmlentities($output).'<hr>';
                    //echo '<br>Error= '.$lastError;die;
                    if($lastError==''){
                        
                        $xml = simplexml_load_string($output);
                        $json = json_encode($xml);
                        $array = json_decode($json,TRUE);
                        for($i=0;$i<count($array['OBJECT']);$i++){
                         $patient_list_id .=$array['OBJECT'][$i]['patient_hsi_value'].',';
                        }
                        $patient_list_id=substr($patient_list_id,0,-1);
                        $xml=simplexml_load_file($erxprescript_xml_file);
                        $prescriptions = $xml->OBJECT;
                       
                        $found_prescription_count += count($prescriptions);
                    }else{
                        echo $lastError;
                    }

                }
               // echo $patient_list_id;
                echo '<br><br><b># of Prescriptions found=</b> '.count($prescriptions).'<br><br>'; //# OF PRESCRIPTIONS
                $q2 = "UPDATE emdeon_erx_count SET prescriptions = ".$found_prescription_count.",patient_id='".$patient_list_id."' WHERE id= '".$erx_count_record_id."'";
              //  echo $q2.'<br>';
                imw_query($q2);
                $st++;
            }else if(is_int($st) && $st>=0 && $st>=count($date_range_array)){
                echo '<div class="alignCenter text12b m10" style="color:#00F"><br><br>*********eRx DOWNLOAD COMPLETE. Close this popup and re-run MUR.*********</div>';
                $noJS= true;
            }else{
                echo '<div class="subsection ml20 alignCenter padd10" style="width:90%;"><input class="btn btn-success" type="button" value="Start Process" onclick="document.frm_cpoe.submit();"></div>';
                $noJS= true;
            }	
        }else{
            echo '<div class="alignCenter text12 m10" style="color:#F00"><br><br><b>ERROR:</b> 	Schedule Facility (for appointments of this EP durig MU period) is not mapped with eRx Facility.<br>OR<br>No appointments found during this duration.</div>';
            $noJS= true;
        }
        
    }
    ?>
    </div>
</div>
</div>
<form name="frm_cpoe" id="frm_cpoe" method="post">
	<input type="hidden" name="provider" id="provider" value="<?php echo $provider;?>">
    <input type="hidden" name="eRx_prescriber_id" id="eRx_prescriber_id" value="<?php echo $eRx_prescriber_id;?>">
    <input type="hidden" name="eRx_facility_id" id="eRx_facility_id" value="<?php echo $eRx_facility_id;?>">
    <input type="hidden" name="eRx_user_name" id="eRx_user_name" value="<?php echo $eRx_user_name;?>">
    <input type="hidden" name="erx_password" id="erx_password" value="<?php echo $erx_password;?>">
    <input type="hidden" name="date_range_array" id="date_range_array" value="<?php echo htmlentities(serialize($date_range_array));?>"> 
	<input type="hidden" name="appt_erx_facilities" id="appt_erx_facilities" value="<?php echo htmlentities(serialize($appt_erx_facilities));?>">
	<input type="hidden" name="st" id="st" value="<?php echo intval($st);?>">
	<input type="hidden" name="dtfrom" id="dtfrom" value="<?php echo $dtfrom;?>">
	<input type="hidden" name="dtupto" id="dtupto" value="<?php echo $dtupto;?>">
</form>
<?php if ($noJS != true){?>
<script type="text/javascript"> 
setTimeout(function(){document.frm_cpoe.submit();},200);
</script>
<?php }?>
</body>
</html>
