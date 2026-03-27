<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class MloadModuleIkp extends Model
{
    protected $DBGroup = 'simrs_db';

    protected $dbDefault;
    protected $dbHris;
    protected $dbSimrs;

    public function __construct()
    {
        parent::__construct();

        $this->dbDefault = Database::connect();        // DB 1 (default)
        $this->dbHris  = Database::connect('db2');       // MySQL
        $this->dbSimrs = Database::connect('simrs_db'); // PostgreSQL
    }

    public function cari_pasien($kd_pasien, $tgl_masuk, $kd_bagian)
    {
        $sql = "
        SELECT
            kunjungan.kd_pasien,
            pasien.nama,
            pasien.tgl_lahir,
            to_char(kunjungan.tgl_masuk, 'YYYY-MM-DD') AS tgl_masuk,
            to_char(kunjungan.jam_masuk, 'HH24:MI:SS') AS jam_masuk,

            date_part('year', age(kunjungan.tgl_masuk, pasien.tgl_lahir)) AS umur_tahun,

            CASE
                WHEN date_part('year', age(kunjungan.tgl_masuk, pasien.tgl_lahir)) BETWEEN 0 AND 1 THEN 'Bayi'
                WHEN date_part('year', age(kunjungan.tgl_masuk, pasien.tgl_lahir)) BETWEEN 2 AND 12 THEN 'Anak'
                WHEN date_part('year', age(kunjungan.tgl_masuk, pasien.tgl_lahir)) BETWEEN 13 AND 17 THEN 'Remaja'
                WHEN date_part('year', age(kunjungan.tgl_masuk, pasien.tgl_lahir)) BETWEEN 18 AND 59 THEN 'Dewasa'
                ELSE 'Lansia'
            END AS kelompok_umur,

            unit.nama_unit,
            kamar.nama_kamar,

            CASE
                WHEN pasien.jenis_kelamin = 't' THEN 'laki-laki'
                ELSE 'perempuan'
            END AS kelamin,

            customer.customer AS penjamin,

            CASE 
                WHEN unit.kd_bagian = '2' THEN 'RAWAT JALAN'
                WHEN unit.kd_bagian = '3' THEN 'IGD'
                ELSE 'RAWAT INAP'
            END AS unitkerja
        FROM kunjungan
        JOIN unit USING (kd_unit)
        JOIN customer USING (kd_customer)
        JOIN pasien USING (kd_pasien)
        LEFT JOIN nginap ON 
            nginap.kd_pasien = kunjungan.kd_pasien
            AND nginap.tgl_masuk = kunjungan.tgl_masuk
            AND nginap.kd_unit = kunjungan.kd_unit
            AND nginap.urut_masuk = kunjungan.urut_masuk
            AND nginap.akhir = 't'
        LEFT JOIN kamar USING (no_kamar)
        WHERE
            kunjungan.kd_pasien = ?
            AND kunjungan.tgl_masuk = ?
            AND unit.kd_bagian = ?
        ";

        $query = $this->dbSimrs->query($sql, [
            $kd_pasien,
            $tgl_masuk,
            $kd_bagian
        ]);

        return $query->getRow();
    }


    public function get_departments($keyword = null)
    {
        $builder = $this->dbDefault
            ->table('unit_karu')
            ->select('
            unit_karu.department_id,
            unit_karu.hris_user_id,
            master_institution_department.department_name
        ')
            ->join(
                'master_institution_department',
                'master_institution_department.department_id = unit_karu.department_id',
                'left'
            )
            ->where('unit_karu.role_id', 1) // 1 = KARU
            ->where('unit_karu.aktif', 1);

        if (!empty($keyword)) {
            $builder->like(
                'LOWER(master_institution_department.department_name)',
                strtolower($keyword)
            );
        }

        return $builder
            ->orderBy('master_institution_department.department_name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
