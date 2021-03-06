<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\Engine\Db,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\HeaderUrl;

class CategoryFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        (new ForumModel)->addCategory($this->httpRequest->post('title'));
        HeaderUrl::redirect(Uri::get('forum', 'admin', 'addforum', Db::getInstance()->lastInsertId()), t('The Category was added successfully!'));
    }

}
