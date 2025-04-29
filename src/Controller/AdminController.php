<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddProductForm;
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
    #[Route('/admin', name:'admin_dashboard')]
    public function admin_dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/admin/product/new', name: 'admin_product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {   
        $product = new Product();
        $form = $this->createForm(AddProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product created!');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/product_new.html.twig', [
            'form' => $form->createView()
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
}