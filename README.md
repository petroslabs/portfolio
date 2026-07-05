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

Le site est alors accessible sur https://petroslabs.localhost (domaine par
défaut si `APP_DOMAIN` n'est pas défini — inutile de créer un `.env.docker`
en local, seule la production a besoin d'un vrai domaine).

> ⚠️ Le `docker-proxy` de `symfony_env` n'a pas la permission `EVENTS` : Traefik
> ne détecte pas à chaud la création/recréation du conteneur `petroslabs_app`.
> Après un `make build`/`up`, relancer Traefik une fois : `make traefik-restart`.

Le `Dockerfile` est multi-stage : le stage `frankenphp_dev` (utilisé ici via
`compose.override.yaml`, auto-chargé en local) ajoute `pdo_pgsql`/`opcache`
et `composer` à l'image `dunglas/frankenphp:php8.4`, le code venant du
bind-mount de l'hôte pour le hot-reload. Le stage `frankenphp_prod` est
décrit dans la section Production ci-dessous.

`trusted_proxies` (`config/packages/framework.yaml`) fait confiance à Traefik
comme proxy immédiat — sans ça, Symfony croit être servi en HTTP (le
conteneur ne voit que du trafic HTTP en interne) et génère des URLs
canoniques/SEO en `http://` au lieu de `https://`, en plus de casser la
validation CSRF des formulaires.

Autres commandes : `make ps` / `make logs` / `make sh` (shell dans le
conteneur) / `make console cmd="..."` (bin/console dans le conteneur) /
`make db-drop`.

## Déploiement en production (VPS, infra `symfony_env`)

Le stage `frankenphp_prod` du `Dockerfile` intègre le code, les dépendances
Composer (`--no-dev`) et les assets compilés (Tailwind minifié +
`asset-map:compile`) directement dans l'image au build — pas de bind-mount,
exécution en utilisateur non-root (`www-data`). `compose.yaml` seul (sans
`compose.override.yaml`, qui bascule sur le stage dev) est donc déjà
prod-safe par défaut ; `compose.prod.yaml` ajoute les limites de ressources
et injecte `.env.local` (non versionné, présent sur le VPS mais jamais copié
dans l'image) comme variables d'environnement du conteneur.

### Prérequis

- `symfony_env` déjà déployé **en mode production** sur le VPS (domaine
  réel, Let's Encrypt, réseau Docker `symfony_env` créé) — voir son propre
  README, section Production. Ce projet ne fait que rejoindre ce réseau, il
  ne démarre pas l'infra partagée.
- Un sous-domaine DNS (ex. `petroslabs.dev`) qui pointe vers l'IP du VPS.

### Premier déploiement

```bash
# Sur le VPS
git clone <url-du-repo> portfolio
cd portfolio

# 1. Créer la base dédiée (une fois, depuis symfony_env)
cd ../symfony_env && make db-create name=petroslabs
cd ../portfolio

# 2. .env.local (non versionné) avec les vraies valeurs
#    DATABASE_URL="postgresql://<user>:<mdp affiché par db-create>@symfony_env_postgresql:5432/petroslabs?serverVersion=17&charset=utf8"
#    REDIS_URL="redis://:<REDIS_PASSWORD>@symfony_env_redis:6379"
#    MAILER_DSN="<vrai SMTP>"
#    APP_SECRET=<valeur forte, ex: openssl rand -hex 16>

# 3. .env.docker (non versionné, copié depuis .env.docker.example) avec le vrai domaine
cp .env.docker.example .env.docker
#    APP_DOMAIN=petroslabs.dev

# 4. Build → migrations → démarrage
make deploy-prod

# 5. Premier démarrage : Traefik ne détecte pas le nouveau conteneur à chaud
#    (limitation docker-proxy de symfony_env, cf. plus bas)
make traefik-restart

# 6. Créer le compte admin
make console cmd="app:create-admin"
```

Le site est alors accessible sur `https://<APP_DOMAIN>`.

### Déploiements suivants

```bash
git pull
make deploy-prod   # build (nouvelle image) → migrations → redémarrage du conteneur
```

`make traefik-restart` n'est nécessaire qu'à la création/recréation du
conteneur (rare en usage normal, `deploy-prod` recrée le conteneur à chaque
fois via `up-prod` — le lancer par précaution si le site ne répond pas juste
après un déploiement).

### Autres commandes

`make build-prod` / `make up-prod` / `make down-prod` / `make logs-prod` /
`make migrate-prod` (déploiement étape par étape plutôt que
`make deploy-prod`).

## Structure

```
Makefile                       # Commandes de dev/build/Docker/prod (make help)
Dockerfile                    # Image multi-stage (frankenphp_prod / frankenphp_dev)
compose.yaml                  # Service app (prod par défaut) + intégration Traefik
compose.override.yaml         # Overrides dev (auto-chargés) : stage dev + bind-mount du code
compose.prod.yaml             # Overrides production : limites de ressources + injection .env.local
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
- Édition via un éditeur WYSIWYG ([Toast UI Editor](https://ui.toast.com/tui-editor)),
  ajouté via AssetMapper (`assets/controllers/blog_editor_controller.js`,
  Stimulus). Il édite visuellement mais synchronise en continu le Markdown
  généré vers le champ réel du formulaire — le stockage reste inchangé.

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
