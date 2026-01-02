'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import useStore from '@/store/useStore';
import Loader from '@/components/UI/Loader';

export default function HomePage() {
  const router = useRouter();
  const { isAuthenticated, user, isLoading } = useStore();

  useEffect(() => {
    if (!isLoading) {
      if (isAuthenticated) {
        // Redirect to dashboard if logged in
        router.push('/dashboard');
      } else {
        // Redirect to login if not logged in
        router.push('/login');
      }
    }
  }, [isAuthenticated, isLoading, router]);

  return <Loader />;
}