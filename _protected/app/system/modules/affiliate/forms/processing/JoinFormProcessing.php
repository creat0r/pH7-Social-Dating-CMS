<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Util\Various,
PH7\Framework\Ip\Ip,
PH7\Framework\Date\CDateTime,
PH7\Framework\Mvc\Router\Uri;

class JoinFormProcessing extends Form
{

    private $iActiveType;

    public function __construct()
    {
        parent::__construct();

        $this->iActiveType = DbConfig::getSetting('affActivationType');
    }

    public function step1()
    {
        $sBirthDate = $this->dateTime->get($this->httpRequest->post('birth_date'))->date('Y-m-d');

        $aData = [
            'email' => $this->httpRequest->post('mail'),
            'username' => $this->httpRequest->post('username'),
            'password' => $this->httpRequest->post('password'),
            'first_name' => $this->httpRequest->post('first_name'),
            'last_name' => $this->httpRequest->post('last_name'),
            'sex' => $this->httpRequest->post('sex'),
            'birth_date' => $sBirthDate,
            'country' => $this->httpRequest->post('country'),
            'city' => $this->httpRequest->post('city'),
            'state' => $this->httpRequest->post('state'),
            'zip_code' => $this->httpRequest->post('zip_code'),
            'ip' => Ip::get(),
            'prefix_salt' => Various::genRnd(),
            'suffix_salt' => Various::genRnd(),
            'hash_validation' => Various::genRnd(),
            'current_date' => (new CDateTime)->get()->dateTime('Y-m-d H:i:s'),
            'is_active' => $this->iActiveType
        ];

        $oAffModel = new AffiliateModel;

        $iTimeDelay = (int) DbConfig::getSetting('timeDelayUserRegistration');
        if(!$oAffModel->checkWaitJoin($aData['ip'], $iTimeDelay, $aData['current_date'], 'Affiliates'))
        {
            \PFBC\Form::setError('form_join_aff', Form::waitRegistrationMsg($iTimeDelay));
        }
        elseif(!$oAffModel->join($aData))
        {
            \PFBC\Form::setError('form_join_aff', t('An error occurred during registration!<br /> Please try again with other information in the form fields or come back later.'));
        }
        else
        {   // Send an email and sets the welcome message.
            \PFBC\Form::setSuccess('form_join_aff', t('Your affiliate account has been created! %0%', (new Registration)->sendMail($aData)->getMsg()));
        }

        unset($oAffModel);
    }

}
