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
  PURPOSE : Get QRDA - III score card
  ACCESS TYPE : Direct
 */

$without_pat = "yes";
require_once(dirname(__FILE__) . "/../reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../../library/classes/class.reports.php');
require_once('../../../library/classes/cls_common_function.php');
require_once('../common/report_logic_info.php');
$temp_id = $_REQUEST['sch_temp_id'];

include_once(dirname(__FILE__) . "/../../../config/globals.php");
include_once(dirname(__FILE__) . "/../../../library/classes/class.mur_reports.php");
$library_path = $GLOBALS['webroot'] . '/library';
$objMUR = new MUR_Reports;

//From main file, where this current file is included.
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
	<script>var result_gen = false;</script>
	<script type="text/javascript" src="./qrda.js?<?php echo filemtime($library_path . '/js/reports_mur.js'); ?>"></script>
	<style>
	    .pd5.report-content {
		background-color: #EAEFF5;
	    }	
	    #content1{
		background-color:#EAEFF5;
	    }
	    #mur_version,.date-pick{width:100px;}
	    .visibility_hidden{visibility:hidden;}
	</style>
    </head>
    <body>
	<div class=" container-fluid">
	    <div class="whitebox" id="report_form">
		<form name="form_mur" id="form_mur" method="post" action="" autocomplete="off">
		    <input type="hidden" name="mur_version" id="mur_version" value="<?php echo $mur_version; ?>">
		    <input type="hidden" name="selectedNQF" id="selectedNQF" value="" />
		    <div class="row">
			<div class="col-sm-2">
			    <?php echo '<h4><b>2019 QRDA Export</b></h4>'; ?>
			</div>
                        <div class="col-sm-2">
			    <label for="provider">Search Patient</label>
			    <input type="hidden" name="patient_id" id="patient_id" value="" /> 
                            <input type="text" class="form-control" name="patient_name" id="patient_name" placeholder="Search Patient" /> 
			</div>
                        
			<div class="col-sm-2">
			    <label for="provider">Provider</label>
			    <select class="form-control minimal" name="provider" id="provider">
				<option value="0">----SELECT----</option>
				<?php
				$option_pro_arr = $objMUR->get_provider_ar(0);
				foreach ($option_pro_arr as $OptphyId => $OptphyName) {
				    echo '<option value="' . $OptphyId . '">' . $OptphyName . '</option>';
				}
				?>
			    </select>
			</div>
			<div class="col-sm-1"></div>
			<div class="col-sm-1">
			    <label for="dtfrom">Date From</label>
			    <input type="text" name="dtfrom" id="dtfrom" size="11" maxlength="10" class="form-control date-pick" onBlur="checkdate(this);" value="<?php echo date(phpDateFormat(), strtotime(date('Y/m/1'))); ?>" />
			</div>
			<div class="col-sm-1">
			    <label for="dtupto">To</label>
			    <input type="text" name="dtupto" id="dtupto" size="11" maxlength="10" class="form-control date-pick" onBlur="checkdate(this);" value="<?php echo date(phpDateFormat()); ?>" />
			</div>
			
			<div class="col-sm-1 text-center"><label>&nbsp;</label><br><input type="button" class="btn btn-success" value="Get Report" onClick="searchResult(2)"></div>
			<div class="col-sm-3"></div>
		    </div>
		</form>
	    </div>
	    <div class="clearfix"></div>
	    <div class="whitebox">
		<div id="report_result_area">
		    &nbsp;
		</div>
	    </div>
	</div>
	<script type="text/javascript">
	    function set_container_height() {
		h = parseInt(window.innerHeight) - parseInt($('#report_form').height());
		h = h - 60; //margins
		$('#report_result_area').css({
		    'height': (h),
		    'max-height': (h),
		    'overflow-x': 'hidden',
		    'overflowY': 'auto'
		});
	    }

	    $(window).load(function () {
		set_container_height();
	    });

	    $(window).resize(function () {
		set_container_height();
	    });

	    //Btn --
	    if (result_gen) {
		var ar = [["qrda_cat1", "QRDA Cat I", "window.frames['fmain'].searchResult(7);"],
		    ["qrda_cat3", "QRDA Cat III", "window.frames['fmain'].searchResult(8);"]];
		top.btn_show("mur_stage_qrda", ar);
	    } else {
		top.btn_show();
	    }
	    //Btn --
            
            $(function(){
              $("#patient_name").typeahead({
                onSelect: function(item) {
                    $("#patient_id").val(item.value);
                },
                ajax: {
                        url: "patient_list.php",
                        timeout: 500,
                        displayField: "title",
                        triggerLength: 1,
                        method: "get",
                        loadingClass: "loading-circle",
                        preProcess: function (data) {
                            if (data.success === false) {
                                // Hide the list, there was some error
                                return false;
                            }
                            return data.mylist;
                        }
                }  
                  
              }).keyup(function() {
                    if ($(this).val() == '') {  
                        $("#patient_id").val('');
                    }
                 });
            });

	</script> 
    </body>
</html>