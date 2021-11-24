<?php
class APPolicies{

	public function __construct(){

	}
	function get_appolicies(){
		$htm="";
		$i=1;
		$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'to_do_id';
		$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
		$q = "SELECT tb.to_do_id, tb.task,tb.assessment as assessment_dx,tb.plan,tb.site_type,
			group_concat(distinct(os.orderset_name) SEPARATOR ', ') as order_set_name,
			group_concat(distinct(od.name) SEPARATOR ', ') as order_name,tb.dxcode as assessment_dx9,tb.dxcode_10 as assessment_dx10,tb.strCptCd ,
			tb.strCptCd as elem_assocCpt, tb.dxcode as elem_assocDx, tb.dxcode_10 as elem_assocDx10, tb.assessment, tb.specialityId, tb.xmlFU, tb.order_id ,
			tb.order_set_name as order_set_ids,
			tb.severity, tb.location
			FROM console_to_do tb
			LEFT JOIN order_sets os ON (FIND_IN_SET(os.id, tb.order_set_name)>0 AND os.delete_status = 0)
			LEFT JOIN order_details od ON (FIND_IN_SET(od.id, tb.order_id)>0 AND od.delete_status = 0)
			WHERE providerID = 0
			group by tb.to_do_id
		    ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = $fu_arr=array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){

				$pln = nl2br($rs["plan"]);
				$oFu = new Fu();
				list($len_fu, $ar_fu) = $oFu->fu_getXmlValsArr($rs['xmlFU']);
				if(count($len_fu)>0){
					foreach($ar_fu as $k => $v){
						$pln .= "<br/>".$v["number"]." ".$v["time"]." ".$v["visit_type"];
					}
				}

				$ar_tmp = explode("@@@", $rs["elem_assocDx10"]);
				$rs["elem_assocDx10"] = $ar_tmp[0];
				$rs["elem_assocDx10Id"] = !empty($ar_tmp[1]) ? $ar_tmp[1] : "" ;
				$rs["assessment_dx10"] = $ar_tmp[0];

				$rs["to_do_id"] = trim($rs["to_do_id"]);
				if(!empty($rs["to_do_id"])){
				$htm .= "<tr class=\"link_cursor\">
							<td><div class=\"checkbox\"><input type=\"checkbox\" name=\"id\" id=\"id".$i."\" class=\"chk_sel\" value=\"".$rs["to_do_id"]."\"><label for=\"id".$i."\"></label></div></td>
							<td  onclick=\"addNew(1,'".$rs["to_do_id"]."');\">&nbsp;".$rs["task"]."</td>
							<td  onclick=\"addNew(1,'".$rs["to_do_id"]."');\">".$rs["assessment_dx"]."</td>
							<td  onclick=\"addNew(1,'".$rs["to_do_id"]."');\">".$pln."</td>
							<td  onclick=\"addNew(1,'".$rs["to_do_id"]."');\">".$rs["order_set_name"]."</td>
							<td  onclick=\"addNew(1,'".$rs["to_do_id"]."');\" >&nbsp;".$rs["order_name"]."</td>
							<td  onclick=\"addNew(1,'".$rs["to_do_id"]."');\" >&nbsp;".$rs["assessment_dx9"]."</td>
							<td  onclick=\"addNew(1,'".$rs["to_do_id"]."');\" >&nbsp;".$rs["assessment_dx10"]."</td>
							<td  onclick=\"addNew(1,'".$rs["to_do_id"]."');\">&nbsp;".$rs["strCptCd"]."</td>
						</tr>
						";
				}
				$i++;
			}
		}

		if(empty($htm)){
			$htm .= "<tr class=\"link_cursor\"><td colspan=\"9\">No result found.</td></tr>";
		}

		echo $htm;
	}

	function save(){
		$pkId	= "to_do_id";
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['aptask']);
		$_POST['providerID']=0;
		$_POST['date_time3']=date("Y-m-d H:i:s");

		$fu = new Fu();
		$strFollowup = $fu->fu_getXml($_POST["elem_followUpNumber"],$_POST["elem_followUp"],
								$_POST["elem_followUpVistType"],$_POST["elem_followUpVistTypeOther"]);

		unset($_POST["elem_followUpNumber"]);
		unset($_POST["elem_followUp"]);
		unset($_POST["elem_followUpVistType"]);
		unset($_POST["elem_followUpVistTypeOther"]);
		unset($_POST["elem_fuCntr"]);
		$_POST["severity"] = $_POST["elem_severity"];
		if($_POST["severity"]=="null"){$_POST["severity"]="";}
		unset($_POST["elem_severity"]);

		$de=$_POST['order_set_name'];
		if(count($de)>0){
			$_POST['order_set_name']=implode($de,',');
		}

		if($strFollowup){
			$_POST['xmlFU']=$strFollowup;
		}

		$query_part = "";

		//----BEGIN ORDERS----

		$arr_order = $arr_orderset = array();
		$_POST['order_id'] = '';
		$oOrders = new Orders;
		$order_type = $oOrders->get_order_types_arr();
		foreach($order_type as $index=>$order_type){
			//replaced XX
			$order_type = preg_replace('/[^A-Za-z0-9\-]/', '_', $order_type);
			$ele_order_type = "ele_order_".$order_type;
			//if(count($_POST[$ele_order_type])>0)
			//$arr_order[] = implode(",", $_POST[$ele_order_type]);
			unset($_POST[$ele_order_type]);
			unset($_POST["all_".$order_type]);

			//replaced above
			$ele_order_type_x = $ele_order_type."_x";
			$val_order_type_x = $_POST[$ele_order_type_x];
			if(empty($val_order_type_x)){ $val_order_type_x = ""; }
			if($val_order_type_x!=""){	$arr_order[] = $val_order_type_x; }
			unset($_POST[$ele_order_type_x]);
		}

		if(count($arr_order)>0)
		$_POST['order_id'] = implode(",", $arr_order);

		//replaced XX
		if(count($_POST['ele_order_orderset'])>0){	$_POST['order_set_name'] = implode(",", $_POST['ele_order_orderset']); }
		else{	$_POST['order_set_name'] = ''; }
		unset($_POST["ele_order_orderset"]);
		unset($_POST["all_orderset"]);

		//replaced above
		$val_order_orderset_x = $_POST['ele_order_orderset_x'];
		$_POST['order_set_name'] = $val_order_orderset_x;
		if($val_order_type_x!=""){	$val_order_orderset_x = "";}
		unset($_POST['ele_order_orderset_x']);

		//----END ORDERS------

		//-----BEGIN MANAGE DX AND CPT----
		unset($_POST["elem_assocDxStr"]);
		unset($_POST["elem_assocCptStr"]);
		unset($_POST["elem_assocDxStr10"]);
		$_POST['dxcode'] = trim($_POST['elem_assocDx']);
		$_POST['dxcode'] = trim($_POST['dxcode'],",");
		unset($_POST['elem_assocDx']);
		$_POST['dxcode_10'] = trim($_POST['elem_assocDx10']);
		$_POST['dxcode_10'] = trim($_POST['dxcode_10'],",");
		unset($_POST['elem_assocDx10']);
		$tmp = trim($_POST["elem_aDx10id"]);
		if(!empty($_POST['dxcode_10']) && !empty($tmp)){
			$tmp = trim($tmp,",");
			if(!empty($tmp)){
				$_POST['dxcode_10'] .= "@@@".$tmp;
			}
		}
		unset($_POST["elem_aDx10id"]);

		$_POST['strCptCd'] = $_POST['elem_assocCpt'];
		unset($_POST['elem_assocCpt']);

		//-----END MANAGE DX AND CPT------

		$_POST["assessment"] = $_POST["elem_assessment"];
		unset($_POST["elem_assessment"]);

		unset($_POST['elem_ormedmaster']);
		unset($_POST['elem_ormed']);

		$_POST["specialityId"] = $_POST["elem_speciality"];
		unset($_POST["elem_speciality"]);




		foreach($_POST as $k=>$v){
			if($k=='xmlFU'){
				$query_part .= $k."='".($v)."', ";
			}else{
				$query_part .= $k."='".addslashes($v)."', ";
			}
		}

		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."' ";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE  AND ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."' ".$qry_con;
		$r_c=imw_query($q_c);

		if(imw_num_rows($r_c)==0){

			if($id==''){
				$q = "INSERT INTO console_to_do SET ".$query_part;
			}else{
				$q = "UPDATE console_to_do SET ".$query_part." WHERE ".$pkId." = '".$id."'";
			}
			$res = imw_query($q);
			if($res){
				echo 'Record Saved Successfully.';
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		}else {
			echo "enter_unique";
		}
	}

	function get_record_inf(){
		$id = $_GET["eid"];
		$sql = "SELECT tb.to_do_id, tb.task,tb.assessment as elem_assessment,tb.plan,tb.site_type,
			group_concat(distinct(os.orderset_name) SEPARATOR ', ') as order_set_name,
			group_concat(distinct(od.name) SEPARATOR ', ') as order_name,tb.dxcode as assessment_dx9,tb.dxcode_10 as assessment_dx10,tb.strCptCd ,
			tb.strCptCd as elem_assocCpt, tb.dxcode as elem_assocDx, tb.dxcode_10 as elem_assocDx10, tb.assessment, tb.specialityId AS elem_speciality, tb.xmlFU, tb.order_id ,
			tb.order_set_name as order_set_ids,
			tb.severity, tb.location
			FROM console_to_do tb
			LEFT JOIN order_sets os ON (FIND_IN_SET(os.id, tb.order_set_name)>0 AND os.delete_status = 0)
			LEFT JOIN order_details od ON (FIND_IN_SET(od.id, tb.order_id)>0 AND od.delete_status = 0)
			WHERE to_do_id='".$id."'
			";
		$rs = sqlQuery($sql);
		if($rs!=false){
			$oOrders = new Orders();
			$tmp = $oOrders->get_order_options($rs['order_id'],$rs['order_set_ids'],$rs['order_set_name']);

			$ar_tmp = explode("@@@", $rs["elem_assocDx10"]);
			$rs["elem_assocDx10"] = $ar_tmp[0];
			$rs["elem_assocDx10Id"] = !empty($ar_tmp[1]) ? $ar_tmp[1] : "" ;
			$rs["assessment_dx10"] = $ar_tmp[0];
			$rs["plan"] = filter_var($rs["plan"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

			$rs = array_merge($rs, $tmp);
		}

		echo json_encode($rs);
	}

	function get_fu_htm(){

		$arrOpFu = array("Days", "Weeks", "Months", "Year");
		$fu = new Fu();
		$lenFu = 1;

		//Get FU
		$elem_Followup = $_REQUEST["elem_Followup"];
		if(isset($elem_Followup) && !empty($elem_Followup)){
			list($lenFu, $arrFuVals) = $fu->fu_getXmlValsArr($elem_Followup);
		}

		if(empty($lenFu)){$lenFu = 1;}//

		//FU
		global $arrFuNum_menu, $arrFuVist_menu;
		$arrFuNum_menu = $fu->get_fu_menu("n");
		$arrFuVist_menu = $fu->get_fu_menu("v");

		for($j=0,$i=0;$i<$lenFu;$i++){
		$j = $i+1;

		$elem_followUpNumber = $elem_followUp = $elem_followUpVistType = $elem_fuProName = "";
		$elem_followUpNumber = "" . $arrFuVals[$i]["number"];
		$elem_followUp = "" . $arrFuVals[$i]["time"];
		$elem_followUpVistType = "" . $arrFuVals[$i]["visit_type"];
		$elem_fuProName = "" . $arrFuVals[$i]["provider"];

		//elem_followUpNumber
		$str_elem_followUpNumber = $fu->getFuSelNum($j, $elem_followUpNumber, '', '', '');

		//folowup
		$str_elem_followUp="";
		foreach ($arrOpFu as $key => $val) {
			$sel = ($elem_followUp == $val) ? "selected" : "";
			$str_elem_followUp .= "<option value=\"" . $val . "\" " . $sel . " >" . $val . "</option>";
		}

		//elem_followUpVistType
		$str_elem_followUpVistType = $fu->getSelectFuHtml($j, $elem_followUpVistType);

		?>
			<div class="row pt10" >
				<div class='col-sm-3'>
				<?php echo $str_elem_followUpNumber; ?>
				</div>
				<div class='col-sm-3'>
				<select name="elem_followUp[]" id="elem_followUp_<?php echo $j; ?>" class="form-control minimal"
							onchange="fu_move(this)"   >
							<option value=""></option>
							<?php echo $str_elem_followUp; ?>
				</select>
				</div>
				<div class='col-sm-3'>
				<?php echo $str_elem_followUpVistType;	?>
				</div>

				<div class='col-sm-3'>
				<?php if($j==1){ ?>
				<span class="glyphicon glyphicon-plus pdl_10 pt5" data-toggle="tooltip" title="Add F/U" onclick="fu_add()"></span>
				<?php }else{ ?>
				<span class="glyphicon glyphicon-remove pdl_10 pt5" data-toggle="tooltip" title="Remove F/U" id=\"fu_del<?php echo $j;?>\" onclick="fu_del('<?php echo $j;?>')"></span>
				<?php } ?>
				</div>

			</div>
			<div class="clearfix"></div>
		<?php
		}
		?>
		<input type="hidden" name="elem_fuCntr" value="<?php echo $j;?>">
		<?php

	}

	function op_delete(){
		$id = $_POST['pkId'];
		$q 	= "DELETE FROM console_to_do WHERE to_do_id IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';
		}
	}

}
?>
