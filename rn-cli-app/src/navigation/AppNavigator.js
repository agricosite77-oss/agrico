import React, { useContext, useState, useCallback } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';

import { AuthContext } from '../context/AuthContext';
import SplashScreen from '../screens/SplashScreen';
import SignInScreen from '../screens/SignInScreen';
import SignUpScreen from '../screens/SignUpScreen';
import ForgotPasswordScreen from '../screens/ForgotPasswordScreen';
import VerifyCodeScreen from '../screens/VerifyCodeScreen';
import ResetPasswordScreen from '../screens/ResetPasswordScreen';
import HomeScreen from '../screens/HomeScreen';
import ScanSoilScreen from '../screens/ScanSoilScreen';

import { nativeStackAuthOptions } from './authTransitions';

const Stack = createNativeStackNavigator();

export default function AppNavigator() {
  const { isLoading, userToken } = useContext(AuthContext);
  const [splashFinished, setSplashFinished] = useState(false);

  const handleFinish = useCallback(() => {
    setSplashFinished(true);
  }, []);

  if (!splashFinished) {
    return <SplashScreen onFinish={handleFinish} isReady={!isLoading} />;
  }

  return (
    <NavigationContainer>
      <Stack.Navigator
        screenOptions={nativeStackAuthOptions}
      >
        {userToken == null ? (
          <>
            <Stack.Screen name="SignIn" component={SignInScreen} />
            <Stack.Screen name="SignUp" component={SignUpScreen} />
            <Stack.Screen name="ForgotPassword" component={ForgotPasswordScreen} />
            <Stack.Screen name="VerifyCode" component={VerifyCodeScreen} />
            <Stack.Screen name="ResetPassword" component={ResetPasswordScreen} />
          </>
        ) : (
          <>
            <Stack.Screen name="Home" component={HomeScreen} />
            <Stack.Screen name="ScanSoil" component={ScanSoilScreen} />
          </>
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
}
