<?php

namespace App\Controller\Tools;

use App\Form\Type;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
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

            var_dump($uploadFile);die;

            $fileUploader = $this->get('component_core_file_uploader');
            $phpSpreadsheetService = $this->get('component_core_php_spreadsheet_service');
            $file = $fileUploader->upload($uploadFile);
            $dirUploadFile = $file->getRealPath();

            $supportDomains = $data['support_domains'] ?? [];
            $regexPattern = '/^([a-z][a-z0-9_\.\+]{3,39}@(%s))/';

            $filesystem = new Filesystem();

            $mimeType = $file->getMimeType();
            $dirUploadZip = null;

            if ($file->getExtension() && in_array($mimeType, ['application/zip'])) {
                $zip = new \ZipArchive;
                if ($zip->open($file->getRealPath()) === true) {
                    $dirUploadZip = $this->getParameter('file_upload_dir') . '/zip';
                    $zip->extractTo($dirUploadZip);
                    $zip->close();

                    $finder = new Finder();
                    $finder->in($dirUploadZip)->exclude('__MACOSX');

                    /** @var SplFileInfo $fileExtract */
                    foreach ($finder->files()->name(['*.xlsx', '*.xls']) as $fileExtract) {
                        $file = new HttpFoundation\File\File($fileExtract->getRealPath());
                    }
                }
            }

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

                $filesystem->remove($dirUploadFile);
                if ($dirUploadZip) {
                    $filesystem->remove($dirUploadZip);
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
