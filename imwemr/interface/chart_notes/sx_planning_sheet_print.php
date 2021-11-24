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

File: sx_planning_sheet_print.php
Purpose: This file contain print section of Print meds in order/order set pop up.
Access Type : Direct
*/
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

//Checking For IOL  Link Syncing
$ioSync = (isset($_REQUEST['sa_date']) && empty($_REQUEST['sa_date']) == false && isset($_REQUEST['mode']) && (strtolower($_REQUEST['mode']) == 'send' || strtolower($_REQUEST['mode']) == 'resync')) ? true : false;
$chartId = '';

//IOLink Sync Directory
$iolLinkDir = $iolinkDirPath;
$sxPrintPtid = (isset($_REQUEST['patientId']) && empty($_REQUEST['patientId']) == false) ? $_REQUEST['patientId'] : $_SESSION['patient'];

//Only include these files if call is not From iAsc Sync
if($ioSync === false){
	require_once(dirname(__FILE__).'/../../config/globals.php');
	require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
	include_once $GLOBALS['srcdir']."/classes/SaveFile.php";
	include_once $GLOBALS['srcdir']."/classes/common_function.php";
	require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
	require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
	include_once $GLOBALS['srcdir']."/classes/work_view/Patient.php";
	include_once $GLOBALS['srcdir']."/classes/work_view/sx_plan.class.php";	
	
	//Empty if call is not from iAsc Sync
	$chartId = '';
}else{
	include_once $GLOBALS['srcdir']."/classes/work_view/Patient.php";
	include_once $GLOBALS['srcdir']."/classes/work_view/sx_plan.class.php";	
	//If call is for Sync
	
	//HTML 2 PDF
	//require($GLOBALS['srcdir']."/html_to_pdf/html2pdf.class.php");
	
	$patient_id = $sxPrintPtid;
	
	//Get SA Doctor ID
	$qrySID	=	"SELECT sa_doctor_id FROM schedule_appointments WHERE sa_patient_id = '".$patient_id."' and id = '".(int)$schedule_id."' "; 
	$sqlSID	=	imw_query($qrySID);
	$rowSID	=	imw_fetch_assoc($sqlSID);
	$sa_doctor_id = $rowSID['sa_doctor_id'];
	
	//Create Object
	$sx_obj = New Sx_Plan($patient_id);
	$sx_obj->sa_doctor_id = (int)$sa_doctor_id;
	
	//Get Previous Eye Data
	$prevEyeArr = $sx_obj->getPrevEyeValues($patient_id);
	
	//If ID exists than set it as the new Chart ID to retrive the assoc. data
	$chartId = (isset($prevEyeArr['ID']) && empty($prevEyeArr['ID']) == false) ? $prevEyeArr['ID'] : '';
	unset($sx_obj);
	unset($patient_id);
}


function getPatientLastAppointment($pid){
	$return_arr = array();
	$qry = imw_query("select sa_doctor_id , sa_facility_id from schedule_appointments
			where sa_patient_app_status_id not in (201, 18, 203, 19, 20)
			and sa_patient_id = '$pid' and sa_app_start_date <= now()
			order by sa_app_start_date desc, sa_app_starttime desc limit 0, 1"); 
	if(imw_num_rows($qry) > 0){
		while($row = imw_fetch_assoc($qry)){
			$return_arr[] = $row;
		}
	}
	return $return_arr;
}

$chartId = (isset($_REQUEST['chartId']) && empty($_REQUEST['chartId']) == false) ? $_REQUEST['chartId'] : $chartId;
$sxObj = New Sx_Plan($sxPrintPtid,$chartId);
$pid = $sxPrintPtid;

//Fetching Data
$sx_plan_data = '';
$sx_plan_data = $sxObj->get_sx_plan_data($sxObj->patient_id,$sxObj->sx_plan_id);

if(empty($sx_plan_data) == false) extract($sx_plan_data);

//Allergies
$ptAllergies = "No";
$allergy = $sxObj->getAllergies($sxObj->patient_id,"title",1);
$checkAllergy = commonNoMedicalHistoryAddEdit($moduleName="Allergy",$moduleValue="",$mod="get");
if($checkAllergy == "checked"){
	$allergy = "2";	  // means NKA checkbox is checked
}
$ptAllergies=($allergy=="1") ? "Yes" : "No";

//Diabetes
$strPtDiabetic = $sxObj->getPtDiabeticVal($sxObj->patient_id);
$arrPtDiabetic =  explode(" -- ", $strPtDiabetic);
$ptDiabetic = $arrPtDiabetic[0];

//Flomax
$pt_flomax = "No"; //from latest ascan or IOL_master
$pt_flomax = $sxObj->getPtFlomax($sxObj->patient_id);

//Multi phy data
$phy_dt_str = $sxObj->getMultiPhy($sxObj->patient_id);
$arr_phyInfo = explode("!@!", $phy_dt_str);
$ptPcp = $arr_phyInfo[1];
$ptRefer = $arr_phyInfo[0];
$ptCoManage = $arr_phyInfo[2];

//Medication
$ar_pt_med = $sxObj->sps_getPtMeds($sxObj->patient_id);

//Pt choice arr
$ar_admn_pt_choices = $sxObj->sps_get_pt_choices();
$ar_admn_mbn = $sxObj->sps_get_mbn();
$ar_admn_toric_btn = $sxObj->sps_get_toric_buttons();

//IOL Recommendations
$ar_admn_iol_recomd = $sxObj->sps_get_iol_master_recomds();
$ar_admn_ecp = $sxObj->sps_getECP();

//Pt. Data
$pt_data = $sxObj->get_patient_details();


//PDF IOL Data
$pdf_html = '';	
	// Following Values can be used in $pdf_print_type
		# only border = onlyBdr;
		# full border = fullBdr;

		$pdf_print_type = 'onlyBdr'; 

		if($pdf_print_type == 'onlyBdr'){
			$pdf_border_class = '';
		}else if($pdf_print_type == 'fullBdr'){
			$pdf_border_class = 'pd bdrBtmRght';
		}

		$pdf_html .= '<table id="ptinfo" style="width:100%;font-size:12px;border-collapse:collapse;border:1px solid #C0C0C0">
			<tr>
				<td colspan="4" class="bdrbtm" style="width:100%;height:5px"></td>
			</tr>
			<tr>
				<td class="tb_dataHeader pd bgcolor" style="width:29%;vertical-align:baseline"><strong>Patient Name:</strong>  '.$pt_data['pt_name'].'</td>
				<td class="tb_dataHeader pd bgcolor" style="width:26%;vertical-align:baseline"><strong>DOB:</strong> '.$pt_data['pt_dob'].'</td>
				<td class="tb_dataHeader pd bgcolor" style="width:25%;vertical-align:baseline"><strong>Account:</strong> '.$sxObj->patient_id.'</td>
				<td class="tb_dataHeader pd bgcolor" style="width:20%;vertical-align:baseline"><strong>Age:</strong> '.$pt_data['pt_age'].'</td>
			</tr>
		</table>';
		
		$pdf_html .= '<table id="tblsx" style="width:100%;font-size:12px;border-collapse:collapse;border:1px solid #C0C0C0">
						<tr>
							<td class="bdrlft pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>Sx:</strong> '.$el_sx_type.'</td>
							<td class="pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>Allergies:</strong> '.$ptAllergies.'</td>
							<td class="pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>Diabetic:</strong> '.$ptDiabetic.'</td>	
							<td class="pd '.$pdf_border_class.' bdrRght" style="width:25%;vertical-align:baseline"><strong>Flomax:</strong> '.$pt_flomax.'</td>
						</tr>
						<tr>
							<td class="bdrlft pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>PCP:</strong> '.$ptPcp.'</td>
							<td class="pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>Referring:</strong> '.$ptRefer.'</td>
							<td colspan="2" class="pd '.$pdf_border_class.' bdrRght" style="width:50%;vertical-align:baseline"><strong>Co Managed:</strong> '.$ptCoManage.'</td>
						</tr>
						
						</table>';
						
		if($el_k_given != ''){
			$pdf_el_k_given = '<tr>
					<td colspan="4" style="width:100%;" class="tb_dataHeader bgcolor pd">'.$el_k_given."'s".'</td>
				</tr>';
		}else{
			$pdf_el_k_given = '';
		}
		
		$patient_choice_title_pdf = '';
			if(count($ar_admn_pt_choices)>0){
				foreach($ar_admn_pt_choices as $key => $val){
					if(!empty($val)){
						$sel = ($el_pt_choice == $val) ? "SELECTED" : "";
						if($el_pt_choice == $val)
						{
							$patient_choice_title_pdf = $val;
						}
					}
				}
			}	
		$ocular_title_pdf = '';
		if(count($ar_pt_med[6])>0){
			foreach($ar_pt_med[6] as $k=>$v){
				$title = $v[1];
				$id = $v[0];
				if($el_prev_sx_ocu == $id)
				{
					$ocular_title_pdf = $title;
				}
				if(empty($ocular_title_pdf) == false) break;
			}
		}	
		
		$systemetic_title_pdf = '';
		if(count($ar_pt_med[5])>0){
			foreach($ar_pt_med[5] as $k=>$v){
				$title = $v[1];
				$id = $v[0];
				if($el_prev_sx_sys == $id)
				{
					$systemetic_title_pdf = $title;
				}
			}
		}
			
		
		$pdf_html .= '
		<table id="ptchoice" style="width:100%;border-collapse:collapse;border:1px solid #C0C0C0;font-size:12px">
			<tr>
				<td colspan="3" class="bdrlft pd '.$pdf_border_class.' bdrrght" style="width:100%;vertical-align:baseline"><strong>Patient Choices:</strong> '.$patient_choice_title_pdf.'</td>
			</tr>
			<tr>			
				<td class="pd '.$pdf_border_class.' bdrlft" style="width:33%;vertical-align:baseline" ><strong>Previous Sx/Procedures</strong></td>
				<td class="pd '.$pdf_border_class.'" style="width:33%;vertical-align:baseline"><strong>Ocular:</strong> '.$ocular_title_pdf.'</td>
				<td class="pd '.$pdf_border_class.' bdrrght" style="width:33%;vertical-align:baseline"><strong>Systemic:</strong> '.$systemetic_title_pdf.'</td>
			</tr>'.$pdf_el_k_given.'
			
		</table>';	

		$pdf_html .='<table id="tbl_ks" border="0" style="width:100%;font-size:12px;border-collapse:collapse">
						<tr>
							<td class="bdrlft pd '.$pdf_border_class.'" style="width:49%;vertical-align:baseline;">
								<table style="width:100%;border-collapse:collapse;font-size:12px">
									<tr>
										<td style="50%;vertical-align:baseline"><strong>Eye:</strong>'.$el_mank_eye.'</td>
										<td style="50%;vertical-align:baseline"><strong>Ref:</strong>'.$el_mank_ref.'</td>
									</tr>
								</table>
							</td>
							<td class="pd '.$pdf_border_class.' bdrRght" style="width:51%;padding-left:0;vertical-align:baseline">
								<table style="width:100%;border-collapse:collapse;font-size:12px">
									<tr>
										<td style="50%"></td>
										<td style="50%;vertical-align:baseline"><strong>Dominant Eye:</strong>'.$el_domi.'</td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>
							<td class="bdrlft pd '.$pdf_border_class.'" style="width:49%;vertical-align:baseline">
								<table style="width:100%;border-collapse:collapse;font-size:12px">
									<tr>
										<td style="width:100%;vertical-align:baseline"><strong>Refraction:</strong></td>
									</tr>
									<tr>
										<td style="width:100%;vertical-align:baseline">'.$el_refraction.'</td>
									</tr>
								</table>
							</td>
							<td class="pd '.$pdf_border_class.' bdrRght" style="width:51%;padding-left:2px;vertical-align:baseline">
								<table style="width:100%;border-collapse:collapse;font-size:12px">
									<tr>
										<td class="p15" style="width:100%;vertical-align:baseline"><strong>Other Eye Refraction:</strong></td>
									</tr>
									<tr>
										<td style="width:100%;vertical-align:baseline">'.$el_othr_eye_ref.'</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="bdrlft pd '.$pdf_border_class.' bdrRght" colspan="2" style="width:100%;vertical-align:baseline"><strong>Recommendations: </strong>'.$el_iol_recomd_pdf.'</td>
						</tr>
						<tr>
							<td class="bdrlft pd" colspan="2" style="width:100%;vertical-align:baseline"><strong>Comments: </strong>'.$el_iol_desc.'</td>
						</tr>
						<tr>
							<td class="bdrlft pd bdrBtmRght" colspan="2" style="width:100%;vertical-align:baseline"><strong>Lens - as of SLE Summary: </strong>'.$el_lens_sle_summary.'</td>
						</tr>
					</table>';	


		if($el_date_surgery != '' && $el_date_surgery != '00-00-0000'){$pdf_el_date_surgery = $el_date_surgery;}else{$pdf_el_date_surgery = '';}
		
		$phyArray = $sxObj->getMrPersonnal(2,"cn2");
		foreach($phyArray as $pId => $physicianNameOS){
			if($el_surgeon_id == $pId){
				$pdf_selected_surgeon = $physicianNameOS;
			}
		}
		
		$pdf_html .='<table style="width:100%;border-collapse:collapse;font-size:12px">
						<tr>
							<td style="width:33%;border-right:1px solid #fff;vertical-align:baseline" class="tb_dataHeader bgcolor pd">Surgery</td>
							<td style="width:33.5%;border-right:1px solid #fff;vertical-align:baseline" class="tb_dataHeader bgcolor pd">Final K\'s</td>
							<td style="width:33.5%;vertical-align:baseline" class="tb_dataHeader bgcolor pd">Other Eye K\'s</td>
						</tr>
						<tr>
							<td class="bdrlft pd bdrBtmRght" style="width:33%;vertical-align:baseline"> 
								<table style="width:100%;border-collapse:collapse;font-size:12px">
									<tr>
										<td style="width:50%"><strong>Date : </strong>'.$pdf_el_date_surgery.'</td>
										<td style="width:50%"><strong>Time : </strong>'.trim($el_time_surgery).'</td>
									</tr>
								</table>
							</td>
							
							<td class="pd bdrBtmRght" style="width:33.5%;vertical-align:baseline"> 
								<table style="width:100%;font-size:12px">
									<tr>
										<td style="width:25%"><strong>Flat</strong></td>
										<td style="width:25%"><strong>Steep</strong></td>
										<td style="width:25%"><strong>Axis</strong></td>
										<td style="width:25%"><strong>Cyl</strong></td>
									</tr>
									<tr>
										<td style="width:25%">'.trim($el_k_flat).'</td>
										<td style="width:25%">'.trim($el_k_steep).'</td>
										<td style="width:25%">'.trim($el_k_axis).'</td>
										<td style="width:25%">'.trim($el_k_cyl).'</td>
									</tr>
								</table>
							</td>
							
							<td class="pd bdrBtmRght" style="width:33.5%;vertical-align:baseline"> 
								<table style="width:100%;font-size:12px">
									<tr>
										<td style="width:25%"><strong>Flat</strong></td>
										<td style="width:25%"><strong>Steep</strong></td>
										<td style="width:25%"><strong>Axis</strong></td>
										<td style="width:25%"><strong>Cyl</strong></td>
									</tr>
									<tr>
										<td style="width:25%">'.trim($el_ok_flat).'</td>
										<td style="width:25%">'.trim($el_ok_steep).'</td>
										<td style="width:25%">'.trim($el_ok_axis).'</td>
										<td style="width:25%">'.trim($el_ok_cyl).'</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<table style="width:100%;border-collapse:collapse;font-size:12px">
						<tr>
							<td style="width:50%;border-right:1px solid #fff;vertical-align:baseline" class="tb_dataHeader pd bgcolor">Surgeon - '.$pdf_selected_surgeon.'</td>
							<td style="width:50%;margin-left:1px;vertical-align:baseline" class="tb_dataHeader pd bgcolor">Procedure</td>
						</tr>
						<tr>
							<td rowspan="2" style="width:50%;vertical-align:baseline" class="pd bdrlft '.$pdf_border_class.'"></td>
							<td style="width:50%;vertical-align:baseline" class="pd '.$pdf_border_class.' bdrRght">'.$el_proc_prim.'</td>
						</tr>
						<tr>
							<td style="width:50%;vertical-align:baseline" class="pd '.$pdf_border_class.' bdrRght">'.$el_proc_sec.'</td>
						</tr>
					</table>';

		$pdf_html .= '
					<!-- <table style="width:100%;font-size:12px;border-collapse:collapse">
						<tr>
							<td class="bdrlft pt5" style="width:15%;vertical-align:baseline"> <strong> Previous Eye: </strong></td>
							<td class="pt5" style="width:3%;vertical-align:baseline">'.$el_prev_eye_site.'</td>
							<td class="pt5" style="width:15%;vertical-align:baseline"><strong>Date: </strong>'.$el_prev_eye_date.'</td>
							<td class="pt5" style="width:15%;vertical-align:baseline"><strong>Lens: </strong>'.$el_prev_eye_lens.'</td>
							<td class="pt5" style="width:13%;vertical-align:baseline"><strong>Power: </strong>'.$el_prev_eye_power.'</td>
							<td class="pt5" style="width:10%;vertical-align:baseline"><strong>Cyl: </strong>'.$el_prev_eye_cyl.'</td>
							<td class="pt5" style="width:15%;vertical-align:baseline"><strong>Axis : </strong>'.$el_prev_eye_axis.'</td>
							
						</tr>
					</table> -->
					<table style="width:100%;border-collapse:collapse;font-size:12px">
						<tr>
							<td style="width:100%;margin-left:1px;vertical-align:baseline" class="tb_dataHeader pd bgcolor">Planning</td>
						</tr>
					</table>	
					<table style="width:100%;font-size:12px;border-collapse:collapse">		
						<tr>
							<td class="bdrlft  pd" style="width:14%;vertical-align:baseline"><strong>VA  : </strong>'.$el_prev_eye_va.'</td>
							<td class=" pd" style="width:15%;vertical-align:baseline"></td>
							<td class="pd" style="width:18%;vertical-align:baseline"><strong>ORA Results:</strong>'.$el_prev_eye_ora_res.'</td>
							<td class="pd" style="width:18%;vertical-align:baseline"><strong>Toric Position:</strong>'.$el_prev_eye_torpos.'</td>
							<td class="pd" style="width:15%;vertical-align:baseline">'.$el_prev_eye_comm.'</td>
							<td class="pd bdrrght" style="width:20%;vertical-align:baseline"><strong>Method-Lens SX: </strong>'.$el_meth_lens.'</td>
							
						</tr>	
					</table>
					<table style="width:100%;font-size:12px;border-collapse:collapse">		
						<tr>
							<td class="bdrlft pd" style="width:14%;vertical-align:baseline"><strong>ORA: </strong>'.$el_ora.'</td>
							<td class="bdrlft pd" style="width:15%;vertical-align:baseline"></td>
							<td class="pd" style="width:18%;vertical-align:baseline"><strong>Version :</strong>'.$el_version.'</td>
							<td class="pd" style="width:18%;vertical-align:baseline"><strong>MBN :</strong>'.$MBN_pdf.'</td>
							<td class="pd bdrRght" style="width:35%;vertical-align:baseline"><strong>Premium Lens: </strong>'.$premium_lens_pdf.'</td>
						</tr>		
					</table>
					<table id="tbl_cci" style="width:100%;border-collapse:collapse;font-size:12px">
						<tr>
							<td class="bdrlft pd bdrbtm" style="width:20%;vertical-align:baseline"><strong>CCI:</strong>'.$el_cci.'</td>
							<td class="pd bdrbtm" style="width:20%;vertical-align:baseline"><strong>Pachymetry</strong>'.$el_pachy.'</td>
							<td class="pd bdrbtm" style="width:20%;vertical-align:baseline"><strong>White to White</strong>'.$el_w2w.'</td>
							<td class="pd bdrbtm" style="width:20%;vertical-align:baseline"><strong>Pupil Max</strong>'.$el_pupilmx.'</td>
							<td class="pd bdrBtmRght" style="width:20%;vertical-align:baseline"><strong>Cap Max</strong>'.$el_cupmx.'</td>
						</tr>
					</table>';			

		//PDF Code
		$pdf_predict_sel = '';
		if(strpos($el_predict_sel, "Barret")!==false){
			$pdf_predict_sel .= "<strong>Barret</strong>,";
		}

		if(strpos($el_predict_sel, "SRK-T /HQ")!==false){
			$pdf_predict_sel .= "<strong>SRK-T /HQ</strong>,";
		}

		if(strpos($el_predict_sel, "Holiday- I / II")!==false){
			$pdf_predict_sel .= "<strong>Holiday- I / II</strong>";
		}
		
		$pdf_arra_lens = '';
		//Surgeon IOL Lenses
		$selProvLens = $sxObj->getIOLLens($el_surgeon_id, true);
		
		//if(is_array($selProvLens) && count($selProvLens) > 0){
			//Get Lens Values
			$lensArr = $sxObj->getLensType($el_surgeon_id, $id_chart_sx_plan_sheet, $selProvLens);
		//}
		
		if(count($lensArr) > 0){
			foreach($lensArr as $obj){
				$fieldStr = '';
				if(is_array($obj) && count($obj) > 0 && isset($obj['ID'])){
					//For PDF
					if(isset($obj['Used']) && empty($obj['Used']) == false){
						$pdf_used_val = 'Yes';
						$pdf_used_bg = ' hylight';
					}else{
						$pdf_used_val = '';
						$pdf_used_bg = '';
					}
					
					if(isset($obj['ID'])) unset($obj['ID']);
					
					$counter = 1;
					$sizeArr = count($obj);
					foreach($obj as $key => &$val){
						$align = 'center';
						$brdrlst = 'bdrbtm bdrRght';
						//If want to show border on every side of the column use this code
						$pdf_arr_lens_classname = $pdf_border_class;
						
						//PDF Borders
						$brdrFirst = ($counter == 1) ? 'bdrlft  bdrbtm bdrRght' : '';
						//$brdrlst = ($counter == $sizeArr) ? 'bdrbtm bdrRght' : '';
						
						if($key == 'Type'){
							$val = $sxObj->iol_lenses[$val]['lensType'];
							$align = 'left';
						}
						
						if($key == 'Used'){
							if(empty($val) == false && $val == 1) $val = 'Yes';
							else $val = '';
						}
						
						$fieldStr .= '<td class=" '.$pdf_arr_lens_classname.' '.$brdrFirst.' '.$brdrlst.' pd" style="width:11%;text-align:'.$align.'">'.$val.'</td>';
						$counter++;
					}
				}
				if(empty($fieldStr) == false) $pdf_arra_lens .= '<tr>'.$fieldStr.'</tr>';
			}
		}
		
		$secWidth = '703px';
		if(empty($pdf_arra_lens)){
			$secWidth = '717px';
			$pdf_arra_lens = '<tr><td colspan="9" class="pd bdrRght bdrlft bdrbtm" style="width:100%;text-align:center;font-size:12px">No Lens Found</td></tr>';
		}
		$pdf_html .= '
			<table id="tbl_lens" style="width:'.$secWidth.';border-collapse:collapse;font-size:12px">
				<tr>
					<td colspan="5" style="width:50%;text-align:left;font-size:11px;border-right:1px solid #fff" class="tb_dataHeader bgcolor pd">Lens - PrePlan Lens (Traditional IOL) differs from Primary Lens</td>
					<td colspan="4" style="width:50%;text-align:left;border-right:1px solid #fff" class="tb_dataHeader bgcolor pd bdrRght">Predicted - '.$pdf_predict_sel.'</td>
				</tr>
				
				<tr>
					<td class="'.$pdf_border_class.' bdrlft  bdrbtm bdrRght" style="width:10%;text-align:center;padding-left:0px"><strong>Lens</strong></td>
					<td class="'.$pdf_border_class.'  bdrbtm bdrRght" style="width:10%;text-align:center;padding-left:0px"><strong>Power</strong></td>
					<td class="'.$pdf_border_class.'  bdrbtm bdrRght" style="width:10%;text-align:center;padding-left:0px"><strong>Cyl</strong></td>
					<td class="'.$pdf_border_class.'  bdrbtm bdrRght" style="width:10%;text-align:center;padding-left:0px"><strong>Axis</strong></td>
					<td class="'.$pdf_border_class.'  bdrbtm bdrRght" style="width:10%;text-align:center;padding-left:0px"><strong>Used</strong></td>
					
					
					<td style="width:10%;text-align:center;" class="'.$pdf_border_class.'  bdrbtm bdrRght"><strong>Target</strong></td>
					<td style="width:10%;text-align:center;" class="'.$pdf_border_class.'  bdrbtm bdrRght"><strong>Predicted</strong></td>
					<td style="width:10%;text-align:center;" class="'.$pdf_border_class.'  bdrbtm bdrRght"><strong>ACD/AL(%)</strong></td>
					<td style="width:10%;text-align:center" class="'.$pdf_border_class.'  bdrbtm bdrRght"><strong>S/P CRS</strong></td>
				</tr>'.$pdf_arra_lens.'	
			</table>';
			
		$pdf_arr_asti_as = '';
		foreach($sxObj->arr_asti_as as $k => $asti_source){
			if(!empty($id_chart_sx_plan_sheet)){
				$sql = "SELECT * FROM chart_sps_ast_assess where id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."'  AND ast_source='".imw_real_escape_string($asti_source)."' ";
				$row =  sqlQuery($sql);
				if($row!=false){
					$$magni = $row["magni_diopter"];
					$$magni_used = $row["magni_used"];
					$$axis = $row["axis"];
					$$axis_used = $row["axis_used"];
				}
			}
			
			//PDF Code
			$pdf_arr_asti_as_classname = $pdf_border_class;
			
			$pdf_arr_asti_as .= '
				<tr>
					<td class="bdrlft pd '.$pdf_arr_asti_as_classname.'" style="width:20%;vertical-align:baseline">'.$asti_source.'</td>
					<td class="pd '.$pdf_arr_asti_as_classname.'" style="width:20%;vertical-align:baseline">'.$$magni.'</td>';
					if($asti_source=="Coma Max (u)" || $asti_source=="CCT (u)" || $asti_source=="OCTM FT (u)"){
						if($asti_source=="Coma Max (u)"){
							$pdf_arr_asti_as .= '<td class="pd '.$pdf_arr_asti_as_classname.' bdrRght" colspan="3" rowspan="3" style="width:60%;border-top:1px solid #C0C0C0;vertical-align:baseline">'.$el_asti_com.'</td>';
						}
					}else{
					if(!empty($$magni_used))
					{
						$pdf_magni_used = 'Yes';
					}else{
						$pdf_magni_used = '';
					}
					
					if(!empty($$axis_used))
					{
						$pdf_axis_used = 'Yes';
					}else{
						$pdf_axis_used= '';
					}
					
					
					$pdf_arr_asti_as .='<td class="pd '.$pdf_arr_asti_as_classname.'" style="width:20%;vertical-align:baseline">'.$pdf_magni_used.'</td>
					<td class="pd '.$pdf_arr_asti_as_classname.'" style="width:20%;vertical-align:baseline">'.$$axis.'</td>
					<td class="pd '.$pdf_arr_asti_as_classname.' bdrRght" style="width:20%;vertical-align:baseline">'.$pdf_axis_used.'</td>';
				
			} 
			$pdf_arr_asti_as .= '</tr>'; 	
		}	
		
		
		//Surgeon IOL Model Values
		$pdf_chart_sx_plan_sheet = '';
		$valueArr = $sxObj->getIolModelValues($id_chart_sx_plan_sheet, $el_surgeon_id);
		
		
		foreach($valueArr as $obj){
			$iolCounter = 0;
			$borderClass = 'bdrbtm bdrRght';
			if($iolCounter == 0) $borderClass = 'bdrlft  bdrbtm bdrRght';
			
			$fieldName = $sxObj->arr_lens[$obj['Index']];
			$lensName = $sxObj->iol_lenses[$obj['Type']]['lensType'];
			
			$iolToric = $obj['Model'];
			$iolPower = $obj['Power'];
			$iolAxis = $obj['Axis'];
			
			if(empty($fieldName) == false && empty($lensName) == false){
				$pdf_chart_sx_plan_sheet .= '
					<tr>
						<td style="font-size:12px;width:25%"  class="'.$borderClass.' '.$pdf_border_class.'"><strong>'.$fieldName.'</strong> - '.$lensName.'</td>
						<td style="font-size:12px;width:25%;text-align:center"  class="'.$borderClass.' '.$pdf_border_class.' pd">'.$iolPower.'</td>
						<td style="font-size:12px;width:25%;text-align:center"  class="'.$borderClass.' '.$pdf_border_class.'">'.$iolToric.'</td>
						<td style="font-size:12px;width:25%;text-align:center"  class="'.$borderClass.' '.$pdf_border_class.' pd">'.$iolAxis.'</td>
					</tr>';
			}
			$iolCounter++;
		}
		
		//PDF Code
		//First table is the Astigmatism Assessment Section and second table is Astigmatism Plan Section in PDF
		if(!empty($el_plan_anterior))
		{
			$pdf_plan_anterior = 'Yes';
		}else{
			$pdf_plan_anterior = '';
		}
		
		if(!empty($el_plan_insratromal))
		{
			$pdf_plan_insratromal = 'Yes';
		}else{
			$pdf_plan_insratromal = '';
		}
		
		$pdf_html .='
			<table id="con_tbl_asti_plan" style="width:100%;border-collapse:collapse;font-size:12px">
				<tr>
					<td colspan="4" style="width:100%;vertical-align:baseline" class="tb_dataHeader pd bgcolor">IOL Lens Type</td>
				</tr>
				<tr>
					<td class="bdrlft  bdrbtm bdrRght '.$pdf_border_class.' " style="width:25%;vertical-align:baseline;text-align:center"><strong>Type</strong></td>
					<td class=" bdrbtm bdrRght '.$pdf_border_class.'" style="width:25%;vertical-align:baseline;text-align:center"><strong>Power</strong></td>
					<td class=" bdrbtm bdrRght '.$pdf_border_class.'" style="width:25%;vertical-align:baseline;text-align:center"><strong>Cyl</strong></td>
					<td class=" bdrbtm bdrRght '.$pdf_border_class.' bdrRght" style="width:25%;vertical-align:baseline;text-align:center"><strong>Axis</strong></td>
				</tr>'.$pdf_chart_sx_plan_sheet.'
			</table>
			
			<table id="Astigmatism Assessment" style="width:100%;border-collapse:collapse;font-size:12px">
				<tr>
					<td colspan="5" style="width:100%;vertical-align:baseline" class="tb_dataHeader pd bgcolor">Astigmatism Assessment</td>
				</tr>
				<tr>
					<td class="bdrlft pd '.$pdf_border_class.'" style="width:20%;vertical-align:baseline"><strong>Astigmatism Source</strong></td>
					<td class="pd '.$pdf_border_class.'" style="width:20%;vertical-align:baseline"><strong>Magnitude (Diopters)</strong></td>
					<td class="pd '.$pdf_border_class.'" style="width:20%;vertical-align:baseline"><strong>Magnitude Used</strong></td>
					<td class="pd '.$pdf_border_class.'" style="width:20%;vertical-align:baseline"><strong>Axis (Degrees)</strong></td>
					<td class="pd '.$pdf_border_class.' bdrRght" style="width:20%;vertical-align:baseline"><strong>Axis Used</strong></td>
				</tr>'.$pdf_arr_asti_as.'
			</table>
			
			<table id="con_tbl_asti_plan" style="width:100%;border-collapse:collapse;font-size:12px">
				<tr>
					<td colspan="4" style="width:100%;vertical-align:baseline" class="tb_dataHeader pd bgcolor">Astigmatism Plan</td>
				</tr>
				<tr>
					<td class="bdrlft pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>Femto</strong></td>
					<td class="'.$pdf_border_class.' bdrRght pd" colspan="3" style="width:75%;vertical-align:baseline">'.$el_plan_femto.'</td>
				</tr>
				<tr>
					<td class="bdrlft pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>AK#</strong></td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_ak.'</td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"></td>
					<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline"></td>
				</tr>
				<tr>
					<td class="bdrlft '.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>AK# 1 Length</strong></td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_ak1_len.'</td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>AK# 2 Length</strong></td>
					<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline">'.$el_plan_ak2_len.'</td>
				</tr>
				<tr>
					<td class="bdrlft '.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>AK# 1 Axis(&deg;)</strong></td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_ak1_axis.'</td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Arc 2 Angle(&deg;)</strong></td>
					<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline">'.$el_plan_arc2_axis.'</td>
				</tr>
				<tr>
					<td class="bdrlft '.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>AK# 1 Depth(%)</strong></td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_ak1_depth.'</td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Arc 2 Depth(%)</strong></td>
					<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline">'.$el_plan_ak2_depth.'</td>
				</tr>
				<tr>
					<td class="bdrlft '.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Optical Zone</strong></td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_opt_zone.'</td>
					<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Anterior </strong> '.$pdf_plan_anterior.'</td>
					<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline"><strong>Intrastromal </strong> '.$pdf_plan_insratromal.'</td>
				</tr>
			</table>
		';
		
		$pdf_ecp = '';
		if(count($ar_admn_ecp)>0){
			foreach($ar_admn_ecp as $key => $val){
				if(!empty($val)){
					if($el_ecp == $val)
					{
						$pdf_ecp = $val;
					}
				}
			}
		}
		
		$pdf_html .='
				<table id="tbl_sx_plan" style="width:100%;font-size:12px;border-collapse:collapse">
					<tr>
						<td class="bdrlft '.$pdf_border_class.'" style="width:23%;vertical-align:baseline"><strong>Sx Planning - Hooks:</strong> '.$el_sx_pln_hook.'</td>
						<td class="'.$pdf_border_class.'" style="width:21%;vertical-align:baseline"><strong>Flomax Cocktail:</strong> '.$el_flomx_cocktail.'</td>
						<td class="'.$pdf_border_class.'" style="width:18%;vertical-align:baseline"><strong>Trypan Blue:</strong> '.$el_trypan_blue.'</td>
						<td class="'.$pdf_border_class.'" style="width:12%;vertical-align:baseline"><strong>LRI:</strong> '.$el_lri.'</td>
						<td class="'.$pdf_border_class.'" style="width:14%;vertical-align:baseline"><strong>FEMTO:</strong> '.$el_femto.'</td>
						<td class="'.$pdf_border_class.' bdrRght" style="width:12%;vertical-align:baseline"><strong>ECP:</strong> '.$pdf_ecp.'</td>
					</tr>
					<tr>
						<td class="bdrlft pd bdrBtmRght" colspan="6" style="width:100%;vertical-align:baseline"><strong>Comments:</strong> '.$el_sx_pln_com.'</td>
					</tr>
				</table>';
		
		//Previous Test Details
		$iolTest = $sxObj->getTestDetails('iol_master', $el_ids_iol);
		$ascanTest = $sxObj->getTestDetails('ascan', $el_ids_ascan);
		$octTest = $sxObj->getTestDetails('oct', $el_ids_oct);
		$topoTest = $sxObj->getTestDetails('topogrphy', $el_ids_topo);
		$vfTest = $sxObj->getTestDetails('vf', $el_ids_vf);
		
		$pdf_html .='	
				<table id="dv_drop_down_tests" style="width:100%;border-collapse:collapse;font-size:12px">
					<tr>
						<td style="width:100%" colspan="5" class="tb_dataHeader pd bgcolor"><strong>All Previous Tests</strong></td>
					</tr>
					
					<tr>
						<td class="bdrlft '.$pdf_border_class.' " style="width:20%;vertical-align:baseline">'.$iolTest['Date'].'</td>
						<td class="'.$pdf_border_class.' " style="width:20%;vertical-align:baseline">'.$ascanTest['Date'].'</td>
						<td class="'.$pdf_border_class.' " style="width:20%;vertical-align:baseline">'.$octTest['Date'].'</td>
						<td class="'.$pdf_border_class.' " style="width:20%;vertical-align:baseline">'.$topoTest['Date'].'</td>
						<td class="'.$pdf_border_class.' bdrRght" style="width:20%;vertical-align:baseline">'.$vfTest['Date'].'</td>
					</tr>
					
					<tr>
						<td class="bdrlft '.$pdf_border_class.' bdrbtm" style="width:20%;vertical-align:baseline"><strong> IOL Master</strong> </td>
						<td class="'.$pdf_border_class.' bdrbtm" style="width:20%;vertical-align:baseline"><strong> A-scan</strong></td>
						<td class="'.$pdf_border_class.' bdrbtm" style="width:20%;vertical-align:baseline"><strong>OCT</strong></td>
						<td class="'.$pdf_border_class.' bdrbtm" style="width:20%;vertical-align:baseline"><strong>Topography</strong></td>
						<td class="'.$pdf_border_class.' bdrBtmRght" style="width:20%;vertical-align:baseline"><strong>VF</strong></td>
					</tr>	
				</table>
		';
//echo $pdf_html;die;
$oPatient = new Patient($pid);
$val="";
			//---get Detail For Patient -------
			$qry = imw_query("select * from patient_data where id = '".$pid."'");
			$patientDetails = imw_fetch_assoc($qry);
			$patientName = $patientDetails['lname'].', '.$patientDetails['fname'].' ';
			$patientName .= $patientDetails['mname'];

			$date = substr($patientDetails['date'],0,strpos($patientDetails['date'],' '));
			$created_date = get_date_format($date);
			$date_of_birth = get_date_format($patientDetails['DOB']);
			$cityAddress = $patientDetails['city'];
			if($patientDetails['state'])
				$cityAddress .= ', '.$patientDetails['state'].' ';
			else
				$cityAddress .= ' ';
			$cityAddress .= $patientDetails['postal_code'];
			list($y,$m,$d) = explode('-',$patientDetails['DOB']);
			$age = $oPatient->getAge();//date('Y') - $y ;
			
			//--- Get Physician Details --------
			if((int)$patientDetails['providerID'] > 0){
				$phyId = $patientDetails['providerID'];
			}
			else{
				$appointmentQryRes = getPatientLastAppointment($pid);
				$phyId = $appointmentQryRes[0]['sa_doctor_id'];
			}
			if($phyId){
				$qry = imw_query("select concat(fname,', ',lname) as name, mname from users
						where id = '$phyId'");
				$phyDetails = imw_fetch_assoc($qry);
				$phyName = trim($phyDetails['name'].' '.$phyDetails['mname']);
			}
			
			//--- Get Reffering Physician Details --------
			$primary_care_phy_name=$patientDetails['primary_care_phy_name'];
			$reffPhyId = $patientDetails['primary_care_id'];
			$qry = imw_query("select concat(FirstName,', ',LastName) as name, MiddleName from refferphysician
					where physician_Reffer_id = '$reffPhyId'");
			$refPhyDetails = imw_fetch_assoc($qry);
			$reffPhyName = trim($refPhyDetails['name'].' '.$refPhyDetails['MiddleName']);
			//if(!$reffPhyName) $reffPhyName = $phyName;
			//---- Get Patient Facility Details -------
			$default_facility = $patientDetails['default_facility'];
			$qry = imw_query("select facilityPracCode from pos_facilityies_tbl 
					where pos_facility_id = '$default_facility'");
			$facilityRes = imw_fetch_assoc($qry);
			
			//--- Get Detail How create patient -------
			$created_by = $patientDetails['created_by'];
			$qry = imw_query("select fname, lname, mname from users
					where id = '$created_by'");
			$createByDetail = imw_fetch_assoc($qry);
			$createByName = core_name_format($createByDetail['lname'],$createByDetail['fname'],$createByDetail['mname'],'');
			
			$qry = imw_query("select * from resp_party where patient_id = '$pid'
					and fname != '' and lname != ''");
			$res_party_detail = imw_fetch_array($qry);
			//--- Get Patient Occupation Details ------
			$qry = imw_query("select * from employer_data where pid = '$pid' and name != ''");
			$emp_details = imw_fetch_assoc($qry);
			
			
			//--- Get Default Facility Details -------
			$qry = imw_query("select default_group from facility where facility_type = 1");
			$facilityDetail = imw_fetch_assoc($qry);
			if(count($facilityDetail)>0){
				$gro_id = $facilityDetail['default_group'];
				$qry = imw_query("select * from groups_new where gro_id = '$gro_id'");
				$groupDetails = imw_fetch_assoc($qry);
			}



//CSS Part Of PDF
$val ='<style>
.text_b_w{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
}
.paddingLeft{
	padding-left:5px;
}
.paddingTop{
	padding-top:5px;
}
.tb_subheading{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#f3f3f3;;
}
.tb_heading{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#999999;
	margin-top:10px;
}
.tb_headingHeader{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684ab;
}
.tb_dataHeader{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000;
	background-color:#9a9a9a;
}
.text_lable{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#FFFFFF;
		font-weight:bold;
}
.text_value{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:100;
		background-color:#FFFFFF;
	}
.text_blue{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		color:#0000CC;
	font-weight:bold;
	}
.text_green{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		color:#006600;
		font-weight:bold;
}
.hylight{background-color:lightblue; }

table{
			font-size:14px;
		}
		.fheader{
			padding:5px 0px 5px 0px;
			font-weight:bold;
			font-size:16px;
			text-decoration:underline;
			text-align:center;
		}
		.bold{
			font-weight:bold;
		}
		.pt5{
			padding-top:5px;	
		}
		.pd{
			padding:4px;		
		}
		.pl5{
			padding-left:5px;
		}
		.bgcolor{
			background:#C0C0C0;
		}
		.cbold{
			text-align:center;
			font-weight:bold;		
		}
		.bdrbtm{
			border-bottom:1px solid #C0C0C0;
			height:20px;	
			vertical-align:baseline;
		}
		.bdrtop{
			border-top:1px solid #C0C0C0;
			//height:10px;	
		}
		.bdrrght{
			border-right:1px solid #C0C0C0;
			//height:20px;
			vertical-align:baseline;
		}
		
		.bdrlft{
			border-left:1px solid #C0C0C0;
			//height:20px;
			vertical-align:baseline;
		}
		.bdrbtm_new{
			border-bottom:1px solid #C0C0C0;
			vertical-align:baseline;
		}
		.bdrrght_new{
			border-right:1px solid #C0C0C0;
			vertical-align:baseline;
		}
		.bdrBtmRght{
			border-bottom:1px solid #C0C0C0;
			border-right:1px solid #C0C0C0;
			vertical-align:baseline;
		}
		.tb_headingHeader{
			font-weight:bold;
			color:#FFFFFF;
			background-color:#4684ab;
		}

.imgCon{width:325px;height:auto;}
</style>';

 if(!empty($patientName))
 { 
 	$patient_heading = $patientDetails['title'].' '.$patientName."-".$patientDetails['id']; 
 }
 
 $about_patient = $patientDetails['sex'].'&nbsp;('.$age.')'.'&nbsp;'.$date_of_birth;
 
 $patient_address = core_address_format(' ', ' ', $patientDetails['city'], $patientDetails['state'], $patientDetails['postal_code']);
// Main Content
$val .='<page backtop="5mm" backbottom="5mm">
	<page_header>
		<table style="width:100%;border-collapse:collapse" border="0" cellspacing="0"  cellpadding="0">
				<tr>
					<td style="width:40%" class="tb_headingHeader">'.$patient_heading.'</td>
					<td style="width:30%" class="tb_headingHeader">'.$about_patient.'&nbsp;</td>
				    <td style="width:30%; text-align:right" class="tb_headingHeader">Date of Service:&nbsp;'.wv_formatDate($_REQUEST['new_sx_dos']).'&nbsp;</td>
				</tr>
		</table>
	</page_header>';  

$val .='<table style="width:100%;border-collapse:collapse" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="text_b_w" style="width:30%;" align="left"><strong>Sx Plan Sheet</strong></td>
				<td class="text_b_w" style="width:1%;"></td>
				<td class="text_value" style="width:69%;" align="right">Printed by:'.$opertator_name.'&nbsp;on&nbsp;'.get_date_format(date("Y-m-d"))." ".date("H:i:s").'</td>
			</tr>
			<tr>
				<td class="text_b_w" style="width:100%;" colspan="3"><hr/></td>
			</tr>
		</table>
'; 


$val .='<table style="width:100%;border-collapse:collapse" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td style="width:40%" align="left" valign="top"> 
					<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
						<tr>
							<td style="width:100%" class="text_lable">'.$patientName.'-'.$patientDetails['id'].'</td>
						</tr>';
		 	if($about_patient != ''){				
				$val .='<tr>
							<td style="width:100%" class="text_value">'.$about_patient.'&nbsp;</td>
						</tr>';
		 	}
		 
		 	if($patientDetails['street'] != ''){ 
				$val .='<tr>
							<td style="width:100%" class="text_value">'.$patientDetails['street'].'&nbsp;</td>
						</tr>';
		 	}
			if($patientDetails['street2'] != ''){		
				$val .='<tr>
							<td style="width:100%" class="text_value">'.$patientDetails['street2'].'&nbsp; </td>
						</tr>';
				}
			if($patient_address != ''){			
				$val .='<tr>
							<td style="width:100%" class="text_value">'.$patient_address.'</td>
						</tr>';
			}
			
				$val .='<tr>
							<td style="width:100%" class="text_value">Ph.: '.core_phone_format($patientDetails['phone_home']).'&nbsp; </td>
						</tr>
					</table>
			  </td>';
			$val .='<td style="width:20%"  valign="top">&nbsp;</td>';
			$val .='<td style="width:40%" align="right" valign="top">
					<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
						<tr>
							<td style="width:100%" class="text_lable">'.$groupDetails['name'].'</td>
						</tr>';
			if($groupDetails['group_Address1'] != ''){			
				$val .='<tr>
							<td style="width:100%" class="text_value">'.ucwords($groupDetails['group_Address1']).'</td>
						</tr>';
			}
			if($groupDetails['group_Address2'] != ''){	
				$val .='<tr>
							<td style="width:100%" class="text_value">'.ucwords($groupDetails['group_Address2']).'&nbsp;</td>
						</tr>';
			}
			
				$val .='<tr>
							<td style="width:100%" class="text_value">'.$groupDetails['group_City'].', '.$groupDetails['group_State'].' '.$groupDetails['group_Zip'].'</td>
						</tr>';
			if($groupDetails['group_Telephone'] != ''){			
				$val .='<tr>
							<td style="width:100%" class="text_value">Ph.:&nbsp;'.$groupDetails['group_Telephone'].'</td>
						</tr>';
			}
				$val .='<tr>
							<td style="width:100%" class="text_value">Fax:&nbsp;'.$groupDetails['group_Fax'].'</td>
						</tr>
					</table>
				</td>';  
			$val .='</tr>
		</table>'; 
		
		//Sx Plan Sheet Data	
		$val .= $pdf_html;
$val .= "</page>";

$print_file_name = "sx_plan_sheet.html";
$file_path = write_html($val,$print_file_name );

//If call is not from iAsc Link
if($ioSync === false){
?>
<form name="print_sx_plan" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST">
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="file_location" value="<?php echo $file_path; ?>">
</form>
<script type="text/javascript">
	document.print_sx_plan.submit();
</script>
<?php 
}else{
	//If call is from iAsc Link
	$htmlFlName = 'Sx_Plan_Sheet';
	
	$patientDir = "/PatientId_".$sxObj->patient_id;
	
	$pdfFileName = 'Sx_Plan_Sheet.pdf';
	$pdfFilePath = urldecode($iolLinkDir.$patientDir.'/'.$pdfFileName);

	try {
		$op = 'P';
		$op = strtoupper($op);
		$html2pdf = new Html2Pdf($op,'A4','en');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->writeHTML($val, isset($_GET['vuehtml']));
		$html2pdf->output($pdfFilePath,'F');
	} catch (Html2PdfException $e) {
		$html2pdf->clean();
		$formatter = new ExceptionFormatter($e);
		echo $formatter->getHtmlMessage();
	}
		
	/*
	$html2pdf = new HTML2PDF('P','A4','en');
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->WriteHTML($val);
	
	$html2pdf->Output($pdfFilePath, 'F');
	
	//comment Sync URL
	
	//Sync URL
	$syncUrl = $GLOBALS['php_server'].'/library/html_to_pdf/iolinkMakePdf.php';
	
	$data1 = "";
	$curNew = curl_init();
	$urlPdfFile = $syncUrl."?copyPathIolink=$pdfFilePath&pdf_name=$pdfFilePath&name=$htmlFlName";

	curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
	$data1 = curl_exec($curNew);
	curl_close($curNew);
	*/
}
?>