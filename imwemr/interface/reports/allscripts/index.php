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

//Function Files
$without_pat = "yes";
require_once("../reports_header.php");
require_once($GLOBALS['fileroot'] .'/library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;

//---Get Global Date Format
$phpDateFormat = phpDateFormat();

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

 
// Template field value form database
$dbtemp_name = "Workview - TW SaveDocumentImage API Call Log";

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
                position:relative;
                margin-left:40px;

                background-color: #EAEFF5;
            }
            .fltimg {
                position:absolute;
            }
            .fltimg span.glyphicon {
                position: absolute;
                top: 170px;
                left: 10px;
                color: #fff;
            }
            .reportlft .btn.btn-mkdef {
                padding-top: 6px;
                padding-bottom: 6px;
            }
            #content1{
                background-color:#EAEFF5;
            }
			.rpt_table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > thead > tr > th {
				border: 1px solid #ddd;
			}
            .cursor{
                cursor: pointer;
            }
		</style>
    </head>
    <body>
        <!--<form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">-->
            <div class=" container-fluid">
                <div class="anatreport">
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Log Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria" >
										<div class="row">
                                            <div class="col-sm-4">
                                                <label>Patient ID</label>
                                                <input type="text" name="patientId" style="font-size: 12px;" id="patientId" class="form-control">
                                            </div>
											<div class="col-sm-8">
                                                <label>DOS</label>
                                                <div id="dateFieldControler">
                                                    <select name="dayReport" id="dayReport" class="selectpicker" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
                                                        <option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'SELECTED'; ?>>Daily</option>
                                                        <option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'SELECTED'; ?>>Weekly</option>
                                                        <option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'SELECTED'; ?>>Monthly</option>
                                                        <option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'SELECTED'; ?>>Quarterly</option>
                                                        <option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'SELECTED'; ?>>Date Range</option>
                                                    </select>
                                                </div>
                                                <div class="row" style="display:none" id="dateFields">
                                                    <div class="col-sm-5">
                                                        <div class="input-group">
                                                            <input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                        </div>
                                                    </div>	
                                                    <div class="col-sm-5">	
                                                        <div class="input-group">
                                                            <input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo $_REQUEST['End_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button type="button" class="btn" onclick="DateOptions('x');"><span class="glyphicon glyphicon-arrow-left"></span></button>
                                                    </div>
                                                </div>
                                            </div>
										</div>
                                    </div>
								</div>
								<br /><br /><br />
								<div class="grpara">
                                    <div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
                                        <button class="savesrch" type="button" onClick="top.fmain.createReport();">Search</button>
                                    </div>
                                </div>                                                                                        
                            </div>
                        </div>
                        <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="<?php echo $GLOBALS['php_server']; ?>/library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
                            </div>
							<div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
                                        <?php
                                            echo '<div class="text-center alert alert-info">No Search Done.</div>';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
            </div>
        <!--</form>-->
	
<script type="text/javascript">
	
	$(document).ready(function () {
		DateOptions("<?php echo $_POST['dayReport']; ?>");
		$(".toggle-sidebar").click(function () {
			$("#sidebar").toggleClass("collapsed");
			$("#content1").toggleClass("col-md-12 col-md-9");

			if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
			} else {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
			}
			return false;
		});
	});
	
	function createReport()
	{
		var parameters = {reportType:'WvCallLog'};
		
		if( $('#patientId').val() !== '' )
			parameters.patientId = $('#patientId').val();
		
		parameters.dayReport = $('#dayReport').val();
		
		parameters.Start_date = $('#Start_date').val();
		parameters.End_date = $('#End_date').val();
		
		$.ajax({
			url: '<?php echo $GLOBALS['php_server']; ?>/interface/reports/allscripts/ajax.php',
			method: 'POST',
			data: parameters,
			beforeSend: function()
			{
				top.show_loading_image('show');
			},
			success: function(resp)
			{
				$('#html_data_div').html(resp);
			},
			complete: function()
			{
				top.show_loading_image('hide');
			}
		});
	}

    /*Show Details of the API call Log*/
    function showDetails( logId )
    {
        if( typeof(logId) != "number" )
        {
            top.fAlert('Invalid Log Id', 'TW API Call Detials', '', 960);
            return false;
        }

        var parameters = {reportType:'logDetails', logId: logId};

        $.ajax({
            url: '<?php echo $GLOBALS['php_server']; ?>/interface/reports/allscripts/ajax.php',
            method: 'POST',
            data: parameters,
            beforeSend: function()
            {
                top.show_loading_image('show');
            },
            success: function(resp)
            {
                resp = $.parseJSON(resp);

                var data = '<style>#logData > div > span:first-child{float: left; width:180px; font-weight: bold; text-align: right; padding-right: 10px; margin-right: 10px}';

                data += '#logData > div > pre{float:left; padding-left: 6px; margin:0px;}';
                data += '#logData {max-height: 700px; overflow:scroll;}';
                data += '#logData pre {width: 720px;}';

                data += '#logData > div {border-bottom: 1px solid #000000; padding: 8px 0;}';
                //data += '#logData > div > div {padding-top: 8px; padding-bottom: 2px;}';
                data += '#logData > div > div.clear {clear:both}';

                data += '</style><div id="logData">';

                data += '<div><span>Action</span>'+resp.message.action+'<div class="clear"></div></div>';

                data += '<div><span>URL</span>'+resp.message.url+'<div class="clear"></div></div>';
                
                data += '<div><span>Response Code</span>'+resp.message.respCode+'<div class="clear"></div></div>';

                data += '<div><span>Error Message</span>'+JSON.stringify(resp.message.errroMessage, null, '\t')+'<div class="clear"></div></div>';

                data += '<div><span>Parameters Sent</span><pre style="height:200px;">'+JSON.stringify(resp.message.params, null, '\t')+'</pre><div class="clear"></div></div>';

                data += '<div><span>Response Data</span><pre style="height:320px;">'+JSON.stringify(resp.message.response, null, '\t')+'</pre><div class="clear"></div></div>';


                data += '</data>';

                top.fAlert(data, 'TW API Call Detials', '', 960);

                console.log(resp.message.params);
            },
            error: function ()
            {
                top.fAlert('Unable to complete the request.', 'TW API Call Detials', '', 960);
            },
            complete: function()
            {
                top.show_loading_image('hide');
            }
        });
    }
	
	function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
	}

    $(document).ready(function(){
        set_container_height();
    });

	$(window).resize(function(){
		set_container_height();
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);
</script> 
</body>
</html>