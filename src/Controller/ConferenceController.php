<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/", name="conference")
     * @param Request $request
     * @return Response
     */
    public function index( Request $request ) : Response
    {
        $greet = '';
        if ( $name = $request->getRequestUri()) {
            $greet = sprintf( '<h1>Hello %s!</h1>', htmlspecialchars( $name ) );
        }

        return $this->render( 'conference/index.html.twig', [
            'controller_name' => 'ConferenceController',
            'greet' => $greet,
        ] );
    }
}
