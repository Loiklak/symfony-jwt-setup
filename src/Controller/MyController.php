<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    #[Route('/secure', name: 'secure')]
    public function getSecureInfos(): Response
    {
      return new JsonResponse(['message' => 'You have access to this restricted area!']);
    }
}
