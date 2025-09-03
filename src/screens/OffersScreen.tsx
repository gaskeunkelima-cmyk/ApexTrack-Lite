import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, Alert, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import Card from '../components/Card';
import Button from '../components/Button';
import LoadingSpinner from '../components/LoadingSpinner';
import ApiService from '../services/api';

interface Offer {
  id: string;
  name: string;
  url: string;
  status: string;
  country: string;
  device: string;
  can_show_to_proxy: boolean;
}

export default function OffersScreen() {
  const [offers, setOffers] = useState<Offer[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadOffers();
  }, []);

  const loadOffers = async () => {
    try {
      const data = await ApiService.getOffers();
      setOffers(Array.isArray(data) ? data : data.data || []);
    } catch (error) {
      Alert.alert('Error', 'Failed to load offers');
    } finally {
      setLoading(false);
    }
  };

  const handleDeleteOffer = async (id: string) => {
    Alert.alert(
      'Confirm Delete',
      'Are you sure you want to delete this offer?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await ApiService.deleteOffer(id);
              setOffers(offers.filter(offer => offer.id !== id));
              Alert.alert('Success', 'Offer deleted successfully');
            } catch (error) {
              Alert.alert('Error', 'Failed to delete offer');
            }
          }
        }
      ]
    );
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return '#10b981';
      case 'paused': return '#ef4444';
      case 'pending': return '#f59e0b';
      default: return '#6b7280';
    }
  };

  if (loading) {
    return <LoadingSpinner message="Loading offers..." />;
  }

  return (
    <ScrollView style={styles.container}>
      <View style={styles.content}>
        <View style={styles.header}>
          <Text style={styles.title}>Offers Management</Text>
          <Button
            title="Add Offer"
            onPress={() => {/* Navigate to create offer */}}
            style={styles.addButton}
          />
        </View>

        {offers.length === 0 ? (
          <Card>
            <Text style={styles.emptyText}>No offers found</Text>
          </Card>
        ) : (
          offers.map((offer) => (
            <Card key={offer.id} style={styles.offerCard}>
              <View style={styles.offerHeader}>
                <Text style={styles.offerName}>{offer.name}</Text>
                <View style={styles.offerActions}>
                  <TouchableOpacity
                    onPress={() => {/* Navigate to edit */}}
                    style={styles.actionButton}
                  >
                    <Ionicons name="pencil" size={16} color="#3b82f6" />
                  </TouchableOpacity>
                  <TouchableOpacity
                    onPress={() => handleDeleteOffer(offer.id)}
                    style={styles.actionButton}
                  >
                    <Ionicons name="trash" size={16} color="#ef4444" />
                  </TouchableOpacity>
                </View>
              </View>
              
              <View style={styles.offerDetails}>
                <View style={styles.detailRow}>
                  <Text style={styles.detailLabel}>Status:</Text>
                  <View style={[styles.statusBadge, { backgroundColor: getStatusColor(offer.status) }]}>
                    <Text style={styles.statusText}>{offer.status.toUpperCase()}</Text>
                  </View>
                </View>
                
                <View style={styles.detailRow}>
                  <Text style={styles.detailLabel}>Country:</Text>
                  <Text style={styles.detailValue}>{offer.country || 'N/A'}</Text>
                </View>
                
                <View style={styles.detailRow}>
                  <Text style={styles.detailLabel}>Device:</Text>
                  <Text style={styles.detailValue}>{offer.device || 'N/A'}</Text>
                </View>
                
                <View style={styles.detailRow}>
                  <Text style={styles.detailLabel}>Proxy Check:</Text>
                  <Text style={styles.detailValue}>{offer.can_show_to_proxy ? 'Yes' : 'No'}</Text>
                </View>
              </View>
            </Card>
          ))
        )}
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
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1f2937'
  },
  addButton: {
    paddingHorizontal: 16,
    paddingVertical: 8
  },
  offerCard: {
    marginBottom: 12
  },
  offerHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12
  },
  offerName: {
    fontSize: 18,
    fontWeight: '600',
    color: '#1f2937',
    flex: 1
  },
  offerActions: {
    flexDirection: 'row',
    gap: 8
  },
  actionButton: {
    padding: 8
  },
  offerDetails: {
    gap: 8
  },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center'
  },
  detailLabel: {
    fontSize: 14,
    color: '#6b7280',
    fontWeight: '500'
  },
  detailValue: {
    fontSize: 14,
    color: '#1f2937'
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12
  },
  statusText: {
    fontSize: 12,
    color: '#ffffff',
    fontWeight: '600'
  },
  emptyText: {
    textAlign: 'center',
    color: '#6b7280',
    fontStyle: 'italic',
    paddingVertical: 20
  }
});