<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddProductForm;
use App\Form\UserRoleChangeFormTypeForm;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\RentalRepository;
use App\Enum\RentalStatus;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Flex\Response as FlexResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;



#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController {

    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(
        Request $request, 
        ProductRepository $productRepo, 
        UserRepository $userRepo,
        RentalRepository $rentalRepo, ): Response
    {   
        // "p" dla Products; Inne wkładki mają własne wyszukiwarki
        $searchTermProduct = $request->query->get("p");
        $products = $searchTermProduct
            ? $productRepo->findByNameLike($searchTermProduct)
            : $productRepo->findAll();


        $users = $userRepo->findAll();
        $rentals = $rentalRepo->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'products' => $products,
            'p' => $searchTermProduct,
            'users' => $users,
            'u' => '',
            'rentals' => $rentals,
        ]);
    }


    #[Route('/admin/product/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function delete(Product $product, EntityManagerInterface $em)
    {
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Product deleted!');
        return $this->redirectToRoute('admin_dashboard');
    }



    #[Route(path: '/admin/product-form/{id}', name: 'admin_product_form', defaults: ['id' => null])]
    public function productForm(?int $id, ProductRepository $repo, Request $request): Response
    {
        $product = $id ? $repo->find($id) : new Product();

        // Używam ten sam formularz dla tworzenia nowych produktów i zmiany starych.
        // Zapis dla siebie: zmień nazwę AddProductForm aby odpowiadała temu przeznaczeniu

        $form = $this->createForm(AddProductForm::class, $product, [
            'action' => $id ? 
                $this->generateUrl('admin_product_update', ['id'=> $id]) :
                $this->generateUrl('admin_product_create'),
            'method' => 'POST'
        ]);

        return $this->render('admin/_product_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route(path:'/admin/product/{id}/update', name:'admin_product_update', methods: ['POST'])]
    public function update(
        int $id, 
        EntityManagerInterface $em, 
        Request $request, 
        ProductRepository $repo,
        SluggerInterface $slugger,
        Filesystem $filesystem
        ): Response
    {
        $product = $repo->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(AddProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                // 1. Usuń stary plik, jeśli istnieje
                $oldImagePath = $this->getParameter('product_images_directory') . '/' . $product->getImagePath();
                if ($product->getImagePath() && $filesystem->exists($oldImagePath)) {
                    $filesystem->remove($oldImagePath);
                }

                // 2. Zapisz nowy plik
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('product_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Nie udało się przesłać obrazu.');
                    return $this->redirectToRoute('admin_dashboard');
                }

                // 3. Zapisz ścieżkę do nowego pliku
                $product->setImagePath($newFilename);
            }

            $em->flush();

            $this->addFlash('success','Product updated');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/_product_form.html.twig', [
            'form' => $form->createView()
        ]);
    }



    #[Route(path:'/admin/product/create', name:'admin_product_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger,): Response
    {
        $product = new Product();
        $form = $this->createForm(AddProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('product_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // tu miałby być jakiś error message dla formularza?
                }

                $product->setImagePath('uploads/products/' . $newFilename);
            }


            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Product created');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/_product_form.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route(path: '/admin/user/role-form/{id}', name:'admin_user_role_form')]
    public function userForm(int $id, UserRepository $repo) {
        $user = $repo->find($id);

        $form = $this->createForm(UserRoleChangeFormTypeForm::class, $user, [
            'action' => 
                $this->generateUrl('admin_user_role_update', ['id'=> $id]),
            'method' => 'POST'
        ]);

        return $this->render('admin/_user_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route(path:'/admin/user/updateRole/{id}', name:'admin_user_role_update', methods: ['POST'])]
    public function userRoleUpdate(
        int $id, 
        UserRepository $repo, 
        Request $request,
        EntityManagerInterface $em): Response
    {
        $user = $repo->find($id);
        $form = $this->createForm(UserRoleChangeFormTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // Add check so admin can't demote themselves

            $this->addFlash('success','User role updated');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/_user_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/rental/{id}/change-status', name: 'admin_rental_status_change', methods: ['POST'])]
    public function changeStatus(
        int $id,
        Request $request,
        RentalRepository $rentalRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $rental = $rentalRepo->find($id);
        $data = json_decode($request->getContent(), true);

        if (!$rental || $rental->getStatus() !== RentalStatus::ONGOING) {
            return new JsonResponse(['error' => 'Invalid rental'], 400);
        }

        try {
            $rental->setStatus(RentalStatus::from($data['new_status']));
            $em->flush();
        } catch (\ValueError $e) {
            return new JsonResponse(['error' => 'Invalid status'], 400);
        }

        return new JsonResponse(['status' => 'ok']);
    }

}