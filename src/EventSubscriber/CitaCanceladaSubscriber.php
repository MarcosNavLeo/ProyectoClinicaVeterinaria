<?php
namespace App\EventSubscriber;

use App\Event\CitaCanceladaEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CitaCanceladaSubscriber implements EventSubscriberInterface
{
    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public static function getSubscribedEvents()
    {
        return [
            CitaCanceladaEvent::NAME => 'onCitaCancelada',
        ];
    }

    public function onCitaCancelada(CitaCanceladaEvent $event)
    {
        $cita = $event->getCita();
        $userEmail = $cita->getUser()->getEmail();
        $appointmentDate = $cita->getFecha();
        $appointmentTime = $cita->getHora();

        $this->mailerService->sendAdminNotification($userEmail, $appointmentDate, $appointmentTime);
    }
}