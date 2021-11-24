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
include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once(dirname(__FILE__)."/../../../library/classes/class.mur_reports.php");
include_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
$objMUR			= new MUR_Reports;
$save_file 		= new SaveFile();
$action			= trim(strip_tags($_GET['action']));
$task			= trim(strip_tags($_GET['task']));
$mur_version	= trim(strip_tags($_GET['mur_version']));
$temp_dir = $save_file->upDir.'/users/UserId_'.$_SESSION['authId'].'/scorecard';
if(!is_dir($temp_dir) || !file_exists($temp_dir)){
	mkdir($temp_dir,0777,true);
}


/****CLEANING DIRECTORY IF ANY OLD CSV OR PDF THERE**/
$handle = opendir($temp_dir);
while (false != ($entry = readdir($handle))){
	if(substr($entry,-4) == '.pdf' || substr($entry,-4) == '.csv'){
		unlink($temp_dir.'/'.$entry);
	}	
}


switch($action){
	case 'get_report_html':{
		if(strlen($mur_version)==4) 		include_once(dirname('__FILE__')."/mur_".$mur_version.".php");
		else die('Report version not defined to include.');

		break;
	}
	case 'get_audit_patients':{
		$html = '';
		$comma_patients			= trim(strip_tags($_POST['comma_patients']));
		$specialcase			= trim(strip_tags($_POST['specialcase']));
		$measurename			= trim(strip_tags($_POST['mname']));
		$PtCat					= trim(strip_tags($_POST['PtCat']));
		
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
			$CSVname = str_replace(' ','_',$measurename." ".$PtCat.".csv");
			
			$file = fopen($temp_dir.'/'.$CSVname,"w");
			
			$CSVdataArr = array();
			$CSVHeader = array('Patient Name - ID','DOB','Gender');
			$html .= '<div style="overflow:auto; max-height:400px; overflow-x:hidden;">';
			$table = '
			<table class="table table-striped" style="font-size:85%;" cellspacing="0">
			<thead>
				<tr>
					';
			
			$table .= '		
					<th>Patient Name - ID</th>
					<th>D.O.B</th>
					<th>Gender</th>';
			if($specialcase=='send_toc' || $specialcase=='receive_toc'){
				$table .= '<th>D.O.S.</th>';
				array_push($CSVHeader,'DOS');
			}
			$table .= '
				</tr>
			</thead>
			<tbody>
			';
			fputcsv($file, $CSVHeader);
			
			while($rs = imw_fetch_assoc($res)){
				if($rs['DOB']=='00-00-0000') $rs['DOB'] = '';
				$csvDataLine = array($rs['fname'].' '.$rs['lname'].' - '.$rs['id'],$rs['DOB'],$rs['sex']);
				$table .= '
				<tr>
					';
				
				$table .= '
					<td class="one">'.$rs['fname'].' '.$rs['lname'].' - '.$rs['id'].'</td>
					<td class="two">'.$rs['DOB'].'</td>
					<td class="three">'.$rs['sex'].'</td>';
				
				if($specialcase=='send_toc' || $specialcase=='receive_toc'){
					$table .= '<td class="four">'.$rs['date_of_service'].'</td>';
					array_push($csvDataLine,$rs['date_of_service']);
				}
				
				$table .= '
				</tr>				
				';
				fputcsv($file, $csvDataLine);
			}
			$table .= '
			</tbody>
			</table>';
			
			$pdfStyle = '<style>
			table{table-collpase:collapse; border:0px solid #000;}
			table td, table th{border:0.5px solid #999;}
			table td.one{width:250px;}
			table td.two{width:100px;}
			table td.three{width:100px;}
			table td.four{width:100px;}
			</style><p><b>'.$PtCat.'</b>: '.$measurename.'</p>';
			
			$file_pointer = write_html($pdfStyle.$table,'muraudit.html');
			
			
			$html .= $table.'
			<div class="row">
				<div class="col-sm-4 text-right">
				<form action="../reports/downloadCSV.php" method="post">
					<input type="hidden" name="file_format" value="csv">
					<input type="hidden" name="file" value="'.$temp_dir.'/'.$CSVname.'">
					<input type="submit" value="Export CSV" class="btn btn-success" title="Download list as CSV.">
				</form>
				</div>
				<div class="col-sm-4 text-left">
				<form action="../../library/html_to_pdf/createPdf.php" method="post" target="_blank">
					<input type="hidden" name="onePage" value="false">
					<input type="hidden" name="op" value="p" >
					<input type="hidden" name="file_location" value="'.$file_pointer.'">
					<input type="submit" value="Print PDF" class="btn btn-success" title="Download list as PDF.">
				</form>
				</div>
				<div class="col-sm-4 text-center">
					<input type="button" class="btn btn-danger" value="Close" onclick="top.removeMessi();">
				</div>
			</div>
			</div>
			';
		}
		fclose($file);
		
		echo $html;
		break;	
	}
	default:{
		die('TASK= '.$action);
	}
	
}

?>