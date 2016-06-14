<div id="facture">
    <img src="/themes/mespaysansV2/img/mpc/logo_slogan.jpg" />
    <h2>Votre facture du mois de {$mois} {$annee}</h2>
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
            {if $dateCommandeOld!=$dateCommande}
                {if !$cartDto@first}
                    <tr>
                        <td colspan="5"></td>
                        <td class="endOrder">
                            <table>
                            <tr><td><label><b>Total à payer :</b></label></td></tr>
                            <tr><td><label><b>Paiement effectué le :</b></label></td></tr>
                            </table>
                        </td>
                        <td class="total">
                            <table>
                            <tr><td><label><b>{convertPrice price=$totalDueToSupplier}</b></label></td></tr>
                            <tr><td><label><b>-</b></label></td></tr>
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
                        <tr><td><label><b>Total à payer :</b></label></td></tr>
                        <tr><td><label><b>Paiement effectué le :</b></label></td></tr>
                        </table>
                    </td>
                    <td class="total">
                        <table>
                        <tr><td><label><b>{convertPrice price=$totalDueToSupplier}</b></label></td></tr>
                        <tr><td><label><b>-</b></label></td></tr>
                        </table>
                    </td>
                </tr>
            {/if}
        {/foreach}
    </table>
</div>