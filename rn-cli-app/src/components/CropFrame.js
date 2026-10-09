// src/components/CropFrame.js
// Visual-only overlay for the scan viewfinder: dimmed outside, thin white frame, rule-of-thirds grid,
// thick white corner brackets and edge-midpoint bars. It never intercepts touches (pointerEvents none),
// so it can sit on top of the camera preview or the picked image without affecting capture.
import React from 'react';
import { View, Text, StyleSheet, useWindowDimensions } from 'react-native';

const T = 4;     // bracket / bar thickness
const L = 22;    // bracket arm length
const BAR = 28;  // edge bar length
const WHITE = '#fff';
const GRID = 'rgba(255,255,255,0.55)';
const DIM = 'rgba(0,0,0,0.55)';

export default function CropFrame({ size = 260, offsetY = 0, label = null }) {
  const { width: W, height: H } = useWindowDimensions();
  const left = (W - size) / 2;
  const top = (H - size) / 2 + offsetY;

  return (
    <View style={StyleSheet.absoluteFill} pointerEvents="none">
      {/* dim the area outside the frame */}
      <View style={[s.abs, { top: 0, left: 0, right: 0, height: top, backgroundColor: DIM }]} />
      <View style={[s.abs, { top: top + size, left: 0, right: 0, bottom: 0, backgroundColor: DIM }]} />
      <View style={[s.abs, { top, left: 0, width: left, height: size, backgroundColor: DIM }]} />
      <View style={[s.abs, { top, left: left + size, right: 0, height: size, backgroundColor: DIM }]} />

      {/* frame */}
      <View style={[s.abs, { top, left, width: size, height: size, borderWidth: 1, borderColor: 'rgba(255,255,255,0.9)' }]}>
        {/* rule-of-thirds grid */}
        <View style={[s.abs, { left: '33.333%', top: 0, bottom: 0, width: StyleSheet.hairlineWidth * 2, backgroundColor: GRID }]} />
        <View style={[s.abs, { left: '66.666%', top: 0, bottom: 0, width: StyleSheet.hairlineWidth * 2, backgroundColor: GRID }]} />
        <View style={[s.abs, { top: '33.333%', left: 0, right: 0, height: StyleSheet.hairlineWidth * 2, backgroundColor: GRID }]} />
        <View style={[s.abs, { top: '66.666%', left: 0, right: 0, height: StyleSheet.hairlineWidth * 2, backgroundColor: GRID }]} />

        {/* corner brackets */}
        <View style={[s.abs, { left: -1, top: -1, width: L, height: L, borderLeftWidth: T, borderTopWidth: T, borderColor: WHITE }]} />
        <View style={[s.abs, { right: -1, top: -1, width: L, height: L, borderRightWidth: T, borderTopWidth: T, borderColor: WHITE }]} />
        <View style={[s.abs, { left: -1, bottom: -1, width: L, height: L, borderLeftWidth: T, borderBottomWidth: T, borderColor: WHITE }]} />
        <View style={[s.abs, { right: -1, bottom: -1, width: L, height: L, borderRightWidth: T, borderBottomWidth: T, borderColor: WHITE }]} />

        {/* edge-midpoint bars */}
        <View style={[s.abs, { top: -1, left: size / 2 - BAR / 2 - 1, width: BAR, height: T, backgroundColor: WHITE }]} />
        <View style={[s.abs, { bottom: -1, left: size / 2 - BAR / 2 - 1, width: BAR, height: T, backgroundColor: WHITE }]} />
        <View style={[s.abs, { left: -1, top: size / 2 - BAR / 2 - 1, width: T, height: BAR, backgroundColor: WHITE }]} />
        <View style={[s.abs, { right: -1, top: size / 2 - BAR / 2 - 1, width: T, height: BAR, backgroundColor: WHITE }]} />
      </View>

      {/* optional size label above the frame, e.g. label="W: 1024 px" */}
      {label ? (
        <View style={[s.abs, { top: top - 34, left: 0, right: 0, alignItems: 'center' }]}>
          <Text style={s.label}>{label}</Text>
        </View>
      ) : null}
    </View>
  );
}

const s = StyleSheet.create({
  abs: { position: 'absolute' },
  label: {
    backgroundColor: 'rgba(0,0,0,0.72)',
    color: '#fff',
    fontSize: 12,
    paddingHorizontal: 9,
    paddingVertical: 5,
    borderRadius: 4,
    overflow: 'hidden',
  },
});
