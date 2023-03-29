<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'nama',
        'tgl_pembelian',
        'saldo',
        'gender',
        'id_customer'
    ];

    protected $hidden = [
        'id_customer',
        'created_at',
        'updated_at'
    ];
    protected $primaryKey = 'id_customer';

    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';


    public function customerDetails()
    {
        return $this->HasMany(DetailCustomer::class);
    }
}
