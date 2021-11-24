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
require_once("../../../config/globals.php");

	if($_GET['value'] == 'SaveQues')
	{
		$validate = 0;
		$quesTitle = $_POST['questitle'];
		$quesType = $_POST['quesType'];
		$quesOpt = $_POST['quesOpt'];
		$survey_id = $_POST['survey_id'];
		$question_id = $_POST['quesId'];
		
		$quesTitleSelect = imw_query("select * from survey_question where question_id = $question_id AND survey_id = $survey_id");
		if(imw_num_rows($quesTitleSelect) > 0)
		{
			$row = imw_fetch_array($quesTitleSelect);
			$question_id = $row['question_id'];
			$optionInsert = imw_query("Insert into survey_option(survey_option_description ,question_id,sort_id,survey_id)VALUES('$quesOpt','$question_id',0,$survey_id)");
			$validate++;
			
		}else
		{
			$quesTitleInsert = imw_query("Insert into survey_question(question_id,question_description,question_type,survey_id)VALUES('$question_id','$quesTitle','$quesType',$survey_id)");
			if($quesTitleInsert)
			{
				$optionInsert = imw_query("Insert into survey_option(survey_option_description ,question_id,sort_id,survey_id)VALUES('$quesOpt','$question_id',0,'$survey_id')");
				$validate++;
			}
		
		}
		if(count($validate) > 0)
		{
			echo "Done";
			exit();
		}else{
			echo "Fail";
			exit();
		} 
		
	}

	if($_GET['value'] == 'UpdateQues')
	{
 		$validate = 0;
		$quesTitle = $_POST['questitle'];
		$quesType = $_POST['quesType'];
		$quesOpt = $_POST['quesOpt'];
		$survey_id = $_POST['survey_id'];
		$question_id = $_POST['quesId'];
		$option_id = $_POST['quesOptId'];
		
		$quesTitleSelect = imw_query("SELECT * FROM survey_question WHERE survey_id = '$survey_id' AND question_id = '$question_id' AND question_del_status = '0'");
			
		if(imw_num_rows($quesTitleSelect) > 0)
		{
			$row = imw_fetch_array($quesTitleSelect);
			if($quesTitle == $row['question_description'] && $quesType == $row['question_type'] && $question_id == $row['question_id'])
			{
				$optionQuery = imw_query("select * from survey_option where question_id = $question_id AND survey_option_id = '$option_id' AND survey_id = '$survey_id' AND option_del_status = '0'");
				if(imw_num_rows($optionQuery) > 0)
				{
					$optionInsert = imw_query("update survey_option set survey_option_description = '$quesOpt' where question_id = '$question_id' AND survey_option_id = '$option_id' AND survey_id = '$survey_id'");
				}else{
					$optionInsert = imw_query("Insert into survey_option(survey_option_description ,question_id,sort_id,survey_id)VALUES('$quesOpt','$question_id',0,'$survey_id')");
				}
				
			}
			else
			{
				$optionInsert = imw_query("update survey_question set question_description = '$quesTitle',question_type = '$quesType' where question_id = '$question_id' AND survey_id = '$survey_id'");
			}
			
			if($quesType != $row['question_type'])
			{
				$optionInsert = imw_query("update survey_question set question_type = '$quesType' where question_id = '$question_id' AND survey_id = '$survey_id'");
			}
			
			$validate++;
			
		}
		else
		{
			$quesTitleInsert = imw_query("Insert into survey_question(question_id,question_description,question_type,survey_id)VALUES('$question_id','$quesTitle','$quesType',$survey_id)");
			
			if($quesTitleInsert)
			{
				$questionIDD = $question_id;
				$optionInsert = imw_query("Insert into survey_option(survey_option_description ,question_id,sort_id,survey_id)VALUES('$quesOpt','$questionIDD',0,'$survey_id')");
				$validate++;
			}
		
		}
		if(count($validate) > 0)
		{
			echo "Done";
			exit();
		}else{
			echo "Fail";
			exit();
		} 
	}
	
	if($_GET['value'] == 'SaveQues')
	{	
		$quesTitle = $_POST['questitle'];
		$quesType = $_POST['quesType'];
		$quesOpt = $_POST['quesOpt'];
		$survey_id = $_POST['survey_id'];
		$quesTitleSelect = imw_query("select * from survey_question where question_description LIKE '%$quesTitle%'");
		if(imw_num_rows($quesTitleSelect) > 0)
		{
			$row = imw_fetch_array($quesTitleSelect);
			$question_id = $row['question_id'];
			$optionInsert = imw_query("Insert into survey_option(survey_option_description ,question_id,sort_id)VALUES('$quesOpt','$question_id',0)");
		}else{
			$quesTitleInsert = imw_query("Insert into survey_question(question_description,question_type,survey_id)VALUES('$quesTitle','$quesType',$survey_id)");
			
			if($quesTitleInsert)
			{
				$question_id = imw_insert_id();
				$optionInsert = imw_query("Insert into survey_option(survey_option_description ,question_id,sort_id)VALUES('$quesOpt','$question_id',0)");
			}
		}
		exit();
	}

	if($_GET['value'] == 'deleteQues')
	{
		$question_id = $_POST['question_id'];
		$query = imw_query("update survey_question set question_del_status = '1' where question_id = $question_id");
		if($query)
		{
			imw_query("update survey_question set option_del_status = '1' where question_id = $question_id");
			echo "Done";
			exit();
		}
	}
	
	if($_GET['value'] == 'ShowQues')
	{
		$survey_id = $_POST['survey_id'];
		$counter = '';
		$optCounter = '';
		$questionIDD = '';
		
		$questionQuery = imw_query("select * from survey_question where survey_id = '$survey_id' AND question_del_status = '0' ORDER BY question_id ASC");
		if(imw_num_rows($questionQuery) > 0) {
			$counter = 1;
			$optCounter = 1;
		while($rowQues = imw_fetch_array($questionQuery))	{
			$quesId = $rowQues['question_id'];
			$questionIDD = $quesId;
		?>
		<div class="question row optioninp" id="questionDetails_<?php echo $quesId; ?>">
			<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-8 form-group"><label for="quesTitle">Question</label>
							<input type="text" name="quesTitle[]" class="form-control" id="quesTitle" value="<?php echo $rowQues['question_description'] ?>" placeholder="Please enter your question here" />
						</div>
						<div class="col-sm-4 form-group"><label for="quesType">Question Type</label>
							<select name="quesType[]" class="questionTypeVal form-control minimal" id="quesType" onChange="OptionToShow(value,this)">
								<option value="multiple" <?php if($rowQues['question_type'] == 'multiple'){echo "selected";} ?>>Multiple</option>
								<option value="single" <?php if($rowQues['question_type'] == 'single'){echo "selected";} ?>>Single</option>
							</select>
							<input type="hidden" id="survey_id" value="<?php echo $survey_id; ?>" />
							<input type="hidden" id="counter" value="<?php echo $counter; ?>" />
							<span onclick="addRow(this)" class="glyphicon glyphicon-plus"></span>
							<?php if($counter > 1){ ?>
								<span onclick='delCol("<?php echo $quesId; ?>",this)' class="glyphicon glyphicon-remove"></span>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
		<div class="col-sm-8 form-group" id="optionSelection_<?php echo $quesId; ?>">
		<?php
		//	$var = 0;
			$optionQuery = imw_query("SELECT * FROM survey_option WHERE question_id = '$quesId' AND survey_id = '$survey_id' AND option_del_status = '0' ORDER BY survey_option_id ASC");
		?>	
			<label for="optionVal">Options</label>
		<?php		
		if($optionQuery)
			{

				$count = imw_num_rows($optionQuery);
				for($i=1;$i<=$count;$i++)
				{
					$row[$i] = imw_fetch_array($optionQuery);
					$divData = "<div style=\"margin-top:5px;\" class='inputField' id='".$quesId."_".$row[$i]['survey_option_id']."'>
					<input type='text' class='form-control' name='optionVal[]' value='".$row[$i]['survey_option_description']."' >&nbsp;<span onclick='addMultiCol(".$quesId.",this)' class='glyphicon glyphicon-plus'></span>";
					if($i > 1)
					{
						$divData .="&nbsp;&nbsp;<span onclick=\"delRow('".$quesId."_".$row[$i]['survey_option_id']."');\" class='glyphicon glyphicon-remove'></span>";
					}
					$optCounter++;
					echo $divData."</div>";
				}
				
			} ?>
			</div>
		</div>
			
		<?php
		 $counter++;
		}
			?>
		<script type="text/javascript">
			var number = {};
			var quesCounter = "<?php echo $counter; ?>";
			var optCounter = "<?php echo $optCounter+1; ?>";
			var questionIDD = "<?php echo $questionIDD; ?>";
			if(quesCounter == "")
			{
				number.count = 1;
			}else{
				number.count = questionIDD;
			}
			
			
			if(optCounter == "")
			{
				var n = 1;
			}else{
				var n = optCounter;
			}	
		</script> 
		<?php 
		} else{?>
			<input type="hidden" id="survey_id" value="<?php echo $survey_id; ?>">
			<div class="question row optioninp" id="questionDetails_1">
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-8 form-group"><label for="quesTitle">Question</label>
							<input type="text" name="quesTitle[]" class="form-control" id="quesTitle" placeholder="Please enter your question here" />
						</div>
						<div class="col-sm-4 form-group"><label for="quesType">Question Type</label>
							<select name="quesType[]" onChange="OptionToShow(value,this)" id="quesType" class="form-control minimal">
								<option value="multiple">Multiple</option>
								<option value="single" selected>Single</option>
							</select>
							<span onclick="addRow(this)" class="glyphicon glyphicon-plus"></span>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>	
				<div class="col-sm-8 form-group">
					<label for="optionVal">Options</label>
					<div id="optionSelection_1"><div class='inputField' id='1_0'><input class="form-control" type='text' id="optionVal" name='optionVal[]' placeholder='Please enter your option here'>&nbsp;<span onclick='addMultiCol("1",this)' class="glyphicon glyphicon-plus"></span></div></div>
				</div>
			</div>
			
			<!--
			
			<div class="question bg6 padd5 bg1" id="questionDetails_1" style="padding-left:50px;">
				<label><b>Question:</b> </label><input type="text" name="quesTitle[]" placeholder="Please enter your question here" style="width:400px;margin-top:5px;margin-bottom:5px;padding-top:3px;padding-bottom:3px">
				<label><b>Question Type:</b></label>
				<select name="quesType[]" onChange="OptionToShow(value,this)" style="margin-top:5px; margin-bottom:5px;">
				<option value="multiple">Multiple</option>
				<option value="single" selected>Single</option>
				</select>
				<span onclick="addRow(this)"><img align="absmiddle" style="display: inline; cursor: pointer;" alt="Add More" src="../../../images/add_medical_history.gif"></span>
				<div id="optionSelection_1"><div class='inputField' id='1_0'><label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Option: </label><input  type='text' name='optionVal[]' placeholder='Please enter your option here' style='width:400px;margin-top:5px;margin-bottom:5px;padding-top:3px;padding-bottom:3px'>&nbsp;<span onclick='addMultiCol("1",this)' style='padding-left:3px;padding-right:3px'><img style="display: inline; cursor: pointer;" alt="Add More" src="../../../images/add_medical_history.gif" align="absmiddle"></span></div>
				</div>	
			</div> -->
		<?php
		
		} 
		?>
		<?php	
		exit();
	}
	
	if($_GET['value'] == 'deleteOpt')
	{
		$opt_id = $_POST['opt_id'];
		$question_id = str_split($_POST['question_id']);
		$question_id = $question_id[0];
		$query = imw_query("update survey_option set option_del_status = '1' where question_id = $question_id AND survey_option_id = $opt_id");
		
		if($query)
		{
			echo "Done";
			exit();
		}else{
			echo "Fail";
			exit();
		}
	}
?>