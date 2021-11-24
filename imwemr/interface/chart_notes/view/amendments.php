<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>

<!-- Bootstrap -->

<!-- Bootstrap -->
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/workview.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/wv_landing.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/drawing.css" rel="stylesheet" type="text/css">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<style>
	#dv_prv_amedments{  overflow:auto; }
</style>
<!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
   <script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script> 	
    <script>
	var zPath = "<?php echo $GLOBALS['rootdir'];?>";
	var elem_per_vo = "<?php echo $elem_per_vo;?>";
	var sess_pt = "<?php echo $patient_id; ?>";	
	var logged_user_type = "<?php echo $logged_user_type;?>";	
	var examName = "Amendments";
		
	function validate(){
		var valid = true;
		var msg = "Please fill in the following:-\n";  
		if($("#dos").val()=="" || $("#dos").val() == "-Select DOS-"){
			msg +="\n-DOS";
			valid = false;
		}
		
		if ( $("#amend_body").val() == "" )
		{
			msg +="\n-Note";
			//alert ( "Please enter note" );
			valid = false;
		}
		
		if(valid == false){			
			$("#frm_msg").html(msg);			
		}
		
		return valid;
	}
	
	
	function save_amend(flg_final){
		//validation	
		var flg_del = $("#op_modify").val();	
		if(typeof(flg_del)!="undefined"&&flg_del=="Delete"){
			//
			var x = true;
		}else{
			var x = validate();
		}	
		if(x){	
			var strsave=$("#frm_amend").serialize(); 
			strsave+="&savedby=ajax";
			if(flg_final=="1"){ strsave+="&finalize=1";}				
				$.post(zPath+"/chart_notes/saveCharts.php", strsave, function(data) {
					window.location.reload();
				});
		}//x
	}
	
	function op_modify(id, flg_del){
		if(typeof(flg_del)!="undefined" && flg_del=="1"){
			$("#op_modify").val("Delete");
			$("#editId").val(id);			
			save_amend();
			return;
		}else{
			//load id
			$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=GetAmendmentInfo&editId="+id, function(data) {
					if(data){						
						$("#amend_body").val(""+data.amend_body);
						$("#editId").val(""+data.amendment_id);
						$("#form_id option[value='"+data.form_id+"']").prop("selected", true).triggerHandler("change");
						$("#dos").val(""+data.dos);
						$("#addAmendModal").modal("show");
					}
				},"json");
		}
	}
	
	
	function setAmndDos(obj){
		var o = document.getElementById("dos");	
		if(o){
			o.value = obj.options[obj.selectedIndex].text;
		}
	 }
	
	$(document).ready(function () {	
		if($("#dv_prv_amedments").length>0){		
			$("#dv_prv_amedments").css({ "height":$(window).height() * 0.80+"px"});	
		}
	});
	
    </script>
</head>
<body>
<!--<form method="post" id="amedfrm1" name="amedfrm1" action="<?php echo $GLOBALS['webroot'];?>/chart_notes/onload_wv.php?elem_action=Amendments" >-->
<div id="dvamendments" class=" container-fluid">
<div class="whtbox ">



<div class="row" >
	<div class="col-sm-8" >
		<h4>Below are the previous amendment notes</h4>		
	</div>
<?php
if(!empty($allow_add_amend)){
?>
	<div class="col-sm-2" ><button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#addAmendModal">Add Amendment</button></div>
<?php }//end ?>	
</div>



<div id="dv_prv_amedments">
<?php echo $html_prev_amendments; ?>
</div>

</div>
</div>

<!-- add  -->
<!-- Modal -->
<div id="addAmendModal" class="modal fade" role="dialog">
<div class="modal-dialog">

	<!-- Modal content-->
	<div class="modal-content">
		<form id="frm_amend">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Amendment</h4>
		</div>
		
		<div class="modal-body">
		<div class="row" id="frm_msg"></div>
		<div class="row">
			<div class="form-group">
				<label>Previous DOS:</label>
				<select name="form_id" id="form_id" class="form-control" onchange="setAmndDos(this)" required><option value="">-Select-</option><?php echo $htm_prev_dos; ?></select>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label>Notes:</label>
				<textarea name="amend_body" id="amend_body" class="form-control" required></textarea>
				<input type="hidden" name="editId" id="editId" value="">
				<input type="hidden" name="op_modify" id="op_modify" value="">
				<input type="hidden" name="elem_saveForm" id="elem_saveForm" value="Amendments">
				<input type="hidden" id="dos" name="dos" value="" />
				<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>" />
				<input type="hidden" name="free_amend" id="free_amend" value="test">
			</div>
		</div>		
		
		</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-success"  onclick="save_amend();">Save</button>
		<button type="button" class="btn btn-success"  onclick="save_amend(1);">Finalize</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		</div>
		</form>
	</div>

</div>
</div>
<!-- add -->



<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>

<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>

</body>
</html>
