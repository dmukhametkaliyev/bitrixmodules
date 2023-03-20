<?php

namespace QBS\Customfield\Userfield;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserField\Types\BaseType;
use Bitrix\Main\UserField\Types\StringType;
use CUserTypeManager;

Loc::loadMessages(__FILE__);

class CustomString extends BaseType
{
    public const
        USER_TYPE_ID = 'customfield',
        RENDER_COMPONENT = 'qbs:main.field.string';
        //RENDER_COMPONENT = 'bitrix:main.field.string';

   /*public static function getUserTypeDescription(): array
    {
        return array(
            "USER_TYPE_ID" => self::USER_TYPE_ID,
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => Loc::getMessage("QBS.CUSTOMFIELD.DESCRIPTION"),
            "BASE_TYPE" => CUserTypeManager::BASE_TYPE_STRING
        );
    }*/

    public static function getDescription(): array
    {
        return [
            "USER_TYPE_ID" => self::USER_TYPE_ID,
            "CLASS_NAME" => __CLASS__,
            'DESCRIPTION' => Loc::getMessage('QBS.CUSTOMFIELD.DESCRIPTION'),
            'BASE_TYPE' => CUserTypeManager::BASE_TYPE_STRING,
            "EDIT_CALLBACK" => array(__CLASS__, 'getPublicEdit'),
            "VIEW_CALLBACK" => array(__CLASS__, 'getPublicView')
        ];

    }

    public static function getDbColumnType(): string
    {
        return 'text';
    }
}