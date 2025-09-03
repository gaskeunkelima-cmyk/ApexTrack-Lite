import React, { useState, useEffect } from 'react';
import { View, Text, TouchableOpacity, StyleSheet, Platform } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import AuthService, { User } from '../services/auth';

interface HeaderProps {
  title: string;
  onMenuPress?: () => void;
  showMenu?: boolean;
}

export default function Header({ title, onMenuPress, showMenu = true }: HeaderProps) {
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    loadUser();
  }, []);

  const loadUser = async () => {
    const userData = await AuthService.getUser();
    setUser(userData);
  };

  const handleLogout = async () => {
    await AuthService.logout();
    // Navigation will be handled by the auth context
  };

  return (
    <View style={styles.container}>
      <View style={styles.leftSection}>
        {showMenu && (
          <TouchableOpacity onPress={onMenuPress} style={styles.menuButton}>
            <Ionicons name="menu" size={24} color="#374151" />
          </TouchableOpacity>
        )}
        <Text style={styles.title}>{title}</Text>
      </View>
      
      <View style={styles.rightSection}>
        <Text style={styles.userName}>{user?.name || 'User'}</Text>
        <TouchableOpacity onPress={handleLogout} style={styles.logoutButton}>
          <Ionicons name="log-out-outline" size={20} color="#ef4444" />
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#e5e7eb',
    ...Platform.select({
      web: {
        position: 'sticky' as any,
        top: 0,
        zIndex: 10
      }
    })
  },
  leftSection: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1
  },
  menuButton: {
    marginRight: 12,
    padding: 4
  },
  title: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1f2937'
  },
  rightSection: {
    flexDirection: 'row',
    alignItems: 'center'
  },
  userName: {
    fontSize: 14,
    color: '#6b7280',
    marginRight: 12
  },
  logoutButton: {
    padding: 8
  }
});