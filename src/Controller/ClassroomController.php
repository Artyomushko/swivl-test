<?php

namespace App\Controller;

use App\Entity\Classroom;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ClassroomController
 * @property ValidatorInterface validator
 * @package App\Controller
 * @Route("/classrooms", name="classrooms.")
 */
class ClassroomController extends AbstractController
{
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @Route("/", name="getAll", methods={"GET"})
     */
    public function index(): Response
    {
        $classrooms = $this->getDoctrine()->getRepository(Classroom::class)->findAll();
        return $this->json($classrooms);
    }

    /**
     * @Route("/{id}", name="get", methods={"GET"})
     * @param Classroom $classroom
     * @return Response
     */
    public function getAction(Classroom $classroom): Response
    {
        return $this->json($classroom);
    }

    /**
     * @Route("/", name="post", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws BadRequestHttpException
     */
    public function postAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $classroom = new Classroom();
        $classroom->setName($request->get("name"));
        $classroom->setIsActive($request->get("isActive"));

        $errors = $this->validator->validate($classroom);
        if (count($errors) > 0) {
            throw new BadRequestHttpException();
        }

        $em->persist($classroom);
        $em->flush();

        return $this->json($classroom, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="put", methods={"PUT"})
     * @param Classroom $classroom
     * @param Request $request
     * @return Response
     */
    public function putAction(Classroom $classroom, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        if (null !== $request->get("name"))
            $classroom->setName($request->get("name"));
        if (null !== $request->get("isActive"))
            $classroom->setIsActive($request->get("isActive"));

        $errors = $this->validator->validate($classroom);
        if (count($errors) > 0) {
            throw new BadRequestHttpException();
        }

        $em->persist($classroom);
        $em->flush();

        return $this->json($classroom);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Classroom $classroom
     * @return Response
     */
    public function deleteAction(Classroom $classroom): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($classroom);
        $em->flush();

        return $this->json("DELETED");
    }
}
