<table class="product" width="100%" cellpadding="4" cellspacing="0">
    <thead>
    <tr>
        <th class="product header small">{l s='Product' pdf='true'}</th>
        <th class="product header small">Producteur</th>
        <th class="product header small">{l s='Unit Price' pdf='true'}</th>
        <th class="product header small">T.V.A</th>
        <th class="product header small">{l s='Qty' pdf='true'}</th>
        <th class="product header small">Total T.V.A</th>
        <th class="product header small">{l s='Total' pdf='true'}</th>
    </tr>
    </thead>

    <tbody>
    <!-- PRODUCTS -->
    {assign var="withdrawal" value=''}
    {foreach $listProduct as $product}
        {if $withdrawal!=$product.withdrawal}
            {assign var="withdrawal" value=$product.withdrawal}
            <tr>
                <th class="product header small" colspan="7">Date de livraison des produits suivants : {$product.withdrawal}</th>
            </tr>
        {/if}
        <tr class="product {$bgcolor_class}">
            <td class="product center">
                <b>{$product.name}</b>
            </td>
            <td class="product center">
                {$product.supplierName}
            </td>
            <td class="product center">
                {displayPrice currency=1 price=$product.unitPrice}
            </td>
            <td class="product center">
                {if $product.tax!='0.0'}
                    {$product.tax}
                {else}
                    -
                {/if}
            </td>
            <td class="product center">
                {$product.quantity} {if !empty($product.attribute)}x {$product.attribute}{/if}
            </td>
            <td class="product center">
                {if $product.tax!='0.0'}
                    {displayPrice currency=1 price=$product.totalTax}
                {else}
                    -
                {/if}
            </td>
            <td class="product center">
                {displayPrice currency=1 price=$product.total}
            </td>
        </tr>
        {if $product.panier==1}
            <tr class="product {$bgcolor_class}">
                <td colspan="7">Détail du panier :{$product.description}</td>
            </tr>
        {/if}
    {/foreach}
    <tr style="border:1px solid #000;">
        <th class="header" colspan="5"></th>
        <th class="header">Frais de livraison</th>
        <th class="header">{$shipping}</th>
    </tr>
    {foreach $totalTax as $tax=>$cost}
        {if $tax!='0.0'}
            <tr style="border:1px solid #000;">
                <th class="header" colspan="5"></th>
                <th class="header">Total T.V.A {$tax}</th>
                <th class="header">{$cost}</th>
            </tr>
        {/if}
    {/foreach}
    <tr style="border:1px solid #000;">
        <th class="header" colspan="5"></th>
        <th class="header">Total</th>
        <th class="header">{convertPrice price=$totalPrice}</th>
    </tr>
    <!-- END PRODUCTS -->
    </tbody>
</table>
<br /><br />
<table>
    <tr>
        <th style="text-align: center;font-weight: bold;font-size: 1.6em;margin-bottom: 10px;">
            Découvrez les paysans chez qui vous avez commandé vos produits
        </th>
    </tr>
    {foreach $listSupplier as $supplierDto}
        <tr>
            <td>
                <br /><br />
                <img src="http://www.mespaysans.com/themes/mespaysansV2/img/paysans/{$supplierDto->getIdSupplier()}.jpg" alt="{$supplierDto->getName()}" title="{$supplierDto->getName()}" />
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 10px;">
                {$supplierDto->getDescription()}
            </td>
        </tr>
    {/foreach}
</table>