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
?>
<?php
/*
  FILE : index.php
  PURPOSE : OPTICAL ORDER DETAIL
  ACCESS TYPE : INCLUDED
 */

require_once("../reports_header.php");
require_once('../../../library/classes/class.reports.php');
require_once('../../../library/classes/cls_common_function.php');
require_once('../common/report_logic_info.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

//ICD10 Options
$arrICD10Code=$CLSReports->getICD10Codes($withInvCommas='no', 'desc');
//$arrICD10Code = $CLSReports->getICD10Codes();
//pre($arrICD10Code);
$all_dx10_code_options = '';
$sel_dx10_code_options = '';
foreach ($arrICD10Code as $dxkey=>$dx10code) {
	$dx10code = str_replace("'", "", $dx10code);
	$sel = (in_array($dxkey,$dxcodes10)) ? 'selected' : '';
    $all_dx10_code_options .= "<option value='" . $dxkey . "' ".$sel.">" . $dx10code . "</opton>";
}
$allDXCount10 = sizeof($arrICD10Code);

//CPT CODES
$cptDetailsArr = array();
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl 
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color =$sel= '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = $row['cpt_prac_code'];
    if ($row['delete_status'] == 1 || $row['status'] == 'Inactive'){
        $color = 'color:#CC0000!important';
	}
	$cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
	$sel = (in_array($cpt_fee_id,$proc_codes)) ? 'SELECTED' : '';
	$cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
}
$allCPTCatCount = sizeof($cptDetailsArr);

// GET MEDICATIONS NAME
$strMedicationTitle='';
$medication_options='';
$rs=imw_query("Select id,medicine_name FROM medicine_data WHERE del_status = '0' ORDER BY medicine_name");
while($res=imw_fetch_array($rs)){
	$medicationTitleArr[]="'".addslashes($res['medicine_name'])."'";
    $sel = (in_array($res['medicine_name'],$medications)) ? 'selected' : '';
	$medication_options.= "<option value='".$res['medicine_name']."' ".$sel.">".$res['medicine_name']."</opton>";
}
$strMedicationTitle = join(',',$medicationTitleArr);

// GET MEDICATION ALLERGY
$medicationAllergyStr='';
$medication_allergy_options='';
$rs=imw_query("Select allergie_name FROM allergies_data ORDER BY allergie_name");
while($res=imw_fetch_array($rs)){
	$medicationAllergyArr[]="'".addslashes($res['allergie_name'])."'";
    $sel = (in_array($res['allergie_name'],$medication_allergy)) ? 'selected' : '';
    $medication_allergy_options.= "<option value='".$res['allergie_name']."' ".$sel.">".$res['allergie_name']."</opton>";
}
$medicationAllergyStr = join(',',$medicationAllergyArr);


// GET LAB TYPE AHEAD DATA
$stringAllObserv='';
$arrayAllObserv=array();
$sql = "select * from lab_radiology_tbl WHERE lab_radiology_status!='2' and lab_type='Lab' order by lab_radiology_name ASC";
$rez = imw_query($sql);	
while($row=imw_fetch_array($rez)){
	$id = $row["lab_radiology_tbl_id"];
	$lab_radiology_name = $row["lab_radiology_name"];
	$stringAllObserv.="'".addslashes($lab_radiology_name)."',";
	$arrayAllObserv[]=$lab_radiology_name;
}		
$stringAllObserv = substr($stringAllObserv,0,-1);


//--- GET ALL Physicians DETAILS ----
$selPhyId=implode(',',$physicians);
$physiciansOption = $CLSCommonFunction->dropDown_providers($selPhyId, '', '1');


//Time options
$timeOptions = '<option value="0">00 am</option>';
for ($i = 1; $i <= 23; $i++) {
    $fromSel = $toSel = '';
    $ampm = 'am';
    $num = $i;
    if ($i > 11) {
        if ($i > 12)
            $num = $i - 12;
        $ampm = 'pm';
    }
    if ($num < 10)
        $num = '0' . $num;

    if ($_POST['hourFrom'] == $i)
        $fromSel = 'SELECTED';
    $timeHourFromOptions .= '<option value="' . $i . '" ' . $fromSel . '>' . $num . ' ' . $ampm . '</option>';

    if ($_POST['hourTo'] == $i)
        $toSel = 'SELECTED';
    $timeHourToOptions .= '<option value="' . $i . '" ' . $toSel . '>' . $num . ' ' . $ampm . '</option>';
}


//Laboratory Section

//Static HTML Section
$jsString = '';

//Select Dropdowns
$fromArr = array(
    'greater' => '>',
    'greater_equal' => '>=',
    'equalsto' => '=',
    'less' => '<',
    'less_equal' => '<=',
);

$toArr = array(
    'less' => '<',
    'less_equal' => '<=',
);

$hidetoArr = array('equalsto','less','less_equal');
$fromOpt = '';
foreach($fromArr as $key=> $val) {
    $sel = '';
    $fromOpt.='<option value="'.$key.'" '.$sel.'> '.$val.' </option>';
}

$toOpt = '';
foreach($toArr as $key=> $val) {
    $sel = '';
    $toOpt.='<option value="'.$key.'" '.$sel.'> '.$val.' </option>';
}

$labHTML.='<div id="labRow{COUNT}">';
$labHTML.='<div class="col-sm-5">';
$labHTML.='<input type="text" name="observation{COUNT}" id="observation{COUNT}" value="'.(($_REQUEST)? '{OBS_VALUE}':'').'" menu="#select_drop" class="form-control observationType" >';
$labHTML.='</div>';
$labHTML.='<div class="col-sm-3 form-inline">';
$labHTML.='<select id="observation_criteria_from{COUNT}" name="observation_criteria_from{COUNT}" class="form-control minimal" style="width:40px" onchange="checkRange(this.value,{COUNT});">{OBSERVATION_FROM_DROP}</select>';
$labHTML.='<input type="text" name="observation_val_from{COUNT}" id="observation_val_from{COUNT}" value="'.(($_REQUEST)? '{OBS_VAL_FROM}':'').'" style="width:40px" class="form-control">';
$labHTML.='</div>';
$labHTML.='<div class="col-sm-3 form-inline">';
$labHTML.='<select id="observation_criteria_to{COUNT}" name="observation_criteria_to{COUNT}" class="form-control minimal" style="width:40px" >{OBSERVATION_TO_DROP}</select>';
$labHTML.='<input type="text" name="observation_val_to{COUNT}" style="width:40px" id="observation_val_to{COUNT}" value="'.(($_REQUEST)? '{OBS_VALUE_TO}':'').'" class="form-control">';
$labHTML.='</div>';
$labHTML.='<div class="col-sm-1">';
$labHTML.='<span id="add_observation_row{COUNT}" class="glyphicon glyphicon-{ICON} pointer" title="Add More" onClick="addLabRows({COUNT});"></span>';
$labHTML.='</div>';
$labHTML.='</div><div class="clearfix"></div>';

$rowData = $labHTML;
$laboratory_section = '';
//$finalCount = 0;
$totLabRows = (isset($_REQUEST['totLabRows']) && empty($_REQUEST['totLabRows']) == false) ? $_REQUEST['totLabRows'] : 1;
if(empty($totLabRows) == false)$finalCount = ($totLabRows+1);

$array_replace = array('{OBS_VALUE}', '{OBS_VAL_FROM}', '{OBS_VALUE_TO}', '{OBSERVATION_FROM_DROP}', '{OBSERVATION_TO_DROP}', '{ICON}');
$array_with = array('','','',$fromOpt, $toOpt, 'plus');
$labHTML = str_replace($array_replace, $array_with, $labHTML);

//Replacing Count and values for laboratory section
for($i=1;$i<=$totLabRows; $i++) {
    $fromOpt = '';
    foreach($fromArr as $key=> $val) {
        $sel = (empty($_REQUEST['observation_criteria_from'.$i]) == false && $key == $_REQUEST['observation_criteria_from'.$i]) ? 'selected' : '';
        $fromOpt.='<option value="'.$key.'" '.$sel.'> '.$val.' </option>';
    }
    
    $toOpt = '';
    foreach($toArr as $key=> $val) {
        $sel = (empty($_REQUEST['observation_criteria_to'.$i]) == false && $key == $_REQUEST['observation_criteria_to'.$i]) ? 'selected' : '';
        $toOpt.='<option value="'.$key.'" '.$sel.'> '.$val.' </option>';
    }
    $icon_class = 'remove';
    if($i==$totLabRows) {
        $icon_class = 'plus';
    }
    $array_replace = array('{COUNT}', '{OBS_VALUE}', '{OBS_VAL_FROM}', '{OBS_VALUE_TO}', '{OBSERVATION_FROM_DROP}', '{OBSERVATION_TO_DROP}', '{ICON}');
	$array_with = array($i, $_REQUEST['observation'.$i], $_REQUEST['observation_val_from'.$i], $_REQUEST['observation_val_to'.$i], $fromOpt, $toOpt, $icon_class);
	$htmlStr = str_replace($array_replace, $array_with, $rowData);
	$laboratory_section .= $htmlStr;

    if(in_array($_REQUEST['observation_criteria_from'.$i], $hidetoArr)) {
        $jsString.= "top.fmain.checkRange('".$_REQUEST['observation_criteria_from'.$i]."','".$i."')";
    }
}
//Report Name
$dbtemp_name = 'Clinical Report';
$dbtemp_name_CSV = 'clinical_report.csv';

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
        
        <div class=" container-fluid">
            <div class="anatreport">
                <div id="select_drop" style="position:absolute;bottom:0px;"></div>
                <div class="row" id="row-main">
                    <div class="col-md-3" id="sidebar">
                        <form name="clinical_reports_frm" id="clinical_reports_frm" action="" method="post">
                            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
                            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
                            <input type="hidden" id="send_fax_subject" name="send_fax_subject" value="<?php echo constant('fax_subject');?>">
                            <input type="hidden" id="preObjBack" name="preObjBack" value="">
							<input type="hidden" name="printFile" id="printFile" value="0">
							<input type="hidden" name="file_location" id="file_location" value="">
                            <?php include 'clinical_left_bar.php' ?>
                        </form>
                    </div>

                    <div class="col-md-9" id="content1">
                        <div class="btn-group fltimg" role="group" aria-label="Controls">
                            <img class="toggle-sidebar" src="../../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
                        </div>
                        <div class="pd5 report-content">
                            <div class="rptbox">
								<div id="onload_msg" class="text-center alert alert-info">No Search Done.</div>
                                <div id="html_data_div" class="row"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
		<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="../downloadCSV.php" method ="post" > 
			<input type="hidden" name="file_format" id="file_format" value="csv">
			<input type="hidden" name="zipName" id="zipName" value="">	
			<input type="hidden" name="file" id="file" value="" />
		</form>  
        
        <form name="frm_pt_consult_letter" id="frm_pt_consult_letter" method="post" action="consult_letters_diabetic_exam.php" enctype="multipart/form-data">
            <div class="border bg1" id="send_fax_div" style="background:#fff;top:100px;left:150px; position:absolute;display:none; width:550px;z-index:900;">
                <div class="page_block_heading_patch pt4 boxhead" style="cursor:move;text-align:left;">
                    <span class="closeBtn" id="closeBtn" onclick="javascript:document.getElementById('send_fax_div').style.display = 'none';top.show_loading_image('hide');"></span>
                    <b style="font-size:14px; ">Send Fax</b>
                </div>
                <div id="consult_fax_div" ></div>
                <div>
                    <table border="0" align="left" cellpadding="0" cellspacing="0" style="width:500px; height:100px;">
                        <tr>
                            <td style="width:50%; text-align:right; padding-right:10px;"><input type="button" class="dff_button hold" value="Send Fax" id="send_close_btn" onclick="return getFxFun();"></td>
                            <td style="width:50%;" class="alignLeft">
                                <input type="button" class="dff_button cancel" value="Close" onclick="javascript:document.getElementById('send_fax_div').style.display = 'none';top.show_loading_image('hide');" id="fax_cancel_btn">
                                <input type="hidden" name="hidd_fax_consult_id_comma" id="hidd_fax_consult_id_comma" value="">
                            </td>

                        </tr>
                    </table>
                </div>
            </div>             
        </form>

        <form name="frm_pt_consult_letter_direct" id="frm_pt_consult_letter_direct" method="post" action="consult_letters_direct_diabetic_exam.php" enctype="multipart/form-data">
            <div class="border bg1" id="send_direct_div" style="background:#fff;top:100px;left:150px; position:absolute;display:none; width:1000px;z-index:900;">
                <div class="page_block_heading_patch pt4 boxhead" style="cursor:move;text-align:left;">
                    <span class="closeBtn" id="closeBtn" onclick="javascript:document.getElementById('send_direct_div').style.display = 'none';top.show_loading_image('hide');"></span>
                    <b style="font-size:14px; ">Send Direct</b>
                </div>
                <div id="consult_direct_div" ></div>
                <div>
                    <table border="0" align="left" cellpadding="0" cellspacing="0" style="width:900px; height:100px;">
                        <tr>
                            <td style="width:50%; text-align:right; padding-right:10px;"><input type="button" class="dff_button hold" value="Send Direct" id="send_direct_close_btn" onclick="return sndDirectConsultFun();"></td>
                            <td style="width:50%;" class="alignLeft">
                                <input type="button" class="dff_button cancel" value="Close" onclick="javascript:document.getElementById('send_direct_div').style.display = 'none';top.show_loading_image('hide');" id="direct_cancel_btn">
                                <input type="hidden" name="hidd_direct_consult_id_comma" id="hidd_direct_consult_id_comma" value="">
                            </td>

                        </tr>
                    </table>
                </div>
            </div>             
        </form>

        <script type="text/javascript">
            var dbtemp_name = '<?php echo $dbtemp_name; ?>';
            var customarrayObserv ="";
			<?php if($stringAllObserv!=""){ ?>
					customarrayObserv = new Array(<?php echo str_ireplace(array("\r","\n"),array("\\r","\\n"),$stringAllObserv); ?>);
            <?php }?>
            
            var customarrayTitle = new Array(<?php echo str_ireplace(array("\r","\n"),array("\\r","\\n"),$strMedicationTitle); ?>);
            var medicationAllergyStr = new Array(<?php echo str_ireplace(array("\r","\n"),array("\\r","\\n"),$medicationAllergyStr); ?>);
            
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $(document).ready(function () {
                DateOptions('<?php echo $_POST['dayReport'];?>');
                $(".fltimg").click(function () {
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
        
            var show_remove_btn = '<?php echo $show_remove_btn; ?>';
            function GetXmlHttpObject(){            
                var objXMLHttp=null;
                if(window.XMLHttpRequest){
                    objXMLHttp=new XMLHttpRequest();
                }else if(window.ActiveXObject){
                    objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
                return objXMLHttp;
            }
            
            function get_saved_report_types(){
				var url_dt = "get_saved_reports.php";                                
                
                xmlHttp_sch=GetXmlHttpObject();                        
				
                if(xmlHttp_sch==null){
                    alert ("Browser does not support HTTP Request");
                    return false;
                }
				
                top.show_loading_image('show');
                
                xmlHttp_sch.open("POST",url_dt,true);
                xmlHttp_sch.send(null);
                
                xmlHttp_sch.onreadystatechange = function(){                        
                    if (xmlHttp_sch.readyState==4 || xmlHttp_sch.readyState=="complete"){		
                        $("#saved_report_types").html(xmlHttp_sch.responseText);
                        top.show_loading_image('hide');
                    }else{        
                        top.show_loading_image('show');
                    }
                }
			}
            
            function show_saved_report_types(mode){
				
				var url_dt = "get_saved_reports.php?mode=div";                                
                
                xmlHttp_sch=GetXmlHttpObject();                        
				
                if(xmlHttp_sch==null){
                    alert ("Browser does not support HTTP Request");
                    return false;
                }
				
                top.show_loading_image('show');
                
                xmlHttp_sch.open("POST",url_dt,true)
                xmlHttp_sch.send(null);
                
                xmlHttp_sch.onreadystatechange = function(){                        
                    if (xmlHttp_sch.readyState==4 || xmlHttp_sch.readyState=="complete"){		
						//alert(xmlHttp_sch.responseText);
						//document.write(xmlHttp_sch.responseText);
						//return false;
						top.removeMessi();
						top.fAlert(xmlHttp_sch.responseText,'','','','','Close');
                        //document.getElementById("show_saved_report_type").innerHTML = xmlHttp_sch.responseText;
                        //document.getElementById("show_saved_report_type").style.display = "block";

						top.show_loading_image('hide');
						if(mode == "re"){
							get_saved_report_types();
						}
                    }else{        
                        top.show_loading_image('show');
                    }
                }
			}
            
            function delete_report(del_id, cnfrm){
				//console.log(del_id);
				//console.log(cnfrm);
				if(typeof(cnfrm)=="undefined"){
					top.fancyConfirm("Are you sure to delete this report?",'',"top.fmain.reportsFrame.delete_report("+del_id+",true)");
					return;
				}
				else{
					var url_dt = "delete_reports.php?id="+del_id;                                
					
					xmlHttp_sch=GetXmlHttpObject();                        
					
					if(xmlHttp_sch==null){
						alert ("Browser does not support HTTP Request");
						return false;
					}
					
					top.show_loading_image('show');
					
					xmlHttp_sch.open("POST",url_dt,true)
					xmlHttp_sch.send(null);
					
					xmlHttp_sch.onreadystatechange = function(){                        
						if (xmlHttp_sch.readyState==4 || xmlHttp_sch.readyState=="complete"){	
							
							
							show_saved_report_types("re");
							
							top.show_loading_image('hide');
						}else{        
							top.show_loading_image('show');
						}
					}
				}
			}
                
            function laboratoryData() {
                var totLabRows=$('#totLabRows').val();
				var labVals='';
				var checkRange=0;
				var arrLabCrt=Array();
				arrLabCrt['greater']= '>';
				arrLabCrt['greater_equal']= '>=';
				arrLabCrt['equalsto']= '=';
				arrLabCrt['less_equal']= '<=';
				arrLabCrt['less']= '<';
				var ifOneAdded=0;
				var labLabel='';
				for(j=1; j<=totLabRows; j++){
					checkRange=0;
					if($('#observation'+j).val()!=''){
						labVals+=(ifOneAdded=='1') ? " OR (" : " (" ;
						labVals+=" lab_obs.observation='"+trim($('#observation'+j).val())+"'";
						labLabel+=trim($('#observation'+j).val());
						checkRange=1;
						ifOneAdded=1;
					}
					if(checkRange=='1'){
						if($('#observation_val_from'+j).val()!=''){
							labVals+=" AND lab_obs.result "+arrLabCrt[$('#observation_criteria_from'+j).val()]+$('#observation_val_from'+j).val();
							labLabel+=' '+arrLabCrt[$('#observation_criteria_from'+j).val()]+$('#observation_val_from'+j).val();
						}
						if($('#observation_val_to'+j).val()!=''){
							labVals+=" AND lab_obs.result "+arrLabCrt[$('#observation_criteria_to'+j).val()]+$('#observation_val_to'+j).val();
							labLabel+=' '+arrLabCrt[$('#observation_criteria_to'+j).val()]+$('#observation_val_to'+j).val();
						}
					labLabel+='<br>';
					labVals+=")";					
					}
				}
                return labLabel+'~~'+labVals;
            }
            function get_result() {
                top.show_loading_image('hide');
                top.show_loading_image('show');
                
                var labData = laboratoryData();
                var result = labData.split('~~');
                var labLabel = result[0];
                var labVals = result[1];
                var input_labVals = $("<input>").attr("type", "hidden").attr("name", "labVals").val(labVals);
                var input_labLabel = $("<input>").attr("type", "hidden").attr("name", "labLabel").val(labLabel);
                $('#clinical_reports_frm').append($(input_labVals)).append($(input_labLabel));
					  
				req_data = $('#clinical_reports_frm').serialize();
				$('#csv_text').val('');
				$('#file').val('');
				$.ajax({
					type: "POST",
					url: top.JS_WEB_ROOT_PATH + "/interface/reports/clinical/process.php",
					data: req_data,
					dataType: 'JSON',
					success: function(resp) {
						document.getElementById('onload_msg').style.display='none';
						if(!resp.html){
							$("#html_data_div").html('<div class="text-center alert alert-info">No Record Found.</div>');
						}else{
							var csvValue = resp.csvFile;
							$('#file').val(csvValue);
							$("#html_data_div").html('<div class="text-center alert alert-info">Please check exported csv file.</div>');
							if(resp.csv == 'output_csv'){
								download_csv();
								top.show_loading_image('hide');
							}
						}
						top.show_loading_image('hide');
					}
				});
            }
            
            var diabetic_exam = $('#diabetic_exam option:selected').val();
            var physicians_new = "";

            lightBoxFlag = 0;
            function toggle_lightbox(show_hide_flag) {
                show_hide_flag = show_hide_flag || '';
                if (show_hide_flag == 'hide')
                    lightBoxFlag = 1;
                else if (show_hide_flag == 'show')
                    lightBoxFlag = 0;

                var popupid = '#divLightBox';
                if (!lightBoxFlag) {
                    $(popupid).fadeIn();
                    lightBoxFlag = 1;
                } else {
                    $(popupid).fadeOut();
                    lightBoxFlag = 0;
                }
                $('#report_name').val('');
                //$('#divLightBox').append('<div id="fade"></div>');
                //$('#fade').css({'filter':'alpha(opacity=50)'}).fadeIn();
                var popuptopmargin = ($(popupid).height() + 10) / 2;
                var popupleftmargin = ($(popupid).width() + 10) / 2;
                $(popupid).css({
                    'margin-top': -popuptopmargin,
                    'margin-left': -popupleftmargin
                });
            }

            function generate_pdf() {
				 var file_location_val =  $('#file_location').val();
                if (file_location_val != '') {
                    top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
                    top.html_to_pdf(file_location_val, 'l');
                    window.close();
                }
            }



            function enableOrNot(val) {
                /*		if(val=='Detail'){
                 $('#home_facility').attr('disabled', false);
                 }else{
                 $('#home_facility').attr('checked', false);
                 $('#home_facility').attr('disabled', true);
                 }*/
            }

            function addRemoveGroupBy(dateRangeFor) {
                if (dateRangeFor == 'date_of_service') {
                    $("#viewBy").append('<option value="operator">Operator</option>');
                    $('#without_deleted_amounts').attr('disabled', true);
                } else {
                    $("#viewBy option[value='operator']").remove();
                    $('#without_deleted_amounts').attr('disabled', false);
                }
            }

            // SAVED SEARCH FUNCTIONS
            var dChk = 0;
            function callAjaxFile(ddText, opIndex) {
                oDropdown.off("change");
                var returnVal = 0;
                dChk = 1;
                var dd = confirm('Are sure to delete the selected search?');
                if (dd) {
                    $.ajax({
                        url: "delete_search.php?sTxt=" + ddText,
                        success: function (callSts) {
                            if (callSts == '1') {
                                oDropdown.close();
                                oDropdown.remove(opIndex);
                                oDropdown.set("selectedIndex", 0);
                            }
                        }
                    });
                }
                return returnVal;
            }

            function callSavedSearch(srchVal, formId) {
                top.show_loading_image('hide');
                top.show_loading_image('show');

                if (srchVal != '' && dChk != '1') {
                    dChk = 0;

                    $('#call_from_saved').val('yes');
                    $('#' + formId).submit();
                }
                dChk = 0;
            }
            // END SAVED SEARCH	

            function order_status(id, name, status) {
                if (status = true) {
                    document.frm_reports.action = "index.php?showpage=" + optical_showpage + "&confirm_id=" + id + "&name=" + name;
                    document.frm_reports.submit();
                }
            }
           
            function enable_disable_time(ctrlVal) {
                if (ctrlVal == 'transaction_date') {
                    $('#hourFrom').prop('disabled', false);
                    $('#hourTo').prop('disabled', false);
                } else {
                    $('#hourFrom').prop('disabled', true);
                    $('#hourTo').prop('disabled', true);
                }
            }
            
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
            
            $(document).ready(function (e) {
                var page_heading = "<?php echo $dbtemp_name; ?>";
                set_header_title(page_heading);
                
               // addLabRows(0);
                //var ac = new actb(document.getElementById('medications'),customarrayTitle);
				//var ac1 = new actb(document.getElementById('medication_allergy'),medicationAllergyStr);
                var ac1 = $('#medication_allergy').typeahead( { 'source': medicationAllergyStr } );
                var obj = $('.observationType').typeahead( { 'source': customarrayObserv } );
                //get_saved_report_types();
            });
            
            $(window).load(function () {
                set_container_height();
                $('#csvFileDataTable').height($('.reportlft').height() - 70);
            });

            $(window).resize(function () {
                set_container_height();
                $('#csvFileDataTable').height($('.reportlft').height() - 70);
            });
            
            function checkRange(val,i){
                if(val=='greater' || val=='greater_equal'){
					$('#observation_criteria_to'+i).removeClass('hide');
					$('#observation_val_to'+i).removeClass('hide');
				}else if(val=='equalsto' || val=='less_equal' || val=='less'){
					$('#observation_val_to'+i).val('');
					$('#observation_criteria_to'+i).addClass('hide');
					$('#observation_val_to'+i).addClass('hide');
				}
			}
            
            function addLabRows(rowNo){
               var rowData='';
				if(rowNo>0){
					var elmObj = document.getElementById("add_observation_row"+rowNo);

					elmObj.title = 'Delete Row';
                    $(elmObj).removeClass("glyphicon-plus")
                    $(elmObj).addClass("glyphicon-remove");
					elmObj.onclick=function(){ 
						$("#labRow"+rowNo).remove(); 
					}
				}
				var i=rowNo+1;
				if(rowNo>0){ elmObj.id=i;}
				
                rowData = '<?php echo $labHTML; ?>';
                rowData = rowData.replace(/{COUNT}/g, i);
                rowData = rowData.replace('/\{.*\}/', '');
                
				if(i=='1'){
					document.getElementById('table_lab').innerHTML=rowData;
				}else{
					$("#labRow"+rowNo).after(rowData);
				}
                var obj = $( '.observationType' ).typeahead( { 'source': customarrayObserv } );
				//var obj = new actb(document.getElementById('observation'+i),customarrayObserv);
				$('#totLabRows').val(i);
				
			}
            
            function zip_vs_state(objZip,objExt,objCity,objState,objCountry,objCounty){
                // window.opener.top.stop_zipcode_validation -> Not working yet as constant not declared 
                objCity = $('#'+objCity+'');
                objState = $('#'+objState+'');
                objExt = $('#'+objExt+'');
                var zip_code = $(objZip).val();
                if(zip_code == ''){
                    return false;
                }
                var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php?zipcode="+zip_code+'&zipext=y';
                $.ajax({
                    url:url,
                    success:function(data)
                    {
                        var val=data.split("-");
                        var city = $.trim(val[0]);
                        var state = $.trim(val[1]);
                        var extension = $.trim(val[2]);
                        if(city!="")
                        {
                            $(objCity).val(city);
                            $(objState).val(state);
                            $(objExt).val(extension);
                            return;
                        }

                        $(objZip).val("");
                        top.fAlert('Please enter correct Zip code');
                        //changeClass(document.getElementById($(objZip).attr("id")),1);
                        $(objZip).select();
                        $(objCity).val(" ");
                        $(objState).val(" ");
                        $(objExt).val(" ");
                    }
               });
            }
             
            
            function addOtherDiv(obj){
                var dataArr = $(obj).data();
                var elemId = dataArr.call;
                var arrSel = $(obj).val();
                
                var parentObj = $('#'+elemId);
                var otherObj = $('#other_'+elemId);
                
                if($.isArray(arrSel)){
                    if(arrSel.indexOf("Other") !== -1){
                        if(parentObj.hasClass('hide') == false) parentObj.addClass('hide').selectpicker('hide').selectpicker('val', '');
                        if(otherObj.hasClass('hide') == true) otherObj.removeClass('hide');
                        
                        $(obj).trigger('hide.bs.select');
                    }else{
                        if(parentObj.hasClass('hide') == true) parentObj.removeClass('hide').selectpicker('show');
                        if(otherObj.hasClass('hide') == false) otherObj.addClass('hide');
                        otherObj.val('');
                    }
                }

			}
            
            function hideThis(obj){
                var dataArr = $(obj).data();
                var elemId = dataArr.call;
                var parentObj = $('#'+elemId);
                var otherObj = $('#other_'+elemId);
				if(otherObj.hasClass('hide') == false) otherObj.addClass('hide');
				if(parentObj.hasClass('hide') == true) parentObj.removeClass('hide').selectpicker('show');
                
                //$(obj).trigger('show.bs.select');
			}
            
            //Sort
//            if($("#ajax_res_tbl").length >0){							
//                ajax_res_tbl=new table.sorter("ajax_res_tbl");							
//                ajax_res_tbl.init("ajax_res_tbl");							
//            }
            
            
            <?php if(empty($jsString) == false) echo $jsString;?>
            
            function checkAllChkBox($_this,physicianID)
			{
				var cObj = $(".chk_box_"+physicianID+"");
				if($_this.checked == true)
				{
					cObj.prop('checked',true);
				}
				else
				{
					cObj.prop('checked',false);
				}
			}
            /* 
            
            
            function consult_letter_fun(consult_option) {
				var obj 					= document.getElementsByName("chbx_diab_exam[]");
				var obj_hidd_pt_name_id 	= document.getElementsByName("hidd_pt_name_id[]");
				var obj_hidd_pt_pcp 		= document.getElementsByName("hidd_pt_pcp[]");
				var obj_hidd_pt_pcp_id 		= document.getElementsByName("hidd_pt_pcp_id[]");
				var obj_hidd_pt_pcp_fax_no 	= document.getElementsByName("hidd_pt_pcp_fax_no[]");
				
				var msg = "";
				var chkbx_id_comma = "";
				var objLength = obj.length;
				var j=0;
				var consult_id_val = "";
				var fax_html = "" ;
				if(objLength >0 && consult_option=="consult_fax") {
					fax_html += '<table  align="left" cellpadding="1" cellspacing="0" style="width:100%; height:100px; border:1px solid #000000;">';	
					fax_html += '	<tr>';
					fax_html += '		<td style="padding-top:2px; width:30px; max-width:30px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;" class="txt_11b alignLeft pl5">&nbsp;</td>';
					fax_html += '		<td style="padding-top:2px; width:260px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;border-right:1px solid #000000;" class="txt_11b alignLeft pl5">Patient&nbsp;Name</td>';
					fax_html += '		<td style="padding-top:2px; width:210px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;border-right:1px solid #000000;" class="txt_11b alignLeft pl5">PCP</td>';
					fax_html += '		<td style="padding-top:2px; width:150px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;border-right:1px solid #000000;" class="txt_11b alignLeft pl5">PCP Fax#</td>';
					
					fax_html += '	</tr>';
				}
				
				var row_color = "";
				var border_top = "";
				for(i=0; i<objLength; i++){
					if(obj[i].checked == true){
						consult_id_val 		= obj[i].value;
						var pt_name_id_val 		= obj_hidd_pt_name_id[i].value
						var pt_pcp_val 			= obj_hidd_pt_pcp[i].value
						var pt_pcp_id_val 		= obj_hidd_pt_pcp_id[i].value
						var pt_pcp_fax_no_val 	= obj_hidd_pt_pcp_fax_no[i].value
						
						j++;
						if(j==1) { 
							chkbx_id_comma 	= consult_id_val;
							border_top  	= "border-right:1px solid #000000;";
						} else { 
							chkbx_id_comma += ","+consult_id_val; 
							border_top 		= "";
						}
						row_color = "";
						if(j%2!=0) { row_color = "background:#FFFFFF;"; }
						if(consult_option=="consult_fax") {
							fax_html += '<tr style="'+row_color+'">';
							fax_html += '<td class="pl5" style="padding-top:2px; width:30px; max-width:30px;" id="td_fax_status_'+consult_id_val+'"></td>';
							fax_html += '<td class="pl5" style="padding-top:2px; width:260px;border-right:1px solid #000000;'+border_top+'">'+pt_name_id_val+'</td>';
							fax_html += '<td class="pl5" style="padding-top:2px; width:210px;border-right:1px solid #000000;">'+pt_pcp_val+'<input type="hidden" name="pt_pcp_id_'+consult_id_val+'" id="pt_pcp_id_'+consult_id_val+'" value="'+pt_pcp_id_val+'"></td>';
							if(pt_pcp_val)
							{
								fax_html += '<td class="pl5" style="padding-top:2px; width:150px;border-right:1px solid #000000;"><input type="text" name="pt_pcp_fax_no_'+consult_id_val+'" id="pt_pcp_fax_no_'+consult_id_val+'" value="'+pt_pcp_fax_no_val+'"></td>';
							}
							else
							{
								fax_html += '<td class="pl5" style="padding-top:2px; width:150px;border-right:1px solid #000000;">&nbsp;</td>';
							}
							
							fax_html += '	</tr>';
						}
					}
				}
				if(objLength >0 && consult_option=="consult_fax") {
					fax_html += '</table>';
				}
				
				if(chkbx_id_comma && consult_option=="consult_print") {
					document.frm_more_options.action = 'consult_letters_diabetic_exam.php';
					document.frm_more_options.chkbx_id_comma.value = chkbx_id_comma;
					document.frm_more_options.hidd_consult_mod.value = consult_option;
					document.frm_more_options.submit();
				}else if(chkbx_id_comma && consult_option=="consult_fax") {
					document.frm_pt_consult_letter.hidd_fax_consult_id_comma.value = chkbx_id_comma;
					//send_fax_div
					if($("#consult_fax_div").length > 0) {
						//document.getElementById("send_fax_div").style.display = "inline-block";
						$("#send_fax_div").show();
						$("#consult_fax_div").html(fax_html); //= 	fax_html;
						//$("#send_fax_div").draggable();
					}
					/*
					document.frm_pt_consult_letter.action = 'consult_letters_diabetic_exam.php';
					document.frm_pt_consult_letter.hidd_fax_consult_id_comma.value = chkbx_id_comma;
					document.frm_pt_consult_letter.hidd_consult_mod.value = consult_option;
					document.frm_pt_consult_letter.submit();
					*/
	/*			}else {
					msg = "Please select patient(s) to print consult letter";
					if(consult_option=="consult_fax") {
						msg = "Please select patient(s) to send fax";
					}
                    top.fAlert(msg);
					//alert(msg);	
				}
				
			}
            
            var $_directCounter = 0 ;
			function generateConsultLetter(consultIds){
				
				if(consultIds)
				{
					$.ajax({
						type: "POST",
						url: "consult_letters_direct_diabetic_exam.php?consultIds="+consultIds+"&hidd_consult_mod=consult_direct",
						success: function(resp){ }
					});					
				}
				
			}
            
            
            function consult_letter_fun_direct(consult_option) {
				top.show_loading_image('show','Generating Reports...');
				var obj 					= document.getElementsByName("chbx_diab_exam[]");
				var objLength 				= obj.length;
				var obj_hidd_pt_name_id 	= document.getElementsByName("hidd_pt_name_id[]");
				var obj_hidd_pt_pcp 		= document.getElementsByName("hidd_pt_pcp[]");
				var obj_hidd_pt_pcp_id 		= document.getElementsByName("hidd_pt_pcp_id[]");
				var obj_hidd_pt_pcp_email	= document.getElementsByName("hidd_pt_pcp_email[]");
				
				var msg = '';
				var consult_id_val = '';
				var html = '';
				var chkbx_id_comma = "";
				
				var j=0; 
				var selected = 0;
				
				if(objLength >0 && consult_option=="consult_direct") {
					html += '<table  align="left" cellpadding="1" cellspacing="0" style="width:100%; height:100px; border:1px solid #000000;">';	
					html += '<tr>';
					html += '<td style="padding-top:2px; width:30px; max-width:30px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;" class="txt_11b alignLeft pl5">&nbsp;</td>';
					html += '<td style="padding-top:2px; width:300px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;border-right:1px solid #000000;" class="txt_11b alignLeft pl5">Patient&nbsp;Name</td>';
					html += '<td style="padding-top:2px; width:210px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;border-right:1px solid #000000;" class="txt_11b alignLeft pl5">PCP</td>';
					html += '<td style="padding-top:2px; width:150px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;border-right:1px solid #000000;" class="txt_11b alignLeft pl5">PCP Email Id</td>';
					html += '<td style="padding-top:2px; width:200px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;border-right:1px solid #000000;" class="txt_11b alignLeft pl5">Subject</td>';
					html += '<td style="padding-top:2px; width:200px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;border-right:1px solid #000000;" class="txt_11b alignLeft pl5">Message</td>';
					//html += '<td style="padding-top:2px; width:50px;font-weight:bold;font-size:11px;border-bottom:1px solid #000000;border-right:1px solid #000000;" class="txt_11b alignLeft pl5">Attachment</td>';
					html += '</tr>';
				}
				var row_color = "";
				var border_top = "";
				for(i=0; i < objLength; i++){
					if(obj[i].checked == true){
						selected++;
						var consult_id_val 		= obj[i].value;
						var pt_name_id_val 		= obj_hidd_pt_name_id[i].value
						var pt_pcp_val 			= obj_hidd_pt_pcp[i].value
						var pt_pcp_id_val 		= obj_hidd_pt_pcp_id[i].value
						var pt_pcp_email_val 	= obj_hidd_pt_pcp_email[i].value;
						var pt_pcp_subject_val	= 'Consult Letter';
						var pt_pcp_msg_val		= 'Please find attachment';
						//var pt_pcp_attachment_val='';
						
						j++;
						if(j==1) { 
							border_top  	= "border-right:1px solid #000000;";
						} else { 
							border_top 		= "";
						}
						row_color = "";
						if(j%2!=0) { row_color = "background:#FFFFFF;"; }
						if(consult_option=="consult_direct") {
							html += '<tr style="height:25px; '+row_color+'">';
							html += '<td class="pl5" style="padding-top:2px; width:30px; max-width:30px;" id="td_dir_status_'+consult_id_val+'"></td>';
							html += '<td class="pl5" style="padding-top:2px; width:300px;border-right:1px solid #000000;'+border_top+'">'+pt_name_id_val+'</td>';
							html += '<td class="pl5" style="padding-top:2px; width:210px;border-right:1px solid #000000;">'+pt_pcp_val+'<input type="hidden" name="pt_pcp_id_'+consult_id_val+'" id="pt_pcp_id_'+consult_id_val+'" value="'+pt_pcp_id_val+'"></td>';
							if(pt_pcp_val)
							{	
								generateConsultLetter(consult_id_val);
								chkbx_id_comma += ","+consult_id_val+'_y'; 
								
								html += '<td class="pl5" style="padding-top:2px; width:150px;border-right:1px solid #000000;"><input type="text" name="pt_pcp_email_'+consult_id_val+'" id="pt_pcp_email_'+consult_id_val+'" value="'+pt_pcp_email_val+'"></td>';
								html += '<td class="pl5" style="padding-top:2px; width:200px;border-right:1px solid #000000;"><input type="text" name="pt_pcp_subject_'+consult_id_val+'" id="pt_pcp_subject_'+consult_id_val+'" value="'+pt_pcp_subject_val+'"></td>';
								html += '<td class="pl5" style="padding-top:2px; width:200px;border-right:1px solid #000000;"><textarea name="pt_pcp_msg_'+consult_id_val+'" id="pt_pcp_msg_'+consult_id_val+'" style="height:18px;width:180px;">'+pt_pcp_msg_val+'</textarea></td>';
								//html += '<td class="pl5" style="padding-top:2px; width:50px;border-right:1px solid #000000;"><input type="hidden" name="pt_pcp_attachment_'+consult_id_val+'" id="pt_pcp_attachment_'+consult_id_val+'" value="'+pt_pcp_attachment_val+'"></td>';
									
							}
							else
							{
								chkbx_id_comma += ","+consult_id_val+'_n'; 
								html += '<td class="pl5" style="padding-top:2px; width:150px;border-right:1px solid #000000;">&nbsp;</td>';
								html += '<td class="pl5" style="padding-top:2px; width:200px;border-right:1px solid #000000;">&nbsp;</td>';
								html += '<td class="pl5" style="padding-top:2px; width:200px;border-right:1px solid #000000;">&nbsp;</td>';
								//html += '<td class="pl5" style="padding-top:2px; width:50px;border-right:1px solid #000000;">&nbsp;</td>';
							}
							
							html += '	</tr>';
						}
					}
				}
				if(selected == 0 )
				{
					top.show_loading_image('hide');
					top.fAlert('Please Select Records');
					return;
				}
				if(objLength >0 && consult_option=="consult_direct") {
					html += '</table>';
				}
				
				if(chkbx_id_comma && consult_option=="consult_direct") {
					chkbx_id_comma = chkbx_id_comma.substr(1);
					
					document.frm_pt_consult_letter_direct.hidd_direct_consult_id_comma.value = chkbx_id_comma;
					/*if(document.getElementById("consult_direct_div")) {
						document.getElementById("send_direct_div").style.display = "inline-block";
						document.getElementById("consult_direct_div").innerHTML = 	html;
						$("#send_direct_div").draggable();
					}*/
		/*			if($("#consult_direct_div").length > 0) {
						$("#send_direct_div").show();
						$("#consult_direct_div").html(html);
						//$("#send_direct_div").draggable();
					}
				}
				top.show_loading_image('hide');
				
			}
            
            function sndDirectConsultFun(){
				$_directCounter = 0
				top.show_loading_image('show','300', 'Sending Direct Message...');
				if(document.getElementById("hidd_direct_consult_id_comma")) {
					var consult_id_comma = $("#hidd_direct_consult_id_comma").val();
				}
				var consult_id_arr = consult_id_comma.split(",");
				var consult_id=""; var field_name = '';
				for(var u=0; u < consult_id_arr.length;u++) 
				{
					var $_arr = consult_id_arr[u].split("_");
					var $_cid = $_arr[0];
					var $_est = $_arr[1];
					sendConsultDirectAjax($_cid,$_est,consult_id_arr.length);
				}
			}
            
            function sendConsultDirectAjax($_cid,$_est,totalCnt){
				if($_est === 'n')
				{
					$_directCounter++;
					showTrStatus("td_dir_status_"+$_cid,'0');
					if($_directCounter == totalCnt ) {
						top.show_loading_image('hide');
						top.fAlert('Direct Process Completed.');
					}	
				}
				else
				{
					var $_url = '../../reports/consult_letters_report_direct.php?consultId='+$_cid;
					$_url += '&toEmail=' + encodeURI($("#pt_pcp_email_"+$_cid).val());
					$_url += '&subject=' + encodeURI($("#pt_pcp_subject_"+$_cid).val());		
					$_url += '&message=' + encodeURI($("#pt_pcp_msg_"+$_cid).val());
					$_url += '&pcpId=' + $("#pt_pcp_id_"+$_cid).val();		
				
					$.ajax({
						type: "POST",
						url: $_url,
						success: function(r){
							$_directCounter++;
							showTrStatus("td_dir_status_"+$_cid,r);
							if($_directCounter == totalCnt ) {
								top.show_loading_image('hide');
								top.fAlert('Direct Process Completed.');
							}	
						},
						error:function()
						{
							$_directCounter++;
							showTrStatus("td_dir_status_"+$_cid,'0');
							if($_directCounter == totalCnt ) {
								top.show_loading_image('hide');
								top.fAlert('Direct Process Completed.');
							}
						}
					});
				}
			}
            
            var sendSuccess = 0;
			var cnt=0;
			function getFxFun(){
				top.show_loading_image('show','300', 'Sending Fax...');
				if($("#hidd_fax_consult_id_comma").length > 0) {
					var fax_consult_id_comma = $("#hidd_fax_consult_id_comma").val();
				}
				var fax_consult_id_arr = new Array();
				var fxNmbr = "";
				
				if(fax_consult_id_comma) {
					$.ajax({
						type: "POST",
						url: "consult_letters_diabetic_exam.php?chkbx_id_comma="+fax_consult_id_comma+"&hidd_consult_mod=consult_fax",
						success: function(resp){
							fax_consult_id_arr = fax_consult_id_comma.split(",");
							
							var  fax_consult_id=""; var field_name = '';
							for(var q=0;q<fax_consult_id_arr.length;q++) {
								fax_consult_id = fax_consult_id_arr[q].trim();
								field_name = "pt_pcp_fax_no_"+fax_consult_id;
									
								if($("#pt_pcp_fax_no_"+fax_consult_id).length > 0) {
									if($("#pt_pcp_fax_no_"+fax_consult_id).val() !="") {
										cnt++;
									}
								}
							}
							var counter = 0; var  fax_consult_id=""; var field_name = '';
							for(var k=0;k<fax_consult_id_arr.length;k++) {
								fax_consult_id = fax_consult_id_arr[k].trim();
								field_name = "pt_pcp_fax_no_"+fax_consult_id;
									
								if($("#pt_pcp_fax_no_"+fax_consult_id).length > 0) {
									if($("#pt_pcp_fax_no_"+fax_consult_id).val() !="") {
										counter++;
										fxNmbr	= $("#pt_pcp_fax_no_"+fax_consult_id).val();
										pcpId	= $("#pt_pcp_id_"+fax_consult_id).val();
										//isLast	= (counter == (cnt) ? true : false);
										sndFxFun(fxNmbr,fax_consult_id,pcpId);
									}
								}
							}
							
						}
					});					
					
				}
			}
            
            function sndFxFun(fxNmbr,hid_consult_letter_ids,pcpId){
				if(fxNmbr) { 
					$.ajax({
						type: "GET",
						//url: "../../common/new_html2pdf/createPdf.php?saveOption=fax&name=faxConsultLetterReportPdf_"+hid_consult_letter_ids+"&htmlFileName=faxConsultReport_"+hid_consult_letter_ids,
						url: "'../../../library/html_to_pdf/createPdf.php?onePage=false&op=l&saveOption=fax&name=faxConsultLetterReportPdf_"+hid_consult_letter_ids+"&file_location=faxConsultReport_"+hid_consult_letter_ids,
						success: function(resp,textStatus,xhr){
							if(textStatus == 'success')
							{
							$.ajax({
								type: "GET",
								url: "../../reports/consult_letters_report_fax.php?send_fax_number="+fxNmbr+"&txtFaxPdfName=faxConsultLetterReportPdf_"+hid_consult_letter_ids+"&txtFaxHtmlName=faxConsultReport_"+hid_consult_letter_ids+"&consult_letter_ids="+hid_consult_letter_ids+"&hidd_consult_mod=consult_fax&pcp_id="+pcpId+"&send_fax_subject="+$("#send_fax_subject").val(),
								success: function(r){ sendSuccess++;
									if(parent.document.getElementById("div_load_image")) {
										parent.document.getElementById("div_load_image").style.display="none";
										parent.document.getElementById("hiddselectReferringPhy").value="";
										parent.document.getElementById("selectReferringPhy").value="";
										parent.document.getElementById("send_fax_number").value="";
										parent.document.getElementById("send_fax_div").style.display="none";
									}
									
									var rArr = r.split('@@');
									var o = $("#td_fax_status_"+hid_consult_letter_ids);
									if(rArr[0] == '1'){
										var h = '&#x2714;';	o.style.color='green'; o.style.fontSize='20px';
									}else{
										var h = '&#x2718;';	o.style.color='red'; o.style.fontSize='17px';
									}
									o.innerHTML = h;
									
									if(sendSuccess == cnt) {
										top.show_loading_image('hide');
										top.fAlert('Fax Process Completed.');
									}
									
									
								}
							});
							}
						},
						error:function()
						{
							sendSuccess++;
							var o = $("#td_fax_status_"+hid_consult_letter_ids);
							var h = '&#x2718;';	o.style.color='red'; o.style.fontSize='17px';
							o.innerHTML = h;
									
							if(sendSuccess == cnt) {
								top.show_loading_image('hide');
								top.fAlert('Fax Process Completed.');
							}	
						}
					});
				}
			}
            
            function showTrStatus($_fieldId,$_type){
				var o = document.getElementById($_fieldId);
				if($_type == '1'){
					var h = '&#x2714;';	o.style.color='green'; o.style.fontSize='20px';
				}else{
					var h = '&#x2718;';	o.style.color='red'; o.style.fontSize='17px';
				}	
				
				o.innerHTML = h;
				return o.innerHTML;
			} */
            
            
        </script>
    </body>
</html>