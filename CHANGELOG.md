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

### Modifié
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
