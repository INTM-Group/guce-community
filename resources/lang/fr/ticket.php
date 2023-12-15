<?php

use App\Models\Ticket;

return [
    'priority' => [
        '_250' => "Anomalie Bloquante",
        '_190' => "Anomalie Non Bloquante",
        '_130' => "Demande Administrative",
        '_70' => "Demande d'Information",
        '_10' => "Prestation",
    ],
    'status' => [
        '_' . Ticket::STATUS_DISABLED => 'Désactivée',
        '_' . Ticket::STATUS_VALID => 'Valide',
        '_' . Ticket::STATUS_OPEN => 'Ouvert',
        '_' . Ticket::STATUS_COURS_CT => 'En cours (Ct)',
        '_' . Ticket::STATUS_ATTENTE_CT => "En attente (Ct)",
        '_' . Ticket::STATUS_COURS_CR => 'En cours (Cr)',
        '_' . Ticket::STATUS_ATTENTE_CR => 'En attente (Cr)',
        '_' . Ticket::STATUS_RESOLVED => 'Résolu',
        '_' . Ticket::STATUS_CLOSED => 'Clos',
    ],
    'criticality' => [
        '_2' => 'C',
        '_1' => 'Normal',
        '_0' => 'NC',
    ]
];
