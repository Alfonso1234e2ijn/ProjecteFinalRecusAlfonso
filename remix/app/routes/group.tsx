import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';

const Group = () => {
  const { groupId } = useParams(); // Get the "groupId" from the URL
  const [responses, setResponses] = useState([]);
  const [newMessage, setNewMessage] = useState("");

  // Fetch responses (messages) for the group from the API
  useEffect(() => {
    const fetchResponses = async () => {
      try {
        const response = await fetch(`/api/groups/${groupId}/messages`);
        if (response.ok) {
          const data = await response.json();
          setResponses(data.messages); // Set the responses
        } else {
          console.error("Error fetching messages");
        }
      } catch (error) {
        console.error("Error fetching responses:", error);
      }
    };

    fetchResponses();
  }, [groupId]);

  const handleSendMessage = async (event: React.FormEvent) => {
    event.preventDefault();
    // Send the new message via a POST request
    try {
      const response = await fetch(`/api/groups/${groupId}/send-message`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message: newMessage }),
      });

      if (response.ok) {
        const data = await response.json();
        setResponses(prev => [...prev, data.message]); // Add the new message to responses
        setNewMessage(""); // Clear the message input
      } else {
        console.error("Error sending the message");
      }
    } catch (error) {
      console.error("Error sending the message:", error);
    }
  };

  return (
    <div className="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-600 flex items-center justify-center min-h-screen">
      <div className="bg-white w-full max-w-md h-full max-h-screen flex flex-col shadow-lg rounded-lg relative">
        
        {/* Back to Discussions link at the top-left corner, just above the title */}
        <div className="absolute top-0 left-0 w-full p-4">
          <Link to="/threads" className="flex items-center text-gray-600 hover:text-gray-800 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className="w-5 h-5 mr-2">
              <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Discussions
          </Link>
        </div>

        {/* Chat title */}
        <div className="bg-blue-500 text-white py-4 px-6 rounded-t-lg text-center font-bold text-lg mt-16">
          Discussion Chat: {groupId} {/* Use groupId to identify the group */}
        </div>

        {/* Responses (messages) */}
        <div className="flex-1 overflow-y-auto p-4 space-y-4">
          {responses.map((response) => (
            <div key={response.id} className={`flex ${response.userId === 1 ? 'items-end justify-end' : 'items-start'}`}> {/* Replace "1" with the authenticated user's ID */}
              <div className={`bg-${response.userId === 1 ? 'blue' : 'gray'}-500 text-white rounded-lg p-3 max-w-xs shadow`}>
                <p>{response.content}</p>
                <small className="text-xs">{response.username}</small> {/* Display the user's name */}
              </div>
            </div>
          ))}
        </div>

        {/* Form to send a new message */}
        <form onSubmit={handleSendMessage} className="bg-gray-50 p-4 border-t flex items-center space-x-2">
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
