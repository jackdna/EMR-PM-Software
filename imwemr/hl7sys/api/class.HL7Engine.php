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
/*	
File: CLS_makeHL7.php
Purpose: Class for HL7
Access Type: Include 
*/
require_once(dirname(__FILE__)."/class.HL7DB.php");
require_once(dirname(__FILE__)."/class.HL7Variables.php");
//error_reporting(-1);
//ini_set("display_errors",-1);

class HL7Engine extends HL7DB{
	private $field_separator, $component_separator, $repetition_separator, $subcomponent_separator, $escape_char, $segmentEnding, $segment_counter;
	public  $interface_mode, $HL7_version;
	function __construct(){ //constructor
		$this->field_separator			= "|";
		$this->component_separator		= "^";
		$this->repetition_separator		= "~";
		$this->subcomponent_separator	= "&";
		$this->escape_char				= "\\";
		$this->segmentEnding			= chr(13);
		$this->segment_counter			= array();
		$this->interface_mode			= 'T';
		$this->HL7_version				= '2.5.1';
		
	}

	function generateHL7(){
		if(!$this->application_module) return;
		$a = $this->get_enabled_outbound_message_types($this->application_module);
		if(!$a) return;
		
		foreach($a as $interface_id=>$msg_types_arr){
			$this->interface_id		= $interface_id;
			$this->msgFor 			= $msg_types_arr['interface_with'];
			$this->interface_mode 	= $msg_types_arr['interface_mode'];
			$this->HL7_version 		= $msg_types_arr['HL7_version'];
			unset($msg_types_arr['interface_mode']);
			unset($msg_types_arr['HL7_version']);
			unset($msg_types_arr['interface_with']);
			foreach ($msg_types_arr as $msg_type=>$flag){
				$this->msgType = $msg_type;
				if($msg_type=='SIU' && !$this->SIU_MSG_Types($this->msgSubType)){
					return;
				}
				$segmentsRS = $this->get_segments_message_type_wise($this->interface_id,$this->msgType);
				$segments = $segmentsRS['msg_segments'];
				if($segments) $segments = explode(',',$segments);
				if(is_array($segments)){
					$segData = '';
					foreach($segments as $seg){
						$segData .= $this->getSegmentData($seg);
					}
					$this->save_new_hl7_message($segData);
				}
			}
		}
		//die('<br>end of msg generation code.<hr>');
	}
	
	function getSegmentSettings($profile_id,$segment){
		$master_settings	= $this->get_master_segment_settings($segment);
		$profiled_settings	= $this->get_profiled_segment_settings($segment,$profile_id);
		if($master_settings && is_array($master_settings) && $profiled_settings && is_array($profiled_settings)){
			$master_settings = array_replace($master_settings,$profiled_settings);
		}
		if($master_settings && is_array($master_settings)){
			return $master_settings;
		}
		return false;
	}
	
	function getSegmentData($seg){
		if($this->msgType=='ZMS' && $this->ZMSgivenMR && !empty($this->ZMSgivenMR) && substr($this->ZMSgivenMR,0,2)=='CL' && $seg=='Z01'){
			$seg_settings = $this->getSegmentSettings($this->interface_id,'Z01CL');
		}else{
			$seg_settings = $this->getSegmentSettings($this->interface_id,$seg);
		}
		$seg_line = '';
		switch($seg){
			case 'MSH':{
				$seg_line .= $this->MSH($seg_settings);				
				break;	
			}
			case 'EVN':{
				$seg_line .= $this->EVN($seg_settings);
				break;	
			}
			default:{
				if($this->msgType=='ADT') $this->patient_id = $this->source_id;
				if($this->msgType=='ZMS' && $this->ZMSgivenMR && !empty($this->ZMSgivenMR) && substr($this->ZMSgivenMR,0,2)=='MR' && $seg=='Z02') return '';// Z02 not sent in MR.
				if($this->msgType=='ZMS' && $this->ZMSgivenMR && !empty($this->ZMSgivenMR) && substr($this->ZMSgivenMR,0,2)=='CL' && $seg=='Z03') return '';// Z03 not sent in CL.
				$seg_line .= $this->HL7segment($seg,$seg_settings,$this->msgType,$this->source_id);
				break;	
			}
		}
		return $seg_line;
	}
	
	function SIU_MSG_Types($appt_status_id){
		$segmentsRS = $this->get_segments_message_type_wise($this->interface_id,$this->msgType);
		$valid_status_ids = explode(',',$segmentsRS['trigger_events']);
		//$valid_status_ids = array(0, 11, 13, 18, 202, 203);
		if(in_array($appt_status_id,$valid_status_ids) || in_array('*',$valid_status_ids)){
			switch($appt_status_id){
				case 0:		return 'S12'; 	break;
				
				case 11:
				case 13:
				case 4: 
				case '*':	return 'S14'; 	break;
				
				case 18:	return 'S15';	break;
				case 202:	return 'S13';	break;
				case 203:	return 'S17';	break;
				default: return 'S14'; 	break;
			}
		}else{
			return false;
		}
	}
	
	function MSH($settings){
		$NewMsgId = $this->newMessageUniqueId();
		
		$msh['MSH'][] = $this->component_separator.$this->repetition_separator.$this->escape_char.$this->subcomponent_separator; //MSH.1 and MSH.2
		$msh['MSH'][] = $settings[3][0]['val']; //MSH.3 SENDING APPLICATION
		$msh['MSH'][] = $settings[4][0]['val']; //MSH.4 SENDING FACILITY
		$msh['MSH'][] = $settings[5][0]['val']; //MSH.5 RECEIVING APPLICATION
		$msh['MSH'][] = $settings[6][0]['val']; //MSH.6 RECEIVING FACILITY
		$msh['MSH'][] = date('YmdHis');			//MSH.7 DATE/TIME OF MSG
		$msh['MSH'][] = "";						//MSH.8 SECURITY
		
		$MSH9 = '';
		foreach($settings[9] as $setting){
			if($MSH9=='' && $setting['val_type']==$this->msgSubType){
				$MSH9 = $this->msgType.'^'.$setting['val'];
			}
		}
		if($this->msgType=='SIU'){
			$MSH9 = $this->msgType.'^'.$this->SIU_MSG_Types($this->msgSubType);
		}
		
		$msh['MSH'][] = $MSH9;					//MSH.9 MESSAGE tYPE
		$msh['MSH'][] = $NewMsgId;				//MSH.10 MESSAE CONTROL ID.
		$msh['MSH'][] = $this->interface_mode;	//MSH.11
		$msh['MSH'][] = $this->HL7_version;		//MSH.12
		$msh['MSH'][] = "";						//MSH.13
		$msh['MSH'][] = "";						//MSH.14
		$msh['MSH'][] = "";						//MSH.15
		$msh['MSH'][] = "";						//MSH.16
		$msh['MSH'][] = "";						//MSH.17
		$msh['MSH'][] = "ASCII";				//MSH.18
		$msh['MSH'][] = "";						//MSH.19
		$msh['MSH'][] = "";						//MSH.20
		$msh['MSH'][] = "";						//MSH.21
		$msh = $this->makeSegmentLineFromArray($msh);
		return $msh;
	}
	
	function EVN($settings)
	{
		$EVN1 = '';
		foreach($settings[1] as $setting){
			if($EVN1=='' && $setting['val_type']==$this->msgSubType){
				$EVN1 = $setting['val'];
			}
		}
		if($this->msgType=='SIU'){
			$EVN1 = $this->SIU_MSG_Types($this->msgSubType);
		}
		$evn['EVN'][] = $EVN1;
		$evn['EVN'][] = date('YmdHis');
		$evn['EVN'][] = "";
		$evn['EVN'][] = "";
		$evn['EVN'][] = strtoupper($_SESSION['authUser']);
		$evn['EVN'][] = "";
		$evn['EVN'][] = "";
		$evn = $this->makeSegmentLineFromArray($evn);
		return $evn;
	}
	
	function HL7segment($seg,$settings,$msgType,$source_id){
		$SEGMENT_LINE = '';
		$r = $this->makeSegmentQueryResult($seg,$settings,$msgType,$source_id);
		if($r){
			$SEGMENT_LINE = $this->makeSegmentLineFromResultset($r,$seg);
			return $SEGMENT_LINE;
		}
		return false;
	}
	
	function makeSegmentQueryResult($seg='',$settings,$msgType,$source_id=0){
		if(empty($seg)) return false;
		if($source_id<=0) return false;
		$q = "";
		foreach($settings as $vals){
			if(!empty($q)) $q .= ", ";
			$val_arr = explode('^',$vals[0]['val']);
			if(count($val_arr)>1){
				$vals[0]['val'] = "CONCAT(".implode(",'^',",$val_arr).")";
			}
			if(empty($vals[0]['val'])) $vals[0]['val'] = "''";
			$q .= $vals[0]['val']." AS ".$vals[0]['val_type'];
		}
		$q = str_replace(',,',',',$q);
		return $this->getQueryResult($q,$seg,$msgType,$source_id);
	}
	
	function makeSegmentLineFromResultset($result,$seg=''){
		if(empty($seg)) return false;
		$str = '';
		foreach($result as $a=>$rs){
			if(isset($this->segment_counter[$seg])){$this->segment_counter[$seg] = intval($this->segment_counter[$seg])+1;}
			else {$this->segment_counter[$seg] = 1;}
			//echo $seg.'='.$this->segment_counter[$seg].'<br>';
			
			$vals = array();
			$rs = $this->mapAndFormat($rs,$seg);
			foreach($rs as $key=>$val){
				if($val=='0000000000' || $val=='00000000'){$val = '';}
				$rs[$key] = $val;
				$vals[] = $val;
			}
			$str .= $seg.$this->field_separator.implode($this->field_separator,$vals);
			$str .= $this->segmentEnding;			
		}
		return $str;
	}
		
	function makeSegmentLineFromArray($dataARR){
		$str = '';
		foreach($dataARR as $segment=>$values){
			$values2 = array();
			foreach($values as $val){
				if($val=='0000000000' || $val=='00000000'){$val = '';}
				$values2[] = $val;
			}
			if($this->msgType=='ZMS' && $this->ZMSgivenMR && !empty($this->ZMSgivenMR) && substr($this->ZMSgivenMR,0,2)=='CL' && $segment=='Z01CL') $segment='Z01';
			$str .= $segment.$this->field_separator.implode($this->field_separator,$values2);
			$str .= $this->segmentEnding;
		}
		return $str;
	}
	
	function mapAndFormat($rs,$seg){
		foreach($rs as $k=>$v){
			preg_match('/\{(.*?)\}/', $v, $matches);
			if(count($matches)>0){
				$parseMethodName = $matches[1];
				$parseMethodArgs = array('patient_id'=>$this->patient_id,'source_id'=>$this->source_id,'segment'=>$seg,'message_type'=>$this->msgType,'interface_id'=>$this->interface_id,'message_subtype'=>$this->msgSubType);
				$v = $this->parse_variable($parseMethodName,$parseMethodArgs);
			}
			switch($seg){
				case 'PID':{
					if($k=='death_indicator'){
						if(strtolower($v)=='active') $v = 'N';
						else if(strtolower($v)=='deceased') $v = 'Y';
						else $v = '';
					}
					break;
				}
				case 'SCH':{
					if($k=='appointment_timing_quantity'){
						$v = '^^^'.str_replace(array(' ','-',':'),'',$v);
					}
					break;
				}
			}
			$rs[$k] = $v;
		}
		return $rs;
	}
	
	function parse_variable($parseMethodName, $parseMethodArgs=array()){
		if($parseMethodName=='SET_ID'){
			$format = $this->get_segment_value_format($parseMethodArgs['segment'],$parseMethodName);
			$set_cnt = $this->segment_counter[$parseMethodArgs['segment']];
			$format = str_replace('X','0',$format);
			$format = str_replace('N',$set_cnt,$format);
			return $format;
		}else{
			$objVarParser = new HL7Variables;
			return $objVarParser->{$parseMethodName}($parseMethodArgs);
		}
	}
	
	function post_message_for_parsing($data,$parsing_script,$msg_encryption,$connectionID=0){
		$data = str_replace(array(chr(11),chr(28),'"'),'',$data);
		if($msg_encryption == 'base64'){
			$data = base64_encode($data);
		}
		
		$data = urlencode($data);
		$myvars = 'connectionID='.$connectionID.'&data='.$data;
		$ch = curl_init( $parsing_script ); 
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		if(substr($parsing_script,0,5)=='https'){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		$response = curl_exec( $ch );
		if(curl_errno($ch)){
			$response.= 'ERROR: '.curl_error($ch);
		}
		curl_close($ch);
		return $response;
	}
	
	
}?>