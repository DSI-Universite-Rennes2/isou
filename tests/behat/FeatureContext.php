<?php // phpcs:disable Generic.Files.LowercasedFilename.NotFound

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

declare(strict_types=1);

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;

/**
 * Définit des verbes spécifiques pour naviguer sur le site testé.
 *
 * Scripts qui peuvent aider :
 *   - vendor/behat/gherkin/i18n.php
 *   - vendor/friends-of-behat/mink-extension/src/Behat/MinkExtension/Context/MinkContext.php
 *   - vendor/friends-of-behat/mink-extension/i18n/fr.xliff
 */
class FeatureContext extends MinkContext {
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct() {
    }

    /**
     * Alias pour texte "Je suis <page>".
     *
     * @param string $page URL de la page.
     *
     * @When   /^(?:|je )suis sur la page "(?P<page>[^"]+)"$/
     * @return void
     */
    public function jeCliqueSurLaPage(string $page): void {
        $this->visit($page);
    }

    /**
     * Alias pour texte "Je suis <lien>".
     *
     * @param string $link Lien recherché. La valeur peut représenter le libellé du lien, l'attribut id, title ou alt.
     *
     * @When   /^(?:|je )clique sur le lien "(?P<link>(?:[^"]|\\")*)"$/
     * @return void
     */
    public function jeCliqueSurLeLien(string $link): void {
        $this->clickLink($link);
    }
}
