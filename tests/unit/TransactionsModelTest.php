<?php

use App\Models\TransactionsModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TransactionsModelTest extends CIUnitTestCase
{
    public function testGetGainByTransfertForOperatorTypeReturnsArrayWithoutThrowing(): void
    {
        $db = db_connect([
            'database' => WRITEPATH . 'mobilemoney.db',
            'DBDriver' => 'SQLite3',
            'DBPrefix' => '',
            'DBDebug' => true,
            'swapPre' => '',
            'failover' => [],
            'foreignKeys' => true,
            'busyTimeout' => 1000,
            'synchronous' => null,
            'dateFormat' => [
                'date' => 'Y-m-d',
                'datetime' => 'Y-m-d H:i:s',
                'time' => 'H:i:s',
            ],
        ]);
        $model = new TransactionsModel($db);

        $result = $model->getGainByTransfertForOperatorType(0);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('frais', $result);
    }
}
