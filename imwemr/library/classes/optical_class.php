<?php
	Class Optical{
		public $patient_id = '';
		public $auth_id = '';
		public $order_id = '';
		public $vendor_typeahead_arr = array();
		public $global_currency = '';
		
		//Today order list variables
		public $today_order_arr = array();
		public $phy_name_arr = array();
		
		function __construct($pid,$auth_id){
			$this->patient_id = $pid;
			$this->auth_id = $auth_id;
			$this->get_vendor_details();
			$this->global_currency = show_currency();
			//Setting Operator ID
			$opr_id_sql= imw_query("select operator_id from optical_order_form where Optical_Order_Form_id = '$order_id'");
			$orderQryRes = imw_fetch_array($opr_id_sql);
			if(empty($orderQryRes['operator_id']) === false){
				$this->auth_id = $orderQryRes['operator_id'];
			}
		}
		
		function get_pt_ins_details(){
			$return_str = '';
			if(empty($this->patient_id) === false){
				//--- GET PATIENT INSURANCE CASE ID --------			
                $qry_str = imw_query("select insurance_case.ins_caseid from insurance_case join insurance_case_types
                        on insurance_case_types.case_id = insurance_case.ins_case_type 
                        join insurance_data on insurance_data.ins_caseid = insurance_case.ins_caseid
                        where insurance_case.patient_id = '$this->patient_id'
                        and insurance_case.case_status = 'Open' 
                        and insurance_data.provider > '0'
                        group by insurance_case.ins_caseid
                        order by insurance_case.ins_case_type limit 0,1");
                $qryRes = imw_fetch_array($qry_str);                
                $ins_caseid = $qryRes['ins_caseid'];
				
				//--- GET PRIMARY INSURANCE COMPANY DETAILS ---
                $priInsDetail = $this->get_ins_details($this->patient_id,$ins_caseid,'primary');
				$primaryInsId = $priInsDetail->provider;
				if($primaryInsId > 0){
                    $ins_comp_sql = imw_query("select in_house_code,name from insurance_companies
                            where id = '$primaryInsId'");
                    $qryRes = imw_fetch_array($ins_comp_sql);
                    $primaryInsName = $qryRes['in_house_code'];
                    if(empty($primaryInsName)){
                        $primaryInsName = substr($qryRes['name'],0,8).'...';
                    }
                    $return_str .= 'Primary Ins. '.$primaryInsName;
                }
				
				//--- GET SECONDARY INSURANCE COMPANY DETAILS ---                
                $priInsDetail = $this->get_ins_details($this->patient_id,$ins_caseid,'secondary');
                $secondaryInsId = $priInsDetail->provider;                
                if($secondaryInsId > 0){
                    $ins_comp_sec_sql = imw_query("select in_house_code,name from insurance_companies
                            where id = '$secondaryInsId'");
                    $qryRes = imw_fetch_array($ins_comp_sec_sql);
                    $secInsName = $qryRes['in_house_code'];
                    if(empty($secInsName)){
                        $secInsName = substr($qryRes['name'],0,8).'...';
                    }
                    if(empty($primaryInsName) == false){
                        $return_str .= ', ';
                    }
                    $return_str .= 'Secondary Ins. '.$secInsName;
                }
			}
			return $return_str;
		}
		function get_ins_details($patientId,$caseId,$type){
			$sql_qry = imw_query("select * from insurance_data where pid = '$patientId'
					and ins_caseid = '$caseId' and provider > '0'
					and type = '$type' and actInsComp = '1'");
			if(imw_num_rows($sql_qry) > 0){
				$return_val = imw_fetch_object($sql_qry);
			}
			return $return_val;
		}
		
		
		//CL Order list func.
		function get_pt_data_row($id){
			$sql = "SELECT * from patient_data where id = '$id'";
			$res = imw_query($sql);
			$row_address = @imw_fetch_array($res);
			return 	$row_address;
		}

		function changeDateFormat($selectDt) {
			$gtDate='';
			if($selectDt) {
				list($Mnt,$Dy,$Yr) = explode('-',$selectDt);
				if($Mnt && $Dy && $Yr) {
					$gtDate = $Yr.'-'.$Mnt.'-'.$Dy;
				}	
			}
			return $gtDate;
		}

		function displayDateFormat($selectDt) {
			$setDate='';
			if($selectDt && $selectDt!='0000-00-00') {
				list($Yr,$Mnt,$Dy) = explode('-',$selectDt);
				if($Yr && $Mnt && $Dy) {
					$setDate = date('m-d-Y',mktime(0,0,0,$Mnt,$Dy,$Yr));
				}	
			}
			return $setDate;
		}
		function displayDateFormatMMDDYY($selectDt) {
			$setDate='';
			if($selectDt && $selectDt!='0000-00-00') {
				list($Yr,$Mnt,$Dy) = explode('-',$selectDt);
				if($Yr && $Mnt && $Dy) {
					$setDate = date('m-d-y',mktime(0,0,0,$Mnt,$Dy,$Yr));
				}	
			}
			return $setDate;
		}

		function getUsrNme($usrID,$initial='') {
			$userNme='';
			if($usrID) {
				$qryUserNme = "SELECT * from users where id = '".$usrID."'";
				$resUserNme = imw_query($qryUserNme);
				$rowUserNme = imw_fetch_array($resUserNme);
				$fname = $rowUserNme['fname'];
				$mname = $rowUserNme['mname'];
				$lname = $rowUserNme['lname'];
				if($mname != '' && $mname != 'NULL') {
					$userNme = $fname."&nbsp;".$mname."&nbsp;".$lname;
				}
				else {
					$userNme = $fname."&nbsp;".$lname;
				}
				
				if($initial='initial') {
					$userNme = ucfirst(substr($fname,0,1)).ucfirst(substr($lname,0,1));
				}
			}
			return $userNme;
		}
		
		//Today order list functions
		function get_today_order_array(){
			$qry_sql = imw_query("select optical_order_form.frame_name,optical_order_form.lens_opt,
			optical_order_form.frame_color,optical_order_form.frame_style,
			date_format(order_date,'%m-%d-%y') as orderDate,
			date_format(order_place_date,'%m-%d-%y') as orderPlaceDate,		
			optical_order_form.balance,optical_order_form.patient_id,
			optical_order_form.operator_id,optical_order_form.Optical_Order_Form_id,
			date_format(optical_order_form.order_date,'%m-%d-%Y %r') as order_date,
			CONCAT(patient_data.lname,', ',fname) as Name,patient_data.fname,
			patient_data.phone_home	from optical_order_form
			join patient_data on patient_data.id = optical_order_form.patient_id
			where optical_order_form.order_status = '0' 
			and optical_order_form.patient_id = ".$this->patient_id."
			order by order_place_date desc , Optical_Order_Form_id desc");
			if(imw_num_rows($qry_sql) > 0){
				while($row = imw_fetch_array($qry_sql)){
					$this->today_order_arr[] = $row;
				}
			}
			return $this->today_order_arr;
		}	
		
		function get_operator(){
			$user_id = array();	
			foreach($this->today_order_arr as $obj){
				$user_id[] = $obj['operator_id'];
			}
			
			$userId = join(',',$user_id);
			$phy_sql = imw_query("select id,lname,fname,mname from users where id in ($userId)");
			if(imw_num_rows($phy_sql) > 0){
				while($phyQryRes = imw_fetch_array($phy_sql)){
					$id = $phyQryRes['id'];
					$phyName = ucfirst($phyQryRes['fname'][0]);
					$phyName .= ucfirst($phyQryRes['lname'][0]);
					$this->phy_name_arr[$id] = $phyName;
				}
			}
			return $this->phy_name_arr;
		}
		
		
		//Order form functions
		function get_vendor_details(){
			$return_arr = array();	
			$vendor_sql = imw_query("select distinct vendor_name from vendor_details where vendor_status = '0'");
			if(imw_num_rows($vendor_sql) > 0){
				while($row = imw_fetch_array($vendor_sql)){
					$return_arr[] = $row;
					$this->vendor_typeahead_arr[] = $row['vendor_name'];
				}
			}
			return $return_arr;	
		}
		
		function chartVisionVals($patient_id){
			
			$sql_check = "
				SELECT 
			
				c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
				c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
				c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
							
				c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, 
				c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
				c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l
							
				FROM chart_master_table c4
				INNER JOIN chart_vis_master c0 ON c4.id = c0.form_id
				LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
				LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
				LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
				WHERE c4.finalize='0' AND c4.patient_id = '$patient_id' 
				AND c1.mr_none_given!='' AND mr_none_given!='None'
				AND c1.ex_type='MR' AND c1.delete_by='0'  
				Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number
				LIMIT 1;
			";			 
			$res_check = imw_query($sql_check);
			$num_rows_check = imw_num_rows($res_check);
			if($num_rows_check > 0){
				return $res_check;
			}		 
			else{
				
				$sql_check = "
						SELECT 
					
						c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
						c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
						c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
									
						c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, 
						c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
						c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l
									
						FROM chart_master_table c4
						INNER JOIN chart_vis_master c0 ON c4.id = c0.form_id
						LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
						LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
						LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
						WHERE c4.finalize='1' AND c4.patient_id = '$patient_id' 
						AND c1.mr_none_given!='' AND mr_none_given!='None'
						AND c1.ex_type='MR' AND c1.delete_by='0'  
						Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number
						LIMIT 1;
					";		
						
				$res = imw_query($sql);
				$num_rows_check = imw_num_rows($res);
				if($num_rows_check > 0){
					return $res;
				}		
			}
			return false;
		}
		
		function chartVisionValsNotGiven($patient_id){
			$sql_check = "
				SELECT 
			
				c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
				c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
				c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
							
				c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, 
				c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
				c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l
							
				FROM chart_master_table c4
				INNER JOIN chart_vis_master c0 ON c4.id = c0.form_id
				LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
				LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
				LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
				WHERE c4.finalize='0' AND c4.patient_id = '$patient_id' 				
				AND c1.ex_type='MR' AND c1.delete_by='0'  
				AND (c2.sph!='' OR c2.cyl!='' OR c2.axs!='' OR c2.ad!='' OR c3.sph!='' OR c3.cyl!='' OR c3.axs!='' OR c3.ad!='')
				Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number
				LIMIT 1;
			";			 
			$res_check = imw_query($sql_check);
			$num_rows_check = imw_num_rows($res_check);
			if($num_rows_check > 0){
				return $res_check;
			}else{
				$sql_check = "
						SELECT 
					
						c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
						c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
						c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
									
						c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, 
						c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
						c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l
									
						FROM chart_master_table c4
						INNER JOIN chart_vis_master c0 ON c4.id = c0.form_id
						LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
						LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
						LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
						WHERE c4.finalize='1' AND c4.patient_id = '$patient_id' 						
						AND c1.ex_type='MR' AND c1.delete_by='0'  
						AND (c2.sph!='' OR c2.cyl!='' OR c2.axs!='' OR c2.ad!='' OR c3.sph!='' OR c3.cyl!='' OR c3.axs!='' OR c3.ad!='')
						Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number
						LIMIT 1;
					";		
						
				$res = imw_query($sql);
				$num_rows_check = imw_num_rows($res);
				if($num_rows_check > 0){
					return $res;
				}
				
			}
			return false;
		}		
		
		function formatDate($format){
			 list($m,$d,$y) = explode('-',$format);
			 $setDate = $y.'-'.$m.'-'.$d;
			 return $setDate;
		}

		function AddRecords1($fieldName,$tableName){
			extract($fieldName);
			$frameOrder = $this->formatDate($Notification_comments);
			$lensOrder = $this->formatDate($lens_order);
			$frameRecieve = $this->formatDate($frame_recieve);
			$lensRecieve = $this->formatDate($lens_recieve);
			$notify = $this->formatDate($patient_notify);
			$picked_up = $this->formatDate($patient_picked_up);
			$date_of_sale = $this->formatDate($sale_date);
			$order_date = date("Y-m-d H:i:s");
			$order_place_date = date('Y-m-d');
			$add_query = "Insert into $tableName set ht_ph_od ='$ht_ph_od',
							ht_pv_od ='$ht_pv_od',ht_ph_os = '$ht_ph_os',
							ht_pv_os = '$ht_pv_os',	patient_id = '$patient_id',
							ref = '$ref',reorder = '$reorder',sphere_od = '$sphere_od',
							cyl_od = '$cyl_od',axis_od = '$axis_od',add_od = '$add_od',
							prism_od = '$prism_od',base_od = '$base_od',dec_od = '$dec_od',
							dist_pd_od = '$dist_pd_od',near_pd_od = '$near_pd_od',
							sphere_os = '$sphere_os',cyl_os = '$cyl_os',axis_os = '$axis_os',
							add_os = '$add_os',prism_os = '$prism_os',base_os = '$base_os',
							dec_os = '$dec_os',dist_pd_os = '$dist_pd_os',
							near_pd_os = '$near_pd_os',frame_name = '$frame_name',
							frame_color = '$frame_color',frame_eye = '$frame_eye',
							frame_bridge = '$frame_bridge',	frame_b = '$frame_b',
							frame_ed = '$frame_ed',	frame_scr = '$frame_scr',
							frame_uv = '$frame_uv',	lens_opt = '$lens_opt',
							bifocal_opt = '$bifocal_opt',trifocal_opt = '$trifocal_opt',
							lens_material = '$lens_material',hi_index = '$hi_index',
							tini_opt = '$tini_opt',	transition_opt = '$transition_opt',
							ar_lens = '$ar_lens',ar_charge = '$ar_charge',
							frame_cost = '$frame_cost',	lenese_cost = '$lenese_cost',
							adminPatientLenseCost  = '$adminPatientLenseCost',
							txtUnit  = '$txtUnit',
							tint_cost = '$tint_cost',scr_cost = '$scr_cost',
							ar_cost = '$ar_cost',other_cost = '$other_cost',
							discount = '$discount',	total = '$total',
							deposit = '$deposit',balance = '$balance',
							order_place_date = '$order_place_date',
							paid_by = '$paid_by',payment_method = '$payment_method',
							comments = '$comments',promotions = '$promotions',
							Notification_comments = '$frameOrder',operator_id = '$operator_id',
							order_date = '$order_date',physician_id = '$physician_id',
							progressive_opt = '$progressive_opt',HT_lens = '$HT_lens',
							frame_scr_price = '$frame_scr_price',
							tini_solid_price = '$tini_solid_price',
							tini_gradient_price = '$tini_gradient_price',
							transition_price = '$transition_price',
							frame_ar_price = '$frame_ar_price',
							back_order_operator_id = '$back_order_operator_id',
							recieved_operator_id = '$recieved_operator_id',
							delevered_operator_id = '$delevered_operator_id',
							frame_style = '$frame_style',
							vendor_name = '$vendor_name',temple = '$temple',
							frame_a = '$frame_a',other_lens = '$other_lens',
							ar_desc = '$ar_desc',ar_cost1 = '$ar_cost1',
							polarized = '$polarized',lens_order = '$lensOrder',
							frame_recieve = '$frameRecieve',lens_recieve = '$lensRecieve',
							patient_notify = '$notify',patient_picked_up = '$picked_up',
							pof_frame = '$pof_frame',optic_ht = '$optic_ht',
							other_polar = '$other_polar',optic_ht_os = '$optic_ht_os',
							ref_frame_order = '$ref_frame_order',
							ref_lens_order = '$ref_lens_order',
							ref_frame_recieve = '$ref_frame_recieve',
							ref_lens_recieve = '$ref_lens_recieve',
							ref_pt_notify = '$ref_pt_notify',ref_pt_picked = '$ref_pt_picked',
							ref_date_sale = '$ref_date_sale',sale_date = '$date_of_sale',
							dis_actual_per = '$dis_actual_per',frameCostVal = '$frameCostVal',
							mr_od_p = '$elem_visMrOdP',mr_od_prism = '$elem_visMrOdPrism',
							mr_od_splash = '$elem_visMrOdSlash',mr_od_sel = '$elem_visMrOdSel1',
							mr_os_p = '$elem_visMrOsP',mr_os_prism = '$elem_visMrOsPrism',
							mr_os_splash = '$elem_visMrOsSlash',mr_os_sel = '$elem_visMrOsSel1',
							discount_frames = '$discount_frames',polar_cost = '$polar_cost',
							trans_cost = '$trans_cost',saleOperatorVal = '$saleOperatorVal',
							frame_dis_ap = '$frame_dis_ap',Polaroid_material = '$Polaroid_material',
							slad_off = '$slad_off',Photochromatic = '$Photochromatic',
							Slad_Off_cost = '$Slad_Off_cost',hi_cost_price = '$hi_cost_price',
							tint_cost_price = '$tint_cost_price',
							Photochromatic_cost='$Photochromatic_cost',
							framePrice = '$framePrice',prism_cost = '$prism_cost',
							uv_cost = '$uv_cost',order_confirm = '$order_confirm'";
			//print $insertQuery;
			$qryId = imw_query($add_query);
			$inserID = imw_insert_id();
			return $inserID;
		}		
		
		function UpdateRecords1($id,$chkField,$fieldName,$tableName){
			extract($fieldName);
			$frameOrder = $this->formatDate($Notification_comments);
			$lensOrder = $this->formatDate($lens_order);
			$frameRecieve = $this->formatDate($frame_recieve);
			$lensRecieve = $this->formatDate($lens_recieve);
			$notify = $this->formatDate($patient_notify);
			$picked_up = $this->formatDate($patient_picked_up);
			$date_of_sale = $this->formatDate($sale_date);
			$order_date = date("Y-m-d H:i:s");
			$update_query = "update $tableName set ht_ph_od ='$ht_ph_od',
							ht_pv_od ='$ht_pv_od',ht_ph_os = '$ht_ph_os',
							ht_pv_os = '$ht_pv_os',patient_id = '$patient_id',
							ref = '$ref',reorder = '$reorder',sphere_od = '$sphere_od',
							cyl_od = '$cyl_od',axis_od = '$axis_od',
							add_od = '$add_od',prism_od = '$prism_od',
							base_od = '$base_od',dec_od = '$dec_od',
							dist_pd_od = '$dist_pd_od',near_pd_od = '$near_pd_od',
							sphere_os = '$sphere_os',cyl_os = '$cyl_os',
							axis_os = '$axis_os',add_os = '$add_os',
							prism_os = '$prism_os',base_os = '$base_os',
							dec_os = '$dec_os',dist_pd_os = '$dist_pd_os',
							near_pd_os = '$near_pd_os',frame_name = '$frame_name',
							frame_color = '$frame_color',frame_eye = '$frame_eye',
							frame_bridge = '$frame_bridge',frame_b = '$frame_b',
							frame_ed = '$frame_ed',frame_scr = '$frame_scr',
							frame_uv = '$frame_uv',lens_opt = '$lens_opt',
							bifocal_opt = '$bifocal_opt',trifocal_opt = '$trifocal_opt',
							lens_material = '$lens_material',hi_index = '$hi_index',
							tini_opt = '$tini_opt',transition_opt = '$transition_opt',
							ar_lens = '$ar_lens',ar_charge = '$ar_charge',
							frame_cost = '$frame_cost',lenese_cost = '$lenese_cost',
							adminPatientLenseCost  = '$adminPatientLenseCost',
							txtUnit  = '$txtUnit',
							tint_cost = '$tint_cost',scr_cost = '$scr_cost',
							ar_cost = '$ar_cost',other_cost = '$other_cost',
							discount = '$discount',total = '$total',
							deposit = '$deposit',balance = '$balance',
							paid_by = '$paid_by',payment_method = '$payment_method',
							comments = '$comments',promotions = '$promotions',
							Notification_comments = '$frameOrder',operator_id = '$operator_id',
							order_date = '$order_date',physician_id = '$physician_id',
							progressive_opt = '$progressive_opt',HT_lens = '$HT_lens',
							frame_scr_price = '$frame_scr_price',
							tini_solid_price = '$tini_solid_price',
							tini_gradient_price = '$tini_gradient_price',
							transition_price = '$transition_price',
							frame_ar_price = '$frame_ar_price',
							back_order_operator_id = '$back_order_operator_id',
							recieved_operator_id = '$recieved_operator_id',
							delevered_operator_id = '$delevered_operator_id',
							frame_style = '$frame_style',vendor_name = '$vendor_name',
							temple = '$temple',frame_a = '$frame_a',
							other_lens = '$other_lens',ar_desc = '$ar_desc',
							ar_cost1 = '$ar_cost1',polarized = '$polarized',
							lens_order = '$lensOrder',frame_recieve = '$frameRecieve',
							lens_recieve = '$lensRecieve',patient_notify = '$notify',
							patient_picked_up = '$picked_up',pof_frame = '$pof_frame',
							optic_ht = '$optic_ht',other_polar = '$other_polar',
							optic_ht_os = '$optic_ht_os',ref_frame_order = '$ref_frame_order',
							ref_lens_order = '$ref_lens_order',
							ref_frame_recieve = '$ref_frame_recieve',
							ref_lens_recieve = '$ref_lens_recieve',
							ref_pt_notify = '$ref_pt_notify',ref_pt_picked = '$ref_pt_picked',
							ref_date_sale = '$ref_date_sale',sale_date = '$date_of_sale',
							dis_actual_per = '$dis_actual_per',frameCostVal = '$frameCostVal',
							mr_od_p = '$elem_visMrOdP',mr_od_prism = '$elem_visMrOdPrism',
							mr_od_splash = '$elem_visMrOdSlash',mr_od_sel = '$elem_visMrOdSel1',
							mr_os_p = '$elem_visMrOsP',mr_os_prism = '$elem_visMrOsPrism',
							mr_os_splash = '$elem_visMrOsSlash',mr_os_sel = '$elem_visMrOsSel1',
							discount_frames = '$discount_frames',polar_cost = '$polar_cost',
							trans_cost = '$trans_cost',saleOperatorVal = '$saleOperatorVal',
							frame_dis_ap = '$frame_dis_ap',Polaroid_material = '$Polaroid_material',
							slad_off = '$slad_off',Photochromatic = '$Photochromatic',
							Slad_Off_cost = '$Slad_Off_cost',hi_cost_price = '$hi_cost_price',
							tint_cost_price = '$tint_cost_price',prism_cost = '$prism_cost',
							Photochromatic_cost='$Photochromatic_cost',uv_cost = '$uv_cost',
							framePrice = '$framePrice',order_confirm = '$order_confirm'
							WHERE $chkField = '$id'";
			//print $insertQuery;die;
			$qryId = imw_query($update_query);
			return $id;
		}
		
		function copay_apply_chk($proc_code='',$pri_ins='',$sec_ins=''){
			$copay_collect=array();
			$copay_collect[0]=false;
			$copay_collect[1]=false;
			if($pri_ins){
				$pri_copay_collect = $this->getInsuranceDetails($pri_ins);
				if($pri_copay_collect->collect_copay==1){
					$copay_collect[0] = true;
				}
			}
			if($sec_ins){
				$sec_copay_collect = $this->getInsuranceDetails($sec_ins);
				if($sec_copay_collect->collect_copay==1){
					$copay_collect[1]=true;
				}
			}
			
			if($proc_code){
				$proc_code_arr = explode(',',$proc_code);
				$copay_apply_arr = array("99212","99213","99214","99215","99201","99202","99203","99204","99205","99241",
									 "99242","99243","99244","99245","92012","92013","92014","92002","92003","92004");
				$copay_apply_proc_code = array_intersect($copay_apply_arr,$proc_code_arr);
				if(count($copay_apply_proc_code)>0){
					$copay_apply_proc_imp = implode(',',$copay_apply_proc_code);
					$copay_collect[0] = true;
					$copay_collect[1] = true;
				}
			}
			return $copay_collect;
		}
		
		function getInsuranceDetails($id){
			$sql_qry = "select * from insurance_companies where id = '$id'";
			$qryId = imw_query($sql_qry);
			if(imw_num_rows($qryId)>0){
				$qryRes = imw_fetch_object($qryId);
			}
			return $qryRes;
		}
		
		function getHqFacility(){
			$sql = imw_query("SELECT id FROM facility WHERE facility_type = '1' LIMIT 0,1 ");
			$row = imw_fetch_array($sql);
			if(imw_num_rows($sql) > 0){
				return $row["id"];
			}
			else{
				// Fix if No Hq. is selected
				$sql = imw_query("SELECT id FROM facility LIMIT 0,1 ");
				$row = imw_fetch_array($sql);
				if(imw_num_rows($sql) > 0){
					return $row["id"];
				}
			}
		}
		
		function getEncounterId(){
			$facilityId = $this->getHqFacility();
			$sql = imw_query("SELECT encounterId FROM facility WHERE id='".$facilityId."' ");
			$row = imw_fetch_array($sql);

			if(imw_num_rows($sql) > 0){
				$encounterId = $row["encounterId"];
			}
			
			//get from policies
			$sql = imw_query("select Encounter_ID from copay_policies WHERE policies_id = '1' ");
			$row = imw_fetch_array($sql);
			if(imw_num_rows($sql) > 0){
				$encounterId_2 = $row["Encounter_ID"];		
			}
			//bigg
			if($encounterId<$encounterId_2){
				$encounterId = $encounterId_2;
			}
			
			//--		
			$counter=0; //check only 100 times
			do{
			
			$flgbreak=1;
			//check in superbill
			if($flgbreak==1){
				$sql = "select count(*) as num FROM superbill WHERE encounterId='".$encounterId."' ";
				$row = sqlQuery($sql);
				if($row!=false && $row["num"]>0){
					$flgbreak=0;
				}	
			}
			
			//check in chart_master_table--
			if($flgbreak==1){
				$sql = "select count(*) as num FROM chart_master_table WHERE encounterId='".$encounterId."' ";
				$row = sqlQuery($sql);
				if($row!=false && $row["num"]>0){
					$flgbreak=0;
				}
			}
			
			//check in Accounting
			if($flgbreak==1){
				$sql = "select count(*) as num FROM patient_charge_list WHERE encounter_id='".$encounterId."'";
				$row = sqlQuery($sql);
				if($row!=false && $row["num"]>0){
					$flgbreak=0;
				}	
			}
			
			if($flgbreak==0) {$encounterId=$encounterId+1;}
			$counter++;
			}while($flgbreak==0 && $counter<100);
			if($counter>=100){ exit("Error: encounter Id counter needs to reset."); }
			//--
			
			$sql = imw_query("UPDATE copay_policies SET Encounter_ID = '".($encounterId+1)."' WHERE policies_id='1' ");
			//$row = sqlQuery($sql);
			

			$sql = imw_query("UPDATE facility SET encounterId = '".($encounterId+1)."' WHERE id='".$facilityId."' ");
			//$row = sqlQuery($sql);
			return $encounterId;
		}
		
		function opticPrismVal($order_id){
			 $qry = "SELECT mr_od_p,mr_od_prism,mr_od_splash,mr_od_sel,
					mr_os_p,mr_os_prism,mr_os_splash,mr_os_sel FROM
					optical_order_form WHERE
					Optical_Order_Form_id = '$order_id'";
			$res = imw_query($qry);	
			return $res;
		}
		
		function accessAdmin($id){
			if(core_check_privilege(array("priv_admin")) == true){
				return "yes";
			}
			else
			{
				return "no";
			}
		}
		
		function getPofVal($order_id){
			$sql_qry = imw_query("SELECT pof_frame from optical_order_form where Optical_Order_Form_id = '$order_id'");
			$row = imw_fetch_array($sql_qry);
			if($row['pof_frame'] == ''){
				return true;
			}
			else{
				return false;
			}
		}
		
		
		//Optical order form functions
		function get_cpt_cost($request){
			$return_value = '';
			$cptCode = $request['cptCode'];
			$lenseType = $request['lenseType'];
			$frameMake = $request['frameMake'];

			$qry = imw_query("select cpt4_code,cpt_fee_id from cpt_fee_tbl 
					where (cpt4_code in('$cptCode') or cpt_prac_code in('$cptCode') 
					or cpt_desc in ('$cptCode')) AND delete_status = '0'");
			while($row = imw_fetch_array($qry)){
				$procQryRes[] = $row;
			}
			$cpt4_code = $procQryRes[0]['cpt4_code'];
			$cpt_fee_id = $procQryRes[0]['cpt_fee_id'];

			$qry = imw_query("select cpt_fee,cpt_fee_id from cpt_fee_table
					where fee_table_column_id = '1'
					and cpt_fee_id in ($cpt_fee_id)");
					
			while($row = imw_fetch_array($qry)){
				$FeeTableQryRes[] = $row;
			}
			if($lenseType){
				$qryGetlensCost = imw_query("select max(Retail_Price) as Retail_Price,Patient_Discount from optical_lenses where Tab_val = '$lenseType'");
				$resGetlensCost = imw_fetch_array($qryGetlensCost);
				$lensCost = $resGetlensCost['Retail_Price'];
				$lensDiscount = $resGetlensCost['Patient_Discount'];
			}
			if($frameMake){
				$qryGetFrameCost = imw_query("select max(retail_price) as retail_price,discount_patient from optical_frames where make_frame = '$frameMake'");
				$resGetFrameCost = imw_fetch_array($qryGetFrameCost);
				$frameCost = $resGetFrameCost['retail_price'];
				$frameDiscount = $resGetFrameCost['discount_patient'];
			}
			$return_value = $request['cptCode'].'__'.$FeeTableQryRes[0]['cpt_fee'].'__'.$lensCost.'__'.$lensDiscount.'__'.$frameCost.'__'.$frameDiscount;
			return $return_value;
		}
		
		function noBalBill($patient_id){
			$sql = imw_query("SELECT noBalanceBill from patient_data where id = '$patient_id'");
			$row = imw_fetch_array($sql);
			return $row['noBalanceBill'];
		}
		
		function get_frame_data($request){
			extract($request);
			$return_val = '';
			if($Vendor_name){
				$qry = "select * from vendor_details where vendor_name like '$Vendor_name%'
						and vendor_status = '0'";
				$qryId = imw_query($qry);
				if(imw_num_rows($qryId)>0){
					$data = '
						<tr class="grythead">
							<th>Manufacturer</th>
							<th>Address</th>
							<th>Telephone</th>
							<th>Mobile</th>
							<th>Fax</th>
							<th>Email Address</th>
						</tr>';
				}
				else{
					$data = '
						<tr align="center">
							<th colspan="7">Vendor Name is Not Exists.</th>
						</tr>
					';
				}
				while($qryRes = imw_fetch_assoc($qryId)){
					$vandorRes = (object)$qryRes;
					$fax = $vandorRes->fax > 0 ? $vandorRes->fax : '';
					$data .= '
						<tr class="text-center">
							<td ><a href="javascript:get_frame(\''.$vandorRes->vendor_name.'\')" >'.$vandorRes->vendor_name.'</a></td>
							<td ><a href="javascript:get_frame(\''.$vandorRes->vendor_name.'\')" >'.$vandorRes->vendor_address.'</a></td>		
							<td ><a href="javascript:get_frame(\''.$vandorRes->vendor_name.'\')" >'.$vandorRes->tel_num.'</a></td>
							<td ><a href="javascript:get_frame(\''.$vandorRes->vendor_name.'\')" >'.$vandorRes->mobile.'</a></td>
							<td ><a href="javascript:get_frame(\''.$vandorRes->vendor_name.'\')" >'.$fax.'</a></td>
							<td ><a href="javascript:get_frame(\''.$vandorRes->vendor_name.'\')" >'.$vandorRes->email.'</a></td>
						</tr>
					';
				}
				
			}

			if($vendor_name_val){
				$qry = "select * from optical_frames where vendor_name = '$vendor_name_val'";
				$qryId = imw_query($qry);
				if(imw_num_rows($qryId)>0){
					$data = '
						<tr class="grythead">
							<th>Make</th>
							<th>Bar Code Id</th>
							<th>Style</th>
							<th>color</th>
							<th>Size Horizontal</th>
							<th>Size Bridge</th>
							<th>Size Vertical</th>
							<th>Cost Price</th>
							<th>Retail Price</th>
						</tr>
					';
				}else{
				$data = '
						<tr>
							<th class="text-danger text-center" colspan="8">No Make found.</th>
						</tr>
					';
				}
				if(imw_num_rows($qryId) > 0){
					$qryRes = imw_fetch_assoc($qryId);
					$frameRes = (object)$qryRes;
					$retail_price = $frameRes->retail_price > 0 ? '$'.number_format($frameRes->retail_price,2):'';
					$cost_price = $frameRes->cost_price > 0 ? '$'.number_format($frameRes->cost_price,2):'';
					$vertical = $frameRes->vertical > 0 ? $frameRes->vertical:'';
					$bridge = $frameRes->bridge > 0 ? $frameRes->bridge:'';
					$discount_frames = $this->getFrameDiscount($frameRes->vendor_name,$frameRes->make_frame,$frameRes->frame_style,$frameRes->frame_color) ;
					$horizontal = $frameRes->horizontal > 0 ? $frameRes->horizontal:'';
					$diagonal = $frameRes->diagonal > 0 ? $frameRes->diagonal:'';
					$data .= '
						<tr>
							<td align="center"><a href="javascript:get_frame_name(\''.$frameRes->vendor_name.'\',\''.$frameRes->frame_color.'\',\''.$frameRes->retail_price.'\',\''.$frameRes->frame_style.'\',\''.$discount_frames.'\',\''.$frameRes->make_frame.'\',\''.$horizontal.'\',\''.$bridge.'\',\''.$vertical.'\',\''.$diagonal.'\')" >'.$frameRes->make_frame.'</a></td>
							<td align="center"><a href="javascript:get_frame_name(\''.$frameRes->vendor_name.'\',\''.$frameRes->frame_color.'\',\''.$frameRes->retail_price.'\',\''.$frameRes->frame_style.'\',\''.$discount_frames.'\',\''.$frameRes->make_frame.'\',\''.$horizontal.'\',\''.$bridge.'\',\''.$vertical.'\',\''.$diagonal.'\')" >'.$frameRes->bar_code_id.'</a></td>
							<td align="center"><a href="javascript:get_frame_name(\''.$frameRes->vendor_name.'\',\''.$frameRes->frame_color.'\',\''.$frameRes->retail_price.'\',\''.$frameRes->frame_style.'\',\''.$discount_frames.'\',\''.$frameRes->make_frame.'\',\''.$horizontal.'\',\''.$bridge.'\',\''.$vertical.'\',\''.$diagonal.'\')" >'.$frameRes->frame_style.'</a></td>
							<td align="center"><a href="javascript:get_frame_name(\''.$frameRes->vendor_name.'\',\''.$frameRes->frame_color.'\',\''.$frameRes->retail_price.'\',\''.$frameRes->frame_style.'\',\''.$discount_frames.'\',\''.$frameRes->make_frame.'\',\''.$horizontal.'\',\''.$bridge.'\',\''.$vertical.'\',\''.$diagonal.'\')" >'.$frameRes->frame_color.'</a></td>
							<td align="center"><a href="javascript:get_frame_name(\''.$frameRes->vendor_name.'\',\''.$frameRes->frame_color.'\',\''.$frameRes->retail_price.'\',\''.$frameRes->frame_style.'\',\''.$discount_frames.'\',\''.$frameRes->make_frame.'\',\''.$horizontal.'\',\''.$bridge.'\',\''.$vertical.'\',\''.$diagonal.'\')" >'.$horizontal.'</a></td>
							<td align="center"><a href="javascript:get_frame_name(\''.$frameRes->vendor_name.'\',\''.$frameRes->frame_color.'\',\''.$frameRes->retail_price.'\',\''.$frameRes->frame_style.'\',\''.$discount_frames.'\',\''.$frameRes->make_frame.'\',\''.$horizontal.'\',\''.$bridge.'\',\''.$vertical.'\',\''.$diagonal.'\')" >'.$bridge.'</a></td>
							<td align="center"><a href="javascript:get_frame_name(\''.$frameRes->vendor_name.'\',\''.$frameRes->frame_color.'\',\''.$frameRes->retail_price.'\',\''.$frameRes->frame_style.'\',\''.$discount_frames.'\',\''.$frameRes->make_frame.'\',\''.$horizontal.'\',\''.$bridge.'\',\''.$vertical.'\',\''.$diagonal.'\')" >'.$vertical.'</a></td>
							<td align="center"><a href="javascript:get_frame_name(\''.$frameRes->vendor_name.'\',\''.$frameRes->frame_color.'\',\''.$frameRes->retail_price.'\',\''.$frameRes->frame_style.'\',\''.$discount_frames.'\',\''.$frameRes->make_frame.'\',\''.$horizontal.'\',\''.$bridge.'\',\''.$vertical.'\',\''.$diagonal.'\')" >'.$cost_price.'</a></td>
							<td align="center"><a href="javascript:get_frame_name(\''.$frameRes->vendor_name.'\',\''.$frameRes->frame_color.'\',\''.$frameRes->retail_price.'\',\''.$frameRes->frame_style.'\',\''.$discount_frames.'\',\''.$frameRes->make_frame.'\',\''.$horizontal.'\',\''.$bridge.'\',\''.$vertical.'\',\''.$diagonal.'\')" >'.$retail_price.'</a></td>
						</tr>
					';
				}
			}
			$return_val = '<table class="table table-bordered table-striped">'.$data.'</table>';
			return $return_val;
		}
		
		function get_frame_dropdown($request){
			$select_option = '';
			$vendor_name_val = $request['manf_name'];
			$cptCode = $request['cptCode'];

			$qry = imw_query("select cpt4_code,cpt_fee_id from cpt_fee_tbl 
					where (cpt4_code in('$cptCode') or cpt_prac_code in('$cptCode') 
					or cpt_desc in ('$cptCode')) AND delete_status = '0'");
			while($row = imw_fetch_array($qry)){
				$procQryRes[] = $row;
			}
			$cpt4_code = $procQryRes[0]['cpt4_code'];
			$cpt_fee_id = $procQryRes[0]['cpt_fee_id'];

			$qry = imw_query("select cpt_fee,cpt_fee_id from cpt_fee_table
					where fee_table_column_id = '1'
					and cpt_fee_id in ($cpt_fee_id)");
					
			while($row = imw_fetch_array($qry)){
				$FeeTableQryRes[] = $row;
			}

			$qry = imw_query("select * from optical_frames where vendor_name = '$vendor_name_val' and frame_status = '0'");
			$select_option = '
				<select name="frames_name" onChange="get_frame_name(this)" class="form-control minimal">
					<option value=""> Select </option>';
			while($qryRes = imw_fetch_array($qry)){
				$frameRes = (object)$qryRes;
				$retail_price = $frameRes->retail_price > 0 ? $this->global_currency.number_format($frameRes->retail_price,2):'';
				$cost_price = $frameRes->cost_price > 0 ? $this->global_currency.number_format($frameRes->cost_price,2):'';
				$vertical = $frameRes->vertical > 0 ? $frameRes->vertical:'';
				$bridge = $frameRes->bridge > 0 ? $frameRes->bridge:'';
				$discount_frames = $this->getFrameDiscount($frameRes->vendor_name,$frameRes->make_frame,$frameRes->frame_style,$frameRes->frame_color) ;
				$horizontal = $frameRes->horizontal > 0 ? $frameRes->horizontal:'';
				$diagonal = $frameRes->diagonal > 0 ? $frameRes->diagonal:'';
				$select_option .= '<option value="'.$frameRes->vendor_name.','.$frameRes->frame_color.','.$frameRes->retail_price.','.$frameRes->frame_style.','.$discount_frames.','.$frameRes->make_frame.','.$horizontal.','.$bridge.','.$vertical.','.$diagonal.','.$FeeTableQryRes[0]['cpt_fee'].'">'.$frameRes->make_frame.'</option>';
			}
			$select_option .= '</select>';
			return $select_option;
		}
		
		function getFrameDiscount($vendName,$frame,$style,$color){
			$discount_app = $this->noBalBill($this->patient_id);
			$qry_dis = "SELECT discount_family_friend,discount_patient,patient_discount_actual,family_discount_actual
						FROM optical_frames WHERE vendor_name = '$vendName' and make_frame = '$frame'
						and frame_style = '$style' and frame_color = '$color'";
			$res_dis = imw_query($qry_dis);
			$row_dis = imw_fetch_array($res_dis);	
			
			if(($discount_app == 0  && $row_dis['discount_patient'] != 0) || ($discount_app == 1 && $row_dis['discount_family_friend'] == 0 && $row_dis['family_discount_actual'] == 0))
			{
				return $row_dis['discount_patient']."p";	
			}
			else if(($discount_app == 0  && $row_dis['discount_patient'] == 0) || ($discount_app == 1 && $row_dis['discount_family_friend'] == 0 && $row_dis['family_discount_actual'] == 0))
			{
				return $row_dis['patient_discount_actual']."a";	
			}
			else if($discount_app == 1 && $row_dis['discount_family_friend'] != 0)
			{
				return $row_dis['discount_family_friend']."p";	
			}
			else if($discount_app == 1 && $row_dis['discount_family_friend'] == 0)
			{
				return $row_dis['family_discount_actual']."a";	
			}	
		}
		
		
		function get_tab_title($page_name){
			$ret_name = array();
			switch($page_name){
				case '':
					$ret_name['header_title'] = 'Contact Lens';
					$ret_name['tab_title']    = 'Contact Lens Order Sheet';
				break;
				
				case 'cl_order_list':
					$ret_name['header_title'] = 'Contact lens';
					$ret_name['tab_title']    = 'CL List';
				break;
				
				case 'todays_order_list':
					$ret_name['header_title'] = 'Glasses';
					$ret_name['tab_title']    = 'Order Form List';
				break;	
				
				case 'optical_order_form':
					$ret_name['header_title'] = 'Glasses';
					$ret_name['tab_title']    = 'Order Form';
				break;	
			}
			return $ret_name;
		}
	}

?>