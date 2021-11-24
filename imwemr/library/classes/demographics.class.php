<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Patient Info/Demographics Data
 Access Type: Indirect Access.
 
*/
include_once 'class.language.php';
class Demographics extends core_lang
{
	private $comm_data = '';
	
	public function load_demographics_data($patient_id)
	{
		if(!$patient_id) return;
		$data	=	array();
		
		$data['patient_data']			=	self::load_patient_data($patient_id);
		$data['elig_data']				=	$this->load_emergency_last_check($patient_id);
		$data['last_appointment']	=	get_patient_last_appointment($patient_id);
		$data['heard_aboutus']		=	get_heard_about_list($data['patient_data']->ptHeardAbtUs);	
		$data['all_comm'] = get_array_records('patient_multi_address','patient_id',$patient_id,'*'," AND del_status = 0 AND id != '".$data['patient_data']->default_address."'");
		//$data['account_status']		=	$this->get_pat_all_next_action_status($patient_id);
		$data['multi_rp']					=	$this->get_referring_physician($patient_id,'1');
		$data['multi_pcp']				=	$this->get_referring_physician($patient_id,'3,4');
		$data['multi_cp']					=	$this->get_referring_physician($patient_id,'2');
		$data['heardAboutSearch'] = array('Family','Friends','Doctor','Previous Patient.','Previous Patient');
		
		return (object) $data;
	}
	
	public function load_defaults_data()
	{
		$defaults	=	array();
		$defaults['emerg_relats']		=	get_relationship_array('emergency_relation');
		$defaults['relations']			=	array('','self','spouse','child','POA','other');
		//$defaults['marital_status']	=	array('','divorced','domestic partner','married','single','separated','widowed');
		$defaults['marital_status']	=	marital_status();
		$defaults['mandatory_fld']	=	get_mandatory_fields('demographics');
		$defaults['vocabulary']			=	$this->get_vocabulary("patient_info", "demographics");
		$defaults['operator_name']	=	$this->get_operator_name();
		return $defaults;
	}
	
	
	/*
	* Function : load_patient_data 
	* Param : $patient_id
	*/
	private static function load_patient_data($patient_id)
	{
		if(!$patient_id)  return;
		
		$query	=	"SELECT pd.id as ptID,pd.assigned_nurse as assigned_nurse, pd.title as ptTitle, pd.fname as ptFname, pd.lname as ptLname, pd.mname as ptMname, pd.suffix as ptSuffix, pd.DOB as ptDOB, pd.street as ptStreet, pd.mname_br as ptBname,
							 pd.street2 as ptStreet2, pd.postal_code as ptPostalCode, pd.zip_ext as ptzip_ext, pd.city as ptCity, pd.state as ptState, pd.country_code as country_code, pd.ss as ptSS, pd.occupation as ptOccupation,pd.language as language, pd.lang_code as ptLangCode, 
							 pd.phone_home as ptPhoneHome, pd.phone_biz as ptPhoneBiz, pd.phone_biz_ext as ptPhoneBiz_ext, pd.phone_contact as ptPhoneContact, pd.phone_cell as ptPhoneCell, pd.status as ptMaritalStatus, pd.nick_name as ptNickName, pd.phonetic_name as ptPhoneticName,
							 pd.contact_relationship as ptContactRelationship, pd.date as ptRegDate, pd.sex as ptGender, pd.referrer as ptReferrer, pd.referrerID as ptReferrerID,
							 pd.providerID as ptProviderID, pd.email as ptEmail, pd.ethnoracial as ptEthnoracial,pd.interpreter_type as ptInterpreter_type, pd.interpretter as ptInterpretter, pd.monthly_income as ptMonthlyIncome,
							 pd.genericval1 as ptGenericVal1, pd.genericval2 as ptGenericVal2, pd.hipaa_mail as ptHipaaMail, pd.hipaa_email as ptHipaaEmail, pd.hipaa_voice as ptHipaaVoice,pd.hipaa_text as ptHipaaText,
							 pd.username as ptUserName, pd.password as ptPassword, pd.p_imagename as ptImageName, pd.driving_licence as ptDrivingLicence, pd.licence_photo as ptLicencePhoto,
							 pd.primary_care as ptReferringPhyName, pd.default_facility as ptDefaultFacility, pd.created_by as ptCreatedBy, pd.patient_notes as ptPatientNotes, 
						  	 pd.patientStatus as ptPatientStatus, pd.otherPatientStatus as ptOtherPatientStatus, pd.primary_care_id as ptReferringPhyID, pd.noBalanceBill as ptNoBalanceBill,
							 pd.EMR as ptEMR, pd.erx_entry as ptErxEntry, pd.ptInfoCollapseStatus as ptInfoCollapseStatus, pd.resPartyCollapseStatus as ptResPartyCollapseStatus,	
							 pd.ptOccCollapseStatus as ptOccCollapseStatus, pd.miscCollapseStatus as ptMiscCollapseStatus, pd.athenaID  as ptAthenaID, pd.External_MRN_1 as ptExternalID1, pd.External_MRN_2 as ptExternalID2, pd.heard_abt_us as ptHeardAbtUs, pd.heard_abt_desc as ptHeardAbtDesc,
							 pd.heard_abt_search as ptHeardAbtSearch, pd.heard_abt_search_id as ptHeardAbtSearchId,
							 pd.relInfoName1 as ptRelInfoName1, pd.relInfoPhone1 as ptRelInfoPhone1, pd.relInfoReletion1 as ptRelInfoReletion1, pd.otherRelInfoReletion1 as ptOtherRelInfoReletion1, 
							 pd.relInfoName2 as ptRelInfoName2, pd.relInfoPhone2 as ptRelInfoPhone2, pd.relInfoReletion2 as ptRelInfoReletion2, pd.otherRelInfoReletion2 as ptOtherRelInfoReletion2,	
							 pd.relInfoName3 as ptRelInfoName3, pd.relInfoPhone3 as ptRelInfoPhone3, pd.relInfoReletion3 as ptRelInfoReletion3, pd.otherRelInfoReletion3 as ptOtherRelInfoReletion3,						            
							 pd.relInfoName4 as ptRelInfoName4, pd.relInfoPhone4 as ptRelInfoPhone4, pd.relInfoReletion4 as ptRelInfoReletion4, pd.otherRelInfoReletion4 as ptOtherRelInfoReletion4,						            
							 pd.emergencyRelationship as ptEmergencyRelationship, pd.emergencyRelationship_other as ptEmergencyRelOther, pd.reportExemption as ptReportExemption, pd.race as ptRace,
							 pd.otherRace as ptOtherRace, pd.ethnicity as ptEthnicity, pd.otherEthnicity as ptOtherEthnicity, 
							 pd.sor_txt as ptSexualOrientation, pd.other_sor as ptOtherSOR,
							 pd.gi_txt as ptGenderIdentity, pd.other_gi as ptOtherGI,
							 pd.chk_mobile as ptChkMobile, pd.ado_option as ptAdoOption,
							 pd.desc_ado_other_txt as ptDescAdoOtherTxt, pd.chk_notes_scheduler as ptchkNotesScheduler, pd.chk_notes_chart_notes as ptChkNotesChartNotes, pd.chk_notes_accounting as ptChkNotesAccounting, pd.chk_notes_optical as ptChkNotesOptical,pd.locked,
							 pd.primary_care_phy_id as ptPriCarePhyId,pd.primary_care_phy_name as ptPriCarePhyName,date_format(dod_patient,'%m-%d-%Y') as dod_patient, pd.vip, pd.hold_statement, pd.preferr_contact, pd.co_man_phy, pd.co_man_phy_id,
							 pd.maiden_fname as 'ptMaidenFname', pd.maiden_mname as 'ptMaidenMname', pd.maiden_lname as 'ptMaidenLname', pd.temp_key as temp_key, pd.temp_key_expire as temp_key_expire,pd.default_address, pd.temp_key_chk_val, pd.pt_disable, pd.county as county, ";
if(constant("REMOTE_SYNC") == 1){
	$query			.= "pd.src_server,";
}
$query			.= " rp.id as rpPtID, rp.title as rpPtTitle, rp.fname as rpPtFname, rp.mname as rpPtMname, rp.lname as rpPtLname, rp.lname as rpPtLname, rp.suffix as rpPtSuffix, rp.dob as rpPtDOB, rp.sex as rpPtSex ,
							 rp.ss as rpPtSS, rp.ss as rpPtSS, rp.address as rpPtAdd, rp.city as rpPtCity, rp.state as rpPtState, rp.zip as rpPtZip, rp.zip_ext as rpzip_ext, rp.marital as rpPtMaritalStatus, rp.relation as rpPtRelation,
							 rp.other1 as rpPtOther1, rp.home_ph as rpPtHomePh, rp.work_ph as rpPtWorkPh, rp.mobile as rpPtMobilePh, rp.email as rpPtEmail, rp.licence as rpPtLicence, rp.licence_image as rpPtLicenceImage,
							 rp.address2 as rpPtAddress2, hippa_release_status as hippaRelSta, ed.id as edPtID, ed.name as edPtName, ed.street as edPtStreet, ed.postal_code as edPtPostalCode, ed.zip_ext as edPtzip_ext, ed.city as edPtCity, UPPER(ed.state) as edPtState, pd.view_portal, pd.update_portal,rp.erp_resp_username,rp.erp_resp_imw_password  							 
							FROM patient_data pd
							LEFT JOIN resp_party rp ON rp.patient_id = pd.id
							LEFT JOIN employer_data ed ON ed.pid = pd.id
							WHERE pd.id = '".$patient_id."'";
		$sql	= imw_query($query);
		$row	= imw_fetch_assoc($sql);	
		
		$row['patient_image']	=	self::load_patient_image($row['ptImageName'],true);
		$row['pt_license_image']=	self::load_patient_image($row['ptLicencePhoto']);
		$row['rp_license_image']=	($row['rpPtLicenceImage']) ? self::load_patient_image($row['rpPtLicenceImage']) : '';
		
		$row['country_code']	=	($row['country_code']) ? $row['country_code'] : 'USA';
		
		$tempD	=	get_extract_record('copay_policies','policies_id','1', 'Allow_erx_medicare,no_balance_bill');
		
		// Custom Fields
		$row['intNoBalanceBill']	=	$tempD['no_balance_bill']	?	$tempD['no_balance_bill'] : 0;
		$row['noBalanceBillTxt']	=	($row['intNoBalanceBill'] == 1) ? "No Balance Bill" : "&nbsp;" ;
		
		if( (!empty($row["ptExternalID1"]) || !empty($row["ptExternalID2"]) ) && constant("EXTERNAL_MRN_SEARCH") == "YES")
		{
			if(!empty($row["ptExternalID1"]))
				$secondary_id = $row["ptExternalID1"];
			elseif(!empty($row["ptExternalID2"]))
				$secondary_id = $row["ptExternalID2"];
		}
		else
		{
			$secondary_id = $row["ptAthenaID"];	
		}
		
		$row['secondary_id']					=	$secondary_id;
		$row['secondary_id_display']	=	($secondary_id > 0 ) ? 'block' : 'none';
		$row['heard_about_us_cols']		=	($secondary_id > 0 ) ? '27' : '33';
		$row['heard_about_us_width']	=	($secondary_id > 0 ) ? '280' : '320';
		$row['patient_mrn'] = self::patient_mrn($row["ptExternalID1"],$row["ptExternalID2"],$row["ptAthenaID"]);
		
		// Add data for hidden fields 
		$temp = get_extract_record('restricted_providers','patient_id',$patient_id,'restrict_providers');
		$row['zipCodeStatus']	=	(inter_country() == "UK")	?	'OK'	:	'';
		$row['curr_date']	=	date("m-d-Y");
		$row['seltab']		=	'defaultTab';
		$row['allow_erx_medicare']	=	$tempD['Allow_erx_medicare'] ? $tempD['Allow_erx_medicare'] : '';
		$row['erx_entry']	=	$row['ptErxEntry'];
		$row['preObjBack'] = '';
		$row['chkErxAsk']	=	0;
		$row['hidDemoChangeOption'] = 0;
		$row['hidden_providersToRestrictDemographics']	=	$temp['restrict_providers'];
		$row['restrict_providers'] = explode(',',$temp['restrict_providers']);	
		$row['hid_create_acc_resp_party']	=	'no';	
		
		return (object) $row;
			
	}
	
	
	/******
	* Function :	load_patient_image
	* Param : 
	*****/
	
	private static function load_patient_image($p_image, $default_image = false, $p_dir = '/data', $d_image = '/no_image_found.png', $d_dir = '/library/images')
	{
			$root_path	=	$GLOBALS['fileroot'];
			$web_path		=	$GLOBALS['webroot'];
			$practice		= '/'.PRACTICE_PATH.'/';
			
			$d_image		=	trim($d_dir).trim($d_image);
			$d_root			=	$root_path.$d_image;
			$d_web			=	$web_path.$d_image;
			
			$p_image		=	trim($p_image);
			$p_image		=	($p_image) ? $p_image : $d_image;
			$p_image		=	$p_dir.$practice.$p_image;
			$p_root			=	$root_path.$p_image;
			$p_web			=	$web_path.$p_image;
			
			$file_exists=	file_exists($p_root) ? true : false;
			if($file_exists) 
				return $p_web;
			
			if(!$file_exists && $default_image && file_exists($d_root))
				return $d_web;
			
			
			return;
		
	}
	
	private function load_emergency_last_check($patient_id)
	{
			$return['count'] = 0;
			if($patient_id)
			{
				$query	= "SELECT rtme.id as RTMEId, DATE_FORMAT(rtme.request_date_time, '".get_sql_date_format()."') as elDate,
												DATE_FORMAT(rtme.request_date_time, '%h:%i %p') as elTime, 
												DATE_FORMAT(rtme.responce_date_time , '".get_sql_date_format()."') as elDEC, 
												rtme.EB_responce as elResponce, rtme.transection_error as tranError,
												CONCAT_WS(', ',SUBSTRING(us.lname,1,1),SUBSTRING(us.fname,1,1)) as elOpName
												FROM real_time_medicare_eligibility rtme
												LEFT JOIN users us ON us.id = rtme.request_operator
												WHERE rtme.patient_id = '".$patient_id."' 
												AND rtme.del_status = '0' 
												ORDER BY request_date_time DESC
												LIMIT 1";
				$sql	 = imw_query($query);
				$count = imw_num_rows($sql);
				
				if($sql)
				{
					if($count > 0)
					{
						$row = imw_fetch_array($sql);
						$db_elig_id		= $row["RTMEId"];
						$db_elig_date = $row["elDate"];
						$db_elig_time	= $row["elTime"];										
						$db_elig_dec	= $row["elDEC"];										
						$db_elig_resp	= $row["elResponce"];
						$strEBResponse= $this->get_vocabulary("vision_share_271", "EB", (string)$db_elig_resp);
						$db_elig_op		= $row["elOpName"];
						$db_tran_err 	= $row["tranError"];
						
						if($db_tran_err != "")
						{
							$db_elig_resp = "Error";
							$attr_title	 	= $db_tran_err;
							$attr_class		= 'text-red';
						}
						else
						{
							if(($db_elig_resp == "6") || ($db_elig_resp == "7") || ($db_elig_resp == "8") || ($db_elig_resp == "V"))
							{
								$db_elig_resp = $strEBResponse;
								$attr_title		=	$db_elig_resp;
								$attr_class		= 'text-red';
							}
							else
							{
								$attr_class = "text-green";
								$db_elig_resp = (is_string($strEBResponse) ? $strEBResponse : "View Detail");
								if(strlen($db_elig_resp) > 18){
									$attr_title		= "title='".$db_elig_resp."'";
									$db_elig_resp = substr($db_elig_resp,0, 15)."...";
								}
							}
						}
						
						$return['elig_count']	=	$count;
						$return['elig_id']		=	$db_elig_id;
						$return['elig_date']	=	$db_elig_date;
						$return['elig_time']	=	$db_elig_time;
						$return['elig_dec']		=	$db_elig_dec;
						$return['elig_resp']	=	$db_elig_resp;
						$return['elig_op']		=	$db_elig_op;
						$return['attr_title']	=	$attr_title;
						$return['attr_class']	=	$attr_class;
						
					}
					imw_free_result($sql);
				}
			}
			
			return (object)$return;
		
	}
	
	public function time_numbers($end=12, $selected='')
	{
		$opts='';
		$timeSlot = 1;
		if($end==59){ $timeSlot=5;}
		for($i=0; $i<=$end;)
		{
			if($i < 10){ $i='0'.$i; }
			$sel = ($i == $selected) ? 'selected' : '';
			$opts.='<option value="'.$i.'" '.$sel.'>'.$i.'</option>';
			$i += $timeSlot;
		}
		return $opts;
	}
	
	private function get_operator_name()
	{
		$operator_name = '';
		if(isset($_SESSION['authProviderName']) && $_SESSION['authProviderName'] != "")
		{
			$arrOpName = explode(",",$_SESSION['authProviderName']);
			$operator_name = strtoupper(substr(trim($arrOpName[1]),0,1)).strtoupper(substr(trim($arrOpName[0]),0,1));
		}
		
		return $operator_name;
	}
		
	public function get_pat_all_next_action_status($p_id = 0)
	{
		$arrPatAction = array();
		if($p_id != 0){
			$sql="Select patHis.user_id, patHis.old_account_sts, patHis.new_account_sts, patHis.action_code, DATE_FORMAT(patHis.change_date, '".get_sql_date_format()."') as change_date, nextAction.action_status,
			users.fname,users.lname  
			FROM patient_next_action_history patHis LEFT JOIN patient_next_action nextAction ON 
			nextAction.id=patHis.action_code 
			LEFT JOIN users ON users.id=patHis.user_id
			WHERE patHis.patient_id='".$p_id."' ORDER BY patHis.id DESC,patHis.change_date DESC, patHis.change_time DESC";
			$rs = imw_query($sql);
			$divRows.='<table class="table table-bordered table-hover table-striped scroll release-table"><thead class="header"><tr class="grythead"><th>Date</th><th>Description</th><th>Operator</th></tr></thead><tbody>';
			while($res=imw_fetch_array($rs)){
				$show_operatorName_mod = substr($res['fname'],0,1).substr($res['lname'],0,1);
				
				if($res['action_code']>0){
					$divRows.='<tr>
									 <td style="background-color:#E8F9F9">'.$res['change_date'].'</td>
									 <td style="background-color:#E8F9F9">'.$res['action_status'].'</td>
									 <td style="background-color:#E8F9F9;">'.$show_operatorName_mod.'</td>
								 </tr>';
				}			
		}
		
		if(imw_num_rows($rs)<=0){
			$divRows.='<tr">
				<td colspan="3" height="30" class="bg-default">No Record Found.</td>
		   </tr>';
		}
		$divRows.='</tbody></table>';
	}
	return $divRows;				
}

	public function get_referring_physician($patient_id, $phy_type = '' )
	{
		$id_array = $name_array = $type_array = $status_array = $address_array = array();

		$fields = "ref_phy_id, phy_type, TRIM(CONCAT(refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName, ' ',refPhy.Title)) as refName, refPhy.delete_status, refPhy.Address1, refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.physician_phone, refPhy.physician_fax, 
		refPhy.physician_email,refPhy.comments, refPhy.PractiseName";
		
		$query = "SELECT ".$fields." FROM patient_multi_ref_phy pmrf INNER JOIN refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id WHERE pmrf.patient_id = '".$patient_id."' AND  pmrf.status = '0' " .($phy_type ? " AND phy_type IN (".$phy_type.")" : '' );
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		
		while($row = imw_fetch_assoc($sql))
		{
			array_push($id_array,$row['ref_phy_id']);
			array_push($type_array,$row['phy_type']);
			array_push($name_array,$row['refName']);
			array_push($status_array,(int)$row['delete_status']);
			array_push($address_array,format_ref_data($row));
		}
		
		$return = array('id' => $id_array, 'name' => $name_array, 'type' => $type_array, 'status' => $status_array, 'address' => $address_array);
		return (object) $return;
	}

	public function race_modal($common_only = 0)
	{
		$data = array();
		$qry = "select * From race Where ".($common_only ? "common_use = 1 " : "h_code <> '' ")."Order By if(h_code = '' or h_code is null,1,0),h_code";
		
		if( $qry)
		{
			$sql = imw_query($qry);
			$cnt = imw_num_rows($sql);
			if( $cnt > 0 )
			{
				while( $row = imw_fetch_assoc($sql) )
				{
					if( $common_only ) 
						$data[] = $row['race_name'];
					else
						$data[$row['parent_id']][$row['race_id']] = $row['race_name'];
				}
				
			}
			
			if( $common_only ) return $data;
			
			
			$this->comm_data = $data;
			return $this->print_html($data[0],0,'race',0);
		
		}
	}
	
	public function ethnicity_modal($common_only = 0)
	{
		$data = array();
		$qry = "select * From ethnicity Where ".($common_only ? "common_use = 1 " : "h_code <> '' ")."Order By if(h_code = '' or h_code is null,1,0),h_code";
		if( $qry)
		{
			$sql = imw_query($qry);
			$cnt = imw_num_rows($sql);
			if( $cnt > 0 ){
				while( $row = imw_fetch_assoc($sql) ){
					if( $common_only ) 
						$data[] = $row['ethnicity_name'];
					else
						$data[$row['parent_id']][$row['ethnicity_id']] = $row['ethnicity_name'];
				}
			}
			
			if( $common_only ) return $data;
			
			$this->comm_data = $data;
			return $this->print_html($data[0],0,'ethnicity',0);			
		}
	}
	
	public function language_modal($common_only = 0)
	{
		$data = array();
		$qry = "select lang_name, if(iso_639_1_code = '', iso_639_2_B_code, iso_639_1_code) as lang_code  From languages 	".($common_only ? "Where common_use = 1 " : "")."Order By lang_name='Other' Asc, lang_name='Declined to Specify' Asc, lang_name ASc ";
		if( $qry)
		{
			$sql = imw_query($qry);
			$cnt = imw_num_rows($sql);
			if( $cnt > 0 ){
				while( $row = imw_fetch_assoc($sql) ){
					$data[$row['lang_code']] = $row['lang_name'];
				}
			}
			
			if( $common_only ) return $data;
			
			$this->comm_data = $data;
			return $this->print_html($data,0,'language',0);			
		}
	}

	public function interpreter_modal($common_only = 0)
	{
		$interpreter_arr=["Sign Language","Oral","Qued Speech","Tactile","Accompanying Spouse","Accompanying Child"];
		$data = array();
		foreach($interpreter_arr as $val){
			$data[$val] = $val;
		}
		if( $common_only ) return $data;			
		$this->comm_data = $data;
		return $this->print_html($data,0,'interpreter_type',0);
	}
	
	private function print_html($data,$group_id,$type,$level){
		$html = '';
		if( is_array( $data ) && count( $data ) > 0 )
		{
			$onClick = "addExtra(this);" ;
			if( $type == 'language') $onClick = "addLanguage(this);" ;
			if( $type == 'interpreter_type') $onClick = "addInterpreter(this);" ;
		
			$levelArr = array('nav__list','group','subbox sub-group','subbox sub-sub-group','subbox sub-sub-sub-group','subbox sub-sub-sub-sub-group');	
			if( $level == 0) $html .= '<nav class="nav" role="navigation">';
			
			$ulClass = ($level > 0) ? $levelArr[$level] .'-list' : $levelArr[$level];
			
			$html .= '<ul class="'.$ulClass.' '.((($type=='language' || $type=='interpreter_type') && $level == 0) ? 'langbox' : ''  ).'">';
			$counter = 0;
			foreach($data as $p_id => $name)
			{
				$counter++;
				if( is_array( $this->comm_data[$p_id] ) && count($this->comm_data[$p_id]) > 1  )
				{
					//$chk_name_id = 'chk_'.$type.'_'.$group_id.'_'.$p_id;
					$html .= '<li>';
					$tmp_id = trim(str_replace("subbox","",$levelArr[$level+1]));
					if( $level > 0 )
					{
						$html .= '<div class="firstcheck">';
						$html .= '<div class="checkbox margin_0">';
						$html .= '<input id="1_'.$tmp_id.'-'.$type.'-'.$group_id.'_'.$counter.'" name="'.$tmp_id.'-'.$type.'-'.$group_id.'_'.$counter.'" type="checkbox" value="'.$name.'" data-object-id="'.$type.'" onChange="'.$onClick.'" />';
						$html .= '<label for="1_'.$tmp_id.'-'.$type.'-'.$group_id.'_'.$counter.'"></label>';
						$html .= '</div>';
						$html .= '</div>';
					}
					
					$html .= '<input id="'.$tmp_id.'-'.$type.'-'.$group_id.'_'.$counter.'" type="checkbox" hidden />';
					$html .= '<label for="'.$tmp_id.'-'.$type.'-'.$group_id.'_'.$counter.'"><span class="glyphicon glyphicon-chevron-right"></span> '.$name.'</label>';
					$html .= $this->print_html($this->comm_data[$p_id],$p_id,$type, ($level+1));
					$html .= '</li>';
					
				}
				else
				{
					$chk_name_id = 'chk_'.$type.'_'.$group_id.'_'.$p_id;
					$html .= '<div '.($levelArr[$level] == 'group' ? 'class="subbox listalg"' : '' ).' '.(($level == 0 && $type == 'ethnicity') ? 'class="ethnt"' : '' ).'>';
					$html .= '<div id="header_'.$p_id.'">';
					$html .= '<div class="checkbox margin_0">';
					$html .= '<input type="checkbox" name="'.$chk_name_id.'" id="'.$chk_name_id.'" value="'.$name.'" data-object-id="'.$type.'" data-code-name="'.$p_id.'" onChange="'.$onClick.'" />';
					$html .= '<label for="'.$chk_name_id.'">'.$name.'</label>';
					$html .= '</div>';
					$html .= '</div>';
					$html .= '</div>';
				}
			}
		
			$html .= '</ul>';
			if( $level == 0) $html .= '</nav>';
		}
		return $html;
		
	}
	
	private static function patient_mrn($ext_1,$ext_2,$athena_id){
		$patient_MRN="";	
		if(((empty($ext_1) == false) || (empty($ext_2) == false)) && (constant("EXTERNAL_MRN_SEARCH") == "YES")){
			
			if(DISP_EXTERNAL_MRN || DISP_EXTERNAL_MRN=='1'){ 
				if(empty($ext_1) == false){
					if(strlen($ext_1) == 6){
						$patient_MRN=$ext_1;	
					}
					else{
						$patient_MRN=$ext_1;
					}
				}
				elseif(empty($ext_2) == false){
					if(strlen($ext_2) == 6){
						$patient_MRN="0".$ext_2;	
					}
					else{
						$patient_MRN=$ext_2;
					}
				}
			}
			
			//====Show ExternalMRN2 value of first preference ===============// 
									
			if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('marr','mage')) || constant('DISP_EXTERNAL_MRN')=='2') {
				if(empty($ext_2) == false){
					if(strlen($ext_2) == 6){
						$patient_MRN="0".$ext_2;	
					}
					else{
						$patient_MRN=$ext_2;
					}
				}
				elseif(empty($ext_1) == false){
					if(strlen($ext_1) == 6){
						$patient_MRN="0".$ext_1;	
					}
					else{
						$patient_MRN=$ext_1;
					}
				}	
			}
		}
		else { $patient_MRN=$athena_id; }
		
		return $patient_MRN;
		
	}
	
	public function patient_audit(){
		$opreaterId = $_SESSION['authId'];			
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];													 
		//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);	
		global $rowGetPatientData,$patRes,$resultr,$result2,$patientDataFields,$respPartyDataFields,$empDataFields,$restrictedProvidersDataFields,$pid,$restricted_Row,$forSelTypeAhed;
		$arrAuditTrail = array();
		//pre($forSelTypeAhed);
		$arrAuditTrail [] =
			array(
					"Pk_Id"=> ($restricted_Row["restrict_id"]) ? $restricted_Row["restrict_id"] : "",
					"Table_Name"=>"restricted_providers",
					"Data_Base_Field_Name"=> "restrict_providers",
					"Filed_Label"=> "providersToRestrictDemographics",
					"Filed_Text"=> "Restrict Access",
					"Data_Base_Field_Type"=> fun_get_field_type($restrictedProvidersDataFields,"restrict_providers") ,
					"Action"=> "update",
					"Operater_Id"=> $opreaterId,
					"Operater_Type"=> getOperaterType($opreaterId) ,
					"IP"=> $ip,
					"MAC_Address"=> $_REQUEST['macaddrs'],
					"URL"=> $URL,
					"Browser_Type"=> $browserName,
					"OS"=> $os,
					"Machine_Name"=> $machineName,
					"Category"=> "Restricted_Providers",
					"Category_Desc"=> "Restricted Provider",					
					"Depend_Select"=> "select CONCAT_WS(', ',lname,fname) as Restricted" ,
					"Depend_Table"=> "users" ,
					"Depend_Search"=> "id" ,
					"Old_Value"=> addcslashes(addslashes($restricted_Row["restrict_providers"]),"\0..\37!@\177..\377")																							
				);
		
		$arrAuditTrail [] = 
						array(
								"Pk_Id"=> $rowGetPatientData["ptID"],
								"Table_Name"=>"patient_data",
								"Data_Base_Field_Name"=> "reportExemption" ,
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"reportExemption") ,
								"Filed_Label"=> "reportExemption",
								"Filed_Text"=> "Exempt from Reports",
								"Action"=> "update",
								"Operater_Id"=> $opreaterId,
								"Operater_Type"=> getOperaterType($opreaterId) ,
								"IP"=> $ip,
								"MAC_Address"=> $_REQUEST['macaddrs'],
								"URL"=> $URL,
								"Browser_Type"=> $browserName,
								"OS"=> $os,
								"Machine_Name"=> $machineName,
								"Category"=> "patient_info",
								"Category_Desc"=> "demographics",	
								"Old_Value"=> ($rowGetPatientData["ptReportExemption"]) ? $rowGetPatientData["ptReportExemption"] : ""																																										
							);																		
							if(empty($forSelTypeAhed) == true){
								$pt_heard_val="";
							}else{								
								$pt_heard_val = $forSelTypeAhed;
							}
		$arrAuditTrail [] = 
						array(
								"Data_Base_Field_Name"=> "patientStatus" ,
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"patientStatus") ,
								"Filed_Label"=> "elem_patientStatus",
								"Filed_Text"=> "Patient Status",
								"Old_Value"=> ($rowGetPatientData["ptPatientStatus"]) ? $rowGetPatientData["ptPatientStatus"] : ""

							);
		$arrAuditTrail [] = 
						array(
								"Data_Base_Field_Name"=> "dod_patient" ,
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"dod_patient") ,
								"Filed_Label"=> "dod_patient",
								"Filed_Text"=> "Patient DOD",
								"Old_Value"=> ($rowGetPatientData["dod_patient"]) ? $rowGetPatientData["dod_patient"] : ""

							);
		
		$arrAuditTrail [] = 
						array(																								
								"Data_Base_Field_Name"=> "otherPatientStatus" ,
								"Filed_Text"=> "Status Text",
								"Filed_Label"=> "otherPatientStatus",
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherPatientStatus") ,
								"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptOtherPatientStatus']),"\0..\37!@\177..\377")
								
							);			
		$arrAuditTrail [] = 
						array(																								
								"Data_Base_Field_Name"=> "pat_account_status" ,
								"Filed_Text"=> "Pt. AS",
								"Filed_Label"=> "account_status",
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"pat_account_status") ,
								"Old_Value"=> addcslashes(addslashes($patRes['pat_account_status']),"\0..\37!@\177..\377"),
								"Depend_Select"=> "select status_name as account_status" ,
								"Depend_Table"=> "account_status" ,
								"Depend_Search"=> "id" 
							);			
		$arrAuditTrail [] = 
						array(																								
								"Data_Base_Field_Name"=> "vip" ,
								"Filed_Text"=> "Patient VIP",
								"Filed_Label"=> "vip",
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"vip") ,
								"Old_Value"=> addcslashes(addslashes($rowGetPatientData['vip']),"\0..\37!@\177..\377"),
							);			
		$arrAuditTrail [] = 
						array(																								
								"Data_Base_Field_Name"=> "hold_statement" ,
								"Filed_Text"=> "Patient Hold Statement",
								"Filed_Label"=> "h_statement",
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"hold_statement") ,
								"Old_Value"=> addcslashes(addslashes($rowGetPatientData['hold_statement']),"\0..\37!@\177..\377"),
							);			
		
		$arrAuditTrail [] = 
						array(																								
								"Data_Base_Field_Name"=> "heard_abt_us" ,
								"Filed_Label"=> "elem_heardAbtUs",
								"Filed_Text"=> "Heard about us",
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"heard_abt_us") ,
								"Old_Value"=> addcslashes(addslashes($pt_heard_val),"\0..\37!@\177..\377")
								
							);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "heard_abt_desc" ,
							"Filed_Label"=> "heardAbtDesc",
							"Filed_Text"=> "Heard about us Description",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"heard_abt_desc") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptHeardAbtDesc']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
					array(
							"Pk_Id"=> $rowGetPatientData["ptID"],
							"Table_Name"=>"patient_data",
							"Data_Base_Field_Name"=> "title" ,
							"Filed_Label"=> "title",
							"Filed_Text"=> "Patient Title",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"title") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptTitle']),"\0..\37!@\177..\377"),
							"New_Value"=> ""																																																																						
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "fname" ,
							"Filed_Label"=> "fname",
							"Filed_Text"=> "Patient First Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"fname") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptFname']),"\0..\37!@\177..\377")
							
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "mname" ,
							"Filed_Label"=> "mname",
							"Filed_Text"=> "Patient Middle Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"mname") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptMname']),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "lname" ,
							"Filed_Label"=> "lname",
							"Filed_Text"=> "Patient Last Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"lname") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptLname']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "suffix" ,
							"Filed_Label"=> "suffix",
							"Filed_Text"=> "Patient Suffix",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"suffix") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptSuffix']),"\0..\37!@\177..\377")
							
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "status" ,
							"Filed_Label"=> "status",
							"Filed_Text"=> "Patient Marital Status",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"status") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptMaritalStatus']),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "sex" ,
							"Filed_Label"=> "sex",
							"Filed_Text"=> "Patient Gender",
							"Data_Base_Field_Type"=>  fun_get_field_type($patientDataFields,"sex") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptGender']),"\0..\37!@\177..\377")
							
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "ss" ,
							"Filed_Label"=> "ss",
							"Filed_Text"=> "Patient Social security",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"ss") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptSS']),"\0..\37!@\177..\377")
							
						);			
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "DOB" ,
							"Filed_Label"=> "dob",
							"Filed_Text"=> "Patient DOB",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"DOB") ,
							"Old_Value"=> (addcslashes(addslashes($rowGetPatientData['ptDOB']),"\0..\37!@\177..\377")!="0000-00-00") ? addcslashes(addslashes($rowGetPatientData['ptDOB']),"\0..\37!@\177..\377") : ""
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "maiden_fname" ,
							"Filed_Label"=> "maiden_fname",
							"Filed_Text"=> "Patient Mother Maiden F.Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"maiden_fname") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptMaidenFname']),"\0..\37!@\177..\377")
							
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "maiden_mname" ,
							"Filed_Label"=> "maiden_mname",
							"Filed_Text"=> "Patient Mother Maiden M.Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"maiden_mname") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptMaidenMname']),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "maiden_lname" ,
							"Filed_Label"=> "maiden_lname",
							"Filed_Text"=> "Patient Mother Maiden L.Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"maiden_lname") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptMaidenLname']),"\0..\37!@\177..\377")
							
						);		
		 
		 $arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "street" ,
							"Filed_Label"=> "street",
							"Filed_Text"=> "Patient Address 1",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"street") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptStreet']),"\0..\37!@\177..\377")																								
						);			
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "street2" ,
							"Filed_Label"=> "street2",
							"Filed_Text"=> "Patient Address 2",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"street2") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptStreet2']),"\0..\37!@\177..\377")
							
						);		
		$zipPostal = (($GLOBALS['phone_country_code'] == '1') ? 'Zip Code' : 'Postal Code');
		$zipPostalExt = (($GLOBALS['phone_country_code'] == '1') ? 'Zip Ext' : 'Postal Ext');
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "postal_code" ,
							"Filed_Label"=> "postal_code",							
							"Filed_Text"=> "Patient ".$zipPostal,
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"postal_code") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptPostalCode']),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "zip_ext" ,
							"Filed_Label"=> "zip_ext",							
							"Filed_Text"=> "Patient ".$zipPostalExt,
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"zip_ext") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptzip_ext']),"\0..\37!@\177..\377")																								
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "city" ,
							"Filed_Label"=> "city",
							"Filed_Text"=> "Patient City",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"city") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptCity']),"\0..\37!@\177..\377")																								
						);	
		$stateLocality = ($GLOBALS['phone_country_code'] == '1') ? 'State' : 'Locality';				
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "state" ,
							"Filed_Label"=> "state",							
							"Filed_Text"=> "Patient ".$stateLocality,
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"state") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptState']),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "phone_home" ,
							"Filed_Label"=> "phone_home",
							"Filed_Text"=> "Patient Home Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"phone_home") ,
							"Old_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData['ptPhoneHome'])),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "phone_biz" ,
							"Filed_Label"=> "phone_biz",
							"Filed_Text"=> "Patient Work Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"phone_biz") ,
							"Old_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData['ptPhoneBiz'])),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "phone_cell" ,
							"Filed_Label"=> "phone_cell",
							"Filed_Text"=> "Patient Mobile Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"phone_cell") ,
							"Old_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData['ptPhoneCell'])),"\0..\37!@\177..\377")
							
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "email" ,
							"Filed_Label"=> "email",
							"Filed_Text"=> "Patient Email-Id",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"email") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptEmail']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "chk_mobile" ,
							"Filed_Label"=> "chk_mobile",
							"Filed_Text"=> "Checkbox email Mobile",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"chk_mobile") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptChkMobile']),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "contact_relationship" ,
							"Filed_Label"=> "contact_relationship",
							"Filed_Text"=> "Patient Emergency Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"contact_relationship") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptContactRelationship']),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "emergencyRelationship" ,
							"Filed_Label"=> "emerRelation",
							"Filed_Text"=> "Patient Relationship",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"emergencyRelationship") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptEmergencyRelationship']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
				array(																								
						"Data_Base_Field_Name"=> "emergencyRelationship_other" ,
						"Filed_Label"=> "relation_other_textbox",
						"Filed_Text"=> "Patient Relationship Other",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"emergencyRelationship_other") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptEmergencyRelOther']),"\0..\37!@\177..\377")
					);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "phone_contact" ,
							"Filed_Label"=> "phone_contact",
							"Filed_Text"=> "Patient Emergency Tel#",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"phone_contact") ,
							"Old_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData['ptPhoneContact'])),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "providerID" ,
							"Filed_Label"=> "providerID",
							"Filed_Text"=> "Patient Primary Physician",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"providerID") ,
							"Depend_Select"=> "select CONCAT_WS(',',fname,lname) as provider" ,
							"Depend_Table"=> "users" ,
							"Depend_Search"=> "id" ,
							"Old_Value"=> (addcslashes(addslashes($rowGetPatientData['ptProviderID']),"\0..\37!@\177..\377")) ? addcslashes(addslashes($rowGetPatientData['ptProviderID']),"\0..\37!@\177..\377") : ""
							
						);
		$arrAuditTrail [] = 
						array(																								
							"Data_Base_Field_Name"=> "ptPriCarePhyId" ,
							"Filed_Label"=> "pCarePhy",
							"Filed_Text"=> "Patient Primary Care Physician",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"ptPriCarePhyId") ,
							"Depend_Select"=> "select CONCAT_WS(',',fname,lname) as provider" ,
							"Depend_Table"=> "users" ,
							"Depend_Search"=> "id" ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptPriCarePhyId']),"\0..\37!@\177..\377")							
						);
		$arrAuditTrail [] = 
						array(																								
							"Data_Base_Field_Name"=> "primary_care_id" ,
							"Filed_Label"=> "pcare",
							"Filed_Text"=> "Patient Referring Physician",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"primary_care_id") ,
							"Depend_Select"=> "select CONCAT_WS(',',FirstName,LastName) as refPhy" ,
							"Depend_Table"=> "refferphysician" ,
							"Depend_Search"=> "physician_Reffer_id" ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptReferringPhyID']),"\0..\37!@\177..\377")							
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "default_facility" ,
							"Filed_Label"=> "default_facility",
							"Filed_Text"=> "Patient Facility",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"default_facility") ,
							"Depend_Select"=> "SELECT facility_name as facilityName" ,
							"Depend_Table"=> "pos_facilityies_tbl" ,
							"Depend_Search"=> "pos_facility_id" ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptDefaultFacility']),"\0..\37!@\177..\377")							
						);
		$arrAuditTrail [] = 
						array(																								
							"Data_Base_Field_Name"=> "co_man_phy_id" ,
							"Filed_Label"=> "co_man_phy_id",
							"Filed_Text"=> "Patient Co-Managed Physician",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"co_man_phy_id") ,
							"Depend_Select"=> "select CONCAT_WS(', ',LastName,FirstName) as coManPhy" ,
							"Depend_Table"=> "refferphysician" ,
							"Depend_Search"=> "physician_Reffer_id" ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['co_man_phy_id']),"\0..\37!@\177..\377")							
						);
		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "username" ,
							"Filed_Label"=> "usernm",
							"Filed_Text"=> "Patient Login-Id",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"username") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptUserName']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "password" ,
							"Filed_Label"=> "pass1",
							"Filed_Text"=> "Patient Password",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"password") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptPassword']),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "password" ,
							"Filed_Label"=> "pass2",
							"Filed_Text"=> "Patient Confirm Password",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"password") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptPassword']),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "patient_notes" ,
							"Filed_Label"=> "patient_notes",
							"Filed_Text"=> "Patient Notes",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"patient_notes") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptPatientNotes']),"\0..\37!@\177..\377")
						);
					
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "chk_notes_scheduler" ,
							"Filed_Label"=> "chkNotesScheduler",
							"Filed_Text"=> "Patient Checkbox Scheduler",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"chk_notes_scheduler") ,
							"Old_Value"=> ($rowGetPatientData["ptchkNotesScheduler"]) ? $rowGetPatientData["ptchkNotesScheduler"] : "0"							
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "chk_notes_chart_notes" ,
							"Filed_Label"=> "chkNotesChartNotes",
							"Filed_Text"=> "Patient Checkbox Chart Notes",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"chk_notes_chart_notes") ,							
							"Old_Value"=> ($rowGetPatientData["ptChkNotesChartNotes"]) ? $rowGetPatientData["ptChkNotesChartNotes"] : "0"
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "chk_notes_accounting" ,
							"Filed_Label"=> "chkNotesAccounting",
							"Filed_Text"=> "Patient Checkbox Accounting",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"chk_notes_accounting") ,
							"Old_Value"=> ($rowGetPatientData["ptChkNotesAccounting"]) ? $rowGetPatientData["ptChkNotesAccounting"] : "0"							
						);			
					
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "noBalanceBill" ,
							"Filed_Label"=> "noBalBill",
							"Filed_Text"=> "Patient No Balance Bill",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"noBalanceBill") ,
							"Old_Value"=> ($rowGetPatientData["ptNoBalanceBill"]) ? $rowGetPatientData["ptNoBalanceBill"] : ""
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "EMR" ,
							"Filed_Label"=> "emr",
							"Filed_Text"=> "Patient Emr",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"EMR") ,
							"Old_Value"=> ($rowGetPatientData['ptEMR']) ? $rowGetPatientData["ptEMR"] : ""
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "created_by" ,
							"Filed_Label"=> "created_by",
							"Filed_Text"=> "Patient Created By",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"created_by") ,
							"Depend_Select"=> "select CONCAT_WS(',',fname,lname) as createdBy" ,
							"Depend_Table"=> "users" ,
							"Depend_Search"=> "id" ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData['ptCreatedBy']),"\0..\37!@\177..\377")
							
						);				
		$regDate = $rowGetPatientData['ptRegDate'];
		$arrRegDate = explode(" ",$regDate);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "date" ,
							"Filed_Label"=> "reg_date",
							"Filed_Text"=> "Patient Registration Date",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"date") ,
							"Old_Value"=> addcslashes(addslashes($arrRegDate[0]),"\0..\37!@\177..\377")																								
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "driving_licence",
							"Filed_Label"=> "dlicence",
							"Filed_Text"=> "Patient Driving License",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"driving_licence") ,
							"Old_Value"=> $rowGetPatientData["ptDrivingLicence"]
						);
		$arrAuditTrail [] = 
					array(							
							"Pk_Id"=> $rowGetPatientData["rpPtID"],
							"Table_Name"=>"resp_party",																	
							"Data_Base_Field_Name"=> "title",
							"Filed_Label"=> "title1",
							"Filed_Text"=> "Patient Responsible Party Title",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"title") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtTitle"]),"\0..\37!@\177..\377")																													
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "fname",
							"Filed_Label"=> "fname1",
							"Filed_Text"=> "Patient Responsible Party First Name",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"fname") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtFname"]),"\0..\37!@\177..\377")
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "mname",
							"Filed_Label"=> "mname1",
							"Filed_Text"=> "Patient Responsible Party Middle Name",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"mname") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtMname"]),"\0..\37!@\177..\377")																													
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "lname",
							"Filed_Label"=> "lname1",
							"Filed_Text"=> "Patient Responsible Party Last Name",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"lname") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtLname"]),"\0..\37!@\177..\377")																													
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "suffix",
							"Filed_Label"=> "suffix1",
							"Filed_Text"=> "Patient Responsible Party Suffix",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"suffix") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtSuffix"]),"\0..\37!@\177..\377")																													
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "relation",
							"Filed_Label"=> "relation1",
							"Filed_Text"=> "Patient Responsible Party Relation",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"relation") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtRelation"]),"\0..\37!@\177..\377")
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "marital",
							"Filed_Label"=> "status1",
							"Filed_Text"=> "Patient Responsible Party Marital Status",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"marital") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtMaritalStatus"]),"\0..\37!@\177..\377")																												
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "hippa_release_status",
							"Filed_Label"=> "chkHippaRelResp",
							"Filed_Text"=> "Patient Responsible Party Relation HIPAA",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"hippa_release_status") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["hippaRelSta"]),"\0..\37!@\177..\377")
						);				
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "dob",
							"Filed_Label"=> "dob1",
							"Filed_Text"=> "Patient Responsible Party DOB",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"dob") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtDOB"]),"\0..\37!@\177..\377")																												
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "sex",
							"Filed_Label"=> "sex1",
							"Filed_Text"=> "Patient Responsible Party Gender",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"sex") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtSex"]),"\0..\37!@\177..\377")																												
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "address",
							"Filed_Label"=> "street1",
							"Filed_Text"=> "Patient Responsible Party Address 1",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"address") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtAdd"]),"\0..\37!@\177..\377")
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "address2",
							"Filed_Label"=> "street_emp",
							"Filed_Text"=> "Patient Responsible Party Address 2",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"address2") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtAddress2"]),"\0..\37!@\177..\377")																											
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "zip",
							"Filed_Label"=> "postal_code1",							
							"Filed_Text"=> "Patient Responsible Party ".$zipPostal,
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"zip") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtZip"]),"\0..\37!@\177..\377")
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "zip_ext",
							"Filed_Label"=> "rzip_ext",							
							"Filed_Text"=> "Patient Responsible Party ".$zipPostalExt,
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"zip_ext") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpzip_ext"]),"\0..\37!@\177..\377")
						);
		
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "city",
							"Filed_Label"=> "city1",
							"Filed_Text"=> "Patient Responsible Party City",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"city") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtCity"]),"\0..\37!@\177..\377")																											
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "state",
							"Filed_Label"=> "state2",							
							"Filed_Text"=> "Patient Responsible Party ".$stateLocality,
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"state") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtState"]),"\0..\37!@\177..\377")
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "ss",
							"Filed_Label"=> "ss1",
							"Filed_Text"=> "Patient Responsible Party S.S",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"ss") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtSS"]),"\0..\37!@\177..\377")																													
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "home_ph",
							"Filed_Label"=> "phone_home1",
							"Filed_Text"=> "Patient Responsible Party Home Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"home_ph") ,
							"Old_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData["rpPtHomePh"])),"\0..\37!@\177..\377")
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "work_ph",
							"Filed_Label"=> "phone_biz1",
							"Filed_Text"=> "Patient Responsible Party Work Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"work_ph") ,
							"Old_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData["rpPtWorkPh"])),"\0..\37!@\177..\377")																													
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "mobile",
							"Filed_Label"=> "phone_cell1",
							"Filed_Text"=> "Patient Responsible Party Mobile Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"mobile") ,
							"Old_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData["rpPtMobilePh"])),"\0..\37!@\177..\377")																													
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "email",
							"Filed_Label"=> "email1",
							"Filed_Text"=> "Patient Responsible Party Email",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"email") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["rpPtEmail"]),"\0..\37!@\177..\377")
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "licence",
							"Filed_Label"=> "dlicence1",
							"Filed_Text"=> "Patient Responsible Party Driving License ",
							"Data_Base_Field_Type"=> fun_get_field_type($respPartyDataFields,"licence") ,
							"Old_Value"=> $rowGetPatientData["rpPtLicence"]
						);
		// Advanced Directive
		$arrAuditTrail [] =
				array(
						
						"Data_Base_Field_Name"=> "ado_option",
						"Filed_Label"=> "ado_option",
						"Filed_Text"=> "Patient Advanced Directive Option",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"ado_option") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptAdoOption"]),"\0..\37!@\177..\377")
					);
		$arrAuditTrail [] =
				array(
						
						"Data_Base_Field_Name"=> "desc_ado_other_txt",
						"Filed_Label"=> "ado_other_txt",
						"Filed_Text"=> "Patient Advanced Directive Other Text Box",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"desc_ado_other_txt") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptDescAdoOtherTxt"]),"\0..\37!@\177..\377")																							
					);
		// ----- Advanced Directive
		$arrAuditTrail [] =
			array(
					"Pk_Id"=> ($restricted_Row["restrict_id"]) ? $restricted_Row["restrict_id"] : "",
					"Table_Name"=>"restricted_providers",
					"Data_Base_Field_Name"=> "restrict_providers",
					"Filed_Label"=> "providersToRestrictDemographics",
					"Filed_Text"=> "Demographics Restrict Access",
					"Data_Base_Field_Type"=> fun_get_field_type($restrictedProvidersDataFields,"restrict_providers") ,
					"Depend_Select"=> "select CONCAT_WS(', ',lname,fname) as Restricted" ,
					"Depend_Table"=> "users" ,
					"Depend_Search"=> "id" ,
					"Old_Value"=> addcslashes(addslashes($restricted_Row["restrict_providers"]),"\0..\37!@\177..\377")																							
				);
		// ----- Restrict Access
		$arrAuditTrail [] = 																							
					array(
							"Pk_Id"=> $rowGetPatientData["edPtID"],
							"Table_Name"=>"employer_data",
							"Data_Base_Field_Name"=> "name",
							"Filed_Label"=> "ename",
							"Filed_Text"=> "Patient Occupation Employer",
							"Data_Base_Field_Type"=> fun_get_field_type($empDataFields,"name") ,
							"Old_Value"=> addcslashes(addslashes($rowGetPatientData["edPtName"]),"\0..\37!@\177..\377")																													
						);	
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "occupation",
						"Filed_Label"=> "occupation",
						"Filed_Text"=> "Patient Occupation",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"occupation") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptOccupation"]),"\0..\37!@\177..\377")																													
					);

		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"employer_data",
						"Data_Base_Field_Name"=> "street",
						"Filed_Label"=> "estreet",
						"Filed_Text"=> "Patient Occupation Address",
						"Data_Base_Field_Type"=> fun_get_field_type($empDataFields,"street") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["edPtStreet"]),"\0..\37!@\177..\377")																													
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"employer_data",
						"Data_Base_Field_Name"=> "postal_code",
						"Filed_Label"=> "epostal_code",
						"Filed_Text"=> "Patient Occupation Zip Code",
						"Data_Base_Field_Type"=> fun_get_field_type($empDataFields,"postal_code") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["edPtPostalCode"]),"\0..\37!@\177..\377")
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"employer_data",
						"Data_Base_Field_Name"=> "zip_ext",
						"Filed_Label"=> "ezip_ext",
						"Filed_Text"=> "Patient Occupation Zip Ext",
						"Data_Base_Field_Type"=> fun_get_field_type($empDataFields,"zip_ext") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["edPtzip_ext"]),"\0..\37!@\177..\377")
					);
		
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"employer_data",
						"Data_Base_Field_Name"=> "city",
						"Filed_Label"=> "ecity",
						"Filed_Text"=> "Patient Occupation City",
						"Data_Base_Field_Type"=> fun_get_field_type($empDataFields,"city") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["edPtCity"]),"\0..\37!@\177..\377")
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"employer_data",
						"Data_Base_Field_Name"=> "state",
						"Filed_Label"=> "estate",
						"Filed_Text"=> "Patient Occupation State",
						"Data_Base_Field_Type"=> fun_get_field_type($empDataFields,"state") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["edPtState"]),"\0..\37!@\177..\377")																													
					);
		$arrAuditTrail [] = 																							
				array(
						"Pk_Id"=> $rowGetPatientData["ptID"],
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "hipaa_mail",
						"Filed_Label"=> "hipaa_mail",
						"Filed_Text"=> "Patient Allow postal Mail",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"hipaa_mail") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptHipaaMail"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "hipaa_email",
						"Filed_Label"=> "hipaa_email",
						"Filed_Text"=> "Patient Allow eMail",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"hipaa_email") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptHipaaEmail"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "hipaa_voice",
						"Filed_Label"=> "hipaa_voice",
						"Filed_Text"=> "Patient Voice Msg",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"hipaa_voice") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptHipaaVoice"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "genericval1",
						"Filed_Label"=> "genericval1",
						"Filed_Text"=> "Patient Miscellaneous User Defined 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"genericval1") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptGenericVal1"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "genericval2",
						"Filed_Label"=> "genericval2",
						"Filed_Text"=> "Patient Miscellaneous User Defined 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"genericval2") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptGenericVal2"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Pk_Id"=> $rowGetPatientData["ptID"],
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoName1",
						"Filed_Label"=> "relInfoName1",
						"Filed_Text"=> "Patient Release Information Name 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoName1") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoName1"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoPhone1",
						"Filed_Label"=> "relInfoPhone1",
						"Filed_Text"=> "Patient Release Information Phone# 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoPhone1") ,
						"Old_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData["ptRelInfoPhone1"])),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoReletion1",
						"Filed_Label"=> "relInfoReletion1",
						"Filed_Text"=> "Patient Release Information Relationship 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoReletion1") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoReletion1"]),"\0..\37!@\177..\377")
						
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRelInfoReletion1",
						"Filed_Label"=> "otherRelInfoReletion1",
						"Filed_Text"=> "Patient Release Information Relationship Other 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRelInfoReletion1") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptOtherRelInfoReletion1"]),"\0..\37!@\177..\377")																							
					);	
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoName2",
						"Filed_Label"=> "relInfoName2",
						"Filed_Text"=> "Patient Release Information Name 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoName2") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoName2"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoPhone2",
						"Filed_Label"=> "relInfoPhone2",
						"Filed_Text"=> "Patient Release Information Phone# 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoPhone2") ,
						"Old_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData["ptRelInfoPhone2"])),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoReletion2",
						"Filed_Label"=> "relInfoReletion2",
						"Filed_Text"=> "Patient Release Information Relationship 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoReletion2") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoReletion2"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRelInfoReletion2",
						"Filed_Label"=> "otherRelInfoReletion2",
						"Filed_Text"=> "Patient Release Information Relationship Other 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRelInfoReletion2") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptOtherRelInfoReletion2"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoName3",
						"Filed_Label"=> "relInfoName3",
						"Filed_Text"=> "Patient Release Information Name 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoName3") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoName3"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoPhone3",
						"Filed_Label"=> "relInfoPhone3",
						"Filed_Text"=> "Patient Release Information Phone# 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoPhone3") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoPhone3"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoReletion3",
						"Filed_Label"=> "relInfoReletion3",
						"Filed_Text"=> "Patient Release Information Relationship 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoReletion3") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoReletion3"]),"\0..\37!@\177..\377")
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRelInfoReletion3",
						"Filed_Label"=> "otherRelInfoReletion3",
						"Filed_Text"=> "Patient Release Information Relationship Other 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRelInfoReletion3") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptOtherRelInfoReletion3"]),"\0..\37!@\177..\377")																							
					);			
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoName4",
						"Filed_Label"=> "relInfoName4",
						"Filed_Text"=> "Patient Release Information Name 4",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoName4") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoName4"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoPhone4",
						"Filed_Label"=> "relInfoPhone4",
						"Filed_Text"=> "Patient Release Information Phone# 4",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoPhone4") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoPhone4"]),"\0..\37!@\177..\377")
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoReletion4",
						"Filed_Label"=> "relInfoReletion4",
						"Filed_Text"=> "Patient Release Information Relationship 4",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoReletion4") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRelInfoReletion4"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRelInfoReletion4",
						"Filed_Label"=> "otherRelInfoReletion4",
						"Filed_Text"=> "Patient Release Information Relationship Other 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRelInfoReletion4") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptOtherRelInfoReletion4"]),"\0..\37!@\177..\377")																							
					);

		
		$language_val = $rowGetPatientData["language"];
		$other_language=substr($rowGetPatientData["language"],0,5);
		if($other_language=='Other'){
			$language_val=substr($rowGetPatientData["language"],9);
		}
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "language",
						"Filed_Label"=> "language",
						"Filed_Text"=> "Patient Language",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"language") ,
						"Old_Value"=> addcslashes(addslashes($language_val),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "language",
						"Filed_Label"=> "otherLanguage",
						"Filed_Text"=> "Patient Other Language",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"language") ,
						"Old_Value"=> addcslashes(addslashes($language_val),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "interpretter",
						"Filed_Label"=> "interpretter",
						"Filed_Text"=> "Patient Interpreter",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"interpretter") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptInterpretter"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "race",
						"Filed_Label"=> "race",
						"Filed_Text"=> "Patient Race",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"race") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptRace"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRace",
						"Filed_Label"=> "otherRace",
						"Filed_Text"=> "Patient Other race",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRace") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptOtherRace"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "ethnicity",
						"Filed_Label"=> "ethnicity",
						"Filed_Text"=> "Patient Ethnicity",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"ethnicity") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptEthnicity"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] =
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherEthnicity",
						"Filed_Label"=> "otherEthnicity",
						"Filed_Text"=> "Patient Other Ethnicity",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherEthnicity") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["ptOtherEthnicity"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] =
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "view_portal",
						"Filed_Label"=> "view_portal",
						"Filed_Text"=> "Patient Access View",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"view_portal") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["view_portal"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] =
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "update_portal",
						"Filed_Label"=> "update_portal",
						"Filed_Text"=> "Patient Access Update",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"update_portal") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["update_portal"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] =
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "locked",
						"Filed_Label"=> "lockPatient",
						"Filed_Text"=> "Patient Access Lock",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"locked") ,
						"Old_Value"=> addcslashes(addslashes($rowGetPatientData["locked"]),"\0..\37!@\177..\377")																							
					);
		
		return $arrAuditTrail;										
	}
	
}


?>