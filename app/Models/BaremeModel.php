<?php

namespace App\Models;
use CodeIgniter\Model;

class BaremeModel extends Model
{
    protected $table = 'baremes';

    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['id_type_operation', 'montant_min', 'montant_max', 'frais'];

    public function getBaremeByTypeOperation($id_type_operation)
    {
        return $this->where('id_type_operation', $id_type_operation)->first();
    }
    public function addBareme($data)
    {
        return $this->insert($data);
    }

    public function updateBareme($id, $data)
    {
        return $this->update($id, $data);
    }

}