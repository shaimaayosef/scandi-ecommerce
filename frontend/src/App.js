import React, { Component } from 'react';
import { ApolloConsumer } from '@apollo/client';
import gql from 'graphql-tag';

const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      name
    }
  }
`;
class App extends Component {
  state = {
    categories: [],
    selectedCategory: null,
    loading: false,
    error: null,
  };

  componentDidMount() {
    this.fetchCategories(this.props.client);
  }

  fetchCategories = (client) => {
    this.setState({ loading: true, error: null });
    client
      .query({ query: GET_CATEGORIES })
      .then((result) => {
        this.setState({ categories: result.data.categories, loading: false });
      })
      .catch((error) => this.setState({ error, loading: false }));
  };

  render() {
    const {
      categories,
      selectedCategory,
      loading,
      error,
    } = this.state;

    return (
      <div className="app-container">
        <h1>Categories</h1>
        {loading && <p>Loading...</p>}
        {error && <p>Error: {error.message}</p>}
        <ul>
          {categories.map((category) => (
            <li key={category.name}>
              <button
                type="button"
                onClick={() => this.setState({ selectedCategory: category.name })}
              >
                {category.name}
              </button>
            </li>
          ))}
        </ul>
        {selectedCategory && <p>Selected category: {selectedCategory}</p>}
      </div>
    );
  }
}

const AppWithApollo = () => (
  <ApolloConsumer>
    {(client) => <App client={client} />}
  </ApolloConsumer>
);

export default AppWithApollo;
