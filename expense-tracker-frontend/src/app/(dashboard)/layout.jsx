'use client';

import { useState, useEffect } from 'react';
import Navbar from '@/components/Layout/Navbar';
import Sidebar from '@/components/Layout/Sidebar';
import ProtectedRoute from '@/components/Layout/ProtectedRoute';
import useStore from '@/store/useStore';
import { categoryAPI, authAPI } from '@/services/api';

export default function DashboardLayout({ children }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const { setCategories, setUser } = useStore();

  useEffect(() => {
    loadInitialData();
  }, []);

  const loadInitialData = async () => {
    try {
      // Load categories
      const categoriesResponse = await categoryAPI.getAll();
      setCategories(categoriesResponse.data);
      
      // Verify user session
      const userResponse = await authAPI.getUser();
      setUser(userResponse.data);
    } catch (error) {
      console.error('Failed to load initial data:', error);
    }
  };

  return (
    <ProtectedRoute>
      <div className="min-h-screen bg-gray-50">
        <Navbar onMenuClick={() => setSidebarOpen(true)} />
        <div className="flex">
          <Sidebar 
            isOpen={sidebarOpen} 
            onClose={() => setSidebarOpen(false)} 
          />
          <main className="flex-1 p-4 lg:p-8">
            <div className="max-w-7xl mx-auto">
              {children}
            </div>
          </main>
        </div>
      </div>
    </ProtectedRoute>
  );
}