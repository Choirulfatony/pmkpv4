<?php

namespace App\Controllers;

use App\Models\TriasMutuDokumenModel;

class TriasMutu extends AppController
{
    protected $dokumenModel;

    public function __construct()
    {
        $this->dokumenModel = new TriasMutuDokumenModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $userId = session()->get('user_id');
        $role = session()->get('user_role');
        $deptId = session()->get('department_id');

        $builder = $db->table('triasmutu_dokumen td');
        $builder->select("
            td.*,
            mid.department_name AS unit_name,
            COALESCE(qi.indicator_element, lqi.indicator_element) AS indicator_name,
            u.nama AS created_by_name,
            uf.nama AS final_by_name
        ");
        $builder->join('master_institution_department mid', 'mid.department_id = td.unit_id', 'left');
        $builder->join('quality_indicator qi', 'qi.indicator_id = td.indicator_id AND td.indicator_category_id = 4', 'left');
        $builder->join('local_quality_indicator lqi', 'lqi.indicator_id = td.indicator_id AND td.indicator_category_id != 4', 'left');
        $builder->join('users u', 'u.id_user = td.created_by', 'left');
        $builder->join('users uf', 'uf.id_user = td.final_by', 'left');

        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            $builder->where('td.created_by', $userId);
        }

        $builder->orderBy('td.created_at', 'DESC');
        $documents = $builder->get()->getResultArray();

        return $this->render('siimut/triasmutu/index', [
            'judul'     => 'Trias Mutu',
            'icon'      => '<i class="bi bi-file-earmark-text"></i>',
            'documents' => $documents,
        ]);
    }

    public function pengukuran()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $role = session()->get('user_role');
        $deptId = session()->get('department_id');

        $units = $db->table('master_institution_department')
            ->select('department_id, department_name')
            ->where('department_status', 'A')
            ->orderBy('department_name', 'ASC')
            ->get()
            ->getResultArray();

        $tahunList = range(date('Y'), date('Y') - 5);

        $dokumenId = $this->request->getGet('dokumen_id');
        $measurement = null;
        $selected = null;

        if ($dokumenId) {
            $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumenId);
            if ($selected) {
                $measurement = $this->dokumenModel->getMeasurementData(
                    $selected['indicator_category_id'],
                    $selected['indicator_id'],
                    $selected['tahun'],
                    $selected['triwulan'],
                    $selected['unit_id']
                );
            }
        }

        return $this->render('siimut/triasmutu/pengukuran', [
            'judul'       => 'Pengukuran Indikator',
            'icon'        => '<i class="bi bi-bar-chart"></i>',
            'units'       => $units,
            'tahunList'   => $tahunList,
            'selected'    => $selected,
            'measurement' => $measurement,
        ]);
    }

    public function analisisPenyebab()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $role = session()->get('user_role');
        $deptId = session()->get('department_id');

        $units = $db->table('master_institution_department')
            ->select('department_id, department_name')
            ->where('department_status', 'A')
            ->orderBy('department_name', 'ASC')
            ->get()
            ->getResultArray();

        $tahunList = range(date('Y'), date('Y') - 5);

        $dokumenId = $this->request->getGet('dokumen_id');
        $selected = null;

        if ($dokumenId) {
            $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumenId);
        }

        return $this->render('siimut/triasmutu/analisis_penyebab', [
            'judul'     => 'Analisis Penyebab Masalah',
            'icon'      => '<i class="bi bi-diagram-3"></i>',
            'units'     => $units,
            'tahunList' => $tahunList,
            'selected'  => $selected,
        ]);
    }

    public function pdsa()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $units = $db->table('master_institution_department')
            ->select('department_id, department_name')
            ->where('department_status', 'A')
            ->orderBy('department_name', 'ASC')
            ->get()
            ->getResultArray();

        $tahunList = range(date('Y'), date('Y') - 5);

        $dokumenId = $this->request->getGet('dokumen_id');
        $selected = null;

        if ($dokumenId) {
            $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumenId);
        }

        return $this->render('siimut/triasmutu/pdsa', [
            'judul'     => 'Siklus PDSA',
            'icon'      => '<i class="bi bi-arrow-repeat"></i>',
            'units'     => $units,
            'tahunList' => $tahunList,
            'selected'  => $selected,
        ]);
    }

    public function cetak()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $role = session()->get('user_role');
        $deptId = session()->get('department_id');

        $units = $db->table('master_institution_department')
            ->select('department_id, department_name')
            ->where('department_status', 'A')
            ->orderBy('department_name', 'ASC')
            ->get()
            ->getResultArray();

        $tahunList = range(date('Y'), date('Y') - 5);

        $dokumenId = $this->request->getGet('dokumen_id');
        $selected = null;
        $measurement = null;

        if ($dokumenId) {
            $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumenId);
            if ($selected) {
                $measurement = $this->dokumenModel->getMeasurementData(
                    $selected['indicator_category_id'],
                    $selected['indicator_id'],
                    $selected['tahun'],
                    $selected['triwulan'],
                    $selected['unit_id']
                );
            }
        }

        return $this->render('siimut/triasmutu/cetak', [
            'judul'        => 'Cetak Trias Mutu',
            'icon'         => '<i class="bi bi-printer"></i>',
            'units'        => $units,
            'tahunList'    => $tahunList,
            'selected'     => $selected,
            'measurement'  => $measurement,
        ]);
    }

    public function getIndicators()
    {
        $categoryId = (int) $this->request->getPost('category_id');
        $tahun = (int) $this->request->getPost('tahun');
        $unitId = $this->request->getPost('unit_id');

        $table = $categoryId == 4 ? 'quality_indicator' : 'local_quality_indicator';
        $tableGroup = $categoryId == 4 ? 'quality_indicator_group' : 'local_quality_indicator_group';
        $tableResult = $categoryId == 4 ? 'quality_indicator_result' : 'local_quality_indicator_result';

        $db = db_connect();

        $builder = $db->table("$tableResult qir");
        $builder->select("DISTINCT qi.indicator_id, qi.indicator_element");
        $builder->join("$table qi", 'qi.indicator_id = qir.result_indicator_id', 'inner');
        $builder->join("$tableGroup qig", 'qig.group_indicator_id = qi.indicator_id', 'inner');
        $builder->where('qi.indicator_category_id', $categoryId);
        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('qir.result_record_status', 'A');
        if ($unitId && $unitId > 0) {
            $builder->where('qig.group_department_id', $unitId);
        }
        $builder->orderBy('qi.indicator_element', 'ASC');

        $indicators = $builder->get()->getResultArray();
        return $this->response->setJSON($indicators);
    }

    public function getPengukuranData()
    {
        $categoryId = (int) $this->request->getPost('category_id');
        $indicatorId = (int) $this->request->getPost('indicator_id');
        $tahun = (int) $this->request->getPost('tahun');
        $triwulan = (int) $this->request->getPost('triwulan');
        $unitId = (int) $this->request->getPost('unit_id');

        $measurement = $this->dokumenModel->getMeasurementData(
            $categoryId, $indicatorId, $tahun, $triwulan, $unitId
        );

        $selected = null;
        $dokumen = $this->dokumenModel->where([
            'unit_id'               => $unitId,
            'indicator_category_id' => $categoryId,
            'indicator_id'          => $indicatorId,
            'triwulan'              => $triwulan,
            'tahun'                 => $tahun,
        ])->first();

        if ($dokumen) {
            $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumen['id']);
        }

        return $this->response->setJSON([
            'measurement' => $measurement,
            'selected'    => $selected,
        ]);
    }

    public function getOrCreateDokumen()
    {
        $data = [
            'unit_id'               => (int) $this->request->getPost('unit_id'),
            'indicator_category_id' => (int) $this->request->getPost('category_id'),
            'indicator_id'          => (int) $this->request->getPost('indicator_id'),
            'triwulan'              => (int) $this->request->getPost('triwulan'),
            'tahun'                 => (int) $this->request->getPost('tahun'),
        ];

        $id = $this->dokumenModel->getOrCreateDokumen($data);
        return $this->response->setJSON(['dokumen_id' => $id]);
    }

    public function saveAnalisis()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        $permasalahan = $this->request->getPost('permasalahan');
        $kategori = $this->request->getPost('kategori');
        $penyebab = $this->request->getPost('penyebab');

        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $db = db_connect();
        $db->table('triasmutu_analisis')->where('dokumen_id', $dokumenId)->delete();

        if (!empty($kategori) && is_array($kategori)) {
            $data = [];
            foreach ($kategori as $i => $kat) {
                if (!empty($kat) && !empty($penyebab[$i])) {
                    $data[] = [
                        'dokumen_id'    => $dokumenId,
                        'permasalahan'  => $permasalahan,
                        'kategori'      => $kat,
                        'penyebab'      => $penyebab[$i],
                        'created_at'    => date('Y-m-d H:i:s'),
                    ];
                }
            }
            if (!empty($data)) {
                $db->table('triasmutu_analisis')->insertBatch($data);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function savePdsa()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');

        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $db = db_connect();
        $existing = $db->table('triasmutu_pdsa')
            ->where('dokumen_id', $dokumenId)
            ->get()
            ->getRowArray();

        $data = [
            'plan'  => $this->request->getPost('plan'),
            'do'    => $this->request->getPost('do'),
            'study' => $this->request->getPost('study'),
            'act'   => $this->request->getPost('act'),
        ];

        if ($existing) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $db->table('triasmutu_pdsa')
                ->where('dokumen_id', $dokumenId)
                ->update($data);
        } else {
            $data['dokumen_id'] = $dokumenId;
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('triasmutu_pdsa')->insert($data);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function simpanDraft()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $this->dokumenModel->update($dokumenId, ['status' => 'draft']);
        return $this->response->setJSON(['success' => true]);
    }

    public function finalisasi()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $dokumen = $this->dokumenModel->find($dokumenId);
        if (!$dokumen) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak ditemukan']);
        }

        if ($dokumen['status'] === 'final') {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen sudah di-final']);
        }

        $this->dokumenModel->update($dokumenId, [
            'status'   => 'final',
            'final_by' => session()->get('user_id'),
            'final_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Dokumen berhasil di-final']);
    }

    public function deleteDokumen()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya Admin dan Komite Mutu yang dapat menghapus dokumen']);
        }

        $db = db_connect();
        $db->table('triasmutu_analisis')->where('dokumen_id', $dokumenId)->delete();
        $db->table('triasmutu_pdsa')->where('dokumen_id', $dokumenId)->delete();
        $db->table('triasmutu_ttd')->where('dokumen_id', $dokumenId)->delete();
        $db->table('triasmutu_dokumen')->where('id', $dokumenId)->delete();

        return $this->response->setJSON(['success' => true, 'message' => 'Dokumen berhasil dihapus']);
    }

    public function saveTtd()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $db = db_connect();
        $existing = $db->table('triasmutu_ttd')
            ->where('dokumen_id', $dokumenId)
            ->get()
            ->getRowArray();

        $data = [
            'disusun_oleh' => $this->request->getPost('disusun_oleh'),
            'mengetahui'   => $this->request->getPost('mengetahui'),
            'kepala_unit'  => $this->request->getPost('kepala_unit'),
        ];

        if ($existing) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $db->table('triasmutu_ttd')
                ->where('dokumen_id', $dokumenId)
                ->update($data);
        } else {
            $data['dokumen_id'] = $dokumenId;
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('triasmutu_ttd')->insert($data);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function cetakPdf()
    {
        $dokumenId = (int) $this->request->getGet('dokumen_id');
        if (!$dokumenId) {
            return redirect()->to('siimut/trias-mutu/cetak');
        }

        $selected = $this->dokumenModel->getDokumenWithRelations($dokumenId);
        if (!$selected) {
            return redirect()->to('siimut/trias-mutu/cetak');
        }

        $measurement = $this->dokumenModel->getMeasurementData(
            $selected['indicator_category_id'],
            $selected['indicator_id'],
            $selected['tahun'],
            $selected['triwulan'],
            $selected['unit_id']
        );

        $categoryLabels = [4 => 'INM', 5 => 'IMPRS', 6 => 'IMPUNIT'];
        $triwulanLabels = ['', 'I', 'II', 'III', 'IV'];

        $html = view('siimut/triasmutu/pdf_template', [
            'dokumen'        => $selected,
            'measurement'    => $measurement,
            'categoryLabel'  => $categoryLabels[$selected['indicator_category_id']] ?? '-',
            'triwulanLabel'  => $triwulanLabels[$selected['triwulan']] ?? '-',
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("trias_mutu_{$dokumenId}.pdf", ['Attachment' => true]);
    }

    public function getDokumenDetail()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        $selected = $this->dokumenModel->getDokumenWithRelations($dokumenId);

        if (!$selected) {
            return $this->response->setJSON(['success' => false]);
        }

        $measurement = $this->dokumenModel->getMeasurementData(
            $selected['indicator_category_id'],
            $selected['indicator_id'],
            $selected['tahun'],
            $selected['triwulan'],
            $selected['unit_id']
        );

        $categoryLabels = [4 => 'INM', 5 => 'IMPRS', 6 => 'IMPUNIT'];

        return $this->response->setJSON([
            'success'     => true,
            'dokumen'     => $selected,
            'measurement' => $measurement,
            'categoryLabel' => $categoryLabels[$selected['indicator_category_id']] ?? '-',
        ]);
    }
}
