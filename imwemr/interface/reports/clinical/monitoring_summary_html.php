<?php
$page_header_val = '
<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
		<tr>
			<td class="rptbx1" style="width:33%;">Monitoring Report '.ucfirst($report_view).'</td>
			<td class="rptbx2" style="width:33%;">From '.$Start_date.' To '.$End_date.'</td>
			<td class="rptbx3" style="width:34%;">Created by: '.$op_name.' on '.$curDate.'</td>
		</tr>
</table>';

if($task_type== 'reminders' || $task_type == ""){	
	if(count($userFutureArr)){
		$userFuturehtmlsummary = '<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding"><tr><td colspan="2" class="text_b_w">Reminders</td></tr><tr><td class="text_b_w" style="width:50%">User Name</td><td class="text_b_w" style="width:50%">Count</td></tr>';
		foreach($userFutureArr as $userFuturekey => $userFutureval){
			$userFutureUserName = $providerNameArr[$userFuturekey];
			$userFuturehtmlsummary .= '<tr><td>'.$userFutureUserName.'</td> <td>'.count($userFutureval).'</td></tr>';
		}
		$csvpdfData .= $userFuturehtmlsummary .= '</table>';
	}
}

if($task_type== 'tasks' || $task_type == ""){	
	if(count($testsArr)){
		$testshtmlsummary = '<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding"><tr><td colspan="2" class="text_b_w">Tasks / Tests</td></tr><tr><td class="text_b_w" style="width:50%">User Name</td><td class="text_b_w" style="width:50%">Count</td></tr>';
		foreach($testsArr as $testskey => $testsArrval){
			$testUserName = $providerNameArr[$testskey];
			$testshtmlsummary .= '<tr><td>'.$testUserName.'</td> <td>'.count($testsArrval).'</td></tr>';
		}
		$csvpdfData .= $testshtmlsummary .= '</table>';
	}
}

if($task_type== 'messages' || $task_type == ""){	
	if(count($userMsgArr)){
		$userMsghtmlsummary = '<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding"><tr><td colspan="2" class="text_b_w">User Messages</td></tr><tr><td class="text_b_w" style="width:50%">User Name</td><td class="text_b_w" style="width:50%">Count</td></tr>';
		foreach($userMsgArr as $userMsgkey => $userMsgval){
			$userMsgUserName = $providerNameArr[$userMsgkey];
			$userMsghtmlsummary .= '<tr><td>'.$userMsgUserName.'</td> <td>'.count($userMsgval).'</td></tr>';
		}
		$csvpdfData .= $userMsghtmlsummary .= '</table>';
	}
}

if($task_type== 'orders' || $task_type == ""){	
	if(count($userOdrArr)){
		$userOdrhtmlsummary = '<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding"><tr><td colspan="2" class="text_b_w">Orders</td></tr><tr><td class="text_b_w" style="width:50%">User Name</td><td class="text_b_w" style="width:50%">Count</td></tr>';
		foreach($userOdrArr as $userOdrkey => $userOdrval){
			$userOdrName = $providerNameArr[$userOdrkey];
			$userOdrhtmlsummary .= '<tr><td>'.$userOdrName.'</td> <td>'.count($userOdrval).'</td></tr>';
		}
		$csvpdfData .= $userOdrhtmlsummary .= '</table>';
	}
}

if($task_type== 'to_do' || $task_type == ""){
	if(count($toDoArr)){
		$toDohtmlsummary = '<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding"><tr><td colspan="2" class="text_b_w">To Do List</td></tr><tr><td class="text_b_w" style="width:50%">User Name</td><td class="text_b_w" style="width:50%">Count</td></tr>';
		foreach($toDoArr as $toDokey => $toDoval){
			$toDoUserName = $providerNameArr[$toDokey];
			$toDohtmlsummary .= '<tr><td>'.$toDoUserName.'</td> <td>'.count($toDoval).'</td></tr>';
		}
		$csvpdfData .= $toDohtmlsummary .= '</table>';
	}
}

if($csvpdfData){
	$printFile = 1;
	$op='l';
	$cssHTML =  '<style>' . file_get_contents('../css/reports_pdf.css') . '</style>';
	$pdfData = $page_header_val.$cssHTML.$csvpdfData;
	$file_location = write_html($pdfData);
	echo $page_header_val.$csvpdfData;
}else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}		
?>	