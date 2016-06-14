<div id="cms">
    <div class="image">
        <img src="/themes/mespaysansV2/img/mpc/chef_jesus.jpg" alt="Chef Jésus" title="Chef Jésus" />
    </div>
    <div class="image">
        {$content}
    </div>
    <div class="listRecettes">
        <div class="title">Les recettes de Chef Jésus :</div>
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
                    <li><a href="/recette/{$cmsDto->getCMSId()}/{$cmsDto->getLinkRewrite()}">{$cmsDto->getMetaTitle()}</a></li>
                {/foreach}
            </ul>
            {if $listRecetteDto@last}
                </div>
            {/if}
        {/foreach}
    </div>
    <div class="image">
        <img src="/themes/mespaysansV2/img/cms/jesus/chefjesus_3.jpg" alt="Chef Jésus" title="Chef Jésus" />
        <img src="/themes/mespaysansV2/img/cms/jesus/chefjesus_1.jpg" alt="Chef Jésus" title="Chef Jésus" />
        <img src="/themes/mespaysansV2/img/cms/jesus/chefjesus_4.jpg" alt="Chef Jésus" title="Chef Jésus" />
    </div>
</div>
