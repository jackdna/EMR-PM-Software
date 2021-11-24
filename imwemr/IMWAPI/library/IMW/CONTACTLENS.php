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

namespace IMW;

include_once($GLOBALS['fileroot']."/iportal_config/vocabulary.php");

use DateTime;
use DateInterval;
use DatePeriod;

/**
 * CONTACTLENS
 *
 * Main Contact Lens Class
 */
class CONTACTLENS
{
	public $dbh_obj;
	public $service_obj;
	public $currentDate;
	public $patientID;
	public $masterData;
	
	public function __construct($db_obj = '', $service_obj = '', $patientId = ''){
		
		$this->currentDate = strtotime(date('Y-m-d'));
		
		if(empty($db_obj) == false){
			$this->dbh_obj = $db_obj;
		}
		
		if(empty($service_obj) == false){
			$this->service_obj = $service_obj;
		}
		
		if(empty($patientId) == false){
			$this->patientID = $patientId;
		}
		
		//Load all the required data
		$this->masterData = $this->getMasterData($this->patientID);
	}
	
	//Returns Master Data for contact lens
	public function getMasterData($patientId = ''){
		if(empty($patientId)) return false;
		$returnArr = array();
		$dbh = $this->dbh_obj;
		
		//Getting Patient Info and POS Facility Address
		$chkDetail = $dbh->imw_query("
			SELECT 
				pd.id,
				pd.fname,
				pd.mname,
				pd.lname,
				pd.street,
				pd.city,
				pd.state,
				pd.postal_code,
				pd.zip_ext,
				pd.default_facility,
				pos_fac.pos_facility_address, 
				pos_fac.pos_facility_city, 
				pos_fac.pos_facility_state, 
				pos_fac.pos_facility_zip,
				pos_fac.zip_ext as 'pos_zip_ext'
			FROM 
				patient_data pd 
				LEFT JOIN pos_facilityies_tbl pos_fac ON pos_fac.pos_facility_id= pd.default_facility 
			WHERE 
				pd.id = '".$patientId."'
		");
		
		if($chkDetail && $dbh->imw_num_rows($chkDetail) > 0){
			$rowDetail = $dbh->imw_fetch_assoc($chkDetail);
			//while(){
				//Patient Address
				$patient_addrowDetails = $rowDetail['fname'].' '.$rowDetail['lname'].' - '.$rowDetail['id'].',\n';
				$patient_addrowDetails.= trim(stripslashes($rowDetail['street'])).'\n';
				$patient_addrowDetails.= trim(stripslashes($rowDetail['city'].", ".$rowDetail['state']." ".$rowDetail['postal_code']));
				$patient_addrowDetails.= (empty($rowDetail['zip_ext'])==false) ? '-'.$rowDetail['zip_ext'] : '';	
				$returnArr['patientAddress'] = $patient_addrowDetails;
				
				//Facility Address
				if($rowDetail['default_facility']>0){
					$facility_address.= trim(stripslashes($rowDetail['pos_facility_address'])).'\n';
					$facility_address.= trim(stripslashes($rowDetail['pos_facility_city'].", ".$rowDetail['pos_facility_state']." ".$rowDetail['pos_facility_zip']));
					$facility_address.= (empty($rowDetail['pos_zip_ext'])==false) ? '-'.$rowDetail['pos_zip_ext'] : '';	
					$returnArr['facilityAddress'] = $facility_address;
				}else{
					//GETTING LAST APPOINTMENT FACILITY ADDRESS
					$qryLastFac = "Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$patientId."' ORDER BY id DESC limit 0,1";
					$rsLastFac = $dbh->imw_query($qryLastFac);
					$resFac = $dbh->imw_fetch_assoc($rsLastFac);
					$facilityId = $resFac['sa_facility_id'];
					unset($resFac);
				
					if($facility_id > 0){
						$qry = "Select pos_fac.pos_facility_address, pos_fac.pos_facility_city, pos_fac.pos_facility_state, pos_fac.pos_facility_zip,
						pos_fac.zip_ext as 'pos_zip_ext' FROM facility 
						JOIN pos_facilityies_tbl pos_fac ON pos_fac.pos_facility_id = facility.fac_prac_code 
						WHERE facility.id='".$facilityId."'";
						$rs = $dbh->imw_query($qry);
						$res = $dbh->imw_fetch_assoc($rs);
						$facility_address.=trim(stripslashes($res['pos_facility_address'])).'\n';
						$facility_address.=trim(stripslashes($res['pos_facility_city'].", ".$res['pos_facility_state']." ".$res['pos_facility_zip']));
						$facility_address.=(empty($res['pos_zip_ext'])==false) ? '-'.$res['pos_zip_ext'] : '';
						$returnArr['facilityAddress'] = $facility_address;
					}
				}
			//}
		}
		
		//Get Last CL Sheet ID
		$getCLId = "Select clws_id FROM contactlensmaster WHERE patient_id='".$patientId."' ORDER BY clws_id DESC LIMIT 0,1";
		$rsClId = $dbh->imw_query($getCLId);
		$resId = $dbh->imw_fetch_assoc($rsClId);
		$clws_id = $resId['clws_id'];
		$returnArr['clId'] = $clws_id;
		
		//Get Disposable Data
		$disposeDt = "Select id, cat_name FROM in_contact_cat WHERE del_status='0' order by cat_name asc";
		$disposeRes = $dbh->imw_query($disposeDt);
		while($disposeRow = $dbh->imw_fetch_assoc($disposeRes)){
			$returnArr['DisposableData'][$disposeRow['id']] = strtolower($disposeRow['cat_name']);
		}
		
		//Get Supply Data
		$supplyQry = "Select id, supply_name FROM in_supply WHERE del_status='0' order by supply_name asc";
		$resSupply = $dbh->imw_query($supplyQry);
		while($supplyRow = $dbh->imw_fetch_assoc($resSupply)){
			$returnArr['SupplyData'][$supplyRow['id']] = strtolower($supplyRow['supply_name']);
		}
		
		//Get Packages Data
		$arrAllPackages = array();
		$packageQry = "Select in_options.id, in_options.opt_val, in_options.opt_sub_type, in_contact_cat.cat_name, in_contact_cat.id as catID FROM in_options 
		JOIN in_contact_cat ON in_contact_cat.id= in_options.opt_sub_type 
		WHERE in_options.opt_type='5' and in_options.module_id='3' and in_options.del_status='0' 
		order by CAST(in_options.opt_val AS UNSIGNED) asc";
		$packageRes = $dbh->imw_query($packageQry);
		while($packageRow = $dbh->imw_fetch_assoc($packageRes)){
			if(strtolower($packageRow['cat_name'])=='daily'){
				$arrAllPackages[$packageRow['catID']][$packageRow['id']] = strtolower($packageRow['opt_val']);
			}else if(strtolower($packageRow['cat_name'])=='weekly'){
				$arrAllPackages[$packageRow['catID']][$packageRow['id']] = strtolower($packageRow['opt_val']);
			}else if(strtolower($packageRow['cat_name'])=='monthly'){
				$arrAllPackages[$packageRow['catID']][$packageRow['id']] = strtolower($packageRow['opt_val']);
			}
		}
		$returnArr['PackageData'] = $arrAllPackages;
		
		//Get Manufacturers
		$arrAllMake = array();
		$manuFacturerQry = "Select make_id, manufacturer, style, type FROM contactlensemake";
		$ManuRes = $dbh->imw_query($manuFacturerQry);
		while($ManuRow = $dbh->imw_fetch_assoc($ManuRes)){
			$id = $ManuRow['make_id'];
			$arrAllMake[$id]['brand'] = $ManuRow['style'];
			$arrAllMake[$id]['manufacturer'] = $ManuRow['manufacturer'];
			$arrAllMake[$id]['type'] = $ManuRow['type'];
		}
		$returnArr['ManufacturersData'] = $arrAllMake;
		
		return $returnArr;
	}
	
	//Returns Patient Last Contact Lens Rx
	public function getLastRx( $clId = ''){
		if(empty($clId)) return false;
		
		$dbh = $this->dbh_obj;
		$returnArr = array();
		
		//Get Last RX details
		$chkQry = $dbh->imw_query("
			Select 
				clm.clws_id, 
				clm.dos, 
				cldet.clType, 
				cldet.clEye, 
				cldet.SclsphereOD, 
				cldet.SclCylinderOD, 
				cldet.SclaxisOD, 
				cldet.SclBcurveOD, 
				cldet.SclDiameterOD, 
				cldet.SclTypeOD_ID, 
				cldet.SclsphereOS, 
				cldet.SclCylinderOS, 
				cldet.SclaxisOS, 
				cldet.SclBcurveOS, 
				cldet.SclDiameterOS, 
				cldet.SclTypeOS_ID,
				cldet.RgpPowerOD, 
				cldet.RgpBCOD, 
				cldet.RgpDiameterOD, 
				cldet.RgpTypeOD_ID, 
				cldet.RgpPowerOS, 
				cldet.RgpBCOS, 
				cldet.RgpDiameterOS, 
				cldet.RgpTypeOS_ID,
				cldet.RgpCustomPowerOD, 
				cldet.RgpCustomBCOD, 
				cldet.RgpCustomDiameterOD, 
				cldet.RgpCustomTypeOD_ID, 
				cldet.RgpCustomPowerOS, 
				cldet.RgpCustomBCOS, 
				cldet.RgpCustomDiameterOS, 
				cldet.RgpCustomTypeOS_ID 
		FROM 
			contactlensmaster clm 
			JOIN contactlensworksheet_det cldet ON cldet.clws_id = clm.clws_id 
		WHERE 
			clm.clws_id='".$clId."' AND 
			clm.del_status='0' 
		ORDER BY 
			cldet.clEye, cldet.id DESC");
			
		if($chkQry && $dbh->imw_num_rows($chkQry) > 0){
			while($contactRow = $dbh->imw_fetch_assoc($chkQry)){
				$site = $contactRow['clEye'];
				
				//If Site is OD / OS
				switch(strtoupper($site)){
					case 'OD':
						$returnArr[$contactRow['dos']][$site][] = $this->getLensTypeData($contactRow, $site);
					break;
					
					case 'OS':
						$returnArr[$contactRow['dos']][$site][] = $this->getLensTypeData($contactRow, $site);
					break;
				}
			}
		}	
		
		return $returnArr;
	}
	
	//Returns Filtered Data for the provided contact lens site
	public function getLensTypeData($arrData = array(), $site = ''){
		if(count($arrData) == 0 || empty($site)) return ;
		$returnArr = array();
		$typeId = '';
		
		$mapArr = array(
			'SCL' => array(
				'sphere' => 'Sclsphere'.$site,
				'cylinder' => 'SclCylinder'.$site,
				'bc' => 'SclBcurve'.$site,
				'diameter' => 'SclDiameter'.$site,
				'axis' => 'Sclaxis'.$site
			),
			'RGP' => array(
				'sphere' => 'RgpPower'.$site,
				'cylinder' => '',
				'bc' => 'RgpBC'.$site,
				'diameter' => 'RgpDiameter'.$site,
				'axis' => ''
			),
			'CUST_RGP' => array(
				'sphere' => 'RgpCustomPower'.$site,
				'cylinder' => '',
				'bc' => 'RgpCustomBC'.$site,
				'diameter' => 'RgpCustomDiameter'.$site,
				'axis' => ''
			)
		);
		
		$lensType = (isset($arrData['clType']) && empty($arrData['clType']) == false) ? strtoupper($arrData['clType']) : '';
		
		if(empty($lensType) == false){
			switch($lensType){
				case 'SCL':
					$typeId = $arrData['SclType'.$site.'_ID'];
					foreach($mapArr[$lensType] as $key => &$val){
						$returnArr[$key] = (isset($arrData[$val]) && empty($val) == false) ? $arrData[$val] : '';
					}
				break;
				
				case 'RGP':
					$typeId = $arrData['RgpType'.$site.'_ID'];
					foreach($mapArr[$lensType] as $key => &$val){
						$returnArr[$key] = (isset($arrData[$val]) && empty($val) == false) ? $arrData[$val] : '';
					}
				break;
				
				case 'CUST_RGP':
					$typeId = $arrData['RgpCustomType'.$site.'_ID'];
					foreach($mapArr[$lensType] as $key => &$val){
						$returnArr[$key] = (isset($arrData[$val]) && empty($val) == false) ? $arrData[$val] : '';
					}
				break;	
			}
		}
		
		if(count($returnArr) > 0 && empty($typeId) == false){
			$returnArr['brand'] = $this->masterData['ManufacturersData'][$typeId]['brand'];
			$returnArr['manufacturer'] = $this->masterData['ManufacturersData'][$typeId]['manufacturer'];
			$returnArr['type'] = $this->masterData['ManufacturersData'][$typeId]['type'];
		}
		
		return $returnArr;
	}
	
	//Return Patient past contact lens orders
	public function getPtOrders($patientId = '', $startDate = '', $endDate = ''){
		if(empty($patientId)) return false;
		$where = '';
		$returnData = array();
		
		if(empty($endDate) && empty($startDate) == false) $endDate = $startDate;
		
		if(empty($startDate) == false && empty($endDate) == false){
			$where = " AND DATE(ordered_date) BETWEEN '".$startDate."' AND '".$endDate."' ";
		}
		
		//Get Patient Orders
		$chkQry = $this->dbh_obj->imw_query("
			Select 
				clws_id as clId,
				eye as Site,
				brand as Brand,
				manufacturer as Manufacturer,
				disposable as Disposal,
				package as Package,
				supplies as Supplies,
				boxes as Boxes,
				DATE(ordered_date) as OrderDate,
				ship_to as ShippingTo,
				shipping_address as ShippingAddress,
				comments as Comments,
				CASE is_approved
					WHEN 1 THEN 'Approved'
					WHEN 2 THEN 'Declined'
					ELSE 'Pending Approval'
				END as isApproved
			FROM 
				iportal_req_orders 
			WHERE 
				patient_id = '".$patientId."' AND 
				order_for = 'cl'
				".$where."
			ORDER BY 
				id DESC
		");
		
		if($chkQry && $this->dbh_obj->imw_num_rows($chkQry) > 0){
			while($rowData = $this->dbh_obj->imw_fetch_assoc($chkQry)){
				$rowData['Supplies'] = ($rowData['Supplies'] == 12) ? '1 Year' : $rowData['Supplies'].' Month';
				$returnData[$rowData['OrderDate']][$rowData['Site']][] = $rowData;
			}
		}
		
		return $returnData;
	}
	
	public function calculateBoxes($disposableId = '', $lenPkgId = '', $supplyId = ''){
		if(empty($disposableId) || empty($lenPkgId) || empty($supplyId)) return false;
		$supplyValue = $boxes = '';
		
		$disposeVal = $this->masterData['DisposableData'][$disposableId];
		$supplyVal = $this->masterData['SupplyData'][$supplyId];
		$packageVal = preg_replace('/\D/', '', $this->masterData['PackageData'][$disposableId][$lenPkgId]);
		
		switch($supplyVal){
			case '1 month':
			case 'month':
				$supplyValue = 1;
			break;	
			
			case '3 months':
			case '3 month':
				$supplyValue = 3;
			break;	
			
			case '6 months':
			case '6 month':
				$supplyValue = 6;
			break;	
			
			case '1 year':
			case 'year':
				$supplyValue = 12;
			break;	
		}
		
		//if(empty($supplyValue) == false){
			//Calculate Boxes based on disposeVal
			switch($disposeVal){
				case 'daily':
					$boxes = round(( 30 * $supplyValue) / $packageVal);
					if($boxes <= 0) $boxes = 1;
				break;
				
				case 'weekly':
					$weeks = (30 * $supplyValue) / 7;
					$boxes = round( $weeks / $packageVal );
					if($boxes <= 0) $boxes = 1;
				break;

				case 'monthly':
					$boxes = round((30 * $supplyValue) / ($packageVal * 30));
					if($boxes <= 0) $boxes = 1;
				break;	
			}
		//};
		
		return $boxes;
	}
	
	public function orderContactLens($site = 0, $disposeId = '', $lensPkgId = '', $suppliesId = '', $Boxes = '', $shipType = '', $shipAddress = '', $comments = ''){
		if(empty($disposeId) || empty($lensPkgId) || empty($suppliesId) || empty($Boxes) || empty($shipType) || empty($shipAddress)) return false;
		$site = (empty($site) == false) ? $site : 0;
		$loopArr = $lastRx = array();
		$siteArr = array(0 => array('OD', 'OS'), 1 => array('OD'), 2 => array('OS')); //Runs Loop based on Site provided
		$shipTo = array(1 => 'home', 2 => 'facility');
		
		//Fields required in saving values in DB
		$mapArr = array(
			'temp_order_num' => time().$this->masterData["clId"],
			'order_for' => 'cl',
			'type' => '',
			'brand' => '',
			'manufacturer' => '',
			'sphere' => '',
			'cylinder' => '',
			'bc' => '',
			'diameter' => '',
			'axis' => '',
			'disposable_id' => $disposeId,
			'disposable' => $this->masterData['DisposableData'][$disposeId],
			'package_id' => $lensPkgId,
			'package' => $this->masterData['PackageData'][$disposeId][$lensPkgId],
			'supplies_id' => $suppliesId,
			'supplies' => $this->masterData['SupplyData'][$suppliesId],
			'boxes' => $Boxes,
			'ship_to' => $shipTo[$shipType],
			'shipping_address' => $shipAddress,
			'comments' => $comments,
			'clws_id' => $this->masterData['clId'],
			'eye' => '___EYE___',
		);
		
		//print $site.'----'.$disposeId.'-----'.$lensPkgId.'------'.$suppliesId.'-----'.$Boxes.'-----'.$shipType.'-----'.$shipAddress;
		
		//Set Queries based on Site
		$loopArr = $siteArr[$site];
		
		if(count($loopArr) > 0){
			foreach($loopArr as $siteKey => &$siteVal){
				foreach($mapArr as $key => &$val){
					$val = str_ireplace('___EYE___', $siteVal, $val);
					print $key.'----'.$val.'~~~~~~~~';
				}
			}
		}
		
		
		
		
		die;
	}
	
}