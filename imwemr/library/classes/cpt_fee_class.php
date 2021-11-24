<?php 
	class CPT_Fee{
		
		public $auth_id = '';
		public $csv_cpt_global_arr = array();
		public $cpt_global_arr = array();
		public $cpt_fee_name_arr = array();
		public $json_cpt_fee_name_arr = array();
		public $cpt_cat_arr = array();
		public $cpt_rvu_records = array();
		public $cpt_fee_table = array();
		public $cpt_fee_table_arr = array();
		public $entered_date =''; 
		
		function __construct($auth_id,$request_made=null,$order_field='',$order_type=''){
			$this->auth_id = $auth_id;
			$this->entered_date = date('Y-m-d H:i:s'); 
			$this->get_cpt_master_arr($request_made,$order_field,$order_type);
			$this->get_csv_cpt_master_arr($request_made,$order_field,$order_type);
			$this->get_cpt_fee_table_nm();
			$this->get_cpt_category();
			$this->get_rvu_records();
			$this->get_cpt_fee_table();	
		}
		
		public function get_csv_cpt_master_arr($cptfeeId=null,$order_field="cpt_category_tbl.cpt_category",$order_type="ASC"){
		if(empty($order_field)) $order_field = 'cpt_category_tbl.cpt_category';
		
		$Whrcondition = "";
		if($cptfeeId!=null){
			$Whrcondition = "and cpt_category_tbl.cpt_cat_id IN($cptfeeId)";
		}
		$qryId = imw_query("SELECT cpt_category_tbl.cpt_category,cpt_fee_tbl.cpt_fee_id,
				cpt_fee_tbl.cpt_desc,cpt_fee_tbl.cpt4_code,
				cpt_fee_tbl.cpt_prac_code FROM cpt_fee_tbl 
				LEFT JOIN cpt_category_tbl ON cpt_category_tbl.cpt_cat_id = cpt_fee_tbl.cpt_cat_id
				where cpt_fee_tbl.delete_status = '0'
				AND cpt_fee_tbl.status='Active' $Whrcondition
				ORDER BY $order_field $order_type ");	
				while($row = imw_fetch_array($qryId)){
				$Detail[] = $row;
			}
			$this->csv_cpt_global_arr = $Detail;
		}
	
		//Returns global arr for CPT Fee to be used
		public function get_cpt_master_arr($cptfeeId=null,$order_field="cpt_category_tbl.cpt_category",$order_type="ASC"){
			if(empty($order_field)) $order_field = 'cpt_category_tbl.cpt_category';
			$Whrcondition = "";
			if(is_null($cptfeeId) == false && $cptfeeId!=''){
				$Whrcondition = "and cpt_category_tbl.cpt_cat_id IN($cptfeeId)";
			}
			$Detail = array();
			$qryId = imw_query("SELECT ".
			   "cpt_category_tbl.cpt_category, ".
			   "pos_tbl.pos_code, ".
			   "tos_tbl.tos_code, ".			   
			   "cpt_fee_tbl.* ".			   
			   "FROM ".
			   "cpt_fee_tbl ".
			   "LEFT JOIN cpt_category_tbl ON cpt_category_tbl.cpt_cat_id = cpt_fee_tbl.cpt_cat_id ".
			   "LEFT JOIN tos_tbl ON tos_tbl.tos_id = cpt_fee_tbl.tos_id ".
			   "LEFT JOIN pos_tbl ON pos_tbl.pos_id = cpt_fee_tbl.pos_id ".
				"where cpt_fee_tbl.delete_status = '0' AND cpt_fee_tbl.status='Active' $Whrcondition".
			   "ORDER BY $order_field $order_type ");
			while($row = imw_fetch_array($qryId)){
				$Detail[] = $row;
			}
			$this->cpt_global_arr = $Detail;
		}
		
		//Returns CPT Fee table names array
		public function get_cpt_fee_table_nm(){
			$fee_name_arr=array();
			$json_fee_name_arr=array();
			$qryId = imw_query("select * from fee_table_column order by fee_table_column_id");
			while($row =imw_fetch_array($qryId)){
				$fee_name_arr[] = $row;
				$json_fee_name_arr[]=$row['column_name'];
			}
			$this->cpt_fee_name_arr = $fee_name_arr;
			$this->json_cpt_fee_name_arr = $json_fee_name_arr;
		}
		
		
		//Saves header data
		public function save_header_data($request){
			//----- Start Insert And Updation Query --------
			if($request['saveDataFld']){
				$table_column = $request['table_column'];
				$fee_table_column_id="";
				$copy_fee_tbl_col_opt=$request['copy_fee_tbl_col_opt'];
				//---- Start Query To check The already Exsts Name --------
				if($id == ''){
					$qryId = imw_query("select column_name from fee_table_column where column_name = '$table_column'");
				}
				else{
					$qryId = imw_query("select column_name from fee_table_column where column_name = '$table_column'
										  and fee_table_column_id != '$id'");
				}
				
				if(imw_num_rows($qryId) > 0){
					$err = 'Column Name already exists.';
				}else{
					if($id == ''){
						$qry = "insert into fee_table_column set column_name = '".trim($table_column)."'";
						$qryId = imw_query($qry);
						$fee_table_column_id = imw_insert_id();
					}
					else{
						$qry = "update fee_table_column set column_name = '".trim($table_column)."'
								where fee_table_column_id = '$id'";
						$qryId = imw_query($qry);
					}
					if(imw_affected_rows()){
						$err = 'Column Name Successfully Saved.';
					}
				}
				
				if($fee_table_column_id>0 && $copy_fee_tbl_col_opt>0){
					$copy_fee_qry=imw_query("select * from cpt_fee_table where fee_table_column_id='$copy_fee_tbl_col_opt'");
					while($copy_fee_row = imw_fetch_array($copy_fee_qry)){
						$copy_cpt_fee_id=$copy_fee_row['cpt_fee_id'];
						$copy_cpt_fee=$copy_fee_row['cpt_fee'];
						imw_query("insert into cpt_fee_table set cpt_fee_id='$copy_cpt_fee_id',cpt_fee='$copy_cpt_fee',fee_table_column_id='$fee_table_column_id'");
					}
				}
			}
			if($request['inc_dec_save']){
				$fee_tbl_col_opt_arr=$request['fee_tbl_col_opt'];
				$counter = 0;
				for($i=0;$i<count($fee_tbl_col_opt_arr);$i++){
					$fee_tbl_col_opt=$fee_tbl_col_opt_arr[$i];
					$inc_dec_opt= $request['inc_dec_opt'];
					$inc_dec_per= $request['inc_dec_per'];
					if($inc_dec_opt=="decrease"){
						$sign="-";
					}else{
						$sign="+";
					}
					imw_query("insert into cpt_fee_inc_dec set fee_table_column_id='$fee_tbl_col_opt',increase_decrease='$inc_dec_opt',percentage='$inc_dec_per',operator_id='$this->auth_id',entered_date='$this->entered_date'");
					imw_query("UPDATE cpt_fee_table SET `cpt_fee` = cpt_fee".$sign."(cpt_fee*".$inc_dec_per."/100) where `fee_table_column_id`='$fee_tbl_col_opt'");
					$counter = ($counter+imw_affected_rows());
				}
				if ($counter >0) {
					$err = 'Record Updated Successfully.';
				}
			}
			return $err;
		}
		
		public function save_fee_table_data($request){
			 if($request['saveData'] == 'Save'){
				$this->get_csv_cpt_master_arr($request['cat_fee_tbl']); // This is used to change seleted cat values after reload
				$counter = 0;
				if($request['cat_fee_tbl']!=""){
					//$qry = "update cpt_fee_tbl set not_covered = '0' where cpt_cat_id in(".$request['cat_fee_tbl'].")";
				}else{
					//$qry = "update cpt_fee_tbl set not_covered = '0'";
				}
				//imw_query($qry);
				/* if(count($notCovered)>0){
					$cptId = implode(',',$notCovered);
					$qry = "update cpt_fee_tbl set not_covered = '1' where cpt_fee_id in($cptId)";
					imw_query($qry);
				} */
				$arr_Cptfee_id=array();
				$qry="select cpt_fee_table_id,cpt_fee_id,fee_table_column_id from cpt_fee_table ";
				$queryId=imw_query($qry);	
				while($row_f=imw_fetch_assoc($queryId)){
					$cpt_fee_table_id=$row_f['cpt_fee_table_id'];
					$cptFee_id=$row_f['cpt_fee_id'];
					$cptFee_col_id=$row_f['fee_table_column_id'];
					$arr_Cptfee_id[$cptFee_id][$cptFee_col_id]=$cpt_fee_table_id;
				}
				$arr_rvu=array();
				$qry_rvu="Select id,cpt_fee_id FROM rvu_records";
				$rs_rvu=imw_query($qry_rvu);
				while($row_rvu=imw_fetch_assoc($rs_rvu)){
					$arr_rvu[$row_rvu['cpt_fee_id']]=$row_rvu['id'];
				}
				$cptDetails = $this->csv_cpt_global_arr;
				for($i=0;$i<count($cptDetails);$i++){
					$cptFeeId = $cptDetails[$i]['cpt_fee_id'];
					//SET RVU VALUES
					$qryPrefix = "Insert INTO";
					$where ='';
					if($arr_rvu[$cptFeeId]){ 
						$qryPrefix='Update'; 
						$where=" WHERE id ='".$arr_rvu[$cptFeeId]."'";
					}
					$currency = str_ireplace("&nbsp;"," ",show_currency());
					$currency_arr=array($currency,trim($currency));
					$qry=$qryPrefix." rvu_records SET 
					cpt_fee_id = '".$cptFeeId."',
					work_rvu='".filter_var($request['work_rvu'][$cptFeeId], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION)."',
					pe_rvu='".filter_var($request['pe_rvu'][$cptFeeId], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION)."',
					mp_rvu='".filter_var($request['mp_rvu'][$cptFeeId], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION)."'
					".$where;
					$rs = imw_query($qry);
				//----------------
				$Detail = $this->cpt_fee_name_arr;
				for($d=0;$d<count($Detail);$d++){
				//$notCoverVal = $request['notCovered'.($i+1)];
				$tableColumnName = str_replace(" ","_",$Detail[$d]['column_name']."_".$Detail[$d]['fee_table_column_id']);
				$tableColumnId = $Detail[$d]['fee_table_column_id'];
				$feeValue = filter_var($request[''.$tableColumnName.''][$cptFeeId], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
				/*$feeValue = str_replace(',','',$request[''.$tableColumnName.''][$cptFeeId]);
				$feeValue = str_replace($currency_arr,'',$feeValue);
				$feeValue = str_replace($currency_arr,'',htmlentities($feeValue));	*/
				$cpt_fee_table_id = $arr_Cptfee_id[$cptFeeId][$tableColumnId];
				$updatecpt = false;
				if($feeValue!=''){
				$feequery = ",cpt_fee = '$feeValue'";
				$updatecpt = true;
				}
				if(!$cpt_fee_table_id){
					$query = "insert into cpt_fee_table set cpt_fee_id = '$cptFeeId', fee_table_column_id = '$tableColumnId'".$feequery;
				}
				elseif($updatecpt){
					$query = "update cpt_fee_table set cpt_fee = '".$feeValue."' where cpt_fee_table_id = '$cpt_fee_table_id'";
					
				}
					$queryId = imw_query($query);
					$counter = ($counter+imw_affected_rows());	
				}
					if ($counter >0) {
						$err = 'Record Updated Successfully.';
					} 
				}
				return $err;
			}
		}
	
		public function del_fee_col($request){
			if($request['DelColumn']){
				$qry = "delete from fee_table_column where fee_table_column_id = '".$request['DelColumn']."'";
				$qryId = imw_query($qry);
				if(imw_affected_rows() >0) {
					$err = 'Record Deleted Successfully.';
				} 
				return $err;
			}
		}
		
		public function get_cpt_category(){
			$cpt_name_arr=array();
			$qryId = imw_query("SELECT cpt_cat_id, cpt_category from cpt_category_tbl");
			while($row =imw_fetch_array($qryId)){
				$cpt_name_arr[] = $row;
			}
			$this->cpt_cat_arr = $cpt_name_arr;
		}
		
		public function get_rvu_records(){
			$rvu_records_arr=array();
			$qryId = imw_query("Select * FROM rvu_records");
			while($res =imw_fetch_array($qryId)){
				$cptFeeId = $res['cpt_fee_id'];
				$rvu_records_arr[$cptFeeId]['work_rvu'] = $res['work_rvu'];
				$rvu_records_arr[$cptFeeId]['pe_rvu'] = $res['pe_rvu'];
				$rvu_records_arr[$cptFeeId]['mp_rvu'] = $res['mp_rvu'];
			}
			$this->cpt_rvu_records = $rvu_records_arr;
		}
		
		public function get_cpt_fee_table(){
			$cpt_fee_table_arr=array();
			$qryId = imw_query("select cpt_fee_id,fee_table_column_id,cpt_fee,cpt_fee_table_id from cpt_fee_table");
			while($res =imw_fetch_array($qryId)){
				$cptFee_Id 			= $res["cpt_fee_id"];
				$feeTableColumnId 	= $res["fee_table_column_id"];
				$cptFee 			= $res["cpt_fee"];
				$cptFeeTableId 		= $res["cpt_fee_table_id"];
				
				$cpt_fee_table_arr[$cptFee_Id][$feeTableColumnId]['cpt_fee'] = $cptFee;
				$cpt_fee_table_arr[$cptFee_Id][$feeTableColumnId]['cpt_fee_table_id'] = $cptFeeTableId;
			}
			$this->cpt_fee_table = $cpt_fee_table_arr;
		}
	}
?>