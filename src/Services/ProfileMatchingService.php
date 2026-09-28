<?php

namespace App\Services;

use PDO;

class ProfileMatchingService
{
    private PDO $db;

    // Pemetaan nilai GAP ke Bobot Nilai SPK Profile Matching
    private array $gapMap = [
        0  => 5.0,
        1  => 4.5,
       -1  => 4.0,
        2  => 3.5,
       -2  => 3.0,
        3  => 2.5,
       -3  => 2.0,
        4  => 1.5,
       -4  => 1.0,
    ];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Menghitung nilai GAP (Selisih Nilai Profil Alternatif dengan Target Kriteria)
     */
    public function calculateGap(float $nilaiActual, float $nilaiTarget): float
    {
        return $nilaiActual - $nilaiTarget;
    }

    /**
     * Mengonversi nilai GAP menjadi Bobot Nilai
     */
    public function getWeightFromGap(float $gap): float
    {
        return $this->gapMap[(int)$gap] ?? 1.0;
    }

    /**
     * Memproses Perhitungan Lengkap Profile Matching
     */
    public function processAll(): array
    {
        // 1. Ambil data Aspek, Kriteria, dan Alternatif beserta Nilainya
        $aspekList = $this->db->query("SELECT * FROM aspek ORDER BY id_aspek ASC")->fetchAll();
        $kriteriaList = $this->db->query("SELECT * FROM kriteria ORDER BY id_kriteria ASC")->fetchAll();
        $alternatifList = $this->db->query("SELECT * FROM alternatif ORDER BY id_alternatif ASC")->fetchAll();

        // Ambil data nilai penilaian
        $rawNilai = $this->db->query("SELECT * FROM penilaian")->fetchAll();
        $nilaiMap = [];
        foreach ($rawNilai as $n) {
            $nilaiMap[$n['id_alternatif']][$n['id_kriteria']] = (float)$n['nilai'];
        }

        $results = [];

        foreach ($alternatifList as $alt) {
            $idAlt = $alt['id_alternatif'];
            $totalNilaiAkhir = 0;
            $aspekResults = [];

            foreach ($aspekList as $aspek) {
                $idAspek = $aspek['id_aspek'];
                $coreSum = 0; $coreCount = 0;
                $secSum = 0;  $secCount = 0;

                // Filter kriteria sesuai aspek
                foreach ($kriteriaList as $kriteria) {
                    if ($kriteria['id_aspek'] != $idAspek) continue;

                    $idKriteria = $kriteria['id_kriteria'];
                    $actual = $nilaiMap[$idAlt][$idKriteria] ?? 0;
                    $target = (float)$kriteria['target'];

                    $gap = $this->calculateGap($actual, $target);
                    $weight = $this->getWeightFromGap($gap);

                    if (strtolower($kriteria['type']) === 'core') {
                        $coreSum += $weight;
                        $coreCount++;
                    } else {
                        $secSum += $weight;
                        $secCount++;
                    }
                }

                $ncf = $coreCount > 0 ? ($coreSum / $coreCount) : 0; // Nilai Core Factor
                $nsf = $secCount > 0 ? ($secSum / $secCount) : 0;   // Nilai Secondary Factor

                // Formula Total Aspek: 60% Core + 40% Secondary (atau atur sesuai standar proyek)
                $nilaiTotalAspek = (0.6 * $ncf) + (0.4 * $nsf);

                $aspekResults[$idAspek] = [
                    'ncf' => $ncf,
                    'nsf' => $nsf,
                    'total_aspek' => $nilaiTotalAspek
                ];

                // Akumulasi ke Nilai Akhir berdasarkan Bobot Aspek (%)
                $totalNilaiAkhir += $nilaiTotalAspek * ((float)$aspek['bobot'] / 100);
            }

            $results[] = [
                'id_alternatif'   => $idAlt,
                'nama_alternatif' => $alt['nama_alternatif'],
                'aspek_detail'    => $aspekResults,
                'nilai_akhir'     => $totalNilaiAkhir
            ];
        }

        // Urutkan berdasarkan nilai akhir tertinggi (Perankingan)
        usort($results, fn($a, $b) => $b['nilai_akhir'] <=> $a['nilai_akhir']);

        return $results;
    }
}