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
//require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
//require_once($GLOBALS['fileroot'].'/library/classes/billing_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/class.electronic_billing.php');
$objEBilling = new ElectronicBilling();

$rqPhyId = $_REQUEST["phyId"];
$rqFacId = $_REQUEST["facId"];
$rqDated = $_REQUEST["dated"];
$defaultCaseId = ((int)$_REQUEST['comboChoseCase'] > 0) ? (int)$_REQUEST['comboChoseCase'] : 0;
$blDefaultSet = false;

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
<!Doctype html>
<html>
    <head>
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">
        
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" type="text/css" rel="stylesheet">
      
    	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap-dropdownhover.min.js"></script>
        
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
        <!--jquery to suport discontinued functions-->
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery-migrate-1.2.1.js"></script> 
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
        
        <script type="text/javascript">
		$(document).ready(function() {
     		$('#sel_all').click(function(){
				$('.chk').attr('checked',$(this).is(":checked"));
			});
        });
		function reload_data(val){
			selectedPhy = '<?php echo $_REQUEST["phyId"];?>';
			selectedFac	= '<?php echo $_REQUEST["facId"];?>';
			selectedDate= '<?php echo $_REQUEST["dated"];?>';
			selectedCase= val;
			window.location.href = 'pre_auth_send.php?phyId='+selectedPhy+'&facId='+selectedFac+'&dated='+selectedDate+'&comboChoseCase='+selectedCase;
		}
		</script>
    </head>
    <body style="overflow-x:hidden;">
    <div class="container-fluid">
    
    	<div class="whtbox" >
    	<div class="pd5">
    	<div class="row eleg">
        	<div class="col-sm-6"><h3>Pre-Authorization</h3></div>
        	<div class="col-sm-3 form-inline text-right">
            	<div class="form-group">
                	<label>Pre-Auth. Template </label>
                	<select name="pre_auth_template" id="pre_auth_template" class="form-control minimal">
                    <option value="">-SELECT-</option>
                    <?php $q_template = "SELECT id,template_name FROM pre_auth_templates WHERE del_status='0'";
                    $res_template = imw_query($q_template);
                    while($rs_template = imw_fetch_assoc($res_template)){
                        echo '<option value="'.$rs_template['id'].'">'.$rs_template['template_name'].'</option>';
                    }
                    ?>
                	</select>
                </div>
            </div>
        	<div class="col-sm-3 form-inline text-right">
            	<div class="form-group">
                	<label>Ins. Case </label>
					<select name="comboChoseCase" id="comboChoseCase" class="form-control minimal" onChange="reload_data(this.value);">
					<?php
                    $qrySelCase = "select case_id, case_name, normal from insurance_case_types where status  = '0'";
                    $rsSelCase = imw_query($qrySelCase);
                    while($rowSelCase = imw_fetch_array($rsSelCase)){
                        $sel = "";
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
        </div>
        
        <div style="height:<?php echo ($_REQUEST['height'])?$_REQUEST['height']-150:$_SESSION['wn_height'] - 380; ?>px; overflow:auto;" class="whtbox">
            <form name="frm_to_forum" id="frm_to_forum">
            <table class="table table-striped table-bordered table-hover adminnw" id="form_rows">
            <thead>
            <tr>
                <th>
                <div class="checkbox">
                    <input id="sel_all" name="sel_all" type="checkbox">
                    <label for="sel_all"></label>
                </div></th>
                <th>App. Date</th>
                <th>App. Time</th>
                <th>Physician Name</th>
                <th>Location</th>
                <th>Patient Name-ID</th>
                <th>Ins Practice Name-Policy #</th>
                <th>Pre-Auth. Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if((count($arrPhyId) > 0) && (count($arrFacId) > 0)){
                $intTempCounter = 0;
                $rqPhyId = $rqFacId = "";
                $rqPhyId = implode(",",$arrPhyId);
                $rqFacId = implode(",",$arrFacId);
                $qryGetPatEl = "select 
                                sa.id as patAppIdSA, sa.case_type_id as patAppCaseId, sa.sa_doctor_id, sa.sa_patient_id, 
                                DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format()."') as saDate, DATE_FORMAT(sa.sa_app_starttime, '%h:%i %p') as saTime,
                                CONCAT_WS(', ',usSA.lname,usSA.fname) as saPhy,
                                fac.name as saFac,
                                CONCAT(pd.lname,', ',pd.fname) as patName, sa.sa_patient_id as patIdSA,
                                insComp.name as insCompName,insComp.claim_type as insCompType,insData.provider as patInsProvider, insData.id as insRecId, insData.policy_number as insPatPolicyNo, 
                                insData.ins_caseid as insPatCaseId, insData.id as insDataId, 
                                grp.gro_id as group_id
                                
                                FROM schedule_appointments sa
                                LEFT JOIN users usSA ON (usSA.id = sa.sa_doctor_id) 
                                LEFT JOIN facility fac ON (fac.id = sa.sa_facility_id)   
                                LEFT JOIN groups_new grp ON (grp.gro_id = fac.default_group and grp.del_status='0') 
                                LEFT join patient_data pd ON (pd.id = sa.sa_patient_id) 
                                LEFT JOIN insurance_case insCase ON (insCase.patient_id = sa.sa_patient_id)   
                                LEFT JOIN insurance_data insData ON (insData.pid = sa.sa_patient_id and insData.ins_caseid = insCase.ins_caseid) 
                                LEFT JOIN insurance_companies insComp ON (insComp.id = insData.provider) 
                                
                                WHERE sa.sa_doctor_id in (".$rqPhyId.") and sa.sa_facility_id in (".$rqFacId.") and sa.sa_app_start_date = '".$rqDated."' 
                                AND sa.sa_patient_app_status_id NOT IN(201,18,19,20,203) 
                                AND insData.actInsComp = '1' AND insData.type = 'primary'
                                AND insData.ins_caseid > 0
                                AND insCase.ins_case_type = '".$defaultCaseId."' 
                                order by sa.sa_app_start_date, sa.sa_app_starttime";
                $rsGetPatEl = imw_query($qryGetPatEl);
                if($rsGetPatEl){
                    if(imw_num_rows($rsGetPatEl) > 0){
                        if(imw_num_rows($rsGetPatEl) > 0){
                            while($rowGetPatEl = imw_fetch_array($rsGetPatEl)){
                                $dbAppDate = $dbAppTime = $dbElOpName = $dbInsPatPolicyNo = $dbElDDE = $dbElCopay = $dbElCoins = $dbElResponce = $dbElTranError = "";
                                $dbSAPhy = $dbSAFac = $dbPatName = $dbPatID = $dbPatInsCompName = $strEBResponce = "";
                                $dbPatAppIdSA = $dbInsRecId = $dbInsCompType = "";
                                $dbPatInsProvider = $dbInsCompType = 0;

                                $dbPatAppIdSA = $rowGetPatEl["patAppIdSA"];
                                $dbAppDate = $rowGetPatEl["saDate"];
                                $dbAppTime = $rowGetPatEl["saTime"];
                                $dbSAPhy = $rowGetPatEl["saPhy"];
                                $dbSAFac = $rowGetPatEl["saFac"];
                                $dbPatName = $rowGetPatEl["patName"];
                                $dbPatID = $rowGetPatEl["patIdSA"];
                                $dbPatInsCompName = $rowGetPatEl["insCompName"];
                                $responsetext	= '&lt;Not Checked&gt;';
                                $cpa_res 		= imw_query("SELECT response_data, response_details FROM claim_pre_auth WHERE sch_id = '".$dbPatAppIdSA."' ORDER BY id DESC LIMIT 1");
                                if($cpa_res && imw_num_rows($cpa_res)>0){
                                    $cpa_rs			= imw_fetch_assoc($cpa_res);
                                    if($cpa_rs['response_details']!=''){
                                        $response 	= unserialize(html_entity_decode(stripslashes($cpa_rs['response_details'])));
                                        $responsetext	= $objEBilling->responseArray2Text($response);
                                    }else if($cpa_rs['response_data']!=''){
                                        $response	= $objEBilling->read278EDI($cpa_rs['response_data']);
                                        $responsetext	= $objEBilling->responseArray2Text($response);
                                    }
                                    
                                }else{
                                    
                                }

                                
                                $dbInsPatPolicyNo = $rowGetPatEl["insPatPolicyNo"];
                                $dbInsRecId = $rowGetPatEl["insRecId"];
                                
                                $dbPatInsProvider = $rowGetPatEl["patInsProvider"];
                                ?>
                                <tr>
                                    <td>
                                    <div class="checkbox">
                                        <input name="sch_id[]" id="sch_id<?php echo $intTempCounter?>" type="checkbox" value="<?php echo $dbPatAppIdSA.'~~'.$rowGetPatEl['sa_patient_id'].'~~'.$rowGetPatEl['sa_doctor_id'].'~~'.$rowGetPatEl['group_id'].'~~'.$rowGetPatEl['insDataId'].'~~'.$rowGetPatEl['saDate'];?>" class="chk">
                                        <label for="sch_id<?php echo $intTempCounter?>"></label>
                                    </div></td>
                                    <td><?php echo $dbAppDate; ?></td>
                                    <td><?php echo $dbAppTime; ?></td>
                                    <td><?php echo $dbSAPhy; ?></td>
                                    <td><?php echo $dbSAFac; ?></td>
                                    <td><?php echo $dbPatName."&nbsp;-&nbsp;".$dbPatID; ?></td>
                                    <td><?php echo $dbPatInsCompName."<br>".$dbInsPatPolicyNo; ?></td>
                                    <td id="status_td_<?php echo $dbPatAppIdSA;?>"><?php echo $responsetext;?></td>
                                </tr>
                                <?php
                                $intTempCounter++;										
                            }
                        }
                    }
                    else{
                        ?>
                        <tr>
                            <td colspan="10">
                                No Patient History Exits For Eligibility!
                            </td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            </tbody>
           </table>
           </form>
    	</div>
      
		<form action="forum_print_pdf.php" target="_blank" name="print_pdf_frm">
			<input type="hidden" name="phyId" value="<?php echo base64_decode($_REQUEST["phyId"]);?>">
			<input type="hidden" name="facId" value="<?php echo $_REQUEST["facId"];?>">
			<input type="hidden" name="dated" value="<?php echo $_REQUEST["dated"];?>">
		</form>
		</div>
        </div>
        <div>
            <div class="text-right">
            <?php 
            //if($intTempCounter>0){
                ?>
            <button class="btn btn-success" name="btn_send" id="btn_send" value="Check Pre-Authorization" onClick="check_preauth();">Check Pre-Authorization</button>&nbsp;&nbsp;
            <!--<button class="btn btn-default" name="btn_print" id="btn_print" value="Print" onClick="document.print_pdf_frm.submit();">
            <span class="glyphicon glyphicon-print"></span> Print</button>-->
            <?php 
           // }
            ?>
            <button class="btn btn-danger" value="Close" onClick="window.close();">Close</button>&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
           
        </div>
    </div>
    </body>
    <script language="javascript">
		function check_preauth(){
			var arr_sch_id = new Array();
			var i = 0;
			$('.chk').each(function(index, element) {
				if($(this).attr('checked')){
					chk_val			= $(this).val();
					arr_sch_id[i] 	= chk_val;
					i++;
				}
			});
			if(arr_sch_id.length>0){
				j = 0;
				ajax_request(arr_sch_id,j);
			}else{
				alert('No Record Selected.');
			}
		}
		function ajax_request(arr_sch_id,j){
			template_id	= $('#pre_auth_template').val();	
			if(template_id==''){alert('Please select Pre-Auth. Template'); return;}	
			chk_val	= arr_sch_id[j];
			chk_val_arr	= chk_val.split('~~');
			sch_id = chk_val_arr[0];
			td_obj = $('#status_td_'+sch_id);
			td_obj.html('<span class="doing"></span>');
			$.ajax({
				type: "GET",
				url: "pre_auth_ajax.php?pre_auth_str="+chk_val+'&template_id='+template_id,
				success: function(d){
					td_obj.html(d);
					if(j<(arr_sch_id.length -1)){
						j++;
						ajax_request(arr_sch_id,j);
					}else if(j==(arr_sch_id.length -1)){
						alert('Process Finished.');
					}
				}
			});
		}
	
	</script>
</html>