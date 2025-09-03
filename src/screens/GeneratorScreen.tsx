import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, TextInput, Alert } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import Card from '../components/Card';
import Button from '../components/Button';
import LoadingSpinner from '../components/LoadingSpinner';
import ApiService from '../services/api';
import * as Clipboard from 'expo-clipboard';

interface GeneratorData {
  offers: any[];
  domains: string[];
  redirect_types: string[];
  types: string[];
  generation_modes: string[];
  shortener_choices: string[];
}

export default function GeneratorScreen() {
  const [formData, setFormData] = useState({
    offer: '',
    shared_domain: '',
    redirect_type: '',
    type: '',
    generation_mode: '',
    shortener_choice: '',
    meta_title: '',
    meta_description: ''
  });
  
  const [generatorData, setGeneratorData] = useState<GeneratorData | null>(null);
  const [loading, setLoading] = useState(true);
  const [generating, setGenerating] = useState(false);
  const [result, setResult] = useState<any>(null);

  useEffect(() => {
    loadGeneratorData();
  }, []);

  const loadGeneratorData = async () => {
    try {
      const data = await ApiService.getGeneratorData();
      setGeneratorData(data);
    } catch (error) {
      Alert.alert('Error', 'Failed to load generator data');
    } finally {
      setLoading(false);
    }
  };

  const handleGenerate = async () => {
    if (!formData.shared_domain || !formData.redirect_type) {
      Alert.alert('Error', 'Please fill in required fields');
      return;
    }

    setGenerating(true);
    try {
      const formDataToSend = new FormData();
      Object.entries(formData).forEach(([key, value]) => {
        if (value) {
          formDataToSend.append(key, value);
        }
      });

      const result = await ApiService.generateSmartlink(formDataToSend);
      setResult(result);
      Alert.alert('Success', 'Smartlink generated successfully!');
    } catch (error) {
      Alert.alert('Error', error instanceof Error ? error.message : 'Failed to generate smartlink');
    } finally {
      setGenerating(false);
    }
  };

  const copyToClipboard = async (text: string) => {
    await Clipboard.setStringAsync(text);
    Alert.alert('Copied', 'URL copied to clipboard');
  };

  if (loading) {
    return <LoadingSpinner message="Loading generator..." />;
  }

  return (
    <ScrollView style={styles.container}>
      <View style={styles.content}>
        <Text style={styles.title}>Smartlink Generator</Text>

        <Card>
          <Text style={styles.sectionTitle}>Configuration</Text>
          
          <View style={styles.inputGroup}>
            <Text style={styles.label}>Offer (Optional)</Text>
            <View style={styles.pickerContainer}>
              <Picker
                selectedValue={formData.offer}
                onValueChange={(value) => setFormData({...formData, offer: value})}
                style={styles.picker}
              >
                <Picker.Item label="Select Offer" value="" />
                {generatorData?.offers?.map((offer) => (
                  <Picker.Item key={offer.id} label={offer.name} value={offer.id} />
                ))}
              </Picker>
            </View>
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Domain *</Text>
            <View style={styles.pickerContainer}>
              <Picker
                selectedValue={formData.shared_domain}
                onValueChange={(value) => setFormData({...formData, shared_domain: value})}
                style={styles.picker}
              >
                <Picker.Item label="Select Domain" value="" />
                {generatorData?.domains?.map((domain) => (
                  <Picker.Item key={domain} label={domain} value={domain} />
                ))}
              </Picker>
            </View>
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Redirect Type *</Text>
            <View style={styles.pickerContainer}>
              <Picker
                selectedValue={formData.redirect_type}
                onValueChange={(value) => setFormData({...formData, redirect_type: value})}
                style={styles.picker}
              >
                <Picker.Item label="Select Redirect Type" value="" />
                {generatorData?.redirect_types?.map((type) => (
                  <Picker.Item key={type} label={type} value={type} />
                ))}
              </Picker>
            </View>
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Meta Title</Text>
            <TextInput
              style={styles.input}
              value={formData.meta_title}
              onChangeText={(value) => setFormData({...formData, meta_title: value})}
              placeholder="Enter meta title"
            />
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Meta Description</Text>
            <TextInput
              style={[styles.input, styles.textArea]}
              value={formData.meta_description}
              onChangeText={(value) => setFormData({...formData, meta_description: value})}
              placeholder="Enter meta description"
              multiline
              numberOfLines={3}
            />
          </View>

          <Button
            title={generating ? 'Generating...' : 'Generate Smartlink'}
            onPress={handleGenerate}
            disabled={generating}
            style={styles.generateButton}
          />
        </Card>

        {result && (
          <Card>
            <Text style={styles.sectionTitle}>Result</Text>
            <View style={styles.resultItem}>
              <Text style={styles.resultLabel}>Final URL:</Text>
              <Text style={styles.resultUrl} onPress={() => copyToClipboard(result.final_shared_url)}>
                {result.final_shared_url}
              </Text>
            </View>
            {result.smartlink_url_after_first_shortening && (
              <View style={styles.resultItem}>
                <Text style={styles.resultLabel}>Shortened URL:</Text>
                <Text style={styles.resultUrl} onPress={() => copyToClipboard(result.smartlink_url_after_first_shortening)}>
                  {result.smartlink_url_after_first_shortening}
                </Text>
              </View>
            )}
          </Card>
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
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 20
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#1f2937',
    marginBottom: 16
  },
  inputGroup: {
    marginBottom: 16
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: '#374151',
    marginBottom: 8
  },
  input: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 8,
    paddingHorizontal: 16,
    paddingVertical: 12,
    fontSize: 16,
    backgroundColor: '#ffffff'
  },
  textArea: {
    height: 80,
    textAlignVertical: 'top'
  },
  pickerContainer: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 8,
    backgroundColor: '#ffffff'
  },
  picker: {
    height: 50
  },
  generateButton: {
    marginTop: 16
  },
  resultItem: {
    marginBottom: 16
  },
  resultLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: '#374151',
    marginBottom: 4
  },
  resultUrl: {
    fontSize: 14,
    color: '#3b82f6',
    textDecorationLine: 'underline'
  }
});