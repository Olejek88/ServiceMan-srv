<?php

/**
 * Class m180907_075445_add_missed_relations
 */
class m180907_075445_add_missed_relations extends \console\yii2\Migration
{
    const FK_USERS2USER = 'fk_users_user_id__user_id';
    const FK_JOURNAL2USERS = 'fk_journal_useruuid__users_uuid';
    const USER = '{{%user}}';
    const USERS = '{{%users}}';
    const JOURNAL = '{{%journal}}';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addForeignKey(
            self::FK_USERS2USER,
            self::USERS,
            'user_id',
            self::USER,
            '_id',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );

        $db = $this->getDb();
        $db->createCommand('ALTER TABLE journal CHARACTER SET utf8 COLLATE utf8_unicode_ci')->execute();
        $db->createCommand('ALTER TABLE journal MODIFY userUuid VARCHAR(255) COLLATE utf8_unicode_ci')->execute();
        $db->createCommand('ALTER TABLE journal MODIFY description TEXT COLLATE utf8_unicode_ci')->execute();
        $this->createIndex('idx-journal-userUuid', self::JOURNAL, 'userUuid');
        $this->addForeignKey(
            self::FK_JOURNAL2USERS,
            self::JOURNAL,
            'userUuid',
            self::USERS,
            'uuid',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m180907_075445_add_missed_relations cannot be reverted.\n";

        $this->dropForeignKey(self::FK_JOURNAL2USERS, self::JOURNAL);
        $this->dropIndex('idx-journal-userUuid', self::JOURNAL);
        $db = $this->getDb();
        $db->createCommand('ALTER TABLE journal MODIFY description TEXT CHARACTER SET latin1')->execute();
        $db->createCommand('ALTER TABLE journal MODIFY userUuid VARCHAR(50) CHARACTER SET latin1')->execute();
        $db->createCommand('ALTER TABLE journal CHARACTER SET latin1')->execute();
        $this->dropForeignKey(self::FK_USERS2USER, self::USERS);

        return true;
    }
}
