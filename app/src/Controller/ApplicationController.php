<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Offer;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ApplicationController extends AbstractController
{
    #[Route('/apply/{id}', name: 'app_apply', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function apply(Offer $offer, Request $request, EntityManagerInterface $em, ApplicationRepository $applicationRepository): Response
    {
        $user = $this->getUser();

        $existing = $applicationRepository->findOneBy([
            'candidate' => $user,
            'offer' => $offer,
        ]);

        if ($existing) {
            $this->addFlash('warning', 'Vous avez déjà postulé à cette offre.');
            return $this->redirectToRoute('app_offer_show', ['id' => $offer->getId()]);
        }

        if ($request->isMethod('POST')) {
            $application = new Application();
            $application->setCandidate($user);
            $application->setOffer($offer);
            $application->setCoverLetter($request->request->get('cover_letter'));

            $em->persist($application);
            $em->flush();

            $this->addFlash('success', 'Votre candidature a été envoyée !');
            return $this->redirectToRoute('app_my_applications');
        }

        return $this->render('application/apply.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/my-applications', name: 'app_my_applications')]
    #[IsGranted('ROLE_USER')]
    public function myApplications(ApplicationRepository $applicationRepository): Response
    {
        $applications = $applicationRepository->findBy(
            ['candidate' => $this->getUser()],
            ['appliedAt' => 'DESC']
        );

        return $this->render('application/index.html.twig', [
            'applications' => $applications,
        ]);
    }
}
