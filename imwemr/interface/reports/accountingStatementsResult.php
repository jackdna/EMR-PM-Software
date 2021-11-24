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
//---- Display of Reports in Credit Reports ---------
set_time_limit(0);
include_once(dirname(__FILE__)."/../../config/globals.php");
if(isERPPortalEnabled()) {
	include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
	$OBJRabbitmqExchange = new Rabbitmq_exchange();
}
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
include_once(dirname(__FILE__)."/../accounting/accounting_session.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");

use PHPMailer\PHPMailer;

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<title><?php echo $title; ?></title>
<link href="../../library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
<link href="../../library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="../../library/css/common.css" type="text/css" rel="stylesheet">
<link href="../../library/css/core.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="../../library/messi/messi.css">
<?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
<?php } ?>
<script type="text/javascript" src="../../library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="../../library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="../../library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../library/js/common.js"></script>
<script type="text/javascript" src="../../library/messi/messi.js"></script>
<?php
$curDate = date('m-d-Y');
$buttons="";

//--- Print previous statements for accounting -----
$imp_g_code_proc=implode("','",$arr_g_code_proc);
if(count($selectpatient)>0){
	$selectpatientStr = join(',',$selectpatient);
	$affected_id=$selectpatientStr;
	$qry = imw_query("select previous_statement.created_date,previous_statement_detail.statement_data,
			previous_statement_detail.statement_txt_data,previous_statement.patient_id from previous_statement
			join previous_statement_detail on previous_statement_detail.previous_statement_id = previous_statement.previous_statement_id 
			where previous_statement.statement_acc_status=1 and previous_statement.previous_statement_id in ($selectpatientStr)");
	$printDataArr = array();
	while($qryRes=imw_fetch_array($qry)){
		$printDataArr[]	= $qryRes['statement_data'];
		$printDataTxtArr[]	= $qryRes['statement_txt_data'];
	}
	if(count($printDataArr)>0){
		$printData = join(' ',$printDataArr);
		$printDataTxt = join("\r\n",$printDataTxtArr);
		$save_html = $printData;
		if($text_print!="" && $printDataTxt!=""){
			//--- CREATE TEXT FILE PRINTING -----
			$filename = 'statement_report.txt';
			$txt_filePath=write_html($printDataTxt,$filename);
			header("location: downloadtxt.php?txt_filePath=$txt_filePath");		
		}else{
			#mail send to patient
			if($emailStatement)
			{
				
				$queryEmailCheck=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1")or die(imw_error());
				if(imw_num_rows($queryEmailCheck)>=1)
				{
					$dEmailCheck=imw_fetch_object($queryEmailCheck);
					$groupEmailConfig['email']=$dEmailCheck->config_email;
					$groupEmailConfig['pwd']=$dEmailCheck->config_pwd;
					$groupEmailConfig['host']=$dEmailCheck->config_host;
					$groupEmailConfig['header']=$dEmailCheck->config_header;
					$groupEmailConfig['footer']=$dEmailCheck->config_footer;
					$groupEmailConfig['port']=$dEmailCheck->config_port;
				}
		
				if(!$groupEmailConfig['email'] || !$groupEmailConfig['pwd'] || !$groupEmailConfig['host']){
				?>  	<script>
							top.fAlert("Email is not configured");
							top.show_loading_image('hide');
						</script>
				<?php
				} else {
					//Create a new PHPMailer instance
					$mail = new PHPMailer\PHPMailer;
					//Tell PHPMailer to use SMTP
					$mail->isSMTP();
					//Enable SMTP debugging
					// 0 = off (for production use)
					// 1 = client messages
					// 2 = client and server messages
					$mail->SMTPDebug = 2;
					//Ask for HTML-friendly debug output
					$mail->Debugoutput = 'html';
					//Set the hostname of the mail server
					$mail->Host = $groupEmailConfig['host'];
					//Set the SMTP port number - likely to be 25, 465 or 587
					$mail->Port = $groupEmailConfig['port'];
					//Whether to use SMTP authentication
					$mail->SMTPAuth = true;
					// SMTP connection will not close after each email sent, reduces SMTP overhead
					$mail->SMTPKeepAlive = true;
					//Username to use for SMTP authentication
					$mail->Username = $groupEmailConfig['email'];
					//Password to use for SMTP authentication
					$mail->Password = $groupEmailConfig['pwd'];
					//Set who the message is to be sent from
					$mail->setFrom($groupEmailConfig['email'], '');
					//Set an alternative reply-to address
					//$mail->addReplyTo('replyto@example.com', 'First Last');
					//Set the subject line
					$mail->Subject = 'imwemr Account Statement';
			
					//get patient detail
					$pQuery=imw_query("select lname,mname,fname, email from patient_data where id='".$_SESSION['patient']."'");
					$pData=imw_fetch_array($pQuery);
					
					$divData = $save_html;
					$pt_email=$pData['email'];
					// echo "<script type='text/javascript'>alert('".$totalMailsSent."/".$totalMails." emails sent successfully');<//script>";

					if( !empty($pt_email) ){
						$respartyName=ucwords(trim($pData['lname'].", ".$pData['fname']." ".$pData['mname']));
						include('emailStatements.php');

						if($totalMails != ""){
							$totalSentMails = $totalMailsSent;
							$totalEmails = $totalMails;
							$processEmail = 'true';
							$_SESSION['processEmailState'] = true;
						} else {
							$processEmail = 'false';	
							$_SESSION['processEmailState'] = false;
						}
					} else{
						$processEmail = 'false';
						$_SESSION['processEmailState'] = false;
					}
				}
			}
			else
			{
				$filePath=write_html(html_entity_decode($save_html));
				echo "<script type='text/javascript'>top.JS_WEB_ROOT_PATH = '".$GLOBALS['webroot']."';html_to_pdf('".$filePath."','p','','','','html_to_pdf_reports');</script>";
			}
		}
	}
}
//--- Search Certeria -----------
if($Submit){
	//----- Get Patient Id From Last Name Search ---------  
	if($startLname){
		$qry = "select id,lname from patient_data where lname > '$startLname'";
		if($endLname){
			$qry .= " and lname < '$endLname' or lname like '$endLname%'";
		}
		if($patientId){
			$qry .= " or id = '$patientId'";
		}
		$qry .= "order by lname,fname";
		$qry_exe = imw_query($qry);
		while($patientIdRes=imw_fetch_array($qry_exe)){
			$patId[] = $patientIdRes['id'];
		}
		if(count($patId)>0){
			$patientId = implode(",",$patId);
		}
	}	
	
	$qry = imw_query("select Statement_Elapsed,fully_paid,statement_base,vip_bill_not_pat,full_enc from copay_policies where policies_id = '1'");
	$res=imw_fetch_array($qry);
	$Statement_Elapsed = $res['Statement_Elapsed'];	
	$fully_paid = $res['fully_paid'];
	$statement_base = $res['statement_base'];
	$vip_bill_not_pat = $res['vip_bill_not_pat'];
	$full_enc = $res['full_enc'];
	$Statement_Elapsed_date = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-$Statement_Elapsed,date('Y')));
	//--- Get Previous Statements ---------
	if($pevious){
		$qry = "select patient_data.lname,patient_data.fname, patient_data.mname,
				date_format(previous_statement.created_date,'".get_sql_date_format()."') as createdDate,patient_data.id	,
				previous_statement.previous_statement_id from previous_statement 		
				join patient_data on patient_data.id = previous_statement.patient_id
				where previous_statement.statement_acc_status=1 and previous_statement.statement_satus  = '0'";
		if($patientId){
			$qry .= " and previous_statement.patient_id = '$patientId'";
		}
		$qry .= " order by previous_statement.created_date desc,
				previous_statement.previous_statement_id desc,
				patient_data.lname, patient_data.fname";
		$preQryExe = imw_query($qry);
		while($preQryRes=imw_fetch_array($preQryExe)){	
			$createdDate = $preQryRes['createdDate'];
			$id = $preQryRes['id'];
			$previous_statement_id = $preQryRes['previous_statement_id'];
			$patient_name = $preQryRes['lname'].', ';
			$patient_name .= $preQryRes['fname'].' ';
			$patient_name .= $preQryRes['mname'];
			if($patient_name[0] == ','){
				$patient_name = preg_replace("/, /","",$patient_name);
			}
			$patient_name = ucwords(trim($patient_name));
			$res_data .= "<tr>
					<td>
						<div class='checkbox'>
							<input type='checkbox' value='$previous_statement_id' name='selectpatient[]' id='id_$previous_statement_id'>
							<label for='id_$previous_statement_id'></label>
						</div>
					</td>
					<td>
						$patient_name - $id
					</td>
					<td>
						$createdDate
					</td>
				</tr>";
		}	
		if(imw_num_rows($preQryExe)>0){
			$data ="<tr class='purple_bar'>
					<td colspan='2'>Previous Statements</td>
					<td>
						<div class='checkbox'>
							<input type='checkbox' name='text_print' id='text_print'/>
							<label for='text_print'>Text File</label>
						</div>
					</td>
				</tr>
				<tr class='grythead'>
					<th>
						<div class='checkbox'>
							<input type='checkbox' name='selectAll' id='selectAll' onclick='selAll1(this.checked);'/>
							<label for='selectAll'></label>
						</div>
					</th>
					<th>Patient - ID</th>
					<th>Created Date</th>		
				</tr>
				$res_data";
		}
		else{
			$data = "<tr><td class='text-center lead'>There is no previous statement for selected patient</td></tr>";
		}
	}
	else{
		require_once('accountingStatement.php');
		
		if($Submit == 'PrintStatement' || $emailStatement>0){
			require_once('printStatements.php'); 
		}
	}
}
?>
<script type="text/javascript">
	function getStatement(){
		if($("#force_print").length>0){
			if($('#force_print').is(':checked')){
				var force_cond="yes";
			}else{
				var force_cond="no";
			}
		}else{
			var force_cond="no";
		}
		var val = '<?php print $_SESSION['patient']; ?>';
		window.location.href = 'accountingStatementsResult.php?Submit=GetReport&pre=yes&patientId='+val+'&force_cond='+force_cond;
	}
	function checkRes1(){
		$('#emailStatement').val(0);
		var obj = document.getElementsByName("selectpatient[]");
		var msg = '';
		var msgflag = false;		
		for(i=0;i<obj.length;i++){
			if(obj[i].checked == false && msgflag == false){
				msg = "Please select any record to print Statement.";
			}
			else{
				msg = '';
				msgflag = true;
				break;
			}
		}
		if(msg){
			top.fAlert(msg);
		}
		else{
			document.statementsResultFrm.submit();
		}
	}
	
	function send_email(){
		$('#emailStatement').val(1);
		var userHippa=$('#hipaa_email').val();
		var user_email=$('#email').val();
		if(user_email=='')
		{
			top.fAlert("Patient have not register any Email ID");
			return false;
		}
		
		var obj = document.getElementsByName("selectpatient[]");
		var msg = '';
		var msgflag = false;		
		for(i=0;i<obj.length;i++){
			if(obj[i].checked == false && msgflag == false){
				msg = "Please select any record to email Statement.";
			}
			else{
				msg = '';
				msgflag = true;
				break;
			}
		}
		
		if(msg){
			top.fAlert(msg);
		}
		else{
			document.statementsResultFrm.submit();
		}
	}
	
	function send_email1(){
		$('#emailStatement').val(1);
		//if(opener.document.getElementById("statement")){
//			opener.document.getElementById("statement").className = 'dff_buttonog';
//		}
		var obj = document.getElementsByName("chargeList[]");
		var msg = '';
		var msgflag = false;		
		for(i=0;i<obj.length;i++){
			if(obj[i].checked == false && msgflag == false){
				msg = "Please select any record to email Statement.";
			}
			else{
				msg = '';
				msgflag = true;
				break;
			}
		}
		if(msg){
			top.fAlert(msg);
		}
		else{
			document.getElementById("Submit").value = "PrintStatement";
			document.statementsResultFrm.submit();
		}
	}
	
	function checkRes(){
		$('#emailStatement').val(0);
		//if(opener.$('#statement').length>0){
		//	opener.$('#statement').addClass("active");
		//}
		var obj = document.getElementsByName("chargeList[]");

		var msg = '';
		var msgflag = false;		
		for(i=0;i<obj.length;i++){
			if(obj[i].checked == false && msgflag == false){
				msg = "Please select any record to print Statement.";
			}
			else{
				msg = '';
				msgflag = true;
				break;
			}
		}
		if(msg){
			top.fAlert(msg);
		}
		else{
			if($("#printStatements").length>0){
				$("#printStatements").prop("disabled",true);
			}
			$("#Submit").val("PrintStatement");
			document.statementsResultFrm.submit();
		}
	}
	
	
	function selAll1(val){
		var obj = document.getElementsByName("selectpatient[]");
		for(i=0;i<obj.length;i++){
			obj[i].checked = val;
		}
	}
	
	function selAll(val){
		var obj = document.getElementsByName("chargeList[]");
		for(i=0;i<obj.length;i++){
			obj[i].checked = val;
		}
	}
	
$(document).ready(function(e) {
	<?php if($customMsg != ""){?>
		top.fAlert('<?php echo $customMsg;?>');
	<?php }?>
});
</script>
</head>
<body>
<div class="mainwhtbox">
<div class="table-responsive" style="height:570px; overflow:auto; width:100%;">
<?php
if( isset($_GET['processEmail']) ) {
	if($_GET['processEmail'] == 'false') {
		?>
		<script>top.fAlert("Email is not configured");</script>
		<?php
	} elseif($_GET['processEmail'] == 'true') {
		$tsm = (isset($_GET['totalSentMails']) && !empty($_GET['totalSentMails']) && $_GET['totalSentMails'] > 0) ? $_GET['totalSentMails'] : 0 ;
		$te = (isset($_GET['totalEmails']) && !empty($_GET['totalEmails']) && $_GET['totalEmails'] > 0) ? $_GET['totalEmails'] : 0 ;

		if( isset($_SESSION['processEmailState']) && $_SESSION['processEmailState'] == true ):
		?>
			<script>top.fAlert('<?php echo $tsm.'/'.$te; ?> email(s) are sent successfully');</script>
		<?php
			unset($_SESSION['processEmailState']); // unset session
		endif;
	}
}

//get patient detail
$pQuery=imw_query("select hipaa_email, email from patient_data where id='".$_SESSION['patient']."'");
$pData=imw_fetch_array($pQuery);
$hipaa_email=$pData['hipaa_email'];
$email=$pData['email'];

$processEmail = $totalSentMails = $totalEmails = '';
if($Submit == 'PrintStatement' || $emailStatement>0){
	$st_srh_frm="Accounting";
	$html = $divData;
	//---- Save Statements As Per date and Patient Id -----
	if(count($pat_statements)>0){
		
		if($emailStatement>0){
			$queryEmailCheck2=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1")or die(imw_error());
			if(imw_num_rows($queryEmailCheck2)>=1)
			{
				$dEmailCheck=imw_fetch_object($queryEmailCheck2);
				$groupEmailConfig['email']=$dEmailCheck->config_email;
				$groupEmailConfig['pwd']=$dEmailCheck->config_pwd;
				$groupEmailConfig['host']=$dEmailCheck->config_host;
				$groupEmailConfig['header']=$dEmailCheck->config_header;
				$groupEmailConfig['footer']=$dEmailCheck->config_footer;
				$groupEmailConfig['port']=$dEmailCheck->config_port;
			}
	
			if(
				!empty($groupEmailConfig['email']) && 
				!empty($groupEmailConfig['pwd']) && 
				!empty($groupEmailConfig['host']) && 
				!empty($groupEmailConfig['port'])
			){
				$processEmail = 'true';
				$_SESSION['processEmailState'] = true;
				$pt_email=$pData['email'];
				$respartyName=ucwords(trim($pData['lname'].", ".$pData['fname']." ".$pData['mname']));
				include('emailStatements.php');
			} else{
				$processEmail = 'false';
				$_SESSION['processEmailState'] = false;
			}
		}
		
		if($text_print){
			$srh_text_print=1;
		}else{
			$srh_text_print=0;
		}
		/*$search_option_arr['from']=$st_srh_frm;
		$search_option_arr['grp_id']="";
		$search_option_arr['startLname']="";
		$search_option_arr['endLname']="";
		$search_option_arr['rePrint']="";
		$search_option_arr['fully_paid']="";
		$search_option_arr['text_print']=$srh_text_print;
		$search_option_arr['force_cond']=$force_cond;
		$search_option_arr['inc_chr_amt']="";
		$search_option_arr['show_min_amt']="";
		$search_option_arr['show_new_statements']="";
		$search_options_serz=serialize($search_option_arr);*/
	}
	
	//--- CREATE HTML FILE FOR PDF PRINTING ----
	if($emailStatement>0){
		if($totalMails != ""){
			// echo "<script type='text/javascript'>alert('".$totalMailsSent."/".$totalMails." emails sent successfully');<//script>";
			$totalSentMails = $totalMailsSent;
			$totalEmails = $totalMails;
			$processEmail = 'true';
			$_SESSION['processEmailState'] = true;
		} else {
			$processEmail = 'false';	
			$_SESSION['processEmailState'] = false;
		}
	$txt_filePath='';
	}else if($text_print>0){
		//header("location: downloadtxt.php?txt_filePath=$txt_filePath");			
	}else{
		$filePath=write_html($html);
		echo "<script type='text/javascript'>top.JS_WEB_ROOT_PATH = '".$GLOBALS['webroot']."';html_to_pdf('".$filePath."','p');</script>";
		$txt_filePath='';
	}
	if(count($pat_statements)>0){
		$update_pat_statement="yes";
		$_REQUEST['from']=$st_srh_frm;
		$_REQUEST['text_print']=$srh_text_print;
		$_REQUEST['force_cond']=$force_cond;
		require_once('update_statement.php');
	}
	
	//KEEP RECORD IF EMAIL IS SENT
	if($emailStatement>0 && empty($affected_id)==false){
		$email_status= ($email_success=='1')? 'sent': 'failed';
		
		$qry="Update previous_statement SET email_sent='1',
		email_status='".$email_status."',
		email_operator='".$_SESSION['authId']."',
		email_date_time='".date('Y-m-d H:i:s')."' 
		WHERE previous_statement_id IN(".$affected_id.")";
		$r=imw_query($qry);
	}

	$loc_href='accountingStatementsResult.php?Submit=Get Report&pevious=yes&patientId='.$_SESSION['patient'].'&txt_filePath='.$txt_filePath.'&processEmail='.$processEmail.'&totalSentMails='.$totalSentMails.'&totalEmails='.$totalEmails;
	echo "<script type='text/javascript'>window.location.href='".$loc_href."'</script>";
}


?>
<form name="statementsResultFrm" action="" method="post">

<table class="table table-bordered table-hover table-striped">
    <input type="hidden" name="dayReport" value="<?php print $dayReport; ?>">
    <input type="hidden" name="Start_date" value="<?php print $Start_date; ?>">
    <input type="hidden" name="End_date" value="<?php print $End_date; ?>">
    <input type="hidden" name="patientId" value="<?php print $patientId; ?>">
    <input type="hidden" name="chargeListId">
    <input type="hidden" name="rePrint">
    <input type="hidden" name="startLname">
    <input type="hidden" name="endLname">
    <input type="hidden" name="email" id="email" value="<?php echo $email?>">
    <input type="hidden" name="hipaa_email" id="hipaa_email" value="<?php echo $hipaa_email?>">
	<input type="hidden" name="emailStatement" id="emailStatement" value="<? echo $emailStatement;?>">
	<input type="hidden" name="affected_id" id="affected_id" value="<?php echo $affected_id?>">
    <input type="hidden" name="Submit" id="Submit" value="<?php print $Submit; ?>">
<?php 
	print $data;
?>	
</table>
<iframe src="" id="txt_iframe" name="txt_iframe" height="0" width="0" allowtransparency="1" class="hide"></iframe>
</form>
</div>
</div>
<footer>
	<div class="text-center" id="module_buttons">
		<?php if($buttons!=""){
		 echo $buttons;}else{
		 if($pevious){?>
			<?php if(imw_num_rows($preQryExe)>0){?>
			<input type="button" id="viewStatements" class="btn btn-success" value="View Statements" onClick="checkRes1();" name="viewStatements">
			<?php } ?>
			<input type="button" id="printStatements" class="btn btn-success" value="Create Statement" onClick="getStatement();" name="printStatements">
			<input type="button" id="emailStatements" class="btn btn-success" value="Email Statements to Pt." name="emailStatements" onClick="send_email();">
			<input type="button" id="close" class="btn btn-danger" value="Close" name="close" onClick="window.close();">
		<?php }}?>	
	</div>
</footer>
<script type="text/javascript">
<?php if(empty($_REQUEST['Submit']) === false && $_REQUEST['txt_filePath']!="" ){ ?>
	$("#txt_iframe").attr('src','<?php echo "downloadtxt.php?txt_filePath=".$_REQUEST['txt_filePath'];?>');
<?php } ?>
</script> 
</body>
</html>