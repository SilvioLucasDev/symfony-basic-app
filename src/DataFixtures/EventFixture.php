<?php

namespace App\DataFixtures;

use App\Entity\Event;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
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

        $manager->persist($event);
        $manager->flush();
    }
}
