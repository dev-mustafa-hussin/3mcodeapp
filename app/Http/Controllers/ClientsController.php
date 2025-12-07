<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of clients (sample data).
     */
    public function index()
    {
        $clients = [
            ['id' => 1, 'name' => 'أحمد علي', 'email' => 'ahmed@example.com', 'phone' => '01012345678'],
            ['id' => 2, 'name' => 'فاطمة حسن', 'email' => 'fatima@example.com', 'phone' => '01123456789'],
            ['id' => 3, 'name' => 'محمود سمير', 'email' => 'mahmoud@example.com', 'phone' => '01234567890'],
        ];

        return view('clients', compact('clients'));
    }
}
