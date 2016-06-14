<div id="espacepro" class="container mpborder">
    <div>
        {include file="$menu"}
    </div>
    <div id="banque">
        <h2>Mes coordonnées banquaire</h2>
        <form action="/index.php?controller=espace-pro&mod=validebanque" method="post">
            {if isset($valide)}
                <p>Changement enregistré</p>
            {/if}
            <p>
                {if isset($err.codeEtablissement)}
                    <p><span class="error">Le code établissement est obligatoire</span></p>
                {/if}
                <p><label for="codeEtablissement">Code établissement&nbsp;:&nbsp;</label><input type="text" name="codeEtablissement" value="{$supplierAccount->getCodeEtablissement()}" /></p>
                {if isset($err.codeGuichet)}
                    <p><span class="error">Le code guichet est obligatoire</span></p>
                {/if}
                <p><label for="codeGuichet">Code guichet&nbsp;:&nbsp;</label><input type="text" name="codeGuichet" value="{$supplierAccount->getCodeGuichet()}" /></p>
                {if isset($err.numeroCompte)}
                    <p><span class="error">Le numéro de compte est obligatoire</span></p>
                {/if}
                <p><label for="numeroCompte">Numéro de compte&nbsp;:&nbsp;</label><input type="text" name="numeroCompte" value="{$supplierAccount->getNumeroCompte()}" /></p>
                {if isset($err.clefRib)}
                    <p><span class="error">La clef RIB est obligatoire</span></p>
                {/if}
                <p><label for="clefRib">Clef RIB&nbsp;:&nbsp;</label><input type="text" name="clefRib" value="{$supplierAccount->getClefRib()}" /></p>
                {if isset($err.iban)}
                    <p><span class="error">Le code IBAN est obligatoire</span></p>
                {/if}
                <p><label for="IBAN">IBAN&nbsp;:&nbsp;</label><input type="text" name="iban" value="{$supplierAccount->getIban()}" /></p>
                {if isset($err.codeBic)}
                    <p><span class="error">Le code BIC est obligatoire</span></p>
                {/if}
                <p><label for="codeBic">Code BIC&nbsp;:&nbsp;</label><input type="text" name="codeBic" value="{$supplierAccount->getCodebic()}" /></p>
            <p><input type="submit" value="Enregistrer" /></p>
        </form>
    </div>
</div>