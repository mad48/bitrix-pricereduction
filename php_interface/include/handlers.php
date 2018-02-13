<?php

if (file_exists($handler_file = $_SERVER["DOCUMENT_ROOT"] . "/local/modules/madsoft.pricereduction/lib/handler.php"))
    require_once($handler_file);


/*
use \Bitrix\Main\Loader;

// работает
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler(
    'catalog',
    'OnPriceUpdate',
    ['\MadSoft\PriceReduction\Lib\Handler', 'onPriceUpdate']
);*/







/*Loader::registerAutoLoadClasses(null, [//"madsoft.pricereduction"
    '\MadSoft\PriceReduction\Lib\Handler' => '/local/modules/madsoft.pricereduction/lib/handler.php',

]);*/

/*Loader::registerAutoLoadClasses(null, [//"madsoft.pricereduction"
    '\MadSoft\PriceReduction' => '/local/components/madsoft/pricereduction/class.php',

]);*/



/*$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandlerCompatible("catalog", "OnBeforePriceUpdate",  array("myClass", "myMethod"));
class myClass
{
    function myMethod($id, $fields)
    {
        $print = print_r($fields, true);
        \Bitrix\Main\Diag\Debug::writeToFile(array('myMethod' => $print), "", "myMethod.log");
    }
}*/


/*$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler("catalog", "OnBeforePriceUpdate",  array("myClass", "myMethod"));
class myClass
{
    function myMethod(\Bitrix\Main\Entity\Event $event)
    {
        $fields = $event->getParameter("fields");
        $print = print_r($fields, true);
        \Bitrix\Main\Diag\Debug::writeToFile(array('myMethod' => $print), "", "myMethod.log");
    }
}*/


//как б..ть заставить это работать?

/*class myClass
{
    function myMethod($id, $fields)
    {
        $print = print_r($fields, true);
        \Bitrix\Main\Diag\Debug::writeToFile(array('myMethod' => $print), "", "myMethod.log");
    }
}


$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->registerEventHandlerCompatible(
    'catalog',
    'OnBeforePriceUpdate',
    'pricereduction',
    'myClass',
    'myMethod');

$handlers = $eventManager->findEventHandlers("catalog", "OnBeforePriceUpdate");

print_r($handlers);*/

