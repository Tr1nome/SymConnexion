<?php

namespace App\EventListener;

use App\Entity\Image;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\ImageCreatedEvent;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class ImageCreatedListener implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    private $userManager;
    private $swift;
    private $templating;

    public function __construct(
        LoggerInterface $logger,
        Swift_Mailer  $serviceMail,
        Environment $templating
    )
    {
        $this->setLogger($logger);
        $this->swift = $serviceMail;
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            "image.created" => [
                ["onImageCreated", 0],
                ["writeLog", -10],
            ]
        ];
    }

    public function onImageCreated(ImageCreatedEvent $event):void
    {
        $image = $event->getImage();
    
        if($image instanceof Image)
        {
            $this->sendMail($image);
            $event->stopPropagation();
            $this->writeLog($image);
        }
    }

    public function writeLog(Image $image): void
    {
        //$this->logger->info(sprintf('New Image : %s , created by %s , and send mail'));
    }

    private function sendMail(Image $image) : void
    {
        
        $sendTo  = 'leosouly@gmail.com';
        $message = (new Swift_Message('[FenrirStudio.fr] : Upload d\'image'))
            ->setFrom('no-reply@fyps.fr')
            ->setTo($sendTo)
            ->setBody(
                $this->templating->render(
                    'emails/image_creation.email.twig',
                    ['image' => $image]
                ),
                'text/html'
            );
        $this->swift->send($message);
    }
}