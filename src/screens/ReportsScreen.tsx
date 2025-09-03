import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity } from 'react-native';
import Card from '../components/Card';
import LoadingSpinner from '../components/LoadingSpinner';
import ApiService from '../services/api';

type ReportType = 'advance' | 'clicks' | 'leads' | 'breakdown';

export default function ReportsScreen() {
  const [activeTab, setActiveTab] = useState<ReportType>('advance');
  const [reportData, setReportData] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadReports();
  }, [activeTab]);

  const loadReports = async () => {
    setLoading(true);
    try {
      const data = await ApiService.getReports(activeTab);
      setReportData(data);
    } catch (error) {
      console.error('Failed to load reports:', error);
    } finally {
      setLoading(false);
    }
  };

  const tabs = [
    { key: 'advance', label: 'Advance' },
    { key: 'clicks', label: 'Clicks' },
    { key: 'leads', label: 'Conversions' },
    { key: 'breakdown', label: 'Breakdown' }
  ];

  const renderTabContent = () => {
    if (loading) {
      return <LoadingSpinner message="Loading reports..." />;
    }

    if (!reportData?.data || reportData.data.length === 0) {
      return (
        <Card>
          <Text style={styles.emptyText}>No data found for this report</Text>
        </Card>
      );
    }

    return (
      <Card>
        <ScrollView horizontal showsHorizontalScrollIndicator={false}>
          <View style={styles.table}>
            {activeTab === 'advance' && (
              <>
                <View style={styles.tableHeader}>
                  <Text style={[styles.tableHeaderText, styles.col1]}>Sub ID</Text>
                  <Text style={[styles.tableHeaderText, styles.col2]}>Hits</Text>
                  <Text style={[styles.tableHeaderText, styles.col2]}>Unique</Text>
                  <Text style={[styles.tableHeaderText, styles.col2]}>Conv.</Text>
                  <Text style={[styles.tableHeaderText, styles.col2]}>CR %</Text>
                  <Text style={[styles.tableHeaderText, styles.col2]}>Payout</Text>
                </View>
                {reportData.data.map((row: any, index: number) => (
                  <View key={index} style={styles.tableRow}>
                    <Text style={[styles.tableCellText, styles.col1]}>{row.username}</Text>
                    <Text style={[styles.tableCellText, styles.col2]}>{row.hits}</Text>
                    <Text style={[styles.tableCellText, styles.col2]}>{row.unique_clicks}</Text>
                    <Text style={[styles.tableCellText, styles.col2]}>{row.leads}</Text>
                    <Text style={[styles.tableCellText, styles.col2]}>{row.cr}%</Text>
                    <Text style={[styles.tableCellText, styles.col2]}>${row.total_payout}</Text>
                  </View>
                ))}
              </>
            )}
            
            {activeTab === 'clicks' && (
              <>
                <View style={styles.tableHeader}>
                  <Text style={[styles.tableHeaderText, styles.col1]}>Sub ID</Text>
                  <Text style={[styles.tableHeaderText, styles.col2]}>IP</Text>
                  <Text style={[styles.tableHeaderText, styles.col2]}>Country</Text>
                  <Text style={[styles.tableHeaderText, styles.col2]}>Device</Text>
                  <Text style={[styles.tableHeaderText, styles.col2]}>Date</Text>
                </View>
                {reportData.data.map((row: any, index: number) => (
                  <View key={index} style={styles.tableRow}>
                    <Text style={[styles.tableCellText, styles.col1]}>{row.username}</Text>
                    <Text style={[styles.tableCellText, styles.col2]}>{row.ip_address}</Text>
                    <Text style={[styles.tableCellText, styles.col2]}>{row.country_code}</Text>
                    <Text style={[styles.tableCellText, styles.col2]}>{row.device_type}</Text>
                    <Text style={[styles.tableCellText, styles.col2]}>
                      {new Date(row.created_at).toLocaleDateString()}
                    </Text>
                  </View>
                ))}
              </>
            )}
          </View>
        </ScrollView>
      </Card>
    );
  };

  return (
    <ScrollView style={styles.container}>
      <View style={styles.content}>
        <Text style={styles.title}>Reports</Text>

        {/* Tab Navigation */}
        <View style={styles.tabContainer}>
          {tabs.map((tab) => (
            <TouchableOpacity
              key={tab.key}
              style={[
                styles.tab,
                activeTab === tab.key && styles.activeTab
              ]}
              onPress={() => setActiveTab(tab.key as ReportType)}
            >
              <Text style={[
                styles.tabText,
                activeTab === tab.key && styles.activeTabText
              ]}>
                {tab.label}
              </Text>
            </TouchableOpacity>
          ))}
        </View>

        {renderTabContent()}
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
  tabContainer: {
    flexDirection: 'row',
    backgroundColor: '#ffffff',
    borderRadius: 8,
    padding: 4,
    marginBottom: 16
  },
  tab: {
    flex: 1,
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderRadius: 6,
    alignItems: 'center'
  },
  activeTab: {
    backgroundColor: '#3b82f6'
  },
  tabText: {
    fontSize: 14,
    fontWeight: '500',
    color: '#6b7280'
  },
  activeTabText: {
    color: '#ffffff'
  },
  table: {
    minWidth: 600
  },
  tableHeader: {
    flexDirection: 'row',
    backgroundColor: '#f9fafb',
    paddingVertical: 12,
    paddingHorizontal: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#e5e7eb'
  },
  tableRow: {
    flexDirection: 'row',
    paddingVertical: 12,
    paddingHorizontal: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#f3f4f6'
  },
  tableHeaderText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#6b7280',
    textTransform: 'uppercase'
  },
  tableCellText: {
    fontSize: 14,
    color: '#1f2937'
  },
  col1: {
    width: 120
  },
  col2: {
    width: 100
  },
  emptyText: {
    textAlign: 'center',
    color: '#6b7280',
    fontStyle: 'italic',
    paddingVertical: 20
  }
});