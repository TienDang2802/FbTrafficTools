<?php

namespace App\Controller\Tools;

use App\Form\Type;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/tools")
 */
class FilterEmailController extends Controller
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
    public function index(
        HttpFoundation\Request $request
    ): HttpFoundation\Response {
        $form = $this->createForm(Type\FilterEmailType::class);
        $form->handleRequest($request);

        $results = [];
        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $outputFormat = explode('|', $data['output_format']);

            /** @var $uploadFile UploadedFile */
            $uploadFile = $data['upload_file'];

            $fileUploader = $this->get('component_core_file_uploader');
            $phpSpreadsheetService = $this->get('component_core_php_spreadsheet_service');
            $file = $fileUploader->upload($uploadFile);

            $supportDomains = $data['support_domains'] ?? [];
            $regexPattern = '/^([a-z][a-z0-9_\.\+]{3,39}@(%s))/';

            try {
                $rows = $phpSpreadsheetService->readFile($file);

                $header = array_map('strtolower', $rows[0]);
                unset($rows[0]);

                foreach ($rows as $row) {
                    $dataOutputFormat = [];
                    $record = array_combine($header, $row);
                    if (empty($record['email'])) {
                        continue;
                    }
                    if (filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
                        foreach ($supportDomains as $supportDomain) {
                            $regex = sprintf($regexPattern, trim($supportDomain, '*'));
                            if (preg_match($regex, $record['email'])) {
                                foreach ($outputFormat as $format) {
                                    $dataOutputFormat[] = $record[$format];
                                }

                                $results[$supportDomain][] = implode('|', $dataOutputFormat);
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $this->render('tools/filter_email.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
            'errors' => $errors,
        ]);
    }
}
