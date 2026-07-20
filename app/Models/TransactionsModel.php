<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionsModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_compte_source', 'id_compte_destination', 'id_type_operation', 'montant', 'date_transaction', 'frais'];
    protected $useTimestamps = true;

    public function getTransactionsByCompte($id_compte)
    {
        return $this->select('transactions.*, types_operations.libelle as type_operation')
            ->join('types_operations', 'types_operations.id = transactions.id_type_operation')
            ->where('id_compte_source', $id_compte)
            ->orWhere('id_compte_destination', $id_compte)
            ->orderBy('date_transaction', 'DESC')
            ->findAll();
    }

    public function getTransactionsByCompteAndType($id_compte, $id_type_operation)
    {
        return $this->select('transactions.*, types_operations.libelle as type_operation')
            ->join('types_operations', 'types_operations.id = transactions.id_type_operation')
            ->where('id_compte_source', $id_compte)
            ->orWhere('id_compte_destination', $id_compte)
            ->where('id_type_operation', $id_type_operation)
            ->orderBy('date_transaction', 'DESC')
            ->findAll();
    }

    public function getGainByTransfert()
    {

        return $this->selectSum('frais')->where('id_type_operation', 3)->first();
    }
    public function getGainByRetrait()
    {
        return $this->selectSum('frais')->where('id_type_operation', 2)->first();
    }

}