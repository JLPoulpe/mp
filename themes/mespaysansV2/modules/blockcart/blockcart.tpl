{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- MODULE Block cart -->
{if isset($blockcart_top) && $blockcart_top}
<div{if $PS_CATALOG_MODE} class="header_user_catalog"{/if}>
{/if}
    <div id="cart" class="shopping_cart mpborder colright">
        <a href="{$link->getPageLink($order_process, true)|escape:'html':'UTF-8'}" title="{l s='View my shopping cart' mod='blockcart'}" rel="nofollow">
            <span class="ajax_cart_quantity{if $cart_qties>9} small{/if}">{$cart_qties}</span>
        </a>
        <div class="smart">
            <div class="basketTitle">Votre panier</div>
            <span class="ajax_cart_quantity{if $cart_qties>9} small{/if}">{$cart_qties}</span> produit(s)
        </div>
        <a id="button_order_cart" class="button-smart" href="{$link->getPageLink("$order_process", true)|escape:"html":"UTF-8"}" title="{l s='Check out' mod='blockcart'}" rel="nofollow">
            Commander
        </a>
        {if !$PS_CATALOG_MODE}
            <div class="cart_block block exclusive">
                <div class="block_content">
                    <!-- block list of products -->
                    <div class="cart_block_list{if isset($blockcart_top) && !$blockcart_top}{if isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded' || !$ajax_allowed || !isset($colapseExpandStatus)} expanded{else} collapsed{/if}{/if}">
                        {if $products}
                            <dl class="products">
                                {foreach from=$products item='product' name='myLoop'}
                                    {assign var='productId' value=$product.id_product}
                                    {assign var='productAttributeId' value=$product.id_product_attribute}
                                    <dt data-id="cart_block_product_{$product.id_product}_{if $product.id_product_attribute}{$product.id_product_attribute}{else}0{/if}_{if $product.id_address_delivery}{$product.id_address_delivery}{else}0{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} cartproduct">
                                        <div class="cart-info">
                                            <div class="product-name">
                                                <b><span title="{$product.name}">{$product.name|truncate:25:'...':true|escape:'html':'UTF-8'}</span></b>
                                            </div>
                                            <div class="remove_link">
                                                {if !isset($customizedDatas.$productId.$productAttributeId) && (!isset($product.is_gift) || !$product.is_gift)}
                                                    <a class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, 'delete=1&amp;id_product={$product.id_product}&amp;ipa={$product.id_product_attribute}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$static_token}', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='remove this product from my cart' mod='blockcart'}"></a>
                                                {/if}
                                            </div>
                                            <div class="clear"></div>
                                            <div class="cart_detail">
                                                Quantité : <span class="quantity">{$product.quantity}</span>
                                                <br />
                                                {if isset($product.attributes)}
                                                    {$product.attributes}
                                                {/if}
                                            </div>
                                            <div class="product-price">
                                                {if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice p="`$product.total`"}{else}{displayWtPrice p="`$product.total_wt`"}{/if}
                                            </div>
                                        </div>
                                    </dt>
                                    {if isset($product.attributes_small)}
                                        <dd data-id="cart_block_combination_of_{$product.id_product}{if $product.id_product_attribute}_{$product.id_product_attribute}{/if}_{$product.id_address_delivery|intval}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
                                    {/if}
                                    <!-- Customizable datas -->
                                    {if isset($customizedDatas.$productId.$productAttributeId[$product.id_address_delivery])}
                                        {if !isset($product.attributes_small)}
                                            <dd data-id="cart_block_combination_of_{$product.id_product}_{if $product.id_product_attribute}{$product.id_product_attribute}{else}0{/if}_{if $product.id_address_delivery}{$product.id_address_delivery}{else}0{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
                                        {/if}
                                        <ul class="cart_block_customizations" data-id="customization_{$productId}_{$productAttributeId}">
                                            {foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization' name='customizations'}
                                                <li name="customization">
                                                    <div data-id="deleteCustomizableProduct_{$id_customization|intval}_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$product.id_address_delivery|intval}" class="deleteCustomizableProduct">
                                                        <a class="ajax_cart_block_remove_link" href="{$link->getPageLink("cart", true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$static_token}", true)|escape:"html":"UTF-8"}" rel="nofollow">&nbsp;</a>
                                                    </div>
                                                    {if isset($customization.datas.$CUSTOMIZE_TEXTFIELD.0)}
                                                        {$customization.datas.$CUSTOMIZE_TEXTFIELD.0.value|replace:"<br />":" "|truncate:28:'...'|escape:'html':'UTF-8'}
                                                    {else}
                                                        {l s='Customization #%d:' sprintf=$id_customization|intval mod='blockcart'}
                                                    {/if}
                                                </li>
                                            {/foreach}
                                        </ul>
                                        {if !isset($product.attributes_small)}</dd>{/if}
                                    {/if}
                                    {if isset($product.attributes_small)}</dd>{/if}
                                {/foreach}
                            </dl>
                        {/if}
                        <p class="cart_block_no_products{if $products} unvisible{/if}">
                            {l s='No products' mod='blockcart'}
                        </p>
                        {if $discounts|@count > 0}
                            <table class="vouchers"{if $discounts|@count == 0} style="display:none;"{/if}>
                                {foreach from=$discounts item=discount}
                                    {if $discount.value_real > 0}
                                        <tr class="bloc_cart_voucher" data-id="bloc_cart_voucher_{$discount.id_discount}">
                                            <td class="quantity">1x</td>
                                            <td class="name" title="{$discount.description}">
                                                {$discount.name|truncate:18:'...'|escape:'html':'UTF-8'}
                                            </td>
                                            <td class="price">
                                                -{if $priceDisplay == 1}{convertPrice price=$discount.value_tax_exc}{else}{convertPrice price=$discount.value_real}{/if}
                                            </td>
                                            <td class="delete">
                                                {if strlen($discount.code)}
                                                    <a class="delete_voucher" href="{$link->getPageLink("$order_process", true)}?deleteDiscount={$discount.id_discount}" title="{l s='Delete' mod='blockcart'}" rel="nofollow">
                                                        <i class="icon-remove-sign"></i>
                                                    </a>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/if}
                                {/foreach}
                            </table>
                        {/if}
                        <div class="cart-prices">
                            {if $show_wrapping}
                                <div class="cart-prices-line">
                                    {assign var='cart_flag' value='Cart::ONLY_WRAPPING'|constant}
                                    <span class="price cart_block_wrapping_cost">
                                        {if $priceDisplay == 1}
                                            {convertPrice price=$cart->getOrderTotal(false, $cart_flag)}{else}{convertPrice price=$cart->getOrderTotal(true, $cart_flag)}
                                        {/if}
                                    </span>
                                    <span>
                                        {l s='Wrapping' mod='blockcart'}
                                    </span>
                               </div>
                            {/if}
                            {if $show_tax && isset($tax_cost)}
                                <div class="cart-prices-line">
                                    <span class="price cart_block_tax_cost ajax_cart_tax_cost">{$tax_cost}</span>
                                    <span>{l s='Tax' mod='blockcart'}</span>
                                </div>
                            {/if}
                            <div class="cart-prices-line last-line">
                                <span class="price cart_block_total ajax_block_cart_total">{$product_total}</span>
                                <span>{l s='Total' mod='blockcart'}</span>
                            </div>
                            {if $use_taxes && $display_tax_label == 1 && $show_tax}
                                <p>
                                {if $priceDisplay == 0}
                                    {l s='Prices are tax included' mod='blockcart'}
                                {elseif $priceDisplay == 1}
                                    {l s='Prices are tax excluded' mod='blockcart'}
                                {/if}
                                </p>
                            {/if}
                        </div>
                        <p class="cart-buttons">
                            <a id="button_order_cart" class="btn btn-default button button-small" href="{$link->getPageLink("$order_process", true)|escape:"html":"UTF-8"}" title="{l s='Check out' mod='blockcart'}" rel="nofollow">
                                <span>
                                    {l s='Check out' mod='blockcart'}<i class="icon-chevron-right right"></i>
                                </span>
                            </a>
                        </p>
                        <div class="icone-paiement"><img src="/themes/mespaysansV2/img/paypal.jpg" />&nbsp;<img src="/themes/mespaysansV2/img/paiementsecurise.jpg" /></div>
                    </div>
                </div>
            </div><!-- .cart_block -->
        {/if}
    </div>
{if isset($blockcart_top) && $blockcart_top}
</div>
{/if}
{counter name=active_overlay assign=active_overlay}
{if !$PS_CATALOG_MODE && $active_overlay >= 1}
    <div id="layer_cart">
        <div class="layer_cart_cart">
            <div class="supplierTalk">
                <div class="supplierFace"><div class="product-image-container layer_cart_img"></div></div>
                <div class="supplierMore">
                    <div class="etavecceci"></div>
                    <div class="choices">
                        <a class="bouton" onclick="closeAllCartDiv();">Mmmh, je regarde</a>
                    </div>
                    <div class="choices">
                        <a class="bouton" href="{$link->getPageLink("$order_process", true)|escape:"html":"UTF-8"}" title="{l s='Proceed to checkout' mod='blockcart'}" rel="nofollow">Merci, ca sera tout</a>	
                    </div>
                    <div class="clear"></div>
                    <div class="noshow">
                        <a class="retour" onclick="closeAllCartDiv();noShow();">Ne plus afficher cette fenêtre</a>
                    </div>
                </div>
            </div>
        </div>
        <!--div class="crossseling"></div-->
    </div> <!-- #layer_cart -->
    <div id="layerCartOverlay" class="layer_cart_overlay"></div>
{/if}
{strip}
{addJsDef CUSTOMIZE_TEXTFIELD=$CUSTOMIZE_TEXTFIELD}
{addJsDef img_dir=$img_dir|escape:'quotes':'UTF-8'}
{addJsDef generated_date=$smarty.now|intval}
{addJsDef ajax_allowed=$ajax_allowed|boolval}

{addJsDefL name=customizationIdMessage}{l s='Customization #' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=removingLinkText}{l s='remove this product from my cart' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=freeShippingTranslation}{l s='Free shipping!' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=freeProductTranslation}{l s='Free!' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=delete_txt}{l s='Delete' mod='blockcart' js=1}{/addJsDefL}
{/strip}
<!-- /MODULE Block cart -->