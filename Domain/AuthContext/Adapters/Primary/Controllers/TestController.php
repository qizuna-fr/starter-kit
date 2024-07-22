<?php

declare(strict_types=1);

namespace Domain\AuthContext\Adapters\Primary\Controllers;

use Aws\S3\S3ClientInterface;
use Domain\PdfContext\Adapters\PdfGeneratorGateway;
use Infrastructure\Service\S3\S3Service;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

use function array_rand;

final class TestController extends AbstractController
{


    public function __construct(
        private PdfGeneratorGateway $pdfGenerator,
        private readonly S3ClientInterface $s3Client,
        private S3Service $s3Service,
        private $bucket
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('index.html.twig');
    }

    #[Route('/demo/slide', name: 'app_demo')]
    public function slidepanel(): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('demo/demo_slidepanel.html.twig');
    }

    #[Route('/demo/modal', name: 'app_demo_modal')]
    public function modal(): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('demo/demo_page.html.twig');
    }

    #[Route('/demo/pdf', name: 'app_demo_pdf')]
    public function printPdf(Request $request): Response
    {

//        $package = new Package(
//            new JsonManifestVersionStrategy(
//                Path::join($this->getParameter('kernel.project_dir'), "public/build/manifest.json")
//            )
//        );
//        $background = base64_encode(
//            file_get_contents(
//                Path::join(
//                    $this->getParameter('kernel.project_dir'),
//                    "public",
//                    $package->getVersion('build/images/brand.jpg')
//                )
//            )
//        );

        $content = $this->renderView('demo/pdf/example.html.twig', []);
        $streamContent = $this->pdfGenerator->generatePdf($content, '');
        $headers = [
            'Content-Type' => 'application/pdf',
        ];

        if ($request->get('download') === '1') {
            $headers['Content-Disposition'] = 'attachment; filename="example.pdf"';
        }

        return new Response((string)$streamContent, Response::HTTP_OK, $headers);
    }

    #[Route('/demo/s3', name: 'app_demo_s3')]
    public function s3()
    {
        $s3Service = new S3Service($this->s3Client, $this->bucket);

        return $this->render('demo/demo_s3.html.twig', [
            'files' => $s3Service->generateDownloadUrls(),
        ]);
    }

    #[Route('/demo/excel', name: 'app_demo_excel')]
    public function excel()
    {
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);

        $columns = ["A", "B", "C", "D", "E"];
        $lines = [1, 2, 3, 4, 5];

        for ($i = 0; $i < 10; $i++) {
            $cell = $columns[array_rand($columns)] . $lines[array_rand($lines)];
            $spreadsheet->getActiveSheet()->setCellValue($cell, 'Cellule' . $cell);
        }

        // Capturer le contenu du fichier Excel en mémoire
        $outputStream = fopen('php://temp', 'r+');
        $writer->save($outputStream);

        rewind($outputStream);
        $excelContent = stream_get_contents($outputStream);
        fclose($outputStream);

        return new Response($excelContent, Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="export_excel.xlsx"',
        ]);
    }



//    #[Route('/login')]
//    final  public function login(): Response
//    {
//        return $this->render('login.html.twig');
//    }

    #[Route('/email')]
    public function email(MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from('fabien@example.com')
            ->to(new Address('ryan@example.com'))
            ->subject('Thanks for signing up down!')

            // path of the Twig template to render
            ->htmlTemplate('emails/example.html.twig')

            /** TODO : Find a way to disable cache when twig is rendering
             * Fow now, solution is to rename the file to force new rendering
             */

            // pass variables (name => value) to the template
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => 'foo',
            ])
        ;

        $mailer->send($email);

        return new Response(null, 201);
    }
}
