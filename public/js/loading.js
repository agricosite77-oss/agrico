/**
 * AgriCo Website Loading Screen — Production Ready Controller
 * 
 * Choreographs the 10.5 - 11.0 second brand reveal timeline:
 * - 0.0s – 1.5s: Initial Logo Reveal (clean white)
 * - 1.5s – 3.0s: Curved Green Wave Expansion (vibrant AgriCo green)
 * - 3.0s – 4.0s: Centered Logo Scale Transformation
 * - 4.0s – 6.5s: AgriCo Wordmark Reveal ([Logo] AgriCo centered lockup)
 * - 6.5s – 8.0s: Brand Hold
 * - 8.0s – 9.5s: Curved Green Retraction (revealing white, wordmark turns green)
 * - 9.5s – 10.5s: Final State Hold
 * - 10.5s – 11.0s: Smooth Exit into Website
 */

(function (window, document) {
    'use strict';

    // Timeline Configuration (milliseconds)
    var TIMELINE = {
        LOGO_REVEAL:      50,     // 0.05s: Initial logo appears centered on white
        GREEN_EXPAND:     1500,   // 1.5s: Organic green wave begins upward expansion
        LOGO_SCALE:       3000,   // 3.0s: Screen filled green; logo scales to 0.70
        WORDMARK_REVEAL:  4000,   // 4.0s: Wordmark "AgriCo" reveals next to logo
        BRAND_HOLD:       6500,   // 6.5s: Full lockup holds on green screen
        GREEN_RETRACT:    8000,   // 8.0s: Green wave retracts downward revealing white
        FINAL_STATE:      9500,   // 9.5s: Clean white screen; final brand hold
        EXIT_START:       10500,  // 10.5s: Loading screen starts fading out
        COMPLETE:         11000   // 11.0s: Loader hidden, interaction enabled
    };

    var loaderElement = null;
    var timerIds = [];
    var isWindowLoaded = false;
    var isAnimationComplete = false;

    /**
     * Check if user prefers reduced motion
     */
    function prefersReducedMotion() {
        return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    /**
     * Clear all scheduled timeline timers
     */
    function clearTimers() {
        for (var i = 0; i < timerIds.length; i++) {
            clearTimeout(timerIds[i]);
        }
        timerIds = [];
    }

    /**
     * Dispatch custom event with stage details
     */
    function dispatchStageEvent(stageName, phaseNumber) {
        try {
            var event = new CustomEvent('agrico:loading:phase', {
                detail: { phase: phaseNumber, name: stageName, timestamp: Date.now() }
            });
            window.dispatchEvent(event);
        } catch (e) {
            // Graceful fallback for older environments
        }
    }

    /**
     * Complete the loading transition and reveal the website
     */
    function completeLoader() {
        if (isAnimationComplete) return;
        isAnimationComplete = true;
        clearTimers();

        if (loaderElement) {
            loaderElement.classList.add('loader-hidden');
            loaderElement.setAttribute('aria-hidden', 'true');
        }

        document.body.classList.add('loaded');

        try {
            window.dispatchEvent(new CustomEvent('agrico:loading:complete', {
                detail: { timestamp: Date.now() }
            }));
        } catch (e) {}

        // Remove from DOM accessibility tree after CSS transition completes
        setTimeout(function () {
            if (loaderElement) {
                loaderElement.style.display = 'none';
            }
        }, 600);
    }

    /**
     * Begin the coordinated brand reveal timeline
     */
    function startTimeline() {
        if (!loaderElement) return;

        // Immediate check for prefers-reduced-motion
        if (prefersReducedMotion()) {
            loaderElement.classList.add('is-logo-revealed', 'is-logo-scaled', 'is-wordmark-revealed', 'is-wordmark-green');
            setTimeout(completeLoader, 400);
            return;
        }

        try {
            window.dispatchEvent(new CustomEvent('agrico:loading:start', {
                detail: { timestamp: Date.now() }
            }));
        } catch (e) {}

        // Phase 1: 0.0s – 1.5s Initial Logo Reveal (Center on White)
        timerIds.push(setTimeout(function () {
            if (!loaderElement) return;
            loaderElement.classList.add('is-logo-revealed');
            dispatchStageEvent('logo-reveal', 1);
        }, TIMELINE.LOGO_REVEAL));

        // Phase 2: 1.5s – 3.0s Curved Green Wave Expands to Fullscreen
        timerIds.push(setTimeout(function () {
            if (!loaderElement) return;
            loaderElement.classList.add('is-green-expanded');
            dispatchStageEvent('green-expanded', 2);
        }, TIMELINE.GREEN_EXPAND));

        // Phase 3: 3.0s – 4.0s Centered Logo Scale Transformation
        timerIds.push(setTimeout(function () {
            if (!loaderElement) return;
            loaderElement.classList.add('is-logo-scaled');
            dispatchStageEvent('logo-scaled', 3);
        }, TIMELINE.LOGO_SCALE));

        // Phase 4: 4.0s – 6.5s AgriCo Wordmark Reveal ([Logo] AgriCo Lockup)
        timerIds.push(setTimeout(function () {
            if (!loaderElement) return;
            loaderElement.classList.add('is-wordmark-revealed');
            dispatchStageEvent('wordmark-revealed', 4);
        }, TIMELINE.WORDMARK_REVEAL));

        // Phase 5: 6.5s – 8.0s Brand Hold on Vibrant Green
        timerIds.push(setTimeout(function () {
            if (!loaderElement) return;
            loaderElement.classList.add('is-brand-holding');
            dispatchStageEvent('brand-hold', 5);
        }, TIMELINE.BRAND_HOLD));

        // Phase 6: 8.0s – 9.5s Curved Green Retraction (Reveals White, Wordmark turns Green)
        timerIds.push(setTimeout(function () {
            if (!loaderElement) return;
            loaderElement.classList.remove('is-brand-holding');
            loaderElement.classList.remove('is-green-expanded');
            loaderElement.classList.add('is-green-retracted');
            loaderElement.classList.add('is-wordmark-green');
            dispatchStageEvent('green-retracted', 6);
        }, TIMELINE.GREEN_RETRACT));

        // Phase 7: 9.5s – 10.5s Final Logo State Hold on Pure White
        timerIds.push(setTimeout(function () {
            if (!loaderElement) return;
            dispatchStageEvent('final-state', 7);
        }, TIMELINE.FINAL_STATE));

        // Phase 8: 10.5s – 11.0s Exit Transition to Website
        timerIds.push(setTimeout(function () {
            if (!loaderElement) return;
            dispatchStageEvent('exit-start', 8);

            // Wait until window.load has fired, or proceed if already loaded
            if (isWindowLoaded) {
                completeLoader();
            } else {
                // If page is still loading, wait up to a safety threshold
                window.addEventListener('load', completeLoader, { once: true });
                // Fallback max wait 3 seconds
                setTimeout(completeLoader, 3000);
            }
        }, TIMELINE.EXIT_START));
    }

    /**
     * Initialization routine
     */
    function initLoader() {
        loaderElement = document.getElementById('agrico-loader');
        if (!loaderElement) return;

        // Ensure clean initial state
        loaderElement.classList.remove(
            'is-logo-revealed',
            'is-green-expanded',
            'is-logo-scaled',
            'is-wordmark-revealed',
            'is-brand-holding',
            'is-green-retracted',
            'is-wordmark-green',
            'loader-hidden'
        );

        startTimeline();
    }

    // Monitor document ready state
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLoader);
    } else {
        initLoader();
    }

    // Track full page resource load
    window.addEventListener('load', function () {
        isWindowLoaded = true;
    });

    /**
     * Public Developer & Testing API
     */
    window.AgriCoLoader = {
        skip: function () {
            completeLoader();
        },
        replay: function () {
            isAnimationComplete = false;
            clearTimers();
            if (loaderElement) {
                loaderElement.style.display = 'flex';
                document.body.classList.remove('loaded');
                initLoader();
            }
        },
        isComplete: function () {
            return isAnimationComplete;
        }
    };

})(window, document);
