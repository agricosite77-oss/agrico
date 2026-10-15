/**
 * AgriCo GIS - Lal-lo Boundary Module (Single Source of Truth)
 * 
 * Manages the official NAMRIA / PSA boundary dataset for Lal-lo, Cagayan:
 * - Singleton in-memory GeoJSON cache across maps and modals
 * - Consistent styling for Municipality (#22c55e) & 35 Barangays (#8EB69B)
 * - Custom Leaflet pane (zIndex 350) ensuring pins and popups remain on top
 * - High-speed zero-dependency Point-in-Polygon check (Ray-Casting)
 * - Calibrated centroids for all 35 official barangays
 */

(function (window) {
    'use strict';

    if (window.LalloBoundary) {
        return;
    }

    const LalloBoundary = {
        _cache: null,
        _fetchPromise: null,
        geoJsonUrl: '/geojson/lal-lo-with-barangays.geojson',

        // 35 Official Barangays of Lal-lo, Cagayan with calibrated centroids
        BARANGAYS_35: [
            { name: 'Abagao',                    lat: 18.2431, lng: 121.7410 },
            { name: 'Alaguia',                   lat: 18.1564, lng: 121.6358 },
            { name: 'Bagumbayan',                lat: 18.1924, lng: 121.6690 },
            { name: 'Bangag',                    lat: 18.1176, lng: 121.6632 },
            { name: 'Bical',                     lat: 18.2402, lng: 121.7289 },
            { name: 'Bicud',                     lat: 18.1283, lng: 121.5843 },
            { name: 'Binag',                     lat: 18.1813, lng: 121.6949 },
            { name: 'Cabayabasan (Capacuan)',     lat: 18.1129, lng: 121.5687 },
            { name: 'Cagoran',                   lat: 18.1880, lng: 121.6845 },
            { name: 'Cambong',                   lat: 18.1909, lng: 121.7133 },
            { name: 'Catayauan',                 lat: 18.1728, lng: 121.6610 },
            { name: 'Catugan',                   lat: 18.1659, lng: 121.6319 },
            { name: 'Centro (Pob.)',              lat: 18.2007, lng: 121.6647 },
            { name: 'Cullit',                    lat: 18.2408, lng: 121.7515 },
            { name: 'Dagupan',                   lat: 18.1776, lng: 121.7383 },
            { name: 'Dalaya',                    lat: 18.1746, lng: 121.6724 },
            { name: 'Fabrica',                   lat: 18.2012, lng: 121.6316 },
            { name: 'Fusina',                    lat: 18.2393, lng: 121.7091 },
            { name: 'Jurisdiction',              lat: 18.2245, lng: 121.6901 },
            { name: 'Lalafugan',                 lat: 18.1495, lng: 121.6142 },
            { name: 'Logac',                     lat: 18.1518, lng: 121.6103 },
            { name: 'Magallungon (Sta. Teresa)', lat: 18.2174, lng: 121.6942 },
            { name: 'Magapit',                   lat: 18.1224, lng: 121.6868 },
            { name: 'Malanao',                   lat: 18.1939, lng: 121.6285 },
            { name: 'Maxingal',                  lat: 18.2131, lng: 121.6832 },
            { name: 'Naguilian',                 lat: 18.2214, lng: 121.7724 },
            { name: 'Paranum',                   lat: 18.1595, lng: 121.6705 },
            { name: 'Rosario',                   lat: 18.2056, lng: 121.7405 },
            { name: 'San Antonio (Lafu)',         lat: 18.1832, lng: 121.6428 },
            { name: 'San Jose',                  lat: 18.2057, lng: 121.6638 },
            { name: 'San Juan',                  lat: 18.2349, lng: 121.7219 },
            { name: 'San Lorenzo',               lat: 18.1510, lng: 121.6434 },
            { name: 'San Mariano',               lat: 18.1297, lng: 121.7300 },
            { name: 'Santa Maria',               lat: 18.1505, lng: 121.6715 },
            { name: 'Tucalana',                  lat: 18.1805, lng: 121.6674 }
        ],

        // Unified Style Tokens
        styles: {
            municipality: {
                color: '#22c55e',
                weight: 2.5,
                opacity: 1,
                dashArray: null,
                fillColor: 'transparent',
                fillOpacity: 0
            },
            barangay: {
                color: '#8EB69B',
                weight: 1.2,
                opacity: 0.9,
                dashArray: '3, 4',
                fillColor: '#163832',
                fillOpacity: 0.12
            },
            barangayHover: {
                color: '#4ade80',
                weight: 2.0,
                opacity: 1,
                fillColor: '#163832',
                fillOpacity: 0.25
            }
        },

        /**
         * Fetch GeoJSON with singleton in-memory caching.
         * Only downloads over the network once per session.
         */
        fetchData: function () {
            if (this._cache) {
                return Promise.resolve(this._cache);
            }
            if (!this._fetchPromise) {
                this._fetchPromise = fetch(this.geoJsonUrl)
                    .then(function (res) {
                        if (!res.ok) throw new Error('Failed to load Lal-lo GeoJSON: ' + res.status);
                        return res.json();
                    })
                    .then(function (data) {
                        LalloBoundary._cache = data;
                        return data;
                    })
                    .catch(function (err) {
                        LalloBoundary._fetchPromise = null;
                        console.warn('[LalloBoundary] Load error:', err);
                        throw err;
                    });
            }
            return this._fetchPromise;
        },

        /**
         * Zero-dependency Ray-Casting algorithm for point-in-polygon.
         * Coordinates format: pt = [lng, lat], GeoJSON ring = [[lng, lat], ...]
         */
        pointInPolygon: function (lat, lng, geometry) {
            if (!geometry || !geometry.coordinates) return false;
            const pt = [lng, lat];
            const coords = geometry.coordinates;

            function raycast(ring) {
                let inside = false;
                for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
                    const xi = ring[i][0], yi = ring[i][1];
                    const xj = ring[j][0], yj = ring[j][1];
                    const intersect = ((yi > pt[1]) !== (yj > pt[1])) &&
                        (pt[0] < (xj - xi) * (pt[1] - yi) / (yj - yi) + xi);
                    if (intersect) inside = !inside;
                }
                return inside;
            }

            if (geometry.type === 'Polygon') {
                return raycast(coords[0]);
            } else if (geometry.type === 'MultiPolygon') {
                for (let k = 0; k < coords.length; k++) {
                    if (raycast(coords[k][0])) return true;
                }
            }
            return false;
        },

        /**
         * Check if a given (lat, lng) is inside Lal-lo, and if so, which barangay it belongs to.
         * Returns a Promise resolving to { inside: boolean, barangay: string|null, feature: object|null }
         */
        findBarangayAt: function (lat, lng) {
            const self = this;
            return this.fetchData().then(function (data) {
                if (!data || !data.features) return { inside: false, barangay: null, feature: null };

                let insideMuni = false;
                let muniFeature = null;

                for (let i = 0; i < data.features.length; i++) {
                    const f = data.features[i];
                    const isMuni = f.properties && (f.properties.isMunicipality || f.properties.admin_level === '6');
                    if (!isMuni) {
                        if (self.pointInPolygon(lat, lng, f.geometry)) {
                            const name = (f.properties && (f.properties.name || f.properties.adm4_name)) || null;
                            return { inside: true, barangay: name, feature: f };
                        }
                    } else {
                        if (self.pointInPolygon(lat, lng, f.geometry)) {
                            insideMuni = true;
                            muniFeature = f;
                        }
                    }
                }

                if (insideMuni) {
                    return { inside: true, barangay: 'Lal-lo Overall', feature: muniFeature };
                }

                return { inside: false, barangay: null, feature: null };
            });
        },

        /**
         * Render the Lal-lo boundary on any Leaflet map instance.
         * 
         * @param {L.Map} map - Leaflet map instance
         * @param {Object} options - Configuration options:
         *   - fitBounds: boolean (default: false)
         *   - maxBounds: boolean (default: false)
         *   - interactiveBarangays: boolean (default: true)
         *   - showTooltips: boolean (default: true)
         *   - onBarangayClick: function(e, feature, layer)
         *   - onReady: function(geoJsonLayer, data)
         *   - bringMarkersToFront: L.LayerGroup | L.Marker | null
         */
        render: function (map, options) {
            if (!map || typeof L === 'undefined') {
                return Promise.reject(new Error('Valid Leaflet map instance required.'));
            }

            options = options || {};
            const self = this;

            // Ensure custom pane at zIndex 350 so vector boundaries sit between tiles and pins
            const paneName = options.paneName || 'lalloBoundaryPane';
            if (!map.getPane(paneName)) {
                const pane = map.createPane(paneName);
                pane.style.zIndex = '350';
            }

            // Legal Attribution update: add official data source attribution
            if (options.addAttribution !== false && map.attributionControl) {
                map.attributionControl.addAttribution('Boundary: NAMRIA / PSA / OCHA');
            }

            return this.fetchData().then(function (data) {
                const geoJsonLayer = L.geoJSON(data, {
                    pane: paneName,
                    style: function (feature) {
                        const isMunicipality = feature.properties && (feature.properties.isMunicipality || feature.properties.admin_level === '6');
                        return Object.assign({}, isMunicipality ? self.styles.municipality : self.styles.barangay);
                    },
                    onEachFeature: function (feature, layer) {
                        const isMuni = feature.properties && (feature.properties.isMunicipality || feature.properties.admin_level === '6');
                        const name = feature.properties && (feature.properties.name || feature.properties.adm4_name);

                        // Municipality is visual boundary only (non-interactive to avoid capturing clicks)
                        if (isMuni) {
                            if (layer.getElement) {
                                const el = layer.getElement();
                                if (el) el.style.pointerEvents = 'none';
                            }
                            return;
                        }

                        // Tooltip on Barangays
                        if (options.showTooltips !== false && name) {
                            layer.bindTooltip(`<strong>${name}</strong> (Barangay)`, {
                                sticky: true,
                                direction: 'auto',
                                className: 'lallo-brgy-tooltip'
                            });
                        }

                        // Hover animations & click handlers for Barangays
                        if (options.interactiveBarangays !== false) {
                            layer.on({
                                mouseover: function (e) {
                                    const l = e.target;
                                    l.setStyle(self.styles.barangayHover);
                                    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                                        l.bringToFront();
                                    }
                                },
                                mouseout: function (e) {
                                    geoJsonLayer.resetStyle(e.target);
                                },
                                click: function (e) {
                                    if (typeof options.onBarangayClick === 'function') {
                                        options.onBarangayClick(e, feature, layer);
                                    }
                                }
                            });
                        }
                    }
                }).addTo(map);

                // Initial fit to municipality bounds if requested
                if (options.fitBounds) {
                    map.fitBounds(geoJsonLayer.getBounds(), { padding: [24, 24] });
                }

                // Lock pan within Lal-lo bounds if maxBounds is enabled
                if (options.maxBounds) {
                    const bounds = geoJsonLayer.getBounds();
                    const paddedBounds = bounds.pad(options.maxBoundsPadding !== undefined ? options.maxBoundsPadding : 0.08);
                    map.setMaxBounds(paddedBounds);
                    map.options.minZoom = options.minZoom || 10;
                }

                // Bring pin/markers groups to front if supplied
                if (options.bringMarkersToFront) {
                    if (typeof options.bringMarkersToFront.bringToFront === 'function') {
                        options.bringMarkersToFront.bringToFront();
                    } else if (Array.isArray(options.bringMarkersToFront)) {
                        options.bringMarkersToFront.forEach(function (m) {
                            if (m && typeof m.bringToFront === 'function') m.bringToFront();
                        });
                    }
                }

                if (typeof options.onReady === 'function') {
                    options.onReady(geoJsonLayer, data);
                }

                return geoJsonLayer;
            });
        }
    };

    // Inject unified tooltip styling
    if (typeof document !== 'undefined') {
        const styleId = 'lallo-boundary-tooltip-style';
        if (!document.getElementById(styleId)) {
            const style = document.createElement('style');
            style.id = styleId;
            style.textContent = `
                .lallo-brgy-tooltip {
                    background: rgba(11, 43, 38, 0.94) !important;
                    color: #DAF1DE !important;
                    border: 1px solid rgba(142, 182, 155, 0.45) !important;
                    border-radius: 6px !important;
                    padding: 4px 8px !important;
                    font-size: 11px !important;
                    font-weight: 500 !important;
                    box-shadow: 0 4px 14px rgba(0,0,0,0.4) !important;
                    backdrop-filter: blur(4px) !important;
                }
                .lallo-brgy-tooltip strong {
                    color: #ffffff !important;
                    font-weight: 700 !important;
                }
                .lallo-brgy-tooltip::before {
                    border-top-color: rgba(11, 43, 38, 0.94) !important;
                }
                html.light-mode .lallo-brgy-tooltip,
                html.light .lallo-brgy-tooltip {
                    background: rgba(255, 255, 255, 0.96) !important;
                    color: #163832 !important;
                    border: 1px solid rgba(34, 197, 94, 0.4) !important;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
                }
                html.light-mode .lallo-brgy-tooltip strong,
                html.light .lallo-brgy-tooltip strong {
                    color: #051F20 !important;
                }
                html.light-mode .lallo-brgy-tooltip::before,
                html.light .lallo-brgy-tooltip::before {
                    border-top-color: rgba(255, 255, 255, 0.96) !important;
                }
            `;
            document.head.appendChild(style);
        }
    }

    window.LalloBoundary = LalloBoundary;

})(window);
