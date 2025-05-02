<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddProductForm;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Flex\Response as FlexResponse;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController {

    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(ProductRepository $productRepo, Request $request): Response
    {   
        $searchTermProduct = $request->query->get("p");
        // "p" is for products tab; other tabs might also have searchbars, they will use different keywords
        $products = $searchTermProduct
            ? $productRepo->findByNameLike($searchTermProduct)
            : $productRepo->findAll();
        return $this->render('admin/dashboard.html.twig', [
            'products' => $products,
            'p' => $searchTermProduct
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

        // I am using the same form for creating and updating products from dashboard.
        // Note to self: refactor AddProductForm to reflect this

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
    public function update(int $id, EntityManagerInterface $em, Request $request, ProductRepository $repo): Response
    {
        $product = $repo->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(AddProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success','Product updated');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/_product_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path:'/admin/product/create', name:'admin_product_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form = $this->createForm(AddProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Product created');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/_product_form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}