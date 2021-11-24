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
 * Purpose : List of Variables Shown
 * Access Type: Include
 */

//---GLOBAL FILE INCLUSION---
include_once("../../../../config/globals.php");

//---VARIABLE HELP CSV FILE INCLUSION---
$csvFileName = $GLOBALS['fileroot']."/interface/admin/documents/variable_help/Variable_mapping.csv";

//---FILE CHECKED, IF EXIST ON REQUIRED LOCATION---
if(!file_exists($csvFileName))
{
	die("File not exists: ".$csvFileName);
}

//---READ DATA FROM FILE---
$fileContents = fopen($csvFileName,"r");
$row = 0; //LOOP COUNT

?>

<!---CSS STYLESHEET WORK --->
<style>
<!---SUB CLASSES--->
.sno{ width:2%; }
.imgscroll{ overflow:auto; max-height: 506px; }
.form-control{ font-family:'robotoregular';}
.myclass{overflow: hidden auto; max-height: 548px;padding-bottom:10Px;}
.name{ width:22%; }
.var{ font-family: 'robotobold'; color: #616161; font-weight: 500;text-align:center;width:8%; }
.var_name{ text-align:left;padding-left:3px;font-size:16px; font-family:'roboto'; font-weight:300;font-size:14px; }
.list{ background-color:#673782 !important;color:white; font-size:20px; }
<!---MAIN CLASSES--->
table{ width:100%; font-size: 14px; vertical-align: top;border:1px solid #ddd; }
table td { vertical-align: top;padding-top:5px;padding-bottom:5px;border-top:1px solid #ddd;border-right:1px solid #ddd;text-align:center; }
</style>

<!--- MAIN HEADING --->
<script type="text/javascript">
	set_header_title('Variable Help');
</script>

<!---MAIN TABLE WORK--->
<table align="center" cellpadding="0" cellspacing="0">
	<table class="table table-bordered table-hover adminnw" id="variableTable">
	<!---SUB HEADING ROW--->
	<thead>
		<tr>
			<!-- <th class="sno">S.NO</th> -->
			<th class="name" >
				<div class="row">
					<div class="col-sm-5" >
						<label >VARIABLE NAME</label><span></span>
					</div>	
					<div class="col-sm-7 ">
						<div class="input-group">
							<input type="text" size="15" value="" placeholder="Search..." style="font-weight:none;" id="search_variable" class="form-control" onkeyup="valSearch();" autocomplete="off">
							<label class="input-group-addon pointer" id="search" >
							<i class="glyphicon glyphicon-search " aria-hidden="true" ></i>
							</label>
						</div>
					</div>	
				</div>
			</th>
			<th>COLLECTION</th>
			<th>CONSENT</th>
			<th>CONSULT</th>
			<th>EDU/INS</th>
			<th>PT. DOCS</th>
			<th>OP. NOTE</th>
			<th>RECALL</th>
			<th>PRESCRIPTIONS</th>
			<th>PANELS</th>
			<th>STATEMENT</th>
		</tr>
	</thead>
	<tbody id="searchTable">
	<?php
	
	if(file_exists($csvFileName))
	{
		
		while(($data = fgetcsv($fileContents,10000,',')) !== FALSE)
		{
		
			if($row > 0)
			{ 
					
	?>
	
			<!---DATA ROWS--->
			<tr>
				<!-- <td><?php //echo$data[0]; ?></td> -->
			
				<td class="var_name" onclick="variableImage(<?php echo $data[12] ?>)"><?php echo $data[1]; ?></td>
				<td title="<?php $data[2]=="Yes" ? print 'Collection' : ''; ?>" ><?php echo clearnArrangeText($data[2]); ?></td>
				<td title="<?php $data[3]=="Yes" ? print 'Consent' : ''; ?>"><?php echo clearnArrangeText($data[3]); ?></td>
				<td title="<?php $data[4]=="Yes" ? print 'Consult' : ''; ?>"><?php echo clearnArrangeText($data[4]); ?></td>
				<td title="<?php $data[5]=="Yes" ? print 'Education/Instructions' : ''; ?>"><?php echo clearnArrangeText($data[5]); ?></td>
				<td title="<?php $data[6]=="Yes" ? print 'Pt-docs' : ''; ?>"><?php echo clearnArrangeText($data[6]); ?></td>
				<td title="<?php $data[7]=="Yes" ? print 'OP. Note' : ''; ?>"><?php echo clearnArrangeText($data[7]); ?></td>
				<td title="<?php $data[8]=="Yes" ? print 'Recall' : ''; ?>"><?php echo clearnArrangeText($data[8]); ?></td>
				<td title="<?php $data[9]=="Yes" ? print 'Prescriptions' : ''; ?>"><?php echo clearnArrangeText($data[9]); ?></td>
				<td title="<?php $data[10]=="Yes" ? print 'Panels' : ''; ?>"><?php echo clearnArrangeText($data[10]); ?></td>
				<td title="<?php $data[11]=="Yes" ? print 'Statement' : ''; ?>"><?php echo clearnArrangeText($data[11]); ?></td>
			</tr>
	<?php		
			}
			$row++;
		}
	}
	
	?>

	</tbody>
</table>
<div id="var_modal" class="modal text-center " role="dialog" >
	<div class="modal-dialog modal-lg" style="width:80%;">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary" style="height:45px;">
				<button type="button" class="close" onclick="modelClose()">x</button>
				<h4 class="modal-title myclass" id="modal_title">
				</h4>
			</div>
			<div class="modal-body imgscroll "  id="variableImage" >
				
			</div>
			<div class="modal-footer ad_modal_footer" >
		
			<button type="button" class="btn btn-danger" onclick="modelClose()">Close</button>
			</div>
		</div>
	</div>
</div>

<?php
//---FUNCTION TO RE-ARRANGE DATA---
function clearnArrangeText($data)
{
	$data = str_ireplace("Yes",'<img src="../../../library/images/tick_box.png" />',$data);
	$data = str_ireplace('No','',$data);
	return $data;
}
?>
<script>
//---FUNCTION TO SHOW VARIABLES IMAGES ON VARIABLE NAME CLICK---
function variableImage(data)
{
	$("#var_modal").modal("show");
	$("#variableImage").html('<img src="../../../library/images/var_images/'+data+'.jpg" >');

}
//---SEARCHING ON PRESS ENTER---
$('#search_variable').on("keypress",function(event){
	 if (event.which == 13) {
		 event.preventDefault();
	
		 valSearch();
	 }
});
//---SEARCHING ON KEY PRESS---
function valSearch()
{
	var input, filter, table, tr, td, j, txt;
	input = document.getElementById("search_variable");
	filter = input.value.toUpperCase();
	table = document.getElementById("variableTable");
	tr = table.getElementsByTagName("tr");
	for (j = 0; j < tr.length; j++)
	{
		td = tr[j].getElementsByTagName("td")[0];
		if (td)
		{
		  txt = td.textContent || td.innerText;
		  if (txt.toUpperCase().indexOf(filter) > -1)
		  {
			tr[j].style.display = "";
		  }
		  else 
		  {
			tr[j].style.display = "none";
		  }
		}       
	}
}

function modelClose()
{
	$("#var_modal").modal("hide");
	$(".var_name").removeClass("imgname"); 
	try
	{
		window.stop();
	} 
	catch (exception)
	{
		document.execCommand('Stop');
	}
}

//---MOVE HOVER ROW COLOR ADD,REMOVE---
$(document).ready(function()
{
	$(".var_name").mouseover(function()
	{
		$(this).addClass( "text-success" );
	});
	
	$(".var_name").mouseout(function()
	{
		$(this).removeClass( "text-success" );
	});
	
	//---VARIABLE NAME DISPLAYED ON TOP OF MODAL---
	$(".var_name").on('click',function()
	{
		$(this).addClass("imgname"); 
		var imgname = $(".imgname").html();	
		$("#modal_title").html(imgname);
	});
});	
</script>