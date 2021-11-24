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


include_once( $GLOBALS['srcdir'].'/classes/medical_hx/ocular.class.php' );

class as_medical_hx extends Ocular
{
    
    public $patient_id;

    public function __construct()
    {
        $this->patient_id = ( array_key_exists('idoc_pt_id', $_POST) && $_POST['idoc_pt_id'] != '' ) ? trim($_POST['idoc_pt_id']): false;
    }


    public function sync_medical_hx()
    {
        if( $this->patient_id === false )
        {
            return true;
        }

        if( array_key_exists('asMedicalHistorySync', $_SESSION) )
        {
            unset( $_SESSION['asMedicalHistorySync'] );
        }

        /**
         * Pull Mecical Hx data from Allscripts
         */
        $this->all_script_ocular();
    }
}