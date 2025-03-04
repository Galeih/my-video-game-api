<?php
// src/Controller/EditorController.php

namespace App\Controller;

use App\Entity\Editor;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/editors')]
class EditorController extends AbstractController
{
    #[Route('', name: 'editor_list', methods: ['GET'])]
    public function list(EditorRepository $editorRepository): JsonResponse
    {
        $editors = $editorRepository->findAll();
        return $this->json($editors);
    }

    #[Route('/{id}', name: 'editor_show', methods: ['GET'])]
    public function show(Editor $editor): JsonResponse
    {
        return $this->json($editor);
    }

    #[Route('', name: 'editor_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $jsonData = $request->getContent();
        try {
            /** @var Editor $editor */
            $editor = $serializer->deserialize($jsonData, Editor::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        $errors = $validator->validate($editor);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $em->persist($editor);
        $em->flush();
        return $this->json($editor, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'editor_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Editor $editor, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $jsonData = $request->getContent();
        try {
            $serializer->deserialize($jsonData, Editor::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $editor]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        $errors = $validator->validate($editor);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $em->flush();
        return $this->json($editor);
    }

    #[Route('/{id}', name: 'editor_delete', methods: ['DELETE'])]
    public function delete(Editor $editor, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $em->remove($editor);
        $em->flush();
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
