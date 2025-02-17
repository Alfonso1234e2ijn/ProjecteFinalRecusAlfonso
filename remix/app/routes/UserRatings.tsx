import { json, redirect } from "@remix-run/node";
import { useLoaderData, Form, Link } from "@remix-run/react";
import { useState, useEffect } from "react";

interface User {
  id: number;
  name: string;
  username: string;
  email: string;
  rating: number | null;
}

// Loader function to fetch users
export const loader = async () => {
  try {
    const response = await fetch("http://localhost/api/users/getAll");

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || "Failed to fetch users");
    }

    const data = await response.json();
    return json({ users: data.users });
  } catch (error: any) {
    return json({ error: error.message }, { status: 500 });
  }
};

// Action function to handle ratings
export const action = async ({ request }: { request: Request }) => {
  const formData = await request.formData();
  const userId = formData.get("userId");
  const rating = formData.get("rating");
  const token = formData.get("token");

  if (!userId || !rating || !token) {
    return json({ error: "Missing required fields" }, { status: 400 });
  }

  try {
    const response = await fetch("http://localhost/api/uratings/rate", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ user_id: Number(userId), rating: Number(rating) }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || "Failed to submit rating");
    }

    return redirect("/RateConfirmation");
  } catch (error: any) {
    return json({ error: error.message }, { status: 500 });
  }
};

export default function UserRatings() {
  const { users, error } = useLoaderData<typeof loader>();
  const [searchQuery, setSearchQuery] = useState<string>("");
  const [hoveredRatings, setHoveredRatings] = useState<{ [key: number]: number | null }>({});
  const [token, setToken] = useState<string | null>(null);

  useEffect(() => {
    // Access localStorage only in the browser
    const storedToken = localStorage.getItem("token");
    setToken(storedToken);
  }, []);

  const handleSearch = (event: React.ChangeEvent<HTMLInputElement>) => {
    setSearchQuery(event.target.value.toLowerCase());
  };

  const filteredUsers = users.filter((user: User) =>
    user.name.toLowerCase().includes(searchQuery) ||
    user.username.toLowerCase().includes(searchQuery)
  );

  const handleHover = (userId: number, star: number) => {
    setHoveredRatings((prev) => ({ ...prev, [userId]: star }));
  };

  const handleMouseLeave = (userId: number) => {
    setHoveredRatings((prev) => ({ ...prev, [userId]: null }));
  };

  return (
    <div className="bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 min-h-screen flex items-center justify-center p-6">
      <div className="absolute top-4 left-4">
        <Link
          to="/dashboard"
          className="text-white bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg shadow-lg transition-transform transform hover:scale-105"
        >
          Back to Dashboard
        </Link>
      </div>

      <div className="bg-white rounded-xl shadow-2xl p-8 w-full max-w-4xl">
        <h1 className="text-3xl font-extrabold text-gray-800 text-center mb-8">
          🌟 User Ratings
        </h1>

        <input
          type="text"
          placeholder="Search by name or username..."
          value={searchQuery}
          onChange={handleSearch}
          className="w-full p-4 mb-6 border border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-500 focus:outline-none"
        />

        {error ? (
          <p className="text-center text-red-600 font-semibold">{error}</p>
        ) : filteredUsers.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {filteredUsers.map((user: User) => (
              <div
                key={user.id}
                className="p-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg shadow-md transform hover:scale-105 transition-transform"
              >
                <h3 className="text-lg font-bold text-gray-800">{user.name}</h3>
                <p className="text-sm text-gray-600">@{user.username}</p>
                <div className="mt-4 flex items-center space-x-1">
                  {[1, 2, 3, 4, 5].map((star) => (
                    <Form method="post" key={star}>
                      <input type="hidden" name="userId" value={user.id} />
                      <input type="hidden" name="rating" value={star} />
                      <input type="hidden" name="token" value={token || ""} />
                      <button
                        type="submit"
                        className={`cursor-pointer text-2xl ${
                          (hoveredRatings[user.id] || user.rating || 0) >= star
                            ? "text-yellow-400"
                            : "text-gray-300"
                        }`}
                        onMouseEnter={() => handleHover(user.id, star)}
                        onMouseLeave={() => handleMouseLeave(user.id)}
                      >
                        ★
                      </button>
                    </Form>
                  ))}
                </div>
                <p className="text-sm text-gray-500 mt-2">
                  {user.rating ? `Current rating: ${user.rating}` : "No ratings yet"}
                </p>
              </div>
            ))}
          </div>
        ) : (
          <p className="text-center text-gray-600">No users found</p>
        )}
      </div>
    </div>
  );
}
