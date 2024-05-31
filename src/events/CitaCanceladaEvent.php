<?php
namespace App\Event;

use App\Entity\Citas;
use Symfony\Contracts\EventDispatcher\Event;

class CitaCanceladaEvent extends Event
{
    public const NAME = 'cita.cancelada';

    protected $citas;

    public function __construct(Citas $cita)
    {
        $this->citas = $cita;
    }

    public function getCita(): Citas
    {
        return $this->citas;
    }
}