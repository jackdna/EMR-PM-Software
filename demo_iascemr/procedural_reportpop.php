<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
set_time_limit(900);
session_start();
if($_REQUEST['hidd_report_format']!='csv') {
	echo '<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">Please wait while data is retrieving from the server.</td>
			</tr>
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><img src="images/pdf_load_img.gif"></td> 
			</tr>
		</table>';
}

include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include("admin/classObjectFunction.php");
$objManageData = new manageData;
$get_http_path=$_REQUEST['get_http_path'];
$procedure1=$_REQUEST['procedure'];
$frm_date=$_REQUEST['startdate'];
$to_date=$_REQUEST['enddate'];
$showAllApptStatus=$_REQUEST['showAllApptStatus'];
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
$showAllApptStatusQry = "";
if($showAllApptStatus=="Active") {
	$showAllApptStatusQry = " AND  stub_tbl.patient_status != 'Canceled' AND  stub_tbl.patient_status != 'No Show' AND  stub_tbl.patient_status != 'Aborted Surgery'";	
}else if($showAllApptStatus=="Canceled") {
	$showAllApptStatusQry = " AND  stub_tbl.patient_status='Canceled'";	
}else if($showAllApptStatus=="No Show") {
	$showAllApptStatusQry = " AND  stub_tbl.patient_status='No Show'";	
}else if($showAllApptStatus=="Aborted Surgery") {
	$showAllApptStatusQry = " AND  stub_tbl.patient_status='Aborted Surgery'";	
}

//get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripcslashes($dataFac->fac_name);
$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);


  //set surgerycenter detail
			
	$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
	$SurgeryRes= imw_query($SurgeryQry);
	while($SurgeryRecord=imw_fetch_array($SurgeryRes))
	{
	//$name= stripslashes($SurgeryRecord['name']);
	//$address= stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
	$img = $SurgeryRecord['logoName'];
	$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
	}
        //$file=@fopen('html2pdf/white.jpg','w+');
		//@fputs($file,$surgeryCenterLogo);
		$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
		imagejpeg($bakImgResource,'new_html2pdf/white.jpg');
		
		$size=getimagesize('new_html2pdf/white.jpg');
	    $hig=$size[1];
	    $wid=$size[0];
$filename='html2pdf/white.jpg';
 function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
{ 

if(file_exists($fileName))
{ 
$img_size=getimagesize('new_html2pdf/white.jpg');
 $width=$img_size[0];
 $height=$img_size[1];
 $filename;
do
{
if($width > $targetWidth)
{
 $width=$targetWidth;
 $percent=$img_size[0]/$width;
 $height=$img_size[1]/$percent; 
}
if($height > $targetHeight)
{
$height=$targetHeight;
$percent=$img_size[1]/$height;
 $width=$img_size[0]/$percent; 
}

}while($width > $targetWidth || $height > $targetHeight);

$returnArr[] = "<img src='white.jpg' width='$width' height='$height'>";
$returnArr[] = $width;
$returnArr[] = $height;
return $returnArr; 
} 
return "";
}				
// end set surgerycenter detail 		
// end set surgerycenter detail  

   if($frm_date!=0)
   {
       $fm_date=explode("-",$frm_date);
	   $fm_date[0];
	   $fm_date[1];
	   $fm_date[2];
	   $from_date=$fm_date[2]."-".$fm_date[0]."-".$fm_date[1];
	   //echo $from_date;
   }
   if($to_date!=0)
   {
      $t_date=explode("-",$to_date);
	  $t_date[0];
	  $t_date[1];
	  $t_date[2];
	  $to_date1=$t_date[2]."-".$t_date[0]."-".$t_date[1];
	// echo $to_date1;
    }
	$current_date=date('m-d-Y');
//echo	"select * from procedures where procedureId='$procedure1'";
 $procedure_tbl=imw_query("select * from procedures where name='$procedure1' and del_status='' ");
	$proc=imw_fetch_array($procedure_tbl);
	 $procedure_id=$proc['procedureId'];
	 $procedure_name=$proc['name'];	
	//echo $prd_report="select * from patientconfirmation where dos between '$from_date' AND '$to_date1'";
	if( defined('STRING_SEARCH') && constant('STRING_SEARCH')=='YES')
	{
		$searchStr="(patientconfirmation.patient_primary_procedure='$procedure_name'
		|| patientconfirmation.patient_secondary_procedure='$procedure_name')";	 
	}else{
		$searchStr="(patientconfirmation.patient_primary_procedure_id='$procedure_id'
		|| patientconfirmation.patient_secondary_procedure_id='$procedure_id')";	 
	}
		
	 if($procedure1=="All" && $from_date=="" && $to_date=="")
	{  
	  $prd_report="SELECT patientconfirmation. * , 
	  patient_data_tbl.patient_fname, 
	  patient_data_tbl.patient_mname, 
	  patient_data_tbl.patient_lname, 
	  preopnursingrecord.preopnursingSaveDateTime,
	  stub_tbl.patient_status,
	  dischargesummarysheet.summarySaveDateTime
      FROM patientconfirmation
	  left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
      LEFT JOIN preopnursingrecord ON patientconfirmation.patientConfirmationId = preopnursingrecord.confirmation_id
      LEFT JOIN dischargesummarysheet ON patientconfirmation.patientConfirmationId= dischargesummarysheet.confirmation_id 
	  LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=dischargesummarysheet.confirmation_id
	  where stub_tbl.patient_confirmation_id !=0 $fac_con ".$showAllApptStatusQry;
	}elseif($procedure1=="All" && $from_date<>"" && $to_date<>"")
	{
	 
           $prd_report="select patientconfirmation.*,
		   patient_data_tbl.patient_fname,
		   patient_data_tbl.patient_mname,
		   patient_data_tbl.patient_lname,
		   preopnursingrecord.preopnursingSaveDateTime,
		   stub_tbl.patient_status,
		   dischargesummarysheet.summarySaveDateTime from patientconfirmation 
		   left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
		   left join preopnursingrecord on patientconfirmation.patientConfirmationId=preopnursingrecord.confirmation_id
		   left join dischargesummarysheet on patientconfirmation.patientConfirmationId= dischargesummarysheet.confirmation_id
		   LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=dischargesummarysheet.confirmation_id
		   where patientconfirmation.dos between '$from_date' AND '$to_date1'
		   AND stub_tbl.patient_confirmation_id !=0
			$fac_con ".$showAllApptStatusQry;
    }
	 elseif($procedure1=="All" && $from_date<>"" && $to_date=="")
	{ 
		 $prd_report="select patientconfirmation.*,
		   patient_data_tbl.patient_fname,
		   patient_data_tbl.patient_mname,
		   patient_data_tbl.patient_lname,
		   preopnursingrecord.preopnursingSaveDateTime,
		   stub_tbl.patient_status,
		   dischargesummarysheet.summarySaveDateTime from patientconfirmation 
		   left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
		   left join preopnursingrecord on patientconfirmation.patientConfirmationId=preopnursingrecord.confirmation_id 
		   left join dischargesummarysheet on patientconfirmation.patientConfirmationId=dischargesummarysheet.confirmation_id
		   LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=dischargesummarysheet.confirmation_id
		   where patientconfirmation.dos>='$from_date'
		   AND stub_tbl.patient_confirmation_id !=0
			$fac_con ".$showAllApptStatusQry;
	}
	 elseif($procedure1==$procedure_name &&  $procedure1<>'' && $from_date<>"" && $to_date<>"")
     {
		
	  $prd_report="select patientconfirmation.*,
		   patient_data_tbl.patient_fname,
		   patient_data_tbl.patient_mname,
		   patient_data_tbl.patient_lname,
		   preopnursingrecord.preopnursingSaveDateTime,
		   stub_tbl.patient_status,
		   dischargesummarysheet.summarySaveDateTime from patientconfirmation 
		  left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
		   left join preopnursingrecord on patientconfirmation.patientConfirmationId=preopnursingrecord.confirmation_id
		   left join dischargesummarysheet on patientconfirmation.patientConfirmationId= dischargesummarysheet.confirmation_id
		   LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=dischargesummarysheet.confirmation_id
	       where $searchStr AND patientconfirmation.dos between '$from_date' AND '$to_date1'
		   AND stub_tbl.patient_confirmation_id !=0
			$fac_con ".$showAllApptStatusQry;
    }
	elseif($procedure1==$procedure_name && $procedure1<>'' && $from_date<>"" && $to_date=="")
	{ 
	    $prd_report="select patientconfirmation.*,
		   patient_data_tbl.patient_fname,
		   patient_data_tbl.patient_mname,
		   patient_data_tbl.patient_lname,
		   preopnursingrecord.preopnursingSaveDateTime,
		   stub_tbl.patient_status,
		   dischargesummarysheet.summarySaveDateTime from patientconfirmation 
		   left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id 
		   left join preopnursingrecord on patientconfirmation.patientConfirmationId=preopnursingrecord.confirmation_id 
		   left join dischargesummarysheet on patientconfirmation.patientConfirmationId= dischargesummarysheet.confirmation_id
		   LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=dischargesummarysheet.confirmation_id
	       where $searchStr AND patientconfirmation.dos>='$from_date'
		   AND stub_tbl.patient_confirmation_id !=0
			$fac_con ".$showAllApptStatusQry;
	}
	
   elseif($procedure1==$procedure_name && $procedure1<>"" && $from_date=="" && $to_date=="")
	{  
	    $prd_report="select patientconfirmation.*,
		   patient_data_tbl.patient_fname,
		   patient_data_tbl.patient_mname,
		   patient_data_tbl.patient_lname,
		   preopnursingrecord.preopnursingSaveDateTime,
		   stub_tbl.patient_status,
		   dischargesummarysheet.summarySaveDateTime from patientconfirmation 
		  left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
		   left join preopnursingrecord on patientconfirmation.patientConfirmationId=preopnursingrecord.confirmation_id
		   left join dischargesummarysheet on patientconfirmation.patientConfirmationId= dischargesummarysheet.confirmation_id
		   LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=dischargesummarysheet.confirmation_id
	       where $searchStr AND stub_tbl.patient_confirmation_id !=0
			$fac_con ".$showAllApptStatusQry;
	}
	else
	//Change on all queries
	// left join patient_data_tbl on patientconfirmation.ascId =patient_data_tbl.asc_id 
	     //Above line is replaced with left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id 			

	{
	
	   $msg="No record found!";
	}
	//echo $procedure1.'<br>'.$procedure_name;
	 $procedure_data=@imw_query($prd_report);
	   	
				 $rows=@imw_num_rows($procedure_data); 
				$current_date=date("m-d-Y");
				$csv_content = '';
				if(@imw_num_rows($procedure_data)>0) 
		     {
			    $csv_content .= 'Seq'.','.'"Name"'.','.'"ASC-id"'.','.'"Status"'.','.'"Surgeon"'.','.'"Procedure"'.','.'"Site"'.','.'"Date"'.','.'"From"'.','.'"To"'."\n";
				$a=1;
				while($report=imw_fetch_array($procedure_data))
				{
				   foreach($report as $key=>$val)
				  {
				    
				  $report1[$a][$key]=$val;
					
				  }    
			        $a++;
				}
				 $num=imw_num_rows($procedure_data);
			   $rowbreak=13;
			   $numb=ceil($num/$rowbreak);
			   $rowstart=1;
			   $rowend = 13;

			   for($rows=0;$rows<$numb;$rows++)
			   {
			      $tablerow='';
				  $csv_content_val = '';
            	    for($i=$rowstart;$i<$rowend;$i++)
		          {

			         $asc_id=$report1[$i]['ascId'];
					 $patient_id=$report1[$i]['patientId'];
					 $confirmation_id=$report1[$i]['patientConfirmationId'];
					 $surgeon_name=$report1[$i]['surgeon_name'];
					 $procedure_pname=$report1[$i]['patient_primary_procedure'];
					 $procedure_sname=$report1[$i]['patient_secondary_procedure'];
					 //FINDING PROCEUDRE NAME TO BE APPLIED
					
					
					 
					  if($procedure_sname && $procedure_sname<>"N/A")
					 
					 {
					  $procedure_name=$procedure_pname.","."<br>".$procedure_sname;
					 }
					 else
					 {
					   $procedure_name=$procedure_pname;
					 }
					 //END
					$dos=$report1[$i]['dos'];
					 //exploding date of surgery
					 $date=explode("-",$dos);
					 $date[0];
					 $date[1];
					 $date[2];
					  $date_of_surgery=$date[1]."-".$date[2]."-".$date[0];
					 //end of exploding
					 $site=$report1[$i]['site']; 
					 //APPLYING NUMBERS TO SITE
					 if($site==1)
					 {
					    $eyesite="Left eye";
					 }
					 elseif($site==2)
					 {
					   $eyesite="Right eye";
					 }
					 elseif($site==3)
					 {
					    $eyesite="Both eyes";
					 } //END
			//CODE FOR RETRIVING NAME OF PATIENT
				$patient_fname=$report1[$i]['patient_fname'];
				$patient_mname=$report1[$i]['patient_mname'];
				$patient_lname=$report1[$i]['patient_lname'];
				$patient_name=$patient_lname.", ".$patient_fname;
				$patient_status=$report1[$i]['patient_status'];
				$patient_status_new =$patient_status;
				if($patient_status == 'Canceled') {
					$patient_status_new = 'Cancelled';	
				}
			//END OF CODE
		    	//STRARTING TIME OF SURGERY
						
						if($report1[$i]['preopnursingSaveDateTime']=="0000-00-00 00:00:00" || $report1[$i]['preopnursingSaveDateTime']=="") {
						$start_time = "";	
					}else {
						$start_time= $objManageData->getTmFormat($report1[$i]['preopnursingSaveDateTime']);
					}
		  
			    /* $from_time=$report1[$i]['preopnursingSaveDateTime'];
				 $frm_time=explode(" ",$from_time);
				 $frm_time[0];
				 $frm_time[1];
						
				 $start_time1=explode(":",$frm_time[1]);
				if($start_time1[0]>=12){
				   $am_pm = "PM";
			     }else {
				           $am_pm = "AM";
			     }	      
				 if($start_time1[0]>=13){
				   $start_time1[0]=$start_time1[0]-12;
				   if(strlen($start_time1[0])==1){
				     $start_time1[0] = "0".$start_time1[0];
					}
					
				}
				if($start_time1[0]>=1)
				{
			     $start_time=$start_time1[0].":".$start_time1[1]." ".$am_pm;
				 }
				 else
				 {
				  $start_time='';
				 }*/
				 //END OF CODE FOR STARTING TIME
		       //ENDING TIME OF SURGERY
						
						if($report1[$i]['summarySaveDateTime']=="0000-00-00 00:00:00" || $report1[$i]['summarySaveDateTime']==""){
							$end_time = "";
						}else{
							$end_time = $objManageData->getTmFormat($report1[$i]['summarySaveDateTime']);
						}
				/*to_time=$report1[$i]['summarySaveDateTime'];
				$totime=explode(" ",$to_time);
				$totime[0];
				$totime[1];
				 $end_time1=explode(":",$totime[1]);
				if($end_time1[0]>=12){
				   $am_pm = "PM";
			     }else {
				           $am_pm = "AM";
			     }	      
				 if($end_time1[0]>=13){
				   $end_time1[0]=$end_time1[0]-12;
				   if(strlen($end_time1[0])==1){
				     $end_time1[0] = "0".$end_time1[0];
					}
					
				}
				if($end_time1[0]>=1)
				{
				$end_time=$end_time1[0].":".$end_time1[1]."".$am_pm;
				}
				else
				{
				  $end_time='';
				}*/
			//END OF CODE
			//SET SPACE FOR SEQ & NAME
				if($i<10)
				{
				 $nbsp="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				elseif($i>=10 && $i<100)
				{
				 $nbsp="&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				elseif($i>100  && $i<1000)
				{
				 $nbsp=" ";
				}	
			//END SET SPACE FOR SEQ & NAME	
				 
				 if($i<=$num)
			{	
				  $j=$i+1;
			  // bgcolor="#F1F4F0"
			 	$borderBottomFirstRow = $borderBottomSecondRow = 'bottomBorder';
				$tablerow.='
					<tr valign="top">
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="20">'.$i.'</td>
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="100">'.$patient_name.'</td>
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="50">'.$asc_id.'</td>
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="80">'.$patient_status_new.'</td>
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="80">'.$surgeon_name.'</td>
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="100">'.$procedure_name.'</td>
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="65">'.$eyesite.'</td>
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="85">'.$date_of_surgery.'</td>
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="75">'.$start_time.'</td>
						<td class="'.$borderBottomSecondRow.' text_15" align="left" width="75">'.$end_time.'</td>
						
					</tr>
				';
				//START CSV CODE
				$seq_csv 				=	'"'.trim($i).'"';
				$patient_name_csv 		=	'"'.trim($patient_name).'"';
				$asc_id_csv 			=	'"'.trim($asc_id).'"';
				$patient_status_new_csv	=	'"'.trim($patient_status_new).'"';
				$surgeon_name_csv 		=	'"'.trim($surgeon_name).'"';
				$procedure_name_csv 	= 	'"'.trim(str_ireplace("<br>","  ",$procedure_name)).'"';
				$eyesite_csv			=	'"'.trim($eyesite).'"';
				$date_of_surgery_csv	=	'"'.trim($date_of_surgery).'"';
				$start_time_csv			=	'"'.trim($start_time).'"';
				$end_time_csv			=	'"'.trim($end_time).'"';
				
				$csv_content_val 		.= $seq_csv.','.$patient_name_csv.','.$asc_id_csv.','.$patient_status_new_csv.','.$surgeon_name_csv.','.$procedure_name_csv.','.$eyesite_csv.','.$date_of_surgery_csv.','.$start_time_csv.','.$end_time_csv."\n";		
				//END CSV CODE
				
				}//ending of if statement
				
		      }//ending of inner for loop			
		  $rowstart = $rowend;
	      $rowend=$rowend + 13;
	$img_logo = showThumbImages('html2pdf/white.jpg',170,50);
	$imgheight= $img_logo[2]+8;
    $imgwidth= $img_logo[1]+8;		  

$table.='
		<style>
			.tb_heading{
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000000;
				background-color:#FE8944;
			}
			.text_b{
				font-size:14px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000000;
			}
			.text_16b{
				font-size:16px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000000;
			}
			.text_14b{
				font-size:14px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000000;
			}
			.text{
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
			}
			.orangeFace{
				color:#FE8944;
			}
			.text_15 {
				font-size:15px;
				font-family:Arial, Helvetica, sans-serif;
				
			}
			.text_18 {
				font-size:18px;
				font-family:Arial, Helvetica, sans-serif;
				
			}
			.bottomBorder {
				border-bottom-style:solid; border-bottom:2px;padding-top:4px;padding-bottom:4px;
				font-family:Arial, Helvetica, sans-serif;
			}
			.lightBlue {
				border-bottom-style:solid; border-bottom:2px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#EAF4FD;
			}
			.midBlue {
				font-family:Arial, Helvetica, sans-serif;
				background-color:#80AFEF;
			}
			.text_orangeb{
				font-weight:bold;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
				color:#CB6B43;
			}
			.lightGreen {
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#ECF1EA;
			}
			.lightorange {
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#CB6B43;
			}
			.midorange {
				font-size:18px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FE8944;
			}
			
		</style>
		<page backtop="37mm" backbottom="15mm">			
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>';

$frm_date_csv 	= '';
$to_date_csv 	= '';
if($frm_date!='' && $to_date!=''){
	$frm_date_csv 	= '"From '.trim($frm_date).'"';
	$to_date_csv 	= '"To '.trim($to_date).'"';
}
$table.='
		<page_header>
			
		
		<table width="700" border="0" cellpadding="0" cellspacing="0" >
			<tr height="'.$higinc.'" >
				<td  class="text_14b" width="600" style="background-color:#CD523F; padding-left:5px; color:white "  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b>
				 </td>
				<td style="background-color:#CD523F; "  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'&nbsp;</td>
			</tr>		
			<tr height="22" bgcolor="#F1F4F0">
				<td align="left" colspan="2" class="text_14b">
					Procedural Report&nbsp;&nbsp;&nbsp;&nbsp;From&nbsp;'.$frm_date.'&nbsp;&nbsp;&nbsp;&nbsp;To&nbsp;'.$to_date.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Report Date'.$current_date.'
				</td>
				
			</tr>
			<tr >
				<td colspan="2">&nbsp;</td>
			</tr>				
		</table>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">	
			<tr  valign="top">
			<td align="left" class="text_b" width="20">Seq</td>
			<td align="left" class="text_b" width="100">Name</td>
			<td align="left" class="text_b" width="50">ASC-id</td>
			<td align="left" class="text_b" width="80">Status</td>
			<td align="left" class="text_b" width="80">Surgeon</td>
			<td align="left" class="text_b" width="100">Procedure</td>
			<td align="left" class="text_b" width="65">Site</td>
			<td align="left" class="text_b" width="85">Date</td>
			<td align="left" class="text_b" width="75">From</td>
			<td align="left" class="text_b" width="75">To</td>
		</tr>
	   </table>
	 </page_header>
	 <table width="100%" border="0" cellpadding="0" cellspacing="0">';
		
$table.= $tablerow.'
			</table></page>';
			
			$csv_content1 = $name.','."".','."".','."".','."".','."".','."".',Procedural Report '.$current_date;
			$csv_content2 = $address.','."".','.$frm_date_csv.','."".','.$to_date_csv.','."".','."".','."";
			$csv_content .= $csv_content_val."\n";		
			
			if($num>=$rowend)
			{
			//$table.='<newpage>';
			 }
			 
				}//end of outer loop		 
			}//end of if condition
			else
			{
			  $msg="No Record Found!";
			
			$img_logo = showThumbImages('html2pdf/white.jpg',170,50);
			$imgheight= $img_logo[2]+8;
        $imgwidth= $img_logo[1]+8;	
			$table.='
	 <table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr><td colspan=9 height="150"></td></tr>
		<tr>
			<td align="center" colspan=9>'.$msg.'</td>
		</tr>
	</table>';
}

if($_REQUEST['hidd_report_format']=='csv' && imw_num_rows($procedure_data)>0) {
	$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/procedural_reportpop.csv';
	if(file_exists($file_name)) {
		@unlink($file_name);
	}
	$fpH1 = fopen($file_name,'w');
	fwrite($fpH1, $csv_content1."\n");
	fwrite($fpH1, $csv_content2."\n\r");
	fwrite($fpH1, $csv_content."\n\r");
	$objManageData->download_file($file_name);
	fclose($fpH1);
	exit;
} else {
	$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
	$intBytes = fputs($fileOpen,$table);
	fclose($fileOpen);
}

?>	
<!DOCTYPE html>
<html>
<head>
<title>Discharge Summary Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   

<body>
<script language="javascript">
	window.focus();
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>

<form name="printFrm" action="new_html2pdf/createPdf.php?op=p" method="post">

</form>

<?php 
if($_REQUEST['hidd_report_format']!='csv' && imw_num_rows($procedure_data)>0) {?>
	<script type="text/javascript">
        submitfn();
    </script>
<?php 
}else {
?>
	<script>
		if(document.getElementById("loader_tbl")) {
			document.getElementById("loader_tbl").style.display = "none";	
		}
	</script>	

<?php	
	if($_REQUEST['hidd_report_format']=='csv') {
	?>
		<script type="text/javascript">
			location.href = "procedural_report.php?no_record=yes&showAllApptStatus=<?php echo $_REQUEST['showAllApptStatus'];?>&procedure=<?php echo $_REQUEST['procedure'];?>&startdate=<?php echo $_REQUEST['startdate'];?>&enddate=<?php echo $_REQUEST['enddate'];?>";
        </script>
    <?php
	}
	?>

<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#EAF0F7; width:100%; height:100%;">
	<tr>
		<td class="alignCenter valignTop" style="width:100%;"><b>No Record Found</b></td> 
	</tr>
</table>
<?php		
}?>	
</body>
</html>