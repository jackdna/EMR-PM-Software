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

function reportLogicInfo($report,$typ,$width)
{
	switch($report)	
	{
		case'productivity'://Practice analytics
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<div class="infoSubTitle">DOS Search:</div>Displays charges, payments, adjustments and balance of encounters whose DOS falls between searched date range.<br>
						 Deleted amounts are not included.							
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">DOT/DOR Search:</div>If <strong>DOT</strong> is searched then report display all transactions between
						date range even that are deleted or non-deleted.<br>
						In case of <strong>DOR</strong> search report display only active transactions between search date range. 
						Suppose if a transaction done and then deleted between selected date range then report does not display 
						the same but if that transaction deleted after selected date range then report display the same 
						transaction as non-deleted.<br>
						Balance displays for every fetched encounter.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">No Del Amts.:</div>Option works for DOT search. In DOT search report display all transactions between selected date range even deleted or non-deleted.<br> 
						While selecting on “No Del Amt.” option report display transactions which are non-deleted between selected 
						date range. In this case if a transaction deleted after date range then report display the same but 
						if transaction is done and deleted between selected date range the report does not display the transaction.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Billing location/Pay location:</div>If option <strong>billing location</strong> is selected 
						then search is done based on billing location of encounter and result display billing location facilities.<br>
						<strong>Pay location</strong> works only for DOT and for DOR. If selected then search is done based on pay locations of 
						transactions and result displays encounters by pay locations rather than facility or billing location. 
						So in this case same encounter can display on different pay locations.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Registered Facility:</div>If option selected then report search all patients 
						having selected demographic facility and result display registered facility of patient rather than encounter
						facility. From options pay location, billing location and registered facility only one can be selected at a time.						
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Not Posted Amounts:</div>Fetches only in case of DOT search. 
						It display charges of encounters which have DOS between selected date range but yet not posted and no 
						any adjustment and payment done for these encounters.						
						<div class="infoDataLine"></div>
						
						<div class="infoSubTitle">Voided Transactions Block:</div>Display only for search of DOT or DOR.<br>
						If <strong>DOT</strong> searched then check all transactions which are deleted between selected date range. 
						But if “No Del Amt” selected then report fetch all transactions deleted between selected range but 
						their DOT/First posted date is before selected date range.<br>
						If <strong>DOR</strong> searched then check all encounters which are fetched by non-deleted block and check if any 
						transaction of these encounters deleted in future date then report display that transaction. 
						No del amt option has no effect on DOR search.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Gross Coll. Ratio:</div>Display if searched by DOS with summary view and based on below formula.<br>
						Gross Coll. Ratio = (Patient Paid * 100) / Charges
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Net Coll. Ratio:</div>Display if searched by DOS with summary view and based on below formula.<br>
						Net Coll. Ratio = ((Patient Paid + Adjustments) * 100) / Charges Amt
					</div>
				</div>';
			$str.='<!-- END -->';
				
			break;
		case'cpt_analyses'://cpt analysis
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						This report will show a list of patient encounters and the total CPT codes allocated. For example: for 11 encounters, there may be 19 CPT codes. The report will also display the financials of those encounters.
						Essentially, this report breaks down charges by CPT Code. It will help you determine how much revenue each procedure brings to a practice.
						<div class="infoDataLine"></div>
						
						<div class="infoSubTitle">Provider and Crediting Provider:</div>Provider refers to billing provider in enter charges screen whereas crediting provider is 
						refer to crediting provider field. Filter “Exclude where billing and crediting providers are same” available to exclude those encounters 
						where billing and crediting physicians are same.
						<div class="infoDataLine"></div>
						
						<div class="infoSubTitle">DOS Search:</div>Displays charges, payments, adjustments and balance of encounters whose DOS falls between searched date range.
						 Deleted amounts are not included.							
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">DOT/DOR Search:</div>Search display charges based on first posted date and all other amounts display based on 
						selected DOT/DOR. All transactions between selected date range display including deleted and non-deleted transactions.<br>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Voided Records:</div>Block display all deleted transactions between selected date ranges. 
						It may include transactions done out of date range and also the transactions done and then deleted between selected date ranges. 
						Block display only for DOT or DOR search
						<div class="infoDataLine"></div>
						In detail view heavy data output automatically converted to CSV zipped file when browser is unable to display such load of data.
					</div>
				</div>';
				
			if($typ=='tpl')$str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			else $str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			
			$str.='<!-- END -->';
				
			break;
		case'yearly'://yearly
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<div class="infoSubTitle">DOS Search:</div>Displays charges, payments, adjustments and balance of encounters falls under DOS of selected years.
						Deleted charges, payments and adjustments are not included.<br>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">DOP Search:</div>Displays payments and adjustments based on DOP which falls between searched date range.<br>
						Charges are based on first posted date which falls between searched date range.<br>
						Credit amount and balances fetched of all charges, payment and adjustment encounters.<br>
						<div class="infoDataLine"></div>					
						<div class="infoSubTitle">Appt Kept Column:</div>Displays number of appointments of physicians but facility search is not considered in this.<br>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Other Logics based on DOP:</div><br>*Del date search is based on last date of search criteria. So there may be some difference of amounts for individual month compare to other reports.<br><br>
						*Report is feching last default write-off amount. For example default write-off amount is done in Jan and in Feb and in March.
						So all three months will display default write-off and physician total is sum of all these three.
						But in grand total report is considering only last write-off so grand total will avoid Jan and Feb write-off and consider only March write-off amount.
					</div>
				</div>';
				
			if($typ=='tpl')$str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			else $str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			
			$str.='<!-- END -->';

			break; 
		case'reportLedger'://ledger
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						This report can be used to balance out checks.
						The Ledger is an extremely versatile report. It is a complete record of all financial transactions. 
						This report is used to balance what the billers have posted. Billers can easily locate batches and/or payments. 
						User can quickly see how much revenue belongs to different departments that have been created within the system.
						You can run the report either by batch or check number.<br><br>
						Report display results in blocks of posted, not posted and re-submitted. All the charges, payments and adjustment also display in department 
						wise under department block. 
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">DOS Search:</div>Displays encounters based on DOS which falls between searched date range.<br>
						All the payments, adjustments of these encounters displays. Deleted amounts are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">DOC Search:</div>Displays encounters based on first posted date which falls between searched date range.<br>
						All the payments, adjustments and balance of these encounters displays. Deleted amounts are not considered.
						<div class="infoDataLine"></div>						
						<div class="infoSubTitle">DOT/DOR Search:</div>Displays payments and adjustments based on DOR or DOT which falls between 
						searched date range based on selected option. Charges are fetched based on first posted date. 
						Transactions considered which are deleted after selected date range.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Batch Search:</div>Displays charges, payments and adjustments of encounters exists in selected batch.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Billing location/Pay location:</div>If option billing location is selected then search is done based on 
						billing location of encounter and result display billing location facilities.<br>
						Pay location works only for DOT and for DOR. If selected then search is done based on pay locations of transactions and 
						result displays encounters by pay locations rather than facility or billing location. 
						So in this case same encounter can display on different pay locations.						
					</div>
				</div>
				<!-- END -->';
				
			break;
		case'provider_poductivity'://Revenue -> provider monthly
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<div class="infoSubTitle">Appointments:</div>Displays appointments excluding appointment status deleted, rescheduled, cancel, noshow.<br>
						Start date is the same date as start date in searched criteria.	End date is the last date of searched end month.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Total Charges:</div>Are based on DOS and as per selected criteria of Groups, Physician, Facility, Appointment Type, Procedures.<br>
						Start date is the same date as start date in searched criteria.	End date is the last date of searched end month.<br>
						Deleted charges are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Summary Charges:</div>Are based on DOS and as per selected criteria of Groups, Physician, Facility, Appointment Type and we do not consider selected fields of Procedures.<br>
						Start date is first date of searched start month. End date is last date of searched end month.<br>
						Deleted charges are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Total Payments:</div>Are based on DOT/DOP and as per selected criteria of Physician, Facility and we do not consider selected fields of Group, Appointment Type, Procedures.<br>
						Start date is first date of searched start month. End date is last date of searched end month.<br>
						Deleted payments are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Summary Payments:</div>Are based on DOT/DOP and as per selected criteria of Physician and we do not consider selected fields of Group, Facility, Appointment Type, Procedures.<br>
						Start date is first date of searched start month. End date is last date of searched end month.<br>
						Deleted payments are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">A/R Aging:</div>Displays insurance and patient balance as per selected criteria of Physician and we do not consider Group, Facility and Procedures.
					</div>
				</div>';
				
			if($typ=='tpl')$str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			else $str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			
			$str.='<!-- END -->';
			break;
		case'ref_phy_monthly'://Revenue -> ref Phy monthly
			$str='<!-- INFO DIV -->				
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<div class="infoSubTitle">Physician Search:</div>Primary and secondary both physician are checked for encounters.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Appointments:</div>Displays appointments excluding appointment status deleted, rescheduled, cancel, noshow.<br>
						Based on appointment date and as per selected criteria of Physician, Facility and Referring Physician.<br>
						Start date and end date is same as date range in searched criteria.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Charges:</div>Are based on DOS and as per selected criteria of Groups, Physician, Reffering Physicians, Facility and Procedures.<br>
						Start date and end date is same as date range in searched criteria. Deleted charges are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Summary Charges:</div>Are based on DOS and as per selected criteria of Groups, Physician, Reffering Physicians and we do not consider selected fields of Facility and Procedures.
						Start date and end date is same as date range in searched criteria. Deleted charges are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Receipts:</div>Are based on DOT/DOP and as per selected criteria of Groups, Physician, Reffering Physicians and we do not consider selected fields of Facility and Procedures.
						Start date and end date is same as date range in searched criteria. Deleted payments are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Summary Receipts:</div>Are based on DOT/DOP and as per selected criteria of Reffering Physicians and we do not consider selected fields of Groups, Physician, Facility and Procedures.
						Start date and end date is same as date range in searched criteria. Deleted payments are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">A/R Aging:</div>Displays insurance and patient balance as per selected criteria of Referring Physician and we do not consider Group, Physician, Facility and Procedures.
					</div>
				</div>';
				
			if($typ=='tpl')$str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			else $str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			
			$str.='<!-- END -->';
			break;
		case'facility_revenue'://Revenue -> facility monthly
			$str='<!-- INFO DIV -->				
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						Allows user to compare your facilities revenue side-by-side, month-by-month. User must select at least one facility to run this report. 
						Can be run based on Date of Remittance (DOR) or Date of Transaction (DOT).<br><br>
						
						Selecting Originating Facility, or the home facility set-up in Accounting > Service Charges, enables user to view where revenue is coming from, and determine which facility is referring more patients.						
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Appointments:</div>Displays appointments excluding appointment status deleted, rescheduled, cancel, noshow.<br>
						Based on appointment date and patients fetched by charges query and as per selected criteria of Facility.<br>
						Start date and end date is same as date range in searched criteria.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Charges:</div>Are based on DOS and as per selected criteria of Groups, Physician, Facility and Procedures.<br>
						Start date and end date is same as date range in searched criteria. Deleted charges are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Summary Charges:</div>Are based on DOS and as per selected criteria of Groups, Physician, Facility and report does not consider selected fields of Procedures.
						Start date and end date is same as date range in searched criteria. Deleted charges are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Receipts:</div>Are based on DOT/DOP and encounters of charges and as per selected criteria of Facility and report does not consider selected fields of Groups, Physician, and Procedures.
						Start date and end date is same as date range in searched criteria. Deleted payments are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Summary Receipts:</div>Are based on DOT/DOP and as per selected criteria of Facility and report does not consider selected fields of Groups, Physician, and Procedures.
						Start date and end date is same as date range in searched criteria. Deleted payments are not considered.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">A/R Aging:</div>Displays insurance and patient balance as per selected criteria of Facility and report does not consider Group, Physician and Procedures.
					</div>
				</div>';
				
			if($typ=='tpl')$str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			else $str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			
			$str.='<!-- END -->';
			break;	
		case'referring_physician'://Ref. Physician
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						Report displays for referring physicians, particular ref. physicians can be filtered in report to get their accounting details about 
						charges and revenues. Search can be done for based on accounting or on demographics ref. physicians.<br><br>
						Grouping wise result for cpt codes and dx codes can be generated and user will directly get CSV file for these results. 
						Report executes results based on date of service.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Physician Types:</div>Drop down has below options.
						<ul>
							<li>Referring Physician</li>
							<li>Primary Care Physician</li>
							<li>Co-managed Physician</li>
							<li>Not associated PCP</li>
						</ul>	
						If any of above is selected then search works is based on demographics rather than based on accounting.
						If non option is selected then report executes based on referring physicians saved for encounters in accounting screen. 
						<div class="infoDataLine"></div>					
						
						<div class="infoSubTitle">All/Initial Encounters:</div>If "All" is selected then displays all encounters of patient as per selected criteria.<br>
						If "Initial" is selected then displays first encounter of patient if searched criteria is matching with it.
						<div class="infoDataLine"></div>					
						<div class="infoSubTitle">Group By Physician/Facility:</div>In case of group by physician the referring physician records are not displaying if its name is same as its grouped physician name. 
						Facility grouping has no such type of conditional check.
						<div class="infoDataLine"></div>					
						<div class="infoSubTitle">PT/Units:</div>PT displaying unique count of patients. Units displaying total nummber of units for procedures.
						<div class="infoDataLine"></div>					
						<div class="infoSubTitle">Billed Amount:</div>DOS based charges of procedures of selected encounters as per selected criteria.
						<div class="infoDataLine"></div>					
						<div class="infoSubTitle">Allowed Amount:</div>DOS based allowed amount of procedures of selected encounters as per selected criteria.
						<div class="infoDataLine"></div>					
						<div class="infoSubTitle">Ins./Pt. Paid:</div>DOS based insurance and patient paid amount of procedures of encounters as per selected criteria.
						<div class="infoDataLine"></div>					
						<div class="infoSubTitle">Deductible Amount:</div>DOS based deductible amount of procedures of encounters as per selected criteria. Deductible is applied by selecting "Deductible" from accounting payment window.
						<div class="infoDataLine"></div>					
						<div class="infoSubTitle">Write Off:</div>DOS based write-off amount of procedures of encounters as per selected criteria.
						<div class="infoDataLine"></div>					
						<div class="infoSubTitle">Comments:</div>Displayed from ref. physician block saved for per ref. physician.				
					</div>
				</div>';
				
			if($typ=='tpl')$str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			else $str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			
			$str.='<!-- END -->';
			break;	
		case'payroll_report'://Provider analytic
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<div class="infoSubTitle">RVU:</div>Displays data under "Non Facility Pricing Amt." if searched by DOS and based on below formula. Values of all RVU elements stored under admin tab.<br>
						Non Facility Pricing Amt. = ((((Work RVU * BNA) * Work GPCI) + (PE RVU * PE GPCI) + (MP RVU * MP GPCI))  * Conv. Factor)  * CPT Units
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">DOS Search:</div>Charges, payments, write-off, adjustment and refund are based on DOS and as per searched criteria.<br>
						Deleted amounts are not considered.
						<div class="infoDataLine"></div>			
						<div class="infoSubTitle">Charges Date Search:</div>Charges, payments, write-off, adjustment and refund are based on posted date and as per searched criteria. 
						Deleted amounts are not considered.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Payment/Trasaction Date Search:</div>Charges are based on first posted date and as per selected search criteria.<br>
						Payments, write-off, adjustment and refund based on DOP/DOT and as per searched criteria.<br>
						Amounts deleted after searched end date are considered.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Count Column:</div>Displays number of units of procedures.
					</div>
				</div>';
				
			if($typ=='tpl')$str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			else $str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			
			$str.='<!-- END -->';
			break;
		case'insurance_analytics'://Ins. Analytics
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						Report displaying results only for the encounters which have insurance exist.
						This report answers two basic questions: how much has insurance paid our practice, and what have we sent them?<br><br> 

						The Insurance Analytics report breaks down payments by insurance, tells you what CPT codes are used, and how much your practice 
						has paid out for a certain time period. It displays how many patients have insurance, and from which companies in a given time frame. 
						Specifically, it shows encounters, CPT Codes, and how much was billed and paid by each insurance company. This report is used for 
						finding discrepancies in reimbursements.<br><br> 

						The insurance analytics report can be run to analyse if the CPT was billed as either Primary or Secondary. 
						The report can be displayed in Summary or Detail format, with Detail showing patient names.<br><br>

						Running this report via DOT displays the allowed amount, how much the Primary and Secondary paid, as well as the total 
						institutional balance. It can also be run based on DOS (date of service).
												
						<div class="infoDataLine"></div>						
						<div class="infoSubTitle">Ins. Billed (DOS Based):</div>DOS based total charges of encounters as per selected search criteria.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Ins. Paid (DOS Based):</div>Payments done by insurance of encounters that are DOS based and as per selected search criteria.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Ins. Billed (DOT Based):</div>Display charges of encounters that have DOT based payments done by insurance and as per selected search criteria.<br>
						Pri. Billed displays charges only if primary insurance balance is exist. Same is applying for secondary insurance.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Ins. Paid (DOT Based):</div>DOT based payments done by insurance and as per selected search criteria.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Ins. Balance:</div>Balance of all fetched encounter that are fetched either by DOS or DOT based search and as per selected criteria.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Total Patient Count:</div>Total displaying unique patients. For example there a patient displaying in both primary and secondary insurance in two different rows then report will count that patient as single count.						
						<div class="infoDataLine"></div>		
						*Total payments of this report can be calculated with below logic to match other report\'s insurance payments.<br>
						- First report should be executed based on "Primary" selected from filter "Ins. Types" and note down the total of "Pri. Pad" column.<br>
						- Now execute report by selecting "Secondary" and total of "Sec. Paid" column should added to note down payment fetched from previous execution.<br>
						Now this total can be match with insurance payments of other reports.
					</div>
				</div>';
			break;
		case'allowVerify'://Allowable Verify
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<div class="infoSubTitle">Charges Column:</div>Displays total charges of procedure related to encounter.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Fee Table Column:</div>Displays fee of cpt code from master table and fee column amount is 
						based on the fee column value that exists against primary insurance company of encounter.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">ERA Allowable Column:</div>Displays allowable amount of encounters.
						<div class="infoDataLine"></div>								
						<div class="infoSubTitle">Allowable Amt. Drop Down:</div>In case of "All", report displays all encounters if allowable amount matching with "Fee Table" amount or not.<br>
						In case of "Un-Matched", report displays only those encounter\'s allowable amounts that are not matching with "Fee Table" amount.
					</div>
				</div>';
			break;
		case'testnew'://surgical payment report
			$str='<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">testing data here</div>';
			break;
		case'payments'://Payment Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						In this high-level report, it will say posted payments-breakdown cash check, cc, etc. It can then be broken down through payments by patient or insurance.
						<div class="infoDataLine"></div>		
						The Payment report displays all posted payments, unapplied CI/CO payments, and unapplied pre-payments. This report helps billers track 
						how many payments were collected and posted in each time frame. It also helps locate payments that haven’t yet been applied to the 
						patient’s charges.
						<div class="infoDataLine"></div>		
						User can run this report by DOR or DOT. Most will run by DOT, because your practice will want to see exactly what happens in a certain 
						timeframe. This includes: what funds have been taken in, applied, or posted in a time period.						
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">DOT Search:</div>Displays all transaction of searched date including non-deleted and deleted without any deleted date check.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">DOR Search:</div>Fetches transactions which are non-deleted for the selected date range. Transactions which are done and then deleted 
						between date range are not considered, transaction should be non-deleted or either deleted date should be out of selected date range.<br><br>
						The applied CI/CO and pre-payments are checked based on their applied date. If applied date of transaction is out of date range then report display transaction as unapplied payment.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Pay Location:</div>If option selected then report fetch all transactions based on pay location and result display transactions pay location wise.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Without Consolidation Selection:</div>
						Without consolidation checked, deleted payments run for DOT for certain date range will display. 
						However, if something was deleted outside of this selected range, it will still show up on the report.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">With Consolidation Selection:</div>
						Essentially, having Consolidation checked in the Payment report means that if a payment was deleted within a timeslot,
						the payment will not show up in the report. Consolidation will let you truly see what was in the system during a certain time.
						<div class="infoDataLine"></div>		
						Refund amounts are based on DOT of refunds.<br>						
					</div>
				</div>';
				
			if($typ=='tpl')$str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			else $str.='<script type="text/javascript">$(\'#div_rpt_info\').draggable();</script>';
			
			$str.='<!-- END -->';
			break;		
		case'net_gross'://Net/Gross
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<strong>*Deleted amounts are not considered based on DOS search and DOP search.</strong>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">DOS Based Search:</div>Charges, payments, adjustments and balance are based on DOS and as per selected search creteria.<br>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Charges & Balance (DOT Based Search):</div>Charges and balance are DOS based and as per selected search criteria.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Payments & Adjustments (DOT Based Search):</div>Payments and adjustments are DOT based and as per selected search criteria.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Gross Coll. Ratio:</div>Based on below formula-<br>
						Gross Coll. Ratio = (Gross Payments * 100) / Charges
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Net Coll. Ratio:</div>Based on below formula-<br>
						Net Coll. Ratio = (Gross Payments * 100) / Physician\'s Total Gross Payment
					</div>
				</div>';
			break;			
		case'provider_ar'://Proivder A/R
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>					
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						This useful report provides the practice with all A/R information. 
						Pending/expected payments are displayed in a general summary of A/R data,
						organized by physician or facility. This report	displays beginning and ending A/R, how many charges have been posted, payments, 
						and pending payments.						
						<div class="infoDataLine"></div>
						<strong>*Deleted amounts are not considered based on DOP search but are considered based on DOT search. These are those amouunts
						which are deleted after selected date range.<br>
						All transactions which are done and deleted between selected date range does not considered in report.
						<div class="infoDataLine"></div>
						*Charges are based on first posted date for both DOT and DOP searches and deleted charges are considered in both type of searches.</strong>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Beginning & Ending A/R:</div>Beginning A/R displays balance of all encounters having first posted date/DOT/DOP is before searched start date.<br>
						<strong>Ending A/R</strong> displays balance of all encounters having first posted date/DOT/DOP is till searched end date.<Br>
						All deleted transactions which are deleted after searched start date are considered as non-deleted in beginning A/R, same for ending A/R which checks
						deleted after searched end date.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Deleted Amounts:</div>Amounts that are deleted between searched date range but their first posted date/DOT/DOP is before searched start date.
						These amounts are considered in beginning a/r as non-deleted transactions.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Prev. Write-off:</div>Default write-off which is calculated after applying allowed amount at procedure of encounter.
						If any default write-off which is first fetched in beginning a/r and its modified later which is further fetched in adjustment of 
						which is fetched for date range between selection. Then it needs the previous fetched default write-off should be deuducted.
						So "Prev. Write-off" row deducts such write-off amount from collected adjustments.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Not Posted:</div>Fetches only in case of DOT search. 
						It display charges of encounters which have DOS between selected date range but yet not posted and no 
						any adjustment and payment done for these encounters.						
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Unapplied CI/CO Amounts:</div>These are always DOT based becuase no DOP exist for CI/CO payments.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Unapplied Pre-Payments:</div>Based on DOT/DOP depending on searched criteria.
						<div class="infoDataLine"></div>		
						<div class="infoSubTitle">Refunds of CI/CO and Pre-Payments:</div>Displays all refunds of CI/CO and pre-payments done between selected
						date range.
						<div class="infoDataLine"></div>								
						Refund amounts are based on DOT of refunds.
					</div>
				</div>';
			break;	
		case'days_in_ar'://Days In A/R
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<div class="infoSubTitle">Charges:</div>Charges for DOS of last six months.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Total A/R:</div>Balance of selected encounters of last six months.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Avg. Daily Charges:</div>Based on below formula.<br>
						Avg. Daily Charges = Charges / Total days of last six months
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Days in A/R:</div>Based on below formula.<br>
						Days in A/R = Total A/R / Avg. Daily Charges
					</div>
				</div>';
			break;
		case'patient_ar_aging'://Days In A/R (Patient)
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>	
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						Report displays aging wise balance outstanding for patient. A/R aging patient report displays pre-payments if option “Unapplied Pre-payments” selected.
						By selecing option "Pt. Demographics" adddress of patient display under patient\'s name.
						<br><br>
						By clicking on patient name accounting details of patient opened in parent window.
						<br><br>
						Report always executes result based on DOS. If DOT is selected then aging calculation is done based on 
						last paid date but date range criteria is calculated based on DOS.	
					</div>
				</div>';
			break;									
		case'insurance_ar_aging'://Days In A/R (Patient)
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>	
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						Report displays aging wise balance. If detail view option is selected then some fields task status display where user can select assigned operator, status of task and due dates of task.
						<br><br>
						If filter “Acc. Details” is selected then result displays policy number, encounter related procedures, account notes and next follow up date which can be display by clicking on arrow button on right hand side of result. 
						<br><br>
						By clicking on patient name accounting details of patient opened in parent window.
						<br><br>
						Report always executes result based on DOS. If DOT is selected then aging calculation is done based on 
						last paid date but date range criteria is calculated based on DOS.						
					</div>
				</div>';
			break;	
		case'ar_reports':// A/R Reports
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<strong>*Doe patients are not considered in report.</strong>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Total Charges:</div>Based on first posted date of encounter. Also considered charges having first posted date between selected date range but deleted after selected date range.<br>
						Deleted charges between selected date range which have first posted date before selected date range are also cosidered and deducted from charges.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Total Write-off & Adj.:</div>Based on DOT of write-off and adjustments. Also considered write-off & adj. having DOT between selected date range but deleted after selected date range.<br>
						Deleted write-off and adj. between selected date range which have DOT before selected date range are also cosidered and deducted from adjustments.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Total Payments:</div>Based on DOT of payments. Also considered payments having DOT between selected date range but deleted after selected date range.<br>
						Deleted payments between selected date range which have DOT before selected date range are also cosidered and deducted from payments.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Not Billed (Pri):</div>Displaying charges for encounters that are posted and first posted date is between selected date range and they have insurance exists but not submitted for claim.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Bad Debt:</div>Displaying patient due for patients having account status under collections and account status should not active or collection history 
						and encounter for that patient having first posted date between selected date range.
					</div>
				</div>';
			break;	
		case'receivables':// Receivables Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<strong>*Doe patients are not considered in report.</strong>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Receivable Type (Search):</div>Display only patient due if receivable type(patient) is slected.<br>
						Display primary+secondary+tertiary due if receivable type(insurance) is selected.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Amount (Search):</div>Patient/insurnace due should be greater than filled amount and based on receivable type selected.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Aging:</div>Display in detail view of report.<br>
						Receivable Type(Patient) - Aging is calculated based on since the patient due is pending.<br>
						Receivable Type(Insurance) - Aging is calculated based on since primary/secondary/tertiary due is pending. Latest date is used for aging based on type of due.<br>
						If no payment date exists then DOS is used for aging. 
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">#Statements:</div>Display in detail view of patient receivable.<br>
						No. of statements generated after last payment date of patient\'s encounter.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Total Payment:</div>Display in summary view of insurance receivable.<br>
						Payments of procedures for which insurance balance is displaying.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Total Charges:</div>Display in detail view of insurance receivable.<br>
						Charges of procedures for which insurance balance is displaying.
					</div>
				</div>';
			break;	
		case'unworked_ar':// Unworked A/R Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<strong>*Doe patients are not considered in report.</strong>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Results Display:</div>Claims that have been submitted but not paid by payor 
						and aging from DOS/last paid date/submitted date is exceeding more than 30 days.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Aging Column:</div>Displays aging based on smallest number from DOS/last paid date/submitted date.
					</div>
				</div>';
			break;	
		case'unbilled_claims':// Unworked A/R Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<strong>*Doe patients are not considered in report.</strong>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Results Display:</div>Records that have been posted but not submitted and they have insurance exists and outstanding balance.
					</div>
				</div>';
			break;																							
		case'top_rej_reasons':// Top Rej. Reasons Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<strong>*Doe patients are considered in report.</strong><br>
						Rejections may done by ERA or by manually. Amount column displays the denied amounts.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Date Range Search:</div>If search is DOT based then encounters fetched based on date of denied <br>
						and if search is DOS based then results displayed based on DOS of encounters.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Operator Selection:</div>If operators selected then fetch records for operators who denied the payments.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Insurance Companies Selction:</div>In case insurance companies selected then records displays only for insurance companies who denied the payments.
					</div>
				</div>';
			break;	
		case'unapplied_superbills':// Unapplied Superbills Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						The Unapplied Superbills report displays how many superbills have unposted charges. 
						Applied super bills are checked based on either posted date or entered date or re-submitted date or date of service. 
						User can run this report as a Summary or by Detail (broken down by individual patients). This report can only be run by DOS. 
						<div class="infoDataLine"></div>
						Applied super bills are checked based on either posted date or entered date or re-submitted date or date of service.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Created column (Summary):</div>Displays no. of appointments for those superbills are made.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Not Created column (Summary):</div>Displays no. of appointments for those superbills are not made.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Not Applied column (Summary):</div>Displays no. of superbill that are not processed.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">If no encounter status selected:</div>Then fetches all patients of having appointments between selected date range.<br>
						Their superbills are checked based on same date range criteria.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">No SuperBill option (if selected):</div>Fetches all encounters that are entered but they have no superbill exists.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Charges Not Posted option (if selected):</div>Fetches all encounters that are entered but they are not posted.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Charges Not Submitted option (if selected):</div>Fetches all encounters that are entered but not submitted.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Charges Not Entered option (if selected):</div>Fetches all appointments whose charges are not entered and superbill are not processed.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">No Subperbill + Charges not entered (if selected):</div>Fetches all appointments whose charges are not entered and also those superbills that are not processed.						
					</div>
				</div>';
			break;
		case 'adjustment':// Adjustment Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						The Adjustment report displays charges, payments, write-offs, discounts and refunds. 
						This report can be run by DOT (Date of Transaction) or DOS (Date of Service). 
						You can also run reports on specific batches of charges by entering the tracking number in Batch Track #. 
						Date range search is not allowed with batch number search.<br><br>
						Search can be done based on adjustment codes, write-off codes, discount codes and reason codes.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">DOS Based Search:</div>Charges, payments, write-off and balance displays for the encounter that have DOS between searched date range and write-off is done for them.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Transaction Based Search:</div>Write-off displays for encounters which have DOT between searched date range.
						Charges, payments and balance fetched for based on that procedures and displayed.
					</div>
				</div>';
			break;
		case'fd_collection':// FD Collection Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<strong>*Doe patients are considered in report.<br>
						*Deleted amounts are not considered in report.</strong>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Two Views of Report:</div>Report has two views based on admin settings.<br>
						Settings of Visit Payment block from Admin -> Billing -> Policies effects on flow of report.<br>
						If Encounter option is checked then payments of encounters and pre-payments displayed.<br>
						If Check In and Check Out options are checked then payments of check in/out and pre-payments displayed.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Encounter Based:</div>Payments displayed having DOT or DOP falling between selected date range.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Check In/Out Based:</div>Payments displayed having DOT falling between selected date range.
						<div class="infoDataLine"></div>
						Refund amounts are based on DOT of refunds.
					</div>
				</div>';
			break;	
		case'provider_rvu':// Provider RVU Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						<strong>*Doe patients are considered in report.<br>
						*Deleted amounts are not considered in report.</strong>
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">CPT Count:</div>Displays no. of records exists for corresponding CPT code.<br>
						In detail view this amount is displaying with \'/\' sign and the second part displaying total no. of encounters.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Tot. Work RVU:</div>Based on below formual -<br> 
						Tot. Work RVU = Work RVU * No. of records of CPT code.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Adj. RVU:</div>Displays only in detail view and based on below formula -<br>
						Adj. RVU = (((Work RVU * BNA) * Work GPCI) + (PE RVU * PE GPCI) + (MP RVU * MP GPCI))						
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">RVU/CPT:</div>Displays only in detail view and based on below formula -<br>
						RVU/CPT = Adj. RVU * Conv. Factor Amt.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Provider RVU:</div>Based on below formula-<br>
						Provider RVU = RVU/CPT * No. of records of CPT code.
					</div>
				</div>';
			break;
		case'tfl_proof':// TFL Proof Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						Doe patients are considered in report.<br>
						<div class="infoDataLine"></div>
						Report is displaying encounters which are submitted and difference between submitted date and DOS is less than or equal to 60 days.
					</div>
				</div>';
			break;			
		case'pt_status':// PT Status Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">Below four status type of patients displaying with their patient balance.<br>
						 - All patients having accoung status rather than active.<br>
						 - All deferred patients based on accounting.<br>
						 - All VIP patients based on accounting.<br>
						 - All patients which have hold status exist. 
					</div>
				</div>';
			break;	
		case'billing_verification':// Billing Veri. Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">Report is based on appointments of selected date range.
					<div class="infoDataLine"></div>
					Chart note details for patients displayed which have appointments and date of service between selected date range.<br>
					<div class="infoDataLine"></div>
					Superbill and encounter details displayed based on date of service or patients have appointments between selected date range.
					</div>
				</div>';
			break;						
		case'front_desk':// Front Desk Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:15px">
						<div id="infoTitleText" class="fl"></div>
						<div style="float:right"><img src="../icons/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv"><strong>*Doe patients are considered in report.<br>
					*Records deleted after searched date also considered in report.</strong>
					<div class="infoDataLine"></div>
					Posted payments, Check In/Out payments and Pre-payments displaying in this report.
					<div class="infoDataLine"></div>
					Refund amounts are based on DOT of refunds.
					</div>
				</div>';
			break;						
		case'refund_report':// Refund Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						The Refund report displays any refunds that the practice has given to the patient, it includes refunds of posted payments,
						CI/CO refunds and pre-payments refunds. This report can only be run by Date of Transaction (DOT).
						<div class="infoDataLine"></div>
						To search for groups of patients by last name, use the Last Name, From, and To fields. 
						Or, you may search for a patient by Last Name or by ID#.
					</div>
				</div>';
			break;									
		case'unapplied_payments':// Unapplied Payments
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						The Unapplied Payments report displays all payments that were collected from the patient,
						but have not yet been applied to their charges. It also displays payments that have been deleted.
						This report can only be run by DOR (Date of Remittance).
						<div class="infoDataLine"></div>
						Basically, this report shows what payments haven’t been posted. It can be run in summary or detail.
						<div class="infoDataLine"></div>
						This report is useful to know when charges have been posted, because if a charge is not posted to a
						patient’s account, your practice’s A/R will be negatively affected.
					</div>
				</div>';
			break;	
		case'daily_balance':// Daily Balance Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						This report will display how much the system thinks has been collected versus the actual receipts collected. 
						The Daily Balance report displays CI/CO payments, pre-payments, posted payments, and grand totals. This is the recommended report for your front desk staff to balance daily.
						This report can be run by Date of Transaction (DOT) or DOR (Date of remittance/payment). 
						Reports run in detail show breakdowns by individual patients. Depending on workflow, 
						If you’re good about closing out before you leave, run by DOT—when the user has physically inputted something into the system.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">CI/CO and Pre-payments:</div>Fetched based on selected dor/dot, then report checks all applied amounts from 
						these payments without any check of applied date which displays under "Applied" column, and all unapplied balance amount displayed 
						under “Unapplied” column.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Posted payments:</div>Displayed based on all direct posted to encounters, all payments which are applied/posted
						from CI/CO or from pre-payments does not consider here.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Applied for date range:</div>Display amounts which are fetched based on dor/dot of selected date range. 
						It considered all direct payments done for encounters and all CI/CO or Pre-payment which have applied/posted date between 
						selected date range.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Applied from collected:</div>Display total of direct payments of encounters and all applied/posted payments
						from CI/CO or pre-payments which are collected between selected date range where date of applied/posted for these amounts does not checked.
						<div class="infoDataLine"></div>
						<div class="infoSubTitle">Unapplied from collected:</div>Display all not applied amounts from CI/CO or pre-payments which are collected
						between selected date range.
						<div class="infoDataLine"></div>
						Red colored amounts display where some refund has done. Refund is already deducted from displayed amounts.
					</div>
				</div>';
			break;
		case'day_sheet':// Day Sheet Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						The day sheet details all transactions from a given day. This report helps billers figuratively “balance their drawers” and identify where payments are missing. 
						<br>This report displays:<br>
						<ul>
							<li>Check-in and Check-out time</li>
							<li>Patient Name and ID#</li>
							<li>Appointment type</li>
							<li>Total Superbill charge </li>
							<li>If the charges were posted or not</li>
							<li>What types of payments were applied to this charge</li>
							<li>CI/CO and pre-payments.</li>
						</ul>
						<div class="infoDataLine"></div>
						This is the report which is usually run daily, so billers can easily keep track of payments. Day sheet is only for scheduled appointments.
						It displays appointment time, payments, and charges specifically for a particular day. Do not confuse this with payment & daily 
						balance reports.
						<div class="infoDataLine"></div>
						<b>Cancelled appointments</b> are fetched based on date of cancelled.<br>
						<b>Superbills</b> are fetched based on selected date of service.<br>
						<b>Charges</b> are fetched based on posted date or entered date or re-submitted date or based on date of service. <br><br>
						<b>Payments</b> display for all fetched charges which have selected payment date.<br>
						Payments also display for CI/CO and pre-payments which have same payment date and having same date of appointment.
						<div class="infoDataLine"></div>
						Summary near bottom of report display <b>total scheduled, re-scheduled, no-show, cancelled and total checked</b> patients.
						<br><br>
						Red colored CI/CO and Pre-Payments represents a refund amount deducted from these payments. Refund amount can be viewed by mousing over the red colored amount.
					</div>
				</div>';
			break;	
		case'unfinalized_encounters':// Unfinalized Encounters Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						The Unfinalized Encounters report displays all unfinalized patient charts and tests. Charts will automatically 
						finalize after 3 days, but tests can only be finalized when the physician clicks done at the bottom of the test screen.<br><br>

						This report is useful for when doctors are forgetting to finalize encounters (charts). Office managers can use this report at 
						the end of the week, and email it to the doctors who are most often forgetting to finalize encounters.<br><br>
						<div class="infoDataLine"></div>
						Filter “Charts only” if selected then tests are not display in result.<br><br>

						Report fetches chart notes results only for physician, attending physician and for physician assistant.
						But if filter “Exclude User Types” is selected then chart note fetched for all users.
					</div>
				</div>';
			break;					
		case'copay_reconciliation':// Copay Reconciliation Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						The Copay Reconciliation report helps determine if patients are paying their complete copays, and to help balance collected copays. 
						This report is a front desk tool. It shows a patient’s primary insurance for that day, what the patient’s copay was in the system,
						patient info insurance, and how much money was collected.
						<div class="infoDataLine"></div>

						Report is based on appointment date and copay is checked from CI/CO screen and associated insurance case copay is fetched.<br>
						Report has below three blocks:<br>
						<ul>
							<li>Copay collected records block</li>
							<li>Copay not collected records block</li>
							<li>Other status records (display records for which no check-in or check-out done.)</li>
						</ul>	
						<div class="infoDataLine"></div>
						
						At the end of the day, the front desk manager can run this report, and pri ins/copay value should match copay collected.
						If there is a discrepancy, the front desk can see who forgot to collect the copay.						
					<div class="infoInnerDiv">				
					</div>
				</div>';
			break;		
		case'provider_analytics':// Copay Reconciliation Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						Report is similar to the practice analytics report. 
						The difference is, for a physician, provider analytics will give the count of encounters.
						Report can be executed based on DOS, DOR and DOT.
						<div class="infoDataLine"></div>
						
						It displays financial data per physician: how many of each procedure they conducted, as well as the charges,
						payments, write-offs, and refunds. It also shows details like the number of payments from medicare and medicaid, 
						refraction, and copay payments.						
					<div class="infoInnerDiv">				
					</div>
				</div>';
			break;	
		case'denial_report':// Denial Report
			$str='<!-- INFO DIV -->
				<div id="div_rpt_info" class="infoBox" style="width:'.$width.'px; height:auto; z-index:9999; position:absolute; display:none">
					<div class="infoTitle" style="height:22px">
						<div id="infoTitleText" style="float:left" ></div>
						<div style="float:right" id="closeRptInfo"><img src="../../library/images/delete_icon.png" border="0" onClick="javascript:$(\'#div_rpt_info\').toggle(\'slow\');"></div>
					</div>
					<div class="infoTitleLine"></div>
					<div class="infoInnerDiv">
						A report of claim denials made by practice. Let’s practice know what claims are being denied, and why. 
						Report is based on denial date which can be DOR or DOT. Report display charge amount and balance amount for related encounters.
					<div class="infoInnerDiv">				
					</div>
				</div>';
			break;								
		default:
			$str='';
			break;	
	}
	return $str;
}

function reportLogicInfoHeader($typ)
{

}

?>