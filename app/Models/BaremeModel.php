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

    public function deleteBareme($id)
    {
        return $this->delete($id);
    }

    public function getBaremeById($id)
    {
        return $this->find($id);
    }

    public function getAllBaremes()
    {
        return $this->findAll();
    }
    public function checkBaremeExists($id_type_operation, $montant_min, $montant_max)
    {
        return $this->where('id_type_operation', $id_type_operation)
            ->where('montant_min', $montant_min)
            ->where('montant_max', $montant_max)
            ->first() !== null;
    }
    public function updateBareme($id, $data)
    {
        return $this->update($id, $data);
    }
    // Dans App\Models\BaremeModel.php

    public function getAllBaremesWithOperations()
    {
        return $this->select('baremes.*, types_operations.libelle as type_libelle')
            ->join('types_operations', 'types_operations.id = baremes.id_type_operation', 'left')
            ->orderBy('baremes.id_type_operation', 'ASC')
            ->orderBy('baremes.montant_min', 'ASC')
            ->findAll();
    }
}