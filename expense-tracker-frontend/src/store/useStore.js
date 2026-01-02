import { create } from 'zustand';
import { persist } from 'zustand/middleware';

const useStore = create(
  persist(
    (set, get) => ({
      // Auth state
      user: null,
      token: null,
      isAuthenticated: false,
      
      // UI state
      isLoading: false,
      toast: null,
      modal: null,
      
      // Data state
      transactions: [],
      categories: [],
      dashboardData: null,
      
      // Auth actions
      setAuth: (user, token) => set({ user, token, isAuthenticated: true }),
      clearAuth: () => set({ user: null, token: null, isAuthenticated: false }),
      
      // UI actions
      setLoading: (isLoading) => set({ isLoading }),
      showToast: (toast) => set({ toast }),
      showModal: (modal) => set({ modal }),
      hideModal: () => set({ modal: null }),
      
      // Data actions
      setTransactions: (transactions) => set({ transactions }),
      addTransaction: (transaction) => set((state) => ({
        transactions: [transaction, ...state.transactions]
      })),
      updateTransaction: (id, updates) => set((state) => ({
        transactions: state.transactions.map(t => 
          t.id === id ? { ...t, ...updates } : t
        )
      })),
      deleteTransaction: (id) => set((state) => ({
        transactions: state.transactions.filter(t => t.id !== id)
      })),
      
      setCategories: (categories) => set({ categories }),
      setDashboardData: (dashboardData) => set({ dashboardData }),
      
      // Helper getters
      getCategoryById: (id) => {
        return get().categories.find(c => c.id === id);
      },
      
      getIncomeCategories: () => {
        return get().categories.filter(c => c.type === 'income');
      },
      
      getExpenseCategories: () => {
        return get().categories.filter(c => c.type === 'expense');
      },
    }),
    {
      name: 'expense-tracker-storage',
      partialize: (state) => ({
        user: state.user,
        token: state.token,
        isAuthenticated: state.isAuthenticated,
      }),
    }
  )
);

export default useStore;