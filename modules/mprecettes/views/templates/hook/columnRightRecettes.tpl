<div id="recettes">
    <div class="title">
        <h3>Les dernières recettes</h3>
    </div>
    <div class="content">
        <ul>
        {foreach $listRecettes as $recetteDto}
            <li><a href="/recette/{$recetteDto->getCMSId()}/{$recetteDto->getLinkRewrite()}">{$recetteDto->getMetaTitle()}</a></li>
        {/foreach}
        </ul>
        <a href="/toutes-les-recettes" class="allrecipes">Toutes les recettes</a>
    </div>
</div>
