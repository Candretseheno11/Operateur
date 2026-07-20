<?php

namespace App\Models;
use CodeIgniter\Model;

class UserObjectifModel extends Model
{
    protected $table = 'user_objectif';

    protected $allowedFields = ['user_id', 'objectif_id'];
    public function addObjectifToUser($user_id, $objectif_id)
    {
        $user_id = (int) $user_id;
        $objectif_id = (int) $objectif_id;
        $currentRecord = $this->where('user_id', $user_id)->first();

        if ($currentRecord) {
            return $this->where('user_id', $user_id)
                ->set(['objectif_id' => $objectif_id])
                ->update();
        } else {
            return $this->insert([
                'user_id' => $user_id,
                'objectif_id' => $objectif_id
            ]);
        }
    }
    public function getObjectifsByUser($user_id)
    {
        return $this->select('objectifs.*')
            ->join('objectifs', 'objectifs.id = user_objectif.objectif_id')
            ->where('user_objectif.user_id', $user_id)
            ->findAll();
    }

    public function updateObjectif($user_id, $old_objectif_id, $new_objectif_id)
    {

    }

}