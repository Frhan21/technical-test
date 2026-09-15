<?php

namespace App\Models;

use CodeIgniter\Model;

class Transaction extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "user_id", 
        "product_id", 
        "payment_method", 
        "qty", 
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
        'user_id'        => 'required|integer',
        'product_id'     => 'required|integer',
        'payment_method' => 'required|in_list[cash,transfer]',
        'qty'            => 'required|integer|greater_than_equal_to[1]',
    ];
    
    protected $validationMessages   = [
        'user_id' => [
            'required'    => 'User ID wajib diisi.',
            'integer'     => 'User ID harus berupa angka.',
        ],
        'product_id' => [
            'required'    => 'Product ID wajib diisi.',
            'integer'     => 'Product ID harus berupa angka.',
        ],
        'payment_method' => [
            'required'    => 'Metode pembayaran wajib diisi.',
            'in_list'     => 'Metode pembayaran tidak valid.',
        ],
        'qty' => [
            'required'              => 'Jumlah wajib diisi.',
            'integer'               => 'Jumlah harus berupa angka bulat.',
            'greater_than_equal_to' => 'Jumlah minimal 1.',
        ],
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

}
