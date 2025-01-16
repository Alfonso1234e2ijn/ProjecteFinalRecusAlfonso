import { useEffect } from "react";

export default function RateConfirmation() {
  useEffect(() => {
    setTimeout(() => {
      window.location.href = "/userRatings";
    }, 2000);
  }, []);

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-r from-green-500 to-blue-500 p-6">
      <div className="bg-white p-8 rounded-xl shadow-2xl w-full max-w-sm text-center">
        <h2 className="text-2xl font-bold text-gray-800">Thank you for your rating!</h2>
        <p className="text-gray-600 mt-4">You will be redirected to your ratings list shortly.</p>
      </div>
    </div>
  );
}
