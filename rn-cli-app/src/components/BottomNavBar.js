import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet, Platform } from 'react-native';
import {
  HomeNavIcon,
  MunsellNavIcon,
  ScanCenterIcon,
  ShutterScanIcon,
  MapNavIcon,
  ProfileNavIcon,
} from './AppIcons';

export default function BottomNavBar({ activeTab = 'Home', onTabPress }) {
  const isScanTab = activeTab === 'ScanSoil';

  return (
    <View style={styles.outerContainer} pointerEvents="box-none">
      {/* Main Rounded Navigation Bar */}
      <View style={styles.barCard}>
        {/* Tab 1: Home */}
        <TouchableOpacity
          style={styles.navItem}
          onPress={() => onTabPress('Home')}
          activeOpacity={0.7}
        >
          <HomeNavIcon
            size={22}
            color={activeTab === 'Home' ? '#22c55e' : '#64748b'}
          />
          <Text
            style={[
              styles.navLabel,
              activeTab === 'Home' && styles.navLabelActive,
            ]}
          >
            Home
          </Text>
        </TouchableOpacity>

        {/* Tab 2: Munsell Color Chart */}
        <TouchableOpacity
          style={styles.navItem}
          onPress={() => onTabPress('Munsell')}
          activeOpacity={0.7}
        >
          <MunsellNavIcon
            size={21}
            color={activeTab === 'Munsell' ? '#22c55e' : '#64748b'}
          />
          <Text
            style={[
              styles.navLabel,
              activeTab === 'Munsell' && styles.navLabelActive,
            ]}
          >
            Munsell
          </Text>
        </TouchableOpacity>

        {/* Center Spacer / Action Button Tap Zone */}
        <TouchableOpacity
          style={styles.centerSpacer}
          onPress={() => onTabPress('ScanSoil')}
          activeOpacity={0.85}
        />

        {/* Tab 4: Map */}
        <TouchableOpacity
          style={styles.navItem}
          onPress={() => onTabPress('Map')}
          activeOpacity={0.7}
        >
          <MapNavIcon
            size={21}
            color={activeTab === 'Map' ? '#22c55e' : '#64748b'}
          />
          <Text
            style={[
              styles.navLabel,
              activeTab === 'Map' && styles.navLabelActive,
            ]}
          >
            Map
          </Text>
        </TouchableOpacity>

        {/* Tab 5: Profile */}
        <TouchableOpacity
          style={styles.navItem}
          onPress={() => onTabPress('Profile')}
          activeOpacity={0.7}
        >
          <ProfileNavIcon
            size={21}
            color={activeTab === 'Profile' ? '#22c55e' : '#64748b'}
          />
          <Text
            style={[
              styles.navLabel,
              activeTab === 'Profile' && styles.navLabelActive,
            ]}
          >
            Profile
          </Text>
        </TouchableOpacity>
      </View>

      {/* Floating Center Action Button Dip & Button (rendered after barCard so it sits on top) */}
      <View style={styles.centerButtonWrapper} pointerEvents="box-none">
        {/* Soft Dip Cutout Ring */}
        <View style={styles.scoopBackdrop} />

        {/* Elevated Floating Button / Scan Shutter Trigger */}
        <TouchableOpacity
          style={[
            styles.floatingCenterBtn,
            isScanTab && styles.floatingCenterBtnScanActive,
          ]}
          onPress={() => onTabPress('ScanSoil')}
          activeOpacity={0.82}
          accessibilityLabel="Scan Soil"
        >
          {isScanTab ? (
            <ShutterScanIcon size={25} color="#ffffff" />
          ) : (
            <ScanCenterIcon size={24} color="#ffffff" />
          )}
        </TouchableOpacity>
        <Text
          style={[
            styles.centerBtnLabel,
            isScanTab && styles.centerBtnLabelActive,
          ]}
        >
          Scan Soil
        </Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  outerContainer: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    height: Platform.OS === 'ios' ? 104 : 100,
    justifyContent: 'flex-end',
    alignItems: 'center',
    zIndex: 99,
  },
  barCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-around',
    width: '100%',
    height: Platform.OS === 'ios' ? 76 : 68,
    backgroundColor: '#ffffff',
    borderTopLeftRadius: 28,
    borderTopRightRadius: 28,
    borderWidth: 1,
    borderBottomWidth: 0,
    borderColor: 'rgba(218, 241, 222, 0.95)',
    paddingHorizontal: 8,
    paddingBottom: Platform.OS === 'ios' ? 14 : 4,
    // Soft organic green-tinted shadow
    shadowColor: '#15803d',
    shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.1,
    shadowRadius: 16,
    elevation: 12,
  },
  navItem: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 4,
  },
  centerSpacer: {
    width: 64,
  },
  navLabel: {
    fontSize: 11,
    fontWeight: '600',
    color: '#64748b',
    marginTop: 3,
  },
  navLabelActive: {
    color: '#22c55e',
    fontWeight: '800',
  },
  centerButtonWrapper: {
    position: 'absolute',
    top: 6,
    left: 0,
    right: 0,
    alignItems: 'center',
    zIndex: 105,
    elevation: 20,
  },
  scoopBackdrop: {
    position: 'absolute',
    top: -4,
    width: 68,
    height: 68,
    borderRadius: 34,
    backgroundColor: '#f6faf7',
    borderWidth: 1,
    borderColor: 'rgba(218, 241, 222, 0.9)',
  },
  floatingCenterBtn: {
    width: 56,
    height: 56,
    borderRadius: 28,
    backgroundColor: '#22c55e',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#22c55e',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.4,
    shadowRadius: 12,
    elevation: 22,
    borderWidth: 3,
    borderColor: '#ffffff',
  },
  floatingCenterBtnScanActive: {
    backgroundColor: '#16a34a',
    borderColor: '#bbf7d0',
    borderWidth: 3.5,
    shadowColor: '#22c55e',
    shadowOpacity: 0.6,
    shadowRadius: 14,
    elevation: 12,
  },
  centerBtnLabel: {
    fontSize: 10,
    fontWeight: '800',
    color: '#166534',
    marginTop: 2,
    letterSpacing: -0.2,
  },
  centerBtnLabelActive: {
    color: '#15803d',
    fontWeight: '900',
  },
});
