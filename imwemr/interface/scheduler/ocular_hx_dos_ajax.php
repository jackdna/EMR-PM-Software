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
$schedule_id = $_REQUEST['ap_id'];
$doneChartArr = $pendingChartArr = array();
$mainVal = "";
if($schedule_id) {
	$qry = "SELECT sa_patient_id,sa_app_start_date FROM schedule_appointments WHERE id = '".$schedule_id."' Limit 0,1";
	$res = imw_query($qry) or die(imw_error());
	if(imw_num_rows($res)>0) {
		$row = imw_fetch_assoc($res);
		$sa_patient_id = $row['sa_patient_id'];
		$sa_app_start_date = $row['sa_app_start_date'];

		$qryOcular = "SELECT cap.form_id, cap.surgical_ocular_hx, 
						DATE_FORMAT(cap.surgical_ocular_hx_sent_dos,'%m-%d-%Y') as ocular_sent_dos,
						DATE_FORMAT(cmt.date_of_service,'%m-%d-%Y') as chart_dos  
						FROM chart_assessment_plans cap
						INNER JOIN chart_master_table cmt ON (cmt.id=cap.form_id AND cmt.purge_status=0 AND cmt.date_of_service <='".$sa_app_start_date."')
						WHERE cap.surgical_ocular_hx='1' AND cap.patient_id = '".$sa_patient_id."' ORDER BY cmt.date_of_service DESC ";
		$resOcular = imw_query($qryOcular) or die(imw_error());
		if(imw_num_rows($resOcular)<=0) {
			$qryOcular = "SELECT cap.form_id, cap.surgical_ocular_hx, 
							DATE_FORMAT(cap.surgical_ocular_hx_sent_dos,'%m-%d-%Y') as ocular_sent_dos,
							DATE_FORMAT(cmt.date_of_service,'%m-%d-%Y') as chart_dos  
							FROM chart_assessment_plans cap
							INNER JOIN chart_master_table cmt ON (cmt.id=cap.form_id AND cmt.purge_status=0 AND cmt.date_of_service <='".$sa_app_start_date."')
							WHERE cap.patient_id = '".$sa_patient_id."' ORDER BY cmt.date_of_service DESC LIMIT 0,1";
			$resOcular = imw_query($qryOcular) or die(imw_error());
		}
		if(imw_num_rows($resOcular)>0) {
			while($rowOcular = imw_fetch_assoc($resOcular)) {
				$rowOcularArr[] = $rowOcular;
			}
			foreach($rowOcularArr as $rowOcularHx)	 {
				$form_id 			=	$rowOcularHx['form_id'];
				$chart_dos 			= 	$rowOcularHx['chart_dos'];
				$ocular_sent_dos 	= 	$rowOcularHx['ocular_sent_dos'];
				if($ocular_sent_dos=='00-00-0000') {
					$pendingChartArr[] 	= 	$form_id.'~~'.$chart_dos;
				}else {
					$doneChartArr[] 	= 	$form_id.'~~'.$chart_dos."(Already sent for DOS: ".$ocular_sent_dos.")";
				}
			}
			if(count($pendingChartArr)>0) {
				$mainVal = implode(',',$pendingChartArr);	
			}else if(count($doneChartArr)>0) {
				$mainVal = implode(',',$doneChartArr);	
			}
		}
	}
	echo $mainVal;
}
?>