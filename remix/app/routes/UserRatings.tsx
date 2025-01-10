import { useEffect, useState } from "react";
import { Link } from "@remix-run/react";

interface User {
  id: number;
  name: string;
  username: string;
  email: string;
  rating: number | null; // User's rating, can be null if no rating is given
}

export default function UserRatings() {
  const [users, setUsers] = useState<User[]>([]); // Complete list of users
  const [filteredUsers, setFilteredUsers] = useState<User[]>([]); // Filtered list of users based on search query
  const [loading, setLoading] = useState(true); // Loading state for fetching data
  const [error, setError] = useState<string | null>(null); // Error state if fetching fails
  const [searchQuery, setSearchQuery] = useState<string>(""); // State for storing search query
  const [hoveredRatings, setHoveredRatings] = useState<{ [key: number]: number | null }>({}); // Hover state for stars, stored by userId

  // Fetch users when the component mounts
  useEffect(() => {
    const fetchUsers = async () => {
      setLoading(true); // Set loading to true before fetching data
      setError(null); // Reset any previous errors

      try {
        const response = await fetch("http://localhost/api/users/getAll", {
          method: "GET",
        });

        if (response.ok) {
          const data = await response.json();
          setUsers(data.users); // Save the complete list of users
          setFilteredUsers(data.users); // Initialize the filtered list with the full list
        } else {
          const errorData = await response.json();
          setError(errorData.message || "Failed to fetch users"); // Set error message if fetching fails
        }
      } catch (error) {
        setError("Error fetching users"); // Handle any fetch errors
        console.error("Error fetching users:", error);
      } finally {
        setLoading(false); // Set loading to false once fetching is complete
      }
    };

    fetchUsers(); // Call fetchUsers function when component mounts
  }, []); // Empty dependency array ensures this runs only once

  // Handle search input and filter users by name or username
  const handleSearch = (event: React.ChangeEvent<HTMLInputElement>) => {
    const query = event.target.value.toLowerCase();
    setSearchQuery(query); // Update search query

    // Filter the original user list by name or username
    const filtered = users.filter((user) =>
      user.name.toLowerCase().includes(query) || user.username.toLowerCase().includes(query)
    );

    setFilteredUsers(filtered); // Update filtered users list
  };

  // Handle rating submission (POST request to backend)
  const handleRating = async (userId: number, rating: number) => {
    const token = localStorage.getItem("token"); // Get auth token from localStorage
    if (!token) {
      setError("No token found. Please log in.");
      return; // Stop if no token is found
    }

    try {
      const response = await fetch("http://localhost/api/uratings/rate", {
        method: "POST",
        headers: {
          "Content-Type": "application/json", // Ensure correct content type
          Authorization: `Bearer ${token}`, // Pass token in Authorization header
        },
        body: JSON.stringify({
          user_id: userId,
          rating, // Pass the rating to the server
        }),
      });

      if (response.ok) {
        // Reload the list of users after submitting rating
        const data = await response.json();
        const updatedUsers = users.map((user) =>
          user.id === userId ? { ...user, rating: data.rating } : user
        );
        setUsers(updatedUsers); // Update the complete user list
        setFilteredUsers(updatedUsers); // Update the filtered list
      } else {
        const errorData = await response.json();
        setError(errorData.message || "Failed to submit rating"); // Handle error from server
      }
    } catch (error) {
      setError("Error submitting rating"); // Handle any errors that occur during submission
      console.error("Error submitting rating:", error);
    }
  };

  // Handle hover effect on star rating
  const handleHover = (userId: number, star: number) => {
    setHoveredRatings((prevHoveredRatings) => ({
      ...prevHoveredRatings,
      [userId]: star, // Set the hovered star for the specific user
    }));
  };

  // Handle mouse leave event for star rating
  const handleMouseLeave = (userId: number) => {
    setHoveredRatings((prevHoveredRatings) => ({
      ...prevHoveredRatings,
      [userId]: null, // Reset hovered rating when mouse leaves the star
    }));
  };

  return (
    <div className="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center min-h-screen">
      {/* Back Button */}
      <div className="absolute top-4 left-4">
        <Link
          to="/dashboard"
          className="flex items-center text-white font-medium text-lg bg-gradient-to-r from-indigo-400 via-blue-500 to-purple-600 hover:from-purple-600 hover:to-blue-500 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 hover:scale-105 focus:outline-none"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            strokeWidth="2"
            stroke="currentColor"
            className="w-6 h-6 mr-3"
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

      <div className="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md relative">
        {/* Title */}
        <h1 className="text-3xl font-extrabold text-center text-gray-700 mb-6">
          User Ratings
        </h1>
        <p className="text-gray-500 text-center mb-8">
          Browse and search through users and their ratings.
        </p>

        {/* Search Bar */}
        <div className="mb-6">
          <input
            type="text"
            placeholder="Search by name or username"
            value={searchQuery}
            onChange={handleSearch} // Update the search query on input change
            className="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <div id="usersContainer" className="space-y-5">
          {loading ? (
            <p className="text-center text-gray-500">Loading...</p> // Show loading text while fetching
          ) : error ? (
            <p className="text-center text-red-500">{error}</p> // Show error message if there's an error
          ) : filteredUsers.length > 0 ? (
            filteredUsers.map((user) => (
              <div
                key={user.id}
                className="user-item flex items-center justify-between bg-blue-200 text-blue-800 text-lg font-medium py-4 rounded-lg shadow-md hover:bg-blue-300 transition-transform transform hover:scale-105"
              >
                <div>
                  <h3 className="text-xl font-bold">{user.name}</h3>
                  <p className="text-sm text-gray-600">@{user.username}</p>
                  <p className="text-sm text-gray-600">{user.email}</p>
                  <div className="mt-2">
                    <div className="flex items-center">
                      {[1, 2, 3, 4, 5].map((star) => (
                        <svg
                          key={star}
                          xmlns="http://www.w3.org/2000/svg"
                          className={`w-6 h-6 cursor-pointer transition-all duration-200 ease-in-out ${
                            (user.rating && user.rating >= star) ||
                            (hoveredRatings[user.id] && hoveredRatings[user.id] >= star)
                              ? "text-yellow-400"
                              : "text-gray-400"
                          }`}
                          fill="currentColor"
                          viewBox="0 0 20 20"
                          onClick={() => handleRating(user.id, star)} // Call rating handler when star is clicked
                          onMouseEnter={() => handleHover(user.id, star)} // Handle hover effect
                          onMouseLeave={() => handleMouseLeave(user.id)} // Reset hover effect when mouse leaves
                        >
                          <path
                            fillRule="evenodd"
                            d="M10 15l-5.878 3.09L5.724 11 1 6.91l6.124-.518L10 0l2.876 5.392 6.124.518-4.724 4.09 1.602 6.09L10 15z"
                            clipRule="evenodd"
                          />
                        </svg>
                      ))}
                    </div>
                    <span className="text-sm text-gray-600">
                      {user.rating ? `★ ${user.rating}` : "No ratings yet"}
                    </span>
                  </div>
                </div>
              </div>
            ))
          ) : (
            <p className="text-center text-gray-500">No users found</p> // Message when no users are found after filtering
          )}
        </div>
      </div>
    </div>
  );
}
