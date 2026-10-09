// src/components/ResizableFrame.js
//
// Interactive Resizable Viewfinder:
// 3-Tier Visual Brightness / Contrast:
// 1. Non-usable screen area (outside bounds): DARKER (rgba(0,0,0, 0.72))
// 2. Allowable bounds work area (inside bounds, outside frame): MEDIUM DARK (rgba(0,0,0, 0.40))
// 3. Resizable viewfinder window (inside rect): LIGHT & CLEAR (transparent view with grid & brackets)
//
import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { View, Text, PanResponder, StyleSheet } from 'react-native';

const HIT = 30; // generous touch padding around visible handles for effortless dragging on mobile
const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
const hasSize = (b) => b && b.width > 0 && b.height > 0;

function centeredRect(b, size) {
  const s = Math.min(size, b.width, b.height);
  return { x: b.x + (b.width - s) / 2, y: b.y + (b.height - s) / 2, width: s, height: s };
}

// Pure geometry: calculate new rect for gesture (dx, dy) starting from rect `s`.
function resize(type, s, dx, dy, b, min, square) {
  const bR = b.x + b.width, bB = b.y + b.height;
  const sR = s.x + s.width, sB = s.y + s.height;

  if (type === 'move') {
    return {
      x: clamp(s.x + dx, b.x, bR - s.width),
      y: clamp(s.y + dy, b.y, bB - s.height),
      width: s.width,
      height: s.height,
    };
  }

  if (!square) {
    let L = s.x, T = s.y, R = sR, B = sB;
    if (type.includes('e')) R = clamp(R + dx, L + min, bR);
    if (type.includes('w')) L = clamp(L + dx, b.x, R - min);
    if (type.includes('s')) B = clamp(B + dy, T + min, bB);
    if (type.includes('n')) T = clamp(T + dy, b.y, B - min);
    return { x: L, y: T, width: R - L, height: B - T };
  }

  // Square mode: frame always stays square
  const isCorner = type.length === 2;
  const sx = type.includes('e') ? 1 : type.includes('w') ? -1 : 0;
  const sy = type.includes('s') ? 1 : type.includes('n') ? -1 : 0;

  let d;
  if (isCorner) d = Math.abs(dx) >= Math.abs(dy) ? sx * dx : sy * dy;
  else d = sx !== 0 ? sx * dx : sy * dy;

  let max;
  if (isCorner) {
    max = Math.min(sx > 0 ? bR - s.x : sR - b.x, sy > 0 ? bB - s.y : sB - b.y);
  } else if (sx !== 0) {
    // e / w: grows sideways, vertically centered
    max = Math.min(
      sx > 0 ? bR - s.x : sR - b.x,
      s.height + 2 * (s.y - b.y),
      s.height + 2 * (bB - sB)
    );
  } else {
    // n / s: grows vertically, horizontally centered
    max = Math.min(
      sy > 0 ? bB - s.y : sB - b.y,
      s.width + 2 * (s.x - b.x),
      s.width + 2 * (bR - sR)
    );
  }
  const size = clamp(s.width + d, min, Math.max(min, max));

  let x, y;
  if (isCorner) {
    x = sx > 0 ? s.x : sR - size;
    y = sy > 0 ? s.y : sB - size;
  } else if (sx !== 0) {
    x = sx > 0 ? s.x : sR - size;
    y = s.y - (size - s.height) / 2;
  } else {
    y = sy > 0 ? s.y : sB - size;
    x = s.x - (size - s.width) / 2;
  }
  return { x, y, width: size, height: size };
}

// Visual layout matching the reference viewfinder design:
// White brackets at 4 corners, 4 white midpoint bars, rule-of-thirds grid
export function ScanFrameVisual({
  width,
  height,
  color = '#ffffff',
  gridColor = 'rgba(255, 255, 255, 0.45)',
  borderColor = 'rgba(255, 255, 255, 0.85)',
  thickness = 4,
  bracketLength = 24,
  barLength = 32,
  showGrid = true,
}) {
  const T = thickness;
  const L = bracketLength;
  const BAR = barLength;
  const W = width;
  const H = height;

  return (
    <View pointerEvents="none" style={{ width: W, height: H, position: 'relative' }}>
      {/* 1. Thin Enclosing Border */}
      <View
        style={{
          position: 'absolute',
          left: 0,
          top: 0,
          width: W,
          height: H,
          borderWidth: 1,
          borderColor: borderColor,
        }}
      />

      {/* 2. Rule-of-Thirds Grid */}
      {showGrid && (
        <>
          <View
            style={{
              position: 'absolute',
              left: '33.333%',
              top: 0,
              bottom: 0,
              width: 1,
              backgroundColor: gridColor,
            }}
          />
          <View
            style={{
              position: 'absolute',
              left: '66.666%',
              top: 0,
              bottom: 0,
              width: 1,
              backgroundColor: gridColor,
            }}
          />
          <View
            style={{
              position: 'absolute',
              top: '33.333%',
              left: 0,
              right: 0,
              height: 1,
              backgroundColor: gridColor,
            }}
          />
          <View
            style={{
              position: 'absolute',
              top: '66.666%',
              left: 0,
              right: 0,
              height: 1,
              backgroundColor: gridColor,
            }}
          />
        </>
      )}

      {/* 3. 4 Corner Brackets */}
      {/* Top Left */}
      <View
        style={{
          position: 'absolute',
          left: -1,
          top: -1,
          width: L,
          height: L,
          borderLeftWidth: T,
          borderTopWidth: T,
          borderColor: color,
        }}
      />
      {/* Top Right */}
      <View
        style={{
          position: 'absolute',
          right: -1,
          top: -1,
          width: L,
          height: L,
          borderRightWidth: T,
          borderTopWidth: T,
          borderColor: color,
        }}
      />
      {/* Bottom Left */}
      <View
        style={{
          position: 'absolute',
          left: -1,
          bottom: -1,
          width: L,
          height: L,
          borderLeftWidth: T,
          borderBottomWidth: T,
          borderColor: color,
        }}
      />
      {/* Bottom Right */}
      <View
        style={{
          position: 'absolute',
          right: -1,
          bottom: -1,
          width: L,
          height: L,
          borderRightWidth: T,
          borderBottomWidth: T,
          borderColor: color,
        }}
      />

      {/* 4. 4 Midpoint Bars (Resize Handles) */}
      {/* Top Midpoint Bar */}
      <View
        style={{
          position: 'absolute',
          top: -1,
          left: Math.max(0, W / 2 - BAR / 2),
          width: BAR,
          height: T,
          backgroundColor: color,
          borderRadius: 1,
        }}
      />
      {/* Bottom Midpoint Bar */}
      <View
        style={{
          position: 'absolute',
          bottom: -1,
          left: Math.max(0, W / 2 - BAR / 2),
          width: BAR,
          height: T,
          backgroundColor: color,
          borderRadius: 1,
        }}
      />
      {/* Left Midpoint Bar */}
      <View
        style={{
          position: 'absolute',
          left: -1,
          top: Math.max(0, H / 2 - BAR / 2),
          width: T,
          height: BAR,
          backgroundColor: color,
          borderRadius: 1,
        }}
      />
      {/* Right Midpoint Bar */}
      <View
        style={{
          position: 'absolute',
          right: -1,
          top: Math.max(0, H / 2 - BAR / 2),
          width: T,
          height: BAR,
          backgroundColor: color,
          borderRadius: 1,
        }}
      />
    </View>
  );
}

export default function ResizableFrame({
  bounds,
  initialSize = 220,
  minSize = 80,
  square = true,
  movable = true,
  disabled = false,
  showHandles = true,
  dimOutside = 0.40,     // Medium dark tint inside the allowable workspace
  dimInactive = 0.72,    // Extra dark tint on non-usable areas of the screen
  dimBounds,
  resetSignal,
  onChange,
  onResizeEnd,
  children,
}) {
  const [rect, setRect] = useState(null);
  const [activeHandle, setActiveHandle] = useState(null);
  const rectRef = useRef(null);
  const startRef = useRef(null);
  const boundsRef = useRef(bounds);
  boundsRef.current = bounds;
  const propsRef = useRef({});
  propsRef.current = { minSize, square, movable, disabled, onChange, onResizeEnd };

  const commit = useCallback((r) => {
    rectRef.current = r;
    setRect(r);
    if (propsRef.current.onChange) propsRef.current.onChange(r);
  }, []);

  // Place the frame once the bounds are known, and keep it inside bounds when they change
  useEffect(() => {
    if (!hasSize(bounds)) return;
    const r = rectRef.current;
    if (!r) {
      commit(centeredRect(bounds, initialSize));
      return;
    }
    let w = Math.min(r.width, bounds.width);
    let h = Math.min(r.height, bounds.height);
    if (propsRef.current.square) w = h = Math.min(w, h);
    const next = {
      x: clamp(r.x, bounds.x, bounds.x + bounds.width - w),
      y: clamp(r.y, bounds.y, bounds.y + bounds.height - h),
      width: w,
      height: h,
    };
    if (next.x !== r.x || next.y !== r.y || next.width !== r.width || next.height !== r.height) {
      commit(next);
    }
  }, [bounds.x, bounds.y, bounds.width, bounds.height, initialSize, commit]);

  // Reset button support
  const firstReset = useRef(true);
  useEffect(() => {
    if (firstReset.current) {
      firstReset.current = false;
      return;
    }
    if (hasSize(boundsRef.current)) commit(centeredRect(boundsRef.current, initialSize));
  }, [resetSignal]); // eslint-disable-line react-hooks/exhaustive-deps

  const responders = useMemo(() => {
    const make = (type) =>
      PanResponder.create({
        onStartShouldSetPanResponder: () =>
          !propsRef.current.disabled && (type !== 'move' || propsRef.current.movable),
        onMoveShouldSetPanResponder: () =>
          !propsRef.current.disabled && (type !== 'move' || propsRef.current.movable),
        onPanResponderTerminationRequest: () => false,
        onPanResponderGrant: () => {
          startRef.current = rectRef.current;
          setActiveHandle(type);
        },
        onPanResponderMove: (_, g) => {
          if (!startRef.current || !hasSize(boundsRef.current)) return;
          const p = propsRef.current;
          commit(resize(type, startRef.current, g.dx, g.dy, boundsRef.current, p.minSize, p.square));
        },
        onPanResponderRelease: () => {
          setActiveHandle(null);
          if (propsRef.current.onResizeEnd) propsRef.current.onResizeEnd(rectRef.current);
        },
        onPanResponderTerminate: () => {
          setActiveHandle(null);
          if (propsRef.current.onResizeEnd) propsRef.current.onResizeEnd(rectRef.current);
        },
      });
    const map = {};
    ['nw', 'ne', 'sw', 'se', 'n', 's', 'e', 'w', 'move'].forEach((t) => {
      map[t] = make(t);
    });
    return map;
  }, [commit]);

  if (!rect) return null;

  // 8 resize handles: 4 corners + 4 edge midpoints
  const handles = [
    ['nw', 0, 0, 'corner'],
    ['ne', 1, 0, 'corner'],
    ['sw', 0, 1, 'corner'],
    ['se', 1, 1, 'corner'],
    ['n', 0.5, 0, 'h-bar'],
    ['s', 0.5, 1, 'h-bar'],
    ['w', 0, 0.5, 'v-bar'],
    ['e', 1, 0.5, 'v-bar'],
  ];

  const size = { width: rect.width, height: rect.height };
  let content;
  if (typeof children === 'function') content = children(size);
  else if (children) content = children;
  else content = <ScanFrameVisual {...size} />;

  const D = dimBounds || bounds;
  const B = bounds;

  // 3-Tier Dimming Calculations:
  // 1. Inactive area (outside B within D)
  const inactiveDark = `rgba(0,0,0,${dimInactive})`;
  // 2. Allowable bounds area (inside B, outside rect)
  const boundsDark = `rgba(0,0,0,${dimOutside})`;

  return (
    <>
      {/* 3-Tier Overlay System */}
      {hasSize(D) && (
        <View pointerEvents="none" style={StyleSheet.absoluteFillObject}>
          {/* Tier 1: Extra Dark Areas (Screen areas outside the allowable bounds) */}
          {/* Top Inactive Bar (above bounds) */}
          {B.y > D.y && (
            <View
              style={{
                position: 'absolute',
                left: D.x,
                top: D.y,
                width: D.width,
                height: Math.max(0, B.y - D.y),
                backgroundColor: inactiveDark,
              }}
            />
          )}
          {/* Bottom Inactive Bar (below bounds) */}
          {D.y + D.height > B.y + B.height && (
            <View
              style={{
                position: 'absolute',
                left: D.x,
                top: B.y + B.height,
                width: D.width,
                height: Math.max(0, (D.y + D.height) - (B.y + B.height)),
                backgroundColor: inactiveDark,
              }}
            />
          )}
          {/* Left Inactive Bar */}
          {B.x > D.x && (
            <View
              style={{
                position: 'absolute',
                left: D.x,
                top: B.y,
                width: Math.max(0, B.x - D.x),
                height: B.height,
                backgroundColor: inactiveDark,
              }}
            />
          )}
          {/* Right Inactive Bar */}
          {D.x + D.width > B.x + B.width && (
            <View
              style={{
                position: 'absolute',
                left: B.x + B.width,
                top: B.y,
                width: Math.max(0, (D.x + D.width) - (B.x + B.width)),
                height: B.height,
                backgroundColor: inactiveDark,
              }}
            />
          )}

          {/* Tier 2: Medium Dark Area (Inside allowable bounds, outside the viewfinder frame) */}
          {/* Top of frame inside bounds */}
          <View
            style={{
              position: 'absolute',
              left: B.x,
              top: B.y,
              width: B.width,
              height: Math.max(0, rect.y - B.y),
              backgroundColor: boundsDark,
            }}
          />
          {/* Bottom of frame inside bounds */}
          <View
            style={{
              position: 'absolute',
              left: B.x,
              top: rect.y + rect.height,
              width: B.width,
              height: Math.max(0, (B.y + B.height) - (rect.y + rect.height)),
              backgroundColor: boundsDark,
            }}
          />
          {/* Left of frame inside bounds */}
          <View
            style={{
              position: 'absolute',
              left: B.x,
              top: rect.y,
              width: Math.max(0, rect.x - B.x),
              height: rect.height,
              backgroundColor: boundsDark,
            }}
          />
          {/* Right of frame inside bounds */}
          <View
            style={{
              position: 'absolute',
              left: rect.x + rect.width,
              top: rect.y,
              width: Math.max(0, (B.x + B.width) - (rect.x + rect.width)),
              height: rect.height,
              backgroundColor: boundsDark,
            }}
          />
        </View>
      )}

      {/* Tier 3: Light & Clear Resizable Viewfinder Frame */}
      <View
        pointerEvents="box-none"
        style={{
          position: 'absolute',
          left: rect.x - HIT,
          top: rect.y - HIT,
          width: rect.width + HIT * 2,
          height: rect.height + HIT * 2,
        }}
      >
        {/* Center Move Area */}
        <View
          style={{ position: 'absolute', left: HIT, top: HIT, width: rect.width, height: rect.height }}
          {...responders.move.panHandlers}
        >
          {content}
        </View>

        {/* 8 Touch Handles for Resizing */}
        {!disabled &&
          handles.map(([type, fx, fy, shape]) => {
            const isPressed = activeHandle === type;
            return (
              <View
                key={type}
                {...responders[type].panHandlers}
                style={{
                  position: 'absolute',
                  width: HIT * 2,
                  height: HIT * 2,
                  left: fx * rect.width,
                  top: fy * rect.height,
                  alignItems: 'center',
                  justifyContent: 'center',
                  zIndex: 50,
                }}
              >
                {showHandles && (
                  shape === 'corner' ? (
                    /* Corner Circular Grab Handle Knob */
                    <View
                      pointerEvents="none"
                      style={{
                        width: 15,
                        height: 15,
                        borderRadius: 7.5,
                        backgroundColor: isPressed ? '#22c55e' : '#ffffff',
                        borderWidth: 2,
                        borderColor: '#22c55e',
                        shadowColor: '#000000',
                        shadowOffset: { width: 0, height: 2 },
                        shadowOpacity: 0.45,
                        shadowRadius: 3,
                        elevation: 5,
                      }}
                    />
                  ) : shape === 'h-bar' ? (
                    /* Top/Bottom Horizontal Grab Handle Pill */
                    <View
                      pointerEvents="none"
                      style={{
                        width: 30,
                        height: 8,
                        borderRadius: 4,
                        backgroundColor: isPressed ? '#22c55e' : '#ffffff',
                        borderWidth: 1.5,
                        borderColor: '#22c55e',
                        shadowColor: '#000000',
                        shadowOffset: { width: 0, height: 1 },
                        shadowOpacity: 0.4,
                        shadowRadius: 2,
                        elevation: 4,
                      }}
                    />
                  ) : (
                    /* Left/Right Vertical Grab Handle Pill */
                    <View
                      pointerEvents="none"
                      style={{
                        width: 8,
                        height: 30,
                        borderRadius: 4,
                        backgroundColor: isPressed ? '#22c55e' : '#ffffff',
                        borderWidth: 1.5,
                        borderColor: '#22c55e',
                        shadowColor: '#000000',
                        shadowOffset: { width: 0, height: 1 },
                        shadowOpacity: 0.4,
                        shadowRadius: 2,
                        elevation: 4,
                      }}
                    />
                  )
                )}
              </View>
            );
          })}
      </View>
    </>
  );
}

// Maps the on-screen frame to pixel coordinates in the captured photo
export function frameToImageCrop(rect, preview, image) {
  const scale = Math.max(preview.width / image.width, preview.height / image.height);
  const offX = (image.width * scale - preview.width) / 2;
  const offY = (image.height * scale - preview.height) / 2;
  const x = clamp(Math.round((rect.x + offX) / scale), 0, image.width - 1);
  const y = clamp(Math.round((rect.y + offY) / scale), 0, image.height - 1);
  const width = clamp(Math.round(rect.width / scale), 1, image.width - x);
  const height = clamp(Math.round(rect.height / scale), 1, image.height - y);
  return { x, y, width, height };
}
