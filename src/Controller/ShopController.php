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
use App\Repository\PurchaseRepository;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ShopController extends AbstractController
{
    
    #[Route(path: "/", name: "shop_home")]
    public function index(ProductRepository $productRepo, Request $request): Response
    {
        $searchTerm = $request->query->get("search");
        $minPrice = $request->query->get("min_price");
        $maxPrice = $request->query->get("max_price");
        $type = $request->query->get("type");

        $products = $productRepo->filterProducts($searchTerm, $minPrice, $maxPrice, $type);

        return $this->render('shop/index.html.twig', [
            'products' => $products,
            'search' => $searchTerm,
            'minPrice'=> $minPrice,
            'maxPrice'=> $maxPrice,
            'type'=> $type
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
            if ($purchase->getQuantity() > $product->getStock()) {
                return $this->redirectToRoute('shop_product_show', ['id'=> $id]);
            }


            $purchase->setUserID($security->getUser());
            $purchase->setProductID($product);
            $purchase->setTimestamp(new \DateTime());
            $purchase->setAmount(
                $product->getBasePrice() * $purchase->getQuantity()
            );

            $em->persist($purchase);
            $em->flush();

            $product->setStock($product->getStock() - $purchase->getQuantity());

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

            // Koniec musi być nie wcześniej od początku
            if ($days <= 0) {
                return $this->redirectToRoute('shop_product_show', ['id'=> $id]);
            }

            $rental->setAmount($days * $product->getBaseRentPerDay());

            $rental->setBuyoutCost( $rental->getAmount() > $product->getBasePrice()/2 ?
                $product->getBasePrice() /2 :
                $product->getBasePrice() - $rental->getAmount()
            );
            $rental->setStatus(RentalStatus::ONGOING);

            $em->persist($rental);
            $em->flush();

            $product->setStock($product->getStock() - 1);

            $this->addFlash('success', 'Rent started!');
            return $this->redirectToRoute('shop_home');
        }

        return $this->render('shop/rental_form.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/account/myOrders', name:'shop_my_orders')]
    public function renderCurrentUserOrders(
        Security $security,
        RentalRepository $rentalRepo,
        PurchaseRepository $purchaseRepo
    ): Response
    {
        $user = $security->getUser();
        $rentalOrders = $rentalRepo->findBy(['userID' => $user]);
        $purchaseOrders = $purchaseRepo->findBy(['userID'=> $user]);

        return $this->render('shop/my_orders.html.twig', [
            'rentals' => $rentalOrders,
            'purchases' => $purchaseOrders
        ]);
    }


    #[Route(path:'/product/{id}/name', name:'get_product_name')]
    public function getProductName(
        ProductRepository $repo,
        int $id
    ): Response {
        $product = $repo->find($id);
        return new Response($product->getName());
    }

    #[Route('/rental/{id}/extend', name: 'rental_extend', methods: ['POST'])]
    public function extendRental(
        int $id,
        Request $request,
        RentalRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $rental = $repo->find($id);
        $data = json_decode($request->getContent(), true);

        $newEndDate = new \DateTime($data['new_end_date'] ?? '');
        if ($newEndDate <= $rental->getEndTimestamp()) {
            return new JsonResponse(['error' => 'Data musi być póżniejsza'], 400);
        }

        $rental->setEndTimestamp($newEndDate);

        $days = $rental->getStartTimestamp()->diff($newEndDate)->days;
        $rate = $rental->getProductID()->getBaseRentPerDay();
        $rental->setAmount($days * $rate);

        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }

}