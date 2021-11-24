<?php
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');
include(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
include(dirname(__FILE__)."/../../library/classes/work_view/wv_functions.php");
include(dirname(__FILE__)."/../../library/classes/work_view/ChartNote.php");
include(dirname(__FILE__)."/../../library/classes/work_view/ChartAP.php");
include(dirname(__FILE__)."/../../library/classes/work_view/MedHx.php");
include(dirname(__FILE__)."/../../library/classes/work_view/Fu.php");
include(dirname(__FILE__)."/../../library/classes/work_view/PnTempParser.php");
include(dirname(__FILE__)."/../../library/classes/work_view/CcHx.php");
include(dirname(__FILE__)."/../../library/classes/work_view/Patient.php");

//ini_set("display_errors",1);
class consent_forms extends patient_app{	
	var $reqModule;
	var $arrProvider = array();
	var $template_id;
	public function __construct($patient){
		parent::__construct($patient);
		$this->template_id = $_REQUEST['temp_id'];
	}
	public function get_admin_consent_forms(){
		$i=0;
		$arrReturn=array();
		$qry_consent_cat="select cat_id,category_name from consent_category";
		$res_consent_cat=imw_query($qry_consent_cat);
		if(imw_num_rows($res_consent_cat)>0){
			while($row_consent_cat=imw_fetch_assoc($res_consent_cat)){
				$category_id=$row_consent_cat['cat_id'];
				$category_name=trim($row_consent_cat['category_name']);
				$arrReturn['consent_forms'][$i] = array("level"=>0,"name"=>$category_name);
				$qry_consent_form="Select consent_form_id,consent_form_name,cat_id FROM consent_form WHERE consent_form_status='Active' and cat_id='".$category_id."' ORDER by consent_form_name";
				$res_consent_form=imw_query($qry_consent_form);
				while($row_consent_form=imw_fetch_assoc($res_consent_form)){
					$consent_form_name=trim($row_consent_form['consent_form_name']);
					$consent_form_id=$row_consent_form['consent_form_id'];
					$consent_form_cat_id=$row_consent_form['cat_id'];
					$arr_consent_form[$consent_form_cat_id]=$consent_form_id;
					$arr_consent_form_id[$consent_form_id]=$consent_form_name;
					$arrReturn['consent_forms'][$i]['Objects'][] = array("level"=>1,"name"=>$consent_form_name,"id"=>$consent_form_id,"type"=>"new");
				}
				$i++;
			}
		}
		return $arrReturn;
	}
	
	public function get_consent_forms(){
		
		//require_once("../interface/main/main_functions.php");
		$arrReturn = array();
		//$arrReturn['Signed Forms']["04/23/2015"] = "PDF1";
		//$arrReturn['Signed Forms']["05/25/2015"] = "PDF1";
		//$arrReturn['Signed Package']["04/23/2015"] = "PDF1";
		//$arrReturn['Signed Package']["05/25/2015"] = "PDF1";
		$arrReturn['consent_forms'][0] = array("level"=>0,"name"=>"Signed Forms");
		$arrReturn['consent_forms'][1] = array("level"=>0,"name"=>"Signed Packages"); 
		$arrReturn['consent_forms'][2] = array("level"=>0,"name"=>"Trash");
		
		//------------GET SIGNED FORMS DATA------------------------------------------
		$this->db_obj->qry = "SELECT DATE_FORMAT(pcf.form_created_date,'%m/%d/%y') AS date, chart_procedure_id, GROUP_CONCAT(chart_procedure_id)as c_proc,
									GROUP_CONCAT(pcf.form_information_id) as form_information_id,pcf.package_category_id
									FROM patient_consent_form_information pcf
									WHERE pcf.patient_id='".$this->patient."'
									AND  movedToTrash=0
									AND package_category_id = 0
									GROUP BY DATE_FORMAT(pcf.form_created_date,'%y-%m-%d')
									ORDER BY pcf.form_created_date DESC
									";
		$arrImage = $this->db_obj->get_resultset_array();	
		$i=0;
		foreach($arrImage as $key=>$arr){
			//Check procedure notes related consent form
			$chart_procedure_id = $arr['chart_procedure_id'];
			$chart_proc_ids_arr = explode(",",$arr['c_proc']);
			if(!empty($chart_procedure_id) && !isProcedureNoteFinalized($chart_procedure_id) && !in_array("0",$chart_proc_ids_arr)){
				continue;
			}
			//--
		
			$arrReturn['consent_forms'][0]['Objects'][$i] = array("level"=>1,"name"=>$arr['date']);
			$arrReturn['consent_forms'][1]['Objects'][$i] = array("level"=>1,"name"=>$arr['date']);
			
			{
				$this->db_obj->qry = "SELECT pcf.consent_form_name, pcf.form_created_date,pcf.form_information_id AS id,pcf.chart_procedure_id,
									users.fname, users.lname,pcf.package_category_id,pcf.package_category_id
									FROM patient_consent_form_information pcf
									JOIN users ON users.id = pcf.operator_id
									WHERE pcf.form_information_id  IN (".$arr['form_information_id'].")
									AND  movedToTrash=0
									AND package_category_id = 0
									ORDER BY pcf.form_created_date DESC
									";
				
			   $arrTemp = $this->db_obj->get_resultset_array();	
				foreach($arrTemp  as $tempArr){
				$chart_procedure_id = $tempArr['chart_procedure_id'];
				if(!empty($chart_procedure_id) && !isProcedureNoteFinalized($chart_procedure_id)){
					continue;
				}
		
					if($tempArr['package_category_id']==0){
						$mod_date 	= date("g:i A",strtotime($tempArr['form_created_date']));
						$opr      	= ucfirst($tempArr['fname'][0])." ".ucfirst($tempArr['lname'][0]);
						$str 		= '('.$mod_date.' '.$opr.')';
						$arrReturn['consent_forms'][0]['Objects'][$i]['Objects'][] = array("level"=>2,"name"=>$tempArr['consent_form_name'].$str,"id"=>$tempArr['id']);
					}else{
						//------------GET SIGNED PACKAGE DATA------------------------------------------
						$arrReturn['consent_forms'][1]['Objects'][$i]['Objects'][] = array("level"=>2,"name"=>$tempArr['consent_form_name'].$str,"id"=>$tempArr['id']);
						//--------------------------------------------------------------------------//
					}
				}
			}
			$i++;
		}
		//--------------------------------------------------------------------------
		
		//------------GET FAX DATA------------------------------------------
		/*$this->db_obj->qry = "SELECT DATE_FORMAT(pcl.date,'%m/%d/%y') AS date,
									 GROUP_CONCAT(pcl.patient_consult_id) as patient_consult_id
									FROM patient_consult_letter_tbl  pcl
									WHERE pcl.patient_id='".$this->patient."' 
										AND pcl.fax_status = 1
									GROUP BY pcl.date
									ORDER BY pcl.date DESC
									";
		$arrImage = $this->db_obj->get_resultset_array();	
		foreach($arrImage as $key=>$arr){
			
			$arrReturn['consent_forms'][1]['Objects'][$key] = array("level"=>1,"name"=>$arr['date']);
			{
				$this->db_obj->qry = "SELECT pcl.templateName , pcl.cur_date,
									 	users.fname, users.lname
									FROM patient_consult_letter_tbl  pcl
									JOIN users ON users.id = pcl.operator_id
									WHERE pcl.patient_consult_id  IN (".$arr['patient_consult_id'].")
									ORDER BY pcl.date DESC
									";
			   $arrTemp = $this->db_obj->get_resultset_array();	
				foreach($arrTemp  as $tempArr){
					$mod_date 	= date("g:i A",strtotime($tempArr['cur_date']));
					$opr      	= ucfirst($tempArr['fname'][0])." ".ucfirst($tempArr['lname'][0]);
					$str 		= '('.$mod_date.' '.$opr.')';
					$arrReturn['consent_forms'][1]['Objects'][$key]['Objects'][] = array("level"=>2,"name"=>$tempArr['templateName'].$str);
				}
			}
		}*/
		//--------------------------------------------------------------------------
		
		//-----GET TRASH DATA------------------------------------------------
		$this->db_obj->qry = "SELECT DATE_FORMAT(pcf.form_created_date,'%m/%d/%y') AS date,
									 GROUP_CONCAT(pcf.form_information_id) as form_information_id
									FROM patient_consent_form_information pcf
									WHERE pcf.patient_id='".$this->patient."' 
										AND pcf.movedToTrash = 1
									GROUP BY DATE_FORMAT(pcf.form_created_date,'%y-%m-%d')
									ORDER BY pcf.form_created_date DESC
									";
		$arrImage = $this->db_obj->get_resultset_array();	
		foreach($arrImage as $key=>$arr){
			$arrReturn['consent_forms'][2]['Objects'][$key] = array("level"=>1,"name"=>$arr['date']);
			{
				$this->db_obj->qry = "SELECT pcf.consent_form_name , pcf.form_created_date,pcf.form_information_id AS id,
									 	users.fname, users.lname
									FROM patient_consent_form_information pcf
									JOIN users ON users.id = pcf.operator_id
									WHERE pcf.form_information_id  IN (".$arr['form_information_id'].")
									ORDER BY pcf.form_created_date DESC
									";
			   $arrTemp = $this->db_obj->get_resultset_array();	
				foreach($arrTemp  as $tempArr){
					$mod_date 	= date("g:i A",strtotime($tempArr['form_created_date']));
					$opr      	= ucfirst($tempArr['fname'][0])." ".ucfirst($tempArr['lname'][0]);
					$str 		= '('.$mod_date.' '.$opr.')';
					$arrReturn['consent_forms'][2]['Objects'][$key]['Objects'][] = array("level"=>2,"name"=>$tempArr['consent_form_name'].$str,"id"=>$tempArr['id']);
				}
			}
		}
		//--------------------------------------------------------------------------
		/*$arrReturn['Objects'][] = "Signed Forms";
		$arrReturn['Objects']['Signed Forms'] = array("name"=>"name 1","level"=>0);
		$arrReturn['Objects'][] = "Signed Package";*/
		/*$arrReturn['Objects'][] = "item0";
		$arrReturn['Objects']['item0'] = array("name"=>"name 1","level"=>0);
		$arrReturn['Objects'][] = "item1";
		$arrReturn['Objects']['item1'] = array("name"=>"name 2","level"=>0);*/
		$arrReturn['patient_data'] = $this->get_patient_data();
		return $arrReturn;
	}
	public function create_pdf(){
		$arrReturn = array();
		global $webServerRootDirectoryName,$myExternalIP,$web_RootDirectoryName,$phpHTTPProtocol;
		foreach($arr_consent  as $tempArr){
			$consent_form_content=stripcslashes(html_entity_decode($tempArr["consent_form_content_data"])); 
		}
	
		$_REQUEST['patient_id']=$this->patient;
		$_REQUEST['consent_id']=$this->template_id;
		$_REQUEST['callFromApp'] = true;
		//pre($_REQUEST);die;
		//include_once(realpath(dirname(__FILE__).'/../../interface/patient_info/consent_forms/print_consent_form_app.php'));
		include_once(dirname(__FILE__).'/print_all_consent_form_app.php');
		$consentData = get_consent();
		$consentData = html_entity_decode($consentData);
		$fileLocation = data_path()."app_services/tmp/";
		if(empty($consentData) == false){
			$consentData = str_replace(data_path(1), $GLOBALS['php_server'].'/data/'.constant('PRACTICE_PATH').'/', $consentData);
			
			if(!is_dir($fileLocation)) mkdir($fileLocation, 0777, true);
			$filePath = $fileLocation.'consentPDF.pdf';
			if(file_exists($filePath)) unlink($filePath);
			$html2pdf = new HTML2PDF('P','A4','en');
			$html2pdf->setTestTdInOnePage(false);
			$html2pdf->WriteHTML($consentData);
			$html2pdf->Output($filePath, 'F');
			
			$filePath = str_replace($GLOBALS['fileroot'], $phpHTTPProtocol.$myExternalIP.'/'.$web_RootDirectoryName, $filePath);
			//$filePath = str_replace($GLOBALS['fileroot'], $GLOBALS['php_server'], $filePath);
			$arrReturn["URL"] = $filePath;
		}
		return $arrReturn; 
		
		/* echo $consentData;die;
		unset($_REQUEST['callFromApp']);
		$myHTTPAddress =$phpHTTPProtocol.$myExternalIP.'/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/createPdf.php';
		$setNameFaxPDF="pdffile_appc_iapp";
		unlink($webServerRootDirectoryName.'/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/'.$setNameFaxPDF.'.pdf');
		$urlPdfFile=$myHTTPAddress."?saveOption=fax&htmlFileName=pdffile_appc&name=".$setNameFaxPDF;
		$curNew = curl_init();
		curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
		curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
		$data = curl_exec($curNew);
		$arrReturn["URL"] = $GLOBALS['php_server']."/interface/common/new_html2pdf/".$setNameFaxPDF.".pdf";
		return $arrReturn;  */
	}
	public function get_patient_consent_form(){
		
		$get_mad_conent_qry_="SELECT * from consent_form WHERE consent_form_id='".$this->template_id."'  LIMIT 0,1";
		$get_mas_consent_qry=imw_query($get_mad_conent_qry_);
		$get_mas_consent_arr=imw_fetch_assoc($get_mas_consent_qry);
		
		$arrReturn['consent'][0]['name'] = $get_mas_consent_arr['consent_form_name'];
		$arrReturn['consent'][0]['data'] = stripslashes(html_entity_decode($get_mas_consent_arr['consent_form_content'],ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1'));
		$arrReturn['consent'][0]['id']   = $get_mas_consent_arr['consent_form_id'];
		$consent_name_arr[$get_mas_consent_arr['consent_form_id']]=$get_mas_consent_arr['consent_form_name'];
		$_REQUEST['print_false']=1;
		
		$_REQUEST['patient']=$_REQUEST['pt_id'];
		$_REQUEST['patId']=$_REQUEST['pt_id'];
		$_REQUEST['form_id']=$this->template_id;
		$_REQUEST['phyId']=$_REQUEST['op_id'];
		$_REQUEST['consent_id']=$get_mas_consent_arr['consent_form_id'];
		$todayDate=explode('-',date('Y-m-d'));
		$consent_form_content = '';
		if(isset($_REQUEST['tbl_pri_id']))
		{
				
			//check in main consent form table
				$qry_check_form2="select consent_form_content_data from patient_consent_form_information where patient_id='$_REQUEST[pt_id]' and form_information_id='$_REQUEST[tbl_pri_id]'";
				$QUERY_CHECK_MAIN=imw_query($qry_check_form2)or die(imw_error());
				if(imw_num_rows($QUERY_CHECK_MAIN)>=1)
				{
					//get and send saved data
					$DATA_MAIN=imw_fetch_object($QUERY_CHECK_MAIN);
					$consent_form_content=html_entity_decode(html_entity_decode($DATA_MAIN->consent_form_content_data,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1'));	
                                        //$consent_form_content=($DATA_MAIN->consent_form_content_data);
				}	
		}
		else
			
			{
			//check do we have saved form for that date in temporary folder
			$qry_check_form="select consent_form_content_data from patient_consent_form_information_app where patient_id='$_REQUEST[pt_id]' and chart_procedure_id = 0 and consent_form_id='$_REQUEST[form_id]' and YEAR(form_created_date)='".$todayDate[0]."' and MONTH(form_created_date)='".$todayDate[1]."' and DAY(form_created_date)='".$todayDate[2]."' and consent_form_content_data!=''";
			$QUERY_CHECK=imw_query($qry_check_form)or die(imw_error());

			if(imw_num_rows($QUERY_CHECK) > 0)
			{
				//get and send saved data
				$DATA=imw_fetch_assoc($QUERY_CHECK);
				//$consent_form_content= html_entity_decode($DATA['consent_form_content_data'],ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1');
				$consent_form_content=html_entity_decode($DATA['consent_form_content_data']);
			}
			else
			{
				//check in main consent form table
				$qry_check_form2="select consent_form_content_data from patient_consent_form_information where patient_id='$_REQUEST[pt_id]' and consent_form_id='$_REQUEST[form_id]' and YEAR(form_created_date)='".$todayDate[0]."' and MONTH(form_created_date)='".$todayDate[1]."' and DAY(form_created_date)='".$todayDate[2]."' and consent_form_content_data!='' and chart_procedure_id=0 ";
				$QUERY_CHECK_MAIN=imw_query($qry_check_form2)or die(imw_error());
				if(imw_num_rows($QUERY_CHECK_MAIN)> 0)
				{
					//get and send saved data
					$DATA_MAIN=imw_fetch_object($QUERY_CHECK_MAIN);
					//$consent_form_content=html_entity_decode($DATA_MAIN->consent_form_content_data,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1');	
                                        $consent_form_content=html_entity_decode($DATA_MAIN->consent_form_content_data);
				}
				else
				{
					include(dirname(__FILE__)."/consentFormDetails_app.php");
					$sigPatsStr=serialize($sigPats);
					if($_REQUEST[form_id])
					{
						if($protocol==''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
						if($protocol=='https://')$consent_form_content = str_ireplace('http://','https://',$consent_form_content);
					
						$ret=imw_query("insert into patient_consent_form_information_app set consent_form_id='$_REQUEST[form_id]',
								consent_form_name='".htmlentities(addslashes(trim($consent_form_name)))."',
								patient_id='$_REQUEST[pt_id]',
								operator_id='$_REQUEST[op_id]',
								form_created_date='".date('Y-m-d H:i:s')."',
								patient_signature='".imw_real_escape_string($sigPatsStr)."',
								consent_form_content_data='".imw_real_escape_string(trim($consent_form_content))."'")or die(imw_error());
					}
					$consent_form_content = html_entity_decode($consent_form_content);
				}	
			}
		}
		echo $consent_form_content;
		die();
	}
	
	public function save_patient_consent_form()
	{
		$_REQUEST['form_id']=$this->template_id;
		global $webServerRootDirectoryName,$web_RootDirectoryName;
		$todayDate=explode('-',date('Y-m-d'));
		
		//get image name
		$file_name=$_REQUEST['sig_img_key'];
		//$filePathOrg=$webServerRootDirectoryName.$web_RootDirectoryName."/interface/common/new_html2pdf/app";
		$filePathOrg = data_path()."PatientId_".$_REQUEST['pt_id']."/sign";
		//if dir not exist then create it
		if(!is_dir($filePathOrg)){
			$mk_dir=mkdir($filePathOrg,0777,true);
			if(!$mk_dir){
				file_put_contents('errorLog.txt',"unable to create folder: ".$filePathOrg." \n", FILE_APPEND);		
			}
		}
		$filePathOrg.="/".$file_name;
		//$replace="/".$web_RootDirectoryName."/interface/common/new_html2pdf/app/".$file_name;
		$replace = data_path(1)."PatientId_".$_REQUEST['pt_id']."/sign/".$file_name;
		if($_REQUEST['typ']=='sigimg')
		{
			//$filePath=dirname(__FILE__)."/../../../interface/common/new_html2pdf/app/";
			//$target="/".$web_RootDirectoryName."/app_services/signature/tmp/".$_REQUEST['op_id'].'/'.$file_name;
			$target = data_path(1)."app_services/signature/tmp/".$_REQUEST['op_id']."/admin_consent/".$file_name;
			if($_FILES['sig_img_key']['name']!='')
			{
				move_uploaded_file($_FILES['sig_img_key']['tmp_name'],$filePathOrg);
				$this->update_patient_consent_form($_REQUEST['pt_id'],$_REQUEST['form_id'],$target,$replace,'sign','',$file_name);
			}
			
		}
		else if($_REQUEST['typ']=='content')
		{
			//code to handle json response	
			$handle = fopen('php://input','r');
			$jsonInput = fgets($handle);
			// Decoding JSON into an Array
			$decoded = json_decode($jsonInput);
			foreach($decoded as $data){
				$consent_form_content_data=$data->content_key;	
			}
			
			$this->update_patient_consent_form($_REQUEST['pt_id'],$_REQUEST['form_id'],'','','content',$consent_form_content_data);
		}
		// To handle html response for new app
		else if($_REQUEST['typ']=='content_app')
		{
			//code to handle html response	
			 $handle = file_get_contents('php://input');
			$this->update_patient_consent_form($_REQUEST['pt_id'],$_REQUEST['form_id'],'','','content',$handle);
		}
		
		else if($_REQUEST['typ']=='save')
		{
			$this->update_patient_consent_form($_REQUEST['pt_id'],$_REQUEST['form_id'],'','','save','');
		}
	}
	public function update_patient_consent_form($patient_id,$consent_form_id,$target,$replace,$typ,$content,$sig_id)
	{
		
		$todayDate=explode('-',date('Y-m-d'));
		//check do we have saved form for that date
		$qry_ch="select * from patient_consent_form_information_app where patient_id='$_REQUEST[pt_id]'  and consent_form_id='$_REQUEST[form_id]' and YEAR(form_created_date)='".$todayDate[0]."' and MONTH(form_created_date)='".$todayDate[1]."' and DAY(form_created_date)='".$todayDate[2]."'";
		$QUERY_CHECK=imw_query($qry_ch);
		if(imw_num_rows($QUERY_CHECK)>=1)
		{
			//get and send saved data
			$DATA=imw_fetch_object($QUERY_CHECK);
			if($typ=='sign')
			{
				$sigArr= unserialize(stripcslashes($DATA->patient_signature));
				$sigIdArr=explode('.',$sig_id);
				unset($sigArr[$sigIdArr[0]]);
				
				########## Temp fix to save sig #################### Remove that code after app next release (1.4)
				$temp_arr= unserialize(stripcslashes($DATA->witness_signature));
				$temp_arr[$target]=$replace;
				$temp_str=serialize($temp_arr);
				####################################################
				$consent_form_content=html_entity_decode($DATA->consent_form_content_data);
				$consent_form_content=str_replace($target,$replace,$consent_form_content);
				$consent_form_content=str_ireplace("border:#999 1px solid;","",$consent_form_content);
				$consent_form_content=htmlentities(imw_real_escape_string(trim($consent_form_content)));
				$sigStr=serialize($sigArr);
				
				imw_query("update patient_consent_form_information_app set consent_form_content_data='$consent_form_content',
							patient_signature='". imw_real_escape_string($sigStr) ."',
							witness_signature='". imw_real_escape_string($temp_str) ."',
                                                            modified_form_created_date=now()
						  	where form_information_id=$DATA->form_information_id")or die(imw_error());
			}
			elseif($typ=='content')
			{
				$temp_arr= unserialize(stripcslashes($DATA->witness_signature));
				foreach($temp_arr as $target=>$replace)
				{
					$content=str_replace($target,$replace,$content);	
				}
				//file_put_contents("text.txt","CONTENT<br><br>update patient_consent_form_information_app set consent_form_content_data='$consent_form_content' where form_information_id=$DATA->form_information_id",FILE_APPEND);
				$consent_form_content=htmlentities(imw_real_escape_string(trim($content)));
				imw_query("update patient_consent_form_information_app set consent_form_content_data='$consent_form_content' where form_information_id=$DATA->form_information_id")or die(imw_error());
			}
			elseif($typ=='save')
			{
				//remove javascript	 		
				$datatemp=stripcslashes(html_entity_decode(trim($DATA->consent_form_content_data)));
				//file_put_contents('test1.txt',$datatemp);
				
				$htmlArr=explode("<endofcode></endofcode>",$datatemp);	
				//remove smart tag links
				$htmlArr[0] = preg_replace('/<a id=\"(.*?)\" class=\"(.*?)\" href=\"(.*?)\">(.*?)<\/a>/', "\\4", $htmlArr[0]);
				$htmlArr[0] = preg_replace('/align=\"center\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">/', "align=\"left\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">", $htmlArr[0]);
				
				$htmlArr[0] = preg_replace('/<textarea class=\"manageUserInput\" rows=\"2\" cols=\"90\" name=\"(.*?)\" id=\"(.*?)\">/', "", $htmlArr[0]);
				$htmlArr[0] = preg_replace('/<\/textarea>/', "", $htmlArr[0]);
				//replace text boxes
				$htmlArr[0] = preg_replace('/<input class=\"manageUserInput\" type=\"text\" name=\"(.*?)\" id=\"(.*?)\" value=\"(.*?)\" size=\"(.*?)\" maxlength=\"(.*?)\" autocomplete=\"off\">/', "", $htmlArr[0]);
				//set display block for hidden user input values
				$htmlArr[0] = preg_replace('/<span id=\"(.*?)\" style=\"display:none\">(.*?)<\/span>/', "\\2", $htmlArr[0]);
				
				//remove signature links
				$htmlArr[0] = preg_replace('/<a name=\"typ_sig\" href=\"(.*?)\" id=\"(.*?)\">(.*?)<\/a>/', "\\3", $htmlArr[0]);
				
				//replace any unsigned image
				//$consentDATA=$this->removeTempImg($htmlArr[0],$DATA->patient_signature);
				//remove text input box
				$consentDATA=str_ireplace("height:20px; width:200px; min-width:200px; border:#999 1px solid;","",$consentDATA);
				//remove textarea box
				$consentDATA=str_ireplace("height:70px; width:300px; min-width:300px; border:#999 1px solid;","",$consentDATA);
				//remove signature border
				$consentDATA=str_ireplace('style="border:solid 1px;" bordercolor="#FF9900"',"",$consentDATA);
				$consentDATA=str_ireplace("HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;border:solid 1px; border-color:#FF9900;","",$consentDATA);
							
				$consentDATA=htmlentities(imw_real_escape_string(trim($consentDATA)));
				//file_put_contents('text.html', html_entity_decode($consentDATA).'--------------------------------------------------------------------------------\r\n\r\n', FILE_APPEND | LOCK_EX);
				$query="insert into patient_consent_form_information set consent_form_id='$DATA->consent_form_id',
							consent_form_name='".imw_real_escape_string($DATA->consent_form_name)."',
							patient_id='$DATA->patient_id',
							operator_id='$DATA->operator_id',
							form_created_date='".date('Y-m-d H:i:s')."',
							consent_form_content_data='$consentDATA'";
				//file_put_contents('test.txt',"\n 360 line \n".$query, FILE_APPEND);
				imw_query($query);
				$qtring="delete from patient_consent_form_information_app where form_information_id=$DATA->form_information_id";
				imw_query($qtring);
			}
		}
		else
		{
			//check in main consent form table
			
			$QUERY_CHECK_MAIN=imw_query("select consent_form_content_data,form_information_id from patient_consent_form_information where patient_id='$_REQUEST[pt_id]' and consent_form_id='$_REQUEST[form_id]' and YEAR(form_created_date)='".$todayDate[0]."' and MONTH(form_created_date)='".$todayDate[1]."' and DAY(form_created_date)='".$todayDate[2]."'");
			if(imw_num_rows($QUERY_CHECK_MAIN)>=1)
			{
				//get and send saved data
				$DATA_MAIN=imw_fetch_object($QUERY_CHECK_MAIN);
				if($typ=='sign')
				{
					$sigArr= unserialize(stripcslashes($DATA->patient_signature));
					$sigIdArr=explode('.',$sig_id);
					unset($sigArr[$sigIdArr[0]]);
					unset($sigArr[$sig_id]);
					$consent_form_content=$DATA_MAIN->consent_form_content_data;	 
					$consent_form_content=str_replace($target,$replace,$consent_form_content);
					$consent_form_content=imw_real_escape_string(trim($consent_form_content));
					$sigStr=serialize($sigArr);
					imw_query("update patient_consent_form_information_app set consent_form_content_data='$consent_form_content',
								patient_signature='". imw_real_escape_string($sigStr) ."'
								where form_information_id=$DATA_MAIN->form_information_id")or die(imw_error());
				}
				elseif($typ=='content')
				{
					$htmlArr=explode("<endofcode></endofcode>",$content);
					//remove smart tag links
					$htmlArr[0] = preg_replace('/<a id=\"(.*?)\" class=\"(.*?)\" href=\"(.*?)\">(.*?)<\/a>/', "\\4", $htmlArr[0]);
					$htmlArr[0] = preg_replace('/align=\"center\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">/', "align=\"left\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">", $htmlArr[0]);
					//remove signature links
					$htmlArr[0] = preg_replace('/<a name=\"typ_sig\" href=\"(.*?)\" id=\"(.*?)\">(.*?)<\/a>/', "\\3", $htmlArr[0]);
				
					$htmlArr[0] = preg_replace('/<textarea class=\"manageUserInput\" rows=\"2\" cols=\"90\" name=\"(.*?)\" id=\"(.*?)\">/', "", $htmlArr[0]);
					$htmlArr[0] = preg_replace('/<\/textarea>/', "", $htmlArr[0]);
					//replace text boxes
					$htmlArr[0] = preg_replace('/<input class=\"manageUserInput\" type=\"text\" name=\"(.*?)\" id=\"(.*?)\" value=\"(.*?)\" size=\"(.*?)\" maxlength=\"(.*?)\" autocomplete=\"off\">/', "", $htmlArr[0]);
					//set display block for hidden user input values
					$htmlArr[0] = preg_replace('/<span id=\"(.*?)\" style=\"display:none\">(.*?)<\/span>/', "\\2", $htmlArr[0]);
					
					
					$content=$htmlArr[0];
					$consent_form_content=htmlentities(imw_real_escape_string(trim($content)));//file_put_contents('test.txt',"\n 391 line \n".$consent_form_content, FILE_APPEND);
					imw_query("update patient_consent_form_information set consent_form_content_data='$consent_form_content' where form_information_id=$DATA_MAIN->form_information_id")or die(imw_error());
				}
				//NOTHING to do in save case because form is alredy saved
				
			}
		}
	}
	
	public function removeTempImg($dataContent,$sigStr)
	{
		//check do we have blank image in destination folder
		$sigArr=unserialize(stripcslashes($sigStr));
		$consent_form_content=$dataContent;
		if(sizeof($sigArr)>=1)//if we have unsigned images then move remaining file to final folder
		{
			global $webServerRootDirectoryName,$web_RootDirectoryName;	 
			

			foreach($sigArr as $key=>$val)
			{		
				//$replace=$webServerRootDirectoryName.$web_RootDirectoryName."/interface/common/new_html2pdf/app/".$key.".jpeg";
				//$target=$webServerRootDirectoryName.$web_RootDirectoryName."/app_services/signature/tmp/$_REQUEST[op_id]/".$key.".jpeg";
				//move files that are not signed
				//rename($target,$replace);
                                //$consent_form_content=str_replace($target,$replace,$consent_form_content);
				//$replace="interface/common/new_html2pdf/app/".$key.".jpeg";
				$replace = data_path(1)."PatientId_".$_REQUEST['pt_id']."/sign/".$key.".jpeg";
				$target= data_path(1)."app_services/signature/tmp/$_REQUEST[op_id]/admin_consent/".$key.".jpeg";
                                $consent_form_content=str_replace($target,$replace,$consent_form_content);
				$target2=data_path(1)."/interface/common/new_html2pdf/app/".$key.".jpeg";
				$consent_form_content=str_replace($target2,$replace,$consent_form_content);
				unset($sigArr[$key]);
			}
			//return $consent_form_content=htmlentities(addslashes(trim($consent_form_content)));
		}
		return $consent_form_content;
	}
	
	function get_smartTags_array($id=0){
		$query = "SELECT id, tagname FROM smart_tags WHERE under=".intval($id)." AND status=1";
		$result = imw_query($query);
		if($result && imw_num_rows($result)>0){
			$arrResult = array();
			while($rs = imw_fetch_assoc($result)){
				$id = $rs['id'];
				$tagname = $rs['tagname'];
				$arrResult[$id] = $tagname;
			}
			return $arrResult;		
		}else{
			return false;
		}
	}
	// get the physician list 
	 function get_physician_name_app(){
				
				$query = "select id,lname,mname,fname from users where delete_status != 1 order by lname ASC";
				$record = imw_query($query);
				$i=0;
				while($result = imw_fetch_assoc($record)){
					if($result['fname']!=""){
						$res[$i]["id"] = $result['id'];
						if($result['mname']!=""){
							$res[$i]["name_list"] = $result['lname']." ".$result['mname'].", ".$result['fname'];
						}
						else{
							$res[$i]["name_list"] = $result['lname'].", ".$result['fname'];
						}
						$i++;
					}
				}
					return $res;
		}
	
	 
}
?>