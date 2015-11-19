<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Grade;
use AppBundle\Form\GradeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GradeController extends Controller
{
    /**
     * @Route("/grade", name="grade")
     */
    public function indexAction()
    {
        // 1. Doctrine
        $em   = $this->getDoctrine()->getManager();
        // 2. Repository (LessonRepository)
        $repo = $em->getRepository('AppBundle:Grade');
        // 3. findAll()
        $grades = $repo->findAll();

        return $this->render('grade/grades.html.twig', [
            'grades' => $grades,
        ]);
    }

    /**
     * @Route("/grade/create", name="grade_create")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $grade = new Grade();
        $form  = $this->createForm(new GradeType(), $grade);
        $form->add('submit', 'submit', [
            'label' => 'Create',
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($grade);
            $em->flush();

            return $this->redirectToRoute('grade');
        }

        return $this->render('grade/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/grade/{id}/edit", name="grade_edit")
     */
    public function updateAction(Request $request, $id)
    {
        $grade = $this->getDoctrine()->getManager()->getRepository('AppBundle:Grade')->find($id);

        if (null === $grade)
            throw $this->createNotFoundException(sprintf(
                'Grade n°%d not found.',
                $id
            ));

        $form  = $this->createForm(new GradeType(), $grade);
        $form->add('submit', 'submit', [
            'label' => 'Update',
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('grade');
        }

        return $this->render('grade/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/api/grade", name="api_grade")
     */
    public function apiIndexAction()
    {
        $repo   = $this->getDoctrine()->getManager()->getRepository('AppBundle:Grade');
        $grades = $repo->findAllArray();

        return new JsonResponse($grades);
    }
}
