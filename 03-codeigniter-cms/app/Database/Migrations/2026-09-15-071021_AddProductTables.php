<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 5,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "product_name" => [
                "type" => "VARCHAR",
                "constraint" => 100,
            ],
            "price" => [
                "type" => "DECIMAL",
                "constraint" => "10, 2",
            ],
            "qty_in_stock" => [
                "type" => "INT",
                "constraint" => 5,
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "updated_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
        ]);

        $this->forge->addKey("id", true);

        $this->forge->createTable("products");
    }

    public function down()
    {
        $this->forge->dropTable("products");
    }
}
