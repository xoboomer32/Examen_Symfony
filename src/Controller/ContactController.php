<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\Contact1Type;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact')]
final class ContactController extends AbstractController
{
    #[Route(name: 'app_contact_index', methods: ['GET'])]
    public function index(Request $request, ContactRepository $contactRepository): Response
    {
        $search = $request->query->get('search');
        $sort = strtoupper($request->query->get('sort', 'ASC'));
        if (!in_array($sort, ['ASC', 'DESC'])) {
            $sort = 'ASC';
        }
        $contacts = $contactRepository->findByNomSearchAndSort($search, $sort);
        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    #[Route('/suggest', name: 'app_contact_suggest', methods: ['GET'])]
    public function suggest(Request $request, ContactRepository $contactRepository): Response
    {
        $search = $request->query->get('q');
        $contacts = $contactRepository->findByNomSearchAndSort($search, 'ASC');
        $results = [];
        foreach ($contacts as $contact) {
            $results[] = [
                'id' => $contact->getId(),
                'contact' => $contact->getContact(),
                'nom' => $contact->getNom(),
                'numero' => $contact->getNumero(),
                'email' => $contact->getEmail(),
            ];
        }
        return $this->json($results);
    }

    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(\App\Form\ContactCreateType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Remplir nom avec contact si nom n'est pas renseigné
            if (method_exists($contact, 'setNom') && (null === $contact->getNom() || $contact->getNom() === '')) {
                $contact->setNom($contact->getContact());
            }
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Contact1Type::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
