<?php 

require_once "../interface/globals.php";
require_once "inc_classes/chart_cc_hx_ocular.php";
require_once "inc_classes/GetWVSummery.php";
require_once "inc_classes/VisionData.php";
require_once "inc_classes/AssessPlan.php";
require_once "../interface/chart_notes/common/ChartNote.php";
require_once "inc_classes/GetSuperBill.php";
$authId = $_REQUEST['phyId'];
$serviceObj_hx = new chart_cc_hx_ocular($_REQUEST['patId'],$_REQUEST['form_id']);
$serviceObj_vision = new VisionData($_REQUEST['patId'],$_REQUEST['form_id']);
$serviceObj_wv = new GetWVSummery($_REQUEST['patId'],$_REQUEST['form_id']);
$serviceObj_assess = new AssessPlan($_REQUEST['patId'],$_REQUEST['form_id']);
$serviceObj_final = new ChartNote($_REQUEST['patId'],$_REQUEST['form_id']);

//$serviceObj->reqModule = $reqModule;

$var=array();

$var["work_view"]["ocular_hx"] = $serviceObj_hx->get_cc_hx_ocular();
$var["work_view"]["vision_data"] = $serviceObj_vision->getVision_app();
$var["work_view"]["getWV_summary"] = $serviceObj_wv->getWVSummery_app();
$var["work_view"]["get_assess_plan"] = $serviceObj_assess->getAssessPlan();
$var["work_view"]["getWV_drawing"] = $serviceObj_wv->getWVDrawing();
//$filter = count($var["work_view"]["getWV_drawing"]);

foreach($var["work_view"]["getWV_drawing"] as $val){
 $temp = explode("/",$val["url"]);
 $temp1 = explode("_",$temp[10]);
 $draw[] =$temp1[0]; 

}
if(isset($_POST['submit'])){
	$serviceObj_final->finalizeChartNote();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Template</title>
<!-- Bootstrap -->
<link href="html/css/custom.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body onLoad="show()">
<div class="tophead">
  <button class="btn btn-default" type="submit">Poor View : 
  <figure><?php if($var["work_view"]["ocular_hx"]["PoorView_Phthisis_Prosthesis"]=="Poor View"){echo "YES";} else echo "NO"; ?></figure>
  </button>
  <button class="btn btn-default" type="submit">Phthisis : 
  <figure><?php if($var["work_view"]["ocular_hx"]["PoorView_Phthisis_Prosthesis"]=="Phthisis"){echo "YES";} else echo "NO"; ?></figure>
  </button>
  <button class="btn btn-default" type="submit">Prosthesis : 
  <figure><?php if($var["work_view"]["ocular_hx"]["PoorView_Phthisis_Prosthesis"]=="Prosthesis"){echo "YES";} else echo "NO"; ?></figure>
  </button>
  
  <button class="btn btn-default" type="submit">Dominant : 
  <?php if($var["work_view"]["ocular_hx"]["Dominant"]){ ?><figure><?php echo $var["work_view"]["ocular_hx"]["Dominant"]; ?></figure><?php }?>
  </button>
  <button class="btn btn-default" type="submit">Color : 
   <?php if($var["work_view"]["ocular_hx"]["Color"]){ ?><figure><?php echo $var["work_view"]["ocular_hx"]["Color"]; ?></figure><?php }?>
  </button>
  <button class="btn btn-default" type="submit">Neuro / Psych : 
  <?php if($var["work_view"]["ocular_hx"]["Neuro/Psych"]){ ?><figure><?php echo $var["work_view"]["ocular_hx"]["Neuro/Psych"]; ?></figure><?php }?>
  </button>
</div>
<div class="clearfix"></div>
<div class="accordion">
  <dl>
 
    <dt> <a href="#accordion1" aria-expanded="false" aria-controls="accordion1" class="accordion-title accordionTitle js-accordionTrigger">Chief Complaint</a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion1" aria-hidden="true">
      <div class="accord_cont"><?php if($var["work_view"]["ocular_hx"]["chief_compliant"] == "")
	  {echo "The Old Patient";}
	  else{echo $var["work_view"]["ocular_hx"]["chief_compliant"]; }?></div>
    </dd>
	
	
	
    <dt> <a href="#accordion2" aria-expanded="false" aria-controls="accordion2" class="accordion-title accordionTitle js-accordionTrigger"> History Of Complaints</a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion2" aria-hidden="true">
      <div class="accord_cont">
	  <?php 
	  if($var["work_view"]["ocular_hx"]["cc_hx"] == ""){
	  echo "History Of A Patient";}
	  else{
		$str = $var["work_view"]["ocular_hx"]["cc_hx"];
		echo nl2br($str);
		}
		?>
      </div>
    </dd>
	
	<?php $count = count($var["work_view"]["ocular_hx"]["ocular_meds"]);
	if($count != 0){
	?>
    <dt> <a href="#accordion3" aria-expanded="false" aria-controls="accordion3" class="accordion-title accordionTitle js-accordionTrigger"> Ocular Medications </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion3" aria-hidden="true">
      <div class="accord_cont">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
		   <tr>
              <td align="center" width="34%"><strong>Ocular Name </strong></td>
              <td align="center" width="8%"><strong>Site</strong></td>
              <td align="center" width="25%"><strong>Sig.</strong></td>
              <td align="center" width="8%"><strong>Dosage</strong></td>
              <td align="center" width="25%"><strong>Comment</strong></td>
            </tr>
		   <?php foreach($var["work_view"]["ocular_hx"]["ocular_meds"] as $value) { 
		  ?>
		  
              <tr>
              <td width="34%">&nbsp;<strong><?php echo $value["title"]; ?></strong></td>
              <td align="center" width="8%">&nbsp;<?php echo $value["sites"]; ?></td>
             <td width="25%">&nbsp;<?php echo $value["sig"]; ?></td>
              <td align="center" width="8%">&nbsp;<?php echo $value["destination"]; ?></td>
              <td width="25%">&nbsp;<?php echo $value["med_comments"]; ?></td>
            </tr>
			<?php } ?>
          
            <!-- <tr>
              <td><strong>Lumigan</strong></td>
              <td align="center">OU</td>
              <td>Ghs</td>
              <td>0</td>
              <td>dssd</td>
            </tr>
            <tr>
              <td><strong>Lumigan</strong></td>
              <td align="center">OU</td>
              <td>Ghs</td>
              <td>0</td>
              <td>dssd</td>
            </tr>
            <tr>
              <td><strong>Lumigan</strong></td>
              <td align="center">OU</td>
              <td>Ghs</td>
              <td>0</td>
              <td>dssd</td>
            </tr>-->
          </table>
        </div>
      </div>
    </dd>
	<?php } ?>
    <dt> <a href="#accordion4" aria-expanded="false" aria-controls="accordion4" class="accordion-title accordionTitle js-accordionTrigger"> Vision </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion4" aria-hidden="true">
      <div class="accord_cont ">
        <div class="visionbx">
          <h2>Distance</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" ><?php foreach($var["work_view"]["vision_data"]["Distance"]["OD"] as $value){echo $value.'&nbsp;';}?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td ><?php foreach($var["work_view"]["vision_data"]["Distance"]["OS"] as $value){echo $value.'&nbsp;';}?></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;<strong><?php echo $var["work_view"]["vision_data"]["Distance"]["Desc"]; ?></strong></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="visionbx">
          <h2>Additional</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" ><?php foreach($var["work_view"]["vision_data"]["AdditionalAcuity"]["OD"] as $value){echo $value.'&nbsp;';} ?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td ><?php foreach($var["work_view"]["vision_data"]["AdditionalAcuity"]["OS"] as $value){echo $value.'&nbsp;';} ?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>&nbsp;<?php echo $var["work_view"]["vision_data"]["AdditionalAcuity"]["Desc"]; ?></strong></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="visionbx">
          <h2>Near</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" ><?php foreach($var["work_view"]["vision_data"]["Near"]["OD"] as $value){echo $value.'&nbsp;';} ?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td ><?php foreach($var["work_view"]["vision_data"]["Near"]["OD"] as $value){echo $value.'&nbsp;';} ?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>&nbsp;<?php echo $var["work_view"]["vision_data"]["Near"]["Desc"]; ?></strong></td>
              </tr>
            </table>
          </div>
        </div></div>
		 <div class="accord_cont">
		<?php $filter = array_filter($var["work_view"]["vision_data"]["PC"]["1"]["Initial"]["OD"]);
		      $filter_1 = array_filter($var["work_view"]["vision_data"]["PC"]["1"]["Initial"]["OS"]);
			  	$fill_1 = count($filter_1);
				$fill = count($filter);
				if($fill != 0 || $fill_1 != 0){
		?>
				
        <div class="visionbx">
          <h2>PC 1st</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" >&nbsp;<?php 
									$filter = array_filter($var["work_view"]["vision_data"]["PC"]["1"]["Initial"]["OD"]);
								foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td ><?php 
						$filter = array_filter($var["work_view"]["vision_data"]["PC"]["1"]["Initial"]["OS"]);
						foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;<strong><?php echo $var["work_view"]["vision_data"]["PC"]["1"]["Initial"]["Desc"];?></strong></td>
              </tr>
            </table>
          </div>
        </div>
		<?php } ?>
        <div class="visionbx">
          <h2>Over Refraction</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" >&nbsp;<?php 
											$filter = array_filter($var["work_view"]["vision_data"]["PC"]["1"]["Over Refraction"]["OD"]);
								foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td >&nbsp;<?php 
											$filter = array_filter($var["work_view"]["vision_data"]["PC"]["1"]["Over Refraction"]["OS"]);
								foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>&nbsp;</strong></td>
              </tr>
            </table>
          </div>
        </div></div>
		 <div class="accord_cont ">
		 <?php $filter = array_filter($var["work_view"]["vision_data"]["PC"]["2"]["Initial"]["OD"]);
		      $filter_1 = array_filter($var["work_view"]["vision_data"]["PC"]["2"]["Initial"]["OS"]);
			  	$fill_1 = count($filter_1);
				$fill = count($filter);
				if($fill != 0 || $fill_1 != 0){
		?>
		 <div class="visionbx">
          <h2>PC 2nd</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" ><?php 
						$filter = array_filter($var["work_view"]["vision_data"]["PC"]["2"]["Initial"]["OD"]);
						foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td ><?php 
						$filter = array_filter($var["work_view"]["vision_data"]["PC"]["2"]["Initial"]["OS"]);
						foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;<strong><?php echo $var["work_view"]["vision_data"]["PC"]["2"]["Initial"]["Desc"];?></strong></td>
              </tr>
            </table>
          </div>
        </div>
		<?php } ?>
        <div class="visionbx">
          <h2>Over Refraction</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" ><?php 
											$filter = array_filter($var["work_view"]["vision_data"]["PC"]["2"]["Over Refraction"]["OD"]);
								foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td ><?php 
											$filter = array_filter($var["work_view"]["vision_data"]["PC"]["2"]["Over Refraction"]["OS"]);
								foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>&nbsp;</strong></td>
              </tr>
            </table>
          </div>
        </div></div>
		<div class="accord_cont ">
		 <?php $filter = array_filter($var["work_view"]["vision_data"]["PC"]["3"]["Initial"]["OD"]);
		      $filter_1 = array_filter($var["work_view"]["vision_data"]["PC"]["3"]["Initial"]["OS"]);
			  	$fill_1 = count($filter_1);
				$fill = count($filter);
				if($fill != 0 || $fill_1 != 0){
		?>
		 <div class="visionbx">
          <h2>PC 3rd</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" ><?php 
						$filter = array_filter($var["work_view"]["vision_data"]["PC"]["3"]["Initial"]["OD"]);
						foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td ><?php 
						$filter = array_filter($var["work_view"]["vision_data"]["PC"]["3"]["Initial"]["OS"]);
						foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;<strong><?php echo $var["work_view"]["vision_data"]["PC"]["3"]["Initial"]["Desc"];?></strong></td>
              </tr>
            </table>
          </div>
        </div>
		<?php } ?>
        <div class="visionbx">
          <h2>Over Refraction</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" ><?php 
								$filter = array_filter($var["work_view"]["vision_data"]["PC"]["3"]["Over Refraction"]["OD"]);
								foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td ><?php 
							$filter = array_filter($var["work_view"]["vision_data"]["PC"]["3"]["Over Refraction"]["OS"]);
							foreach($filter as $key => $value){
									
									
									echo $key;
									echo $value.'&nbsp;';			
									}?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>&nbsp;</strong></td>
              </tr>
            </table>
          </div>
        </div></div>
		<div class="accord_cont">
        <div class="visionbx">
          <h2>MR 1st</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" ><?php $filter = array_filter($var["work_view"]["vision_data"]["MR"]["1"]["Initial"]["OD"]);
									foreach($filter as $key => $value){
										 if($key=="Txt1"){
											echo $value." ";
											
										 }
										 else if($key=="Txt2"){
											echo "+/-CYL"." ".$value." ";
										 }
										 else {
											echo $key." ".$value." ";
											
										}
									}
									 $filterGl = array_filter($var["work_view"]["vision_data"]["MR"]["1"]["GL/PH"]["OD"]);
									foreach($filterGl as $key => $value){echo "GL"." ".$value;}
								?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td>&nbsp;<?php $filter = array_filter($var["work_view"]["vision_data"]["MR"]["1"]["Initial"]["OS"]);
									foreach($filter as $key => $value){
										 if($key=="Txt1"){
											echo $value." ";
											
										 }
										 else if($key=="Txt2"){
											echo "+/-CYL"." ".$value." ";
										 }
										 else {
											echo $key." ".$value." ";
											
										}
									}
									 $filterGl = array_filter($var["work_view"]["vision_data"]["MR"]["1"]["GL/PH"]["OS"]);
									foreach($filterGl as $key => $value){echo "GL"." ".$value;}
								?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>&nbsp;<?php echo $var["work_view"]["vision_data"]["MR"]["1"]["Initial"]["Desc"];?></strong></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="visionbx">
          <h2>MR 2nd</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" >&nbsp;<?php $filter = array_filter($var["work_view"]["vision_data"]["MR"]["2"]["Initial"]["OD"]);
									foreach($filter as $key => $value){
										 if($key=="Txt1"){
											echo $value." ";
											
										 }
										 else if($key=="Txt2"){
											echo "+/-CYL"." ".$value." ";
										 }
										 else {
											echo $key." ".$value." ";
											
										}
									}
									 $filterGl = array_filter($var["work_view"]["vision_data"]["MR"]["2"]["GL/PH"]["OD"]);
									foreach($filterGl as $key => $value){echo "GL"." ".$value;}
								?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td >&nbsp;<?php $filter = array_filter($var["work_view"]["vision_data"]["MR"]["2"]["Initial"]["OS"]);
									foreach($filter as $key => $value){
										 if($key=="Txt1"){
											echo $value." ";
											
										 }
										 else if($key=="Txt2"){
											echo "+/-CYL"." ".$value." ";
										 }
										 else {
											echo $key." ".$value." ";
											
										}
									}
									 $filterGl = array_filter($var["work_view"]["vision_data"]["MR"]["2"]["GL/PH"]["OS"]);
									foreach($filterGl as $key => $value){echo "GL"." ".$value;}
								?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>&nbsp;<?php echo $var["work_view"]["vision_data"]["MR"]["2"]["Initial"]["Desc"];?></strong></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="visionbx">
          <h2>MR 3rd</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="64" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="333" >&nbsp;<?php $filter = array_filter($var["work_view"]["vision_data"]["MR"]["3"]["Initial"]["OD"]);
									foreach($filter as $key => $value){
										 if($key=="Txt1"){
											echo $value." ";
											
										 }
										 else if($key=="Txt2"){
											echo "+/-CYL"." ".$value." ";
										 }
										 else {
											echo $key." ".$value." ";
											
										}
									}
									 $filterGl = array_filter($var["work_view"]["vision_data"]["MR"]["3"]["GL/PH"]["OD"]);
									foreach($filterGl as $key => $value){echo "GL"." ".$value;}
								?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td >&nbsp;<?php $filter = array_filter($var["work_view"]["vision_data"]["MR"]["3"]["Initial"]["OS"]);
									foreach($filter as $key => $value){
										 if($key=="Txt1"){
											echo $value." ";
											
										 }
										 else if($key=="Txt2"){
											echo "+/-CYL"." ".$value." ";
										 }
										 else {
											echo $key." ".$value." ";
											
										}
									}
									 $filterGl = array_filter($var["work_view"]["vision_data"]["MR"]["3"]["GL/PH"]["OS"]);
									foreach($filterGl as $key => $value){echo "GL"." ".$value;}
								?></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;<strong><?php echo $var["work_view"]["vision_data"]["MR"]["3"]["Initial"]["Desc"];?></strong></td>
              </tr>
            </table>
          </div>
        </div>
		</div>
		<div class="accord_cont">
        <div class="visionbx">
          <h2>BAT</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="40" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td width="83%" >&nbsp;<?php foreach($var["work_view"]["vision_data"]["BAT"]["OD"] as $key => $value){
									echo $key.'&nbsp;';
									echo $value.'&nbsp;';			}?></td>
              </tr>
              <tr>
                <td align="center"><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
                <td >&nbsp;<?php foreach($var["work_view"]["vision_data"]["BAT"]["OS"] as $key => $value){
									echo $key.'&nbsp;';
									echo $value.'&nbsp;';			}?></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;<strong>Desc: <?php echo $var["work_view"]["vision_data"]["BAT"]["Desc"]; ?></strong></td>
              </tr>
            </table>
          </div>
        </div></div>
		<div class="accord_cont">
		<?php 
		
		if($var["work_view"]["vision_data"]["CVF"]["draw_od"]!= "" 
		|| $var["work_view"]["vision_data"]["CVF"]["draw_os"]!= ""){ ?>
		
		
        <div class="visionbx">
          <h2>CVF ( Confrontation Field )</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td  align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td align="center"  ><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
              </tr>
              <tr>
                <td align="center"><img src="<?php echo $var["work_view"]["vision_data"]["CVF"]["draw_od"]; ?>" height="140"></td>
                <td align="center" ><img src="<?php echo $var["work_view"]["vision_data"]["CVF"]["draw_os"]; ?>" height="140"></td>
              </tr>
              <tr>
                <td align="center"><?php echo $var["work_view"]["vision_data"]["CVF"]["sumod"]; ?></td>
                <td align="center" ><?php echo $var["work_view"]["vision_data"]["CVF"]["sumos"]; ?></td>
              </tr>
          <!-- <tr>
                <td align="center">&nbsp;</td>
                <td align="center" >&nbsp;</td>
              </tr>-->
            </table>
          </div>
        </div>
		<?php } ?>
		<?php if(($var["work_view"]["vision_data"]["Amsler Grid"]["draw_od"]!= "") ||
		($var["work_view"]["vision_data"]["Amsler Grid"]["draw_os"] != "")||
		($var["work_view"]["vision_data"]["Amsler Grid"]["desc"] != "")|| 
		($var["work_view"]["vision_data"]["Amsler Grid"]["exam_date"] != "")) {
		
		//$count2 = count($var["work_view"]["vision_data"]["Amsler Grid"]["desc"]); 
			
		
		?>
		 <div class="visionbx">
          <h2>Amsler Grid</h2>
          <div class="clearfix"></div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td  align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
                <td align="center"  ><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
              </tr>
              <tr>
                <td align="center"><img src="<?php echo $var["work_view"]["vision_data"]["Amsler Grid"]["draw_od"]; ?>" height="140"></td>
                <td align="center" ><img src="<?php echo $var["work_view"]["vision_data"]["Amsler Grid"]["draw_os"]; ?>" height="140"></td>
              </tr>
              <tr>
                <td align="left" colspan="2">Note :<?php echo $var["work_view"]["vision_data"]["Amsler Grid"]["desc"]; ?> <br> Exam Date : <?php echo $var["work_view"]["vision_data"]["Amsler Grid"]["exam_date"]; ?></td>
               </tr>
             <!-- </tr>
             <tr>
                <td align="left" colspan="2"></td>
                
              </tr>-->
            </table>
          </div>
        </div>
     
	<?php } ?>
	 </div>
	  
    </dd>
	
	<?php
	 
 
	$i=0;
  
   foreach($var["work_view"]["getWV_summary"] as $value){
	 if($i<=2 && $i!=1){ 
	 		if(($value["exam"]["sub_exams"][0]["summary"]["od"]["value"]!="")||
	 		($value["exam"]["sub_exams"][0]["summary"]["os"]["value"]!="")){
 			 ?>
    <dt> <a href="#accordion5" aria-expanded="false" aria-controls="accordion5" class="accordion-title accordionTitle js-accordionTrigger"> <?php echo $value["exam"]["header"]["title"];?> </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion5" aria-hidden="true">
      <div class="accord_cont ">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
		  
            <tr>
              <td  width="50%" align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
              <td width="50%" align="center"  ><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
            </tr>
			<?php if($value["exam"]["one_eye"]){ ?>
		   <tr>
              <td  width="50%" align="center" ><?php echo $value["exam"]["one_eye"]["od"]["value"]; ?> </td>
             <td  width="50%" align="center" ><?php echo $value["exam"]["one_eye"]["os"]["value"]; ?> </td> 
            </tr>
			<?php } ?>
            <tr>
              <td width="50%" align="left"><?php echo $value["exam"]["sub_exams"][0]["summary"]["od"]["value"]; ?>
    </td>
              <td width="50%" align="left" ><?php echo $value["exam"]["sub_exams"][0]["summary"]["os"]["value"]; ?></td>
            </tr>
          </table>
       </div></div></dd>
    <?php }}$i++;}?>
	<?php if($var["work_view"]["getWV_summary"][1]["exam"]["sub_exams"][0]["summary"]["od"]["value"] != ""){ ?>	
	 <dt> <a href="#accordion5" aria-expanded="false" aria-controls="accordion5" class="accordion-title accordionTitle js-accordionTrigger"> EOM </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion5" aria-hidden="true">
      <div class="accord_cont">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            
            <tr>
             
              <td width="100%" align="left" >&nbsp;<?php echo $var["work_view"]["getWV_summary"][1]["exam"]["sub_exams"][0]["summary"]["od"]["value"]; ?></td>
            </tr>
          </table>
       </div></div></dd>	
      
       <?php } ?>
 <?php foreach($draw as $value){if($value == "LA"){ $count_LA++; } }
 if(($var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][0]["summary"]["od"]["value"] != "" ||
 $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][0]["summary"]["os"]["value"] != ""  ||
 $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][1]["summary"]["od"]["value"] != "" || 
 $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][1]["summary"]["os"]["value"] != "" ||
 $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][2]["summary"]["od"]["value"] != "" ||
 $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][2]["summary"]["os"]["value"] != "" ||
 $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][3]["summary"]["od"]["value"] != "" || 
 $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][3]["summary"]["os"]["value"] != "" || $count_LA!="")
 ){ ?>
   
    <dt> <a href="#accordion7" aria-expanded="false" aria-controls="accordion7" class="accordion-title accordionTitle js-accordionTrigger"> L & A </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion7" aria-hidden="true">
      <div class="accord_cont ">
        <div class="table-responsive">
		
          <table class="table table-striped table-bordered">
            <tr>
              <td align="left" width="13%">&nbsp;</td>
              <td  align="center" width="35%"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
              <td align="center" width="30%" ><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
              <td align="center"  width="22%">&nbsp;</td>
            </tr>
			<?php if($value["exam"]["one_eye"]){ ?>
		   <tr>
		   <td align="left" >&nbsp;</td>
              <td   align="center" ><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["one_eye"]["od"]["value"]; ?> </td>
             <td   align="center" ><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["one_eye"]["os"]["value"]; ?> </td> 
			  <td   align="center" >&nbsp; </td> 
            </tr>
			<?php } ?>
            <tr>
              <td align="left" >Lids</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][0]["summary"]["od"]["value"]; ?> </td>
              <td align="left" ><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][0]["summary"]["os"]["value"]; ?></td>
              <td align="center" rowspan="4" >&nbsp; 
			  <?php $i=0; foreach($draw as $value){if($value == "LA"){?>
			  	<img src='<?php echo $var["work_view"]["getWV_drawing"][$i]["url"]; ?>'><?php }$i++;} ?>
			  </td> 
            </tr>
            <tr>
              <td align="left" >Lesion </td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][1]["summary"]["od"]["value"]; ?> </td>
              <td align="left" ><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][1]["summary"]["os"]["value"]; ?> </td>
             
            </tr>
            <tr>
              <td align="left" >Lid Postion</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][2]["summary"]["od"]["value"]; ?></td>
              <td align="left" ><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][2]["summary"]["os"]["value"]; ?></td>
              
            </tr>
			<tr>
              <td align="left" >Lacrimal System</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][3]["summary"]["od"]["value"]; ?></td>
              <td align="left" ><?php echo $var["work_view"]["getWV_summary"][3]["exam"]["sub_exams"][3]["summary"]["os"]["value"]; ?></td>
              
            </tr>
          </table>
        </div>
      </div>
    </dd>
	<?php } ?>
	<?php foreach($draw as $value){if($value == "IOP"){$count_IOP++;}}
	if(($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][0]["summary"]["od"]["value"] != "") ||
 ($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][0]["summary"]["os"]["value"] != "") ||
 ($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][1]["summary"]["od"]["value"] != "") || 
 ($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][1]["summary"]["os"]["value"] != "") ||
 ($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][2]["summary"]["od"]["value"] != "") || 
 ($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][2]["summary"]["os"]["value"] != "") ||
 ($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][3]["summary"]["od"]["value"] != "") || 
 ($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][3]["summary"]["os"]["value"] != "") ||
 ($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][4]["summary"]["od"]["value"] != "") || 
 ($var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][4]["summary"]["os"]["value"] != "") || $count_IOP!=""
){ ?>
	
    <dt> <a href="#accordion8" aria-expanded="false" aria-controls="accordion8" class="accordion-title accordionTitle js-accordionTrigger"> IOP / Gonio </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion8" aria-hidden="true">
      <div class="accord_cont ">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <tr>
              <td width="13%" align="left" >&nbsp;</td>
              <td width="35%"  align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
              <td width="30%" align="center"  ><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
              <td width="22%" align="center"   >&nbsp;</td>
            </tr>
			<?php if($value["exam"]["one_eye"]){ ?>
		   <tr>
		   <td align="left" >&nbsp;</td>
              <td   align="center" ><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["one_eye"]["od"]["value"]; ?> </td>
             <td   align="center" ><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["one_eye"]["os"]["value"]; ?> </td> 
			  <td   align="center" >&nbsp; </td> 
            </tr>
			<?php } ?>
            <tr>
              <td align="left" >IOP</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][0]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][0]["summary"]["os"]["value"]; ?></td>
             <td   align="center" rowspan="5">&nbsp;   <?php $i=0; foreach($draw as $value){if($value == "IOP"){?>
			  	<img src='<?php echo $var["work_view"]["getWV_drawing"][$i]["url"]; ?>'> <?php }$i++;} ?></td> 
            </tr>
            <tr>
              <td align="left" >Gonio </td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][1]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][1]["summary"]["os"]["value"]; ?></td>
              
            </tr>
            <tr>
              <td align="left" >Anethestic</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][2]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][2]["summary"]["os"]["value"]; ?></td>
             
            </tr>
            <tr>
              <td align="left" >Dialtion</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][3]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][3]["summary"]["os"]["value"]; ?></td>
             
            </tr>
            <tr>
              <td align="left" >Ophth. Drops</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][4]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][4]["exam"]["sub_exams"][4]["summary"]["od"]["value"]; ?></td>
              
            </tr>
          </table>
        </div>
      </div>
    </dd>
	<?php } ?>
	<?php foreach($draw as $value){if($value == "SLE"){ $count_SLE++; }} 
	if(($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][0]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][0]["summary"]["os"]["value"])||
	($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][1]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][1]["summary"]["os"]["value"])||
	($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][2]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][2]["summary"]["os"]["value"])||
	($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][3]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][3]["summary"]["os"]["value"])||
	($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][4]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][4]["summary"]["os"]["value"])|| $count_SLE!="" ){ ?>
    <dt> <a href="#accordion9" aria-expanded="false" aria-controls="accordion9" class="accordion-title accordionTitle js-accordionTrigger"> SLE </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion9" aria-hidden="true">
      <div class="accord_cont ">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <tr>
              <td width="13%" align="left" >&nbsp;</td>
              <td width="35%"  align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
              <td width="30%" align="center"  ><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
              <td width="22%" align="center"  >&nbsp;</td>
            </tr>
			<?php if($value["exam"]["one_eye"]){ ?>
		   <tr>
		   <td align="left" >&nbsp;</td>
              <td   align="center" ><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["one_eye"]["od"]["value"]; ?> </td>
             <td   align="center" ><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["one_eye"]["os"]["value"]; ?> </td> 
			  <td   align="center" >&nbsp; </td> 
            </tr>
			<?php } ?>
            <tr>
              <td align="left" >Conjunctiva</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][0]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][0]["summary"]["os"]["value"]; ?></td>
              <td   align="center" rowspan="5" >&nbsp;  <?php $i=0; foreach($draw as $value){if($value == "SLE"){?>
			  	<img src='<?php echo $var["work_view"]["getWV_drawing"][$i]["url"]; ?>'><?php }$i++;}?></td> 
            </tr>
            <tr>
              <td align="left" >Cornea </td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][1]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][1]["summary"]["os"]["value"]; ?></td>
             
            </tr>
            <tr>
              <td align="left" >Ant. Chamber</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][2]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][2]["summary"]["os"]["value"]; ?></td>
              
            </tr>
            <tr>
              <td align="left" >Iris &amp; Pupil</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][3]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][3]["summary"]["os"]["value"]; ?></td>
             
            </tr>
            <tr>
              <td align="left" >Lens</td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][4]["summary"]["od"]["value"]; ?></td>
              <td align="left"><?php echo $var["work_view"]["getWV_summary"][5]["exam"]["sub_exams"][4]["summary"]["os"]["value"]; ?></td>
             
            </tr>
          </table>
        </div>
      </div>
    </dd>
	<?php } ?>
	<?php foreach($draw as $value){if($value == "Fundus"){ $count_Fundus++;}}
	if(($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][0]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][0]["summary"]["os"]["value"])||
	($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][1]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][1]["summary"]["os"]["value"])||
	($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][2]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][2]["summary"]["os"]["value"])||
	($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][3]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][3]["summary"]["os"]["value"])||
	($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][4]["summary"]["od"]["value"])|| 
	($var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][4]["summary"]["os"]["value"])|| $count_Fundus!=""){ ?>
    <dt> <a href="#accordion10" aria-expanded="false" aria-controls="accordion10" class="accordion-title accordionTitle js-accordionTrigger"> Fundus </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion10" aria-hidden="true">
      <div class="accord_cont ">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <tr>
              <td width="13%" align="left" >&nbsp;</td>
              <td width="35%"  align="center"><img src="html/img/tstodactive.png" width="40" height="40" alt=""/></td>
              <td width="30%" align="center"  ><img src="html/img/tstosactive.png" width="40" height="40" alt=""/></td>
              <td width="22%" align="center"  >&nbsp;</td>
            </tr>
			<?php if($value["exam"]["one_eye"]){ ?>
		   <tr>
		   <td align="left" >&nbsp;</td>
              <td   align="center" ><?php echo $var["work_view"]["getWV_summary"][6]["exam"]["one_eye"]["od"]["value"]; ?> </td>
             <td   align="center" ><?php echo $var["work_view"]["getWV_summary"][6]["exam"]["one_eye"]["os"]["value"]; ?> </td> 
			  <td   align="center" >&nbsp; </td> 
            </tr>
			<?php } ?>
            <tr>
              <td align="left" >C : D</td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][0]["summary"]["od"]["value"]; ?></td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][0]["summary"]["os"]["value"]; ?></td>
             <td   align="center" rowspan="5">&nbsp;  <?php $i=0; foreach($draw as $value){if($value == "Fundus"){?>
			  	<img src='<?php echo $var["work_view"]["getWV_drawing"][$i]["url"]; ?>'/><?php }$i++;}?></td>
            </tr>
            <tr>
              <td align="left" >Optic Nerve </td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][1]["summary"]["od"]["value"]; ?></td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][1]["summary"]["os"]["value"]; ?></td>
              
            </tr>
            <tr>
              <td align="left" >Vitreous</td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][2]["summary"]["od"]["value"]; ?></td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][2]["summary"]["os"]["value"]; ?></td>
              
            </tr>
            <tr>
              <td align="left" >Retinal Exam</td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][3]["summary"]["od"]["value"]; ?></td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][3]["summary"]["os"]["value"]; ?></td>
              
            </tr>
            <tr>
              <td align="left" >Refractive Surgery</td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][4]["summary"]["od"]["value"]; ?></td>
              <td align="left">&nbsp;<?php echo $var["work_view"]["getWV_summary"][6]["exam"]["sub_exams"][4]["summary"]["os"]["value"]; ?></td>
              
            </tr>
          </table>
        </div>
      </div>
    </dd>
	<?php } ?>
	<?php $count = count($var["work_view"]["get_assess_plan"]["AssessPlan"]);
	if($count > 1){?>
    <dt> <a href="#accordion11" aria-expanded="false" aria-controls="accordion11" class="accordion-title accordionTitle js-accordionTrigger"> Assesment & Plan </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion11" aria-hidden="true">
      <div class="accord_cont ">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <tr>
              <td width="3%"  align="left" ><strong>SN</strong></td>
              <td width="40%"  align="left"><strong>Assessment</strong></td>
              <td width="8%"  align="left"><strong>Eye</strong></td>
              <td width="16%"  align="left" ><strong>Dx Code</strong></td>
              <td width="33%" align="left" ><strong>Plan</strong></td>
            </tr>
			<?php $i=1; foreach($var["work_view"]["get_assess_plan"]["AssessPlan"] as $value){?>
			
            <tr>
              <td width="3%"  align="left" ><?php echo $i;?></td>
               <td width="40%"  align="left"><?php echo $value["assessment"]; ?></td>
              <td width="8%"  align="left"><?php echo $value["eye"];?></td>
              <td width="16%"  align="left" ><?php echo $value["dxcode"];?></td>
              <td width="33%" align="left" ><?php echo $value["plan"];?></td>
            </tr>
			<?php $i++;}?>
          </table>
        </div>
      </div>
    </dd>
	<?php } ?>
	<?php if(($var["work_view"]["get_assess_plan"]["Other"]["Transition_of_care"] != "")||
	($var["work_view"]["get_assess_plan"]["Other"]["Surgical_Ocular_Hx"] != 0) || 
	($var["work_view"]["get_assess_plan"]["Other"]["Consult_Reason"]!= "")||
	($var["work_view"]["get_assess_plan"]["Other"]["ScribedBy"] != "")||
	($var["work_view"]["get_assess_plan"]["Other"]["Comments"] != "")||
	($var["work_view"]["get_assess_plan"]["Other"]["Comments_requested_by_Patient"])){?>
    <dt> <a href="#accordion12" aria-expanded="false" aria-controls="accordion12" class="accordion-title accordionTitle js-accordionTrigger"> Follow Up </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion12" aria-hidden="true">
      <div class="accord_cont ">
        <div class="followbx">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <td width="35%" align="left" ><strong>Transition of care</strong></td>
                <td width="65%" align="left"><?php echo $var["work_view"]["get_assess_plan"]["Other"]["Transition_of_care"]; ?></td>
              </tr>
              <tr>
                <td  align="left" ><strong>Surgical Ocular HX</strong></td>
                <td  align="left"><?php echo $var["work_view"]["get_assess_plan"]["Other"]["Surgical_Ocular_Hx"]; ?></td>
              </tr>
              <tr>
                <td  align="left" ><strong>Consult Reason</strong></td>
                <td  align="left"><?php echo $var["work_view"]["get_assess_plan"]["Other"]["Consult_Reason"]; ?></td>
              </tr>
              <tr>
                <td  align="left" ><strong>Scribed By</strong></td>
                <td  align="left"><?php echo $var["work_view"]["get_assess_plan"]["Other"]["ScribedBy"]; ?></td>
              </tr>
              <tr>
                <td  align="left" ><strong>Comment</strong></td>
                <td  align="left"><?php echo $var["work_view"]["get_assess_plan"]["Other"]["Comments"]; 
										?> </td>
              </tr>
              <tr>
                <td  align="left" ><strong>Comments requested by patient</strong></td>
                <td  align="left"><?php echo $var["work_view"]["get_assess_plan"]["Other"]["Comments_requested_by_Patient"]; ?></td>
              </tr>
            </table>
          </div>
        </div>
		<?php foreach($var["work_view"]["get_assess_plan"]["FU"] as $value){ ?>
        <div class="followbx">&nbsp; <?php echo $value["number"]." ".$value["time"]." "."for F/U With"."
		".$value["visit_type"]."<br>".
          $value["provider"]; ?></div><?php } ?>
      </div>
    </dd>
	<?php } ?>
	<?php if($var["work_view"]["get_assess_plan"]["Other"]["Sign"]!=""){ ?>
	<div>
    <dt> <a href="#accordion13" aria-expanded="false" aria-controls="accordion13" class="accordion-title accordionTitle js-accordionTrigger"> Signatures </a> </dt>
    <dd class="accordion-content accordionItem is-collapsed" id="accordion13" aria-hidden="true">
      <div class="accord_cont "> <img src='<?php echo 'http://'.$_SERVER['HTTP_HOST']."/R6-Dev/interface/main/uploaddir".$var["work_view"]["get_assess_plan"]["Other"]["Sign"];?>' alt=""> </div>
    </dd>
	</div>
	<?php } ?>
 <div style="padding-bottom:25px;"></div>
  <div class="tophead" style="bottom:-10px;position:fixed; width:100%; text-align:center; background-color:#CCCCCC">
 <form action="" method="POST">
  <button style="height:40px; width:150px; text-align:center; " class="btn btn-default" onClick="finalize()" name="submit">FINALIZE</button></form>
  </div>

<div class="clearfix"></div>
<script src="html/js/custom-accordien.js"></script>
<script>
function finalize(){
	alert("Chart Note is Finalized Successfull");
}
</script>
 </dl>
 </div>
</body>
</html>

