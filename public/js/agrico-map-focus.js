/**
 * AgriCo GIS - Unified Map Pin Focus & Viewport Management Module
 * 
 * Provides unified, smooth, responsive focus and auto-centering for map pins and popups:
 * - Dynamically calculates vertical offset so the popup AND pin are centered in the visible map
 * - Prevents popups from being clipped by headers, toolbars, or card edges
 * - Respects target zoom (default: 15 for Lal-lo parcel level) without zooming out if user is already closer
 * - Smooth flyTo / panTo with prefers-reduced-motion compliance
 * - Reset zoom on popup close (returns smoothly to previous overview view)
 * - Accessible keyboard navigation (Escape to close, focus management)
 */

(function (window) {
    'use strict';

    if (window.AgricoMapFocus) {
        return;
    }

    const AgricoMapFocus = {
        defaultZoom: 15,
        defaultPaddingTopLeft: [30, 30],
        defaultPaddingBottomRight: [30, 30],

        /**
         * Returns recommended Leaflet popup options for all Agrico maps
         */
        getPopupOptions: function(overrides) {
            return Object.assign({
                className: 'agrico-custom-popup',
                maxWidth: 320,
                minWidth: 260,
                autoPan: false, // Managed by AgricoMapFocus.focusPin for smooth flyTo
                closeButton: true,
                autoClose: true,
                closeOnEscapeKey: true
            }, overrides || {});
        },

        /**
         * Smoothly focuses the map on a pin and positions the map so the entire popup is visible and centered
         * 
         * @param {L.Map} map Leaflet map instance
         * @param {L.Marker} marker Leaflet marker instance
         * @param {Object} options Configuration overrides
         */
        focusPin: function(map, marker, options) {
            if (!map || !marker) return;

            options = options || {};
            const desiredZoom = typeof options.targetZoom === 'number' ? options.targetZoom : this.defaultZoom;
            const currentZoom = map.getZoom();
            const targetZoom = Math.max(currentZoom, desiredZoom);

            // Record previous overview view for "Reset zoom on close" if not already stored
            if (!map._agricoPrevView && !map._agricoIsNavigatingPins) {
                map._agricoPrevView = {
                    center: map.getCenter(),
                    zoom: currentZoom,
                    bounds: map._agricoOverviewBounds || null
                };
            }

            // Flag that we are intentionally focusing a pin (prevents accidental close-reset race condition)
            map._agricoIsNavigatingPins = true;
            clearTimeout(map._agricoNavTimer);
            map._agricoNavTimer = setTimeout(() => {
                map._agricoIsNavigatingPins = false;
            }, 850);

            // Open the popup if not already open
            if (!marker.isPopupOpen()) {
                marker.openPopup();
            }

            const popup = marker.getPopup();
            const popupEl = popup ? popup.getElement() : null;

            // Pin anchor offset is 50px above base.
            // Measure actual rendered height or default to typical ~270px card height
            const popupHeight = (popupEl && popupEl.offsetHeight > 50) ? popupEl.offsetHeight : 270;

            // Shift target point up by half the popup height + pin anchor offset so pin & popup sit centered
            const verticalOffset = Math.round((popupHeight / 2) + 38);

            const latlng = marker.getLatLng();
            const targetPoint = map.project(latlng, targetZoom).subtract([0, verticalOffset]);
            const targetLatLng = map.unproject(targetPoint, targetZoom);

            const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (currentZoom !== targetZoom) {
                map.flyTo(targetLatLng, targetZoom, {
                    animate: !reduceMotion,
                    duration: reduceMotion ? 0 : 0.65,
                    easeLinearity: 0.25
                });
            } else {
                map.panTo(targetLatLng, {
                    animate: !reduceMotion,
                    duration: reduceMotion ? 0 : 0.45,
                    easeLinearity: 0.25
                });
            }

            // Manage accessibility
            if (popupEl) {
                popupEl.setAttribute('tabindex', '-1');
                const closeBtn = popupEl.querySelector('.leaflet-popup-close-button');
                if (closeBtn) {
                    closeBtn.setAttribute('aria-label', 'Close popup');
                }
            }
        },

        /**
         * Attaches reset-on-close and keyboard accessibility listeners to a map
         * 
         * @param {L.Map} map Leaflet map instance
         * @param {Object} options Configuration overrides
         */
        attachMapListeners: function(map, options) {
            if (!map || map._agricoFocusListenersAttached) return;
            map._agricoFocusListenersAttached = true;

            options = options || {};
            const resetZoomOnClose = options.resetZoomOnClose !== false;

            if (resetZoomOnClose) {
                map.on('popupclose', function(e) {
                    // If switching directly to another pin, do not zoom out
                    if (map._agricoIsNavigatingPins) return;

                    clearTimeout(map._agricoCloseResetTimer);
                    map._agricoCloseResetTimer = setTimeout(() => {
                        // Check again if another popup was opened or if we are actively navigating
                        if (map._agricoIsNavigatingPins) return;

                        const prev = map._agricoPrevView;
                        if (prev) {
                            const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                            
                            if (prev.bounds && typeof prev.bounds.isValid === 'function' && prev.bounds.isValid()) {
                                map.fitBounds(prev.bounds, {
                                    padding: [40, 40],
                                    animate: !reduceMotion,
                                    duration: reduceMotion ? 0 : 0.6
                                });
                            } else if (Array.isArray(prev.bounds) && prev.bounds.length > 0) {
                                map.fitBounds(prev.bounds, {
                                    padding: [40, 40],
                                    animate: !reduceMotion,
                                    duration: reduceMotion ? 0 : 0.6
                                });
                            } else if (prev.center && typeof prev.zoom === 'number') {
                                map.flyTo(prev.center, prev.zoom, {
                                    animate: !reduceMotion,
                                    duration: reduceMotion ? 0 : 0.6
                                });
                            }
                            map._agricoPrevView = null;
                        }
                    }, 140);
                });
            }
        }
    };

    window.AgricoMapFocus = AgricoMapFocus;

})(window);
