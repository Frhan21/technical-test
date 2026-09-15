<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User; 

class UserController extends BaseController

{
    protected $user;
    public function __construct() {
        $this->user = new User(); 
    }
    
    public function index()
    {
        $data = [
            'users' => $this->user->findAll(),
        ];
        return view("users/index", $data); 
    }

    public function create()
    {
        return view('users/create'); 
    }

    public function store() 
    {
        $data = [
            'name' => $this->request->getPost('name'),
        ];

        if (! $this->user->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->user->errors());
        }

        return redirect()->to('/users')->with('success', 'User berhasil ditambahkan.'); 
    }

    public function edit($id)
    {
        $user = $this->user->find($id);
        $data = [
            'user' => $user,
        ];
        return view('users/edit', $data);
    }

    public function update($id)
    {
        $data = [
            'name' => $this->request->getPost('name'),
        ];

        if (! $this->user->update($id, $data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->user->errors());
        }

        return redirect()->to('/users')->with('success', 'User berhasil diupdate.');
    }

    public function delete($id)
    {
        $this->user->delete($id);
        return redirect()->to('/users');
    }
}
