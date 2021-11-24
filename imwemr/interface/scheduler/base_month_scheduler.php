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
require_once(dirname(__FILE__).'/../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html

require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
//require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_cl_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_ac_functions.php");
//require_once($GLOBALS['fileroot']."/library/classes/appt_cn_functions.php");

//scheduler object
$obj_scheduler = new appt_scheduler();
//$obj_contactlens = new appt_contactlens();
$obj_accounting = new appt_accounting();
//$obj_chartnotes = new appt_chartnotes($_SESSION['authId'], $_SESSION["patient"]);

//getting date
$load_date = date("m-d-Y");
if(isset($_REQUEST["sel_date"]) && !empty($_REQUEST["sel_date"])){
	$load_date = $_REQUEST["sel_date"];
}
list($m, $dt, $y) = preg_split('/-/', $load_date);

//if jumped
if(isset($_REQUEST["op_typ"]) && !empty($_REQUEST["op_typ"])){
	if(isset($_REQUEST["jmpto"]) && (int)$_REQUEST["jmpto"] > 0){
		if($_REQUEST["op_typ"] == "day"){
			$load_dt_ts_jmp = mktime(0, 0, 0, $m, $dt + $_REQUEST["jmpto"], $y);
			$load_date = date("m-d-Y", $load_dt_ts_jmp);
		}else if($_REQUEST["op_typ"] == "week"){
			$load_dt_ts_jmp = mktime(0, 0, 0, $m, $dt + ($_REQUEST["jmpto"] * 7), $y);
			$load_date = date("m-d-Y", $load_dt_ts_jmp);	
		}else if($_REQUEST["op_typ"] == "month"){
			$load_dt_ts_jmp = mktime(0, 0, 0, $m + $_REQUEST["jmpto"], $dt, $y);
			$load_date = date("m-d-Y", $load_dt_ts_jmp);
		}
	}
	//re getting month day and year
	list($m, $dt, $y) = preg_split('/-/', $load_date);
}

$load_dt_ts = mktime(0, 0, 0, $m, $dt, $y);
$db_load_date = date("Y-m-d", $load_dt_ts);

//getting next 3 mon and 6 mon dates
$load_dt_ts_3 = mktime(0, 0, 0, $m + 3, $dt, $y);
$load_date_3 = date("m-d-Y", $load_dt_ts_3);

$load_dt_ts_6 = mktime(0, 0, 0, $m + 6, $dt, $y);
$load_date_6 = date("m-d-Y", $load_dt_ts_6);

//setting selected fac and prov, if any
if(isset($_REQUEST["sel_fac"]) && !empty($_REQUEST["sel_fac"])){
	$_SESSION['sess_sch_sel_facs'] = $_REQUEST["sel_fac"];//explode(",", $_REQUEST["sel_fac"]);
	$arr_sess_facs = explode(",", $_REQUEST["sel_fac"]);
}else if(isset($_SESSION['sess_sch_sel_facs']) && !empty($_SESSION['sess_sch_sel_facs'])){
	$arr_sess_facs = explode(",", $_SESSION['sess_sch_sel_facs']);
}else{
	$arr_default_facs = array();
	$arr_facs_temp = $obj_scheduler->load_facilities($_SESSION["authId"], "ARRAY");
	for($fc_cnt = 0; $fc_cnt < count($arr_facs_temp); $fc_cnt++){
		$arr_default_facs[] = $arr_facs_temp[$fc_cnt]["id"];
	}
	$_SESSION['sess_sch_sel_facs'] = implode(",", $arr_default_facs);
	$arr_sess_facs = explode(",", $arr_default_facs);
}

if(isset($_REQUEST["sel_pro"]) && !empty($_REQUEST["sel_pro"])){
	$_SESSION['sess_sch_sel_prov'] = $_REQUEST["sel_pro"];//explode(",", $_REQUEST["sel_pro"]);
	$arr_sess_prov = explode(",", $_REQUEST["sel_pro"]);
}else if(isset($_SESSION['sess_sch_sel_prov']) && !empty($_SESSION['sess_sch_sel_prov'])){
	$arr_sess_prov_1 = explode(",", $_SESSION['sess_sch_sel_prov']);
	$arr_sess_prov = $arr_sess_prov_1;
}else{
	$arr_default_prov = array();
	$arr_prov_temp = $obj_scheduler->load_providers("ARRAY");
	for($pr_cnt = 0; $pr_cnt < count($arr_prov_temp); $pr_cnt++){
		$arr_default_prov[] = $arr_prov_temp[$pr_cnt]["id"];
	}
	$_SESSION['sess_sch_sel_prov'] = implode(",", $arr_default_prov);
	$arr_sess_prov = explode(",", $arr_default_prov);
}

//populating week list
$arr_month_list = $obj_scheduler->generate_month_list($db_load_date);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>imwemr :: Monthly Appointments Viewer</title>
		
		<meta name="viewport" content="width=device-width, maximum-scale=0.8" />
		<meta charset="UTF-8" />
		<!-- Bootstrap -->
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css">
    	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">

     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css"><!--
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css">-->
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-colorpicker.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css?version=<?php echo fileatime('../../library/css/schedulemain.css');?>">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-multiselect.css">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css?version=<?php echo fileatime('../../library/css/common.css');?>">     
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="<?php echo $GLOBALS['webroot']?>/library/js/html5shiv.min.js"></script>
          <script src="<?php echo $GLOBALS['webroot']?>/library/js/respond.min.js"></script>
        <![endif]-->
        <script type="text/javascript" src="js_week_scheduler.php"></script>
        
	</head>
	<body onkeypress="keyPressHandler(this);">
		<!-- base hidden fields for storing date -->
		<input type="hidden" id="global_date" name="global_date" value="<?php echo $dt; ?>"/>
		<input type="hidden" id="global_month" name="global_month" value="<?php echo $m; ?>"/>
		<input type="hidden" id="global_year" name="global_year" value="<?php echo $y; ?>"/>

		<div style="width:100%;height:<?php echo $_SESSION["wn_height"] - 242;?>px;margin:0px;background-color:#e6f0ea;">
			<!-- top -->
			
			   <div class="container-fluid scheduleara scheduleara_monthly green_bg">
                 <div class="schedfltara">
                    <div class="row">
                        <div class="col-sm-5 form-inline schflt">
                            <div class="form-group multiselect">
                                <label for="sel_pro_month">Physician</label>
                                <select class="selectpicker minimal selecicon" multiple data-actions-box="true" data-done-button="true" data-size="10" id="sel_pro_month" name="sel_pro_month[]">
                                <?php echo $obj_scheduler->load_providers("OPTIONS", $sel_prov);?>
                                </select>
                            </div>
                            <div class="form-group multiselect">
                                <label for="exampleInputEmail2">Facility</label>
                                <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-actions-box="true" data-size="10" id="facilities" name="facilities[]">
                                    <?php echo $obj_scheduler->load_facilities($_SESSION["authId"], "OPTIONS", $sel_fac);?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-sm-7 form-inline schflt">
                        <div class="row">
                        <div class="col-sm-3 datesched_wk">
                            <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('day');">1</a> 
                            <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('week');">7</a> 
                            <a href="javascript:void(0);" class="active">31</a>
                        </div>
                        <div class="col-sm-5 text-center calndmonth">
                            <ul> 
                                
                                <li>
                                    <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('month', '<?php echo $arr_month_list[2];?>');" title="Previous Month">
                                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/lft1.png" alt="Previous Month" title="Previous Month"/>
                                    </a>
                                </li>
                                
                                <li id="sel_month_year_container">
                                    <select class="form-control minimal" id="sel_week" name="sel_week" onchange="javascript:toggle_sch_type('month', this.value);">
										<?php echo $arr_month_list[0];?>
                                    </select>
                                </li> 
                                
                                <li>
                                    <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('month', '<?php echo $arr_month_list[1];?>');" title="Next Month">
                                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/rtarrow.png" alt="Next Month" title="Next Month"/>
                                    </a>
                                </li> 
                                <li>
                                    <label style="margin-bottom:0px">
										<input type="hidden" id="sel_date" name="sel_date" value="<?php echo $load_date;?>">
                                    	<input type="text" id="dt" name="dt" value="<?php echo $load_date;?>" style="z-index:0; position:absolute; top:-40px">
                                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/calendar1.png" id="dt_img" style="width: 30px">
                                    </label>
                                    
                                </li>
                            </ul>
                    </div>
                    	<div class="col-sm-4 period text-right">
                            <div class="btn-group" role="group" aria-label="...">
                              <button type="button" class="btn btn-default" name="today_button" id="today_button" value="Today" onClick="javascript:toggle_sch_type('month', '<?php echo date("m-d-Y");?>');">Today</button>
                              <button type="button" class="btn btn-default" name="3_mon" id="3_mon" value="3 Mon" onClick="javascript:toggle_sch_type('month', '<?php echo $load_date_3;?>');">3 Mon</button>
                              <button type="button" class="btn btn-default" name="6_mon" id="6_mon" value="6 Mon" onClick="javascript:toggle_sch_type('month', '<?php echo $load_date_6;?>');">6 Mon</button>
                            </div>
                            <div class="" style="float:right">
                             <a style="background-color: #EEE; padding-top: 4px; padding-bottom: 6px; cursor: pointer;" aria-hidden="true" onClick="javascript:print_monthly_sch();" title="Print Month Appts / OR Applicability"><img src="../../library/images/scprint.png" title="Print Month Appts / OR Applicability"></a>
                             </div>
                              
                    	</div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
			<!-- calendar and front desk -->
			
				<div id="hold">
					<div id="wn" style="left:0px">
						<div id="lyr1">
							<?php 
							$dataStr=$obj_scheduler->create_month_calendar($db_load_date, $arr_sess_prov, $arr_sess_facs);
							list($display,$print_data)=explode('~~::~~',$dataStr);
							$file_location = write_html($print_data,'month_schedule_'.$_SESSION['authId'].'.html');
							echo $display;
							?>
						</div>
					</div>
				</div>
			
		</div>
        
        
        <!-- day proc summary div -->
		<div id="day_proc_summ_div" class="section" style="position:absolute;z-index:999;width:220px;top:100px;left:460px;display:none;">
			<div id="day_proc_summ_div-handle" class="section_header" style="height:14px;text-align:left"><div class="fl" id="day_proc_summ_date"></div><div class="fl"> - Day Summary</div><div class="fr"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/close14.png"  onclick="javascript:display_block_none('day_proc_summ_div', 'none');" style="cursor:pointer;" /></div></div>
			<div id="baseContentDiv" style="width:100%; height:100%;background-color:#ffffff; overflow-y:scroll;overflow-x:hidden;">
            	<div id="day_proc_summ_content" style="height:250px;"></div>
            </div>
		</div>

		<script>
			/*
			Purpose: on load actions
			Author: AA
			*/
			$(document).ready( function() {
				$('#facilities').on('hide.bs.select', function() {
					top.show_loading_image("show");
					fac_change_load('month'); // or $(this).val()
				});
				
				$('#sel_pro_month').on('hide.bs.select', function() {
					top.show_loading_image("show");
				  	pro_change_load('month'); // or $(this).val()
				});	
				
				var date_global_format = top.jquery_date_format;
				$('#dt').datetimepicker({
					timepicker:false,
					format:date_global_format,
					formatDate:'Y-m-d',
					scrollInput:false
				}).change(function(){ 
						var dt_val = $("#dt").val();
						dt_val = top.getDateFormat(dt_val,'mm-dd-yyyy');
						toggle_sch_type('month', dt_val);
					});
							

				total_mnth_blocks = $('.mnth_bl_sh').size();
				total_month_rows = total_mnth_blocks/7;

				for(i =1; i<=total_month_rows; i++)
				{
					var hg_mnth_block_height = 0;
					for(j=1;j<=7;j++)
					{
						ind = (j+(i*7))-8;
						cur_blk_el = $('.mnth_bl_sh')[ind];
						cur_blk_ht = $(cur_blk_el).height();
						if(cur_blk_ht > hg_mnth_block_height)
						{
							hg_mnth_block_height = cur_blk_ht;
						}
					}
					//alert('got the value'); return false;
					for(j=1;j<=7;j++)
					{
						ind = (j+(i*7))-8;
						cur_blk_el = $('.mnth_bl_sh')[ind];
						$(cur_blk_el).height(hg_mnth_block_height);
					}					
				}
			});
			function ld_month_sch_fn()
			{
				top.show_loading_image("show");
				window.location.reload();
			}						
			top.show_loading_image("show");
			load_month_scheduler('<?php echo implode(',', $arr_sess_facs);?>', '<?php echo implode(',', $arr_sess_prov);?>');	
			function print_monthly_sch()
			{
				top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
				top.html_to_pdf('<?php echo $file_location; ?>','l');

			}
		</script>
	</body>
</html>