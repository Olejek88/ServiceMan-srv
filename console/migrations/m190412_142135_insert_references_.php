<?php

use common\models\User;
use common\models\Users;
use console\yii2\Migration;

/**
 * Class m190412_142135_insert_references_
 */

class m190412_142135_insert_references_ extends Migration
{
    /**
     * {@inheritdoc}
     */
    const AUTH_KEY = 'K4g2d-bTENTHzzAJp22G1yF6otaUj9EF';
    const USERS_PIN_MD5 = '303f8364456898f50c877766f2f1f5ae';

    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');

        $this->insert('{{%user}}', [
            '_id' => '1',
            'username' => 'dev',
            'auth_key' => 'f1elprxfre3ri79clcY2VcaBdPqhPLZQ',
            'password_hash' => '$2y$13$nGZaF9DU5t/v63X./MM3Gu/eg0HsXBRtnBZ7adA3spSbJUKtLIEbC',
            'email' => 'shtrmvk@gmail.com',
            'status' => '10',
            'created_at' => $currentTime,
            'updated_at' => $currentTime
        ]);

        $this->insertIntoType('equipment_status','E681926C-F4A3-44BD-9F96-F0493712798D',
            'В порядке', $currentTime, $currentTime);
        $this->insertIntoType('equipment_status','D5D31037-6640-4A8B-8385-355FC71DEBD7',
            'Неисправно', $currentTime, $currentTime);
        $this->insertIntoType('equipment_status','A01B7550-4211-4D7A-9935-80A2FC257E92',
            'Отсутствует', $currentTime, $currentTime);


        $this->insertIntoType('work_status','1E9B4D73-044C-471B-A08D-26F32EBB22B0',
            'Новая', $currentTime, $currentTime);
        $this->insertIntoType('work_status', '31179027-8416-47E4-832F-2A94D7804A4F',
            'В работе', $currentTime, $currentTime);
        $this->insertIntoType('work_status', 'F1576F3E-ACB6-4EEB-B8AF-E34E4D345CE9',
            'Выполнена', $currentTime, $currentTime);
        $this->insertIntoType('work_status', 'EFDE80D2-D00E-413B-B430-0A011056C6EA',
            'Не выполнена', $currentTime, $currentTime);
        $this->insertIntoType('work_status', 'C2FA4A7B-0D7C-4407-A449-78FA70A11D47',
            'Отменена', $currentTime, $currentTime);

        $this->insertIntoType('alarm_status','4329BF34-D3D1-49AA-A8FC-C8A06E4C395A',
            'Обнаружено', $currentTime, $currentTime);
        $this->insertIntoType('alarm_status','0AABB3A1-C8DD-490E-92F3-BDD996182ADD',
            'Устранена', $currentTime, $currentTime);
        $this->insertIntoType('alarm_status','57CCC9A0-50F2-4432-BFF3-AE301CEBA50E',
            'Неизвестен', $currentTime, $currentTime);


        $this->insertIntoType('house_status','9236E1FF-D967-4080-9F42-59B03ADD25E8',
            'В порядке', $currentTime, $currentTime);
        $this->insertIntoType('house_status','559FBFE0-9543-4965-AC84-8919237EC317',
            'Не доступен', $currentTime, $currentTime);
        $this->insertIntoType('house_status','9B6C8A1D-498E-40EE-B973-AA9ACC6322A0',
            'Отсутствует', $currentTime, $currentTime);

        $this->insertIntoType('object_status','32562AA9-DE1D-436D-A0ED-5F5789DB8712',
            'В порядке', $currentTime, $currentTime);
        $this->insertIntoType('object_status','FEA3CC91-DD48-4264-AEF6-F91947A1B8EB',
            'Не доступна', $currentTime, $currentTime);
        $this->insertIntoType('object_status','BB6E24F2-6FA5-4E9A-83C8-5E1F4D51789B',
            'Отсутствует', $currentTime, $currentTime);

        $this->insertIntoType('request_status','F45775D3-9876-4831-9781-92E00240D44F',
            'Новая', $currentTime, $currentTime);
        $this->insertIntoType('request_status','49085FF9-5223-404A-B98D-7B042BB571A3',
            'В работе', $currentTime, $currentTime);
        $this->insertIntoType('request_status','FB7E8A7C-E228-4226-AAF5-AD3DB472F4ED',
            'Выполнена', $currentTime, $currentTime);
        $this->insertIntoType('request_status','B17CB2E0-58DF-4CA3-B620-AF8B39D6C229',
            'Не выполнена', $currentTime, $currentTime);
        $this->insertIntoType('request_status','8DA302D8-978B-4900-872C-4EB4DE13682A',
            'Отменена', $currentTime, $currentTime);

        $this->insert('{{%user}}', [
            'username' => 'sUser',
            'auth_key' => self::AUTH_KEY,
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash(self::AUTH_KEY),
            'email' => 'demonwork9@yandex.ru',
            'status' => '10',
            'created_at' => $currentTime,
            'updated_at' => $currentTime
        ]);

        $this->insert('{{%users}}', [
            '_id' => '1',
            'uuid' => 'E788CF00-CDCF-4BB5-A53A-DCBC946B2325',
            'user_id' => 1,
            'name' => 'Олег Иванов',
            'whoIs' => 'Ведущий инженер',
            'pin' => 'E20040008609006920603ED7',
            'contact' => '+79227000293 Олег',
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);

        $user = User::find()->where(['username' => 'sUser'])->one();
        if ($user) {
            $this->insert('{{%users}}', [
                'uuid' => Users::USER_SERVICE_UUID,
                'name' => 'sUser',
                'pin' => self::USERS_PIN_MD5,
                'contact' => 'none',
                'whoIs' => 'Сервисный',
                'user_id' => $user['_id'],
                'createdAt' => $currentTime,
                'changedAt' => $currentTime
            ]);
        }

        $this->insertIntoType('equipment_register_type', '2D3AD301-FD41-4A45-A18B-6CD13526CFDD',
            'Смена статуса', $currentTime, $currentTime);
        $this->insertIntoType('equipment_register_type', 'BE1D4149-2563-4771-88DC-2EB8B3DA684F',
            'Смена местоположения', $currentTime, $currentTime);
        $this->insertIntoType('equipment_register_type', '4C74019F-45A9-43Ab-9B97-4D077F8BF3FA',
            'Изменение свойств', $currentTime, $currentTime);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190412_142135_insert_references_ cannot be reverted.\n";

        return true;
    }

    private function insertIntoType($table, $uuid, $title, $createdAt, $changedAt) {
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190412_142135_insert_references_ cannot be reverted.\n";

        return false;
    }
    */
}
