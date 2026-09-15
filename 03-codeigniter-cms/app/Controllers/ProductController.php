<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Product;
use CodeIgniter\HTTP\ResponseInterface;

class ProductController extends BaseController
{
    protected $products; 
    public function __construct() 
    {
        $this->products = new Product;
    }

    public function index()
    {
        $data = [
            'products' => $this->products->findAll(),
        ];
        return view('products/index', $data); 
    }

    public function create() 
    {
        return view('products/create');
    }

    public function store() 
    {
        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'price' => $this->request->getPost('price'),
            'qty_in_stock' => $this->request->getPost('qty_in_stock'),
        ];

        if (! $this->products->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->products->errors());
        }

        return redirect()->to('/products')->with('success', 'Product berhasil ditambahkan.'); 
    }

    public function edit($id) 
    {
        $product = $this->products->find($id);
        $data = [
            'product' => $product,
        ];
        return view('products/edit', $data);
    }

    public function update($id) 
    {
        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'price' => $this->request->getPost('price'),
            'qty_in_stock' => $this->request->getPost('qty_in_stock'),
        ];

        if (! $this->products->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->products->errors());
        }

        return redirect()->to('/products')->with('success', 'Product berhasil diupdate.');
    }

    public function delete($id) 
    {
        $this->products->delete($id);
        return redirect()->to('/products')->with('success', 'Product berhasil dihapus.');
    } 
}
