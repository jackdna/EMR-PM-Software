<?php
//Orders.php
class Orders{
	private $ar_order_type ;
	public function __construct(){		
		$this->ar_order_type = array("1"=>"Meds", "2"=>"Labs", "3"=>"Imaging/Rad", "4"=>"Procedure/Sx",  "5"=>"Information/Instructions");
	}

	function saveOrder($ar){
	
		if(!empty($ar["id"])){
	
			$sql1 = " UPDATE order_details SET ";
						
			$sql3 = " where id='".$ar["id"]."' ";
		
		}else{
		
			$sql1 = " INSERT INTO order_details SET ";
					
			$sql3 = "  ";
		
		}
		
		$str = "";
		if(count($ar)>0){
			foreach($ar as $colnm => $val){
				if(!empty($str)){ $str .= ","; }
				$str .= "".$colnm."='".sqlEscStr($val)."' ";
			}
		}
		
		if(!empty($str)){
			$sql =  $sql1.$str.$sql3;
			if(empty($ar["id"])){
			    //echo $sql;exit;
				return sqlInsert($sql);
			}else{
			    //echo $sql;exit;
				return sqlQuery($sql);
				return $ar["id"];
			}
		}
		return 0;
	}
	
	function get_emdeon_warnings(){
		$str_or_asc_id = $_GET["str_or_asc_id"];
		$str_or_asc_id=urldecode($str_or_asc_id);
		
		$str_or_det_id = $_GET["str_or_det_id"];
		$str_or_det_id=urldecode($str_or_det_id);			
		
		if(!empty($str_or_det_id)){
			$arr_or_det_id=explode(",", $str_or_det_id);
		}else{
			$arr_or_det_id=array();
		}
		
		
		
		if(!empty($str_or_asc_id)){
		$sql = "SELECT  c2.order_id
			FROM  order_set_associate_chart_notes_details c2 				
			WHERE c2.order_set_associate_details_id IN (".$str_or_asc_id.") ".				
			"";
		
		$rez=sqlStatement($sql);	
		for($i=1;$row=sqlFetchArray($rez);$i++){
			if(!empty($row["order_id"])){
				$arr_or_det_id[]=$row["order_id"];
			}
		}
		}
		
		
		
		//
		if(count($arr_or_det_id)>0){
		$str_or_det_id_tmp = implode(",", $arr_or_det_id);			
		}
		if(!empty($str_or_det_id_tmp)){				
			$xml="";
			$sql="SELECT id, name, o_type, order_type_id from order_details WHERE id IN (".$str_or_det_id_tmp.") AND (o_type='Meds' OR order_type_id='1') ";
			$rez=sqlStatement($sql);	
			for($i=1;$row=sqlFetchArray($rez);$i++){	

				$order_id= $row["id"];
				$order_name= $row["name"];
				$order_type= $row["o_type"];
				if(empty($order_type) && $row["order_type_id"]==1){ $order_type= "Meds";  }
				
				//send for  emdeon --
				$tmpxml = $this->cpoe_getXmlOfMeds($order_id, $order_name, $order_type);
				if(!empty($tmpxml)){$xml.=$tmpxml;}
				
			}
			
			$strError = $this->cpoe_checkMedwithEmdeon($xml);	
		}
		
		if(empty($strError)){$strError="No warning!";}
		
		//$strError = "<ul><li>This is warning to you.</li></ul>";
		
		echo $strError;
	
	}

function write_erx_log($url,$request_data='',$response_data='',$error=''){
	$q = "INSERT INTO erx_log SET 
	curl_url	= '".$url."', 
	request_data	= '".addslashes($request_data)."', 
	response_data	= '".addslashes($response_data)."', 
	user_id	= '".$_SESSION['authId']."', 
	patient_id	= '".$_SESSION["patient"]."', 
	request_datetime	= '".date('Y-m-d H:i:s')."', 
	error	= '".$error."'";
	imw_query($q);//echo $q.'<br>'.imw_error().'<hr>';
}

function cpoe_checkMedwithEmdeon($xml){
	$strError="";
	if(!empty($xml)){
		
		//--
		
		$erx_credentialsRes = sqlStatement("SELECT eRx_user_name, erx_password, eRx_facility_id FROM users WHERE id = '".intval($_SESSION['authId'])."'");
		$erx_URLres 		= sqlStatement("SELECT EmdeonUrl FROM copay_policies WHERE policies_id = '1'");
		$erx_credentials 	= sqlFetchArray($erx_credentialsRes);
		$erx_URLrs	 		= sqlFetchArray($erx_URLres);
		$user				= $erx_credentials['eRx_user_name'];
		$pass				= $erx_credentials['erx_password'];
		$facId				= trim($_SESSION['login_facility_erx_id']);//$erx_credentials['eRx_facility_id'];
		$emdeonURL			= $erx_URLrs['EmdeonUrl'];
		$erx_severity_lvl_admin     = $this->cpoe_getERxSeverityAdmin();
		
		//--
		 
		$xml="<?xml version=\"1.0\" ?>
				<REQUEST userid=\"".$user."\" password=\"".$pass."\" facility=\"".$facId."\" >
				".$xml.
				"</REQUEST>";
		
		//pass to curl-
		 if($xml != '' && $user != '' && $pass != '' && $facId != ''){
		 
			//log the input
			//$inputtime=time();	
			//file_put_contents ( dirname(__FILE__)."/tmp/emdeon_resquest_".$inputtime.".xml" , $xml );

			$URL = $emdeonURL."/servlet/XMLServlet";
			
			//echo $URL;
			
			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_MUTE, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "request=$xml");
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			if($output === false){ $strError = 'Curl error: '.curl_error($ch); }
			// Close handle
			curl_close($ch);
			
			$this->write_erx_log($URL,$xml,$output,$strError);
			//echo "<pre>";
			//var_dump($output);
			
			if($output!=''){
			
				//log the output			
				//file_put_contents ( dirname(__FILE__)."/tmp/emdeon_response_".$inputtime.".xml" , $output );
			
				//require_once(dirname(__FILE__)."/../../common/CLSCommonFunction.php");
				//$OBJCommonFunction = new CLSCommonFunction;
				//$values = $OBJCommonFunction -> XMLToArray($output);	
				//Array Severity Description
				$arrSvrtyDesc=array("1"=>"Avoid combination","2"=>"Usually avoid combination","3"=>"Minimize risk","4"=>"No action needed","5"=>"No interaction");
				
				$values = simplexml_load_string($output);
				$xml_med_arr=array();
				foreach($values->OBJECT as $val){	
					
					if((!empty($erx_severity_lvl_admin) && $val->severity>$erx_severity_lvl_admin) || empty($erx_severity_lvl_admin)){
					$temp = trim($val->severity);
					if(empty($temp)){ $temp="Not Set"; }
					//$xml_med_arr['reaction'][] = $val->reaction." [".$arrSvrtyDesc["".$val->severity]."]";
					$xml_med_arr['reaction'][] = $val->reaction." [ Severity Level: ".$temp." ]";
					
					}
					
					/*
					if($val["tag"] =="name"){	
						$xml_med_arr['name'][]=$val["value"];
					}
					if($val["tag"]=="id"){
						$xml_med_arr['id'][]=$val["value"];	
					}
					if($val["tag"]=="severity"){
						$xml_med_arr['severity'][]=$val["value"];	
					}
					
					if($val["tag"]=="reaction"){
						$xml_med_arr['reaction'][]="<pre><xmp>".$output."</xmp></pre>"; //$val["value"];	
					}
					
					if($val["tag"] =="drug_name"){	
						$xml_med_arr['drug_name'][]=$val["value"];
					}
					if($val["tag"] =="allergen"){	
						$xml_med_arr['allergen'][]=$val["value"];
					}
					*/
				}

				if(count($xml_med_arr['reaction'])>0){
					$strError = "<ul><li>".implode("</li><li>", $xml_med_arr['reaction'])."</li></ul>";
				}
			}
			
		 }
		//	
	}
	return $strError;
}

//
function cpoe_getXmlOfMeds($orid, $ornm, $ortp, $xml_ordr_given="", $xml_allergy_given=""){
	
	//if($ortp!="Meds"){return;};
	$xml="";
	//global $arr_current_meds_notsaved, $arr_current_meds_notsaved_all;
	$arr_current_meds_notsaved = $arr_current_meds_notsaved_all =array();
	$new_med = $ornm;
	$new_med_type = $ortp;
	$new_med_id = $this->cpoe_getFDBid($orid, $new_med);		
	$arr_current_meds_notsaved_all[$new_med]=$new_med_id;
	
	if(!empty($new_med_id)){		
		$old_xml ="";
		//Get PrevOrdersGiven to pt.		
		$old_xml .=$xml_ordr_given;
		
		/*not in use*/
		if(count($arr_current_meds_notsaved)>0){						
			//existing meds
			foreach($arr_current_meds_notsaved as $key=> $val){
				$tmp_med_nm = $val[0];
				$old_med_id=0;
				if(isset($arr_current_meds_notsaved_all[$tmp_med_nm]) && !empty($arr_current_meds_notsaved_all[$tmp_med_nm])){
					$old_med_id = $arr_current_meds_notsaved_all[$tmp_med_nm];
				}else{
					$old_med_id = 0;
				}
				if(!empty($old_med_id)){
					//$arr["old"][]=array("name"=>$tmp_med_nm, "id"=>$old_med_id);
					//xml
					 $old_xml .= "<drug id=\"".$old_med_id."\" name=\"".$tmp_med_nm."\" />";
				}
			}			
		}
		/*not in use*/
		
		//
		if(!empty($old_xml)){
			//pass 
			$old_xml="<ExistingMeds>".$old_xml."</ExistingMeds>";
			
			$xml.="<OBJECT name=\"dur\" op=\"screenDrugs\">
					<new_drug_name>".$new_med."</new_drug_name>".
					"<new_drug_id>".$new_med_id."</new_drug_id>".
					$old_xml.
					"</OBJECT>";
		}

		//existing allergies
		if(!empty($xml_allergy_given)){
			//pass 
			$xml_allergy="<Allergies>".$xml_allergy_given."</Allergies>";
			
			$xml.="<OBJECT name=\"dur\" op=\"screenAllergies\">
					<new_drug_name>".$new_med."</new_drug_name>".
					"<new_drug_id>".$new_med_id."</new_drug_id>".
					$xml_allergy.
					"</OBJECT>";
		}
		
		$arr_current_meds_notsaved[]=array($new_med, $new_med_id);
		
	}
	return $xml;
}

function cpoe_getERxSeverityAdmin(){
	$ret = 0;
	$sql = " SELECT cpoe_severity FROM copay_policies  ";
	$row= sqlQuery($sql);
	if($row != false && !empty($row["cpoe_severity"])){
		$ret = $row["cpoe_severity"];
	}
	return $ret;
}

function cpoe_getFDBid($orid, $ornm){
	//$sql="select medicine_name as name, fdb_id as id from medicine_data where medicine_name='".$ornm."' limit 0,1 ";
	$sql="select fdb_id from order_details where id='".$orid."' limit 0,1 ";
	$row=sqlQuery($sql);
	if($row!=false && !empty($row["fdb_id"])){
		return $row["fdb_id"];
	}
	return 0;
}

function showOrdersInAP(){
	$ordertype = ucfirst(strtolower($_GET["ordertype"]));
	$assi = $_GET["assi"];
	$srch = $_GET["srch"];
	
	$str="";
	
	$searchphrase=$norecstr="";
	if(isset($_GET["srch"]) && !empty($_GET["srch"])){
		$searchphrase=" AND name LIKE '".sqlEscStr($_GET["srch"])."%' ";
		$norecstr=" with name like '".sqlEscStr($_GET["srch"])."' ";
		
	}
	
	$phrsCount="";
	if($ordertype=="Meds"){
		$phrsCount=" , count(*) as num ";
	}
	
	//$arr_ordername=array();
	$sql = "select name, id,snowmed ".$phrsCount."  from order_details where delete_status = '0' AND o_type ='".$ordertype."' ".$searchphrase." ";
	if($ordertype=="Meds"){$sql .= "		GROUP BY name ";}
	$sql .= "		order by name";
	$rez = sqlStatement($sql);
	for($i=0; $row=sqlFetchArray($rez);$i++){
		$sno_med="";if($row["snowmed"]){$sno_med="<span class=\"snw\"> (SNOMED CT: ".$row["snowmed"].")</span>";}
		if($ordertype=="Meds"){
			//	
				if($row["num"]>1){
					$tmp = $this->orlist_getOrdersByName($row["name"], $row["id"], $ordertype);
					if(!empty($tmp)){
						$pntr= "<span class=\"pointer  ui-icon ui-icon-circle-arrow-e\" onmouseover=\"showrelorders('".$row["id"]."',1, this)\" onmouseout=\"showrelorders('".$row["id"]."',0)\" ></span>";							
						$str.="<div id=\"dv_relorders".$row["id"]."\" class=\"relorders\" onmouseover=\"showrelorders('".$row["id"]."',3)\" onmouseout=\"showrelorders('".$row["id"]."',0)\" >".$tmp."</div>";
						$prnt_sufx="master";
						$prnt_js=" onclick=\"checkrelorders(this)\" ";
					}
				}else{
					$pntr="";
					$nmsufx="";
					$prnt_js="";
					$prnt_sufx="";
				}						
				
				$str.= "<input type=\"checkbox\" id=\"elem_inorder".$i.$prnt_sufx."\" value=\"".$row["id"]."\" ".$prnt_js." >";//onclick=\"showOrderDetail('".$assi."', '".$row["id"]."')\"
				$str.="<label for=\"elem_inorder".$i.$prnt_sufx."\">".$row["name"]." ".$sno_med."</label>".$pntr."<br/>";
			
			
		}else{
		$str.="<label onclick=\"showOrderDetail('".$assi."', '".$row["id"]."')\" >".$row["name"]." ".$sno_med."</label><br/>";	
		}
	}		
	
	if(!empty($str)){
		if($ordertype=="Meds"){				
		$btnAdOrdr=" <input type=\"button\" class=\"btn btn-info\" name=\"elem_btnorderdone\" value=\"Done\" onclick=\"showOrderDetailStart('".$assi."','".$ordertype."')\" > ";
		}
	}else{
		$str = "<label>No Order exists for '".$ordertype."' ".$norecstr." </label><br/>";
	}
	$strAdNw .= " <label onclick=\"showOrderDetail('".$assi."', '0', '0','".$ordertype."')\" class='a_clr1'>Add new Order</label>";
	
	if(isset($_GET["srch"])){
		
		echo "<div class=\"cont\">".$str."</div>";
		
	}else{
	
		echo "
			<div id=\"divOrderList\" class=\"OrderList\" >
				<div class=\"purple_bar form-inline\">
				<span class=\"glyphicon glyphicon-remove pull-right\" onclick=\"$('.OrderList').remove();\"></span>
				$ordertype Orders <input type=\"text\" id=\"elem_searchOrders\" placeholder=\" &#128269; Search\" onkeyup=\"searchOrders(this, '$ordertype', '$assi' )\" class=\"form-control\">
				$strAdNw
				$btnAdOrdr
				</div>
				<div class=\"cont\">
				$str
				</div>
			</div>
		";	
	
	}
}

// order list
function orlist_getOrdersByName($nm, $id, $ordertype){
	$str="";
	$sql = "select name, id,snowmed, dosage, qty, sig from order_details where delete_status = '0' AND o_type ='".$ordertype."' AND name='".sqlEscStr($nm)."'  order by name"; //AND id!='".$id."'
	$rez = sqlStatement($sql);
	for($i=0; $row=sqlFetchArray($rez);$i++){
		$sno_med=$dosage=$qty=$sig="";
		if($row["snowmed"]){$sno_med="<span class=\"snw\"> (SNOMED CT: ".$row["snowmed"].")</span>";}
		if(!empty($row["dosage"])){  $dosage = " ".$row["dosage"]."";  }
		if(!empty($row["qty"])){  $qty = " ".$row["qty"]."";  }
		if(!empty($row["sig"])){  $sig = " ".$row["sig"]."";  }
		$nm2=str_replace(" ","_", $nm);		
		$str.= "<div class=\"checkbox\"><input type=\"checkbox\" id=\"elem_inorder_relorder".$nm2.$i."\" value=\"".$row["id"]."\">";
		$str.="<label for=\"elem_inorder_relorder".$nm2.$i."\">".$row["name"]." ".$sno_med."".$dosage."".$qty."".$sig."</label></div>";
	}
	return $str;
}

function get_order_type($type_id){
	$ret = "";
	if(!empty($this->ar_order_type[$type_id])){  $ret = $this->ar_order_type[$type_id]; }
	return $ret;
}

function get_order_type_id($type){
	$db_order_type_id = '';
	$o_type = strtoupper($type) ; //;
	switch($o_type){
		case "MEDS":
		case "MED":
		case "MEDICATION":
		$db_order_type_id = 1;
		break;
		case "LAB":
		case "LABS":
		$db_order_type_id = 2;
		break;
		case "IMAGING":
		case "IMAGING/RAD":
		case "RADIOLOGY/IMAGING":
		$db_order_type_id = 3;
		break;
		case "PROCEDURAL":
		case "PROCEDURE/SX":
		case "SURGERY":
		$db_order_type_id = 4;
		break;
		case "INFORMATION":
		case "INFORMATION/INSTRUCTIONS":
		case "INFO/INST":
		$db_order_type_id = 5;
		break;
		default:
		$db_order_type_id = '';
		break;
	}
	return $db_order_type_id;
}

function getOrderDetail_Popup($pid, $fid){
	$flgInsideAdmin = (!empty($_REQUEST['flgInsideAdmin'])) ? 1 : 0 ;//
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "med" ){
	
		$arr_row_order=array();
		if(isset($_REQUEST['id']) && $_REQUEST['id']!="" && $_REQUEST['id']!=0){
			$sql_order = "SELECT * FROM order_details WHERE id IN (".$_REQUEST['id'].")";	
			$result_order = sqlStatement($sql_order);
			$c=0;
			while($row_order = sqlFetchArray($result_order)){
			$db_order_type_id = $row_order['order_type_id'];
			if( $db_order_type_id == ""){
				$db_order_type_id = $this->get_order_type_id($row_order['o_type']);
			}
			$pfs = ($c==0) ? "" : $c ;
			
			$tmp = array();
			$tmp = array_merge($tmp, $row_order);
			$tmp["db_order_type_id"] = $db_order_type_id;
			$tmp["pfs"] = $pfs;
			$arr_row_order[$c] = $tmp;
			
			$c++;
			
			}	
		}
		
		include($GLOBALS['incdir']."/chart_notes/popup_medorders.php");
	
	}else{
		
		$OBJsmart_tags = new SmartTags;
		$order_type = $this->ar_order_type;
		
		if($_REQUEST['mode'] == "get_template"){
			if($_REQUEST['order_template_id']>0){
				$sql = "SELECT * FROM order_template WHERE template_id = '".$order_template_id."'";
				if(!$order_template_id){
				    $sql = "SELECT * FROM order_template WHERE template_id = '".$_REQUEST['order_template_id']."'";
				}
				$res = sqlStatement($sql);
				$row = sqlFetchArray($res);
				$template_content = stripcslashes(html_entity_decode($row['template_content']));
				$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
				if($arr_smartTags){
					foreach($arr_smartTags as $key=>$val){
						$template_content = str_ireplace("[".$val."]",'<a id="'.$key.'" href="javascript:;" class="cls_smart_tags_link">'.$val.'</a>',$template_content);	
					}	
				}
				echo $template_content;
			}else echo "";
			die();
		}
		
		$db_order_type_id = 1;
		//t1
		if(isset($_REQUEST['c_otype']) && $_REQUEST['c_otype']!="" && $_REQUEST['c_otype']!="0"  ){ 
			$_REQUEST['c_otype'] = trim($_REQUEST['c_otype']);
			$db_order_type_id = $this->get_order_type_id($_REQUEST['c_otype']);
		}
		//
		
		//
		if(isset($_REQUEST['id']) && $_REQUEST['id']!="" && $_REQUEST['id']!=0){
			$sql_order = "SELECT * FROM order_details WHERE id = '".$_REQUEST['id']."'";
			$row_order = sqlQuery($sql_order);
			$db_order_type_id = $row_order['order_type_id'];
			if( $db_order_type_id == ""){
				$o_type = $row_order['o_type'];
				$db_order_type_id = $this->get_order_type_id($o_type);
			}
			
			$db_order_template_id = $row_order['template_id'];
			$db_template_content = stripcslashes(html_entity_decode($row_order['template_content']));
			$order_content_details = $db_template_content;
			$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
			if($arr_smartTags){
				foreach($arr_smartTags as $key=>$val){
					$db_template_content = str_ireplace("[".$val."]",'<a id="'.$key.'" href="javascript:;" class="cls_smart_tags_link">'.$val.'</a>',$db_template_content);	
				}	
			}
		}
		
		//
		if(isset($_REQUEST['chart_order_id']) && $_REQUEST['chart_order_id']!="" && $_REQUEST['chart_order_id']!=0){
			$chart_order_id = $_REQUEST['chart_order_id'];
			$sql_sel = "SELECT order_set_associate_id FROM order_set_associate_chart_notes_details WHERE order_set_associate_details_id = '".$chart_order_id."'";
			$row_sel = sqlQuery($sql_sel);
			$order_set_associate_id = $row_sel['order_set_associate_id'];
			$sql_order = "SELECT 
										c3.orders_dx_code,c3.orders_dx_icd10_code,  c3.order_lab_name, 
										c3.o_type, c3.id, c3.name, 
										c3.order_set_option,
										c3.instruction, c3.resp_person,c3.med_id, c3.fdb_id, c3.orders_dx_icd10_code,
										
										c2.dosage, c2.qty, c2.sig, c2.refill, c2.ndccode, c2.testname, 
										c2.loinc_code,  c2.cpt_code, c2.inform, 
										c2.orders_options, c2.orders_dx_code,  c2.order_lab_name, c2.resp_person,
										c2.orders_site_text, c2.instruction_information_txt,
										c2.snowmed,c2.template_id,c2.template_content,c2.order_id
										
										FROM  order_set_associate_chart_notes c1 ".
										"LEFT JOIN order_set_associate_chart_notes_details c2 ON c1.order_set_associate_id = c2.order_set_associate_id ".
										"LEFT JOIN order_details c3 ON c3.id = c2.order_id ".
										"WHERE c1.order_set_associate_id = '".$order_set_associate_id."' 
											AND c2.delete_status='0' ".
											"AND c2.order_set_associate_details_id = '".$_REQUEST['chart_order_id']."'".
										"ORDER BY c1.order_set_associate_id ".
										
										"";	
			$row_order = sqlQuery($sql_order);
			$row_order['instruction'] = $row_order['instruction_information_txt'] ;
			$elem_order_edit_id = $chart_ordr_id;
			$db_order_template_id = $row_order['template_id'];
			$db_template_content = stripcslashes(html_entity_decode($row_order['template_content']));
			$db_order_type = $row_order['o_type'];
			$db_order_type_id = $this->get_order_type_id($db_order_type);
			if($db_order_type_id == 1){ $fdb_id = $row_order['fdb_id']; }
			$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
			if($arr_smartTags){
				foreach($arr_smartTags as $key=>$val){
					$db_template_content = str_ireplace("[".$val."]",'<a id="'.$key.'" href="javascript:;" class="cls_smart_tags_link">'.$val.'</a>',$db_template_content);	
				}	
			}
			$_REQUEST['id'] = $row_order['order_id'];
		}
		
		//--- BEGIN GET LAB / RADIOLOGY DETAILS ----
			$qry_lab_rad = "select lab_radiology_name, lab_radiology_tbl_id
					from lab_radiology_tbl where lab_radiology_status = '0'";
			$res_lad_rad = sqlStatement($qry_lab_rad);
			
			$labOptionData = "";
			$order_lab_name_arr = preg_split('/,/',$order_lab_name);
			while($row_lad_rad = sqlFetchArray($res_lad_rad)){
				$lab_radiology_name = $row_lad_rad['lab_radiology_name'];
				$tbl_id = $row_lad_rad['lab_radiology_tbl_id'];
				$sel = '';
					if(in_array($tbl_id,$order_lab_name_arr) === true){
						$sel = 'selected="selected"';
				}
				$labOptionData .= "<option value='$tbl_id' $sel>$lab_radiology_name</option>";
			}
			$labSelectData = '
				<select name="ele_lad_rad_type[]" id="ele_lad_rad_type" multiple  >
					<option value="">None</option>
					'.$labOptionData.'
				</select>
			';
			
			if($row_order['order_lab_name'] !=""){	
				$db_qry_lab_rad =$qry_lab_rad. " and lab_radiology_tbl_id in (".$row_order['order_lab_name'].")";
				$db_res_lad_rad = sqlStatement($db_qry_lab_rad);
				$db_labOptionData = "";
				while($db_row_lad_rad = sqlFetchArray($db_res_lad_rad)){
					$db_lab_radiology_name = $db_row_lad_rad['lab_radiology_name'];
					$db_tbl_id = $db_row_lad_rad['lab_radiology_tbl_id'];
					$db_labOptionData .= "<option value='$db_tbl_id' selected>$db_lab_radiology_name</option>";
				}
			}
		//--- END GET LAB / RADIOLOGY DETAILS ----
		
		//
		$ouser = new User(); 
		$userOption = $ouser->users_getDropDown($sel_per);
		
		if($row_order['resp_person'] != ""){
			$qry = "SELECT * FROM users WHERE id in(".$row_order['resp_person'].")";
			$res = sqlStatement($qry);
			$db_resp_per_options = '';
			while($row = sqlFetchArray($res)){
				$tmp = $ouser->getUNameFormatted(4,$row);
				$name = $tmp;
				$db_resp_per_options .= "<option value='".$row['id']."' selected>".$name."</option>";
			}	
		}
		//
		
		//--- BEGIN GET ALL DX CODE -------
		$qry = "select orders_dx_icd10_code from order_sets where delete_status = '0'
		and orders_dx_icd10_code != ''";
		if(empty($orders_dx_code) == false and empty($order_id) == false){
			$qry .= " and orders_dx_icd10_code not in($orders_dx_code)";
		}
		$res = sqlStatement($qry);
		$existsDxCodeArr = array();
		while($row = sqlFetchArray($res)){
			if($row['orders_dx_icd10_code'] != "")
			$existsDxCodeArr[] = trim($row['orders_dx_icd10_code'],",");
		}
		$existsDxCodeStr = join(',',$existsDxCodeArr);
		
		//$qry_dx_code = "select diagnosis_id,d_prac_code,diag_description from diagnosis_code_tbl";
		$qry_dx_code = "SELECT id AS diagnosis_id, icd10 AS d_prac_code, icd10_desc AS diag_description FROM icd10_data";
		if(empty($existsDxCodeArr) == false){
			//$qry_dx_code .= " where diagnosis_id not in ($existsDxCodeStr)";
			$qry_dx_code .= " where id not in ($existsDxCodeStr)";
		}
		$qry_dx_code .= " order by diag_description,d_prac_code";
		$res_dx_code = sqlStatement($qry_dx_code);
		
		$dxOptions = '';
		while($row_dx_code = sqlFetchArray($res_dx_code)){
			$diagnosis_id = $row_dx_code['diagnosis_id'];
			$d_prac_code = $row_dx_code['d_prac_code'];
			$d_prac_code .= '  '.$row_dx_code['diag_description'];
			$sel = '';
			$ln_dca = is_array($orders_dx_code_arr) ? count($orders_dx_code_arr) : 0 ;
			for($l=0;$l<$ln_dca;$l++){
				if(trim($orders_dx_code_arr[$l] == $diagnosis_id)){
					$sel = 'selected';
				}
			}
			$dxOptions .= <<<DATA
				<option value="$diagnosis_id" $sel>$d_prac_code</option>
DATA;
		}
		
		if($row_order['orders_dx_icd10_code'] !=""){	
			//$db_qry_dx_code = "select diagnosis_id,d_prac_code,diag_description from diagnosis_code_tbl";
			//$db_qry_dx_code .= " where diagnosis_id in (".$row_order['orders_dx_icd10_code'].")";
			$db_qry_dx_code = "SELECT id AS diagnosis_id, icd10 AS d_prac_code, icd10_desc AS diag_description FROM icd10_data";
			$db_qry_dx_code .= " where id in (".$row_order['orders_dx_icd10_code'].")";

			
			$db_res_dx_code= sqlStatement($db_qry_dx_code);
			$db_dxOptions = '';
			while($db_row_dx_code = sqlFetchArray($db_res_dx_code)){
				$diagnosis_id = $db_row_dx_code['diagnosis_id'];
				$d_prac_code = $db_row_dx_code['d_prac_code'];
				$d_prac_code .= '  '.$db_row_dx_code['diag_description'];
				$db_dxOptions .= <<<DATA
					<option value="$diagnosis_id" selected>$d_prac_code</option>
DATA;
			}
		}
//--- END GET ALL DX CODE -------

//----- BEGIN ORDER TEMPLATE DROP DOWN ----------
	$order_type_id = (($_REQUEST['order_type_id']>0)?$_REQUEST['order_type_id'] : $db_order_type_id);
	$sql = "SELECT * FROM order_template WHERE order_type_id = '".$order_type_id."' AND status = '0' ";
	$res = sqlStatement($sql);
	$str_tmp_select = '<select id="order_template" name="order_template" onChange="get_template(this);" class="form-control">';
	$str_tmp_select .= '<option value=""> - </option>';
	while($row = sqlFetchArray($res)){
		$sel = ($row['template_id']==$db_order_template_id)? "selected":'';
		$str_tmp_select .= "<option value='".$row['template_id']."' ".$sel.">".$row['template_name']."</option>";
	}
	$str_tmp_select .= '</select>';
	
	if($_REQUEST['mode'] == "get_order_template_option" && $_REQUEST['order_type_id']>0){
		echo $str_tmp_select;die();
	}
//----- END ORDER TEMPLATE DROP DOWN ------------

//--- BEGIN ORDER TYPE DROP DOWN ----
	$type_option_val = '';
	if(count($order_type)>0){
		foreach($order_type as $order_type_id_tmp=>$order_type_name){
			$sel = ($order_type_id_tmp==$order_type_id) ? 'selected' : '';			
			$type_option_val .= '<option '.$sel.' value="'.$order_type_id_tmp.'">'.$order_type_name.'</option>';
		}
	}
//--- END ORDER TYPE DROP DOWN ----
$doc_width = ($db_template_content!="" && $_REQUEST['callFrom'] == "WV")?"900":"900";	
		
		
		if($db_template_content != "" && $_REQUEST['callFrom'] == "WV"){
			if(!empty($pid) && !empty($fid)){			
			$objParser = new PnTempParser;
			//pre($_SESSION);
			//echo $objParser->getSle($db_template_content,$_SESSION['patient'],$_SESSION['form']);
			$id = $_REQUEST['id'];
			unset($_REQUEST['id']);
			unset($_GET['id']);
			//$table = 'consultTemplate';
			
			$db_template_content = $objParser->getDataParsed($db_template_content,$pid, $fid);
			$_REQUEST['id'] = $id;
			}
		}
		
		//
		$var = (isset($row_order['name']))?$row_order['name']:'';
		$htm_addmoreMeds4Edit="";
		if((isset($flgInsideAdmin) && $flgInsideAdmin==1) ){
			$htm_addmoreMeds4Edit=$this->addmoreMeds4Edit($_REQUEST['id'], $var);
		}
		
		
		include($GLOBALS['incdir']."/chart_notes/popup_orders.php");
	}
	
}

function get_order_options($order_id="", $order_set_id="", $order_set_name=""){
	$rs = array();
	$order_type = $this->ar_order_type;
	
	foreach($order_type as $key=>$order_type_name){
		$order_type_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $order_type_name);
		$rs['ele_order_'.$order_type_name."[]"] = array();
		//-----BEGIN SET ALL OPTIONS FOR ORDERS----------
		$qry = "select id,name from order_details where delete_status ='0' and (order_type_id = '".$key."' or o_type = '".$order_type_name."') ";
		if($order_id!="" && trim($order_id)!=","){
			$qry .= " and id NOT IN (".$order_id.")";
		}
		$qry .= " order by name asc";
		$rs['all_'.$order_type_name."[]"] = array();
		$res= imw_query($qry);
		while($row = imw_fetch_assoc($res)){
			$id = $row['id'];
			$name = trim(ucwords($row['name']));
			$rs['all_'.$order_type_name."[]"][] = array("id"=>$id,"name"=>$name);
		}
		//-----END SET ALL OPTIONS FOR ORDERS----------
	}

	//make array ok
	$str_order_id="";
	if(!empty($order_id)){ 
		$tmp_ar = explode(",", $order_id);
		$tmp_ar = array_filter( $tmp_ar );
		$str_order_id= implode(",", $tmp_ar);					
	}
	
	$sql = "SELECT id,order_type_id,o_type,name FROM order_details WHERE id IN (".$str_order_id.") AND delete_status = 0 order by name asc";
	$res = imw_query($sql);
	$arr = array();
	while($row = imw_fetch_assoc($res)){
		if($row['order_type_id'] != "")
		$order_type_name = $order_type[$row['order_type_id']];
		else
		$order_type_name = $row['o_type'];
		$order_type_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $order_type_name);
		//$arr[$order_type_name][] = $row['id'];
		$rs['ele_order_'.$order_type_name."[]"][] = array("id"=>$row['id'],"name"=>$row['name']);
	}
	
	$rs['ele_order_orderset[]'] = array();
	if($order_set_id!="" && $order_set_id!="NULL"){	//$rs['order_set_ids']
		$arr_order_set_ids = explode(",", $order_set_id);
		$arr_order_set_name = explode(",", $order_set_name); //$rs['order_set_name']
		foreach($arr_order_set_ids as $key=>$val){
			$rs['ele_order_orderset[]'][] = array("id"=>$val,"name"=>$arr_order_set_name[$key]);
		}
	}
	
	//-----BEGIN SET ALL OPTIONS FOR ORDER SETS----------
	$rs['all_orderset[]'] = array();
	$order_set_arr = $this->order_set();
	foreach($order_set_arr as $order_set_row){
		if(!in_array($order_set_row['id'],$arr_order_set_ids))
		$rs['all_orderset[]'][] = array("id"=>$order_set_row['id'],"name"=>$order_set_row['orderset_name']);
	}
	//-----END SET ALL OPTIONS FOR ORDER SETS----------

	return $rs;	
}

function get_order_types_arr(){ return $this->ar_order_type; }

function get_order_types_htm(){ 	
	$htm = "";
	$order_type = $this->ar_order_type;
	$str_orders_js= " var order_type = ".json_encode($order_type).";";
	$order_type[] = "Order Sets"; //added Order set
	foreach($order_type as $index=>$order_type_name){
		$order_type_name1 = $order_type_name;
		$order_type_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $order_type_name);
		
		$htm .= "<td>";
		
		//Meds --
		if($index=="1" || $order_type_name=="Meds"){
			$qry = "select id,name, snowmed, count(*) as num from order_details 
				where delete_status ='0' and (order_type_id = '".$index."' or o_type = '".$order_type_name."') ";
			$qry .= " GROUP by name ";
			$qry .= " order by name";
			$res= imw_query($qry);
			$all_order_option = $all_order_option_morediv = '';
			$i=1;
			while($row = imw_fetch_assoc($res)){
				$id = $row['id'];
				$name = trim(ucwords($row['name']));
				//$sel = 'checked';
				
				if(empty($name)){continue;}				
				
				//--
				if($row["num"]>1){
					$tmp = $this->orlist_getOrdersByName($row["name"], $row["id"], $order_type_name);
					if(!empty($tmp)){
						$pntr= "<span class=\"pointer  ui-icon ui-icon-circle-arrow-e\" onmouseover=\"showrelorders('".$row["id"]."',1, this)\" onmouseout=\"showrelorders('".$row["id"]."',0)\" ></span>";							
						$all_order_option_morediv.="<div id=\"dv_relorders".$row["id"]."\" class=\"relorders\" onmouseover=\"showrelorders('".$row["id"]."',3)\" onmouseout=\"showrelorders('".$row["id"]."',0)\" >".$tmp."</div>";
						$prnt_sufx="master";
						$prnt_js=" onclick=\"checkrelorders(this)\" ";
					}
				}else{
					$pntr="";
					$nmsufx="";
					$prnt_js="";
					$prnt_sufx="";
				}
				//--
				
				$all_order_option .= "<div class=\"checkbox\"><input type=\"checkbox\" id=\"elem_inorder".$i.$prnt_sufx."\" name=\"elem_ormed".$prnt_sufx."[]\" value=\"$id\"  ".$prnt_js."  $sel><label for=\"elem_inorder".$i.$prnt_sufx."\">$name</label>".$pntr."</div>";
				$i++;
			}
			$all_order_option = '<div id="dvordermedlist">'.$all_order_option.'</div>'.$all_order_option_morediv;
			//Meds --
		}else{
			
			$order_type_name_hdr="";
			if($order_type_name1 == "Order Sets"){
				$sql = "SELECT * FROM order_sets WHERE delete_status = 0";
				$res = imw_query($sql);
				$arr_order_set = array();
				while ($row = imw_fetch_assoc($res)) {
					$id = $row['id'];
					$orderset_name = trim(ucwords($row['orderset_name']));
					$arr_order_set[] = array("id"=>$id,"name"=>$orderset_name);
				}
				
				$str_orders_js .= " var all_orderset_options = ".json_encode($arr_order_set)."; var all_orderset_options_admin = all_orderset_options; ";							
				
				$order_type_name="orderset";
				$order_type_name_hdr="".$order_type_name1."";
			}else{
				//
				$qry = "select id,name from order_details where delete_status ='0' and (order_type_id = '" . $index . "' or o_type = '" . $order_type_name . "')";
				$qry .= " order by name";
				$res = imw_query($qry);
				$arr = array();
				while ($row = imw_fetch_assoc($res)) {
					$id = $row['id'];
					$name = trim(ucwords($row['name']));
					//$arr[$id] = $name;
					$arr[] = array("name"=>$name, "id"=>$id);
				}
				//if (count($arr) > 0) {$order_type_name = preg_replace("/[^a-zA-Z]/", '', $order_type_name);}
				$str_orders_js .= " var all_orders_option_".$order_type_name." = ".json_encode($arr)."; ";
				//
				$order_type_name_hdr="".$order_type_name1." Orders ";
			}	
			
			
			$all_order_option ='<select  class="form-control border" id="all_'.$order_type_name.'" name="all_'.$order_type_name.'[]"  size="15" multiple="multiple"></select>';
		}
		
		$htm .='<div class="div_shadow"  id="pop_up_'.$order_type_name.'" >
				<table class="tblBg">
					<tr><td class="purple_bar" colspan="3">Please Add/Remove '.$order_type_name_hdr.' using Arrow Buttons.</td></tr>
					<tr>
						<td>List of '.$order_type_name_hdr.'</td>
						<td></td>
						<td>List of Selected '.$order_type_name_hdr.'</td>
					</tr>
					<tr>
						<td>
							'.$all_order_option.'
						</td>
						<td>
							<input class=" btn btn-default" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl(\'pop_up_'.$order_type_name.'\',\'all_'.$order_type_name.'\',\'selected_'.$order_type_name.'\',\'all\');">
							<input class=" btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl(\'pop_up_'.$order_type_name.'\',\'all_'.$order_type_name.'\',\'selected_'.$order_type_name.'\',\'single\');">
							<input class=" btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl(\'pop_up_'.$order_type_name.'\',\'selected_'.$order_type_name.'\',\'all_'.$order_type_name.'\',\'single_remove\');">
							<input class=" btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl(\'pop_up_'.$order_type_name.'\',\'selected_'.$order_type_name.'\',\'all_'.$order_type_name.'\',\'all_remove\');">
						</td>
						<td>
							<select class="form-control border"  id="selected_'.$order_type_name.'" name=""  size="15" multiple="multiple"></select>
						</td>
					</tr>
					<tr>
						<td colspan="3" class="text-center">
							<input class="btn btn-success " type="button"  value="Done" onClick="selected_ele_close(\'pop_up_'.$order_type_name.'\',\'selected_'.$order_type_name.'\',\'ele_order_'.$order_type_name.'\',\'div_order_'.$order_type_name.'\',\'done\')">
							<input class="btn btn-success " type="button"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close(\'pop_up_'.$order_type_name.'\',\'selected_'.$order_type_name.'\',\'ele_order_'.$order_type_name.'\',\'div_order_'.$order_type_name.'\',\'close\')">
						</td>
					</tr>
				</table>
				</div>';
		
		$htm .='
			<div  class="ele form-group" id="div_order_'.$order_type_name.'" onClick="return popup_dbl(\'pop_up_'.$order_type_name.'\',\'all_'.$order_type_name.'\',\'selected_'.$order_type_name.'\',\'\',\'ele_order_'.$order_type_name.'\')">
			<label for="ele_order_'.$order_type_name.'" class="text_purple"><strong>'.$order_type_name1.'</strong></label>
			<select  name="ele_order_'.$order_type_name.'[]" id="ele_order_'.$order_type_name.'" multiple="multiple" size="3" class="form-control"  >        				
			</select>
			</div>
			</td>
		';		
	}
	
	
	
	return array($htm, $str_orders_js);
}

function order_set(){
	$qry = "select id,orderset_name from order_sets where delete_status = '0'";
	$res = imw_query($qry);
	$order_set_arr=array();
	if(imw_num_rows($res)>0){
		while($rs_order_set=imw_fetch_assoc($res)){
			$order_set_arr[]=$rs_order_set;
		}	
	}
	return $order_set_arr;
}

function addmoreMeds4Edit($id, $ordername){
	
	if(empty($ordername)||empty($id)){return "";}
	
	$str="";	
	$sql_order = "SELECT dosage, qty, sig, refill, ndccode, med_id, id,fdb_id FROM order_details 
				WHERE  name='".sqlEscStr($ordername)."' 
				AND (order_type_id='1' OR o_type='meds' OR o_type='med')  
				AND  id != '".$id."' AND delete_status = '0'
				ORDER by id ";	
	$rez = sqlStatement($sql_order);
	for($i=1;$row=sqlFetchArray($rez);$i++){
	
	$dosage = (isset($row['dosage']))?$row['dosage']:'';
	$qty = (isset($row['qty']))?$row['qty']:'';
	$sig = (isset($row['sig']))?$row['sig']:'';
	$refill = (isset($row['refill']))?$row['refill']:'';
	$ndccode = (isset($row['ndccode']))?$row['ndccode']:'';
	$med_id = $row['med_id']; 
	$fdb_id = $row['fdb_id']; 
	$id = $row['id']; 
	
	$str.="<br/>".
	 "<div class=\"ele\" id=\"div_dosage".$i."\">".
            "<input type=\"text\" name=\"ele_dosage".$i."\" id=\"ele_dosage".$i."\" value=\"".$dosage."\" class=\"form-control\" >".
            "<span class=\"label\">Dosage</span> ".
	     "<input type=\"hidden\" name=\"med_id".$i."\" id=\"med_id".$i."\" value=\"".$med_id."\" > ".
	     "<input type=\"hidden\" name=\"id".$i."\" id=\"id".$i."\" value=\"".$id."\" />".
        "</div>".
        "<div class=\"ele\" id=\"div_qty".$i."\">".
        "     <input type=\"text\" name=\"ele_quantity".$i."\" id=\"ele_quantity".$i."\" value=\"".$qty."\" class=\"form-control\" >".
        "     <span class=\"label\">Quantity</span> ".
        "</div>".	
        "<div class=\"ele\" id=\"div_sig".$i."\">";
        
	//if(isset($flgInsideAdmin) && $flgInsideAdmin==1){
		$str.="<textarea name=\"ele_sig".$i."\" id=\"ele_sig".$i."\" class=\"form-control\" >".$sig."</textarea>";
	//}else{
	//	$str.="   <input type=\"text\" name=\"ele_sig".$i."\" id=\"ele_sig".$i."\" value=\"".$sig."\" style=\"width:100px;\" >";
	//}
	
        $str.="     <span class=\"label\">Sig</span> ".
        "</div>  ".	
        "<div class=\"ele\" id=\"div_refill".$i."\">".
        "     <input type=\"text\" name=\"ele_refill".$i."\" id=\"ele_refill".$i."\" value=\"".$refill."\" class=\"form-control\" >".
        "     <span class=\"label\">Refill</span> ".
        "</div> ".
        "<div class=\"ele\" id=\"div_ndc".$i."\">".
        "    <input type=\"text\" name=\"ele_ndc_code".$i."\" id=\"ele_ndc_code".$i."\" value=\"".$ndccode."\" class=\"form-control\" >".
        "    <span class=\"label\">NDC Code</span> ".
        "</div> ".
		"<div class=\"ele\" id=\"div_fdb".$i."\">".
        "    <input type=\"text\" name=\"ele_fdb_code".$i."\" id=\"ele_fdb_code".$i."\" value=\"".$fdb_id."\" class=\"form-control\" readonly onClick=\"check_fdb(this);\" >".
        "    <span class=\"label\">FDB ID</span> ".
        "</div>";
	
	}
	//
	return $str;	
}

function del_admin_order($id){
	if(!empty($id)){
		$userauthorized = $_SESSION['authId'];		
		$q = "SELECT * FROM order_details WHERE id = '".$id."'";
		$r = imw_query($q);
		$row = imw_fetch_assoc($r);
		if($row['o_type'] == "Meds" || $row['o_type'] == "Med" || $row['order_type_id'] == 1){
			if($row['med_id'] > 0){
				imw_query("UPDATE medicine_data SET del_status = 1 WHERE id = '".$row['med_id']."'");
			}
		}
		$qry=imw_query("update order_details set delete_status = '1',modified_by='".$userauthorized."',modified_on='".wv_dt('now')."' where id='".$id."'");
	}
}

function save_admin_order(){
	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";exit;
	
	//--Delete Order ----
	if(isset($_POST["el_del_id"]) && !empty($_POST["el_del_id"])){		
		$this->del_admin_order($_POST["el_del_id"]);		
	}
	
	//--- SAVE ORDERS ---
	$save_frm = $_POST["save_frm"];
	if(trim($save_frm) != ''){
		$order_type = $this->ar_order_type;
		
		$dataArr = array();
		//print $instruction = preg_replace('/\\n/',"\\r\\n",$instruction);
		$dataArr['o_type'] = $order_type[$_POST["ele_order_type"]];
		$dataArr['order_type_id'] = $_POST["ele_order_type"];
		
		//
		if($dataArr['order_type_id'] == 1 || strtolower($dataArr['o_type']) == "meds" || strtolower($dataArr['o_type']) == "med"){
			$flgdo=true;
			
		}else{
			$flgdo=false;
		}
		
		$c=0;
		do{
			$prs="";
			if($flgdo==true && $c>0){
				$prs=$c;
				
				//no set
				if(!isset($_POST["ele_dosage".$prs])){
					$flgdo=false;
					break;
				}else{
					//all empty
					if(empty($_POST["ele_dosage".$prs]) &&  empty($_POST["ele_quantity".$prs]) && empty($_POST["ele_sig".$prs]) && 
						empty($_POST["ele_refill".$prs]) && empty($_POST["ele_ndc_code".$prs])){
						$flgdo=false;
						break;
					}			
				}
			}
			
			$dataArr['name'] = $_POST["ele_order_name"];		
			$dataArr['template_id'] = $_POST["order_template"];
			$dataArr['testname'] = $_POST["ele_test_name"];
			$dataArr['inform'] = $_POST["ele_information"] ;
			$dataArr['instruction'] = $_POST["ele_instruction"];
			$dataArr['order_lab_name'] = @join(',',$_POST["ele_lad_rad_type"]);
			$dataArr['resp_person'] = @join(',',$_POST["ele_responsible_person"]);
			$dataArr['cpt_code'] = $_POST["ele_cpt_code"];
			$dataArr['loinc_code'] = $_POST["ele_loinc"];
			$dataArr['snowmed'] = $_POST["ele_snowmed"];
			//$dataArr['resp_group'] = @join(',',$responsible_group);
			//$dataArr['orders_dx_code'] = @join(',',$ele_dx_code);	
			$dataArr['orders_dx_icd10_code'] = @join(',',$_POST["ele_dx_code"]);
			$dataArr['delete_status'] = 0;
			//$dataArr['o_type'] = (!empty($_POST["elem_order_type"])) ? $_POST["elem_order_type"] : @join(',',$_POST['o_type']);
			
			//$dataArr['ref_code'] = $ref_code;
			//$dataArr['order_set_option'] = $elem_order_set_option;
			//$dataArr['type_other_value'] = $other_val;
			//$dataArr['order_lab_name'] = @join(',',$txt_lab_name);
			
			//[elem_order_type] => Meds 
			//[orders_name] => dshksd 
			$id=$_POST["id".$prs];
			$dataArr['med_id'] = $_POST["med_id".$prs];
			$dataArr['dosage'] = $_POST["ele_dosage".$prs];
			$dataArr['qty'] = $_POST["ele_quantity".$prs];
			$dataArr['sig'] = $_POST["ele_sig".$prs] ;
			$dataArr['refill'] = $_POST["ele_refill".$prs] ;
			$dataArr['ndccode'] = $_POST["ele_ndc_code".$prs] ;
			$dataArr['fdb_id'] = $_POST["ele_fdb_code".$prs] ;
			$dataArr['template_content'] = htmlentities($_POST["hdnCKeditor"]);
			
			//$dataArr['testname'] = $_POST["elem_testname"] ;				
			
			if(empty($id) == false){
				$dataArr["id"] = $id;
				$dataArr['modified_by'] = $_SESSION['authId'];
				$dataArr['modified_on'] = date('Y-m-d H:i:s');
				if($dataArr['order_type_id'] == 1 || strtolower($dataArr['o_type']) == "meds" || strtolower($dataArr['o_type']) == "med"){
					$q_md = "SELECT * FROM medicine_data 
								WHERE LOWER(medicine_name) = '". strtolower($dataArr['name'])."'
								AND del_status = '0'
							";
					$res_md = imw_query($q_md);//echo mysql_error();
					if(imw_num_rows($res_md) <= 0){
						$q_md_ins = "INSERT INTO medicine_data
									SET medicine_name = '".ucwords(strtolower($dataArr['name']))."',
										ocular = 1
									";
						imw_query($q_md_ins);			
						$dataArr['med_id'] = imw_insert_id();
					}
				}
				$insertId = $this->saveOrder($dataArr); //$objManageData->UpdateRecords($id,'id',$dataArr,'order_details');//echo mysql_error();				
				$msg = 'Orders set successfully updated.';
			}
			else{
				if($dataArr['order_type_id'] == 1 || strtolower($dataArr['o_type']) == "meds" || strtolower($dataArr['o_type']) == "med"){
					$q_md = "SELECT * medicine_data 
								WHERE LOWER(medicine_name) = '".strtolower($dataArr['name'])."'
								AND del_status = '0'
							";
					$res_md = imw_query($q_md);		
					if(imw_num_rows($res_md) <= 0){
						$q_md_ins = "INSERT INTO medicine_data
									SET medicine_name = '".ucwords(strtolower($dataArr['name']))."',
										ocular = 1
									";
						imw_query($q_md_ins);			
						$dataArr['med_id'] = imw_insert_id();
					}
				}
				$dataArr['created_by'] = $_SESSION['authId'];
				$dataArr['created_on'] = date('Y-m-d H:i:s');				
				$insertId = $this->saveOrder($dataArr); //$objManageData->AddRecords($dataArr,'order_details');echo mysql_error();
				if($insertId){
					$msg = 'Orders set successfully saved.';
				}
			}
			$c++;
			
		}while( $flgdo==true );		
	}
	//--	
	echo "0";
}
	
}