<table class="product" width="100%" cellpadding="4" cellspacing="0">
    <thead>
    <tr>
        <th class="product header small" width="20%">{l s='Product' pdf='true'}</th>
        <th class="product header small" width="20%">Détails</th>
        <th class="product header-right small" width="20%">{l s='Unit Price' pdf='true'}</th>
        <th class="product header small" width="20%">{l s='Qty' pdf='true'}</th>
        <th class="product header small" width="20%">{l s='Total' pdf='true'}</th>
    </tr>
    </thead>

    <tbody>
    <!-- PRODUCTS -->
    {foreach $listMarket as $row}
        {assign var="listProduct" value=$row['product']}
	{foreach $listProduct as $product}
            <tr class="product {$bgcolor_class}">
                <td class="product left">
                    {$product.name}
                </td>
                <td class="product left" style="text-align:center;">
                    {if $product.category_panier==$product.category}
                        {$product.description}
                    {else}
                        <span style="font-size: 0.8em;">{$product.supplierName}</span>
                    {/if}
                </td>
                <td class="product right">
                    {$product.unitPrice} €
                </td>
                <td class="product center">
                    {$product.quantity}
                </td>
                <td class="product center">
                    {$product.total} €
                </td>
            </tr>
        {/foreach}
    {/foreach}
    <tr style="border:1px solid #000;">
        <th class="header" colspan="3"></th>
        <th class="header">Total</th>
        <th class="header">{$totalPrice} €</th>
    </tr>
    <!-- END PRODUCTS -->
    </tbody>
</table>