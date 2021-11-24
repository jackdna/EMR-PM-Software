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
File: reff_phy_add.php
Purpose: Show referring physician multiple addresses
Access Type: Direct 
*/
include_once(dirname(__FILE__)."/../../../config/globals.php");
$physician_Reffer_id = $_POST['id'];
if($_POST['req_type']=='email')
{
	$str = '';
	if($physician_Reffer_id != "" && $physician_Reffer_id >0 ){
	$sql = "SELECT primary_id FROM refferphysician WHERE physician_Reffer_id = '".$physician_Reffer_id."' AND primary_id > 0";
	$res = imw_query($sql);
	if(imw_num_rows($res) > 0){
		$row = imw_fetch_assoc($res);
		$physician_Reffer_id = $row['primary_id'];
	}
	
	$sql = "SELECT * FROM refferphysician WHERE physician_Reffer_id = '".$physician_Reffer_id."' OR primary_id = '".$physician_Reffer_id."' AND delete_status = 0";
	$res = imw_query($sql);
	
	$count = 1;
	$hidd_id = $_POST['hidd_id'];
	$hidd_id2 = $_POST['hidd_id2'];
	$email_id2 = $_POST['email_id2'];
	$email_ref_name_id = $_POST['email_ref_name_id'];
	if(imw_num_rows($res) > 1){
		$str .= "<div class='row'>";
		while($row = imw_fetch_assoc($res)){
				if($count > 1){
					$pd_top = 'pt10';
				}
				$LastName = stripslashes($row["LastName"]);
				$FirstName = stripslashes($row["FirstName"]);
				$MiddleName = stripslashes($row["MiddleName"]);
				$reffName = trim($LastName.', '.$FirstName.' '.$MiddleName);
					$str .= "<div class='col-sm-12 ".$pd_top."'>";
					$checked = ($_POST['id'] == $row['physician_Reffer_id'])?"checked":"";
					$str .= '<div class="radio radio-inline" ><input type="radio" id="reff_address['.$count.']" name="reff_address['.$count.']" value="'.$row['physician_Reffer_id'].'" onclick="set_reff_add(\''.$row['physician_Reffer_id'].'\',\''.$hidd_id.'\',this,\''.$row['physician_email'].'\',\''.$hidd_id2.'\',\''.$email_id2.'\',\''.$reffName.'\',\''.$email_ref_name_id.'\');" '.$checked.'/>';
					$str .= '<label for="reff_address['.$count.']">';
						$str .= $row['Address1'];
						$str .= ($row['Address2'] != "")?", ".$row['Address2']:"";
						$str .= ($row['City'] != "")?", ".$row['City']:"";
						$str .= ($row['State'] != "")?", ".$row['State']:"";
						$str .= ($row['ZipCode'] != "")?" ".$row['ZipCode']:"";
						$str .= ($row['physician_phone'] != "")?"<br> Phone:".$row['physician_phone']:"";
						$str .= ($row['physician_email'] != "")?"<br>Email:".$row['physician_email']:"";
					$str .= "</label></div>";
				$str .= "</div>";
				$str .= '<div class="clearfix"></div>';
				$count++;
		}
		$str .= "</div>";
	}
	}	
}
else
{
	$str = '';
	if($physician_Reffer_id != "" && $physician_Reffer_id >0 ){
	$sql = "SELECT primary_id FROM refferphysician WHERE physician_Reffer_id = '".$physician_Reffer_id."' AND primary_id > 0";
	$res = imw_query($sql);
	if(imw_num_rows($res) > 0){
		$row = imw_fetch_assoc($res);
		$physician_Reffer_id = $row['primary_id'];
	}
	
	$sql = "SELECT * FROM refferphysician WHERE physician_Reffer_id = '".$physician_Reffer_id."' OR primary_id = '".$physician_Reffer_id."' AND delete_status = 0";
	$res = imw_query($sql);
	
	$count = 1;
	$hidd_id = $_POST['hidd_id'];
	$hidd_id2 = $_POST['hidd_id2'];
	$fax_id2 = $_POST['fax_id2'];
	$fax_ref_name_id = $_POST['fax_ref_name_id'];
	if(imw_num_rows($res) > 1){
		$str .= "<div class='row'>";
		while($row = imw_fetch_assoc($res)){
				if($count > 1){
					$pd_top = 'pt10';
				}
				$LastName = stripslashes($row["LastName"]);
				$FirstName = stripslashes($row["FirstName"]);
				$MiddleName = stripslashes($row["MiddleName"]);
				$reffName = trim($LastName.', '.$FirstName.' '.$MiddleName);
				$str .= "<div class='col-sm-12 ".$pd_top."'>";
					$checked = ($_POST['id'] == $row['physician_Reffer_id'])?"checked":"";
					$str .= '<div class="radio radio-inline"><input type="radio" id="reff_address['.$count.']" name="reff_address['.$count.']" value="'.$row['physician_Reffer_id'].'" onclick="set_reff_add(\''.$row['physician_Reffer_id'].'\',\''.$hidd_id.'\',this,\''.$row['physician_fax'].'\',\''.$hidd_id2.'\',\''.$fax_id2.'\',\''.$reffName.'\',\''.$fax_ref_name_id.'\');" '.$checked.'/>';
					$str .= '<label for="reff_address['.$count.']">';
						$str .= $row['Address1'];
						$str .= ($row['Address2'] != "")?", ".$row['Address2']:"";
						$str .= ($row['City'] != "")?", ".$row['City']:"";
						$str .= ($row['State'] != "")?", ".$row['State']:"";
						$str .= ($row['ZipCode'] != "")?" ".$row['ZipCode']:"";
						$str .= ($row['physician_phone'] != "")?"<br> Phone:".$row['physician_phone']:"";
						$str .= ($row['physician_fax'] != "")?"<br>Fax:".$row['physician_fax']:"";
						$str .= (trim($row['PractiseName']) != "")?"<br>Practice Name: ".$row['PractiseName']:"";
						$str .= (trim($row['comments']) != "")?"<br>Comments: ".$row['comments']:"";
					$str .= "</label></div>";
				$str .= "</div>";
				$str .= '<div class="clearfix"></div>';
				$count++;
		}
		$str .= "</div>";
	}
	}
}
echo $str;
?>
