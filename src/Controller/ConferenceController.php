<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    private $entityManager;

    public function __construct( EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="conferences")
     * @param Request $request
     * @param ConferenceRepository $conferenceRepository
     * @return Response
     */
    public function index( Request $request, ConferenceRepository $conferenceRepository ) : Response
    {
        return $this->render( 'conference/conference.html.twig', [
            //'conferences' => $conferenceRepository->findAll(),
        ] );
    }

    /**
     * @Route("/conference/{id}", name="conference")
     * @param Request $request
     * @param Conference $conference
     * @param CommentRepository $commentRepository
     * @param ConferenceRepository $conferenceRepository
     * @return Response
     */
    public function show( Request $request, Conference $conference, CommentRepository $commentRepository, ConferenceRepository $conferenceRepository ) : Response
    {
        $comment = new Comment();
        $form = $this->createForm( CommentFormType::class, $comment );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() && $form->isValid() ) {
            $comment->setConference( $conference );
            $this->entityManager->persist( $comment );
            $this->entityManager->flush();

            //return $this->redirectToRoute( 'conference', [ 'slug' => $conference->getSlug() ] );
            return $this->redirectToRoute( 'conference', [ 'id' => $conference->getId() ] );
        }

        $offset = max( 0, $request->query->getInt( 'offset', 0 ) );
        $paginator = $commentRepository->getCommentPaginator( $conference, $offset );

        return $this->render( 'conference/show.html.twig', [
            //'conferences' => $conferenceRepository->findAll(),
            'conference' => $conference,
            //'comments' => $commentRepository->findBy( [ 'conference' => $conference ], [ 'createdAt' => 'DESC' ] ),
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min( count( $paginator ), $offset + CommentRepository::PAGINATOR_PER_PAGE ),
            'comment_form' => $form->createView(),
        ] );
    }
}
