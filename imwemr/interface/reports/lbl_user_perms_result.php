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
FILE : patient_recall_result.php
PURPOSE : PATIENT APPOINTMENT RECALL REPORT
ACCESS TYPE : INCLUDED
*/
require_once(dirname(__FILE__)."/../../config/globals.php");
require_once($GLOBALS['srcdir']."/classes/admin/GroupPrevileges.php");
$oGroupPrevileges = new GroupPrevileges();
//scheduler object
//$obj_scheduler = new appt_scheduler();

//print_r($_POST);
//exit();
//Array ( [processReport] => Daily [Submit] => get reports [form_submitted] => 1 [providerID] => Array ( [0] => 98 [1] => 89 [2] => 95 ) )

$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$page_data = '';	$printFile= false;

if($start_date == ""){
	$start_date = $curDate;
	$end_date = $curDate;
}else{
	$end_date = $start_date;//start and end date wil be equal
}
$curDate.='&nbsp;'.date(" h:i A");

if($_POST['form_submitted']){		
		
		$printFile = true;
		
		
		$primaryProviderId = implode(",",$providerID);	 //posted	
		
		
		
		//--- GET ALL USERS IDS ----		
		if(!empty($primaryProviderId)){
		$arr_pro = $oGroupPrevileges->get_report_data($primaryProviderId);	
		}else{
			$er_msg = "Please select provider.";
		}		
		
		//getting report generator name
		$report_generator_name = NULL;
		if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
			$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
			$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
			$report_generator_name = strtoupper($report_generator_name);
		}


		if(count($arr_pro) > 0){
			//$strHTML = file_get_contents(dirname(_FILE__)."/../themes/default/pdf.css");

			//foreach($lbl_count as $docId => $fac_arr){
				$j=1;
			//	$page_content.='<tr><td class="text_b_w alignLeft nowrap" colspan="7">Physician : '.$provider_arr[$docId].'</td></tr>';

				foreach($arr_pro as $pro_id=>$lbl_counts){
					$page_content.='
					<tr>
						<td class="text alignCenter white" style="width:5%; height:20px;" valign="top">'.$j.'</td>
						<td class="text alignLeft white" style="width:20%;" valign="top">&nbsp;'.$lbl_counts["user"].'</td>
						<td class="text alignLeft white" style="width:10%;" valign="top">&nbsp;'.$lbl_counts["grp_prvlg"].'</td>
						<td class="text alignLeft white" style="width:65%;" valign="top">&nbsp;'.$lbl_counts["prmsns"].'</td>	
					</tr>';
					$j++;
				}

			//}

			$page_data='
			<table class="rpt_table rpt_table-bordered rpt_padding">
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:20%">'.$dbtemp_name.'</td>	
				<td class="rptbx2" style="width:40%">DOS : '.$start_date.'</td>					
				<td class="rptbx3" style="width:40%">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
			</tr>
			</table>
			<table class="rpt_table rpt_table-bordered">
			<tr>
				<td class="text_b_w alignCenter" style="width:5%;">#</td>
				<td class="text_b_w alignCenter" style="width:20%;">User</td>
				<td class="text_b_w alignCenter" style="width:10%;">Group Privilege</td>
				<td class="text_b_w alignCenter" style="width:65%;">Permissions</td>
			</tr>
			</table>
			<table class="rpt_table rpt_table-bordered">
			'.$page_content.'
			</table>';

			$pdf_data= '
				<page backtop="11mm" backbottom="10mm">			
					<page_footer>
						<table style="width:100%;">
							<tr>
								<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
							</tr>
						</table>
					</page_footer>
					<page_header>
						<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
						<tr>
							<td class="rptbx1" style="width:20%">'.$dbtemp_name.'</td>	
							<td class="rptbx2" style="width:40%">From : '.$start_date.' To : '.$end_date.'</td>					
							<td class="rptbx3" style="width:40%">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
						</tr>
						</table>
						<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
						<tr>
							<td class="text_b_w alignCenter" style="width:5%;">#</td>
							<td class="text_b_w alignCenter" style="width:20%;">User</td>
							<td class="text_b_w alignCenter" style="width:25%;">Group Privilege</td>
							<td class="text_b_w alignCenter" style="width:10%;">Permissions</td>
						</tr></table>
					</page_header>
				<table style="width:100%" class="rpt_table rpt_table-bordered"  style="width:100%">'.
				$page_content
				.'</table>
				</page>';						
	} // outermost IF	


	//--- CREATE PDF FILE FOR PRINTING -----
	$hasData=0;
	if($printFile == true and $page_data != ''){
		$hasData=1;
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
		$csv_file_data= $styleHTML.$page_data;

		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		$strHTML = $stylePDF.$pdf_data;

		$file_location = write_html($strHTML);
	}else{
		if(empty($er_msg)){$er_msg="No Record Found.";}
		$csv_file_data = '<div class="text-center alert alert-info">'.$er_msg.'</div>';
	}

echo $csv_file_data;

}
?>