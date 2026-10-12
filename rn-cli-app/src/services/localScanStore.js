import RNFS from 'react-native-fs';

const STORE_PATH = `${RNFS.DocumentDirectoryPath}/local_scan_history.json`;

let inMemoryScans = null;

/**
 * Retrieve all local scans from persistent file storage.
 * Runs instantly on device even when completely offline.
 */
export async function getLocalScans() {
  if (inMemoryScans !== null) {
    return inMemoryScans;
  }
  try {
    if (await RNFS.exists(STORE_PATH)) {
      const content = await RNFS.readFile(STORE_PATH, 'utf8');
      const parsed = JSON.parse(content);
      if (Array.isArray(parsed)) {
        inMemoryScans = parsed;
        return parsed;
      }
    }
  } catch (err) {
    console.warn('[LocalScanStore] Failed to read local scans:', err?.message);
  }
  inMemoryScans = [];
  return [];
}

/**
 * Save / prepend a scan into local store immediately.
 */
export async function saveLocalScan(scanItem) {
  try {
    const existing = await getLocalScans();
    const targetKey = scanItem.scanId || scanItem.soil_scan_id;
    const filtered = existing.filter(
      (item) => (item.scanId || item.soil_scan_id) !== targetKey
    );
    const updated = [scanItem, ...filtered];
    inMemoryScans = updated;
    await RNFS.writeFile(STORE_PATH, JSON.stringify(updated), 'utf8');
    return updated;
  } catch (err) {
    console.warn('[LocalScanStore] Failed to save local scan:', err?.message);
    return inMemoryScans || [];
  }
}

/**
 * Merge remote server scans with any pending offline scans.
 * Ensures offline scans awaiting sync stay visible at the top.
 */
export async function mergeServerScans(serverScans) {
  try {
    const existing = await getLocalScans();
    const pendingOffline = existing.filter((s) => s.syncState === 'queued');
    const serverScanIds = new Set(
      serverScans.map((s) => s.soil_scan_id || s.id || s.scanId)
    );

    const stillPending = pendingOffline.filter(
      (p) => !serverScanIds.has(p.soil_scan_id) && !serverScanIds.has(p.scanId)
    );

    const merged = [...stillPending, ...serverScans];
    inMemoryScans = merged;
    await RNFS.writeFile(STORE_PATH, JSON.stringify(merged), 'utf8');
    return merged;
  } catch (err) {
    console.warn('[LocalScanStore] Failed to merge server scans:', err?.message);
    return serverScans;
  }
}

/**
 * Update scan state when successfully synced to server.
 */
export async function markScanSynced(scanId, { serverScanId, serverImageUrl }) {
  try {
    const existing = await getLocalScans();
    let updatedAny = false;
    const updated = existing.map((item) => {
      if (item.scanId === scanId || item.soil_scan_id === scanId) {
        updatedAny = true;
        return {
          ...item,
          soil_scan_id: serverScanId,
          id: 'scan_' + serverScanId,
          syncState: 'synced',
          isOffline: false,
          scanImage: serverImageUrl || item.scanImage,
        };
      }
      return item;
    });

    if (updatedAny) {
      inMemoryScans = updated;
      await RNFS.writeFile(STORE_PATH, JSON.stringify(updated), 'utf8');
    }
    return inMemoryScans;
  } catch (err) {
    console.warn('[LocalScanStore] Failed to mark scan synced:', err?.message);
    return inMemoryScans || [];
  }
}
