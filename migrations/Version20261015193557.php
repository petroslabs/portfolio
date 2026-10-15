<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261015193557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Ajoute le projet FR Validator (fr-validator + fr-validator-symfony)";
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO project (slug, name, summary_fr, summary_en, content_fr, content_en, image, stack, status, pinned, repo_url, demo_url, position)
            VALUES (
                'fr-validator',
                'FR Validator',
                'Validateurs PHP pour les identifiants français (NIR, SIRET, IBAN, TVA...) — algorithmes vérifiés contre des sources primaires, testés par mutation testing. Package agnostique + pont Symfony Validator.',
                'PHP validators for French identifiers (NIR, SIRET, IBAN, VAT...) — algorithms verified against primary sources, mutation-tested. Framework-agnostic package + Symfony Validator bridge.',
                'Deux packages Composer publiés séparément : `petroslabs/fr-validator`, sans dépendance à un framework, et `petroslabs/fr-validator-symfony`, qui l''expose comme contraintes du composant Validator de Symfony.

            Valide NIR (numéro de sécurité sociale), clé RIB, SIRET/SIREN, IBAN/BIC, numéro de TVA, numéro de téléphone et code NAF/APE — et dit *pourquoi* une valeur est invalide (checksum erroné, code département inconnu...) plutôt qu''un simple booléen.

            Chaque algorithme est vérifié contre une source primaire plutôt que recopié d''un article de blog — plusieurs « exemples » de NIR largement diffusés en ligne ne respectent en réalité pas la formule de clé officielle. Les tests sont vérifiés par mutation testing (Infection) en plus de la couverture de lignes : la CI mute délibérément le code et échoue si un test laisse encore passer le comportement cassé.

            **Liens**
            - [petroslabs/fr-validator](https://github.com/petroslabs/fr-validator) — [Packagist](https://packagist.org/packages/petroslabs/fr-validator)
            - [petroslabs/fr-validator-symfony](https://github.com/petroslabs/fr-validator-symfony) — [Packagist](https://packagist.org/packages/petroslabs/fr-validator-symfony)',
                'Two separately published Composer packages: `petroslabs/fr-validator`, with zero framework dependency, and `petroslabs/fr-validator-symfony`, which exposes it as Symfony Validator constraints.

            Validates NIR (social security number), RIB key, SIRET/SIREN, IBAN/BIC, VAT number, phone number and NAF/APE activity code — and says *why* a value is invalid (bad checksum, unknown department code...) instead of a plain boolean.

            Each algorithm is verified against a primary source rather than copy-pasted from a blog post — several widely circulated "example" NIR numbers online don''t actually satisfy the official checksum formula. Tests are checked with mutation testing (Infection) on top of line coverage: CI deliberately mutates the code and fails the build if any test still lets the broken behavior through.

            **Links**
            - [petroslabs/fr-validator](https://github.com/petroslabs/fr-validator) — [Packagist](https://packagist.org/packages/petroslabs/fr-validator)
            - [petroslabs/fr-validator-symfony](https://github.com/petroslabs/fr-validator-symfony) — [Packagist](https://packagist.org/packages/petroslabs/fr-validator-symfony)',
                'images/projects/fr-validator.webp',
                '["PHP", "PHPStan", "Infection", "Symfony"]',
                'in_progress',
                false,
                'https://github.com/petroslabs/fr-validator',
                NULL,
                7
            )
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM project WHERE slug = 'fr-validator'");
    }
}
