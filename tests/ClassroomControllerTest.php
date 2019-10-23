<?php

namespace App\Tests;

use App\Controller\ClassroomController;
use App\Entity\Classroom;
use App\Repository\ClassroomRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;

class ClassroomControllerTest extends KernelTestCase
{
    public function testIndex()
    {
        $repository = $this->createMock(ClassroomRepository::class);
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn([new Classroom(), new Classroom()]);

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->expects($this->once())
            ->method('getRepository')
            ->with($this->identicalTo(Classroom::class))
            ->willReturn($repository);

        $controller = $this->getMockBuilder(ClassroomController::class)
            ->setMethods(['getDoctrine'])
            ->disableOriginalConstructor()
            ->getMock();
        $controller->expects($this->once())
            ->method('getDoctrine')
            ->willReturn($managerRegistry);

        self::bootKernel();
        $controller->setContainer(self::$kernel->getContainer());

        /** @var Response $response */
        $response = $controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(2, json_decode($response->getContent()));
    }

    public function testGetAction()
    {
        $classroom = new Classroom();
        $classroom->setName('TEST');
        $classroom->setIsActive(false);

        $controller = $this->getMockBuilder(ClassroomController::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        self::bootKernel();
        $controller->setContainer(self::$kernel->getContainer());

        /** @var Response $response */
        $response = $controller->getAction($classroom);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('TEST', json_decode($response->getContent())->name);
        $this->assertFalse(json_decode($response->getContent())->isActive);
    }

    public function testPostAction()
    {
        $request = new Request([], [
            'name' => 'TEST',
            'isActive' => false
        ]);

        $classroom = new Classroom();
        $classroom->setName('TEST');
        $classroom->setIsActive(false);

        $controller = $this->getMockBuilder(ClassroomController::class)
            ->setMethods(['getDoctrine'])
            ->disableOriginalConstructor()
            ->getMock();
        $controller->validator = (new ValidatorBuilder())->enableAnnotationMapping()->getValidator();

        $em = $this->createMock(ObjectManager::class);
        $em->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($classroom));
        $em->expects($this->once())
            ->method('flush');

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->expects($this->once())
            ->method('getManager')
            ->willReturn($em);

        $controller->expects($this->once())
            ->method('getDoctrine')
            ->willReturn($managerRegistry);

        self::bootKernel();
        $controller->setContainer(self::$kernel->getContainer());

        /** @var Response $response */
        $response = $controller->postAction($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('TEST', json_decode($response->getContent())->name);
        $this->assertFalse(json_decode($response->getContent())->isActive);
    }

    public function testPostActionError()
    {
        $this->expectException(BadRequestHttpException::class);
        $request = new Request([], [
            'name' => '',
            'isActive' => false
        ]);

        $classroom = new Classroom();
        $classroom->setName('');
        $classroom->setIsActive(false);

        $controller = $this->getMockBuilder(ClassroomController::class)
            ->setMethods(['getDoctrine'])
            ->disableOriginalConstructor()
            ->getMock();
        $controller->validator = (new ValidatorBuilder())->enableAnnotationMapping()->getValidator();

        $em = $this->createMock(ObjectManager::class);
        $em->expects($this->never())
            ->method('persist')
            ->with($this->equalTo($classroom));
        $em->expects($this->never())
            ->method('flush');

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->expects($this->once())
            ->method('getManager')
            ->willReturn($em);

        $controller->expects($this->once())
            ->method('getDoctrine')
            ->willReturn($managerRegistry);

        self::bootKernel();
        $controller->setContainer(self::$kernel->getContainer());

        /** @var Response $response */
        $response = $controller->postAction($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testPutAction()
    {
        $classroom = new Classroom();
        $classroom->setName('TEST');
        $classroom->setIsActive(false);

        $request = new Request([], [
            'isActive' => true
        ]);

        $controller = $this->getMockBuilder(ClassroomController::class)
            ->setMethods(['getDoctrine'])
            ->disableOriginalConstructor()
            ->getMock();
        $controller->validator = (new ValidatorBuilder())->enableAnnotationMapping()->getValidator();

        $em = $this->createMock(ObjectManager::class);
        $em->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($classroom));
        $em->expects($this->once())
            ->method('flush');

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->expects($this->once())
            ->method('getManager')
            ->willReturn($em);

        $controller->expects($this->once())
            ->method('getDoctrine')
            ->willReturn($managerRegistry);

        self::bootKernel();
        $controller->setContainer(self::$kernel->getContainer());

        /** @var Response $response */
        $response = $controller->putAction($classroom, $request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('TEST', json_decode($response->getContent())->name);
        $this->assertTrue(json_decode($response->getContent())->isActive);
    }

    public function testPutActionError()
    {
        $this->expectException(BadRequestHttpException::class);
        $classroom = new Classroom();
        $classroom->setName('TEST');
        $classroom->setIsActive(false);

        $request = new Request([], [
            'name' => '',
            'isActive' => false
        ]);

        $controller = $this->getMockBuilder(ClassroomController::class)
            ->setMethods(['getDoctrine'])
            ->disableOriginalConstructor()
            ->getMock();
        $controller->validator = (new ValidatorBuilder())->enableAnnotationMapping()->getValidator();

        $em = $this->createMock(ObjectManager::class);
        $em->expects($this->never())
            ->method('persist')
            ->with($this->equalTo($classroom));
        $em->expects($this->never())
            ->method('flush');

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->expects($this->once())
            ->method('getManager')
            ->willReturn($em);

        $controller->expects($this->once())
            ->method('getDoctrine')
            ->willReturn($managerRegistry);

        self::bootKernel();
        $controller->setContainer(self::$kernel->getContainer());

        /** @var Response $response */
        $response = $controller->putAction($classroom, $request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testDeleteAction()
    {
        $classroom = new Classroom();
        $classroom->setName('TEST');
        $classroom->setIsActive(false);

        $controller = $this->getMockBuilder(ClassroomController::class)
            ->setMethods(['getDoctrine'])
            ->disableOriginalConstructor()
            ->getMock();

        $em = $this->createMock(ObjectManager::class);
        $em->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($classroom));
        $em->expects($this->once())
            ->method('flush');

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->expects($this->once())
            ->method('getManager')
            ->willReturn($em);

        $controller->expects($this->once())
            ->method('getDoctrine')
            ->willReturn($managerRegistry);

        self::bootKernel();
        $controller->setContainer(self::$kernel->getContainer());

        /** @var Response $response */
        $response = $controller->deleteAction($classroom);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
