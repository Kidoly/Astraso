<?php

namespace App\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(title: "Periode_DTO", description: "Periode DTO")]
class Periode_DTO
{
    #[OA\Property(description: "Start date of the period", format: "date-time")]
    public \DateTime $dateDebut;

    #[OA\Property(description: "End date of the period", format: "date-time")]
    public \DateTime $dateFin;
}
