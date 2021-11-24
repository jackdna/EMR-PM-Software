<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:: imwemr ::</title>

<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvcss" rel="stylesheet"> 

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
    <![endif]-->

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsmain"></script>

<script>
	var zPath = "<?php echo $GLOBALS['rootdir'];?>";
	var elem_per_vo = '<?php echo $elem_per_vo;?>';
	function createFirstChart(val){
		if(val == 1){
            if(elem_per_vo==1){top.fAlert("You do not have permission to perform this action.",'',340);return false;}
            else{makeNewChart();}
		}else{
			$("#cr_first_chart_Modal").modal("hide");
			top.core_redirect_to("Tests","",1);
		}
	}
	
	$(document).ready(function () {
		$("#cr_first_chart_Modal").modal("show");
	});
</script>

</head>
<body>
	
<!-- Modal -->
<div id="cr_first_chart_Modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onClick="createFirstChart(0);">&times;</button>
        <h4 class="modal-title">Prompt patient's first chart</h4>
      </div>
      <div class="modal-body">
	<div class="row">
		<div class="col-sm-2 text-center">
			<h1><span class="glyphicon glyphicon-warning-sign"></span></h1> 
		</div>	
		<div class="col-sm-10">
			<h3>Do you want to create the First Chart Note of <span class="label label-danger"><?php echo $elem_curPatientName; ?></span></h3>
		</div>	
	</div>
      </div>
      <div class="modal-footer text-center">
	<button type="button" class="btn btn-success" onClick="createFirstChart(1);" >OK</button>
	<button type="button" class="btn btn-danger" onClick="createFirstChart(0);" >Close</button>
      </div>
    </div>

  </div>
</div>
<?php
//Chart Lock function
echo $htm_pt_chart_lock;
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjs"></script>
</body>
</html>