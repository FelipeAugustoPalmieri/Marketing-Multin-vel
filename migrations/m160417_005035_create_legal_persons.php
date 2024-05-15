<?php

use yii\db\Migration;

class m160417_005035_create_legal_persons extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            'legal_persons',
            [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp(),
                'person_class' => $this->string()->notNull(),
                'person_id' => $this->integer()->notNull(),
                'address' => $this->string()->notNull(),
                'district' => $this->string()->notNull(),
                'city_id' => $this->integer()->notNull(),
                'zip_code' => $this->string()->notNull(),
                'phone_number' => $this->string()->notNull(),
                'email' => $this->string()->notNull(),
                'website' => $this->string(),
            ]
        );
        $this->createIndex('uk_legal_persons', 'legal_persons', ['person_class', 'person_id'], true);
        $this->addForeignKey(
            'fk-legal_persons-city_id',
            'legal_persons',
            'city_id',
            'cities',
            'id',
            'CASCADE'
        );

        $this->execute("INSERT INTO legal_persons
        (created_at, person_class, person_id, address, district, city_id, zip_code, phone_number, email, website)
        SELECT c.created_at, legal_person_type, legal_person_id, '', '', (SELECT MIN(id) FROM cities), '', '', '', COALESCE(pj.website, pf.website)
        FROM businesses c
        LEFT JOIN physical_persons pf ON c.legal_person_type = 'PhysicalPerson' AND c.legal_person_id = pf.id
        LEFT JOIN juridical_persons pj ON c.legal_person_type = 'JuridicalPerson' AND c.legal_person_id = pj.id
        ");

        $this->dropIndex('uk_businesses', 'businesses');
        $this->execute("UPDATE businesses c
        SET legal_person_id = (
            SELECT id
            FROM legal_persons
            WHERE person_class = c.legal_person_type
            AND person_id = c.legal_person_id
        )");

        $this->dropColumn('physical_persons', 'website');
        $this->dropColumn('juridical_persons', 'website');

        $this->dropColumn('businesses', 'legal_person_type');
        $this->addForeignKey(
            'fk-businesses-legal_person_id',
            'businesses',
            'legal_person_id',
            'legal_persons',
            'id',
            'CASCADE'
        );
        $this->createIndex('uk_businesses', 'businesses', ['legal_person_id'], true);
    }

    public function safeDown()
    {
        return false;
    }
}
