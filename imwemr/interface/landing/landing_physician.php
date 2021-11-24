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

//this file being include to get top 5 messages
require_once (dirname(__FILE__).'/../../library/classes/msgConsole.php');
$msgConsoleObj = new msgConsole();
$landPhyObj = new landing_physician();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:: imwemr ::</title>
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/landing_page.css" rel="stylesheet">
    <!--this style sheet being used for form styles like checkboxes-->
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css">
	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/core.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/owl.theme.default.min.css">
    <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
    <?php } ?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
    <![endif]-->


    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/serial.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/pie.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/themes/light.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/chart_js.js"></script>

  </head>
<body>
<div class="container-fluid pt10 landphy">
	<div class="row">
    	<div class="col-lg-4 col-md-4 col-sm-6">
    		<div class="whitebox">
    			<div class="boxheader">
					<h2 data-toggle="collapse" data-target="#load_messages" class="link_cursor"><figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/mail_icon.png" alt=""/></figure>Top 5 User Messages/Reminders </h2>
					<div class="hdoption">
                    	<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Move" /> -->
                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/delete.png" alt="" data-toggle="tooltip" data-placement="bottom" title="Delete" onclick="deleteMessages('messages');" />
                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Reload" onclick="reload('load_messages');" />
                    </div>
				</div>
				<div class="clearfix"></div>
                <div id="load_messages" class="scroll-content mCustomScrollbar tablcont collaspe in">
                <?php
				$landPhyObj->top_five_messages('messages');
				?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-6">
            <div class="whitebox">
                <div class="boxheader">
                    <h2 data-toggle="collapse" data-target="#load_direct" class="link_cursor"><figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/mail_icon.png" alt=""/></figure>Top 5 Direct </h2>
                    <div class="hdoption">
						<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" />-->
						<img src="<?php echo $GLOBALS['webroot'];?>/library/images/delete.png" alt="" data-toggle="tooltip" data-placement="bottom" title="Delete" onclick="deleteMessages('direct');" />
						<img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" onclick="reload('load_direct');" />
					</div>
                </div>
                <div class="clearfix"></div>
                <div id="load_direct" class="scroll-content mCustomScrollbar tablcont collaspe in">
                <?php
					$landPhyObj->top_five_messages('direct');
				?>
                </div>
                <div class="clearfix"></div>
            </div>
		</div>
        <div class="col-lg-4 col-md-4 col-sm-12">
        	<div class="whitebox">
        		<div class="boxheader">
                    <h2 data-toggle="collapse" data-target="#erxListing" class="link_cursor" style="padding:0px;">eRx </h2>
        			<div class="hdoption">
                    	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/loading.gif" id="img_erx_loading_anim" class="hide" />
                    	<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Move" /> -->
                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="load_landing_erx_data();" class="link_cursor" />
                    </div>
		        </div>
        		<div class="clearfix"></div>
        		<div id="erxListing" class="scroll-content mCustomScrollbar tablcont collaspe in">
					<?php
                    //$landPhyObj->top_five_messages('erx');
                    ?>
        	     </div>
                <div class="clearfix"></div>
            </div>
        </div>
	</div>

    <div class="clearfix"></div>
    <div class="row">
        <div class="col-lg-9 col-md-8 col-sm-12">
            <div class="whitebox">
                <div class="boxheader">
                	<h2 data-toggle="collapse" data-target="#patientSchedule" class="link_cursor">
                		<figure>
                        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/checkin.png" alt=""/>
                        </figure>
                		Today’s Patient Schedule
                    </h2>

                	<div class="hdoption">
                    	<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" /> -->
                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="reload('patientSchedule')" />
                    </div>

                </div>
            	<div class="clearfix"></div>
                <div id="patientSchedule" class="scroll-content-middle mCustomScrollbar tablcont collaspe in">
                	<?php $landPhyObj->today_appts();?>
                </div>
            	<div class="clearfix"></div>
            </div>
    	</div>
    <div class="col-lg-3 col-md-4 col-sm-12">
    <div class="whitebox">
        <div class="boxheader">
        	<h2 data-toggle="collapse" data-target="#appointmentSummary" class="link_cursor">
            	<figure>
            		<img src="<?php echo $GLOBALS['webroot'];?>/library/images/overall.png" alt=""/>
                </figure>
        		Overall
            </h2>

        	<div class="hdoption">
            	<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" />-->
                <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="reload('appointmentSummary')" />
            </div>
        </div>
        <div class="clearfix"></div>
        <div id="appointmentSummary" class="appointsum collaspe in">
			<?php
			$arrApptSummary=$landPhyObj->appt_summary();
			list($arrApptSummary,$html)=explode('~:~',$arrApptSummary);
			echo $html;
			?>
        </div>
        <div class="clearfix"></div>
    </div>
    </div>
    </div>

    <div class="clearfix"></div>
    <div class="row">
        <div class="col-sm-6">
            <div class="whitebox charttab">
                <div class="boxheader erx">
                    <h2 data-toggle="collapse" data-target="#unfinalizedCharts" class="link_cursor"><figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/icon.png" alt=""/></figure>
                    Un-finalized Chart </h2>

                    <div class="hdoption">
                       <!-- <img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" /> -->
                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="reload('unfinalizedCharts')" />
                        <!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/print.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Print" />
                        <img src="<?php echo $GLOBALS['webroot'];?>/library/images/delete.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Delete" /> -->
                    </div>
                </div>
                <div class="clearfix"></div>
                <div id="unfinalizedCharts" class="scroll-content mCustomScrollbar tablcont collapse in">
                      <?php $landPhyObj->unfinalized_chart();?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="whitebox charttab">
                <div class="boxheader erx">
                <h2 data-toggle="collapse" data-target="#uninterpretedTests" class="link_cursor"><figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/intreupttest.png" alt=""/></figure>
                Un-Interpreted Tests <span class="hint pull-right" style="margin-right:50px;">(listing limited to max 15 records)</span></h2>
                <div class="hdoption">
                    <!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" /> -->
                    <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="reload('uninterpretedTests')" /> <!--
                    <img src="<?php echo $GLOBALS['webroot'];?>/library/images/print.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Print" />
                    <img src="<?php echo $GLOBALS['webroot'];?>/library/images/delete.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Delete" /> -->
                </div>
                </div>
                <div class="clearfix"></div>
                <div id="uninterpretedTests" class="scroll-content mCustomScrollbar tablcont collapse in">
                    <?php $landPhyObj->un_int_test();?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
	
	<?php if( get_refill_direct_users() && isERPPortalEnabled() ) {
		include_once('medication_refill_req.php');
	} ?>
	
</div>


<?php require_once('common_landing_bottom.php');?>
<script>
<?php if($arrApptSummary){?>pie_chart('pie','appt_summary_div','<?php echo $arrApptSummary; ?>','','','0');<?php }?>
function load_landing_erx_data(){
	$('#img_erx_loading_anim').removeClass('hide');
	$.ajax({
	  url: '<?php echo $GLOBALS['webroot'];?>/interface/landing/ajax_erx_inbox_data.php',
	  beforeSend: function(){
		$('#erxListing').mCustomScrollbar();
		$('#erxListing').mCustomScrollbar('updateContent', landingSectionLoader, true);
	  },
	  success: function(r) {//a=window.open();a.document.write(r);
		$('#img_erx_loading_anim').addClass('hide');
		if(r!=''){
			$('#erxListing').mCustomScrollbar('updateContent', r);
		}
	  }
	});
}

function open_erx(patientId){
	window.open('../chart_notes/erx_patient_selection.php?patientFromSheduler='+patientId,'erx_window_new','resizable=1,width=1200,height='+screen.height+',scrollbars=1');
}

function loadPtThenTEST(pt_id,test_table,test_id){
	moduleHandlerURL = '../tests/module_handler.php?param='+test_id+'~'+test_table;
	window.top.LoadPtThenModule(pt_id,moduleHandlerURL);

}

//pT portal --
var oPUF=[];
var erp_api_patient_portal = '<?php echo isERPPortalEnabled() ? "1" : "0"; ?>';

$(document).ready(function(){
	///setTimeout(function(){load_landing_erx_data();},5000) 
	iportal_load_app_reqs();
});
    if(typeof(top.btn_show)!='undefined'){top.btn_show('DEF');}
</script>
  </body>
</html>
