<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        // Data driver atau pesan bisa dioper ke view
        return view('admin.chat.index');
    }
}
