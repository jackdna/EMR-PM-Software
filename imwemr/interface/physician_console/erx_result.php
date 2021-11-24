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
//require_once(dirname(__FILE__).'/../common/functions.inc.php');
$qry = imw_query("select EmdeonUrl from copay_policies where Allow_erx_medicare = 'Yes' LIMIT 1");
$qryRes = imw_fetch_assoc($qry);
$EmdeonUrl = $qryRes['EmdeonUrl'];
$pid = $_SESSION['authId'];
$qry = imw_query("select eRx_user_name, erx_password, eRx_facility_id, eRx_prescriber_id, lasteRxInboxReadTime, concat(lname,', ',fname) as name, user_npi from users where id = '$pid' LIMIT 1");
$phyRes = imw_fetch_assoc($qry);
$eRx_user_name = $phyRes['eRx_user_name'];
$erx_password = $phyRes['erx_password'];
$eRx_prescriber_id = $phyRes['eRx_prescriber_id'];
$phyName = $phyRes['name'];
$logged_user_npi =  trim($phyRes['user_npi']);
$eRx_facility_id = trim($_SESSION['login_facility_erx_id']);//$phyRes['eRx_facility_id'];

/*
$eRx_user_name = "xx";
$erx_password = "yy";
$eRx_facility_id = "zz";
$EmdeonUrl = "https://clinician.changehealthcare.com";
//*/
$checkErx = 1;
if(preg_replace('/[^0-9]/','',$phyRes['lasteRxInboxReadTime']) != '00000000000000'){
	$lastReadErxTime = strtotime($phyRes['lasteRxInboxReadTime']);
	$timeDiff = strtotime(date('Y-m-d H:i:s')) - $lastReadErxTime;
	if(isset($GLOBALS['eRxInboxReadTime']) && $timeDiff/30 < $GLOBALS['eRxInboxReadTime']){
	//	$checkErx = 0;
	}
}
if($EmdeonUrl != '' && $eRx_user_name != '' && $erx_password != '' && $checkErx){
	$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&target=servlet/servlets.apiRxServlet&actionCommand=rxinboxext&apiLogin=true&textError=true";
	$url = preg_replace('/ /','%20',$url);
	$cur = curl_init();
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
	$erx_data = curl_exec($cur);
	curl_close($cur);
	//--- Log out from emdeon erx --------
	$cur = curl_init();
	$url = "$EmdeonUrl/servlet/lab.security.DxLogout?userid=$eRx_user_name&BaseUrl=$EmdeonUrl&LogoutPath=/html/AutoPrintFinished.html";
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
	$log_data = curl_exec($cur);
	curl_close($cur);
	preg_match("/<-- ERROR :/",$erx_data_arr[0],$errorArr);
	imw_query("update users set lasteRxInboxReadTime = '".date('Y-m-d H:i:s')."' where id = '$pid'");
	
	$data = '';
	if(count($errorArr) == 0){
		$erx_data = preg_replace('/<--BEGIN RX>/','',$erx_data);
		$erx_data = trim(preg_replace('/<--END RX>/','',$erx_data));
		$erx_data_arr  = explode("^",$erx_data);
		
		/*******GETTING ARRAY OF USER WITH NPI*******/
		$res_users = imw_query("SELECT id,CONCAT(lname,', ',fname,' ',mname) AS phy_name,user_npi FROM users WHERE eRx_user_name!='' AND erx_password!='' AND user_npi!='' AND delete_status='0'");
		$array_users = array();
		$current_user_npi = '';
		if($res_users && imw_num_rows($res_users)>0){
			while($rs_users = imw_fetch_assoc($res_users)){
				$array_users[$rs_users['user_npi']] = $rs_users;
				if($_SESSION['authId']==$rs_users['id']) $current_user_npi = $rs_users['user_npi'];
			}
		}
		
		/********************************************/
		for($i=0;$i<count($erx_data_arr);$i++){
			$inbox_data = $erx_data_arr[$i];
			$inbox_data_arr = explode('|',$inbox_data);
			//pre($inbox_data_arr,1);
			
			$issue_method 	= trim($inbox_data_arr[0]); //ELECTRONIC/PRINT ETC.
			$issue_type		= trim($inbox_data_arr[2]); //NEW/RENEWAL ETC
			$erx_error		= strtolower(trim($inbox_data_arr[4])) == 'error' ? true : false;
			$erx_error_msg	= trim($inbox_data_arr[3]); //if error then text, else empty.
			$erx_status		= trim($inbox_data_arr[5]); //PENDING/AUTHORIZED ETC.
				$issued_date_arr = explode(' ',$inbox_data_arr[6]);
			$issued_date = date('m-d-Y',strtotime($issued_date_arr[0]));
			$prescription	= trim($inbox_data_arr[7]);
			$patient_name	= trim($inbox_data_arr[8]);
			$prescriber_name= trim($inbox_data_arr[9]);
			$pres_sig		= trim($inbox_data_arr[10]);
			$patient_id		= trim($inbox_data_arr[17]);
			$prescriber_npi	= trim($inbox_data_arr[19]);
			$entered_by		= trim($inbox_data_arr[22]); //erx username.
			$patient_city	= trim($inbox_data_arr[25]);
			$patient_state	= trim($inbox_data_arr[26]);
			$patient_zip	= trim($inbox_data_arr[27]);

			//Enable the line below to see only PENDING prescription records.
			if(strtolower($erx_status)!='pending') 	continue;
			if($prescriber_npi=='') 				continue; //if no prescriber npi received, skip this record.
			if($current_user_npi != $prescriber_npi && !in_array($_SESSION['logged_user_type'],array('3','13')))continue; //showing records related to logged in user only.
			
			

			$phy_name_db = 	$array_users[$prescriber_npi]['phy_name'];
		
		
			$qry_patient = "SELECT concat(lname,', ',fname,' - ',id) as patient_name, id FROM patient_data WHERE ";
			if(!empty($patient_id)){	
				$qry_patient .= "id = '$patient_id' LIMIT 0,1";	
				$res_patient = imw_query($qry_patient);
			}else{
				if(!empty($patient_name)){					
					$arrPtnm = explode(",",$patient_name);					
					$qry_patient .= "UPPER(fname) = '".strtoupper(trim($arrPtnm[1]))."' AND UPPER(lname)= '".strtoupper(trim($arrPtnm[0]))."' ";
					$res_patient = imw_query($qry_patient);
					if($res_patient && imw_num_rows($res_patient)!=1){
						if(!empty($patient_city)){					
							$qry_patient .= "AND UPPER(city) = '".strtoupper($patient_city)."' ";
						}
						if(!empty($patient_state)){
							$qry_patient .= "AND UPPER(state) = '".strtoupper($patient_state)."' ";
						}
						if(!empty($patient_zip)){
							$qry_patient .= "AND postal_code = '".$patient_zip."'";
						}
						$qry_patient .= " LIMIT 0,5";
						imw_free_result($res_patient);
						$res_patient = imw_query($qry_patient);
					}
				}
			}
			
			$PtMatch= false;
			if($res_patient && imw_num_rows($res_patient)==1){
				$rs_patient = imw_fetch_assoc($res_patient);
				$patient_id = $rs_patient['id'];
				$patient_name = $rs_patient['patient_name'];
				$PtMatch= true;
			}
			$prescrition_tr_class = '';
			if(in_array($_SESSION['logged_user_type'],array('3','13'))) $prescrition_tr_class = ' class="prescriptions_rows npi'.$prescriber_npi.' hide"';
				

			if(strtoupper($erx_status) == 'PENDING'){
				$issued_date_arr = explode(' ',$inbox_data_arr[6]);
				$issued_date = date('m-d-y',strtotime($issued_date_arr[0]));
				$data .= '
				<tr'.$prescrition_tr_class.'>
					<td valign="top">'.$issued_date.'</td>
					<td valign="top" class="text-left"><i>Method</i>: '.$issue_method.'<br><i>Type</i>: '.$issue_type.'<br><i>Rx Status</i>: '.$erx_status.'</td>
					<td valign="top" class="text-left" >
						<span class="text-left">';
						if($PtMatch){
							$data .='<a href="javascript:void(0)" class="a_clr1" onClick="open_erx(\''.$patient_id.'\');">'.ucwords($patient_name).'</a>';
						}else{
							$data .='<a href="javascript:void(0)" class="a_clr1" style="color:#f00;" onClick="alert(\'Patient ID is empty or is not matched with database records.\')">'.ucwords($patient_name).'</a>';
						}
						$data .='</span><br>
						<small>'.$prescription.'<br>'.$pres_sig.'</small>';
						if($erx_error) $data .= '<div class="warning" style="text-align:left;"><b>ERROR: </b>'.$erx_error_msg.'</div>';
				$data .= '
					</td>
					<td valign="top" class="text-left">'.ucfirst($prescriber_name).'</td>
				</tr>
				';
	
			}			
		}
	}
}
if($data != ''){?>
<button onclick="printWindow();" name="Print_window" value="Print" class="btn btn-info" id="Print_window">Print</button>

<div class="table-responsive">
<table class="table table-bordered table-striped table-hover">
	<tr class="grythead">
    	<td style="width:100px;">Issue Date</td>
        <td style="width:180px">Details</td>
        <td style="width:auto;">Prescriptions</td>
        <td style="width:180px;"><?php if(in_array($_SESSION['logged_user_type'],array('3','13'))){?>
        <select name="dd_rx_prescriber" onchange="show_selected_prescription(this);" class="form-control">
        	<option value="0">--SELECT PRESCRIBER--</option>
        <?php foreach($array_users as $npi=>$user_rs){
			echo '<option value="'.$npi.'">'.$user_rs['phy_name'].'</option>';
		}?>
        </select>
        <?php }else{?>Prescriber<?php }?></td>
    </tr>   
<?php print $data;?>
</table>
</div>
<?php }else{?><div class="alert alert-danger">No record found.</div><?php }?>