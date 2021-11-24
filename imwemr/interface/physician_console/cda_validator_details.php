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
require_once(dirname(__FILE__).'/../../config/globals.php');
$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
$ccda_file = trim($_GET['ccda_file']);
set_time_limit(60);
if($_GET['source']=='tempunzipped'){
	$ccda_file = $dir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped/'.$ccda_file;
}else{
	$ccda_file = $dir_path.'/users'.$ccda_file;
}

$arrName   = explode("/",$ccda_file);
$file_name = end($arrName);
$extension = pathinfo($ccda_file, PATHINFO_EXTENSION);
//die('end='.substr($file_name,0,-4));
$txtRootPath = $dir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped';
//if(is_dir($txtRootPath) == false){mkdir($txtRootPath, 0755, true);}
$txtFileFullpath = $txtRootPath.'/'.substr($file_name,0,-4).'.txt';

//die($txtFileFullpath);
$validationObjective = 'C-CDA_IG_Plus_Vocab';
$referenceFileName	 = 'Readme.txt';
if(!file_exists($txtFileFullpath) || !is_file($txtFileFullpath)){
	die('Result file not found. Please perform validation again.');
}else{
	$response = file_get_contents($txtFileFullpath);
	$error = '';
	$eventType = 'file';
}
if($response){
	$result = json_decode($response,false);
	if(is_object($result)){
		$metaData = $result->resultsMetaData;
		unset($metaData->ccdaFileName);
		unset($metaData->ccdaFileContents);
		$TotalErrors = 0;
		$serviceErrorMessage = trim($metaData->serviceErrorMessage);
		foreach($metaData->resultMetaData as $i=>$error){
			if($i==0 || $i==3 || $i==6)
			$TotalErrors += (int)$error->count;
		}
		//if($TotalErrors > 0 || $serviceErrorMessage != '') unset($metaData->resultMetaData);
		$result->resultsMetaData->TotalErrors = $TotalErrors;
	}
	
	if($eventType == 'curl'){
		echo json_encode($metaData);
		die;
	}else{
		//echo $eventType;
		//die;
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
    <title>:: imwemr ::</title>
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
        <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/html5shiv.min.js"></script>
        <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/respond.min.js"></script>
    <![endif]-->
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
     <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script> 
     <!-- Include all compiled plugins (below), or include individual files as needed --> 
     <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script> 
     <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
     <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
     <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.js"></script>
     <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/console.js?<?php echo filemtime('../../library/js/console.js');?>"></script>
	 <script type="text/javascript">
	 $(document).ready(function(){
	  // Add smooth scrolling to all links
	  $("a").on('click', function(event) {
	
		// Make sure this.hash has a value before overriding default behavior
		if (this.hash !== "") {
		  // Prevent default anchor click behavior
		  event.preventDefault();
	
		  // Store hash
		  var hash = this.hash;
	
		  // Using jQuery's animate() method to add smooth page scroll
		  // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
		  $('html, body').animate({
			scrollTop: $(hash).offset().top
		  }, 800, function(){
	   
			// Add hash (#) to URL when done scrolling (default click behavior)
			window.location.hash = hash;
		  });
		} // End if
	  });
	});
	 </script>
	<style type="text/css">
		body, html, .main {
			height: 100%;
		}
		
		section {
			min-height: 100%;
		}
		body{background-color:#FFF;}
		p{margin:2px 5px;}
		div.panel{margin-bottom:10px !important; border-left-width:5px;}
		.panel-body{padding:5px !important;}
		.panel-error{border:1px solid #CD413D; background-color:#FDDDDC;}
		.panel-warning{border:1px solid #EEA034; background-color:#FEF7ED;}
		.panel-info{border:1px solid #3CB2D6; background-color:#E9F2F5;}
		span.label{font-size:110%;}
	</style>
</head>
<body>
<?php if($eventType == 'file'){/*	pre($result,1);*/?>
<div class="whitebox">
    <h3 style="margin:0px;">Validation Result for: <b><span class="text-info"><?php echo $file_name;?></span></b></h3>
    <p><b>Validation Objective: </b><?php echo $validationObjective;?><br>
       <b>Reference File Name: </b><?php echo $referenceFileName;?></p>
    <?php if($serviceErrorMessage != ''){?>
    	<div class="alert alert-warning"><?php echo $serviceErrorMessage;?></div>
    <?php }?>
        <table class="table table-bordered">
            <thead>
            <tr class="bg-success">
                <th class="col-sm-6">CONFORMANCE</th>
                <th class="col-sm-2">ERROR</th>
                <th class="col-sm-2">WARNING</th>
                <th class="col-sm-2">INFORMATION</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($result->resultsMetaData->resultMetaData as $i=>$arr){
            if($i==0 || $i==3 || $i==6){$badge = 'label-danger';}
            if($i==1 || $i==4 || $i==7){$badge = 'label-warning';}	
            if($i==2 || $i==5 || $i==8){$badge = 'label-info';}	
            
            if($i==0){?>
            <tr>
                <th class="col-sm-6"><?php echo str_ireplace(' error','',$arr->type);?></th>
            <?php }else if($i==3 || $i==6){?>
            </tr>
            <tr>
                <th class="col-sm-6"><?php echo str_ireplace(' error','',$arr->type);?></th>
            <?php }?>
                <th class="col-sm-2"><a href="#<?php echo str_replace(array(' ','&'),'',$arr->type);?>"><span class="label <?php echo $badge;?>"><?php echo $arr->count;?></span></a></th>
            
            <?php }?>
            </tr>
            </tbody>
        </table>
		<?php 
        if($serviceErrorMessage == ''){
			$old_Section_type = '';
			foreach($result->ccdaValidationResults as $i=>$errArr){
				$er_type = strtoupper(end(explode(' ',trim($errArr->type))));
				if($er_type=='ERROR') $panel_bg = 'panel-danger';
				else if($er_type=='WARNING') $panel_bg = 'panel-warning';
				else if($er_type=='INFO') $panel_bg = 'panel-info';
				if($old_Section_type!= str_replace(array(' ','&'),'',$errArr->type)){
					$old_Section_type = str_replace(array(' ','&'),'',$errArr->type);
					
			?>	<a id="<?php echo str_replace(array(' ','&'),'',$errArr->type);?>"></a>
            <?php }?>
				<div class="panel panel-<?php echo strtolower($er_type);?>">
					<div class="panel-body">
						<p><b><?php echo strtoupper(end(explode(' ',trim($errArr->type))));?>: </b><?php echo $errArr->description;?></p>
						<p><b><?php echo $errArr->xPath;?></b></p>
						<p><b>LINE#: </b><?php echo $errArr->documentLineNumber;?></p>
					</div>
				</div>
			<?php } 
		}?>
          
</div>
	
    
<?php }?>
</body>
</html>
<?php
unlink($txtFileFullpath);
?>