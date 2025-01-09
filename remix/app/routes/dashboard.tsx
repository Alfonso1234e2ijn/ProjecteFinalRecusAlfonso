import { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import axios from "axios";

export default function Dashboard() {
    const navigate = useNavigate();
    const [notifications, setNotifications] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const [menuOpen, setMenuOpen] = useState(false);
    const [userName, setUserName] = useState("");
    const [userEmail, setUserEmail] = useState("");
    const [userUsername, setUserUsername] = useState("");
    const [editing, setEditing] = useState(false);
    const [originalUser, setOriginalUser] = useState({
        name: "",
        email: "",
        username: "",
    });

    useEffect(() => {
        const fetchUserDetails = async () => {
            try {
                const response = await fetch("http://localhost/api/user", {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                        "Content-Type": "application/json",
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    setUserName(data.name);
                    setUserEmail(data.email);
                    setUserUsername(data.username);
                    setOriginalUser({
                        name: data.name,
                        email: data.email,
                        username: data.username,
                    });
                } else {
                    console.error("Error fetching user details");
                }
            } catch (error) {
                console.error("Error:", error);
            }
        };

        fetchUserDetails();

        const fetchNotifications = async () => {
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

    const handleLogout = async () => {
        try {
            const response = await fetch("http://localhost/api/logout", {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                    "Content-Type": "application/json",
                },
            });

            if (response.ok) {
                localStorage.removeItem("token");
                console.log("User logged out successfully");
                navigate("/login");
            } else {
                console.error("Error during logout");
            }
        } catch (error) {
            console.error("Error:", error);
        }
    };

    const handleSaveChanges = async () => {
        const updatedUser = {
            name: userName,
            email: userEmail,
            username: userUsername,
        };

        try {
            const response = await fetch("http://localhost/api/user/update", {
                method: "PUT",
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(updatedUser),
            });

            if (response.ok) {
                alert("User details updated successfully!");
                setEditing(false);
            } else {
                const errorData = await response.json();
                console.error("Error updating user details:", errorData);
                alert(
                    `Failed to update user details: ${
                        errorData.message || "Unknown error"
                    }`
                );
            }
        } catch (error) {
            console.error("Error:", error);
            alert("An unexpected error occurred while updating user details.");
        }
    };

    const handleCancel = () => {
        setUserName(originalUser.name);
        setUserEmail(originalUser.email);
        setUserUsername(originalUser.username);
        setEditing(false);
        window.location.href = "/dashboard";
    };

    const handleDeleteAccount = async () => {
        if (window.confirm("Are you sure you want to delete your account?")) {
            try {
                const response = await fetch(
                    "http://localhost/api/user/delete",
                    {
                        method: "DELETE",
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            "Content-Type": "application/json",
                        },
                    }
                );

                if (response.ok) {
                    localStorage.removeItem("token");
                    navigate("/welcome");
                    alert("Your account has been deleted.");
                } else {
                    console.error("Error deleting account");
                }
            } catch (error) {
                console.error("Error:", error);
                alert(
                    "An unexpected error occurred while deleting your account."
                );
            }
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-gray-900 dark:text-gray-100">
            <header className="bg-white dark:bg-gray-800 shadow">
                <div className="container mx-auto px-4 py-6 flex justify-between items-center">
                    <h2 className="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Dashboard
                    </h2>
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
                                    <li
                                        onClick={handleLogout}
                                        className="px-4 py-2 flex items-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                                    >
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
                                                d="M5.121 17.804A3 3 0 004 15V7a3 3 0 013-3h10a3 3 0 013 3v8a3 3 0 01-1.121 2.804M9 13h6M10 17h4"
                                            />
                                        </svg>
                                        Logout
                                    </li>
                                    <li
                                        onClick={() => setEditing(true)}
                                        className="px-4 py-2 flex items-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                                    >
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
                                    <li
                                        onClick={handleDeleteAccount}
                                        className="px-4 py-2 flex items-center cursor-pointer hover:bg-red-600 dark:hover:bg-red-600 text-red-600 dark:text-red-400"
                                    >
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
                                                d="M19 7l-7 7-7-7"
                                            />
                                        </svg>
                                        Delete Account
                                    </li>
                                    <li className="px-4 py-2 flex items-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <Link
                                            to="/my-threads"
                                            className="flex items-center"
                                        >
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
                                                    d="M4 6h16M4 12h16m-7 6h7"
                                                />
                                            </svg>
                                            My Threads
                                        </Link>
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
                    {editing ? (
                        <>
                            <h3 className="text-3xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                Edit User Details
                            </h3>
                            <div>
                                <div className="mb-4">
                                    <label
                                        htmlFor="userName"
                                        className="block text-sm font-medium text-gray-700"
                                    >
                                        Name
                                    </label>
                                    <input
                                        id="userName"
                                        type="text"
                                        value={userName}
                                        onChange={(e) =>
                                            setUserName(e.target.value)
                                        }
                                        className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                                    />
                                </div>
                                <div className="mb-4">
                                    <label
                                        htmlFor="userEmail"
                                        className="block text-sm font-medium text-gray-700"
                                    >
                                        Email
                                    </label>
                                    <input
                                        id="userEmail"
                                        type="email"
                                        value={userEmail}
                                        onChange={(e) =>
                                            setUserEmail(e.target.value)
                                        }
                                        className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                                    />
                                </div>
                                <div className="mb-4">
                                    <label
                                        htmlFor="userUsername"
                                        className="block text-sm font-medium text-gray-700"
                                    >
                                        Username
                                    </label>
                                    <input
                                        id="userUsername"
                                        type="text"
                                        value={userUsername}
                                        onChange={(e) =>
                                            setUserUsername(e.target.value)
                                        }
                                        className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                                    />
                                </div>
                                <div className="flex items-center justify-end gap-4">
                                    <button
                                        onClick={handleSaveChanges}
                                        className="bg-blue-500 text-white py-2 px-4 rounded-md"
                                    >
                                        Save Changes
                                    </button>
                                    <button
                                        onClick={handleCancel}
                                        className="bg-gray-500 text-white py-2 px-4 rounded-md"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </>
                    ) : (
                        <>
                            <h3 className="text-3xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                Welcome, {userName || "Loading..."}!
                            </h3>
                            <p className="text-lg text-gray-700 dark:text-gray-300">
                                You're logged in! Enjoy exploring your
                                dashboard.
                            </p>

                            {/* Button below the Welcome text */}
                            <Link to="/threads">
                                <button className="mt-6 bg-blue-500 text-white py-2 px-4 rounded-md">
                                    Go to Threads
                                </button>
                            </Link>
                        </>
                    )}
                </div>
            </main>
        </div>
    );
}
