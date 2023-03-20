<?php
//require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/qbs.notification/classes/general/notifications.php");
CModule::AddAutoloadClasses(
    'qbsnotification',
    array(
        'CNotifications' => 'classes/general/notifications.php'
    )
);




