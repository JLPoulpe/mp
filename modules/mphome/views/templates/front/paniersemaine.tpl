<div id="paniersemaine" class="mpborder">
    <h3>Les paniers de vos marchés</h3>
    <div class="annonce">
        Commandez vos paniers des marchés du jour - Nous les livrons l'après midi ou en soirée le jour des marchés.
        <div class="form-ville">
            Votre ville est-elle dans la zone de livraison ?&nbsp;&nbsp;<input type="text" name="cp" placeholder="Votre code postal" />&nbsp;<input type="submit" value="Rechercher" id="searchForCP" />
            <div id="resultFormOk">Votre ville fait partie de notre zone de livraison</div>
            <div id="resultFormKo"><span>Votre ville ne fait pas encore partie de notre zone de livraison.</span><br />Vous habitez trop loin ? Contactez-nous car dans le cadre d'une commande groupée des solutions existent.</div>
            <br /><a href="/informations/cgu#livraison" class="shippingCost">Nos tarifs de livraison</a>
        </div>
    </div>
    <div id="listepanier">
        {foreach $listDays as $panier}
            <div class="panier">
                <span class="nompanier" itemprop="name">Les paniers du {$panier.jour}</span>
                <div class="cart-withdrawal">Prochaine livraison le : <b>{$panier.date}</b></div>
                <div class="clearfix"></div>
                <div class="photo">
                    {if $panier.img}
                        <img src="{$panier.img}" itemprop="image" alt="mespaysans.com - Les paniers du {$panier.jour}"/>
                    {/if}
                </div>
                <div class="box-info-product">
                    <a href="/paniers/jour/{$panier.jour}/"><button class="exclusive" name="Submit" type="submit"><span>Découvrir leurs contenus</span></button></a>
                </div>
            </div>
        {/foreach}
    </div>
    <div class="clearfix"></div>
</div>