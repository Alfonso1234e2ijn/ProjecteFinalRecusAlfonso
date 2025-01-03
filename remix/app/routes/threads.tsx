import { useEffect, useState } from "react";
import { Link } from "react-router-dom"; // Usamos Link para las rutas de Remix

export default function Threads() {
  const [threads, setThreads] = useState([]);
  const [filteredThreads, setFilteredThreads] = useState([]);
  const [searchQuery, setSearchQuery] = useState("");

  useEffect(() => {
    const fetchThreads = async () => {
      try {
        const response = await fetch("/api/threads");
        if (response.ok) {
          const data = await response.json();
          setThreads(data.threads);
          setFilteredThreads(data.threads);
        } else {
          console.error("Failed to fetch threads");
        }
      } catch (error) {
        console.error("Error fetching threads:", error);
      }
    };

    fetchThreads();
  }, []);

  const handleSearch = (event) => {
    const query = event.target.value.toLowerCase();
    setSearchQuery(query);
    const filtered = threads.filter((thread) =>
      thread.title.toLowerCase().includes(query)
    );
    setFilteredThreads(filtered);
  };

  return (
    <div className="bg-gradient-to-r from-green-400 via-green-500 to-green-600 flex items-center justify-center min-h-screen">
      <div className="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        {/* Back to Dashboard */}
        <div className="mb-4">
          <Link
            to="/dashboard"
            className="flex items-center text-gray-600 hover:text-gray-800 text-sm font-medium"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              strokeWidth="2"
              stroke="currentColor"
              className="w-5 h-5 mr-2"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M15 19l-7-7 7-7"
              />
            </svg>
            Back to Dashboard
          </Link>
        </div>

        {/* Title */}
        <h1 className="text-3xl font-extrabold text-center text-gray-700 mb-6">
          Search a Discussion
        </h1>
        <p className="text-gray-500 text-center mb-8">
          Find and select the discussion group.
        </p>

        {/* Search Bar */}
        <div className="mb-6">
          <input
            type="text"
            id="searchInput"
            placeholder="Search discussions..."
            value={searchQuery}
            onChange={handleSearch}
            className="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>

        {/* Threads */}
        <div id="threadsContainer" className="space-y-5">
          {filteredThreads.map((thread) => (
            <Link
              key={thread.id}
              to={`/group`}
              className="thread-item flex items-center justify-center bg-blue-200 text-blue-800 text-lg font-medium py-4 rounded-lg shadow-md hover:bg-blue-300 transition-transform transform hover:scale-105"
            >
              {thread.title}
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}
