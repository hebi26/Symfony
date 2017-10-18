<?php

namespace VL\CrudBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use VL\CrudBundle\Entity\listarticles;
use VL\CrudBundle\Form\listarticlesType;


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
    public function editaction(Request $request, listarticles $listarticles, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('vl_list');
        }
        $form = $this->createForm(listarticlesType::class, $listarticles);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

}
