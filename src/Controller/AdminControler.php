<?php
/**
 * Created by PhpStorm.
 * User: jakubzientek
 * Date: 31/05/2019
 * Time: 12:54
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;

class AdminControler extends AbstractController
{


    /**
     * @Route("/admin/article/", name = "newArticle")
     */
    public function newArticle(Request $request) {
        $article = new Article();
        $article->setDate(new \DateTime('today'));

        $form = $this->createFormBuilder($article)
            ->add('active', CheckboxType::class,[
                'label'    => 'Show this entry publicly?',
                'required' => false,
            ])
            ->add('Title', TextType::class)
            ->add('Text', TextType::class)
            ->add('Date', DateType::class)
            ->add('Tags', TextType::class)
            ->add('Url', TextType::class)

            ->add('save', SubmitType::class, ['label' => 'Uložit'])
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $article = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Článek uložen');

            return $this->redirectToRoute('articles');
        } else {
            $form->getErrors();
            die;
        }

        return $this->render(
            'admin/article.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{id}", name = "article")
     */
    public function article(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->find('App\Entity\Article', $id);


        $form = $this->createFormBuilder($article)
            ->add('active', CheckboxType::class,[
                'label'    => 'Show this entry publicly?',
                'required' => false,
            ])
            ->add('Title', TextType::class)
            ->add('Text', TextareaType::class)
            ->add('Date', DateType::class)
            ->add('Tags', TextType::class)
            ->add('Url', TextType::class)

            ->add('save', SubmitType::class, ['label' => 'Uložit'])
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $article = $form->getData();
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Článek uložen');

            return $this->redirectToRoute('articles');
        }

        return $this->render(
            'admin/article.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/articles", name = "articles")
     */
    public function articles()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $articles = $entityManager->getRepository('App\Entity\Article')->findAll();

        return $this->render(
            'admin/articles.html.twig',[
            'articles' => $articles,
        ]);
    }

}