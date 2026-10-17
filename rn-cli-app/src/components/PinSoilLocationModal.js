import React, { useState, useEffect, useRef, useMemo } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  Pressable,
  StyleSheet,
  Modal,
  ScrollView,
  Dimensions,
  ActivityIndicator,
  Alert,
  Platform,
  KeyboardAvoidingView,
  Image,
} from 'react-native';
import { WebView } from 'react-native-webview';
import NetInfo from '@react-native-community/netinfo';
import api from '../services/api';

const { width } = Dimensions.get('window');

// The 35 Official Barangays of Lal-lo, Cagayan with Calibrated Coordinates
const LALLO_35_BARANGAYS = [
  { name: 'Abagao', lat: 18.1650, lng: 121.6850 },
  { name: 'Alaguia', lat: 18.1520, lng: 121.6720 },
  { name: 'Bagumbayan', lat: 18.2102, lng: 121.6587 },
  { name: 'Bangag', lat: 18.2284, lng: 121.6701 },
  { name: 'Bical', lat: 18.1887, lng: 121.6743 },
  { name: 'Bicud', lat: 18.2350, lng: 121.6450 },
  { name: 'Binag', lat: 18.1720, lng: 121.6550 },
  { name: 'Cabayabasan (Capacuan)', lat: 18.2215, lng: 121.6845 },
  { name: 'Cagoran', lat: 18.1754, lng: 121.6421 },
  { name: 'Cambong', lat: 18.2420, lng: 121.6650 },
  { name: 'Catayauan', lat: 18.2155, lng: 121.6789 },
  { name: 'Catugan', lat: 18.2010, lng: 121.6420 },
  { name: 'Centro (Poblacion)', lat: 18.2048, lng: 121.6625 },
  { name: 'Cullit', lat: 18.1610, lng: 121.6620 },
  { name: 'Dagupan', lat: 18.1920, lng: 121.6510 },
  { name: 'Dalaya', lat: 18.2098, lng: 121.6712 },
  { name: 'Fabrica', lat: 18.2187, lng: 121.6523 },
  { name: 'Fusina', lat: 18.1865, lng: 121.6821 },
  { name: 'Jurisdiction', lat: 18.1990, lng: 121.6700 },
  { name: 'Lalafugan', lat: 18.2412, lng: 121.6754 },
  { name: 'Logac', lat: 18.2256, lng: 121.6498 },
  { name: 'Magallungon (Santa Teresa)', lat: 18.1843, lng: 121.6598 },
  { name: 'Magapit', lat: 18.1972, lng: 121.6612 },
  { name: 'Malanao', lat: 18.1901, lng: 121.6487 },
  { name: 'Maxingal', lat: 18.2023, lng: 121.6887 },
  { name: 'Naguilian', lat: 18.2145, lng: 121.6954 },
  { name: 'Paranum', lat: 18.1798, lng: 121.6678 },
  { name: 'Rosario', lat: 18.2200, lng: 121.6600 },
  { name: 'San Antonio (Lafu)', lat: 18.1680, lng: 121.6780 },
  { name: 'San Jose', lat: 18.2120, lng: 121.6740 },
  { name: 'San Juan', lat: 18.2260, lng: 121.6590 },
  { name: 'San Lorenzo', lat: 18.2312, lng: 121.6578 },
  { name: 'San Mariano', lat: 18.1965, lng: 121.6754 },
  { name: 'Santa Maria', lat: 18.2234, lng: 121.6687 },
  { name: 'Tucalana', lat: 18.2178, lng: 121.6654 },
];

// Lal-lo Overall Area Default Coordinates & Zoom (matches Website & Admin panel)
const LALLO_OVERVIEW_LAT = 18.1972;
const LALLO_OVERVIEW_LNG = 121.6612;
const LALLO_OVERVIEW_ZOOM = 12;

const COVERAGE_OPTIONS = [
  { label: '50 m²', radius: 4 },
  { label: '250 m²', radius: 9 },
  { label: '500 m²', radius: 13 },
  { label: '1,000 m²', radius: 18 },
  { label: '2,500 m²', radius: 28 },
  { label: '5,000 m²', radius: 40 },
  { label: '1 Hectare+', radius: 56 },
];

// Leaflet HTML Builder for Interactive Pinning
const buildPinMapHtml = (initialLat, initialLng, initialZoom = LALLO_OVERVIEW_ZOOM) => {
  return `
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body, #map {
      width: 100%;
      height: 100%;
      background-color: #e8f2f8;
      overflow: hidden;
    }
    .leaflet-control-attribution {
      font-size: 8px !important;
      background: rgba(255, 255, 255, 0.85) !important;
      padding: 1px 5px !important;
      border-radius: 4px;
    }
    .leaflet-control-zoom {
      display: none !important;
    }
    .sakop-circle {
      pointer-events: none;
    }
  </style>
</head>
<body>
  <div id="map"></div>
  <script>
    var map = L.map('map', {
      center: [${initialLat}, ${initialLng}],
      zoom: ${initialZoom},
      zoomControl: false,
      attributionControl: true
    });

    var mainTile = L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
      subdomains: ['0', '1', '2', '3'],
      maxZoom: 20,
      attribution: '&copy; Google Maps · AgriCo GIS'
    }).addTo(map);

    // Trigger invalidateSize after container finishes mounting
    setTimeout(function() { if (map) map.invalidateSize(); }, 200);
    setTimeout(function() { if (map) map.invalidateSize(); }, 500);
    setTimeout(function() { if (map) map.invalidateSize(); }, 1000);

    var coverageCircle = null;
    var currentRadius = 13; // default 500m2 approx meters radius

    function updateCoverageCircle(lat, lng, radiusMeters) {
      if (coverageCircle) {
        map.removeLayer(coverageCircle);
      }
      if (radiusMeters > 0) {
        coverageCircle = L.circle([lat, lng], {
          radius: radiusMeters,
          color: '#16a34a',
          weight: 2,
          fillColor: '#22c55e',
          fillOpacity: 0.22,
          className: 'sakop-circle'
        }).addTo(map);
      }
    }

    function emitLocation() {
      var center = map.getCenter();
      updateCoverageCircle(center.lat, center.lng, currentRadius);
      if (window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
        window.ReactNativeWebView.postMessage(JSON.stringify({
          type: 'MAP_MOVED',
          lat: center.lat,
          lng: center.lng,
          zoom: map.getZoom()
        }));
      }
    }

    map.on('move', emitLocation);
    map.on('moveend', emitLocation);

    // Initial circle render
    updateCoverageCircle(${initialLat}, ${initialLng}, currentRadius);

    // Handle incoming messages from React Native
    function handleIncomingMessage(event) {
      try {
        var data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
        if (data.type === 'PAN_TO') {
          map.flyTo([data.lat, data.lng], data.zoom || 16, { duration: 1.2 });
        } else if (data.type === 'SET_COVERAGE') {
          currentRadius = data.radiusMeters;
          var center = map.getCenter();
          updateCoverageCircle(center.lat, center.lng, currentRadius);
        } else if (data.type === 'INVALIDATE_SIZE') {
          map.invalidateSize();
        }
      } catch (e) {}
    }

    window.addEventListener('message', handleIncomingMessage);
    document.addEventListener('message', handleIncomingMessage);
  </script>
</body>
</html>
  `;
};

export default function PinSoilLocationModal({
  visible,
  onClose,
  specimen,
  onSuccess,
}) {
  const webViewRef = useRef(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedBarangay, setSelectedBarangay] = useState(null);
  const [currentCoords, setCurrentCoords] = useState({
    lat: LALLO_OVERVIEW_LAT,
    lng: LALLO_OVERVIEW_LNG,
  });
  const [selectedCoverage, setSelectedCoverage] = useState('500 m²');
  const [scanNote, setScanNote] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showDropdown, setShowDropdown] = useState(false);

  // Memoize static map HTML so it is never destroyed/reloaded on state change
  const memoizedPinMapHtml = useMemo(
    () => buildPinMapHtml(LALLO_OVERVIEW_LAT, LALLO_OVERVIEW_LNG, LALLO_OVERVIEW_ZOOM),
    []
  );

  // Filter only from the 35 Barangays of Lal-lo
  const filteredBarangays = useMemo(() => {
    if (!searchQuery.trim()) return LALLO_35_BARANGAYS;
    return LALLO_35_BARANGAYS.filter((b) =>
      b.name.toLowerCase().includes(searchQuery.toLowerCase())
    );
  }, [searchQuery]);

  // Set default specimen values when opening
  useEffect(() => {
    if (visible) {
      setSearchQuery('');
      setSelectedBarangay(null);
      setCurrentCoords({
        lat: LALLO_OVERVIEW_LAT,
        lng: LALLO_OVERVIEW_LNG,
      });
      setShowDropdown(false);

      if (specimen) {
        setScanNote(
          specimen.name
            ? `Soil sample classified as ${specimen.name} (${specimen.munsellCode || specimen.fullCode || 'Munsell'}).`
            : ''
        );
      }

      // Invalidate Leaflet map size after modal transition
      if (webViewRef.current) {
        setTimeout(() => {
          webViewRef.current?.postMessage(JSON.stringify({ type: 'INVALIDATE_SIZE' }));
        }, 350);
      }
    }
  }, [visible, specimen]);

  // Handle selecting a barangay from suggestions (auto-zooms & pans to barangay)
  const handleSelectBarangay = (barangay) => {
    setSelectedBarangay(barangay.name);
    setSearchQuery(barangay.name);
    setShowDropdown(false);
    setCurrentCoords({ lat: barangay.lat, lng: barangay.lng });

    // Send flyTo command with auto-zoom (zoom: 16) to Leaflet WebView
    if (webViewRef.current) {
      webViewRef.current.postMessage(
        JSON.stringify({
          type: 'PAN_TO',
          lat: barangay.lat,
          lng: barangay.lng,
          zoom: 16,
        })
      );
    }
  };

  // Handle setting covered area (Sakop)
  const handleSelectCoverage = (option) => {
    setSelectedCoverage(option.label);
    if (webViewRef.current) {
      webViewRef.current.postMessage(
        JSON.stringify({
          type: 'SET_COVERAGE',
          radiusMeters: option.radius,
        })
      );
    }
  };

  // Handle map center updates from Leaflet
  const handleMapMessage = (event) => {
    try {
      const data = JSON.parse(event.nativeEvent.data);
      if (data.type === 'MAP_MOVED') {
        setCurrentCoords({
          lat: parseFloat(data.lat.toFixed(6)),
          lng: parseFloat(data.lng.toFixed(6)),
        });
      }
    } catch (e) {
      // Ignored
    }
  };

  // Submit Pinned Soil Location for Admin Approval (Online-Only)
  const handleSubmitPin = async () => {
    // 1. Check Network Connectivity
    const netState = await NetInfo.fetch();
    if (!netState.isConnected) {
      Alert.alert(
        'Internet Connection Required',
        'You need an active internet connection to pin and submit soil coordinates for admin verification.',
        [{ text: 'OK', style: 'default' }]
      );
      return;
    }

    const finalBarangay = selectedBarangay || searchQuery.trim() || 'Lal-lo Overall';

    setIsSubmitting(true);

    try {
      const payload = {
        latitude: currentCoords.lat,
        longitude: currentCoords.lng,
        barangay: finalBarangay,
        soil_id: specimen?.soil_id || specimen?.soilId || null,
        soil_scan_id: specimen?.soil_scan_id || specimen?.scanId || null,
        scan_note: scanNote.trim() || `Soil sample pinned in Brgy. ${finalBarangay}, Lal-lo.`,
        coverage_area: selectedCoverage,
      };

      const response = await api.post('/locations/pin', payload);
      const resData = response.data;

      Alert.alert(
        'Location Submitted for Approval',
        `Your soil sample coordinates (${currentCoords.lat.toFixed(4)}, ${currentCoords.lng.toFixed(4)}) in Brgy. ${finalBarangay} [Sakop: ${selectedCoverage}] have been submitted to the Admin dashboard for approval!`,
        [
          {
            text: 'Great!',
            onPress: () => {
              if (onSuccess) onSuccess(resData?.data || payload);
              onClose();
            },
          },
        ]
      );
    } catch (error) {
      Alert.alert(
        'Submission Notice',
        error?.response?.data?.message ||
          'Failed to submit location coordinates. Please verify your connection and try again.'
      );
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Modal
      visible={visible}
      animationType="slide"
      transparent={true}
      onRequestClose={onClose}
    >
      <View style={styles.modalBackdrop}>
        <Pressable
          style={StyleSheet.absoluteFill}
          onPress={onClose}
        />
        <KeyboardAvoidingView
          behavior={Platform.OS === 'ios' ? 'padding' : undefined}
          style={styles.modalContainer}
          pointerEvents="auto"
        >
          {/* Top Drag Pill */}
          <View style={styles.dragPill} />

          {/* Modal Header */}
          <View style={styles.headerRow}>
            <View>
              <Text style={styles.headerTitle}>Pin Soil Location</Text>
              <Text style={styles.headerSubtitle}>
                Select Barangay & Pin Coordinates for Admin Review
              </Text>
            </View>
            <TouchableOpacity
              style={styles.closeBtn}
              onPress={onClose}
              activeOpacity={0.7}
              accessibilityLabel="Close pin location modal"
            >
              <Text style={styles.closeBtnIcon}>✕</Text>
            </TouchableOpacity>
          </View>

          <ScrollView
            contentContainerStyle={styles.scrollContent}
            showsVerticalScrollIndicator={false}
            keyboardShouldPersistTaps="handled"
          >
            {/* 1. Single Search Text Box at Top for Searching 35 Barangays */}
            <View style={styles.searchSection}>
              <Text style={styles.fieldLabel}>SEARCH BARANGAY (LAL-LO, CAGAYAN)</Text>
              <View style={styles.searchBarContainer}>
                {/* Left Pin Icon */}
                <View style={styles.searchLeftIcon}>
                  <Text style={{ fontSize: 16 }}>📍</Text>
                </View>

                <TextInput
                  style={styles.searchTextInput}
                  value={searchQuery}
                  onChangeText={(text) => {
                    setSearchQuery(text);
                    setShowDropdown(true);
                  }}
                  onFocus={() => setShowDropdown(true)}
                  placeholder="Search from 35 Barangays in Lal-lo..."
                  placeholderTextColor="#94a3b8"
                />

                {/* Right Action: Clear or Target Icon */}
                {searchQuery.length > 0 ? (
                  <TouchableOpacity
                    style={styles.clearBtn}
                    onPress={() => {
                      setSearchQuery('');
                      setSelectedBarangay(null);
                      setShowDropdown(true);
                      // Reset to Lal-lo Overview
                      if (webViewRef.current) {
                        webViewRef.current.postMessage(
                          JSON.stringify({
                            type: 'PAN_TO',
                            lat: LALLO_OVERVIEW_LAT,
                            lng: LALLO_OVERVIEW_LNG,
                            zoom: LALLO_OVERVIEW_ZOOM,
                          })
                        );
                      }
                    }}
                    activeOpacity={0.7}
                  >
                    <Text style={styles.clearBtnText}>✕</Text>
                  </TouchableOpacity>
                ) : (
                  <TouchableOpacity
                    style={styles.targetIconBadge}
                    activeOpacity={0.8}
                    onPress={() => setShowDropdown(!showDropdown)}
                  >
                    <View style={styles.targetInnerDot} />
                  </TouchableOpacity>
                )}
              </View>

              {/* Suggestions Dropdown (Limited to 35 Barangays of Lal-lo) */}
              {showDropdown && (
                <View style={styles.dropdownContainer}>
                  <View style={styles.dropdownHeaderRow}>
                    <Text style={styles.dropdownHeaderText}>
                      35 BARANGAYS OF LAL-LO ({filteredBarangays.length})
                    </Text>
                    <TouchableOpacity
                      onPress={() => setShowDropdown(false)}
                      activeOpacity={0.7}
                    >
                      <Text style={styles.dropdownCloseText}>Done</Text>
                    </TouchableOpacity>
                  </View>
                  <ScrollView
                    style={styles.dropdownScroll}
                    nestedScrollEnabled={true}
                    keyboardShouldPersistTaps="always"
                  >
                    {filteredBarangays.map((b) => {
                      const isSelected = selectedBarangay === b.name;
                      return (
                        <TouchableOpacity
                          key={b.name}
                          style={[
                            styles.dropdownItem,
                            isSelected && styles.dropdownItemActive,
                          ]}
                          onPress={() => handleSelectBarangay(b)}
                          activeOpacity={0.75}
                        >
                          <Text style={styles.dropdownItemPin}>📍</Text>
                          <Text
                            style={[
                              styles.dropdownItemText,
                              isSelected && styles.dropdownItemTextActive,
                            ]}
                          >
                            Brgy. {b.name}
                          </Text>
                          {isSelected && (
                            <Text style={styles.dropdownCheckmark}>✓</Text>
                          )}
                        </TouchableOpacity>
                      );
                    })}
                  </ScrollView>
                </View>
              )}

              {/* Quick Horizontal Chips for 35 Barangays */}
              {!showDropdown && (
                <View style={styles.chipsContainer}>
                  <ScrollView
                    horizontal
                    showsHorizontalScrollIndicator={false}
                    contentContainerStyle={styles.chipsRow}
                    keyboardShouldPersistTaps="always"
                  >
                    {LALLO_35_BARANGAYS.map((b) => (
                      <TouchableOpacity
                        key={b.name}
                        style={[
                          styles.brgyChip,
                          selectedBarangay === b.name && styles.brgyChipActive,
                        ]}
                        onPress={() => handleSelectBarangay(b)}
                        activeOpacity={0.75}
                      >
                        <Text
                          style={[
                            styles.brgyChipText,
                            selectedBarangay === b.name && styles.brgyChipTextActive,
                          ]}
                        >
                          {b.name}
                        </Text>
                      </TouchableOpacity>
                    ))}
                  </ScrollView>
                </View>
              )}
            </View>

            {/* 2. Interactive Map with Website/Admin Pin Design & Drag/Pan */}
            <View style={styles.mapCard}>
              <View style={styles.mapHeaderRow}>
                <Text style={styles.mapHeaderLabel}>
                  📍 Drag map freely to pinpoint exact sample site
                </Text>
                <View style={styles.liveCoordPill}>
                  <Text style={styles.liveCoordText}>
                    {currentCoords.lat.toFixed(4)}, {currentCoords.lng.toFixed(4)}
                  </Text>
                </View>
              </View>

              <View style={styles.mapFrame}>
                <WebView
                  ref={webViewRef}
                  originWhitelist={['*']}
                  source={{
                    html: memoizedPinMapHtml,
                    baseUrl: 'https://cartocdn.com',
                  }}
                  userAgent="Mozilla/5.0 (Linux; Android 13; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36 AgriCo/1.0"
                  style={styles.mapWebView}
                  javaScriptEnabled={true}
                  domStorageEnabled={true}
                  mixedContentMode="always"
                  allowsInlineMediaPlayback={true}
                  onMessage={handleMapMessage}
                  scrollEnabled={false}
                />

                {/* Fixed Center Pin Overlay (Exact Website & Admin Panel Pin Design) */}
                <View style={styles.centerPinContainer} pointerEvents="none">
                  <Image
                    source={require('../assets/agrico-map-pin.png')}
                    style={styles.adminPinImage}
                    resizeMode="contain"
                  />
                  {/* Pin Contact Shadow */}
                  <View style={styles.pinContactShadow} />
                </View>
              </View>
            </View>

            {/* 3. Soil Covered Area (Sakop) Selector */}
            <View style={styles.sectionBlock}>
              <View style={styles.sectionTitleRow}>
                <Text style={styles.fieldLabel}>SOIL COVERED AREA (SAKOP)</Text>
                <Text style={styles.fieldHelper}>Selected: {selectedCoverage}</Text>
              </View>
              <ScrollView
                horizontal
                showsHorizontalScrollIndicator={false}
                contentContainerStyle={styles.coverageRow}
              >
                {COVERAGE_OPTIONS.map((opt) => (
                  <TouchableOpacity
                    key={opt.label}
                    style={[
                      styles.coveragePill,
                      selectedCoverage === opt.label && styles.coveragePillActive,
                    ]}
                    onPress={() => handleSelectCoverage(opt)}
                    activeOpacity={0.8}
                  >
                    <Text
                      style={[
                        styles.coveragePillText,
                        selectedCoverage === opt.label && styles.coveragePillTextActive,
                      ]}
                    >
                      {opt.label}
                    </Text>
                  </TouchableOpacity>
                ))}
              </ScrollView>
            </View>

            {/* 4. Scan Note Textbox */}
            <View style={styles.sectionBlock}>
              <Text style={styles.fieldLabel}>SCAN NOTE</Text>
              <TextInput
                style={styles.notesTextInput}
                value={scanNote}
                onChangeText={setScanNote}
                placeholder="Enter soil sampling notes, crop conditions, moisture, or landmarks..."
                placeholderTextColor="#94a3b8"
                multiline={true}
                numberOfLines={3}
                textAlignVertical="top"
              />
            </View>

            {/* 5. Submit Button for Admin Approval */}
            <TouchableOpacity
              style={[
                styles.submitBtn,
                isSubmitting && { opacity: 0.8 },
              ]}
              onPress={handleSubmitPin}
              disabled={isSubmitting}
              activeOpacity={0.88}
            >
              {isSubmitting ? (
                <ActivityIndicator color="#ffffff" size="small" />
              ) : (
                <Text style={styles.submitBtnText}>
                  Submit for Admin Approval 📍
                </Text>
              )}
            </TouchableOpacity>

            <Text style={styles.onlineNoticeText}>
              🟢 Online verification enabled · Pinned location sent for Admin review
            </Text>
          </ScrollView>
        </KeyboardAvoidingView>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  modalBackdrop: {
    flex: 1,
    backgroundColor: 'rgba(5, 31, 32, 0.75)',
    justifyContent: 'flex-end',
  },
  modalBackdropTapArea: {
    flex: 1,
  },
  modalContainer: {
    backgroundColor: '#ffffff',
    borderTopLeftRadius: 28,
    borderTopRightRadius: 28,
    height: '92%',
    paddingTop: 10,
    paddingHorizontal: 18,
    paddingBottom: Platform.OS === 'ios' ? 34 : 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -6 },
    shadowOpacity: 0.2,
    shadowRadius: 18,
    elevation: 25,
    zIndex: 10,
  },
  dragPill: {
    width: 44,
    height: 4.5,
    backgroundColor: '#cbd5e1',
    borderRadius: 3,
    alignSelf: 'center',
    marginBottom: 10,
  },
  headerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: '800',
    color: '#0f172a',
    letterSpacing: -0.2,
  },
  headerSubtitle: {
    fontSize: 12,
    fontWeight: '600',
    color: '#64748b',
    marginTop: 2,
  },
  closeBtn: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#f1f5f9',
    alignItems: 'center',
    justifyContent: 'center',
  },
  closeBtnIcon: {
    fontSize: 14,
    fontWeight: '700',
    color: '#475569',
  },
  scrollContent: {
    paddingBottom: 16,
  },

  // 1. Single Search Section (35 Barangays of Lal-lo)
  searchSection: {
    marginBottom: 14,
  },
  fieldLabel: {
    fontSize: 11,
    fontWeight: '800',
    color: '#475569',
    textTransform: 'uppercase',
    letterSpacing: 0.6,
    marginBottom: 6,
  },
  fieldHelper: {
    fontSize: 11,
    fontWeight: '600',
    color: '#16a34a',
  },
  searchBarContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f8fafc',
    borderWidth: 1.4,
    borderColor: '#e2e8f0',
    borderRadius: 16,
    paddingHorizontal: 12,
    paddingVertical: Platform.OS === 'ios' ? 10 : 4,
  },
  searchLeftIcon: {
    marginRight: 6,
  },
  searchTextInput: {
    flex: 1,
    fontSize: 13.5,
    fontWeight: '700',
    color: '#0f172a',
    paddingVertical: 6,
  },
  clearBtn: {
    width: 24,
    height: 24,
    borderRadius: 12,
    backgroundColor: '#e2e8f0',
    alignItems: 'center',
    justifyContent: 'center',
    marginLeft: 6,
  },
  clearBtnText: {
    fontSize: 11,
    fontWeight: '700',
    color: '#475569',
  },
  targetIconBadge: {
    width: 26,
    height: 26,
    borderRadius: 13,
    backgroundColor: '#0f172a',
    alignItems: 'center',
    justifyContent: 'center',
    marginLeft: 6,
  },
  targetInnerDot: {
    width: 7,
    height: 7,
    borderRadius: 3.5,
    backgroundColor: '#ffffff',
  },

  // Dropdown Suggestion List (35 Barangays)
  dropdownContainer: {
    marginTop: 6,
    backgroundColor: '#ffffff',
    borderRadius: 16,
    borderWidth: 1.2,
    borderColor: '#e2e8f0',
    maxHeight: 180,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 10,
    elevation: 8,
    overflow: 'hidden',
  },
  dropdownHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 6,
    backgroundColor: '#f8fafc',
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
  },
  dropdownHeaderText: {
    fontSize: 10,
    fontWeight: '800',
    color: '#94a3b8',
    letterSpacing: 0.5,
  },
  dropdownCloseText: {
    fontSize: 11,
    fontWeight: '700',
    color: '#16a34a',
  },
  dropdownScroll: {
    maxHeight: 140,
  },
  dropdownItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 9,
    borderBottomWidth: 1,
    borderBottomColor: '#f8fafc',
  },
  dropdownItemActive: {
    backgroundColor: '#ecfdf5',
  },
  dropdownItemPin: {
    fontSize: 13,
    marginRight: 8,
  },
  dropdownItemText: {
    fontSize: 13,
    fontWeight: '600',
    color: '#334155',
    flex: 1,
  },
  dropdownItemTextActive: {
    fontWeight: '800',
    color: '#047857',
  },
  dropdownCheckmark: {
    fontSize: 13,
    fontWeight: '800',
    color: '#16a34a',
    marginLeft: 6,
  },

  // Quick Chips
  chipsContainer: {
    marginTop: 8,
  },
  chipsRow: {
    flexDirection: 'row',
    gap: 6,
    paddingRight: 10,
  },
  brgyChip: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    backgroundColor: '#f8fafc',
    borderRadius: 20,
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  brgyChipActive: {
    backgroundColor: '#16a34a',
    borderColor: '#16a34a',
  },
  brgyChipText: {
    fontSize: 12,
    fontWeight: '700',
    color: '#334155',
  },
  brgyChipTextActive: {
    color: '#ffffff',
  },

  // 2. Map Card
  mapCard: {
    backgroundColor: '#f8fafc',
    borderRadius: 20,
    borderWidth: 1.4,
    borderColor: '#e2e8f0',
    overflow: 'hidden',
    marginBottom: 14,
  },
  mapHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 8,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
  },
  mapHeaderLabel: {
    fontSize: 11,
    fontWeight: '700',
    color: '#334155',
    flex: 1,
  },
  liveCoordPill: {
    backgroundColor: '#ecfdf5',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#a7f3d0',
  },
  liveCoordText: {
    fontSize: 10.5,
    fontWeight: '800',
    color: '#047857',
  },
  mapFrame: {
    height: 210,
    width: '100%',
    position: 'relative',
    backgroundColor: '#e8f2f8',
  },
  mapWebView: {
    flex: 1,
    backgroundColor: 'transparent',
  },

  // Fixed Center Pin Overlay (Exact Website / Admin Panel Pin Icon)
  centerPinContainer: {
    position: 'absolute',
    top: '50%',
    left: '50%',
    width: 38,
    height: 56,
    marginLeft: -19,
    marginTop: -54, // tip touches exact center point
    alignItems: 'center',
    justifyContent: 'flex-start',
    zIndex: 99,
  },
  adminPinImage: {
    width: 38,
    height: 52,
  },
  pinContactShadow: {
    width: 14,
    height: 4,
    borderRadius: 7,
    backgroundColor: 'rgba(0, 0, 0, 0.35)',
    marginTop: -2,
  },

  // 3. Covered Area (Sakop)
  sectionBlock: {
    marginBottom: 14,
  },
  sectionTitleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  coverageRow: {
    flexDirection: 'row',
    gap: 8,
    paddingVertical: 2,
  },
  coveragePill: {
    paddingHorizontal: 14,
    paddingVertical: 8,
    backgroundColor: '#f8fafc',
    borderRadius: 14,
    borderWidth: 1.4,
    borderColor: '#e2e8f0',
  },
  coveragePillActive: {
    backgroundColor: '#16a34a',
    borderColor: '#16a34a',
  },
  coveragePillText: {
    fontSize: 12.5,
    fontWeight: '700',
    color: '#475569',
  },
  coveragePillTextActive: {
    color: '#ffffff',
  },

  // 4. Scan Notes
  notesTextInput: {
    backgroundColor: '#f8fafc',
    borderWidth: 1.4,
    borderColor: '#e2e8f0',
    borderRadius: 16,
    paddingHorizontal: 14,
    paddingVertical: 10,
    fontSize: 13,
    fontWeight: '600',
    color: '#0f172a',
    minHeight: 70,
  },

  // 5. Submit Button
  submitBtn: {
    backgroundColor: '#16a34a',
    paddingVertical: 14,
    borderRadius: 18,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 4,
    shadowColor: '#16a34a',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.28,
    shadowRadius: 10,
    elevation: 6,
  },
  submitBtnText: {
    color: '#ffffff',
    fontSize: 15,
    fontWeight: '800',
    letterSpacing: 0.2,
  },
  onlineNoticeText: {
    fontSize: 11,
    fontWeight: '600',
    color: '#64748b',
    textAlign: 'center',
    marginTop: 10,
  },
});
