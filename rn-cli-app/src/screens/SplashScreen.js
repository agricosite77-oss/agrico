/**
 * SplashScreen.js
 *
 * Exact 1:1 match of the AgriCo Website Loading Screen (resources/views/splash.blade.php & public/preview-mobile-splash.html).
 *
 * Storyboard & Timeline:
 * - 0.0s – 1.5s: Initial Logo Reveal on light background (#DAF1DE -> #ffffff) with 3 concentric geometric rings
 * - 1.5s – 4.0s: Curved Pastel Wave (#9be8b3 -> #c3f4cf -> #dcfcb0) rises smoothly from bottom with organic curve
 * - 4.0s – 8.0s: Screen filled with vibrant pastel green gradient; "AgriCo" wordmark reveals beside logo
 *                ("Agri" in #16a34a, "Co" in #22c55e). Full brand lockup holds centered.
 * - 8.0s – 9.5s: Wave exits UPWARD off the top edge (translateY: 0 -> -130vh), revealing the base
 *                concentric rings again from below.
 * - 9.5s – 10.5s: Final brand hold on clean light background.
 * - 10.5s – 11.0s: Smooth dissolve into Log In screen.
 */

import React, { useEffect, useRef, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  StatusBar,
  Image,
  Animated,
  Easing,
  Dimensions,
  Platform,
} from 'react-native';
import Svg, { Circle, Path, Defs, LinearGradient, Stop } from 'react-native-svg';
import { syncSoilCalibrationFromWebsite } from '../services/soilCalibrationService';

const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window');

// Wave dimensions matching splash.blade.php (.wave { top: -25vh; height: 150vh; })
const WAVE_HEIGHT = Math.round(SCREEN_HEIGHT * 1.5);
const WAVE_BASE_TOP = Math.round(-SCREEN_HEIGHT * 0.25);

// Wave curve height / depth (gentle organic curve matching website proportion)
const CURVE_DEPTH = 32;
const wavePath = `M0 20 C${SCREEN_WIDTH * 0.21} 20 ${SCREEN_WIDTH * 0.31} ${20 + CURVE_DEPTH} ${SCREEN_WIDTH * 0.5} ${20 + CURVE_DEPTH} C${SCREEN_WIDTH * 0.69} ${20 + CURVE_DEPTH} ${SCREEN_WIDTH * 0.79} 20 ${SCREEN_WIDTH} 20 L${SCREEN_WIDTH} ${WAVE_HEIGHT - 20 - CURVE_DEPTH} C${SCREEN_WIDTH * 0.79} ${WAVE_HEIGHT - 20 - CURVE_DEPTH} ${SCREEN_WIDTH * 0.69} ${WAVE_HEIGHT - 20} ${SCREEN_WIDTH * 0.5} ${WAVE_HEIGHT - 20} C${SCREEN_WIDTH * 0.31} ${WAVE_HEIGHT - 20} ${SCREEN_WIDTH * 0.21} ${WAVE_HEIGHT - 20 - CURVE_DEPTH} 0 ${WAVE_HEIGHT - 20 - CURVE_DEPTH} Z`;

// Concentric ring radii matching preview-mobile-splash.html (260px, 360px, 460px diameters)
const RADIUS_INNER = Math.round(SCREEN_WIDTH * 0.33); // ~130px
const RADIUS_MID = Math.round(SCREEN_WIDTH * 0.46);   // ~180px
const RADIUS_OUTER = Math.round(SCREEN_WIDTH * 0.59); // ~230px

// Exact 3-stop vertical gradient matching website:
// #DAF1DE (0%) -> #f6faf7 (45%) -> #ffffff (100%)
function PureGradient() {
  const stops = [
    '#DAF1DE', '#ddf2e1', '#e2f4e6', '#e7f6eb', '#ecf7f0',
    '#f1f9f4', '#f6faf7', '#f8fbf8', '#f9fcf9', '#fafcfa',
    '#fbfdfc', '#fcfefd', '#fdfefd', '#fefffe', '#ffffff',
  ];

  return (
    <View style={StyleSheet.absoluteFillObject} pointerEvents="none">
      {stops.map((c, i) => (
        <View
          key={i}
          style={{
            flex: i <= 6 ? 45 / 7 : 55 / 8,
            backgroundColor: c,
          }}
        />
      ))}
    </View>
  );
}

export default function SplashScreen({ onFinish }) {
  // Master progress value running from 0 to 11000 ms
  const progress = useRef(new Animated.Value(0)).current;
  const onFinishRef = useRef(onFinish);
  const [syncStatus, setSyncStatus] = useState('Initializing AgriCo Soil Engine…');
  const [syncPercent, setSyncPercent] = useState(0.06);
  const syncProgressAnim = useRef(new Animated.Value(0.06)).current;

  useEffect(() => {
    onFinishRef.current = onFinish;
  }, [onFinish]);

  useEffect(() => {
    // Proactively download real & fake soil images and sync calibration from website
    syncSoilCalibrationFromWebsite((msg, pct) => {
      if (typeof msg === 'string') {
        setSyncStatus(msg);
      }
      if (typeof pct === 'number') {
        setSyncPercent(pct);
        Animated.timing(syncProgressAnim, {
          toValue: Math.min(1.0, Math.max(0.06, pct)),
          duration: 250,
          easing: Easing.out(Easing.quad),
          useNativeDriver: false,
        }).start();
      }
    }).catch(() => {});
  }, [syncProgressAnim]);

  useEffect(() => {
    const animation = Animated.timing(progress, {
      toValue: 11000,
      duration: 11000,
      easing: Easing.linear,
      useNativeDriver: false,
    });

    animation.start(({ finished }) => {
      if (finished && onFinishRef.current) {
        onFinishRef.current();
      }
    });

    return () => {
      animation.stop();
    };
  }, [progress]);

  // 1. Initial Logo Reveal (0ms - 1000ms): fade in & subtle scale from 0.9 to 1.0
  const logoOpacity = progress.interpolate({
    inputRange: [0, 800],
    outputRange: [0, 1],
    extrapolate: 'clamp',
  });

  const logoScale = progress.interpolate({
    inputRange: [0, 1000, 11000],
    outputRange: [0.9, 1.0, 1.0],
    extrapolate: 'clamp',
  });

  // 2. Wave movement matching splash.blade.php:
  // - 0 to 1500ms (0% - 13.6%): translateY = +1.3 * SCREEN_HEIGHT (parked completely below screen)
  // - 1500ms to 4000ms (13.6% - 36.4%): translateY rises to 0 (curves up and covers screen)
  // - 4000ms to 8000ms (36.4% - 72.7%): translateY holds at 0 (full green screen)
  // - 8000ms to 9500ms (72.7% - 86.4%): translateY rises UPWARD to -1.3 * SCREEN_HEIGHT (exits off top)
  // - 9500ms to 11000ms: remains off top
  const waveTranslateY = progress.interpolate({
    inputRange: [0, 1500, 4000, 8000, 9500, 11000],
    outputRange: [
      SCREEN_HEIGHT * 1.3,
      SCREEN_HEIGHT * 1.3,
      0,
      0,
      -SCREEN_HEIGHT * 1.3,
      -SCREEN_HEIGHT * 1.3,
    ],
    extrapolate: 'clamp',
  });

  // 3. Wordmark Reveal (4000ms - 5060ms): expands horizontally & slides up slightly
  const wordWidth = progress.interpolate({
    inputRange: [0, 4000, 5060, 11000],
    outputRange: [0, 0, 160, 160],
    extrapolate: 'clamp',
  });

  const wordMarginLeft = progress.interpolate({
    inputRange: [0, 4000, 5060, 11000],
    outputRange: [0, 0, 10, 10],
    extrapolate: 'clamp',
  });

  const wordOpacity = progress.interpolate({
    inputRange: [0, 4000, 4800, 11000],
    outputRange: [0, 0, 1, 1],
    extrapolate: 'clamp',
  });

  const wordTranslateY = progress.interpolate({
    inputRange: [0, 4000, 5060, 11000],
    outputRange: [10, 10, 0, 0],
    extrapolate: 'clamp',
  });

  // 4. Lower Screen Updating & Sync Loader Opacity (0ms - 800ms fade in, 10200ms - 10800ms fade out)
  const bottomLoaderOpacity = progress.interpolate({
    inputRange: [0, 800, 10200, 10800],
    outputRange: [0, 1, 1, 0],
    extrapolate: 'clamp',
  });

  const progressWidth = syncProgressAnim.interpolate({
    inputRange: [0, 1],
    outputRange: ['0%', '100%'],
    extrapolate: 'clamp',
  });

  // 5. Overall Screen Exit Dissolve (10500ms - 11000ms)
  const screenOpacity = progress.interpolate({
    inputRange: [0, 10500, 11000],
    outputRange: [1, 1, 0],
    extrapolate: 'clamp',
  });

  return (
    <Animated.View style={[styles.container, { opacity: screenOpacity }]}>
      <StatusBar barStyle="dark-content" backgroundColor="#DAF1DE" translucent={false} />

      {/* 1. Base light gradient (#DAF1DE 0% -> #f6faf7 45% -> #ffffff 100%) */}
      <PureGradient />

      {/* 2. Concentric geometric circular rings matching splash.blade.php & preview-mobile-splash.html */}
      <Svg
        width={SCREEN_WIDTH}
        height={SCREEN_HEIGHT}
        style={StyleSheet.absoluteFillObject}
        pointerEvents="none"
      >
        <Circle
          cx={SCREEN_WIDTH / 2}
          cy={SCREEN_HEIGHT / 2}
          r={RADIUS_OUTER}
          stroke="rgba(187, 247, 208, 0.45)"
          strokeWidth={1.5}
          fill="none"
        />
        <Circle
          cx={SCREEN_WIDTH / 2}
          cy={SCREEN_HEIGHT / 2}
          r={RADIUS_MID}
          stroke="rgba(187, 247, 208, 0.65)"
          strokeWidth={1.5}
          fill="none"
        />
        <Circle
          cx={SCREEN_WIDTH / 2}
          cy={SCREEN_HEIGHT / 2}
          r={RADIUS_INNER}
          stroke="rgba(142, 182, 155, 0.75)"
          strokeWidth={1.5}
          fill="none"
        />
      </Svg>

      {/* 3. Curved Pastel Wave (rises from bottom, covers screen, exits off the top) */}
      <Animated.View
        pointerEvents="none"
        style={[
          styles.waveWrapper,
          {
            transform: [{ translateY: waveTranslateY }],
          },
        ]}
      >
        <Image
          source={require('../assets/wave_gradient.png')}
          style={styles.waveImage}
          resizeMode="stretch"
        />
      </Animated.View>

      {/* 4. Centered AgriCo Brand Identity Lockup (Clean - no pills or badges) */}
      <Animated.View style={styles.centerStage} pointerEvents="none">
        <View style={styles.brandLockup}>
          {/* AgriCo Logo */}
          <Animated.View
            style={[
              styles.logoWrapper,
              {
                opacity: logoOpacity,
                transform: [{ scale: logoScale }],
              },
            ]}
          >
            <Image
              source={require('../assets/agrico-logo.png')}
              style={styles.logoImage}
              resizeMode="contain"
            />
          </Animated.View>

          {/* AgriCo Wordmark */}
          <Animated.View
            style={[
              styles.wordWrapper,
              {
                width: wordWidth,
                marginLeft: wordMarginLeft,
                opacity: wordOpacity,
                transform: [{ translateY: wordTranslateY }],
              },
            ]}
          >
            <Text style={styles.wordText}>
              Agri<Text style={styles.wordHighlight}>Co</Text>
            </Text>
          </Animated.View>
        </View>
      </Animated.View>

      {/* 5. Lower Screen Updating & Dataset Sync Loader (Positioned at bottom) */}
      <Animated.View
        style={[
          styles.bottomUpdatingContainer,
          {
            opacity: bottomLoaderOpacity,
          },
        ]}
        pointerEvents="none"
      >
        <View style={styles.updatingCard}>
          <View style={styles.updatingHeaderRow}>
            <View style={styles.updatingBadge}>
              <View style={styles.pulsingDot} />
              <Text style={styles.updatingTitle}>SYNCING SOIL DATASET</Text>
            </View>
            <Text style={styles.updatingPercentText}>{Math.round(syncPercent * 100)}%</Text>
          </View>

          {/* Sleek Progress Bar Track & Animated Fill */}
          <View style={styles.progressBarTrack}>
            <Animated.View style={[styles.progressBarFill, { width: progressWidth }]} />
          </View>

          {/* Detailed status: real & fake soil images */}
          <Text style={styles.updatingDetailText} numberOfLines={1}>
            {syncStatus}
          </Text>
        </View>
      </Animated.View>
    </Animated.View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#DAF1DE',
    overflow: 'hidden',
  },

  centerStage: {
    ...StyleSheet.absoluteFillObject,
    alignItems: 'center',
    justifyContent: 'center',
    zIndex: 999,
    elevation: 50,
  },

  /* Lower Screen Updating & Sync Container */
  bottomUpdatingContainer: {
    position: 'absolute',
    bottom: Platform.OS === 'ios' ? 44 : 34,
    left: 20,
    right: 20,
    zIndex: 9999,
    elevation: 60,
    alignItems: 'center',
  },
  updatingCard: {
    width: '100%',
    maxWidth: 420,
    backgroundColor: 'rgba(255, 255, 255, 0.90)',
    borderRadius: 16,
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderWidth: 1,
    borderColor: 'rgba(34, 197, 94, 0.25)',
    shadowColor: '#052e16',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.10,
    shadowRadius: 10,
    elevation: 6,
  },
  updatingHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  updatingBadge: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  pulsingDot: {
    width: 7,
    height: 7,
    borderRadius: 3.5,
    backgroundColor: '#16a34a',
    marginRight: 7,
  },
  updatingTitle: {
    fontSize: 10,
    fontWeight: '800',
    color: '#15803d',
    letterSpacing: 0.8,
    fontFamily: Platform.OS === 'ios' ? 'System' : 'sans-serif-medium',
  },
  updatingPercentText: {
    fontSize: 11,
    fontWeight: '700',
    color: '#16a34a',
    fontFamily: Platform.OS === 'ios' ? 'System' : 'sans-serif-medium',
  },
  progressBarTrack: {
    height: 5,
    borderRadius: 2.5,
    backgroundColor: 'rgba(22, 163, 74, 0.14)',
    overflow: 'hidden',
    marginBottom: 7,
  },
  progressBarFill: {
    height: '100%',
    borderRadius: 2.5,
    backgroundColor: '#16a34a',
  },
  updatingDetailText: {
    fontSize: 11,
    fontWeight: '500',
    color: '#374151',
    fontFamily: Platform.OS === 'ios' ? 'System' : 'sans-serif',
  },

  /* Wave Wrapper: top: -25vh, height: 150vh matching splash.blade.php */
  waveWrapper: {
    position: 'absolute',
    left: 0,
    top: WAVE_BASE_TOP,
    width: SCREEN_WIDTH,
    height: WAVE_HEIGHT,
    zIndex: 10,
    elevation: 10,
  },
  waveImage: {
    width: SCREEN_WIDTH,
    height: WAVE_HEIGHT,
  },

  /* Brand Lockup: Logo + Wordmark centered */
  brandLockup: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  logoWrapper: {
    width: 64,
    height: 64,
    alignItems: 'center',
    justifyContent: 'center',
  },
  logoImage: {
    width: 64,
    height: 64,
  },
  wordWrapper: {
    overflow: 'hidden',
    justifyContent: 'center',
    height: 48,
  },
  wordText: {
    width: 160,
    fontSize: 38,
    fontWeight: '800',
    color: '#16a34a',
    letterSpacing: -1,
    fontFamily: Platform.OS === 'ios' ? 'System' : 'sans-serif-medium',
    includeFontPadding: false,
  },
  wordHighlight: {
    color: '#22c55e',
    fontWeight: '800',
  },
});
