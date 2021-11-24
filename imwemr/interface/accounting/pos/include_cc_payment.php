<?php

$patient_id=$_SESSION['patient'];
$operator_id=$_SESSION['authId'];
$login_facility=$_SESSION['login_facility'];

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

$no_pos_selected='';
if($editRow_tsys_trans_id<=0 && $chk_trans_edit_id>0) {
    $no_pos_selected='selected="selected" ';
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
            //$selected='selected="selected" ';
            $no_pos_selected='selected="selected" ';
        } else {
            if($no_pos_selected==""){
              $selected=($row['d_id']==$defaultDevice)?'selected="selected" ':'';
            }
        }
        $devices_option .= "<option ".$selected." data-device_ip='".$ipAddress."' data-device_url='".$device_url."' value='" . $row['d_id'] . "'>" . $row['deviceName'] . "</option>";
    }
}

//Token Dropdown
$card_array=array();
$sql='select id,patient_id,TransactionNumber,token,Account,ExpireDate,CardType
        from tsys_possale_transaction 
        where patient_id="'.$patient_id.'" 
        and token!="" 
		group by token 
		order by added_on desc';
$sql_rs=imw_query($sql);
$sql_count_rs=imw_num_rows($sql_rs);
while( $row=imw_fetch_assoc($sql_rs) ) {
    $card_array[]=$row;
}

$current_month=date('m');
$current_year=date('Y');
$tokenStr='';
foreach($card_array as $key) {
     switch ($key['CardType']) {
        case '01':
            $CardType = 'Visa';
            break;
        case '02':
            $CardType = 'MC';
            break;
        case '03':
            $CardType = 'AX';
            break;
        case '04':
            $CardType = 'Dis';
            break;
        case '99':
            $CardType = 'Other';
            break;
        default:
            $CardType = 'Other';
            break;
    }
    
    $month='';
    $year='';
    if(strlen($key['ExpireDate'])==4){
        $month=substr($key['ExpireDate'], 0,2);
        $year=substr($key['ExpireDate'], 2,4);
        $year='20'.$year;
    }
    if(strlen($key['ExpireDate'])==6){
        $month=substr($key['ExpireDate'], 0,2);
        $year=substr($key['ExpireDate'], 2,6);
    }
    
    if($month!='' && $year!='') {
        if($current_year<$year || ($current_year<$year && $current_month>=$month) || ($current_year<=$year && $current_month<$month) ){
            $tokenStr.='<option data-cardtype="'.$CardType.'" data-expdate="'.$key['ExpireDate'].'" value="'.$key['token'].'">'.$key['Account'].' - '.$CardType.'</option>';
        } else {
           // $tokenStr.='<option data-cardtype="'.$CardType.'" data-expdate="'.$key['ExpireDate'].'" value="'.$key['token'].'" disabled style="color:red;">'.$key['Account'].' - '.$CardType.'</option>';
        }
    }
    
}

?>

<div class="clearfix"></div>
<div class="row <?php echo $cc_class;?>">
    <div class="col-sm-6" style="padding-left:15px;">
        <label>POS</label>
        <select name="tsys_device_url" id="tsys_device_url" class="form-control minimal" onchange="setDefaultDevice(this);">
            <option value="no_pos_device" <?php echo $no_pos_selected;?> >No POS</option>
            <?php echo $devices_option; ?>
        </select>
    </div>
<!--    <div class="col-sm-2">
        <label for="pos_card_type">Card Type</label>
        <select name="pos_card_type" id="pos_card_type" class="form-control minimal">
            <option value="">- Select -</option>
            <option value="HSA">HSA</option>
            <option value="FSA">FSA</option>
        </select>
    </div>-->
    <?php /* if($tokenStr!=''){ ?>
        <div class="col-sm-3">
            <label for="tsys_token">Select Card</label>
            <select name="tsys_token" id="tsys_token" class="form-control minimal">
                <option value=""> - SELECT - </option>
                <?php echo $tokenStr; ?>
            </select>
        </div>
    <?php } */ ?>
    <div class="col-sm-6">
        <label for="moto_trans_mode">Transaction Mode</label>
        <select name="moto_trans_mode" id="moto_trans_mode" class="form-control minimal" onchange="fill_card_details(this);">
            <option value="">- Select -</option>
            <option value="M">Mail Order (MOTO)</option>
            <option value="T">Phone Order (MOTO)</option>
<!--            <option value="I">Installment</option>-->
<!--            <option value="R">Recurring</option>-->
        </select>
        <input type="hidden" name="tsys_OrderNumber" id="tsys_OrderNumber" placeholder="Order Number" title="Order Number" value="">
    </div>

    <input type="hidden" name="laneId" id="laneId" value="<?php echo $laneID;?>" />
    <input type="hidden" name="swipeCard" id="swipeCard" value="" />
    <input type="hidden" name="referenceNumber" id="referenceNumber" value="" />
    <input type="hidden" name="tsys_payment_type_log_id" id="tsys_payment_type_log_id" value="" />
    
    <input type="hidden" name="pos_cardHolderName" id="pos_cardHolderName" value="" class="form-control">
    <input type="hidden" name="pos_cardNumber" id="pos_cardNumber" value="" class="form-control">
    <input type="hidden" name="pos_expirationDate" id="pos_expirationDate" value="" class="form-control">
    <input type="hidden" name="pos_cvv2" id="pos_cvv2" value="" class="form-control">
    <input type="hidden" name="pos_creditCardtype" id="pos_creditCardtype" value="" class="form-control">

    <input type="hidden" name="pos_pay_count" id="pos_pay_count" value="" placeholder="Payment Count"/>
    <input type="hidden" name="pos_curr_pay_count" id="pos_curr_pay_count" value="" placeholder="Current Payment Count"/>

</div>


<div class="modal" id="cccarddetailsmodal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" id="modal_title">Enter Credit Card Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="cardHolderName">Card Holder Name</label>
                            <input type="text" name="cardHolderName" id="cardHolderName" value="" class="form-control">
                        </div>	
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" name="cardNumber" id="cardNumber" value="" class="form-control" placeholder="Enter valid card number">
                        </div>	
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="expirationDate">Expiration Date</label>
                            <input type="text" name="expirationDate" id="expirationDate" value="" class="form-control" placeholder="mm/yy" onblur="return expDate('expirationDate');">
                        </div>	
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="cvv2">CVV</label>
                            <input type="text" name="cvv2" id="cvv2" value="" class="form-control" placeholder="CVV">
                        </div>	
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="creditCardtype">Credit Card Type</label>
                            <select name="creditCardtype" id="creditCardtype" class="form-control minimal" placeholder="Card Type" data-width="100%" data-title="Please Select">
                                <?php
                                    //--- SET CREDIT CARD DROP DOWN ---
                                    $ccr_name_arr = array(""=>"");
                                    $ccr_name_arr["AX"] = "American Express";
                                    $ccr_name_arr["Care Credit"] = "Care Credit";
                                    $ccr_name_arr["Dis"] = "Discover";
                                    $ccr_name_arr["MC"] = "Master Card";
                                    $ccr_name_arr["Visa"] = "Visa";
                                    $ccr_name_arr["Other"] = "Other";
                                
                                    foreach($ccr_name_arr as $key => $val){
                                        $sel = '';
                                        if($key == $cr_selected){
                                            $sel = 'selected';
                                        }
                                        echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                                    }
                                ?>
                            </select>
                        </div>	
                    </div>
                    <div class="clearfix"></div>
                    <div id="inst_paycount" style="display:none;">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="tpos_pay_count">Payment Count</label>
                                <input type="text" name="tpos_pay_count" id="tpos_pay_count" value="" class="form-control" placeholder="Payment Count"/>
                            </div>	
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="tpos_curr_pay_count">Current Payment Count</label>
                                <input type="text" name="tpos_curr_pay_count" id="tpos_curr_pay_count" value="" class="form-control" placeholder="Current Payment Count"/>
                            </div>	
                        </div>
                    </div>
                    <div class="clearfix"></div>
                        
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveccardData();">Done</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        
    </div>
  </div>
</div>


<script>
    var fcount=false;
    var JS_WEB_ROOT_PATH='<?php echo $GLOBALS['webroot'] ?>';
    <?php if(isset($fcount) && $fcount > 0) { ?>
        fcount='<?php echo $fcount;?>';
    <?php } ?>
    var scheduID=0;
    <?php if(isset($scheduID) && $scheduID > 0) { ?>
        scheduID='<?php echo $scheduID;?>';
    <?php } ?>
    var encounter_id=0;
    <?php if(isset($encounter_id) && $encounter_id > 0) { ?>
        encounter_id='<?php echo $encounter_id;?>';
    <?php } ?>
    
    var tsys_patient_id="<?php echo $patient_id; ?>";
        
    var laneId="10000001";
    <?php if($laneID) { ?>
        laneId="<?php echo $laneID; ?>";
    <?php } ?>
        
    var target_id="10000001";
    <?php if($target_ids) { ?>
        target_id="<?php echo $target_ids; ?>";
    <?php } ?>

    var pos_patient_id='<?php echo $patient_id;?>';
    //var patient_id="<?php echo $patient_id; ?>";

</script>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/pos/jquery.base64.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/pos/pos.js"></script>

<script>

function setDefaultDevice(obj) {
    var tsys_device_id=$(obj).val();
    if(!tsys_device_id) {
        tsys_device_id=$('#tsys_device_url').val();
    }
    var postData={tsys_device_id:tsys_device_id};
    $.ajax({
        type: "POST",
        url:top.JS_WEB_ROOT_PATH+'/interface/accounting/pos/pos_handler.php?method=set_user_device',
        dataType:'JSON',
        data:postData,
        success: function(r){
        }
    });
}

function make_cccard_payment(obj) {

    var totalAmt=0;
    var paymentArr={};

    if(typeof($('#laneId').val())!='undefined') { laneId=$('#laneId').val(); }

    var completeValue=calculateTotalAmount();
    if(!completeValue) {
        console.log('completeValue missing.');
        return false;
    }

   // return false;
    var completeArr=completeValue.split('~~');
    totalAmt=completeArr[0];
    paymentArr=completeArr[1];
    
    var MotoMode=$('#moto_trans_mode').val();
    if(MotoMode && MotoMode!='') {
        var posMachine='NOT PRESENT';
    } else {
        var posMachine='PRESENT';
    }
    
    /*Create referenceNumber using ajax Log table entry */
    createReferenceNumber(posMachine);
    var referenceNumber=$('#log_referenceNumber').val();
    if(!referenceNumber) {
        console.log('referenceNumber does not exists.');
        return false;
    }
    
    var tsysOrderNumber = {};
    var MotoMode=$('#moto_trans_mode').val();
    if(MotoMode && MotoMode!='') {
        tsysOrderNumber.MotoMode=MotoMode;
        generateOrderNumber();
        tsysOrderNumber.OrderNumber=$('#tsys_OrderNumber').val();
    }
    
    show_cc_loading_image('show','', 'Please Wait...');

    if(MotoMode && MotoMode!='') {
        pos_api_payment(totalAmt,referenceNumber,tsysOrderNumber);
    } else {
        //pos_machine_payment(totalAmt,referenceNumber,tsysOrderNumber);
        pos_machine_payment(totalAmt,referenceNumber);
    }

}


/* Calculate total payment amount */
function calculateTotalAmount() {
    var paymentArr={};
    var totalAmt=0;
    var temptotalAmt=0;
    if(fcount) {
        for(i=1;i<=12;i++){
            var objtarget_id=target_id+i;//console.log(objtarget_id);
            var optionValue=$('#'+objtarget_id).val();
            if(optionValue=='Credit Card' && $('#chkbox_'+i).is(':checked')==true){
                var optionLabel=$('label[for=chkbox_'+i+']').text();
                if($('#item_pay_'+i).val()>0 || $('#item_pay_'+i).val()!='') {
                    totalAmt=parseFloat($('#item_pay_'+i).val());
                    totalAmt=Math.round(totalAmt*100);
                    paymentArr[optionLabel]=totalAmt;
                    temptotalAmt=totalAmt+temptotalAmt;
                    totalAmt=temptotalAmt;
                }
            }
        }
    } else {
        var optionValue=$('#'+target_id).val();
        if(optionValue=='Credit Card'){
            if('<?php echo $laneID;?>' == '10000003') {
                var optionLabel='Pre Payment';
                var optionAmt=$('#paid_amount').val();
            } else {
                var optionLabel='Amount';
                var optionAmt=$('#paidAmount').val();
            }

            if(optionAmt>0 || optionAmt!='') {
                totalAmt=parseFloat(optionAmt);
                totalAmt=Math.round(totalAmt*100);
                paymentArr[optionLabel] = totalAmt;
            }
        }
    }
    if(totalAmt==0) {
        top.fAlert('Enter Valid Transaction Amount.');
        return false;
    }

    return totalAmt+'~~'+JSON.stringify(paymentArr);
}


//function pos_machine_payment(totalAmt,referenceNumber, tsysOrderNumber) {
function pos_machine_payment(totalAmt,referenceNumber) {
    //tsysOrderNumber=tsysOrderNumber?tsysOrderNumber:{};
    var alert_msg='';
    //transactionType Default SALE="01"
    var transactionType="01";
    var laneId=$('#laneId').val();
    var transactionNumber=false;
    //chargeAmount( totalAmt, laneId, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber, '', '', tsysOrderNumber );
    chargeAmount( totalAmt, laneId, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber );
}


function void_transaction(transactionNumber,totalAmt,transactionType,referenceNumber,voidid,obj) {
    if(obj!='partial'){
        var r=confirm("Are you sure to void this transaction?");
        if (r==false)
        {
            return false;
        }

        $('#tsys_void_id').val(voidid);

        if(totalAmt) {
            totalAmt=parseFloat(totalAmt);
            totalAmt=Math.round(totalAmt*100);
        } else {
            totalAmt=false;  
        }
    }	

    var laneId=$('#laneId').val();
    var is_api=$(obj).data('is_api');

    var partialStatus=false;
    if(obj=='partial'){
        partialStatus=obj;
        voidid="";
    }

    $('#tsys_last_status').val('VOIDED');

    if(obj=='btnVoid') {
        transactionNumber='';
    } else {
        if(!transactionNumber) {
            fAlert('Invalid Transaction.');
            return false;
        }
    }
    
    if(is_api && is_api!='') {
        var posMachine='NOT PRESENT';
    } else {
        var posMachine='PRESENT';
    }
    
    /*Create referenceNumber using ajax Log table entry */
    createReferenceNumber(posMachine);
    var referenceNumber=$('#log_referenceNumber').val();
    if(!referenceNumber) {
        console.log('referenceNumber does not exists.');
        return false;
    }
    
    show_cc_loading_image('show','', 'Please Wait...');
    
    if(is_api==1) {
        var tsysOrderNumber={};
        tsysOrderNumber.transactionNumber=transactionNumber;
        
        pos_api_payment(totalAmt,referenceNumber,tsysOrderNumber,'void');
    } else {
        totalAmt=false;
        chargeAmount( totalAmt, laneId, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber, voidid, partialStatus );
    }
    
}

function return_transaction(transactionNumber,totalAmt,transactionType,referenceNumber,voidid,obj) {
    var laneId=$('#laneId').val();
    $('#tsys_last_status').val('RETURNED');
    $('#tsys_void_id').val(voidid);

    if(totalAmt) {
        totalAmt=parseFloat(totalAmt);
        totalAmt=Math.round(totalAmt*100);
    } else {
        totalAmt=false;  
    }
    
    var is_api=$(obj).data('is_api');
    
    var MotoMode=$('#moto_trans_mode').val();
    if((MotoMode && MotoMode!='') || is_api) {
        var posMachine='NOT PRESENT';
    } else {
        var posMachine='PRESENT';
    }

    if(obj=='btnRefund') {
        totalAmt=false;
        transactionNumber=false;

        //var posMachine='PRESENT';
        /*Create referenceNumber using ajax Log table entry */
        createReferenceNumber(posMachine);
        var referenceNumber=$('#log_referenceNumber').val();
        if(!referenceNumber) {
            console.log('referenceNumber does not exists.');
            return false;
        }
    } else {
        if(!transactionNumber && !MotoMode) {
            fAlert('Invalid Transaction.');
            return false;
        }
    }
    
    var tsysOrderNumber = {};
    var MotoMode=$('#moto_trans_mode').val();
    if(MotoMode && MotoMode!='') {
        tsysOrderNumber.MotoMode=MotoMode;
        generateOrderNumber();
        tsysOrderNumber.OrderNumber=$('#tsys_OrderNumber').val();
    }

    show_cc_loading_image('show','', 'Please Wait...');
    //totalAmt=false;
    if(is_api==1) {
        tsysOrderNumber.transactionNumber=transactionNumber;
        if(MotoMode!='' && saveccardData()) { delete tsysOrderNumber['transactionNumber']; }
        pos_api_payment(totalAmt,referenceNumber,tsysOrderNumber,'refund');
    } else {
        chargeAmount( totalAmt, laneId, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber, voidid );
    }
    
}

function show_transaction_popup(){
    var urlRTEFile=top.JS_WEB_ROOT_PATH+'/interface/accounting/pos/patient_pos_transaction_log.php?patient_id=<?php echo $patient_id;?>';
    window.open(urlRTEFile,'','toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,width=1550,height=700,left=10,top=10');
}

function cc_card_check() {
    var cc_card=false;
    for(j=1;j<=12;j++){
        objAll = $('#pay_method_'+j);
        objPay = $('#item_pay_'+j);
        chkbox = $('#chkbox_'+j);
        if(objAll && objAll.val()=='Credit Card' && objPay.val() != '' && chkbox.is(':checked')==true){
            cc_card=true;
        }
    }
    if(typeof($('#paymentMode'))!='undefined' && $('#paymentMode').length > 0 && $('#paymentMode').val()=='Credit Card') {
        cc_card=true;
    }
    if(typeof($('#payment_mode'))!='undefined' && $('#payment_mode').length > 0 && $('#payment_mode').val()=='Credit Card') {
        cc_card=true;
    }

    return cc_card;
}

function fill_card_details() {
    var tsys_device_id=$('#tsys_device_url option:selected').val();
    var moto_trans_mode=$('#moto_trans_mode option:selected').val();
    var tsys_token='';if($('#tsys_token').length>0 && $('#tsys_token option:selected').val()!=''){tsys_token=$('#tsys_token option:selected').val();}
    if(moto_trans_mode!='' && cc_card_check() && tsys_token==''){
        $('#cccarddetailsmodal').modal('show');
        if(moto_trans_mode=='I') $("#inst_paycount").show();
        else $("#inst_paycount").hide();
    }
}

function saveccardData() {
    var alertmsg='';
    if($('#cardHolderName').val()=='') {
       alertmsg+='Card Holder Name is required. <br>'; 
    }
    //if($('#cardNumber').val()=='' || !$.isNumeric($('#cardNumber').val()) || $.trim($('#cardNumber').val()).length!=16 ) {
    if($('#cardNumber').val()=='' || !$.isNumeric($('#cardNumber').val()) ) {
       alertmsg+='Enter Vaild Card Number. <br>'; 
       $('#cardNumber').val('');
    }
    if($('#expirationDate').val()=='') {
       alertmsg+='Enter Vaild Expiration Date. <br>';
       $('#expirationDate').val('');
    }
    if($('#cvv2').val()=='') {
       alertmsg+='Enter Vaild CVV. <br>';
       $('#cvv2').val('');
    }
    var creditCardtype=$('#creditCardtype option:selected').val();
    if(creditCardtype=='') {
       alertmsg+='Credit Card Type is required. <br>'; 
    }
    if(alertmsg!='') {
        fAlert(alertmsg);
        return false;
    }

    $('#pos_cardHolderName').val( btoa($('#cardHolderName').val()) );
    $('#pos_cardNumber').val( btoa($('#cardNumber').val()) );
    $('#cCNo').val($('#cardNumber').val().slice(-4));
    $('#cc_no').val($('#cardNumber').val().slice(-4));
    $('#pos_expirationDate').val( btoa($('#expirationDate').val()) );
    $('#pos_cvv2').val( btoa($('#cvv2').val()) );
    $('#date2').val($('#expirationDate').val());
    $('#expireDate').val($('#expirationDate').val());
    $('#cc_exp_date').val($('#expirationDate').val());
    $('#pos_creditCardtype').val(creditCardtype);
    $("#creditCardCo option[value='"+creditCardtype+"']").prop('selected', true);
    $("#credit_card_co option[value='"+creditCardtype+"']").prop('selected', true);
    $("#creditCardCo").selectpicker("refresh");
    
    $('#pos_pay_count').val('');
    $('#pos_curr_pay_count').val('');
    if($('#moto_trans_mode option:selected').val()=='I') {
        $('#pos_pay_count').val($('#tpos_pay_count').val());
        $('#pos_curr_pay_count').val($('#tpos_curr_pay_count').val());
    }
    $('#cccarddetailsmodal').modal('hide');
    return true;
}



</script>