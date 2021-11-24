<?php
$ignoreAuth = true;
require_once(dirname(__FILE__)."/../../../config/globals.php");
require_once(dirname(__FILE__)."/../../../library/classes/payments/class.transitAPI.php");

if(isset($_REQUEST['method']) && $_REQUEST['method']!='') {
    switch($_REQUEST['method']) {
        case 'validate_card' :
            //sale_transaction();
            card_authentication();
            break;
        
    }
}

function card_authentication() {
    $laneID=$_POST['laneId'];
    $tsys_device_id=$_POST['tsys_device_id'];
    $transItObj = new TransitAPI($laneID,$tsys_device_id);
    $isRecurring=$_POST['isRecurring']?$_POST['isRecurring']:'';
    $pos_pay_count=$_POST['pos_pay_count']?$_POST['pos_pay_count']:'';
    $pos_curr_pay_count=$_POST['pos_curr_pay_count']?$_POST['pos_curr_pay_count']:'';
    $apireturn=$_POST['apireturn']?$_POST['apireturn']:'';
    $referenceNumber=$_POST['referenceNumber'];
    $hsa_fsa=(isset($_POST['hsa_fsa']) && $_POST['hsa_fsa']!='')?$_POST['hsa_fsa']:'';
    $tsys_token=(isset($_POST['tsys_token']) && $_POST['tsys_token']!='')?$_POST['tsys_token']:'';
    $MotoMode=$_POST['tsysOrderNumber']['MotoMode'];
    $OrderNumber=$_POST['tsysOrderNumber']['OrderNumber'];
    $trans_id=='';
    if($tsys_token==''){
        $trans_id=(isset($_POST['tsysOrderNumber']['transactionNumber']) && $_POST['tsysOrderNumber']['transactionNumber']!='')?$_POST['tsysOrderNumber']['transactionNumber']:false;
    }
    
    $card_source='';
    switch($MotoMode) {
        case 'M':
            $card_source='MAIL';
            break;
        case 'T':
            $card_source='PHONE';
            break;
        case 'I':
        case 'R':
            $card_source='MANUAL';
            $MotoMode='';
            $OrderNumber='';
            break;
    }
    if($tsys_token!='' && $card_source=='')$card_source='MANUAL';
    
    $data=array();
    $otherParams=array();
    $data['cardDataSource']=$card_source;
    $data['cardNumber']=($tsys_token!='')?$tsys_token:base64_decode($_POST['cardNumber']);
    $data['expirationDate']= base64_decode($_POST['expirationDate']);
    $data['cardHolderName']= base64_decode($_POST['cardHolderName']);
    $data['pos_cvv2']= base64_decode($_POST['pos_cvv2']);

    $data['orderNumber']= $OrderNumber;
    
    $otherParams['referenceNumber']= $referenceNumber;
    $otherParams['cardDataSource']=$card_source;
    $otherParams['MotoMode']=$MotoMode;
    $otherParams['OrderNumber']=$OrderNumber;
    $otherParams['laneID']=$laneID;
    $otherParams['scheduID']=$_POST['scheduID'];
    $otherParams['encountID']=$_POST['encounter_id'];
    $otherParams['isRecurring']=$isRecurring;
    $otherParams['pos_pay_count']=$pos_pay_count;
    $otherParams['pos_curr_pay_count']=$pos_curr_pay_count;
    $otherParams['creditCardtype']=$_POST['creditCardtype'];
    $otherParams['pos_cvv2']=$data['pos_cvv2'];
    $otherParams['hsa_fsa']=$hsa_fsa;
    $otherParams['tsys_token']=$tsys_token;

    if($tsys_token=='' && ($apireturn=='' || ($apireturn=='refund' && $_POST['cardNumber']!='')) ) {
        $return = $transItObj->cardAuthentication($data,$otherParams);
    }
    
    if($tsys_token!='' || $apireturn=='void' || $apireturn=='refund' || $return['success']) {
        $transaction_amount=($_POST['totalAmt']/100);
        $card_data=array();
        $card_data['referenceNumber']=$referenceNumber;
        $card_data['card_source']=$card_source;
        if(($tsys_token!='')) {
            $card_data['cardNumber']=$tsys_token;
            $card_data['pos_cvv2']='';
        } else {
            $card_data['cardNumber']=base64_decode($_POST['cardNumber']);
            $card_data['pos_cvv2']=base64_decode($_POST['pos_cvv2']);
        }
        $card_data['expirationDate']= str_replace('/', '', base64_decode($_POST['expirationDate']));
        $currency_code="USD";
        
        switch($apireturn){
            case 'refund':
                saveAPIrequestLog('02',$trans_id,$OrderNumber);
                $salereturn = $transItObj->returnTransaction($trans_id, $return_reason = '', $transaction_amount, $currency_code,$card_data,$otherParams);
                break;
            case 'void':
                saveAPIrequestLog('16',$trans_id,$OrderNumber);
                $salereturn = $transItObj->voidTransaction($trans_id, $void_reason = '', $transaction_amount,$otherParams);
                break;
            default:
                saveAPIrequestLog('01',$trans_id,$OrderNumber);
                $salereturn = $transItObj->saleTransaction($card_data, $transaction_amount, $currency_code, $card_source, $otherParams);
                break;
        }

        if(empty($salereturn)==false){
            $data = array();
            $data['TransactionNumber'] = $salereturn['transactionID'];
            $data['message'] = $salereturn['responseMessage'];
            $data['card_details_str'] = (isset($salereturn['card_details_str']) && $salereturn['card_details_str']!='')?$salereturn['card_details_str']:'';
            if(isset($salereturn['partialTransaction']) && $salereturn['partialTransaction']=='partial'){$data['partialTransaction']=$salereturn['partialTransaction'];}
            
            echo json_encode($data);
            die;
        }
    } else {
        $data = array();
        $data['ResponseMessage'] = $return['error'];
        echo json_encode($data);
        die;
    }
}


function saveAPIrequestLog($transactionType,$trans_id='',$OrderNumber='') {
    $referenceNumber=$_POST['referenceNumber'];
    if ($referenceNumber) {
        $operator_id=$_SESSION['authId'];
        $tsys_device_id=trim($_POST['tsys_device_id']);
        $login_facility=$_SESSION['login_facility'];

        $data = array();
        $transaction_amount = ($_POST['totalAmt']/100);
        $data['transactionAmount'] = $transaction_amount;
        $data['laneId'] = $_POST['laneId'];
        $data['scheduID'] = $_POST['scheduID'];
        $data['encounter_id'] = $_POST['encounter_id'];
        $data['transactionType'] = $transactionType;
        $data['transactionNumber'] = $trans_id;
        $data['referenceNumber'] = $referenceNumber;
        $data['tsysOrderNumber'] = $OrderNumber;
        $data['device_id'] = $tsys_device_id;
        $data['device_url'] = 'api';
        $data['final_hex_string'] = '';
        $data['operator_id'] = $operator_id;
        $data['merchant_id'] = 0;
        $data['facility_id'] = $login_facility;

        $devices_sql = "Select tsys_merchant.id as merchant_id, tsys_device_details.id as device_id from tsys_device_details 
                          JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
                          WHERE device_status=0 
                          AND tsys_device_details.facility_id='" . $login_facility . "' 
                          AND tsys_device_details.id='" . $tsys_device_id . "' 
                          AND merchant_status=0 
                          ";
        $resp = imw_query($devices_sql);
        if ($resp && imw_num_rows($resp) > 0) {
            $row = imw_fetch_assoc($resp);
            $data['merchant_id'] = $row['merchant_id'];
        }

        $updatesql = 'UPDATE tsys_payment_type_log SET refrenceNumber="' . $referenceNumber . '", transactionAmount="' . $data['transactionAmount'] . '", laneId="' . $data['laneId'] . '", scheduID="' . $data['scheduID'] . '" 
                            , encounter_id="' . $data['encounter_id'] . '", transactionType="' . $data['transactionType'] . '", transactionNumber="' . $data['transactionNumber'] . '"
                            , tsysOrderNumber="' . $data['tsysOrderNumber'] . '", device_id="' . $data['device_id'] . '", device_url="' . $data['device_url'] . '", final_hex_string="' . $data['final_hex_string'] . '"
                            , operator_id="' . $data['operator_id'] . '", merchant_id="' . $data['merchant_id'] . '", facility_id="' . $data['facility_id'] . '"
                            WHERE refrenceNumber="' . $referenceNumber . '" ';

        imw_query($updatesql);
    }
}

?>