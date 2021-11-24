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
ini_set("memory_limit","3072M");
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);
//check settings
if(!isERPPortalEnabled()){ exit("Error: 'ERP_API_PATIENT_PORTAL' setting is not enabled!"); }

include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/Patient.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/User.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/Facility.php");
require_once (dirname(__FILE__).'/../../library/erp_portal/appointmentrequests.php');
include_once($GLOBALS['srcdir']."/erp_portal/pghd_requests.php");

//get reqs from Pt portal
$oAppoint = new AppointmentRequests();

$OBJRabbitmqExchange = new Rabbitmq_exchange();
$Pghd_requests = new Pghd_requests();
			
$erp_error=array();
//Update request status on portal
if(isset($_GET["op"])){
	try {
		if($_GET["op"] == "aprv"||$_GET["op"] == "dcln"){
		  if($_GET["pghd"] == "1"){
			  //$Pghd_requests->updatePortal($_GET["id"], $_GET["op"]);
		  }else {
			  $oAppoint->updatePortal($_GET["id"], $_GET["op"]);
		  }

		}
		//
		if($_GET["op"]=="refresh"){
		//Get Requests from portal
		$oAppoint->getRequests();
		//$Pghd_requests->getPghdRequests();
		}
	} catch(Exception $e) {
		$erp_error[]='Unable to connect to ERP Portal';
	}
  exit();
}

//create tbl to display requests
$data = $oAppoint->show_requests("popup");

$data2 = '';//$Pghd_requests->show_requests("popup");


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Pt Portal Appointment Requests</title>

    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/landing_page.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.min.js"></script>
    <style>
      #dv_data{  }
      .container {  width: 98%; }
      .glyphicon-refresh {
          color: #1b9e95!important;
          text-align: center;
          font-size: 20px;
      }
      .dv_data_con{
        padding-left: 0px;
        padding-right: 0px;
      }
    </style>
    <script>

    function proc_req(o){
      if($(o).hasClass("btn_aprv")){
        var act = "Approved";
        var op = "aprv";
        var tr_cs="success";
      }else if($(o).hasClass("btn_dcln")){
        var act = "Declined";
        var op = "dcln";
        var tr_cs="danger";
      }else{return;}
      var apid = $(o).data("app_id");
	  var pghd = $(o).data("pghd");
      $("#dvloader").show();
      $.get("get_app_req.php?op="+op+"&id="+apid+"&pghd="+pghd, function(d){
        $("#dvloader").hide();
        if(d==0){
			$(o).parents("tr").removeClass().addClass(tr_cs);
			if(pghd==1) {
				$(o).parents("tr").find("td:nth-child(4)").addClass("text-"+tr_cs).html("<b>"+act+"</b>");
			} else {
				$(o).parents("tr").find("td:nth-child(7)").addClass("text-"+tr_cs).html("<b>"+act+"</b>");
			}
        }else{
          alert("Error occured: "+d);
        }
      }).fail(function() {
        $("#dvloader").hide();
        alert( "Error: could not connect!" );
      });
    }

    function refresh_app_reqs(){
      $("#dvloader").show();
      $.get("get_app_req.php?op=refresh", function(d){
        window.location.replace("get_app_req.php?updtd=1");
        }).fail(function() {
          $("#dvloader").hide();
          alert( "Error: could not connect!" );
        });
    }

    function set_div_hgt(){
      var hgt = parseInt(($(window).height()*80)/100);
      //$("#dv_data").css({"height":hgt+"px"});
    }
    $( window ).resize(function() { set_div_hgt();  });
    $(document).ready(function(){
        $(".btn_aprv, .btn_dcln").on("click", function(){
            proc_req(this);
          });
        $(".glyphicon-refresh").on("click", function(){
            refresh_app_reqs();
          });
        $(".glyphicon-remove").on("click", function(){
            window.close();
          });
        set_div_hgt();
        $("#dvloader").hide();

        <?php if(!isset($_GET["updtd"])){ ?>
        refresh_app_reqs();
        <?php } ?>

      });
    </script>
  </head>
  <body>
    <div class="container mainwhtbox pd10">
      <div class="row purple_bar">
        <div class="col-sm-10">
          <h4>Pt Portal Appointment Requests</h4>
        </div>
        <div class="col-sm-1">
            <button class="btn btn-xs btn-default glyphicon glyphicon-refresh pull-right" title="Refresh Appointment Requests"></button>
        </div>
        <div class="col-sm-1">
            <button class="btn btn-xs btn-default glyphicon glyphicon-remove pull-right" title="Close PopUp"></button>
        </div>
      </div>
      <div class="row">
        <div  class="col-sm-12 dv_data_con">
          <div id="dv_data" class="table-responsive">
            <?php echo $data ; ?>
          </div>
        </div>
      </div>
	  <?php if($data2) { ?>
		  <div class="row purple_bar">
			<div class="col-sm-10">
			  <h4>Pt Portal PGHD Requests</h4>
			</div>
		  </div>
		  <div class="row">
			<div  class="col-sm-12 dv_data_con">
			  <div id="pghd_data" class="table-responsive">
				<?php echo $data2 ; ?>
			  </div>
			</div>
		  </div>
	  <?php } ?>
      <div id="dvloader" class="loader"></div>
    </div>
  </body>
</html>
