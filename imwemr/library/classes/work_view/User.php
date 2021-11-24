<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: User.php
Coded in PHP7
Purpose: This class file provides functions to manage User/Providers of chart notes.
Access Type : Include file
*/
?>
<?php
//User
class User{
	public $uid;
	public function __construct($id=""){
		$this->uid = (!empty($id)) ? $id : $_SESSION["authId"];
	}

	//User FirstName
	function getName($flgFull="",$usrType=""){
		if($flgFull==""){ $flgFull=0; }
		if($usrType==""){$usrType=0;}

		$ret = "";
		if(!empty($this->uid)){

			if($flgFull==4){
				$sql = "SELECT fname,lname,mname,pro_suffix,user_type_name, id, pro_title FROM users ";
				$sql .= " LEFT JOIN user_type ON user_type_id = user_type ";
			}else{
				$sql = "SELECT fname,lname,mname,pro_suffix, id, pro_title FROM users ";
			}

			$sql .="WHERE id='".$this->uid."' ";
			if($usrType>0) $sql .= " AND user_type='".$usrType."' ";
			//$res = $this->db->Execute($sql);
			$res = imw_exec($sql);
			if( $res != false ){
				$ret = $this->getUNameFormatted($flgFull, $res);
			}
		}
		return $ret;
	}

	function getUNameFormatted($flgFull,$arr){
		$ret = $arr["fname"];
		if($flgFull == 1){
			$ret = $arr["lname"].", ".$arr["fname"]." ".$arr["mname"];
		}else if($flgFull == 2){
			$ret = array();
			$ret[] = $arr["lname"].", ".$arr["fname"]." ".$arr["mname"];
			$ret[] = substr($arr["fname"],0,1)."".substr($arr["lname"],0,1);
			$ret[] = $arr["fname"]." ".substr($arr["lname"],0,1);
		}else if($flgFull == 3){
			$tmp = "";
			$tmp .= !empty($arr["fname"]) ? $arr["fname"]." " : "";
			$tmp .= !empty($arr["mname"]) ? $arr["mname"]." " : "";
			$tmp .= !empty($arr["lname"]) ? $arr["lname"]." " : "";
			$ret = $tmp;
		}
		else if($flgFull == 4){
			$tmp = "";
			if(!empty($arr["lname"])) {$tmp .= $arr["lname"].", ";}
			if(!empty($arr["fname"])) {$tmp .= $arr["fname"]." ";}
			if(!empty($arr["mname"])) {$tmp .= $arr["mname"]." ";}
			if(!empty($arr["pro_suffix"])) {$tmp .= $arr["pro_suffix"]." ";}
			if(!empty($arr["user_type_name"])) {
				if($arr["user_type_name"]=="Resident Physician"){$arr["user_type_name"]="Resident";}

				//---
				if(isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"]) && $arr["id"] == $_SESSION["authId"] ){

					$arr["user_type_name"] = $this->get_user_type_nm($_SESSION["user_role"]);
				}
				//---

				$tmp .= "(".$arr["user_type_name"].") ";
			}
			$tmp = trim($tmp);
			$ret = $tmp;
		}else if($flgFull == 5){
			$tmp = "";
			$tmp .= !empty($arr["pro_title"]) ? $arr["pro_title"]." " : "";
			$tmp .= !empty($arr["fname"]) ? $arr["fname"]." " : "";
			$tmp .= !empty($arr["mname"]) ? substr($arr["mname"],0,1)." " : "";
			$tmp .= !empty($arr["lname"]) ? $arr["lname"]." " : "";
			//$tmp .= !empty($arr["id"]) ? "- ".$arr["id"]." " : "";
			$ret = trim($tmp);
		}else if($flgFull == 6){
			$tmp = "";
			if(!empty($arr["lname"])) {$tmp .= $arr["lname"].", ";}
			if(!empty($arr["fname"])) {$tmp .= substr($arr["fname"],0,1)." ";}
			if(!empty($arr["mname"])) {$tmp .= substr($arr["mname"],0,1)." ";}
			$ret = trim($tmp);
		}else if($flgFull == 7){
			$name = $arr['fname'];
			$name .= !empty($arr['lname']) ? "&nbsp;".strtoupper(substr($arr['lname'],0,1))."" : "" ;
			$name = (strlen($name) > 30) ? substr($name,0,28).".." : $name;
			$ret = trim($name);
		}else if($flgFull == 8){
			$tmp = "";
			if(!empty($arr["fname"])) {$tmp .= substr($arr["fname"],0,1)."";}
			if(!empty($arr["lname"])) {$tmp .= substr($arr["lname"],0,1)."";}
			$ret = trim(strtoupper($tmp));
		}

		return $ret;
	}

	function getUType($flgCn=0, $flg_real=0){
		$ret = "";
		$sql = "SELECT user_type FROM users WHERE id = '".$this->uid."'  ";
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		if($res != false){
			$ret = $res["user_type"];
		}

		//-- Role Change --
		if(empty($flg_real) && isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"]) && $this->uid == $_SESSION["authId"] && ($ret=="3" || $ret=="13")){
			$ret = $_SESSION["user_role"];
		}
		//-- Role Change --


		//GET Chart Notes specific Usr Type --
		if($flgCn == 1){
			$ret = $this->getUType_cn($ret);
		}
		//GET Chart Notes specific Usr Type --

		return $ret;
	}

	function getUType_cn($user_type){
		if(empty($user_type))return 0;
		//Assign Chart Notes specific user Type by checking the list
		if(in_array($user_type,$GLOBALS['arrValidCNPhy'])){
			$user_type = 1;
		}else if(in_array($user_type,$GLOBALS['arrValidCNTech'])){
			$user_type = 3;
		}
		//Assign Chart Notes specific user Type by checking the list
		return $user_type;
	}

	function getSign($flgCheck=""){

		$usrHasSign=0;
		$sql = "SELECT sign, sign_path FROM users WHERE id = '".$this->uid."' ";
		$row = imw_exec($sql);
		if($row != false){
			$strpixls = trim($row["sign"]);
			$str_sign_path = trim($row["sign_path"]);

			$chk1=$chk2=0;
			if((!empty($strpixls) && $strpixls!="0-0-0:;" && empty($str_sign_path))){  $chk1=1; }

			$oSaveFile = new SaveFile($this->uid,1);
			$flg_file_exists = $oSaveFile->isFileExists($str_sign_path);

			if((!empty($str_sign_path) && strpos($str_sign_path,"UserId") !== false && $flg_file_exists )){  $chk2=1; }
			if($chk1==1||$chk2==1){
				$usrHasSign=1;

				//make path complete
				if($flgCheck==2||$flgCheck==3){
					if($flgCheck==3){ $str_sign_path_db = $str_sign_path; }
					$str_sign_path = $oSaveFile->getFilePath($str_sign_path,"w");
				}

			}else{
				$str_sign_path = $strpixls = "";
			}
		}
		if($flgCheck==1){
			return $usrHasSign;
		}else if($flgCheck==3){
			return array($str_sign_path, $str_sign_path_db);
		}else{
			return array($str_sign_path, $strpixls);
		}
	}

	function getUsersDropDown($nm, $ev="", $val="", $utype="", $clss="", $retOpts=0, $addFuOpts=0, $frmt=0){
		if(empty($frmt)){ $frmt=4; }
		$str_sel_phy="";
		$type_srch = implode(",",$GLOBALS['arrValidCNPhy']);
		if(!empty($utype)&&$utype=="scribeby"){$type_srch = "3,13";}
		else if($utype=="all_usrs"){ $type_srch = "all_usrs"; }
		$str_sel_val = (!empty($val)) ? " OR c1.id = '".$val."' " : "" ;

		if(!empty($type_srch)){
		$phrase_usr_type = ($type_srch == "all_usrs") ? "" : " c1.user_type IN (".$type_srch.") AND ";

		$sql="SELECT c1.id, c1.fname, c1.mname, c1.lname, c1.pro_suffix, c2.user_type_name
				FROM users c1
				LEFT JOIN user_type c2 ON c2.user_type_id = c1.user_type
				WHERE ".$phrase_usr_type."
				c1.delete_status != 1 AND (c1.locked != 1 ".$str_sel_val.")
				ORDER BY c1.lname, c1.fname, c1.mname, c1.id ";
		$res = sqlStatement($sql);
		if($res != false){
			while($row=sqlFetchArray($res)){
				$id = $row["id"];
				$tmp = $this->getUNameFormatted($frmt,$row);

				if(!empty($tmp)){
					$sel="";
					if(!empty($val) && $id==$val){  $sel=" selected ";  }
					$str_sel_phy.="<option value=\"".$id."\" ".$sel.">".$tmp."</option>";
				}
			}
		}

		if(!empty($addFuOpts)){
			if($addFuOpts=="All Providers"){
					$str_sel_phy="<option value=\"0\" >".$addFuOpts."</option>".$str_sel_phy;
			}else{
				$ar_fu = array("Tech Only", "Contact Lens Tech", "Any doctor", "Any OD", "Any MD");
				foreach($ar_fu as $k => $v){
					$sel="";
					if(!empty($val) && $v==$val){  $sel=" selected ";  }
					$str_sel_phy.="<option value=\"".$v."\" ".$sel.">".$v."</option>";
				}
			}
		}


		}
		//
		if(!empty($str_sel_phy)){

			if(!empty($retOpts)){
				//donot add select
			}else{

			$emp_opt = "";
			if(strpos($clss, "selectpicker")===false){
					$emp_opt = "<option value=\"\"></option>";
			}

			$str_sel_phy="<select name=\"".$nm."\" id=\"".$nm."\" ".$ev." class=\"form-control ".$clss."\" >
						".$emp_opt.$str_sel_phy."</select>";
			}

		}else{
			$str_sel_phy="";
		}

		return $str_sel_phy;

	}

	function users_getDropDown($selId=""){
		$str_sel_phy="";

		$arr_sel_id=array();
		if(!empty($selId)){
			$arr_sel_id = explode(",",$selId);
		}

		$sql = "SELECT * FROM user_type ORDER BY user_type_id ";
		$rez2 = sqlStatement($sql);
		for($j=1;$row2=sqlFetchArray($rez2);$j++){
			$user_type_id = $row2["user_type_id"];

			$str_sel_phy.="<optgroup label=\"".$row2["user_type_name"]."\">";

			$sql="SELECT c1.id, c1.fname, c1.mname, c1.lname, c1.pro_suffix, c2.user_type_name
					FROM users c1
					LEFT JOIN user_type c2 ON c2.user_type_id = c1.user_type
					WHERE c1.delete_status != 1 AND c1.locked != 1
					AND c1.user_type = '".$user_type_id."'
					ORDER BY c1.lname, c1.fname, c1.mname, c1.id ";
			$rez = sqlStatement($sql);
			for($i=0; $row=sqlFetchArray($rez); $i++){

				$id = $row["id"];

				$tmp = "";
				$tmp = $this->getUNameFormatted(4,$row);

				if(!empty($tmp)){

					$sel_tmp = (in_array($id,$arr_sel_id)) ? "SELECTED" : "";

					$str_sel_phy.="<option value=\"".$id."\" ".$sel_tmp.">".$tmp."</option>";
				}

			}

			$str_sel_phy.="</optgroup>";

		}

		return $str_sel_phy;

	}

	function users_getAccordian($selId=""){
		$str_sel_phy=""; $c = 0; $cc=0;

		$arr_sel_id=array();
		if(!empty($selId)){
			$arr_sel_id = explode(",",$selId);
		}

		$sql = "SELECT * FROM user_type ORDER BY user_type_id ";
		$rez2 = sqlStatement($sql);
		for($j=1;$row2=sqlFetchArray($rez2);$j++){
			$user_type_id = $row2["user_type_id"];

			$str_sel_phy_tmp = "";
			$sql="SELECT c1.id, c1.fname, c1.mname, c1.lname, c1.pro_suffix, c2.user_type_name
					FROM users c1
					LEFT JOIN user_type c2 ON c2.user_type_id = c1.user_type
					WHERE c1.delete_status != 1 AND c1.locked != 1
					AND c1.user_type = '".$user_type_id."'
					ORDER BY c1.lname, c1.fname, c1.mname, c1.id ";
			$rez = sqlStatement($sql);
			for($i=0; $row=sqlFetchArray($rez); $i++){

				$id = $row["id"];

				$tmp = "";
				$tmp = $this->getUNameFormatted(4,$row);

				if(!empty($tmp)){

					$sel_tmp = (in_array($id,$arr_sel_id)) ? "Checked" : "";
					$c = $c+1;
					$str_sel_phy_tmp.="
									<li class=\"list-group-item\">
										<div class=\"checkbox\"><input type=\"checkbox\"  id=\"chk_sel_".$c."\" value=\"".$id."\" ".$sel_tmp." ><label for=\"chk_sel_".$c."\">".$tmp."</label></div>
									</li>
									";
				}

			}

			if(!empty($str_sel_phy_tmp)){
				$cc=$cc+1;
				//$str_sel_phy.="<optgroup label=\"".$row2["user_type_name"]."\">".$str_sel_phy_tmp;
				//$str_sel_phy.="</optgroup>";

				$str_sel_phy.="
						<div class=\"panel panel-default\" id=\"dv_usrcat_".$user_type_id."\">
						<div class=\"panel-heading\">
							<div class=\"checkbox\" ><input type=\"checkbox\"  id=\"chk_cat_".$cc."\" value=\"".$cc."\"  >
							<label for=\"chk_cat_".$cc."\">
							</label>
							 </div>
							<h4 class=\"panel-title\" >

							 <a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse".$cc."\">".$row2["user_type_name"]."
							 </a>
							</h4>
						</div>
						<div id=\"collapse".$cc."\" class=\"panel-collapse collapse\">
							<div class=\"panel-body\">
								<ul class=\"list-group\">".$str_sel_phy_tmp."
								</ul>
								</div>
							</div>
						</div>
						";
			}
		}

		if(!empty($str_sel_phy)){
			$str_sel_phy = "<div class=\"panel-group\" id=\"usr_acrdn\">".$str_sel_phy."</div>";
		}

		return $str_sel_phy;
	}

	function getUserArr($format="", $type=""){

		$utype="";
		if(empty($type) || $type=="cn"){	$utype=implode(",",$GLOBALS['arrValidCNPhy']); }
		else if($type=="cn_all"){
			$utype = "";
			$utype .= implode($GLOBALS['arrValidCNPhy'],",");
			$utype .= implode($GLOBALS['arrValidCNTech'],",");
		}else	if($type=="cn_tech"){$utype .= implode($GLOBALS['arrValidCNTech'],",");}
		else if($type=="cn_phy"){$utype .= implode($GLOBALS['arrValidCNPhy'],",");}
		else if($type=="all"){$utype = "";} // all users

		//
		if(!empty($utype)){ $utype = " c1.user_type IN (".$utype.") AND "; }

		$ar_sel_phy=array();
		$sql="SELECT c1.id, c1.fname, c1.mname, c1.lname, c1.pro_suffix, c2.user_type_name
				FROM users c1
				LEFT JOIN user_type c2 ON c2.user_type_id = c1.user_type
				WHERE  ".$utype."
				c1.delete_status != 1 AND c1.locked != 1
				ORDER BY c1.lname, c1.fname, c1.mname, c1.id ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$id = $row["id"];
			$tmp = $this->getUNameFormatted($format,$row);
			$ar_sel_phy[$id]=$tmp;
		}

		return $ar_sel_phy;

	}

	function checkPermissions($flg=""){
		$retv = 0;
		$sql = "SELECT access_pri FROM users WHERE id = '".$this->uid."'  ";
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		if($res != false){
			$ret = unserialize(html_entity_decode(trim($res["access_pri"])));
		}
		if(!empty($ret) && count($ret)>0){
			if($flg == "checkAccessChartVO" && $ret["priv_vo_clinical"]==1){
				$retv = 1;
			}
		}
		return $retv;
	}

	//Check if providerId is tech or scribe: if yes, then get follow to physician,
	function getFollowPhyId4Tech(){
		$ret=0;
		if(in_array($_SESSION["logged_user_type"], $GLOBALS['arrValidCNTech']) || $_SESSION["logged_user_type"] == "13"){
			$providerId = $this->uid;
			$sql = "SELECT follow_physician FROM users WHERE id = '".$providerId."' ";
			$row = sqlQuery($sql);
			if($row != false){
				$follow_physician = $row["follow_physician"];
				//
				if(!empty($follow_physician)){
					////get physician id from pt scheduler appointment --
					//$ret = $follow_physician;
					if(!empty($_SESSION["patient"])){

						//
						$tmp_form_id = "";
						if(!empty($_SESSION["form_id"])){
							$tmp_form_id = $_SESSION["form_id"];
						}else if(!empty($_SESSION["finalize_id"])){
							$tmp_form_id = $_SESSION["finalize_id"];
						}
						if(!empty($tmp_form_id)){
							//
							$sql = "SELECT c2.sa_doctor_id, c1.date_of_service from chart_master_table c1
									LEFT JOIN schedule_appointments c2 ON c2.sa_patient_id=c1.patient_id AND c1.date_of_service=c2.sa_app_start_date
									WHERE c1.id='".$tmp_form_id."' AND c1.patient_id='".$_SESSION["patient"]."' ";
							$row = sqlQuery($sql);
							if($row!=false){
								$ret = $row["sa_doctor_id"];
							}
						}

						if(empty($ret)){//if still empty then get last appointment doctor id
							$sql = "
									SELECT sa_doctor_id, sa_app_start_date  FROM `schedule_appointments`
									WHERE sa_patient_id = '".$_SESSION["patient"]."'
									ORDER BY sa_app_start_date DESC
									LIMIT 0, 1	";
							$row = sqlQuery($sql);
							if($row!=false){
								$ret = $row["sa_doctor_id"];
							}
						}
					}
				}
			}
		}
		return $ret;
	}

	function getPhyRefSetting(){
		$oAdmn = new Admn();
		$ret=$oAdmn->cp_getRefSetting();

		if($ret[0] == false){
			$id = $this->uid;
			$sql = "SELECT collect_refraction FROM users WHERE id = '".$id."' ";
			$row = sqlQuery($sql);
			if($row != false){
				$ret[0]= ($row["collect_refraction"] == "1") ? true : false;
			}
		}

		return $ret;
	}

	static function getProviderColors($flgAr=0){
		$arr=array();
		$sql = "SELECT user_type_id, color FROM user_type WHERE color != '' ";
		$rez=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			if(!empty($row["color"])){
				$id=$row["user_type_id"];

				$tmp = $row["color"];
				//
				if(strpos($row["color"],",")!==false){
					$tmp = explode(",",$row["color"]);
				}
				$arr[$id]=$tmp;
			}
		}
		if($flgAr==1){
			return $arr;
		}else{
			return json_encode($arr);
		}
	}

	function show_refer_phy_modal(){
		$result_return = '';
		$user_type_arr[1] = 'PHYSICIAN';
		$user_type_arr[12] = 'ATTENDING PHYSICIAN';
		$query = "SELECT id, fname, mname, lname, user_type FROM users WHERE (user_type = 1 or user_type = 12) and  delete_status = 0 ORDER BY lname";
		$phy_result_obj = imw_query($query);
		if(imw_num_rows($phy_result_obj)>0)
		{
			while($phy_data = imw_fetch_assoc($phy_result_obj))
			{
				$tmp = $this->getUNameFormatted(1,$phy_data);
				$result_return .= '<tr><td >'.$tmp.'</td>
							<td >'.$user_type_arr[$phy_data['user_type']].'</td>
							<td ><input onClick="follow_main_att_phy('.$phy_data['id'].');" type="button" name="" value="Follow" class="btn btn-success" /></td></tr>';
			}
		}

		if($result_return!=""){
			$result_return='<!-- Modal -->
			<div id="res_fel_div_modal" class="modal fade" role="dialog">
			  <div class="modal-dialog">

			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Please Choose the Physician to Follow</h4>
			      </div>
			      <div class="modal-body">
					<div class="table-responsive">
					<table class="table table-striped"><tr><th>PHYSICIAN NAME</th><th>TYPE</th><th>ACTION</th></tr>'.$result_return.'</table>
					</div>
			      </div>
			      <div class="modal-footer text-center">
				<center>
				<button type="button" class="btn btn-success" onclick="follow_main_att_phy(0);" >Un-Follow</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</center>
			      </div>
			    </div>

			  </div>
			</div>';
		}
		echo $result_return;
	}

	function get_user_type_nm($uti){
		$ret="";
		$sql = "SELECT user_type_name FROM user_type WHERE user_type_id = '".$uti."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$ret = $row["user_type_name"];
		}
		return $ret;
	}

	function get_user_info(){
		$ret="";
		if(!empty($this->uid)){
			$sql = "SELECT CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME, fname, mname, lname, pro_suffix, licence, user_npi FROM users WHERE id = '".$this->uid."'";

			$row = sqlQuery($sql);
			if($row!=false){
				$phy_fname 	   = $row['fname'];
				$phy_mname 	   = $row['mname'];
				$phy_lname     = $row['lname'];
				$phy_fullname  = $row['PHYSICIANNAME'];
				$phy_suffix    = $row['pro_suffix'];
				$phy_npi	   = $row['user_npi'];
				$phy_lic 	   = $row['licence'];

				$ret = array($phy_fname,$phy_mname,$phy_lname,$phy_fullname,$phy_suffix,$phy_npi,$phy_lic);
			}
		}
		return $ret;
	}

}


?>
