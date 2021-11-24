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

?><?php
/*
File: cal_month_view.php
Purpose: Show calendar month wise
Access Type: Include
*/
list($thisc, $nextc, $next2c) = $obj_scheduler->load_calendar($y."-".$this_m."-".intval($dt));
?>
<div class="fl cl_otln" id="cal_month_vp_1">
	<?php echo $thisc;?>
</div>
<div class="fl cl_otln" id="cal_month_vp_2">
	<?php echo $nextc; ?>
</div>
<div class="fl cl_otln" id="cal_month_vp_3">
	<?php echo $next2c; ?>
</div>