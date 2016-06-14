<div id="paniersjour" class="mpborder">
    <h3>Vos paniers du {$jour}</h3>
    <div class="annonce">
        Commandez vos paniers des marchés du jour - Nous les livrons l'après midi ou en soirée le jour des marchés.
    </div>
    <div id="listepanier">
        {foreach $aPanier as $panier}
            <div class="panier">
                <div class="cartname" itemprop="name">{$panier.name}<br />{if $panier.reduction}<span class="reduction_cost">En promo !</span>{/if}</div>
                <div class="cart-withdrawal">Prochaine livraison le : <b>{$panier.date}</b></div>
                <div class="clearfix"></div>
                <div class="photo">
                    {if $panier.image}
                        <img src="{$panier.image}" itemprop="image" alt="mespaysans.com - Les paniers du {$jour}"/>
                    {/if}
                </div>
                <div class="description">
                    {$panier.description}
                </div>
                <div>
                    <input type="hidden" name="dateWithDrawal-{$panier.id}" value="{$panier.dateWithDrawal}" />
                </div>
                <div class="prix"><u>Prix du panier :</u> <span class="prixfinal">{if $panier.reduction}<span class="promo">{/if}{$panier.price}€{if $panier.reduction}</span> <span class="reduction_cost">{$panier.reductionCost}€</span> = {$panier.priceReduction}€{/if} TTC</span></div>
                <div class="command" id="{$panier.id}">Commander</div>
            </div>
        {/foreach}
    </div>
    <div class="clearfix"></div>
    <div><a href="/paniers/semaine/" class="btn btn-default button button-small"><span><i class="icon-chevron-left left"></i>Revenir à la liste des paniers de vos marchés</span></a></div>
</div>