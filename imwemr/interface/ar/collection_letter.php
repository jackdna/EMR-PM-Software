<?php
$collection_data_pdf=$letter_file_location=$error_log="";
$sent=$pt_total=$ins_total=0;
$data_pdf_css = "<style>
			.text_b_w{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#FFFFFF;
				background-color:#4684ab;
			}
			.text_10b{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				background-color:#FFFFFF;
			}
			.text_10{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
			}
		</style>";
	foreach($main_pt_id_ins_wise_arr as $ins_id => $sub_patient_id_arr)
	{
		$ins_total++;
		//get insurance detail
		$ins_detail_arr=$insCompArr[$ins_id];
		foreach($sub_patient_id_arr  as $pat_key => $patient_id){
			$pt_total++;
			//--- GET Encounter ID ARRAY ----
			//$encounterId=$main_encounter_id_arr[$e];
			$pat_name_arr['TITLE'] = $mainPatResArr[$patient_id][0]['title'];
			$pat_name_arr['LAST_NAME'] = $mainPatResArr[$patient_id][0]['lname'];
			$pat_name_arr['FIRST_NAME'] = $mainPatResArr[$patient_id][0]['fname'];
			$pat_name_arr['MIDDLE_NAME'] = $mainPatResArr[$patient_id][0]['mname'];
			$patientName = changeNameFormat($pat_name_arr);
			$res_name_arr['TITLE'] = $mainPatResArr[$patient_id][0]['res_title'];
			$res_name_arr['LAST_NAME'] = $res_party_arr[$patient_id][0]['res_lname'];
			$res_name_arr['FIRST_NAME'] = $res_party_arr[$patient_id][0]['res_fname'];
			$res_name_arr['MIDDLE_NAME'] = $res_party_arr[$patient_id][0]['res_mname'];
			$resName = changeNameFormat($res_name_arr);
			$patientEmail = $mainPatResArr[$patient_id][0]['email'];
			if($_POST['letter_type']=='email' && $_POST['letter_to']=='patient'){
				if(!$patientEmail){
					$error_log.="$patientName-$patient_id:- Email ID not found.<br/>";
					continue;//skip rest of the processing
				}
			}

			$phy_name=$mainPatResArr[$patient_id][0]['physicianLname'].', '.$mainPatResArr[$patient_id][0]['physicianFname'];

			$collection_data_pdf=$collection_data;

			$raceShow				= trim($mainPatResArr[$patient_id][0]["race"]);
			$otherRace				= trim($mainPatResArr[$patient_id][0]["otherRace"]);
			if($otherRace) { 
				$raceShow			= $otherRace;
			}
			$language				= str_ireplace("Other -- ","",$mainPatResArr[$patient_id][0]["language"]);
			$ethnicityShow			= trim($mainPatResArr[$patient_id][0]["ethnicity"]);			
			$otherEthnicity			= trim($mainPatResArr[$patient_id][0]["otherEthnicity"]);
			if($otherEthnicity) { 
				$ethnicityShow		= $otherEthnicity;
			}

			//new variable added
			$collection_data_pdf = str_ireplace('{PATIENT MRN}',ucwords($mainPatResArr[$patient_id][0]['External_MRN_1']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{PATIENT MRN2}',ucwords($mainPatResArr[$patient_id][0]['External_MRN_2']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RACE}',$raceShow,$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{LANGUAGE}',$language,$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{ETHNICITY}',$ethnicityShow,$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{PATIENT NAME TITLE}',ucwords($mainPatResArr[$patient_id][0]['title']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{MIDDLE NAME}',ucwords($mainPatResArr[$patient_id][0]['mname']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{PATIENT CITY}',ucwords($mainPatResArr[$patient_id][0]['city']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{STATE ZIP CODE}',ucwords($mainPatResArr[$patient_id][0]['state'].' '.$mainPatResArr[$patient_id][0]['postal_code']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{DOB}',ucwords($mainPatResArr[$patient_id][0]['pat_dob']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{PatientID}',$patient_id,$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{DATE}',date(''.$global_date_format.''),$collection_data_pdf);
			//new variable added

			$collection_data_pdf = str_ireplace('{FULL NAME}',ucwords($patientName),$collection_data_pdf);
			//$collection_data_pdf = str_ireplace('{FIRST NAME}',ucwords($mainPatResArr[$patient_id][0]['fname']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{PATIENT FIRST NAME}',ucwords($mainPatResArr[$patient_id][0]['fname']),$collection_data_pdf);

			$collection_data_pdf = str_ireplace('{LAST NAME}',ucwords($mainPatResArr[$patient_id][0]['lname']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{SUFFIX}',ucwords($mainPatResArr[$patient_id][0]['suffix']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{ADDRESS1}',ucwords($mainPatResArr[$patient_id][0]['street']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{ADDRESS2}',ucwords($mainPatResArr[$patient_id][0]['street2']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{HOME PHONE}',core_phone_format($mainPatResArr[$patient_id][0]['phone_home']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{WORK PHONE}',core_phone_format($mainPatResArr[$patient_id][0]['phone_biz']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{MOBILE PHONE}',core_phone_format($mainPatResArr[$patient_id][0]['phone_cell']),$collection_data_pdf);
			//==============RESPONSIBLE PARTY VARIABLE REPLACEMENT STARTS HERE==============
			//==IF PATIENT HAVE NO RESPONSIBLE PERSON THEN PATIENT DETAILS WILL REPLACE WITH RESPONSIBLE PERSON DETAILS
			if(!empty($resName) || count($res_party_arr[$patient_id][0])>0){
			$collection_data_pdf = str_ireplace('{RES FULL NAME}',ucwords($resName),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($res_party_arr[$patient_id][0]['res_fname']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY Last NAME}',ucwords($res_party_arr[$patient_id][0]['res_lname']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES SUFFIX}',ucwords($res_party_arr[$patient_id][0]['res_suffix']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY CITY}',ucwords($res_party_arr[$patient_id][0]['city']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY STATE}',ucwords($res_party_arr[$patient_id][0]['state']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY ZIP}',ucwords($res_party_arr[$patient_id][0]['zip']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY HOME PH.}',core_phone_format($res_party_arr[$patient_id][0]['home_ph']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY WORK PH.}',core_phone_format($res_party_arr[$patient_id][0]['work_ph']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY MOBILE PH.}',core_phone_format($res_party_arr[$patient_id][0]['mobile']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($res_party_arr[$patient_id][0]['address']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($res_party_arr[$patient_id][0]['address2']),$collection_data_pdf);

			}else{

			$collection_data_pdf = str_ireplace('{RES FULL NAME}',ucwords($patientName),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY FIRST NAME}',ucwords($mainPatResArr[$patient_id][0]['fname']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY Last NAME}',ucwords($mainPatResArr[$patient_id][0]['lname']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES SUFFIX}',ucwords($mainPatResArr[$patient_id][0]['suffix']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY CITY}',ucwords($mainPatResArr[$patient_id][0]['city']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY STATE}',ucwords($mainPatResArr[$patient_id][0]['state']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY ZIP}',ucwords($mainPatResArr[$patient_id][0]['postal_code']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY HOME PH.}',core_phone_format($mainPatResArr[$patient_id][0]['phone_home']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY WORK PH.}',core_phone_format($mainPatResArr[$patient_id][0]['phone_biz']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY MOBILE PH.}',core_phone_format($mainPatResArr[$patient_id][0]['phone_cell']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY ADDRESS1}',ucwords($mainPatResArr[$patient_id][0]['street']),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{RES.PARTY ADDRESS2}',ucwords($mainPatResArr[$patient_id][0]['street2']),$collection_data_pdf);	

			}
			//=========================RESPONSIBLE PARTY DATA REPLACEMENT ENDS HERE==========================	

			$collection_data_pdf = str_ireplace('{TOTAL OUTSTANDING CHARGES}',$showCurrencySymbol.number_format(array_sum($mainPatTotBalArr[$patient_id]) ,2),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{DOS}',implode(',',$mainPatDOSArr[$patient_id]),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{CHARGES}',$showCurrencySymbol.number_format(array_sum($mainPatTotAmtArr[$patient_id]),2),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{DOS & CHARGES}',implode(',',$mainPatDOSArr[$patient_id]).' & '.$showCurrencySymbol.number_format(array_sum($mainPatTotAmtArr[$patient_id]),2),$collection_data_pdf);

			//$collection_data_pdf = str_ireplace('{PHYSICIAN}',ucwords($phy_name),$collection_data_pdf);
			$collection_data_pdf = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name),$collection_data_pdf);

			 //=========================IMAGE PATH REPLACEMENT WORK START HERE ==========================	
			$collection_data_pdf = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$collection_data_pdf);
			$collection_data_pdf = str_ireplace($web_root.'/interface/reports/new_html2pdf/','',$collection_data_pdf);
			$collection_data_pdf = str_ireplace($web_root.'/interface/main/uploaddir/document_logos/','../../main/uploaddir/document_logos/',$collection_data_pdf);
			$collection_data_pdf = str_ireplace('%20',' ',$collection_data_pdf);
			$collection_data_pdf = mb_convert_encoding($collection_data_pdf, "HTML-ENTITIES", 'UTF-8');

			$curDate = date(''.$global_date_format.' m:i A');		
			//-- OPERATOR INITIAL -------
			$authProviderNameArr = preg_split('/ /',strtoupper($_SESSION['authProviderName']));
			$opInitial = $authProviderNameArr[1][0];
			$opInitial .= $authProviderNameArr[0][0];
			$opInitial = strtoupper($opInitial);
			//add css each time in case of email to patient only
			if($_POST['letter_to']=='patient')$data_pdf=$data_pdf_css;
			$data_pdf.='<page backtop="0mm" backbottom="5mm">
						<page_footer></page_footer>'.$collection_data_pdf.'</page>';

			//DATA SAVING FRO PT DOCS
			$pt_doc_collection_pdf='<page backtop="0mm" backbottom="5mm">
						<page_footer>
						</page_footer>
						'.$collection_data_pdf.'
						</page>';

			/*	REPLACING SMART TAG OPTONS WITH NON-ANCHOR STRING. */
			$regpattern='|<a class=\"cls_smart_tags_link\" id=(.*) href=(.*)>(.*)<\/a>|U'; 
			$pt_doc_collection_pdf = preg_replace($regpattern, "\\3", $pt_doc_collection_pdf);
			$regpattern='|<a id=(.*) class=\"cls_smart_tags_link\" href=(.*)>(.*)<\/a>|U'; 
			$pt_doc_collection_pdf = preg_replace($regpattern, "\\3", $pt_doc_collection_pdf);

			preg_replace('/[^`~!<>@$?a-zA-Z0-9_{}:; ,#%\[\]\.\(\)%&-\/\\r\\n\\\\]/s','',$pt_doc_collection_pdf);
			
			$str_sent_to="";
			if(($_POST['letter_type']=='email' || $_POST['letter_type']=='fax') && $_POST['letter_to']=='insurance'){
				$str_sent_to=",sent_to=1";
			}
			$qry="Insert INTO pt_docs_collection_letters SET 
			patient_id='".$patient_id."',
			template_id='".$collectionTemplateId."',
			template_content='".htmlentities(addslashes(trim($pt_doc_collection_pdf)))."',
			created_date='".date('Y-m-d H:i:s')."',
			operator_id='".$_SESSION['authId']."' $str_sent_to";					
			$rs=imw_query($qry);
			$inserted_id=imw_insert_id($rs);
			//send email in case of patient
			if($_POST['letter_type']=='email' && $_POST['letter_to']=='patient')
			{	
				$letter_html_file = write_html($data_pdf,'letter_pdf.html');
				$pdfOp = 'p';
				$pdfVer = 'html_to_pdf';
				$pdfCreate = 'createPdf.php';
				$getPCIP=$_SESSION["authId"];			
				$getIP=str_ireplace(".","_",$getPCIP);
				$setNameFaxPDF="collection_letter_".$getIP.".pdf"; //FAX PDF NAME
				$pdfPath=data_path()."UserId_".$_SESSION['authUserID']."/tmp/ar/"; 
				$myHTTPAddress = $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php';

				$curNew = curl_init();
				$urlPdfFile=$myHTTPAddress."?setIgnoreAuth=true&saveOption=F&font_size=8.0&page=1.3&pdf_name=". $pdfPath.$setNameFaxPDF ."&file_location=". $letter_html_file;
				curl_setopt($curNew, CURLOPT_URL,$urlPdfFile);
				curl_setopt($curNew, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curNew, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
				$data = curl_exec($curNew);
				curl_close($curNew); 
				//===============CREATED PDF ADDRESS==================
				$letter_pdf_file= $pdfPath.$setNameFaxPDF;
				$filetype= 'PDF';
				if(!($fp = fopen($letter_pdf_file, "r"))){ 
					$error_log.="$patientName-$patient_id:- Error opening PDF file. <br>";
				}
				//convert HTML into a basic plain-text alternative body
				$mail->msgHTML($groupEmailConfig['header']."<br/>Please find attached document.<br/>".$groupEmailConfig['footer']);
				//Attach an image file
				if($letter_pdf_file)$mail->addAttachment($letter_pdf_file);
				$mail->addAddress($patientEmail,$patientName);
				$email_status='';
				if (!$mail->send()) {
					$failed++;
					$error_log.="$patientName-$patient_id:- Mailer error:".$mail->ErrorInfo.".<br/>";
					$email_status='failed';
				} else {
					$sent++;
					$email_status='sent';
				}

				//UPDATE RECORD
				imw_query("Update pt_docs_collection_letters SET email_sent='1', email_status='".$email_status."' WHERE id='".$inserted_id."'");

				// Clear all addresses and attachments for next loop
				$mail->clearAddresses();
				$mail->clearAttachments();
				//remove html and pdf file from disk
				unlink($letter_html_file,$letter_pdf_file);
				unset($data_pdf);
			}

		}//patient loop for this insurance ends here
		//send fax or email to insurance
		if($_POST['letter_to']=='insurance')
		{	
			if(!$ins_id && $ins_id<=0)
			{
				$error_log.="Insurance company not found. <br>";
				//remove html and pdf file from disk
				unlink($letter_html_file);
				unset($data_pdf);
				continue;
			}
			$letter_html_file = write_html($data_pdf_css.$data_pdf,'letter_pdf.html');
			$pdfOp = 'p';
			$pdfVer = 'html_to_pdf';
			$pdfCreate = 'createPdf.php';
			$getPCIP=$_SESSION["authId"];			
			$getIP=str_ireplace(".","_",$getPCIP);
			$setNameFaxPDF="collection_letter_".$getIP.".pdf"; //FAX PDF NAME
			$pdfPath=data_path()."UserId_".$_SESSION['authUserID']."/tmp/ar/"; 
			$myHTTPAddress = $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php';

			$curNew = curl_init();
			$urlPdfFile=$myHTTPAddress."?setIgnoreAuth=true&saveOption=F&font_size=8.0&page=1.3&pdf_name=". $pdfPath.$setNameFaxPDF ."&file_location=". $letter_html_file;
			curl_setopt($curNew, CURLOPT_URL,$urlPdfFile);
			curl_setopt($curNew, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curNew, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
			$data = curl_exec($curNew);
			curl_close($curNew); 
			//===============CREATED PDF ADDRESS==================
			$letter_pdf_file= $pdfPath.$setNameFaxPDF;
			$filetype= 'PDF';
			if(!($fp = fopen($letter_pdf_file, "r"))){ 
				$error_log.=$ins_detail_arr['name'].":- Error opening PDF file. <br>";
			}
			if($_POST['letter_type']=='email')
			{
				if(!$ins_detail_arr['email'])
				{
					$error_log.=$ins_detail_arr['name'].":- Email ID not found. <br>";
					//remove html and pdf file from disk
					unlink($letter_html_file,$letter_pdf_file);
					unset($data_pdf);
					continue;
				}
				
				//convert HTML into a basic plain-text alternative body
				$mail->msgHTML($groupEmailConfig['header']."<br/>Please find attached document.<br/>".$groupEmailConfig['footer']);
				//Attach an image file
				if($letter_pdf_file)$mail->addAttachment($letter_pdf_file);
				$mail->addAddress($ins_detail_arr['email'],$ins_detail_arr['name']);
				if (!$mail->send()) {
					$failed++;
					$error_log.="$patientName-$patient_id:- Mailer error:".$mail->ErrorInfo.". <br>";
					$email_status='failed';
				} else {
					$sent++;
					$email_status='sent';
				}

				//UPDATE RECORD
				imw_query("Update pt_docs_collection_letters SET email_sent='1', email_status='".$email_status."' WHERE id='".$inserted_id."'");

				// Clear all addresses and attachments for next loop
				$mail->clearAddresses();
				$mail->clearAttachments();
			}elseif($_POST['letter_type']=='fax')
			{
				if(!$ins_detail_arr['fax'])
				{
					$error_log.=$ins_detail_arr['name'].":- Fax number not found. <br>";
					//remove html and pdf file from disk
					unlink($letter_html_file,$letter_pdf_file);
					unset($data_pdf);
					continue;
				}
				
				if($letter_pdf_file)
				{
					$faxnumber 		= $ins_detail_arr['fax'];//insurance company fax number 
					$faxRecipent	= $ins_detail_arr['name'];//insurance company name

					//=====FAX RECIPENT REQUESTED FOR USING UPDOX FAX FUNCTIONALITY========
					$faxnumber = preg_replace('/[^0-9+]/', "", $faxnumber);
					if( is_updox('fax') ){
						$PDFContent = base64_encode(file_get_contents($letter_pdf_file));
						//===========UPDOX FAX WORKS STARTS FROM HERE=========
						include($GLOBALS['srcdir'].'/updox/updoxFax.php');  //UPDOX LIBRAY FILE
						$updox = new updoxFax();  //UPDOX OBJECT
						//========SEND FAX TO PRIMARY RECIPENT WORK==========
						$resp  = $updox->sendFax($faxRecipent, $faxnumber, $PDFContent);
					}
					elseif( is_interfax() )
					{
						if(!($fp = fopen($letter_pdf_file, "r"))){
							$resp['statusCode'] = '0';
							$resp['message'] = "Fax failed.";
							goto logfield;
						}
						$filetype = pathinfo($letter_pdf_file, PATHINFO_EXTENSION);
						$pdfContent = "";
						while(!feof($fp)) $pdfContent .= fread($fp,1024);
						fclose($fp);

						$client = new SoapClient("http://ws.interfax.net/dfs.asmx?WSDL");
						$params->Username  			= fax_username;
						$params->Password  			= fax_password;
						$params->FaxNumbers 		= $faxnumber;
						$params->FilesData  		= $pdfContent;
						$params->FileTypes  		= $filetype;
						$params->FileSizes   		= strlen($pdfContent);
						$params->Postpone   		= "2005-04-25T20:31:00-04:00";
						$params->IsHighResolution   = "0";
						$params->CSID   			= "";
						$params->Subject   			= "";
						$params->ReplyAddress 		= "";

						$result = $client->SendFaxEx($params);
						$returnMsg=$result->SendfaxExResult; // returns the transactionID if successful

						if($returnMsg>0){
							$resp['status'] = 'success';
							$resp['data']->faxId = $returnMsg;
						}
						elseif($returnMsg=='-1003'){
							$resp['statusCode'] = $returnMsg;
							$resp['message'] = "Authentication error";
						}
						elseif($returnMsg=='-112'){
							$resp['statusCode'] = $returnMsg;
							$resp['message'] = "No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
						}
						else{
							$resp['statusCode'] = $returnMsg;
							$resp['message'] = "Fax sending failed.";
						}
					}
					//goto statement varialbe for interfax
					logfield:
					if($resp['status']=='success'){
						$sent++;
						//IF FAX STATUS IS SUCCESS, TRANSACTION ALERT WILL BE POPULATE
						//$customMsg = "Transaction_No. ".($resp['data']->faxId)." - Fax sent successfully ";	

						  if(trim($formId) || trim($worksheetId)) {
								//INSERT INTO SEND FAX LOG WHEN NO CONSULT ID IS COMING
								$qry_insrt_sendfaxlog ="INSERT INTO `send_fax_log_tbl` SET patient_id='".$patient_id."', 
								folder_date='".date('Y-m-d')."', 
								operator_id='".$OperatorId."', 
								updox_id='".$resp['data']->faxId."', 
								updox_status='queued', 
								fax_type='Primary', 
								file_name='".$log_file_name."', 
								fax_number='".$faxnumber."',
								section_name='ar_worksheet' ";
								$sendfaxlog_row = imw_query($qry_insrt_sendfaxlog);
								$sendfaxlogInsertId = imw_insert_id();
						  }
					}
					else
					{  
						//IF FAX FAILED, ALERT WILL DISPLAY WITH CODE AND MESSAGE.
						$error_log.=$ins_detail_arr['name'].":-".$resp['statusCode']." - ".$resp['message']." <br>";
					}
				}
				else
				{  
					//IF FAX FAILED, ALERT WILL DISPLAY WITH CODE AND MESSAGE.
					$error_log.=$ins_detail_arr['name'].":- Error PDF file not found. <br>";
				}
			}
			//remove html and pdf file from disk
			unlink($letter_html_file,$letter_pdf_file);
			unset($data_pdf);
		}
		
	}
	
	if($_POST['letter_type']=='email'){
		if($_POST['letter_to']=='insurance') $customMsg="$sent/$ins_total email(s) sent successfully.<br/>".$error_log;
		else $customMsg="$sent/$pt_total email(s) sent successfully.<br/>".$error_log;
	}elseif($_POST['letter_type']=='fax'){
	 	$customMsg="$sent/$ins_total fax(s) sent successfully.<br/>".$error_log;
	}
?>