<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBarangModal extends Model
{
    protected $fillable = [
        'kode_barang', 'nomor_seri', 'merk', 'tipe', 'tahun_perolehan', 'ruang_id', 'kondisi', 'nie', 'atribut', 'keterangan', 'harga', 'kapitalisasi', 'jumlah'
    ];

    protected $casts = [
        'atribut' => 'array',
    ];

    // Status constants
    const KONDISI_BAIK = 'baik';
    const KONDISI_PERBAIKAN = 'perbaikan';
    const KONDISI_RUSAK_BERAT = 'rusak_berat';

    // Attribute constants
    const ATRIBUT_NIE = 'nie';
    const ATRIBUT_BPKB = 'bpkb';
    const ATRIBUT_STNK = 'stnk';
    const ATRIBUT_SERIAL_NUMBER = 'serial_number';
    const ATRIBUT_MAC_ADDRESS = 'mac_address';
    const ATRIBUT_IP_ADDRESS = 'ip_address';
    const ATRIBUT_WARRANTY = 'warranty';
    const ATRIBUT_LICENSE = 'license';

    // Methods
    public function getKondisiLabel()
    {
        return match($this->kondisi) {
            self::KONDISI_BAIK => 'Baik',
            self::KONDISI_PERBAIKAN => 'Perbaikan',
            self::KONDISI_RUSAK_BERAT => 'Rusak Berat',
            default => 'Tidak Diketahui',
        };
    }

    public function getKondisiBadge()
    {
        return match($this->kondisi) {
            self::KONDISI_BAIK => 'badge-success',
            self::KONDISI_PERBAIKAN => 'badge-warning',
            self::KONDISI_RUSAK_BERAT => 'badge-error',
            default => 'badge-secondary',
        };
    }

    // Attribute helper methods
    public function setAtribut($key, $value)
    {
        $atribut = $this->atribut ?? [];
        $atribut[$key] = $value;
        $this->atribut = $atribut;
        return $this;
    }

    public function getAtribut($key, $default = null)
    {
        $atribut = $this->atribut ?? [];
        return $atribut[$key] ?? $default;
    }

    public function removeAtribut($key)
    {
        $atribut = $this->atribut ?? [];
        unset($atribut[$key]);
        $this->atribut = $atribut;
        return $this;
    }

    public function hasAtribut($key)
    {
        $atribut = $this->atribut ?? [];
        return isset($atribut[$key]);
    }

    // Specific attribute getters
    public function getNie()
    {
        return $this->getAtribut(self::ATRIBUT_NIE) ?? $this->nie;
    }

    public function getBpkb()
    {
        return $this->getAtribut(self::ATRIBUT_BPKB);
    }

    public function getStnk()
    {
        return $this->getAtribut(self::ATRIBUT_STNK);
    }

    public function getSerialNumber()
    {
        return $this->getAtribut(self::ATRIBUT_SERIAL_NUMBER) ?? $this->nomor_seri;
    }

    public function getMacAddress()
    {
        return $this->getAtribut(self::ATRIBUT_MAC_ADDRESS);
    }

    public function getIpAddress()
    {
        return $this->getAtribut(self::ATRIBUT_IP_ADDRESS);
    }

    public function getWarranty()
    {
        return $this->getAtribut(self::ATRIBUT_WARRANTY);
    }

    public function getLicense()
    {
        return $this->getAtribut(self::ATRIBUT_LICENSE);
    }

    // Get all attributes as formatted array
    public function getFormattedAtribut()
    {
        $atribut = $this->atribut ?? [];
        $formatted = [];
        
        $labels = [
            self::ATRIBUT_NIE => 'NIE',
            self::ATRIBUT_BPKB => 'Nomor BPKB',
            self::ATRIBUT_STNK => 'Nomor STNK',
            self::ATRIBUT_SERIAL_NUMBER => 'Nomor Seri',
            self::ATRIBUT_MAC_ADDRESS => 'MAC Address',
            self::ATRIBUT_IP_ADDRESS => 'IP Address',
            self::ATRIBUT_WARRANTY => 'Garansi',
            self::ATRIBUT_LICENSE => 'Lisensi',
        ];

        foreach ($atribut as $key => $value) {
            $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
            $formatted[] = [
                'key' => $key,
                'label' => $label,
                'value' => $value
            ];
        }

        return $formatted;
    }

    public function masterBarangModal()
    {
        return $this->belongsTo(MasterBarangModal::class, 'kode_barang', 'kode_barang');
    }

    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'ruang_id');
    }

    public function puskesmas()
    {
        return $this->hasOneThrough(Puskesmas::class, Ruang::class, 'id', 'id', 'ruang_id', 'puskesmas_id');
    }

    public function monitoringAlkes()
    {
        return $this->hasMany(MonitoringAlkes::class, 'detail_barang_modal_id');
    }
}
