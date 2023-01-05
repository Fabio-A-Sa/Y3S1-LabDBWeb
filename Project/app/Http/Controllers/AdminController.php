<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Admin;    
use App\Models\User; 
use App\Models\Post; 
use App\Models\Blocked; 

class AdminController extends Controller
{
    public function show() {
        $this->authorize('show', Admin::class);
        return view('pages.admin');
    }

    public function block_user(Request $request) {
        $this->authorize('block_user', Admin::class);
        Blocked::insert([ 'id' => $request->id]);
    }

    public function unblock_user(Request $request) {
        $this->authorize('block_user', Admin::class);
        Blocked::where(['id' => $request->id])->delete();
    }
}