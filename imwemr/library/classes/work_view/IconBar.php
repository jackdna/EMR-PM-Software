<?php
class IconBar{
	public $fid;
	public $pid;
	public $uid;
	public $arr_notifications;
	public function __construct($pid,$fid=""){
		$this->fid = $fid;
		$this->pid = $pid; //
		$this->uid = $_SESSION['authId'];
		
		//
		$cls_notifications = new core_notifications();
		if(!empty($this->pid) && (($_SESSION['notifiUpdatedForPt1'] != $this->pid) || ($_SESSION['notifiUpdatedForPr1'] != $this->uid))){
			$_SESSION['notifiUpdatedForPt1'] = $this->pid;
			$_SESSION['notifiUpdatedForPr1'] = $this->uid;
			$cls_notifications->update_all_notifications();
		}
		
		$this->arr_notifications = $cls_notifications->get_notification_status();
		
	}
	
	function getPtInstDocStatus(){
		$edu_status_icon = ''; //normal
		$edu_status_arr = getPtEduCondition($this->pid, $this->fid, $this->uid);
		if($edu_status_arr[3]=='yes'){
			$edu_status_icon = 'cbgreen'; //
		}else if($edu_status_arr[2]=='yes'){
			$edu_status_icon = 'cborange';
		}
		return $edu_status_icon;
	}
	
	function getSxStatus(){
		$sx_icon_bg = '';
		if($this->arr_notifications['sx']=='1'){
			$sx_icon_bg = 'cbgreen';
		}else if($this->arr_notifications['sx']=='2'){
			$sx_icon_bg = 'cborange';
		}
		return $sx_icon_bg;		
	}
	
	function getPtCommStatus(){
		$pvc_icon_bg = '';
		$query_pvc = "SELECT user_message_id FROM user_messages WHERE patientId = '".$this->pid."' AND Pt_Communication='1' 
					  and message_status = '0' and del_status = 0 and edit_status = 0";
		$result_pvc = imw_query($query_pvc);
		if($result_pvc && imw_num_rows($result_pvc)>0){
			$pvc_icon_bg = 'cbgreen';
		}
		
		$query_pvcRVW = "SELECT user_message_id FROM user_messages WHERE patientId = '".$this->pid."' AND Pt_Communication='1' 
						AND message_status = '0' 
						AND review_by = 0
						AND del_status = 0
						AND edit_status = 0
						";
		$result_pvcRVW = imw_query($query_pvcRVW);
		if($result_pvcRVW && imw_num_rows($result_pvcRVW)>0){
			$pvc_icon_bg = 'cborange';
		}				
		
		return  $pvc_icon_bg;
	}
	
	function getRFSStatus(){
		$ptalert_icon_bg = '';
		$query_ptalert = "Select cp.id,cp.patient_id,cp.form_id,cp.complication,cp.cmt,cp.proc_id,cp.site,cp.dx_code,cp.iop_type,cp.iop_od,cp.iop_os,cp.intravit_meds,cp.user_id,date_format(cmt.date_of_service,'%m-%d-%Y') as dos from chart_procedures as cp inner join chart_master_table as cmt on (cmt.id=cp.form_id and cmt.patient_id=cp.patient_id) inner join operative_procedures as opr on (opr.procedure_id=cp.proc_id)  WHERE cp.patient_id='".$this->pid."' and (cp.site IN ('OU','OD','OS') OR cp.lids_opts!='') AND cp.deleted_by=0 AND cmt.finalize='1' AND opr.ret_gl=1 AND opr.del_status!='1' order by cmt.date_of_service DESC";
		$result_ptalert = imw_query($query_ptalert);
		if($result_ptalert && imw_num_rows($result_ptalert)>0){
			$ptalert_icon_bg = 'cbgreen';
		}
		return $ptalert_icon_bg;		
	}
	
	function getPFSStatus(){
		$ptalert_icon_bg = '';
		$query_ptalert = "Select cp.id,cp.patient_id,cp.form_id,cp.complication,cp.cmt,cp.proc_id,cp.site,cp.dx_code,cp.iop_type,cp.iop_od,cp.iop_os,cp.intravit_meds,cp.user_id,date_format(cmt.date_of_service,'%m-%d-%Y') as dos from chart_procedures as cp inner join chart_master_table as cmt on (cmt.id=cp.form_id and cmt.patient_id=cp.patient_id)  LEFT JOIN chart_procedures_botox as cpb ON cp.id = cpb.chart_proc_id inner join operative_procedures as opr on (opr.procedure_id=cp.proc_id)  WHERE cp.patient_id='".$this->pid."' and (cp.site IN ('OU','OD','OS') OR cp.lids_opts!='' OR cpb.chart_proc_id IS NOT NULL) AND cmt.finalize='1' AND cp.deleted_by=0 AND opr.ret_gl!=1 AND  opr.del_status!='1' order by cmt.date_of_service DESC";	 //opr.ret_gl!=2 AND		
		$result_ptalert = imw_query($query_ptalert);
		if($result_ptalert && imw_num_rows($result_ptalert)>0){
			$ptalert_icon_bg = 'cbgreen';
		}
		return $ptalert_icon_bg;
	}
	
	function getGFSStatus(){
		$ptalert_icon_bg = '';
		$query_ptalert = "SELECT glucomaId,activate FROM glucoma_main WHERE patientId = '".$this->pid."' order by glucomaId DESC LIMIT 0,1";
		$result_ptalert = imw_query($query_ptalert);
		if($result_ptalert && imw_num_rows($result_ptalert)>0){
			$row_golacuma=imw_fetch_assoc($result_ptalert);
			$activ_status=$row_golacuma['activate'];
			$ptalert_icon_bg = 'cborange';
			if($activ_status==1){
				$ptalert_icon_bg = 'cbgreen';
			}			
		}
		return $ptalert_icon_bg;
	}
	
	function getPRSStatus(){
		$ptalert_icon_bg = '';
		if(!empty($this->pid)){
		/*Count Glasses(Refractive Rx)*/
		$query_ptalert = "
			SELECT count(*) AS count 
			FROM(
			SELECT 
			c0.*		
			FROM chart_vis_master c0
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			WHERE c0.patient_id = '".$this->pid."' AND c1.ex_type='MR' AND c1.delete_by='0'
			AND (
				c2.sph!='' OR c2.cyl!='' OR c2.axs!='' OR
				c3.sph!='' OR c3.cyl!='' OR c3.axs!='' 				
			) 
			GROUP BY c0.form_id) AS `tmptbl`
		";		
		$result_ptalert = imw_query($query_ptalert);
		$dataCount = ($result_ptalert && imw_num_rows($result_ptalert)>0) ? imw_fetch_assoc($result_ptalert):array('count'=>0);
		$dataCount = $dataCount['count'];
		/*End Count Glasses(Refractive Rx)*/
		
		/*Count SCL - Contact Lens (Rx)*/		
		$query_ptalert1 = "SELECT COUNT(`CLW`.`id`) AS `count` FROM `chart_master_table` `CM` INNER JOIN `contactlensmaster` `CLM` ON(`CM`.`id`=`CLM`.`form_id` AND CLM.clws_id>0) INNER JOIN `contactlensworksheet_det` `CLW` ON(`CLM`.`clws_id`=`CLW`.`clws_id` AND CLW.clws_id>0) WHERE `CM`.`patient_id`='".$this->pid."' AND `CLM`.`patient_id`='".$this->pid."' AND `CLW`.`clType` IN('scl','rgp', 'cust_rgp') GROUP BY `CLW`.`clws_id`";
		$result_ptalert1 = imw_query($query_ptalert1);
		$dataCount1 = ($result_ptalert1 && imw_num_rows($result_ptalert1)>0) ? imw_num_rows($result_ptalert1) : 0;
		
		/*End Count SCL - contact Lens (Rx)*/
		
		/*Count Previous Data for Precision*/
		if(isset($billing_global_server_name) && strtolower($billing_global_server_name)=='precision'){
		$query_ptalert2 = "SELECT COUNT(*) AS `count` FROM `flowsheet_master` `M` INNER JOIN `flowsheet_child` `C` ON(`M`.`FLOWSHEET_ID`=`C`.`FLOWSHEET_ID`) WHERE `M`.`PatientMRN`='".$this->pid."' AND `M`.`FLOWSHEETNAME` IN('Spectacle Rx', 'Contact Lens Rx 1') GROUP BY `C`.`FLOWSHEET_ID`";
		$result_ptalert2 = imw_query($query_ptalert2);
		$dataCount2 = ($result_ptalert2 && imw_num_rows($result_ptalert2)>0) ? imw_num_rows($result_ptalert2) : 0;
		}
		/*End Count Previous Data for Precision*/
		}
		
		if($dataCount>0 || $dataCount1>0 || $dataCount2>0){
			$ptalert_icon_bg = 'cbgreen';
		}
		return $ptalert_icon_bg;	
	}
	
	function getGenHealthStatus(){
		$medhx_img = '';
		if($this->arr_notifications['medHx']=='1'){
			$medhx_img = 'cbgreen';
		}else if($this->arr_notifications['medHx']=='2'){
			$medhx_img = 'cborange';
		}
		return $medhx_img;
	}
	
	function getTestEyeStatus(){
		$r="";
		if($this->arr_notifications['testseye']=='2'){
			$r="cborange";
		}else if($this->arr_notifications['testseye']=='1'){
			$r="cbgreen";
		}
		return $r;
	}
    
	function getConsultLetterStatus(){
		$r="";
		if($this->arr_notifications['consult']=='2'){
			$r="cborange";
		}else if($this->arr_notifications['consult']=='1'){
			$r="cbgreen";
		}
		return $r;
	}
	
	function getAllergyStatus(){
		$oMedHx =  new MedHx($this->pid);		
		$pt_allergy_cls = $oMedHx->getAllergies("title",2);	
		if($pt_allergy_cls=="NoData"){ $pt_allergy_cls="cborange"; }
		else if($pt_allergy_cls=="Allergic"){ $pt_allergy_cls="cbred"; }
		else if($pt_allergy_cls=="NKAllergy"){ $pt_allergy_cls="cbgreen"; }
		
		return $pt_allergy_cls;
	}
	
	function get_icons_status($m=""){
		$ret=array();		
		
		//Pt Instruction document
		$ret["patient_instruction_documents"]=$this->getPtInstDocStatus();
		
		//Allergies
		$ret['allergies'] =$this->getAllergyStatus();
		
		//Surgeries
		$ret['Surgeries'] =$this->getSxStatus();
		
		//Pt Comm.
		$ret['patient_communication'] = $this->getPtCommStatus();
		
		//Patient RFS(Retinal Flow Sheet)
		$ret['retinal_flow_sheet'] = $this->getRFSStatus();
		
		//Patient PFS(Procedue Flow Sheet)
		$ret['procedure_flow_sheet'] = $this->getPFSStatus();
		
		//Patient GFS(Glaucoma Flow Sheet)
		$ret['glaucoma_flow_sheet'] = $this->getGFSStatus();
		
		//PRS(Patient Refractive Sheet)
		$ret['patient_refractive_sheet'] = $this->getPRSStatus();
		
		//MEDICAL HISTORY (stethoscope)--------index (7)
		$ret['general_health'] = $this->getGenHealthStatus();
		
		//Test Manager
		$ret['test_manager'] = $this->getTestEyeStatus();
        
        //Consult Letter
		$ret['consult_letter'] = $this->getConsultLetterStatus();
		
		if($m==1){ echo json_encode($ret); }
		else{	return $ret; }
		
	}
}
?>