<?php

namespace App\tests\Unit\Form;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\BookType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Validation;
use Doctrine\Common\Persistence\ObjectManager;


class BookTypeTest extends TypeTestCase
{

    /**
    * @var \Doctrine\ORM\EntityManager
    */
    private $em;

     protected function setUp() : void
     {
         $this->em = DoctrineTestHelper::createTestEntityManager();

         parent::setUp();
     }

     /**
     * Load the ValidatorExtension so RepeatedType can resolve 'invalid_message'
     *
     * @return array
     */
     protected function getExtensions()
     {
         return [
             new ValidatorExtension(Validation::createValidator()),
         ];
     }

     /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockEntityType()
    {
        $mockEntityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mockRegistry = $this->getMockBuilder('Symfony\Bridge\Doctrine\ManagerRegistry')
            ->disableOriginalConstructor()
            ->setMethods(array('getManagerForClass'))
            ->getMock();

        $mockRegistry->expects($this->any())->method('getManagerForClass')
            ->will($this->returnValue($mockEntityManager));

        $mockEntityType = $this->getMockBuilder('Symfony\Bridge\Doctrine\Form\Type\EntityType')
            ->setMethods(array('getName'))
            ->setConstructorArgs(array($mockRegistry))
            ->getMock();

        $mockEntityType->expects($this->any())->method('getName')
            ->will($this->returnValue('entity'));

        return $mockEntityType;
    }


    public function testFormType() : void
    {

        $category = new Category();
        $category->setName(mt_rand(42, 1337));

        $formData = [
              "title"  => "test",
              "author" => "test",
              "category" => $category
        ];

         $form = $this->factory->create(BookType::class);

         $bookComparedToForm = new Book();
         $bookComparedToForm
               ->setTitle($formData["title"])
               ->setAuthor($formData["author"])
               ->setCategory($formData["category"]);
           ;

          $form->submit($formData);

          $this->assertTrue($form->isSynchronized());
          $this->assertEquals($bookComparedToForm, $form->getData());

          $view = $form->createView();
          $children = $view->children;

           foreach (array_keys($formData) as $key) {
               $this->assertArrayHasKey($key, $children);
           }
       }
}
