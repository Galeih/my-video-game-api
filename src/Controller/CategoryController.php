<?php
// src/Controller/CategoryController.php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/categories')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'category_list', methods: ['GET'])]
    public function list(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();
        return $this->json($categories);
    }

    #[Route('/{id}', name: 'category_show', methods: ['GET'])]
    public function show(Category $category): JsonResponse
    {
        return $this->json($category);
    }

    #[Route('', name: 'category_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $jsonData = $request->getContent();
        try {
            /** @var Category $category */
            $category = $serializer->deserialize($jsonData, Category::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        $errors = $validator->validate($category);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $em->persist($category);
        $em->flush();
        return $this->json($category, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'category_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Category $category, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $jsonData = $request->getContent();
        try {
            $serializer->deserialize($jsonData, Category::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $category]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        $errors = $validator->validate($category);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $em->flush();
        return $this->json($category);
    }

    #[Route('/{id}', name: 'category_delete', methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $em->remove($category);
        $em->flush();
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
