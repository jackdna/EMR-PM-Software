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

	$from_time = '00:00:00';
	$to_time = '23:59:59';	

    if(sizeof($groups)>0)$groups=array_combine($groups,$groups);
	$groups_str = join(',',$groups);
	$operator_id_str = join(',',$operator_id);	

	$sel_opr=imw_query("select id, fname, lname, mname from users");
	$arrAllPhysicians=array();
	while($row_opr=imw_fetch_array($sel_opr)){
		$opr_name="";
		$opr_name = ucfirst(trim($row_opr['fname'][0]));
		$opr_name .= ucfirst(trim($row_opr['lname'][0]));
		$opr_ins[$row_opr['id']]=$opr_name;
		$arrAllPhysicians[$row_opr['id']]=core_name_format($row_opr['lname'], $row_opr['fname'], $row_opr['mname']);
	}
	// GET FACILITIES DETAILS
	$qry = "Select id, name, default_group, fac_prac_code from facility";
	$rs=imw_query($qry);
    $arrAllFacilities = array();
    $arr_sel_pos_fac_ids=array();
	while($res = imw_fetch_array($rs)){
        $grp_id=$res['default_group'];
        $arrAllFacilities[$res['id']] = $res['name'];
        
        if($groups[$grp_id] && $grp_id>0){
            if($res['fac_prac_code']>0){
                $arr_sel_pos_fac_ids[$res['fac_prac_code']]=$res['fac_prac_code'];
            }
        }
    }
    
    $str_sel_pos_fac_ids='';
    if(sizeof($groups)>0){
        if(sizeof($arr_sel_pos_fac_ids)>0){
            $str_sel_pos_fac_ids=implode(',', $arr_sel_pos_fac_ids);
        }else{
            $str_sel_pos_fac_ids='NULL';
        }
    }

	$arrMainData=array();

	//STATEMENTS DATA
    $qry="Select st.patient_id, st.email_status, st.email_operator, DATE_FORMAT(st.email_date_time, '".$date_format_SQL." %H:%i') as 'email_date_time',
    st.statement_default_print as 'doc_type', pd.fname, pd.mname, pd.lname, pd.email  
	FROM previous_statement st
	JOIN patient_data pd ON pd.id=st.patient_id 
	WHERE st.email_sent='1' AND (st.email_date_time BETWEEN '$start_date_whr $from_time' AND '$end_date_whr $to_time')";
	if(empty($operator_id_str)==false){
		$qry.=" AND st.email_operator IN(".$operator_id_str.")";
	}
    if(sizeof($groups)>0){
        $qry.=" AND pd.default_facility IN(".$str_sel_pos_fac_ids.")";
    }
	if(empty($message_output)==false){
        $qry.=" AND st.email_status='".$message_output."'";
	}
	$qry.=" ORDER BY st.email_date_time DESC";
	
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$date=$res['email_date_time'];
		$res['pat_name']=core_name_format($res['lname'], $res['fname'], $res['mname']);
		$arrMainData[$date][]=$res;	
	}

	//DIRECT MESSAGES DATA
	$qry="Select dm.subject as 'pat_name', dm.to_email as 'email', dm.email_status, DATE_FORMAT(dm.local_datetime, '".$date_format_SQL." %H:%i') as 'email_date_time',
	GROUP_CONCAT(DISTINCT ma.mime SEPARATOR ', ') as 'doc_type'
	FROM direct_messages dm
	JOIN direct_messages_attachment ma ON ma.direct_message_id=dm.id  
	WHERE dm.folder_type='3' AND (dm.local_datetime	 BETWEEN '$start_date_whr $from_time' AND '$end_date_whr $to_time')";
	if(empty($operator_id_str)==false){
		$qry.=" AND dm.imedic_user_id IN(".$operator_id_str.")";
	}
	if(empty($message_output)==false){
        $qry.=" AND dm.email_status='".$message_output."'";
	}	
	$qry.=" GROUP BY dm.id ORDER BY dm.local_datetime DESC";

	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$date=$res['email_date_time'];
		$arrMainData[$date][]=$res;	
	}

	//AR WORKSHEET DATA
    $qry="Select cl.patient_id, cl.email_status, DATE_FORMAT(cl.created_date, '".$date_format_SQL." %H:%i') as 'email_date_time',
    pd.fname, pd.mname, pd.lname, pd.email  
	FROM pt_docs_collection_letters cl
	JOIN patient_data pd ON pd.id=cl.patient_id 
	WHERE cl.email_sent='1' AND (cl.created_date BETWEEN '$start_date_whr $from_time' AND '$end_date_whr $to_time')";
	if(empty($operator_id_str)==false){
		$qry.=" AND cl.operator_id IN(".$operator_id_str.")";
	}
    if(sizeof($groups)>0){
        $qry.=" AND pd.default_facility IN(".$str_sel_pos_fac_ids.")";
    }
	if(empty($message_output)==false){
        $qry.=" AND cl.email_status='".$message_output."'";
	}
	$qry.=" ORDER BY cl.created_date DESC";
	
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$date=$res['email_date_time'];
		$res['pat_name']=core_name_format($res['lname'], $res['fname'], $res['mname']);
		$res['doc_type']='PDF';
		$arrMainData[$date][]=$res;	
	}	


	$htmlpart='';
	if(sizeof($arrMainData)>0){

		//MAKING CSV DATA
		$file_name="email_log_".time().".csv";
		$csv_file_name= write_html("", $file_name);
		$pfx=",";
		//CSV FILE NAME
		if(file_exists($csv_file_name)){
			unlink($csv_file_name);
		}
		$fp = fopen ($csv_file_name, 'a+');
		$data_output.="Email Log Report (".$grpTitle." Detail)".$pfx;
		$data_output.="Report Period: ".$Start_date." to ".$End_date.$pfx;
		$data_output.='Created by: '.$createdBy.' on '.date("".$phpDateFormat." h:i A").$pfx;
		$data_output.="\n";
		
		$data_output.="Patient ID".$pfx;
		$data_output.="Patient Name".$pfx;
		$data_output.="Email".$pfx;
		$data_output.="Email Date Time".$pfx;
		$data_output.="Attacement Type".$pfx;
		$data_output.="Status".$pfx;
		$data_output.="\n"; 

		foreach($arrMainData as $date => $dateData){
			foreach($dateData as $detdata){
				
				$htmlpart.='
				<tr>
					<td class="text_10" style="background:#FFFFFF; width:10%">'.$detdata['patient_id'].'</td>
					<td class="text_10" style="background:#FFFFFF; width:20%">'.$detdata['pat_name'].'</td>
					<td class="text_10" style="text-align:left; background:#FFFFFF; width:25%">'.$detdata['email'].'</td>
					<td class="text_10" style="text-align:left; background:#FFFFFF; width:15%">'.$detdata['email_date_time'].'</td>
					<td class="text_10" style="text-align:left; background:#FFFFFF; width:10%">'.strtoupper($detdata['doc_type']).'</td>
					<td class="text_10" style="text-align:left; background:#FFFFFF; width:20%">'.ucfirst($detdata['email_status']).'</td>
				</tr>';

				$data_output.=$detdata['patient_id'].$pfx;
				$data_output.=$detdata['pat_name'].$pfx;
				$data_output.=$detdata['email'].$pfx;
				$data_output.=$detdata['email_date_time'].$pfx;
				$data_output.=strtoupper($detdata['doc_type']).$pfx;
				$data_output.=ucfirst($detdata['email_status']).$pfx;
				$data_output.="\n"; 
			}
		}
		@fwrite($fp,$data_output);
    	fclose($fp);
	}
}

$hasData=0;
$op='l';
if($htmlpart){
	$hasData=1;
	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	
	$headerdata='<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
	<tr class="rpt_headers">
		<td class="rptbx1"  style="width:33%">Email Log Report</td>
		<td class="rptbx2"  style="width:33%">Report Period: '.$Start_date.' to '.$End_date.'</td>
		<td class="rptbx3"  style="width:34%">Created by: '.$createdBy.' on '.date("".$phpDateFormat." h:i A").'</td>
	</tr>
	</table>';
	
	$htmldata=$headerdata.
	'<table style="width:100%" class="rpt_table rpt_table-bordered">
	<tr>
		<td class="text_b_w" style="width:10%; text-align:center">Patient ID</td>
		<td class="text_b_w" style="width:20%; text-align:center">Patient Name</td>
		<td class="text_b_w" style="width:25%; text-align:center">Email</td>
		<td class="text_b_w" style="width:15%; text-align:center">Email Date Time</td>
		<td class="text_b_w" style="width:10%; text-align:center">Attacement Type</td>
		<td class="text_b_w" style="width:20%; text-align:center">Status</td>
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
		<td class="text_b_w" style="width:10%; text-align:center">Patient ID</td>
		<td class="text_b_w" style="width:20%; text-align:center">Patient Name</td>
		<td class="text_b_w" style="width:25%; text-align:center">Email</td>
		<td class="text_b_w" style="width:15%; text-align:center">Email Date Time</td>
		<td class="text_b_w" style="width:10%; text-align:center">Attacement Type</td>
		<td class="text_b_w" style="width:20%; text-align:center">Status</td>
	</tr>
	</table>
	</page_header>
	<table  class="rpt_table rpt_table-bordered" style="width:100%;>'
	.$htmlpart.
	'</table>
	</page>';
	
	$file_location = write_html($pdfdata, 'email_log_report.html');

	if($output_option=='output_csv'){
		echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
	}elseif($output_option=='output_pdf'){		
		echo '<div class="text-center alert alert-info">PDF generated in separate window.</div>';
	}else{
		echo $htmldata;	
	}
}else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>

