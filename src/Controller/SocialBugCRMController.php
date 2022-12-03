<?php
// modules/yourmodule/src/Controller/DemoController.php
namespace MyCompany\socialbugcrm\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class SocialBugCRMController extends FrameworkBundleAdminController
{
    public function demoAction()
    {
        // $yourService = $this->get('mycompany.socialbugcrm.socialbugcrmservice');

        // return $this->render('@Modules/socialbugcrm/views/templates/admin/socialbugcrm.tpl', [
            // 'customMessage' => $yourService->getTranslatedCustomMessage(),
        // ]);
    }
}