<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Send Fax Log</title>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<style>
	table tr{cursor:pointer}
</style>
<script type="text/javascript">
//======SEND CONSULT ID & POST DATA THROUGH AJAX============
function get_pdf(file_name){
	//$.ajax({
	//	type:'POST',
	//	data:'id='+consult_id,
	//	url:'send_fax_log.php?get_html=yes',
	//	success:function(response){
	//	//console.log(response);
	file_name = $.trim(file_name);
	var basePath = '<?php echo $pt_up_dir; ?>';
	if(file_name!==''){
		basePath=basePath.replace("var/www/html/","");
		window.open(basePath+'/'+file_name,'Fax_Log_Pdfs', "width=700,height=500,top=150,left=150,scrollbars=yes");
	}
	//	}
	//});
}
</script>
</head>
<body>
 <!--==========SEND FAX LOG POP UP HTML DATA WORKS==========-->
 <div class="panel panel-default">
  <div class="panel-heading">
	<label>Send Fax Log</label>
	<label style="margin-left:30%;"><?php echo $patientName; ?></label>
</div>
  <div class="panel-body">
 <table class="table table-bordered table-striped"> 
 <tr>
	<td >Date</td>
	<td >Fax To</td>
	<td >Section</td>
	<td >Template Name</td>
	<td >Transaction Id</td>
	<td >Fax Status</td>
</tr>

<?php
echo $str_send_fax_log;
?>

</table>
</div>
</div>
</body>
</html>