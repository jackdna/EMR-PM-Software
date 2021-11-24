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
 
 File: index.php
 Purpose: Filters HTML used for finalcial daily
 Access Type: Indirect Access.
*/
$filter_arr = @$filter_arr;
?>
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6" id="practice_filter">
    <div class="grpbox">
      <div class="head"><span>Practice Filter</span></div>
      <div class="clearfix"></div>
      <div class="tblBg">
        <div class="row">
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="groups" id="groups" value="1" <?php if ($filter_arr['groups'] == '1') echo 'CHECKED'; ?>/>
              <label for="groups">Groups</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="facility" id="facility" value="1" <?php if ($filter_arr['facility'] == '1') echo 'CHECKED'; ?>/>
              <label for="facility">Facility</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="physician" id="physician" value="1" <?php if ($filter_arr['physician'] == '1') echo 'CHECKED'; ?>/>
              <label for="physician">Physician</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="department" id="department" value="1" <?php if ($filter_arr['department'] == '1') echo 'CHECKED'; ?>/>
              <label for="department">Department</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="operators" id="operators" value="1" <?php if ($filter_arr['operators'] == '1') echo 'CHECKED'; ?>/>
              <label for="operators">Operators</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="date_range" id="date_range" value="1" <?php if ($filter_arr['date_range'] == '1') echo 'CHECKED'; ?>/>
              <label for="date_range">Date Range</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="time_range" id="time_range" value="1" <?php if ($filter_arr['time_range'] == '1') echo 'CHECKED'; ?>/>
              <label for="time_range">Time Range</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="summary_detail" id="summary_detail" value="1" <?php if ($filter_arr['summary_detail'] == '1') echo 'CHECKED'; ?>/>
              <label for="summary_detail">Summary/Detail</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="dos" id="dos" value="1" <?php if ($filter_arr['dos'] == '1') echo 'CHECKED'; ?>/>
              <label for="dos">DOS</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="doc" id="doc" value="1" <?php if ($filter_arr['doc'] == '1') echo 'CHECKED'; ?>/>
              <label for="doc">DOC</label>
            </div>
          </div>
          
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="dor" id="dor" value="1" <?php if ($filter_arr['dor'] == '1') echo 'CHECKED'; ?>/>
              <label for="dor">DOR</label>
            </div>
          </div>
          
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="dot" id="dot" value="1" <?php if ($filter_arr['dot'] == '1') echo 'CHECKED'; ?>/>
              <label for="dot">DOT</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-6" id="appointment_filter">
    <div class="grpbox">
      <div class="head"><span>Analytic Filter</span></div>
      <div class="clearfix"></div>
      <div class="tblBg">
        <div class="row">
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="ins_group" id="ins_group" value="1" <?php if ($filter_arr['ins_group'] == '1') echo 'CHECKED'; ?>/>
              <label for="ins_group">Ins. Group</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="ins_carriers" id="ins_carriers" value="1" <?php if ($filter_arr['ins_carriers'] == '1') echo 'CHECKED'; ?>/>
              <label for="ins_carriers">Ins. Carriers</label>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2 col-md-6 col-sm-6" id="group_by">
    <div class="grpbox">
      <div class="head"><span>Group By</span></div>
      <div class="clearfix"></div>
      <div class="tblBg">
        <div class="row">
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="grpby_groups" id="grpby_groups" value="1" <?php if ($filter_arr['grpby_groups'] == '1') echo 'CHECKED'; ?>/>
              <label for="grpby_groups">Groups</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="grpby_facility" id="grpby_facility" value="1" <?php if ($filter_arr['grpby_facility'] == '1') echo 'CHECKED'; ?>/>
              <label for="grpby_facility">Facility</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="grpby_physician" id="grpby_physician" value="1" <?php if ($filter_arr['grpby_physician'] == '1') echo 'CHECKED'; ?>/>
              <label for="grpby_physician">Physician</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="grpby_operators" id="grpby_operators" value="1" <?php if ($filter_arr['grpby_operators'] == '1') echo 'CHECKED'; ?>/>
              <label for="grpby_operators">Operators</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="grpby_department" id="grpby_department" value="1" <?php if ($filter_arr['grpby_department'] == '1') echo 'CHECKED'; ?>/>
              <label for="grpby_department">Department</label>
            </div>
          </div>
       	</div>
      </div>
    </div>
  </div>
  <div class="col-lg-2 col-md-6 col-sm-6" id="include">
    <div class="grpbox">
      <div class="head"><span>Include</span></div>
      <div class="clearfix"></div>
      <div class="tblBg">
        <div class="row">
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_appt_detail" id="inc_appt_detail" value="1" <?php if ($filter_arr['inc_appt_detail'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_appt_detail">Appt Detail</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_appt_summary" id="inc_appt_summary" value="1" <?php if ($filter_arr['inc_appt_summary'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_appt_summary">Appt Summary</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_ci_co_prepay" id="inc_ci_co_prepay" value="1" <?php if ($filter_arr['inc_ci_co_prepay'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_ci_co_prepay">CI/CO/Pre-Pay</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_summary_charges" id="inc_summary_charges" value="1" <?php if ($filter_arr['inc_summary_charges'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_summary_charges">Summary Charges</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_payments" id="inc_payments" value="1" <?php if ($filter_arr['inc_payments'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_payments">Payments</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_adjustments" id="inc_adjustments" value="1" <?php if ($filter_arr['inc_adjustments'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_adjustments">Adjustments</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_opening_ar" id="inc_opening_ar" value="1" <?php if ($filter_arr['inc_opening_ar'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_opening_ar">Opening AR</label>
            </div>
          </div>
          
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_ending_ar" id="inc_ending_ar" value="1" <?php if ($filter_arr['inc_ending_ar'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_ending_ar">Ending AR</label>
            </div>
          </div>
          
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_aging" id="inc_aging" value="1" <?php if ($filter_arr['inc_aging'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_aging">Aging</label>
            </div>
          </div>
       	</div>
      </div>
    </div>
  </div>
  <div class="col-lg-2 col-md-6 col-sm-6" id="format">
    <div class="grpbox">
      <div class="head"><span>Format</span></div>
      <div class="clearfix"></div>
      <div class="tblBg">
        <div class="row">
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="output_actvity_summary" id="output_actvity_summary" value="1" <?php if ($filter_arr['output_actvity_summary'] == '1') echo 'CHECKED'; ?>/>
              <label for="output_actvity_summary">View</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="output_pdf" id="output_pdf" value="1" <?php if ($filter_arr['output_pdf'] == '1') echo 'CHECKED'; ?>/>
              <label for="output_pdf">PDF</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="output_csv" id="output_csv" value="1" <?php if ($filter_arr['output_csv'] == '1') echo 'CHECKED'; ?>/>
              <label for="output_csv">CSV</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>