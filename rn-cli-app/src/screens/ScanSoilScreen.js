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
        setSyncStatus(null); // Do not show fake synced badge on demo
        Animated.spring(resultCardAnim, { toValue: 1, friction: 6, tension: 70, useNativeDriver: true }).start();
      }, 2350);
      return;
    }

    const gps = getCurrentGps();
    const scanId = newScanId();

    const runOfflineAnalysisFlow = async () => {
      // Advance through ML determination stages with smooth animation timing
      setTimeout(() => advanceStage(1), 320);
      setTimeout(() => advanceStage(2), 700);
      setTimeout(() => advanceStage(3), 1100);
      setTimeout(() => advanceStage(4), 1500);
      setTimeout(() => advanceStage(5), 1900);

      // Queue for background server synchronization once connectivity is restored
      try {
        await queueAsyncScan({ scanId, imageUri: photo.uri, ...gps });
      } catch (e) {
        console.warn('[ScanSoilScreen] queue failed:', e?.message);
      }

      // Perform local spectral extraction & Munsell classification
      const offlineSpecimen = await analyzeSoilOffline({
        imageUri: photo.uri,
        imageBase64: photo.base64,
      });

      // Complete pipeline & show results
      setTimeout(async () => {
        advanceStage(6);
        await finishProgressEngine();
        setIsScanning(false);
        setScanResult(offlineSpecimen);
        setHasScanned(true);
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
      }, 2350);
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

  const handleManualScan = async () => {
    if (isScanning) return;
    const photo = await capturePhoto();
    if (photo && photo.base64) {
      setUserCustomImage(photo.uri);
      runScan(photo);
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
        setUserCustomImage(a.uri);
        runScan({ uri: a.uri, base64: a.base64 });
      }
    } catch (err) {
      Alert.alert('Gallery', 'Unable to open gallery');
    }
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
      handleManualScan();
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
            isActive={!showDetailModal && !showPinModal}
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

      {/* 3. Top Header: "Scanning" & Close (X) Button */}
      <View style={styles.topHeader}>
        <View style={{ width: 44 }} />
        <View style={styles.titleContainer}>
          <Text style={styles.headerTitle}>Scanning</Text>
          {isTorchOn && <Text style={styles.torchActiveSub}>Torch Active</Text>}
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

          {/* Flashlight / Torch Button */}
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
        /* State B: Scan Result Card */
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
            style={styles.recentCard}
            activeOpacity={0.88}
            onPress={() => setShowDetailModal(true)}
          >
            {/* Swatch / Image on the left */}
            <View style={styles.recentThumbBox}>
              {currentSpecimen.thumbnail ? (
                <Image
                  source={currentSpecimen.thumbnail}
                  style={styles.recentSoilThumb}
                  resizeMode="cover"
                />
              ) : (
                <View
                  style={[
                    styles.recentSoilThumb,
                    { backgroundColor: currentSpecimen.hexColor || '#8F694E' },
                  ]}
                />
              )}
            </View>

            {/* Details Column */}
            <View style={styles.recentDetails}>
              <View style={styles.recentTitleRow}>
                <Text style={styles.recentName} numberOfLines={1}>
                  {currentSpecimen.name}
                </Text>
                <Text style={styles.scanTimeText}>Live Scan</Text>
              </View>

              <Text style={styles.recentDesc} numberOfLines={1}>
                {currentSpecimen.soilCategory || currentSpecimen.soilType || currentSpecimen.scientificName}
              </Text>

              <View style={styles.recentCodeRow}>
                <Text style={styles.recentCode}>{currentSpecimen.munsellCode}</Text>
                <View style={styles.identifiedBadge}>
                  <Text style={styles.identifiedBadgeIcon}>✓</Text>
                  <Text style={styles.identifiedBadgeText}>Identified</Text>
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
            </View>

            {/* Action Button: View Details */}
            <TouchableOpacity
              style={styles.recentViewPill}
              onPress={() => setShowDetailModal(true)}
              activeOpacity={0.8}
              accessibilityLabel="View Details"
            >
              <Text style={styles.recentViewPillText}>View</Text>
            </TouchableOpacity>
          </TouchableOpacity>
        </Animated.View>
      ) : rejectMsg ? (
        /* State C: Rejection Card */
        <View style={styles.bottomCardWrapper}>
          <View style={styles.analyzingCard}>
            <View style={styles.analyzingTextBox}>
              <Text style={styles.analyzingCardTitle}>No soil detected</Text>
              <Text style={styles.analyzingCardDesc}>{rejectMsg}</Text>
              <TouchableOpacity onPress={() => setRejectMsg(null)}>
                <Text style={[styles.analyzingCardDesc, { color: '#16a34a', fontWeight: '700', marginTop: 4 }]}>
                  Dismiss
                </Text>
              </TouchableOpacity>
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

      {/* 7. Soil Record Detail Modal */}
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
              <View style={styles.soilModalHeaderRow}>
                <Text style={styles.soilModalTitle}>Soil Record Details</Text>
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
                contentContainerStyle={styles.soilModalScrollContent}
                showsVerticalScrollIndicator={false}
                keyboardShouldPersistTaps="always"
              >
                {/* Dual-Preview Soil Banner */}
                <View style={styles.soilBannerWrapper}>
                  <View
                    style={[
                      styles.soilSwatchBlock,
                      { backgroundColor: currentSpecimen.hexColor || '#66503C' },
                    ]}
                  >
                    <Text style={styles.soilBannerCode}>
                      {currentSpecimen.fullCode || currentSpecimen.munsellCode}
                    </Text>
                    <View style={styles.soilBannerHexPill}>
                      <Text style={styles.soilBannerHexText}>
                        {(currentSpecimen.hexColor || '#66503C').toUpperCase()}
                      </Text>
                    </View>
                  </View>

                  <View style={styles.soilCubeThumbnailBox}>
                    {currentSpecimen.thumbnail ? (
                      <Image
                        source={currentSpecimen.thumbnail}
                        style={styles.soilCubeThumbnailImage}
                        resizeMode="cover"
                      />
                    ) : (
                      <View
                        style={[
                          styles.soilCubeThumbnailImage,
                          { backgroundColor: currentSpecimen.hexColor || '#66503C' },
                        ]}
                      />
                    )}
                  </View>
                </View>

                {/* 3 Metric Cards Row 1 */}
                <View style={styles.metricCardsRow}>
                  <View style={styles.metricGridCard}>
                    <Text style={styles.metricGridLabel}>Full Code</Text>
                    <Text style={styles.metricGridValue}>
                      {currentSpecimen.fullCode || currentSpecimen.munsellCode}
                    </Text>
                  </View>

                  <View style={styles.metricGridCard}>
                    <Text style={styles.metricGridLabel}>Hue Code</Text>
                    <Text style={styles.metricGridValue}>
                      {currentSpecimen.hueCode || '10R'}
                    </Text>
                  </View>

                  <View style={styles.metricGridCard}>
                    <Text style={styles.metricGridLabel}>Scan Count</Text>
                    <Text style={styles.metricGridValue}>
                      {currentSpecimen.scanCount || '1'}
                    </Text>
                  </View>
                </View>

                {/* 3 Metric Cards Row 2 */}
                <View style={styles.metricCardsRow}>
                  <View style={styles.metricGridCard}>
                    <Text style={styles.metricGridLabel}>Soil Organic Index</Text>
                    <Text style={styles.metricGridValueSmall}>
                      {currentSpecimen.organicIndex || 'Moderate'}
                    </Text>
                  </View>

                  <View style={styles.metricGridCard}>
                    <Text style={styles.metricGridLabel}>Soil Type</Text>
                    <Text style={styles.metricGridValueSmall}>
                      {currentSpecimen.soilType || 'Determined Soil'}
                    </Text>
                  </View>

                  <View style={styles.metricGridCard}>
                    <Text style={styles.metricGridLabel}>Near-Match Rating</Text>
                    <Text style={styles.metricGridValue}>
                      {currentSpecimen.nearMatchRating || '95%'}
                    </Text>
                  </View>
                </View>

                {/* Created Date Card */}
                <View style={styles.fullWidthMetricCard}>
                  <Text style={styles.metricGridLabel}>CREATED</Text>
                  <Text style={styles.metricGridValue}>
                    {currentSpecimen.createdDate || new Date().toLocaleDateString()}
                  </Text>
                </View>

                {/* Description Card */}
                <View style={styles.fullWidthMetricCard}>
                  <Text style={styles.metricGridLabel}>DESCRIPTION</Text>
                  <Text style={styles.descriptionTextValue}>
                    {currentSpecimen.description || `${currentSpecimen.munsellCode} - ${currentSpecimen.name}`}
                  </Text>
                </View>

                {/* 6-Stage ML Soil Determination Pipeline Card */}
                <View style={styles.pipelineOverviewCardModal}>
                  <View style={styles.pipelineOverviewTitleRow}>
                    <View style={styles.pipelineTitleIconBox}>
                      <Text style={styles.pipelineTitleCheckIcon}>✓</Text>
                    </View>
                    <Text style={styles.pipelineOverviewTitleText}>
                      6-Stage ML Soil Determination Pipeline
                    </Text>
                  </View>
                  <View style={styles.pipelineStagesListModal}>
                    {[1, 2, 3, 4, 5, 6].map((stg) => {
                      const meta = STAGE_META[stg];
                      return (
                        <View key={stg} style={styles.pipelineStageItemModal}>
                          <View style={styles.pipelineStageBadgeBox}>
                            <Text style={styles.pipelineStageBadgeText}>{meta.badge}</Text>
                          </View>
                          <View style={styles.pipelineStageInfoCol}>
                            <Text style={styles.pipelineStageNameText}>{meta.name}</Text>
                            <Text style={styles.pipelineStageSubText}>{meta.sub}</Text>
                          </View>
                          <View style={styles.pipelineStageStatusBox}>
                            <Text style={styles.pipelineStageStatusText}>✓ PASSED</Text>
                          </View>
                        </View>
                      );
                    })}
                  </View>
                </View>

                {/* Modal Buttons: Pin Soil Location */}
                <View style={styles.soilModalButtonsCol}>
                  <TouchableOpacity
                    style={styles.pinSoilLocationBtn}
                    onPress={() => {
                      setShowDetailModal(false);
                      setTimeout(() => {
                        setShowPinModal(true);
                      }, 250);
                    }}
                    activeOpacity={0.85}
                  >
                    <MapPinIcon size={18} color="#ffffff" />
                    <Text style={styles.pinSoilLocationBtnText}>Pin Soil Location</Text>
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

  soilModalBackdrop: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.55)',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 16,
  },
  soilModalCard: {
    backgroundColor: '#ffffff',
    borderRadius: 24,
    width: '100%',
    maxHeight: SCREEN_HEIGHT * 0.88,
    paddingHorizontal: 20,
    paddingTop: 18,
    paddingBottom: 20,
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.28,
    shadowRadius: 24,
    elevation: 16,
    zIndex: 10,
  },
  soilModalHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingBottom: 14,
  },
  soilModalTitle: {
    fontSize: 18,
    fontWeight: '800',
    color: '#0f172a',
    letterSpacing: -0.2,
  },
  soilModalCloseBtn: {
    width: 32,
    height: 32,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 16,
    backgroundColor: '#f8fafc',
  },
  soilModalCloseIcon: {
    fontSize: 14,
    color: '#64748b',
    fontWeight: '700',
  },
  soilModalScrollContent: {
    paddingBottom: 16,
  },

  soilBannerWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 16,
    gap: 12,
  },
  soilSwatchBlock: {
    flex: 1,
    height: 100,
    borderRadius: 18,
    padding: 14,
    justifyContent: 'space-between',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 8,
    elevation: 4,
  },
  soilBannerCode: {
    color: '#ffffff',
    fontSize: 18,
    fontWeight: '900',
    letterSpacing: 0.5,
    textShadowColor: 'rgba(0, 0, 0, 0.4)',
    textShadowOffset: { width: 0, height: 1 },
    textShadowRadius: 2,
  },
  soilBannerHexPill: {
    alignSelf: 'flex-start',
    backgroundColor: 'rgba(255, 255, 255, 0.25)',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  soilBannerHexText: {
    color: '#ffffff',
    fontSize: 11,
    fontWeight: '800',
  },
  soilCubeThumbnailBox: {
    width: 100,
    height: 100,
    borderRadius: 18,
    overflow: 'hidden',
    backgroundColor: '#f1f5f9',
    borderWidth: 1,
    borderColor: 'rgba(0, 0, 0, 0.08)',
  },
  soilCubeThumbnailImage: {
    width: '100%',
    height: '100%',
  },

  metricCardsRow: {
    flexDirection: 'row',
    gap: 8,
    marginBottom: 8,
  },
  metricGridCard: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    padding: 10,
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  metricGridLabel: {
    fontSize: 10,
    fontWeight: '700',
    color: '#64748b',
    textTransform: 'uppercase',
    marginBottom: 4,
    letterSpacing: 0.3,
  },
  metricGridValue: {
    fontSize: 13,
    fontWeight: '800',
    color: '#0f172a',
  },
  metricGridValueSmall: {
    fontSize: 11,
    fontWeight: '700',
    color: '#0f172a',
  },

  fullWidthMetricCard: {
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    padding: 12,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    marginBottom: 8,
  },
  descriptionTextValue: {
    fontSize: 12,
    color: '#334155',
    lineHeight: 18,
    fontWeight: '500',
  },

  soilModalButtonsCol: {
    marginTop: 8,
  },
  pinSoilLocationBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#16a34a',
    borderRadius: 14,
    paddingVertical: 14,
    shadowColor: '#16a34a',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
    elevation: 4,
  },
  pinSoilLocationBtnText: {
    color: '#ffffff',
    fontSize: 14,
    fontWeight: '800',
    letterSpacing: 0.3,
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
