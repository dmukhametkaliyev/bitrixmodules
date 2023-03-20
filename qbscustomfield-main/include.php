<?php
use Bitrix\Main\Loader;

if (\Bitrix\Main\Loader::includeModule('qbscustomfield'))
{
    CModule::AddAutoloadClasses(
        'qbscustomfield',
        array(
            '\QBS\Customfield\Userfield\CustomString' => 'lib/userfield/types/CustomString.php',
            '\QBS\Customfield\Userfield\CustomElement' => 'lib/userfield/types/CustomElement.php',
            'CustomStringUfComponent' => 'install/components/bitrix/qbscustomfield.field.customstring/class.php'
        )
    );
}
