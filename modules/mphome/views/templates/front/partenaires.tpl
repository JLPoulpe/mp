<div class="mpborder">
    <h3>Vos vins Bio Avec Full Nature</h3>
    <div class="annonce">Retrouvez Full Nature et ses vins Bio sur les marchés suivants <img src="/themes/mespaysans/img/logo_fullnature.png" /></div>
    <dl id="marchepartenaire">
    {foreach $listMarketDto as $market}
        {assign var=dateMarket value=''}
        {foreach $market as $marketDto}
            {if $dateMarket!=$marketDto->getNextDateOpenWithFormat('Ymd')}
                {assign var=dateMarket value=$marketDto->getNextDateOpenWithFormat('Ymd')}
                <div class="dateMarket">Le {$marketDto->getNextDateOpenWithFormat('stringDay')}</div>
            {/if}
            <dd class="marche"><a href="/marches-locaux/{$marketDto->getCityRewrite()}/{$marketDto->getPostalCode()}/{$marketDto->getIdMarket()}/{$marketDto->getLinkRewrite()}/jour/{$marketDto->getNextDateOpenWithFormat('jour')}/vins-bio" title="{$marketDto->getCity()} ({$marketDto->getPostalCode()})" style="background-image: url(/img/marche/{$marketDto->getIdMarket()}.jpg); background-repeat: no-repeat;" title="{$marketDto->getName()}"><div class="marketdetails"><span class="marketName">{$marketDto->getName()}</span><br />{$marketDto->getAddress()} {$marketDto->getPostalCode()} {$marketDto->getCity()}<br />Ouverture de {$marketDto->getOpenAt()} à {$marketDto->getCloseAt()}.</div></a></dd>
        {/foreach}
    {/foreach}
    </dl>
</div>