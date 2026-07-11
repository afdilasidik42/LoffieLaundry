<?php

namespace App\Services;

use App\Models\DetailTransaksi;
use App\Models\Pesanan;

/**
 * GM(1,4) Grey Prediction Service — Full Mathematical Engine
 *
 * Implements the Grey System Theory GM(1,N) model with N=4 variables:
 *   X1 = durasi aktual (jam)     — TARGET (dependent variable)
 *   X2 = berat cucian (kg)       — influencing factor
 *   X3 = complexity_score (1–5)  — influencing factor
 *   X4 = kapasitas_mesin (%)     — influencing factor
 *
 * Algorithm Steps:
 *   1. Retrieve historical data sequences from completed orders
 *   2. AGO  — Accumulated Generating Operation on all sequences
 *   3. Z    — Mean (background value) sequence for X1
 *   4. B, Y — Construct data matrix B and observation vector Y
 *   5. θ    — Estimate parameters via Least Squares: θ = (BᵀB)⁻¹BᵀY
 *   6. Discrete Solution — Compute fitted/predicted X1^(1) values
 *   7. IAGO — Inverse AGO to recover original-scale prediction
 *
 * Reference: Deng Julong, "Control problems of grey systems",
 *            Systems & Control Letters 1(5), 1982.
 */
class GmPredictionService
{
    /**
     * Minimum number of historical data points required for GM(1,4).
     *
     * The GM(1,N) model needs at least N data points to form a solvable
     * system of equations. With 4 parameters (a, b2, b3, b4) we need
     * n >= 4 observations, yielding (n-1) >= 3 rows in matrix B.
     * We use n = 4 minimum (producing 3 rows for 4 unknowns); more data
     * yields a better least-squares fit.
     */
    private const MIN_DATA_POINTS = 4;

    // ========================================================================
    //  PUBLIC API
    // ========================================================================

    public static function predict(array $params): float
    {
        $beban      = (float) ($params['beban'] ?? 0);
        $complexity = (int)   ($params['complexity_score'] ?? 1);
        $kapasitas  = (float) ($params['kapasitas_mesin'] ?? 0);

        // ------------------------------------------------------------------
        // Step 1 — Retrieve historical data from completed orders
        // ------------------------------------------------------------------
        $historicalData = self::fetchHistoricalData();

        if (count($historicalData) < self::MIN_DATA_POINTS) {
            // Insufficient data → return heuristic fallback
            return self::fallbackEstimate($complexity);
        }

        // Build raw data sequences (0-indexed arrays)
        $X1 = array_column($historicalData, 'durasi_jam');    // Target: actual hours
        $X2 = array_column($historicalData, 'beban');         // Beban (kg/pcs)
        $X3 = array_column($historicalData, 'complexity');    // Complexity score
        $X4 = array_column($historicalData, 'kapasitas');     // Machine capacity %

        $n = count($X1);  // number of historical data points

        // Append the new (to-be-predicted) data point's influencing factors
        // We need X2, X3, X4 at position n+1 for the prediction formula
        $X2[] = $beban;
        $X3[] = $complexity;
        $X4[] = $kapasitas;

        // ------------------------------------------------------------------
        // Step 2 — AGO (Accumulated Generating Operation)
        // ------------------------------------------------------------------
        $X1_ago = self::ago($X1);               // length = n
        $X2_ago = self::ago($X2);               // length = n+1
        $X3_ago = self::ago($X3);               // length = n+1
        $X4_ago = self::ago($X4);               // length = n+1

        // ------------------------------------------------------------------
        // Step 3 — Mean (background value) sequence Z for X1
        //          Z1[k] = 0.5 * (X1_ago[k] + X1_ago[k-1])  for k = 1..n-1
        //          (using 0-indexed: k = 1..n-1 in X1_ago)
        // ------------------------------------------------------------------
        $Z1 = [];
        for ($k = 1; $k < $n; $k++) {
            $Z1[] = 0.5 * ($X1_ago[$k] + $X1_ago[$k - 1]);
        }

        // ------------------------------------------------------------------
        // Step 4 — Construct matrix B and vector Y
        //          Y[i]  = X1[k]  for k = 1..n-1  (0-indexed: k from 1)
        //          B row = [-Z1[i], X2_ago[k], X3_ago[k], X4_ago[k]]
        // ------------------------------------------------------------------
        $rows = $n - 1;  // number of equations
        $B = [];
        $Y = [];

        for ($i = 0; $i < $rows; $i++) {
            $k = $i + 1;  // index into AGO sequences (k = 1..n-1)
            $B[] = [
                -$Z1[$i],
                $X2_ago[$k],
                $X3_ago[$k],
                $X4_ago[$k],
            ];
            $Y[] = [$X1[$k]];  // column vector
        }

        // ------------------------------------------------------------------
        // Step 5 — Least Squares estimation: θ = (BᵀB)⁻¹ × BᵀY
        // ------------------------------------------------------------------
        $Bt   = self::matTranspose($B);          // 4 × rows
        $BtB  = self::matMul($Bt, $B);           // 4 × 4
        $BtY  = self::matMul($Bt, $Y);           // 4 × 1

        $BtB_inv = self::matInverse($BtB);       // 4 × 4

        if ($BtB_inv === null) {
            // Matrix is singular (non-invertible) → fallback
            return self::fallbackEstimate($complexity);
        }

        $theta = self::matMul($BtB_inv, $BtY);  // 4 × 1

        // ------------------------------------------------------------------
        // Step 6 — Extract estimated parameters
        // ------------------------------------------------------------------
        $a  = $theta[0][0];   // development coefficient
        $b2 = $theta[1][0];   // driving coefficient for X2
        $b3 = $theta[2][0];   // driving coefficient for X3
        $b4 = $theta[3][0];   // driving coefficient for X4

        // Guard: if a ≈ 0, the exponential model degenerates → fallback
        if (abs($a) < 1e-10) {
            return self::fallbackEstimate($complexity);
        }

        // ------------------------------------------------------------------
        // Step 7 — Discrete solution of GM(1,4)
        // ------------------------------------------------------------------
        $X1_hat_ago = [];
        $X1_hat_ago[0] = $X1[0];  // X1_hat^(1)(1) = X1^(0)(1) by definition

        // Compute fitted values for k=1..n AND prediction at k=n (position n+1)
        // We need positions up to n (0-indexed) in AGO domain
        // X2_ago, X3_ago, X4_ago have length n+1, so index n is valid
        for ($idx = 1; $idx <= $n; $idx++) {
            $sumBiXi = $b2 * $X2_ago[$idx] + $b3 * $X3_ago[$idx] + $b4 * $X4_ago[$idx];
            $X1_hat_ago[$idx] = ($X1[0] - $sumBiXi / $a) * exp(-$a * $idx) + $sumBiXi / $a;
        }

        // ------------------------------------------------------------------
        // Step 8 — IAGO (Inverse Accumulated Generating Operation)
        // ------------------------------------------------------------------
        // IAGO: recover the original-scale predicted value at index n
        $predicted = $X1_hat_ago[$n] - $X1_hat_ago[$n - 1];

        // Ensure prediction is positive and reasonable (max 240 hours / 10 days)
        if ($predicted <= 0 || !is_finite($predicted) || $predicted > 240) {
            return self::fallbackEstimate($complexity);
        }

        // Round to 4 decimal places
        return round($predicted, 4);
    }

    // ========================================================================
    //  PRIVATE — Data Retrieval
    // ========================================================================

    /**
     * Fetch historical data from completed/picked-up orders.
     *
     * Returns an array of associative arrays with keys:
     *   durasi_jam  — actual completion duration in hours
     *   berat       — weight in kg
     *   complexity  — layanan complexity score
     *   kapasitas   — machine capacity utilisation %
     *
     * Only orders where actual_selesai IS NOT NULL are included.
     * Ordered by created_at ASC (oldest first) for proper time-series.
     *
     * @return array<int, array{durasi_jam: float, berat: float, complexity: int, kapasitas: float}>
     */
    private static function fetchHistoricalData(): array
    {
        $window = (int) env('GM_TRAINING_WINDOW', 30);

        $completedOrders = Pesanan::with(['detailTransaksi.layanan'])
            ->whereNotNull('actual_selesai')
            ->orderBy('created_at', 'desc') // Fetch newest first
            ->limit($window)
            ->get()
            ->reverse() // Restore chronological order for AGO
            ->values(); // Reset array keys

        $data = [];

        foreach ($completedOrders as $pesanan) {
            $detail = $pesanan->detailTransaksi->first();

            if (!$detail || !$detail->layanan) {
                continue;
            }

            // Calculate actual duration in hours
            $tanggalMasuk  = $pesanan->tanggal_masuk;
            $actualSelesai = $pesanan->actual_selesai;

            $durasiJam = $actualSelesai->diffInSeconds($tanggalMasuk) / 3600.0;

            // Skip zero/negative durations (data anomalies)
            if ($durasiJam <= 0) {
                continue;
            }

            $bebanCucian = $detail->layanan->tipe_layanan === 'satuan' 
                ? (float) $detail->jumlah 
                : (float) $detail->berat;

            if ($bebanCucian <= 0) {
                continue;
            }

            $data[] = [
                'durasi_jam' => round($durasiJam, 4),
                'beban'      => $bebanCucian,
                'complexity' => (int) $detail->layanan->complexity_score,
                'kapasitas'  => (float) $detail->kapasitas_mesin,
            ];
        }

        return $data;
    }

    /**
     * Return a heuristic fallback duration (in hours) based on service complexity.
     *
     * Used when historical data is insufficient (< 4 records) for GM(1,4).
     * Values based on typical laundry processing times:
     *   1 = Cuci Reguler (~24 jam / 1 hari)
     *   2 = Setrika (~3 jam)
     *   3 = Express (~6 jam)
     *   5 = Dry Cleaning (~48 jam / 2 hari)
     */
    private static function fallbackEstimate(int $complexity): float
    {
        return match ($complexity) {
            1       => 24.0,
            2       => 3.0,
            3       => 6.0,
            5       => 48.0,
            default => 12.0,
        };
    }

    // ========================================================================
    //  PRIVATE — Mathematical Operations (Grey System)
    // ========================================================================

    /**
     * AGO — Accumulated Generating Operation.
     *
     * Given a raw sequence X^(0) = [x1, x2, ..., xn], produce the
     * 1-AGO sequence X^(1) where X^(1)[k] = Σ X^(0)[j] for j=0..k.
     *
     * @param  array<int, float|int>  $sequence  Raw data sequence
     * @return array<int, float>      AGO sequence (same length as input)
     */
    private static function ago(array $sequence): array
    {
        $result = [];
        $cumSum = 0.0;

        foreach ($sequence as $value) {
            $cumSum += (float) $value;
            $result[] = $cumSum;
        }

        return $result;
    }

    // ========================================================================
    //  PRIVATE — Pure PHP Matrix Algebra
    // ========================================================================

    /**
     * Matrix Multiplication: C = A × B
     *
     * A is (m × p), B is (p × q), result C is (m × q).
     * Each matrix is a 2D array: $M[row][col].
     *
     * @param  array  $A  Matrix A (m × p)
     * @param  array  $B  Matrix B (p × q)
     * @return array  Result matrix C (m × q)
     */
    private static function matMul(array $A, array $B): array
    {
        $m = count($A);
        $p = count($A[0]);
        $q = count($B[0]);

        $C = array_fill(0, $m, array_fill(0, $q, 0.0));

        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $q; $j++) {
                $sum = 0.0;
                for ($k = 0; $k < $p; $k++) {
                    $sum += $A[$i][$k] * $B[$k][$j];
                }
                $C[$i][$j] = $sum;
            }
        }

        return $C;
    }

    /**
     * Matrix Transpose: Mᵀ
     *
     * @param  array  $M  Matrix (m × n)
     * @return array  Transposed matrix (n × m)
     */
    private static function matTranspose(array $M): array
    {
        $rows = count($M);
        $cols = count($M[0]);

        $T = [];

        for ($j = 0; $j < $cols; $j++) {
            $T[$j] = [];
            for ($i = 0; $i < $rows; $i++) {
                $T[$j][$i] = $M[$i][$j];
            }
        }

        return $T;
    }

    /**
     * Matrix Inverse via Gauss-Jordan Elimination.
     *
     * Solves for M⁻¹ by augmenting [M | I] and row-reducing to [I | M⁻¹].
     * Works for any n×n matrix. Returns null if the matrix is singular.
     *
     * @param  array  $M  Square matrix (n × n)
     * @return array|null  Inverse matrix (n × n), or null if singular
     */
    private static function matInverse(array $M): ?array
    {
        $n = count($M);

        // Build augmented matrix [M | I]
        $aug = [];
        for ($i = 0; $i < $n; $i++) {
            $aug[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                $aug[$i][$j] = (float) $M[$i][$j];
            }
            for ($j = 0; $j < $n; $j++) {
                $aug[$i][$n + $j] = ($i === $j) ? 1.0 : 0.0;
            }
        }

        // Forward elimination with partial pivoting
        for ($col = 0; $col < $n; $col++) {
            // Find the row with the largest absolute value in this column (pivot)
            $maxVal = abs($aug[$col][$col]);
            $maxRow = $col;

            for ($row = $col + 1; $row < $n; $row++) {
                if (abs($aug[$row][$col]) > $maxVal) {
                    $maxVal = abs($aug[$row][$col]);
                    $maxRow = $row;
                }
            }

            // Check for singularity
            if ($maxVal < 1e-12) {
                return null; // Matrix is singular
            }

            // Swap rows if needed
            if ($maxRow !== $col) {
                [$aug[$col], $aug[$maxRow]] = [$aug[$maxRow], $aug[$col]];
            }

            // Scale pivot row so that aug[col][col] = 1
            $pivotVal = $aug[$col][$col];
            for ($j = 0; $j < 2 * $n; $j++) {
                $aug[$col][$j] /= $pivotVal;
            }

            // Eliminate all other rows in this column
            for ($row = 0; $row < $n; $row++) {
                if ($row === $col) {
                    continue;
                }
                $factor = $aug[$row][$col];
                for ($j = 0; $j < 2 * $n; $j++) {
                    $aug[$row][$j] -= $factor * $aug[$col][$j];
                }
            }
        }

        // Extract the inverse from the right half of the augmented matrix
        $inv = [];
        for ($i = 0; $i < $n; $i++) {
            $inv[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                $inv[$i][$j] = $aug[$i][$n + $j];
            }
        }

        return $inv;
    }
}
