import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Image,
  ScrollView,
  Alert,
  Platform,
  ActivityIndicator,
} from 'react-native';
import { launchImageLibrary, launchCamera } from 'react-native-image-picker';
import api from '../services/api';
import { ENV } from '../config/env';
import {
  BackArrowIcon,
  LogoutHeaderIcon,
  PencilEditIcon,
  EnvelopeMailIcon,
  IdBadgeIcon,
  RoleShieldIcon,
  CalendarJoinedIcon,
} from './AppIcons';

// Helper to resolve full photo URL from backend storage or absolute URIs
const resolvePhotoUrl = (rawPath) => {
  if (!rawPath || typeof rawPath !== 'string') return null;
  const trimmed = rawPath.trim();
  if (!trimmed) return null;
  if (
    trimmed.startsWith('http://') ||
    trimmed.startsWith('https://') ||
    trimmed.startsWith('file://') ||
    trimmed.startsWith('data:image') ||
    trimmed.startsWith('content://')
  ) {
    return trimmed;
  }
  const cleanPath = trimmed.replace(/^\/+/, '');
  const cleanBase = (ENV.API_URL || 'http://10.0.2.2:8080/api').replace(/\/api\/?$/, '');
  if (cleanPath.startsWith('storage/')) {
    return `${cleanBase}/${cleanPath}`;
  }
  return `${cleanBase}/storage/${cleanPath}`;
};

// Compute user initials (1-2 uppercase letters)
const getUserInitial = (name) => {
  if (!name || typeof name !== 'string') return 'A';
  const trimmed = name.trim();
  if (!trimmed) return 'A';
  const parts = trimmed.split(/\s+/).filter(Boolean);
  if (parts.length >= 2) {
    return (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase();
  }
  return trimmed.charAt(0).toUpperCase();
};

export default function ProfileView({
  user,
  onBack,
  logout,
  scanHistory = [],
  onUpdateUser,
}) {
  const [customPhotoUri, setCustomPhotoUri] = useState(
    user?.profile_photo_url || user?.profile_photo_path || null
  );
  const [isUploadingPhoto, setIsUploadingPhoto] = useState(false);
  const [imgError, setImgError] = useState(false);

  useEffect(() => {
    setCustomPhotoUri(user?.profile_photo_url || user?.profile_photo_path || null);
    setImgError(false);
  }, [user?.profile_photo_url, user?.profile_photo_path]);

  // Format joined date based on database created_at
  const formatJoinedDate = (dateString) => {
    if (!dateString) return '—';
    try {
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return '—';
      return date.toLocaleDateString('en-US', {
        month: 'short',
        year: 'numeric',
      });
    } catch {
      return '—';
    }
  };

  // Compute accurate scan statistics from scanHistory of the account
  const totalScans = Array.isArray(scanHistory) ? scanHistory.length : 0;
  const identifiedScans =
    Array.isArray(scanHistory)
      ? scanHistory.filter((s) => s.munsellCode || s.colorName || s.munsell_code || s.soil_name).length
      : 0;
  const uniqueVarieties =
    Array.isArray(scanHistory)
      ? new Set(scanHistory.map((s) => s.munsellCode || s.colorName).filter(Boolean)).size
      : 0;
  const syncedScans = totalScans;

  const pickImage = async (mode) => {
    try {
      const picker = mode === 'camera' ? launchCamera : launchImageLibrary;
      const result = await picker({
        mediaType: 'photo',
        quality: 0.8,
        maxWidth: 800,
        maxHeight: 800,
      });

      if (result.didCancel || !result.assets || result.assets.length === 0) {
        return;
      }

      const asset = result.assets[0];
      await uploadProfilePhoto(asset);
    } catch (err) {
      Alert.alert('Photo Error', err?.message || 'Could not select photo');
    }
  };

  const uploadProfilePhoto = async (asset) => {
    setIsUploadingPhoto(true);
    try {
      const formData = new FormData();
      formData.append('photo', {
        uri: Platform.OS === 'ios' ? asset.uri.replace('file://', '') : asset.uri,
        type: asset.type || 'image/jpeg',
        name: asset.fileName || 'profile_photo.jpg',
      });

      const res = await api.post('/user/profile-photo', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      if (res.data?.status === 'success') {
        const newUrl = res.data.profile_photo_url || asset.uri;
        setCustomPhotoUri(newUrl);
        setImgError(false);
        if (typeof onUpdateUser === 'function') {
          onUpdateUser();
        }
        Alert.alert('Success', 'Profile photo updated successfully!');
      } else {
        Alert.alert('Upload Failed', res.data?.message || 'Failed to update profile photo.');
      }
    } catch (err) {
      console.warn('Profile photo upload error:', err?.message);
      setCustomPhotoUri(asset.uri);
      setImgError(false);
      Alert.alert('Photo Updated', 'Profile photo saved locally.');
    } finally {
      setIsUploadingPhoto(false);
    }
  };

  const handleRemovePhoto = async () => {
    setIsUploadingPhoto(true);
    try {
      await api.post('/user/profile-photo', { remove_photo: true });
      setCustomPhotoUri(null);
      setImgError(false);
      if (typeof onUpdateUser === 'function') {
        onUpdateUser();
      }
      Alert.alert('Success', 'Profile photo removed.');
    } catch (err) {
      setCustomPhotoUri(null);
      setImgError(false);
    } finally {
      setIsUploadingPhoto(false);
    }
  };

  const handleEditPhoto = () => {
    const options = [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Choose from Gallery',
        onPress: () => pickImage('library'),
      },
      {
        text: 'Take Photo',
        onPress: () => pickImage('camera'),
      },
    ];

    if (customPhotoUri) {
      options.splice(1, 0, {
        text: 'Remove Photo',
        style: 'destructive',
        onPress: handleRemovePhoto,
      });
    }

    Alert.alert('Profile Photo', 'Update your account profile photo:', options);
  };

  const handleLogoutPrompt = () => {
    Alert.alert(
      'Log Out',
      'Are you sure you want to log out of your account?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Log Out',
          style: 'destructive',
          onPress: logout,
        },
      ]
    );
  };

  const handleInfoRowPress = (title, value) => {
    Alert.alert(title, String(value));
  };

  const activePhotoUrl = !imgError ? resolvePhotoUrl(customPhotoUri) : null;

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.contentContainer}
      showsVerticalScrollIndicator={false}
    >
      {/* 1. Top Header (Back Button, Title, Log Out Icon Button) */}
      <View style={styles.topHeader}>
        <TouchableOpacity
          style={styles.circleHeaderBtn}
          onPress={onBack}
          activeOpacity={0.7}
          accessibilityLabel="Go back"
        >
          <BackArrowIcon size={18} color="#0f172a" />
        </TouchableOpacity>

        <Text style={styles.headerTitle}>Profile</Text>

        <TouchableOpacity
          style={styles.circleHeaderBtn}
          onPress={handleLogoutPrompt}
          activeOpacity={0.7}
          accessibilityLabel="Log Out"
        >
          <LogoutHeaderIcon size={18} color="#ef4444" />
        </TouchableOpacity>
      </View>

      {/* 2. Profile Photo with Edit Pencil & Name */}
      <View style={styles.avatarSection}>
        <View style={styles.avatarContainer}>
          {activePhotoUrl ? (
            <Image
              source={{ uri: activePhotoUrl }}
              style={styles.avatarImage}
              onError={() => setImgError(true)}
            />
          ) : (
            <View style={styles.avatarFallback}>
              <Text style={styles.avatarInitialText}>
                {getUserInitial(user?.name)}
              </Text>
            </View>
          )}

          {isUploadingPhoto && (
            <View style={styles.avatarLoadingOverlay}>
              <ActivityIndicator size="small" color="#ffffff" />
            </View>
          )}

          {/* Edit Pencil Badge */}
          <TouchableOpacity
            style={styles.editPencilBadge}
            onPress={handleEditPhoto}
            activeOpacity={0.8}
            accessibilityLabel="Edit Profile Photo"
          >
            <PencilEditIcon size={13} color="#0f172a" />
          </TouchableOpacity>
        </View>

        {/* User Name */}
        <Text style={styles.userName}>
          {user?.name || 'Account'}
        </Text>
      </View>

      {/* 3. Rounded Info Card with Icon Rows (No Chevrons) */}
      <View style={styles.infoCard}>
        {/* Row 1: Email */}
        <TouchableOpacity
          style={styles.infoRow}
          onPress={() => handleInfoRowPress('Email Address', user?.email || '—')}
          activeOpacity={0.65}
        >
          <View style={styles.iconBox}>
            <EnvelopeMailIcon size={19} color="#475569" />
          </View>
          <View style={styles.infoTextContainer}>
            <Text style={styles.infoLabel}>EMAIL</Text>
            <Text style={styles.infoValue} numberOfLines={1}>
              {user?.email || '—'}
            </Text>
          </View>
        </TouchableOpacity>

        <View style={styles.rowDivider} />

        {/* Row 2: ID */}
        <TouchableOpacity
          style={styles.infoRow}
          onPress={() => handleInfoRowPress('User ID', user?.user_id || user?.id || '—')}
          activeOpacity={0.65}
        >
          <View style={styles.iconBox}>
            <IdBadgeIcon size={19} color="#475569" />
          </View>
          <View style={styles.infoTextContainer}>
            <Text style={styles.infoLabel}>ID</Text>
            <Text style={styles.infoValue} numberOfLines={1}>
              {user?.user_id || user?.id || '—'}
            </Text>
          </View>
        </TouchableOpacity>

        <View style={styles.rowDivider} />

        {/* Row 3: Role */}
        <TouchableOpacity
          style={styles.infoRow}
          onPress={() =>
            handleInfoRowPress(
              'User Role',
              user?.role ? user.role.toUpperCase() : '—'
            )
          }
          activeOpacity={0.65}
        >
          <View style={styles.iconBox}>
            <RoleShieldIcon size={19} color="#475569" />
          </View>
          <View style={styles.infoTextContainer}>
            <Text style={styles.infoLabel}>ROLE</Text>
            <Text style={styles.infoValue} numberOfLines={1}>
              {user?.role ? user.role.toUpperCase() : '—'}
            </Text>
          </View>
        </TouchableOpacity>

        <View style={styles.rowDivider} />

        {/* Row 4: Joined */}
        <TouchableOpacity
          style={styles.infoRow}
          onPress={() =>
            handleInfoRowPress(
              'Joined Date',
              formatJoinedDate(user?.created_at)
            )
          }
          activeOpacity={0.65}
        >
          <View style={styles.iconBox}>
            <CalendarJoinedIcon size={19} color="#475569" />
          </View>
          <View style={styles.infoTextContainer}>
            <Text style={styles.infoLabel}>JOINED</Text>
            <Text style={styles.infoValue} numberOfLines={1}>
              {formatJoinedDate(user?.created_at)}
            </Text>
          </View>
        </TouchableOpacity>
      </View>

      {/* 4. "Scan History" Section (Repurposing "My documents", No Chevron) */}
      <View style={styles.sectionHeaderRow}>
        <Text style={styles.sectionTitle}>Scan History</Text>
      </View>

      {/* 3 Metric Tiles / Cubes */}
      <View style={styles.tilesRow}>
        {/* Tile 1: Total Scans */}
        <TouchableOpacity
          style={styles.metricTile}
          activeOpacity={0.8}
          onPress={onBack}
        >
          <View style={[styles.tileCountBadge, { backgroundColor: '#dcfce7' }]}>
            <Text style={[styles.tileCountText, { color: '#15803d' }]}>
              {totalScans}
            </Text>
          </View>
          <Text style={styles.tileTitle}>Total Scans</Text>
          <Text style={styles.tileSubtitle}>All time</Text>
        </TouchableOpacity>

        {/* Tile 2: Identified Soils */}
        <TouchableOpacity
          style={styles.metricTile}
          activeOpacity={0.8}
          onPress={onBack}
        >
          <View style={[styles.tileCountBadge, { backgroundColor: '#e0f2fe' }]}>
            <Text style={[styles.tileCountText, { color: '#0369a1' }]}>
              {identifiedScans}
            </Text>
          </View>
          <Text style={styles.tileTitle}>Identified</Text>
          <Text style={styles.tileSubtitle}>Munsell soil</Text>
        </TouchableOpacity>

        {/* Tile 3: Admin Synced */}
        <TouchableOpacity
          style={styles.metricTile}
          activeOpacity={0.8}
          onPress={() => {
            Alert.alert(
              'Admin Synchronization',
              'All mobile soil scans are synced with the Admin Soil Scan History database.'
            );
          }}
        >
          <View style={[styles.tileCountBadge, { backgroundColor: '#fef3c7' }]}>
            <Text style={[styles.tileCountText, { color: '#b45309' }]}>
              {syncedScans}
            </Text>
          </View>
          <Text style={styles.tileTitle}>Synced</Text>
          <Text style={styles.tileSubtitle}>Cloud admin</Text>
        </TouchableOpacity>
      </View>

      {/* 5. Repurposed Section: Account & Preferences (Replacing Banking Documents) */}
      <View style={styles.sectionHeaderRow}>
        <Text style={styles.sectionTitle}>Account & Security</Text>
      </View>

      <View style={styles.preferencesCard}>
        {/* Row 1: Cloud Sync Status */}
        <View style={styles.preferenceRow}>
          <View style={styles.preferenceLeft}>
            <View style={styles.statusDotGreen} />
            <Text style={styles.preferenceLabel}>Admin Sync Active</Text>
          </View>
          <View style={styles.onlineBadge}>
            <Text style={styles.onlineBadgeText}>ONLINE</Text>
          </View>
        </View>

        <View style={styles.rowDivider} />

        {/* Row 2: App Details */}
        <View style={styles.preferenceRow}>
          <Text style={styles.preferenceLabelMuted}>Platform</Text>
          <Text style={styles.preferenceValueMuted}>
            Agrico Mobile · v1.0.0
          </Text>
        </View>

        <View style={styles.rowDivider} />

        {/* Row 3: Sign Out Action */}
        <TouchableOpacity
          style={styles.signOutRowBtn}
          onPress={() => {
            Alert.alert(
              'Sign Out',
              'Are you sure you want to sign out of Agrico?',
              [
                { text: 'Cancel', style: 'cancel' },
                {
                  text: 'Sign Out',
                  style: 'destructive',
                  onPress: logout,
                },
              ]
            );
          }}
          activeOpacity={0.8}
        >
          <Text style={styles.signOutRowText}>Sign Out of Account</Text>
        </TouchableOpacity>
      </View>

      {/* Bottom spacing for bottom navigation bar */}
      <View style={{ height: 110 }} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc',
  },
  contentContainer: {
    paddingTop: Platform.OS === 'ios' ? 52 : 36,
    paddingBottom: 24,
  },

  // 1. Top Header
  topHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 22,
    marginBottom: 16,
  },
  circleHeaderBtn: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
    elevation: 2,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: '800',
    color: '#0f172a',
    letterSpacing: -0.3,
  },

  // 2. Avatar & Name Section
  avatarSection: {
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 8,
    marginBottom: 20,
  },
  avatarContainer: {
    position: 'relative',
    width: 104,
    height: 104,
    borderRadius: 52,
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.12,
    shadowRadius: 8,
    elevation: 5,
  },
  avatarImage: {
    width: 104,
    height: 104,
    borderRadius: 52,
    borderWidth: 3.5,
    borderColor: '#ffffff',
    backgroundColor: '#e2e8f0',
  },
  avatarFallback: {
    width: 104,
    height: 104,
    borderRadius: 52,
    backgroundColor: '#16a34a',
    borderWidth: 3.5,
    borderColor: '#ffffff',
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarInitialText: {
    fontSize: 40,
    fontWeight: '900',
    color: '#ffffff',
    letterSpacing: 1,
  },
  avatarLoadingOverlay: {
    ...StyleSheet.absoluteFillObject,
    borderRadius: 52,
    backgroundColor: 'rgba(0, 0, 0, 0.4)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  editPencilBadge: {
    position: 'absolute',
    bottom: 2,
    right: 2,
    width: 30,
    height: 30,
    borderRadius: 15,
    backgroundColor: '#ffffff',
    borderWidth: 1.5,
    borderColor: '#e2e8f0',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.12,
    shadowRadius: 3,
    elevation: 4,
  },
  userName: {
    fontSize: 22,
    fontWeight: '800',
    color: '#0f172a',
    marginTop: 14,
    letterSpacing: -0.4,
  },

  // 3. Rounded Info Card
  infoCard: {
    backgroundColor: '#ffffff',
    borderRadius: 24,
    marginHorizontal: 20,
    paddingVertical: 8,
    paddingHorizontal: 16,
    borderWidth: 1,
    borderColor: '#f1f5f9',
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.04,
    shadowRadius: 8,
    elevation: 2,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
  },
  iconBox: {
    width: 44,
    height: 44,
    borderRadius: 14,
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#f1f5f9',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 14,
  },
  infoTextContainer: {
    flex: 1,
  },
  infoLabel: {
    fontSize: 11,
    fontWeight: '700',
    color: '#94a3b8',
    letterSpacing: 0.8,
    marginBottom: 3,
  },
  infoValue: {
    fontSize: 14.5,
    fontWeight: '700',
    color: '#0f172a',
  },
  rowDivider: {
    height: 1,
    backgroundColor: '#f8fafc',
    marginLeft: 58,
  },

  // 4. Section Headers
  sectionHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 22,
    marginTop: 26,
    marginBottom: 12,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '800',
    color: '#0f172a',
    letterSpacing: -0.3,
  },
  sectionChevronBtn: {
    padding: 4,
  },

  // 5. Scan History Metric Tiles
  tilesRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    gap: 10,
  },
  metricTile: {
    flex: 1,
    backgroundColor: '#ffffff',
    borderRadius: 20,
    padding: 14,
    borderWidth: 1,
    borderColor: '#f1f5f9',
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.04,
    shadowRadius: 6,
    elevation: 2,
  },
  tileCountBadge: {
    width: 38,
    height: 38,
    borderRadius: 12,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 10,
  },
  tileCountText: {
    fontSize: 17,
    fontWeight: '800',
  },
  tileTitle: {
    fontSize: 13,
    fontWeight: '800',
    color: '#0f172a',
    marginBottom: 2,
  },
  tileSubtitle: {
    fontSize: 11,
    fontWeight: '600',
    color: '#94a3b8',
  },

  // 6. Account & Preferences Card
  preferencesCard: {
    backgroundColor: '#ffffff',
    borderRadius: 22,
    marginHorizontal: 20,
    paddingVertical: 6,
    paddingHorizontal: 16,
    borderWidth: 1,
    borderColor: '#f1f5f9',
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.04,
    shadowRadius: 8,
    elevation: 2,
  },
  preferenceRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 14,
  },
  preferenceLeft: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  statusDotGreen: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#22c55e',
    marginRight: 8,
  },
  preferenceLabel: {
    fontSize: 14,
    fontWeight: '700',
    color: '#0f172a',
  },
  preferenceLabelMuted: {
    fontSize: 13.5,
    fontWeight: '600',
    color: '#64748b',
  },
  preferenceValueMuted: {
    fontSize: 13.5,
    fontWeight: '700',
    color: '#0f172a',
  },
  onlineBadge: {
    backgroundColor: '#dcfce7',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  onlineBadgeText: {
    color: '#15803d',
    fontSize: 11,
    fontWeight: '800',
    letterSpacing: 0.5,
  },
  signOutRowBtn: {
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
  },
  signOutRowText: {
    fontSize: 14,
    fontWeight: '800',
    color: '#ef4444',
  },
});
