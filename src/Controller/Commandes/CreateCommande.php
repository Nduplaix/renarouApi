<?php


namespace App\Controller\Commandes;


use App\Entity\Basket;
use App\Entity\Commande;
use App\Entity\CommandeLine;
use App\Entity\CommandeStatus;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateCommande extends AbstractController
{
    public function __invoke(Commande $data, EntityManagerInterface $manager, \Swift_Mailer $mailer)
    {
        if (null === $this->getUser()) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Vous devez vous connecter pour passer une commande."
                ],
                401
            );
        }

        if (!$this->getUser()->getActivated()) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Veuillez activer votre adresse Email pour passer commande."
                ],
                401
            );
        }

        $basket = $this->getUser()->getBasket();

        if ($basket instanceof Basket && sizeof($basket->getBasketLines()) !== 0) {
            $status = $manager->getRepository(CommandeStatus::class)->find(1);

            $data->setUser($this->getUser())
                ->setStatus($status);

            foreach ($basket->getBasketLines() as $line) {
                $commandeLine = new CommandeLine();
                $commandeLine->setQuantity($line->getQuantity())
                    ->setReference($line->getReference())
                    ->setCommande($data);
                $data->addCommandeLine($commandeLine);
                $line->getReference()->setStock($line->getReference()->getStock() - $line->getQuantity());

                if ($line->getReference()->getStock() < 0) {
                    return new JsonResponse(
                        [
                            "code" => "401",
                            "message" => sprintf(
                                "Le stocke pour l'article %s est insufisant",
                                $line->getReference()->getProduct()->getLabel())
                        ],
                        401
                    );
                }

                $manager->persist($commandeLine);
            }
            $manager->persist($data);
            $basket->clearBasket();
            $manager->persist($basket);
            $manager->flush();

            $adminMessage = (new \Swift_Message(sprintf('[%s] - NOUVELLE COMMANDE', $data->getDelivery()->getLabel())))
                ->setFrom($this->getParameter('email'))
                ->setTo($this->getParameter('admin_email'))
                ->setBody(
                    $this->renderView(
                        'emails/command_confirmation_admin.html.twig',
                        ['command' => $data]
                    ),
                    'text/html'
                )

                // you can remove the following code if you don't define a text version for your emails
                ->addPart(
                    $this->renderView(
                    // templates/emails/registration.txt.twig
                        'emails/command_confirmation_admin.txt.twig',
                        ['command' => $data]
                    ),
                    'text/plain'
                )
            ;

            $clientMessage = (new \Swift_Message('RENAROU - Confirmation de votre commande'))
                ->setFrom($this->getParameter('email'))
                ->setTo($this->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/command_confirmation.html.twig',
                        ['commande' => $data]
                    ),
                    'text/html'
                )

                // you can remove the following code if you don't define a text version for your emails
                ->addPart(
                    $this->renderView(
                    // templates/emails/registration.txt.twig
                        'emails/command_confirmation.txt.twig',
                        ['commande' => $data]
                    ),
                    'text/plain'
                )
            ;

            $mailer->send($adminMessage);
            $mailer->send($clientMessage);

            // Configure Dompdf according to your needs
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);

            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('pdf/facture.html.twig',[
                'commande' => $data
            ]);

            // Load HTML to Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Store PDF Binary Data
            $output = $dompdf->output();

            // In this case, we want to write the file in the public directory
            $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/pdf';
            // e.g /var/www/project/public/mypdf.pdf
            $pdfFilepath =  sprintf('%s/%s-%s.pdf', $publicDirectory, $data->getId(), date('d-m-Y'));

            // Write file to the desired path
            file_put_contents($pdfFilepath, $output);

            return $data;
        }
        return new JsonResponse(["code" => "401", "message" => "Le panier ne peut pas être vide"], 401);
    }
}
