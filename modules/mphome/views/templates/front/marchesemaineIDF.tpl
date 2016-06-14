<div id="marchesemaine" class="mpborder">
    <h3>Vos marchés de la semaine</h3>
    <div class="annonce">
        Commandez vos produits sur les marchés du jour - Nous les livrons l'après midi ou en soirée le jour des marchés.
        <div class="form-ville">
            Votre ville est-elle dans la zone de livraison ?&nbsp;&nbsp;<input type="text" name="cp" placeholder="Votre code postal" />&nbsp;<input type="submit" value="Rechercher" id="searchForCP" /><input type="hidden" name="dep" value="{$loc}" />
            <div id="resultFormOk">Votre ville fait partie de notre zone de livraison</div>
            <div id="resultFormKo"><span>Votre ville ne fait pas encore partie de notre zone de livraison.</span><br />Vous habitez trop loin ? Contactez-nous car dans le cadre d'une commande groupée des solutions existent.</div>
            <br /><a href="/informations/cgu#livraison" class="shippingCost">Nos tarifs de livraison</a>
        </div>
    </div>
    <h3>Quel jour souhaitez-vous être livré ?</h3>
    <div>
        {foreach $listJour as $jour}
            <div class="joursemaine">
                <div class="jour">
                    {foreach $jour.marketDto as $marketDto}
                        <a href="/marches-locaux/{$marketDto->getCityRewrite()}/{$marketDto->getPostalCode()}/{$marketDto->getIdMarket()}/{$marketDto->getLinkRewrite()}/jour/{$jour.jour}" class="marcheidf">
                            <div class="date">{$jour.displayDate}</div>
                        </a>
                    {/foreach}
                    <div class="clearfix"></div>
                </div>
            </div>
        {/foreach}
    </div>
    <div class="clearfix"></div>
</div>