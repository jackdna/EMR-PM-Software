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
	$surgeonExclude = 'And vs.surgeonId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').')';	
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
	$procedure_id 	= implode(",", $tmpReqProcArr);
	
	$physicianImp = $_REQUEST["physician"];
	//$physicianImp = implode(",",$physicianArr);
	
	if(!$procedure_id) {  $procedure_id = '0';}
	if(!$physicianImp) {  $physicianImp = '0';}
	else
	{
		if($physicianImp!='all'){
		$physicianQry=" AND vs.surgeonId IN ($physicianImp)";}
		
		if($physicianImp =='all'){
		$physicianQry = $surgeonExclude;}
	}
	
	
	$procIdQry = "";
	if($procedure_id!='all') {
		$procIdQry = " AND vs.procedure IN(".$procedure_id.") ";	 
	}
	
	$qry = "SELECT vs.*,
			users.usersId, users.fname, users.mname, users.lname,
			procedures.name as proc_name,
			CONCAT(pdt.patient_lname,', ',pdt.patient_fname,' ', pdt.patient_mname) as pname, pdt.patient_id, vs.dos
			FROM vision_success vs
			LEFT JOIN users ON(users.usersId = vs.surgeonId)
			LEFT JOIN patient_data_tbl pdt ON(vs.patientId = pdt.patient_id)
			LEFT JOIN procedures ON(procedures.procedureId = vs.procedure)
			WHERE (vs.dos BETWEEN '".$from_date."' AND '".$to_date."') ".$fac_con."
			AND vs.vision_20_40!='' ".$physicianQry."
			ORDER BY users.usersId ASC";	
	$res = imw_query($qry)or die(imw_error().' ----- ');
	$total_proc = array();
	$tmpUsrIdArr = array();
	$pConfIdArr = array();
	$pDetail = array();
	if(imw_num_rows($res)>0) {
		//get total cost of procedure for each surgeon
		while($row = imw_fetch_assoc($res)) 
		{
			$recExist=false;
			$pConfId = $row["confirmation_id"];
			if(count(array_intersect($superBillProcIdArr[$pConfId], $tmpReqProcArr)) == count($tmpReqProcArr) && count($superBillProcIdArr[$pConfId]) == count($tmpReqProcArr)){
				$recExist = true;
				$pConfIdArr[] = $pConfId;
			}
			
			if($row['fname'] && $recExist == true)
			{
				//$surgeon_name=trim($row['lname']).', '.trim($row['fname']).' '.trim($row['mname']);
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
				
				if($row['vision_20_40']=='Yes'){$total_better_vision[$surgeon_name]+=1;}
				else{$total_worst_vision[$surgeon_name]+=1;}
				$total_proc[$surgeon_name]++;
				$proc_name=$row['proc_name'];
			}
		}
		if($procedure_id=='all')$proc_name='All';
		elseif(strstr($procedure_id,','))
		{$proc_name='Selected';}
		//get average of procedure for each surgeon
		foreach($total_better_vision as $surgeon=>$better_vision)
		{
			if($better_vision)
			{
				$value=0;
				$better_vision_average[$surgeon]=number_format(($better_vision/$total_proc[$surgeon]),2,'.','');
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
		array_push($csvData,array($name, '', 'Surgery Center Vision Success Report '.date("m-d-Y"),'' ));
		array_push($csvData,array($address,'','',''));
		array_push($csvData,array('','','',''));
		array_push($csvData,array('Procedure(s): '.$sel_proc,'','','Surgeon(s):'.$sel_physician));
		array_push($csvData,array('','','','Date Range:'.$selDateRange));
		array_push($csvData,array('','','',''));
		array_push($csvData,array('','','',''));
		array_push($csvData,array('Surgeon','Total Procedures','Total 20/40 Vision Or Better','Ave. 20/40 Vision Or Better'));

		foreach($total_better_vision as $surgeon=>$better_vision)
		{
			$csvDataNode	=	array($surgeon,$total_proc[$surgeon],$better_vision,$better_vision_average[$surgeon]);
			array_push($csvData,$csvDataNode);
		}

		
		$file_name	=	'vision_success_report.csv';
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
<title>Vision Success Report</title>    
<script type="text/javascript" src="js/chart_jquery.js"></script>

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
		<form name="vision_success_reportpop_csv" id="vision_success_reportpop_csv" method="post" action="vision_success_reportpop.php">
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
                                <span>Vision Success Report</span>
                                <a href="javascript:void(0)" class="btn btn-info analysis-csv-btn" id="generate_pdf" onclick="return exportPDF();" >
                                	<b class="fa fa-download"></b> Generate PDF 
                              	</a>
                                <!--
                                <a href="javascript:void(0)" class="btn btn-info analysis-csv-btn" id="generate_pdf" onclick="javascript:window.print();" >
                                	<b class="fa fa-print"></b> Print 
                              	</a>
                                -->
                                <a href="javascript:void(0)" class="btn btn-info analysis-csv-btn" id="generate_csv" onclick="javascript:document.vision_success_reportpop_csv.submit();" >
                                	<b class="fa fa-download"></b> Export CSV 
                              	</a>
                            </div>
                        </div>
                </div> 
              </div>  
        </div>
    </div>
 </div><?php
	
?>
<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="margin-top:35px;">
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
<div class="clearfix margin_clear"></div>
<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 margin_top_5">
    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 ">
        <div class="middle_sub_head text-left">
        <span>Total Post-OP Patient with 20/40 Vision or Better</span></div>
    
        <div class="clearfix margin_adjustment_only margin_line"></div>
        <div id="total_cost_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:400px"></div>
    </div>
    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 ">
    	<div class="middle_sub_head text-left">
        <span>Average  Post-OP Patient with 20/40 Vision or Better</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
    	<div id="average_cost_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:400px"></div> 
	</div>
    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 ">
    	<div class="middle_sub_head text-left">
        <span>Mean of Post-OP Patient with 20/40 Vision or Better</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
    	<div id="mean_cost_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:400px"></div> 
	</div>
   
</div>


<?php
//create json array to total better vision 
$key=0;
foreach($total_better_vision as $surgeon_name=>$better_vision)
{
	if($total_worst_vision[$surgeon_name])
	{
		$total_vision_arr[$key]["kee"]=$surgeon_name.' (Better)';
		$total_vision_arr[$key]["val"]=$better_vision;
		$key++;
		$total_vision_arr[$key]["kee"]=$surgeon_name.' (Worse)';
		$total_vision_arr[$key]["val"]=$total_worst_vision[$surgeon_name];
		
	}else
	{
		$total_vision_arr[$key]["kee"]=$surgeon_name;
		$total_vision_arr[$key]["val"]=$better_vision;	
	}
	$key++;
}

//create json array for average better vision
$key=0;
foreach($better_vision_average as $surgeon_name=>$vision_average)
{
	if($total_worst_vision[$surgeon_name])
	{
		$average_vision_arr[$key]["kee"]=$surgeon_name.' (Better)';
		$average_vision_arr[$key]["val"]=$vision_average;
		$key++;
		$average_vision_arr[$key]["kee"]=$surgeon_name.' (Worse)';
		$average_vision_arr[$key]["val"]=(1-$vision_average);
	}else
	{
		$average_vision_arr[$key]["kee"]=$surgeon_name;
		$average_vision_arr[$key]["val"]=$vision_average;	
	}
	$key++;
}
//run loop again to create mean chat array
$key=0;
foreach($better_vision_average as $surgeon_name=>$vision_average)
{
	$mean_vision_arr[$key]["kee"]=$surgeon_name;
	$mean_vision_arr[$key]["val"]=$vision_average;
	$key++;
}

$total_vision_js_arr=json_encode($total_vision_arr);
$average_vision_js_arr=json_encode($average_vision_arr);
$mean_vision_arr=json_encode($mean_vision_arr);
//create json array for mean
$key=0;
$totalSugeons=0;
foreach($better_vision_average as $surgeon_name=>$vision_average)
{
	$totalSugeonsAverVision+=$vision_average;
	$totalSugeons++;
}

$meanVision=number_format($totalSugeonsAverVision/$totalSugeons,2,'.','');
?>

<script type="text/javascript">
	<?php if($total_vision_js_arr){?>pie_chart('pie','total_cost_div','<?php echo $total_vision_js_arr; ?>','');<?php }?>
	<?php if($average_vision_js_arr){?>pie_chart('pie','average_cost_div','<?php echo $average_vision_js_arr; ?>','');<?php }?>
	//bar_chart('serial','mean_cost_div','<?php echo $total_cost_js_arr; ?>','','');
	<?php if($mean_vision_arr){?>line_chart2(<?php echo $mean_vision_arr; ?>,'Mean',<?php echo $meanVision;?>,'mean_cost_div','15','','');<?php }?>
</script>
<!--
---------------------
TOP TEN SURGEON
---------------------
-->

<div class="main_wrapper">
      <div class="container-fluid padding_0">
        <div class="inner_surg_middle ">
        
                <div style="" id="" class="all_content1_slider ">	         
                
                      <div class="wrap_inside_admin">
                        <div class=" subtracting-head">
                            <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                <span>Top Ten Surgeon</span>
                            </div>
                        </div>
                </div> 
              </div>  
        </div>
    </div>
 </div>
 
 <?php
//get top ten procedure for given surgeons
$andSurgeonIdQry = "";
$tmpUsrIdImplode = trim(implode(",",$tmpUsrIdArr));
if($tmpUsrIdImplode) { $andSurgeonIdQry = " AND vs.surgeonId IN(".$tmpUsrIdImplode.") "; }

$pConfIdImplode = trim(implode(",",$pConfIdArr));
$andConfIdQry = "";
if($pConfIdImplode) {$andConfIdQry = " AND vs.confirmation_id IN (".$pConfIdImplode.") ";  }
		
$qry = "SELECT vs.*,
			users.usersId, users.fname, users.mname, users.lname,
			procedures.name as proc_name
			FROM vision_success vs
			LEFT JOIN users ON(users.usersId = vs.surgeonId)
			LEFT JOIN procedures ON(procedures.procedureId = vs.procedure)
			WHERE (vs.dos BETWEEN '".$from_date."' AND '".$to_date."') ".$fac_con."
			AND vs.vision_20_40!='' ".$physicianQry."
			".$andSurgeonIdQry.$andConfIdQry."
			ORDER BY users.usersId ASC";

$res = imw_query($qry)or die(imw_error().' ----- ');
$recExist=false;//die($qry);
if(imw_num_rows($res)>0) {
	//get total cost of procedure for each surgeon
	$total_better_vision = $total_worst_vision= $total_proc = array();
	while($row = imw_fetch_assoc($res)) 
	{
		if($row['fname'])
		{
			//$surgeon_name=trim($row['lname']).', '.trim($row['fname']).' '.trim($row['mname']);
			$surgeon_name= "Surgeon - ".$row['usersId'];
			if($row['vision_20_40']=='Yes'){ $total_better_vision[$surgeon_name]+=1;}
			else{$total_worst_vision[$surgeon_name]+=1;}
			$total_proc[$surgeon_name]++;
		}
	}
}
	
?>
<div class="clearfix margin_clear"></div>

<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 ">
        <div class="middle_sub_head text-left">
        <span>Top Ten Sugeon by Number of Post-op Patient with 20/40 Vision or Better</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
        <div id="highest_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:450px"></div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 ">
        <div class="middle_sub_head text-left">
        <span>Top Ten Sugeon by Number of Post-op Patient with Vision Worse than 20/40</span></div>
        <div class="clearfix margin_adjustment_only margin_line"></div>
    	<div id="lowest_div" class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style="height:450px"></div> 
	</div>
</div>
<?php
//create json array for average cost
$key=0;
foreach($total_better_vision as $surgeon_name=>$total_visions)
{
	$better_arr[$key]["surgeon"]=$surgeon_name;
	$better_arr[$key]["average_cost"]=$total_visions;
	$better_arr[$key]["no_of_procedures"]=$total_proc[$surgeon_name];
	$key++;
}$key=0;
foreach($total_worst_vision as $surgeon_name=>$total_visions)
{
	$worse_arr[$key]["surgeon"]=$surgeon_name;
	$worse_arr[$key]["average_cost"]=$total_visions;
	$worse_arr[$key]["no_of_procedures"]=$total_proc[$surgeon_name];
	$key++;
}
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
	if( is_array($arr) && count($arr) > 0 ) {
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
	}
	else $arr = array();

    array_multisort($sort_col, $dir, $arr);
}

array_sort_by_column($better_arr, 'average_cost',SORT_DESC);
//create new array with only 10 value
for($i=0;$i<=10;$i++)
{
	if($better_arr[$i])$highest_arr[]=$better_arr[$i];
}
$highest_js_arr=json_encode($highest_arr);
array_sort_by_column($worse_arr, 'average_cost',SORT_DESC);
//create new array with only 10 value
for($i=0;$i<=10;$i++)
{
	if($worse_arr[$i])$lowest_arr[]=$worse_arr[$i];
}
$lowest_js_arr=json_encode($lowest_arr);
?>
<script type="text/javascript">
	window.focus();
	<?php if($highest_js_arr){?>columnAndLine_chart(<?php echo $highest_js_arr; ?>,'highest_div','',15, 'No. of Patient');<?php }?>
	<?php if($lowest_js_arr){?>columnAndLine_chart(<?php echo $lowest_js_arr; ?>,'lowest_div','',15, 'No. of Patient');<?php }?>
</script> 
<!--
---------------------
TABLE FOR DATA
---------------------
-->
<div class="clearfix margin_clear"></div>
<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 well-sm">

    <div  class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    
    <div class="middle_sub_head text-left">
        <span>Vision Success Data</span>
    </div>
    <span class="pull-right">
      	<button class="btn btn-success" data-toggle="modal" data-target="#pDetail">More Details</button>
     	</span>
    <div class="clearfix margin_line margin_Adjust_sl"></div>
    </div>
    
    
    <div class="clearfix"></div>
    <table class = "table table-bordered">
   <tbody>
      <tr>
         <th class="col-md-3 col-sm-3 col-xs-3 col-lg-3">Surgeon</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Total Procedure</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Total 20/40 Vision Or Better</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Ave. 20/40 Vision Or Better</th>
         
         <th class="col-md-3 col-sm-3 col-xs-3 col-lg-3">Surgeon</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Total Procedure</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Total 20/40 Vision Or Better</th>
         <th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Ave. 20/40 Vision Or Better</th>
      </tr>
   
   <tr>
      <?php
				$pdfSurgeonArray	=	array();
				$pdfSurgeonArray[]	=	array('Surgeon','Total Procedure','Total 20/40 Vision Or Better','Ave. 20/40 Vision Or Better');
				foreach($total_better_vision as $surgeon=>$better_vision)
				{
					$pdfSurgeonArray[]	=	array($surgeon,(string)$total_proc[$surgeon],(string)$better_vision,$better_vision_average[$surgeon]);
			?>
      
         <td class="ol-md-3 col-sm-3 col-xs-3 col-lg-3"><?php echo $surgeon;?></td>
         <td class="col-md-1 col-sm-1 col-xs-1 col-lg-1"><?php echo $total_proc[$surgeon];?></td>
         <td class="col-md-1 col-sm-1 col-xs-1 col-lg-1"><?php echo $better_vision;?></td>
         <td class="col-md-1 col-sm-1 col-xs-1 col-lg-1"><?php echo $better_vision_average[$surgeon];?></td>
      <?php 
	  $rowCount++;
	  if(($rowCount%2)==0)echo '</tr><tr>';?>
      
      <?php }?>
      </tr>
   </tbody>
	
</table>
    
</div>
<?php echo $objManageData->vcnaPatientModal($pDetail); ?>
<script>

function exportPDF()
{	
		var _pdfNoTimeArray	=	'';	
		var _pdfSurgeonArray=	<?=json_encode($pdfSurgeonArray)?>;
		var _pdfReportTitle	=	' Vision Success Report <?=date('m-d-Y')?> ';
		var _pdfSaveAsName	=	'vision_success_report.pdf';
		var _pdfChartsArray	=	["total_cost_div", "average_cost_div", "mean_cost_div", "highest_div","lowest_div"];
		var _pdfFilters			=	{'sel_proc':'<?=$sel_proc?>','sel_physician':'<?=$sel_physician?>','date_range':'<?=$selDateRange?>'};
		var _pdfContent			=	[];
		
		
		// Push Cost Charts Per Surgeon Total/Average/Mean
		_pdfContent.push({
			columns: 
			[
					{
						width: "33%",
						text: 'Total Post-OP Patient with 20/40 Vision or Better',
						style:'boxTitle'
					},
					{
						width: "33%",
						text: 'Average Post-OP Patient with 20/40 Vision or Better',
						style:'boxTitle'
						
					},
					{
						width: "*",
						text: 'Mean of Post-OP Patient with 20/40 Vision or Better',
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
						fit: [180, 200]
					},
					{
						width: "33%",
						image: 1,
						fit: [180, 200]
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
		
		
		// Push Top Ten Surgeon Charts 
		_pdfContent.push({ 
			text:' Top Ten Surgeon ',style:'subHeader'
		});
		
		_pdfContent.push({
			columns: 
			[
				{
					width: "50%",
					text: "Top Ten Sugeon by Number of Post-op Patient with 20/40 Vision or Better",
					style: "boxTitle",
				},
				{
					width: "*",
					text: "Top Ten Sugeon by Number of Post-op Patient with Vision Worse than 20/40",
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
					image: 3,
					fit: [250, 300]
				},
				{
					width: "*",
					image: 4,
					fit: [270, 300]
				}
			],
			columnGap: 5,
			margin:[0,5,0,0]
		});
		
		// Push Surgeon's Data Table
		_pdfContent.push({ 
			text:' Vision Success Data ', style:'boxTitle', margin:[0,20,0,0]
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