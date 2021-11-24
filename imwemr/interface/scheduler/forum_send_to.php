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

?><?php
/*
File: forum_send_to.php
Purpose: For send Forum
Access Type: Direct
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
$forum_btn_val = 'FORUM';
$user_join_condition = " AND u.user_type IN (5)";
if(constant("HIE_FORUM") == "YES"){
	$forum_btn_val = 'HIE';
	$user_join_condition = "";
}
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


if((count($arrPhyId) > 0) && (count($arrFacId) > 0)){
	$strPhyId = $strFacId = "";
	$strPhyId = implode(",",$arrPhyId); //condition in query to check user_type IN (test)
	$strFacId = implode(",",$arrFacId);
	$q1 = "SELECT sa.id AS sch_id, 
			sp.proc AS sch_proc, 
			DATE_FORMAT(sa.sa_app_starttime,'%h:%i %p') AS sch_time, 
			CONCAT_WS(', ',u.lname,u.fname) AS sch_phy, 
			fac.name as sch_fac, 
			CONCAT(pd.lname,', ',pd.fname) AS patient_name, 
			sa.sa_patient_id AS patient_id, sa.sa_comments, sa.procedure_site, 
			hl7.sent, hl7.status, hl7.response, hl7.status_text 
			FROM schedule_appointments sa 
			LEFT JOIN slot_procedures sp ON (sp.id = sa.procedureid) 
			JOIN users u ON (u.id = sa.sa_doctor_id".$user_join_condition.") 
			LEFT JOIN facility fac ON (fac.id = sa.sa_facility_id) 
			LEFT JOIN patient_data pd ON (pd.id = sa.sa_patient_id) 
			LEFT JOIN hl7_sent hl7 ON (hl7.sch_id=sa.id AND hl7.send_to='".$forum_btn_val."') 
			WHERE sa.sa_doctor_id IN (".$strPhyId.") 
			AND sa.sa_facility_id IN (".$strFacId.") 
			AND sa.sa_app_start_date = '".$rqDated."' 
			AND sa.sa_patient_app_status_id NOT IN(201,18,19,20,203) 
			order by sa.sa_app_start_date, sa.sa_app_starttime";
	$res1 = imw_query($q1);
	if($res1){
		$numrecords1 = imw_num_rows($res1);
	}else{
		echo imw_error();exit;	
	}
}
?>
<!Doctype html>
<html>
    <head>
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" type="text/css" rel="stylesheet">
    <style type="text/css">
		#form_rows td{vertical-align:top;}
	</style>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/messi/messi.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/common.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
           // show2(); //start the timer
			$('#sel_all').click(function(){
				$('.chk').prop('checked',$(this).is(":checked"));
			});
        });
		</script>
</head>
<body>
<div class="container-fluid">
	<div class="whtbox" >
    	<div class="pd5">
        	<div class="row eleg">
            	<div class="col-sm-12"><h3>Send to <?php echo $forum_btn_val;?></h3></div>
            </div>
            <div style="height:<?php echo $_SESSION['wn_height'] - 235; ?>px; overflow:auto">
            <form name="frm_to_forum" id="frm_to_forum">
            <table class="table table-striped table-bordered table-hover adminnw">
            <thead>
            <tr>
                <th style="width:30px;"><div class="checkbox"><input type="checkbox" name="sel_all" id="sel_all"><label for="sel_all"></label></div></th>
                <td style="width:250px;">Patient Name&nbsp;-&nbsp;ID</td>
                <td style="width:180px;">Appt. Proc</td>
                <td style="width:100px;">Appt. Time</td>
                <td style="width:100px;">Site</td>
                <td style="width:180px;">Physician Name</td>
                <td style="width:180px;">Location</td>
                <td style="width:auto;">Status</td>
            </tr>
            </thead>
            <tbody>          
                <?php
                if($res1 && $numrecords1>0){
					while($rs1 = imw_fetch_assoc($res1)){
						$status = "";
						$status = '&lt;not sent&gt; ';
						if($rs1['sent']=='1'){$status = 'Sent. ';}
						if($rs1['status']=='Y'){$status .= 'Accepted. ';}
						else if($rs1['status']=='N'){$status .= 'Rejected. '.$rs1['status_text'].' ';}
						else if($rs1['sent']=='1' && trim($rs1['response'])!=''){$status .= '['.$rs1['response'].']';}
						?>
						<tr>
                        	<td><div class="checkbox"><input type="checkbox" name="sch_id[]" id="chksch_id_<?php echo $rs1['sch_id'];?>" value="<?php echo $rs1['sch_id'];?>" class="chk"><label for="chksch_id_<?php echo $rs1['sch_id'];?>"></label></div></td>
							<td><?php echo $rs1['patient_name'].' - '.$rs1['patient_id'];?></td>
							<td><?php echo $rs1['sch_proc'];?></td>
							<td><?php echo $rs1['sch_time'];?></td>
							<td><?php echo $rs1['procedure_site'];?></td>
							<td><?php echo $rs1['sch_phy'];?></td>
							<td><?php echo $rs1['sch_fac'];?></td>
							<td id="status_td_<?php echo $rs1['sch_id'];?>"><?php echo $status;?></td>
						</tr>
			<?php 	}
				}else{?>
                    <tr>
                        <td class="warning alignCenter text12b">
                            No appointment found with imaging order.
                        </td>
                    </tr>
               <?php }
				   imw_free_result($rsGetPatEl);?>
               </tbody>
               </table>
               </form>
            </div>
		</div>
	</div>

    <div class="text-center">
       	<?php if($res1 && $numrecords1>0){?>
		<input type="button" class="btn btn-success" name="btn_send" id="btn_send" value="Send to <?php if(constant("HIE_FORUM") == "YES"){echo 'HIE';}else{echo 'Forum';}?>" onClick="send_to_forum();"/>&nbsp;&nbsp;&nbsp;
		<input type="button" class="btn btn-print" name="btn_print" id="btn_print" value="Print" onClick="document.print_pdf_frm.submit();"/>&nbsp;&nbsp;&nbsp;
		<?php }?>
		<input type="button" class="btn btn-danger" value="Close" onClick="window.close();"/>
    </div>
    <form action="forum_print_pdf.php" target="_blank" name="print_pdf_frm">
        <input type="hidden" name="phyId" value="<?php echo $_REQUEST["phyId"];?>">
        <input type="hidden" name="facId" value="<?php echo $_REQUEST["facId"];?>">
        <input type="hidden" name="dated" value="<?php echo $_REQUEST["dated"];?>">
    </form>

    </body>
    <script language="javascript">
		function send_to_forum(){
			var arr_sch_id = new Array();
			var i = 0;
			$('.chk').each(function(index, element) {
				if($(this).is(":checked")){
					arr_sch_id[i] = $(this).val();
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
			sch_id = arr_sch_id[j];
			td_obj = $('#status_td_'+sch_id);
			td_obj.html('<span>processing...</span>');
			$.ajax({
				type: "GET",
				url: "forum_ajax.php?sch_id="+sch_id,
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