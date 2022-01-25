<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class homeController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('index.html.twig');
    }
    
   
}
