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
require_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/ChartTemp.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/User.php');

function getExamWnl($exam, $cti="", $phy="0"){
	if(empty($exam)){return "";}
	if(empty($phy)){ $phy = "0"; }
	$ret="";
	$sql = "SELECT wnl FROM chart_admin_wnl WHERE UPPER(exam) = '".strtoupper($exam)."' AND chart_template_id='".$cti."' AND phyid='".$phy."' AND deleted='0' ";
	$row = imw_query($sql);
	if($row  != false){
		$row1 = imw_fetch_array($row);
		$ret=$row1["wnl"];
	}
	return $ret;
}

function getChartTempSelect($sel=""){
	global $oChartTemp;
	$arr = $oChartTemp->getAll();

	$htm = "";
	if(count($arr)>0){
		foreach($arr as $key => $arval){
			$tmp = ($sel == $arval["id"]) ? "SELECTED" : "";
			$htm .= "<option value=\"".$arval["id"]."\" ".$tmp.">".$arval["name"]."</option>";
		}
	}
	$htm = "<select class=\"selectpicker\" data-width=\"100%\" id=\"elem_chart_temp\" name=\"elem_chart_temp\" onchange=\"loadWnl(this.value)\"><option value=\"\"></option>".$htm."</select>";
	echo $htm;
}

$ouser = new User();
$oChartTemp = new ChartTemp();

if(!empty($_GET["cti"])){
	$chart_temp_id = $_GET["cti"];
	$chart_phy_id = (!empty($_GET["phy"])) ? $_GET["phy"] : "0" ;
}else{
	$def_comp_id = $oChartTemp->getIdFromName("Comprehensive");
	$chart_temp_id = $def_comp_id;
	$chart_phy_id = "0";
}

$arrExamNames = array("CVF", "Amsler Grid", "Pupil",  "EOM", "External",
					"L&A" => array("Lids", "Lesion", "Lid Position", "Lacrimal System"),
					"Gonio",
					"SLE" => array("Conjunctiva", "Cornea", "Ant. Chamber", "Iris & Pupil", "Lens"),
					"Fundus"=> array("Optic Nerve", "Vitreous", "Macula", "Blood Vessels", "Periphery", "Retinal Exam" )
					 );

if(isset($_POST["elem_edit_wnl"]) && !empty($_POST["elem_edit_wnl"])){
	$len = count($_POST["elem_wnl"]);
	$elem_chart_temp = $_POST["elem_chart_temp"];
	$elem_src_prov_id = !empty($_POST["src_prov_id"]) ? $_POST["src_prov_id"] : 0 ;

	if(!empty($elem_chart_temp)){
		for($i=0; $i<$len; $i++){
			$tmp_exam = imw_real_escape_string($_POST["elem_exam"][$i]);
			$tmp_wnl = imw_real_escape_string($_POST["elem_wnl"][$i]);
			$sql = "SELECT id FROM chart_admin_wnl WHERE UPPER(exam) = '".strtoupper($tmp_exam)."' AND chart_template_id='".$elem_chart_temp."'  AND phyid='".$elem_src_prov_id."' AND deleted='0' ";
			$row = sqlQuery($sql);
			if($row != false){
				$tmp_id = $row["id"];
				$sql = "UPDATE chart_admin_wnl SET wnl = '".$tmp_wnl."' WHERE id = '".$tmp_id."'  ";
				$rr = sqlQuery($sql);
			}else{
				$sql = "INSERT INTO chart_admin_wnl (id, wnl, exam, chart_template_id, phyid) VALUES (NULL, '".$tmp_wnl."', '".$tmp_exam."', '".$elem_chart_temp."', '".$elem_src_prov_id."')  ";
				$rr = sqlQuery($sql);
			}
		}
	}
}
?>
<style>
	#th_hd{ position:relative; }
	#dv_def_prov_wnl { position:absolute; display: inline-block;  right:40px; min-width:50%; border:0px; top:0px; }
	#dv_def_prov_wnl table{border:0px;padding:0px;margin:0px;}
	#dv_def_prov_wnl .table td{border:0px;}
</style>
<script type="text/javascript">
	function checkFields(){
		document.frm_wnl.submit();
	}
	function resetFields(){
		$("input[id*=elem_wnl_]").val("");
	}

	function loadWnl(val, uid){
		if(val==""){
			top.fAlert("Please select chart template .");
			return;
		}
		//
		uid = (typeof(uid)!="undefined" && uid != "") ? "&phy="+uid : "" ;
		window.location.replace("?cti="+val+uid);//chart template id
	}

	function copy_to_prov(){
			var sid = $("#src_prov_id").val();
			var ar_tid = $("#trgt_prov_id").val();
			var cti = $("#elem_chart_temp").val();

			if(sid!="" && ar_tid && ar_tid.length>0){
				var index = ar_tid.indexOf(sid);
				if (index !== -1)
				{
					 ar_tid.splice(index, 1);
				}
		  }

			var msg = "";
			if(sid=="" || sid=="0"){
				msg+="<br/>&nbsp;- Select Provider ";
			}

			if(!ar_tid || (ar_tid && ar_tid.length<=0)){
				msg+="<br/>&nbsp;- Copy To Another Provider ";
			}

			if(cti==""){
				msg+="<br/>&nbsp;- Chart Template ";
			}

			if(msg!=""){
				top.fAlert("Please fill the following : "+msg);
				return;
			}

			var prm = {
				"sid":sid,
				"tid":ar_tid,
				"task":"copy_wnl",
				"cti":cti,
			};

			$.post("visit_ajax.php", prm, function(d){
				if(d=="-1"){
					top.fAlert("WNL Statements are copied Successfully!");
				}else{
					d = (typeof(d)!="undefined" && d!="") ? ""+d : "";
					top.fAlert("Error occured! Copy function not done. "+d);
				}
			});
	}

	function load_prov_wnl(){
		var val = $("#elem_chart_temp").val();
		var uid = $("#src_prov_id").val();
		loadWnl(val, uid);
	}

</script>
<body>
<div class="whtbox">
	<form method="post" name="frm_wnl" action="" id="frm_wnl">
	<input type="hidden" name="elem_edit_wnl" value="1">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:250px;">Exam</th>
						<th id="th_hd">

							WNL

 						<?php if(core_check_privilege(array("priv_def_wnl_stmt"))){ ?>
							<div id="dv_def_prov_wnl" class="form-inline">
								<table class="table" >
									<tr>
										<td>
											<div class="form-group">
												<label>Select Provider</label>
												<?php echo $ouser->getUsersDropDown("src_prov_id", " onchange=\"load_prov_wnl()\" ", $chart_phy_id, "", "", 0, 0, 3 );?>
											</div>
										</td>
										<td><label>Copy to another Provider</label></td>
										<td><?php echo $ouser->getUsersDropDown("trgt_prov_id", " data-width=\"300px\" multiple=\"multiple\" data-actions-box=\"true\"  ", "", "", "selectpicker", 0, 0, 3 );?></td>
										<td>
											<button type="button" name="btn_copy_to_prov" class="btn btn-success btn-xs" onclick="copy_to_prov()">Copy</button>
										</td>
									</tr>
							 </table>
							</div>
						<?php } ?>

						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="danger">
						<td>Chart Template</td>
						<td>
						<?php getChartTempSelect($chart_temp_id); ?>
						</td>
					</tr>
				<?php
					foreach($arrExamNames as $key => $val){
						if(is_array($val) ){
						$subExm =$val;
						$key_show=$key;
						echo "<tr class=\"danger\"><td style=\"padding-left:10px;\">".$key."</td><td></td></tr>";
						foreach($subExm as $key2 => $val2){
							$exam = $val2;
							$elem_wnl_name="elem_wnl[]";
							$elem_wnl_id="elem_wnl_".$key."_".$key2;
							$elem_wnl_val=getExamWnl($exam, $chart_temp_id, $chart_phy_id);
							$exam_show = $exam;
							if($exam_show=="Blood Vessels"){ $exam_show="Vessels"; }
								echo "<tr class=\"warning\">
									<td style=\"padding-left:30px;\">".$exam_show."
										<input type=\"hidden\" name=\"elem_exam[]\" value=\"".$exam."\">
									</td>
									<td>
										<input class=\"form-control\" type=\"text\" id=\"".$elem_wnl_id."\" name=\"".$elem_wnl_name."\" value=\"".$elem_wnl_val."\">
									</td>
								</tr>";
							}
						}else{
						$exam=$val;
						$elem_wnl_name="elem_wnl[]";
						$elem_wnl_id="elem_wnl_".$key;
						$elem_wnl_val=getExamWnl($exam, $chart_temp_id, $chart_phy_id);
						echo "<tr class=\"danger\">
							<td style=\"padding-left:10px;\">".$exam."
								<input type=\"hidden\" name=\"elem_exam[]\" value=\"".$exam."\">
							</td>
							<td>
								<input class=\"form-control\" type=\"text\" id=\"".$elem_wnl_id."\" name=\"".$elem_wnl_name."\" value=\"".$elem_wnl_val."\">
							</td>
						</tr>";
						}
					}
				?>
				</tbody>
			</table>
		</div>
	</form>
</div>
<script type="text/javascript">

$(document).ready(function(){
	var ar = [["saveWnlTab","Save","top.fmain.checkFields();"],
						["rstWnlVal","Reset","top.fmain.resetFields();"],
						];
	top.btn_show("ADMN",ar);
	set_header_title('WNL');
	var msg = '<?php echo $_GET["op"]; ?>';
	if(msg!="")	{
		top.alert_notification_show('Wnl is Saved.');
	}
	top.show_loading_image('none');
});

</script>
<?php
	require_once("../admin_footer.php");
?>
