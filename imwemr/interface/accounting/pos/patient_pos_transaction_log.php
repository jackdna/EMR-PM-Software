<?php 
require_once(dirname(__FILE__).'/../../../config/globals.php');

$patient_id=($_REQUEST['patient_id'])?$_REQUEST['patient_id']:$_SESSION['patient'];
$operator_id=$_SESSION['authId'];
$login_facility=$_SESSION['login_facility'];

$qry_usr=imw_query("select * from users order by lname,fname asc");		
while($row_usr=imw_fetch_array($qry_usr)){
	$ins_operator_name_arr[$row_usr['id']] = substr($row_usr['fname'],0,1).substr($row_usr['lname'],0,1);
	$ins_operator_full_name_arr[$row_usr['id']] = $row_usr['lname'].', '.$row_usr['fname'];
	if($row_usr["Enable_Scheduler"]=='1' || in_array($row_usr["user_type"],$phy_id_cn)){
		$users_arr[$row_usr['id']] = $row_usr['lname'].', '.$row_usr['fname'];
	}
}


$cookieName="imedicwareposdevice_".$operator_id;
if(isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName]!='') {
    $poscookie=json_decode($_COOKIE[$cookieName],true);
    if(empty($poscookie)==false){
        if($poscookie['login_facility']==$login_facility) {
            if($poscookie['user_id']==$operator_id) {
                if($poscookie['expire_time']<=time()) {
                    unset($_COOKIE[$cookieName]);
                } else {
                    $defaultDevice=$poscookie['device_id'];
                }
            }
        }
    }
}


$devicesArr=array();
$devices_sql="Select *, tsys_device_details.id as d_id from tsys_device_details 
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
              WHERE device_status=0 
              AND tsys_device_details.facility_id='".$login_facility."' 
              AND merchant_status=0 
              ";
$resp = imw_query($devices_sql);
$devices_option = "";
$counter=0;
if ($resp && imw_num_rows($resp) > 0) {
    while ($row = imw_fetch_assoc($resp)) {
        $counter++;
        $ipAddress=$row['ipAddress'];
        $port=$row['port'];
        $device_url=$phpHTTPProtocol.$ipAddress.':'.$port;
        $selected='';
        if(!$defaultDevice && $counter==1) {
            $selected='selected="selected" ';
        } else {
            $selected=($row['d_id']==$defaultDevice)?'selected="selected" ':'';
        }
        $devices_option .= "<option ".$selected." data-device_ip='".$ipAddress."' data-device_url='".$device_url."' value='" . $row['d_id'] . "'>" . $row['deviceName'] . "</option>";
    }
}


$qry = "SELECT id,
				CONCAT(patient_data.lname,', ',patient_data.fname, ' - ',id) AS patient_name
				FROM patient_data 
				WHERE patient_data.id=$patient_id";
$res = imw_query($qry);
$patientData=imw_fetch_assoc($res);
$patient_name=$patientData['patient_name'];

$tsys_sale_response=array();
$sql='select id,patient_id,operator_id,laneId,scheduID,encounter_id,status,log_referenceNumber,ResponseMessage,TransactionNumber,transactionAmount,added_on,Account,ExpireDate,CardType,TransactionType,HostResponseMessage,motoType,motoOrderNumber,isRecurring,currentPaymentCount,paymentCount from tsys_possale_transaction where patient_id="'.$patient_id.'" order by added_on desc';
$sql_rs=imw_query($sql);
$sql_count_rs=imw_num_rows($sql_rs);
$sale_html='';
$return_html='';
if($sql_count_rs>0) {
    while($sale_row=imw_fetch_assoc($sql_rs)) {
        $TransactionType=trim($sale_row['TransactionType']);
        $transactionID=trim($sale_row['TransactionNumber']);
        $patient_id=trim($sale_row['patient_id']);
        $transactionAmount=trim($sale_row['transactionAmount']);
        switch($sale_row['CardType']) {
            case '01':
                $CardType='Visa';
                break;
            case '02':
                $CardType='MasterCard';
                break;
            case '03':
                $CardType='American Express';
                break;
            case '04':
                $CardType='Discover';
                break;
            case '99':
                $CardType='Other';
                break;
        }
        $paymentMethod=$CardType.' - '.$sale_row['Account'].' - '.$sale_row['ExpireDate'];
        
        if($sale_row['ResponseMessage']=='OK') {
            $sale_row['ResponseMessage']='SUCCESS';
        }
        $motoType='';
        if($sale_row['motoType']=='M' && $sale_row['motoOrderNumber']!='') {
            $motoType='Mail Order -(MOTO)';
        }else if($sale_row['motoType']=='T' && $sale_row['motoOrderNumber']!='') {
            $motoType='Phone Order -(MOTO)';
        }else if($sale_row['isRecurring']=='R') {
            $motoType='Recurring';
        }else if($sale_row['isRecurring']=='T') {
            $motoType='Installment - '.$sale_row['currentPaymentCount'].'/'.$sale_row['paymentCount'];
        }
        
        if($sale_row['laneID']!='' && $sale_row['encounter_id']!=0) {
            $laneID=$sale_row['laneID'];
        }
        if($sale_row['scheduID']!='' && $sale_row['encounter_id']!=0) {
            $scheduID=$sale_row['scheduID'];
        }
        if($sale_row['encounter_id']!='' && $sale_row['encounter_id']!=0) {
            $encounter_id=$sale_row['encounter_id'];
        }
        if($sale_row['log_referenceNumber']!='') {
            $log_referenceNumber=$sale_row['log_referenceNumber'];
        }
        $voidType=array("16","17","18","19","20","21","22");
        $sale_html.='<tr id="transRow_'.$transactionID.'">
                <td>'.date('m-d-Y h:i A', strtotime($sale_row['added_on'])).'</td>
                <td>'.$transactionID.'</td>
                <td>$'.$transactionAmount.'</td>
                <td>'.$paymentMethod.'</td>
                <td>'.$ins_operator_full_name_arr[$sale_row['operator_id']].'</td>
                <td>'.$sale_row['HostResponseMessage'].'</td>
                ';
                if($TransactionType=="01") {
                    $sale_html.='<td>SALE</td>';
                    //$sale_html.='<td><button type="button" class="btn btn-primary" id="returnbtn_'.$transactionID.'" onclick="return_transaction(\''.$transactionID.'\',\''.$transactionAmount.'\',\'02\',this)">Return</button>
                         //   &nbsp;<button type="button" class="btn btn-danger" id="voidbtn_'.$transactionID.'" onclick="void_transaction(\''.$transactionID.'\',\'16\',this)">Void</button></td>';
                } else if($TransactionType=="02") {
                    $sale_html.='<td>RETURN</td>';
                }  else if(in_array($TransactionType,$voidType)) {
                    $sale_html.='<td>VOID</td>';
                } else {
                    $sale_html.='<td>&nbsp;</td>';
                }
        $sale_html.='<td>'.$motoType.'</td>';
        $sale_html.='</tr>';
    }
} else {
    $sale_html.='<tr><td colspan="8" class="text-center">No Record Found.</td></tr>';
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>imwemr - POS Transaction Log</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


        <!-- Bootstrap -->
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery-ui.min.css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">

        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.mCustomScrollbar.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/normalize.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-multiselect.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.datetimepicker.min.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">

        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.min.1.12.4.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/bootstrap-dropdownhover.min.js"></script>

        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
        <!--jquery to suport discontinued functions-->
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-migrate-1.2.1.js"></script> 
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.datetimepicker.full.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>

    </head>
    <body>
        <div class="container-fluid">
            <div id="main_form" class="whitebox">
                <div class="row boxheadertop">
                    <div class="col-sm-5 text-left"><h3><?php echo 'POS Transactions Log'; ?></h3></div>
                    <div class="col-sm-7 "><strong><?php echo $patient_name; ?></strong></div>
                </div>                            
                <!-- start listing -->
                <div style="height:540px; overflow-y:auto; overflow-x:auto;">
                    <table class="table table-striped table-bordered table-hover adminnw">
                        <thead>
                            <tr>
                                <th nowrap>Date of Transaction</th>
                                <th>Transaction Id</th>
                                <th>Payment</th>
                                <th>Payment Method</th>
                                <th>Operator</th>
                                <th>Status</th>
                                <th>Transaction Type</th>
                                <th>Transaction Mode</th>
                            </tr>
                        </thead>
                        <tbody id="prepend_row"><?php echo $sale_html; ?></tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-4 hide">
                        <label>POS</label>
                        <?php include 'include_cc_payment.php'; ?>
                    </div>
                    <div class="col-sm-8"></div>
                </div>
            </div>

            <footer id="footer_bar" class="text-right">     
                <button type="button" name="close" value="Close" class="btn btn-danger" onClick="window.close();">Close</button>
            </footer> 
        </div>

        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/pos/jquery.base64.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/pos/pos.js"></script>

    </body>
</html>