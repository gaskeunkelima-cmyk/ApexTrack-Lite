export const API_CONFIG = {
  BASE_URL: 'https://www3.apextrack.site/api',
  FACEBOOK_ACCESS_TOKEN: '1308899767242947|HVu-8GkDtyPmpAR2SQOAx2BT2bg',
  ENDPOINTS: {
    LOGIN: '/auth/login',
    DASHBOARD: '/dashboard',
    OFFERS: '/offers',
    GENERATOR_DATA: '/generator-data',
    GENERATE_SMARTLINK: '/generate-smartlink',
    REPORTS: '/reports',
    USERS: '/users',
    PROFILE: '/profile',
    PASSWORD: '/password',
    POSTBACK: '/postback/conversion'
  }
};

export const NETWORK_EXAMPLES = [
  {
    name: 'Our Platform',
    clickIdParam: '{clickid}',
    subIdParam: '{tracking}',
    exampleUrl: 'https://your-offer-domain.com/base?clickid={clickid}&subid={tracking}'
  },
  {
    name: 'NOVA',
    clickIdParam: '{sub2}',
    subIdParam: '{sub3}',
    exampleUrl: 'https://your-offer-domain.com/base?sub2={sub2}&sub3={tracking}'
  },
  {
    name: 'AdsEmpire',
    clickIdParam: '{clickid}',
    subIdParam: '{subid2}',
    exampleUrl: 'https://your-offer-domain.com/base?clickid={clickid}&subid2={tracking}'
  }
];

export const POSTBACK_EXAMPLES = [
  {
    name: 'General',
    url: 'https://www3.apextrack.site/api/postback/conversion?clickid=Di_Isi_Click_ID&payout=Di_Isi_Payout'
  },
  {
    name: 'NOVA',
    url: 'https://www3.apextrack.site/api/postback/conversion?clickid={sub2}&payout={payout_amount}'
  },
  {
    name: 'AdsEmpire',
    url: 'https://www3.apextrack.site/api/postback/conversion?clickid={clickid}&payout={payout}'
  }
];