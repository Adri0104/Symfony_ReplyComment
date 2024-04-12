<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAll();
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parentId = $form->get("parentId")->getData();

            if($parentId != null){
                $parent = $entityManager->getRepository(Comment::class)->find($parentId);
            }

            $comment->setParent($parent ?? null);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'comments' => $comments,
        ]);
    }
}
