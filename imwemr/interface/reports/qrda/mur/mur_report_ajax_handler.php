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
  FILE : scheduler_new_report.php
  PURPOSE : Search criteria for scheduler report
  ACCESS TYPE : Direct
 */
include_once(dirname(__FILE__)."/../../../../config/globals.php");
include_once(dirname(__FILE__)."/../../../../library/classes/class.mur_reports.php");
$objMUR			= new MUR_Reports;
$action			= trim(strip_tags($_GET['action']));
$task			= trim(strip_tags($_GET['task']));
$mur_version	= trim(strip_tags($_GET['mur_version']));

switch($action){
	case 'get_report_html':{
		if($mur_version=='2016') 		include_once(dirname('__FILE__')."/mur_2016.php");
		else if($mur_version=='2017') 	include_once(dirname('__FILE__')."/mur_2017.php");
		else if($mur_version=='2018') 	include_once(dirname('__FILE__')."/mur_2018.php");
		break;
	}
	case 'get_audit_patients':{
		
		$html = '';
		$comma_patients			= trim(strip_tags($_POST['comma_patients']));
		$specialcase			= trim(strip_tags($_POST['specialcase']));
		
		if($specialcase==''){
			$q = "SELECT id,fname,lname,mname,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB,sex FROM patient_data WHERE id IN ($comma_patients)";
		}else if($specialcase=='send_toc'){
			$q = "SELECT pd.id,pd.fname,pd.lname,DATE_FORMAT(pd.DOB,'%m-%d-%Y') as DOB,pd.sex,DATE_FORMAT(cmt.date_of_service,'%m-%d-%Y') AS date_of_service 
						  FROM chart_master_table cmt 
						  JOIN patient_data pd ON (cmt.patient_id = pd.id) 
						  WHERE cmt.id IN ($comma_patients) 
						  ORDER BY cmt.patient_id
						 ";
		}else if($specialcase=='receive_toc'){
			$q = "SELECT pd.id,pd.fname,pd.lname,DATE_FORMAT(pd.DOB,'%m-%d-%Y') as DOB,pd.sex,DATE_FORMAT(sa.sa_app_start_date,'%m-%d-%Y') AS date_of_service 
						  FROM schedule_appointments sa 
						  JOIN patient_data pd ON (sa.sa_patient_id = pd.id) 
						  WHERE sa.id IN ($comma_patients) 
						  ORDER BY sa.sa_patient_id
						 ";
		}
		
		
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$html .= '
			<table class="table table-striped" style="font-size:85%;">
			<thead>
				<tr>
					';
			if($specialcase=='send_toc' || $specialcase=='receive_toc'){
				$html .= '<th>D.O.S.</th>';
			}
			$html .= '		
					<th>Patient Name - ID</th>
					<th>D.O.B/Gender</th>
				</tr>
			</thead>
			<tbody>
			';
			while($rs = imw_fetch_assoc($res)){
				$html .= '
				<tr>
					';
				if($specialcase=='send_toc' || $specialcase=='receive_toc'){
					$html .= '<td>'.$rs['date_of_service'].'</td>';
				}
				$html .= '
					<td>'.$rs['fname'].' '.$rs['lname'].' - '.$rs['id'].'</td>
					<td>'.$rs['DOB'].' / '.$rs['sex'].'</td>
				</tr>				
				';
			}
			$html .= '
			</tbody>
			</table>
			';
		}
		
		echo '<div style="height:300px; overflow:auto;">'.$html.'</div>';
		break;	
	}
	default:{
		die('TASK= '.$action);
	}
	
}

?>