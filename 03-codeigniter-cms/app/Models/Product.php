<?php

namespace App\Models;

use CodeIgniter\Model;

class Product extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "product_name",
        "price",
        "qty_in_stock",
        "created_at",
        "updated_at"
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'product_name' => 'required|min_length[3]|max_length[100]',
        'price' => 'required|decimal',
        'qty_in_stock' => 'required|integer',
    ];

    protected $validationMessages   = [
        'product_name' => [
            'required'    => 'Nama wajib diisi.',
            'min_length'  => 'Nama minimal 3 karakter.',
            'max_length'  => 'Nama maksimal 100 karakter.',
        ],
        'price' => [
            'required'    => 'Harga wajib diisi.',
            'decimal'     => 'Harga harus berupa angka.',
        ],
        'qty_in_stock' => [
            'required'    => 'Stok wajib diisi.',
            'integer'     => 'Stok harus berupa angka bulat.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
