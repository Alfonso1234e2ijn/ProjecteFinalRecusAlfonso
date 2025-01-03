import { useEffect, useState } from "react";

export default function Dashboard() {
  const [notifications, setNotifications] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  const userName = "User"; // Replace this with dynamic data from your backend.

  useEffect(() => {
    // Simulate fetching notifications
    const fetchNotifications = async () => {
      // Replace with your actual API call
      const response = await fetch("http://localhost/api/notifications");
      if (response.ok) {
        const data = await response.json();
        setNotifications(data.notifications);
        setUnreadCount(data.unreadCount);
      }
    };

    fetchNotifications();
  }, []);

  const toggleDropdown = () => setDropdownOpen((prev) => !prev);
  const toggleMenu = () => setMenuOpen((prev) => !prev);

  const handleLogout = () => {
    // Simulate logout (replace with your actual logout logic)
    console.log("User logged out");
    window.location.href = "/login"; // Redirect to login page
  };

  return (
    <div className="min-h-screen bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-gray-900 dark:text-gray-100">
      {/* Header */}
      <header className="bg-white dark:bg-gray-800 shadow">
        <div className="container mx-auto px-4 py-6 flex justify-between items-center">
          {/* Dashboard Title */}
          <h2 className="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Dashboard
          </h2>

          {/* Notification Bell */}
          <div className="relative">
            <button
              onClick={toggleDropdown}
              className="relative focus:outline-none text-gray-800 dark:text-gray-200"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14V10a6 6 0 10-12 0v4c0 .387-.158.735-.405 1.005L4 17h5m6 0a3 3 0 11-6 0h6z"
                />
              </svg>
              {unreadCount > 0 && (
                <span className="absolute top-0 right-0 w-4 h-4 bg-red-600 text-white text-xs rounded-full flex justify-center items-center">
                  {unreadCount}
                </span>
              )}
            </button>
            {dropdownOpen && (
              <div
                className="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-700 rounded-lg shadow-lg overflow-hidden"
                onClick={() => setDropdownOpen(false)}
              >
                <ul className="divide-y divide-gray-200 dark:divide-gray-600">
                  {notifications.length > 0 ? (
                    notifications.map((notif, idx) => (
                      <li key={idx} className="px-4 py-2">
                        {notif.message}
                      </li>
                    ))
                  ) : (
                    <li className="px-4 py-2 text-gray-600 dark:text-gray-400">
                      No new notifications
                    </li>
                  )}
                </ul>
              </div>
            )}
          </div>

          {/* Hamburger Menu */}
          <div className="relative">
            <button
              onClick={toggleMenu}
              className="text-gray-800 dark:text-gray-200 focus:outline-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M4 6h16M4 12h16m-7 6h7"
                />
              </svg>
            </button>
            {menuOpen && (
              <div
                className="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg overflow-hidden"
                onClick={() => setMenuOpen(false)}
              >
                <ul className="divide-y divide-gray-200 dark:divide-gray-600">
                  <li className="px-4 py-2 flex items-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      className="h-5 w-5 mr-2"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M5.121 17.804A3 3 0 004 15V7a3 3 0 013-3h10a3 3 0 013 3v8a3 3 0 01-1.121 2.804M15 12h3m0 0l-3 3m3-3l-3-3"
                      />
                    </svg>
                    Logout
                  </li>
                  <li className="px-4 py-2 flex items-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      className="h-5 w-5 mr-2"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M12 4v16m8-8H4"
                      />
                    </svg>
                    Manage User
                  </li>
                </ul>
              </div>
            )}
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="container mx-auto px-4 py-12">
        <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 className="text-3xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
            Welcome, {userName}!
          </h3>
          <p className="text-lg text-gray-700 dark:text-gray-300">
            You're logged in! Enjoy exploring your dashboard.
          </p>
        </div>
      </main>
    </div>
  );
}
