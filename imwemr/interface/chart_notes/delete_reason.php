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
include_once(dirname(__FILE__) . "/../../config/globals.php");

//-----  Get data from remote server -------------------

$zRemotePageName = "delete_reason";
//require(dirname(__FILE__)."/get_chart_from_remote_server.inc.php");

//-----  Get data from remote server -------------------
include_once($GLOBALS['srcdir'] . "/classes/audit_common_function.php");
require_once($GLOBALS['srcdir'] . "/classes/cls_common_function.php");

$objManageData = new CLSCommonFunction;
//--- Save order set in database ----
$formId = $_SESSION['form_id'];
$patient_id = $_SESSION['patient'];
$logged_provider_id = $_SESSION['authId'];

//--- GET ALL ORDERS DETAILS ----
$sql = "select * from order_details";
$ordersQryRes = $objManageData->mysqlifetchdata($sql);
$ordersDetailsArr = array();
$inf_order_arr = array();
for($o=0;$o<count($ordersQryRes);$o++){
	$id = $ordersQryRes[$o]['id'];
	$ordersDetailsArr[$id] = $ordersQryRes[$o];
	$o_type = $ordersQryRes[$o]['o_type'];
	preg_match('/Information/',$o_type,$inf_check);
	if(count($inf_check)>0){
		$inf_order_arr[] = $id;
	}
}

$policyStatus = 0;
$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Order'];
    
if($policyStatus == 1){
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);		
}	
if($policyStatus == 1){
	 //Audit view data
	$qry = imw_query("select order_id from order_set_associate_chart_notes_details
			where order_set_associate_id = '$delete_id' and delete_status = '0'");
	while($qry_fet=imw_fetch_array($qry)){				
		$order_id=$qry_fet['order_id'];
		$data_arr=array();
		$data_arr["Operater_Id"] = $logged_provider_id;
		$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
		$data_arr["IP"] = $ip;
		$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
		$data_arr["URL"] = $URL;
		$data_arr["Browser_Type"] = $browserName;
		$data_arr["OS"] = $os;
		$data_arr["Machine_Name"] = $machineName;
		$data_arr["Date_Time"] = date('Y-m-d H:i:s');
		$data_arr["pid"] = $patient_id; 
		$data_arr["Table_Name"] = "order_set_associate_chart_notes";
		$data_arr["Pk_Id"] = $delete_id;	
		$data_arr["Data_Base_Field_Name"] = "order_set_associate_id";
		$data_arr["Data_Base_Field_Type"] = "int";
		$data_arr["Field_Label"] = "id";
		$data_arr["Category"] = "order";
		$data_arr["Action"] = "delete";
		$data_arr["Category_Desc"] = $ordersDetailsArr[$order_id]['name'];
		$data_arr["Filed_Text"] = $ordersDetailsArr[$order_id]['name'].' - '.$patient_id;
		AddRecords($data_arr,'audit_trail');
	}
}

//--- Delete from database  ----
if(empty($delete_id) == false){
	$query = "update order_set_associate_chart_notes set delete_status = '1',order_set_reason_text='".sqlEscStr($reason)."' 
			where order_set_associate_id = '$delete_id'";
	$rs = imw_query($query);
	//--- DELETE FROM DETAILS TABLES ----
	$query = "update order_set_associate_chart_notes_details set delete_status = '1',
				orders_reason_text ='".sqlEscStr($reason)."' 
			where order_set_associate_id = '$delete_id'";
	$rs = imw_query($query);
	
	echo "<script type='text/javascript'> 
		opener.reloadOrderSet();
		window.close();	 
	</script>";	
}

?>
<!DOCTYPE html>
<html lang="en">
        <title>Delete order set</title>
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" type="text/css">
            <style>
            .heading {
                color: #fff;
                background-color: #1b9e95;
                padding: 10px 15px;
            }
            </style>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script> 
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/script_function.js"></script>
    </head>
    <body>
        <div class="heading">Reason for Deleting</div>
        <div class="container-fluid">
            <form method="post" action="">
                <input type="hidden"  name="delete_id" value="<?php echo $_REQUEST['o_id'] ?>" />
                <div class="row">
                    <div class="col-sm-6">
                        <label>Enter Reason for Deleting</label>
                        <input type="text" class="form-control" name="reason" id="reason" />
                    </div>
                </div>
                <div class="text-center pd5" id="module_buttons">
                    <button type="submit" class="btn btn-success" name="sub" id="sub" value="Submit">Submit</button>
                    <button type="button" name="cancel" id="cancel" class="btn btn-danger" value="Cancel" onclick="javacript:window.close();">Cancel</button>
                </div>
<!--                <table style="width:100%">
                    <tr>
                        <td colspan="2">
                            <table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="8"><img src="/r4/images/edge_left.jpg" width="8"></td>
                                    <td width="100%" background="/r4/images/bottom_line.jpg"></td>
                                    <td width="8"><img src="/r4/images/edge_right.jpg" width="8"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>-->
            </form>
        </div>    
    </body>
</html>