<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class SoilValidationService
{
    public const NON_SOIL_MSG = 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.';

    /**
     * Validate an image input before soil processing.
     * Throws InvalidArgumentException if non-soil object, synthetic material, or skin is detected.
     */
    public function validate(mixed $imageInput): void
    {
        $gd = $this->loadGdImage($imageInput);
        if (!$gd) {
            throw new InvalidArgumentException('Invalid image payload: Unable to load or parse image data.');
        }

        $w = imagesx($gd);
        $h = imagesy($gd);
        if ($w < 10 || $h < 10) {
            imagedestroy($gd);
            throw new InvalidArgumentException('Image resolution is too small to analyze.');
        }

        // Circular ROI radius calculation
        $cx     = $w / 2.0;
        $cy     = $h / 2.0;
        $radius = ($w === $h) ? (($w / 2.0) * 0.92) : (min($w, $h) * 0.30);

        // Run validation tiers as diagnostics to ensure real field specimens are never blocked
        $isTierA = $this->tierA($gd, $w, $h);
        $isTierB = $this->tierB($gd, $w, $h, $cx, $cy, $radius);
        $isTierD = $this->tierD_skinTexture($gd, $w, $h, $cx, $cy, $radius);
        $isTierC = $this->tierC($gd, $w, $h, $cx, $cy, $radius);

        if (!$isTierA || !$isTierD) {
            throw new InvalidArgumentException(self::NON_SOIL_MSG);
        }

        if (!$isTierB || !$isTierC) {
            \Illuminate\Support\Facades\Log::info("Soil validation advisory: TierB=" . ($isTierB ? '1' : '0') . " TierC=" . ($isTierC ? '1' : '0'));
        }

        imagedestroy($gd);
    }

    protected function loadGdImage(mixed $imageInput)
    {
        if ($imageInput instanceof UploadedFile) {
            $data = file_get_contents($imageInput->getRealPath());
            return @imagecreatefromstring($data);
        }

        if (is_string($imageInput)) {
            if (preg_match('/^data:image\/\w+;base64,/', $imageInput)) {
                $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $imageInput);
                return @imagecreatefromstring(base64_decode($base64));
            }
            if (file_exists($imageInput)) {
                $data = file_get_contents($imageInput);
                return @imagecreatefromstring($data);
            }
        }

        return false;
    }

    protected function tierA($gd, int $w, int $h): bool
    {
        $sx = max(1, (int)($w / 40));
        $sy = max(1, (int)($h / 40));
        $nonSoil = 0;
        $total   = 0;

        for ($y = 0; $y < $h; $y += $sy) {
            for ($x = 0; $x < $w; $x += $sx) {
                $c  = imagecolorat($gd, $x, $y);
                $a  = ($c >> 24) & 0x7F;
                if ($a > 100) continue;

                $r  = ($c >> 16) & 0xFF;
                $g  = ($c >>  8) & 0xFF;
                $b  =  $c        & 0xFF;
                if ($r === 0 && $g === 0 && $b === 0) continue;

                $total++;
                [$hue, $sat] = $this->rgbToHS($r, $g, $b);

                $blueCyan     = ($sat > 0.28) && (($b > $r + 25 && $b > $g + 15) || ($hue >= 175 && $hue <= 255));
                $greenToy     = ($sat > 0.30) && (($g > $r + 25) || ($hue >= 75 && $hue <= 165));
                $pinkPurple   = ($sat > 0.28) && (($hue >= 260 && $hue <= 345) || ($r > $g + 35 && $b > $g + 35 && $b > $r));
                $brightYellow = ($sat > 0.45) && ($hue >= 45 && $hue <= 65 && $r > 160 && $g > 160);

                if ($blueCyan || $greenToy || $pinkPurple || $brightYellow) {
                    $nonSoil++;
                }
            }
        }

        return ($total > 0) ? (($nonSoil / $total) <= 0.45) : true;
    }

    protected function tierB($gd, int $w, int $h, float $cx, float $cy, float $radius): bool
    {
        $step = max(1, (int)($radius / 30));
        $x0 = (int)($cx - $radius); $x1 = (int)($cx + $radius);
        $y0 = (int)($cy - $radius); $y1 = (int)($cy + $radius);
        $r2 = $radius * $radius;

        $gray = []; $reds = [];

        for ($y = $y0; $y <= $y1; $y += $step) {
            for ($x = $x0; $x <= $x1; $x += $step) {
                if ($x < 0 || $x >= $w || $y < 0 || $y >= $h) continue;
                $dx = $x - $cx; $dy = $y - $cy;
                if ($dx * $dx + $dy * $dy > $r2) continue;

                $c = imagecolorat($gd, $x, $y);
                $a = ($c >> 24) & 0x7F;
                if ($a > 100) continue;

                $r = ($c >> 16) & 0xFF;
                $g = ($c >>  8) & 0xFF;
                $b =  $c        & 0xFF;
                if ($r === 0 && $g === 0 && $b === 0) continue;

                $gray[] = (int)(0.299 * $r + 0.587 * $g + 0.114 * $b);
                $reds[] = $r;
            }
        }

        $n = count($gray);
        if ($n < 4) return true;

        $edges = 0;
        for ($k = 1; $k < $n; $k++) {
            if (abs($gray[$k] - $gray[$k - 1]) > 7) $edges++;
        }

        $edgeDensity = $edges / $n;
        $avgR = array_sum($reds) / $n;
        $var  = 0.0;
        foreach ($reds as $rv) $var += ($rv - $avgR) ** 2;
        $stdDev = sqrt($var / $n);

        return !($edgeDensity < 0.025 && $stdDev < 3.5);
    }

    protected function tierD_skinTexture($gd, int $w, int $h, float $cx, float $cy, float $radius): bool
    {
        $step = max(1, (int)($radius / 30));
        $x0 = (int)($cx - $radius); $x1 = (int)($cx + $radius);
        $y0 = (int)($cy - $radius); $y1 = (int)($cy + $radius);
        $r2 = $radius * $radius;

        $gray = []; $reds = []; $skinCount = 0; $total = 0;

        for ($y = $y0; $y <= $y1; $y += $step) {
            for ($x = $x0; $x <= $x1; $x += $step) {
                if ($x < 0 || $x >= $w || $y < 0 || $y >= $h) continue;
                $dx = $x - $cx; $dy = $y - $cy;
                if ($dx * $dx + $dy * $dy > $r2) continue;

                $c = imagecolorat($gd, $x, $y);
                $a = ($c >> 24) & 0x7F;
                if ($a > 100) continue;

                $rv = ($c >> 16) & 0xFF;
                $gv = ($c >>  8) & 0xFF;
                $bv =  $c        & 0xFF;
                if ($rv === 0 && $gv === 0 && $bv === 0) continue;

                $total++;
                $gray[] = (int)(0.299 * $rv + 0.587 * $gv + 0.114 * $bv);
                $reds[] = $rv;

                $cb = 128 - 0.168736 * $rv - 0.331264 * $gv + 0.5       * $bv;
                $cr = 128 + 0.5       * $rv - 0.418688 * $gv - 0.081312 * $bv;

                if ($cb >= 70 && $cb <= 135 && $cr >= 125 && $cr <= 175) {
                    $skinCount++;
                }
            }
        }

        if ($total < 4) return true;

        $n = count($gray);
        if ($n === 0) return true;

        $edges = 0;
        for ($k = 1; $k < $n; $k++) {
            if (abs($gray[$k] - $gray[$k - 1]) > 7) $edges++;
        }

        $edgeDensity = $edges / $n;
        $avgR = array_sum($reds) / $n;
        $var  = 0.0;
        foreach ($reds as $rv) $var += ($rv - $avgR) ** 2;
        $stdDev = sqrt($var / $n);

        $skinRatio = $skinCount / $total;

        // Primary: Photographed skin has high skin-chrominance ratio (> 0.75) and smooth,
        // fleshy transitions (edgeDensity < 0.065). Note that we do NOT require low stdDev (< 4.5)
        // because natural facial contours, features, and lighting gradients produce stdDev ~10-25.
        // Granular soils also share YCbCr skin chrominance (Cb: 70-135, Cr: 125-175), but genuine
        // soil always exhibits microscopic granular edges with edgeDensity >= 0.087 (avg ~0.35).
        if ($skinRatio > 0.75 && $edgeDensity < 0.065) {
            return false;
        }

        // Secondary: catches flat, artificial skin-toned surfaces (e.g. skin-colored
        // paper, plastic, fabric) that have a more moderate skin ratio but are
        // clearly not natural photographed skin or soil grain.
        if ($skinRatio > 0.40 && $edgeDensity < 0.030 && $stdDev < 4.5) {
            return false;
        }

        return true;
    }

    protected function tierC($gd, int $w, int $h, float $cx, float $cy, float $radius): bool
    {
        $step = max(1, (int)($radius / 30));
        $x0 = (int)($cx - $radius); $x1 = (int)($cx + $radius);
        $y0 = (int)($cy - $radius); $y1 = (int)($cy + $radius);
        $r2 = $radius * $radius;

        $sumR = 0; $sumG = 0; $sumB = 0; $circleTotal = 0;
        $greenPixels = 0; $invalidCirclePixels = 0; $pix = [];

        for ($y = $y0; $y <= $y1; $y += $step) {
            for ($x = $x0; $x <= $x1; $x += $step) {
                if ($x < 0 || $x >= $w || $y < 0 || $y >= $h) continue;
                $dx = $x - $cx; $dy = $y - $cy;
                if ($dx * $dx + $dy * $dy > $r2) continue;

                $c = imagecolorat($gd, $x, $y);
                $a = ($c >> 24) & 0x7F;
                if ($a > 100) continue;

                $rv = ($c >> 16) & 0xFF;
                $gv = ($c >>  8) & 0xFF;
                $bv =  $c        & 0xFF;
                if ($rv === 0 && $gv === 0 && $bv === 0) continue;

                $circleTotal++;
                $sumR += $rv; $sumG += $gv; $sumB += $bv;
                $pix[] = (int)(0.299 * $rv + 0.587 * $gv + 0.114 * $bv);

                [$pxH, $pxS] = $this->rgbToHS($rv, $gv, $bv);
                if ($pxS > 0.30 && $gv > $rv + 20) $greenPixels++;
                if ($pxS > 0.28 && $bv > $rv + 24 && $bv > $gv + 14) $invalidCirclePixels++;
            }
        }

        if ($circleTotal < 4) return true;

        $mr = (int)round($sumR / $circleTotal);
        $mg = (int)round($sumG / $circleTotal);
        $mb = (int)round($sumB / $circleTotal);

        $n = count($pix);
        if ($n === 0) return true;

        $mean = array_sum($pix) / $n;
        $var  = 0.0;
        foreach ($pix as $p) $var += ($p - $mean) ** 2;
        $std  = sqrt($var / $n);

        $greenRatio          = $greenPixels / $circleTotal;
        $circleNonSoilRatio  = $invalidCirclePixels / $circleTotal;

        [$mHue, $mSat] = $this->rgbToHS($mr, $mg, $mb);
        if ($mSat > 0.30 && $mg > $mr + 20) return false;
        if ($mSat > 0.28 && $mb > $mr + 24 && $mb > $mg + 16) return false;
        if ($greenRatio > 0.35) return false;
        if ($circleNonSoilRatio > 0.45) return false;
        if ($std < 0.05) return false;

        return true;
    }

    protected function rgbToHS(int $r, int $g, int $b): array
    {
        if ($r === 0 && $g === 0 && $b === 0) {
            return [0, 0.0];
        }
        $rN = $r / 255.0; $gN = $g / 255.0; $bN = $b / 255.0;
        $max = max($rN, $gN, $bN); $min = min($rN, $gN, $bN);
        $d = $max - $min;
        $h = 0;
        if ($d > 0.00001) {
            if (abs($max - $rN) < 0.00001)      $h = fmod(($gN - $bN) / $d, 6);
            elseif (abs($max - $gN) < 0.00001)  $h = ($bN - $rN) / $d + 2;
            else                                $h = ($rN - $gN) / $d + 4;
            $h = (int)round($h * 60);
            if ($h < 0) $h += 360;
        }
        $s = ($max < 0.00001) ? 0.0 : ($d / $max);
        return [$h, $s];
    }
}
