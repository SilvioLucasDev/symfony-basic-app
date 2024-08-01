<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Repository\EventRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/events', name: 'admin_event_')]
class EventController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/store', name: 'store', methods: ['POST'])]
    public function store(EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->getConnection()->beginTransaction();

            $timeZone = new DateTimeZone('America/Sao_Paulo');

            $event = new Event();
            $event->setTitle('Evento Teste');
            $event->setDescription('Descrição do evento teste');
            $event->setBody('Corpo do evento teste');
            $event->setSlug('evento-teste');
            $event->setStartDate(new DateTime('2024-06-01 08:00:00', $timeZone));
            $event->setEndDate(new DateTime('2024-06-01 18:00:00', $timeZone));
            $event->setCreatedAt(new DateTimeImmutable('now', $timeZone));
            $event->setUpdatedAt(new DateTimeImmutable('now', $timeZone));

            $entityManager->persist($event);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return new Response('Evento criado com sucesso');
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollBack();

            return new Response($e->getMessage());
        }
    }

    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        try {
            $entityManager->getConnection()->beginTransaction();

            $timeZone = new DateTimeZone('America/Sao_Paulo');

            $event = $eventRepository->find(1);
            if (!$event) {
                throw $this->createNotFoundException('Evento não encontrado');
            }

            $event->setTitle('Evento Teste Atualizado');
            $event->setUpdatedAt(new DateTimeImmutable('now', $timeZone));

            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return new Response('Evento atualizado com sucesso');
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollBack();

            return new Response($e->getMessage());
        }
    }

    #[Route('/remove', name: 'remove', methods: ['DELETE'])]
    public function remove(EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        try {
            $entityManager->getConnection()->beginTransaction();

            $event = $eventRepository->find(1);
            if (!$event) {
                throw $this->createNotFoundException('Evento não encontrado');
            }

            $entityManager->remove($event);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return new Response('Evento removido com sucesso');
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollBack();

            return new Response($e->getMessage());
        }
    }
}
