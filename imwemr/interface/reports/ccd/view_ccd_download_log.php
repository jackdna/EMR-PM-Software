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

include("../../../config/globals.php");
require_once('../../../library/classes/common_function.php');
require_once('../../../library/classes/cls_common_function.php');

$phpDateFormat=phpDateFormat();

$library_path = $GLOBALS['webroot'].'/library';



//START FETCHING DATA
$getQry = "SELECT *, IF(save_date_time='0000-00-00 00:00:00','',DATE_FORMAT(save_date_time, '".get_sql_date_format('','Y','-')." %h:%i %p')) as execute_date_time
		   FROM ccda_download_log WHERE ccda_export_schedule_operator_id='".$_SESSION["authId"]."' order by save_date_time desc" ;
$getRows = get_array_records_query($getQry);
$facility_id_arr = array();
$dayNumArr = array("1"=>"1<sup>st</sup>","2"=>"2<sup>nd</sup>","3"=>"3<sup>rd</sup>","4"=>"4<sup>th</sup>","5"=>"5<sup>th</sup>");


//START SET DEFAULT VALUES
if(!$_REQUEST["edit_id"]) {
	$HH = "01";
	$MM = "00";
	$reocHH = "01";	
	$reocMM = "00";
}
//END SET DEFAULT VALUES

//END FETCHING DATA
?>
<html>
    <title>imwemr</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery-ui.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-colorpicker.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/messi/messi.css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/admin.css" type="text/css">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
	<![endif]-->
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery-ui.min.1.11.2.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.dragToSelect.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-formhelpers-colorpicker.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/messi/messi.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/simple_drawing.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/Driving_License_Scanning.js"></script>
		
        <style>
		.text_12{
			font-size:11px;
		}
		</style>
    </head>
<body class="whtbox">
<form name="frm_ccd_schedule" id="frm_ccd_schedule" action="manage_ccd_schedule.php" method="post" enctype="multipart/form-data" >
<input type="hidden" name="mode_save_form" id="mode_save_form"/>
<input type="hidden" name="edit_id" id="edit_id" value="<?php echo $_REQUEST["edit_id"];?>" />
<div class="container-fluid" id="report_form">
	
    <?php $col_height_frame = (int) ($_SESSION['wn_height'] - 630);?>
	<div class="col-sm-12">
	</div>
</div>
</form>	
<?php 
$col_height_frame = (int) ($_SESSION['wn_height'] - 370);

?>
    <div style=" width:100%;height:<?php echo $col_height_frame;?>px; overflow:scroll; overflow-x:hidden; ">
        <table class="table table-bordered adminnw" >	
            <thead>
                <tr >
                    <th align="left" width="5%">S.No.</th>
                    <th align="left" width="15%">DOS From</th>
                    <th align="left" width="15%">DOS To</th>
					<th align="left" width="15%">File Name</th>
                    <th align="left" width="20%">Encryption Key</th>
                    <th align="left" width="15%">Execute Date/Time</th>
                    <th align="left" width="15%">Download</th>
                </tr>
            </thead>
            <tbody>
                <?php
				
                $cnt=0;
				if(count($getRows)>0) {
					foreach($getRows as $getRow) {
						$cnt++;
						$id 		= $getRow["id"];
						$encKey 	= $getRow["enc_key"];
						$dateFrom 	= core_date_format($getRow["date_from"],$phpDateFormat);
						$dateTo 	= core_date_format($getRow["date_to"],$phpDateFormat);
						$fileName	= $getRow["file_name"];
						$file_path	= $getRow["file_path"];
						$executeDateTime = $getRow["execute_date_time"];
						$full_file_path= data_path().$file_path;
						
						if(file_exists($full_file_path)){						
					?>
					<tr >
						<td align="left" width="5%" ><?php echo $cnt;?></td>
						
						<td align="left" width="15%" ><?php echo $dateFrom;?></td>
						<td align="left" width="15%" ><?php echo $dateTo;?></td>
						<td align="left" width="15%" ><?php echo $fileName;?></td>
						<td align="left" width="20%" ><?php echo $encKey;?></td>
						<td align="left" width="15%" ><?php echo $executeDateTime;?></td>
                        <td align="left" width="15%" ><a class='btn btn-primary' onclick='window.location.href="download_zip.php?fileName=<?php echo $full_file_path; ?>&zip=1"' >Download</a></td>
						
						
					</tr>
					
					<?php
						}					
					}
				}else{ ?>
					<tr><td colspan="6" style="text-align:center;">No record</td></tr>
				<?php } ?>
            </tbody>
        </table>
        
        
    </div>    
	
		<script type="text/javascript">
			var ar = [ ["manage_ccd_export","Manage CCD Export","top.fmain.manage_ccd_export();"]];			
			top.btn_show("O4A",ar);
			//Btn--
			
			function manage_ccd_export() {
				top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/index.php";
			}
			function edit_ccd_export(edit_id) {
				top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/manage_ccd_schedule.php?edit_id="+edit_id;	
			}
			function del_ccd(del_id) {
				top.fancyConfirm('Do you want to delete selected?','',"top.fmain.del_ccd_export('"+del_id+"')");
			}
			function del_ccd_export(del_id) {
				top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/manage_ccd_schedule.php?del_id="+del_id;	
			}
			function show_hide_fields(sch_type) {
				document.getElementById("div_specific_dt_tm_id").style.display="none";
				document.getElementById("div_reoccurring_dt_tm_id").style.display="none";
				if(sch_type=="Specific Date Time") {
					document.getElementById("div_specific_dt_tm_id").style.display="block";
				}else if(sch_type=="Reoccurring Date Time") {
					document.getElementById("div_reoccurring_dt_tm_id").style.display="block";
				}
			}
			set_header_title('CCD Export');
			
		</script>
    </body>
</html>