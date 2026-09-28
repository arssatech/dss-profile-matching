<?php
namespace App\Services;

use PDO;

class ProfileMatchingService
{
    private PDO $db;

    // Tabel Bobot Gap standar Profile Matching
    private array $gapTable = [
        0  => 5.0,
        1  => 4.5,
       -1  => 4.0,
        2  => 3.5,
       -2  => 3.0,
        3  => 2.5,
       -3  => 2.0,
        4  => 1.5,
       -4  => 1.0
    ];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Konversi selisih (Gap) ke Nilai Bobot
     */
    public function getWeightFromGap(int $gap): float
    {
        return $this->gapTable[$gap] ?? 1.0;
    }

    /**
     * Menghitung seluruh skoring Profile Matching untuk semua alternatif/kandidat
     */
    public function calculateAll(): array
    {
        // 1. Ambil semua data aspek
        $aspekList = $this->db->query("SELECT * FROM aspek ORDER BY id_aspek ASC")->fetchAll();

        // 2. Ambil semua data kriteria
        $kriteriaList = $this->db->query("SELECT * FROM kriteria ORDER BY id_aspek ASC, id_kriteria ASC")->fetchAll();

        // Grouping kriteria berdasarkan id_aspek
        $kriteriaByAspek = [];
        foreach ($kriteriaList as $k) {
            $kriteriaByAspek[$k['id_aspek']][] = $k;
        }

        // 3. Ambil semua alternatif
        $alternatifList = $this->db->query("SELECT * FROM alternatif ORDER BY id_alternatif ASC")->fetchAll();

        // 4. Ambil semua nilai sampel
        $penilaianRaw = $this->db->query("SELECT * FROM penilaian")->fetchAll();
        $nilaiMap = [];
        foreach ($penilaianRaw as $p) {
            $nilaiMap[$p['id_alternatif']][$p['id_kriteria']] = (int)$p['nilai'];
        }

        $results = [];

        foreach ($alternatifList as $alt) {
            $altId = $alt['id_alternatif'];
            $totalFinalScore = 0;
            $aspekScores = [];

            foreach ($aspekList as $asp) {
                $aspId = $asp['id_aspek'];
                $kriterias = $kriteriaByAspek[$aspId] ?? [];

                $coreSum = 0;
                $coreCount = 0;
                $secSum = 0;
                $secCount = 0;

                $kriteriaDetails = [];

                foreach ($kriterias as $k) {
                    $kId = $k['id_kriteria'];
                    $val = $nilaiMap[$altId][$kId] ?? 0;
                    $target = (int)$k['target'];
                    $gap = $val - $target;
                    $weight = $this->getWeightFromGap($gap);

                    if (strtolower($k['type']) === 'core') {
                        $coreSum += $weight;
                        $coreCount++;
                    } else {
                        $secSum += $weight;
                        $secCount++;
                    }

                    $kriteriaDetails[] = [
                        'nama_kriteria' => $k['nama_kriteria'],
                        'nilai' => $val,
                        'target' => $target,
                        'gap' => $gap,
                        'bobot_gap' => $weight,
                        'type' => $k['type']
                    ];
                }

                $ncf = $coreCount > 0 ? ($coreSum / $coreCount) : 0;
                $nsf = $secCount > 0 ? ($secSum / $secCount) : 0;

                // Hitung Nilai Total Aspek (60% Core + 40% Secondary)
                $nilaiTotalAspek = ($ncf * 0.60) + ($nsf * 0.40);

                // Kontribusi ke nilai akhir berdasarkan Bobot Aspek (%)
                $bobotAspekPersen = (float)$asp['bobot'] / 100;
                $totalFinalScore += ($nilaiTotalAspek * $bobotAspekPersen);

                $aspekScores[$aspId] = [
                    'nama_aspek' => $asp['nama_aspek'],
                    'ncf' => $ncf,
                    'nsf' => $nsf,
                    'nilai_total_aspek' => $nilaiTotalAspek,
                    'kriteria_details' => $kriteriaDetails
                ];
            }

            $results[] = [
                'id_alternatif' => $altId,
                'nama_alternatif' => $alt['nama_alternatif'],
                'aspek_scores' => $aspekScores,
                'final_score' => $totalFinalScore
            ];
        }

        // Urutkan (Ranking) dari nilai tertinggi ke terendah
        usort($results, fn($a, $b) => $b['final_score'] <=> $a['final_score']);

        return [
            'aspek_list' => $aspekList,
            'kriteria_list' => $kriteriaList,
            'rankings' => $results
        ];
    }
}