<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\SensitiveNote;
use App\Entity\User;
use App\Repository\SensitiveNoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SensitiveNoteController extends AbstractController
{
    #[Route('/sensitive-notes', name: 'sensitive_note_index', methods: ['GET'])]
    public function index(Security $security, SensitiveNoteRepository $sensitiveNoteRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('sensitive_notes/index.html.twig', [
            'notes' => $sensitiveNoteRepository->findLatestByUser($currentUser),
        ]);
    }

    // ⚠️ Route volontairement vulnérable pour le challenge Base64
    #[Route('/sensitive-notes', name: 'sensitive_note_store', methods: ['POST'])]
    public function store(Request $request, Security $security, EntityManagerInterface $entityManager): RedirectResponse
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $title = trim($request->request->getString('title'));
        $value = trim($request->request->getString('value'));

        if ($title === '' || $value === '') {
            $this->addFlash('error', 'Le type de donnée et la valeur sont requis.');

            return $this->redirectToRoute('sensitive_note_index');
        }

        // ⚠️ VULNÉRABLE — Cryptographic Failure : base64_encode() transforme le format mais ne protège pas la donnée.
        $note = (new SensitiveNote())
            ->setUser($currentUser)
            ->setTitle($title)
            ->setEncodedValue(base64_encode($value));

        $entityManager->persist($note);
        $entityManager->flush();

        return $this->redirectToRoute('sensitive_note_show', ['id' => $note->getId()]);
    }

    #[Route('/sensitive-notes/{id}', name: 'sensitive_note_show', methods: ['GET'])]
    public function show(SensitiveNote $note, Security $security): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User || $note->getUser() !== $currentUser) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('sensitive_notes/show.html.twig', [
            'note' => $note,
        ]);
    }

    #[Route('/sensitive-notes/{id}/decode', name: 'sensitive_note_decode', methods: ['POST'])]
    public function decode(SensitiveNote $note, Security $security): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User || $note->getUser() !== $currentUser) {
            throw $this->createAccessDeniedException();
        }

        $decoded = base64_decode($note->getEncodedValue(), true);

        return $this->render('sensitive_notes/show.html.twig', [
            'note' => $note,
            'decoded_value' => $decoded === false ? 'Décodage impossible.' : $decoded,
        ]);
    }
}
