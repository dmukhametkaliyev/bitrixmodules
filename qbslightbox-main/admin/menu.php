<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('qbslightbox')) return;

Loc::loadMessages(__FILE__);

return array();
