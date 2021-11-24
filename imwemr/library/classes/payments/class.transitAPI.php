<?php

// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

//include_once ('../../../../config/globals.php');

class TransitAPI {

    private $merchantUserID = false;
    private $merchantPasswd = false;
    private $mid = false;
    private $applicationID = false;
    private $transactionKey = false;
    private $transactionKeyTime = false;
    private $api_url = false;
    private $deviceID = false;
    private $developerID = false;
    private $tsys_dtls_id = false;
    private $tsys_production = false;
    private $curl_error_msg = false;
    private $curl_error_no = false;
    private $error_msg = false;
    private $swiped_card_data = false;
    private $patient_id = false;
    private $operator_id = false;
    private $facility = false;
    private $laneID = false;
    private $is_api = false;
    private $manifest_url = false;
    
    // 10000001 - Check In Screen - Default 
    // 10000002 - Check Out Screen
    // 10000003 - Prepayment Screen
    // 10000004 - Enter Charges -> Payments Screen
    // 20000001 - iPortal
    // 30000001 - Cron for Installment Transactions
    
    function __construct($laneID,$tsys_device_id=false,$portalParms=array()) {
        if(empty($portalParms)==false ) {
            $this->patient_id=$portalParms['patient_id'];
            $this->operator_id=$portalParms['operator_id'];
            $this->facility=$portalParms['facilityID'];
        }
        
        if(isset($GLOBALS["TSEP_MANIFEST_URL"]) && $GLOBALS["TSEP_MANIFEST_URL"]==1) {
            //Manifest Production URL:
            $this->manifest_url="https://gateway.transit-pass.com/transit-tsep-web/jsView/";
        } else {
            //Manifest Staging URL:
            $this->manifest_url="https://stagegw.transnox.com/transit-tsep-web/jsView/";
        }
        
        $this->is_api=1;
        $this->api_url="https://stagegw.transnox.com/servlets/TransNox_API_Server/";
        // Initialize App
        $this->initApp($laneID,$tsys_device_id);

        // Validate Transaction Key
        $this->validateTransactionKey();
    }

    public function saleTransaction($card_data, $transaction_amount, $currency_code = 'USD', $card_source = 'SWIPE',$otherParams=array()) {

        if (!empty($card_data) && is_array($card_data)) {
            $card_source = $card_source;
            $cardNumber = trim($card_data['cardNumber']);
            $expirationDate = trim($card_data['expirationDate']);
            $pos_cvv2 = trim($card_data['pos_cvv2']);
        }
        $transaction_amount = number_format($transaction_amount, 2);
        
        $params = array();
        $params['deviceID'] = $this->deviceID;
        $params['transactionKey'] = $this->transactionKey;
        $params['cardDataSource'] = $card_source;
        $params['transactionAmount'] = $transaction_amount;
        $params['currencyCode'] = $currency_code;
        if ($card_source == 'SWIPE') {
            $params['track2Data'] = $card_data;
        } else {
            $params['cardNumber'] = $cardNumber;
            $params['expirationDate'] = $expirationDate;
            $params['cvv2'] = $pos_cvv2;
        }

        //$params['operatorID'] = str_pad($this->operator_id, 3, '0', STR_PAD_LEFT);
        
        $params['cardOnFile'] = "Y";
        $params['tokenRequired'] = 'Y';
        if(isset($otherParams['hsa_fsa']) && $otherParams['hsa_fsa']!=''){
            $params['healthCareAccountType'] = $otherParams['hsa_fsa'];
            $params['isQualifiedIIAS'] = 'NO';
        }
        $params['terminalCapability'] = 'KEYED_ENTRY_ONLY';
        $params['terminalOperatingEnvironment'] = 'ON_MERCHANT_PREMISES_ATTENDED';
        if($card_source=='INTERNET') {$params['terminalOperatingEnvironment'] = 'OFF_MERCHANT_PREMISES_UNATTENDED';}
        $params['cardholderAuthenticationMethod'] = 'NOT_AUTHENTICATED';
        $params['terminalAuthenticationCapability'] = 'NO_CAPABILITY';
        $params['terminalOutputCapability'] = 'DISPLAY_ONLY';
        $params['maxPinLength'] = 'NOT_SUPPORTED';
        $params['terminalCardCaptureCapability'] = 'NO_CAPABILITY';
        
        if($card_source=='INTERNET') { $params['cardholderPresentDetail'] = 'CARDHOLDER_NOT_PRESENT_ELECTRONIC_COMMERCE'; }
        if($card_source=='PHONE') { $params['cardholderPresentDetail'] = 'CARDHOLDER_NOT_PRESENT_PHONE_TRANSACTION'; }
        if($card_source=='MAIL') { $params['cardholderPresentDetail'] = 'CARDHOLDER_NOT_PRESENT_MAIL_TRANSACTION'; }
        
        $params['cardPresentDetail'] = 'CARD_NOT_PRESENT';
        $params['cardDataInputMode'] = 'KEY_ENTERED_INPUT';
        if($card_source=='INTERNET') {$params['cardDataInputMode'] = 'ELECTRONIC_COMMERCE_NO_SECURITY_CHANNEL_ENCRYPTED_SET_WITHOUT_CARDHOLDER_CERTIFICATE';}
        if($otherParams['creditCardtype']=='AX' && isset($params['cvv2']) && $params['cvv2']!='') {
            if($card_source=='INTERNET') {unset($params['cvv2']);}
            $params['cardDataInputMode'] = 'MANUALLY_ENTERED_WITH_KEYED_CID_AMEX_JCB';
        }
        if(isset($otherParams['tsys_token']) && $otherParams['tsys_token']!='') {
            if($card_source!='INTERNET') {unset($params['cvv2']);}
            unset($params['cardOnFile']);
            $params['cardDataInputMode'] = 'MERCHANT_INITIATED_TRANSACTION_CARD_CREDENTIAL_STORED_ON_FILE';
        }
        
        /*
        //Overwrite variables for recurring transaction only
        if(isset($otherParams['isRecurring']) && $otherParams['isRecurring']!='') {
            $params['terminalCapability'] = 'KEYED_ENTRY_ONLY';
            $params['terminalOperatingEnvironment'] = 'OFF_MERCHANT_PREMISES_UNATTENDED';
            if($otherParams['creditCardtype']=='MC') {
                $params['terminalOperatingEnvironment'] = 'NO_TERMINAL';
            }
            $params['cardholderAuthenticationMethod'] = 'NOT_AUTHENTICATED';
            $params['terminalAuthenticationCapability'] = 'NO_CAPABILITY';
            $params['terminalOutputCapability'] = 'DISPLAY_ONLY';
            $params['maxPinLength'] = 'NOT_SUPPORTED';
            $params['terminalCardCaptureCapability'] = 'NO_CAPABILITY';
            $params['cardholderPresentDetail'] = 'CARDHOLDER_NOT_PRESENT_RECURRING_TRANSACTION';
            if(isset($otherParams['isRecurring']) && $otherParams['isRecurring']=='T' && $otherParams['pos_pay_count']!='' && $otherParams['pos_curr_pay_count']!='') {
                $params['cardholderPresentDetail'] = 'CARDHOLDER_NOT_PRESENT_INSTALLMENT_TRANSACTION';
            }
            $params['cardPresentDetail'] = 'CARD_NOT_PRESENT';
            $params['cardDataInputMode'] = 'MERCHANT_INITIATED_TRANSACTION_CARD_CREDENTIAL_STORED_ON_FILE';
        }
        */
        
        $params['cardholderAuthenticationEntity'] = 'NOT_AUTHENTICATED';
        $params['cardDataOutputCapability'] = 'NONE';
        
        $params['developerID'] = $this->developerID;
        
        /*
        //Overwrite variables for recurring transaction only
        if(isset($otherParams['isRecurring']) && $otherParams['isRecurring']=='R') {
            $params['isRecurring'] = "Y";
            unset($params['cvv2']);
        } else if(isset($otherParams['isRecurring']) && $otherParams['isRecurring']=='T' && $otherParams['pos_pay_count']!='' && $otherParams['pos_curr_pay_count']!='') {
            $params['isRecurring'] = "Y";
            $params['billingType'] = "INSTALLMENT";
            $params['paymentCount'] = $otherParams['pos_pay_count'];
            $params['currentPaymentCount'] = $otherParams['pos_curr_pay_count'];
            unset($params['cvv2']);
        }
        */
        
        if($card_source!='INTERNET') { $params['laneID'] = $this->laneID; }
        $params['authorizationIndicator'] = 'FINAL';
            
        $imw_trans_id = $this->init_transaction('sale', $card_data, $transaction_amount, $card_source,$otherParams);

        $result = $this->CURL('Sale', $params, $imw_trans_id,$otherParams);
        $result = $result['SaleResponse'];

        $status = $result['status'];

        $return = array();
        $data = array();
        if ($result) {
            $data['status'] = $return['status'] = $result['status'];
            $data['responseCode'] = $return['responseCode'] = $result['responseCode'];
            $data['responseMessage'] = $return['responseMessage'] = $result['responseMessage'];
            $return['authCode'] = (isset($result['authCode']) && $result['authCode'] != '') ? $result['authCode'] : '';
            $return['hostReferenceNumber'] = (isset($result['hostReferenceNumber']) && $result['hostReferenceNumber'] != '') ? $result['hostReferenceNumber'] : '';
            $return['hostResponseCode'] = (isset($result['hostResponseCode']) && $result['hostResponseCode'] != '') ? $result['hostResponseCode'] : '';
            $return['taskID'] = (isset($result['taskID']) && $result['taskID'] != '') ? $result['taskID'] : '';
            $data['transactionID'] = $return['transactionID'] = (isset($result['transactionID']) && $result['transactionID'] != '') ? $result['transactionID'] : '';
            $return['transactionTimestamp'] = (isset($result['transactionTimestamp']) && $result['transactionTimestamp'] != '') ? $result['transactionTimestamp'] : '';
            $return['transactionAmount'] = (isset($result['transactionAmount']) && $result['transactionAmount'] != '') ? $result['transactionAmount'] : '';
            $return['processedAmount'] = (isset($result['processedAmount']) && $result['processedAmount'] != '') ? $result['processedAmount'] : '';
            $return['totalAmount'] = (isset($result['totalAmount']) && $result['totalAmount'] != '') ? $result['totalAmount'] : '';
            $return['addressVerificationCode'] = (isset($result['addressVerificationCode']) && $result['addressVerificationCode'] != '') ? $result['addressVerificationCode'] : '';
            $return['cardType'] = (isset($result['cardType']) && $result['cardType'] != '') ? $result['cardType'] : '';
            $return['maskedCardNumber'] = (isset($result['maskedCardNumber']) && $result['maskedCardNumber'] != '') ? $result['maskedCardNumber'] : '';
            $return['token'] = (isset($result['token']) && $result['token'] != '') ? $result['token'] : '';
            $return['expirationDate'] = (isset($result['expirationDate']) && $result['expirationDate'] != '') ? $result['expirationDate'] : '';
            $return['commercialCard'] = (isset($result['commercialCard']) && $result['commercialCard'] != '') ? $result['commercialCard'] : '';
            $return['aci'] = (isset($result['aci']) && $result['aci'] != '') ? $result['aci'] : '';
            $return['cardTransactionIdentifier'] = (isset($result['cardTransactionIdentifier']) && $result['cardTransactionIdentifier'] != '') ? $result['cardTransactionIdentifier'] : '';
            $return['customerReceipt'] = (isset($result['customerReceipt']) && $result['customerReceipt'] != '') ? $result['customerReceipt'] : '';
            $return['merchantReceipt'] = (isset($result['merchantReceipt']) && $result['merchantReceipt'] != '') ? $result['merchantReceipt'] : '';
            
            switch($return['cardType']) {
                case 'V':
                    $cardType='Visa';
                    break;
                case 'M':
                    $cardType='MC';
                    break;
                case 'AX':
                case 'X':
                    $cardType='AX';
                    break;
                case 'Dis':
                case 'R':
                    $cardType='Dis';
                    break;
                default:
                    $cardType='Other';
                    break;
            }
            $card_details_str=$cardType.'~~'.$return['maskedCardNumber'].'~~'.$return['expirationDate'];
            $data['card_details_str']=$card_details_str;
        } else {

            if ($this->curl_error_no) {
                $error_msg = 'Error - ' . $this->curl_error_no . ' - ' . $this->curl_error_msg;
                $this->handle_error($error_msg);
            } else {
                throw new Exception('No data returned');
            }
        }

        $return['imw_trans_id'] = $imw_trans_id;
        if ($imw_trans_id > 0) {
            imw_query('update tsys_sale_request set status="' . $return['status'] . '" where id="' . $imw_trans_id . '" ');
        }

        if (empty($return) == false) {
            $this->saveSaleTransaction($return,$otherParams);
        }

        if($return['transactionAmount']!=$return['processedAmount']) {
            $otherParams['cardDataSource']=$card_source;
            $otherParams['partialTransaction']='partial';
            return $this->voidTransaction($return['transactionID'], $void_reason = '', $return['processedAmount'],$otherParams);
        } else {
            
            $status = $return['status'];
            if($status == 'PASS'){
                if($this->laneID == 20000001 || $otherParams['pos_cron']=='cron'){
                    $referenceNumber='';
                    $return['TransactionType']='01';
                    if (!empty($otherParams) && is_array($otherParams)) {
                        $referenceNumber=$otherParams['referenceNumber'];
                    }
                    switch($return['cardType']) {
                        case 'V':
                            $credit_card_co='Visa';
                            break;
                        case 'M':
                            $credit_card_co='MC';
                            break;
                        case 'AX':
                        case 'X':
                            $credit_card_co='AX';
                            break;
                        case 'Dis':
                        case 'R':
                            $credit_card_co='Dis';
                            break;
                        default:
                            $credit_card_co='Other';
                            break;
                    }
                    $entered_date=date('Y-m-d');
                    $entered_time=date('H:i:s');
                    $comment = 'Payment(s) made from iPortal';
                    if($otherParams['pos_cron']=='cron')$comment = 'Cron Payments';
                    $res_qry = 'insert into patient_pre_payment set patient_id="'.$this->patient_id.'", paid_amount="'.$return['transactionAmount'].'",
                    facility_id="'.$this->facility.'", provider_id="'.$this->operator_id.'", credit_card_co="'.$credit_card_co.'", payment_mode="Credit Card",
                    cc_no="'.$return['maskedCardNumber'].'", cc_exp_date="'.$return['expirationDate'].'", entered_date="'.$entered_date.'", entered_time="'.$entered_time.'",
                    entered_by="'.$this->operator_id.'", paid_date="'.$entered_date.'", tsys_transaction_id="'.$return['transactionID'].'", tsys_status="",iportal_payment="1",
                    comment="'.$comment.'", log_referenceNumber="'.$referenceNumber.'"';
                    $res_sql = imw_query($res_qry);
                    //echo 'Transaction successfully done.';	
                    
                    $patient_name = (isset($otherParams['patient_name']) && $otherParams['patient_name']!='') ? $otherParams['patient_name']:'';
                    $card_details=$credit_card_co.' - '.$return['maskedCardNumber'].' - '.$return['expirationDate'];
                    $dot_date_time=date('m-d-Y').' '.$entered_time;
                    
                    $iportal_req=array();
                    $iportal_req['Patient Name']=$patient_name;
                    $iportal_req['Account Number']=$card_details;
                    $iportal_req['Date Of Transaction']=$dot_date_time;
                    $iportal_req['Transaction ID']=$return['transactionID'];
                    $iportal_req['Amount']='$'.$return['transactionAmount'];
                    
                    $table_str="<table>
                                <tr>
                                    <td colspan='2'>Patient Payment(s) Detail</td>
                                </tr><tr>
                                    <td style='font-weight:bold;'>Patient Name</td>
                                    <td>".$patient_name."</td>						
                                </tr><tr>
                                    <td style='font-weight:bold;'>Account Number</td>
                                    <td>".$card_details."</td>						
                                </tr><tr>
                                    <td style='font-weight:bold;'>Date Of Transaction</td>
                                    <td>".$dot_date_time."</td>						
                                </tr><tr>
                                    <td style='font-weight:bold;'>Transaction ID</td>
                                    <td>".$return['transactionID']."</td>						
                                </tr><tr>
                                    <td style='font-weight:bold;'>Amount</td>
                                    <td>$".$return['transactionAmount']."</td>						
                                </tr>
                            </table>";
                       
                    $req_qry1 = "INSERT INTO iportal_req_changes SET pt_id='".$this->patient_id."',tb_name='patient_pre_payment',title_msg='".$comment."', 
                    col_pri_id='0',new_val='".$res_qry."',new_val_lbl='".addslashes($table_str)."',new_val_arr='".json_encode($iportal_req)."',action='insert', 
                    reqDateTime='".$entered_date.' '.$entered_time."',is_approved=3 " ; 
                    $req_qry_obj = imw_query($req_qry1);
                }
            }
        
            return $data;
        }
    }

    public function saveSaleTransaction($return,$otherParams) {
        $response_time = date('Y-m-d H:i:s');
        if ($return['transactionTimestamp'])
            $return['transactionTimestamp'] = str_replace('T', ' ', $return['transactionTimestamp']);

        $sql = 'INSERT INTO tsys_sale_response (sale_req_id,patient_id,operator_id,status,responseCode,responseMessage,authCode,hostReferenceNumber,
            hostResponseCode,taskID,transactionID,transactionTimestamp,transactionAmount,processedAmount,totalAmount,addressVerificationCode,token,
            cardType,maskedCardNumber,expirationDate,commercialCard,aci,cardTransactionIdentifier,customerReceipt,merchantReceipt,added_on)
        VALUES ("' . $return['imw_trans_id'] . '", "' . $this->patient_id . '", "' . $this->operator_id . '", "' . $return['status'] . '", "' . $return['responseCode'] . '", "' . $return['responseMessage'] . '", "' . $return['authCode'] . '", "' . $return['hostReferenceNumber'] . '",
                "' . $return['hostResponseCode'] . '", "' . $return['taskID'] . '", "' . $return['transactionID'] . '", "' . $return['transactionTimestamp'] . '", "' . $return['transactionAmount'] . '", "' . $return['processedAmount'] . '", "' . $return['totalAmount'] . '",
                "' . $return['addressVerificationCode'] . '","' . $return['token'] . '", "' . $return['cardType'] . '", "' . $return['maskedCardNumber'] . '", "' . $return['expirationDate'] . '", "' . $return['commercialCard'] . '", "' . $return['aci'] . '", "' . $return['cardTransactionIdentifier'] . '",
                "' . $return['customerReceipt'] . '", "' . $return['merchantReceipt'] . '","' . $response_time . '")';

        imw_query($sql);
        $otherParams['TransactionType']='01';
        $this->updateToTransactionLog($return,$otherParams);
    }

    public function voidTransaction($trans_id, $void_reason = '', $trans_amount = 0.00,$otherParams=array()) {

        $partialTransaction=(isset($otherParams['partialTransaction']) && $otherParams['partialTransaction']=='partial')?$otherParams['partialTransaction']:'';
        $trans_id = trim($trans_id);
        $void_reason = trim($void_reason);
        $trans_amount = number_format($trans_amount, 2);
        
        if (!$trans_id)
            $this->handle_error('Transaction ID missing.');

        $params = array();
        $params['deviceID'] = $this->deviceID;
        $params['transactionKey'] = $this->transactionKey;
        if($partialTransaction=='partial' && $otherParams['cardDataSource']!="INTERNET") { $params['transactionAmount'] = $trans_amount; }
        $params['transactionID'] = $trans_id;
        $params['operatorID'] = str_pad($this->operator_id, 3, '0', STR_PAD_LEFT);
        $params['tokenRequired'] = 'Y';
        $params['developerID'] = $this->developerID;
        if($otherParams['cardDataSource']!="INTERNET"){$params['laneID'] = $this->laneID;}
        if($void_reason){$params['achCancelNote'] = $void_reason;}
        if($partialTransaction!='') {$params['voidReason'] = 'POST_AUTH_USER_DECLINE';}

        // Initiate transaction in imwemr Database
        $qry = "Insert into tsys_void_request Set patient_id = '" . $this->patient_id . "', operator_id = '" . $this->operator_id . "', tsys_dtls_id = '" . $this->tsys_dtls_id . "', sale_trans_id = '" . $trans_id . "', trans_amount = '" . $trans_amount . "', lane_id = '" . $this->laneID . "', reason = '" . addslashes($void_reason) . "', added_on = '" . date('Y-m-d H:i:s') . "'  ";
        $sql = imw_query($qry) or die(imw_error());
        $imw_trans_id = imw_insert_id();

        $result = $this->CURL('Void', $params, $imw_trans_id, $otherParams);
        $result = $result['VoidResponse'];

        $status = $result['status'];

        if ($result) {
            // Fields list to update into response table
            $voidFields = array('status', 'responseCode', 'responseMessage', 'authCode', 'hostReferenceNumber', 'hostResponseCode', 'taskID', 'transactionID', 'transactionTimestamp', 'orderNumber', 'externalReferenceID', 'transactionAmount', 'voidedAmount', 'cardType', 'maskedCardNumber', 'token', 'expirationDate', 'customerReceipt', 'merchantReceipt');

            // Start Inserting Response Fields
            $tmpQry = "";
            foreach ($voidFields as $field) {
                if ($field == 'transactionTimestamp')
                    $result[$field] = str_replace('T', ' ', $result[$field]);
                $tmpQry .= isset($result[$field]) ? $field . " = '" . addslashes($result[$field]) . "', " : "";
            }
            $res_qry = "Insert Into tsys_void_response Set request_id = '" . $imw_trans_id . "', " . $tmpQry . " added_on = '" . date('Y-m-d H:i:s') . "' ";
            $res_sql = imw_query($res_qry);
            
            $void_id=imw_insert_id();
            // Update status into Request Table
            $req_qry = "Update tsys_void_request Set status = '" . $status . "' Where id = " . $imw_trans_id . " ";
            $req_sql = imw_query($req_qry);

            $return = array();
            if ($status == 'PASS') {
                $return = array('status' => $status, 'transactionID' => $result['transactionID'], 'responseMessage' => $result['responseMessage']);
                
                
                //update idoc tables
                switch($this->laneID) {
                    case '10000003':
                        $update_table='patient_pre_payment';
                        break;
                    case '10000001':
                    case '10000002':
                        $update_table='check_in_out_payment';
                        break;
                    case '10000004':
                        $update_table='patient_charges_detail_payment_info';
                        break;
                }

                if($void_id) {
                    $updateSql='update '.$update_table.' set tsys_status="VOIDED" where tsys_transaction_id="'.$trans_id.'" ' ;
                    imw_query($updateSql);
                }
                $return['partialTransaction']=$partialTransaction;
                $otherParams['TransactionType']='16';
                
                $this->updateToTransactionLog($result,$otherParams);
                
            } else {
                $return = array('status' => $status, 'responseCode' => $result['responseCode'], 'responseMessage' => $result['responseMessage']);
            }

            return $return;
        } else {

            if ($this->curl_error_no) {
                $error_msg = 'Error - ' . $this->curl_error_no . ' - ' . $this->curl_error_msg;
                $this->handle_error($error_msg);
            } else {
                throw new Exception('No data returned');
            }
        }
    }

    public function returnTransaction($trans_id = false, $return_reason = '', $trans_amount = 0.00, $currency_code = 'USD', $card_data = false,$otherParams=array()) {
        $card_source = '';
        if (!$trans_id) {
            $card_source = trim($card_data['card_source']);
            $cardNumber = trim($card_data['cardNumber']);
            $expirationDate = trim($card_data['expirationDate']);
        } else {
            $trans_id = trim($trans_id);
        }

        $return_reason = trim($return_reason);
        $trans_amount = number_format($trans_amount, 2);

        if (!$trans_id && !$card_data)
            $this->handle_error('Transaction ID missing.');

        $params = array();
        $params['deviceID'] = $this->deviceID;
        $params['transactionKey'] = $this->transactionKey;
        if (!$trans_id) {
            //Without reference
            $params['cardDataSource'] = $card_source;
            $params['transactionAmount'] = $trans_amount;
            $params['currencyCode'] = $currency_code;
            if ($card_source == 'SWIPE') {
                $params['track2Data'] = $card_data;
            } else {
                $params['cardNumber'] = $cardNumber;
                $params['expirationDate'] = $expirationDate;
            }
        } else {
            //With reference
            $params['transactionAmount'] = $trans_amount;
            $params['transactionID'] = $trans_id;
        }
        $params['operatorID'] = str_pad($this->operator_id, 3, '0', STR_PAD_LEFT);
        $params['tokenRequired'] = "Y";
        if ($return_reason) {
            $return_reason = str_replace('.', '', $return_reason);
            $params['softDescriptor'] = substr($return_reason, 0, 24);
        }
        $params['developerID'] = $this->developerID;
        $params['laneID'] = $this->laneID;

        // Initiate transaction in imwemr Database
        $qry = "Insert into tsys_return_request Set patient_id = '" . $this->patient_id . "', operator_id = $this->operator_id, tsys_dtls_id = '" . $this->tsys_dtls_id . "', sale_trans_id = '" . $trans_id . "', trans_amount = '" . $trans_amount . "', lane_id = '" . $this->laneID . "', reason = '" . addslashes($return_reason) . "', added_on = '" . date('Y-m-d H:i:s') . "', card_source='" . $card_source . "' ";
        $sql = imw_query($qry);
        $imw_trans_id = imw_insert_id();

        $result = $this->CURL('Return', $params, $imw_trans_id, $otherParams);
        $result = $result['ReturnResponse'];

        $status = $result['status'];

        if ($result) {
            // Fields list to update into response table
            $returnFields = array('status', 'responseCode', 'responseMessage', 'authCode', 'hostReferenceNumber', 'hostResponseCode', 'taskID', 'transactionID', 'transactionTimestamp', 'orderNumber', 'externalReferenceID', 'transactionAmount', 'returnedAmount', 'cardType', 'maskedCardNumber', 'token', 'expirationDate', 'customerReceipt', 'merchantReceipt');

            // Start Inserting Response Fields
            $tmpQry = "";
            foreach ($returnFields as $field) {
                if ($field == 'transactionTimestamp')
                    $result[$field] = str_replace('T', ' ', $result[$field]);
                $tmpQry .= isset($result[$field]) ? $field . " = '" . addslashes($result[$field]) . "', " : "";
            }
            $res_qry = "Insert Into tsys_return_response Set request_id = '" . $imw_trans_id . "', " . $tmpQry . " added_on = '" . date('Y-m-d H:i:s') . "' ";
            $res_sql = imw_query($res_qry);
            $imw_ret_id = imw_insert_id();
            
            // Update status into Request Table
            $req_qry = "Update tsys_return_request Set status = '" . $status . "' Where id = " . $imw_trans_id . " ";
            $req_sql = imw_query($req_qry);
            
            if($trans_id){
                // Update status into Sale Response Table
                $req_qry = "Update tsys_sale_response Set return_resp_id = '" . $imw_ret_id . "' Where transactionID = " . $trans_id . " and patient_id = '" . $this->patient_id . "' ";
                $req_sql = imw_query($req_qry);
            }
            
            $return = array();
            if ($status == 'PASS') {
                $return = array('status' => $status, 'transactionID' => $result['transactionID'], 'responseMessage' => $result['responseMessage']);
                switch($result['cardType']) {
                case 'V':
                    $cardType='Visa';
                    break;
                case 'M':
                    $cardType='MC';
                    break;
                case 'AX':
                case 'X':
                    $cardType='AX';
                    break;
                case 'Dis':
                case 'R':
                    $cardType='Dis';
                    break;
                default:
                    $cardType='Other';
                    break;
            }
            $card_details_str=$cardType.'~~'.$result['maskedCardNumber'].'~~'.$result['expirationDate'];
            $return['card_details_str']=$card_details_str;
            
            } else {
                $return = array('status' => $status, 'responseCode' => $result['responseCode'], 'responseMessage' => $result['responseMessage']);
            }
            $otherParams['TransactionType']='02';
            $this->updateToTransactionLog($result,$otherParams);
            
            return $return;
        } else {

            if ($this->curl_error_no) {
                $error_msg = 'Error - ' . $this->curl_error_no . ' - ' . $this->curl_error_msg;
                $this->handle_error($error_msg);
            } else {
                throw new Exception('No data returned');
            }
        }
    }

    public function batchClose($deviceID = '', $userID = '') {

        $deviceID = trim($deviceID);
        $userID = trim($userID);

        $params = array();
        $params['deviceID'] = $this->deviceID;
        $params['transactionKey'] = $this->transactionKey;
        $params['operatingUserID'] = str_pad($this->operator_id, 3, '0', STR_PAD_LEFT);
        if ($deviceID)
            $params['batchCloseParameter']['deviceID'] = $deviceID;
        if ($userID)
            $params['batchCloseParameter']['userID'] = $userID;

        // Initiate transaction in imwemr Database
        $qry = "Insert into tsys_batch_close_request Set operator_id = '" . $this->operator_id . "', tsys_dtls_id = '" . $this->tsys_dtls_id . "', requested_device_id = '" . $deviceID . "', requested_user_id = '" . $userID . "', added_on = '" . date('Y-m-d H:i:s') . "'  ";
        $sql = imw_query($qry) or die(imw_error());
        $imw_trans_id = imw_insert_id();

        $result = $this->CURL('BatchClose', $params, $imw_trans_id);
        $result = $result['BatchCloseResponse'];

        $status = $result['status'];

        if ($result) {
            // Fields list to update into response table
            $batchFields = array('status', 'responseCode', 'responseMessage', 'batchInfo' => array('deviceID', 'userID', 'SICCODE', 'saleCount', 'saleAmount', 'returnCount', 'returnAmount'));

            // Start Inserting Response Fields
            $tmpQry = "";
            foreach ($batchFields as $key => $field) {
                $tmpQry .= isset($result[$field]) ? $field . " = '" . addslashes($result[$field]) . "', " : "";
                if (is_array($field)) {
                    foreach ($field as $sField) {
                        $tmpQry .= isset($result[$key][$sField]) ? $sField . " = '" . addslashes($result[$key][$sField]) . "', " : "";
                    }
                }
            }
            $res_qry = "Insert Into tsys_batch_close_response Set request_id = '" . $imw_trans_id . "', " . $tmpQry . " added_on = '" . date('Y-m-d H:i:s') . "' ";
            $res_sql = imw_query($res_qry);

            // Update status into Request Table
            $req_qry = "Update tsys_batch_close_request Set status = '" . $status . "' Where id = " . $imw_trans_id . " ";
            $req_sql = imw_query($req_qry);

            return $result;
        } else {

            if ($this->curl_error_no) {
                $error_msg = 'Error - ' . $this->curl_error_no . ' - ' . $this->curl_error_msg;
                $this->handle_error($error_msg);
            } else {
                throw new Exception('No data returned');
            }
        }
    }
    
    
    public function cardAuthentication($data = array(),$otherParams=array() ) {
        $params['deviceID'] = $this->deviceID;
        $params['transactionKey'] = $this->transactionKey;
        $params['cardDataSource']=$data['cardDataSource'];
        $params['currencyCode']="USD";
        $params['cardNumber']=$data['cardNumber'];
        $params['expirationDate']= $data['expirationDate'];
        $params['cardHolderName']= $data['cardHolderName'];
        //$params['eciIndicator']= "5";
        $params['operatorID'] = str_pad($this->operator_id, 3, '0', STR_PAD_LEFT);
        if(isset($data['orderNumber']) && $data['orderNumber']!='')$params['orderNumber']= $data['orderNumber'];
        $params['cardOnFile']= "Y";
        $params['tokenRequired']= "Y";
        $params['developerID'] = $this->developerID;
        $params['laneID'] = $this->laneID;
        $params['terminalCapability'] = "ICC_CHIP_READ_ONLY";
        $params['terminalOperatingEnvironment'] = "ON_MERCHANT_PREMISES_ATTENDED";
        $params['cardholderAuthenticationMethod'] = "PIN";
        $params['terminalAuthenticationCapability'] = "NO_CAPABILITY";
        $params['terminalOutputCapability'] = "UNKNOWN";
        $params['maxPinLength'] = "UNKNOWN";
        
        
        $result = $this->CURL('CardAuthentication',$params,false,$otherParams);
        $result = $result['CardAuthenticationResponse'];

        $return=array();
        if ($result && $result['status']=='PASS') {
            $return['success']=true;
        } else if($result['status']=='FAIL') {
            $error_msg = 'Error while card authentication - ' . $result['responseCode'] . ' - ' . $result['responseMessage'];
            $return['error']=$error_msg;
        } else {
            if ($this->curl_error_no) {
                $error_msg = 'Error - ' . $this->curl_error_no . ' - ' . $this->curl_error_msg;
                $return['error']=$error_msg;
            } else {
                $return['error']='No data returned';
            }
        }
        
        return $return;
    }
    
    
    

    private function init_transaction($type, $card_data = array(), $transaction_amount = 0.00, $card_source = 'SWIPE',$otherParams) {

        $paymentStr='';
        $scheduID='';
        $encountID='';
        $log_referenceNumber='';
        $tsys_payment_type_log_id='';
        if (!empty($otherParams) && is_array($otherParams)) {
            $paymentStr= addslashes($otherParams['paymentArr']);
            $scheduID=$otherParams['scheduID'];
            $encountID=$otherParams['encountID'];
            $log_referenceNumber=$otherParams['referenceNumber'];
            $tsys_payment_type_log_id=$otherParams['tsys_payment_type_log_id'];
        }
        if (!empty($card_data) && is_array($card_data)) {
            $cardNumber = trim($card_data['cardNumber']);
            $last_four = substr($cardNumber, -4);
            $expirationDate = trim($card_data['expirationDate']);
        } else {
            $card_data_arr = explode('=', trim($card_data));
            $cardNumber = trim($card_data_arr[0]);
            $exp_date = substr(trim($card_data_arr[1]), 0, 4);
            $last_four = substr($cardNumber, -4);
            $expirationDate = trim($exp_date);
        }
        $request_time = date('Y-m-d H:i:s');
        $transaction_amount = number_format($transaction_amount, 2);

        $sql = 'INSERT INTO tsys_' . $type . '_request(patient_id, operator_id, tsys_dtls_id, cardNumber, expirationDate, transactionAmount, card_source, added_on,paymentStr,scheduID,encountID,reqLaneID,log_referenceNumber,tsys_payment_type_log_id)
            VALUES ("'.$this->patient_id.'", "'.$this->operator_id.'", "'.$this->tsys_dtls_id.'", "'.$last_four.'", "'.$expirationDate.'", "'.$transaction_amount.'", "'.$card_source.'", "'.$request_time.'", "'.$paymentStr.'", "'.$scheduID.'", "'.$encountID.'", "'.$this->laneID.'", "'.$log_referenceNumber.'", "'.$tsys_payment_type_log_id.'")';

        $rs = imw_query($sql);
        $trans_id = imw_insert_id();

        return $trans_id;
    }

    private function initApp($laneID = 10000001,$tsys_device_id=false) {

        if (!trim($laneID))
            $laneID = 10000001;
        
        $sql="Select *, tsys_device_details.id as d_id from tsys_device_details 
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
              WHERE device_status=0 
              AND merchant_status=0
              ";
        if($tsys_device_id) {
            $sql.=" AND tsys_device_details.id='".$tsys_device_id."' " ;
        } else {
            $sql.=" AND tsys_device_details.facility_id='".$_SESSION['login_facility']."'  Limit 1 " ;
        }
        $rs = imw_query($sql);
        $row = imw_fetch_assoc($rs);

        if ($row) {
            $this->tsys_dtls_id = $row['id'];
            $this->mid = $row['mid'];
            $this->merchantUserID = $row['userID'];
            $this->merchantPasswd = $row['mid_paswrd']; //.'L';
            $this->deviceID = $row['deviceID'];
            $this->developerID = $row['developerID'];
            $this->api_url = $this->api_url;
            $this->applicationID = $row['applicationID'];
        }
        if(!$this->patient_id)
            $this->patient_id = $_SESSION['patient'];
        if(!$this->operator_id)
            $this->operator_id = $_SESSION['authId'];
        $this->laneID = $laneID;
    }

    private function validateTransactionKey() {

       // if (!$this->transactionKey || $this->transactionKeyTime == '0000-00-00 00:00:00' || (strtotime($this->transactionKeyTime) < strtotime("-24 Hours"))) {
            // Generate Transaction Key
            $this->generateTransactionKey();
       // }
    }

    //Generate Key
    private function generateTransactionKey() {

        $params = array();
        $params['mid'] = $this->mid;
        $params['userID'] = $this->merchantUserID;
        $params['password'] = $this->merchantPasswd;
        $params['developerID'] = $this->developerID;

        $result = $this->CURL('GenerateKey', $params);
        $result = $result['GenerateKeyResponse'];

        $status = $result['status'];
        if ($result) {
            if ($status == 'PASS') {
                $this->transactionKey = $result['transactionKey'];
                $this->transactionKeyTime = date('Y-m-d H:i:s');
            } else {
                // write error msg
                $error_msg = 'Error while generating transaction key - ' . $result['responseCode'] . ' - ' . $result['responseMessage'];
                $this->handle_error($error_msg);
            }
        } else {
            if ($this->curl_error_no) {
                $error_msg = 'Error - ' . $this->curl_error_no . ' - ' . $this->curl_error_msg;
                $this->handle_error($error_msg);
            } else {
                throw new Exception('No data returned');
            }
        }
    }

    // Common CURL Request fucntion 
    private function CURL($key, $params, $imw_trans_id = false,$otherParams=array()) {
        $referenceNumber=(isset($otherParams['referenceNumber']) && $otherParams['referenceNumber']!='')?$otherParams['referenceNumber']:'';

        $transactionKey=$params['transactionKey'];
        $payload = json_encode(array($key => $params));

        $request_headers = array();
        $request_headers[] = 'User-Agent:infonox';
        $request_headers[] = 'Content-Type:application/json';

        // Reset Curl message before each request
        $this->reset_curl_msg();

        // API End Point
        $url = $this->api_url;

        // keep record of each transaction initiated/requested by imwemr
        $log_req_qry = "Insert into tsys_trans_log Set transaction_key = '" . $transactionKey . "', request_type = '" . $key . "', request_url = '" . $url . "', imw_trans_id = '" . $imw_trans_id . "', request_data = '" . $payload . "', request_date_time = '" . date('Y-m-d H:i:s') . "',  operator_id = '" . $_SESSION['authId'] . "',log_referenceNumber = '" . $referenceNumber . "' ";
        $log_req_sql = imw_query($log_req_qry);
        $log_req_id = imw_insert_id();

        // Initiate Curl 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /* Return the response */
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HEADER, false); /* Include header in Output/Response */
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Execute Curl Request
        $result = curl_exec($ch);

        // Update response for each executed request by imwemr
        $log_res_qry = "Update tsys_trans_log Set response_data = '" . $result . "', response_date_time = '" . date('Y-m-d H:i:s') . "' Where log_id = " . $log_req_id . " ";
        $log_res_sql = imw_query($log_res_qry);

        // DECODE result data from json format
        $result = json_decode($result, true);

        if (curl_errno($ch)) {
            // If error then set curl message
            $this->set_curl_msg($ch);
        }

        // Close Curl Request
        curl_close($ch);

        return $result;
    }

    private function set_curl_msg($curl) {

        $this->curl_error_no = curl_errno($curl);
        $this->curl_error_msg = curl_error($curl);
    }

    private function reset_curl_msg() {

        $this->curl_error_no = false;
        $this->curl_error_msg = false;
    }

    private function handle_error($msg) {

        if (trim($msg))
            die($msg);
    }
    
    
    public function updateToTransactionLog($return,$otherParams) {
        $paymentStr='';
        $scheduID='';
        $encountID='';
        $referenceNumber='';
        $MotoMode='';
        $OrderNumber='';
        $cardDataSource='';
        $tsys_payment_type_log_id='';
        $isRecurring='';
        $pos_pay_count='';
        $pos_curr_pay_count='';
        $return['TransactionType']='01'; //default SALE
        if (!empty($otherParams) && is_array($otherParams)) {
            $paymentStr= addslashes($otherParams['paymentArr']);
            $scheduID=$otherParams['scheduID'];
            $encountID=$otherParams['encountID'];
            $referenceNumber=$otherParams['referenceNumber'];
            $tsys_payment_type_log_id=$otherParams['tsys_payment_type_log_id'];
            $cardDataSource=$otherParams['cardDataSource'];
            $MotoMode=$otherParams['MotoMode'];
            $OrderNumber=$otherParams['OrderNumber'];
            $isRecurring=$otherParams['isRecurring'];
            $pos_pay_count=$otherParams['pos_pay_count'];
            $pos_curr_pay_count=$otherParams['pos_curr_pay_count'];
            $return['TransactionType']=$otherParams['TransactionType'];
        }
        
        $response_time = date('Y-m-d H:i:s');
        
        $CardType=$return['cardType'];
        
        switch($return['cardType']) {
            case 'V':
                $CardType='01';
                break;
            case 'M':
                $CardType='02';
                break;
            case 'AX':
            case 'X':
                $CardType='03';
                break;
            case 'Dis':
            case 'R':
                $CardType='04';
                break;
            default:
                $CardType='99';
                break;
        }
        
        $sql1 = 'INSERT INTO tsys_possale_transaction (patient_id,operator_id,scheduID,encounter_id,laneId,transactionAmount,
                HostInformation,AmountInformation,AccountInformation,TraceInformation,
                AVSinformation,CommercialInformation,motoEcommerce,AdditionalInformation,
                TransactionNumber,Account,ExpireDate,CardType,HostResponseCode,
                HostResponseMessage,AuthCode,HostReferenceNumber,TraceNumber,BatchNumber,
                TimeStamp,added_on,status,ResponseCode,ResponseMessage,TransactionType,log_referenceNumber,tsys_payment_type_log_id,
                motoType,motoOrderNumber,token,is_api,isRecurring,paymentCount,currentPaymentCount)
        VALUES ("' . $this->patient_id . '", "' . $this->operator_id . '","' . $scheduID . '","' . $encountID . '","' . $this->laneID . '", "' . $return['transactionAmount'] . '", 
                 "' . $return['HostInformation'] . '", "' . $return['AmountInformation'] . '", "' . $return['AccountInformation'] . '", "' . $return['TraceInformation'] . '",
                "' . $return['AVSinformation'] . '", "' . $return['CommercialInformation'] . '", "' . $return['motoEcommerce'] . '", "' . $return['AdditionalInformation'] . '",
                "' . $return['transactionID'] . '", "' . $return['maskedCardNumber'] . '", "' . $return['expirationDate'] . '", "' . $CardType . '", "' . $return['hostResponseCode'] . '",
                "' . $return['responseMessage'] . '", "' . $return['authCode'] . '", "' . $return['hostReferenceNumber'] . '", "' . $return['TraceNumber'] . '", "' . $return['BatchNumber'] . '",
                "' . $return['TimeStamp'] . '", "' . $response_time . '", "' . $return['status'] . '", "' . $return['responseCode'] . '", "' . $return['responseMessage'] . '",
                "' . $return['TransactionType'] . '","' . $referenceNumber . '","' . $tsys_payment_type_log_id . '",
                "' . $MotoMode . '", "'. $OrderNumber .'", "'. $return['token'] .'","'.$this->is_api.'", "'. $isRecurring .'", "'. $pos_pay_count .'", "'. $pos_curr_pay_count .'")';
        
        imw_query($sql1);
                
    }
   
    
    
    /*Encryption manifeststring code starts here*/
    public function encryptManifest() {
        $merchantId=$this->mid;
        $deviceId=$this->deviceID;
        $transactionKey=$this->transactionKey;
        
        $data = str_pad($merchantId, 20) . str_pad($deviceId, 24) . str_pad(0, 12, 0, STR_PAD_LEFT) . str_pad((new DateTime)->format('mdY'), 8);
        $key = substr($transactionKey, 0, 16);
        $manifest = openssl_encrypt($data, 'AES-128-CBC', $key, OPENSSL_NO_PADDING, $key);
        $manifest = bin2hex($manifest);
        $hash = hash_hmac('md5', $transactionKey, $transactionKey);
        $finalmanifeststring=substr($hash, 0, 4) . $manifest . substr($hash, -4);
   
        
        
        $data1=array();
        $data1['data']=$data;
        $data1['key']=$key;
        $data1['manifest']=$manifest;
        $data1['hash']=$hash;
        $data1['finalmanifeststring']=$finalmanifeststring;
        $data1['script']=$this->manifest_url.$deviceId.'?'.$finalmanifeststring;
        return $data1;
    }
    /*Encryption manifeststring code ends here*/
    
    
    public function createReferenceNumber($ptId) {
        $referenceNumber=false;
        if(empty($ptId)==false) {
            $added_on_time = date('Y-m-d H:i:s');
            $sql='INSERT INTO tsys_payment_type_log (patient_id,posMachine,added_on) VALUES ("' . $ptId . '", "ABSENT", "' . $added_on_time . '") ';
            imw_query($sql);
            $refInsert_id=imw_insert_id();
            $referenceNumber=$ptId.'*'.$refInsert_id;
            if($refInsert_id && $referenceNumber) {
                //$updatesql='UPDATE tsys_payment_type_log SET refrenceNumber="'.$referenceNumber.'" WHERE id="'.$refInsert_id.'" ';
                //imw_query($updatesql);
                
                $data=array();
                $transaction_amount=$_REQUEST['amount'];
                $data['transactionAmount']=$transaction_amount;
                $data['laneId']=$this->laneID;
                $data['scheduID']=0;
                $data['encounter_id']=0;
                $data['transactionType']='01';
                $data['transactionNumber']='';
                $data['referenceNumber']=$referenceNumber;
                $data['tsysOrderNumber']='';
                $data['device_id']=$_REQUEST['tsys_device_id'];
                $data['device_url']='';
                $data['final_hex_string']='';
                $data['operator_id']=$_REQUEST['providerID'];
                $data['merchant_id']=0;
                $data['facility_id']=$_REQUEST['facilityID'];

                $devices_sql="Select tsys_merchant.id as merchant_id, tsys_device_details.id as device_id from tsys_device_details 
                          JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
                          WHERE device_status=0 
                          AND tsys_device_details.facility_id='".$_REQUEST['facilityID']."' 
                          AND tsys_device_details.id='".$_REQUEST['tsys_device_id']."' 
                          AND merchant_status=0 
                          ";
                $resp = imw_query($devices_sql);
                if ($resp && imw_num_rows($resp) > 0) {
                    $row = imw_fetch_assoc($resp);
                    $data['merchant_id']=$row['merchant_id'];
                }

                $updatesql='UPDATE tsys_payment_type_log SET refrenceNumber="'.$referenceNumber.'", transactionAmount="'.$data['transactionAmount'].'", laneId="'.$data['laneId'].'", scheduID="'.$data['scheduID'].'" 
                            , encounter_id="'.$data['encounter_id'].'", transactionType="'.$data['transactionType'].'", transactionNumber="'.$data['transactionNumber'].'"
                            , tsysOrderNumber="'.$data['tsysOrderNumber'].'", device_id="'.$data['device_id'].'", device_url="'.$data['device_url'].'", final_hex_string="'.$data['final_hex_string'].'"
                            , operator_id="'.$data['operator_id'].'", merchant_id="'.$data['merchant_id'].'", facility_id="'.$data['facility_id'].'"
                            WHERE id="'.$refInsert_id.'" ';

                imw_query($updatesql);
                
            }
        }

        return $referenceNumber;
    }
    
}

//$obj = new TransitAPI();

//END CLASS
?>