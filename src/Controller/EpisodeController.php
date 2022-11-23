<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/episode')]
class EpisodeController extends AbstractController
{
    #[Route('/', name: 'app_episode_index', methods: ['GET'])]
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_episode_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MailerInterface $mailer, EpisodeRepository $episodeRepository, SluggerInterface $slugger): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {
            if (trim($episode->getTitle()) != "") {
                $episode->setSlug($slugger->slug($episode->getTitle()));

            }
            $episodeRepository->save($episode, true);
            $program = $episode->getSeason()->getProgram();
            $email = (new Email())

                ->from($this->getParameter('mailer_from'))

                ->to($this->getParameter('mailer_to'))

                ->subject('Un nouvel épisode vient d\'être publiée !')

                ->html($this->renderView('episode/newEpisodeEmail.html.twig', ['episode' => $episode,
            'program' => $program]));
                
                $mailer->send($email);

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_episode_show', methods: ['GET'])]
    public function show(Episode $episode): Response
    {
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Episode $episode, EpisodeRepository $episodeRepository): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodeRepository->save($episode, true);

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_episode_delete', methods: ['POST'])]
    public function delete(Request $request, Episode $episode, EpisodeRepository $episodeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $episodeRepository->remove($episode, true);
        }

        return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
    }
}
