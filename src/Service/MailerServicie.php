<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailerService)
    {
        $this->mailer = $mailerService;
    }

    //Eliminacion de mascota
    public function sendPetDeletedEmail($userEmail, $petName)
    {
        $email = (new Email())
            ->from('admin@clinicaveterinaria.com')
            ->to($userEmail)
            ->subject('Mascota Eliminada')
            ->text('Su mascota con nombre ' . $petName . ' ha sido eliminada.');
        $this->mailer->send($email);
    }

    //Registro de mascota
    public function sendPetRegisteredEmail($userEmail, $petName)
    {
        $email = (new Email())
            ->from('admin@clinicaveterinaria.com') 
            ->to($userEmail)
            ->subject('Mascota Registrada')
            ->text('Su mascota con nombre ' . $petName . ' ha sido registrada.');
        $this->mailer->send($email);
    }

    //Cita cancelada
    public function sendAdminNotification($userEmail, $appointmentDate, $appointmentTime)
    {
        $formattedDate = $appointmentDate->format('Y-m-d');
        $formattedTime = $appointmentTime->format('H:i:s');

        $email = (new Email())
            ->from('admin@clinicaveterinaria.com')
            ->to($userEmail)
            ->subject('Cita Cancelada')
            ->text('Su cita programada para ' . $formattedDate . ' a las ' . $formattedTime . ' ha sido cancelada.');
        $this->mailer->send($email);
    }
}
