<div id="espacepro">
    <h2>Vos commandes</h2>
    <table>
        <tr>
            <th>Date de la commande</th>
            <th>Nom du produit</th>
            <th>Prix de référence</th>
            <th>Quantité commandé</th>
            <th>Montant commandé</th>
            <th>Date de retrait</th>
            <th>Montant à payer au producteur</th>
        </tr>
        {assign var=dateCommandeOld value=""}
        {assign var=totalDueToSupplier value=0}
        {foreach $listCommands as $cartDto}
            {assign var=dateCommande value=$cartDto->getDateWithdrawal()}
            {assign var=datePaiement value=$cartDto->getDatePayment()}
            {if $dateCommandeOld!=$dateCommande}
                {if !$cartDto@first}
                    <tr>
                        <td colspan="5"></td>
                        <td class="endOrder">
                            <table>
                            <tr><td><label><b>Date de récupération des produits :</b></label></td></tr>
                            <tr><td><label><b>Total à payer :</b></label></td></tr>
                            <tr><td><label><b>Paiement effectué le :</b></label></td></tr>
                            </table>
                        </td>
                        <td class="total">
                            <table>
                            <tr><td><label><b>{$dateCommande}</b></label></td></tr>
                            <tr><td><label><b>{convertPrice price=$totalDueToSupplier}</b></label></td></tr>
                            <tr><td><label><b>{if !empty($datePaiement)}{$datePaiement}{/if}</b></label></td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="7"></td></tr>
                    {assign var=totalDueToSupplier value=0}
                {/if}
                <tr class="newDate">
                    <td>{$cartDto->getDateWithdrawal()}</td>
                    <td colspan="6"></td>
                </tr>
                {assign var=dateCommandeOld value=$dateCommande}
            {/if}
            {assign var=totalDueToSupplier value=$totalDueToSupplier+$cartDto->getPriceForSupplier()}
            <tr>
                <td></td>
                <td class="productName">{$cartDto->getProductName()}</td>
                <td>{convertPrice price=$cartDto->getProductPrice()} / {$cartDto->getUnity()}</td>
                <td>{$cartDto->getQuantity()}</td>
                <td>{convertPrice price=$cartDto->getPriceIncludeTax()}</td>
                <td>-</td>
                <td>{convertPrice price=$cartDto->getPriceForSupplier()}</td>
            </tr>
            {if $cartDto@last}
                <tr>
                    <td colspan="5"></td>
                    <td class="endOrder">
                        <table>
                        <tr><td><label><b>Date de récupération des produits :</b></label></td></tr>
                        <tr><td><label><b>Total à payer :</b></label></td></tr>
                        <tr><td><label><b>Paiement effectué le :</b></label></td></tr>
                        </table>
                    </td>
                    <td class="total">
                        <table>
                        <tr><td><label><b>{$dateCommande}</b></label></td></tr>
                        <tr><td><label><b>{convertPrice price=$totalDueToSupplier}</b></label></td></tr>
                        <tr><td><label><b>{if !empty($datePaiement)}{$datePaiement}{/if}</b></label></td></tr>
                        </table>
                    </td>
                </tr>
            {/if}
        {/foreach}
    </table>
    <hr />
    <div class="pdf">
        <h3>Récupérer sa facture</h3>
        <form action="/espace-pro/facture?content_only=1" method="post" target="_blank">
            <select name="mois">
                {foreach $listDates as $date}
                    <option value="{$date.value}">{$date.label}</option>
                {/foreach}
            </select>
            <input type="submit" value="Recupérer la facture" />
        </form>
    </div>
</div>
<ul class="footer_links clearfix">
    <li><a class="btn btn-default button button-small" href="/mon-compte" title="Mon compte"><span><i class="icon-chevron-left"></i>Mon compte</span></a></li>
</ul>