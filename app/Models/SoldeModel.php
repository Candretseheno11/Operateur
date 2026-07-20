<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table = 'soldes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employe_id', 'type_conge_id', 'solde', 'annee'];
    protected $useTimestamps = true;
}