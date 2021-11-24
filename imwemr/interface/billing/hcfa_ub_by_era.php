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
 
include_once(dirname(__FILE__)."/../../config/globals.php"); 
$InsComp=$_REQUEST['InsComp'];
$vchl=$_REQUEST['vchl'];
$printHcfa=$_REQUEST['printHcfa'];
$vchld=$_REQUEST['vchld'];
$Printub="yes";
$only_show="yes";
$secInsId=array();
$terInsId=array();
$chld_ids_arr=array();
if($vchld!=""){
	$whr_chld=" and charge_list_detail_id in($vchld)";
}
$sel_qry = imw_query("Select charge_list_detail_id from patient_charge_list_details where del_status='0' and charge_list_id in($vchl) $whr_chld");
while($row=imw_fetch_array($sel_qry)){
	$chld_ids_arr[$row['charge_list_detail_id']]=$row['charge_list_detail_id'];
}
$chld_ids=implode(',',$chld_ids_arr);
if($vchl>0){
	//--- Print HCFA Form For Primary Insurance Company --------
	$validChargeListId=array();
	$validChargeListId[]= $vchl;
	$newFile="yes";
	if($print_paper_from_claims=="1"){
		if($printHcfa=="1"){
			require_once("print_hcfa_form_era.php");
		}else if($printHcfa=="2"){
			require_once("print_ub_form_era.php");
		}
	}else{
		if($printHcfa=="1"){
			require_once("print_hcfa_form.php");
		}else if($printHcfa=="2"){
			require_once("print_ub.php");
		}
	}
}
?>
<script type="text/javascript">
var nav_name=navigator.appVersion;
closeIT();
function closeIT(){
	var ie7 = (document.all && !window.opera && window.XMLHttpRequest) ? true : false; 
	if (ie7){
		window.open("","_parent",""); 
		window.close(); 
	}else{
		this.focus();
		self.opener = this;
		self.close();
	}
}
</script>