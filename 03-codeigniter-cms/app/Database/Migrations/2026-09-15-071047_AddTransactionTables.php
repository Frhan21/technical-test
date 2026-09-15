<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransactionTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT", 
                "auto_increment" => true, 
            ],
            "user_id" => [
                "type" => "INT", 
            ], 
            "product_id" => [
                "type" => "INT", 
            ], 
            "payment_method" => [
                "type" => "VARCHAR", 
                "constraint" => 50, 
            ], 
            "qty" => [
                "type" => "INT", 
            ], 
            "created_at" => [
                "type" => "DATETIME", 
                "null" => true, 
            ], 
            "updated_at" => [
                "type" => "DATETIME", 
                "null" => true, 
            ] 
            
            
        ]);

        $this->forge->addKey("id", true); 

        // Foreign key untukt user
        $this->forge->addForeignKey(
            "user_id", 
            "users", 
            "id",
            "CASCADE", 
            "CASCADE"   
        ); 

        // Foreign key untuk products
        $this->forge->addForeignKey(
            "product_id", 
            "products", 
            "id",
            "CASCADE", 
            "CASCADE" 
        ); 

        $this->forge->createTable("transactions");
    }

    public function down()
    {
        $this->forge->dropTable("transactions");
    }
}
