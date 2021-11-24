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
?>
<?php
/*
File: handle_pt_registration.php
Purpose: Action script to approve/decline contact lens orders
Access Type: Direct
*/
include_once("../config/globals.php");

//$_REQUEST['mode']='approve';
//$_REQUEST['orderNum']='14680502984006';

$mode=$_REQUEST['sel_op'];
$orderNum=$_REQUEST['row_id'];
$arrResult=array();

if(empty($orderNum)==false){
	if($mode=='approve_all'){
		$arrOrderNum=explode(',', $orderNum);
	}else{
		$arrOrderNum[]=$orderNum;
	}
	if(sizeof($arrOrderNum)>0){
		if($mode=='decline'){
			$qry="Update iportal_req_orders SET is_approved='2', operator_id='".$_SESSION['authId']."' WHERE temp_order_num='".$orderNum."'";
			$rs=imw_query($qry);
			if(!$rs){
				$arrResult[$orderNum]='error';
			}else{
				$arrResult[$orderNum]='success';
			}
		}else{
			foreach($arrOrderNum as $orderNum){
				//GETTING OPTICAL FACILITY ID
				$loc_id=0;
				$qry="Select pos.pos_facility_id FROM facility JOIN pos_facilityies_tbl pos ON pos.pos_facility_id=facility.fac_prac_code 
				WHERE facility.id='".$_SESSION['login_facility']."' LIMIT 0,1";
				$rs=imw_query($qry);
				$res=imw_fetch_assoc($rs);
				$pos_fac_id=$res['pos_facility_id'];
				unset($rs);
				if($pos_fac_id>0){
					$qry="Select id FROM in_location WHERE pos='".$pos_fac_id."' LIMIT 0,1";
					$rs=imw_query($qry);
					$res=imw_fetch_assoc($rs);
					$loc_id=$res['id'];
					unset($rs);
				}
	
				//ADD NEW ORDER IN OPTICAL
				if($mode=='approve'){
					$arrOrderDet=array();
					$comments='';
					$clws_id=$patient_id=0;
					$qry="Select patient_id, eye, brand, manufacturer, boxes, disposable_id, package_id, supplies_id, ordered_data, clws_id,
					comments FROM iportal_req_orders WHERE temp_order_num='".$orderNum."' AND is_approved='0' ORDER BY eye ASC";
					$rs=imw_query($qry);
					while($res=imw_fetch_assoc($rs)){
						$eye=$res['eye'];
						
						$arrOrderDet[$eye]['brand']=$res['brand'];
						$arrOrderDet[$eye]['manufacturer']=$res['manufacturer'];
						$arrOrderDet[$eye]['boxes']=$res['boxes'];
						$arrOrderDet[$eye]['disposable_id']=$res['disposable_id'];
						$arrOrderDet[$eye]['package_id']=$res['package_id'];
						$arrOrderDet[$eye]['supply_id']=$res['supplies_id'];
						$arrOrderDet[$eye]['ordered_data']=$res['ordered_data'];
						
						$patient_id=$res['patient_id'];
						$clws_id=$res['clws_id'];
						$comments=$res['comments'];
					}unset($rs);
				
					//GETTING DOCTOR INFO FOR CL RX
					if($clws_id>0){
						$qry="Select cl.provider_id, cl.dos, users.fname, users.mname, users.lname 
						FROM contactlensmaster cl LEFT JOIN users ON users.id=cl.provider_id WHERE cl.clws_id='".$clws_id."'";
						$rs=imw_query($qry);
						$res=imw_fetch_assoc($rs);
						$physician_id=$res['provider_id'];
						$physician_name=$res['lname'].', '.$res['fname'];
						$cl_dos=$res['dos'];
						unset($rs);
					}
					
					$manufac_od_id=$brand_od_id=$manufac_os_id=$brand_os_id=0;
					if(empty($arrOrderDet['OD']['manufacturer'])==false){
						$qry="Select id FROM in_manufacturer_details WHERE manufacturer_name='".$arrOrderDet['OD']['manufacturer']."' LIMIT 0,1";
						$rs=imw_query($qry);
						$res=imw_fetch_assoc($rs);
						$manufac_od_id=$res['id'];
						unset($rs);
					}
					if(empty($arrOrderDet['OD']['brand'])==false){
						$qry="Select id FROM in_contact_brand WHERE brand_name='".$arrOrderDet['OD']['brand']."' LIMIT 0,1";
						$rs=imw_query($qry);
						$res=imw_fetch_assoc($rs);
						$brand_od_id=$res['id'];
						unset($rs);
					}
					if(empty($arrOrderDet['OS']['manufacturer'])==false){
						$qry="Select id FROM in_manufacturer_details WHERE manufacturer_name='".$arrOrderDet['OS']['manufacturer']."' LIMIT 0,1";
						$rs=imw_query($qry);
						$res=imw_fetch_assoc($rs);
						$manufac_os_id=$res['id'];
						unset($rs);
					}
					if(empty($arrOrderDet['OS']['brand'])==false){
						$qry="Select id FROM in_contact_brand WHERE brand_name='".$arrOrderDet['OS']['brand']."' LIMIT 0,1";
						$rs=imw_query($qry);
						$res=imw_fetch_assoc($rs);
						$brand_os_id=$res['id'];
						unset($rs);
					}

					$itemArr = array();	
					
					//If OD brand and manufacturer id exits
					if($manufac_od_id>0 && $brand_od_id>0){
						$tmpArr = array();
						
						$qry="Select id, dx_code, upc_code, name, module_type_id, lab_id, retail_price, discount,
						discount_till, item_prac_code,manufacturer_id,brand_id FROM in_item WHERE manufacturer_id='".$manufac_od_id."' AND module_type_id='3' AND brand_id='".$brand_od_id."' 
						LIMIT 0,1";
						
						$rs=imw_query($qry);
						if(imw_num_rows($rs)>0){
							$tmpArr = imw_fetch_assoc($rs);
						}
						
						if(count($tmpArr) > 0 ) $itemArr['OD'] = $tmpArr;
					}
					
					//If OS brand and manufacturer id exits
					if($brand_os_id>0 && $manufac_os_id>0){
						$tmpArr = array();
						
						$qry="Select id, dx_code, upc_code, name, module_type_id, lab_id, retail_price, discount,
						discount_till, item_prac_code,manufacturer_id,brand_id FROM in_item WHERE manufacturer_id='".$manufac_os_id."' AND module_type_id='3' AND brand_id='".$brand_os_id."' 
						LIMIT 0,1";
						
						$rs=imw_query($qry);
						if(imw_num_rows($rs)>0){
							$tmpArr = imw_fetch_assoc($rs);
						}
						
						if(count($tmpArr) > 0 ) $itemArr['OS'] = $tmpArr;
					}
					
					//If Something is there in itemArr
					if(count($itemArr) > 0){
						// INSERT ORDER
						$order_id = 0;
						$totQty = $arrOrderDet['OD']['boxes'] + $arrOrderDet['OS']['boxes'];
						$totPrice = (($arrOrderDet['OD']['boxes'] * $itemArr['OD']['retail_price']) + ($arrOrderDet['OS']['boxes'] * $itemArr['OS']['retail_price']));
						$curDate = date('Y-m-d');
						$curTime = date('H:i:s');
						
						$qry="Insert INTO in_order SET 
						patient_id='".$patient_id."',
						total_qty='".$totQty."',
						total_price='".$totPrice."',
						grand_total='".$totPrice."',
						entered_date='".$curDate."',
						entered_time='".$curTime."',
						operator_id='".$_SESSION['authId']."',
						modified_date='".$curDate."',
						modified_time='".$curTime."',
						modified_by='".$_SESSION['authId']."',
						order_status='pending',
						comment='".addslashes($comments)."',
						iportal_cl_order_id='".$orderNum."',
						loc_id='".$loc_id."'";
						$rs = imw_query($qry);
						$order_id = imw_insert_id();
						
						//INSERT ORDER DETAILS
						$order_detail_id = 0;
						if($order_id > 0){
							$qry="Insert INTO in_order_details SET 
							order_id='".$order_id."',
							patient_id='".$patient_id."',
							item_id='".$itemArr['OD']['id']."',
							item_id_os='".$itemArr['OS']['id']."',
							item_prac_code='".$itemArr['OD']['item_prac_code']."',
							item_prac_code_os='".$itemArr['OS']['item_prac_code']."',
							dx_code='".$itemArr['OD']['dx_code']."',
							upc_code='".$itemArr['OD']['upc_code']."',
							upc_code_os='".$itemArr['OS']['upc_code']."',
							item_name='".$itemArr['OD']['item_name']."',
							item_name_os='".$itemArr['OS']['item_name']."',
							manufacturer_id='".$itemArr['OD']['manufacturer_id']."',
							brand_id='".$itemArr['OD']['brand_id']."',
							contact_cat_id	='".$contact_cat_id."',
							module_type_id='3',
							contact_bc_od='".$item_bc."',
							contact_diameter_od='".$item_diameter."',
							contact_bc_os='',
							contact_diameter_os='',
							cl_packaging_id='".$arrOrderDet['OD']['package_id']."',
							cl_wear_sch_id='".$arrOrderDet['OD']['disposable_id']."',
							supply_id='".$arrOrderDet['OD']['supply_id']."',
							qty='".$arrOrderDet['OS']['boxes']."',
							qty_right='".$arrOrderDet['OD']['boxes']."',
							price='".$itemArr['OD']['retail_price']."',
							price_os='".$itemArr['OS']['retail_price']."',
							price_retail='".$itemArr['OD']['retail_price']."',
							price_retail_os='".$itemArr['OS']['retail_price']."',
							discount='".$itemArr['OD']['discount']."',
							discount_os='".$itemArr['OS']['discount']."',
							total_amount='".($arrOrderDet['OD']['boxes'] * $itemArr['OD']['retail_price'])."',
							total_amount_os='".($arrOrderDet['OS']['boxes'] * $itemArr['OS']['retail_price'])."',
							pt_resp='".$totPrice."',
							manufacturer_id_os='".$itemArr['OS']['manufacturer_id']."',
							brand_id_os='".$itemArr['OS']['manufacturer_id']."',
							entered_date='".$curDate."',
							entered_time='".$curTime."',
							operator_id='".$_SESSION['authId']."',
							modified_date='".$curDate."',
							modified_time='".$curTime."',
							modified_by='".$_SESSION['authId']."',
							order_status='pending',
							loc_id='".$loc_id."'";
							$rs = imw_query($qry);
							$order_detail_id = imw_insert_id();
						}
						
						if($order_detail_id > 0){
							$arrRxOD=unserialize($arrOrderDet['OD']['ordered_data']);
							$arrRxOS=unserialize($arrOrderDet['OS']['ordered_data']);
							
							$rx_make_od = $arrOrderDet['OD']['manufacturer'].' - '.$arrOrderDet['OD']['brand'].' - '.$arrOrderDet['OD']['type'];
							$rx_make_os = $arrOrderDet['OS']['manufacturer'].' - '.$arrOrderDet['OS']['brand'].' - '.$arrOrderDet['OS']['type'];
							
							//INSERTING RX
							$qry="Insert INTO in_cl_prescriptions SET 
							patient_id='".$patient_id."',
							order_id='".$order_id."',
							det_order_id='".$order_detail_id."',
							physician_id='".$physician_id."',
							physician_name='".$physician_name."',
							operator_id='".$_SESSION['authId']."',
							sphere_od='".$arrRxOD['sphere']."',
							cylinder_od='".$arrRxOD['cylinder']."',
							axis_od='".$arrRxOD['axis']."',
							base_od='".$arrRxOD['bc']."',
							diameter_od='".$arrRxOD['diameter']."',
							sphere_os='".$arrRxOS['sphere']."',
							cylinder_os='".$arrRxOS['cylinder']."',
							axis_os='".$arrRxOS['axis']."',
							base_os='".$arrRxOS['bc']."',
							diameter_os='".$arrRxOS['diameter']."',
							date_added='".$curDate."',
							entered_date='".$curDate."',
							entered_time='".$curTime."',
							entered_by='".$_SESSION['authId']."',
							rx_dos='".$cl_dos."',
							rx_make_od='".$rx_make_od."',
							rx_make_os='".$rx_make_os."'";
							
							$rs=imw_query($qry);
							
							if(count($itemArr) > 0){
								foreach($itemArr as $obj){
									if(count($obj) > 0){
										//ORDER STATUS TABLE
										$qry="Insert INTO in_order_detail_status SET 
										patient_id='".$patient_id."',
										item_id='".$obj['id']."',
										order_id='".$order_id."',
										order_detail_id='".$order_detail_id."',
										order_qty='".$totQty."',
										order_status='pending',
										order_date='".$curDate."',
										order_time='".$curTime."',
										operator_id='".$_SESSION['authId']."'";
										$rs=imw_query($qry);
										
										//ORDER FACILITY TABLE
										$qry="Insert INTO in_order_fac SET 
										patient_id='".$patient_id."',
										item_id='".$obj['id']."',
										order_id='".$order_id."',
										order_det_id='".$order_detail_id."',
										facility_id='".$_SESSION['login_facility']."',
										loc_id='".$loc_id."',
										qty='".$totQty."',
										entered_date='".$curDate."',
										entered_time='".$curTime."',
										entered_by='".$_SESSION['authId']."'";
										$rs=imw_query($qry);
										
									}
								}
							}
							
							
							

							

							//UPDATING PATIENT-PORTAL ORDER TABLE							
							$qry="Update iportal_req_orders SET is_approved='1', operator_id='".$_SESSION['authId']."',
							optical_order_id='".$order_id."' WHERE temp_order_num='".$orderNum."'";
							$rs=imw_query($qry);
							if(!$rs){
								$arrResult[$orderNum] = 'error';
							}else{
								$arrResult[$orderNum] = 'success';

								// Audit trail for Patient Portal - Contact Lense Order Approval
								$logged_provider_id = $_SESSION['authId'];
								$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
								if($policyStatus == 1){
									$ip = getRealIpAddr();
									$URL = $_SERVER['PHP_SELF'];													 
									$os = getOS();
									$browserInfoArr = array();
									$browserInfoArr = _browser();
									$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
									$browserName = str_replace(";","",$browserInfo);
									$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);		

									$data_arr=array();
									$data_arr["Pk_Id"] = $_SESSION['patient'];
									$data_arr["Operater_Id"] = $logged_provider_id;
									$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
									$data_arr["IP"] = $ip;
									$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
									$data_arr["URL"] = $URL;
									$data_arr["Browser_Type"] = $browserName;
									$data_arr["OS"] = $os;
									$data_arr["Machine_Name"] = $machineName;
									$data_arr["Category"] = "patient_info";
									$data_arr["Category_Desc"] = "";
									$data_arr["Action"] = "approve";
									$data_arr["Date_Time"] = date('Y-m-d H:i:s');
									$data_arr["pid"] = $_SESSION['patient']; 
									// pre($data_arr);
									AddRecords($data_arr,'audit_trail');
								}	
							}
						}
					}
				}
			}
		}
	}
}

echo json_encode(array('arrResult'=>$arrResult));
?>