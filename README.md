# PetrosLabs — petroslabs.dev

Site personnel de Pierre : un hub central qui accueillera progressivement un
blog, une vitrine de projets et un espace d'administration.

**Direction artistique** : fusion entre l'austérité de la Grèce antique
(pierre, marbre, motif méandre) et un laboratoire de dev moderne (terminal,
lueurs teal/bronze nocturnes). Détails complets dans [`CLAUDE.md`](CLAUDE.md).

## État du projet

- ✅ Landing page (`/`) — hub central avec logo, bio et grille de liens
- ✅ Projets (`/projects`) — vitrine des projets
- ✅ L'établi (`/uses`) — outils, stack et matériel au quotidien
- ✅ Internationalisation FR/EN (sans préfixe d'URL, cookie de préférence)
- ✅ SEO de base (robots.txt, sitemap, canonical, og:image, JSON-LD)
- ✅ Blog (`/blog`) — articles en base, éditables depuis l'admin
- ✅ Espace admin (`/admin`) — auth + CRUD Projets, Hub, Établi, Blog

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
make install
```

## Développement

Un `Makefile` centralise les commandes courantes — `make help` liste tout.

```bash
# Terminal 1 : recompile le CSS Tailwind à la volée
make tailwind-watch

# Terminal 2 : lance le serveur
make serve
```

Le site est alors accessible sur http://localhost:8000.

> Préférer `symfony serve` (`make serve`) à `php -S ... public/index.php` :
> le serveur PHP intégré a un conflit connu avec le Runtime Symfony sur les
> fichiers statiques du dossier `public/` (ex. `robots.txt`), qui renvoient
> une 500. `symfony serve` (et FrankenPHP en prod) n'ont pas ce problème.

Autres commandes utiles : `make lint` (Twig + YAML), `make test` (PHPUnit),
`make cache-clear`.

## Développement via Docker (infra `symfony_env`)

Le projet peut aussi tourner dans l'infra Docker partagée
[`symfony_env`](../symfony_env) (Traefik + PostgreSQL + Redis + Mailpit),
utilisée par plusieurs projets Symfony.

```bash
# 1. Démarrer symfony_env si ce n'est pas déjà fait (depuis son propre dossier)
cd ../symfony_env && make up

# 2. Créer la base dédiée à ce projet (une fois)
make db-create

# 3. Créer .env.local (non versionné) avec les DSN partagés
#    DATABASE_URL="postgresql://<POSTGRES_USER>:<POSTGRES_PASSWORD>@symfony_env_postgresql:5432/petroslabs?serverVersion=17&charset=utf8"
#    REDIS_URL="redis://:<REDIS_PASSWORD>@symfony_env_redis:6379"
#    MAILER_DSN="smtp://symfony_env_mailpit:1025"

# 4. Démarrer le conteneur de l'app (construit l'image au besoin)
make build
make up
```

Le site est alors accessible sur https://petroslabs.localhost.

> ⚠️ Le `docker-proxy` de `symfony_env` n'a pas la permission `EVENTS` : Traefik
> ne détecte pas à chaud la création/recréation du conteneur `petroslabs_app`.
> Après un `make build`/`up`, relancer Traefik une fois : `make traefik-restart`.

Le `Dockerfile` ajoute `pdo_pgsql` à l'image `dunglas/frankenphp:php8.4` (absent
par défaut), nécessaire pour la connexion PostgreSQL de `doctrine/doctrine-bundle`.

`trusted_proxies` (`config/packages/framework.yaml`) fait confiance à Traefik
comme proxy immédiat — sans ça, Symfony croit être servi en HTTP (le
conteneur ne voit que du trafic HTTP en interne) et génère des URLs
canoniques/SEO en `http://` au lieu de `https://`, en plus de casser la
validation CSRF des formulaires.

Autres commandes : `make ps` / `make logs` / `make sh` (shell dans le
conteneur) / `make console cmd="..."` (bin/console dans le conteneur) /
`make db-drop`.

## Build de production

```bash
make tailwind-build
```

## Structure

```
Makefile                       # Commandes de dev/build/Docker (make help)
Dockerfile                    # Image dev pour l'infra symfony_env (frankenphp + pdo_pgsql)
compose.yaml                  # Service app + intégration Traefik (réseau symfony_env)
migrations/                   # Migrations Doctrine (schéma + données reprises des YAML/Markdown supprimés)
src/Blog/                     # BlogPost (DTO résolu), BlogPostRepository (résout langue + Markdown→HTML, lit la base)
src/Command/                   # CreateAdminCommand (app:create-admin)
src/Entity/                    # User, Project, Profile, HubLink, UseCategory, UseItem, BlogPost (Doctrine ORM)
src/Repository/                # UserRepository, ProjectRepository, ProfileRepository, HubLinkRepository, UseCategoryRepository, UseItemRepository, BlogPostRepository
src/Form/                      # ProjectType, ProfileType, HubLinkType, UseCategoryType, UseItemType, BlogPostType
src/Controller/               # HomeController, ProjectController, UsesController, BlogController, LocaleController, SitemapController
src/Controller/Admin/          # DashboardController, SecurityController (login/logout), ProjectController, ProfileController, HubLinkController, UseCategoryController, UseItemController, BlogPostController (CRUD)
src/EventListener/            # LocaleSubscriber (langue depuis le cookie)
src/Twig/                     # LocalizedContentExtension (filtre |localized)
translations/                 # messages.fr.yaml / messages.en.yaml (libellés d'interface)
templates/
├── base.html.twig            # Layout commun + switcher de langue + meta SEO
├── home/                     # Landing page
├── projects/                 # Page Projets
├── uses/                     # Page L'établi
├── blog/                     # Liste des articles + page article
├── admin/                    # Espace admin (layout, login, tableau de bord, CRUD Projets/Hub/Établi/Blog)
├── sitemap/                  # Template XML du sitemap
└── components/                # Composants Twig réutilisables (LinkCard, ProjectCard, PostCard, Meander, SiteFooter…)
assets/styles/app.css         # Thème Tailwind (@theme : palette, polices, animations) + typographie .prose-blog
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

## Blog

- Articles en base de données (`App\Entity\BlogPost`), éditables depuis
  `/admin/blog` — voir section Admin. Contenu (titre, résumé, corps) stocké
  en Markdown brut par langue (`contentFr`/`contentEn`), converti en HTML à
  l'affichage via `league/commonmark`.
- Si la traduction d'une langue manque pour un article, la version FR sert
  de repli (même logique que le filtre `|localized`).
- `App\Blog\BlogPostRepository` (namespace distinct de `App\Repository\`)
  résout l'article pour la langue courante et fait la conversion Markdown →
  HTML ; c'est cette façade que lisent `BlogController` et
  `SitemapController`.

## Admin

Espace `/admin`, protégé par authentification — couvre l'ensemble du
contenu du site : Projets, Hub, Établi et Blog.

- Un seul compte admin, pas d'inscription publique. Le créer (ou changer son
  mot de passe) :
  ```bash
  make console cmd="app:create-admin"
  ```
- Connexion : `/admin/login` (formulaire), déconnexion via le lien dans
  l'en-tête admin.
- Tableau de bord : `/admin` — une carte par section de contenu (Projets,
  Hub, Établi et Blog). Le login y redirige.
- Projets : `/admin/projects` (liste, création, édition, suppression) —
  formulaires Symfony faits main (pas d'EasyAdmin), gabarit sobre et
  utilitaire (`templates/admin/`), sans l'habillage antique du site public.
  Contenu en base (`App\Entity\Project`) — la page publique `/projects` lit
  `ProjectRepository`.
- Hub : `/admin/profile` (profil — singleton, édition seule) et
  `/admin/hub-links` (liens du hub — liste, création, édition, suppression).
  Contenu en base (`App\Entity\Profile`, `App\Entity\HubLink`) — la landing
  publique (`/`) lit `ProfileRepository`/`HubLinkRepository`.
- L'établi : `/admin/uses` (catégories — liste, création, édition,
  suppression) et items (rattachés à une catégorie, même CRUD). Supprimer
  une catégorie supprime ses items. Contenu en base (`App\Entity\UseCategory`,
  `App\Entity\UseItem`) — la page publique `/uses` lit
  `UseCategoryRepository`.
- Blog : `/admin/blog` (liste, création, édition, suppression). Contenu
  Markdown par langue stocké en base (`App\Entity\BlogPost`) — voir section
  Blog pour le détail du rendu.

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

## Licence

[MIT](LICENSE)
