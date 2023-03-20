<?php

namespace QBS\Lightbox\Handlers;

use Bitrix\Crm\Category\DealCategory;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\IO\File;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use CJSCore;
use CUtil;

class EventHandler
{
    public static function onBeforeProlog()
    {
        Debug::writeToFile("QB Lightbox onBeforeProlog");
        Loader::includeModule('fileman');
        CJSCore::Init(array("jquery"));
        CJSCore::Init(array("qbs_lightbox"));

        /*Debug::writeToFile(CJSCore::Init(array("qbs_lightbox")));
        Debug::writeToFile(CJSCore::isExtensionLoaded('qbs_lightbox'));
        Debug::writeToFile(CJSCore::IsExtRegistered('qbs_lightbox'));
        Debug::writeToFile(CJSCore::IsCoreLoaded());*/
    }
}
