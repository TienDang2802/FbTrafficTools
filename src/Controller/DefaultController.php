<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController  extends AbstractController
{
    /**
     * @Route(
     *     "/",
     *     name="default",
     *     methods={"GET"}
     * )
     */
    public function index()
    {
        return $this->redirectToRoute('tools.filter_email');
    }
}
