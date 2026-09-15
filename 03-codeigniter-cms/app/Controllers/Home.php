<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;

class Home extends BaseController
{
    protected $user; 
    protected $product; 
    protected $transaction; 

    public function __construct() 
    {
        $this->user = new User(); 
        $this->product = new Product(); 
        $this->transaction = new Transaction(); 
    }

    public function index(): string
    {
        $data = [
            'users' => $this->user->countAll(),
            'products' => $this->product->countAll(),
            'transactions' => $this->transaction->countAll(),
        ];
        return view('index', $data); 
    }
}
