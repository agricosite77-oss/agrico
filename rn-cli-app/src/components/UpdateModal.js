import React, { useState } from 'react';
import {
  Modal,
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Linking,
  ScrollView,
} from 'react-native';
import { downloadAndInstallApk } from '../services/updateService';

export default function UpdateModal({
  visible,
  updateInfo,
  onClose,
}) {
  const [isDownloading, setIsDownloading] = useState(false);
  const [downloadProgress, setDownloadProgress] = useState(0);
  const [downloadBytes, setDownloadBytes] = useState({ written: 0, total: 0 });
  const [errorMessage, setErrorMessage] = useState('');

  if (!visible || !updateInfo) return null;

  const handleStartUpdate = async () => {
    setIsDownloading(true);
    setErrorMessage('');
    setDownloadProgress(0);

    try {
      const urlToUse = updateInfo.directApkUrl || updateInfo.downloadUrl;
      await downloadAndInstallApk(urlToUse, (percent, written, total) => {
        setDownloadProgress(percent);
        setDownloadBytes({ written, total });
      });
    } catch (err) {
      console.warn('[UpdateModal] Download failed:', err?.message);
      setErrorMessage('Direct download could not complete. You can download via browser below.');
      setIsDownloading(false);
    }
  };

  const handleOpenBrowser = () => {
    const url = updateInfo.downloadUrl || updateInfo.directApkUrl;
    if (url) {
      Linking.openURL(url);
    }
    if (onClose) onClose();
  };

  const writtenMb = (downloadBytes.written / 1048576).toFixed(1);
  const totalMb = (downloadBytes.total / 1048576).toFixed(1);

  return (
    <Modal
      transparent
      animationType="fade"
      visible={visible}
      onRequestClose={isDownloading ? undefined : onClose}
    >
      <View style={styles.modalBackdrop}>
        <View style={styles.modalCard}>
          {/* Header Icon & Title */}
          <View style={styles.iconCircle}>
            <Text style={styles.iconEmoji}>🚀</Text>
          </View>

          <Text style={styles.modalTitle}>New Update Available</Text>
          <Text style={styles.modalSubtitle}>
            A newer version of AgriCo is available online with improvements and new features.
          </Text>

          {/* Version Transition Pills */}
          <View style={styles.versionRow}>
            <View style={styles.versionPillOld}>
              <Text style={styles.versionTextOld}>Current: {updateInfo.currentVersion}</Text>
            </View>
            <Text style={styles.versionArrow}>➔</Text>
            <View style={styles.versionPillNew}>
              <Text style={styles.versionTextNew}>New: {updateInfo.latestVersion}</Text>
            </View>
          </View>

          {/* Release Notes */}
          <View style={styles.notesContainer}>
            <View style={styles.notesHeader}>
              <Text style={styles.notesTitle}>What's New</Text>
              {updateInfo.fileSize ? (
                <Text style={styles.fileSizeText}>Size: {updateInfo.fileSize}</Text>
              ) : null}
            </View>
            <ScrollView style={styles.notesScroll} showsVerticalScrollIndicator={false}>
              <Text style={styles.notesBody}>
                {updateInfo.releaseNotes || 'Bug fixes, performance improvements, and offline optimizations.'}
              </Text>
            </ScrollView>
          </View>

          {/* Download Progress Bar */}
          {isDownloading && (
            <View style={styles.progressContainer}>
              <View style={styles.progressInfoRow}>
                <Text style={styles.progressLabel}>
                  {downloadProgress === 100 ? 'Opening installer...' : 'Downloading update...'}
                </Text>
                <Text style={styles.progressPercent}>{downloadProgress}%</Text>
              </View>
              <View style={styles.progressBarTrack}>
                <View style={[styles.progressBarFill, { width: `${downloadProgress}%` }]} />
              </View>
              {downloadBytes.total > 0 && (
                <Text style={styles.bytesText}>
                  {writtenMb} MB / {totalMb} MB
                </Text>
              )}
            </View>
          )}

          {/* Error Message */}
          {Boolean(errorMessage) && (
            <Text style={styles.errorText}>{errorMessage}</Text>
          )}

          {/* Action Buttons */}
          <View style={styles.btnRow}>
            {!isDownloading && (
              <TouchableOpacity
                style={styles.cancelBtn}
                onPress={onClose}
                activeOpacity={0.7}
              >
                <Text style={styles.cancelBtnText}>Later</Text>
              </TouchableOpacity>
            )}

            <TouchableOpacity
              style={[
                styles.updateBtn,
                isDownloading && styles.updateBtnDisabled,
              ]}
              onPress={handleStartUpdate}
              disabled={isDownloading}
              activeOpacity={0.85}
            >
              {isDownloading ? (
                <View style={styles.btnLoadingRow}>
                  <ActivityIndicator size="small" color="#ffffff" />
                  <Text style={styles.updateBtnText}> Downloading...</Text>
                </View>
              ) : (
                <Text style={styles.updateBtnText}>Update Now</Text>
              )}
            </TouchableOpacity>
          </View>

          {/* Fallback Browser Link */}
          <TouchableOpacity
            style={styles.browserLinkBtn}
            onPress={handleOpenBrowser}
            activeOpacity={0.7}
          >
            <Text style={styles.browserLinkText}>Or download APK directly in browser →</Text>
          </TouchableOpacity>
        </View>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  modalBackdrop: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.72)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 22,
  },
  modalCard: {
    width: '100%',
    maxWidth: 380,
    backgroundColor: '#ffffff',
    borderRadius: 24,
    padding: 22,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.15,
    shadowRadius: 28,
    elevation: 8,
  },
  iconCircle: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: '#ecfdf5',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 12,
    borderWidth: 2,
    borderColor: '#a7f3d0',
  },
  iconEmoji: {
    fontSize: 28,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: '800',
    color: '#0f172a',
    marginBottom: 6,
    textAlign: 'center',
    letterSpacing: -0.3,
  },
  modalSubtitle: {
    fontSize: 13,
    color: '#64748b',
    textAlign: 'center',
    lineHeight: 18,
    marginBottom: 16,
    paddingHorizontal: 8,
  },
  versionRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginBottom: 14,
  },
  versionPillOld: {
    backgroundColor: '#f1f5f9',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
  },
  versionTextOld: {
    fontSize: 11,
    fontWeight: '600',
    color: '#64748b',
  },
  versionArrow: {
    fontSize: 12,
    color: '#94a3b8',
    fontWeight: '700',
  },
  versionPillNew: {
    backgroundColor: '#dcfce7',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
  },
  versionTextNew: {
    fontSize: 11,
    fontWeight: '700',
    color: '#15803d',
  },
  notesContainer: {
    width: '100%',
    backgroundColor: '#f8fafc',
    borderRadius: 14,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    padding: 12,
    marginBottom: 14,
  },
  notesHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  notesTitle: {
    fontSize: 12,
    fontWeight: '700',
    color: '#334155',
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  fileSizeText: {
    fontSize: 11,
    fontWeight: '600',
    color: '#94a3b8',
  },
  notesScroll: {
    maxHeight: 80,
  },
  notesBody: {
    fontSize: 12.5,
    color: '#475569',
    lineHeight: 17,
  },
  progressContainer: {
    width: '100%',
    marginBottom: 14,
  },
  progressInfoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 6,
  },
  progressLabel: {
    fontSize: 12,
    fontWeight: '600',
    color: '#15803d',
  },
  progressPercent: {
    fontSize: 12,
    fontWeight: '700',
    color: '#0f172a',
  },
  progressBarTrack: {
    width: '100%',
    height: 8,
    borderRadius: 4,
    backgroundColor: '#e2e8f0',
    overflow: 'hidden',
  },
  progressBarFill: {
    height: '100%',
    backgroundColor: '#22c55e',
    borderRadius: 4,
  },
  bytesText: {
    fontSize: 11,
    color: '#94a3b8',
    textAlign: 'right',
    marginTop: 4,
  },
  errorText: {
    fontSize: 12,
    color: '#ef4444',
    textAlign: 'center',
    marginBottom: 12,
  },
  btnRow: {
    flexDirection: 'row',
    width: '100%',
    gap: 10,
    marginTop: 4,
  },
  cancelBtn: {
    flex: 1,
    height: 46,
    borderRadius: 14,
    backgroundColor: '#f1f5f9',
    justifyContent: 'center',
    alignItems: 'center',
  },
  cancelBtnText: {
    fontSize: 14,
    fontWeight: '700',
    color: '#64748b',
  },
  updateBtn: {
    flex: 2,
    height: 46,
    borderRadius: 14,
    backgroundColor: '#22c55e',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#22c55e',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 3,
  },
  updateBtnDisabled: {
    backgroundColor: '#86efac',
  },
  updateBtnText: {
    fontSize: 14,
    fontWeight: '800',
    color: '#ffffff',
    letterSpacing: -0.2,
  },
  btnLoadingRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  browserLinkBtn: {
    marginTop: 12,
    paddingVertical: 4,
  },
  browserLinkText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#15803d',
    textDecorationLine: 'underline',
  },
});
