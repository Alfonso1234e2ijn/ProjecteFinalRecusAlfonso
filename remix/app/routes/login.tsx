import { Link } from "@remix-run/react";

export default function Login() {
  return (
    <div
      style={{
        background: "linear-gradient(45deg, #6a11cb 0%, #2575fc 100%)",
        minHeight: "100vh",
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
        color: "white",
        fontFamily: "'Arial', sans-serif",
        position: "relative", // Para permitir la posición absoluta dentro del div
      }}
    >
      <div
        style={{
          background: "rgba(255, 255, 255, 0.1)", // Fondo semi-translúcido para el formulario
          padding: "40px 60px",
          borderRadius: "10px",
          boxShadow: "0 4px 10px rgba(0, 0, 0, 0.2)",
          width: "100%",
          maxWidth: "400px",
        }}
      >
        <div
          style={{
            position: "absolute", // Posiciona este elemento de manera absoluta
            top: "20px", // Alineación desde el borde superior
            left: "20px", // Alineación desde el borde izquierdo
            fontSize: "0.9rem", // Tamaño más pequeño
            color: "white", // Blanco
            textDecoration: "none",
            fontWeight: "bold",
          }}
        >
          <Link to="/welcome">&lt; Back to Home</Link>
        </div>

        <h1
          style={{
            fontSize: "2rem",
            fontWeight: "bold",
            textAlign: "center",
            marginBottom: "30px",
          }}
        >
          Login to Your Account
        </h1>

        <form>
          <div style={{ marginBottom: "20px" }}>
            <label
              htmlFor="email"
              style={{
                display: "block",
                marginBottom: "8px",
                fontWeight: "bold",
              }}
            >
              Email:
            </label>
            <input
              type="email"
              id="email"
              name="email"
              required
              style={{
                width: "100%",
                padding: "10px",
                borderRadius: "5px",
                border: "1px solid #ccc",
                fontSize: "1rem",
                backgroundColor: "rgba(255, 255, 255, 0.2)",
                color: "white",
              }}
            />
          </div>
          <div style={{ marginBottom: "30px" }}>
            <label
              htmlFor="password"
              style={{
                display: "block",
                marginBottom: "8px",
                fontWeight: "bold",
              }}
            >
              Password:
            </label>
            <input
              type="password"
              id="password"
              name="password"
              required
              style={{
                width: "100%",
                padding: "10px",
                borderRadius: "5px",
                border: "1px solid #ccc",
                fontSize: "1rem",
                backgroundColor: "rgba(255, 255, 255, 0.2)",
                color: "white",
              }}
            />
          </div>
          <button
            type="submit"
            style={{
              width: "100%",
              padding: "12px",
              background: "linear-gradient(45deg, #ff7e5f, #feb47b)",
              border: "none",
              borderRadius: "5px",
              color: "white",
              fontSize: "1.1rem",
              fontWeight: "bold",
              cursor: "pointer",
              transition: "background 0.3s",
            }}
            onMouseEnter={(e) => e.target.style.background = 'linear-gradient(45deg, #feb47b, #ff7e5f)'}
            onMouseLeave={(e) => e.target.style.background = 'linear-gradient(45deg, #ff7e5f, #feb47b)'}
          >
            Login
          </button>
        </form>
      </div>
    </div>
  );
}
