<!-- TEST -->
	<div class="searchexam">
		<div class="searchexamheader">
		<form id="frmSearch" name="frmSearch" onsubmit="return false;" class="form-inline">
		<h2>Search Exam / Test Diagnosis</h2><span class="glyphicon glyphicon-remove cur_hnd" onclick="closePopUps()"></span>
		<div class="form-inline">
		<input type="text" class="form-control" name="elem_findSearch" placeholder="" size="8"> 
		<select name="elem_findPhy" onclick="stopClickBubble();" class="form-control minimal">
			<option value="Exam">Exam</option>
			<option value="Provider">Provider</option>
			<option value="DxCode">Dx Code</option>
			<option value="CCHx">CC & Hx.</option>
		</select> 
		<select name="elem_findStatus" onclick="stopClickBubble();" class="form-control minimal">
		  <option value="">All</option>
			<option value="Finalized">Finalized</option>
			<option value="Purged">Purged</option>
			<option value="Active">Active</option>
		</select>
		<!--
		</div>
		<div class="clearfix"> 
		-->
			<button name="elem_findButton" id="elem_findButton" class="gobut" type="button" onclick="showChartNotesTree('srch');">GO</button> 
			<button name="elem_AllButton" id="elem_AllButton" class="allbut" type="button" onClick="showChartNotesTree(2);">All</button>
		</div>
		</form>
		</div>
		<div class="clearfix"></div>
		
		<div id="sec_exams" class=" table-responsive">
		
		<?php		
		
		//echo "<pre>";
		//print_r($arrAllCharts);
		
		
		if(count($arrAllCharts)>0){
		?>
		<table class="table table-striped">
		<?php
		foreach($arrAllCharts as $k_arrChart => $v_arrChart){
			$loadedChart = $purge_statusStrike = ""; 
			$highlighted= $dos = $idActiveLabel = $clrVisit = "";
			$dos2 = $chartStatus = $ptVisitTestTmp2 = $lastModDt = "";
			$temp = $typeChart = $form_id = $chartStatus = $releaseNum = "";
			$chartStatus2 = $ptVisitTest = $initProvId = $serverAbbr = $purge_user = "";
			$ChartImg=$scan_id=$docTitle2=$idActiveLabel="";
			$arrPlans = array();
			$arr_exm_test = array();
			
			extract($v_arrChart);
		?>
		<tr><td colspan="2" class="<?php echo $loadedChart; ?>">
			
			<?php
			if(!empty($ChartImg)){
			?>	
				<span data-toggle="tooltip" title="<?php echo $dos2; ?>"  onclick="sl_showopts(this);"><?php echo $dos; ?></span>
				<span <?php echo $idActiveLabel; ?> class="vInfo" data-toggle="tooltip" title="<?php echo $docTitle2; ?>" 
					onclick="<?php echo $temp; ?>showPrevChartsImags('<?php echo $ChartImg; ?>', '<?php echo $scan_id; ?>')"><?php echo $chartStatus2."".$ptVisitTest; ?></span>
			<?php
			}else{
				
			?>
			
			<span class="<?php echo $purge_statusStrike; ?>">
			<span class="<?php echo $highlighted; ?>" onclick="sl_showopts(this);" ><?php echo $dos; ?></span>
			<span <?php echo $idActiveLabel; ?> class="vInfo <?php echo $clrVisit; ?>"
				data-toggle="tooltip" title="<?php echo "$dos2 $chartStatus $ptVisitTestTmp2 $lastModDt "; ?>"				
				onclick="<?php echo $temp; ?>showFinalize('<?php echo $typeChart; ?>','<?php echo $form_id; ?>','<?php echo trim($chartStatus); ?>','<?php echo $releaseNum; ?>');" >
				<?php echo "$chartStatus2 $ptVisitTest $initProvId $serverAbbr "; ?></span>
				</span>
				<?php echo "$purge_user"; ?>
			
			<?php
			}//end else
			?>	
				
			<?php
				//Ammendments--
				if(in_array("Amendment", $arrPlans)){
			?>
				<div class="li_amend" onclick="showFinalize('Amendment','<?php echo $form_id; ?>','<?php echo trim($chartStatus); ?>','<?php echo $releaseNum; ?>')">Amendment</div>
			<?php
				}
			?>
			
			<!-- TESt -->
			<ul class="list-unstyled">
			
				<?php
				// FormId Exams --
				if(count($arrPlans)>0){
					foreach($arrPlans as $keyPln=>$valPln){
						if($valPln=="Amendment")continue;						
				?>		
						<li onclick="showFinalize('<?php echo $valPln; ?>','<?php echo $form_id; ?>','<?php echo trim($chartStatus); ?>','<?php echo $releaseNum; ?>')\"><?php echo $valPln; ?></li>
				<?php		
					}
				}
				?>
			
				<?php
				if(count($arr_exm_test)>0){	
				foreach($arr_exm_test as $k_exm_test => $v_exm_test){
					extract($v_exm_test);
					$tmp_nm = $name;
					if(!empty($tmpSubArrNM)){$tmp_nm = $tmpSubArrNM;}
				?>
				<li onclick="showFinalize('<?php echo $name; ?>','0','<?php echo trim($chartStatus); ?>','<?php echo $releaseNum; ?>',0,'<?php echo $testid; ?>')" ><?php echo $name; ?></li>
				<?php
				}
				}
				?>	
			</ul>
			<!-- TESt -->
			
			
		</td></tr>
		
		<?php
		}
		?>
		</table>
		<?php
		}
		?>
		
		</div>
		<div class="clearfix"></div>
		
		<div class="testhead">Tests</div>
		
		<div id="sec_tests" class=" table-responsive">
		<?php
		if(count($arPtTests)>0){
		?>
		<table class="table table-striped">
		<?php
		foreach($arPtTests as $dt => $ardttest){
			if(count($ardttest)>0){			
			$tmpdt = $dt; 	
			foreach($ardttest as $k_ardttest => $v_ardttest){			
			extract($v_ardttest);
		?>
		<tr>
		<td><?php echo $tmpdt; ?></td>
		<td>
			<span class="<?php echo $purge_statusStrike; ?>" >
			<span onclick="showFinalize('<?php echo addslashes($val); ?>','0','0','0','0','<?php echo $tst_id; ?>')" ><?php echo $tst_name; ?></span>
			<span id="flgTest_<?php echo $tst_id; ?>" class="<?php echo $flgClass; ?>" ></span>
			</span>
			<?php echo $purge_user; ?>
		</td>
		</tr>
		<?php
			$tmpdt = "";//
			}
			}
		}
		?>

		</table>
		<?php
		}
		?>
		</div>


	</div>
<!-- TEST -->