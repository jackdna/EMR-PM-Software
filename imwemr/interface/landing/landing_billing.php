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

$landBillAdmObj = new landing_billing_admin();
$loginFacilityName=$landBillAdmObj->loginFacilityName;

$startDate=date('Y-m-01');
$endDate=date('Y-m-t');
$curDate=date('Y-m-d');
//getting unapplied charges for given date range
$arrUnappliedCharges = $landBillAdmObj->get_unapplied_charges($startDate, $endDate, $chart_view='1');
//getting unapplied superbills for given date range
$arrUnappliedSuperbills = $landBillAdmObj->get_unapplied_superbills($startDate, $endDate, $chart_view='1');
//getting sum of collection from front desk for given date range
$arrFDCollectionHTML = $landBillAdmObj->get_fd_collection($startDate, $endDate, $_SESSION['login_facility']);

$arrUnappliedSuperbills_js_arr=json_encode($arrUnappliedSuperbills);

if(sizeof($arrUnappliedCharges)>0){
	$bar_data_arr=$landBillAdmObj->bar_chart_fun($arrUnappliedCharges,'Charges');
}
//preparing data for graphs
$column_graph_js_arr=json_encode($bar_data_arr['column_graph_arr']);
$value_field_graph_js_arr=json_encode($bar_data_arr['value_field_graph_arr']);

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
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/owl.carousel.css">
<!--
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/owl.theme.default.min.css">
-->
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/js/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/js/respond.min.js"></script>
    <![endif]-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/amcharts.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/serial.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/pie.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/themes/light.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/chart_js.js"></script>

</head>
<body>
<div class="container-fluid pt10 landbilling">
  <div class="row">
    <div class="col-lg-5 col-md-12 col-sm-12">
      <div class="whitebox">
        <div class="boxheader">
          <h2>
            <figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/mail_icon.png" alt=""/></figure>
            <span <?php echo show_tooltip("Not Posted superbills/encounters charges of current month till today"); ?>>Unapplied Charges</span> </h2>
          <div class="hdoption"><!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" />--> <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" /></div>
        </div>
        <div class="clearfix"></div>
        <div class="text-center">
            <div id="unapplied_charge_div" style="width:100%;float:left;height:350px;"></Div>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
    <div class="col-lg-5 col-md-8 col-sm-7">
      <div class="whitebox">
        <div class="boxheader">
          <h2>
            <figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/unappliedbill.png" alt=""/></figure>
            <span <?php echo show_tooltip("Unprocessed superbills charges of current month till today"); ?>>Unprocessed Superbill</span></h2>
          <div class="hdoption"><!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" /> --><img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" /></div>
        </div>
        <div class="clearfix"></div>
		<div id="unapplied_superbills_div" style="width:100%;float:left;height:350px;"></div>
        <div class="clearfix"></div>
      </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-5">
    <div class="whitebox tcharg">
    <div class="boxheader">
          <h2>
            <figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/today_charges1.png" alt=""/></figure>
            Today Charges </h2>
          <div class="hdoption"><!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" />--> <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" /></div>
        </div>
        <div class="clearfix"></div>
    <div class="totalpayment">
      <figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/totalcharges.png" alt=""/></figure>
      <h3> <span <?php echo show_tooltip("Unprocessed superbills and encounter charges of today","top"); ?>>Total Charges</span> <?php echo $landBillAdmObj->get_total_charges(date('Y-m-d'), date('Y-m-d'));?></h3>
    </div>
    <div class="clearfix"></div>
    <div class="totalpayment">
         <figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/total_payment.png" alt=""/></figure>
      	 <h3> <span <?php echo show_tooltip("CI/CO, PMT and applied encounter payments of today","top"); ?>>Total Payments</span> <?php echo $landBillAdmObj->get_total_payments(date('Y-m-d'), date('Y-m-d'));?></h3>
    </div>
    </div>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="row">
    <div class="col-sm-5">
      <div class="whitebox">
        <div class="boxheader">
          <h2>
            <figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/checkin.png" alt=""/></figure>
            <span <?php echo show_tooltip("Not Posted ERA files"); ?>>ERA</span></h2>
          <div class="hdoption"><!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" />--> <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" /></div>
        </div>
        <div class="clearfix"></div>
        <div class="scroll-content mCustomScrollbar tablcont">
          <div class="table-responsive respotable">
				<?php echo $landBillAdmObj->get_era($startDate, $endDate); ?>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
    <div class="col-sm-7">
      <div class="whitebox fdcolblc">
        <div class="boxheader">
          <h2>
            	<figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/fdcollection.png" alt=""/></figure>
            	<span <?php echo show_tooltip("CI/CO and PMT payments of current month till today"); ?>>FD Collection</span><span class="reportsum">(Report Summary)</span>
          </h2>
          <div class="hdoption"><!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" />--> <img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" /></div>
        </div>
        <div class="clearfix"></div>
        <div class="scroll-content mCustomScrollbar tablcont">
		  <div class="owl-carousel">
              <div class="item">
              <div class="colltable">
                <h3>Facility <span class="facility"> <?php echo $loginFacilityName;?></span></h3>
                <div class="clearfix"></div>
                <div class="table-responsive respotable">
                    <?php echo $arrFDCollectionHTML['cicoHTML'];?>
                </div>
              </div>
              <div class="clearfix"></div>
              </div>
			 <div class="item">
              <div class="colltable">
                <h3>Patient <span class="prepaymen"> Pre Payments</span></h3>
                <div class="clearfix"></div>
                <div class="table-responsive respotable">
                    <?php echo $arrFDCollectionHTML['prePaymentHTML'];?>
                </div>
              </div>
              <div class="clearfix"></div>
              </div>
		</div>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="whitebox toprejection">

    <div class="row">
        <div class="col-lg-7 col-md-7 col-sm-12">
            <div class="boxheader">
                <h2>
                    <figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/toprejection.png" alt=""/></figure>
                    <span <?php echo show_tooltip("Denied encounters of current month till today"); ?>>Top Rejection</span> </h2>
                  <div class="hdoption"><!--<img src="<?php echo $GLOBALS['webroot']; ?>/library/images/move_icon.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Move" /> --><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" /></div>
            </div>
            <div class="clearfix"></div>
            <div class="scroll-content mCustomScrollbar tablcont" style="min-height:190px;border:1px solid #E9E9E9;">
                <div class="table-responsive checkpatient respotable">
                    <table class="table table-bordered table-hover table-striped">
                        <?php
                        $topRejRows = 0;
                        $arrTopRej = $landBillAdmObj->get_top_rejection($startDate, $endDate);
                        $topRejHTML = $arrTopRej['topRejHTML'];
                        $topRejRows = $arrTopRej['topRejRows'];
                        echo $topRejHTML;
                        ?>
                        <tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-12">
            <div class="clearfix"></div>
            <div class="boxheader">
                <h2>Top 5 Tasks </h2>
<!--                <div class="hdoption"><img src="<?php //echo $GLOBALS['webroot']; ?>/library/images/refresh.jpg" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reload" /></div>-->
            </div>
            <div class="clearfix"></div>
            <div class="scroll-content mCustomScrollbar tablcont" style="min-height:190px;border:1px solid #E9E9E9;">
                <div class="table-responsive checkpatient respotable">
                    <table class="table table-bordered table-hover table-striped" style="overflow-x: hidden">
                        <?php echo $landBillAdmObj->get_top_tasks(); ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
  </div>
  <?php if( get_refill_direct_users() && isERPPortalEnabled() ) {
		include_once('medication_refill_req.php');
	} ?>
</div>
<div class="clearfix"></div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script>
function displayNoRecord(divId){
	$('#'+divId).html('<div style="text-align:center">No Record Exists</div>');
}

(function($){
	$(window).load(function(){

		$("#content-1").mCustomScrollbar({
			theme:"minimal"
		});

	});

	//SET HEIGHT OF TOP REJECTION BLOCK
	//$('.toprejection .checkpatient').css('height', '50px');

	var topRejRows='<?php echo $topRejRows;?>';
	if(topRejRows>1 && topRejRows<=9){
		topRejRows=topRejRows-1;
		rHeight=100;
		heightExceed= 36 * topRejRows;
		rHeight=rHeight+heightExceed;
		$('.toprejection .scroll-content').css('height', rHeight+'px');
	}else if(topRejRows>9){
		$('.toprejection .scroll-content').css('height', '385px');
	}else if(topRejRows<=0){
		$('.toprejection .scroll-content').css('height', '50px');
	}

})(jQuery);
</script>
<script>
$(function () {
$('[data-toggle="tooltip"]').tooltip()
})

//UNAPPLIED CHARGES CHART
'<?php if(sizeof($arrUnappliedCharges)>0){?>'
	multi_bar_chart('serial','unapplied_charge_div','<?php echo $column_graph_js_arr; ?>','<?php echo $value_field_graph_js_arr; ?>','','','', 'Charges','$');
'<?php }else {?>'
	displayNoRecord('unapplied_charge_div');
'<?php }?>'

//UNAPPLIED SUPERBILL CHART
'<?php if(sizeof($arrUnappliedSuperbills)>0){?>'
	pie_chart('pie','unapplied_superbills_div','<?php echo $arrUnappliedSuperbills_js_arr; ?>','$','','2');
'<?php }else {?>'
	displayNoRecord('unapplied_superbills_div');
'<?php }?>'


function hide_display(rowClass){
	$(".cicoTable .cicoSubPart").not('.'+rowClass).hide();
	$("."+rowClass).toggle('slow');
}
</script>

<script src="<?php echo $GLOBALS['webroot'];?>/library/js/owl.carousel.min.js"></script>
<script>
//pT portal --
var oPUF=[];
var erp_api_patient_portal = '<?php echo isERPPortalEnabled() ? "1" : "0"; ?>';

$(document).ready(function(){
$('.owl-carousel').owlCarousel({
	loop:true,
	margin:10,
	navText:false,
	nav:true,
	dots:false,
	responsive:{
		0:{
			items:1
		},
		630:{
			items:1
		},
		1000:{
			items:1
		},

	}
});
iportal_load_app_reqs();
});
    if(typeof(top.btn_show)!='undefined'){top.btn_show('DEF');}
</script>
</body>
</html>
