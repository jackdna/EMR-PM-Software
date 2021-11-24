<?php
$sch_tmp_id = $id;
$del_status_child_check=" and del_status=''";
if($archive==1)$del_status_child_check="";

$strQryChild = "select * from schedule_templates WHERE template_type != 'SYSTEM' and parent_id = '".$sch_tmp_id."' $del_status_child_check order by schedule_name";
$rsDataChild = imw_query($strQryChild);
if($rsDataChild){
    $intCnt = imw_num_rows($rsDataChild);
    if($intCnt > 0){
        $child = 0;
        while($arrRowChild = imw_fetch_array($rsDataChild,imw_ASSOC)){
            $id = $arrRowChild['id'];
            $schedule_name = $arrRowChild['schedule_name'];
            $label = $arrRowChild['label'];
            $mor_start_time = explode(":",$arrRowChild['morning_start_time']);
            $mor_end_time = explode(":",$arrRowChild['morning_end_time']);
            $availability = $arrRowChild['availability'];
            $date_status = explode(",",$arrRowChild['date_status']);
			$del_child_status=$arrRowChild['del_status'];
			
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
            if($arrRowChild['morning_start_time'] != "00:00:00" && $arrRowChild['morning_end_time'] != "00:00:00"){
                $display = $start_time.'-'.$end_time; 
            }
            
            $display2 = "NA";
            if($arrRowChild['fldLunchStTm'] != "00:00:00" && $arrRowChild['fldLunchEdTm'] != "00:00:00"){
                $lunch_start_time = explode(":",$arrRowChild['fldLunchStTm']);
                $lunch_end_time = explode(":",$arrRowChild['fldLunchEdTm']);
                
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
			$display3=$arrRowChild['MinAppointments'].'/'.$arrRowChild['MaxAppointments'];

            if($child % 2 == 0) { $bgClr = 'bgCl3'; } else{ $bgClr = 'bgCl4';}
			
			$a_open_child="<a  href=\"javascript:edit('".$id."','inside','".$sch_tmp_id."');\">";
			$a_close_child="</a>";
			$del_class_child="";
			if($del_status || $del_child_status)//check parent del status
			{
				$a_open_child=$a_close_child="";
				$del_class_child="class=\"text-danger\"";
			}
			
            $data .= "<tr class='sch_tmp_child_row ".$bgClr."'><td>&nbsp;</td>";
			$data .= "<td $del_class_child>".$a_open_child.htmlentities(stripslashes($schedule_name)).$a_close_child."</td>";
			$data .= "<td $del_class_child>".$a_open_child.$display.$a_close_child."</td>";
			$data .= "<td $del_class_child>".$a_open_child.$display2.$a_close_child."</td>";
			$data .= "<td $del_class_child>".$a_open_child.$display3.$a_close_child."</td>";
			$data .= "<td $del_class_child>";
			if(!$del_status)//check parent del status
			{	
				if(!$del_child_status)
				{
					$data .= "<div class='row'>
						<div class='col-sm-2 text-center'>
							<a class=\"text_ab\" href=\"javascript:edit('".$id."','inside','".$sch_tmp_id."');\" title=\"Edit Template\"><span class=\"glyphicon glyphicon-pencil\" style=\"font-size:20px\"></span></a>
						</div>	
						<div class='col-sm-2'>
							<a  href=\"javascript:void(0);\" onClick=\"confirm_child_del('".$id."');\" title=\"Move to Archive\"><span class=\"glyphicon glyphicon-log-in text-red\" style=\"font-size:22px\"></span></a>
						</div>	
					</div>";
				}else{
					list($child_user_id, $child_dateTime, $child_delReason)=explode("::",$del_child_status)	;
					
					list($date,$time, $am)=explode(' ',$child_dateTime);
					list($year,$month, $day)=explode('-',$date);
					list($hour,$min)=explode(':',$time);
					$childTimeStamp=date($phpDateFormat . ' h:i A', mktime($hour,$min,0,$month,$day,$year) );
					
					$data .= "<div class='row'>
					<div class='col-sm-2 text-center'>
					<a  href=\"javascript:void(0);\" onClick=\"confirm_restore('".$id."');\" title=\"Restore Template\"><span class=\"glyphicon glyphicon-log-out text-green\" style=\"font-size:22px\"></span></a>
					</div>
					<div class='col-sm-10 text-left' title='Template Archived On'>$childTimeStamp
					</div>
					</div>";
				}
			}
			$data .= "</td></tr>";
			$child++;
        }
    } 
}
?>