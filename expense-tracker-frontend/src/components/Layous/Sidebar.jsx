import { Home, TrendingUp, CreditCard, PieChart, Settings, Users, Tag } from 'lucide-react';
import { usePathname } from 'next/navigation';
import useStore from '@/store/useStore';

const userNavItems = [
  { href: '/', icon: Home, label: 'Dashboard' },
  { href: '/transactions', icon: CreditCard, label: 'Transactions' },
  { href: '/reports', icon: PieChart, label: 'Reports' },
];

const adminNavItems = [
  { href: '/admin', icon: Home, label: 'Admin Dashboard' },
  { href: '/admin/users', icon: Users, label: 'Users' },
  { href: '/admin/categories', icon: Tag, label: 'Categories' },
];

export default function Sidebar({ isOpen, onClose }) {
  const pathname = usePathname();
  const { user } = useStore();
  const isAdmin = user?.role === 'admin';
  
  const navItems = isAdmin ? adminNavItems : userNavItems;

  return (
    <>
      {/* Mobile overlay */}
      {isOpen && (
        <div
          className="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
          onClick={onClose}
        />
      )}

      {/* Sidebar */}
      <div
        className={`
          fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-200 ease-in-out
          ${isOpen ? 'translate-x-0' : '-translate-x-full'} 
          lg:translate-x-0 lg:static lg:inset-0
        `}
      >
        <div className="h-full flex flex-col">
          <div className="p-4 border-b">
            <h2 className="text-lg font-semibold text-gray-800">Expense Tracker</h2>
            {isAdmin && (
              <p className="text-xs text-primary-600 mt-1">Administrator</p>
            )}
          </div>

          <nav className="flex-1 p-4 space-y-1 overflow-y-auto">
            {navItems.map((item) => {
              const isActive = pathname === item.href;
              return (
                <a
                  key={item.href}
                  href={item.href}
                  className={`
                    flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors
                    ${isActive
                      ? 'bg-primary-50 text-primary-600'
                      : 'text-gray-700 hover:bg-gray-100'
                    }
                  `}
                  onClick={onClose}
                >
                  <item.icon className="h-5 w-5" />
                  <span className="font-medium">{item.label}</span>
                </a>
              );
            })}
          </nav>

          <div className="p-4 border-t">
            <a
              href="/settings"
              className="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100"
            >
              <Settings className="h-5 w-5" />
              <span className="font-medium">Settings</span>
            </a>
          </div>
        </div>
      </div>
    </>
  );
}