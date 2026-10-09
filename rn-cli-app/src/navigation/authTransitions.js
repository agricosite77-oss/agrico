// src/navigation/authTransitions.js
// Smooth screen-to-screen change: the next screen fades in while rising a little,
// the previous one fades out. Same easing as the rest of the AgriCo motion system.
import { Easing } from 'react-native';

/* Option A: @react-navigation/native-stack (simplest) */
export const nativeStackAuthOptions = {
  headerShown: false,
  animation: 'fade',        // crossfade; the card pieces then rise in via AuthCardLayout
  animationDuration: 300,
  contentStyle: { backgroundColor: '#f6faf7' },
};

/* Option B: @react-navigation/stack (JS stack, fully custom) */
const OPEN = { animation: 'timing', config: { duration: 350, easing: Easing.bezier(0.22, 1, 0.36, 1) } };
const CLOSE = { animation: 'timing', config: { duration: 250, easing: Easing.bezier(0.4, 0, 1, 1) } };

export const jsStackAuthOptions = {
  headerShown: false,
  gestureEnabled: false,
  transitionSpec: { open: OPEN, close: CLOSE },
  cardStyle: { backgroundColor: '#f6faf7' },
  cardStyleInterpolator: ({ current, next }) => ({
    cardStyle: {
      opacity: current.progress.interpolate({ inputRange: [0, 1], outputRange: [0, 1] }),
      transform: [
        {
          translateY: current.progress.interpolate({ inputRange: [0, 1], outputRange: [18, 0] }),
        },
        ...(next
          ? [{ translateY: next.progress.interpolate({ inputRange: [0, 1], outputRange: [0, -10] }) }]
          : []),
      ],
    },
  }),
};
