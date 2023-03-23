<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class customerController extends Controller
{
    public function index(){

        //get all posts from Models
        $customers = DB::table('customers')->get();
        
        return view('customers.index', ['customers' => $customers]);
       
    }
}
