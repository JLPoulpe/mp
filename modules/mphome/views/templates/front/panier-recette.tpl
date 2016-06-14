<div id="panierrecette" class="mpborder">
    <h3>Les recettes pour bébé</h3>
    <div id="listepanier">
        {foreach $aPanier as $panier}
            <div class="panier-recette">
                <div class="cartname" itemprop="name">{$panier.name}<br /></div>
                <div class="cart-withdrawal">Prochaine livraison le : <b>19/01/2016</b></div>
                <div class="clearfix"></div>
                <div class="photo">
                    {if $panier.image}
                        <img src="{$panier.image}" itemprop="image" alt="mespaysans.com - Les paniers du {$jour}"/>
                    {/if}
                    <a href="{$panier.recette}" target="_blank">Afficher la recette</a>
                </div>
                <div class="description">
                    {$panier.description}
                </div>
                <div>
                    <input type="hidden" name="dateWithDrawal-{$panier.id}" value="2016-01-19" />
                </div>
                <div class="prix"><u>Prix du panier :</u> <span class="prixfinal">{convertPrice price=$panier.price} € TTC</span></div>
                <div class="command" id="{$panier.id}">Commander</div>
            </div>
        {/foreach}
    </div>
    <div class="clearfix"></div>
</div>