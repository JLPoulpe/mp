<div id="listAction">
    <h1>{$title}</h1><a href="/admin1121/index.php?controller=AdminMPFacture&token={$token}">Revenir au menu</a>
    <form action="/admin1121/index.php?controller=AdminMPFacture&method=alreadyPaid&token={$token}" method="post">
        <select name="idSupplier" class="listSupplier">
            <option value="">Choisir un producteur</option>
        {foreach $listSupplier as $supplier}
            <option value="{$supplier.id_supplier}"{if !empty($idSupplier) && $idSupplier==$supplier.id_supplier} selected="selected"{/if}>{$supplier.name}</option>
        {/foreach}
        </select>
        <input type="submit" value="Rechercher" />
    </form>
    {if !empty($listOrders)}
        <table>
        {assign var="idOrder" value=0}
        {assign var="totalPaid" value=0}
        {foreach $listOrders as $orderDetailDto}
            {if $idOrder!=$orderDetailDto->getIdOrder()}
                {if !$orderDetailDto@first}
                    <tr>
                        <td colspan="4"></td>
                        <th>Total payé : {convertPrice price=$totalPaid}</th>
                    </tr>
                    {assign var="totalPaid" value=0}
                {/if}
                <tr class="neworder">
                    <td colspan="5" class="orderNumber">Commande n°{$orderDetailDto->getIdOrder()} - Payé le : {$orderDetailDto->getDatePayment()}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th></th>
                </tr>
                {assign var="idOrder" value=$orderDetailDto->getIdOrder()}
            {/if}
            <tr>
                <td></td>
                <td></td>
                <td class="detail">{$orderDetailDto->getProductName()}</td>
                <td class="detail">{$orderDetailDto->getQuantity()}</td>
                <td></td>
            </tr>
            {assign var="totalPaid" value=$totalPaid+$orderDetailDto->getPriceForSupplier()}
            {if $orderDetailDto@last}
                <tr>
                    <td colspan="4"></td>
                    <th>Total payé : {convertPrice price=$totalPaid}</th>
                </tr>
            {/if}
        {/foreach}
        </table>
    {/if}
</div>
