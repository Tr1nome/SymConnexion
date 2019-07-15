<?php

namespace App\EventListener;

use App\Entity\Formation;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\FormationRegisteredEvent;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class FormationRegisterListener implements EventSubscriberInterface
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
            "formation.registered" => [
                ["onFormationRegistered", 0],
                ["writeLog", -10],
            ]
        ];
    }

    public function onFormationRegistered(FormationRegisteredEvent $event):void
    {
        $formation = $event->getFormation();
    
        if($formation instanceof Formation)
        {
            $this->sendMail($formation);
            $event->stopPropagation();
            $this->writeLog($formation);
        }
    }

    public function writeLog(Formation $formation): void
    {
        //$this->logger->info(sprintf('New Image : %s , created by %s , and send mail'));
    }

    private function sendMail(Formation $formation) : void
    {
        
        $sendTo  = 'leosouly@gmail.com';
        $message = (new Swift_Message('[FenrirStudio.fr] : Inscription à une formation'))
            ->setFrom('no-reply@fyps.fr')
            ->setTo($sendTo)
            ->setBody(
                $this->templating->render(
                    'emails/formation_registered.email.twig',
                    ['formation' => $formation]
                ),
                'text/html'
            );
        $this->swift->send($message);
    }
}