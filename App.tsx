import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { AuthProvider, useAuth } from './src/context/AuthContext';
import Layout from './src/components/Layout';
import LoginScreen from './src/screens/LoginScreen';
import AppNavigator from './src/navigation/AppNavigator';
import LoadingSpinner from './src/components/LoadingSpinner';

function AppContent() {
  const { isAuthenticated, loading, login } = useAuth();

  if (loading) {
    return <LoadingSpinner message="Initializing app..." />;
  }

  return (
    <Layout>
      {isAuthenticated ? (
        <NavigationContainer>
          <AppNavigator />
        </NavigationContainer>
      ) : (
        <LoginScreen onLoginSuccess={() => {}} />
      )}
    </Layout>
  );
}

export default function App() {
  return (
    <AuthProvider>
      <AppContent />
    </AuthProvider>
  );
}