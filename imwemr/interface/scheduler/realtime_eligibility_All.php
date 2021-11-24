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
require_once(dirname(__FILE__).'/../../config/globals.php');

$rqPhyId = $rqFacId = $rqDated = "";
$rqPhyId = $_REQUEST["phyId"];
$rqFacId = $_REQUEST["facId"];
$rqDated = $_REQUEST["dated"];
list($year, $month, $day) = explode("-", $rqDated);
$day_name = date("l", mktime(0, 0, 0, $month, $day, $year));
if(strlen($day) == 1){
	$day = "0".$day;
}
$rqDated = $year."-".$month."-".$day;
$arrPhyId = $arrFacId = array();
$arrPhyId = explode(",",$rqPhyId);
foreach($arrPhyId as $key => $val){
	if(empty($val) == true){
		unset($arrPhyId[$key]);
	}
}
$arrPhyId = array_values($arrPhyId);
$arrFacId = explode(",",$rqFacId);
foreach($arrFacId as $key => $val){
	if(empty($val) == true){
		unset($arrFacId[$key]);
	}
}
$arrFacId = array_values($arrFacId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" type="text/css" rel="stylesheet">
    
    
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/messi/messi.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/common.js"></script><!--
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/admin/menuIncludes_menu/js/disableBackspace.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/common/script_function.js"></script> -->
    <script language="javascript">
        window.focus();
        
		$(document).ready(function() {
     		$('#cbkAll').click(function(){
				$('.chk').prop('checked',$(this).is(":checked"));
			});
        });
		
        function chk(obj){
            if((document.getElementById("cbkAll").checked == true) && (obj.checked == false)){
                document.getElementById("cbkAll").checked = false;
            }
        }
        function doAllEligibility(){
            var field = document.getElementsByName("cbkRTE");
            var blChkExits = false;
            $(field).each(function(){
                if(this.checked == true){
                    blChkExits = true;
                }
             });
            if(!blChkExits){
                top.fAlert("Please select minimum one patient to ask Eligibility precede start process!");
                return;
            }else{
                $("#btStPro").prop('disabled',true);
                var checkboxes = document.getElementsByName("cbkRTE");//id="cbk-114427" name="cbkRTE" value="84280~0~114427~2016-02-16"
                var checked_Array = new Array();
                $(checkboxes).each(function(index, element) {
                    if($(this).prop('checked')==true){
                        checked_Array[checked_Array.length] = $(this).val();
                    }
                });
                j=0;
                checkRTE(checked_Array,j);			
            }
        }
        
        function checkRTE(checked_Array,j){
            curr_record 		= checked_Array[j]; //84280~0~114427~2016-02-16
            curr_record_ele 	= curr_record.split('~');
            
            //FETCHING QUERY STRING DATA
            insRecId			= curr_record_ele['0'];
            if(curr_record_ele['1'] == "0"){
                askElFrom = "1";
            }
            else if(curr_record_ele['1'] == "1"){
                askElFrom = "0";
            }
            schId				= curr_record_ele['2'];
            strAppDate			= curr_record_ele['3'];
            strRootDir = "<?php echo $GLOBALS['rootdir']; ?>";
            
            //SETTING DISPLAY STATUS FOR CURRENT ROW
            objTd = $('#td-'+schId); objTd.html('');
            ajaxDone = $('#ajaxDone-'+schId); ajaxDone.hide();
            ajaxload = $('#ajax-'+schId); ajaxload.show();
            
            //SENDING REQUEST
            document.getElementById("btStPro").disabled = true;
            $.ajax({
                url: strRootDir +'/patient_info/ajax/make_270_edi.php?action=ins_eligibility&insRecId='+insRecId+'&askElFrom='+askElFrom+'&schId='+schId+'&strAppDate='+strAppDate,
                success: function(res){
					res = JSON.parse(res);
					d=res.data;
                    //PROCESSING RESPONSE
                    arrResp = d.split("~~");
                    if(arrResp[0] == "1" || arrResp[0] == 1){
                        var alertResp = "";
                        if(arrResp[1] != ""){
                            alertResp += "Patient Eligibility Or Benefit Information Status :"+arrResp[1]+"\n";
                        }
                        if(arrResp[2] != ""){
                            alertResp += "With Insurance Type Code :"+arrResp[2]+"\n\n";
                        }
                        if(alertResp != ""){
                            if(arrResp[3] == "A"){
                                objTd.css({'color':'green','font-weight':'bold'});
                                objTd.html("Active");
                            }
                            else if(arrResp[3] == "INA"){
                                objTd.css({'color':'red','font-weight':'bold'});
                                objTd.html("Inactive");
                            }
                            objTd.prop('title',alertResp);
                            //alert(alertResp);
                        }
                    }
                    else if(arrResp[0] == "2" || arrResp[0] == 2){						
                        if(arrResp[1] != ""){
                            objTd.css({'color':'black','font-weight':'bold'});
                            objTd.html("Error");
                            objTd.prop('title',arrResp[1]);
                            //alert(arrResp[1]);
                        }
                    }
                    else{
                        objTd.css({'color':'black','font-weight':'bold'});
                        objTd.html("Error");
                        objTd.prop('title',arrResp[0]);
                    }
                    ajaxDone.show();
                    ajaxload.hide();			
                    
                    if(j<(checked_Array.length -1)){
                        j++;
                        checkRTE(checked_Array,j);
                    }else if(j==(checked_Array.length -1)){
                        top.fAlert('Process Finished.');
                        document.getElementById("btStPro").disabled = false;
                    }
                }
            });
        }
    </script>      
</head>
<body>
    	<form name="frmEligibility" id="frmEligibility" action="">
            <input type="hidden" name="phyId" value="<?php echo $_REQUEST["phyId"]; ?>" />
            <input type="hidden" name="facId" value="<?php echo $_REQUEST["facId"]; ?>" />
            <input type="hidden" name="dated" value="<?php echo $_REQUEST["dated"]; ?>" />
            <div class="container-fluid">
			
               
                <div class="whtbox" >
                <div class="pd5"><div class="row eleg">
                <div class="col-sm-9  ">
                    <h3>Eligibility for: <?php echo get_date_format($rqDated); ?></h3>
                </div>
                <div class="col-sm-3 form-inline text-right">
                    Insurance Cases: 
                    <?php 
                    $defaultCaseId = ((int)$_REQUEST['comboChoseCase'] > 0) ? (int)$_REQUEST['comboChoseCase'] : 0;
                    $blDefaultSet = false;
                    ?>
                    <select name="comboChoseCase" id="comboChoseCase" class="form-control minimal" onChange="document.frmEligibility.submit();" >
                    <?php
                    $qrySelCase = "select case_id, case_name, normal from insurance_case_types where status  = '0'";
                    $rsSelCase = imw_query($qrySelCase);
                    while($rowSelCase = imw_fetch_assoc($rsSelCase)){
                        $dbCaseId = $dbCaseNormal = 0;
                        $dbCaseName = $sel = "";
                        $dbCaseId = $rowSelCase["case_id"];
                        $dbCaseName = $rowSelCase["case_name"];
                        $dbCaseNormal = $rowSelCase["normal"];
                        if(($dbCaseNormal == 1) && ($defaultCaseId == 0) && ($blDefaultSet == false)){
                            $defaultCaseId = $dbCaseId;
                            $sel = 'selected="selected"';
                            $blDefaultSet = true;
                        }
                        elseif(((int)$_REQUEST['comboChoseCase'] == $dbCaseId) && ($blDefaultSet == false)){
                            $sel = 'selected="selected"';
                            $blDefaultSet = true;
                        }
                        echo '<option value="'.$dbCaseId.'" '.$sel.'>'.$dbCaseName.'</option>';
                    }
                    ?>
                    </select>
                </div>
            </div>
                
                    <div style="height:<?php echo $_SESSION['wn_height'] - 270; ?>px; overflow:auto">
                    <table class="table table-striped table-bordered table-hover adminnw">
                    <thead>
                    <tr >
                        <th>
							<div class="checkbox">
								<input id="cbkAll" name="cbkAll" type="checkbox">
								<label for="cbkAll"></label>
							</div>
                        </th>
                        <th nowrap>App. Date</th>
                        <th nowrap>App. Time</th>
                        <th nowrap>Physician Name</th>
                        <th nowrap>Location</th>
                        <th nowrap>Patient Name-ID</th>
                        <th nowrap>Ins Practice Name-Policy #</th>
                        <th nowrap>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if((count($arrPhyId) > 0) && (count($arrFacId) > 0)){
                        $intTempCounter = 0;
                        $rqPhyId = $rqFacId = "";
                        $rqPhyId = implode(",",$arrPhyId);
                        $rqFacId = implode(",",$arrFacId);
                        /*$qryGetPatEl = "select 
						sa.id as patAppIdSA,  sa.rte_id as patRTEId , sa.case_type_id as patAppCaseId, 
						DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format()."') as saDate, DATE_FORMAT(sa.sa_app_starttime, '%h:%i %p') as saTime,
						CONCAT_WS(', ',usSA.lname,usSA.fname) as saPhy,
						fac.name as saFac,
						CONCAT(pd.lname,', ',pd.fname) as patName, sa.sa_patient_id as patIdSA,
						insComp.name as insCompName,insComp.claim_type as insCompType,insData.provider as patInsProvider, 
						insData.id as insRecId, insData.policy_number as insPatPolicyNo, insData.ins_caseid as insPatCaseId, 
						(SELECT rtme.id FROM real_time_medicare_eligibility rtme WHERE rtme.ins_data_id=insData.id AND EB_responce!='' ORDER BY rtme.request_date_time DESC LIMIT 0,1) AS rte_master_id, 
						(SELECT DATEDIFF('".date('Y-m-d')."',DATE_FORMAT(rtme.request_date_time,'%Y-%m-%d')) FROM real_time_medicare_eligibility rtme WHERE rtme.ins_data_id=insData.id AND EB_responce!='' ORDER BY rtme.request_date_time DESC LIMIT 0,1) AS rte_passed_days 
						
						from schedule_appointments sa
						INNER JOIN users usSA ON usSA.id = sa.sa_doctor_id 
						INNER JOIN facility fac ON fac.id = sa.sa_facility_id 
						INNER join patient_data pd on pd.id = sa.sa_patient_id 
						INNER JOIN insurance_case insCase ON insCase.patient_id = sa.sa_patient_id 
						INNER JOIN insurance_data insData ON insData.pid = sa.sa_patient_id and insData.ins_caseid = insCase.ins_caseid 
						INNER JOIN insurance_companies insComp ON insComp.id = insData.provider 
						
						
						where sa.sa_doctor_id in (".$rqPhyId.") and sa.sa_facility_id in (".$rqFacId.") and sa.sa_app_start_date = '".$rqDated."' 
						AND sa.sa_patient_app_status_id NOT IN(201,18,19,20,203) AND sa.rte_id = '0'
						AND insData.actInsComp = '1' AND insData.type = 'primary'
						AND insData.ins_caseid > 0 
						AND insData.ins_caseid = sa.case_type_id 
						AND insCase.ins_case_type = '".$defaultCaseId."' 
						ORDER BY sa.sa_app_start_date, sa.sa_app_starttime";*/
						$qryGetPatEl = "select 
						sa.id as patAppIdSA,  sa.rte_id as patRTEId , sa.case_type_id as patAppCaseId, 
						DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format()."') as saDate, DATE_FORMAT(sa.sa_app_starttime, '%h:%i %p') as saTime,
						CONCAT_WS(', ',usSA.lname,usSA.fname) as saPhy,
						fac.name as saFac,
						CONCAT(pd.lname,', ',pd.fname) as patName, sa.sa_patient_id as patIdSA,
						insComp.name as insCompName,insComp.claim_type as insCompType,insData.provider as patInsProvider, 
						insData.id as insRecId, insData.policy_number as insPatPolicyNo, insData.ins_caseid as insPatCaseId 						
						from schedule_appointments sa
						INNER JOIN users usSA ON usSA.id = sa.sa_doctor_id 
						INNER JOIN facility fac ON fac.id = sa.sa_facility_id 
						INNER join patient_data pd on pd.id = sa.sa_patient_id 
						INNER JOIN insurance_case insCase ON insCase.patient_id = sa.sa_patient_id 
						INNER JOIN insurance_data insData ON (insData.pid = sa.sa_patient_id and insData.ins_caseid = insCase.ins_caseid and insData.actInsComp = '1') 
						INNER JOIN insurance_companies insComp ON insComp.id = insData.provider 						
						where sa.sa_doctor_id in (".$rqPhyId.") and sa.sa_facility_id in (".$rqFacId.") and sa.sa_app_start_date = '".$rqDated."' 
						AND sa.sa_patient_app_status_id NOT IN(201,18,19,20,203) AND sa.rte_id = '0'
						AND insData.actInsComp = '1' AND insData.type = 'primary'
						AND insData.ins_caseid > 0 
						AND insData.ins_caseid = sa.case_type_id 
						AND insCase.ins_case_type = '".$defaultCaseId."' 
						ORDER BY sa.sa_app_start_date, sa.sa_app_starttime";
                        $rsGetPatEl = imw_query($qryGetPatEl);
                        
                        $ResPolicy = imw_query("select RTEValidDays from copay_policies where policies_id = '1'");
                        $RowPolicy = imw_fetch_assoc($ResPolicy);
                        $intRTEValidDays = intval($RowPolicy['RTEValidDays']);
                        
                        if($rsGetPatEl){
                            if(imw_num_rows($rsGetPatEl) > 0){
                                if(imw_num_rows($rsGetPatEl) > 0){
                                    while($rowGetPatEl = imw_fetch_array($rsGetPatEl)){
                                        $classTr = ($intTempCounter % 2) == 0 ? "bgcolor" : "";
                                        $dbAppDate = $dbAppTime = $dbElOpName = $dbInsPatPolicyNo = $dbElDDE = $dbElCopay = $dbElCoins = $dbElResponce = $dbElTranError = "";
                                        $dbSAPhy = $dbSAFac = $dbPatName = $dbPatID = $dbPatInsCompName = $strEBResponce = "";
                                        $dbRTEID = $dbPatAppIdSA = $dbInsRecId = $dbInsCompType = "";
                                        $rteChk = "No";
                                        $dbPatInsProvider = $dbInsCompType = 0;
										
										$db_rte_master_id = '';
										$dbRTEpassedDays = '';
										$q_rte = "SELECT ri.id AS rte_master_id, DATEDIFF('".date('Y-m-d')."', DATE_FORMAT(ri.request_date_time, '%Y-%m-%d')) AS rte_passed_days ";
										$q_rte .="FROM real_time_medicare_eligibility AS ri WHERE ri.ins_data_id = '".$rowGetPatEl["insRecId"]."' ";
										$q_rte .="AND (ri.EB_responce <> '' AND ri.EB_responce NOT LIKE '%error%') ";
										$q_rte .="ORDER BY ri.request_date_time DESC LIMIT 1";
										$res_rte = imw_query($q_rte);
										if($res_rte && imw_num_rows($res_rte)==1){
											$rs_rte = imw_fetch_assoc($res_rte);
											$db_rte_master_id = $rs_rte["rte_master_id"];
											$dbRTEpassedDays = $rs_rte["rte_passed_days"];	
										}
										
										
                                        $dbRTEID = $rowGetPatEl["patRTEId"];
                                      //  $db_rte_master_id = $rowGetPatEl["rte_master_id"];
                                      //  $dbRTEpassedDays = $rowGetPatEl["rte_passed_days"];
                                        $rte_checked_recently = false;
                                        if($intRTEValidDays>0 && $dbRTEpassedDays!=NULL && $dbRTEpassedDays<$intRTEValidDays){$rte_checked_recently=true;}
                                        $dbPatAppIdSA = $rowGetPatEl["patAppIdSA"];
                                        $dbAppDate = $rowGetPatEl["saDate"];
                                        $dbAppTime = $rowGetPatEl["saTime"];
                                        $dbSAPhy = $rowGetPatEl["saPhy"];
                                        $dbSAFac = $rowGetPatEl["saFac"];
                                        $dbPatName = $rowGetPatEl["patName"];
                                        $dbPatID = $rowGetPatEl["patIdSA"];
                                        $dbPatInsCompName = $rowGetPatEl["insCompName"];
                                        //$dbInsCompType = $rowGetPatEl["insCompType"];
                                        
                                        $dbInsPatPolicyNo = $rowGetPatEl["insPatPolicyNo"];
                                        $dbInsRecId = $rowGetPatEl["insRecId"];
                                        
                                        $intTotVSCertInsComp = 0;
                                        $dbPatInsProvider = $rowGetPatEl["patInsProvider"];
                                        $qryGetCertInfo = "SELECT ins_comp_id FROM vision_share_cert_config	WHERE ins_comp_id = '".(int)$dbPatInsProvider."' LIMIT 1 ";
                                        $rsGetCertInfo = imw_query($qryGetCertInfo);
                                        $intTotVSCertInsComp = imw_num_rows($rsGetCertInfo);
                                        if($intTotVSCertInsComp > 0){
                                            $dbInsCompType = 1;
                                        }
                                        elseif($intTotVSCertInsComp == 0){
                                            $dbInsCompType = 0;
                                        }
                                        ?>
                                        <tr class="<?php echo $classTr; ?>">
                                            <td>
                                               <div class="checkbox">
													<input id="cbk-<?php echo $dbPatAppIdSA; ?>" name="cbkRTE" type="checkbox" <?php if($rte_checked_recently){echo 'disabled="disabled" title="RTE checked '.$dbRTEpassedDays.' days ago."';}?> onClick="chk(this);" value="<?php echo $dbInsRecId."~".$dbInsCompType."~".$dbPatAppIdSA."~".$rqDated; ?>" class="chk">
													<label for="cbk-<?php echo $dbPatAppIdSA; ?>"></label>
												</div>
                                                
                                                <div class="fl" id="ajax-<?php echo $dbPatAppIdSA; ?>" style="display:none;"><img src="../../library/images/loading_image.gif" alt="Loading"></div>
                                                <div class="fl" id="ajaxDone-<?php echo $dbPatAppIdSA; ?>" style="display:none;"><img src="../../library/images/confirm.gif" alt="Done" ></div>
                                            </td>
                                            <td><?php echo $dbAppDate; ?></td>
                                            <td nowrap><?php echo $dbAppTime; ?></td>
                                            <td nowrap><?php echo $dbSAPhy; ?></td>
                                            <td><?php echo $dbSAFac; ?></td>
                                            <td><?php echo $dbPatName."&nbsp;-&nbsp;".$dbPatID; ?></td>
                                            <td><?php echo $dbPatInsCompName."<br>".$dbInsPatPolicyNo; ?></td>
                                            <td id="td-<?php echo $dbPatAppIdSA; ?>" ><?php if($rte_checked_recently){echo '<a onClick="top.window.opener.top.popup_win(\'../patient_info/eligibility/eligibility_report.php?id='.$db_rte_master_id.'\');" class="a_clr1 link_cursor">Checked '.$dbRTEpassedDays.' days ago.</a>';}?></td>
                                        </tr>
                                        <?php
                                        $intTempCounter++;										
                                    }
                                }
                            }
                            else{
                                ?>
                                <tr>
                                    <td class="text_10" colspan="10">
                                        No Patient History Exits For Eligibility!
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>
                    </tbody>
                    </table></div></div>
                </div>
             <div class="text-right">   
				<button type="button" class="btn btn-success" name="btStPro" id="btStPro" onClick="doAllEligibility();">Start Process</button>
				<button type="button" class="btn btn-danger" name="btClose" id="btClose" onClick="window.close();">Close</button>	
             </div>
             </div>
      </form>
    </body>
	
</html>