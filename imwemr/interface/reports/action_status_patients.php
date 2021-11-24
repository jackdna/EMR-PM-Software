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

$without_pat = "yes";
require_once("reports_header.php");
// ---- UPDATE-----
$patId_str = $_REQUEST['patId_str'];

if($patId_str!=''){
	// SET STATUS TO COLLECTION
	$collectionDate = date('Y-m-d');
	$collectionId = get_account_status_id('collections');
	$stscollectionIds = get_account_status_id_collections();
	$patRs=imw_query("Update patient_data SET pat_account_status=".$collectionId." WHERE id IN(".$patId_str.") AND pat_account_status NOT IN(".$stscollectionIds.")");
	// UPDATE ALL ENCOUNTER OF PATIENT HAVING totalBalance > 0
	$rs=imw_query("Select encounter_id FROM patient_charge_list WHERE collection!='true' AND totalBalance>0 AND patient_id IN(".$patId_str.")");
	while($res = imw_fetch_array($rs)){
		$arrTempEnc[$res['encounter_id']]=$res['encounter_id'];
	}
	if(sizeof($arrTempEnc)>0){
		$strTempEnc=implode(',', $arrTempEnc);

		$updateStr = "UPDATE patient_charge_list SET collection = 'true',collectionAmount=totalBalance,collectionDate = '".$collectionDate."' WHERE encounter_id IN (".$strTempEnc.")";
		$updateRs = imw_query($updateStr);

		$updateStr = "UPDATE report_enc_detail SET collection = 'true',collectionAmount=proc_balance,collectionDate = '".$collectionDate."' WHERE encounter_id IN (".$strTempEnc.")";
		$updateRs = imw_query($updateStr);	
	}
	
	
		
	// DISPLAY SELECTED PATIENTS	
	$qry="Select id, fname, mname, lname FROM patient_data WHERE id IN(".$patId_str.") ORDER BY lname";
	$dataRs=imw_query($qry);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>imwemr - Reports</title>
<link rel="stylesheet" href="<?php print $GLOBALS['rootdir']; ?>/themes/default/common.css" type="text/css">
<link rel="stylesheet" href="<?php print $GLOBALS['rootdir']; ?>/themes/style_patient.css" type="text/css">
<style>
	.text_b_w{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		color:#000000;
		background-color:#BCD5E1;
		border-style:solid;
		border-color:#FFFFFF;
		border-width: 1px; 
	}
	.text_12b{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		color:#000000;
	}
	.text_12{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		color:#000000;
	}
	.total-row{
		height:1px; 
		padding:0px;
		background:#009933;		
	}
</style>    
<script type="text/javascript" src="<?php print $GLOBALS['webroot']; ?>/js/jquery.js"></script>

<script type="text/javascript">
function setActionCode(){
	var actionCode = document.getElementById('actionCode').value;
	if(actionCode!=''){
		window.opener.top.show_loading_image("show");
		
		var patId_str = '<?php echo $_REQUEST['patId_str'];?>';
		$.ajax({ 
			url: "set_patient_action_status.php?patId_str="+patId_str+"&actionCode="+actionCode,
			success: function(updateSts){
				window.opener.top.show_loading_image("hide");
				self.close();
				window.opener.get_sch_report();
			}
		});
	}else{
		alert('Please select action code');
	}
}
</script>
</head>
<body>
<table class="table_collapse" style="margin:0 auto; width:100%; background-color:#ffffff;">
    <tr>
        <th class="rptbx3">Set Patient Action Code</th>
    </tr>
	<tr>
		<td>
			<table class="bg1" cellspacing="1" cellpadding="1" style="padding-bottom:0px; width:100%;">
				<tr><td class="text_b_w" style="width:100px">&nbsp;Pat. Id</td><td class="text_b_w" style="width:auto">Patient Name</td></tr>
            </table>
            <div style="height:210px; overflow-y:scroll;"> 
            <table width="100%" cellpadding="0" cellspacing="1" border="0" bgcolor="#FFF3E8">
                <?php
				while($dataRes = imw_fetch_array($dataRs)){
					$patient_name = $dataRes['lname'].', ';
					$patient_name .= $dataRes['fname'].' ';
					$patient_name .= $dataRes['mname'];
					$patient_name = ucfirst(trim($patient_name));
					if($patient_name[0] == ','){
						$patient_name = substr($patient_name,1);
					}
					echo '<tr><td style="background-color:#FFF; width:100px;" bgColor="#FFF" class="text_12" >&nbsp;'.$dataRes['id'].'</td><td style="background-color:#FFF;width:auto;" class="text_12">'.$patient_name.'</td></tr>';
				}
				?>
			</table>
            </div>
            <div style="width:100%; text-align:center; margin-top:5px; margin-bottom:10px;" >
            	<strong>Action Code :</strong> 
                
                <select name="actionCode" id="actionCode" style="margin-left:5px; width:200px;" class="selectpicker">
                <option value="">Set Action Code</option>
				<?php
                    $nextActionDDOptions='';
                    $rs=imw_query("Select id,action_status FROM patient_next_action WHERE del_status='0' ORDER BY action_status");
                    while($res=imw_fetch_array($rs)){
                        $nextActionDDOptions.='<option value="'.$res['id'].'" '.$sel.'>'.$res['action_status'].'</option>';
                    }
					echo $nextActionDDOptions;
				?>               	
                </select>
                <input type="button" name="btnAction" id="btnAction" value="Save" class="btn btn-success" onClick="setActionCode();">
            </div>
		</td>
	</tr>
</table>

</body>
</html>