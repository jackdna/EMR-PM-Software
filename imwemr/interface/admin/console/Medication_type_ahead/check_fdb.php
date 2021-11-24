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

require_once("../../../../config/globals.php");

?><?php
//$ignoreAuth = true;
//include_once(dirname(__FILE__)."/../../../../interface/globals.php");
//require_once(dirname(__FILE__)."/../../../../interface/main/Functions.php");
//require_once(dirname(__FILE__)."/../../../../interface/common/functions.inc.php");
require_once($GLOBALS['srcdir']."/classes/cls_common_function.php");
//$objDataManage = new DataManage;
$OBJCommonFunction = new CLSCommonFunction;

$erx_credentialsRes = imw_query("SELECT eRx_user_name, erx_password, eRx_facility_id FROM users WHERE id = '".intval($_SESSION['authId'])."'");
$erx_URLres 		= imw_query("SELECT EmdeonUrl FROM copay_policies WHERE policies_id = '1'");
$erx_credentials 	= imw_fetch_assoc($erx_credentialsRes);
$erx_URLrs	 		= imw_fetch_assoc($erx_URLres);
$user				= $erx_credentials['eRx_user_name'];
$pass				= $erx_credentials['erx_password'];
$facId				= trim($_SESSION['login_facility_erx_id']);//$erx_credentials['eRx_facility_id'];
$emdeonURL			= $erx_URLrs['EmdeonUrl'];
$med_name=$_REQUEST['med_name'];
$xml_action=$_REQUEST['xml_action'];
if(isset($_REQUEST['index'])){
	$index = trim(urldecode($_REQUEST['index']));
}
function replace_fun($val){
	$val=str_replace('%','',$val);
	$val=str_replace("'",'',$val);
	return $val;
}
//to find a drug.
if($xml_action=="workview"){
	$new_fdb_id=$_REQUEST['new_fdb_id'];
	$patient_id=$_REQUEST['patient_id'];
	$med_list_qry=imw_query("select * from lists where pid='$patient_id' AND allergy_status = 'Active'");
	while($med_list_row=imw_fetch_array($med_list_qry)){
		
		if($med_list_row['title']!=""){
			$title=$med_list_row['title'];
			if($med_list_row['type']=='1' || $med_list_row['type']=='4'){
				if($med_list_row['fdb_id']>0){
					$med_xml[]="<drug id='".$med_list_row['fdb_id']."' name='".replace_fun($title)."'/>";
				}else{
					$med_qry=imw_query("select * from medicine_data where medicine_name='$title' and del_status = '0' order by fdb_id desc");
					$med_row=imw_fetch_array($med_qry);
					if(imw_num_rows($med_qry)>0){
						$medicine_name=$med_row['medicine_name'];
						$fdb_id=$med_row['fdb_id'];
						if($med_row['fdb_id']>0){
							$med_xml[]="<drug id='".$fdb_id."' name='".replace_fun($medicine_name)."'/>";
						}
					}
				}
			}
			if($med_list_row['type']=='3' || $med_list_row['type']=='7'){
				if($med_list_row['fdb_id']>0){
					$allergy_xml[]="<allergy id='".$med_list_row['fdb_id']."' name='".replace_fun($title)."' type='fdbATDrugName'/>";
				}
			}
		}
	}
	if(count($med_xml)>0){
		$med_imp_xml=implode('',$med_xml);
		$final_med_xml="<ExistingMeds>".$med_imp_xml."</ExistingMeds>";
	}
	if(count($allergy_xml)>0){
		$allergy_imp_xml=implode('',$allergy_xml);
		$final_allergy_xml="<Allergies>".$allergy_imp_xml."</Allergies>";
	}
	if($final_med_xml!='' || $final_allergy_xml!=''){
		$xml_data = "<?xml version='1.0'?>
				 <REQUEST userid='$user' password='$pass' facility='$facId'>";
		if($final_med_xml!=''){
			$xml_data .= "<OBJECT name='dur' op='screenDrugs'>
						<new_drug_name>".replace_fun($med_name)."</new_drug_name>
						<new_drug_id>".$new_fdb_id."</new_drug_id>
						".$final_med_xml."
					</OBJECT>";
		}		 
		if($final_allergy_xml!=''){
			$xml_data .= "<OBJECT name='dur' op='screenAllergies'>
						<new_drug_name>".replace_fun($med_name)."</new_drug_name>
						<new_drug_id>".$new_fdb_id."</new_drug_id>
						".$final_allergy_xml."
					</OBJECT>";
		}
		$xml_data .= "</REQUEST>";
	}
}else{
	if(trim($med_name)!=''){
		$arr_med_name = explode(' ',$med_name);
		if($arr_med_name[0]!=''&& strlen($arr_med_name[0])>=4){
			$med_name_str = "%25".$arr_med_name[0]."%25";
		}else{
			$med_name_str = "%25".$med_name."%25";	
		}
		$xml_data = "<?xml version='1.0'?>
				 <REQUEST userid='$user' password='$pass' facility='$facId'>
					<OBJECT name='drug' op='search'><name>".trim($med_name_str)."</name></OBJECT>
				 </REQUEST>";
	}
}

if($xml_data != '' && $user != '' && $pass != '' && $facId != ''){
	//TEMP LOG FILE MAKING FOR OPENED REQUEST
	$time = time();
	file_put_contents(dirname(__FILE__).'/temp_logs/emdeon_request_'.$time.'.xml',$xml_data);
	
	$URL = $emdeonURL."/servlet/XMLServlet";
	$ch = curl_init($URL);
	curl_setopt($ch, CURLOPT_MUTE, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "request=$xml_data");
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	if(stristr(strip_tags($output),'SecurityException')){
		echo '<div class="warning">Your CHC Clinician Credentials are invalid.</div>';
		die;
	}
	//TEMP LOG FILE MAKING FOR RECEIVED RESPONSE
	file_put_contents(dirname(__FILE__).'/temp_logs/emdeon_response_'.$time.'.xml',$output);

	$lastError = curl_error($ch);
	curl_close($ch);
	echo $lastError;
	if($output!=''){
		$values = $OBJCommonFunction -> XMLToArray($output);	
		$xml_med_arr=array();
		foreach($values as $key => $val){	
			if($val["tag"] =="name"){	
				$xml_med_arr['name'][]=$val["value"];
			}
			if($val["tag"]=="id"){
				$xml_med_arr['id'][]=$val["value"];	
			}
			if($val["tag"]=="severity"){
				$xml_med_arr['severity'][]=$val["value"];	
			}
			if($val["tag"]=="reaction"){
				$xml_med_arr['reaction'][]=$val["value"];	
			}
			if($val["tag"] =="drug_name"){	
				$xml_med_arr['drug_name'][]=$val["value"];
			}
			if($val["tag"] =="allergen"){	
				$xml_med_arr['allergen'][]=$val["value"];
			}
		}
		//echo "<pre>";
		//print_r($xml_med_arr);
		//exit();
		if($xml_action=="workview"){
			$pol_qry=imw_query("select cpoe_severity from copay_policies");
			$pol_row=imw_fetch_array($pol_qry);
			if(count($xml_med_arr)>0){
				for($i=0;$i<=count($xml_med_arr['reaction']);$i++){
					if($xml_med_arr["reaction"][$i]!="" && $xml_med_arr["severity"][$i]>=$pol_row['cpoe_severity'] && $xml_med_arr['allergen'][$i]==""){
						$severity = $xml_med_arr["severity"][$i];
						echo $reaction = "<ul style='padding-left:10px;'><li style='margin-bottom:-8px;'>".$xml_med_arr["reaction"][$i].' (Severity:'.$severity.')'."</li></ul>";
					}
				}
				for($i=0;$i<=count($xml_med_arr['reaction']);$i++){
					if($xml_med_arr["reaction"][$i]!="" && $xml_med_arr['allergen'][$i]!=""){
						$severity = $xml_med_arr["severity"][$i];
						echo $reaction = "<ul style='padding-left:10px;'><li style='margin-bottom:-8px;'>".$xml_med_arr["reaction"][$i]."</li></ul>";
					}
				}
			}else{
				//echo 'Unable to get Drug-drug &amp; Drug-allergy reactions.';
			}
		}else{
			$str = '<div><table class="table_collapse cellBorder4">
			<tr><td class="text_10b">&nbsp;</td><th class="text_10b" >Drug Name</th><th class="text_10b">FDB Id</th></tr>';
			for($i=0;$i<=count($xml_med_arr['name']);$i++){
				$med_name = "";
				$fdb_id = "";
				$med_name = $xml_med_arr["name"][$i];	
				$fdb_id = $xml_med_arr["id"][$i];	
				if($med_name!=""){	
					$sel="";
					if($i==0){
						//$sel="checked";
					}
					$str .= '<tr>
								<td class="text_10"><div class="radio"><input type="radio" value="'.$fdb_id.'" name="sel_fdb" id="sel_fdb'.$i.'" '.$sel.' onChange="fill_fdb_code(\''.$fdb_id.'\',\''.$index.'\')"><label for="sel_fdb'.$i.'" ></label></div></td>
								<td class="text_10">'.$med_name.'</td>
								<td class="text_10">'.$fdb_id.'</td>
							</tr>';
				}
			}
			if(count($xml_med_arr['name'])==0){
				$str .= '<tr><td class="text_10b" colspan="3" style="text-align:center;">No Record Found.</td></tr>';
			}
			echo $str .= '</table></div>';
		}
	}
}else{
	echo '<div class="warning">Please check your CHC Clinician Credentials.</div>';
}
?>
