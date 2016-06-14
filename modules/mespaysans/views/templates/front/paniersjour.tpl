<div id="paniersjour">
    <div id="listepanier">
        <input type="hidden" name="datewithdrawal" value="{$dateWithDrawal}" />
        {foreach $aPanier as $panier}
            <div class="panier">
                <div class="cartname" itemprop="name">{$panier.name}<br />{if $panier.reduction}<span class="reduction_cost">En promo !</span>{/if}</div>
                <div class="cart-withdrawal">Prochaine livraison le : <b>{$panier.date}</b></div>
                <div class="clearfix"></div>
                <div class="photo">
                    {if $panier.image}
                        <img src="{$panier.image}" itemprop="image" alt="mespaysans.com - Les paniers du {$jour}"/>
                    {/if}
                    <div class="infos">Photo non-contractuelle</div>
                </div>
                <div class="description">
                    {$panier.description}
                </div>
                <div class="prix"><u>Prix du panier :</u> <span class="prixfinal">{if $panier.reduction}<span class="promo">{/if}{$panier.price}€{if $panier.reduction}</span> <span class="reduction_cost">{$panier.reductionCost}€</span> = {$panier.priceReduction}€{/if} TTC</span></div>
                {assign var=contentUrl value='add=1&amp;id_product='|cat:$panier.id|cat:'&token='|cat:$static_token}
                <a id="submit{$panier.id}" class="ajax_add_to_cart_button bouton" href="{$link->getPageLink('cart',false, NULL, $contentUrl, false)|escape:'html':'UTF-8'}" rel="nofollow" title="Ajouter au panier" data-id-product="{$panier.id|intval}">
                    Je vais en prendre
                </a>
            </div>
        {/foreach}
    </div>
    <div class="clearfix"></div>
</div>