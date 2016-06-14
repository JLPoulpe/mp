<div id="recette">
    <h3>{if $idCategoryChefJesus==$cmsDto->getIdCategory()}{$cmsDto->getMetaTitle()} par Chef Jésus{else}{$cmsDto->getMetaTitle()}{/if}</h3>
    <div class="content">
        {$cmsDto->getContent()}
    </div>
    <a href="/toutes-les-recettes" class="retour">&nbsp;Retour à toutes les recettes</a>
</div>
