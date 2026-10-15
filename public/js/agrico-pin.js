/**
 * AgriCo GIS - Unified Map Pin & Status Module
 * 
 * Provides a single shared factory for map pin icons across Website and Admin:
 * - Active / Approved / Verified: Red pin with green AgriCo logo (agrico-map-pin.png)
 * - Pending Review: Gray pin with grayscale logo (agrico-map-pin-gray.png)
 * - Rejected / Inactive: Dimmed gray pin (agrico-map-pin-gray.png with dimmed opacity)
 * 
 * Uniform dimensions across all maps:
 * - iconSize: [38, 56]
 * - iconAnchor: [19, 56]
 * - popupAnchor: [0, -50]
 */
(function (window) {
    'use strict';

    if (window.AgricoPin) {
        return;
    }

    const RED_PIN_URL = '/images/agrico-map-pin.png';
    const GRAY_PIN_URL = '/images/agrico-map-pin-gray.png';

    function normalizeStatus(status) {
        return String(status || '').toLowerCase().trim();
    }

    function getAgricoPinStatusMeta(status) {
        const s = normalizeStatus(status);
        const isApproved = s === 'active' || s === 'approved' || s === 'verified';
        const isRejected = s === 'rejected' || s === 'inactive' || s === 'disapproved';

        if (isApproved) {
            return {
                key: 'approved',
                badgeText: 'VERIFIED (PIN VISIBLE)',
                shortText: 'Verified',
                textColor: '#15803d',
                darkTextColor: '#4ade80',
                bgColor: '#dcfce7',
                darkBgColor: 'rgba(34, 197, 94, 0.15)',
                borderColor: '#bbf7d0',
                darkBorderColor: 'rgba(34, 197, 94, 0.3)',
                polyBorderColor: '#16a34a',
                polyFillColor: '#22c55e',
                polyHoverColor: '#15803d',
                polyOpacity: 0.22,
                isApproved: true,
                isPending: false,
                isRejected: false
            };
        }

        if (isRejected) {
            return {
                key: 'rejected',
                badgeText: 'NOT VISIBLE ON MAP',
                shortText: 'Not Visible',
                textColor: '#b91c1c',
                darkTextColor: '#f87171',
                bgColor: '#fee2e2',
                darkBgColor: 'rgba(239, 68, 68, 0.15)',
                borderColor: '#fecaca',
                darkBorderColor: 'rgba(239, 68, 68, 0.3)',
                polyBorderColor: '#6b7280',
                polyFillColor: '#9ca3af',
                polyHoverColor: '#4b5563',
                polyOpacity: 0.12,
                isApproved: false,
                isPending: false,
                isRejected: true
            };
        }

        return {
            key: 'pending',
            badgeText: 'PENDING APPROVAL',
            shortText: 'Pending Approval',
            textColor: '#b45309',
            darkTextColor: '#fbbf24',
            bgColor: '#fef3c7',
            darkBgColor: 'rgba(245, 158, 11, 0.15)',
            borderColor: '#fde68a',
            darkBorderColor: 'rgba(245, 158, 11, 0.3)',
            polyBorderColor: '#6b7280',
            polyFillColor: '#9ca3af',
            polyHoverColor: '#4b5563',
            polyOpacity: 0.18,
            isApproved: false,
            isPending: true,
            isRejected: false
        };
    }

    function createSoilPinIcon(status, options = {}) {
        if (typeof L === 'undefined') {
            console.warn('[AgricoPin] Leaflet (L) is not loaded yet.');
            return null;
        }

        const meta = getAgricoPinStatusMeta(status);
        const redUrl = options.redPinUrl || RED_PIN_URL;
        const grayUrl = options.grayPinUrl || GRAY_PIN_URL;

        const iconUrl = meta.isApproved ? redUrl : grayUrl;

        let extraClass = 'agrico-map-pin-marker';
        if (meta.isRejected) {
            extraClass += ' is-rejected-pin';
        } else if (meta.isPending) {
            extraClass += ' is-pending-pin';
        } else {
            extraClass += ' is-approved-pin';
        }

        if (options.className) {
            extraClass += ' ' + options.className;
        }

        return L.icon({
            iconUrl: iconUrl,
            iconSize: options.iconSize || [38, 56],
            iconAnchor: options.iconAnchor || [19, 56],
            popupAnchor: options.popupAnchor || [0, -50],
            className: extraClass
        });
    }

    window.AgricoPin = {
        RED_PIN_URL,
        GRAY_PIN_URL,
        getStatusMeta: getAgricoPinStatusMeta,
        createIcon: createSoilPinIcon
    };

    window.createSoilPinIcon = createSoilPinIcon;
    window.getAgricoPinStatusMeta = getAgricoPinStatusMeta;

})(window);
