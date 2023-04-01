<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    protected $uppercaseFields = ['no_invoice', 'nama','gender'];

    public function setNoInvoiceAttribute($value)
    {
        if (in_array('no_invoice', $this->uppercaseFields)) {
            $this->attributes['no_invoice'] = strtoupper($value);
        } else {
            $this->attributes['no_invoice'] = $value;
        }
    }

    public function seNamaAttribute($value)
    {
        if (in_array('nama', $this->uppercaseFields)) {
            $this->attributes['nama'] = strtoupper($value);
        } else {
            $this->attributes['nama'] = $value;
        }
    }

    public function setGenderAttribute($value)
    {
        if (in_array('gender_id', $this->uppercaseFields)) {
            $this->attributes['gender'] = strtoupper($value);
        } else {
            $this->attributes['gender'] = $value;
        }
    }


    
}
