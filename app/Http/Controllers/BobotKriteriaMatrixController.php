<!-- Menampilkan perangkingan -->

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PreferensiController;

class BobotKriteriaMatrixController extends Controller
{
    public function index()
    {
        // Ambil data dari PreferensiController
        $preferensiController = new PreferensiController();
        $preferensiMatrix = $preferensiController->getPreferensiMatrix();

        // Siapkan array untuk menyimpan bobot matrix
        $matrixBobot = [];

        foreach ($preferensiMatrix as $pref) {
            $kriteria = $pref['kriteria'];

            if (!isset($matrixBobot[$kriteria])) {
                $matrixBobot[$kriteria] = [];
            }

            $keys = ['a1_a2', 'a1_a3', 'a1_a4', 'a2_a1', 'a2_a3', 'a2_a4', 'a3_a1', 'a3_a2', 'a3_a4', 'a4_a1', 'a4_a2', 'a4_a3'];
            foreach ($keys as $key) {
                $matrixBobot[$kriteria][$key] = $pref['h_d_' . $key] ?? 0;
            }
        }

        // Ambil data alternatif dari PreferensiController
        $alternatives = ['a1', 'a2', 'a3', 'a4'];

        // Hitung Leaving Flow
        $leavingFlow = [];
        foreach ($alternatives as $alt) {
            $total = 0;
            foreach ($matrixBobot as $kriteria => $values) {
                foreach ($alternatives as $targetAlt) {
                    if ($alt != $targetAlt) {
                        $key = "{$alt}_{$targetAlt}";
                        $total += $values[$key] ?? 0;
                    }
                }
            }
            $leavingFlow[$alt] = $total / (count($alternatives) - 1);
        }

        // Hitung Net Flow
        $netFlow = [];
        foreach ($leavingFlow as $alt => $leaving) {
            $netFlow[$alt] = $leaving;
        }

        // Mensorting ranking
        arsort($netFlow);

        return view('bobot_kriteria_matrix.index', compact('matrixBobot', 'alternatives', 'leavingFlow', 'netFlow'));
    }
}
