<?php
$without_pat = "yes";
require_once("reports_header.php");
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once("../../library/classes/SaveFile.php");

use PHPMailer\PHPMailer;

$dbtemp_name="Previous Statement";
$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;
$phpDateFormat = phpDateFormat();

$imp_g_code_proc=implode("','",$arr_g_code_proc);
if($_REQUEST['grp_id']!=""){
	$grp_id=$_REQUEST['grp_id'];
	$_REQUEST['groups']= explode(',',$grp_id);
}else{
	$grp_id=implode(',',$_REQUEST['groups']);
}

if(count($_REQUEST['selectpatient']) > 0){
	$email_config=$totalMails=$totalMailsSent="";
	$printDataArr = $printOldDataArr = array();
	
	if($_REQUEST['print_pdf']!=""){
		$selectpatientStr = join(',',$_REQUEST['selectpatient']);
		$qry = "select previous_statement.previous_statement_id, previous_statement.created_date,previous_statement_detail.statement_data,
				previous_statement_detail.statement_txt_data,previous_statement.patient_id from previous_statement
				join previous_statement_detail on previous_statement_detail.previous_statement_id = previous_statement.previous_statement_id 
				where previous_statement.statement_acc_status=1 and previous_statement.previous_statement_id in ($selectpatientStr)";
		$qryres=imw_query($qry);
		while($qryrow=imw_fetch_array($qryres)){
			if($qryrow['created_date'] < '2010-06-01'){			
				if($send_email)
				{
					$pt_id=$qryrow['patient_id'];
					$pt_id_arr[]=$pt_id;
					$emailDataArr[$pt_id]= $qryrow;
				}
				else
				{
					$printOldDataArr[]	= $qryrow['statement_data'];
				}
			}
			else{
				if($send_email)
				{
					$id=$qryrow['previous_statement_id'];

					$pt_id=$qryrow['patient_id'];
					$emailDataArr[$pt_id][$id]= $qryrow['statement_data'];
					$pt_id_arr[$pt_id]=$pt_id;
				}
				else
				{
					$printDataArr[]	= $qryrow['statement_data'];
					if($qryrow['statement_txt_data']){
						$printDataTxtArr[]	= $qryrow['statement_txt_data'];
					}
				}
			}
		}
		if(count($printDataArr)>0){
			$printData = join(' ',$printDataArr);
			$printDataTxt = join("\r\n",$printDataTxtArr);
			$save_html = $printData;
			if($text_print!="" && $printDataTxt!=""){
				$filename = 'statement_report.txt';
				$txt_filePath=write_html($printDataTxt,$filename);
				//header("location: downloadtxt.php?txt_filePath=$txt_filePath");		
			}else{
				$filePath=write_html(html_entity_decode($save_html));
				echo "<script type='text/javascript'>html_to_pdf('".$filePath."','p','','','','html_to_pdf_reports');</script>";
				$txt_filePath="";
			}
		}
		if(count($emailDataArr)>0){
		
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
		
			if(!$groupEmailConfig['email'] || !$groupEmailConfig['pwd'] || !$groupEmailConfig['host'])
			{
		?>  	<script>
					top.fAlert("Email not configured.");
					top.show_loading_image('hide');
				</script>
		<?php
			}
			//send smtp mail here
			// require_once '../../library/phpmailer/PHPMailerAutoload.php';
			
			//Create a new PHPMailer instance
			$mail = new PHPMailer\PHPMailer;
			//Tell PHPMailer to use SMTP
			$mail->isSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = 0;
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
		

			//get pat email ids
			$pt_ids=implode(',',$pt_id_arr);
			if($pt_ids)
			{
				$q=imw_query("select email,id, lname,fname, mname from patient_data where id IN($pt_ids) and email!=''")or die(imw_error());
				while($d=imw_fetch_object($q))
				{
					$pt_email_arr[$d->id]=$d->email;
					$pt_name_arr[$d->id]=ucwords(trim($d->lname.", ".$d->fname." ".$d->mname));
				}
			}
	
			foreach($emailDataArr as $pat_id=>$dataArr)
			{
				if($pt_email_arr[$pat_id])
				{
					$printData=$pt_email=$respartyName='';
					$printData = join(' ',$dataArr);
					$pt_email=$pt_email_arr[$pat_id];
					$respartyName=$pt_name_arr[$pat_id];
					$divData = $printData;
					include('emailStatements.php');

					

					//KEEP RECORD IF EMAIL IS SENT
					$arr_ids= array_keys($dataArr);
					$arr_ids=array_unique($arr_ids);
					$statement_ids=implode(', ',$arr_ids);

					if(empty($statement_ids)==false){
						$email_status= ($email_success=='1')? 'sent': 'failed';
						
						$qry="Update previous_statement SET email_sent='1',
						email_status='".$email_status."',
						email_operator='".$_SESSION['authId']."',
						email_date_time='".date('Y-m-d H:i:s')."' 
						WHERE previous_statement_id IN(".$statement_ids.")";
						$r=imw_query($qry);
					}					
				}
			}
			echo "<script type='text/javascript'>alert('".$totalMailsSent."/".$totalMails." emails sent successfully');</script>";
			$txt_filePath="";
		}
	}
	$loc_href='previous_statement.php?form_submitted=submit&grp_id='.$grp_id.'&send_email='.$send_email.'&text_print='.$text_print.'&Start_date='.$Start_date.'&End_date='.$End_date.'&patientId='.$patientId.'&txt_patient_name='.$txt_patient_name.'&txt_filePath='.$txt_filePath;
	echo "<script type='text/javascript'>window.location.href='".$loc_href."'</script>";
}

//--- GET Groups SELECT BOX ----
$selArrGroups = array_combine($_REQUEST['groups'],$_REQUEST['groups']);
$group_query = "Select  gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
    $group_id_arr[$group_id] = $group_res['name'];
	if($selArrGroups[$group_id])$sel='SELECTED';
	$groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
	
	$group_color = trim($group_res['group_color']);
	if(empty($group_color) == true){
		$group_color = '#FFFFFF';
	}
	$groupColorArr[$group_id] = $group_color;
}
?> 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: imwemr ::</title>
        <!-- Bootstrap -->
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
              <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
              <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->
		<link href="css/reports_html.css" type="text/css" rel="stylesheet">
        <style>
            .pd5.report-content {
                position:relative;
                margin-left:40px;

                background-color: #EAEFF5;
            }
            .fltimg {
                position:absolute;
            }
            .fltimg span.glyphicon {
                position: absolute;
                top: 170px;
                left: 10px;
                color: #fff;
            }
            .reportlft .btn.btn-mkdef {
                padding-top: 6px;
                padding-bottom: 6px;
            }
            #content1{
                background-color:#EAEFF5;
            }
			.total-row {
				height: 1px;
				padding: 0px;
				background: #009933;
			}	
		</style>
    </head>
    <body>
        <form name="sch_report_form" id="sch_report_form" method="post"  action="previous_statement.php">
            <input type="hidden" name="Submit" id="Submit" value="get reports">
            <input type="hidden" name="form_submitted" id="form_submitted" value="submit">
            <input type="hidden" name="print_pdf" id="print_pdf" value="">
            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
            <div class=" container-fluid">
                <div class="anatreport">
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Practice Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                    	<?php
											if($_REQUEST['Start_date']==""){$_REQUEST['Start_date']=date($phpDateFormat);}
											if($_REQUEST['End_date']==""){$_REQUEST['End_date']=date($phpDateFormat);}
										?>
                                        <div class="row">
                                        	<div class="col-sm-4">
                                                <label>Groups</label>
                                                <select name="groups[]" id="groups" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $groupName; ?>
												</select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>From</label>
                                                <div class="input-group">
                                                    <input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo ($_REQUEST['Start_date']!="")?$_REQUEST['Start_date']:''; ?>" class="form-control date-pick">
                                                    <label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                </div>
                                            </div>	
                                            <div class="col-sm-4">	
                                                <label>To</label>
                                                <div class="input-group">
                                                    <input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo ($_REQUEST['End_date']!="")?$_REQUEST['End_date']:''; ?>" class="form-control date-pick">
                                                    <label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                </div>
                                            </div>
                                         </div> 
                                         <div class="row">       
                                            <div class="col-sm-12">
												<div class="">
													<!-- Pt. Search -->
													<div class="col-sm-12"><label>Patient</label></div>
													<div class="col-sm-5">
														<input type="hidden" name="patientId" id="patientId" value="<?php echo $_REQUEST['patientId'];?>">
														<input class="form-control" type="text" id="txt_patient_name" value="<?php echo $_REQUEST['txt_patient_name'];?>" name="txt_patient_name" onkeypress="{if (event.keyCode==13)return searchPatient()}" >
													</div> 
													<div class="col-sm-5">
														<select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control minimal">
															<option value="Active">Active</option>
															<option value="Inactive">Inactive</option>
															<option value="Deceased">Deceased</option> 
															<option value="Resp.LN">Resp.LN</option> 
															<option value="Ins.Policy">Ins.Policy</option>
														</select>
													</div> 
													<div class="col-sm-2 text-center">
														<button class="btn btn-success" type="button" onclick="searchPatient();"><span class="glyphicon glyphicon-search"></span></button>
													</div> 	
												</div>
                                            </div>
										</div>
                                        <div class="row"> 
                                            <div class="col-sm-3">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="text_print" id="text_print" value="1" <?php if ($_REQUEST['text_print'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="text_print">Text File</label>
                                                </label>
                                         	</div>
                                            <div class="col-sm-3">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="send_email" id="send_email" value="1" <?php if ($_REQUEST['send_email'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="send_email">Send Email</label>
                                                </label>
                                         	</div>       
                                         </div>
                                    </div>
                                </div>
								<div class="grpara">
									<div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
                                        <button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
                                    </div>
                                </div>                                                                                        
                            </div>
                        </div>
                        <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
                            </div>
							<div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
										<?php
                                            if(empty($_REQUEST['form_submitted']) === false){
                                                $printFile = false;	
                                                if($txt_patient_name>0){
													$patientId=$txt_patient_name;
												}
												if(count($selectpatient) == 0){
													$Start_date = getDateFormatDB($Start_date);
													$End_date   = getDateFormatDB($End_date);	
												}
												
												//--- GET DATA FROM PREVIOUS STATEMENTS TABLE ---
												$grp_name_arr=array();
												if(empty($grp_id) == false){
													$qryGrp = "select name from groups_new where gro_id in($grp_id)";
													$resultgrp = imw_query($qryGrp);
													while($grparr = imw_fetch_array($resultgrp))
													{
														$grp_name_arr[$grparr['name']]=$grparr['name'];
													}
												}
												
												$qry = "select patient_data.lname,patient_data.fname, patient_data.mname,
														date_format(previous_statement.created_date,'".get_sql_date_format()."') as createdDate,patient_data.id	,
														previous_statement.previous_statement_id, previous_statement.statement_balance,
														previous_statement.search_options,previous_statement.created_time,
														users.fname as user_fname,users.lname as user_lname
														from previous_statement 
														join previous_statement_detail on previous_statement_detail.previous_statement_id = previous_statement.previous_statement_id
														join patient_data on patient_data.id = previous_statement.patient_id
														join patient_charge_list on patient_charge_list.patient_id = patient_data.id
														join users on users.id=previous_statement.operator_id
														where previous_statement.statement_acc_status=1 and previous_statement.statement_satus  = '0'";
												if($patientId){
													$qry .= " and previous_statement.patient_id = '$patientId'";
												}
												if($send_email){
													$qry .= " and patient_data.email != ''";
												}
												if($Start_date != '' && $End_date != ''){
													$qry .= " and previous_statement.created_date between '$Start_date' and '$End_date'";
												}
												if(empty($grp_id) == false){
													$grp_or_cond="";
													$grp_name_srh="";
													$final_grp_name_srh="";
													foreach($grp_name_arr as $grp_val){
														if($grp_val!=""){
															if(in_array(strtolower($billing_global_server_name), array('azar')) && $grp_val=="Laser &amp; Surgery Center"){
																$grp_val="Laser &amp;";
															}
															$grp_name_srh .="$grp_or_cond previous_statement_detail.statement_txt_data like '%$grp_val%'";
															$grp_or_cond=" or ";
														}
													}
													if($grp_name_srh!=""){
														$final_grp_name_srh=" and ($grp_name_srh)";
													}
													$qry .= " and patient_charge_list.gro_id in($grp_id) $final_grp_name_srh";
												}
												$qry .= " group by previous_statement.previous_statement_id
														order by patient_data.lname, patient_data.fname, previous_statement.created_date desc, previous_statement.created_time desc,
														previous_statement.previous_statement_id desc";
												$qryres=imw_query($qry);
												if(imw_num_rows($qryres)>0){
												?>
                                                <table class="rpt_table rpt rpt_table-bordered table" style="width:100%">
                                                    <tr>
                                                        <td class="text_b_w">
                                                         	<label class="checkbox checkbox-inline pointer">
                                                                <input type="checkbox" name="selectAll" id="selectAll" onclick="selAll(this.id);">
                                                                <label for="selectAll"><?php echo $label;?></label>
                                                            </label>
                                                        </td>
                                                        <td class="text_b_w text-center">Patient - ID</td>
                                                        <td class="text_b_w text-center">Total Balance</td>
                                                        <td class="text_b_w text-center">Forcefully Print</td>
                                                        <td class="text_b_w text-center">New Statement</td>
                                                        <td class="text_b_w text-center">Created Date</td>	
                                                        <td class="text_b_w text-center">Operator</td>	
                                                    </tr>
                                                <?php
												while($patientChargeListRes=imw_fetch_array($qryres)){
													$printFile = true;
													$search_options_arr = unserialize(html_entity_decode($patientChargeListRes['search_options']));
													$createdDate = $patientChargeListRes['createdDate'];
													$previous_statement_id = $patientChargeListRes['previous_statement_id'];
													
													//--- GET PATIENT DETAILS ----
													$patient_id = $patientChargeListRes['id'];		
													$patient_name = $patientChargeListRes['lname'].', ';
													$patient_name .= $patientChargeListRes['fname'].' ';
													$patient_name .= $patientChargeListRes['mname'];
													$patient_name = ucwords(trim($patient_name));
													if($patient_name[0] == ','){
														$patient_name = preg_replace("/, /","",$patient_name);
													}
													$srh_show_new_statements="No";
													if(strtolower($search_options_arr['show_new_statements'])=="yes" || $search_options_arr['show_new_statements']>0){
														$srh_show_new_statements="Yes";
													}
													$srh_force_cond="No";
													if(strtolower($search_options_arr['force_cond'])=="yes" || $search_options_arr['force_cond']>0){
														$srh_force_cond="Yes";
													}
													if($patientChargeListRes['created_time']=='00:00:00'){
														$created_time="";
													}else{
														$created_time_exp=explode(':',$patientChargeListRes['created_time']);
														$created_time=date(' h:i A',mktime($created_time_exp[0],$created_time_exp[1],$created_time_exp[2],0,0,0));
													}
													$operator_by=$patientChargeListRes['user_lname'];
													if($patientChargeListRes['user_fname']!=""){
														$operator_by.=', '.$patientChargeListRes['user_fname'];
													}
													
													$bgcolor = fmod($i,2) == 0 ? '#F4F9EE' : '#FFFFFF';
													
													$totBalance+= $patientChargeListRes['statement_balance'];
													$totCount++;
												
											?>
                                            	<tr bgcolor="<?php echo $bgcolor;?>">
                                                    <td class="text_10">
                                                    	<label class="checkbox checkbox-inline pointer">
                                                            <input type="checkbox" name="selectpatient[]" id="selectpatient<?php echo $previous_statement_id;?>" class="selectAll" value="<?php echo $previous_statement_id;?>">
                                                            <label for="selectpatient<?php echo $previous_statement_id;?>"></label>
                                                        </label>
                                                    </td>
                                                    <td class="text_10">
                                                    	<?php echo $patient_name.' - '.$patient_id;?>
                                                    </td>
                                                    <td class="text_10" align="right" style="text-align:right; padding-right:5px;">
                                                       <?php echo $patientChargeListRes['statement_balance'];?>
                                                    </td>
                                                    <td class="text_10" align="center" style="text-align:center;">
                                                        <?php echo $srh_force_cond;?>
                                                    </td>
                                                    <td class="text_10" align="center" style="text-align:center;">
                                                       <?php echo $srh_show_new_statements;?>
                                                    </td>
                                                     <td class="text_10" align="center">
                                                         <?php echo $createdDate.$created_time;?>
                                                    </td>
                                                    <td class="text_10" align="center">
                                                        <?php echo $operator_by;?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                                    <tr>
                                                        <td class="text_10b" style="text-align:right;">Total : </td>
                                                        <td class="text_10b" style="text-align:right;" colspan="2"><?php echo numberFormat($totBalance,2,'yes'); ?></td>
                                                        <td class="text_10b" style="text-align:right;" colspan="4"></td>
                                                    </tr>
                                        	</table>
											<?php
												}else{
													echo '<div class="text-center alert alert-info">No Record Found.</div>';
												}
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
            </div>
            <iframe src="" id="txt_iframe" name="txt_iframe" height="0" width="0" allowtransparency="1" class="hide"></iframe>
        </form>
        
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var conditionChk = '<?php echo $printFile; ?>';
	var send_emailChk = '<?php echo $send_email; ?>';
	var mainBtnArr = new Array();
	var btncnt=0;
	
	if(conditionChk==true){
		mainBtnArr[btncnt] = new Array("print", "Print", "top.fmain.generate_pdf('print');");
		btncnt++;
		if(send_emailChk>0){
			mainBtnArr[btncnt] = new Array("sendEmail", "Send Email", "top.fmain.generate_pdf('email');");
		}
	}
	top.btn_show("PPR", mainBtnArr);
	
	function generate_pdf(arg) {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		var sel = false;
		var obj = document.getElementsByName("selectpatient[]");
		for(i=0;i<obj.length;i++){
			if(obj[i].checked == true){
				sel = true;
				break;
			}
		}
		if(sel == false){
			top.show_loading_image("hide");
			var msg = "Please select any patient to print the statement.";
			if(arg=="email"){
				msg = "Please select any patient to send the statement via email."; 
			}
			alert(msg);
			return false;
		}else{
			top.show_loading_image('hide');
			$('#print_pdf').val(arg);
			document.sch_report_form.submit();
		}
	}
	function get_sch_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		$('#print_pdf').val('');
		document.sch_report_form.submit();
	}

	$(document).ready(function () {
		$(".toggle-sidebar").click(function () {
			$("#sidebar").toggleClass("collapsed");
			$("#content1").toggleClass("col-md-12 col-md-9");

			if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
			} else {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
			}
			return false;
		});
	});

	function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
	} 
		
	function selAll(master){
		var obj = $('#'+master);
		if(obj.is(':checked')){
			$('.'+master).prop('checked',true);
		}else{
			$('.'+master).prop('checked',false);
		}
	}
	
	function searchPatient(){
		var name = document.getElementById("txt_patient_name").value;
		var findBy = document.getElementById("txt_findBy").value;
		var validate = true;
		  if(name.indexOf('-') != -1){
			name = name.replace(' ','');
			name = name.split('-');
			name = name[0]
			validate = false;
		  }
		  if(validate){
			if(isNaN(name)){
				pt_win = window.open("../../interface/scheduler/search_patient_popup.php?btn_enter="+findBy+"&btn_sub="+name+"&call_from=physician_console","mywindow","width=800,height=500,scrollbars=yes");
			}
			else{
				getPatientName(name);
			}
		  }
		return false;
	}
	function getPatientName(id,obj){
		$.ajax({
			type: "POST",
			url: top.JS_WEB_ROOT_PATH+"/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
			dataType:'JSON',
			success: function(r){
				if(r.id){
					if(obj){
						set_xml_modal_values(r.id,r.pt_name);
					}else{
						$("#txt_patient_name").val(r.pt_name);
						$("#patientId").val(r.id);
					}
				}else{
					fAlert("Patient not exists");
					$("#txt_patient_name").val('');
					return false;
				}	
			}
		});
	}
	
	//previous name was getvalue
	function physician_console(id,name){
		document.getElementById("txt_patient_name").value = name;
		document.getElementById("patientId").value = id;
	}
	
	
$(window).load(function(){
	set_container_height();
});

$(window).resize(function(){
	set_container_height();
});
var page_heading = "<?php echo $dbtemp_name; ?>";
set_header_title(page_heading);

<?php if(empty($_REQUEST['form_submitted']) === false && $_REQUEST['txt_filePath']!="" ){ ?>
	$("#txt_iframe").attr('src','<?php echo "downloadtxt.php?txt_filePath=".$_REQUEST['txt_filePath'];?>');
<?php } ?>
</script>  
</body>
</html>