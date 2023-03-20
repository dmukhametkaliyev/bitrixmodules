<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class qbscustomfield extends CModule
{
    var $MODULE_ID = "qbscustomfield";
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
        $this->MODULE_NAME = Loc::getMessage("QBS.CUSTOMFIELD.MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("QBS.CUSTOMFIELD.MODULE_DESC");
        $this->PARTNER_NAME = Loc::getMessage("QBS.CUSTOMFIELD.PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("QBS.CUSTOMFIELD.PARTNER_URI");
    }
    function InstallFiles()
    {
        /*CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/components/qbs/main.field.string", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/qbs/main.field.string", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/components/qbs/crm.field.element", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/qbs/crm.field.element", true, true);*/
    }
    function UnInstallFiles()
    {
        /*DeleteDirFilesEx("/bitrix/components/qbs/main.field.string");
        DeleteDirFilesEx("/bitrix/components/qbs/crm.field.element");*/
    }

    function DoInstall()
    {

        RegisterModule($this->MODULE_ID);
        $this->InstallEvents();
        $this->InstallFiles();
        //RegisterModuleDependences("main", "OnUserTypeBuildList", "qbscustomfield", 'QBS\Customfield\Userfield\CustomString', 'getDescription');
    }

    function DoUninstall()
    {
        //UnRegisterModuleDependences("main", "OnUserTypeBuildList", "qbscustomfield", 'QBS\Customfield\Userfield\CustomString', 'getDescription');
        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallEvents();
        $this->UnInstallFiles();

    }
    function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            'main',
            'OnUserTypeBuildList',
            'qbscustomfield',
            '\QBS\Customfield\Userfield\CustomString',
            'getDescription'
        );
        $eventManager->registerEventHandler(
            'main',
            'OnUserTypeBuildList',
            'qbscustomfield',
            '\QBS\Customfield\Userfield\CustomElement',
            'getDescription'
        );
    }
    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            'qbscustomfield',
            '\QBS\Customfield\Userfield\CustomString',
            'getDescription'
        );
        $eventManager->unRegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            'qbscustomfield',
            '\QBS\Customfield\Userfield\CustomElement',
            'getDescription'
        );

    }
}

