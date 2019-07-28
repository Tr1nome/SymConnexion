<?php

namespace App\EventListener;

use App\Entity\Formation;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\FormationSuggestedEvent;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class FormationSuggestedListener implements EventSubscriberInterface
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
            "formation.suggested" => [
                ["onFormationSuggested", 0],
                ["writeLog", -10],
            ]
        ];
    }

    public function onFormationSuggested(FormationSuggestedEvent $event):void
    {
        $formation = $event->getFormation();
        $user = $event->getUser();
    
        if($formation instanceof Formation && $user instanceof User)
        {
            $this->sendMail($formation, $user);
            $event->stopPropagation();
            $this->writeLog($formation, $user);
        }
    }

    public function writeLog(Formation $formation): void
    {
        //$this->logger->info(sprintf('New Image : %s , created by %s , and send mail'));
    }

    private function sendMail(Formation $formation, User $user) : void
    {
        
        $sendTo  = 'leosouly@gmail.com';
        $message = (new Swift_Message($user->getEmail()))
            ->setFrom('no-reply@fyps.fr')
            ->setTo($sendTo)
            ->setBody(
                $this->templating->render(
                    'emails/formation_suggested.email.twig',
                    ['user'=> $user, 'formation' => $formation]
                ),
                'text/html'
            );
        $this->swift->send($message);
    }
}