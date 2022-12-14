<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinsType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('pins/index.html.twig', compact('pins'));
    }

    #[Route('/pins/create', name: 'app_pins_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;
        $form = $this->createForm(PinsType::class, $pin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            $pin = $form->getData();

            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin successfully created !');

            return $this->redirectToRoute('app_home');
        }


        return $this->render('pins/create.html.twig', [

            'form' => $form->createView()
        ]);
    }


    #[Route('/pins/{id<[0-9]+>}', name: 'app_pins_show', methods: ['GET'])]
    public function show(Pin $pin): Response
    {
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    #[Route('/pins/edit/{id<[0-9]+>}', name: 'app_pins_edit', methods: ['GET', 'PUT'])]
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PinsType::class, $pin, [
            'method' => 'PUT'

        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Pin successfully updated !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()

        ]);
    }

    #[Route('/pins/delete/{id<[0-9]+>}', name: 'app_pins_delete', methods: ['DELETE'])]
    public function delete(Pin $pin, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('pin_deletion_' . $pin->getId(), $request->request->get('csrf_token'))) {

            $em->remove($pin);
            $em->flush();

            $this->addFlash('info', 'Pin successfully deleted !');
        }


        return $this->redirectToRoute('app_home');
    }
}

