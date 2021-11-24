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
File: ajax_validation.php
Purpose: Patient demographics validation
Access Type: Direct 
*/
include_once("../../../../config/globals.php");
$_REQUEST = array_map('trim',$_REQUEST);
extract($_REQUEST);
$userName = xss_rem($_REQUEST['userName']);
$ssnNumber = xss_rem($_REQUEST['ssnNumber']);
$resSsnNumber = xss_rem($_REQUEST['resSsnNumber']);


$return = array('action' => $action);

$strUserName = $strSSNNumber = "0";

$pid = $_SESSION['patient'];
if($pid == ""){
	if($ssnNumber != ""){		
		$qryGetSSNUnique = "select id from patient_data where ss='".$ssnNumber."' LIMIT 0,10";		
		$rsGetSSNUnique = imw_query($qryGetSSNUnique);
		if($rsGetSSNUnique){
			if(imw_num_rows($rsGetSSNUnique) > 0){
				$strSSNNumber = "1";
			}
			imw_free_result($rsGetSSNUnique);	
		}
	}	
	if($userName != ""){		
		$qryGetUserNameUnique = "select id from patient_data where username='".$userName."' LIMIT 0,10";		
		$rsGetUserNameUnique = imw_query($qryGetUserNameUnique);
		if($rsGetUserNameUnique){
			if(imw_num_rows($rsGetUserNameUnique) > 0){
				$strUserName = "1";
			}
			imw_free_result($rsGetUserNameUnique);	
		}
	}	
}
elseif($pid != ""){
	if($ssnNumber != ""){		
		$qryGetSSNUnique = "select id, fname, mname, lname from patient_data where ss='".$ssnNumber."' AND ss != '000-00-0000' and id != '".$pid."' LIMIT 0,10";		
		$rsGetSSNUnique = imw_query($qryGetSSNUnique);
		if($rsGetSSNUnique){
			if(imw_num_rows($rsGetSSNUnique) > 0){
				$strSSNNumber = "1";
				$ssn_default_height = 400;
				$ssn_recordwise_height = imw_num_rows($rsGetSSNUnique) * 20;
				if($ssn_recordwise_height > $ssn_default_height){$ssn_recordwise_height = $ssn_default_height;}
				$strSSNDetail = '<dl>
													<dt>Following Patient(s) have same SSN#:</dt>
												';
				while($rsGetSSN = imw_fetch_array($rsGetSSNUnique)){
					$name  = '';
					$name .= !empty($rsGetSSN['lname']) ? ' '.$rsGetSSN['lname'].', ' : '';
					$name .= !empty($rsGetSSN['fname']) ? $rsGetSSN['fname'] : '';
					$name .= !empty($rsGetSSN['mname']) ? ' '.$rsGetSSN['mname'] : '';
					
					$strSSNDetail .= '<dd><b>&raquo; '.$name .'</b> <small> - '.$rsGetSSN['id'].'</small></d>';
				}
				$strSSNDetail .= '</dl>';
			}
			imw_free_result($rsGetSSNUnique);	
		}
	}	
	if($userName != ""){		
		$qryGetUserNameUnique = "select id from patient_data where username='".$userName."' and id != '".$pid."' LIMIT 0,10";		
		$rsGetUserNameUnique = imw_query($qryGetUserNameUnique);
		if($rsGetUserNameUnique){
			if(imw_num_rows($rsGetUserNameUnique) > 0){
				$strUserName = "1";
			}
			imw_free_result($rsGetUserNameUnique);	
		}
	}	
	if($resSsnNumber != ""){
		$qry = "select id, fname, mname, lname from patient_data where ss='".$resSsnNumber."' AND ss != '000-00-0000' LIMIT 0,10";		
		$res = imw_query($qry);
		if(imw_num_rows($res)>0){
			$strResPartyBox = 1;
		}else{
			$strResPartyBox = 0;
		}
	}
}

$data = $strSSNNumber."~~".$strUserName."~~".$strSSNDetail."~~".$strResPartyBox;
$return['response'] = $data;
//$return['ssn'] = $qryGetSSNUnique;

echo json_encode($return);
?>