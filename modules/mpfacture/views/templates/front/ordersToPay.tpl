<div id="listAction">
    <h1>{$title}</h1><a href="/admin1121/index.php?controller=AdminMPFacture&token={$token}">Revenir au menu</a>
    <table>
        {assign var="listIdOrder" value=""}
        {assign var="idSupplier" value=""}
        {foreach $listOrders as $listOrderDetailDtoBySupplier}
            {foreach $listOrderDetailDtoBySupplier as $idOrder=>$listOrderDetailDto}
                {assign var="oldIdOrder" value=""}
                {foreach $listOrderDetailDto as $orderDetailDto}
                    {if $idSupplier!=$orderDetailDto->getIdSupplier()}
                        {if !$listOrderDetailDtoBySupplier@first}
                            <tr class="neworder {$idSupplier}">
                                <td colspan="2"></td>
                                <th>Total : </th>
                                <th>{convertPrice price=$totalSupplier}</th>
                                <td><button value="{$idSupplier}-{$listIdOrder}">Payer</button></td>
                            </tr>
                            {assign var="listIdOrder" value=""}
                        {/if}
                        {assign var="idSupplier" value=$orderDetailDto->getIdSupplier()}
                        <tr class="neworder {$idSupplier}">
                            <td colspan="5" class="orderNumber">{$orderDetailDto->getSupplierName()}</td>
                        </tr>
                        {assign var="totalSupplier" value=0}
                    {/if}
                    {if $idOrder!=$oldIdOrder}
                        {assign var="oldIdOrder" value=$idOrder}
                        {assign var="listIdOrder" value=$listIdOrder|cat:$idOrder|cat:'|'}
                        <tr class="newsupplier {$idSupplier}">
                            <td></td>
                            <td class="supplierName">Commande {$idOrder}</td>
                            <td colspan="3"></td>
                        </tr>
                        <tr class="{$idSupplier}">
                            <td></td>
                            <th>Produit</th>
                            <th>Prix</th>
                            <th>A payer</th>
                            <td></td>
                        </tr>
                    {/if}
                    <tr class="{$idSupplier}">
                        <td></td>
                        <td class="detail">{$orderDetailDto->getProductName()}</td>
                        <td class="detail">{convertPrice price=$orderDetailDto->getTotalPriceTaxIncl()}</td>
                        <td class="detail">{convertPrice price=$orderDetailDto->getPriceForSupplier()}</td>
                        <td></td>
                    </tr>
                    {assign var="totalSupplier" value=$totalSupplier+$orderDetailDto->getPriceForSupplier()}
                    {if $listOrderDetailDtoBySupplier@last && $listOrderDetailDto@last && $orderDetailDto@last}
                        <tr class="neworder {$idSupplier}">
                            <td colspan="2"></td>
                            <th>Total : </th>
                            <th>{convertPrice price=$totalSupplier}</th>
                            <td><button value="{$idSupplier}-{$listIdOrder}">Payer</button></td>
                        </tr>
                        {assign var="listIdOrder" value=""}
                    {/if}
                {/foreach}
            {/foreach}
        {/foreach}
    </table>
</div>
