# PetrosLabs — petroslabs.dev

Site perso de Pierre. Symfony (dernier LTS), pensé comme un hub qui accueillera
progressivement un blog, des projets et un espace admin.

## État actuel

La **landing page (`/`)**, la **page Projets (`/projects`)**, **L'établi
(`/uses`)** et le **blog (`/blog`)** sont implémentés, avec
**internationalisation FR/EN**. L'admin (`Admin\`) n'est **pas encore
développé** — le code est structuré pour l'accueillir sans refactoring, mais
ne pas le générer sans demande explicite.

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

## Architecture

- `src/Controller/HomeController.php` : route `/`, reçoit le contenu du hub
  via injection de paramètres (bind dans `config/services.yaml`). Conçu pour
  cohabiter avec de futurs `BlogController`, `Admin\…` sans modification.
- `src/Controller/ProjectController.php` : route `/projects`, même pattern
  d'injection que `HomeController`, contenu depuis `config/projects.yaml`.
- `src/Controller/UsesController.php` : route `/uses`, contenu depuis
  `config/uses.yaml`.
- `config/hub.yaml` / `config/projects.yaml` / `config/uses.yaml` : contenu
  **en configuration YAML**, pas en base de données. Ce choix est volontaire
  et temporaire — le jour où l'espace admin est développé, ce contenu
  bascule en base (les projets y seront alors ajoutables directement) ; ne
  pas anticiper cette migration avant qu'elle soit demandée.
- `src/Controller/BlogController.php` : routes `/blog` (liste) et
  `/blog/{slug}` (article). Contenu via `App\Blog\BlogPostRepository`, qui
  scanne `content/blog/{slug}.fr.md` / `.en.md` (frontmatter YAML +
  Markdown), pas de base de données — même logique temporaire que les YAML
  ci-dessus. Fallback FR si une traduction manque, comme `|localized`.
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
  l'établi…) : champs `{ fr: '...', en: '...' }` directement dans les YAML
  de contenu (`hub.yaml`, `projects.yaml`, `uses.yaml`), résolus dans les
  templates par le filtre Twig `\|localized`
  (`src/Twig/LocalizedContentExtension.php`). Un champ resté en simple
  chaîne (nom propre, valeur technique) est renvoyé tel quel par ce filtre —
  ne pas forcer un `{ fr, en }` quand la valeur ne change pas de langue.
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

## Attentes de collaboration

- **Tenir le `CHANGELOG.md` à jour** : à chaque modification notable
  (ajout, changement, correction), ajouter une entrée dans la section
  `[Non publié]` (catégories Ajouté / Modifié / Corrigé, format Keep a
  Changelog). Pas nécessaire pour des micro-détails sans impact (typo dans un
  commentaire, reformulation mineure) — utiliser le jugement.
- **Tenir le `README.md` à jour** : quand un changement modifie la stack, la
  structure du projet, les commandes de dev/build ou l'état d'avancement
  (cases à cocher blog/projets/admin), répercuter la mise à jour dans le
  README. Comme pour le changelog, pas la peine pour un détail sans impact
  sur ce que lirait un nouvel arrivant sur le projet.
- Avant de générer de nouvelles pages/fonctionnalités (blog, projets, admin),
  proposer l'arborescence et attendre validation avant d'exécuter, comme pour
  la landing initiale.
- **Ne jamais commit/push automatiquement.** Une fois le travail terminé,
  présenter ce qui a été fait puis demander explicitement avant de committer
  et pousser. Ne pas considérer un "go" donné une fois comme valable pour la
  suite de la conversation — redemander à chaque fois.
- Respecter l'accessibilité : balises sémantiques, `alt` sur les images,
  contrastes suffisants malgré le fond sombre.
- Code simple et lisible, pas de sur-ingénierie ni d'abstraction anticipée.
