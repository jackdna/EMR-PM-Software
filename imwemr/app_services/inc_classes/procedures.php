<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
set_time_limit(900);
include_once(dirname(__FILE__).'/chart_notes.php');
include(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
include(dirname(__FILE__)."/../../library/classes/work_view/wv_functions.php");
include(dirname(__FILE__)."/../../library/classes/work_view/ChartNote.php");
include(dirname(__FILE__)."/../../library/classes/work_view/ChartAP.php");
include(dirname(__FILE__)."/../../library/classes/work_view/MedHx.php");
include(dirname(__FILE__)."/../../library/classes/work_view/Fu.php");
include(dirname(__FILE__)."/../../library/classes/work_view/PnTempParser.php");
include(dirname(__FILE__)."/../../library/classes/work_view/CcHx.php");
include(dirname(__FILE__)."/../../library/classes/work_view/Patient.php");


class procedures extends chart_notes{
	public $procedure_id=0;
	public function __construct(){
		parent::__construct();
	}
	function get_procedures(){
		
		$objParser=new PnTempParser();
		$arrReturn 				 = array();
		$arrReturn['procedures'] = array();
		$arr_cosent=array();
		$get_mas_consent_qry1=imw_query("SELECT consent_form_id,consent_form_name from consent_form order by consent_form_name asc");
		while($get_mas_consent_arr1=imw_fetch_array($get_mas_consent_qry1)){
			$arr_cosent[$get_mas_consent_arr1['consent_form_id']]=$get_mas_consent_arr1['consent_form_name'];
		}
		
		$arr_op_report=array();
		$get_mas_op_report_qry1=imw_query("SELECT temp_name, temp_id from pn_template order by temp_name asc");
		while($get_mas_op_report_arr1=imw_fetch_array($get_mas_op_report_qry1)){
			$arr_op_report[$get_mas_op_report_arr1['temp_id']]=$get_mas_op_report_arr1['temp_name'];
		}
		
		$k=0;
		$arr_cpt_str=$arr_mod_str='';
		$arr_cpt_mod=array();
		$get_mas_proc_qry=imw_query("SELECT * from operative_procedures where del_status!=1 order by procedure_name asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			$procedure_name_arr[$get_mas_proc_arr['procedure_id']]=$get_mas_proc_arr['procedure_name'];
			$arrReturn['procedure'][$k]['name'] = $get_mas_proc_arr['procedure_name'];
			$arrReturn['procedure'][$k]['type'] = '';
			$arrReturn['procedure'][$k]['id']   = $get_mas_proc_arr['procedure_id'];
			$arr_cpt_mod=unserialize(html_entity_decode(trim($get_mas_proc_arr['cpt_code'])));
			if(count($arr_cpt_mod)>0)
			{
				for($i=1;$i<=count($arr_cpt_mod);$i++)
				{				
					if($arr_cpt_mod['cpt_code1_'.$i]){
						if(trim($arr_cpt_mod['cpt_code1_'.$i]))$arr_cpt_str.=$arr_cpt_mod['cpt_code1_'.$i]."; ";
						if(trim($arr_cpt_mod['mod_code1_'.$i]))$arr_mod_str.=$arr_cpt_mod['mod_code1_'.$i];
					}
					if($arr_cpt_mod['cpt_code2_'.$i]){
						if(trim($arr_cpt_mod['cpt_code2_'.$i]))$arr_cpt_str.=$arr_cpt_mod['cpt_code2_'.$i]."; ";
						if(trim($arr_cpt_mod['mod_code2_'.$i]))$arr_mod_str.=$arr_cpt_mod['mod_code2_'.$i];	
					}
					if($arr_cpt_mod['cpt_code3_'.$i]){
						if(trim($arr_cpt_mod['cpt_code3_'.$i]))$arr_cpt_str.=$arr_cpt_mod['cpt_code3_'.$i]."; ";
						if(trim($arr_cpt_mod['mod_code3_'.$i]))$arr_mod_str.=$arr_cpt_mod['mod_code3_'.$i];
					}
				}
			}
			$arrReturn['procedure'][$k]['cpt_code']=unserialize(html_entity_decode(trim($get_mas_proc_arr['cpt_code'])));
			$arrReturn['procedure'][$k]['cpt_code_str']=$arr_cpt_str;
			$arrReturn['procedure'][$k]['mod_code_str']=$arr_mod_str;
			$arrReturn['procedure'][$k]['dx_code']=$get_mas_proc_arr['dx_code'];
			$arrReturn['procedure'][$k]['time_out_request']=$get_mas_proc_arr['time_out_request'];
			unset($medArr,$medArrMAIN);
			foreach(explode('|',$get_mas_proc_arr['pre_op_meds']) as $singleMed)
			{
				$medArr['med'] = $singleMed;	
				$medArrMAIN[]=$medArr;
			}
			$arrReturn['procedure'][$k]['preop']=$medArrMAIN;
			
			unset($medArr,$medArrMAIN);
			foreach(explode('|',$get_mas_proc_arr['intraviteral_meds']) as $singleMed)
			{
				$medArr['med'] = $singleMed;
				$medArrMAIN[]=$medArr;	
			}
			$arrReturn['procedure'][$k]['intravitreal']=$medArrMAIN;
			
			unset($medArr,$medArrMAIN);
			foreach(explode('|',$get_mas_proc_arr['post_op_meds']) as $singleMed)
			{
				$medArr['med'] = $singleMed;
				$medArrMAIN[]=$medArr;	
			}
			
			$arrReturn['procedure'][$k]['postop']=$medArrMAIN;
			$arrReturn['procedure'][$k]['consent_form_id']=$get_mas_proc_arr['consent_form_id'];
			$arrReturn['procedure'][$k]['consent_form_name']=$arr_cosent[$get_mas_proc_arr['consent_form_id']];
			$arrReturn['procedure'][$k]['op_report_id']=$get_mas_proc_arr['op_report_id'];
			$arrReturn['procedure'][$k]['op_report_name']=$arr_op_report[$get_mas_proc_arr['op_report_id']];
			$k++;
		}
		
		$k=0;
		$get_mas_proc_qry=imw_query("SELECT * from diagnosis_code_tbl order by d_prac_code asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			//$arrReturn['dx'][$k]['name'] = $get_mas_proc_arr['d_prac_code'];
			$arrReturn['dx']['name'][$k] = $get_mas_proc_arr['d_prac_code'];
			$k++;
		}
		
		$k=0;
		$get_mas_proc_qry=imw_query("SELECT * from cpt_fee_tbl order by cpt_prac_code asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			$arrReturn['cpt'][$k]['name'] = $get_mas_proc_arr['cpt_prac_code'];
			$k++;
		}
		
		$k=0;
		$get_mas_proc_qry=imw_query("SELECT * from modifiers_tbl order by mod_prac_code asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			$arrReturn['mod'][$k]['name'] = $get_mas_proc_arr['mod_prac_code'];
			$k++;
		}
		
		if(constant("connect_optical")==1){
			$get_item_qry=imw_query("SELECT id,qty_on_hand from in_item where module_type_id='6' order by name asc");
			while($get_item_arr=imw_fetch_array($get_item_qry)){
				$item_arr[$get_item_arr['id']] = $get_item_arr['qty_on_hand'];
			}
			
			$get_item_lot_qry=imw_query("SELECT item_id,lot_no from in_item_lot_total order by lot_no asc");
			while($get_item_lot_arr=imw_fetch_array($get_item_lot_qry)){
				$item_lot_arr[$get_item_lot_arr['item_id']][] = $get_item_lot_arr['lot_no'];
			}
		}
		
		$k=0;
		$get_mas_proc_qry=imw_query("SELECT * from medicine_data order by medicine_name asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			$arrReturn['med'][$k]['name'] = $get_mas_proc_arr['medicine_name'];
			$arrReturn['med'][$k]['opt_med_id'] = $get_mas_proc_arr['opt_med_id'];
			$qty_on_hand="";
			$item_lot_val="";
			if($get_mas_proc_arr['opt_med_id']>0 && constant("connect_optical")==1){
				$qty_on_hand= $item_arr[$get_mas_proc_arr['opt_med_id']];
				$item_lot_val= $item_lot_arr[$get_mas_proc_arr['opt_med_id']];
			}
			
			$arrReturn['med'][$k]['qty_on_hand'] = $qty_on_hand;
			$arrReturn['med'][$k]['lot_no'] =  $item_lot_val;
			
			$k++;
		}
		
		$ik=0;
		$get_mas_consent_qry=imw_query("SELECT * from consent_form order by consent_form_name asc");
		require_once(dirname(__FILE__)."/print_all_consent_form_app.php");
		while($get_mas_consent_arr=imw_fetch_array($get_mas_consent_qry)){
			$arrReturn['consent'][$ik]['name'] = $get_mas_consent_arr['consent_form_name'];
			$_REQUEST['print_false']=1;
			$_REQUEST['patient_id']=$this->patient;
			$_REQUEST['form_id']=$this->form_id;
			$_REQUEST['consent_id']=$get_mas_consent_arr['consent_form_id'];
			$consent_form_content = get_consent();
			$arrReturn['consent'][$ik]['data'] = stripslashes(html_entity_decode($consent_form_content));
			$arrReturn['consent'][$ik]['id']   = $get_mas_consent_arr['consent_form_id'];
			$ik++;
		}
		
		$k=0;
		
		$get_mas_pn_qry=imw_query("SELECT * from pn_template order by temp_name asc");
		while($get_mas_pn_arr=imw_fetch_array($get_mas_pn_qry)){
			$arrReturn['opnote'][$k]['name'] = $get_mas_pn_arr['temp_name'];
			$arrReturn['opnote'][$k]['data'] = stripslashes(html_entity_decode($objParser->getDataParsed($get_mas_pn_arr['temp_data'],$this->patient,$this->form_id,'','','','','','','procedures')));
			$arrReturn['opnote'][$k]['id']   = $get_mas_pn_arr['temp_id'];
			$op_note_name_arr[$get_mas_pn_arr['temp_id']]=$get_mas_pn_arr['temp_name'];
			$k++;
		}
		
		$j=0;	
		$get_proc_qry=imw_query("SELECT DATE_FORMAT(exam_date,'%m-%d-%y %H:%i') AS exam_dat,chart_procedures.* from chart_procedures where 
										patient_id='".$this->patient."' 
										AND form_id ='".$this->form_id."'
										AND deleted_by=0
										ORDER BY exam_date desc");
		while($arr=imw_fetch_array($get_proc_qry)){
			$complication="No";
			$timeout="No";
			$site_marked="No";
			$pos_pros_implant="No";
			$consent_completed="No";
			$heart_attack="No";
			$timeProc="";
			$procedure_nam="";
			if($arr['complication']=="1"){$complication="Yes";}
			if($arr['timeout']=="1"){$timeout="Yes";}
			if($arr['site_marked']=="1"){$site_marked="Yes";}
			if($arr['pos_pros_implant']=="1"){$pos_pros_implant="Yes";}
			if($arr['consent_completed']=="1"){$consent_completed="Yes";}
			
			if($arr['proc_id']>0){$procedure_nam=$procedure_name_arr[$arr['proc_id']];}
			if($arr['corr_proc_id']>0){$timeProc=$procedure_name_arr[$arr['corr_proc_id']];}
			
			if($arr['heart_attack']>0){$heart_attack="Yes";}
			
			$arrReturn['procedures'][$j]['exam_id'] = $arr['id'];
			$arrReturn['procedures'][$j]['exam_date'] = $arr['exam_dat'];
			$arrReturn['procedures'][$j]['data']['proc_id'] = $arr['proc_id'];
			$arrReturn['procedures'][$j]['data']['procedure'] = $procedure_nam;
			$arrReturn['procedures'][$j]['data']['site'] = $arr['site'];
			$arrReturn['procedures'][$j]['data']['dxCode'] = $arr['dx_code'];
			$arrReturn['procedures'][$j]['data']['cptCode'] = $arr['cpt_code'];
			$arrReturn['procedures'][$j]['data']['mod'] = $arr['cpt_mod'];
			$arrReturn['procedures'][$j]['data']['startTime'] = $arr['start_time'];
			$arrReturn['procedures'][$j]['data']['endTime'] = $arr['end_time'];
			$arrReturn['procedures'][$j]['data']['postOP'] = $arr['iop_type'];
			$arrReturn['procedures'][$j]['data']['postOPOD'] = $arr['iop_od'];
			$arrReturn['procedures'][$j]['data']['postOPOS'] = $arr['iop_os'];
			$arrReturn['procedures'][$j]['data']['iopTime'] = $arr['iop_time'];
			$arrReturn['procedures'][$j]['data']['complication'] = $complication;
			$arrReturn['procedures'][$j]['data']['cmt'] = $arr['cmt'];
			$arrReturn['procedures'][$j]['data']['comm'] = $arr['comments'];
			$arrReturn['procedures'][$j]['data']['bp'] = $arr['comments'];
			$arrReturn['procedures'][$j]['data']['heartAttack'] = $heart_attack;
			$arrReturn['procedures'][$j]['data']['otherProcNote'] = $arr['otherProcNote'];
			
			$arrReturn['procedures'][$j]['data']['timeout'] = $timeout;
			$arrReturn['procedures'][$j]['data']['timeProc'] = $timeProc;
			$arrReturn['procedures'][$j]['data']['timeProcID'] = $arr['corr_proc_id'];
			$arrReturn['procedures'][$j]['data']['timeSite'] = $arr['corr_site'];
			$arrReturn['procedures'][$j]['data']['timeSiteMarked'] = $site_marked;
			$arrReturn['procedures'][$j]['data']['timePosition'] = $pos_pros_implant;
			$arrReturn['procedures'][$j]['data']['timeConsent'] = $consent_completed;
			$arrReturn['procedures'][$j]['data']['timeProviders'] = $arr['providers'];
			
			$arrReturn['procedures'][$j]['data']['consentFormId'] = $arr['consent_form_id'];
			$arrReturn['procedures'][$j]['data']['opReportId'] = $arr['op_report_id'];
			$arrReturn['procedures'][$j]['data']['orderId'] = $arr['order_id'];
			
			if($arr_med['med_type'])$arrReturn['procedures'][$j]['data'][$arr_med['med_type']] = array();
			$arrReturn['procedures'][$j]['data']['consent'] = array("id"=>"","name"=>"","data"=>"","tbl_pri_id"=>"");
			$arrReturn['procedures'][$j]['data']['opnote'] = array("id"=>"","name"=>"","data"=>"");
			$get_proc_med_qry=imw_query("SELECT * from chart_procedures_med_lot where chart_procedure_id='".$arr['id']."'");
			while($arr_med=imw_fetch_array($get_proc_med_qry)){
				$arrReturn['procedures'][$j]['data'][$arr_med['med_type']][] = array("med"=>$arr_med['med_name'],"lot"=>$arr_med['lot_number'],"qty"=>"1","tbl_pri_id"=>$arr_med['id']);
			}
			
			$get_proc_con_qry=imw_query("SELECT * from patient_consent_form_information where chart_procedure_id='".$arr['id']."'");
			while($arr_con=imw_fetch_array($get_proc_con_qry)){
				$arrReturn['procedures'][$j]['data']['consent']	= array("id"=>$arr_con['consent_form_id'],"name"=>$arr_con['consent_form_name'],"data"=>($arr_con['consent_form_content_data']<>""?stripslashes(html_entity_decode($arr_con['consent_form_content_data'])):""),"tbl_pri_id"=>$arr_con['form_information_id']);
			}
			
			$get_proc_pn_qry=imw_query("SELECT * from pn_reports where chart_procedure_id='".$arr['id']."'");
			while($arr_pn=imw_fetch_array($get_proc_pn_qry)){
				$op_note_name_arr[$arr_pn['tempId']] = ($op_note_name_arr[$arr_pn['tempId']] == NULL)?"":$op_note_name_arr[$arr_pn['tempId']];
				$arrReturn['procedures'][$j]['data']['opnote']	= array("id"=>$arr_pn['tempId'],"name"=>$op_note_name_arr[$arr_pn['tempId']],"data"=>($arr_pn['txt_data']<>""?stripslashes(html_entity_decode($arr_pn['txt_data'])):""),"tbl_pri_id"=>$arr_pn['pn_rep_id']);
			}
			$j++;
		}
		
		$arrReturn['patient_data'] = $this->get_patient_data();
		return $arrReturn;
	}
	function isHomogenous($arrmod) {
		$firstValue = ";";
		foreach ($arrmod as $val) {
			if ($firstValue !== $val) {
				return false;
			}
		}
		return true;
	}
	
	function all_Procedure(){
		$result_Set['procedure_Date']=array();
		$get_proc_qry=imw_query("SELECT id ,DATE_FORMAT(exam_date,'%m-%d-%y %H:%i') AS exam_date from chart_procedures where 
										patient_id='".$this->patient."' 
										AND form_id ='".$this->form_id."'
										AND deleted_by=0
										ORDER BY id DESC");
		
		while($result=imw_fetch_assoc($get_proc_qry)){
			$result_Set['procedure_Date'][]=$result;
		}
		$this->procedure_id=$result_Set['procedure_Date'][0]['id'];
		$result_Set['procedures']=$this->get_procedures_app();
		return $result_Set;
	}
	
	function get_procedures_app(){
	
		//include(dirname(__FILE__)."/../../library/classes/pnTempParser.php");
		$procedure_Obj=new procedures();
		$objParser=new PnTempParser();
		$arrReturn 	= array();
		$arrReturn['procedures_Detail'] = array();
		$arrReturn['procedure']=array();
		$arrReturn['procedure']=$procedure_Obj->get_procedureDetail();
		//print_r($arrReturn['procedure']);
		
		// below code is commented by Aqib //
		
		/*$arr_cosent=array();
		$get_mas_consent_qry1=imw_query("SELECT consent_form_id,consent_form_name from consent_form order by consent_form_name asc");
		while($get_mas_consent_arr1=imw_fetch_array($get_mas_consent_qry1)){
			$arr_cosent[$get_mas_consent_arr1['consent_form_id']]=$get_mas_consent_arr1['consent_form_name'];
		}
		
		$arr_op_report=array();
		$get_mas_op_report_qry1=imw_query("SELECT temp_name, temp_id from pn_template order by temp_name asc");
		while($get_mas_op_report_arr1=imw_fetch_array($get_mas_op_report_qry1)){
			$arr_op_report[$get_mas_op_report_arr1['temp_id']]=$get_mas_op_report_arr1['temp_name'];
		}
		
		$k=0;
		$arr_cpt_str=$arr_mod_str='';
		$arr_cpt_mod=array();
		$get_mas_proc_qry=imw_query("SELECT * from operative_procedures where del_status!=1 order by procedure_name asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			$procedure_name_arr[$get_mas_proc_arr['procedure_id']]=$get_mas_proc_arr['procedure_name'];
			$arrReturn['procedure'][$k]['name'] = $get_mas_proc_arr['procedure_name'];
			//$arrReturn['procedure'][$k]['type'] = '';
			$arrReturn['procedure'][$k]['id']   = $get_mas_proc_arr['procedure_id'];
			$arr_cpt_mod=unserialize(html_entity_decode(trim($get_mas_proc_arr['cpt_code'])));
			if(count($arr_cpt_mod)>0)
			{
				for($i=1;$i<=count($arr_cpt_mod);$i++)
				{
					if($arr_cpt_mod['cpt_code1_'.$i]){
						if(trim($arr_cpt_mod['cpt_code1_'.$i]))$arr_cpt_str.=$arr_cpt_mod['cpt_code1_'.$i]."; ";
						if(trim($arr_cpt_mod['mod_code1_'.$i]))$arr_mod_str.=$arr_cpt_mod['mod_code1_'.$i];
					}
					if($arr_cpt_mod['cpt_code2_'.$i]){
						if(trim($arr_cpt_mod['cpt_code2_'.$i]))$arr_cpt_str.=$arr_cpt_mod['cpt_code2_'.$i]."; ";
						if(trim($arr_cpt_mod['mod_code2_'.$i]))$arr_mod_str.=$arr_cpt_mod['mod_code2_'.$i];	
					}
					if($arr_cpt_mod['cpt_code3_'.$i]){
						if(trim($arr_cpt_mod['cpt_code3_'.$i]))$arr_cpt_str.=$arr_cpt_mod['cpt_code3_'.$i]."; ";
						if(trim($arr_cpt_mod['mod_code3_'.$i]))$arr_mod_str.=$arr_cpt_mod['mod_code3_'.$i];
					}
				}
			}
			$arrReturn['procedure'][$k]['cpt_code']=unserialize(html_entity_decode(trim($get_mas_proc_arr['cpt_code'])));
			$arrReturn['procedure'][$k]['cpt_code_str']=$arr_cpt_str;
			$arrReturn['procedure'][$k]['mod_code_str']=$arr_mod_str;
			$arrReturn['procedure'][$k]['dx_code']=$get_mas_proc_arr['dx_code'];
			$arrReturn['procedure'][$k]['time_out_request']=$get_mas_proc_arr['time_out_request'];
			
			unset($medArr,$medArrMAIN);
			foreach(explode('|',$get_mas_proc_arr['pre_op_meds']) as $singleMed)
			{
				$medArr['med'] = $singleMed;	
				$medArrMAIN[]=$medArr;
			}
			$arrReturn['procedure'][$k]['preop']=$medArrMAIN;
			
			unset($medArr,$medArrMAIN);
			foreach(explode('|',$get_mas_proc_arr['intraviteral_meds']) as $singleMed)
			{
				$medArr['med'] = $singleMed;
				$medArrMAIN[]=$medArr;	
			}
			$arrReturn['procedure'][$k]['intravitreal']=$medArrMAIN;
			
			unset($medArr,$medArrMAIN);
			foreach(explode('|',$get_mas_proc_arr['post_op_meds']) as $singleMed)
			{
				$medArr['med'] = $singleMed;
				$medArrMAIN[]=$medArr;	
			}
			
			$arrReturn['procedure'][$k]['postop']=$medArrMAIN;
			
			$arrReturn['procedure'][$k]['consent_form_id']=$get_mas_proc_arr['consent_form_id'];
			$arrReturn['procedure'][$k]['consent_form_name']=$arr_cosent[$get_mas_proc_arr['consent_form_id']];
			$arrReturn['procedure'][$k]['op_report_id']=$get_mas_proc_arr['op_report_id'];
			$arrReturn['procedure'][$k]['op_report_name']=$arr_op_report[$get_mas_proc_arr['op_report_id']];
			
			$k++;
		}
		
		$k=0;
		$get_mas_proc_qry=imw_query("SELECT * from diagnosis_code_tbl order by d_prac_code asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			//$arrReturn['dx'][$k]['name'] = $get_mas_proc_arr['d_prac_code'];
			$arrReturn['dx']['name'][$k] = $get_mas_proc_arr['d_prac_code'];
			$k++;
		}
		
		$k=0;
		$get_mas_proc_qry=imw_query("SELECT * from cpt_fee_tbl order by cpt_prac_code asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			$arrReturn['cpt'][$k]['name'] = $get_mas_proc_arr['cpt_prac_code'];
			$k++;
		}
		
		$k=0;
		$get_mas_proc_qry=imw_query("SELECT * from modifiers_tbl order by mod_prac_code asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			$arrReturn['mod'][$k]['name'] = $get_mas_proc_arr['mod_prac_code'];
			$k++;
		}
		*/
		if(constant("connect_optical")==1){
			$get_item_qry=imw_query("SELECT id,qty_on_hand from in_item where module_type_id='6' order by name asc");
			while($get_item_arr=imw_fetch_array($get_item_qry)){
				$item_arr[$get_item_arr['id']] = $get_item_arr['qty_on_hand'];
			}
			
			$get_item_lot_qry=imw_query("SELECT item_id,lot_no from in_item_lot_total order by lot_no asc");
			while($get_item_lot_arr=imw_fetch_array($get_item_lot_qry)){
				$item_lot_arr[$get_item_lot_arr['item_id']][] = $get_item_lot_arr['lot_no'];
			}
		}
		
		$k=0;
		$get_mas_proc_qry=imw_query("SELECT * from medicine_data order by medicine_name asc");
		while($get_mas_proc_arr=imw_fetch_array($get_mas_proc_qry)){
			$arrReturn['med'][$k]['name'] = $get_mas_proc_arr['medicine_name'];
			$arrReturn['med'][$k]['opt_med_id'] = $get_mas_proc_arr['opt_med_id'];
			$qty_on_hand="";
			$item_lot_val="";
			if($get_mas_proc_arr['opt_med_id']>0 && constant("connect_optical")==1){
				$qty_on_hand= $item_arr[$get_mas_proc_arr['opt_med_id']];
				$item_lot_val= $item_lot_arr[$get_mas_proc_arr['opt_med_id']];
			}
			
			$arrReturn['med'][$k]['qty_on_hand'] = $qty_on_hand;
			$arrReturn['med'][$k]['lot_no'] =  $item_lot_val;
			
			$k++;
		}
			
		$ik=0;$arrReturn['consent']=array();
		$get_mas_consent_qry=imw_query("SELECT * from consent_form where consent_form_status = 'Active' Order by consent_form_name asc");
		require_once(dirname(__FILE__)."/print_all_consent_form_app.php");
		while($get_mas_consent_arr=imw_fetch_array($get_mas_consent_qry)){
			$arrReturn['consent'][$ik]['name'] = $get_mas_consent_arr['consent_form_name'];
			//	$_REQUEST['print_false']=1;
			$_REQUEST['patient_id']=$this->patient;
			$_REQUEST['form_id']=$this->form_id;
			$_REQUEST['consent_id']=$get_mas_consent_arr['consent_form_id'];
			
			$consent_form_content=get_consent();
			$arrReturn['consent'][$ik]['data'] = stripslashes(html_entity_decode($consent_form_content));
			$arrReturn['consent'][$ik]['id']   = $get_mas_consent_arr['consent_form_id'];
			$ik++;
		}
		
		$k=0;$arrReturn['opnote']=array();
		$get_mas_pn_qry=imw_query("SELECT * from pn_template order by temp_name asc");
		while($get_mas_pn_arr=imw_fetch_array($get_mas_pn_qry)){
			$arrReturn['opnote'][$k]['name'] = $get_mas_pn_arr['temp_name'];
			$arrReturn['opnote'][$k]['data'] = stripslashes(html_entity_decode($objParser->getDataParsed($get_mas_pn_arr['temp_data'],$this->patient,$this->form_id,'','','','','','','procedures')));
			$arrReturn['opnote'][$k]['id']   = $get_mas_pn_arr['temp_id'];
			$op_note_name_arr[$get_mas_pn_arr['temp_id']]=$get_mas_pn_arr['temp_name'];
			$k++;
		}
		
		if(isset($_REQUEST['procedure_id'])){
			$this->procedure_id=$_REQUEST['procedure_id'];
		}
		
		$get_proc_qry=imw_query("SELECT DATE_FORMAT(exam_date,'%m-%d-%y %H:%i') AS exam_dat,chart_procedures.* from chart_procedures 
									where patient_id='".$this->patient."' 
										AND form_id ='".$this->form_id."'
										AND id ='".$this->procedure_id."'
										AND deleted_by=0
										ORDER BY exam_date desc");
									
		$arr=imw_fetch_assoc($get_proc_qry);
		
		if($arr!=false){
			
		
			$complication="No";
			$timeout="No";
			$site_marked="No";
			$pos_pros_implant="No";
			$consent_completed="No";
			$heart_attack="No";
			$timeProc="";
			$procedure_nam="";
			if($arr['complication']=="1"){$complication="Yes";}
			if($arr['timeout']=="1"){$timeout="Yes";}
			if($arr['site_marked']=="1"){$site_marked="Yes";}
			if($arr['pos_pros_implant']=="1"){$pos_pros_implant="Yes";}
			if($arr['consent_completed']=="1"){$consent_completed="Yes";}
			
			$pro_qry="select procedure_name from operative_procedures where del_status!=1 and procedure_id='".$arr['proc_id']."'";
			$result_pro=imw_query($pro_qry);
			$fetch_pro=imw_fetch_assoc($result_pro);
			$pro_qry_1="select procedure_name from operative_procedures where del_status!=1 and procedure_id='".$arr['corr_proc_id']."'";
			$result_pro=imw_query($pro_qry_1);
			$fetch_pro_1=imw_fetch_assoc($result_pro);
			
			
			if($arr['proc_id']>0){$procedure_nam=$fetch_pro['procedure_name'];}
			if($arr['corr_proc_id']>0){$timeProc=$fetch_pro_1['procedure_name'];}
			
			if($arr['heart_attack']>0){$heart_attack="Yes";}
			//$arrReturn['procedure'][$j]['preop']=$medArrMAIN;
			$arrReturn['procedures_Detail']['exam_id'] = $arr['id'];
			$arrReturn['procedures_Detail']['exam_date'] = $arr['exam_dat'];
			$arrReturn['procedures_Detail']['data']['proc_id'] = $arr['proc_id'];
			$arrReturn['procedures_Detail']['data']['procedure'] = $procedure_nam;
			$arrReturn['procedures_Detail']['data']['site'] = $arr['site'];
			
			$arrReturn['procedures_Detail']['data']['lids_opt'] = $arr['lids_opts'];
			$lids_arr=explode(",",$arr['lids_opts']);
			$arr_lids=array("RUL"=>"","RLL"=>"","LUL"=>"","LLL"=>"");
			if(trim($arr['lids_opts'])!=""){
				foreach($lids_arr as $value){
					$arr_lids[$value]=$value;
				}	
			}
			$arrReturn['procedures_Detail']['data']['lids_opts'] = $arr_lids;
			
			$arrReturn['procedures_Detail']['data']['dxCode'] = $arr['dx_code'];
			$arr_dx=explode(";",$arr['dx_code']);
			$arr_dx_code=array();
			if(!empty($arr['dx_code'])){
				foreach($arr_dx as $value){
					$arr_dx_code[]["dx_code"]=$value.';';
				}
			}
			$arrReturn['procedures_Detail']['data']['dxCodes'] = $arr_dx_code;
			
			$arrReturn['procedures_Detail']['data']['cptCode'] = $arr['cpt_code'];
			
			$arr_cpt=explode("|~|",$arr['cpt_code']);
			$arr_cpt_code=array();
			if(!empty($arr['cpt_code'])){
				foreach($arr_cpt as $value){
					$arr_cpt_code[]["cpt_code"]=$value;
				}
			}
			//$arrReturn['procedures_Detail']['data']['cptCodes'] = $arr_cpt_code;
			
			$arrReturn['procedures_Detail']['data']['mod'] = $arr['cpt_mod'];
			
			$arr_mod=explode("|~|",$arr['cpt_mod']);
			$arr_mod_code=array();
			if(!empty($arr['cpt_mod']) && $procedure_Obj->isHomogenous($arr_mod)==false){
				foreach($arr_mod as $value){
					$value=str_replace(";","",$value);
					$arr_mod_code[]["mod_code"]=$value;
				}
			}
			$arr_cpt_mod_code=array();
			for($i=0;$i<count($arr_cpt_code);$i++){
				$arr_cpt_mod_code[$i]['cpt_desc']=$arr_cpt_code[$i]['cpt_code'];
				$arr_cpt_mod_code[$i]['mod_desc']=$arr_mod_code[$i]['mod_code'];
			}
			
			//$arrReturn['procedures_Detail']['data']['mods'] = $arr_mod_code;
			$arrReturn['procedures_Detail']['data']['cpt_mode_desc'] = $arr_cpt_mod_code;
			$arrReturn['procedures_Detail']['data']['startTime'] = $arr['start_time'];
			$arrReturn['procedures_Detail']['data']['endTime'] = $arr['end_time'];
			$arrReturn['procedures_Detail']['data']['postOP'] = $arr['iop_type'];
			$arrReturn['procedures_Detail']['data']['postOPOD'] = $arr['iop_od'];
			$arrReturn['procedures_Detail']['data']['postOPOS'] = $arr['iop_os'];
			$arrReturn['procedures_Detail']['data']['iopTime'] = $arr['iop_time'];
			$arrReturn['procedures_Detail']['data']['complication'] = $complication;
			$arrReturn['procedures_Detail']['data']['cmt'] = $arr['cmt'];
			$arrReturn['procedures_Detail']['data']['comm'] = $arr['comments'];
			$arrReturn['procedures_Detail']['data']['bp'] = $arr['bp'];
			$arrReturn['procedures_Detail']['data']['heartAttack'] = $heart_attack;
			$arrReturn['procedures_Detail']['data']['otherProcNote'] = $arr['otherProcNote'];
			
			$arrReturn['procedures_Detail']['data']['timeout'] = $timeout;
			$arrReturn['procedures_Detail']['data']['timeProc'] = $timeProc;
			$arrReturn['procedures_Detail']['data']['timeProcID'] = $arr['corr_proc_id'];
			$arrReturn['procedures_Detail']['data']['timeSite'] = $arr['corr_site'];
			
			
			$arrReturn['procedures_Detail']['data']['timeLids'] = $arr['cor_lids_opts'];
			$cor_lids_arr=explode(",",$arr['cor_lids_opts']);
			$cor_arr_lids=array("RUL"=>"","RLL"=>"","LUL"=>"","LLL"=>"");
			if(trim($arr['cor_lids_opts'])!=""){
				foreach($cor_lids_arr as $value){
					$cor_arr_lids[$value]=$value;
				}	
			}
			$arrReturn['procedures_Detail']['data']['cor_lids_opts'] = $cor_arr_lids;
				
			$arrReturn['procedures_Detail']['data']['timeSiteMarked'] = $site_marked;
			$arrReturn['procedures_Detail']['data']['timePosition'] = $pos_pros_implant;
			$arrReturn['procedures_Detail']['data']['timeConsent'] = $consent_completed;
			$arrReturn['procedures_Detail']['data']['timeProvidersID'] = $arr['user_id'];
			$arrReturn['procedures_Detail']['data']['timeProviders'] = $arr['providers'];
			
			$arrReturn['procedures_Detail']['data']['consentFormId'] = $arr['consent_form_id'];
			$arrReturn['procedures_Detail']['data']['opReportId'] = $arr['op_report_id'];
			$arrReturn['procedures_Detail']['data']['orderId'] = $arr['user_id'];
			
			if($arr_med['med_type'])$arrReturn['procedures']['data'][$arr_med['med_type']] = array();
			$arrReturn['procedures_Detail']['data']['consent'] = array("id"=>"","name"=>"","data"=>"","tbl_pri_id"=>"");
			$arrReturn['procedures_Detail']['data']['opnote'] = array("id"=>"","name"=>"","data"=>"","tbl_pri_id"=>"");
			$get_proc_med_qry=imw_query("SELECT * from chart_procedures_med_lot where chart_procedure_id='".$arr['id']."'");
				
			$arrReturn['procedures_Detail']['data']['preop']=array();
			$arrReturn['procedures_Detail']['data']['intravitreal']=array();
			$arrReturn['procedures_Detail']['data']['postop']=array();
				
			while($arr_med=imw_fetch_array($get_proc_med_qry)){
				$arrReturn['procedures_Detail']['data'][$arr_med['med_type']][] = array("med"=>$arr_med['med_name'],"lot"=>$arr_med['lot_number'],"qty"=>"1","tbl_pri_id"=>$arr_med['id']);
			}
			
			if(preg_match("/\bbotox\b/i",$fetch_pro['procedure_name'])){
				$execute_query=imw_query("SELECT * from chart_procedures_botox where chart_proc_id='".$arr['id']."'");
				$result=imw_fetch_assoc($execute_query);
				//if($result['drw_path'] !=""){
				//	$arrReturn['procedures_Detail']['data']['drw_path']=$_SERVER['HTTP_HOST'].'/R6-Dev/interface/main/uploaddir'.$result['drw_path'];
				//}
				
				$arrReturn['procedures_Detail']['data']['drw_path']=$GLOBALS['php_server'].'/interface/chart_notes/iDoc-Drawing/images/face_bottox_v2.jpg';
				$arrReturn['procedures_Detail']['data']['btx_total']=$result['btx_total'];
				$arrReturn['procedures_Detail']['data']['btx_used']=$result['btx_usd'];
				$arrReturn['procedures_Detail']['data']['btx_wasted']=$result['btx_wstd'];
				$arrReturn['procedures_Detail']['data']['lot']=$result['lot'];
				$arrReturn['procedures_Detail']['data']['vis_sc_od']=$result['vis_sc_od'];
				$arrReturn['procedures_Detail']['data']['vis_cc_od']=$result['vis_cc_od'];
				$arrReturn['procedures_Detail']['data']['vis_other_od']=$result['vis_othr_od'];
				$arrReturn['procedures_Detail']['data']['vis_sc_os']=$result['vis_sc_os'];
				$arrReturn['procedures_Detail']['data']['vis_cc_os']=$result['vis_cc_os'];
				$arrReturn['procedures_Detail']['data']['vis_othr_os']=$result['vis_othr_os'];
				$arrReturn['procedures_Detail']['data']['rd_injection']=$result['rd_injctn'];
				if($result['rbdcs'] == "R&B Discussed, Consent signed"){
					$arrReturn['procedures_Detail']['data']['rbdcs']= "1";
				}
				else{
					$arrReturn['procedures_Detail']['data']['rbdcs'] = "0";
				}
				$arrReturn['procedures_Detail']['data']['type_btx']=$result['type_btx'];
				$arrReturn['procedures_Detail']['data']['vis_othr_os']=$result['vis_othr_os'];
				$arrReturn['procedures_Detail']['data']['drw_coords'] = json_decode($result['drw_coords'],true);
				
				$query = imw_query("select * from botox_dosages");
				while($result = imw_fetch_assoc($query)){
					
					$arrReturn['procedures_Detail']['data']['units'][]['value'] = $result['bdos'];
					
					
				//echo $result['drw_coords'];
				//var_dump($var1);
				//die();
				}
			}
			else{
					$arrReturn['procedures_Detail']['data']['units'] = array();
			}
			$get_proc_con_qry=imw_query("SELECT * from patient_consent_form_information where chart_procedure_id='".$arr['id']."'");
			while($arr_con=imw_fetch_assoc($get_proc_con_qry)){
				$arrReturn['procedures_Detail']['data']['consent']	= array("id"=>$arr_con['consent_form_id'],"name"=>$arr_con['consent_form_name'],"data"=>stripslashes(html_entity_decode($arr_con['consent_form_content_data'])),"tbl_pri_id"=>$arr_con['form_information_id']);
			}
			
			$get_proc_pn_qry=imw_query("SELECT * from pn_reports where chart_procedure_id='".$arr['id']."'");
			while($arr_pn=imw_fetch_array($get_proc_pn_qry)){
				$op_note_name_arr[$arr_pn['tempId']] = ($op_note_name_arr[$arr_pn['tempId']] == NULL)?"":$op_note_name_arr[$arr_pn['tempId']];
				$arrReturn['procedures_Detail']['data']['opnote']	= array("id"=>$arr_pn['tempId'],"name"=>$op_note_name_arr[$arr_pn['tempId']],"data"=>stripslashes(html_entity_decode($arr_pn['txt_data'])),"tbl_pri_id"=>$arr_pn['pn_rep_id']);
			}
		}
		
		$arrReturn['patient_data'] = $this->get_patient_data();
		return $arrReturn;
	}
	
	/*function consent(){
	//error_reporting(E_ALL);
		$get_mas_consent_qry=imw_query("SELECT consent_form_id,consent_form_name,consent_form_content from consent_form where consent_form_status = 'Active' Order by consent_form_name asc");
		$i=0;
		require_once(dirname(__FILE__)."/../../interface/patient_info/consent_forms/print_all_consent_form_app.php");
		while($result=imw_fetch_assoc($get_mas_consent_qry)){
			$final[$i]['id']=$result['consent_form_id'];
			$final[$i]['name']=$result['consent_form_name'];
			$_REQUEST['patient_id']=$this->patient;
			$_REQUEST['form_id']=$this->form_id;
			$_REQUEST['consent_id']=$result['consent_form_id'];
			
			$consent_form_content=get_consent();
			$final[$i]['content']=stripslashes(html_entity_decode($consent_form_content));
			$i++;
		}
		return $final;
	}*/
	
	function print_getData($val){
		
		$getDetailsStr = "SELECT fname,mname,lname FROM users WHERE id = '$val'";
		$getDetailsQry = imw_query($getDetailsStr);
		$getDetailsRow = imw_fetch_array($getDetailsQry);
		$name_str=$getDetailsRow['fname']." ".$getDetailsRow['mname']." ".$getDetailsRow['lname'];
		return $name_str;
	}
	
	function get_procedureDetail()
	{	
		$qq="select * from operative_procedures where del_status!=1 order by procedure_name asc";
		$getPro=imw_query($qq)or die(imw_error());
		
			while($setPro=imw_fetch_object($getPro)){
				$cpt=unserialize(html_entity_decode($setPro->cpt_code));
				$proDetail['proc_id']=$setPro->procedure_id;
				$proDetail['proc_name']=$setPro->procedure_name;
				$proDetail['cpt_code']=implode(';',$cpt);
				//$proDetail['cpt_code_mode']=$cpt;
				$proDetail["cpt_mode_desc"]=array();
				$proDetail['dx_code']=$setPro->dx_code;
				$arr_dx=explode(";",$setPro->dx_code);
				$dx_codes=array();
				if($setPro->dx_code!="" && !empty($setPro->dx_code)){
					foreach($arr_dx as $value){
						if(trim($value)!=""){
							$dx_codes[]['dx_code']=trim($value);
						}
					}
				}
				$proDetail['dx_codes']=$dx_codes;
				$proDetail['image']=$GLOBALS['php_server'].'/interface/chart_notes/iDoc-Drawing/images/face_bottox_v2.jpg';
				$proDetail['time_out_request']=$setPro->time_out_request;
				$pre_op_meds=explode('|',$setPro->pre_op_meds);
				$intraviteral_meds=explode('|',$setPro->intraviteral_meds);
				$post_op_meds=explode('|',$setPro->post_op_meds);
				$proDetail['consent_form_id']=$setPro->consent_form_id;
				$consent=array();
				if(!empty($proDetail['consent_form_id']) && $proDetail['consent_form_id']!=""){
					$arr_Consent=explode(",",$proDetail['consent_form_id']);
					$i=0;
					foreach($arr_Consent as $value){
						
						$get_Consent=imw_query("SELECT consent_form_id,consent_form_name from consent_form where consent_form_id ='".$value."'");
						$consent_result=imw_fetch_assoc($get_Consent);
						$consent[$i]['form_id']=$value;
						$consent[$i]['form_name']=$consent_result['consent_form_name'];
						$i++;
					}
				}
				$proDetail['consent_form']=$consent;
				$proDetail['op_report_id']=$setPro->op_report_id;
				
				$op_report=array();
				if(!empty($proDetail['op_report_id']) && $proDetail['op_report_id']!=""){
					$arr_Op=explode(",",$proDetail['op_report_id']);
					$i=0;
					foreach($arr_Op as $value){
						$get_Op=imw_query("SELECT temp_name, temp_id from pn_template where temp_id='".$value."'");
						$op_report_result=imw_fetch_assoc($get_Op);
						$op_report[$i]['form_id']=$value;
						$op_report[$i]['form_name']=$op_report_result['temp_name'];
						$i++;
					}
				}
				$proDetail['op_report']=$op_report;
				$proDetail['operator_id']=$setPro->operator_id;
				$proDetail['operator_name']=$this->print_getData($setPro->operator_id);
				$proDetail['ret_inj']=$setPro->ret_inj;
				$proDetail['ret_gl']=$setPro->ret_gl;
				$proDetail['pre_op_meds']=array();
				$proDetail['intraviteral_meds']=array();
				$proDetail['post_op_meds']=array();
				
				if(!empty($pre_op_meds[0])){
					foreach($pre_op_meds as $meds){
						$proDetail['pre_op_meds'][]['meds']=str_replace("\r","",$meds);
					}
				}
				if(!empty($intraviteral_meds[0])){
					foreach($intraviteral_meds as $meds){
						$proDetail['intraviteral_meds'][]['meds']=str_replace("\r","",$meds);
					}
				}
				if(!empty($post_op_meds[0])){
					foreach($post_op_meds as $meds){
						$proDetail['post_op_meds'][]['meds']=str_replace("\r","",$meds);
					}
				}
				$i=1;$k=0;
				foreach($cpt as $cpt_code){
					$j=1;
					foreach($cpt as $key=>$cpt_code){
						$qry="select cpt_desc from cpt_fee_tbl where cpt_prac_code = '".$cpt_code."' order by cpt_fee_id desc";
						$result_set=imw_query($qry);
						$cpt_codes=imw_fetch_array($result_set);
						if(preg_match("/cpt_code".$j."_".$i."/", $key)){
							$proDetail["cpt_mode_desc"][$k]['cpt_desc']=$cpt["cpt_code".$j."_".$i];
							$proDetail["cpt_mode_desc"][$k]['mod_desc']=$cpt["mod_code".$j."_".$i];
							$j++;$k++;
						}
					}	
					$i++;
				}
				$proDetail['units'] = array();
				if($proDetail['proc_name'] == 'Botox'){
					$query = imw_query("select * from botox_dosages");
					while($result = imw_fetch_assoc($query)){
						$proDetail['units'][]['value'] = $result['bdos']; 
						 
					}
				
				}
				else{
					$proDetail['units'] = array();
				}
				
				$arrReturn[]=$proDetail;	
			}
		return $arrReturn;
		
                                  /*[proc_id] => 3
								    [procedure] => Chalazion
                                    [site] => OD
                                    [dxCode] => 333.81
                                    [cptCode] => 2019F
                                    [mod] => 3
                                    [startTime] => 
                                    [endTime] => 
                                    [postOP] => 
                                    [postOPOD] => 
                                    [postOPOS] => 
                                    [iopTime] => 
                                    [complication] => Yes
                                    [cmt] => g
                                    [comm] => 
                                    [bp] => 
                                    [heartAttack] => No
                                    [otherProcNote] => 
                                    [timeout] => No
                                    [timeProc] => 
                                    [timeProcID] => 0
                                    [timeSite] => 
                                    [timeSiteMarked] => No
                                    [timePosition] => No
                                    [timeConsent] => No
                                    [timeProviders] => 
                                    [consentFormId] => 0
                                    [opReportId] => 116
                                    [orderId] => 0
                                    [consent] => Array
                                        (
                                            [id] => 
                                            [name] => 
                                            [data] => 
                                            [tbl_pri_id] => 
                                        )

                                    [opnote] => Array
                                        (
                                            [id] => 0
                                            [name] => 
                                            [data] => 
                                            [tbl_pri_id] => 116
                                        )*/
		
		//$getPro.close;
	}
	
	function save_procedure(){
                $handle = fopen("php://input",'r');
                $arrData = fgets($handle);
                $arr = json_decode($arrData);
                foreach($arr as $data){
                        $complication="0";
			$timeout="0";
			$site_marked="0";
			$pos_pros_implant="0";
			$consent_completed="0";
			$heart_attack="0";
			
			$id = $data->exam_id;
			
			$patient_id = $data->patient_id;
			$form_id = $data->form_id;
			$bp = $data->bp;
			$heart_attack = $data->heartAttack;
			
			$comments = $data->comm;
			$proc_id = $data->proc_id;
			$site = $data->site;
			$dx_code = $data->dxCode;
			$start_time = $data->startTime;
			$end_time = $data->endTime;
			$iop_type = $data->postOP;
			$iop_od = $data->postOPOD;
			$iop_os = $data->postOPOS;
			$iop_time = $data->timeSite;
			//$complication_desc = $data->complication_desc;
			
			$corr_proc_name = $data->timeProc;
			$corr_proc_id = $data->timeProcID;
			$corr_site = $data->timeSite;
			$providers = $data->timeProviders;
			$consent_form_id = $data->consentFormId;
			$op_report_id = $data->opReportId;
			$user_id = $data->phyId;
			
			$otherProcNote = $data->otherProcNote;
			
			//$hidd_hold_to_physician = $data->heartAttack;
			
			$cmt = $data->cmt;
			
			//$finalized_status = $data->heartAttack;
			//$proc_note_masterId = $data->heartAttack;
			
			$order_id = $data->orderId;
			$cpt_code = $data->cptCode;
			$cpt_mod = $data->mod;
			$operator_id=$data->phyId;
			
			$preop_arr=array();
			$intravitreal_arr=array();
			$postop_arr=array();
			$med_data=array();
			
			$i = 0;
			foreach($data->preop as $preopmeds){
				//echo $postMeds->med."<br>";
				$preop_arr[]=$preopmeds->med;
				$med_data['preop'][$i]['id']=$preopmeds->med_lot_id;
				$med_data['preop'][$i]['med_name']=$preopmeds->med;
				$med_data['preop'][$i]['lot_number']=$preopmeds->lot;
				$med_data['preop'][$i]['tbl_pri_id']=$preopmeds->tbl_pri_id;
				$i++;
			}
			$i = 0;
			foreach($data->intravitreal as $intravitrealmeds){
				//echo $postMeds->med."<br>";
				$intravitreal_arr[]=$intravitrealmeds->med;
				$med_data['intravitreal'][$i]['id']=$intravitrealmeds->med_lot_id;
				$med_data['intravitreal'][$i]['med_name']=$intravitrealmeds->med;
				$med_data['intravitreal'][$i]['lot_number']=$intravitrealmeds->lot;
				$med_data['intravitreal'][$i]['tbl_pri_id']=$intravitrealmeds->tbl_pri_id;
				$i++;
			}
			$i = 0;
			foreach($data->postop as $postMeds){
				//echo $postMeds->med."<br>";
				$postop_arr[]=$postMeds->med;
				$med_data['postop'][$i]['id']=$postMeds->med_lot_id;
				$med_data['postop'][$i]['med_name']=$postMeds->med;
				$med_data['postop'][$i]['lot_number']=$postMeds->lot;
				$med_data['postop'][$i]['tbl_pri_id']=$postMeds->tbl_pri_id;
				$i++;
			}
			
			$pre_op_meds = implode('|~|',$preop_arr);
			$intravit_meds = implode('|~|',$intravitreal_arr);
			$post_op_meds = implode('|~|',$postop_arr);
			if($data->exam_id>0){
				$qry="update ";
				$qry_whr=" where id='$id'";
				$other_qry="";
			}else{
				$qry="insert into ";
				$qry_whr="";
				$other_qry=",exam_date = '".date('Y-m-d H:i:s')."'";
			}
			
			if($data->complication=="YES"){$complication="1";}
			if($data->timeout=="YES"){$timeout="1";}
			if($data->timeSiteMarked=="YES"){$site_marked="1";}
			if($data->timePosition=="YES"){$pos_pros_implant="1";}
			if($data->timeConsent=="YES"){$consent_completed="1";}
			if($data->heartAttack=="YES"){$heart_attack="1";}
			
			/*bp='$bp',heart_attack='$heart_attack',*/
			$ins_qry="$qry chart_procedures set patient_id='$patient_id',form_id='$form_id',
			comments='$comments',proc_id='$proc_id',site='$site',dx_code='$dx_code',start_time='$start_time',end_time='$end_time',iop_type='$iop_type',iop_od='$iop_od',iop_os='$iop_os',
			iop_time='$iop_time',complication='$complication',pre_op_meds='$pre_op_meds',intravit_meds='$intravit_meds',post_op_meds='$post_op_meds',
			timeout='$timeout',corr_proc_id='$corr_proc_id',corr_site='$corr_site',site_marked='$site_marked',pos_pros_implant='$pos_pros_implant',
			consent_completed='$consent_completed',providers='$providers',consent_form_id='$consent_form_id',op_report_id='$op_report_id',user_id='$user_id',
			otherProcNote='$otherProcNote',cmt='$cmt',cpt_code='$cpt_code',cpt_mod='$cpt_mod' $other_qry $qry_whr";
			imw_query($ins_qry);
			
			if($data->exam_id>0){
				$chart_procedure_id=$data->exam_id;
			}else{
				$chart_procedure_id=imw_insert_id();
			}
			foreach($med_data as $key=>$arrType){
				foreach($arrType as $index=>$arrIndex){
					$tbl_pri_id = $arrIndex['tbl_pri_id'];
					$med_name 	= $arrIndex['med_name'];
					$lot_number	= $arrIndex['lot_number'];
					$med_type=$key;
					if($tbl_pri_id >0){
						$act_qry="update ";
						$med_qry_whr=" where id='$tbl_pri_id'";
					}else{
						$act_qry="insert into ";
						$med_qry_whr="";
					}
					$med_qry=" $act_qry chart_procedures_med_lot set med_name='$med_name',med_type='$med_type',chart_procedure_id='$chart_procedure_id',lot_number='$lot_number' $med_qry_whr";
					imw_query($med_qry);
					$j++;
				}
			}
			//pre($data->consent);
				$id		=	$data->consent->id;
				$name	=	$data->consent->name;
				//$data_cont	=	addslashes(htmlentities($data->consent->data));
				//get consent data from _app table
				$todayDate=explode('-',date('Y-m-d'));
				//check do we have saved form for that date
				$qry_ch="select * from patient_consent_form_information_app where patient_id='$patient_id' and consent_form_id='$id' and YEAR(form_created_date)='".$todayDate[0]."' and MONTH(form_created_date)='".$todayDate[1]."' and DAY(form_created_date)='".$todayDate[2]."'";
				$dataConForm=imw_query($qry_ch);
				if(imw_num_rows($dataConForm)>=1)
				{
					$con_temp_data=imw_fetch_object($dataConForm);
					$data_cont	= stripcslashes(html_entity_decode(trim($con_temp_data->consent_form_content_data)));
					$_REQUEST['phyId']=$operator_id;
					
					//remove javascript	 		
					$htmlArr=explode("<endofcode></endofcode>",$data_cont);	
					//remove smart tag links
					$htmlArr[0] = preg_replace('/<a id=\"(.*?)\" class=\"(.*?)\" href=\"(.*?)\">(.*?)<\/a>/', "\\4", $htmlArr[0]);
					$htmlArr[0] = preg_replace('/align=\"center\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">/', "align=\"left\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">", $htmlArr[0]);
					$htmlArr[0] = preg_replace('/<textarea class=\"manageUserInput\" rows=\"2\" cols=\"90\" name=\"(.*?)\" id=\"(.*?)\">/', "", $htmlArr[0]);
					$htmlArr[0] = preg_replace('/<\/textarea>/', "", $htmlArr[0]);
					//replace text boxes
					$htmlArr[0] = preg_replace('/<input class=\"manageUserInput\" type=\"text\" name=\"(.*?)\" id=\"(.*?)\" value=\"(.*?)\" size=\"(.*?)\" maxlength=\"(.*?)\" autocomplete=\"off\">/', "", $htmlArr[0]);
					//set display block for hidden user input values
					$htmlArr[0] = preg_replace('/<span id=\"(.*?)\" style=\"display:none\">(.*?)<\/span>/', "\\2", $htmlArr[0]);
					
					//replace any unsigned image
					//$data_cont=$this->removeTempImg($htmlArr[0],$con_temp_data->patient_signature);
					//remove text input box
					$data_cont=str_ireplace("height:20px; width:200px; min-width:200px; border:#999 1px solid;","",$data_cont);
					//remove textarea box
					$data_cont=str_ireplace("height:70px; width:300px; min-width:300px; border:#999 1px solid;","",$data_cont);
					//remove signature border
					$data_cont=str_ireplace('style="border:solid 1px;" bordercolor="#FF9900"',"",$data_cont);
					$data_cont=str_ireplace("HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;border:solid 1px; border-color:#FF9900;","",$data_cont);
					
					$tbl_pri_id = $data->consent->tbl_pri_id;
					
					$form_created_date=date('Y-m-d H:i:s');
					$modified_form_created_date=date('Y-m-d H:i:s');
					if($tbl_pri_id>0){
						$qry="update ";
						$qry_whr=" where form_information_id='$tbl_pri_id'";
						$qry_other=",modified_operator_id='".$operator_id."',modified_form_created_date='".$modified_form_created_date."'";
					}else{
						$qry="insert into ";
						$qry_whr="";
						$qry_other=",operator_id='".$operator_id."',form_created_date='".$form_created_date."'";
					}
					$con_qry=" $qry patient_consent_form_information set consent_form_id='$id',
					consent_form_name='$name',
					patient_id='$patient_id',
					consent_form_content_data='".htmlentities(imw_real_escape_string(trim($data_cont)))."',chart_procedure_id='$chart_procedure_id' $qry_other $qry_whr";
					imw_query($con_qry);
					
					//delete temp data
					if($con_temp_data->form_information_id)imw_query("delete from patient_consent_form_information_app where form_information_id=$con_temp_data->form_information_id");
				}
				if($id>0){
					$consent_form_id=$id;
				}else{
					$consent_form_id=imw_insert_id();
				}
				imw_query("update chart_procedures set consent_form_id='$consent_form_id' where id='$chart_procedure_id'");
				
				$id=$data->opnote->id;
				$temp_name=$data->opnote->name;
				$data_cont=addslashes($data->opnote->data);
				$tbl_pri_id = $data->opnote->tbl_pri_id;
				$operator_id="";
				$pn_rep_date=date('Y-m-d H:i:s');
				if($tbl_pri_id>0){
					$qry="update ";
					$qry_whr=" where pn_rep_id='$tbl_pri_id'";
				}else{
					$qry="insert into ";
					$qry_whr="";
				}
				$con_qry=" $qry pn_reports set patient_id='$patient_id',form_id='$form_id',txt_data='$data_cont',tempId='$id',opid='$operator_id',pn_rep_date='$pn_rep_date',chart_procedure_id='$chart_procedure_id' $qry_whr";
				imw_query($con_qry);
								
				if($id>0){
					$op_report_id=$id;
				}else{
					$op_report_id=imw_insert_id();
				}
				imw_query("update chart_procedures set op_report_id='$op_report_id' where id='$chart_procedure_id'");
			
		}
		return "1";
	}
	public function removeTempImg($dataContent,$sigStr)
	{
		//check do we have blank image in destination folder
		$sigArr=unserialize(stripcslashes($sigStr));
		$consent_form_content=$dataContent;	
		if(sizeof($sigArr)>=1)//if we have unsigned images then move remaing file to final folder
		{
			global $webServerRootDirectoryName,$web_RootDirectoryName; 
			foreach($sigArr as $key=>$val)
			{		
				/*$replace=$webServerRootDirectoryName.$web_RootDirectoryName."/interface/common/new_html2pdf/app/".$key.".jpeg";
				$target=$webServerRootDirectoryName.$web_RootDirectoryName."/app_services/signature/tmp/$_REQUEST[phyId]/admin_consent/".$key.".jpeg";
				//move files that are not signed
				rename($target,$replace);
				$replace="interface/common/new_html2pdf/app/".$key.".jpeg";
				$target="app_services/signature/tmp/$_REQUEST[phyId]/admin_consent/".$key.".jpeg";
				
				$consent_form_content=str_replace($target,$replace,$consent_form_content);
				unset($sigArr[$key]);*/
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
	
	function get_consent_form_by_id(){
		//pre($_REQUEST);die;
		if($_REQUEST['consent_id']>0)
		{
			$_REQUEST['print_false']=1;
			$_REQUEST['patient']=$_REQUEST['patId'];
			//save form id
			$procedure_id=$_REQUEST['form_id'];
			//overwrite form id by consent form id
			$_REQUEST['form_id']=$this->form_id;
			$_REQUEST['op_id']=$_REQUEST['phyId'];
			
			//need to add addition path for signature temp img
			$addition_path="/admin_consent";
			$todayDate=explode('-',date('Y-m-d'));
			
			//check do we have saved form for that date in temporary folder
			$qry_check_form="select consent_form_content_data from patient_consent_form_information_app where patient_id='$_REQUEST[patId]'  and consent_form_id='$_REQUEST[consent_id]' and YEAR(form_created_date)='".$todayDate[0]."' and MONTH(form_created_date)='".$todayDate[1]."' and DAY(form_created_date)='".$todayDate[2]."' and consent_form_content_data!=''";
			$QUERY_CHECK=imw_query($qry_check_form)or die(imw_error());
			
			if(imw_num_rows($QUERY_CHECK)> 0)
			{
				//get and send saved data
				$DATA=imw_fetch_object($QUERY_CHECK);
				$consent_form_content= html_entity_decode($DATA->consent_form_content_data);
			}
			else
			{
				/*//check in main consent form table
				$qry_check_form2="select consent_form_content_data from patient_consent_form_information where patient_id='$_REQUEST[patId]' and consent_form_id='$_REQUEST[consent_id]' and YEAR(form_created_date)='".$todayDate[0]."' and MONTH(form_created_date)='".$todayDate[1]."' and DAY(form_created_date)='".$todayDate[2]."' and consent_form_content_data!=''";
				$QUERY_CHECK_MAIN=imw_query($qry_check_form2)or die(imw_error());
				if(imw_num_rows($QUERY_CHECK_MAIN)>=1)
				{
					//get and send saved data
					$DATA_MAIN=imw_fetch_object($QUERY_CHECK_MAIN);
					$consent_form_content=stripcslashes(html_entity_decode($DATA_MAIN->consent_form_content_data));	
				}
				else
				{*/
			//$consent_form_content = '';
					require_once(dirname(__FILE__)."/print_all_consent_form_app.php");
					
					require_once(dirname(__FILE__)."/consentFormDetails_app.php");
					
					$sigPatsStr=serialize($sigPats);
					//echo $consent_form_content;
					if($protocol==''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
					
					if($protocol=='https://')$consent_form_content = str_ireplace('http://','https://',$consent_form_content);
					$ret=imw_query("insert into patient_consent_form_information_app set consent_form_id='$_REQUEST[consent_id]',
								consent_form_name='".htmlentities(addslashes(trim($consent_form_name)))."',
								patient_id='$_REQUEST[patId]',
								chart_procedure_id='$procedure_id',
								operator_id='$_REQUEST[phyId]',
								form_created_date='".date('Y-m-d H:i:s')."',
								patient_signature='".imw_real_escape_string($sigPatsStr)."',
								consent_form_content_data='".htmlentities(imw_real_escape_string(trim($consent_form_content)))."'");
				/*}*/	
				$consent_form_content = html_entity_decode($consent_form_content);
			}
			$consent_form_content = html_entity_decode($consent_form_content);
			echo $consent_form_content;
			die();
		}
	}
	
	function save_consent_form_by_id(){
		//file_put_contents('test_1.txt',print_r($_REQUEST,true), FILE_APPEND);
				
				
		$_REQUEST['print_false']=1;
		$_REQUEST['patient_id']=$_REQUEST['patId'];
		//save form id
		$procedure_id=$_REQUEST['form_id'];
		//overwrite form id by consent form id
		$_REQUEST['form_id']=$this->form_id;
		//get image name
		$file_name=$_REQUEST['sig_img_key'];
		global $webServerRootDirectoryName,$myExternalIP,$web_RootDirectoryName,$phpHTTPProtocol;
		$filePathOrg = data_path()."PatientId_".$_REQUEST['patient_id']."/sign";
		//if dir not exist then create it
		if(!is_dir($filePathOrg)){
			$mk_dir=mkdir($filePathOrg,0777);
			if(!$mk_dir){
				file_put_contents('test.txt',"unable to create folder: ".$filePathOrg." \n", FILE_APPEND);		
			}
		}
		$filePathOrg.="/".$file_name;
                $replace = data_path(1)."PatientId_".$_REQUEST['patient_id']."/sign/".$file_name;
		if($_REQUEST['typ']=='sigimg')
		{   
                       // $this->save_signature_image($_REQUEST['phyId'],$file_name,$filePathOrg);
                        $filePath = data_path()."app_services/signature/tmp/".$_REQUEST['phyId']."/admin_consent/".$file_name;
			$target = data_path(1)."app_services/signature/tmp/".$_REQUEST['phyId']."/admin_consent/".$file_name;
			if($_FILES['sig_img_key']['name']!='')
			{
				move_uploaded_file($_FILES['sig_img_key']['tmp_name'],$filePathOrg);
				$this->update_consent_form_by_id($_REQUEST['patId'],$_REQUEST['form_id'],$target,$replace,'sign','',$file_name,$procedure_id);
			}
			
		}
		elseif($_REQUEST['typ']=='content')
		{
			
			$handle = fopen('php://input','r');
			$jsonInput = fgets($handle);
			
			// Decoding JSON into an Array
			 $decoded = json_decode($jsonInput);
			
			foreach($decoded as $data){
				$consent_form_content_data=$data->content_key;	
			}
			
			//file_put_contents('test_pro.txt',"proce id".$procedure_id.'<br/><br/>'.$consent_form_content_data.'<br/>------------------------<br/>', FILE_APPEND);
			$this->update_consent_form_by_id($_REQUEST['patId'],$_REQUEST['form_id'],'','','content',$consent_form_content_data,'',$procedure_id);
		}
	}
	function save_consent_form_by_id_app(){
		//file_put_contents('test_1.txt',print_r($_REQUEST,true), FILE_APPEND);
				
				
		$_REQUEST['print_false']=1;
		$_REQUEST['patient_id']=$_REQUEST['patId'];
		//save form id
		$procedure_id=$_REQUEST['form_id'];
		//overwrite form id by consent form id
		$_REQUEST['form_id']=$this->form_id;
		//get image name
		$file_name=$_REQUEST['sig_img_key'];
		//global $webServerRootDirectoryName,$myExternalIP,$web_RootDirectoryName,$phpHTTPProtocol;
		$filePathOrg = data_path()."PatientId_".$_REQUEST['patient_id']."/sign";
		//if dir not exist then create it
		if(!is_dir($filePathOrg)){
			$mk_dir=mkdir($filePathOrg,0777,true);
			if(!$mk_dir){
				file_put_contents('test.txt',"unable to create folder: ".$filePathOrg." \n", FILE_APPEND);		
			}
		}
		$filePathOrg.="/".$file_name;
		$replace = data_path(1)."PatientId_".$_REQUEST['patient_id']."/sign/".$file_name;
		
		if($_REQUEST['typ']=='sigimg')
		{
			$filePath = $filePathOrg.'/';
			$target = data_path(1)."app_services/signature/tmp/".$_REQUEST['phyId']."/admin_consent/".$file_name;
                       //  $this->save_signature_image($_REQUEST['phyId'],$file_name,$filePathOrg);
                        //$target = "/".$web_RootDirectoryName."/app_services/signature/tmp/".$_REQUEST['phyId'].'/admin_consent/'.$file_name;
			if($_FILES['sig_img_key']['name']!='')
			{
				move_uploaded_file($_FILES['sig_img_key']['tmp_name'],$filePathOrg);
				$this->update_consent_form_by_id($_REQUEST['patId'],$_REQUEST['form_id'],$target,$replace,'sign','',$file_name,$procedure_id);
			}
			
		}
		elseif($_REQUEST['typ']=='content')
		{	
			
			   $handle = file_get_contents('php://input');
			
			
			//file_put_contents('test_pro.txt',"proce id".$procedure_id.'<br/><br/>'.$consent_form_content_data.'<br/>------------------------<br/>', FILE_APPEND);
			$this->update_consent_form_by_id($_REQUEST['patId'],$_REQUEST['form_id'],'','','content',$handle,'',$procedure_id);
		}
	}
	
	public function update_consent_form_by_id($patient_id,$consent_form_id,$target,$replace,$typ,$content,$sig_id,$procedure_id)
	{
		
		$todayDate=explode('-',date('Y-m-d'));
		//check do we have saved form for that date
		$qry_ch="select * from patient_consent_form_information_app where patient_id='$_REQUEST[patId]'  and consent_form_id='$_REQUEST[consent_id]' and (chart_procedure_id='".$procedure_id."' OR chart_procedure_id=0)";
		$QUERY_CHECK=imw_query($qry_ch);
		if(imw_num_rows($QUERY_CHECK) > 0)
		{
			//get and send saved data
			
			while($rowFetch = imw_fetch_object($QUERY_CHECK)){
				$DATA = $rowFetch;
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
					$consent_form_content=htmlentities(imw_real_escape_string(trim($consent_form_content)));
					$sigStr=serialize($sigArr);
					$qry_sig="update patient_consent_form_information_app set consent_form_content_data='$consent_form_content',
								patient_signature='". imw_real_escape_string($sigStr) ."',
								witness_signature='". imw_real_escape_string($temp_str) ."',
                                                                modified_form_created_date=now()
								where form_information_id=$DATA->form_information_id";
					imw_query($qry_sig)or die(imw_error());
				}
				elseif($typ=='content')
				{	
					foreach($temp_arr as $target=>$replace)
					{
						$content=str_replace($target,$replace,$content);	
					}
					$consent_form_content=htmlentities(imw_real_escape_string(trim($content)));
					$qq="update patient_consent_form_information_app set consent_form_content_data='$consent_form_content' where form_information_id=$DATA->form_information_id";
					imw_query($qq)or die(imw_error());
				}
			}
		}else
		{
				
		}
		
	}
	function get_opnote_form_by_id(){
		//include(dirname(__FILE__)."/../../library/classes/pnTempParser.php");
		$objParser=new PnTempParser();
		$op_id=$_REQUEST['op_id'];
		$patient_id=$_REQUEST['patId'];
		$form_id=$_REQUEST['form_id'];
		$qry_op_notes="SELECT temp_data from pn_template WHERE temp_id='".$op_id."'";
		$get_mas_pn_qry=imw_query($qry_op_notes);
		$get_mas_pn_arr=imw_fetch_assoc($get_mas_pn_qry);
		$op_note_data=stripslashes(html_entity_decode($objParser->getDataParsed($get_mas_pn_arr['temp_data'],$patient_id,$form_id,'','','','','','','procedures')));
		echo $op_note_data;die();
		return $op_note_data;
	}
	function get_patient_op_note(){
		$chart_procedure_id=$_REQUEST['proc_id'];
		$op_id=$_REQUEST['temp_id'];
		$patient_id=$_REQUEST['pt_id'];
		$qry_op_notes="SELECT txt_data from pn_reports WHERE pn_rep_id='".$op_id."' and chart_procedure_id='".$chart_procedure_id."' and patient_id='".$patient_id."'";
		$get_pn_qry=imw_query($qry_op_notes);
		$row_pn=imw_fetch_assoc($get_pn_qry);
		$patient_pn_data=$row_pn['txt_data'];
		echo html_entity_decode(stripcslashes($patient_pn_data));
		die();
		
	}
	
	function save_procedure_app(){
			//error_reporting(E_ALL);
			$phy_id = $_REQUEST['phyId'];
			$pat_id = $_REQUEST['patId'];
			$form_id = $_REQUEST['form_id'];
			$id = $_REQUEST['id'];
			$site = $_REQUEST['site'];
			$bp = trim($_REQUEST['bp']);
			$heart_attack = $_REQUEST['heart_attack'];
			$proc_id = $_REQUEST['proc_id'];
			$otherProcNote = trim($_REQUEST['otherProcNote']);
			$lids = $_REQUEST['lid'];
			$cpt_code = $_REQUEST['cpt_code'];
			$mod = $_REQUEST['mode'];
			$dx_code = $_REQUEST['dx_code'];
			$start_time = $_REQUEST['start_time'];
			$end_time = $_REQUEST['end_time'];
			$post_op_iop = $_REQUEST['post_op_iop'];
			$iop_od =$_REQUEST['iop_od'];
			$iop_os =$_REQUEST['iop_os'];
			$iop_time =$_REQUEST['iop_time'];
			$complication = $_REQUEST['complication'];
			$cmt = trim($_REQUEST['cmt']);
			$comment = trim($_REQUEST['comment']);
			$time_out = $_REQUEST['time_out'];
			$corr_proc_id = $_REQUEST['correct_proc_id'];
			$corr_site = $_REQUEST['correct_site'];
			$corr_lids = $_REQUEST['correct_lids'];
			$site_marked = $_REQUEST['site_marked'];
			$pos_pros_implant = $_REQUEST['pos_pros_implant'];
			$consent_completed = $_REQUEST['consent_completed'];
			$provider = $_REQUEST['provider'].',';
			$med_pre = $_REQUEST['med_pre'];
			$med_intra = $_REQUEST['med_intra'];
			$med_post = $_REQUEST['med_post'];
			$pre_lot = $_REQUEST['pre_lot'];
			$intra_lot = $_REQUEST['intra_lot'];
			$post_lot = $_REQUEST['post_lot'];
			$date = date("Y-m-d h:i:sa");
			$pre_med_id = $_REQUEST['pre_med_id'];
			$intra_med_id = $_REQUEST['intra_med_id'];
			$post_med_id = $_REQUEST['post_med_id'];
			$consent_id = $_REQUEST['consent_id'];
			$op_report_id = $_REQUEST['op_report_id'];
			// fetch the dos for app //
			$query = "select create_dt from chart_master_table where id = '".$form_id."'";
			$dos = imw_query($query);
			//end of dos
					if($id != ""){
						$query = "Update chart_procedures SET patient_id = '".$pat_id."',exam_date = '".$date."'";
							if($site != ""){
							$query .= ",site = '".$site."'";
							} 
							if($bp != ""){
							$query .= ",bp = '".$bp."'";
							} 		
							if($heart_attack != ""){
							$query .= ",heart_attack = '".$heart_attack."'";
							} 	
							if($proc_id != ""){
							$query .= ",proc_id = '".$proc_id."'";
							} 	
							if($otherProcNote != ""){
							$query .= ",otherProcNote = '".$otherProcNote."'";
							} 	
							if($lids != ""){
							$query .= ",lids_opts = '".$lids."'";
							} 
							if($cpt_code != ""){
							$query .= ",cpt_code = '".$cpt_code."'";
							} 
							if($mod != ""){
							$query .= ",cpt_mod = '".$mod."'";
							} 
							if($dx_code != ""){
							$query .= ",dx_code = '".$dx_code."'";
							} 
							if($start_time != ""){
							$query .= ",start_time = '".$start_time."'";
							} 
							if($end_time != ""){
							$query .= ",end_time = '".$end_time."'";
							} 
							if($post_op_iop != ""){
							$query .= ",iop_type = '".$post_op_iop."'";
							} 
							if($iop_od != ""){
							$query .= ",iop_od = '".$iop_od."'";
							} 
							if($iop_os != ""){
							$query .= ",iop_os = '".$iop_os."'";
							} 
							if($iop_time != ""){
							$query .= ",iop_time = '".$iop_time."'";
							} 
							if($complication != ""){
							$query .= ",complication = '".$complication."'";
							} 
							if($cmt != ""){
							$query .= ",cmt = '".$cmt."'";
							} 
							if($comment != ""){
							$query .= ",comments = '".$comment."'";
							} 
							if($time_out != ""){
							$query .= ",timeout = '".$time_out."'";
							} 
							if($corr_proc_id != ""){
							$query .= ",corr_proc_id = '".$corr_proc_id."'";
							} 
							if($corr_site != ""){
							$query .= ",corr_site = '".$corr_site."'";
							} 
							if($corr_lids != ""){
							$query .= ",cor_lids_opts = '".$corr_lids."'";
							} 
							if($site_marked != ""){
							$query .= ",site_marked = '".$site_marked."'";
							} 
							if($pos_pros_implant != ""){
							$query .= ",pos_pros_implant = '".$pos_pros_implant."'";
							} 
							if($consent_completed != ""){
							$query .= ",consent_completed = '".$consent_completed."'";
							} 
							if($provider != ""){
							$query .= ",providers = '".$provider."'";
							} 
							if($med_pre != ""){
							$query .= ",pre_op_meds = '".$med_pre."'";
							}
							if($med_intra != ""){
							$query .= ",intravit_meds = '".$med_intra."'";
							}
							if($med_post != ""){
							$query .= ",post_op_meds = '".$med_post."'";
							}
							if($intra_lot != ""){
							$query .= ",intravit_meds_lot = '".$intra_lot."'";
							}
							if($post_lot != ""){
							$query .= ",post_op_meds_lot = '".$post_lot."'";
							}
							$query .= " where id = '".$id."' AND 
											form_id = '".$form_id."' AND 
											user_id = '".$phy_id."' AND 
											patient_id = '".$pat_id."'";
											
							$result = imw_query($query);
							
							if(!empty($med_pre)){
								$pre_med = explode('|~|',$med_pre);
								$pre_lot = explode('|~|',$pre_lot);
								$pre_id = explode('|~|',$pre_med_id);
								array_pop($pre_med);
								array_pop($pre_lot);
								array_pop($pre_id);
								foreach($pre_med as $key => $value){
									if($pre_id[$key] == ""){
										 $query = "insert into chart_procedures_med_lot set 
													med_name = '".$value."',
													med_type = 'preop',
													chart_procedure_id = '".$id."',
													lot_number = '".$pre_lot[$key]."'";
											$result = imw_query($query);
										}
									else{
											 $query = "update chart_procedures_med_lot set 
													med_name = '".$value."',
													lot_number = '".$pre_lot[$key]."'
													where id = '".$pre_id[$key]."'
													";
													$result = imw_query($query);
									}
								}
							}
							if(!empty($med_intra)){
								$intra_med = explode('|~|',$med_intra);
								$intra_lot = explode('|~|',$intra_lot);
								$intra_id = explode('|~|',$intra_med_id);
								array_pop($intra_med);
								array_pop($intra_lot);
								array_pop($intra_id);
								foreach($intra_med as $key => $value){
									if($intra_id[$key] == ""){
										 $query = "insert into chart_procedures_med_lot set 
													med_name = '".$value."',
													med_type = 'intravitreal',
													chart_procedure_id = '".$id."',
													lot_number = '".$intra_lot[$key]."'";
										 $result = imw_query($query);
											}
									else{
										$query = "update chart_procedures_med_lot set 
													med_name = '".$value."',
													lot_number = '".$intra_lot[$key]."'
													where id = '".$intra_id[$key]."'
													";
										$result = imw_query($query);
									}
									$query = imw_query("select title from lists where type = 4 AND title = '".$value."' AND pid = '".$pat_id."' AND user = '".$phy_id."' AND allergy_status != 'Deleted'");
									$get_med_title = imw_num_rows($query);
									if($get_med_title == 0){
											$qry = "insert into lists set type = 4, title = '".$value."',
											date = '".$date."',
											allergy_status = 'Administered',
											sites = '".$site."',
											pid = '".$pat_id."',
											user = '".$phy_id."',
											timestamp = '".$date."',
											begdate = '".$dos['create_dt']."'";
											$res = imw_query($qry);
									}							
								}
							}
							
							 if(!empty($med_post)){
								$post_med = explode('|~|',$med_post);
								$post_lot = explode('|~|',$post_lot);
								$post_id = explode('|~|',$post_med_id);
								array_pop($post_med);
								array_pop($post_lot);
								array_pop($post_id);
								foreach($post_med as $key => $value){
									if($post_id[$key] == ""){
										 $query = "insert into chart_procedures_med_lot set 
													med_name = '".$value."',
													med_type = 'postop',
													chart_procedure_id = '".$id."',
													lot_number = '".$post_lot[$key]."'";
											$result = imw_query($query);
											}
									else{
											 $query = "update chart_procedures_med_lot set 
													med_name = '".$value."',
													lot_number = '".$post_lot[$key]."'
													where id = '".$post_id[$key]."'
													";
													$result = imw_query($query);
									}
								}
							}			
							if($consent_id != 0 && $id !=0){
									$result = $this->insert_update_consent_app($phy_id,$pat_id,$form_id,$consent_id,$id);
									
							}
							if($op_report_id !=0 && $id !=0){
								$result = $this->insert_update_op_report_app($phy_id,$pat_id,$form_id,$op_report_id,$id);	
							}
							return true; 
					}
				else{
					$query = "INSERT INTO chart_procedures SET patient_id = '".$pat_id."',
								form_id = '".$form_id."',user_id = '".$phy_id."',exam_date = '".$date."'";
					if($site != ""){
						$query .= ",site = '".$site."'";
					} 
					if($bp != ""){
						$query .= ",bp = '".$bp."'";
					} 		
					if($heart_attack != ""){
						$query .= ",heart_attack = '".$heart_attack."'";
					} 	
					if($proc_id != ""){
						$query .= ",proc_id = '".$proc_id."'";
					} 	
					if($otherProcNote != ""){
						$query .= ",otherProcNote = '".$otherProcNote."'";
					} 	
					if($lids != ""){
						$query .= ",lids_opts = '".$lids."'";
					} 
					if($cpt_code != ""){
						$query .= ",cpt_code = '".$cpt_code."'";
					} 
					if($mod != ""){
						$query .= ",cpt_mod = '".$mod."'";
					} 
					if($dx_code != ""){
						$query .= ",dx_code = '".$dx_code."'";
					} 
					if($start_time != ""){
						$query .= ",start_time = '".$start_time."'";
					} 
					if($end_time != ""){
						$query .= ",end_time = '".$end_time."'";
					} 
					if($post_op_iop != ""){
						$query .= ",iop_type = '".$post_op_iop."'";
					} 
					if($iop_od != ""){
						$query .= ",iop_od = '".$iop_od."'";
					} 
					if($iop_os != ""){
						$query .= ",iop_os = '".$iop_os."'";
					} 
					if($iop_time != ""){
						$query .= ",iop_time = '".$iop_time."'";
					} 
					if($complication != ""){
						$query .= ",complication = '".$complication."'";
					} 
					if($cmt != ""){
						$query .= ",cmt = '".$cmt."'";
					} 
					if($comment != ""){
						$query .= ",comments = '".$comment."'";
					} 
					if($time_out != ""){
						$query .= ",timeout = '".$time_out."'";
					} 
					if($corr_proc_id != ""){
						$query .= ",corr_proc_id = '".$corr_proc_id."'";
					} 
					if($corr_site != ""){
						$query .= ",corr_site = '".$corr_site."'";
					} 
					if($corr_lids != ""){
						$query .= ",cor_lids_opts = '".$corr_lids."'";
					} 
					if($site_marked != ""){
						$query .= ",site_marked = '".$site_marked."'";
					} 
					if($pos_pros_implant != ""){
						$query .= ",pos_pros_implant = '".$pos_pros_implant."'";
					} 
					if($consent_completed != ""){
						$query .= ",consent_completed = '".$consent_completed."'";
					} 
					if($provider != ""){
						$query .= ",providers = '".$provider."'";
					} 
					if($med_pre != ""){
						$query .= ",pre_op_meds = '".$med_pre."'";
					} 
					if($med_intra != ""){
						$query .= ",intravit_meds = '".$med_intra."'";
					} 
					if($med_post != ""){
						$query .= ",post_op_meds = '".$med_post."'";
					} 
					if($intra_lot != ""){
						$query .= ",intravit_meds_lot = '".$intra_lot."'";
					} 
					if($post_lot != ""){
						$query .= ",post_op_meds_lot = '".$post_lot."'";
					} 
					//echo $query;
					$result = imw_query($query);
					$prob_id =  imw_insert_id();
					if($prob_id != "" && $prob_id != 0){
						$query = "select proc_id from chart_procedures where id = '".$prob_id ."'";
						$record = imw_query($query);
						$rec = imw_fetch_assoc($record);
						$qry = "select procedure_name from operative_procedures where procedure_id = '".$rec['proc_id']."'";
						$result = imw_query($qry);
						$res = imw_fetch_assoc($result);
						$date = date('Y-m-d H:i:s');
						switch($site){
								case "OS":
								$site = "1";
								break;
								case "OD":
								$site = "2";
								break;
								case "OU":
								$site = "3";
								break;
								case "PO":
								$site = "4";
								break;
								default :
								$site = "0";
								break;
						}
						$query = imw_query("select title,begdate from lists where type = 6 AND title = '".$res['procedure_name']."' AND begdate = '".$dos."' AND allergy_status != 'Deleted'");
						$get_type_6 = imw_num_rows($query);
						if($get_type_6 == 0){
							$query_proc = "Insert Into lists set type = 6,title = '".$res['procedure_name']."',
												date = '".$date."',
												pid = '".$pat_id."',
												user = '".$phy_id."',
												allergy_status = 'Active',
												sites = '".$site."',
												timestamp = '".$date."',
												proc_type = 'procedure',
												begdate = '".$dos['create_dt']."'";
										$res = imw_query($query_proc);
						}
					}
					if(!empty($med_pre)){
						$pre_med = explode('|~|',$med_pre);
						$pre_lot = explode('|~|',$pre_lot);
						array_pop($pre_med);
						array_pop($pre_lot);
						
						foreach($pre_med as $key => $value){
							$query = "insert into chart_procedures_med_lot set 
										med_name = '".$value."',
										med_type = 'preop',
										chart_procedure_id = '".$prob_id."',
										lot_number = '".$pre_lot[$key]."'";
										$result = imw_query($query);
						}
					}
					
					if(!empty($med_intra)){
						$intra_med = explode('|~|',$med_intra);
						$intra_lot = explode('|~|',$intra_lot);
						$date = date('Y-m-d H:i:s');
						array_pop($intra_med);
						array_pop($intra_lot);
						switch($site){
								case "OS":
								$site = "1";
								break;
								case "OD":
								$site = "2";
								break;
								case "OU":
								$site = "3";
								break;
								case "PO":
								$site = "4";
								break;
								default :
								$site = "0";
								break;
						}
						foreach($intra_med as $key => $value){
							$query = "insert into chart_procedures_med_lot set 
										med_name = '".$value."',
										med_type = 'intravitreal',
										chart_procedure_id = '".$prob_id."',
										lot_number = '".$intra_lot[$key]."'";
										$result = imw_query($query);
							$query = imw_query("select title from lists where type = 4 AND title = '".$value."' AND pid = '".$pat_id."' AND user = '".$phy_id."' AND allergy_status != 'Deleted'");
							$get_med_title = imw_num_rows($query);
							if($get_med_title == 0){
								$qry = "insert into lists set type = 4, title = '".$value."',
											date = '".$date."',
											allergy_status = 'Administered',
											sites = '".$site."',
											pid = '".$pat_id."',
											user = '".$phy_id."',
											timestamp = '".$date."',
											begdate = '".$dos['create_dt']."'";
											$res = imw_query($qry);
							}							
						}
					}
					
					if(!empty($med_post)){
						$post_med = explode('|~|',$med_post);
						$post_lot = explode('|~|',$post_lot);
						array_pop($post_med);
						array_pop($post_lot);
						foreach($post_med as $key => $value){
							$query = "insert into chart_procedures_med_lot set 
										med_name = '".$value."',
										med_type = 'postop',
										chart_procedure_id = '".$prob_id."',
										lot_number = '".$post_lot[$key]."'";
										$result = imw_query($query);
						}
					}
					
						if($consent_id != "" && $prob_id != 0){
									$result = $this->insert_update_consent_app($phy_id,$pat_id,$form_id,$consent_id,$prob_id);
									
						}
						if($op_report_id !=0 && $prob_id !=0){
								$result = $this->insert_update_op_report_app($phy_id,$pat_id,$form_id,$op_report_id,$prob_id);	
							}
						if($prob_id != 0){
							return true;
							}
						else{
								return false;
							}
				}		
			} 
			
			function insert_update_consent_app($phy_id,$pat_id,$form_id,$consent_id,$id){
					
					$todayDate=explode('-',date('Y-m-d'));
					$query = "select * from patient_consent_form_information_app where patient_id='".$pat_id."' and consent_form_id='".$consent_id."'";
					$record = imw_query($query);
					$result = imw_fetch_assoc($record);
					
					$res = stripcslashes(html_entity_decode(trim($result['consent_form_content_data'])));
					$consent=explode("<endofcode></endofcode>",$res);  
					$consent[0] = preg_replace('/<a id=\"(.*?)\" class=\"(.*?)\" href=\"(.*?)\">(.*?)<\/a>/', "\\4", $consent[0]);
					$consent[0] = preg_replace('/align=\"center\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">/', "align=\"left\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">", $consent[0]);
					$consent[0] = preg_replace('/<textarea class=\"manageUserInput\" rows=\"2\" cols=\"90\" name=\"(.*?)\" id=\"(.*?)\">/', "", $consent[0]);
					$consent[0] = preg_replace('/<\/textarea>/', "", $consent[0]);
					//replace text boxes
					$consent[0] = preg_replace('/<input class=\"manageUserInput\" type=\"text\" name=\"(.*?)\" id=\"(.*?)\" value=\"(.*?)\" size=\"(.*?)\" maxlength=\"(.*?)\" autocomplete=\"off\">/', "",$consent[0]);
					//set display block for hidden user input values
					$consent[0] = preg_replace('/<span id=\"(.*?)\" style=\"display:none\">(.*?)<\/span>/', "\\2", $consent[0]);
					
					//replace any unsigned image
					$consent[0]=$this->removeTempImg($consent[0],$result['patient_signature']);
					//remove text input box
					$consent[0]=str_ireplace("height:20px; width:200px; min-width:200px; border:#999 1px solid;","",$consent[0]);
					//remove textarea box
					$consent[0]=str_ireplace("height:70px; width:300px; min-width:300px; border:#999 1px solid;","",$consent[0]);
					//remove signature border
					$consent[0]=str_ireplace('style="border:solid 1px;" bordercolor="#FF9900"',"",$consent[0]);
					$consent[0]=str_ireplace("HEIGHT: 90px; WIDTH: 320px;display:inline-block;position: relative;border:solid 1px; border-color:#FF9900;","",$consent[0]);	
					$consent[0]= htmlentities(imw_real_escape_string(trim($consent[0])));
					
					
					if($id != ""){
					
						 $query = "INSERT INTO patient_consent_form_information set 
										consent_form_id = '".$result['consent_form_id']."',
										consent_form_name = '".$result['consent_form_name']."',
										patient_id = '".$result['patient_id']."',
										operator_id = '".$result['operator_id']."',
										patient_signature = '".$result['patient_signature']."',
										witness_signature = '".$result['witness_signature']."',
										form_status = '".$result['form_status']."',
										form_created_date = '".$result['form_created_date']."',
										consent_form_content_data = '".$consent[0]."',
										movedToTrash = '".$result['movedToTrash']."',
										modified_operator_id = '".$result['modified_operator_id']."',
										modified_form_created_date = '".$result['modified_form_created_date']."',
										chart_procedure_id = '".$id."',
										iportal_patient_id = '".$result['iportal_patient_id']."',
										package_category_id = '".$result['package_category_id']."'
													";
						$res = imw_query($query);
						if($res !=0 && $res !=""){
							$query = imw_query("update chart_procedures set consent_form_id = '".$result['consent_form_id']."'
																				where id = '".$id."'");
								
							 $qry = imw_query("Delete from patient_consent_form_information_app 
													where patient_id = '".$pat_id."' AND
													chart_procedure_id = '".$form_id."' AND
													consent_form_id = '".$consent_id."' AND
													form_information_id = '".$result['form_information_id']."'
													order by form_information_id desc LIMIT 1");
													return true;
								}
								else {
									return false;
								}
											
							}
						}
			function insert_update_op_report_app($phy_id,$pat_id,$form_id,$op_report_id,$id){
					
					$today = date('Y-m-d h:i:s');
					$query = "select * from pn_template where temp_id = '".$op_report_id."'";
					$record = imw_query($query);
					$result = imw_fetch_assoc($record);
					
					$qry = "INSERT INTO pn_reports set
								patient_id = '".$pat_id."',
								form_id = '".$form_id."',
								txt_data = '".imw_real_escape_string(stripcslashes($result['temp_data']))."',
								pn_rep_date = '".$today."',
								tempId =  '".$result['temp_id']."',
								opid = '".$phy_id."',
								chart_procedure_id = '".$id."'
								";
								
					$res = imw_query($qry);
					$op_id =  imw_insert_id();
					if($op_id !="" && $op_id !=0){
						$query = imw_query("update chart_procedures set op_report_id = '".$result['temp_id']."'
																				where id = '".$id."'");
					}
				
				}
		function save_botox_procedure_app(){
				 
				$date = date("Y-m-d h:i:sa");
				$phy_id = $_REQUEST['phyId'];
				$pat_id = $_REQUEST['patId'];
				$form_id = $_REQUEST['form_id'];
				$id = $_REQUEST['id'];
				$site = $_REQUEST['site'];
				$bp = trim($_REQUEST['bp']);
				$heart_attack = $_REQUEST['heart_attack'];
				$proc_id = $_REQUEST['proc_id'];
				$otherProcNote = trim($_REQUEST['otherProcNote']);
				$lids = $_REQUEST['lid'];
				$cpt_code = $_REQUEST['cpt_code'];
				$type_btx = $_REQUEST['type_btx'];
				$mod = $_REQUEST['mode'];
				$dx_code = $_REQUEST['dx_code'];
				$start_time = $_REQUEST['start_time'];
				$end_time = $_REQUEST['end_time'];
				$post_op_iop = $_REQUEST['post_op_iop'];
				$iop_od =$_REQUEST['iop_od'];
				$iop_os =$_REQUEST['iop_os'];
				$iop_time =$_REQUEST['iop_time'];
				$complication = $_REQUEST['complication'];
				$comment = trim($_REQUEST['comments']);
				$total = $_REQUEST['total'];
				$used = $_REQUEST['used'];
				$wasted = $_REQUEST['wasted'];
				$lot = $_REQUEST['lot'];
				$vis_sc_od = $_REQUEST['vis_sc_od'];
				$vis_cc_od = $_REQUEST['vis_cc_od'];
				$vis_other_od = $_REQUEST['vis_other_od'];
				$vis_sc_os = $_REQUEST['vis_sc_os'];
				$vis_cc_os = $_REQUEST['vis_cc_os'];
				$vis_other_os = $_REQUEST['vis_other_os'];
				$draw_path = $_REQUEST['draw_path'];
				$draw_coords = $_REQUEST['draw_coords'];
				$rbdcs = $_REQUEST['rbdcs'];
				$rd_injctn	 = $_REQUEST['rd_injctn'];
				$drw_img = "iDoc-Drawing/images/face_bottox_v2.jpg";
				$drw_img_dim = $_REQUEST['draw_img_dim'];
				$consent_id = $_REQUEST['consent_id'];
				$op_report_id = $_REQUEST['op_report_id'];
				if($rbdcs == 1){
					$rbdcs = "R&B Discussed, Consent signed";
				}
				else{
					$rbdcs = "";
				}	
			
				if($id != ""){
					
					$query = "Update chart_procedures SET patient_id = '".$pat_id."',exam_date = '".$date."'";
					if($site != ""){
						$query .= ",site = '".$site."'";
					} 
					if($bp != ""){
						$query .= ",bp = '".$bp."'";
					} 		
					if($heart_attack != ""){
						$query .= ",heart_attack = '".$heart_attack."'";
					} 	
					if($proc_id != ""){
						$query .= ",proc_id = '".$proc_id."'";
					} 	
					if($otherProcNote != ""){
						$query .= ",otherProcNote = '".$otherProcNote."'";
					} 	
					if($lids != ""){
						$query .= ",lids_opts = '".$lids."'";
					} 
					if($cpt_code != ""){
						$query .= ",cpt_code = '".$cpt_code."'";
					} 
					if($mod != ""){
						$query .= ",cpt_mod = '".$mod."'";
					} 
					if($dx_code != ""){
						$query .= ",dx_code = '".$dx_code."'";
					} 
					if($start_time != ""){
						$query .= ",start_time = '".$start_time."'";
					} 
					if($end_time != ""){
						$query .= ",end_time = '".$end_time."'";
					} 
					if($post_op_iop != ""){
						$query .= ",iop_type = '".$post_op_iop."'";
					} 
					if($iop_od != ""){
						$query .= ",iop_od = '".$iop_od."'";
					} 
					if($iop_os != ""){
						$query .= ",iop_os = '".$iop_os."'";
					} 
					if($iop_time != ""){
						$query .= ",iop_time = '".$iop_time."'";
					} 
					if($complication != ""){
						$query .= ",complication = '".$complication."'";
					} 
					if($comment != ""){
						$query .= ",comments = '".$comment."'";
					} 
					 $query .= " where id = '".$id."' AND 
								form_id = '".$form_id."' AND 
								user_id = '".$phy_id."' AND 
								patient_id = '".$pat_id."'";
					$result = imw_query($query);
					// upload a botox photo //
					
					if($draw_path == 'image'){
						$imagePath=dirname(__FILE__)."/../../interface/main/uploaddir";
						$patientDir = "/PatientId_".$pat_id."";
						$idocDrawingDirName = "/proc_botox";		
						if(is_dir($imagePath.$patientDir.$idocDrawingDirName) == false){
							mkdir($imagePath.$patientDir.$idocDrawingDirName, 0777, true);
						}	
						$image = "/drw_procid_".$id.".png";
						$thumb_img= "/drw_procid_".$id."_s.png";
						$target = $imagePath.$patientDir.$idocDrawingDirName.$image;
						$target_thumb = $imagePath.$patientDir.$idocDrawingDirName.$thumb_img;
						if($_FILES['image']['name'] != ""){
							move_uploaded_file($_FILES['image']['tmp_name'],$target);
							move_uploaded_file($_FILES['image']['tmp_name'],$target_thumb);
						}
					}
					 $path = $patientDir.$idocDrawingDirName.$image;
					//end a upload code//	
					$qry = "UPDATE chart_procedures_botox SET type_btx = '".$type_btx."'";
					if($total != ""){
						$qry .= ",btx_total = '".$total."'";
					} 
					if($used != ""){
						$qry .= ",btx_usd = '".$used."'";
					} 
					if($wasted != ""){
						$qry .= ",btx_wstd = '".$wasted."'";
					} 
					if($lot != ""){
						$qry .= ",lot = '".$lot."'";
					} 
					if($vis_sc_od != ""){
						$qry .= ",vis_sc_od = '".$vis_sc_od."'";
					} 
					if($vis_cc_od != ""){
						$qry .= ",vis_cc_od = '".$vis_cc_od."'";
					} 
					if($vis_other_od != ""){
						$qry .= ",vis_othr_od = '".$vis_other_od."'";
					} 
					if($vis_sc_os != ""){
						$qry .= ",vis_sc_os = '".$vis_sc_os."'";
					} 
					if($vis_cc_os != ""){
						$qry .= ",vis_cc_os = '".$vis_cc_os."'";
					} 
					if($vis_other_os != ""){
						$qry .= ",vis_othr_os = '".$vis_other_os."'";
					} 
					if($path != ""){
						$qry .= ",drw_path = '".$path."'";
					} 
					if($type_btx != ""){
						$qry .= ",type_btx = '".$type_btx."'";
					} 
					if($rbdcs != ""){
						$qry .= ",rbdcs = '".$rbdcs."'";
					}
					if($rd_injctn != ""){
						$qry .= ",rd_injctn = '".$rd_injctn."'";
					}
					if($drw_img != ""){
						$qry .= ",drw_img = '".$drw_img."'";
					}
					if($drw_img_dim != ""){
						$qry .= ",drw_img_dim = '".$drw_img_dim."'";
					}
					$qry .= "where chart_proc_id = '".$id."'";
					
					$result = imw_query($qry);
						
					if($consent_id != 0 && $id !=0){
							$result = $this->insert_update_consent_app($phy_id,$pat_id,$form_id,$consent_id,$id);
					}
					if($op_report_id !=0 && $id !=0){
						$result = $this->insert_update_op_report_app($phy_id,$pat_id,$form_id,$op_report_id,$id);	
					}
					if($id != 0){
						$arr['flag']=true;
						$arr['id']=$id;
						return $arr;
					}
					else{
						$arr['flag']=false;
						return $arr;
					}
				}
				else{	
						$query = "INSERT INTO chart_procedures SET patient_id = '".$pat_id."',
								form_id = '".$form_id."',user_id = '".$phy_id."',exam_date = '".$date."'";
							
							if($site != ""){
								$query .= ",site = '".$site."'";
							} 
							if($bp != ""){
								$query .= ",bp = '".$bp."'";
							} 		
							if($heart_attack != ""){
								$query .= ",heart_attack = '".$heart_attack."'";
							} 	
							if($proc_id != ""){
								$query .= ",proc_id = '".$proc_id."'";
							} 	
							if($otherProcNote != ""){
								$query .= ",otherProcNote = '".$otherProcNote."'";
							} 	
							if($lids != ""){
								$query .= ",lids_opts = '".$lids."'";
							} 
							if($cpt_code != ""){
								$query .= ",cpt_code = '".$cpt_code."'";
							} 
							if($mod != ""){
								$query .= ",cpt_mod = '".$mod."'";
							} 
							if($dx_code != ""){
								$query .= ",dx_code = '".$dx_code."'";
							} 
							if($start_time != ""){
								$query .= ",start_time = '".$start_time."'";
							} 
							if($end_time != ""){
								$query .= ",end_time = '".$end_time."'";
							} 
							if($post_op_iop != ""){
								$query .= ",iop_type = '".$post_op_iop."'";
							} 
							if($iop_od != ""){
								$query .= ",iop_od = '".$iop_od."'";
							} 
							if($iop_os != ""){
								$query .= ",iop_os = '".$iop_os."'";
							} 
							if($iop_time != ""){
								$query .= ",iop_time = '".$iop_time."'";
							} 
							if($complication != ""){
								$query .= ",complication = '".$complication."'";
							} 
							
							if($comment != ""){
								$query .= ",comments = '".$comment."'";
							} 
							
							$result = imw_query($query);
							$prob_id =  imw_insert_id();
							// upload a botox photo //
							if($draw_path == 'image'){
							$imagePath=dirname(__FILE__)."/../../interface/main/uploaddir";
							$patientDir = "/PatientId_".$pat_id."";
							$idocDrawingDirName = "/proc_botox";		
							if(is_dir($imagePath.$patientDir.$idocDrawingDirName) == false){
								mkdir($imagePath.$patientDir.$idocDrawingDirName, 0777, true);
							}	
							$image = "/drw_procid_".$prob_id.".png";
							$target = $imagePath.$patientDir.$idocDrawingDirName.$image;
							if($_FILES['image']['name'] != ""){
								move_uploaded_file($_FILES['image']['tmp_name'],$target);
							}
						}
							$path = $patientDir.$idocDrawingDirName.$image;
							//end of code//
							$qry = "INSERT INTO chart_procedures_botox SET type_btx = '".$type_btx."',chart_proc_id = '".$prob_id."'";
							if($total != ""){
								$qry .= ",btx_total = '".$total."'";
							} 
							if($used != ""){
								$qry .= ",btx_usd = '".$used."'";
							} 
							if($wasted != ""){
								$qry .= ",btx_wstd = '".$wasted."'";
							} 
							if($lot != ""){
								$qry .= ",lot = '".$lot."'";
							} 
							if($vis_sc_od != ""){
								$qry .= ",vis_sc_od = '".$vis_sc_od."'";
							} 
							if($vis_cc_od != ""){
								$qry .= ",vis_cc_od = '".$vis_cc_od."'";
							} 
							if($vis_other_od != ""){
								$qry .= ",vis_othr_od = '".$vis_other_od."'";
							} 
							if($vis_sc_os != ""){
								$qry .= ",vis_sc_os = '".$vis_sc_os."'";
							} 
							if($vis_cc_os != ""){
								$qry .= ",vis_cc_os = '".$vis_cc_os."'";
							} 
							if($vis_other_os != ""){
								$qry .= ",vis_othr_os = '".$vis_other_os."'";
							} 
							if($path != ""){
								$qry .= ",drw_path = '".$path."'";
							} 
							if($rbdcs != ""){
								$qry .= ",rbdcs = '".$rbdcs."'";
							}
							if($rd_injctn != ""){
								$qry .= ",rd_injctn = '".$rd_injctn."'";
							}
							if($drw_img != ""){
								$qry .= ",drw_img = '".$drw_img."'";
							}
							if($drw_img_dim != ""){
								$qry .= ",drw_img_dim = '".$drw_img_dim."'";
							}
							$result = imw_query($qry);
							
							if($consent_id != "" && $prob_id != 0){
									$result = $this->insert_update_consent_app($phy_id,$pat_id,$form_id,$consent_id,$prob_id);
									
						}
							if($op_report_id !=0 && $prob_id !=0){
								$result = $this->insert_update_op_report_app($phy_id,$pat_id,$form_id,$op_report_id,$prob_id);	
							}
							if($prob_id != 0){
								$arr['flag']=true;
								$arr['id']="$prob_id";
								return $arr;
							}
							else{
								$arr['flag']=false;
								return $arr;
							}
				}
			}
			
		 function insert_coordinate_app(){
			$id = $_REQUEST['id'];
			$handle = file_get_contents('php://input');
		 	$coord = stripcslashes($handle);
		 	$coord_string = imw_real_escape_string($coord);
			if($coord_string != ""){
		 		 $query = "update chart_procedures_botox set drw_coords = '".$coord."'
											where chart_proc_id = '".$id."'";
		 		$result = imw_query($query);
				return true;
			}	
		}
		
		function delete_procedure_app(){
			
			$id = $_REQUEST['id'];
			$pat_id = $_REQUEST['pat_id'];
			$phy_id = $_REQUEST['phy_id'];
			$form_id = $_REQUEST['form_id'];
			if($id != ""){
				$query = "update chart_procedures set deleted_by = 1 where id = '".$id."'";
				$result = imw_query($query);
				return true;
			}
			else{
				return false;
			
			}
		}
		
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
		
		function on_hold_procedure_app(){
			
			$id = $_REQUEST['exam_id'];
			$pat_id = $_REQUEST['pat_id'];
			$phy_id = $_REQUEST['phy_id'];
			$form_id = $_REQUEST['form_id'];
			$hidd_hold_to_physician = $_REQUEST['hidd_hold_to_physician'];
			if($hidd_hold_to_physician != "" && $id != ""){
				$query = "update chart_procedures set hidd_hold_to_physician = '".$hidd_hold_to_physician."'
								where id = '".$id."'
								AND patient_id = '".$pat_id."'
								AND user_id = '".$phy_id."'
								AND form_id = '".$form_id."'";
				$result = imw_query($query);
				return true;
			}
			else{
				return false;
			}
		}

/*		
		$arrReturn['procedures'][0]['exam_date'] = "03-02-2015";
		$arrReturn['procedures'][0]['exam_id'] = "101";
		$arrReturn['procedures'][0]['data']['procedure'] = "avastin-inj";
		$arrReturn['procedures'][0]['data']['site'] = "OU";
		$arrReturn['procedures'][0]['data']['dxCode'] = "104";
		$arrReturn['procedures'][0]['data']['cptCode'] = "113.02";
		$arrReturn['procedures'][0]['data']['mod'] = "mod 1";
		$arrReturn['procedures'][0]['data']['startTime'] = "st 1";
		$arrReturn['procedures'][0]['data']['endTime'] = "et 1";
		$arrReturn['procedures'][0]['data']['postOP'] = "postOP";
		$arrReturn['procedures'][0]['data']['postOPOD'] = "OD";
		$arrReturn['procedures'][0]['data']['postOPOS'] = "OS";
		$arrReturn['procedures'][0]['data']['complication'] = "Yes";
		$arrReturn['procedures'][0]['data']['comm'] = "comm1 ";
		$arrReturn['procedures'][0]['data']['timeout'] = "Yes";
		$arrReturn['procedures'][0]['data']['timeProc'] = "Yes";
		$arrReturn['procedures'][0]['data']['timeSite'] = "Yes";
		$arrReturn['procedures'][0]['data']['timeSiteMarked'] = "Yes";
		$arrReturn['procedures'][0]['data']['timePosition'] = "Yes";
		$arrReturn['procedures'][0]['data']['timeConsent'] = "Yes";
		$arrReturn['procedures'][0]['data']['timeProviders'] = "Providers";
		$arrReturn['procedures'][0]['data']['preopmeds'][] = array("med"=>"med1","lot"=>"lot 1","qty"=>"1");
		$arrReturn['procedures'][0]['data']['preopmeds'][] = array("med"=>"med2","lot"=>"lot 2","qty"=>"2");
		$arrReturn['procedures'][0]['data']['preopmeds'][] = array("med"=>"med2","lot"=>"lot 2","qty"=>"3");
		$arrReturn['procedures'][0]['data']['preopmeds'][] = array("med"=>"med2","lot"=>"lot 2","qty"=>"4");
		
		$arrReturn['procedures'][0]['data']['intravitreal'][] = array("med"=>"med1","lot"=>"lot 1","qty"=>"1");
		$arrReturn['procedures'][0]['data']['intravitreal'][] = array("med"=>"med2","lot"=>"lot 2","qty"=>"2");
		$arrReturn['procedures'][0]['data']['intravitreal'][] = array("med"=>"med2","lot"=>"lot 2","qty"=>"3");
		$arrReturn['procedures'][0]['data']['intravitreal'][] = array("med"=>"med2","lot"=>"lot 2","qty"=>"4");
		
		$arrReturn['procedures'][0]['data']['postopmeds'][] = array("med"=>"med1","lot"=>"lot 1","qty"=>"1");
		$arrReturn['procedures'][0]['data']['postopmeds'][] = array("med"=>"med2","lot"=>"lot 2","qty"=>"2");
		$arrReturn['procedures'][0]['data']['postopmeds'][] = array("med"=>"med2","lot"=>"lot 2","qty"=>"3");
		$arrReturn['procedures'][0]['data']['postopmeds'][] = array("med"=>"med2","lot"=>"lot 2","qty"=>"4");
		
		$arrReturn['procedures'][0]['data']['consent']		= array("id"=>"101","name"=>"consent 1","data"=>"data 234");
		$arrReturn['procedures'][0]['data']['opnote']		= array("id"=>"101","name"=>"opnote 1","data"=>"data 111");
		
		$arrReturn['consent'][0]['name'] = "Consent 1";
		$arrReturn['consent'][0]['data'] = "data 1";
		$arrReturn['consent'][0]['id']   = "101";
		
		$arrReturn['opnote'][0]['name'] = "opnote 1";
		$arrReturn['opnote'][0]['data'] = "data 1";
		$arrReturn['opnote'][0]['id']   = "101";
		
		$arrReturn['dx'][0]['name'] = '112.5';
		$arrReturn['cpt'][0]['name'] = '92015';
		$arrReturn['mod'][0]['name'] = 'VI';
		$arrReturn['med'][0]['name'] = 'avastin';
		$arrReturn['med'][0]['opt_med_id'] = 123;
		$arrReturn['lot'][0]['no'] = '54455445';
		$arrReturn['lot'][0]['opt_item_id'] = 123;
		
*/
		
}
?>