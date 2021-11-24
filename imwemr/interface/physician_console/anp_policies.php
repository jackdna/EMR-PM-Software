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
require_once(dirname(__FILE__) . '/../../config/globals.php');
require_once($GLOBALS['fileroot'] . '/library/classes/msgConsole.php');
require_once($GLOBALS['fileroot'] . '/library/classes/user_console.php');
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/Fu.php');
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/Dx.php');
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/wv_functions.php');
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/Admn.php');
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/exam_sev_loc_options.php');
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/exam_options.php');
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/TypeAhead.php');
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/ExamXml.php');
$fu = new Fu();
$dx = new Dx();
$ad = new Admn();
$TypeAhead = new TypeAhead();
/*
  require_once("../chart_notes/fu_functions.php");
  require_once("../main/main_functions.php");
  require_once("../chart_notes/common/simpleMenu_2.php");
  require_once("../chart_notes/common/exam_options.php");
  require_once("../chart_notes/common/exam_sev_loc_options.php");
 */

//--
if (isset($_GET["finding"]) && !empty($_GET["finding"])) {
	$arr = getExmSevLoc($_GET["finding"]);
	echo json_encode($arr);
	exit();
}
if (isset($_GET["term"]) && !empty($_GET["term"])) {
	$arr = $TypeAhead->main();
	echo json_encode($arr);
	exit();
}
if (isset($_GET["simple_menu"]) && !empty($_GET["simple_menu"])) {
	$arrMenu = $_POST['arrMenu'];
	$menuId = $_POST['menuId'];
	$elemTextId = $_POST['elemTextId'];
	$str = "";
	$str .= get_simple_menu($arrMenu,$menuId,$elemTextId);
	echo $str;
	exit();
}
if (isset($_POST["elem_formAction"]) && !empty($_POST["elem_formAction"])) {
	switch ($_POST["elem_formAction"]) {
		case "setConsoleCptCode":
			$okstr = "";
			$str = $_POST["elem_strCptCode"];
			if (!empty($str)) {
				$str = "'" . str_replace(", ", "','", $str) . "'";
				$sql = "SELECT cpt_fee_id,cpt_prac_code FROM cpt_fee_tbl WHERE cpt4_code IN ($str) AND delete_status = '0'" .
						"ORDER BY cpt_desc, cpt_prac_code, cpt4_code ";
				$rez = sqlStatement($sql);
				for ($i = 0; $row = sqlFetchArray($rez); $i++) {
					$tmp = $row["cpt_prac_code"];
					if (!empty($tmp) && strpos($okstr, $tmp) === false) {
						$okstr .= !empty($okstr) ? ", " : "";
						$okstr .= $tmp;
					}
				}
			}
			echo $okstr;
			exit();
	}
}
// Delete
if($_REQUEST['delid']<>""){
	$delQuery = "delete from console_to_do where to_do_id='".$_REQUEST['delid']."'";
	imw_query($delQuery);
	echo 'DELETED';
	
	exit;
}

//--
// functions --------------------
function ap_getOrders4Meds() {

	$order_type_name = "Meds";
	$order_type_name1 = $order_type_name;
	$order_type_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $order_type_name);

	$qry = "select id,name, snowmed, count(*) as num from order_details 
				where delete_status ='0' and (order_type_id = '" . $index . "' or o_type = '" . $order_type_name . "') ";
	$qry .= " GROUP by name ";
	$qry .= " order by name";
	$res = imw_query($qry);
	$all_order_option = $all_order_option_morediv = '';
	$i = 1;
	while ($row = imw_fetch_assoc($res)) {
		$id = $row['id'];
		$name = trim(ucwords($row['name']));

		if (empty($name)) {
			continue;
		}
		//--
		if ($row["num"] > 1) {
			$tmp = orlist_getOrdersByName($row["name"], $row["id"], $order_type_name);
			if (!empty($tmp)) {
				$pntr = "<span class=\"pointer  ui-icon ui-icon-circle-arrow-e\" onmouseover=\"showrelorders('" . $row["id"] . "',1, this)\" onmouseout=\"showrelorders('" . $row["id"] . "',0)\" ></span>";
				$all_order_option_morediv .= "<div id=\"dv_relorders" . $row["id"] . "\" class=\"relorders\" onmouseover=\"showrelorders('" . $row["id"] . "',3)\" onmouseout=\"showrelorders('" . $row["id"] . "',0)\" >" . $tmp . "</div>";
				$prnt_sufx = "master";
				$prnt_js = " onclick=\"checkrelorders(this)\" ";
			}
		} else {
			$pntr = "";
			$nmsufx = $prnt_sufx = "";
			$prnt_js = "";
		}
		//--

		$all_order_option .= "
		<div class=\"checkbox\">
			<input id=\"elem_inorder" . $i . $prnt_sufx . "\" type=\"checkbox\" name=\"elem_ormed" . $prnt_sufx . "[]\" value=\"$id\" $prnt_js $sel>
			<label for=\"elem_inorder" . $i . $prnt_sufx . "\">$name</label>" . $pntr . "</br>
		</div>";
		$i++;
	}
	?>

	<div class="div_shadow"  id="pop_up_<?php echo $order_type_name; ?>">
		<table style="width:100%;" class="tblBg alignLeft table_collapse_autoW border" cellspacing="2" cellpadding="2" >
			<tr >
				<td colspan="3" class="purple_bar">
					<div id="header_<?php echo $order_type_name; ?>">
						Please Add/Remove <?php echo $order_type_name; ?> Orders using Arrow Buttons.
					</div>
				</td>
			</tr>
			<tr>
				<td><label>List of <?php echo $order_type_name1; ?> Orders</label></td>
				<td>&nbsp;</td>
				<td><label>List of Selected <?php echo $order_type_name1; ?> Orders</label></td>
			</tr>
			<tr>
				<td class="alignCenter"  >
					<div id="dvordermedlist"  style="border:#CCC 1px solid;width:350px; height:200px;overflow:auto;"> 
						<?php print $all_order_option; ?><br/>
					</div>

					<?php echo $all_order_option_morediv; ?>
					<!--</select>-->			
				</td>
				<td style="vertical-align:top;">
					<input class="btn btn_size" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl_Meds('pop_up_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'all');">
					<input class="btn btn_size" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl_Meds('pop_up_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'single');">
					<input class="btn btn_size" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl_Meds('pop_up_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'single_remove');">
					<input class="btn btn_size" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl_Meds('pop_up_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'all_remove');">

				</td>
				<td class="alignCenter">
					<select class="larger_dropdowns" id="selected_<?php echo $order_type_name; ?>" name=""  size="15" multiple="multiple"></select>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="text-right pd10 ad_modal_footer" id="page_buttons">
					<button type="button" class="btn btn-success"  value="Done" onClick="selected_ele_close('pop_up_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'ele_order_<?php echo $order_type_name; ?>', 'div_order_<?php echo $order_type_name; ?>', 'done')">Done</button>
					&nbsp;
					<button type="button" class="btn btn-danger"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_up_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'ele_order_<?php echo $order_type_name; ?>', 'div_order_<?php echo $order_type_name; ?>', 'close')">Close</button>
				</td>
			</tr>
		</table>
	</div>

	<?php
	if ($order_type_name1 == "Information/Instructions") {
		$order_type_name1 = "Info./Instructions";
	}
	?>

	<div  class="col-sm-2" id="div_order_<?php echo $order_type_name; ?>" onClick="return popup_dbl('pop_up_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', '', 'ele_order_<?php echo $order_type_name; ?>')">
		<select  name="ele_order_<?php echo $order_type_name; ?>[]" id="ele_order_<?php echo $order_type_name; ?>" multiple="multiple" size="2" class=" form-control" onClick="return false">
			<?php echo $db_order_option; ?>
		</select><a class="text_purple" style="cursor:pointer" id="a_<?php echo $order_type_name; ?>"><?php echo $order_type_name1; ?></a>
	</div> 

	<?php
}

// functions --

$msgConsoleObj = new msgConsole();

if ($_REQUEST['mode'] == "getDxCode") {
	$elem_chkDx = trim($_REQUEST["elem_chkDx"]);
	$sql = "SELECT dx_code,pqriCode,diag_description FROM diagnosis_code_tbl " .
			"WHERE (dx_code='" . $elem_chkDx . "' || d_prac_code='" . $elem_chkDx . "' || " .
			"REPLACE(diag_description,'\r\n','')='" . $elem_chkDx . "') " .
			"  AND delete_status = '0' ";
	//echo $sql;	 
	$row = sqlQuery($sql);
	if ($row != false) {
		$dxCode = $row["dx_code"];
		$pqriCode = trim($row["pqriCode"]);
		$diagDesc = trim($row["diag_description"]);

		echo json_encode(array("dxCode" => $dxCode));
	}

	exit();
}

if ($_REQUEST['mode'] == "getDxCode10") {
	$elem_chkDx = trim($_REQUEST["elem_chkDx"]);
	$elem_chkDx_desc = trim($_REQUEST["elem_chkDx"]);
	$elem_chkDx_exp = explode(',', str_replace(', ', ',', $elem_chkDx));
	$elem_chkDx_imp = "'" . implode("','", $elem_chkDx_exp) . "'";
	$elem_chkDxId = trim($_REQUEST["elem_chkDxId"]);
	$elem_chkDxId = str_replace(' ', '', $elem_chkDxId);	
	$ar_chkDxId = (!empty($elem_chkDxId)) ? explode(',', $elem_chkDxId) : array();
	
	
	$sql = imw_query("SELECT id, icd10 FROM icd10_data " .
			"WHERE (icd10 in($elem_chkDx_imp) || icd10_desc in('" . $elem_chkDx_desc . "'))" .
			"  AND deleted = '0' ORDER BY icd10, id  ");
	//echo $sql;
	$icd10_con_arr = array(); $icd10_con_arr_uni = array();
	while ($row2 = imw_fetch_array($sql)) {
		$row2['icd10'] = trim($row2['icd10']);
		if(!empty($row2['icd10'])){
			if(in_array($row2['id'], $ar_chkDxId)){
				$icd10_con_arr[$row2['id']] = $row2['icd10'];
			}else if(!in_array($row2['icd10'], $icd10_con_arr)){
				$icd10_con_arr_uni[$row2['id']] = $row2['icd10'];
			}
		}
	}
	//
	if (count($icd10_con_arr_uni) > 0) {
		foreach($icd10_con_arr_uni as $k => $val){
			if(!empty($val) && !in_array($val, $icd10_con_arr)){
				$icd10_con_arr[$k] = $val;
			}
		}
	}
	
	if (count($icd10_con_arr) > 0) {
		$icd10_con = implode(", ", $icd10_con_arr);
		$icd10_con_id = implode(", ", array_keys($icd10_con_arr));		
		echo json_encode(array("dxCode" => $icd10_con, "dxCodeId"=>$icd10_con_id));
	}

	exit();
}

if ($_REQUEST['mode'] == "get_fu") {
	$elem_Followup = $_REQUEST['elem_Followup'];
	$arrFuNum_menu = array();
	for ($i = 1; $i <= 18; $i++) {
		$txt = $i;
		if ($i == 11) {
			$txt = "12";
		}
		if ($i == 12) {
			$txt = "Today";
		}
		if ($i == 13) {
			$txt = "Calendar";
		}
		if ($i == 14) {
			$txt = "PRN";
		}
		if ($i == 15) {
			$txt = "PMD";
		}
		if ($i == 16) {
			$txt = "First available";
		}
		if ($i == 17) {
			$txt = "As Scheduled";
		}
		if ($i == 18) {
			$txt = "Next Available";
		}
		$arrFuNum_menu[] = array($txt, $emp, $txt);
	}
	$arrFuVist = array("CEE/DFE", "ER/Acute", "CL Fit");
	$tmp = $ad->getFuOptions(0, 1);
	if (count($tmp) > 0) {
		$arrFuVist = array_merge($arrFuVist, $tmp);
	}
	
	$arrFuVist_menu = array();
	foreach ($arrFuVist as $key => $val) {
		$arrFuVist_menu[] = array($val, $emp, $val);
	}
	$arrFuVist_menu[] = array("Other", $emp, "Other"); //Other
	if (isset($elem_Followup) && !empty($elem_Followup)) {
		list($lenFu, $arrFuVals) = $fu->fu_getXmlValsArr($elem_Followup);
	}
	if (empty($lenFu)) {
		$arrFuVals[] = array("number" => "",
			"time" => "",
			"visit_type" => "");
		$lenFu = 1;
	}
	$flgNotShowFULabel = 1;
	$fu_coords1 = "280,20";
	$fu_coords2 = "525,20,200";
	echo '<div class="row pt10">';
	echo '
				<div class="col-sm-10" id="fu_val">
				<div id="follow_up" style="width:480px; height:160px;">';
	$arrOpFu = array("Days", "Weeks", "Months", "Year");

	$str = "";
	if (!isset($flgNotShowFULabel)) {
		$str .= "<label id=\"labelFU\">F/U</label>";
	}
	$str .= "<div id=\"listFU\" class=\"ulFL bg-success pd10\" data-cntrfu=\"" . $lenFu . "\" data-fuCntr1=\"" . $fu_coords1 . "\" 
						data-fuCntr2=\"" . $fu_coords2 . "\">";
	$j = 0;
	for ($i = 0; $i < $lenFu; $i++) {

		$j = $i + 1;
		$trId = "tr_fuId_" . $j;
		$td5Id = "followUpTr" . $j;
		$td6Id = "td6_fuId_" . $j;
		$td6Txt = "<div class=\"row pt5\">";
		$td6Txt .= "<div class=\"col-sm-6 text-left\">
					<span class=\"pdl_10 spnFuDel glyphicon glyphicon-remove link_cursor\" title=\"Remove F/U\" onclick=\"fu_del('" . $j . "')\"></span></div>";
		if ($j == 1) {
			$td6Txt .= "<div class=\"col-sm-6 text-left\"><span class=\"spnFuAdd glyphicon glyphicon-plus pointer\" title=\"Add F/U\" onclick=\"fu_add()\"></span></div>";
		}
		$td6Txt .= "</div>";

		$elem_followUpNumber = $elem_followUp = $elem_followUpVistType = $elem_fuProName = "";
		$elem_followUpNumber = "" . $arrFuVals[$i]["number"];
		$elem_followUp = "" . $arrFuVals[$i]["time"];
		$elem_followUpVistType = "" . $arrFuVals[$i]["visit_type"];
		$elem_fuProName = "" . $arrFuVals[$i]["provider"];

		//Set Pro Name
		if (empty($elem_followUpNumber) && empty($elem_followUp) && empty($elem_followUpVistType) && empty($elem_fuProName)) {
			if (!empty($arrMrPersonnal[$GLOBALS['alwaysDocFU']])) {
				$elem_fuProName = $GLOBALS['alwaysDocFU'];
			} else if (isset($_SESSION['res_fellow_sess']) && !empty($_SESSION['res_fellow_sess'])) { ////.Attestation: The follow up care instructions show the resident or fellows name that touched the chart last.  This happens when either the scribe or attending is in the chart.
				$elem_fuProName = $_SESSION['res_fellow_sess'];
			} else {
				$elem_fuProName = $elem_physicianId;
			}
		}

		//Arc --
		//number
		if (!empty($arTmpRecArc["div"]["number"][$j])) {
			echo $arTmpRecArc["div"]["number"][$j];
			$moeArc_fu["number"] = $arTmpRecArc["js"]["number"][$j];
			$flgArcColor_fu["number"] = $arTmpRecArc["css"]["number"][$j];
			if (!empty($arTmpRecArc["curText"]["number"][$j]))
				$elem_followUpNumber = $arTmpRecArc["curText"]["number"][$j];
		}else {
			$moeArc_fu["number"] = $flgArcColor_fu["number"] = "";
		}
		//time
		if (!empty($arTmpRecArc["div"]["time"][$j])) {
			echo $arTmpRecArc["div"]["time"][$j];
			$moeArc_fu["time"] = $arTmpRecArc["js"]["time"][$j];
			$flgArcColor_fu["time"] = $arTmpRecArc["css"]["time"][$j];
			if (!empty($arTmpRecArc["curText"]["time"][$j]))
				$elem_followUp = $arTmpRecArc["curText"]["time"][$j];
		}else {
			$moeArc_fu["time"] = $flgArcColor_fu["time"] = "";
		}
		//visit_type
		if (!empty($arTmpRecArc["div"]["visit_type"][$j])) {
			echo $arTmpRecArc["div"]["visit_type"][$j];
			$moeArc_fu["visit_type"] = $arTmpRecArc["js"]["visit_type"][$j];
			$flgArcColor_fu["visit_type"] = $arTmpRecArc["css"]["visit_type"][$j];
			if (!empty($arTmpRecArc["curText"]["visit_type"][$j]))
				$elem_followUpVistType = $arTmpRecArc["curText"]["visit_type"][$j];
		}else {
			$moeArc_fu["visit_type"] = $flgArcColor_fu["visit_type"] = "";
		}
		//Arc --

		$str .= "<div class='row ".($j>1?' pt10':'')."'>";
		//if($j == 1 && !isset($flgNotShowFULabel)) $str.= " ";
		$disble_time = (ctype_alpha($elem_followUpNumber) || is_numeric($elem_followUpNumber)) ? '': 'disabled';

		$tmp = $fu->getFuSelNum($j, $elem_followUpNumber, $moeArc_fu["number"], $flgArcColor_fu["number"], $fu_coords1);
		$str .= "<div class='col-sm-3'>" . $tmp . " </div>";

		$str .= "
					<div class='col-sm-3'>
					<select name=\"elem_followUp[]\" id=\"elem_followUp_" . $j . "\"  ". $disble_time ." class=\" form-control minimal " . $flgArcColor_fu["time"] . "\" 
								onchange=\"fu_move(this)\" " . $moeArc_fu["time"] . " >" .
				"<option value=\"\"></option>";

		foreach ($arrOpFu as $key => $val) {
			$sel = ($elem_followUp == $val) ? "selected" : "";
			$str .= "<option value=\"" . $val . "\" " . $sel . " >" . $val . "</option>";
		}

		$str .= "</select>
					</div> ";

		//$strFuSelectHtml;
		$tmp = $fu->getSelectFuHtml($j, $elem_followUpVistType, $moeArc_fu["visit_type"], $flgArcColor_fu["visit_type"], $fu_coords2);
		$str .= "<div class='col-sm-3'>" . $tmp . " </div>";


		//Provider Names + id --
		if (isset($fu_showProvider) && count($arrPhysician) > 0) {
			$str .= "
					<div class='col-sm-3'>
					<select name=\"elem_fuProName[]\" id=\"elem_fuProName_" . $j . "\" class=\"  form-control minimal " . $flgArcColor_fu["fuProName"] . "\" 
								" . $moeArc_fu["fuProName"] . " onmouseover=\"this.title=this.options[this.selectedIndex].text+'-'+this.value;\" >" .
					"<option value=\"\"></option>";

			foreach ($arrPhysician as $key => $val) {
				$tmp = $key;
				$sel = ($elem_fuProName == $tmp) ? "selected" : "";
				$str .= "<option value=\"" . $tmp . "\" title=\"" . $tmp . "\" " . $sel . " >" . $val . "</option>";
			}

			//Tech Only
			$sel = ($elem_fuProName == "Tech Only") ? "selected" : "";
			$str .= "<option value=\"Tech Only\" " . $sel . ">Tech Only</option>";

			$str .= "</select></div> ";
		}
		//Provider Names + id --

		$str .= "<div class='col-sm-3'>" . $td6Txt . "</div>";
		$str .= "</div>";
	}
	$str .= "</div>";
	$str .= "<input type=\"hidden\" name=\"elem_fuCntr\" value=\"" . $j . "\">";
	echo $str;
	echo '</div></div></div>';
	die();
}
if ($_REQUEST['mode'] == "add" || $_REQUEST['mode'] == "edit") {
	$strFollowup = $fu->fu_getXml($_POST["elem_followUpNumber"], $_POST["elem_followUp"], $_POST["elem_followUpVistType"], $_POST["elem_followUpVistTypeOther"]);
	// Insert Console
	$arr_order = $arr_orderset = array();
	foreach ($common_order_type as $index => $order_type) {
		//replaced xx
		$order_type = preg_replace('/[^A-Za-z0-9\-]/', '_', $order_type);
		$ele_order_type = "ele_order_" . $order_type;

		//replaced above
		$ele_order_type_x = $ele_order_type . "_x";
		$val_order_type_x = $_POST[$ele_order_type_x];
		if (empty($val_order_type_x)) {
			$val_order_type_x = "";
		}
		if ($val_order_type_x != "") {
			$arr_order[] = $val_order_type_x;
		}
		unset($_POST[$ele_order_type_x]);
	}
	if (count($arr_order) > 0)
		$_POST['order_id'] = implode(",", $arr_order);
	
	if (count($_POST['severity']) > 0)
		$_POST['elem_severity'] = implode(",", $_POST['severity']);

	//replaced xx
	if (count($_POST['ele_order_orderset']) > 0)
		$_POST['order_set_name'] = implode(",", $_POST['ele_order_orderset']);

	//replaced above
	$val_order_orderset_x = $_POST['ele_order_orderset_x'];
	if ($val_order_type_x != "") {
		$val_order_orderset_x = "";
	}
	$_POST['order_set_name'] = $val_order_orderset_x;
	unset($_POST['ele_order_orderset_x']);
	
	//add dx id
	if(isset($_POST["elem_aDx10id"]) && !empty($_POST["elem_aDx10id"])){
		$_POST["elem_assocDx10"] .= "@@@".$_POST["elem_aDx10id"];
	}
	//
	$datetime = date("Y-m-d H:i:s");

	if ($_REQUEST['mode'] == "add") {
		$qry = "insert into console_to_do set
						task='" . imw_real_escape_string($task) . "',
						providerID='" . $_SESSION['authUserID'] . "',
						assessment='" . imw_real_escape_string($_POST["elem_assessment"]) . "',
						plan='" . imw_real_escape_string($_POST["elem_plan"]) . "',
						dxcode='" . imw_real_escape_string($_POST["elem_assocDx"]) . "',
						dxcode_10='" . imw_real_escape_string($_POST["elem_assocDx10"]) . "',
						strCptCd='" . imw_real_escape_string($_POST["elem_assocCpt"]) . "',
						order_set_name = '" . imw_real_escape_string($_POST['order_set_name']) . "',
						order_id = '" . imw_real_escape_string($_POST['order_id']) . "',
						xmlFU = '" . $strFollowup . "',
						date_time3='$datetime',
						specialityId = '" . $_POST["elem_speciality"] . "',
						dynamic_ap = 0,
						severity='" . imw_real_escape_string($_POST['elem_severity']) . "',
						location='" . imw_real_escape_string($_POST['location']) . "',
						site_type='" . $_POST["site_type"] . "'
						";
		$insertId = sqlInsert($qry) or die(imw_error());
	} else if ($_REQUEST['mode'] == "edit") {
		switch ($_REQUEST['ap_type']) {
			case "0":
				$qry = "UPDATE console_to_do set
									task='" . imw_real_escape_string($task) . "',
									providerID='" . $_SESSION['authUserID'] . "',
									assessment='" . imw_real_escape_string($_POST["elem_assessment"]) . "',
									plan='" . imw_real_escape_string($_POST["elem_plan"]) . "',
									dxcode='" . imw_real_escape_string($_POST["elem_assocDx"]) . "',
									dxcode_10='" . imw_real_escape_string($_POST["elem_assocDx10"]) . "',
									strCptCd='" . imw_real_escape_string($_POST["elem_assocCpt"]) . "',
									order_set_name = '" . imw_real_escape_string($_POST['order_set_name']) . "',
									order_id = '" . imw_real_escape_string($_POST['order_id']) . "',
									xmlFU = '" . $strFollowup . "',
									date_time3='$datetime',
									specialityId = '" . $_POST["elem_speciality"] . "',
									dynamic_ap = 0,
									severity='" . imw_real_escape_string($_POST['elem_severity']) . "',
									location='" . imw_real_escape_string($_POST['location']) . "',
									site_type='" . $_POST["site_type"] . "'
							WHERE to_do_id = '" . $_REQUEST['editid'] . "'		
						";
				break;
			case "1":
				$qry = "insert into console_to_do set
							task='" . imw_real_escape_string($task) . "',
							providerID='" . $_SESSION['authUserID'] . "',
							assessment='" . imw_real_escape_string($_POST["elem_assessment"]) . "',
							plan='" . imw_real_escape_string($_POST["elem_plan"]) . "',
							dxcode='" . imw_real_escape_string($_POST["elem_assocDx"]) . "',
							dxcode_10='" . imw_real_escape_string($_POST["elem_assocDx10"]) . "',
							strCptCd='" . imw_real_escape_string($_POST["elem_assocCpt"]) . "',
							order_set_name = '" . imw_real_escape_string($_POST['order_set_name']) . "',
							order_id = '" . imw_real_escape_string($_POST['order_id']) . "',
							xmlFU = '" . $strFollowup . "',
							date_time3='$datetime',
							specialityId = '" . $_POST["elem_speciality"] . "',
							dynamic_ap = 0,		
							severity='" . imw_real_escape_string($_POST['elem_severity']) . "',
							location='" . imw_real_escape_string($_POST['location']) . "',
							site_type='" . $_POST["site_type"] . "'
						";
				break;
			case "2":
				$qry = "UPDATE console_to_do set
									task='" . imw_real_escape_string($task) . "',
									providerID='" . $_SESSION['authUserID'] . "',
									assessment='" . imw_real_escape_string($_POST["elem_assessment"]) . "',
									plan='" . imw_real_escape_string($_POST["elem_plan"]) . "',
									dxcode='" . imw_real_escape_string($_POST["elem_assocDx"]) . "',
									dxcode_10='" . imw_real_escape_string($_POST["elem_assocDx10"]) . "',
									strCptCd='" . imw_real_escape_string($_POST["elem_assocCpt"]) . "',
									order_set_name = '" . imw_real_escape_string($_POST['order_set_name']) . "',
									order_id = '" . imw_real_escape_string($_POST['order_id']) . "',
									xmlFU = '" . $strFollowup . "',
									date_time3='$datetime',
									specialityId = '" . $_POST["elem_speciality"] . "',
									dynamic_ap = 0,
									severity='" . imw_real_escape_string($_POST['elem_severity']) . "',
									location='" . imw_real_escape_string($_POST['location']) . "',
									site_type='" . $_POST["site_type"] . "'
							WHERE to_do_id = '" . $_REQUEST['editid'] . "'		
						";
				break;
		}
		$insertId = sqlInsert($qry) or die(imw_error());
	}
	die();
}
?>

<?php
$final_data_arr = array();
$final_data_arr = $msgConsoleObj->get_ap_policies();
$result_data_arr = $final_data_arr[0];

$smartheight = (($_SESSION['wn_height']-350)/3);

$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding" id="ap_pol_prov" style="max-height:' . ($smartheight) . 'px;overflow-y:auto;overflow-x:hidden; border-bottom:#000 1px solid">';
if (count($result_data_arr) > 0) {
	$tableElem .= '<table class="table table-bordered table-striped table-hover" style="margin-bottom:5px;">
							<thead>
								<tr class="purple_bar">						
									<th style="min-width:100px">Findings</th>
									<th>Assessment</th>
									<th>Plan</th>
									<th style="min-width:100px">Order Set</th>
									<th style="min-width:100px">Orders</th>
									<th style="min-width:100px">CPT Code</th>
									<th style="min-width:100px">ICD-9</th>
									<th style="min-width:100px">ICD-10</th>
									<th align="center">Actions</th>																											
								</tr>
							</thead>
							<tbody>';

	foreach ($result_data_arr as $key => $row) {
		$assess = (!empty($row['assessment'])) ? $row['assessment'] : "&nbsp;";
		//$assess .= (!empty($row['dxcode'])) ? " - ".$row['dxcode'] : "";
		$tmpCptcd = $row["strCptCd"];

		$order_set_id_arr = preg_split('/,/', $row['order_set_name']);
		$order_set_name_arr = array();
		for ($o = 0; $o < count($order_set_id_arr); $o++) {
			$order_set_name_arr[] = $orderSetNameArr[$order_set_id_arr[$o]];
		}
		$order_set_name_str = join(', ', $order_set_name_arr);
		if (trim($order_set_name_str) == '') {
			$order_set_name_str = '&nbsp;';
		}

		$follow_up = "";
		if (!empty($row["xmlFU"])) {
			list($len_arrFu, $arrFu) = $fu->fu_getXmlValsArr($row["xmlFU"]);
			if (count($arrFu) > 0) {
				foreach ($arrFu as $val) {
					$tmp = trim($val["number"] . " " . $val["time"] . " " . $val["visit_type"]);
					if (!empty($tmp)) {
						$follow_up .= "<br>" . $tmp . "";
					}
				}
			}
		}

		if ($row['to_do'] == "yes") {
			$chk = 'checked="checked"';
			$status = "yes";
		} else {
			$chk = '';
			$status = "no";
		}
		$findings_view = (!empty($row['task'])) ? str_replace(",", ",<br>", $row["task"]) : "&nbsp;";
		$follow_up_view = (!empty($row['plan'])) ? nl2br($row['plan']) . "<b>" . $follow_up . "</b>" : "&nbsp;";
		
		$on_click_tr = ' onclick="$(\'#console_form\').prop(\'src\',\'console_to_do.php?editid=' . $row['to_do_id'] . '\');" ';
		$on_click_td = ' onclick="add_edit_anp(\'' . $key . '\',\'1\')" ';
		if(!empty($row['providerID']) && !empty($row['no_delete'])){ $on_click_td = ''; $on_click_tr=''; }

		$t_dxc10 = $row['dxcode_10'];
		$ar_dxc10 = explode('@@@', $t_dxc10);
		$t_dxc10 = trim($ar_dxc10[0]);

		$tableElem .= '<tr class="link_cursor" '.$on_click_tr.' >
									<td '.$on_click_td.'>' . $findings_view . '</td>
									<td '.$on_click_td.'>' . $assess . '</td>
									<td '.$on_click_td.'>' . $follow_up_view . '</td>
									<td '.$on_click_td.'>' . $row['order_set_name'] . '</td>
									<td '.$on_click_td.'>' . $row['order_name'] . '</td>
									<td '.$on_click_td.'>' . $tmpCptcd . '</td>
									<td '.$on_click_td.'>' . $row['dxcode'] . '</td>
									<td '.$on_click_td.'>' . $t_dxc10 . '</td>
									<!-- <td '.$on_click_td.' align="center"> -->
									<td>';

		if (!empty($row['providerID']) && empty($row['no_delete'])) {
			//$tableElem .= '<a href="console_to_do.php?delid=' . $row['to_do_id'] . '" target="console_form" title="Delete"><img src="' . $GLOBALS['webroot'] . '/library/images/del.png"></a>';
			$tableElem .= '<span class="glyphicon glyphicon-remove link_cursor" onclick="delete_anp('.$row['to_do_id'].');" title="Delete"></span>';
		}

		$tableElem .= '</td>
								</tr>';
	}
	$tableElem .= '</tbody></table>';
	$tableElem .= '<script>$("#spanPhyPrint").show();</script>';
} else {
	$tableElem .= '<div class="alert alert-danger">No Record Found.</div></div>';
	$tableElem .= '<script>$("#spanPhyPrint").hide();</script>';
}
$tableElem .= '</div>';
//-------BEGIN Community A & P-----------
$result_data_arr = $final_data_arr[1];
$tableElem .= '<div class="clearfix"></div><div id="console_comm" > 
		<div class="meastop">
			<div class="row">
				<div class="col-sm-7" style="padding-left:10px!important;"><h2>SMART A&P - Community</h2></div>
				<div class="col-sm-5  text-right pt10 pdb5"> ';
if (count($result_data_arr) > 0) {
	$tableElem .= '<a href="console_pdf.php?task=ap_policies_community" target="_blank"  title="Complete Patient Record" alt="Complete Patient Record">
						<img src="' . $GLOBALS['webroot'] . '/library/images/scan.png" alt=""/></a>';
}
$tableElem .= '</div>
			</div>
		</div>';

$tableElem .= '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"  style="max-height:' . ($smartheight) . 'px;overflow-y:auto;overflow-x:hidden; border-bottom:#000 1px solid">';
if (count($result_data_arr) > 0) {
		$tableElem .= '<table class="table table-bordered table-striped table-hover" style="margin-bottom:5px">
							<thead>
								<tr class="purple_bar">						
									<th style="min-width:100px">Findings</th>
									<th>Assessment</th>
									<th>Plan</th>
									<th style="min-width:100px">Order Set</th>
									<th style="min-width:100px">Orders</th>
									<th style="min-width:100px">CPT Code</th>
									<th style="min-width:100px">ICD-9</th>
									<th style="min-width:100px">ICD-10</th>																									
								</tr>
							</thead>
							<tbody>';

	foreach ($result_data_arr as $key => $row) {
		$assess = (!empty($row['assessment'])) ? $row['assessment'] : "&nbsp;";
		//$assess .= (!empty($row['dxcode'])) ? " - ".$row['dxcode'] : "";
		$tmpCptcd = $row["strCptCd"];

		$order_set_id_arr = preg_split('/,/', $row['order_set_name']);
		$order_set_name_arr = array();
		for ($o = 0; $o < count($order_set_id_arr); $o++) {
			$order_set_name_arr[] = $orderSetNameArr[$order_set_id_arr[$o]];
		}
		$order_set_name_str = join(', ', $order_set_name_arr);
		if (trim($order_set_name_str) == '') {
			$order_set_name_str = '&nbsp;';
		}

		$follow_up = "";
		if (!empty($row["xmlFU"])) {
			list($len_arrFu, $arrFu) = $fu->fu_getXmlValsArr($row["xmlFU"]);
			if (count($arrFu) > 0) {
				foreach ($arrFu as $val) {
					$tmp = trim($val["number"] . " " . $val["time"] . " " . $val["visit_type"]);
					if (!empty($tmp)) {
						$follow_up .= "<br>" . $tmp . "";
					}
				}
			}
		}

		if ($row['to_do'] == "yes") {
			$chk = 'checked="checked"';
			$status = "yes";
		} else {
			$chk = '';
			$status = "no";
		}
		$findings_view = (!empty($row['task'])) ? str_replace(",", ",<br>", $row["task"]) : "&nbsp;";
		$follow_up_view = (!empty($row['plan'])) ? nl2br($row['plan']) . "<b>" . $follow_up . "</b>" : "&nbsp;";
		
		$on_click_tr = ' onclick="$(\'#console_form\').prop(\'src\',\'console_to_do.php?editid=' . $row['to_do_id'] . '\');" '; 
		$on_click_td = ' onclick="add_edit_anp(\'' . $key . '\',\'2\');" ';
		if(!empty($row['no_delete'])){ $on_click_td = ''; $on_click_tr=''; }
		
		$t_dxc10 = $row['dxcode_10'];
		$ar_dxc10 = explode('@@@', $t_dxc10);
		$t_dxc10 = trim($ar_dxc10[0]);

		$tableElem .= '<tr class="link_cursor" '.$on_click_tr.' >
									<td '.$on_click_td.' >' . $findings_view . '</td>
									<td  '.$on_click_td.'>' . $assess . '</td>
									<td  '.$on_click_td.'>' . $follow_up_view . '</td>
									<td  '.$on_click_td.'>' . $row['order_set_name'] . '</td>
									<td  '.$on_click_td.'>' . $row['order_name'] . '</td>
									<td  '.$on_click_td.'>' . $tmpCptcd . '</td>
									<td  '.$on_click_td.'>' . $row['dxcode'] . '</td>
									<td  '.$on_click_td.'>' . $t_dxc10 . '</td>
									</tr>';
	}
	$tableElem .= '</tbody></table>';
} else {
	$tableElem .= '<div class="alert alert-danger">No Record Found.</div>';
}
$tableElem .= '</div></div>';
//-------BEGIN DYNAMIC A & P-----------
$result_data_arr = $final_data_arr[2];

$tableElem .= '<div class="clearfix"></div><div id="console_dynamic"> 
			<div class="meastop">
				<div class="row">
					<div class="col-sm-7" style="padding-left:10px!important;"><h2>SMART A&P - Dynamic</h2></div>
					<div class="col-sm-5  text-right pt10 pdb5">';
if (count($result_data_arr) > 0) {
	$tableElem .= '<a href="console_pdf.php?task=ap_policies_dynamic" target="_blank"  title="Complete Patient Record" alt="Complete Patient Record">
						<img src="' . $GLOBALS['webroot'] . '/library/images/scan.png" alt=""/></a>';
}
$tableElem .= '</div>
				</div>
			</div>';
$tableElem .= '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"  style="max-height:'.($smartheight).'px;overflow-x:hidden; border-bottom:#000 1px solid">';
if (count($result_data_arr) > 0) {
		$tableElem .= '<table class="table table-bordered table-striped table-hover" style="margin-bottom:5px;">
								<thead>
									<tr class="purple_bar">						
										<th style="min-width:100px">Findings</th>
										<th>Assessment</th>
										<th>Plan</th>
										<th style="min-width:100px">Order Set</th>
										<th style="min-width:100px">Orders</th>
										<th style="min-width:100px">CPT Code</th>
										<th style="min-width:100px">ICD-9</th>
										<th style="min-width:100px">ICD-10</th>
										<th align="center">Actions</th>																											
									</tr>
								</thead>
								<tbody>
							';

	foreach ($result_data_arr as $key => $row) {
		$assess = (!empty($row['assessment'])) ? $row['assessment'] : "&nbsp;";
		//$assess .= (!empty($row['dxcode'])) ? " - ".$row['dxcode'] : "";
		$tmpCptcd = $row["strCptCd"];

		$order_set_id_arr = preg_split('/,/', $row['order_set_name']);
		$order_set_name_arr = array();
		for ($o = 0; $o < count($order_set_id_arr); $o++) {
			$order_set_name_arr[] = $orderSetNameArr[$order_set_id_arr[$o]];
		}
		$order_set_name_str = join(', ', $order_set_name_arr);
		if (trim($order_set_name_str) == '') {
			$order_set_name_str = '&nbsp;';
		}

		$follow_up = "";
		if (!empty($row["xmlFU"])) {
			list($len_arrFu, $arrFu) = $fu->fu_getXmlValsArr($row["xmlFU"]);
			if (count($arrFu) > 0) {
				foreach ($arrFu as $val) {
					$tmp = trim($val["number"] . " " . $val["time"] . " " . $val["visit_type"]);
					if (!empty($tmp)) {
						$follow_up .= "<br>" . $tmp . "";
					}
				}
			}
		}

		if ($row['to_do'] == "yes") {
			$chk = 'checked="checked"';
			$status = "yes";
		} else {
			$chk = '';
			$status = "no";
		}
		$findings_view = (!empty($row['task'])) ? str_replace(",", ",<br>", $row["task"]) : "&nbsp;";
		$follow_up_view = (!empty($row['plan'])) ? nl2br($row['plan']) . "<b>" . $follow_up . "</b>" : "&nbsp;";

		$on_click_tr = ' onclick="$(\'#console_form\').prop(\'src\',\'console_to_do.php?editid=' . $row['to_do_id'] . '\');" '; 
		$on_click_td = ' onclick="add_edit_anp(\'' . $key . '\',\'3\')" ';
		if(!empty($row['providerID']) && !empty($row['no_delete'])){ $on_click_td = ''; $on_click_tr=''; }
		
		$t_dxc10 = $row['dxcode_10'];
		$ar_dxc10 = explode('@@@', $t_dxc10);
		$t_dxc10 = trim($ar_dxc10[0]);
		
		$tableElem .= '<tr class="link_cursor" '.$on_click_tr.' >
										<td '.$on_click_td.' >' . $findings_view . '</td>
										<td '.$on_click_td.' >' . $assess . '</td>
										<td '.$on_click_td.' >' . $follow_up_view . '</td>
										<td '.$on_click_td.' >' . $row['order_set_name'] . '</td>
										<td '.$on_click_td.' >' . $row['order_name'] . '</td>
										<td '.$on_click_td.' >' . $tmpCptcd . '</td>
										<td '.$on_click_td.' >' . $row['dxcode'] . '</td>
										<td '.$on_click_td.' >' . str_replace(',', ',<br>', $t_dxc10) . '</td>
										<!-- <td '.$on_click_td.' > -->
										<td>';
		if (!empty($row['providerID']) && empty($row['no_delete'])) {								
		$tableElem .= '<span class="glyphicon glyphicon-remove link_cursor" onclick="delete_anp('.$row['to_do_id'].');" title="Delete"></span>';
		}
		//$tableElem .= '<span onclick="$(\'#console_form\').prop(\'src\',\'console_to_do.php?delid=' . $row['to_do_id'] . '\');"><img src="' . $GLOBALS['webroot'] . '/library/images/del.png"></span>';
		/* <a href="console_to_do.php?delid=' . $row['to_do_id'] . '" target="console_form"  title="Delete">
		  <img src="' . $GLOBALS['webroot'] . '/library/images/del.png"></a> */
		$tableElem .= '</td>
									</tr>';
	}
	$tableElem .= '</tbody></table>';
} else {
	$tableElem .= '<div class="alert alert-danger">No Record Found.</div>';
}
$tableElem .= '</div></div>';
//-------END DYNAMIC A & P--------------
echo $tableElem;
?>

<?php
// Get Dx Codes Str
list($strTHDesc, $strTHPracCode, $strTHDesc2) = $dx->getDxTHs();
// Get DX Codes
list($arrDxCodes, $arrDxCodesAndDesc, $strDxCodesAndDesc) = $dx->getDXCodes4Menu();

// Get ICD-10 Codes
//$z_flg_uniq_desc=1;
list($arrDxCodes10, $arrDxCodesAndDesc10, $strDxCodesAndDesc10, $strTHDesc10, $strTHPracCode10, $strTHDesc2_10) = $dx->getDXCodes4Menu10();
//Get CPT Codes
list($arrCptCats4Menu, $arrCptCodeAndDesc, $arrActiveCptCodes, $strCptCodeAndDesc, $strCptCodeDescActive) = $dx->getCptCodes4Menu_console();

//exm findings
$oExmXml = new ExamXml();
list($ar_ee_find_full, $ar_ee_finding) = $oExmXml->get_exm_ext_findings('','All');
if(count($ar_ee_find_full) > 0){ $arrMain = array_merge($arrMain, $ar_ee_find_full); }

//combine descriptions icd 9 and icd 10 and unique them
//$strTHDesc2 = $strTHDesc2.",".$strTHDesc2_10;
//Get Speciality
$str_spec_opts = "";
$sql = "SELECT * FROM admn_speciality WHERE status='0' Order By name ";
$rez = imw_query($sql);
while ($row = imw_fetch_assoc($rez)) {

	$sel = (!empty($elem_speciality) && $row["id"] == $elem_speciality) ? " selected " : "";
	$str_spec_opts .= "<option value=\"" . $row["id"] . "\"  " . $sel . " >" . $row["name"] . "</option>";
}
?> 
<style>
	/*#divContainer{
		position:absolute;
		width:950px;
		left:240px;
		top:80px;
		height:400px;	
		display:none;	
	}
	#divForm{
		width:500px;
		padding:10px;
		height:225px;
		overflow-X:hidden;
		overflow-Y:auto;
		float:left;
		margin:10px;
	}
	#divPtDemo{
		width:320px;
		padding:10px;
		height:225px;
		overflow-X:hidden;
		overflow-Y:auto;
		float:left;
		margin-top:10px;
		margin-bottom:10px;
	}
	.fldLabel{
		display:table-cell;
		padding:5px;
		width:80px;
		vertical-align:top;	 
	}
	.border1{
		border:1px solid #ccc;
	}
	
	#dvordermedlist{text-align:left;}
	#dvordermedlist label{border:0px solid yellow;margin-left:2px;}
	#dvordermedlist .pointer{display:inline-block;margin-left:3px;vertical-align:middle;}*/
	.relorders{border:1px solid black; width:200px; height:100px; display:none; background-color:white;position:absolute;overflow:auto;overflow-x:hidden; text-align:left; }
	.relorders label{border:0px solid yellow;margin-left:2px;}
	.spnFuDel,.spnFuAdd{font-size:15px;font-weight:bold;color:black;cursor:pointer;}
	.spnFuDel{color:red;}
	.spnFuAdd{position:absolute; margin-left:5px}

	.btn_size
	{
		width:35px !important;
		margin-bottom:2px
	}
</style>
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery-ui.min.1.12.1.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.datetimepicker.min.css" rel="stylesheet">
<!-- Js Script -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui.min.1.12.1.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.datetimepicker.full.min.js"></script>

<script>
		function anp_sort_sel(sid) {
			//sort
			var opt = $("#" + sid + " option").sort(function (a, b) {
				return a.innerHTML.toUpperCase().localeCompare(b.innerHTML.toUpperCase())
			});
			$("#" + sid).append(opt);
		}
		function popup_dbl_Meds(divid, sourceid, destinationid, act, odiv) {
			if (act == "single" || act == "all") {
				var ptrn = "";
				if (act == 'single') {
					//$("#"+sourceid+" option:selected").appendTo("#"+destinationid);
					ptrn = "#dvordermedlist :checked[type=checkbox], .relorders :checked[type=checkbox]";
				} else if (act == "all") {
					//$("#"+sourceid+" option").appendTo("#"+destinationid);				
					ptrn = "#dvordermedlist input[type=checkbox], .relorders  input[type=checkbox]";
				}
				if (ptrn != "") {
					var chk_arr = [];
					$("#" + destinationid + " option").each(function () {
						chk_arr[chk_arr.length] = "" + this.value;
					});

					//$("#"+destinationid).html("");
					var stro = "";
					$(ptrn).each(function () {
						var id = this.value;
						if (chk_arr.indexOf(id) != -1 || this.id.indexOf("master") != -1) { /*donothing*/
						} else {
							chk_arr[chk_arr.length] = id;
							var txt = $("label[for=" + this.id + "]").html();
							stro += "<option value=\"" + id + "\">" + txt + "</option>";
						}
					});
					$(stro).appendTo("#" + destinationid);

					//sort
					anp_sort_sel(destinationid);
				}

			} else if (act == "single_remove" || act == "all_remove") {
				//if(act=="single_remove"){$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);}
				//if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
				if (act == "single_remove") {
					$("#" + sourceid + "  option:selected").remove();
				}
				if (act == "all_remove") {
					$("#" + sourceid + "  option").remove();
				}

			} else {
				$("#" + destinationid + " option").remove();
				$("#" + odiv + " option").clone().appendTo("#" + destinationid);
				//sort
				anp_sort_sel(destinationid);
				$("#" + divid).show("clip");
			}
		}

		function popup_dbl(divid, sourceid, destinationid, act, odiv) {
			if (act == "single" || act == "all") {
				if (act == 'single') {
					$("#" + sourceid + " option:selected").appendTo("#" + destinationid);
				} else if (act == "all") {
					$("#" + sourceid + " option").appendTo("#" + destinationid);
				}
			} else if (act == "single_remove" || act == "all_remove") {
				if (act == "single_remove") {
					$("#" + sourceid + "  option:selected").appendTo("#" + destinationid);
				}
				if (act == "all_remove") {
					$("#" + sourceid + "  option").appendTo("#" + destinationid);
				}
				$("#" + destinationid).append($("#" + destinationid + " option").remove().sort(function (a, b) {
					var at = $(a).text(), bt = $(b).text();
					return (at > bt) ? 1 : ((at < bt) ? -1 : 0);
				}));
				$("#" + destinationid).val('');
			} else {
				$("#" + destinationid + " option").remove();
				$("#" + odiv + " option").clone().appendTo("#" + destinationid);
				//sort
				anp_sort_sel(destinationid);
				$("#" + divid).show("clip");
			}
		}
		function selected_ele_close(divid, sourceid, destinationid, div_cover, action) {
			if (action == "done") {
				var sel_cnt = $("#" + sourceid + " option").length;
				$("#" + divid).hide("clip");
				$("#" + destinationid + " option").each(function () {
					$(this).remove();
				})
				$("#" + sourceid + " option").appendTo("#" + destinationid);
				$("#" + destinationid + " option").prop("selected", true);
				$("#" + div_cover).width(parseInt($("#" + destinationid).width()) + 'px');
				if (sel_cnt > 8) {
					$("#" + div_cover).width(parseInt($("#" + destinationid).width() - 15) + "px");
				}
			} else if (action == "close") {
				$("#" + divid).hide("clip");
			}
		}
//Simple Menu
		var imgPath = "<?php echo $GLOBALS['webroot']; ?>";
//Zpath
		var zPath = "<?php echo $GLOBALS['rootdir']; ?>";
		var menu_position = 1;

		function myTrim(str)
		{
			str = str.replace(/^\s+|\s+$/, '');
			return str;
		}
		function ap_hasOrders() {
			var flg = 0;
			$("select[id*=ele_order_]").each(function () {
				var t = $(this).val();
				if (t && t != "") {
					flg = 1;
				}
			});
			return flg;
		}
		function chkform() {
			$(":input[name*=menuOptionValue]").remove(); //remove menu options
			var objFrm = document.frmForm;
			var objTask = objFrm.task;
			var objAssess = objFrm.elem_assessment;
			var objPlan = objFrm.elem_plan;
			var fo = ap_hasOrders();
			if (objTask.value != "") {
				if ((objAssess.value == "") && (objPlan.value == "") && fo == 0)
				{
					alert("Please enter assessment and plan/order.");
					return false;
				}
			} else if (objTask.value == "")
			{
				if ((objAssess.value == "") && (objPlan.value == "") && fo == 0) {
					alert("Please enter assessment and plan/order.");
					return false;
				} else if (objAssess.value == "")
				{
					alert("Please enter assessment.");
					return false;
				} else if (objPlan.value == "" && fo == 0)
				{
					alert("Please enter plan/order.");
					return false;
				}
			}
			$('#innerLoader').html('<div class="doing"></div>');
			frm_data = $('#frmForm').serialize();
			var sev_v = '';
			//sev_v = $('#severity').multipleSelect('getSelects');
			//if(typeof(sev_v)!="undefined" && sev_v!=""){	sev_v = encodeURIComponent(sev_v); 	}
			frm_data += "&elem_severity=" + sev_v;
			//
			var xx = ['ele_order_Meds', 'ele_order_Labs', 'ele_order_Imaging_Rad', 'ele_order_Procedure_Sx', 'ele_order_Information_Instructions', 'ele_order_orderset'];
			for (var x = 0; x < xx.length; x++) {
				var fo = [];
				$('#' + xx[x] + ' :selected').each(function (i, selected) {
					fo[i] = $(selected).val();
				});
				var sfo = fo.join(',');
				sfo = sfo || "";
				frm_data += "&" + xx[x] + "_x=" + encodeURIComponent(sfo);
			}
			
			//ICD10	
			var dx10 = $("#elem_assocDx10").val();
			var dx10Id = $("#elem_assocDx10").data("dxid");
			if(typeof(dx10Id)=="undefined"){ dx10Id=""; }
			frm_data +="&elem_aDx10id="+$.trim(dx10Id);


			$.ajax({
				type: "POST",
				url: "anp_policies.php",
				data: frm_data,
				success: function (r) {
					load_link_data('a_p_policies_opt');
				},
				complete: function() {
					$("#divContainer").modal('hide');
				}
			});
			//objFrm.submit();
		}
		function to_do2(val, stat) {
			if (stat == "no") {
				stat = "yes";
				location.href = "console_to_do.php?todoid=" + val + "&status=" + stat;
			} else {
				stat = "no";
				location.href = "console_to_do.php?todoid=" + val + "&status=" + stat;
			}
		}

		function checkDesc(obj) {
			var temp = document.getElementById("elem_assocDx");
			var temp = document.getElementById("elem_assocDx10");
			/*
			temp.value = ""; //Empty fields
			var strVal_f = myTrim(obj.value);
			var arr_strVal = strVal_f.split("-");
			strVal = $.trim(arr_strVal[0]);
			var len = arrTHDesc2.length;
			for (var i = 0; i < len; i++) {
				if (myTrim(arrTHDesc2[i].toLowerCase()) == strVal.toLowerCase()) {
					if (temp != null) {
						var temp_val = (typeof (arrTHPracCode[i]) == "undefined") ? "" : arrTHPracCode[i];
						temp.value = "" + temp_val;
					}
					break;
				}
			}

			
			temp.value = ""; //Empty fields
			var strVal_f = myTrim(obj.value);
			var arr_strVal = strVal_f.split("-");
			strVal = $.trim(arr_strVal[0]);
			var len = arrTHDesc2_10.length;
			for (var i = 0; i < len; i++) {
				if (myTrim(arrTHDesc2_10[i].toLowerCase()) == strVal.toLowerCase()) {
					if (temp != null) {
						temp.value = "" + arrTHPracCode10[i];
					}
					break;
				}
			}
			*/
		}

		function checkCode(obj) {
			checkDesc(obj);
			return 0;
		}

		function setConsoleDxCode(obj, mode) {
			//arrTHPracCode
			obj.value = $.trim(obj.value);
			if ((obj.value != "")) {
				/*
				var strRet = "";
				var strDx = "" + obj.value;
				var arr = new Array();
				if (strDx.indexOf(",") != -1) {
					arr = strDx.split(",");
				} else {
					arr[0] = strDx;
				}

				var len = arr.length;
				var lenPracCd = arrTHPracCode.length;
				for (var i = 0; i < len; i++) {
					for (j = 0; j < lenPracCd; j++) {
						var valIndx = myTrim(arr[i]);
						if (arrTHPracCode[j] == valIndx && (strRet.indexOf(valIndx) == -1) && (myTrim(valIndx) != "")) {
							strRet += (strRet != "") ? ", " + valIndx : "" + valIndx;
							break;
						}
					}
				}

				if (strRet != "") {
					obj.value = strRet;
				} else {
				*/
					
					//search in db
					if (mode != "getDxCode10") {
						var mode = "getDxCode";
					}
					var url = "anp_policies.php";
					var parms = "mode=" + mode + "&";
					parms += "elem_chkDx=" + obj.value + "&";
					parms += "elem_chkDxId=" + dxId + "&";

					$.get("" + url, "" + parms, function (data) {
						if (data && data.dxCode && data.dxCode != "") {
							obj.value = data.dxCode;
						} else {
							//alert("Please Enter valid Dx Code.");
							obj.value = "";
						}
					}, "json");
					
				//}
			}
		}

		function setConsoleCptCode(obj, id) {
			if ((obj.value != "")) {
				//var url = "../chart_notes/getSuperBillInfo.php";
				var url = "anp_policies.php";
				params = "elem_formAction=setConsoleCptCode";
				params += "&elem_strCptCode=" + obj.value;

				$.post(url, params,
						function (data) {
							if (data != "") {
								obj.value = "" + data;
							} else {
								alert("Please Enter valid CPT Code.");
								obj.value = "";
							}
						});
			}
		}

		function addConsoleDxCode(obj, id) {
			if (obj.value != "") {
				var objStr = gebi("" + id);
				var strDx = objStr.value;
				var curVal = myTrim(obj.value);
				if (curVal != "") {
					strDx += (myTrim(strDx) != "") ? ", " + curVal : "" + curVal;
				}
				objStr.value = strDx;
				objStr.onchange();
			}
		}

		function addConsoleCptCode(obj, id) {
			//
			var cptCd = myTrim(obj.value);
			var cptCdFull = myTrim(obj.value);
			if ((obj.value != "") && (cptCd.length >= 3)) {
				var indx = cptCd.lastIndexOf("~!~");
				cptCd = (indx != -1) ? myTrim(cptCd.substring(0, indx)) : cptCd;
			}

			//
			if (cptCd != "") {
				var objStr = gebi("" + id);
				var strCpt = objStr.value;
				var curVal = myTrim(cptCd);
				if (curVal != "") {
					strCpt += (myTrim(strCpt) != "") ? ", " + curVal : "" + curVal;
				}
				objStr.value = strCpt;
				objStr.onchange();
			}
		}

		function cancelForm() {
			window.location.href = 'console_to_do.php';
		}

///order meds
		var showrelorders_flg;
		function showrelorders(id, flg, obj) {
			if (flg == 3) {
				clearTimeout(showrelorders_flg);
				showrelorders_flg = null;
			} else if (flg == 2) {
				if (showrelorders_flg) {
					$("#dv_relorders" + id).hide();
					clearTimeout(showrelorders_flg);
					showrelorders_flg = null;
				}
			} else if (flg == 1) {

				//alert(window.event.pageX+" - "+window.event.pageY+" - "+$("#dvordermedlist").scrollTop());
				var lft = 100;//parseInt(window.event.pageX)+"px";
				var tp = 100;//parseInt(window.event.pageY);		
				$(".relorders").not("#dv_relorders" + id).hide();
				var dd = $(obj).position();
				//alert(dd.top+" - "+dd.left);
				lft = "" + dd.left;
				tp = "" + dd.top;
				//alert("11");
				$("#dv_relorders" + id).show();
				$("#dv_relorders" + id).css({"left": lft + "px", "top": tp + "px"});
				//$("#dv_relorders"+id).show().position({my: "left top",  at: "left top",  of: window.event});//,  within: "#pop_up_Meds"
			} else {
				if (!showrelorders_flg) {
					showrelorders_flg = setTimeout(function () {
						showrelorders(id, 2, obj);
					}, 500);
				}
			}
		}

		function checkrelorders(obj) {
			$("#dv_relorders" + obj.value + " input").each(function () {
				this.checked = (obj.checked) ? true : false;
			});
		}

		function checkSevLocOpts(o, f, sv, lc,rem) {
			if(typeof(rem)!="undefined" && rem!=""){ $("#"+rem).val(''); return;	}
			var a = $.trim(o.value);
			if (f == 1) {
				$("#severity,#location").html("<option value=\"\">Please Select</option>");
				return;
			}
			if (a != "") {
				// check finding		
				if(arrTHSym.indexOf(a)==-1){
					msg = "Entered Finding does not match with existing \"Finding\", Do you want to continue?";					
					var tt = (window.top.fmain) ? "window.top.fmain" : "top";	
					tt = ""+tt+".checkSevLocOpts('', '', '', '','"+o.id+"');";	
					top.fancyConfirm(msg, "", "", ""+tt);
					return;
				}
				// check finding
				$("#innerLoader").show();
				$.get("anp_policies.php?finding=" + a, function (data) {
					$("#innerLoader").hide();
					if (data) {
						if (data.Severity && data.Severity.length > 0) {
							var opt = "";
							for (var x in data.Severity) {
								var sel = (typeof (sv) != "undefined" && sv != "" && (sv + ",").indexOf(data.Severity[x] + ",") != -1) ? "selected" : "";
								opt += "<option value=\"" + data.Severity[x] + "\" " + sel + ">" + data.Severity[x] + "</option>";
							}
							$("#severity").html("" + opt);
							$('.selectpicker').selectpicker('refresh');
						}
						/*
						 if(data.Location&&data.Location.length>0){ 									
						 var opt="";	for(var x in data.Location){	
						 var sel = (typeof(lc)!="undefined"&&lc!=""&&lc==data.Location[x]) ? "selected" : "";
						 opt+="<option value=\""+data.Location[x]+"\" "+sel+" >"+data.Location[x]+"</option>";	
						 }
						 $("#location").html("<option value=\"\"></option>"+opt);
						 }
						 */

						//Multi select			
						//	$("#severity").multipleSelect({'width':'140px','allSelected':false, 'countSelected':false });

					}
				}, "json");
			}
		}

</script>
<!-- Type Ahead --> 
<!--<div  style="position:absolute;top:90;left:270;display:none; z-index:1000;" id="msgToDoDiv"></div>  -->
<div id="innerLoader" style="position:absolute; top:70px;left:500px"></div>
<!--modal wrapper class is being used to control modal design-->
<div class="common_modal_wrapper">
	<!-- Modal -->
    <div id="divContainer" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="width:60%">
			<!-- Modal content-->
            <div class="modal-content smart_popup">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="divHeader"> Smart </h4>
                </div>
                <div class="modal-body">
                    <div id="divContainer">
						<form name="frmForm" id="frmForm" method="post">
							<input type="hidden" name="editid" id="editid" value="">
							<input type="hidden" name="ap_type" id="ap_type" value="">
							<input type="hidden" name="mode"  id="mode"  value="" />
							<div class="row pt10">
								<div class="col-sm-1">
									<label for="task">Findings</label>
									<div id="loader" style="position:absolute;"></div>
								</div>
								<div class="col-sm-6" style="padding-right:9px!important;">
									<div class="row">
										<div class="col-sm-5"><input type="text" id="task" name="task" value="" class="form-control" onfocus="checkSevLocOpts(this, 1)" onblur="checkSevLocOpts(this)" autocomplete="off"></div>
										<div class="col-sm-2"><label for="severity">&nbsp;&nbsp;Severity</label></div>
										<div class="col-sm-5">
											<select id="severity" name="severity[]" multiple="multiple" class="selectpicker minimal selecicon" size="1">
												<option value="">Please Select</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-sm-1"><label for="elem_speciality">&nbsp;&nbsp;Specialty</label></div>
								<div class="col-sm-4">
									<select name="elem_speciality" id="elem_speciality" class="form-control minimal">
										<option value=""></option>
										<?php echo $str_spec_opts; ?>
									</select>
								</div>
							</div>
							<div class="row pt10">
								<div class="col-sm-1"><label for="elem_assessment">Assessment</label></div>
								<div class="col-sm-6">
									<input css="width:300px;" type="text" name="elem_assessment" id="elem_assessment"  value="" class="form-control" onChange="checkCode(this);"/>
								</div>
								<div class="col-sm-1"><label for="elem_assocDx">&nbsp;&nbsp;ICD-9</label></div>
								<div class="col-sm-4">
									<div class="input-group">
										<input type="text" name="elem_assocDx" id="elem_assocDx"  value="" class="form-control" onChange="setConsoleDxCode(this, 'getDxCode');" autocomplete="off">
										<input type="hidden" name="elem_assocDxStr" id="elem_assocDxStr" value="" onChange="addConsoleDxCode(this, 'elem_assocDx');">
										<?php
										echo get_simple_menu($arrDxCodes, "menu_DxCodes", "elem_assocDxStr");
										?>
									</div>
								</div>
							</div>
							<div class="row pt10">
								<div class="col-sm-1"><label>Site Type</label></div>
								<div class="col-sm-6">
									<div class="radio radio-inline">
										<input name="site_type" id="site_type_0" type="radio" value="0" class="form-control" checked>
										<label for="site_type_0">Normal Site</label>
									</div>
									<div class="radio radio-inline">
										<input name="site_type" id="site_type_1" type="radio" value="1" class="form-control">
										<label for="site_type_1">Site with lids</label>
									</div>
								</div>
								<div class="col-sm-1"><label for="elem_assocDx10">&nbsp;&nbsp;ICD-10</label></div>
								<div class="col-sm-4">
									<div class="input-group">
										<input type="text" name="elem_assocDx10" id="elem_assocDx10"  value="" class="form-control" onChange="setConsoleDxCode(this, 'getDxCode10');" autocomplete="off">
										<input type="hidden" name="elem_assocDxStr10" id="elem_assocDxStr10" value="" onChange="addConsoleDxCode(this, 'elem_assocDx10');">
										<?php echo get_simple_menu($arrDxCodes10, "menu_DxCodes10", "elem_assocDxStr10"); ?>
									</div>
								</div>
							</div>
							<div class="row pt10">
								<div class="col-sm-1"><label for="elem_plan">Plan</label></div>
								<div class="col-sm-6"><textarea name="elem_plan" id="elem_plan" rows="2" cols="100" class="form-control"></textarea></div>
								<div class="col-sm-1"><label for="elem_assocCpt">&nbsp;&nbsp;CPT Code</label></div>
								<div class="col-sm-4">
									<div class="input-group">
										<input type="text" id="elem_assocCpt" name="elem_assocCpt" value="" class="form-control" onChange="setConsoleCptCode(this);">
										<input type="hidden" name="elem_assocCptStr" id="elem_assocCptStr" value="" onChange="addConsoleCptCode(this, 'elem_assocCpt');">
										<?php echo get_simple_menu($arrCptCats4Menu, "menu_CptCats4Menu", "elem_assocCptStr"); ?>
									</div>
								</div>
							</div>
							<div class="clear-fix"></div>
							<div class="row pt10">
								<div class="col-sm-1"><label>Orders</label></div>
								<div class="col-sm-11">
									<div class="row">
										<?php
										$sql = "SELECT * FROM order_sets WHERE delete_status = 0";
										$res = imw_query($sql);
										$all_order_orderset = '';
										while ($row = imw_fetch_assoc($res)) {
											$id = $row['id'];
											$orderset_name = trim(ucwords($row['orderset_name']));
											$sel = 'selected';
											$all_order_orderset .= '<option value="'.$id.'" '.$sel.'>'.$orderset_name.'</option>';
										}
										foreach ($common_order_type as $index => $order_type_name) { // ARRAY DEFINED IN ../../order_sets/config_order.php
											//Meds --
											if ($index == "1" || $order_type_name == "Meds") {

												ap_getOrders4Meds();

												continue;
											}
											//Meds --

											$qry = "select id,name from order_details where delete_status ='0' and (order_type_id = '" . $index . "' or o_type = '" . $order_type_name . "')";
											$qry .= " order by name";
											$res = imw_query($qry);
											$all_order_option = '';
											while ($row = imw_fetch_assoc($res)) {
												$id = $row['id'];
												$name = trim(ucwords($row['name']));
												$sel = 'selected';
												$all_order_option .= '<option value="'.$id.'" '.$sel.'>'.$name.'</option>';
											}
											$order_type_name1 = $order_type_name;
											$order_type_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $order_type_name);
											?>
											<div class="div_shadow"  id="pop_up_<?php echo $order_type_name; ?>">
												<table style="width:100%;" class="tblBg alignLeft table_collapse_autoW border" cellspacing="2" cellpadding="2" >
													<tr>
														<td colspan="3" class="purple_bar">
															<div id="header_<?php echo $order_type_name; ?>">
																Please Add/Remove <?php echo $order_type_name; ?> Orders using Arrow Buttons.
															</div>
														</td>
													</tr>
													<tr>
														<td><label>List of <?php echo $order_type_name1; ?> Orders</label></td>
														<td>&nbsp;</td>
														<td><label>List of Selected <?php echo $order_type_name1; ?> Orders</label></td>
													</tr>
													<tr>
														<td class="alignCenter"  >
															<select class="larger_dropdowns" id="all_<?php echo $order_type_name; ?>" name="all_<?php echo $order_type_name; ?>[]"  size="15" multiple="multiple">                            
																<?php print $all_order_option; ?>
															</select>
														</td>
														<td style="vertical-align:top;">
															<input class="btn btn_size" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'all');">
															<input class="btn btn_size" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'single');">
															<input class="btn btn_size" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'single_remove');">
															<input class="btn btn_size" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'all_remove');">

														</td>
														<td class="alignCenter">
															<select class="larger_dropdowns" id="selected_<?php echo $order_type_name; ?>" name=""  size="15" multiple="multiple"></select>
														</td>
													</tr>
													<tr>
														<td colspan="3" class="text-right pd10 ad_modal_footer" id="page_buttons">
															<button type="button" class="btn btn-success"  value="Done" onClick="selected_ele_close('pop_up_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'ele_order_<?php echo $order_type_name; ?>', 'div_order_<?php echo $order_type_name; ?>', 'done')">Done</button>
															&nbsp;
															<button type="button" class="btn btn-danger"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_up_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', 'ele_order_<?php echo $order_type_name; ?>', 'div_order_<?php echo $order_type_name; ?>', 'close')">Close</button>
														</td>
													</tr>
												</table>
											</div>

											<?php
											//text overlapping in safari
											if ($order_type_name1 == "Information/Instructions") {
												$order_type_name1 = "Info./Instructions";
											}
											?>

											<div class="col-sm-2" id="div_order_<?php echo $order_type_name; ?>" onClick="return popup_dbl('pop_up_<?php echo $order_type_name; ?>', 'all_<?php echo $order_type_name; ?>', 'selected_<?php echo $order_type_name; ?>', '', 'ele_order_<?php echo $order_type_name; ?>')">
												<select name="ele_order_<?php echo $order_type_name; ?>[]" id="ele_order_<?php echo $order_type_name; ?>" multiple="multiple" size="2" class=" form-control" onClick="return false">
													<?php echo $db_order_option; ?>
												</select><a class="text_purple" style="cursor:pointer" id="a_<?php echo $order_type_name; ?>"><?php echo $order_type_name1; ?></a>
											</div>
										<?php }
										?>
										<div class="div_shadow"  id="pop_up_orderset">
											<table style="width:100%;" class="tblBg alignLeft table_collapse_autoW border" cellspacing="2" cellpadding="2" >
												<tr >
													<td colspan="3" class="purple_bar">Please Add/Remove Order Sets using Arrow Buttons.</td>
												</tr>
												<tr>
													<td><label>List of Order Sets</label></td>
													<td>&nbsp;</td>
													<td><label>List of Selected Order Sets</label></td>
												</tr>
												<tr>
													<td class="alignCenter"  >
														<select class="larger_dropdowns" id="all_orderset" name="all_orderset[]"  size="15" multiple="multiple">										

															<?php print $all_order_orderset; ?>
														</select>
													</td>
													<td style="vertical-align:top;">
														<input class="btn btn_size" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_orderset', 'all_orderset', 'selected_orderset', 'all');">
														<input class="btn btn_size" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_orderset', 'all_orderset', 'selected_orderset', 'single');">
														<input class="btn btn_size" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_orderset', 'selected_orderset', 'all_orderset', 'single_remove');">
														<input class="btn btn_size" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_orderset', 'selected_orderset', 'all_orderset', 'all_remove');">

													</td>
													<td class="alignCenter">
														<select class="larger_dropdowns" id="selected_orderset" name=""  size="15" multiple="multiple"></select>
													</td>
												</tr>
												<tr>
													<td colspan="3" class="text-right pd10 ad_modal_footer" id="page_buttons">
														<button type="button" class="btn btn-success"  value="Done" onClick="selected_ele_close('pop_up_orderset', 'selected_orderset', 'ele_order_orderset', 'div_order_orderset', 'done')">Done</button>
														&nbsp;
														<button type="button" class="btn btn-danger"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_up_orderset', 'selected_orderset', 'ele_order_orderset', 'div_order_orderset', 'close')">Close</button>
													</td>
												</tr>
											</table>
										</div>

										<div class="col-sm-2" id="div_order_orderset" onClick="return popup_dbl('pop_up_orderset', 'all_orderset', 'selected_orderset', '', 'ele_order_orderset')">
											<select  name="ele_order_orderset[]" id="ele_order_orderset" multiple="multiple" size="2" class=" form-control" >
											</select><a class="text_purple" style="cursor:pointer" id="a_order_sets">Order Sets</a>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class='col-sm-1 pt10'><label>F/U</label></div>
								<?php
								$arrFuNum_menu = array();
								for ($i = 1; $i <= 14; $i++) {
									$txt = $i;
									if ($i == 11) {
										$txt = "Today";
									}
									if ($i == 12) {
										$txt = "Calendar";
									}
									if ($i == 13) {
										$txt = "PRN";
									}
									if ($i == 14) {
										$txt = "PMD";
									}
									$arrFuNum_menu[] = array($txt, $emp, $txt);
								}

								//Fu Visit Type
								$arrFuVist = array("CEE/DFE", "ER/Acute", "CL Fit");
								$tmp = $ad->getFuOptions(0, 1);
								if (count($tmp) > 0) {
									$arrFuVist = array_merge($arrFuVist, $tmp);
								}
								$arrFuVist_menu = array();
								foreach ($arrFuVist as $key => $val) {
									$arrFuVist_menu[] = array($val, $emp, $val);
								}
								$arrFuVist_menu[] = array("Other", $emp, "Other"); //Other
								// Menu -------------
								//Follow Up default ------------
								//Get FU
								if (isset($elem_Followup) && !empty($elem_Followup)) {
									list($lenFu, $arrFuVals) = $fu->fu_getXmlValsArr($elem_Followup);
								}

								// No Option
								if (empty($lenFu)) {
									$arrFuVals[] = array("number" => "",
										"time" => "",
										"visit_type" => "");
									$lenFu = 1;
								}

								//Follow Up -----
								$flgNotShowFULabel = 1;
								$fu_coords1 = "280,20";
								$fu_coords2 = "525,20,200";
								if (!isset($flgNotShowFULabel)) {
									$str .= "<div class='col-sm-2'><label id=\"labelFU\">F/U</label></div>";
								}
								echo '<div class="col-sm-11" id="fu_val">
								<div id="follow_up" style="width:480px; height:160px;" >
								';
								$arrOpFu = array("Days", "Weeks", "Months", "Year");

								$str = "";

								$str .= "<div id=\"listFU\" class=\"ulFL bg-success pd10\" data-cntrfu=\"" . $lenFu . "\" data-fuCntr1=\"" . $fu_coords1 . "\" 
                                data-fuCntr2=\"" . $fu_coords2 . "\">";
								$j = 0;
								for ($i = 0; $i < $lenFu; $i++) {

									$j = $i + 1;
									$trId = "tr_fuId_" . $j;
									$td5Id = "followUpTr" . $j;
									$td6Id = "td6_fuId_" . $j;
									$td6Txt = "<div class=\"row pt5\">";
									$td6Txt .= "<div class=\"col-sm-6 text-left\">
									<span class=\"pdl_10 spnFuDel glyphicon glyphicon-remove link_cursor\" title=\"Remove F/U\" onclick=\"fu_del('" . $j . "')\"></span></div>";
									if ($j == 1) {
										$td6Txt .= "<div class=\"glyphicon glyphicon-plus pointer col-sm-6 text-left\"><span class=\"spnFuAdd\" title=\"Add F/U\" onclick=\"fu_add()\"></span></div>";
									}
									$td6Txt .= "</div>";


									$elem_followUpNumber = $elem_followUp = $elem_followUpVistType = $elem_fuProName = "";
									$elem_followUpNumber = "" . $arrFuVals[$i]["number"];
									$elem_followUp = "" . $arrFuVals[$i]["time"];
									$elem_followUpVistType = "" . $arrFuVals[$i]["visit_type"];
									$elem_fuProName = "" . $arrFuVals[$i]["provider"];

									//Set Pro Name
									if (empty($elem_followUpNumber) && empty($elem_followUp) && empty($elem_followUpVistType) && empty($elem_fuProName)) {
										if (!empty($arrMrPersonnal[$GLOBALS['alwaysDocFU']])) {
											$elem_fuProName = $GLOBALS['alwaysDocFU'];
										} else if (isset($_SESSION['res_fellow_sess']) && !empty($_SESSION['res_fellow_sess'])) { ////.Attestation: The follow up care instructions show the resident or fellows name that touched the chart last.  This happens when either the scribe or attending is in the chart.
											$elem_fuProName = $_SESSION['res_fellow_sess'];
										} else {
											$elem_fuProName = $elem_physicianId;
										}
									}

									//Arc --
									//number
									if (!empty($arTmpRecArc["div"]["number"][$j])) {
										echo $arTmpRecArc["div"]["number"][$j];
										$moeArc_fu["number"] = $arTmpRecArc["js"]["number"][$j];
										$flgArcColor_fu["number"] = $arTmpRecArc["css"]["number"][$j];
										if (!empty($arTmpRecArc["curText"]["number"][$j]))
											$elem_followUpNumber = $arTmpRecArc["curText"]["number"][$j];
									}else {
										$moeArc_fu["number"] = $flgArcColor_fu["number"] = "";
									}
									//time
									if (!empty($arTmpRecArc["div"]["time"][$j])) {
										echo $arTmpRecArc["div"]["time"][$j];
										$moeArc_fu["time"] = $arTmpRecArc["js"]["time"][$j];
										$flgArcColor_fu["time"] = $arTmpRecArc["css"]["time"][$j];
										if (!empty($arTmpRecArc["curText"]["time"][$j]))
											$elem_followUp = $arTmpRecArc["curText"]["time"][$j];
									}else {
										$moeArc_fu["time"] = $flgArcColor_fu["time"] = "";
									}
									//visit_type
									if (!empty($arTmpRecArc["div"]["visit_type"][$j])) {
										echo $arTmpRecArc["div"]["visit_type"][$j];
										$moeArc_fu["visit_type"] = $arTmpRecArc["js"]["visit_type"][$j];
										$flgArcColor_fu["visit_type"] = $arTmpRecArc["css"]["visit_type"][$j];
										if (!empty($arTmpRecArc["curText"]["visit_type"][$j]))
											$elem_followUpVistType = $arTmpRecArc["curText"]["visit_type"][$j];
									}else {
										$moeArc_fu["visit_type"] = $flgArcColor_fu["visit_type"] = "";
									}
									//Arc --

									$str .= " <div class='row pt10'>";
									$str .= "<div class='col-sm-3'>";
									$tmp = $fu->getFuSelNum($j, $elem_followUpNumber, $moeArc_fu["number"], $flgArcColor_fu["number"], $fu_coords1);
									
									/*~~~~~~~~~~~~~~~~~~~~~~~*/
									
									
									$str .= $tmp . " ";
									$str .= "</div>";

									$str .= "
							<div class='col-sm-3'>
							<select name=\"elem_followUp[]\" id=\"elem_followUp_" . $j . "\" class=\" " . $flgArcColor_fu["time"] . " form-control minimal\" 
                                        onchange=\"fu_move(this)\" " . $moeArc_fu["time"] . " >" .
											"<option value=\"\"></option>";

									foreach ($arrOpFu as $key => $val) {
										$sel = ($elem_followUp == $val) ? "selected" : "";
										$str .= "<option value=\"" . $val . "\" " . $sel . " >" . $val . "</option>";
									}

									$str .= "</select> 
							</div>";

									//$strFuSelectHtml;
									$tmp = $fu->getSelectFuHtml($j, $elem_followUpVistType, $moeArc_fu["visit_type"], $flgArcColor_fu["visit_type"], $fu_coords2);
									$str .= "<div class='col-sm-3'>" . $tmp . "</div>";

									//Provider Names + id --
									if (isset($fu_showProvider) && count($arrPhysician) > 0) {
										$str .= "<div class='col-sm-3'>";
										$str .= "<select name=\"elem_fuProName[]\" id=\"elem_fuProName_" . $j . "\" class=\" " . $flgArcColor_fu["fuProName"] . " form-control minimal\" 
                                        " . $moeArc_fu["fuProName"] . " onmouseover=\"this.title=this.options[this.selectedIndex].text+'-'+this.value;\" >" .
												"<option value=\"\"></option>";

										foreach ($arrPhysician as $key => $val) {
											$tmp = $key;
											$sel = ($elem_fuProName == $tmp) ? "selected" : "";
											$str .= "<option value=\"" . $tmp . "\" title=\"" . $tmp . "\" " . $sel . " >" . $val . "</option>";
										}

										//Tech Only
										$sel = ($elem_fuProName == "Tech Only") ? "selected" : "";
										$str .= "<option value=\"Tech Only\" " . $sel . ">Tech Only</option>";

										$str .= "</select> ";
										$str .= "</div>";
									}
									//Provider Names + id --

									$str .= $td6Txt;
									$str .= "</div>";
								}
								$str .= "</div>";
								$str .= "<input type=\"hidden\" name=\"elem_fuCntr\" value=\"" . $j . "\">";
								echo $str;
								echo '</div></div>';
								?></div>



						</form>    
                    </div>    
                </div>
                <div class="modal-footer ad_modal_footer" id="page_buttons">
                    <button type="button" class="btn btn-success" onClick="javascript:chkform();" value="Save">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
	</div>
</div>

<!-- Type Head -->
<script type="text/javascript">
	//Type Ahead
	var arrTHDesc = new Array(<?php echo $strTHDesc; ?>);
	var arrTHPracCode = new Array(<?php echo $strTHPracCode; ?>);
	var arrTHDesc2 = new Array(<?php echo $strTHDesc2; ?>);
	var arrTHSym = new Array(<?php echo "'" . implode("','", $arrMain) . "'"; ?>);
	var tmpTHDx = arrTHPracCode.concat(arrTHDesc2);

	var arrTHDesc10 = new Array(<?php echo $strTHDesc10; ?>);
	var arrTHPracCode10 = new Array(<?php echo $strTHPracCode10; ?>);
	var arrTHDesc2_10 = new Array(<?php echo $strTHDesc2_10; ?>);
	var tmpTHDx10 = arrTHPracCode10.concat(arrTHDesc2_10);

	arrObj0 = <?php echo json_encode($final_data_arr[0]); ?>;
	arrObj1 = <?php echo json_encode($final_data_arr[1]); ?>;
	arrObj2 = <?php echo json_encode($final_data_arr[2]); ?>;
	
	arrFuNum_menu = <?php echo json_encode($arrFuNum_menu); ?>;
	arrFuVist_menu = <?php echo json_encode($arrFuVist_menu); ?>;

<?php
$sql = "SELECT * FROM order_sets WHERE delete_status = 0";
$res = imw_query($sql);
$arr_order_set = array();
while ($row = imw_fetch_assoc($res)) {
	$id = $row['id'];
	$orderset_name = trim(ucwords($row['orderset_name']));
	$arr_order_set[$id] = $orderset_name;
}
if (count($arr_order_set) > 0) {
	?>
		var all_orderset_options = <?php echo json_encode($arr_order_set); ?>;
		var all_orderset_options_admin = all_orderset_options;
	<?php
}
foreach ($common_order_type as $index => $order_type_name) { // ARRAY DEFINED IN ../../order_sets/config_order.php
	$qry = "select id,name from order_details where delete_status ='0' and (order_type_id = '" . $index . "' or o_type = '" . $order_type_name . "')";
	$qry .= " order by name";
	$res = imw_query($qry);
	$arr = array();
	while ($row = imw_fetch_assoc($res)) {
		$id = $row['id'];
		$name = trim(ucwords($row['name']));
		//$arr[$id] = $name;
		$arr[] = array($name, $id);
	}
	if (count($arr) > 0) {
		$order_type_name = preg_replace("/[^a-zA-Z]/", '', $order_type_name);
		?>
			var all_orders_option_<?php echo $order_type_name; ?> = <?php echo json_encode($arr); ?>;
		<?php
	}
}
?>
	arrObj = '';
	function add_edit_anp(key, type) {
		key = key || '';
		switch (type) {
			case "1":
				arrObj = arrObj0;
				ap_type = "0";
				break;
			case "2":
				arrObj = arrObj1;
				ap_type = "1";
				break;
			case "3":
				arrObj = arrObj2;
				ap_type = "2";
				break;
		}
		order_type = <?php echo json_encode($common_order_type); ?>;
		//-------NEW A&P-------
		if (key == "") {
			$("#mode").val('add');
			$("#task").val('');
			$("#elem_assessment").val('');
			$("#elem_speciality option[value='']").prop('selected', true);
			$("#elem_assocDx").val('');
			$("#elem_assocDxStr").val('');
			$("#elem_assocDx10").val('').data("dxid", "");
			$("#elem_assocDxStr10").val('');
			$("#elem_assocCpt").val('');
			$("#elem_assocCptStr").val('');
			$("#elem_plan").val('');
			$("#divContainer").modal('show');
			$("#ap_type").val("0");
			$("#severity").html("");
			$("#location").html("");
			$("#site_type_0").prop('checked', true);

			fill_orders('', 1);
			document.getElementById("ele_order_orderset").options.length = 0;
			//------BEGIN SET VALUE FOR FU------------
			document.getElementById("fu_val").innerHTML = "<div class='doing'></div>";
			$.ajax({
				type: "POST",
				url: "anp_policies.php?elem_Followup=&mode=get_fu",
				success: function (r) {
					document.getElementById("fu_val").innerHTML = r;
				}
			});
			//------END SET VALUE FOR FU------------
		} else {//-------EDIT A&P-------
			$("#mode").val('edit');
			$("#task").val(arrObj[key]['task']);
			$("#elem_assessment").val(arrObj[key]['assessment']);
			$("#elem_speciality").val(arrObj[key]['specialityId']);
			$("#elem_assocDx").val(arrObj[key]['dxcode']);
			$("#elem_assocDxStr").val('');
			$("#elem_assocDx10").val(arrObj[key]['dxcode_10']);
			$("#elem_assocDx10").data("dxid", arrObj[key]['dxcode_10_id']);
			$("#elem_assocDxStr10").val('');
			$("#elem_assocCpt").val(arrObj[key]['strCptCd']);
			$("#elem_assocCptStr").val('');
			$("#elem_plan").val(arrObj[key]['plan']);
			$("#editid").val(arrObj[key]['to_do_id']);
			$("#ap_type").val(ap_type);
			$("#severity").html("");
			$("#location").html("");
			var stp = (arrObj[key]['site_type'] == "1") ? "1" : "0";

			$("#site_type_" + stp).prop('checked', true);
			fill_orders(key, 1);
			//console.log(arrObj[key]);
			//console.log(arrObj[key]['severity']);
			checkSevLocOpts($("#task")[0], 0, arrObj[key]['severity'], arrObj[key]['location']);

			$("#divContainer").modal('show');
			//------BEGIN SET VALUE FOR FU------------
			document.getElementById("fu_val").innerHTML = "<div class='doing'></div>";
			elem_Followup = arrObj[key]['xmlFU'];
			$.ajax({
				type: "POST",
				url: "anp_policies.php?elem_Followup=" + encodeURIComponent(elem_Followup) + "&mode=get_fu",
				success: function (r) {
					document.getElementById("fu_val").innerHTML = r;
				}
			});
			//------END SET VALUE FOR FU------------
		}
	}
	function fill_orders(key, empty) {
		empty = empty || 0;
		//------BEGIN SET OPTIONS FOR ORDERS------------
		key = key || '';
		for (order_id in order_type) {
			order_type_name = order_type[order_id].replace(/[^A-Za-z0-9\-]/, '_');
			o = document.getElementById("ele_order_" + order_type_name);
			$(o).unbind('click', '');
			$(o).bind('click', function () {
				fill_orders(key);
			});
			jId = "#a_" + order_type_name;
			$(jId).unbind('click', '');
			$(jId).bind('click', function () {
				fill_orders(key);
			});
			if (o != "undefined" && o.tagName == "SELECT" && o.type == "select-multiple") {
				if (empty)
					o.options.length = 0;
				if (key != "") {
					arr_order_type_name = new Array();
					jID = "#ele_order_" + order_type_name + ' option';
					$(jID).each(function () {
						id = $(this).val();
						arr_order_type_name[id] = $(this).text();
					});
					v = arrObj[key]['ele_order_' + order_type[order_id] + '[]'];
					for (order_id in v) {
						var option = document.createElement("option");
						option.text = v[order_id];
						option.value = order_id;
						option.selected = 'selected';
						if (typeof (arr_order_type_name[option.value]) == "undefined")
							o.appendChild(option);
					}
				}
			}
		}
		//---BEGIN SET ALL OPTIONS FOR ORDERS-------
		for (order_id in order_type) {
			order_type_name = order_type[order_id].replace(/[^A-Za-z0-9\-]/, '_');
			o = document.getElementById("all_" + order_type_name);
			if (o && o != "undefined" && o.tagName == "SELECT" && o.type == "select-multiple") {
				//o.options.length = 0;
				arr_order_type_name = new Array();
				$('#ele_order_' + order_type_name + ' option').each(function (index, element) {
					id = $.trim($(this).val());
					arr_order_type_name[id] = $(this).text();
				});
				var all_orders_tmp = "all_orders_option_" + order_type_name.replace(/[^a-zA-Z]/g, '');
				if (typeof window[all_orders_tmp] != "undefined") {
					var all_orders = eval("all_orders_option_" + order_type_name.replace(/[^a-zA-Z]/g, ''));
					o_all = document.getElementById("all_" + order_type_name);
					o_all.options.length = 0;
					for (order_detail_id_indx in all_orders) {
						var option = document.createElement("option");
						option.text = all_orders[order_detail_id_indx][0];
						var order_detail_id = all_orders[order_detail_id_indx][1];
						option.value = order_detail_id;
						option.selected = 'selected';
						if (typeof (arr_order_type_name[order_detail_id]) == "undefined")
							o_all.appendChild(option);
					}
				}
			}
		}

		//------BEGIN SET OPTIONS FOR ORDER SET------------
		o = document.getElementById("ele_order_orderset");
		$(o).unbind('click', '');
		$(o).bind('click', function () {
			fill_orders(key);
		});
		jId = "#a_order_sets";
		$(jId).unbind('click', '');
		$(jId).bind('click', function () {
			fill_orders(key);
		});
		if (empty)
			o.options.length = 0;
		if (key != "") {
			arr_order_orderset = new Array();
			jID = '#ele_order_orderset option';
			$(jID).each(function () {
				id = $(this).val();
				arr_order_orderset[id] = $(this).text();
			});
			v = arrObj[key]['ele_order_orderset[]'];
			for (order_set_id in v) {
				var option = document.createElement("option");
				option.text = v[order_set_id];
				option.value = order_set_id;
				option.selected = 'selected';
				if (typeof (arr_order_orderset[option.value]) == "undefined")
					o.appendChild(option);
			}
		}
		//------END SET OPTIONS FOR ORDER SET------------
		//---BEGIN SET ALL OPTIONS FOR ORDER SET-------
		o = document.getElementById("all_orderset");
		if (o != "undefined" && o.tagName == "SELECT" && o.type == "select-multiple") {
			//o.options.length = 0;
			arr_order_orderset = new Array();
			$('#ele_order_orderset option').each(function () {
				id = $(this).val();
				arr_order_orderset[id] = $(this).text();
			});
			o_orderset_all = document.getElementById("all_orderset");
			o_orderset_all.options.length = 0;
			if (typeof (all_orderset_options) == "undefined") {
				if (typeof (all_orderset_options_admin) != "undefined") {
					var all_orderset_options = all_orderset_options_admin;
				} else {
					var all_orderset_options = [];
				}
			}
			for (order_set_id in all_orderset_options) {
				var option = document.createElement("option");
				option.text = all_orderset_options[order_set_id];
				option.value = order_set_id;
				option.selected = 'selected';
				if (typeof (arr_order_orderset[order_set_id]) == "undefined")
					o_orderset_all.appendChild(option);
			}
		}
		//---END SET ALL OPTIONS FOR ORDER SET -------
	}
	
	//Delete A&P
	function delete_anp(delID) {
		$.ajax({
			type: "POST",
			url: "anp_policies.php?delid="+delID,
			success: function (r) {
				window.parent.load_link_data('a_p_policies_opt');
			}
		});
	}
	
function admin_ap_typeahead(){
	$('#elem_assocDx10, #elem_assocDx').autocomplete({			
			source: function( request, response ) {				
				var icd = (this.element[0].id.indexOf("10") == -1) ? 9 : 1;
			    $.getJSON( zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=get_dx_data&ICD10="+icd+"&req_ptwo=1", {
				term: $.trim(request.term) 				    
			    }, response );
			},
			search: function() {
			    // custom minLength
			    var term = $.trim(this.value);			   
			},
			 minLength: 2,
			 autoFocus: true,	
			 focus: function( event, ui ) {return false;},		
			 select: function( event, ui ) { if(ui.item.value!=""){
					var t = this.value ;
					var s = $(this).data("dxid");
					if(typeof(t)!="undefined" && t!=""){						
						if(t.indexOf(",")!=-1){
							var ar = t.split(",");
							var ar_s = (typeof(s)!="undefined" && s!="") ? s.split(",") : [] ; 
							if(ar.length>1){
								ar.length = ar.length-1;
								var t1 ="", s1 ="" ;
								for(var z in ar){
									if(typeof(ar[z])!="undefined" && $.trim(ar[z])!=""){
										t1 += ar[z]+",";
										s1 += (typeof(ar_s[z])!="undefined" && $.trim(ar_s[z])!="") ? ar_s[z]+"," : ",";
									}
								}
								//t = ar.join(","); 
								//t=t+",";
								t = t1;
								s = s1;
							}else{ t=""; s=""; }
						}else{ t=""; s=""; }
					}					
					
					var r = ""+ui.item.value;
					var rid = ""+ui.item.id;
					t =  $.trim(""+t+""+r+",");
					s =  $.trim(""+s+""+rid+",");
					//t=""+t.replace(/^,/,"");
					this.value = t;
					$(this).data("dxid", s);
					
					//$(this).triggerHandler("change");
					//$(this).triggerHandler("blur");
					//$(this).triggerHandler("click");
					    
					return false;
				} }
		});
			
}
	
	$(document).ready(function (e) {
		//delete
		$('a[title="Delete"]').bind("click", function (event) {
			if (!confirm('Do you want to delete this record?')) {
				return false;
			} else {
				event.stopPropagation();
			}
			;
		});

		//	$("#divContainer").draggable({ handle: "#divHeader" });
		//$("#divContainer").css({ "position":"absolute",	"width":"950px","left":"240px","top":"80px","height":"400px","display":"none" }).hide();
		$("#divContainer").modal('hide');
		//$('#elem_assocDx').typeahead({source: tmpTHDx});
		//--new actb(document.getElementById("elem_assocDx"),tmpTHDx,'','','','','pos'); //Type Ahead
		//$('#elem_assocDx10').typeahead({source: tmpTHDx10});
		//--new actb(document.getElementById("elem_assocDx10"),tmpTHDx10,'','','','','pos'); //Type Ahead
		//$('#task').typeahead({source: arrTHSym});
		$('#task').autocomplete({source:arrTHSym, 
				minLength: 2,
				focus: function (event, ui) {
					return false;
				},
				select: function (event, ui) {
					if (ui.item.value != "") {
						var ar = ui.item.value;						
						this.value = "" + ar;
						this.onblur();
					}
					return false;
				}
			});
		//--new actb(document.getElementById("task"),arrTHSym,'','','','','pos');
<?php
foreach ($common_order_type as $index => $order_type_name) { // ARRAY DEFINED IN ../../order_sets/config_order.php
	$order_type_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $order_type_name);
	?>
			//$("#pop_up_<?php echo $order_type_name; ?>").draggable({ handle: "#header_<?php echo $order_type_name; ?>" });
<?php } ?>

		//TA--
		$("#elem_assessment").autocomplete({
			source: "anp_policies.php?mode=getICD10Data",
			minLength: 2,
			focus: function (event, ui) {
				return false;
			},
			select: function (event, ui) {
				if (ui.item.value != "") {
					var ar = ui.item.value.split("[ICD-10:");
					var ard = $.trim(ar[0]);
					this.value = "" + ard;
					var dxid = ui.item.id;
					var ardx = ar[1].split("ICD-9:");
					var tdx = $.trim(ardx[0]);
					tdx = tdx.replace(",", ""); tdx=$.trim(tdx);
					if(typeof(tdx)!="undefined"){
						$("#elem_assocDx10").val(tdx).data("dxid", dxid);
					}
					//icd9
					var tdx = $.trim(ardx[1]);
					tdx = tdx.replace("]", ""); tdx=$.trim(tdx);
					if(typeof(tdx)!="undefined"){
						$("#elem_assocDx").val(tdx).data("dxid", dxid);
					}
					//this.onchange();
				}
				return false;
			}

		});

		$('.selectpicker').selectpicker('refresh');
		admin_ap_typeahead();
	});
	
	var menu1 = $('<menu1>').text("<?php echo addslashes(get_simple_menu($arrFuNum_menu,"menufunum", 'elem_followUpNumber_#dynID#')); ?>");
	var menu2 = $('<menu2>').text("<?php echo addslashes(get_simple_menu($arrFuVist_menu,"menufuvisit", 'elem_followUpVistType_#dynID#')); ?>");
</script>
<!-- Type Head -->
