<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Interkassa API for PHP
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT-style license
 * @package Interkassa
 * @author Anton Suprun <kpobococ@gmail.com>
 * @author Odarchenko N.D. <odarchenko.n.d@gmail.com>
 * @version 2.0.0
 */

/**
 * Interkassa base class
 *
 * This class is used to initialize the library and also contains several
 * constants.
 *
 * @license MIT-style license
 * @package Interkassa
 * @author Anton Suprun <kpobococ@gmail.com>
 * @author Odarchenko N.D. <odarchenko.n.d@gmail.com>
 * @version 2.0.0
 */
class Model_Interkassa
{
    /**
     * URL method constant
     *
     * @see Interkassa_Payment::setSuccessMethod()
     * @see Interkassa_Payment::setFailMethod()
     * @see Interkassa_Payment::setStatusMethod()
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_LINK = 'LINK';
    const METHOD_OFF = 'OFF';


    /**
     * State constant
     *
     * @see Interkassa_Status::getState()
     */
    const STATE_SUCCESS = 'success';
    const STATE_FAIL = 'fail';
    /**

    /**
     * Fees payer constant
     *
     * @see Interkassa_Status::getFeesPayer()
     */
    const FEES_PAYER_SHOP = 0;
    const FEES_PAYER_BUYER = 1;
    const FEES_PAYER_EQUAL = 2;
}