<?php
$without_pat = "yes";
require_once("reports_header.php");
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
$dbtemp_name="Previous HCFA";
$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$getSqlDateFormat = get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat);	
if(empty($_POST['form_submitted']) === true){
	$_POST['Start_date']=$_POST['End_date']=$curDate;
}
if(count($_POST['selectpatient']) > 0 && $_POST['print_pdf']>0){
	$PrintCms_white_chk=$PrintCms_chk;
	include_once "../billing/print_prev_hcfa_form.php";	
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
        <form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">
            <input type="hidden" name="Submit" id="Submit" value="get reports">
            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="print_pdf" id="print_pdf" value="1">
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
                                        
										<div class="row">
                                        	<div class="col-sm-4">
                                                <label>Start Date</label>
                                                <div class="input-group">
                                                    <input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo ($_POST['Start_date']!="")?$_POST['Start_date']:''; ?>" class="form-control date-pick">
                                                    <label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                </div>
                                            </div>	
                                            <div class="col-sm-4">	
                                                <label>End Date</label>
                                                <div class="input-group">
                                                    <input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo ($_POST['End_date']!="")?$_POST['End_date']:''; ?>" class="form-control date-pick">
                                                    <label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">	
                                                <label>CMS Type</label>
                                                <select name="PrintCms_chk" id="PrintCms_chk" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true">
                                                	<option value="PrintCms" <?php if($_REQUEST['PrintCms_chk']=="PrintCms"){echo "selected";}?>>Print CMS-1500</option>
                                                    <option value="PrintCms_white" <?php if($_REQUEST['PrintCms_chk']=="PrintCms_white"){echo "selected";}?>>W/O CMS-1500</option>
                                            	</select>
                                            </div>
                                            <div class="col-sm-12">
												<div class="">
													<!-- Pt. Search -->
													<div class="col-sm-12"><label>Patient</label></div>
													<div class="col-sm-5">
														<input type="hidden" name="patientId" id="patientId" value="<?php echo $_REQUEST['patientId'];?>">
														<input class="form-control" type="text" id="txt_patient_name" value="<?php echo $_REQUEST['txt_patient_name'];?>" name="txt_patient_name" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control" >
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
                                    </div>
                                </div>
								                                                                                   
                            </div>
                            <div id="module_buttons" class="ad_modal_footer text-center">
                                        <button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
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
                                            if(empty($_POST['form_submitted']) === false){
                                                $printFile = false;	
                                                
                                                $StartDate = getDateFormatDB($Start_date);
                                                $EndDate   = getDateFormatDB($End_date);
                                                
                                                $qry = "select patient_data.lname,patient_data.fname, patient_data.mname,patient_data.id,
                                                        previous_hcfa.previous_hcfa_id,previous_hcfa.enc_balance,
														date_format(previous_hcfa.created_date,'$getSqlDateFormat') as createdDate
                                                        from previous_hcfa 		
                                                        join patient_data on patient_data.id = previous_hcfa.patient_id
                                                        where previous_hcfa.hcfa_satus  = '0'";
                                                if($patientId>0){
                                                    $qry .= " and previous_hcfa.patient_id = '$patientId'";
                                                }else{
                                                    if($Start_date != '' && $End_date != ''){
                                                        $qry .= " and previous_hcfa.created_date between 
                                                                '$StartDate' and '$EndDate'";
                                                    }
                                                }
                                                $qry .= " order by previous_hcfa.created_date desc,
                                                        previous_hcfa.previous_hcfa_id desc,
                                                        patient_data.lname, patient_data.fname";
                                                $qryRes = imw_query($qry);
                                                if(imw_num_rows($qryRes)>0){
													$conditionChk = true;
												?>
                                                <table class="rpt_table rpt rpt_table-bordered table" style="width:100%">
                                                    <tr>
                                                        <td class="text_b_w" style="width:20px;">
                                                            <label class="checkbox checkbox-inline pointer">
                                                                <input style="cursor:pointer;" type="checkbox" name="chk_box" id="chk_box" onclick="chk_all_fun(this.checked,'');">
                                                                <label for="chk_box"></label>
                                                            </label>
                                                        </td>
                                                        <td class="text_b_w text-center">Patient - ID</td>
                                                        <td class="text_b_w text-center">Total Balance</td>
                                                        <td class="text_b_w text-center">Created Date</td>		
                                                    </tr>
                                                    <?php 
													while($row=imw_fetch_array($qryRes)){
													?>
                                                    <tr bgcolor="<?php echo $row['BG_COLOR']; ?>">
                                                        <td class="text_10">
                                                        	<label class="checkbox checkbox-inline pointer">
                                                                <input style="cursor:pointer;" type="checkbox" name="selectpatient[]" id="selectpatient_<?php echo $row['previous_hcfa_id'];?>" value="<?php echo $row['previous_hcfa_id'];?>">
                                                                <label for="selectpatient_<?php echo $row['previous_hcfa_id'];?>"></label>
                                                            </label>
                                                        </td>
                                                        <td class="text_10">
                                                            <?php echo $row['lname'].', '.$row['fname'];?> - <?php echo $row['id'];?>
                                                        </td>
                                                        <td class="text_10" align="right">
                                                           <?php echo numberFormat($row['enc_balance'],2);?>
                                                        </td>
                                                        <td class="text_10" align="center">
                                                           <?php echo $row['createdDate'];?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
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
        </form>
        
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var conditionChk = '<?php echo $conditionChk; ?>';
	var mainBtnArr = new Array();
	var btncnt=0;
	
	if(conditionChk==true){
		mainBtnArr[btncnt] = new Array("print", "Print", "top.fmain.generate_pdf();");
	}
	top.btn_show("PPR", mainBtnArr);
	
	function generate_pdf() {
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
			alert('Please select any patient to print HCFA.');
			return false;
		}else{
			$('#print_pdf').val(1);
			document.sch_report_form.submit();
		}
	}
	function get_sch_report() {
		top.show_loading_image('hide');
		//top.show_loading_image('show');
		$('#print_pdf').val('');
		document.sch_report_form.submit();
	}

	$(document).ready(function () {
		DateOptions("<?php echo $_POST['dayReport']; ?>");
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
		$_hgt = (window.innerHeight) - $('#module_buttons').outerHeight(true);
		$('.reportlft').css({
			'height':$_hgt,
			'max-height':$_hgt,
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
		$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});
	} 
	
	function chk_all_fun(val,mode){
		var obj= document.getElementsByName('selectpatient[]');
		for(i=0;i<obj.length;i++){
			obj[i].checked=val;
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

</script> 
</body>
</html>