<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ShopController extends AbstractController
{
    
    #[Route(path: "/", name: "shop_home")]
    public function index(ProductRepository $productRepo, Request $request): Response
    {
        $searchTerm = $request->query->get("search");
        $products = $searchTerm
            ? $productRepo->findByNameLike($searchTerm)
            : $productRepo->findAll();

        return $this->render('shop/index.html.twig', [
            'products' => $products,
            'search' => $searchTerm
        ]);
    }
}