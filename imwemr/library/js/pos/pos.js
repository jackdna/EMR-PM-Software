//HEX TO BASE64
function hexToBase64(str) {
	return $.base64.btoa(String.fromCharCode.apply(null,
		str.replace(/\r|\n/g, "").replace(/([\da-fA-F]{2}) ?/g, "0x$1 ").replace(/ +$/, "").split(" "))
	);
}

function isValidIP(ip) {
    var reg =  /^(\d{1,2}|0\d\d|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|0\d\d|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|0\d\d|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|0\d\d|1\d\d|2[0-4]\d|25[0-5])$/     
    return reg.test(ip);
}
    
function getDeviceURL() {
    var tsys_device_url = $('#tsys_device_url option:selected').data('device_url');
    var tsys_device_ip = $('#tsys_device_url option:selected').data('device_ip');

    if(typeof(tsys_device_url)=='undefined' || tsys_device_url=='' || tsys_device_url=='undefined') {
         $.ajax({
            type: "POST",
            url:top.JS_WEB_ROOT_PATH+'/interface/accounting/pos/pos_handler.php?method=get_default_device',
            dataType:'JSON',
            async: false,
            complete : function(r) {
                tsys_device_url=r.responseText;
            }
        });
        return tsys_device_url;
    }else {
        return tsys_device_url;
    }
}

function getLRC(params){

	var lrc = 0;
	for(i=1; i< params.length; i++){
		var type_of = typeof(params[i]);
		if(type_of == "string"){
			var element = params[i].split("");
			for(ii=0; ii<element.length; ii++){
				lrc ^= element[ii].charCodeAt(0);
			}
		}else{
			lrc ^= params[i];
		}
	}
	return (lrc>0)?String.fromCharCode(lrc):0;
}

//BASE64 TO HEX
function base64ToHex(str)
{
	for (var i = 0, bin = $.base64.atob(str), hex = []; i < bin.length; ++i)
	{
		var tmp = bin.charCodeAt(i).toString(16);
		if (tmp.length === 1) tmp = "0" + tmp;
		hex[hex.length] = tmp;
	}
	return hex.join(" ");
}

function AddBase64(elements,type,objectInfo){

	var empty = 0;
	var arr = [];
	arr = arr.concat(elements);
	for(name in objectInfo){
		if(objectInfo[name] == '' && type!="additionalInformation")
		{
			arr.push(this.mUS.code);
			continue;
		}
		if(type == "additionalInformation"){
			if(objectInfo[name] == '')
				continue;
			empty++;
			arr.push(base64ToHex($.base64.btoa(name+"="+objectInfo[name].toString())));
		}else{
			empty++;
			arr.push(base64ToHex($.base64.btoa(objectInfo[name].toString())));
		}
		arr.push(this.mUS.code);
	}
	arr.pop();
	if(empty==0 && type!="additionalInformation"){
		arr = elements;
	}
	if(empty==0 && type=="additionalInformation"){
		arr.push(this.mFS.code);
	}

	return arr;
}

function StringToHex(response){
	var responseHex = "";
	for(var i=0; i<response.length; i++){
		if(responseHex == "")
			responseHex = response.charCodeAt(i).toString(16).length<2?'0'+response.charCodeAt(i).toString(16):response.charCodeAt(i).toString(16);
		else
			responseHex += response.charCodeAt(i).toString(16).length<2?" " + '0'+response.charCodeAt(i).toString(16):" " + response.charCodeAt(i).toString(16);
	}
	return responseHex;
		
}

function HexToString(response){
	var responseHex = "";
	var arr = response.split(" ");
	for(var i=0; i<arr.length; i++){
		if(arr[i] == "")
			continue;
		responseHex += String.fromCharCode(parseInt(arr[i],16));
	}
	return responseHex;	
}

/*
* Hexadecimal values to be used as separators for final encoded for sending to POS
*/
mStx = {
   "hex" : 0x02,
   "code" : "02"
};

mFS = {
   "hex" : 0x1c,
   "code" : "1c"
};

mEtx = {
   "hex" : 0x03,
   "code" : "03"
};

mUS = {
   "hex" : 0x1F,
   "code" : "1F"
};

function structureCreditResponse( data )
{
	var response = {};
	var i=0,j=-1;

	response.Status = data[++i];
    response.Command = data[++i];
    response.Version = data[++i];
    response.ResponseCode = data[++i];
    response.ResponseMessage = data[++i];

    
    response.HostInformation = (data[++i]!=undefined)?data[i]:'';
    if(response.HostInformation == ''){
        response.HostInformation = {};
        response.HostInformation.HostResponseCode = '';
        response.HostInformation.HostResponseMessage = '';
        response.HostInformation.AuthCode = '';
        response.HostInformation.HostReferenceNumber = '';
        response.HostInformation.TraceNumber = '';
        response.HostInformation.BatchNumber = '';
    }else{
        response.HostInformation.HostResponseCode = (data[i][++j]!=undefined)?data[i][j]:'';
        response.HostInformation.HostResponseMessage = (data[i][++j]!=undefined)?data[i][j]:'';
        response.HostInformation.AuthCode = (data[i][++j]!=undefined)?data[i][j]:'';
        response.HostInformation.HostReferenceNumber = (data[i][++j]!=undefined)?data[i][j]:'';
        response.HostInformation.TraceNumber = (data[i][++j]!=undefined)?data[i][j]:'';
        response.HostInformation.BatchNumber = (data[i][++j]!=undefined)?data[i][j]:'';
    }

    response.TransactionType = (data[++i]!=undefined)?data[i]:'';

    response.AmountInformation = (data[++i]!=undefined)?data[i]:'';
    if(response.AmountInformation == ''){
        response.AmountInformation = {};
        response.AmountInformation.ApproveAmount = '';
        response.AmountInformation.AmountDue = '';
        response.AmountInformation.TipAmount = '';
        response.AmountInformation.CashBackAmount = '';
        response.AmountInformation.MerchantFee_SurchargeFee = '';
        response.AmountInformation.TaxAmount = '';
        response.AmountInformation.Balance1 = '';
        response.AmountInformation.Balance2 = '';

    }else{
        j=-1;
        response.AmountInformation.ApproveAmount = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AmountInformation.AmountDue = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AmountInformation.TipAmount = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AmountInformation.CashBackAmount = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AmountInformation.MerchantFee_SurchargeFee = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AmountInformation.TaxAmount = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AmountInformation.Balance1 = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AmountInformation.Balance2 = (data[i][++j]!=undefined)?data[i][j]:'';
    }

    response.AccountInformation = (data[++i]!=undefined)?data[i]:'';
    if(response.AccountInformation == ''){
        response.AccountInformation = {};
        response.AccountInformation.Account = '';
        response.AccountInformation.EntryMode = '';
        response.AccountInformation.ExpireDate = '';
        response.AccountInformation.EBTtype = '';
        response.AccountInformation.VoucherNumber = '';
        response.AccountInformation.NewAccountNo = '';
        response.AccountInformation.CardType = '';
        response.AccountInformation.CardHolder = '';
        response.AccountInformation.CVDApprovalCode = '';
        response.AccountInformation.CVDMessage = '';
        response.AccountInformation.CardPresentIndicator = '';

    }else{
        j=-1;
        response.AccountInformation.Account = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.EntryMode = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.ExpireDate = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.EBTtype = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.VoucherNumber = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.NewAccountNo = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.CardType = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.CardHolder = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.CVDApprovalCode = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.CVDMessage = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AccountInformation.CardPresentIndicator = (data[i][++j]!=undefined)?data[i][j]:'';
    }

    response.TraceInformation = (data[++i]!=undefined)?data[i]:'';
    if(response.TraceInformation == ''){
        response.TraceInformation = {};
        response.TraceInformation.TransactionNumber = '';
        response.TraceInformation.ReferenceNumber = '';
        response.TraceInformation.TimeStamp = '';
    }else{
        j=-1;
        response.TraceInformation.TransactionNumber = (data[i][++j]!=undefined)?data[i][j]:'';
        response.TraceInformation.ReferenceNumber = (data[i][++j]!=undefined)?data[i][j]:'';
        response.TraceInformation.TimeStamp = (data[i][++j]!=undefined)?data[i][j]:'';
    }

    response.AVSinformation = (data[++i]!=undefined)?data[i]:'';
    if(response.AVSinformation == ''){
        response.AVSinformation = {};
        response.AVSinformation.AVSApprovalCode = '';
        response.AVSinformation.AVSMessage = '';
    }else{
        j=-1;
        response.AVSinformation.AVSApprovalCode = (data[i][++j]!=undefined)?data[i][j]:'';
        response.AVSinformation.AVSMessage = (data[i][++j]!=undefined)?data[i][j]:'';
    }

    response.CommercialInformation = (data[++i]!=undefined)?data[i]:'';
    if(response.CommercialInformation == ''){
        response.CommercialInformation = {};
        response.CommercialInformation.PONumber = '';
        response.CommercialInformation.CustomerCode = '';
        response.CommercialInformation.TaxExempt = '';
        response.CommercialInformation.TaxExemptID = '';
        response.CommercialInformation.MerchantTaxID = '';
        response.CommercialInformation.DestinationZipCode = '';
        response.CommercialInformation.ProductDescription = '';
    }else{
        j=-1;
        response.CommercialInformation.PONumber = (data[i][++j]!=undefined)?data[i][j]:'';
        response.CommercialInformation.CustomerCode = (data[i][++j]!=undefined)?data[i][j]:'';
        response.CommercialInformation.TaxExempt = (data[i][++j]!=undefined)?data[i][j]:'';
        response.CommercialInformation.TaxExemptID = (data[i][++j]!=undefined)?data[i][j]:'';
        response.CommercialInformation.MerchantTaxID = (data[i][++j]!=undefined)?data[i][j]:'';
        response.CommercialInformation.DestinationZipCode = (data[i][++j]!=undefined)?data[i][j]:'';
        response.CommercialInformation.ProductDescription = (data[i][++j]!=undefined)?data[i][j]:'';
    }

    response.motoEcommerce = (data[++i]!=undefined)?data[i]:'';
    if(response.motoEcommerce == ''){
        response.motoEcommerce = {};
        response.motoEcommerce.MOTO_ECommerceMode = '';
        response.motoEcommerce.TransactionType = '';
        response.motoEcommerce.SecureType = '';
        response.motoEcommerce.OrderNumber = '';
        response.motoEcommerce.Installments = '';
        response.motoEcommerce.CurrentInstallment = '';
    }else{
        j=-1;
        response.motoEcommerce.MOTO_ECommerceMode = (data[i][++j]!=undefined)?data[i][j]:'';
        response.motoEcommerce.TransactionType = (data[i][++j]!=undefined)?data[i][j]:'';
        response.motoEcommerce.SecureType = (data[i][++j]!=undefined)?data[i][j]:'';
        response.motoEcommerce.OrderNumber = (data[i][++j]!=undefined)?data[i][j]:'';
        response.motoEcommerce.Installments = (data[i][++j]!=undefined)?data[i][j]:'';
        response.motoEcommerce.CurrentInstallment = (data[i][++j]!=undefined)?data[i][j]:'';
    }
    
    response.AdditionalInformation = (data[++i]!=undefined)?data[i]:'';
    if(response.AdditionalInformation == '')
        response.AdditionalInformation = {};
    var additionalInfoArr = response.AdditionalInformation,keyValue=[];
    for(i=0; i<additionalInfoArr.length; i++){
        keyValue = additionalInfoArr[i].split("=");
        response.AdditionalInformation[keyValue[0]] = keyValue[1];
        keyValue = [];
    }

    return response;
}

function chargeAmount( amount, laneId, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber, voidid, partialStatus, OrderNumberArr ){
	if(!OrderNumberArr || OrderNumberArr === null) OrderNumberArr = {};
    
    var posUrl = getDeviceURL();
    if(!posUrl) { console.log('Device URL not found.');return false; }
	/*
	 * This the application version for the POS machine.
	 * Option from Admin Setting for POS
	*/
	var version = "1.28";

	/*
	 * Type of the Transaction to be done.
	 * Based on the "Transaction Type" dropdown selected from the interface.
	 * @Allowed = Void, Sale, Return
	 * Valeus Mapping:
	 * @Sale : 01
	 * @Return : 02
	 * @Void : 16
	*/
   
   var hsa_fsa=$('#pos_card_type option:selected').val();
   
	var transactionType = transactionType;
	var OrderNumber = OrderNumberArr?OrderNumberArr.OrderNumber:"";
	var Token = (OrderNumberArr && OrderNumberArr.Token)?OrderNumberArr.Token:"";
	var PassThruDat = (hsa_fsa)?String(hsa_fsa)+' HealthCare,'+String(amount):"";
	var MotoMode = ( OrderNumberArr && OrderNumberArr.OrderNumber )?OrderNumberArr.MotoMode:"";
	var transactionNumber = transactionNumber?transactionNumber:"";
	var referenceNumber = referenceNumber?referenceNumber:"1";

	var amountInformation = {
		"CashBackAmount" : "",
		"FuelAmount" : "",
		"MerchantFee" : "",
		"TaxAmount" : "",
		"TipAmount" : "",
		"TransactionAmount" : amount?String(amount):""
	}

    var traceInformation = {};
        traceInformation.ReferenceNumber = String(referenceNumber);
        traceInformation.InvoiceNumber = "";
        traceInformation.AuthCode = "";
        traceInformation.TransactionNumber = String(transactionNumber);
        traceInformation.TimeStamp = "";
        traceInformation.ECRTransID = "";

	/*
	 * Request Parameters for POS
	*/
	var CreditInfo = {
		"command": 'T00',
		"version": version,
		"transactionType": transactionType,
		"amountInformation": amountInformation
	}

    var motoEcommerce = {};
        motoEcommerce.MOTO_E_CommerceMode = String(MotoMode);     //M for mail order //T for telephone order //$("select[name='MOTO_E_CommerceMode']").val();
        motoEcommerce.TransactionType = "S";     //$("select[name='TransactionType']").val();
        motoEcommerce.SecureType = "S";     //$("select[name='SecureType']").val();
        motoEcommerce.OrderNumber = String(OrderNumber);     //$("input[name='OrderNumber']").val();
        motoEcommerce.Installments = "";     //$("input[name='Installments']").val();
        motoEcommerce.CurrentInstallment = "";     //$("input[name='CurrentInstallment']").val();

    var tokenReq="";
    if(transactionType=='01') tokenReq="TOKENREQUEST=1";
    
    var additionalInformation ={};
        additionalInformation.TOKENREQUEST = String(tokenReq);
        additionalInformation.TOKEN = String(Token);           
        additionalInformation.CARDTYPE = "";
        additionalInformation.CARDTYPEBITMAP = "";
        additionalInformation.PASSTHRUDATA = (PassThruDat)?"PASSTHRUDATA="+String(PassThruDat):"";
        
        
	var posParameters = [];

	/*Command*/
	posParameters.push( mStx.hex );
	posParameters.push( CreditInfo.command );
	
	/*Version*/
	posParameters.push( mFS.hex );
	posParameters.push( CreditInfo.version );
	
	/*Transaction Type Details*/
	posParameters.push( mFS.hex );
	if( transactionType != '' )
	{
		posParameters.push( CreditInfo.transactionType );
	}

	/*
	 * Amount Detials
	*/
	posParameters.push( mFS.hex );

	/*
	 * Transaction Amount - amountInformation
	*/
   if(amount) {
        posParameters.push( amountInformation.TransactionAmount );
        posParameters.push( mUS.hex );
        posParameters.push( mUS.hex );
        posParameters.push( mUS.hex );
        posParameters.push( mUS.hex );
        posParameters.push( mUS.hex );
    }
	/*accountInformation*/
	posParameters.push( mFS.hex );

	/*traceInformation*/
	posParameters.push( mFS.hex );
	//posParameters.push( "1" );
	posParameters.push( traceInformation.ReferenceNumber );
	posParameters.push( mUS.hex );
    posParameters.push( mUS.hex );
	posParameters.push( mUS.hex );
    if(transactionNumber) {
        posParameters.push( traceInformation.TransactionNumber );
    }
	posParameters.push( mUS.hex );
	posParameters.push( mUS.hex );

	/*avsInformation*/
	posParameters.push( mFS.hex );

	/*cashierInformation*/
	posParameters.push( mFS.hex );

	/*commercialInformation*/
	posParameters.push( mFS.hex );

	/*motoEcommerce*/
    posParameters.push( mFS.hex );
    if( OrderNumberArr && OrderNumberArr.OrderNumber && OrderNumberArr.MotoMode ) {
        posParameters.push( motoEcommerce.MOTO_E_CommerceMode );
        posParameters.push( mUS.hex );
        posParameters.push( mUS.hex );
        posParameters.push( mUS.hex );
        posParameters.push( motoEcommerce.OrderNumber );
        posParameters.push( mUS.hex );
        posParameters.push( mUS.hex );
    }
	/*additionalInformation*/
	posParameters.push( mFS.hex );

	/*Closing Character*/
	posParameters.push( mEtx.hex );

	/*Chart Code for the Length*/
	var lrc = getLRC(posParameters);


	/*Prepare for base64 encoding.*/
	var command_hex = base64ToHex($.base64.btoa(CreditInfo.command));
	var version_hex = base64ToHex($.base64.btoa(CreditInfo.version));
	var transactionType_hex = base64ToHex($.base64.btoa(CreditInfo.transactionType));

	/*
	 *************************************
	 * Prepare element for base64 encoding
	 *************************************
	*/
	var elements = [];

	/*Starting Char*/
	elements.push( mStx.code );
	elements.push( command_hex );

	elements.push( mFS.code );
	elements.push( version_hex );

	elements.push( mFS.code );
	if( transactionType_hex != '' )
	{
		elements.push( transactionType_hex );
	}

	/*
	 *******************
	 *amountInformation
	 *******************
	 */
    elements.push( mFS.code );
    if(amount) {
        elements.push( base64ToHex($.base64.btoa(amountInformation.TransactionAmount)) );

        /*Blank Values*/
        /*TipAmount*/
        elements.push( mUS.code );
        /*TaxAmount*/
        elements.push( mUS.code );
        /*MerchantFee*/
        elements.push( mUS.code );
        /*FuelAmount*/
        elements.push( mUS.code );
        /*CashBackAmount*/
        elements.push( mUS.code );
    }
	/*
	 *******************
	 *accountInformation
	 *******************
	*/
	elements.push( mFS.code );

	/*
	 *******************
	 *traceInformation
	 *******************
	*/
	elements.push( mFS.code );
	/*ReferenceNumber*/
	//elements.push( base64ToHex($.base64.btoa("1")) );
	elements.push( base64ToHex($.base64.btoa(traceInformation.ReferenceNumber)) );
	/*InvoiceNumber*/
	elements.push( mUS.code );
	/*AuthCode*/
	elements.push( mUS.code );
    
    elements.push( mUS.code );
    if(transactionNumber) {
        /*TransactionNumber*/
        elements.push( base64ToHex( $.base64.btoa(traceInformation.TransactionNumber)) );
    }
	/*TimeStamp*/
	elements.push( mUS.code );
	/*ECRTransID*/
	elements.push( mUS.code );

	/*
	 *******************
	 *avsInformation
	 *******************
	*/
	elements.push( mFS.code );

	/*
	 *******************
	 *cashierInformation
	 *******************
	*/
	elements.push( mFS.code );

	/*
	 *******************
	 *commercialInformation
	 *******************
	*/
	elements.push( mFS.code );

	/*
	 *******************
	 *motoEcommerce
	 *******************
	*/
    elements.push( mFS.code );
    if( OrderNumberArr && OrderNumberArr.OrderNumber && OrderNumberArr.MotoMode ) {
        elements.push( base64ToHex( $.base64.btoa(motoEcommerce.MOTO_E_CommerceMode)) );
        elements.push( mUS.code );
        elements.push( mUS.code );
        elements.push( mUS.code );
        elements.push( base64ToHex( $.base64.btoa(motoEcommerce.OrderNumber)) );
        elements.push( mUS.code );
        elements.push( mUS.code );
    }
	/*
	 *******************
	 *additionalInformation
	 *******************
	*/
	elements.push( mFS.code );


	/*
	 *******************
	 *LRC base64 encoded
	 *******************
	*/
	elements.push( mEtx.code );
	elements.push( base64ToHex($.base64.btoa(lrc)) );

	var final_string = elements.join(" ");
	var final_b64 = hexToBase64(final_string);

	/*URL Should be dynamic - based on admin settings*/
    if(posUrl){
        posUrl = posUrl + '?' + final_b64;
    }
	//var posUrl = 'http://192.168.18.151:10009' + '?' + final_b64;

    //save transaction log when a requested is generated for POS device to make payments.
    savePosTransactionLog(amount, laneId, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber, voidid, partialStatus, OrderNumberArr, final_b64, posUrl);
    //console.log('saveStatus success'); return false;
	$.ajax({
		url : posUrl,
		method : 'GET',
		complete: function( data, status ){

			var response = data.responseText;
			
			var checkParams = StringToHex(response).split(" ").pop();
			var RedundancyCheck = StringToHex(response).split(" ").pop().substring(1);


			var check = getLRC(checkParams);

			if(check == RedundancyCheck){
				//get package detail info
				var packetInfo = [];
				var len = StringToHex(response).indexOf("03");
				var hex = StringToHex(response).slice(0,len).split(/02|1c/);

				var subHex = [];
				var subPacketInfo = [];

				for( var i=0; i<hex.length; i++ )
				{
					if(hex[i] != "")
					{
						if(hex[i].indexOf("1f")>0)
						{
							subHex = hex[i].split("1f");
							subPacketInfo = [];

							for(var j=0; j<subHex.length; j++)
							{
								if(subHex[j]!='')
								{
									subPacketInfo.push(HexToString(subHex[j]));
								}
							}
							
							packetInfo.push(subPacketInfo);
						}
						else
						{
							packetInfo[i] = HexToString(hex[i]);
						}
					}
				}
				var structuredResponse = structureCreditResponse(packetInfo);
                
                save_transaction(amount, transactionType, laneId, scheduID, encounter_id, structuredResponse, transactionNumber, referenceNumber, voidid, partialStatus );
                
			}
		}
	});
}


function save_transaction(totalAmt,trans_type,laneId,scheduID,encounter_id, structuredResponse, transactionNumber ,referenceNumber, voidid, partialStatus ) {
    var tsys_payment_type_log_id=$('#tsys_payment_type_log_id').val();
    var tsys_last_status=$('#tsys_last_status').val();
    var tsys_void_id=voidid?voidid: $('#tsys_void_id').val();;
    var postData={totalAmt:totalAmt,laneId:laneId,scheduID:scheduID,encounter_id:encounter_id,transactionNumber:transactionNumber,tsys_void_id:tsys_void_id,
        referenceNumber:referenceNumber,tsys_payment_type_log_id:tsys_payment_type_log_id,structuredResponse:structuredResponse};
    $.ajax({
        type: "POST",
        url:top.JS_WEB_ROOT_PATH+'/interface/accounting/pos/pos_handler.php?method=possale',
        dataType:'JSON',
        data:postData,
        success: function(r){
            if(r){
                show_cc_loading_image('hide');
                
                if(typeof($('#pos_counter').val())!='undefined') {
                    var i=$('#pos_counter').val();
                    if(i<=transNumberArr.length){
                        i++;
                        $('#pos_counter').val(i);
                        func_recursive();
                    }
                }
                
                if(r.TransactionNumber) {
                    $('#tsys_void_id').val(tsys_void_id);
                    $('#log_referenceNumber').val(referenceNumber);
                    $('#tsys_transaction_id').val(r.TransactionNumber);
                    $('#card_details_str_id').val(r.card_details_str);

                    var ApproveAmount=structuredResponse.AmountInformation.ApproveAmount;
                    var TransactionNumber=structuredResponse.TraceInformation.TransactionNumber;

                    var partialForm=partialStatus?'':'pos_submit_frm()';
                    var posmessage=partialStatus?'Transaction voided.':r.message;
                    if(partialStatus=='acc_void'){
                        partialForm=top.fmain.pos_submit_frm();
                    }
                    if(partialStatus=='acc_ret'){
                        var card_details_arr=$('#card_details_str_id').val().split('~~');
                        $('.ccnocls').val(card_details_arr[1]);   
                        posmessage='Return successful.';
                        partialForm=top.fmain.pos_submit_frm();
                    }
                    
                    if(totalAmt!=ApproveAmount && trans_type=="01") {
                        var transactionType="16";
                        void_transaction(TransactionNumber,ApproveAmount,transactionType,referenceNumber,voidid,'partial');
                    } else {
                        if(typeof($('#pos_counter').val())!='undefined' && typeof(transNumberArr)!='undefined' && i==transNumberArr.length) {
                            top.fAlert(posmessage+'<br>Transaction ID: '+r.TransactionNumber + '', '', partialForm, '');
                        } else if(typeof($('#pos_counter').val())=='undefined'){
                            top.fAlert(posmessage+'<br>Transaction ID: '+r.TransactionNumber + '', '', partialForm, '');
                        }
                    }
                }
                
                if(r.ResponseMessage) {
                    //fancyConfirm(r.ResponseMessage + '; Do you want to continue with submission?', '', 'pos_submit_frm()', 'return_false()');
                    top.fAlert(r.ResponseMessage);
                }
                
            }
        }
    });

}


function return_false() {
    return false;
}

/****function show/hide loading image*******/
function show_cc_loading_image(mode, padd_top, show_text){//TO SHOW / HIDE LOADING IMAGE
    if(mode == "show"){
        $("#div_loading_image").show();
        if(padd_top != "" && typeof(padd_top) != "undefined"){
            $("#div_loading_image").css("margin-top", padd_top+"px");
        }else{
            $("#div_loading_image").css("margin-top", "0px");
        }
        if(show_text != "" && typeof(show_text) != "undefined"){
            $("#div_loading_text").html(show_text).show();
        }
        $(".btn").prop('disabled', true);
    }
    if(mode == "hide"){
        $("#div_loading_text").html("").hide();
        $("#div_loading_image").hide();
        $(".btn").prop('disabled', false);
    }
}

function createReferenceNumber(posMachine) {
    var tsys_patient_id=pos_patient_id;
    var tsys_device_id=$('#tsys_device_url option:selected').val();
    var postData={tsys_patient_id:tsys_patient_id,posMachine:posMachine,tsys_device_id:tsys_device_id};
    $.ajax({
        type: "POST",
        url:top.JS_WEB_ROOT_PATH+'/interface/accounting/pos/pos_handler.php?method=refNum',
        dataType:'JSON',
        async: false,
        data:postData,
        success: function(r){
            if(r){
                $('#log_referenceNumber').val(r.referenceNumber);
                $('#tsys_payment_type_log_id').val(r.refInsert_id);
            }
        }
    });
}

/// 
function SetPOSVar(_this){
	
	$(_this).prop('disabled',true);
	
	top.show_loading_image('show');
	// Set Var Variables 
	var m_user = String($(_this).data('muser'));
	var m_pass = String($(_this).data('mpass'));
	var mid = String($(_this).data('mid'));
	var did = String($(_this).data('did'));
	// End get Set VAR Variable values
	
	var posUrl = $(_this).data('durl');//$(_this).data('durl');//top.JS_WEB_ROOT_PATH+ '/library/js/pos/pos_test.php';//
  if(!posUrl) { console.log('Device URL not found.');return false; }
	/*
	 * This the application version for the POS machine.
	 * Option from Admin Setting for POS
	*/
	var version = "1.40";

	/*
	 * Type of the Transaction to be done.
	 * Based on the "Transaction Type" dropdown selected from the interface.
	 * @Allowed = Void, Sale, Return
	 * Valeus Mapping:
	 * @Sale : 01
	 * @Return : 02
	 * @Void : 16
	*/
	var transactionType = '01';
	
	/*
	 * Request Parameters for POS
	*/
	var CreditInfo = {
		"command": 'A04',
		"version": version,
		"transactionType": transactionType,
		"UserName":m_user,
		"UserPassword":m_pass,
		"MID":mid,
		"DeviceID":did
		//"amountInformation": amountInformation
	}
	

	var posParameters = [];

	/*Command*/
	posParameters.push( mStx.hex );
	posParameters.push( CreditInfo.command );
	
	/*Version*/
	posParameters.push( mFS.hex );
	posParameters.push( CreditInfo.version );
	
	/*Transaction Type Details*/
	posParameters.push( mFS.hex );
	if( transactionType != '' )
	{
		posParameters.push( CreditInfo.transactionType );
	}
	
	/*UserName*/
	posParameters.push( mFS.hex );
	posParameters.push( 'UserName' );
	posParameters.push( mFS.hex );
	posParameters.push( CreditInfo.UserName );
	
	/*UserPassword*/
	posParameters.push( mFS.hex );
	posParameters.push( 'UserPassword' );
	posParameters.push( mFS.hex );
	posParameters.push( CreditInfo.UserPassword );
	
	/*MID*/
	posParameters.push( mFS.hex );
	posParameters.push( 'MID' );
	posParameters.push( mFS.hex );
	posParameters.push( CreditInfo.MID );
	
	/*DeviceID*/
	posParameters.push( mFS.hex );
	posParameters.push( 'DeviceID' );
	posParameters.push( mFS.hex );
	posParameters.push( CreditInfo.DeviceID );
	
	posParameters.push( mFS.hex );
	posParameters.push( mFS.hex );
	/*Closing Character*/
	posParameters.push( mEtx.hex );
	
	/*Chart Code for the Length*/
	var lrc = getLRC(posParameters);
	
	/*Prepare for base64 encoding.*/
	var command_hex = base64ToHex($.base64.btoa(CreditInfo.command));
	var version_hex = base64ToHex($.base64.btoa(CreditInfo.version));
	var transactionType_hex = base64ToHex($.base64.btoa(CreditInfo.transactionType));
	var UserName_hex = base64ToHex($.base64.btoa(CreditInfo.UserName));
	var UserPassword_hex = base64ToHex($.base64.btoa(CreditInfo.UserPassword));
	var MID_hex = base64ToHex($.base64.btoa(CreditInfo.MID));
	var DeviceID_hex = base64ToHex($.base64.btoa(CreditInfo.DeviceID));
	
	/*
	 *************************************
	 * Prepare element for base64 encoding
	 *************************************
	*/
	var elements = [];

	/*Starting Char*/
	elements.push( mStx.code );
	elements.push( command_hex );

	elements.push( mFS.code );
	elements.push( version_hex );

	elements.push( mFS.code );
	if( transactionType_hex != '' )
	{
		elements.push( transactionType_hex );
	}
	
	elements.push( mFS.code );
	if( UserName_hex != '' )
	{
		//elements.push( 'UserName' );
		elements.push( base64ToHex($.base64.btoa('UserName')) );
		elements.push( mFS.code );
		elements.push( UserName_hex );
		//elements.push( CreditInfo.UserName );
	}
	
	elements.push( mFS.code );
	if( UserPassword_hex != '' )
	{
		//elements.push( 'UserPassword' );
		elements.push( base64ToHex($.base64.btoa('UserPassword')) );
		elements.push( mFS.code );
		elements.push( UserPassword_hex );
		//elements.push( CreditInfo.UserPassword );
	}
	elements.push( mFS.code );
	if( MID_hex != '' )
	{
		//elements.push( 'MID' );
		elements.push( base64ToHex($.base64.btoa('MID')) );
		elements.push( mFS.code );
		elements.push( MID_hex );
		//elements.push( CreditInfo.MID );
	}
	elements.push( mFS.code );
	if( DeviceID_hex != '' )
	{
		//elements.push( 'DeviceID' );
		elements.push( base64ToHex($.base64.btoa('DeviceID')) );
		elements.push( mFS.code );
		elements.push( DeviceID_hex );
		//elements.push( CreditInfo.DeviceID );
	}
	elements.push( mFS.code );
	elements.push( mFS.code );
	
	/*
	 *******************
	 *LRC base64 encoded
	 *******************
	*/
	elements.push( mEtx.code );
	elements.push( base64ToHex($.base64.btoa(lrc)) );
	
	var final_string = elements.join(" ");
	var final_b64 = hexToBase64(final_string);

	/*URL Should be dynamic - based on admin settings*/
    if(posUrl){
        posUrl = posUrl + '?' + final_b64;
    }
		
	$.ajax({
		url : posUrl,
		method : 'GET',
		complete: function( data, status ){
			var response = data.responseText;
			
			var checkParams = StringToHex(response).split(" ").pop();
			var RedundancyCheck = StringToHex(response).split(" ").pop().substring(1);


			var check = getLRC(checkParams);

			if(check == RedundancyCheck){
				//get package detail info
				var packetInfo = [];
				var len = StringToHex(response).indexOf("03");
				var hex = StringToHex(response).slice(0,len).split(/02|1c/);

				var subHex = [];
				var subPacketInfo = [];

				for( var i=0; i<hex.length; i++ )
				{
					if(hex[i] != "")
					{
						if(hex[i].indexOf("1f")>0)
						{
							subHex = hex[i].split("1f");
							subPacketInfo = [];

							for(var j=0; j<subHex.length; j++)
							{
								if(subHex[j]!='')
								{
									subPacketInfo.push(HexToString(subHex[j]));
								}
							}
							
							packetInfo.push(subPacketInfo);
						}
						else
						{
							packetInfo[i] = HexToString(hex[i]);
						}
					}
				}
				var s = structureCreditResponse(packetInfo);
       	
				if(s.ResponseMessage == 'OK' && s.ResponseCode == '000000' ) {
					top.fAlert(s.ResponseMessage + ' : ' + 'Machine variable successfully set.'  );
				}
				else {
					top.fAlert('Error: ' + s.ResponseCode + ' - ' + s.ResponseMessage + ' : ' + 'Machine variable not set.'  );
				}
				top.show_loading_image('hide');
				$(_this).prop('disabled',false);
      }
		}
	});
}

function generateOrderNumber(elem) {
    var tsys_patient_id=pos_patient_id;
    var tsys_trans_mode = $('#moto_trans_mode option:selected').val();
    var referenceNumber = $('#log_referenceNumber').val();
    if(typeof(tsys_trans_mode!='undefined') && tsys_trans_mode!='') {
        var postData={tsys_patient_id:tsys_patient_id,tsys_trans_mode:tsys_trans_mode,referenceNumber:referenceNumber};
        $.ajax({
            type: "POST",
            url:top.JS_WEB_ROOT_PATH+'/interface/accounting/pos/pos_handler.php?method=ordNum',
            dataType:'JSON',
            async: false,
            data:postData,
            success: function(r){
                if(r){
                    $('#tsys_OrderNumber').val(r.orderNumber);
                }
            }
        });
    }
}


/*FUNCTION FOR PAYMENT USING TRANSIT API STARTS HERE */

function pos_api_payment(totalAmt,referenceNumber,tsysOrderNumber,apireturn) {
    apireturn=apireturn?apireturn:'';
    var transactionNumber=($.inArray(transactionNumber, tsysOrderNumber) && tsysOrderNumber.transactionNumber!='')?tsysOrderNumber.transactionNumber:'';
    var MotoMode=($.inArray(MotoMode, tsysOrderNumber) && tsysOrderNumber.MotoMode!='')?tsysOrderNumber.MotoMode:'';
    var cardHolderName=$('#pos_cardHolderName').val();
    //Recurring and Installment transactions.
    var isRecurring='';if(MotoMode=='I'){isRecurring='T';} if(MotoMode=='R') {isRecurring='R';}
    var pos_pay_count='';if($('#pos_pay_count').length>0 && $('#pos_pay_count').val()!='') {pos_pay_count=$('#pos_pay_count').val();}
    var pos_curr_pay_count='';if($('#pos_curr_pay_count').length>0 && $('#pos_curr_pay_count').val()!='') {pos_curr_pay_count=$('#pos_curr_pay_count').val();}
    var cardNumber=$('#pos_cardNumber').val();
    var expirationDate=$('#pos_expirationDate').val();
    var creditCardtype=$('#pos_creditCardtype').val();
    var laneId=$('#laneId').val();
    var pos_cvv2=$('#pos_cvv2').val();
    var tsys_device_id=$('#tsys_device_url option:selected').val();
    var hsa_fsa=$('#pos_card_type option:selected').val();
    var tsys_token='';
    if($('#tsys_token').length>0 && $('#tsys_token option:selected').val()!=''){
        tsys_token=$('#tsys_token option:selected').val();
        expirationDate=btoa( $('#tsys_token option:selected').data('expdate') );
        creditCardtype=$('#tsys_token option:selected').data('cardtype');
    }
    //check for card information for API MOTO payment are filled or not. 
    if(tsys_token=='') {
        if( (cardHolderName=='' || cardNumber=='' || expirationDate=='' || creditCardtype=='') && apireturn!='void' && transactionNumber=='' && MotoMode!='' && isRecurring!='') {
            show_cc_loading_image('hide'); top.fAlert('Please change the MOTO mode OR Fill Card details in Popup.'); return false;
        }
    }
    if(isRecurring!='' && isRecurring=='T' && (pos_pay_count=='' || pos_curr_pay_count=='') ) { 
        show_cc_loading_image('hide'); top.fAlert('Please enter Payment count and Current Payment count'); return false;
    }
    scheduID=(typeof(scheduID)!='undefined' && scheduID)?scheduID:( (tsysOrderNumber.scheduID)?tsysOrderNumber.scheduID:'' );
    encounter_id=(typeof(encounter_id)!='undefined' && encounter_id)?encounter_id:( (tsysOrderNumber.encounter_id)?tsysOrderNumber.encounter_id:'' );
    laneId=(typeof(laneId)!='undefined' && laneId!='')?laneId:( (tsysOrderNumber.laneID)?tsysOrderNumber.laneID:'' );
    var postData={totalAmt:totalAmt,laneId:laneId,scheduID:scheduID,encounter_id:encounter_id,referenceNumber:referenceNumber,cardHolderName:cardHolderName
                    ,cardNumber:cardNumber,expirationDate:expirationDate,creditCardtype:creditCardtype,tsysOrderNumber:tsysOrderNumber,tsys_device_id:tsys_device_id
                ,apireturn:apireturn,isRecurring:isRecurring,pos_pay_count:pos_pay_count,pos_curr_pay_count:pos_curr_pay_count,pos_cvv2:pos_cvv2,hsa_fsa:hsa_fsa,tsys_token:tsys_token};
                         
    $.ajax({
        type: "POST",
        url:top.JS_WEB_ROOT_PATH+'/interface/accounting/pos/pos_api_handler.php?method=validate_card',
        dataType:'JSON',
        data:postData,
        success: function(r){
            show_cc_loading_image('hide');

            if(typeof($('#pos_counter').val())!='undefined') {
                var i=$('#pos_counter').val();
                if(i<=transNumberArr.length){
                    i++;
                    $('#pos_counter').val(i);
                    func_recursive();
                    if(i<transNumberArr.length){ return false; }
                }
            }

            if(r.partialTransaction && r.partialTransaction=='partial') {
                top.fAlert(r.message+'<br>Transaction ID: '+r.TransactionNumber + '<br>Partial transaction Voided.');
                return false;
            }
            
            if(typeof($('#pos_counter').val())!='undefined' && typeof(transNumberArr)!='undefined' && i==transNumberArr.length) {
                top.fAlert(r.message+'<br>Transaction ID: '+r.TransactionNumber + '');
            } else if(typeof($('#pos_counter').val())=='undefined'){
                top.fAlert(r.message+'<br>Transaction ID: '+r.TransactionNumber + '');
            }
            if(r.TransactionNumber=='' || r.TransactionNumber=='undefined') {
                return false;
            }
            if(r.TransactionNumber!='') {
                $('#tsys_transaction_id').val(r.TransactionNumber);
                $('#log_referenceNumber').val(referenceNumber);
                $('#card_details_str_id').val(r.card_details_str);
                
                if(tsysOrderNumber.hasOwnProperty('acc_sec') ) {
                    pos_submit_frm();
                } else {
                    top.pos_submit_frm();
                }
            }
            if(r.ResponseMessage) {
                top.fAlert(r.ResponseMessage);
                return false;
            }
        }
    });
}

/*FUNCTION FOR PAYMENT USING TRANSIT API ENDS HERE */


/*Start FUNCTION to save request transaction log when device is requested for payment. */
function savePosTransactionLog(amount, laneId, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber, voidid, partialStatus, OrderNumberArr, final_b64, posUrl) {
    var tsys_patient_id=pos_patient_id;
    var tsys_device_id=$('#tsys_device_url option:selected').val();

    var postData={totalAmt:amount,laneId:laneId,scheduID:scheduID,encounter_id:encounter_id,transactionType:transactionType,transactionNumber:transactionNumber
                ,referenceNumber:referenceNumber,tsysOrderNumber:OrderNumberArr,tsys_device_id:tsys_device_id,posUrl:posUrl,final_b64:final_b64};

    $.ajax({
        type: "POST",
        url:top.JS_WEB_ROOT_PATH+'/interface/accounting/pos/pos_handler.php?method=save_pos_log',
        dataType:'JSON',
        async: false,
        data:postData,
        success: function(r){}
    });
}
/*End FUNCTION to save request transaction log when device is requested for payment. */