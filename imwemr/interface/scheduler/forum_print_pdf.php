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
//RESPONSE FROM SCHEDULER PRE-AUTHORIZATION PRINT BUTTON
require_once(dirname(__FILE__)."/../../config/globals.php");
require_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");

$forum_btn_val = 'FORUM';  //HEADING NAME
$user_join_condition = " AND u.user_type IN (5)"; //USER TYPE CONDITION ADDED
if(constant("HIE_FORUM") == "YES"){
	$forum_btn_val = 'HIE';
	$user_join_condition = "";
}
//DATA REQUEST FROM PRE-AUTHORIZATION FILE
$rqPhyId = $rqFacId = $rqDated = "";
$rqPhyId = base64_encode($_REQUEST["phyId"]);
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
	$strPhyId = implode(",",$arrPhyId); //CONDITION IN QUERY TO CHECK user_type IN (test)
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
	$res1 = mysql_query($q1);
	if($res1){ 
		$numrecords1 = mysql_num_rows($res1);
	}else{
		die('No record exists');	
	}
}
//===========CSS WORK STARTS HERE ====================
$styles = '
<page backtop="5mm" backbottom="5mm">
<page_footer>

<table style="width: 100%;">
	<tr>
		<td style="text-align:center;width:100%" class="text_value">Page [[page_cu]]/[[page_nb]]</td>
	</tr>
</table>
</page_footer>

<style>
.cellBorder{
	border-bottom:1px solid #333; border-right:1px solid #333; font-size:10px;
}
.leftBorder{
	border-left:1px solid #333;
}
td, .text_b_w, .text_lable, .text_value, .tb_subheading, .tb_heading, .tb_headingHeader{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
}
.text_b_w{
		
		font-weight:bold;
}
.text_lable{
		background-color:#FFFFFF;
		font-weight:bold;
}
.text_value, .text_value_sm{
		font-weight:100;
		vertical-align:top;
		background-color:#FFFFFF;
	}
.text_value_sm{
		font-size:10px;
	}

.paddingTop{
	padding-top:5px;
}

.tb_subheading{
	font-weight:bold;
	padding:2px 0px 2px 2px;
	color:#000000;
	background-color:#dddddd;;
}
.tb_heading{
	font-size:13px;
	font-weight:bold;
	color:#012778;
	margin-bottom:5px;
}
.tb_headingHeader{
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684ab;
}
.alignCenter{text-align:center;}
.bluehed{ 
	font-size:14px;
	color:#012778;
	font-weight:bold;
}
.backgroundHeading{
	background-color:#dbdbdb;
	color:#012778;
	font-weight:bold;
	padding-top:2px;
}
</style>';
//===========HTML WORK STARTS HERE ====================
$page_html	= '<div style="width:740px; text-align:center;" class="tb_heading">'.$forum_btn_val.' Orders</div>';
$page_html .= '<table cellpadding=0 cellspacing=0 style="width:740px" border="0">
                <tr>
                    <th class="backgroundHeading bluehed" style="width:15px;">#</th>
                    <td class="backgroundHeading bluehed" style="width:150px;">Patient Name&nbsp;-&nbsp;ID</td>
                    <td class="backgroundHeading bluehed" style="width:120px;">Appt. Proc</td>
                    <td class="backgroundHeading bluehed" style="width:100px;">Appt. Time</td>
                    <td class="backgroundHeading bluehed alignCenter" style="width:60px;">Site</td>
                    <td class="backgroundHeading bluehed" style="width:120px;">Physician Name</td>
                    <td class="backgroundHeading bluehed" style="width:110px;">Location</td>
                    <td class="backgroundHeading bluehed" style="width:auto;text-align:left;">Status </td>
				</tr>';
                if($res1 && $numrecords1>0){
					$sno = 0;
					while($rs1 = mysql_fetch_assoc($res1)){
						$classTr = $classTr == "" ? "alt" : "";
						$status = "";
						$status = '&lt;not sent&gt; ';
						if($rs1['sent']=='1'){$status = 'Sent. ';}
						if($rs1['status']=='Y'){$status .= 'Accepted. ';}
						else if($rs1['status']=='N'){$status .= 'Rejected. '.$rs1['status_text'].' ';}
						else if($rs1['sent']=='1' && trim($rs1['response'])!=''){$status .= '['.$rs1['response'].']';}

$page_html .= '			<tr class="text_value_sm">
                        	<td class="cellBorder leftBorder">'.($sno+1).'</td>
							<td class="cellBorder">'.$rs1['patient_name'].' - '.$rs1['patient_id'].'</td>
							<td class="cellBorder">'.$rs1['sch_proc'].'</td>
							<td class="cellBorder">'.$rs1['sch_time'].'</td>
							<td class="cellBorder alignCenter">'.$rs1['procedure_site'].'</td>
							<td class="cellBorder">'.$rs1['sch_phy'].'</td>
							<td class="cellBorder">'.$rs1['sch_fac'].'</td>
							<td class="cellBorder">'.$status.'</td>
						</tr>';
						$sno++;
				 	}
				}
				   mysql_free_result($rsGetPatEl);
    $page_html .= '</table>';
	$strHTML = "";
	$strHTML = $styles.$page_html;
	$strHTML.= '</page>';
	//=======HTML FILE WRITE HERE===========
	$fileName =write_html($strHTML); //FUNCTION USED IN /library/classes/common_function.php
	$page_style='p'; //PAGE ORIENTATION
	?>
	<html>
		<body>
			<script type="text/javascript">
				function closeIT(){
					var ie7 = (document.all && !window.opera && window.XMLHttpRequest) ? true : false;
					if (ie7){
						window.open("","_parent","");
						window.close();
					}
					else {
						this.focus();
						self.opener = this;
						self.close();
					}
				}
				var parWidth = parent.document.body.clientWidth;
				var parHeight = parent.document.body.clientHeight;
				window.open('../../library/html_to_pdf/createPdf.php?op=<?php echo $page_style;?>&file_location=<?php echo $fileName; ?>','pdfPrint','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
				closeIT();
				//parent.show_img('none');
			</script>
		</body>
	</html>