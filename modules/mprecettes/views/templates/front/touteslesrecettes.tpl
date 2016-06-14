<div id="toutes-recettes">
    <div class="formulaire">
        <form action="/toutes-les-recettes" method="post">
            <p><input type="text" name="search" placeholder="Mots-clés" {if !empty($search)}value="{$search}" {/if}/></p>
            <p>
                <input type="radio" value="1" name="typePlat" id="entree"  {if !empty($typePlat) && $typePlat==1}checked {/if}/>&nbsp;<label for="entree">Entrée</label>&nbsp;
                <input type="radio" value="2" name="typePlat" id="plat"  {if !empty($typePlat) && $typePlat==2}checked {/if}/>&nbsp;<label for="plat">Plat</label>&nbsp;
                <input type="radio" value="3" name="typePlat" id="accompagnement"  {if !empty($typePlat) && $typePlat==3}checked {/if}/>&nbsp;<label for="accompagnement">Accompagnement</label>&nbsp;
                <input type="radio" value="4" name="typePlat" id="dessert"  {if !empty($typePlat) && $typePlat==4}checked {/if}/>&nbsp;<label for="dessert">Dessert</label>&nbsp;
            </p>
            <input type="submit" value="Rechercher" />
        </form>
    </div>
    {if !empty($listRecettesSearch)}
        <div class="resultatSearch">
            <h3>Résultat de votre recherche</h3>
            {foreach $listRecettesSearch as $cmsDto}
                <a href="/recette/{$cmsDto->getCMSId()}/{$cmsDto->getLinkRewrite()}">{if $idCategoryChefJesus==$cmsDto->getIdCategory()}<div class="chefjesus">{$cmsDto->getMetaTitle()} <div>par Chef Jésus</div></div>{else}{$cmsDto->getMetaTitle()}{/if}</a>
            {/foreach}
        </div>
    {/if}
    <hr />
    <div class="listRecettes">
        {assign var=typePlat value=''}
        {foreach $listRecettes as $key=>$listRecetteDto}
            {if $typePlat!=$key}
                {if !$listRecetteDto@first}
                    </div>
                {/if}
                <div class="typePlat">
                {$key}
                {assign var=typePlat value=$key}
            {/if}
            <ul>
                {foreach $listRecetteDto as $cmsDto}
                    <li><a href="/recette/{$cmsDto->getCMSId()}/{$cmsDto->getLinkRewrite()}">{if $idCategoryChefJesus==$cmsDto->getIdCategory()}<div class="chefjesus">{$cmsDto->getMetaTitle()} <div>par Chef Jésus</div></div>{else}{$cmsDto->getMetaTitle()}{/if}</a></li>
                {/foreach}
            </ul>
            {if $listRecetteDto@last}
                </div>
            {/if}
        {/foreach}
    </div>
</div>
