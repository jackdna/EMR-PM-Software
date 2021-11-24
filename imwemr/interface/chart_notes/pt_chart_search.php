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
File: pt_chart_search.php
Purpose: This file provides Patient search in chart notes.
Access Type : Direct
*/
//pt_chart_search.php
include_once("../../config/globals.php");

//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

$library_path = $GLOBALS['webroot'].'/library';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Patient Chart Search</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]--> 
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<style>
			.process_loader {
				border: 16px solid #f3f3f3;
				border-radius: 50%;
				border-top: 16px solid #3498db;
				width: 80px;
				height: 80px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
				display: inline-block;
			}
			.adminbox{min-height:inherit}
			.adminbox label{overflow:initial;}
			li label{display:inline!important}
			.alert{margin-bottom:0px!important;padding:5px!important;}
		</style>
	</head>
	<script>
		function openThisChart(fid, cstts, relsnum){	
			if(typeof(window.opener.top.fmain.showFinalize)=="function"){	window.opener.top.fmain.showFinalize('Chart Note',fid,cstts,relsnum);}	
			window.close();
		}

		function search(){
			if($('div.process_loader').hasClass('hide') === true){
				$('div.process_loader').removeClass('hide');
			}
			var tmp = $("#elem_search").val();
			if($.trim(tmp)==""){
				$('.alert.alert-warning').html("<b>Enter text to search.</b>");
				if($('.alert.alert-warning').hasClass('hide') === true){
					$('.alert.alert-warning').removeClass('hide').slideDown('fast');
					setTimeout(function(){ $('.alert.alert-warning').addClass('hide').slideUp('fast'); }, 1000);
				}
				$("#searchResult").empty();
				if($('div.process_loader').hasClass('hide') === false){
					$('div.process_loader').addClass('hide');
				}		
				return; 
			}else{
				if($('.alert.alert-warning').hasClass('hide') === false){
					$('.alert.alert-warning').addClass('hide').slideUp('fast');
				}
			}
			
			var o = {"elem_formAction":"PatientChartSearch", "term":""+tmp };
			$.get("requestHandler.php",o, function(data){
				$("#searchResult").html(""+data+"");
			});
			
			if($('div.process_loader').hasClass('hide') === false){
				$('div.process_loader').addClass('hide');
			}
				
			return false;
		}
		
		$(document).ready(function(){
			if(typeof(window.opener.top.innerDim)=='function'){
				var innerDim = window.opener.top.innerDim();
				if(innerDim['w'] > 1600) innerDim['w'] = 1600;
				if(innerDim['h'] > 900) innerDim['h'] = 900;
				window.resizeTo(innerDim['w'],innerDim['h']);
				brows	= get_browser();
				if(brows!='ie') innerDim['h'] = innerDim['h']-35;
				var result_div_height = innerDim['h']-150;
				$('#searchResult').height(result_div_height+'px');
			}
			
			 $('#elem_search').keydown(function(event) {
				if (event.keyCode == 13) {
					search();
					return false;
				 }
			});	
		});	
	</script>	
	<body>
		<div class="mainwhtbox">
			<div class="row">
				<div class="col-sm-12 purple_bar">
					<div class="row">
						<form name="search_form" onsubmit="search();return false;">
							<div class="col-sm-3">
								<div class="input-group">
									<input type="text" id="elem_search" name="elem_search" class="form-control" value="<?php echo $_REQUEST['elem_search']; ?>" placeholder="Enter text to search.." onkeyup="search()">
									<label for="elem_search" class="input-group-btn " onclick="search()">
										 <button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-search"></span></button>
									</label>	
								</div>	
							</div>
							<div class="col-sm-3">
								<div class="alert alert-warning hide text-center"></div>
							</div>
						</form>	
					</div>	
				</div>
				
				<div class="col-sm-12 text-center pt10">
					<div class="process_loader hide"></div>
				</div>

				<div class="col-sm-12 pt10">
					<div id="searchResult" style="overflow-y:scroll"></div>	
				</div>	
			</div>
		</div>
	</body>
</html>


