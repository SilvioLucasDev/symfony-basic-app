<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/events', name: 'event_')]
class EventController extends AbstractController
{
    public function __construct(
        private EventRepository $eventRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $events = $this->eventRepository->findAll();
        $jsonEvents = $this->serializer->serialize($events, 'json');

        return new JsonResponse($jsonEvents, Response::HTTP_OK, [], true);
    }

    #[Route('/store', name: 'store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'JSON inválido'], Response::HTTP_BAD_REQUEST);
        }

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleTransaction(function () use ($event) {
                $this->entityManager->persist($event);
                $this->entityManager->flush();

                return new JsonResponse(['message' => 'Evento criado com sucesso'], Response::HTTP_CREATED);
            });
        }

        return $this->createValidationErrorResponse($form);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            return new JsonResponse(['error' => 'Evento não encontrado'], Response::HTTP_NOT_FOUND);
        }

        $jsonEvent = $this->serializer->serialize($event, 'json');

        return new JsonResponse($jsonEvent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH', 'PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'JSON inválido'], Response::HTTP_BAD_REQUEST);
        }

        $event = $this->eventRepository->find($id);
        if (!$event) {
            return new JsonResponse(['error' => 'Evento não encontrado'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(EventType::class, $event);
        $form->submit($data, false);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleTransaction(function () {
                $this->entityManager->flush();

                return new JsonResponse(['message' => 'Evento atualizado com sucesso'], Response::HTTP_OK);
            });
        }

        return $this->createValidationErrorResponse($form);
    }

    #[Route('/{id}', name: 'destroy', methods: ['DELETE'])]
    public function destroy(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            return new JsonResponse(['error' => 'Evento não encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleTransaction(function () use ($event) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Evento removido com sucesso'], Response::HTTP_OK);
        });
    }

    private function handleTransaction(callable $function): JsonResponse
    {
        try {
            $this->entityManager->beginTransaction();
            $response = $function();
            $this->entityManager->commit();

            return $response;
        } catch (\Exception $e) {
            $this->entityManager->rollBack();

            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createValidationErrorResponse($form): JsonResponse
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $field = $error->getOrigin()->getName();
            $errors[$field][] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}
