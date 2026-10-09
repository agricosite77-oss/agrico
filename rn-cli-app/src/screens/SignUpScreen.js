import React, { useState, useContext, useRef, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Alert,
  ScrollView,
  KeyboardAvoidingView,
  Platform,
  StatusBar,
  Image,
  Animated,
} from 'react-native';

import { AuthContext } from '../context/AuthContext';

// 1. Exact 3-Stop Gradient Simulation: #DAF1DE (0%) -> #f6faf7 (45%) -> #ffffff (100%)
function PureGradient() {
  return (
    <View style={StyleSheet.absoluteFill} pointerEvents="none">
      <View style={{ flex: 1.2, backgroundColor: '#DAF1DE' }} />
      <View style={{ flex: 0.8, backgroundColor: '#f6faf7' }} />
      <View style={{ flex: 1.5, backgroundColor: '#ffffff' }} />
    </View>
  );
}

// 2. User Icon for Full Name
function UserInputIcon({ size = 16, color = '#64748b' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: size * 0.44,
          height: size * 0.44,
          borderRadius: (size * 0.44) / 2,
          borderWidth: 1.6,
          borderColor: color,
          marginBottom: 1,
        }}
      />
      <View
        style={{
          width: size * 0.84,
          height: size * 0.42,
          borderTopLeftRadius: size * 0.4,
          borderTopRightRadius: size * 0.4,
          borderWidth: 1.6,
          borderColor: color,
          borderBottomWidth: 0,
        }}
      />
    </View>
  );
}

// 3. Mail Input Icon
function MailInputIcon({ size = 16, color = '#64748b' }) {
  return (
    <View
      style={{
        width: 18,
        height: 13,
        borderWidth: 1.5,
        borderColor: color,
        borderRadius: 2.5,
        alignItems: 'center',
        justifyContent: 'flex-start',
        overflow: 'hidden',
      }}
    >
      <View
        style={{
          width: 9,
          height: 9,
          borderBottomWidth: 1.5,
          borderRightWidth: 1.5,
          borderColor: color,
          transform: [{ rotate: '45deg' }],
          marginTop: -4,
        }}
      />
    </View>
  );
}

// 4. Lock Input Icon
function LockInputIcon({ size = 16, color = '#64748b' }) {
  return (
    <View style={{ width: size, height: size + 2, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: size * 0.58,
          height: size * 0.46,
          borderWidth: 1.6,
          borderColor: color,
          borderBottomWidth: 0,
          borderTopLeftRadius: size * 0.29,
          borderTopRightRadius: size * 0.29,
          marginBottom: -1,
        }}
      />
      <View
        style={{
          width: size * 0.88,
          height: size * 0.56,
          borderWidth: 1.6,
          borderColor: color,
          borderRadius: 3,
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        <View style={{ width: 2.2, height: 3.5, backgroundColor: color, borderRadius: 1 }} />
      </View>
    </View>
  );
}

// 5. Eye Toggle Icon
function EyeToggleIcon({ size = 18, color = '#64748b', isHidden = true }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: size * 0.88,
          height: size * 0.54,
          borderWidth: 1.6,
          borderColor: color,
          borderRadius: size * 0.35,
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        <View
          style={{
            width: size * 0.32,
            height: size * 0.32,
            borderRadius: (size * 0.32) / 2,
            backgroundColor: color,
          }}
        />
      </View>
      {isHidden && (
        <View
          style={{
            position: 'absolute',
            width: size * 0.95,
            height: 1.8,
            backgroundColor: color,
            transform: [{ rotate: '-45deg' }],
          }}
        />
      )}
    </View>
  );
}

// 6. User Plus / Register Badge Icon
function RegisterBadgeIcon({ size = 20, color = '#22c55e' }) {
  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <View
        style={{
          width: size * 0.44,
          height: size * 0.44,
          borderRadius: (size * 0.44) / 2,
          borderWidth: 1.8,
          borderColor: color,
          marginBottom: 1.5,
        }}
      />
      <View
        style={{
          width: size * 0.82,
          height: size * 0.42,
          borderTopLeftRadius: size * 0.4,
          borderTopRightRadius: size * 0.4,
          borderWidth: 1.8,
          borderColor: color,
          borderBottomWidth: 0,
        }}
      />
    </View>
  );
}

// 7. Floating Input Component
function FloatingInput({
  label,
  value,
  onChangeText,
  isFocused,
  onFocus,
  onBlur,
  secureTextEntry = false,
  error = null,
  leftIcon = null,
  rightIcon = null,
  autoCapitalize = 'none',
  keyboardType = 'default',
}) {
  const isFloated = isFocused || (value && value.length > 0);
  const animatedFloat = useRef(new Animated.Value(isFloated ? 1 : 0)).current;

  useEffect(() => {
    Animated.timing(animatedFloat, {
      toValue: isFloated ? 1 : 0,
      duration: 180,
      useNativeDriver: false,
    }).start();
  }, [isFloated, animatedFloat]);

  const top = animatedFloat.interpolate({
    inputRange: [0, 1],
    outputRange: [12, -9],
  });

  const left = animatedFloat.interpolate({
    inputRange: [0, 1],
    outputRange: [42, 12],
  });

  const fontSize = animatedFloat.interpolate({
    inputRange: [0, 1],
    outputRange: [13.5, 11],
  });

  const borderColor = error
    ? '#dc2626'
    : isFocused
    ? '#64748b'
    : isFloated
    ? '#64748b'
    : '#cbd5e1';

  const labelColor = error
    ? '#dc2626'
    : isFocused
    ? '#475569'
    : isFloated
    ? '#64748b'
    : '#94a3b8';

  return (
    <View style={styles.fieldWrapper}>
      <View
        style={[
          styles.inputContainer,
          { borderColor },
          isFocused && !error && styles.inputContainerFocused,
          error && styles.inputContainerError,
        ]}
      >
        <Animated.View
          pointerEvents="none"
          style={[
            styles.floatingLabelContainer,
            {
              top,
              left,
            },
          ]}
        >
          <Animated.Text
            style={[
              styles.floatingLabelText,
              {
                fontSize,
                color: labelColor,
                fontWeight: isFloated ? '700' : '500',
              },
            ]}
          >
            {label}
          </Animated.Text>
        </Animated.View>

        <View style={styles.leftIconBox}>{leftIcon}</View>

        <TextInput
          style={styles.input}
          value={value}
          onChangeText={onChangeText}
          onFocus={onFocus}
          onBlur={onBlur}
          secureTextEntry={secureTextEntry}
          autoCapitalize={autoCapitalize}
          keyboardType={keyboardType}
          autoCorrect={false}
        />

        {rightIcon && <View style={styles.eyeBtn}>{rightIcon}</View>}
      </View>

      {!!error && <Text style={styles.fieldErrorText}>{error}</Text>}
    </View>
  );
}

export default function SignUpScreen({ navigation }) {
  const { signUpWithEmail, continueAsGuest } = useContext(AuthContext);

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');

  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const [nameFocused, setNameFocused] = useState(false);
  const [emailFocused, setEmailFocused] = useState(false);
  const [passwordFocused, setPasswordFocused] = useState(false);
  const [confirmPasswordFocused, setConfirmPasswordFocused] = useState(false);

  const [isSubmitting, setIsSubmitting] = useState(false);

  const [nameError, setNameError] = useState('');
  const [emailError, setEmailError] = useState('');
  const [passwordError, setPasswordError] = useState('');
  const [confirmError, setConfirmError] = useState('');
  const [generalError, setGeneralError] = useState('');

  const validateEmailFormat = (val) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(val);
  };

  const handleSignUp = async () => {
    let hasError = false;
    setNameError('');
    setEmailError('');
    setPasswordError('');
    setConfirmError('');
    setGeneralError('');

    const trimmedName = name.trim();
    const trimmedEmail = email.trim();
    const trimmedPassword = password.trim();
    const trimmedConfirm = passwordConfirmation.trim();

    if (!trimmedName) {
      setNameError('Full name is required.');
      hasError = true;
    }

    if (!trimmedEmail) {
      setEmailError('Email address is required.');
      hasError = true;
    } else if (!validateEmailFormat(trimmedEmail)) {
      setEmailError('Please enter a valid email address.');
      hasError = true;
    }

    if (!trimmedPassword) {
      setPasswordError('Password is required.');
      hasError = true;
    } else if (trimmedPassword.length < 8) {
      setPasswordError('Password must be at least 8 characters.');
      hasError = true;
    }

    if (!trimmedConfirm) {
      setConfirmError('Please confirm your password.');
      hasError = true;
    } else if (trimmedPassword !== trimmedConfirm) {
      setConfirmError('Passwords do not match.');
      hasError = true;
    }

    if (hasError) return;

    setIsSubmitting(true);

    try {
      await signUpWithEmail(trimmedName, trimmedEmail, trimmedPassword, trimmedConfirm);
    } catch (error) {
      const msg =
        error.response?.data?.message ||
        error.response?.data?.errors?.email?.[0] ||
        error.response?.data?.errors?.password?.[0] ||
        'Registration failed. Please check your inputs.';
      setGeneralError(msg);
      Alert.alert('Sign-Up Failed', msg);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.keyboardView}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <StatusBar barStyle="dark-content" backgroundColor="#DAF1DE" />

      {/* 1. Exact 3-stop Vertical Gradient Background */}
      <PureGradient />

      {/* 2. Exact Decorative Arc Ring */}
      <View style={styles.authArc} pointerEvents="none" />

      {/* 3. Responsive AgriCo Header Logo positioned at top-left */}
      <View style={styles.topBrandHeader}>
        <Image
          source={require('../assets/agrico-logo.png')}
          style={styles.brandLogo}
        />
        <Text style={styles.brandName}>AgriCo</Text>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        keyboardShouldPersistTaps="handled"
        showsVerticalScrollIndicator={false}
      >
        {/* 4. Auth Container Card */}
        <View style={styles.authCard}>
          {/* Razor-sharp 4px Green Top Accent Bar */}
          <View style={styles.cardTopAccent} />

          {/* Top Icon Badge */}
          <View style={styles.iconBadge}>
            <RegisterBadgeIcon size={20} color="#22c55e" />
          </View>

          {/* Title */}
          <Text style={styles.title}>Create Account</Text>

          {/* Subtitle */}
          <Text style={styles.subtitle}>
            Join AgriCo to access soil analysis{'\n'}& smart agricultural tools.
          </Text>

          {/* General Error Banner */}
          {!!generalError && (
            <View style={styles.errorBanner}>
              <Text style={styles.errorText}>{generalError}</Text>
            </View>
          )}

          {/* Form Fields */}
          <View style={styles.form}>
            {/* Full Name */}
            <FloatingInput
              label="Full Name"
              value={name}
              onChangeText={(val) => {
                setName(val);
                if (nameError) setNameError('');
                if (generalError) setGeneralError('');
              }}
              isFocused={nameFocused}
              onFocus={() => setNameFocused(true)}
              onBlur={() => setNameFocused(false)}
              autoCapitalize="words"
              error={nameError}
              leftIcon={<UserInputIcon size={16} color={nameError ? '#dc2626' : '#64748b'} />}
            />

            {/* Email Address */}
            <FloatingInput
              label="Email Address"
              value={email}
              onChangeText={(val) => {
                setEmail(val);
                if (emailError) setEmailError('');
                if (generalError) setGeneralError('');
              }}
              isFocused={emailFocused}
              onFocus={() => setEmailFocused(true)}
              onBlur={() => setEmailFocused(false)}
              keyboardType="email-address"
              autoCapitalize="none"
              error={emailError}
              leftIcon={<MailInputIcon size={16} color={emailError ? '#dc2626' : '#64748b'} />}
            />

            {/* Password */}
            <FloatingInput
              label="Password"
              value={password}
              onChangeText={(val) => {
                setPassword(val);
                if (passwordError) setPasswordError('');
                if (generalError) setGeneralError('');
              }}
              isFocused={passwordFocused}
              onFocus={() => setPasswordFocused(true)}
              onBlur={() => setPasswordFocused(false)}
              secureTextEntry={!showPassword}
              error={passwordError}
              leftIcon={<LockInputIcon size={16} color={passwordError ? '#dc2626' : '#64748b'} />}
              rightIcon={
                <TouchableOpacity
                  onPress={() => setShowPassword(!showPassword)}
                  hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
                >
                  <EyeToggleIcon size={18} color="#64748b" isHidden={!showPassword} />
                </TouchableOpacity>
              }
            />

            {/* Confirm Password */}
            <FloatingInput
              label="Confirm Password"
              value={passwordConfirmation}
              onChangeText={(val) => {
                setPasswordConfirmation(val);
                if (confirmError) setConfirmError('');
                if (generalError) setGeneralError('');
              }}
              isFocused={confirmPasswordFocused}
              onFocus={() => setConfirmPasswordFocused(true)}
              onBlur={() => setConfirmPasswordFocused(false)}
              secureTextEntry={!showConfirmPassword}
              error={confirmError}
              leftIcon={<LockInputIcon size={16} color={confirmError ? '#dc2626' : '#64748b'} />}
              rightIcon={
                <TouchableOpacity
                  onPress={() => setShowConfirmPassword(!showConfirmPassword)}
                  hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
                >
                  <EyeToggleIcon size={18} color="#64748b" isHidden={!showConfirmPassword} />
                </TouchableOpacity>
              }
            />

            {/* Submit Button: Full-width, Pill Shape, 44px height, #22c55e */}
            <TouchableOpacity
              style={[styles.submitBtn, isSubmitting && { opacity: 0.85 }]}
              onPress={handleSignUp}
              disabled={isSubmitting}
              activeOpacity={0.88}
            >
              {isSubmitting ? (
                <ActivityIndicator color="#ffffff" size="small" />
              ) : (
                <Text style={styles.submitBtnText}>Create Account</Text>
              )}
            </TouchableOpacity>

            {/* Sign In Link */}
            <View style={styles.bottomLinkRow}>
              <Text style={styles.bottomLinkText}>Already have an account?</Text>
              <TouchableOpacity onPress={() => navigation.navigate('SignIn')}>
                <Text style={styles.signInLinkText}> Sign In</Text>
              </TouchableOpacity>
            </View>

            {/* Divider */}
            <View style={styles.guestDivider} />

            {/* Continue as Guest */}
            <TouchableOpacity
              style={styles.guestBtn}
              onPress={() => continueAsGuest && continueAsGuest()}
              activeOpacity={0.7}
            >
              <Text style={styles.guestBtnText}>Continue as Guest →</Text>
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  keyboardView: {
    flex: 1,
    backgroundColor: '#DAF1DE',
  },
  authArc: {
    position: 'absolute',
    width: 640,
    height: 640,
    borderRadius: 320,
    borderWidth: 1.5,
    borderColor: 'rgba(142, 182, 155, 0.4)',
    top: '50%',
    left: '50%',
    transform: [{ translateX: -320 }, { translateY: -320 }],
    pointerEvents: 'none',
  },
  topBrandHeader: {
    paddingTop: Platform.OS === 'android' ? (StatusBar.currentHeight || 20) + 12 : 52,
    paddingHorizontal: 24,
    paddingBottom: 4,
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    zIndex: 20,
  },
  brandLogo: {
    width: 34,
    height: 34,
    resizeMode: 'contain',
    marginRight: 8,
  },
  brandName: {
    fontSize: 20,
    fontWeight: '900',
    color: '#22c55e',
    letterSpacing: -0.3,
  },
  scrollContent: {
    flexGrow: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 24,
  },
  authCard: {
    width: '100%',
    maxWidth: 410,
    backgroundColor: '#ffffff',
    borderRadius: 22,
    borderWidth: 1,
    borderColor: 'rgba(218, 241, 222, 0.9)',
    paddingHorizontal: 24,
    paddingTop: 22,
    paddingBottom: 22,
    shadowColor: '#15803d',
    shadowOffset: { width: 0, height: 20 },
    shadowOpacity: 0.08,
    shadowRadius: 25,
    elevation: 8,
    overflow: 'hidden',
    position: 'relative',
  },
  cardTopAccent: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    height: 4,
    backgroundColor: '#22c55e',
  },
  iconBadge: {
    width: 44,
    height: 44,
    borderRadius: 14,
    backgroundColor: '#f0fdf4',
    borderWidth: 1,
    borderColor: '#bbf7d0',
    alignItems: 'center',
    justifyContent: 'center',
    alignSelf: 'center',
    marginBottom: 10,
    shadowColor: '#22c55e',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.12,
    shadowRadius: 7,
    elevation: 2,
  },
  title: {
    fontSize: 21,
    fontWeight: '800',
    color: '#0f172a',
    textAlign: 'center',
    letterSpacing: -0.3,
    marginBottom: 4,
  },
  subtitle: {
    fontSize: 12.5,
    color: '#64748b',
    textAlign: 'center',
    lineHeight: 17,
    marginBottom: 16,
  },
  errorBanner: {
    backgroundColor: '#fef2f2',
    borderColor: '#fecaca',
    borderWidth: 1,
    borderRadius: 10,
    paddingVertical: 8,
    paddingHorizontal: 12,
    marginBottom: 12,
  },
  errorText: {
    color: '#b91c1c',
    fontSize: 12.5,
    textAlign: 'center',
    fontWeight: '600',
  },
  form: {
    width: '100%',
  },
  fieldWrapper: {
    marginBottom: 12,
  },
  inputContainer: {
    position: 'relative',
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    borderWidth: 1.5,
    borderRadius: 12,
    height: 44,
    paddingHorizontal: 12,
  },
  inputContainerFocused: {
    borderColor: '#22c55e',
  },
  inputContainerError: {
    borderColor: '#dc2626',
  },
  floatingLabelContainer: {
    position: 'absolute',
    backgroundColor: '#ffffff',
    paddingHorizontal: 4,
    zIndex: 4,
  },
  floatingLabelText: {
    letterSpacing: 0.1,
  },
  leftIconBox: {
    width: 24,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 6,
    zIndex: 2,
  },
  input: {
    flex: 1,
    height: 44,
    fontSize: 14,
    color: '#0f172a',
    paddingVertical: 0,
    zIndex: 3,
  },
  eyeBtn: {
    position: 'absolute',
    right: 10,
    top: 0,
    bottom: 0,
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 4,
  },
  fieldErrorText: {
    color: '#dc2626',
    fontSize: 11.5,
    fontWeight: '700',
    marginTop: 3,
    marginLeft: 2,
  },
  submitBtn: {
    height: 44,
    backgroundColor: '#22c55e',
    borderRadius: 9999,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 4,
    shadowColor: '#22c55e',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.35,
    shadowRadius: 10,
    elevation: 4,
  },
  submitBtnText: {
    color: '#ffffff',
    fontSize: 15,
    fontWeight: '800',
    letterSpacing: 0.2,
  },
  bottomLinkRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 14,
    marginBottom: 8,
  },
  bottomLinkText: {
    fontSize: 13,
    color: '#64748b',
  },
  signInLinkText: {
    fontSize: 13,
    fontWeight: '700',
    color: '#22c55e',
  },
  guestDivider: {
    height: 1,
    backgroundColor: '#e2e8f0',
    marginVertical: 6,
  },
  guestBtn: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
  },
  guestBtnText: {
    fontSize: 13,
    fontWeight: '600',
    color: '#64748b',
  },
});
