<?php

declare(strict_types=1);

namespace Domain\AuthContext\Adapters\Primary\Controllers;

use Aws\S3\S3ClientInterface;
use Domain\AuthContext\Adapters\Secondary\Repositories\TenantRepository;
use Domain\AuthContext\Adapters\Secondary\Repositories\UserRepository;
use Domain\PdfContext\Adapters\PdfGeneratorGateway;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Infrastructure\Service\S3\S3Service;
use Infrastructure\Service\Security\TwoFactorSecurityConfig;
use OpenAI;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

use function array_rand;

final class TestController extends AbstractController
{


    public function __construct(
        private PdfGeneratorGateway $pdfGenerator,
        private readonly S3ClientInterface $s3Client,
        private S3Service $s3Service,
        private TwoFactorSecurityConfig $securityConfig,
        private OpenAI\Client $openAi,
        private $bucket
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $isTwoFactorRegistered = $user->isTotpAuthenticationEnabled() || $user->isGoogleAuthenticatorEnabled() || $user->isEmailAuthEnabled();

        return $this->render('index.html.twig', [
            'isTwoFactorEnabled' => $this->securityConfig->isTwoFactorEnabled(),
            'isTwoFactorRegistered' => $isTwoFactorRegistered
        ]);
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

    #[Route('/demo/filter/list', name: 'app_demo_filtered_list')]
    public function filteredList(): Response
    {

        //return new Response(null,200);
        //uses Symfony Live Components
        return $this->render('demo/demo_filtered_table.html.twig');
    }

    #[Route('/demo/filter/cards', name: 'app_demo_filtered_cards')]
    public function filteredCards(): Response
    {
        //return new Response(null,200);
        //uses Symfony Live Components
        return $this->render('demo/demo_filtered_cards.html.twig');
    }

    #[Route('/demo/charts', name: 'app_demo_filtered_charts')]
    public function charts(ChartBuilderInterface $chartBuilder)
    {
        $chartLine = $this->buildLineGraph($chartBuilder);
        $chartCircle = $this->buildPieGraph($chartBuilder);

        return $this->render('demo/demo_chartjs.html.twig', [
            //'package' => $package,
            'chart' => $chartLine,
            'chartCircle' => $chartCircle,
        ]);
    }

    #[Route('/demo/react', name: 'app_demo_react')]
    public function react(){

        return $this->render('demo/demo_react.html.twig' , [
            'data' => "Hello World !"
        ]);

    }

    #[Route('/demo/qrcode', name: 'app_demo_qrcode')]
    public function qrCode(string $projectDir)
    {
        $writer = new PngWriter();

// Create QR code
        $qrCode = QrCode::create('https://www.qizuna.fr')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create($projectDir.'/assets/images/brand.png')
            ->setResizeToWidth(100)
            ->setPunchoutBackground(true)
        ;
        $logo = null;

        // Create generic label
        $label = Label::create('visitez qizuna.fr')
            ->setTextColor(new Color(255, 0, 0));


        $result = $writer->write($qrCode, $logo, $label);

        // Validate the result
        //$writer->validateResult($result, 'Life is too short to be generating QR codes');

        return $this->render('demo/demo_qrcode.html.twig', [
            'qrCode' => $result->getDataUri(),
        ]);
    }

    #[Route('/demo/openai_chat', name: 'app_demo_openai')]
    public function openAi(): Response
    {




        return $this->render('demo/demo_openai.html.twig' );


    }



//    #[Route('/login')]
//    final  public function login(): Response
//    {
//        return $this->render('login.html.twig');
//    }

    #[Route('/email')]
    public function email(MailerInterface $mailer): Response
    {
        $package = new Package(
            new JsonManifestVersionStrategy(
                Path::join($this->getParameter('kernel.project_dir'), "public/build/manifest.json")
            )
        );
        $image = base64_encode(
            file_get_contents(
                Path::join(
                    $this->getParameter('kernel.project_dir'),
                    "public",
                    $package->getVersion('build/images/logos/symfony.png')
                )
            )
        );

        $email = (new TemplatedEmail())
            ->from('fabien@example.com')
            ->to(new Address('ryan@example.com'))
            ->subject('Thanks for signing up down!')

            // path of the Twig template to render
            ->htmlTemplate('emails/example.html.twig')
            // pass variables (name => value) to the template
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => 'foo',
                'url' => 'https://www.google.fr',
                'tenant' => "Nom du tenant",
                'title' => "Ceci est le titre ",
                'base64image' => $image // image as base 64 -> easy tenant image sending
            ])
        ;

        $mailer->send($email);

        return new Response(null, 201);
    }

    #[Route('/demo/chat', name: 'app_chat' , methods: ['POST'])]
    public function askToChatGpt(Request $request){

        $phrase = $request->get('phrase');
        $query = 'Peux tu traduire en trois langues aléatoires la phrase suivante et uniquement afficher
        la traduction sans aucun commentaire. Choisis ces langues parmi celles de l\'union européene. 
        Renvoie le contenu sous forme de liste HTML.
        Chaque traduction devra se positionner sur une nouvelle ligne, et devra être précédée de l\emoji , 
        ainsi que de la langue entre parentheses 
        correspondant à la langue de la traduction suivante: "'.$phrase.'" ';

        $response = $this->openAi->chat()->create(
            [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
//                    ['role' => 'user', 'content' => 'Peux tu corriger la phrase suivante : "Je croix ke cet frase contiens des ereurres" '],
                    ['role' => 'user', 'content' => $query],
                ],
            ]
        );

        return new Response($response['choices'][0]['message']['content']);
    }

    private function buildLineGraph(ChartBuilderInterface $chartBuilder): Chart
    {
        $chartLine = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chartLine->setData(
            [
                'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                'datasets' => [
                    [
                        'label' => 'Cookies mangés 🍪',
                        'backgroundColor' => 'rgb(50,164,154)',
                        'borderColor' => 'RGB(30 104 97)',
                        'data' => [2, 10, 5, 18, 20, 30, 45],
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'Km parcourus 🏃‍♀️',
                        'backgroundColor' => 'rgb(231,64,17)',
                        'borderColor' => 'RGB(168 48 14)',
                        'data' => [10, 15, 4, 3, 25, 41, 25],
                        'tension' => 0.4,
                    ],
                ],
            ]
        );
        $chartLine->setOptions(
            [
                'maintainAspectRatio' => false,
            ]
        );
        return $chartLine;
    }

    #[Route('/demo/tenant/{tenantId}', name: 'app_demo_tenant')]
    public function testTenant(string $tenantId, UserRepository $repo, TenantRepository $tenantRepository){

        $tenant = $tenantRepository->find($tenantId);
        $users = $repo->findUsersByTenant($tenant);

        dd($users);

    }

    private function buildPieGraph(ChartBuilderInterface $chartBuilder): Chart
    {
        $chartCircle = $chartBuilder->createChart(Chart::TYPE_PIE);

        $chartCircle->setData(
            [
                'labels' => ['Red', 'Blue', 'Yellow'],
                'datasets' => [
                    [
                        'label' => 'My First Dataset',
                        'data' => [300, 50, 100],
                        'backgroundColor' => [
                            'rgb(50,164,154)',
                            'rgb(231,64,17)',
                            'rgb(32,42,55)',
                        ],
                    ],
                ],
            ]
        );
        return $chartCircle;
    }
}
