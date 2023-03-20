<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class qbsnotification extends CModule
{
    var $MODULE_ID = "qbsnotification";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    function __construct()
    {
        $arModuleVersion = array();

        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("QBS.NOTIFICATION.MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("QBS.NOTIFICATION.MODULE_DESC");
        $this->PARTNER_NAME = Loc::getMessage("QBS.NOTIFICATION.PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("QBS.NOTIFICATION.PARTNER_URI");
    }

    function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        CAgent::AddAgent(
            'CNotifications::sendNotificationAgent();',
            'qbsnotification',
            'N',
            '',
            '',
            'N',
            '',
        );
    }

    function DoUninstall()
    {
        ModuleManager::unregisterModule($this->MODULE_ID);
        CAgent::RemoveAgent(
            'CNotifications::sendNotificationAgent();',
            'qbsnotification',
            '1'
        );
    }
}
