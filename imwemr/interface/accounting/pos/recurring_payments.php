<?php
$ignoreAuth = true;
require_once(dirname(__FILE__)."/../../../config/globals.php");
require_once(dirname(__FILE__)."/../../../library/classes/payments/class.transitAPI.php");

//where patient_id="'.$patient_id.'"
$sql=' select count(patient_id) as pt_count,id,patient_id,operator_id,laneId,scheduID,encounter_id,status,log_referenceNumber,ResponseMessage,TransactionNumber,transactionAmount,added_on,Account,
        ExpireDate,CardType,TransactionType,HostResponseMessage,motoType,motoOrderNumber,token,paymentCount,isRecurring,currentPaymentCount  
        from tsys_possale_transaction 
        where isRecurring="T" 
        and paymentCount!="" 
        and currentPaymentCount!="" 
        group by patient_id,transactionAmount having count(patient_id)!=paymentCount
        order by added_on desc ';
$sql_rs=imw_query($sql);
$sql_count_rs=imw_num_rows($sql_rs);

$installmentArr=array();
while ($row=imw_fetch_assoc($sql_rs)) {
    $installmentArr[]=$row;
}
//pre($installmentArr);die;
$dsql="Select tsys_device_details.id as d_id from tsys_device_details 
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
              WHERE device_status=0 
              AND merchant_status=0 limit 0,1 
              ";
$dsql_rs=imw_query($dsql);
$dsql_count_rs=imw_num_rows($dsql_rs);
$drow=imw_fetch_assoc($dsql_rs);

//pre($installmentArr);die;
$returnVal=array();
foreach($installmentArr as $data) {
    if($data['paymentCount']==$data['pt_count']) continue;
    if($data['transactionAmount']==42.00 && $data['pt_count']==1)continue;
    if($data['transactionAmount']==10.00 && $data['pt_count']==2)continue;
    if($data['transactionAmount']==40.00 && $data['pt_count']==3)continue;
    if($data['transactionAmount']==50.00 && $data['pt_count']==5)continue;
    if($data['transactionAmount']==32.00 && $data['pt_count']==9)continue;
    if($data['transactionAmount']==45.00 && $data['pt_count']==10)continue;
    $laneID=$data['laneId'];
    //pre($data);continue;
    $portalParms=array();
    $portalParms['patient_id']=$data['patient_id'];
    $portalParms['operator_id']=$data['operator_id'];
    $portalParms['tsys_device_id']=$drow['d_id'];
    
    $transitObj = new TransitAPI($laneID, $drow['d_id'],$portalParms);
    
    $referenceNumber=$transitObj->createReferenceNumber($data['patient_id']);
    $transaction_amount = $data['transactionAmount'];
    $isRecurring = $data['isRecurring'];
    $pos_pay_count = $data['paymentCount'];
    $pos_curr_pay_count = $data['pt_count']+1;
    
    $card_data=array();
    $card_data['referenceNumber']=$referenceNumber;
    $card_data['card_source']=$card_source='MANUAL';
    $card_data['cardNumber']=$data['token'];
    $card_data['expirationDate']=$data['ExpireDate'];

    $otherParams=array();
    $otherParams['referenceNumber']= $referenceNumber;
    $otherParams['cardDataSource']=$card_source;
    $otherParams['laneID']=$laneID;
    $otherParams['isRecurring']=$isRecurring;
    $otherParams['pos_pay_count']=$pos_pay_count;
    $otherParams['pos_curr_pay_count']=$pos_curr_pay_count;
    $otherParams['pos_cron']='cron';

    $return = $transitObj->saleTransaction($card_data, $transaction_amount, $currency_code="USD", $card_source, $otherParams);
    $returnVal[]=$return;
    $returnVal['patient_id']=$data['patient_id'];
}

if(empty($returnVal)) {
    echo 'No installment pending for patients.';
} else {
    foreach($returnVal as $rdata) {
        echo $rdata['transactionID']." ".$rdata['responseMessage'].'<br>';
    }
}

?>