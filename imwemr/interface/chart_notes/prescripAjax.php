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
include_once('../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/cl_functions.php'); 
function get_query_array($qry){
	$return = array();
	$sql_qry = imw_query($qry);
	while($Row = imw_fetch_array($sql_qry)){
		$return[] = $Row;
	}
	return $return;
}
//GET ALL LENS MANUFACTURER IN ARRAY
$arrLensManuf = getLensManufacturer();
//GET FEE OF DEFAULT CL CHARGES
$arrDefaultCPTFee = getCPTDefaultCharges();

ob_start();

$callFrom = $_REQUEST['callFrom'];

$authUserID = $_SESSION['authUserID'];

//START CODE TO GET AUTHORIZATION NUMBER
$unusedAuthorization='';
$authInfoQry = "
SELECT patient_auth.auth_name 
FROM patient_auth,insurance_data
WHERE insurance_data.pid='".$_SESSION['patient']."'
AND insurance_data.type='primary'
AND insurance_data.auth_required='Yes'
AND insurance_data.id=patient_auth.ins_data_id
ORDER BY patient_auth.a_id DESC 
";
$authInfoRes = imw_query($authInfoQry) or die(imw_error());				
$authInfoNumRow = imw_num_rows($authInfoRes);
if($authInfoNumRow<=0) {
	$authInfoQry = "
	SELECT patient_auth.auth_name 
	FROM patient_auth,insurance_data
	WHERE insurance_data.pid='".$_SESSION['patient']."'
	AND insurance_data.type='secondary'
	AND insurance_data.auth_required='Yes'
	AND insurance_data.id=patient_auth.ins_data_id
	ORDER BY patient_auth.a_id DESC 
	";
	$authInfoRes = imw_query($authInfoQry) or die(imw_error());			
	$authInfoNumRow = imw_num_rows($authInfoRes);
}

if($authInfoNumRow>0) {
	$authInfoRow = imw_fetch_array($authInfoRes);
	$unusedAuthorization = $authInfoRow['auth_name'];
}	
$authStyleVisible='hidden';
if($unusedAuthorization){
	$authStyleVisible='visible';
}
//END CODE TO GET AUTHORIZATION NUMBER


// GET LENSE CODES AND COLORS in ARRAY
$arrLensCode	=	getLensCodeArr();
$arrLensColor	=	getLensColorArr();
//---------------------------------


$GetTypeManufacQuery= "SELECT contactlensmaster.*, contactlensworksheet_det.* FROM contactlensmaster LEFT JOIN contactlensworksheet_det ON contactlensworksheet_det.clws_id = contactlensmaster.clws_id WHERE contactlensmaster.patient_id='".$_SESSION['patient']."' AND contactlensmaster.clws_id='".$_REQUEST['clws_id']."' ORDER BY contactlensworksheet_det.id";
$GetTypeManufacRow = get_query_array($GetTypeManufacQuery);

$clws_type = $GetTypeManufacRow[0]['clws_type'];
$clws_charges_id = $GetTypeManufacRow[0]['charges_id'];

$GetTypeManufacNumRow 	= sizeof($GetTypeManufacRow);
$typeManufac='';
$commaSepManuFac='';
if($GetTypeManufacNumRow>0){
	$oldClType = '';	$j=1;
	$typeManufac='';
	$commaSepManuFac='';
	$LensBoxOD= array();
	$LensBoxOS= array();
	$PriceOD= array();
	$PriceOS= array();
	$clwsid_ArrOD = array();
	$clwsid_ArrOS = array();
?>
<div class="row pt10">
	<div class="col-sm-12 purple_bar">
		<label class="la_bg">Prescription</label>	
	</div>
	
	<?php	
	for($i=0;$i < $GetTypeManufacNumRow; $i++){
		$id= $GetTypeManufacRow[$i]['id'];
		$clGrp				= trim(stripslashes($GetTypeManufacRow[$i]['clGrp']));
		$clEye 	  			= trim(stripslashes($GetTypeManufacRow[$i]['clEye']));
		
		$dispTitle =0;	$topPad='';	
		$odos = strtolower($GetTypeManufacRow[$i]['clEye']);

		$clType = $GetTypeManufacRow[$i]['clType'];
		if($clType != $oldClType) { 
			$dispTitle=1;	//$topPad="padding-top:17px"; 
			$divHeight = "45px"; $rgpHeight = "48px";
		}else {
			$divHeight = "30px"; $rgpHeight = "30px";
		}
		
		// -------GET TYPE AND TYPE & PRICE FOR FURTHER ROWS--------------
		$typeOD = $typeOS = $typeOU ='';
		$priceOD = $priceOS =$priceOU = '';
		$commaSepManuFac = '';
		$adminStylOD = $adminManufacOD = $adminStylOS = $adminManufacOS = $adminStylOU = $adminManufacOU ='';
		
		$clID = $GetTypeManufacRow[$i]['id'];
		 
		if($clType=='scl'){
			$typeOD_ID = $GetTypeManufacRow[$i]['SclTypeOD_ID'];
			$typeOS_ID = $GetTypeManufacRow[$i]['SclTypeOS_ID'];
			$typeOD	  = trim(stripslashes($arrLensManuf[$typeOD_ID]['det']));
			$typeOS	  = trim(stripslashes($arrLensManuf[$typeOS_ID]['det']));
			$cpt_fee_id = $arrLensManuf[$typeOD_ID]['cpt_fee_id'];
			$priceOD  = $arrDefaultCPTFee[$cpt_fee_id];
			$cpt_fee_id = $arrLensManuf[$typeOS_ID]['cpt_fee_id'];
			$priceOS  = $arrDefaultCPTFee[$cpt_fee_id];
		}elseif($clType=='rgp'){
			$typeOD_ID = $GetTypeManufacRow[$i]['RgpTypeOD_ID'];
			$typeOS_ID = $GetTypeManufacRow[$i]['RgpTypeOS_ID'];
			$typeOD	  = trim(stripslashes($arrLensManuf[$typeOD_ID]['det']));
			$typeOS	  = trim(stripslashes($arrLensManuf[$typeOS_ID]['det']));
			$cpt_fee_id = $arrLensManuf[$typeOD_ID]['cpt_fee_id'];
			$priceOD  = $arrDefaultCPTFee[$cpt_fee_id];
			$cpt_fee_id = $arrLensManuf[$typeOS_ID]['cpt_fee_id'];
			$priceOS  = $arrDefaultCPTFee[$cpt_fee_id];
		}elseif($clType=='cust_rgp'){
			$typeOD_ID = $GetTypeManufacRow[$i]['RgpCustomTypeOD_ID'];
			$typeOS_ID = $GetTypeManufacRow[$i]['RgpCustomTypeOS_ID'];
			$typeOD	  = trim(stripslashes($arrLensManuf[$typeOD_ID]['det']));
			$typeOS	  = trim(stripslashes($arrLensManuf[$typeOS_ID]['det']));
			$cpt_fee_id = $arrLensManuf[$typeOD_ID]['cpt_fee_id'];
			$priceOD  = $arrDefaultCPTFee[$cpt_fee_id];
			$cpt_fee_id = $arrLensManuf[$typeOS_ID]['cpt_fee_id'];
			$priceOS  = $arrDefaultCPTFee[$cpt_fee_id];
		}
		
		if($typeOD && $typeOS) {
			$commaSepManuFac = ', ';
		}
		
		if($typeOD != $typeOS)
		{
			if($typeOD!='' && ($clGrp=='OU' || $clGrp=='OD')) {
				$typeManufac.='OD - '.$typeOD;
				//if(!(in_array($typeOD, $LensBoxOD))){
						$LensBoxOD[]=$typeOD;
						$PriceOD[] = $priceOD;
						$arrPrintOD[]['LensBoxOD_ID']= $typeOD_ID;
						$clwsid_ArrOD[] = $clID;
				//}
			}
			$typeManufac.=$commaSepManuFac;
	
			if($typeOS!='' && ($clGrp=='OU' || $clGrp=='OS')) {
				$typeManufac.='OS - '.$typeOS;
				//if(!(in_array($typeOS, $LensBoxOS))){
						$LensBoxOS[]=$typeOS;
						$PriceOS[] = $priceOS;
						$arrPrintOS[]['LensBoxOS_ID']= $typeOS_ID;
						$clwsid_ArrOS[] = $clID;
				//}
			}
			$typeManufac.=$commaSepManuFac;
		}
		
		if($typeOD==$typeOS && ($typeOD!='' || $typeOS!='') && $clGrp=='OU') {
			$typeManufac.='OD - '.$typeOD;
				if($typeOS!=''){
					$LensBoxOS[]=$typeOS;
					$PriceOS[] = $priceOS;
					$arrPrintOS[]['LensBoxOS_ID']= $typeOS_ID;
					$clwsid_ArrOS[] = $clID;
				}
				//if(!(in_array($typeOD, $LensBoxOD)) && $typeOD!=''){
				if($typeOD!=''){
					$LensBoxOD[]=$typeOD;
					$PriceOD[] = $priceOD;
					$arrPrintOD[]['LensBoxOD_ID']= $typeOD_ID;
					$clwsid_ArrOD[] = $clID;
				}
		}

		$typeManufac.=" -- ";
		// ------------END TYPE ROWS------------------------------		
		
		if($clType =='scl') { ?>
		<div id="CLRow<?php echo $j;?>" class="col-sm-12 pt10" style="display:block; clear:both;" >
			<div class="row">
				<div class="col-sm-1 fl" style="<?php echo $topPad;?>">
					<strong>SCL</strong>
				</div>
				<div class="col-sm-11 fl">
					<table class="table table-condensed table-bordered">
					  <?php if($dispTitle==1) { ?>
						 <tr class="grythead">
							<th class="text-left">&nbsp;</th>
							<th class="text-nowrap">B. Curve</th> 
							<th>Diameter</th> 
							<th>Sphere</th>												
							<th>Cylinder</th>
							<th>Axis</th>  
							<th>ADD</th>
							<th>DVA</th>
							<th>NVA</th>
							<th>Type</th>
						</tr>
			<?php 	} ?>                 
			<?php	if($odos=='od') { ?>
						<tr>
							<td class="od"><label>&nbsp;OD</label></td> 
							<td>
								<input class="form-control" type="text" name="SclBcurveOD" value="<?php echo $GetTypeManufacRow[$i]['SclBcurveOD'];?>"  readonly>
							</td> 
							<td>
								<input  id="SclDiameterOD" type="text" class="form-control " name="SclDiameterOD" value="<?php echo $GetTypeManufacRow[$i]['SclDiameterOD'];?>" readonly>
							</td> 
							<td>
								<input type="text" name="sphere<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['SclsphereOD'];?>" class="form-control"  id="SclsphereOD"  onBlur="justify2Decimal(this)" readonly >
							</td>												
							<td>
								<input  id="SclCylinderOD" type="text" name="SclCylinderOD" value="<?php echo $GetTypeManufacRow[$i]['SclCylinderOD'];?>" class="form-control"  onBlur="justify2Decimal(this)" readonly >
							</td> 
							<td>
								<input type="text" name="SclaxisOD" value="<?php echo $GetTypeManufacRow[$i]['SclaxisOD'];?>" class="form-control"  id="SclaxisOD" readonly>
							</td> 
							<td>
								<input id="SclAddOD" type="text" class="form-control " name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['SclAddOD'];?>" readonly>
							</td>
							<td>
								<input id="SclDvaOD" type="text" name="SclDvaOD" value="<?php echo $GetTypeManufacRow[$i]['SclDvaOD']; ?>" class="form-control" readonly>
							</td>
							<td>
								<input type="text" name="SclNvaOD"  id="SclNvaOD" value="<?php echo $GetTypeManufacRow[$i]['SclNvaOD'];?>" class="form-control" readonly>
							</td>
							<td>
								<input class="form-control" type="text" name="SclTypeOD" id="SclTypeOD" value="<?php echo $GetTypeManufacRow[$i]['SclTypeOD'];?>" readonly>
							</td>
						</tr>
			<?php 	} if($odos=='os') {  ?>
						<tr>
							<td class="os"><label>&nbsp;OS</label></td>
							<td>
								<input  type="text" name="SclBcurveOS" value="<?php echo $GetTypeManufacRow[$i]['SclBcurveOS'];?>" class="form-control"  readonly>
							</td> 
							<td>
								<input  id="SclDiameterOS" type="text" class="form-control " name="SclDiameterOS" value="<?php echo $GetTypeManufacRow[$i]['SclDiameterOS'];?>" readonly>
							</td> 
							<td>
								<input type="text" name="sphere<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['SclsphereOS'];?>" class="form-control" id="SclsphereOS" onKeyUp="check2Blur(this,'S','SclCylinderOS');" onBlur="justify2Decimal(this)" readonly >
							</td> 
							<td>
								<input class="form-control" id="SclCylinderOS" type="text" name="SclCylinderOS" value="<?php echo $GetTypeManufacRow[$i]['SclCylinderOS'];?>" onkeyup="check2Blur(this,'C','SclaxisOS');" onBlur="justify2Decimal(this)" readonly>
							</td> 
							<td>
								<input type="text" name="SclaxisOS" value="<?php echo $GetTypeManufacRow[$i]['SclaxisOS'];?>" class="form-control" id="SclaxisOS" onKeyUp="check2Blur(this,'A','SclaxisOS');" readonly >
							</td> 
							<td>
								<input  id="SclAddOS" type="text" class="form-control " name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['SclAddOS'];?>" readonly>
							</td>
							<td>
								<input id="SclDvaOS" type="text" name="SclDvaOS" value="<?php echo $GetTypeManufacRow[$i]['SclDvaOS']; ?>" class="form-control" readonly>
							</td>
							<td>
								<input type="text" name="SclNvaOS"  id="SclNvaOS" value="<?php echo $GetTypeManufacRow[$i]['SclNvaOS'];?>" class="form-control"  readonly>
							</td>
							<td>
								<input class="form-control" type="text" name="SclTypeOS" id="SclTypeOS" value="<?php echo $GetTypeManufacRow[$i]['SclTypeOS'];?>" readonly>
							</td>
						</tr>
			<?php	} ?>  
					</table>
				</div>
			</div>	
		</div>
<?php	}else if($clType =='rgp') { ?>
			<div id="CLRow<?php echo $j;?>" class="col-sm-12 pt10" style="display:block; clear:both;" >
				<div class="row">
					<div class="col-sm-1 fl" style="<?php echo $topPad;?>">
						<strong>RGP</strong>
					</div>	
					<div class="col-sm-11 fl">
						<table class="table table-condensed table-bordered">
					<?php 	if($dispTitle==1) { ?>
								<tr class="grythead">
									<th class="od">&nbsp;</th> 
									<th>BC</th> 												
									<th>Diameter</th>
									<th class="text-nowrap">Power</th>
									<th>OZ</th>
									<th>CT</th>
									<th>Cylinder</th>
									<th>Axis</th> 
									<th>Color</th> 
									<th class="text-left">Add</th> 
									<th>DVA</th> 
									<th>NVA</th> 
									<th>Type</th>
								</tr>
					<?php 	}
							if($odos=='od') {	?>                 
								<tr id="rgpODRow" style="display:<?php echo $rgpSupplyDisplayOD;?>;">
									<td class="od"><label>&nbsp;OD</label></td> 
									<td>
										<input readonly class="form-control"  type="text" name="RgpBCOD" value="<?php echo $GetTypeManufacRow[$i]['RgpBCOD'];?>" >
									</td>												
									<td>
										<input readonly type="text" name="RgpDiameterOD" value="<?php echo $GetTypeManufacRow[$i]['RgpDiameterOD'];?>" class="form-control">
									</td> 
									<td>
										<input readonly class="form-control"  type="text" name="sphere<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpPowerOD'];?> " >
									</td>
									<td>
										<input readonly type="text" name="RgpOZOD" value="<?php echo $GetTypeManufacRow[$i]['RgpOZOD'];?>" class="form-control" />
									</td> 
									<td>
										<input readonly type="text" name="RgpCTOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCTOD'];?>" class="form-control" />
									</td> 
									<td>
										<input readonly type="text" name="RgpCylinderOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCylinderOD'];?>" class="form-control" />
									</td> 
									<td>
										<input readonly type="text" name="RgpAxisOD" value="<?php echo $GetTypeManufacRow[$i]['RgpAxisOD'];?>" class="form-control" />
									</td> 
									<td>
										<input readonly type="text" name="RgpColorOD"  id="RgpColorOD" value="<?php  echo $GetTypeManufacRow[$i]['RgpColorOD']; ?>" class="form-control" >
									</td>
									<td>
										<input readonly  id="RgpAddOD" type="text" class="form-control" name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpAddOD'];?>" >
									</td> 
									<td>
										<input readonly id="RgpDvaOD" type="text" name="RgpDvaOD" value="<?php if($GetTypeManufacRow[$i]['RgpDvaOD']) echo $GetTypeManufacRow[$i]['RgpDvaOD']; else echo '20/'; ?>" class="form-control">
									</td>
									<td>
										<input readonly type="text" name="RgpNvaOD"  id="RgpNvaOD" value="<?php if($GetTypeManufacRow[$i]['RgpNvaOD']) echo $GetTypeManufacRow[$i]['RgpNvaOD']; else echo '20/'; ?>" class="form-control" >
									</td>
									<td>
										<input readonly class="form-control" type="text" name="RgpTypeOD" value="<?php echo $GetTypeManufacRow[$i]['RgpTypeOD'];?>">
									</td> 
								</tr>
					<?php 	} if($odos=='os') { ?>
								<tr id="rgpOSRow" style="display:<?php echo $rgpSupplyDisplayOS;?>;">
									<td class="os"><label>&nbsp;OS</label></td>
									<td>
										<input readonly class="form-control" type="text" name="RgpBCOS" value="<?php echo $GetTypeManufacRow[$i]['RgpBCOS'];?>"></td>												
									<td>
										<input readonly   type="text" name="RgpDiameterOS" value="<?php echo $GetTypeManufacRow[$i]['RgpDiameterOS'];?>" class="form-control">
									</td> 
									<td>
										<input readonly class="form-control" type="text" name="sphere<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpPowerOS'];?>">
									</td>
									<td>
										<input readonly type="text" name="RgpOZOS" value="<?php echo $GetTypeManufacRow[$i]['RgpOZOS'];?>" class="form-control" />
									</td> 
									<td>
										<input readonly type="text" name="RgpCTOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCTOS'];?>" class="form-control" />
									</td> 
									<td>
										<input readonly type="text" name="RgpCylinderOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCylinderOS'];?>" class="form-control" />
									</td> 
									<td>
										<input readonly type="text" name="RgpAxisOS" value="<?php echo $GetTypeManufacRow[$i]['RgpAxisOS'];?>" class="form-control"/>
									</td> 
									<td>
										<input readonly type="text" name="RgpColorOS"  id="RgpColorOS" value="<?php echo $GetTypeManufacRow[$i]['RgpColorOS']; ?>" class="form-control" >
									</td>
									<td>
										<input readonly  id="RgpAddOS" type="text" class="form-control" name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpAddOS'];?>" >
									</td> 
									<td>
										<input readonly type="text" name="RgpDvaOS"  id="RgpDvaOS" value="<?php if($GetTypeManufacRow[$i]['RgpDvaOS']) echo $GetTypeManufacRow[$i]['RgpDvaOS']; else echo '20/'; ?>" class="form-control" >
									</td>
									<td>
										<input readonly type="text" name="RgpNvaOS"  id="RgpNvaOS" value="<?php if($GetTypeManufacRow[$i]['RgpNvaOS']) echo $GetTypeManufacRow[$i]['RgpNvaOS']; else echo '20/'; ?>" class="form-control" >
									</td>
									<td>
										<input readonly class="form-control" type="text" name="RgpTypeOS" value="<?php echo $GetTypeManufacRow[$i]['RgpTypeOS'];?>">
									</td> 
								</tr>
					<?php 	} 	?>            
						</table>	
					</div>	
				</div>	
			</div> 
<?php	}else if($clType =='cust_rgp'){ ?>
			<div id="CLRow<?php echo $j;?>" class="col-sm-12 pt10" style="display:block; clear:both;" >
				<div class="row">
					<div class="col-sm-1" style="<?php echo $topPad;?>">
						<strong>CUS <br> RGP</strong>	
					</div>	
					<div class="col-sm-11">
						<table class="table table-condensed table-bordered">
							<?php if($dispTitle==1) { ?>
								<tr class="grythead">
									<th>&nbsp;</th>
									<th>BC</th>							
									<th>Diameter</th>
									<th>Power</th>	
									<th>2&#176;/W</th>
									<th>PC/W</th>
									<th>3&#176;/W</th>
									<th>OZ</th>
									<th>CT</th>
									<th>Cylinder</th>
									<th>Axis</th>
									<th>Color</th>
									<th>Blend</th>
									<th>Edge</th>
									<th>Add</th>
									<th>DVA</th>
									<th>NVA</th>
									<th>Type</th>
								</tr>
					<?php 	}
							if($odos=='od') {	?>    
								<tr id="rgpCustomODRow" style="display:<?php echo $rgpSupplyDisplayOD;?>;">
									<td class="od"><label>&nbsp;OD</label></td>
									<td>
										<input readonly class="form-control"  type="text" name="RgpCustomBCOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomBCOD']);?>" >
									</td>												
									<td>
										<input readonly  id="RgpCustomDiameterOD" type="text" class="form-control" name="RgpCustomDiameterOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomDiameterOD'];?>"></td> 
									<td>
										<input readonly class="form-control"  type="text" name="sphere<?php echo $id;?>" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomPowerOD']);?>" >
									</td> 
									<td>
										<input readonly  type="text" name="RgpCustom2degreeOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustom2degreeOD']);?>" class="form-control" >
									</td> 
									<td>
										<input readonly class="form-control"  type="text" name="RgpCustom3degreeOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustom3degreeOD']);?>" >
									</td>	
									<td>
										<input readonly class="form-control"  type="text" name="RgpCustomPCWOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomPCWOD']);?>">
									</td>	
									<td>
										<input readonly  id="RgpCustomOZOD" type="text" class="form-control" name="RgpCustomOZOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomOZOD'];?>">
									</td> 
									<td>
										<input readonly  id="RgpCustomCTOD" type="text" class="form-control" name="RgpCustomCTOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomCTOD'];?>">
									</td> 
									<td>
										<input readonly  id="RgpCustomCylinderOD" type="text" class="form-control " name="RgpCustomCylinderOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomCylinderOD'];?>">
									</td> 
									<td>
										<input readonly  id="RgpCustomAxisOD" type="text" class="form-control" name="RgpCustomAxisOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomAxisOD'];?>">
									</td> 
									<td>
										<input readonly type="text" name="RgpCustomColorOD"  id="RgpCustomColorOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomColorOD']; ?>" class="form-control">
									</td>
									<td>
										<input readonly type="text" name="RgpCustomBlendOD"  id="RgpCustomBlendOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomBlendOD']; ?>" class="form-control" >
									</td>
									<td>
										<input readonly type="text" name="RgpCustomEdgeOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomEdgeOD']);?>" class="form-control"/>
									</td> 
									<td>
										<input readonly  id="RgpCustomAddOD" type="text" class="form-control" name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomAddOD'];?>">
									</td> 
									<td>
										<input readonly id="RgpCustomDvaOD" type="text" name="RgpCustomDvaOD" value="<?php if($GetTypeManufacRow[$i]['RgpCustomDvaOD']) echo $GetTypeManufacRow[$i]['RgpCustomDvaOD']; else echo '20/'; ?>" class="form-control" >
									</td>
									<td>
										<input readonly id="RgpCustomNvaOD" type="text" name="RgpCustomNvaOD" value="<?php if($GetTypeManufacRow[$i]['RgpCustomNvaOD']) echo $GetTypeManufacRow[$i]['RgpCustomNvaOD']; else echo '20/'; ?>" class="form-control" >
									</td>
									<td>
										<input readonly  type="text" name="RgpCustomTypeOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomTypeOD']);?>" class="form-control">
									</td> 
								</tr>
					<?php 	} if($odos=='os') { ?>
								<tr id="rgpCustomOSRow" style="display:<?php echo $rgpSupplyDisplayOS;?>;">
									<td class="os"><label>&nbsp;OS</label></td>  
									<td>
										<input readonly class="form-control"  type="text" name="RgpCustomBCOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomBCOS']);?>">
									</td>												
									<td>
										<input readonly  id="RgpCustomDiameterOS" type="text" class="form-control " name="RgpCustomDiameterOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomDiameterOS'];?>">
									</td> 
									<td>
										<input readonly class="form-control" type="text" name="sphere<?php echo $id;?>" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomPowerOS']);?>">
									</td> 
									<td>
										<input readonly  type="text" name="RgpCustom2degreeOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustom2degreeOS']);?>" class="form-control" >
									</td> 
									<td>
										<input readonly class="form-control"  type="text" name="RgpCustom3degreeOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustom3degreeOS']);?>">
									</td>	
									<td>
										<input readonly class="form-control" type="text" name="RgpCustomPCWOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomPCWOS']);?>" >
									</td>	
									<td>
										<input readonly  id="RgpCustomOZOS" type="text" class="form-control " name="RgpCustomOZOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomOZOS'];?>">
									</td> 
									<td>
										<input readonly  id="RgpCustomCTOS" type="text" class="form-control" name="RgpCustomCTOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomCTOS'];?>">
									</td> 
									<td>
										<input readonly  id="RgpCustomCylinderOS" type="text" class="form-control" name="RgpCustomCylinderOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomCylinderOS'];?>">
									</td> 
									<td>
										<input readonly  id="RgpCustomAxisOS" type="text" class="form-control" name="RgpCustomAxisOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomAxisOS'];?>">
									</td> 
									<td>
										<input readonly type="text" name="RgpCustomColorOS"  id="RgpCustomColorOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomColorOS']; ?>" class="form-control">
									</td>
									<td>
										<input readonly type="text" name="RgpCustomBlendOS"  id="RgpCustomBlendOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomBlendOS']; ?>" class="form-control">
									</td>
									<td>
										<input readonly type="text" name="RgpCustomEdgeOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomEdgeOS']);?>" class="form-control" />
									</td> 
									<td>
										<input readonly  id="RgpCustomAddOS" type="text" class="form-control" name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomAddOS'];?>">
									</td> 
									<td>
										<input readonly id="RgpCustomDvaOS" type="text" name="RgpCustomDvaOS" value="<?php if($GetTypeManufacRow[$i]['RgpCustomDvaOS']) echo $GetTypeManufacRow[$i]['RgpCustomDvaOS']; else echo '20/'; ?>" class="form-control" >
									</td>
									<td>
										<input readonly id="RgpCustomNvaOS" type="text" name="RgpCustomNvaOS" value="<?php if($GetTypeManufacRow[$i]['RgpCustomNvaOS']) echo $GetTypeManufacRow[$i]['RgpCustomNvaOS']; else echo '20/'; ?>" class="form-control" >
									</td>
									<td>
										<input readonly type="text" name="RgpCustomTypeOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomTypeOS']);?>" class="form-control" >
									</td> 
								</tr>
					<?php 	} ?>
						</table>
					</div>	
				</div>
			</div>
<?php	}
	$oldClType = $clType;
	$j++;
	}	// END For Loop
	
	echo "~~";
 ?>
	<div class="row purple_bar form-inline">
		<div class="col-sm-4">
			<label>Order Trial #/Supply</label>
		</div>
		<div class="col-sm-8 text-right">
			<?php 
				$eval='';
				$eVal = substr(trim($GetTypeManufacRow[0]['cpt_evaluation_fit_refit']),0,1);
				if($eVal=="$"){
					$cptEvalCharges = substr($GetTypeManufacRow[0]['cpt_evaluation_fit_refit'],1,strlen($GetTypeManufacRow[0]['cpt_evaluation_fit_refit']));
				}else{
					$cptEvalCharges = trim($GetTypeManufacRow[0]['cpt_evaluation_fit_refit']);
				}
			?>	
			<div class="row">
				<div class="col-sm-4">
					<label>CL Exam:</label>	
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-usd"></span>	
						</div>	
						<input type="text" id="clExam"  name="clExam" readonly value="<?php echo $cptEvalCharges;?>" class="form-control" >
					</div>	
				</div>
				
				<div class="col-sm-4">
					<label>CL Supply:</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-usd"></span>	
						</div>	
						<input type="text" id="clSupply" name="clSupply" value="" class="form-control" readonly >
					</div>		
				</div>
				<div class="col-sm-4">
					<label>Total:</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-usd"></span>	
						</div>	
						<input type="text" id="totalCharges" name="totalCharges" value="" class="form-control" readonly >
					</div>		
				</div>	
			</div>	
		</div>
	</div>	
	<div class="row pt10">
		<div class="col-sm-12">
			<table class="table table-condensed table-bordered">
				<tr class="grythead">
					<th class="od">&nbsp;</th>
					<th class="od">&nbsp;</th> 
					<th>Type</th>
					<th>Lens&nbsp;Code</th>
					<th>Color</th>
					<th>Price</th>
					<th>Qty</th>  
					<th class="text-nowrap">Sub Total</th> 
					<th>Discount</th> 
					<th>Total</th>
					<th>Ins.</th> 
					<th>Balance</th> 
				</tr>
				<?php
					$odLen = sizeof($LensBoxOD);
					for($i=0; $i< $odLen; $i++){  
						if($QtyOD=='')$QtyOD=1;	?>							
						<tr id="typeTrOD<?php echo $i;?>">
							<td class="od">
								<?php
								if($_REQUEST['callFrom']!='clSupply'){
								 if($i < ($odLen-1)) {  ?>
								<img src="../../library/images/closebut.png" alt="Delete Row" class="link_cursor" id="imgOD<?php echo $i;?>" title="Delete Row" onClick="removeTableRow('typeTrOD<?php echo $i;?>');">
								<?php }else { ?>
								<img id="imgOD<?php echo $i;?>" class="link_cursor" src="../../library/images/addinput.png" alt="Add More" title="Add More" onClick="addNewRow('od', 'typeTrOD', 'imgOD', '<?php echo $i;?>');">
								<?php } 
								}?>
							</td>
							<td class="od"><label>&nbsp;OD</label>
							  <input type="hidden" name="ordODId<?php echo $i;?>" id="ordODId<?php echo $i;?>" value="~" />
							  <input type="hidden" name="cl_det_idOD<?php echo $i;?>" id="cl_det_idOD<?php echo $i;?>" value="<?php echo $clwsid_ArrOD[$i];?>" />
							</td> 
							<td>
								<input type="text" name="LensBoxOD<?php echo $i;?>" id="LensBoxOD<?php echo $i;?>" class="form-control lensboxod_menu" value="<?php echo $LensBoxOD[$i];?>"  />
								<input type="hidden" name="LensBoxOD<?php echo $i;?>ID" id="LensBoxOD<?php echo $i;?>ID" value="<?php echo $arrPrintOD[$i]['LensBoxOD_ID'];?>" funVars="printOrder~od~<?php echo $i;?>" >
							</td>
							<td>
								<select name="lensNameIdList<?php echo $i;?>" id="lensNameIdList<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
									<?php echo lenseCodes($arrLensCode, ''); ?>
								</select>
							</td>
							<td>
								<select name="colorNameIdList<?php echo $i;?>" id="colorNameIdList<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
									<?php echo lenseColors($arrLensColor, '');?>
								</select>
							</td>
							<td>
								<input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);"  id="PriceOD<?php echo $i;?>" type="text" name="PriceOD<?php echo $i;?>" value="<?php echo $PriceOD[$i];?>" class="form-control" >
							</td> 
							<td>
								<input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>',this);" type="text" name="QtyOD<?php echo $i;?>" value="<?php echo $QtyOD;?>" class="form-control"  id="QtyOD<?php echo $i;?>" />
							</td> 
							<td>
								<input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="SubTotalOD<?php echo $i;?>" id="SubTotalOD<?php echo $i;?>" value="<?php echo $SubTotalOD;?>">
							</td> 
							<td  class="text-left">
								<input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);"  id="DiscountOD<?php echo $i;?>" type="text" class="form-control" name="DiscountOD<?php echo $i;?>" value="<?php echo $DiscountOD;?>">
							</td> 
							<td >
								<input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);"  type="text" class="form-control " name="TotalOD<?php echo $i;?>" id="TotalOD<?php echo $i;?>" value="<?php echo $TotalOD;?>">
							</td>
							<td >
								<input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="InsOD<?php echo $i;?>" id="InsOD<?php echo $i;?>" value="<?php echo $InsOD;?>" >
							</td> 
							<td>
								<input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="BalanceOD<?php echo $i;?>" id="BalanceOD<?php echo $i;?>" value="<?php echo $BalanceOD;?>" />
							</td> 
						</tr>
			<?php 	} 
				if($odLen==0) { ?>
					<tr id="typeTrOD0">
						<td class="od">
							<?php if($callFrom!='clSupply'){ ?>
								<img src="../../library/images/addinput.png" alt="Add More" class="link_cursor" id="imgOD0" title="Add Row" onClick="addNewRow('od', 'typeTrOD', 'imgOD', '0');">
							<?php } ?>
						</td>
						<td class="od">
							<label>OD</label>
							<input type="hidden" name="ordODId0" id="ordODId0" value="~" />
						</td> 
						<td>
							<input type="text" name="LensBoxOD0" id="LensBoxOD0" class="form-control lensboxod_menu" value=""  />
							<input type="hidden" name="LensBoxOD0ID" id="LensBoxOD0ID" value="" funVars="printOrder~od~0">
						</td>
						<td>
							<select name="lensNameIdList0" id="lensNameIdList0" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
								<?php echo lenseCodes($arrLensCode, ''); ?>
							</select>
						</td>
						<td>
							<select name="colorNameIdList0" id="colorNameIdList0" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
								<option value="">Select</option>
								<?php echo lenseColors($arrLensColor, '');?>
							</select>
						</td>
						<td>
							<input onBlur="javascript:calcTotalBalODFn(0,this);justify2Decimal(this);"  id="PriceOD0" type="text" name="PriceOD0" value="" class="form-control" >
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalODFn(0,this);" type="text" name="QtyOD0" value="1" class="form-control"  id="QtyOD0" />
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalODFn(0,this);justify2Decimal(this);" class="form-control" type="text" name="SubTotalOD0" id="SubTotalOD0" value="" >
						</td> 
						<td  class="text-left">
							<input onBlur="javascript:calcTotalBalODFn(0,this);justify2Decimal(this);"  id="DiscountOD0" type="text" class="form-control" name="DiscountOD0" value="" >
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalODFn(0,this);justify2Decimal(this);"  type="text" class="form-control " name="TotalOD0" id="TotalOD0" value="">
						</td>
						<td >
							<input onBlur="javascript:calcTotalBalODFn(0,this);justify2Decimal(this);" class="form-control" type="text" name="InsOD0" id="InsOD0" value="" >
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalODFn(0,this);justify2Decimal(this);" class="form-control" type="text" name="BalanceOD0" id="BalanceOD0" value="" />
						</td> 
					</tr>
				<?php
				}
				$osLen = sizeof($LensBoxOS);
				for($i=0; $i< $osLen; $i++){  
					if($QtyOS=='')$QtyOS=1; 
				?>	                       
					<tr id="typeTrOS<?php echo $i;?>">
						<td class="os">
							<?php
							if($_REQUEST['callFrom']!='clSupply'){
							if($i < ($osLen-1)) {  ?>
								<img id="imgOS<?php echo $i;?>" class="link_cursor" src="../../library/images/closebut.png" alt="Delete Row" title="Delete Row" onClick="removeTableRow('typeTrOS<?php echo $i;?>');">
							<?php }else { ?>
								<img src="../../library/images/addinput.png" alt="Add More" class="link_cursor" id="imgOS<?php echo $i;?>" title="Add Row" onClick="addNewRow('os', 'typeTrOS', 'imgOS', '<?php echo $i;?>');">
							<?php }
							}?>
						</td>
						<td class="os">
							<label>&nbsp;OS</label>
							<input type="hidden" name="ordOSId<?php echo $i;?>" id="ordOSId<?php echo $i;?>" value="~" />
							<input type="hidden" name="cl_det_idOS<?php echo $i;?>" id="cl_det_idOS<?php echo $i;?>" value="<?php echo $clwsid_ArrOS[$i];?>" />
						</td> 
						<td>
							<input type="text" name="LensBoxOS<?php echo $i;?>" id="LensBoxOS<?php echo $i;?>" class="form-control lensboxod_menu" value="<?php echo $LensBoxOS[$i];?>"  />
							<input type="hidden" name="LensBoxOS<?php echo $i;?>ID" id="LensBoxOS<?php echo $i;?>ID" value="<?php echo $arrPrintOS[$i]['LensBoxOS_ID'];?>" funVars="printOrder~os~<?php echo $i;?>">                                                               
						</td>
						<td>
							<select name="lensNameIdListOS<?php echo $i;?>" id="lensNameIdList<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
							   <?php echo lenseCodes($arrLensCode, ''); ?>
							</select>
						</td>
						<td>
							<select name="colorNameIdListOS<?php echo $i;?>" id="colorNameIdList<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
							  <?php echo lenseColors($arrLensColor, '');?>                                      
							</select>
						</td>
						<td>
							<input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);"  id="PriceOS<?php echo $i;?>" type="text" name="PriceOS<?php echo $i;?>" value="<?php echo $PriceOS[$i];?>" class="form-control" >
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);" type="text" name="QtyOS<?php echo $i;?>" value="<?php echo $QtyOS;?>" class="form-control"  id="QtyOS<?php echo $i;?>" />
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="SubTotalOS<?php echo $i;?>" id="SubTotalOS<?php echo $i;?>" value="<?php echo $SubTotalOS;?>">
						</td> 
						<td class="text-left">
							<input onBlur="javascript:calcTotalBalOSFn();justify2Decimal(this);" id="DiscountOS<?php echo $i;?>" type="text" class="form-control" name="DiscountOS<?php echo $i;?>" value="<?php echo $DiscountOS;?>">
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);"  id="TotalOS<?php echo $i;?>" type="text" class="form-control" name="TotalOS<?php echo $i;?>" value="<?php echo $TotalOS;?>">
						</td>
						<td>
							<input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="InsOS<?php echo $i;?>" id="InsOS<?php echo $i;?>" value="<?php echo $InsOS;?>" >
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="BalanceOS<?php echo $i;?>" id="BalanceOS<?php echo $i;?>" value="<?php echo $BalanceOS;?>" />
						</td> 
					</tr>
		<?php 	}	
				if($osLen==0) { ?>
					<tr id="typeTrOS0">
						<td class="os">
						  <?php if($callFrom!='clSupply'){ ?>  
							<img src="../../library/images/addinput.png" alt="Add More" class="link_cursor" id="imgOS0" title="Add Row" onClick="addNewRow('os', 'typeTrOS', 'imgOS', '0');">
						  <?php } ?>  
						</td>
						<td class="os"><label>&nbsp;OS</label>
						 <input type="hidden" name="ordOSId0>" id="ordOSId0" value="~" />
						</td> 
						<td>
							<input type="text" name="LensBoxOS0" id="LensBoxOS0" class="form-control lensboxod_menu" value=""  />
							<input type="hidden" name="LensBoxOS0ID" id="LensBoxOS0ID" value="" funVars="printOrder~os~0">          
						</td>
						<td>
							<select name="lensNameIdListOS0" id="lensNameIdList0" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
							   <?php echo lenseCodes($arrLensCode, $selVal); ?>
							</select>
						</td>
						<td>
							<select name="colorNameIdListOS0" id="colorNameIdList0" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
							  <?php echo lenseColors($arrLensColor, $selVal);?>                                      
							</select>
						</td>
						<td>
							<input onBlur="javascript:calcTotalBalOSFn(0,this);justify2Decimal(this);"  id="PriceOS0" type="text" name="PriceOS0" value="" class="form-control" >
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalOSFn(0,this);" type="text" name="QtyOS0" value="1" class="form-control"  id="QtyOS0" />
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalOSFn(0,this);justify2Decimal(this);" class="form-control" type="text" name="SubTotalOS0" id="SubTotalOS0" value="">
						</td> 
						<td  class="alignLeft">
							<input onBlur="javascript:calcTotalBalOSFn(0,this);justify2Decimal(this);" id="DiscountOS0" type="text" class="form-control " name="DiscountOS0" value="">
						</td> 
						<td >
							<input onBlur="javascript:calcTotalBalOSFn(0,this);justify2Decimal(this);"  id="TotalOS0" type="text" class="form-control " name="TotalOS0" value="">
						</td>
						<td >
							<input onBlur="javascript:calcTotalBalOSFn(0,this);justify2Decimal(this);" class="form-control" type="text" name="InsOS0" id="InsOS0" value="">
						</td> 
						<td>
							<input onBlur="javascript:calcTotalBalOSFn(0,this);justify2Decimal(this);" class="form-control" type="text" name="BalanceOS0" id="BalanceOS0" value="" />
						</td> 
					</tr>
		<?php 	}	?>	
			</table>
		</div>	
	</div>
</div>
<?php  
	$txtTotOD = ($odLen>0) ? $odLen : 1;
	$txtTotOS = ($osLen>0) ? $osLen : 1;
 
	echo "~~";
	$typeManufac=substr($typeManufac,0, strlen($typeManufac)-4);
	echo $typeManufac;
	
	echo "~~";
	$dos = $GetTypeManufacRow[0]['dos'];
	echo $dos;
	
	echo "~~";
	echo $clws_type;	
	
	echo "~~";
	echo $txtTotOD;
	echo "~~";
	echo $txtTotOS;
	echo "~~";
	echo $clws_charges_id;
}	// END IF LOOP		

$dd = ob_get_contents();
ob_end_clean();
echo $dd;
?>	