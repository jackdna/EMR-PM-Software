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
	$pid = $_POST['pid'];
	$rqFindBy = $_REQUEST["findBy"];
	
	$fax = (isset($_REQUEST['from']) && $_REQUEST['from']==='inbound_fax')?true:false;
	$response = array('status'=>'failed', 'pId'=>'', 'pname'=>'');	/**Response Container for fax*/
	
    //break glass privilege check
    $isBGPriv = (core_check_privilege(array("priv_break_glass")) == true) ? 1 : 0;
    $bgPriv = ($isBGPriv == true) ? "y" : "n";
    
	if($rqFindBy == "External MRN"){
		$external_MRN = trim($pid);
		$MRNSearch="(TRIM(LEADING '0' FROM External_MRN_1) = '".(int)$external_MRN."' or TRIM(LEADING '0' FROM External_MRN_2) = '".(int)$external_MRN."') ";
		if(constant("DISP_EXTERNAL_MRN")==1){$MRNSearch="TRIM(LEADING '0' FROM External_MRN_1) = '".(int)$external_MRN."' ";}
		if(constant("DISP_EXTERNAL_MRN")==2){$MRNSearch="TRIM(LEADING '0' FROM External_MRN_2) = '".(int)$external_MRN."' ";}

	//	$qry = "SELECT ".$search_cols.", DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS FROM patient_data WHERE ".$MRNSearch;
		
		$qry = "SELECT id,lname,fname,mname,suffix FROM patient_data WHERE $MRNSearch";
	}
	else{
		$qry = 'SELECT id,lname,fname,mname,suffix FROM patient_data WHERE id="'.$pid.'"';
	}
	$result_obj = imw_query($qry);
	if(imw_num_rows($result_obj) > 0){
		$result_arr = imw_fetch_assoc($result_obj);
		$pid = $result_arr['id'];
		$askForReason = sa_core_get_restricted_status($result_arr["id"]);
        if($askForReason == true){
            $response['askForReason'] = $askForReason;
            $response['patId'] = $result_arr["id"];
            $response['bgPriv'] = $bgPriv;
        } else {
            $patientname = '';
            if($result_arr['fname']!="")
                $patientname=$result_arr['fname'];
            if($result_arr['mname']!="")
                $patientname.=' '.$result_arr['mname'];
            if($result_arr['lname']!="")
                $patientname.=' '.$result_arr['lname'];
            if($result_arr['suffix']!="")
                $patientname.=' '.$result_arr['suffix'];

            if($fax){
                $response['status'] = 'success';
                $response['pId'] = $result_arr['id'];
                $response['pname'] = $patientname;
            }
            else
            {
                $response = $pid;	
            }
        }
	}
	else{
		if($fax)
			$response['status'] = 'failed';
		else
			$response = 'n';	
	}
	echo (is_array($response))?json_encode($response):$response;
    
    
    
    ###################################################################
	#   Getting provider's restricted status for particular patient
	###################################################################
	function sa_core_get_restricted_status($patient_id){	
		$askForReason = false;
		
		if(empty($patient_id)){
			return $askForReason;
		}
		
		if(isset($_SESSION["glassBreaked_ptId"])){
			if($_SESSION["glassBreaked_ptId"] == $patient_id){
				return $askForReason;
			}
		}
		
		$sql_getRestricted = "SELECT restrict_providers FROM restricted_providers where patient_id ='".imw_real_escape_string($patient_id)."' and restrict_providers != ''";
		$res_Restricted = imw_query($sql_getRestricted);				
		$num_rows = 0;	
		if($res_Restricted){
			$num_rows = imw_num_rows($res_Restricted);
			if($num_rows > 0){
				$resultRow = imw_fetch_array($res_Restricted);
				$explodeArray = explode(",", $resultRow["restrict_providers"]);
				if(is_array($explodeArray)){
					if(in_array($_SESSION["authId"], $explodeArray)){
						$askForReason = true;
					}
				}
			}
		}
		return $askForReason;
	}
    
?>