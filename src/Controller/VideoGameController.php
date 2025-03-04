<?php
// src/Controller/VideoGameController.php

namespace App\Controller;

use App\Entity\VideoGame;
use App\Repository\VideoGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/videogames')]
class VideoGameController extends AbstractController
{
    #[Route('', name: 'video_game_list', methods: ['GET'])]
    public function list(VideoGameRepository $videoGameRepository): JsonResponse
    {
        $games = $videoGameRepository->findAll();
        return $this->json($games);
    }

    #[Route('/{id}', name: 'video_game_show', methods: ['GET'])]
    public function show(VideoGame $videoGame): JsonResponse
    {
        return $this->json($videoGame);
    }

    #[Route('', name: 'video_game_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $jsonData = $request->getContent();
        try {
            /** @var VideoGame $videoGame */
            $videoGame = $serializer->deserialize($jsonData, VideoGame::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        $errors = $validator->validate($videoGame);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $em->persist($videoGame);
        $em->flush();
        return $this->json($videoGame, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'video_game_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, VideoGame $videoGame, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $jsonData = $request->getContent();
        try {
            // Désérialisation dans l'objet existant
            $serializer->deserialize($jsonData, VideoGame::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $videoGame]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        $errors = $validator->validate($videoGame);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $em->flush();
        return $this->json($videoGame);
    }

    #[Route('/{id}', name: 'video_game_delete', methods: ['DELETE'])]
    public function delete(VideoGame $videoGame, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $em->remove($videoGame);
        $em->flush();
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
