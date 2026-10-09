// src/components/AuthUI.js
// Shared pieces for the four auth screens: hero, floating-label field, pill button, icons, API helper.
import React, { useEffect, useRef, useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, TouchableWithoutFeedback,
  ActivityIndicator, Animated, Easing, Image, StyleSheet, Modal, Platform,
} from 'react-native';
import Svg, { Path, Circle, Rect, Line, Polyline } from 'react-native-svg';
import ENV from '../config/env';

/* ---------- API helper ---------- */
export async function api(path, body) {
  let res;
  try {
    res = await fetch(`${ENV.API_URL}${path}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(body),
    });
  } catch (e) {
    throw new Error('Unable to connect to AgriCo server. Please check your connection.');
  }
  let data = {};
  try { data = await res.json(); } catch (e) { }
  if (!res.ok) {
    const first = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
    throw new Error(first || data?.message || 'Something went wrong. Please try again.');
  }
  return data;
}

/* ---------- Icons (swap for your own if you prefer) ---------- */
const ic = { fill: 'none', strokeWidth: 2, strokeLinecap: 'round', strokeLinejoin: 'round' };
export const MailIcon = ({ color = '#475569' }) => (
  <Svg width={18} height={18} viewBox="0 0 24 24" stroke={color} {...ic}>
    <Path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
    <Polyline points="22,6 12,13 2,6" />
  </Svg>
);
export const LockIcon = ({ color = '#475569' }) => (
  <Svg width={18} height={18} viewBox="0 0 24 24" stroke={color} {...ic}>
    <Rect x="3" y="11" width="18" height="11" rx="2" />
    <Path d="M7 11V7a5 5 0 0 1 10 0v4" />
  </Svg>
);
export const ShieldIcon = ({ color = '#475569' }) => (
  <Svg width={18} height={18} viewBox="0 0 24 24" stroke={color} {...ic}>
    <Path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
    <Polyline points="9 12 11 14 15 10" />
  </Svg>
);
const EyeIcon = () => (
  <Svg width={18} height={18} viewBox="0 0 24 24" stroke="#94a3b8" {...ic}>
    <Path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
    <Circle cx="12" cy="12" r="3" />
  </Svg>
);
const EyeOffIcon = () => (
  <Svg width={18} height={18} viewBox="0 0 24 24" stroke="#94a3b8" {...ic}>
    <Path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
    <Line x1="1" y1="1" x2="23" y2="23" />
  </Svg>
);
const ArrowLeft = () => (
  <Svg width={14} height={14} viewBox="0 0 24 24" stroke="#16a34a" {...ic}>
    <Line x1="19" y1="12" x2="5" y2="12" />
    <Polyline points="12 19 5 12 12 5" />
  </Svg>
);

/* ---------- Hero: soft gradient + rings + logo and name close together ---------- */
const lerp = (a, b, t) => Math.round(a + (b - a) * t);
function SoftGradient({ steps = 16 }) {
  return (
    <View style={StyleSheet.absoluteFill}>
      {Array.from({ length: steps }).map((_, i) => {
        const t = i / (steps - 1);
        return <View key={i} style={{ flex: 1, backgroundColor: `rgb(${lerp(218, 241, t)},${lerp(241, 250, t)},${lerp(222, 243, t)})` }} />;
      })}
    </View>
  );
}

export function AuthHero({ big = false, logo = require('../assets/agrico-logo.png') }) {
  const w = big ? 52 : 40, h = big ? 60 : 46;
  const enter = useRef(new Animated.Value(0)).current;
  useEffect(() => {
    Animated.timing(enter, { toValue: 1, duration: 450, easing: Easing.bezier(0.22, 1, 0.36, 1), useNativeDriver: true }).start();
  }, [enter]);
  return (
    <View style={StyleSheet.absoluteFill}>
      <SoftGradient />
      {[260, 360].map((d, i) => (
        <View key={d} pointerEvents="none" style={{
          position: 'absolute', width: d, height: d, borderRadius: d / 2, borderWidth: 1.5,
          borderColor: `rgba(134,239,172,${i ? 0.35 : 0.55})`, alignSelf: 'center', top: '50%', marginTop: -d / 2 - 20,
        }} />
      ))}


      <View style={a.center}>
        <Animated.View style={[a.row, { opacity: enter, transform: [{ scale: enter.interpolate({ inputRange: [0, 1], outputRange: [0.94, 1] }) }] }]}>
          {logo ? (
            <Image source={logo} style={{ width: w, height: h }} resizeMode="contain" />
          ) : (
            <Svg width={w} height={h} viewBox="0 0 100 120">
              <Path d="M50 112C50 112 14 76 14 48a36 36 0 0 1 72 0c0 28-36 64-36 64Z" fill="#fff" stroke="#22c55e" strokeWidth={6} strokeLinejoin="round" />
              <Circle cx="50" cy="48" r="25" fill="#86efac" />
              <Path d="M27 58c14-14 34-14 46-4v9C60 55 42 57 29 67Z" fill="#22c55e" />
            </Svg>
          )}
          <Text style={[a.word, { fontSize: big ? 30 : 24, marginLeft: 8 }]}>
            Agri<Text style={{ color: '#22c55e' }}>Co</Text>
          </Text>
        </Animated.View>
      </View>
    </View>
  );
}

/* ---------- Floating-label field ---------- */
export function FloatingField({ label, icon, value, onChangeText, secure = false, error, ...rest }) {
  const [focused, setFocused] = useState(false);
  const [hidden, setHidden] = useState(secure);
  const inputRef = useRef(null);
  const up = focused || !!value;
  const t = useRef(new Animated.Value(up ? 1 : 0)).current;

  useEffect(() => {
    Animated.timing(t, { toValue: up ? 1 : 0, duration: 180, easing: Easing.bezier(0.22, 1, 0.36, 1), useNativeDriver: false }).start();
  }, [up, t]);

  const top = t.interpolate({ inputRange: [0, 1], outputRange: [14, -8] });
  const size = t.interpolate({ inputRange: [0, 1], outputRange: [14, 11] });
  const border = error ? '#f87171' : focused ? '#64748b' : '#dfe3f0';
  const labelColor = error ? '#ef4444' : focused ? '#475569' : '#7d86b5';

  // A tiny delay lets the tap event cycle finish before the IME request fires.
  const handleBoxPress = () => {
    setTimeout(() => {
      inputRef.current?.focus();
    }, 50);
  };

  return (
    <View style={{ marginBottom: 14 }}>
      <TouchableWithoutFeedback onPress={handleBoxPress}>
        <View style={[f.box, { borderColor: border }]}>
          {icon}
          <TextInput
            ref={inputRef}
            style={f.input}
            value={value}
            onChangeText={onChangeText}
            secureTextEntry={hidden}
            onFocus={() => setFocused(true)}
            onBlur={() => setFocused(false)}
            showSoftInputOnFocus={true}
            {...rest}
          />
          {secure && (
            <TouchableOpacity onPress={() => setHidden((h) => !h)} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
              {hidden ? <EyeOffIcon /> : <EyeIcon />}
            </TouchableOpacity>
          )}
          <Animated.Text
            pointerEvents="none"
            style={[f.label, { top, fontSize: size, color: labelColor, backgroundColor: up ? '#fff' : 'transparent' }]}
          >
            {label}
          </Animated.Text>
        </View>
      </TouchableWithoutFeedback>
      {!!error && <Text style={f.err}>{error}</Text>}
    </View>
  );
}

/* ---------- Buttons and small pieces ---------- */
export function PillButton({ title, onPress, loading }) {
  return (
    <TouchableOpacity style={[b.btn, loading && { opacity: 0.85 }]} onPress={onPress} disabled={loading} activeOpacity={0.9}>
      {loading ? <ActivityIndicator color="#fff" /> : <Text style={b.text}>{title}</Text>}
    </TouchableOpacity>
  );
}

export function BackLink({ text, onPress }) {
  return (
    <TouchableOpacity onPress={onPress} style={b.back} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
      <ArrowLeft />
      <Text style={b.backText}>{text}</Text>
    </TouchableOpacity>
  );
}

/* ---------- Frosted loading overlay (\"Logging you in\") ---------- */
export function LoadingOverlay({ visible, title = 'Logging you in', subtitle = 'Getting your soil data ready' }) {
  const o = useRef(new Animated.Value(0)).current;
  const spin = useRef(new Animated.Value(0)).current;
  const [show, setShow] = useState(false);

  useEffect(() => {
    if (visible) {
      setShow(true);
      Animated.timing(o, { toValue: 1, duration: 250, easing: Easing.bezier(0.22, 1, 0.36, 1), useNativeDriver: true }).start();
    } else {
      Animated.timing(o, { toValue: 0, duration: 200, easing: Easing.bezier(0.4, 0, 1, 1), useNativeDriver: true }).start(() => setShow(false));
    }
  }, [visible, o]);

  useEffect(() => {
    if (!show) return;
    const loop = Animated.loop(
      Animated.timing(spin, { toValue: 1, duration: 1100, easing: Easing.linear, useNativeDriver: true })
    );
    loop.start();
    return () => loop.stop();
  }, [show, spin]);

  const rotate = spin.interpolate({ inputRange: [0, 1], outputRange: ['0deg', '360deg'] });

  return (
    <Modal visible={show} transparent animationType="none" statusBarTranslucent onRequestClose={() => { }}>
      <Animated.View style={[StyleSheet.absoluteFill, b.overlay, { opacity: o }]}>
        <View style={b.overlayCard}>
          <Animated.View style={[b.spinner, { transform: [{ rotate }] }]} />
          <Text style={b.overTitle}>{title}</Text>
          <Text style={b.overSub}>{subtitle}</Text>
        </View>
      </Animated.View>
    </Modal>
  );
}

export const ErrorBanner = ({ text }) => (
  <View style={b.banner}><Text style={b.bannerText}>{text}</Text></View>
);

const a = StyleSheet.create({
  center: { ...StyleSheet.absoluteFillObject, justifyContent: 'center', alignItems: 'center', paddingBottom: 56 },
  row: { flexDirection: 'row', alignItems: 'center' },
  word: { fontWeight: '800', color: '#16a34a', letterSpacing: -0.5 },
});
const f = StyleSheet.create({
  box: { height: 48, borderWidth: 1.5, borderRadius: 14, flexDirection: 'row', alignItems: 'center', paddingHorizontal: 14, backgroundColor: '#fff' },
  input: { flex: 1, marginLeft: 10, paddingVertical: 0, fontSize: 14, color: '#0f172a' },
  label: { position: 'absolute', left: 38, paddingHorizontal: 4 },
  err: { color: '#ef4444', fontSize: 12, marginTop: 4, marginLeft: 6 },
});
const b = StyleSheet.create({
  btn: {
    height: 48, borderRadius: 9999, backgroundColor: '#22c55e', alignItems: 'center', justifyContent: 'center',
    shadowColor: '#22c55e', shadowOpacity: 0.35, shadowRadius: 10, shadowOffset: { width: 0, height: 6 }, elevation: 4,
  },
  text: { color: '#fff', fontSize: 15, fontWeight: '700' },
  back: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', marginTop: 16 },
  backText: { color: '#16a34a', fontSize: 13, fontWeight: '600', marginLeft: 6 },
  overlay: { backgroundColor: 'rgba(15,23,42,0.45)', alignItems: 'center', justifyContent: 'center' },
  overlayCard: {
    backgroundColor: 'rgba(246,250,247,0.97)',
    borderRadius: 20,
    paddingVertical: 32,
    paddingHorizontal: 40,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOpacity: 0.15,
    shadowRadius: 20,
    shadowOffset: { width: 0, height: 8 },
    elevation: 10,
  },
  spinner: { width: 56, height: 56, borderRadius: 28, borderWidth: 4, borderColor: '#bbf7d0', borderTopColor: '#22c55e', marginBottom: 18 },
  overTitle: { fontSize: 16, fontWeight: '700', color: '#0f172a' },
  overSub: { fontSize: 12.5, color: '#64748b', marginTop: 4 },
  banner: { backgroundColor: '#fef2f2', borderRadius: 12, padding: 10, marginBottom: 12 },
  bannerText: { color: '#dc2626', fontSize: 12.5 },
});
