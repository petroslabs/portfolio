<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Crée (ou met à jour, si le slug existe déjà) un article de blog — remplace
 * l'écriture à la main d'une migration Doctrine par article. Le contenu
 * Markdown (FR/EN) se rédige dans l'éditeur habituel ; la commande ne fait
 * que lire les fichiers et les stocker en base.
 */
#[AsCommand(name: 'app:blog:post', description: "Crée ou met à jour un article de blog")]
final class BlogPostCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BlogPostRepository $posts,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $slug = $io->ask('Slug (ex. mon-article)', validator: $this->slugValidator());
        $post = $this->posts->findOneBy(['slug' => $slug]);

        if ($post) {
            $io->note(\sprintf('Article existant trouvé ("%s") — touche Entrée pour garder une valeur.', $post->getTitleFr()));
        } else {
            $post = (new BlogPost())->setSlug($slug);
        }

        $post->setTitleFr($this->askWithDefault($io, 'Titre (FR)', $post->getTitleFr()));
        $post->setTitleEn($this->askWithDefault($io, 'Titre (EN)', $post->getTitleEn()));
        $post->setSummaryFr($this->askWithDefault($io, 'Résumé (FR)', $post->getSummaryFr()));
        $post->setSummaryEn($this->askWithDefault($io, 'Résumé (EN)', $post->getSummaryEn()));
        $post->setContentFr($this->readMarkdownFile($io, 'Contenu (FR)', $post->getContentFr()));
        $post->setContentEn($this->readMarkdownFile($io, 'Contenu (EN)', $post->getContentEn()));
        $post->setCover($this->readOptionalText($io, 'Image de couverture (chemin, vide pour aucune)', $post->getCover()));
        $post->setDate($this->readDate($io, $post->getDate()));

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $io->success(\sprintf('Article "%s" (/blog/%s) enregistré.', $post->getTitleFr(), $post->getSlug()));

        return Command::SUCCESS;
    }

    private function slugValidator(): \Closure
    {
        return static function (?string $answer): string {
            if (!$answer || !preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $answer)) {
                throw new \InvalidArgumentException('Slug invalide — minuscules, chiffres et tirets uniquement (ex. mon-article).');
            }

            return $answer;
        };
    }

    private function askWithDefault(SymfonyStyle $io, string $label, string $current): string
    {
        $question = '' !== $current ? \sprintf('%s [%s]', $label, $current) : $label;

        return $io->ask($question, '' !== $current ? $current : null) ?? '';
    }

    private function readOptionalText(SymfonyStyle $io, string $label, ?string $current): ?string
    {
        $question = $current ? \sprintf('%s [%s]', $label, $current) : $label;
        $answer = $io->ask($question, $current);

        return ('' === $answer || null === $answer) ? null : $answer;
    }

    private function readMarkdownFile(SymfonyStyle $io, string $label, string $current): string
    {
        while (true) {
            $path = $io->ask(\sprintf('%s — chemin vers un fichier Markdown (vide pour garder le contenu actuel)', $label));

            if (!$path) {
                if ('' === $current) {
                    $io->warning('Aucun contenu actuel à conserver — un chemin est requis.');

                    continue;
                }

                return $current;
            }

            if (!is_readable($path)) {
                $io->error(\sprintf('Fichier introuvable ou illisible : %s', $path));

                continue;
            }

            return (string) file_get_contents($path);
        }
    }

    private function readDate(SymfonyStyle $io, \DateTimeImmutable $current): \DateTimeImmutable
    {
        while (true) {
            $answer = $io->ask('Date (AAAA-MM-JJ)', $current->format('Y-m-d'));

            try {
                return new \DateTimeImmutable((string) $answer);
            } catch (\Exception) {
                $io->error('Date invalide — format attendu AAAA-MM-JJ.');
            }
        }
    }
}
