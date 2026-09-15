<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $product = [
            "product_name" => "Apple", 
            "price" => 20000, 
            "qty_in_stock" => 10, 
            "created_at" => date("Y-m-d H:i:s"), 
            "updated_at" => date("Y-m-d H:i:s")
        ]; 

        $this->db->table("products")->insert($product); 

        $product2 = [
            "product_name" => "Orange", 
            "price" => 15000, 
            "qty_in_stock" => 20, 
            "created_at" => date("Y-m-d H:i:s"), 
            "updated_at" => date("Y-m-d H:i:s")
        ]; 

        $this->db->table("products")->insert($product2); 

        $product3 = [
            "product_name" => "Grapes", 
            "price" => 30000, 
            "qty_in_stock" => 5, 
            "created_at" => date("Y-m-d H:i:s"), 
            "updated_at" => date("Y-m-d H:i:s")
        ]; 

        $this->db->table("products")->insert($product3); 
    }
}
