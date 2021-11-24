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
require_once("../../../config/globals.php");
require_once("../../../library/patient_must_loaded.php");
include_once($GLOBALS['srcdir']."/classes/eligibility.class.php");
ini_set("memory_limit","1024M");
$library_path = $GLOBALS['webroot'].'/library';

$rqId = (int)$_REQUEST["id"];
$typeKeyArr = array('aco' => 'Active Coverage','coi'=> 'Co-Insurance', 'ded' => 'Deductible', 'lmt' => 'Limitations', 'cop' => 'Co-Payment' );
$rqType = (isset($_REQUEST['t'])) ? trim($_REQUEST['t']) : 'false';

if( !array_key_exists($rqType,$typeKeyArr) || $rqId <= 0 ) exit('Invalid Request');


$q = "select rtme.request_270_file_path as requTXTPath,rtme.response_271_file_path as respTXTPath, rtme.eligibility_ask_from  as elAsk, 
						rtme.hipaa_5010 as elHIPAA5010, 
						rtme.response_deductible, rtme.response_copay,rtme.response_co_insurance, 
						insData.scan_card as insCard1, insData.scan_card2 as insCard2,
						CONCAT(pd.lname,', ',pd.fname,' ',SUBSTR(pd.mname,1,1),' - ',pd.id) AS patient_name_id 
						from real_time_medicare_eligibility rtme
						left join patient_data pd on pd.id = rtme.patient_id
						left join insurance_data insData on insData.id = rtme.ins_data_id
						where rtme.id = '".$rqId."' 
						LIMIT 1";
$res = imw_query($q);		
if($res){	
		$rs = imw_fetch_assoc($res);	
		$dbInsCard1 	= $rs["insCard1"];
		$dbInsCard2 	= $rs["insCard2"];
		$db_deductible 	= $rs['response_deductible'];
		$db_copay		= $rs['response_copay'];
		$db_coins		= $rs['response_co_insurance'];
		
		$img_location = $GLOBALS['webroot'].'/data/'.constant('PRACTICE_PATH');
		if(empty($dbInsCard1) == false){
			$strCard1SRCMain = check_pt_file_exists($dbInsCard1,'web');
		}
		if(empty($dbInsCard2) == false){
			$strCard2SRCMain = check_pt_file_exists($dbInsCard2,'web');
		}
		imw_free_result($res);
		
		/**********IMPLEMENTAITON OF NEW PARSER******/
		require_once(dirname(__FILE__)."/../../../library/classes/RTEparser.php");
		$RTEparser = new RTEParser();
		
		if($rs["elHIPAA5010"] == 0){
			$dbRespTXTPath = $rs["respTXTPath"];
		}else{
			$dbRespTXTPath = data_path().$rs["respTXTPath"];
			$dbRequTXTPath = data_path().$rs["requTXTPath"];
		};
		if(file_exists($dbRequTXTPath) && is_file($dbRequTXTPath)){
			$db270RequestText = file_get_contents($dbRequTXTPath);
			$RTEresponse = $RTEparser->readRTEresponse($db271ResponseText);
		}
		if(file_exists($dbRespTXTPath) && is_file($dbRespTXTPath)){
			$db271ResponseText = file_get_contents($dbRespTXTPath);
			$RTEresponse = $RTEparser->readRTEresponse($db271ResponseText);
		}else{
			exit('Eligbility response data file not found.');
		}
		
}
else{
		exit('Invalid report ID provided. NO RTE data found.');
}

if($RTEresponse['error']!=''){ 
	exit($RTEresponse['error']); 
}
else {  
	$EB_response	= $RTEresponse['result']['2110C'];
}

?>
<table class="table table-bordered table-striped">
	<thead>
		<tr class="grythead">
				<th class="col-sm-2">Insurance Type</th>
				<th class="col-sm-2">Service Type</th>
				<th class="col-sm-2">Coverage Level /<bR>Description</th>
				<th class="col-sm-1">Time Period</th>
				<th class="col-sm-1">Date Details</th>
				<th class="col-sm-1">Benefit<br>(Amount / %)</th>
				<th class="col-sm-1">Quantity /<br>Qualifier</th>
				<th class="col-sm-2">Comments /<br>Message</th>
		</tr>
	</thead>
	<tbody class="rte_resultset">
	<?php
		if( count($EB_response[$typeKeyArr[$rqType]]) > 0  && is_array($EB_response[$typeKeyArr[$rqType]]) )
		{
			$DTP_key = $DTP_val = '';																						 
			foreach($EB_response[$typeKeyArr[$rqType]] as $k=>$v){
				$eb_data_Arr = $v[0]; 
				foreach($eb_data_Arr['DTP'] as $dtp_type=>$dtp_val){$DTP_key = $dtp_type; $DTP_val = $dtp_val;};
	?>
				<tr>
					<td><?php echo '<b>'.$eb_data_Arr['Insurance_Type'].'</b>';
							if(isset($eb_data_Arr['Additional_Payer_Info']) && is_array($eb_data_Arr['Additional_Payer_Info'])){
									echo '<div class="margin-2 padding-2 border bg-info"><div class="bg-success"><b>Additional Payer Info</b></div>';
									$comm_qualifier_arr = array('ED'=>'EDI Access Number',
																					'EM'=>'Email',
																					'FX'=>'FAX',
																					'TE'=>'Telephone',
																					'WP'=>'Work Phone',
																					'EX'=>'Extension',
																					'UR'=>'Website',
																					'IC'=>'Information Contact');
									foreach($eb_data_Arr['Additional_Payer_Info'] as $k=>$v){
										foreach($v as $k1=>$v1){
											if($k1=='Payer_Name' && $v1!='') echo $v1.'<br>';
											if(($k1=='Street1' || $k1=='Street2') && $v1!='') echo trim($v1).'<br>';
											if($k1 == 'State/Province' && $v1!='') echo trim($v1).' ';
											if($k1 == 'Zip_Code' && $v1!='') echo ', '.$v1;
											if(in_array($k1,$comm_qualifier_arr) && $v1!='') echo '<div>'.$k1.': '.$v1.'</div>';
										}
									}
									echo '</div>';
							}
							if($eb_data_Arr['Auth_Cert_Indicator']!= '') echo '<div class="m5 bg6"><i>Authorization or Cert. Required:</i> '.$eb_data_Arr['Auth_Cert_Indicator'].'</div>';
							if($eb_data_Arr['Benefits_In_Plan_Network']!= '') echo '<div class="m5 bg6"><i>Benefits in Plan Network:</i> '.$eb_data_Arr['Benefits_In_Plan_Network'].'</div>';
							 ?>
					</td>
					<td><?php echo $eb_data_Arr['Service_Type']; ?></td>
					<td><?php echo $eb_data_Arr['Coverage_Level']; if($eb_data_Arr['Coverage_Level']!='' && $eb_data_Arr['Plan_Coverage_Description']!=''){echo '<br>';} echo $eb_data_Arr['Plan_Coverage_Description'];
							if(isset($eb_data_Arr['Additional_Information']) && is_array($eb_data_Arr['Additional_Information'])){
									echo '<div class="margin-2 padding-2 border bg-info"><div class="bg-success"><b>LIMITATIONS</b></div>';
									foreach($eb_data_Arr['Additional_Information'] as $k=>$v){
											foreach($v as $k1=>$v1){
													if($k1=='Facility_Type') $v1 = $RTEparser->pos_facility_codes($v1);
													//echo '<b>'.str_replace('_',' ',$k1).'</b>: '.$v1.'<br>';
													echo $v1.'<br>';
											}
									}
									echo '</div>';
							}

							if(isset($eb_data_Arr['Medical_Procedure_Qualifier']) && $eb_data_Arr['Medical_Procedure_Qualifier']!=''){
									echo '<div class="m5 bg6"><i>'.$eb_data_Arr['Medical_Procedure_Qualifier'].'</i>: '.$eb_data_Arr['Medical_Procedure_Value'].'</div>';
							}						

							?>
					</td>
					<td class="text-center"><?php echo $eb_data_Arr['Time_Period_Qualifier']; ?></td>
					<td class="text-center"><?php echo str_replace('_',' ',$DTP_key).'<br>'.$DTP_val; if($eb_data_Arr['Health_Care_Service_Delivery']!='') echo '<br>'.$eb_data_Arr['Health_Care_Service_Delivery'];?></td>
					<td class="text-right"><?php echo $eb_data_Arr['Benefit_Amount']; if($eb_data_Arr['Benefit_Amount']!='' && $eb_data_Arr['Benefit_Percentage']!=''){echo '<br>';} echo $eb_data_Arr['Benefit_Percentage'];?>&nbsp;&nbsp;&nbsp;</td>
					<td class="text-center"><?php echo $eb_data_Arr['Quantity']; ?><br><?php echo $eb_data_Arr['Quantity_Qualifier']; ?></td>
					<td><?php echo $eb_data_Arr['Comments'];?></td>
				</tr>
	<?php
			}
		}
		else{
			echo '<tr><td colspan="8" class="bg bg-info">No '.$typeKeyArr[$rqType].'  Information Found</td></tr>';
		}
		
	?>
	</tbody>
</table>