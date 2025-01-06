import { useState, useEffect, useRef } from "react";
import { useLocation, useNavigate } from "@remix-run/react";

const Group = () => {
    const location = useLocation();
    const navigate = useNavigate();

    const threadTitle = location.state?.threadTitle;
    const threadId = location.state?.threadId;

    const [responses, setResponses] = useState<any[]>([]);
    const [enrichedResponses, setEnrichedResponses] = useState<any[]>([]);
    const [newMessage, setNewMessage] = useState("");
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const responseListRef = useRef<HTMLDivElement | null>(null);
    const [user, setUser] = useState<any>(null);

    // Fetch the authenticated user
    useEffect(() => {
        const fetchUser = async () => {
            try {
                const response = await fetch(`http://localhost/api/profile`, {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });

                if (!response.ok) {
                    throw new Error("Failed to fetch user profile");
                }

                const data = await response.json();
                setUser(data.data.user);
            } catch (error) {
                setError("Failed to fetch user data.");
                console.error(error);
            }
        };

        fetchUser();
    }, []);

    // Fetch responses for the thread
    const fetchMessages = async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await fetch(
                `http://localhost/api/responses/${threadId}`,
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                }
            );
            if (response.ok) {
                const data = await response.json();
                setResponses(data.responses);
            } else {
                const errorText = await response.text();
                setError(`Failed to fetch responses: ${errorText}`);
            }
        } catch (err) {
            setError("An error occurred while fetching responses.");
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchMessages();
    }, [threadId]);

    // Enrich responses with user data
    useEffect(() => {
        const enrichResponses = async () => {
            try {
                const updatedResponses = await Promise.all(
                    responses.map(async (response) => {
                        if (!response.user) {
                            const res = await fetch(
                                `http://localhost/api/responses/${response.id}/user`,
                                {
                                    headers: {
                                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                                    },
                                }
                            );
                            if (res.ok) {
                                const data = await res.json();
                                return { ...response, user: data.user };
                            }
                        }
                        return response;
                    })
                );
                setEnrichedResponses(updatedResponses);
            } catch (error) {
                console.error("Failed to enrich responses:", error);
            }
        };

        if (responses.length > 0) {
            enrichResponses();
        }
    }, [responses]);

    // Handle sending a new message
    const handleSendMessage = async (event: React.FormEvent) => {
        event.preventDefault();

        try {
            const response = await fetch(`http://localhost/api/responses`, {
                method: "POST",
                body: JSON.stringify({
                    content: newMessage,
                    thread_id: threadId,
                }),
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            });

            if (response.ok) {
                setNewMessage("");
                fetchMessages();
                if (responseListRef.current) {
                    responseListRef.current.scrollTop =
                        responseListRef.current.scrollHeight;
                }
            } else {
                const errorText = await response.text();
                setError(`Error sending message: ${errorText}`);
            }
        } catch (error) {
            setError("An error occurred while sending the message.");
            console.error("Error:", error);
        }
    };

    return (
        <div className="bg-gradient-to-r from-purple-400 via-indigo-500 to-blue-600 flex items-center justify-center min-h-screen">
            <div className="bg-white w-full max-w-md h-full max-h-screen flex flex-col shadow-lg rounded-lg">
                <div className="mb-4">
                    <button
                        onClick={() => navigate("/threads")}
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
                        Back to Threads
                    </button>
                </div>

                <div className="bg-blue-500 text-white py-4 px-6 rounded-t-lg text-center font-bold text-lg">
                    Discussion Chat: {threadTitle || "Loading..."}
                </div>

                <div
                    ref={responseListRef}
                    className="flex-1 overflow-y-auto p-4 space-y-4"
                >
                    {loading ? (
                        <p>Loading...</p>
                    ) : error ? (
                        <p className="text-red-500">{error}</p>
                    ) : enrichedResponses.length > 0 ? (
                        enrichedResponses.map((response: any) => (
                            <div
                                key={response.id}
                                className={`flex ${
                                    response.user_id === user?.id
                                        ? "items-end justify-end"
                                        : "items-start"
                                }`}
                            >
                                <div
                                    className={`bg-${
                                        response.user_id === user?.id
                                            ? "blue"
                                            : "gray"
                                    }-500 text-white rounded-lg p-3 max-w-xs shadow`}
                                >
                                    <p>{response.content}</p>
                                    <small className="text-xs">
                                        {response.user?.username || "Unknown User"}
                                    </small>
                                </div>
                            </div>
                        ))
                    ) : (
                        <p>No responses yet.</p>
                    )}
                </div>

                <form
                    onSubmit={handleSendMessage}
                    className="bg-gray-50 p-4 border-t flex items-center space-x-2"
                >
                    <input
                        type="text"
                        value={newMessage}
                        onChange={(e) => setNewMessage(e.target.value)}
                        placeholder="Write a message..."
                        className="flex-1 border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <button
                        type="submit"
                        className="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition"
                    >
                        Send
                    </button>
                </form>
            </div>
        </div>
    );
};

export default Group;
