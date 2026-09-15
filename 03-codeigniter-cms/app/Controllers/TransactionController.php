<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;

class TransactionController extends BaseController
{
    protected $transaction;
    protected $user;
    protected $product;

    public function __construct()
    {
        $this->transaction = new Transaction();
        $this->user = new User();
        $this->product = new Product();
    }

    public function index()
    {
        $data = [
            'transactions' => $this->transaction
                ->select('transactions.*, users.name AS user_name, products.product_name, products.price AS product_price')
                ->join('users', 'users.id = transactions.user_id')
                ->join('products', 'products.id = transactions.product_id')
                ->findAll(),
        ];
        return view("transactions/index", $data);
    }

    public function create()
    {
        $data = [
            'users' => $this->user->findAll(),
            'products' => $this->product->findAll(),
        ];
        return view("transactions/create", $data);
    }

    public function store()
    {
        // Get product by id 
        $product = $this->product->find($this->request->getPost('product_id'));
        if (!$product) {
            return redirect()->back()->withInput()->with('error', 'Product tidak ditemukan');
        }

        $qty = $product['qty_in_stock'];
        $qty_req = $this->request->getPost("qty");

        if ($qty < $qty_req) {
            return redirect()->back()->withInput()->with('error', 'Stock tidak cukup');
        }

        $data = [
            "user_id" => $this->request->getPost("user_id"),
            "product_id" => $this->request->getPost("product_id"),
            "payment_method" => $this->request->getPost("payment_method"),
            "qty" => $this->request->getPost("qty"),
        ];

        $db = \Config\Database::connect();
        $db->transBegin();

        $this->product->update($product['id'], [
            'qty_in_stock' => $qty - $qty_req
        ]);

        if (!$this->transaction->save($data) || $db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with("errors", $this->transaction->errors());
        }

        $db->transCommit();
        return redirect()->to("/transaction");
    }

    public function edit($id)
    {
        $transaction = $this->transaction->find($id);
        $data = [
            'transaction' => $transaction,
            'users' => $this->user->findAll(),
            'products' => $this->product->findAll(),
        ];
        return view("transactions/edit", $data);
    }

    public function update($id)
    {
        $old = $this->transaction->find($id);
        if (!$old) {
            return redirect()->to("/transaction")->with('error', 'Transaction tidak ditemukan');
        }

        $productId = $this->request->getPost('product_id');
        $newQty    = (int) $this->request->getPost('qty');

        $product = $this->product->find($productId);
        if (!$product) {
            return redirect()->back()->withInput()->with('error', 'Product tidak ditemukan');
        }

        // qty lama dikembalikan dulu ke produk lama sebelum dihitung
        $available = $product['qty_in_stock'] + ($old['product_id'] == $productId ? $old['qty'] : 0);
        if ($available < $newQty) {
            return redirect()->back()->withInput()->with('error', 'Stock tidak cukup');
        }

        $data = [
            "user_id" => $this->request->getPost("user_id"),
            "product_id" => $productId,
            "payment_method" => $this->request->getPost("payment_method"),
            "qty" => $newQty,
        ];

        $db = \Config\Database::connect();
        $db->transBegin();

        if ($old['product_id'] == $productId) {
            $this->product->update($productId, [
                'qty_in_stock' => $product['qty_in_stock'] + $old['qty'] - $newQty
            ]);
        } else {
            $oldProduct = $this->product->find($old['product_id']);
            if ($oldProduct) {
                $this->product->update($old['product_id'], [
                    'qty_in_stock' => $oldProduct['qty_in_stock'] + $old['qty']
                ]);
            }
            $this->product->update($productId, [
                'qty_in_stock' => $product['qty_in_stock'] - $newQty
            ]);
        }

        if (!$this->transaction->update($id, $data) || $db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with("errors", $this->transaction->errors());
        }

        $db->transCommit();
        return redirect()->to("/transaction");
    }

    public function delete($id)
    {
        $old = $this->transaction->find($id);
        if (!$old) {
            return redirect()->to("/transaction")->with('error', 'Transaction tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        $product = $this->product->find($old['product_id']);
        if ($product) {
            $this->product->update($old['product_id'], [
                'qty_in_stock' => $product['qty_in_stock'] + $old['qty']
            ]);
        }

        $this->transaction->delete($id);

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to("/transaction")->with('error', 'Gagal menghapus transaction');
        }

        $db->transCommit();
        return redirect()->to("/transaction");
    }
}
