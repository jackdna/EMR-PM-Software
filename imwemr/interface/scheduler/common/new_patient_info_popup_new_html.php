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
        <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
            <link href="<?php echo $GLOBALS['webroot'] ?>/library/css/imw_css.css" rel="stylesheet">
        <?php } ?>
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
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/ci_function.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/Driving_License_Scanning.js"></script>
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
			var mandatory_arr_js='<?php echo json_encode($mandatory_fields_array); ?>';
			var mandatory_field_arr = JSON.parse(mandatory_arr_js);
            var resp_mandatory_js='<?php echo json_encode($resp_mandatory_arr); ?>';
			var resp_mandatory_arr = JSON.parse(resp_mandatory_js);
			var advisory_arr_js='<?php echo json_encode($advisory_fields_array); ?>';
			var advisory_field_arr = JSON.parse(advisory_arr_js);
			var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
			var practice_dir	 		=	"<?php echo constant('PRACTICE_PATH'); ?>";
            var mandatory = <?php echo json_encode($mandatory_tmp_arr); ?>;
			var vocabulary = <?php echo json_encode($vocabulary); ?>;
			var zipLabel = "<?php getZipPostalLabel(); ?>";
			var hashOrNo = "<?php getHashOrNo(); ?>";
			var isERPPortalEnabled = '<?php echo isERPPortalEnabled(); ?>';
		</script>
	
	<style>
		.previmg{ border:1px solid #ACACAC; padding:5px; text-align:center; color:#747474; margin-top:25px; line-height:50px; max-height:90px; height:75px; overflow:hidden; position:relative; }
		.previmg img { width:auto; max-height:70px; }
		.previmg .layer { position:absolute; top:0; left:0; width:100%;  text-align:center; height:100%; display:none; background:rgba(0,0,0,0.8) url(../images/search-hover.png) no-repeat center; cursor:pointer; }
		.previmg:hover .layer { display:block;} 
		.adminbox{ padding:10px;  margin-bottom:10px;border:none!important}
		.adminbox .headinghd{ border-bottom:2px solid #ff6b6b; padding:0px; margin:0px 0px 10px 0px; display:inline-block; float:left; width:100%  }
		.adminbox .headinghd h4{ text-transform:uppercase; font-size:16px; font-family: 'robotobold'; }
		.adminbox h3{ font-size:14px; color:#6c6c6c; margin:0px 0px 10px 0px; padding:0px; text-transform:uppercase; font-weight:bold }
		.adminbox .input-group-addon {padding: 0px 7px !important;}
		.adminbox label{overflow:inherit}
		.adminbox .tblBg { clear:both }
		.input-group-btn select {
			border-color: #ccc;
			margin-top: 0px;
			margin-bottom: 0px;
			padding-top: 7px;
			padding-bottom: 7px;
		}
		.thumbnail{padding:0px; margin-bottom: 0px!important}
		.extension_box label::before{display:none}
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
		.process_loader {
			border: 16px solid #f3f3f3;
			border-radius: 50%;
			border-top: 16px solid #3498db;
			width: 80px;
			height: 80px;
			-webkit-animation: spin 2s linear infinite;
			animation: spin 2s linear infinite;
			display: inline-block;
		}
		.thumbnail .caption {
			position: absolute;
			top: 5px;
			width: 80%;
		}
		.xdsoft_monthselect .xdsoft_option{
			text-align: left !important;
		}
		span.dropdown-toggle{padding:10px;}
		.UGAIcon:hover a {background-position: center -35;}
        
        /*Release Information CSS starts Here*/
        .release_info {margin:10px 0px;}         
        .release_info .panel-heading {padding:5px 5px!important;}         
        .release_info .panel-heading a {font-size:14px;text-decoration:none;}  
        .release_info .panel-heading a:hover {color:#333!important;}  
        .release_info a[data-toggle="collapse"]::before {
            content: "";
            float: left;
            font-family: "Glyphicons Halflings";
            margin-right: 1em;
        }
        .release_info a.collapsed[data-toggle="collapse"]::before {content: "";}
        .release_info .table>thead>tr>th {padding:4px!important;}
        /*Release Information CSS ends Here*/

        @media (min-width: 768px) {
			.form-inline .checkbox input[type=checkbox], .form-inline .radio input[type=radio] {
			    margin-left: -18px !important;
			}
		}
        
	</style>
	<script>
			//ser_root = "{$web_root}/xml/refphy";
			//WRP = "{$web_root}";
			var isMpay = '';
			var showViewPaymentsRow = '<?php echo $showViewPaymentsRow; ?>';
			if(typeof(window.opener)!= 'undefined'){
				REF_PHY_FORMAT = window.opener.top.REF_PHY_FORMAT;
			}else{
				REF_PHY_FORMAT = '';	
			}
			popup_resize(screen.availWidth,screen.availHeight,0.85)
			//setTimeout(popup_resize(screen.availWidth,screen.availHeight,0.8),0);
			function chkshowMultiPhy(op, phyType){
				if(sessionPatID == ''){
					get_action('submit_form')
					return false;
				}else
				show_multi_phy(op, phyType)
			}
			
			function genTempKeyCheckIn(tempKeySize,pid) {
				$.ajax({
					url:'../../patient_info/ajax/demographics/ajax_handler.php',
					data:{action:'temp_key_generate',temp_key_size:tempKeySize,pid:pid},
					type:'POST',
					complete:function(respData){
						var response = $.trim(respData.response);
						$('#temp_key').val(response);
					}
				});		
				
			}	
			function get_browser(){
				browser = '';
				if(navigator.userAgent.indexOf("MSIE") != -1 || !!navigator.userAgent.match(/Trident\/7\./)){
					browser =  "ie";
				}
				else if(typeof(window.mozilla) == "object"){
					browser =  "mozilla";
				}
				else if(typeof(window.chrome) == "object"){
					browser =  "chrome";
				}else if(navigator.userAgent.indexOf("Safari") != -1){
					browser =  "safari";
				}
				return browser;
			}	
			
			$(document).ready(function(){
				window.opener.top.show_loading_image('hide');
				//window.resizeTo(1200,screen.height-75);
				var pat_val = '<?php echo $pat_val; ?>';
				/*if(pat_val == 'New'){
					var temp_height = parseInt($(window).height());
					if(temp_height > 730){
						window.resizeTo(1200,730);
					}
				}*/
				//var body_div_height = $(window).height();
				setTimeout(function(){
					browser = get_browser();
					if(browser == "ie"){
						var footertop = (parseInt($(window).height())-40);
					}else if(browser == "chrome" || browser == "safari"){
						var footertop = document.body.scrollHeight;
					}
				},0);
				//$('.mainwhtbox').css('height',body_div_height+'px');
				var date_global_format = window.opener.top.jquery_date_format;
				$('.dob-date-pick').datetimepicker({
					timepicker:false,
					format:date_global_format,
					formatDate:date_global_format,
					scrollInput:false,
					maxDate:new Date(),
					onSelectDate:function(input){
						on_dob_changed();
					}
				});	
				
				$('.date-pick').each(function(id,elem){
					var value = $(elem).val();
					if(value == '00-00-0000'){
						$(elem).val('');
					}
				});
				//$( ".date-pick" ).datepicker({changeMonth: true,changeYear: true,dateFormat:'<?php echo inter_date_format(); ?>', yearRange: 'c-10:c+10'});
				var edit_payment_tbl_id = '<?php echo $main_pay_id; ?>';
				
			if(edit_payment_tbl_id != ''){
				document.getElementById("print_rec_id").style.display = "inline";
				document.getElementById("print_save_rec_id").style.display = "inline";
				isMpay = '<?php echo verify_payment_method("MPAY"); ?>';
				if(isMpay.length){
					$("#mpay_btn_cell").css('display','inline');
				}
			}
			
			var edit_patient_id = '<?php echo $patientQryRes[0]['pid'] ?>';
			if(edit_patient_id != ''){
				var edit_patient_id	 = edit_patient_id;
				var patient_username = '<?php echo trim($patientQryRes[0]['username']); ?>';
				var patient_password = '<?php echo trim($patientQryRes[0]['password']); ?>';
				var patient_temp_key = '<?php echo trim($patientQryRes[0]['temp_key']); ?>';
				if(edit_patient_id!="" && patient_temp_key=="" && (patient_username=="" || patient_username=='NULL')) {
					genTempKeyCheckIn('9',edit_patient_id);	
				}
				$('#print_save_rec_id').show();
				$('#print_rec_id').show();
				if(isMpay.length){	
					$("#mpay_btn_cell").css('display','inline');
				}
			}
			
			
			if(showViewPaymentsRow == 'none'){
				document.getElementById("print_rec_id").style.display = "none";
				document.getElementById("print_save_rec_id").style.display = "none";
				if((typeof(document.getElementById("mpay_btn_cell"))!="undefined" && typeof(document.getElementById("mpay_btn_cell"))!=null)
				  && (document.getElementById("mpay_btn_cell")!="undefined" && document.getElementById("mpay_btn_cell")!=null)){
					$("#mpay_btn_cell").css('display','none');
				}
			}	
			
			$("#ethnicity").selectpicker();
			$("input[name^='ethnicity']").bind('click',function(){
				var ethana_val=$("#ethnicity").val();
				var n=ethana_val.split(",");
				if(n.indexOf("Other")>0){
					if($(this).is(":selected")){
						var htmlData = showOtherTxtBoxEthnacity('ethnicity','otherEthnicity_span','other_ethnicity','','other_ethnicity'); 
						//$(".multiSelectOptions").css('visibility', 'hidden');
					}
				}else if(ethana_val=="Other"){
					var htmlData = showOtherTxtBoxEthnacity('ethnicity','otherEthnicity_span','other_ethnicity','','other_ethnicity');
					//$("#otherEthnicity").focus(); 
					$(".multiSelectOptions").css('visibility', 'hidden');
				}else{
					var htmlData = showOtherTxtBoxEthnacity('otherEthnicity_span','ethnicity','other_ethnicity','y','other_ethnicity');
				}
			});
			//$("#race").multiSelect({noneSelected:'Select All',listHeight:'340'});
			$("input[name^='race']").bind('click',function(){
				var rave_val=$("#race option:selected").val();
				var race_n=rave_val.split(",");
				if(race_n.indexOf("Other")>0){
					if($(this).is(":checked")){
						$("#otherRace").css('display','block');
						$("#race_lbl").css('display','block');
					}
				}else if(rave_val=="Other"){
					$("#otherRace").css('display','block');
					$("#race_lbl").css('display','block');
					//$(".multiSelectOptions").css('visibility', 'hidden');
				}else{
					$("#otherRace").css('display','none');
					$("#race_lbl").css('display','none');
				}
			});
			$('body').on('hidden.bs.modal',function(){
				document.getElementById("anchor_patient_photo").disabled = false;
				document.getElementById("anchor_scan_license").disabled = false;
				//$("#ptLicToolBarDiv").css('display','block');
			});

			var _selectors = document.querySelectorAll('#ref_phy_name,#primary_care_name');
			for(var i = 0; i < _selectors.length; i++) {
					_selectors[i].addEventListener('keyup', function(event){
						var _this = $(this)[0];
						if( _this.hasAttribute('data-content') ) {
							if( _this.value == '' || _this.getAttribute('data-prev-val') !== _this.value ) {
								_this.setAttribute('data-content','');
								$(this).popover('destroy');
							}
							else {
								$(this).popover('hide');
							}
						}
					});
			}

			function iportal_load_pghd_reqs(){
				var n="pghd_reqs";
				window.open("get_pghd_req.php",n,'location=1,status=1,resizable=1,left=10,top=1,scrollbars=1,width=1000,height=500');
			}
			
			<?php if($pat_val == 'Check In' && !isset($_REQUEST['pghd_done'])){ ?>
				//Open Pop up For iPortal PGHD demographics and Insurance data
				if(isERPPortalEnabled && edit_patient_id != ''){
					iportal_load_pghd_reqs();
				}
				
			<?php } ?>
			
			<?php if($pat_val!== 'New') { ?>
				rx_consent_notification();
			<?php } ?>
			
		});
		
			function print_save_fun(hit_done){
                var no_pos_device=false;
                if($('#tsys_device_url').val()=='no_pos_device') {
                    no_pos_device=true;
                }
				dgi("btn_submit_print").value = 'yes';
				if( !chk_negative() ){return false;}
				if( typeof hit_done != 'boolean' ) hit_done = false;	
				$("#hitDoneBtn").val(hit_done?'1':'0');
                var cc_card=cc_card_check();
                
                if(!checkValidPayMethod(cc_card)){return false;}
                if(cc_card && pos_device && typeof(make_cccard_payment)!='undefined' && no_pos_device==false) {
                    make_cccard_payment();
                } else {
                    
                    pos_submit_frm();
                }
			}
			
			function get_action(fun_val,hit_done,adult_check,resp_cred){
				
				if( typeof hit_done != 'boolean' ) hit_done = false;
				if(!adult_check) adult_check=false;
				if(!resp_cred) resp_cred=false;
				switch(fun_val){
					case "submit_form":
						$("#hitDoneBtn").val(hit_done?'1':'0');
						return validate_input('',adult_check,resp_cred);
					break;
					case "reset_form":
						on_reset_actions();
					break;
					case "mpay_form":
						mpay_div_show('<?php echo $visitCopayId; ?>','<?php echo $testCopayId; ?>','<?php echo $prevBalId; ?>');
					break;
				}
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
				if(!checkValidPayMethod(cc_card)){return false;}
			}
			function print_receipt_check(){
				var edit_id = dgi("edit_payment_tbl_id").value;
				var sch_id = dgi("sch_id").value;
				window.open("payment_receipt.php?id="+sch_id,'print_receipt','width=800,height=550,top=10,left=40,scrollbars=yes,resizable=yes');
			}
			
			String.prototype.trim = function() {
				return this.replace(/^\s+|\s+$/g,"");
			}
			
			
			function newWindow(q)
			{
				window.open('../../common/mycal.php?md='+q,'imwemr','width=200,height=250,top=200,left=300');
			}
			
			function restart(obj)
			{
				document.getElementById(obj).value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year ;
			}
function validate_filled_fields(myvar, changecolor, arr_msg, arr_focus){
	var msg_code_str = "";
	var matched_ssn_pt = "";
	//ssn uniqueness, duplicate login id check
	var userName, ssnNumber;
	ssnNumber = document.demographics_edit_form.ssnNumber.value;
	$.ajax({
		url: "../../patient_info/demographics/AJAXValidationDemo.php?ssnNumber=" + ssnNumber,
		success: function(responseText){
			if(responseText != ""){
				
				var arrAJAXResp = responseText.split("~~");
				//unique ssn			
				if(arrAJAXResp[0] == "1"){
					msg_code_str += "1,";
					document.demographics_edit_form.ssnNumber.style.backgroundColor = changecolor;
					matched_ssn_pt = arrAJAXResp[2];
					flagForm = 0;
				}else{
					flagForm = 1;
				}
				if(msg_code_str != ""){
					var arr_func = new Array();
					arr_func[0] = "return false";
					arr_func[1] = "";
					top.show_loading_image('hide');
					flagFrom = pi_show_alert("alert", msg_code_str, arr_msg, arr_focus, arr_func, null, '', matched_ssn_pt);
				}
				return flagForm;
			}
		}
	});
}
function pi_show_alert(mode, msg_str, arr_msg, arr_focus, arr_func, response_mode, height_adjustment, optional_val){	
	
	var msg_to_show = "Please fill the following fields correctly:<br><br>";
	var set_focus_to = "";
	var focus_set = false;

	var default_width = 375;
	
	if(msg_str.substring(0,2) == "!!"){
		msg_to_show += msg_str.substring(2);
	}else{
		var arr_show_msg = msg_str.split(",");
		arr_show_msg = arr_show_msg.filter(function(e){return e}); 
		for(i = 0; i < arr_show_msg.length; i++){
			if(focus_set == false){
				if(arr_focus[arr_show_msg[i]] != ""){
					set_focus_to = arr_focus[arr_show_msg[i]];
					focus_set = true;
				}				
			}
			msg_to_show += arr_msg[arr_show_msg[i]] + "<br>";
			if(set_focus_to=='ssnNumber'){
					msg_to_show += optional_val+'<br><br>';
			}
		}
	}
	if(mode == "alert"){
		if(response_mode == ""){
			msg_to_show += "<br>Click OK Button to continue saving.";
		}
		if(set_focus_to != ""){
			fAlert(msg_to_show);
		}
	}
}



function validate_input(flagForm,adult_check,resp_cred){
	var alert_msg = '';
	var mandatory_input_arr = new Array();
	frm = document.demographics_edit_form;
    var resp_container = $('#new_resp_container').is(':visible');
    $.each(mandatory_field_arr,function(v,i){
		if(typeof i === 'string') {
            if( resp_mandatory_arr.indexOf(i) >= 0 && resp_container==false ){return true;}
			var obj = $("#"+i);
            if( i == 'sex') obj = $("#selGender");
            else if( i == 'status') obj = $("#pat_marital_status");
            else if( i == 'ss') obj = $("#ssnNumber");
            else if( i == 'email') obj = $("#pat_email");
            else if( i == 'elem_physicianName') obj = $("#ref_phy_name");
            else if( i == 'primaryCarePhy') obj = $("#primary_care_name");
			if(obj.length)
			{               
				var t_msg = typeof(vocabulary[i]) == 'undefined' ? '' : vocabulary[i];
				t_msg = t_msg.replace(/\\n/g,'');
				if(obj.val() === '' ) {
					alert_msg +=(t_msg)?t_msg +'<br>':'';
					mandatory_input_arr.push(i);
				}
                
			}
		}
	});
	// Alerting user 
	if(alert_msg != ''){
		var alert_heading = 'Following fields are mandatory:<br><br>';
		fAlert(alert_heading+alert_msg);	
		change_to_mandatory(mandatory_input_arr);
		return false;
	}
	if(dgi("ssnNumber").value!="" && !validate_ssn(dgi("ssnNumber"))){  
		top.show_loading_image('hide');
		frm.ssnNumber.focus();					
		return false;
	}
	if(!validateAddress(document.demographics_edit_form,"checkin")){
		top.show_loading_image('hide');
		return false;
	}
    
    //If patient age is below 18 years responsible party is required.
    if(!adult_check)adult_check=false;
	if(!resp_cred)resp_cred=false;
    
    <?php if($pat_val == 'New'){ ?> 
		if(isERPPortalEnabled){
			if(!flagForm)flagForm=false;
			if( (dgi('fname1').value != "" || dgi('lname1').value != "" ) && resp_cred==false ){
				var msg='';
				var set_focus_to='erp_resp_username';
				if(dgi("erp_resp_username").value == ""){
					msg=" - Without Username and password the Representative account cannot be created on Patient Portal. ";
					set_focus_to="erp_resp_username";
				}else if(dgi("erp_resp_passwd").value == ""){
					msg=" - Without Username and password the Representative account cannot be created on Patient Portal. ";
					set_focus_to="erp_resp_passwd";
				}else if(dgi("erp_hidd_passwd").value == ""){
					var new_pass = dgi("erp_resp_passwd").value;
					var confirm_pass = dgi("erp_resp_cpasswd").value;
					if(new_pass != confirm_pass){
						if(confirm_pass==''){
							msg=" - Confirm Representative Password is required. ";
							set_focus_to="erp_resp_cpasswd";
						} else {
							msg=" - Confirm Password does not matches with Password. ";
							set_focus_to="erp_resp_cpasswd";
						}	
					}
				}
    
				var arr_func = [];
				arr_func[0] = "return false";
				arr_func[1] = "";

				if(msg!='') {
					top.fancyConfirm(msg, "",  "window.top.validate_input("+flagForm+","+adult_check+",\"1\")",  "document.getElementById('"+set_focus_to+"').focus(); "+arr_func[1]);
					return false;
				}
			}
		}
		if(!resp_party_adult_checks(adult_check,resp_cred)){return false;}  
	<?php } ?>
	
    
    
	if(dgi('insPriProv').value != ''){
		top.show_loading_image('hide');
		if(!fun_chk_ins_dates('Primary','Pri')){
			return false
		}
	}
	if(dgi('insSecProv').value != ''){
		top.show_loading_image('hide');
		if(!fun_chk_ins_dates('Secondary','Sec')){
			return false
		}
	}
	
	var pri_ins_pro_id = $('#insPriProv').val();
	var sec_ins_pro_id = $('#insSecProv').val();
	var pri_ins_return = check_ins_exist(pri_ins_pro_id, 'primary');
	var sec_ins_return = check_ins_exist(sec_ins_pro_id, 'secondary');
	if(pri_ins_return != true  || sec_ins_return != true){
		return false;	
	}
    var no_pos_device=false;
    if($('#tsys_device_url').val()=='no_pos_device') {
        no_pos_device=true;
    }
	
	if( !chk_negative() ) { return false; }
	
    var cc_card=cc_card_check();
    
    if(!checkValidPayMethod(cc_card)){return false;}
    if(cc_card && pos_device && typeof(make_cccard_payment)!='undefined' && no_pos_device==false) {
        make_cccard_payment();
    } else {
        pos_submit_frm();
    } 
}

function set_pay_method(j) {
    if(pos_device) {
        $('#pay_method_'+j).find('option[value="Credit Card"]').prop("selected", "selected");
        $('#pay_method_'+j).selectpicker('refresh');
        showRow($('#pay_method_'+j)[0]);
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

function pos_submit_frm() {
    if(document.getElementById('submit_btn')){
		document.getElementById('submit_btn').disabled=true;
	}
	if(document.getElementById('btn_print')){
		document.getElementById('btn_print').disabled=true;
	}
	
	if( $("#hiddCheckInOnDone").val() && $("#hiddApptIdsLen").val() > 1 ) {
			var msg="Is Check-in be applied to the other appointments of the same patient";
			top.fancyConfirm(msg, '','confirmApplyAll(1);','confirmApplyAll(0);');
	} 
	else {
		dgi("btn_submit").disabled = false;
		dgi("btn_submit").click();
	}
}

function confirmApplyAll(v) {
	v = parseInt(v);
	$("#hiddConfirmCheckInApplyAll").val(v);
	dgi("btn_submit").disabled = false;
	dgi("btn_submit").click();
}			
			
			function fun_chk_ins_dates(nm,pre){
				actDate=Date.parse(dgi('ins'+pre+'ActDt').value); 
				expDate=Date.parse(dgi('ins'+pre+'ExpDt').value);
				if(dgi('ins'+pre+'ActDt').value != '' && dgi('ins'+pre+'ExpDt').value != ''){
					if(actDate >= expDate){
						fAlert(nm+' Insurance expiration date must be greater than '+nm+' Insurance activation date.','',dgi('ins'+pre+'ExpDt'));
						return false;
					}
				}
				return true;
			}

			function on_reset_actions(){
				frm = document.demographics_edit_form;
				frm.reset(); 
			}
			
			function delete_license(){//alert('hi '+document.getElementById("license_image_name").value);
				document.getElementById("ptLicToolBarDiv").style.display = "none";
				var del_path = document.getElementById("license_image_name").value;
				if(del_path != ""){
					del_path = del_path.replace("/temp/", "");
					$.ajax({ url: "../../patient_info/demographics/webcam/delSessionImg.php?del_path="+del_path, success: function(){
						document.getElementById("ptLicImageDiv").innerHTML = "";
						document.getElementById("license_image_name").value = "";
						document.getElementById("anchor_scan_license").disabled = false;
					}});
				}
			}
			
			function loadCaseInfo(id,pid){	
				var case_id = id;
				if(case_id){
					$.ajax({
						url:"getInsComp.php?ins="+case_id+"&pid="+pid,
						type:'GET',
						success:function(response){
							document.getElementById("insSecProv").disabled = false;
							document.getElementById("insSecPolicy").disabled = false;
							document.getElementById("insSecGroup").disabled = false;
							document.getElementById("insSecCopay").disabled = false;
							document.getElementById("insSecActDt").disabled = false;
							document.getElementById("insSecExpDt").disabled = false;
							document.getElementById("anchor_secondary_scan").disabled = false;
							document.getElementById("cbk_self_pay_provider").checked = false;
							var insDataRes = response.split("~~");
							var caseType = insDataRes[0].split("||");
							var arrInsSwapData = [];
							//--- NORMAL CASE CHECK --
							var refReqDis = 'none';
							var conInsFld = 'none';
							//var authReqDis = 'none';
							dgi("pri_ref_req").value = 'No';
							dgi("sec_ref_req").value = 'No';
							if(caseType[0] == 1 || caseType[1] == 1){						
								if(caseType[0] == 1){
									refReqDis = 'block';
								}
								var conInsFld = 'block'; 
								//if(caseType[1] == 1){
									//authReqDis = 'block';
								//}
								dgi("Primary_Policy").innerHTML = "Policy "+top.hashOrNo;
								dgi("Primary_Group").innerHTML = "Group "+top.hashOrNo;
								dgi("Primary_Copay").innerHTML = "Copay";
								
								dgi("Secondary_Policy").innerHTML = "Policy "+top.hashOrNo;
								dgi("Secondary_Group").innerHTML = "Group "+top.hashOrNo;
								dgi("Secondary_Copay").innerHTML = "Copay";
		
							}else{
								dgi("Primary_Policy").innerHTML = "Claim";
								dgi("Primary_Group").innerHTML = "Emp. Name";
								dgi("Primary_Copay").innerHTML = "Adj. Name";
								
								dgi("Secondary_Policy").innerHTML = "Claim";
								dgi("Secondary_Group").innerHTML = "Emp. Name";
								dgi("Secondary_Copay").innerHTML = "Adj. Name";
							}
							
							dgi("pri_coins_div").style.display = conInsFld;
							dgi("pri_ref_div").style.display = refReqDis;
							dgi("sec_ref_div").style.display = refReqDis;
							//dgi("pri_auth_div").style.display = authReqDis;
							//dgi("sec_auth_div").style.display = authReqDis;
							
							dgi("refRaqDiv").style.display = 'none';
							dgi("secRefRaqDiv").style.display = 'none';
							//dgi("authReqDisDiv").style.display = 'none';
							//dgi("secAuthReqDisDiv").style.display = 'none';
							
							$insDataArr = insDataRes[1].split('|~|');
							$insSwapDataArr = insDataRes[2].split('|~|');
							//--- PRIMARY INSURANCE COMPANY DATA ---
							var priInsData = $insDataArr[0].split("||");
							
							dgi("pri_auth_req").value = "No";
							dgi("pri_ref_req").value = "No";
							var filedNameArr = new Array();
							filedNameArr[0] = "insPriProv";
							filedNameArr[1] = "insurance_primary_id";
							filedNameArr[2] = "insPriGroup";
							filedNameArr[3] = "insPriCopay";
							filedNameArr[4] = "insPriActDt";
							filedNameArr[5] = "insPriExpDt";
							filedNameArr[6] = "insPriPolicy";
							filedNameArr[7] = "insPriProv_id";
							filedNameArr[8] = "pri_ref_req";
							filedNameArr[9] = "self_pay_provider";
							filedNameArr[10] = "insPriCoIns";
							filedNameArr[11] = "pri_auth_req";
							filedNameArr[12] = "auth_pri_id";
							filedNameArr[13] = "AuthPriNumber";
							filedNameArr[14] = "AuthPriAmount";
							filedNameArr[15] = "pri_auth_date";
							filedNameArr[16] = "pri_auth_date_end";
							filedNameArr[17] = "pri_auth_visits";
							filedNameArr[18] = "pri_simple_menu";
							
							if(caseType[0] == 1){
								filedNameArr[19] = "pri_reff_id";
								filedNameArr[20] = "pri_ref_phy_id";
								filedNameArr[21] = "pri_ref_phy";
								filedNameArr[22] = "pri_ref_visits";
								filedNameArr[23] = "pri_ref_number";
								filedNameArr[24] = "pri_ref_stDt";
								filedNameArr[25] = "pri_ref_enDt";
							}
							
							/*if(caseType[1] == 1){
								filedNameArr[11] = "auth_pri_id";
								filedNameArr[12] = "AuthPriNumber";
								filedNameArr[13] = "AuthPriAmount";
								filedNameArr[14] = "pri_auth_date";
								filedNameArr[15] = "pri_simple_menu";
							}*/
							
							
							//--- DISABLE SECONDARY INSURANCE COMPANY ---
							if(priInsData[0] == ''){
								document.getElementById("insSecProv").disabled = true;
								document.getElementById("insSecPolicy").disabled = true;
								document.getElementById("insSecGroup").disabled = true;
								document.getElementById("insSecCopay").disabled = true;
								document.getElementById("insSecActDt").disabled = true;
								document.getElementById("insSecExpDt").disabled = true;
								document.getElementById("anchor_secondary_scan").disabled = true;
							}
							
							for(i=0;i<priInsData.length;i++){
								var fldObj = dgi(filedNameArr[i]);
								if(fldObj){									
									if(fldObj.id == 'pri_simple_menu'){
										$('#'+filedNameArr[i]).parent().replaceWith(priInsData[i]);
										//fldObj.innerHTML = priInsData[i];
									}
									else{
										fldObj.value = priInsData[i];
									}
									if(i == 8 && fldObj.value == 'Yes' && caseType[0] == 1){
										dgi("refRaqDiv").style.display = 'block';	
									}
									/*if(i == 10 && fldObj.value == 'Yes' && caseType[1] == 1){
										dgi("authReqDisDiv").style.display = 'block';	
									}*/
								}
							}
							get_accept_assignment();
							if(priInsData[9] == 1){
								dgi("cbk_self_pay_provider").checked = true;
								dis_insurance_com(dgi("cbk_self_pay_provider"));
							}
							$("#pri_ref_req,#pri_auth_req").trigger('change').selectpicker('refresh');
							
							if(typeof($insSwapDataArr[0])!='undefined'){
								var tmpArr = {};var tmpInnArr = {};
								var priInsSwapData = $insSwapDataArr[0].split("||");
								if(typeof(priInsSwapData)!='undefined'){
									tmpArr['ins_case_id'] = priInsSwapData[0];
									tmpInnArr['insType'] = priInsSwapData[1];
									tmpInnArr['insDataId'] = priInsSwapData[2];
									tmpInnArr['providerId'] = priInsSwapData[3];
									tmpInnArr['providerName'] = priInsSwapData[4];
									tmpArr['insData'] = tmpInnArr;
									arrInsSwapData.push(tmpArr);
								}
							}
							//--- SECONDARY INSURANCE COMPANY DATA ---
							var secInsData = new Array();
							if($insDataArr[1]){
								secInsData = $insDataArr[1].split("||");
							}
							
							dgi("sec_auth_req").value = 'No';
							dgi("sec_ref_req").value = "No";
							
							var filedNameArr = new Array();
							filedNameArr[0] = "insSecProv";
							filedNameArr[1] = "insurance_secondary_id";
							filedNameArr[2] = "insSecGroup";
							filedNameArr[3] = "insSecCopay";
							filedNameArr[4] = "insSecActDt";
							filedNameArr[5] = "insSecExpDt";
							filedNameArr[6] = "insSecPolicy";
							filedNameArr[7] = "insSecProv_id";
							filedNameArr[8] = "sec_ref_req";
							filedNameArr[9] = "self_pay_provider";
							filedNameArr[10] = "sec_auth_req";
							filedNameArr[11] = "auth_sec_id";
							filedNameArr[12] = "AuthSecNumber";
							filedNameArr[13] = "AuthSecAmount";
							filedNameArr[14] = "sec_auth_date";
							filedNameArr[15] = "sec_auth_date_end";
							filedNameArr[16] = "sec_auth_visits";
							filedNameArr[17] = "sec_simple_menu";
							
							if(caseType[0] == 1){
								filedNameArr[18] = "sec_reff_id";
								filedNameArr[19] = "sec_ref_phy_id";
								filedNameArr[20] = "sec_ref_phy";
								filedNameArr[21] = "sec_ref_visits";
								filedNameArr[22] = "sec_ref_number";
								filedNameArr[23] = "sec_ref_stDt";
								filedNameArr[24] = "sec_ref_enDt";
							}

							/*if(caseType[1] == 1){
								filedNameArr[11] = "auth_sec_id";
								filedNameArr[12] = "AuthSecNumber";
								filedNameArr[13] = "AuthSecAmount";
								filedNameArr[14] = "sec_auth_date";
								filedNameArr[15] = "sec_simple_menu";
							}*/
							for(i=0;i<secInsData.length;i++){
								var fldObj = dgi(filedNameArr[i]);
								if(fldObj){
									if(fldObj.id == "sec_simple_menu"){
										$('#'+filedNameArr[i]).parent().replaceWith(secInsData[i]);
										//fldObj.innerHTML = secInsData[i];
									}
									else{
										fldObj.value = secInsData[i];
									}
									if(i == 8 && fldObj.value == 'Yes' && caseType[0] == 1){
										dgi("secRefRaqDiv").style.display = 'block';
									}
									/*if(i == 10 && fldObj.value == 'Yes' && caseType[1] == 1){
										dgi("secAuthReqDisDiv").style.display = 'block';
									}*/
								}
							}
							$("#sec_ref_req,#sec_auth_req").trigger('change').selectpicker('refresh');
							
							var secInsSwapData = $insSwapDataArr[1].split("||");	
							var tmpArr = {};var tmpInnArr = {};
							tmpArr['ins_case_id'] = secInsSwapData[0];
							tmpInnArr['insType'] = secInsSwapData[1];
							tmpInnArr['insDataId'] = secInsSwapData[2];
							tmpInnArr['providerId'] = secInsSwapData[3];
							tmpInnArr['providerName'] = secInsSwapData[4];
							tmpArr['insData'] = tmpInnArr;
							arrInsSwapData.push(tmpArr);
							
							if( arrInsSwapData.length > 0 ) {
								var v = writeReArrangeIns(arrInsSwapData);
								$("#rowReArrangeIns").html(v);	
							}
							top.show_loading_image('hide');
						}
					});
				}
			}
				var pat_id = "<?php echo $patient_id; ?>";
				var open_mode = '<?php echo $_REQUEST["mode"]; ?>';
				var source = '<?php echo $source; ?>';
				if(pat_id == ''){
					if(open_mode != "weekly" && source != "demographics"){
						window.opener.top.fmain.close_patient_info(pat_id);
					}
				}
				else{
					var sch_id = "<?php echo $sch_id; ?>";
					if(open_mode != "weekly" && source != "demographics"){
						if(typeof(window.opener)!= 'undefined'){
							if(window.opener.top.fmain.pre_load_front_desk)
								window.opener.top.fmain.pre_load_front_desk(pat_id,sch_id);
						}
					}
				}
			
			function writeReArrangeIns(d){
				var counter = 0;
				var html = '';
				for(var i in d){
					if(i == 0){
						html += '<input type="hidden" id="hidInsCaseId" name="hidInsCaseId" value="'+d[i]['ins_case_id']+'" >';
						html += '<input type="hidden" id="hidSaveInsSwap" name="hidSaveInsSwap">';
					}
					if(d[i]['insData']['insType'] == 'Primary'){
						html += '<input type="hidden" name="compId[]" value="'+d[i]['insData']['providerId']+'" >';
					}
					
					if(d[i]['insData']['insType'] == 'Secondary'){
						html += '<input type="hidden" name="compId[]" value="'+d[i]['insData']['providerId']+'" >';
					}
					
					html += '<div class="row">';
					html += '<div class="col-sm-4">'+d[i]['insData']['insType']+' Ins. </div>';
					html += '<div class="col-sm-4">'+d[i]['insData']['providerName']+'</div>';
					html += '<div class="col-sm-4">';
					
					html += '<div class="radio radio-inline">';
					html += '<input type="radio" name="name_'+d[i]['insData']['insType']+'" '+(d[i]['insData']['insType']=='Primary'?'checked':'')+' value="primary__'+d[i]['insData']['insDataId']+'" onClick="swap_ins(\''+d[i]['insData']['insType']+'\',\'Primary\');" style="cursor:pointer;" id="primaryInsRadio_'+counter+'">';
					html += '<label for="primaryInsRadio_'+counter+'">Primary &nbsp;</label>';
					html += '</div>';
					
					html += '<div class="radio radio-inline">';
					html += '<input type="radio" name="name_'+d[i]['insData']['insType']+'" '+(d[i]['insData']['insType']=='Secondary'?'checked':'')+' value="secondary__'+d[i]['insData']['insDataId']+'" onClick="swap_ins(\''+d[i]['insData']['insType']+'\',\'Secondary\');" style="cursor:pointer;" id="secondaryInsRadio_'+counter+'">';
					html += '<label for="secondaryInsRadio_'+counter+'">Secondary &nbsp;</label>';
					html += '</div>';
					
					html += '</div>';
					
					html += '</div>';
					
					counter++;
				}
				
				return html;
			}
			function get_insurance_details(ins_case_id,pid){
				if(ins_case_id != ""){
					var arr_val = ins_case_id.split("-");
					dgi("current_caseid").value = arr_val[3];
					if(typeof(arr_val[3])=='undefined'){
						$('#accept_assignment_div').html("");
					}
					dgi("insurance_primary_id").value = "";
					dgi("insPriProv").value = "";
					dgi("insPriProv_id").value = "";
					dgi("insPriPolicy").value = "";
					dgi("insPriGroup").value = "";
					dgi("insPriCopay").value = "";
					dgi("insPriCoIns").value = "";
					dgi("insPriActDt").value = "";
					dgi("insPriExpDt").value = "";
					dgi("insurance_secondary_id").value = "";
					dgi("insSecProv").value = "";
					dgi("insSecProv_id").value = "";
					dgi("insSecPolicy").value = "";
					dgi("insSecGroup").value = "";
					dgi("insSecCopay").value = "";
					dgi("insSecActDt").value = "";
					dgi("insSecExpDt").value = "";
					dgi("cbk_self_pay_provider").checked = false;
					
					dgi("insPriProv").disabled = false;
					dgi("anchor_primary_scan").disabled = false;
					dgi("insPriPolicy").disabled = false;
					dgi("insPriGroup").disabled = false;
					dgi("insPriCopay").disabled = false;
					dgi("insPriActDt").disabled = false;
					dgi("insPriExpDt").disabled = false;
					
					
					dgi("insSecProv").disabled = true;
					dgi("anchor_secondary_scan").disabled = true;
					dgi("insSecPolicy").disabled = true;
					dgi("insSecGroup").disabled = true;
					dgi("insSecCopay").disabled = true;
					dgi("insSecActDt").disabled = true;
					dgi("insSecExpDt").disabled = true;
					
					
					if(arr_val.length == 4){
						loadCaseInfo(ins_case_id,pid);
					}
					else{
						var refReqDis = 'none';
						var conInsFld = 'none';
						//var authReqDis = 'none';
						dgi("pri_ref_req").value = 'No';
						dgi("sec_ref_req").value = 'No';
						dgi("pri_auth_req").value = 'No';						
						dgi("sec_auth_req").value = 'No';
						if(arr_val[1] == 1 || arr_val[2] == 1){						
							if(arr_val[1] == 1){
								refReqDis = 'block';
							}
							conInsFld = 'block';
							/*if(arr_val[2] == 1){
								authReqDis = 'block';
							}*/
							
							dgi("Primary_Policy").innerHTML = "Policy "+top.hashOrNo;
							dgi("Primary_Group").innerHTML = "Group "+top.hashOrNo;
							dgi("Primary_Copay").innerHTML = "Copay";
							
							dgi("Secondary_Policy").innerHTML = "Policy "+top.hashOrNo;
							dgi("Secondary_Group").innerHTML = "Group "+top.hashOrNo;
							dgi("Secondary_Copay").innerHTML = "Copay";
	
						}else{
							dgi("Primary_Policy").innerHTML = "Claim";
							dgi("Primary_Group").innerHTML = "Emp. Name";
							dgi("Primary_Copay").innerHTML = "Adj. Name";
							
							dgi("Secondary_Policy").innerHTML = "Claim";
							dgi("Secondary_Group").innerHTML = "Emp. Name";
							dgi("Secondary_Copay").innerHTML = "Adj. Name";
						}
						
						dgi('pri_coins_div').style.display = conInsFld;
						dgi("pri_ref_div").style.display = refReqDis;
						dgi("sec_ref_div").style.display = refReqDis;
						//dgi("pri_auth_div").style.display = authReqDis;
						//dgi("sec_auth_div").style.display = authReqDis;
						dgi("refRaqDiv").style.display = 'none';
						dgi("secRefRaqDiv").style.display = 'none';
					}
				}
			}
			
			function dis_insurance_com(obj){
				var disIns = obj.checked;				
				dgi("insPriProv").disabled = disIns;
				dgi("anchor_primary_scan").disabled = disIns;
				dgi("insPriPolicy").disabled = disIns;
				dgi("insPriGroup").disabled = disIns;
				dgi("insPriCopay").disabled = disIns;
				dgi("insPriActDt").disabled = disIns;
				dgi("insPriExpDt").disabled = disIns;
				
				
				var refVal = dgi("pri_ref_req").value;
				dgi("pri_ref_req").disabled = disIns;
				$("#pri_ref_req").selectpicker('refresh');
				if(refVal == "Yes"){
					dgi("pri_ref_phy").disabled = disIns;
					dgi("pri_ref_visits").disabled = disIns;
					dgi("pri_ref_number").disabled = disIns;
					dgi("pri_reff_id").disabled = disIns;
					dgi("pri_ref_stDt").disabled = disIns;
					dgi("pri_ref_enDt").disabled = disIns;
					dgi("pri_ref_image").disabled = disIns;					
				}
				
				dgi("insSecProv").disabled = disIns;
				dgi("anchor_secondary_scan").disabled = disIns;
				dgi("insSecPolicy").disabled = disIns;
				dgi("insSecGroup").disabled = disIns;
				dgi("insSecCopay").disabled = disIns;
				dgi("insSecActDt").disabled = disIns;
				dgi("insSecExpDt").disabled = disIns;
				dgi("sec_ref_req").disabled = disIns;	
				$("#sec_ref_req").selectpicker('refresh');	
				var secRefVal = dgi("sec_ref_req").value;
				if(secRefVal == 'Yes'){
					dgi("sec_ref_phy").disabled = disIns;
					dgi("sec_ref_visits").disabled = disIns;
					dgi("sec_ref_number").disabled = disIns;
					dgi("sec_reff_id").disabled = disIns;
					dgi("sec_ref_stDt").disabled = disIns;
					dgi("sec_ref_enDt").disabled = disIns;
					dgi("sec_ref_image").disabled = disIns;					
				}
			}
			
			function search_physician_popup(field_name,isModal,searchBy,val){
				var search_criterion = $('#'+field_name+'').val();
				var url = '';
				if(search_criterion != ""){
					url = "../../patient_info/ajax/search_physician.php?type="+field_name+"&btn_sub=y&searchBy=LastName&val="+search_criterion;
				}else{
					top.fAlert('Please enter some text for search');
					//url = "../../patient_info/ajax/search_physician.php?type="+field_name;
                    return false;
				}
				
				if(isModal == 'true'){
					var searchByVal = $('#'+searchBy+'').val();
					var value = $('#'+val+'').val();
					if(value != ''){
						url = "../../patient_info/ajax/search_physician.php?type="+field_name+"&btn_sub=y&searchBy="+searchByVal+"&val="+value;
					}else{
						top.fAlert('Please enter some text for search');
						return false;	
					}
				}
				$.ajax({
					url:url,
					type:'GET',
					success:function(resp){
						var response = $.parseJSON(resp);
						$('#search_physician_result .modal-body').html(response.html);
						if($('#search_physician_result').hasClass('in') === false){
							$('#search_physician_result').modal('show');
						}
						
						$("body").on('click','a[data-click="pick_physician"]',function(e){
							var d = $(this).data();
							var id = d.refId;
							var name = d.name;
							selpidtt(name,id,field_name);
							$("#phy_ajax").val('');
						});
					}
				});
			}
			
			function selpidtt(name,id,type){
				var name = name.replace('&quot;', '"');
				get_phy_name_from_search(name,id,type)
				$("#search_physician_result .modal-body").html('<div class="loader"></div>');
				$("#search_physician_result").modal('hide');
			}
			
			function get_phy_name_from_search(name, id, type){
				if(type == "ref_phy_name"){
					callme(document.getElementById("ref_phy_name"),'form-control');
					document.getElementById("ref_phy_id").value = id;
					document.getElementById("ref_phy_name").value = name.replace(",", ", ");
				}else if(type == "primary_care_name"){
					callme(document.getElementById("primary_care_name"),'form-control');
					document.getElementById("primary_care_phy_id").value = id;
					document.getElementById("primary_care_name").value = name.replace(",", ", ");
				}
				else if(type == "pri_ref_phy"){
					callme(document.getElementById("pri_ref_phy"),'form-control');
					document.getElementById("pri_ref_phy_id").value = id;
					document.getElementById("pri_ref_phy").value = name.replace(",", ", ");
				}
				else if(type == "sec_ref_phy"){
					callme(document.getElementById("sec_ref_phy"),'form-control');
					document.getElementById("sec_ref_phy_id").value = id;
					document.getElementById("sec_ref_phy").value = name.replace(",", ", ");
				}				
			}
			
			function scan_patient_image(){
				var webcam_window = window.open("../../patient_info/demographics/webcam/flash.php",'webcam_window_popup','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=700,height=600,left=150,top=60');		
			}
			
			function image_DIV(imageSrc,div){
				if(imageSrc){
					imageSrc2 = JS_WEB_ROOT_PATH + '/data/'+ practice_dir +'/'+imageSrc;
					pName = imageSrc.replace("/tmp/", "");
					if(div == "ptImage"){
						var nWidth = '100%';
						//var nHeight = 90;
						document.getElementById("ptImageDiv").innerHTML = "<img src="+imageSrc2+" width='"+nWidth+"' >";
						document.getElementById("ptImageDelDiv").style.display = "block";
						document.getElementById("patient_photo_name").value = imageSrc;
						document.getElementById("anchor_patient_photo").disabled = true;
					}
					else if(div == "ptLic"){
						document.getElementById("ptLicImageDiv").innerHTML = "<img src="+imageSrc2+"  width='"+nWidth+"' >";
						document.getElementById("ptLicImageDiv").style.display = "block";
						document.getElementById("ptLicToolBarDiv").style.display = "block";
						document.getElementById("license_image_name").value = imageSrc;
						document.getElementById("anchor_scan_license").disabled = true;
					}
					else if(div == "ptInsCard"){	
						var div_name = div+image_DIV.arguments[2];
						var div_toolbar_name = div_name+"ToolBar";
						var div_large_name = "ptLargeDiv"+image_DIV.arguments[2];
						var large_div_name = "ptLargeDivContainer"+image_DIV.arguments[2];
						var hidden_field_name = "scan_card_"+image_DIV.arguments[2];
						$.ajax({ url: "../../patient_info/demographics/webcam/getSessionPicResized.php?pName="+pName+"&tWidth=35&tHeight=35",success: function(resp){
							var arr_resp = resp.split("~");
							var nWidth = arr_resp[0];
							var nHeight = arr_resp[1];
							document.getElementById(div_name).innerHTML = "<img src=\""+imageSrc2+"\" width=\""+nWidth+"\" height=\""+nHeight+"\">";
							document.getElementById(div_large_name).innerHTML = "<img src=\""+imageSrc2+"\" />";
							document.getElementById(div_name).style.display = "block";
							document.getElementById(div_toolbar_name).style.display = "block";
							document.getElementById(hidden_field_name).value = imageSrc;
						}});
					}
					else if(div == "respLic")
					{
						var tmpArr = imageSrc2.split('/');
						var lKey = tmpArr.length-1;
						tmpArr[lKey] = 'thumbnail/'+tmpArr[lKey];
						var thumbSrc = tmpArr.join('/');
						var html = '<span><img src="'+thumbSrc+'" /><span class="layer" data-toggle="modal" data-target="#resp_party_license"></span></span>';
						dgi("respLicDiv").innerHTML = html;
						dgi('resp_license_image').value = imageSrc;
						$("#resp_party_license .modal-body").html('<img src="'+imageSrc2+'">');
					}
				}
			}
			
			function scan_licence_new(type){
				if( typeof type==='undefined') type = '';
				var url = "../../patient_info/demographics/scan_licence.php" + (type?'?type='+type:'')
				var scan_window = window.open(url,'scan_window_popup','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=650,height=600,left=150,top=60');
			}
			
			function show_license(){
				document.getElementById("ptLicDiv").style.display = "none";
				document.getElementById("ptLicToolBarDiv").style.display = "none";
				document.getElementById("ptLicLargeDivContainer").style.display = "block";
				document.getElementById("anchor_patient_photo").disabled = true;
				document.getElementById("anchor_scan_license").disabled = true;
			}
			
			function hide_license(){
				document.getElementById("ptLicLargeDivContainer").style.display = "none";
				document.getElementById("ptLicDiv").style.display = "block";
				document.getElementById("ptLicToolBarDiv").style.display = "block";
				document.getElementById("anchor_patient_photo").disabled = false;
			}
			var popupWin = new Array();;
			function scan_card(mode,mainId){
				var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/insurance/scan/scan_card.php?';
				
				if(mainId){
					if(mode=="Primary" && document.getElementById('insurance_primary_id').value==""){
						if(document.getElementById("insSecProv").value==""){
							fAlert("First Select The Primary Insurance",'',dgi("insPriProv"));							
							return false;
						}
						get_action('submit_form');						
					}
					if(mode=="Secondary" && document.getElementById('insurance_secondary_id').value==""){
						if(document.getElementById("insPriProv").value==""){
							fAlert("First Select The Secondary Insurance",'',dgi("insSecProv"));
							return false;
						}
						get_action('submit_form');
					}
					
					url += 'type='+mode;
					url += '&isRecordExists='+mainId;
					url += '&cur_case_id=<?php echo $current_caseid; ?>&call_from=scheduler';
					window.opener.top.popup_win(url,'lic','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1');
					
				}else if((mode=="Primary" && document.getElementById("insPriProv").value!="") || (mode=="Secondary" && document.getElementById("insSecProv").value!="")){
						if(document.getElementById('fname').value==''){
							fAlert("Please enter the first name",'',dgi('fname'));
							return false;
						}
						if(document.getElementById('lname').value==''){
							fAlert("Please enter the last name",'',dgi('lname'));
							return false;
						}
						if(document.getElementById('hidd_scan_card_type')){
							document.getElementById('hidd_scan_card_type').value=mode;
						}
						
						get_action('submit_form');
						/*
						url += 'type='+mode;
						url += '&isRecordExists='+mainId;
						url += '&cur_case_id=<?php echo $current_caseid; ?>&call_from=scheduler';
						window.opener.top.popup_win(url,'lic','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1');
						*/
				}else{
					if(mode=="Primary"){
						fAlert("First Select The Primary Insurance",'',dgi("insPriProv"));
					}else if(mode=="Secondary"){
						fAlert("First Select The Secondary Insurance",'',dgi("insSecProv"));
					}
				}
			}
			
			function show_ins_card(mode){
				var div_name = "ptInsCard"+mode;
				var div_toolbar_name = div_name+"ToolBar";
				var large_div_name = "ptLargeDivContainer"+mode;
				var hidden_field_name = "scan_card_"+mode;
				document.getElementById(div_name).style.display = "none";
				document.getElementById(div_toolbar_name).style.display = "none";
				document.getElementById(large_div_name).style.display = "block";
				document.getElementById("anchor_patient_photo").disabled = true;
				document.getElementById("anchor_scan_license").disabled = true;

				document.getElementById("ptLicDiv").style.display = "none";
				document.getElementById("ptLicToolBarDiv").style.display = "none";
			}
			
			function hide_scan_card(mode){
				var div_name = "ptInsCard"+mode;
				var div_toolbar_name = div_name+"ToolBar";
				var large_div_name = "ptLargeDivContainer"+mode;
				var hidden_field_name = "scan_card_"+mode;

				document.getElementById(large_div_name).style.display = "none";
				document.getElementById(div_name).style.display = "block";
				document.getElementById(div_toolbar_name).style.display = "block";
				document.getElementById("anchor_patient_photo").disabled = false;
				document.getElementById("anchor_scan_license").disabled = false;

				if(document.getElementById("license_image_name").value != ""){
					document.getElementById("ptLicDiv").style.display = "block";
					document.getElementById("ptLicToolBarDiv").style.display = "block";
				}
			}
			
			function delete_scan_card(mode){
				var div_name = "ptInsCard"+mode;
				var div_toolbar_name = div_name+"ToolBar";
				var large_div_name = "ptLargeDivContainer"+mode;
				var hidden_field_name = "scan_card_"+mode;

				document.getElementById(div_toolbar_name).style.display = "none";
				var del_path = document.getElementById(hidden_field_name).value;
				if(del_path != ""){
					del_path = del_path.replace("/temp/", "");
					$.ajax({ url: "../../patient_info/demographics/webcam/delSessionImg.php?del_path="+del_path, success: function(){
						document.getElementById(div_name).innerHTML = "";
						document.getElementById(div_name).style.display = "none";
						document.getElementById(hidden_field_name).value = "";
					}});
				}
			}
			
			function enable_insurance_options(mode){
				var secDis = true;
				if(mode == "Primary" && document.getElementById("insPriProv").value != ""){
					secDis = false;					
				}
				dgi("insSecProv").disabled = secDis;
				dgi("anchor_secondary_scan").disabled = secDis;
				dgi("insSecPolicy").disabled = secDis;
				dgi("insSecGroup").disabled = secDis;
				dgi("insSecCopay").disabled = secDis;
				dgi("sec_ref_req").disabled = secDis;
				dgi("insSecActDt").disabled = secDis;
				dgi("insSecExpDt").disabled = secDis;
				dgi("sec_ref_phy").disabled = secDis;
				dgi("sec_ref_visits").disabled = secDis;
				dgi("sec_ref_number").disabled = secDis;
			}
			
			function setInsuranceAutoFill(strString,field_id){
				strString =  (typeof strString == 'undefined') ? '' : strString;
				if(strString != ""){
					strArray = strString.split(" * ");
					if(strArray[1] != ''){
						dgi(field_id).value = strArray[0];
						var hid_obj = dgi(field_id+'_id');
						hid_obj.value = strArray[1];
					
						var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/ajax/insurance/get_co_ins.php";
						$.post(url,{'ins_id':strArray[1]}).done(function(r){
							if(typeof(r) != 'undefined') {
								if( $('#insPriCoIns').length > 0 ) {
									$('#insPriCoIns').val(r);
								}
							}
						});
					}
				}
			}
			
			function delete_photo(){
				document.getElementById("ptImageDelDiv").style.display = "none";
				var del_path = document.getElementById("patient_photo_name").value;
				if(del_path != ""){
					del_path = del_path.replace("/temp/", "");
					$.ajax({ url: top.JS_WEB_ROOT_PATH + "/interface/patient_info/demographics/webcam/delSessionImg.php?del_path="+del_path, success: function(){
						document.getElementById("ptImageDiv").innerHTML = "<img src='../../../library/images/no_image_found.png' />";
						document.getElementById("patient_photo_name").value = "";
						document.getElementById("anchor_patient_photo").disabled = false;
					}});
				}
			}
			
			function add_lang_code(_this)
			{
				var code = $(_this).find('option:selected').data('code');
				if( code ) $("#lang_code").val(code);
			}
			
			
			function fill_amt(obj,id,item_name){
				var item_charges = "item_charges_"+id;
				var item_pay = "item_pay_"+id;
				var total_amt = '';
				if(item_name=='Refraction'){
					if(document.getElementById('refraction_Chk')){
						if(document.getElementById('refraction_Chk').value=='No'){
							fAlert("Refraction can not be collected.");
							document.getElementById(item_pay).value='0.00';
							obj.checked=false;
							return false;
						}
					}
				}
				if(obj.checked == true){
					if(item_name=='Copay-visit'){
						if(document.getElementById('copay_dilated')){
							document.getElementById('copay_dilated').checked=true;
							total_amt = document.getElementById('copay_dilated_tb').value;
							document.getElementById(item_charges).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_dilated_tb').value;
						}else {
							if(document.getElementById('copay_dilated_tb')){
								total_amt = document.getElementById('copay_dilated_tb').value;
								document.getElementById(item_charges).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_dilated_tb').value;
							}
						}
					}
					if(item_name=='Copay'){
						if(document.getElementById('copay_dilated')){
							document.getElementById('copay_dilated').checked=true;
							total_amt = document.getElementById('copay_dilated_tb').value;
							document.getElementById(item_charges).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_dilated_tb').value;
						}else {
							if(document.getElementById('copay_dilated_tb')){
								total_amt = document.getElementById('copay_dilated_tb').value;
								document.getElementById(item_charges).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_dilated_tb').value;
							}
						}
					}
					if(item_name=='Copay-test'){
						if(document.getElementById('copay_test_dilated')){
							if(document.getElementById('copay_test_non_dilated')){
								document.getElementById('copay_test_dilated').checked=true;
								total_amt = document.getElementById('copay_test_dilated_tb').value;
								document.getElementById(item_charges).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_test_dilated_tb').value;
							}else{
								if(document.getElementById('copay_test_non_dilated_tb')){
									total_amt = document.getElementById('copay_test_non_dilated_tb').value;
									document.getElementById(item_charges).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_test_non_dilated_tb').value;
								}
							}
						}else{
							if(document.getElementById('copay_test_non_dilated_tb')){
								total_amt = document.getElementById('copay_test_non_dilated_tb').value;
								document.getElementById(item_charges).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_test_non_dilated_tb').value;
							}
						}
					}else{
						var chr_amt  = trim(document.getElementById(item_charges).value);
						if(chr_amt != '-'){
							var currency_val=$.trim("<?php echo htmlspecialchars_decode(show_currency()); ?>");
							currency_val=currency_val.replace("&nbsp;","");
							total_amt = $.trim(chr_amt.replace(currency_val,""));
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
			
			
			function checkValidPayMethod(cc_card){
                var no_pos_device=false;
                if($('#tsys_device_url').val()=='no_pos_device') {
                    no_pos_device=true;
                }
				if(showViewPaymentsRow == 'block'){
					var CCselected = CHQselected = EFTselected = MOselected = false;
					for(i=1;i<13;i++){
						chkbox = "chkbox_"+i;
						item_pay = "item_pay_"+i;
						pay_method_dd = "pay_method_"+i;
						if($('#'+chkbox).is(':checked')){
							item_amt  = parseFloat($('#'+item_pay).val());
							pay_dd_val = $('#'+pay_method_dd).val();
							if(item_amt > 0 && pay_dd_val==''){
								fAlert("Payment method not selected.",'',$('#'+pay_method_dd));
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
						fAlert("Please enter Check/EFT/MO number.",'',$('#checkNo'));
						return false;
					}
                    if(cc_card && (!pos_device || no_pos_device==true) ){
                        if(CCselected && ccCOMP==''){
                            fAlert("Please select credit card type.",'',$('#creditCardCo'));
                            return false;
                        }
                        if(CCselected && ccNO==''){
                            fAlert("Please enter credit card number.",'',$('#cCNo'));
                            return false;
                        }
                        if(CCselected && ccEXP==''){
                            fAlert("Please enter credit card expiry date.",'',$('#date2'));
                            return false;
                        }
                    }
				}
				return true;
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
				document.getElementById('tot_payment').innerHTML = "<?php echo htmlspecialchars_decode(show_currency()); ?>"+tdVal.toFixed(2);
				document.getElementById('tot_payment_txt').value = tdVal.toFixed(2);
				tot_charges_fun();
			}
			function tot_charges_fun(op){
				op = op || "";
				var tdVal_charges=0;
				for(i=1;i<13;i++){
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
				if(document.getElementById('tot_charges')){
					document.getElementById('tot_charges').innerHTML = "<?php echo htmlspecialchars_decode(show_currency()); ?>"+tdVal_charges.toFixed(2);
				}
				if(op == "1"){
					/* var RteCopayMismatchAlert = ('{/literal}{$RTE_COPAY_MISMATCH_ALERT}{literal}') ? '{/literal}{$RTE_COPAY_MISMATCH_ALERT}{literal}' : "";
					if(RteCopayMismatchAlert == 1){
						fAlert("Insurance Co-Pay is Mismatch from Real Time Eligibility Co-Pay!");
					} */
				}
			}
			function sel_chkbox(obj,id,item_name){
				var chkbox="chkbox_"+id;
				var item_pay_name = "item_pay_"+id;
				if(item_name=='Refraction'){
					if(document.getElementById('refraction_Chk')){
						if(document.getElementById('refraction_Chk').value=='No'){
							fAlert("Refraction can not be collected.");
							document.getElementById(item_pay_name).value='0.00';
							chkbox.checked=false;
							return false;
						}
					}
				}
				var amt = document.getElementById(item_pay_name).value;
				amt = amt.replace(",","");
				amt = amt.replace("<?php echo htmlspecialchars_decode(show_currency()); ?>","");
				if(amt > 0){
					document.getElementById(chkbox).checked = true;
				}
			}
			
			function showRow11(){
				var show = document.getElementById("payment_method").value;
				switch(show){
					case "Cash":
						document.getElementById("checkRow").style.display = "none";
						document.getElementById("creditCardRow").style.display = "none";
					break;
					case "Check":
						document.getElementById("checkRow").style.display = "block";
						document.getElementById("creditCardRow").style.display="none";
					break;
					case "Credit Card":
						document.getElementById("creditCardRow").style.display = "block";
						document.getElementById("checkRow").style.display="none";
					break;
					case "EFT":
						document.getElementById("checkRow").style.display = "block";
						document.getElementById("creditCardRow").style.display="none";
					break;
					case "Money Order":
						document.getElementById("checkRow").style.display = "block";
						document.getElementById("creditCardRow").style.display="none";
					break;
				}
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
								fAlert("Please enter date in forrmat mm/yy")
								document.getElementById("date2").value = '';
							}else{
								yySess = expireDate.substr(3, 2);
								if(yySess!=20){
									fAlert("Please enter year correctly.")
									document.getElementById("date2").value = '';
								}
							}
						}else{
							fAlert("Please enter date in forrmat mm/yy")
							document.getElementById("date2").value = '';
						}
					}
				}
			}
						
			
			
			function check_patient(title, fname, lname){				
				if(fname != "" && lname != ""){
					top.show_loading_image('show');
					$.ajax({ url: "scheduler_check_patientinfo.php?pname="+fname+"&lastname="+lname+"&title="+title, success: function(resPoi){
						var resPoiArr = resPoi.split("<SHOWME>");
						if(resPoiArr[0]!="<No>"){
							$('body').append(resPoiArr[1]);
							//document.getElementById("ptMatchFoundHeading").innerHTML = "Patient(s) with similar name found";
							$('#scheduler_pt_search').modal('show');
							//document.getElementById("ptMatchFound").style.display = "block";
							document.getElementById("anchor_patient_photo").disabled = true;
							document.getElementById("anchor_scan_license").disabled = true;
							document.getElementById("ptLicToolBarDiv").style.display = "none";
						}
						top.show_loading_image('hide');					
					}});
				}
			}
			
			function selpid(pid){
				if(source == "scheduler"){
					if(window.opener.top.fmain.pre_load_front_desk)
						window.opener.top.fmain.pre_load_front_desk(pid, "");
				}else{
					var curr_tab = window.opener.top.document.getElementById("curr_main_tab").value;
					var redirect_url = window.opener.top.core_get_tab_path(curr_tab);
					var redirect_obj_to_str = new Array();
					$.each(redirect_url,function(id,val){
						if(val != ''){
							redirect_obj_to_str.push(val);
						}
					});
					window.opener.top.core_set_pt_session(window.opener.top.fmain, pid, redirect_obj_to_str[0]);
					window.opener.top.document.getElementById("findBy").value = "Active";
					window.opener.top.document.getElementById("findByShow").value = "Active";
				}
				window.close();
			}
			
			
			function check_dob(dob){
				if(dob != ""){
					document.getElementById("divAjaxLoader").style.display = "block";
					$.ajax({ url: "scheduler_match_dob.php?dob="+dob, success: function(resPoi){
						var resPoiArr = resPoi.split("<SHOWME>");
						if(resPoiArr[0]!="<No>"){
							document.getElementById("ptMatchFoundHeading").innerHTML = "Patient(s) with same Date of Birth";
							document.getElementById("ptMatchFoundResult").innerHTML = resPoiArr[1];
							document.getElementById("ptMatchFound").style.display = "block";
							
							document.getElementById("anchor_patient_photo").disabled = true;
							document.getElementById("anchor_scan_license").disabled = true;

							document.getElementById("ptLicDiv").style.display = "none";
							document.getElementById("ptLicToolBarDiv").style.display = "none";
						}
						top.show_loading_image('hide');	
					}});
				}
			}
			
			function hide_pt_found(){
				document.getElementById("ptMatchFoundHeading").innerHTML = "";
				document.getElementById("ptMatchFoundResult").innerHTML = "";				
				document.getElementById("ptMatchFound").style.display = "none";				
				document.getElementById("anchor_patient_photo").disabled = false;
				document.getElementById("anchor_scan_license").disabled = false;
				if(document.getElementById("license_image_name").value != ""){
					document.getElementById("ptLicDiv").style.display = "block";
					document.getElementById("ptLicToolBarDiv").style.display = "block";
				}
			}
			/*
			function show_scanned(id,val,type){
				window.open('../../patient_info/insurance/show_scan_img.php?id='+id+'&val='+val+'&type='+type,'scan','');
			}*/
			function show_scanned(_this){
				var s = $(_this).data('src');
				var modal_popup = parseInt($("#show_ins_scan_in_modal").val());
				if( modal_popup ) {
					var h = $(_this).attr('title');
					var i = '<img src="'+s+'" title="'+h+'" />';
					$("#scan_card_show").find('h4#modal_title').html(h);
					$("#scan_card_show").find('.modal-body').html(i).addClass('text-center');
					$("#scan_card_show").fadeIn('fast');
				}
				else {
					window.opener.top.popup_win(s,'location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width=800,height=700');
				}
			}
			function show_dl(_this){
				var s = $(_this).data('src');
				var h = $(_this).attr('title');
				var i = '<img src="'+s+'" title="'+h+'" />';
				
				$("#imageLicense").find('h4#modal_title').html(h);
				$("#imageLicense").find('.modal-body').html(i).addClass('text-center');
				$("#imageLicense").modal('show');
			}
			function set_auth_info(obj,send_val,send_val2,send_val3){
				var caseType = document.getElementById('choose_prevcase').value;
				var case_arr= caseType.split("-");
				var case_len=case_arr.length;
				var case_final=case_arr[case_len-1];
				var ins_case_id = trim(case_final);
				var auth_number_chk=obj.value;
				var url="new_patient_info_popup_new.php?accounting_auth=yes&auth_number="+auth_number_chk+"&ins_case_id="+ins_case_id;
				$.ajax({
					url:url,
					type:'GET',
					success:function(response){
						result=response;
						var all_result=result.split("~~~");
						dgi(send_val).value = all_result[0];
						dgi(send_val2).value = all_result[1];
						dgi(send_val3).value = all_result[2];
					}
				});
			}
			
			function getFocusObj(obj){
				var objId = obj.id;
				if(document.getElementById(objId)){
					var str = document.getElementById(objId).value;
					setCaretPosition(objId, str.length);
				}
			}
			
			function setCaretPosition(elemId, caretPos) {
				var elem = document.getElementById(elemId);
				if(elem != null) {
					if(elem.createTextRange) {
						var range = elem.createTextRange();
						range.move('character', caretPos);
						range.select();
					}else {
						if(elem.selectionStart) {
							elem.focus();
							elem.setSelectionRange(caretPos, caretPos);
						}else
							elem.focus();
					}
				}
			}
			
			
			
			function showMessage() {
				if (document.getElementById("dob").value == "") {
					top.fAlert ("Invalid date format - please re-enter as "+date_global_format+"");
					//document.getElementById("dob").value = "";
					document.getElementById("patient_age").innerHTML = "0";
					document.getElementById("patient_age_month").innerHTML = "0";
					//document.getElementById("dob").focus();
				}
			}
			
			function DaysInMonth(Y, M) {
				with (new Date(Y, M, 1, 12)) {
					setDate(0);
					return getDate();
				}
			}

			function daysInMonthNew(iMonth, iYear){
				return 32 - new Date(iYear, iMonth, 32).getDate();
			}
			
			
			
			function refReqChk(refObj){
				var refDis = 'none';
				var refDisb = true;
				/*dgi("refRaqDiv").style.display = 'none';
				dgi("secRefRaqDiv").style.display = 'none';*/
				if(refObj.value == 'Yes'){
					refDis = 'block';
					refDisb = false;
				}
				if(refObj.id == 'pri_ref_req'){
					dgi("refRaqDiv").style.display = refDis;
					if(refDisb)
						$("#pri_reff_id, #pri_ref_phy_id,#pri_ref_phy,#pri_ref_visits,#pri_ref_number,#pri_ref_stDt,#pri_ref_enDt").val('');
				}
				else if(refObj.id == 'sec_ref_req'){
					if(refDisb)
						$("#sec_reff_id, #sec_ref_phy_id,#sec_ref_phy,#sec_ref_visits,#sec_ref_number,#sec_ref_stDt,#sec_ref_enDt").val('');
					dgi("secRefRaqDiv").style.display = refDis;
					dgi("sec_ref_phy").disabled = refDisb;
					dgi("sec_ref_visits").disabled = refDisb;
					dgi("sec_ref_number").disabled = refDisb;
					
				}
			}
			
			function authReqChk(authObj){
				var authDis = 'none';
				var authDisb = true;
				/*dgi("authReqDisDiv").style.display = 'none';
				dgi("secAuthReqDisDiv").style.display = 'none';*/
				if(authObj.value == 'Yes'){
					authDis = 'block';
					authDisb = false;
				}
				if(authObj.id == 'pri_auth_req'){
					dgi("authReqDisDiv").style.display = authDis;
					if( authDisb )
						$("#auth_pri_id, #AuthPriNumber,#AuthPriAmount,#pri_auth_date,#pri_auth_date_end,#pri_auth_visits").val('');
				}
				if(authObj.id == 'sec_auth_req'){
					if( authDisb )
						$("#auth_sec_id, #AuthSecNumber,#AuthSecAmount,#sec_auth_date,#sec_auth_date_end,#sec_auth_visits").val('');
					dgi("secAuthReqDisDiv").style.display = authDis;
					dgi("AuthSecNumber").disabled = authDisb;
					dgi("AuthSecAmount").disabled = authDisb;
					dgi("sec_auth_date").disabled = authDisb;
					dgi("sec_auth_date_end").disabled = authDisb;
					dgi("sec_auth_visits").disabled = authDisb;
					
				
				}
			}
			function sel_notes_chk(obj){
				if(obj.value!=""){
					dgi("chkNotesScheduler").checked = true;
					dgi("chkNotesChartNotes").checked = true;
					dgi("chkNotesAccounting").checked = true;
				}
			}

	//  ---------- Compnay Name Popup window code----
	var t;
	
	function closeInsDiv(){
		t = setTimeout('close_div()',200);
	}
	
	function close_div(){
		document.getElementById("insPrimaryPop").style.display = 'none';
		if(typeof(document.getElementById("insSecondaryPop"))!="undefined") {
			document.getElementById("insSecondaryPop").style.display = 'none';
		}
		
	}
	
	function cleartimeout(){
		clearTimeout(t);
	}

	function closeWindow(div_id){	
		$('#'+div_id+'').popover('hide');
	}



//---      -------------- --
var changeFlag=false;

	function chkChange(olddata,newData,e){
		e = event
		characterCode = e.keyCode
		if(characterCode!=9 && characterCode!=16 ){
			if(olddata!=newData){
				changeFlag=true;
			}else{
				if(changeFlag!=true){
					changeFlag=false;
				}
			}
		}	
	}
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
				document.getElementById(item_charges_chk).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_dilated_tb').value;
				document.getElementById('copay_non_dilated').checked=false;
				document.getElementById(chkbox).checked=true;
			}else{
				document.getElementById(item_pay_chk).value=document.getElementById('copay_non_dilated_tb').value;
				document.getElementById(item_charges_chk).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_non_dilated_tb').value;
				document.getElementById('copay_dilated').checked=false;
				document.getElementById('copay_non_dilated').checked=true;
				document.getElementById(chkbox).checked=true;
			}
		}else{
			if(document.getElementById('copay_non_dilated').checked==true){
				document.getElementById(item_pay_chk).value=document.getElementById('copay_non_dilated_tb').value;
				document.getElementById(item_charges_chk).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_non_dilated_tb').value;
				document.getElementById('copay_dilated').checked=false;
				document.getElementById(chkbox).checked=true;
			}else{
				document.getElementById(item_pay_chk).value=document.getElementById('copay_dilated_tb').value;
				document.getElementById(item_charges_chk).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_dilated_tb').value;
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
				document.getElementById(item_charges_chk).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_test_dilated_tb').value;
				document.getElementById('copay_test_non_dilated').checked=false;
				document.getElementById(chkbox).checked=true;
			}else{
				document.getElementById(item_pay_chk).value=document.getElementById('copay_test_non_dilated_tb').value;
				document.getElementById(item_charges_chk).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_test_non_dilated_tb').value;
				document.getElementById('copay_test_dilated').checked=false;
				document.getElementById('copay_test_non_dilated').checked=true;
				document.getElementById(chkbox).checked=true;
			}
		}else{
			if(document.getElementById('copay_test_non_dilated').checked==true){
				document.getElementById(item_pay_chk).value=document.getElementById('copay_test_non_dilated_tb').value;
				document.getElementById(item_charges_chk).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_test_non_dilated_tb').value;
				document.getElementById('copay_test_dilated').checked=false;
				document.getElementById(chkbox).checked=true;
			}else{
				document.getElementById(item_pay_chk).value=document.getElementById('copay_test_dilated_tb').value;
				document.getElementById(item_charges_chk).value="<?php echo htmlspecialchars_decode(show_currency()); ?>"+document.getElementById('copay_test_dilated_tb').value;
				document.getElementById('copay_test_non_dilated').checked=false;
				document.getElementById('copay_test_dilated').checked=true;
				document.getElementById(chkbox).checked=true;
			}
		}
	}
	
	function changeBackImage(obj,val){
		var cur_tab = document.getElementById("curr_tab").value;
		if(cur_tab != obj.id){
			var clasName = val == false ? 'hovertab' : 'normaltab';
			
			obj.className = clasName;
		}
	}
	
	function consentTab(){
		dgi('after_save_url').value = 'consentTab';
		var a = validate_input(1);
	}

	function changeClassCombo(){
		var arguments = changeClassCombo.arguments;				
		if(arguments[0].value!="" && arguments[0].className=="mandatory"){
			arguments[0].className="input_text_10";
		}
		else if(arguments[0].value=="" && arguments[1]=="mandatory"){
			arguments[0].className="mandatory";
		}
	}
	
	
	
	function swap_ins(val,type){
		if(val == 'Primary'){
			document.frmRearrangeIns.name_Secondary[0].checked = false;
			document.frmRearrangeIns.name_Secondary[1].checked = false;
			if(type == 'Primary'){			
				document.frmRearrangeIns.name_Secondary[1].checked = true;
			}
			else if(type == 'Secondary'){
				document.frmRearrangeIns.name_Secondary[0].checked = true;
			}
		}
		else if(val == 'Secondary'){
			document.frmRearrangeIns.name_Primary[0].checked = false;
			document.frmRearrangeIns.name_Primary[1].checked = false;
			if(type == 'Primary'){
				document.frmRearrangeIns.name_Primary[1].checked = true;;
			}
			else if(type == 'Secondary'){
				document.frmRearrangeIns.name_Primary[0].checked = true;;
			}
		}
	}
	
	function saveSwap(){
		top.show_loading_image('show');
		//dgi("divAjaxLoader").style.display = 'inline';
		dgi("hidSaveInsSwap").value = "1";
		document.frmRearrangeIns.submit();
	}
	
	
	function getToolTip(id,objBoxName,providerRCOId){
		providerRCOId = providerRCOId || "";
		if(id){
			var url=webroot+"/interface/patient_info/ajax/insurance/insurance_result.php?id="+id+"&providerRCOId="+providerRCOId;
			$.ajax({
				url:url,
				type:'GET',
				success:function(resp){
					if(resp != ''){
						var html = '<div class="row">';
						var response = $.parseJSON(resp);
						if(response != ''){
							$.each(response,function(id,val){
								html += '<div class="col-sm-4"><strong>'+id+'</strong></div>';
								html += '<div class="col-sm-8"><p class="">'+val+'</p></div>';
							});
							html += '<div>';
							
							$('#'+objBoxName+'').popover({html: true,trigger: 'hover',placement:'top'});
							$('#'+objBoxName+'').data('bs.popover').options.content = html;
							$('#'+objBoxName+'').popover('show');
						}
					}
				}
			});
		}
	}

	function stateChanged(){ 
		if(xmlHttp.readyState==4){ 
			document.getElementById("insPrimaryPop").innerHTML=xmlHttp.responseText;
			document.getElementById("insPrimaryPop").style.display = "block";
		}
		else{ 
			document.getElementById("insPrimaryPop").innerHTML="Wait";			
			document.getElementById("insPrimaryPop").style.display = "block";
		}
	}	
	
	
	function get271Report(id){
			var h = '<?php echo $REPORT_WINDOW; ?>';
			window.open('../../patient_info/eligibility/eligibility_report.php?id='+id,'eligibility_report','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
	}
	
	function refine_data(obj, field){
			var data = obj.value;
			data = data.replace('&quot;','"');
			obj.value = data;
			
			if( obj.hasAttribute('data-content') ) {
				if( obj.value == '' ) {
					obj.setAttribute('data-content','');
					$(obj).popover('destroy');
				}
			}

			if(field == 'refphy'){
				if($('#pre_ref_phy_name').val() != $('#ref_phy_name').val()){
					if($('#ref_phy_id').val() == $('#pre_ref_phy_id').val()){
						$('#ref_phy_id').val('');
					}
				}
			}else if(field == 'pcp'){
				if($('#primary_care_name').val() != $('#pre_pcp_name').val()){
					if($('#primary_care_phy_id').val() == $('#pre_pcp_id').val()){
						$('#primary_care_phy_id').val('');
					}
				}
			}
		}
		
		function func_unload(){
			if(typeof(popupWin)!='undefined'){
				if(popupWin.length){
					for(i=0; i<=popupWin.length; i++){
						popupWin[i].close();
					}
				}
			}
		}
		function makeNullProviderId(obj){
			if(obj.id == "insPriProv"){
				document.getElementById('insPriProv_id').value = '';		
			}
			else if(obj.id == "insSecProv"){
				document.getElementById('insSecProv_id').value = '';
			}
		}
function check_ins_exist(id, ins_type){
		switch(ins_type){
			case 'primary':
				var ins_provider_id = $('#insPriProv_id').val();
				if(!(id=="" && (ins_provider_id == "" || ins_provider_id == "undefined"))){
					if(id.indexOf('*') == '-1'){
						if(ins_provider_id == "" || ins_provider_id == "undefined"){
							$('#insPriProv').val('');
							fAlert("Please Select Primary Insurance provider From Drop Down.",'',$('#insPriProv'));
							return false;
						}
						else{
							return true;
						}	
					}
					else{
						return true;
					}
				}
				else{
					return true;
				}
				break;
			
			case 'secondary':
				var ins_provider_id = $('#insSecProv_id').val();
				if(!(id=="" && (ins_provider_id == "" || ins_provider_id == "undefined"))){
					if(id.indexOf('*') == '-1'){
						if(ins_provider_id == "" || ins_provider_id == "undefined"){
							$('#insSecProv').val('');
							fAlert("Please Select Secondary Insurance provider From Drop Down.",'',$('#insSecProv'));
							return false;
						}	
						else{
							return true;
						}
					}
					else{
						return true;
					}
				}
				else{
					return true;
				}
				break;		
			}
		
	
}
var arr_cnt='<?php echo $count; ?>';
var lang_key_arr='<?php echo $lang_arr_key; ?>';
var lang_code_arr='<?php echo $js_arr_lang; ?>';
lang_code_arr=$.parseJSON(lang_code_arr);
lang_key_arr=$.parseJSON(lang_key_arr);
function pt_languages_typehead(obj,obj2,eve){
	x=$("#otherLanguage").offset().left;
	y=$("#otherLanguage").offset().top;
	y=parseInt(y+20);
	
	var txtValLen=$(obj).val().length;
	var txtVal=$(obj).val();
	var htmlTypeHead="";
	$('#pt_lang_type').html("");
	if(txtValLen==3){
		var srchTxtVal=txtVal.toLowerCase();
		t=0;
		htmlTypeHead+="<div id='tp_head'><select id='select_tyhead' onKeyPress='$(\"#otherLanguage\").val(this.value);this.style.display=\"none\"' onClick='$(\"#otherLanguage\").val(this.value);this.style.display=\"none\"'  multiple style='position:relative;overflow:auto; height:60px;'>";
		for(i=0;i<arr_cnt;i++){
			if(lang_code_arr[i][srchTxtVal]){
				t++;
				htmlTypeHead+="<option value='"+lang_code_arr[i][srchTxtVal]+"'>"+lang_code_arr[i][srchTxtVal]+"</option>";
			}
		}
		htmlTypeHead+"</select></div>";
		if(t>0){
		var divH=parseInt(t*17);
		$('#pt_lang_type').html("");
		$('#pt_lang_type').html(htmlTypeHead);
			if(eve.keyCode==40){
				$('#select_tyhead').focus();
				$('#select_tyhead')[0].selectedIndex=0;
			}
			
			$("#tp_head").css({left:x,top:y,height:divH});
			$("#select_tyhead").css({height:divH});
			$("#pt_lang_type").css({left:x,top:y});
		}
	}else if(txtValLen>=4){
		var str=txtVal.toLowerCase();
		var userTxtVal=str.substr(0,txtValLen);
		htmlTypeHead+="<div id='tp_head'><select id='select_tyhead' multiple onKeyPress='$(\"#otherLanguage\").val(this.value);this.style.display=\"none\"' onClick='$(\"#otherLanguage\").val(this.value);this.style.display=\"none\"' style='overflow:hidden; height:40px;'>";
		j=0;
		for(i=0;i<arr_cnt;i++){
			var arr_val=lang_key_arr[i].toLowerCase();
			var arr_val_ins=lang_key_arr[i];
			var arr_txt_val=arr_val.substr(0,txtValLen)
			if(userTxtVal==arr_txt_val){
				j++;
				htmlTypeHead+="<option value='"+arr_val_ins+"'>"+arr_val_ins+"</option>";
			}
		}
		htmlTypeHead+"</select></div>";
		if(j>0){
			var divH=parseInt(j*17);
			$('#pt_lang_type').html("");
			$('#pt_lang_type').html(htmlTypeHead);
			if(eve.keyCode==40){
				$('#select_tyhead').focus();
				$('#select_tyhead')[0].selectedIndex=0;
			}
			$("#tp_head").css({left:x,top:y});
			$("#select_tyhead").css({height:divH});
			$("#pt_lang_type").css({left:x,top:y});
		}
	}
	
}
function showOtherTxtBoxEthnacity(obj1,obj2,obj3,con4,obj4){
	var obj1Id = obj1;
	var obj2Id = obj2;
	var obj3Id = obj3;
	var obj4Id = obj4;
	if(con4=='y'){
		document.getElementById(obj1Id).style.display = 'none';
		if(obj4Id){document.getElementById(obj4Id).style.display = 'none';}
	}
	if(document.getElementById(obj2Id))document.getElementById(obj2Id).style.display = 'block';
	if(document.getElementById(obj3Id))document.getElementById(obj3Id).style.display = 'block';		
}

function get_accept_assignment(){
	//"AA – Courtesy Billing"
	//"NAA - Courtesy Billing"
	//"NAA - No Courtesy Billing"
	var priInsId = $('#insPriProv_id').val();
	$('#accept_assignment_div').html("AA");
	$('#accept_assignment_div').attr('title', 'Accept Assignment');
	if(priInsId>0){
		$.ajax({
			url: "../../patient_info/insurance/get_ins_claim.php?insCompanyId="+priInsId,
			success: function(resp){
				if(resp){
					var val_arr = resp.split("~~~");
                    if(val_arr[1]!=''){//medicare
                        $('#pri_accept_assignment').val(val_arr[1]);
                    }
					if(val_arr[1]==1){
						 $('#accept_assignment_div').html("NAA - CB");
						 $('#accept_assignment_div').attr('title', 'NAA - Courtesy Billing');
					}else if(val_arr[1]==2){
						 $('#accept_assignment_div').html("NAA - No CB");
						 $('#accept_assignment_div').attr('title', 'NAA - No Courtesy Billing');
					}
				}			
			}
		});
	}
}
	</script>
	</head>
	<body onLoad="tot_charges_fun('1');" onbeforeunload="func_unload();" >
		<div style="position:absolute; top:30%; left:40%; display:none; z-index: 9;" id="divAjaxLoader" class="process_loader"></div>
		
		<div id="div1"></div>
		
		<div id="divMultiRefPhy" class="section mt10 m5" style="display:none; position:absolute; left:840px; top:120px; height:330px; width:250px;" onMouseDown="drag_move_div(this, event);"></div>
        
		<div id="divMultiPCPDemo" class="section mt10 m5" style="display:none; position:absolute; left:840px; top:120px; height:330px; width:250px;" onMouseDown="drag_move_div(this, event);"></div>
        
		<div id="divMultiCoPhy" class="section mt10 m5" style="display:none; position:absolute; left:840px; top:120px; height:330px; width:250px;" onMouseDown="drag_move_div(this, event);"></div>
	
		<span id="ref_pcp_coman_details_span" class="div_popup white border padd5 hide" style="height:300px; width:245px; overflow-x:hidden; overflow-y:auto;">
			<span class="closeBtn" onClick="hideDetail();"></span>
			<span id="ref_pcp_coman_details_span_inner"></span>
		</span>
		
		<div id="ptMatchFound" style="z-index:3;border:1px solid #4684AB;background-color:#ffffff;position:absolute;top:32px;left:0px;display:none;">
			<div id="ptMatchFoundHeader" style="width:845px;height:20px; padding-top:5px;" class="section_header">
            	<span class="closeBtn mr10" onClick="hide_pt_found();"></span>
				<span id="ptMatchFoundHeading" class="text12b" style="margin-right:475px;">Patient with similar name found.</span>
			</div>
			<div id="ptMatchFoundResult">
				
			</div>
            <div style="margin-bottom:10px; text-align:center;">
            	<input type="button" id="div_close" name="div_close" value="Close" class="dff_button" onClick="hide_pt_found();">
            </div>
		</div>
		
		<div id="divCommonAlertMsg1" style="display:none;"></div>
		<form name="demographics_edit_form" id="demographics_edit_form" action="new_patient_info_popup_new.php?view_result=yes" method="post" onSubmit="return do_total()" enctype="multipart/form-data">
		<div class="mainwhtbox pd10" id="mainWhtbox">
			<div class="row">
				<!-- Tabs -->
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-5">
							<ul class="nav nav-tabs" style="border-bottom:none">
								<li class="active lead"><a href="javascript:void[0]"><?php if($pat_val == 'New'){echo "New Patient";}else{echo "Check In";} ?></a></li>
								<li class="lead" onClick="consentTab();"><a href="#">Consent Forms</a></li>
							</ul>
						</div>	
						<div class="col-sm-7">
							<?php
								$edit_patient_id = $patientQryRes[0]['pid'];
								if($edit_patient_id != ''){
									$last_name = $patientQryRes[0]['lname'];
									$first_name = $patientQryRes[0]['fname'];
									echo '<p class="lead">'.$last_name.', '.$first_name.' - '.$edit_patient_id.'</p>';
								}
							?>
						</div>	
					</div>	
				</div>	
				
				<!-- Content -->
        <!-- style="overflow:auto; overflow-x:hidden; height:<?php echo $popheight_body_div-190 ?>px" -->
				<div id="body_div" class="col-sm-12">
					<div id="copy_ins_data"></div>	
					<?php
						$edit_patient_id = $patientQryRes[0]['pid'];
					?>	
					<div class="row">
						
							<input type="hidden" name="curr_tab" id="curr_tab" value="Demographics">
							<input type="hidden" name="edit_patient_id" id="edit_patient_id" value="<?php echo $edit_patient_id; ?>">
							<input type="hidden" name="ci_pid" id="ci_pid" value="<?php echo $ci_pid; ?>">
							<input type="hidden" name="patient_photo_name" id="patient_photo_name" value="<?php echo $patient_image_name; ?>">
							<input type="hidden" name="license_image_name" id="license_image_name" value="<?php echo $license_image_name ?>">
							<input type="hidden" name="scan_card_Primary" id="scan_card_Primary" value="">
							<input type="hidden" name="scan_card_Secondary" id="scan_card_Secondary" value="">
							<input type="hidden" name="scan_card_Tertiary" id="scan_card_Tertiary" value="">
							<input type="hidden" name="sch_id" id="sch_id" value="<?php echo $sch_id; ?>">
							<input type="hidden" name="current_caseid" id="current_caseid" value="<?php echo $current_caseid; ?>">
							<input type="hidden" name="btn_submit_print" id="btn_submit_print" value="" />
							<input type="hidden" name="after_save_url" id="after_save_url" value="" />
							<input type="hidden" name="source" id="source" value="<?php echo $source; ?>" />
							<input type="hidden" id="copy_ins_name" name="copy_ins_name" value="copy_ins_name" > 
							<input type="hidden" name="edit_payment_tbl_id" id="edit_payment_tbl_id" value="<?php echo $main_pay_id; ?>">
							<input type="hidden" name="edit_payment_tbl_id_cash" id="edit_payment_tbl_id_cash" value="<?php echo $edit_payment_tbl_id_cash; ?>">
							<input type="hidden" name="edit_payment_tbl_id_check" id="edit_payment_tbl_id_check" value="<?php echo $edit_payment_tbl_id_check; ?>">
							<input type="hidden" name="edit_payment_tbl_id_card" id="edit_payment_tbl_id_card" value="<?php echo $edit_payment_tbl_id_card; ?>">
                            
                            <input type="hidden" name="log_referenceNumber" id="log_referenceNumber" value="" />
                            <input type="hidden" name="tsys_transaction_id" id="tsys_transaction_id" value="" />
                            <input type="hidden" name="tsys_void_id" id="tsys_void_id" value="" />
                            <input type="hidden" name="tsys_last_status" id="tsys_last_status" value="" />
                            <input type="hidden" name="card_details_str_id" id="card_details_str_id" value="" />
        
							<input type="hidden" name="edit_payment_tbl_id_eft" id="edit_payment_tbl_id_eft" value="<?php echo $edit_payment_tbl_id_eft; ?>">
							<input type="hidden" name="edit_payment_tbl_id_mo" id="edit_payment_tbl_id_mo" value=" <?php echo  $edit_payment_tbl_id_mo; ?>">
							<input type="hidden" name="tot_cash_payment" id="tot_cash_payment" value="">
							<input type="hidden" name="tot_check_payment" id="tot_check_payment" value="">
							<input type="hidden" name="tot_card_payment" id="tot_card_payment" value="">
							<input type="hidden" name="tot_eft_payment" id="tot_eft_payment" value="">
							<input type="hidden" name="tot_mo_payment" id="tot_mo_payment" value="">
							<input type="hidden" name="from_date_byram1" id="from_date_byram1" value="<?php echo get_date_format(date('Y-m-d')); ?>">
							<input type="hidden" name="hidd_scan_card_type" id="hidd_scan_card_type" value="">
							<input type="hidden" id="show_ins_scan_in_modal" value="<?php echo defined("SHOW_INS_SCAN_IN_MODAL_POPUP")?constant("SHOW_INS_SCAN_IN_MODAL_POPUP"):"";?>">	
							<input type="hidden" name="hiddCheckInOnDone" id="hiddCheckInOnDone" value="<?php echo $checkin_on_done; ?> " />
							<input type="hidden" name="hiddApptIds" id="hiddApptIds" value="<?php echo implode(",",$apptIds);?>" />
							<input type="hidden" name="hiddApptIdsLen" id="hiddApptIdsLen" value="<?php echo count($apptIds);?>" />
							<input type="hidden" name="hiddConfirmCheckInApplyAll" id="hiddConfirmCheckInApplyAll" value="0" /> 
							<input type="hidden" name="hiddSelDate" id="hiddSelDate" value="<?php echo $sel_date; ?>" />
              <input type="hidden" name="hitDoneBtn" id="hitDoneBtn" value="0" />            
                           
                            <!-- Title Bar -->
							<div class="col-sm-12">
								<div class="row purple_bar">
									<div class="col-sm-3 text-left">
										<span class="glyphicon glyphicon-user"></span>
										<label>
										<?php
											if($pat_val == 'New'){
												echo "New&nbsp;";
											}else{
												echo "Check In&nbsp;";
											}
										?>
										</label><label>
										<?php	
											echo $top_ci_header;
										?></label>	
									</div>	
									<div class="col-sm-8">
										<div class="row form-inline">
											<div class="col-sm-6 text-right">
												<label style="vertical-align:top">Heard about us :</label>
												<select name="elem_heardAbtUs" id="elem_heardAbtUs" class="form-control minimal" onChange="setHeardOther(this,'20','<?php echo stripslashes($patientQryRes[0]["heard_abt_us"]); ?>');" data-size="10" data-title="" title="<?php echo $forSelTypeAhed;?>">
													<option value=""></option>
													<?php echo $heard_select_options; ?>
													<option value="Other">Other</option>
												</select>
											</div>	
											<div id="heardAbtOther" class="col-sm-2 hide">
												<div class="input-group">
													<input class="form-control" type="text" id="heardAbtOtherTxt" name="heardAbtOther">
													<div class="input-group-addon pointer">
														<span class="glyphicon glyphicon-chevron-left" onClick="swap_visibility_of_objects('heardAbtOther','heardAbtOtherTxt','elem_heardAbtUs')"></span>	
													</div>	
												</div>
											</div>		
											<div class="col-sm-2">
												<?php
													$searchFieldShow = $txtFieldShow = false;
													$searchEvents = '';
													if( $patientQryRes[0]["heard_abt_us"] ) {
														if( in_array($forSelTypeAhed,['Family','Friends','Doctor','Previous Patient.','Previous Patient']) ) {
															$searchFieldShow = true;
															if( $forSelTypeAhed == 'Doctor' ){
																$searchEvents = 'onkeyup="top.loadPhysicians(this,\'heardAbtSearchId\')";onfocus="top.loadPhysicians(this,\'heardAbtSearchId\')";';
															}
															else {
																$searchEvents = 'onkeydown="if( event.keyCode == 13) { searchHeardAbout(); }";';
															}
														} else{
															$txtFieldShow = true;
														}
													}
												?>
												<!--<span id="tdTableHeardAboutDesc">
													<textarea id="heardAbtDesc" name="heardAbtDesc" class="form-control" rows="1" style="display:<?php echo $display_heardtextarea; ?>;"><?php echo stripslashes($patientQryRes[0]["heard_abt_desc"]); ?></textarea>
												</span>-->
												<span id="tdHeardAboutDesc">
													<textarea class="form-control <?php echo ($txtFieldShow ? 'inline' : 'hidden');?>" id="heardAbtDesc" name="heardAbtDesc" rows="1" data-provide="multiple" data-seperator="newline"><?php echo stripslashes($patientQryRes[0]["heard_abt_desc"]); ?></textarea>
												</span>
												<div id="tdHeardAboutSearch" class="<?php echo ($searchFieldShow ? 'inline' : 'hidden');?>">
													<div class="input-group">
														<input type="hidden" id="heardAbtSearchId" name="heardAbtSearchId" value="<?php echo stripslashes($patientQryRes[0]['heard_abt_search_id']); ?>" />
														<input type="text" class="form-control " id="heardAbtSearch" name="heardAbtSearch" value="<?php echo stripslashes($patientQryRes[0]['heard_abt_search']); ?>" autoComplete="off" <?php echo $searchEvents;?> />
														<label class="input-group-addon btn" onClick="searchHeardAbout();" >
															<span class="glyphicon glyphicon-search"></span>
														</label>
													</div>
												</div>
											</div>
	
											<?php 
												if($pat_val!== 'New') {

												// If patient age > 18 then default checked else unchecked.
												$defaultChecked = '';
												if( $patient_age > 18 ) {
													$defaultChecked = ' checked="checked" ';
												}
												
												$sqlrx = "SELECT * FROM `patient_rx_notification_consent` WHERE `patient_id` = ".$pid;
												$resultrx = imw_query($sqlrx);
												if( imw_num_rows($resultrx) == 1 ) {
													$row=imw_fetch_assoc($resultrx);

													if($row['rx_notification_consent']==1) {
														$defaultChecked = ' checked="checked" ';
													} else {
														$defaultChecked = '';
													}
												}
												
											?>		

											<div class="section_rx_notification_consent col-sm-4">
												<div class="checkbox">
			                                       	<input type="checkbox" name="rx_notification_consent" value="0" class="form-control" id="rx_notification_consent" <?php echo $defaultChecked; ?> onclick="rx_consent_notification();">
			                                      	<label for="rx_notification_consent">
														To improve adherence, monitor prescription fill data and send text message to patient with supporting information to:
													</label>	
			                                    </div>
											</div>
											<script type="text/javascript">
												function rx_consent_notification() {
													if($("#rx_notification_consent").is(":checked")) {
														dgi("rx_notification_consent").value="1";
													}else {
														dgi("rx_notification_consent").value="0";
												}
													
													save_rx_notification_consent(dgi("rx_notification_consent").value);
												}
												
												function save_rx_notification_consent(rx_notification_consent) {
													var url=webroot+"/interface/core/ajax_handler.php?task=rx_notification_consent";
													$.ajax({
														url:url,
														type:'POST',
														data:{'rx_notification_consent':rx_notification_consent},
														success:function(resp){
															console.log(resp);
														}
													});
													
												}
												
												
											</script>
											<?php 
												}
											?>

											<?php if(isUGAEnable() && (isset($_SESSION['patient']) && $_SESSION['patient']!='')) { ?>
												<ul class="list-unstyled pull-right" style="margin: 0;">
													<li class="UGAIcon" onClick="top.get_uga_status();"><a href="#" class="main_iconbar_icon demograph_icons" title="UGA Finance Patient Status" style="background-image:url('<?php echo $GLOBALS['webroot'] ?>/library/images/uga_icon_set.png')"></a></li>
												</ul>
											<?php } ?>
											
										</div>
									</div>	
									<div class="col-sm-1 text-right">
										<label> By: <?php echo strtoupper($operatorName); ?></label>	
									</div>		
								</div>	
							</div>
							
              <!-- Patient Demographics & More Information Block -->
							<div class="col-sm-12">
								<div class="row">
									<div class="adminbox">
									<!-- Patient Demographics -->
										<div class="col-sm-6">
											<div class="row">
												<!-- Heading -->
												<div class="col-sm-12">
													<div class="headinghd">
														<div class="row">
															<div class="col-sm-9">
																<h4>PATIENT DEMOGRAPHICS</h4>	
															</div>
                              <div class="col-sm-3">
                              	<div class="row">
                                	<div class="col-sm-6 text-center">
                                    <div class="checkbox ">
                                      <input title="Hold Statements" type="checkbox" tabindex="1" name="pat_hs" value="1" <?php echo $pat_hs_ck; ?> id="pat_hs">	
                                      <label for="pat_hs">HS</label>	
                                    </div>
                             			</div>
                                  
                                  <div class="col-sm-6 text-center">   
                                    <div class="checkbox">
                                       <input type="checkbox" <?php if($edit_patient_id == ''){ echo 'checked=checked'; }?> tabindex="1" name="pat_emr" value="1" <?php echo $pat_emr_ck ?> class="" id="pat_emr">
                                      <label for="pat_emr">EMR</label>	
                                    </div>	
                                  </div>	
                              	</div>
                            	</div>
														</div>	
													</div>
												</div>
												
												<!-- 1st Row -->
												<div class="col-sm-12">
													<div class="row">
														<div class="col-sm-2">
															<label>Title</label>
															<select tabindex="1" id="title" name="title" class="form-control minimal" data-width="100%" data-title="<?php echo $patientQryRes[0]['title'];?>" title="<?php echo $patientQryRes[0]['title'];?>">
																<option value=""></option>
																<?php 
																	foreach($title_arr as $key => $val){
																		if($val == $patientQryRes[0]['title']){
																			$selected = 'selected';
																		}else{$selected = '';}
																		echo '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
																	}
																?>
															</select>
															<input type="hidden" name="hidd_prev_title" id="hidd_prev_title" value="">
														</div>
														<div class="col-sm-3">	
															<label>First Name</label>
															<input tabindex="2" type="text" id="fname" name="fname" value="<?php echo $patientQryRes[0]['fname']; ?>" class="form-control" onKeyUp="callme(this,'');" onBlur="javascript:lostFocus(this,'', '');">
														</div>
														<div class="col-sm-2">
															<label>Middle</label>
															 <input tabindex="3" type="text" id="mname" name="mname" value="<?php echo $patientQryRes[0]['mname']; ?>" class="form-control">
														</div>
														<div class="col-sm-3">
															<label>Last Name</label>
															 <input tabindex="4" type="text" id="lname" name="lname" value="<?php echo $patientQryRes[0]['lname'] ?>" class="form-control" onKeyUp="callme(this,'');" onBlur="javascript:lostFocus(this,'', ''); 
															<?php
																if($patientQryRes[0]['lname'] == ''){
																	echo "check_patient(document.demographics_edit_form.title.value,document.demographics_edit_form.fname.value,document.demographics_edit_form.lname.value)"; 
																}	
															?>">
														</div>
														<div class="col-sm-2">
															<label>Suffix</label>
															 <input tabindex="5" type="text" id="suffix" name="suffix" value="<?php echo $patientQryRes[0]['suffix']; ?>" class="form-control">
														</div>	
													</div>
												</div>

												<!-- 2nd Row -->
												<div class="col-sm-12">
													<div class="row">
														<div class="col-sm-3">
															<label>Marital Status</label>
															<select tabindex="6" class="form-control minimal" name="pat_marital_status" id="pat_marital_status" onChange="callme(this,'');" onBlur="javascript:lostFocus(this,'', '');" data-width="100%" title="<?php echo ucwords($patientQryRes[0]['status']);?>" data-title="<?php echo $patientQryRes[0]['status'];?>">
																<option value=""></option>
																<?php
																
																	foreach($marital_st_arr_val as $key => $val){
																		$selected = '';
																		$val = trim($val);
																		if( !$val) continue;
																		if($patientQryRes[0]['status'] == $val){
																			$selected = 'selected';
																		}
																		echo "<option value='".$val."' ".$selected.">".ucfirst($val)."</option>";
																	}
																?>	
															</select>
														</div>
														<div class="col-sm-3">
															<label>Sex</label>	
															<select tabindex="7" id="selGender" name="selGender" class="form-control minimal" onChange="callme(this,'<?php echo $genClassOnKeyUp; ?>');" onBlur="javascript:lostFocus(this,'<?php echo $genClass; ?>', '');" data-width="100%" title="<?php echo $patientQryRes[0]['sex'];?>" data-title="<?php echo $patientQryRes[0]['sex'];?>">
																<option value="" selected>&nbsp;</option>
																<?php 
																	foreach($gender_info_arr as $key => $val){
																		$selected = '';
																		$key = trim($key);
																		if( !$key) continue;
																		if($patientQryRes[0]['sex'] == $key){
																			$selected = 'selected';
																		}
																		echo "<option value='".$key."' ".$selected.">".$key."</option>";
																	}
																
																?>
															</select>														
														</div>
														
                            <div class="col-sm-3">
															<label>DOB (<?php echo inter_date_format(); ?>):</label>
															<?php $respPartyFun=''; if($pat_val == 'New'){ $respPartyFun=' showHideRespPartyDiv(this); ';} ?>
															<div class="input-group"> 
																<input name="pt_dob" id="dob" type="text"  class="dob-date-pick <?php echo $dobClass; ?> form-control" onBlur="callme(this,'<?php echo $dobClassOnKeyUp; ?>');doDateCheck(this.form.from_date_byram1, this.form.pt_dob);" tabindex="9" title="<?php echo inter_date_format(); ?>" value="<?php if($patientQryRes[0]['patient_dob'] != '00-00-0000'){echo $patientQryRes[0]['patient_dob']; }?>" maxlength="10" onFocus="getFocusObj(this);" onChange="on_dob_changed(); <?php echo $respPartyFun;?>"/>	
																<div class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>	
																</div>	
															</div>
														</div>
														<div class="col-sm-3">
															<label>Age</label>
															<div class="form-control">
																<span id="patient_age"><?php echo $patient_age ;?></span>Year(s) , <span id="patient_age_month"><?php echo $patient_age_month ?></span>Month(s)
															</div>
														</div>
														
                            	
													</div>		
												</div>

												<!-- 3rd Row -->
												<div class="col-sm-12">
													<div class="row">
                          	<div class="col-sm-3">
															<label>Social Security</label>	
															 <input tabindex="8" type="text" id="ssnNumber" name="ssnNumber" value="<?php echo $patientQryRes[0]['ss']; ?>" class="<?php echo $socialClass; ?> form-control" onKeyUp="callme(this,'<?php echo $socialClassOnKeyUp; ?>');" onBlur="javascript:lostFocus(this,'<?php echo $socialClass; ?>', '');if(this.value!='')validate_ssn(this);">
														</div>
                            
                            <div class="col-sm-3">
                              <label for="sexual_orientation">Sexual&nbsp;Orientation</label>
                              <?php 
                                $arrSOR = sexual_orientation();
                              ?>	
                              <select name="sexual_orientation" id="sexual_orientation" class="form-control minimal" data-width="100%" data-prev-val="<?php echo addslashes($patientQryRes[0]['sor_txt']); ?>" title="<?php echo $patientQryRes[0]['sor_txt']; ?>" onChange="showHippaOtherTxtBox(this.id,'otherSOR','imgBackSOR'); callme(this,'');" >
                              <option value="" selected>&nbsp;</option>
                              <?php 
                                foreach($arrSOR as $key => $val)
                                {
                                  $sel = ($patientQryRes[0]['sor_txt'] == $key ) ? 'selected' : '';
                                  echo '<option value="'.$key.'" '.$sel.'>'.$val['value'].'</option>';
                                }
                              
                              ?>
                              </select>
                             	<div class="input-group hide">
                                <input class="form-control" name="otherSOR" id="otherSOR"  value="<?php echo stripslashes($patientQryRes[0]['other_sor']); ?>" style="display:none" onKeyUp="callme(this,''); chkChange('<?php echo $patientQryRes[0]['other_sor']; ?>',this.value);" /> 
                                <div class="input-group-addon pointer">
                                  <span class="glyphicon glyphicon-chevron-left" id="imgBackSOR" style="display:none; border:none; vertical-align:text-top;" onClick="showHippaComboBox('sexual_orientation','otherSOR','imgBackSOR');callme(document.getElementById('sexual_orientation'),'');"></span>
                                  <script type="text/javascript">
                                    showHippaOtherTxtBox('sexual_orientation','otherSOR','imgBackSOR');
                                  </script>		
                                </div>
                              </div>
                          	</div>
                        
                            <div class="col-sm-3">
                              <label for="gender_identity">Gender&nbsp;Identity</label>
                              <?php 
                                $arrGID = gender_identity();
                              ?>
                              <select name="gender_identity" id="gender_identity" class="form-control minimal" data-width="100%" data-prev-val="<?php echo addslashes($patientQryRes[0]['gi_txt']); ?>" title="<?php echo $patientQryRes[0]['gi_txt']; ?>" onChange="showHippaOtherTxtBox(this.id,'otherGI','imgBackGI'); callme(this,'');" >
                              <option value="" selected>&nbsp;</option>
                              <?php 
                                foreach($arrGID as $key => $val)
                                {
                                  $sel = ($patientQryRes[0]['gi_txt'] == $key ) ? 'selected' : '';
                                  echo '<option value="'.$key.'" '.$sel.'>'.$val['value'].'</option>';
                                }
                              ?>
                              </select>
                              <div class="input-group hide">
                                <input class="form-control" name="otherGI" id="otherGI"  value="<?php echo $patientQryRes[0]['other_gi']; ?>" style="display:none" onKeyUp="callme(this,''); chkChange('<?php echo $patientQryRes[0]['other_gi']; ?>',this.value);" /> 
                                <div class="input-group-addon pointer">
                                  <span class="glyphicon glyphicon-chevron-left" id="imgBackGI" style="display:none; border:none; vertical-align:text-top;" onClick="showHippaComboBox('gender_identity','otherGI','imgBackGI');callme(document.getElementById('gender_identity'),'');"></span>
                                  <script type="text/javascript">
                                    showHippaOtherTxtBox('gender_identity','otherGI','imgBackGI');
                                  </script>		
                                </div>
                              </div>
                          	</div>
                            
														<div class="col-sm-3 form-group">
															<label>Email-Id</label>
															<input name="pat_email" id="pat_email" type="text" class="<?php echo $emailClass; ?> form-control" tabindex="10" value="<?php echo $patientQryRes[0]['email']; ?>" onKeyUp="javascript:search_email(event,this,'div_email_section')" onFocus="getFocusObj(this);">
															<div id="div_email_section" class="input-group" style="position:absolute">
																<span class="input-group-btn">
																	<select class="btn hide"  size="5" style="width:100%">
																		<option selected value="@aol.com">aol.com</option>
																		<option value="@gmail.com">gmail.com</option>
																		<option value="@hotmail.com">hotmail.com</option>
																		<option value="@msn.com">msn.com</option>
																		<option value="@yahoo.com">yahoo.com</option>

																	</select>
																</span>
															</div>
														</div>	
													</div>	
												</div>	
												<!-- All communication Block -->
												<div class="col-sm-12 pt10">
													<div class="headinghd">
														<div class="row">
															<div class="col-sm-10">
																<div class="radio radio-inline">
																	<input type="radio" id="all_communication" value="0" name="all_communication" checked="checked" autocomplete="off">
																	<label for="all_communication"><strong style="font-family: 'robotobold';font-size:16px">ALL COMMUNICATIONS</strong></label>
																</div>
															</div>
															<div class="col-sm-2 text-right">
																<span class="glyphicon glyphicon-plus pointer" onClick="add_new_address('checkin');"></span>
															</div>	
														</div>	
													</div>	
												</div>
												<div class="col-sm-12">
													<div style="height:225px;overflow:hidden; overflow-y:auto;" id="div_all_addresses">
														<div class="row">
															<div class="col-sm-6">
																<label>Street1</label>
																<input type="hidden" name="id_address[0]" value="<?php echo $patientQryRes[0]['default_address']; ?>">
																<input tabindex="11" type="text" id="street" name="street[0]" value="<?php echo $patientQryRes[0]['street']; ?>" class="<?php echo $addClass; ?> form-control" onKeyUp="callme(this,'<?php echo $addClassOnKeyUp ?>');" onBlur="javascript:lostFocus(this,'<?php echo $addClass; ?>', '');">	
															</div>	
															<div class="col-sm-6">
																<label>Street2</label>
																 <input tabindex="12" type="text" id="street2" name="street2[0]" value="<?php echo $patientQryRes[0]['street2']; ?>" class="<?php echo $addClass1; ?> form-control" onKeyUp="callme(this,'<?php echo $addClassOnKeyUp ?>');" onBlur="javascript:lostFocus(this,'<?php echo $addClass1; ?>', '');">
															</div>
															<div class="clearfix"></div>
															<div class="col-sm-6">
																<div class="row"> 
																	<div class="col-sm-3">
																		<label><?php getZipPostalLabel(); ?></label>	
																		<input tabindex="13" type="text" id="code" name="postal_code[0]" value="<?php echo $patientQryRes[0]['postal_code'] ?>" class="<?php echo $zipClass; ?> form-control" size="<?php echo $zip_size; ?>" maxlength="<?php echo $zip_size; ?>" 	 onBlur="zip_vs_state(this,'city','state','country_code','county');lostFocus(this,'<?php echo $zipClass ?>', '');" onKeyUp="callme(this,'<?php echo $zipClassOnKeyUp ?>');">	
																	</div>	
																	<div class="col-sm-3" style="display:<?php echo $zip_ext;  ?>">
																		<label>&nbsp;</label>
																		<input tabindex="13" type="text" id="zip_ext" name="zip_ext[0]" value="<?php echo $patientQryRes[0]['zip_ext']; ?>" maxlength="4" class="form-control"/>
																	</div>		
																	<div class="col-sm-6">
																		<label>City</label>
																		<input tabindex="14" type="text" id="city" name="city[0]" value="<?php echo $patientQryRes[0]['city']; ?>" class="<?php echo $cityClass; ?> form-control" onKeyUp="callme(this,'<?php echo $cityClassOnKeyUp ?>');"  onFocus="callme(this,'<?php echo $cityClassOnKeyUp ?>');" onBlur="javascript:callme(this,'<?php echo $cityClassOnKeyUp; ?>');lostFocus(this,'<?php echo $cityClass ?>', '');">
																	</div>			
																</div>
															</div>
															<div class="col-sm-6">
																<div class="row">
																	<div class="col-sm-4">
																		<label><?php echo ucfirst(inter_state_label()); ?></label>
																		<input tabindex="15" type="text" id="state" name="state[0]" value="<?php echo $patientQryRes[0]['state']; ?>" class="<?php echo $stateClass; ?> form-control" size="2" onKeyUp="callme(this,'<?php echo $stateClassOnKeyUp; ?>');" onBlur="javascript:lostFocus(this,'<?php echo $stateClass; ?>', '');">
																	</div>		
																	<div class="col-sm-4">
																		<label>County</label>
																		<input name="county[0]" type="text" class="form-control" id="county" tabindex="16" value="<?php echo $patientQryRes[0]['county']; ?>">
																	</div>	
																	<div class="col-sm-4">
																		<label>Country</label>
																		<input name="country_code[0]" type="text" class="form-control" id="country_code" tabindex="16" value="<?php echo inter_country(); ?>">
																	</div>		
																</div>
															</div>	
															<div class="clearfix"></div>
															<div class="col-sm-12">
																<div class="row">
																	<div class="col-sm-4">
																		<label>
																			<div class="radio radio-inline">
																				<input <?php if($pf_contact_chk == 0 ){echo "checked='checked'";} ?> style="padding:0px; float:left;" type="radio" id="pf_contact_home" name="pf_contact" title="Preferred" value="0">&nbsp;<label for="pf_contact_home"><b>Home Phone <?php getHashOrNo(); ?></b></label>	
																			</div>
																		</label>
																		<input type="text" size="15" tabindex="17" id="phone_home" name="phone_home" onChange="set_phone_format1(this,'<?php echo inter_phone_format(); ?>','','','form-control mandatory')" value="<?php echo $phone_home; ?>"  class="<?php echo $homePhoneClass; ?> form-control" onKeyUp="callme(this,'<?php echo $homePhoneClassOnKeyUp ?>');" onBlur="javascript:lostFocus(this,'<?php echo $homePhoneClass; ?>', '');">
																	</div>
																	<div class="col-sm-4">
																		<div class="row">
																			<div class="col-sm-8">
																				<label>
																					<div class="radio radio-inline">
																						<input <?php if($pf_contact_chk == 1){echo 'checked = "checked"';} ?> style="float:left; padding:0px" type="radio" id="pf_contact_work" name="pf_contact" title="Preferred" value="1" /><label for="pf_contact_work"><b>Work Phone <?php getHashOrNo(); ?></b></label>	
																					</div>
																				</label>
																				<input type="text" tabindex="18" id="phone_biz" name="phone_biz" onChange="set_phone_format1(this,'<?php echo inter_phone_format(); ?>','','','form-control mandatory')" value="<?php echo $phone_biz; ?>"  class="<?php echo $workPhoneClass; ?> form-control" onKeyUp="callme(this,'<?php echo $workPhoneClassOnKeyUp; ?>');" onBlur="javascript:lostFocus(this,'<?php echo $workPhoneClass; ?>', '');">
																			</div>
																			<div class="col-sm-4 checkbox extension_box">
																				<label><b>Ext.</b></label>
																				<input type="text" name="phone_biz_ext" id="phone_biz_ext" size="6" class="form-control" value="<?php echo $phone_biz_ext; ?>">		
																			</div>	
																		</div>
																	</div>	
																	<div class="col-sm-4">
																		<label>
																			<div class="radio radio-inline">
																				<input <?php if($pf_contact_chk == 2 || $pf_contact_chk == ''){echo 'checked="checked"';} ?> style="float:left; padding:0px;" type="radio" id="pf_contact_mobile" name="pf_contact" title="Preferred" value="2" />&nbsp;<label for="pf_contact_mobile"><b>Mobile Phone <?php getHashOrNo(); ?></b></label>	
																			</div>
																		</label>
																		<input type="text" tabindex="19" onChange="set_phone_format1(this,'<?php echo inter_phone_format(); ?>','','','form-control mandatory')" id="phone_cell" name="phone_cell" value="<?php echo $phone_cell; ?>" class="<?php echo $mobilePhoneClass; ?> form-control" onKeyUp="callme(this,'<?php echo $mobilePhoneClassOnKeyUp ?>');" onBlur="javascript:lostFocus(this,'<?php echo $mobilePhoneClass; ?>', '');">
																	</div>	
																</div>	
															</div>
                                                            <div class="clearfix"></div>
                                                            <div class="col-sm-12 hasotherbox">
                                                                <div class="row">
                                                                    <?php $erotherval=$patientQryRes[0]['emergencyRelationship'];
                                                                    $eroclass='hidden';$erclass='inline';if($erotherval=='Other'){$erclass='hidden';$eroclass='inline';} ?>
                                                                    <div class="col-xs-12 col-md-4">
                                                                        <label for="contact_relationship">Emergency Name</label>
                                                                        <input name='contact_relationship' id="contact_relationship" type='text' class="form-control"  value="<?php echo stripslashes($patientQryRes[0]['contact_relationship']); ?>" data-prev-val="<?php echo addslashes($patientQryRes[0]['contact_relationship']); ?>" />
                                                                    </div>

                                                                    <div class="col-xs-12 col-md-4">
                                                                        <label>Relationship</label>
                                                                        <select name='emerRelation' id="emerRelation" class="form-control minimal <?php echo $erclass;?>" title="<?php echo $patientQryRes[0]['emergencyRelationship'];?>" data-header="Relationship" data-width="100%">
                                                                            <?php
                                                                            $arrEmerRelation = get_relationship_array('emergency_relation');
                                                                            foreach ($arrEmerRelation as $s) {
                                                                                echo "<option value='" . $s . "'";
                                                                                if ($s == $patientQryRes[0]['emergencyRelationship']) {
                                                                                    echo " selected";
                                                                                    echo ">" . ucfirst($s) . "</option>\n";
                                                                                } else {
                                                                                    echo ">" . ucfirst($s) . "</option>\n";
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                        <div id="relation_other_box" class="<?php echo $eroclass;?>">
                                                                            <div class="input-group ">
                                                                                <input type="text" id="relation_other_textbox" name="relation_other_textbox" class="form-control" value="<?php echo $patientQryRes[0]['emergencyRelationship_other']; ?>" >
                                                                                <label class="input-group-btn btn btn-primary btn-xs back_other" data-tab-name="emerRelation" id="imgRelOtherTextBox">
                                                                                    <i class="glyphicon glyphicon-arrow-left"></i>
                                                                                </label>  
                                                                            </div>
                                                                        </div>   
                                                                    </div>	

                                                                    <div class="col-xs-12 col-md-4">
                                                                        <label>Emergency Tel #</label>
                                                                        <input name="phone_contact" id="phone_contact" type='text' class="form-control" onChange="set_phone_format1(this,'<?php echo inter_phone_format(); ?>','','','form-control mandatory')" value="<?php echo stripslashes($patientQryRes[0]['phone_contact']); ?>" data-prev-val="<?php echo addslashes($patientQryRes[0]['phone_contact']); ?>"  />
                                                                    </div>      

                                                                    <div class="clearfix mb5"></div>

                                                                </div>
                                                            </div>
														</div>
														<!-- Multi Address Block -->
														<div class="row pt10">
															<div id="div_addlist1" class="col-sm-12">
																<div id="addNew_address" class="row">
																	<input type="hidden" name="address_del_id" id="address_del_id">
																	<?php
																		$address_cnt = 1;
																		foreach($patient_multi_add as $key=>$val){
																	?>	
																		<div id="div_address<?php echo $address_cnt; ?>" class="col-sm-12">    
																			 <input type="hidden" name="id_address[<?php echo $address_cnt; ?>]" value="<?php echo $val['id']; ?>">
																			<div class="row">
																				<div class="col-sm-12">
																					<div class="headinghd">
																						<div class="row">
																							<div class="col-sm-10">
																								<div class="radio radio-inline">
																									<input type="radio" name="all_communication" id="all_communication_<?php echo $address_cnt; ?>" value="<?php echo $address_cnt; ?>">
																									<label for="all_communication_<?php echo $address_cnt; ?>"><strong style="font-family: 'robotobold';font-size:16px">ALL COMMUNICATIONS</strong></label>
																								</div>
																							</div>
																							<div class="col-sm-2 text-right">
																								<span class="glyphicon glyphicon-remove pointer" onClick="del_address(<?php echo $address_cnt; ?>,<?php echo $val['id']; ?>)"></span>
																							</div>	
																						</div>	
																					</div>	
																				</div>
																				<div class="col-sm-12 pt10">
																					<div class="row">
																						<div class="col-sm-6">
																							<label>Street1</label>
																							<input name="street[<?php echo $address_cnt; ?>]" id="street" type="text" value="<?php echo $val['street']; ?>" class="form-control">
																						</div>
																						<div class="col-sm-6">
																							<label>Street2</label>
																							<input name="street2[<?php echo $address_cnt; ?>]" id="street2" type=text class="form-control" value="<?php echo $val['street2'];  ?>">	
																						</div>	
																					</div>	
																				</div>
																				<div class="col-sm-6">
																					<div class="row">
																						<div class="col-sm-3">
																							<label><?php getZipPostalLabel(); ?></label>
																								 <input name="postal_code[<?php echo $address_cnt; ?>]" type="text" id="code<?php echo $address_cnt; ?>"  onBlur="zip_vs_state(this,'city_<?php echo $address_cnt; ?>','state_<?php echo $address_cnt; ?>','country_code_<?php echo $address_cnt; ?>','county_<?php echo $address_cnt; ?>'); validate_zip(this);" value="<?php echo $val['postal_code']; ?>"   maxlength="<?php echo $zip_size; ?>" class="form-control">
																						</div>
																						<div class="col-sm-3" style="display:<?php echo $zip_ext;  ?>">
																							<label>&nbsp;</label>
																							<input name="zip_ext[<?php echo $address_cnt; ?>]" type="text" id="zip_ext" value="<?php echo $val['zip_ext']; ?>" maxlength="4" class="form-control"/>	
																						</div>
																						<div class="col-sm-6">
																							<label>City</label>
																							<input name="city[<?php echo $address_cnt; ?>]" type="text" id="city_<?php echo $address_cnt; ?>" value="<?php echo $val['city']; ?>" class="form-control">	
																						</div>	
																					</div>	
																				</div>
																				<div class="col-sm-6">
																					<div class="row">
																						<div class="col-sm-4">
																							<label><?php echo ucfirst(inter_state_label()); ?></label>
																							<input name="state[<?php echo $address_cnt; ?>]" type="text" maxlength="<?php echo $state_length; ?>" id="state_<?php echo $address_cnt; ?>" value="<?php echo $val['state']; ?>" class="form-control">			
																						</div>
																						<div class="col-sm-4">
																							<label>County</label>
																							 <input name="county[<?php echo $address_cnt; ?>]" type="text" class="form-control" id="county_<?php echo $address_cnt; ?>" value="<?php echo $val['county']; ?>">	
																						</div>
																						<div class="col-sm-4">
																							<label>Country</label>	
																							 <input name="country_code[<?php echo $address_cnt; ?>]" type="text" class="form-control" id="country_code_<?php echo $address_cnt; ?>" value="<?php echo $val['country_code']; ?>">	
																						</div>	
																					</div>	
																				</div>	
																			</div>	
																		</div>
																	<?php	
																			$address_cnt++;	
																		}
																	?>	
																</div>
															</div>	
														</div>
													</div>	
												</div>
											</div>	
										</div>
									
									<!-- More Information -->
									<div class="col-sm-6">
										<div class="row">
											<!-- Heading -->
											<div class="col-sm-12">
												<div class="headinghd">
													<div class="col-sm-9">
														<h4>More Information</h4>	
													</div>
													<div class="col-sm-3">
														<?php	
															if($pat_val != 'New'){
															?>
																<input type="button" class="btn btn-primary" value="Pharmacy Pref." onClick="var parentWid = parent.document.body.clientWidth; var parenthei = parent.document.body.clientHeight; window.open('../../chart_notes/erx_patient_selection.php?loadmodule=ptdemo','erx_window_new','resizable=1,width='+parentWid+',height='+parenthei+'');">
															<?php
															}
														?>	
													</div>	
												</div>	
											</div>

											<!-- Content -->
											<div class="col-sm-12">
												<div class="row">
													<!-- Photo Div -->
													<div class="col-sm-3">
														<div class="thumbnail text-center">
															<span id="ptImageDiv"><img class="img-responsive" src="<?php echo $patient_image_path; ?>"  /></span>
															  <div class="caption text-center">
																<span><a tabindex="20" id="anchor_patient_photo" class="text12b_purpule" onClick="scan_patient_image()" href="javascript:foo();">Patient&nbsp;Photo
																<span class="glyphicon glyphicon-camera pull-right" style="font-size:21px;text-decoration:none"></span>
																</a>
																</span>
																
																<a href="javascript:void[0]" class="pull-right" id="ptImageDelDiv" style="display:<?php echo $image_del_dis; ?>" onClick="javascript:delete_photo();"><i class="glyphicon glyphicon-remove pointer"></i></a>
																
																
																
															</div>
														</div>
													</div>	
													
													<div class="col-sm-3">
														<!-- Photo Div -->
														<div class="thumbnail text-center">
															<span id="ptLicImageDiv">
																<?php echo $lic_img_src; ?>
                                                                <span class="layer" data-toggle="modal" data-target="#imageLicense"></span>
															</span>
															<div class="caption text-center">
																<span><a tabindex="20" id="anchor_scan_license" <?php echo $dis_lic_anchor; ?> class="text12b_purpule" onClick="scan_licence_new()" href="javascript:foo();">Scan&nbsp;License</a></span>
																
																<a href="javascript:void[0]" class="pull-right" id="ptLicToolBarDiv" style="display:<?php echo $lic_show_div; ?>" onClick="javascript:delete_license();"><i class="glyphicon glyphicon-remove pointer"></i></a>
																<!-- <script src="../../../js/Driving_License_Scanning.js"></script> -->
															</div>
															<span class="pull-right pointer">
																<input type="text" style="height:0px; width:0px;position:absolute;left:-100000px" id="scanner" autocomplete="off">
																<img style="position: relative;" src="../../../library/images/scan.png" onClick="set_Focus()">
															</span>
														</div>
													</div>
													
													<!-- Form -->
													<div class="col-sm-6">
														<div class="row">
															<div class="col-sm-12">
																<label>Driving License</label>	
																<input tabindex="20" name="dlicence" id="dlicence" type="text" class="<?php echo $drvClass; ?> form-control" value="<?php echo $patientQryRes[0]['driving_licence']; ?>"  onKeyUp="callme(this,'<?php echo $drvClassOnKeyUp; ?>');" onBlur="javascript:lostFocus(this,'<?php echo $drvClass; ?>', '');">
															</div>	
															<div class="col-sm-12">
																<label>Home&nbsp;Facility</label>
																<select tabindex="21" id="default_facility" name="default_facility" class="form-control minimal"  onChange="callme(this,'<?php echo $faciClassOnKeyUp; ?>');" onBlur="javascript:lostFocus(this,'<?php echo $faciClass; ?>', '');" data-size="10" data-width="100%" data-title="<?php echo $facility_data_arr[$patientQryRes[0]['default_facility']];?>" title="<?php echo $facility_data_arr[$patientQryRes[0]['default_facility']];?>">
																<option value=""></option>
																	<?php
																		foreach($facility_data_arr as $key => $val){
																			$selected = '';
																			if($key == $patientQryRes[0]['default_facility']){
																				$selected = 'selected';
																			}
                                                                            if(empty($user_pos_fac_arr)==false && (!in_array($key,$user_pos_fac_arr)) ) {
                                                                                if($key == $patientQryRes[0]['default_facility'] && $pat_val != 'New')
                                                                                    { /*do not skip already selected pos facility which does not exists in users pos facility group */ }
                                                                                else 
                                                                                    { /*skip pos facility those does not exists in users pos facility group */
                                                                                    continue; }
                                                                            }
																			echo '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
																		}
																	?>
																</select>
															</div>
														</div>
														<div class="row pt10">	
															<div class="col-sm-6">
																<label id="spanAddRefPhy" onClick="chkshowMultiPhy(1, 1);" class="pointer text_purple">Referring Phy.</label>
																<input type="hidden" name="pre_ref_phy_id" id="pre_ref_phy_id" value="<?php echo $reff_phy_id; ?>">
																<input type="hidden" name="pre_ref_phy_name" id="pre_ref_phy_name" value="<?php echo $ref_phy_name; ?>">
																<input type="hidden" name="ref_phy_id" id="ref_phy_id" value="<?php echo $reff_phy_id; ?>">
																<div class="input-group">
																	<input tabindex="22" type="text" id="ref_phy_name" name="ref_phy_name" value="<?php echo $refPhyStr; ?>" class="<?php echo $ref_phy_class; ?> form-control" size="14" data-prev-val="<?php echo $refPhyStr; ?>" onKeyUp="top.loadPhysicians(this,'ref_phy_id');callme(this,'<?php echo $ptRefClassOnKeyUp; ?>');" onBlur="javascript:lostFocus(this,'<?php echo $ptRefClass; ?>', ''); refine_data(this, 'refphy');" onFocus="top.loadPhysicians(this,'ref_phy_id');" <?php echo show_popover($refPhyPopover,'bottom','hover');?> /><input type="hidden" id="hidd_ref_phy_name" name="hidd_ref_phy_name" value="<?php echo $ref_phy_name ?>">
																	<div class="input-group-addon" onClick="javascript:search_physician_popup('ref_phy_name');">
																		<span class="glyphicon glyphicon-search"></span>	
																	</div>
																</div>
															</div>	
															<div class="col-sm-6">
																<label id="spanAddPCPDemo" onClick="chkshowMultiPhy(1, 4);" class="pointer text_purple">Primary&nbsp;Care&nbsp;Provider</label>
																<input type="hidden" name="pre_pcp_id" id="pre_pcp_id" value="<?php echo $primary_care_phy_id; ?>">
																<input type="hidden" name="pre_pcp_name" id="pre_pcp_name" value="<?php echo $primary_care_phy_name; ?>">
																<input type="hidden" name="primary_care_phy_id" id="primary_care_phy_id" value="<?php echo $primary_care_phy_id; ?>">
																<div class="input-group">
																	<input tabindex="24" type="text" id="primary_care_name" name="primary_care_name" value="<?php echo $primaryPhyStr; ?>" data-prev-val="<?php echo $primaryPhyStr; ?>" class="<?php echo $primary_care_phy_class; ?> form-control"  onKeyUp="loadPhysicians(this,'primary_care_phy_id');callme(this,'<?php echo $ptRefClassOnKeyUp; ?>');" onBlur="javascript:lostFocus(this,'<?php echo $ptRefClass; ?>', '');refine_data(this,'pcp');" onFocus="loadPhysicians(this,'primary_care_phy_id');" <?php echo show_popover($primaryPhyPopover,'bottom','hover');?> /><input type="hidden" id="hidd_primary_care_name" name="hidd_primary_care_name" value="<?php echo $primary_care_phy_name; ?>">	
																	<div class="input-group-addon">
																		<span class="glyphicon glyphicon-search" onClick="javascript:search_physician_popup('primary_care_name');" style="cursor:pointer; border:none;"></span>	
																	</div>
																</div>
															</div>	
														</div>	
													</div>
												</div>	
												<div class="row">
													<div class="col-sm-12">
														<div class="row pt10">
															<div class="col-sm-3">
																<label class="purple-text pointer" data-toggle="modal" data-target="#race_modal"><b>Race</b></label>
																<select name='race[]' id="race" class="<?php echo $raceClass; ?> selectpicker" multiple="multiple"  onChange="showHippaOtherTxtBox(this.id,'otherRace','imgBackRace'); callme(this,'<?php echo $raceClassOnKeyUp; ?>');" data-width="100%" data-size="5" data-title="">
																	<?php
																		
																		$pat_race = explode(",",$patientQryRes[0]['race']);pre($pat_race);
																		$pat_race = array_filter($pat_race);
																		$tmpArr  = array_diff($pat_race,$arrRace);
																		$tmpNewArr=array_splice($arrRace,-1);
																		$arrRace = array_merge($arrRace,$tmpArr,$tmpNewArr);
																		foreach($arrRace as $key=>$val){
																			$selected = '';
																			if(in_array($val,$pat_race)){
																				$selected = 'selected';
																			}
																			echo '<option value="'.$val.'" '.$selected.'>'.$val.'</option>';
																		}
																	?>
																</select>
																<div class="input-group hide">
																	<input class="form-control" name="otherRace" id="otherRace" value="<?php echo $patientQryRes[0]['otherRace']; ?>" style="display:none;" onKeyUp="callme(this,''); chkChange('',this.value);" />
																	<div class="input-group-addon">
																		<span class="glyphicon glyphicon-chevron-left" id="imgBackRace" style="display:none; border:none; vertical-align:text-top;" onClick="showHippaComboBox('race','otherRace','imgBackRace');callme(document.getElementById('race'),'');" onKeyUp="chkChange('<?php echo $patientQryRes[0]['otherRace']; ?>',this.value);"></span>
																	</div>
																</div>
															</div>
															<div class="col-sm-3">
																<?php
																	if( $patientQryRes[0]['language'] && !in_array($patientQryRes[0]['language'],$arrLanguage))
																	{
																		$tmpNewArr=array_splice($arrLanguage,-2);	
																		array_push($arrLanguage,$patientQryRes[0]['language']);
																		sort($arrLanguage);
																		$arrLanguage = array_merge($arrLanguage,$tmpNewArr);	
																	}
																?>
																<label class="purple-text pointer" data-toggle="modal" data-target="#language_modal"><b>Language</b></label>
																<input type="hidden" name="lang_code" id="lang_code" value="<?php echo $patientQryRes[0]['lang_code'] ?>" />
																<select name='language' id="language" class="form-control minimal" onChange="showHippaOtherTxtBox(this.id,'otherLanguage','imgBackLanguage'); callme(this,'');add_lang_code(this);" data-width="100%" data-size="5" data-title="<?php echo imw_msg('drop_sel');?>">
																	<option value=""></option>
																	<?php
																		foreach($arrLanguage as $key=>$val){
																			$selected = '';
																			if($val == $patientQryRes[0]['language']){
																				$selected = 'selected';
																			}
																			echo '<option value="'.$val.'" '.$selected.' data-common="1" data-code="'.$key.'">'.$val.'</option>';
																		}
																	?>
																</select>
																<div class="input-group hide">
																	<input class="form-control" name="otherLanguage" id="otherLanguage"  value="<?php echo $other_language_val; ?>" style="display:none" onKeyUp="callme(this,''); chkChange('<?php echo $other_language_val; ?>',this.value);" /> 
																	<div class="input-group-addon">
																		<span class="glyphicon glyphicon-chevron-left" id="imgBackLanguage" style="display:none; border:none; vertical-align:text-top;" onClick="showHippaComboBox('language','otherLanguage','imgBackLanguage');callme(document.getElementById('language'),'');"></span>
																		<script type="text/javascript">
																			showHippaOtherTxtBox('language','otherLanguage','imgBackLanguage');
																		</script>		
																	</div>
																</div>
																<div id="pt_lang_type" style="position:absolute;z-index:1000;"></div>
															</div>
															<div class="col-sm-3">
																<label class="purple-text pointer" data-toggle="modal" data-target="#ethnicity_modal"><b>Ethnicity</b></label>
																<select name='ethnicity[]' id="ethnicity" class="<?php echo $ethnicityClass ?> selectpicker"  onChange="showHippaOtherTxtBox(this.id,'otherEthnicity','imgBackEthnicity'); callme(this,'<?php echo $ethnicityClassOnKeyUp; ?>');" multiple="multiple" data-width="100%" data-size="5" data-title="">
																	<?php 
																		$pat_ethnicity = explode(",",$patientQryRes[0]['ethnicity']);
																		foreach($arrEthnicity as $key=>$val){
																			$selected = '';
																			if(in_array($val,$pat_ethnicity)){
																				$selected = 'selected';
																			}
																			echo '<option value="'.$val.'" '.$selected.'>'.$val.'</option>';
																		}
																	?>
																</select>
																<div class="input-group hide">
																	<input class="form-control" name="otherEthnicity" id="otherEthnicity"   value="<?php echo $patientQryRes[0]['otherEthnicity']; ?>" onKeyUp="callme(this,''); chkChange('<?php echo $patientQryRes[0]['otherEthnicity']; ?>',this.value);" />
																	<div class="input-group-addon">
																		<span class="glyphicon glyphicon-chevron-left" id="imgBackEthnicity" style="display:none; margin-top:2px; border:none;" onClick="showHippaComboBox('ethnicity','otherEthnicity','imgBackEthnicity');callme(document.getElementById('ethnicity'),'');"></span> 
																	</div>
																</div>	
															</div>
															<div class="col-sm-3">
																<div class="row">
																	<div class="col-sm-6">
																		<label style="display:<?php echo $pt_key ?>">&nbsp;Temp Key</label>
																		<input type="text" name="temp_key" class="form-control" id="temp_key" value="<?php echo $patientQryRes[0]['temp_key']; ?>" style="display:<?php echo $pt_key ?>" />	
																	</div>
																	<div class="col-sm-6">
																		<br />
																		<div class="checkbox checkbox-inline">
																			<input type="checkbox" id="temp_key_chk_val" name="temp_key_chk_val" value="1" <?php echo $temp_key_chk_status; ?>  style="display:<?php echo $pt_key ?>">
																			<label for="temp_key_chk_val" style="display:<?php echo $pt_key ?>"> Given</label>
																		</div>
																	</div>
																</div>	
															</div>																
														</div>	
													</div>	
													<!-- Notes -->	
													<div class="col-sm-12">
														<div class="row">
															<div class="col-sm-12">
																<label>Notes:</label>
																 <textarea tabindex="26" name="patient_notes" id="patient_notes" rows="3" class="form-control" onChange="sel_notes_chk(this.value);" onFocus="getOperatorNameAndDate();"><?php echo $patientQryRes[0]['patient_notes']; ?></textarea>
															</div>
															<div class="col-sm-3 pt10">
																<div class="checkbox checkbox-inline">
																	<input tabindex="27" type="checkbox" id="chkNotesScheduler" name="chkNotesScheduler" value="1" <?php echo $scheduler_chk; ?> />
																	<label for="chkNotesScheduler">Scheduler</label>	
																</div>	
															</div>
															<div class="col-sm-3 pt10">
																<div class="checkbox checkbox-inline">
																	 <input tabindex="28" type="checkbox" id="chkNotesChartNotes" name="chkNotesChartNotes" value="1" <?php echo $chart_notes_chk; ?> /> 
																	<label for="chkNotesChartNotes">Chart Notes</label>	
																</div>	
															</div>	
															<div class="col-sm-3 pt10">
																<div class="checkbox checkbox-inline">
																	<input tabindex="29" type="checkbox" id="chkNotesAccounting" name="chkNotesAccounting" value="1" <?php echo $accounting_chk; ?> /> 
																	<label for="chkNotesAccounting">Accounting</label>	
																</div>		
															</div>
															<div class="col-sm-3 pt10">
																<div class="checkbox checkbox-inline">
																	<input tabindex="29" type="checkbox" id="chkNotesOptical" name="chkNotesOptical" value="1" <?php echo $optical_chk; ?> /> 
																	<label for="chkNotesOptical">Optical</label>	
																</div>		
															</div>
														</div>	
													</div>	
                                                </div>	
                                                
                                                <div class="clearfix"></div>
                                                <div class="panel-group accordion" id="accordion-right" role="tablist" aria-multiselectable="true">
                                                
                                                    <div class="release_info hasotherbox">
                                                        <div class="panel panel-default" id="ReleaseInformation">
                                                            <div class="panel-heading" role="tab" id="headingRelease">
                                                                <h4 class="panel-title">
                                                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-right" href="#collapseRelease" aria-expanded="false" aria-controls="collapseRelease">Release Information</a>
                                                                </h4>
                                                            </div>

                                                            <div id="collapseRelease" class="panel-collapse collapse pt-box" role="tabpanel" aria-labelledby="headingRelease">
                                                                <div class="panel-body grid-box" tabindex="0">
                                                                    <!-- Div container for dropdown option in release information -->
                                                                    <div id="rel_select_picker" style="height:0px;"></div>
                                                                    <div class="col-sm-12">
                                                                        <div class="row" id="rel_table_parent">
                                                                            <table class="table table-bordered table-hover table-striped scroll release-table mb5">
                                                                                <thead >
                                                                                    <tr>
                                                                                        <th class="col-sm-4 text-center">Name</th>
                                                                                        <th class="col-sm-4 text-center">Phone</th>
                                                                                        <th class="col-sm-4 text-center">Relationship</th>
                                                                                    </tr>
                                                                                </thead>

                                                                                <tbody>
                                                                                    <?php
                                                                                    $arrHippaRelation = get_relationship_array('hipaa_relation');
                                                                                    for ($loop = 1; $loop < 5; $loop++) {
                                                                                        $name_var = $phone_var = $rel_var = $other_rel_var = '';
                                                                                        $f_name_var = 'relInfoName' . $loop;
                                                                                        $f_phone_var = 'relInfoPhone' . $loop;
                                                                                        $f_rel_var = 'relInfoReletion' . $loop;
                                                                                        $f_other_rel_var = 'otherRelInfoReletion' . $loop;

                                                                                        $name_var = 'ptRelInfoName' . $loop;
                                                                                        $phone_var = 'ptRelInfoPhone' . $loop;
                                                                                        $rel_var = 'ptRelInfoReletion' . $loop;
                                                                                        $other_rel_var = 'ptOtherRelInfoReletion' . $loop;

                                                                                        $ff_name_var = $patientQryRes[0][$f_name_var];
                                                                                        $ff_phone_var = $patientQryRes[0][$f_phone_var];
                                                                                        $ff_rel_var = $patientQryRes[0][$f_rel_var];
                                                                                        $ff_other_rel_var = $patientQryRes[0][$f_other_rel_var];

                                                                                        $oclass='hidden';$sclass='inline';if($ff_rel_var=='Other'){$sclass='hidden';$oclass='inline';}
                                                                                        ?>
                                                                                        <tr>
                                                                                            <td data-label="Name"><input class="form-control" name="<?php echo $f_name_var; ?>" id="<?php echo $f_name_var; ?>" value="<?php echo $ff_name_var; ?>" data-prev-val="<?php echo addslashes($ff_name_var); ?>" /></td>
                                                                                            <td data-label="Phone"><input class="form-control" name="<?php echo $f_phone_var; ?>" id="<?php echo $f_phone_var; ?>" onChange="set_phone_format1(this,'<?php echo inter_phone_format(); ?>','','','form-control mandatory')" value="<?php echo $ff_phone_var; ?>" data-prev-val="<?php echo addslashes($ff_phone_var); ?>" /></td>
                                                                                            <td data-label="Relationship" class="text-nowrap">
                                                                                                <select name='<?php echo $f_rel_var; ?>' id="<?php echo $f_rel_var; ?>" class="form-control minimal <?php echo $sclass;?>" data-container="#rel_select_picker" title="<?php echo $patientQryRes[0][$f_rel_var]; ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" data-width="100%" data-size="10" data-tab-num="<?php echo $loop; ?>" data-dropdown-align-right="true" >
                                                                                                    <?php
                                                                                                    foreach ($arrHippaRelation as $s) {
                                                                                                        $value_print = ucfirst(strtolower($s));
                                                                                                        echo "<option value='".$s."'";
                                                                                                        if ($s == $patientQryRes[0][$f_rel_var] || empty($s)){
                                                                                                          echo " selected";
                                                                                                        }
                                                                                                        echo ">".(empty($value_print) ? '' : $value_print)."</option>\n";
                                                                                                    }
                                                                                                    ?>
                                                                                                </select>
                                                                                                <div id="otherRelInfoBox<?php echo $loop; ?>" class="<?php echo $oclass;?>">
                                                                                                    <div class="input-group" >
                                                                                                        <input class="form-control" name="<?php echo $f_other_rel_var; ?>" id="<?php echo $f_other_rel_var; ?>" value="<?php echo $ff_other_rel_var; ?>" data-prev-val="<?php echo addslashes($ff_other_rel_var); ?>" />
                                                                                                        <label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackRelInfoReletion<?php echo $loop; ?>" data-tab-name="relInfoReletion" data-tab-num="<?php echo $loop; ?>" style="color:white;">
                                                                                                            <i class="glyphicon glyphicon-arrow-left"></i>
                                                                                                        </label>
                                                                                                    </div>
                                                                                                </div> 	   
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php } ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                
												</div>	
											</div>
										</div>	
									</div>
									</div>		
								</div>
							</div>
                            
                            <!-- Start Responsible Party/Guarantor Grid -->
                            <div class="col-xs-12 col-sm-12 border " id="new_resp_container" style="display:none;">
                                <div class="adminbox" style="min-height:1px;">
                                    <div class="headinghd"><h4 class="pull-left">Responsible Party/Guarantor</h4></div>
                                </div>
                                
                                <input type="hidden" name="hid_resp_party_sel_our_sys" id="hid_resp_party_sel_our_sys" value="no"/>
                                <input type="hidden" name="hid_create_acc_resp_party" id="hid_create_acc_resp_party" value="no"/>
                                 
                                <div class="row mb10">
                                    <div class="col-sm-6">
                                        <div class="col-xs-12 "> 
                                            <!-- Title -->
                                            <div class="col-xs-3"	>
                                                <label>Title</label>
                                                <br>
                                                <select name="title1" id="title1" class="form-control minimal" data-width="100%"  title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>">
                                                    <option value=""> </option>
                                                    <option value="Mr.">Mr.</option>
                                                    <option value="Mrs.">Mrs.</option>
                                                    <option value="Ms.">Ms.</option>
                                                    <option value="Miss">Miss</option>
                                                    <option value="Master">Master</option>
                                                    <option value="Prof.">Prof.</option>
                                                    <option value="Dr.">Dr.</option>
                                                </select>
                                            </div>

                                            <!-- First Name -->
                                            <div class="col-xs-3  "	>
                                                <label>First Name</label>
                                                <br>
                                                <input  type="text" id="fname1" name="fname1" value="" class="form-control" />
                                            </div>

                                            <!-- Middle Name -->
                                            <div class="col-xs-3 ">
                                                <label>Middle Name</label>
                                                <br>
                                                <input  type="text" id="mname1"  name="mname1" value="" class="form-control" />
                                            </div>

                                            <!-- Last Name -->
                                            <div class="col-xs-3  "	>
                                                <label>Last Name</label>
                                                <br>
                                                <input  type="text" id="lname1" class="form-control" name="lname1" value="" onblur="search_patient_resp(this)" data-grid="0" />
                                            </div>
                                        </div>

                                        <div class="col-xs-12 ">
											<div class="row">
												<div class="col-xs-8">
													<!-- Suffix-->
													<div class="col-xs-4  "	>
														<label>Suffix</label>
														<br>
														<input  type="text" id="suffix1" name="suffix1" class="form-control" value=""  />
													</div>

													<!-- RelationShip -->
													<div class="col-xs-4 "	>
														<label>RelationShip</label>
														<br>
														<select name='relation1' id="relation1" class="form-control minimal" title="<?php echo imw_msg('drop_sel'); ?>" data-width="100%" data-header="<?php echo imw_msg('drop_sel'); ?>">
															<?php
															$relats = get_relationship_array('emergency_relation');
															foreach ($relats as $s) {
																if ($s == 'Doughter') {
																	echo "<option value='" . $s . "'"; echo ">Daughter</option>\n";
																}else {
																	echo "<option value='" . $s . "'";if (empty($s)) echo " selected"; echo ">" . (empty($s) ? ' ' : ucfirst($s)) . "</option>\n";
																}
															}
															?>
														</select>

														<div id="relation1_oth" class="hidden">
															<div class="input-group ">
																<input type="text" value="" id="oth" class="form-control" name="other1"  />
																<label class="input-group-btn btn btn-xs btn-primary back_other" data-tab-name="relation1">
																	<span class="glyphicon glyphicon-arrow-left"></span>
																</label>
															</div>
														</div>

													</div>

													<!-- Marital Status -->
													<div class="col-xs-4  "	>
														<label>Marital Status</label>
														<br>
														<select name="status1" id="status1" class="form-control minimal" data-width="100%"  title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>">
															<?php
															foreach ($defaults['marital_status'] as $s) {
																$s = ucfirst($s);
																echo "<option value='" . ucwords($s) . "'"; if (empty($s)) echo " selected"; echo ">" . (empty($s) ? ' ' : ucwords($s)) . "</option>";
															}
															?>
														</select>
													</div>

													<div class="clearfix"></div>
													
													 <!-- DOB -->
													 <div class="col-xs-3  "	>
														<label>Dob&nbsp;<small>(<?php echo inter_date_format(); ?>)</small></label>
														<br>
														<input type="hidden" name="from_date_byram1" id="from_date_byram1" value="<?php echo(get_date_format(date("Y-m-d"))); ?>">
														<div class="input-group">
															<input name="dob1" id="dob1" type="text"  class="form-control datepicker" title="<?php echo inter_date_format(); ?>" value=''  maxlength="10" />
															<label class="input-group-addon btn" for="dob1">
																<span class="glyphicon glyphicon-calendar"></span>
															</label>
														</div>
													</div>

													<!-- Sex-->
													<div class="col-xs-2 ">
														<label>Sex</label>
														<br>
														<select name="sex1" id="sex1" class="form-control minimal" title="<?php echo imw_msg('drop_sel'); ?>" data-width="100%" data-header="<?php echo imw_msg('drop_sel'); ?>" >
															<option value=""> </option>
															<option value="Male">Male</option>
															<option value="Female">Female</option>
														</select>
													</div>

													<!-- Social Security -->
													<div class="col-xs-3 "	>
														<label>Social Security#</label>
														<br>
														<input name="ss1" id="ss1" type="text" class="form-control" value="" onblur="if(this.value!='')validate_ssn(this);" maxlength="<?php echo inter_ssn_length(); ?>" size="<?php echo inter_ssn_length(); ?>">
													</div>

													<!-- Relaese HIPAA Info -->
													<div class="col-xs-4  form-inline"><br>
														<div class="checkbox"><input type="checkbox" id="chkHippaRelResp" name="chkHippaRelResp" value="1" ><label for="chkHippaRelResp"><span class="text-red">Release HIPAA Info</span></label></div>
													</div>
												</div>

												<div class="col-xs-4">
													<!-- Driving License -->
													<div class="col-xs-7 "	>
														<label>Driving&nbsp;License#</label>
														<input name="dlicence1" id="dlicence1" type="text" class="form-control" value="" />
														<span class="clearfix"></span>
														<button class="btn btn-primary f-bold width-100 mt10" type="button" id="btPatRespScanDo" onClick="scan_licence_new('rp');">License</button>
														<input name="resp_license_image" id="resp_license_image" type="hidden" value="" />
													</div>
													
													<!-- Upload License -->
													<div class="col-xs-5">
														<div class="previmg" id="respLicDiv"></div>
													</div>

												</div>

											</div>
										</div>
										
										<?php if(isERPPortalEnabled()) { ?>
											<div class="clearfix"></div>
											<div class="col-xs-12" style="margin-bottom:10px;"> 
												<div class="head">Responsible Party Credentials for ERP Portal</div>
												<div class="col-xs-4">
													<label>Username</label>
													<br>
													<input name='erp_resp_username' id="erp_resp_username" type='text' class="form-control" value=""/>
                                                </div>
												<div class="col-xs-4">
													<label>Password</label>
													<br>
													<input name='erp_resp_passwd' id="erp_resp_passwd" type='password' class="form-control" value="" />
													<input name='erp_hidd_passwd' id="erp_hidd_passwd" type='hidden' class="form-control" value="" />
												</div>
												<div class="col-xs-4">
													<label>Confirm Password</label>
													<br>
													<input name='erp_resp_cpasswd' id="erp_resp_cpasswd" type='password' class="form-control" value="" />
												</div>
											</div>
										<?php } ?>
										
									</div>
                                    <div class="col-sm-6">
                                        <div class="col-xs-12 "> 
                                            <!-- Street1 -->
                                            <div class="col-xs-5 "	>
                                                <label>Street 1</label>
                                                <br>
                                                <input name="street1" id="street1" type="text" class="form-control" value="" />
                                            </div>

                                            <!-- Street2 -->
                                            <div class="col-xs-4 "	>
                                                <label>Street 2</label>
                                                <br>
                                                <input name="street_emp" type="text" class="form-control" id="street_emp" value="" />
                                            </div>

                                            <!-- Zip Code -->
                                            <div class="col-xs-3 "	>
                                                <label><?php getZipPostalLabel(); ?></label>
                                                <div class="row">
                                                    <div class="col-xs-<?php echo (inter_zip_ext() ? '7' : '12'); ?>" >
                                                        <input name="postal_code1" type="text" class="form-control" id="rcode" onblur="zip_vs_state(this,'rcity','rstate','country_code1');lostFocus(this,'', '');" onkeyup="callme(this,'');" value="" maxlength="<?php echo inter_zip_length(); ?>" size="<?php echo inter_zip_length(); ?>">
                                                    </div>
                                                    <?php if (inter_zip_ext()) { ?>
                                                        <div class="col-xs-5 ">
                                                            <input name="rzip_ext" type="text" class="form-control" id="rzip_ext" value="" maxlength="4" />
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 "> 
                                            <!-- City -->
                                            <div class="col-xs-3 ">
                                                <label>City</label>
                                                <br>
                                                <input name="city1"  type="text" class="form-control" id="rcity" value="" />
                                            </div>

                                            <!-- State -->
                                            <div class="col-xs-2 ">
                                                <label><?php echo ucwords(inter_state_label()); ?></label>
                                                <br>
                                                <input name="state2" type="text" class="form-control" id="rstate" maxlength="<?php if (inter_state_val() == "abb") echo '2'; ?>" value="" />
                                                <input name="country_code1" id="country_code1" type="hidden" class="form-control"  value="USA" disabled="disabled">
                                            </div>

                                            <!-- Email ID -->
                                            <div class="col-xs-7 "	>
                                                <label>Email-Id</label>
                                                <br>
                                                <input name='email1' id="email1" type='text' onkeyup="javascript:search_email(event,this,'div_email_section_resp')" onfocus="getFocusObj(this);" class="form-control" value="" />
                                                <div id="div_email_section_resp" class="input-group" style="position:absolute;">
                                                    <span class="input-group-btn">
                                                        <select class="btn hide"  size="5" style="width:100%">
                                                            <option selected value="@aol.com">aol.com</option>
                                                            <option value="@gmail.com">gmail.com</option>
                                                            <option value="@hotmail.com">hotmail.com</option>
                                                            <option value="@msn.com">msn.com</option>
                                                            <option value="@yahoo.com">yahoo.com</option>

                                                        </select>
                                                    </span>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-xs-12 "> 
                                            <!-- Home Phone -->
                                            <div class="col-xs-4 "	>
                                                <label>Home Phone <?php getHashOrNo(); ?></label>
                                                <br>
                                                <input name='phone_home1' id="phone_home1" type='text' onchange="set_phone_format1(this,'###-###-####','','','form-control')" class="form-control" value="" maxlength="<?php echo inter_phone_length(); ?>">
                                            </div>

                                            <!-- Work Phone -->
                                            <div class="col-xs-4 "	>
                                                <label>Work Phone <?php getHashOrNo(); ?></label>
                                                <br>
                                                <input name='phone_biz1' id="phone_biz1" type='text'  onchange="set_phone_format1(this,'###-###-####','','','form-control')" class="form-control" value="" maxlength="<?php echo inter_phone_length(); ?>"/>
                                            </div>

                                            <!-- Mobile Phone -->
                                            <div class="col-xs-4 "	>
                                                <label>Mobile Phone <?php getHashOrNo(); ?></label>
                                                <br>
                                                <input name='phone_cell1' id="phone_cell1" type='text'  onchange="set_phone_format1(this,'###-###-####','','','form-control')" class="form-control" value="" maxlength="<?php echo inter_phone_length(); ?>"/>
                                            </div>

                                        </div>
                                    </div>
                                </div>	
                            </div>
                            <!-- End Responsible Party/Guarantor Grid -->
              
							<!-- Insurance Block -->
							<div id="insurance_block" class="col-sm-12">	
								<div class="row">	
									<!-- Insurance Plan -->
									<div class="col-sm-12">
										<div class="row purple_bar">
											<div class="col-sm-12">
													<div class="pull-left">
														<label>Insurance&nbsp;Plan</label>
													</div>	
															
												<?php
													/*if(count($arrInsSwapData) > 0){
														?>
															<div class="pull-right"><a href="javascript:copy_insurance_div('block','primary');" class="text_10b_purpule"><span class="glyphicon glyphicon-duplicate" style="font-size:20px;color:white"></span></a></div>
														<?php
													}*/
												?>
												
												<div  class="pull-right mr10">
												<select name="choose_prevcase" tabindex="31" id="choose_prevcase" class="selectpicker" onChange="javascript:get_insurance_details(this.value,'<?php echo $patient_id; ?>')" data-size="5" data-width="100%" data-title="<?php echo imw_msg('drop_sel');?>">
													<?php echo $insCaseDropVal; ?>
												</select>
												</div>
												<!--rearrange insurance-->
												<?php
													if(count($arrInsSwapData) > 1){
														?><div  class="pull-right mr10 pt5">
															<span class="glyphicon glyphicon-transfer" style="font-size:20px;color:white;cursor:pointer" data-toggle="modal"  data-target="#divRearrangeIns" title="Rearrange Insurance"></span>
															</div>
														<?php
													}
												?>
												<!--self pay check box-->	
												<div class="pull-right mr10">
													<div class="checkbox">
														<input type="checkbox" name="cbk_self_pay_provider" tabindex="30" id="cbk_self_pay_provider" style="cursor:pointer;" <?php echo $self_pay_provider_val; ?> value="1" onClick="dis_insurance_com(this);" /><label for="cbk_self_pay_provider">Self Pay	</label>
													</div>
												</div>
												
												
											</div>
										</div>	
									</div>
								</div>
								<div class="row">	
									<div class="co-sm-12">
										<div class="row">
											<div class="adminbox">
												<!-- Primary Insurance -->
												<div class="col-sm-6">
													<div class="row">
														<div class="col-sm-12">
															<div class="headinghd">
																<div class="row">
																	<div class="col-sm-4">
																		<h4 class="pull-left">Primary Insurance</h4>		
																	</div>
																	<div class="col-sm-6">
																		<div class="row">
																			<div class="col-sm-2">
																				<div class="div_td pr5" id="accept_assignment_div" style="font-weight:bold;"></div>	  
																			</div>
																			<div class="col-sm-10">
																				<div class="row">
																					<div class="col-sm-2">
																						<?php echo $imgRealTimeEli; ?>	
																					</div>	
																					<div class="col-sm-10">
																						<?php 
																							$tooltip = show_tooltip($VSTOOLTIP); 
																							if(date('m-d-Y') == $VSRESPDATECOMP && $VSTRAN == 'sucss'){
																								$color = 'green';
																							}else if($VSTRAN == 'error'){
																								$color = 'red';	
																							}else{
																								$color = 'black';
																							}
																						?>
																						<div class="div_td link_cursor" <?php echo $tooltip; ?> style="overflow:hidden; color:<?php echo $color; ?>" onClick="get271Report('<?php echo $RTME_ID; ?>');">
																							<?php echo $VSSTATUS; ?>
																						</div>	
																					</div>		
																				</div>
																			</div>	
																		</div>	
																	</div>
                                                                    
																	<div class="col-sm-2 text-right pointer">
																		<?php echo $insDataArr['primary']['scan_card'].'&nbsp;'.$insDataArr['primary']['scan_card2']; ?>&nbsp;	
                                                                        <span class="glyphicon glyphicon-print pull-right pt5" id="anchor_primary_scan" onClick="javascript:return scan_card('Primary','<?php echo $insDataArr['primary']['MAIN_ID']; ?>');" title='Scan'></span>		
																	</div>	
																</div>
															</div>		
														</div>
														<div class="col-sm-offset-3 col-sm-6">
															<!-- Pop up div -->
															<a href="#" id="insPrimaryPop" data-content=""></a>
														</div>
														<div class="col-sm-12" >
															<div id="primaryInsDiv">
																<div class="row">
																	<div class="col-sm-4">
																			<label>Primary Ins. Provider</label>
																			<input type="hidden" name="i1providerRCOCode" id="i1providerRCOCode" value="<?php echo $insDataArr['primary']['rco_code']; ?>" />
																			<input type="hidden" name="i1providerRCOId" id="i1providerRCOId" value="<?php echo $insDataArr['primary']['rco_code_id']; ?>" />
																			<input type="hidden" name="insurance_primary_id" id="insurance_primary_id" value="<?php echo $insDataArr['primary']['MAIN_ID']; ?>">	
																			<input tabindex="32" type="text" class="form-control" id="insPriProv" name="insPriProv" data-sort="contain" value="<?php echo $insDataArr['primary']['ins_name']; ?>"  onChange="callme(this,'form-control'); enable_insurance_options('Primary');get_accept_assignment(); " onBlur="javascript:lostFocus(this,'form-control', '');" autocomplete="off" onMouseOver="getToolTip(document.getElementById('insPriProv_id').value,'insPrimaryPop','<?php echo $insDataArr['primary']['rco_code']; ?>');" onMouseOut="closeWindow('insPrimaryPop');">
																			<input type="hidden" name="insPriProv_id" id="insPriProv_id" value="<?php echo $insDataArr['primary']['ins_com_id']; ?>">	
																	</div>	
																	<div class="col-sm-4">
																		<label id="Primary_Policy"><?php echo $policyFld; ?></label>
																		 <input value="<?php echo $insDataArr['primary']['policy_number']; ?>" <?php echo $insDisabled; ?> tabindex="33" type="text" id="insPriPolicy" name="insPriPolicy" class="form-control" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">	
																	</div>
																	<div class="col-sm-4">
																		<label id="Primary_Group"><?php echo $groupFld; ?></label>
																		<input value="<?php echo $insDataArr['primary']['group_number']; ?>" <?php echo $insDisabled; ?> tabindex="34" type="text" id="insPriGroup" name="insPriGroup" class="form-control" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">
																	</div>	
																</div>
																<div class="row pt10">
																	<div class="col-sm-4">
																	 <div class="row">
																		<div class="col-sm-4">
																				<label id="Primary_Copay"><?php echo $copayFld; ?></label>
																				<input value="<?php echo $insDataArr['primary']['copay']; ?>" <?php echo $insDisabled; ?> tabindex="35" type="text" id="insPriCopay" name="insPriCopay" class="form-control" size="3" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">	
																		</div>

																		<div class="col-sm-4" id="pri_coins_div" style="display:<?php echo $coins_dis;?>">
																				<label id="insPriCoIns">Co-Ins</label>
																				<input value="<?php echo $insDataArr['primary']['co_ins']; ?>" <?php echo $insDisabled; ?> tabindex="35" type="text" id="insPriCoIns" name="insPriCoIns" class="form-control" size="3" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">	
																		</div>

																		<div id="pri_ref_div" class="col-sm-4" style="display:<?php echo $normal_dis;?>">
																			<label>Ref. Req.</label>
																			<select <?php echo $insDisabled; ?> name="pri_ref_req" id="pri_ref_req" class="selectpicker" tabindex="36" onChange="refReqChk(this);" data-width="100%" data-title="<?php echo imw_msg('drop_sel');?>">
																				<option value="No" selected>No</option>
																				<option value="Yes" <?php echo $insDataArr['primary']['referal_required']; ?>>Yes</option>
																			</select>
																		</div>

																	 </div>
																	</div>
																	
																	<div id="pri_auth_div" class="col-sm-2">
																		<label>Auth Req.</label>
																		<select <?php echo $insDisabled; ?> name="pri_auth_req" id="pri_auth_req" class="selectpicker" tabindex="36" onChange="authReqChk(this);" data-width="100%" data-title="<?php echo imw_msg('drop_sel');?>">
																			<option value="No" selected>No</option>
																			<option value="Yes" <?php echo $insDataArr['primary']['auth_required']; ?>>Yes</option>
																		</select>
																	</div>	
																	
																	<div class="col-sm-3">
																		<label>Activation Date</label>
																		<div class="input-group">
																			<input <?php echo $insDisabled; ?> name="insPriActDt" id="insPriActDt" type="text" class="date-pick form-control" tabindex="37" title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);"  value="<?php echo $insDataArr['primary']['effective_date']; ?>" maxlength="10" onFocus="getFocusObj(this);"/>	
																			<div class="input-group-addon">
																				<span class="glyphicon glyphicon-calendar"></span>
																			</div>
																		</div>
																	</div>	
																	
																	<div class="col-sm-3">
																		<label>Expiration Date</label>
																		<div class="input-group">
																			<input <?php echo $insDisabled; ?> name="insPriExpDt" id="insPriExpDt" type="text" class="date-pick form-control" tabindex="37" title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);"  value="<?php echo $insDataArr['primary']['expiration_date']; ?>" maxlength="10" onFocus="getFocusObj(this);"/>
																			<div class="input-group-addon">
																				<span class="glyphicon glyphicon-calendar"></span>	
																			</div>
																		</div>	
																		
																	</div> 
																	<div class="col-sm-2" id="primary_ins_doc">
																		<?php //echo $insDataArr['primary']['scan_card'].'&nbsp;'.$insDataArr['primary']['scan_card2']; ?>
																	</div>	
																</div>	
															</div>
															
														</div>
													</div>
												</div>	
												
												<!-- Secondary Insurance -->
												<div class="col-sm-6">
													<div class="row">
														<div class="col-sm-12">
															<div class="headinghd">
																<div class="row">
																	<div class="col-sm-4">
																		<h4 class="pull-left">Secondary Insurance</h4>		
																	</div>
                                                                    <div class="col-sm-6">
                                                                    	<?php
																		$sec_rte_icon_resp_arr =secondary_rte_icon($edit_patient_id,$insDataArr['secondary']['MAIN_ID']);
																		?>
																		<div class="row">
                                                                            <div class="col-sm-2">
                                                                                <?php echo $sec_rte_icon_resp_arr[0] ?>	
                                                                            </div>	
                                                                            <div class="col-sm-10">
                                                                                <div class="div_td link_cursor" style="overflow:hidden;" onClick="get271Report('<?php echo $sec_rte_icon_resp_arr[1]; ?>');"><?php echo $sec_rte_icon_resp_arr[2]; ?></div>	
                                                                            </div>		
																		</div>	
																	</div>
                                                                    
                                                                    
																	<div class="col-sm-2 text-right pt8 pointer">
																		<?php echo $insDataArr['secondary']['scan_card']."&nbsp;".$insDataArr['secondary']['scan_card2'] ; ?>&nbsp;
                                                                        <span class="glyphicon glyphicon-print" id="anchor_secondary_scan" onClick="javascript:scan_card('Secondary','<?php echo $insDataArr['secondary']['MAIN_ID']; ?>');" title='Scan'></span>
																	</div>	
																</div>
															</div>	
														</div>
														<div class="col-sm-12">
															<div  id="secondaryInsDiv">
																<div class="row">
																	<div class="col-sm-offset-3 col-sm-6">
																		<!-- Pop up div -->
																		<a href="#" id="insSecondaryPop" data-content=""></a>
																	</div>
																	<div class="col-sm-4">
																		<label>Secondary Ins. Provider</label>
																		<input type="hidden" name="i2providerRCOCode" id="i2providerRCOCode" value="<?php echo $insDataArr['secondary']['rco_code']; ?>" />
																		<input type="hidden" name="i2providerRCOId" id="i2providerRCOId" value="<?php echo $insDataArr['secondary']['rco_code_id']; ?>" />
																		<input type="hidden" name="insurance_secondary_id" id="insurance_secondary_id" value="<?php echo $insDataArr['secondary']['MAIN_ID']; ?>">	
																		<input tabindex="42"  type="text" class="form-control" id="insSecProv" name="insSecProv" data-sort="contain" value="<?php echo $insDataArr['secondary']['ins_name']; ?>"  onChange="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');" onMouseOver="getToolTip(document.getElementById('insSecProv_id').value,'insSecondaryPop','<?php echo $insDataArr['secondary']['rco_code']; ?>');" autocomplete="off" onMouseOut="closeWindow('insSecondaryPop');" <?php //echo $secDisabled; ?>> 
																		<input type="hidden" name="insSecProv_id" id="insSecProv_id" value="<?php echo $insDataArr['secondary']['ins_com_id']; ?>">		
																	</div>
																	<div class="col-sm-4">
																		<label id="Secondary_Policy"><?php echo $policyFld; ?></label>
																		 <input value="<?php echo $insDataArr['secondary']['policy_number']; ?>"  tabindex="43" type="text" id="insSecPolicy" name="insSecPolicy" class="form-control" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">
																	</div>
																	<div class="col-sm-4">
																		<label id="Secondary_Group"><?php echo $groupFld; ?></label>
																		<input value="<?php echo $insDataArr['secondary']['group_number']; ?>" tabindex="44" type="text" id="insSecGroup" name="insSecGroup" class="form-control" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">
																	</div>	
																</div>	
																<div class="row pt10">
																	<div class="col-sm-2">
																		<label id="Secondary_Copay"><?php echo $copayFld; ?></label>
																		<input value="<?php echo $insDataArr['secondary']['copay']; ?>"  tabindex="45" type="text" id="insSecCopay" name="insSecCopay" class="form-control" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">
																	</div>
																	<div id="sec_ref_div" class="col-sm-2" style="display:<?php echo $normal_dis; ?>;">
																		<label>Ref. Req.</label>
																		<select  name="sec_ref_req" id="sec_ref_req" class="selectpicker" tabindex="46" onChange="refReqChk(this);" data-width="100%" data-title="<?php echo imw_msg('drop_sel');?>">
																			<option value="No" selected>No</option>
																			<option value="Yes" <?php echo $insDataArr['secondary']['referal_required']; ?>>Yes</option>
																		</select>
																	</div>
																	<div id="sec_auth_div" class="col-sm-2">
																		<label>Auth Req.</label>
																		<select <?php echo $insDisabled; ?> name="sec_auth_req" id="sec_auth_req" class="selectpicker" tabindex="46" onChange="authReqChk(this);" data-width="100%" data-title="<?php echo imw_msg('drop_sel');?>">
																			<option value="No" selected>No</option>
																			<option value="Yes" <?php echo $insDataArr['secondary']['auth_required']; ?>>Yes</option>
																		</select>
																	</div>	
																	<div class="col-sm-3">	
																		<label>Activation Date</label>
																		<div class="input-group">
																			<input  name="insSecActDt" id="insSecActDt" type="text" class="date-pick form-control" tabindex="47" title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);"  value="<?php echo $insDataArr['secondary']['effective_date']; ?>" maxlength="10" onFocus="getFocusObj(this);"/>
																			<div class="input-group-addon">
																				<span class="glyphicon glyphicon-calendar"></span>	
																			</div>	
																		</div>		
																	</div>

																	<div class="col-sm-3">
																		<label>Expiration Date</label>
																		<div class="input-group">
																			<input  name="insSecExpDt" id="insSecExpDt" type="text" class="date-pick form-control" tabindex="47" title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);"  value="<?php echo $insDataArr['secondary']['expiration_date'];?>" maxlength="10" onFocus="getFocusObj(this);"/>
																			<div class="input-group-addon">
																				<span class="glyphicon glyphicon-calendar"></span>
																			</div>	
																		</div>	
																	</div>	
																	
																	<div id="secondary_ins_doc" class="col-sm-2">
																		<?php //echo $insDataArr['secondary']['scan_card']."&nbsp;".$insDataArr['secondary']['scan_card2'] ; ?>
																	</div>	
																</div>	
															</div>	
														</div>
													</div>
												</div>
												<div class="clearfix"></div>
                                                
                                                
                                                <!-- Primary policy holder -->
                                                <div class="col-sm-6 primry_policyholder" id="insPolicy_pri_table">
													<div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="headinghd">
                                                                <h4>Primary Policy Holder </h4>	
                                                            </div>	
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <?php
                                                        
                                                        if ($insDataArr['primary']['subscriber_fname'] == '' && ($insDataArr['primary']['subscriber_relationship'] == '' || $insDataArr['primary']['subscriber_relationship'] == 'self')) {
                                                            $patientFname = $patientQryRes[0]['fname'];
                                                            $patientMname = $patientQryRes[0]['mname'];
                                                            $patientLname = $patientQryRes[0]['lname'];
                                                            $patientSuffix = $patientQryRes[0]['suffix'];
                                                        } else {
                                                            $patientFname = $insDataArr['primary']['subscriber_fname'];
                                                            $patientLname = $insDataArr['primary']['subscriber_lname'];
                                                            $patientMname = $insDataArr['primary']['subscriber_mname'];
                                                            $patientSuffix = $insDataArr['primary']['subscriber_suffix'];
                                                        }

                                                        if (trim($patientFname) != "" && trim($patientLname) != "") {
                                                            $hid_pri_subscriber_exits_our_sys = "yes";
                                                        } else {
                                                            $hid_pri_subscriber_exits_our_sys = "No";
                                                        }

                                                        ?>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="pri_subscriber_fname">First Name</label>
                                                                <input type="hidden" name="hid_pri_subscriber_exits_our_sys" id="hid_pri_subscriber_exits_our_sys" value="<?php echo $hid_pri_subscriber_exits_our_sys; ?>"/>
                                                                <input type="hidden" name="sub_pri_pat_id" id="sub_pri_pat_id" value="<?php echo $insDataArr['primary']['sub_pat_id']; ?>"/>
                                                                <input type="text" class="form-control" size="13" name="pri_subscriber_fname" id="pri_subscriber_fname" value="<?php echo stripslashes($patientFname); ?>" >
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="pri_lastName">Last Name</label>
                                                                <input size="11" class="form-control" type="text" name="pri_lastName" id="pri_lastName" value="<?php echo stripslashes($patientLname); ?>" onblur="search_patient_resp(this,'pri');" data-action="search_patient" data-grid="0" data-i-key="pri" />
                                                                <input size="11" class="form-control" type="hidden" name="pri_hidlastName" id="pri_hidlastName" value="<?php echo $patientQryRes[0]['lname']; ?>"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="pri_subscriber_mname">Middle</label>
                                                                <input type="text" class="form-control" size="5" name="pri_subscriber_mname" id="pri_subscriber_mname" value="<?php echo stripslashes($patientMname); ?>" >
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="pri_suffix_rel">Suffix</label>
                                                                <input size="2" type="text" id="pri_suffix_rel" name="pri_suffix_rel" class="form-control" value="<?php echo $patientSuffix; ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for=" ">Sub.Relation</label>
                                                                <select class="form-control minimal" name="pri_subscriber_relationship" id="pri_subscriber_relationship" onchange="popUpRelation(this.value,'primary',true,'','pri');" data-width="100%" data-size="5">
                                                                    <option value="" selected><?php echo imw_msg('drop_sel'); ?></option>
                                                                    <?php
                                                                    if ($insDataArr['primary']['subscriber_relationship'] == '') {
                                                                        $subscriber_relationship = 'self';
                                                                    } else {
                                                                        $subscriber_relationship = $insDataArr['primary']['subscriber_relationship'];
                                                                    }
                                                                    $selectOption = '';
                                                                    foreach ($relats as $val) {
                                                                        $subscriber_relationship = preg_replace('/Doughter/', 'Daughter', $subscriber_relationship);
                                                                        if (strtolower($val) == strtolower($subscriber_relationship)) {
                                                                            $sel = 'selected="selected"';
                                                                        } else {
                                                                            $sel = '';
                                                                        }
                                                                        $selectOption .= '<option value="' . $val . '" ' . $sel . '>' . ucfirst($val) . '</option>';
                                                                    }
                                                                    echo $selectOption;
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for=" ">S.S</label>
                                                                <?php
                                                                if ($insDataArr['primary']['subscriber_ss'] == '' && ($insDataArr['primary']['subscriber_relationship'] == '' || $insDataArr['primary']['subscriber_relationship'] == 'self')) {
                                                                    $subscriber_ss = $insDataArr['primary']['ss'];
                                                                } else {
                                                                    $subscriber_ss = $insDataArr['primary']['subscriber_ss'];
                                                                }
                                                                ?>
                                                                <input type="text" size="20" name="pri_subscriber_ss" id="pri_subscriber_ss" value="<?php echo stripslashes($subscriber_ss); ?>" class="form-control" onBlur="javascript:lostFocus(this,'', '');if(this.value!='')validate_ssn(this);" >
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="pri_subscriber_DOB">DOB</label>
                                                                <input type="hidden" name="pri_from_date_subscriber" id="pri_from_date_subscriber" value="<?php echo get_date_format(date("Y-m-d")); ?>" >
                                                                <?php $subscriber_DOB=$insDataArr['primary']['subscriber_DOB']; ?>
                                                                <div class="input-group">
                                                                    <input type="text" maxlength="10" name='pri_subscriber_DOB' id="pri_subscriber_DOB" value='<?php echo $subscriber_DOB; ?>' title='<?php echo inter_date_format(); ?>' class="datepicker form-control" />
                                                                    <label class="input-group-addon btn" for="pri_subscriber_DOB">
                                                                        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="pri_subscriber_sex">Gender</label>
                                                                <?php
                                                                if ($patientQryRes[0]['sex'] != '' && $patientQryRes[0]['sex'] != $insDataArr['primary']['subscriber_sex'] && ($insDataArr['primary']['subscriber_relationship'] == '' || $insDataArr['primary']['subscriber_relationship'] == 'self' || $insDataArr['primary']['subscriber_relationship'] == 'Self')) {
                                                                    $subscriber_sex = ucfirst($patientQryRes[0]['sex']);
                                                                } else {
                                                                    $subscriber_sex = ucfirst($insDataArr['primary']['subscriber_sex']);
                                                                }
                                                                ?>
                                                                <select name="pri_subscriber_sex" id="pri_subscriber_sex" data-width="100%" class="form-control minimal" title="Select" >
                                                                    <option value="" selected>&nbsp;</option>
                                                                    <option value="Male" <?php if ($subscriber_sex == 'Male') echo 'selected="selected"'; ?> >Male</option>
                                                                    <option value="Female" <?php if ($subscriber_sex == 'Female') echo 'selected="selected"'; ?> >Female</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="pri_accept_assignment">Accept Assignment</label>
                                                                <select id="pri_accept_assignment" name="pri_accept_assignment" class="form-control minimal" data-width="100%" disabled="disabled">
                                                                    <option value="0">Accept Assignment</option>
                                                                    <option value="1">NAA - Courtesy Billing</option>
                                                                    <option value="2">NAA - No Courtesy Billing</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="pri_comments">Comments</label>
                                                                <textarea rows="2" class="form-control" name="pri_comments" id="pri_comments" placeholder="Comments..." ><?php echo stripslashes($insDataArr['primary']['comments']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-sm-6">
                                                            <div class="clearfix">&nbsp;</div>
                                                            <div class="checkbox checkbox-inline ">
                                                                <input name="pri_paymentauth" id="pri_paymentauth" type="checkbox" checked />
                                                                <label for="pri_paymentauth">Pymt. Auth</label>
                                                            </div>
                                                            <div class="checkbox checkbox-inline ">
                                                                <input name="pri_signonfile" id="pri_signonfile" type="checkbox" checked />
                                                                <label for="pri_signonfile">Sign. on File</label>
                                                            </div>
                                                        </div> -->
                                                        
                                                    </div>
                                                    
                                                </div>
                                                
                                                
                                                
                                                <!-- Secondary policy holder -->
                                                <div class="col-sm-6 secnd_policyholder" id="insPolicy_sec_table">
													<div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="headinghd">
                                                                <h4>Secondary Policy Holder </h4>	
                                                            </div>	
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <?php
                                                        
                                                        if ($insDataArr['secondary']['subscriber_fname'] == '' && ($insDataArr['secondary']['subscriber_relationship'] == '' || $insDataArr['secondary']['subscriber_relationship'] == 'self')) {
                                                            $secPatientFname = $patientQryRes[0]['fname'];
                                                            $secPatientMname = $patientQryRes[0]['mname'];
                                                            $secPatientLname = $patientQryRes[0]['lname'];
                                                            $secPatientSuffix = $patientQryRes[0]['suffix'];
                                                        } else {
                                                            $secPatientFname = $insDataArr['secondary']['subscriber_fname'];
                                                            $secPatientLname = $insDataArr['secondary']['subscriber_lname'];
                                                            $secPatientMname = $insDataArr['secondary']['subscriber_mname'];
                                                            $secPatientSuffix = $insDataArr['secondary']['subscriber_suffix'];
                                                        }

                                                        if (trim($secPatientFname) != "" && trim($secPatientLname) != "") {
                                                            $hid_sec_subscriber_exits_our_sys = "yes";
                                                        } else {
                                                            $hid_sec_subscriber_exits_our_sys = "No";
                                                        }
                                                        ?>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="sec_subscriber_fname">First Name</label>
                                                                <input type="hidden" name="hid_sec_subscriber_exits_our_sys" id="hid_sec_subscriber_exits_our_sys" value="<?php echo $hid_sec_subscriber_exits_our_sys; ?>"/>
                                                                <input type="hidden" name="sub_sec_pat_id" id="sub_sec_pat_id" value="<?php echo $insDataArr['secondary']['sub_pat_id']; ?>"/>
                                                                <input type="text" class="form-control" size="13" name="sec_subscriber_fname" id="sec_subscriber_fname" value="<?php echo stripslashes($secPatientFname); ?>" >
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="sec_lastName">Last Name</label>
                                                                <input size="11" class="form-control" type="text" name="sec_lastName" id="sec_lastName" value="<?php echo stripslashes($secPatientLname); ?>" onBlur="search_patient_resp(this,'sec');" data-action="search_patient" data-grid="0" data-i-key="sec" />
                                                                <input size="11" class="form-control" type="hidden" name="sec_hidlastName" id="sec_hidlastName" value="<?php echo $patientQryRes[0]['lname']; ?>"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="sec_subscriber_mname">Middle</label>
                                                                <input type="text" class="form-control" size="5" name="sec_subscriber_mname" id="sec_subscriber_mname" value="<?php echo stripslashes($secPatientMname); ?>" >
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="sec_suffix_rel">Suffix</label>
                                                                <input size="2" type="text" id="sec_suffix_rel" name="sec_suffix_rel" class="form-control" value="<?php echo $patientSuffix; ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for=" ">Sub.Relation</label>
                                                                <select class="form-control minimal" name="sec_subscriber_relationship" id="sec_subscriber_relationship" onChange="popUpRelation(this.value,'secondary',true,'','sec');" data-width="100%" data-size="5">
                                                                    <option value="" selected><?php echo imw_msg('drop_sel'); ?></option>
                                                                    <?php
                                                                    if ($insDataArr['secondary']['subscriber_relationship'] == '') {
                                                                        $sec_subscriber_relationship = 'self';
                                                                    } else {
                                                                        $sec_subscriber_relationship = $insDataArr['secondary']['subscriber_relationship'];
                                                                    }
                                                                    $selectOption = '';
                                                                    foreach ($relats as $val) {
                                                                        $sec_subscriber_relationship = preg_replace('/Doughter/', 'Daughter', $sec_subscriber_relationship);
                                                                        if (strtolower($val) == strtolower($sec_subscriber_relationship)) {
                                                                            $sel = 'selected="selected"';
                                                                        } else {
                                                                            $sel = '';
                                                                        }
                                                                        $selectOption .= '<option value="' . $val . '" ' . $sel . '>' . ucfirst($val) . '</option>';
                                                                    }
                                                                    echo $selectOption;
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for=" ">S.S</label>
                                                                <?php
                                                                if ($insDataArr['secondary']['subscriber_ss'] == '' && ($insDataArr['secondary']['subscriber_relationship'] == '' || $insDataArr['secondary']['subscriber_relationship'] == 'self')) {
                                                                    $sec_subscriber_ss = $insDataArr['secondary']['ss'];
                                                                } else {
                                                                    $sec_subscriber_ss = $insDataArr['secondary']['subscriber_ss'];
                                                                }
                                                                ?>
                                                                <input type="text" size="20" name="sec_subscriber_ss" id="sec_subscriber_ss" value="<?php echo stripslashes($sec_subscriber_ss); ?>" class="form-control" onBlur="javascript:lostFocus(this,'', '');if(this.value!='')validate_ssn(this);" >
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="sec_subscriber_DOB">DOB</label>
                                                                <input type="hidden" name="sec_from_date_subscriber" id="sec_from_date_subscriber" value="<?php echo get_date_format(date("Y-m-d")); ?>" >
                                                                <?php $sec_subscriber_DOB=$insDataArr['secondary']['subscriber_DOB']; ?>
                                                                <div class="input-group">
                                                                    <input type="text" maxlength="10" name='sec_subscriber_DOB' id="sec_subscriber_DOB" value='<?php echo $sec_subscriber_DOB; ?>' title='<?php echo inter_date_format(); ?>' class="datepicker form-control" />
                                                                    <label class="input-group-addon btn" for="sec_subscriber_DOB">
                                                                        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="sec_subscriber_sex">Gender</label>
                                                                <?php
                                                                if ($patientQryRes[0]['sex'] != '' && $patientQryRes[0]['sex'] != $insDataArr['secondary']['subscriber_sex'] && ($insDataArr['secondary']['subscriber_relationship'] == '' || $insDataArr['secondary']['subscriber_relationship'] == 'self' || $insDataArr['secondary']['subscriber_relationship'] == 'Self')) {
                                                                    $sec_subscriber_sex = ucfirst($patientQryRes[0]['sex']);
                                                                } else {
                                                                    $sec_subscriber_sex = ucfirst($insDataArr['secondary']['subscriber_sex']);
                                                                }
                                                                ?>
                                                                <select name="sec_subscriber_sex" id="sec_subscriber_sex" data-width="100%" class="form-control minimal" title="Select" >
                                                                    <option value="" selected>&nbsp;</option>
                                                                    <option value="Male" <?php if ($sec_subscriber_sex == 'Male') echo 'selected="selected"'; ?> >Male</option>
                                                                    <option value="Female" <?php if ($sec_subscriber_sex == 'Female') echo 'selected="selected"'; ?> >Female</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="sec_comments">Comments</label>
                                                                <textarea rows="2" class="form-control" name="sec_comments" id="sec_comments" placeholder="Comments..." ><?php echo stripslashes($insDataArr['secondary']['comments']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-sm-6">
                                                            <div class="clearfix">&nbsp;</div>
                                                            <div class="checkbox checkbox-inline ">
                                                                <input name="sec_paymentauth" id="sec_paymentauth" type="checkbox" checked />
                                                                <label for="sec_paymentauth">Pymt. Auth</label>
                                                            </div>
                                                            <div class="checkbox checkbox-inline ">
                                                                <input name="sec_signonfile" id="sec_signonfile" type="checkbox" checked />
                                                                <label for="sec_signonfile">Sign. on File</label>
                                                            </div>
                                                        </div> -->
                                                        
                                                    </div>
                                                    
                                                </div>
												<div class="clearfix"></div>
                                                
                                                
												<!-- Primary Refferal Information -->
												<div class="col-sm-6">
													<div class="row">
														<div class="col-sm-12 pt10" style="height:150px;display:<?php echo $primary_referal_required_dis; ?>" id="refRaqDiv">
															<div class="row">
																<div class="col-sm-12">
																	<div class="headinghd">
																		<h4>Referral Information </h4>	
																	</div>	
																</div>	
																<div class="col-sm-12">
																	<div class="row">
																		<div class="col-sm-3">
																			<label>Referring Physician</label>
																			<input type="hidden" name="pri_reff_id" id="pri_reff_id" value="<?php echo $insDataArr['primary']['reff_id']; ?>">
																			<input type="hidden" name="pri_ref_phy_id" id="pri_ref_phy_id" value="<?php echo $insDataArr['primary']['pri_ref_phy_id']; ?>">
																			<div class="input-group">
																				<input type="text" class="form-control" tabindex="39" <?php echo $insDisabled; ?> name="pri_ref_phy" id="pri_ref_phy" value="<?php echo $insDataArr['primary']['pri_ref_phy']; ?>"  onKeyUp="loadPhysicians(this,'pri_ref_phy_id');" onFocus="loadPhysicians(this,'pri_ref_phy_id');">
																				<div class="input-group-addon">
																					<span id="pri_ref_image" tabindex="39" class="glyphicon glyphicon-search" onClick="javascript:search_physician_popup('pri_ref_phy');" style="cursor:pointer; border:none;"></span>	
																				</div>	
																			</div>
																		</div>	
																		<div class="col-sm-2">
																			<label>Visits</label>
																			 <input <?php echo $insDisabled; ?> tabindex="40" name="pri_ref_visits" id="pri_ref_visits" size="2" type="text" class="form-control" value="<?php echo $insDataArr['primary']['pri_ref_visits']; ?>" onFocus="getFocusObj(this);"/>
																		</div>
																		<div class="col-sm-2">
																			<label>Referral#</label>
																			<input <?php echo $insDisabled; ?> tabindex="40" name="pri_ref_number" id="pri_ref_number" type="text" class="form-control" value="<?php echo $insDataArr['primary']['reffral_no']; ?>" onFocus="getFocusObj(this);"/>
																		</div>
																		<div class="col-sm-5">
																			<div class="row">
																				<div class="col-sm-6">
																					<label>Start Date</label>
																					<div class="input-group">
																						<input <?php echo $insDisabled; ?> tabindex="40" name="pri_ref_stDt" id="pri_ref_stDt" type="text" class="date-pick form-control"  title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);"  value="<?php if($insDataArr['primary']['pri_ref_stDt'] != '00-00-0000'){echo $insDataArr['primary']['pri_ref_stDt'];} ?>" maxlength="10" onFocus="getFocusObj(this);"/>
																							<div class="input-group-addon">
																								<span class="glyphicon glyphicon-calendar"></span>	
																							</div>	
																					</div>	
																				</div>	
																				<div class="col-sm-6"> 
																					<label>End Date</label>
																					<div class="input-group">
																						<input <?php echo $insDisabled; ?> tabindex="40"  name="pri_ref_enDt" id="pri_ref_enDt" type="text" class="date-pick form-control"  title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);"  value="<?php if($insDataArr['primary']['pri_ref_enDt'] != '00-00-0000'){echo $insDataArr['primary']['pri_ref_enDt'];}  ?>" maxlength="10" onFocus="getFocusObj(this);"/>	
																						<div class="input-group-addon">
																							<span class="glyphicon glyphicon-calendar"></span>	
																						</div>
																					</div>
																				</div>	
																			</div>	
																		</div>
																	</div>	
																</div>	
															</div>	
														</div>		
													</div>	
												</div>
												<!-- Secondary Refferal Information -->
												<div class="col-sm-6">
													<div class="row pt10">
														<div class="col-sm-12" style="height:150px;display:<?php echo $secondary_referal_required_dis; ?>;" id="secRefRaqDiv">
															<div class="row">
																<div class="col-sm-12">
																	<div class="headinghd">
																		<h4>Referral Information</h4>	
																	</div>	
																</div>	
																<div class="col-sm-3">
																	<label>Reffering Physician</label>
																	<input type="hidden" name="sec_reff_id" id="sec_reff_id" value="<?php echo $insDataArr['secondary']['reff_id']; ?>">
																	<input type="hidden" name="sec_ref_phy_id" id="sec_ref_phy_id" value="<?php echo $insDataArr['secondary']['pri_ref_phy_id']; ?>">
																	<div class="input-group"> 
																		<input tabindex="49" type="text" name="sec_ref_phy" class="form-control" id="sec_ref_phy" value="<?php echo $insDataArr['secondary']['pri_ref_phy']; ?>" size="9" onKeyUp="loadPhysicians(this,'sec_ref_phy_id');" onFocus="loadPhysicians(this,'sec_ref_phy_id');">
																		<div class="input-group-addon">
																			<span class="glyphicon glyphicon-search" onClick="javascript:search_physician_popup('sec_ref_phy');" style="cursor:pointer; border:none;"></span>
																		</div>	
																	</div>
																</div>	
																<div class="col-sm-2">
																	<label>Visits</label>
																	 <input  tabindex="50" name="sec_ref_visits" id="sec_ref_visits" type="text" class="form-control" value="<?php echo $insDataArr['secondary']['pri_ref_visits']; ?>" onFocus="getFocusObj(this);"/>
																</div>
																<div class="col-sm-2">
																	<label>Referral#</label>
																	 <input  tabindex="51" name="sec_ref_number" id="sec_ref_number" type="text" class="form-control" value="<?php echo $insDataArr['secondary']['reffral_no']; ?>"  onFocus="getFocusObj(this);"/>
																</div>
																<div class="col-sm-5">
																	<div class="row">
																		<div class="col-sm-6">
																			<label>Start Date</label>
																			<div class="input-group">
																				<input  tabindex="51" name="sec_ref_stDt" id="sec_ref_stDt" type="text" class="date-pick form-control"  title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);"  value="<?php echo $insDataArr['secondary']['pri_ref_stDt']; ?>" maxlength="10" onFocus="getFocusObj(this);"/>
																				<div class="input-group-addon">
																					<span class="glyphicon glyphicon-calendar"></span>	
																				</div>	
																			</div>	
																		</div>
																		<div class="col-sm-6">
																			<label>End Date</label>
																			<div class="input-group">
																				<input  tabindex="51" name="sec_ref_enDt" id="sec_ref_enDt" type="text" class="date-pick form-control"  title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);"  value="<?php echo $insDataArr['secondary']['pri_ref_enDt']; ?>" maxlength="10" onFocus="getFocusObj(this);"/>
																				<div class="input-group-addon">
																					<span class="glyphicon glyphicon-calendar"></span>	
																				</div>	
																			</div>	
																		</div>	
																	</div>	
																</div>	
															</div>	
														</div>	
													</div>	
												</div>
												<div class="clearfix"></div>	
												<!-- Primary Auth Information -->
												<div class="col-sm-6">
													<div class="row pt10">
															<div class="col-sm-12" id="authReqDisDiv" style="height:150px;display:<?php echo $primary_auth_required_dis; ?>">
																<div class="row">
																	<div class="col-sm-12">
																		<div class="headinghd">
																			<h4>Auth Information</h4>
																		</div>	
																	</div>	
																	<div class="col-sm-3">
																		<label>Primary Auth Number</label>
																		<input type="hidden" name="" value="<?php print_r($auth_pri_arr); ?>">
																		<input type="hidden" name="auth_pri_id" id="auth_pri_id" value="<?php echo $authDataArr[1]['a_id'][0]?>">
																		<div class="input-group">
																			<input value="<?php echo $authDataArr[1]['auth_name'][0] ?>" <?php echo $insDisabled; ?> type="text" id="AuthPriNumber" name="AuthPriNumber" class="form-control" size="17" onChange="set_auth_info(this,'AuthPriAmount','auth_pri_id','pri_auth_date');" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">
																			<?php echo get_simple_menu($auth_pri_arr,"pri_simple_menu","AuthPriNumber"); ?>
																		</div>
																	</div>	
																	<div class="col-sm-2">
																		<label>Auth Amount</label>	
																		<input value="<?php echo $authDataArr[1]['AuthAmount'][0]; ?>" <?php echo $insDisabled; ?> type="text" id="AuthPriAmount" name="AuthPriAmount" class="form-control" size="10" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">	
																	</div>
																	<div class="col-sm-5">
																		<div class="row">
																			<div class="col-sm-6">
																				<label>Auth Date</label>
																				<div class="input-group">
																					<input <?php echo $insDisabled; ?> name="pri_auth_date" id="pri_auth_date" type="text" class="date-pick form-control" value="<?php echo $authDataArr[1]['auth_date'][0] ?>" title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);" maxlength="10" onFocus="getFocusObj(this);"/>	
																					<div class="input-group-addon">
																						<span class="glyphicon glyphicon-calendar"></span>	
																					</div>
																				</div>
																			</div>	
																			<div class="col-sm-6">
																				<label>End Date</label>
																				<div class="input-group">
																					 <input <?php echo $insDisabled; ?> name="pri_auth_date_end" id="pri_auth_date_end" type="text" class="date-pick form-control" value="<?php echo $authDataArr[1]['end_date'][0]; ?>" title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);" maxlength="10" onFocus="getFocusObj(this);"/>
																					<div class="input-group-addon">
																						<span class="glyphicon glyphicon-calendar"></span>	
																					</div>
																				</div>	
																			</div>	
																		</div>
																	</div>
																	<div class="col-sm-2">
																		<label>Visits</label>	
																		 <input <?php echo $insDisabled; ?> name="pri_auth_visits" id="pri_auth_visits" type="text"  value="<?php echo $authDataArr[1]['visits'][0]; ?>" class="form-control" maxlength="10" onFocus="getFocusObj(this);"/>	
																	</div>	
																</div>
															</div>
													</div>	
												</div>
												<!-- Secondary Auth Information -->												
												<div class="col-sm-6">
													<div class="row pt10">
															<div class="col-sm-12" id="secAuthReqDisDiv" style="height:150px;display:<?php echo $secondary_auth_required_dis; ?>">
																<div class="row">
																	<div class="col-sm-12">
																		<div class="headinghd">
																			<h4>Auth Information</h4>	
																		</div>	
																	</div>
																	<div class="col-sm-3">
																		<label>Secondary Auth Number</label>
																		<input type="hidden" name="" value="<?php print_r($authDataSecArr); ?>">
																		<input type="hidden" name="auth_sec_id" id="auth_sec_id" value="<?php echo $authDataArr[2]['a_id'][0]; ?>">
																		<div class="input-group">
																			<input value="<?php echo $authDataArr[2]['auth_name'][0]; ?>" <?php echo $secDisabled; ?> type="text" id="AuthSecNumber" name="AuthSecNumber" class="form-control" size="17" onChange="set_auth_info(this,'AuthSecAmount','auth_sec_id','sec_auth_date');" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">
																			<?php echo get_simple_menu($auth_sec_arr,"sec_simple_menu","AuthSecNumber"); ?>
																		</div>    
																	</div>
																	<div class="col-sm-2">
																		<label>Auth Amount</label>
																		<input value="<?php echo $authDataArr[2]['AuthAmount'][0]; ?>" <?php echo $secDisabled; ?> type="text" id="AuthSecAmount" name="AuthSecAmount" class="form-control" size="10" onKeyUp="callme(this,'form-control');" onBlur="javascript:lostFocus(this,'form-control', '');">	
																	</div>	
																	<div class="col-sm-5">
																		<div class="row">
																			<div class="col-sm-6">
																				<label>Auth Date</label>
																				<div class="input-group">
																					<input <?php echo $secDisabled; ?> name="sec_auth_date" id="sec_auth_date" type="text" class="date-pick form-control" value="<?php echo $authDataArr[2]['auth_date'][0]; ?>"  title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this);" maxlength="10" onFocus="getFocusObj(this);"/>
																					<div class="input-group-addon">
																						<span class="glyphicon glyphicon-calendar"></span>	
																					</div>	
																				</div>
																			</div>	
																			<div class="col-sm-6">
																				<label>End Date</label>
																				<div class="input-group">
																					 <input <?php echo $insDisabled; ?> name="sec_auth_date_end" id="sec_auth_date_end" type="text" class="date-pick form-control" value="<?php echo $authDataArr[2]['end_date'][0]; ?>" title="<?php inter_date_format(); ?>" onBlur="checkdate(this);" maxlength="10" onFocus="getFocusObj(this);"/>
																					<div class="input-group-addon">
																						<span class="glyphicon glyphicon-calendar"></span>	
																					</div>	
																				</div>	
																			</div>		
																		</div>	
																	</div>	
																	<div class="col-sm-2">
																		<label>Visits</label>
																		<input <?php echo $insDisabled; ?> name="sec_auth_visits" id="sec_auth_visits" type="text"  value="<?php echo $authDataArr[2]['visits'][0]; ?>" class="form-control"  maxlength="10" onFocus="getFocusObj(this);"/>
																	</div>	
																</div>	
															</div>		
													</div>	
												</div>
											</div>
										</div>	
									</div>
								</div>
							</div>	
							<!-- Eligibility -->
							<?php if(constant('ENABLE_REAL_ELIGILIBILITY') == 'YES' && $date_remain == 'YES'){ ?>
								<div class="col-sm-12 pt10 adminbox" style="min-height: inherit;">
									<div class="row">
											<div class="col-sm-12">
												<div class="headinghd">
													<h4>Real-time Eligibility Amount Information</h4>	
												</div>
											</div>	
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-4">
														Co-Pay: <?php if($intCopayAmtPre != ''){echo show_currency().$intCopayAmtPre;}else{echo 'N/A';} ?>	
													</div>	
													<div class="col-sm-4" style="<?php if ($RTE_OUT_DAYS_STATUS_COLOR != ''){?> color:<?php echo $RTE_OUT_DAYS_STATUS_COLOR ;} ?>">
														Deductible: <?php if($intDDCAmtPre != ''){echo show_currency().$intDDCAmtPre;}else{echo 'N/A';} ?>
													</div>	
													<div class="col-sm-4" style=" <?php if ($RTE_OUT_DAYS_STATUS_COLOR != ''){?> color:<?php echo $RTE_OUT_DAYS_STATUS_COLOR ;} ?>">
														Co-Insurance: <?php if($strCoInsAmtPre != ''){echo $strCoInsAmtPre.'%';}else{echo 'N/A';} ?>
													</div>		
												</div>	
											</div>	
									</div>
								</div>
							<?php } ?>
							<!-- Visit Payment -->
							<div class="co-sm-12" style="display:<?php echo $showViewPaymentsRow ; ?>">
								<div class="row">
									<div class="adminbox">
										<div class="col-sm-12">
											<?php 
												$chk_copay_dilated = 0;
												for($i = 0; $i<=count($checkInFieldsQryRes);$i++){
													if($checkInFieldsQryRes[$i]['item_amt']['dilated'] > 0 && $checkInFieldsQryRes[$i]['item_amt']['nondilated'] > 0){
														$chk_copay_dilated = 1;
													}
												}
												$default_currency = htmlspecialchars_decode(show_currency());
											?>
											<div class="headinghd">
												<h4 class="pull-left">Visit Payment</h4>
												
												<?php if($patientQryRes[0]['vip'] == 1){?> <span class="pull-right" style="color:#f00;">Patient is VIP</span><?php	} ?>
											</div>
										</div>
										<div class="col-sm-12">
													<div class="row">
														<input type="hidden" name="refraction_Chk" id="refraction_Chk" value="<?php echo $refractionChk; ?>">
															<?php
																$tabindex = 52;
																for($loop=0,$i=0;$loop<count($checkInFieldsQryRes);$i++,$loop++){
																	$chk_main_id = $checkInFieldsQryRes[$loop]['id'];
																	if($loop == 0){
																?>
																
																<div class = "col-sm-4">
																	<div class="row pt10">
																<?php } ?>	
																	<div class="col-sm-4">
																		<input type="hidden" name="chk_payment_detail_arr[<?php echo $chk_main_id; ?>]" value="<?php echo $checkInFieldsQryRes[$loop]['pay_detail_id']; ?>">
																		<div class="checkbox checkbox-inline">
																			<input <?php echo $visit_payment_readonly; ?> type="checkbox" tabindex="<?php echo $tabindex; ?>" style="cursor:pointer;" id="chkbox_<?php echo $chk_main_id; ?>" name="check_in_out_pay[]" <?php echo $checkInFieldsQryRes[$loop]['item_checked']; ?>  value="<?php echo $chk_main_id; ?>" onClick="fill_amt(this,'<?php echo $chk_main_id; ?>','<?php echo $checkInFieldsQryRes[$loop]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																			<label class="text-nowrap" for="chkbox_<?php echo $chk_main_id; ?>"><?php echo $checkInFieldsQryRes[$loop]['item_name']; ?></label>	
																		</div>	
																	</div>
																	<div class="col-sm-2 text-center">
																		<?php
																		if($checkInFieldsQryRes[$loop]['item_name'] == 'Copay-visit' || $checkInFieldsQryRes[$loop]['item_name'] == 'Copay-test' || $checkInFieldsQryRes[$loop]['item_name'] == 'Copay'){
																				if($checkInFieldsQryRes[$loop]['item_name'] == 'Copay-visit'){
																					if($checkInFieldsQryRes[$loop]['item_amt']['dilated'] > 0 && $checkInFieldsQryRes[$loop]['item_amt']['nondilated'] > 0){
																						echo $default_currency.'&nbsp;'.$checkInFieldsQryRes[$loop]['item_amt']['dilated'].'/'.$checkInFieldsQryRes[$loop]['item_amt']['nondilated'];
																						?>
																						<div class="checkbox checkbox-inline">
																							 <input <?php echo $visit_payment_readonly; ?> type="checkbox" tabindex="<?php echo $tabindex; ?>" style="cursor:pointer;" name="copay_dilated_<?php echo $chk_main_id; ?>"  id="" title="Copay Dilated" <?php if($checkInFieldsQryRes[$loop]['copay_type'] == '1'){echo 'checked';} ?> onClick="set_copay(this,'<?php echo $chk_main_id; ?>','<?php echo $checkInFieldsQryRes[$loop]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																							<label for="copay_dilated">D</label>	
																						</div>
																						<div class="checkbox checkbox-inline">
																							<input <?php echo $visit_payment_readonly; ?> type="checkbox" tabindex="<?php print $tabindex; ?>" style="cursor:pointer;" name="copay_non_dilated_<?php echo $chk_main_id; ?>" id="copay_non_dilated" title="Copay Non Dilated"  <?php if($checkInFieldsQryRes[$loop]['copay_type'] == '2'){echo 'checked';} ?> onClick="set_copay(this,'<?php echo $chk_main_id; ?>','<?php $checkInFieldsQryRes[$loop]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																							<label for="copay_non_dilated">ND</label>	
																						</div>  
																					 <input type="hidden" id="copay_dilated_tb" name="copay_dilated_tb" value="<?php echo $checkInFieldsQryRes[$loop]['item_amt']['dilated']; ?>">
																					 <input type="hidden" id="copay_non_di	lated_tb" name="copay_non_dilated_tb" value="<?php echo $checkInFieldsQryRes[$loop]['item_amt']['nondilated']; ?>">
																						<?php
																					}else if ($checkInFieldsQryRes[$loop]['item_amt']['dilated'] > 0){
																						$default_currency.'&nbsp;'.$checkInFieldsQryRes[$loop]['item_amt']['dilated'];
																						?> 
																						 <input type="hidden" name="copay_dilated_<?php echo $chk_main_id; ?>" id="copay_dilated" value="1">
																						 <input type="hidden" id="copay_dilated_tb" name="copay_dilated_tb" value="<?php echo $checkInFieldsQryRes[$loop]['item_amt']['dilated']; ?>">
																						 <input type="hidden" id="copay_non_dilated_tb" name="copay_non_dilated_tb" value="0">
																						 <?php
																					}else{
																						echo '-';
																					}
																					$copay_chrg = 0;
																					if($checkInFieldsQryRes[$loop]['copay_type'] == '1'){
																						$copay_chrg = $checkInFieldsQryRes[$loop]['item_amt']['dilated'];
																					}
																					
																					if($checkInFieldsQryRes[$loop]['copay_type'] == '2'){
																						$copay_chrg = $checkInFieldsQryRes[$loop]['item_amt']['nondilated'];
																					}	
																				}
																				
																				if($checkInFieldsQryRes[$loop]['item_name'] == 'Copay'){
																					if($checkInFieldsQryRes[$loop]['item_amt']['dilated'] > 0 && $checkInFieldsQryRes[$loop]['item_amt']['nondilated'] > 0){
																						echo $default_currency.'&nbsp;'.$checkInFieldsQryRes[$loop]['item_amt']['dilated'].'/'.$checkInFieldsQryRes[$loop]['item_amt']['nondilated'];
																						?>
																						<div class="checkbox checkbox-inline">
																							<input <?php echo $visit_payment_readonly; ?> type="checkbox" tabindex="<?php echo $tabindex; ?>" style="cursor:pointer;" name="copay_dilated_<?php echo $chk_main_id; ?>"  id="copay_dilated" title="Copay Dilated"<?php if($checkInFieldsQryRes[$loop]['copay_type'] == 1){echo 'checked';}?> onClick="set_copay(this,'{$chk_main_id}','<?php echo $checkInFieldsQryRes[$loop]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																							<label for="copay_dilated">D</label>	
																						</div>	
																						
																						<div class="checkbox checkbox-inline">
																							<input <?php echo $visit_payment_readonly; ?> type="checkbox" tabindex="<?php echo $tabindex; ?>" style="cursor:pointer;" name="copay_non_dilated_<?php echo $chk_main_id; ?>" id="copay_non_dilated" title="Copay Non Dilated" <?php if($checkInFieldsQryRes[$loop]['copay_type'] == 2){echo 'checked';} ?> onClick="set_copay(this,'<?php echo $chk_main_id; ?>','<?php echo $checkInFieldsQryRes[$loop]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																							<label for="copay_non_dilated">ND</label>	
																						</div>	
																					
																						<input type="hidden" id="copay_dilated_tb" name="copay_dilated_tb" value="<?php echo $checkInFieldsQryRes[$loop]['item_amt']['dilated']; ?>">
																						<input type="hidden" id="copay_non_dilated_tb" name="copay_non_dilated_tb" value="<?php echo $checkInFieldsQryRes[$loop]['item_amt']['nondilated']; ?>">	
																						<?php
																					}else if($checkInFieldsQryRes[$loop]['item_amt']['dilated'] > 0){
																						echo $default_currency.'&nbsp;'.$checkInFieldsQryRes[$loop]['item_amt']['dilated'];
																						?>
																						<input type="hidden" name="copay_dilated_<?php echo $chk_main_id; ?>" id="copay_dilated" value="1">
																						 <input type="hidden" id="copay_dilated_tb" name="copay_dilated_tb" value="<?php echo $checkInFieldsQryRes[$loop]['item_amt']['dilated']; ?>">
																						 <input type="hidden" id="copay_non_dilated_tb" name="copay_non_dilated_tb" value="0">
																						<?php
																					}else{
																						echo '-';
																					}	
																					
																					$copay_chrg = 0;
																					if($checkInFieldsQryRes[$loop]['copay_type'] == 1){
																						$copay_chrg = $checkInFieldsQryRes[$loop]['item_amt']['dilated'];
																					}
																				
																					if($checkInFieldsQryRes[$loop]['copay_type'] == 2){
																						$copay_chrg = $checkInFieldsQryRes[$loop]['item_amt']['nondilated'];
																					}
																				}
																				
																				if($checkInFieldsQryRes[$loop]['item_name'] == 'Copay-test'){
																					if($checkInFieldsQryRes[$loop]['item_amt']['test_dilated'] > 0 && $checkInFieldsQryRes[$loop]['item_amt']['test_nondilated'] > 0){
																						echo $default_currency.'&nbsp;'.$checkInFieldsQryRes[$loop]['item_amt']['test_dilated'].'/'.$checkInFieldsQryRes[$loop]['item_amt']['test_nondilated'];
																						?>
																						<div class="checkbox checkbox-inline">
																							<input <?php echo $visit_payment_readonly; ?> type="checkbox" tabindex="<?php echo $tabindex; ?>" style="cursor:pointer;" name="copay_test_dilated_<?php echo $chk_main_id; ?>"  id="copay_test_dilated" title="Copay Dilated" <?php if($checkInFieldsQryRes[$loop]['copay_type'] == 1){echo 'checked';} ?> onClick="set_test_copay(this,'<?php echo $chk_main_id; ?>','<?php $checkInFieldsQryRes[$loop]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																							<label for="copay_test_dilated">D</label>				
																						</div>
																						
																						<div class="checkbox checkbox-inline">
																							<input <?php echo $visit_payment_readonly; ?> type="checkbox" tabindex="<?php echo $tabindex; ?>" style="cursor:pointer;" name="copay_test_non_dilated_<?php echo $chk_main_id; ?>" id="copay_test_non_dilated" title="Copay Non Dilated" <?php if($checkInFieldsQryRes[$loop]['copay_type'] == 2){echo 'checked'; } ?>  onClick="set_test_copay(this,'<?php echo $chk_main_id; ?>','<?php echo $checkInFieldsQryRes[$loop]['item_name']; ?>'); tot_payment(); tot_charges_fun();">
																								<label for="copay_test_non_dilated">ND</label>				
																						</div>	
																						 <input type="hidden" id="copay_test_dilated_tb" name="copay_test_dilated_tb" value="<?php echo $checkInFieldsQryRes[$loop]['item_amt']['test_dilated']; ?>">
																						 <input type="hidden" id="copay_test_non_dilated_tb" name="copay_test_non_dilated_tb" value="<?php echo  $checkInFieldsQryRes[$loop]['item_amt']['test_nondilated']; ?>">
																						
																						<?php
																					}else if($checkInFieldsQryRes[$loop]['item_amt']['test_nondilated'] > 0){
																						echo $default_currency.'&nbsp;'.$checkInFieldsQryRes[$loop]['item_amt']['test_nondilated'];
																						?>	
																						<input type="hidden" name="copay_test_dilated_<?php echo $chk_main_id; ?>" id="copay_test_dilated" value="1">
																						 <input type="hidden" id="copay_test_dilated_tb" name="copay_test_dilated_tb" value="0">
																						<input type="hidden" id="copay_test_non_dilated_tb" name="copay_test_non_dilated_tb" value="<?php echo $checkInFieldsQryRes[$loop]['item_amt']['test_nondilated']; ?>">
																						<?php 
																					}else{
																						echo '-';		
																					}	
																					$copay_chrg = 0;
																					if($checkInFieldsQryRes[$loop]['copay_type'] == 1){
																						$copay_chrg = $checkInFieldsQryRes[$loop]['item_amt']['test_dilated'];
																					}
																					
																					if($checkInFieldsQryRes[$loop]['copay_type'] == 2){
																						$copay_chrg = $checkInFieldsQryRes[$loop]['item_amt']['test_nondilated'];
																					}
																				}
																				?>
																					<input type="hidden" id="item_charges_<?php echo $chk_main_id; ?>" name="item_charges_<?php echo $chk_main_id; ?>" value="$<?php echo $copay_charge; ?>">
																				<?php
																			}else{
																				echo $checkInFieldsQryRes[$loop]['item_amt'] ;
																				?>
																					<input type="hidden" id="item_charges_<?php echo $chk_main_id; ?>" name="item_charges_<?php echo $chk_main_id; ?>" value="<?php echo $checkInFieldsQryRes[$loop]['item_amt']; ?>">
																				<?php
																			}
																		?>	
																	</div>
																	<div class="col-sm-6">
																		<div class="row">
																			<div class="col-sm-2 text-right pt10">
																			
																				<?php echo htmlspecialchars_decode(show_currency()); ?>	 
																			</div>	
																			<div class="col-sm-5">
																				<input <?php echo $visit_payment_readonly; ?> type="text" size="4" class="form-control" tabindex="<?php echo $tabindex; ?>" id="item_pay_<?php echo $chk_main_id; ?>" value="<?php echo $checkInFieldsQryRes[$loop]['item_payment']; ?>" <?php echo $checkInFieldsQryRes[$loop]['nr_enabled']; ?> name="item_pay_<?php echo $chk_main_id; ?>" onBlur="tot_payment();" onChange="tot_payment();" onKeyUp="sel_chkbox(this,'<?php echo $chk_main_id; ?>','<?php echo $checkInFieldsQryRes[$loop]['item_name']; ?>');set_pay_method('<?php echo $chk_main_id; ?>');" onClick="sel_chkbox(this,'<?php echo $chk_main_id; ?>');tot_charges_fun();">	
																			</div>	
																			<div class="col-sm-5">
																				<select name="pay_method_<?php echo $chk_main_id; ?>" id="pay_method_<?php echo $chk_main_id; ?>" class="selectpicker" onChange="return showRow(this);" <?php echo $visit_payment_readonly; ?> data-width="100%" data-title="<?php echo imw_msg('drop_sel');?>">
																				<option value=""></option>
																				<?php
																				
																					foreach($pay_method as $key => $val){
																						$select = '';
																						if($selected_pay_method[$chk_main_id] == $val){
																							$select = 'selected';
																						}
																						echo '<option value="'.$key.'" '.$select.'>'.$val.'</option>';
																						
																					}
																				?>
																			</select>	
																			</div>		
																		</div>	
																	</div>
														<?php 	$tabindex++;
																if(fmod($i,3) > 0){
																	print '</div></div><div class="col-sm-4"><div class="row pt10">';
																}else if(fmod($i,3) == 0){
																	print '</div></div><div class="col-sm-4"><div class="row pt10">';
																	$i = 0;
																}
															} ?>		
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-12">
											<label>Comments</label>
											<textarea <?php echo $visit_payment_readonly; ?> rows="3" class="form-control" name="ci_comments"><?php echo core_extract_user_input($paymentQryRes[0]["ci_comments"]); ?></textarea>
										</div>	
										<div class="col-sm-12">
											<div class="row pt10">
												<div class="col-sm-4">
													<label>Today Charges : <?php echo show_currency().number_format($total_allowable_charges,2); ?></label>	
												</div>	
												<div class="col-sm-4">
													<?php
														foreach($group_name as $key => $val){
															$Previous_Balance = array_sum($previous_data[$key]['Patient_Due']);
															if($Previous_Balance > 0){
																?>
																	<label>Pt Bal(<?php echo $val; ?>) :  
																	<?php echo $default_currency.'&nbsp;'.$Previous_Balance ?></label>
																<?php
															}
														}
														
														foreach($group_name as $key => $val){
															$Previous_Balance = array_sum($previous_data[$key]['Insurance_Due']);
															if($Previous_Balance > 0){
																?>
																	<label>Ins Bal(<?php echo $val; ?>) : 
																	<?php echo $default_currency.'&nbsp;'.$Previous_Balance ?></label><!-- {$default_currency}{$Previous_Balance|number_format:2:".":","} -->
																<?php
															}
														}	
													?>	
												</div>	
												<div class="col-sm-4 text-center">
													<label>Total Payments : <span id="tot_payment"><?php echo $default_currency.str_replace(',','',number_format(array_sum($total_payment_arr),2)); ?></span></label>	
													 <input type="hidden" name="total_charges_txt" id="total_charges_txt" value="<?php echo $totalCharges; ?>">
                                                <input type="hidden" name="tot_payment_txt" id="tot_payment_txt" value="<?php echo str_replace(',','',number_format(array_sum($total_payment_arr),2)); ?>">
												</div>		
											</div>	
										</div>	
										<div class="col-sm-12 pt10">
											<div class="row">
												<div class="col-sm-2">
													<label>Payment method Details:&nbsp;</label>	
												</div>
												<div  id="checkRow" class="col-sm-2 " style="display:<?php echo $checkRow; ?>;">
													<label> &nbsp;Check #:&nbsp;</label>	
													<input <?php echo $visit_payment_readonly; ?> name="checkNo" id="checkNo" type="text" class="form-control" value="<?php echo $payment_chk_number; ?>" />
												</div>
												<div class="col-sm-6" id="creditCardRow" style="display:<?php echo $creditCardRow; ?>;">
													<div class="row ">
														<div class="col-sm-5 ">
															<label>CC Type.:&nbsp;</label>
															<select <?php echo $visit_payment_readonly; ?> name="creditCardCo" id="creditCardCo" class="selectpicker" data-title="<?php echo imw_msg('drop_sel');?>">
																<?php
																	foreach($cr_name_arr as $key => $val){
																		$select = '';
																		if($key == $cr_selected){
																			$select = 'selected';
																		}
																		echo '<option value="'.$key.'" '.$select.'>'.$val.'</option>';
																	}
																?>
															</select>	
														</div>
														<div class="col-sm-4 ">
															<label>CC #:&nbsp;</label>
															<input <?php echo $visit_payment_readonly; ?> name="cCNo" id="cCNo" type="text" class="form-control" value="<?php echo $creditCardNumber; ?>" />	
														</div>
														<div class="col-sm-3 ">
															<label>Exp. Date:&nbsp;</label>
															<input <?php echo $visit_payment_readonly; ?> id="date2" type="text"  name="CCexpireDate" value="<?php echo $creditCardDate; ?>" onBlur="return expDate();" size="10" maxlength="10" class="form-control" />	
														</div>
													</div>	
												</div>
                                                <?php if($pos_device) { ?>
                                                    <div class="col-sm-4">
                                                        <?php
                                                            $laneID='10000001';
                                                            $cc_class='checkin_payment';
                                                            $target_ids='pay_method_';
                                                            $scheduID=$sch_id;
                                                            $fcount=count($checkInFieldsQryRes);
                                                            include (dirname(__FILE__).'/../../accounting/pos/include_cc_payment.php');
                                                        ?>
                                                    </div>
                                                <?php } ?>
											</div>	
                                            
										</div>
                                        </div>
									</div>
								</div>	
							<div id="footer_div_height"></div>
							
							<!-- Mpay Modal Box -->
							<?php 
                if($isMpay){ 
									$mpay_modal_data = '';
									$mpay_modal_data .= '
									<div class="row">
										<div class="col-sm-12 ">
											<div class="row">
												<div class="col-sm-12">
													<label>Copay Amount Collected :</label>
													<div class="input-group">
														<div class="input-group-addon">
															<span class="glyphicon glyphicon-usd"></span>
														</div>
														<input type="text" id="mpay_copay" class="form-control" name="mpay_copay" value="" />	
													</div>	
												</div>
												<div class="col-sm-12">
													<label>Non-Copay Amount Collected :</label>
													<div class="input-group">
														<div class="input-group-addon">
															<span class="glyphicon glyphicon-usd"></span>
														</div>
														<input type="text" id="mpay_noncopay" class="form-control" name="mpay_noncopay" value="" />									
													</div>	
												</div>
												<div class="col-sm-12">
													<label>Due Amount :</label>
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
								} 
							?>
  				</div>
      	</div>    
      	 
     	</div>
   	</div>
    
    <div class="mainWhtbox">
    	<div class="row">
      	<div id="module_buttons" class="footer ad_modal_footer">
        	<div class="col-sm-12 text-center">
          
          	<span>
            	<input type="button" id="submit_btn" name="submit_btn" value="Done" class="btn btn-success" onClick="dgi('after_save_url').value='closeCI';return get_action('submit_form',true);  ">
                <input type="submit" id="btn_submit" name="btn_submit" value="Save" class="hide">	
          	</span>
            
            <span id="print_save_rec_id" style="display:none;">
            	<input type="button" id="btn_print" name="btn_print" value="Save & Print Receipt" class="btn btn-success" onClick="print_save_fun(true);">	
           	</span>
            
            <span id="print_rec_id" style="display:none;">
            	<input type="button" tabindex="150" id="print_receipt" name="print_receipt" value="Print Receipt" class="btn btn-success" onClick="print_receipt_check();">	
          	</span>	
            
            <?php if(isset($isMpay) && trim($isMpay) != ''){ ?>
            <span id="mpay_btn_cell" style="display:none;">
            	<input type="button" id="btn_mpay" name="btn_mpay" value="Mpay" class="btn btn-success" onClick="return get_action('mpay_form')">	
          	</span>
            <?php } ?>
            <?php if($pos_device) { ?>
                <button type="button" name="pos_log" value="pos_log" class="btn btn-primary" onClick="window.show_transaction_popup();">POS Log</button>
            <?php } ?>

			<?php if(isUGAEnable() && (isset($_SESSION['patient']) && $_SESSION['patient']!='')) { ?>
				<!-- <button type="button" name="uga_register" value="uga_register" class="btn btn-info" onClick="apply_uga_finance('checkin');">UGA Finance</button> -->
			<?php } ?>
            <span>
            	<input type="button" id="but_closeTemp" name="but_closeTemp" value="Close" class="btn btn-danger" onClick="javaScript: window.close();">
          	</span>	
        
        	</div>
     		</div>
      </div>
    </div>  
    
            	</form>        	
	<!-- Re-Arrange Insurance Provider Block -->
	<div id="divRearrangeIns" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Re-Arrange Insurance Provider</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto; ">
      	<form name="frmRearrangeIns" method="post" action="">
					<div class="row" id="rowReArrangeIns">
							<?php
							$counterRadio = 0;
								foreach($arrInsSwapData as $key => $val){
									if($key == 0){
										?>
											<input type="hidden" id="hidInsCaseId" name="hidInsCaseId" value="<?php echo $val['ins_case_id']; ?>" >
											<input type="hidden" id="hidSaveInsSwap" name="hidSaveInsSwap">
										<?php
									}
									if($val['insData']['insType'] == 'Primary'){
										?>
											<input type="hidden" name="compId[]" value="<?php echo $val['insData']['providerId']; ?>" >
										<?php
									}
									
									if($val['insData']['insType'] == 'Secondary'){
										?>
											<input type="hidden" name="compId[]" value="<?php echo $val['insData']['providerId']; ?>" >
										<?php
									}
									
									?>
									<div class="row">
										<div class="col-sm-4"> 
											<?php echo $val['insData']['insType']; ?> Ins.	
										</div>	
										<div class="col-sm-4">
											<?php echo $val['insData']['providerName']; ?>	
										</div>	
										<div class="col-sm-4">
											<div class="radio radio-inline">
												<input type="radio" name="name_<?php echo $val['insData']['insType']; ?>" <?php if($val['insData']['insType'] == 'Primary'){ echo 'checked' ; } ?> value="primary__<?php echo $val['insData']['insDataId']; ?>" onClick="swap_ins('<?php echo $val['insData']['insType']; ?>','Primary');" style="cursor:pointer;" id="primaryInsRadio_<?php echo $counterRadio; ?>"><label for="primaryInsRadio_<?php echo $counterRadio; ?>">Primary &nbsp;</label>
											</div>	
											<div class="radio radio-inline">
												<input type="radio" name="name_<?php echo $val['insData']['insType']; ?>" <?php if($val['insData']['insType'] == 'Secondary'){echo 'checked'; } ?> value="secondary__<?php echo $val['insData']['insDataId']; ?>" onClick="swap_ins('<?php echo $val['insData']['insType']; ?>','Secondary');" style="cursor:pointer;" id="secondaryInsRadio_<?php echo $counterRadio; ?>"><label for="secondaryInsRadio_<?php echo $counterRadio; ?>">Secondary &nbsp;</label>
											</div>		
										</div>	
									</div>	
								<?php
									$counterRadio++;
								}
							?>
						
					</div>
				</form>	
      	
      	
      </div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
				<button type="submit" id="btInsSwap" name="btInsSwap" class="btn btn-success" onClick="saveSwap();">Save</button>
       	<button type="button" id="btInsSwapClose" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
      
    </div>
  </div>
</div>

	<!-- Search Physician Modal -->
		<div id="search_physician_result" class="modal" role="dialog" >
			<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h4 class="modal-title col-xs-4 col-sm-3" id="modal_title">Select Physician</h4>
				<div class="col-xs-7 col-sm-8 form-inline">
				<select class="selectpicker col-xs-4" id="search_by" title="Search By">
					<option value="LastName" selected="selected">Last Name</option>
								<option value="FirstName">First Name</option>
								<option value="Address1">Street Address</option>
                                <option value="PractiseName">Practice Name</option>
                                <option value="physician_phone">Phone Number</option>
                                <option value="physician_fax">Fax Number</option>
				</select>&nbsp;For&nbsp;
				<span class="col-xs-7 input-group">
					<input type="text" id="phy_ajax" class="form-control" title="Search Physician" placeholder="Search Physician" data-action="search_physician" data-text-box="" data-id-box="" />
				  <label class="input-group-addon btn search_physician" id="phy_ajax_btn" title="Click to Search" data-source="phy_ajax" onClick="search_physician_popup('ref_phy_name','true','search_by','phy_ajax')">
					<span class="glyphicon glyphicon-search"></span>
					</label>
				</span>
				</div>
				</div>
			  
			  <div class="modal-body">
				<div class="loader"></div>
			  </div>
			  
			  <div class="modal-footer ad_modal_footer" id="module_buttons">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			  </div>
			  
			</div>
		  </div>
		</div>
	<!-- -->
	<div id="primaryCarePhysician" class="modal" role="dialog" ></div>
	<div id="referringPhysician" class="modal" role="dialog" ></div>
  
  <!-- Start Race Modal -->
<div id="race_modal" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h4 class="modal-title" id="modal_title">&nbsp;Race</h4>
     	</div>
		<div class="modal-body" style="max-height:450px; overflow:hidden; overflow:auto;">
    	<?php echo $demo->race_modal(0); ?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Done&nbsp;&&nbsp;Close</button>
		</div>
      
    </div>
  </div>
</div>
<!-- End Race Modal -->


<!-- Start Ethnicity Modal -->
<div id="ethnicity_modal" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h4 class="modal-title" id="modal_title">&nbsp;Ethnicity</h4>
     	</div>
		<div class="modal-body" style="max-height:450px; overflow:hidden; overflow:auto;">
    	<?php echo $demo->ethnicity_modal(0); ?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Done&nbsp;&&nbsp;Close</button>
		</div>
      
    </div>
  </div>
</div>
<!-- End Ethnicity Modal -->


<!-- Start Language Modal -->
<div id="language_modal" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h4 class="modal-title" id="modal_title">&nbsp;Language</h4>
     	</div>
		<div class="modal-body" style="max-height:450px; overflow:hidden; overflow:auto;">
    	<?php echo $demo->language_modal(0); ?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Done&nbsp;&&nbsp;Close</button>
		</div>
      
    </div>
  </div>
</div>
<!-- End Language Modal -->

<!-- 
	Start
  Responsible Party Driving License Image Modal 
-->
<div id="resp_party_license" class="modal" role="dialog" >
	<div class="modal-dialog modal-md">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Driving License (Resp. Party)</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
      	<img src="<?php echo $resp_license_img; ?>" />
      </div>
      	
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- Responsible Party Driving License Image Modal -->

<div id="scan_card_show" class="movable_modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" onClick="$('#scan_card_show').fadeOut('fast');">×</button>
        <h4 class="modal-title" id="modal_title"></h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto; "></div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger" onClick="$('#scan_card_show').fadeOut('fast');">Close</button>
      </div>
      
    </div>
  </div>
</div>

<div id="imageLicense" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Scan License Image</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
      	<?php
        	if($lic_img_src)
					{ 
						echo $lic_img_src;
					}
				?>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<!-- Start Search Patient for family grid info AND Responsible Party-->
<style>#search_patient_result_resp a:hover {text-decoration: none;}</style>
<div id="search_patient_result_resp" class="modal" role="dialog" >
    <div class="modal-dialog modal-md" style="width:700px!important;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title col-xs-4 col-sm-5 col-md-3" id="modal_title">Select Patient</h4>
                <span class="col-xs-6 col-md-5 input-group">
                    <input type="text" id="sp_ajax" class="form-control" title="Search Patient" placeholder="Last Name, First Name" onBlur="search_patient_resp(this)" onFocus="search_patient_resp(this)" data-fld="Active" data-search-type="pname" data-grid="" />
                    <label class="input-group-addon btn" id="sp_ajax_btn" for="sp_ajax">
                        <span class="glyphicon glyphicon-search"></span>
                    </label>
                </span>
            </div>

            <div class="modal-body" style="min-height:200px;max-height:400px;overflow-x:none;overflow-y:scroll;"></div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- Search Patient for family grid info AND Responsible Party -->


<div id="div_loading_image" class="text-center" style="z-index:9999;display:none;">
    <div class="loading_container">
        <div class="process_loader"></div>
        <div id="div_loading_text" class="text-info"></div>
    </div>
</div>
  <script type="text/javascript">
		$(function(){
			$("#scan_card_show").draggable({ handle: ".modal-header" });	
		});
		
		//START CODE FOR SCAN POPUP AFTER ADDING A NEW PATIENT
		var hidd_scan_card_type_val = '<?php echo trim($_REQUEST["hidd_scan_card_type"]);?>';
		if(hidd_scan_card_type_val){
			scan_card(hidd_scan_card_type_val,'<?php echo $insDataArr[strtolower($_REQUEST["hidd_scan_card_type"])]['MAIN_ID']; ?>');
		}
		//END CODE FOR SCAN POPUP AFTER ADDING A NEW PATIENT
		
		
		var str_ins_type_ahead = <?php echo json_encode($insCompanyStr) ?>;
		function getOperatorNameAndDate(){
			$('#chkNotesScheduler, #chkNotesChartNotes, #chkNotesAccounting').prop('checked',true);
			var previous_txt = $('#patient_notes').text();
			var new_txt = "<?php echo $notes_date.' '.$strOpName; ?>: \n";
			
			var final_str = new_txt+previous_txt;
			$('#patient_notes').text(final_str);
			setCaretPosition("patient_notes", 13);
		}
		arrVal = document.getElementById("elem_heardAbtUs").value.split("-");
		if(arrVal[1] == "Dr" || arrVal[1] == "Dr."){
			if (!document.getElementById("heardAbtDesc").addEventListener) {
				document.getElementById("heardAbtDesc").attachEvent("onkeydown",function(){loadPhysicians(document.getElementById("heardAbtDesc"))});
				document.getElementById("heardAbtDesc").attachEvent("onfocus",function(){loadPhysicians(document.getElementById("heardAbtDesc"))});
			}
			else {
				document.getElementById("heardAbtDesc").addEventListener("keydown",function(){loadPhysicians(document.getElementById("heardAbtDesc"))});
				document.getElementById("heardAbtDesc").addEventListener("focus",function(){loadPhysicians(document.getElementById("heardAbtDesc"))});
			}
		}
		//new actb(document.getElementById("insSecProv"), str_ins_type_ahead);
		$('#insPriProv').typeahead({source:str_ins_type_ahead,items:8,scrollBar:true,ajax:'',onSelect:function(item){ setInsuranceAutoFill(item.value,'insPriProv');  } });
		$('#insSecProv').typeahead({source:str_ins_type_ahead,items:8,scrollBar:true,ajax:'',onSelect:function(item){ setInsuranceAutoFill(item.value,'insSecProv');  } });
		get_accept_assignment();
		function set_window_height()
		{
			//var window_height	= parseInt($(window).height());
			var footer_height =parseInt($(".mainWhtbox:eq(1)").outerHeight(true));
			if(get_browser()=='ie') footer_height = 80;//(screen.availHeight > 900) ?  180 : 220 ;
			//var container_height = window_height - (footer_height + 100);
			var window_height=screen.availHeight*0.8;
			/*'<?php echo $intClientWindowH;?>';console.log(window_height);*/
			var container_height = window_height - (footer_height +100);
			//console.log(window_height + '-' + footer_height + ' === ' + container_height+ "session ht = <?php echo $intClientWindowH;?>");
			$("#mainWhtbox").css({'max-height':container_height+'px','min-height':container_height+'px','overflow':'hidden','overflow-y':'scroll'});
		}
		
		window.onresize = set_window_height();
		window.onload = set_window_height();
		
	  $('#demographics_edit_form').on('keyup keypress', function(e) {
		  var keyCode = e.keyCode || e.which;
		  if (keyCode === 13) { 
			e.preventDefault();
			return false;
		  }
		});	
		function addExtra(_this)
		{
			var isChecked = $(_this).is(':checked') ? true : false;
			var fldName = $(_this).data('object-id');
			var v = $(_this).val();
			var fldObj = $("#"+fldName);
			
			var tmpObj = fldObj.find('option[value="'+v+'"]');
			
			if( tmpObj.length > 0 ) {
				
				var isCommon = tmpObj.data('common');
				
				if( isCommon == '0' )	tmpObj.remove();
				else tmpObj.prop('selected',isChecked);
			
			} else {
				if( fldName == 'race')
					$("#"+fldName + " option:eq(-1)").before($('<option></option>').val(v).text(v).data('common','0').prop('selected',isChecked));
				else
					$("#"+fldName + " option:eq(-2)").before($('<option></option>').val(v).text(v).data('common','0').prop('selected',isChecked));
			}
			
			fldObj.selectpicker('refresh');
		}
		
		function addLanguage(_this)
		{
			var isChecked = $(_this).is(':checked') ? true : false;
			
			$("#language_modal [type=checkbox]").prop('checked',false);
			$(_this).prop('checked',isChecked);
			
			var fldName = $(_this).data('object-id');
			var code = $(_this).data('code-name');
			var v = $(_this).val();
			var fldObj = $("#"+fldName);
			
			var c_value = fldObj.val();
			if( c_value == 'Other')	$("#imgBackLanguage").trigger('click');
			
			var tmpObj = fldObj.find('option[value="'+v+'"]');
			
			if( tmpObj.length > 0 ) {
				
				var isCommon = tmpObj.data('common');
				
				if( isCommon == '0' )	tmpObj.remove();
				else tmpObj.prop('selected',isChecked);
			
			} else {
					$("#"+fldName + " option:eq(-2)").before('<option value="'+v+'" data-common="0" data-code="'+code+'" '+(isChecked ? 'selected' : '')+' >'+v+'</option>');
			}
			
			fldObj.trigger('change');
		}
		
		function set_phone_format1(objPhone,default_format,phone_length,msg_txt,class_name){
			class_name = typeof class_name == 'undefined' ? 'form-control' : class_name;
			if( objPhone.value !== '') {
				set_phone_format(objPhone,default_format,phone_length,msg_txt,class_name);
				$(objPhone).trigger('keyup');
			}
			else $(objPhone).addClass(class_name);
		}
		
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
        
        //Auto fill the Responsible party/guarantor address if patient's age is below 18 years
        //Starts Here
        var date_global_format = window.opener.top.jquery_date_format;
        //resp_party date picker
        $('.datepicker').datetimepicker({
            timepicker:false,
            format:date_global_format,
            formatDate:date_global_format,
            scrollInput:false,
            maxDate:new Date()
        });
                
        $('#new_resp_container').on('click', function(){
            $("#fname1").bind( "focus blur keyup",function(){ autofillRespParty(); });
            $("#lname1").bind( "focus blur keyup",function(){ autofillRespParty(); }); 
        });

        function autofillRespParty() {
            var d=[];
                d['resp_ptStreet']=$("#street").val();
                d['resp_ptStreet2']=$("#street2").val();
                d['resp_ptPostalCode']=$("#code").val();
                d['resp_ptzip_ext']=$("#zip_ext").val();
                d['resp_ptCity']=$("#city").val();
                d['resp_ptState']=$("#state").val();

            if(d && $.isArray(d)) {
                if( ($("#fname1").val()!='' && $("#lname1").val()!='') && $("#street1").val()=='' && $("#rcode").val()=='' && $("#rcity").val()=='' && $("#rstate").val()=='') {
                    $("#street1").val(d.resp_ptStreet);
                    $("#street_emp").val(d.resp_ptStreet2);
                    $("#rcode").val(d.resp_ptPostalCode);
                    $("#rzip_ext").val(d.resp_ptzip_ext);
                    $("#rcity").val(d.resp_ptCity);
                    $("#rstate").val(d.resp_ptState);	
                }
            }
        }

        function showHideRespPartyDiv(obj) {
            var calcdAge=document.getElementById("patient_age").innerHTML;
            
            if(calcdAge < 18) {
                $("#new_resp_container").show();
            }else {
                $("#new_resp_container").hide();

                $('#new_resp_container').find('input[type="text"],input[type="hidden"]').val('');
                $('#new_resp_container').find('input[type="checkbox"]').prop('checked',false);
                $('#new_resp_container').find('select').val('');
            }
        }
        
       
        function set_resp_after_save_actions(msg_index, val){
            if(msg_index == "9"){
                if(val == "1") { val = "yes"; } else { val = "no"; }		
                document.getElementById("hid_create_acc_resp_party").value = val;
            }
        }

        function return_resp_false() {
            return false;
        }

        function resp_party_adult_checks(adult_check,resp_cred) {
            if(!adult_check)adult_check=false;
			if(!resp_cred)resp_cred=false;
            var calcdAge=document.getElementById("patient_age").innerHTML;
            var resp_container = $('#new_resp_container').is(':visible');
            if(resp_container!=false && calcdAge < 18 && adult_check==false && (dgi("fname1").value=="" || dgi("lname1").value=="") ) {
                var msg_to_show = 'You have not filled the following fields:<br><br>';
                msg_to_show += vocabulary['patient_not_adult'];
                msg_to_show += "<br><br>Do you wish to continue?";

                top.fancyConfirm(msg_to_show,'','get_action("submit_form",true,"checked","'+resp_cred+'");','return_resp_false();');
                return false;
            }

            if(dgi("fname1").value!="" && dgi("lname1").value!="" && (adult_check=="checked" || adult_check==false) ){
                var msg_to_show = 'Please confirm the following actions:<br><br>';
                msg_to_show += "<div class=\"col-sm-12\">"+vocabulary['create_acc_grantor']+ "</div><div class=\"col-sm-2\"><div class=\"radio\"><input type=\"radio\" name=\"r9\" id=\"r9_yes\" checked onclick=\"set_resp_after_save_actions('9', '1');\" /><label for=\"r9_yes\">Yes</label></div></div><div class=\"col-sm-2\"><div class=\"radio\"><input type=\"radio\" name=\"r9\" id=\"r9_no\" onclick=\"set_resp_after_save_actions('9', 0);\" /><label for=\"r9_no\">No</label></div></div><div class=\"clearfix\"></div>";
                top.fAlert(msg_to_show,'','get_action("submit_form",true,"resp_checked","'+resp_cred+'");');
                return false;
            }

            return true;
        }
        
        function search_patient_resp(obj,iKey) {
            var v = $(obj).val();
            iKey= iKey||'';
            if($(obj).attr('id') !== 'sp_ajax' && !v){
                top.fAlert('Please enter last name to precede search');
                return false;
            }if(v=='' || v=='undefined')return false;
            top.show_loading_image('show');
            var u = top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/search_patient.php?grid=0&action=search_patient&val='+v;
            $.ajax({
                url:u,
                type:'post',
                dataType:'json',
                beforeSend:function(){
                    if (iKey) $('#sp_ajax', '#search_patient_result_resp').data('i-key', iKey)
                    if (!iKey) iKey = $('#sp_ajax', '#search_patient_result_resp').data('i-key')
                    $("#search_patient_result_resp").modal('show');
                },
                success:function(r){
                    top.show_loading_image('hide');
                    grid_id = r.grid; search_data_resp = r.data;
                    $("#search_patient_result_resp #sp_ajax").data('grid',r.grid);
                    var ht = ['Name','ID','Address','Phone'];
                    if(r.grid == 0) ht = ['Name','SS','DOB','ID'];
                    var html = '';

                        html	+=	'<table class="table table-bordered table-hover table-striped scroll release-table">'
                        html	+=	'<thead class="header">';
                        html	+=	'<tr class="grythead">';	
                        html	+=	'<th class="col-xs-4">'+ht[0]+'</th>';
                        html	+=	'<th class="col-xs-3">'+ht[1]+'</th>';
                        html	+=	'<th class="col-xs-2">'+ht[2]+'</th>';
                        html	+=	'<th class="col-xs-3">'+ht[3]+'</th>';
                        html	+=	'</tr>';
                        html	+=	'</thead>';
                        html	+=	'<tbody>';

                    if(r.pdata.length > 0 )
                    { 
                        var g = (r.grid > 0) ? 'family' : 'resp';
                        var k = (typeof(iKey)!='undefined' && iKey!='') ? 'data-i-key="'+iKey+'"' : '';
                        for(i in r.pdata) {

                            var f1 = (r.grid > 0) ? r.pdata[i].name 	: r.pdata[i].name;
                            var f2 = (r.grid > 0) ? r.pdata[i].id 		: ( (r.pdata[i].ss==null)?'':r.pdata[i].ss );
                            var f3 = (r.grid > 0) ? r.pdata[i].address  : r.pdata[i].dob;
                            var f4 = (r.grid > 0) ? r.pdata[i].phone 	: r.pdata[i].id;

                            html	+=	'<tr >';
                            html	+=	'<td data-label="'+ht[0]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f1+'</a></td>';
                            html	+=	'<td data-label="'+ht[1]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f2+'</a></td>';
                            html	+=	'<td data-label="'+ht[2]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f3+'</a></td>';
                            html	+=	'<td data-label="'+ht[3]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f4+'</a></td>';
                            html	+=	'</tr>';
                        }
                    }
                    else
                    {
                        html += '<tr><td colspan="4" class="bg-warning">No Patient Found.</td></tr>'
                    }
                    html	+=	'</tbody>';
                    html	+=	'</table>';

                    $("#search_patient_result_resp .modal-body").html(html);
                }
            });
        }
        
        $("body").on('click','[data-grid="resp"]',function(e){
            var i = $(this).attr('data-row'); var t = $(this).attr('data-grid'); var c = $(this).attr('data-i-key'); fill_patient_resp_party(i,t,c);
        });
        
        function fill_patient_resp_party(id,t,call_from) {
            if(id == '0' || typeof id == 'undefined') return;
            call_from = call_from || '';
            var d = search_data_resp[id];
            
            if(typeof(call_from) !='undefined' && call_from!='')
			{
				popUpRelationValue(call_from,d);
				var grid_obj = $("#insPolicy"+call_from+"_table");
			} else {
                $("#title1").val(d.title);
                $("#fname1").val(d.fname);
                $("#mname1").val(d.mname);
                $("#lname1").val(d.lname);
                $("#suffix1").val(d.suffix);
                $("#status1").val(d.status);
                $("#dob1").val(d.DOB);
                $("#sex1").val(d.sex);
                $("#street1").val(d.street);
                $("#street_emp").val(d.street2);
                $("#rcode").val(d.postal_code);
                $("#rcity").val(d.city);
                $("#rstate").val(d.state);	
                $("#ss1").val(d.ss);	
                $("#phone_home1").val(d.phone_home);	
                $("#phone_biz1").val(d.phone_biz);	
                $("#phone_cell1").val(d.phone_cell);	
                $("#hid_resp_party_sel_our_sys").val('yes');	

                $("#title1.selectpicker,#status1.selectpicker").selectpicker('refresh');

                var grid_obj = $("#search_patient_result_resp");
            }
            $("#search_patient_result_resp .modal-body").html('');
            $("#search_patient_result_resp").modal('hide');
            grid_obj.find('input[type="text"],select.minimal,select.selectpicker').triggerHandler('change');
        }
        
        //Ends Here
        
        // Release Information starts here
        function swap_combo_other(showIDArr,hideIDArr,comboObj)
        {
            if((typeof(comboObj) != 'object') || (comboObj.value == 'Other') || (comboObj.value == 'Others')){
                if(hideIDArr){ 
                    $(hideIDArr).each(function(){
                        if($('#'+this).hasClass('selectpicker')) $('#'+this).selectpicker('hide'); 
                        else $('#'+this).removeClass('inline').addClass('hidden'); 	
                    });
                } 
                $(showIDArr).each(function(){ 
                    if($('#'+this).hasClass('selectpicker')) 
                        $('#'+this).val('').selectpicker('show').selectpicker('refresh').trigger('change'); 
                    else $('#'+this).removeClass('hidden').addClass('inline'); 
                });
            }
        }
        
        
        $('.hasotherbox').on('click','.back_other',function(){
            _this = $(this)[0]; $_this= $(this);
            var n = $_this.attr('data-tab-name'); 
            if(n === 'relInfoReletion') { var t = $_this.attr('data-tab-num'); swap_combo_other(Array(n+t),Array('otherRelInfoBox'+t)); }
			else if(n === 'emerRelation') { swap_combo_other(Array('emerRelation'), Array('relation_other_box')); }
        });

        $('.hasotherbox').on('change','select',function(){
            _this = $(this)[0]; $_this= $(this);
            var n = $_this.attr('name');
            if(n)
            {
                if(str_exists(n,'relInfoReletion')) { var t = $_this.attr('data-tab-num'); swap_combo_other(Array('otherRelInfoBox'+t),Array(n),_this); }
				else if(n === 'emerRelation') { swap_combo_other(Array('relation_other_box'),Array('emerRelation'),_this); }
            }
        });
        
        function str_exists(str,search_string)
        {
            return (str.indexOf(search_string) === -1 ) ? false : true;
        }
        // Release Information ends here
        
        
        /* Policy holder section for primary/secondary starts Here*/
        // Function to Perform action when change policy holder sub relation dropdown
        // occur for Sub.Relation DropDown
        function popUpRelation()
        {
            var arguments = popUpRelation.arguments;
            
            callFrom=arguments[1];
            var s_name = callFrom.substr(0,3);
            arguments[4] = arguments[4] || 'pri';
            var i_key = arguments[4];
            var lname1 = "";
            var fname1 = "";		

            if(arguments[0] != "self")
            {
                if(typeof(arguments[2])!='boolean'){ arguments[2] = true; }
                if(arguments[2])
                {
                    top.fancyConfirm("Clear Contact Details?","","popUpRelation('"+arguments[0]+"','"+arguments[1]+"',false,true,'"+arguments[4]+"')","popUpRelation('"+arguments[0]+"','"+arguments[1]+"',false,false,'"+arguments[4]+"')");
                    return;
                }

                bk_action = arguments[3];
                if(bk_action == false)
                {
                    return false;	
                }
                var ln = i_key + '_lastName' ;
                var hn = 'hid_' + i_key + '_subscriber_exits_our_sys';
                var fn =  i_key+'_subscriber_fname';

                lname1 = document.getElementById(ln).value;
                if(arguments[0] != "Spouse"){	
                    fname1 = document.getElementById(fn).value;
                }
                document.getElementById(hn).value = "no";	

                lname1 = (lname1 === '') ? document.getElementById(i_key+"_hidlastName").value : lname1;
                if(lname1)
                {
                    //var tmp = $("#"+i_key+"_lastName");
                    //search_patient_resp(tmp,i_key);
                    popUpRelationValue(i_key);
                }
                else{
                    top.fAlert("Please enter last name to precede search");
                }
            }
            else
            {
                var i = 'sub_'+i_key+'_pat_id';
                document.getElementById(i).value = document.getElementById("edit_patient_id").value;
                if(arguments[0] == "self" || arguments[0] == "Self"){
                      makePtValArr(i_key);
                }
            }	
        }
        
        // Function to fill Relation info in their respective fields
        //pid,title,iterfname,itermname,iterlname,itersuffix,status,dob_format,sex,street,zip,city,state,ss,phone_home,phone_biz,phone_cell,street2,zipext
        function popUpRelationValue(call_from,d)
        {
            call_from = call_from || 'pri';
            var has_data = (typeof d !== 'undefined') ? true : false;

            $("#sub_"+call_from+"_pat_id")[0].value = has_data ? d.id : '' ;

            $("#"+call_from+"_subscriber_fname")[0].value=has_data ? d.fname : '' ;
            $("#"+call_from+"_subscriber_mname")[0].value =has_data ? d.mname : '' ;
            $("#"+call_from+"_lastName")[0].value=has_data ? d.lname : '' ;
            $("#"+call_from+"_suffix_rel")[0].value=has_data ? d.suffix : '' ;	
            $("#"+call_from+"_subscriber_DOB")[0].value = has_data ? d.DOB : '' ;
            $("#"+call_from+"_subscriber_ss")[0].value = has_data ? d.ss : '' ;
            $("#hid_"+call_from+"_subscriber_exits_our_sys")[0].value = has_data ? 'yes' : '' ; 
            $("#"+call_from+"_subscriber_sex").val(has_data ? d.sex : '');
        }
        
        //Create array to auto populate value in plolicy holder fields
        function makePtValArr(i_key) {
            var d=[];
            d.id=''|| '<?php echo ($patientQryRes[0]['id']);?>';
            d.fname=$('#fname').val() || '<?php echo ($patientQryRes[0]['fname']);?>';
            d.mname=$('#mname').val() || '<?php echo ($patientQryRes[0]['mname']);?>';
            d.lname=$('#lname').val() || '<?php echo ($patientQryRes[0]['lname']);?>';
            d.suffix=$('#suffix').val() || '<?php echo ($patientQryRes[0]['suffix']);?>';
            d.DOB=$('#dob').val() || '<?php echo ($patientQryRes[0]['patient_dob']);?>';
            d.ss=$('#ssnNumber').val() || '<?php echo ($patientQryRes[0]['ss']);?>';
            d.sex=$('#selGender').val() || '<?php echo ($patientQryRes[0]['sex']);?>';
            
            popUpRelationValue(i_key,d);
        }
        
        //For new patient case primary insurance policy holder
        
        $('#pri_lastName, #pri_subscriber_fname').on('click', function() {
            if($('#pri_subscriber_relationship').val() == "self" || $('#pri_subscriber_relationship').val() == "Self" && $('#fname').val()  && $('#lname').val()){
                makePtValArr('pri');
            }
        });
        //For new patient case secondary insurance policy holder
        $('#sec_lastName, #sec_subscriber_fname').on('click', function() {
            if($('#sec_subscriber_relationship').val() == "self" || $('#sec_subscriber_relationship').val() == "Self" && $('#fname').val()  && $('#lname').val()){
                makePtValArr('sec');
            }
        });
        
		$("body").on('change','select.minimal',function() {
			var v = $(this).find('option:selected').text();
			if( !v ) v = "Please Select";
			$(this).attr('title',v);
		});
	
        /* Policy holder section for primary/secondary ends Here*/
        
	</script>
	

	</body>
</html>
