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
FILE : yearlyReports.php
PURPOSE : Search criteria of yearly report
ACCESS TYPE : Direct
*/

//Function files
require_once("reports_header.php");
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('common/report_logic_info.php');


$CLSCommonFunction= new CLSCommonFunction;
$CLSReports = new CLSReports;

//REPORT LOGIC INFORMATION FLOATING DIV
$logicWidth=600;
$logicDiv=reportLogicInfo('yearly','tpl', $logicWidth);
$logicCSS=reportLogicInfoHeader('tpl');


$Start_year=$End_year=date('Y');

$strPhysician='';
if($_POST['form_submitted']){
	$grp_id = array_combine($grp_id,$grp_id);
	$sc_name = array_combine($sc_name,$sc_name);
	$strPhysician= implode(',', $_POST['Physician']);
	$Start_year=$_POST['Start_year'];
	$End_year=$_POST['End_year'];
}


//GET GROUPS NAME
$rs = imw_query("Select  gro_id,name,del_status from groups_new order by name");
$core_drop_groups = "<option value=''> All </option>";
while($row = imw_fetch_array($rs))
{
	$sel=''; $color='';
	if($row['del_status']=='1')$color='color:#CC0000!important';

	if($grp_id[$row['gro_id']])$sel='SELECTED';

	$core_drop_groups.='<option value="'.$row['gro_id'].'" '.$sel.' style="'.$color.'" >'.$row['name'].'</option>';
}
$allGrpCount = sizeof(explode('</option>', $core_drop_groups))-2;


//--- GET PHYSICIAN NAME ---
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician,'1','1','','report');
$allPhyCount = sizeof(explode('</option>', $physicianName))-1;

//--- GET FACILITY NAME ----
$facilityName = $CLSReports->getFacilityName($sc_name);
$allFacCount = sizeof(explode('</option>', $facilityName))-1;

//--- YEAR SELECTION ---
$year_drop_data='';
$YearArr = range(date('Y'), 2005);
foreach($YearArr as $year){
	$startSel=$endSel='';
	if($year==$Start_year)$startSel='SELECTED';
	if($year==$End_year)$endSel='SELECTED';
	$start_year_options.='<option value="'.$year.'" '.$startSel.'>'.$year.'</option>';
	$end_year_options.='<option value="'.$year.'" '.$endSel.'>'.$year.'</option>';
}
?>

<style>
	.rptsearch1, .rptsearch2, .rptsearch3{ min-height:65px;}
	.rptsearch2 .col-sm-5 {
    	width:44%;
	}


</style>

<form name="frm_reports" id="frm_reports" action="" method="post">
<input type="hidden" name="form_submitted" id="form_submitted" value="1">
<div class="reptbox"><div class="row productivity">
 
    <div class="col-sm-6">
  
        <div class="rptsearch1">
        <div class="row">
            <div class="col-sm-3">
                <label>Groups</label>
                <select name="grp_id[]" id="core_grp_id" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
					<?php echo $core_drop_groups;?>
                </select>
            </div>
            <div class="col-sm-3">
                <label>Facility</label>
                <select name="sc_name[]" id="sc_name" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                    <option value="">Select All</option>
                    <?php echo $facilityName;?>
                </select>
            </div>	
            <div class="col-sm-3">
                <label>Physician</label>
                <select name="Physician[]" id="Physician" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                    <option value="">Select All</option>
					<?php echo $physicianName;?>
                </select>
            </div>		
            <div class="col-sm-3"></div>		
        </div>
        </div>            	
    </div>
    <div class="col-sm-6">
        <div class="rptsearch2">
        <div class="row">
            <div class="col-sm-2">
            	<label>Date For</label>
                <select name="date_range_for" id="date_range_for" class="selectpicker" data-width="100%" data-size="10" data-actions-box="false">
                  <option value="DOS" <?php if($date_range_for=='DOS')echo 'SELECTED';?>>Date of Service</option>
                  <option value="DOP" <?php if($date_range_for=='DOP')echo 'SELECTED';?>>Date of Payment</option>
                  <option value="DOT" <?php if($date_range_for=='DOT')echo 'SELECTED';?>>Date of Transaction</option>
                </select>
            </div>	
            <div class="col-sm-2">	
               <label>From</label>
               <select name="Start_year" id="Start_year" class="selectpicker" data-width="100%" data-size="10" data-actions-box="false">
               		<?php echo $start_year_options;?>
               </select>
         	</div>
            <div class="col-sm-2">	
               <label>To</label>
               <select name="End_year" id="End_year" class="selectpicker" data-width="100%" data-size="10" data-actions-box="false">
               		<?php echo $end_year_options;?>
               </select>
         	</div>
            <div class="col-sm-6">	
         	</div>            
         </div>	        
        </div>
        </div>    
                   	
    </div>
   </div>	
<div></div>
</div>
</form>

<!-- RESULT PART -->
<div id="csvFileDataTable" style="height:555px; overflow-y:scroll;">
	<?php
    if($_POST['form_submitted']){
       include('yearlyResult.php'); 
    }
    ?>
</div>
<!-- END RESULT PART -->

<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="yearly_report.csv" />
</form>

<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var printFile= '<?php echo $printFile; ?>';

	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("generate","Get Report","top.fmain.get_report();");
	if(printFile=='1'){
		mainBtnArr[1] = new Array("print","Print PDF","top.fmain.generate_pdf();");
		mainBtnArr[2] = new Array("start_process","Export CSV","top.fmain.export_csv();");
	}
	top.btn_show("PPR",mainBtnArr);
	//-------	

	function get_report(){
		top.show_loading_image('hide');
		top.show_loading_image('show');

		document.frm_reports.submit();
	}


	function generate_pdf(){
		if(file_location!=''){
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location,'l');
			window.close();
		}
	}

	function export_csv(){
		top.show_loading_image('hide');
		getCSVData();
		document.csvDownloadForm.submit();
	}

	$(document).ready(function(e) {
        $('.oldYear').hide();
    });
	
</script>