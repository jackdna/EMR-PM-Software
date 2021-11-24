<?php
/*
 * File: header.php
 * Coded in PHP7
 * Purpose: Header information
 * Access Type: Include file
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
include_once(dirname(__FILE__)."/../../config/globals.php");
$show_tab="birds_eye_tab";
if($_REQUEST['tab_name']!=""){
	$show_tab=$_REQUEST['tab_name'];
}
$financial_cur_date=date('m-d-Y');
?>
<!DOCTYPE html>
<html>
<head>

<title>Financial Dashboard</title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width= device-width, initial-scale=1,maximum-scale=1,zoom=1">
   
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/bootstrap-multiselect.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/messi/messi.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']?>/fd/library/css/common.css">
    
	<!-- SCript -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/moment.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/bootstrap-multiselect.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/daterangepicker.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/amcharts/amcharts.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/amcharts/pie.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/amcharts/serial.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/amcharts/themes/light.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/amcharts/responsive.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/common.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/messi/messi.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/fd/library/js/buttons.js"></script>

    <script type="text/javascript">
    var dformat = "<?php echo inter_date_format(); ?>";
    var date_format = dformat.toUpperCase();

	function refresh_data(){
		var div_data="<div style='text-align:center;padding-top:40px;'><img class='g_home' src='../images/BiColorCircle.gif'></div><div style='text-align:center;padding-top:40px;'><strong>Please wait while your data refresh is processed.</strong></div>";
		fancyModal(div_data,"300px","200px");
		$('#ref_iframe').attr('src', 'refresh_data.php');
		$('#show_financial_id').html('<?php echo $financial_cur_date; ?>');
	}
	function cb(start, end) {
		//alert(start);
		$('.date-pick span#date_display').html(start.format(date_format) + ' to ' + end.format(date_format));
		$('#start_date').val(start.format(date_format));
		$('#end_date').val(end.format(date_format));
	}
	$(document).ready(function(e) {
       /* $('.date-pick').datetimepicker({
			format:"MM-DD-YYYY"
		});*/
		cb(moment().subtract('', 'days'), moment());
	
		$('.date-pick').daterangepicker({
			 ranges: {
			   'Today': [moment(), moment()],
			   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			   'This Month': [moment().startOf('month'), moment().endOf('month')],
			   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
			   'This Year': [moment().startOf('year'), moment().endOf('year')]
			}
		}, cb);
		$(".multi_drop").multiselect({includeSelectAllOption: true,enableFiltering: true,enableCaseInsensitiveFiltering: true,enableFullValueFiltering: true,maxHeight: 240}); <!--  Btn-group width   -->
	});
</script>
</head>
<body class="" id="md_white">
<!--<div class="messi-modal" style="width: 1552px; height: 789px; z-index: 99999; opacity: 0.2;"></div>-->
<div id="div_loading_image" class="loading" style="display:none;">
    <div id="div_loading_text" style="width:300px;position:relative;padding-top:50px;display:none;text-align:center;"></div>
</div>
<script type="text/javascript">
	//show_loading_image('show');
</script>
<Div class="main_main_wrapper">
<Div class="main_wrapper">
	<Div class="header_wrap">
    	<Div class="container-fluid">
          	<Div class="col-md-7 col-sm-7 col-xs-7 col-lg-7">
               <Div class="left_header">
                	 <div class="custom_dash_label">
                     	<!--<img class="" src="../images/dashboard_white.png">-->
                    	<span class="single_header_label"> Financial Dashboard </span>
                     </div>
                    	
                </Div>	
            </Div>
            
            <!--- LEft PArt Ends -->
            <Div class="col-md-5 col-sm-5 col-xs-5 col-lg-5">
                <Div class="right_header pull-right text-right">
                	<a class="rob_btn btn_custom btn_refresh" href="javascript:void(0);" onClick="refresh_data();">
                    	Refresh
                    </a>
                    <a class="rob_btn btn_custom btn_sign_out" href="<?php echo $GLOBALS['webroot']."/fd/?act=logout" ?>">
                    	Logout
                    </a>
                </Div>	
            </Div>
            <!--- Right Part ENds -->  
            <!--- CENTER PART -->
            <!--<Div class="centered_header text-center">
          		 <img class="" src="../images/dashboard_white.png"> <img class="g_home" src="../images/dashboard_green.png"> 
                 <Div class="clearfix"></Div>
                 <h4 class="rob"> FINANCIAL DASHBOARD </h4>
            </Div>-->	
            <!--- CENTER PART -->
        </Div>     
    </Div>	
    <Div class="middle_wrap">
    	<!--<Div class="container-fluid">
   			<Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 rob">
        	   <ul class=" list-inline list-unstyled custom_breadcrumbs">
                  <li>  <a href="javascript:void(0)"> HOME </a><span> / </span>   </li>
		          <li>  <a href="javascript:void(0)"> DASHBOARD </a><span> / </span>   </li>
                  <li>  <a href="javascript:void(0)"> BIRDSEYE VIEW </a></li>
               </ul> 	
            </Div>	
        </Div>-->
        <Div class="container-fluid padding_0">
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 padding_0">
         		<div class="top_middle hidden-sm tabs_wrap" id="sty_top_ul">
                    <ul class="nav nav-justified nav-tabs imp_tab_borders">
                    	<li <?php if($show_tab=="birds_eye_tab"){ echo 'class="active"';}?>> 
                        	<a href="index.php?tab_name=birds_eye_tab" class="other_tab">
                            	<span class="fa fa-eye"> </span> Analytics
                            </a> 
                        </li>
                       	<li <?php if($show_tab=="charges_tab"){ echo 'class="active"';}?>>
                        	<a href="index.php?tab_name=charges_tab" class="other_tab">
                            	<span class="fa fa-dollar"> </span>  Charges   
                            </a> 
                        </li>
                         <li <?php if($show_tab=="payments_tab"){ echo 'class="active"';}?>> 
                        	<a href="index.php?tab_name=payments_tab" class="other_tab">
                            	<span class="fa fa-dollar"> </span> Receipts
                            </a>
                        </li>
                    	<li <?php if($show_tab=="ledger_tab"){ echo 'class="active"';}?>> 
                        	<a href="index.php?tab_name=ledger_tab" class="other_tab">
                            	<span class="fa fa-book"> </span> 	Ledger
                            </a>
                        </li>
                        <li <?php if($show_tab=="physicians_tab"){ echo 'class="active"';}?>> 
                        	<a href="index.php?tab_name=physicians_tab" class="other_tab">
                            	<span class="fa fa-user-md"> </span> Physicians
                            </a>
                        </li>
                        <li <?php if($show_tab=="referring_tab"){ echo 'class="active"';}?>> 
                        	<a href="index.php?tab_name=referring_tab" class="other_tab ref_tab">
                            	<span class="fa fa-user-md"> </span> Referring Physician
                            </a>
                        </li>
                        <li <?php if($show_tab=="sch_tab"){ echo 'class="active"';}?>> 
                        	<a href="index.php?tab_name=sch_tab" class="other_tab sch_tab">
                            	<span class="fa fa-calendar"> </span> Scheduled Appointments 	
                            </a>
                        </li>
                        <li <?php if($show_tab=="trends_tab"){ echo 'class="active"';}?>> 
                        	<a href="index.php?tab_name=trends_tab" class="other_tab">
                            	<span class="fa fa-bar-chart"> </span> Trends	
                            </a>
                        </li>
                    </ul>
                </Div>
               
                  <Div class="row">
                	<div class="visible-sm col-sm-3 padding_right">
                    	<div class="sidebar_nav">
                             <ul class="nav nav-tabs nav-pills nav-stacked">
                                 <li <?php if($show_tab=="birds_eye_tab"){ echo 'class="active"';}?>> 
                                    <a href="index.php?tab_name=birds_eye_tab" class="other_tab">
                                        <span class="fa fa-eye"> </span> Analytics
                                    </a> 
                                </li>
                                <li <?php if($show_tab=="charges_tab"){ echo 'class="active"';}?>>
                                    <a href="index.php?tab_name=charges_tab" class="other_tab">
                                        <span class="fa fa-dollar"> </span>  Charges   
                                    </a> 
                                </li>
                                 <li <?php if($show_tab=="payments_tab"){ echo 'class="active"';}?>> 
                                    <a href="index.php?tab_name=payments_tab" class="other_tab">
                                        <span class="fa fa-dollar"> </span> Receipts
                                    </a>
                                </li>
                                <li <?php if($show_tab=="ledger_tab"){ echo 'class="active"';}?>> 
                                    <a href="index.php?tab_name=ledger_tab" class="other_tab">
                                        <span class="fa fa-book"> </span> 	Ledger
                                    </a>
                                </li>
                                <li <?php if($show_tab=="physicians_tab"){ echo 'class="active"';}?>> 
                                    <a href="index.php?tab_name=physicians_tab" class="other_tab">
                                        <span class="fa fa-user-md"> </span> Physicians
                                    </a>
                                </li>
                                <li <?php if($show_tab=="referring_tab"){ echo 'class="active"';}?>> 
                                    <a href="index.php?tab_name=referring_tab" class="other_tab ref_tab">
                                        <span class="fa fa-user-md"> </span> Referring Physician
                                    </a>
                                </li>
                                <li <?php if($show_tab=="sch_tab"){ echo 'class="active"';}?>> 
                                    <a href="index.php?tab_name=sch_tab" class="other_tab sch_tab">
                                        <span class="fa fa-calendar"> </span> Scheduled Appointments 	
                                    </a>
                                </li>
                                <li <?php if($show_tab=="trends_tab"){ echo 'class="active"';}?>> 
                                    <a href="index.php?tab_name=trends_tab" class="other_tab">
                                        <span class="fa fa-bar-chart"> </span> Trends	
                                    </a>
                                </li>
                            </ul>
                        </div>	    
                    </div>