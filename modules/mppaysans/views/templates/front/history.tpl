<div id="espacepro" class="container mpborder">
    <div>
        {include file="$menu"}
    </div>
    <div id="banque">
        <h2>Mon historique de commande</h2>
        <table>
            <tr>
                <th class="table-iteration">Reference commande</td>
                <th class="table-produit">Produit</th>
                <th class="table-quantite">Prix à l'unité</th>
                <th class="table-quantite">Quantité</th>
                <th class="table-remove">Total</th>
                <th class="table-remove">Date de retrait</th>
            </tr>
            {foreach $detail as $commande}
                <tr>
                    <td class="table-iteration">{$commande['order']}</td>
                    <td class="table-produit">{$commande['productName']}</th>
                    <td class="table-quantite">{$commande['price']}</th>
                    <td class="table-quantite">{$commande['quantity']}</th>
                    <td class="table-quantite">{$commande['totalPrice']}</th>
                    <td class="table-quantite">{$commande['dateRetrait']}</th>
                </tr>
            {/foreach}
        </table>
    </div>
</div>