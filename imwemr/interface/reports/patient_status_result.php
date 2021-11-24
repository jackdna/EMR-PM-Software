<?php
$print_file = true;

if(empty($form_submitted) === false){	
	$print_file = false;	
	//-- OPERATOR INITIAL -------
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	$curDate = date(phpDateFormat().' h:i A');

    $sel_account_status_count=sizeof($account_status);
    
    $facilities = implode(',',$facilities);
    $physicians = implode(',',$physicians);
    $account_status = implode(',',$account_status);
    

	// -- GET ALL POS-FACILITIES
	$arrAllFacilities=array();
	$arrAllFacilities[0] = 'No Facility';
	$qry = "select pos_facilityies_tbl.facilityPracCode as name,
		pos_facilityies_tbl.pos_facility_id as id,
		pos_tbl.pos_prac_code
		from pos_facilityies_tbl
		left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
		order by pos_facilityies_tbl.headquarter desc,
		pos_facilityies_tbl.facilityPracCode";
	$qryRs = imw_query($qry);
	while($qryRes  =imw_fetch_assoc($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}						
	// ------------------------------
		
	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
    }

    $qry="Select * from account_status ORDER BY status_name";
    $rs=imw_query($qry);
    $arr_all_status=array();
    while($res=imw_fetch_array($rs)){
        $arr_all_status[$res['id']].=$res['status_name'];
    }
    //------------------------------------------------

    $cols=6;
    $firstColWidth=5;
    $w_cols= (100-$firstColWidth)/ ($cols-1);
    $firstColWidth.='%';
    $w_cols.='%';


    $qry = "Select id, fname, mname, lname, default_facility, providerID, pat_account_status FROM patient_data WHERE pat_account_status>0";
    if(empty($account_status)==false){
        $qry.=" AND pat_account_status IN(".$account_status.")";
    }
    if(empty($facilities)==false){
        $qry.=" AND default_facility IN(".$facilities.")";
    }
    if(empty($physicians)==false){
        $qry.=" AND providerID IN(".$physicians.")";
    }
    $qry.=" ORDER BY lname, fname, mname";
	$rs = imw_query($qry) or die(imw_error());
    $i=1;
	while($res = imw_fetch_assoc($rs)){
        $print_file=true;
        $patientName = core_name_format($res['lname'], $res['fname'], $res['mname']);

        $html_part.='<tr>
            <td class="text_10" style="background:#FFFFFF; text-align:center; width:'.$firstColWidth.';">'.$i.'</td>
            <td class="text_10" style="background:#FFFFFF; width:'.$w_cols.';">'.$patientName.'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:center; width:'.$w_cols.';">'.$res['id'].'</td>
            <td class="text_10" style="background:#FFFFFF; width:'.$w_cols.';">'.$arr_all_status[$res['pat_account_status']].'</td>
            <td class="text_10" style="background:#FFFFFF; width:'.$w_cols.';">'.$arrAllFacilities[$res['default_facility']].'</td>
            <td class="text_10" style="background:#FFFFFF; width:'.$w_cols.';">'.$providerNameArr[$res['providerID']].'</td>
        </tr>';	
        $i++;	
	}
}

$HTMLCreated = 0;
if($print_file == true){

    $selFac = $CLSReports->report_display_selected($facilites,'practice',1, $allFacCount);	
    $selPhy = $CLSReports->report_display_selected($physicians,'physician',1, $allPhyCount);
    $selSts='All';

    if($sel_account_status_count>0 && $sel_account_status_count<sizeof($arr_all_status)){
        $selSts=($sel_account_status_count==1) ? $arr_all_status[$account_status] : 'Multi';
    }
    
	$html_file_data='<table class="rpt_table rpt rpt_table-bordered rpt_padding">
    <tr >
        <td style="text-align:left;" class="rptbx1" width="34%">Patient Status Report</td>
        <td style="text-align:left;" class="rptbx2" width="34%">Account Status : '.$selSts.'</td>
        <td style="text-align:left;" class="rptbx3" width="33%">Created by '.$opInitial.' on '.$curDate.'</td>
    </tr>
    <tr>
        <td class="rptbx1">Facility : '.$selFac.'</td>
        <td class="rptbx2">Physician : '.$selPhy.'</td>
        <td class="rptbx3"></td>
    </tr>
	</table>
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		<tr>
            <td class="text_b_w" style="text-align:center; width:'.$firstColWidth.';">#</td>
            <td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Patient Name</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Patient ID</td>
            <td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Account Status</td>
            <td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Facility</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Physician</td>
		</tr>
		'.$html_part.'
    </table>';

    
    $pdfdata=
	'<page backtop="15mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
    <page_header>
    <table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
    <tr >
        <td style="text-align:left; width:34%" class="rptbx1">Patient Status Report</td>
        <td style="text-align:left; width:34%;" class="rptbx2">Account Status : '.$selSts.'</td>
        <td style="text-align:left; width:33%;" class="rptbx3">Created by '.$opInitial.' on '.$curDate.'</td>
    </tr>
    <tr>
        <td class="rptbx1">Facility : '.$selFac.'</td>
        <td class="rptbx2">Physician : '.$selPhy.'</td>
        <td class="rptbx3"></td>
    </tr>
    </table>
	<table style="width:100%" class="rpt_table rpt_table-bordered">
		<tr>
            <td class="text_b_w" style="text-align:center; width:'.$firstColWidth.';">#</td>
            <td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Patient Name</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Patient ID</td>
            <td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Account Status</td>
            <td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Facility</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Physician</td>
        </tr>
    </table>                
	</page_header>
	<table  class="rpt_table rpt_table-bordered" style="width:100%;>'
	.$html_part.
	'</table>
	</page>';

    $HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
    $html_file_data= $styleHTML.$html_file_data;
    
    $stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
    $pdfdata= $stylePDF.$pdfdata;

	$file_location = write_html($pdfdata);
    $HTMLCreated = 1;
    
	echo $html_file_data;
} else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>