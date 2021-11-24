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
include_once(dirname(__FILE__)."/../../config/globals.php"); 
set_time_limit(0);
$st_start = $_GET['st'];
$st_last = $_GET['slt'];
$st_limit = $_GET['st_limit'];
$text_print = $_GET['text_print'];
$tot_pat_len = xss_rem($_GET['tot_pat_len']);
$st_chl_ids = xss_rem($_GET['st_chl_ids']);	/* Reject parameter with unwanted values - Security Values */
$chargeList = explode(',',$st_chl_ids);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<title><?php echo $title; ?></title>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
	var zPath = "<?php echo $GLOBALS['rootdir'];?>";
	var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
</head>

<?php
	if($_GET['total_mails']>0){
		$totalMailsSent=$_GET['total_mails_sent'];
		$totalMails=$_GET['total_mails'];
	}
	require_once("printStatements.php"); 
	if(count($pat_statements)>0){
		require_once('update_statement.php');

		//KEEP RECORD IF EMAIL IS SENT
		if(empty($affected_id)==false){
			$email_status= ($email_success=='1')? 'sent': 'failed';
			
			$qry="Update previous_statement SET email_sent='1',
			email_status='".$email_status."',
			email_operator='".$_SESSION['authId']."',
			email_date_time='".date('Y-m-d H:i:s')."' 
			WHERE previous_statement_id IN(".$affected_id.")";
			$r=imw_query($qry);
		}		
	}
?>

<script type="text/javascript">
var call_from='<?php echo $_GET['from'];?>';
if(call_from=="AR Worksheet"){
	var wtf_report=window.top;
}else{
	var wtf_report=window.parent.fmain;
}
var data_var = "&from=<?php echo $_GET['from'];?>";
data_var += "&total_mails_sent=<?php echo $totalMailsSent;?>";
data_var += "&total_mails=<?php echo $totalMails;?>";
data_var += "&mails_error=<?php echo $error;?>";
if(typeof(wtf_report.$("#groups").val())!="undefined" && wtf_report.$("#groups").val()!=null){data_var +="&grp_id="+wtf_report.$("#groups").val();}
if(typeof(wtf_report.$("#startLname").val())!="undefined"){data_var +="&startLname="+wtf_report.$("#startLname").val();}
if(typeof(wtf_report.$("#endLname").val())!="undefined"){data_var +="&endLname="+wtf_report.$("#endLname").val();}
if(wtf_report.$('#rePrint').is(':checked')==true){data_var +="&rePrint="+wtf_report.$("#rePrint").val();}
if(wtf_report.$("#fully_paid").is(':checked')==true){data_var +="&fully_paid="+wtf_report.$("#fully_paid").val();}
if(wtf_report.$("#text_print").is(':checked')==true){data_var +="&text_print="+wtf_report.$("#text_print").val();}
if(wtf_report.$("#send_email").is(':checked')==true){data_var +="&send_email="+wtf_report.$("#send_email").val();}
if(call_from=="AR Worksheet"){
	if(typeof(wtf_report.$("#force_cond").val())!="undefined"){data_var +="&force_cond="+wtf_report.$("#force_cond").val();}
}else{
	if(wtf_report.$("#force_cond").is(':checked')==true){data_var +="&force_cond="+wtf_report.$("#force_cond").val();}
}
if(wtf_report.$("#inc_chr_amt").is(':checked')==true){data_var +="&inc_chr_amt="+wtf_report.$("#inc_chr_amt").val();}
if(wtf_report.$("#show_min_amt").is(':checked')==true){data_var +="&show_min_amt="+wtf_report.$("#show_min_amt").val();}
if(wtf_report.$("#show_new_statements").is(':checked')==true){data_var +="&show_new_statements="+wtf_report.$("#show_new_statements").val();}
if(wtf_report.$("#exclude_sent_email").is(':checked')==true){data_var +="&exclude_sent_email="+wtf_report.$("#exclude_sent_email").val();}
if(typeof(wtf_report.$("#print_pdf").val())!="undefined"){data_var +="&print_pdf="+wtf_report.$("#print_pdf").val();}	
<?php if($st_start <= $tot_pat_len){?>
	$(document).ready(function(e){
		top.$('#st_process_id').html('');
		var st = <?php echo $st_last;?>;
		var slt = <?php echo $st_last+$st_limit;?>;
		var st_limit = <?php echo $st_limit;?>;
		var st_chl_ids=[];
		if(call_from=="AR Worksheet"){
			var elements = wtf_report.$('#html_data_div input.chk_box_css[name="pat_chk_arr[]"]:checkbox:checked');
		}else{
			var elements = wtf_report.$('#html_data_div input.chk_box_css[name="chargeList[]"]:checkbox:checked');
		}
		var tot_pat_len=elements.length;
		elements = elements.slice(st,slt);
		$(elements).each(function(){
			if(call_from=="AR Worksheet"){
				var ptId = $(this).val()
				wtf_report.$('input.chk_box_'+ptId+'_child:checkbox:checked').each(function(){
					var chlid = $(this).data().chlid;	
					st_chl_ids.push(chlid);
				});
			}else{
				st_chl_ids.push($(this).val());
			}
		});
		window.location.href = 'process_statements.php?st_chl_ids='+st_chl_ids+'&st='+st+'&slt='+slt+'&st_limit='+st_limit+'&tot_pat_len='+tot_pat_len+data_var;
	});
<?php }else{?>
	if(call_from=="AR Worksheet"){
		wtf_report.get_ar('statement_update');
	}else{
		loc = wtf_report.location.href;
		loc_path=loc.split('?');
		var txt_filePath='<?php echo $txt_filePath;?>';
		wtf_report.location.href = loc_path[0]+"?form_submitted=submit&txt_filePath="+txt_filePath+data_var;
	}
	top.removeMessi();
<?php }?>
</script>

<body class="whitebox">
<?php if($st_start <= $tot_pat_len){?>
	<h3>Processing <?php echo ($st_start+1);?> of <?php echo $tot_pat_len;?>.</h3>
    <p>Please do not close this dialog box until process complete.</p>
<?php }else{?>
	<h3>Process Complete</h3>
    <p>You may close this dialog box now.</p>
	<div class="row">
    	<div class="col-sm-12 text-right"><input type="button" class="btn btn-danger" value="Close" onClick="top.removeMessi();"></div>
    </div>
<?php
}
?>
</body>

</html>
