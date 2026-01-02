'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import useStore from '@/store/useStore';
import Loader from '@/components/UI/Loader';

export default function ProtectedRoute({ children, adminOnly = false }) {
  const { isAuthenticated, user, isLoading } = useStore();
  const router = useRouter();

  useEffect(() => {
    if (!isLoading && !isAuthenticated) {
      router.push('/login');
    }
    
    if (!isLoading && isAuthenticated && adminOnly && user?.role !== 'admin') {
      router.push('/');
    }
  }, [isAuthenticated, isLoading, user, router, adminOnly]);

  if (isLoading) {
    return <Loader />;
  }

  if (!isAuthenticated || (adminOnly && user?.role !== 'admin')) {
    return null;
  }

  return <>{children}</>;
}