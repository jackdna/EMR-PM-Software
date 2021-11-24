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
require_once("../../../config/globals.php");

$browserIpad='';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true || stristr($_SERVER['HTTP_USER_AGENT'], 'Safari') == true) {
	
	$browserIpad = 'yes';
}
$consentQry = "SELECT cf.consent_form_id,cf.consent_form_name,cf.cat_id,cc.category_name as consent_category_name FROM consent_form cf 
			   INNER JOIN consent_category cc ON(cc.cat_id=cf.cat_id)
			   WHERE 1=1 ORDER BY cc.category_name,cf.consent_form_name	
			  ";
$consentRes = imw_query($consentQry) or die(imw_error());
$consent_form_arr = array();
if(imw_num_rows($consentRes)>0) {
	while($consentRow = imw_fetch_array($consentRes)) {
		$consent_form_id 								= $consentRow['consent_form_id'];
		$consent_form_name 								= stripslashes($consentRow['consent_form_name']);
		$consent_category_name 							= stripslashes($consentRow['consent_category_name']);
		$consent_form_arr[$consent_form_id] 			= $consent_form_name;
		$consent_category_name_arr[$consent_form_id] 	= $consent_category_name;
	}
}

//saving records
$msgSave="";
if(isset($_POST['action']) && $_POST['action'] == "save"){
	$iPortalConsentNme=implode(",",$_REQUEST['consent_form_name']);
	$qrySaveIportalSettings = " UPDATE facility set iportal_consent = '".$iPortalConsentNme."' WHERE facility_type=1 ";
	
	$qrySaveIportalSettingsRsId = imw_query($qrySaveIportalSettings);
	if(!$qrySaveIportalSettingsRsId){
		echo ("Error : ". imw_error()."<br>".$qrySaveIportalSettings);
	}
	else{
		$msgSave="Record Saved Successfully";
	}
	
} 

$ipcQry = "select iportal_consent from facility WHERE facility_type=1 ";
$ipcRes = imw_query($ipcQry);
if(imw_num_rows($ipcRes)>0) {
	$ipcRow = imw_fetch_array($ipcRes);
	$iportalConsentForm = $ipcRow["iportal_consent"];
	$iportalConsentFormArr = explode(",",$iportalConsentForm);
}

?>
<!DOCTYPE html>
<html>
<head>
<title>imwemr</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="../../themes/default/common.css" type="text/css">
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/common.js"></script>
<script type="text/javascript" src="../../common/script_function.js"></script>

<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../js/jquery.multiSelect.js"></script>

<script type="text/javascript">
var browserIpad = '<?php echo $browserIpad;?>';
$(document).ready( function() {
	if(browserIpad!='yes') {
		$("#consent_form_name").multiSelect({noneSelected:'Select All',listHeight:'440'});
	}
	show_loading_image('none');
	});

function save_action(){
	show_loading_image('block');
	document.getElementById("frmiPortalConsent").submit();
}

function show_loading_image(val){
	document.getElementById("loading_img").style.display = val;
}

function newPackCat(objVal){
	if(objVal == 'Other'){
		document.getElementById('packSpanId').style.display='none';
		document.getElementById('packSpanTextId').style.display='inline-block';
	}else {
		location.href = 'open.php?edit_id='+objVal;	
	}
}
function restorePackCat(obj) {
	obj.value='';
	document.getElementById('packSpanTextId').style.display='none';
	document.getElementById('packSpanId').style.display='inline-block';
}
</script>
    <script src = "../../js/dragresize.js"></script>
    </head>
    <body class="body_c mainBgClr" topmargin="0" leftmargin="0" rightmargin="0">
	<div align="center" id="loading_img" width="100%" style="display:none; top:250px; left:530px; z-index:1000; position:absolute;">
	<img src="../../../images/loading_image.gif">
    
	</div>
<?php
$iportalQry = "SELECT package_category_id, package_category_name, package_consent_form FROM consent_package WHERE delete_status!='yes' ORDER BY package_category_name";
$iportalRes = imw_query($iportalQry) or die(imw_error());
while($iportalRow = imw_fetch_assoc($iportalRes)) {
	$iportalArr[] = $iportalRow;
}

?>    
<div id="divCommonAlertMsgProcTemplate"></div>
        <div align="center" id="loading_img" width="100%" style="display:none; top:220px; left:470px; z-index:1000; position:absolute;"><img src="../../../images/loading_image.gif"></div>
        <form name="frmiPortalConsent" id="frmiPortalConsent" action="" method="post">
            <input type="hidden" id="action" name="action" value="save">
            <input type="hidden" id="div_doctor_id" name="div_doctor_id" value="">
        <table width="100%" border="0" cellpadding="0" class="mainBgClr">
            <tr>									
                <td  class="section_header">iPortal Consent Forms</td>									
            </tr>
            <TR>
                <TD valign="middle"  nowrap="nowrap" >
                    <table class="alignLeft table_collapse"  bgcolor="#FFE2C6">
                        <TR valign="top" class="tblBg">
                            <TD height="40" class="text_10b valignTop nowrap" align="left" style="width:100px;">Select iPortal Consent Forms</TD>
                            <Td height="40" class="text_10 valignTop nowrap" align="center">
                            	<select style="width:525px; margin:0px;" class="text" id="consent_form_name" name="consent_form_name[]" multiple >
                                    <option value="">--Select--</option>
                                    <?php 
                                        foreach($consent_form_arr as $consentFormKey => $consentFormName){
                                            $sel='';
                                            if(in_array($consentFormKey,$iportalConsentFormArr)) {
                                                $sel = "SELECTED";	
                                            }
                                            if(!in_array($consent_category_name_arr[$consentFormKey],$consentLabelArr)){
                                                $consentLabel = stripslashes($consent_category_name_arr[$consentFormKey]);
                                                $consentLabelArr[] =$consentLabel;
                                            ?>
                                                <optgroup label="<?php echo $consentLabel;?>">
                                            <?php	
                                            }
                                            
                                            ?>
                                            
                                                    <option value="<?php echo $consentFormKey; ?>" <?php echo $sel; ?>><?php echo $consentFormName; ?></option>
                                            <?php
                                            if(!in_array($consentLabel,$consentLabelArr)){
                                            ?>
                                                </optgroup>
                                            <?php
                                            }
                                        }
                                    ?>
                                    
                                </select>
                                
                            </Td>
                        </tr>
                    </table>
                </td>
            </TR>
            
            <Tr valign="middle">
                <td valign="middle"  nowrap="nowrap" >
                    <table class="alignLeft"  style="width:430px;" >
                        <TR valign="top">
                            <td style="width:65px; " class="alignRight nowrap">
                            <input  type="button" value="Save" id="save"   class="dff_button"  onMouseOver="button_over('save')" onMouseOut="button_over('save', '')" onClick="javascript:save_action();">
                            <input type="button" id="close" class="dff_button"  onMouseOver="button_over('close')" onMouseOut="button_over('close', '')" value="Close"  onClick="window.close();"></td>
                        </TR>
                    </table>
                </td>
            </Tr>
        </table>
        </form>
        
        <?php
		if($msgSave) {
		?>
			<script>top.fancyAlert("Record Saved Successfully\n","imwemr","",top.document.getElementById("divCommonAlertMsgProcTemplate"),'','','','',false,10, 300, '');
				//window.opener.top.fmain.all_data.iFrameDocuments.location.href='../../admin/iportal/index.php';
			</script>
		<?php
		}
		
        ?>
    </body>
</html>