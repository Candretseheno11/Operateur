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

    public function getGainByTransfertForOperatorType(int $isOtherOperator): array
    {
        return $this->selectSum('transactions.frais', 'frais')
            ->join('comptes as compte_destination', 'compte_destination.id = transactions.id_compte_destination', 'left')
            ->join('clients as client_destination', 'client_destination.id = compte_destination.id_client', 'left')
            ->join('prefixes', 'prefixes.id = client_destination.id_prefixe', 'left')
            ->where('transactions.id_type_operation', 3)
            ->where('prefixes.est_autre_operateur', $isOtherOperator)
            ->first();
    }

    public function getTransferGainBreakdown(): array
    {
        return $this->select('prefixes.prefixe, prefixes.est_autre_operateur, COALESCE(SUM(transactions.frais), 0) as total_frais, COALESCE(SUM(transactions.montant), 0) as total_montant')
            ->join('comptes as compte_destination', 'compte_destination.id = transactions.id_compte_destination', 'left')
            ->join('clients as client_destination', 'client_destination.id = compte_destination.id_client', 'left')
            ->join('prefixes', 'prefixes.id = client_destination.id_prefixe', 'left')
            ->where('transactions.id_type_operation', 3)
            ->groupBy('prefixes.id, prefixes.prefixe, prefixes.est_autre_operateur')
            ->orderBy('prefixes.prefixe', 'ASC')
            ->findAll();
    }

    public function getGainByTransfertByPrefixeId($prefixe_id)
    {
        return $this->selectSum('frais')
            ->join('comptes', 'comptes.id = transactions.id_compte_source')
            ->where('id_type_operation', 3)
            ->where('comptes.id_client LIKE', $prefixe_id . '%')
            ->first();
    }

    public function getGainByRetraitByPrefixeId($prefixe_id)
    {
        return $this->selectSum('frais')
            ->join('comptes', 'comptes.id = transactions.id_compte_source')
            ->where('id_type_operation', 2)
            ->where('comptes.id_client LIKE', $prefixe_id . '%')
            ->first();
    }

}