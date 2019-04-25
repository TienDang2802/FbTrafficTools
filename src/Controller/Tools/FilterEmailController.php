<?php

namespace App\Controller\Tools;

use App\Form\Type;
use Symfony\Component\HttpFoundation;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/tools")
 */
class FilterEmailController extends AbstractController
{
    /**
     * @Route(
     *     "/filter-email",
     *     name="tools.filter_email",
     *     methods={"GET", "POST"}
     * )
     * @param HttpFoundation\Request $request
     *
     * @return HttpFoundation\Response
     */
    public function index(HttpFoundation\Request $request): HttpFoundation\Response
    {
        $form = $this->createForm(Type\FilterEmailType::class);
        $form->handleRequest($request);

        $results = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $emailsInput = trim($data['emails'] ?? '');
            $domainSupport = $data['domain_support'] ?? [];

            $emails = preg_split('~[\r\n\s]+~', $emailsInput);

            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    list(, $domain) = explode('@', $email);
                    if (\in_array($domain, $domainSupport)) {
                        $results[$domain][] = $email;
                    }
                }
            }
        }

        return $this->render('tools/filter_email.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
        ]);
    }
}
