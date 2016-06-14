<div id="formulaire" class="container mpborder">
    <div><div class="floatleft"><img src="/themes/mespaysans/img/paysans_en_paysans.jpg" title="Rejoindre mespaysans.com" /></div><div class="floatleft title">Vous souhaitez rejoindre mespaysans.com</div><div class="clearfix"></div></div>
    {if empty($ok)}
        <p>
            Rejoindre <span class="mespaysanscom">mespaysans.com</span> c'est :
            <ul>
                <li>L'assurance d'une meilleure visibilité : <span class="mespaysanscom">mespaysans.com</span> met à votre service toute son expertise pour vous assurer la visite d'internaute sur les marchés où vous êtes présent. La préparation d'une fiche informative, décrivant vous et votre activité, permet aux internautes de mieux vous connaître</li>
                <li>Une augmentation de chiffre d'affaire... sans rien faire : une fois référencé sur le site, vous ne changez pas vos habitudes. Avec <span class="mespaysanscom">mespaysans.com</span>, nous venons récupérer les produits sur le marché pour les livrer aux clients. Garder vos habitudes, nous nous chargeons du reste.</li>
                <li>Une nouvelle clientèle à portée : <span class="mespaysanscom">mespaysans.com</span> augmente l'accès aux marchés grâce à Internet. Toute la population habituée à commander leurs produits alimentaires sur Internet et jamais sur les marchés seront maintenant autant de client potentiel pour votre structure.</li>
            </ul>
        </p>
        <form action="/paysans/nousrejoindre" method="post">
            {if isset($err.nom)}
                {if $err.nom=='invalide'}
                    <p class="error">Nom invalide</p>
                {else}
                    <p class="error">Vous devez saisir un nom</p>
                {/if}
            {/if}
            <p><label for="nom">Nom :</label>&nbsp;<input type="text" name="nom" value="{if isset($nom)}{$nom}{/if}" /></p>
            {if isset($err.prenom)}
                {if $err.prenom=='invalide'}
                    <p class="error">Prénom invalide</p>
                {else}
                    <p class="error">Vous devez saisir un prénom</p>
                {/if}
            {/if}
            <p><label for="prenom">Prénom :</label>&nbsp;<input type="text" name="prenom" value="{if isset($prenom)}{$prenom}{/if}" /></p>
            {if isset($err.tel)}
                {if $err.tel=='invalide'}
                    <p class="error">Numéro de téléphone invalide</p>
                {else}
                    <p class="error">Vous devez saisir un numéro de téléphone</p>
                {/if}
            {/if}
            <p><label for="tel">Téléphone :</label>&nbsp;<input type="text" name="tel" value="{if isset($tel)}{$tel}{/if}" /></p>
            {if isset($err.email)}
                {if $err.email=='invalide'}
                    <p class="error">Email invalide</p>
                {else}
                    <p class="error">Vous devez saisir un email</p>
                {/if}
            {/if}
            <p><label for="email">Adresse email :</label>&nbsp;<input type="email" name="email" value="{if isset($email)}{$email}{/if}" /></p>
            <p class="submit"><input type="submit" value="Valider" /></p>
            <input type="hidden" name="form" value="sent" />
        </form>
    {else}
        <div class="ok">
            Votre enregistrement a bien été effectué.
            <br />Nous vous contacterons très prochainement.
        </div>
    {/if}
</div>