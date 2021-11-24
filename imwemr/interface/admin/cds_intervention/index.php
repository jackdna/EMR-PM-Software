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

require_once("../admin_header.php");
require_once($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");

//default selected 
$elem_visit = "CEE";
$scriptMsg = '';
// Get Selected
if(isset($_GET["elem_visit"]) && !empty( $_GET["elem_visit"] )){
	$elem_visit = $_GET["elem_visit"];
}

if($_REQUEST['save_data']){
	$combination_implode 	= implode(",",$_REQUEST['combination']);
	
	$sql 			= "SELECT id FROM cds_intervention WHERE id = '1' ";
	$row 			= imw_query($sql);
	$numrow			= imw_num_rows($row);
	$insUpdtQry 	= " UPDATE ";
	$whrQry 		= " WHERE id = '1' ";
	if($numrow <=0){
		$insUpdtQry = " INSERT INTO ";
		$whrQry 	= "";
	}else{
		//$scriptMsg .= "<script>fAlert('Visit name \'$ptVisit\' already exists.');</ script>";
	}
	$sql = $insUpdtQry." cds_intervention SET
			problem_list 	= '".$_REQUEST['problem_list']."',
			medication_list = '".$_REQUEST['medication_list']."',
			allergy_list 	= '".$_REQUEST['allergy_list']."',
			laboratory_test = '".$_REQUEST['laboratory_test']."',
			vital_sign 		= '".$_REQUEST['vital_sign']."',
			pt_gender 		= '".$_REQUEST['pt_gender']."',
			combination 	= '".$combination_implode."'
			".$whrQry;
	$res = imw_query($sql);			
	$scriptMsg .= "<script>top.alert_notification_show('Record Saved Successfully');</script>";
	
	
}

//GET NEW VALUES
$sql_info = imw_query("SELECT * FROM cds_intervention WHERE id='1'");	
if(imw_num_rows($sql_info) > 0){
	$row_fet 		= imw_fetch_array($sql_info);
	$problem_list 	= $row_fet['problem_list'];
	$medication_list= $row_fet['medication_list'];
	$allergy_list 	= $row_fet['allergy_list'];
	$laboratory_test= $row_fet['laboratory_test'];
	$vital_sign 	= $row_fet['vital_sign'];
	$pt_gender 		= $row_fet['pt_gender'];
	$combination 	= explode(",",$row_fet['combination']);
}
?>	
<?php echo $scriptMsg;?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_cds_intevention.js"></script>
<body>

<div class="whtbox pdnon" id="pracfields" >
	<input type="hidden" name="preObjBack" value="">
		<form action="" name="frm_cds" method="post">
		<input type="hidden" name="tech_id" value="<?php echo $tech_id; ?>">
		<input type="hidden" name="save_data" value="">
		
			<div class="headinghd pd10">
			<div class="row">
				<div class="col-sm-12">
					<h2 style="margin-top:5px;">Enable / Disable Info-Buttons</h2>
				</div>
			</div>
		</div>
		<div class="row pd10">
			<div class="col-sm-12">
                <div class="col-md-6">
                    <div class="table-responsive respotable adminnw" id="meducalHistoryTable1">
                        <table class="table table-bordered table-hover">
                            
                            <tr class="lhtgrayhead">
                                <th>Individual</th>
                                <th class="text-center">Yes</th>
                            </tr>
                            <tr>
                                <td>Problem List</td>
                                <td class="text-center"><div class="checkbox"><input type="checkbox" name="problem_list" id="problem_list" value="yes" <?php if($problem_list=='yes')echo 'checked';?>><label for="problem_list"></label></div></td>
                            </tr>
                            <tr>
                                <td>Medication List</td>
                                <td class="text-center"><div class="checkbox"><input type="checkbox" name="medication_list" id="medication_list" value="yes" <?php if($medication_list=='yes')echo 'checked';?>><label for="medication_list"></label></div></td>
                            </tr>
                            <tr>
                                <td>Medication allergy list</td>
                                <td class="text-center"><div class="checkbox"><input type="checkbox" name="allergy_list" id="allergy_list" value="yes" <?php if($allergy_list=='yes')echo 'checked';?>><label for="allergy_list"></label></div></td>
                            </tr>
                            <tr>
                                <td>Laboratory tests</td>
                                <td class="text-center"><div class="checkbox"><input type="checkbox" name="laboratory_test" id="laboratory_test"  value="yes" <?php if($laboratory_test=='yes')echo 'checked';?>><label for="laboratory_test"></label></div></td>
                            </tr>
                            <tr>
                                <td>Vital Signs</td>
                                <td class="text-center"><div class="checkbox"><input type="checkbox" name="vital_sign" id="vital_sign"  value="yes" <?php if($vital_sign=='yes')echo 'checked';?>><label for="vital_sign"></label></div></td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td class="text-center"><div class="checkbox"><input type="checkbox" name="pt_gender" id="pt_gender"  value="yes" <?php if($pt_gender=='yes')echo 'checked';?>><label for="pt_gender"></label></div></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive respotable adminnw" id="meducalHistoryTable2">
                        
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="col-md-6">
                    <div class="table-responsive respotable adminnw" id="meducalHistoryTable3">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                <tr class="lhtgrayhead">
                                    <th>Combination</th>
                                    <th class="text-center">Yes</th>
                                </tr>
                                <tr>
                                    <td>Problem List</td>
                                    <td class="text-center"><div class="checkbox"><input type="checkbox" name="combination[]" id="problem_list_comb" value="problem_list" <?php if(in_array("problem_list",$combination))echo 'checked';?>><label for="problem_list_comb"></label></div></td>
                                </tr>
                                <tr>
                                    <td>Medication List</td>
                                    <td class="text-center"><div class="checkbox"><input type="checkbox" name="combination[]" id="medication_list_comb" value="medication_list" <?php if(in_array("medication_list",$combination))echo 'checked';?>><label for="medication_list_comb"></label></div></td>
                                </tr>
                                <tr>
                                    <td>Medication allergy list</td>
                                    <td class="text-center"><div class="checkbox"><input type="checkbox" name="combination[]" id="allergy_list_comb" value="allergy_list" <?php if(in_array("allergy_list",$combination))echo 'checked';?>><label for="allergy_list_comb"></label></div></td>
                                </tr>
                                <tr>
                                    <td>Laboratory tests</td>
                                    <td class="text-center"><div class="checkbox"><input type="checkbox" name="combination[]" id="laboratory_test_comb"  value="laboratory_test" <?php if(in_array("laboratory_test",$combination))echo 'checked';?>><label for="laboratory_test_comb"></label></div></td>
                                </tr>
                                <tr>
                                    <td>Vital Signs</td>
                                    <td class="text-center"><div class="checkbox"><input type="checkbox" name="combination[]" id="vital_sign_comb"  value="vital_sign" <?php if(in_array("vital_sign",$combination))echo 'checked';?>><label for="vital_sign_comb"></label></div></td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td class="text-center"><div class="checkbox"><input type="checkbox" name="combination[]" id="pt_gender_comb"  value="pt_gender" <?php if(in_array("pt_gender",$combination))echo 'checked';?>><label for="pt_gender_comb"></label></div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive respotable adminnw" id="meducalHistoryTable4">
    
                    </div>
                </div>
            </div>
		</div>
		
	</form>
</div>
<script>
set_header_title('CDS Intervention');
</script>
<?php
	require_once("../admin_footer.php");
?>
