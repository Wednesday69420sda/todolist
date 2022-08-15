<?php

namespace App\Controller;

use App\Entity\TodoItem;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TodoController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $todos = $this->getDoctrine()->getRepository(TodoItem::class)
            ->findAll();

        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     */
    public function create(Request $request){
        $todo = new TodoItem();
        $todo->setTitle('');
        $todo->setContent('');

        $form = $this->createFormBuilder($todo)
            ->add('title',TextType::class)
            ->add('content',TextType::class)
            ->add('save', SubmitType::class, ['label' =>'Add todo'])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $todo = $form->getData();

            $en = $this->getDoctrine()->getManager();
            $en->persist($todo);
            $en->flush();

            return $this->redirectToRoute('index');
        }


        return $this->render('todo/create.html.twig',[
            'form'=> $form->createView()
        ]);


    }

    /**
     * @Route("/update", name="update")
     * @param Request $request
     */
    public function update(Request $request, $id) {
        $article = new TodoItem();
        $article = $this->getDoctrine()->getRepository(TodoItem::class)->find($id);

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('body', TextType::class, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/testcreate",name="test")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function test_create(Request $request){
        $todoTest = new TodoItem();
        $todoTest->setTitle("Test");
        $todoTest->setContent("Test");

        $em = $this->getDoctrine()->getManager();
        $em->persist($todoTest);
        $em->flush();
        return $this->redirectToRoute('index');

    }

    /**
     * @Route("/delete/{id}",name="delete")
     * @param $id
     */
    public function delete($id){
        $em = $this->getDoctrine()->getManager();
        $todo = $this->getDoctrine()->getRepository(TodoItem::class)->find($id);

        if(!$todo){
            throw $this->createNotFoundException("Not found todo with id: " . $id);
        }
        $em->remove($todo);
        $em->flush();
        return $this->redirectToRoute('index');
    }
}
