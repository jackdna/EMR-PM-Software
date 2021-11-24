<?php
	include_once $GLOBALS['srcdir'].'/classes/common_function.php';
	include_once $GLOBALS['srcdir'].'/classes/work_view/wv_functions.php';

	class Smart_chart{
		public $patient_id = '';
		public $auth_id = '';
		public $dos = '';

		//Task List
		public $strTasks = '';


		//Check Arrays--
		public $arTaskChk=array();
		public $arAsssChk=array();
		public $arPhyAsssChk=array();
		public $arCommAsssChk=array();
		public $arDynAsssChk=array();

		//strAssessonly
		public $strAssessonly="";

		public function __construct($pid,$auth_id,$dos){
			$this->patient_id = $pid;
			$this->auth_id = $auth_id;
			$this->dos = $dos;
			$this->tmp_providerId = $this->auth_id;
			//Check if providerId is tech or scribe: if follow to physician, then apply his assessments and policy
			$isTech = $this->getFollowPhyId4Tech($this->tmp_providerId);
			if(!empty($isTech)){
				$this->tmp_providerId = $isTech;
			}
			//Site Arr
			$this->arr1 = array("OU","OD","OS");
			$this->arr2 = array("RUL","RLL","LUL","LLL");
			$this->arr3 = array("-ve", "T", "1", "2", "3", "4");

		}

		static public function sqlExe($sql){
			$return_arr = array();
			if(empty($sql))return false;
			$qry = imw_query($sql);
			if($qry !== false){
				while($row = imw_fetch_array($qry)){
					$return_arr[] = $row;
				}
			}
			return $return_arr;
		}

		public function get_username_by_id($id_arr = array()){
			$filterById = '';
			$result_arr = array();
			if(count($id_arr)>0)
			{
				$id_arr_str = implode(',',$id_arr);
				$filterById = ' and id IN ('.$id_arr_str.')';
			}

			$reqQry = 'select id, concat(SUBSTRING(fname,1,1),SUBSTRING(lname,1,1)) as short_name, concat(lname,", ",fname) as medium_name, concat(lname,", ",fname," ",mname) as full_name from users where id > 0 '.$filterById.' and delete_status = 0 order by lname,fname';
			$resultOb = imw_query($reqQry);
			while($result_row = imw_fetch_assoc($resultOb))
			{
				$result_arr[$result_row['id']]['short'] = $result_row['short_name'];
				$result_arr[$result_row['id']]['medium'] = $result_row['medium_name'];
				$result_arr[$result_row['id']]['full'] = $result_row['full_name'];
			}
			return $result_arr;
		}

		public function sc_getExamMenuOptions($arr, $id="", $css="", $exm="",$lvl=0){
			$ret = "";
				if(count($arr) > 0){
					foreach($arr as $key => $val){
						if(is_array($val) && count($val) > 0){
							$ret .="<li class='dropdown-submenu'><a href=\"#\" ><label>".$key."</label><span class='glyphicon glyphicon-chevron-right pull-right'></span></a>";
							$ret .= $this->sc_getExamMenuOptions($val,"","",$key,$lvl+1);
							$ret .="</li>";
						}else if(!empty($val)){
							if(!empty($exm) && $lvl>1){
								$data_tmp =  " data-exam=\"".$exm."\" " ;
							}
							$ret .="<li><a href=\"#\" onclick=\"sc_searchExam(this);\" ".$data_tmp.">".$val."</a></li>";
						}
					}
					$strId="";
					if(!empty($id)){
						$strId = " id=\"".$id."\" ";
					}
					if(!empty($css)){
						$strCss = " style=\"".$css."\"  ";
					}
					$ret_drop_down .= "<ul ".$strId." ".$strCss." class='dropdown-menu dropdown-menu-right  multi-level' role='menu'>".$ret."</ul>";
				}
			return $ret_drop_down;
		}

		static public function FormatDate_insert($dt){
			if(!empty($dt))
			{
				if(preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/",$dt,$regs))
				{
					$dt=$regs[3]."-".$regs[1]."-".$regs[2];
				}else if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/",$dt,$regs)){
					$dt=$regs[1]."-".$regs[2]."-".$regs[3];
				}
			}
			return $dt;
		}

		public function getFollowPhyId4Tech(){
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

		public function recursive_array_search($needle,$haystack) {
			foreach($haystack as $key=>$value) {
				$current_key=$key;
				if($needle==$value || (is_array($value) && $this->recursive_array_search($needle,$value) !== false)) {
					return $current_key;
				}
			}
			return false;
		}

		public function get_task_list(){
			$sql = imw_query("Select * from console_to_do
					where (providerID='".$this->tmp_providerId."' OR providerID='0')
					ORDER BY task,providerID DESC, (CASE WHEN task='' OR task IS NULL then 1 ELSE 0 END),   assessment ASC ");
			$arTaskChk = array();
			$i = 0;
			while($row = imw_fetch_array($sql)){
				$todoid = $row["to_do_id"];
				$assess = ucwords(strtolower(trim($row["assessment"])));
				//Empty Assessment Not to show
				if(empty($assess)){continue;}

				//dx code
				$tdx = trim($row["dxcode_10"]);
				if(!empty($tdx)){
					$oDx = new Dx();
					list($tdx, $tdxId) = $oDx->refineDx($tdx);
					if(!empty($tdx)){
					//check if Dx belongs to dos: if not continue;
					if(!Dx::isDxCodeBelongsToDos($this->dos, $tdx, $tdxId)){
						continue;
					}
					}
				}

				$task = trim($row["task"]);
				$dynamic_ap = $row["dynamic_ap"];
				if(!empty($task)){
					$symptom = (strpos($task,";")!==false) ? explode(";",$row["task"]) : explode(",",$row["task"]);
				}else{
					$symptom = array() ;
				}
				if(count($symptom)>0){
					//$str="";
					$lnSump = count($symptom);
					for($j=0;$j<$lnSump;$j++){

						$symptom_tmp = trim($symptom[$j]);
						if(empty($symptom_tmp)){
							continue;
						}else{
							//Check Duplicates tasks: //check duplicacy with community symptopms only. multiple symtoms are allowed
							if(in_array($symptom_tmp,$arTaskChk)){ //show signle symptom and show all assocaiated AP if opened
								continue;
							}else{ $arTaskChk[]=$symptom_tmp; }
						}
						//Set i+j as iter
						$iter = $i.$j;
						$str .= '<li class="list-group-item pointer" onclick="top.fmain.sc_showExamDetails(\''.$iter.'\')">'.ucwords(strtolower($symptom_tmp)).'
							<input type="hidden" name="elem_symptom_'.$iter.'" value="'.$symptom_tmp.'">
							<input type="hidden" name="elem_todoid_'.$iter.'" value="'.$todoid.'">
						</li>';
					}
					$row['symptom_tmp'] = $symptom_tmp;
				}
				$task_arr[] = $row;
				$i++;
			}
			//Creates Assessment arrays
			$this->get_assessment_arr($task_arr);

			if(!empty($str)){

				$new_string = '<ul id="sc_con_symptons" class="list-group"><li class="list-group-item active">Symptoms</li>'.$str.'</ul>';
			}
			//tasks --
			$this->strTasks = $new_string;
		}

		//Creates Assessment arr for COMMUNITY, PHYSICIAN, DYNNAMIC
		public function get_assessment_arr($arr){
			foreach($arr as $row){
				$symptom_tmp = $row['symptom_tmp'];
				$assess = ucwords(strtolower(trim($row["assessment"])));
				$dynamic_ap = $row["dynamic_ap"];
				//Empty Assessment Not to show
				if(empty($assess)){continue;}
				if($row["providerID"] == 0){	// COMMUNITY ASSESSMENT ARRAY
					if($key == ""){ $this->arCommAsssChk[]=array("asses"=>$assess,"site_type"=>$row['site_type'],"todoid"=>$row["to_do_id"],"symptom_tmp"=>$symptom_tmp);}
				}
				if($row["providerID"] == $_SESSION['authId'] && empty($dynamic_ap)){	// PHYSICIAN ASSESSMENT ARRAY
					$key = $this->recursive_array_search($assess,$this->arPhyAsssChk);
					if($key == ""){ $this->arPhyAsssChk[]=array("asses"=>$assess,"site_type"=>$row['site_type'],"todoid"=>$row["to_do_id"],"symptom_tmp"=>$symptom_tmp);}
				}
				if($row["providerID"] == $_SESSION['authId'] && $dynamic_ap == 1){ // DYNAMIC ASSESSMENT ARRAY
					$key = $this->recursive_array_search($assess,$this->arDynAsssChk);
					if($key == ""){ $this->arDynAsssChk[]=array("asses"=>$assess,"site_type"=>$row['site_type'],"todoid"=>$row["to_do_id"],"symptom_tmp"=>$symptom_tmp);}
				}
				if(in_array($assess,$this->arAsssChk)){continue;}else{ $this->arAsssChk[]=$assess; } //Assess duplicates
			}
		}


		//Returns HTML for Community Assessment
		public function get_community_html(){
			$strCommuAssess = '';
			global $chkCount;
			$chkCount = 0 ;
			foreach($this->arCommAsssChk as $index=>$arrCommu){
					$strLvl=$strComm=$symptom_tmp="";
					$iter = $chkCount."0";
					$strSite = '';
					if($arrCommu['site_type'] == 0){
						foreach($this->arr1 as $key => $val){
							$strSite .= "<li calss='col-sm-4'><input type=\"checkbox\" id=\"elem_site_".$iter.$key."\" name=\"elem_site_".$iter."[]\" value=\"".$val."\" onclick=\"setSmartApEye('elem_site_',".$iter.",this)\" id=\"elem_site_".$iter.$val."\"><label for= \"elem_site_".$iter.$key."\">".$val."</label></li>";
						}
					}else if($arrCommu['site_type'] == 1){
						foreach($this->arr2 as $key => $val){
							$strSite .= "<li><input type=\"checkbox\" id=\"elem_site_".$iter.$key."\" name=\"elem_site_".$iter."[]\" value=\"".$val."\" onclick=\"setSmartApEye('elem_site_',".$iter.",this)\" id=\"elem_site_".$iter.$val."\"><label for= \"elem_site_".$iter.$key."\">".$val."</label></li>";
						}
					}

					$str = '
					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-8">
								<input type="checkbox" id="elem_ap_assess_'.$chkCount.'" name="elem_ap_assess[]" value="'.$iter.'"
								onclick="setSmartAssessEye(\'elem_site_\',\''.$iter.'\',this)"><label for="elem_ap_assess_'.$chkCount.'">'.$arrCommu['asses'].'</label>
								<input type="hidden" name="elem_symptom_'.$iter.'" value="'.$arrCommu['symptom_tmp'].'">
								<input type="hidden" name="elem_todoid_'.$iter.'" value="'.$arrCommu['todoid'].'">
							</div>
							<div class="col-sm-4">
								<ul class="list-inline list-group">
									'.$strSite.'
								<ul>
							</div>
						</div>
					</div>';
					if(empty($str) === false){
						$strCommuAssess.=$str;
					}
				$chkCount++;
			}
			if(strlen($strCommuAssess) == 0){
				$strCommuAssess = 'No record found';
			}
			return $strCommuAssess;
		}

		//Returns HTML for Physician Assessment
		public function get_physician_html(){
			global $chkCount;
			$strPhyAssess = '';
			foreach($this->arPhyAsssChk as $index=>$arrPhy){
					$strLvl=$strComm=$symptom_tmp="";
					$iter = $chkCount."0";
					$strSite = '';
					if($arrPhy['site_type'] == 0){
						foreach($this->arr1 as $key => $val){
							$strSite .= "<li class='col-sm-4'><input type=\"checkbox\" id=\"elem_site_".$iter.$key."\" name=\"elem_site_".$iter."[]\" value=\"".$val."\" onclick=\"setSmartApEye('elem_site_',".$iter.",this)\" id=\"elem_site_".$iter.$val."\"><label for= \"elem_site_".$iter.$key."\">".$val."</label></li>";
						}
					}else if($arrPhy['site_type'] == 1){
						foreach($this->arr2 as $key => $val){
							$strSite .= "<li class='col-sm-4'><input type=\"checkbox\" id=\"elem_site_".$iter.$key."\" name=\"elem_site_".$iter."[]\" value=\"".$val."\" onclick=\"setSmartApEye('elem_site_',".$iter.",this)\" id=\"elem_site_".$iter.$val."\"><label for= \"elem_site_".$iter.$key."\">".$val."</label></li>";
						}
					}
					$str = '
					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-8">
								<input type="checkbox" id="elem_ap_assess_'.$chkCount.'" name="elem_ap_assess[]" value="'.$iter.'"
								onclick="setSmartAssessEye(\'elem_site_\',\''.$iter.'\',this)"><label for="elem_ap_assess_'.$chkCount.'">'.htmlspecialchars($arrPhy['asses']).'</label>
								<input type="hidden" name="elem_symptom_'.$iter.'" value="'.$arrPhy['symptom_tmp'].'">
								<input type="hidden" name="elem_todoid_'.$iter.'" value="'.$arrPhy['todoid'].'">
							</div>
							<div class="col-sm-4">
								<ul class="list-inline list-group">
									'.$strSite.'
								<ul>
							</div>
						</div>
					</div>';
					if(empty($str) === false){
						$strPhyAssess.=$str;
					}
			$chkCount++;
			}
			if(strlen($strPhyAssess) == 0){
				$strPhyAssess = 'No record found';
			}
			return $strPhyAssess;
		}

		//Returns Dynamic assessment HTML
		public function get_dynamic_assessment_html(){
			global $chkCount;
			$strDynAssess = '';
			foreach($this->arDynAsssChk as $index=>$arrDyn){
					$strLvl=$strComm=$symptom_tmp="";
					$iter = $chkCount."0";
					$strSite = '';
					if($arrDyn['site_type'] == 0){
						foreach($this->arr1 as $key => $val){
							$strSite .= "<li class='col-sm-4'><input type=\"checkbox\" id=\"elem_site_".$iter.$key."\" name=\"elem_site_".$iter."[]\" value=\"".$val."\" onclick=\"setSmartApEye('elem_site_',".$iter.",this)\" id=\"elem_site_".$iter.$val."\"><label for= \"elem_site_".$iter.$key."\">".$val."</label></li>";
						}
					}else if($arrDyn['site_type'] == 1){
						foreach($this->arr2 as $key => $val){
							$strSite .= "<li class='col-sm-4'><input type=\"checkbox\" id=\"elem_site_".$iter.$key."\" name=\"elem_site_".$iter."[]\" value=\"".$val."\" onclick=\"setSmartApEye('elem_site_',".$iter.",this)\" id=\"elem_site_".$iter.$val."\"><label for= \"elem_site_".$iter.$key."\">".$val."</label></li>";
						}
					}

					$str = '
					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-8">
								<input type="checkbox" id="elem_ap_assess_'.$chkCount.'" name="elem_ap_assess[]" value="'.$iter.'"
								onclick="setSmartAssessEye(\'elem_site_\',\''.$iter.'\',this)"><label for="elem_ap_assess_'.$chkCount.'">'.$arrDyn['asses'].'</label>
								<input type="hidden" name="elem_symptom_'.$iter.'" value="'.$arrDyn['symptom_tmp'].'">
								<input type="hidden" name="elem_todoid_'.$iter.'" value="'.$arrDyn['todoid'].'">
							</div>
							<div class="col-sm-4">
								<ul class="list-inline list-group">
									'.$strSite.'
								<ul>
							</div>
						</div>
					</div>';
					if(empty($str) === false){
						$strDynAssess.=$str;
					}
				$chkCount++;
			}
			if(strlen($strDynAssess) == 0){
				$strDynAssess = 'No record found';
			}
			return $strDynAssess;
		}
	}
?>
