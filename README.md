# PetrosLabs — petroslabs.dev

Site personnel de Pierre : un hub central qui accueillera progressivement un
blog, une vitrine de projets et un espace d'administration.

**Direction artistique** : fusion entre l'austérité de la Grèce antique
(pierre, marbre, motif méandre) et un laboratoire de dev moderne (terminal,
lueurs teal/bronze nocturnes). Détails complets dans [`CLAUDE.md`](CLAUDE.md).

## État du projet

- ✅ Landing page (`/`) — hub central avec bannière, logo, bio et grille de liens
- ✅ Projets (`/projects`) — vitrine des projets
- ⬜ Blog (`/blog`)
- ⬜ Espace admin (`Admin\`)

Voir [`CHANGELOG.md`](CHANGELOG.md) pour le détail des évolutions.

## Stack

- Symfony (dernier LTS), PHP 8.4, FrankenPHP en cible d'exécution
- Twig + AssetMapper (pas de bundler npm/Node)
- Tailwind CSS v4 via [`symfonycasts/tailwind-bundle`](https://symfony.com/bundles/TailwindBundle/current/index.html) (binaire standalone)
- [`symfony/ux-twig-component`](https://symfony.com/bundles/ux-twig-component/current/index.html) pour les composants Twig réutilisables

## Prérequis

- PHP 8.4+
- Composer
- Symfony CLI (optionnel, pour `symfony serve`)

## Installation

```bash
composer install
```

## Développement

```bash
# Terminal 1 : recompile le CSS Tailwind à la volée
php bin/console tailwind:build --watch

# Terminal 2 : lance le serveur
symfony serve
# ou : php -S 127.0.0.1:8000 -t public public/index.php
```

Le site est alors accessible sur http://localhost:8000.

## Build de production

```bash
php bin/console tailwind:build --minify
```

## Structure

```
config/hub.yaml              # Contenu de la landing (profil + liens du hub)
config/projects.yaml         # Contenu de la page Projets
src/Controller/               # HomeController, ProjectController (puis Blog/Admin à venir)
templates/
├── base.html.twig            # Layout commun
├── home/                     # Landing page
├── projects/                 # Page Projets
└── components/                # Composants Twig réutilisables (LinkCard, ProjectCard, Meander, SiteFooter…)
assets/styles/app.css         # Thème Tailwind (@theme : palette, polices, animations)
```

## Documentation interne

- [`CLAUDE.md`](CLAUDE.md) — contexte projet, direction artistique et attentes de collaboration
- [`CHANGELOG.md`](CHANGELOG.md) — historique des changements
