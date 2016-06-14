<div id="listAction">
    <h1>{$title}</h1><a href="/admin1121/index.php?controller=AdminMPFacture&token={$token}">Revenir au menu</a>
    <table>
        {foreach $listOrders as $idOrder=>$listOrderDetailDto}
            <tr class="neworder">
                <td colspan="5" class="orderNumber">Commande n°{$idOrder}</td>
            </tr>
            {assign var="supplierName" value=""}
            {foreach $listOrderDetailDto as $orderDetailDto}
                {if $orderDetailDto->getSupplierName()!=$supplierName}
                    {assign var="supplierName" value=$orderDetailDto->getSupplierName()}
                    <tr class="newsupplier">
                        <td></td>
                        <td colspan="4" class="supplierName">{$supplierName}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Date de livraison</th>
                    </tr>
                {/if}
                <tr>
                    <td></td>
                    <td></td>
                    <td class="detail">{$orderDetailDto->getProductName()}</td>
                    <td class="detail">{$orderDetailDto->getQuantity()}</td>
                    <td class="detail">{$orderDetailDto->getDateWithdrawal()}</td>
                </tr>
            {/foreach}
        {/foreach}
    </table>
</div>
