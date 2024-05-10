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
}
