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
File: iop_graphs.php
Purpose: This file provides IOP Graph detail in work view.
Access Type : Include File
*/
?>
<?php

//if($zRemotePageName == "main_page"){}else{
if(isset($_GET["inc_glbl"]) && !empty($_GET["inc_glbl"])){
require_once(dirname(__FILE__).'/../../../config/globals.php');
}
//include_once(dirname(__FILE__)."/common/functions.php");
//include_once(dirname(__FILE__)."/../main/main_functions.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsmain"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjs"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/amcharts.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/amcharts/serial.js"></script>
<?php //} ?>

<script type="text/javascript">

var vision_chart = "<?php echo $_GET["vision"]; ?>";
var ptid = '<?php echo $_SESSION["patient"]; ?>';
function IOP_showGraphsAm(val){	
	var elem_formAction = 'getGlucomaGraphAm'; var ptqry=""; 
	if(vision_chart!=''){ elem_formAction = 'getVisionGraphAm'; ptqry="&req_ptwo=1&ptid="+ptid; }	
	if( (typeof(save_exam)!="undefined") && (typeof(val)=="undefined" || val == "") && (finalize_flag!="1" || isReviewable == "1") && elem_per_vo != "1" && vision_chart=='' ){  save_exam("IOPGRPH"); return;}	
	var u="<?php echo $GLOBALS['rootdir']; ?>/chart_notes/requestHandler.php?elem_formAction="+elem_formAction+ptqry;	
	var p="elem_opts=All";
	$.post(u,p,function(data){
		var ARR_result = JSON.parse(data);
		if(vision_chart!=''){
			
			var line_pay_graph_var_arr_js = (ARR_result['line_pay_graph_var_detail'] && ARR_result['line_pay_graph_var_detail']["dis"]) ? ARR_result['line_pay_graph_var_detail']["dis"] : null ;
			var line_payment_tot_arr_js = (ARR_result['line_payment_tot_detail'] && ARR_result['line_payment_tot_detail']["dis"]) ? ARR_result['line_payment_tot_detail']["dis"] : null ;
			
			var line_pay_graph_var_arr_js_nr = (ARR_result['line_pay_graph_var_detail'] && ARR_result['line_pay_graph_var_detail']["nr"]) ? ARR_result['line_pay_graph_var_detail']["nr"] : null;
			var line_payment_tot_arr_js_nr = (ARR_result['line_payment_tot_detail'] && ARR_result['line_payment_tot_detail']["nr"]) ? ARR_result['line_payment_tot_detail']["nr"] : null;
			var noflg=0;
			
			if(line_payment_tot_arr_js && line_pay_graph_var_arr_js){
				line_chart('serial','IOPGraphChartAm',line_payment_tot_arr_js,line_pay_graph_var_arr_js,'90'); noflg+=1;
			}else{
				document.getElementById('IOPGraphChartAm').style.marginTop = "15%";
				document.getElementById('IOPGraphChartAm').innerHTML = "No distance data available";
			}
			
			if(line_payment_tot_arr_js_nr && line_pay_graph_var_arr_js_nr){
				line_chart('serial','IOPGraphChartAm1',line_payment_tot_arr_js_nr,line_pay_graph_var_arr_js_nr,'90'); noflg+=1;
			}else{
				document.getElementById('IOPGraphChartAm1').style.marginTop = "15%";
				document.getElementById('IOPGraphChartAm1').innerHTML = "No Near data available";
			}
			
			if(noflg==0){
				top.fAlert("Graph information does not exists.");
				if(top && top.fmain && typeof(top.fmain.show_iop_graphs)!='undefined'){				
					top.fmain.show_iop_graphs(1);
				}
			}else{
				document.getElementById('IOPGraphChartAm').style.display='block';	document.getElementById('IOPGraphChartAm1').style.display='block';
				document.getElementById('IOPGraphChartAm').style.width='50%'; document.getElementById('IOPGraphChartAm1').style.width='50%'; 
				document.getElementById('IOPGraphChartAmMain').style.display='block';
				$("#IOPGraphChartAmCon").draggable();
			}		
			
		}else{		
			var line_pay_graph_var_arr_js = ARR_result['line_pay_graph_var_detail'];
			var line_payment_tot_arr_js = ARR_result['line_payment_tot_detail'];
			if(line_payment_tot_arr_js && line_pay_graph_var_arr_js){
				line_chart('serial','IOPGraphChartAm',line_payment_tot_arr_js,line_pay_graph_var_arr_js,'90');
				document.getElementById('IOPGraphChartAmMain').style.display='block';
				document.getElementById('IOPGraphChartAm').style.display='block';
				$("#IOPGraphChartAmCon").draggable();
			}else{
				top.fAlert("Graph information does not exists.");
				if(top && top.fmain && typeof(top.fmain.show_iop_graphs)!='undefined'){				
					top.fmain.show_iop_graphs(1);
				}else if(typeof(show_iop_graphs)!='undefined'){
					show_iop_graphs(1);
				}
			}
		}
	});
}

function line_chart(chart_type,div_id,data_arr,data_graph_arr,labelRotation){
	if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0};
	//alert(data_arr)
	var chartData = $.parseJSON(data_arr);
	var chartData_Graph = $.parseJSON(data_graph_arr);
	var title = 'IOP';
	if(vision_chart!=''){ title = div_id == 'IOPGraphChartAm1' ? "Near" : "Distance"; }
		
	var chart = AmCharts.makeChart(div_id, {
		"type": chart_type,
		"categoryField": "category",
		"startDuration": 0,
		"theme": "light",
		"fontSize": 12,
		"categoryAxis": {
			"gridPosition": "start",
			"labelRotation": labelRotation,
			autoWrap:false, minHorizontalGap:0
		},
		"trendLines": [],
		"graphs": chartData_Graph,
		"guides": [],
		"valueAxes": [{
			"unit": "",
			"unitPosition": "left",
		}],
		"allLabels": [],
		"balloon": {},
		"legend": {
			"useGraphSettings": true
		},
		"titles": [{"text": ""+title}],
		"dataProvider": chartData,
		
	} );
}

function close_graph(){
	//document.getElementById('IOPGraphChartAmMain').style.display='none';
	parent.show_iop_graphs(1);
}

$(document).ready(function () { IOP_showGraphsAm(); });

</script>
</head>
<body>

<div style="position:fixed; top:0px; left:0px; width:100%; height:100%; z-index:999;background:rgba(0, 0 , 0 , 0.1); display:none;" id="IOPGraphChartAmMain">
	<div id="IOPGraphChartAmCon" style="position:relative; width: 75%; left:0; top:50px; height: 400px;  margin: 0 auto ; display:table; background:#fff" >
		<div class="cross" style="position:absolute; top:-2px; right:12px;z-index:1000; cursor:pointer;" onClick="close_graph()"> 
		<span style="line-height:50px; border-radius:100%; -moz-border-radius:100%; -webkit-border-radius:100%; background:#000000;padding:5px 7px;  color:#fff">X</span>   </div>
		<div id="IOPGraphChartAm" style=" float:left; width:100%; display:none;height: 500px;margin-top: 5px;text-align:center;"></div>
		<div id="IOPGraphChartAm1" style=" float:left; width:100%; display:none;height: 500px;margin-top: 5px;text-align:center;"></div>
	</div>
</div>
</body>
</html>