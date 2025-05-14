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
    /**
     * Renders main page
     * 
     * @param ProductRepository $productRepo Product repository for filtering products
     * @param Request $request
     * 
     * @Route("/", name="shop_home")
     * 
     * @return Response The rendered index page
     */
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

    /**
     * Renders product details page
     * 
     * @param ProductRepository $productRepo Product repository for finding product
     * @param Request $request Request with product ID
     * 
     * @Route("/product/{id}", name="shop_product_show")
     * 
     * @return Response The rendered product page
     */
    #[Route(path: '/product/{id}', name: 'shop_product_show')]
    public function showProduct(ProductRepository $repo, Request $request, int $id): Response
    {
        $product = $repo->find($id);
        return $this->render('shop/product.html.twig', [
            'product'=> $product
        ]);
    }


    /**
     * Displays form for product purchase
     * 
     * @param ProductRepository $repo Product repository for finding product
     * @param string $stringID ID of Product to be bought
     * 
     * @Route("/product/{stringID}/purchase-form", name="shop_purchase_form")
     * 
     * @return Response Rendered purchase form with Product data
     */
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


    /**
     * Creates Purchase from form data and adds to DB
     * 
     * @param int $id ID of Product to be purchased
     * @param ProductRepository $repo Product repository to find Product
     * @param Security $security Security to get logged user as buyer
     * @param Request $request 
     * @param EntityManagerInterface $em
     * 
     * @Route("/product/{id}/purchase-submit", name="shop_purchase_submit", methods=["POST"])
     * 
     * @return Response Flash and redirect if purchase successful
     */
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


    /**
     * Displays form for renting
     * 
     * @param string $stringID
     * @param ProductRepository $repo
     * 
     * @Route("/product/{stringID}/rental-form", name="shop_rental_form")
     * 
     * @return Response Rendered form with Product data
     */
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

    /**
     * Creates Rental from form data and adds to DB
     * 
     * Handles date check, calculates buyout cost and rent cost, changes stock, sets status to ongoing.
     * 
     * @param int $id ID of Product to be rented
     * @param ProductRepository $repo Product repository to find Product
     * @param Security $security Security to get logged user as buyer
     * @param Request $request
     * @param EntityManagerInterface $em
     * 
     * @Route("/product/{id}/rental-submit", name="shop_rental_submit", methods=["POST"])
     * 
     * @return Response Flash and redirect if Renting successful
     */
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
            $days = (int) $interval->format('%a'); //Number of whole days

            // End date must not be before start
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

    /**
     * Displays current user's basket page
     * 
     * @param Security $security Security to get logged user
     * @param RentalRepository $rentalRepo Rental repository to find user's rentals
     * @param PurchaseRepository $purchaseRepo Purchase repository to find user's purchases
     * 
     * @Route("/account/myOrders", name="shop_my_orders")
     * 
     * @return Response Rendered basket (shop/my_orders.html.twig) with user's rentals and purchases data
     */
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


    /**
     * Returns product name
     * 
     * @Route("/product/{id}/name", name="get_product_name")
     * 
     * @param int $id ID of Product
     * @param ProductRepository $repo Repository to find Product
     * 
     * @return Response Product name
     */
    #[Route(path:'/product/{id}/name', name:'get_product_name')]
    public function getProductName(
        ProductRepository $repo,
        int $id
    ): Response {
        $product = $repo->find($id);
        return new Response($product->getName());
    }

    /**
     * Updates end date on a Rental
     * 
     * New end date must be later than the old end date.
     * 
     * @param int $id   ID of Rental to be updated
     * @param RentalRepository $repo    Rental repository to find the Rental
     * @param Request $request
     * @param EntityManagerInterface $em
     * 
     * @Route("/rental/{id}/extend", name="rental_extend", methods=["POST"])
     * 
     * @return JsonResponse Error or confirmation JSON
     */
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