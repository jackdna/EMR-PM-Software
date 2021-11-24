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

include_once(__DIR__."/../../../config/globals.php");
include_once(__DIR__.'/qrda.php');

try
{
    $qrda = new qrda($performance_year);
    $measures_list = $qrda->list_measures();

    $patientId = $_REQUEST['patient_id'] ?? '';

    $ipop = $qrda->getipop($patientId);
}
catch( Throwable $e )
{
    echo '<div class="alert alert-warning" role="alert">Unable to list the measures for the performance year <b>'.$performance_year.'</b> or definition for the performance year does not exists</div>';
    exit;
}
?>
<table class="table table-bordered table-hover">
    <thead class="bg-primary">
        <tr>
            <td width="50" style="text-align: center;">
                <div class="checkbox">
                    <input type="checkbox" id="selectAllNQF" onClick="selectAllCQM()" />
                    <label for="selectAllNQF" style="padding:0;"></label>
                </div>
            </td>
            <td width="120" valign="top">&nbsp; CMS ID</td>
            <th width="auto"> &nbsp; Measure Name</th>
            <th width="200">Initial Population</th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <th class="bg-info" colspan="9">CLINICAL QUALITY MEASURE</th>
        </tr>
<?php
    /**
     * Iterate through measures list and make lsit structure
     */
    foreach( $measures_list as $measure ) :
        $measurePatientIds = implode(',', array_unique($ipop[$measure['cms']]) );
        $measureIpop = count($ipop[$measure['cms']]);
?>
        <tr>
            <td style="text-align: center;">
                <div class="checkbox">
                    <input type="checkbox" id="<?php echo $measure['cms']; ?>" class="nqfchkbx" value="<?php echo $measurePatientIds; ?>" <?php echo ($measureIpop<1)?'disabled':''; ?> />
                    <label for="<?php echo $measure['cms']; ?>" style="padding:0;"></label>
                </div>
            </td>
            <td valign="top"><b><?php echo $measure['cms']; ?></b></td>
            <td class="measure_name"><?php echo (($measure['parent'])?'&nbsp; &nbsp; &#10157 ' : '').$measure['measure']; ?></td>
            <td><span class="link_cursor" <?php echo ($measureIpop>0)? 'onDblClick = "showPTs(\''.$measurePatientIds.'\', this)"' : ''; ?>><?php echo $measureIpop; ?></span></td>
        </tr>
<?php
    endforeach;
?>
    </tbody>
</table>