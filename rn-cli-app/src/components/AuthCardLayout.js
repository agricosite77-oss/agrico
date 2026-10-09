// src/components/AuthCardLayout.js
// Layout only: a large hero area on top and a rounded card that overlaps it.
// Your existing inputs, buttons, icons, validation and loading logic go inside as children.
import React, { useEffect, useRef } from 'react';
import {
  View,
  Text,
  ScrollView,
  KeyboardAvoidingView,
  Platform,
  StatusBar,
  StyleSheet,
  Animated,
  Easing,
  useWindowDimensions,
  AccessibilityInfo,
} from 'react-native';

const OVERLAP = 28; // how far the card rides up over the hero

export default function AuthCardLayout({
  hero,               // your existing gradient + logo (any node)
  title,              // first title line, e.g. "Welcome back to"
  highlight,          // second title line in green, e.g. "AgriCo"
  children,           // your fields, actions, buttons
  footer,             // e.g. your "Forgot password" / "Create an account" row
  overlay,            // e.g. <LoadingOverlay visible={loading} />
  heroRatio = 0.38,   // share of screen height used by the hero
}) {
  const { height } = useWindowDimensions();
  const heroH = Math.round(height * heroRatio);

  // Android fix: opacity:0 on a native-driver Animated.View blocks ALL child
  // touch events at the native layer — TextInputs can't focus, keyboard never opens.
  // Solution: on Android we skip the opacity animation entirely (just slide in).
  // iOS gets the full fade + slide as designed.
  const enterOpacity = useRef(new Animated.Value(Platform.OS === 'android' ? 1 : 0)).current;
  const enterSlide = useRef(new Animated.Value(28)).current;

  useEffect(() => {
    const animations = [
      Animated.timing(enterSlide, {
        toValue: 0,
        // Android: instant snap — a translateY animation in progress causes Android
        // to silently reject IME (keyboard) requests from focused TextInputs.
        duration: Platform.OS === 'android' ? 0 : 450,
        easing: Easing.bezier(0.22, 1, 0.36, 1),
        useNativeDriver: true,
      }),
    ];
    // Only animate opacity on iOS — Android keeps opacity=1 always
    if (Platform.OS !== 'android') {
      animations.unshift(
        Animated.timing(enterOpacity, {
          toValue: 1,
          duration: 450,
          easing: Easing.bezier(0.22, 1, 0.36, 1),
          useNativeDriver: true,
        })
      );
    }
    Animated.parallel(animations).start();
  }, [enterOpacity, enterSlide]);

  return (
    <View style={s.root}>
      <StatusBar barStyle="dark-content" translucent backgroundColor="transparent" />

      {/* Upper visual area */}
      <View style={[s.hero, { height: heroH + OVERLAP }]}>{hero}</View>

      <KeyboardAvoidingView style={s.flex} behavior={Platform.OS === 'ios' ? 'padding' : 'height'}>
        <ScrollView
          contentContainerStyle={s.scroll}
          keyboardShouldPersistTaps="always"
          showsVerticalScrollIndicator={false}
        >
          {/* Empty space so the card starts over the bottom of the hero */}
          <View style={{ height: heroH - OVERLAP }} />

          <Animated.View
            style={[
              s.card,
              {
                minHeight: height - heroH + OVERLAP,
                opacity: enterOpacity,
                transform: [{ translateY: enterSlide }],
              },
            ]}
          >
            <View style={s.handle} />

            {(title || highlight) && (
              <View style={s.titleWrap}>
                {!!title && <Text style={s.title}>{title}</Text>}
                {!!highlight && <Text style={[s.title, s.highlight]}>{highlight}</Text>}
              </View>
            )}

            {React.Children.toArray(children).map((c, i) => (
              <Stagger key={i} index={i}>{c}</Stagger>
            ))}

            {!!footer && <View style={s.footer}>{footer}</View>}
          </Animated.View>
        </ScrollView>
      </KeyboardAvoidingView>

      {overlay}
    </View>
  );
}

// Each piece of the card fades and rises in turn (title, fields, button...)
// ANDROID NOTE: Animated.View with useNativeDriver=true and opacity=0 blocks ALL
// touch events at the native layer — TextInputs can't focus and keyboard never opens.
// Solution: Android version never animates opacity (always 1). iOS gets full fade+slide.
function StaggerAndroid({ index, children }) {
  const slide = useRef(new Animated.Value(12)).current;
  useEffect(() => {
    Animated.timing(slide, {
      toValue: 0,
      duration: 380,
      delay: 120 + Math.min(index, 6) * 55,
      easing: Easing.bezier(0.22, 1, 0.36, 1),
      useNativeDriver: true,
    }).start();
  }, [slide, index]);
  return (
    <Animated.View style={{ transform: [{ translateY: slide }] }}>
      {children}
    </Animated.View>
  );
}

function StaggerIOS({ index, children }) {
  const v = useRef(new Animated.Value(0)).current;
  useEffect(() => {
    let alive = true;
    AccessibilityInfo.isReduceMotionEnabled().then((reduce) => {
      if (!alive) return;
      if (reduce) { v.setValue(1); return; }
      Animated.timing(v, {
        toValue: 1,
        duration: 380,
        delay: 120 + Math.min(index, 6) * 55,
        easing: Easing.bezier(0.22, 1, 0.36, 1),
        useNativeDriver: true,
      }).start();
    });
    return () => { alive = false; };
  }, [v, index]);
  return (
    <Animated.View style={{ opacity: v, transform: [{ translateY: v.interpolate({ inputRange: [0, 1], outputRange: [12, 0] }) }] }}>
      {children}
    </Animated.View>
  );
}

const Stagger = Platform.OS === 'android' ? StaggerAndroid : StaggerIOS;

const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#f6faf7' },
  flex: { flex: 1 },
  hero: { position: 'absolute', top: 0, left: 0, right: 0, overflow: 'hidden' },
  scroll: { flexGrow: 1 },
  card: {
    flexGrow: 1,
    backgroundColor: '#ffffff',
    borderTopLeftRadius: 28,
    borderTopRightRadius: 28,
    paddingHorizontal: 22,
    paddingTop: 10,
    paddingBottom: 24,
    shadowColor: '#0f172a',
    shadowOpacity: 0.08,
    shadowRadius: 16,
    shadowOffset: { width: 0, height: -4 },
    elevation: 10,
  },
  handle: { alignSelf: 'center', width: 36, height: 4, borderRadius: 2, backgroundColor: '#e2e8f0', marginBottom: 14 },
  titleWrap: { alignItems: 'center', marginBottom: 16 },
  title: { fontSize: 22, lineHeight: 28, fontWeight: '800', color: '#0f172a', textAlign: 'center' },
  highlight: { color: '#16a34a' },
  footer: { marginTop: 'auto', paddingTop: 16, alignItems: 'center' },
});
