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

$intTotVSCertInsComp = 0;$vsStatus = '';$rowGetRealTimeData=false;
if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES")
{
	$qryGetCertInfo = "SELECT ins_comp_id FROM vision_share_cert_config	WHERE ins_comp_id = '".(int)$comDetail->provider."' LIMIT 1 ";
	$rsGetCertInfo = imw_query($qryGetCertInfo);
	$intTotVSCertInsComp = imw_num_rows($rsGetCertInfo);
	if($intTotVSCertInsComp > 0)
	{ //For Medicare Eligibility
		$qryGetRealTimeData = "select DATE_FORMAT(responce_date_time, '".get_sql_date_format()." %I:%i %p') as vs270RespDate, transection_error as vsTransectionError, EB_responce as vsEBLoopResp from real_time_medicare_eligibility where patient_id = '".$patient_id."' and eligibility_ask_from = '0' and del_status = '0' and ins_data_id = '".$comDetail->id."' AND response_271_file_path != '' order by responce_date_time desc limit 1 ";
		$rsGetRealTimeData = imw_query($qryGetRealTimeData);
		if($rsGetRealTimeData){
			if(imw_num_rows($rsGetRealTimeData)>0){
				$rowGetRealTimeData = imw_fetch_object($rsGetRealTimeData);
			}
		}
	}
	elseif($intTotVSCertInsComp == 0)
	{ //For Comercial Eligibility
		$qryGetRealTimeData = "select DATE_FORMAT(responce_date_time, '".get_sql_date_format()." %I:%i %p') as vs270RespDate, transection_error as vsTransectionError, EB_responce as vsEBLoopResp 
							from real_time_medicare_eligibility
							where patient_id = '".$patient_id."' and eligibility_ask_from = '1' and del_status = '0'
							and ins_data_id = '".$comDetail->id."' AND response_271_file_path != '' order by responce_date_time desc limit 1
							";
		$rsGetRealTimeData = imw_query($qryGetRealTimeData);
		if($rsGetRealTimeData){
			if(imw_num_rows($rsGetRealTimeData)>0){
				$rowGetRealTimeData = imw_fetch_object($rsGetRealTimeData);
			}
		}
	}
}



if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES")
{
	$vsStatus = "";
	if($intTotVSCertInsComp > 0){
		$vsToolTip = $strEBResponce = $imgRealTimeEli = "";																
		if(($rowGetRealTimeData->vs270RespDate != "00-00-0000") && ($rowGetRealTimeData->vs270RespDate != "")){
			$vsToolTip = "Last Transaction on: ".$rowGetRealTimeData->vs270RespDate."\n";
			$vsStatus = $rowGetRealTimeData->vs270RespDate."<br>";
		}
		else{
			$imgRealTimeEli = " active";
		}								
		if($rowGetRealTimeData->vsTransectionError != ""){
			$vsToolTip .= $rowGetRealTimeData->vsTransectionError;
			$vsStatus .= "Status: Error";
			$imgRealTimeEli = "er_icon active";	
		}
		elseif($rowGetRealTimeData->vsEBLoopResp != ""){		
			$strEBResponce = $data_obj->get_vocabulary("vision_share_271", "EB", (string)trim($rowGetRealTimeData->vsEBLoopResp));
			$vsToolTip .= "Status: ".$strEBResponce;
			//$vsStatus .= "Status: ".$strEBResponce;
			if(strlen($strEBResponce) > 15){
				$vsStatus .= "Status: ".substr($strEBResponce,0, 15)."...";
			}
			else{
				$vsStatus .= "Status: ".$strEBResponce;
			}
			if(($rowGetRealTimeData->vsEBLoopResp == "6") || ($rowGetRealTimeData->vsEBLoopResp == "7") || ($rowGetRealTimeData->vsEBLoopResp == "8") || ($rowGetRealTimeData->vsEBLoopResp == "V")){
				$imgRealTimeEli = "er_icon active";					
			}
			else{
				$imgRealTimeEli = "er_icon active";	
			}
		}
	}
	elseif($intTotVSCertInsComp == 0){
		$vsToolTip = $strEBResponce = $imgRealTimeEli = "";																
		if(($rowGetRealTimeData->vs270RespDate != "00-00-0000") && ($rowGetRealTimeData->vs270RespDate != "")){
			$vsToolTip = "Last Transaction on: ".$rowGetRealTimeData->vs270RespDate."\n";
			$vsStatus = $rowGetRealTimeData->vs270RespDate."<br>";
		}
		else{
			$imgRealTimeEli = "";	
		}								
		if($rowGetRealTimeData->vsTransectionError != ""){
			$vsToolTip .= $rowGetRealTimeData->vsTransectionError;
			$vsStatus .= "Status: Error";
			$imgRealTimeEli = "er_icon active";
		}
		elseif($rowGetRealTimeData->vsEBLoopResp != ""){									
			$strEBResponce = $data_obj->get_vocabulary("vision_share_271", "EB", (string)trim($rowGetRealTimeData->vsEBLoopResp));
			$vsToolTip .= "Status: ".$strEBResponce;
			if(strlen($strEBResponce) > 15){
				$vsStatus .= "Status: ".substr($strEBResponce,0, 15)."...";
			}
			else{
				$vsStatus .= "Status: ".$strEBResponce;
			}
			if(($rowGetRealTimeData->vsEBLoopResp == "6") || ($rowGetRealTimeData->vsEBLoopResp == "7") || ($rowGetRealTimeData->vsEBLoopResp == "8") || ($rowGetRealTimeData->vsEBLoopResp == "V")){
				$imgRealTimeEli = "er_icon active";			
			}
			else{
				$imgRealTimeEli = "er_icon";	
			}
		}
	}
}

?>
