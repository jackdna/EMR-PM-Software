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
File: co_done_popup.php
Purpose: Main interface of iMedicMonitor 
Access Type: Direct File
*/
include_once("../globals.php");
include_once("common_functions.php");
if(!$_SESSION['login_sucess']){
	header("Location:../login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>iMedic Monitor main page</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="imagetoolbar" content="no" />
   	<meta name="viewport" content="width=device-width, maximum-scale=1.0;" />
	<link rel="stylesheet" type="text/css" href="../css/screen_styles.css?<?php echo filemtime('../css/screen_styles.css');?>" />
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/script.js?<?php echo filemtime('../js/script.js');?>"></script>
	<script type="text/javascript" src="../js/common.js?<?php echo filemtime('../js/common.js');?>"></script>
	<script type="text/javascript" src="../js/jquery.contextMenu.js"></script>
	<script type="text/javascript">
	var arDim = innerDim(); var page_loader2 = new Object; var allTimer =  new Object;
	$(document).ready(function(){
		arDim = innerDim();
		initDisplay(arDim,'co');
	});
	
	function room_priority_change(task,sch_id){
		//alert(task+'::'+sch_id);
		url = "change_pt_room_priority.php?sch_id="+sch_id+"&task="+escape(task);
		$.ajax({type:"GET",url:url,success:function(r){
				clearInterval(page_loader2);
				getSchData();								
			}
		});
		
	}

	function getSchData(){	
		if(typeof(page_loader2)=='number'){
			clearInterval(page_loader2);	
		}	
		url_var = "appts.php";
		$.ajax({
			type: "POST",
			url: url_var,
			dataType: "xml", 
			success: function(xml){
						//alert(xml);
						$("#checked_out_pts tbody").html('');
						var snotech = 1;
						$(xml).find("pt").each(function(){
							var color_class = "";
							if($(this).find("chart_opened").text() == "yes" && $(this).find("tech_click").text() > 0){
								color_class = "green_bg";
							}
							if($(this).find("waiting_4long").text() == "1" && $(this).find("ready4DrId").text() == "0"){
								color_class = "red_bg";
							}
							if($(this).find("st").text() == "11" || $(this).find("pt_with").text() == "6"){
								co_td_val	= $(this).find("co").text();
								if($(this).find("st").text() != "11" && $(this).find("pt_with").text() == "6"){
									co_td_val='Done <a href="#" onclick="room_priority_change(\'task_4\',\''+$(this).find("id").text()+'\');">(Undo)</a>';
								}
								
								chkout_html  = '<tr class="'+color_class+'" sch_id="'+$(this).find("id").text()+'">';
								chkout_html +='<td>'+snotech+'</td>';
								chkout_html += '<td>'+$(this).find("tm").text()+'</td>';
								chkout_html += '<td class="pickTime">'+$(this).find("ci").text()+'</td>';
								chkout_html += '<td class="pickTime2">'+co_td_val+'</td>';
								chkout_html += '<td>'+$(this).find("name").text()+' - '+$(this).find("pid").text()+'</td>';
								chkout_html += '<td>'+$(this).find("proc").text()+'</td>';
								chkout_html += '<td>'+$(this).find("docnm").text()+'</td>';
								chkout_html += '<td>'+$(this).find("oprnm").text()+'</td>';
								chkout_html += '<td class="message_div">'+$(this).find("msg").text()+' '+$(this).find("doctor_mess").text()+'&nbsp;</td>';
								chkout_html += '<td class="showTime">&nbsp;</td>';
								chkout_html += '</tr>';
								$("#checked_out_pts tbody").append(chkout_html);
								snotech++;
							}
						});
						StartTimers();
						page_loader2 = setInterval("getSchData()",30000);	//30 sec
					}
	   });
	}
	
	function StartTimers(){
		$("#checked_out_pts tbody tr").each(function(){
			oT = $(this).find("td.pickTime").text(); //original time value of event.
			oT = oT.trim();
			if(oT.length==8){
				arr_oT = oT.split(" ");			
				oT = arr_oT[0];
				arOt = oT.split(":");
				oH = arOt[0]; //original Hour
				if(arr_oT[1]=="PM" && (oH.length==1 || oH<10)){
					oH = parseInt(oH)+12;
				}
				oM = parseInt(arOt[1]); //original Minute

				cT = $(this).find("td.pickTime2").text(); //original time value of event.
				cT = cT.trim();
				if(cT.length==8){
					arr_cT = cT.split(" ");			
					cT = arr_cT[0];
					arCt = cT.split(":");
					cH = arCt[0]; //original Hour
					if(arr_cT[1]=="PM" && (cH.length==1 || cH<10)){
						cH = parseInt(cH)+12;
					}
					cM = parseInt(arCt[1]); //original Minute
					var addClass = 'greenText';
					dH = cH-oH; //getting hours difference.
					//alert(dH+' :: '+cH+'-'+oH);
					if(cM>oM){//manipulating minutes.
						dM = cM-oM;
					}else{
						dM = 60-(oM-cM);
						dH--;
					}
					if(dM==60){
						dM="00";
						dH++;
					}
					dM = Math.abs(dM);
					if(dM<10){dM='0'+dM;}
					if(dH<10){dH = '0'+dH;}
					if(dH != '00'){addClass='redText';}
					else if(dM>=30 && dH=='00'){addClass='orangeText';}
					else if(dM<30){addClass='greenText';}
		
					timeString = '<span class="'+addClass+'">'+dH+':'+dM+'</span>';
					$(this).find("td.showTime").html(timeString);
				}
			}
		});
	}

	</script>
    <style type="text/css">
	#checked_out_pts_header thead th{border:none;}
	.pickTime, .pickTime2{text-align:center;}
	</style>
</head>
<body>
<!------------TITLE BAR START------------>
<div id="topbar">
	<div class="bigText ml10">Done/Co</div>
</div>


<div class="section">
	<table id="checked_out_pts_header" class="table_collapse w100per">
	 <thead>
	  <tr>
	   <th style="width:4%;">#</th>
	   <th style="width:7%;">Appt.</th>
	   <th style="width:7%;">CI Time</th>
	   <th style="width:7%;">CO/Done</th>
	   <th style="width:20%;">Patient Name</th>
	   <th style="width:10%;">Procedure</th>
	   <th style="width:12%;">Provider</th>
	   <th style="width:12%;">Technician</th>
	   <th style="width:auto;">Message</th>
	   <th style="width:8%;">Timer</th>
	   <th style="width:15px;"></th>
	  </tr>
	 </thead>
	</table>
</div>
<div class="section" style="height:250px; overflow-x:hidden; overflow-y:auto;">
	<table id="checked_out_pts" class="table_collapse w100per tablesorter">
	 <thead>
	  <tr>
	   <th style="width:4%;"></th>
	   <th style="width:7%;"></th>
	   <th style="width:7%;"></th>
	   <th style="width:7%;"></th>
	   <th style="width:20%;"></th>
	   <th style="width:10%;"></th>
	   <th style="width:12%;"></th>
	   <th style="width:12%;"></th>
	   <th style="width:auto;"></th>
	   <th style="width:8%;"></th>
	  </tr>
	 </thead>
	 <tbody>
	 </tbody>
	</table>
</div>


<!------------PAGE BOTTOM START------------>
<div id="page_bottom_bar"><input type="button" class="btn_normal" id="btn_close" title="Close this window" value="&nbsp; &nbsp; Close &nbsp; &nbsp;" onClick="window.close();">Copyrights &copy; 2006 - <?php echo date("Y"); ?> imwemr &reg; All rights reserved.</div>

<!--------MESSAGES EDITOR---------->
<div class="msgEditor bg1">
	<textarea class="m5" cols="20" rows="2" style="height:50px; width:250px;"></textarea>
	<input type="hidden" name="hidd_sch_id" id="hidd_sch_id" value="">
	<input type="button" class="btn_ok m5" value="Update" onClick="saveMessages();" /> &nbsp; &nbsp; &nbsp;
	<input type="button" class="btn_normal m5" value="Cancel" onClick="hideEditMessages();" />
</div>
<script>
	getSchData();
</script>
</body>
</html>