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
?>
<?php
/*
  FILE : scheduler_new_report.php
  PURPOSE : Search criteria for scheduler report
  ACCESS TYPE : Direct
 */
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/class.mur_reports.php");

$objMUR			= new MUR_Reports;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>
<!-- Bootstrap -->
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<style>
.pd5.report-content {
	background-color: #EAEFF5;
}	
#content1{
	background-color:#EAEFF5;
}
#mur_version,.date-pick{width:100px;}
</style>
</head>
<body>
<form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">
<input name="processReport" id="processReport" autocomplete="off" value="Daily" type="hidden">
<input type="hidden" name="SchedulerNewReport" id="SchedulerNewReport" value="">
<input type="hidden" name="Submit" id="Submit" value="get reports">
<input type="hidden" name="form_submitted" id="form_submitted" value="1">
<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
<div class=" container-fluid">
    <div class="whitebox" id="report_form">
        <div class="row">
        	<div class="col-sm-3">
            <label for="mur_version">MUR Version</label>
            <select class="form-control minimal" id="mur_version" name="mur_version">
            	<option value="2017">MIPS 2017</option>
            	<option value="2017">Stage 2016</option>
            	<option value="2017">Stage I 2015</option>
            	<option value="2017">Stage II 2015</option>
            	<option value="2017">Stage I 2014</option>
            	<option value="2017">Stage II 2014</option>
            	<option value="2017">Stege I 2013</option>                
            </select>
            </div>
        	<div class="col-sm-3">
            	<label for="provider">Provider</label>
            	<select class="form-control minimal" name="provider" id="provider">
                 <option value="0">----SELECT----</option>
                 <?php
                    $option_pro_arr = $objMUR->get_provider_ar(0);
                    foreach($option_pro_arr as $OptphyId=>$OptphyName){
                        echo '<option value="'.$OptphyId.'">'.$OptphyName.'</option>
                        ';
                    }
                 ?>
                 </select>
            </div>
            <div class="col-sm-1"></div>
        	<div class="col-sm-1">
            	<label for="dtfrom">Date From</label>
            	<input type="text" name="dtfrom" id="dtfrom" size="11" maxlength="10" class="form-control date-pick" onBlur="checkdate(this);" value="<?php echo date(phpDateFormat(), strtotime(date('Y/m/1'))); ?>" />
            </div>
        	<div class="col-sm-1">
                <label for="dtupto">To</label>
                <input type="text" name="dtupto" id="dtupto" size="11" maxlength="10" class="form-control date-pick" onBlur="checkdate(this);" value="<?php echo date(phpDateFormat());?>" />
            </div>
            
            <div class="col-sm-1"></div>
        	<div class="col-sm-1">
            	<label for="provider">Measure</label>
            	<select class="form-control minimal" name="meausre" id="measure">
                 <option value="1">Cal-Core/Menu</option>
                 <option value="2">Cal-CMS</option>
                 <option value="3">Analyze</option>
                 </select>
            </div>
            <div class="col-sm-1"><label>&nbsp;</label><br><input type="button" class="btn btn-success" value="Get Report"></div>
	    </div>
    </div>
    <div class="clearfix"></div>
    <div class="whitebox">
        <div id="report_result_area">
    	    result area..
        </div>
    </div>
</div>
</form>
<script type="text/javascript">
function set_container_height(){
	h = parseInt(window.innerHeight)-parseInt($('#report_form').height());
	h = h-60; //margins
	$('#report_result_area').css({
		'height':(h),
		'max-height':(h),
		'overflow-x':'hidden',
		'overflowY':'auto'
	});
} 

$(window).load(function(){
	set_container_height();
});

$(window).resize(function(){
	set_container_height();
});

</script> 
</body>
</html>