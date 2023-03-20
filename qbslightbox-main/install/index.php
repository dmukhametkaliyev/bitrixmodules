<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class qbslightbox extends CModule
{
    var $MODULE_ID = "qbslightbox";
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
        $this->MODULE_NAME = Loc::getMessage("QBS.LIGHTBOX.MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("QBS.LIGHTBOX.MODULE_DESC");
        $this->PARTNER_NAME = Loc::getMessage("QBS.LIGHTBOX.PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("QBS.LIGHTBOX.PARTNER_URI");
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/qbslightbox/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/qbslightbox", true, true);
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/images/qbslightbox/");
    }

    function DoInstall()
    {
        $this->InstallFiles();
        Debug::writeToFile("install");
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallEvents();
        Loader::includeModule('qbslightbox');
    }

    function DoUninstall()
    {
        $this->UnInstallFiles();
        Loader::includeModule('qbslightbox');
        $this->UnInstallEvents();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            'main',
            'onBeforeProlog',
            'qbslightbox',
            '\QBS\Lightbox\Handlers\EventHandler',
            'onBeforeProlog'
        );
    }
    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'main',
            'onBeforeProlog',
            'qbslightbox',
            '\QBS\Lightbox\Handlers\EventHandler',
            'onBeforeProlog'
        );

    }
}

