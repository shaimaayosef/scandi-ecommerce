import React from 'react';
import ReactDOM from 'react-dom/client';
import { ApolloClient, InMemoryCache, ApolloProvider } from "@apollo/client";
import './index.css';
import App from './App';

// Initialize Apollo Client
const client = new ApolloClient({
  uri: "http://localhost:8000/src/Controller/GraphQL.php",
  cache: new InMemoryCache(),
});

// Create root and render the application
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
  <ApolloProvider client={client}>
    <App />
  </ApolloProvider>
);