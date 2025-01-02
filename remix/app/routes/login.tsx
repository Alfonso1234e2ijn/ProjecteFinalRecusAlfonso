import { useState } from "react";
import { Link } from "@remix-run/react";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(""); // Resetea el error en cada intento

    try {
      const response = await fetch("http://localhost/api/register", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          email,
          password,
        }),
      });

      if (!response.ok) {
        const data = await response.json();
        setError(data.message || "Invalid credentials");
      } else {
        console.log("Login successful");
        window.location.href = "/dashboard";
      }
    } catch (err) {
      console.error("Error:", err);
      setError("An error occurred while logging in");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-r from-green-400 via-blue-500 to-blue-700 flex justify-center items-center">
      <div className="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 className="text-3xl font-semibold mb-6 text-center text-gray-800">Login to Your Account</h1>

        {error && <div className="text-red-600 mb-4">{error}</div>}

        <form onSubmit={handleSubmit}>
          <div className="mb-4">
            <label htmlFor="email" className="block text-sm font-medium text-gray-700">Email:</label>
            <input
              type="email"
              id="email"
              name="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              className="w-full px-4 py-2 border rounded-md text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div className="mb-6">
            <label htmlFor="password" className="block text-sm font-medium text-gray-700">Password:</label>
            <input
              type="password"
              id="password"
              name="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              className="w-full px-4 py-2 border rounded-md text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <button
            type="submit"
            className="w-full py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200"
            disabled={loading}
          >
            {loading ? "Logging in..." : "Login"}
          </button>
        </form>

        <div className="mt-4 text-center">
          <Link
            to="/welcome"
            className="text-blue-600 hover:underline block mb-2"
          >
            &lt; Back to Home
          </Link>
          
          <Link
            to="/register"
            className="text-blue-600 hover:underline"
          >
            Don't have an account? <span className="font-bold">Sign up here</span>
          </Link>
        </div>
      </div>
    </div>
  );
}
