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

/*
 *File: hl7Create.php
 *Puppose: General Purpose HL7 message generation Class
 *Access Type: Include
 */


require_once(dirname(__FILE__)."/hl7Base.php");

class hl7Create extends hl7Base{
	
	public $data, $authorId, $useCase;
	protected $receiving = array();
	protected $sending = array();
	
	public static $stripVals = array("telephone_number", "dob", "plan_effective_date", "plan_expire_date", "new_comment_date", "actInsCompDate", "cardscan_date", "cardscan1_datetime", "insured_dob", "signSurgeon1DateTime", "signNurseDateTime", "signAnesthesia1DateTime", "signWitness1DateTime", "form_created_date", "contract_effective_date", "transfer_bad_debt_date", "delete_account_date", "admit_date_rime", "discharge_date_time", "date_time", "start_date", "end_date", "start_date_time", "end_date_time", "date_time_transaction", "requested_date_time", "observation_date_time", "observation_end_date_time", "specimen_received_date_time", "result_rpt_change_date_time", "home", "cell");
	public static $find = array("-", ")", "(", ":", " ");
	public static $replace = array("", "", "", "", "");
	
	public static $find1 = array("\r\n", "\r", "\n");
	
	public static $mapVals = array("race", "ethnic_group", "primary_language", "marital_status", "relationship", "contact_role", "sex");
	
	public $msgtypes = array();
	
	/*Constructor Elements*/
	public function init()
	{
		$this->authorId = isset($_SESSION['authId']) ? intval($_SESSION['authId']) : 0;
		
		/*ADT Message - Trigger Event = Update*/
		$this->msgtypes['ADT']['segments'] = array("PV1", "NTE", "NK1", "GT1", "IN1", "ZIN", "ZNC", "ZAL");
		$this->msgtypes['ADT']['trigger_event'] = "A08";
		$this->msgtypes['ADT']['msg_structure'] = "ADT_A01";
		
		/*MDM Message - Trigger Event = Original Document Notification and Content*/
		$this->msgtypes['MDM']['segments'] = array("ZNS", "OBX", "ZOB");
		$this->msgtypes['MDM']['trigger_event'] = "T02";
		$this->msgtypes['MDM']['msg_structure'] = "MDM_T02";
		
		/*SIU Message - Trigger Event = Notification of Appointment Modification*/
		$this->msgtypes['SIU']['segments'] = array("PR1", "ZPR", "SCH", "PV1", "IN1", "AIL", "AIP","LOC", "NK1");
		$this->msgtypes['SIU']['trigger_event'] = "S14";
		$this->msgtypes['SIU']['msg_structure'] = "SIU_S12";
		
		/*ORU Message - Trigger Event = Unsolicited transmission of an observation message*/
		$this->msgtypes['ORU']['segments'] = array("OBX", "AL1", "ZOR");
		$this->msgtypes['ORU']['trigger_event'] = "R01";
		$this->msgtypes['ORU']['msg_structure'] = "";
		
		/*VXR Message - Trigger Event = Query for vaccination Record*/
		$this->msgtypes['VXR']['segments'] = array("RXA");
		$this->msgtypes['VXR']['trigger_event'] = "V01";
		$this->msgtypes['VXR']['msg_structure'] = "";
		
		/*ORM message - trigger Event = Laboratory Order*/
		$this->msgtypes['ORM']['segments'] = array("ORC", "OBR");
		$this->msgtypes['ORM']['trigger_event'] = "O01";
		$this->msgtypes['ORM']['msg_structure'] = "";
		
		/*ACK Message Acknowledgement*/
		$this->msgtypes['ACK']['segments'] = array("MSA");
		$this->msgtypes['ACK']['trigger_event'] = "A08";
		$this->msgtypes['ACK']['msg_structure'] = "";
	}
  
	public function addmsh($type, $trigger, $structure){
	  	if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('ohioeye','berkeleyeye','utaheye','cumberlandvalleyretina'))) $this->hl7Version = '2.6';
		
		$data = array();
		$msg_id = $this->newMessageUniqueId();
		//$data['field_seperator'] = $this->fieldSeparator;
		$data['encoding_characters'] = $this->componentSeparator.$this->repeatFieldSeperator.$this->escapeCharacter.$this->subcomponentSeparator;
		$data['sending_application'] = (isset($this->sending['application']))?$this->sending['application']:"IMW";
		$data['sending_facility'] = (isset($this->sending['facility']))?$this->sending['facility']:"IMW";
		$data['receiving_application'] = (isset($this->receiving['application']))?$this->receiving['application']:"IMW";
		$data['receiving_facility'] = (isset($this->receiving['facility']))?$this->receiving['facility']:"IMW";
		$data['date_time'] = date("YmdHis");
		$data['message_type']['type'] = $type;
		$data['message_type']['trigger_event'] = $trigger;
		$data['message_type']['structure'] = $structure;
		$data['message_control_id'] = $msg_id;
		$data['processing_id'] = "P";
		$data['version_id'] = $this->hl7Version;
		return($this->addSegment("MSH", $data));
	}
  
	public function addSegment($segment, $data, $type=""){
	  
		$this->segmenData = $this->getSegmentStructure($segment);

		if(isset($data['setId_'.$segment])){
			$data['setId_'.$segment] = str_pad($data['setId_'.$segment], 3, 0, STR_PAD_LEFT);
		}
		
		$this->data = $data;
		$this->processData();
		
		foreach($this->segmenData as $key=>$data){
			if(is_array($data)){
				foreach($data as $key1=>$data1){
					if(is_array($data1)){
						$data[$key1] = implode($this->subcomponentSeparator, $data1);
					}
				}
				$this->segmenData[$key] = implode($this->componentSeparator, $data);
			}
		}
		if($segment == "MSH"){
			return(implode($this->fieldSeparator, $this->segmenData));
		}
		else{
			if($type==""){
				$this->message[$segment][] = implode($this->fieldSeparator, $this->segmenData);
			}
			else{
				$this->message[$type][$segment][] = implode($this->fieldSeparator, $this->segmenData);
			}
		}
	}
  
	private function processData() {
		foreach($this->data as $key=>$data){
			if(is_array($data)){
				foreach($data as $key1=>$data1){
					if(isset($this->segmenData[$key][$key1])){
						if(is_array($data1)){
							foreach($data1 as $key2=>$data2){
								if(isset($this->segmenData[$key][$key1][$key2])){
									$this->segmenData[$key][$key1][$key2] = $data2;
								}
							}
						}
						else{
							$this->segmenData[$key][$key1] = $data1;
						}
					}
				}
			}
			else{
				if(isset($this->segmenData[$key])){
					$this->segmenData[$key] = $data;
				}
			}
		}
		
		if( $this->segmenData['segment_id'] !== 'MSH'	)
			array_walk_recursive($this->segmenData, 'hl7Create::process');
	}
  
	public static function process(&$item, $key){
		$item = trim($item);
		if($key != 'encoding_characters'){
			$item = str_replace("&", "\&" , $item);
			$item = addslashes($item);
		}
		if($key == "doc_data"){$item = urlencode($item);}
		
		$item = str_replace(hl7Create::$find1, ",", $item);
		
		/*Adding Escape Characters*/
		$item = str_replace(array('^', '~'), array('\^', '\~'), $item);
		
		if(in_array($key, hl7Create::$stripVals)){
			$item = str_replace(hl7Create::$find, hl7Create::$replace, $item);
		}
		elseif(in_array($key, hl7Create::$mapVals)){
			if($item!=""){
				$item = mapVals::val($key, $item);
			}
		}
	}
  
	public function getMessage($combined=1){
		
		$msg = array();
		foreach($this->msgtypes as $key=>$vals){
			
			$segms = array();
			
			if(isset($this->message[$key])){
				$segms[] = $this->addmsh($key, $vals['trigger_event'], $vals['msg_structure']);
				if(isset($this->message["EVN"])){$segms[] = implode(chr(13), $this->message["EVN"]);}
				
				if( isset($this->message["PID"]) )
				{
					$segms[] = implode(chr(13), $this->message["PID"]);
				}
				
				if(count(array_intersect($vals['segments'], array_keys($this->message[$key])))>0){
					
					foreach($vals['segments'] as $segm){
						if(isset($this->message[$key][$segm])){
							$segms[] = implode(chr(13), $this->message[$key][$segm]);
						}
						elseif(isset($this->message[$segm])){
							$segms[] = implode(chr(13), $this->message[$segm]);
						}
					}
				}
				else{ }
			}
			else{
				if(count(array_intersect($vals['segments'], array_keys($this->message)))>0){
					$segms[] = $this->addmsh($key, $vals['trigger_event'], $vals['msg_structure']);
					if($key!="ACK" && isset($this->message["PID"]))
						$segms[] = implode(chr(13), $this->message["PID"]);
					
					foreach($vals['segments'] as $segm){
						if(isset($this->message[$segm])){
							$segms[] = implode(chr(13), $this->message[$segm]);
						}
					}
				}
				else{ }
			}
			if(count($segms)>0){
				if($key=="SIU"){ /*Moving SCH segment before PID in SIU messages*/
					$pid = $segms[1]; $sch = $segms[2];
					if(substr($pid, 0, 3)=="PID" && substr($sch, 0, 3)=="SCH"){
						$segms[1] = $sch; $segms[2] = $pid;
					}
				}
				$msg[] = implode(chr(13), $segms);
			}
		}
		if($combined==1){
			$msg = implode(chr(13).chr(28).chr(13), $msg);
		}
		return($msg);
	}

	public function encode_file($file){
		$file = realpath($file);
		$fp = fopen($file, "rb");
		$dt = fread($fp, filesize($file));
		$contents = base64_encode($dt);
		fclose($fp);
		return($contents);
	}
	
	public function decode_file($data){
		$data = base64_decode($data);
		return($data);
	}
	
	
	/*
	 *	Function to get message's unisque id
	 *	Copied from "interface\patient_info\CLS_makeHL7.php"
	 */
	private function newMessageUniqueId(){
		$NewMsgId="";
		if(strtoupper($this->useCase)=="IOLINK"){
			$nId = file_get_contents($this->filePath."/iolinkMsgId.txt");
			$nId = unserialize($nId);			
			$NewMsgId = $nId['iolink'];
			$nId['iolink']++;
			$nId = serialize($nId);
			file_put_contents($this->filePath."iolinkMsgId.txt", $nId);
			return($NewMsgId);
		}
		
		$hl7_table_name = $this->hl7SentTable();
		
		$res1 = imw_query("SELECT if(MAX(id) IS NULL,0,MAX(id))+1  as NewMsgId FROM ".$hl7_table_name);
		if($res1 && imw_num_rows($res1)==1){
			$rs1 = imw_fetch_assoc($res1);
			$NewMsgId = $rs1['NewMsgId'];
			$set_number = $NewMsgId * 1000;
			$set_number = substr($set_number,0,7);
			$set_number = $set_number + $NewMsgId;
			return $set_number;
		}
	}
	
	public function hl7SentTable()
	{
		$tableName = 'hl7_sent';
		if( defined('HL7_SENT_TABLE') && constant('HL7_SENT_TABLE') !== '' ){
			$tableName = constant('HL7_SENT_TABLE');
		}
		return $tableName;
	}
	
	
}

class mapVals{
  private static $languageTypes = array('primary_language');
  private static $relationTypes = array('relationship');
  public static function val($key, $val, $flag=0){
    
	if(in_array($key, array('primary_language','marital_status')) && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('ohioeye','berkeleyeye','utaheye'))) return $val;
	
    if(in_array($key, mapVals::$languageTypes)){
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('visionfirst','eyelasikcenter'))) return $val;
      $key = "LANGUAGE";
    }
	if(in_array($key, mapVals::$relationTypes)){
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('eyelasikcenter'))) return $val;
    }
	
	$key = strtoupper($key);
    $val = strtoupper($val);
	
	if($key == 'RACE' && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))) $key = 'RACE3';
	if($key == 'ETHNIC_GROUP' && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))) $key = 'ETHNIC_GROUP2';
	if($key == 'MARITAL_STATUS' && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))) $key = 'MARITAL_STATUS2';

    /*Race*/
      /*HL7 Standard Values*/
	$data['RACE']['ASIAN'] = "2028-9";
	$data['RACE']['BLACK'] = "2054-5";
	$data['RACE']['BLACK OR AFRICAN AMERICAN'] = "2054-5";
	$data['RACE']['HAWAIIAN/PAC ISLAND'] = "2076-8";
	$data['RACE']['NATIVE HAWAIIAN OR OTHER PACIFIC ISLANDER'] = "2076-8";
	$data['RACE']['OTHER'] = "2131-1";
    $data['RACE']['OTHER RACE'] = "2131-1";
	$data['RACE']['WHITE'] = "2106-3";
	$data['RACE']['AMERICAN INDIAN OR ALASKA NATIVE'] = "1002-5";
      /*Custom Values*/
    $data['RACE']['HISPANIC'] = "1";
	$data['RACE']['INDIAN'] = "2";
	$data['RACE']['NATIVE AMERICAN'] = "3";
	$data['RACE']['UNKNOWN'] = "4";
    $data['RACE']['LATIN AMERICAN'] = "5";
    $data['RACE']['DECLINED TO SPECIFY'] = "6";
    
	//RACE3 IS FOR VALLEY EYE/SPECTRUM.
	$data['RACE3']['AMERICAN INDIAN OR ALASKA NATIVE']				= "2D8262BD-97C4-4419-B4C4-C68EABFC46C8";
	$data['RACE3']['ASIAN'] 										= "2363FBA6-9A55-4A76-8B04-FCA2A6FC1183";
	$data['RACE3']['BLACK OR AFRICAN AMERICAN'] 					= "87234E85-7E8E-42D7-9473-192DF2F3B241";
	$data['RACE3']['NATIVE HAWAIIAN OR OTHER PACIFIC ISLANDER'] 	= "7174CAB1-F840-4425-A587-90E383AE0DF0";
	$data['RACE3']['WHITE']											= "78721AA6-2A28-477E-8EF6-D4D16A3962CE";	
	$data['RACE3']['OTHER'] 										= "75DA84A8-A06A-4916-958E-77447363FEC7";	
	$data['RACE3']['UNKNOWN'] 										= "75DA84A8-A06A-4916-958E-77447363FEC7";
	$data['RACE3']['DECLINED TO SPECIFY'] 							= "11111111-1234-5678-3333-111122223333";
	
    /*Ethnicity*/
      /*HL7 Standard Values*/
    $data['ETHNIC_GROUP']['HISPANIC OR LATINO'] = "H";
    $data['ETHNIC_GROUP']['NOT HISPANIC OR LATINO'] = "N";
    $data['ETHNIC_GROUP']['UNKNOWN']	= "U";
    $data['ETHNIC_GROUP']['UNKNOWN/NOT SPECIFIED']	= "U";
    $data['ETHNIC_GROUP']['DECLINED TO SPECIFY'] = "U";
      /*Custom Values*/
    $data['ETHNIC_GROUP']['AFRICAN AMERICANS'] = "AFA";
    $data['ETHNIC_GROUP']['AFGANISTANI'] = "AFG";
    $data['ETHNIC_GROUP']['AFRICAN'] = "AFR";
    $data['ETHNIC_GROUP']['ALBANIAN'] = "ALB";
    $data['ETHNIC_GROUP']['AMERICAN'] = "AME";
    $data['ETHNIC_GROUP']['ARGENTINEAN'] = "ARG";
    $data['ETHNIC_GROUP']['ARMENIAN'] = "ARM";
    $data['ETHNIC_GROUP']['ASIAN INDIAN'] = "ASI";
    $data['ETHNIC_GROUP']['ASIAN'] = "ASN";
    $data['ETHNIC_GROUP']['ASSYRIAN'] = "ASR";
    $data['ETHNIC_GROUP']['BARBADIAN'] = "BAR";
    $data['ETHNIC_GROUP']['BANGLADESHI'] = "BGL";
    $data['ETHNIC_GROUP']['BHUTANESE'] = "BHU";
    $data['ETHNIC_GROUP']['BELIZE'] = "BLZ";
    $data['ETHNIC_GROUP']['BOLIVIAN']	= "BOL";
    $data['ETHNIC_GROUP']['BOSNIAN'] = "BOS";
    $data['ETHNIC_GROUP']['BRAZILIAN'] = "BRZ";
    $data['ETHNIC_GROUP']['BURMESE'] = "BUR";
    $data['ETHNIC_GROUP']['CENTRAL AMERICAN INDIAN'] = "CAI";
    $data['ETHNIC_GROUP']['CAMBODIAN'] = "CBD";
    $data['ETHNIC_GROUP']['CARIBBEAN ISLAND'] = "CBI";
    $data['ETHNIC_GROUP']['CHINESE'] = "CHI";
    $data['ETHNIC_GROUP']['CHILEAN'] = "CHL";
    $data['ETHNIC_GROUP']['CENTRAL AMERICAN'] = "CNA";
    $data['ETHNIC_GROUP']['COLUMBIAN'] = "COL";
    $data['ETHNIC_GROUP']['COSTA RICAN'] = "COS";
    $data['ETHNIC_GROUP']['CRIOLLO'] 	= "CRI";
    $data['ETHNIC_GROUP']['CROATIAN'] = "CRO";
    $data['ETHNIC_GROUP']['CUBAN'] = "CUB";
    $data['ETHNIC_GROUP']['CAPE VERDEAN'] = "CVD";
    $data['ETHNIC_GROUP']['DOMINICA ISLANDER'] = "DMI";
    $data['ETHNIC_GROUP']['DOMINICAN'] = "DOM";
    $data['ETHNIC_GROUP']['ECUADORIAN'] = "ECU";
    $data['ETHNIC_GROUP']['EGYPTIAN'] = "EGY";
    $data['ETHNIC_GROUP']['ENGLISH'] = "ENG";
    $data['ETHNIC_GROUP']['EASTERN EUROPEAN'] = "EST";
    $data['ETHNIC_GROUP']['ETHIOPIAN'] = "ETH";
    $data['ETHNIC_GROUP']['EUROPEAN'] = "EUR";
    $data['ETHNIC_GROUP']['FILIPINO'] = "FIL";
    $data['ETHNIC_GROUP']['FRENCH'] = "FRE";
    $data['ETHNIC_GROUP']['GERMAN'] = "GER";
    $data['ETHNIC_GROUP']['GHANA'] = "GHA";
    $data['ETHNIC_GROUP']['GREEK'] = "GRK";
    $data['ETHNIC_GROUP']['GUATEMALAN'] = "GTM";
    $data['ETHNIC_GROUP']['GUYANA'] = "GUY";
    $data['ETHNIC_GROUP']['HMONG'] = "HMO";
    $data['ETHNIC_GROUP']['HONDURAN'] = "HON";
    $data['ETHNIC_GROUP']['HAITIAN'] = "HTN";
    $data['ETHNIC_GROUP']['INDONESIAN'] = "IDO";
    $data['ETHNIC_GROUP']['IRANIAN'] = "IRA";
    $data['ETHNIC_GROUP']['IRISH'] = "IRE";
    $data['ETHNIC_GROUP']['IRAQI'] = "IRQ";
    $data['ETHNIC_GROUP']['ISRAELI'] = "ISR";
    $data['ETHNIC_GROUP']['ITALIAN'] = "ITA";
    $data['ETHNIC_GROUP']['IWO JIMAN'] = "IWO";
    $data['ETHNIC_GROUP']['JAMAICAN'] = "JAM";
    $data['ETHNIC_GROUP']['JAPANESE'] = "JAP";
    $data['ETHNIC_GROUP']['KOREAN'] = "KOR";
    $data['ETHNIC_GROUP']['LAOTIAN'] = "LAO";
    $data['ETHNIC_GROUP']['LEBANESE'] = "LEB";
    $data['ETHNIC_GROUP']['LIBERIAN'] = "LIB";
    $data['ETHNIC_GROUP']['MADAGASCAR'] = "MAD";
    $data['ETHNIC_GROUP']['MALAYSIAN'] = "MAL";
    $data['ETHNIC_GROUP']['MEXICAN']	= "MEX";
    $data['ETHNIC_GROUP']['MEX AMER'] = "MEX";
    $data['ETHNIC_GROUP']['CHICANO'] = "MEX";
    $data['ETHNIC_GROUP']['MIDDLE EASTERN'] = "MID";
    $data['ETHNIC_GROUP']['MALDIVIAN'] = "MLD";
    $data['ETHNIC_GROUP']['NEPALESE'] = "NEP";
    $data['ETHNIC_GROUP']['NICARAGUAN'] = "NIC";
    $data['ETHNIC_GROUP']['NIGERIAN'] = "NIG";
    $data['ETHNIC_GROUP']['OKINAWAN'] = "OKI";
    $data['ETHNIC_GROUP']['OTHER ETHNICITY'] = "OTH";
    $data['ETHNIC_GROUP']['PAKISTANI'] = "PAK";
    $data['ETHNIC_GROUP']['PALESTINIAN'] = "PAL";
    $data['ETHNIC_GROUP']['PANAMANIAN'] = "PAN";
    $data['ETHNIC_GROUP']['POLISH'] 	= "POL";
    $data['ETHNIC_GROUP']['PORTUGUESE'] = "POR";
    $data['ETHNIC_GROUP']['PUERTO RICAN'] = "PRC";
    $data['ETHNIC_GROUP']['PARAGUAYAN'] = "PRG";
    $data['ETHNIC_GROUP']['PERUVIAN'] 	= "PRV";
    $data['ETHNIC_GROUP']['RUSSIAN'] 	= "RUS";
    $data['ETHNIC_GROUP']['SOUTH AMERICAN INDIAN']			= "SAI";
    $data['ETHNIC_GROUP']['SALVADORAN'] = "SAL";
    $data['ETHNIC_GROUP']['SOUTH AMERICAN'] 	= "SAM";
    $data['ETHNIC_GROUP']['SCOTTISH'] 	= "SCO";
    $data['ETHNIC_GROUP']['SINGAPOREAN'] = "SIN";
    $data['ETHNIC_GROUP']['SIERRA LEONIAN'] 	= "SLE";
    $data['ETHNIC_GROUP']['SOMALI'] 	= "SOM";
    $data['ETHNIC_GROUP']['SPANISH'] 	= "SPA";
    $data['ETHNIC_GROUP']['SRI LANKAN'] = "SRI";
    $data['ETHNIC_GROUP']['SYRIAN'] 	= "SYR";
    $data['ETHNIC_GROUP']['TAIWANESE'] = "TAI";
    $data['ETHNIC_GROUP']['THAI']  = "THA";
    $data['ETHNIC_GROUP']['TOBAGOAN'] = "TOB";
    $data['ETHNIC_GROUP']['TRINIDADIAN'] = "TRN";
    $data['ETHNIC_GROUP']['UKRANIAN'] = "UKR";
    $data['ETHNIC_GROUP']['URUGUAYAN'] = "URG";
    $data['ETHNIC_GROUP']['VENEZUELAN'] = "VEN";
    $data['ETHNIC_GROUP']['VIETNAMESE'] = "VTN";
    $data['ETHNIC_GROUP']['WEST INDIAN'] = "WST";
    $data['ETHNIC_GROUP']['AMERICAN INDIANS'] = "AMI";
    $data['ETHNIC_GROUP']['EUROPEAN AMERICANS'] = "EUA";
    $data['ETHNIC_GROUP']['JEWISH'] = "JEW";
    
	//ethnicity for valleyeye
	$data['ETHNIC_GROUP2']['AFRICAN AMERICANS']					= "27FDF9CD-26A5-4F13-824F-54DCD7BB8957";
	$data['ETHNIC_GROUP2']['AMERICAN']							= "27FDF9CD-26A5-4F13-824F-54DCD7BB8957";
	$data['ETHNIC_GROUP2']['AMERICAN INDIANS']					= "27FDF9CD-26A5-4F13-824F-54DCD7BB8957";
	$data['ETHNIC_GROUP2']['ASIAN']								= "27FDF9CD-26A5-4F13-824F-54DCD7BB8957";
	$data['ETHNIC_GROUP2']['HISPANIC OR LATINO']				= "9ED8153B-E8A8-40D0-BADA-A4D8F1E3B2CC";
	$data['ETHNIC_GROUP2']['NATIVE HAWAIIAN OR OTHER ISLANDER']	= "34617ADB-F810-4057-9973-1D6A1975E756";
	$data['ETHNIC_GROUP2']['DECLINED TO SPECIFY']				= "B2103576-255B-46C8-8338-4DD66E6300A6";
	
    /*Language*/
      /*Custom Values*/
    $data['LANGUAGE']['ALBANIAN'] = "ALB";
    $data['LANGUAGE']['AMHARIC'] = "AMH";
    $data['LANGUAGE']['ARABIC'] = "ARB";
    $data['LANGUAGE']['ARMENIAN'] = "ARM";
    $data['LANGUAGE']['ASL-SIGN LANGUAGE'] = "SIG";
    $data['LANGUAGE']['BENGALI'] = "BEN";
    $data['LANGUAGE']['BOSNIAN'] = "BOS";
    $data['LANGUAGE']['BULGARIAN'] = "BUL";
    $data['LANGUAGE']['BURMESE'] = "BUR";
    $data['LANGUAGE']['CAMBODIAN'] = "CAM";
    $data['LANGUAGE']['CANTONESE'] = "CHC";
    $data['LANGUAGE']['CAPE VERDEAN'] = "CVD";
    $data['LANGUAGE']['CZECH'] = "CZE";
    $data['LANGUAGE']['DUTCH'] = "DUT";
    $data['LANGUAGE']['ENGLISH'] = "ENG";
    $data['LANGUAGE']['ESTONIAN'] = "EST";
    $data['LANGUAGE']['FARSI'] = "FAR";
    $data['LANGUAGE']['FRENCH'] = "FRE";
    $data['LANGUAGE']['FUKANESE'] = "CHF";
    $data['LANGUAGE']['GAELIC'] = "GAE";
    $data['LANGUAGE']['GERMAN'] = "GER";
    $data['LANGUAGE']['GREEK'] = "GRK";
    $data['LANGUAGE']['GUJARATI'] = "GUJ";
    $data['LANGUAGE']['HAITIAN CREOLE'] = "CRE";
    $data['LANGUAGE']['HAKKA'] = "HAK";
    $data['LANGUAGE']['HEBREW'] = "HEB";
    $data['LANGUAGE']['HINDI'] = "IND";
    $data['LANGUAGE']['HUNGARIAN'] = "HUN";
    $data['LANGUAGE']['IBOL'] = "IBO";
    $data['LANGUAGE']['IRANIAN'] = "IRA";
    $data['LANGUAGE']['ITALIAN'] = "ITL";
    $data['LANGUAGE']['JAPANESE'] = "JAP";
    $data['LANGUAGE']['KHMER'] = "KHM";
    $data['LANGUAGE']['KOREAN'] = "KOR";
    $data['LANGUAGE']['LAOTIAN'] = "LAO";
    $data['LANGUAGE']['LATVIAN'] = "LAT";
    $data['LANGUAGE']['LEBANESE'] = "LEB";
    $data['LANGUAGE']['LITHUANIAN'] = "LIT";
    $data['LANGUAGE']['MALAY'] = "MAL";
    $data['LANGUAGE']['MANDARIN'] = "CHM";
    $data['LANGUAGE']['MARACI'] = "MAR";
    $data['LANGUAGE']['MARATHI'] = "MAT";
    $data['LANGUAGE']['OTHER'] = "ZZZ";
    $data['LANGUAGE']['POLISH'] = "POL";
    $data['LANGUAGE']['PORTUGUESE'] = "POR";
    $data['LANGUAGE']['RUMANIAN'] = "RUM";
    $data['LANGUAGE']['RUSSIAN'] = "RUS";
    $data['LANGUAGE']['SERBO-CROAT'] = "SEC";
    $data['LANGUAGE']['SLOVAK'] = "SLO";
    $data['LANGUAGE']['SOMALI'] = "SOM";
    $data['LANGUAGE']['SPANISH'] = "SPA";
    $data['LANGUAGE']['SWEDISH'] = "SWE";
    $data['LANGUAGE']['TAGALOG'] = "TAG";
    $data['LANGUAGE']['TAIWANESE'] = "TAI";
    $data['LANGUAGE']['THAI'] = "THA";
    $data['LANGUAGE']['TIGRINIAN'] = "TIG";
    $data['LANGUAGE']['TOISANESE'] = "CHT";
    $data['LANGUAGE']['TURKISH'] = "TUR";
    $data['LANGUAGE']['UKRAINIAN'] = "UKR";
    $data['LANGUAGE']['VIETNAMESE'] = "VIE";
    
    /*Relationship*/
      /*HL7 Standard Values*/
    $data['RELATIONSHIP']['SELF'] = "SEL";
    $data['RELATIONSHIP']['SPOUSE'] = "SPO";
    $data['RELATIONSHIP']['HUSBAND'] = "SPO";
    $data['RELATIONSHIP']['WIFE'] = "SPO";
    $data['RELATIONSHIP']['LIFE PARTNER'] = "DOM";
    $data['RELATIONSHIP']['CHILD'] = "CHD";
    $data['RELATIONSHIP']['GRAND CHILD'] = "GCH";
    $data['RELATIONSHIP']['GRANDCHILD'] = "GCH";
    $data['RELATIONSHIP']['NATURAL CHILD'] = "NCH";
    $data['RELATIONSHIP']['STEP CHILD'] = "SCH";
    $data['RELATIONSHIP']['STEPCHILD'] = "SCH";
    $data['RELATIONSHIP']['FOSTER CHILD'] = "FCH";
    $data['RELATIONSHIP']['HANDICAPPED DEPENDANT'] = "DEP";
    $data['RELATIONSHIP']['WARD OF THE COURT'] = "WRD";
    $data['RELATIONSHIP']['WARD OF COURT'] = "WRD";
    $data['RELATIONSHIP']['PARENT'] = "PAR";
    $data['RELATIONSHIP']['FATHER'] = "FTH";
    $data['RELATIONSHIP']['MOTHER'] = "MTH";
    $data['RELATIONSHIP']['CARE GIVER'] = "CGV";
    $data['RELATIONSHIP']['LEGAL GUARDIAN'] = "GRD";
    $data['RELATIONSHIP']['GUARDIAN'] = "GRD";
    $data['RELATIONSHIP']['GRANDPARENT'] = "GRP";
    $data['RELATIONSHIP']['GRANDFATHER'] = "GRP";
    $data['RELATIONSHIP']['GRANDMOTHER'] = "GRP";
    $data['RELATIONSHIP']['EXTENDED FAMILY'] = "EXF";
    $data['RELATIONSHIP']['SIBLING'] = "SIB";
    $data['RELATIONSHIP']['BROTHER'] = "BRO";
    $data['RELATIONSHIP']['SISTER'] = "SIS";
    $data['RELATIONSHIP']['BROTHER/SISTER'] = "BRO";
    $data['RELATIONSHIP']['BROTHER\SISTER'] = "BRO";
    $data['RELATIONSHIP']['FRIEND'] = "FND";
    $data['RELATIONSHIP']['OTHER'] = "OTH";
    $data['RELATIONSHIP']['OTHER ADULT'] = "OAD";
    $data['RELATIONSHIP']['EMPLOYEE'] = "EME";
    $data['RELATIONSHIP']['EMPLOYER'] = "EMR";
    $data['RELATIONSHIP']['ASSOCIATES'] = "ASC";
    $data['RELATIONSHIP']['EMERGENCY CONTACT'] = "EMC";
    $data['RELATIONSHIP']['OWNER'] = "OWN";
    $data['RELATIONSHIP']['TRAINER'] = "TRA";
    $data['RELATIONSHIP']['MANAGER'] = "MGR";
    $data['RELATIONSHIP']['NONE'] = "NON";
    $data['RELATIONSHIP']['UNKNOWN'] = "UNK";
      /*Custom Values*/
    $data['RELATIONSHIP']['STUDENT'] = "3";
    $data['RELATIONSHIP']['DEP CHILD:FIN RESPONSIBILITY'] = "4";
    $data['RELATIONSHIP']['CHILD:NO FIN RESPONSIBILITY'] = "6";
    $data['RELATIONSHIP']['DONOR LIVE'] = "13";
    $data['RELATIONSHIP']['DONOR-DCEASED'] = "14";
    $data['RELATIONSHIP']['NIECE\NEPHEW'] = "16";
    $data['RELATIONSHIP']['NIECE/NEPHEW'] = "16";
    $data['RELATIONSHIP']['INJURED PLANTIFF'] = "17";
    $data['RELATIONSHIP']['SPONSORED DEPENDENT'] = "18";
    $data['RELATIONSHIP']['MINOR DEPENDENT OF A DEPENDENT'] = "19";
    $data['RELATIONSHIP']['UNCLE'] = "22";
    $data['RELATIONSHIP']['AUNT'] = "22";
    $data['RELATIONSHIP']['AUNT/UNCLE'] = "22";
    $data['RELATIONSHIP']['AUNT\UNCLE'] = "22";
    $data['RELATIONSHIP']['INLAW'] = "25";
    $data['RELATIONSHIP']['RELATIVE'] = "26";
    
    /*Marital Status*/
    $data['MARITAL_STATUS']['SEPARATED'] = "A";
    $data['MARITAL_STATUS']['DIVORCED'] = "D";
    $data['MARITAL_STATUS']['MARRIED'] = "M";
    $data['MARITAL_STATUS']['SINGLE'] = "S";
    $data['MARITAL_STATUS']['WIDOWED'] = "W";
    $data['MARITAL_STATUS']['COMMON LAW'] = "C";
    $data['MARITAL_STATUS']['LIVING TOGETHER'] = "G";
    $data['MARITAL_STATUS']['DOMESTIC PARTNER'] = "P";
    $data['MARITAL_STATUS']['REGISTERED DOMESTIC PARTNER'] = "R";
    $data['MARITAL_STATUS']['LEFALLY SEPARATED'] = "E";
    $data['MARITAL_STATUS']['ANNULLED'] = "A";
    $data['MARITAL_STATUS']['INTERLOCUTORY'] = "I";
    $data['MARITAL_STATUS']['UNMARRIED'] = "B";
    $data['MARITAL_STATUS']['UNKNOWN'] = "U";
    $data['MARITAL_STATUS']['OTHER'] = "O";
    $data['MARITAL_STATUS']['UNREPORTED'] = "T";
	
	//MARITAL STATUS valleyeye Spectrum
	$data['MARITAL_STATUS2']['DIVORCED']				= "D";
	$data['MARITAL_STATUS2']['DOMESTIC PARTNER']		= "T";
	$data['MARITAL_STATUS2']['MARRIED']					= "M";
	$data['MARITAL_STATUS2']['SINGLE']					= "S";
	$data['MARITAL_STATUS2']['SEPARATED']				= "X";
	$data['MARITAL_STATUS2']['WIDOWED']					= "W";
	$data['MARITAL_STATUS2']['UNKNOWN']					= "U";
	$data['MARITAL_STATUS2']['LIFE PARTNER']			= "L";
	$data['MARITAL_STATUS2']['POLYGAMOUS']				= "P";
	$data['MARITAL_STATUS2']['INTERLOCUTORY']			= "I";
	$data['MARITAL_STATUS2']['ANNULLED']				= "A";
    
    $data['CONTACT_ROLE']['EMPLOYER'] = "E";
    $data['CONTACT_ROLE']['EMERGENCY CONTACT'] = "C";
    $data['CONTACT_ROLE']['FEDERAL AGENCY'] = "F";
    $data['CONTACT_ROLE']['INSURANCE COMPANY'] = "I";
    $data['CONTACT_ROLE']['NEXT-OF-KIN'] = "N";
    $data['CONTACT_ROLE']['STATE AGENCY'] = "S";
    $data['CONTACT_ROLE']['OTHER'] = "O";
    $data['CONTACT_ROLE']['UNKNOWN'] = "U";
    
    $data['SEX']['FEMALE'] = "F";
    $data['SEX']['MALE'] = "M";
    $data['SEX']['OTHER'] = "O";
    $data['SEX']['UNKNOWN'] = "U";
    $data['SEX']['AMBIGUOUS'] = "A";
    $data['SEX']['NOT APPLICABLE'] = "N";
    
	if($flag==0){
		if( isset($data[$key][$val]) )
		{
			return($data[$key][$val]);
		}
		else
		{
			return $val;
		}
	}
	else{
		return(ucwords(strtolower(array_search($val, $data[$key]))));
	}
  }
}