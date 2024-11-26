import { createRoot } from "react-dom/client";
import { ApolloClient, InMemoryCache, ApolloProvider } from "@apollo/client";
import { BrowserRouter as Router } from "react-router-dom";
import "./index.css";
import App from "./App.jsx";
import { Provider } from "react-redux";
import { store } from "./store/store";

// Initialize Apollo Client
const client = new ApolloClient({
  uri: "http://localhost:8000/src/Controller/GraphQL.php",
  cache: new InMemoryCache({
    dataIdFromObject: (o) => (o._id ? `${o.__typename}:${o._id}` : null),
  }),
});

createRoot(document.getElementById("root")).render(
  <ApolloProvider client={client}>
    <Provider store={store}>
      <Router future={{ v7_relativeSplatPath: true, v7_startTransition: true }}>
        <App client={client} />
      </Router>
    </Provider>
  </ApolloProvider>
);
