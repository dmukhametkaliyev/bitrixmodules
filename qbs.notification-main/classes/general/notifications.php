<?php
IncludeModuleLangFile(__FILE__);
use Bitrix\Main\Loader;
use Bitrix\Im as IM;

Loader::includeModule('im');
class CNotifications
{
    public static function sendNotificationAgent()
    {
        $paramValue = COption::GetOptionString("qbsnotification", "field_name", "");
        $activeUsers = array();
        $filter = array("ACTIVE" => "Y");
        $rsUsers = CUser::GetList(($by = "id"), ($order = "asc"), $filter);
        while ($arUser = $rsUsers->Fetch()) {
            $activeUsers[] = $arUser['ID'];
        }
        foreach ($activeUsers as $id) {
            $arFields = array(
                "TO_USER_ID" => $id,
                "FROM_USER_ID" => 1,
                "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                "NOTIFY_MODULE" => "main",
                "NOTIFY_EVENT" => "notify",
                "NOTIFY_MESSAGE" => $paramValue
            );
            CIMNotify::Add($arFields);
        }

        return "sendNotificationAgent();";
    }

}