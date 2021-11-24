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
Purpose: Providing required data to main interface of iMedicMonitor.
Access Type: include file.
*/
//require_once(dirname(__FILE__).'/common_function.php');
require_once(dirname(__FILE__).'/class.imm.db.php');
class iMMLogic
{
	private $iMMDb,$getUsersNTypes,$tz_value,$sort_by,$sort_order;
	###################################################################
	#	constructor function to set commonally used variable on page
	###################################################################
	function __construct($facId='',$provId='',$tz_value='',$sort_by='',$sort_order=''){
		$this->iMMDb 			= new immDb($facId,$provId,$tz_value,$sort_by,$sort_order);
		$this->getUsersNTypes 	= $this->iMMDb->getUsersNTypes();
		$this->tz_value			= $tz_value;
	}
	

	function getAllProvs(){
		return $this->iMMDb->allProvsRS;
	}
	
	function getAllFacs(){
		return $this->iMMDb->allFacsRS;
	}
	
	function getAllRooms($idwise=false){
		return $this->iMMDb->getRooms($idwise);
	}
	
	function ProviderReadyFor($page){
		$ProWiseReadyFor=$this->iMMDb->getReadyFor();
		$prev_provider = '';
		$ProWiseReady4 = array();
		if($ProWiseReadyFor){
			foreach($ProWiseReadyFor as $rs){
				if($prev_provider!=$rs['provider_id']){
					$ProWiseReady4[$rs['provider_id']]["task_1"] = array("name"=>"Doctor");
					if($page=='main'){
						$ProWiseReady4[$rs['provider_id']]["task_2"] = array("name"=>"Technician");
						$ProWiseReady4[$rs['provider_id']]["task_4"] = array("name"=>"Test/Waiting");
					}
					$ProWiseReady4[$rs['provider_id']]["sep1"] = "---------";
				}
				$ProWiseReady4[$rs['provider_id']]["taskc_".$rs['id']] = array("name"=>$rs['status_text']);
				$prev_provider = $rs['provider_id'];
			}
		}else{
			$ProWiseReady4['0']["task_1"] = array("name"=>"Doctor");
			if($page=='main'){
				$ProWiseReady4['0']["task_2"] = array("name"=>"Technician");
				$ProWiseReady4['0']["task_4"] = array("name"=>"Test/Waiting");
			}
			//$ProWiseReady4[$rs['provider_id']]["sep1"] = "---------";
		}	
		return $ProWiseReady4;
	}
	
	function getDDProvs($sel){
		$arr_sel_pro_ids = explode(',',$sel);
		$return = '';
		foreach($this->iMMDb->allProvsRS as $id=>$provname){
			$sel_dd_prov = '';
			if(in_array($id,$arr_sel_pro_ids)){$sel_dd_prov=' selected';}
			$return .= '<option value="'.$id.'"'.$sel_dd_prov.'>'.$provname.'</option>';
		}
		return $return;
	}
	
	function getDDFacs($sel){
		$return = '';
		foreach($this->iMMDb->allFacsRS as $id=>$facname){
			$sel_dd_facility = '';
			if($sel>0 && $sel==$id){$sel_dd_facility=' selected';}
			$return .= '<option value="'.$id.'"'.$sel_dd_facility.'>'.$facname.'</option>';
		}
		return $return;
	}
	
	function getSchData(){
		$appts 			= $this->iMMDb->getApptsToday();
		$apptStatus 	= $this->iMMDb->getApptStatus();
		$TodaysFS		= $this->iMMDb->FSprintToday();
		$returnDataArr = array();
		$i = 0;
		foreach($appts as $schId=>$schRS){
			//DATA FROM PATIENT MONITOR DAILY
			$patMonitorRS = $this->getPatientMonitorFinal($schId);
			
			$returnDataArr['sch'.$schId]['sch_id'] 				= $schId;
			$returnDataArr['sch'.$schId]['patient_name'] 			= $schRS['sa_patient_name'].' - '.$schRS['sa_patient_id'];
			$returnDataArr['sch'.$schId]['appt_status_id'] 		= $schRS['sa_patient_app_status_id'];
			$returnDataArr['sch'.$schId]['appt_doctor_id'] 		= $schRS['sa_doctor_id'];
			$returnDataArr['sch'.$schId]['appt_doctor_initials'] 	= $this->getUsersNTypes[$schRS['sa_doctor_id']]['initials'];
			$returnDataArr['sch'.$schId]['appt_doctor_name'] 		= $this->getUsersNTypes[$schRS['sa_doctor_id']]['user_name'];
			$returnDataArr['sch'.$schId]['appt_reason'] 			= $schRS['proc'];
			$returnDataArr['sch'.$schId]['appt_time'] 			= date_format(date_create($schRS['sa_app_starttime']),'h:i a');
			$returnDataArr['sch'.$schId]['arrival_time'] 			= isset($apptStatus[$schId][4]['show']) ? $apptStatus[$schId][4]['show'] : '';
			$returnDataArr['sch'.$schId]['checkin_time'] 			= isset($apptStatus[$schId][13]['show']) ? $apptStatus[$schId][13]['show'] : '';
			
			$fd_time = '';
			if(!empty($apptStatus[$schId][13]['show'])) 
			$fd_time=$this->getDiffInMinutes($apptStatus[$schId][4]['show'],$apptStatus[$schId][13]['show']);
			$returnDataArr['sch'.$schId]['fd_time'] 			= $fd_time;
			$returnDataArr['sch'.$schId]['fsheet'] 			= (is_array($TodaysFS) && in_array($schId,$TodaysFS)) ? 'Y' : 'N';

			$co_time 									= isset($patMonitorRS[$schId]['co_time']) ? $patMonitorRS[$schId]['co_time'] : '';
			
			if(isset($apptStatus[$schId][4]['db'])){
				$returnDataArr['sch'.$schId]['arrival_2now'] 		= $this->getDiffInMinutes($apptStatus[$schId][4]['db'],$co_time);
			}else {$returnDataArr['sch'.$schId]['arrival_2now'] = '';}		
			
			if(isset($apptStatus[$schId][13]['db'])){
				$returnDataArr['sch'.$schId]['checkin_2now'] 		= $this->getDiffInMinutes($apptStatus[$schId][13]['db'],$co_time);
				
				//if arrival time not recorded, check arrival_2now from appt_time
				$apptStartTime2Now = intval(str_ireplace(' mins','',$this->getDiffInMinutes(date('Y-m-d').' '.$schRS['sa_app_starttime'],date('Y-m-d H:i:s'),'mins')));
				if($apptStartTime2Now > 0 && !isset($apptStatus[$schId][4]['db'])){
					$returnDataArr['sch'.$schId]['arrival_2now'] 		= $this->getDiffInMinutes(date('Y-m-d').' '.$schRS['sa_app_starttime'],date('Y-m-d H:i:s'));
				}
			}else{$returnDataArr['sch'.$schId]['checkin_2now'] = '';}
			
			$returnDataArr['sch'.$schId]['tech_room'] 		= isset($patMonitorRS[$schId]['tech_room']) ? $patMonitorRS[$schId]['tech_room'] : '';
			$returnDataArr['sch'.$schId]['tech_start_time'] 	= isset($patMonitorRS[$schId]['tech_start_time']) ? $patMonitorRS[$schId]['tech_start_time'] : '';
			$returnDataArr['sch'.$schId]['tech_stop_time'] 	= isset($patMonitorRS[$schId]['tech_stop_time']) ? $patMonitorRS[$schId]['tech_stop_time'] : '';
			$returnDataArr['sch'.$schId]['tech_name'] 		= isset($patMonitorRS[$schId]['tech_name']) ? $patMonitorRS[$schId]['tech_name'] : '';
			$returnDataArr['sch'.$schId]['tech_total'] 		= isset($patMonitorRS[$schId]['TechTotal']) ? $patMonitorRS[$schId]['TechTotal'] : '';
			
			$returnDataArr['sch'.$schId]['phy_room'] 			= isset($patMonitorRS[$schId]['phy_room']) ? $patMonitorRS[$schId]['phy_room'] : '';
			$returnDataArr['sch'.$schId]['phy_start_time'] 	= isset($patMonitorRS[$schId]['phy_start_time']) ? $patMonitorRS[$schId]['phy_start_time'] : '';
			$returnDataArr['sch'.$schId]['phy_stop_time'] 	= isset($patMonitorRS[$schId]['phy_stop_time']) ? $patMonitorRS[$schId]['phy_stop_time'] : '';
			$returnDataArr['sch'.$schId]['phy_name'] 			= isset($patMonitorRS[$schId]['phy_name']) ? $patMonitorRS[$schId]['phy_name'] : '';
			$returnDataArr['sch'.$schId]['doc_total'] 		= isset($patMonitorRS[$schId]['DocTotal']) ? $patMonitorRS[$schId]['DocTotal'] : '';
			
			$returnDataArr['sch'.$schId]['dilation_time'] 	= isset($patMonitorRS[$schId]['dilation_time']) ? $patMonitorRS[$schId]['dilation_time'] : '';
			$returnDataArr['sch'.$schId]['dilation_timer'] 	= isset($patMonitorRS[$schId]['dilation_timer']) ? $patMonitorRS[$schId]['dilation_timer'] : '';
			
			//if DILATION elapsed time greated than pre-defined time.
			$presetDILtime 								= intval(constant('DEFAULT_DILATION_TIMER'))>0 ? intval(constant('DEFAULT_DILATION_TIMER')) : 15;
			//echo intval(str_ireplace(' mins','',$this->getDiffInMinutes($patMonitorRS[$schId]['dilation_time'],date('Y-m-d H:i:s'),'mins'))).'<br>';
			if($returnDataArr['sch'.$schId]['dilation_time'] != 'N/A' || stristr($returnDataArr['sch'.$schId]['dilation_time'],'refused')){
				if(intval(str_ireplace(' mins','',$this->getDiffInMinutes($patMonitorRS[$schId]['dilation_time'],date('Y-m-d H:i:s'),'mins'))) > $presetDILtime){
					$returnDataArr['sch'.$schId]['dilation_complete'] = 'Y';
				}
			}
			
			if($co_time && !empty($co_time) || $returnDataArr['sch'.$schId]['dilation_complete']=='Y'){
				$returnDataArr['sch'.$schId]['dilation_timer']= $this->getDiffInMinutes($patMonitorRS[$schId]['dilation_time'],$co_time);
			}
			
			$returnDataArr['sch'.$schId]['wait_total'] 		= isset($patMonitorRS[$schId]['WaitTotal']) ? $patMonitorRS[$schId]['WaitTotal'] : '';
			$returnDataArr['sch'.$schId]['co_time'] 			= !empty($co_time) ? date_format(date_create($co_time),'h:i a') : '';
			$returnDataArr['sch'.$schId]['pt_time_full']		= !empty($co_time) ? $this->getDiffInMinutes($apptStatus[$schId][4]['db'],$apptStatus[$schId][11]['db']):'';
			if(empty($returnDataArr['sch'.$schId]['pt_time_full'])){
				$returnDataArr['sch'.$schId]['pt_time_full']		= !empty($co_time) ? $this->getDiffInMinutes($apptStatus[$schId][13]['db'],$apptStatus[$schId][11]['db']):'';
			}
		}
		
		return $returnDataArr;
	}
	
	function getPatientMonitorFinal($schId){
		$masterArr = array();
		$patMonitorRS = $this->iMMDb->getPatientMonitor($schId);
	//	pre($patMonitorRS);
		$arrSubTot = array();
		$arrTime = $arrTechTime = $arrDocTime = $arrWaitingTime = array();
		foreach($patMonitorRS as $rs){

			$si = $rs['appt_id'];
			$ui = $rs['user_id'];
			$ut = $rs['user_type_id'];
			$at = $rs['action_time'];
			$an = $rs['action_name'];
			
			switch($an){
				case 'CHECK_IN':
					/*if(!isset($arrWaitingTime[$si]['START'])	){
						$arrWaitingTime[$si]['START'] = $at;
					}*/
					break;
				case 'CHART_OPEN':
					switch($ut){
						case '1':
							if(!isset($masterArr[$si]['phy_start_time']) || $masterArr[$si]['phy_start_time']==''){
								$masterArr[$si]['phy_name'] = $this->getUsersNTypes[$ui]['user_name'];
								$masterArr[$si]['phy_start_time'] = date_format(date_create($at),'h:i a');
								$masterArr[$si]['phy_room'] = $rs['app_room'];
								
							}
							if(!$arrDocTime[$si][$ui]['START']){
								$arrDocTime[$si][$ui]['START'] = $at;
							}
							if($arrWaitingTime[$si]['START'] && !$arrWaitingTime[$si]['STOP']){
								$arrWaitingTime[$si]['STOP'] = $at;
								$WaitTime = $this->getConsumedTime($arrWaitingTime[$si]['START'],$arrWaitingTime[$si]['STOP']);
								$tt = explode(':', $WaitTime);

								$arrWaitingTime[$si]['Hours'][]= $tt[0];
								$arrWaitingTime[$si]['Minutes'][]= $tt[1];
								$arrWaitingTime[$si]['Seconds'][]= $tt[2];
			
								$arrWaitingTime[$si]['START']='';
								$arrWaitingTime[$si]['STOP']='';
							}
							break;
						case '3':
							if(!isset($masterArr[$si]['tech_start_time']) || $masterArr[$si]['tech_start_time']==''){
								$masterArr[$si]['tech_name'] = $this->getUsersNTypes[$ui]['user_name'];
								$masterArr[$si]['tech_start_time'] = date_format(date_create($at),'h:i a');
								$masterArr[$si]['tech_room'] = $rs['app_room'];

							}
							if(!$arrTechTime[$si][$ui]['START']){
								$arrTechTime[$si][$ui]['START'] = $at;
							}
							/*if($arrWaitingTime[$si]['START'] && !$arrWaitingTime[$si]['STOP']){
								$arrWaitingTime[$si]['STOP'] = $at;
								$WaitTime = $this->getConsumedTime($arrWaitingTime[$si]['START'],$arrWaitingTime[$si]['STOP']);
								$tt = explode(':', $WaitTime);

								$arrWaitingTime[$si]['Hours'][]= $tt[0];
								$arrWaitingTime[$si]['Minutes'][]= $tt[1];
								$arrWaitingTime[$si]['Seconds'][]= $tt[2];
			
								$arrWaitingTime[$si]['START']='';
								$arrWaitingTime[$si]['STOP']='';
							}*/
							break;
					}					
					break;
				case 'CHART_CLOSE':
				case 'PATIENT_CLOSE':
				case 'SWITCH_USER_FORM':
				case 'DONE_WITH_PT':
				case 'STATUS_CHANGED':
				case 'CHECK_OUT':
				case 'FINALIZED':
				case 'READY FOR DOCTOR':
				case 'READY FOR TECHNICIAN':
				case 'DONE':
				case 'READY FOR WAITING ROOM':
				case 'READY FOR TEST':
					switch($ut){
						case '1':
							if(!$masterArr[$si]['finalized_time'] && $arrDocTime[$si][$ui]['START'] && !$arrDocTime[$si][$ui]['STOP']){
								$arrDocTime[$si][$ui]['STOP'] = $at;
								$masterArr[$si]['phy_stop_time'] = date_format(date_create($at),'h:i a');
								$DocTime = $this->getConsumedTime($arrDocTime[$si][$ui]['START'],$arrDocTime[$si][$ui]['STOP']);
								$tt = explode(':', $DocTime);

								$arrDocTime[$si]['Hours'][]= $tt[0];
								$arrDocTime[$si]['Minutes'][]= $tt[1];
								$arrDocTime[$si]['Seconds'][]= $tt[2];
			
								$arrDocTime[$si][$ui]['START']='';
								$arrDocTime[$si][$ui]['STOP']='';
							}
							break;
						case '3':
							if(!$masterArr[$si]['finalized_time'] && $arrTechTime[$si][$ui]['START'] && !$arrTechTime[$si][$ui]['STOP']){
								$arrTechTime[$si][$ui]['STOP'] = $at;
								$masterArr[$si]['tech_stop_time'] = date_format(date_create($at),'h:i a');
								$masterArr[$si]['tech_stop_time_for_wait'] = $at;
								$TechTime = $this->getConsumedTime($arrTechTime[$si][$ui]['START'],$arrTechTime[$si][$ui]['STOP']);
								$tt = explode(':', $TechTime);
								
								$arrTechTime[$si]['Hours'][]= $tt[0];
								$arrTechTime[$si]['Minutes'][]= $tt[1];
								$arrTechTime[$si]['Seconds'][]= $tt[2];
			
								$arrTechTime[$si][$ui]['START']='';
								$arrTechTime[$si][$ui]['STOP']='';
							}
							
							//START WAIT TIME ON EVEN WHEN TECH OR PHY LEAVE PATIENT.
							if(!$masterArr[$si]['dilation_time']){
								$arrWaitingTime[$si]['START'] = $at;
							}  //condition commented to record last tech left time.
							break;
					}
					
					
					if($an=='FINALIZED' && !$masterArr[$si]['finalized_time']){
						$masterArr[$si]['finalized_time'] = date_format(date_create($at),'h:i a');
						if($arrTechTime[$si][$ui]['START'] && !$arrTechTime[$si][$ui]['STOP']){
							$masterArr[$si]['tech_stop_time'] = date_format(date_create($at),'h:i a');
						}
						if($arrDocTime[$si][$ui]['START'] && !$arrDocTime[$si][$ui]['STOP']){
							$masterArr[$si]['phy_stop_time'] = date_format(date_create($at),'h:i a');
						}
						
					}
					
					if($an=='CHECK_OUT' && !$masterArr[$si]['co_time']){
						$masterArr[$si]['co_time'] = $at;
						if($masterArr[$si]['dilation_timer']){
							$masterArr[$si]['dilation_timer']='';
						}
					}
					break;
				case 'DILATION':
					//if(!$masterArr[$si]['dilation_time']){
						$masterArr[$si]['dilation_time']	= date_format(date_create($at),'h:i a');
						$masterArr[$si]['dilation_timer']	= $this->convertTZtime(date('Y-m-d').' '.$at);
						$arrWaitingTime[$si]['START'] 		= $at;
				//	}
					break;
				case 'NO_DILATION':
					//if(!$masterArr[$si]['dilation_time']){
						$masterArr[$si]['dilation_time']	= 'N/A';
						$masterArr[$si]['dilation_timer']	= '';
						$arrWaitingTime[$si]['START'] 		= '';
				//	}
					break;
				case 'REFUSED_DILATION':
					//if(!$masterArr[$si]['dilation_time']){
						$masterArr[$si]['dilation_time']	= date_format(date_create($at),'h:i a').'<br>Refused Dilation';
						$masterArr[$si]['dilation_timer']	= '';
						$arrWaitingTime[$si]['START'] 		= '';
				//	}
					break;	
				default:
			}
			
			/*if($arrWaitingTime[$si]['START'] && !$arrWaitingTime[$si]['STOP'] && !$masterArr[$si]['phy_start_time']){
				$arrWaitingTime[$si]['STOP'] = date('Y-m-d H:i:s');
				$WaitTime = $this->getConsumedTime($arrWaitingTime[$si]['START'],$arrWaitingTime[$si]['STOP']);
				$tt = explode(':', $WaitTime);

				$arrWaitingTime[$si]['Hours'][]= $tt[0];
				$arrWaitingTime[$si]['Minutes'][]= $tt[1];
				$arrWaitingTime[$si]['Seconds'][]= $tt[2];

				//$arrWaitingTime[$si]['START']='';
			//	$arrWaitingTime[$si]['STOP']='';
			}
			*/
			
			foreach($arrTechTime as $sid=>$TechTime){
				$tot = $this->getTotTime(array_sum($TechTime['Hours']),array_sum($TechTime['Minutes']),array_sum($TechTime['Seconds']));
				$masterArr[$si]['TechTotal'] = $this->makeShowTime($tot);
			}
			foreach($arrDocTime as $sid=>$DocTime){
				$tot = $this->getTotTime(array_sum($DocTime['Hours']),array_sum($DocTime['Minutes']),array_sum($DocTime['Seconds']));
				$masterArr[$si]['DocTotal'] = $this->makeShowTime($tot);
			}
			
			foreach($arrWaitingTime as $sid=>$WaitingTime){
				$wh = isset($WaitingTime['Hours']) ? array_sum($WaitingTime['Hours']) : 0;
				$wm = isset($WaitingTime['Minutes']) ? array_sum($WaitingTime['Minutes']) : 0;
				$ws = isset($WaitingTime['Seconds']) ? array_sum($WaitingTime['Seconds']) : 0;
				$tot = $this->getTotTime($wh,$wm,$ws);
				$masterArr[$si]['WaitTotal'] = $this->makeShowTime($tot);
			}
			unset($tot);
		}
		//pre($masterArr);
		return $masterArr;
	}
	
	function convertTZtime($tm){
		if(empty($this->tz_value)) return '';
		$q 		= "SELECT CONVERT_TZ('".$tm."','".date_default_timezone_get()."','".$this->tz_value."') AS dilated_timer";
		$res 	= imw_query($q);
		$rs 	= imw_fetch_assoc($res);	
		return $rs['dilated_timer'];
	}
	
	function getDiffInMinutes($dtTime1='',$dtTime2='',$return=''){
		if(empty($dtTime1)) return '';
		if(empty($dtTime2)) $dtTime2 = date('Y-m-d H:i:s');
		$to_time = strtotime($dtTime2);
		$from_time = strtotime($dtTime1);
		$mins = round(abs($to_time - $from_time) / 60,0);
//		if($mins>1) $mins=$mins-1;
		$hr = floor($mins / 60);
		$mins = ($mins -   $hr * 60);
		$hrtext = ' hr '; if($hr>1) $hrtext = ' hrs ';
		$mintext = ' min'; if($mins>1) $mintext = ' mins';
		if(($hr == 0 && $mins < 60) || $return=='mins'){if($mins>1) $mintext = ' mins'; return ($mins).$mintext;}
		else {
			if($mins>1) $mintext = ' mins';
			return $hr.$hrtext.$mins.$mintext;	
		}
	}
	
	function makeShowTime($hhmmss){
		$hh = $mins = $secs = 0;
		$tmp = explode(':',$hhmmss);
		
		$hh   = isset($tmp[0]) ? $tmp[0] : 0;
		$mins = isset($tmp[1]) ? $tmp[1] : 0;
		$secs = isset($tmp[2]) ? $tmp[2] : 0;		
		
		$hrtext = ' hr '; if($hh>1) $hrtext = ' hrs ';
		$mintext = ' min'; if($mins>1) $mintext = ' mins ';
		$sectext = ' sec'; if($secs>1) $sectext = ' secs';
		$text = '';
		if($hh >= 1) $text .= intval($hh).$hrtext;
		if($mins > 0) $text .= intval($mins).$mintext;
		if($secs > 0) $text .= intval($secs).$sectext;
		return $text;
	}
	
	function getConsumedTime($startTime, $endTime){
		$docTime='';
		$seconds = strtotime($endTime) - strtotime($startTime);
		if($seconds<60){
			$seconds= $seconds;
		}else{
			$minutes = floor($seconds/60);
			$seconds = $seconds%60;
			if($minutes>60) {
				$hour=floor($minutes/60);
				$minutes = $minutes%60;
			}else{
				$minutes= $minutes;
			}
		}
		if($hour>0 || $minutes>0 || $seconds>0){
			$hour= ($hour>23) ? '00' : (($hour<10) ? '0'.$hour : $hour);
			$minutes= ($minutes<10) ? '0'.$minutes : $minutes;
			$seconds= ($seconds<10) ? '0'.$seconds : $seconds;

			$hour= ($hour==0) ? '00' : $hour;
			$minutes= ($minutes==0) ? '00' : $minutes;
			$seconds= ($seconds==0) ? '00' : $seconds;

			$docTime = $hour.':'.$minutes.':'.$seconds;
		}
		
		return $docTime;
	}
	
	function getTotTime($tH, $tM, $tS){
		$docTime='';
		if($tS>59) {
			$tM+=floor($tS/60);
			$tS=$tS%60;
		}
		if($tM>59) {
			$tH+=floor($tM/60);
			$tM=floor($tM%60);
		}
		
		if($tH>0 || $tM>0 || $tS>0){
			$tH= ($tH<10) ? '0'.$tH : $tH;
			$tM= ($tM<10) ? '0'.$tM : $tM;
			$tS= ($tS<10) ? '0'.$tS : $tS;

			$tH= ($tH==0) ? '00' : $tH;
			$tM= ($tM==0) ? '00' : $tM;
			$tS= ($tS==0) ? '00' : $tS;

			$docTime = $tH.':'.$tM.':'.$tS;
		}
		
		return $docTime;
	}
	
	function getImmExtendedColList(){
		return $this->iMMDb->getImmExtendedCols();
	}
	
	function get_imm_saved_configuration(){
		return $this->iMMDb->get_imm_configuration();	
	}
}
?>