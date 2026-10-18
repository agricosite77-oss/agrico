import NetInfo from '@react-native-community/netinfo';
import * as Keychain from 'react-native-keychain';
import RNFS from 'react-native-fs';
import { startScan, pollScan } from './scanAsync';
import { markScanSynced } from './localScanStore';
import { ENV } from '../config/env';

const OFFLINE_KEYCHAIN_SERVICE = 'agrico_offline_scans_queue';
const QUEUE_DIR = `${RNFS.DocumentDirectoryPath}/scan_queue`;
const MAX_ATTEMPTS = 5;

// In-memory queue cache for quick synchronous reads
let memoryQueue = [];
let flushing = false;

/**
 * Retrieve all pending offline scans from Keychain storage
 */
export async function getOfflineScans() {
  try {
    const creds = await Keychain.getGenericPassword({ service: OFFLINE_KEYCHAIN_SERVICE });
    if (creds && creds.password) {
      const parsed = JSON.parse(creds.password);
      if (Array.isArray(parsed)) {
        memoryQueue = parsed;
        return parsed;
      }
    }
  } catch (err) {
    console.warn('[ScanSyncService] Error reading offline queue:', err?.message);
  }
  return memoryQueue;
}

/**
 * Persist queue to Keychain
 */
export async function saveOfflineQueue(queue) {
  memoryQueue = queue;
  try {
    if (queue.length === 0) {
      await Keychain.resetGenericPassword({ service: OFFLINE_KEYCHAIN_SERVICE });
    } else {
      await Keychain.setGenericPassword('offline_scans', JSON.stringify(queue), {
        service: OFFLINE_KEYCHAIN_SERVICE,
      });
    }
  } catch (err) {
    console.warn('[ScanSyncService] Error persisting offline queue:', err?.message);
  }
}

/**
 * Offline (or upload failed): copy the image out of the picker cache, queue metadata only.
 * Avoids storing large base64 strings in Keychain.
 */
export async function queueAsyncScan({ scanId, imageUri, latitude, longitude, locationName }) {
  const queue = await getOfflineScans();
  if (queue.some((q) => q.scanId === scanId)) return; // same id -> never duplicate

  await RNFS.mkdir(QUEUE_DIR);
  const imagePath = `${QUEUE_DIR}/${scanId}.jpg`;
  const cleanUri = decodeURI(imageUri.replace('file://', ''));
  await RNFS.copyFile(cleanUri, imagePath);

  queue.unshift({
    id: scanId,
    kind: 'async',
    scanId,
    state: 'queued',
    attempts: 0,
    imagePath,
    latitude: latitude ?? null,
    longitude: longitude ?? null,
    locationName: locationName ?? null,
    queuedAt: new Date().toISOString(),
  });
  await saveOfflineQueue(queue);
}

/**
 * Upload accepted (202) but result not yet read:
 * Remember it so a killed app or dropped connection can resume polling.
 */
export async function trackInflightScan(scanId) {
  const queue = await getOfflineScans();
  if (queue.some((q) => q.scanId === scanId)) return;
  queue.unshift({
    id: scanId,
    kind: 'async',
    scanId,
    state: 'uploaded',
    attempts: 0,
    imagePath: null,
    queuedAt: new Date().toISOString(),
  });
  await saveOfflineQueue(queue);
}

/**
 * Clear completed or rejected scan from offline queue and remove copied image
 */
export async function clearQueuedScan(scanId) {
  const queue = await getOfflineScans();
  const item = queue.find((q) => q.scanId === scanId);
  if (item?.imagePath) {
    RNFS.unlink(item.imagePath).catch(() => {});
  }
  await saveOfflineQueue(queue.filter((q) => q.scanId !== scanId));
}

/**
 * Flush and process pending offline scans.
 * Purges legacy mock items so fake demo data is never synced.
 */
export async function flushOfflineQueue() {
  if (flushing) return { flushed: 0, rejected: 0, remaining: memoryQueue.length };
  flushing = true;
  try {
    const net = await NetInfo.fetch();
    if (!net.isConnected) return { flushed: 0, rejected: 0, remaining: memoryQueue.length };

    const queue = await getOfflineScans();
    const keep = [];
    let flushed = 0;
    let rejected = 0;

    for (let i = 0; i < queue.length; i++) {
      const item = queue[i];
      // Legacy items holding client-guessed demo specimens: purge them
      if (item.kind !== 'async') {
        continue;
      }

      const drop = () => {
        if (item.imagePath) {
          RNFS.unlink(item.imagePath).catch(() => {});
        }
      };

      try {
        if (item.state !== 'uploaded') {
          if (!item.imagePath || !(await RNFS.exists(item.imagePath))) {
            continue; // Image file gone: drop item
          }
          const b64 = await RNFS.readFile(item.imagePath, 'base64');
          await startScan({
            scanId: item.scanId,
            imageBase64: `data:image/jpeg;base64,${b64}`,
            latitude: item.latitude,
            longitude: item.longitude,
            locationName: item.locationName,
          });
          item.state = 'uploaded';
        }

        const r = await pollScan(item.scanId);
        if (r.status === 'done') {
          flushed++;
          drop();
          const serverScanId = r.scan?.soil_scan_id;
          const serverImageUrl = r.scan?.scan_image_url || r.scan?.scan_image;
          await markScanSynced(item.scanId, { serverScanId, serverImageUrl });
          continue;
        }
        if (r.status === 'rejected') {
          rejected++;
          drop();
          continue;
        }
        if (r.status === 'auth') {
          keep.push(item, ...queue.slice(i + 1));
          break; // Keep the rest until authenticated
        }

        item.attempts = (item.attempts || 0) + 1;
        if (item.attempts < MAX_ATTEMPTS) {
          keep.push(item);
        } else {
          drop();
        }
      } catch (e) {
        item.attempts = (item.attempts || 0) + 1;
        if (item.attempts < MAX_ATTEMPTS) {
          keep.push(item);
        } else {
          drop();
        }
      }
    }

    await saveOfflineQueue(keep);
    return { flushed, rejected, remaining: keep.length };
  } finally {
    flushing = false;
  }
}

// Global network listener to auto-sync when device comes online
NetInfo.addEventListener((state) => {
  if (state.isConnected) {
    flushOfflineQueue().catch(() => {});
  }
});

/**
 * Maps server analysis model and response payload into a standardized specimen shape.
 */
export function mapServerScan(scan, data = {}) {
  const munsellCode = scan?.munsell_code || data?.munsell || data?.munsell_code || '10R 4/1';
  const name = scan?.soil_name || data?.soil_name || data?.color_name || 'Determined Soil';
  const hueCode = scan?.hue || data?.hue || (munsellCode.includes(' ') ? munsellCode.split(' ')[0] : '10R');
  const hexColor = data?.hex || '#66503C';
  const confidence = scan?.confidence || data?.confidence_formatted || data?.confidence || '95%';
  const confStr = typeof confidence === 'number' ? `${confidence}%` : String(confidence).includes('%') ? confidence : `${confidence}%`;
  const organicIndex = data?.organic_matter || data?.organic_index || 'Moderate';
  const soilType = data?.soil_type || data?.soilCategory || scan?.description || 'Determined Soil';
  const description = scan?.description || data?.description || `${munsellCode} - ${name}`;
  const scanId = scan?.soil_scan_id || data?.scan_id;
  const createdDate = scan?.created_at ? new Date(scan.created_at).toLocaleDateString() : new Date().toLocaleDateString();
  const scanCount = String(data?.scan_seq || 1);
  const rawImg = data?.scan_image_url || data?.image || scan?.scan_image_url || scan?.scan_image;
  const baseUrl = (ENV?.API_URL || 'http://10.0.2.2:8080/api').replace(/\/api\/?$/, '');
  const imageUrl = rawImg
    ? (String(rawImg).startsWith('http') || String(rawImg).startsWith('data:')
        ? rawImg
        : `${baseUrl}${String(rawImg).startsWith('/') ? '' : '/'}${rawImg}`)
    : null;

  const hasPin = Boolean(scan?.location_id || scan?.location || data?.hasPin || data?.location_id || data?.location || data?.latitude);
  const rawNote = scan?.scan_note || data?.scan_note || '';
  const isRealNote = rawNote && !rawNote.startsWith('Determined Soil:') && rawNote !== '—' && rawNote !== '-';
  const scanNote = (hasPin && isRealNote) ? rawNote : '-';
  const soilPercentage = data?.soil_percentage || data?.percentage || (data?.regions && data.regions[0] ? data.regions[0].percent || Math.round((data.regions[0].proportion || 0) * 100) : null);

  return {
    id: scanId,
    scanId,
    name,
    scientificName: `${munsellCode} · Munsell Color`,
    munsellCode,
    fullCode: munsellCode,
    hueCode,
    hexColor,
    scanCount,
    organicIndex,
    soilType,
    nearMatchRating: confStr,
    confidence: confStr,
    createdDate,
    description,
    scanNote,
    soilPercentage,
    soilCategory: soilType,
    imageUrl,
    thumbnail: imageUrl ? { uri: imageUrl } : null,
    regions: data?.regions || [],
  };
}

