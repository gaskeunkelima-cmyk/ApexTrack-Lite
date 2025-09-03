# ApexTrack Mobile

A React Native application for tracking and managing affiliate marketing campaigns. Built with Expo for cross-platform deployment on Web, Android, and iOS.

## Features

- **Dashboard**: Real-time analytics and performance metrics
- **Smartlink Generator**: Create and manage tracking links
- **Offers Management**: CRUD operations for affiliate offers
- **Reports**: Detailed analytics and conversion tracking
- **User Management**: Profile and account settings
- **Cross-Platform**: Runs on Web, Android, and iOS

## Tech Stack

- **React Native** with **Expo**
- **TypeScript** for type safety
- **Expo Router** for navigation
- **Expo SecureStore** for secure token storage
- **React Navigation** for tab and stack navigation

## Getting Started

### Prerequisites

- Node.js 18+ 
- Expo CLI
- For mobile development: Android Studio or Xcode

### Installation

1. Install dependencies:
```bash
npm install
```

2. Start the development server:
```bash
npm start
```

### Platform-Specific Commands

- **Web**: `npm run web`
- **Android**: `npm run android`
- **iOS**: `npm run ios`

### Building for Production

- **Android APK**: `npm run build:android`
- **iOS**: `npm run build:ios`
- **Web**: `npm run build:web`

## API Configuration

The app connects to the ApexTrack API at `https://www3.apextrack.site/api`. Update the API configuration in `src/config/api.ts` if needed.

## Project Structure

```
src/
├── components/     # Reusable UI components
├── screens/        # Screen components
├── navigation/     # Navigation configuration
├── services/       # API and auth services
├── context/        # React context providers
└── config/         # App configuration
```

## Features Overview

### Authentication
- Secure login with JWT tokens
- Token storage using Expo SecureStore
- Automatic session management

### Dashboard
- Real-time metrics display
- Recent clicks and conversions
- Performance summaries

### Generator
- Smartlink creation with multiple options
- Meta tag configuration
- URL shortening options

### Offers Management
- Create, read, update, delete offers
- Status management
- Country and device targeting

### Reports
- Multiple report types (advance, clicks, conversions, breakdown)
- Filterable data
- Export capabilities

## Deployment

### Web Deployment
The web version can be deployed to any static hosting service:

1. Build for web: `npm run build:web`
2. Deploy the `dist/` folder to your hosting service

### Mobile App Stores
Use Expo Application Services (EAS) for app store deployment:

1. Install EAS CLI: `npm install -g eas-cli`
2. Configure EAS: `eas build:configure`
3. Build for stores: `eas build --platform all`

## License

This project is licensed under the MIT License.