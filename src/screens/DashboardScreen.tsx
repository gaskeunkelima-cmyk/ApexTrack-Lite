import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, RefreshControl } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import Card from '../components/Card';
import LoadingSpinner from '../components/LoadingSpinner';
import ApiService from '../services/api';

interface DashboardData {
  summary: {
    today_clicks: number;
    today_leads: number;
    today_revenue: number;
    today_epc: number;
  };
  recent_clicks: any[];
  recent_leads: any[];
  performance_report: any[];
}

export default function DashboardScreen() {
  const [data, setData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadDashboard();
  }, []);

  const loadDashboard = async () => {
    try {
      const dashboardData = await ApiService.getDashboard();
      setData(dashboardData);
    } catch (error) {
      console.error('Failed to load dashboard:', error);
    } finally {
      setLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadDashboard();
    setRefreshing(false);
  };

  if (loading) {
    return <LoadingSpinner message="Loading dashboard..." />;
  }

  return (
    <ScrollView 
      style={styles.container}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
      }
    >
      <View style={styles.content}>
        <Text style={styles.title}>Dashboard</Text>
        
        {/* Summary Cards */}
        <View style={styles.summaryGrid}>
          <Card style={[styles.summaryCard, styles.clicksCard]}>
            <View style={styles.cardHeader}>
              <Ionicons name="trending-up" size={20} color="#3b82f6" />
              <Text style={styles.cardTitle}>Clicks Today</Text>
            </View>
            <Text style={styles.cardValue}>
              {data?.summary.today_clicks?.toLocaleString() || '0'}
            </Text>
          </Card>

          <Card style={[styles.summaryCard, styles.conversionsCard]}>
            <View style={styles.cardHeader}>
              <Ionicons name="checkmark-circle" size={20} color="#8b5cf6" />
              <Text style={styles.cardTitle}>Conversions Today</Text>
            </View>
            <Text style={styles.cardValue}>
              {data?.summary.today_leads?.toLocaleString() || '0'}
            </Text>
          </Card>

          <Card style={[styles.summaryCard, styles.revenueCard]}>
            <View style={styles.cardHeader}>
              <Ionicons name="cash" size={20} color="#f59e0b" />
              <Text style={styles.cardTitle}>Revenue Today</Text>
            </View>
            <Text style={styles.cardValue}>
              ${data?.summary.today_revenue?.toFixed(2) || '0.00'}
            </Text>
          </Card>

          <Card style={[styles.summaryCard, styles.epcCard]}>
            <View style={styles.cardHeader}>
              <Ionicons name="analytics" size={20} color="#10b981" />
              <Text style={styles.cardTitle}>EPC Today</Text>
            </View>
            <Text style={styles.cardValue}>
              ${data?.summary.today_epc?.toFixed(2) || '0.00'}
            </Text>
          </Card>
        </View>

        {/* Recent Activity */}
        <Card style={styles.activityCard}>
          <Text style={styles.sectionTitle}>Recent Clicks</Text>
          {data?.recent_clicks && data.recent_clicks.length > 0 ? (
            data.recent_clicks.slice(0, 5).map((click, index) => (
              <View key={index} style={styles.activityItem}>
                <View style={styles.activityInfo}>
                  <Text style={styles.activityText}>{click.sub_id || 'N/A'}</Text>
                  <Text style={styles.activitySubtext}>
                    {click.country_code} • {click.device_type}
                  </Text>
                </View>
                <Text style={styles.activityTime}>
                  {new Date(click.created_at).toLocaleTimeString()}
                </Text>
              </View>
            ))
          ) : (
            <Text style={styles.emptyText}>No recent clicks</Text>
          )}
        </Card>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f3f4f6'
  },
  content: {
    padding: 16
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 20
  },
  summaryGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    marginBottom: 20
  },
  summaryCard: {
    width: Platform.OS === 'web' ? '23%' : '48%',
    marginBottom: 12,
    minHeight: 100
  },
  clicksCard: {
    borderLeftWidth: 4,
    borderLeftColor: '#3b82f6',
    backgroundColor: '#eff6ff'
  },
  conversionsCard: {
    borderLeftWidth: 4,
    borderLeftColor: '#8b5cf6',
    backgroundColor: '#f3e8ff'
  },
  revenueCard: {
    borderLeftWidth: 4,
    borderLeftColor: '#f59e0b',
    backgroundColor: '#fffbeb'
  },
  epcCard: {
    borderLeftWidth: 4,
    borderLeftColor: '#10b981',
    backgroundColor: '#ecfdf5'
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8
  },
  cardTitle: {
    fontSize: 12,
    fontWeight: '600',
    color: '#6b7280',
    marginLeft: 8,
    textTransform: 'uppercase'
  },
  cardValue: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1f2937'
  },
  activityCard: {
    marginTop: 8
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#1f2937',
    marginBottom: 16
  },
  activityItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#f3f4f6'
  },
  activityInfo: {
    flex: 1
  },
  activityText: {
    fontSize: 14,
    fontWeight: '500',
    color: '#1f2937'
  },
  activitySubtext: {
    fontSize: 12,
    color: '#6b7280',
    marginTop: 2
  },
  activityTime: {
    fontSize: 12,
    color: '#9ca3af'
  },
  emptyText: {
    textAlign: 'center',
    color: '#6b7280',
    fontStyle: 'italic',
    paddingVertical: 20
  }
});