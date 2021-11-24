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
FILE : new_account_report_result_refphysician.php
PURPOSE : NEW ACCOUNT REPORT FOR REF PHYSICIAN
ACCESS TYPE : INCLUDED
*/
$hrd_abt_title .='Heard about us';
$page_data = $page_data2 = '';
$top_pad="15mm";
//----MAKE ARRAY FOR HEARD ABOUT US AND PROCEDURE TYPES	
					
		$arrHrdAbtData = array();
		$arrHrdAbtDataVal = array();
		$arrProTypeData = array();
		$arrProTypeDataVal = array();
		$refPhyIDs = array_keys($arrDataRefPhy);
		$arrRefPhyDetail = array();
		if(!isset($GLOBALS['REF_PHY_FORMAT']) || strtolower($GLOBALS['REF_PHY_FORMAT']) != 'boston'){
			$nameFormat = "CONCAT(TRIM(TITLE), ' ', TRIM(LastName), ', ', TRIM(FirstName), ' ', TRIM(MiddleName))";
		}
		else if(isset($GLOBALS['REF_PHY_FORMAT']) && strtolower($GLOBALS['REF_PHY_FORMAT']) == 'boston'){
			$nameFormat = "CONCAT(TRIM(LastName), ', ', TRIM(FirstName), ' ', TRIM(MiddleName), ' ', TRIM(TITLE))";
		}
		$id_reff_physician = implode(',',$refPhyIDs);
		$qry = "SELECT $nameFormat AS refName, physician_Reffer_id
					 FROM refferphysician 
					 WHERE physician_Reffer_id IN($id_reff_physician)
					 ORDER BY LastName,FirstName
					";
		$res = imw_query($qry);

		while($row = imw_fetch_array($res)){
			if(!empty($row['refName']))
			$arrRefPhyDetail[$row['physician_Reffer_id']] = $row['refName'];
		}
		$arr_all_id_reff_physician = explode(",",$id_reff_physician);
		$arr_id_reff_physician = array_keys($arrRefPhyDetail);
		$arrTmp = array_diff($arr_all_id_reff_physician, $arr_id_reff_physician);
		
		foreach($arrTmp as $ref_phy_id){
			$arrRefPhyDetail[$ref_phy_id] = '';
		}
		foreach($arrDataRefPhy as $ref_phy_id=>$arrRefPhyPatients){
			for($i = 0; $i<count($arrRefPhyPatients); $i++){
				$patID = $arrRefPhyPatients[$i]['id'];
				$heardAboutID = $arrRefPhyPatients[$i]['heard_abt_us'];
				$arrHrdAbtData[$ref_phy_id][$heardAboutID][] = $heard_arr[$heardAboutID];
				$arrProTypeData[$ref_phy_id][$sch_data_arr[$patID]['appt_proc']][]=$sch_data_arr[$patID]['appt_proc'];
				
				$hrdAbtVal =  $heard_arr[$heardAboutID] == '' ?'None' : $heard_arr[$heardAboutID];
				$arrHrdAbtDataVal[$hrdAbtVal][] = $patID;
				
				$proTypeVal =  $sch_data_arr[$patID]['appt_proc'] == '' ?'None' : $sch_data_arr[$patID]['appt_proc'];
				$arrProTypeDataVal[$proTypeVal][] = $patID;
			}
		}

		//------- IF ONLY NONE IS SELECTED IN HEARD ABOUT US
		//$arrTempHrd = (array_flatten($arrHrdAbtData,array()));
		if($heard_str == "0" && $processReport!="Detail")
		$arrTempHrd  = array();
		else 
		$arrTempHrd = $arrHrdAbtData;
		if($heard_str == 0 && $processReport!="Detail"){
		$page_data .='<tr style="background-color:#ffffff;"><td style="text-align:left;" class="text_10b" colspan="9">
							<table width="50%">';
		$page_data .='<tr><td width="50%" class="text_10b">Referring Physician</td>
							  <td width="50%" class="text_10b">Total New Account</td>
						</tr>';							
		$page_data .='	</table>
							</td></tr>';
		$page_data2 = '<tr style="background-color:#ffffff;"><td style="text-align:left;" class="text_b_w" colspan="9">
							<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%"><tr><td width="520" class="text_b_w">Referring Physician</td>
							  <td width="520" class="text_b_w">Total New Account</td>
						</tr></table>
							</td></tr>';					
		}
		//---------------------------------------------------
if($processReport=="Detail"){

			
			$page_data .='<tr>
							<td style="width:20px; text-align:center;" class="text_b_w">#</td>
							<td style="width:50px; text-align:center;" class="text_b_w">Ref. Physician</td>
							<td style="width:250px; text-align:center;" class="text_b_w">Patient Name - ID</td>
							<td style="width:80px; text-align:center;" class="text_b_w">Appt Created Date</td>
							<td style="width:80px; text-align:center;" class="text_b_w">Appt Date</td>
							<td style="width:100px; text-align:center;" class="text_b_w">Appointment Type</td>
							<td style="width:180px; text-align:center;" class="text_b_w">Physician Name</td>
							<td style="width:120px; text-align:center;" class="text_b_w">Heard About us</td>
							<td style="width:130px; text-align:center;" class="text_b_w">Pat Created Date</td>
							<td style="width:60px; text-align:center;" class="text_b_w">Operator</td>
						</tr>';
			$page_data2 .='<tr>
							<td style="width:20px; text-align:center;" class="text_b_w">#</td>
							<td style="width:80px; text-align:center;" class="text_b_w">Ref. Physician</td>
							<td style="width:200px; text-align:center;" class="text_b_w">Patient Name - ID</td>
							<td style="width:80px; text-align:center;" class="text_b_w">Appt Created Date</td>
							<td style="width:80px; text-align:center;" class="text_b_w">Appt Date</td>
							<td style="width:100px; text-align:center;" class="text_b_w">Appointment Type</td>
							<td style="width:180px; text-align:center;" class="text_b_w">Physician Name</td>
							<td style="width:100px; text-align:center;" class="text_b_w">Heard About us</td>
							<td style="width:130px; text-align:center;" class="text_b_w">Pat Created Date</td>
							<td style="width:60px; text-align:center;" class="text_b_w">Operator</td>
					</tr>';
}else{
			$page_data .='<tr style="background-color:#ffffff;">
							<td style="text-align:left;width:30px;" class="text_10"></td>
							<td style="text-align:left;width:220px" class="text_10"></td>
							<td style="text-align:left;width:150px;" class="text_10"></td>
							<td style="text-align:left;width:100px;" class="text_10"></td>
							<td style="text-align:left;width:180px;" class="text_10"></td>
							<td style="text-align:left;width:80px;" class="text_10"></td>
							<td style="text-align:left;width:100px;" class="text_10"></td>
							<td style="text-align:left;width:130px;" class="text_10"></td>
							<td style="text-align:left;width:40px;" class="text_10"></td>
					</tr>';
			$page_data2 .='<tr style="background-color:#ffffff;">
							<td style="text-align:left;width:30px;" class="text_10"></td>
							<td style="text-align:left;width:220px" class="text_10"></td>
							<td style="text-align:left;width:150px;" class="text_10"></td>
							<td style="text-align:left;width:100px;" class="text_10"></td>
							<td style="text-align:left;width:180px;" class="text_10"></td>
							<td style="text-align:left;width:80px;" class="text_10"></td>
							<td style="text-align:left;width:100px;" class="text_10"></td>
							<td style="text-align:left;width:130px;" class="text_10"></td>
							<td style="text-align:left;width:40px;" class="text_10"></td>
					</tr>';

}
	//$arrRefPhyIDs = array_keys($arrDataRefPhy);//pre($arrRefPhyIDs);
	//----FOR NONE REFERRING PHYSICIAN-------
	//$arrRefPhyDetail[0]='';
	$arrRefPhyIDs = array_keys($arrRefPhyDetail);
	$st_no = 1;
	foreach($arrRefPhyIDs as $refPhyID){
		$phyName = $arrRefPhyDetail[$refPhyID] == '' ? 'None':$arrRefPhyDetail[$refPhyID];
		if($heard_str != 0 || $processReport =="Detail"){
			//$page_data.='<tr><td style="text-align:left; height:20px;" class="text_b_w" colspan="9">Ref. Physician : '.$phyName.'</td></tr>';
			//$page_data2.='<tr><td style="text-align:left; height:20px;" class="text_b_w" colspan="9">Ref. Physician : '.$phyName.'</td></tr>';
		}
		$arrRefPhyPatients = array();
		
		if($processReport=="Detail"){
				$arrRefPhyPatients = $arrDataRefPhy[$refPhyID];
				//-----BEGIN PATIENT DETAIL DISPLAY
				
				foreach($arrRefPhyPatients as $key=>$arrPatientData){
					$pat_name = "";
					$pat_name_arr = array();
					$pat_name_arr['LAST_NAME'] = $arrPatientData['lname'];
					$pat_name_arr['FIRST_NAME'] = $arrPatientData['fname'];
					$pat_name_arr['MIDDLE_NAME'] = $arrPatientData['mname'];		
					$pat_name= changeNameFormat($pat_name_arr);	
					$pt_id = $arrPatientData['id'];
					$pat_name = $pat_name.' - '.$pt_id;	
					$heard_abt_us_name = $heard_arr[$arrPatientData['heard_abt_us']];
					$pat_date = $arrPatientData['pat_date'];
					$created_by = $arrPatientData['created_by'];
					$page_data .='<tr style="background-color:#ffffff;">
										<td style="text-align:left;width:20px;" class="text_10">'.$st_no.'</td>
										<td style="text-align:left;width:100px;" class="text_10">'.$phyName.'</td>								
										<td style="text-align:left;width:250px" class="text_10">'.$pat_name.'</td>
										<td style="text-align:left;width:80px;" class="text_10">'.$arr_appt_created_date[$pt_id].'</td>
										<td style="text-align:left;width:150px;" class="text_10">'.$sch_data_arr[$pt_id]['appt_date'].'</td>
										<td style="text-align:left;width:100px;" class="text_10">'.$sch_data_arr[$pt_id]['appt_proc'].'</td>
										<td style="text-align:left;width:250px;" class="text_10">'.$sch_data_arr[$pt_id]['physician'].'</td>
										<td style="text-align:left;width:120px;" class="text_10">'.$heard_abt_us_name.'</td>
										<td style="text-align:left;width:150px;" class="text_10">'.$pat_date.'</td>
										<td style="text-align:left;width:40px;" class="text_10">'.$opr_ins[$created_by].'</td>
									</tr>';	
									
					$page_data2 .='<tr style="background-color:#ffffff;">
										<td style="text-align:left;width:20px;" class="text_10">'.$st_no.'</td>
										<td style="text-align:left;width:80px;" class="text_10">'.$phyName.'</td>
										<td style="text-align:left;width:200px" class="text_10">'.$pat_name.'</td>
										<td style="text-align:left;width:80px;" class="text_10">'.$arr_appt_created_date[$pt_id].'</td>
										<td style="text-align:left;width:80px;" class="text_10">'.$sch_data_arr[$pt_id]['appt_date'].'</td>
										<td style="text-align:left;width:100px;" class="text_10">'.$sch_data_arr[$pt_id]['appt_proc'].'</td>
										<td style="text-align:left;width:180px;" class="text_10">'.$sch_data_arr[$pt_id]['physician'].'</td>
										<td style="text-align:left;width:100px;" class="text_10">'.$heard_abt_us_name.'</td>
										<td style="text-align:left;width:130px;" class="text_10">'.$pat_date.'</td>
										<td style="text-align:left;width:60px;" class="text_10">'.$opr_ins[$created_by].'</td>
									</tr>';				
					$st_no++;
				}	//-----END  PATIENT DETAIL DISPLAY		
		}
		
/*		if($heard_str != 0 || $processReport =="Detail"){
		$page_data .='<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="9">Total Number of New Account(s) : '.count($arrDataRefPhy[$refPhyID]).'</td>
					</tr>
					<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td>
					</tr>';	
		$page_data2 .='<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="9">Total Number of New Account(s) : '.count($arrDataRefPhy[$refPhyID]).'</td>
					</tr>
					<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td>
					</tr>';
		}
		else{ //------- IF ONLY NONE IS SELECTED IN HEARD ABOUT US
			$page_data .='<tr style="background-color:#ffffff;"><td style="text-align:left;" class="text_10" colspan="9">
							<table width="50%">';
			$page_data .='<tr><td width="50%">'.$phyName.'</td>
							  <td width="50%">'.count($arrDataRefPhy[$refPhyID]).'</td>
						</tr>';							
			$page_data .='	</table>
							</td></tr>
							';	
			$page_data2 .= 	'<tr style="background-color:#ffffff;"><td style="text-align:left;" class="text_10" colspan="9">
							<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%"><tr><td width="520">'.$phyName.'</td>
							  <td width="520">'.count($arrDataRefPhy[$refPhyID]).'</td>
						</tr></table>
							</td></tr>';			
		}
		//-----------BEGIN HEARD ABOUT US -----------------
		if($heard_str != 0 || $processReport =="Detail"){
			$colCount = 0;
			$arrHrdAbtDataPhy = $arrHrdAbtData[$refPhyID];
			$hrdAbtHTML = '';
			$hrdAbtHTML = hrd_abt_data($arrHrdAbtDataPhy);
		//-----------END HEARD ABOUT US -----------------

		//-----------BEGIN PROCEDURE TYPE -----------------	
			$colCount = 0;
			$arrProTypeDataPhy = $arrProTypeData[$refPhyID];
			$proTypeHTML = '';
			$proTypeHTML = proc_type_data($arrProTypeDataPhy);
		
		//-----------END PROCEDURE TYPE -----------------
			$page_data .= $hrdAbtHTML . '<tr style="background-color:#ffffff;">
										<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td>
										</tr>' .
						  $proTypeHTML. '<tr style="background-color:#ffffff;">
										<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td>
										</tr>';	
		
			$page_data2 .= $hrdAbtHTML . '<tr style="background-color:#ffffff;">
										<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td>
										</tr>' .
						  $proTypeHTML. '<tr style="background-color:#ffffff;">
										<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td>
										</tr>';
		}*/
	}

	//-----------BEGIN GRAND TOTAL DATA DISPLAY -----------------		
	$totalPat = 0;
	$grdTotalHTML = '';
	foreach ($arrDataRefPhy as $arrRefPhy) 
	$totalPat += sizeof($arrRefPhy);
	$page_data .='<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td></tr><tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_b_w" colspan="10">Grand Total Number of New Account(s) : '.$totalPat.'</td></tr>
						<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td></tr>';
						
	$page_data2 .='<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td></tr><tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_b_w" colspan="10">Grand Total Number of New Account(s) : '.$totalPat.'</td></tr>
						<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td></tr>';
	//-----------BEGIN TOTAL HEARD ABOUT US -----------------					
	$colCount = 0;$hrdAbtHTML = '';
	$hrdAbtHTML .= hrd_abt_grand_data($arrHrdAbtDataVal);
	//---------END TOTAL HEARD ABOUT US -----------------	
	$page_data .= '<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
					</tr>';	
	$page_data2 .= '<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
					</tr>';	
	//-----------BEGIN TOTAL PROCEDURE TYPE -----------------			
	$colCount = 0; $proTypeHTML = '';
	$proTypeHTML .= proc_type_grand_data($arrProTypeDataVal);
	//---------END TOTAL PROCEDURE TYPE -----------------	
	$page_data .= $hrdAbtHTML . '<tr style="background-color:#ffffff;">
									<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
									</tr>' .
			      $proTypeHTML. '<tr style="background-color:#ffffff;">
									<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
									</tr>';	
	$page_data2 .= $hrdAbtHTML . '<tr style="background-color:#ffffff;">
									<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
									</tr>' .
			      $proTypeHTML. '<tr style="background-color:#ffffff;">
									<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
									</tr>';	


	$page_data .= '<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
					</tr>';	
	$page_data .= '<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
					</tr>';
	$page_data2 .= '<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
					</tr><tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td>
					</tr>';
					
function array_flatten($array,$return)
{
  foreach($array as $key=>$val)
  {
    if(is_array($val))
    {
        foreach($array as $key=>$val)
  		$return[] = $val;
    }
    else
    {
        $return[] = $val;
    }
  }
  return $return;
}
function hrd_abt_data($arrHrdAbtDataPhy){
	global $hrd_abt_title;
	foreach($arrHrdAbtDataPhy as $hrdAbtID=>$arrSubHrdAbtData){
				$colCount = $colCount == 3 ? 0:$colCount;
				$hrdAbtVal = $arrSubHrdAbtData[0] == ''? 'None': $arrSubHrdAbtData[0];
				//------CALCULATE COL SPAN---------------------------------------------
				$index = array_search($hrdAbtID,array_keys($arrHrdAbtDataPhy));
				$leftArrCount = count($arrHrdAbtDataPhy) - ($index+1) ;
				$colSpan = ($leftArrCount == 0 && $colCount == 0)? '9' : (($leftArrCount == 0 && $colCount == 1)? '6' :  '3');
				//---------------------------------------------------------------------
				if($colCount == 0)
				$hrdAbtHTML .='<tr style="background-color:#ffffff;">';
				$hrdAbtHTML .='<td style="text-align:left;" class="text_10b" colspan="'.$colSpan.'">'.$hrd_abt_title.': ';
				$hrdAbtHTML .= '<span style="font-weight:normal">'.$hrdAbtVal." : ".count($arrSubHrdAbtData).'</span>';
				$hrdAbtHTML .='</td>';	
				if($colCount == 2 || $leftArrCount == 0)
				$hrdAbtHTML .='</tr>	';
				$colCount++;

		} //-----END FOREACH LOOP	
		return $hrdAbtHTML;
}
function hrd_abt_grand_data($arrHrdAbtDataVal){
	global $hrd_abt_title;
	foreach($arrHrdAbtDataVal as $hrdAbtVal=>$arrHrdAbtPat){
				$colCount = $colCount == 3 ? 0:$colCount;
				$hrdAbtVal = $hrdAbtVal == ''? 'None': $hrdAbtVal;
				//------CALCULATE COL SPAN---------------------------------------------
				$index = array_search($hrdAbtVal,array_keys($arrHrdAbtDataVal));
				$leftArrCount = count($arrHrdAbtDataVal) - ($index+1) ;
				$colSpan = ($leftArrCount == 0 && $colCount == 0)? '9' : (($leftArrCount == 0 && $colCount == 1)? '6' :  '3');
				//---------------------------------------------------------------------
				if($colCount == 0)
				$hrdAbtHTML .='<tr style="background-color:#ffffff;">';
				$hrdAbtHTML .='<td style="text-align:left;" class="text_10b" colspan="'.$colSpan.'">'.$hrd_abt_title.': ';
				$hrdAbtHTML .= '<span style="font-weight:normal">'.$hrdAbtVal." : ".count($arrHrdAbtPat).'</span>';
				$hrdAbtHTML .='</td>';	
				if($colCount == 2 || $leftArrCount == 0)
				$hrdAbtHTML .='</tr>	';
				$colCount++;

	} //-----END FOREACH LOOP	
	return $hrdAbtHTML;
}
function proc_type_data($arrProTypeDataPhy){
	foreach($arrProTypeDataPhy as $proTypeId=>$arrSubProType){
				$colCount = $colCount == 3 ? 0:$colCount;
				$proTypeVal = $arrSubProType[0] == ''? 'None': $arrSubProType[0];
				//------CALCULATE COL SPAN---------------------------------------------
				$index = array_search($proTypeId,array_keys($arrProTypeDataPhy));
				$leftArrCount = count($arrProTypeDataPhy) - ($index+1) ;
				$colSpan = ($leftArrCount == 0 && $colCount == 0)? '9' : (($leftArrCount == 0 && $colCount == 1)? '6' :  '3');
				//---------------------------------------------------------------------
				if($colCount == 0)
				$proTypeHTML .='<tr style="background-color:#ffffff;">';
				$proTypeHTML .='<td style="text-align:left;" class="text_10b" colspan="'.$colSpan.'">Procedure Type: ';
					$proTypeHTML .= '<span style="font-weight:normal">'.$proTypeVal." : ".count($arrSubProType).'</span>';
				$proTypeHTML .='</td>';	
				if($colCount == 2 || $leftArrCount == 0)
				$proTypeHTML .='</tr>';
				$colCount++;

		} //-----END FOREACH LOOP	*/
		return $proTypeHTML;
}
function proc_type_grand_data($arrProTypeDataVal){
	foreach($arrProTypeDataVal as $proTypeVal=>$arrProTypePat){
				$colCount = $colCount == 3 ? 0:$colCount;
				$proTypeVal = $proTypeVal == ''? 'None': $proTypeVal;
				//------CALCULATE COL SPAN---------------------------------------------
				$index = array_search($proTypeVal,array_keys($arrProTypeDataVal));
				$leftArrCount = count($arrProTypeDataVal) - ($index+1) ;
				$colSpan = ($leftArrCount == 0 && $colCount == 0)? '9' : (($leftArrCount == 0 && $colCount == 1)? '6' :  '3');
				//---------------------------------------------------------------------
				if($colCount == 0)
				$proTypeHTML .='<tr style="background-color:#ffffff;">';
				$proTypeHTML .='<td style="text-align:left;" class="text_10b" colspan="'.$colSpan.'">Procedure Type: ';
				$proTypeHTML .='<span style="font-weight:normal">'. $proTypeVal." : ".count($arrProTypePat).'</span>';
				$proTypeHTML .='</td>';	
				if($colCount == 2 || $leftArrCount == 0)
				$proTypeHTML .='</tr>';
				$colCount++;

	} //-----END FOREACH LOOP	*/		
	return $proTypeHTML;
}
?>