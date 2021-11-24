<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include("../common/conDb.php");
include("../common/link_new_file.php");
$patient_in_waiting_id 	= $_REQUEST['patient_in_waiting_id'];
$patient_id 			= $_REQUEST['patient_id'];

$prevConsentDosQry 		= "SELECT DISTINCT(DATE_FORMAT(piwt.dos,'%m-%d-%Y')) as dosShow,piwt.patient_in_waiting_id as previousWaitingId FROM patient_in_waiting_tbl piwt,iolink_consent_filled_form icff 
							  WHERE piwt.dos < (SELECT dos FROM patient_in_waiting_tbl WHERE patient_in_waiting_id = '".$patient_in_waiting_id."' AND patient_status != 'Canceled')
								AND piwt.patient_id='".$patient_id."'
								AND piwt.patient_in_waiting_id = icff.fldPatientWaitingId ORDER BY piwt.dos DESC";
$prevConsentDosRes 		= imw_query($prevConsentDosQry) or die(imw_error());

?>
<LINK HREF="../css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<script src="../js/jsFunction.js"></script>
<script src="../js/epost.js"></script>
<script>
function copyConsentAjaxFun(currentWatingId) {
	var previousWaitingId='';
	if(document.getElementById('previousWaitingId')) {
		previousWaitingId = document.getElementById('previousWaitingId').value	
		if(!previousWaitingId) {
			alert('Please select DOS to copy consent forms.');
			document.getElementById('previousWaitingId').focus();	
		}
		if(previousWaitingId) {
			if(confirm('Copy previous consent form(s) ! Are you sure.')) {
				xmlHttp=GetXmlHttpObject()
				if (xmlHttp==null){
					alert ("Browser does not support HTTP Request")
					return
				 }
				var url="copyConsentFormAjax.php"
				url=url+"?previousWaitingId="+previousWaitingId
				url=url+"&currentWatingId="+currentWatingId
				xmlHttp.onreadystatechange=function() {
					if(xmlHttp.readyState==1) {
						if(top.document.getElementById("divScanAjaxLoadId")) {
							top.document.getElementById("divScanAjaxLoadId").style.display='block';
						}
					}
					if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
						if(top.document.getElementById("divScanAjaxLoadId")) {
							top.document.getElementById("divScanAjaxLoadId").style.display='none';
						}						
						alert(xmlHttp.responseText);
						if(top.consent_tree) {
							top.consent_tree.location.reload();
						}
					} 
				};
				xmlHttp.open("GET",url,true)
				xmlHttp.send(null)
			}
		}
	}
}
</script>

<table height="520"  bgcolor="#ECF1EA" cellpadding="0" cellspacing="0" border="0" align="center" width="95%"  >
    <tr height="10">
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr valign="top" height="30">
        <td colspan="2" align="left" valign="top" class="text_10b" style="padding-left:240px;" nowrap="nowrap">Copy Previous Consent Forms</td>
    </tr>
    <?php 
	if(imw_num_rows($prevConsentDosRes)>0) {?>
        <tr valign="top" height="465">
            <td align="right" class="text_10" style="font-size:11px; height:20px;  padding-right:5px; padding-top:4px;" >
                <select name="previousWaitingId" id="previousWaitingId" class="tst11"  style=" width:110px;">
                    <option value="">Select DOS</option>
            <?php 	while($prevConsentDosRow = imw_fetch_array($prevConsentDosRes)) {
                        $previousConsentWaitingId = $prevConsentDosRow['previousWaitingId'];
                        $prevConsentDos = $prevConsentDosRow['dosShow'];?>
                        <option value="<?php echo $previousConsentWaitingId;?>"><?php echo $prevConsentDos;?></option>
            <?php	}?>
                </select>
            </td>
            <td align="left" valign="top"> 
                <a style="width:120px; " href="#" onClick="MM_swapImage('consentCopyButton','','../images/copy_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('consentCopyButton','','../images/copy_hover.gif',1)">
                    <img src="../images/copy.gif" name="consentCopyButton"  border="0" id="consentCopyButton" alt="Copy" onClick="copyConsentAjaxFun('<?php echo $patient_in_waiting_id;?>');" style="cursor:pointer;" />
                </a>
            </td>
        </tr>
    <?php 
	}else { ?>
        <tr valign="top" height="465">
            <td colspan="2" align="left" valign="top" class="tst11b" style="padding-left:240px;" nowrap="nowrap">No previous consent form(s) exist to copy</td>
        </tr> 	
    <?php 
	}?>
</table>
		           
<script>
	if(top.document.getElementById("anchorShow")) {
		top.document.getElementById("anchorShow").style.display = 'none';
	}
	if(top.document.getElementById("iolinkUploadBtn")) {
		top.document.getElementById("iolinkUploadBtn").style.display = 'none';
	}
	if(top.document.getElementById("PrintBtn")) {
		top.document.getElementById("PrintBtn").style.display = 'none';
	}
	if(top.document.getElementById("deleteSelected")) {
		top.document.getElementById("deleteSelected").style.display = 'none';
	}
</script>