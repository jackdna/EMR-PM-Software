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

/*	
*	File: muti_phy.php
*	Purpose: Show multi reff popup
*	Access Type: Direct 
*/
require_once(dirname(__FILE__)."/../../../config/globals.php");

$webroot_tmp = $GLOBALS['webroot'];

//-----  Get data from remote server -------------------

//$zRemotePageName = "patient_info/common/muti_phy";
//require(dirname(__FILE__)."/../../chart_notes/get_chart_from_remote_server.inc.php");

//if(isset($GLOBALS["remote"]["webroot"])&&!empty($GLOBALS["remote"]["webroot"])){
	//$webroot_tmp = $GLOBALS["remote"]["webroot"];
//}
$callFrom = (isset($_REQUEST['callFrom']) && $_REQUEST['callFrom']!= "")?$_REQUEST['callFrom']:"";
$callFrom = "popup";
//-----  Get data from remote server -------------------


require_once(dirname(__FILE__)."/../../../library/classes/cls_common_function.php");
$OBJCommonFunction = new CLSCommonFunction;

$_REQUEST["phyType"] = xss_rem($_REQUEST["phyType"]);
$WRP = $webroot_tmp;

if(strtolower($_REQUEST["mode"]) == "get"){
		ob_start();
		
		$phyType	=	$_REQUEST["phyType"];
		
		$body	=	'';
        if(isset($_REQUEST['request_from']) && $_REQUEST['request_from'] == 'scheduler'){
            $onCloseClick  = 'onClick="show_multi_phy(0,'.$phyType.');"';
        } else {
            $onCloseClick  = 'onClick="top.fmain.show_multi_phy(0,'.$phyType.');"';
        }
		$headerTitle   = "";
		if($phyType == 1)		$headerTitle = 'Enter Referring Physicians';
		elseif($phyType == 2)	$headerTitle = 'Enter Co-Managed Physicians';
		elseif($phyType == 3)	$headerTitle = 'Enter Primary Care Physicians';
		elseif($phyType == 4)	$headerTitle = 'Enter Primary Care Provider';
		
		$fieldName	=	"";
		if($phyType == 1)		$fieldName = 'RefPhy';
		elseif($phyType == 2)	$fieldName = 'CoPhy';
		elseif($phyType == 3)	$fieldName = 'PCPMedHx';
		elseif($phyType == 4)	$fieldName = 'PCPDemo';
		
		$rqPhy	=	"";
		if($phyType == 1)		$rqPhy = $_REQUEST['strRefPhy'];
		elseif($phyType == 2)	$rqPhy = $_REQUEST['strCoPhy'];
		elseif($phyType == 3)	$rqPhy = $_REQUEST['strPCPMedHx'];
		elseif($phyType == 4)	$rqPhy = $_REQUEST['strPCPDemoPhy'];
		
		$rqPhyHid	=	"";
		if($phyType == 1)		$rqPhyHid = $_REQUEST['strRefPhyHid'];
		elseif($phyType == 2)	$rqPhyHid = $_REQUEST['strCoPhyHid'];
		elseif($phyType == 3)	$rqPhyHid = $_REQUEST['strPCPMedHxHid'];
		elseif($phyType == 4)	$rqPhyHid = $_REQUEST['strPCPDemoHid'];
		
		$columnTitle	=	"";
		if($phyType == 1)		$columnTitle = 'Referring Physicians';
		elseif($phyType == 2)	$columnTitle = 'Co-Managed Physicians';
		elseif($phyType == 3)	$columnTitle = 'Primary Care Provider';
		elseif($phyType == 4)	$columnTitle = 'Primary Care Provider';
		
		$arrPatPhyDB = $arrPatPhyDBDataID = $arrTemp = array();
		$intTemp = 1;
		
		$qrySelpatPhy = "select TRIM(CONCAT(refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName,if(refPhy.MiddleName!='',' ',''),refPhy.Title)) as refName, 
				refPhy.Address1, refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.physician_phone, refPhy.physician_fax, 
				refPhy.physician_email,refPhy.delete_status, pmrf.id as pmrfDataId, pmrf.ref_phy_id as pmrfRefId, refPhy.comments, refPhy.PractiseName
				from patient_multi_ref_phy pmrf INNER JOIN refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
				where pmrf.patient_id = '".$_SESSION['patient']."' ".(($phyType == 3 || $phyType == 4)?" and pmrf.phy_type IN (3,4)" : " and pmrf.phy_type = '".$phyType."'")." and pmrf.status = '0' ORDER BY pmrf.id";
		$rsSelpatPhy = imw_query($qrySelpatPhy);
		if(imw_num_rows($rsSelpatPhy) > 0)
		{
			while($rowSelpatPhy = imw_fetch_array($rsSelpatPhy))
			{
				if(in_array((int)$rowSelpatPhy["pmrfRefId"], $arrTemp) == false)
				{
					$strPhyVal = "";
					//$strPhyVal = trim(stripslashes($rowSelpatPhy["refName"]));
					$strPhyVal = trim(stripslashes($OBJCommonFunction->get_ref_phy_name($rowSelpatPhy["pmrfRefId"])));
					$address = "";
					$address = core_extract_user_input($rowSelpatPhy['Address1']);
					if($address != "" && $rowSelpatPhy['Address2']!= ""){
						$address .= ", ".core_extract_user_input($rowSelpatPhy['Address2']);
					}
					else if($address == "" && $rowSelpatPhy['Address2']!= ""){
						$address = core_extract_user_input($rowSelpatPhy['Address2']);
					}
					if($address != ""){
						$address .= ", ".core_extract_user_input($rowSelpatPhy['City']);
					}
					else{
						$address = core_extract_user_input($rowSelpatPhy['City']);
					}

					if($address != ""){
						$address .= ", ".core_extract_user_input($rowSelpatPhy['State'])." ".core_extract_user_input($rowSelpatPhy['ZipCode']);
					}
					else{
						$address = core_extract_user_input($rowSelpatPhy['State'])." ".core_extract_user_input($rowSelpatPhy['ZipCode']);
					}
					if(trim($address) != ""){
						$address .= "\n";
					}
					if($rowSelpatPhy['physician_phone'] != ""){
						$address .= "Phone: ".$rowSelpatPhy['physician_phone']."\n";
					}
					
					//if( $phyType == 1 ) {
							if($rowSelpatPhy['physician_fax'] != "" && $rowSelpatPhy['physician_fax'] != "0"){
								$address .= "Fax: ".$rowSelpatPhy['physician_fax']."\n";
							}
							if(trim($rowSelpatPhy['PractiseName']) != ""){
								$address .= "Practice Name: ".$rowSelpatPhy['PractiseName']."\n";
							}
							if(trim($rowSelpatPhy['comments']) != ""){
								$address .= "Comments: ".$rowSelpatPhy['comments']."\n";
							} 
					//}
					
					$address = trim($address);

					if($rowSelpatPhy['delete_status'] == 0){
						$phyClass = "form-control";
					}
					else{
						$phyClass = "form-control red-font";
					}

					if(empty($strPhyVal) == false)
					{
						$body .= '
							<div id="divTR-'.$phyType.'-'.$intTemp.'" class="col-xs-12 margin-top-5" title="'.$address.'">
								<div class="col-xs-2 text-center" >'.$intTemp.'</div>
								<div class="col-xs-9" title="'.$address.'">
									<input type="hidden" name="hid'.$fieldName.'Id[]" id="hid'.$fieldName.'Id'.$intTemp.'" value = "'.$rowSelpatPhy["pmrfDataId"].'">
									<input type="text" name="txt'.$fieldName.'Arr[]" id="txt'.$fieldName.'Arr-'.$intTemp.'" value="'.$strPhyVal.'" class="'.$phyClass.'" onKeyUp="top.loadPhysicians(this,\'hid'.$fieldName.'Arr-'.$intTemp.'\');" onFocus="top.loadPhysicians(this,\'hid'.$fieldName.'Arr-'.$intTemp.'\');" />
									<input type="hidden" name="hid'.$fieldName.'Arr[]" id="hid'.$fieldName.'Arr-'.$intTemp.'" value="'.$rowSelpatPhy["pmrfRefId"].'" />
								</div>
								<div class="col-xs-1">
									<span id="imgDel-'.$phyType.'-'.$intTemp.'" name="imgDel-'.$phyType.'-'.$intTemp.'" class="pointer" onClick="del_phy_row(\'imgDel-'.$phyType.'-'.$intTemp.'\', \''.$intTemp.'\',\''.$rowSelpatPhy["pmrfDataId"].'\', \''.$phyType.'\');"><i class="glyphicon glyphicon-remove"></i></span>
									<span id="imgAdd-'.$phyType.'-'.$intTemp.'" name="imgAdd-'.$phyType.'-'.$intTemp.'" class="pointer hidden" onClick="add_phy_row(\'imgAdd-'.$phyType.'-'.$intTemp.'\',\'imgDel-'.$phyType.'-'.$intTemp.'\', \''.$intTemp.'\', \''.$phyType.'\',\''.$callFrom.'\');"><i class="glyphicon glyphicon-plus"></i></span>
								</div>
							</div>';

							$intTemp++;
							$arrTemp[] = $rowSelpatPhy["pmrfRefId"];
					}
					
				}
			}
			
		}
		
		$arrPhy = $arrPhyHid = array();
		$arrPhy = explode("!~#~!", $rqPhy);
		$arrPhyHid = explode("!~#~!", $rqPhyHid);
		if(count($arrPhy) > 0)
		{
			foreach($arrPhy as $intPhyKey => $strPhyVal)
			{
				if(in_array((int)$arrPhyHid[$intPhyKey], $arrTemp) == false)
				{
					if(empty($strPhyVal) == false)
					{
						$intTemp++;
					}
				}
			}
			
			if($intTemp > 1)
			{
				$body .= '
					<div id="divTR-'.$phyType.'-'.$intTemp.'" class="col-xs-12 margin-top-5" >
						<div class="col-xs-2 text-center" >'.$intTemp.'</div>
						<div class="col-xs-9" >
							<input type="text" name="txt'.$fieldName.'Arr[]" id="txt'.$fieldName.'Arr-'.$intTemp.'" value="" class="form-control" onKeyUp="top.loadPhysicians(this,\'hid'.$fieldName.'Arr-'.$intTemp.'\');" onFocus="top.loadPhysicians(this,\'hid'.$fieldName.'Arr-'.$intTemp.'\');" />
							<input type="hidden" name="hid'.$fieldName.'Arr[]" id="hid'.$fieldName.'Arr-'.$intTemp.'" />
						</div>
						<div class="col-xs-1">
							<span id="imgDel-'.$phyType.'-'.$intTemp.'" name="imgDel-'.$phyType.'-'.$intTemp.'" class="pointer hidden" onClick="del_phy_row(\'imgDel-'.$phyType.'-'.$intTemp.'\',\''.$intTemp.'\',\'\', \''.$phyType.'\');"><i class="glyphicon glyphicon-remove"></i></span>
								<span id="imgAdd-'.$phyType.'-'.$intTemp.'" name="imgAdd-'.$phyType.'-'.$intTemp.'" class="pointer" onClick="add_phy_row(\'imgAdd-'.$phyType.'-'.$intTemp.'\',\'imgDel-'.$phyType.'-'.$intTemp.'\', \''.$intTemp.'\', \''.$phyType.'\',\''.$callFrom.'\');"><i class="glyphicon glyphicon-plus"></i></span>
						</div>
					</div>';
						
			}
			
		}
		
		if(!$body)
		{
			$intTemp = 1;
			$body .= '
				<div id="divTR-'.$phyType.'-'.$intTemp.'" class="col-xs-12 margin-top-5">
					<div class="col-xs-2 text-center" >'.$intTemp.'</div>
					<div class="col-xs-9">
						<input type="text" name="txt'.$fieldName.'Arr[]" id="txt'.$fieldName.'Arr-'.$intTemp.'" value="" class="form-control" onKeyUp="top.loadPhysicians(this,\'hid'.$fieldName.'Arr-'.$intTemp.'\');" onFocus="top.loadPhysicians(this,\'hid'.$fieldName.'Arr-'.$intTemp.'\');" />
						<input type="hidden" name="hid'.$fieldName.'Arr[]" id="hid'.$fieldName.'Arr-'.$intTemp.'" />
					</div>
					<div class="col-xs-1">
						<span id="imgDel-'.$phyType.'-'.$intTemp.'" name="imgDel-'.$phyType.'-'.$intTemp.'" class="pointer hidden" onClick="del_phy_row(\'imgDel-'.$phyType.'-'.$intTemp.'\',\''.$intTemp.'\',\'\', \''.$phyType.'\');"><i class="glyphicon glyphicon-remove"></i></span>
						<span id="imgAdd-'.$phyType.'-'.$intTemp.'" name="imgAdd-'.$phyType.'-'.$intTemp.'" class="pointer" onClick="add_phy_row(\'imgAdd-'.$phyType.'-'.$intTemp.'\',\'imgDel-'.$phyType.'-'.$intTemp.'\', \''.$intTemp.'\', \''.$phyType.'\',\''.$callFrom.'\');"><i class="glyphicon glyphicon-plus"></i></span>
					</div>
				</div>';
		}	
			
			
	?>
	
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal" <?=$onCloseClick?> >×</button>
				<h4 class="modal-title" id="modal_title"><?=$headerTitle?></h4>
			</div>
		  
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 grythead ">
						<div class="col-xs-2 text-center"><label>Sr. No.</label></div>
						<div class="col-xs-9"><label><?=$columnTitle?></label></div>
						<div class="col-xs-1">&nbsp;</div>
          	<div class="col-xs-12 margin-top-5"></div>  
            <br>
					</div>
					<div class="clearfix">&nbsp;</div>
				</div>
				
				<div class="row" style="height:265px; overflow:hidden; overflow-y:scroll;" id="divMultiPhyInner<?=$phyType?>">
					<input type="hidden" name="hidDelete<?=($phyType == 3 ? 'PCP' : $fieldName)?>" id="hidDelete<?=($phyType == 3 ? 'PCP' : $fieldName)?>" />
					<?=$body?>
				</div>	
			</div>
		  
			<div class="panel-footer">
				<?php 
					if(isset($_REQUEST['request_from']) && $_REQUEST['request_from'] == 'scheduler'){
						?>
							<button type="button" class="btn btn-danger" name="btSaveCloseMultiPhy" id="btSaveCloseMultiPhy" onClick="show_multi_phy(2,<?=$phyType?>,'<?=$WRP?>');">Save & Close</button>
						<?php
					}else{
						?>
							<button type="button" class="btn btn-danger btn-md" name="btSaveCloseMultiPhy" id="btSaveCloseMultiPhy" onClick="top.fmain.show_multi_phy(2,<?=$phyType?>,'<?=$WRP?>');">Save & Close</button>
						<?php
					}
				?>
			</div>
		</div>
	</div>
  
	<?php
		$contentsHTML = ob_get_contents();
		ob_end_clean();
		echo $contentsHTML."!~-1-~!".$stringAllphy."!~-1-~!".$stringPhyId;
	?>
<?php
}
elseif(strtolower($_REQUEST["mode"]) == "save"){
	if($_REQUEST["phyType"] == 1){
		$arrHidDelete = array();
		$arrHidDelete = explode('-', $_REQUEST['hidDeleteRefPhyVal']);
		foreach($arrHidDelete as $intHidDeleteKey => $strHidDeleteVal){
			$arrHidDeleteVal = array();
			$arrHidDeleteVal = explode("~~", $strHidDeleteVal);
			$intHidDeleteVal = $intCounterHid = 0;
			$intHidDeleteVal = $arrHidDeleteVal[0];
			$intCounterHid = $arrHidDeleteVal[1];
			if((int)$intHidDeleteVal > 0){
				$qryDelete = "update patient_multi_ref_phy set status = '1', deleted_by = '".$_SESSION['authId']."', deleted_by_date_time = '".date('Y-m-d H:i:s')."' where id= '".$intHidDeleteVal."' ";
				$rsDelete = imw_query($qryDelete);
			}
		}
		
		$rqStrTxtRefPhyArr = "";
		$rqStrTxtRefPhyArr = $_REQUEST["strTxtRefPhyArr"];
		$intRefId4Hid = 0;
		$strRef4Txt = "";
		$strRef4PopTxt = "";
		if(empty($rqStrTxtRefPhyArr) == false){
			$arrRefPhyArr = array();
			$arrRefPhyArr = explode("!$@$!",$rqStrTxtRefPhyArr);
			if(count($arrRefPhyArr) > 0){
				$rqStrHidRefPhyIdID = "";
				$rqStrHidRefPhyIdID = $_REQUEST["strHidRefPhyIdID"];		
				$arrHidId = array();
				$arrHidId = explode("!$@$!",$rqStrHidRefPhyIdID);
				
				$rqStrHidRefPhyArrID = "";
				$rqStrHidRefPhyArrID = $_REQUEST["strHidRefPhyArrID"];		
				$arrRefPhyID = array();
				$arrRefPhyID = explode("!$@$!",$rqStrHidRefPhyArrID);
				
				foreach((array)$arrRefPhyArr as $intRefPhyArrKey => $strRefPhyArrVal){
					if(empty($arrHidId[$intRefPhyArrKey]) == true){
						if(empty($strRefPhyArrVal) == false){
							/* IF ENTRING PHYSICIAN NAME MANUALLY*/
							if($arrRefPhyID[$intRefPhyArrKey] == '' || $arrRefPhyID[$intRefPhyArrKey] == 0){
								list($intRefPhyId, $strRefPhyName) = $OBJCommonFunction->chk_create_ref_phy($strRefPhyArrVal);
								if((empty($intRefPhyId) == false) && (empty($strRefPhyName) == false)){
									$arrRefPhyID[$intRefPhyArrKey] = $intRefPhyId;
									$strRefPhyArrVal = $strRefPhyName;
								}	
							}
							/* IF ENTRING PHYSICIAN NAME MANUALLY*/
							$qryInsertRefPhy = "insert into patient_multi_ref_phy 
														(patient_id, ref_phy_id, ref_phy_name, phy_type, created_by, created_by_date_time) Values ('".$_SESSION['patient']."', '".$arrRefPhyID[$intRefPhyArrKey]."', '".addslashes($strRefPhyArrVal)."', '1','".$_SESSION['authId']."', '".date('Y-m-d H:i:s')."')";
							$rsInsertRefPhy = imw_query($qryInsertRefPhy);
						}
					}
					elseif($arrHidId[$intRefPhyArrKey]){
						$qryUpdateRefPhy = "update patient_multi_ref_phy set patient_id = '".$_SESSION['patient']."', ref_phy_id = '".$arrRefPhyID[$intRefPhyArrKey]."', ref_phy_name = '".addslashes($strRefPhyArrVal)."', modified_by = '".$_SESSION['authId']."', modified_by_date_time = '".date('Y-m-d H:i:s')."' where id = '".$arrHidId[$intRefPhyArrKey]."'";
						$rsUpdateRefPhy = imw_query($qryUpdateRefPhy);
					}
				}
			}
		}
		
		
		$qrySelpatRefPhy = "select TRIM(CONCAT(refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName,if(refPhy.MiddleName!='',' ',''),refPhy.Title)) as refName, refPhy.physician_Reffer_id,
							refPhy.Address1, refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.physician_phone, refPhy.physician_fax, refPhy.comments, refPhy.PractiseName
							from patient_multi_ref_phy pmrf INNER JOIN refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
							where pmrf.patient_id = '".$_SESSION['patient']."' and pmrf.phy_type = '1' and pmrf.status = '0' 
							ORDER BY pmrf.id";
		$rsSelpatRefPhy = imw_query($qrySelpatRefPhy);
		$counter = 0;
		$totalRows = imw_num_rows($rsSelpatRefPhy);
		if( $totalRows > 0){
			while($rowSelpatRefPhy = imw_fetch_array($rsSelpatRefPhy)){
				$counter++;
				//$strRef4Txt .= trim(stripslashes($rowSelpatRefPhy["refName"]))."; ";
				$refPhyName = trim(stripslashes($OBJCommonFunction->get_ref_phy_name($rowSelpatRefPhy["physician_Reffer_id"])));
				$refPhyStatus = $OBJCommonFunction->get_ref_phy_del_status((int)$rowSelpatRefPhy["physician_Reffer_id"]);
				$strRef4Txt .= $refPhyName."; ";
				$format_addres = format_ref_data($rowSelpatRefPhy);
				$strRef4PopTxt .= '<span class="col-xs-12 '.($refPhyStatus?'red-font':'').'"><b>&bull; '.$refPhyName.'</b><br>'.$format_addres.'</span>';
				$strRef4PopTxt .= ( $counter < $totalRows ) ? '<span class="col-xs-12 border-dashed"></span>' : ''; 

				if($intRefId4Hid == 0){
					$qryUpDatePatData = "update patient_data set primary_care_id = '".$rowSelpatRefPhy["physician_Reffer_id"]."', primary_care = '".addslashes($rowSelpatRefPhy["refName"])."' where id = '".$_SESSION['patient']."' LIMIT 1 ";
					imw_query($qryUpDatePatData);
					$intRefId4Hid = $rowSelpatRefPhy["physician_Reffer_id"];
				}
			}
		}
		else{
			$qryUpDatePatData = "update patient_data set primary_care_id = '0', primary_care = '' where id = '".$_SESSION['patient']."' LIMIT 1 ";
			imw_query($qryUpDatePatData);
		}
		
		echo "DONE"."-!-".$strRef4Txt."-!-".$intRefId4Hid."-!-".$strRef4PopTxt;
	}
	elseif($_REQUEST["phyType"] == 2){
		$arrHidDelete = array();
		$arrHidDelete = explode('-', $_REQUEST['hidDeleteCoPhyVal']);
		foreach($arrHidDelete as $intHidDeleteKey => $strHidDeleteVal){
			$arrHidDeleteVal = array();
			$arrHidDeleteVal = explode("~~", $strHidDeleteVal);
			$intHidDeleteVal = $intCounterHid = 0;
			$intHidDeleteVal = $arrHidDeleteVal[0];
			$intCounterHid = $arrHidDeleteVal[1];
			if((int)$intHidDeleteVal > 0){
				$qryDelete = "update patient_multi_ref_phy set status = '1', deleted_by = '".$_SESSION['authId']."', deleted_by_date_time = '".date('Y-m-d H:i:s')."' where id= '".$intHidDeleteVal."' ";
				$rsDelete = imw_query($qryDelete);
			}
		}
		
		$rqStrTxtCoPhyArr = "";
		$rqStrTxtCoPhyArr = $_REQUEST["strTxtCoPhyArr"];
		$intRefId4Hid = 0;
		$strRef4Txt = "";
		if(empty($rqStrTxtCoPhyArr) == false){
			$arrCoPhyArr = array();
			$arrCoPhyArr = explode("!$@$!",$rqStrTxtCoPhyArr);
			if(count($arrCoPhyArr) > 0){
				$rqStrHidCoPhyIdID = "";
				$rqStrHidCoPhyIdID = $_REQUEST["strHidCoPhyIdID"];
				$arrHidId = array();
				$arrHidId = explode("!$@$!",$rqStrHidCoPhyIdID);
				
				$rqStrHidCoPhyArrID = "";
				$rqStrHidCoPhyArrID = $_REQUEST["strHidCoPhyArrID"];
				$arrCoPhyID = array();
				$arrCoPhyID = explode("!$@$!",$rqStrHidCoPhyArrID);
				
				foreach((array)$arrCoPhyArr as $intCoPhyArrKey => $strCoPhyArrVal){
					if(empty($arrHidId[$intCoPhyArrKey]) == true){
						if(empty($strCoPhyArrVal) == false){
							/* IF ENTRING PHYSICIAN NAME MANUALLY*/
							if($arrCoPhyID[$intCoPhyArrKey] == '' || $arrCoPhyID[$intCoPhyArrKey] == 0){
								list($intCoPhyId, $strCoPhyName) = $OBJCommonFunction->chk_create_ref_phy($strCoPhyArrVal);
								if((empty($strCoPhyName) == false) && (empty($strCoPhyName) == false)){
									$arrCoPhyID[$intCoPhyArrKey] = $intCoPhyId;
									$strCoPhyArrVal = $strCoPhyName;
								}	
							}
							/* IF ENTRING PHYSICIAN NAME MANUALLY*/
							$qryInsertCoPhy = "insert into patient_multi_ref_phy 
														(patient_id, ref_phy_id, ref_phy_name, phy_type, created_by, created_by_date_time) Values ('".$_SESSION['patient']."', '".$arrCoPhyID[$intCoPhyArrKey]."', '".addslashes($strCoPhyArrVal)."', '2','".$_SESSION['authId']."', '".date('Y-m-d H:i:s')."')";
							$rsInsertCoPhy = imw_query($qryInsertCoPhy);
						}
					}
					elseif($arrHidId[$intCoPhyArrKey]){
						$qryUpdateCoPhy = "update patient_multi_ref_phy set patient_id = '".$_SESSION['patient']."', ref_phy_id = '".$arrCoPhyID[$intCoPhyArrKey]."',
													ref_phy_name = '".addslashes($strCoPhyArrVal)."', modified_by = '".$_SESSION['authId']."', modified_by_date_time = '".date('Y-m-d H:i:s')."' 
													where id = '".$arrHidId[$intCoPhyArrKey]."'";
						$rsUpdateCoPhy = imw_query($qryUpdateCoPhy);
					}
				}
			}
		}
		
		$qrySelpatRefPhy = "select TRIM(CONCAT(refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName,if(refPhy.MiddleName!='',' ',''),refPhy.Title)) as refName, refPhy.physician_Reffer_id,
							refPhy.Address1, refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.physician_phone, refPhy.physician_fax, refPhy.comments, refPhy.PractiseName
							from patient_multi_ref_phy pmrf INNER JOIN refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
							where pmrf.patient_id = '".$_SESSION['patient']."' and pmrf.phy_type = '2' and pmrf.status = '0' 
							ORDER BY pmrf.id";
		$rsSelpatRefPhy = imw_query($qrySelpatRefPhy);
		$counter = 0;
		$totalRows = imw_num_rows($rsSelpatRefPhy);
		if( $totalRows > 0){
			while($rowSelpatRefPhy = imw_fetch_array($rsSelpatRefPhy)){
				$counter++;
				//$strRef4Txt .= trim(stripslashes($rowSelpatRefPhy["refName"]))."; ";
				$refPhyName = trim(stripslashes($OBJCommonFunction->get_ref_phy_name($rowSelpatRefPhy["physician_Reffer_id"])));
				$refPhyStatus = $OBJCommonFunction->get_ref_phy_del_status((int)$rowSelpatRefPhy["physician_Reffer_id"]);
				$strRef4Txt .= $refPhyName."; ";
				$format_addres = format_ref_data($rowSelpatRefPhy);
				$strRef4PopTxt .= '<span class="col-xs-12 '.($refPhyStatus?'red-font':'').'"><b>&bull; '.$refPhyName.'</b><br>'.$format_addres.'</span>';
				$strRef4PopTxt .= ( $counter < $totalRows ) ? '<span class="col-xs-12 border-dashed"></span>' : ''; 

				if($intRefId4Hid == 0){
					$qryUpDatePatData = "update patient_data set co_man_phy_id = '".$rowSelpatRefPhy["physician_Reffer_id"]."', co_man_phy = '".addslashes($rowSelpatRefPhy["refName"])."' where id = '".$_SESSION['patient']."' LIMIT 1 ";
					imw_query($qryUpDatePatData);
					$intRefId4Hid = $rowSelpatRefPhy["physician_Reffer_id"];
				}
			}
		}
		else{
			$qryUpDatePatData = "update patient_data set co_man_phy_id = '', co_man_phy = '' where id = '".$_SESSION['patient']."' LIMIT 1 ";
			imw_query($qryUpDatePatData);
		}
		
		echo "DONE"."-!-".$strRef4Txt."-!-".$intRefId4Hid."-!-".$strRef4PopTxt;
	}
	elseif($_REQUEST["phyType"] == 3){
		$arrHidDelete = array();
		$arrHidDelete = explode('-', $_REQUEST['hidDeletePCPMedHxVal']);
		foreach($arrHidDelete as $intHidDeleteKey => $strHidDeleteVal){
			$arrHidDeleteVal = array();
			$arrHidDeleteVal = explode("~~", $strHidDeleteVal);
			$intHidDeleteVal = $intCounterHid = 0;
			$intHidDeleteVal = $arrHidDeleteVal[0];
			$intCounterHid = $arrHidDeleteVal[1];
			if((int)$intHidDeleteVal > 0){
				$qryDelete = "update patient_multi_ref_phy set status = '1', deleted_by = '".$_SESSION['authId']."', deleted_by_date_time = '".date('Y-m-d H:i:s')."' where id= '".$intHidDeleteVal."' ";
				$rsDelete = imw_query($qryDelete);
			}
		}
		$rqStrTxtPCPMedHxArr = "";
		$rqStrTxtPCPMedHxArr = $_REQUEST["strTxtPCPMedHxArr"];
		$intRefId4Hid = 0;
		$strRef4Txt = "";
		if(empty($rqStrTxtPCPMedHxArr) == false){
			$arrPCPMedHxArr = array();
			$arrPCPMedHxArr = explode("!$@$!",$rqStrTxtPCPMedHxArr);
			if(count($arrPCPMedHxArr) > 0){
				$rqStrHidPCPMedHxId = "";
				$rqStrHidPCPMedHxId = $_REQUEST["strHidPCPMedHxId"];			
				$arrHidId = array();
				$arrHidId = explode("!$@$!",$rqStrHidPCPMedHxId);
				//pre($arrHidId,1);
				
				$rqStrHidPCPMedHxArr = "";
				$rqStrHidPCPMedHxArr = $_REQUEST["strHidPCPMedHxArr"];
				$arrPCPMedHxID = array();
				$arrPCPMedHxID = explode("!$@$!",$rqStrHidPCPMedHxArr);
				
				foreach((array)$arrPCPMedHxArr as $intPCPMedHxArrKey => $strPCPMedHxArrVal){
					if(empty($arrHidId[$intPCPMedHxArrKey]) == true){
						if(empty($strPCPMedHxArrVal) == false){
							/* IF ENTRING PHYSICIAN NAME MANUALLY*/
							if($arrPCPMedHxID[$intPCPMedHxArrKey] == '' || $arrPCPMedHxID[$intPCPMedHxArrKey] == 0){
								list($intPCPPhyId, $strPCPPhyName) = $OBJCommonFunction->chk_create_ref_phy($strPCPMedHxArrVal);
								if((empty($intPCPPhyId) == false) && (empty($strPCPPhyName) == false)){
									$arrPCPMedHxID[$intPCPMedHxArrKey] = $intPCPPhyId;
									$strPCPMedHxArrVal = $strPCPPhyName;
								}	
							}
							/* IF ENTRING PHYSICIAN NAME MANUALLY*/
							$qryInsertPCPMedHx = "insert into patient_multi_ref_phy 
														(patient_id, ref_phy_id, ref_phy_name, phy_type, created_by, created_by_date_time) Values ('".$_SESSION['patient']."', '".$arrPCPMedHxID[$intPCPMedHxArrKey]."', '".addslashes($strPCPMedHxArrVal)."', '3','".$_SESSION['authId']."', '".date('Y-m-d H:i:s')."')";
							$rsInsertPCPMedHx = imw_query($qryInsertPCPMedHx);
						}
					}
					elseif($arrHidId[$intPCPMedHxArrKey]){
						$qryUpdatePCPMedHx = "update patient_multi_ref_phy set patient_id = '".$_SESSION['patient']."', ref_phy_id = '".$arrPCPMedHxID[$intPCPMedHxArrKey]."',
													ref_phy_name = '".addslashes($strPCPMedHxArrVal)."', modified_by = '".$_SESSION['authId']."', modified_by_date_time = '".date('Y-m-d H:i:s')."' 
													where id = '".$arrHidId[$intPCPMedHxArrKey]."'";
						$rsUpdatePCPMedHx = imw_query($qryUpdatePCPMedHx);
					}
				}
			}
		}
			
		$qrySelpatRefPhy = "select TRIM(CONCAT(refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName,if(refPhy.MiddleName!='',' ',''),refPhy.Title)) as refName, refPhy.physician_Reffer_id,
							refPhy.Address1, refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.physician_phone, refPhy.physician_fax, refPhy.comments, refPhy.PractiseName
							from patient_multi_ref_phy pmrf INNER JOIN refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
							where pmrf.patient_id = '".$_SESSION['patient']."' and pmrf.phy_type IN(3,4) and pmrf.status = '0' 
							ORDER BY pmrf.id";
		$rsSelpatRefPhy = imw_query($qrySelpatRefPhy);
		$tmpArr = array();
		$counter = 0;
		$totalRows = imw_num_rows($rsSelpatRefPhy);
		if( $totalRows > 0){
			while($rowSelpatRefPhy = imw_fetch_array($rsSelpatRefPhy)){
				$counter++;
				if(!in_array($rowSelpatRefPhy["physician_Reffer_id"],$tmpArr))
				{
					//$strRef4Txt .= trim(stripslashes($rowSelpatRefPhy["refName"]))."; ";
					$refPhyName = trim(stripslashes($OBJCommonFunction->get_ref_phy_name($rowSelpatRefPhy["physician_Reffer_id"])));
					$refPhyStatus = $OBJCommonFunction->get_ref_phy_del_status((int)$rowSelpatRefPhy["physician_Reffer_id"]);
					$strRef4Txt .= $refPhyName."; ";
					$format_addres = format_ref_data($rowSelpatRefPhy);
					$strRef4PopTxt .= '<span class="col-xs-12 '.($refPhyStatus?'red-font':'').'"><b>&bull; '.$refPhyName.'</b><br>'.$format_addres.'</span>';
					$strRef4PopTxt .= ( $counter < $totalRows ) ? '<span class="col-xs-12 border-dashed"></span>' : ''; 
					if($intRefId4Hid == 0){
						$qryUp = "update patient_data set primary_care_phy_id = '".$rowSelpatRefPhy["physician_Reffer_id"]."', primary_care_phy_name = '".addslashes($rowSelpatRefPhy["refName"])."' where id = '".$_SESSION['patient']."' LIMIT 1 ";
						imw_query($qryUp);
						$qryUpMedGH = "update general_medicine set med_doctor = '".addslashes($rowSelpatRefPhy["refName"])."' where patient_id = '".$_SESSION['patient']."' LIMIT 1 ";
						imw_query($qryUpMedGH);
						$intRefId4Hid = $rowSelpatRefPhy["physician_Reffer_id"];
					}
					$tmpArr[] = $rowSelpatRefPhy["physician_Reffer_id"];
				}
			}
		}
		else{
			$qryUp = "update patient_data set primary_care_phy_id = '0', primary_care_phy_name = '' where id = '".$_SESSION['patient']."' LIMIT 1 ";
			imw_query($qryUp);
			$qryUpMedGH = "update general_medicine set med_doctor = '' where patient_id = '".$_SESSION['patient']."' LIMIT 1 ";
			imw_query($qryUpMedGH);
		}
		
		echo "DONE"."-!-".$strRef4Txt."-!-".$intRefId4Hid."-|-".$strRef4PopTxt;
	}
	elseif($_REQUEST["phyType"] == 4){
		$arrHidDelete = array();
		$arrHidDelete = explode('-', $_REQUEST['hidDeletePCPDemoVal']);
		foreach($arrHidDelete as $intHidDeleteKey => $strHidDeleteVal){
			$arrHidDeleteVal = array();
			$arrHidDeleteVal = explode("~~", $strHidDeleteVal);
			$intHidDeleteVal = $intCounterHid = 0;
			$intHidDeleteVal = $arrHidDeleteVal[0];
			$intCounterHid = $arrHidDeleteVal[1];
			if((int)$intHidDeleteVal > 0){
				$qryDelete = "update patient_multi_ref_phy set status = '1', deleted_by = '".$_SESSION['authId']."', deleted_by_date_time = '".date('Y-m-d H:i:s')."' where id= '".$intHidDeleteVal."' ";
				$rsDelete = imw_query($qryDelete);
			}
		}
		$rqStrTxtPCPDemoArr = "";
		$rqStrTxtPCPDemoArr = $_REQUEST['strTxtPCPDemoArr'];
		$intRefId4Hid = 0;
		$strRef4Txt = "";
		if(empty($rqStrTxtPCPDemoArr) == false){
			$arrPCPDemoArr = array();
			$arrPCPDemoArr = explode("!$@$!",$rqStrTxtPCPDemoArr);
			if(count($arrPCPDemoArr) > 0){
				$rqStrHidPCPDemoIdID = "";
				$rqStrHidPCPDemoIdID = $_REQUEST["strHidPCPDemoIdID"];			
				$arrHidId = array();
				$arrHidId = explode("!$@$!",$rqStrHidPCPDemoIdID);
				
				$rqStrHidPCPDemoArrID = "";
				$rqStrHidPCPDemoArrID = $_REQUEST["strHidPCPDemoArrID"];			
				$arrPCPDemoID = array();
				$arrPCPDemoID = explode("!$@$!",$rqStrHidPCPDemoArrID);
				
				foreach((array)$arrPCPDemoArr as $intPCPDemoArrKey => $strPCPDemoArrVal){
					if(empty($arrHidId[$intPCPDemoArrKey]) == true){
						if(empty($strPCPDemoArrVal) == false){
							/* IF ENTRING PHYSICIAN NAME MANUALLY*/
							if($arrPCPDemoID[$intPCPDemoArrKey] == '' || $arrPCPDemoID[$intPCPDemoArrKey] == 0){
								list($intPCPDemoPhyId, $strPCPDemoPhyName) = $OBJCommonFunction->chk_create_ref_phy($strPCPDemoArrVal);
								if((empty($intPCPDemoPhyId) == false) && (empty($strPCPDemoPhyName) == false)){
									$arrPCPDemoID[$intPCPDemoArrKey] = $intPCPDemoPhyId;
									$strPCPDemoArrVal = $strPCPDemoPhyName;
								}	
							}
							/* IF ENTRING PHYSICIAN NAME MANUALLY*/
							$qryInsertPCPDemo = "insert into patient_multi_ref_phy 
														(patient_id, ref_phy_id, ref_phy_name, phy_type, created_by, created_by_date_time) Values ('".$_SESSION['patient']."', '".$arrPCPDemoID[$intPCPDemoArrKey]."', '".addslashes($strPCPDemoArrVal)."', '4','".$_SESSION['authId']."', '".date('Y-m-d H:i:s')."')";
							$rsInsertPCPDemo = imw_query($qryInsertPCPDemo);
						}
					}
					elseif($arrHidId[$intPCPDemoArrKey]){
						$qryUpdatePCPDemo = "update patient_multi_ref_phy set patient_id = '".$_SESSION['patient']."', ref_phy_id = '".$arrPCPDemoID[$intPCPDemoArrKey]."',
													ref_phy_name = '".addslashes($strPCPDemoArrVal)."', modified_by = '".$_SESSION['authId']."', modified_by_date_time = '".date('Y-m-d H:i:s')."' 
													where id = '".$arrHidId[$intPCPDemoArrKey]."'";
						$rsUpdatePCPDemo = imw_query($qryUpdatePCPDemo);
					}
				}
			}
		}
		
		$qrySelpatRefPhy = "select TRIM(CONCAT(refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName,if(refPhy.MiddleName!='',' ',''),refPhy.Title)) as refName, refPhy.physician_Reffer_id,
							refPhy.Address1, refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.physician_phone, refPhy.physician_fax, refPhy.comments, refPhy.PractiseName
							from patient_multi_ref_phy pmrf INNER JOIN refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
							where pmrf.patient_id = '".$_SESSION['patient']."' and pmrf.phy_type IN(3,4) and pmrf.status = '0' 
							ORDER BY pmrf.id";
		$rsSelpatRefPhy = imw_query($qrySelpatRefPhy);
		$counter = 0;
		$totalRows = imw_num_rows($rsSelpatRefPhy);
		if( $totalRows > 0){
			while($rowSelpatRefPhy = imw_fetch_array($rsSelpatRefPhy)){
				$counter++;	
				//$strRef4Txt .= trim(stripslashes($rowSelpatRefPhy["refName"]))."; ";
				$refPhyName = trim(stripslashes($OBJCommonFunction->get_ref_phy_name($rowSelpatRefPhy["physician_Reffer_id"])));
				$refPhyStatus = $OBJCommonFunction->get_ref_phy_del_status((int)$rowSelpatRefPhy["physician_Reffer_id"]);
				$strRef4Txt .= $refPhyName."; ";
				$format_addres = format_ref_data($rowSelpatRefPhy);
				$strRef4PopTxt .= '<span class="col-xs-12 '.($refPhyStatus?'red-font':'').'"><b>&bull; '.$refPhyName.'</b><br>'.$format_addres.'</span>';
				$strRef4PopTxt .= ( $counter < $totalRows ) ? '<span class="col-xs-12 border-dashed"></span>' : ''; 
				if($intRefId4Hid == 0){
					$qryUp = "update patient_data set primary_care_phy_id = '".$rowSelpatRefPhy["physician_Reffer_id"]."', primary_care_phy_name = '".addslashes($rowSelpatRefPhy["refName"])."' where id = '".$_SESSION['patient']."' LIMIT 1 ";
					imw_query($qryUp);
					$qryUpMedGH = "update general_medicine set med_doctor = '".addslashes($rowSelpatRefPhy["refName"])."' where patient_id = '".$_SESSION['patient']."' LIMIT 1 ";
					imw_query($qryUpMedGH);
					$intRefId4Hid = $rowSelpatRefPhy["physician_Reffer_id"];
				}
			}
		}
		else{
			$qryUp = "update patient_data set primary_care_phy_id = '0', primary_care_phy_name = '' where id = '".$_SESSION['patient']."' LIMIT 1 ";
			imw_query($qryUp);
			$qryUpMedGH = "update general_medicine set med_doctor = '' where patient_id = '".$_SESSION['patient']."' LIMIT 1 ";
			imw_query($qryUpMedGH);
		}
		
		echo "DONE"."-!-".$strRef4Txt."-!-".$intRefId4Hid."-!-".$strRef4PopTxt;
	}
}
?>