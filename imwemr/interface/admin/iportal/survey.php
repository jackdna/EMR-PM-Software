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
require_once("../admin_header.php");
?>
		<script type="text/javascript">
			var arrAllShownRecords = new Array();
			var totalRecords	   = 0;
			var formObjects		   = new Array('phrase_id','phrase');
			function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Loading iPortal Survey...');
				
				if(typeof(s)!='string' || s==''){s = 'Active';}
				s_url = "&s="+s;
				
				if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
				if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f};
				
				oso		= $('#ord_by_field').val(); //old_so
				soAD	= $('#ord_by_ascdesc').val();
				if(typeof(so)=='undefined' || so==''){
					so 		= $('#ord_by_field').val();
				}else{
					$('#ord_by_field').val(so);
					if(oso==so){
						if(soAD=='ASC') soAD = 'DESC';
						else  soAD = 'ASC';
					}else{
						soAD = 'ASC';
					}
					$('#ord_by_ascdesc').val(soAD);
				};
				//so 		= 'pos_prac_code';
				$('.link_cursor span').html('');
				if(soAD=='ASC')	$(currLink).find('span').html(' <img src="../../../library/images/arr_up.gif">');
				else $(currLink).find('span').html(' <img src="../../../library/images/arr_dn.gif">');
				
				so_url='&so='+so+'&soAD='+soAD;
						
				ajaxURL = "ajax_survey.php?task=show_list"+s_url+p_url+f_url+so_url;
				//alert(ajaxURL);//a=window.open(); a.document.write(ajaxURL);
				$.ajax({
				  url: ajaxURL,
				  success: function(r) {
					 //alert(r);//a=window.open();a.document.write(r); ///*dataType: "json",*/
					showRecords(r);
				  }
				});
			}
			function showRecords(r){
			//$('#result_set').html(r+'<hr>end of Response');exit;
				r = jQuery.parseJSON(r);
				result = r.records;
				h='';var no_record='yes';
				if(r != null){
					row = '';
					row_class = '';
					for(x in result){no_record='no';
						s = result[x];
						rowData = new Array();
						row += '<tr class="link_cursor'+row_class+'">';
						for(y in s){
							tdVal = s[y];
							if(y=='survey_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
							rowData[y] = tdVal;
							if(y=='survey_title'){
								row	+= '<td data-label="Name" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
							}
							if(y=='survey_description'){
								row	+= '<td data-label="Description" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
							}
							if(y==''){
								row	+= '<td data-label="">&nbsp;</td>';
							}
						}
						if(row_class==''){row_class=' alt';}else{row_class='';}
						row	+= '<td class="leftborder" onclick="addQuestion(\''+pkId+'\');">&nbsp Click here to add questions</td>';
						totalRecords++;
						row += '</tr>';
						arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
					}
					h = row;
				}
				if(no_record=='yes'){h+="<tr><td colspan='4' style='text-align:center;'>No Record Found</td></tr>";}
				$('#result_set').html(h);		
				top.show_loading_image('hide');
			}
			function addNew(ed,pkId){
				var modal_title = '';
				if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Survey';}
				else {
					modal_title = 'Add New Record';
					$('#adm_epostId').val('');
					document.add_edit_frm.reset();
				}
				$('#myModal .modal-header .modal-title').text(modal_title);
				$('#myModal').modal('show');
				if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
			}
			
			function saveFormData(){
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Saving data...');
				frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
				if($.trim($('#epost_pre_defines').val())==""){
					top.fAlert("Please enter the survay name.");
					top.show_loading_image('hide');
					return false;
				}
				$.ajax({
					type: "POST",
					url: "ajax_survey.php",
					data: frm_data,
					success: function(d) {
						top.show_loading_image('hide');
						if(d=='enter_unique'){
							top.fAlert('Record already exist.');		
							return false;
						}
						if(d.toLowerCase().indexOf('success') > 0){
							top.alert_notification_show(d);
						}else{
							top.fAlert(d);
						}
						$('#myModal').modal('hide');
						LoadResultSet();
					}
				});
				return false;
			}
			
			function deleteSelectet(){
				pos_id = '';
				$('.chk_sel').each(function(){
					if($(this).is(':checked')){
						pos_id += $(this).val()+', ';
					}
				});
				if(pos_id!=''){
					top.fancyConfirm("Are you sure you want to delete?","","window.top.fmain.deleteModifiers('"+pos_id+"')");
				}else{
					top.fAlert('No Record Selected.');
				}
			}
			function deleteModifiers(pos_id) {
				pos_id = pos_id.substr(0,pos_id.length-2);
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Deleting Record(s)...');
				frm_data = 'pkId='+pos_id+'&task=delete';
				$.ajax({
					type: "POST",
					url: "ajax_survey.php",
					data: frm_data,
					success: function(d) {
						top.show_loading_image('hide');
						if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
						else{top.fAlert(d+'Record delete failed. Please try again.');}
					}
				});
			}
			
			function setStatus(rowid,value)
			{
				var dataString = 'task=change_status&rid='+rowid+'&value='+value;
				$.ajax({
					type: "POST",
					url: "ajax_survey.php",
					data: dataString,
					cache: false,
					success: function(response)
					{
						if(response=="true")
						{
							if(value==1)
							{
								$("#status_"+rowid).html('<span style="color:red;"> Deleted </span>');
								$("#status_"+rowid).attr('onClick','setStatus("'+rowid+'","0")');
							}
							else if(value==0)
							{
								$("#status_"+rowid).html('<span style="color:green;"> Active </span>');
								$("#status_"+rowid).attr('onClick','setStatus("'+rowid+'","1")');
							}
							top.alert_notification_show("Record Updated Successfully");
						}
					}
				});
			}
			
			function fillEditData(pkId){
				f = document.add_edit_frm;
				e = f.elements;
				add_edit_frm.reset();
				$('#adm_epostId').val(pkId);
				for(i=0;i<e.length;i++){
					o = e[i];
					if($.inArray(o.phrase,formObjects)){
						on	= o.name;
						//alert(arrAllShownRecords[]);
						v	= arrAllShownRecords[pkId][on];
						if (o.tagName == "INPUT" || o.tagName == "SELECT" ||  o.tagName == "TEXTAREA"){
							if (o.type == "checkbox" || o.type == "radio"){
								oid = on;
								if(v==1)
								{
									$('#'+oid).attr('checked',true);
								}
							} else if(o.type!='submit' && o.type!='button'){
								o.value = v;
							}
						}
					}
				}		
			}
		</script>
	<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="survey_title">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
		<div class="whtbox">
			<div class="table-responsive respotable">
				<table class="table table-bordered adminnw">
					<thead>
						<tr>
							<th style="width:20px; padding-left:8px;">
								<div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
									<label for="chk_sel_all"></label>
								</div>
							</th>
							<th style="width:23%;" onClick="LoadResultSet('','','','survey_title',this);">Name<span></span></th>
							<th style="width:60%;" onClick="LoadResultSet('','','','survey_description',this);">Description<span></span></th>
							<th style="width:15%;"><span></span></th>
						</tr>
					</thead>
					<tbody id="result_set"></tbody>
				</table>
			</div>
		</div>
		<div class="common_modal_wrapper"> 
		<!-- Modal -->
			<div id="myModal" class="modal" role="dialog">
				<div class="modal-dialog"> 
				<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<form name="add_edit_frm" id="add_edit_frm" style="margin:0px;" onSubmit="saveFormData(); return false;">
								<div class="bg6 padd5 bg1">
								<input type="hidden" class="form-control" name="survey_id" id="adm_epostId" >
									<label for="epost_pre_defines" >Title of Survey:</label>
									<input class="form-control" name="survey_title" id="epost_pre_defines" type="text">
									<br />
									<label for="survey_description" >Description of Survey:</label>
									<textarea class="form-control" name="survey_description" id="survey_description"></textarea>
								</div>
                </form>
							</div>
						</div>
						<div id="module_buttons" class="ad_modal_footer modal-footer">
						<button type="button" class="btn btn-success" onClick="saveFormData();">Save</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
					</div>
					
					
				</div>
			</div>
		</div>
	   <div class="common_modal_wrapper"> 
		<!-- Modal -->
			<div id="myModal_question" class="modal" role="dialog">
				<div class="modal-dialog" style="width:58%">  
				<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Add Questions for Survey</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
							<form method="POST" name="questionDetail" id="questionDetail">
								<div id="questionViewBlock"></div>
							</form>
							</div>
						</div>
						<div id="module_buttons" class="ad_modal_footer modal-footer">
						<button id="submitButoon" class="btn btn-success">Save</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
					</div>
					
					</form>
				</div>
			</div>
		</div>
	
    <script type="text/javascript">
	   
		var number = {};
		var quesCounter = "<?php echo $counter; ?>";
		var optCounter = "<?php echo $optCounter+1; ?>";
		var questionIDD = "<?php echo $questionIDD; ?>";
		if(quesCounter == "")
		{
			number.count = 2;
		}else{
			number.count = questionIDD;
		}
			
		if(optCounter == "")
		{
			var n = 1;
		}else{
			var n = optCounter;
		}
		function addRow(ths)
		{

		if(quesCounter != '')
			{
				number.count++;
			}
			var contentStr = "<div class=\"col-sm-12\"><div class=\"row\"><div class=\"col-sm-8 form-group\"><label for=\"quesTitle\">Question</label><input type=\"text\" name=\"quesTitle[]\" class=\"form-control\" id=\"quesTitle\" placeholder=\"Please enter your question here\" /></div><div class=\"col-sm-4 form-group\"><label for=\"quesType\">Question Type</label><select name=\"quesType[]\" onChange=\"OptionToShow(value,this)\" id=\"quesType\" class=\"form-control minimal\"><option value=\"multiple\">Multiple</option><option value=\"single\" selected>Single</option></select><span onclick=\"addRow(this)\" class=\"glyphicon glyphicon-plus\"></span> &nbsp;&nbsp;<span onclick='delCol("+number.count+",this)' class=\"glyphicon glyphicon-remove\"></span></div></div></div><div class=\"clearfix\"></div><div class=\"col-sm-8 form-group\"><label for=\"optionVal\">Options</label><div id='optionSelection_"+number.count+"'><div style=\"margin-top:5px;\" class='inputField' id='"+number.count+"_"+n+"'><input type='text' name='optionVal[]' class=\"form-control\" id=\"optionVal\" placeholder='Please enter your option here'>&nbsp;<span onclick='addMultiCol("+number.count+",this)' class='glyphicon glyphicon-plus'></span></div></div></div>";
			$('#questionViewBlock').append('<div class="question row optioninp" id="questionDetails_'+parseInt(number.count)+'">'+contentStr+'</div>');
			number.count++;
		}
		
		function addMultiCol(id,ths)
		{
			n++;
			var id = $(ths).parent().parent().attr('id');
			id = id.split('_');
			var multiC = "<div style=\"margin-top:5px;\" class='inputField' id='"+id[1]+"_"+n+"'><input type='text' class=\"form-control\" name='optionVal[]' placeholder='Please enter your option here' />&nbsp;<span onclick='addMultiCol("+number.count+",this)' class=\"glyphicon glyphicon-plus\"></span>&nbsp;&nbsp;<span onclick=\"delRow('"+id[1]+"_"+n+"');\" class=\"glyphicon glyphicon-remove\"></span></div>";
			n++;
			$('#optionSelection_'+id[1]).append(multiC);
			
		}
		
		function delRow(optid)
		{
			var optionID = optid;
			var ajaxQuesId = '';
			var questionId = optionID.toString().split("_");
			var question_id =questionId[0];
			var opt_id = questionId[1];
			var data = 'question_id='+question_id+"&opt_id="+opt_id;
			//alert(data);
			$.ajax({
				type: 'POST',
				url: "ajax_survey_question.php?value=deleteOpt",
				data: data,
				success : function(data) {
					if(data == "Done")
					{
						$('#'+question_id+"_"+opt_id+'.inputField').remove();
						n--;
					}else{
						alert("Some error occured. Please try again later...!");
					}
				}
			}); 
		}
		

		function delCol(id,ths){
			var id = $(ths).parent().parent().parent().parent().attr('id');
			id = id.split('_');
			if(optCounter != ''){
			var data = 'question_id='+id[1];
			$.ajax({
				type: 'POST',
				url: "ajax_survey_question.php?value=deleteQues",
				data: data,
				success : function(data) {
				if(data == "Done"){
					$("#questionDetails_"+id[1]+'.question').remove();
						number.count--;
					}
				}
			});
		}else{
			$("#questionDetails_"+id[1]+'.question').remove();
			number.count--;
			}
		}
		
		function addQuestion(id) {
			$('#myModal_question').modal('show');
			$('#survey_id').attr('value',id);
			var url = "ajax_survey_question.php?value=ShowQues";
			var data = 'survey_id='+id;
			$.ajax({
				type: 'POST',
				url: url,
				data: data,
				dataType: 'html',
				success : function(data) {
				var Finaldata = data.split('&&&');
				$("#questionViewBlock").html(Finaldata[0]);
				
					if(Finaldata[1] = 'UpdateCounTER'){
					   $('#submitButoon').attr('onclick','updateQuestion()');
					}else{
					   $('#submitButoon').attr('onclick','addQuestions()');
					} 
				}
				});
		}
		
		function addQuestions() {
			
			var questions = [];
			var quesOpt = [];
			var url = "ajax_survey_question.php?value=SaveQues";
			var validate = "";
			$('#questionDetail').find('input').each(function(idx, elem){
			   if($(elem).val().length == 0){
				  validate = false;
				  $(elem).addClass('formError');
				  
			   }else{
				   validate = true;
			   }
			});
			
			if(validate == false)
			{
				alert('Please fill the required fields');
			}else{
				var optId = 1;
				var quesLength = 0;
				var quesId = "";
				var successConfirm = 0;
				 $('.question').each(function(){
					var quesId = $(this).attr('id');
					var sinQuesId = quesId.split("_");
					optId = sinQuesId[1];
					//alert(optId);
					$('.inputField').each(function(){
						var optionId = $(this).attr('id');
						var sinopTId= optionId.split("");
						var splitOptID = optionId.slice(1);
						if(optId == sinopTId[0])
						{
							//alert(optionId);
							var surveyId = $('#survey_id').val();
							var questitle = $('#'+quesId+' input').val();
							var quesType = $('#'+quesId+' select').val();
							var quesOpt = $('#'+optionId+'.inputField input').val();
							var data = 'questitle='+questitle+"&quesOpt="+quesOpt+"&quesType="+quesType+"&survey_id="+surveyId+"&quesId="+optId+"&quesOptId="+sinopTId[1];
							$.ajax({
								type: 'POST',
								url: url,
								data: data,
								dataType: 'html',
								success : function(data) {
								}
							});
							//alert($('#'+quesId+' input').val() +"------------------"+ $('#'+optionId+'.inputField input').val());  QuesId -----> OptionID
							//alert(optId+"--------"+sinopTId[0]+sinopTId[1]);   QuesId -----> OptionID
						}
					});
				});
				$('.closeBtn').click();
			}
		}			
		
		function updateQuestion(){
			var questions = [];
			var quesOpt = [];
			var url = "ajax_survey_question.php?value=UpdateQues";
			var quesCounter = $('#totalQuesNo').val();
			var validate = "";
			$('#questionDetail').find('input').each(function(idx, elem){
			   if($(elem).val().length == 0){
					validate = false;
					$(elem).addClass('formError');
				}else{
				   validate = true;
			   }
			});
			if(validate == false)
			{
				alert('Please fill the required fields');
			}else{
				var optId = 1;
				var quesLength = 0;
				var quesId = "";
				var successConfirm = 0;
				 $('.question').each(function(){
					var quesId = $(this).attr('id');
					var sinQuesId = quesId.split("_");
					optId = sinQuesId[1];
					$('.inputField').each(function(){
						var optionId = $(this).attr('id');
						var sinopTId= optionId.split("_");
						var splitOptID = optionId.slice(1);
						if(optId == sinopTId[0])
						{
							var surveyId  = $('#survey_id').val();
							var questitle = $('#'+quesId+' input[type="text"]').val();
							var quesType = $('#'+quesId+' select').val();
							var quesOpt = $('#'+optionId+'.inputField input').val();
							var data = 'questitle='+questitle+"&quesOpt="+quesOpt+"&quesType="+quesType+"&survey_id="+surveyId+"&quesId="+optId+"&quesOptId="+sinopTId[1];
							$.ajax({
								type: 'POST',
								url: url,
								data: data,
								success : function(data) {
								}
							}); 
							//alert($('#'+quesId+' input').val() +"------------------"+ $('#'+optionId+'.inputField input').val());  QuesId -----> OptionID
							//alert(optId+"--------"+sinopTId[0]+sinopTId[1]);   QuesId -----> OptionID
						}
					});
				}); $('#myModal_question').modal('hide');
			}
		}
	
		
		LoadResultSet();
		var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
		top.btn_show("ADMN",ar);
		$(document).ready(function(){
			check_checkboxes();
			set_header_title('Survey');
			
			
			$('body').on('shown.bs.modal','#myModal_question',function(){
				top.fmain.set_modal_height('myModal_question');
			});
			
			$('body').on('shown.bs.modal','#myModal',function(){
				top.fmain.set_modal_height('myModal');
			});
			
		});
		show_loading_image('none');
	</script>
<?php 
	require_once('../admin_footer.php');
?>