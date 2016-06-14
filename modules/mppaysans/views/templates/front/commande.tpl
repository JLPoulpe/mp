<div id="espacepro" class="container mpborder">
    <div>
        {include file="$menu"}
    </div>
    <div>
        <h2>Mes commandes en cours</h2>
        <fieldset class="legende"><legend>Comment ça marche</legend>
            Vous trouverez ci-dessous la liste des commandes qui vous ont été passées pour les marchés à venir.
            <br />
            Si tous les produits commandés peuvent être livré, cliquez sur "Valider la commande" pour informer le client que sa commande est en préparation.
            <br />
            Si un produit ne peut être livré, cliquez sur la case à coché à côté de celui-ci pour prévenir le client.
        </fieldset>
        <table>
            <thead>
                <tr>
                    <th>Numéro de commande</th>
                    <th>Date de retrait</th>
                    <th>Lieu de retrait</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {assign var=reference value=''}
                {if $commandes|count>0}
                    {assign var='idMarket' value=''}
                    {foreach $commandes as $commande}
                        {assign var='marketDto' value=$commande->getMarketDto()}
                        {assign var='productDto' value=$commande->getProductDto()}
                        {if $reference!=$commande->getOrderReference() || $idMarket!=$commande->getIdMarket()}
                            {assign var=iterationProduct value=1}
                            <tr class="new-reference">
                                <td class="numero-cmd">
                                    {$commande->getOrderReference(1)}
                                </td>
                                <td class="date-retrait">{$commande->getDateWithdrawal()}</td>
                                <td class="lieu-retrait">{$marketDto->getName()} - {$marketDto->getCity()}</td>
                                <td><a class="lien-validation" href="/index.php?controller=espace-pro&mod=validate&id_cart={$commande->getIdCart()}" name="{$commande->getIdCart()}">Valider la commande</a></td>
                            </tr>
                            {assign var='idMarket' value=$commande->getIdMarket()}
                        {else}
                            {assign var=iterationProduct value=$iterationProduct+1}
                        {/if}
                        <tr>
                            <td colspan="4">
                                <table>
                                {if $reference!=$commande->getOrderReference()}
                                    <tr>
                                        <td class="table-iteration"></td>
                                        <th class="table-produit">Produit</th>
                                        <th class="table-quantite">Quantité</th>
                                        <th class="table-remove"></th>
                                    </tr>
                                    {assign var=reference value=$commande->getOrderReference()}
                                {/if}
                                <tr id="product-{$commande->getIdProduct()}">
                                    <td class="table-iteration">{$iterationProduct}</td>
                                    <td class="table-produit">{$productDto->getProductName()}</td>
                                    <td class="table-quantite">{$commande->getQuantity()}</td>
                                    <td class="table-remove"><input type="checkbox" class="product-remove" name="cart-{$commande->getIdCart()}" value="{$commande->getIdProduct()}" title="Annuler le produit" /></td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                    {/foreach}
                {else}
                    <tr><td class="info" colspan="3">Aucune commande en attente</td></tr>
                {/if}
            </tbody>
        </table>
    </div>
</div>