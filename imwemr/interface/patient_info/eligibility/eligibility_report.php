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
$setRTEAmt = (isset($_REQUEST['set_rte_amt'])) ? trim($_REQUEST['set_rte_amt']) : false;

if($rqId > 0){
	if($_POST['btSave']){
		$qryUpdate = "update real_time_medicare_eligibility set response_deductible = '".$_POST['cbkDeductible']."', response_copay = '".$_POST['cbkCoPay']."', response_co_insurance = '".$_POST['cbkCoIns']."' where id = '".$rqId."' ";
		$rsUpdate = imw_query($qryUpdate);
		if($rsUpdate && constant('SET_COPAY_FROM_RTE')){
			$temp_copay_amt_arr = explode('-',$_POST['cbkCoPay']);
			imw_query("UPDATE insurance_data SET copay='".$temp_copay_amt_arr[4]."' WHERE id='".$_POST['hid_ins_data_id']."'");
			unset($temp_copay_amt_arr);
		}
	}
	
 	$q = "select rtme.request_270_file_path as requTXTPath,rtme.response_271_file_path as respTXTPath, rtme.eligibility_ask_from  as elAsk, rtme.hipaa_5010 as elHIPAA5010, 
						rtme.response_deductible, rtme.response_copay,rtme.response_co_insurance, rtme.ins_data_id,
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
		$db_ins_data_id = $rs['ins_data_id'];
		
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
			die('Eligibility response not received for this request.');
		}
		
	}else{
		die('Invalid report ID provided. NO RTE data found.');
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Eligibility Report Details</title>
    <!-- Bootstrap -->
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <!-- Application Common CSS -->
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">   
    <link href="<?php echo $library_path; ?>/css/billinginfo.css" rel="stylesheet">    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <!-- Bootstrap -->
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
    <!-- Application Common JS -->
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
	function enlarge_ins_image(o){
		s = o.src;
		top.show_modal('InsscanCard','Insurance Card','<img src="'+s+'" style="max-width:800px; max-height:600px;">','','350','modal-lg');
	}
	function edi_source_code(val){
		if(typeof(val)=='undefined') {title = 'Response Request EDI Source'; sc = $('#source_code').val();}
		else{title = 'Request EDI Source'; sc = $('#source_code_request').val();}
		top.show_modal('EDI_source_code',title,'<textarea style="width:100%; height:300px;">'+sc+'</textarea>','','350','modal-lg');	
	}
	function toggle_display(o){
		to = $(o).next();
		if(to.css('display')!='none') to.fadeOut();
		else to.fadeIn();
	}
	function chbkASradio(t){
		o = $(t);
		curr_class = o.attr('class');
		if(o.prop('checked')){
			$('.'+curr_class).prop('checked',false);
			o.prop('checked',true);
		}
	}
	</script>
    <style type="text/css">
	.resultset_div table td {white-space:normal;}
	table tr td{vertical-align:top !important;}
	</style>
  </head>
  <body>
  	<div class="container-fluid mtb10">
        <form name="frmSetAmountRTE" action="" method="POST" style="margin:0px;">
        <input type="hidden" name="id" id="id" value="<?php echo $rqId; ?>" />
        <input type="hidden" name="hid_ins_data_id" value="<?php echo $db_ins_data_id;?>" />
    	<div class="whitebox">
            <div class="row" id="eleg_head1">
                <div class="col-sm-3">
                	<big><b>ELIGIBILITY REPORT DETAILS</b></big>
                </div>
                <div class="col-sm-3">
					<?php if($strCard1SRCMain){?>
                        <span class="pull-right"><img style="margin-right:50px;max-height:22px; cursor:pointer" src="<?php echo $strCard1SRCMain;?>" onClick="enlarge_ins_image(this);"></span>
                    <?php }?>
                    <?php if($strCard2SRCMain){?>
                        <span class="pull-right"><img style=" margin-right:10px;max-height:22px; cursor:pointer" src="<?php echo $strCard2SRCMain;?>" onClick="enlarge_ins_image(this);"></span>
                    <?php }?>                  
                </div>
                <div class="col-sm-3 text-right">
                    <img src="../../../library/images/icon_source_code.png" onClick="edi_source_code('request');" title="View Eligibility Request Source Code">&nbsp;&nbsp;
                    <img src="../../../library/images/icon_source_code.png" onClick="edi_source_code();" title="View Eligibility Response Source Code">&nbsp;&nbsp;&nbsp;
            	</div>
                <div class="col-sm-3 text-right"><?php echo $rs['patient_name_id'];?>&nbsp; </div>
            </div>
            <div class="clearfix"></div>
            <div id="eleg_head2" class="row">
            	<div class="col-sm-12">
				<?php if($RTEresponse['error']!=''){echo $RTEresponse['error'];}
                else{
                     $subs_details	= $RTEresponse['result']['2100C'];
                     $info_source	= $RTEresponse['result']['2100A'];
                     $info_receiver	= $RTEresponse['result']['2100B'];
                     $EB_response	= $RTEresponse['result']['2110C'];
                ?>            
                <table class="table table-striped"><tr>
                <td class="col-sm-4" style="vertical-align:top !important">
                    <div class="grythead"> &nbsp;Subscriber Details</div>
                    <table class="table table-striped">					
                    <tr><td><b>Subscriber</b></td><td><?php echo $subs_details['NM1']['Subscriber_Name'];?></td></tr>
                    <tr><td><b><?php echo $subs_details['NM1']['Billing_Provider_ID_Type'];?></b></td><td><?php echo $subs_details['NM1']['Subscriber_ID'];?></td></tr>
                    <tr><td><b>Address</b></td><td><?php echo trim($subs_details['N3']['Street1'].' '.$subs_details['N3']['Street1']);?><br>
                                            <?php echo $subs_details['N4']['City'];
                                            if($subs_details['N4']['State/Province']!='') echo ', '.$subs_details['N4']['State/Province'];
                                            if($subs_details['N4']['Zip_Code']!='') echo ', '.$subs_details['N4']['Zip_Code'];
                                            ?>
                        </td>
                    </tr>
                    <?php if($subs_details['DMG']['DOB']!='') echo '<tr><td><b>DOB</b></td><td>'.$subs_details['DMG']['DOB'].'</td></tr>';
                          if($subs_details['DMG']['Sex']!='') echo '<tr><td><b>Sex</b></td><td>'.$subs_details['DMG']['Sex'].'</td></tr>';
                    ?>
                    </table>
                    <?php if(is_array($subs_details['AAA'])){//ERROR CODE RETURNED
                         echo '<table class="table"><tr><th class="bg-warning" colspan="2">Error/Warnings</th></tr>';
                         foreach($subs_details['AAA'] as $k=>$v){
                            echo '<tr><th class="text-left">'.str_replace('_',' ',$k).'</th><td>'.$v.'</td></tr>'; 
                         }
                         echo '</table>';
                     }
                     ?>
                </td>
                <td class="col-sm-4" style="vertical-align:top !important">
                     <div class="grythead"> &nbsp;Information Source</div>
                     <table class="table">					
                     <tr><td><b>Payer Name</b></td><td><?php echo $info_source['NM1']['Payer_Name'].' <span class="text_small">('.$info_source['NM1']['ENTITY_TYPE'].')</span>'; ?></td></tr>
                     <tr><td><b>Payer Identification</b></td><td><?php echo $info_source['NM1']['Payer_ID']; ?></td></tr>
                     <?php $info_source_per = $info_source['PER'];
                     foreach($info_source_per as $k=>$v){?>
                         <tr><td><b><?php echo $k;?></b></td><td><?php echo $v;?></td></tr>
                     <?php }?>
                     </table>
                     <?php if(is_array($info_source['AAA'])){//ERROR CODE RETURNED
                         echo '<table class="table_collapse bg3 border"><tr><th class="bg-warning" colspan="2">Error/Warnings</th></tr>';
                         foreach($info_source['AAA'] as $k=>$v){
                            echo '<tr><th class="text-left">'.str_replace('_',' ',$k).'</th><td>'.$v.'</td></tr>'; 
                         }
                         echo '</table>';
                     }
                     ?>
                </td>
                <td class="col-sm-4" style="vertical-align:top !important">
                    <div class="grythead"> &nbsp;Information Receiver</div>
                     <table class="table">		
                    <tr><td>Billing Provider</td><td><?php echo $info_receiver['NM1']['Billing_Provider_Name'];?></td></tr>
                     <tr><td><?php echo $info_receiver['NM1']['Billing_Provider_ID_Type']; ?></td><td><?php echo $info_receiver['NM1']['Billing_Provider_ID']; ?></td></tr>
                     </table>
                     <?php if(is_array($info_receiver['AAA'])){//ERROR CODE RETURNED
                         echo '<table class="table_collapse bg3 border"><tr><th class="bg-warning" colspan="2">Error/Warnings</th></tr>';
                         foreach($info_receiver['AAA'] as $k=>$v){
                            echo '<tr><th class="text-left">'.str_replace('_',' ',$k).'</th><td>'.$v.'</td></tr>'; 
                         }
                         echo '</table>';
                     }
                     ?>
                </td>
                </tr></table>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
            <div class="col-sm-12">
                <input name="source_code_request" id="source_code_request" type="hidden" value="<?php echo addslashes($db270RequestText);?>">
                <input name="source_code" id="source_code" type="hidden" value="<?php echo addslashes($db271ResponseText);?>">
                <!-- Hidden Fields Section End -->
                    <div class="table-responsive col-sm-12 resultset_div">
                    <?php foreach($EB_response as $EB_type=>$EB_array){
                        $msgonly = false;
                        if(strtolower($EB_type)=='benefit disclaimer'){$msgonly = true;}
                        ?>
                         <h3 class="section_header" onClick="toggle_display(this)"><?php echo $EB_type;?></h3>
                            <?php if(!$msgonly){?>
                            <table class="table table-bordered table-striped">
                            <thead>
                            <tr class="grythead">
                            <?php if($setRTEAmt && $setRTEAmt=='yes'){?><th>&nbsp;</th><?php }?>
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
                            }
                            $DTP_key = $DTP_val = '';
                            $alt_class = ' class="bg2"';
                            $this_section_chk = false;
                            foreach($EB_array as $k=>$v){
                                 $eb_data_Arr = $v[0]; foreach($eb_data_Arr['DTP'] as $dtp_type=>$dtp_val){$DTP_key = $dtp_type; $DTP_val = $dtp_val;};
                                 if(!$msgonly){
                                    //GETTING DATE
                                    ?>
                                    
                                    <tr>
                                    <?php if(isset($setRTEAmt) && $setRTEAmt=='yes'){
                                         $chbk_name = ''; $sel_chbk = ''; 
                                         if(strtolower($EB_type)=='co-insurance'){$chbk_name = 'cbkCoIns';}
                                         else if(strtolower($EB_type)=='co-payment'){$chbk_name = 'cbkCoPay';}
                                         else if(strtolower($EB_type)=='deductible'){$chbk_name = 'cbkDeductible';}
                                         if($chbk_name!=''){
                                             if(($db_deductible==$eb_data_Arr['Set_RTE_Amt']  || $db_copay==$eb_data_Arr['Set_RTE_Amt'] || $db_coins==$eb_data_Arr['Set_RTE_Amt']) && $this_section_chk==false){$sel_chbk = ' checked';$this_section_chk=true;}else{$sel_chbk = '';}
                                    ?>
                                            <td><input type="checkbox" value="<?php echo $eb_data_Arr['Set_RTE_Amt'];?>" name="<?php echo $chbk_name;?>" class="chbk_<?php echo $chbk_name;?>" onClick="chbkASradio(this);"<?php echo $sel_chbk;?>></td>
                                    <?php }else{echo '<td></td>';}
                                      }?>
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
                                                //echo '<b>'.str_replace('_',' ',$k1).'</b>: '.$v1.'<br>';
                                                //echo $v1.'<br>';
                                            }
                                        }
                                        echo '</div>';
                                    }
                                    if($eb_data_Arr['Auth_Cert_Indicator']!= '') echo '<div class="m5 bg6"><i>Authorization or Cert. Required:</i> '.$eb_data_Arr['Auth_Cert_Indicator'].'</div>';
                                    if($eb_data_Arr['Benefits_In_Plan_Network']!= '') echo '<div class="m5 bg6"><i>Benefits in Plan Network:</i> '.$eb_data_Arr['Benefits_In_Plan_Network'].'</div>';
                                     ?></td>
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
                                    
                                    ?></td>
                                    <td class="text-center"><?php echo $eb_data_Arr['Time_Period_Qualifier']; ?></td>
                                    <td class="text-center"><?php echo str_replace('_',' ',$DTP_key).'<br>'.$DTP_val; if($eb_data_Arr['Health_Care_Service_Delivery']!='') echo '<br>'.$eb_data_Arr['Health_Care_Service_Delivery'];?></td>
                                    <td class="text-right"><?php echo $eb_data_Arr['Benefit_Amount']; if($eb_data_Arr['Benefit_Amount']!='' && $eb_data_Arr['Benefit_Percentage']!=''){echo '<br>';} echo $eb_data_Arr['Benefit_Percentage'];?>&nbsp;&nbsp;&nbsp;</td>
                                    <td class="text-center"><?php echo $eb_data_Arr['Quantity']; ?><br><?php echo $eb_data_Arr['Quantity_Qualifier']; ?></td>
                                    <td><?php echo $eb_data_Arr['Comments'];?></td>
                                    </tr>
                            <?php 
                                 }else{
                                     echo '<div class="m10 section">'.$eb_data_Arr['Comments'].'</div>';
                                 }
                            }
                            if(!$msgonly){
                            ?>
                            </tbody>
                            </table>
        
                    <?php	}
                         }?>            
                    </div>
                    
                    <?php }?>
                
            </div>
            </div>
			<div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-center">
               <?php if(isset($setRTEAmt) && $setRTEAmt=='yes'){?>
                <button type="submit" class="btn btn-success" name="btSave" id="btSave" value="1">Save</button> &nbsp; &nbsp;&nbsp;<?php }?>
                <button class="btn btn-warning" onClick="javascript:window.close();">Close Window</button>
             </div>
        </div>
        <div class="clearfix"></div>
        </form>
  	</div>
    <script>
		if(typeof(window.opener.top.innerDim)=='function'){
			var innerDim = window.opener.top.innerDim();
			if(innerDim['w'] > 1600) innerDim['w'] = 1600;
			if(innerDim['h'] > 900) innerDim['h'] = 900;
			window.resizeTo(innerDim['w'],innerDim['h']);
			brows	= get_browser();
			if(brows!='ie') innerDim['h'] = innerDim['h']-35;
			var result_div_height = innerDim['h']-(($('#eleg_head1').height()*6)+($('#eleg_head2').height()));
			//alert(innerDim['h']+' :: '+$('#eleg_head1').height()+' :: '+$('#eleg_head2').height()+' :: '+result_div_height);
			$('.resultset_div').height((result_div_height-20)+'px');
		}
	</script>
	</body>  
</html>