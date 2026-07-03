# PetrosLabs — petroslabs.dev

Site perso de Pierre. Symfony (dernier LTS), pensé comme un hub qui accueillera
progressivement un blog, des projets et un espace admin.

## État actuel

La **landing page (`/`)** et la **page Projets (`/projects`)** sont
implémentées. Le blog (`/blog`) et l'admin (`Admin\`) ne sont **pas encore
développés** — le code est structuré pour les accueillir sans refactoring,
mais ne pas les générer sans demande explicite.

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
  composants Twig (`<twig:LinkCard>`, `<twig:ProjectCard>`, `<twig:Meander>`,
  `<twig:Column>`, `<twig:SiteFooter>` dans `templates/components/`) —
  préférer cette approche à la duplication de longues chaînes de classes ou
  à de simples `{% include %}`.
- Éviter le CSS vanilla hors nécessité stricte (keyframes, texture de fond) :
  privilégier les utilitaires Tailwind.

## Architecture

- `src/Controller/HomeController.php` : route `/`, reçoit le contenu du hub
  via injection de paramètres (bind dans `config/services.yaml`). Conçu pour
  cohabiter avec de futurs `BlogController`, `Admin\…` sans modification.
- `src/Controller/ProjectController.php` : route `/projects`, même pattern
  d'injection que `HomeController`, contenu depuis `config/projects.yaml`.
- `config/hub.yaml` / `config/projects.yaml` : contenu **en configuration
  YAML**, pas en base de données. Ce choix est volontaire et temporaire — le
  jour où l'espace admin est développé, ce contenu bascule en base (les
  projets y seront alors ajoutables directement) ; ne pas anticiper cette
  migration avant qu'elle soit demandée.
- `templates/base.html.twig` : layout commun (polices, metas, fond, colonnes
  décoratives).
- `templates/home/index.html.twig` : la landing, étend `base.html.twig`.
- `templates/projects/index.html.twig` : la page Projets, étend `base.html.twig`.
- `templates/components/` : composants Twig réutilisables.

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
