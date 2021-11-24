<?php
require_once("../../../../config/globals.php");
$phpDateFormat = phpDateFormat();
//loading schedule templates
$rsData = '';
$data = '<table class="table table-bordered table-condensed">';
$childstrQryReq = "select distinct parent_id from schedule_templates WHERE template_type != 'SYSTEM' and parent_id != 0  order by schedule_name";
$childstrQryReqData = imw_query($childstrQryReq);
$childTempArrCnt = array();
while($childTempRow=imw_fetch_assoc($childstrQryReqData))
{
	$childTempArrCnt[] = $childTempRow['parent_id'];
}
$del_status_check=" and del_status=''";
if($archive==1)$del_status_check="";

$strQry = "select * from schedule_templates WHERE template_type != 'SYSTEM' and parent_id = 0 $del_status_check order by schedule_name";
$rsData = imw_query($strQry);
if($rsData){
    $intCnt = imw_num_rows($rsData);
    if($intCnt > 0){
        $j = 0;
        while($arrRow = imw_fetch_array($rsData,imw_ASSOC)){
            $id = $arrRow['id'];
            $schedule_name = $arrRow['schedule_name'];
            $label = $arrRow['label'];
            $mor_start_time = explode(":",$arrRow['morning_start_time']);
            $mor_end_time = explode(":",$arrRow['morning_end_time']);
            $availability = $arrRow['availability'];
            $date_status = explode(",",$arrRow['date_status']);
			$del_status=$arrRow['del_status'];
			
            if($mor_start_time[0]>=12 )    {
                $daytime1=date("h",mktime($mor_start_time[0]));
                $time1=" PM";
            }else if($mor_start_time[0]<12 || $mor_start_time[0]==0)    {
                $daytime1=date("h",mktime($mor_start_time[0]));
                $time1=" AM";
            }
             
            $start_time=$daytime1.":".$mor_start_time[1].$time1;
             
            if($mor_end_time[0]>=12 )    {
                $daytime2=date("h",mktime($mor_end_time[0]));
                $time2=" PM";
            }else if($mor_end_time[0]<12 || $mor_end_time[0]==0)    {
                $daytime2=date("h",mktime($mor_end_time[0]));
                $time2=" AM";
            }
            
            $end_time=$daytime2.":".$mor_end_time[1].$time2;
            
            $display = "";
            if($arrRow['morning_start_time'] != "00:00:00" && $arrRow['morning_end_time'] != "00:00:00"){
                $display = $start_time.'-'.$end_time; 
            }
            
            $display2 = "NA";
            if($arrRow['fldLunchStTm'] != "00:00:00" && $arrRow['fldLunchEdTm'] != "00:00:00"){
                $lunch_start_time = explode(":",$arrRow['fldLunchStTm']);
                $lunch_end_time = explode(":",$arrRow['fldLunchEdTm']);
                
                if($lunch_start_time[0]>=12 ){
                    $daytime3=date("h",mktime($lunch_start_time[0]));
                    $time3=" PM";
                }else if($lunch_start_time[0]<12 || $lunch_start_time[0]==0){
                    $daytime3=date("h",mktime($lunch_start_time[0]));
                    $time3=" AM";
                }

                $start_lunch_time=$daytime3.":".$lunch_start_time[1].$time3;

                if($lunch_end_time[0]>=12 ){
                    $daytime4=date("h",mktime($lunch_end_time[0]));
                    $time4=" PM";
                }else if($lunch_end_time[0]<12 || $lunch_end_time[0]==0){
                    $daytime4=date("h",mktime($lunch_end_time[0]));
                    $time4=" AM";
                }
                    
                $end_lunch_time=$daytime4.":".$lunch_end_time[1].$time4;
            
                $display2 = $start_lunch_time.'-'.$end_lunch_time; 
				
            }
			$display3=$arrRow['MinAppointments'].'/'.$arrRow['MaxAppointments'];

            if($j % 2 == 0) { $bgClr = 'alt3'; } else{ $bgClr = '';}
			if(in_array($arrRow['id'],$childTempArrCnt)){$cExp_dis = '[+]'; $chClick = "show_child_temp('".$id."',this);";}else{$cExp_dis = '[-]&nbsp;';$chClick = "";}
			$a_open="<a href=\"javascript:edit('".$id."','inside');\">";
			$a_close="</a>";
			$del_class="";
			if($del_status){
				$a_open=$a_close="";
				$del_class="class=\"text-danger\"";
			}
			
			
            $data .= "<tr class=".$bgClr.">";
			$data .= "<td class='text-center'>";
			if($del_status=='')
			{
				$data .= "<div class=\"checkbox\"><input type=\"checkbox\" name=\"id\" class=\"chk_sel\" id=\"chk_sel_".$id."\" value=\"".$id."\"><label for=\"chk_sel_".$id."\"></label></div>";
			}
			$data .= "</td>
			<td $del_class>".$a_open.htmlentities(stripslashes($schedule_name)).$a_close."</td>
			<td $del_class>".$a_open.$display.$a_close."</td>
			<td $del_class>".$a_open.$display2.$a_close."</td>
			<td $del_class>".$a_open.$display3.$a_close."</td>
			<td $del_class>";
			if($del_status=='')
			{	
				$data .= "<a  title=\"Add Child Template\" href=\"javascript:void(0);\" onClick=\"hide_child_temp(this);open_child_temp('".$id."')\" style=\"font-size:17px;font-weight:bold;\"><img src=\"../../../../library/images/child_template.png\" title=\"Add Child Template\"> </a>";
			}else
			{
				list($user_id, $dateTime, $delReason)=explode("::",$del_status);
				list($date,$time, $am)=explode(' ',$dateTime);
				list($year,$month, $day)=explode('-',$date);
				list($hour,$minu)=explode(':',$time);
				$timeStamp=date($phpDateFormat . ' h:i A', mktime($hour,$minu,0,$month,$day,$year));
				$data .=  "
					<div class='row'>
					<div class='col-sm-2 text-center'>
					<a  href=\"javascript:void(0);\" onClick=\"confirm_restore('".$id."');\" title=\"Restore Template\"><span class=\"glyphicon glyphicon-log-out text-green\" style=\"font-size:22px\"></span></a>
					</div>
					<div class='col-sm-10 text-left' title='Archived On'>
						$timeStamp
					</div>
					</div>";
			}
			$data .= "</td></tr>";
			//add child template records
			include("load_child.php");

            $j++;
        }
    } else{
        $data .= "<tr><td class=\"failureMsg\" colspan=6 style=\"text-align: center\">".imw_msg('no_rec')." <a class=\"text_12b_purple\" href='javascript:edit(\"\",\"inside\");'>Click here</a> to add New Schedule Template.</td></tr>";
    }
}
$data .= "</table><textarea id=\"hidd_reason_text\" style=\"display:none;\"></textarea>";
echo $data;
?>