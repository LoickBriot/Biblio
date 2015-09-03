<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Form\CommentType;
use AppBundle\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    public function indexAction()
    {
        return $this->render('blog/index.html.twig');
    }


    /*
     * CHOIX A FAIRE : UTILISER GET POUR PERMETTRE LE PARTAGE D'URL DE RECHERCHE OU UTILISER POST ?
     */

    public function singleSearchPageAction(Request $request)
    {
        $word="Nancy";
        if (isset($_POST['searchedWord'])) {
            $word = $_POST['searchedWord'];
        }

        // Requête SQL. Trouver la liste de document (complete) ou réduite pa rapport à des critère SQL. (Tome, auteurs, date...)
        $documentList = array("document_patriote.pdf",  "test.pdf",  "these.pdf" ,"test2.pdf",  "test3.pdf");
        $doc="document_patriote.pdf";
        if (isset($_POST['SelectDoc'])) {
            $doc = $_POST['SelectDoc'];
        }

        // Utilisation de Elastic Search. Trouver les mots similaires au mot $word contenu dans le pdf $doc selectionné
        $wordList = array($word, "vous", "lous", "nous");

        return $this->render('blog/singleSearch.html.twig', array( "searchedWord" => $word, "chosenDoc" => $doc , "wordList" => $wordList, "documentList" => $documentList )); //, array('posts' => $posts));
    }


    public function multipleSearchPageAction(Request $request)
    {
        $word="Nancy";
        if (isset($_POST['fullSearch'])) {
            $word = $_POST['fullSearch'];
        }

        // Utilisation de Elastic Search. Trouver les mots similaires au mot $word contenu dans l'ensemble des pdf
        $wordList = array($word, "vous", "lous", "nous");

        // Utilisation de Elastic Search. Trouver tous les documents contenant exactement le mot $word.
        $documentList = array("document_patriote.pdf",  "test.pdf",  "these.pdf" ,"test2.pdf",  "test3.pdf");

        return $this->render('blog/multipleSearch.html.twig', array( "fullSearch" => $word, "wordList" => $wordList, "documentList" => $documentList)); //, array('posts' => $posts));
    }




    /*
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
     */
    public function postShowAction(Post $post)
    {
        return $this->render('blog/post_show.html.twig', array('post' => $post));
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Method("POST")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     *
     * NOTE: The ParamConverter mapping is required because the route parameter
     * (postSlug) doesn't match any of the Doctrine entity properties (slug).
     * See http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter
     */
    public function commentNewAction(Request $request, Post $post)
    {
        $form = $this->createCommentForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthorEmail($this->getUser()->getEmail());
            $comment->setPost($post);
            $comment->setPublishedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('blog_post', array('slug' => $post->getSlug()));
        }

        return $this->render('blog/comment_form_error.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     *
     * @param Post $post
     *
     * @return Response
     */
    public function commentFormAction(Post $post)
    {
        $form = $this->createCommentForm();

        return $this->render('blog/comment_form.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * This is a utility method used to create comment forms. It's recommended
     * to not define this kind of methods in a controller class, but sometimes
     * is convenient for defining small methods.
     */
    private function createCommentForm()
    {
        $form = $this->createForm(new CommentType());
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
}
