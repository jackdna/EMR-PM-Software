<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
set_time_limit(500);
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['iolink_loginUserId'];
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<script>
var selectDay='';
var selectPrevDay='';
var prevColor='';
function iOLink_swap_cal_color(obj,cond,selectDay,selectPrevDay,prevColor){
	if(selectDay) {
		selectDay = parseFloat(selectDay);
	}
	if(selectPrevDay) {
		selectPrevDay = parseFloat(selectPrevDay);
	}
	if(document.getElementById(obj)){
		if(cond=="Yes"){
			
			document.getElementById(obj).style.backgroundColor="#FBD78D";
			if(selectPrevDay && selectPrevDay!=selectDay) {
				var prevDayColor = document.getElementById('hiddSelectedPrevDayColor').value;
				if(document.getElementById('mon_'+selectPrevDay)) {
					document.getElementById('mon_'+selectPrevDay).style.backgroundColor=prevDayColor;
				}
				document.getElementById('hiddSelectedPrevDayId').value=selectDay;
				if(document.getElementById('hiddSelectedPrevDayColor')) {
					document.getElementById('hiddSelectedPrevDayColor').value=prevColor;
				}
				
			}
		}else{
			if(selectDay && selectDay==parseFloat(document.getElementById('hiddSelectedDayId').value)) { 
				//DO NOTHING
			}else if(prevColor){
				
				document.getElementById(obj).style.backgroundColor=prevColor;
			}else {
				document.getElementById(obj).style.backgroundColor="";
			}	
		}
	}
}

//START
function iOLink_change_month(month_number,year_number,surgeon_name_id) {
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	 }

	var curDy = document.getElementById('hiddSelectedDayId').value;	
	if(document.getElementById('mon_'+curDy)) {
		document.getElementById('hiddSelectedPrevDayColor').value=document.getElementById('mon_'+curDy).style.backgroundColor;
	}
	
	var url="iOLink_cal_ajax.php"
	url=url+"?sel_month_number="+month_number
	url=url+"&year_now="+year_number
	url=url+"&reqUserId="+surgeon_name_id
	
	xmlHttp.onreadystatechange=iOLink_stateCalFun 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
function iOLink_stateCalFun(){ 
	
	if(xmlHttp.readyState==1) {
		document.getElementById("iOLink_cal_ajax_id").innerHTML='<center><img src="images/pdf_load_img.gif"></center>';
	}
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("iOLink_cal_ajax_id").innerHTML=xmlHttp.responseText; 
	} 
}

function iOLinkDisplayTimeDiv(lftPos,topPos,objId,disp) {
	topPos=parseInt(topPos)+280;
	lftPos=parseInt(lftPos)-15;
	if(document.getElementById(objId)) {
		if(top.iframeHome.document.getElementById("surgeryTimeDivId")) { //SEE this div in iOLink.php
			if(disp=="Yes"){ 
				if(document.getElementById(objId).innerHTML) {
					top.iframeHome.document.getElementById("surgeryTimeDivId").innerHTML = document.getElementById(objId).innerHTML;
					top.iframeHome.document.getElementById("surgeryTimeDivId").style.display='block';
					top.iframeHome.document.getElementById("surgeryTimeDivId").style.left=lftPos;
					top.iframeHome.document.getElementById("surgeryTimeDivId").style.top=topPos;
				}
			}else {
				top.iframeHome.document.getElementById("surgeryTimeDivId").style.display='none';
			}
		}	
	}
}
function iOLinkHideTimeDiv(lftPos,rtPos,objId) {
	if(document.getElementById(objId)) {
		document.getElementById(objId).style.display='none';
	}
}

function schClick(year_number,month_number,day_number,surgeon_name_id) {
	top.iframeHome.iOLinkBookSheetFrameId.location.href='iOLinkBookingSheet.php?day_number='+day_number+'&sel_month_number='+month_number+'&year_now='+year_number+'&reqUserId='+surgeon_name_id;
}
</script>
<?php
$spec= "
</head>
<body>";

include("common/link_new_file.php");
include("common/iOlinkFunction.php");
include("common/iOLinkCommonFunction.php");

$practiceName = getPracticeName($loginUser,'Coordinator');
$coordinatorType = getCoordinatorType($loginUser);
?>



<form name="frmiOLinkScheduler" action="ioLinkScheduler.php" method="post">
	<input type="hidden" name="hiddSelectedDayId" id="hiddSelectedDayId" value="<?php echo date('d');?>">
	<input type="hidden" name="hiddSelectedPrevDayId" id="hiddSelectedPrevDayId" value="<?php echo date('d');?>">
    <input type="hidden" name="hiddSelectedPrevDayColor" id="hiddSelectedPrevDayColor" value="">
<table class="table_collapse" style="border:none;">
	<tr class="valignTop">
        <td class="text_10" id="iOLink_cal_ajax_id" >
            <table class="table_collapse">
                <tr class="valignTop">
                    <td class="text_10b valignTop " style="width:100%; background-image:url(<?php echo $bgHeadingImage;?>);">
                        <?php 
                        //CODE FOR CALENDER
                            if(!$year_now) { $year_now=date('Y'); }
                            if($_REQUEST["sel_month_number"]<>""){
                                $selected_month_number=$_REQUEST["sel_month_number"];
                                $year_now=$_REQUEST["year_now"];
                                if($selected_month_number>12) {
                                    $selected_month_number = 1;
                                    if(strlen($selected_month_number)==1) {
                                        $selected_month_number = '0'.$selected_month_number;
                                    }
                                    $year_now = $year_now+1;
                                }
                                if($selected_month_number==0) {
                                    $selected_month_number = 12;
                                    $year_now = $year_now-1;
                                }
                            }else{	
                                $selected_month_number = date("m");
                                $year_now = date("Y");
                            }
                            $selected_month_number_IncrByOne = $selected_month_number+1;//date("m",mktime(0,0,0,$selected_month_number+1,1,$year_now));
                            $selected_month_number_DecrByOne = $selected_month_number-1;//date("m",mktime(0,0,0,$selected_month_number-1,1,$year_now));
                            $year_now_IncrByOne = $year_now+1;//date("Y",mktime(0,0,0,$selected_month_number,1,$year_now+1));
                            $year_now_DecrByOne = $year_now-1;//date("Y",mktime(0,0,0,$selected_month_number,1,$year_now-1));
                            $lastday=date("t",mktime(0,0,0,$selected_month_number,1,$year_now));
                            $weekday=date("w",mktime(0,0,0,$selected_month_number,1,$year_now));
                            if($weekday==0) {
                                $weekday = 7;
                            }
                            $days=array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
                            $month_name = date("F",mktime(0,0,0,$selected_month_number,1,$year_now));
                        //CODE FOR CALENDER
                        ?>	
                        <input type="hidden" name="selected_month_number" id="selected_month_number" value="<?php echo $selected_month_number;?>">
                        <input type="hidden" name="year_now" id="year_now" value="<?php echo $year_now;?>">
                        <table class="table_collapse" style="border:none;">
                            <tr class="valignTop" style="height:24px;">
                                <?php $reqUserId= $_REQUEST['reqUserId'];?>
                                <td class="tst11b nowrap"  style=" width:16%; padding-left:5px;background-image:url(<?php echo $bgHeadingImage;?>);">
                                    Surgeon&nbsp;<select name="surgeon_name_id" id="surgeon_name_id" class="field text_10" onChange="javascript:iOLink_change_month(<?php echo $selected_month_number;?>,'<?php echo $year_now;?>',document.getElementById('surgeon_name_id').value);"  style=" font-size:11px;width:140px;border:1px solid #cccccc; margin-top:2px;">
                                        <?php
                                        $getSurgeosDetails=array();
                                        $strQuery1Part="";
									    if($coordinatorType!='Master') { //DISPLAY ALL SUGEON FOR MASTER COORDINATOR
	                                        $strQuery1Part=getPracticeUser($practiceName,"AND");   
										}
										$qrySurgeonDetail="Select * FROM users Where user_type='Surgeon' ".$strQuery1Part." ORDER BY lname ASC";
										$resSurgeonDetail=imw_query($qrySurgeonDetail)or die(imw_error());
										if(imw_num_rows($resSurgeonDetail)>0){
											while($rowSurgeosDetails=imw_fetch_object($resSurgeonDetail)){
												$getSurgeosDetails[]=$rowSurgeosDetails;
											}
										}
                                        if(count($getSurgeosDetails)>=2) {
                                        ?>
                                            <option value="">All Surgeon</option>
                                        <?php
                                        }
                                        
                                        foreach($getSurgeosDetails as $surgeonsList){
                                            $usersId = $surgeonsList->usersId;
                                            if(count($getSurgeosDetails)==1) {
                                                $reqUserId=$surgeonsList->usersId;
                                            }
                                            $surgeonFname = trim($surgeonsList->fname);
                                            $surgeonLname = trim($surgeonsList->lname);
                                            $surgeonMname = trim($surgeonsList->mname);
                                            if($surgeonMname) {
                                                $surgeonMname = ' '.$surgeonMname;
                                            }
                                            $surgeonName = $surgeonFname.$surgeonMname.' '.$surgeonLname;
                                            $surgeon_deleteStatus = $surgeonsList->deleteStatus;
                                            if($surgeon_deleteStatus=="Yes") {
                                            }else{
                                            ?>
                                                <option value="<?php echo $usersId; ?>" <?php if($reqUserId==$usersId) { echo 'selected'; }?>><?php echo stripslashes($surgeonLname.', '.$surgeonFname.' '.$surgeonMname); ?></option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>&nbsp;
                                </td>
                                <td class="text_10 alignLeft" style=" width:50%;background-image:url(<?php echo $bgHeadingImage;?>);">
                                    <table class="table_pad_bdr alignCenter">
                                        <tr>
                                            <td class="alignCenter">
                                                <a href="javascript:iOLink_change_month(<?php echo $selected_month_number;?>,'<?php echo $year_now_DecrByOne;?>',document.getElementById('surgeon_name_id').value);">
                                                    <img  src="images/DoubleArrowLeft.jpg" alt="Previous Year" style="margin-top:4px; border:none; " />
                                                </a>		
                                            </td>
                                            <td style="width:5px;">&nbsp;</td>
                                            <td class="alignCenter">
                                                <a href="javascript:iOLink_change_month(<?php echo $selected_month_number_DecrByOne;?>,'<?php echo $year_now;?>',document.getElementById('surgeon_name_id').value);">
                                                    <img src="images/SingleArrowLeft.jpg" alt="Previous Month" style="border:none;"/>
                                                </a>
                                            </td>
                                            <td style="width:5px;">&nbsp;</td>					
                                            <td class="text_10b alignCenter nowrap" style="font-size:9px; ">
                                                <select class="text_10" name="monthList" id="monthList" onChange="javascript:iOLink_change_month(this.value,'<?php echo $year_now;?>',document.getElementById('surgeon_name_id').value);" style=" font-size:10px;width:90px;border:1px solid #cccccc; margin-top:2px;">
                                                    <?php
                                                    for($k=1;$k<=12;$k++) {
                                                        $monthListValue = date("F",mktime(0,0,0,$k,1,$year_now));
                                                        if(strlen($k)==1) { $k='0'.$k;}
                                                        if(strlen($selected_month_number)==1) { $selected_month_number='0'.$selected_month_number;}
                                                    ?>
                                                        <option value="<?php echo $k;?>" <?php if($selected_month_number==$k) { echo "selected"; }?>><?php echo $monthListValue;?></option>
                                                    <?php
                                                    }
                                                    ?>	
                                                </select>
                                                
                                                
                                            </td>
                                            <td class="tst11b" style="width:5px;" >&nbsp;<?php echo $year_now;?>&nbsp;</td>
                                            <td class="alignCenter">
                                                <a href="javascript:iOLink_change_month(<?php echo $selected_month_number_IncrByOne;?>,'<?php echo $year_now;?>',document.getElementById('surgeon_name_id').value);">
                                                    <img src="images/SingleArrowRight.jpg"  alt="Next Month" style="margin-top:3px; border:none; " />
                                                </a>
                                            </td>
                                            
                                            <td class="alignCenter">
                                                <a href="javascript:iOLink_change_month(<?php echo $selected_month_number;?>,'<?php echo $year_now_IncrByOne;?>',document.getElementById('surgeon_name_id').value);">
                                                    <img src="images/DoubleArrowRight.jpg"  alt="Next Year" style="margin-left:3px;margin-top:3px; border:none; "  />
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td  class="text_10 alignLeft" style="background-image:url(<?php echo $bgHeadingImage;?>);"></td>
                            </tr>
                        </table>
                    </td>
                </tr>	
                <tr class="valignTop">
                    <td colspan="3">
                        <table class="alignCenter" style="border:solid 1px; border-color:#9FBFCC; border:none; width:100%; height:100%; padding:2px; ">
                            <Tr class="text_10b" style="background-color:#F8F9F7; height:10px;">
                                <?php 
                                for($i=0;$i<7;$i++){?>
                                    <Td class="text_10" style="border:solid 1px; border-color:#9FBFCC;"><?php echo $days[$i];?></Td>
                                    <?php 
                                }
                                ?>
                            </Tr>
                            <TR class="alignRight valignTop" style="height:65px; background-color:#FFFFFF;">
                                <?php 
                                $j=1;
                                $weekday2 = 0;
                                $days = false;
                                $p = 1;
                                $emptyBlocks = 0;
                                $calHTML='';
                                $rowCount = 1;	
                                $intLastDisplay = 0;			
                                $surgeonHighlightArr=array();
                                while($p<$lastday){
                                    if($days == true){
                                        $p++;
                                    }
                                    $selDos=$year_now.'-'.$selected_month_number.'-'.$p;
                                    if($j<=7){				
                                        $weekday2++;
                                        $color="";
                                        if($p==date("d")){
                                            //$color="#FBD78D";
                                            $txtColor="#FF0000";
                                        }else{
                                            
                                            $txtColor="";
                                        }
                                        unset($surgeonHighlightArr);
                                        if($weekday2==$weekday || $days == true){				
                                            $days = true;
                                            $surgeonHighlightArr =  getFirstSurgeryColor($selDos,$reqUserId,'makeDivYes',$practiceName,$coordinatorType);
                                            if(@in_array($p,$surgeonHighlightArr)) { $color="#D7E4EA";}
                                            
                                            $calHTML .= "<TD id='mon_".$p."' class='alignLeft' style='border:solid 1px; border-color:#9FBFCC; width:50px; background-color:".$color.";' onMouseOver='javascript:iOLink_swap_cal_color(this.id,\"Yes\",".$p.");'  onMouseOut='javascript:iOLink_swap_cal_color(this.id,\"No\",".$p.",\"\",\"$color\");' >
                                                            ".getFirstSurgeryTime($selDos,$reqUserId,'makeDivYes',$practiceName,$coordinatorType).
                                                            "<table class='table_collapse alignLeft' style='border:none;'>
                                                                <tr>
                                                                    <td class='text_10 alignLeft' style='font-size:9px; width:10%; cursor:pointer; ' onMouseOver='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$p."\"))+50,parseInt(findPos_Y(\"mon_".$p."\"))+30,\"iOLinkSurgeryTimeId".$p."\",\"Yes\");' onMouseOut='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$p."\")),parseInt(findPos_Y(\"mon_".$p."\")),\"iOLinkSurgeryTimeId".$p."\",\"No\");'>".getFirstSurgeryTime($selDos,$reqUserId,'',$practiceName,$coordinatorType)."</td>
                                                                    <td class='text_10 valignTop alignRight' style='width:90%;'><a class='link_home text_10' style='color:$txtColor;' href='javascript:void(0);' onClick='javascript:document.getElementById(\"hiddSelectedDayId\").value=".$p.";iOLink_swap_cal_color(\"mon_$p\",\"Yes\",".$p.",document.getElementById(\"hiddSelectedPrevDayId\").value,\"$color\");schClick(\"$year_now\",\"$selected_month_number\",\"$p\",\"$reqUserId\");'>$p</a></td>
                                                                </tr>
                                                            </table>		
                                                         </td>";								
                                        }else{
                                            $emptyBlocks++;
                                            $calHTML .= "<TD class='alignLeft' style='border:solid 1px; border-color:#9FBFCC;width:50px;' >{".$emptyBlocks."}</td>";	
                                        }
                                        if($j%7==0){
                                            $calHTML .= "</Tr><tr class='alignRight valignTop' style='height:65px;background-color:#FFFFFF;'>";
                                        }
                                    }else{	
                                        $color="";
                                        if($p==date("d")){
                                            //$color="#FBD78D";
                                            $txtColor="#FF0000";
                                        }else{
                                            
                                            $txtColor="";
                                        }
                                        unset($surgeonHighlightArr);
                                        if($rowCount <= 4){
                                            $surgeonHighlightArr =  getFirstSurgeryColor($selDos,$reqUserId,'makeDivYes',$practiceName,$coordinatorType);
                                            if(@in_array($p,$surgeonHighlightArr)) { $color="#D7E4EA";}
                                            
                                            $calHTML .= "<TD id='mon_".$p."' class='alignLeft' style='border:solid 1px; border-color:#9FBFCC; width:50px; background-color:".$color.";' onMouseOver='javascript:iOLink_swap_cal_color(this.id,\"Yes\",".$p.");'  onMouseOut='javascript:iOLink_swap_cal_color(this.id,\"No\",".$p.",\"\",\"$color\");' >
                                                            ".getFirstSurgeryTime($selDos,$reqUserId,'makeDivYes',$practiceName,$coordinatorType).
                                                            "<table class='table_collapse alignLeft' style='border:none;'>
                                                                <tr>
                                                                    <td class='text_10 alignLeft' style='font-size:9px; width:10%; cursor:pointer; ' onMouseOver='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$p."\"))+50,parseInt(findPos_Y(\"mon_".$p."\"))+30,\"iOLinkSurgeryTimeId".$p."\",\"Yes\");' onMouseOut='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$p."\")),parseInt(findPos_Y(\"mon_".$p."\")),\"iOLinkSurgeryTimeId".$p."\",\"No\");'>".getFirstSurgeryTime($selDos,$reqUserId,'',$practiceName,$coordinatorType)."</td>
                                                                    <td class='text_10 valignTop alignRight' style='width:90%;' ><a class='link_home text_10' style='color:$txtColor;' href='javascript:void(0);' onClick='javascript:document.getElementById(\"hiddSelectedDayId\").value=".$p.";iOLink_swap_cal_color(\"mon_$p\",\"Yes\",".$p.",document.getElementById(\"hiddSelectedPrevDayId\").value,\"$color\");schClick(\"$year_now\",\"$selected_month_number\",\"$p\",\"$reqUserId\");'>$p</a></td>
                                                                </tr>
                                                            </table>		
                                            
                                                        </td>";
                                            $intLastDisplay = $p;
                                            if($j%7==0){
                                                $rowCount ++;
                                                $calHTML .= "</Tr><tr class='alignRight valignTop' style='height:65px;background-color:#FFFFFF;' >";
                                            }
                                        }
                                    }
                                    $j++;
                                }
                                $totalBlocks = $emptyBlocks + $lastday;
                                $totalRows = ceil($totalBlocks / 7);
                                unset($surgeonHighlightArr);
                                if($totalRows > 5){
                                    $r = 1;					
                                    while($lastday > $intLastDisplay){							
                                        $color="";
                                        if($lastday==date("d")){
                                            //$color="#FBD78D";
                                            $txtColor="#FF0000";
                                        }else{
                                            
                                            $txtColor="";
                                        }
                                        $intLastDisplay++;
                                        $selDosLastDays=$year_now.'-'.$selected_month_number.'-'.$intLastDisplay;
                                        
                                        $surgeonHighlightArr =  getFirstSurgeryColor($selDosLastDays,$reqUserId,'makeDivYes',$practiceName,$coordinatorType);
                                        if(@in_array($p,$surgeonHighlightArr)) { $color="#D7E4EA";}
                                        
                                        $calHTML = str_replace(">{".$r."}",
                                                    "id='mon_".$intLastDisplay."' style='background-color:".$color.";' onMouseOver='javascript:iOLink_swap_cal_color(this.id,\"Yes\",".$p.");'  onMouseOut='javascript:iOLink_swap_cal_color(this.id,\"No\",".$p.",\"\",\"$color\");'   >
                                                    ".getFirstSurgeryTime($selDosLastDays,$reqUserId,'makeDivYes',$practiceName,$coordinatorType).
                                                    "<table class='table_collapse alignLeft' style='border:none;'>
                                                        <tr>
                                                            <td class='text_10 alignLeft' style='font-size:9px; width:10%; cursor:pointer; ' onMouseOver='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$intLastDisplay."\"))+40,parseInt(findPos_Y(\"mon_".$intLastDisplay."\"))+30,\"iOLinkSurgeryTimeId".$intLastDisplay."\",\"Yes\");' onMouseOut='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$intLastDisplay."\")),parseInt(findPos_Y(\"mon_".$intLastDisplay."\")),\"iOLinkSurgeryTimeId".$intLastDisplay."\",\"No\");'>".getFirstSurgeryTime($selDosLastDays,$reqUserId,'',$practiceName,$coordinatorType)."</td>
                                                            <td class='text_10 valignTop alignRight' style='width:90%;'><a class='link_home text_10' style='color:$txtColor;' href='javascript:void(0);' onClick='javascript:document.getElementById(\"hiddSelectedDayId\").value=".$p.";iOLink_swap_cal_color(\"mon_$p\",\"Yes\",".$p.",document.getElementById(\"hiddSelectedPrevDayId\").value,\"$color\");schClick(\"$year_now\",\"$selected_month_number\",\"$intLastDisplay\",\"$reqUserId\");'>".$intLastDisplay."</a></td>
                                                        </tr>
                                                    </table>",
                                                    $calHTML);
                                        $r++;
                                    }
                                }
                                $calHTML = preg_replace("/{[0-9]}/","",$calHTML);
                                echo $calHTML;
                            
                            ?>
                        </table>
                    </td>
                </tr>
            </table>
        </td>	
	</tr>
</table>	
</form>
<script>
var curDy = document.getElementById('hiddSelectedDayId').value;	
if(document.getElementById('mon_'+curDy)) {
	document.getElementById('hiddSelectedPrevDayColor').value=document.getElementById('mon_'+curDy).style.backgroundColor;
}
</script>
<?php
//if(count($getSurgeosDetails)==1) {
	?>
	<script>
	iOLink_change_month('<?php echo $selected_month_number;?>','<?php echo $year_now;?>',document.getElementById('surgeon_name_id').value);
	</script>
	<?php
//}
?>
</body>
</html>
