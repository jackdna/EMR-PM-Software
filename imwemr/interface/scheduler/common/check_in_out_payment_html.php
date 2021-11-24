<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
		<!--<link href="css_accounting.php" type="text/css" rel="stylesheet">-->
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/core.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/accounting.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css" type="text/css" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
		  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
		<![endif]-->
		<!--<script type="text/javascript" src="js_accounting.php"></script>-->
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery-ui.min.1.11.2.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-typeahead.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.datetimepicker.full.min.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/core_main.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/sc_script.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
		<script type="text/javascript">
			var pat_js='<?php echo $_SESSION["patient"]; ?>';
			var pos_device='<?php echo $pos_device; ?>';
            var pos_patient_id=pat_js;
			var claim_status_request_js='<?php echo constant('CLAIM_STATUS_REQUEST'); ?>';
			var server_name='<?php echo strtolower($billing_global_server_name); ?>';
			var medicare_imp='<?php echo implode(',',$arr_Medicare_payers); ?>';
			var sessionPatID = "<?php echo $_SESSION['patient']; ?>";
			var source = "<?php echo $_REQUEST['source']; ?>";
			<?php echo $closeDemoScript ?>
			var ssn_format = "<?php echo inter_ssn_format(); ?>";
			var ssn_length = "<?php echo inter_ssn_length(); ?>";
			var ssn_reg_exp_js = "<?php echo inter_ssn_reg_exp_js(); ?>";
			
			var phone_length = "<?php echo inter_phone_length(); ?>";
			var phone_min_length = "<?php echo inter_phone_length(); ?>";
			var phone_format = "<?php echo inter_phone_format(); ?>";
			var state_length = "<?php echo inter_state_length(); ?>";
			var state_label = "<?php echo inter_state_label(); ?>";
			var zip_length = "<?php echo inter_zip_length(); ?>";
			var int_country = "<?php echo inter_country(); ?>";
			var int_county = "<?php echo $patientQryRes[0]['county']; ?>";
			var zip_ext = "<?php echo inter_zip_ext(); ?>";
			var stop_zipcode_validation = "<?php echo constant("STOP_ZIPCODE_VALIDATION") ?>";
			var mandatory_arr_js='<?php echo json_encode($mandatory_flds); ?>';
			var mandatory_field_arr = $.parseJSON(mandatory_arr_js);
		</script>
	<style>
		.adminbox{ padding:10px;  margin-bottom:10px;}
		.adminbox .headinghd{ border-bottom:2px solid #ff6b6b; padding:0px; margin:0px 0px 10px 0px; display:inline-block; float:left; width:100%  }
		.adminbox .headinghd h4{ text-transform:uppercase; font-size:16px; font-family: 'robotobold'; }
		.adminbox h3{ font-size:14px; color:#6c6c6c; margin:0px 0px 10px 0px; padding:0px; text-transform:uppercase; font-weight:bold }
		.adminbox .input-group-addon {padding: 0px 7px !important;}
		.adminbox .tblBg { clear:both }
		.input-group-btn select {
			border-color: #ccc;
			margin-top: 0px;
			margin-bottom: 0px;
			padding-top: 7px;
			padding-bottom: 7px;
		}
		.thumbnail{padding:0px}	
	</style>
        <script type="text/javascript">
			function fill_amt(obj,id,item_name){
				var item_charges = "item_charges_"+id;
				var item_pay = "item_pay_"+id;
				var total_amt = '';
				
				if(obj.checked == true){
					if(item_name=='Copay-visit'){
						if(document.getElementById('copay_dilated')){
							if(document.getElementById('copay_dilated')){
								document.getElementById('copay_dilated').checked=true;
							}
							if(document.getElementById('copay_dilated_tb')){
								total_amt = document.getElementById('copay_dilated_tb').value;
								document.getElementById(item_charges).value="<?php echo $default_currency; ?>"+document.getElementById('copay_dilated_tb').value;
							}
						}else{
							if(document.getElementById('copay_dilated_tb')){
								total_amt = document.getElementById('copay_dilated_tb').value;
								document.getElementById(item_charges).value="<?php echo $default_currency; ?>"+document.getElementById('copay_dilated_tb').value;
							}
						}
					}
					if(item_name=='Copay'){
						if(document.getElementById('copay_dilated')){
							if(document.getElementById('copay_dilated')){
								document.getElementById('copay_dilated').checked=true;
							}
							if(document.getElementById('copay_dilated_tb')){
								total_amt = document.getElementById('copay_dilated_tb').value;
								document.getElementById(item_charges).value="<?php echo $default_currency; ?>"+document.getElementById('copay_dilated_tb').value;
							}
						}else{
							if(document.getElementById('copay_dilated_tb')){
								total_amt = document.getElementById('copay_dilated_tb').value;
								document.getElementById(item_charges).value="<?php echo $default_currency; ?>"+document.getElementById('copay_dilated_tb').value;
							}
						}
					}
					if(item_name=='Copay-test'){
						if(document.getElementById('copay_test_dilated')){
							if(document.getElementById('chk_sel_dilated_id').value=='1'){
								document.getElementById('copay_test_dilated').checked=true;
								if(document.getElementById('copay_test_dilated_tb')){
									total_amt = document.getElementById('copay_test_dilated_tb').value;
									document.getElementById(item_charges).value="<?php echo $default_currency; ?>"+document.getElementById('copay_test_dilated_tb').value;
								}
							}else{
								if(document.getElementById('copay_test_non_dilated')){
									document.getElementById('copay_test_non_dilated').checked=true;
									if(document.getElementById('copay_test_non_dilated_tb')){
										total_amt = document.getElementById('copay_test_non_dilated_tb').value;
										document.getElementById(item_charges).value="<?php echo $default_currency; ?>"+document.getElementById('copay_test_non_dilated_tb').value;
									}
								}else{
									if(document.getElementById('copay_test_non_dilated_tb')){
										total_amt = document.getElementById('copay_test_non_dilated_tb').value;
										document.getElementById(item_charges).value="<?php echo $default_currency; ?>"+document.getElementById('copay_test_non_dilated_tb').value;
									}
								}
							}
						}else{
							if(document.getElementById('copay_test_non_dilated_tb')){
								total_amt = document.getElementById('copay_test_non_dilated_tb').value;
								document.getElementById(item_charges).value="<?php echo $default_currency; ?>"+document.getElementById('copay_test_non_dilated_tb').value;
							}
						}
					}else{
						var chr_amt  = trim(document.getElementById(item_charges).value);
						if(chr_amt != '-'){
							total_amt = chr_amt.substr(1,chr_amt.length);
						}
					}
				}else{
					if(item_name=='Copay-visit'){
						if(document.getElementById('copay_dilated')){
							document.getElementById('copay_dilated').checked=false;
							if(document.getElementById('copay_non_dilated')){
								document.getElementById('copay_non_dilated').checked=false;
							}
							document.getElementById(item_charges).value=0;
						}
					}
					if(item_name=='Copay'){
						if(document.getElementById('copay_dilated')){
							document.getElementById('copay_dilated').checked=false;
							if(document.getElementById('copay_non_dilated')){
								document.getElementById('copay_non_dilated').checked=false;
							}
							document.getElementById(item_charges).value=0;
						}
					}
					if(item_name=='Copay-test'){
						if(document.getElementById('copay_test_dilated')){
							document.getElementById('copay_test_dilated').checked=false;
							if(document.getElementById('copay_test_non_dilated')){
								document.getElementById('copay_test_non_dilated').checked=false;
							}
							document.getElementById(item_charges).value=0;
						}
					}
				}
				if(total_amt == ''){
					total_amt = '0.00';
				}
				document.getElementById(item_pay).value = total_amt;
				copay_nr_chk();
			}
			
			function copay_nr_chk(){
				if(document.getElementById('copay_nr')){
					if(document.getElementById('copay_nr').checked == true){
						document.getElementById('item_pay_1').value = '0.00';
						document.getElementById('item_pay_1').disabled = true;
					}else if(document.getElementById('copay_nr').checked == false){
						document.getElementById('item_pay_1').disabled = false;
					}
				}
			}
			
			function removeCommas(val){
				if(val.indexOf(",") != -1){
					do{
						var val = val.replace(",", "");
					}while(val.indexOf(",") != -1)
				}
				if(val.indexOf("$") != -1){
					do{
						var val = val.replace("$", "");
					}while(val.indexOf("$") != -1)
				}
				return val;
			}
			
			function tot_payment(){
				var tdVal = 0;
				var tot_ci_pay=document.getElementById('tot_CI_payment').value;
				for(i=1;i<13;i++){
					var chkbox = "chkbox_"+i;
					var item_pay = "item_pay_"+i;
					if(document.getElementById(chkbox)){
						if(document.getElementById(chkbox).checked == true){
							document.getElementById(item_pay).value = removeCommas(document.getElementById(item_pay).value);
							if(document.getElementById(item_pay).value > 0){
								tdVal = parseFloat(tdVal) + parseFloat(document.getElementById(item_pay).value);
							}
						}
					}	
				}
				var tot_final_amt=parseFloat(tot_ci_pay)+parseFloat(tdVal);
				document.getElementById('tot_co_payment_only').innerHTML = "<?php echo $default_currency; ?>"+tdVal.toFixed(2);
				document.getElementById('tot_payment').innerHTML = "<?php echo $default_currency; ?>"+tot_final_amt.toFixed(2);
				document.getElementById('tot_payment_txt').value = tot_final_amt.toFixed(2);
				tot_charges_fun();
			}
			function  tot_charges_fun(){
				var tdVal_charges=0;
				for(i=1;i<10;i++){
					var chkbox = "chkbox_"+i;
					var item_charges_chk = "item_charges_"+i;
					if(document.getElementById(chkbox)){
						if(document.getElementById(chkbox).checked == true){
							var chr_amt  = trim(document.getElementById(item_charges_chk).value);
							var chrg_val=chr_amt.substr(1,chr_amt.length);
							if(chrg_val > 0){
								tdVal_charges = parseFloat(tdVal_charges) + parseFloat(chrg_val);
							}
						}
					}	
				}
				if(document.getElementById('tot_charges'))
				{
					document.getElementById('tot_charges').innerHTML = "<?php echo $default_currency; ?>"+tdVal_charges.toFixed(2);
				}
			}
			function sel_chkbox(obj,id){
				var chkbox="chkbox_"+id;
				var item_pay_name = "item_pay_"+id;
				var amt = document.getElementById(item_pay_name).value;
				amt = amt.replace(",","");
				amt = amt.replace("<?php echo $default_currency; ?>","");
				if(amt > 0){
					document.getElementById(chkbox).checked = true;
				}
			}
			
			function showRow(obj){
				var show = obj.value;
				var checkType = false; var cardType = false; 
				for(i=1;i<=12;i++){
					objAll = dgi('pay_method_'+i);
					objPay = dgi('item_pay_'+i);
					if(objAll && objAll.name != obj.name && (objAll.value=='Check' || objAll.value=='EFT' || objAll.value=='Money Order')){
						checkType = true;
					}else if(objAll && objAll.name != obj.name && objAll.value=='Credit Card'){
						cardType = true;
					}
				}
				switch(show){
					case "Cash":
						if(!checkType)
							$("#checkRow").hide();
						if(!cardType)
							$("#creditCardRow").hide();
					break;
					case "Check":
						document.getElementById("checkRow").style.display = "inline-table";
						if(!cardType)
							$("#creditCardRow").hide();
					break;
					case "EFT":
						document.getElementById("checkRow").style.display = "inline-table";
						if(!cardType)
							$("#creditCardRow").hide();
					break;
					case "Money Order":
						document.getElementById("checkRow").style.display = "inline-table";
						if(!cardType)
							$("#creditCardRow").hide();
					break;
					case "Credit Card":
						document.getElementById("creditCardRow").style.display = "inline-table";
						if(!checkType)
							$("#checkRow").hide();
					break;
				}
			}
			
			function print_receipt_check(){
				var edit_id = document.getElementById("edit_payment_tbl_id").value;
				var patient_id = document.getElementById("patient_id").value;
				var sch_id = document.getElementById("sch_id").value;
				window.open("payment_receipt.php?id="+sch_id+"&pid="+patient_id,'print_receipt','width=800,height=550,top=10,left=40,scrollbars=yes,resizable=yes');
			}
			
			function print_pt_summary_check(){
				var sch_id = document.getElementById("sch_id").value;
				var patient_id = document.getElementById("patient_id").value;
				window.open("pt_summary_print.php?pid="+patient_id+"&sch_id="+sch_id,'Print_pt_summary','width=800,height=550,top=10,left=40,scrollbars=yes,resizable=yes');
			}
			
			function checkValidPayMethod(cc_card){
                var no_pos_device=false;
                if($('#tsys_device_url').val()=='no_pos_device') {
                    no_pos_device=true;
                }
				var CCselected = CHQselected = EFTselected = MOselected = false;
				for(i=1;i<13;i++){
					chkbox = "chkbox_"+i;
					item_pay = "item_pay_"+i;
					pay_method_dd = "pay_method_"+i;
					if($('#'+chkbox).prop('checked')==true){
						item_amt  = parseFloat($('#'+item_pay).val());
						pay_dd_val = $('#'+pay_method_dd).val();//alert(item_amt+' :: '+pay_dd_val);return false;
						if(item_amt > 0 && pay_dd_val==''){
							alert("Payment method not selected.");//, actionToPerform, width, height_adjustment);
							$('#'+pay_method_dd).focus();
							return false;
						}else{
							if((pay_dd_val=='Check' || pay_dd_val=='EFT' || pay_dd_val=='Money Order') && CHQselected==false){
								CHQselected = true;
							}else if(pay_dd_val=='Credit Card' && CCselected==false){
								CCselected = true;
							}
						}
					}
				}
				chqNO	= $('#checkNo').val();
				ccCOMP	= $('#creditCardCo').val();
				ccNO	= $('#cCNo').val();
				ccEXP	= $('#date2').val();
				if(CHQselected && chqNO==''){
					alert("Please enter Check/EFT/MO number.");//, actionToPerform, width, height_adjustment);
					$('#checkNo').focus();
					return false;
				}
                if(cc_card && (!pos_device || no_pos_device==true) ){
                    if(CCselected && ccCOMP==''){
                        alert("Please select credit card type.");//, actionToPerform, width, height_adjustment);
                        $('#creditCardCo').focus();
                        return false;
                    }
                    if(CCselected && ccNO==''){
                        alert("Please enter credit card number.");//, actionToPerform, width, height_adjustment);
                        $('#cCNo').focus();
                        return false;
                    }
                    if(CCselected && ccEXP==''){
                        alert("Please enter credit card expiry date.");//, actionToPerform, width, height_adjustment);
                        $('#date2').focus();
                        return false;
                    }
                }
				return true;
			}				
			
			function print_save_fun(){
                var no_pos_device=false;
                if($('#tsys_device_url').val()=='no_pos_device') {
                    no_pos_device=true;
                }
				if(!chk_negative()){return false;}
                
                var cc_card=cc_card_check();
                if(!checkValidPayMethod(cc_card)){return false;}
                if(typeof(make_cccard_payment)!='undefined' && pos_device && cc_card && no_pos_device==false){
                    make_cccard_payment();
                } else {
                    dgi("btn_submit_print").value = 'yes';
                    pos_submit_frm();
                }
			}
			function save_but_disable(){
                var no_pos_device=false;
                if($('#tsys_device_url').val()=='no_pos_device') {
                    no_pos_device=true;
                }
				if(!chk_negative()){return false;}
                
                var cc_card=cc_card_check();
                if(!checkValidPayMethod(cc_card)){return false;}
                if(typeof(make_cccard_payment)!='undefined' && pos_device && cc_card && no_pos_device==false){
                    make_cccard_payment();
                } else {
                    pos_submit_frm();
                }
			}
            
            function pos_submit_frm() {
                dgi("btn_submit").click();
				if(document.getElementById('submit_btn')){
					document.getElementById('submit_btn').disabled=true;
				}
				if(document.getElementById('btn_print')){
					document.getElementById('btn_print').disabled=true;
				}
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

                return cc_card;
            }
            
            function set_pay_method(j) {
                if(pos_device) {
                    $('#pay_method_'+j).find('option[value="Credit Card"]').prop("selected", "selected");
                    $('#pay_method_'+j).selectpicker('refresh');
                    showRow($('#pay_method_'+j)[0]);
                }
            }
			
			$(document).ready( function() {
				//$("#btn_submit").click(function(){
				//});
				//$('#main_form').css({'height':$(window).innerHeight()-40});
			});
			function set_copay(obj,id,item_name){
				var obj_name=obj.name;
				var chkbox = "chkbox_"+id;
				var item_pay_chk = "item_pay_"+id;
				var item_charges_chk = "item_charges_"+id;
				var cd_name='copay_dilated_'+id;
				var cnd_name='copay_non_dilated'+id;
				
				if(obj_name==cd_name){
					if(document.getElementById('copay_dilated').checked==true){
						document.getElementById(item_pay_chk).value=document.getElementById('copay_dilated_tb').value;
						document.getElementById(item_charges_chk).value="<?php echo $default_currency; ?>"+document.getElementById('copay_dilated_tb').value;
						document.getElementById('copay_non_dilated').checked=false;
						document.getElementById(chkbox).checked=true;
					}else{
						document.getElementById(item_pay_chk).value=document.getElementById('copay_non_dilated_tb').value;
						document.getElementById(item_charges_chk).value="<?php echo $default_currency; ?>"+document.getElementById('copay_non_dilated_tb').value;
						document.getElementById('copay_dilated').checked=false;
						document.getElementById('copay_non_dilated').checked=true;
						document.getElementById(chkbox).checked=true;
					}
				}else{
					if(document.getElementById('copay_non_dilated').checked==true){
						document.getElementById(item_pay_chk).value=document.getElementById('copay_non_dilated_tb').value;
						document.getElementById(item_charges_chk).value="<?php echo $default_currency; ?>"+document.getElementById('copay_non_dilated_tb').value;
						document.getElementById('copay_dilated').checked=false;
						document.getElementById(chkbox).checked=true;
					}else{
						document.getElementById(item_pay_chk).value=document.getElementById('copay_dilated_tb').value;
						document.getElementById(item_charges_chk).value="<?php echo $default_currency; ?>"+document.getElementById('copay_dilated_tb').value;
						document.getElementById('copay_non_dilated').checked=false;
						document.getElementById('copay_dilated').checked=true;
						document.getElementById(chkbox).checked=true;
					}
				}
			}
			function set_test_copay(obj,id,item_name){
				var obj_name=obj.name;
				var chkbox = "chkbox_"+id;
				var item_pay_chk = "item_pay_"+id;
				var item_charges_chk = "item_charges_"+id;
				var cd_name='copay_test_dilated_'+id;
				var cnd_name='copay_test_non_dilated_'+id;
				
				if(obj_name==cd_name){
					if(document.getElementById('copay_test_dilated').checked==true){
						document.getElementById(item_pay_chk).value=document.getElementById('copay_test_dilated_tb').value;
						document.getElementById(item_charges_chk).value="<?php echo $default_currency; ?>"+document.getElementById('copay_test_dilated_tb').value;
						document.getElementById('copay_test_non_dilated').checked=false;
						document.getElementById(chkbox).checked=true;
					}else{
						document.getElementById(item_pay_chk).value=document.getElementById('copay_test_non_dilated_tb').value;
						document.getElementById(item_charges_chk).value="<?php echo $default_currency; ?>"+document.getElementById('copay_test_non_dilated_tb').value;
						document.getElementById('copay_test_dilated').checked=false;
						document.getElementById('copay_test_non_dilated').checked=true;
						document.getElementById(chkbox).checked=true;
					}
				}else{
					if(document.getElementById('copay_test_non_dilated').checked==true){
						document.getElementById(item_pay_chk).value=document.getElementById('copay_test_non_dilated_tb').value;
						document.getElementById(item_charges_chk).value="<?php echo $default_currency; ?>"+document.getElementById('copay_test_non_dilated_tb').value;
						document.getElementById('copay_test_dilated').checked=false;
						document.getElementById(chkbox).checked=true;
					}else{
						document.getElementById(item_pay_chk).value=document.getElementById('copay_test_dilated_tb').value;
						document.getElementById(item_charges_chk).value="<?php echo $default_currency; ?>"+document.getElementById('copay_test_dilated_tb').value;
						document.getElementById('copay_test_non_dilated').checked=false;
						document.getElementById('copay_test_dilated').checked=true;
						document.getElementById(chkbox).checked=true;
					}
				}
			}
			function showClSupplyOrderFromFrontDesk2() {
				var SupplyUrl="../../chart_notes/print_order.php?callFrom=clSupply&clws_id=1&dos=";
				window.open(SupplyUrl,"ClSupplyOrderWindow","width='100%',scrollbars=0,height=430,top=2,left=0");
			}
			function superbill_info2(id) {
				var current_caseId_sch_obj = $("#choose_prevcase",window.opener.top.fmain.document);
				var app_id_obj = $("#global_apptid",window.opener.top.fmain.document);
				//console.log(window.opener.top.fmain.document.$('#global_apptid').val());
				var current_caseId_sch = current_caseId_sch_obj.val();
				var app_id = app_id_obj.val();
				$.ajax({
					url: "../get_latest_encounter.php?pid="+id+"&app_id="+app_id,
					success: function(resp){
						if(resp==''){
							fAlert("No SuperBill is associated with this DOS.");
						}else {
							window.open("../../chart_notes/requestHandler.php?elem_formAction=SuperBill_Print&e_id="+resp+"&patientId_sch="+id+"&current_caseId_sch="+current_caseId_sch,'Bill','width=1170,height=630,top=10,left=10,scrollbars=yes,resizable=yes');	
						}
					}
				});
			}
			
			function do_total(){
				cashPay = 0; checkPay = 0; cardPay = 0; eftPay = 0; moPay = 0;
				for(j=1;j<=12;j++){
					objAll = dgi('pay_method_'+j);
					objPay = dgi('item_pay_'+j);
					if(objAll && objAll.value=='Check' && objPay.value != ''){
						checkPay += parseFloat(objPay.value);
					}else if(objAll && objAll.value=='Credit Card' && objPay.value != ''){
						cardPay += parseFloat(objPay.value);
					}else if(objAll && objAll.value=='Cash' && objPay.value != ''){
						cashPay += parseFloat(objPay.value);
					}
					else if(objAll && objAll.value=='EFT' && objPay.value != ''){
						eftPay += parseFloat(objPay.value);
					}
					else if(objAll && objAll.value=='Money Order' && objPay.value != ''){
						moPay += parseFloat(objPay.value);
					}
				}
				dgi('tot_cash_payment').value = cashPay;
				dgi('tot_check_payment').value = checkPay;
				dgi('tot_card_payment').value = cardPay;
				dgi('tot_eft_payment').value = eftPay;
				dgi('tot_mo_payment').value = moPay;
                
                var cc_card=cc_card_check();
				if(!checkValidPayMethod(cc_card)){return false;}
			}
		function show_payment(id){
			var paymentUrl="../acc_charges_details.php?patient_id="+id;
			window.open(paymentUrl,"PaymentWindow","width=1200,height=450,top=100,left=50,resizable=no");
		}
		
		function get271Report(id){
			var h = '<?php echo $_SESSION['wn_height'] - 140; ?>';
			window.open('../../patient_info/eligibility/eligibility_report.php?id='+id,'eligibility_report','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
		}
		
		function show_loading_image(val){
			document.getElementById("loading_img").style.display = val;
		}
		</script>
		<style>
            .text_10b_purpule{
                font-family:Verdana, Arial, Helvetica, sans-serif;
                font-size:12px;
                color:#9900CC;
                font-weight:bold;
                text-align:left;
                padding-left:5px;
            }
			
			.adminbox .headinghd h4{
				font-size:15px
			}
			footer::before{
				content: "";
				position: fixed;
				left: 0;
				width: 100%;
				height: 100%;
				-webkit-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
				-moz-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
				box-shadow: 0px 0px 6px rgba(0,0,0,.8);
				z-index: 100;
			}
        </style>
	</head>
	<?php	
		$patient_vip = '';
		if($superbill_vip == '1'){
			$patient_vip_code = "fAlert('&nbsp;&nbsp;Patient is <b>VIP</b> for that visit','imwemr');";
		}
		$default_currency = show_currency();
	?>
	<body onLoad="tot_charges_fun();<?php echo $patient_vip_code; ?>">
		<form name="check_out_form" id="check_out_form" action="check_in_out_payment.php" method="post" onSubmit="return do_total();">
		<div class="mainwhtbox" id="content_box">
			<div align="center" id="loading_img" width="100%" style="display:none; top:50%; left:50%; z-index:1000; position:absolute;">
				<div class="loader"></div>
			</div>
			<div id="main_form" class="row ">
					<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
					<input type="hidden" name="sch_id" id="sch_id" value="<?php echo $sch_id; ?>">
					<input type="hidden" name="edit_payment_tbl_id" id="edit_payment_tbl_id" value="<?php $main_pay_id; ?>">
					<!-- <input type="hidden" name="edit_payment_tbl_id" id="edit_payment_tbl_id" value="{$edit_payment_tbl_id}"> -->
					<input type="hidden" name="edit_payment_tbl_id_cash" id="edit_payment_tbl_id_cash" value="<?php echo $edit_payment_tbl_id_cash; ?>">
					<input type="hidden" name="edit_payment_tbl_id_check" id="edit_payment_tbl_id_check" value="<?php echo $edit_payment_tbl_id_check; ?>">
					<input type="hidden" name="edit_payment_tbl_id_card" id="edit_payment_tbl_id_card" value="<?php echo $edit_payment_tbl_id_card; ?>">
        
                    <input type="hidden" name="log_referenceNumber" id="log_referenceNumber" value="" />
                    <input type="hidden" name="tsys_transaction_id" id="tsys_transaction_id" value="" />
                    <input type="hidden" name="tsys_void_id" id="tsys_void_id" value="" />
                    <input type="hidden" name="tsys_last_status" id="tsys_last_status" value="" />
                    <input type="hidden" name="card_details_str_id" id="card_details_str_id" value="" />
        
					<input type="hidden" name="edit_payment_tbl_id_eft" id="edit_payment_tbl_id_eft" value="<?php echo $edit_payment_tbl_id_eft; ?>">
					<input type="hidden" name="edit_payment_tbl_id_mo" id="edit_payment_tbl_id_mo" value="<?php echo $edit_payment_tbl_id_mo; ?>">
					<input type="hidden" name="chk_sel_dilated_id" id="chk_sel_dilated_id" value="<?php echo $chk_sel_dilated; ?>" />
					<input type="hidden" name="tot_cash_payment" id="tot_cash_payment" value="">
					<input type="hidden" name="tot_check_payment" id="tot_check_payment" value="">
					<input type="hidden" name="tot_card_payment" id="tot_card_payment" value="">
					<input type="hidden" name="tot_eft_payment" id="tot_eft_payment" value="">
					<input type="hidden" name="tot_mo_payment" id="tot_mo_payment" value="">
                    <input type="hidden" name="vip_alert_post" id="vip_alert_post" value="<?php echo $superbill_vip; ?>">
					<!-- Main Page Heading -->
					<div class="col-sm-12">
						<div class="row purple_bar">
							
								<div class="col-xs-5">
									<div class="row">
										<div class="col-sm-5">
											<span class="glyphicon glyphicon-user"></span>
											<span> <?php echo $top_co_header; ?> </span>	
										</div>
										
										<div class="col-sm-7">
											<div class="row">
												<div class="col-sm-2">
													<span><?php echo $imgRealTimeEli; ?></span>		
												</div>	
												<div class="col-sm-10">
													<?php 
														$vsStatusColor = '';
														if($vsTran == "sucss"  && $rowGetRealTimeData->vsRespDate == date('m-d-Y')){
															$vsStatusColor = "#90";
														}else if($vsTran == "error"){
															$vsStatusColor = "#F00";
														}else{
															$vsStatusColor = "#63C";
														} ?>
													<span title="<?php echo $vsToolTip; ?>" style="color:<?php echo $vsStatusColor; ?>" onClick="get271Report('<?php echo $rtme_id; ?>');"><?php echo $vsStatus; ?></span>	
												</div>	
											</div>
										</div>
								
									</div>	
								</div>
							
								
							
							
							
							
							
							
							<div class="col-sm-4">
								<div class="row">
									<div class="col-sm-6">
										<span><?php echo $patient_name; ?></span>	
									</div>
									<div class="col-sm-6">
										<span><?php echo $top_ci_header; ?></span>	
									</div>	
								</div>	
							</div>

							<div class="col-sm-3">
								<button class="btn btn-success" type="button" onClick="superbill_info2('<?php echo $patient_id; ?>');">Super Bill</button>
								<button class="btn btn-success" type="button" onClick="showClSupplyOrderFromFrontDesk2();">CL-Sply</button>
								<button class="btn btn-success" type="button" onClick="show_payment('<?php echo $patient_id; ?>');">Accounts Details</button>	
							</div>	
						</div>
					</div>	
					<?php if(constant('ENABLE_REAL_ELIGILIBILITY') == 'YES' && $date_remain == 'YES'){ ?>
						<!-- Real Eligibility Block -->
						<div class="col-sm-12 pt10">
							<div class="row ">
								<div class="col-sm-4">
									<div class="headinghd">
										<h4>Real-time Eligibility Amount Information</h4>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="headinghd">
										<h4>Primary Insurance: <?php echo $primaryInsurnaceName; ?></h4>
									</div>
								</div>	
								<div class="col-sm-4">
									<div class="headinghd tex-nowrap">
										<h4>Secondary Insurance: <?php echo $SecondaryInsurnaceName; ?></h4>
									</div>
								</div>	
							</div>	
							<div class="row">
								<div class="col-sm-4">
									Co-Pay: <?php if($intCopayAmtPre != ''){echo $default_currency.$intCopayAmtPre;}else{echo 'N/A';}?>	
								</div>	
								<div class="col-sm-4" style="<?php if($RTE_OUT_DAYS_STATUS_COLOR != ''){echo 'color:'.$RTE_OUT_DAYS_STATUS_COLOR.'';} ?>">
									Deductible: <?php if($intDDCAmtPre != ''){echo $default_currency.$intDDCAmtPre;}else{echo 'N/A';}?>	
								</div>
								<div class="col-sm-4" style="<?php if($RTE_OUT_DAYS_STATUS_COLOR != ''){echo 'color:'.$RTE_OUT_DAYS_STATUS_COLOR.'';} ?>">
									Co-Insurance: <?php if($strCoInsAmtPre != ''){echo $strCoInsAmtPre.'%';}else{echo 'N/A';}?>	
								</div>
							</div>	
						</div>
					<?php } ?>
					<!-- Visit Details -->
					<div class="col-sm-12 pt10">
						<div class="row">
								<?php
									require_once("../superbill_record.php");
								?>	
						</div>
					</div>
					<!-- Today Details -->
					<?php
						if($show_pt_co_ins == 'show'){
							$pt_co_ins="block"; $pt_ins_ins="block";
						}else{
							 $pt_co_ins="none"; $pt_ins_ins="none";
						}
					?>
					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-12 headinghd">
								<h4>Today:	</h4>
							</div>
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-2 div_td">
										<label>&nbsp;Allowable Chrg: <?php echo $default_currency.$total_allowable_charges; ?></label>
									</div>	
									<div class="col-sm-2 div_td">
										<label>Pt Prev. Bal: <?php echo $default_currency.$total_prev_patientDue; ?></label>
									</div>
									<div class="col-sm-2 div_td">
										<label>Copay: <?php echo $default_currency.$total_today_copay; ?></label>
									</div>
									<div class="col-sm-2 div_td">
										<label>Co-Ins: <?php echo $co_ins_pat; ?></label>
									</div>
									<div class="col-sm-2 div_td">
										<label>Max Allowable Ded.: <?php echo $item_deductable_amt; ?></label>
									</div>	
									<div class="col-sm-2 div_td" style="display:<?php echo $pt_co_ins; ?>">
										<label>Pt. Co-Ins: <?php echo $item_co_ins; ?></label>
									</div>
									<div class="col-sm-2 div_td" style="display:<?php echo $pt_ins_ins; ?>">
										<label>Ins. Co-Ins: <?php echo $ins_co_ins; ?></label>
									</div>
									<div class="col-sm-2 div_td">
										<label>Pt Due: <?php echo $pat_total_today_patientDue; ?></label>
									</div>	
										
								</div>	
							</div>	
						</div>	
					</div>

					<!-- Previous Details -->
					<div class="col-sm-12 pt10">
						<div class="row">
							<div class="col-sm-12 headinghd">
								<h4>Previous:</h4>
							</div>	
							<div class="col-sm-12">
								<div class="row">
									<?php 
										foreach($group_name as $key => $val){
											$Previous_Balance = array_sum($Previous_Data[$key]['Patient_Due']);
											?>
											<div class="col-sm-2 div_td">
												<label>Pt Bal(<?php echo $val;  ?>): <?php echo $default_currency.'&nbsp;'.number_format($Previous_Balance ,2)?></label>  
											</div>
											<?php
										}
										
										foreach($group_name as $key => $val){
											$Previous_Balance = array_sum($Previous_Data[$key]['Insurance_Due']);
											?>
											<div class="col-sm-2 div_td">
												<label>Ins Bal(<?php echo $val;  ?>): <?php echo $default_currency.'&nbsp;'.number_format($Previous_Balance,2) ?></label>	   
											</div>
											<?php
										}	
									?> 
								</div>	
							</div>
						</div>	
					</div>	
					
					<!-- Total Details -->
					<div class="col-sm-12 pt10">
						<input type="hidden" name="total_charges_txt" id="total_charges_txt" value="<?php echo $totalCharges; ?>">
						<input type="hidden" name="tot_payment_txt" id="tot_payment_txt" value="<?php echo $total_payment; ?>">
						<input type="hidden" name="tot_CI_payment" id="tot_CI_payment" value="<?php echo $total_ci_payment; ?>">
						<div class="row">
							<div class="col-sm-12 headinghd">
								<h4>Total</h4>	
							</div>
							<div class="col-sm-12">
								<?php
									$balance_color="";
									if($total_credit_chk < 0){
										$balance_color = "color:#5D738E;font-weight: bold;";
									}else if($total_credit_chk > 0){
										$balance_color="color:#F00;";
									}
								?>
								<div class="row">
									<div class="col-sm-2">
										<label>Patient Due: <?php echo $total_pat_due_payment; ?></label>	
									</div>
									<div class="col-sm-2">
										<label>Payment at CI: <?php echo $default_currency.$total_ci_payment; ?></label>	
									</div>	
									<div class="col-sm-2" >
										<label >Payment at CO: <span id="tot_co_payment_only"><?php echo $default_currency.$total_co_payment; ?></span></label>	
									</div>	
									<div class="col-sm-2" >
										<label >Payments: <span id="tot_payment"><?php echo $default_currency.$total_payment; ?></span></label>	
									</div>	
									<div class="col-sm-2">
										<label>Balance: <span id="tot_payment" style="<?php echo $balance_color; ?>"><?php echo $total_credit; ?></span></label>	
									</div>
									<div class="col-sm-2">
										<div class="row">
											<?php 
												if($last_final_rx_id != '' && $final_cl_rx_status == '1'){
													?>
													<div class="col-sm-4">
														<label>
															<a href="javascript:printContactRx('1','<?php echo $cl_rx; ?>',1);" class="text_10b_purpule"><b>CL Rx</b></a>
														</label>	
													</div>
													<?php
												}else{
													echo "<div class='col-sm-4'><label>No CL Rx</label></div>";
												}
												
												if($vis_mr_none_given != ''){
													?>
													<div class="col-sm-4">
														<label>
															<a href="javascript:printMr('<?php echo $gl_rx; ?>',1);"  class="text_10b_purpule"><b>GL Rx</b></a>
														</label>	
													</div>
													<?php
												}else{
													echo "<div class='col-sm-4'><label>No GL Rx</label></div>";
												}
												
												if($pc_rx > 0){
													?>
													<div class="col-sm-4">
														<a href="javascript:print_vision_pc_1(<?php echo $pc_rx; ?>);" class="text_10b_purpule"><b>PC Rx</b></a>
													</div>
													
													<?php
												}else{
													echo "<div class='col-sm-4'><label>No PC Rx</label></div>";
												}
											?> 
										</div>	
									</div>	
								</div>	
							</div>	
						</div>		
					</div>

					<!-- Check Out Payments details -->	
					<div class="col-sm-12 pt10">
						<div class="row">
							<div class="col-sm-12 headinghd">
								<h4 class="pull-left">Check Out Payment</h4>
								<?php if($patient_vip == '1'){ ?>
									<h4 class="pull-left" style="font-size:13px; color:#f00;padding-left:1%">Patient is VIP</h4>	
								<?php } ?>
							</div>	
							<div class="col-sm-12">
								<div class="row">
									<?php 
										$i = 0;
										$tabindex="98";
										$div_width="79";
										$div_heading="170";
										for($check_out=0,$i=1;$check_out<count($check_out_data);$check_out++){
											$chk_main_id = $check_out_data[$check_out]['id'];
											$arr_chk_out = $check_out_data[$check_out]['checkout'];
											$arr_chk_in = $check_out_data[$check_out]['checkin'];
											
											if($check_out_data[$check_out]['item_amt']['dilated'] > 0 && $check_out_data[$check_out]['item_amt']['nondilated'] > 0){
												$div_width="166";
												$div_heading="70";
											}else if($check_out_data[$check_out]['item_amt']['test_dilated'] > 0 && $check_out_data[$check_out]['item_amt']['test_nondilated'] > 0){
												 $div_width="166";
												 $div_heading="70";
											}else{
												$div_width="68";
												$div_heading="168";
											}
									?>
											<div class = "col-sm-4">
												<div class="row pt10">	
													<div class="col-sm-4">
														<input type="hidden" name="chk_payment_detail_arr[<?php echo $chk_main_id; ?>]" value="<?php echo $arr_chk_out['pay_detail_id']; ?>">
														<div class="checkbox checkbox-inline">
															<input <?php echo $visit_payment_readonly;  ?> type="checkbox" tabindex="
															<?php print $tabindex; ?>" style="cursor:pointer;" id="chkbox_<?php echo $chk_main_id; ?>" name="check_in_out_pay[]" <?php echo $arr_chk_out['item_checked']; ?>  value="<?php echo $chk_main_id; ?>" onClick="fill_amt(this,'<?php echo $chk_main_id; ?>','<?php echo $check_out_data[$check_out]['item_name']; ?>'); tot_payment(); tot_charges_fun();">	
															<label for="chkbox_<?php echo $chk_main_id; ?>"><?php echo $check_out_data[$check_out]['item_name']; ?></label>
														</div>
													</div>
													
													<div class="col-sm-3">
														<div class="row">
															<div class="col-sm-6">
																<?php 
															if($check_out_data[$check_out]['item_name'] == 'Copay-visit' || $check_out_data[$check_out]['item_name'] == 'Copay-test' || $check_out_data[$check_out]['item_name'] == 'Copay'){
																if($check_out_data[$check_out]['item_name'] == 'Copay-visit'){
																	if($check_out_data[$check_out]['item_amt']['dilated'] > '0' && $check_out_data[$check_out]['item_amt']['nondilated'] > '0'){
																		echo $default_currency.$check_out_data[$check_out]['item_amt']['dilated'].'/'.	$check_out_data[$check_out]['item_amt']['nondilated'];
																		$checked= '';
																		?>
																		<div class="checkbox checkbox-inline">
																			<input <?php echo $visit_payment_readonly;  ?> type="checkbox" tabindex="<?php print $tabindex; ?>" style="cursor:pointer;" name="copay_dilated_<?php echo $chk_main_id; ?>"  id="copay_dilated_<?php echo $chk_main_id; ?>" title="Copay Dilated" <?php if($arr_chk_out['copay_type'] == '1'){echo 'checked';} ?> onClick="set_copay(this,'<?php echo $chk_main_id; ?>','<?php echo $check_out_data[$check_out]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																			<label for="copay_dilated_<?php echo $chk_main_id; ?>">D</label>	
																		</div>
																		<div class="checkbox checkbox-inline">
																			<input <?php echo $visit_payment_readonly;  ?> type="checkbox" tabindex="<?php print $tabindex; ?>" style="cursor:pointer;" name="copay_non_dilated_<?php echo $chk_main_id; ?>" id="copay_non_dilated_<?php echo $chk_main_id; ?>" title="Copay Non Dilated" <?php if($arr_chk_out['copay_type'] == '2'){echo 'checked';} ?>  onClick="set_copay(this,'<?php echo $chk_main_id; ?>','<?php echo $check_out_data[$check_out]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																			<label for="copay_non_dilated_<?php echo $chk_main_id; ?>">ND</label>	
																		</div>
																		<input type="hidden" id="copay_dilated_tb" name="copay_dilated_tb" value="<?php echo $check_out_data[$check_out]['item_amt']['dilated']; ?>">
																																			<input type="hidden" id="copay_non_dilated_tb" name="copay_non_dilated_tb" value="<?php echo $check_out_data[$check_out]['item_amt']['nondilated']; ?>">
																		<?php	
																	}else if($check_out_data[$check_out]['item_amt']['dilated'] > 0){
																		echo $default_currency.$check_out_data[$check_out]['item_amt']['dilated'];
																		?>
																		<input type="hidden" name="copay_dilated_<?php echo $chk_main_id; ?>" id="copay_dilated" value="1">
																																			<input type="hidden" id="copay_dilated_tb" name="copay_dilated_tb" value="<?php echo $check_out_data[$check_out]['item_amt']['dilated']; ?>">
																																			<input type="hidden" id="copay_non_dilated_tb" name="copay_non_dilated_tb" value="0">	
																		<?php	
																	}else{
																		echo "-";
																	}
																	$copay_chrg = 0;

																	if($arr_chk_out['copay_type'] == '1'){
																		$copay_chrg = $check_out_data[$check_out]['item_amt']['dilated'];
																	}

																	if($arr_chk_out['copay_type'] == '2'){
																		$copay_chrg = $check_out_data[$check_out]['item_amt']['nondilated'];
																	}	
																}
																if($check_out_data[$check_out]['item_name'] == 'Copay'){
																	if($check_out_data[$check_out]['item_amt']['dilated'] > '0' && $check_out_data[$check_out]['item_amt']['nondilated'] > '0'){
																		echo $default_currency.$check_out_data[$check_out]['item_amt']['dilated'].'/'.$check_out_data[$check_out]['item_amt']['nondilated'];
																		?>
																		<div class="checkbox checkbox-inline">
																			<input <?php echo $visit_payment_readonly;  ?> type="checkbox" tabindex="<?php print $tabindex; ?>" style="cursor:pointer;" name="copay_dilated_<?php echo $chk_main_id; ?>"  id="copay_dilated_<?php echo $chk_main_id; ?>" title="Copay Dilated" <?php if($arr_chk_out['copay_type'] == '1'){echo "checked";} ?> onClick="set_copay(this,'<?php echo $chk_main_id; ?>','<?php echo $check_out_data[$check_out]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																			<label for="copay_dilated_<?php echo $chk_main_id; ?>">D</label>	
																		</div>

																		<div class="checkbox checkbox-inline">
																			<input <?php echo $visit_payment_readonly;  ?> type="checkbox" tabindex="<?php print $tabindex; ?>" style="cursor:pointer;" name="copay_non_dilated_<?php echo $chk_main_id; ?>" id="copay_non_dilated" title="Copay Non Dilated" <?php if($arr_chk_out['copay_type'] == '2'){echo 'checked';} ?> onClick="set_copay(this,'<?php echo $chk_main_id; ?>','<?php echo $check_out_data[$check_out]['item_name'];?>'); tot_payment(); tot_charges_fun();">
																			<label for="copay_dilated_<?php echo $chk_main_id; ?>">ND</label>	
																		</div>
																																			 <input type="hidden" id="copay_dilated_tb" name="copay_dilated_tb" value="<?php echo $check_out_data[$check_out]['item_amt']['dilated']; ?>">
																																			 <input type="hidden" id="copay_non_dilated_tb" name="copay_non_dilated_tb" value="<?php echo $check_out_data[$check_out]['item_amt']['nondilated']; ?>">
																		<?php 
																	}else if($check_out_data[$check_out]['item_amt']['dilated'] > 0){
																																			echo $default_currency.$check_out_data[$check_out]['item_amt']['dilated']; 
																		?>
																																			<input type="hidden" name="copay_dilated_<?php echo $chk_main_id; ?>" id="copay_dilated" value="1">
																																			<input type="hidden" id="copay_dilated_tb" name="copay_dilated_tb" value="<?php echo $check_out_data[$check_out]['item_amt']['dilated']; ?>">
																																			<input type="hidden" id="copay_non_dilated_tb" name="copay_non_dilated_tb" value="0">
																		<?php
																	}else{
																		echo "-";
																	}
																	$copay_chrg = 0;

																	if($arr_chk_out['copay_type'] == '1'){
																		$copay_chrg = $check_out_data[$check_out]['item_amt']['dilated'];
																	}

																	if($arr_chk_out['copay_type'] == '2'){
																		$copay_chrg = $check_out_data[$check_out]['item_amt']['nondilated'];
																	}	
																}
																if($check_out_data[$check_out]['item_name'] == 'Copay-test'){
																	if($check_out_data[$check_out]['item_amt']['test_dilated'] > 0 && $check_out_data[$check_out]['item_amt']['test_nondilated ']> '0'){
																		echo $default_currency.$check_out_data[$check_out]['item_amt']['test_dilated'].'/'.$check_out_data[$check_out]['item_amt']['test_nondilated'];
																		?>
																		<div class="checkbox checkbox-inline">
																			<input <?php echo $visit_payment_readonly;  ?> type="checkbox" tabindex="<?php  print $tabindex; ?>" style="cursor:pointer;" name="copay_test_dilated_<?php echo $chk_main_id; ?>"  id="copay_test_dilated_<?php echo $chk_main_id; ?>" title="Copay Dilated" <?php if($arr_chk_out['copay_type'] == '1'){echo 'checked';} ?> onClick="set_test_copay(this,'<?php echo $chk_main_id; ?>','<?php echo $check_out_data[$check_out]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																			<label for="copay_test_dilated_<?php echo $chk_main_id; ?>">D</label>	
																		</div>	
																		<div class="checkbox checkbox-inline">
																			<input <?php echo $visit_payment_readonly;  ?> type="checkbox" tabindex="<?php print $tabindex; ?>" style="cursor:pointer;" name="copay_test_non_dilated_<?php echo $chk_main_id; ?>" id="copay_test_non_dilated" title="Copay Non Dilated" <?php if($arr_chk_out['copay_type'] == '2'){echo 'checked';} ?> onClick="set_test_copay(this,'<?php echo $chk_main_id; ?>','<?php echo $check_out_data[$check_out]['item_name']; ?>'); tot_payment(); tot_charges_fun();">		
																			<label for="">ND</label>	
																		</div>
																																			 <input type="hidden" id="copay_test_dilated_tb" name="copay_test_dilated_tb" value="<?php echo $check_out_data[$check_out]['item_amt']['test_dilated']; ?>">
																																			 <input type="hidden" id="copay_test_non_dilated_tb" name="copay_test_non_dilated_tb" value="<?php echo $check_out_data[$check_out]['item_amt']['test_nondilated']; ?>">	
																		<?php
																	}else if($check_out_data[$check_out]['item_amt']['test_nondilated'] > 0){
																																			echo $default_currency.$check_out_data[$check_out]['item_amt']['test_nondilated'];
																		?>
																		<input type="hidden" name="copay_test_dilated_<?php echo $chk_main_id; ?>" id="copay_test_dilated" value="1">
																																			<input type="hidden" id="copay_test_dilated_tb" name="copay_test_dilated_tb" value="0">
																																			<input type="hidden" id="copay_test_non_dilated_tb" name="copay_test_non_dilated_tb" value="<?php echo $check_out_data[$check_out]['item_amt']['test_nondilated']; ?>">	
																		<?php
																	}else{
																		echo '-';
																	}
																	$copay_chrg = 0;

																	if($arr_chk_out['copay_type'] == '1'){
																		$copay_chrg = $check_out_data[$check_out]['item_amt']['test_dilated'];
																	}

																	if($arr_chk_out['copay_type'] == '2'){
																		$copay_chrg = $check_out_data[$check_out]['item_amt']['test_nondilated'];
																	}	
																}
																?>
																<input type="hidden" id="item_charges_<?php echo $chk_main_id; ?>" name="item_charges_<?php echo $chk_main_id; ?>" value="$<?php echo $copay_chrg; ?>">
																<?php
															}else{
																echo $check_out_data[$check_out]['item_amt'];
																if($check_out_data[$check_out]['item_name'] == 'Refraction' && $check_out_data[$check_out]['item_amt'] > 0){
																	?>
																	<img src="../../../library/images/confirm.gif" style="width:16px;"/>
																	<?php
																}
																?>
																	<input type="hidden" id="item_charges_<?php echo $chk_main_id; ?>" name="item_charges_<?php echo $chk_main_id; ?>" value="<?php echo $check_out_data[$check_out]['item_amt']; ?>">
																<?php
															}		
														?>		
															</div>
															<div class="col-sm-6">
																<?php
																	if($arr_chk_in['item_payment'] > 0){
																		echo '<span>'.$default_currency.$arr_chk_in['item_payment'].'</span>';
																	}
																?>	
															</div>	
														</div>
													</div>
													<div class="col-sm-5">
														<?php $tabindex++; ?>
																											<div class="row">
															<div class="col-sm-1">
																<?php echo $default_currency; ?>	
															</div>	
															<div class="col-sm-4">
																<input <?php echo $visit_payment_readonly;  ?> type="text"  tabindex="<?php  print $tabindex; ?>" id="item_pay_<?php echo $chk_main_id; ?>" value="<?php echo $arr_chk_out['item_payment']; ?>" <?php echo $check_out_data[$check_out]['nr_enabled']; ?> name="item_pay_<?php echo $chk_main_id; ?>" onBlur="tot_payment();" onChange="tot_payment();" onKeyUp="sel_chkbox(this,'<?php echo $chk_main_id; ?>');set_pay_method('<?php echo $chk_main_id; ?>');" onClick="sel_chkbox(this,'<?php echo $chk_main_id; ?>');tot_charges_fun();" class="form-control">
															</div>
															<div class="col-sm-7">
																<select <?php echo $visit_payment_readonly;  ?> name="pay_method_<?php echo $chk_main_id; ?>" id="pay_method_<?php echo $chk_main_id; ?>" class="selectpicker" data-width="100%" onChange="return showRow(this);" data-title="Please Select">
																	<?php
																		foreach($pay_method as $key => $val){
																			$select = '';
																			if($selected_pay_method[$chk_main_id] == $key){
																				$select = 'selected';
																			}
																			echo "<option value='".$key."' ".$select.">".$val."</option>";
																		}
																	?>	
																</select>	
															</div>
														</div> 	
													</div>	
												</div>
											</div>	
									<?php
											$tabindex++;
											//if(fmod($i,3) > 0){
												//print '</div></div><div class="col-sm-4"><div class="row pt10">';
											//}else 
											if(fmod($i,3) == 0){
												print '<div class="clearfix"></div>';
												$i = 0;
											}
											$i++;
										}
									?>	
								</div>	
							</div>	
						</div>	
					</div>
					<!-- Comments -->
					<div class="col-sm-12 pt10">
						<label>Check Out Comment:</label>&nbsp; <textarea <?php echo $visit_payment_readonly;  ?> class="form-control" cols="160" rows="1" name="co_comment"><?php echo $co_comment; ?></textarea>	
					</div>	

					<!-- Payment Method Fields -->
					<div class="col-sm-12 pt10">
						<div class="row">
							<div id="checkRow" class="col-sm-2" style="display:<?php echo $checkRow; ?>">
								<label>&nbsp;Check #:&nbsp;</label>	
								<input <?php echo $visit_payment_readonly;  ?> name="checkNo" id="checkNo" type="text" class="form-control" value="<?php echo $payment_chk_number; ?>" />
							</div>
							<div id="creditCardRow" class="col-sm-6" style="display:<?php echo $creditCardRow; ?>">
								<div class="row">
									<div class="col-sm-4">
										<label>CC Type.:&nbsp;</label>	
										<select <?php echo $visit_payment_readonly;  ?> name="creditCardCo" id="creditCardCo" class="selectpicker" data-width="100%" data-title="Please Select">
											<?php
												foreach($cr_name_arr as $key => $val){
													$sel = '';
													if($key == $cr_selected){
														$sel = 'selected';
													}
													echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
												}
											?>
										</select>
									</div>

									<div class="col-sm-4">
										<label>CC #:&nbsp;</label>
										<input <?php echo $visit_payment_readonly;  ?> name="cCNo" id="cCNo" type="text" class="form-control" value="<?php echo $creditCardNumber; ?>" />	
									</div>

									<div class="col-sm-4">
										<label>Exp. Date:&nbsp;</label>
										<div class="input-group">
											<input <?php echo $visit_payment_readonly;  ?> id="date2" type="text" name="expireDate" value="<?php echo $creditCardDate; ?>" onBlur="return expDate();" class="form-control" />	
											<div class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>	
											</div>	
										</div>
									</div>	
								</div>	
							</div>
                            <?php if($pos_device) { ?>
                                <div class="col-sm-4">
                                    <?php 
                                    $laneID='10000002';
                                    $cc_class='checkout_payment';
                                    $target_ids='pay_method_';
                                    $scheduID=$sch_id;
                                    $fcount=count($checkInFieldsQryRes);
                                    include (dirname(__FILE__).'/../../accounting/pos/include_cc_payment.php');
                                ?>
                                </div>
                            <?php } ?>
						</div>
					</div>
					
					<div class="clearfix">&nbsp;</div>					
				
			</div>	
		</div>
		<div class="clearfix"></div>				
		<div class="mainwhtbox mt2 pd0" id="footer_box">
			<div id="module_buttons" class="ad_modal_footer" style="padding:0px;background:#fff">
				<div class="row text-center" style="padding-top:5px">
					<div class="col-sm-12" style="z-index: 9999999;">
						<button type="button" id="submit_btn" name="submit_btn" class="btn btn-success" onClick="save_but_disable();" >Save</button>
						<input type="submit" id="btn_submit" name="btn_submit" value="Save" class="hide">
						<input type="hidden" name="btn_submit_print" id="btn_submit_print" value="" />
						<button type="button" id="btn_print" name="btn_print" class="btn btn-success" onClick="print_save_fun();">Save & Print Receipt</button>
						<button type="button" tabindex="150" id="print_receipt" name="print_receipt" class="btn btn-default" onClick="print_receipt_check();">
						<span class="glyphicon glyphicon-print"></span> Print Receipt</button>
						<button type="button" tabindex="150" id="print_pt_visit" name="print_pt_visit" class="btn btn-default" onClick="print_pt_summary_check();">
						<span class="glyphicon glyphicon-print"></span> Print Pt Summary</button>
                        <?php if($pos_device) { ?>
                            <button type="button" name="pos_log" value="pos_log" class="btn btn-primary" onClick="window.show_transaction_popup();">POS Log</button>
                        <?php } ?>
						<button type="button" id="sv_button" name="sv_button" class="btn btn-danger" onClick="javascript:window.close();">Close</button>
					</div>
				</div>
			</div>
		</div>					
		</form>		
        
        
    <div id="div_loading_image" class="text-center" style="z-index:9999;display:none;">
        <div class="loading_container">
            <div class="process_loader"></div>
            <div id="div_loading_text" class="text-info"></div>
        </div>
    </div>
        
		<!-- Mpay Modal Box -->
		<?php 
			if($isMpay){ 
				$mpay_modal_data = '';
				$mpay_modal_data .= '
				<div class="row">
					<div class="col-sm-12 ">
						<div class="row">
							<div class="col-sm-12">
								<label>Copay Amount Collected: </label>
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-usd"></span>
									</div>
									<input type="text" id="mpay_copay" class="form-control" name="mpay_copay" value="" />	
								</div>	
							</div>
							<div class="col-sm-12">
								<label>Non-Copay Amount Collected: </label>
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-usd"></span>
									</div>
									<input type="text" id="mpay_noncopay" class="form-control" name="mpay_noncopay" value="" />									
								</div>	
							</div>
							<div class="col-sm-12">
								<label>Due Amount: </label>
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-usd"></span>
									</div>	
									<input type="text" id="mpay_dueamount" class="form-control" name="mpay_dueamount" value="" />
								</div>	
							</div>
							<div class="col-sm-12">
								<iframe name="mpay_frame" src="" id="mpay_frame" frameborder="0" style="margin:0px;height:1px; width:1px;"></iframe>
							</div>	
						</div>
						<br/>
					</div>
				</div>';
				$div_id = 'mpay_ask_div';
				$header_cont = 'Send to Mpay';
				$body_cont = $mpay_modal_data;
				$footer_cont = '<input type="button" value="Submit to Mpay" name="btn_mpay" id="btn_mpay" class="btn btn-success" onClick="this.value=\'Sending... Please Wait!\'; collect_n_send_mpay();" />&nbsp;';
				show_modal($div_id, $header_cont = 'imwemr', $body_cont, $footer_cont);
				echo '<script>$("#mpay_ask_div").modal("show");</script>';
				?>
				
	<?php	} ?>
	<script>
		<?php if(isset($isMpay) && $isMpay != ''){ ?>
			function mpay_div_show(){
				var copayVisitID = '<?php echo $visitCopayId; ?>';
				var copayTestID = '<?php echo $testCopayId; ?>';
				var PrevBalID = '<?php echo $prevBalId; ?>';
				var SuperbillDue = '<?php echo $superbilldue; ?>';
				if($('#'+copayVisitID).val()==''){firstCopay = 0;}else{firstCopay = parseInt($('#'+copayVisitID).val());}
				if($('#'+copayTestID).val()==''){secondCopay = 0;}else{secondCopay = parseInt($('#'+copayTestID).val())}
				if($('#'+PrevBalID).val()==''){prevBal = '0.00';}else{prevBal = $('#'+PrevBalID).val();}
				$('#mpay_copay').val((firstCopay+secondCopay)+'.00');
				$('#mpay_noncopay').val(prevBal);
				$('#mpay_dueamount').val(SuperbillDue);
				$('#mpay_ask_div').show()
			}
			
			
			function getRealTimeEligibility(insRecId, askElFrom){
				askElFrom = askElFrom || 0;
				show_loading_image("block");
				var url = '<?php echo $GLOBALS['webroot']; ?>/interface/patient_info/ajax/make_270_edi.php?insRecId='+insRecId+'&askElFrom='+askElFrom;
				$.ajax({
					url:url,
					type:'GET',
					success:function(response){
						if(response){
							show_loading_image("none");	
							var strResp = $.parseJSON(response);
							var arrResp = strResp.data.split("~~");
							if(arrResp[0] == "1" || arrResp[0] == 1){
								var alertResp = "";
								if(arrResp[1] != ""){
									alertResp += "Patient Eligibility Or Benefit Information Status :"+arrResp[1]+"\n";
								}
								if(arrResp[2] != ""){
									alertResp += "With Insurance Type Code :"+arrResp[2]+"\n";
								}
								if(alertResp != ""){
									if(arrResp[3] == "A"){
										document.getElementById('imgEligibility').src = "../../../library/images/eligibility_green.png";
									}
									else if(arrResp[3] == "INA"){
										document.getElementById('imgEligibility').src = "../../../library/images/eligibility_red.png";
									}
									document.getElementById('imgEligibility').title = alertResp;
									alert(alertResp);
								}
							}
							else if(arrResp[0] == "2" || arrResp[0] == 2){						
								if(arrResp[1] != ""){
									alert(arrResp[1]);
									document.getElementById('imgEligibility').src = "../../../library/images/eligibility_red.png";
									document.getElementById('imgEligibility').title = arrResp[1];
								}
							}
							else{
								//document.write(arrResp[0]);
								fAlert(arrResp[0]);
							}
						}
					}
				});
			}
			
			
			function expDate(){ 
				var expireDate = document.getElementById("date2").value;
				if(expireDate!=''){
					var strLen = expireDate.length;
					var posSlash = expireDate.indexOf("/");
					var posDash = expireDate.indexOf("-");
					if((posSlash == 2) || (posDash == 2)){
						var formatExp = true;
					}else{
						if((posSlash == 1) || (posDash == 1)){
							var expireDate = '0'+expireDate;
							
						}else{
							var formatExp = false;
						}
					}
					var mm = expireDate.substr(0,2);
					var yy = expireDate.substr(3);
					if((strLen>5) || (strLen<5) || (formatExp==false) || (mm>12) || (mm<=0) || (yy<=0)){
						if(strLen==7){
							if(formatExp==false){
								alert("Please enter date in forrmat mm/yy")
								document.getElementById("date2").value = '';
							}else{
								yySess = expireDate.substr(3, 2);
								if(yySess!=20){
									alert("Please enter year correctly.")
									document.getElementById("date2").value = '';
								}
							}
						}else{
							alert("Please enter date in forrmat mm/yy")
							document.getElementById("date2").value = '';
						}
					}
				}
			}
			function collect_n_send_mpay(){
				var qString = '../../mpay/index.php?from=checkout&copay='+$('#mpay_copay').val()+'&paid='+$('#mpay_noncopay').val()+'&balance='+$('#mpay_dueamount').val()+'&patient_id='+$('#patient_id').val()+'&sch_id='+$('#sch_id').val();
				$('#mpay_frame').attr('src',qString);
				//alert(qString);
			}
			function hide_mpay_div(){$('#mpay_ask_div').modal('hide');$('#btn_mpay').val('Submit to Mpay');}
		<?php } ?>
		
		
	$(window).load(function(){
		/*if(typeof(window.opener.top.innerDim)=='function'){
			var innerDim = window.opener.top.innerDim();
			if(innerDim['w'] > 1600) innerDim['w'] = 1600;
			if(innerDim['h'] > 900) innerDim['h'] = 900;
			window.resizeTo(innerDim['w'],innerDim['h']);
		}*/
		
		var mw =  parseInt(screen.availWidth*0.9);
		var mh =  parseInt(screen.availHeight*0.9);
		popup_resize(mw,mh);
		brows	= get_browser();
		mh = mh-$("#footer_box").outerHeight(true)-110
		if(brows!=='ie') mh = mh+20;
		$('#content_box').css({'max-height':mh+'px','overflow':'hidden','overflow-y':'auto'});
		
	});
		
		function chk_negative(_this){
			if( typeof _this == 'object' ){
				var v = $(_this).val();
				if( v ) {
					if( v < 0 ) {
						top.fAlert('Negative values are not allowed');
						$(_this).focus();
					}
				}
			} else {
				for(i=1;i<13;i++){
					var chkbox = "chkbox_"+i;
					var item_pay = "item_pay_"+i;
					if(document.getElementById(chkbox)){
						if(document.getElementById(chkbox).checked == true){
							document.getElementById(item_pay).value = removeCommas(document.getElementById(item_pay).value);
							if(document.getElementById(item_pay).value != '' ){
								if(document.getElementById(item_pay).value < 0){
									fAlert("Negative values are not allowed.",'',$('#'+item_pay));
									return false;
								}
							}
						}
					}	
				}
				
				return true;
			}
		}
	</script>
	</body>	
</html>