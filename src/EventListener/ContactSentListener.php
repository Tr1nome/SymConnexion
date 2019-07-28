<?php

namespace App\EventListener;

use App\Entity\Contact;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\ContactSentEvent;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class ContactSentListener implements EventSubscriberInterface
{
    use LoggerAwareTrait;

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
            "contact.sent" => [
                ["onContactSent", 0],
                ["writeLog", -10],
            ]
        ];
    }

    public function onContactSent(ContactSentEvent $event):void
    {
        $contact = $event->getContact();
    
        if($contact instanceof Contact)
        {
            $this->sendMail($contact);
            $event->stopPropagation();
            $this->writeLog($contact);
        }
    }

    public function writeLog(Contact $contact): void
    {
        //$this->logger->info(sprintf('New Image : %s , created by %s , and send mail'));
    }

    private function sendMail(Contact $contact) : void
    {
        
        $sendTo  = 'leosouly@gmail.com';
        $message = (new Swift_Message('[Demande de renseignement]'))
            ->setFrom('no-reply@fyps.fr')
            ->setTo($sendTo)
            ->setBody(
                $this->templating->render(
                    'emails/contact_sent.email.twig',
                    ['contact' => $contact]
                ),
                'text/html'
            );
        $this->swift->send($message);
    }
}