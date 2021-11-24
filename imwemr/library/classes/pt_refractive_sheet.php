<?php 
	include_once $GLOBALS['srcdir'].'/classes/common_function.php';
	
	class Pt_ref_sheet{
		public $patient_id = '';
		public $precision_query = '';
		public $arr_chart_vision = array();
		public $contactLensMakeArr = array();
		
		//Glasses variable
		public $glass_data_array = array(); // Stores all glasses data based on 'vis_id'
		public $dos_arr = array();
		public $noDataGlasses = true;
		public $glasses_precision_flag;
		
		//SCL contact lens variable
		public $chartIds = array();
		public $noDataSCL = true;
		public $scl_precision_flag;
		
		//Custom RGB lens variable
		public $nodataRGP = true;
		
		public function __construct($pid){
			$this->patient_id = $pid;
			
			//Precision Query
			$this->precision_query = "SELECT `C`.`ROW_DESC`, `C`.`COLUMN_DESC`, `C`.`CELLDATA`, `C`.`FLOWSHEET_ID` FROM `flowsheet_master` `M` INNER JOIN `flowsheet_child` `C` ON(`M`.`FLOWSHEET_ID`=`C`.`FLOWSHEET_ID`) WHERE `M`.`PatientMRN`='".$this->patient_id."' AND `M`.`FLOWSHEETNAME`='Contact Lens Rx 1' ORDER BY `M`.`FLOWSHEET_ID` DESC";
			
			//Get Pt. name and dob
			if($this->patient_id){
				$qryPatientData="SELECT concat(lname,', ',fname, ' ',UPPER(SUBSTRING(mname,1,1))) as patient_name,Date_Format(DOB ,'".get_sql_date_format('','y','-')."') as patient_dob,DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS pat_age, facilityPracCode from patient_data 
								LEFT JOIN pos_facilityies_tbl  ON pos_facility_id = default_facility 
								where id='".$this->patient_id."'";					
				$resPatientData=imw_query($qryPatientData);
				$rowPatientData=imw_fetch_assoc($resPatientData);
				$this->patientNameID=$rowPatientData['patient_name']." - ".$this->patient_id;
				
				if(!empty($rowPatientData["facilityPracCode"])){
					$this->patientNameID .= " (".$rowPatientData["facilityPracCode"].")";
				}
				$this->patient_dob_age="DOB: ".$rowPatientData['patient_dob']." (".$rowPatientData['pat_age'].")";
			}
			
			//Get chart array
			$this->get_arr_chart();


			//Get Contact lens manufacturer / style / brands
			$chkSql = " SELECT manufacturer, style, `type` as brand_type, make_id as id  FROM contactlensemake ";
			$resSql = imw_query($chkSql) or die(imw_error().'  '.$chkSql);

			if($resSql && imw_num_rows($resSql) > 0){
				while($rowFetch = imw_fetch_assoc($resSql)){
					$nmArr = array();
					$brandStr = '';

					if($rowFetch['id']){
						if(empty($rowFetch['manufacturer']) == false) $nmArr[] = trim($rowFetch['manufacturer']);
						if(empty($rowFetch['style']) == false) $nmArr[] = trim($rowFetch['style']);
						if(empty($rowFetch['brand_type']) == false) $nmArr[] = trim($rowFetch['brand_type']);

						if(count($nmArr) > 0) $brandStr = implode(' - ', array_unique($nmArr));

						if(empty($brandStr) == false) $this->contactLensMakeArr[$rowFetch['id']] = $brandStr;
					}
				}
			}
		}
		
		public function get_arr_chart(){
			$sql2 = "SELECT * FROM `chart_vis_master` WHERE `patient_id`='".$this->patient_id."' ORDER BY id ";
			$resp2 = imw_query($sql2);
			while($row_chart_vision=imw_fetch_assoc($resp2)){
				$form_id_get=$row_chart_vision["form_id"];
				$patient_id_get=$row_chart_vision["patient_id"];
				$this->arr_chart_vision[$form_id_get][$patient_id_get]=$row_chart_vision;
			}
		}
		
		public function filterMrGiven($mr){
			foreach($mr as $key=>$val){
				$val = trim($val);
				if(in_array($val, array('MR 1', 'MR 2', 'MR 3'))){
					$mr[$key]=trim($val);
				}
				else{
					unset($mr[$key]);
				}
			}
			asort($mr);
			return $mr;
		}

		public function get_glasses_data(){
			$sql1 = "SELECT `id`, date_format(`date_of_service`,'".get_sql_date_format()."') AS 'date_of_service' FROM `chart_master_table` WHERE `patient_id`='".$this->patient_id."' AND delete_status='0' AND purge_status='0' ORDER BY chart_master_table.date_of_service DESC";
			$resp1 = imw_query($sql1);
			if(imw_num_rows($resp1) > 0){
				while($row1 = imw_fetch_assoc($resp1)){
					$date_of_service = $row1['date_of_service'];
					$chartId = $row1['id'];
					array_push($this->chartIds, array("id"=>$chartId,"dos"=>$date_of_service));
					//Pushed date_of_service in the array i.e to be used in further master array loop
					array_push($this->arr_chart_vision[$chartId][$this->patient_id],array('date_of_service' => $date_of_service));
					$row2 = $this->arr_chart_vision[$chartId][$this->patient_id];
					if(count($row2) > 0){
						//Creates master array of glasses based on chart id
						$this->glass_data_array[$chartId] = $row2;	
					}
				}
			}
			$return_arr = array();
			if(count($this->glass_data_array) > 0){
				foreach($this->glass_data_array as $key => $val){
					$obj = $this->glass_data_array[$key];
					$MR_1 = $MR_2 = $MR_3 = "";
					$mr_statusElements = explode(",",$obj['status_elements']);
					$mrGiven=array();$print_icon_val="0";$mrPrintVis="";
					$row_to_apr=0;
					$data_arr = array();
					$date_of_service = $obj[0]['date_of_service'];
					$date_of_service = explode("-",$date_of_service);
					$date_of_service[2] = substr($date_of_service[2],2);
					$date_of_service = implode("-",$date_of_service);					
					
					//MR 4+ --
					$key_fid = $key;
					$ovis =  new Vision($this->patient_id, $key_fid);
					$arMR = $ovis->get_mutli_mr_pc_v2("MR");
					
					if(count($arMR)>0){
						foreach($arMR as $indx => $objMR){
							
							//echo $indx.", ". $objMR['elem_visMrOtherOdS_'.$indx];
							
							$sfx1 = $sfx2 = "";
							if($indx>1){
								$sfx1 = "Other";
								if($indx>2){
								$sfx2 = "_".$indx;
								}
							}	
							
							$MR_3="";
							$mrGiven_str="";$print_icon_val="0";$mrPrint="";
							if((!empty($objMR['elem_visMrOtherOdS_'.$indx]) && in_array('elem_visMr'.$sfx1.'OdS'.$sfx2.'=1',$mr_statusElements) ) || 
								(!empty($objMR['elem_visMrOtherOdC_'.$indx]) && in_array('elem_visMr'.$sfx1.'OdC'.$sfx2.'=1',$mr_statusElements) ) || 
								(!empty($objMR['elem_visMrOtherOdA_'.$indx]) && in_array('elem_visMr'.$sfx1.'OdA'.$sfx2.'=1',$mr_statusElements) )|| 
								(!empty($objMR['elem_visMrOtherOsS_'.$indx]) && in_array('elem_visMr'.$sfx1.'OsS'.$sfx2.'=1',$mr_statusElements) ) ||
								(!empty($objMR['elem_visMrOtherOsC_'.$indx]) && in_array('elem_visMr'.$sfx1.'OsC'.$sfx2.'=1',$mr_statusElements) ) || 
								(!empty($objMR['elem_visMrOtherOsA_'.$indx]) && in_array('elem_visMr'.$sfx1.'OsA'.$sfx2.'=1',$mr_statusElements)) ||
								(!empty($objMR['elem_visMrDescOther_'.$indx]) && in_array('elem_visMrDescOther_'.$indx.'=1',$mr_statusElements))){ 
								$mrGiven_str="MR ".$indx; $mrGiven[]=$mrGiven_str;
								if(strpos($objMR['elem_mrNoneGiven'.$indx],"MR ".$indx)!==false && in_array('elem_mrNoneGiven'.$indx.'=1',$mr_statusElements)){ 
									$print_icon_val="1"; $MR_3="bg-success"; $mrPrint.="MR ".$indx.","; 
								} 
							}
							
							if(!empty($mrGiven_str)){
								
								$dosRowspan = count($mrGiven)*2;
								$rowspanPrint = true;
								$mrGivenPrint = trim($mrPrint,","); //implode(",",$mrGiven);
								$return_arr=array();
								// OD
								$return_arr['row_function'] = "detailMr('".$key_fid."', this, event)";
								if($rowspanPrint){
									$return_arr['date_of_service'] = $dosRowspan.'~~'.$date_of_service;
								}
								$return_arr['class'] = $MR_3;
								$return_arr['od']['vis_mr_od_s'] = $objMR['elem_visMrOtherOdS_'.$indx];
								$return_arr['od']['vis_mr_od_c'] = $objMR['elem_visMrOtherOdC_'.$indx];
								$return_arr['od']['vis_mr_od_a'] = $objMR['elem_visMrOtherOdA_'.$indx].((trim($objMR['elem_visMrOtherOdA_'.$indx])!="")?"<span class=\"degree\">&deg;</span>":"");
								$return_arr['od']['vis_mr_od_txt_1'] = $objMR['elem_visMrOtherOdTxt1_'.$indx];
								$return_arr['od']['vis_mr_od_add'] = $objMR['elem_visMrOtherOdAdd_'.$indx];
								$return_arr['od']['vis_mr_od_txt_2'] = $objMR['elem_visMrOtherOdTxt2_'.$indx];
								$return_arr["od"]['mr_type'] = $objMR['elem_mr_type'.$indx];
								$prism_od = '';
								if($objMR['elem_visMrOtherOdP_'.$indx]!=""){
									$prism_od .= $objMR['elem_visMrOtherOdP_'.$indx]."&nbsp;&utrif;&nbsp;".$objMR['elem_visMrOtherOdSel1_'.$indx];
								}
								if($objMR['elem_visMrOtherOdSlash_'.$indx]!=""){
									$prism_od .= (($prism_od!="")?" / ":"").$objMR['elem_visMrOtherOdSlash_'.$indx]." ".$objMR['elem_visMrOtherOdPrism_'.$indx];
								}
								$return_arr['od']['prism_od'] = $prism_od;
								/* if($mrGivenPrint = "MR 2" && strpos($objMR['vis_mr_none_given'],"MR 2")==false){
									$mrGivenPrint="";
								} */
							
								if(!empty($mrPrintVis)){  $mrPrintVis=$mrPrintVis.","; }
								$mrPrintVis = $mrPrintVis.$mrGivenPrint;
								
								if($rowspanPrint && $print_icon_val==1){
									$return_arr['print_function'] = $dosRowspan.'~~'.'printMrPRS("'.$mrGivenPrint.'",event,"'.$key.'")';
									$rowspanPrint = false;
								}else if($print_icon_val==0 && $rowspanPrint!=false){
									$return_arr['print_function'] = $dosRowspan;
									$rowspanPrint = false;
								}
								
								//OS
								$return_arr['os']['vis_mr_os_s']  = $objMR['elem_visMrOtherOsS_'.$indx];
								$return_arr['os']['vis_mr_os_c']  = $objMR['elem_visMrOtherOsC_'.$indx];
								$return_arr['os']['vis_mr_os_a'] = $objMR['elem_visMrOtherOsA_'.$indx].((trim($objMR['elem_visMrOtherOsA_'.$indx])!="")?"<span class=\"degree\">&deg;</span>":"");
								$return_arr['os']['vis_mr_os_txt_1']  = $objMR['elem_visMrOtherOsTxt1_'.$indx];
								$return_arr['os']['vis_mr_os_add']  = $objMR['elem_visMrOtherOsAdd_'.$indx];
								$return_arr['os']['vis_mr_os_txt_2']  = $objMR['elem_visMrOtherOsTxt2_'.$indx];
								$return_arr["os"]['mr_type'] = $objMR['elem_mr_type'.$indx];
								$prism_os ="";
								if($objMR['elem_visMrOtherOsP_'.$indx]!=""){
									$prism_os .= $objMR['elem_visMrOtherOsP_'.$indx]."&nbsp;&utrif;&nbsp;".$objMR['elem_visMrOtherOsSel1_'.$indx];
								}
								if($objMR['elem_visMrOtherOsSlash_'.$indx]!=""){
									$prism_os .= (($prism_os!="")?" / ":"").$objMR['elem_visMrOtherOsSlash_'.$indx]." ".$objMR['elem_visMrOtherOsPrism_'.$indx];
								}
								$return_arr['os']['prism_os'] = $prism_os;
								$this->noDataGlasses = false;
								$return_arr['os']['noDataGlasses'] = $this->noDataGlasses;									
								
								//Contains MR Data 
								$data_arr[] = $return_arr;
							}						
						}
						
						//correct rowspan
						//echo "<pre>";
						//print_r($data_arr);
						
						if(count($data_arr)>0){
						
							foreach($data_arr as $k => $o_data_arr){
								$tmp = $o_data_arr["date_of_service"];
								$artmp = explode("~~", $tmp);
								$data_arr[$k]["date_of_service"] = $dosRowspan.'~~'.$artmp[1];
								
								$tmp = $o_data_arr["print_function"];
								$artmp = explode("~~", $tmp);
								$tmp_print_fun="";
								if(!empty($mrPrintVis)){
									$tmp_print_fun = '~~'.'printMrPRS("'.$mrPrintVis.'",event,"'.$key_fid.'")';
								}
								$data_arr[$k]["print_function"] = $dosRowspan.$tmp_print_fun; //$artmp[1];
								$data_arr[$k]["mrGivenPrint"] = $mrGivenPrint;	
							}
						}
					}
					
					//MR 4+ --
					
					
					if(count($mrGiven) > 0){
						$return_array[$obj['id']] = $data_arr;	
					}	
				}
			}
			
			//
			//echo "<pre>";
			//print_r($return_array);
			//exit();		
			
			return $return_array;		
		}

		public function get_precision_for_glasses(){
			$return_array = array();
			$this->glasses_precision_flag = false;
			$dtPrev = imw_query($this->precision_query);
			$prevGlasses = array();
			if($dtPrev && imw_num_rows($dtPrev)>0){
				while($row = imw_fetch_assoc($dtPrev)){
					$rowD = trim($row['ROW_DESC']);
					$colD = "";
					if($rowD=="Date"){
						$prevGlasses[$row['FLOWSHEET_ID']]['DATE'] = str_replace("/","-",trim($row['CELLDATA']));
					}
					elseif($rowD=="OD" || $rowD=="OS"){
						$colD = trim($row['COLUMN_DESC']);
						switch($colD){
							case "Sphere":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['S'] = trim($row['CELLDATA']);
							break;
							case "Cylinder":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['C'] = trim($row['CELLDATA']);
							break;
							case "Axis":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['AX'] = trim($row['CELLDATA']);
							break;
							case "Prism":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['P'] = trim($row['CELLDATA']);
							break;
							case "Add":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['AD'] = trim($row['CELLDATA']);
							break;
							case "VD":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['VD'] = trim($row['CELLDATA']);
							break;
						}
					}
				}
			}
			if(count($prevGlasses)>0){
				$this->glasses_precision_flag = true;
				foreach($prevGlasses as $dt){
					$dt['DATE'] = $dt['DATE'];
					$dt['DATE'] = explode("-",$dt['DATE']);
					$dt['DATE'][0] = str_pad($dt['DATE'][0], 2, '0', STR_PAD_LEFT);
					$dt['DATE'][1] = str_pad($dt['DATE'][1], 2, '0', STR_PAD_LEFT);
					$dt['DATE'] = implode("-",$dt['DATE']);
					$dt['DATE'] = explode("-",$dt['DATE']);
					$dt['DATE'][2] = substr($dt['DATE'][2],2);
					$dt['DATE'] = implode("-",$dt['DATE']);
					$odDt = $dt['OD'];
					$osDt = $dt['OS'];
					$dtPrint = false;
					$printIcon = false;
					if(count($odDt)>0){
						if(!$dtPrint){
							$return_arr['OD']['DATE'] = $dt['DATE'];
							$dtPrint = true;
						}
						$return_arr['OD']['S'] = $odDt['S'];
						$return_arr['OD']['C'] = $odDt['C'];
						$return_arr['OD']['AX'] = $odDt['AX'].((trim($odDt['AX'])!="")?"<span class=\"degree\">&deg;</span>":"");
						$return_arr['OD']['VD'] = $odDt['VD'];
						$return_arr['OD']['P'] = $odDt['P'];
						$return_arr['OD']['AD'] = $odDt['AD'];
						if(!$printIcon){
							$return_arr['OD']['no_print'] = $printIcon;
							$printIcon = true;
						}
					}
					if(count($osDt)>0){
						if(!$dtPrint){
							$return_arr['OS']['DATE'] = $dt['DATE'];
							$dtPrint = true;
						}
						$return_arr['OS']['S'] = $osDt['S'];
						$return_arr['OS']['C'] = $osDt['C'];
						$return_arr['OS']['AX'] = $osDt['AX'].((trim($osDt['AX'])!="")?"<span class=\"degree\">&deg;</span>":"");
						$return_arr['OS']['VD'] = $osDt['VD'];
						$return_arr['OS']['P'] = $osDt['P'];
						$return_arr['OS']['AD'] = $osDt['AD'];
						if(!$printIcon){
							$return_arr['OS']['no_print'] = $printIcon;
							$printIcon = true;
						}
					}
					$return_array[] = $return_arr;
				}
			}
			return $return_array;
		}

		public function get_scl_contact_lens_data(){
			$return_arr = array();
			/* foreach($this->chartIds as $chart){
				$date_of_service = $chart['dos'];
				echo "date of service: ".$date_of_service."<br />";
				$date_of_service = explode("-",$date_of_service);
				$date_of_service[2] = substr($date_of_service[2],2);
				$date_of_service = implode("-",$date_of_service);
				$cahrtId = $chart['id']; */
				$sql3 = "SELECT `clws_id`, clws_type, clws_trial_number, dos, DATE(clws_savedatetime) as saved_date FROM `contactlensmaster` WHERE `patient_id`='".$this->patient_id."' AND del_status='0' ORDER BY dos DESC";
				$resp3 = imw_query($sql3);
				if(imw_num_rows($resp3) > 0){
					while($row3 = imw_fetch_assoc($resp3)){
						$data_arr=array();
						$clwsId = $row3['clws_id'];
						$date_of_service = date('m-d-y', strtotime($row3['saved_date']));
						$bgColor=$clws_type_part='';
						if(strstr($row3['clws_type'], 'Final')){
							$bgColor='style="background-color:#fae1fa"';
							$clws_type_part= 'Final';
						}else{
							$tt = explode(',', $row3['clws_type']);
							$clws_type_part= $tt[sizeof($tt)-1];
							if($clws_type_part=='Current Trial'){ $clws_type_part.=' #'.$row3['clws_trial_number'];}
						}
						$sql4 = "SELECT * FROM `contactlensworksheet_det` WHERE `clws_id`='".$clwsId."' AND `clType`='scl' ORDER BY `id` ASC";
						$resp4 = imw_query($sql4);
						if($resp4 && imw_num_rows($resp4)>0){
							$dosRowspan = imw_num_rows($resp4);
							$rowspanPrint = true;
							$data_arr = array();
							while($row4 = imw_fetch_assoc($resp4)){
								$site = $row4['clEye'];
								$BC=$DI=$sp=$cy=$ax=$ad=$dva=$nva=$type="";
								if($site=="OD"){
									$BC = $row4['SclBcurveOD'];
									$DI = $row4['SclDiameterOD'];
									$sp = $row4['SclsphereOD'];
									$cy = $row4['SclCylinderOD'];
									$ax = $row4['SclaxisOD'];
									$ad = $row4['SclAddOD'];
									$dva = $row4['SclDvaOD'];
									$nva = $row4['SclNvaOD'];
									//$type = $row4['SclTypeOD'];
									$type = $row4['SclTypeOD_ID'];
								}
								elseif($site=="OS"){
									$BC = $row4['SclBcurveOS'];
									$DI = $row4['SclDiameterOS'];
									$sp = $row4['SclsphereOS'];
									$cy = $row4['SclCylinderOS'];
									$ax = $row4['SclaxisOS'];
									$ad = $row4['SclAddOS'];
									$dva = $row4['SclDvaOS'];
									$nva = $row4['SclNvaOS'];
									//$type = $row4['SclTypeOS'];
									$type = $row4['SclTypeOS_ID'];
								}
								elseif($site=="OU"){
									$BC = $row4['SclBcurveOU'];
									$DI = $row4['SclDiameterOU'];
									$sp = $row4['SclsphereOU'];
									$cy = $row4['SclCylinderOU'];
									$ax = $row4['SclaxisOU'];
									$ad = $row4['SclAddOU'];
									$dva = $row4['SclDvaOU'];
									$nva = $row4['SclNvaOU'];
									//$type = $row4['SclTypeOU'];
									$type = $row4['SclTypeOU_ID'];
								}
								
								if($bgColor != ''){
									$return_array['bg_color'] = $bgColor;
								}
								if($rowspanPrint){
									$return_array['date_of_service'] = $dosRowspan.'~~'.$date_of_service.'~~'.$clws_type_part;
								}

								$return_array['site'] = $site;
								$return_array['BC'] = $BC;
								$return_array['DI'] = $DI;
								$return_array['sp'] = $sp;
								$return_array['cy'] = $cy;
								$return_array['ax'] = $ax.((trim($ax)!="")?"<span class=\"degree\">&deg;</span>":"");
								$return_array['ad'] = $ad;
								$return_array['dva'] = $dva;
								$return_array['nva'] = $nva;
								$return_array['type'] = ($this->contactLensMakeArr[$type]) ? $this->contactLensMakeArr[$type] : '';
								if($rowspanPrint){
									$return_array['print_function'] = 'printRxPRS("'.$clwsId.'")';
									$rowspanPrint = false;
								}
								
								$this->noDataSCL = false;
								$data_arr[] = $return_array;
							}
							imw_free_result($resp4);
						}
						$return_arr[$clwsId] = $data_arr;	
					}
				}	
			//}
			return $return_arr;
		}

		public function get_precision_for_scl_contact_lens(){
			$return_array = array();
			$this->scl_precision_flag = false;
			$dtPrev = imw_query($this->precision_query);
			$prevGlasses = array();
			if($dtPrev && imw_num_rows($dtPrev)>0){
				while($row = imw_fetch_assoc($dtPrev)){
					$rowD = trim($row['ROW_DESC']);
					$colD = "";
					if($rowD=="Date"){
						$prevGlasses[$row['FLOWSHEET_ID']]['DATE'] = str_replace("/","-",trim($row['CELLDATA']));
					}
					elseif($rowD=="OD" || $rowD=="OS"){
						$colD = trim($row['COLUMN_DESC']);
						switch($colD){
							case "BC":
							case "Base Curve":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['BC'] = trim($row['CELLDATA']);
							break;
							case "Diameter":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['DI'] = trim($row['CELLDATA']);
							break;
							case "Sph":
							case "Sphere":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['S'] = trim($row['CELLDATA']);
							break;
							case "Cyl":
							case "Cylinder":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['C'] = trim($row['CELLDATA']);
							break;
							case "Ax":
							case "Axis":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['AX'] = trim($row['CELLDATA']);
							break;
							case "Add":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['AD'] = trim($row['CELLDATA']);
							break;
							case "Lens Type":
								$prevGlasses[$row['FLOWSHEET_ID']][$rowD]['LT'] = trim($row['CELLDATA']);
							break;
						}
					}
				}
			}
			
			if(count($prevGlasses)>0){
				$this->scl_precision_flag = true;
				foreach($prevGlasses as $dt){
					$dt['DATE'] = $dt['DATE'];
					$dt['DATE'] = explode("-",$dt['DATE']);
					$dt['DATE'][0] = str_pad($dt['DATE'][0], 2, '0', STR_PAD_LEFT);
					$dt['DATE'][1] = str_pad($dt['DATE'][1], 2, '0', STR_PAD_LEFT);
					$dt['DATE'] = implode("-",$dt['DATE']);
					$dt['DATE'] = explode("-",$dt['DATE']);
					$dt['DATE'][2] = substr($dt['DATE'][2],2);
					$dt['DATE'] = implode("-",$dt['DATE']);
					$odDt = $dt['OD'];
					$osDt = $dt['OS'];
					$dtPrint = false;
					$printIcon = false;
					if(count($odDt)>0){
						if(!$dtPrint){
							$return_arr['OD']['DATE'] = $dt['DATE'];
							$dtPrint = true;
						}
						$return_arr['OD']['BC'] = $odDt['BC'];
						$return_arr['OD']['DI'] = $odDt['DI'];
						$return_arr['OD']['AX'] = $odDt['AX'].((trim($odDt['AX'])!="")?"<span class=\"degree\">&deg;</span>":"");
						$return_arr['OD']['S']  = $odDt['S'];
						$return_arr['OD']['C']  = $odDt['C'];
						$return_arr['OD']['AD'] = $odDt['AD'];
						$return_arr['OD']['LT'] = $odDt['LT'];
						if(!$printIcon){
							$return_arr['OD']['no_print'] = $printIcon;
							$printIcon = true;
						}
					}
					if(count($osDt)>0){
						if(!$dtPrint){
							$return_arr['OS']['DATE'] = $dt['DATE'];
							$dtPrint = true;
						}
						$return_arr['OS']['BC'] = $odDt['BC'];
						$return_arr['OS']['DI'] = $odDt['DI'];
						$return_arr['OS']['AX'] = $odDt['AX'].((trim($odDt['AX'])!="")?"<span class=\"degree\">&deg;</span>":"");
						$return_arr['OS']['S']  = $odDt['S'];
						$return_arr['OS']['C']  = $odDt['C'];
						$return_arr['OS']['AD'] = $odDt['AD'];
						$return_arr['OS']['LT'] = $odDt['LT'];
						if(!$printIcon){
							$return_arr['OS']['no_print'] = $printIcon;
							$printIcon = true;
						}
					}
					$return_array[] = $return_arr;
				}
			}
		}

		public function get_custom_rgp_lens_data(){
			$return_array = array();
			/* foreach($this->chartIds as $chart){
				$date_of_service = $chart['dos'];
				$date_of_service = explode("-",$date_of_service);
				$date_of_service[2] = substr($date_of_service[2],2);
				$date_of_service = implode("-",$date_of_service);
				$cahrtId = $chart['id']; */
				$sql5 = "SELECT `clws_id`, clws_type, clws_trial_number, dos, DATE(clws_savedatetime) as saved_date FROM `contactlensmaster` WHERE `patient_id`='".$this->patient_id."' AND del_status='0' order by dos DESC";
				$resp5 = imw_query($sql5);
				if($resp5 && imw_num_rows($resp5)){
					while($row5 = imw_fetch_assoc($resp5)){
						$bgColorRGP=$clws_type_part='';
						if(strstr($row5['clws_type'], 'Final')){
						    $bgColorRGP='style="background-color:#fae1fa"';
							$clws_type_part= 'Final';
						}else{
							$tt=explode(',', $row5['clws_type']);
							$clws_type_part= $tt[sizeof($tt)-1];
							if($clws_type_part=='Current Trial'){ $clws_type_part.=' #'.$row5['clws_trial_number'];}
						}
						if(strstr($row5['clws_type'], 'Final')){
						    $bgColorRGP='style="background-color:#fae1fa"';
						}
						
						$clwsId = $row5['clws_id'];
						$date_of_service = $row5['saved_date'];
						$date_of_service = date('m-d-y', strtotime($date_of_service));
						
						$sql6 = "SELECT * FROM `contactlensworksheet_det` WHERE `clws_id`='".$clwsId."' AND `clType` IN('rgp', 'cust_rgp') ORDER BY `id` ASC";
						$resp6 = imw_query($sql6);
						if($resp6 && imw_num_rows($resp6)>0){
							$dosRowspan = imw_num_rows($resp6);
							$rowspanPrint = true;
							$data_arr = array();
							while($row6 = imw_fetch_assoc($resp6)){
								$site = $row6['clEye'];
								$clType = $row6['clType'];
								$BC=$DI=$sp=$cy=$ax=$ad=$dva=$nva=$type="";
								if($clType=='rgp'){
									if($site=="OD"){
										$BC = $row6['RgpBCOD'];
										$DI = $row6['RgpDiameterOD'];
										$sp = $row6['RgpPowerOD'];
										$cyl = $row6['RgpCylinderOD'];
										$axis = $row6['RgpAxisOD'];
										$cy = $row6['RgpOZOD'];
										$ax = $row6['RgpColorOD'];
										$ad = $row6['RgpAddOD'];
										$dva = $row6['RgpDvaOD'];
										$nva = $row6['RgpNvaOD'];
										//$type = $row6['RgpTypeOD'];
										$type = $row6['RgpTypeOD_ID'];
									}
									elseif($site=="OS"){
										$BC = $row6['RgpBCOS'];
										$DI = $row6['RgpDiameterOS'];
										$sp = $row6['RgpPowerOS'];
										$cyl = $row6['RgpCylinderOS'];
										$axis = $row6['RgpAxisOS'];										
										$cy = $row6['RgpOZOS'];
										$ax = $row6['RgpColorOS'];
										$ad = $row6['RgpAddOS'];
										$dva = $row6['RgpDvaOS'];
										$nva = $row6['RgpNvaOS'];
										//$type = $row6['RgpTypeOS'];
										$type = $row6['RgpTypeOS_ID'];
									}
									elseif($site=="OU"){
										$BC = $row6['RgpBCOU'];
										$DI = $row6['RgpDiameterOU'];
										$sp = $row6['RgpPowerOU'];
										$cy = $row6['RgpOZOU'];
										$ax = $row6['RgpColorOU'];
										$ad = $row6['RgpAddOU'];
										$dva = $row6['RgpDvaOU'];
										$nva = $row6['RgpNvaOU'];
										//$type = $row6['RgpTypeOU'];
										$type = $row6['RgpTypeOU_ID'];
									}
								}
								elseif($clType=='cust_rgp'){
									if($site=="OD"){
										$BC = $row6['RgpCustomBCOD'];
										$DI = $row6['RgpCustomDiameterOD'];
										$sp = $row6['RgpCustomPowerOD'];
										$cyl = $row6['RgpCustomCylinderOD'];
										$axis = $row6['RgpCustomAxisOD'];
										$cy = $row6['RgpCustomOZOD'];
										$ax = $row6['RgpCustomColorOD'];
										$ad = $row6['RgpCustomAddOD'];
										$dva = $row6['RgpCustomDvaOD'];
										$nva = $row6['RgpCustomNvaOD'];
										//$type = $row6['RgpCustomNvaOD'];
										$type = $row6['RgpCustomTypeOD_ID'];
									}
									elseif($site=="OS"){
										$BC = $row6['RgpCustomBCOS'];
										$DI = $row6['RgpCustomDiameterOS'];
										$sp = $row6['RgpCustomPowerOS'];
										$cyl = $row6['RgpCustomCylinderOS'];
										$axis = $row6['RgpCustomAxisOS'];
										$cy = $row6['RgpCustomOZOS'];
										$ax = $row6['RgpCustomColorOS'];
										$ad = $row6['RgpCustomAddOS'];
										$dva = $row6['RgpCustomDvaOS'];
										$nva = $row6['RgpCustomNvaOS'];
										//$type = $row6['RgpCustomTypeOS'];
										$type = $row6['RgpCustomTypeOS_ID'];
									}
									elseif($site=="OU"){
										$BC = $row6['RgpCustomBCOU'];
										$DI = $row6['RgpCustomDiameterOU'];
										$sp = $row6['RgpCustomPowerOU'];
										$cy = $row6['RgpCustomOZOU'];
										$ax = $row6['RgpCustomColorOU'];
										$ad = $row6['RgpCustomAddOU'];
										$dva = $row6['RgpCustomDvaOU'];
										$nva = $row6['RgpCustomNvaOU'];
										//$type = $row6['RgpCustomTypeOU'];
										$type = $row6['RgpCustomTypeOU_ID'];
									}
								}
								$return_arr['bgColorRGP'] = $bgColorRGP;
									if($rowspanPrint){
										$return_arr['date_of_service'] = $dosRowspan.'~~'.$date_of_service.'~~'.$clws_type_part;
									}
									$return_arr['site'] = $site;
									$return_arr['BC']   = $BC;
									$return_arr['DI']   = $DI;
									$return_arr['sp']   = $sp;
									$return_arr['cyl']   = $cyl;
									$return_arr['axis']   = $axis;
									$return_arr['cy']   = $cy;
									$return_arr['ax']   = $ax;
									$return_arr['ad']   = $ad;
									$return_arr['dva']  = $dva;
									$return_arr['nva']  = $nva;
									$return_arr['type'] = ($this->contactLensMakeArr[$type]) ? $this->contactLensMakeArr[$type] : '';
									if($rowspanPrint){
										$return_arr['print_function'] = 'printRxPRS("'.$clwsId.'")';
										$rowspanPrint = false;
									}
								$this->nodataRGP = false;
								$data_arr[] = $return_arr;
							}
							imw_free_result($resp6);
							$return_array[$clwsId] = $data_arr;
						}
					}
				}
				imw_free_result($resp5);
			//}
			//pre($return_array);
			return $return_array;
		}
		
		public function get_modal($request_id){
			$glass_modal_arr = $this->glass_data_array[$request_id];
			$modal_str = '<div class="row">';
				if($glass_modal_arr['provider_id ']!="0"){
					$modal_str .= '
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-12 purple_bar">
										<lable>MR 1</label>
									</div>
									<div class="col-sm-12">
										<div class="row">
											<table class="table table-striped table-bordered">
												<tr>
													<td class="col-xs-1"><label class="od">OD</label></td>
													<td class="col-xs-2"><label>S '.$glass_modal_arr['vis_mr_od_s'].'</label></td>
													<td class="col-xs-2"><label>C '.$glass_modal_arr['vis_mr_od_c'].'</label></td>
													<td class="col-xs-1"><label>A '.$glass_modal_arr['vis_mr_od_a'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_od_txt_1'].'</label></td>
													<td class="col-xs-2"><label>Add '.$glass_modal_arr['vis_mr_od_add'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_od_txt_2'].'</label></td>
												</tr>
												<tr>
													<td class="col-xs-1"><label class="os">OS</label></td>
													<td class="col-xs-2"><label>S '.$glass_modal_arr['vis_mr_os_s'].'</label></td>
													<td class="col-xs-2"><label>C '.$glass_modal_arr['vis_mr_os_c'].'</label></td>
													<td class="col-xs-1"><label>A '.$glass_modal_arr['vis_mr_os_a'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_os_txt_1'].'</label></td>
													<td class="col-xs-2"><label>Add '.$glass_modal_arr['vis_mr_os_add'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_os_txt_2'].'</label></td>
												</tr>	
											</table>	
										</div>
									</div>	
								</div>
							</div>';
				}
				
				if($glass_modal_arr['providerIdOther ']!="0"){
					$modal_str .= '
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-12 purple_bar">
										<lable>MR 2</label>
									</div>
									<div class="col-sm-12">
										<div class="row">
											<table class="table table-striped table-bordered">
												<tr>
													<td class="col-xs-1"><label class="od">OD</label></td>
													<td class="col-xs-2"><label>S '.$glass_modal_arr['vis_mr_od_given_given_s'].'</label></td>
													<td class="col-xs-2"><label>C '.$glass_modal_arr['vis_mr_od_given_c'].'</label></td>
													<td class="col-xs-1"><label>A '.$glass_modal_arr['vis_mr_od_given_a'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_od_given_txt_1'].'</label></td>
													<td class="col-xs-2"><label>Add '.$glass_modal_arr['vis_mr_od_given_add'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_od_given_txt_2'].'</label></td>
												</tr>
												<tr>
													<td class="col-xs-1"><label class="os">OS</label></td>
													<td class="col-xs-2"><label>S '.$glass_modal_arr['vis_mr_os_given_s'].'</label></td>
													<td class="col-xs-2"><label>C '.$glass_modal_arr['vis_mr_os_given_c'].'</label></td>
													<td class="col-xs-1"><label>A '.$glass_modal_arr['vis_mr_os_given_a'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_os_given_txt_1'].'</label></td>
													<td class="col-xs-2"><label>Add '.$glass_modal_arr['vis_mr_os_given_add'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_os_given_txt_2'].'</label></td>
												</tr>	
											</table>
										</div>
									</div>	
								</div>
							</div>';
				}

				if($row2['providerIdOther_3 ']!="0"){
					$modal_str .= '
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-12 purple_bar">
										<lable>MR 3</label>
									</div>
									<div class="col-sm-12">
										<div class="row">
											<table class="table table-striped table-bordered">
												<tr>
													<td class="col-xs-1"><label class="od">OD</label></td>
													<td class="col-xs-2"><label>S '.$glass_modal_arr['visMrOtherOdS_3'].'</label></td>
													<td class="col-xs-2"><label>C '.$glass_modal_arr['visMrOtherOdC_3'].'</label></td>
													<td class="col-xs-1"><label>A '.$glass_modal_arr['visMrOtherOdA_3'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['visMrOtherOdTxt1_3'].'</label></td>
													<td class="col-xs-2"><label>Add '.$glass_modal_arr['visMrOtherOdAdd_3'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['visMrOtherOdTxt2_3'].'</label></td>
												</tr>
												<tr>
													<td class="col-xs-1"><label class="os">OS</label></td>
													<td class="col-xs-2"><label>S '.$glass_modal_arr['vis_mr_os_given_s'].'</label></td>
													<td class="col-xs-2"><label>C '.$glass_modal_arr['vis_mr_os_given_c'].'</label></td>
													<td class="col-xs-1"><label>A '.$glass_modal_arr['vis_mr_os_given_a'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_os_given_txt_1'].'</label></td>
													<td class="col-xs-2"><label>Add '.$glass_modal_arr['vis_mr_os_given_add'].'</label></td>
													<td class="col-xs-2"><label>'.$glass_modal_arr['vis_mr_os_given_txt_2'].'</label></td>
												</tr>	
											</table>
										</div>
									</div>	
								</div>
							</div>';
				}	
			$modal_str .= '<div>';
			return  $modal_str;
		}	
		//BELOW FUNCTION IS CREATED TO DISPLAY PRS EXTERNAL VA WORK BELOW THE GLASSES RX SECTION
		public function get_externalMR_data(){
			
			$glassesExtMRData = $prismOdSel1 = $prismOdSel2 = $prismOsSel1 = $prismOsSel2 = "";
			$rowspan ="2";
			//QUERY EXECUTION
			$sqlQry = "SELECT `id`,	patient_id, date_format(`ext_dos`,'".get_sql_date_format()."') AS 'date_of_service',
			ext_mr_od_s, ext_mr_od_c, ext_mr_od_a, ext_mr_od_txt1, ext_mr_od_add, ext_mr_od_txt2, ext_mr_od_gl_ph, ext_mr_od_gl_ph_txt, ext_mr_od_p, ext_mr_od_sel_1, ext_mr_od_slash, ext_mr_od_prism, 
			ext_mr_os_s, ext_mr_os_c, ext_mr_os_a, ext_mr_os_txt1, ext_mr_os_add, ext_mr_os_txt2, ext_mr_os_gl_ph, ext_mr_os_gl_ph_txt, ext_mr_os_p, ext_mr_os_sel_1, ext_mr_os_slash, ext_mr_os_prism, ext_mr_desc, ext_mr_prism_desc, prescribed_by FROM `chart_vis_ext_mr` WHERE `patient_id`='".$this->patient_id."' AND del_status='0' ORDER BY chart_vis_ext_mr.ext_dos DESC";
			$exeQry = imw_query($sqlQry);
			if(imw_num_rows($exeQry) > 0){
				while($resQry = imw_fetch_assoc($exeQry)){
					$date_of_service = $resQry['date_of_service'];
					
					if($resQry['ext_mr_desc']) {  $rowspan ="3";  }
					//OD PRISM SECTION DISPLAY WORK
					//PRISM OD HORIZONTAL SECTION 
					if($resQry['ext_mr_od_p'] &&  $resQry['ext_mr_od_sel_1'])
					{
						$prismOdSel1=$resQry['ext_mr_od_p'].'&nbsp;&utrif;&nbsp;'.$resQry['ext_mr_od_sel_1'];
					}
					else
					{	
						$prismOdSel1 = $resQry['ext_mr_od_p'].'&nbsp;'.$resQry['ext_mr_od_sel_1'];
					}
					//PRISM OD VERTICAL SECTION -AFTER SLASH SECTION
					if(($resQry['ext_mr_od_slash'] || $resQry['ext_mr_od_prism']))
					{
						$prismOdSel2 = '&nbsp;/&nbsp;'.$resQry['ext_mr_od_slash'].'&nbsp;'.$resQry['ext_mr_od_prism'];
					}	
					
					//OS PRISM SECTION DISPLAY WORK
					if($resQry['ext_mr_os_p'] &&  $resQry['ext_mr_os_sel_1'])
					{
						$prismOsSel1=$resQry['ext_mr_os_p'].'&nbsp;&utrif;&nbsp;'.$resQry['ext_mr_os_sel_1'];
					}
					else
					{	
						$prismOsSel1 = $resQry['ext_mr_os_p'].'&nbsp;'.$resQry['ext_mr_os_sel_1'];
					}
					//PRISM OS VERTICAL SECTION -AFTER SLASH SECTION
					if(($resQry['ext_mr_os_slash'] || $resQry['ext_mr_os_prism']))
					{
						$prismOsSel2 = '&nbsp;/&nbsp;'.$resQry['ext_mr_os_slash'].'&nbsp;'.$resQry['ext_mr_os_prism'];
					}	
							
					//$expelId = urlencode(base64_encode($resQry['id']));
					//FULL DATA DISPLAYED OF PRS EXTERNAL MR 
					$glassesExtMRData .='<tr id="tr_'.$resQry['id'].'_OD">';
					$glassesExtMRData .= '<td class="text-center" rowspan="'.$rowspan.'" ><span>'.$resQry['date_of_service'].'</span></td>';
					$glassesExtMRData .= '<td style="color:green;">OD</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_od_s'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_od_c'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_od_a'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_od_txt1'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_od_add'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_od_txt2'].'</td>';
					$glassesExtMRData .= '<td>'.$prismOdSel1.$prismOdSel2.'</td>';
					$glassesExtMRData .= '<td rowspan="'.$rowspan.'">'.$resQry['prescribed_by'].'</td>';
					$glassesExtMRData .= '<td title="Delete" rowspan="'.$rowspan.'">
					<a class="del_ext_mr" href="#" id="'.$resQry['id'].'" title="Delete"><img src="../../library/images/smclose.png" /></a>
					</td>';
					$glassesExtMRData .='</tr>';
					
					$glassesExtMRData .='<tr id="tr_'.$resQry['id'].'_OS">';
					$glassesExtMRData .= '<td style="color:blue;">OS</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_os_s'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_os_c'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_os_a'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_os_txt1'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_os_add'].'</td>';
					$glassesExtMRData .= '<td>'.$resQry['ext_mr_os_txt2'].'</td>';
					$glassesExtMRData .= '<td>'.$prismOsSel1.$prismOsSel2.'</td>';
					$glassesExtMRData .='</tr>';
					$glassesExtMRData.='<tr id="tr_'.$resQry['id'].'_DESC"><td colspan="8">'.$resQry['ext_mr_desc'].'</tr>';
				}
			}
			else
			{
				$glassesExtMRData.='<tr><td colspan="11"><span class="noData">No Data Found</span></td></tr>';
			}	
			return $glassesExtMRData;		
		}
	} 
?>