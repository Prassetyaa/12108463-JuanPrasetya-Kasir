<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

        protected $fillable = [
            'total_price',
            'pelanggan_id'
        ];

        public function detailPenjualan()
        {
            return $this->hasMany(DetailPenjualan::class);
        }

        public function pelanggan()
        {
            return $this->belongsTo(Pelanggan::class);
        }

        public function createdByUser()
        {
            return $this->belongsTo(User::class, 'created_by_user_id');
        }
    }
