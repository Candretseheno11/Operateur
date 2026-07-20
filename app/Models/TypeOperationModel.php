<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table = 'types_operations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['libelle'];
    protected $useTimestamps = true;
}