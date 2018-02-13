<?
namespace MadSoft;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use \Bitrix\Main\Loader;
use \Bitrix\Main\SystemException;
use \Bitrix\Main\Localization\Loc;


class PriceReduction extends \CBitrixComponent
{
    protected $errors = array();

    /**
     * include lang files
     */
    public function onIncludeComponentLang()
    {
        Loc::loadMessages(__FILE__);
    }

    public function doNothing()
    {
        echo "MadSoft-PriceReduction-doNothing";

        return;

    }

    /**
     * Event called from includeComponent before component execution.
     * Takes component parameters as argument and should return it formatted as needed.
     *
     * @param $params
     * @return mixed
     */
    public function onPrepareComponentParams($params)
    {

        global $USER;

/*        $params['HBLOCK_ID'] = isset($params['HBLOCK_ID']) ? (int)$params['HBLOCK_ID'] : 0;
        $params['PRODUCT_ID'] = isset($params['PRODUCT_ID']) ? (int)$params['PRODUCT_ID'] : 0;
        $params['BUTTON_ID'] = isset($params['BUTTON_ID']) ? (string)$params['BUTTON_ID'] : '';*/
        $params['BUTTON_CLASS'] = isset($params['BUTTON_CLASS']) ? (string)$params['BUTTON_CLASS'] : '';
        $params['DEFAULT_DISPLAY'] = isset($params['DEFAULT_DISPLAY']) ? (bool)$params['DEFAULT_DISPLAY'] : true;

/*        if (!$params['PRODUCT_ID'])
            $this->errors[] = Loc::getMessage('CPS_REQUIRED_PARAMETER', array('#PARAM#' => 'PRODUCT_ID'));
        if (!$params['BUTTON_ID'])
            $this->errors[] = Loc::getMessage('CPS_REQUIRED_PARAMETER', array('#PARAM#' => 'BUTTON_ID'));*/

        if (!$_SESSION['PRICE_REDUCTION_EMAIL']) {
            $params['PRICE_REDUCTION_EMAIL'] = !empty($USER->getEmail()) ? $USER->getEmail() : '';
        } else {
            $params['PRICE_REDUCTION_EMAIL'] = $_SESSION['PRICE_REDUCTION_EMAIL'];
        }

        return $params;
    }

    /**
     * Prepare data to render.
     * @throws SystemException
     */
    protected function formatResult()
    {
        if ($this->errors)
            throw new SystemException(current($this->errors));

/*        $this->arResult['HBLOCK_ID'] = $this->arParams['HBLOCK_ID'];
        $this->arResult['PRODUCT_ID'] = $this->arParams['PRODUCT_ID'];
        $this->arResult['BUTTON_ID'] = $this->arParams['BUTTON_ID'];*/
        $this->arResult['BUTTON_CLASS'] = $this->arParams['BUTTON_CLASS'];
        $this->arResult['DEFAULT_DISPLAY'] = $this->arParams['DEFAULT_DISPLAY'];
        $this->arResult['PRICE_REDUCTION_EMAIL'] = $this->arParams['PRICE_REDUCTION_EMAIL'];

    }


    /**
     * check needed modules
     * @throws LoaderException
     */
    protected function checkModules()
    {
        if (!Loader::includeModule('catalog'))
            throw new SystemException(Loc::getMessage('MODULE_NOT_INSTALLED', array('#NAME#' => 'catalog')));
    }


    /**
     * Function calls __includeComponent in order to execute the component.
     */
    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->formatResult();

            $this->includeComponentTemplate();

        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

}