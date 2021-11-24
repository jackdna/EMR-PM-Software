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

require_once("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");

if( is_allscripts() )
	include_once($GLOBALS['srcdir']."/allscripts/as_dataValues.php");

$_REQUEST = array_map('trim',$_REQUEST);
$medical = new MedicalHistory($_REQUEST['showpage']);

extract($_REQUEST);
$return = array('action'=>$action);

switch($action)
{
	case 'gh_blood_sugar_save':
	for($i = 1; $i <= $blood_sugar_rows; $i++)
	{
		$bs_date = getDateFormatDB($_REQUEST['blood_sugar_date'.$i]).' '.date('H:i:s');
		$bs_mg = $_REQUEST['blood_sugar_mg'.$i];
		$bs_hba = $_REQUEST['blood_sugar_hba1c'.$i];
		$bs_hba_val = $_REQUEST['blood_sugar_hba1c_val'.$i];
		$bs_fasting = $_REQUEST['blood_sugar_fasting'.$i];
		$bs_time = $_REQUEST['blood_sugar_time_of_day'.$i];
		$bs_other = $_REQUEST['blood_sugar_time_of_day_other'.$i];
		$bs_desc = $_REQUEST['blood_sugar_description'.$i];
		
		list($bs_time_seq,$bs_time_of_day) = explode("-",$bs_time);
		$bs_other = ($bs_time_of_day <> 'Other') ? '' : $bs_other;

		if( $bs_date )
		{
			$query = " INSERT INTO patient_blood_sugar SET patient_id = '".$medical->patient_id."', creation_date ='".$bs_date."', 
													sugar_value = '".$bs_mg."', is_fasting='".$bs_fasting."', time_of_day='".$bs_time_of_day."',
													time_of_day_other='".$bs_other."', 	time_of_day_sequence='".$bs_time_seq."', description = '".$bs_desc."',
													created_on = now(), created_by = '".$_SESSION["authId"]."', modified_on = now(), modified_by = '".$_SESSION["authId"]."',
													hba1c = '".$bs_hba."', hba1c_val = '".$bs_hba_val."' "; 
			$sql = imw_query($query);
			$id = imw_insert_id();
			$return['id'.$i] = $id;	
		}
	}
	break;
	
	case 'gh_cholesterol_save':
	for($i = 1; $i <= $cholesterol_rows; $i++)
	{
		$c_date = getDateFormatDB($_REQUEST['cholesterol_date'.$i]).' '.date('H:i:s');
		$c_total = $_REQUEST['cholesterol_total'.$i];
		$c_trig = $_REQUEST['cholesterol_triglycerides'.$i];
		$c_ldl= $_REQUEST['cholesterol_LDL'.$i];
		$c_hdl = $_REQUEST['cholesterol_HDL'.$i];
		$c_desc = $_REQUEST['cholesterol_description'.$i];
		if( $c_date && $c_total)
		{
			$query = " INSERT INTO patient_cholesterol SET patient_id = '".$medical->patient_id."', creation_date ='".$c_date."', 
													cholesterol_total = '".$c_total."', cholesterol_triglycerides='".$c_trig."', cholesterol_LDL='".$c_ldl."',
													cholesterol_HDL='".$c_hdl."', description = '".$c_desc."', created_on = now(), created_by = '".$_SESSION["authId"]."',
													modified_on = now(), modified_by = '".$_SESSION["authId"]."' "; 
			$sql = imw_query($query);
			$id = imw_insert_id();
			$return['id'.$i] = $id;
		}
	}
	break;
	
	case 'delete_blood_sugar':
		$primary_key_id = (int) $primary_key_id;
		if($primary_key_id)
		{
			$query = "Delete FROM patient_blood_sugar WHERE id = '".$primary_key_id."'";
			$sql = imw_query($query);
		}
		$return['del_status'] = $sql ? true : false;
		
	break;
	
	case 'delete_cholesterol':
		$primary_key_id = (int) $primary_key_id;
		if($primary_key_id)
		{
			$query = "Delete FROM patient_cholesterol WHERE id = '".$primary_key_id."'";
			$sql = imw_query($query);
		}
		$return['del_status'] = $sql ? true : false;
		
	break;
	
	case 'blood_sugar_hx':
		$return['bs_history'] = $medical->hx_data('blood_sugar');
	break;
	
	case 'blood_sugar_graph':
		$return['bs_graph'] = $medical->graph_data('blood_sugar');
	break;
			
	case 'cholesterol_hx':
		$return['ch_history'] = $medical->hx_data('cholesterol');
	break;
	
	case 'cholesterol_graph':
		$return['ch_graph'] = $medical->graph_data('cholesterol');
	break;
	
	case 'cholesterol_ldl_graph':
		$return['ch_ldl_graph'] = $medical->graph_data('cholesterol_ldl');
	break;
	
	case 'cholesterol_hdl_graph':
		$return['ch_hdl_graph'] = $medical->graph_data('cholesterol_hdl');
	break;
	
	case 'admin_medicine' :
		$r = $medical->admin_medicine($subAction,$mName,$recordId);
		$return['result'] = $r;	
		$return['data'] = $medical->admin_medicine_data();
		$return['subAction'] = $subAction;
	break;
	
	case 'medicine_typeahead':
		$medicine_typeahead = $medical->medicine_typeahead();
		extract($medicine_typeahead);
		$return['medicationTitleArr'] = $medicationTitleArr;
		$return['medication_ccdacode_Arr'] = $medication_ccdacode_Arr;
		$return['arrMedicines'] = $arrMedicines;
		$return['medication_doses_Arr'] = $medication_doses_Arr;
		$return['medication_sig_Arr'] = $medication_sig_Arr;
		$return['fdb_id_arr'] = $fdb_id_arr; 
	break;
	
	case 'med_external':
		$return['data'] = $medical->med_external();
	break;
	
	case 'med_history':
		$return['data'] = $medical->med_history($id);
	break;	

	/*Search for the medications in TW:*/
	case 'search_tw_medication':

		if( is_allscripts() && array_key_exists('as_id', $_SESSION) && !empty($_SESSION['as_id']) && $_SESSION['as_id'] != '0')
		{
			try
			{
				$asData = new as_dataValues();
				$return['medsData'] = array();
				$medstosearch = $_POST['medNames'];

				if( is_array($medstosearch) && count($medstosearch) > 0)
				{
					foreach ($medstosearch as $key => $value)
					{
						$twMedsEntry = $asData->query($value, 'medications', $_SESSION['as_id']);
						$return['medsData'][$key] = $twMedsEntry;
					}
				}	
			}
			catch( asException $e)
			{

			}
			catch (Exception $e)
			{
			
			}
		}
	break;

	default:
		//do nothing..
}

echo json_encode($return);
?>
