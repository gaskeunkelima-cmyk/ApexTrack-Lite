import { API_CONFIG } from '../config/api';
import AuthService from './auth';

class ApiService {
  private static instance: ApiService;

  static getInstance(): ApiService {
    if (!ApiService.instance) {
      ApiService.instance = new ApiService();
    }
    return ApiService.instance;
  }

  private async getHeaders(): Promise<Record<string, string>> {
    const token = await AuthService.getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token && { 'Authorization': `Bearer ${token}` })
    };
  }

  async request(endpoint: string, options: RequestInit = {}): Promise<any> {
    const headers = await this.getHeaders();
    
    const response = await fetch(`${API_CONFIG.BASE_URL}${endpoint}`, {
      ...options,
      headers: {
        ...headers,
        ...options.headers
      }
    });

    if (response.status === 401) {
      await AuthService.logout();
      throw new Error('Session expired. Please login again.');
    }

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || `Request failed with status ${response.status}`);
    }

    return data;
  }

  async getDashboard() {
    return this.request(API_CONFIG.ENDPOINTS.DASHBOARD);
  }

  async getOffers() {
    return this.request(API_CONFIG.ENDPOINTS.OFFERS);
  }

  async createOffer(offerData: any) {
    return this.request(API_CONFIG.ENDPOINTS.OFFERS, {
      method: 'POST',
      body: JSON.stringify(offerData)
    });
  }

  async updateOffer(id: string, offerData: any) {
    return this.request(`${API_CONFIG.ENDPOINTS.OFFERS}/${id}`, {
      method: 'PUT',
      body: JSON.stringify(offerData)
    });
  }

  async deleteOffer(id: string) {
    return this.request(`${API_CONFIG.ENDPOINTS.OFFERS}/${id}`, {
      method: 'DELETE'
    });
  }

  async getGeneratorData() {
    return this.request(API_CONFIG.ENDPOINTS.GENERATOR_DATA);
  }

  async generateSmartlink(data: FormData) {
    const token = await AuthService.getToken();
    const response = await fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.GENERATE_SMARTLINK}`, {
      method: 'POST',
      body: data,
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to generate smartlink');
    }

    return response.json();
  }

  async getReports(type: string, params: any = {}) {
    const queryString = new URLSearchParams(params).toString();
    return this.request(`${API_CONFIG.ENDPOINTS.REPORTS}/${type}?${queryString}`);
  }

  async getUsers(page: number = 1) {
    return this.request(`${API_CONFIG.ENDPOINTS.USERS}?page=${page}`);
  }

  async createUser(userData: any) {
    return this.request(API_CONFIG.ENDPOINTS.USERS, {
      method: 'POST',
      body: JSON.stringify(userData)
    });
  }

  async updateUser(id: string, userData: any) {
    return this.request(`${API_CONFIG.ENDPOINTS.USERS}/${id}`, {
      method: 'PUT',
      body: JSON.stringify(userData)
    });
  }

  async deleteUser(id: string) {
    return this.request(`${API_CONFIG.ENDPOINTS.USERS}/${id}`, {
      method: 'DELETE'
    });
  }

  async getProfile() {
    return this.request(API_CONFIG.ENDPOINTS.PROFILE);
  }

  async updateProfile(profileData: any) {
    return this.request(API_CONFIG.ENDPOINTS.PROFILE, {
      method: 'PUT',
      body: JSON.stringify(profileData)
    });
  }

  async updatePassword(passwordData: any) {
    return this.request(API_CONFIG.ENDPOINTS.PASSWORD, {
      method: 'PUT',
      body: JSON.stringify(passwordData)
    });
  }
}

export default ApiService.getInstance();