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
File: setAmountRTE.php
Purpose: RTE functionality implemented
Access Type: Include 
*/
require_once(dirname(__FILE__).'/../../../config/globals.php');
require_once(dirname(__FILE__).'/../../../library/classes/class.cls_eligibility.php');
$OBJEligibility = new CLSEligibility;
$rqId = (int)$_REQUEST["id"];

if($rqId > 0){
	if($_REQUEST['btSave']){
		$qryUpdate = "update real_time_medicare_eligibility set response_deductible = '".$_REQUEST['cbkDeductible']."', response_copay = '".$_REQUEST['cbkCoPay']."', response_co_insurance = '".$_REQUEST['cbkCoIns']."' where id = '".$rqId."' ";
		$rsUpdate = imw_query($qryUpdate);
	}
	$dbElPatAdd1 = $dbElPatAdd2 = $dbElPatCity = $dbElPatState = $dbElPatZip = $patAdd = $dbRespXMLPath = "";
	$strDeductibleRow = $strCopayRow = $strCoIns = "";
 	$qryGet271Report = "select DATE_FORMAT(rtme.responce_date_time , '".get_sql_date_format()."') as elDEC, concat(rtme.responce_pat_lname,', ',rtme.responce_pat_fname) as elPatName , 
						rtme.responce_pat_mname as elPatMname, DATE_FORMAT(rtme.responce_pat_dob , '".get_sql_date_format()."') as elPatDOB, 
						rtme.responce_pat_add1 as elPatAdd1, rtme.responce_pat_add2 as elPatAdd2, 
						rtme.responce_pat_city as elPatCity, rtme.responce_pat_state as elPatState,
						rtme.responce_pat_zip as elPatZip, rtme.responce_pat_policy_no as elPatPolicyNo, rtme.xml_271_responce as respXMLPath, rtme.hipaa_5010 as elHIPAA5010,
						concat(pd.lname,', ',pd.fname) as patName , 
						pd.mname as patMname, rtme.eligibility_ask_from  as elAsk, rtme.response_deductible, rtme.response_copay, rtme.response_co_insurance
						from real_time_medicare_eligibility rtme
						left join patient_data pd on pd.id = rtme.patient_id
						where rtme.id = '".$rqId." LIMIT 1'
						";
	$rsGet271Report = imw_query($qryGet271Report);					
	if($rsGet271Report){
		$arrDb = array();
		$rowGet271Report = imw_fetch_array($rsGet271Report);
		$dbElPatAdd1 = $rowGet271Report["elPatAdd1"];
		$dbElPatAdd2 = $rowGet271Report["elPatAdd2"];
		$dbElPatCity = $rowGet271Report["elPatCity"];
		$dbElPatState = $rowGet271Report["elPatState"];
		$dbElPatZip = $rowGet271Report["elPatZip"];
		$patAdd = trim($dbElPatAdd1." ".$dbElPatAdd2." ".$dbElPatCity." ".$dbElPatState.", ".$dbElPatZip);
		if($patAdd == ","){
			$patAdd = "";
		}
		if($rowGet271Report["elAsk"] == 0){
			if($rowGet271Report["elHIPAA5010"] == 0){	
				$dbRespXMLPath = $rowGet271Report["respXMLPath"];
			}
			elseif($rowGet271Report["elHIPAA5010"] == 1){	
				$dbRespXMLPath = $include_root."/main/uploaddir/".$rowGet271Report["respXMLPath"];
			}
			$arrDb["dbCoins"] = $rowGet271Report["response_co_insurance"];
			$arrDb["dbCopay"] = $rowGet271Report["response_copay"];
			$arrDb["dbDeductible"] = $rowGet271Report["response_deductible"];
			//pre($arrDb,1);
			list($strDeductibleRow, $strCopayRow, $strCoIns) = $OBJEligibility->getAmt271Response($dbRespXMLPath, $arrDb);
		}
		elseif($rowGet271Report["elAsk"] == 1){
			$dbRespXMLPath = $include_root."/main/uploaddir/".$rowGet271Report["respXMLPath"];
			$arrDb["dbCoins"] = $rowGet271Report["response_co_insurance"];
			$arrDb["dbCopay"] = $rowGet271Report["response_copay"];
			$arrDb["dbDeductible"] = $rowGet271Report["response_deductible"];
			list($strDeductibleRow, $strCopayRow, $strCoIns) = $OBJEligibility->getAmt271Response($dbRespXMLPath, $arrDb);
		}
		imw_free_result($rsGet271Report);
	}
}

$intMainDivH = $_SESSION['wn_height'] - 305;
$intNameDivH = 20;
$intRestDivH = $intMainDivH - $intNameDivH;
$intEachDivH = $intRestDivH / 3;
?>
<html>
    <head>
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/interface/themes/default/common.css" type="text/css">
        <link rel="stylesheet" href="<?php echo $css_patient; ?>" type="text/css">
		<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/admin/menuIncludes_menu/js/disableBackspace.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/common/script_function.js"></script>
        <script>
			window.focus();
			function makeOneCbk(obj){
				if(obj.checked == true){
					var strElName = obj.name;
					var field = document.getElementsByName(strElName);
					for (i = 0; i < field.length; i++){
						field[i].checked = false;
					}
					obj.checked = true;
					//alert(obj.value);
				}
			}
        </script>
    </head>
    <form name="frmSetAmountRTE" action="" method="get">
    <input type="hidden" name="id" id="id" value="<?php echo $rqId; ?>" />
        <body class="body_c bg2">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" class="div_popup_heading">
                        <b>Set Ammount For: <?php echo $rowGet271Report["patName"]." ".$rowGet271Report["patMname"]; ?></b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="height:<?php echo $intMainDivH; ?>;">
                            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <div >
                                            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                                                <tr class="div_popup_heading">
                                                    <td width="13%" class="alignLeft">
                                                        Subscriber Information
                                                    </td>
                                                    <td width="7%" class="alignLeft">
                                                        Policy No.:
                                                    </td>
                                                    <td width="14%" class="alignLeft">
                                                        <b><?php echo $rowGet271Report["elPatPolicyNo"]; ?></b>
                                                    </td>
                                                    <td width="3%" class="alignLeft">
                                                        Name: 
                                                    </td>
                                                    <td width="17%" class="alignLeft">
                                                        <b><?php echo $rowGet271Report["elPatName"]." ".$rowGet271Report["elPatMname"]; ?></b>
                                                    </td>
                                                    <td width="3%" class="alignLeft">
                                                        DOB:
                                                    </td>
                                                    <td width="8%" class="alignLeft">
                                                        <b><?php echo $rowGet271Report["elPatDOB"]; ?></b>
                                                    </td>
                                                    <td width="4%" class="alignLeft">
                                                        Address:
                                                    </td>
                                                    <td width="31%" class="alignLeft">
                                                        <b><?php echo $patAdd; ?></b>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="div_popup_heading" valign="top">
                                        <b>Deductible Information</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="height:<?php echo $intEachDivH; ?>; overflow:auto">
                                            <table width="100%" border="0" cellpadding="0" cellspacing="2">
                                                <!--<tr>
                                                    <td colspan="12" class="div_popup_heading" valign="top">
                                                        <b>Deductible Information</b>
                                                    </td>
                                                </tr>-->
                                                <tr class="page_block_heading_patch">
                                                    <td width="2%"  valign="top">
                                                    </td>
                                                    <!--<td width="12%"  valign="top">
                                                        Insurance Type                                
                                                    </td>-->
                                                    <td width="13%"  valign="top">
                                                        Service Type
                                                    </td>
                                                    <td width="6%"  valign="top">
                                                        Coverage Level
                                                    </td>
                                                    <td width="8%"  valign="top">
                                                        Plan Coverage Description
                                                    </td>
                                                    <td width="4%"  valign="top">
                                                        Amount                               
                                                    </td>
                                                    <td width="6%"  valign="top">
                                                        Time Period                                
                                                    </td>
                                                    <td width="4%"  valign="top">
                                                        Percent                               
                                                    </td>
                                                     <td width="5%"  valign="top">
                                                        Quantity
                                                        <div>Qualifier</div>
                                                    </td>
                                                    <td width="11%"  valign="top">
                                                        Authorization or Certification Indicator
                                                    </td>
                                                    <td width="11%"  valign="top">
                                                        Plan Network Indicator
                                                    </td>
                                                    <td width="18%" valign="top">
                                                        Date
                                                        <div>Date Detail</div>
                                                    </td>
                                                </tr>                                                            
                                                <?php
                                                if($strDeductibleRow != ""){
                                                    echo $strDeductibleRow; 
                                                }
                                                else{
                                                    echo "<tr><td colspan=\"12\">No Deductible Information Found</td></tr>";
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="div_popup_heading" valign="top">
                                        <b>Co-Payment Information</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="height:<?php echo $intEachDivH; ?>; overflow:auto">
                                            <table width="100%" border="0" cellpadding="0" cellspacing="2">
                                                <!--<tr>
                                                    <td colspan="12" class="div_popup_heading" valign="top">
                                                        <b>Co-Payment Information</b>
                                                    </td>
                                                </tr>-->
                                                <tr class="page_block_heading_patch">
                                                    <td width="2%"  valign="top">
                                                    </td>
                                                    <!--<td width="12%"  valign="top">
                                                        Insurance Type                                
                                                    </td>-->
                                                    <td width="13%"  valign="top">
                                                        Service Type
                                                    </td>
                                                    <td width="6%"  valign="top">
                                                        Coverage Level
                                                    </td>
                                                    <td width="8%"  valign="top">
                                                        Plan Coverage Description
                                                    </td>
                                                    <td width="4%"  valign="top">
                                                        Amount                               
                                                    </td>
                                                    <td width="6%"  valign="top">
                                                        Time Period                                
                                                    </td>
                                                    <td width="4%"  valign="top">
                                                        Percent                               
                                                    </td>
                                                     <td width="5%"  valign="top">
                                                        Quantity
                                                        <div>Qualifier</div>
                                                    </td>
                                                    <td width="11%"  valign="top">
                                                        Authorization or Certification Indicator
                                                    </td>
                                                    <td width="11%"  valign="top">
                                                        Plan Network Indicator
                                                    </td>
                                                    <td width="18%" valign="top">
                                                        Date
                                                        <div>Date Detail</div>
                                                    </td>
                                                </tr>                                                            
                                                <?php
                                                if($strCopayRow != ""){
                                                    echo $strCopayRow; 
                                                }
                                                else{
                                                    echo "<tr><td colspan=\"12\">No Co-Payment Information Found</td></tr>";
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="div_popup_heading" valign="top">
                                        <b>Co-Insurance Information</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="height:<?php echo $intEachDivH; ?>; overflow:auto">
                                            <table width="100%" border="0" cellpadding="0" cellspacing="2">
                                                <!--<tr>
                                                    <td colspan="12" class="div_popup_heading" valign="top">
                                                        <b>Co-Insurance Information</b>
                                                    </td>
                                                </tr>-->
                                                <tr class="page_block_heading_patch">
                                                    <td width="2%"  valign="top">
                                                    </td>
                                                    <!--<td width="12%"  valign="top">
                                                        Insurance Type                                
                                                    </td>-->
                                                    <td width="13%"  valign="top">
                                                        Service Type
                                                    </td>
                                                    <td width="6%"  valign="top">
                                                        Coverage Level
                                                    </td>
                                                    <td width="8%"  valign="top">
                                                        Plan Coverage Description
                                                    </td>
                                                    <td width="4%"  valign="top">
                                                        Amount                               
                                                    </td>
                                                    <td width="6%"  valign="top">
                                                        Time Period                                
                                                    </td>
                                                    <td width="4%"  valign="top">
                                                        Percent                               
                                                    </td>
                                                     <td width="5%"  valign="top">
                                                        Quantity
                                                        <div>Qualifier</div>
                                                    </td>
                                                    <td width="11%"  valign="top">
                                                        Authorization or Certification Indicator
                                                    </td>
                                                    <td width="11%"  valign="top">
                                                        Plan Network Indicator
                                                    </td>
                                                    <td width="18%" valign="top">
                                                        Date
                                                        <div>Date Detail</div>
                                                    </td>
                                                </tr>                                                            
                                                <?php
                                                if($strCoIns != ""){
                                                    echo $strCoIns; 
                                                }
                                                else{
                                                    echo "<tr><td colspan=\"12\">No Co-Insurance Information Found</td></tr>";
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                	<td bgcolor="#93b9dc" width="100%" align="center">
                    	<table width="100%" cellpadding="0" cellspacing="0">
                        	<tr id="footersid" height="10">
                                <td width="9%" align="left">
                                	<img src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/images/logo.jpg"> 
                                </td>
                                <td width="71%" align="center" nowrap="nowrap">
                                   	<input type="submit" class="dff_button" name="btSave" id="btSave" onMouseOver="button_over('btSave')" onMouseOut="button_over('btSave', '')" value="Save" />
                        <input type="button" class="dff_button" name="btClose" id="btClose" onMouseOver="button_over('btClose')" onMouseOut="button_over('btClose', '')" value="Close" onClick="window.close();"/>
                                </td>
                                <td width="20%" align="right" class="text_10"><b><?php echo date('D M dS Y ');?><span id="dt_tm"></span></b></td>
                            </tr>                                  
                        </table> 
                      </td>
                  </tr>
            </table>
        </body>
        <script language="javascript">
			function show2(){
				if (!document.all&&!document.getElementById)
				return
					thelement=document.getElementById? document.getElementById("dt_tm"): document.all.dt_tm
				var Digital=new Date()
				var hours=Digital.getHours()
				var minutes=Digital.getMinutes()
				var seconds=Digital.getSeconds()
				var dn="PM"
				if (hours<12)
					dn="AM"
				if (hours>12)
					hours=hours-12
				if (hours==0)
					hours=12
				if (minutes<=9)
					minutes="0"+minutes
				if (seconds<=9)
					seconds="0"+seconds
				var ctime=hours+":"+minutes+":"+seconds+" "+dn
				thelement.innerHTML="<b style='font-size:10;color:#4A67A2; font-family:Verdana'>"+ctime+"</b>"
				setTimeout("show2()",1000)
			}
			show2();
		</script>
	</form>
</html>