/**
 * ImagePanZoom — drag-to-pan, scroll/pinch-to-zoom for a static image preview.
 * Works via CSS transform, so it plays nicely with any crop logic that reads
 * getBoundingClientRect() fresh at submit time.
 */
class ImagePanZoom {
    constructor(imgElement, containerElement, options = {}) {
        this.img = imgElement;
        this.container = containerElement;
        this.scale = 1;
        this.minScale = options.minScale ?? 1;
        this.maxScale = options.maxScale ?? 4;
        this.translateX = 0;
        this.translateY = 0;

        this._isDragging = false;
        this._startX = 0;
        this._startY = 0;
        this._lastTranslateX = 0;
        this._lastTranslateY = 0;
        this._initialPinchDistance = null;
        this._initialScale = 1;

        if (this.container) {
            this.container.style.overflow = 'hidden';
            this.container.style.touchAction = 'none';
        }
        if (this.img) {
            this.img.style.transformOrigin = 'center center';
            this.img.style.cursor = 'grab';
        }

        this._bindEvents();
    }

    _isActive() {
        return this.img && this.img.style.display !== 'none' && (this.img.src || this.img.naturalWidth > 0);
    }

    _bindEvents() {
        if (!this.container) return;

        this.container.addEventListener('mousedown', this._onDragStart.bind(this));
        window.addEventListener('mousemove', this._onDragMove.bind(this));
        window.addEventListener('mouseup', this._onDragEnd.bind(this));
        this.container.addEventListener('wheel', this._onWheel.bind(this), { passive: false });

        this.container.addEventListener('touchstart', this._onTouchStart.bind(this), { passive: false });
        this.container.addEventListener('touchmove', this._onTouchMove.bind(this), { passive: false });
        this.container.addEventListener('touchend', this._onTouchEnd.bind(this));
    }

    attachDragTarget(element) {
        if (!element || element._panZoomAttached) return;
        element._panZoomAttached = true;

        element.addEventListener('mousedown', (e) => {
            if (e.target && e.target.closest('.resize-handle')) return;
            this._onDragStart(e);
        });

        element.addEventListener('touchstart', (e) => {
            if (e.target && e.target.closest('.resize-handle')) return;
            this._onTouchStart(e);
        }, { passive: false });

        element.addEventListener('touchmove', (e) => {
            if (e.target && e.target.closest('.resize-handle')) return;
            this._onTouchMove(e);
        }, { passive: false });

        element.addEventListener('touchend', (e) => {
            if (e.target && e.target.closest('.resize-handle')) return;
            this._onTouchEnd(e);
        });

        element.addEventListener('wheel', this._onWheel.bind(this), { passive: false });
    }

    _onDragStart(e) {
        if (!this._isActive()) return;
        this._isDragging = true;
        this._startX = e.clientX;
        this._startY = e.clientY;
        this._lastTranslateX = this.translateX;
        this._lastTranslateY = this.translateY;
        if (this.img) this.img.style.cursor = 'grabbing';
    }

    _onDragMove(e) {
        if (!this._isDragging || !this._isActive()) return;
        this.translateX = this._lastTranslateX + (e.clientX - this._startX);
        this.translateY = this._lastTranslateY + (e.clientY - this._startY);
        this._clampTranslate();
        this._applyTransform();
    }

    _onDragEnd() {
        this._isDragging = false;
        if (this.img) this.img.style.cursor = 'grab';
    }

    _onWheel(e) {
        if (!this._isActive()) return;
        e.preventDefault();
        const delta = e.deltaY < 0 ? 0.15 : -0.15;
        this.zoomBy(delta);
    }

    _onTouchStart(e) {
        if (!this._isActive()) return;
        if (e.touches.length === 1) {
            this._isDragging = true;
            this._startX = e.touches[0].clientX;
            this._startY = e.touches[0].clientY;
            this._lastTranslateX = this.translateX;
            this._lastTranslateY = this.translateY;
        } else if (e.touches.length === 2) {
            this._isDragging = false;
            this._initialPinchDistance = this._pinchDistance(e.touches);
            this._initialScale = this.scale;
        }
    }

    _onTouchMove(e) {
        if (!this._isActive()) return;
        if (e.touches.length === 1 && this._isDragging) {
            e.preventDefault();
            this.translateX = this._lastTranslateX + (e.touches[0].clientX - this._startX);
            this.translateY = this._lastTranslateY + (e.touches[0].clientY - this._startY);
            this._clampTranslate();
            this._applyTransform();
        } else if (e.touches.length === 2 && this._initialPinchDistance) {
            e.preventDefault();
            const dist = this._pinchDistance(e.touches);
            const factor = dist / this._initialPinchDistance;
            this.scale = Math.min(this.maxScale, Math.max(this.minScale, this._initialScale * factor));
            this._clampTranslate();
            this._applyTransform();
        }
    }

    _onTouchEnd(e) {
        if (e.touches.length === 0) {
            this._isDragging = false;
            this._initialPinchDistance = null;
        }
    }

    _pinchDistance(touches) {
        const dx = touches[0].clientX - touches[1].clientX;
        const dy = touches[0].clientY - touches[1].clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }

    _clampTranslate() {
        if (!this.container) return;
        const containerRect = this.container.getBoundingClientRect();
        const width = containerRect.width || this.container.clientWidth || 0;
        const height = containerRect.height || this.container.clientHeight || 0;
        const maxOffsetX = Math.max(0, (width * (this.scale - 1)) / 2);
        const maxOffsetY = Math.max(0, (height * (this.scale - 1)) / 2);

        this.translateX = Math.max(-maxOffsetX, Math.min(maxOffsetX, this.translateX));
        this.translateY = Math.max(-maxOffsetY, Math.min(maxOffsetY, this.translateY));
    }

    zoomBy(delta) {
        this.scale = Math.min(this.maxScale, Math.max(this.minScale, this.scale + delta));
        // Snap back to center when returning to minScale
        if (this.scale <= this.minScale) {
            this.scale = this.minScale;
            this.translateX = 0;
            this.translateY = 0;
        }
        this._clampTranslate();
        this._applyTransform();
    }

    _applyTransform() {
        if (!this.img) return;
        this.img.style.transform =
            `translate(${this.translateX}px, ${this.translateY}px) scale(${this.scale})`;
    }

    reset() {
        this.scale = 1;
        this.translateX = 0;
        this.translateY = 0;
        this._applyTransform();
    }
}

/**
 * ResizableViewfinder — drag-to-resize square viewfinder box by its 4 corners.
 * Keeps box square, maintains opposite corner fixed, and clamps within container.
 */
class ResizableViewfinder {
    constructor(viewfinderEl, containerEl, options = {}) {
        this.viewfinder = viewfinderEl;
        this.container = containerEl;
        this.minSize = options.minSize ?? 60;
        this.defaultSize = options.defaultSize ?? 140;
        this.maxSize = options.maxSize ?? Math.min(containerEl?.clientWidth || 400, containerEl?.clientHeight || 400);

        this._activeHandle = null;
        this._startX = 0;
        this._startY = 0;
        this._startRect = null;

        this._lockInitialPosition();
        this._createHandles();
        this._bindEvents();
    }

    _lockInitialPosition() {
        if (!this.viewfinder || !this.container) return;
        const rect = this.viewfinder.getBoundingClientRect();
        const containerRect = this.container.getBoundingClientRect();

        if (rect.width > 0 && rect.height > 0 && containerRect.width > 0) {
            this.defaultSize = this.defaultSize ?? rect.width;
            const left = (typeof this.viewfinder.offsetLeft === 'number' && this.viewfinder.offsetLeft > 0)
                ? this.viewfinder.offsetLeft
                : (rect.left - containerRect.left);
            const top = (typeof this.viewfinder.offsetTop === 'number' && this.viewfinder.offsetTop > 0)
                ? this.viewfinder.offsetTop
                : (rect.top - containerRect.top);
            this.viewfinder.style.position = 'absolute';
            this.viewfinder.style.transform = 'none';
            this.viewfinder.style.margin = '0';
            this.viewfinder.style.right = 'auto';
            this.viewfinder.style.bottom = 'auto';
            this.viewfinder.style.left = `${left}px`;
            this.viewfinder.style.top = `${top}px`;
            this.viewfinder.style.width = `${rect.width}px`;
            this.viewfinder.style.height = `${rect.height}px`;
        } else {
            this.reset(this.defaultSize);
        }
    }

    _createHandles() {
        if (!this.viewfinder) return;
        const existing = this.viewfinder.querySelectorAll('.resize-handle');
        existing.forEach(el => el.remove());

        this.handles = {};
        ['nw', 'ne', 'sw', 'se', 'n', 's', 'e', 'w'].forEach(pos => {
            const handle = document.createElement('div');
            handle.className = `resize-handle resize-handle-${pos}`;
            handle.dataset.position = pos;
            this.viewfinder.appendChild(handle);
            this.handles[pos] = handle;
        });
    }

    _bindEvents() {
        if (!this.handles) return;
        Object.values(this.handles).forEach(handle => {
            handle.addEventListener('mousedown', this._onResizeStart.bind(this));
            handle.addEventListener('touchstart', this._onResizeStart.bind(this), { passive: false });
        });
        window.addEventListener('mousemove', this._onResizeMove.bind(this));
        window.addEventListener('touchmove', this._onResizeMove.bind(this), { passive: false });
        window.addEventListener('mouseup', this._onResizeEnd.bind(this));
        window.addEventListener('touchend', this._onResizeEnd.bind(this));
        window.addEventListener('touchcancel', this._onResizeEnd.bind(this));
    }

    _onResizeStart(e) {
        if (!this.viewfinder || this.viewfinder.style.display === 'none') return;
        const parent = this.viewfinder.parentElement;
        if (parent && (parent.style.display === 'none' || window.getComputedStyle(parent).display === 'none')) return;

        e.preventDefault();
        e.stopPropagation();   // don't let this also trigger image drag-to-pan
        this._activeHandle = e.currentTarget?.dataset?.position || e.target?.closest('.resize-handle')?.dataset?.position;
        if (!this._activeHandle) return;
        const point = e.touches ? e.touches[0] : e;
        this._startX = point.clientX;
        this._startY = point.clientY;

        const rect = this.viewfinder.getBoundingClientRect();
        const containerRect = this.container.getBoundingClientRect();
        const left = (typeof this.viewfinder.offsetLeft === 'number' && this.viewfinder.offsetLeft >= 0)
            ? this.viewfinder.offsetLeft
            : (rect.left - containerRect.left);
        const top = (typeof this.viewfinder.offsetTop === 'number' && this.viewfinder.offsetTop >= 0)
            ? this.viewfinder.offsetTop
            : (rect.top - containerRect.top);
        const width = this.viewfinder.offsetWidth || rect.width;
        const height = this.viewfinder.offsetHeight || rect.height;

        if (this.viewfinder.style.position !== 'absolute') {
            this.viewfinder.style.position = 'absolute';
            this.viewfinder.style.transform = 'none';
            this.viewfinder.style.margin = '0';
            this.viewfinder.style.left = `${left}px`;
            this.viewfinder.style.top = `${top}px`;
        }

        this._startRect = { left, top, width, height };
    }

    _onResizeMove(e) {
        if (!this._activeHandle || !this._startRect) return;
        e.preventDefault();
        e.stopPropagation();
        const point = e.touches ? e.touches[0] : e;
        const dx = point.clientX - this._startX;
        const dy = point.clientY - this._startY;
        const { left, top, width, height } = this._startRect;

        const containerRect = this.container.getBoundingClientRect();
        const cW = containerRect.width || this.container.clientWidth;
        const cH = containerRect.height || this.container.clientHeight;

        let maxAllowed = Math.min(cW, cH);
        if (this.maxSize) {
            maxAllowed = Math.min(maxAllowed, this.maxSize);
        }

        let delta;
        switch (this._activeHandle) {
            // Corners (square diagonal tracking)
            case 'se': delta = Math.abs(dx) > Math.abs(dy) ? dx : dy; break;
            case 'nw': delta = Math.abs(dx) > Math.abs(dy) ? -dx : -dy; break;
            case 'ne': delta = Math.abs(dx) > Math.abs(dy) ? dx : -dy; break;
            case 'sw': delta = Math.abs(dx) > Math.abs(dy) ? -dx : dy; break;

            // Edge Midpoints (single axis drag)
            case 'e':  delta = dx; break;
            case 'w':  delta = -dx; break;
            case 's':  delta = dy; break;
            case 'n':  delta = -dy; break;
        }

        // Boundary clamping to keep the square box strictly inside the container
        if (this._activeHandle === 'se') {
            maxAllowed = Math.min(maxAllowed, cW - left, cH - top);
        } else if (this._activeHandle === 'nw') {
            maxAllowed = Math.min(maxAllowed, left + width, top + height);
        } else if (this._activeHandle === 'ne') {
            maxAllowed = Math.min(maxAllowed, cW - left, top + height);
        } else if (this._activeHandle === 'sw') {
            maxAllowed = Math.min(maxAllowed, left + width, cH - top);
        } else if (this._activeHandle === 'e') {
            maxAllowed = Math.min(maxAllowed, cW - left, height + 2 * top, 2 * (cH - top) - height);
        } else if (this._activeHandle === 'w') {
            maxAllowed = Math.min(maxAllowed, left + width, height + 2 * top, 2 * (cH - top) - height);
        } else if (this._activeHandle === 's') {
            maxAllowed = Math.min(maxAllowed, cH - top, width + 2 * left, 2 * (cW - left) - width);
        } else if (this._activeHandle === 'n') {
            maxAllowed = Math.min(maxAllowed, top + height, width + 2 * left, 2 * (cW - left) - width);
        }

        let newSize = Math.max(this.minSize, Math.min(maxAllowed, width + delta));
        let newLeft = left;
        let newTop = top;

        switch (this._activeHandle) {
            // Corners: opposite corner stays anchored
            case 'se': {
                newLeft = left;
                newTop = top;
                break;
            }
            case 'nw': {
                newLeft = left + (width - newSize);
                newTop = top + (height - newSize);
                break;
            }
            case 'ne': {
                newLeft = left;
                newTop = top + (height - newSize);
                break;
            }
            case 'sw': {
                newLeft = left + (width - newSize);
                newTop = top;
                break;
            }

            // Edge Midpoints: opposite side stays anchored, perpendicular axis expands symmetrically
            case 'e': {
                newLeft = left;
                newTop = top - (newSize - height) / 2;
                break;
            }
            case 'w': {
                newLeft = left + (width - newSize);
                newTop = top - (newSize - height) / 2;
                break;
            }
            case 's': {
                newTop = top;
                newLeft = left - (newSize - width) / 2;
                break;
            }
            case 'n': {
                newTop = top + (height - newSize);
                newLeft = left - (newSize - width) / 2;
                break;
            }
        }

        this.viewfinder.style.width = `${newSize}px`;
        this.viewfinder.style.height = `${newSize}px`;
        this.viewfinder.style.left = `${newLeft}px`;
        this.viewfinder.style.top = `${newTop}px`;
    }

    _onResizeEnd() {
        this._activeHandle = null;
        this._startRect = null;
    }

    reset(defaultSize = null) {
        if (!this.viewfinder || !this.container) return;
        const size = defaultSize ?? this.defaultSize ?? 140;
        this.defaultSize = size;
        const containerRect = this.container.getBoundingClientRect();
        const containerW = containerRect.width || this.container.clientWidth || 320;
        const containerH = containerRect.height || this.container.clientHeight || 320;
        const left = Math.max(0, (containerW - size) / 2);
        const top = Math.max(0, (containerH - size) / 2);
        this.viewfinder.style.position = 'absolute';
        this.viewfinder.style.transform = 'none';
        this.viewfinder.style.margin = '0';
        this.viewfinder.style.right = 'auto';
        this.viewfinder.style.bottom = 'auto';
        this.viewfinder.style.width = `${size}px`;
        this.viewfinder.style.height = `${size}px`;
        this.viewfinder.style.left = `${left}px`;
        this.viewfinder.style.top = `${top}px`;
    }
}

if (typeof window !== 'undefined') {
    window.ImagePanZoom = ImagePanZoom;
    window.ResizableViewfinder = ResizableViewfinder;
}
