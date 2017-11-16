<?php

namespace VL\CrudBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use VL\CrudBundle\Entity\listarticles;
use VL\CrudBundle\Form\listarticlesType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;



class CrudController extends Controller
{
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository('VLCrudBundle:listarticles');

        $listarticles= $repository->findAll();



        return $this->render('VLCrudBundle:Crud:index.html.twig', array(
            'articles'=>$listarticles,
        ));
    }

    public function addAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('vl_list');
        }

        $listarticles = new listarticles();
        $form = $this->createForm(listarticlesType::class, $listarticles);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $title = $listarticles->getTitle();
            $author = $listarticles->getAuthor();
            $content = $listarticles->getContent();
            $date = $listarticles->getDate();

            $file = $form['media']->getData();
            $extension = $file->guessExtension();

            $fileName = 'image'.rand(1, 99999999999).'.'.$extension;

            $file->move(
                $this->getParameter('ImageDirectory'), $fileName
            );


            $listarticles->setTitle($title);
            $listarticles->setAuthor($author);
            $listarticles->setContent($content);
            $listarticles->setDate($date);
            $listarticles->setMedia($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($listarticles);
            $em->flush();

            return new Response('Article ajouté');
        }

        $formView = $form->createView();
        return $this->render('VLCrudBundle:Crud:add.html.twig', array(
            'form'=>$formView,
        ));

    }
//    ================================================================================================
//-------------------------------------------EDIT-----------------------------------------------------

    public function editaction(Request $request, listarticles $listarticles, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('vl_list');
        }
        $form = $this->createForm(listarticlesType::class, $listarticles);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['media']->getData();
            $extension = $file->guessExtension();

            $fileName = 'image'.rand(1, 99999999999).'.'.$extension;

            $file->move(
                $this->getParameter('ImageDirectory'), $fileName
            );

            $listarticles->setMedia($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return new Response('Article modifié');
        }

        $formView = $form->createView();
        return $this->render('VLCrudBundle:Crud:edit.html.twig', array(
            'form' => $formView,
            'id' => $id,
        ));
    }

        public function deleteAction(listarticles $listarticles)
        {
            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('vl_list');
            }

            $em=$this->getDoctrine()->getManager();
            $em->remove($listarticles);
            $em->flush();

            return new Response('Article supprimé');
        }

        public function viewAction($id)
        {
            $repository = $this->getDoctrine()->getRepository('VLCrudBundle:listarticles');

            $article= $repository->find($id);


            return $this->render('VLCrudBundle:Crud:oneview.html.twig', array(
                'article'=>$article,
            ));

        }
}
