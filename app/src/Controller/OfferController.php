<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offers')]
class OfferController extends AbstractController
{
    #[Route('', name: 'app_offer_index')]
    public function index(OfferRepository $offerRepository): Response
    {
        $offers = $offerRepository->findBy(
            ['isActive' => true],
            ['createdAt' => 'DESC']
        );

        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_show', requirements: ['id' => '\d+'])]
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }
}
