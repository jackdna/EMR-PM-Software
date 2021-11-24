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
function setAccountStatus(){
	var status_id = document.getElementById('status_id').value;
	if(status_id!=''){
		window.opener.top.show_loading_image("show");
		var patId = '<?php echo $_REQUEST['patId_str'];?>';
		var selectedText = $('#status_id option:selected').text();
		$.ajax({ 
			url: "../accounting/setPatAccountStatus.php?patId="+patId+"&acId="+status_id+"&selectedText="+selectedText+"&callFrom=reports",
			success: function(updateSts){
				window.opener.top.show_loading_image("hide");
				self.close();
				window.opener.get_sch_report();
			}
		});
	}else{
		alert('Please select account status.');
	}
}
</script>
</head>
<body>
<table class="table_collapse" style="margin:0 auto; width:100%;">
    <tr class="page_block_heading_patch">
        <th class="alignLeft">&nbsp;Set Patient Account Status</th>
    </tr>
	<tr>
		<td>
			<table class="bg1" cellspacing="1" cellpadding="1" style="padding-bottom:0px; width:100%;">
				<tr><td class="text_b_w" style="width:100px">Pat. Id</td><td class="text_b_w" style="width:auto">Patient Name</td></tr>
            </table>
            <div style="height:220px; overflow-y:scroll;"> 
            <table class="table_collapse cellBorder3" >
                <?php
				while($dataRes = imw_fetch_array($dataRs)){
					$patient_name = $dataRes['lname'].', ';
					$patient_name .= $dataRes['fname'].' ';
					$patient_name .= $dataRes['mname'];
					$patient_name = ucfirst(trim($patient_name));
					if($patient_name[0] == ','){
						$patient_name = substr($patient_name,1);
					}
					echo '<tr style="height:22px;"><td style="background-color:#FFF; width:100px;" bgColor="#FFF" class="text_12" >'.$dataRes['id'].'</td><td style="background-color:#FFF;width:auto;" class="text_12">'.$patient_name.'</td></tr>';
				}
				?>
			</table>
            </div>
            <div style="width:100%; text-align:center; margin-top:5px" >
            	<strong>Account Status :</strong> 
                <select name="status_id" id="status_id" style="margin-left:5px; width:200px;">
                <option value="">Set Account Status</option>
				<?php
                $acStatusDDOptions='';
                $rs=imw_query("Select id, status_name FROM account_status WHERE del_status='0' ORDER BY status_name");
                while($res=imw_fetch_array($rs)){
                    $acStatusDDOptions.='<option value="'.$res['id'].'">'.$res['status_name'].'</option>';
                }
                echo $acStatusDDOptions;
				?>
                </select>
                <input type="button" name="btnStatus" id="btnStatus" value="Save" class="dff_button" onClick="setAccountStatus();">
            </div>
		</td>
	</tr>
</table>

</body>
</html>