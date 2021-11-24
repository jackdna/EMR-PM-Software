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
/*
FILE : new_account_report_result.php
PURPOSE :  SCHEDULER NEW REPORT RESULT
ACCESS TYPE : INCLUDED
*/
$page_data = NULL;
$curDate = date('Y-m-d');
if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}

if($_POST){
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	$selUser = $CLSReports->report_display_selected($users,'operator',1, $allUserCnt);

	$printFile=0;
	/*
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();
	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}	
	
		//--- CHANGE DATE FORMAT -------
	if($Start_date != '' && $End_date != ''){
		$start_date_whr = getDateFormatDB($Start_date);
		$end_date_whr = getDateFormatDB($End_date);	
	}
*/

	$sel_opr=imw_query("select id, fname, lname, mname from users ORDER BY lname, mname, fname");
	$arrAllPhysicians=array();
	while($row_opr=imw_fetch_array($sel_opr)){
		$opr_name="";
		$opr_name = ucfirst(trim($row_opr['fname'][0]));
		$opr_name .= ucfirst(trim($row_opr['lname'][0]));
		$opr_ins[$row_opr['id']]=$opr_name;
		$arrAllPhysicians[$row_opr['id']]=core_name_format($row_opr['lname'], $row_opr['fname'], $row_opr['mname']);
	}
	
	$str_users='';
	if(sizeof($users)>0){
		$str_users=implode(',', $users);
	}

	//FETCHING MAIN DATA
	$qry="Select audit_trail.Operater_Id, DATE_FORMAT(audit_trail.Date_Time, '".$date_format_SQL."') as 'logged_date' FROM audit_trail 
	WHERE audit_trail.Action IN('user_logout_s', 'user_login_s', 'user_logout_f', 'user_login_f')";
	if(empty($str_users)==false){
		$qry.=" AND audit_trail.Operater_Id IN(".$str_users.")";
	}	
	$qry.=" ORDER BY audit_trail.id";
	$rs=imw_query($qry);
	$arrMainData=array();
	
	while($res=imw_fetch_array($rs)){
		$user_id=$res['Operater_Id'];
		$arrMainData[$user_id]= $res['logged_date'];
	}

	$htmlpart='';
	if(sizeof($arrMainData)>0){
	
		$file_name="user_log_".time().".csv";
		$csv_file_name= write_html("", $file_name);
		//CSV FILE NAME
		if(file_exists($csv_file_name)){
			unlink($csv_file_name);
		}
		$pfx=",";
		$fp = fopen ($csv_file_name, 'a+');
		$data_output.="User Log Report".$pfx;
		$data_output.="Users Selected: ".$selUser.$pfx;
		$data_output.="Created by: ".$createdBy." on ".date($phpDateFormat." h:i A").$pfx;
		$data_output.="\n";
		$data_output.="User Name".$pfx;
		$data_output.="Last Login Date".$pfx;
		$data_output.="\n"; 
		$fp = fopen ($csv_file_name, 'a+');

		//SORTING OF RESULT
		$tempData=$arrMainData;
		unset($arrMainData);
		foreach($arrAllPhysicians as $id =>$name){
			if($tempData[$id]){
				$arrMainData[$id]=$tempData[$id];
			}
		}	
		unset($tempData);

		$i=1;
		foreach($arrMainData as $user_id => $logged_date){
			$printFile=1;
			$htmlpart.='
			<tr>
				<td class="text_10" style="background:#FFFFFF; width:4%">'.$i.'</td>
				<td class="text_10" style="text-align:left; background:#FFFFFF; width:26%">'.$arrAllPhysicians[$user_id].'</td>
				<td class="text_10" style="text-align:center; background:#FFFFFF; width:10%">'.$logged_date.'</td>
				<td class="text_10" style="text-align:left; background:#FFFFFF; width:60%"></td>
			</tr>';

			//FOR CSV
			$data_output.='"'.$arrAllPhysicians[$user_id].'"'.$pfx;
			$data_output.='"'.$logged_date.'"'.$pfx;
			$data_output.="\n";	

			$i++;
		}
		
		@fwrite($fp,$data_output);
		fclose($fp);
		unset($data_output);
	}
}

$hasData=0;
$op='l';
if($htmlpart){
	$hasData=1;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		
	$headerdata='<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
	<tr class="rpt_headers">
		<td class="rptbx1"  style="width:33%">User Log Report</td>
		<td class="rptbx2"  style="width:33%">Users Selected: '.$selUser.'</td>
		<td class="rptbx3"  style="width:34%">Created by: '.$createdBy.' on '.date("".$phpDateFormat." h:i A").'</td>
	</tr>
	</table>';
	
	$htmldata=$headerdata.
	'<table style="width:100%" class="rpt_table rpt_table-bordered">
	<tr>
		<td class="text_b_w" style="width:4%;  text-align:center">#</td>
		<td class="text_b_w" style="width:26%; text-align:center">User Name</td>
		<td class="text_b_w" style="width:10%; text-align:center">Last Login</td>
		<td class="text_b_w" style="width:60%; text-align:center"></td>
	</tr>'
	.$htmlpart.
	'</table>';
	
	$pdfdata=$stylePDF.
	'<page backtop="10mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$headerdata.
	'<table style="width:100%;" class="rpt_table rpt_table-bordered">
	<tr>
		<td class="text_b_w" style="width:4%; text-align:center">#</td>
		<td class="text_b_w" style="width:26%; text-align:center">User Name</td>
		<td class="text_b_w" style="width:10%; text-align:center">Last Login</td>
		<td class="text_b_w" style="width:60%; text-align:center"></td>
	</tr>
	</table>
	</page_header>
	<table  class="rpt_table rpt_table-bordered" style="width:100%;>'
	.$htmlpart.
	'</table>
	</page>';
	
	
	$file_location = write_html($pdfdata, 'user_log_report.html');
	
	if($output_option=='output_csv'){
		echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
	}else if($output_option=='output_pdf'){
			echo '<div class="text-center alert alert-info">PDF gernerated in separate window.</div>';		
	}else{
		echo $htmldata;	
	}
}else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>

