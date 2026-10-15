# PetrosLabs — petroslabs.dev

Site perso de Pierre. Symfony (dernier LTS), pensé comme un hub qui accueillera
progressivement un blog et des projets.

## État actuel

La **landing page (`/`)**, la **page Projets (`/projects`)**, **L'établi
(`/uses`)** et le **blog (`/blog`)** sont implémentés, avec
**internationalisation FR/EN**. Tout le contenu (Hub, Projets, Établi, Blog)
vit en base de données (Doctrine) — pas d'espace d'administration : le
contenu se gère via migrations Doctrine ou commandes `bin/console` dédiées,
à écrire au cas par cas.

## Direction artistique — à respecter impérativement

Concept : **PetrosLabs, le laboratoire de Pierre** — fusion sincère entre
**Grèce antique / austérité spartiate** (pierre, marbre, colonnes, motif
méandre/grecque) et **laboratoire de dev moderne** (terminal, néons discrets,
lueur nocturne teal/bronze).

Règles de vocabulaire et de ton :
- **Jamais de métaphore "forge/forgeron/marteau"** — Pierre n'aime pas ce
  registre. Préférer le vocabulaire "labs / laboratoire / atelier" (neutre).
- Pas de réseau social **X** — volontairement absent, ne pas le réintroduire.
- Rester sobre : 3 détails d'âme bien exécutés valent mieux que 10 effets
  tape-à-l'œil. Pas de kitsch.

Palette (définie en `@theme` dans `assets/styles/app.css`, noms sémantiques) :
- `charcoal` / `charcoal-800` / `charcoal-700` — fond pierre volcanique sombre
- `bronze` / `bronze-bright` — accent chaud (or/bronze)
- `teal` / `teal-deep` — accent froid (lueur de labo)
- `parchment` / `parchment-dim` — texte blanc cassé chaud

Polices (`@theme` → `fontFamily`) :
- `font-display` → Cinzel (titres, inspiration antique/romaine)
- `font-sans` → Inter (corps de texte)
- `font-mono` → JetBrains Mono (tagline, labels "terminal")

## Stack imposée

- Symfony (dernier LTS), PHP 8.4, cible d'exécution FrankenPHP
- Twig + **AssetMapper** (pas de bundler npm/Node)
- **Tailwind CSS v4** via `symfonycasts/tailwind-bundle` (binaire standalone).
  Config en CSS (`@theme` dans `assets/styles/app.css`), pas de
  `tailwind.config.js`. Build : `php bin/console tailwind:build --watch` (dev)
  / `--minify` (prod).
- **symfony/ux-twig-component** pour factoriser les motifs répétés en
  composants Twig (`<twig:LinkCard>`, `<twig:ProjectCard>`, `<twig:PostCard>`,
  `<twig:Meander>`, `<twig:Column>`, `<twig:SiteFooter>` dans
  `templates/components/`) — préférer cette approche à la duplication de
  longues chaînes de classes ou à de simples `{% include %}`.
- **league/commonmark** pour le rendu des articles de blog (Markdown → HTML,
  pur PHP, pas de build JS).
- Éviter le CSS vanilla hors nécessité stricte (keyframes, texture de fond) :
  privilégier les utilitaires Tailwind.

## Infrastructure Docker (dev et prod, projet autonome)

Le projet est autonome : il embarque sa propre base PostgreSQL et ne dépend
d'aucun autre dépôt applicatif. Seul le reverse proxy reste une propriété de
l'hôte — un proxy possède les ports 80/443 et ne peut en exister qu'un par
machine — fourni par le dépôt partagé **https://github.com/petroslabs/infra**
(Traefik + docker-proxy, rien d'autre : pas de base de données ni de
service mutualisés). Historique : le projet dépendait auparavant de l'infra
partagée `symfony_env` (Traefik + PostgreSQL + Redis + Mailpit communs à
plusieurs projets) — démantelée au profit de ce découplage.
- `Dockerfile` multi-stage : base commune (`pdo_pgsql`/`opcache`, `composer`
  copié depuis l'image officielle — absent de l'image `dunglas/frankenphp`
  de base), puis `frankenphp_prod` (code + `composer install --no-dev` +
  assets compilés — Tailwind minifié, `asset-map:compile` — intégrés à
  l'image au build, exécution en `www-data` non-root) et `frankenphp_dev`
  (image outillage seule, code fourni par bind-mount). `SERVER_NAME=:80`
  désactive la gestion HTTPS automatique de Caddy (Traefik termine déjà le
  TLS) — sans ça, le port 80 interne ne fait que rediriger vers son propre
  443.
- `compose.yaml` : service `app` (cible `frankenphp_prod` par défaut) +
  service `database` (PostgreSQL embarqué, `postgres:16-alpine`, volume
  nommé `database_data`, healthcheck — l'app attend `service_healthy` avant
  de démarrer). L'app rejoint un réseau interne (`default`, app + base,
  cloisonné par projet) et un réseau externe `proxy` (nom paramétrable via
  `PROXY_NETWORK`, `edge` par défaut — celui que possède le dépôt `infra`).
  Labels Traefik templatés sur `${APP_DOMAIN:-petroslabs.localhost}`, sans
  `certresolver` (ajouté par `compose.prod.yaml` seulement — le dev s'appuie
  sur les certificats mkcert de `infra`). `compose.override.yaml`
  (auto-chargé par un `docker compose` sans `-f`, donc actif en dev local)
  bascule l'app sur `frankenphp_dev` avec bind-mount `./:/app`, et publie le
  port de `database` sur `127.0.0.1` pour les clients SQL locaux.
  `compose.prod.yaml` (à combiner explicitement, `-f compose.yaml -f
  compose.prod.yaml` — ce qui désactive l'auto-chargement de
  `compose.override.yaml`) ajoute le label `certresolver=letsencrypt`, des
  limites de ressources, et injecte `.env.local` comme variables
  d'environnement du conteneur (`env_file:`) puisque le stage prod n'a plus
  de bind-mount pour que le dotenv de Symfony le lise depuis le disque.
- `DATABASE_URL` est fixé directement dans `compose.yaml` (`environment:`),
  construit depuis `DB_USER`/`DB_PASSWORD`/`DB_NAME` (défauts de dev
  `app`/`!ChangeMe!`/`app`, à écraser en prod via `.env.docker`) — il prend
  le pas sur toute valeur lue depuis `.env.local`, puisque la base n'est plus
  un secret externe mais une ressource du projet. `.env.docker` (non
  versionné, cf. `.env.docker.example`) porte aussi `APP_DOMAIN` et
  `PROXY_NETWORK` pour Docker Compose — distinct du `.env`/`.env.local` de
  Symfony, pour ne pas mélanger les deux mécanismes de variables.
- `trusted_proxies` (`config/packages/framework.yaml`) : nécessaire pour que
  Symfony sache qu'il est servi en HTTPS derrière Traefik (sinon URLs
  canoniques/SEO en `http://` et validation CSRF cassée — voir `security.csrf
  stateless_token_ids` ci-dessous). Ne pas retirer.
- Déploiement prod : `make deploy-prod` (build → migrations Doctrine → up)
  depuis le dossier du projet cloné sur le VPS, `PROXY_NETWORK` pointé sur
  le réseau du reverse proxy déjà actif sur la machine — détail dans le
  README. **Production actuelle (`petroslabs.dev`)** : `symfony_env`
  (Traefik + PostgreSQL + Redis mutualisés, servait aussi MécaTips) a été
  entièrement démantelé — conteneurs, réseaux et volumes supprimés,
  MécaTips hors ligne (décision assumée). Le VPS tourne désormais sur
  [`petroslabs/infra`](https://github.com/petroslabs/infra)
  (`PROXY_NETWORK=edge`), déployé pour l'occasion.

## Architecture

- `src/Controller/HomeController.php` : route `/`, contenu via
  `App\Repository\ProfileRepository`/`HubLinkRepository` (base de données —
  voir section Gestion du contenu).
- `src/Controller/ProjectController.php` : routes `/projects` (liste) et
  `/projects/{slug}` (détail), contenu via `App\Repository\ProjectRepository`
  (base de données — voir section Gestion du contenu). Résumé court
  (`summary`, affiché sur la carte, tronqué en CSS) et contenu long
  (`content`, Markdown, page détail uniquement) sont deux champs distincts,
  même logique que `BlogPost::summary`/`content`.
- `src/Controller/UsesController.php` : route `/uses`, contenu via
  `App\Repository\UseCategoryRepository` (base de données — voir section
  Gestion du contenu).
- `src/Controller/BlogController.php` : routes `/blog` (liste), `/blog/{slug}`
  (article) et `/blog/rss.xml` (flux RSS — route déclarée avant `/blog/{slug}`
  dans la classe, sans quoi le routeur la ferait matcher comme un slug).
  Contenu via `App\Blog\BlogPostRepository` — une façade (namespace
  `App\Blog`, distinct de `App\Repository`) qui résout la langue et convertit
  le Markdown en HTML, en s'appuyant en interne sur
  `App\Repository\BlogPostRepository` (Doctrine, base de données). Fallback
  FR si une traduction manque, comme `|localized`. Le flux RSS reste en FR
  uniquement (même limite assumée que le reste du SEO — voir section SEO) et
  utilise le résumé comme description, pas le contenu complet.
- `templates/base.html.twig` : layout commun (polices, metas, fond, colonnes
  décoratives, switcher de langue).
- `templates/home/index.html.twig` : la landing, étend `base.html.twig`.
- `templates/projects/index.html.twig` : la page Projets, étend `base.html.twig`.
- `templates/uses/index.html.twig` : L'établi, étend `base.html.twig`.
- `templates/blog/` : liste des articles (`index.html.twig`) et page article
  (`show.html.twig`), étendent `base.html.twig`. Le corps HTML d'un article
  est stylé via `.prose-blog` dans `assets/styles/app.css` (CSS vanilla
  assumé ici : pas de classes utilitaires possibles sur du HTML généré côté
  serveur depuis du Markdown).
- `templates/components/` : composants Twig réutilisables.

## Internationalisation (FR/EN)

- **Pas de préfixe d'URL** : mêmes routes dans les deux langues, la
  préférence est mémorisée dans un cookie (`LocaleController`,
  route `/lang/{locale}`) et lue à chaque requête par `LocaleSubscriber`
  (`src/EventListener/`).
- **Contenu éditorial** (bio, tagline, résumés de projets, items de
  l'établi…) : résolu dans les templates par le filtre Twig `\|localized`
  (`src/Twig/LocalizedContentExtension.php`), qui attend un tableau
  `{ fr: '...', en: '...' }`. Un champ resté en simple chaîne (nom propre,
  valeur technique) est renvoyé tel quel par ce filtre — ne pas forcer un
  `{ fr, en }` quand la valeur ne change pas de langue. Selon le contenu :
  - en base (Hub, Projets, Établi) : deux colonnes distinctes
    (`nomFr`/`nomEn`) sur l'entité Doctrine, plus un accesseur
    `getNom(): array` qui recompose le tableau `{ fr, en }` pour que le
    template n'ait rien à changer.
  - encore en Markdown (Blog) : frontmatter/contenu séparés par fichier
    (`{slug}.fr.md` / `.en.md`).
- **Libellés d'interface** (menus, badges de statut, boutons, footer,
  titres/meta de page) : clés de traduction dans
  `translations/messages.fr.yaml` / `messages.en.yaml`, via le filtre
  `\|trans` — jamais dans les YAML de contenu.
- Quand un nouveau champ éditorial est ajouté, l'ajouter en bilingue dès le
  départ (même schéma `{ fr, en }`) plutôt que de le rattraper après coup.

## SEO

- `public/robots.txt` (statique) + `/sitemap.xml` (`SitemapController`,
  généré depuis les routes statiques et les articles de blog).
- `templates/base.html.twig` : URL canonique, `og:image`/Open Graph/Twitter
  Card complets, bloc Twig `{% block schema %}` pour le JSON-LD (rempli par
  `home/index.html.twig` avec un schema `Person`, et par
  `blog/show.html.twig` avec un schema `BlogPosting` par article).
- **Limite assumée et ne pas tenter de la contourner sans validation** :
  l'i18n cookie/sans-préfixe empêche un hreflang correct — seule la version
  FR (par défaut, sans cookie) est indexée par les moteurs de recherche.
  L'EN reste un confort pour les visiteurs humains, pas un contenu
  référencé séparément. Revoir ce choix impliquerait de repasser sur
  l'architecture des routes (préfixe `/en/…`), à ne faire que sur demande
  explicite.
- Toute nouvelle image destinée au web (screenshots, illustrations) : la
  compresser (WebP, ~150-300 Ko max) avant de l'ajouter — ne pas committer
  d'images brutes de plusieurs Mo.

## Gestion du contenu

Pas d'espace d'administration : volontairement retiré (auth, CRUD, formulaires
Symfony, EasyAdmin avait de toute façon été écarté) car jamais utilisé — tout
le contenu passe par des migrations Doctrine ou des commandes `bin/console`
ponctuelles, écrites au cas par cas selon le besoin.

- **Hub** : profil (`App\Entity\Profile`, singleton — une seule ligne) et
  liens (`App\Entity\HubLink`, champ `position` pour l'ordre d'affichage).
  Champs bilingues stockés en deux colonnes (`labelFr`/`labelEn`, etc.) avec
  un accesseur `getLabel(): array` qui recompose `{ fr, en }` pour rester
  compatible avec le filtre `|localized` dans les templates — même trick que
  `Project::getSummary()`.
- **Établi** : `App\Entity\UseCategory` (catégories, avec sa collection
  `items` triée par `position` via `#[ORM\OrderBy]`) et `App\Entity\UseItem`
  (`ManyToOne` vers sa catégorie). Supprimer une catégorie supprime ses items
  (`cascade: ['remove'], orphanRemoval: true`).
- **Blog** : `App\Entity\BlogPost` (`src/Entity/`, table `blog_post` —
  distinct de `App\Blog\BlogPost`, le DTO résolu pour le rendu). Contenu
  Markdown stocké tel quel en base (`contentFr`/`contentEn`), converti en
  HTML à l'affichage par `App\Blog\BlogPostRepository`. Création/mise à jour
  via `bin/console app:blog:post` (`src/Command/BlogPostCommand.php`,
  interactif, trouve l'article par slug pour savoir s'il crée ou met à
  jour) plutôt qu'une migration à la main — le contenu FR/EN se rédige dans
  un fichier Markdown à part, la commande ne fait que lire le chemin donné.
  Un article peut être lié à un projet (`BlogPost::project`, `ManyToOne`
  nullable, `onDelete: SET NULL`) — la commande le demande (slug, validé
  contre les projets existants). Affiché dans les deux sens : lien vers le
  projet sur l'article (`blog/show.html.twig`), liste des articles liés sur
  la fiche projet (`projects/show.html.twig`, via
  `App\Blog\BlogPostRepository::findByProject()`).
- Toutes ces entités sont passées de fichiers (YAML/Markdown) à des tables
  Doctrine lors de leur migration initiale — aucune ne reste en fichiers.

## Sécurité

- **Headers** (`nelmio/security-bundle`, `config/packages/nelmio_security.yaml`) :
  clickjacking (`X-Frame-Options: DENY`), `X-Content-Type-Options: nosniff`
  et `Referrer-Policy` actifs dans tous les environnements. La CSP et le HSTS
  (`forced_ssl`), eux, ne sont définis que sous `when@prod` — le hot-reload
  de dev charge des scripts depuis `cdn.jsdelivr.net` (voir `base.html.twig`)
  qu'une CSP stricte casserait. Toute nouvelle ressource externe (police,
  script, image tierce) doit être ajoutée aux directives `enforce`
  correspondantes, sans quoi elle sera bloquée silencieusement en prod
  (vérifier la console navigateur).
- **Piège CSP découvert par audit** : si une directive `enforce` a
  exactement la même valeur que `default-src` (ex. `script-src: ["'self'"]`
  == `default-src: ["'self'"]`), nelmio la considère redondante et ne
  l'émet pas dans le header — elle "replie" silencieusement sur
  `default-src`. Ça casse l'injection de nonce (`csp_nonce()`) tant qu'un
  nonce n'est pas explicitement demandé : sans ça, `importmap()` (3
  `<script>` inline) est bloqué en prod sans jamais planter visiblement,
  juste des erreurs CSP en console. D'où `{{ importmap('app', {nonce:
  csp_nonce('script')}) }}` dans `base.html.twig` plutôt que
  `importmap('app')` seul. Pour vérifier une CSP en local sans toucher la
  prod : builder le stage `frankenphp_prod` (`docker build --target
  frankenphp_prod`), le lancer avec un header `X-Forwarded-Proto: https`
  (simule Traefik) contre une base PostgreSQL jetable, puis comparer le
  nonce du header `Content-Security-Policy` à celui des balises `<script>`
  dans le HTML — doivent être identiques sur une même réponse.
- **`'strict-dynamic'` sur `script-src`** : `assets/app.js` importe
  `./styles/app.css` — AssetMapper résout ça en une URI `data:` no-op dans
  l'import map (le CSS est en réalité chargé via le `<link>` généré côté
  serveur). Un nonce ne couvre que le script qui le porte, pas ce qu'il
  importe dynamiquement ensuite : sans `'strict-dynamic'`, cette URI `data:`
  est bloquée par le CSP (console uniquement, aucun impact visible — le CSS
  charge quand même via le `<link>`). `'strict-dynamic'` propage la
  confiance du script noncé à ses imports (CSP3, ignoré sans casse par les
  navigateurs CSP1/2).

## Attentes de collaboration

- **Tenir le `CHANGELOG.md` à jour** : à chaque modification notable
  (ajout, changement, correction), ajouter une entrée dans la section
  `[Non publié]` (catégories Ajouté / Modifié / Corrigé, format Keep a
  Changelog). Pas nécessaire pour des micro-détails sans impact (typo dans un
  commentaire, reformulation mineure) — utiliser le jugement.
- **Tenir le `README.md` à jour** : quand un changement modifie la stack, la
  structure du projet, les commandes de dev/build ou l'état d'avancement
  (cases à cocher blog/projets), répercuter la mise à jour dans le README.
  Comme pour le changelog, pas la peine pour un détail sans impact sur ce que
  lirait un nouvel arrivant sur le projet.
- Avant de générer de nouvelles pages/fonctionnalités (blog, projets),
  proposer l'arborescence et attendre validation avant d'exécuter, comme pour
  la landing initiale.
- **Ne jamais commit/push automatiquement.** Une fois le travail terminé,
  présenter ce qui a été fait puis demander explicitement avant de committer
  et pousser. Ne pas considérer un "go" donné une fois comme valable pour la
  suite de la conversation — redemander à chaque fois.
- Respecter l'accessibilité : balises sémantiques, `alt` sur les images,
  contrastes suffisants malgré le fond sombre.
- Code simple et lisible, pas de sur-ingénierie ni d'abstraction anticipée.
