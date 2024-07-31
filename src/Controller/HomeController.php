<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        $posts = $this->getPosts();
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/{slug?}', name: 'app_show', methods: ['GET'])]
    public function show(string $slug = null): Response
    {
        $posts = $this->getPosts();
        $post = array_filter($posts, fn ($post) => $post['slug'] === $slug);
        $title = $post[0]['title'] ?? 'Post não encontrado';

        return $this->render('home/show.html.twig', [
            'title' => $title,
        ]);
    }

    private function getPosts(): array
    {
        return [
            ['id' => 1, 'title' => 'Primeiro Post', 'slug' => 'first-post'],
            ['id' => 2, 'title' => 'Segundo Post', 'slug' => 'second-post'],
            ['id' => 3, 'title' => 'Terceiro Post', 'slug' => 'third-post'],
            ['id' => 4, 'title' => 'Quarto Post', 'slug' => 'fourth-post'],
            ['id' => 5, 'title' => 'Quinto Post', 'slug' => 'fifth-post'],
        ];
    }
}
