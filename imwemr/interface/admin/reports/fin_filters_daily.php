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
  <div class="col-lg-3 col-md-3 col-sm-6" id="analytic_filter">
    <div class="grpbox">
      <div class="head"><span>Analytic Filter</span></div>
      <div class="clearfix"></div>
      <div class="tblBg">
        <div class="row">
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="ins_group" id="ins_group" value="1" <?php if ($filter_arr['ins_group'] == '1') echo 'CHECKED'; ?>/>
              <label for="ins_group">Ins. Group</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="ins_carriers" id="ins_carriers" value="1" <?php if ($filter_arr['ins_carriers'] == '1') echo 'CHECKED'; ?>/>
              <label for="ins_carriers">Ins. Carriers</label>
            </div>
          </div>
         	<div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="payment_method" id="payment_method" value="1" <?php if ($filter_arr['payment_method'] == '1') echo 'CHECKED'; ?>/>
              <label for="payment_method">Payment Method</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="un_processed" id="un_processed" value="1" <?php if ($filter_arr['un_processed'] == '1') echo 'CHECKED'; ?>/>
              <label for="un_processed">Un-processed</label>
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
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="grpby_view_order" id="grpby_view_order" value="1" <?php if ($filter_arr['grpby_view_order'] == '1') echo 'CHECKED'; ?>/>
              <label for="grpby_view_order">View Order</label>
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
              <input type="checkbox" name="inc_appt" id="inc_appt" value="1" <?php if ($filter_arr['inc_appt'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_appt">Appointments</label>
            </div>
          </div>
          
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_trans" id="inc_trans" value="1" <?php if ($filter_arr['inc_trans'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_trans">Transactions</label>
            </div>
          </div>
          
          <div class="col-sm-12">
            <div class="checkbox checkbox-inline pointer">
              <input type="checkbox" name="inc_count_summary" id="inc_count_summary" value="1" <?php if ($filter_arr['inc_count_summary'] == '1') echo 'CHECKED'; ?>/>
              <label for="inc_count_summary">Count Summary </label>
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
              <input type="checkbox" name="output_view_only" id="output_view_only" value="1" <?php if ($filter_arr['output_view_only'] == '1') echo 'CHECKED'; ?>/>
              <label for="output_view_only">View Only</label>
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