<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

$arr_account_status=array();
if($_POST['form_submitted']) {
    $facility_id_sel = array_combine($facilities, $facilities);
    $strPhysician = implode(',', $physicians);
    $arr_account_status=array_combine($account_status,$account_status);
}

//--- GET POS FACILITY NAME ----
$facilityName = $CLSReports->getFacilityName($facility_id_sel, '1');
$allFacCount = sizeof(explode('</option>', $facilityName)) - 1;

//--- GET PHYSICIAN NAME ---
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_sel= ($filing_provider['0']=='0')? 'SELECTED' : '';
$physicianName.= '<option value="0" '.$phy_sel.'>None</option>';
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

$qry="Select * from account_status ORDER BY status_name";
$rs=imw_query($qry);
while($res=imw_fetch_array($rs)){

    $sel=($arr_account_status[$res['id']]) ? 'SELECTED' : '';
    $color= ($res['del_status']=='1') ? 'color:#CC0000!important' : '';

    $status_options.='<option value="'.$res['id'].'" '.$sel.' style="'.$color.'">'.$res['status_name'].'</option>';
}

$dbtemp_name = "Patient Status";
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: imwemr ::</title>
        <!-- Bootstrap -->
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
              <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
              <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->

        <style>
            .pd5.report-content {
                position:relative;
                margin-left:40px;

                background-color: #EAEFF5;
            }
            .fltimg {
                position:absolute;
            }
            .fltimg span.glyphicon {
                position: absolute;
                top: 170px;
                left: 10px;
                color: #fff;
            }
            .reportlft .btn.btn-mkdef {
                padding-top: 6px;
                padding-bottom: 6px;
            }
            #content1{
                background-color:#EAEFF5;
            }
			.total-row {
				height: 1px;
				padding: 0px;
				background: #009933;
			}	
		</style>
    </head>
    <body>
        <form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">
            <input name="processReport" id="processReport" autocomplete="off" value="Daily" type="hidden">
            <input type="hidden" name="SchedulerNewReport" id="SchedulerNewReport" value="">
            <input type="hidden" name="Submit" id="Submit" value="get reports">
            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
            <div class=" container-fluid">
                <div class="anatreport">
                    <div id="common_drop" style="position:absolute;bottom:0px;"></div>
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Practice Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                       <div class="row">
                                            <div class="col-sm-6">
                                                <label>Facility</label>
                                                <select name="facilities[]" id="facilities" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $facilityName; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Physician</label>
                                                <select name="physicians[]" id="physicians" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $physicianName; ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-12">
                                                <label>Account Status</label>
                                                <select name="account_status[]" id="account_status" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $status_options; ?>
                                                </select>
                                            </div>
											
										</div>
                                    </div>
                                </div>
								<div class="clearfix">&nbsp;</div>
                            </div>
                            <div id="module_buttons" class="ad_modal_footer text-center">
                            	<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
                           	</div>
                        </div>
                        <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="toggle-sidebar glyphicon glyphicon-chevron-left"></span>
                            </div>
							<div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
                                        <?php
										if($_POST['form_submitted']) {
											include('patient_status_result.php');
										}else{
                                            echo '<div class="text-center alert alert-info">No Search Done.</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
            </div>
        </form>
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
	<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form>
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var op='p';
	var HTMLCreated='<?php echo $HTMLCreated; ?>';
	var mainBtnArr = new Array();
	var btncnt=0;
	var dbtemp_name = "<?php echo $dbtemp_name; ?>";
	
	if(HTMLCreated==true){
		mainBtnArr[btncnt] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
		btncnt++;
		mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");	
	}
	top.btn_show("PPR", mainBtnArr);
	
	function generate_pdf(op) {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, op);
			window.close();
		}
	}
	
	function checkData(strVal){
		if(strVal.match(/[A-Za-z]/)){
			top.fAlert('Alphabets not allowed in patient ids search.');
			return false;
		}
	}
	
	function get_sch_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		document.sch_report_form.submit();
	}

		
	$(document).ready(function () {
		DateOptions("<?php echo $_POST['dayReport']; ?>");
		$(".toggle-sidebar").click(function () {
			$("#sidebar").toggleClass("collapsed");
			$("#content1").toggleClass("col-md-12 col-md-9");

			if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
			} else {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
			}
			return false;
		});
        
	});

	function set_container_height(){
		$_hgt = (window.innerHeight) - $('#module_buttons').outerHeight(true);
		$('.reportlft').css({
			'height':$_hgt,
			'max-height':$_hgt,
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
		$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});
	} 

	$(window).load(function(){
		set_container_height();
		//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
		if(HTMLCreated==1){		
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);  
</script>
</body>
</html>