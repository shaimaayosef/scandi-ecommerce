import { createRoot } from 'react-dom/client'
import { ApolloClient, InMemoryCache, ApolloProvider } from "@apollo/client";

import './index.css'
import App from './App.jsx'

// Initialize Apollo Client
const client = new ApolloClient({
  uri: "http://localhost:8000/src/Controller/GraphQL.php",
  cache: new InMemoryCache(),
});

createRoot(document.getElementById('root')).render(
  <ApolloProvider client={client}>
    <App />
  </ApolloProvider>

)
