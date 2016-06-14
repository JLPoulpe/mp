<div class="mpborder">
    <h3>Les paniers de vos marchés du Val d'Oise</h3>
    <div class="annonce">
        Chaque panier est composé de produits récupérés le matin chez les producteurs.
    </div>
    <div id="listepanier">
        {foreach $aPanier as $panier}
            <div class="panieridf">
                <div class="cartname" itemprop="name">{$panier.name}<br />{if $panier.reduction}<span class="reduction_cost">En promo !</span>{/if}</div>
                <div class="cart-withdrawal">
                    Choisissez la date de livraison : 
                    <select name="datewidrawal" id="{$panier.id}">
                        {foreach $panier.listeDate as $key=>$date}
                            <option value="{$key}">{$date}</option>
                        {/foreach}
                    </select>
                </div>
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
                    <input type="hidden" name="dateWithDrawal-{$panier.id}" value="{$panier.date}" />
                </div>
                <div class="prix"><u>Prix du panier :</u> <span class="prixfinal">{if $panier.reduction}<span class="promo">{/if}{$panier.price}€{if $panier.reduction}</span> <span class="reduction_cost">{$panier.reductionCost}€</span> = {$panier.priceReduction}€{/if} TTC</span></div>
                <div class="command" id="{$panier.id}">Commander</div>
            </div>
        {/foreach}
        <div class="clearfix"></div>
        {if !empty($aPanierSemaine)}
            <p style="height:50px;"></p>
            <h3>Les paniers du Sud-Ouest</h3>
            <h4>Les paniers du Sud-Ouest proviennent directement de l'un de nos producteurs de Gironde.<br />Pour vous assurer fraîcheur et qualité du produit, leur commande se fait une semaine à l'avance.</h4>
            <center>
                <a onclick="loadSupplierDetail(17)" style="cursor:pointer"><img src="http://www.mespaysans.com/img/paysans/bandeau/17.jpg" /></a>
                <div id="idSupplier_17" class="supplier-detail" style="display:none;">
                    <div id="portrait" class="descriptif">
                        Myriam, Bruno et Patrice DUCAMP sont éleveurs de canards en Chalosse à Baigts, près de Dax, dans Les Landes (40).
                        <br />
                        <br />Patrice, qui s'est installé en 1989, représente la 3ème génération de cette famille d'éleveurs.
                        <br />Avec son père ils cultivaient 65ha de maïs et s'occupaient d'un atelier d’élevage gavage de canards mulards.
                        <br />En 1991, son épouse Myriam les a rejoint.
                        <br />En 1995, 2 solutions s’offraient à eux:
                        <br />_augmenter le nombre de canards en gavage et travailler pour des groupes industriels ou,
                        <br />_moins gaver et vendre leurs productions directement.
                        <br />Le choix a vite été fait!!
                        <br />Aimant l’authenticité, la simplicité et les contacts humains, ils ont décidés de faire eux-mêmes leurs conserves et de les vendre sur les marchés. Des locaux ont d'abord été loués puis, en 2000, ils ont créé leur conserverie.
                        <br />Aujourd’hui monsieur Ducamp père a pris sa retraite et son petit fils Bruno, en cours d’installation, représente la 4ème génération.
                    </div>
                    <div class="clear"></div>
                    <div><a onclick="closeDiv('idSupplier_17');closeDiv('layerOverlay');" class="retour pointer">Fermer la fenêtre</a></div>
                </div>
            </center>
            {foreach $aPanierSemaine as $panier}
                <div class="panieridf">
                    <div class="cartname" itemprop="name">{$panier.name}<br />{if $panier.reduction}<span class="reduction_cost">En promo !</span>{/if}</div>
                    <div class="cart-withdrawal">
                        Choisissez la date de livraison : 
                        <select name="datewidrawal" id="{$panier.id}">
                            {foreach $panier.listeDate as $key=>$date}
                                <option value="{$key}">{$date}</option>
                            {/foreach}
                        </select>
                    </div>
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
                        <input type="hidden" name="dateWithDrawal-{$panier.id}" value="{$panier.date}" />
                    </div>
                    <div class="prix"><u>Prix du panier :</u> <span class="prixfinal">{if $panier.reduction}<span class="promo">{/if}{$panier.price}€{if $panier.reduction}</span> <span class="reduction_cost">{$panier.reductionCost}€</span> = {$panier.priceReduction}€{/if} TTC</span></div>
                    <div class="command" id="{$panier.id}">Commander</div>
                </div>
            {/foreach}
        {/if}
    </div>
    <div class="clearfix"></div>
</div>