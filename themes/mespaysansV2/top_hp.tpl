<div>
    <div class="header_hp">
        <div id="logo"><a href="/"><img src="/themes/mespaysansV2/img/mpc/logo_transp.png" /></a></div>
        <div id="logo_ms"><a href="/"><img src="/themes/mespaysansV2/img/mpc/logo_transp_slogan.png" /></a></div>
        <div id="slogan"><img src="/themes/mespaysansV2/img/mpc/slogan.png" /></div>
        <div id="mpc">
            <img width="15" src="/themes/mespaysansV2/img/mpc/tel.gif">&nbsp;<a href="tel:+33695113479">06.95.11.34.79</a><br />
            {if $isLogged}
                <a href="/mon-compte" title="Accéder au compte">{$firstname} {$lastname} : vous êtes connecté(e)</a>
            {else}
                <a href="/connexion" title="Se connecter / S'inscrire">se connecter / s'incrire</a>
            {/if}
        </div>
    </div>
</div>