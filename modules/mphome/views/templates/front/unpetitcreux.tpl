<div id="vosrepas" class="mpborder">
    <h3>Découvrez notre sélection de produits pour des repas sans préparation</h3>
    <div class="annonce">Avant 11h, toute commande peut être livrée pour midi.<br />
        Après 11h, seul le retrait sur le marché est disponible.</div>
    {if $nbMarket>0}
        {if $listMarketRotisserie|count>0}
            <div class="titre">Les rôtisseurs</div>
            <dl id="marchepartenaire">
            {foreach $listMarketRotisserie as $marketDto}
                <dd class="marche"><a href="/marches-locaux/{$marketDto->getCityRewrite()}/{$marketDto->getPostalCode()}/{$marketDto->getIdMarket()}/{$marketDto->getLinkRewrite()}/jour/{$marketDto->getNextDateOpenWithFormat('jour')}/rotisserie/petit-creux" title="{$marketDto->getCity()} ({$marketDto->getPostalCode()}) - Les Rôtisseurs" style="background-image: url(/img/marche/{$marketDto->getIdMarket()}.jpg); background-repeat: no-repeat;" title="{$marketDto->getName()}"><div class="marketdetails"><span class="marketName">{$marketDto->getName()}</span><br />{$marketDto->getAddress()} {$marketDto->getPostalCode()} {$marketDto->getCity()}<br /><span class="horaire">Ouverture de {$marketDto->getOpenAt()} à {$marketDto->getCloseAt()}.</span></div></a></dd>
            {/foreach}
            </dl>
        {/if}
        {if $listMarketTraiteur|count>0}
            <div class="titre">Les plats préparés</div>
            <dl id="marchepartenaire">
            {foreach $listMarketTraiteur as $marketDto}
                <dd class="marche"><a href="/marches-locaux/{$marketDto->getCityRewrite()}/{$marketDto->getPostalCode()}/{$marketDto->getIdMarket()}/{$marketDto->getLinkRewrite()}/jour/{$marketDto->getNextDateOpenWithFormat('jour')}/traiteur/petit-creux" title="{$marketDto->getCity()} ({$marketDto->getPostalCode()}) - Les Traiteurs" style="background-image: url(/img/marche/{$marketDto->getIdMarket()}.jpg); background-repeat: no-repeat;" title="{$marketDto->getName()}"><div class="marketdetails"><span class="marketName">{$marketDto->getName()}</span><br />{$marketDto->getAddress()} {$marketDto->getPostalCode()} {$marketDto->getCity()}<br /><span class="horaire">Ouverture de {$marketDto->getOpenAt()} à {$marketDto->getCloseAt()}.</span></div></a></dd>
            {/foreach}
            </dl>
        {/if}
        {if $listMarketPatisserie|count>0}
            <div class="titre">Les douceurs</div>
            <dl id="marchepartenaire">
            {foreach $listMarketPatisserie as $marketDto}
                <dd class="marche"><a href="/marches-locaux/{$marketDto->getCityRewrite()}/{$marketDto->getPostalCode()}/{$marketDto->getIdMarket()}/{$marketDto->getLinkRewrite()}/jour/{$marketDto->getNextDateOpenWithFormat('jour')}/patisserie/petit-creux" title="{$marketDto->getCity()} ({$marketDto->getPostalCode()}) - Les Patissiers" style="background-image: url(/img/marche/{$marketDto->getIdMarket()}.jpg); background-repeat: no-repeat;" title="{$marketDto->getName()}"><div class="marketdetails"><span class="marketName">{$marketDto->getName()}</span><br />{$marketDto->getAddress()} {$marketDto->getPostalCode()} {$marketDto->getCity()}<br /><span class="horaire">Ouverture de {$marketDto->getOpenAt()} à {$marketDto->getCloseAt()}.</span></div></a></dd>
            {/foreach}
            </dl>
        {/if}
        {if $listMarketFromager|count>0}
            <div class="titre">Les fromages</div>
            <dl id="marchepartenaire">
            {foreach $listMarketFromager as $marketDto}
                <dd class="marche"><a href="/marches-locaux/{$marketDto->getCityRewrite()}/{$marketDto->getPostalCode()}/{$marketDto->getIdMarket()}/{$marketDto->getLinkRewrite()}/jour/{$marketDto->getNextDateOpenWithFormat('jour')}/fromagerie/petit-creux" title="{$marketDto->getCity()} ({$marketDto->getPostalCode()}) - Les Fromagers" style="background-image: url(/img/marche/{$marketDto->getIdMarket()}.jpg); background-repeat: no-repeat;" title="{$marketDto->getName()}"><div class="marketdetails"><span class="marketName">{$marketDto->getName()}</span><br />{$marketDto->getAddress()} {$marketDto->getPostalCode()} {$marketDto->getCity()}<br /><span class="horaire">Ouverture de {$marketDto->getOpenAt()} à {$marketDto->getCloseAt()}.</span></div></a></dd>
            {/foreach}
            </dl>
        {/if}
        {if $listMarketBoulanger|count>0}
            <div class="titre">Les boulangers</div>
            <dl id="marchepartenaire">
            {foreach $listMarketBoulanger as $marketDto}
                <dd class="marche"><a href="/marches-locaux/{$marketDto->getCityRewrite()}/{$marketDto->getPostalCode()}/{$marketDto->getIdMarket()}/{$marketDto->getLinkRewrite()}/jour/{$marketDto->getNextDateOpenWithFormat('jour')}/boulangerie/petit-creux" title="{$marketDto->getCity()} ({$marketDto->getPostalCode()}) - Les Boulangers" style="background-image: url(/img/marche/{$marketDto->getIdMarket()}.jpg); background-repeat: no-repeat;" title="{$marketDto->getName()}"><div class="marketdetails"><span class="marketName">{$marketDto->getName()}</span><br />{$marketDto->getAddress()} {$marketDto->getPostalCode()} {$marketDto->getCity()}<br /><span class="horaire">Ouverture de {$marketDto->getOpenAt()} à {$marketDto->getCloseAt()}.</span></div></a></dd>
            {/foreach}
            </dl>
        {/if}
        {if $listMarketVinsBio|count>0}
            <div class="titre">Les vins Bio</div>
            <dl id="marchepartenaire">
            {foreach $listMarketVinsBio as $marketDto}
                <dd class="marche"><a href="/marches-locaux/{$marketDto->getCityRewrite()}/{$marketDto->getPostalCode()}/{$marketDto->getIdMarket()}/{$marketDto->getLinkRewrite()}/jour/{$marketDto->getNextDateOpenWithFormat('jour')}/vins-bio/petit-creux" title="{$marketDto->getCity()} ({$marketDto->getPostalCode()}) - Les Vins Bio" style="background-image: url(/img/marche/{$marketDto->getIdMarket()}.jpg); background-repeat: no-repeat;" title="{$marketDto->getName()}"><div class="marketdetails"><span class="marketName">{$marketDto->getName()}</span><br />{$marketDto->getAddress()} {$marketDto->getPostalCode()} {$marketDto->getCity()}<br /><span class="horaire">Ouverture de {$marketDto->getOpenAt()} à {$marketDto->getCloseAt()}.</span></div></a></dd>
            {/foreach}
            </dl>
        {/if}
    {else}
        <div style="text-align:center;font-weight: bold;">
            {if $matin}
                Ce matin, aucun marché ne propose de plat déjà préparé. Revenez après midi pour découvrir les plats de demain.
            {else}
                Demain matin, aucun marché ne propose de plat déjà préparé.
            {/if}
        </div>
    {/if}
</div>