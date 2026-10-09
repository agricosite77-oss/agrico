import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

// 1. Bell Icon with optional unread badge
export function BellIcon({ size = 22, color = '#0f172a', hasUnread = true }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Bell body */}
      <View
        style={{
          width: size * 0.7,
          height: size * 0.65,
          borderWidth: 1.8,
          borderColor: color,
          borderTopLeftRadius: size * 0.35,
          borderTopRightRadius: size * 0.35,
          borderBottomWidth: 0,
        }}
      />
      {/* Bell rim */}
      <View
        style={{
          width: size * 0.9,
          height: 2,
          backgroundColor: color,
          borderRadius: 1,
        }}
      />
      {/* Bell clapper */}
      <View
        style={{
          width: size * 0.25,
          height: size * 0.15,
          borderBottomLeftRadius: size * 0.15,
          borderBottomRightRadius: size * 0.15,
          backgroundColor: color,
          marginTop: 1,
        }}
      />
      {/* Unread badge dot */}
      {hasUnread && (
        <View
          style={{
            position: 'absolute',
            top: 1,
            right: 1,
            width: 7,
            height: 7,
            borderRadius: 3.5,
            backgroundColor: '#22c55e',
            borderWidth: 1.2,
            borderColor: '#ffffff',
          }}
        />
      )}
    </View>
  );
}

// 2. Search / Magnifying Glass Icon
export function SearchIcon({ size = 18, color = '#ffffff' }) {
  const ringSize = size * 0.65;
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: ringSize,
          height: ringSize,
          borderRadius: ringSize / 2,
          borderWidth: 2,
          borderColor: color,
          marginTop: -2,
          marginLeft: -2,
        }}
      />
      <View
        style={{
          position: 'absolute',
          bottom: 2,
          right: 2,
          width: size * 0.38,
          height: 2.2,
          backgroundColor: color,
          borderRadius: 1,
          transform: [{ rotate: '45deg' }],
        }}
      />
    </View>
  );
}

// 3. Heart / Favorite Icon
export function HeartIcon({ size = 18, color = '#22c55e', isFilled = false }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <Text
        style={{
          fontSize: size * 0.95,
          color: isFilled ? color : '#94a3b8',
          lineHeight: size,
          marginTop: -1,
        }}
      >
        {isFilled ? '♥' : '♡'}
      </Text>
    </View>
  );
}

// 4. Plus Icon
export function PlusIcon({ size = 16, color = '#ffffff' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          position: 'absolute',
          width: size * 0.65,
          height: 2.2,
          backgroundColor: color,
          borderRadius: 1,
        }}
      />
      <View
        style={{
          position: 'absolute',
          width: 2.2,
          height: size * 0.65,
          backgroundColor: color,
          borderRadius: 1,
        }}
      />
    </View>
  );
}

// 5. Home Navigation Icon
export function HomeNavIcon({ size = 20, color = '#22c55e' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Roof */}
      <View
        style={{
          width: size * 0.72,
          height: size * 0.72,
          borderTopWidth: 2,
          borderLeftWidth: 2,
          borderColor: color,
          transform: [{ rotate: '45deg' }],
          marginTop: -size * 0.1,
        }}
      />
      {/* Base */}
      <View
        style={{
          position: 'absolute',
          bottom: size * 0.1,
          width: size * 0.55,
          height: size * 0.45,
          borderWidth: 1.8,
          borderColor: color,
          borderTopWidth: 0,
        }}
      />
    </View>
  );
}

// 6. Munsell Color Chart / Palette Nav Icon
export function MunsellNavIcon({ size = 20, color = '#64748b' }) {
  return (
    <View
      style={{
        width: size,
        height: size,
        borderRadius: 4,
        borderWidth: 1.8,
        borderColor: color,
        padding: 1.5,
        justifyContent: 'space-between',
      }}
    >
      <View style={{ flexDirection: 'row', justifyContent: 'space-between', flex: 1, marginBottom: 1.5 }}>
        <View style={{ flex: 1, backgroundColor: color, borderRadius: 1.5, marginRight: 1.5 }} />
        <View style={{ flex: 1, backgroundColor: 'rgba(34, 197, 94, 0.4)', borderRadius: 1.5 }} />
      </View>
      <View style={{ flexDirection: 'row', justifyContent: 'space-between', flex: 1 }}>
        <View style={{ flex: 1, backgroundColor: 'rgba(142, 182, 155, 0.6)', borderRadius: 1.5, marginRight: 1.5 }} />
        <View style={{ flex: 1, backgroundColor: color, borderRadius: 1.5 }} />
      </View>
    </View>
  );
}

// 7. Center Floating Scan Action Icon (Viewfinder Scanner)
export function ScanCenterIcon({ size = 24, color = '#ffffff' }) {
  const bracketSize = size * 0.3;
  const bw = 2.4;
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Top Left */}
      <View
        style={{
          position: 'absolute',
          top: 0,
          left: 0,
          width: bracketSize,
          height: bracketSize,
          borderTopWidth: bw,
          borderLeftWidth: bw,
          borderColor: color,
          borderTopLeftRadius: 3,
        }}
      />
      {/* Top Right */}
      <View
        style={{
          position: 'absolute',
          top: 0,
          right: 0,
          width: bracketSize,
          height: bracketSize,
          borderTopWidth: bw,
          borderRightWidth: bw,
          borderColor: color,
          borderTopRightRadius: 3,
        }}
      />
      {/* Bottom Left */}
      <View
        style={{
          position: 'absolute',
          bottom: 0,
          left: 0,
          width: bracketSize,
          height: bracketSize,
          borderBottomWidth: bw,
          borderLeftWidth: bw,
          borderColor: color,
          borderBottomLeftRadius: 3,
        }}
      />
      {/* Bottom Right */}
      <View
        style={{
          position: 'absolute',
          bottom: 0,
          right: 0,
          width: bracketSize,
          height: bracketSize,
          borderBottomWidth: bw,
          borderRightWidth: bw,
          borderColor: color,
          borderBottomRightRadius: 3,
        }}
      />
      {/* Center scan beam */}
      <View
        style={{
          width: size * 0.5,
          height: 2,
          backgroundColor: color,
          borderRadius: 1,
        }}
      />
    </View>
  );
}

// 8. Map Nav Icon
export function MapNavIcon({ size = 20, color = '#64748b' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Location Pin Head */}
      <View
        style={{
          width: size * 0.65,
          height: size * 0.65,
          borderRadius: (size * 0.65) / 2,
          borderWidth: 1.8,
          borderColor: color,
          alignItems: 'center',
          justifyContent: 'center',
          marginTop: -size * 0.15,
        }}
      >
        <View
          style={{
            width: size * 0.2,
            height: size * 0.2,
            borderRadius: (size * 0.2) / 2,
            backgroundColor: color,
          }}
        />
      </View>
      {/* Pin Pointer */}
      <View
        style={{
          width: 0,
          height: 0,
          borderLeftWidth: size * 0.15,
          borderRightWidth: size * 0.15,
          borderTopWidth: size * 0.28,
          borderLeftColor: 'transparent',
          borderRightColor: 'transparent',
          borderTopColor: color,
          marginTop: -1,
        }}
      />
    </View>
  );
}

// 9. Profile Nav Icon
export function ProfileNavIcon({ size = 20, color = '#64748b' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Head circle */}
      <View
        style={{
          width: size * 0.42,
          height: size * 0.42,
          borderRadius: (size * 0.42) / 2,
          borderWidth: 1.8,
          borderColor: color,
          marginBottom: 1.5,
        }}
      />
      {/* Shoulders / body arch */}
      <View
        style={{
          width: size * 0.72,
          height: size * 0.35,
          borderTopLeftRadius: size * 0.35,
          borderTopRightRadius: size * 0.35,
          borderWidth: 1.8,
          borderColor: color,
          borderBottomWidth: 0,
        }}
      />
    </View>
  );
}

// 10. Flashlight / Torch Icon
export function FlashlightIcon({ size = 22, color = '#ffffff', isOn = false }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Light Rays if ON */}
      {isOn && (
        <View
          style={{
            position: 'absolute',
            top: -2,
            width: size * 0.88,
            height: 3,
            backgroundColor: '#facc15',
            borderRadius: 1.5,
            shadowColor: '#facc15',
            shadowOffset: { width: 0, height: 0 },
            shadowOpacity: 1,
            shadowRadius: 6,
            elevation: 4,
          }}
        />
      )}
      {/* Torch Head / Reflector */}
      <View
        style={{
          width: size * 0.65,
          height: size * 0.28,
          borderTopLeftRadius: 3,
          borderTopRightRadius: 3,
          backgroundColor: isOn ? '#facc15' : color,
          marginBottom: 1.5,
        }}
      />
      {/* Torch Neck */}
      <View
        style={{
          width: size * 0.45,
          height: size * 0.15,
          backgroundColor: isOn ? '#eab308' : color,
          opacity: 0.9,
          marginBottom: 1,
        }}
      />
      {/* Torch Barrel / Body */}
      <View
        style={{
          width: size * 0.35,
          height: size * 0.45,
          borderBottomLeftRadius: 4,
          borderBottomRightRadius: 4,
          backgroundColor: color,
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        {/* Grip stripe */}
        <View
          style={{
            width: size * 0.22,
            height: 2,
            backgroundColor: isOn ? '#0f172a' : 'rgba(0,0,0,0.3)',
            borderRadius: 1,
          }}
        />
      </View>
    </View>
  );
}

// 11. Shutter / Manual Scan Button Icon
export function ShutterScanIcon({ size = 26, color = '#ffffff' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Outer target ring */}
      <View
        style={{
          width: size,
          height: size,
          borderRadius: size / 2,
          borderWidth: 2.2,
          borderColor: color,
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        {/* Inner solid shutter circle */}
        <View
          style={{
            width: size * 0.52,
            height: size * 0.52,
            borderRadius: (size * 0.52) / 2,
            backgroundColor: color,
          }}
        />
      </View>
    </View>
  );
}

// 12. Gallery / Image Library Icon
export function GalleryIcon({ size = 20, color = '#ffffff' }) {
  return (
    <View
      style={{
        width: size,
        height: size * 0.82,
        borderRadius: 4,
        borderWidth: 1.8,
        borderColor: color,
        overflow: 'hidden',
        position: 'relative',
      }}
    >
      {/* Sun dot */}
      <View
        style={{
          position: 'absolute',
          top: 2,
          left: 3,
          width: 4,
          height: 4,
          borderRadius: 2,
          backgroundColor: color,
        }}
      />
      {/* Large peak */}
      <View
        style={{
          position: 'absolute',
          bottom: 0,
          left: 1,
          width: 0,
          height: 0,
          borderLeftWidth: size * 0.26,
          borderRightWidth: size * 0.26,
          borderBottomWidth: size * 0.4,
          borderLeftColor: 'transparent',
          borderRightColor: 'transparent',
          borderBottomColor: color,
        }}
      />
      {/* Small peak */}
      <View
        style={{
          position: 'absolute',
          bottom: 0,
          right: 1,
          width: 0,
          height: 0,
          borderLeftWidth: size * 0.2,
          borderRightWidth: size * 0.2,
          borderBottomWidth: size * 0.28,
          borderLeftColor: 'transparent',
          borderRightColor: 'transparent',
          borderBottomColor: color,
        }}
      />
    </View>
  );
}

// 15. Location Target / Pin Icon for Map Search Bar
export function LocationTargetIcon({ size = 20, color = '#64748b' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: size * 0.7,
          height: size * 0.7,
          borderRadius: (size * 0.7) / 2,
          borderWidth: 2,
          borderColor: color,
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        <View
          style={{
            width: size * 0.22,
            height: size * 0.22,
            borderRadius: (size * 0.22) / 2,
            backgroundColor: color,
          }}
        />
      </View>
      <View
        style={{
          width: 0,
          height: 0,
          borderLeftWidth: size * 0.18,
          borderRightWidth: size * 0.18,
          borderTopWidth: size * 0.28,
          borderLeftColor: 'transparent',
          borderRightColor: 'transparent',
          borderTopColor: color,
          marginTop: -2,
        }}
      />
    </View>
  );
}

// 16. Filter Sliders / Tune Icon for Map Search Bar
export function FilterSlidersIcon({ size = 20, color = '#334155' }) {
  return (
    <View
      style={{
        width: size,
        height: size,
        flexDirection: 'row',
        justifyContent: 'space-around',
        alignItems: 'center',
      }}
    >
      {/* Left Slider Track */}
      <View
        style={{
          width: 2,
          height: size * 0.88,
          backgroundColor: color,
          borderRadius: 1,
          alignItems: 'center',
        }}
      >
        <View
          style={{
            position: 'absolute',
            top: size * 0.18,
            width: size * 0.36,
            height: size * 0.36,
            borderRadius: (size * 0.36) / 2,
            borderWidth: 2,
            borderColor: color,
            backgroundColor: '#ffffff',
          }}
        />
      </View>
      {/* Right Slider Track */}
      <View
        style={{
          width: 2,
          height: size * 0.88,
          backgroundColor: color,
          borderRadius: 1,
          alignItems: 'center',
        }}
      >
        <View
          style={{
            position: 'absolute',
            bottom: size * 0.18,
            width: size * 0.36,
            height: size * 0.36,
            borderRadius: (size * 0.36) / 2,
            borderWidth: 2,
            borderColor: color,
            backgroundColor: '#ffffff',
          }}
        />
      </View>
    </View>
  );
}

// 17. Wifi Off Icon for Map Offline State
export function WifiOffIcon({ size = 48, color = '#64748b' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Outer arc */}
      <View
        style={{
          width: size * 0.82,
          height: size * 0.82,
          borderRadius: (size * 0.82) / 2,
          borderWidth: 3,
          borderColor: color,
          borderBottomColor: 'transparent',
          borderLeftColor: 'transparent',
          transform: [{ rotate: '-45deg' }],
          opacity: 0.6,
        }}
      />
      {/* Middle arc */}
      <View
        style={{
          position: 'absolute',
          width: size * 0.52,
          height: size * 0.52,
          borderRadius: (size * 0.52) / 2,
          borderWidth: 2.5,
          borderColor: color,
          borderBottomColor: 'transparent',
          borderLeftColor: 'transparent',
          transform: [{ rotate: '-45deg' }],
          opacity: 0.8,
        }}
      />
      {/* Center dot */}
      <View
        style={{
          position: 'absolute',
          bottom: size * 0.2,
          width: size * 0.16,
          height: size * 0.16,
          borderRadius: (size * 0.16) / 2,
          backgroundColor: color,
        }}
      />
      {/* Red diagonal slash */}
      <View
        style={{
          position: 'absolute',
          width: size * 1.05,
          height: 3,
          backgroundColor: '#ef4444',
          borderRadius: 2,
          transform: [{ rotate: '-45deg' }],
        }}
      />
    </View>
  );
}

// 18. Back Arrow Icon
export function BackArrowIcon({ size = 20, color = '#0f172a' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: size * 0.44,
          height: size * 0.44,
          borderLeftWidth: 2.4,
          borderBottomWidth: 2.4,
          borderColor: color,
          transform: [{ rotate: '45deg' }],
          marginLeft: size * 0.12,
        }}
      />
    </View>
  );
}

// 19. Settings Gear Icon
export function SettingsGearIcon({ size = 22, color = '#0f172a' }) {
  const outerSize = size * 0.82;
  const innerHole = size * 0.32;
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* 4 Cross Cog Notches */}
      <View
        style={{
          position: 'absolute',
          width: size * 0.96,
          height: size * 0.26,
          backgroundColor: color,
          borderRadius: 2.5,
        }}
      />
      <View
        style={{
          position: 'absolute',
          width: size * 0.26,
          height: size * 0.96,
          backgroundColor: color,
          borderRadius: 2.5,
        }}
      />
      {/* Diagonal Notches */}
      <View
        style={{
          position: 'absolute',
          width: size * 0.96,
          height: size * 0.26,
          backgroundColor: color,
          borderRadius: 2.5,
          transform: [{ rotate: '45deg' }],
        }}
      />
      <View
        style={{
          position: 'absolute',
          width: size * 0.96,
          height: size * 0.26,
          backgroundColor: color,
          borderRadius: 2.5,
          transform: [{ rotate: '-45deg' }],
        }}
      />
      {/* Center Gear Ring */}
      <View
        style={{
          width: outerSize,
          height: outerSize,
          borderRadius: outerSize / 2,
          backgroundColor: color,
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        {/* Inner Hole */}
        <View
          style={{
            width: innerHole,
            height: innerHole,
            borderRadius: innerHole / 2,
            backgroundColor: '#ffffff',
          }}
        />
      </View>
    </View>
  );
}

// 20. Pencil Edit Icon
export function PencilEditIcon({ size = 13, color = '#0f172a' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: size * 0.3,
          height: size * 0.72,
          backgroundColor: color,
          borderRadius: 1.5,
          transform: [{ rotate: '45deg' }],
        }}
      />
    </View>
  );
}

// 21. Header Log Out Icon (Door with Arrow pointing right)
export function LogoutHeaderIcon({ size = 18, color = '#0f172a' }) {
  const w = size * 0.9;
  const h = size * 0.9;
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Door Frame: Left bracket */}
      <View
        style={{
          position: 'absolute',
          left: 1.5,
          width: w * 0.48,
          height: h,
          borderLeftWidth: 2,
          borderTopWidth: 2,
          borderBottomWidth: 2,
          borderColor: color,
          borderTopLeftRadius: 3,
          borderBottomLeftRadius: 3,
        }}
      />
      {/* Exit Arrow */}
      <View
        style={{
          position: 'absolute',
          right: 1,
          flexDirection: 'row',
          alignItems: 'center',
        }}
      >
        {/* Arrow Shaft */}
        <View
          style={{
            width: w * 0.54,
            height: 2,
            backgroundColor: color,
            borderRadius: 1,
          }}
        />
        {/* Arrow Head (pointing right) */}
        <View
          style={{
            width: w * 0.28,
            height: w * 0.28,
            borderTopWidth: 2,
            borderRightWidth: 2,
            borderColor: color,
            transform: [{ rotate: '45deg' }],
            marginLeft: -3,
          }}
        />
      </View>
    </View>
  );
}

// 22. Envelope / Mail Icon (Clean, sharp Email envelope)
export function EnvelopeMailIcon({ size = 20, color = '#475569' }) {
  const w = size * 0.92;
  const h = size * 0.68;
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: w,
          height: h,
          borderWidth: 1.8,
          borderColor: color,
          borderRadius: 3.5,
          overflow: 'hidden',
          alignItems: 'center',
          justifyContent: 'flex-start',
        }}
      >
        {/* Sharp V-Flap */}
        <View
          style={{
            width: w * 0.66,
            height: w * 0.66,
            borderBottomWidth: 1.8,
            borderRightWidth: 1.8,
            borderColor: color,
            transform: [{ rotate: '45deg' }],
            marginTop: -w * 0.44,
          }}
        />
      </View>
    </View>
  );
}

// 23. ID Badge / Card Icon (Authentic ID Card Badge with photo & info lines)
export function IdBadgeIcon({ size = 20, color = '#475569' }) {
  const w = size * 0.94;
  const h = size * 0.72;
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Top lanyard hole */}
      <View
        style={{
          width: 5,
          height: 2,
          backgroundColor: color,
          borderRadius: 1,
          marginBottom: 1,
        }}
      />
      {/* Card Body */}
      <View
        style={{
          width: w,
          height: h - 3,
          borderWidth: 1.8,
          borderColor: color,
          borderRadius: 3.5,
          paddingHorizontal: 2.5,
          flexDirection: 'row',
          alignItems: 'center',
          justifyContent: 'space-between',
        }}
      >
        {/* Silhouette Photo */}
        <View
          style={{
            width: (h - 3) * 0.62,
            height: (h - 3) * 0.68,
            borderRadius: 2,
            borderWidth: 1.2,
            borderColor: color,
            alignItems: 'center',
            justifyContent: 'center',
            overflow: 'hidden',
          }}
        >
          {/* Head */}
          <View
            style={{
              width: 3.5,
              height: 3.5,
              borderRadius: 2,
              backgroundColor: color,
              marginBottom: 1,
            }}
          />
          {/* Torso */}
          <View
            style={{
              width: 6,
              height: 2.5,
              borderTopLeftRadius: 3,
              borderTopRightRadius: 3,
              backgroundColor: color,
            }}
          />
        </View>
        {/* Info Lines */}
        <View style={{ flex: 1, marginLeft: 4, justifyContent: 'center', gap: 2.5 }}>
          <View style={{ width: '90%', height: 1.8, backgroundColor: color, borderRadius: 1 }} />
          <View style={{ width: '60%', height: 1.8, backgroundColor: color, borderRadius: 1 }} />
        </View>
      </View>
    </View>
  );
}

// 24. Role Shield / Badge Icon (Professional User Profile / Role silhouette)
export function RoleShieldIcon({ size = 20, color = '#475569' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* User Silhouette */}
      {/* Head */}
      <View
        style={{
          width: size * 0.38,
          height: size * 0.38,
          borderRadius: (size * 0.38) / 2,
          borderWidth: 1.8,
          borderColor: color,
          marginBottom: 1.5,
        }}
      />
      {/* Shoulders / Torso */}
      <View
        style={{
          width: size * 0.72,
          height: size * 0.36,
          borderTopLeftRadius: size * 0.36,
          borderTopRightRadius: size * 0.36,
          borderWidth: 1.8,
          borderColor: color,
          borderBottomWidth: 0,
        }}
      />
    </View>
  );
}

// 25. Calendar Joined Icon (Sharp Calendar with Top Rings & Grid)
export function CalendarJoinedIcon({ size = 20, color = '#475569' }) {
  const w = size * 0.88;
  const h = size * 0.82;
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Binder Rings */}
      <View
        style={{
          width: w * 0.62,
          flexDirection: 'row',
          justifyContent: 'space-between',
          marginBottom: -2.5,
          zIndex: 2,
        }}
      >
        <View style={{ width: 2, height: 4, backgroundColor: color, borderRadius: 1 }} />
        <View style={{ width: 2, height: 4, backgroundColor: color, borderRadius: 1 }} />
      </View>
      {/* Calendar Body */}
      <View
        style={{
          width: w,
          height: h,
          borderWidth: 1.8,
          borderColor: color,
          borderRadius: 3.5,
          overflow: 'hidden',
        }}
      >
        {/* Top Header Band */}
        <View style={{ width: '100%', height: 3.5, backgroundColor: color }} />
        {/* Grid Dates */}
        <View
          style={{
            flex: 1,
            paddingHorizontal: 2.5,
            paddingVertical: 2,
            justifyContent: 'space-around',
          }}
        >
          <View style={{ flexDirection: 'row', justifyContent: 'space-around' }}>
            <View style={{ width: 2, height: 2, borderRadius: 1, backgroundColor: color }} />
            <View style={{ width: 2, height: 2, borderRadius: 1, backgroundColor: color }} />
            <View style={{ width: 2, height: 2, borderRadius: 1, backgroundColor: color }} />
          </View>
          <View style={{ flexDirection: 'row', justifyContent: 'space-around' }}>
            <View style={{ width: 2, height: 2, borderRadius: 1, backgroundColor: color }} />
            <View style={{ width: 2, height: 2, borderRadius: 1, backgroundColor: color }} />
            <View style={{ width: 2, height: 2, borderRadius: 1, backgroundColor: color }} />
          </View>
        </View>
      </View>
    </View>
  );
}

// 25. Chevron Right Icon
export function ChevronRightIcon({ size = 16, color = '#cbd5e1' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: size * 0.42,
          height: size * 0.42,
          borderTopWidth: 2,
          borderRightWidth: 2,
          borderColor: color,
          transform: [{ rotate: '45deg' }],
          marginLeft: -size * 0.1,
        }}
      />
    </View>
  );
}

