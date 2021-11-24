<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
session_start();
set_time_limit(300);
$flNme = 'pdf/label_pdf/label_final'.date('YmdHis').'.pdf';

?>
<!DOCTYPE html>
<html>
<head>
  <title>Labels Print</title>
  <meta name="viewport" content="width=device-width, maximum-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="stylesheet" href="./css/sfdc_header.css" type="text/css" />
  <link rel="stylesheet" href="./css/simpletree.css" type="text/css" />
  
  <link rel="stylesheet" type="text/css" href="./css/bootstrap.css" />
  <link rel="stylesheet" type="text/css" href="./css/style.css" />
  <link rel="stylesheet" type="text/css" href="./css/font-awesome.css" />
  <link rel="stylesheet" type="text/css" href="./css/bootstrap-select.css" />
  <link rel="stylesheet" type="text/css" href="./css/ion.calendar.css" />
  
  
  
  <script type="text/javascript" src="./js/jquery-1.11.3.js"></script>
  <script type="text/javascript" src="./js/bootstrap.js"></script>
		  
  <script type="text/javascript" src="./js/wufoo.js"></script>
  <script type="text/javascript" src="./js/jsFunction.js"></script>
  <script type="text/javascript" src="./js/cur_timedate.js"></script>
  <script type="text/javascript" src="./js/simpletreemenu.js"></script>
  <script type="text/javascript" src="./js/jscript.js"></script>
  <script type="text/javascript" src="./js/epost.js"></script>
  
  <script type="text/javascript" src="./js/moment.js"></script>
  <script type="text/javascript" src="./js/ion.calendar.js"></script>
  <!--<script type="text/javascript" src="./js/overflow.js"></script>-->
  <script type="text/javascript" src="./js/bootstrap-select.js"></script>
  
  <!--Dymo Label Printer Option-->
  <script type="text/javascript" src="./js/dymo/DYMO.Label.Framework.latest.js"></script>
  <script type="text/javascript" src="./js/dymo/dymo_common.js"></script>
  <!--End Dymo Label Printer Option-->
  <style>
  #noRecordModal{
  	max-width: 50%;
  }
  .box{
  	width: 99.4%;
    margin: 0 auto;
  }
  </style>
  <?php 
 	//print_r($_REQUEST);	
	$date12=$_REQUEST['date12'];
	$range=$_REQUEST['range'];
	$label=$_REQUEST['label'];
	$showAllApptStatus = $_REQUEST['showAllApptStatus'];
	$pt_stub_id_list_arr = $_REQUEST['pt_stub_id_list'];
	$pt_stub_id_list = implode(",",$pt_stub_id_list_arr);
	$pt_stub_id_list = ($pt_stub_id_list != 'all') ? explode(',',$pt_stub_id_list) : "all";
	include ("day_labelpop.php");
	
	$stub_tbl_group_row_arr_new = $stub_tbl_group_row_arr; //FROM day_labelpop.php
	if($_REQUEST['label']){
		// Do Nothing
	}else {
		foreach(glob("pdf/label_pdf/*.pdf") as $fleName) {
			if(file_exists($fleName)) {
				unlink($fleName);	
			}
			//echo $fleName;
		}
	}
	$range1 = ($range=="") ? 2 : $range;
?>
<script language="javascript">
var dymoLabelData = '';
var selectedPritner = '';
<?php if($is_dymo===true): ?>
dymoLabelData = <?php echo json_encode($dymoOptions,JSON_HEX_QUOT); ?>;
selectedPritner = '<?php echo $selected_dymo_printer; ?>';
$(document).ready(function(){
	selectDymoPrinter();
	printLabels(selectedPritner, dymoLabelData);
});
<?php endif; ?>
 function MM_swapImgRestore(obj,image_path) { //v3.0
	document.getElementById(obj).src =  image_path;
  }
  $(window).load(function() {
		bodySize();
	});
  
  $(window).resize(function(){
	 	//bodySize(470);
  });
  
  var bodySize = function(){
	  var h = 470;
	  window.resizeTo(550,h);
		/*var t = 50;
		var l = parseInt((screen.availWidth - window.outerWidth) / 2)
		window.moveTo(l,t);*/
		$("#content_div").css('height',(h-70)+'px' );
		
  }
  $(document).ready(function(){
	
	
	$('.selectpicker').on( 'change', function (e) {
		// take jquery val()s array for multiple
		//store the selected value
		var $selectedOption = $(this).find(':selected');
		var attending = $selectedOption.data('attending');
		var checkAttendings = [];
		$selectedOption.each(function(index){
			checkAttendings.push($(this).data('attending'));
		});
		if(typeof attending == 'undefined' || attending === '') return; 
		// take jquery vals() array for multiple
		var value = checkAttendings || [];
		
		// take the existing old data or create new
		var old = $(this).data('old') || [];
		//alert('OLD IS '+JSON.stringify(old));
		// take the old order or create a new
		var order = $(this).data('order') || [];
		//alert('ORDER'+JSON.stringify(order));
		// find the new items
		var newone = value.filter(function (val) {
			return old.indexOf(val) == -1;
		});
		//alert('NEW One'+JSON.stringify(newone));
		// find missing items
		var missing = old.filter(function (val) {
			return value.indexOf(val) == -1;
		});
		// console.log(missing,newone)
		// remove missing items from order array and add new ones to it
		$.each(missing, function (i, miss) {
			order.splice(order.indexOf(miss), 1);
		})
		$.each(newone, function (i, thing) {
			order.push(thing);
		})
	
		// save the order and old in data()
		$(this).data('old', value).data('order', order);
		
		if(JSON.stringify(order) == JSON.stringify([1,2]) || JSON.stringify(order) == JSON.stringify([0,2]))
		{
			attending = 2;
		}
		
		
		//if current selected array equal to [0,2] that means user trying to check TBD
		if(JSON.stringify(order) == JSON.stringify([1,0]) || JSON.stringify(order) == JSON.stringify([2,0]) )
		{
			attending = 0;
		}
	
		//if current selected array equal to [0,1]  or [2,1] that means user trying to check Attending date
		if(JSON.stringify(order) == JSON.stringify([0,1]) || JSON.stringify(order) == JSON.stringify([2,1])){
			attending = 1;
		}
		//alert(attending);
		console.log(order, 'attending :' + attending);
	
		if(attending == 1)
		{
	
			//$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-success');
			var tbd = $(this).find('[data-attending="2"]');
			var notAttending = $(this).find('[data-attending="0"]');
			if(tbd.is(':selected'))
			{
				tbd.prop('selected',false);
				
				$(this).selectpicker('refresh');
			}
	
			if(notAttending.is(':selected'))
			{
				notAttending.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			order.reverse();
		}
		 
		/*if(attending == 2)
		{
	
			$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-default');
	
			var coming = $(this).find('[data-attending="1"]');
			var not = $(this).find('[data-attending="0"]');
	
			if(coming.is(':selected'))
			{
				console.log('comming');
				coming.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			if(not.is(':selected'))
			{
				console.log('not comming');
				not.prop('selected',false);
				
				$(this).selectpicker('refresh');
			}
			order.reverse();
		}*/
		
		if(attending == 0 )
		{
			var screening_date_id 	= 0;
			
			//$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-danger');
	
			var yesComing	=	$(this).find('[data-attending="1"]');
			var tbd 		=	$(this).find('[data-attending="2"]');
			
			if(yesComing.is(':selected'))
			{
				yesComing.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			if(tbd.is(':selected'))
			{
				tbd.prop('selected',false);
			   
				$(this).selectpicker('refresh');
			}
			
			order.reverse();
			console.log(order, 'attending :'+attending);
		}
		
		if(typeof attending == 'undefined' || attending == '' )
		{	
			$(this).find('[data-attending="0"]').prop('selected',true);	
			$(this).selectpicker('refresh');
		}
		//order.length = 0;
	});
	
  });
  
  
</script>
</head>
<body>
<?php include('no_record.php'); ?>
<!-- Loader -->
<div class="loader">
	<span><b class='fa fa-spinner fa-pulse' ></b>&nbsp;Loading...</span>
</div>
<!-- Loader-->
<div class="box box-sizing">
  <div class="dialog box-sizing">
	<div class="content box-sizing" id="content_div">
	  <div class="header box-sizing text-left" style="padding:0 12px;">
		<h4>Labels Print</h4>
	  </div>
	  <form class="form-horizontal" action="label_popup.php" name="label1" method="post" style="padding:12px 12px 0 12px;">
		<input type="hidden" value="<?php echo  $_REQUEST['date12'];?>" name="date12">
		<input type="hidden" value="<?php echo  $_REQUEST['showAllApptStatus'];?>" name="showAllApptStatus">
		
		<div class="form-group">
		  <label for="range" class="col-lg-2 col-md-2 col-sm-4 col-xs-4 control-label">Labels Range:</label>
		  <div class="col-lg-10 col-md-10 col-sm-8 col-xs-8 inline_select">
			<select class="form-control selectpicker" name="range" id="range">
			  <?php
				  for($i=1;$i<30;$i++){
						if($i > 5) $i += 4; 
			  ?>
			  <option value="<?php echo $i; ?>" <?php if($i==$range1){echo 'selected';} ?>><?php echo $i; ?></option>
			  <?php } ?>
     	</select>
		  </div>
		</div>
        <div class="form-group">
		  <label for="range" class="col-lg-2 col-md-2 col-sm-4 col-xs-4 control-label">Select Patient(s):</label>
		  <div class="col-lg-9 col-md-9 col-sm-7 col-xs-7 ">
			<select class="form-control selectpicker" name="pt_stub_id_list[]" id="pt_stub_id_list" multiple="multiple" title="Select Patient(s)">
			  <option value="all" data-attending = "0" <?php if(in_array("all",$pt_stub_id_list_arr)){echo 'selected';} ?> >All Patients</option>
			  <?php
				  foreach($stub_tbl_group_row_arr_new as $stub_row) {
					  $pt_stub_id = $stub_row["stub_id"];
					  $pt_dob_format = $stub_row["patient_dob_format"];
					  $pt_name = trim($stub_row["patient_last_name"].", ".$stub_row["patient_first_name"]." ".$stub_row["patient_middle_name"]);
					  $pt_name_dob = $pt_name."(".$pt_dob_format.")";
			  ?>
			  		<option value="<?php echo $pt_stub_id; ?>" data-attending = "1" <?php if(in_array($pt_stub_id,$pt_stub_id_list_arr)){echo 'selected';} ?>><?php echo $pt_name_dob; ?></option>
			  <?php } ?>
			</select>
			</div>
		</div>
       
		
		<div class="form-group">
		  <label class="col-lg-2 col-md-2 col-sm-4 col-xs-4 control-label">Labels Type:</label>
		  <div class="col-lg-10 col-md-10 col-sm-8 col-xs-8 radio" style="padding-left:15px;">
			<label><input style="margin:0 5px 0 0;vertical-align:middle;float:none;" type="radio" name="label" value="large" <?php if($label=='large'){echo"checked";} ?> checked /> Large Labels</label>&nbsp;&nbsp; &nbsp;
			<label><input style="margin:0 5px 0 0;vertical-align:middle;float:none;" type="radio" name="label"  value="small" <?php if($label=='small'){echo"checked";}?> /> Small Labels</label>
		  </div>
		</div>
		
		<div class="form-group">
		  <label class="col-lg-2 col-md-2 col-sm-4 col-xs-4 control-label" for="sel_dymo">Use Dymo Printer:</label>
		  <div class="col-lg-10 col-md-10 col-sm-8 col-xs-8 radio" style="padding-left:15px;">
			<input style="margin-left: 4px;" type="checkbox" name="sel_dymo" id="sel_dymo" value="1" onChange="selectDymoPrinter();" <?php echo ($is_dymo===true)?'checked ':''; ?>/>
		  </div>
		</div>
		
		<div class="form-group" id="pinters_div" style="display: <?php echo ($is_dymo===true)?'block':'none'; ?>;">
		  <label class="col-lg-2 col-md-2 col-sm-4 col-xs-4 control-label" for="dymoPrintersList">Select Printer</label>
		  <div class="col-lg-9 col-md-9 col-sm-7 col-xs-7 " style="padding-left:15px;">
		  	<select class="form-control selectpicker" name="dymoPrintersList" id="dymoPrintersList" style="width: 100px;" title="" disabled></select>
		  </div>
		</div>
		
		<div class="form-group">
		  <label class="col-lg-2 col-md-2 col-sm-4 col-xs-4 control-label"></label>
		  <div class="col-lg-10 col-md-10 col-sm-8 col-xs-8">
			<a class="btn btn-primary" href="javascript:void(0)" id="PrintBtnNew" alt="Print" onClick="document.label1.submit();"><span class="fa fa-print"></span> Print</a>
		  </div>
		</div>
		<input type="hidden" value="1" name="show">
	  </form>
<?php if($_REQUEST['show']==1 && $stub_tbl_group_NumRow>0){?>
	  <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
		  <a style="margin-left:12px;" class="btn btn-primary" href="<?php echo $flNme;?>"><b class="fa fa-download"></b> Download Pdf Label File</a>
		</div>
	  </div>
<?php }?>
	</div>
  </div>
</div>
</body>
</html>
