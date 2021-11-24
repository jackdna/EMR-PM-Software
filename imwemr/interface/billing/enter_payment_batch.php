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
$without_pat="yes"; 
$title = "Encounter Payment";
require_once("../accounting/acc_header.php");
?>
<table class="table table-bordered table-striped enter_pay_wd">
    <tr class="purple_bar border_none">
        <td colspan="2">
            <strong>DOS:&nbsp;</strong><?php echo $date_of_service;?><br />
            <strong>E.Id:&nbsp;&nbsp;</strong><?php echo $encounter_id;?>
        </td>
        <td colspan="3"><strong>Group Name: </strong><?php echo $group_name_final;?></td>
        <td><strong>Ins. Case</strong> <br /><?php echo $case_type_nam;?></td>
        <?php if($primaryInsProviderId>0){?>
            <td <?php echo $tooltip1; ?>><strong>Primary</strong> <br /><?php echo substr($insCo1NameCode1,0,4); ?>/<?php echo numberFormat($pri_copay,2,'yes'); ?></td>
        <?php } ?>
        <?php if($secondaryInsProviderId>0){?>
            <td <?php echo $tooltip2; ?>><strong>Secondary</strong> <br /><?php echo substr($insCo2NameCode1,0,4); ?>/<?php echo numberFormat($sec_copay,2,'yes'); ?></td>
        <?php } ?>
        <?php if($tertiaryInsProviderId>0){?>
            <td <?php echo $tooltip3; ?>><strong>Tertiary</strong> <br /><?php echo substr($insCo3NameCode1,0,4); ?>/<?php echo numberFormat(0,2,'yes'); ?></td>
        <?php } ?>
        <td></td>
        <td colspan="2"><strong>Auth#: </strong><?php if($auth_no) echo $auth_no; else echo '&nbsp;'; ?></td>
        <td colspan="4"><strong>Auth Amount: </strong><?php echo numberFormat($auth_amount,2,'yes'); ?></td>
        <td colspan="5"><div class="btn btn-info pull-right" style="width:80px;" onclick="show_tran_rec(this);">Active</div></td>
    </tr>
    <tr class="grythead">
        <th>Apply</th>
        <th>CPT</th>
        <th class="text-nowrap">Dx Code</th>
        <th class="text-nowrap" style="width:63px;">T. Charges</th>	
        <th style="width:63px;">Allowed</th>
        <th style="width:63px;">Deductible</th>
        <?php 
        $due_col=1;
        if($primaryInsProviderId>0){ $due_col=$due_col+1;?>
            <th style="width:63px;">Pri Amt</th>
        <?php }?>
        <?php if($secondaryInsProviderId>0){ $due_col=$due_col+1;?>
             <th style="width:63px;">Sec Amt</th>
        <?php }?>
        <?php if($tertiaryInsProviderId>0){ $due_col=$due_col+1;?>
             <th style="width:63px;">Ter Amt</th>
        <?php }?>
        <th style="width:63px;">Patient Amt</th>
        <th>Method</th>
        <th style="width:75px;">CC / Ch.# </th>	
        <th>Paid</th>
        <th class="text-nowrap">Balance</th>
        <th style="width:83px;">DOR</th>
        <th>DOT</th>
        <th>Adj</th>
        <th>Credit</th>	
        <th style="width:80px;">Code</th>
    </tr>
</table>        