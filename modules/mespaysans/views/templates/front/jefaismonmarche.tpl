<div id="listCategory">
    {if empty($listCategoryByStringDateTime)}
        Aucun produit trouvé
    {else}
        {foreach $listCategoryByStringDateTime as $stringDate=>$listPosition}
            <div class="colonneJour">
                <div class="date">{$stringDate}</div>
                <div class="listCategory">
                    {foreach $listPosition.list as $listCategoryDto}
                        {foreach $listCategoryDto as $categoryDto}
                            <div class="category"><a href="/les-produits-du-{$listPosition.jour}/categorie/{$categoryDto->getIdCategory()}/{$categoryDto->getLinkRewrite()}/"><img src="/themes/mespaysansV2/img/categorie/{$categoryDto->getIdCategory()}.jpg" alt="{$categoryDto->getName()}" /></a></div>
                        {/foreach}
                    {/foreach}
                    <div class="category"><a href="/les-paniers-du-{$listPosition.jour}/"><img src="/themes/mespaysansV2/img/categorie/37.jpg" alt="Les paniers composés" /></a></div>
                </div>
            </div>
        {/foreach}
    {/if}
</div>