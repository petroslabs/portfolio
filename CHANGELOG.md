# Changelog

Tous les changements notables de ce projet sont documentés dans ce fichier.

Le format s'inspire de [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/).

## [Non publié]

### Ajouté
- Landing page (`/`) : hub central avec bannière (désactivée pour le moment),
  logo, titre, tagline terminal, bio et grille de liens (GitHub, Email, Blog,
  Projets, LinkedIn).
- `HomeController` dédié à la route `/`, structuré pour cohabiter avec de
  futurs `BlogController`, `ProjectController` et un espace `Admin\`.
- Contenu du hub (profil + liens) piloté par `config/hub.yaml`, en attendant
  un passage en base de données lors de l'ajout de l'admin.
- Direction artistique « PetrosLabs » : fusion Grèce antique / laboratoire de
  dev moderne (contraste pierre-marbre spartiate et lueurs teal/bronze de
  terminal).
- Intégration de Tailwind CSS v4 via `symfonycasts/tailwind-bundle` (binaire
  standalone, sans Node/npm), avec palette et polices (Cinzel, Inter,
  JetBrains Mono) configurées en `@theme` sémantique.
- Composants Twig réutilisables (`symfony/ux-twig-component`) :
  `<twig:LinkCard>` (carte de lien avec glow au survol) et `<twig:Meander>`
  (séparateur méandre grec).
- `README.md` : présentation du projet, état d'avancement, stack et
  commandes de dev/build, tenu à jour au fil du projet.
- Composant `<twig:Column>` : colonne dorique décorative (chapiteau, fût
  cannelé, base) en filigrane sur les bords du viewport, visible à partir de
  `lg` uniquement — renforce l'ambiance temple antique sur grand écran.
- Halo teal en bas de page, en complément du halo bronze existant en haut :
  équilibre l'ambiance chaude/froide sur toute la hauteur, pas seulement au
  survol des cartes.
- Garniture typographique lapidaire autour du titre `PetrosLabs` : petits
  losanges bronze (interpuncts) de part et d'autre, avec filets qui
  s'estompent vers l'extérieur à partir de `sm:`.
- Barre de défilement personnalisée : bronze au repos, teal au survol/actif
  (même logique que `::selection`), à la place du gris par défaut du
  navigateur.
- Page Projets (`/projects`) : vitrine des projets avec image, résumé, stack
  technique, badge de statut (en cours / terminé / archivé) et liens code/démo.
  `ProjectController` dédié, contenu piloté par `config/projects.yaml` (même
  logique que `hub.yaml` — migrera en base de données quand l'espace Admin
  sera développé). Nouveau composant `<twig:ProjectCard>`.
- Composant `<twig:SiteFooter>` : pied de page factorisé, réutilisé par la
  landing et la page Projets.
- Page "L'établi" (`/uses`) : outils, stack et matériel utilisés au quotidien,
  par catégories. `UsesController` dédié, contenu piloté par
  `config/uses.yaml` (même logique que `hub.yaml`/`projects.yaml`).
- Internationalisation FR/EN, sans préfixe d'URL : la langue est mémorisée
  dans un cookie (1 an) et se change via un switcher discret en haut de
  chaque page.
  - `LocaleSubscriber` (lit le cookie à chaque requête) et
    `LocaleController` (route `/lang/{locale}`, protégée contre
    l'open-redirect sur le `Referer`).
  - Filtre Twig `\|localized` (`LocalizedContentExtension`) : résout les
    champs de contenu bilingues `{ fr, en }` définis dans `hub.yaml`,
    `projects.yaml` et `uses.yaml` (bio, tagline, résumés, labels…).
  - Libellés d'interface (menus, badges de statut, boutons Code/Démo,
    footer, titres/meta de page) traduits via
    `translations/messages.{fr,en}.yaml`.
  - Les traductions anglaises du contenu éditorial (bio, tagline, résumé de
    projet, items de l'établi) sont une première version à ajuster par
    Pierre pour retrouver sa voix.
- SEO : `robots.txt`, sitemap dynamique (`/sitemap.xml`, `SitemapController`),
  URL canonique par page, image `og:image` (1200×630), meta Open Graph
  complètes (`og:url`, `og:locale`, dimensions d'image) et Twitter Card
  (`summary_large_image`), JSON-LD `Person` sur la landing (nom, URL,
  `sameAs` vers les liens externes du hub).
  - Limite assumée : l'i18n cookie/sans-préfixe ne permet pas un vrai
    hreflang — seule la version FR (par défaut) est indexée par les
    moteurs de recherche ; l'EN reste une option de confort pour les
    visiteurs humains.
- Blog (`/blog`, `/blog/{slug}`) : articles en fichiers Markdown
  (`content/blog/{slug}.fr.md` / `.en.md`, frontmatter YAML pour les
  métadonnées), même logique que `hub.yaml`/`projects.yaml`/`uses.yaml` —
  pas de base de données pour l'instant. `BlogController` dédié,
  `App\Blog\BlogPostRepository` (scan + parsing, converti en HTML via
  `league/commonmark`), fallback FR si une traduction manque.
  - Composant `<twig:PostCard>` pour la liste des articles.
  - JSON-LD `BlogPosting` sur chaque article, ajout au sitemap dynamique.
  - Lien "Blog" du hub : pointe désormais vers `/blog` au lieu de `#`.
  - Premier article publié : "Bienvenue au laboratoire" (billet d'annonce,
    à remplacer/compléter par du vrai contenu quand tu le souhaites).
- Licence MIT (`LICENSE`), projet désormais open source — `composer.json`
  mis à jour en conséquence (`license: "MIT"`, à la place de `proprietary`).
- Intégration à l'infra Docker partagée `symfony_env` (Traefik + PostgreSQL +
  Redis + Mailpit) : `Dockerfile` (image `dunglas/frankenphp:php8.4` + extension
  `pdo_pgsql`), `compose.yaml` (service `app`, réseau externe `symfony_env`,
  labels Traefik pour `petroslabs.localhost`). Remplace le scaffold par
  défaut de `doctrine/doctrine-bundle` (PostgreSQL/Mailpit isolés dans
  `compose.override.yaml`, supprimé).
- `Makefile` : commandes de dev (`serve`, `tailwind-watch`/`tailwind-build`,
  `lint`, `test`, `cache-clear`) et de gestion Docker (`build`, `up`, `down`,
  `logs`, `sh`, `console`, `traefik-restart`, `db-create`/`db-drop`) — `make
  help` liste tout.
- Espace Admin (`/admin`), première itération : authentification
  (`App\Entity\User`, formulaire de connexion `/admin/login`, un seul compte
  provisionné via `app:create-admin`) et CRUD complet des Projets
  (`/admin/projects`), formulaires Symfony faits main (pas d'EasyAdmin).
  Tableau de bord (`/admin`) : une carte par section de contenu (Projets
  actif, Hub/Établi/Blog affichés "Bientôt" en attendant leur migration en
  base) — le login y redirige (`default_target_path`).
  - Les Projets passent de `config/projects.yaml` (supprimé) à une table
    `project` en base — `App\Entity\Project`, `ProjectRepository`, migration
    Doctrine reprenant le contenu existant. Le Hub et L'établi restent en
    YAML pour l'instant (prochaine itération) ; le Blog migrera en base plus
    tard aussi.
- CRUD Hub dans l'Admin : profil (`/admin/profile`, édition seule — c'est un
  singleton) et liens du hub (`/admin/hub-links`, CRUD complet). Le Hub
  passe de `config/hub.yaml` (supprimé) aux tables `profile`/`hub_link` en
  base — `App\Entity\Profile`, `App\Entity\HubLink`, migration Doctrine
  reprenant le contenu existant. `HomeController` (public) lit désormais
  `ProfileRepository`/`HubLinkRepository`. Carte "Hub" du tableau de bord
  admin activée.
- CRUD L'établi dans l'Admin : catégories (`/admin/uses`, liste + CRUD) et
  items (`/admin/uses/items/...`, CRUD, rattachés à une catégorie). L'établi
  passe de `config/uses.yaml` (supprimé) aux tables `use_category`/`use_item`
  en base — `App\Entity\UseCategory` (avec ses items en collection triée par
  position), `App\Entity\UseItem`, migration Doctrine reprenant le contenu
  existant. Suppression d'une catégorie entraîne celle de ses items
  (`orphanRemoval`). `UsesController` (public) lit désormais
  `UseCategoryRepository`. Carte "L'établi" du tableau de bord admin activée.
- CRUD Blog dans l'Admin (`/admin/blog`) : liste, création, édition,
  suppression. Le Blog passe des fichiers Markdown (`content/blog/*.md`,
  supprimés) à une table `blog_post` en base — `App\Entity\BlogPost`
  (contenu Markdown brut stocké en base, converti en HTML à l'affichage via
  `league/commonmark`, comme avant), migration Doctrine reprenant l'article
  "Bienvenue au laboratoire". `App\Blog\BlogPostRepository` (la façade lue
  par `BlogController`/`SitemapController`) garde exactement la même API
  publique (`all()`/`find()`) — seule son implémentation interne change,
  aucune modification nécessaire côté contrôleurs publics. Carte "Blog" du
  tableau de bord admin activée : les quatre sections de contenu du site
  sont désormais gérables depuis l'Admin.
- Éditeur WYSIWYG pour le contenu des articles de blog dans l'admin
  (`/admin/blog/new`, `/edit`) : Toast UI Editor, ajouté via AssetMapper
  (`importmap.php`) et un contrôleur Stimulus
  (`assets/controllers/blog_editor_controller.js`) qui synchronise le
  Markdown généré vers le champ réel du formulaire — `contentFr`/`contentEn`
  restent stockés en Markdown brut en base, aucun changement de schéma ni
  de rendu. Thème sombre officiel de l'éditeur, avec son accent bleu par
  défaut réaligné sur le teal du site (`assets/styles/app.css`).

### Corrigé
- Rendu Markdown des articles de blog : `App\Blog\BlogPostRepository`
  utilisait `CommonMarkConverter` (spec CommonMark de base), qui ne sait pas
  interpréter la syntaxe GFM que génère l'éditeur WYSIWYG (tableaux, texte
  barré, checkboxes/task-lists) — remplacé par
  `GithubFlavoredMarkdownConverter` (même paquet `league/commonmark`, aucune
  dépendance ajoutée).
- `.prose-blog` (`assets/styles/app.css`) : styles manquants ou insuffisants
  pour plusieurs éléments Markdown — titres `h1`/`h4` (seuls `h2`/`h3`
  étaient stylés), séparateurs `<hr>`, citations (fond + marges), tableaux,
  checkboxes de task-lists (puce native masquée, `accent-color` teal), et
  contraste des blocs de code (fond distinct + bordure, au lieu de se fondre
  avec le fond de la page).

### Modifié
- Retrait du champ bannière du profil (`App\Entity\Profile`) : essayé avec
  une vraie image, pas concluant côté direction artistique — retiré de
  l'entité, du formulaire admin et de la base (migration de suppression de
  colonne), plutôt que laissé désactivé/commenté.
- Retrait des références à la métaphore de la « forge » au profit du
  vocabulaire « labs / laboratoire », plus proche de l'identité PetrosLabs.
- Retrait du lien vers X (réseau social).
- Texte du pied de page : « quelque part entre Sparte et le terminal »
  (à la place de « construit à la main, entre pierre & terminal »).
- Lien "Projets" du hub : pointe désormais vers `/projects` au lieu de `#`.
- Premier projet de la vitrine remplacé par ce site lui-même (PetrosLabs),
  avec une vraie capture d'écran à la place du placeholder.
- Switcher de langue : passé en colonne verticale (FR / EN empilés), collé
  au bord réel du viewport, pour ne plus chevaucher les colonnes décoratives
  à partir de `lg:`.
- Image du projet PetrosLabs recompressée en WebP (2,6 Mo → 19 Ko).

### Corrigé
- Lien Email du hub : utilisation du schéma `mailto:` (le lien ouvrait une
  page relative au lieu du client mail).
- `trusted_proxies` (`config/packages/framework.yaml`) : Symfony ne faisait
  pas confiance à Traefik comme proxy, donc croyait être servi en HTTP (le
  conteneur ne voit que du trafic HTTP en interne, Traefik terminant le
  TLS) — cassait les URLs canoniques/SEO (`https://` attendu) et la
  validation CSRF des formulaires admin derrière l'infra `symfony_env`.
- `/admin` (sans suffixe) renvoyait une 404 (aucune route ne le couvrait) —
  ajout de `DashboardController`.
- Layout admin (`templates/admin/base.html.twig`) : aucun moyen de revenir
  au site public depuis `/admin/login` — un lien "← Retour au site" est
  désormais toujours visible.
