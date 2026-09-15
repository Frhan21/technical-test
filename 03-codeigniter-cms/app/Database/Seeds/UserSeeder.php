<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            "name" => "Farhan", 
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]; 

        $this->db->table("users")->insert($data); 

        $data2 = [
            "name" => "John Doe", 
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]; 

        $this->db->table("users")->insert($data2); 

        $data3 = [
            "name" => "Jane Doe", 
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]; 

        $this->db->table("users")->insert($data3); 
    }
}
