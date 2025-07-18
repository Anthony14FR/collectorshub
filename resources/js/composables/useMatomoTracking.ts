interface MatomoTracker {
  push: (data: any[]) => void;
}

declare global {
  interface Window {
    _paq: MatomoTracker;
  }
}

export const useMatomoTracking = () => {
  const isProduction = () => {
    return import.meta.env.PROD;
  };

  const trackEvent = (category: string, action: string, name?: string, value?: number | string) => {
    if (typeof window !== 'undefined' && window._paq) {
      const eventData: any[] = ['trackEvent', category, action];
      if (name) eventData.push(name);
      if (value !== undefined) eventData.push(value);
      
      window._paq.push(eventData);
      
      if (!isProduction()) {
        console.log('Matomo Event:', { category, action, name, value });
      }
    }
  };

  const trackPageView = (customTitle?: string) => {
    if (typeof window !== 'undefined' && window._paq) {
      if (customTitle) {
        window._paq.push(['setDocumentTitle', customTitle]);
      }
      window._paq.push(['trackPageView']);
      
      if (!isProduction()) {
        console.log('Matomo Page View:', customTitle || 'Default title');
      }
    }
  };

  const startTimer = () => {
    return Date.now();
  };

  const trackTimeSpent = (startTime: number, category: string, page: string) => {
    const timeSpent = Math.round((Date.now() - startTime) / 1000); // en secondes
    trackEvent(category, 'Time Spent', page, timeSpent);
  };

  const trackLandingPageAction = (action: 'view' | 'scroll' | 'cta_click' | 'login_click' | 'register_click' | 'exit') => {
    trackEvent('Landing Page', action);
  };

  const trackAuthAction = (action: 'login_page_view' | 'login_attempt' | 'login_success' | 'login_error' | 'register_page_view' | 'register_start' | 'register_step' | 'register_success' | 'register_error' | 'social_login_attempt' | 'social_login_success' | 'social_login_error' | 'password_reset' | 'email_verification' | 'forgot_password_view' | 'reset_password_view' | 'verify_email_view' | 'totp_verification_view' | 'totp_verification', details?: string, step?: number) => {
    trackEvent('Authentication', action, details, step);
  };

  const trackSocialLogin = (provider: 'google' | 'discord', action: 'click' | 'success' | 'error') => {
    trackEvent('Social Login', action, provider);
  };

  const trackRegistrationFlow = (step: number, action: 'view' | 'complete' | 'error', details?: string) => {
    trackEvent('Registration Flow', action, `Step ${step}: ${details || ''}`, step);
  };

  const trackAvatarSelection = (avatarId: number) => {
    trackEvent('Registration', 'Avatar Selected', `Avatar ${avatarId}`, avatarId);
  };

  const trackFormError = (formType: string, errorField: string) => {
    trackEvent('Form Error', formType, errorField);
  };

  const trackUserNavigation = (from: string, to: string) => {
    trackEvent('Navigation', 'Page Change', `${from} -> ${to}`);
  };

  const setCustomVariable = (index: number, name: string, value: string, scope: 'visit' | 'page' = 'page') => {
    if (typeof window !== 'undefined' && window._paq) {
      window._paq.push(['setCustomVariable', index, name, value, scope]);
    }
  };

  const trackGoal = (goalId: number, customRevenue?: number) => {
    if (typeof window !== 'undefined' && window._paq) {
      const goalData = ['trackGoal', goalId];
      if (customRevenue) goalData.push(customRevenue);
      window._paq.push(goalData);
    }
  };

  const trackFlashEvent = (flashEvent: any) => {
    if (flashEvent && flashEvent.category && flashEvent.action) {
      trackEvent(flashEvent.category, flashEvent.action, flashEvent.name, flashEvent.value);
    }
  };

  const checkForFlashEvents = () => {
    if (typeof window !== 'undefined' && (window as any).Laravel?.matomoEvent) {
      const flashEvent = (window as any).Laravel.matomoEvent;
      trackFlashEvent(flashEvent);
      
      delete (window as any).Laravel.matomoEvent;
    }
  };

  return {
    trackEvent,
    trackPageView,
    startTimer,
    trackTimeSpent,
    trackLandingPageAction,
    trackAuthAction,
    trackSocialLogin,
    trackRegistrationFlow,
    trackAvatarSelection,
    trackFormError,
    trackUserNavigation,
    setCustomVariable,
    trackGoal,
    trackFlashEvent,
    checkForFlashEvents,
    isProduction
  };
}; 