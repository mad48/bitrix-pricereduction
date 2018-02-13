<?php
use Bitrix\Main\Loader;


//Loader::includeModule("madsoft.pricereduction");


Loader::registerAutoLoadClasses(null, [//"madsoft.pricereduction"
    '\MadSoft\PriceReduction\Lib\Handler' => 'lib/handler.php',
]);
//\Bitrix\Main\Diag\Debug::writeToFile(array('handler' => "handler"), "", "handler.log");