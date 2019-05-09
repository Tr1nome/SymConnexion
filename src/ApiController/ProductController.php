<?php

namespace App\ApiController;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/product", host="api.connexion.fr")
 */
class ProductController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="product_api",
     * )
     * @Rest\View()
     */
    public function index(ProductRepository $productRepository): View
    {
        $products = $productRepository->findAll();
        return View::create($products, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="productshow_api",
     * )
     * @Rest\View()
     */
    public function show(Product $product): View
    {
        return View::create($product, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     * path = "/new",
     * name="productnew_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request): View
    {
        $product = new Product();
        $product->setName($request->get('name'));
        $product->setPrice($request->get('price'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        return View::create($product, Response::HTTP_CREATED);
        }

    /**
     * @Rest\Put(
     * path = "/{id}",
     * name="productedit_api",
     * )
     * @Rest\View()
     */
    public function edit(Request $request,Product $product): View
    {
        if($product) {
            $product->setName($request->get('name'));
            $product->setPrice($request->get('price'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }
            return View::create($product, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Patch(
     * path = "/{id}",
     * name="productpatch_api",
     * )
     * @Rest\View()
     */
    public function patch(Request $request,Product $product): View
    {
        if($product) {
            $form = $this->createForm(ProductType::class, $product);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }
        return View::create($product, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="productdelete_api",
     * )
     * @Rest\View()
     */
    public function delete(Product $product): View
    {
        if ($product) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return View::create([], Response::HTTP_NO_CONTENT);
    }

}