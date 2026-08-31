<?php

namespace App\Controller;

use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(OfferRepository $offerRepository): Response
    {
        $latestOffers = $offerRepository->findBy(
            ['isActive' => true],
            ['createdAt' => 'DESC'],
            5
        );

        return $this->render('home/index.html.twig', [
            'offers' => $latestOffers,
        ]);
    }
}
