<?php

$arJsConfig = [
    'qbs_lightbox' => [
        'js' => '/local/modules/qbslightbox/js/lightbox.js',
        'css' => '/local/modules/qbslightbox/css/lightbox.css'
    ],
];

foreach ($arJsConfig as $ext => $arExt) {
    CJSCore::RegisterExt($ext, $arExt);
}

CModule::AddAutoloadClasses(
    'qbslightbox',
    array(
        '\QBS\Lightbox\Handlers\EventHandler' => 'lib/Handlers/EventHandler.php'
    )
);
