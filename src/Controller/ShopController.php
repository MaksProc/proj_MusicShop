<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\Rental;
use App\Enum\RentalStatus;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\MakePurchaseForm;
use App\Form\MakeRentalForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

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

    #[Route(path: '/product/{id}', name: 'shop_product_show')]
    public function showProduct(ProductRepository $repo, Request $request, int $id): Response
    {
        $product = $repo->find($id);
        return $this->render('shop/product.html.twig', [
            'product'=> $product
        ]);
    }

    #[Route(path:'/product/{stringID}/purchase-form', name:'shop_purchase_form')]
    public function purchaseForm(
        ProductRepository $repo, 
        string $stringID
    ): Response {
        $purchase = new Purchase();
        $form = $this->createForm(MakePurchaseForm::class, $purchase);

        $id = intval($stringID);

        $product = $repo->find($id);

        return $this->render('shop/purchase_form.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }


    #[Route('/product/{id}/purchase-submit', name:'shop_purchase_submit', methods: ['POST'])]
    public function submitPurchase(
        ProductRepository $repo,
        Request $request,
        int $id,
        EntityManagerInterface $em,
        Security $security
    ): Response {
        $product = $repo->find($id);
        $purchase = new Purchase();

        $form = $this->createForm(MakePurchaseForm::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchase->setUserID($security->getUser());
            $purchase->setProductID($product);
            $purchase->setTimestamp(new \DateTime());
            $purchase->setAmount(
                $product->getBasePrice() * $purchase->getQuantity()
            );

            $em->persist($purchase);
            $em->flush();

            $this->addFlash('success', 'Purchase complete!');
            return $this->redirectToRoute('shop_home');
        }

        return $this->render('shop/purchase_form.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }



    #[Route(path:'/product/{stringID}/rental-form', name:'shop_rental_form')]
    public function rentalForm(
        ProductRepository $repo, 
        string $stringID
    ): Response {
        $rental = new Rental();
        $form = $this->createForm(MakeRentalForm::class, $rental);

        $id = intval($stringID);

        $product = $repo->find($id);

        return $this->render('shop/rental_form.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }


    #[Route('/product/{id}/rental-submit', name:'shop_rental_submit', methods: ['POST'])]
    public function submitRental(
        ProductRepository $repo,
        Request $request,
        int $id,
        EntityManagerInterface $em,
        Security $security
    ): Response {
        $product = $repo->find($id);
        $rental = new Rental();

        $form = $this->createForm(MakeRentalForm::class, $rental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rental->setUserID($security->getUser());
            $rental->setProductID($product);

            $interval = $rental->getStartTimestamp()->diff($rental->getEndTimestamp());
            $days = (int) $interval->format('%a'); //Całkowita liczba dni
            $rental->setAmount($days * $product->getBaseRentPerDay());

            //  Sprawdź, aby $diff nie był ujemny

            $rental->setBuyoutCost( $rental->getAmount() > $product->getBasePrice()/2 ?
                $product->getBasePrice() /2 :
                $product->getBasePrice() - $rental->getAmount()
            );
            $rental->setStatus(RentalStatus::ONGOING);

            $em->persist($rental);
            $em->flush();

            $this->addFlash('success', 'Rent started!');
            return $this->redirectToRoute('shop_home');
        }

        return $this->render('shop/rental_form.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }
}