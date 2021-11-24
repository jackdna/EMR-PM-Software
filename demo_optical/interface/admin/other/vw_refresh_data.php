<?php 
	set_time_limit(0);
	ini_set('max_execution_time', 0);
	ini_set("memory_limit","4096M");
	$include_base = "";$log_msg=true;
	if($argv[1]=="GetTrackingHistory"){
		$ignoreAuth = true;
		$_REQUEST['method'] = "GetTrackingHistory";
		$include_base = dirname(__FILE__)."/";
	}
	require_once($include_base."../../../config/config.php");
	require_once($include_base."../../../library/classes/functions.php");
	function writeMysqlLog($error)
	{
		file_put_contents('../../patient_interface/uploaddir/vwSyncMysqlLog.txt',$error."\n", FILE_APPEND);
	}
	function writeProcessLog($error, $fresh=false)
	{
		if($fresh==true)
		file_put_contents('../../patient_interface/uploaddir/vwSyncLog'.$_SESSION['authId'].'.txt',$error."\n");
		else
		file_put_contents('../../patient_interface/uploaddir/vwSyncLog'.$_SESSION['authId'].'.txt',$error."\n", FILE_APPEND);
	}
	$operator_id = $_SESSION['authId'];
	$entered_date = date("Y-m-d");
	$entered_time = date("h:i:s");
	$entered_date_time=date("Y-m-d H:i:s");
	$order_id=$_REQUEST['order_id'];
	$order_detail_ids=$_REQUEST['order_detail_ids'];
	$patient_id=$_REQUEST['patient_id'];
	$vw_loc_id=$_REQUEST['vw_loc_id'];
	
	$prod_user="IMEDICWAREPROD";
	$prod_pass="vision";
	$prod_sloid="9901";
	$prod_refid="ROIMEDICWARE";
	
	$vw_qry=imw_query("select * from in_vision_web where vw_loc_id>0")or writeMysqlLog(imw_error().' 32');
	while($vw_row=imw_fetch_array($vw_qry)){
		$vw_id_arr[$vw_row['vw_loc_id']]  = $vw_row['id'];
		$username_arr[$vw_row['vw_loc_id']]  = $vw_row['vw_user'];
		$password_arr[$vw_row['vw_loc_id']]  = $vw_row['vw_pass'];
		$refid_arr[$vw_row['vw_loc_id']] = $vw_row['vw_ref_id'];
		$vw_submitted_id_arr[$vw_row['vw_loc_id']] = $vw_row['vw_submitted_id'];
	}
	if($_REQUEST['method']=="getUserProfileByLogin") {
		
		writeProcessLog("Checking Connection..", true);

		$sql = "SELECT 
					GROUP_CONCAT(`vw`.`id`) AS 'id', 
					`vw`.`vw_user`, 
					`vw`.`vw_pass`,
					GROUP_CONCAT(`vw`.`vw_loc_id`) AS 'loc_id'
				FROM 
					`in_vision_web` `vw` 
					INNER JOIN `in_location` `loc` ON(`vw`.`vw_loc_id` = `loc`.`id`) 
				WHERE 
					`vw`.`vw_loc_id` > 0 
					AND `vw`.`vw_user` != '' 
					AND `vw`.`vw_pass` != '' 
					AND `loc`.`del_status` = 0
				GROUP BY `vw`.`vw_user`, `vw`.`vw_pass`
				ORDER BY `vw`.`id` ASC";

		$vw_qry         = imw_query( $sql )or writeMysqlLog(imw_error().' 62');
		$sup_loc_id_arr = array();    /*Container for Supplier Locations*/
		while ( $vw_row = imw_fetch_assoc( $vw_qry ) ) {
			writeProcessLog("Initalizing data for user $vw_row[vw_user]");
			$params['username'] = $vw_row['vw_user'];
			$params['password'] = $vw_row['vw_pass'];
			$params['refid']    = $prod_refid;
			$vw_user_ids        = explode( ',', $vw_row['id'] ); /*IMW VW Location Ids*/

			$soapClient  = new SoapClient( "https://www.visionweb.com/services/services/UserAccountsService/wsdl/UserAccountsService.wsdl" );
			
			try {
				$sendRq  = $soapClient->getUserProfileByLogin( $params );
			}
			catch (SoapFault $soapFault) {
				writeProcessLog("Initalization failed");
				var_dump($soapFault);
				echo "Request :<br>", htmlentities($soapClient->__getLastRequest()), "<br>";
				echo "Response :<br>", htmlentities($soapClient->__getLastResponse()), "<br>";
				die('Call Failed');
			}
			
			
			$sendRq_data = $sendRq->getUserProfileByLoginReturn;
			$xmlparser = xml_parser_create();
			xml_parse_into_struct( $xmlparser, $sendRq_data, $vw_data );
			xml_parser_free( $xmlparser );

			foreach ( $vw_data as $vw_data_key => $vw_data_arr ) {

				/*Loop for Duplication VW Credentials*/
				foreach ( $vw_user_ids as $vw_user_id ) {
					if ( $vw_data_arr['tag'] == "SUPPLIER_LOCATION" ) {
						$sup_loc_name = $sup_loc_id = $sup_billing_number = $sup_shipping_number = $lab_id = $bill_ac_id = $ship_ac_id = "";
						if ( array_key_exists( 'attributes', $vw_data_arr ) && array_key_exists( 'NAME', $vw_data_arr['attributes'] ) ) {
							$sup_loc_name = trim( $vw_data_arr['attributes']['NAME'] );
						}
						if ( $sup_loc_name != "" ) {
							$sup_loc_id = trim( $vw_data_arr['attributes']['ID'] );
							if ( !array_key_exists( $vw_row['id'], $sup_loc_id_arr ) ) {
								$sup_loc_id_arr[$vw_row['id']] = array( 'locIds' => $vw_row['loc_id'], 'suppliers' => array() );
							}
							$sup_loc_id_arr[$vw_row['id']]['suppliers'][$sup_loc_id] = $sup_loc_name;


							$lab_qry = imw_query( "select id from in_lens_lab where vw_lab_id='$sup_loc_id'" )or writeMysqlLog(imw_error().' 107');
							if ( imw_num_rows( $lab_qry ) > 0 ) {
								$lab_fet = imw_fetch_array( $lab_qry );
								$lab_id  = $lab_fet['id'];
							}
							else {
								imw_query( "insert into in_lens_lab set vw_lab_id='$sup_loc_id',lab_name='$sup_loc_name',entered_date='$entered_date',entered_time='$entered_time',entered_by='$operator_id'" )or writeMysqlLog(imw_error().' 112');
								$lab_id = imw_insert_id();
							}
							$lab_user_qry = imw_query( "select * from in_vw_user_lab where vw_user_id='$vw_user_id' AND lab_id='$lab_id'" )or writeMysqlLog(imw_error().' 115');
							if ( imw_num_rows( $lab_user_qry ) == 0 ) {
								imw_query( "insert into in_vw_user_lab set vw_user_id='$vw_user_id',lab_id='$lab_id'" )or writeMysqlLog(imw_error().' 119');
							}
							imw_query( "update in_vision_web set last_import='$entered_date_time' where id='$vw_user_id'" );
						}
					}
					if ( isset( $sup_loc_name ) && $sup_loc_name != "" ) {
						if ( $vw_data_arr['tag'] == "BILLING_ACCOUNT" && array_key_exists( 'attributes', $vw_data_arr ) && array_key_exists( 'NUMBER', $vw_data_arr['attributes'] ) ) {
							$sup_billing_number = trim( $vw_data_arr['attributes']['NUMBER'] );
							$bill_ac_id = trim( $vw_data_arr['attributes']['ID'] );
							$sup_shipping_number=0;
						}
						if ( $sup_billing_number != "" ) {
							if ( $vw_data_arr['tag'] == "ADDRESS" ) {
								$sup_billing_city          = $vw_data_arr['attributes']['CITY'];
								$sup_billing_country       = $vw_data_arr['attributes']['COUNTRY'];
								$sup_billing_ext           = $vw_data_arr['attributes']['EXTENSION'];
								$sup_billing_state         = $vw_data_arr['attributes']['STATE'];
								$sup_billing_street_name   = $vw_data_arr['attributes']['STREET_NAME'];
								$sup_billing_street_number = $vw_data_arr['attributes']['STREET_NUMBER'];
								$sup_billing_tel           = $vw_data_arr['attributes']['TEL'];
								$sup_billing_zip_code      = $vw_data_arr['attributes']['ZIPCODE'];
								
								$where='';
								if($bill_ac_id)$where=" and (ac_id=$bill_ac_id OR ac_id=0)";
								$lab_det_qry = imw_query( "select id from in_lens_lab_detail where vw_billing_number='$sup_billing_number' AND vw_user_id='$vw_user_id' $where" );
								if ( imw_num_rows( $lab_det_qry ) == 0 ) {
									imw_query( "insert into in_lens_lab_detail set city='$sup_billing_city',country='$sup_billing_country',extension='$sup_billing_ext',
									state='$sup_billing_state',street_name='$sup_billing_street_name',street_number='$sup_billing_street_number',
									telephone='$sup_billing_tel', zip_code='$sup_billing_zip_code', vw_billing_number='$sup_billing_number', lab_id='$lab_id',
									entered_date='$entered_date', entered_time='$entered_time', entered_by='$operator_id', vw_user_id='$vw_user_id', ac_id=$bill_ac_id" );
								}
								elseif ( imw_num_rows( $lab_det_qry ) == 1 ) {
									$lab_det_id = imw_fetch_assoc( $lab_det_qry );
									imw_query( "UPDATE in_lens_lab_detail set vw_user_id ='" . $vw_user_id . "', city='$sup_billing_city', country='$sup_billing_country', extension='$sup_billing_ext',
									state='$sup_billing_state', street_name='$sup_billing_street_name', street_number='$sup_billing_street_number',
									telephone='$sup_billing_tel', zip_code='$sup_billing_zip_code', ac_id=$bill_ac_id WHERE id='" . $lab_det_id['id'] . "'" );
								}
							}
						}
						if ( $vw_data_arr['tag'] == "SHIPPING_ACCOUNT" && array_key_exists( 'attributes', $vw_data_arr ) && array_key_exists( 'NUMBER', $vw_data_arr['attributes'] ) ) {
							$sup_shipping_number = trim( $vw_data_arr['attributes']['NUMBER'] );
							$ship_ac_id = trim( $vw_data_arr['attributes']['ID'] );
							$sup_billing_number=0;
						}
						if ( $sup_shipping_number != "" ) {
							if ( $vw_data_arr['tag'] == "ADDRESS" ) {
								$sup_shipping_city          = $vw_data_arr['attributes']['CITY'];
								$sup_shipping_country       = $vw_data_arr['attributes']['COUNTRY'];
								$sup_shipping_ext           = $vw_data_arr['attributes']['EXTENSION'];
								$sup_shipping_state         = $vw_data_arr['attributes']['STATE'];
								$sup_shipping_street_name   = $vw_data_arr['attributes']['STREET_NAME'];
								$sup_shipping_street_number = $vw_data_arr['attributes']['STREET_NUMBER'];
								$sup_shipping_tel           = $vw_data_arr['attributes']['TEL'];
								$sup_shipping_zip_code      = $vw_data_arr['attributes']['ZIPCODE'];
								$where='';
								if($ship_ac_id)$where=" and (ac_id=$ship_ac_id OR ac_id=0)";
								$lab_det_qry = imw_query( "select id from in_lens_lab_detail where vw_shipping_number='$sup_shipping_number' AND vw_user_id='$vw_user_id' $where" )or writeMysqlLog(imw_error().' 170');
								if ( imw_num_rows( $lab_det_qry ) == 0 ) {
									imw_query( "insert into in_lens_lab_detail set city='$sup_shipping_city',country='$sup_shipping_country',extension='$sup_shipping_ext',
									state='$sup_shipping_state',street_name='$sup_shipping_street_name',street_number='$sup_shipping_street_number',
									telephone='$sup_shipping_tel',zip_code='$sup_shipping_zip_code',vw_shipping_number='$sup_shipping_number',lab_id='$lab_id',
									entered_date='$entered_date',entered_time='$entered_time',entered_by='$operator_id',vw_user_id='$vw_user_id', ac_id=$ship_ac_id" )or writeMysqlLog(imw_error().' 174');
								}
								elseif ( imw_num_rows( $lab_det_qry ) == 1 ) {
									$lab_det_id = imw_fetch_assoc( $lab_det_qry );
									imw_query( "UPDATE in_lens_lab_detail set vw_user_id ='" . $vw_user_id . "', city='$sup_shipping_city',country='$sup_shipping_country',extension='$sup_shipping_ext',
									state='$sup_shipping_state',street_name='$sup_shipping_street_name',street_number='$sup_shipping_street_number',
									telephone='$sup_shipping_tel',zip_code='$sup_shipping_zip_code', ac_id=$ship_ac_id WHERE id='" . $lab_det_id['id'] . "'" )or writeMysqlLog(imw_error().' 179');
								}
							}
						}
					}
				}
			}
		}

		/*$sup_loc_id_arr_new = array();
		/*Filer Supplier Locations for Unique Value* /
		reset( $sup_loc_id_arr );
		while ( $val = current( $sup_loc_id_arr ) ) {
			$key = key( $sup_loc_id_arr );
			//$key = preg_replace('/([09]*)(?:,.*)/', '$1', $key);
			$val['suppliers'] = array_unique( $val['suppliers'] );

			$sup_loc_id_arr_new[$key] = $val;
			next( $sup_loc_id_arr );
		}/**/
		$log_msg=false;
		$msg = json_encode($sup_loc_id_arr);
	}
	elseif($_REQUEST['method']=="getCatalogBySupplier"){
		
		$vw_loc_id = (int)trim($_REQUEST['location']);
		$vw_supplier = trim($_REQUEST['supplier']);
		writeProcessLog(" Sync begun for $vw_supplier - $_REQUEST[locName] ".date('d M Y H:i:s'));
		
		$imwLOcId = array_search($vw_loc_id, $vw_id_arr);

		if( $vw_loc_id <= 0 || $vw_supplier <= 0 || $imwLOcId === false)
		{
			$err_msg='Unable to retrieve Vision Web configurations for the location.';
			writeProcessLog($err_msg);
			exit($err_msg);
		}

		$params = array();
		$params['username'] = $username_arr[$imwLOcId];
		$params['password'] = $password_arr[$imwLOcId];
		$params['refid']    = $prod_refid;
		$params['sloid']    = $vw_supplier;
		$params['type']     = 'SUP';

		try
		{
			$soapClient = new SoapClient("https://services.visionweb.com/VWCatalog.asmx?WSDL");
			$sendRq = $soapClient->GetStandardCatalogByLoginSupplier($params);
			$sendRq_data = $sendRq->GetStandardCatalogByLoginSupplierResult;

			$xmlparser = xml_parser_create();
			xml_parse_into_struct($xmlparser,$sendRq_data,$vw_data);
			xml_parser_free($xmlparser);

			unset($sendRq_data, $xmlparser);
			foreach($vw_data as $vw_data_key=>$vw_data_arr){
				if($vw_data[$vw_data_key]['tag']=="LENS_TYPE"){
					if($vw_data[$vw_data_key]['attributes']!=""){
						$type_desc=trim($vw_data[$vw_data_key]['attributes']['DESCRIPTION']);
						$type_code=trim($vw_data[$vw_data_key]['attributes']['CODE']);
						$type_name_like="";
						if($type_code=="TFF"){
							$type_name_like="trifocal";
						}else if($type_code=="SV"){
							$type_name_like="vision";
						}else if($type_code=="PAL"){
							$type_name_like="progressive";
						}else if($type_code=="BFF"){
							$type_name_like="bifocal";
						}
						imw_query("update in_lens_type set vw_code='$type_code' where type_name like '%$type_name_like%'")or writeMysqlLog(imw_error().' 247');
					}
				}
				if($vw_data[$vw_data_key]['tag']=="DESIGN"){
					if( array_key_exists('attributes', $vw_data[$vw_data_key]) && $vw_data[$vw_data_key]['attributes']!="" ){
						$parameter_id=trim($vw_data[$vw_data_key]['attributes']['PARAMETERID']);
						$type_desc=trim($vw_data[$vw_data_key]['attributes']['DESCRIPTION']);
						$type_code=trim($vw_data[$vw_data_key]['attributes']['VWCODE']);
						$lens_type=trim($vw_data[$vw_data_key+1]['attributes']['CODE']);
						$qry=imw_query("select id from in_lens_design where design_name='".imw_real_escape_string($type_desc)."' and del_status!='2'")or writeMysqlLog(imw_error().' 256');
						if( $qry && imw_num_rows($qry)>0){
							$row=imw_fetch_array($qry);
							$design_id=$row['id'];
							imw_query("update in_lens_design set vw_code='$type_code',parameter_id='$parameter_id',lens_vw_code='$lens_type' where id='$design_id'");
						}else{
							$qry=imw_query("select id from in_lens_design where vw_code='$type_code' and del_status!='2'")or writeMysqlLog(imw_error().' 262');
							if(imw_num_rows($qry)==0){
								imw_query("insert into in_lens_design set design_name='".imw_real_escape_string($type_desc)."',vw_code='$type_code',parameter_id='$parameter_id',lens_vw_code='$lens_type'")or writeMysqlLog(imw_error().' 265');
								$design_id=imw_insert_id();
							}
						}
					}
				}
				if($vw_data[$vw_data_key]['tag']=="MATERIAL"){
					if($vw_data[$vw_data_key]['attributes']!=""){
						$type_desc=trim($vw_data[$vw_data_key]['attributes']['DESCRIPTION']);
						$type_code=trim($vw_data[$vw_data_key]['attributes']['VWCODE']);
						$qry=imw_query("select id from in_lens_material where material_name='".imw_real_escape_string($type_desc)."' and del_status!='2'")or writeMysqlLog(imw_error().' 275');
						if(imw_num_rows($qry)>0){
							$row=imw_fetch_array($qry);
							$material_id=$row['id'];
							imw_query("update in_lens_material set vw_code='$type_code' where id='$material_id'")or writeMysqlLog(imw_error().' 278');
						}else{
							$qry=imw_query("select id from in_lens_material where vw_code='$type_code' and del_status!='2'");
							if(imw_num_rows($qry)==0){
								imw_query("insert into in_lens_material set material_name='".imw_real_escape_string($type_desc)."',vw_code='$type_code'")or writeMysqlLog(imw_error().' 281');
								$material_id=imw_insert_id();
							}
						}
					}
				}

				if($vw_data[$vw_data_key]['tag']=="FRAME"){
					if($vw_data[$vw_data_key]['attributes']!=""){
						$type_desc=trim($vw_data[$vw_data_key]['attributes']['DESCRIPTION']);
						$type_code=trim($vw_data[$vw_data_key]['attributes']['VWCODE']);
						$qry=imw_query("select id from in_frame_types where type_name='".imw_real_escape_string($type_desc)."' and del_status!='2'")or writeMysqlLog(imw_error().' 293');
						if(imw_num_rows($qry)>0){
							$row=imw_fetch_array($qry);
							$f_type_id=$row['id'];
							imw_query("update in_frame_types set vw_code='$type_code' where id='$f_type_id'")or writeMysqlLog(imw_error().' 297');
						}else{
							imw_query("insert into in_frame_types set type_name='".imw_real_escape_string($type_desc)."',vw_code='$type_code'")or writeMysqlLog(imw_error().' 300');
							$f_type_id=imw_insert_id();
						}
					}
				}
			}

			foreach($vw_data as $vw_data_key=>$vw_data_arr){

				if($vw_data[$vw_data_key]['tag']=="LENS"){
					if( array_key_exists('attributes', $vw_data[$vw_data_key]) && $vw_data[$vw_data_key]['attributes']!=""){
						$material_code=trim($vw_data[$vw_data_key]['attributes']['MATERIALCODE']);
						$design_code=trim($vw_data[$vw_data_key]['attributes']['DESIGNCODE']);
						$design_desc=trim($vw_data[$vw_data_key]['attributes']['DESCRIPTION']);

						$met_qry=imw_query("select id from in_lens_material where vw_code='$material_code' and del_status!='2'")or writeMysqlLog(imw_error().' 314');
						if(imw_num_rows($met_qry)>0){
							$met_row=imw_fetch_array($met_qry);
							$met_id_ins=$met_row['id'];
						}else{
							$met_ins=imw_query("insert into in_lens_material set material_name='$material_code',vw_code='$material_code'")or writeMysqlLog(imw_error().' 319');
							$met_id_ins=imw_insert_id();
						}

						$des_qry=imw_query("select id from in_lens_design where vw_code='$design_code' and del_status!='2'")or writeMysqlLog(imw_error().' 323');
						if(imw_num_rows($des_qry)>0){
							$des_row=imw_fetch_array($des_qry);
							$des_id_ins=$des_row['id'];
						}else{
							$des_ins=imw_query("insert into in_lens_design set design_name='".imw_real_escape_string($design_desc)."',vw_code='$design_code'")or writeMysqlLog(imw_error().' 328');
							$des_id_ins=imw_insert_id();
						}

						$qry=imw_query("select material_id from in_lens_material_design where material_id='$met_id_ins' and  design_id='$des_id_ins'")or writeMysqlLog(imw_error().' 333');
						if(imw_num_rows($qry)==0){
							imw_query("insert into in_lens_material_design set material_id='$met_id_ins',design_id='$des_id_ins'")or writeMysqlLog(imw_error().' 334');
						}
					}
				}

				if($vw_data[$vw_data_key]['tag']=="TREATMENT"){
					if( array_key_exists('attributes', $vw_data[$vw_data_key]) && is_array($vw_data[$vw_data_key]['attributes']) && array_key_exists('CODE', $vw_data[$vw_data_key]['attributes']) && $vw_data[$vw_data_key]['attributes']['CODE'] != ""){
						$treat_desc=trim($vw_data[$vw_data_key]['attributes']['DESCRIPTION']);
						$treat_code=trim($vw_data[$vw_data_key]['attributes']['CODE']);
						$qry=imw_query("select id,ar_name,vw_code from in_lens_ar where vw_code='$treat_code' and del_status!='2'")or writeMysqlLog(imw_error().' 343');
						if(imw_num_rows($qry)>0){
							$row=imw_fetch_array($qry);
							$t_type_id=$row['id'];
							$t_type_name=$row['ar_name'];
							$t_vw_code=$row['vw_code'];
							$qry2=imw_query("select ar_id from in_lens_ar_material where ar_id='$t_type_id' and material_id='$met_id_ins'")or writeMysqlLog(imw_error().' 349');
							if(imw_num_rows($qry2)==0){
								imw_query("insert into in_lens_ar_material set ar_id='$t_type_id',material_id='$met_id_ins'")or writeMysqlLog(imw_error().' 251');
							}
						}else{
							$qry=imw_query("select id from in_lens_ar where ar_name='".imw_real_escape_string($treat_desc)."' and del_status!='2'")or writeMysqlLog(imw_error().' 355');
							if(imw_num_rows($qry)>0){
								$row=imw_fetch_array($qry);
								$t_type_id=$row['id'];
								imw_query("update in_lens_ar set vw_code='$treat_code' where id='$t_type_id'")or writeMysqlLog(imw_error().' 358');
							}else{
								imw_query("insert into in_lens_ar set ar_name='".imw_real_escape_string($treat_desc)."',vw_code='$treat_code'")or writeMysqlLog(imw_error().' 360');
								$t_type_id=imw_insert_id();
							}

							$qry2=imw_query("select ar_id from in_lens_ar_material where ar_id='$t_type_id' and material_id='$met_id_ins'")or writeMysqlLog(imw_error().' 364');
							if(imw_num_rows($qry2)==0){
								imw_query("insert into in_lens_ar_material set ar_id='$t_type_id',material_id='$met_id_ins'")or writeMysqlLog(imw_error().' 366');
							}
						}
					}
				}
			}
			unset($vw_data);
			$msg="Record imported successfully";
		}
		catch(Exception $e)
		{
			$msg="Error in data import.";
		}
	}
	elseif($_REQUEST['method']=="GetTracking"){
		$soapClient = new SoapClient("https://services.visionweb.com/VWOrderTracking.asmx?WSDL");
		$sel_ord_track=imw_query("select vw_order_id,loc_id from in_order_details where vw_order_id!='' group by vw_order_id")or writeMysqlLog(imw_error().' 382');
		while($row_ord_track=imw_fetch_array($sel_ord_track)){
			$params['username'] = $username_arr[$row_ord_track['loc_id']];
			$params['password'] = $password_arr[$row_ord_track['loc_id']];
			$params['refid'] = $prod_refid;
			$params['orderId'] = $row_ord_track['vw_order_id'];
			$sendRq = $soapClient->GetTracking($params);
			$sendRq_data = $sendRq->GetTrackingResult;
			$sendRq_data_error=simplexml_load_string($sendRq_data);
			$vw_status=$sendRq_data_error->SUPPLIER->ACCOUNT->ITEM->attributes()->Status;
			imw_query("update in_order_details set vw_status='$vw_status' where vw_order_id='".$row_ord_track['vw_order_id']."'")or writeMysqlLog(imw_error().' 392');
		}
	}elseif($_REQUEST['method']=="GetTrackingHistory"){
		$soapClient = new SoapClient("https://services.visionweb.com/VWOrderTracking.asmx?WSDL");
		if($order_id>0){
			$whr_ord="order_id='$order_id'";
		}else{
			$whr_ord="vw_order_id!=''";
		}
		$sel_ord_track=imw_query("select vw_order_id,loc_id from in_order_details where $whr_ord group by vw_order_id")or writeMysqlLog(imw_error().' 401');
		while($row_ord_track=imw_fetch_array($sel_ord_track)){
			$params['username'] = $username_arr[$row_ord_track['loc_id']];
			$params['password'] = $password_arr[$row_ord_track['loc_id']];
			$params['refid'] = $prod_refid;
			$params['orderId'] = $row_ord_track['vw_order_id'];
			$sendRq = $soapClient->GetTrackingHistory($params);
			$sendRq_data = $sendRq->GetTrackingHistoryResult;
			$sendRq_data_error=simplexml_load_string($sendRq_data);
			$vw_received_at_old="";
			foreach($sendRq_data_error->SUPPLIER->ACCOUNT->ITEM as $item_obj){
				$vw_status=$item_obj->attributes()->Status;
				$vw_tracking_id=$item_obj->attributes()->Visionweb_Tracking_Id;
				$vw_received_at_stt=strtotime($item_obj->attributes()->Received_at);
				$vw_received_at=date("Y-m-d h:i:s",$vw_received_at_stt);
				if($vw_received_at>=$vw_received_at_old){
					imw_query("update in_order_details set vw_status='$vw_status' where vw_order_id='".$vw_tracking_id."'")or writeMysqlLog(imw_error().' 417');
				}
				$vw_received_at_old=$vw_received_at;
				$sel_track=imw_query("select id from in_vw_order_status_detail where vw_status='$vw_status' and vw_order_id='$vw_tracking_id'")or writeMysqlLog(imw_error().' 420');
				if(imw_num_rows($sel_track)==0){
					imw_query("insert in_vw_order_status_detail set vw_status='$vw_status',vw_order_id='$vw_tracking_id',vw_received_date='$vw_received_at'")or writeMysqlLog(imw_error().' 422');
				}
			}
		}
	}elseif($_REQUEST['method']=="UploadFile"){
		$file_string_arr=send_to_visionweb($order_id,$order_detail_ids,$patient_id);
		$file_string=$file_string_arr['create_order_xml'];
		$order_detail_ids_str=str_replace(",","','",$order_detail_ids);
		$order_detail_ids_hyp=str_replace(",","-",$order_detail_ids);
		$order_detail_ids_arr=explode(",",$order_detail_ids);
		//$pos_order_id=$order_id.'-'.$order_detail_ids_hyp;
		if($file_string!=""){
			$soapClient = new SoapClient("https://services.visionweb.com/FileUpload.asmx?WSDL");
			
			$xmlparser = xml_parser_create();
			xml_parse_into_struct($xmlparser,$file_string,$vw_data);
			xml_parser_free($xmlparser);
			foreach($vw_data as $vw_data_key=>$vw_data_arr){
				if($vw_data_arr['value']=="SupplierName"){
					$sloid=trim($vw_data[$vw_data_key+1]['value']);
				}
				if($vw_data_arr['value']=="OrderId"){
					$pos_order_id=trim($vw_data[$vw_data_key+1]['value']);
				}
			}

			$params['username'] = $prod_user;
			$params['refid'] = $prod_refid;
			$params['password'] = $prod_pass;
			$params['pswd'] = $prod_pass;
			$params['filestring'] = $file_string;
			$params['subordid'] = $pos_order_id;
			$params['msgguid'] = $vw_submitted_id_arr[$vw_loc_id];
			$params['sloid'] = '9901';
			$params['guid'] = '';
			$params['cbsid'] = '';
			$params['ordtype'] = '';
			$params['filename'] = '';
			$params['type'] = 'SUP';
			$sendRq = $soapClient->UploadFile($params);
			$sendRq_data = $sendRq->UploadFileResult;
			imw_query("update in_vision_web set vw_submitted_id=vw_submitted_id+1 where vw_loc_id='$vw_loc_id'");
			$sendRq_data_error=simplexml_load_string($sendRq_data);
			//print_r($sendRq);
		}
		if($file_string==""){
			$msg="Order already sent";
		}else if($sendRq_data_error->Status=="Sent" && $sendRq_data_error->VWebOrderId != ''){
			$vw_order_id=$sendRq_data_error->VWebOrderId;
			$vw_exchange_id=$sendRq_data_error->VWebExchangeId;
			$msg="Order sent successfully";
			imw_query("update in_order_details set vw_sent_date='$entered_date',vw_order_id='$vw_order_id',vw_exchange_id='$vw_exchange_id',order_status='ordered',ordered='$entered_date' where id in ('$order_detail_ids_str')");
			for($g=0;$g<count($order_detail_ids_arr);$g++){
				imw_query("insert in_order_detail_status set patient_id='$patient_id',item_id='".$file_string_arr['item_id'][$order_detail_ids_arr[$g]]."',order_qty='".$file_string_arr['qty'][$order_detail_ids_arr[$g]]."',order_id='$order_id',order_detail_id='".$order_detail_ids_arr[$g]."',order_status='ordered',order_date='$entered_date',order_time='$entered_time',operator_id='$operator_id'")or writeMysqlLog(imw_error().' 475');
			}
			$chk_ord=imw_query("select id from in_order_details where order_id='$order_id' and del_status='0' group by order_status");
			if(imw_num_rows($chk_ord)==1){
				imw_query("update in_order set order_status='ordered' where id='$order_id'");
			}
		}else{
			$vw_exchange_id=$sendRq_data_error->VWebExchangeId;
			if($vw_exchange_id!=''){
				imw_query("update in_order_details set vw_sent_date='$entered_date',vw_exchange_id='$vw_exchange_id' where id in ('$order_detail_ids_str')")or writeMysqlLog(imw_error().' 484');
			}
			$msg=error_msg($sendRq_data_error->ErrorList);
			if($msg==""){
				$msg=$sendRq_data;
			}
		}
	}
	//echo "Record imported successfully";
	//header("Content-type: text/xml");
	//$params['supid'] = 9901;
	//$params['type'] = 'SUP';
	
	//var_dump($soapClient->__getFunctions());
	if($log_msg==true){	writeProcessLog(" $msg \n -------------------------------------------------------------------- ");}
	print($msg);
?>