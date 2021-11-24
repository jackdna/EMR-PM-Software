<?php
//ChartOrders.php
class ChartOrders extends ChartNote{
	private $arr_order_type; 
	public function __construct($pid, $fid){		
		parent::__construct($pid,$fid);
		$this->arr_order_type = array("1"=>"Meds", "2"=>"Labs", "3"=>"Imaging/Rad", "4"=>"Procedure/Sx",  "5"=>"Information/Instructions");
	}	
	
	function cpoe_getPrevOrdersGiven(){
		$pid = $this->pid;
		$fid = $this->fid;
		$str="";
		$sql = "SELECT c3.name, c3.fdb_id FROM order_set_associate_chart_notes c1 
				LEFT JOIN order_set_associate_chart_notes_details c2 ON c2.order_set_associate_id = c1.order_set_associate_id
				LEFT JOIN order_details c3 ON c3.id = c2.order_id
				WHERE c1.patient_id='".$pid."' AND c1.form_id='".$fid."' AND c1.delete_status='0' AND c2.delete_status='0' 
				AND c3.o_type='Meds' AND c3.order_type_id='1' AND c3.fdb_id!=''
				Order by c3.name	
				"; //AND c3.delete_status='0'  : for orders added from add new in CPOE
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
			$old_med_id = $row["fdb_id"];
			$tmp_med_nm = $row["name"];
			$str .= "<drug id=\"".$old_med_id."\" name=\"".$tmp_med_nm."\" />";		
		}
		return $str;
	}
	
	function cpoe_getAllergyEmdn(){
		$str="";
		$med_list_qry=imw_query("select fdb_id,title from lists where pid='".$this->pid."' AND allergy_status = 'Active' AND type IN (3,7) AND fdb_id!='0' AND fdb_id!='' ");
		while($med_list_row=imw_fetch_array($med_list_qry)){
			if(!empty($med_list_row['fdb_id']) && !empty($med_list_row['title'])){
			$title = htmlspecialchars($med_list_row['title'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
			$str .= "<allergy id='".$med_list_row['fdb_id']."' name='".$title."' type='fdbATDrugName'/>";
			}
		}
		return $str;
	} 
	
	//move order/order set to ocu meds --
	function moveOrders_OrdersSets2OcuMeds($str_ma_det_id="",$dos=""){

	$pid = $this->pid;
	$formid = $this->fid;


	//
	if(empty($pid) || empty($formid)){ return; }

	$oMedHx = new MedHx($pid);
		
	$sql="SELECT 

	c2.orders_site_text,
	c2.dosage, c2.qty, c2.sig, c2.refill, c2.ndccode, DATE_FORMAT(c2.created_date,'%Y-%m-%d') as created_date,
	c3.name

	FROM order_set_associate_chart_notes c1
	LEFT JOIN order_set_associate_chart_notes_details c2 ON c2.order_set_associate_id=c1.order_set_associate_id
	LEFT JOIN order_details c3 ON c3.id = c2.order_id
	WHERE c1.patient_id='".$pid."' AND c1.form_id='".$formid."' 
	AND c1.delete_status='0' AND c2.delete_status='0' ";

	if(!empty($str_ma_det_id)){
		$sql.= " AND  c2.order_set_associate_details_id IN (".$str_ma_det_id.") ";
		$status="Administered";
	}else{
		$sql.= " AND (c3.o_type='Meds' OR c3.order_type_id='1');";
		$status="Active";
	}

	$rez=sqlStatement($sql);
	for($i=0;$row=sqlFetchArray($rez);$i++){
		
		$order_site=$row["orders_site_text"];	
		
		//Add Ocu Meds--
		
		$tmpArr=array();

		$order_site_en='';
		if(strpos($order_site,"OU")!==false){ $order_site_en=3; }
		if(strpos($order_site,"OD")!==false){ $order_site_en=2; }
		if(strpos($order_site,"OS")!==false){ $order_site_en=1; }
		if(strpos($order_site,"PO")!==false){ $order_site_en=4; }
		
		//it will added sysmic
		$tmpArr["type"] = (!empty($order_site_en) && $order_site_en>=1 && $order_site_en<=4) ? 4 : 1 ; 
		$tmpArr["title"] = $row["name"]; 
		$tmpArr["sig"] = $row["sig"]; 
		if(isset($GLOBALS['NO_ADD_MED_START_DATE']) && !empty($GLOBALS['NO_ADD_MED_START_DATE'])){$tmpArr["begdate"] = "";}else{$tmpArr["begdate"] = $row["created_date"];} // Linked to global settings. Should be off for Tufts
		if(!empty($str_ma_det_id)){
			$tmpArr["begdate"] = $dos;
		}
		
		$tmpArr["enddate"] = ""; 
		$tmpArr["pid"] = $pid; 				
		$tmpArr["destination"] = $row["dosage"];
		$tmpArr["allergy_status"] = $status; 
		$tmpArr["med_comments"] = "";
		$tmpArr["sites"] = $order_site_en;
		$tmpArr["compliant"] = "";		
		$tmpArr["qty"] = $row["qty"];
		$tmpArr["refills"] = $row["refill"];
		$tmpArr["ndccode"] = $row["ndccode"];
		
		$oMedHx->ocuMedsSave($tmpArr);
		
		//Add Ocu Meds--

	}

	}
	
	function attachOrder2Chart($key,$order_det_id,$order_set_id, $site, $sig){		
		//order Set
		if(!empty($order_set_id)){
			$sql_sel = "SELECT * FROM order_set_associate_chart_notes oscn
						WHERE oscn.patient_id = '".$this->pid."'
							AND oscn.form_id = '".$this->fid."'
							AND oscn.order_set_id = '".$order_set_id."'
							AND delete_status = 0
							AND plan_num = '".$key."'
						";
			$res_sel = imw_query($sql_sel);
			if(imw_num_rows($res_sel)<=0){			
				$sql_in = "INSERT INTO order_set_associate_chart_notes SET
							order_set_id='".$order_set_id."', 
							patient_id='".$this->pid."',
							form_id='".$this->fid."', 
							created_date='".sqlEscStr(wv_dt('now'))."', 
							logged_provider_id='".$_SESSION["authId"]."',
							plan_num='".$key."'
						";
				$order_set_associate_id = sqlInsert($sql_in);
			}else{
				$row_sel = imw_fetch_assoc($res_sel);
				$order_set_associate_id = $row_sel['order_set_associate_id'];
			}
			
			//--
			//order_detail
			$sql_osd_sel = "SELECT * FROM order_set_associate_chart_notes_details
						WHERE order_set_associate_id = '".$order_set_associate_id."'
						AND order_id = '".$order_det_id."'
						AND delete_status = 0
						";
			$res_osd_sel = imw_query($sql_osd_sel);
			if(imw_num_rows($res_osd_sel)<=0){
				$sq_od = "SELECT *
						FROM order_details
						WHERE id = '".$order_det_id."'
						AND o_type!='' 
						AND order_type_id != '' 
						AND order_type_id != 0
						";
				$rez_od = sqlStatement($sq_od);	
				for($j=1;$row_od=sqlFetchArray($rez_od);$j++){
					$resp_person = @join(',',$row_od['resp_person']);
					$orders_dx_code = @join(',',$row_od['orders_dx_code']);
					$order_lab_name = @join(',',$row_od['order_lab_name']);
					
					//will not enter multi sigs attached to order
					if(empty($sig)){if(strpos($row_od["sig"],"\n")===false){$sig = $row_od["sig"];}}
					
					$sql_d_in = "INSERT INTO order_set_associate_chart_notes_details SET
									order_set_associate_id='".$order_set_associate_id."',
									order_id='".$row_od['id']."',
									created_date='".sqlEscStr(wv_dt('now'))."',
									orders_status='0',
									modified_date = '".sqlEscStr(wv_dt())."',
									modified_operator = '".$_SESSION["authId"]."',
									instruction_information_txt='".$row_od["instruction"]."',
									dosage = '".$row_od["dosage"]."', 
									qty = '".$row_od["qty"]."', 
									sig  = '".$sig."', 
									refill  = '".$row_od["refill"]."', 
									ndccode = '".$row_od["ndccode"]."', 
									testname = '".$row_od["testname"]."', 
									loinc_code = '".$row_od["loinc_code"]."', 
									cpt_code = '".$row_od["cpt_code"]."', 
									inform = '".$row_od["inform"]."',
									resp_person = '".$resp_person."', 
									orders_dx_code = '".$orders_dx_code."', 
									order_lab_name = '".$order_lab_name."',
									snowmed = '".$row_od["snowmed"]."',
									template_id = '".$row_od["template_id"]."',
									template_content = '".$row_od['template_content']."',
									orders_site_text='".$site."'
							";
					$id_rr = sqlInsert($sql_d_in);
				}
			}	
			
			//--	
			
		}else{
		
		
		//Order_details
		$sql = "SELECT * FROM order_details WHERE id = '".$order_det_id."' ";
		$row = sqlQuery($sql);
		if($row != false){
		
			$sql_sel = "SELECT * FROM order_set_associate_chart_notes oscn
						JOIN order_set_associate_chart_notes_details oscnd ON oscnd.order_set_associate_id = oscn.order_set_associate_id
						WHERE oscn.patient_id = '".$this->pid."'
							AND oscn.form_id = '".$this->fid."'
							AND oscnd.order_id = '".$order_det_id."'
							AND plan_num = '".$key."'
							AND oscn.delete_status = 0
						";
			$res_sel = sqlStatement($sql_sel);
			if(imw_num_rows($res_sel)<=0){			
				//order_details
				$sql_in = "INSERT INTO order_set_associate_chart_notes SET
							patient_id='".$this->pid."',
							form_id='".$this->fid."', 
							created_date='".sqlEscStr(wv_dt('now'))."', 
							logged_provider_id='".$_SESSION["authId"]."',
							plan_num='".$key."'
						";
				$order_set_associate_id = sqlInsert($sql_in);	
				
				$resp_person = @join(',',$row['resp_person']);
				$orders_dx_code = @join(',',$row['orders_dx_code']);
				$order_lab_name = @join(',',$row['order_lab_name']);
				
				//will not enter multi sigs attached to order
				if(empty($sig)){if(strpos($row["sig"],"\n")===false){$sig = $row["sig"];}}
				
				$sql_d_in = "INSERT INTO order_set_associate_chart_notes_details SET
								order_set_associate_id='".$order_set_associate_id."',
								order_id='".$row['id']."',
								created_date='".sqlEscStr(wv_dt('now'))."',
								orders_status='0',
								modified_date = '".sqlEscStr(wv_dt())."',
								modified_operator = '".$_SESSION["authId"]."',
								instruction_information_txt='".$row["instruction"]."', 
								
								dosage = '".$row["dosage"]."', 
								qty = '".$row["qty"]."', 
								sig  = '".$sig."', 
								refill  = '".$row["refill"]."', 
								ndccode = '".$row["ndccode"]."', 
								testname = '".$row["testname"]."', 
								loinc_code = '".$row["loinc_code"]."', 
								cpt_code = '".$row["cpt_code"]."', 
								inform = '".$row["inform"]."',
								resp_person = '".$resp_person."', 
								orders_dx_code = '".$orders_dx_code."', 
								order_lab_name = '".$order_lab_name."',
								snowmed = '".$row["snowmed"]."',
								template_id = '".$row["template_id"]."',
								template_content = '".$row['template_content']."',
								orders_site_text='".$site."'
						";
				$id_rr = sqlInsert($sql_d_in);
				

			}
		
		}
		
		}//end else

	}//end function
	
	//--
	function cpoe_chkPln_insertOrder($key,$pln){		
		//	
		$sql = "SELECT c3.name, c3.o_type, c3.order_type_id, c3.order_set_option, c1.order_set_associate_id,c1.order_set_id,
						c2.orders_site_text, c2.instruction_information_txt, c2.orders_options,
						c2.dosage, c2.qty, c2.sig, c2.refill, c2.ndccode, c2.snowmed,c3.snowmed	as snowmed_ct,
						c2.order_set_associate_details_id, c2.order_id
					FROM  order_set_associate_chart_notes c1 
					LEFT JOIN order_set_associate_chart_notes_details c2 ON c1.order_set_associate_id = c2.order_set_associate_id 
					LEFT JOIN order_details c3 ON c3.id = c2.order_id 
					WHERE c1.patient_id='".$this->pid."' ".
					"AND c1.form_id != '".$this->fid."' ".
					"AND c1.delete_status='0' AND c2.delete_status='0' 
					ORDER BY c1.order_set_associate_id DESC
					";
		//echo "<br/>".$sql."<br/>";		
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			if($row["name"]!=""){
				
				//*
				$order_name = $row["name"];
				$order_site = $row["orders_site_text"];
				$instruction = $row["instruction_information_txt"];
				$order_set_option = trim($row["orders_options"]);
				//if(empty($order_set_option)){$order_set_option = $row["order_set_option"];}
				$order_type = $row["o_type"];
				$order_type_id = $row["order_type_id"];
				if(empty($order_type) && !empty($order_type_id)){	$order_type = $arrOrderTypes[$order_type_id];	}
				
				$snowmed = $row["snowmed"];
				$snowmed_admin = $row["snowmed_ct"];
				$order_id = $row["order_id"];
				$order_set_id = $row["order_set_id"];
				//*/				
				
				if($order_type == "Meds"){
					//CPOE - show full med order
					//order name (site) Dosage Qty Sig Refill NDC code
					$dosage = $qty = $sig = $refill = $ndccode = "";
					if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
					if(!empty($row["dosage"])){  $dosage = " ".$row["dosage"]."";  }
					if(!empty($row["qty"])){  $qty = " ".$row["qty"]."";  }
					if(!empty($row["sig"])){  $sig = " ".$row["sig"]."";  }
					if(!empty($row["refill"])){  $refill = " ".$row["refill"]." refills";  }
					if(!empty($row["ndccode"])){  $ndccode = " ".$row["ndccode"]."";  }
					
					$strFormatPlan = $order_name."".$order_site."".$dosage."".$qty."".$sig."".$refill."".$ndc_code;
					
					//$strFormatPlan_wo_site_sig = $order_name."".$dosage."".$qty."".$refill."".$ndc_code;
					
					//$arrRet[$order_type][]=array($strFormatPlan, $row["order_set_associate_details_id"],"ORDER","",$order_name, $flg_supli_order, $strFormatPlan_wo_site_sig);
					
				}else{	

					//order name (site)(Instruction) - Option Optionname
					if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
					if(!empty($instruction)){  $instruction = " (".$instruction.")";  }
					if($order_type == "Labs" || $order_type == "Imaging/Rad" || $order_type == "Procedure/Sx"){					
						if(!empty($snowmed)){ $snowmed = " (SNOMED CT: ".$snowmed.")";  }
					}else{  $snowmed = ""; }
					if(!empty($order_set_option)){ $optionname = " - Option ".$order_set_option;  }
					if($order_type == "Information/Instructions"){
							if(!empty($snowmed_admin)){ $snowmed = " (SNOMED CT: ".$snowmed_admin.")";  }	
					}
					$strFormatPlan = $order_name."".$order_site."".$instruction."".$optionname."".$snowmed;
				
					//$strFormatPlan_wo_site_sig = $order_name."".$instruction."".$optionname."".$snowmed;
				
					//$arrRet[$order_type][]=array($strFormatPlan, $row["order_set_associate_details_id"],"ORDER","",$order_name, $flg_supli_order, $strFormatPlan_wo_site_sig);
				}
				
				//Compare PLan with Previous orders summary
				
				//remove hidden spl characters
				$pln_l2=substr($pln, 0,-2);
				
				//remove Date from Plan if exits
				$pln_l3=wv_strReplace($pln_l2,"DATE");
				
				if(!empty($strFormatPlan) && (trim($strFormatPlan) == trim($pln) || trim($strFormatPlan) == trim($pln_l2) || trim($strFormatPlan) == trim($pln_l3))){
					
					$order_set_associate_details_id = $row["order_set_associate_details_id"];
					
					//check if order exists in current visit of same order id
					$sql = "SELECT count(*) AS num  FROM order_set_associate_chart_notes c1 
							LEFT JOIN order_set_associate_chart_notes_details c2 ON c1.order_set_associate_id = c2.order_set_associate_id
							WHERE patient_id = '".$this->pid."' AND form_id = '".$this->fid."' AND order_id='".$order_id."' 
							AND c1.delete_status='0' AND c2.delete_status='0' 
							";
					$row = sqlQuery($sql);
					if($row==false || $row["num"]<=0 ){						
					
						$sql = "INSERT INTO order_set_associate_chart_notes SET 
								order_set_id = '".$order_set_id."',
								patient_id = '".$this->pid."',
								form_id = '".$this->fid."',
								created_date = '".sqlEscStr(wv_dt('now'))."',
								logged_provider_id='".$_SESSION["authId"]."',
								plan_num='".$key."'
								";
						$new_order_set_associate_id = sqlInsert($sql);
						
						//--
						$sql = "INSERT INTO order_set_associate_chart_notes_details (
								order_set_associate_details_id, order_set_associate_id, order_id, created_date,
								orders_status, delete_status,orders_site_text,orders_when_text,orders_priority_text,
								orders_reason_text,orders_when_day_txt,orders_options,instruction_information_txt,
								dosage,qty,sig,refill,ndccode,testname,loinc_code,cpt_code,inform,resp_person,
								orders_dx_code,order_lab_name,snowmed,template_id,template_content
								) 
								SELECT 
								'', '".$new_order_set_associate_id."', order_id, '".sqlEscStr(wv_dt('now'))."',
								orders_status, 0,orders_site_text,orders_when_text,orders_priority_text,
								orders_reason_text,orders_when_day_txt,orders_options,instruction_information_txt,
								dosage,qty,sig,refill,ndccode,testname,loinc_code,cpt_code,inform,resp_person,
								orders_dx_code,order_lab_name,snowmed,template_id,template_content
								FROM order_set_associate_chart_notes_details where order_set_associate_details_id='".$order_set_associate_details_id."' ";
						$new_order_set_associate_det_id = sqlInsert($sql);
						//--
					}				
				}
			}
		}
	}
	//--
	
//	
	function getOrderSumm($elem_order_edit_id){
		$strFormatPlan="";
		$sql = "SELECT 	c1.orders_site_text, c1.instruction_information_txt, c1.orders_options,
						c1.dosage, c1.qty, c1.sig, c1.refill, c1.ndccode, c1.snowmed,c2.snowmed	as snowmed_ct,
						c1.order_set_associate_details_id,
					c2.name, c2.o_type
				FROM order_set_associate_chart_notes_details c1 ".
				"LEFT JOIN order_details c2 ON c2.id = c1.order_id ".	
				" WHERE c1.order_set_associate_id = '".$elem_order_edit_id."' 
				AND c1.delete_status='0'
				ORDER BY c1.order_set_associate_details_id DESC	
			";
		//exit($sql);		
		$row = sqlQuery($sql);	
		if($row != false){
			
			$order_name = $row["name"];
			$order_site = $row["orders_site_text"];
			$instruction = $row["instruction_information_txt"];
			$order_set_option = $row["orders_options"];
			//if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
			//if(!empty($instruction)){  $instruction = " (".$instruction.")";  }
			//if(!empty($order_set_option)){ $optionname = " - Option ".$order_set_option;  }		
			
			$order_type = $row["o_type"];
			$snowmed = $row["snowmed"];
			$snowmed_admin = $row["snowmed_ct"];	
			
			//$strFormatPlan = $order_name."".$order_site."".$instruction."".$optionname;		
			
			if($order_type == "Meds"){
				//CPOE - show full med order
				//order name (site) Dosage Qty Sig Refill NDC code
				$dosage = $qty = $sig = $refill = $ndccode = "";
				if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
				if(!empty($row["dosage"])){  $dosage = " ".$row["dosage"]."";  }
				if(!empty($row["qty"])){  $qty = " ".$row["qty"]."";  }
				if(!empty($row["sig"])){  $sig = " ".$row["sig"]."";  }
				if(!empty($row["refill"])){  $refill = " ".$row["refill"]." refills";  }
				if(!empty($row["ndccode"])){  $ndccode = " ".$row["ndccode"]."";  }
				
				$strFormatPlan = $order_name."".$order_site."".$dosage."".$qty."".$sig."".$refill."".$ndc_code;		
				
			}else{	

				//order name (site)(Instruction) - Option Optionname
				if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
				if(!empty($instruction)){  $instruction = " (".$instruction.")";  }
				if($order_type == "Labs" || $order_type == "Imaging/Rad" || $order_type == "Procedure/Sx"){					
					if(!empty($snowmed)){ $snowmed = " (SNOMED CT: ".$snowmed.")";  }
				}else{  $snowmed = ""; }
				if(!empty($order_set_option)){ $optionname = " - Option ".$order_set_option;  }
				if($order_type == "Information/Instructions"){
						if(!empty($snowmed_admin)){ $snowmed = " (SNOMED CT: ".$snowmed_admin.")";  }	
				}
				$strFormatPlan = $order_name."".$order_site."".$instruction."".$optionname."".$snowmed;	
				
			}
			
		}
		return $strFormatPlan;
	}
	
	function addNewOrder($arr, $pfs=""){
		$o_order = new Orders();
		$dataArr = array();
		$dataArr['o_type'] = $o_order->get_order_type($arr["ele_order_type".$pfs]);
		$dataArr['order_type_id'] = $arr["ele_order_type".$pfs];
		$dataArr['name'] = $arr["ele_order_name".$pfs];
		$dataArr['med_id'] = $arr["med_id".$pfs];
		$dataArr['template_id'] = $arr["order_template".$pfs];
		$dataArr['testname'] = $arr["ele_test_name".$pfs];
		$dataArr['inform'] = $arr["ele_information".$pfs] ;
		$dataArr['instruction'] = $arr["ele_instruction".$pfs];
		$dataArr['order_lab_name'] = @join(',',$arr["ele_lad_rad_type".$pfs]);
		$dataArr['resp_person'] = @join(',',$arr["ele_responsible_person".$pfs]);
		$dataArr['cpt_code'] = $arr["ele_cpt_code".$pfs];
		$dataArr['loinc_code'] = $arr["ele_loinc".$pfs];
		$dataArr['snowmed'] = $arr["ele_snowmed".$pfs];
		$dataArr['orders_dx_code'] = @join(',',$arr["ele_dx_code".$pfs]);	
		$dataArr['delete_status'] = 1;///set delete status on for newly added orders from work view so that that remain usefull for work view records but do not appear in admin->orders
		$dataArr['dosage'] = $arr["ele_dosage".$pfs];
		$dataArr['qty'] = $arr["ele_quantity".$pfs];
		$dataArr['sig'] = $arr["ele_sig".$pfs] ;
		$dataArr['refill'] = $arr["ele_refill".$pfs] ;
		$dataArr['ndccode'] = $arr["ele_ndc_code".$pfs] ;
		$dataArr['fdb_id'] = $arr["ele_fdb_code"] ;
		$dataArr['template_content'] = $arr["FCKeditor1".$pfs] ;
		$dataArr['created_by'] = $_SESSION['authId'];
		$dataArr['created_on'] = wv_dt('now');
		
		return $o_order->saveOrder($dataArr);
	}
	
	function saveOrdersDetails(){
		$elem_formId = $this->fid;
		$pid = $this->pid;
	
		//---
		$elem_delete = $_POST["elem_delete"];
		$elem_order_edit_id = $_POST["elem_order_edit_id"];
		if($elem_delete == 1 && !empty($elem_order_edit_id)){
			//Get Old plan_summ=
			$old_summ = $this->getOrderSumm($elem_order_edit_id);
			//Get Old plan_summ=	
			$sql = "UPDATE order_set_associate_chart_notes SET delete_status='1' WHERE order_set_associate_id = '".$elem_order_edit_id."' ";
			$row = sqlQuery($sql);	
			$arrRet=array("old_summ"=>$old_summ);
			echo json_encode($arrRet);
			exit();
		}
		$order_set_associate_details_id = $_POST["order_set_associate_details_id"];
		if($elem_delete == 1 && !empty($order_set_associate_details_id)){
			$sql_sel = "SELECT order_set_associate_id FROM order_set_associate_chart_notes_details WHERE order_set_associate_details_id = '".$order_set_associate_details_id."'";
			
			$row_sel = sqlQuery($sql_sel);
			//Get Old plan_summ=
			$old_summ = $this->getOrderSumm($row_sel['order_set_associate_id']);
			//
			$sql = "UPDATE order_set_associate_chart_notes_details SET delete_status='1' WHERE order_set_associate_details_id = '".$order_set_associate_details_id."' ";
			$row = sqlQuery($sql);
			
			$arrRet=array("old_summ"=>$old_summ);
			echo json_encode($arrRet);
			
			exit();
		}
		//---
		//test --
		
		$arrSqls=array();
		$arr_emdn_err=array();
		$arr_current_meds_notsaved = $arr_current_meds_notsaved_all =array();
		$xml=""; $xml_ordrs_given="";

		if(isset($_POST["save_meds_form"]) && $_POST["save_meds_form"]==1){//meds orders
		$flgdo=1;
		}else{
		$flgdo=0;
		}

		//
		$elem_donot_check_emdeon = (isset($_POST["elem_donot_check_emdeon"]) && !empty($_POST["elem_donot_check_emdeon"])) ? 1 : 0;

		$cc=0;

		//
		$order_type_arr=$this->arr_order_type;
		$oorder = new Orders();
		$order_id_arr = array();
		do{

		//check if 
		if($flgdo==1){
			
			$pfs= ($cc==0) ? "" : $cc;
			
			if(!isset($_POST["ele_order_name".$pfs])){//no element
				
				if($cc>$_POST["elem_lenOrders"]){
					$flgdo=0;
					break;	
				}
				//
				$cc++;
				continue;
			}

		}

		//if order name empty: continue;
		if(empty($_POST["ele_order_name".$pfs])){//no element
		$cc++;
		continue;
		}

		$order_id = $_POST["id".$pfs];
		if(empty($order_id)){
			$order_id = $this->addNewOrder($_POST, $pfs);
		}

		$dataArr = array();
		$dataArr['o_type'] = $order_type_arr[$_POST["ele_order_type".$pfs]];
		$dataArr['order_type_id'] = $_POST["ele_order_type".$pfs];
		$dataArr['med_id'] = $med_id;
		$dataArr['template_id'] = $_POST["order_template".$pfs];
		$dataArr['template_content'] = sqlEscStr(htmlentities($_POST["FCKeditor1".$pfs]));
		//meds
		$order_site=$_POST["elem_order_site".$pfs];
		$dosage=$_POST["ele_dosage".$pfs];
		$qty=$_POST["ele_quantity".$pfs];
		$sig=$_POST["ele_sig".$pfs];
		$refill=$_POST["ele_refill".$pfs];
		$ndccode=$_POST["ele_ndc_code".$pfs];
		//lads 
		$testname = $_POST["ele_test_name".$pfs]; 
		$instruction = $_POST["ele_instruction".$pfs];
		$loinc_code = $_POST["ele_loinc".$pfs];
		$orders_dx_code = @join(',',$ele_dx_code);
		//
		$arr_lab_name = $_POST["txt_lab_name".$pfs];
		$ele_lad_rad_type = $_POST["ele_lad_rad_type"];
		$order_lab_name = @join(',',$ele_lad_rad_type);
		$cpt_code = $_POST["ele_cpt_code".$pfs];
		$inform = $_POST["ele_information".$pfs];
		$ele_responsible_person = $_POST["ele_responsible_person"];
		$resp_person = @join(',',$ele_responsible_person);
		$order_name = $_POST["ele_order_name".$pfs];
		$order_type = $order_type_arr[$_POST["ele_order_type".$pfs]];
		$order_set_id=0; // 0 for orders only
		$elem_order_edit_id=$_POST["elem_order_edit_id".$pfs];
		$plan_num = $_POST["elem_plan_num"];
		$snowmed = $_POST["ele_snowmed".$pfs];

		
		//order_set_associate_chart_notes
		$sql_in = "INSERT INTO order_set_associate_chart_notes SET";
		$sql_up = "UPDATE order_set_associate_chart_notes SET ";
		$sql_fld = " order_set_id='".$order_set_id."', patient_id='".$pid."', form_id='".$elem_formId."', 
					created_date='".wv_dt('now')."', logged_provider_id='".$_SESSION["authId"]."',plan_num='".$plan_num."'
				";
		if(!empty($elem_order_edit_id)){
			$sql = $sql_up.$sql_fld." WHERE order_set_associate_id = '".$elem_order_edit_id."'  ";
			$rr = sqlQuery($sql);
			$order_set_associate_id = $elem_order_edit_id;
		}else{
			$sql = $sql_in.$sql_fld;
			$order_set_associate_id = sqlInsert($sql);	
		}
		//order_set_associate_chart_notes_details
		if(!empty($order_set_associate_details_id)){
			$sql_sel = "SELECT order_set_associate_id FROM order_set_associate_chart_notes_details WHERE order_set_associate_details_id = '".$order_set_associate_details_id."'";
			
			$row_sel = sqlQuery($sql_sel);
			$order_set_associate_id = $row_sel['order_set_associate_id'];
		}
		$sql_d_in = "INSERT INTO order_set_associate_chart_notes_details SET";
		$sql_d_up = "UPDATE order_set_associate_chart_notes_details SET ";
		$sql_d_fld = "
					order_set_associate_id='".$order_set_associate_id."',
					order_id='".$order_id."',
					created_date='".wv_dt('now')."',
					orders_status='0',
					orders_site_text='".$order_site."',
					modified_date = CURDATE(),
					modified_operator = '".$_SESSION["authId"]."',
					orders_options='".$order_set_option."',
					instruction_information_txt='".sqlEscStr($instruction)."', 
					dosage = '".sqlEscStr($dosage)."', 
					qty = '".sqlEscStr($qty)."', 
					sig  = '".sqlEscStr($sig)."', 
					refill  = '".sqlEscStr($refill)."', 
					ndccode = '".sqlEscStr($ndccode)."', 
					testname = '".sqlEscStr($testname)."', 
					loinc_code = '".sqlEscStr($loinc_code)."', 
					cpt_code = '".$cpt_code."', 
					inform = '".sqlEscStr($inform)."',
					resp_person = '".sqlEscStr($resp_person)."', 
					orders_dx_code = '".$orders_dx_code."', 
					order_lab_name = '".sqlEscStr($order_lab_name)."',
					snowmed = '".sqlEscStr($snowmed)."',
					template_id = '".$dataArr['template_id']."',
					template_content = '".sqlEscStr($dataArr['template_content'])."'
					
		";


		$order_id_arr[] = $order_id;
		if(!empty($order_set_associate_details_id)){
			$old_summ = $this->getOrderSumm($row_sel['order_set_associate_id']);
			$sql = $sql_d_up.$sql_d_fld." WHERE order_set_associate_details_id = '".$order_set_associate_details_id."'  ";
			//$rr = sqlQuery($sql);
			$arrSqls[]=$sql;
		}else{
			$sql = $sql_d_in.$sql_d_fld;
			//$id_rr = sqlInsert($sql);	
			$arrSqls[]=$sql;
		}
		//Format --
		//order name (site)(Instruction) - Option Optionname
		if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
		if(!empty($instruction)){  $instruction = " (".$instruction.")";  }
		//if(!empty($order_set_option)){ $optionname = " - Option ".$order_set_option;  }

		if($order_type == "Meds"){
			
			if(!empty($dosage)){  $dosage = " ".$dosage."";  }
			if(!empty($qty)){  $qty = " ".$qty."";  }
			if(!empty($sig)){  $sig = " ".$sig."";  }
			if(!empty($refill)){  $refill = " ".$refill." refills";  }
			if(!empty($ndccode)){  $ndccode = " ".$ndccode."";  }

			$strFormatPlan = $order_name."".$order_site."".$dosage."".$qty."".$sig."".$refill."".$ndc_code;

		}else{
			$strFormatPlan = $order_name."".$order_site."".$instruction."".$optionname;	
			
			//order name (site)(Instruction) - Option Optionname
			if($order_type == "Information/Instructions"){
					if(!empty($snowmed_admin)){ $snowmed = " (SNOMED CT: ".$snowmed_admin.")";  } 	
			}else if($order_type == "Labs" || $order_type == "Imaging/Rad" || $order_type == "Procedure/Sx"){					
				if(!empty($snowmed)){ $snowmed = " (SNOMED CT: ".$snowmed.")";  }
			}else{  $snowmed = ""; }
			if(!empty($order_set_option)){ $optionname = " - Option ".$order_set_option;  }
			
			$strFormatPlan = $order_name."".$order_site."".$instruction."".$optionname."".$snowmed;	
			
		}

		$arrRet[]=array("order_sum"=>$strFormatPlan, "plan_num"=>$plan_num, "old_summ"=>$old_summ, "order_type"=>$order_type);


		//send for  emdeon --
		if(empty($elem_donot_check_emdeon)){

		//get prev orders given : run once
		if($cc==0){
			$xml_ordrs_given=$this->cpoe_getPrevOrdersGiven();
			$xml_allergyEmdn=$this->cpoe_getAllergyEmdn();
		}		
		$tmpxml = $oorder->cpoe_getXmlOfMeds($order_id, $order_name, $order_type, $xml_ordrs_given, $xml_allergyEmdn);
		if(!empty($tmpxml)){$xml.=$tmpxml;}

		}

		//if($retv!="OK"){ $flg_donot_run_queries=1; $arr_emdn_err[]=$retv; }else{ $arr_current_meds_notsaved[]=array($order_name, $order_type);  }
		//send for  emdeon --

		$cc++;
		}while($flgdo==1 && $cc<=100);

		//xml
		if(empty($elem_donot_check_emdeon) && !empty($xml)){
		$strError = $oorder->cpoe_checkMedwithEmdeon($xml);
		//$strError = "This is a warning to you!";
		}
		//--

		//START CODE FOR SITE CARE PLAN
		
		$medNameArr = array("testmed");
		if(count($order_id_arr)>0) {
			$order_id_imp = implode(",",$order_id_arr);
			if(trim($order_id_imp)) {
				$getMedNameQry = "SELECT `name` as medName FROM order_details WHERE o_type IN('Meds','Medication') AND id IN(".$order_id_imp.")";	
				$getMedNameRes = imw_query($getMedNameQry);
				if(imw_num_rows($getMedNameRes)>0) {
					while($getMedNameRow = imw_fetch_assoc($getMedNameRes)) {
						$medNameArr[] = stripslashes(strtolower($getMedNameRow["medName"]));	
					}
				}
			}
		}
		require_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");	
		$OBJPatSpecificAlert = new CLSAlerts();		
		$alertToDisplayAt="admin_specific_chart_note_med_hx_cpoe";	
		$tmpAllergyArr = $tmpProbList = array();
		$getAdminAlert = $OBJPatSpecificAlert->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$elem_formId,"350px","","","",$medNameArr,$tmpAllergyArr,$tmpProbList);	
		$getAdminAlert = trim($getAdminAlert);
		//END CODE FOR SITE CARE PLAN

		if(!empty($strError)){
			$arrRet=array();
			$arrRet["cpoe_error"]=$strError;
		}else if(!empty($getAdminAlert)){
			$arrRet=array();
			$arrRet["cpoe_site_care"]=$getAdminAlert;
		}else{
			//queries to run here--
			
			foreach($arrSqls as $key => $val){
				$sql = $val;
				if(!empty($sql)){
				$rr = sqlQuery($sql);	
				}
			}
			
			//queries to run here--	
		}

		echo json_encode($arrRet);
		//Format --	
	}	
	
	function attachOrder2Chart_handler(){
		$symp = trim($_POST["symp"]);
		$as = trim($_POST["as"]);
		$pln = trim($_POST["pln"]);
		$idOdr = trim($_POST["idOdr"]);
		$a_n = trim($_POST["a_n"]);
		$idOdrset = trim($_POST["idOdrset"]);
		$site = trim($_POST["site"]);
		$sig = trim($_POST["sig"]);
		$fInsrtOrdr = trim($_POST["fInsrtOrdr"]);
		//add Order SET  to chart note --
			
			$oChartOrders = $this;
			if(!empty($idOdr) && !empty($a_n)){
				$oChartOrders->attachOrder2Chart($a_n,$idOdr,$idOdrset, $site, $sig);
			}else if(!empty($fInsrtOrdr)){ //Check Plan if it is Previous Order than insert 				
				$oChartOrders->cpoe_chkPln_insertOrder($a_n,$pln);
			}
		
		//add Order SET  to chart note --
	}
	
	function attach_order_2_chart_handler2(){
		$a_n = trim($_POST["a_n"]);
		$strIdOdr = trim($_POST["strIdOdr"]);
		/*
		
		*/
		if(empty($a_n)){  exit("No plan index given."); }
		
		$arrStrIdOdr = explode(",", $strIdOdr);
		
		//echo "<pre>";
		//print_r($_POST);
		
		if(count($arrStrIdOdr)>0){
		
			//echo "\n";
			//print_r($arrStrIdOdr);
			
			foreach($arrStrIdOdr as $key=>$val){
				
				$orderIds =$orderIds_ordrSet ="";
				$idOdrTmp = trim($val);
				
				//echo "\n".$idOdrTmp;					
				if(empty($idOdrTmp)){ continue; }
				
				//get Eye value
				$sep_eye = "~!~|eye|~!~";
				$siteval="";
				if(strpos($idOdrTmp, $sep_eye)!==false){
					$tmp_ordrids = explode($sep_eye, $idOdrTmp);
					$idOdrTmp = $tmp_ordrids[0];
					$siteval = trim("".$tmp_ordrids[1]);						
				}
				
				//get sig
				$sep_sig = "~!~|sig|~!~";
				$sigval="";
				if(strpos($idOdrTmp, $sep_sig)!==false){
					$tmp_ordrids = explode($sep_sig, $idOdrTmp);
					$idOdrTmp = $tmp_ordrids[0];
					$sigval = trim("".$tmp_ordrids[1]);
				}
				
				//get order set id
				if(strpos($idOdrTmp, "~!~|OrderSetId|~!~")!==false){
					$tmp_ordrids = explode("~!~|OrderSetId|~!~", $idOdrTmp);
					$orderIds = $tmp_ordrids[0];
					$orderIds_ordrSet = $tmp_ordrids[1];
				}else if($idOdrTmp!=""){
					$orderIds = $idOdrTmp;
					$orderIds_ordrSet =0;
				}
				
				//echo "\n".$orderIds." - ".$orderIds_ordrSet;
				
				//order id
				if(!empty($orderIds) || !empty($orderIds_ordrSet)){
					//echo("\n$a_n,$orderIds,$orderIds_ordrSet\n\n");
					$this->attachOrder2Chart($a_n,$orderIds,$orderIds_ordrSet, $siteval,$sigval);
				}				
			}
		}
		
		echo "0";
		
	}
}
?>