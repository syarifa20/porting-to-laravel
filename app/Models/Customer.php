<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'nama',
        'tgl_pembelian',
        'saldo',
        'gender'
    ];
}
