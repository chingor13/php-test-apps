<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PostsController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Posts');
        $posts = $repository->findAll();

        return $this->render('posts/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/posts/{permalink}", name="posts.show")
     */
    public function showAction($permalink)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Posts');
        $post = $repository->findOneByPermalink($permalink);
        return $this->render('posts/show.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/postsByUser/{userId}", name="posts.byUser")
     */
    public function postsByUserAction($userId)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Posts');
        $posts = $repository->findBy(['userId' => $userId]);

        return $this->render('posts/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
