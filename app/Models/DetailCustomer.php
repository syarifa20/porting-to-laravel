<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_brg',
        'harga',
        'qty',
        'customer_id',
    ];
    public $timestamps = true;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
