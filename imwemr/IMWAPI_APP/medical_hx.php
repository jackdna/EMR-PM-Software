<?php 
	
	$ignoreAuth = true;
	$_SESSION['patient'] = trim($_REQUEST['patientId']);
	include_once('../config/globals.php');
	
	include_once("../library/classes/audit_common_function.php");
	
	include_once($GLOBALS['srcdir']."/classes/medical_hx/ocular.class.php");
	include_once($GLOBALS['srcdir']."/classes/medical_hx/general_health.class.php");
	
	
	$accessToken = $_REQUEST['accessToken'];
	$sql = "SELECT `id`, `expire_date_time`
			FROM
				`fmh_iportal_api_token_log`
			WHERE
				`token`='".$accessToken."'";
	$resp = imw_query($sql);
	$count = imw_num_rows($resp);
	
	if( $resp && imw_num_rows($resp) === 1 )
	{
		$tokenData = imw_fetch_assoc($resp);
		
		$tokenExpireDateTime = strtotime($tokenData['expire_date_time']);
		
		if( $tokenExpireDateTime > time())
		{
			
	// for social history and familt hx
	
	$query = "select social.*, date_format(social.modified_on,'%c/%e/%y') as date,
									Date_Format(offered_cessation_counselling_date,'".get_sql_date_format()."') as offeredCessationCounsellingDate,
									Date_Format(smoke_start_date,'".get_sql_date_format()."') as smokeStartDate,
									Date_Format(smoke_end_date,'".get_sql_date_format()."') as smokeEndDate,
									time_format(social.modified_on,'%l:%i %p') as time,	users.fname, users.mname, users.lname
									from social_history as social left join users on users.id = social.modified_by
									where patient_id = '".$_SESSION['patient']."'";
									
	$sql = imw_query($query);
	$intSocialNumRow = imw_num_rows($sql);
	$result2 = imw_fetch_assoc($sql);
	//pre($result2);
	
	// end of social history and familt hx
	
	
	$genHealth 	= new GeneralHealth();			//Gen. Health
	$ocular 	= new Ocular();    				//Ocular
	
	$diabetes_values = explode('~|~',$genHealth->data['gen_medicine']['diabetes_values']);
	$pt_diabetes_values = explode(',',$diabetes_values[0]);
	$pt_rel_diabetes_values = explode(',',$diabetes_values[1]);
	//print_r($pt_diabetes_values);
	//echo "<br>";
	//print_r($pt_rel_diabetes_values);
	
	// ocular patient data
	$ocular_pt_data = $ocular->ocular_data['acya_p'];
	
	// ocular family data
	$ocular_pt_rel_data = $ocular->ocular_data['acra_p'];
	
	// GeneralHealth patient data
	$arthrities_sub_option = explode('~|~',$genHealth->data['gen_medicine']['sub_conditions_you']);
	$gen_pt_data_y = array();
	$gen_pt_data_n = array();
	
	//description of patient data
	$desc_bp = explode('~|~',$genHealth->data['gen_medicine']['desc_high_bp']);
	$desc_heart_problem = explode('~|~',$genHealth->data['gen_medicine']['desc_heart_problem']);
	$desc_arthrities = explode('~|~',$genHealth->data['gen_medicine']['desc_arthrities']);
	$desc_lung_problem = explode('~|~',$genHealth->data['gen_medicine']['desc_lung_problem']);
	$desc_stroke = explode('~|~',$genHealth->data['gen_medicine']['desc_stroke']);
	$desc_thyroid_problems = explode('~|~',$genHealth->data['gen_medicine']['desc_thyroid_problems']);
	$desc_ulcers = explode('~|~',$genHealth->data['gen_medicine']['desc_ulcers']);
	$desc_cancer = explode('~|~',$genHealth->data['gen_medicine']['desc_cancer']);
	$desc_LDL = explode('~|~',$genHealth->data['gen_medicine']['desc_LDL']);
	$desc_dia_u = explode('~|~',$genHealth->data['gen_medicine']['desc_u']);
	// end of description data
	
	$genhealth_pt_array = array(1=>'High Blood Pressure',2=>'Heart Problem',7=>'Arthritis',4=>'Lung Problems', 5=>'Stroke',6=>'Thyroid Problems',3=>'Diabetes',13=>'LDL',8=>'Ulcers',14=>'Cancer');
	
	$genhealth_pt_desc = array('1'=>$desc_bp[0],'2'=>$desc_heart_problem[0],7=>$desc_arthrities[0],4=>$desc_lung_problem[0], 5=>$desc_stroke[0],6=>$desc_thyroid_problems[0],3=>$desc_dia_u[0],13=>$desc_LDL[0],8=>$desc_ulcers[0],14=>$desc_cancer[0]);
	
	$genhealth_pt_rel_desc = array('1'=>$desc_bp[1],'2'=>$desc_heart_problem[1],7=>$desc_arthrities[1],4=>$desc_lung_problem[1], 5=>$desc_stroke[1],6=>$desc_thyroid_problems[1],3=>$desc_dia_u[1],13=>$desc_LDL[1],8=>$desc_ulcers[1],14=>$desc_cancer[1]);
	
	$uc_pt_array = array(1=>'High Blood Pressure',2=>'Heart Problem',3=>'Arthritis',4=>'Lung Problems', 5=>'Stroke',6=>'Thyroid Problems',7=>'Diabetes',8=>'LDL',9=>'Ulcers',10=>'Other',11=>'Cancer');
	
	$uc_values = $genHealth->data['is_checked_under_control'];
	$uc_data = array();
	foreach($uc_pt_array as $key => $value){
		if(array_key_exists($key,$uc_values)){
			$uc_data[] = $value;
		}
	}
	
	$i=0;
	foreach($genhealth_pt_array as $key => $value){
		if(array_key_exists($key,$genHealth->data['acya_p1'])){
			$gen_pt_data_y[$i]['status'] = 'Yes';
			$gen_pt_data_y[$i]['key'] = $key;
			$gen_pt_data_y[$i]['data'] = $value;
			$gen_pt_data_y[$i]['desc'] = $genhealth_pt_desc[$key];
			if(in_array($value,$uc_data)){
				$gen_pt_data_y[$i]['uc_status'] = 1;
			}
			else{
				$gen_pt_data_y[$i]['uc_status'] = 0;
			}
		}
	$i++;
	}
	
	$i=0;
	foreach($genhealth_pt_array as $key => $value){
		if(array_key_exists($key,$genHealth->data['arrAnyConditionsYouN'])){
			$gen_pt_data_n[$i]['status'] = 'No';
			$gen_pt_data_n[$i]['key'] = $key;
			$gen_pt_data_n[$i]['data'] = $value;
			$gen_pt_data_n[$i]['desc'] = $genhealth_pt_desc[$key];
			$gen_pt_data_n[$i]['uc_status'] = 0;
			
		}
	$i++;
	}
	
	$gen_pt_data = array_merge($gen_pt_data_y,$gen_pt_data_n);
	$other_desc = explode('~|~',$genHealth->data['gen_medicine']['any_conditions_others']); 
	$other_y = preg_match("/1/", $genHealth->data['gen_medicine']['any_conditions_others_both']);
	$other_n = $genHealth->data['gen_medicine']['any_conditions_others_n'];
	
	$other_rel_y = preg_match("/2/", $genHealth->data['gen_medicine']['any_conditions_others_both']);
	$other_rel_n = $genHealth->data['gen_medicine']['any_conditions_others_rel_n'];
	// Genhealth patient relation data
	
	// relation members
	$desc_bp_rel = $genHealth->data['gen_medicine']['relDescHighBp'];
	$desc_heart_problem_rel = $genHealth->data['gen_medicine']['relDescHeartProb'];
	$desc_arthrities_rel = $genHealth->data['gen_medicine']['relDescArthritisProb'];
	$desc_lung_problem_rel = $genHealth->data['gen_medicine']['relDescLungProb'];
	$desc_stroke_rel = $genHealth->data['gen_medicine']['relDescStrokeProb'];
	$desc_thyroid_problems_rel = $genHealth->data['gen_medicine']['relDescThyroidProb'];
	$desc_ulcers_rel = $genHealth->data['gen_medicine']['relDescUlcersProb'];
	$desc_cancer_rel = $genHealth->data['gen_medicine']['relDescCancerProb'];
	$desc_LDL_rel = $genHealth->data['gen_medicine']['relDescLDL'];
	$desc_dia_u_rel = $genHealth->data['gen_medicine']['desc_r'];
	// end members
	
	$genhealth_pt_rel_member = array('1'=>$desc_bp_rel,'2'=>$desc_heart_problem_rel,7=>$desc_arthrities_rel,4=>$desc_lung_problem_rel, 5=>$desc_stroke_rel,6=>$desc_thyroid_problems_rel,3=>$desc_dia_u_rel,13=>$desc_LDL_rel,8=>$desc_ulcers_rel,14=>$desc_cancer_rel);
	
	$gen_pt_rel_data_y = array();
	$gen_pt_rel_data_n = array();
	
	$pt_rel = explode(',',$genHealth->data['gen_medicine']['any_conditions_relative']);
	$filter_pt_rel = array_filter($pt_rel);
	$pt_rel_data_y = array_flip($filter_pt_rel);
	//print_r($pt_rel_data_y);
	$pt_rel_data_n = $genHealth->data['arrAnyConditionsRelativeN'];
	//print_r($pt_rel_data_n);
	
	
	$i=0;
	foreach($genhealth_pt_array as $key => $value){
		if(array_key_exists($key,$pt_rel_data_y)){
			$gen_pt_rel_data_y[$i]['status'] = 'Yes';
			$gen_pt_rel_data_y[$i]['key'] = $key;
			$gen_pt_rel_data_y[$i]['data'] = $value;
			$gen_pt_rel_data_y[$i]['desc'] = $genhealth_pt_rel_desc[$key];
			$gen_pt_rel_data_y[$i]['member'] = $genhealth_pt_rel_member[$key];
			
		}
	$i++;
	}
	
	$i=0;
	foreach($genhealth_pt_array as $key => $value){
		if(array_key_exists($key,$genHealth->data['arrAnyConditionsRelativeN'])){
			$gen_pt_rel_data_n[$i]['status'] = 'No';
			$gen_pt_rel_data_n[$i]['key'] = $key;
			$gen_pt_rel_data_n[$i]['data'] = $value;
			$gen_pt_rel_data_n[$i]['desc'] = $genhealth_pt_rel_desc[$key];
			$gen_pt_rel_data_n[$i]['member'] = $genhealth_pt_rel_member[$key];
		}
	$i++;
	}
	//pre($genHealth);
	$gen_pt_rel_data = array_merge($gen_pt_rel_data_y,$gen_pt_rel_data_n);
	//print_r($gen_pt_data);
	//$select = mysql_query("select fname from patient_data where id =1"); 
	//$query = mysql_fetch_assoc($select);
	//print_r($query);
	//die();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Medical HX</title>

    <link href="custom.css" rel="stylesheet">

  
  </head>
  <body>
 <div class="insurbox">
<h2>Eye History</h2>
<div class="clearfix"></div> 	
<table width="100%" cellpadding="0" cellspacing="0" class="grybox" >
<tr>
<?php 
if($ocular->ocular_data['you_wear'] == 0){
	$Eyewear_data = 'None';	
}
else if($ocular->ocular_data['you_wear'] == 1){
	$Eyewear_data = 'Glasses';
}
else if($ocular->ocular_data['you_wear'] == 2){
	$Eyewear_data = 'Contact Lenses';
}
else {
	$Eyewear_data = 'Glasses And Contact Lenses';
}
?>
<td width="50%"><strong>Eyewear</strong></td>
<td width="50%"  class="histhed"><?php echo $Eyewear_data?></td>	
</tr>	 
</table> 
 <div class="clearfix"></div>
<div class="makebox">
    <div class="head">Patient Presently or Past problems</div>
	 <table>
 
  <thead>
    <tr>
      <th scope="col"><strong>Problem </strong></th>
      <th scope="col"><strong>Description</strong></th>
      </tr>
  </thead>
  <tbody>
  <?php 
  $ocular_pt_array = array(1=>'Dry Eyes', 2=>'Macular Degeneration', 3=>'Glaucoma', 4=>'Retinal Detachment', 5=>'Cataracts', 6=>'Keratoconus');
	
		foreach($ocular_pt_data as $key => $value){
  				if(array_key_exists($key,$ocular_pt_array)){
	?>
    	<tr>
      	<td data-label="Problem"><?php echo $ocular_pt_array[$key];?></td>
      	<td data-label="Description"><?php echo $ocular->ocular_data['elem_chronicDesc_'.$key];?></td>
      	</tr>
	  <?php 
	  	} 		}
	  	if($ocular->ocular_data['aco_u_checked'] == 'checked'){ ?>
   		<tr>
      	<td scope="row" data-label="Problem"><?php echo 'Other' ?></td>
      	<td data-label="Description"><?php echo $ocular->ocular_data['elem_chronicDesc_other'];?></td>
      	</tr>
	  <?php  
	  		}
	  	?>
    
  </tbody>
</table>	 	 	
	 	 	 	
	 	 	 	</div>	 
<div class="clearfix"></div>
<div class="makebox">
    <div class="head">Patient Family or Relative problems</div>
	 <table>
  
  <thead>
    <tr>
      <th scope="col"><strong>Problem</strong></th>
      <th scope="col"><strong>Family Member</strong></th>
      <th scope="col"><strong>Description</strong></th>
      </tr>
  </thead>
  <tbody>
  	<?php 	
				foreach($ocular_pt_rel_data as $key => $value){
  					if(array_key_exists($key,$ocular_pt_array)){
					 ?>
    <tr>
      <td data-label="Problem"><?php echo $ocular_pt_array[$key];?></td>
      <td data-label="Family Member"><?php echo $ocular->ocular_data['elem_chronicRelative_'.$key];?></td>
      <td data-label="Description"><?php echo $ocular->ocular_data['rel_elem_chronicDesc_'.$key];?></td>
      </tr>
	  <?php }} if($ocular->ocular_data['aco_relative_checked'] == 'checked'){ ?>
    <tr>
      <td scope="row" data-label="Problem"><?php echo 'Other' ?></td>
      <td data-label="Family Member"><?php echo $ocular->ocular_data['elem_chronicRelative_other']; ?></td>
      <td data-label="Description"><?php echo $ocular->ocular_data['rel_elem_chronicDesc_other']; ?></td>
      </tr>
	   <?php  }  ?>
      <!--<tr>
      <td scope="row" data-label="Problem">No Data Available</td>
      <td data-label="Family Member"></td>
      <td data-label="Description"></td>
      </tr>-->
	  <?php ?>
      <!--<tr>
      <td scope="row" data-label="Problem">Macular Degeneration</td>
      <td data-label="Family Member">Brother, Daughter, Father, Mother, Sister, Son</td>
      <td data-label="Description">sfdsdfksdhfsjdfhs</td>
      </tr>
      <tr>
      <td scope="row" data-label="Problem">Glaucoma</td>
      <td data-label="Family Member">Brother, Daughter, Father, Mother, Sister, Son</td>
      <td data-label="Description">sfdsdfksdhfsjdfhs</td>
      </tr>
      <tr>
      <td scope="row" data-label="Problem">Retinal Detachment</td>
      <td data-label="Family Member">Brother, Daughter, Father, Mother, Sister, Son</td>
      <td data-label="Description">sfdsdfksdhfsjdfhs</td>
      </tr>-->
       
       
       
       
      
    
  </tbody>
</table>	 	 	
	 	 	 	
   </div>
<div class="clearfix"></div>
<table width="100%" cellpadding="0" cellspacing="0" class="grybox" >
  <tr>
    <td width="50%"><strong>Advance Directive</strong></td>
    <td width="50%"  class="histhed"><?php echo $genHealth->data['gen_medicine']['ptAdoOption'] ; ?></td>
  </tr>
</table>


 </div>
  <div class="clearfix"></div>
<div class="insurbox2">
	  <h2>Medical Problems</h2>
	  <div class="clearfix"></div>
<div class="makebox">
    <div class="head">Patient Presently or Past problems</div>
	 <table>

  <thead>
    <tr>
      <th width="12%" align="center" scope="col"><strong>Status</strong></th>
      <th width="43%" scope="col"><strong>Problem</strong></th>
      <th width="45%" scope="col"><strong>Description</strong></th>
      </tr>
  </thead>
  <tbody>
  <?php 
	/*if(!empty($gen_pt_data) || $other_y == '1' || $other_n == 1){*/
  		foreach($gen_pt_data as $key => $value){
			
  			if($value['key']!=7 && $value['status'] == 'Yes' && $value['key']!=3){
				
			?>
				<tr>
				<td align="center" data-label="Status"><img src="img/check.png" alt="" align="absmiddle" /></td>
				<td data-label="Problem">
				<?php echo  $value['data']; if($value['uc_status']!=0){ ?>
				<img src="img/check.png" alt="" align="absmiddle" /> UC 
				<?php } ?></td>
				<td data-label="Description"><?php echo $value['desc']; ?> </td>
				</tr>
	<?php }
		if($value['key'] == 7 ){
		 
			$sub_arth_opt_1 = strpos($arthrities_sub_option[0],'1');
			$sub_arth_opt_2 = strpos($arthrities_sub_option[0],'2');
		?>
			<tr>
      		<td align="center" scope="row" data-label="Status">
			<?php if ($value['status']=='Yes'){?>
			<img src="img/check.png" alt="" align="absmiddle" />
			<?php } if ($value['status']=='No'){?> 
			<img src="img/close.png" alt="" align="absmiddle" />
			<?php } ?></td>
      		<td data-label="Problem">Arthritis 
			<?php
			 if($sub_arth_opt_1 != ""){ ?>
			 <img src="img/check.png" alt="" align="absmiddle" />  RA 
			 <?php } if($sub_arth_opt_2 != ""){?>
			 <img src="img/check.png" alt="" align="absmiddle" /> OA
			 <?php } if($value['uc_status']!=0){ ?> 
			 <img src="img/check.png" alt="" align="absmiddle" /> UC 
			 <?php } ?></td>
      		<td data-label="Description"><?php echo $value['desc']; ?></td>
     		 </tr>  
			<?php }
	
	 	if($value['key']==3){?>
    
     		 <tr>
     		<td align="center" scope="row" data-label="Status">
			<?php if ($value['status']=='Yes'){?>
			<img src="img/check.png" alt="" align="absmiddle" />
			<?php } if ($value['status']=='No'){?> 
			<img src="img/close.png" alt="" align="absmiddle" />
			<?php } ?></td>
			
      		<td data-label="Problem">Diabetes
	  		<?php 
			
			foreach($pt_diabetes_values as $diavalues){
					if(trim($diavalues) == 'Diet'){?>
	 				<img src="img/check.png" alt="" align="absmiddle" />  Diet
			 <?php } if(trim($diavalues) == 'NIDDM'){ ?>
			 <img src="img/check.png" alt="" align="absmiddle" /> NIDDM
			 <?php } if(trim($diavalues) == 'IDDM'){?>
			 <img src="img/check.png" alt="" align="absmiddle" /> IDDM 
			 <?php }} if($value['uc_status']!=0){?>
			 <img src="img/check.png" alt="" align="absmiddle" /> UC 
			 <?php }  ?>
			 </td>
      <td data-label="Description"><?php echo $value['desc']; ?> </td>
      </tr>
	  
      <?php } if($value['key']!=7 && $value['status'] == 'No' && $value['key']!=3){?> 
	   
     <tr>
      <td align="center" scope="row" data-label="Status"><img src="img/close.png" alt="" align="absmiddle" /></td>
      <td data-label="Problem"><?php echo  $value['data'];?></td>
      <td data-label="Description"><?php echo $value['desc']; ?></td>
      </tr>  
      
    <?php } } 
		if($other_y == 1 || $other_n == 1){?>
	
	 <tr>
      <td align="center" scope="row" data-label="Status">
	  <?php if($other_y == 1){ ?>
	  <img src="img/check.png" alt="" align="absmiddle" /> 
	  <?php } if($other_n == 1){?> 
	  <img src="img/close.png" alt="" align="absmiddle" /> 
	  <?php } ?>
	  </td>
      <td data-label="Problem">Other 
	  <?php if(array_key_exists('10',$genHealth->data['is_checked_under_control'])){?>
	  <img src="img/check.png" alt="" align="absmiddle" /> UC 
	  <?php } ?>
	   </td>
      <td data-label="Description"><?php echo $other_desc[0];?></td>
      </tr>  
	  <?php } ?>
	<?php /*}} else{*/?> 
	
	 <!--<tr>
      <td align="center" scope="row" data-label="Status">No Data Available</td>
      <td data-label="Problem"></td>
      <td data-label="Description"></td>
      </tr>  -->
	<?php //}?>
  </tbody>
</table>	 	 	
	 	 	 	
  </div>	
    <div class="clearfix"></div>
<div class="makebox">
    <div class="head">Problems your family/blood relative have presently or have had in the past</div>
	 <table>

  <thead>
    <tr>
      <th scope="col"><strong>Status</strong></th>
      <th scope="col"><strong>Problem</strong></th>
      <th scope="col"><strong>Family</strong></th>
       <th scope="col"><strong>Description</strong></th>
      </tr>
  </thead>
  <tbody>
  
  <?php 
  		/*if(!empty($gen_pt_rel_data) || $other_rel_y == '1' || $other_rel_n == 1){*/
		foreach($gen_pt_rel_data as $key => $value){
				if($value['key']!=7 && $value['status'] == 'Yes' && $value['key']!=3){
  ?>
    <tr>
      <td data-label="Status"><img src="img/check.png" alt="" align="absmiddle" /></td>
      <td data-label="Problem"><?php echo $value['data'];?></td>
      <td data-label="Family"><?php echo $value['member'];?></td>
      <td data-label="Description"><?php echo $value['desc'];?></td>
    </tr>
	  
	<?php } if($value['key'] == 7){
	
			$sub_arth_opt_1 = strpos($arthrities_sub_option[1],'1');
			$sub_arth_opt_2 = strpos($arthrities_sub_option[1],'2');
		?>
			<tr>
      		<td align="center" scope="row" data-label="Status">
			<?php if ($value['status']=='Yes'){?>
			<img src="img/check.png" alt="" align="absmiddle" />
			<?php } if ($value['status']=='No'){?> 
			<img src="img/close.png" alt="" align="absmiddle" />
			<?php } ?></td>
      		<td data-label="Problem">Arthritis 
			<?php
			 if($sub_arth_opt_1 != ""){ ?>
			 <img src="img/check.png" alt="" align="absmiddle" />  RA 
			 <?php } if($sub_arth_opt_2 != ""){?>
			 <img src="img/check.png" alt="" align="absmiddle" /> OA
			 <?php } ?></td>
			<td data-label="Family"><?php echo $value['member'];?></td>
      		<td data-label="Description"><?php echo $value['desc'];?></td>
     		 </tr>  
				
<?php	}

		if($value['key']==3){ ?>
    	
     		 <tr>
     		<td align="center" scope="row" data-label="Status">
			<?php if ($value['status']=='Yes'){?>
			<img src="img/check.png" alt="" align="absmiddle" />
			<?php } if ($value['status']=='No'){?> 
			<img src="img/close.png" alt="" align="absmiddle" />
			<?php } ?></td>
			
      		<td data-label="Problem">Diabetes
			<?php foreach($pt_rel_diabetes_values as $diavalues){
	  		if(trim($diavalues) == 'Diet'){?>
	 				<img src="img/check.png" alt="" align="absmiddle" />  Diet
			 <?php } if(trim($diavalues) == 'NIDDM'){ ?>
			 <img src="img/check.png" alt="" align="absmiddle" /> NIDDM
			 <?php } if(trim($diavalues) == 'IDDM'){?>
			 <img src="img/check.png" alt="" align="absmiddle" /> IDDM 
			 <?php } }?>
			 </td>
	  <td data-label="Family"><?php echo $value['member'];?></td>
      <td data-label="Description"><?php echo $value['desc']; ?> </td>
      </tr>
			
	<?php } if($value['key']!=7 && $value['status'] == 'No' && $value['key']!=3){ ?>
    <tr>
      <td scope="row" data-label="Status"><img src="img/close.png" alt="" align="absmiddle" /></td>
      <td data-label="Problem"><?php echo  $value['data'];?></td>
      <td data-label="Family"><?php echo $value['member'];?></td>
      <td data-label="Description"><?php echo $value['desc']; ?></td>
    </tr>
     <?php } } if($other_rel_y == 1 || $other_rel_n == 1){?>
	
	 <tr>
      <td align="center" scope="row" data-label="Status">
	  <?php if($other_rel_y == 1){ ?>
	  <img src="img/check.png" alt="" align="absmiddle" /> 
	  <?php } if($other_rel_n == 1){?> 
	  <img src="img/close.png" alt="" align="absmiddle" /> 
	  <?php } ?>
	  </td>
      <td data-label="Problem">Other </td>
	  <td data-label="Family"><?php echo $genHealth->data['gen_medicine']['ghRelDescOthers'];?></td>
      <td data-label="Description"><?php echo $other_desc[1];?></td>
      </tr>  
	  <?php } ?>
	<?php //}} else {?>
       <!--tr>
      <td data-label="Status">No Data Available</td>
      <td data-label="Problem"></td>
      <td data-label="Family"></td>
      <td data-label="Description"></td>
    </tr>-->
	<?php //} ?>
       
      
    
  </tbody>
</table>	 	 	
	 	 	 	
  </div>	
  <div class="clearfix"></div>	  
	  <div class="insurbox">
<h2>Review of Systems</h2>
<div class="clearfix"></div> 	
<table width="100%" cellpadding="0" cellspacing="0" class="grybox" >

</table> 
 <div class="clearfix"></div>
<div class="makebox">
    <div class="head">
      <div>Allergic/Immunologic &amp; Blood/ Lymphatic</div>
    </div>
	 <table>

  <thead>
   <tr>
      <th width="12%" align="center" scope="row">Status</th>
      <th width="88%"><strong>Medical Conditions</strong></th>
      <!--<th data-label="Description"><strong>Description</strong></th>-->
      </tr>
  </thead>
  <tbody>
		<?php $review_aller_others = trim($genHealth->data['gen_medicine']['review_aller_others']); 
		/*if(array_key_exists(7,$genHealth->data['negChkBx']) || !empty($genHealth->data['gen_medicine']['review_aller']) || !empty($review_aller_others)){*/ 
				
				if(array_key_exists(7,$genHealth->data['negChkBx'])){?>
				 <tr>
				 <td width="12%" align="center" scope="row" data-label="Status">
				 <img src="img/check.png" alt="" align="absmiddle" />
				 </td>
				 <td width="88%" data-label="Medical Condition">Negative</td>
				 </tr>
			<?php } if(array_key_exists(1,$genHealth->data['review_aller'])){?>
			
				 <tr>
				 <td  align="center" scope="row" data-label="Status">
				 <img src="img/check.png" alt="" align="absmiddle" />
				 </td>
				 <td  data-label="Medical Condition">Seasonal Allergies</td>
				 </tr>
			<?php } if(array_key_exists(2,$genHealth->data['review_aller'])){?>
		 
				 <tr>
				 <td  align="center" scope="row" data-label="Status">
				 <img src="img/check.png" alt="" align="absmiddle" />
				 </td>
				 <td  data-label="Medical Condition"> Hay Fever</td>
				 </tr>
		   <?php } 
		   		if(!empty($review_aller_others)){?>
				 <tr>
				 <td  align="center" scope="row" data-label="Status">
				 <img src="img/check.png" alt="" align="absmiddle" />
				 </td>
				 <td  data-label="Medical Condition"> <?php echo "Others ,".$review_aller_others ;?></td>
				 </tr>
	   <?php } //} else {?>
	   			<!--<tr>
				<td width="12%" align="center" data-label="Status">
				No Data Available
				</td>
				<td width="88%" data-label="Description"> </td>
				 </tr>-->
	   <?php //} ?>
       <!--<tr>
      <td scope="row" data-label="Problem"></td>
      <td data-label="Description"></td>
      </tr>-->
      
    
  </tbody>
</table>	 	 	
	 	 	 	
  </div>	
<div class="clearfix"></div>
<div class="makebox">
    <div class="head">
      <div>Cardiovascular</div>
    </div>
	 <table>

  <thead>
    <tr>
      <!--<th scope="col">Status</th>
      <th scope="col"><strong>Cardiovascular</strong></th>
      <th scope="col"><strong>Description</strong></th>-->
	  <th width="12%" align="center" scope="col">Status</th>
      <th width="88%"><strong>Medical Conditions</strong></th>
      </tr>
  </thead>
  <tbody>
	<?php $review_card_array = array(1=>'Chest Pain',2=>'Congestive Heart Failure',3=>'Irregular Rhythm');
		  $review_card_others = trim($genHealth->data['gen_medicine']['review_card_others']);
	/*if(array_key_exists(4,$genHealth->data['negChkBx']) || !empty($genHealth->data['gen_medicine']['review_card']) || !empty($review_card_others)){*/
	
		if(array_key_exists(4,$genHealth->data['negChkBx'])){?>
			  <tr>
			  <td  align="center" scope="col" data-label="Status">
			  <img src="img/check.png" alt="" align="absmiddle" /></td>
			  <td  data-label="Medical Condition">Negative</td>
			  </tr>
		<?php } foreach($review_card_array as $key => $value){
					if(array_key_exists($key,$genHealth->data['review_card'])){?>
		<tr>
		  <td  align="center" scope="col" data-label="Status">
		  <img src="img/check.png" alt="" align="absmiddle" /></td>
		  <td  data-label="Medical Condition"><?php echo $value; ?></td>
		  </tr>
		 <?php } } if(!empty($review_card_others)){?>
		  <tr>
			<td  align="center" scope="col" data-label="Status">
			<img src="img/check.png" alt="" align="absmiddle" /></td>
			<td  data-label="Medical Condition"> <?php echo "Others ,".$review_card_others?></td>
		 </tr>
	<?php } //} else {?>
      		<!--<tr>
				<td width="12%" align="center" data-label="Status">
				No Data Available
				</td>
				<td width="88%" data-label="Description"> </td>
			</tr>-->
    <?php //} ?>
  </tbody>
</table>	 	 	
	 	 	 	
  </div>	
 
 <div class="clearfix"></div>
<div class="makebox">
    <div class="head">
      <div>Constitutional & Integumentary</div>
    </div>
	 <table>

  <thead>
    <tr>
      <!--<th scope="col">Status</th>
      <th scope="col"><strong>Cardiovascular</strong></th>
      <th scope="col"><strong>Description</strong></th>-->
	  <th width="12%" align="center" scope="col">Status</th>
      <th width="88%"><strong>Medical Conditions</strong></th>
      </tr>
  </thead>
  <tbody>
	<?php $review_const_array = array(1=>'Fever',2=>'Weight Loss',3=>'Rash',4=>'Skin Disease');
		  $review_const_others = trim($genHealth->data['gen_medicine']['review_const_others']);
	/*if(array_key_exists(1,$genHealth->data['negChkBx']) || !empty($genHealth->data['gen_medicine']['review_const']) || !empty($review_const_others)){*/
	
		if(array_key_exists(1,$genHealth->data['negChkBx'])){?>
			  <tr>
			  <td  align="center" scope="col" data-label="Status">
			  <img src="img/check.png" alt="" align="absmiddle" /></td>
			  <td  data-label="Medical Condition">Negative</td>
			  </tr>
		<?php } foreach($review_const_array as $key => $value){
					if(array_key_exists($key,$genHealth->data['review_const'])){?>
		<tr>
		  <td  align="center" scope="col" data-label="Status">
		  <img src="img/check.png" alt="" align="absmiddle" /></td>
		  <td  data-label="Medical Condition"><?php echo $value; ?></td>
		  </tr>
		 <?php } } if(!empty($review_const_others)){?>
		  <tr>
			<td  align="center" scope="col" data-label="Status">
			<img src="img/check.png" alt="" align="absmiddle" /></td>
			<td data-label="Medical Condition"> <?php echo "Others ,".$review_const_others; ?></td>
		 </tr>
	<?php } //} else {?>
      		<!--<tr>
				<td width="12%" align="center" data-label="Status">
				No Data Available
				</td>
				<td width="88%" data-label="Description"> </td>
			</tr>-->
    <?php //} ?>
  </tbody>
</table>	 	 	
	 	 	 	
  </div>		
	
	<div class="clearfix"></div>
<div class="makebox">
    <div class="head">
      <div>Gastrointestinal</div>
    </div>
	 <table>

  <thead>
    <tr>
      <!--<th scope="col">Status</th>
      <th scope="col"><strong>Cardiovascular</strong></th>
      <th scope="col"><strong>Description</strong></th>-->
	  <th width="12%" align="center" scope="col">Status</th>
      <th width="88%"><strong>Medical Conditions</strong></th>
      </tr>
  </thead>
  <tbody>
	<?php $review_gastro_array = array(1=>'Vomiting',2=>'Ulcers',3=>'Diarrhea',4=>'Bloody Stools');
		  $review_gastro_others = trim($genHealth->data['gen_medicine']['review_gastro_others']);
	/*if(array_key_exists(5,$genHealth->data['negChkBx']) || !empty($genHealth->data['gen_medicine']['review_gastro']) || !empty($review_gastro_others)){*/
	
		if(array_key_exists(5,$genHealth->data['negChkBx'])){?>
			  <tr>
			  <td  align="center" scope="row" data-label="Status">
			  <img src="img/check.png" alt="" align="absmiddle" /></td>
			  <td  data-label="Medical Condition">Negative</td>
			  </tr>
		<?php } foreach($review_gastro_array as $key => $value){
					if(array_key_exists($key,$genHealth->data['review_gastro'])){?>
		<tr>
		  <td  align="center" scope="row" data-label="Status">
		  <img src="img/check.png" alt="" align="absmiddle" /></td>
		  <td data-label="Medical Condition"><?php echo $value; ?></td>
		  </tr>
		 <?php } } if(!empty($review_gastro_others)){?>
		  <tr>
			<td  align="center" scope="row" data-label="Status">
			<img src="img/check.png" alt="" align="absmiddle" /></td>
			<td  data-label="Medical Condition"> <?php echo "Others ,".$review_gastro_others; ?></td>
		 </tr>
	<?php } //} else {?>
      		<!--<tr>
				<td width="12%" align="center" data-label="Status">
				No Data Available
				</td>
				<td width="88%" data-label="Description"> </td>
			</tr>-->
    <?php //} ?>
  </tbody>
</table>	 	 	
	 	 	 	
  </div>		
	
	<div class="clearfix"></div>
<div class="makebox">
    <div class="head">
      <div>Genitourinary</div>
    </div>
	 <table>

  <thead>
    <tr>
      <!--<th scope="col">Status</th>
      <th scope="col"><strong>Cardiovascular</strong></th>
      <th scope="col"><strong>Description</strong></th>-->
	  <th width="12%" align="center" scope="col">Status</th>
      <th width="88%"><strong>Medical Conditions</strong></th>
      </tr>
  </thead>
  <tbody>
	<?php $review_genit_array = array(1=>'Genital Ulcers',2=>'Discharge',3=>'Kidney Stones',4=>'Blood in Urine');
		  $review_genit_others = trim($genHealth->data['gen_medicine']['review_genit_others']);
	/*if(array_key_exists(6,$genHealth->data['negChkBx']) || !empty($genHealth->data['gen_medicine']['review_genit']) || !empty($review_genit_others)){*/
	
		if(array_key_exists(6,$genHealth->data['negChkBx'])){?>
			  <tr>
			  <td  align="center" scope="col" data-label="Status">
			  <img src="img/check.png" alt="" align="absmiddle" /></td>
			  <td  data-label="Medical Condition">Negative</td>
			  </tr>
		<?php } foreach($review_genit_array as $key => $value){
					if(array_key_exists($key,$genHealth->data['review_genit'])){?>
		<tr>
		  <td  align="center" scope="col" data-label="Status">
		  <img src="img/check.png" alt="" align="absmiddle" /></td>
		  <td  data-label="Medical Condition"><?php echo $value; ?></td>
		  </tr>
		 <?php } } if(!empty($review_genit_others)){?>
		  <tr>
			<td  align="center" scope="col" data-label="Status">
			<img src="img/check.png" alt="" align="absmiddle" /></td>
			<td  data-label="Medical Condition"> <?php echo "Others ,".$review_genit_others?></td>
		 </tr>
	<?php } //} else {?>
      		<!--<tr>
				<td width="12%" align="center" data-label="Status">
				No Data Available
				</td>
				<td width="88%" data-label="Description"> </td>
			</tr>-->
    <?php //} ?>
  </tbody>
</table>	 	 	
	 	 	 	
  </div>		
	
	<div class="clearfix"></div>
<div class="makebox">
    <div class="head">
      <div>Head / Neck</div>
    </div>
	 <table>

  <thead>
    <tr>
      <!--<th scope="col">Status</th>
      <th scope="col"><strong>Cardiovascular</strong></th>
      <th scope="col"><strong>Description</strong></th>-->
	  <th width="12%" align="center" scope="col">Status</th>
      <th width="88%"><strong>Medical Conditions</strong></th>
      </tr>
  </thead>
  <tbody>
	<?php 
		  $review_head_array = array(1=>'Sinus Problems',2=>'Post Nasal Drip',3=>'Runny Nose',4=>'Dry Mouth',5=>'Hearing Loss');
		  $review_head_others = trim($genHealth->data['gen_medicine']['review_head_others']);
	/*if(array_key_exists(2,$genHealth->data['negChkBx']) || !empty($genHealth->data['gen_medicine']['review_head']) || !empty($review_head_others)){*/
	
		if(array_key_exists(2,$genHealth->data['negChkBx'])){?>
			  <tr>
			  <td  align="center" scope="col" data-label="Status">
			  <img src="img/check.png" alt="" align="absmiddle" /></td>
			  <td  data-label="Medical Condition">Negative</td>
			  </tr>
		<?php } foreach($review_head_array as $key => $value){
					if(array_key_exists($key,$genHealth->data['review_head'])){?>
		<tr>
		  <td  align="center" scope="col" data-label="Status">
		  <img src="img/check.png" alt="" align="absmiddle" /></td>
		  <td  data-label="Medical Condition"><?php echo $value; ?></td>
		  </tr>
		 <?php } } if(!empty($review_head_others)){?>
		  <tr>
			<td  align="center" scope="col" data-label="Status">
			<img src="img/check.png" alt="" align="absmiddle" /></td>
			<td  data-label="Medical Condition"> <?php echo "Others ,".$review_head_others?></td>
		 </tr>
	<?php } //} else {?>
      		<!--<tr>
				<td width="12%" align="center" data-label="Status">
				No Data Available
				</td>
				<td width="88%" data-label="Description"> </td>
			</tr>-->
    <?php //} ?>
  </tbody>
</table>	 	 	
	 	 	 	
  </div>		
	 
	 <div class="clearfix"></div>
<div class="makebox">
    <div class="head">
      <div>Neurological Psychiatry & Musculoskeletal</div>
    </div>
	 <table>

  <thead>
    <tr>
      <!--<th scope="col">Status</th>
      <th scope="col"><strong>Cardiovascular</strong></th>
      <th scope="col"><strong>Description</strong></th>-->
	  <th width="12%" align="center" scope="col">Status</th>
      <th width="88%"><strong>Medical Conditions</strong></th>
      </tr>
  </thead>
  <tbody>
	<?php 
		  $review_neuro_array = array(1=>'Headache',2=>'Migraines',3=>'Paralysis Fever',4=>'Joint Ache');
		  $review_neuro_others = trim($genHealth->data['gen_medicine']['review_neuro_others']);
	/*if(array_key_exists(8,$genHealth->data['negChkBx']) || !empty($genHealth->data['gen_medicine']['review_neuro']) || !empty($review_neuro_others)){
	*/
		if(array_key_exists(8,$genHealth->data['negChkBx'])){?>
			  <tr>
			  <td  align="center" scope="col" data-label="Status">
			  <img src="img/check.png" alt="" align="absmiddle" /></td>
			  <td  data-label="Medical Condition">Negative</td>
			  </tr>
		<?php } foreach($review_neuro_array as $key => $value){
					if(array_key_exists($key,$genHealth->data['review_neuro'])){?>
		<tr>
		  <td  align="center" scope="col" data-label="Status">
		  <img src="img/check.png" alt="" align="absmiddle" /></td>
		  <td  data-label="Medical Condition"><?php echo $value; ?></td>
		  </tr>
		 <?php } } if(!empty($review_neuro_others)){?>
		  <tr>
			<td  align="center" scope="col" data-label="Status">
			<img src="img/check.png" alt="" align="absmiddle" /></td>
			<td  data-label="Medical Condition"> <?php echo "Others ,".$review_neuro_others?></td>
		 </tr>
	<?php } //} else {?>
      		<!--<tr>
				<td width="12%" align="center" data-label="Status">
				No Data Available
				</td>
				<td width="88%" data-label="Description"> </td>
			</tr>-->
    <?php //} ?>
  </tbody>
</table>	 	 	
	 	 	 	
  </div>		
	 
	 <div class="clearfix"></div>
<div class="makebox">
    <div class="head">
      <div>Respiratory</div>
    </div>
	 <table>

  <thead>
    <tr>
      <!--<th scope="col">Status</th>
      <th scope="col"><strong>Cardiovascular</strong></th>
      <th scope="col"><strong>Description</strong></th>-->
	  <th width="12%" align="center" scope="col">Status</th>
      <th width="88%"><strong>Medical Conditions</strong></th>
      </tr>
  </thead>
  <tbody>
	<?php 
		  $review_resp_array = array(1=>'Cough',2=>'Bronchitis',3=>'Shortness of Breath',4=>'Asthma',5=>'Emphysema',6=>'COPD');
		  $review_resp_others = trim($genHealth->data['gen_medicine']['review_resp_others']);
	/*if(array_key_exists(3,$genHealth->data['negChkBx']) || !empty($genHealth->data['gen_medicine']['review_resp']) || !empty($review_resp_others)){*/
	
		if(array_key_exists(3,$genHealth->data['negChkBx'])){?>
			  <tr>
			  <td  align="center" scope="col" data-label="Status">
			  <img src="img/check.png" alt="" align="absmiddle" /></td>
			  <td  data-label="Medical Condition">Negative</td>
			  </tr>
		<?php } foreach($review_resp_array as $key => $value){
					if(array_key_exists($key,$genHealth->data['review_resp'])){?>
		<tr>
		  <td align="center" scope="col" data-label="Status">
		  <img src="img/check.png" alt="" align="absmiddle" /></td>
		  <td  data-label="Medical Condition"><?php echo $value; ?></td>
		  </tr>
		 <?php } } if(!empty($review_resp_others)){?>
		  <tr>
			<td  align="center" scope="col" data-label="Status">
			<img src="img/check.png" alt="" align="absmiddle" /></td>
			<td  data-label="Medical Condition"> <?php echo "Others ,".$review_resp_others?></td>
		 </tr>
	<?php } //} else {?>
      		<!--<tr>
				<td width="12%" align="center" data-label="Status">
				No Data Available
				</td>
				<td width="88%" data-label="Description"> </td>
			</tr>-->
    <?php // } ?>
  </tbody>
</table>	 	 	
	 	 	 	
  </div>		
	 	
 </div>
  <div class="clearfix"></div>  
	<div class="clearfix"></div>	 	
<div class="insurbox2">
	  <h2>Social History</h2>
	  <div class="clearfix"></div>
<div class="makebox subopt">
  
	 <table>

  <thead>
    <tr>
      <th scope="col">Smoke</th>
      <th scope="col">Snomed Code</th>
      <th scope="col">Type</th>
       <th scope="col">Frequency</th>
       <th scope="col">For</th>
      <th scope="col">Period</th>
      <th scope="col">Start Date</th>
       <th scope="col">End Date</th>
      </tr>
  </thead>
  <tbody>
  <?php $smoke = explode('/',$result2['smoking_status']);?>
    <tr>
      <td data-label="Smoke">&nbsp;<?php echo $smoke[0];?></td>
      <td data-label="SNOMED">&nbsp;<?php echo $smoke[1];?></td>
      <td data-label="Type">&nbsp;<?php echo $result2['source_of_smoke'];?></td>
      <td data-label="Frequency">&nbsp;<?php echo $result2['smoke_perday'];?></td>
      <td data-label="For">&nbsp;<?php echo $result2['number_of_years_with_smoke'];?></td>
      <td data-label="Period">&nbsp;<?php echo $result2['smoke_years_months'];?></td>
      <td data-label="Start Date">&nbsp;<?php echo $result2['smokeStartDate'];?></td>
      <td data-label="End Date">&nbsp;<?php echo $result2['smokeEndDate'];?></td>
    </tr>
    
     
       
       
      
    
  </tbody>
</table>	 	 	
	 	 	 	
  </div>	
    <div class="clearfix"></div>
	  
  </div>	
  	<div class="clearfix"></div>	 	
<div class="insurbox">
	  <h2>Family Hx of Smoking</h2>
	  <div class="clearfix"></div>
<div class="makebox subopt">
  
	 <table>

  <thead>
    <tr>
      <th scope="col">Smoking</th>
      <th scope="col">Relation</th>
      <th scope="col">Description</th>
       </tr>
  </thead>
  <tbody>
  	
    <tr>
      <td data-label="Status">
	  <?php if($result2['family_smoke']!=0){ ?>
	  <img src="img/check.png" alt="" align="absmiddle" />
	  <?php } ?>&nbsp;</td>
      <td data-label="Relation">&nbsp;<?php echo $result2['smokers_in_relatives'];?></td>
      <td data-label="Description">&nbsp;<?php echo $result2['smoke_description'];?></td>
      </tr>
   
  </tbody>
</table>	 	 	
	 	 	 	
  </div>	
    <div class="clearfix"></div>
	  
  </div> 	
  <div class="clearfix"></div>	 	
<div class="insurbox2">
	  <h2>Alcohol</h2>
	  <div class="clearfix"></div>
<div class="makebox subopt">
  
	 <table>

  <thead>
    <tr>
      <th scope="col">Alcohol</th>
      <th scope="col">Frequency</th>
      <th scope="col">List any Drugs</th>
       <th scope="col">More Information</th>
       </tr>
  </thead>
  <tbody>
    <tr>
      <td data-label="Alcohol">&nbsp;<?php echo $result2['alcohal']; ?></td>
      <td data-label="Frequency">&nbsp;<?php $time = trim($result2['alcohal_time']);
	  	if($time!=""){echo $result2['consumption'].','.$result2['alcohal_time'];}
		else{echo $result2['consumption'];} ?></td>
      <td data-label="List any Drugs">&nbsp;<?php echo $result2['list_drugs']; ?></td>
      <td data-label="More Information">&nbsp;<?php echo $result2['otherSocial']; ?></td>
      </tr>
    
  </tbody>
</table>	 	 	
	 	 	 	
  </div>	
    <div class="clearfix"></div>
	  
  </div>	
  	<div class="clearfix"></div>	   
  </div>

  

</body>
</html>

<?php } else{
				echo "Invalid Token";
}} else{ echo "Something Went Wrong";}?>