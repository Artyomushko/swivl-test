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
use Swagger\Annotations as SWG;

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
     * @SWG\Response(
     *     response="200",
     *     description="OK"
     * )
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
     * @SWG\Response(
     *     response="200",
     *     description="OK"
     * )
     * @SWG\Response(
     *     response="404",
     *     description="Not Found"
     * )
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
     *
     * @SWG\Parameter(
     *     name="name",
     *     required=true,
     *     in="formData",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="isActive",
     *     required=false,
     *     in="formData",
     *     type="boolean",
     *     default="true"
     * )
     * @SWG\Response(
     *     response="201",
     *     description="Created"
     * )
     * @SWG\Response(
     *     response="400",
     *     description="Bad Request"
     * )
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
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="isActive",
     *     required=false,
     *     in="formData",
     *     type="boolean"
     * )
     * @SWG\Response(
     *     response="200",
     *     description="OK"
     * )
     * @SWG\Response(
     *     response="400",
     *     description="Bad Request"
     * )
     * @SWG\Response(
     *     response="404",
     *     description="Not Found"
     * )
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
     * @SWG\Response(
     *     response="200",
     *     description="OK"
     * )
     * @SWG\Response(
     *     response="404",
     *     description="Not Found"
     * )
     */
    public function deleteAction(Classroom $classroom): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($classroom);
        $em->flush();

        return $this->json("DELETED");
    }
}
