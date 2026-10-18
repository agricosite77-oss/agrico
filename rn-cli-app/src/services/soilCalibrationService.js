import RNFS from 'react-native-fs';
import api from './api';
import BUNDLED_CALIBRATION from '../data/soilReferenceDataset.json';

const CACHE_FILE_PATH = `${RNFS.DocumentDirectoryPath}/soil_reference_cache.json`;
const IMAGES_DIR = `${RNFS.DocumentDirectoryPath}/soil_reference_images`;

let inMemoryCalibration = null;

async function ensureImagesDir() {
  try {
    const exists = await RNFS.exists(IMAGES_DIR);
    if (!exists) {
      await RNFS.mkdir(IMAGES_DIR);
    }
  } catch (e) {
    // ignore
  }
}

/**
 * Get active soil calibration data (Stage 1 Soil Gate parameters + reference centroids).
 * Returns cached website data if available, falling back to bundled dataset.
 */
export async function getActiveCalibration() {
  if (inMemoryCalibration) {
    return inMemoryCalibration;
  }

  try {
    if (await RNFS.exists(CACHE_FILE_PATH)) {
      const content = await RNFS.readFile(CACHE_FILE_PATH, 'utf8');
      const parsed = JSON.parse(content);
      if (parsed && (parsed.gate_config || parsed.reference_centroids)) {
        inMemoryCalibration = parsed;
        return inMemoryCalibration;
      }
    }
  } catch (err) {
    console.warn('[SoilCalibrationService] Error reading cached calibration:', err?.message);
  }

  inMemoryCalibration = BUNDLED_CALIBRATION;
  return inMemoryCalibration;
}

/**
 * Sync latest Stage 1 reference dataset and soil images from website API.
 * Called during Splash / Landing page loading or on demand.
 * Downloads and stores real soil images locally on the device for Stage 1.
 */
export async function syncSoilCalibrationFromWebsite(onProgress) {
  try {
    if (typeof onProgress === 'function') {
      onProgress('Connecting to AgriCo Soil Database...');
    }

    const response = await api.get('/real-soil/calibration', { timeout: 9000 });
    if (response?.data?.status === 'success' && response?.data?.gate_config) {
      await ensureImagesDir();

      const samples = response.data.reference_samples || [];
      const cachedSamples = [];

      if (typeof onProgress === 'function') {
        onProgress(`Loading ${samples.length || 40} Stage 1 Reference Soil Images...`);
      }

      // Download and cache top reference images locally (up to 12 representative samples)
      const downloadLimit = Math.min(12, samples.length);
      for (let i = 0; i < downloadLimit; i++) {
        const sample = samples[i];
        if (sample?.url) {
          const filename = sample.filename || `ref_${i}.jpg`;
          const localPath = `${IMAGES_DIR}/${filename}`;
          try {
            const fileExists = await RNFS.exists(localPath);
            if (!fileExists) {
              await RNFS.downloadFile({
                fromUrl: sample.url,
                toFile: localPath,
                connectionTimeout: 5000,
                readTimeout: 5000,
              }).promise;
            }
            cachedSamples.push({
              ...sample,
              localUri: `file://${localPath}`,
            });
          } catch (dlErr) {
            cachedSamples.push(sample);
          }
        } else {
          cachedSamples.push(sample);
        }
      }

      // Append remaining samples (without downloading files)
      for (let i = downloadLimit; i < samples.length; i++) {
        cachedSamples.push(samples[i]);
      }

      const merged = {
        version: response.data.model_version || '2.0.0',
        name: 'AgriCo Website Calibrated Reference Dataset',
        gate_config: response.data.gate_config,
        reference_centroids: cachedSamples.length > 0 ? cachedSamples : BUNDLED_CALIBRATION.reference_centroids,
        munsell_colors: response.data.munsell_colors || null,
        synced_at: new Date().toISOString(),
      };

      await RNFS.writeFile(CACHE_FILE_PATH, JSON.stringify(merged), 'utf8');
      inMemoryCalibration = merged;

      if (typeof onProgress === 'function') {
        onProgress('✓ Stage 1 Soil Gate Calibrated');
      }

      return {
        success: true,
        count: (merged.reference_centroids || []).length,
        version: merged.version,
      };
    }
  } catch (err) {
    console.warn('[SoilCalibrationService] Calibration sync skipped (offline or server unreachable):', err?.message);
  }

  inMemoryCalibration = await getActiveCalibration();
  if (typeof onProgress === 'function') {
    onProgress('✓ Stage 1 Offline Engine Active');
  }

  return {
    success: false,
    fallback: true,
    count: (inMemoryCalibration.reference_centroids || []).length,
    version: inMemoryCalibration.version || '2.0.0',
  };
}

/**
 * Return reference soil sample images for Stage 1.
 */
export async function getStage1ReferenceSamples() {
  const cal = await getActiveCalibration();
  return cal.reference_centroids || BUNDLED_CALIBRATION.reference_centroids || [];
}

