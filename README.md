# PetrosLabs — petroslabs.dev

Site personnel de Pierre : un hub central qui accueillera progressivement un
blog, une vitrine de projets et un espace d'administration.

**Direction artistique** : fusion entre l'austérité de la Grèce antique
(pierre, marbre, motif méandre) et un laboratoire de dev moderne (terminal,
lueurs teal/bronze nocturnes). Détails complets dans [`CLAUDE.md`](CLAUDE.md).

## État du projet

- ✅ Landing page (`/`) — hub central avec bannière, logo, bio et grille de liens
- ✅ Projets (`/projects`) — vitrine des projets
- ✅ L'établi (`/uses`) — outils, stack et matériel au quotidien
- ✅ Internationalisation FR/EN (sans préfixe d'URL, cookie de préférence)
- ✅ SEO de base (robots.txt, sitemap, canonical, og:image, JSON-LD)
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
```

Le site est alors accessible sur http://localhost:8000.

> Préférer `symfony serve` à `php -S ... public/index.php` : le serveur PHP
> intégré a un conflit connu avec le Runtime Symfony sur les fichiers
> statiques du dossier `public/` (ex. `robots.txt`), qui renvoient une 500.
> `symfony serve` (et FrankenPHP en prod) n'ont pas ce problème.

## Build de production

```bash
php bin/console tailwind:build --minify
```

## Structure

```
config/hub.yaml              # Contenu de la landing (profil + liens du hub)
config/projects.yaml         # Contenu de la page Projets
config/uses.yaml             # Contenu de "L'établi"
src/Controller/               # HomeController, ProjectController, UsesController, LocaleController, SitemapController
src/EventListener/            # LocaleSubscriber (langue depuis le cookie)
src/Twig/                     # LocalizedContentExtension (filtre |localized)
translations/                 # messages.fr.yaml / messages.en.yaml (libellés d'interface)
templates/
├── base.html.twig            # Layout commun + switcher de langue + meta SEO
├── home/                     # Landing page
├── projects/                 # Page Projets
├── uses/                     # Page L'établi
├── sitemap/                  # Template XML du sitemap
└── components/                # Composants Twig réutilisables (LinkCard, ProjectCard, Meander, SiteFooter…)
assets/styles/app.css         # Thème Tailwind (@theme : palette, polices, animations)
public/robots.txt             # Autorise l'indexation, référence le sitemap
```

## SEO

- `robots.txt` (statique) + `/sitemap.xml` (généré dynamiquement depuis les
  routes, `SitemapController`).
- URL canonique, `og:image` (1200×630), meta Open Graph et Twitter Card
  complètes sur chaque page.
- JSON-LD `Person` sur la landing.
- **Limite assumée** : sans préfixe d'URL, les moteurs de recherche
  n'indexent que la version FR (par défaut) — l'EN est une option de
  confort pour les visiteurs, pas un contenu séparé référencé.

## Internationalisation

- FR (défaut) / EN, sans préfixe d'URL — la langue est mémorisée dans un
  cookie et se change via le switcher **FR / EN** en haut de chaque page.
- Contenu éditorial bilingue (bio, tagline, résumés…) : champs
  `{ fr: '...', en: '...' }` dans `config/*.yaml`, résolus par le filtre Twig
  `|localized`.
- Libellés d'interface (menus, badges, boutons, footer) : clés de traduction
  dans `translations/messages.{fr,en}.yaml`, via `|trans`.

## Documentation interne

- [`CLAUDE.md`](CLAUDE.md) — contexte projet, direction artistique et attentes de collaboration
- [`CHANGELOG.md`](CHANGELOG.md) — historique des changements
