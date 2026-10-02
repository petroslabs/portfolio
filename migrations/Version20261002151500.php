<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002151500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Étoffe le contenu détail de PetrosLabs, Loto Quine et SymAgri';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            UPDATE project SET
                content_fr = 'Ce site : un hub Symfony qui réunit blog, projets et réseaux sous une direction artistique entre Grèce antique et laboratoire de dev moderne — pierre et marbre spartiates, lueurs teal/bronze de terminal.

            Tout le contenu éditorial (profil, liens du hub, projets, Établi, articles de blog) est géré depuis un espace admin fait main, sans builder low-code — formulaires Symfony, CRUD complet, éditeur Markdown WYSIWYG pour le blog. L''internationalisation FR/EN repose sur un cookie plutôt qu''un préfixe d''URL, avec un filtre Twig dédié pour résoudre le contenu bilingue.

            Le projet embarque son propre PostgreSQL et ne dépend plus que d''un reverse proxy partagé ([Infra](/projects/infra)) pour cohabiter avec d''autres projets sur le même VPS — chaque dépôt reste déployable seul.',
                content_en = 'This site: a Symfony hub bringing together a blog, projects and social links, under an art direction between ancient Greece and a modern dev lab — Spartan stone and marble, teal/bronze terminal glow.

            All editorial content (profile, hub links, projects, workshop, blog posts) is managed from a hand-built admin space, no low-code builder — plain Symfony forms, full CRUD, a Markdown WYSIWYG editor for the blog. FR/EN localization relies on a cookie rather than a URL prefix, with a dedicated Twig filter resolving bilingual content.

            The project embeds its own PostgreSQL and only depends on a shared reverse proxy ([Infra](/projects/infra)) to cohabit with other projects on the same VPS — each repository stays deployable on its own.'
            WHERE slug = 'petroslabs'
            SQL);

        $this->addSql(<<<'SQL'
            UPDATE project SET
                content_fr = 'Un tableau d''affichage pensé pour animer un loto quine (jeu de bingo traditionnel) en soirée : grille complète de 1 à 90, numéro tiré affiché en grand, historique des manches précédentes visible en un coup d''œil pour les joueurs qui arrivent en retard sur une partie.

            Une seule page HTML/CSS/JavaScript, sans dépendance ni serveur — elle tourne en local, dans un simple navigateur, projetée sur un écran ou un vidéoprojecteur. Pas de connexion internet requise une fois la page chargée, ce qui compte en salle des fêtes où le réseau n''est pas toujours fiable.

            Code source public, pensé pour être repris ou adapté par qui veut animer sa propre soirée loto.',
                content_en = 'A display board built to host a loto quine (a traditional French bingo-style game): a full 1-to-90 grid, the last number drawn shown large, previous rounds'' history visible at a glance for players who arrive mid-session.

            A single HTML/CSS/JavaScript page, no dependencies or server — it runs locally in a plain browser, projected on a screen or a video projector. No internet connection needed once the page has loaded, which matters in a village hall where the network isn''t always reliable.

            Public source code, meant to be picked up or adapted by anyone hosting their own loto night.'
            WHERE slug = 'loto-quine'
            SQL);

        $this->addSql(<<<'SQL'
            UPDATE project SET
                content_fr = 'SymAgri répond à un problème concret des entreprises de travaux agricoles (ETA) : la facturation prend du retard parce que la saisie du travail effectué au champ remonte mal, ou trop tard, jusqu''au bureau.

            Les chauffeurs saisissent leurs interventions directement sur le terrain, depuis une application mobile qui fonctionne même sans réseau (PWA offline-first, synchronisation automatique dès que la connexion revient). Ces saisies remontent au back-office et alimentent directement la facturation — barèmes et TVA agricole appliqués automatiquement, factures générées au format conforme à la réglementation à venir (Factur-X).

            Projet encore en développement, fermé (pas de dépôt public) : il est destiné à la vente, édité sous la marque PetrosLabs.',
                content_en = 'SymAgri addresses a concrete problem for agricultural contracting businesses: invoicing falls behind because field work entries reach the office poorly, or too late.

            Drivers log their work directly on-site, from a mobile app that works even offline (offline-first PWA, automatic sync once connectivity returns). These entries flow to the back-office and feed invoicing directly — trade-specific rates and agricultural VAT applied automatically, invoices generated in a format compliant with upcoming regulation (Factur-X).

            Still in development, closed-source (no public repo): built for commercial sale, published under the PetrosLabs brand.'
            WHERE slug = 'symagri'
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            UPDATE project SET content_fr = summary_fr, content_en = summary_en
            WHERE slug IN ('petroslabs', 'loto-quine', 'symagri')
            SQL);
    }
}
