<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/mad/pricereduction/class.php');

use Bitrix\Main\Localization\Loc;

//use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

use Bitrix\Main\Page\Asset;

CJSCore::Init(array("jquery"));
Asset::getInstance()->addJs($this->GetFolder() . '/fancybox.js');
Asset::getInstance()->addCss($this->GetFolder() . '/fancybox.css');
//var_dump($arResult);
?>

<script type="text/javascript">

    BX.ready(function () {

        BX.addCustomEvent('onCatalogStoreProductChange', function (offerId) {
            if (document.getElementById('productid')) {
                document.getElementById('productid').value = offerId;
            }
        });
    });
</script>

<p id="pricereductionbtn">
    <a class="modalbox <?= $arResult['BUTTON_CLASS'] ?>"
       href="#inline"><?= Loc::getMessage('PRICE_REDUCTION_SUBSCRIBE_BUTTON_NAME') ?></a>
</p>

<!-- hidden inline form -->
<div id="inline" style="display: none" class="popup-window popup-window-content-white popup-window-with-titlebar"
     style="z-index: 1100; position: absolute; display: block; ">

    <div class="popup-window-titlebar">
        <span class="popup-window-titlebar-text"><?= Loc::getMessage('PRICE_REDUCTION_TITLE') ?></span>
    </div>


    <form id="contact" name="contact" action="#" method="post">
        <label for="email">Ваш E-mail </label>
        <?= bitrix_sessid_post() ?>
        <input type="email" id="email" name="email" class="txt"
               value="<?= htmlspecialcharsbx($arResult['PRICE_REDUCTION_EMAIL']) ?>">
        <input type="hidden" id="productid" name="productid" class="txt" value="">

        <br>

        <div class="popup-window-buttons">
            <span class="btn btn-primary" style="margin-bottom: 0px; border-bottom: 0px none transparent;">
                <span id="send">Подписаться</span>
            </span>
        </div>

    </form>
</div>


<script type="text/javascript">
    function validateEmail(email) {
        var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return reg.test(email);
    }

    $(document).ready(function () {
        $(".modalbox").fancybox();
        $("#contact").submit(function () {
            return false;
        });


        $("#send").on("click", function () {
            var emailval = $("#email").val();

            var emailvallen = emailval.length;

            var mailvalid = validateEmail(emailval);

            if (mailvalid == false || emailvallen == 0) {
                $("#email").addClass("error");
            }
            else if (mailvalid == true) {
                $("#email").removeClass("error");
            }


            if (mailvalid == true) {

                $("#send").replaceWith("<em>отправка...</em>");

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '/local/components/madsoft/pricereduction/templates/subs.php',
                    data: $("#contact").serialize(),
                    success: function (data) {

                        if (data.success == true) {
                            $("#contact").fadeOut("fast", function () {

                                if (data.subscribe == 'now') {

                                    $(this).before('<div class="bx-catalog-popup-content" style="display: block;"><p class="bx-catalog-popup-message text-center"><?= Loc::getMessage('PRICE_REDUCTION_SUCCESS_SUBSCRIBE') ?></p><br></div>');
                                    setTimeout("$.fancybox.close()", 1000);
                                    $("#pricereductionbtn").replaceWith("<div><?= Loc::getMessage('PRICE_REDUCTION_SUCCESS_SUBSCRIBE') ?></div>");

                                }

                                if (data.subscribe == 'already') {

                                    $(this).before('<div class="bx-catalog-popup-content" style="display: block;"><p class="bx-catalog-popup-message text-center"><?= Loc::getMessage('PRICE_REDUCTION_ALREADY_SUBSCRIBE') ?></p><br></div>');
                                    setTimeout("$.fancybox.close()", 1000);
                                    $("#pricereductionbtn").replaceWith("<div><?= Loc::getMessage('PRICE_REDUCTION_ALREADY_SUBSCRIBE') ?></div>");

                                }
                            });
                        }


                        /*                        if (data.subscribe == tru4e) {
                         $("#contact").fadeOut("fast", function () {
                         $(this).before('<div class="bx-catalog-popup-content" style="display: block;"><p class="bx-catalog-popup-message text-center">Вы успешно подписались на снижение цены товара</p><br></div>');
                         setTimeout("$.fancybox.close()", 1000);
                         $("#pricereductionbtn").replaceWith("<div><?= Loc::getMessage('PRICE_REDUCTION_ALREADY_SUBSCRIBE') ?></div>");
                         });
                         }*/
                    }
                });
            }
        });
    });
</script>

