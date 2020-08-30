<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Book;
use App\Entity\Category;
use App\Form\BookType;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\BookRepository;


class BookController extends AbstractController
{
    /**
     * @Route("/books-list", name="book_list")

     */
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        BookRepository $bookRepository
    ) {
        $book = $bookRepository->findAll(true);

        $paginatorBook = $paginator->paginate(
             $book,
             $request->query->getInt('page', 1)/*page number*/,
         2/*limit per page*/ );

        return $this->render('book/index.html.twig', [
            'book' => $paginatorBook,
        ]);
    }

    /**
   * @Route("/book/create", name="book_create")
   *
   * @param Request $request
   *
   */
  public function create(Request $request)
  {
      $book = new Book();

      $form = $this->createForm(BookType::class, $book);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          
          $book = $form->getData();
          
          $em = $this->getDoctrine()->getManager();
          $em->persist($book);
          $em->flush();

          return $this->redirectToRoute('book_list');
      }

      return $this->render('book/create.html.twig', [
          'form' => $form->createView(), 'book' => $book
      ]);
  }

  /**
   * @Route("book/edit/{id}", name="book_edit")
   *
   * @param Request $request
   * @param Book $book
   *
   */
  public function edit(
      Request $request,
      Book $book
  ) {
      if (!$book) {
            throw $this->createNotFoundException(
                'There are no book with the following id: ' . $id
            );
      }

      $form = $this->createForm(BookType::class, $book);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->getDoctrine()->getManager()->flush();

          $this->addFlash('success', 'you udpated book');

          return $this->redirectToRoute('book_list');
      }

      return $this->render('book/edit.html.twig', [
          'form' => $form->createView(), 'book' => $book
      ]);
  }

      /**
    * @Route("/book/remove/{id}", name="book_delete")
    *
    * @param Request $request
    * @param Book $book
    *
    */
    public function delete(
        Request $request,
        Book $book
    ) {
       if (!$book) {
            throw $this->createNotFoundException(
                'There are no book with the following id: ' . $id
            );
       }

       $em = $this->getDoctrine()->getManager();
       $em->remove($book);
       $em->flush();
       return $this->redirectToRoute('book_list');
    }
}
