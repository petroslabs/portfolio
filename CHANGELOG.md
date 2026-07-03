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

### Modifié
- Retrait des références à la métaphore de la « forge » au profit du
  vocabulaire « labs / laboratoire », plus proche de l'identité PetrosLabs.
- Retrait du lien vers X (réseau social).

### Corrigé
- Lien Email du hub : utilisation du schéma `mailto:` (le lien ouvrait une
  page relative au lieu du client mail).
