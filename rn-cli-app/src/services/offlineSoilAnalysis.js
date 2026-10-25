import { NativeModules } from 'react-native';
import OFFLINE_MUNSELL_COLORS from '../data/munsellColors.json';
import OFFLINE_SOIL_PROFILES from '../data/offlineSoils.json';
import { getActiveCalibration } from './soilCalibrationService';

/**
 * Convert standard sRGB [0-255] to CIELAB [L*, a*, b*]
 */
function rgbToLab(r, g, b) {
  // sRGB to Linear
  const rLin = (r / 255) > 0.04045 ? Math.pow(((r / 255) + 0.055) / 1.055, 2.4) : (r / 255) / 12.92;
  const gLin = (g / 255) > 0.04045 ? Math.pow(((g / 255) + 0.055) / 1.055, 2.4) : (g / 255) / 12.92;
  const bLin = (b / 255) > 0.04045 ? Math.pow(((b / 255) + 0.055) / 1.055, 2.4) : (b / 255) / 12.92;

  // Linear to XYZ (D65 illuminant)
  const x = (rLin * 0.4124 + gLin * 0.3576 + bLin * 0.1805) / 0.95047;
  const y = (rLin * 0.2126 + gLin * 0.7152 + bLin * 0.0722) / 1.00000;
  const z = (rLin * 0.0193 + gLin * 0.1192 + bLin * 0.9505) / 1.08883;

  const fx = x > 0.008856 ? Math.cbrt(x) : (7.787 * x) + (16 / 116);
  const fy = y > 0.008856 ? Math.cbrt(y) : (7.787 * y) + (16 / 116);
  const fz = z > 0.008856 ? Math.cbrt(z) : (7.787 * z) + (16 / 116);

  const L = (116 * fy) - 16;
  const a = 500 * (fx - fy);
  const bVal = 200 * (fy - fz);

  return [L, a, bVal];
}

/**
 * Parse Hex color string to [R, G, B]
 */
function hexToRgb(hex) {
  if (!hex) return [90, 70, 60];
  const clean = hex.replace('#', '');
  const r = parseInt(clean.substring(0, 2), 16) || 0;
  const g = parseInt(clean.substring(2, 4), 16) || 0;
  const b = parseInt(clean.substring(4, 6), 16) || 0;
  return [r, g, b];
}

/**
 * Calculate Euclidean Delta E distance in CIELAB color space
 */
function deltaE(lab1, lab2) {
  const dL = lab1[0] - lab2[0];
  const da = lab1[1] - lab2[1];
  const db = lab1[2] - lab2[2];
  return Math.sqrt(dL * dL + da * da + db * db);
}

/**
 * Stage 1 Soil Gate:
 * Strictly validates that the scanned image is real, granular soil
 * and rejects human hands/skin, blank paper, computer screens, walls, or non-soil objects.
 */
export async function validateSoilGate({
  r,
  g,
  b,
  stdDev = 18.0,
  skinRatio = 0.0,
  gradVariance = 25.0,
}) {
  const calibration = await getActiveCalibration();
  const gateConfig = calibration?.gate_config || {
    min_variance: 8.5,
    max_skin_ratio: 0.32,
    max_delta_e: 30.0,
  };

  // 1. Extreme overexposure check (e.g. blank white paper, direct sunlight glare, white wall)
  if (r > 225 && g > 225 && b > 225) {
    return {
      isValid: false,
      reason: 'overexposed_blank',
      message: 'Overexposed or plain white surface detected. Please place the camera over real soil.',
    };
  }

  // 2. Extreme underexposure check (pitch black or obstructed lens)
  if (r < 18 && g < 18 && b < 18) {
    return {
      isValid: false,
      reason: 'underexposed_dark',
      message: 'Image is too dark or camera is covered. Ensure adequate natural daylight or flash.',
    };
  }

  // 3. Human skin / hand detection (holding sample or hand in viewfinder)
  const maxSkinRatio = gateConfig.max_skin_ratio ?? 0.32;
  if (skinRatio > maxSkinRatio) {
    return {
      isValid: false,
      reason: 'human_skin_detected',
      message: 'Human skin or hand detected in scanning box. Point camera strictly at genuine soil.',
    };
  }

  // 4. Uniform / flat surface check (paper, desk, plastic card, wall lacking soil particle texture)
  const minVar = gateConfig.min_variance ?? 8.5;
  if (stdDev < minVar && gradVariance < 16.0) {
    return {
      isValid: false,
      reason: 'flat_texture',
      message: 'Smooth surface without soil grain detected. Soil requires textured granular particles.',
    };
  }

  // 5. Chromaticity checks (unnatural blue/cyan or bright plant foliage)
  if (b > r + 15 && b > g + 15) {
    return {
      isValid: false,
      reason: 'unnatural_blue',
      message: 'Non-soil blue/cyan hue detected. Real soil does not exhibit dominant blue tones.',
    };
  }
  if (g > r + 28 && g > 110) {
    return {
      isValid: false,
      reason: 'foliage_green',
      message: 'Plant foliage or grass detected. Position viewfinder entirely over bare soil.',
    };
  }

  // 6. Spectral soil gamut check: CIELAB ΔE distance to reference soil centroids
  const targetLab = rgbToLab(r, g, b);
  const centroids = calibration?.reference_centroids || [];
  let minCentroidDist = Infinity;
  let closestCentroid = null;

  for (const c of centroids) {
    let cLab = c.lab;
    if (!cLab && c.rgb) {
      cLab = rgbToLab(c.rgb[0], c.rgb[1], c.rgb[2]);
    } else if (!cLab && c.hexCode) {
      const [cr, cg, cb] = hexToRgb(c.hexCode);
      cLab = rgbToLab(cr, cg, cb);
    }
    if (cLab) {
      const dist = deltaE(targetLab, cLab);
      if (dist < minCentroidDist) {
        minCentroidDist = dist;
        closestCentroid = c;
      }
    }
  }

  const maxDeltaE = gateConfig.max_delta_e ?? 30.0;
  if (minCentroidDist > maxDeltaE) {
    return {
      isValid: false,
      reason: 'outside_soil_gamut',
      message: `Sample color is outside the Munsell soil spectrum (ΔE = ${minCentroidDist.toFixed(1)}). Please scan real soil.`,
      deltaE: minCentroidDist,
    };
  }

  // 7. Non-soil / Fake soil centroid check (human skin, blank paper, desk wood, concrete tile, plastic)
  const fakeCentroids = calibration?.fake_centroids || [];
  let minFakeDist = Infinity;
  let closestFake = null;
  for (const fc of fakeCentroids) {
    let fcLab = fc.lab;
    if (!fcLab && fc.rgb) {
      fcLab = rgbToLab(fc.rgb[0], fc.rgb[1], fc.rgb[2]);
    } else if (!fcLab && fc.hexCode) {
      const [fcr, fcg, fcb] = hexToRgb(fc.hexCode);
      fcLab = rgbToLab(fcr, fcg, fcb);
    }
    if (fcLab) {
      const dist = deltaE(targetLab, fcLab);
      if (dist < minFakeDist) {
        minFakeDist = dist;
        closestFake = fc;
      }
    }
  }

  // Reject if matches non-soil reference and is closer to fake surface than real soil
  if (closestFake && minFakeDist < 14.0 && minFakeDist < minCentroidDist * 0.8) {
    return {
      isValid: false,
      reason: 'non_soil_surface_matched',
      message: `Non-soil surface detected (${closestFake.name || 'artificial/flat surface'}). Please aim camera directly at real soil.`,
      deltaE: minFakeDist,
    };
  }

  return {
    isValid: true,
    reason: 'real_soil_verified',
    message: 'Stage 1 Gate Passed: Verified genuine soil sample.',
    closestCentroid,
    deltaE: minCentroidDist,
  };
}

/**
 * Perform on-device soil classification and Munsell spectral matching.
 * Uses 207 pre-bundled official Munsell colors and offline agronomic profiles.
 * Strictly verifies Stage 1 Soil Gate before proceeding to Stages 2-6.
 */
export async function analyzeSoilOffline({ imageUri, imageBase64 }) {
  let sampledR = null;
  let sampledG = null;
  let sampledB = null;
  let sampledHex = null;
  let stdDev = 0;
  let skinRatio = 0.0;
  let gradVariance = 0;
  let tfliteRealProb = -1.0;
  let tfliteFakeProb = -1.0;
  let tfliteExecuted = false;
  let extractionSuccess = false;

  // 1. Try native Android color & TFLite model extraction from photo file
  if (imageUri && NativeModules.InstallApk && typeof NativeModules.InstallApk.extractSoilColor === 'function') {
    try {
      const rawPath = imageUri.replace('file://', '');
      const nativeColor = await NativeModules.InstallApk.extractSoilColor(rawPath);
      if (nativeColor && nativeColor.hex) {
        sampledR = nativeColor.r;
        sampledG = nativeColor.g;
        sampledB = nativeColor.b;
        sampledHex = nativeColor.hex;
        if (typeof nativeColor.stdDev === 'number') stdDev = nativeColor.stdDev;
        if (typeof nativeColor.skinRatio === 'number') skinRatio = nativeColor.skinRatio;
        if (typeof nativeColor.gradVariance === 'number') gradVariance = nativeColor.gradVariance;
        if (typeof nativeColor.tfliteRealProb === 'number') tfliteRealProb = nativeColor.tfliteRealProb;
        if (typeof nativeColor.tfliteFakeProb === 'number') tfliteFakeProb = nativeColor.tfliteFakeProb;
        if (typeof nativeColor.tfliteExecuted === 'boolean') tfliteExecuted = nativeColor.tfliteExecuted;
        extractionSuccess = true;
      }
    } catch (nativeErr) {
      console.warn('[OfflineSoilAnalysis] Native color extraction failed:', nativeErr?.message);
    }
  }

  // Fail-safe: Never fake soil if image pixels could not be extracted
  if (!extractionSuccess || sampledR === null || sampledG === null || sampledB === null) {
    return {
      isValidSoil: false,
      rejected: true,
      stage: 1,
      reason: 'image_read_error',
      message: 'Stage 1 Soil Gate: Unable to sample image pixels. Ensure camera lens is clean and photo is clear.',
      imageUrl: imageUri || null,
      sampledHex: '#000000',
    };
  }

  // 1b. Stage 1 TFLite AI Model Gate (soil_gate_model.tflite)
  if (tfliteExecuted && tfliteRealProb >= 0.0) {
    // If the mobile TensorFlow Lite deep learning model classifies as non-soil / fake soil
    if (tfliteRealProb < 0.60 || tfliteFakeProb > 0.40) {
      return {
        isValidSoil: false,
        rejected: true,
        stage: 1,
        reason: 'tflite_non_soil_detected',
        message: `Stage 1 AI Gate: Non-soil or artificial surface detected (${(tfliteFakeProb * 100).toFixed(0)}% non-soil confidence). Please point camera directly at real soil.`,
        imageUrl: imageUri || null,
        sampledHex,
        tfliteRealProb,
        tfliteFakeProb,
      };
    }
  }

  // STAGE 1: Heuristic, Colorimetric & Spatial Soil Gate Validation
  const gate = await validateSoilGate({
    r: sampledR,
    g: sampledG,
    b: sampledB,
    stdDev,
    skinRatio,
    gradVariance,
  });

  if (!gate.isValid) {
    return {
      isValidSoil: false,
      rejected: true,
      stage: 1,
      reason: gate.reason,
      message: gate.message,
      imageUrl: imageUri || null,
      sampledHex,
    };
  }

  // 2. Convert sampled soil color to CIELAB
  const targetLab = rgbToLab(sampledR, sampledG, sampledB);

  // 3. Find closest Munsell match from 207 colors
  let bestMatch = null;
  let minDistance = Infinity;

  const munsellList = Array.isArray(OFFLINE_MUNSELL_COLORS) ? OFFLINE_MUNSELL_COLORS : [];

  for (const item of munsellList) {
    if (!item.hexCode) continue;
    const [mr, mg, mb] = hexToRgb(item.hexCode);
    const mLab = rgbToLab(mr, mg, mb);
    const dist = deltaE(targetLab, mLab);

    if (dist < minDistance) {
      minDistance = dist;
      bestMatch = item;
    }
  }

  // Fallback if no match found
  if (!bestMatch) {
    bestMatch = munsellList[0] || {
      munsellCode: '10R 4/1',
      colorName: 'Dark Reddish Gray',
      hue: '10R',
      hexCode: '#5A4643',
      soil_organic_index: 'High (3.8%)',
      soil_type: 'Mineral Clay Subsoil',
      description: '10R 4/1 - Dark Reddish Gray (Mineral Clay Subsoil)',
    };
  }

  // 4. Calculate near match rating / confidence
  const confidenceNum = Math.max(76, Math.min(98, Math.round(98 - minDistance * 0.9)));
  const confidenceStr = `${confidenceNum}%`;

  // 5. Look for complementary agronomic profile from offlineSoils.json
  const soilProfiles = Array.isArray(OFFLINE_SOIL_PROFILES) ? OFFLINE_SOIL_PROFILES : [];
  const matchedProfile = soilProfiles.find(
    (s) => s.munsellCode === bestMatch.munsellCode || s.full_code === bestMatch.munsellCode
  );

  const soilName = matchedProfile?.colorName || bestMatch.colorName || 'Determined Soil';
  const munsellCode = bestMatch.munsellCode || '10R 4/1';
  const hue = bestMatch.hue || (munsellCode.includes(' ') ? munsellCode.split(' ')[0] : '10R');
  const organicIndex = matchedProfile?.soil_organic_index || bestMatch.soil_organic_index || 'Moderate (3.5%)';
  const soilType = matchedProfile?.soil_type || bestMatch.soil_type || 'Determined Soil Horizon';
  const description = matchedProfile?.description || bestMatch.description || `${munsellCode} - ${soilName}`;
  const scanId = Math.floor(1000 + Math.random() * 9000);

  return {
    id: scanId,
    scanId,
    soil_scan_id: scanId,
    isValidSoil: true,
    rejected: false,
    stage1Verified: true,
    closestCentroid: gate.closestCentroid,
    name: soilName,
    scientificName: `${munsellCode} · Munsell Color`,
    munsellCode,
    fullCode: munsellCode,
    hueCode: hue,
    hexColor: bestMatch.hexCode || sampledHex,
    sampledHex,
    scanCount: '1',
    organicIndex,
    soilType,
    nearMatchRating: confidenceStr,
    confidence: confidenceStr,
    createdDate: new Date().toLocaleDateString(),
    description,
    soilCategory: soilType,
    imageUrl: imageUri || null,
    thumbnail: imageUri ? { uri: imageUri } : null,
    isOffline: true,
  };
}
