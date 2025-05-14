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
use Symfony\Bundle\SecurityBundle\Security;



#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController {

    /**
     * @Route("/admin", name="admin_dashboard")
     * 
     * Renders dashboard page with products tab open.
     * 
     * @param Request $request Request and form data
     * @param ProductRepository $productRepo Product repositorium for DB search
     * @param UserRepository $userRepo User repositorium for DB search
     * @param RentalRepository $rentalRepo Rental repositorium for DB search
     */
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(
        Request $request, 
        ProductRepository $productRepo, 
        UserRepository $userRepo,
        RentalRepository $rentalRepo, ): Response
    {   
        // "p" is for Products; Other tabs might have their own search results
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

    /**
     * @Route("/admin/product/{id}/delete", name="admin_product_delete")
     * 
     * Deletes the passed product from DB.
     * 
     * @param Product $product Product to be deleted
     * @param EntityManagerInterface $em EntityManager for DB operation
     */
    #[Route('/admin/product/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function delete(Product $product, EntityManagerInterface $em)
    {
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Product deleted!');
        return $this->redirectToRoute('admin_dashboard');
    }


    /**
     * Renders the product form for creating or editing a product.
     * 
     * If an ID is provided, the method retrieves the corresponding Product entity
     * from the database for editing. If no ID is provided, a new Product entity
     * is created for the form.
     * 
     * The form action is dynamically generated based on whether the product is 
     * being created or updated.
     * 
     * @Route("/admin/product-form/{id}", name="admin_product_form", defaults={"id"=null})
     * 
     * @param int|null $id The ID of the product to edit, or null for a new product
     * @param ProductRepository $repo The repository for retrieving products
     * @param Request $request The current HTTP request
     * 
     * @return Response The rendered product form
     */
    #[Route(path: '/admin/product-form/{id}', name: 'admin_product_form', defaults: ['id' => null])]
    public function productForm(?int $id, ProductRepository $repo, Request $request): Response
    {
        $product = $id ? $repo->find($id) : new Product();

        // "AddProductForm" is used for both product updates and creation.

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


    /**
     * Updates product info, called on "admin_product_form" submission.
     * 
     * Updates data to field inputs. Saves the new image in public/uploads/products if added,
     * with appropriate filepath being saved to DB.
     * 
     * @param int $id The ID of product to be updated
     * @param EntityManagerInterface $em EntityManager for DB operations
     * @param Request $request Request with form data
     * @param ProductRepository $repo Product repository for checking if product exists
     * @param Filesystem $filesystem Filesystem for saving product image
     * @param SluggerInterface $slugger Slugger for image filepath operations
     * 
     * @Route("/admin/product/{id}/update", name="admin_product_update", methods=["POST"])
     * 
     * @return Response Form view; flash and redirect at form submission.
     */
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
                // 1. Delete old file, if exists
                $oldImagePath = $this->getParameter('product_images_directory') . '/' . $product->getImagePath();
                if ($product->getImagePath() && $filesystem->exists($oldImagePath)) {
                    $filesystem->remove($oldImagePath);
                }

                // 2. Save new file
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

                // 3. Save file path to the product
                $product->setImagePath($this->normalizeImagePath($newFilename));
            }

            $em->flush();

            $this->addFlash('success','Product updated');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/_product_form.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Creates a new product on product form submission.
     * 
     * Saves new image to public/uploads/products if added, saves appropriate image filepath to DB.
     * 
     * @param Request $request Request with form data
     * @param EntityManagerInterface $em EntityManager for DB operations
     * @param SluggerInterface $slugger Slugger for filepath operations
     * 
     * @Route("/admin/product/create", name="admin_product_create", methods=["POST"])
     * 
     * @return Response Form view; Flash and redirect if successful
     */
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
                    // error message not implemented
                }

                $product->setImagePath($this->normalizeImagePath($newFilename));
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


    /**
     * Renders user role change form
     * 
     * @param int $id ID of user to be updated
     * @param UserRepository $repo User repository to find user
     * 
     * @return Response Form view
     */
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


    /**
     * Changes user roles to ones set in form
     * 
     * User cannot change their own roles. Note User->getRoles returns client role if empty; Giving or 
     * taking away user client role makes no difference.
     * 
     * @param int $id
     * @param UserRepository $repo
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Security $security
     * 
     * @Route("/admin/user/updateRole/{id}", name="admin_user_role_update", methods=["POST"])
     * 
     * @return Response 
     */
    #[Route(path:'/admin/user/updateRole/{id}', name:'admin_user_role_update', methods: ['POST'])]
    public function userRoleUpdate(
        int $id, 
        UserRepository $repo, 
        Request $request,
        EntityManagerInterface $em,
        Security $security): Response
    {
        $user = $repo->find($id);
        $form = $this->createForm(UserRoleChangeFormTypeForm::class, $user);
        $form->handleRequest($request);
        $currentUser = $security->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            
            if ($currentUser && $currentUser->getId() === $user->getId()) {
                $this->addFlash('error','You cannot change your own roles.');
                return $this->redirectToRoute('admin_dashboard');
            }

            $em->flush();

            $this->addFlash('success','User role updated');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/_user_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * Changes status of given Rental
     * 
     * Used to show change without reload.
     * 
     * @param int $id ID of Rental to be changed
     * @param Request $request Request with form data
     * @param RentalRepository $rentalRepo Rental repository to find Rental
     * @param EntityManagerInterface $em
     * 
     * @Route("/admin/rental/{id}/change-status", name="admin_rental_status_change", methods=["POST"])
     * 
     * @return JsonResponse Error or confirmation JSON
     */
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

    private function normalizeImagePath(string $filename): string
    {
        return str_starts_with($filename, 'uploads/products/')
            ? $filename
            : 'uploads/products/' . $filename;
    }

}