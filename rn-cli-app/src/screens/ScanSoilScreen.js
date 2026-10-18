import React, { useState, useEffect, useRef, useMemo } from 'react';
import {
  View,
  Text,
  Image,
  TouchableOpacity,
  Pressable,
  StyleSheet,
  StatusBar,
  Animated,
  Easing,
  Dimensions,
  Platform,
  Modal,
  ScrollView,
  Alert,
  ActivityIndicator,
  NativeModules,
} from 'react-native';
import { launchCamera, launchImageLibrary } from 'react-native-image-picker';
import RNFS from 'react-native-fs';
import { Camera, useCameraDevice, useCameraPermission } from 'react-native-vision-camera';
import NetInfo from '@react-native-community/netinfo';
import { newScanId, startScan, pollScan } from '../services/scanAsync';
import {
  queueAsyncScan,
  trackInflightScan,
  clearQueuedScan,
  flushOfflineQueue,
  mapServerScan,
} from '../services/scanSyncService';
import { analyzeSoilOffline } from '../services/offlineSoilAnalysis';
import { saveLocalScan } from '../services/localScanStore';
import { FlashlightIcon, GalleryIcon } from '../components/AppIcons';
import BottomNavBar from '../components/BottomNavBar';
import PinSoilLocationModal from '../components/PinSoilLocationModal';
import ResizableFrame, { ScanFrameVisual, frameToImageCrop } from '../components/ResizableFrame';

const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window');

// 6-Stage Soil Determination Pipeline Stages (mirrors the Python ML pipeline & website)
export const STAGE_META = {
  0: {
    stage: 0,
    targetPct: 15,
    badge: 'INIT',
    title: 'Initializing pipeline…',
    sub: 'Preparing soil analysis engine',
    name: 'Pipeline Initialization',
  },
  1: {
    stage: 1,
    targetPct: 32,
    badge: 'STAGE 1',
    title: 'Stage 1/6: Soil Gate Validation…',
    sub: 'Verifying genuine soil grain texture & non-soil rejection',
    name: 'Soil Gate (Texture & Non-Soil Filter)',
    tag: 'Texture & Non-Soil Filter',
  },
  2: {
    stage: 2,
    targetPct: 50,
    badge: 'STAGE 2',
    title: 'Stage 2/6: Lighting Normalization…',
    sub: 'Balancing dark & light exposure to center (Target L* 50.0)',
    name: 'Lighting Normalizer (LAB L* 50% Center)',
    tag: 'LAB L* 50% Center',
  },
  3: {
    stage: 3,
    targetPct: 68,
    badge: 'STAGE 3',
    title: 'Stage 3/6: Multi-Color Detection…',
    sub: 'Extracting dominant soil color clusters via ColorThief',
    name: 'Multi-Color Detection (ColorThief Palette)',
    tag: 'ColorThief Palette',
  },
  4: {
    stage: 4,
    targetPct: 82,
    badge: 'STAGE 4',
    title: 'Stage 4/6: Per-Region Verification…',
    sub: 'Re-checking individual color regions through soil gate',
    name: 'Per-Region Verification (Patch Gate Validation)',
    tag: 'Patch Gate Validation',
  },
  5: {
    stage: 5,
    targetPct: 94,
    badge: 'STAGE 5',
    title: 'Stage 5/6: Munsell Spectral Match…',
    sub: 'Calculating CIEDE2000 (ΔE₀₀) color distance to Munsell chart',
    name: 'Munsell Spectral Match (CIEDE2000 ΔE₀₀)',
    tag: 'CIEDE2000 ΔE₀₀',
  },
  6: {
    stage: 6,
    targetPct: 98,
    badge: 'STAGE 6',
    title: 'Stage 6/6: Agronomic Profile & Centroid…',
    sub: 'Generating soil properties, drainage & crop profile',
    name: 'Agronomic Profile & Reference Centroid',
    tag: 'Profile & Reference Centroid',
  },
};

// Helper download tray icon matching website Save Soil Scan button
function DownloadIcon({ size = 18, color = '#16a34a' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center', marginRight: 8 }}>
      <View style={{ width: 2, height: size * 0.48, backgroundColor: color, position: 'absolute', top: 1 }} />
      <View
        style={{
          width: size * 0.35,
          height: size * 0.35,
          borderBottomWidth: 2,
          borderRightWidth: 2,
          borderColor: color,
          transform: [{ rotate: '45deg' }],
          position: 'absolute',
          top: size * 0.22,
        }}
      />
      <View
        style={{
          width: size * 0.85,
          height: size * 0.32,
          borderBottomWidth: 2,
          borderLeftWidth: 2,
          borderRightWidth: 2,
          borderColor: color,
          borderBottomLeftRadius: 3,
          borderBottomRightRadius: 3,
          position: 'absolute',
          bottom: 1,
        }}
      />
    </View>
  );
}

// Helper map pin icon matching Pin Soil Location button
function MapPinIcon({ size = 18, color = '#ffffff' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center', marginRight: 8 }}>
      <View
        style={{
          width: size * 0.65,
          height: size * 0.65,
          borderRadius: (size * 0.65) / 2,
          backgroundColor: color,
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        <View
          style={{
            width: size * 0.22,
            height: size * 0.22,
            borderRadius: (size * 0.22) / 2,
            backgroundColor: '#15803d',
          }}
        />
      </View>
      <View
        style={{
          width: 0,
          height: 0,
          borderLeftWidth: size * 0.18,
          borderRightWidth: size * 0.18,
          borderTopWidth: size * 0.32,
          borderLeftColor: 'transparent',
          borderRightColor: 'transparent',
          borderTopColor: color,
          marginTop: -1.5,
        }}
      />
    </View>
  );
}

// Helper download tray icon matching website Save Copy button
function SaveTrayIcon({ size = 15, color = '#DAF1DE' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View style={{ width: 1.8, height: size * 0.42, backgroundColor: color, marginBottom: -1 }} />
      <View
        style={{
          width: 0,
          height: 0,
          borderLeftWidth: size * 0.22,
          borderRightWidth: size * 0.22,
          borderTopWidth: size * 0.22,
          borderLeftColor: 'transparent',
          borderRightColor: 'transparent',
          borderTopColor: color,
        }}
      />
      <View
        style={{
          width: size * 0.85,
          height: size * 0.3,
          borderWidth: 1.6,
          borderColor: color,
          borderTopWidth: 0,
          borderRadius: 2,
          marginTop: 1.5,
        }}
      />
    </View>
  );
}

// Helper refresh icon matching website Retake / Live Camera button
function RefreshIcon({ size = 15, color = '#DAF1DE' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <Text style={{ fontSize: size * 1.1, color, fontWeight: '800', lineHeight: size * 1.2 }}>↻</Text>
    </View>
  );
}

export default function ScanSoilScreen({ navigation }) {
  // Flashlight / Torch state
  const [isTorchOn, setIsTorchOn] = useState(false);

  // Vision Camera hooks
  const { hasPermission, requestPermission } = useCameraPermission();
  const cameraDevice = useCameraDevice('back');
  const cameraRef = useRef(null);

  useEffect(() => {
    if (!hasPermission) {
      requestPermission();
    }
  }, [hasPermission]);

  // Scanning process states
  const [isScanning, setIsScanning] = useState(false);
  const [hasScanned, setHasScanned] = useState(false);
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [showPinModal, setShowPinModal] = useState(false);
  const [userCustomImage, setUserCustomImage] = useState(null);
  const [croppedSpecimenUri, setCroppedSpecimenUri] = useState(null);
  const frozenOriginalPhotoRef = useRef(null);
  const [syncStatus, setSyncStatus] = useState(null);
  const [toastMessage, setToastMessage] = useState('');
  const [pinnedLocation, setPinnedLocation] = useState(null);

  // Layout & Bounds
  const [camBounds, setCamBounds] = useState({ x: 0, y: 0, width: SCREEN_WIDTH, height: SCREEN_HEIGHT });
  const [dockTop, setDockTop] = useState(null);
  const frameRectRef = useRef(null);

  // Scan state & Progress Engine
  const [scanResult, setScanResult] = useState(null);   // mapped server scan
  const [scanStage, setScanStage] = useState(0);        // 0..6 from the pipeline
  const [progressPct, setProgressPct] = useState(1);    // 1..100 continuous smooth % counter
  const [rejectMsg, setRejectMsg] = useState(null);     // soft-reject message
  const abortRef = useRef(null);
  const progressTimerRef = useRef(null);
  const currentPctRef = useRef(1);
  const targetPctRef = useRef(15);

  const currentSpecimen = scanResult; // real result only; null until a scan completes

  // Animated values
  const flashAnim = useRef(new Animated.Value(0)).current;
  const resultCardAnim = useRef(new Animated.Value(0)).current;
  const toastAnim = useRef(new Animated.Value(0)).current;

  // Compute frame bounds confined between the top header and the controls dock
  const headerBottom = (Platform.OS === 'ios' ? 54 : 44) + 44 + 16;
  const frameBounds = useMemo(() => ({
    x: 16,
    y: headerBottom,
    width: Math.max(0, camBounds.width - 32),
    height: Math.max(0, (dockTop ?? camBounds.height * 0.6) - 12 - headerBottom),
  }), [camBounds, dockTop, headerBottom]);

  // Cleanup effect
  useEffect(() => () => {
    abortRef.current?.abort();
    if (progressTimerRef.current) clearInterval(progressTimerRef.current);
  }, []);

  // Continuous Progress Counter (mirrors website: 1%, 2%, 3%... with stage bounds & creep)
  const startProgressEngine = () => {
    if (progressTimerRef.current) clearInterval(progressTimerRef.current);
    setProgressPct(1);
    currentPctRef.current = 1;
    targetPctRef.current = STAGE_META[0].targetPct;
    let creepTicks = 0;
    progressTimerRef.current = setInterval(() => {
      if (currentPctRef.current < targetPctRef.current) {
        currentPctRef.current += 1;
        setProgressPct(currentPctRef.current);
      } else if (currentPctRef.current < 96) {
        // Slowly creep forward 1% every ~280ms while awaiting next stage
        creepTicks++;
        if (creepTicks % 8 === 0) {
          currentPctRef.current += 1;
          setProgressPct(currentPctRef.current);
        }
      }
    }, 35);
  };

  const advanceStage = (stage) => {
    setScanStage(stage);
    targetPctRef.current = STAGE_META[stage]?.targetPct ?? 96;
  };

  const stopProgressEngine = () => {
    if (progressTimerRef.current) {
      clearInterval(progressTimerRef.current);
      progressTimerRef.current = null;
    }
  };

  const finishProgressEngine = async () => {
    stopProgressEngine();
    while (currentPctRef.current < 100) {
      currentPctRef.current += 1;
      setProgressPct(currentPctRef.current);
      await new Promise((r) => setTimeout(r, 12));
    }
    await new Promise((r) => setTimeout(r, 180));
  };

  // Queue flush effect on screen focus & NetInfo reconnect
  useEffect(() => {
    const run = async () => {
      const r = await flushOfflineQueue();
      if (r.flushed > 0) {
        triggerAutoSaveToast(`✓ ${r.flushed} queued scan(s) analyzed and saved`);
      }
      if (r.rejected > 0) {
        triggerAutoSaveToast(`${r.rejected} queued scan(s) were not valid soil`);
      }
    };
    const unsubFocus = navigation.addListener('focus', run);
    const unsubNet = NetInfo.addEventListener((s) => {
      if (s.isConnected) run();
    });
    return () => {
      unsubFocus();
      unsubNet();
    };
  }, [navigation]);

  // Trigger floating auto-save confirmation toast
  const triggerAutoSaveToast = (msg) => {
    setToastMessage(msg);
    toastAnim.setValue(0);
    Animated.sequence([
      Animated.timing(toastAnim, {
        toValue: 1,
        duration: 320,
        easing: Easing.out(Easing.ease),
        useNativeDriver: true,
      }),
      Animated.delay(3500),
      Animated.timing(toastAnim, {
        toValue: 0,
        duration: 320,
        easing: Easing.in(Easing.ease),
        useNativeDriver: true,
      }),
    ]).start(() => {
      setToastMessage('');
    });
  };

  const playCaptureAnimations = () => {
    flashAnim.setValue(0.7);
    Animated.timing(flashAnim, {
      toValue: 0,
      duration: 350,
      easing: Easing.out(Easing.ease),
      useNativeDriver: true,
    }).start();
  };

  const getCurrentGps = () => ({
    latitude: pinnedLocation?.latitude ?? null,
    longitude: pinnedLocation?.longitude ?? null,
    locationName: pinnedLocation?.locationName ?? pinnedLocation?.barangay ?? pinnedLocation?.name ?? null,
  });

  const capturePhoto = async () => {
    try {
      // 1. Try taking photo with live VisionCamera if available and active
      if (cameraRef.current && cameraDevice && hasPermission) {
        const photo = await cameraRef.current.takePhoto({
          flash: isTorchOn ? 'on' : 'off',
          enableShutterSound: true,
        });
        if (photo?.path) {
          const fileUri = photo.path.startsWith('file://') ? photo.path : `file://${photo.path}`;
          const rawPath = photo.path.replace('file://', '');
          const base64 = await RNFS.readFile(rawPath, 'base64');
          return { uri: fileUri, base64 };
        }
      }

      // 2. Fallback to native system camera picker
      const res = await launchCamera({
        mediaType: 'photo',
        cameraType: 'back',
        quality: 0.7,
        maxWidth: 1280,
        maxHeight: 1280,
        includeBase64: true,
        saveToPhotos: false,
      });
      const asset = res.assets?.[0];
      if (asset?.base64) return { uri: asset.uri, base64: asset.base64 };
    } catch (err) {
      console.warn('[Camera] Capture error:', err?.message);
    }
    return null;
  };

  const runScan = async (photo) => {
    if (isScanning) return;
    setIsScanning(true);
    setScanResult(null);
    setRejectMsg(null);
    setScanStage(0);
    setSyncStatus(null);
    setHasScanned(false);
    resultCardAnim.setValue(0);
    playCaptureAnimations();
    startProgressEngine();

    // Development demo fallback when running in simulator or without real camera photo
    if (photo?.isDemo || !photo?.base64) {
      setTimeout(() => advanceStage(1), 350);
      setTimeout(() => advanceStage(2), 750);
      setTimeout(() => advanceStage(3), 1150);
      setTimeout(() => advanceStage(4), 1550);
      setTimeout(() => advanceStage(5), 1950);
      setTimeout(async () => {
        advanceStage(6);
        await finishProgressEngine();
        setIsScanning(false);
        const demoSpecimen = mapServerScan({
          soil_scan_id: Math.floor(1000 + Math.random() * 9000),
          soil_name: 'Dark Reddish Gray',
          munsell_code: '10R 4/1',
          hue: '10R',
          confidence: '96%',
          description: '10R 4/1 - Dark Reddish Gray (Mineral Clay Subsoil)',
        }, {
          hex: '#5A4643',
          organic_matter: 'High (3.8%)',
          soil_type: 'Mineral Clay Subsoil',
        });
        setScanResult(demoSpecimen);
        setHasScanned(true);
        setShowDetailModal(true);
        setSyncStatus(null); // Do not show fake synced badge on demo
        Animated.spring(resultCardAnim, { toValue: 1, friction: 6, tension: 70, useNativeDriver: true }).start();
      }, 2350);
      return;
    }

    const gps = getCurrentGps();
    const scanId = newScanId();

    const runOfflineAnalysisFlow = async () => {
      // STAGE 1: Immediate gate check
      advanceStage(1);

      // Perform local spectral extraction & Stage 1 Soil Gate validation
      const offlineSpecimen = await analyzeSoilOffline({
        imageUri: photo.uri,
        imageBase64: photo.base64,
      });

      // If Stage 1 rejected non-soil, halt immediately!
      if (!offlineSpecimen || offlineSpecimen.rejected || !offlineSpecimen.isValidSoil) {
        stopProgressEngine();
        setIsScanning(false);
        await clearQueuedScan(scanId);
        setRejectMsg(offlineSpecimen?.message || 'Stage 1 Soil Gate: No genuine soil detected. Please point camera directly at real soil.');
        return;
      }

      // Smoothly advance through Stages 2 to 5 for verified genuine soil
      setTimeout(() => advanceStage(2), 350);
      setTimeout(() => advanceStage(3), 750);
      setTimeout(() => advanceStage(4), 1150);
      setTimeout(() => advanceStage(5), 1550);

      // Queue for background server synchronization once connectivity is restored
      try {
        await queueAsyncScan({ scanId, imageUri: photo.uri, ...gps });
      } catch (e) {
        console.warn('[ScanSoilScreen] queue failed:', e?.message);
      }

      // Complete pipeline & show results at Stage 6
      setTimeout(async () => {
        advanceStage(6);
        await finishProgressEngine();
        setIsScanning(false);
        setScanResult(offlineSpecimen);
        setHasScanned(true);
        setShowDetailModal(true);
        setSyncStatus({ queued: true, isOffline: true });
        Animated.spring(resultCardAnim, { toValue: 1, friction: 6, tension: 70, useNativeDriver: true }).start();
        triggerAutoSaveToast('✓ Identified offline (Queued to sync)');

        // Persist offline scan directly into local store for Scan History
        const now = new Date();
        saveLocalScan({
          id: 'offline_' + scanId,
          scanId,
          soil_scan_id: scanId,
          munsellCode: offlineSpecimen.munsellCode,
          hue: offlineSpecimen.hueCode,
          colorName: offlineSpecimen.name,
          hexCode: offlineSpecimen.hexColor,
          description: offlineSpecimen.description,
          scansCount: 1,
          scanImage: photo.uri,
          rawDate: now.toISOString(),
          scanTime: now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
          dateGroup: 'Today',
          platform: 'mobile',
          isOffline: true,
          syncState: 'queued',
          soil_organic_index: offlineSpecimen.organicIndex,
          soil_type: offlineSpecimen.soilType,
          nearMatchRating: offlineSpecimen.nearMatchRating,
          confidence: offlineSpecimen.confidence,
          latitude: gps?.latitude ?? null,
          longitude: gps?.longitude ?? null,
          locationName: gps?.locationName ?? null,
        }).catch(() => {});
      }, 2000);
    };

    const net = await NetInfo.fetch();
    if (!net.isConnected) {
      await runOfflineAnalysisFlow();
      return;
    }

    abortRef.current = new AbortController();
    try {
      await startScan({
        scanId,
        imageBase64: `data:image/jpeg;base64,${photo.base64}`,
        ...gps,
      });
    } catch (e) {
      // Server upload unreachable: seamlessly fall back to on-device offline analysis
      await runOfflineAnalysisFlow();
      return;
    }
    await trackInflightScan(scanId); // lets a killed app resume polling on next flush

    const result = await pollScan(scanId, {
      signal: abortRef.current.signal,
      onProgress: (stageNum) => advanceStage(stageNum),
    });

    if (result.status === 'done') {
      await finishProgressEngine();
      setIsScanning(false);
      await clearQueuedScan(scanId);
      const mapped = mapServerScan(result.scan, result.data);
      setScanResult(mapped);
      setHasScanned(true);
      setShowDetailModal(true);
      setSyncStatus({ synced: true, scanId: result.scan.soil_scan_id });
      Animated.spring(resultCardAnim, { toValue: 1, friction: 6, tension: 70, useNativeDriver: true }).start();
      triggerAutoSaveToast(`✓ Saved to Scan History (#${result.scan.soil_scan_id})`);

      // Persist synced scan directly into local store
      const now = new Date();
      saveLocalScan({
        id: 'scan_' + result.scan.soil_scan_id,
        scanId,
        soil_scan_id: result.scan.soil_scan_id,
        munsellCode: mapped.munsellCode,
        hue: mapped.hueCode,
        colorName: mapped.name,
        hexCode: mapped.hexColor,
        description: mapped.description,
        scansCount: mapped.scanCount,
        scanImage: mapped.imageUrl || photo.uri,
        rawDate: result.scan.created_at || now.toISOString(),
        scanTime: now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        dateGroup: 'Today',
        platform: 'mobile',
        isOffline: false,
        syncState: 'synced',
        soil_organic_index: mapped.organicIndex,
        soil_type: mapped.soilType,
        nearMatchRating: mapped.nearMatchRating,
        confidence: mapped.confidence,
        latitude: gps?.latitude ?? null,
        longitude: gps?.longitude ?? null,
        locationName: gps?.locationName ?? null,
      }).catch(() => {});
    } else if (result.status === 'rejected') {
      stopProgressEngine();
      setIsScanning(false);
      await clearQueuedScan(scanId);
      setRejectMsg(result.message || 'No valid soil sample detected.');
    } else if (result.status === 'auth') {
      stopProgressEngine();
      setIsScanning(false);
      navigation.navigate('Login');
    } else {
      // Dropped or interrupted mid-flight: fall back to offline analysis immediately
      await runOfflineAnalysisFlow();
    }
  };

  const cropToFrame = async (photo) => {
    if (!photo?.uri) return photo;
    try {
      const rect = frameRectRef.current || {
        x: frameBounds.x + (frameBounds.width - 240) / 2,
        y: frameBounds.y + (frameBounds.height - 240) / 2,
        width: 240,
        height: 240,
      };
      const pW = camBounds.width || SCREEN_WIDTH;
      const pH = camBounds.height || SCREEN_HEIGHT;

      if (NativeModules?.InstallApk?.cropImageWithFrame) {
        const result = await NativeModules.InstallApk.cropImageWithFrame(
          photo.uri,
          rect.x,
          rect.y,
          rect.width,
          rect.height,
          pW,
          pH
        );
        if (result?.uri && result?.base64) {
          return {
            uri: result.uri,
            base64: result.base64,
            width: result.width,
            height: result.height,
          };
        }
      }
    } catch (err) {
      console.warn('[ScanSoilScreen] cropToFrame error:', err?.message);
    }
    return photo;
  };

  const handleRetake = () => {
    frozenOriginalPhotoRef.current = null;
    setCroppedSpecimenUri(null);
    setUserCustomImage(null);
    setScanResult(null);
    setRejectMsg(null);
    setHasScanned(false);
    setShowDetailModal(false);
    resultCardAnim.setValue(0);
  };

  const handleManualScan = async () => {
    if (isScanning) return;
    const photo = await capturePhoto();
    if (photo && photo.base64) {
      const uriToUse = photo.uri || `data:image/jpeg;base64,${photo.base64}`;
      frozenOriginalPhotoRef.current = { uri: uriToUse, base64: photo.base64 };
      setUserCustomImage(uriToUse);
      const cropped = await cropToFrame({ uri: uriToUse, base64: photo.base64 });
      setCroppedSpecimenUri(cropped.uri);
      runScan(cropped);
    } else if (__DEV__) {
      runScan({ uri: null, base64: null, isDemo: true });
    }
    // In release builds, cancelled camera does nothing
  };

  const handleLaunchGallery = async () => {
    try {
      const res = await launchImageLibrary({
        mediaType: 'photo',
        quality: 0.7,
        maxWidth: 1280,
        maxHeight: 1280,
        includeBase64: true,
      });
      const a = res.assets?.[0];
      if (a?.base64) {
        const uriToUse = a.uri || `data:image/jpeg;base64,${a.base64}`;
        frozenOriginalPhotoRef.current = { uri: uriToUse, base64: a.base64 };
        setUserCustomImage(uriToUse);
        const cropped = await cropToFrame({ uri: uriToUse, base64: a.base64 });
        setCroppedSpecimenUri(cropped.uri);
        runScan(cropped);
      }
    } catch (err) {
      Alert.alert('Gallery', 'Unable to open gallery');
    }
  };

  const handleScanFrozenPhoto = async () => {
    if (isScanning) return;
    const rawPhoto = frozenOriginalPhotoRef.current || (userCustomImage ? { uri: userCustomImage } : null);
    if (!rawPhoto) {
      handleManualScan();
      return;
    }
    const cropped = await cropToFrame(rawPhoto);
    setCroppedSpecimenUri(cropped.uri);
    runScan(cropped);
  };

  const handleToggleFlashlight = () => {
    setIsTorchOn((prev) => !prev);
  };

  // Bottom Navigation tab press
  const handleTabPress = (tab) => {
    if (tab === 'Home') {
      navigation.navigate('Home');
    } else if (tab === 'Munsell') {
      navigation.navigate('Home', { screen: 'Munsell' });
    } else if (tab === 'ScanSoil') {
      if (userCustomImage) {
        // User adjusted the resize frame over frozen photo and tapped Scan Soil
        handleScanFrozenPhoto();
      } else {
        handleManualScan();
      }
    }
  };

  return (
    <View
      style={styles.container}
      onLayout={(e) => {
        const { width, height } = e.nativeEvent.layout;
        setCamBounds({ x: 0, y: 0, width, height });
      }}
    >
      <StatusBar barStyle="light-content" translucent backgroundColor="transparent" />

      {/* 1. Full Live Camera Feed (Edge-to-Edge) */}
      <View style={StyleSheet.absoluteFill}>
        {userCustomImage ? (
          <Image
            source={{ uri: userCustomImage }}
            style={styles.backgroundImage}
            resizeMode="cover"
          />
        ) : cameraDevice != null && hasPermission ? (
          <Camera
            ref={cameraRef}
            style={StyleSheet.absoluteFill}
            device={cameraDevice}
            isActive={!userCustomImage && !showDetailModal && !showPinModal}
            photo={true}
            torch={isTorchOn ? 'on' : 'off'}
          />
        ) : (
          <View style={styles.cameraFallbackContainer}>
            <View style={styles.cameraGridReticle} />
            <Text style={styles.cameraFallbackTitle}>
              {!hasPermission ? 'Camera Access Required' : 'Live Camera Viewfinder'}
            </Text>
            <Text style={styles.cameraFallbackSub}>
              {!hasPermission
                ? 'Allow camera permissions to scan soil live through the viewfinder'
                : 'Position the soil specimen inside the scanning frame'}
            </Text>
            {!hasPermission && (
              <TouchableOpacity
                style={styles.grantPermissionBtn}
                onPress={requestPermission}
                activeOpacity={0.8}
              >
                <Text style={styles.grantPermissionBtnText}>Enable Camera</Text>
              </TouchableOpacity>
            )}
          </View>
        )}

        {/* Torch / Flashlight Simulated Illumination Overlay */}
        {isTorchOn && !cameraDevice && (
          <View style={styles.torchBeamOverlay} pointerEvents="none">
            <View style={styles.torchCenterGlow} />
          </View>
        )}

        {/* Camera shutter flash effect */}
        <Animated.View
          style={[styles.shutterFlash, { opacity: flashAnim }]}
          pointerEvents="none"
        />
      </View>

      {/* 2. Resizable Viewfinder Frame with 3-Tier Contrast Dimming */}
      <ResizableFrame
        bounds={frameBounds}
        dimBounds={camBounds}
        initialSize={Math.min(frameBounds.width * 0.85, frameBounds.height * 0.9, 260)}
        minSize={90}
        square={true}
        disabled={isScanning}
        dimOutside={0.42}
        dimInactive={0.78}
        onChange={(r) => {
          frameRectRef.current = r;
        }}
      >
        {({ width, height }) => (
          <View style={{ width, height }}>
            <ScanFrameVisual width={width} height={height} />

            {/* In-Frame Analyzing HUD */}
            {isScanning && (
              <View style={styles.scanningHudContainer}>
                {/* Stage Pill with Pulsing Dot */}
                <View style={styles.scanLoadingStagePill}>
                  <View style={styles.scanLoadingStageDot} />
                  <Text style={styles.scanLoadingStagePillText}>
                    {scanStage > 0 ? `STAGE ${scanStage}/6` : 'INITIALIZING'}
                  </Text>
                </View>

                {/* Spinner Wrap with Live Center Percentage */}
                <View style={styles.scanLoadingSpinnerWrap}>
                  <ActivityIndicator size="large" color="#22c55e" style={styles.scanLoadingSpinnerRing} />
                  <View style={styles.scanLoadingBadgeCenter}>
                    <Text style={styles.scanLoadingBadgePercent}>{`${progressPct}%`}</Text>
                  </View>
                </View>

                {/* Stage Title & Subtitle */}
                <Text style={styles.scanningHudTitle}>
                  {STAGE_META[scanStage]?.title || 'Analyzing Soil Specimen…'}
                </Text>
                <Text style={styles.scanningHudSubtitle}>
                  {STAGE_META[scanStage]?.sub || 'Processing through ML determination pipeline'}
                </Text>

                {/* Glowing Progress Track & Fill */}
                <View style={styles.scanLoadingProgressTrack}>
                  <View
                    style={[
                      styles.scanLoadingProgressFill,
                      { width: `${Math.min(100, Math.max(progressPct, 3))}%` },
                    ]}
                  />
                </View>
              </View>
            )}
          </View>
        )}
      </ResizableFrame>

      {/* 3. Top Header: "Scanning" / "Photo Review" & Close (X) Button */}
      <View style={styles.topHeader}>
        <View style={{ width: 44 }} />
        <View style={styles.titleContainer}>
          <Text style={styles.headerTitle}>
            {userCustomImage ? 'Photo Review' : 'Scanning'}
          </Text>
          {userCustomImage ? (
            <Text style={[styles.torchActiveSub, { color: '#86efac' }]}>Camera Paused</Text>
          ) : isTorchOn ? (
            <Text style={styles.torchActiveSub}>Torch Active</Text>
          ) : null}
        </View>
        <TouchableOpacity
          style={styles.headerCloseBtn}
          onPress={() => navigation.goBack()}
          activeOpacity={0.8}
          accessibilityLabel="Close Scanning"
        >
          <Text style={styles.closeBtnText}>✕</Text>
        </TouchableOpacity>
      </View>

      {/* Auto-Save & Admin Sync Toast Banner */}
      {toastMessage ? (
        <Animated.View
          style={[
            styles.autoSaveToast,
            {
              opacity: toastAnim,
              transform: [
                {
                  translateY: toastAnim.interpolate({
                    inputRange: [0, 1],
                    outputRange: [-20, 0],
                  }),
                },
              ],
            },
          ]}
          pointerEvents="none"
        >
          <View
            style={[
              styles.autoSaveToastDot,
              syncStatus?.synced && { backgroundColor: '#22c55e' },
            ]}
          />
          <Text style={styles.autoSaveToastText}>{toastMessage}</Text>
        </Animated.View>
      ) : null}

      {/* 4. Hint Pill & Control Buttons Dock (Fixed position above the bottom card) */}
      <View
        style={styles.controlsDock}
        onLayout={(e) => setDockTop(e.nativeEvent.layout.y)}
        pointerEvents="box-none"
      >
        <View style={styles.hintPill}>
          <Text style={styles.hintText}>
            {isScanning
              ? `Processing Stage ${scanStage || 1} of 6… (${progressPct}%)`
              : userCustomImage
              ? 'Photo frozen • Adjust resize box & tap Scan Soil'
              : 'Drag handles to resize • Drag inside to move'}
          </Text>
        </View>

        <View style={styles.controlButtonsRow}>
          {/* Gallery Button */}
          <TouchableOpacity
            style={styles.actionCircleBtn}
            onPress={handleLaunchGallery}
            activeOpacity={0.8}
            accessibilityLabel="Open Gallery"
          >
            <GalleryIcon size={22} color="#ffffff" />
          </TouchableOpacity>

          {/* Flashlight or Retake Button */}
          {userCustomImage ? (
            <TouchableOpacity
              style={[styles.actionCircleBtn, styles.actionCircleBtnRetake]}
              onPress={handleRetake}
              activeOpacity={0.8}
              accessibilityLabel="Retake Photo"
            >
              <Text style={styles.actionCircleRetakeIcon}>🔄</Text>
            </TouchableOpacity>
          ) : (
            <TouchableOpacity
              style={[
                styles.actionCircleBtn,
                isTorchOn && styles.actionCircleBtnTorchOn,
              ]}
              onPress={handleToggleFlashlight}
              activeOpacity={0.8}
              accessibilityLabel="Toggle Flashlight"
            >
              <FlashlightIcon
                size={22}
                color={isTorchOn ? '#facc15' : '#ffffff'}
                isOn={isTorchOn}
              />
              {isTorchOn && <View style={styles.actionTorchDot} />}
            </TouchableOpacity>
          )}
        </View>
      </View>

      {/* 5. Dynamic Bottom Status / Scan Result Card Area */}
      {isScanning ? (
        /* State A: Analyzing Card with 6-Stage Progress */
        <View style={styles.bottomCardWrapper}>
          <View style={styles.analyzingCard}>
            <View style={styles.analyzingSpinnerBox}>
              <Text style={styles.analyzingSpinnerPct}>{`${progressPct}%`}</Text>
            </View>
            <View style={styles.analyzingTextBox}>
              <View style={styles.analyzingStageBadgeRow}>
                <View style={styles.scanLoadingStageDotSmall} />
                <Text style={styles.analyzingCardStageBadge}>
                  {scanStage > 0 ? `STAGE ${scanStage}/6` : 'INITIALIZING'}
                </Text>
              </View>
              <Text style={styles.analyzingCardTitle} numberOfLines={1}>
                {STAGE_META[scanStage]?.title || `Analyzing Soil (Stage ${scanStage || 1}/6)…`}
              </Text>
              <Text style={styles.analyzingCardDesc} numberOfLines={1}>
                {STAGE_META[scanStage]?.sub || 'Processing sample…'}
              </Text>
              <View style={styles.bottomCardProgressTrack}>
                <View
                  style={[
                    styles.bottomCardProgressFill,
                    { width: `${Math.min(100, Math.max(progressPct, 3))}%` },
                  ]}
                />
              </View>
            </View>
          </View>
        </View>
      ) : currentSpecimen ? (
        /* State B: Scan Result Card Preview (Website Result Card Style) */
        <Animated.View
          style={[
            styles.bottomCardWrapper,
            {
              opacity: resultCardAnim,
              transform: [
                {
                  scale: resultCardAnim.interpolate({
                    inputRange: [0, 1],
                    outputRange: [0.92, 1],
                  }),
                },
                {
                  translateY: resultCardAnim.interpolate({
                    inputRange: [0, 1],
                    outputRange: [24, 0],
                  }),
                },
              ],
            },
          ]}
        >
          <TouchableOpacity
            style={styles.stateBResultCard}
            activeOpacity={0.92}
            onPress={() => setShowDetailModal(true)}
          >
            {/* Header: Title + Badge */}
            <View style={styles.stateBHeaderRow}>
              <View style={styles.resultHeaderTitleBox}>
                <Text style={styles.stateBHeaderTitle}>DETERMINED SOIL RECORD</Text>
                <View style={styles.resultMultiBadge}>
                  <Text style={styles.resultMultiBadgeText}>
                    {`${currentSpecimen.regions?.length || 1} DETECTED`}
                  </Text>
                </View>
              </View>
              {syncStatus?.synced ? (
                <View style={styles.syncedAdminBadge}>
                  <Text style={styles.syncedAdminBadgeText}>☁️ Saved</Text>
                </View>
              ) : syncStatus?.queued ? (
                <View style={styles.queuedBadge}>
                  <Text style={styles.queuedBadgeText}>⚡ Queued</Text>
                </View>
              ) : null}
            </View>

            {/* Dual-Preview Soil Banner */}
            <View
              style={[
                styles.stateBSwatchBanner,
                { backgroundColor: currentSpecimen.hexColor || '#594433' },
              ]}
            >
              <View style={styles.stateBThumbBox}>
                {croppedSpecimenUri ? (
                  <Image
                    source={{ uri: croppedSpecimenUri }}
                    style={styles.stateBSoilThumb}
                    resizeMode="cover"
                  />
                ) : currentSpecimen.scanImage ? (
                  <Image
                    source={{ uri: currentSpecimen.scanImage }}
                    style={styles.stateBSoilThumb}
                    resizeMode="cover"
                  />
                ) : currentSpecimen.thumbnail ? (
                  <Image
                    source={
                      typeof currentSpecimen.thumbnail === 'number'
                        ? currentSpecimen.thumbnail
                        : { uri: currentSpecimen.thumbnail?.uri || currentSpecimen.thumbnail }
                    }
                    style={styles.stateBSoilThumb}
                    resizeMode="cover"
                  />
                ) : (
                  <View
                    style={[
                      styles.stateBSoilThumb,
                      { backgroundColor: currentSpecimen.hexColor || '#8F694E' },
                    ]}
                  />
                )}
              </View>

              <View style={styles.stateBDetailsCol}>
                <Text style={styles.stateBMunsellCode}>
                  {currentSpecimen.munsellCode}
                </Text>
                <Text style={styles.stateBColorName} numberOfLines={1}>
                  {currentSpecimen.name}
                </Text>
              </View>

              <View style={styles.stateBHexPill}>
                <Text style={styles.stateBHexText}>
                  {(currentSpecimen.hexColor || '#594433').toUpperCase()}
                </Text>
              </View>
            </View>

            {/* Quick Action Buttons */}
            <View style={styles.stateBActionsRow}>
              <TouchableOpacity
                style={styles.stateBViewBtn}
                onPress={() => setShowDetailModal(true)}
                activeOpacity={0.85}
              >
                <Text style={styles.stateBViewBtnText}>View Result Card</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.stateBRetakeBtn}
                onPress={handleRetake}
                activeOpacity={0.8}
              >
                <Text style={styles.stateBRetakeBtnText}>🔄 Retake Photo</Text>
              </TouchableOpacity>
            </View>
          </TouchableOpacity>
        </Animated.View>
      ) : rejectMsg ? (
        /* State C: Rejection Card */
        <View style={styles.bottomCardWrapper}>
          <View style={styles.analyzingCard}>
            <View style={styles.analyzingTextBox}>
              <Text style={styles.analyzingCardTitle}>No soil detected</Text>
              <Text style={styles.analyzingCardDesc}>{rejectMsg}</Text>
              <View style={styles.rejectionActionsRow}>
                <TouchableOpacity
                  style={styles.rejectionRetakeBtn}
                  onPress={handleRetake}
                  activeOpacity={0.8}
                >
                  <Text style={styles.rejectionRetakeBtnText}>🔄 Retake / Live Camera</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={styles.rejectionDismissBtn}
                  onPress={() => setRejectMsg(null)}
                  activeOpacity={0.8}
                >
                  <Text style={styles.rejectionDismissBtnText}>Dismiss</Text>
                </TouchableOpacity>
              </View>
            </View>
          </View>
        </View>
      ) : (
        /* State D: Initial prompt before scan */
        <View style={styles.bottomCardWrapper} pointerEvents="none">
          <View style={styles.neutralPromptCard}>
            <View style={styles.neutralPulseDot} />
            <Text style={styles.neutralPromptText}>Tap Scan Soil button below to identify soil</Text>
          </View>
        </View>
      )}

      {/* 6. Bottom Navigation Bar with Center Scan Trigger */}
      <BottomNavBar activeTab="ScanSoil" onTabPress={handleTabPress} />

      {/* 7. Soil Record Result Card Modal (Mirrors Website #resultContent inside .result-card Exactly) */}
      {showDetailModal && currentSpecimen ? (
        <Modal
          visible={showDetailModal}
          animationType="fade"
          transparent={true}
          onRequestClose={() => setShowDetailModal(false)}
        >
          <View style={styles.soilModalBackdrop}>
            <Pressable
              style={StyleSheet.absoluteFill}
              onPress={() => setShowDetailModal(false)}
            />
            <View style={styles.soilModalCard} pointerEvents="auto">
              {/* Header */}
              <View style={styles.soilModalHeaderRow}>
                <View>
                  <Text style={styles.soilModalSubHeader}>DETERMINED SOIL RECORD</Text>
                  <Text style={styles.soilModalTitle}>Soil Record Details</Text>
                </View>
                <TouchableOpacity
                  style={styles.soilModalCloseBtn}
                  onPress={() => setShowDetailModal(false)}
                  activeOpacity={0.7}
                  accessibilityLabel="Close details"
                >
                  <Text style={styles.soilModalCloseIcon}>✕</Text>
                </TouchableOpacity>
              </View>

              <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={styles.soilModalScrollContent}
                keyboardShouldPersistTaps="always"
              >
                {/* Dual-Preview Soil Banner (Avatar + Code + Hex Pill) */}
                <View
                  style={[
                    styles.soilBannerDualPreview,
                    { backgroundColor: currentSpecimen.hexColor || currentSpecimen.hex || '#cba985' },
                  ]}
                >
                  {/* Left Avatar Snippet */}
                  <View style={styles.soilBannerAvatarContainer}>
                    {croppedSpecimenUri ? (
                      <Image
                        source={{ uri: croppedSpecimenUri }}
                        style={styles.soilBannerAvatarImg}
                        resizeMode="cover"
                      />
                    ) : currentSpecimen.scanImage ? (
                      <Image
                        source={{ uri: currentSpecimen.scanImage }}
                        style={styles.soilBannerAvatarImg}
                        resizeMode="cover"
                      />
                    ) : currentSpecimen.thumbnail ? (
                      <Image
                        source={
                          typeof currentSpecimen.thumbnail === 'number'
                            ? currentSpecimen.thumbnail
                            : { uri: currentSpecimen.thumbnail?.uri || currentSpecimen.thumbnail }
                        }
                        style={styles.soilBannerAvatarImg}
                        resizeMode="cover"
                      />
                    ) : (
                      <View style={styles.soilBannerAvatarFallback}>
                        <Text style={{ fontSize: 22 }}>🌱</Text>
                      </View>
                    )}
                  </View>

                  {/* Center Code & Label */}
                  <View style={styles.soilBannerCodeBlock}>
                    <Text style={styles.soilBannerMunsellCode}>
                      {currentSpecimen.munsellCode || currentSpecimen.fullCode || '10YR 7/3'}
                    </Text>
                    <Text style={styles.soilBannerSubLabel} numberOfLines={1}>
                      {currentSpecimen.name || currentSpecimen.colorName || 'Very Pale Brown'}
                    </Text>
                  </View>

                  {/* Right Hex Pill */}
                  <View style={styles.soilBannerHexPill}>
                    <Text style={styles.soilBannerHexText}>
                      {(currentSpecimen.hexColor || currentSpecimen.hex || '#CBA985').toUpperCase()}
                    </Text>
                  </View>
                </View>

                {/* Row 1: COLOR NAME | FULL CODE | SCAN COUNT */}
                <View style={styles.soilMetricsRow}>
                  <View style={styles.soilModalGridCard}>
                    <Text style={styles.soilModalLabel}>COLOR NAME</Text>
                    <Text style={styles.soilModalValue} numberOfLines={1}>
                      {currentSpecimen.name || currentSpecimen.colorName || 'Very Pale Brown'}
                    </Text>
                  </View>
                  <View style={styles.soilModalGridCard}>
                    <Text style={styles.soilModalLabel}>FULL CODE</Text>
                    <Text style={styles.soilModalValue} numberOfLines={1}>
                      {currentSpecimen.fullCode || currentSpecimen.munsellCode || '10YR 7/3'}
                    </Text>
                  </View>
                  <View style={styles.soilModalGridCard}>
                    <Text style={styles.soilModalLabel}>SCAN COUNT</Text>
                    <Text style={styles.soilModalValue}>
                      {currentSpecimen.scanCount || '1'}
                    </Text>
                  </View>
                </View>

                {/* Row 2: SOIL ORGANIC | SOIL TYPE | NEAR-MATCH */}
                <View style={styles.soilMetricsRow}>
                  <View style={styles.soilModalGridCard}>
                    <Text style={styles.soilModalLabel}>SOIL ORGANIC</Text>
                    <Text style={styles.soilModalValue} numberOfLines={2}>
                      {currentSpecimen.organicIndex || 'Low (1.1% – 2.0%)'}
                    </Text>
                  </View>
                  <View style={styles.soilModalGridCard}>
                    <Text style={styles.soilModalLabel}>SOIL TYPE</Text>
                    <Text style={styles.soilModalValue} numberOfLines={2}>
                      {currentSpecimen.soilType || 'Dry, light-colored'}
                    </Text>
                  </View>
                  <View style={[styles.soilModalGridCard, styles.soilNearMatchCard]}>
                    <Text style={styles.soilModalLabel}>NEAR-MATCH</Text>
                    <Text style={styles.soilNearMatchValue}>
                      {currentSpecimen.nearMatchRating || currentSpecimen.confidence || '82.7%'}
                    </Text>
                  </View>
                </View>

                {/* Scan Date Card */}
                <View style={styles.soilModalFullCard}>
                  <Text style={styles.soilModalLabel}>SCAN DATE</Text>
                  <Text style={styles.soilModalValue}>
                    {currentSpecimen.scanDate || currentSpecimen.createdDate || currentSpecimen.scanTime || '10/18/2026'}
                  </Text>
                </View>

                {/* Description Card */}
                <View style={styles.soilModalFullCard}>
                  <Text style={styles.soilModalLabel}>DESCRIPTION</Text>
                  <Text style={styles.soilModalDescText}>
                    {currentSpecimen.description ||
                      `Determined Soil: ${currentSpecimen.name || 'Very Pale Brown'} (${currentSpecimen.munsellCode || '10YR 7/3'})${currentSpecimen.soilPercentage != null ? ` - Soil Percentage: ${currentSpecimen.soilPercentage}%` : ''}${currentSpecimen.confidence ? ` - Confidence: ${currentSpecimen.confidence}%` : ' - Confidence: 96.8%'}`}
                  </Text>
                </View>

                {/* Action Buttons: Cancel (Left) & Pin Soil Location (Right) */}
                <View style={styles.soilModalFooterRow}>
                  <TouchableOpacity
                    style={styles.modalCancelBtn}
                    onPress={() => setShowDetailModal(false)}
                    activeOpacity={0.8}
                    accessibilityLabel="Cancel"
                  >
                    <Text style={styles.modalCancelBtnText}>Cancel</Text>
                  </TouchableOpacity>

                  <TouchableOpacity
                    style={styles.modalPinBtn}
                    onPress={() => {
                      setShowDetailModal(false);
                      setTimeout(() => {
                        setShowPinModal(true);
                      }, 250);
                    }}
                    activeOpacity={0.85}
                    accessibilityLabel="Pin Soil Location"
                  >
                    <MapPinIcon size={16} color="#ffffff" />
                    <Text style={styles.modalPinBtnText}>Pin Soil Location</Text>
                  </TouchableOpacity>
                </View>
              </ScrollView>
            </View>
          </View>
        </Modal>
      ) : null}

      {/* 8. Interactive Pin Soil Location Modal */}
      <PinSoilLocationModal
        visible={showPinModal}
        onClose={() => setShowPinModal(false)}
        specimen={currentSpecimen}
        onSuccess={(data) => {
          setPinnedLocation(data);
          triggerAutoSaveToast('📍 Soil sample pinned & submitted for Admin Approval!');
        }}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#051F20',
  },
  backgroundImage: {
    width: '100%',
    height: '100%',
  },
  torchBeamOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(254, 240, 138, 0.22)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  torchCenterGlow: {
    width: SCREEN_WIDTH * 0.88,
    height: SCREEN_WIDTH * 0.88,
    borderRadius: (SCREEN_WIDTH * 0.88) / 2,
    backgroundColor: 'rgba(255, 255, 255, 0.16)',
  },
  shutterFlash: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: '#ffffff',
  },

  topHeader: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingTop: Platform.OS === 'ios' ? 54 : 44,
    zIndex: 20,
  },
  headerCloseBtn: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: 'rgba(0, 0, 0, 0.45)',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.25)',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 4,
    elevation: 4,
  },
  closeBtnText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '700',
  },
  titleContainer: {
    alignItems: 'center',
  },
  headerTitle: {
    color: '#ffffff',
    fontSize: 18,
    fontWeight: '700',
    letterSpacing: 0.3,
    textShadowColor: 'rgba(0, 0, 0, 0.6)',
    textShadowOffset: { width: 0, height: 1 },
    textShadowRadius: 3,
  },
  torchActiveSub: {
    color: '#facc15',
    fontSize: 10,
    fontWeight: '700',
    marginTop: 1,
    letterSpacing: 0.2,
  },

  scanningHudContainer: {
    ...StyleSheet.absoluteFillObject,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: 'rgba(5, 31, 32, 0.65)',
    borderRadius: 26,
    paddingHorizontal: 16,
  },
  scanningSpinnerRing: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: 'rgba(15, 23, 42, 0.9)',
    borderWidth: 2,
    borderColor: '#22c55e',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 10,
    shadowColor: '#22c55e',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.6,
    shadowRadius: 10,
    elevation: 8,
  },
  scanningHudTitle: {
    color: '#22c55e',
    fontSize: 13,
    fontWeight: '900',
    letterSpacing: 1.5,
    marginBottom: 3,
    textShadowColor: 'rgba(0, 0, 0, 0.8)',
    textShadowOffset: { width: 0, height: 1 },
    textShadowRadius: 3,
  },
  scanningHudSubtitle: {
    color: '#e2e8f0',
    fontSize: 11.5,
    fontWeight: '700',
    letterSpacing: 0.3,
    textAlign: 'center',
    textShadowColor: 'rgba(0, 0, 0, 0.8)',
    textShadowOffset: { width: 0, height: 1 },
    textShadowRadius: 3,
  },

  controlsDock: {
    position: 'absolute',
    left: 0,
    right: 0,
    bottom: Platform.OS === 'ios' ? 190 : 178,
    alignItems: 'center',
    zIndex: 10,
  },
  hintPill: {
    backgroundColor: 'rgba(0, 0, 0, 0.65)',
    paddingHorizontal: 16,
    paddingVertical: 6,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.2)',
    marginBottom: 12,
  },
  hintText: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '600',
  },
  controlButtonsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 32,
    pointerEvents: 'auto',
  },
  actionCircleBtn: {
    width: 52,
    height: 52,
    borderRadius: 26,
    backgroundColor: 'rgba(15, 23, 42, 0.85)',
    borderWidth: 1.5,
    borderColor: 'rgba(255, 255, 255, 0.35)',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.4,
    shadowRadius: 6,
    elevation: 6,
  },
  actionCircleBtnTorchOn: {
    backgroundColor: 'rgba(161, 98, 7, 0.9)',
    borderColor: '#facc15',
    shadowColor: '#facc15',
    shadowOpacity: 0.8,
    shadowRadius: 10,
    elevation: 8,
  },
  actionTorchDot: {
    position: 'absolute',
    top: 4,
    right: 4,
    width: 7,
    height: 7,
    borderRadius: 3.5,
    backgroundColor: '#facc15',
  },

  bottomCardWrapper: {
    position: 'absolute',
    bottom: Platform.OS === 'ios' ? 96 : 84,
    left: 16,
    right: 16,
    zIndex: 25,
  },

  analyzingCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    borderRadius: 18,
    paddingVertical: 14,
    paddingHorizontal: 16,
    borderWidth: 1,
    borderColor: 'rgba(34, 197, 94, 0.3)',
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.25,
    shadowRadius: 12,
    elevation: 8,
  },
  analyzingSpinnerBox: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: '#f0fdf4',
    borderWidth: 1,
    borderColor: '#dcfce7',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 14,
  },
  analyzingTextBox: {
    flex: 1,
  },
  analyzingCardTitle: {
    fontSize: 14,
    fontWeight: '800',
    color: '#0f172a',
    marginBottom: 2,
  },
  analyzingCardDesc: {
    fontSize: 11.5,
    fontWeight: '600',
    color: '#64748b',
  },

  neutralPromptCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    alignSelf: 'center',
    backgroundColor: 'rgba(15, 23, 42, 0.82)',
    borderRadius: 9999,
    paddingVertical: 11,
    paddingHorizontal: 20,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.2)',
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 6,
  },
  neutralPulseDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#22c55e',
    marginRight: 10,
    shadowColor: '#22c55e',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.9,
    shadowRadius: 4,
    elevation: 4,
  },
  neutralPromptText: {
    fontSize: 12.5,
    fontWeight: '600',
    color: '#ffffff',
    letterSpacing: 0.2,
  },

  recentCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    borderRadius: 18,
    padding: 12,
    borderWidth: 1,
    borderColor: 'rgba(218, 241, 222, 0.85)',
    shadowColor: '#15803d',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 14,
    elevation: 3,
  },
  recentThumbBox: {
    width: 64,
    height: 64,
    borderRadius: 14,
    marginRight: 12,
    overflow: 'hidden',
    position: 'relative',
    borderWidth: 1,
    borderColor: 'rgba(0, 0, 0, 0.08)',
    backgroundColor: '#e2e8f0',
  },
  recentSoilThumb: {
    width: '100%',
    height: '100%',
  },
  recentDetails: {
    flex: 1,
    paddingRight: 8,
  },
  recentTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 2,
  },
  recentName: {
    fontSize: 14,
    fontWeight: '800',
    color: '#0f172a',
    flex: 1,
    marginRight: 4,
  },
  scanTimeText: {
    fontSize: 10.5,
    fontWeight: '600',
    color: '#94a3b8',
  },
  recentDesc: {
    fontSize: 11,
    color: '#64748b',
    lineHeight: 15,
    marginBottom: 4,
  },
  recentCodeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginTop: 2,
  },
  recentCode: {
    fontSize: 12.5,
    fontWeight: '800',
    color: '#22c55e',
  },
  identifiedBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#22c55e',
    paddingHorizontal: 7,
    paddingVertical: 2,
    borderRadius: 6,
  },
  identifiedBadgeIcon: {
    color: '#ffffff',
    fontSize: 10,
    fontWeight: '900',
    marginRight: 3,
  },
  identifiedBadgeText: {
    color: '#ffffff',
    fontSize: 10,
    fontWeight: '700',
  },
  syncedAdminBadge: {
    backgroundColor: '#f0fdf4',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 5,
    borderWidth: 1,
    borderColor: '#bbf7d0',
  },
  syncedAdminBadgeText: {
    color: '#16a34a',
    fontSize: 9.5,
    fontWeight: '700',
  },
  queuedBadge: {
    backgroundColor: '#fefce8',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 5,
    borderWidth: 1,
    borderColor: '#fef08a',
  },
  queuedBadgeText: {
    color: '#ca8a04',
    fontSize: 9.5,
    fontWeight: '700',
  },
  recentViewPill: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 8,
    backgroundColor: '#f0fdf4',
    borderWidth: 1,
    borderColor: '#bbf7d0',
    alignItems: 'center',
    justifyContent: 'center',
  },
  recentViewPillText: {
    fontSize: 11,
    fontWeight: '800',
    color: '#166534',
  },
  recentActionsCol: {
    flexDirection: 'column',
    alignItems: 'flex-end',
    justifyContent: 'center',
    gap: 6,
  },
  recentRetakePill: {
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: 8,
    backgroundColor: '#f1f5f9',
    borderWidth: 1,
    borderColor: '#cbd5e1',
    alignItems: 'center',
    justifyContent: 'center',
  },
  recentRetakePillText: {
    fontSize: 10.5,
    fontWeight: '700',
    color: '#334155',
  },
  rejectionActionsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 10,
    gap: 10,
  },
  rejectionRetakeBtn: {
    paddingHorizontal: 14,
    paddingVertical: 7,
    borderRadius: 8,
    backgroundColor: '#16a34a',
    alignItems: 'center',
    justifyContent: 'center',
  },
  rejectionRetakeBtnText: {
    fontSize: 12,
    fontWeight: '700',
    color: '#ffffff',
  },
  rejectionDismissBtn: {
    paddingHorizontal: 12,
    paddingVertical: 7,
    borderRadius: 8,
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  rejectionDismissBtnText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#94a3b8',
  },
  actionCircleBtnRetake: {
    backgroundColor: '#15803d',
    borderColor: '#22c55e',
  },
  actionCircleRetakeIcon: {
    fontSize: 18,
    color: '#ffffff',
  },

  soilModalBackdrop: {
    flex: 1,
    backgroundColor: 'rgba(3, 18, 14, 0.82)',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 16,
  },
  soilModalCard: {
    backgroundColor: '#0a2620',
    borderWidth: 1.5,
    borderColor: 'rgba(34, 197, 94, 0.22)',
    borderRadius: 24,
    width: '100%',
    maxHeight: SCREEN_HEIGHT * 0.88,
    paddingHorizontal: 16,
    paddingTop: 16,
    paddingBottom: 16,
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 16 },
    shadowOpacity: 0.5,
    shadowRadius: 28,
    elevation: 20,
    zIndex: 10,
  },
  soilModalHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingBottom: 12,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255, 255, 255, 0.08)',
  },
  soilModalSubHeader: {
    fontSize: 10,
    fontWeight: '800',
    color: '#22c55e',
    letterSpacing: 0.8,
    textTransform: 'uppercase',
    marginBottom: 2,
  },
  soilModalTitle: {
    fontSize: 18,
    fontWeight: '800',
    color: '#ffffff',
    letterSpacing: -0.2,
  },
  soilModalCloseBtn: {
    width: 32,
    height: 32,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 16,
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.15)',
  },
  soilModalCloseIcon: {
    fontSize: 13,
    color: '#ffffff',
    fontWeight: '700',
  },
  soilModalScrollContent: {
    paddingTop: 12,
    paddingBottom: 8,
  },
  // Dual-preview Soil Banner
  soilBannerDualPreview: {
    width: '100%',
    borderRadius: 16,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    paddingHorizontal: 14,
    paddingVertical: 12,
    marginBottom: 12,
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.35,
    shadowRadius: 10,
    elevation: 5,
  },
  soilBannerAvatarContainer: {
    width: 58,
    height: 58,
    borderRadius: 12,
    overflow: 'hidden',
    backgroundColor: 'rgba(0, 0, 0, 0.25)',
    borderWidth: 1.5,
    borderColor: 'rgba(255, 255, 255, 0.25)',
  },
  soilBannerAvatarImg: {
    width: '100%',
    height: '100%',
  },
  soilBannerAvatarFallback: {
    width: '100%',
    height: '100%',
    alignItems: 'center',
    justifyContent: 'center',
  },
  soilBannerCodeBlock: {
    flex: 1,
    minWidth: 0,
  },
  soilBannerMunsellCode: {
    fontSize: 22,
    fontWeight: '900',
    color: '#ffffff',
    letterSpacing: -0.2,
    lineHeight: 26,
  },
  soilBannerSubLabel: {
    fontSize: 12,
    fontWeight: '700',
    color: 'rgba(255, 255, 255, 0.95)',
    marginTop: 2,
  },
  soilBannerHexPill: {
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: 16,
    backgroundColor: 'rgba(0, 0, 0, 0.55)',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.25)',
  },
  soilBannerHexText: {
    fontSize: 10.5,
    fontWeight: '800',
    color: '#ffffff',
    letterSpacing: 0.5,
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
  },
  // 3-Grid Metric Cards
  soilMetricsRow: {
    flexDirection: 'row',
    gap: 8,
    marginBottom: 8,
  },
  soilModalGridCard: {
    flex: 1,
    backgroundColor: '#0c2e27',
    borderWidth: 1.5,
    borderColor: '#18493f',
    borderRadius: 12,
    paddingHorizontal: 10,
    paddingVertical: 9,
    justifyContent: 'center',
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.2,
    shadowRadius: 3,
    elevation: 1,
  },
  soilNearMatchCard: {
    borderColor: 'rgba(34, 197, 94, 0.35)',
    backgroundColor: '#0c2e27',
  },
  soilModalLabel: {
    fontSize: 9.5,
    fontWeight: '800',
    color: '#8eb69b',
    textTransform: 'uppercase',
    letterSpacing: 0.4,
    marginBottom: 3,
  },
  soilModalValue: {
    fontSize: 12.5,
    fontWeight: '800',
    color: '#ffffff',
    lineHeight: 16,
  },
  soilNearMatchValue: {
    fontSize: 13.5,
    fontWeight: '900',
    color: '#22c55e',
  },
  // Full-width cards
  soilModalFullCard: {
    backgroundColor: '#0c2e27',
    borderWidth: 1.5,
    borderColor: '#18493f',
    borderRadius: 12,
    paddingHorizontal: 12,
    paddingVertical: 10,
    marginBottom: 8,
  },
  soilModalDescText: {
    fontSize: 12,
    color: '#e2e8f0',
    lineHeight: 18,
    fontWeight: '600',
  },
  soilModalFooterRow: {
    flexDirection: 'row',
    gap: 10,
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: 'rgba(255, 255, 255, 0.08)',
  },
  modalCancelBtn: {
    flex: 1,
    height: 46,
    borderRadius: 14,
    backgroundColor: '#0c2e27',
    borderWidth: 1.5,
    borderColor: '#1e5347',
    alignItems: 'center',
    justifyContent: 'center',
  },
  modalCancelBtnText: {
    fontSize: 14,
    fontWeight: '800',
    color: '#ffffff',
  },
  modalPinBtn: {
    flex: 2,
    height: 46,
    borderRadius: 14,
    backgroundColor: '#22c55e',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    shadowColor: '#22c55e',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.35,
    shadowRadius: 8,
    elevation: 4,
  },
  modalPinBtnText: {
    fontSize: 14,
    fontWeight: '800',
    color: '#ffffff',
  },

  // State B (Camera preview bottom card) styles
  stateBResultCard: {
    backgroundColor: '#0B2B26',
    borderWidth: 1,
    borderColor: 'rgba(142, 182, 155, 0.25)',
    borderRadius: 20,
    padding: 14,
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 12,
    elevation: 8,
  },
  stateBHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingBottom: 8,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(142, 182, 155, 0.2)',
    marginBottom: 10,
  },
  stateBHeaderTitle: {
    fontSize: 11.5,
    fontWeight: '800',
    color: '#16a34a',
    letterSpacing: 0.8,
    textTransform: 'uppercase',
  },
  stateBSwatchBanner: {
    borderRadius: 12,
    paddingHorizontal: 12,
    paddingVertical: 10,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    marginBottom: 12,
  },
  stateBThumbBox: {
    width: 44,
    height: 44,
    borderRadius: 8,
    overflow: 'hidden',
    backgroundColor: 'rgba(0, 0, 0, 0.2)',
  },
  stateBSoilThumb: {
    width: '100%',
    height: '100%',
  },
  stateBDetailsCol: {
    flex: 1,
    minWidth: 0,
  },
  stateBMunsellCode: {
    fontSize: 16,
    fontWeight: '900',
    color: '#ffffff',
  },
  stateBColorName: {
    fontSize: 12,
    fontWeight: '700',
    color: 'rgba(255, 255, 255, 0.9)',
  },
  stateBHexPill: {
    backgroundColor: 'rgba(0, 0, 0, 0.55)',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.2)',
  },
  stateBHexText: {
    fontSize: 10.5,
    fontWeight: '800',
    color: '#ffffff',
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
  },
  stateBActionsRow: {
    flexDirection: 'row',
    gap: 8,
  },
  stateBViewBtn: {
    flex: 1.6,
    height: 38,
    borderRadius: 9999,
    backgroundColor: '#16a34a',
    alignItems: 'center',
    justifyContent: 'center',
  },
  stateBViewBtnText: {
    fontSize: 12,
    fontWeight: '800',
    color: '#ffffff',
  },
  stateBRetakeBtn: {
    flex: 1,
    height: 38,
    borderRadius: 9999,
    backgroundColor: 'rgba(255, 255, 255, 0.08)',
    borderWidth: 1,
    borderColor: 'rgba(142, 182, 155, 0.25)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  stateBRetakeBtnText: {
    fontSize: 12,
    fontWeight: '700',
    color: '#DAF1DE',
  },

  autoSaveToast: {
    position: 'absolute',
    top: Platform.OS === 'ios' ? 104 : 94,
    alignSelf: 'center',
    backgroundColor: 'rgba(15, 23, 42, 0.92)',
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: 20,
    flexDirection: 'row',
    alignItems: 'center',
    zIndex: 99,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 8,
  },
  autoSaveToastDot: {
    width: 7,
    height: 7,
    borderRadius: 3.5,
    backgroundColor: '#eab308',
    marginRight: 8,
  },
  autoSaveToastText: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '700',
  },

  /* 6-Stage Pipeline Loading HUD Styles (matching website .scan-loading-*) */
  scanLoadingStagePill: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(34, 197, 94, 0.18)',
    borderWidth: 1,
    borderColor: 'rgba(34, 197, 94, 0.45)',
    paddingHorizontal: 12,
    paddingVertical: 5,
    borderRadius: 9999,
    marginBottom: 8,
  },
  scanLoadingStageDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#22c55e',
    marginRight: 6,
    shadowColor: '#22c55e',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.9,
    shadowRadius: 5,
    elevation: 4,
  },
  scanLoadingStageDotSmall: {
    width: 5,
    height: 5,
    borderRadius: 2.5,
    backgroundColor: '#22c55e',
    marginRight: 5,
  },
  scanLoadingStagePillText: {
    color: '#22c55e',
    fontSize: 10.5,
    fontWeight: '800',
    letterSpacing: 0.8,
    textTransform: 'uppercase',
  },
  scanLoadingSpinnerWrap: {
    width: 64,
    height: 64,
    alignItems: 'center',
    justifyContent: 'center',
    marginVertical: 4,
    position: 'relative',
  },
  scanLoadingSpinnerRing: {
    width: 64,
    height: 64,
  },
  scanLoadingBadgeCenter: {
    position: 'absolute',
    alignItems: 'center',
    justifyContent: 'center',
  },
  scanLoadingBadgePercent: {
    color: '#DAF1DE',
    fontSize: 12,
    fontWeight: '900',
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
    letterSpacing: -0.5,
  },
  scanLoadingProgressTrack: {
    width: 180,
    height: 5,
    backgroundColor: 'rgba(142, 182, 155, 0.22)',
    borderRadius: 9999,
    overflow: 'hidden',
    marginTop: 10,
    borderWidth: 0.5,
    borderColor: 'rgba(142, 182, 155, 0.15)',
  },
  scanLoadingProgressFill: {
    height: '100%',
    backgroundColor: '#22c55e',
    borderRadius: 9999,
    shadowColor: '#22c55e',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.9,
    shadowRadius: 6,
    elevation: 3,
  },

  /* Bottom Analyzing Card Stage Styles */
  analyzingSpinnerPct: {
    color: '#16a34a',
    fontSize: 11.5,
    fontWeight: '900',
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
  },
  analyzingStageBadgeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 2,
  },
  analyzingCardStageBadge: {
    fontSize: 10,
    fontWeight: '800',
    color: '#16a34a',
    letterSpacing: 0.5,
    textTransform: 'uppercase',
  },
  bottomCardProgressTrack: {
    width: '100%',
    height: 4,
    backgroundColor: 'rgba(34, 197, 94, 0.15)',
    borderRadius: 9999,
    overflow: 'hidden',
    marginTop: 6,
  },
  bottomCardProgressFill: {
    height: '100%',
    backgroundColor: '#22c55e',
    borderRadius: 9999,
  },

  /* 6-Stage ML Soil Determination Pipeline Modal Card (matching website .pipeline-overview-card) */
  pipelineOverviewCardModal: {
    backgroundColor: '#0c1f1b',
    borderRadius: 16,
    borderWidth: 1,
    borderColor: 'rgba(142, 182, 155, 0.25)',
    padding: 14,
    marginBottom: 12,
  },
  pipelineOverviewTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 10,
    paddingBottom: 8,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(142, 182, 155, 0.15)',
  },
  pipelineTitleIconBox: {
    width: 20,
    height: 20,
    borderRadius: 10,
    backgroundColor: 'rgba(34, 197, 94, 0.2)',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 8,
  },
  pipelineTitleCheckIcon: {
    color: '#22c55e',
    fontSize: 11,
    fontWeight: '900',
  },
  pipelineOverviewTitleText: {
    color: '#22c55e',
    fontSize: 11.5,
    fontWeight: '800',
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  pipelineStagesListModal: {
    gap: 8,
  },
  pipelineStageItemModal: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(22, 56, 50, 0.55)',
    borderRadius: 10,
    paddingHorizontal: 10,
    paddingVertical: 8,
    borderWidth: 1,
    borderColor: 'rgba(142, 182, 155, 0.12)',
  },
  pipelineStageBadgeBox: {
    backgroundColor: 'rgba(34, 197, 94, 0.18)',
    borderWidth: 1,
    borderColor: 'rgba(34, 197, 94, 0.35)',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 6,
    marginRight: 10,
  },
  pipelineStageBadgeText: {
    color: '#22c55e',
    fontSize: 9.5,
    fontWeight: '900',
    letterSpacing: 0.4,
  },
  pipelineStageInfoCol: {
    flex: 1,
    paddingRight: 6,
  },
  pipelineStageNameText: {
    color: '#DAF1DE',
    fontSize: 11.5,
    fontWeight: '700',
    marginBottom: 1,
  },
  pipelineStageSubText: {
    color: '#8EB69B',
    fontSize: 9.5,
    fontWeight: '500',
  },
  pipelineStageStatusBox: {
    backgroundColor: 'rgba(34, 197, 94, 0.15)',
    paddingHorizontal: 6,
    paddingVertical: 3,
    borderRadius: 6,
  },
  pipelineStageStatusText: {
    color: '#22c55e',
    fontSize: 9,
    fontWeight: '800',
    letterSpacing: 0.3,
  },
  cameraFallbackContainer: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: '#051F20',
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 32,
  },
  cameraGridReticle: {
    width: 80,
    height: 80,
    borderRadius: 40,
    borderWidth: 2,
    borderColor: 'rgba(34, 197, 94, 0.4)',
    borderStyle: 'dashed',
    marginBottom: 16,
  },
  cameraFallbackTitle: {
    color: '#DAF1DE',
    fontSize: 16,
    fontWeight: '700',
    textAlign: 'center',
    marginBottom: 6,
  },
  cameraFallbackSub: {
    color: '#8EB69B',
    fontSize: 12.5,
    textAlign: 'center',
    lineHeight: 18,
    marginBottom: 18,
  },
  grantPermissionBtn: {
    backgroundColor: '#22c55e',
    paddingHorizontal: 18,
    paddingVertical: 10,
    borderRadius: 20,
  },
  grantPermissionBtnText: {
    color: '#051F20',
    fontSize: 13,
    fontWeight: '800',
  },
});
