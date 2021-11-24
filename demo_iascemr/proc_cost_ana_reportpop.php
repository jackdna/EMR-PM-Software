<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
set_time_limit(900);

include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include_once("admin/classObjectFunction.php");
$objManageData = new manageData; 

// query part to exclude surgeons in vcna reports
$usersExclude =	'';
$surgeonExclude = '';
if( defined('VCNA_SURGEON_EXCLUDE') && constant('VCNA_SURGEON_EXCLUDE') )
{
	$usersExclude = 'And usersId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').') ';	
	$surgeonExclude = 'And pc.surgeonId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').')';	
}

//get detail for logged in facility
	$queryFac	=	imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
	$dataFac	=	imw_fetch_object($queryFac);
	$name			=	stripcslashes($dataFac->fac_name);
	$address	=	stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);
	
//create array for procedure acro
$proc_q=imw_query("select procedureAlias, procedureId, name from procedures");
while($proc_d=imw_fetch_object($proc_q))
{
	$proc_alias[$proc_d->procedureId]=($proc_d->procedureAlias)?$proc_d->procedureAlias:$proc_d->name;
}

//create array for surgeon name
$physician	=	imw_query("select * from users where user_type='Surgeon' ".$usersExclude." and deleteStatus!='Yes' order by lname");
while( $physician1=imw_fetch_assoc($physician))
{
		$physician_id	=	$physician1['usersId'];
		$physician_fname=	$physician1['fname'];
		$physician_mname=	$physician1['mname'];
		$physician_lname=	$physician1['lname'];
		$physician_name	=	stripslashes($physician_lname.",".$physician_fname." ".$physician_mname);
		$physiciansArr[$physician_id]=$physician_name;
}
//$fac_qry	=	" and st.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
//$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' ); 
$superBillProcIdArr = $objManageData->superBillProcIdArrFun();	
if($_REQUEST['proc_save']=='yes') {
	$date1 = trim($_REQUEST["date1"]);
	$date2 = trim($_REQUEST["date2"]);
	
	$from_date 	= $objManageData->changeDateYMD($date1);
	$to_date 	= $objManageData->changeDateYMD($date2);

	$tmpReqProcArr 	= array_values(array_unique(array_filter(explode(",",$_REQUEST["procedure"]))));
	$tmpReqProcName = '';
	if( is_array($tmpReqProcArr) and count($tmpReqProcArr) > 0 )
	{
		foreach($tmpReqProcArr as $v)
		{
			$tmpReqProcName .= ($tmpReqProcName ? ' & ' : '').$proc_alias[$v];
		}
	}
	
	$procedure_id 	= implode(",", $tmpReqProcArr);	
	$physicianImp = $_REQUEST["physician"];
	//$physicianImp = implode(",",$physicianArr);
	
	if(!$procedure_id) {  $procedure_id = '0';}
	if(!$physicianImp) {  $physicianImp = '0';} 
	else
	{
		if($physicianImp!='all'){
		$physicianQry=" AND pc.surgeonId IN ($physicianImp)";}
		
		if($physicianImp =='all'){
		$physicianQry = $surgeonExclude;}
		
	}
	
	$proc_JOIN = "";
	if( defined('STRING_SEARCH') && constant('STRING_SEARCH')=='YES')
	{	//$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.cost_procedure_name)"; 
	}else{
		//$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.cost_procedure_id)"; 
	}
	$procIdQry = "";
	if($procedure_id!='all') {
		if( defined('STRING_SEARCH') && constant('STRING_SEARCH')=='YES')
		{
			$procedure_tbl=imw_query("select procedureId, name from procedures where procedureId IN(".$procedure_id.") ");
			$procedure_name = array();
			while($proc=imw_fetch_array($procedure_tbl)){
				array_push($procedure_name,"'".$proc['name']."'");
			}
			$procNameImplode = implode(",",$procedure_name);
			//$procIdQry = " AND pc.cost_procedure_name IN(".$procNameImplode.") ";	
			//$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.cost_procedure_name)"; 
		}else{
			//$procIdQry = " AND pc.cost_procedure_id IN(".$procedure_id.") ";	 
			//$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.cost_procedure_id)"; 
		}
			
	}
	

	$qry = "SELECT pc.labor_cost, pc.supply_cost, pc.cost_procedure_id as proc_id, pc.cost_procedure_name as proc_name, 
								 pc.patientConfirmationId, CONCAT(pdt.patient_lname,', ',pdt.patient_fname,' ', pdt.patient_mname) as pname, 
								 pdt.patient_id, pc.dos,
								 users.usersId, users.fname, users.mname, users.lname 
					FROM patientconfirmation pc
					INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.patient_status!='Canceled')
					LEFT JOIN patient_data_tbl pdt ON(pc.patientId = pdt.patient_id)
					LEFT JOIN users ON(users.usersId = pc.surgeonId)
					$proc_JOIN
					WHERE (pc.dos BETWEEN '".$from_date."' AND '".$to_date."') ".$fac_con."
					AND pc.cost_procedure_id > 0
					AND pc.finalize_status='true' ".$physicianQry."
					/*AND (pc.supply_cost>0 || pc.labor_cost>0) */
					AND pc.surgeonId<>0
					ORDER BY users.usersId ASC";
	$res = imw_query($qry)or die(imw_error().' ----- ');
	//die($qry);
	$total_proc = array();//$tmpReqProcArr[]=18;
	//echo '<pre>';print_r($proc_alias);
	$tmpUsrIdArr = array();
	$pConfIdArr = array();
	$pDetail = array();
	if(imw_num_rows($res)>0) {
		//get total cost of procedure for each surgeon
		while($row = imw_fetch_assoc($res)) 
		{
			$recExist=false;
			$pConfId = $row["patientConfirmationId"];
			if(count(array_intersect($superBillProcIdArr[$pConfId], $tmpReqProcArr)) == count($tmpReqProcArr) && count($superBillProcIdArr[$pConfId]) == count($tmpReqProcArr)){
				$recExist = true;
				$pConfIdArr[] = $pConfId;
			}
			
			
			//echo '<br>'.$pConfId.'-----'.$row['proc_id'] .'---'. $row['fname'].($recExist ? '--TRUE--': '---FALSE---');
			if($row['fname'] && $recExist == true)
			{	
				$tmpUsrIdArr[] = $row['usersId'];
				$surgeon_name= "Surgeon - ".$row['usersId'];
					
				$tmpPatientProcNames = '';
				foreach( $superBillProcIdArr[$pConfId] as $tmpProc)
				{
					$tmpPatientProcNames .= '<i class="glyphicon glyphicon-chevron-right" style="font-size:10px;"></i>&nbsp;'.$proc_alias[$tmpProc].'<br>';
				}
				
				$pDetail[$surgeon_name][] = array('dos' => $row['dos'],
																					'name' => $row['pname'] .'-'.$row['patient_id'],
																					'procedures' => $tmpPatientProcNames);
				
				//echo '<br>ConfId = '.count($tmpReqProcArr);
				//$surgeon_name=trim($row['lname']).', '.trim($row['fname']).' '.trim($row['mname']);
				
				$total_cost[$surgeon_name]+=$row['labor_cost']+$row['supply_cost']; //TOTAL COST PER SURGEON
				$total_proc_cost[$proc_alias[$row['proc_id']]]+=$row['labor_cost']+$row['supply_cost']; //TOTAL COST PER PROCEDURE
				$total_proc_cnt[$proc_alias[$row['proc_id']]]++; //TOTAL COUNT PER PROCEDURE
				$proc_name=$row['proc_name'];
				
				if($row['labor_cost']<=0)
				{
					//array to hold not time recorded	
					$no_time_recorded[$surgeon_name]++;
				}
				$total_proc[$surgeon_name]++;
			}
		}
		//print_r($total_proc_cost);
		//die;
		if($procedure_id=='all')$proc_name='All';
		elseif(strstr($procedure_id,','))
		{$proc_name='Selected';}
		//get average of procedure for each surgeon
		foreach($total_cost as $surgeon=>$cost)
		{
			if($cost)
			{
				$value=0;
				$proc_average[$surgeon]=number_format(($cost/$total_proc[$surgeon]),2,'.','');
			}	
		}
		
	}
}


if($proc_name=='Selected')$sel_proc='Multiple';
elseif($proc_name!='all')$sel_proc='All';

if($physicianImp=='all')$sel_physician='All';
elseif(strstr($physicianImp,','))$sel_physician="Multiple";
else $sel_physician=$physiciansArr[$physicianImp];

$selDateRange	=	$date1.' To '.$date2;	

if($_REQUEST['action'] == 'csv')
{
		
		$csvData	=	array();
		array_push($csvData,array($name, '', 'Surgery Center Procedure Cost Analysis Report '.date("m-d-Y"),'' ));
		array_push($csvData,array($address,'','',''));
		array_push($csvData,array('','','',''));
		array_push($csvData,array('Procedure(s): '.$sel_proc,'','','Surgeon(s):'.$sel_physician));
		array_push($csvData,array('','','','Date Range:'.$selDateRange));
		array_push($csvData,array('','','',''));
		array_push($csvData,array('','','',''));
		array_push($csvData,array('Surgeon Name','Total Procedures','Total Cost','Average Cost'));

		foreach($total_cost as $surgeon=>$cost)
		{
			$totalCostNode	=	"$".number_format($total_cost[$surgeon],2,'.',',');
			$avgCostNode		=	"$".number_format($proc_average[$surgeon],2,'.',',');
				
			$csvDataNode	=	array($surgeon,$total_proc[$surgeon],$totalCostNode,$avgCostNode);
			array_push($csvData,$csvDataNode);
		}

		
		$file_name	=	'procedure_cost_analysis_report.csv';
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$file_name);
		
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		// output the column headings
		foreach($csvData as $key=>$csvDataN)
		{
			fputcsv($output, $csvDataN);	
		}
		exit;
		
	}
	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<title>Procedure Cost Analysis Report</title>    
<script type="text/javascript" src="js/chart_jquery.js"></script>


<!--<script type="text/javascript" src="//www.amcharts.com/lib/3/amcharts.js"></script>
<script type="text/javascript" src="//www.amcharts.com/lib/3/pie.js"></script>
<script type="text/javascript" src="//www.amcharts.com/lib/3/serial.js"></script>
-->
<script type="text/javascript" src="amcharts/amcharts.js"></script>
<script type="text/javascript" src="amcharts/pie.js"></script>
<script type="text/javascript" src="amcharts/serial.js"></script>
<script type="text/javascript" src="amcharts/themes/light.js"></script>
<script type="text/javascript" src="amcharts/responsive.js"></script>

<script type="text/javascript" src="amcharts/plugins/export/export.js"></script>
<!--<link  type="text/css" href="amcharts/plugins/export/export.css" rel="stylesheet">-->

<script type="text/javascript" src="js/chart_common.js"></script>
<?php include("common/link_new_file.php");?>
<link rel="stylesheet" type="text/css" href="css/chart_style.css" />
</head>

<body>
<div class="main_wrapper analysis-top-bar">
		<form name="proc_cost_ana_reportpop_csv" id="proc_cost_ana_reportpop_csv" method="post" action="proc_cost_ana_reportpop.php">
			<input type="hidden" name="proc_save" value="<?php echo $_REQUEST["proc_save"];?>">
			<input type="hidden" name="date1" value="<?php echo $_REQUEST["date1"];?>">
			<input type="hidden" name="date2" value="<?php echo $_REQUEST["date2"];?>">
			<input type="hidden" name="physician" value="<?php echo $_REQUEST["physician"];?>">
			<input type="hidden" name="procedure" value="<?php echo $_REQUEST["procedure"];?>">
			<input type="hidden" name="action" value="<?php echo "csv";?>">
		</form>

      <div class="container-fluid padding_0">
        <div class="inner_surg_middle ">
        
                <div style="" id="" class="all_content1_slider ">	         
                
                      <div class="wrap_inside_admin">
                        <div class=" subtracting-head">
                            <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                <span>Procedure Cost Analysis Report</span>
                                <a href="javascript:void(0)" class="btn btn-info analysis-csv-btn" id="generate_pdf" onclick="return exportPDF();" >
                                	<b class="fa fa-download"></b> Generate PDF 
                              	</a>
                              	<!--  
                                <a href="javascript:void(0)" class="btn btn-info analysis-csv-btn" id="generate_pdf" onclick="javascript:window.print();" >
                                	<b class="fa fa-print"></b> Print 
                              	</a>
                                -->
                                <a href="javascript:void(0)" class="btn btn-info analysis-csv-btn" id="generate_csv" onclick="document.proc_cost_ana_reportpop_csv.submit();" >
                                	<b class="fa fa-download"></b> Export CSV 
                              	</a>
                            </div>
                        </div>
                </div> 
              </div>  
        </div>
    </div>
 </div>
<?php
	
?>

<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="margin-top:35px;" id="filtersDiv">
    <div class="ledger_wrap">
             <div class="panel price panel-green margin_adjust_panel_ledger">
                <div class="panel-body text-center adjustable_ipad">
                    <ul class="list-group nav nav-justified">
                        <li class="list-group-item"> 
                            <div class="full_width2"> <span>   Selected Procedures  </span> </div>     
                            <div class="full_width2"> <span class="high">    <?php echo $sel_proc;?>	</span> </div>                                                                           
                        </li>
                         <li class="list-group-item"> 
                            <div class="full_width2"> <span>   Selected Surgeons  </span> </div>     
                            <div class="full_width2"> <span class="high">   <?php echo $sel_physician;?>	</span> </div>                                                                           
                        </li>
                         <li class="list-group-item"> 
                            <div class="full_width2"> <span>   Date Range  </span> </div>     
                            <div class="full_width2"> <span class="high"><?php echo $selDateRange;?></span> </div>                                                                           
                        </li>
                    </ul>
            </div>
         </div>
    </div> <!-- Ledger Wrap -->
</div>
<!--
---------------------
SURGEON WISE DATA
---------------------
-->
<?php
//create json array to total cost 
$key=0;
foreach($total_cost as $surgeon_name=>$cost)
{
	$total_cost_arr[$key]["kee"]=$surgeon_name;
	$total_cost_arr[$key]["val"]=number_format($cost,2,'.','');
	$key++;
}
$total_cost_js_arr=json_encode($total_cost_arr);

//create json array for average cost
$key=0;
foreach($proc_average as $surgeon_name=>$cost)
{
	$average_cost_arr[$key]["kee"]=$surgeon_name;
	$average_cost_arr[$key]["val"]=number_format($cost,2,'.','');
	
	$mean_cost_arr[$key]["kee"]=$surgeon_name;
	$mean_cost_arr[$key]["val"]=number_format($cost,2,'.','');
	$key++;
}

$average_cost_js_arr=json_encode($average_cost_arr);
$mean_cost_js_arr=json_encode($mean_cost_arr);

//create json array for mean cost
$key=0;
$key=0;
foreach($proc_average as $surgeon_name=>$cost)
{
	$totalSugeonsAverCost+=$cost;
	$totalSugeons++;
}
$meanCost=number_format($totalSugeonsAverCost/$totalSugeons,2,'.','');
$total_cost_js_arr=json_encode($total_cost_arr);

?>
<div class="clearfix margin_clear"></div>

<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 ">
        <div class="middle_sub_head text-left">
        <span>Total Procedure Cost Per Surgeon</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
        
        <div id="total_cost_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:400px; margin-left:-30px; margin-top:-30px;"></div>
    </div>
    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 ">
        <div class="middle_sub_head text-left">
        <span>Average Procedure Cost Per Surgeon</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
    	<div id="average_cost_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:400px; margin-left:-30px; margin-top:-30px;"></div> 
	</div>
    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 ">
        <div class="middle_sub_head text-left">
        <span>Mean Cost and Comparison</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
    	<div id="mean_cost_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:400px"></div> 
	</div>
   
</div>
<script type="text/javascript">
	<?php if($total_cost_js_arr){?>pie_chart('pie','total_cost_div','<?php echo $total_cost_js_arr; ?>','$');<?php }?>
	<?php if($average_cost_js_arr){?>pie_chart('pie','average_cost_div','<?php echo $average_cost_js_arr; ?>','$');<?php }?>
	//bar_chart('serial','mean_cost_div','<?php echo $total_cost_js_arr; ?>','','');
	<?php if($mean_cost_js_arr){?>line_chart2(<?php echo $mean_cost_js_arr; ?>,'Mean Cost',<?php echo $meanCost;?>,'mean_cost_div','15','$','Cost');<?php }?>
</script> 
<!--
---------------------
PROCEDURE WISE DATA
---------------------

-->
<?php
//create json array to total cost 
$multi_proc_len = is_array($tmpReqProcArr) ? count($tmpReqProcArr) : 0;
$multi_proc = (is_array($tmpReqProcArr) && count($tmpReqProcArr) > 1 ) ? true : false;
$no_of_proc=0;$proc_char_limit=($multi_proc) ? 15 : 17;
foreach($total_proc_cost as $proc_name=>$cost)
{
	$total_proc_cost_arr[$no_of_proc]["kee"]=(strlen($proc_name)>$proc_char_limit)?substr($proc_name,0,$proc_char_limit).'..':$proc_name.($multi_proc_len > 1 ? ' + '.($multi_proc_len-1) : '' );
	$total_proc_cost_arr[$no_of_proc]["val"]=number_format($cost,2,'.','');
	$no_of_proc++;
}
$total_proc_cost_js_arr=json_encode($total_proc_cost_arr);
//get average of procedure for each surgeon
foreach($total_proc_cost as $proc_name=>$cost)
{
	if($cost)
	{
		$value=0;
		$total_num_of_proc = $total_proc_cnt[$proc_name];
		$proc_cost_average[$proc_name]=number_format(($cost/$total_num_of_proc),2,'.','');
	}	
}
//create json array for average cost
$key=0;
foreach($proc_cost_average as $proc_name=>$cost)
{
	$proc_average_cost_arr[$key]["kee"]=(strlen($proc_name)>$proc_char_limit)?substr($proc_name,0,$proc_char_limit).'..':$proc_name.($multi_proc_len > 1 ? ' + '.($multi_proc_len-1) : '' );
	$proc_average_cost_arr[$key]["val"]=number_format($cost,2,'.','');
	$key++;
}
$proc_average_cost_js_arr=json_encode($proc_average_cost_arr);

?>
<div class="clearfix margin_clear"></div>

<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 ">
        <div class="middle_sub_head text-left">
        <span>Total Cost Per Procedure</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
        
        <div id="total_cost_div1" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:400px; margin-left:-20px; margin-top:-20px;"></div>
        <?php echo '<br clear="all">'.$tmpReqProcName;?>
    </div>
    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 ">
        <div class="middle_sub_head text-left">
        <span>Average Cost Per Procedure</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
    		<div id="average_cost_div1" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:400px; margin-left:-20px; margin-top:-20px;"></div> 
        <?php echo '<br clear="all">'.$tmpReqProcName;?>
		</div>
   <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 ">
        <div class="middle_sub_head text-left">
        <span>No Time Recorded</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
    	<div id="" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:400px; overflow:auto">
        	<table class = "table table-bordered">
               <tbody>
                  <tr>
                     <th class="col-md-2 col-sm-2 col-xs-2 col-lg-2">Sr.</th>
                     <th class="col-md-7 col-sm-7 col-xs-7 col-lg-7">Surgeon</th>
                     <th class="col-md-3 col-sm-3 col-xs-3 col-lg-3">Total Records</th>
                  </tr>
               
               
                  <?php
										$pdfNoTimeArray		=	array();
										$pdfNoTimeArray[]	=	array('Sr.','Surgeon','Total Records');
                    foreach($no_time_recorded as $surgeon=>$total_proc1)
                    {
											$sr++;
											$pdfNoTimeArray[]	=	array((string)$sr,$surgeon,(string)$total_proc1);
                    ?>
                  <tr>
                     <td class="col-md-2 col-sm-2 col-xs-2 col-lg-2"><?php echo $sr;?></td>
                     <td class="col-md-7 col-sm-7 col-xs-7 col-lg-7"><?php echo $surgeon;?></td>
                     <td class="col-md-3 col-sm-3 col-xs-3 col-lg-3"><?php echo $total_proc1;?></td>
                  </tr>
                  <?php  }?>
               </tbody>
                
            </table>
        </div> 
	</div>
</div>
<script type="text/javascript">
	<?php if($total_proc_cost_js_arr){?>pie_chart('pie','total_cost_div1','<?php echo $total_proc_cost_js_arr; ?>','$');<?php }?>
	<?php if($proc_average_cost_js_arr){?>pie_chart('pie','average_cost_div1','<?php echo $proc_average_cost_js_arr; ?>','$');<?php }?>
</script>

<!--
---------------------
TOP TEN
---------------------
-->
<div class="main_wrapper">
      <div class="container-fluid padding_0">
        <div class="inner_surg_middle ">
        
                <div style="" id="" class="all_content1_slider ">	         
                
                      <div class="wrap_inside_admin">
                        <div class=" subtracting-head">
                            <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                <span>Top Ten Surgeon By Procedure Cost</span>
                            </div>
                        </div>
                </div> 
              </div>  
        </div>
    </div>
 </div>
 
 <?php
//get top ten procedure for given surgeons
//unset($surgeon_name, $total_cost, $total_proc, $proc_average, $cost_arr, $highest_js_arr, $lowest_js_arr);
$andSurgeonIdQry = "";
$tmpUsrIdImplode = trim(implode(",",$tmpUsrIdArr));
if($tmpUsrIdImplode) { $andSurgeonIdQry = " AND pc.surgeonId IN(".$tmpUsrIdImplode.") "; }

$pConfIdImplode = trim(implode(",",$pConfIdArr));
$andConfIdQry = "";
if($pConfIdImplode) {$andConfIdQry = " AND pc.patientConfirmationId IN (".$pConfIdImplode.") ";  }
$qry = "SELECT COUNT(*) AS total_rec, SUM(pc.labor_cost+pc.supply_cost) AS total_cost, pc.patientConfirmationId, 
		users.usersId, users.fname, users.mname, users.lname 
		FROM patientconfirmation pc
		INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.patient_status!='Canceled')
		LEFT JOIN users ON(users.usersId = pc.surgeonId)
		$proc_JOIN
		WHERE (pc.dos BETWEEN '".$from_date."' AND '".$to_date."') ".$fac_con."
		AND pc.cost_procedure_id > 0
		AND pc.finalize_status='true'
		/* AND (pc.supply_cost>0 || pc.labor_cost>0) */ ".$physicianQry."
		AND pc.surgeonId<>0
		".$andSurgeonIdQry.$andConfIdQry."
		GROUP BY pc.surgeonId
		ORDER BY total_cost desc
		LIMIT 0,10";

$res = imw_query($qry)or die(imw_error());
if(imw_num_rows($res)>0) {
	//get total cost of procedure for each surgeon
	while($row = imw_fetch_assoc($res)) 
	{
		$recExistNew=false;
		$pConfIdNew = $row["patientConfirmationId"];
		if(count(array_intersect($superBillProcIdArr[$pConfIdNew], $tmpReqProcArr)) == count($tmpReqProcArr) && count($superBillProcIdArr[$pConfIdNew]) == count($tmpReqProcArr)){
			$recExistNew = true;
		}
		//echo '<br>'.$row['fname'].' @@ '.$pConfIdNew.' @@ '.($recExistNew ? 'TRUE' : 'FALSE') ;
		if($row['fname'])
		{	
			//$surgeon_name=trim($row['lname']).', '.trim($row['fname']).' '.trim($row['mname']);
			$surgeon_name= "Surgeon - ".$row['usersId'];
			$total_cost_top_ten[$surgeon_name]+=$row['total_cost'];
			$total_proc_top_ten[$surgeon_name]+=$row['total_rec'];
		}
	}
	//get average of procedure for each surgeon
	foreach($total_cost_top_ten as $surgeon=>$cost)
	{
		if($cost)
		{
			$value=0;
			$proc_average_top_ten[$surgeon]=number_format(($cost/$total_proc_top_ten[$surgeon]),2,'.','');
		}	
	}
}
	
?>
<div class="clearfix margin_clear"></div>

<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 ">
        <div class="middle_sub_head text-left">
        <span>Top Ten Sugeon By Procedure Average Cost (lowest to highest)</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
        <div id="highest_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:450px"></div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 ">
        <div class="middle_sub_head text-left">
        <span>Top Ten Sugeon By Procedure Average Cost (highest to lowest)</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
    	<div id="lowest_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:450px"></div> 
	</div>
</div>
<?php
//create json array for average cost
$key=0;
foreach($proc_average_top_ten as $surgeon_name=>$cost)
{
	$cost_arr[$key]["surgeon"]=$surgeon_name;
	$cost_arr[$key]["average_cost"]=$cost;
	$cost_arr[$key]["no_of_procedures"]=$total_proc_top_ten[$surgeon_name];
	$key++;
}
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

array_sort_by_column($cost_arr, 'average_cost',SORT_ASC);
$highest_js_arr=json_encode($cost_arr);
array_sort_by_column($cost_arr, 'average_cost',SORT_DESC);
$lowest_js_arr=json_encode($cost_arr);
?>
<script type="text/javascript">
	<?php if($highest_js_arr){?>columnAndLine_chart(<?php echo $highest_js_arr; ?>,'highest_div','$',15);<?php }?>
	<?php if($lowest_js_arr){?>columnAndLine_chart(<?php echo $lowest_js_arr; ?>,'lowest_div','$',15);<?php }?>
</script> 

<div class="clearfix margin_clear"></div>
<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 well-sm">

    <div  class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    
    <div class="middle_sub_head text-left">
        <span>Surgeon Data</span>
        
   	</div>
    <span class="pull-right">
    	<button class="btn btn-success" data-toggle="modal" data-target="#pDetail">More Details</button>
   	</span>
    <div class="clearfix margin_line margin_Adjust_sl"></div>
    </div>
    
    
    <div class="clearfix"></div>
    <table class = "table table-bordered margin_0">
  		<tbody>
      <tr>
         <th class="col-md-3 col-sm-3 col-xs-3 col-lg-3">Surgeon</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Total Procedure</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Total Cost</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Average Cost</th>
         
         <th class="col-md-3 col-sm-3 col-xs-3 col-lg-3">Surgeon</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Total Procedure</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Total Cost</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Average Cost</th>
      </tr>
   
   <tr>
      <?php
			$pdfSurgeonArray	=	array();
			$pdfSurgeonArray[]	=	array('Surgeon','Total Procedure','Total Cost','Average Cost');
			foreach($total_cost as $surgeon=>$cost)
			{
				$childNode_cost	=	"$".number_format($total_cost[$surgeon],2,'.',',');
				$childNode_avg	=	"$".number_format($proc_average[$surgeon],2,'.',',');
				
				$pdfSurgeonArray[]	=	array($surgeon,(string)$total_proc[$surgeon],$childNode_cost,$childNode_avg);
				
			?>
      
         <td class="col-md-3 col-sm-3 col-xs-3 col-lg-3"><?php echo $surgeon;?></td>
         <td class="col-md-1 col-sm-1 col-xs-1 col-lg-1"><?php echo $total_proc[$surgeon];?></td>
         <td class="col-md-1 col-sm-1 col-xs-1 col-lg-1"><?php echo $childNode_cost; ?></td>
         <td class="col-md-1 col-sm-1 col-xs-1 col-lg-1"><?php echo $childNode_avg;?></td>
      <?php 
	  		$rowCount++;
	  		if(($rowCount%2)==0)echo '</tr><tr>';
			?>
      <?php }?>
      </tr>
   </tbody>
	
</table>

    
</div>

<?php echo $objManageData->vcnaPatientModal($pDetail); ?>

<script>

function exportPDF()
{	
		var _pdfNoTimeArray	=	<?=json_encode($pdfNoTimeArray)?>;	
		var _pdfSurgeonArray=	<?=json_encode($pdfSurgeonArray)?>;
		var _pdfReportTitle	=	' Procedure Cost Analysis Report <?=date('m-d-Y')?> ';
		var _pdfSaveAsName	=	'procedure_cost_analysis_report.pdf';
		var _pdfChartsArray	=	["total_cost_div", "average_cost_div", "mean_cost_div", "total_cost_div1", "average_cost_div1","highest_div","lowest_div"];
		var _pdfFilters			=	{'sel_proc':'<?=$sel_proc?>','sel_physician':'<?=$sel_physician?>','date_range':'<?=$selDateRange?>'};
		var _proc_names			=	'<?php echo $tmpReqProcName; ?>';
		var _pdfContent			=	[];
		
		
		// Push Cost Charts Per Surgeon Total/Average/Mean
		_pdfContent.push({
			columns: 
			[
					{
						width: "33%",
						text: 'Total Procedure Cost Per Surgeon',
						style:'boxTitle'
					},
					{
						width: "33%",
						text: 'Average Procedure Cost Per Surgeon',
						style:'boxTitle'
						
					},
					{
						width: "*",
						text: 'Mean Cost and Comparison',
						style:'boxTitle'
					}
			],
			columnGap: 5
		});
		
		_pdfContent.push({
			columns: 
			[
					{
						width: "33%",
						image: 0,
						fit: [170, 200]
					},
					{
						width: "33%",
						image: 1,
						fit: [170, 200]
					},
					{
						width: "*",
						image: 2,
						fit: [200, 150]
					}
			],
			columnGap: 5,
			margin:[0,5,0,0]
		});
		
		
		// Push Cost Charts Per Procedure Total/Average/Mean
		_pdfContent.push({
			columns:
			[
				{
					width: "33%",
					text: "Total Cost Per Procedure",
					style:'boxTitle'
				},
				{
					width: "33%",
					text: "Average Cost Per Procedure",
					style:'boxTitle' 
					
				},
				{
					width: "*",
					text: "No Time Recorded",
					style:'boxTitle' 
				}
			],
			columnGap: 5,
			margin:[0,5,0,0]
		});
		
		_pdfContent.push({
			columns: 
			[
					{
						width: "33%",
						image: 3,
						fit: [200, 200]
					},
					{
						width: "33%",
						image: 4,
						fit: [200, 200]
					},
					{
						width: "*",	
						table: {
											// headers are automatically repeated if the table spans over multiple pages
											// you can declare how many rows should be treated as headers
											headerRows: 1,
											widths: ["10%","60%","*"],
											"body": _pdfNoTimeArray
									 }
					}
			],
			columnGap: 5,
			margin:[0,5,0,0]
		});
		
		// Push Procedure Names selected for Per Procedure Total/Average
		_pdfContent.push({
			columns:
			[
				{
					width: "33%",
					text: _proc_names,
					style:'boxText'
				},
				{
					width: "33%",
					text: _proc_names,
					style:'boxText' 
					
				},
				{
					width: "*",
					text: "",
					style:'boxText' 
				}
			],
			columnGap: 5,
			margin:[0,5,0,0]
		});
		
		// Push Top Ten Surgeon Charts 
		_pdfContent.push({ 
			text:' Top Ten Surgeon By Procedure Cost ',style:'subHeader'
		});
		
		_pdfContent.push({
			columns: 
			[
				{
					width: "50%",
					text: "Top Ten Sugeon By Procedure Average Cost (lowest to highest)",
					style: "boxTitle",
				},
				{
					width: "*",
					text: "Top Ten Sugeon By Procedure Average Cost (highest to lowest)",
					style: "boxTitle",
				}
			],
			columnGap: 5,
			margin:[0,5,0,0]
		});
		
		_pdfContent.push({
			columns:
			[
				{
					width: "50%", 
					image: 5,
					fit: [250, 300]
				},
				{
					width: "*",
					image: 6,
					fit: [270, 300]
				}
			],
			columnGap: 5,
			margin:[0,5,0,0]
		});
		
		// Push Surgeon's Data Table
		_pdfContent.push({ 
			text:' Surgeon Data ', style:'boxTitle', margin:[0,20,0,0]
		});
		
		_pdfContent.push({
				table: {
									headerRows: 1,
									widths: ["25%", "25%", "25%", "*"],
									"body": _pdfSurgeonArray
								},
								margin:[0,5,0,0]
		});
		
		downloadPDF(_pdfReportTitle,_pdfSaveAsName,_pdfChartsArray,_pdfFilters,_pdfContent);

}
</script>

</body>
</html>