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
$acc_view_only = 0;
if(core_check_privilege(array("priv_vo_acc")) == true){
	$acc_view_only = 1;
}
$acc_view_chr_only = 1;
if(core_check_privilege(array("priv_vo_charges")) == true){
	$acc_view_chr_only = 0;
}
$acc_view_pay_only = 1;
if(core_check_privilege(array("priv_vo_payment")) == true){
	$acc_view_pay_only = 0;
}
$acc_edit_financials = 0;
if(core_check_privilege(array("priv_edit_financials")) == true){
	$acc_edit_financials = 1;
}
$bi_edit_batch = 0;
if(core_check_privilege(array("priv_bi_edit_batch")) == true){
	$bi_edit_batch = 1;
}
?>