<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogControler extends AbstractController
{
    /**
     *
     * @Route("/")
     */
    public function list()
    {

        return $this->render(
            'blog/list.html.twig'
        );
    }

    /**
     * @Route("/article/{slug}")
     */
    public function detail($slug)
    {
        return $this->render(
            'blog/detail.html.twig'
        );
    }
}