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
            $format = explode('|', $data['format']);
            $lengthFormat = count($format);

            $lstFilter = trim($data['lst_filter'] ?? '');
            $supportDomains = $data['support_domains'] ?? [];

            $lines = preg_split('~[\r\n\s]+~', $lstFilter);

            $regexPattern = '/^([a-z][a-z0-9_\.\+]{3,39}@(%s))/';

            foreach ($supportDomains as $supportDomain) {
                $results[$supportDomain] = [];
            }

            foreach ($lines as $line) {
                $line = array_pad(explode('|', $line), $lengthFormat, null);
                $data = array_combine($format, $line);
                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    foreach ($supportDomains as $supportDomain) {
                        $regex = sprintf($regexPattern, trim($supportDomain, '*'));
                        if (preg_match($regex, $data['email'])) {
                            $results[$supportDomain][] = implode('|', $line);
                        }
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
